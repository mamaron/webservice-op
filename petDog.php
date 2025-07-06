<?php
//共通関数
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「愛犬登録・編集機能');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

/*
<愛犬登録・編集>
・愛犬情報取得展開
=>情報ある場合、ない場合でフラグ立てる。
・POST送信
・未入力バリデ
・変数にまとめる(画像はここで、アップロード)
・DBと比べて違う場合はその他バリデーション
・DB接続
・フラグ判断で、INSERT or UPDATE
・mypage遷移
*/

//自動認証
require('auth.php');

//======================================== 
//画面表示処理開始
//========================================

//愛犬情報取得
$dbFormData = getDogData($_SESSION['user_id']);
debug('愛犬情報:'.print_r($dbFormData,true));
$edit_flg = ($dbFormData) ? 'true' : 'false';
debug('愛犬フラグ:'.$edit_flg);
debug('POST情報:'.print_r($_POST,true));
debug('GET情報:'.print_r($_FILES,true));
//POST情報
if(!empty($_POST) && !empty($_FILES)){
  debug('POST送信及びFILE送信があります。');
  //未入力バリデーション
  validRequired($_POST['dog_name'],'dog_name');
  validRequired($_POST['sex'],'sex');
  validRequired($_POST['age'],'age');
  validRequired($_POST['dog_breed'],'dog_breed');
  validRequired($_FILES['pic'],'pic');
  if(empty($err_msg)){

  }
}

?>
<?php
$siteTitle = '愛犬登録';
require('head.php'); 
?>
  <!--ヘッダー-->
  <?php
  require('header.php');
  ?>
  <div class="site-width">
   <div class="one-columns-site">
    <h1 class="title"><?php echo ($edit_flg === 'true') ? '愛犬情報を変更' : '愛犬登録する'; ?> </h1>
    <div class="area-msg">
      <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
    </div>
    <form class="simple-form" method="post" style="width:50%;" enctype="multipart/form-data">
      <label class="">
        なまえ<span class="lab-asterisk">*</span>
        <input type="text" name="dog_name" class="js-valid-name js-valid-text" value="<?php echo getFormData('dog_name'); ?>" placeholder="ゴン太">
      </label>
      <div class="area-msg">
        <?php if(!empty($err_msg['dog_name'])) echo $err_msg['dog_name']; ?>
      </div>
      <label class="">
        性別<span class="lab-asterisk">*</span><br>
        <label for="">
          <input type="radio" name="sex" value="male" <?php if($_POST['sex'] === 'male' || (!empty($dbFormData['sex']) && $dbFormData['sex'] === 'male')) echo 'checked'; ?>>オス
        </label>
        <label for="">
          <input type="radio" name="sex" value="female" <?php if($_POST['sex'] === 'female' || (!empty($dbFormData['sex']) && $dbFormData['sex'] === 'female')) echo 'checked'; ?>>メス
        </label>
      </label>
      <div class="area-msg">
        <?php if(!empty($err_msg['sex'])) echo $err_msg['sex']; ?>
      </div>
      <label class="">
        年齢<span class="lab-asterisk">*</span>
        <input type="text" name="age" class="js-valid-text js-valid-age" value="<?php echo getFormData('age'); ?>" placeholder="3">
      </label>
      <div class="area-msg">
        <?php if(!empty($err_msg['age'])) echo $err_msg['age']; ?>
      </div>
      <label class="">
        犬種<span class="lab-asterisk">*</span>
        <input type="text" name="dog_breed" class="js-valid-dog-breed js-valid-text" value="<?php echo getFormData('dog_breed'); ?>" placeholder="チワワ">
      </label>
      <div class="area-msg">
        <?php if(!empty($err_msg['dog_breed'])) echo $err_msg['dog_breed']; ?>
      </div>
      写真<span class="lab-asterisk">*</span>
      <label class="area-drop js-area-drop">
        ドラッグ&ドロップ
        <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
        <input type="file" name="pic" class="dog-file js-file" value="">
        <img src="<?php echo (!empty($dbFormData['pic']['name'])) ? $dbFormData['pic']['name'] : $_FILES['pic']['name']; ?>" alt="" class="preview-img js-img">
      </label>
      <div class="area-msg">
        <?php if(!empty($err_msg['pic'])) echo $err_msg['pic']; ?>
      </div>
      <input type="submit" name="submit" value="<?php echo ($edit_flg === 'true')?'編集':'登録';  ?>  " class="simple-btn">
    </form>
   </div>
  </div>
    <!--フッター-->
    <?php
    require('footer.php'); 
    ?>