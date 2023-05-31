let articles = document.querySelector('#articles');
let error = document.querySelector('#error');
const urlRegister = 'https://localhost:8000/api/register';
const urlArticles = 'https://localhost:8000/api/articles/get/all';
let json = JSON.stringify(
    {
        email:'mathieumithridate@adrar-formation.com',password:'1234'
    }
);
const token = fetch(urlRegister,{
                    method: 'POST',
                    body:json}
                    ).then(async response=>{
                            const jwt = await response.json();
                            console.log(jwt.Token_JWT);
                            headers = { 'Authorization': 'Bearer '+jwt.Token_JWT }
                            fetch(urlArticles, {method:'GET', headers
                            }).then(async response1=>{
                               const liste = await response1.json();
                               console.log(liste);
                            })
                    }
                );