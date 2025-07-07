<?php
//共通関数
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「ホスト情報登録・編集');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//自動認証
require('auth.php');



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
    <h1 class="title">ホスト登録する</h1>
    <div class="area-msg">
      <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
    </div>
    <form class="simple-form" method="post" style="width:50%;">
      <label class="label-input">
        名前<span class="lab-asterisk">*</span>
        <input type="text" name="host_name" class="js-valid-text js-valid-name" value="" placeholder="山田 太郎">
      </label>
      <div class="area-msg">
        <?php if(!empty($err_msg['host_name'])) echo $err_msg['host_name']; ?>
      </div>
      <label class="label-input">
        郵便番号<span class="lab-asterisk">*</span> ※ハイフン無しで入力ください
        <input type="text" name="zip" class="js-valid-text js-valid-zip" value="" placeholder="1110000" style="width:50%;margin:0px;">
      </label>
      <div class="area-msg">
        <?php if(!empty($err_msg['zip'])) echo $err_msg['zip']; ?>
      </div>
      <label class="label-input">
        都道府県<span class="lab-asterisk">*</span> 
        <input type="text" name="prefecture" class="js-valid-text js-valid-pref" value="" placeholder="東京都" style="width:50%;margin:0px;">
      </label>
      <div class="area-msg">
        <?php if(!empty($err_msg['prefecture'])) echo $err_msg['prefecture']; ?>
      </div>
      <label class="label-input">
        市区町村<span class="lab-asterisk">*</span> 
        <input type="text" name="municipalities" class="js-valid-text js-valid-pref" value="" placeholder="港区虎ノ門" style="width:50%;margin:0px;">
      </label>
      <div class="area-msg">
        <?php if(!empty($err_msg['municipalities'])) echo $err_msg['municipalities']; ?>
      </div>
      <label class="label-input">
        番地<span class="lab-asterisk">*</span> 
        <input type="text" name="street" class="js-valid-text js-valid-pref" value="" placeholder="1-1-1" style="width:50%;margin:0px;">
      </label>
      <div class="area-msg">
        <?php if(!empty($err_msg['street'])) echo $err_msg['street']; ?>
      </div>
      <label class="label-input">
        建物名<span class="lab-asterisk">*</span> 
        <input type="text" name="building" class="js-valid-text js-valid-pref" value="" placeholder="虎ノ門ビル100F" style="width:50%;margin:0px;">
      </label>
      <div class="area-msg">
        <?php if(!empty($err_msg['building'])) echo $err_msg['building']; ?>
      </div>
      <label class="label-input">
        最寄り駅<span class="lab-asterisk">*</span> 
        <input type="text" name="station" class="js-valid-text js-valid-pref" value="" placeholder="虎ノ門駅" style="width:50%;margin:0px;">
      </label>
      <div class="area-msg">
        <?php if(!empty($err_msg['station'])) echo $err_msg['station']; ?>
      </div>
      <label class="label-input">
        対応可能なワンちゃんの大きさ<span class="lab-asterisk">*</span><br>
        <select name="able_dog" id="" class="able-dog">
          <option value="0">選択してください</option>
          <option value="1">小型犬のみ</option>
          <option value="2">小型犬〜中型犬</option>
          <option value="3">全てのサイズ対応可</option>
        </select>
      </label>
      <div class="area-msg">
        <?php if(!empty($err_msg['able_dog'])) echo $err_msg['able_dog']; ?>
      </div>
      <label class="label-input">
        プラン料金<span class="lab-asterisk">*</span><br>
        <span>おさんぽ</span>
        <input type="text" name="price1" class="js-valid-text js-valid-price" value="" placeholder="1000">
        <span>お泊まり</span>
        <input type="text" name="price2" class="js-valid-text js-valid-price" value="" placeholder="5000">
      </label>
      <?php if(!empty($err_msg['price1'])): ?>
        <div class="area-msg">
          <?php echo $err_msg['price1'];  ?>
        </div>
      <?php endif; ?>
      <?php if(!empty($err_msg['price2'])): ?>
        <div class="area-msg">
          <?php echo $err_msg['price2'];  ?>
        </div>
      <?php endif; ?>

      <div class="host-areadrop">

        <div class="host-pic-left">
          顔写真<span class="lab-asterisk">*</span>
          <label class="area-drop js-area-drop">
            ドラッグ&ドロップ
            <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
            <input type="file" name="pic1" class="dog-file js-file-left" value="">
            <img src="" alt="" class="preview-img js-img">
          </label>
        </div>

        <div class="host-pic-right">
          部屋写真<span class="lab-asterisk">*</span>
          <label class="area-drop js-area-drop">
            ドラッグ&ドロップ
            <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
            <input type="file" name="pic2" class="dog-file js-file-right" value="">
            <img src="" alt="" class="preview-img js-img">
          </label>
        </div>

      </div>
      <div class="area-msg">
      </div>
      <label class="host-input" for="">
        自己紹介<span class="lab-asterisk">*</span>
        <textarea name="comment" class="comment js-valid-text js-valid-comment" id=""></textarea>
        <div class="count"><span class="js-count-text">0</span>/500</div>
      </label>
      <div class="area-msg">
  
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
      

      <input type="submit" name="submit" value="登録！" class="simple-btn">
    </form>
   </div>
  </div>
    <!--フッター-->
    <?php
    require('footer.php');
    ?>