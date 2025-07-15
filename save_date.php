<?php 
//共通関数
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「Ajax処理用。DB保存、処理');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

/*
selectして、情報があったら、delete,なかったら、insertだな。
*/
if(isset($_POST['date']) && isset($_POST['id'])){
  debug('dataのdateとidがあります。');
  $date = $_POST['date'];
  $id = $_POST['id'];
  //例外処理
  try{
    //DB接続
    $dbh = dbConnect();
    //まずSQL
    $sql = 'SELECT count(*) FROM availability WHERE host_id = :u_id AND available_date = :date';
    $data = array(
            ':u_id' => $id,
            ':date' => $date
    );
    //実行
    $stmt = queryPost($dbh,$sql,$data);
    //結果
    $result = $stmt->fetchColumn();
    if($result > 0){
      debug('データが既にあります。');
      debug('情報を削除します。');
      //SQL
      $sql = 'DELETE FROM availability WHERE host_id = :u_id AND available_date = :date';
      //実行
      $stmt = queryPost($dbh,$sql,$data);
      $result = $stmt->rowCount();
      if($result > 0){
        debug('削除完了.');
        echo json_encode(['status' => 'delete']);
      }else{
        debug('削除失敗');
        echo json_encode(['status' => 'error']);
      }
    }else{
      debug('データがないです。');
      debug('DBに保存します。');
      //SQL
      $sql = 'INSERT INTO availability(host_id, available_date, created_date) VALUES(:u_id, :date, :c_date)';
      $data = array(
              ':u_id' => $id,
              ':date' => $date,
              'c_date' => date('Y-m-d H:i:s')
      );
      $stmt = queryPost($dbh,$sql,$data);
      $result = $stmt->rowCount();
      if($result > 0){
        debug('登録成功。');
        echo json_encode(['status' => 'success']);
      }else{
        debug('登録失敗');
        echo json_encode(['status' => 'error']);
      }
    }

  }catch(Exception $e){
    debug('エラー発生:'.$e->getMessage());
    echo json_encode(['status' => 'error']);
  }
}


?>