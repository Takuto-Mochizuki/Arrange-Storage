<?php 
require_once('config.php');

session_start();

//データベース内でPOSTされたユーザー名を検索
try {
    $pdo = new PDO($dsn, $user, $password);
    $stmt = $pdo->prepare('select * from user where name = ?');
    $stmt->execute([$_POST['name']]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
}catch(\Exception $e) {
    echo $e->getMessage() . PHP_EOL;
}

//emailがデータベース内に存在しているか確認
if(!isset($row['name'])) {
    echo 'ユーザー名又はパスワードが間違っています。';
    return false;
}

//パスワード確認後sessionにユーザー名とパスワードを渡す
if(password_verify($_POST['password'], $row['password'])) {
    session_regenerate_id(true); //session_idを新しく生成し、置き換える
    $_SESSION['NAME'] = $row['name'];
    $_SESSION['PASSWORD'] = $row['password'];
    header("location: top_page.php");
}else {
    echo 'ユーザー名又はパスワードが間違っています。';
    return false;
}
  
?>