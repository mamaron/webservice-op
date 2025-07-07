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
debug('DB愛犬情報:'.print_r($dbFormData,true));
$edit_flg = (!empty($dbFormData)) ? 'true' : 'false';
debug('愛犬フラグ:'.$edit_flg);
debug('POST情報の中身:'.print_r($_POST,true));
debug('FILES情報の中身:'.print_r($_FILES,true));
//POST情報
if($_SERVER['REQUEST_METHOD'] === 'POST'){//ageで０があるため
  debug('POST送信があります。');
  //未入力バリデーション
  validRequired($_POST['dog_name'],'dog_name');
  validRequired($_POST['sex'],'sex');
  validRequired($_POST['age'],'age');
  validRequired($_POST['dog_breed'],'dog_breed');
  if(!empty($_FILES['pic']['name'])){
    validRequired($_FILES['pic'],'pic');
  }elseif(!empty($_POST['prev_img'])){
    //何もしない
  }else{
    $err_msg['pic'] = '画像をアップロードしてください。';
  }
  if(empty($err_msg)){
    //変数に格納、画像はここでアップロード
    $dog_name = $_POST['dog_name'];
    $sex = $_POST['sex'];
    $age = (int)$_POST['age'];
    $dog_breed = $_POST['dog_breed'];
    //画像アップロードした上で、変数に詰め込む
    $pic = (!empty($_FILES['pic']['name'])) ? uploadImg($_FILES['pic'],'pic') : $_POST['prev_img'];

    //DBデータがない場合
    if(empty($dbFormData)){
      //初回登録
      validMaxLen($dog_name,'dog_name',50);
      validHalf($age,'age');
      validMaxLen($dog_breed,'dog_breed',50);
    }else{
        //DB情報と違う場合はバリデーション
      if(isset($dbFormData['dog_name']) && $dog_name !== $dbFormData['dog_name']){
        validMaxLen($dog_name,'dog_name',50);
      }
      if(isset($dbFormData['age']) && $age !== $dbFormData['age']){
        validHalf($age,'age');
      }
      if(isset($dbFormData['dog_breed']) && $dog_breed !== $dbFormData['dog_breed']){
        validMaxLen($dog_breed,'dog_breed',50);
      }
    }
    if(empty($err_msg)){
      debug('バリデーションOKです。');
      debug('DBと全く同じかチェック');
      $current = [
        'dog_name' => $dbFormData['dog_name'],
        'sex' => $dbFormData['sex'],
        'age' => $dbFormData['age'],
        'dog_breed' => $dbFormData['dog_breed'],
        'pic' => $dbFormData['pic']
      ];
      $new = [
        'dog_name' => $dog_name,
        'sex' => $sex,
        'age' => $age,
        'dog_breed' => $dog_breed,
        'pic' => $pic
      ];
      if($current === $new){
        $err_msg['common'] = 'DB情報と同じです。';
      }
      if(empty($err_msg)){
        //場合分けしてDB接続
        //例外処理
        try{
          //DB接続
          $dbh = dbConnect();
          //DBデータがある場合
          if(!empty($dbFormData)){
            debug('DB情報をアップデートします。');
            //SQL
            $sql1 = 'UPDATE pet_dog SET dog_name = :dog_name, sex = :sex, age = :age, dog_breed = :dog_breed, pic = :pic WHERE user_id = :u_id';
            $data1 = array(
                  ':dog_name' => $dog_name,
                  ':sex' => $sex,
                  ':age' => $age,
                  ':dog_breed' => $dog_breed,
                  ':pic' => $pic,
                  'u_id' => $_SESSION['user_id']
            );
            $stmt1 = queryPost($dbh,$sql1,$data1);
            $result1 = $stmt1->rowCount();
            debug('結果:'.$result1);
            if($result1 > 0){
              debug('DBアップデート成功');
              $_SESSION['msg_success'] = SUC06;
              header("Location:mypage.php");
              exit;
            }else{
              debug('DBアップデート失敗');
              $err_msg['common'] = MSG07;
            }
            //DBデータがない。
          }else{
            debug('DB情報を代入します。');
            //SQL
            $sql2 = 'INSERT INTO pet_dog(user_id, dog_name, sex, age, dog_breed, pic, create_date) VALUES(:u_id, :dog_name, :sex, :age, :dog_breed, :pic, :c_date)';
            $data2 = array(
                  ':u_id' => $_SESSION['user_id'],
                  ':dog_name' => $dog_name,
                  ':sex' => $sex,
                  ':age' => $age,
                  ':dog_breed' => $dog_breed,
                  ':pic' => $pic,
                  ':c_date' => date('Y-m-d H:i:s')
            );
            $stmt2 = queryPost($dbh,$sql2,$data2);
            $result2 = $stmt2->rowCount();
            if($result2 > 0){
              debug('DB登録成功');
              $_SESSION['msg_success'] = SUC05;
              header("Location:mypage.php");
              exit;
            }else{
              debug('DB登録失敗');
              $err_msg['common'] = MSG07;
            }
          }  
        }catch(Exception $e){
          debug('エラー発生:'.$e->getMessage());
          $err_msg['common'] = MSG07;
        } 
      }
    }

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
          <input type="radio" name="sex" value="male" <?php if(isset($_POST['sex']) && $_POST['sex'] === 'male' || (!empty($dbFormData['sex']) && $dbFormData['sex'] === 'male')) echo 'checked'; ?>>オス
        </label>
        <label for="">
          <input type="radio" name="sex" value="female" <?php if(isset($_POST['sex']) && $_POST['sex'] === 'female' || (!empty($dbFormData['sex']) && $dbFormData['sex'] === 'female')) echo 'checked'; ?>>メス
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
      <!--
          1,1回アップロードしたら本保存する
          2,画像のファイル名を<input type="hidden" name="prev_img"に保存する
          3,次回POST時に$_FILES['pic']が空でもprev_imgを使う。
        -->
      <?php
        $imgPath = '';
        if(!empty($pic)){
          $imgPath = sanitize($pic);
        }elseif(!empty($_POST['prev_img'])){
          $imgPath = sanitize($_POST['prev_img']);
        }elseif(!empty($dbFormData['pic'])){
          $imgPath = sanitize($dbFormData['pic']);
        }
       ?>
      写真<span class="lab-asterisk">*</span>
      <label class="area-drop js-area-drop">
        ドラッグ&ドロップ
        <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
        <img src="uploads/<?php echo $imgPath; ?>" alt="" class="preview-img js-img">
        <input type="hidden" name="prev_img" value="<?php echo $imgPath; ?>">
        <input type="file" name="pic" class="dog-file js-file" value="">
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