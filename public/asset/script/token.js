let error = document.querySelector('#error');
let mail = prompt('Saisir votre mail');
let password = prompt('Saisir votre password');
const url = 'https://localhost:8000/api/register';
let json = JSON.stringify({email:mail, password:password});
//stockage du token JWT dans le local storage 
const token = fetch(url, {
                            method:'POST',
                            body:json
                        }
                )
                .then(async response=>{
                        if(response.status==200){
                            const jwt = await response.json();
                            localStorage.setItem('jwt', jwt.Token_JWT);
                        }
                        else{
                            const erreur = await response.json();
                            error.textContent = erreur.Error;
                        }
                    }
                )