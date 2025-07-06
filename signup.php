<?php
//ユーザー機能
/*
1,POST送信
2,バリデーション
3,DB接続
4,INSERT
5,id取って、セッション格納
6,マイページ遷移

//準備
・エラー表示出力、ログ出力規定
・デバッグ表示機能作る
・セッションどうするか。=>ログイン機能で作る。
・debugのスタートで、現在時刻、セッションID、セッション変数のuser_id,ログインの有効期限、ログインリミットを出したり比べたり
*/

//共通関数
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「ユーザー機能画面処理開始');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//=====================================
//画面処理開始
//=====================================

//POST送信
if(!empty($_POST)){
  debug('POST送信があります.');
  //未入力
  validRequired($_POST['email'],'email');
  validRequired($_POST['pass'],'pass');
  validRequired($_POST['pass_re'],'pass_re');

  if(empty($err_msg)){
    //変数に格納
    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $pass_re = $_POST['pass_re'];

    //emailの形式、最大文字数、emailの重複
    validEmail($email,'email');
    if(empty($err_msg)){
      validMaxLen($email,'email');
    }
    if(empty($err_msg)){
      validMailDup($email,'email');
    }
    //passの最小文字数、半角英数字＋きごう、最大文字数
    validPass($pass,'pass');
    if(empty($err_msg)){
      validMaxLen($pass,'pass');
    }
    if(empty($err_msg)){
      validMinLen($pass,'pass');
    }
    if(empty($err_msg)){
      validMatch($pass,$pass_re,'pass');
    }
    if(empty($err_msg)){
      debug('バリデーションOK');
      //例外処理
      try{
        debug('DB接続します');
        //DB接続
        $dbh = dbConnect();
        //SQL
        $sql = 'INSERT INTO users(email, pass, create_date) VALUES(:email, :pass, :c_date)';
        //data
        $data = array(
          ':email' => $email,
          ':pass' => password_hash($pass,PASSWORD_DEFAULT),
          ':c_date' => date('Y-m-d H:i:s')
        );
        //クエリ実行
        $stmt = queryPost($dbh,$sql,$data);
        $id_number = $dbh->lastInsertId();
        if($id_number){
          //ユーザーID
          $_SESSION['user_id'] = $id_number;
          //ログイン日時、ログインリミット
          $_SESSION['login_date'] = time();
          $_SESSION['login_limit'] = 60*60;
          debug('マイページに遷移します');
          header('Location:mypage.php');
          exit;
        }
      }catch(Exception $e){
        debug('エラー発生:'.$e->getMessage());
        $err_msg['common'] = MSG07;
      }
    }



  }

}
debug('画面実装処理終了<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>

<?php
$siteTitle = 'ユーザー登録';
require('head.php');
?>
<body>
  <!--ヘッダー-->
  <?php
  require('header.php'); 
  ?>

  <div class="site-width">
   <div class="one-columns-site">
    <h1 class="title">ユーザー登録</h1>
    <div class="area-msg">
    <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
    </div>
    <form class="simple-form" method="post">
      <label class="">
        メールアドレス<span class="lab-asterisk">*</span>
        <input type="text" name="email" class="js-valid-text js-valid-email" value="<?php if(!empty($_POST['email'])) echo $_POST['email']; ?>" placeholder="inu@gmail.com">
      </label>
      <div class="area-msg">
      <?php if(!empty($err_msg['email'])) echo $err_msg['email']; ?>
      </div>
      <label class="">
        パスワード<span class="lab-asterisk">* 半角英数字のみ</span>
        <input type="password" name="pass" class="js-valid-text js-valid-pass" value="<?php if(!empty($_POST['pass'])) echo $_POST['pass']; ?>" placeholder="inuinuinu">
      </label>
      <div class="area-msg">
        <?php if(!empty($err_msg['pass'])) echo $err_msg['pass']; ?>
      </div>
      <label class="">
        パスワード(再入力)<span class="lab-asterisk">*</span>
        <input type="password" name="pass_re" class="js-valid-text js-valid-pass-re" value="<?php if(!empty($_POST['pass_re'])) echo $_POST['pass_re']; ?>" placeholder="inuinuinu">
      </label>
      <div class="area-msg">
      <?php if(!empty($err_msg['pass_re'])) echo $err_msg['pass_re']; ?>
      </div>
      <input type="submit" name="submit" value="送信" class="simple-btn">
    </form>
    <div class="divider">
      <span>または</span>
    </div>
    <p class="divider-point"><a href="login.php">登録済みの方はこちら</a></p>
   </div>
  </div>
  <!--フッター-->
  <?php
  require('footer.php');
  ?>
  
</body>
</html>