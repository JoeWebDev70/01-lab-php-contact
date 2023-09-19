//get the link for logout 
const logout = document.querySelector("#logout");

// if user click on then 
logout.addEventListener('click', function(){
    //check in localstorage if exist and remove item remember
    if (localStorage.getItem("rememberMe") != null) {
        localStorage.removeItem("rememberMe");
    }
});