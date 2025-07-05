<?php

//共通関数
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「マイページ機能開始');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//自動認証
require('auth.php');
?>
<?php
$siteTitle = 'マイページ';
require('head.php');
?>
  <!--ヘッダー-->
  <?php
  require('header.php'); 
  ?>
  <!--メッセージ表示-->
  <div class="msg-flash js-msg-flash">
    登録完了しました！
  </div>
  <!--メイン-->
  <div class="site-width">
   <h1 class="title">マイページ</h1>
    <div class="area-msg">

    </div>
    <div class="two-columns-container">
    <!--メインバー-->
     <div class="two-columns-site" id="ad-re-two-columns-site">
      <!--愛犬-->
      <div class="adinterview-wrap">
        <p>登録した愛犬情報</p>
        <div class="adinDog">
          <div class="adinDog-Img">
            <img src="img/download-dog1.jpg" alt="">
          </div>
          <ul class="adinDog-items">
            <li><span class="label">なまえ：</span>ゴン太</li>
            <li><span class="label">年齢：</span>３さい</li>
            <li><span class="label">性別：</span>オス</li>
            <li><span class="label">犬種：</span>ゴールデンレトリバー</li>
          </ul>
        </div>
      </div>
      <!--事前連絡やりとりリスト-->
      <div class="mypage-adInterview">
        <p>事前面談連絡掲示板やりとり</p>
        <table>
          <thead>
            <tr><th>日時</th><th>やりとり</th></tr>
          </thead>
          <tbody>
            <tr><td>2025-06-21</td><td><a href="">初めまして！プロフィールをみてご連絡させていただきました</a></td></tr>
            <tr><td>2025-06-25</td><td><a href="">それでは7日よろしくお願いします！</a></td></tr>
            <tr><td>2025-06-28</td><td><a href="">初めまして！プロフィールをみてご連絡させていただきました</a></td></tr>
          </tbody>
        </table>
      </div>
      <!--予約一覧-->
      <div class="mypage-reservCon">
        <p>予約一覧</p>
        <div class="my-reservCon-wrap">
          <a href="" class="my-reservCon-item">
            <div class="my-reservCon-img">
              <img src="img/download-2.jpg" alt="">
            </div>
            <p>預ける日:<span class="label">7月12日</span></p>
          </a>
          <a href="" class="my-reservCon-item">
            <div class="my-reservCon-img">
              <img src="img/download-dog2.jpg" alt="">
            </div>
            <p>預かる日:<span class="label">7月1日</span></p>
          </a>
          <a href="" class="my-reservCon-item">
            <div class="my-reservCon-img">
              <img src="img/download-dog3.jpg" alt="">
            </div>
            <p>預かる日:<span class="label">7月3日</span></p>
          </a>
        </div>
      </div>

    </div>
     <!--サイドバー-->
     <?php
     require('sidebar.php');
     ?>
    
    </div>
  </div>
    <!--フッター-->
    <?php 
    require('footer.php'); ?>
</body>
</html>