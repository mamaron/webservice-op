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
    $sql = 'UPDATE users SET delete_flg = 1 WHERE id = :id';
    $data = array( ':id' => $_SESSION['user_id']);
    $stmt = queryPost($dbh,$sql,$data);

    ここから

    $dbh->commit();
  }catch(Exception $e){
    $dbh->rollback();
    debug('エラー発生:'.$e->getMessage());
  }
}

debug('画面実装処理終了<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>

<?php 
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
    </div>
    <p class="passremind_msg">
      寂しいわん
    </p>
    <form class="simple-form" method="post">
      <input type="submit" name="submit" value="退会" class="withdraw-btn">
    </form>
   </div>
  </div>
    <!--フッター-->
    <?php 
    require('footer.php');
    ?>
</body>
</html>