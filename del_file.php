<?php
session_start();
require_once('config.php');

$author=$_SESSION["NAME"];
$del_file=$_POST["del_file"];

//サーバーにアップロードされたファイルを削除
if(isset($del_file)){
    $sql = 'SELECT from uploaded_files where filename=:filemane and auther=:auther';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':filename', $del_file, PDO::PARAM_INT);
    $stmt->bindParam(':auther', $author, PDO::PARAM_INT);
    $stmt->execute();
    foreach($stmt as $row){
        $file_path = "https://tb-221199.tech-base.net/Arrange_Storage/".$row["file_path"];
        unlink($file_path);
    }
}

//データベースから削除
if(isset($del_file)){
    $sql = 'delete from uploaded_files where filename=:filemane and auther=:auther';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':filename', $del_file, PDO::PARAM_INT);
    $stmt->bindParam(':auther', $author, PDO::PARAM_INT);
    $stmt->execute();
}

echo "削除しました";

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>del_file</title>
    </head>
    <body>
        <a href="top_page.php">top</a>
    </body>
</html>
