<?php
//===================================================
//エラー表示
//===================================================
date_default_timezone_set('Asia/Tokyo');//UTCからtokyo時間に
ini_set('display_errors',0);//画面にエラーを出さない
ini_set('log_errors',1);//エラーをログに出力
ini_set('error_log','php.log');//ログの出力先
ini_set('error_reporting', E_ALL);//エラーレベル

//===================================================
//セッション
//===================================================
//セッションの保存先変更
session_save_path('/Applications/MAMP/tmp/php_sessions');
//セッションの保持期限を1時間に伸ばす(その後一定の確率で削除される)
ini_set('session.gc_maxlifetime',3600);
//クッキーの有効期限も変える
session_set_cookie_params([
  'lifetime' => 3600,
  'path' => '/',
  'secure' => false,
  'httponly' => true,
  'samesite' => 'Lax'
]);
session_start();
//セッションID都度再発行する
session_regenerate_id();


//===================================================
//デバッグ
//===================================================
function debug($str){
  //フラグ 本番ではfalseにする
  $debug_flg = true;
  if($debug_flg){
    return error_log($str);
  }
}

//===================================================
//デバッグログ初期値
//===================================================
function debugLogStart(){
  debug('セッションIDの中身'.session_id());
  if(!empty($_SESSION['login_date'])){
    debug('現在時刻：'.time());
    debug('セッション変数の中身:'.print_r($_SESSION,true));
    
  }else{
    debug('ログイン時に得るセッション変数は何も持っていません.');
  }
  
}

//===================================================
//定数
//===================================================
define('MSG01','入力必須になります。');
define('MSG02','emailの形式でお願いします。');
define('MSG03','最大文字数を超えています');
define('MSG04','半角英数字のみ使用できます');
define('MSG05','6文字以上でお願いします');
define('MSG06','パスワードとパスワード(再入力)が一致しません');
define('MSG07','不具合が発生いたしました。しばらく経ってから再度お試しください。');
define('MSG08','入力のアドレスは使用できません');
define('MSG09','アドレスもしくはパスワードが一致しません');
define('MSG10','登録されていないアドレスになります。');
define('MSG11','メール送信に失敗しました。しばらく経ってから再度お試しください。');
define('MSG12','入力された認証キーが一致していません。');
define('SUC01','仮のパスワードをメール致しました。ご確認をお願い致します！');
//===================================================
//変数
//===================================================
//エラー用の変数
$err_msg = [];

//===================================================
//関数
//===================================================

//===================================================
//バリデーション関数
//===================================================
//未入力
function validRequired($str,$key){
  global $err_msg;
  if(empty($str)){
    $err_msg[$key] = MSG01;
  }
}
//email形式チェック
function validEmail($email,$key){
  global $err_msg;
  if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
    $err_msg[$key] = MSG02;
  }
}
//emailの重複
function validMailDup($email,$key){
  global $err_msg;
  //例外処理
  try{
    debug('$emailの中身:'.$email);
    //DB接続
    $dbh = dbConnect();
    //sql 論理削除していない有効中のemailアドレスがDBにあるかどうか。
    $sql = 'SELECT count(*) FROM users WHERE email = :email AND delete_flg = 0';
    $data = array(':email' => $email);
    //クエリ実
    $stmt = queryPost($dbh,$sql,$data);
    $result = $stmt->fetchColumn();
    if($result === 0){
      debug('登録されていないアドレスです.');
    }else{
      debug('既に登録されているアドレスになります。');
      $err_msg[$key] = MSG08;
    }
  }catch(Exception $e){
    debug('エラー発生：'.$e->getMessage());
    $err_msg['common'] = MSG07;
  }

}
//半角英数字
function validPass($str,$key){
  global $err_msg;
  if(!preg_match("/^[a-zA-Z0-9]+$/",$str)){
    $err_msg[$key] = MSG04;
  }
}
//最大文字数
function validMaxLen($str,$key,$max = 255){
  global $err_msg;
  if(mb_strlen($str) > $max){
    $err_msg[$key] = MSG03;
  }
}
//最小文字数
function validMinLen($str,$key,$min = 6){
  global $err_msg;
  if(mb_strlen($str) < $min){
    $err_msg[$key] = MSG05;
  }
}
//マッチ確認
function validMatch($str1,$str2,$key){
  global $err_msg;
  if($str1 !== $str2){
    $err_msg[$key] = MSG06;
  }
}

//===================================================
//DB関係
//===================================================
//DB接続
function dbConnect(){
  $dsn = 'mysql:dbname=inu_station_db;host=localhost;charset=utf8mb4';
  $user = 'root';
  $password = 'root';
  $options = array(
            //エラーモード：例外を投げる
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            //デフォルトのフェッチモードを連想配列形式にする
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            //バッファクエリを使用してDBの負担を減らす
            PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true
  );

  return $dbh = new PDO($dsn,$user,$password,$options);
}
//クエリ実行
function queryPost($dbh,$sql,$data){
  $stmt = $dbh->prepare($sql);
  //あくまでもsqlが成功しただけ。何行更新やら、何行返ってきたかは不明。0でもtrueを返す。
  $stmt->execute($data);
  if($stmt){
    debug('クエリ成功');
    return $stmt;
  }
  else{
    debug('クエリ失敗');
    return false;
  }
}
//===================================================
//メール作成
//===================================================
function sendMail($to,$subject,$comment,$from){
  //文字化け、言語設定
  mb_language("Japanese");
  mb_internal_encoding("UTF-8");

  //header
  $header = "From: {$from}\r\n";
  $header .= "Reply-To: {$from}\r\n";
  $header .= "Content-Type: text/plain; charset=UTF-8\r\n";

  //メール送信
   $result = mb_send_mail($to,$subject,$comment,$header);

   if($result){
    debug('メール送信成功');
    return true;
   }else{
    debug('メール送信失敗');
    $err_msg['common'] = MSG11;
    return false;
   }

}
//===================================================
//その他
//===================================================
//認証キー用のトークン作成
function makeToken(){
  $val = '';
  $str = 'abcdefghigklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
  for($i=0;$i < 10;$i++){
    $val .= $str[mt_rand(0,mb_strlen($str) - 1)];
  }
  return $val;
}

//メッセージ表示
function getSessionFlash($key){
  if(!empty($key)){
    //＄msg変数にメッセージ内容を格納し、セッション自体は削除する
    $msg = $key;
    session_unset();
    return $msg;
  }

}




?>