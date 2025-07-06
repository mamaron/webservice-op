<?php
//共通関数
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「設定画面設定');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();


?>
<?php
$siteTitle = '設定';
require('head.php');
?>
  <!--ヘッダー-->
  <?php
  require('header.php');
  ?>
  <div class="site-width">
   <div class="one-columns-site">
    <h1 class="title">設定</h1>
    <div class="area-msg" style="margin-bottom:10px;text-align:center;">
      <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
    </div>
    <form class="form-change-box" method="post">
      <div class="change-box">
        <div class="email-change">
          <h2>アドレスを変更する</h2>
          <label class="">
            現在のアドレス<span class="lab-asterisk">*</span>
            <input type="text" name="email" value="" placeholder="inu@gmail.com" class="js-valid-text js-valid-email">
          </label>
          <div class="area-msg">
         
          </div>
          <label class="">
            新しいアドレス<span class="lab-asterisk">*</span>
            <input type="text" name="email_new" value="" placeholder="inu@gmail.com" class="js-valid-text js-valid-email js-valid-email-new">
          </label>
          <div class="area-msg">
         
          </div>
          <label class="">
            新しいアドレス(再入力)<span class="lab-asterisk">*</span>
            <input type="text" name="email_new_re" value="" placeholder="inu@gmail.com" class="js-valid-text js-valid-email js-valid-email-new-re">
          </label>
          <div class="area-msg" style="padding-left:0px;text-align:center;">

          </div>
        </div>
        <div class="pass-change">
          <h2>パスワードを変更する</h2>
          <label class="">
            現在のパスワード<span class="lab-asterisk">*</span>
            <input type="password" name="pass" value="" placeholder="inuinuinu" class="js-valid-text js-valid-pass1">
          </label>
          <div class="area-msg">
       
          </div>
          <label class="">
            新しいパスワード<span class="lab-asterisk">*</span>
            <input type="password" name="pass_new" value="" placeholder="inuinuinu" class="js-valid-text js-valid-pass-new">
          </label>
          <div class="area-msg">
      
          </div>
          <label class="">
            新しいパスワード(再入力)<span class="lab-asterisk">*</span>
            <input type="password" name="pass_new_re" value="" placeholder="inuinuinu" class="js-valid-text js-valid-pass-new-re">
          </label>
          <div class="area-msg" style="padding:0px;text-align:center;">
    
          </div>
        </div>
      </div>
      <input type="submit" name="submit" value="変更する" class="change-box-simple-btn">
    </form>
   </div>
  </div>
    <!--フッター-->
    <?php
    require('footer.php'); ?>
</body>
</html>