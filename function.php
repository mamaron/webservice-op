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
define('MSG06','入力された値と再入力された値が一致しません。');
define('MSG07','不具合が発生いたしました。しばらく経ってから再度お試しください。');
define('MSG08','入力のアドレスは使用できません');
define('MSG09','アドレスもしくはパスワードが一致しません');
define('MSG10','登録されていないアドレスになります。');
define('MSG11','メール送信に失敗しました。しばらく経ってから再度お試しください。');
define('MSG12','入力された認証キーが一致していません。');
define('MSG13','現在のアドレスと新しいアドレスが同じです。');
define('MSG14','入力した現在のパスワードと登録したパスワードが一致しません。');
define('MSG15','半角数字のみでお願いします。');
define('MSG16','正しい選択肢を選択してください。');
define('MSG17','7文字で入力ください。');
define('SUC01','仮のパスワードをメール致しました。ご確認をお願い致します！');
define('SUC02','emailとパスワードを変更しました！');
define('SUC03','emailを変更しました！');
define('SUC04','パスワードを変更しました！');
define('SUC05','愛犬情報登録成功しました。！');
define('SUC06','愛犬情報編集成功しました。！');
define('SUC07','ホスト情報編集しました。！');
define('SUC08','ホスト情報登録成功しました。！');
define('SUC09','事前面談画面でホストさんとやりとりしましょう！');
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
  if(!isset($str) || $str === ''){
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
//半角数字
function validHalf($str,$key){
  global $err_msg;
  if(!preg_match("/^[0-9]+$/",$str)){
    $err_msg[$key] = MSG15;
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
//文字列が一致してるか
function validMathMatch($str1,$key){
  global $err_msg;
  if(!preg_match('/^\d{7}$/',$str1)){
    $err_msg[$key] = MSG17;
  }
}
//マッチ確認
function validMatch($str1,$str2,$key){
  global $err_msg;
  if($str1 !== $str2){
    $err_msg[$key] = MSG06;
  }
}
//現在パスワードとDBパスワードが一致しているか確認
function validCheckPass($pass,$db_pass,$key){
  global $err_msg;
  if(password_verify($pass,$db_pass) === 'false'){
    debug('入力した現パスワードとDBのパスワードが一致しません');
    $err_msg[$key] = MSG14;
  }
}
//セレクトボックス(ホスト用)
function validSelect($str,$key,$val){
  global $err_msg;
  if(in_array($str,$val,true)){
    $err_msg[$key] = MSG16;
  }
}
//マッチしてしまった場合のチェック
function validMisMatch($str,$key,$match = 0){
  global $err_msg;
  if($str === $match){
    $err_msg[$key] = MSG16;
  }
}
//GETパラメータの改ざんチェック
//========================================
function validGetParam($param){
  if(!is_string($param) || !ctype_digit($param)){
    http_response_code(400);
    debug('GETパラメータ改ざんされてます。処理を止めます。トップページに遷移します。');
    header("Location:index.php");
    exit;
  }
}
//GETパラメータ付与 ?p=1&p_id=2
//$del_key:取り除きたいgetパラメータのキー
function appendGetParam($del_key = array()){
  if(!empty($_GET)){
    $str = '?';

    foreach($_GET as $key => $val){
      if(!in_array($key,$del_key,true)){
        $str .= $key .'='.$val.'&';
      }
    }
    $str = mb_substr($str,0,-1,'UTF-8');
    return $str;
  }
}

//===================================================
//ユーザーデータ取得
//===================================================
function getUser($u_id){
  debug('ユーザーのemail,passwordを取得します。');
  //例外処理
  try{
    //db接続
    $dbh = dbConnect();
    //sql
    $sql = 'SELECT email, pass FROM users WHERE id = :u_id AND delete_flg = 0';
    //data
    $data = array(
            ':u_id' => $u_id
    );
    //クエリ実行
    $stmt = queryPost($dbh,$sql,$data);
    //値を取得
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if($result){
      return $result;
    }
  }catch(Exception $e){
    debug('エラー発生:'.$e->getMessage());
    $err_msg['common'] = MSG07;
  }
}
//===================================================
//愛犬データ取得
//===================================================
function getDogData($u_id){
  global $err_msg;
  debug('愛犬情報取得します。');
  //例外処理
  try{
    //db接続
    $dbh = dbConnect();
    //SQL
    $sql = 'SELECT id, dog_name, sex, age, dog_breed, pic FROM pet_dog WHERE user_id = :u_id AND delete_flg = 0';
    //data
    $data = array(':u_id' => $u_id);
    //クエリ実行
    $stmt = queryPost($dbh,$sql,$data);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if($result){
      return $result;
    }else{
      return false;
    }
  }catch(Exception $e){
    debug('エラー発生：'.$e->getMessage());
    return false;
  }
}
//===================================================
//ホストデータ取得
//===================================================

function getHostData($u_id){
  debug('ホストデータ取得します。');
  //例外処理
  try{
    //db接続
    $dbh = dbConnect();
    //SQL
    $sql = 'SELECT hostname, zip, prefecture, municipalities, street, building, station, able_dog, 
    price1, price2, pic1, pic2, comment FROM host WHERE user_id = :u_id AND delete_flg = 0';
    $data = array(':u_id' => $u_id);
    //クエリ実行
    $stmt = queryPost($dbh,$sql,$data);

    //結果
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if($result){
      return $result;
    }else{
      return false;
    }
  }catch(Exception $e){
    debug('エラー発生：'.$e->getMessage());
    return false;
  }
}

//===================================================
//トップページ用のhostデータ取得
//===================================================
//全ホストデータとデータ数取得の両立は無理だ。
function getTopHostData($limit,$offset,$pref,$genre,$price){
 debug('TOPページ用のホスト全情報取得します。');
 //例外処理
 try{
  //db
  $dbh = dbConnect();
  //wehre句自動生成
  $where = [];
  $param = [];
  //都道府県
  if(isset($pref) && $pref !== ''){
    $where[] = 'prefectures_id = :pref';
    $param[':pref'] = $pref;
  }

  debug('$whereの値'.print_r($where,true));
  debug('$whereのデータ型'.gettype($where));

  //SQL 全データ数取得デフォルト
  $sql = 'SELECT count(*) FROM host WHERE delete_flg = 0';
  //追加用
  if(!empty($where)){
    $sql .= ' AND ' . implode(' AND ',$where);
  }
  $stmt = $dbh->prepare($sql);
  if(!empty($where)){
    $stmt->execute($param);
  }else{
    $stmt->execute();
  }
  $total = $stmt->fetchColumn();
  $total_page = ceil($total/$limit);
  debug('中身:'.$total);

  //データ表示用 
  $sql1 = 'SELECT id, user_id, hostname, prefecture, price1, price2, pic1 FROM host WHERE delete_flg = 0';
  //追加用
  if(!empty($where)){
    $sql1 .= ' AND ' . implode(' AND ',$where);
  }
  //追加用ソート検索
  //ジャンル(1=お散歩、2=お泊まり)が1以上かつpriceが１以上
  if(!empty($genre) && !empty($price)){
    if($genre == 1){//お散歩price1
      
      if($price == 1){//金額が高い順
        $sql1 .= ' ORDER BY price1 DESC';
      }elseif($price == 2){
        $sql1 .= ' ORDER BY price1 ASC';
      }
    }elseif($genre == 2){//お泊まりprice2
      
      if($price == 1){//金額が高い順
        $sql1 .= ' ORDER BY price2 DESC';
      }elseif($price == 2){
        $sql1 .= ' ORDER BY price2 ASC';
      }
    }
  }
  $sql1 .= ' LIMIT :limit OFFSET :offset';
  //$paramに:limit,:offsetを入れる
  $param[':limit'] = $limit;
  $param[':offset'] = $offset;
  debug('＄prefの中身:'.$pref);
  debug('SQL中身:'.$sql1);
  debug('$paramの中身:'.print_r($param,true));
  $stmt1 = $dbh->prepare($sql1);
  //LIMIT 何個取るか、OFFSET 何番目から
  foreach($param as $key => $val){
    $paramType = (is_int($val)) ? PDO::PARAM_INT : PDO::PARAM_STR;
    $stmt1->bindValue($key,$val,$paramType);
  }
  $stmt1->execute();
  $rst = $stmt1->fetchAll();
  if(count($rst) > 0){
    debug('全データ取得成功');
    return [
      'total' => $total,
      'total_page' => $total_page,
      'rst' => $rst
    ];
  }
}catch(Exception $e){
  debug('エラー発生:'.$e->getMessage());
  return false;
}
}

//===================================================
//都道府県のカテゴリー取得
//===================================================
function getPrefData(){
  debug('カテゴリー情報取得します。');
  //hostとprefsマスターテーブルを inner join
  //取得したいのは名前。
  //例外処理
  try{
    //db
    $dbh = dbConnect();
    //SQL
    $sql = 'SELECT DISTINCT p.id,p.name FROM prefectures p INNER JOIN host h WHERE h.prefectures_id = p.id';
    //data
    $data = array();
    $stmt = queryPost($dbh,$sql,$data);
    $result = $stmt->fetchAll();
    $total = count($result);
    if(empty($result)){
      return false;
    }else{
      return [
        'result' => $result,
        'total' => $total
      ];
    }
  }catch(Exception $e){
    debug('エラー発生：'.$e->getMessage());
    return false;
  }
}

//===================================================
//ホスト可能日
//===================================================
function getHostAvailable($u_id){
  debug('ホスト可能日取得');
  //例外処理
  try{
    $dbh = dbConnect();
    $sql = 'SELECT available_date FROM availability WHERE host_id = :u_id';
    $data = array(':u_id' => $u_id);
    $stmt = queryPost($dbh,$sql,$data);
    $result = $stmt->fetchAll();
    if($result > 0){
      debug('可能日データ取得OK');
      return $result;
    }else{
      debug('可能日データ取得がありません');
      return '';
    }
  }catch(Exception $e){
    debug('エラー発生：'.$e->getMessage());
  }
}
//===================================================
//可能日選択
//===================================================
function getDaySelected($user,$host){
  debug('希望日を飼い主が選択したか確認します。');
  //例外処理
  try{
    //DB接続
    $dbh = dbConnect();
    //SQL
    $sql = 'SELECT count(*) FROM availability WHERE host_id = :h_id AND user_id = :u_id';
    //data
    $data = array(
      ':h_id' => $host,
      'u_id' => $user
    );
    //クエリ実行
    $stmt = queryPost($dbh,$sql,$data);
    $result = $stmt->fetchColumn();
    if($result > 0){
      debug('希望日が選択されました。');
      return true;
    }else{
      debug('希望日が選択されませんでした。');
      return false;
    }
  }catch(Exception $e){
    debug('エラー発生:'.$e->getMessage());
    return false;
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
  //executeが成功したかどうか。
  $stmt->execute($data);
  
  if($stmt){
    debug('SQL実行自体は成功');
    return $stmt;
  }
  else{
    debug('sql実行自体は失敗');
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
//画像アップロード
//===================================================
function uploadImg($img,$key){
  global $err_msg;
  /*
  従来のエラー
  MIMEタイプ確認
  サイズ確認
  ファイル名変更
  アップロード先選定
  権限譲渡
  ムーブアップロードファイル
  */
  //例外処理
  try{
    //従来のエラー確認から
    if(!empty($img['error']) && $img['error'] !== 0){
      switch($img['error']){
        case UPLOAD_ERR_INI_SIZE :
          throw new RuntimeException('ファイルサイズが大きすぎます。');
          break;
        case UPLOAD_ERR_FORM_SIZE :
          throw new RuntimeException('ファイルサイズがフォーム指定サイズを超えてます。');
          break;
        case UPLOAD_ERR_NO_FILE :
          throw new RuntimeException('ファイルが選択されていません。');
          break;
        case UPLOAD_ERR_NO_TMP_DIR :
          throw new RuntimeException('一時保存先がありません。');
          break;
          default:
          throw new RuntimeException('その他の理由でファイルアップロード失敗しました。');
          break;
      }
    }  
      //MIMEタイプ確認
      $finfo = new finfo(FILEINFO_MIME_TYPE);
      $mime = $finfo->file($img['tmp_name']);
      $allowed_type = ['image/jpeg','image/png','image/gif'];
      if(!in_array($mime,$allowed_type)){
        throw new RuntimeException('画像ファイルではありません。');
      }
      //ファイルサイズ
      $max_size = 2 * 1024 * 1024; //2MB
      if($img['size'] > $max_size){
        throw new RuntimeException('ファイルサイズが大きすぎます。');
      }
      //ファイル名変更
      $filename = $img['name'];
      //拡張子取得
      $ext = pathinfo($filename,PATHINFO_EXTENSION);
      //日時
      $datetime = time();
      $randomStr = bin2hex(random_bytes(4));
      //新しいファイル名
      $newFileName = $datetime. '_' .$randomStr. '.'.$ext;
      //ファイル移動先
      $upload_Path = 'uploads/'.$newFileName;
      //アップロード
      move_uploaded_file($img['tmp_name'],$upload_Path);
      //権限
      chmod($upload_Path,0644);
      return $newFileName;
    
  }catch(RuntimeException $e){
    debug('エラー発生：'.$e->getMessage());
    $err_msg['common'] = $e->getMessage();
  }
}
  
//===================================================
//サニタイズ
//===================================================
function sanitize($str){
  return htmlspecialchars($str,ENT_QUOTES);
}
//===================================================
//入力保持
//===================================================
function getFormData($str){
  global $err_msg;
  global $dbFormData;
  //POST送信がある場合
  if(isset($_POST[$str])){
    //エラーがある場合
    if(isset($err_msg[$str])){
      return sanitize($_POST[$str]);
    }else{
        return sanitize($_POST[$str]);
    }
  }else{
    if(isset($dbFormData[$str])){
      return sanitize($dbFormData[$str]);
    }else{
      return '';
    }
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
  if(!empty($_SESSION[$key])){
    //＄msg変数にメッセージ内容を格納し、セッション自体は削除する
    $msg = $_SESSION[$key];
    unset($_SESSION[$key]);
    return $msg;
  }

}




?>