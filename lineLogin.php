<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml' lang='ja' xml:lang='ja'>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
<title>LINE Login v2 Sample</title>
</head>
<body>
<?php
/******  このサンプルについて
 * LINEログイン用のSDKなどは利用していません。
 * あくまでcosmicへのLINE識別子を送信する処理のサンプルとなっております。
 * LINEログインの実装についての質問はお答え致しかねます。
 ******/


//LINEログインのチャンネルIDとシークレットを設定します。(Messaging API用のものではないので注意ください。)
//サンプルのため、直接記載していますが、流出を防ぐために、別の場所に格納されることをおすすめ致します。
$channel_id = '[チャネルIDを記載]';
$channel_id_secret = '[シークレットを記載]';


//セッションを保持するための値等をコールバック先まで引き継ぐ場合に利用します。
$state = "";

//ログイン終了時のコールバックURL(サンプルを動作させる場合は、同梱のlineCallback.phpを指定ください)
//コールバック先のURLはSSL化されていなければなりません。
$callback = urlencode('https://' . filter_input(INPUT_SERVER, 'HTTP_HOST'). '/lineCallback.php');

//LINEログインのURL
$url = 'https://access.line.me/dialog/oauth/weblogin?response_type=code&client_id=' . $channel_id . '&redirect_uri=' . $callback . '&state='. $state;
?>
	<a href='<?=$url?>'><img src="./img/btn_line_login_base.png"></a>
	
</body>
</html>