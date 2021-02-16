<?php
//データベース接続
require_once('config.php');

session_start();
//クロスサイトリクエストフォージェリ（CSRF）対策
$_SESSION['token'] = base64_encode(openssl_random_pseudo_bytes(32));
$token = $_SESSION['token'];
//クリックジャッキング対策
header('X-FRAME-OPTIONS: SAMEORIGIN');

//成功・エラーメッセージの初期化
$errors = array();

if(empty($_GET)) {
	header("Location: registration_mail");
	exit();
}else{
	//GETデータを変数に入れる
	$urltoken = isset($_GET["urltoken"]) ? $_GET["urltoken"] : NULL;
	//メール入力判定
	if ($urltoken == ''){
		$errors['urltoken'] = "トークンがありません。";
	}else{
        try{
            //flagが0の未登録者 or 仮登録日から24時間以内
            $sql = "SELECT mail FROM pre_user WHERE urltoken=(:urltoken) AND flag =0 AND date > now() - interval 24 hour";
            $stm = $pdo->prepare($sql);
            $stm->bindValue(':urltoken', $urltoken, PDO::PARAM_STR);
            $stm->execute();

            //レコード件数取得
            $row_count = $stm->rowCount();

            //24時間以内に仮登録され、本登録されていないトークンの場合
			if($row_count == 1){
				$mail_array = $stm->fetch();
				$mail_address = $mail_array["mail"];
				$_SESSION['mail'] = $mail_address;
			}else{
                $errors['urltoken_timeover'] = "このURLはご利用できません。
                有効期限が過ぎたかURLが間違っている可能性がございます。もう一度登録をやりなおして下さい。";
            }
            //データベース接続切断
            $stm = null;
        }catch (PDOException $e){
            print('Error:'.$e->getMessage());
            die();
        }

    }
}

/**
* 確認する(btn_confirm)押した後の処理
*/
if(isset($_POST['btn_confirm'])){
	if(empty($_POST)) {
		header("Location: registration_mail.php");
		exit();
	}else{
		//POSTされたデータを各変数に入れる
		$name = isset($_POST['name']) ? $_POST['name'] : NULL;
		$password = isset($_POST['password']) ? $_POST['password'] : NULL;
		
		//セッションに登録
		$_SESSION['name'] = $name;
		$_SESSION['password'] = $password;

		//アカウント入力判定
		//パスワード入力判定
		if ($password == ''):
			$errors['password'] = "パスワードが入力されていません。";
		else:
			$password_hide = str_repeat('*', strlen($password));
		endif;

		if ($name == ''):
			$errors['name'] = "氏名が入力されていません。";
		endif;
		
	}
	
}

