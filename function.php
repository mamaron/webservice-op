<?php
//===================================================
//エラー表示
//===================================================
ini_set('display_errors',0);//画面にエラーを出さない
ini_set('log_errors',1);//エラーをログに出力
ini_set('error_log','php.log');//ログの出力先
ini_set('error_reporting', E_ALL);//エラーレベル

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
//定数
//===================================================
define('MSG01','入力必須になります。');
define('MSG02','emailの形式でお願いします。');
define('MSG03','最大文字数を超えています');
define('MSG04','半角英数字のみ使用できます');
define('MSG05','6文字以上でお願いします');
define('MSG06','パスワードとパスワード(再入力)が一致しません');
define('MSG07','不具合が発生いたしました。しばらく経ってから再度お試しください。');
define('MSG08','既に登録済みのアドレスになります。');

//===================================================
//変数
//===================================================
//エラー用の変数
$err_flg = array();

//===================================================
//関数
//===================================================
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
//emailの重複
function validMailDup($email,$key){
  global $err_flg;
  //例外処理
  try{
    //DB接続
    $dbh = dbConnect();
    //sql 論理削除していない有効中のemailアドレスがDBにあるかどうか。
    $sql = 'SELECT count(*) FROM users WHERE email = :email AND delete_flg = 0';
    $data = array(':email' => $email);
    //クエリ実
    $stmt = queryPost($dbh,$sql,$data);
    if(!$stmt){
      debug('登録されていないアドレスです.');
    }else{
      debug('既に登録されているアドレスになります。');
      $err_flg[$key] = MSG08;
    }
  }catch(Exception $e){
    debug('エラー発生：'.$e->getMessage());
    $err_flg['common'] = MSG07;
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
  //php.iniでも変えたが、タイムゾーンをアジア/東京に帰る。
  date_default_timezone_set('Asia/Tokyo');
  return $dbh = new PDO($dsn,$user,$password,$options);
}
//クエリ実行
function queryPost($dbh,$sql,$data){
  $stmt = $dbh->prepare($sql);
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



?>