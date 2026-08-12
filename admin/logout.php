<?php

/*
|--------------------------------------------------------------------------
| CORE LTD - ADMIN LOGOUT
|--------------------------------------------------------------------------
*/

session_start();

/*
|--------------------------------------------------------------------------
| Prevent cached admin pages from being displayed after logout
|--------------------------------------------------------------------------
*/

header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Expires: 0');

/*
|--------------------------------------------------------------------------
| Clear all session variables
|--------------------------------------------------------------------------
*/

$_SESSION = [];

/*
|--------------------------------------------------------------------------
| Delete the session cookie
|--------------------------------------------------------------------------
*/

if (ini_get('session.use_cookies')) {

    $params = session_get_cookie_params();

    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

/*
|--------------------------------------------------------------------------
| Destroy the session
|--------------------------------------------------------------------------
*/

session_destroy();

/*
|--------------------------------------------------------------------------
| Redirect to admin login
|--------------------------------------------------------------------------
*/

header('Location: login.php');
exit;