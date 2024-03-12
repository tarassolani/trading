<?php
//Script usato per il logout
//Elimina il cookie di login / la sessione
session_start();

//Cancella il cookie
if (isset($_COOKIE['login-info'])) {
    unset($_COOKIE['login-info']);
    setcookie('login-info', '', time() - 3600, '/');
}

$_SESSION = array();

//Cancella la sessione
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

session_destroy();

//Reindirizza alla pagina index dopo aver eseguito il login
header("Location: ../index.php");
exit();
?>