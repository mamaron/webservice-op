<?php
//共通関数
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「退会機能');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

/*
退会機能 簡易
クリック
->
DB接続
=>
論理削除
-> 
topページに遷移

*/
//=====================================
//画面処理開始
//=====================================

//ログイン認証
require('auth.php');

if(!empty($_POST)){
  debug('POST送信があります。');
  //例外処理
  try{
    debug('db接続します。複数のテーブルのidを削除します。');
    //db接続
    $dbh = dbConnect();
    debug('トランザクションスタート');
    $dbh->beginTransaction();
    //usersテーブル
    $sql1 = 'UPDATE users SET delete_flg = 1 WHERE id = :id';
    $data1 = array( ':id' => $_SESSION['user_id']);
    $stmt1 = queryPost($dbh,$sql1,$data1);
    $result1 =  $stmt1->rowCount();
    //pet_dogテーブル
    $sql2 = 'UPDATE pet_dog SET delete_flg = 1 WHERE user_id = :u_id';
    $data2 = array(':u_id' => $_SESSION['user_id']);
    $stmt2 = queryPost($dbh,$sql2,$data2);
    //likeテーブル
    $sql3 = 'DELETE FROM `like` WHERE user_id = :u_id';
    $data3 = array(':u_id' => $_SESSION['user_id']);
    $stmt3 = queryPost($dbh,$sql3,$data3);
    //hostテーブル
    $sql4 = 'UPDATE host SET delete_flg = 1 WHERE user_id = :u_id';
    $data4 = array(':u_id' => $_SESSION['user_id']);
    $stmt4 = queryPost($dbh,$sql4,$data4);
    //boardテーブル
    $sql5 = 'UPDATE board SET delete_flg = 1 WHERE `owner` = :u_id OR host = :u_id';
    $data5 = array(':u_id' => $_SESSION['user_id']);
    $stmt5 = queryPost($dbh,$sql5,$data5);
    //availabilityテーブル
    $sql6 = 'UPDATE `availability` SET delete_flg = 1 WHERE host_id = :u_id';
    $data6 = array(':u_id' => $_SESSION['user_id']);
    $stmt6 = queryPost($dbh,$sql6,$data6);

    $dbh->commit();

    if($result1){
      debug('ユーザー情報の論理削除完了.TOPページに遷移します。');
      header("Location:index.php");
      exit;
    }else{
      debug('users情報削除失敗。');
      $err_msg['common'] = MSG07;
    }

  }catch(Exception $e){
    $dbh->rollback();
    debug('エラー発生:'.$e->getMessage());
  }
}

debug('画面実装処理終了<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>

<?php 
$siteTitle = '退会';
require('head.php'); 
?>
  <!--ヘッダー-->
  <?php 
  require('header.php');
  ?>

  <div class="site-width">
   <div class="one-columns-site">
    <h1 class="title">退会する</h1>
    <div class="area-msg">
      <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
    </div>
    <p class="passremind_msg">
      寂しいわん
    </p>
    <form class="simple-form" method="post">
      <input type="submit" name="submit"  value="退会" class="withdraw-btn">
    </form>
   </div>
  </div>
    <!--フッター-->
    <?php 
    require('footer.php');
    ?>
</body>
</html>