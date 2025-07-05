<?php
//ログイン機能
/*
0,自動認証
1,POST送信
2,バリデーション
3,DB接続
4,mypage遷移

*/

//共通関数
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「ログイン機能開始');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//=====================================
//画面処理開始
//=====================================

//自動認証
require('auth.php');

//POST送信
if(!empty($_POST)){
  debug('POST送信があります');
  debug('POSTノナカミ:'.print_r($_POST,true));
  //未入力
  validRequired($_POST['email'],'email');
  validRequired($_POST['pass'],'pass');
  if(empty($err_msg)){
    //変数にまとめる
    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $stay_login = (!empty($_POST['stay-login'])) ? $_POST['stay-login']: '';
    //email,形式チェック,最大文字数
    validEmail($email,'email');
    if(empty($err_msg)){
      validMaxLen($email,'email');
    }
    //pass 半角英数字、最小、最大
    validPass($pass,'pass');
    if(empty($err_msg)){
      validMinLen($pass,'pass');
    }
    if(empty($err_msg)){
      validMaxLen($pass,'pass');
    }

    if(empty($err_msg)){
      debug('バリデーションOK');
      //例外処理
      try{
        //db接続
        $dbh = dbConnect();
        //sql
        $sql = 'SELECT id, pass FROM users WHERE email = :email AND delete_flg = 0';
        //data
        $data = array(
          ':email' => $email,
        );
        //クエリ実行
        $stmt = queryPost($dbh,$sql,$data);
        //結果を格納
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if($result['pass'] && password_verify($pass,$result['pass'])){
          debug('パスワード一致しました');
          //セッションIDとlogin_date,login_limitをそれぞれ格納
          $_SESSION['user_id'] = $result['id'];
          //ログイン日時
          $_SESSION['login_date'] = time();
          //デフォルト有効期限
          $sesLimit = 60*60;//１h
          if(!empty($stay_login)){
            $_SESSION['login_limit'] = $sesLimit*24*30;//1ヶ月
          }else{
            $_SESSION['login_limit'] = $sesLimit;
          }
          debug('セッション変数の中身:'.print_r($_SESSION,true));
          debug('マイページに遷移します');
          //マイページに遷移します.
          header('Location:mypage.php');
          exit;
        }else{
          $err_msg['common'] = MSG09;
        }

      }catch(Exception $e){
        debug('エラー発生:'.$e->getMessage());
        $err_msg['common'] = MSG07;
      }
    }
  }

}

?>

<?php
$siteTitle = 'ログイン';
require('head.php'); 
?>
  <!--ヘッダー-->
  <?php
  require('header.php'); 
  ?>
  <?php if(!empty($_SESSION['msg_success'])){ ?>
    <div class="msg-flash js-msg-flash">
      <?php echo getSessionFlash($_SESSION['msg_success']); ?>
    </div>
  <?php }?>

  <div class="site-width">
   <div class="one-columns-site">
    <h1 class="title">ログイン</h1>
    <div class="area-msg" style="text-align:center;">
      <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
    </div>
    <form class="simple-form" method="post">
      <label class="">
        email<span class="lab-asterisk">*</span>
        <input type="text" name="email" class="js-valid-email js-valid-text" value="<?php if(!empty($_POST['email'])) echo $_POST['email'];?>" placeholder="inu@gmail.com">
      </label>
      <div class="area-msg">
      <?php if(!empty($err_msg['email'])) echo $err_msg['email']; ?>
      </div>
      <label class="">
        password<span class="lab-asterisk">*</span>
        <input type="password" name="pass" class="js-valid-pass js-valid-text" value="<?php if(!empty($_POST['pass'])) echo $_POST['pass'];?>" placeholder="inuinuinu">
      </label>
      <div class="area-msg">
      <?php if(!empty($err_msg['pass'])) echo $err_msg['pass']; ?>
      </div>
      <input type="checkbox" name="stay-login"><span class="stay_login">ログインしたままにする</span>
      <input type="submit" name="submit" value="送信"  class="simple-btn">
    </form>

    <p class="divider-point"><a href="passRemind.php">パスワード忘れた方はこちら</a></p>
   </div>
  </div>
    <!--フッター-->
    <?php 
    require('footer.php');
     ?>
</body>
</html>