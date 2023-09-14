let remember = "";

//get if local storage contain some token for remember me and store it
if (!localStorage.getItem("rememberMe")) {
    localStorage.href = 'http://php-dev-1.online';
}
if (localStorage.getItem("rememberMe") != null) {
    remember = JSON.parse(localStorage.getItem("rememberMe"));
}

// if token was found then send it for php treatment
if(remember != ""){
     //HTTP request : URL and object
    fetch('http://php-dev-1.online/treatment/remember.php',{ 
        method: 'POST',
        body: JSON.stringify(remember),  //object of request transformed in json
        headers: {  //object is send to the server as
            "content-Type" : 'application/json' 
        }   
    }).then((response) => { //receive the server response
        if(response.ok){ //check if response is HTTP OK == 200
            return response.json(); //then send the response to a further promise
        }
    })
    .then((data) => { //process the response send by the previous promise
        if(data == "access authorized"){ //only this response is processed and send the user on his dashbord
            window.location.href = "http://php-dev-1.online/treatment/contact_display.php";
        }else if(data == "access denied"){
            //check in localstorage if exist and remove item remember
            if (!localStorage.getItem("rememberMe")) {
                localStorage.href = 'http://php-dev-1.online';
            }
            if (localStorage.getItem("rememberMe") != null) {
                localStorage.removeItem("rememberMe");
            }
            window.location.href = "http://php-dev-1.online/treatment/logout.php";
        }else if(data == "error503.html"){
            window.location.href = "http://php-dev-1.online/treatment/error503.html";
        }//other responses send the user on login page
    })
    .catch(error => { //if there was some error 
        console.error("error fetch : ", error);
    });
}
