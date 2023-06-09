let articles = document.querySelector('#articles');
let error = document.querySelector('#error');
const url = 'https://localhost:8000/api/articles/get/all';
const headers = { 'Authorization': 'Bearer '+localStorage.getItem("jwt")}
const token = fetch(url, {
                            method:'GET',  
                            headers
                        }
                )
                .then(async response=>{
                            //test si le code erreur est 400
                            if(response.status==400){
                                const expired = await response.json();
                                //test si le message est Expired token
                                if(expired.Error == 'Expired token'){
                                    location.href ='https://localhost:8000/api/localToken';
                                }
                                else{
                                    error.textContent = expired.Error;
                                }
                            }
                            //test si le code erreur est 200 ok
                            if(response.status == 200){
                                const liste = await response.json();
                                liste.forEach(e => {
                                    let article = document.createElement('div');
                                    let titre = document.createElement('h2');
                                    titre.textContent = e.titre;
                                    let content = document.createElement('div');
                                    content.textContent = e.contenu;
                                    let date = document.createElement('h3');
                                    date.textContent = e.date.substring(0,10);
                                    articles.appendChild(article);
                                    article.appendChild(titre);
                                    article.appendChild(content);
                                    article.appendChild(date);
                                });
                            }
                            //Gérer l'erreur liste vide
                            else{
                                const jsonResponse = await response.json();
                                error.textContent = jsonResponse.Error;
                            }
                        }
                    );
        
        
                
                            