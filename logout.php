<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>logout</title>
    </head>
    <body>
        <p>Arrange Storage</p>
<?php
session_start();
$output = '';
if(isset($_SESSION["NAME"])){
    $output = 'Logoutしました。';
} else {
    $output = 'SessionがTimeoutしました。';
}
//セッション変数のクリア
$_SESSION = array();
//セッションクッキーも削除
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
//セッションクリア
@session_destroy();

echo $output;

?>
        <a href="top_page_not_login.php">Top</a>
    </body>
</html>