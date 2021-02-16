<?php
//SESSIONでを始めて、ユーザー名とパスワードの情報を保持 ファイルのアップロードの際に、その情報を登録
session_start();
require 'common.php'; //データベースに接続　その他情報を読み込む

if(isset($_FILES["upfile"])){


    function file_upload(){
        // POSTではないとき何もしない
        if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') !== 'POST'){
            return;
        }

        // タイトル
        $title = filter_input(INPUT_POST, 'title');
        if ('' === $title) {
            throw new Exception('ファイル名は入力必須です。');
        }

        // アップロードファイル
        $upfile = $_FILES['upfile'];

        /**
        * @see http://php.net/manual/ja/features.file-upload.post-method.php
        */

        if ($upfile['error'] > 0){
            throw new Exception('ファイルアップロードに失敗しました。');
        }

        $tmp_name = $upfile['tmp_name'];

        // ファイルタイプチェック
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimetype = finfo_file($finfo, $tmp_name);

        // 許可するMIMETYPE
        $allowed_types = [
            'pdf' => 'application/pdf'
        ];

        if (!in_array($mimetype, $allowed_types)){
            throw new Exception('許可されていないファイルタイプです。');
        }

        // ファイル名（ハッシュ値でファイル名を決定するため、同一ファイルは同盟で上書きされる）
        $filename = sha1_file($tmp_name);

        // 拡張子
        $ext = array_search($mimetype, $allowed_types);

        // 保存作ファイルパス
        $destination = sprintf('%s/%s.%s'
            , 'upfiles'
            , $filename
            , $ext
        );

        // アップロードディレクトリに移動
        if (!move_uploaded_file($tmp_name, $destination)){
            throw new Exception('ファイルの保存に失敗しました。');
        }

        // データベースに登録
        $sql = 'INSERT INTO `uploaded_files` (`id`, `filename`, `file_path`,`youtube_link`,`auther`,`ayther_password`,`uploaded_at`) 
        VALUES (NULL, :title, :path, :youtube_link, :auther, :ayther_password, now()) ';
        $arr = [];
        $arr[':title'] = $title;
        $arr[':path'] = $destination;
        $arr[':youtube_link'] = $_POST["youtube_link"];
        $arr[':auther'] = $_SESSION["NAME"];
        $arr[':ayther_password'] = $_SESSION["PASSWORD"];
        $lastInsertId = insert($sql, $arr);

        echo "ファイルアップロードに成功しました。";
    }

    try {
        // ファイルアップロード
        file_upload();
    } catch (Exception $e) {
        $error = $e->getMessage();
    }

}

?>
<!DOCTYPE HTML>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title></title>
        <style type="text/css">
            .error {
                color: red;
            }
        </style>
    </head>
    <body>
        <div id="wrap">
            <?php if (isset($error)) : ?>
                <p class="error"><?= h($error); ?></p>
            <?php endif; ?>
            <form action="" method="post" enctype="multipart/form-data">
                <p>
                    <label for="title">タイトル</label>
                    <input type="text" name="title" id="title" />
                </p>
                <p>
                    <label for="upfile">ファイル</label>
                    <input type="file" name="upfile" id="upfile" />
                </p>
                <p>
                    <label for="youtube_link">リンク（あれば）</label>
                    <input type="text" name="youtube_link" id="youtube_link">
                </p>
                <p>
                    <button type="submit">送信</button>
                </p>
            </form>
            <form action = "top_page.php" method = "post">
                <input type="submit" value="トップへ">
            </form>
        </div>
    </body>
</html>