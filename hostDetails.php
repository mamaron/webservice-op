<?php
//共通関数
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「ホスト詳細');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();
//画面処理開始
//GET
$host = (!empty($_GET['h_id'])) ? $_GET['h_id'] : '';
//ログインユーザー
$user = (!empty($_SESSION['user_id'])) ? $_SESSION['user_id'] : '';

//GET改ざん対策
if(!empty($host)){
  validGetParam($host);
}
//必要情報
//ホスト詳細の情報
$dbHostData = getHostData($host);
debug('ホスト情報:'.print_r($dbHostData,true));

//ホストのスケジュール管理
$dbCalendarDatesRaw = getHostAvailable($_GET['h_id']);
//連想配列の配列なので、日付だけの配列に転換
$dbCalendarDates = [];
if(!empty($dbCalendarDatesRaw)){
  foreach($dbCalendarDatesRaw as $item){
    $dbCalendarDates[] = $item['available_date'];
  }
}
//希望日選択されたかチェック
$avaDaySelected = getDaySelected($user,$host);


/*
POST送信
[A]availabilityテーブルでホスト、飼い主側のIDがあるかどうか。これは事前に必要情報で取得する。
[A]がある場合、
マイページ遷移
Aがない場合はエラー文出す。
*/

//POST送信
if(!empty($_POST['submit'])){
  debug('POST送信があります。');

  debug('自動認証');
  require('auth.php');

  if($avaDaySelected === true){
    debug('マイページに遷移します。');
    $_SESSION['msg_success'] = SUC09;
    header('Location:mypage.php');
    exit;
  }else{
    debug('自動認証通ってて、ホスト情報もあるのに、エラーが発生');
    $err_msg['common'] = MSG07;
  }

}





debug('画面実装処理終了<<<<<<<<<<<<<<<<<<<<<<<<<');
?>

