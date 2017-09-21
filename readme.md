# LINE識別子連携処理

※現在公開されている情報については開発中の情報となるためリリース時には変更になる箇所があるかもしれません。ご了承ください。

## LINE識別子連携

cosmicへ「サイトのユーザーID(以降『ユーザーID』)」と「LINE識別子」が１組になった情報を送信する処理です。


### 連携方法

ベースURL

```
https://t.cosmic4.com/lu/
```

### リクエストメソッド

|メソッド|処理|
|:-----|:-----|
|POST|組となったIDと識別子の登録、更新を行います|
|DELETE|パラメータに指定したユーザーIDとLINE識別子の組を削除します|

### パラメータ

|パラメータ名|設定内容|
|:-----------|:-----------|
|cm4pi|サイトID|
|cm4ui|サイトのユーザーID|
|luid|LINE識別子|

### 戻り

正常に終了した場合のhttp_response_codeは200となります。　処理結果がjsonでレスポンスされます。

```json
{"message":"success"}
```

エラーとなった場合のhttp_response_codeは一律400となります。 エラー内容がjsonでレスポンスされます。
以下がエラーメッセージの例です。(この場合、LINE識別子がパラメータに無い)
```json
{"message":"line_user_id is required. site_id = pXXXXX, site_userid = XXXXXXXXXX, line_userid = "}
```

### 重複判定について

同一サイトID内では、単一のLINE識別子に複数のユーザーIDを割り当てることはできません。
すでに登録済みのユーザーIDに対して、異なるLINE識別子を送信した場合、後から送信したLINE識別子で上書きされます。


## サンプルプログラム

### 基本

LINEログイン処理後にLINE識別子とユーザーIDの組み合わせをcosmicへ送信する処理のサンプルです。ログイン完了時にユーザー情報を取得して識別子をcosmicへ送信します。
テキストリテラル内の[]で囲った箇所については、それぞれ必要な値がセットされるようにしてください。

```php
$channel_id = '[チャネルIDを記載]';
```

のような箇所は

```php
$channel_id = '1234567890';
```

のように修正します。


### サンプルの動作方法について

リポジトリをクローン後、SSLアクセス可能な箇所に設置して動作の確認を行ってください。LINEログインの設定時に、コールバックURLの設定をしますが、その時にhttps:// で始まるURLが必要となります。
