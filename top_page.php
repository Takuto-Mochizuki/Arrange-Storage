<?php
session_start();
$name = $_SESSION["NAME"];
?>
<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <title>top_page</title>
        <link href="stylesheet.css" rel="stylesheet">
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
                    <a href="del_select.php" class="btn edit"><span class="far fa-edit"></span></a>
                    <a href="logout.php" class="btn logout"><span class="fas fa-sign-out-alt"></span></a>
                </div>
            </div>
        </header>
        <br>
        <div class="top_wrapper">
            <div class="top_logo">
                <h1>Arrange Storage</h1>
            </div>
            <div class="search_wrapper">
                <form action="search.php" method="post">
                    <input type="text" name="search_word" class="search_holder">
                    <input type="submit" name="search_submit" value="&#xf002;" class="fas fa-search">
                </form>
            </div>
            <div class="top_words">
                <h3>Free library of arranged works.</h3>
                <h3>Every Instrument has its own beautiful sound.</h3>
                <h3>You can share your works with us.</h3>
            </div>
        </div>
        <footer>
            <hr>
            <p>Arrange Storage (2021)</p>
        </footer>

    </body>
</html>