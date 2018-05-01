<?php
session_start();
    //handles some redirects depending if user is admin or not
    if (empty($_SESSION['userid'])) {
    
        header("Location: index.php"); /* Redirect browser */;
    }elseif($_SESSION['admin']!=true){
        header("Location: seuranta.php"); /* Redirect browser */;
}

?>