/* page_3
* 登録(btn_submit)押した後の処理
*/
if(isset($_POST['btn_submit'])){
	//パスワードのハッシュ化
	$password_hash =  password_hash($_SESSION['password'], PASSWORD_DEFAULT);

	//ここでデータベースに登録する
	try{
		$sql = "INSERT INTO user (name,password,mail,status,created_at,updated_at) VALUES (:name,:password_hash,:mail,1,now(),now())";
        $stm = $pdo->prepare($sql);
		$stm->bindValue(':name', $_SESSION['name'], PDO::PARAM_STR);
		$stm->bindValue(':mail', $_SESSION['mail'], PDO::PARAM_STR);
		$stm->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
		$stm->execute();

		//pre_userのflagを1にする(トークンの無効化)
		$sql = "UPDATE pre_user SET flag=1 WHERE mail=:mail";
		$stm = $pdo->prepare($sql);
		//プレースホルダへ実際の値を設定する
		$stm->bindValue(':mail', $mail_address, PDO::PARAM_STR);
        $stm->execute();
        
        //これ以下、PHPMailerにて、登録されたアドレスにメールを送信する
		require 'src/Exception.php';
		require 'src/PHPMailer.php';
		require 'src/SMTP.php';
		require 'setting.php';

		// PHPMailerのインスタンス生成
    	$mail = new PHPMailer\PHPMailer\PHPMailer();

    	$mail->isSMTP(); // SMTPを使うようにメーラーを設定する
    	$mail->SMTPAuth = true;
    	$mail->Host = MAIL_HOST; // メインのSMTPサーバー（メールホスト名）を指定
    	$mail->Username = MAIL_USERNAME; // SMTPユーザー名（メールユーザー名）
    	$mail->Password = MAIL_PASSWORD; // SMTPパスワード（メールパスワード）
    	$mail->SMTPSecure = MAIL_ENCRPT; // TLS暗号化を有効にし、「SSL」も受け入れます
    	$mail->Port = SMTP_PORT; // 接続するTCPポート

    	// メール内容設定
    	$mail->CharSet = "UTF-8";
    	$mail->Encoding = "base64";
    	$mail->setFrom(MAIL_FROM,MAIL_FROM_NAME);
    	$mail->addAddress($mail_address, 'GUESTさん'); //受信者（送信先）を追加する
    	$mail->Subject = MAIL_SUBJECT; // メールタイトル
    	$mail->isHTML(true);    // HTMLフォーマットの場合はコチラを設定します
    	$body = 'ようこそ Arrange Storage へ！<br><br>会員登録が完了しました。';

    	$mail->Body  = $body; // メール本文
    	// メール送信の実行
    	if(!$mail->send()) {
    		echo 'メッセージは送られませんでした！';
    		echo 'Mailer Error: ' . $mail->ErrorInfo;
    	} else {
    		echo '送信完了！';
    	}

        //データベース接続切断
		$stm = null;

		//セッション変数を全て解除
		$_SESSION = array();
		//セッションクッキーの削除
		if(isset($_COOKIE["PHPSESSID"])) {
				setcookie("PHPSESSID", '', time() - 1800, '/');
		}
		//セッションを破棄する
        session_destroy();
    }catch(PDOException $e) {
        //トランザクション取り消し（ロールバック）
		$pdo->rollBack();
		$errors['error'] = "もう一度やりなおして下さい。";
		print('Error:'.$e->getMessage());
    }
}

?>

<h1>会員登録画面</h1>

<!-- page_3 完了画面-->
<?php if(isset($_POST['btn_submit']) && count($errors) === 0): ?>
本登録されました。

<!-- page_2 確認画面-->
<?php elseif (isset($_POST['btn_confirm']) && count($errors) === 0): ?>
	<form action="<?php echo $_SERVER['SCRIPT_NAME'] ?>?urltoken=<?php print $urltoken; ?>" method="post">
		<p>メールアドレス：<?=htmlspecialchars($_SESSION['mail'], ENT_QUOTES)?></p>
		<p>パスワード：<?=$password_hide?></p>
		<p>氏名：<?=htmlspecialchars($name, ENT_QUOTES)?></p>
		
		<input type="submit" name="btn_back" value="戻る">
		<input type="hidden" name="token" value="<?=$_POST['token']?>">
		<input type="submit" name="btn_submit" value="登録する">
	</form>

<?php else: ?>
<!-- page_1 登録画面 -->
	<?php if(count($errors) > 0): ?>
        <?php
        foreach($errors as $value){
            echo "<p class='error'>".$value."</p>";
        }
        ?>
    <?php endif; ?>
		<?php if(!isset($errors['urltoken_timeover'])): ?>
			<form action="<?php echo $_SERVER['SCRIPT_NAME'] ?>?urltoken=<?php print $urltoken; ?>" method="post">
				<p>メールアドレス：<?=htmlspecialchars($mail_address, ENT_QUOTES, 'UTF-8')?></p>
				<p>パスワード：<input type="password" name="password"></p>
				<p>氏名：<input type="text" name="name" value="<?php if( !empty($_SESSION['name']) ){ echo $_SESSION['name']; } ?>"></p>
				<input type="hidden" name="token" value="<?=$token?>">
				<input type="submit" name="btn_confirm" value="確認する">
			</form>
		<?php endif ?>
<?php endif; ?>