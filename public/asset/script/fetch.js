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
                            if(response.status==400){
                                const expired = await response.json()
                                if(expired.Error == 'Expired token'){
                                    location.href ='https://localhost:8000/api/localToken';
                                }
                                else{
                                    error.textContent = expired.Error;
                                }
                            }
                            if(response.status == 200){
                                const liste = await response.json();
                                console.log(liste);
                            }
                            //Gérer l'erreur liste vide
                            else{
                                const jsonResponse = await response.json();
                                error.textContent = jsonResponse.Error;
                            }
                        }
                    );
        
        
                
                            