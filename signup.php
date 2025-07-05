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


//===================================================
//エラー表示
ini_set('display_errors',0);//画面にエラーを出さない
ini_set('log_errors',1);//エラーをログに出力
ini_set('error_log','php.log');//ログの出力先
ini_set('error_reporting', E_ALL);//エラーレベル


//デバッグ出す
function debug($str){
  //フラグ 本番ではfalseにする
  $debug_flg = true;
  if($debug_flg){
    return error_log($str);
  }
}

//メッセージ定数
define('MSG01','入力必須になります。');
define('MSG02','emailの形式でお願いします。');
define('MSG03','最大文字数を超えています');
define('MSG04','半角英数字のみ使用できます');
define('MSG05','6文字以上でお願いします');
define('MSG06','パスワードとパスワード(再入力)が一致しません');

//エラー用の変数
$err_flg = array();

//未入力
function validRequired($str,$key){
  global $err_flg;
  if(empty($str)){
    $err_flg[$key] = MSG01;
  }
}
//email形式チェック
function validEmail($email,$key){
  global $err_flg;
  if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
    $err_flg[$key] = MSG02;
  }
}
//半角英数字＋記号
function validPass($str,$key){
  global $err_flg;
  if(!preg_match("/^[a-zA-Z0-9]+$/",$str)){
    $err_flg[$key] = MSG04;
  }
}
//最大文字数
function validMaxLen($str,$key,$max = 255){
  global $err_flg;
  if(mb_strlen($str) > $max){
    $err_flg[$key] = MSG03;
  }
}
//最小文字数
function validMinLen($str,$key,$min = 6){
  global $err_flg;
  if(mb_strlen($str) < $min){
    $err_flg[$key] = MSG05;
  }
}
//マッチ確認
function validMatch($str1,$str2,$key){
  global $err_flg;
  if($str1 !== $str2){
    $err_flg[$key] = MSG06;
  }
}

//===================================================
debug('「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「ユーザー機能画面処理開始');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「');

//POST送信
if(!empty($_POST)){
  debug('POST送信があります.');
  //未入力
  validRequired($_POST['email'],'email');
  validRequired($_POST['pass'],'pass');
  validRequired($_POST['pass_re'],'pass_re');

  if(empty($err_flg)){
    //変数に格納
    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $pass_re = $_POST['pass_re'];
    debug('passの中身1:'.$pass);

    //emailの形式、最大文字数、
    validEmail($email,'email');
    if(empty($err_flg)){
      validMaxLen($email,'email');
    }
    //passの最小文字数、半角英数字＋きごう、最大文字数
    validPass($pass,'pass');
    if(empty($err_flg)){
      validMaxLen($pass,'pass');
    }
    if(empty($err_flg)){
      validMinLen($pass,'pass');
    }
    if(empty($err_flg)){
      validMatch($pass,$pass_re,'pass');
    }
    if(empty($err_flg)){
      debug('バリデーションOK');
      //例外処理
      try{
        //DB接続
      }
    }



  }

}

?>

<?php
$siteTitle = 'ユーザー登録';
require('head.php');
?>
<body>
  <!--ヘッダー-->
  <header class="header">
    <div class="container">
      <h1><a href="index.html">いぬの駅</a></h1>
      <nav>
        <ul>
          <li><a href="signup.html">ユーザー登録</a></li>
          <li><a href="login.html">ログイン</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <div class="site-width">
   <div class="one-columns-site">
    <h1 class="title">ユーザー登録</h1>
    <div class="area-msg">

    </div>
    <form class="simple-form" method="post">
      <label class="">
        メールアドレス<span class="lab-asterisk">*</span>
        <input type="text" name="email" class="js-valid-text js-valid-email" value="" placeholder="inu@gmail.com">
      </label>
      <div class="area-msg">
      <?php if(!empty($err_flg['email'])) echo $err_flg['email']; ?>
      </div>
      <label class="">
        パスワード<span class="lab-asterisk">* 半角英数字のみ</span>
        <input type="password" name="pass" class="js-valid-text js-valid-pass" value="" placeholder="inuinuinu">
      </label>
      <div class="area-msg">
        <?php if(!empty($err_flg['pass'])) echo $err_flg['pass']; ?>
      </div>
      <label class="">
        パスワード(再入力)<span class="lab-asterisk">*</span>
        <input type="password" name="pass_re" class="js-valid-text js-valid-pass-re" value="" placeholder="inuinuinu">
      </label>
      <div class="area-msg">
      <?php if(!empty($err_flg['pass_re'])) echo $err_flg['pass_re']; ?>
      </div>
      <input type="submit" name="submit" value="送信" class="simple-btn">
    </form>
    <div class="divider">
      <span>または</span>
    </div>
    <p class="divider-point"><a href="login.html">登録済みの方はこちら</a></p>
   </div>
  </div>
  <!--フッター-->
  <?php
  require('footer.php');
  ?>
  
</body>
</html>