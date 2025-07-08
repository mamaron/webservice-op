<?php
//共通関数
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「ホスト情報登録・編集');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//自動認証
require('auth.php');

/*
・DBからホスト情報取得＋展開
・POST送信
・未入力チェック
・変数格納(画像はアップロード)

・DB情報と違う場合はバリデーション
・DBと完全に一致した場合はエラー
・DB接続,新規の場合insert,編集の場合はupdate
・問題なければメッセージセットしてマイページ遷移

======== 
<カレンダーAjax機能>
予想処理フロー
・どうにかして現在日時及びそれに伴うカレンダーを作成(先月、来月分も自動)
・押す =>js側でdata属性受け取る=>PHPに投げる。=>DB保存してJSに返す
=>cssの処理する。
＊＊DB保存時に、そのデータがあればdelete、なければinsertの処理をするようにする。
それに合わせてcssの処理もかえる。
=======================================================================
*/
//ホスト情報取得
$dbFormData = getHostData($_SESSION['user_id']);
debug('ホスト情報:'.print_r($dbFormData,true));


//======================================== 
//画面表示処理開始
//========================================

if($_SERVER['REQUEST_METHOD'] === 'POST'){
  debug('POST送信があります。');
  debug('POST情報:'.print_r($_POST,true));
  debug('FILES情報;'.print_r($_FILES,true));
  //未入力チェック
  validRequired($_POST['hostname'],'hostname');
  validRequired($_POST['zip'],'zip');
  validRequired($_POST['prefecture'],'prefecture');
  validRequired($_POST['municipalities'],'municipalities');
  validRequired($_POST['street'],'street');
  validRequired($_POST['building'],'building');
  validRequired($_POST['station'],'station');
  validRequired($_POST['able_dog'],'able_dog');
  validRequired($_POST['price1'],'price1');
  validRequired($_POST['price2'],'price2');
  validRequired($_FILES['pic1'],'pic1');
  validRequired($_FILES['pic2'],'pic2');
  validRequired($_POST['comment'],'comment');

  if(empty($err_msg)){
    debug('未入力バリデOK。変数詰め込み');
    $hostName = $_POST['hostname'];
    $zip = (int)$_POST['zip'];
    $prefecture = $_POST['prefecture'];
    $municipalities = $_POST['municipalities'];
    $street = $_POST['street'];
    $able_dog = $_POST['able_dog'];
    $price1 = (int)$_POST['price1'];
    $price2 = (int)$_POST['price2'];
    $pic1 = (!empty($_FILES['pic1'])) ?  uploadImg($_FILES['pic1'],'pic1') : $_POST['img_prev1'];
    $pic2 = (!empty($_FILES['pic2'])) ?  uploadImg($_FILES['pic2'],'pic1') : $_POST['img_prev2'];
    $comment = $_POST['comment'];

    if(empty($err_msg)){
      debug('変数格納&&画像アップロード成功');
      debug('DB情報と異なる場合はバリデーション。');
      //ホスト名前
      if($dbFormData['hostname'] !== $hostName){
        validMaxLen($hostName,'hostName',50);
      }
      //郵便番号
      if($dbFormData['zip'] !== $zip){
        $math = 7;
        validHalf($zip,'zip');
        if(!empty($err_msg)){
          validMatch($zip,$math,'zip');
        }
      }
      //都道府県
      if($dbFormData['prefecture'] !== $prefecture){
        validMaxLen($prefecture,'prefecture',50);
      }
      //市区町村
      if($dbFormData['municipalities'] !== $municipalities){
        validMaxLen($municipalities,'municipalities',50);
      }
      //番地
      if($dbFormData['street'] !== $street){
        validMaxLen($street,'street',50);
      }
      //対応可能サイズ
      if($dbFormData['able_dog'] !== $able_dog){
        validMisMatch($able_dog,'able_dog');
        if(empty($err_msg)){
          //セレクトボックスのvalueの値
          validSelect($able_dog,'able_dog');
        }
      }
    }

  }


}