<?php
$siteTitle = 'ホスト情報の詳細';
require('head.php');
?>
  <!--ヘッダー-->
  <?php 
  require('header.php');
  ?>
  <!--メイン-->
  <div class="site-width">
   <h1 class="title">ホストさんについて</h1>
    <div class="two-columns-container">
    <!--メインバー-->
     <div class="two-columns-site">
    <div class="area-msg">
      <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
    </div>
    <div class="host-pic">
      <img src="uploads/<?php echo sanitize($dbHostData['pic2']); ?>" alt="部屋の写真">
    </div>
    <h3 class="host-detail-title">プラン</h3>
    <table class="host-price">
      <tbody>
        <tr><th>おさんぽ</th><td>¥<?php echo sanitize(number_format($dbHostData['price1'])); ?>円</td></tr>
        <tr><th>お泊まり</th><td>¥<?php echo sanitize(number_format($dbHostData['price2'])); ?> 円</td></tr>
      </tbody>
    </table>
    <h3 class="host-detail-title">自己紹介</h3>
    <p class="host-comment">
      <?php echo sanitize($dbHostData['comment']); ?>
    </p>
    <h3 class="host-detail-title">アクセス 最寄り駅：<span><?php echo sanitize($dbHostData['station']);  ?></span></h3>
    <div class="map-area">
      <?php
       $station = urlencode($dbHostData['station']);
      ?>
      <iframe src="https://www.google.com/maps/embed/v1/place?key=AIzaSyCw_QaBWh-1iVzSqBhlKUlCY47wnAzTTKI&q=<?php echo sanitize($station); ?>" frameborder="0"></iframe>
    </div>
    <a href="index.php<?php echo appendGetParam(array('h_id')); ?>" class="return-icon-host"><i class="fa-solid fa-chevron-left"></i>一覧に戻る</a>
  </div>
     <!--サイドバー-->
     <div class="sidebar">
    <div class="sidebar-img">
      <img src="uploads/<?php echo sanitize($dbHostData['pic1']); ?>" alt="">
    </div>
    <label class="sidebar-item">
      名前：<span><?php echo sanitize($dbHostData['hostname']); ?></span>
    </label><br>
    <label class="sidebar-item">
      都道府県：<span> <?php echo sanitize($dbHostData['prefecture']); ?></span>
    </label><br>
    <label class="sidebar-item">
      対応可能サイズ：<span><?php echo sanitize($dbHostData['able_dog']); ?></span>
    </label><br>
    <button class="like-btn" data-hostid="">
      お気に入り<i class="fa-regular fa-heart"></i>
    </button>
      <!--カレンダー-->
      <?php
      //前月、次月リンクが選択された場合はGETパラメータから年月を取得
      if(isset($_GET['ym'])){
        $ym = $_GET['ym'];
      }else{
        //今月の年月を表示
        $ym = date('Y-m');
      }
      //タイムスタンプを作成し、フォーマットをチェックする
      $timestamp = strtotime($ym . '-01');//年月に初日をプラスしてタイムスタンプ化
      if($timestamp === false){//エラー対策として形式チェックを追加
        //falseが返ってきた時は現在の年月、タイムスタンプを取得
        $ym = date('Y-m');
        $timestamp = strtotime($ym . '-01');//-01は明示的につける。付けないとどの日にちかわからないとなってバージョンによってはエラー発生。バグになる可能性がある。
      }
      //今月の日付　フォーマット 例)2025-07-15
      $today = date('Y-m-j');
      //カレンダーのタイトル月
      $html_title = date('Y年n月',$timestamp);
      //前月・次月の年月を取得
      //strtotime(,基準)
      $prev = date('Y-m',strtotime('-1 month',$timestamp));
      $next = date('Y-m',strtotime('+1 month',$timestamp));
      //該当月の日数を取得
      $day_count = date('t',$timestamp);
      debug($ym . 'の日数'.$day_count);
      debug($ym.'の日数のデータ型:'.gettype($day_count));
      //1日の曜日取得
      $youbi = date('w',$timestamp);
      debug('1日の曜日:'.$youbi);
      //カレンダー作成の準備
      $weeks = [];
      $week = '';

      //第一週目:空のセルを追加=>1日目の初め位置を曜日で測ってその分の空セル
      $week .= str_repeat('<td></td>',$youbi);

      for($day = 1; $day <= $day_count;$day++,$youbi++){
        //日付のフォーマット作成
        $dateStr = $ym . '-' . sprintf('%02d',$day);
        //照合してクラスを追加
        $class = in_array($dateStr,$dbCalendarDates) ? 'host_available' : '';
        if($day < 10){
          $week .= '<td class="calender-td '.$class .'"
           data-host_id="' . $host . '" 
           data-buy_id="' . ((!empty($_SESSION['user_id'])) ? $_SESSION['user_id'] : '') . ' " 
           data-date=" ' . $ym .'-0' .$day .'">' . $day . '</td>';
        }else{
          $week .= '<td class="calender-td '.$class .'" 
           data-host_id="' . $host . '" 
           data-buy_id="' . ((!empty($_SESSION['user_id'])) ? $_SESSION['user_id'] : '') . ' " 
           data-date=" ' . $ym .'-' .$day .'">' . $day . '</td>';
        }

        //週終わりもしくは月末の場合
        if(($youbi % 7) == 6 || $day == (int)$day_count){
          //6は土曜日
          //月の最終日、空のセルを追加

          if($day === (int)$day_count){
            $week .= str_repeat('<td></td>', 6 - ($youbi % 7));
          }
          $weeks[] = '<tr>' . $week . '</tr>';
          $week = '';
        }
      }
      ?>
    
    <div class="hostDetail-calendar-head ">
    <span class="center"><?php echo $html_title; ?></span>
    </div>
    <label for="" style="color:#E76F51;">
      ※希望日を選択してください
    </label>
    <div class="calendar hostDetail-calendar">
    <div class="cale-container">
        <table class="table table-bordered" style="width:250px;">
          <tr>
            <th>日</th>
            <th>月</th>
            <th>火</th>
            <th>水</th>
            <th>木</th>
            <th>金</th>
            <th>土</th>
          </tr>
          <?php
           foreach($weeks as $week){
            echo $week;
           }
          ?>
        </table>
      </div>
    </div>
    <div class="interviewEntry">
      <form action="" method="post">
        <input type="submit" name="submit" value="事前面談をリクエスト!">
      </form>
    </div>
     </div>
    </div>
  </div>
    <!--フッター-->
    <?php
    require('footer.php');
    ?>