<?php

//sessionを受け継ぐ
session_start();
$name = $_SESSION["NAME"];
$password = $_SESSION["PASSWORD"];

try{
    //DBに接続
    $dsn = 'mysql:dbname=データベース名;host=ホスト名';
    $user = 'ユーザー名';
    $password = 'パスワード';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

    //SQL文を実行して、結果を$stmtに代入する。
    $stmt = $pdo->prepare(" SELECT * FROM uploaded_files WHERE auther=:name"); 
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);

    //実行する
    $stmt->execute();

    
} catch(PDOException $e){
    echo "ファイル検索に失敗しました。もう一度お試しください。:" . $e->getMessage() . "\n";
    exit();
}


?>

<!DOCTYPE html>
<html>
  <head>
    <meta　charset = "utf-8">
    <title>del_select</title>
  </head>

  <body>
    <h1>ファイルを選択</h1>
    <form action="del_file.php" method = "POST">
      <select name= "del_file">
        <?php foreach($stmt as $row): ?>
          <option value="<?php echo $row[1]?>"><?php echo $row[1]?></option>
        <?php endforeach; ?>
      </select>
      <input type="submit"name="submit"value="削除"/>
    </form>
  </body>
</html>