<?php
//検索機能のphp guest版 sessionを利用しない
//入力された語を$searchwordに入れる
$searchword = $_POST["search_word"];

try{
    //DBに接続
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
                    <p>Guest</p>
                </div>
                <div class="header_right">
                    <a href="login_index.php" class="btn sign-in">sign-in</a>
                </div>
            </div>
        </header>
        <div class="top_logo">
            <h2>Arrange Storage</h2>
        </div>
        <div class="search_wrapper">
            <form action="search_guest.php" method="post">
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
        <form action = "top_page_not_login.php" method = "post">
            <input type = "submit"  name = "submit" value = "Top" class="btn_top">
        </form>
        <footer>
            <hr>
            <p>Arrange Storage (2021)</p>
        </footer>
    </body>
</html>