//get the link for logout 
const logout = document.querySelector("#logout");

// if user click on then 
logout.addEventListener('click', function(){
    //check in localstorage if exist and remove item remember
    if (!localStorage.getItem("rememberMe")) {
        localStorage.href = 'http://php-dev-1.online';
    }
    if (localStorage.getItem("rememberMe") != null) {
        localStorage.removeItem("rememberMe");
    }
});