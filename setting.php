<?php
//共通関数
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「設定画面設定');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

/*
処理フロー　emailとパスワードの二つ
0,idを元にemailを取得。必要箇所に表示。

POST
バリデーション 
新しいemail,新しいpassどちらかが入力されたら
未入力、現在のアドレスと新しいアドレスが違う場合、現在のパスと新しいパスが違う場合で処理を分ける。
DB接続
update

0-1:valueのgetFormData作る。
*/

//ユーザー情報取得
$dbFormData = getUser($_SESSION['user_id']);
debug('取得したユーザー情報:'.print_r($dbFormData,true));

//自動認証
require('auth.php');

//==========================================
//画面実装処理開始
//==========================================

//POST送信
if(!empty($_POST['email_new']) || !empty($_POST['pass'])){
  debug('POST送信があります。');
  debug('POST送信の中身:'.print_r($_POST,true));

  //新しいEmailが入力されていたら処理実行
  if(!empty($_POST['email_new'])){
    debug('新しいemailの入力があります。');
    //ありえないけども新しいパスワードの未入力&reの未入力
    validRequired($_POST['email_new'],'email_new');
    validRequired($_POST['email_new_re'],'email_new_re');
    if(empty($err_msg)){
      //変数に詰める
      $email_new = $_POST['email_new'];
      $email_new_re = $_POST['email_new_re'];

      //現在のemailと入力した新しいemailが同じ場合
      if($dbFormData['email'] === $email_new){
        $err_msg['email_new'] = '現在のアドレスと新しいアドレスが同じです。';
      }
      if(empty($err_msg)){
        //emailの重複
        validMailDup($email_new,'email_new');
        if(empty($err_msg)){
          //email形式
          validEmail($email_new,'email_new');
          validEmail($email_new_re,'email_new_re');
          if(empty($err_msg)){
            //最大文字数
            validMaxLen($email_new,'email_new');
            validMaxLen($email_new_re,'email_new_re');
            if(empty($err_msg)){
              //再入力とマッチしてるかどうか
              validMatch($email_new,$email_new_re,'email_new');
            }
          }
        }
       
      }
    }
  }
  //パスワードが入力されていたら
  if(!empty($_POST['pass'])){
    debug('パスワードの入力があります。');
    //３つの未入力確認
    validRequired($_POST['pass'],'pass');
    validRequired($_POST['pass_new'],'pass_new');
    validRequired($_POST['pass_new_re'],'pass_new_re');

    if(empty($err_msg)){
      debug('未入力バリデーションOK');
      //パスワード:現在のパスワードと新しいパスワードの一致,新しいパスワードと新しいパスワード()の不一致
      //変数にまとめる
      $pass = $_POST['pass'];
      $pass_new = $_POST['pass_new'];
      $pass_new_re = $_POST['pass_new_re'];
      //現在のパスワードとDBのパスワードの不一致
      validCheckPass($pass,$dbFormData['pass'],'pass');
      
      if(empty($err_msg)){
        debug('現在のパスワードが登録したパスワードと一致したことが確定.');
        //最小文字数
        validMinLen($pass_new,'pass_new');
        validMinLen($pass_new_re,'pass_new_re');
        if(empty($err_msg)){
          //最大文字数
          validMaxLen($pass_new,'pass_new');
          validMaxLen($pass_new_re,'pass_new_re');
          if(empty($err_msg)){
            //半角英数字
            validPass($pass_new,'pass_new');
            validPass($pass_new_re,'pass_new_re');
            if(empty($err_msg)){
              //現在のパスワードと新しいパスワードの一致
              if($pass === $pass_new){
                $err_msg['pass_new'] = '現在のpassと新しいpassが同じです。';
              }
              validMatch($pass_new,$pass_new_re,'pass_new_re');
            }
          }
        }

      }
    }
  }

  if(empty($err_msg)){
    debug('バリデーションOK。');
    //例外処理
    try{
      //DB接続
      $dbh = dbConnect();
      if(!empty($email_new) && !empty($pass_new)){
        debug('emailとpassのsql作成');
        $sql1 = 'UPDATE users SET email = :email, pass = :pass WHERE id = :id';
        $data1 = array(
            ':email' => $email_new,
            ':pass' => password_hash($pass_new,PASSWORD_DEFAULT),
            ':id' => $_SESSION['user_id']
       );
       //クエリ実行
       $stmt1 = queryPost($dbh,$sql1,$data1);
      }
      if(!empty($email_new)){
       $sql2 = 'UPDATE users SET email = :email WHERE id = :id';
       $data2 = array(
            ':email' => $email_new,
            ':id' => $_SESSION['user_id']
       );
       //クエリ実行
       $stmt2 = queryPost($dbh,$sql2,$data2);
      }elseif(!empty($pass_new)){
        $sql3 = 'UPDATE users SET pass = :pass WHERE id = :id';
        $data3 = array(
            ':pass' => password_hash($pass_new,PASSWORD_DEFAULT),
            ':id' => $_SESSION['user_id']
       );
       //クエリ実行
       $stmt3 = queryPost($dbh,$sql3,$data3);
      }
      if(!empty($email_new) && !empty($pass_new)){
        if($stmt1->rowCount() > 0){
          debug('emailとパスワード両方のアップデート完了.');
          debug('マイページへ遷移します.');
          $_SESSION['msg_success'] = SUC02;
          header("Location:mypage.php");
          exit;  
        } 
      }elseif(!empty($email_new)){
        if($stmt2->rowCount() > 0){
          debug('emailのアップデート完了.');
          debug('マイページへ遷移します.');
          $_SESSION['msg_success'] = SUC03;
          header("Location:mypage.php");
          exit;
        }
      }elseif(!empty($pass_new)){
        if($stmt3->rowCount() > 0){
          debug('passのアップデート完了.');
          debug('マイページへ遷移します.');
          $_SESSION['msg_success'] = SUC04;
          header("Location:mypage.php");
          exit;
        }
      }
      }catch(Exception $e){
        debug('エラー発生：'.$e->getMessage());
        $err_msg['common'] = MSG07;
      }
    
    }
  }



