//récupérer la zone ou on va afficher les articles
let container = document.querySelector('#container');
let url = 'https://localhost:8000/api/article/all';
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
                    let article = document.createElement('div');
                    article.setAttribute('id', obj.id);
                    container.appendChild(article);
                    const titre = document.createElement('h1');
                    titre.textContent =obj.titre;
                    const contenu = document.createElement('p');
                    contenu.textContent =obj.contenu ;
                    const date = document.createElement('p');
                    date.textContent = obj.date.substring(0,10);
                    article.appendChild(titre);
                    article.appendChild(contenu);
                    article.appendChild(date);
                });
            }
            //cas ou il n'y a pas d'article
            if(response.status==206){
                //affichage de l'erreur
                container.textContent = data.erreur;
            }
        }
    });

