<?php 
//共通関数
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「パスワードリマインダー送信機能');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//自動認証はいらない。

/*
POST送信
バリデーション
仮のpassword作成
DBに保存
仮のアドレスをメール
ログインページに遷移
*/

//==========================================
//画面実装処理開始
//==========================================

if(!empty($_POST)){
  debug('POST送信があります。');

  //未入力
  validRequired($_POST['auth_key'],'auth_key');

  if(empty($err_msg)){
    //変数を代入
    $auth_key = $_POST['auth_key'];

    //セッションのtokenと入力された値が一致しているかどうか
    validMatch($auth_key,$_SESSION['token'],MSG12);

    if(empty($err_msg)){
      debug('バリデーションOKです。DB接続します。');
      //仮のパスワード
      $draft_pass = makeToken();
      debug('仮のパスワード：'.$draft_pass);
      //例外処理
      try{
        //DB接続
        $dbh = dbConnect();
        //SQL 
        $sql = 'UPDATE users SET pass = :pass WHERE email = :email';
        //data
        $data = array(
              ':pass' => password_hash($draft_pass,PASSWORD_DEFAULT),
              ':email' => $_SESSION['email']
        );
        //クエリ実行
        $stmt = queryPost($dbh,$sql,$data);
        if($stmt){
          debug('仮のパスワードをメールで送信します。');
          $to = $_SESSION['email'];
          $from = 'flipper11041006@yahoo.co.jp';
          $subject = '仮のパスワードをお送りいたします。';
          $comment = <<<EOT
{$to}様
お世話になります。
認証キーの入力いただきありがとうございます。
仮のパスワードを発行致しましたので、ご確認をお願い致します。

     仮パスワード：$draft_pass

何卒宜しくお願い致します。

～～～～～～～～～～～～～～～～～～～～～～～～
会社名
〇〇部
名前
〒000-0000 東京都〇〇〇〇
TEL：03-0000-0000 FAX： 03-0000-0000
Email：××××××＠××××.com
URL: https://××××.com/
～～～～～～～～～～～～～～～～～～～～～～～～
EOT;
            //メール送信
            $result = sendMail($to,$subject,$comment,$from);

            if($result){
              //セッション削除(token)
              unset($_SESSION['token']);
              $_SESSION['msg_success'] = SUC01;
              debug('ログインページに遷移します。');
              header("Location:login.php");
              exit;
            }
        }else{
          $err_msg['common'] = MSG07;
        }
      }catch(Exception $e){
        debug('エラー発生：'.$e->getMessage());
        $err_msg['common'] = MSG07;
      }
    }
  }

}



?>
<?php
$siteTitle = 'パスワード再設定(入力) ';
require('head.php');
?>
  <!--ヘッダー-->
  <?php
   require('header.php');
  ?>
  <!--
  <?php //if(!empty($_SESSION['msg_success'])){ ?>
  <div class="js-msg-flash msg-flash">
    <?php //echo getSessionFlash($_SESSION['msg_success']); ?>
  </div>
  <?php //} ?>
  -->

  <div class="site-width">
   <div class="one-columns-site">
    <h1 class="title">認証キーの入力</h1>
    <div class="area-msg" style="text-align:center;">
      <?php if(!empty($err_msg['common'])) echo $err_msg['common'];  ?>
    </div>
    <p class="passremind_msg">
      メールで届いた<br>
      認証キーを入力してください。
    </p>
    <form class="simple-form" method="post">
      <label class="">
        <input type="text" name="auth_key" class="js-valid-text" value="<?php if(!empty($_POST['auth_key'])) echo $_POST['auth_key'];?>" placeholder="認証キー">
      </label>
      <div class="area-msg">
        <?php if(!empty($err_msg['auth_key'])) echo $err_msg['auth_key']; ?>
      </div>
      <input type="submit" name="submit" value="送信" class="simple-btn">
    </form>
    <a href="passRemind.php" class="return-icon"><i class="fa-solid fa-chevron-left"></i>認証キー送信画面に戻る</a>
   </div>
  </div>
    <!--フッター-->
    <?php 
    require('footer.php'); ?>
</body>
</html>