?>
<?php
$siteTitle = '設定';
require('head.php');
?>
  <!--ヘッダー-->
  <?php
  require('header.php');
  ?>
  <div class="site-width">
   <div class="one-columns-site">
    <h1 class="title">設定</h1>
    <div class="area-msg" style="margin-bottom:10px;text-align:center;">
      <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
    </div>
    <form class="form-change-box" method="post">
      <div class="change-box">
        <div class="email-change">
          <h2>アドレスを変更する</h2>
          <label class="">
            現在のアドレス<span class="lab-asterisk">*</span>
            <input type="text" name="email" value="<?php echo getFormData('email'); ?>" placeholder="inu@gmail.com" class="js-valid-text js-valid-email">
          </label>
          <div class="area-msg">
            <?php if(!empty($err_msg['email'])) echo $err_msg['email']; ?>
          </div>
          <label class="">
            新しいアドレス<span class="lab-asterisk">*</span>
            <input type="text" name="email_new" value="<?php echo getFormData('email_new'); ?>" placeholder="inuinu@gmail.com" class="js-valid-text js-valid-email js-valid-email-new">
          </label>
          <div class="area-msg" style="padding-left:0px;text-align:center;">
            <?php if(!empty($err_msg['email_new'])) echo $err_msg['email_new']; ?>
          </div>
          <label class="">
            新しいアドレス(再入力)<span class="lab-asterisk">*</span>
            <input type="text" name="email_new_re" value="<?php echo getFormData('email_new_re'); ?>" placeholder="inuinu@gmail.com" class="js-valid-text js-valid-email js-valid-email-new-re">
          </label>
          <div class="area-msg" style="padding-left:0px;text-align:center;">
            <?php if(!empty($err_msg['email_new_re'])) echo $err_msg['email_new_re']; ?>
          </div>
        </div>
        <div class="pass-change">
          <h2>パスワードを変更する</h2>
          <label class="">
            現在のパスワード<span class="lab-asterisk">*</span>
            <input type="password" name="pass" value="" placeholder="inuinuinu" class="js-valid-text js-valid-pass1">
          </label>
          <div class="area-msg" style="padding-left:0px;text-align:center;">
          <?php if(!empty($err_msg['pass'])) echo $err_msg['pass']; ?>
          </div>
          <label class="">
            新しいパスワード<span class="lab-asterisk">*</span>
            <input type="password" name="pass_new" value="" placeholder="inuinuinuinu" class="js-valid-text js-valid-pass-new">
          </label>
          <div class="area-msg" style="padding-left:0px;text-align:center;">
            <?php if(!empty($err_msg['pass_new'])) echo $err_msg['pass_new']; ?>
          </div>
          <label class="">
            新しいパスワード(再入力)<span class="lab-asterisk">*</span>
            <input type="password" name="pass_new_re" value="" placeholder="inuinuinuinu" class="js-valid-text js-valid-pass-new-re">
          </label>
          <div class="area-msg" style="padding-left:0px;text-align:center;">
            <?php if(!empty($err_msg['pass_new_re'])) echo $err_msg['pass_new_re']; ?>
          </div>
        </div>
      </div>
      <input type="submit" name="submit" value="変更する" class="change-box-simple-btn">
    </form>
   </div>
  </div>
    <!--フッター-->
    <?php
    require('footer.php'); ?>
</body>
</html>