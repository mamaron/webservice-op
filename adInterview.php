<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css">
  <link href="https://fonts.googleapis.com/css2?family=M+PLUS+Rounded+1c&family=Noto+Sans+JP&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
  <title>事前面談確認 || いぬの駅</title>
</head>
<body>
  <!--ヘッダー-->
  <header class="header">
    <div class="container">
      <h1><a href="index.html">いぬの駅</a></h1>
      <nav>
        <ul>
          <li><a href="logout.html">ログアウト</a></li>
          <li><a href="mypage.html">マイページ</a></li>
        </ul>
      </nav>
    </div>
  </header>
  <!--メイン-->
  <div class="site-width">
   <h1 class="title">事前面談連絡ページ</h1>
    <div class="area-msg">

    </div>
    <div class="two-columns-container">
    <!--メインバー-->
     <div class="two-columns-site" id="ad-two-columns-site">
      <div class="adinterview-wrap">
        <p>愛犬情報</p>
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
        <p></p>
      </div>
      <div class="day_ad">
        <p>日程候補日</p>
        <span>7月13日</span>
      </div>
      <!--メイン連絡掲示板-->
      <div class="chat-container">
        <div class="chat-header">
          <h2>太郎さんとのチャット</h2>
        </div>
        <div class="chat-body js-chat-body">
          <!--自分のメッセージ-->
          <div class="message me">
            <div class="message-block">
              <div class="message-content">
                初めまして。プロフィールを見てご連絡させていただきました。
              </div>
              <div class="message-date">2025/07/03 10:12</div>
            </div>
          </div>
          <!--相手のメッセージ-->
          <div class="message other">
            <div class="message-block">
              <div class="message-content">
                初めまして。プロフィールを見てご連絡させていただきました。
              </div>
              <div class="message-date">2025/07/03 10:12</div>
            </div>
          </div>
          
          <!--自分のメッセージ-->
          <div class="message me">
            <div class="message-content">
              一度お話しさせていただきたいです！
            </div>
          </div>
          <!--相手のメッセージ-->
          <div class="message other">
            <div class="message-content">
              初めまして！ご連絡ありがとうございます！
            </div>
          </div>

        </div>
        <div class="chat-input">
          <form action="" method="post">
            <input type="text" placeholder="メッセージを入力">
            <input type="submit" name="submit" value="送信">
          </form>
        </div>
       </div>
       <!--ホスト側画面で表示させる-->
       <button class="acceptInt" data-msgid="" style="display:none;">事前面談を承諾する</button>
    </div>
     <!--サイドバー-->
     <div class="sidebar sidebar-ad">
      <p>連絡を取りたいホストさんを<br>選択してください!</p>
      <a href="adInterview.html" class="sidebar-host-wrap">
        <div class="sidebar-host-img">
          <img src="img/download-2.jpg" alt="">
        </div>
        <div class="sidebar-host-items">
          <ul>
            <li><span class="label">名前：</span>太郎</li>
            <li><span class="label">最寄駅：</span>虎ノ門駅</li>
          </ul>
        </div>
      </a>
      <a href="adInterview.html" class="sidebar-host-wrap">
        <div class="sidebar-host-img">
          <img src="img/download-3.jpg" alt="">
        </div>
        <div class="sidebar-host-items">
          <ul>
            <li><span class="label">名前：</span>二郎</li>
            <li><span class="label">最寄駅：</span>新宿駅</li>
          </ul>
        </div>
      </a>
      <a href="adInterview.html" class="sidebar-host-wrap">
        <div class="sidebar-host-img">
          <img src="img/download-4.jpg" alt="">
        </div>
        <div class="sidebar-host-items">
          <ul>
            <li><span class="label">名前：</span>三郎</li>
            <li><span class="label">最寄駅：</span>恵比寿駅</li>
          </ul>
        </div>
      </a>
      <div class="ad-line">
      </div>
      <p class="finish-host">飼い主さんから連絡が来ています！</p>
      <a href="adInterview.html" class="sidebar-host-wrap owner">
        <div class="sidebar-owner-img">
          <img src="img/download-dog1.jpg" alt="">
        </div>
        <div class="sidebar-owner-items">
          <p class="owner-items">飼い主さん</p>
        </div>
      </a>

    
     </div>
    </div>
  </div>
    <!--フッター-->
    <?php
    require('footer.php');
     ?>
      <!--<script src="scroll.js"></script>-->
</body>
</html>