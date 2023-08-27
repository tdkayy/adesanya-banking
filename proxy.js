const api_url = 'https://www.expensify.com/api';

//make a request to the PHP API proxy
fetch(`proxy.php?url=${encodeURIComponent(api_url)}`)
    .then(response => response.json())
    .then(data => {
        console.log(data);
        //handle the API response data
    })
    .catch(error => {
        console.error(error);
        //handle any errors
    });