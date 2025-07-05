<?php
/*
パスワードリマインダー機能
1,POST送信
2,バリデーション
3,DB接続
4,true：入力されたアドレスにメール送信(諸々の処理に合わせて、送るトークン作成も)
5,passRemindSend.phpに遷移

*/
//共通関数
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「パスワードリマインダー機能');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//自動認証はいらない。

//==========================================
//画面実装処理開始
//==========================================

//POST送信
if(!empty($_POST)){
  debug('POST送信があります。');
  //未入力バリデーション
  validRequired($_POST['email'],'email');

  if(empty($err_msg['email'])){
    //変数に代入
    $email = $_POST['email'];
    //email形式、最大
    validEmail($email,'email');
    if(empty($err_msg)){
      validMaxLen($email,'email');
    }
    if(empty($err_msg)){
      debug('バリデーションOK。');
      //例外処理
      try{
        //DB接続
        $dbh = dbConnect();
        //SQL
        $sql = 'SELECT count(*) FROM users WHERE email = :email AND delete_flg = 0';
        //data
        $data = array(':email' => $email);
        $stmt = queryPost($dbh,$sql,$data);
        $result = $stmt->fetchColumn();

        if($result > 0){
          debug('登録されているアドレスです。');
          //メール送る。
          /*
          送信先 $to:$email
          送信元 $from：
          $to,$subject,$comment,$from
          mb_send_mail
          */
          
        }else{
          debug('登録されていないアドレスです。');
          $err_msg['email'] = MSG10;
        }
      }catch(Exception $e){
        debug('エラー発生:'.$e->getMessage());
      }
    }
  }
}



?>

<?php 
$siteTitle = 'パスワード再設定';
require('head.php'); 
?>
  <!--ヘッダー-->
  <?php
  require('header.php');
  ?>

  <div class="site-width">
   <div class="one-columns-site">
    <h1 class="title">パスワード再設定</h1>
    <div class="area-msg">
    </div>
    <p class="passremind_msg">
      登録したメールアドレスを<br>
      入力してください。
    </p>
    <form class="simple-form" method="post">
      <label class="">
        <input type="text" name="email" value="<?php if(!empty($_POST['email'])) echo $_POST['email']; ?>" class="js-valid-text js-valid-email" placeholder="inu@gmail.com">
      </label>
      <div class="area-msg">
        <?php if(!empty($err_msg['email'])) echo $err_msg['email']; ?>
      </div>
      <input type="submit" name="submit" value="送信" class="simple-btn">
    </form>
   </div>
  </div>
    <!--フッター-->
    <?php
    require('footer.php');
     ?>
</body>
</html>