?>
<?php
$siteTitle = 'ホスト登録・編集';
require('head.php');
?>
  <!--ヘッダー-->
  <?php
  require('header.php');
  ?>
  <div class="site-width">
   <div class="one-columns-site">
    <h1 class="title"> <?php echo (!empty($dbFormData)) ? 'ホスト編集する' : 'ホスト登録する'; ?> </h1>
    <div class="area-msg">
      <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
    </div>
    <form class="simple-form" method="post" enctype="multipart/form-data" style="width:50%;">

      <label class="label-input">
        名前<span class="lab-asterisk">*</span>
        <input type="text" name="hostname" class="js-valid-text js-valid-name" value="<?php echo getFormData('hostname'); ?>" placeholder="山田 太郎">
      </label>
      <div class="area-msg">
        <?php if(!empty($err_msg['hostname'])) echo $err_msg['hostname']; ?>
      </div>

      <label class="label-input">
        郵便番号<span class="lab-asterisk">*</span> ※ハイフン無しで入力ください
        <input type="text" name="zip" class="js-valid-text js-valid-zip" value="<?php echo getFormData('zip'); ?>" placeholder="1110000" style="width:50%;margin:0px;">
      </label>
      <div class="area-msg">
        <?php if(!empty($err_msg['zip'])) echo $err_msg['zip']; ?>
      </div>

      <label class="label-input">
        都道府県<span class="lab-asterisk">*</span> 
        <input type="text" name="prefecture" class="js-valid-text js-valid-pref" value="<?php echo getFormData('prefecture'); ?>" placeholder="東京都" style="width:50%;margin:0px;">
      </label>
      <div class="area-msg">
        <?php if(!empty($err_msg['prefecture'])) echo $err_msg['prefecture']; ?>
      </div>

      <label class="label-input">
        市区町村<span class="lab-asterisk">*</span> 
        <input type="text" name="municipalities" class="js-valid-text js-valid-pref" value="<?php echo getFormData('municipalities'); ?>" placeholder="港区虎ノ門" style="width:50%;margin:0px;">
      </label>
      <div class="area-msg">
        <?php if(!empty($err_msg['municipalities'])) echo $err_msg['municipalities']; ?>
      </div>

      <label class="label-input">
        番地<span class="lab-asterisk">*</span> 
        <input type="text" name="street" class="js-valid-text js-valid-pref" value="<?php echo getFormData('street'); ?>" placeholder="1-1-1" style="width:50%;margin:0px;">
      </label>
      <div class="area-msg">
        <?php if(!empty($err_msg['street'])) echo $err_msg['street']; ?>
      </div>

      <label class="label-input">
        建物名<span class="lab-asterisk">*</span> 
        <input type="text" name="building" class="js-valid-text js-valid-pref" value="<?php echo getFormData('building'); ?>" placeholder="虎ノ門ビル100F" style="width:50%;margin:0px;">
      </label>
      <div class="area-msg">
        <?php if(!empty($err_msg['building'])) echo $err_msg['building']; ?>
      </div>

      <label class="label-input">
        最寄り駅<span class="lab-asterisk">*</span> 
        <input type="text" name="station" class="js-valid-text js-valid-pref" value="<?php echo getFormData('station'); ?>" placeholder="虎ノ門駅" style="width:50%;margin:0px;">
      </label>
      <div class="area-msg">
        <?php if(!empty($err_msg['station'])) echo $err_msg['station']; ?>
      </div>

      <label class="label-input">
        対応可能なワンちゃんの大きさ<span class="lab-asterisk">*</span><br>
        <select name="able_dog" id="" class="able-dog">
          <option value="0">選択してください</option>
          <option value="1" <?php if(!empty($able_dog) && $able_dog === 1) echo 'selected'; ?>>小型犬のみ</option>
          <option value="2" <?php if(!empty($able_dog) && $able_dog === 2) echo 'selected'; ?>>小型犬〜中型犬</option>
          <option value="3" <?php if(!empty($able_dog) && $able_dog === 3) echo 'selected'; ?>>全てのサイズ対応可</option>
        </select>
      </label>
      <div class="area-msg">
        <?php if(!empty($err_msg['able_dog'])) echo $err_msg['able_dog']; ?>
      </div>

      <label class="label-input">
        プラン料金<span class="lab-asterisk">*</span><br>
        <span>おさんぽ</span>
        <input type="text" name="price1" class="js-valid-text js-valid-price" value="<?php echo getFormData('price1'); ?>" placeholder="1000">
        <span>お泊まり</span>
        <input type="text" name="price2" class="js-valid-text js-valid-price" value="<?php echo getFormData('price2'); ?>" placeholder="5000">
      </label>
      <?php if(!empty($err_msg['price1']) && !empty($err_msg['price2'])){ ?>
        <div class="area-msg">
          <?php echo $err_msg['price1'];  ?>
        </div>
      <?php }elseif(!empty($err_msg['price1'])){ ?>
        <div class="area-msg">
          <?php echo $err_msg['price1'];  ?>
        </div>
      <?php }elseif(!empty($err_msg['price2'])){?>
        <div class="area-msg">
          <?php echo $err_msg['price2'];  ?>
        </div>
      <?php } ?>
      <div class="host-areadrop">
        <?php
        $img_path1 = '';
        $img_path2 = '';
          if(!empty($_POST['pic1'])){//POST
            $img_path1 = $pic1;
          }elseif(!empty($_POST['img_prev1'])){//img_prev1に仮情報があったら。
            $img_path1 = $_POST['img_prev1'];
          }elseif(!empty($dbFormData['pic1'])){//DBにデータがあったら アップロードした画像
            $img_path1 = $dbFormData['pic1'];
          }
          if(!empty($_POST['pic2'])){//POSTがあったら
            $img_path2 = $pic2;
          }elseif(!empty($_POST['img_prev2'])){//img_prev1に仮情報があったら。
            $img_path2 = $_POST['img_prev2'];
          }elseif(!empty($dbFormData['pic2'])){//DBにデータがあったら
            $img_path2 = $dbFormData['pic2'];
          }
        ?>

        <div class="host-pic-left">
          顔写真<span class="lab-asterisk">*</span>
          <label class="area-drop js-area-drop">
            ドラッグ&ドロップ
            <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
            <img src="uploads/<?php echo sanitize($img_path1); ?>" alt="" class="preview-img js-img">
            <input type="hidden" name="img_prev1" value="<?php echo $img_path1;?>">
            <input type="file" name="pic1" class="dog-file js-file-left" value="">
          </label>
        </div>
        <div class="host-pic-right">
          部屋写真<span class="lab-asterisk">*</span>
          <label class="area-drop js-area-drop">
            ドラッグ&ドロップ
            <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
            <img src="uploads/<?php echo sanitize($img_path2); ?>" alt="" class="preview-img js-img">
            <input type="hidden" name="img_prev2" value="<?php echo $img_path2; ?>">
            <input type="file" name="pic2" class="dog-file js-file-right" value="">
          </label>
        </div>
      </div>
      <?php if(!empty($err_msg['pic1'])): ?>
      <div class="area-msg">
        <?php if(!empty($err_msg['pic1'])) echo $err_msg['pic1']; ?>
      </div>
      <?php endif; ?>
      <?php if(!empty($err_msg['pic2'])): ?>
      <div class="area-msg">
        <?php if(!empty($err_msg['pic2'])) echo $err_msg['pic2']; ?>
      </div>
      <?php endif; ?>

      <label class="host-input" for="">
        自己紹介<span class="lab-asterisk">*</span>
        <textarea name="comment" class="comment js-valid-text js-valid-comment" id="">
          <?php echo getFormData('comment'); ?>
        </textarea>
        <div class="count"><span class="js-count-text">0</span>/500</div>
      </label>
      <div class="area-msg">
        <?php if(!empty($err_msg['comment'])) echo $err_msg['common']; ?>
      </div>

      <div class="calendar-head">
        <a href="" class="left">5月分</a>
        <span class="center">6月分カレンダー</span>
        <a href="" class="right">7月分</a>
      </div>
      <label for="" style="color:#E76F51;">
        ※対応可能日を選択してください
      </label>
      <div class="calendar">
        <div class="calendar-header">日</div>
        <div class="calendar-header">月</div>
        <div class="calendar-header">火</div>
        <div class="calendar-header">水</div>
        <div class="calendar-header">木</div>
        <div class="calendar-header">金</div>
        <div class="calendar-header">土</div>

        <div class="calendar-cell" data-date="2025-06-01">1</div>
        <div class="calendar-cell" data-date="2025-06-02">2</div>
        <div class="calendar-cell">3</div>
        <div class="calendar-cell">4</div>
        <div class="calendar-cell">5</div>
        <div class="calendar-cell">6</div>
        <div class="calendar-cell">7</div>
        <div class="calendar-cell">8</div>
        <div class="calendar-cell">9</div>
        <div class="calendar-cell">10</div>
        <div class="calendar-cell">11</div>
        <div class="calendar-cell">12</div>
        <div class="calendar-cell">13</div>
        <div class="calendar-cell">14</div>
        <div class="calendar-cell">15</div>
        <div class="calendar-cell">16</div>
        <div class="calendar-cell">17</div>
        <div class="calendar-cell">18</div>
        <div class="calendar-cell">19</div>
        <div class="calendar-cell">20</div>
        <div class="calendar-cell">21</div>
        <div class="calendar-cell">22</div>
        <div class="calendar-cell">23</div>
        <div class="calendar-cell">24</div>
        <div class="calendar-cell">25</div>
        <div class="calendar-cell">26</div>
        <div class="calendar-cell">27</div>
        <div class="calendar-cell">28</div>
        <div class="calendar-cell">29</div>
        <div class="calendar-cell">30</div>
      </div>
      

      <input type="submit" name="submit" value=" <?php echo (!empty($dbFormData)) ? '編集!' : '登録！'; ?>" class="simple-btn">
    </form>
   </div>
  </div>
    <!--フッター-->
    <?php
    require('footer.php');
    ?>