<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml' lang='ja' xml:lang='ja'>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
<title>LINE Login v2 Sample Callback</title>
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

$callback = 'https://' . filter_input(INPUT_SERVER, 'HTTP_HOST'). '/lineCallback.php';
$code = filter_input(INPUT_GET, 'code');

if (isset($code)) {
	//codeからアクセストークン、リフレッシュトークンを取得します。
	//アクセストークン、リフレッシュトークンの取得には、 コールバック時のcodeと、チャンネルIDとシークレットが必要です。
	//(リダイレクトURIには何も指定しなくても大丈夫だと思います。(未確認))
	$url = 'https://api.line.me/v2/oauth/accessToken';
	$data = array(
		'grant_type' => 'authorization_code',
		'client_id' => $channel_id,
		'client_secret' => $channel_id_secret,
		'code' => $code,
		'redirect_uri' => $callback
	);
	$content = http_build_query($data, '', '&');
	$header = array(
		'Content-Type: application/x-www-form-urlencoded'
	);
	$context = [
		'http' => [
			'method'  => 'POST',
			'header'  => implode('\r\n', $header),
			'content' => $content
		]
	];
	//LINEをログインに利用する場合、取得したaccess_tokenとrefresh_tokenが必要になりますので、cookieで保持する等してください。
	$resultString = file_get_contents($url, false, stream_context_create($context));
	$result = json_decode($resultString, true);
 
	if(isset($result['access_token'])) {
		//取得したアクセストークン利用して、ユーザー情報を取得します。　ユーザー情報には、 ユーザー識別子、ユーザー表示名、プロフィール画像URLが含まれています。
		//詳しくはLINE DevelopersのSocial REST APIのリファレンスを参照ください。
		$url = 'https://api.line.me/v2/profile';
		$context = [
			'http' => [
				'method'  => 'GET',
				'header'  => 'Authorization: Bearer '. $result['access_token']
			]
		];
		$profileString = file_get_contents($url, false, stream_context_create($context));
		$profile = json_decode($profileString, true);
		
		//$profileにユーザー情報がレスポンスされます。　本サンプルでは、取得した情報を画面に表示しています。
		echo '<img src="'. $profile["pictureUrl"] . '" />';
		echo '<p>displayName : '. $profile["displayName"] . '</p>';
		echo '<p>userId : '. $profile["userId"] . '</p>';
		
		
		//cosmicへユーザーID,ユーザー識別子を送信
		$cosmic_send_url = 'https://t.cosmic4.com/lu/';
		$data =[
			'cm4pi'=>'[サイトID]',			//サイトID
			'cm4ui'=>'[ユーザーID]',			//サイトのユーザーID
			'luid' =>$profile['userId'],	//LINEユーザー識別子
		];

		$result = getApiDataCurl($cosmic_send_url, $data);
		
		echo "<p>コズミックへの識別子の送信:<b>$result</b></p>";
		
	}
} else {
	//ログインに失敗した場合の処理
	echo '<p>LINEログイン失敗</p>';
}

function getApiDataCurl($url, $data){
    $ch = curl_init($url);
	curl_setopt($ch, CURLOPT_POST, TRUE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);  // オレオレ証明書対策
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    $json    = curl_exec($ch);
    $info    = curl_getinfo($ch);
    $errorNo = curl_errno($ch);

    // OK以外はエラーなので空白配列を返す
    if ($errorNo !== CURLE_OK) {
        // 詳しくエラーハンドリングしたい場合はerrorNoで確認
        // タイムアウトの場合はCURLE_OPERATION_TIMEDOUT
        return "失敗(errorNo=$errorNo)";
    }

	$json_array = json_decode($json,TRUE);
	
	if($info['http_code'] !== 200){
		//コズミックへの送信に失敗した場合、httpレスポンスコードが400で帰り、原因が messageに記載されます。
		$return = '失敗('.$json_array['message'].')';
	} else {
		//送信結果に問題が無い場合、 httpレスポンスコードは200で、messageに「success(登録完了)」または「continue(登録済)」が帰ります。
		$return = '成功';
	}
    return $return;
}
?>
</body>
</html>