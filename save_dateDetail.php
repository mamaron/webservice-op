<?php 
//共通関数
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「Ajax処理DB追加');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

/*
selectして、情報があったら、delete,なかったら、insertだな。
selectしてあったらupdate、なくてもupdate

DB
host_id = id (ホスト登録時のセッション変数のユーザーID)
user_id = h_id (ホスト詳細で選択する側のユーザー：セッション変数のユーザーID)

*/
if(isset($_POST['date']) && isset($_POST['id']) && isset($_POST['h_id'])){
  debug('dataのdateとid,h_idがあります。');
  $date = $_POST['date'];
  $id = $_POST['id'];//飼い主側ID
  $h_id = $_POST['h_id'];//ホスト側ID
  //例外処理
  try{
    //DB接続
    $dbh = dbConnect();
    //SQL
    $sql = 'SELECT user_id FROM availability WHERE host_id = :h_id AND available_date = :date';
    $data = array(
            ':h_id' => $h_id,
            ':date' => $date
    );
    //実行
    $stmt = queryPost($dbh,$sql,$data);
    //結果
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    debug('resultの中身：'.print_r($result,true));
    if(!empty($result) && !empty($result['user_id'])){
      debug('データが既にあります。');
      debug('$h_idを削除します。');
      //SQL
      $sql = 'UPDATE availability SET user_id = NULL WHERE host_id = :h_id';
      //data
      $data = array(
        ':h_id' => $h_id
      );
      //実行
      $stmt = queryPost($dbh,$sql,$data);
      $result = $stmt->rowCount();
      if($result > 0){
        debug('削除完了.');
        echo json_encode(['status' => 'delete']);
      }else{
        debug('削除失敗');
        echo json_encode(['status' => 'err']);//通信エラー
      }
    }else{
      debug('データがないです。');
      debug('$h_idをDBに保存します。');
      //SQL
      $sql = 'UPDATE availability SET user_id = :u_id WHERE host_id = :h_id AND available_date = :date';
      $data = array(
              'h_id' => $h_id,
              ':u_id' => $id,
              ':date' => $date
      );
      $stmt = queryPost($dbh,$sql,$data);
      $result = $stmt->rowCount();
      if($result > 0){
        debug('登録成功。');
        echo json_encode(['status' => 'success']);
      }else{
        debug('登録失敗');
        echo json_encode(['status' => 'notAvaible']);
      }
    }

  }catch(Exception $e){
    debug('エラー発生:'.$e->getMessage());
    echo json_encode(['status' => 'error']);
  }
}else{
  debug('飼い主側のuser_idがありません。');
  echo json_encode(['status' => 'err']);//else
}


?>