<?php

function getIp() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    
    return $ip;
}

function authMiddleware() {
    $id_token = null;

    if (isset($_REQUEST['id_token'])) {
        $id_token = $_REQUEST['id_token'];
    }

    if (!$id_token) {
        $id_token = $_SESSION['id_token'];
    }

    if (!isset($id_token) || !$id_token || !dbHelper->checkLogin($id_token)) {
        $id_token = null;
        $_SESSION['id_token'] = null;
    }

    if (!$id_token) {
        $ip = getIp();

        if (isset($_REQUEST['userkey'])) {
            dbHelper->setParams(['userkey' => $_REQUEST['userkey']]);
        }

        $anon = dbHelper->findAnon($ip);

        if ($anon) {
            dbHelper->setParams(['userkey' => $anon['key']]);
            $_SESSION['userkey'] = $anon['key'];
            return;
        }

        $anon = dbHelper->registerAnonymously($ip);
    }
}

function authAnonymously() {
    if (!$_SESSION['userkey']) {
        return false;
    }
}

function middleware($reqUri, $middleware = null)
{
    authMiddleware();

    // if ($middleware === 'auth') {
    //     if (!authMiddleware()) {
    //         redirect('/login');
    //     }
    // }

    // if ($middleware === 'auth-api') {
    //     if (!authMiddleware()) {
    //         abort401();
    //     }
    // }

    if ($reqUri === "/") {
        return;
    }

    if ($reqUri !== "/") {
/*
        if (!isset($_SESSION['access_token'])) {

            redirect("/");
           
        }
*/
    }

    if ($reqUri === "/") {

        if (isset($_SESSION['access_token'])) {

            redirect("/home");
           
        }
    }
   

}
