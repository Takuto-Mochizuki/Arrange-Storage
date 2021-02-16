<?php
//検索機能のphp

//sessionを受け継ぐ
session_start();
//名前を$nameに入れる
$name = $_SESSION["NAME"];
//入力された語を$searchwordに入れる
$searchword = $_POST["search_word"];

try{
    require_once('config.php');

    //SQL文を実行して、結果を$stmtに代入する。
    $stmt = $pdo->prepare(" SELECT * FROM uploaded_files WHERE filename LIKE '%" . $_POST["search_word"] . "%' "); 

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
        <meta charset="utf-8">
        <title>search_files</title>
        <link href="stylesheet_search.css" rel="stylesheet">
        <script src="https://kit.fontawesome.com/d05be3f247.js" crossorigin="anonymous"></script>
    </head>
    <body>
        <header>
            <div class="container">
                <div class="header_left">
                    <p><?php echo $name?></p>
                </div>
                <div class="header_right">
                    <a href="upload_page.php" class="btn upload"><span class="fas fa-file-upload"></span></a>
                    <a href="#" class="btn edit"><span class="far fa-edit"></span></a>
                    <a href="logout.php" class="btn logout"><span class="fas fa-sign-out-alt"></span></a>
                </div>
            </div>
        </header>
        <div class="top_logo">
            <h2>Arrange Storage</h2>
        </div>
        <div class="search_wrapper">
            <form action="search.php" method="post">
                <input type="text" name="search_word" class="search_holder">
                <input type="submit" name="search_submit" value="&#xf002;" class="fas fa-search">
            </form>
        </div>
        <div class="result">
            <p>result</p>
            <hr>
            <!-- ここでPHPのforeachを使って結果をループさせる -->
            <?php foreach ($stmt as $row): ?>
                <h3><?php echo $row[1]."<br>"?></h3>
                <p>author <?php echo "\t".$row[5]."<br>"?></p>
                <a href="<?php echo "https://tb-221199.tech-base.net/Arrange_Storage/".$row[2]?>" target="_blank">
                <?php echo "https://tb-221199.tech-base.net/Arrange_Storage/".$row[2]?></a>
                <p>uploaded at <?php echo $row[7]?></p>
                <hr>
            <?php endforeach; ?>
        </div>
        <form action = "top_page.php" method = "post">
            <input type = "submit"  name = "submit" value = "Top" class="btn_top">
        </form>
        <footer>
            <hr>
            <p>Arrange Storage (2021)</p>
        </footer>
    </body>
</html>
