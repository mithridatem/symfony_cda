let urlCourante = document.location.href;
const id = urlCourante.substring(urlCourante.lastIndexOf( "/" )+1);
const url = 'https://localhost:8000/api/articles/get/id/'+id;
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
                                //test sinon autre message d'erreur
                                else{
                                    error.textContent = expired.Error;
                                }
                            }
                            //test si l'article existe
                            if(response.status == 200){
                                const liste = await response.json();
                                console.log(liste);
                                let article = document.createElement('div');
                                let titre = document.createElement('h2');
                                titre.textContent = liste.titre;
                                let content = document.createElement('div');
                                content.textContent = liste.contenu;
                                let date = document.createElement('h3');
                                date.textContent = liste.date.substring(0,10);
                                articles.appendChild(article);
                                article.appendChild(titre);
                                article.appendChild(content);
                                article.appendChild(date);
                            }
                            //Gérer l'erreur liste vide
                            else{
                                const jsonResponse = await response.json();
                                error.textContent = jsonResponse.Error;
                            }
                        }
                    );