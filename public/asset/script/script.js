//récupérer la zone ou on va afficher les articles
let container = document.querySelector('#container');
let url = 'https://localhost:8000/api/article/all';
let url2 = 'https://localhost:8000/api/article/delete/';
let charge = false;
//écrire le script fetch
const getArticle = fetch(url)
    .then(async response => {
        //vérification du code erreur du serveur (BDD hs)
        if(response.status == 500){
            //affichage de l'erreur
            container.textContent = 'le serveur est en maintenance';
        }
        //test des autres codes erreurs
        else{
            //récupére le json
            const data = await response.json();
            //cas ou tout va bien
            if(response.status==200){
                //parcour du json
                data.forEach(obj => {
                    console.log(obj);
                    const article = document.createElement('div');
                    article.setAttribute('class', 'article');
                    container.appendChild(article);
                    const titre = document.createElement('h1');
                    titre.textContent =obj.titre;
                    const contenu = document.createElement('p');
                    contenu.textContent =obj.contenu ;
                    const date = document.createElement('p');
                    date.textContent = obj.date.substring(0,10);
                    const icone = document.createElement('i');
                    icone.setAttribute('class', 'fa-solid fa-trash-can delete');
                    icone.setAttribute('id', obj.id);
                    article.appendChild(titre);
                    article.appendChild(contenu);
                    article.appendChild(date);
                    article.appendChild(icone);
                    charge = true;
                    icone.addEventListener('click', ()=>{
                        console.log(icone.id);
                        //fetch suppression 
                        fetch(url2+icone.id,
                            {method :'DELETE'})
                        .then(async response1 => {
                            //vérification du code erreur du serveur (BDD hs)
                            if(response1.status == 500){
                                //affichage de l'erreur
                                alert('le serveur est en maintenance');
                            }
                            else{
                                //récupérer le json de la suppression
                                const dataSup = await response1.json();
                                if(response1.status == 200){
                                    article.remove();
                                    alert(dataSup.erreur);
                                }
                                if(response1.status == 400){
                                    alert(dataSup.erreur);
                                }
                            }
                        })
                    })
                });
            }
            //cas ou il n'y a pas d'article
            if(response.status==206){
                //affichage de l'erreur
                container.textContent = data.erreur;
            }
        }
    });

