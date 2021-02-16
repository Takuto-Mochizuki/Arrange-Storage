<?php
//ログインフォームを作っている

function h($s) {
    return htmlspecialchars($s, ENT_QUOTES, 'utf-8');
}
session_start();

//ログイン済みの場合
if (isset($_SESSION['NAME'])) {
    header("location: top_page.php");

    exit;
}
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <title>Login</title>
    </head>
    <body>
        <h1>ようこそ、ログインしてください</h1>
        <form action="login.php" method="post">
            <label for="name">name</label>
            <input type="text" name="name">
            <label for="password">password</label>
            <input type="password" name="password">
            <button type="submit">Sign In!</button>
        </form>
        <h1>初めての方はこちら</h1>
        <form action="signup_mail.php" method="post">
            <button type="submit">こちら</button>
        </form>

    </body>
</html>