<?php
    
    session_start();
    session_destroy();
    
    //check in localstorage if exist and remove item remember
    echo '<script>
                if (localStorage.getItem("rememberMe") != null) {
                    localStorage.removeItem("rememberMe");
                }
                window.location.href = "http://php-dev-1.online/";
          </script>'
    
?>