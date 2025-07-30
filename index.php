<?php
//共通関数
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「トップページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

/*

トップページ処理フロー

[1]
※ページネーション
ホスト情報取得
foreachで展開

ページネーションのロジック =>クリック=>getパラメータ付与
ホスト全体数: total
１ページに表示する表示数： $hostSpan

ホストはクリック=>getパラメータ付与

[2]
＊セレクトボックス検索 & ソート検索
都道府県のカテゴリー取得

都道府県は独立で使える、且つジャンル、金額との併用可能。
ジャンルは独立では使えない。あくまでも金額の高い安いと一緒
金額が安いも何を持って高いと定義するのかがあるので、ジャンルと一緒。
====================================

*/
//ページ用のGETパラメータ
$currentPageNum = (!empty($_GET['p'])) ? $_GET['p'] : '1';//デフォルトは1ページ目
//1ページに表示する表示数
$hostSpan = 20;
//現在の表示レコード先頭を算出
$currentMinNum = ($currentPageNum - 1) * $hostSpan;

//都道府県のカテゴリー取得 都道府県情報とその数取得している。
$dbPrefCategory = getPrefData();

//都道府県カテゴリー
$prefCategory = (isset($_GET['pref'])) ? $_GET['pref'] : '';
//ジャンルカテゴリー
$genreCategory = (!empty($_GET['genre'])) ? $_GET['genre'] : '';
//金額カテゴリー
$priceCategory = (!empty($_GET['price'])) ? $_GET['price'] : '';


//$_GET['huge'] = '0';
//var_dump(!empty($_GET['huge'])); => false


//金額ソート

//GETパラメータの改ざんチェック
//========================================
debug('$currentPageNumの値:'.$currentPageNum);
debug('$_GETの値:'.print_r($_GET,true));
validGetParam($currentPageNum);
if(isset($_GET['pref']) && $_GET['pref'] !== ''){
  debug('あ');
  validGetParam($_GET['pref']);
  validSelect($_GET['pref'],'pref',$dbPrefCategory['result']);
}
if(isset($_GET['genre']) && $_GET['genre'] !== '0' && $_GET['genre'] !== ''){
  debug('い');
  validGetParam($_GET['genre']);
  $validList[] = 0;
  validSelect($_GET['genre'],'genre',$validList);
}
if(isset($_GET['price']) && $_GET['price'] !== '0' && $_GET['price'] !== ''){
  debug('う');
  validGetParam($_GET['price']);
  $validList[] = 0;
  validSelect($_GET['price'],'price',$validList);
}





//ホスト全情報取得
$dbHostData = getTopHostData($hostSpan,$currentMinNum,$prefCategory,$genreCategory,$priceCategory);
//debug('ホスト情報一覧:'.print_r($dbHostData,true));


//現在のページ
debug('現在のページ:'.$currentPageNum);



//======================================== 
//画面表示処理開始
//========================================


?>
<?php
$siteTitle = 'トップページ';
require('head.php');
?>
  <!--ヘッダー-->
  <?php 
  require('header.php'); 
  ?>
  <!--メインビジュアル-->
  <section class="main-visual">
    <div class="visual-text">
      <h1>あなたのペットに<br>第二の我が家を</h1>
      <p>安心できるホストとの出会いを提供します</p>
    </div>
   </section>
  <!--カテゴリーゾーン-->
    <section class="main-category">
      <form action="" method="get" class="category-form">
          <div class="cate-wrap">
            <p>都道府県</p>
            <select name="pref" class="category-item cate-prefecture">
              <option value="">選択してください</option>
              <?php 
                foreach($dbPrefCategory['result'] as $pref):
              ?>
              <option value="<?php echo sanitize($pref['id']); ?>" <?php if(!empty($_GET['pref']) && $pref['id'] == $_GET['pref']) echo 'selected';?>><?php echo sanitize($pref['name']); ?></option>
              <?php
                endforeach;
              ?>
            </select>
            <div class="area-msg">
              <?php if(!empty($err_msg['pref'])) echo $err_msg['pref']; ?>
            </div>
          </div>
          <div class="cate-wrap">
            <p>ジャンル</p>
            <select name="genre" class="category-item cate-genre">
              <option value="0">選択してください</option>
              <option value="1" <?php if(!empty($_GET['genre']) && $_GET['genre'] == '1') echo 'selected'; ?>>おさんぽ</option>
              <option value="2" <?php if(!empty($_GET['genre']) && $_GET['genre'] == '2') echo 'selected'; ?>>お泊まり</option>
            </select>
            <div class="area-msg">
              <?php if(!empty($err_msg['genre'])) echo $err_msg['genre']; ?>
            </div>
          </div>
          <div class="cate-wrap">
            <p>金額</p>
            <select name="price" class="category-item">
              <option value="0">選択してください</option>
              <option value="1" <?php if(!empty($_GET['price']) && $_GET['price'] == '1') echo 'selected'; ?>>金額が高い順</option>
              <option value="2" <?php if(!empty($_GET['price']) && $_GET['price'] == '2') echo 'selected'; ?>>金額が安い順</option>
            </select>
            <div class="area-msg">
              <?php if(!empty($err_msg['price'])) echo $err_msg['price']; ?>
            </div>
          </div>
          <input type="submit" class="btn-category" value="検索">
      </form>
    </section>
  <div class="site-width">
   <div class="one-columns-site">
  <!--検索結果件数-->
   <div class="search-wrap">
    <span><?php echo sanitize($currentMinNum + 1); ?></span>件 - 
      <span><?php echo ($currentPageNum == $dbHostData['total_page']) ? sanitize($dbHostData['total']) : sanitize($currentPageNum * $hostSpan); ?></span>件表示/
      <span> <?php echo sanitize($dbHostData['total']); ?> </span>件中</div>
    <div class="like-wrap">
      <?php 
        foreach($dbHostData['rst'] as $host ):
        ?>
      <a href="hostDetails.php<?php echo (!empty($_GET)) ? appendGetParam() . '&h_id=' . $host['user_id'] : '?h_id=' . $host['user_id']; ?>" class="host-like-wrap">
        <div class="like-img">
          <img src="uploads/<?php echo sanitize($host['pic1']); ?>" alt="写真">
        </div>
        <ul class="host-nav">
          <li><span class="label">名前：</span><?php echo sanitize($host['hostname']); ?></li>
          <li><span class="label">お住まい：</span><?php echo sanitize($host['prefecture']); ?></li>
          <li><span class="label">おさんぽ：</span>¥<?php echo number_format(sanitize($host['price1'])); ?></li>
          <li><span class="label">お泊まり：</span>¥<?php echo number_format(sanitize($host['price2'])); ?></li>
        </ul>
      </a>
      <?php endforeach;?>
      
    </div>
      <!--ページネーション-->
      <!--
      -->
      <?php
      //１ページ２０商品として、トータル何ページなのか変数に格納
      $totalPageNum = $dbHostData['total_page'];
      //表示項目数 
      $pageColum = 5;
      //現在ページがトータルページで、且つ表示項目数よりもトータルが上の場合
      if($currentPageNum == $totalPageNum && $totalPageNum >= $pageColum){
        $minPageNum = $currentPageNum -4;
        $maxPageNum = $currentPageNum;
      //現在ページが総ページ数の１ページ前で且つ表示項目数よりもトータルが上の場合
      }elseif($currentPageNum == $totalPageNum - 1 && $totalPageNum >= $pageColum){
        $minPageNum = $currentPageNum -3;
        $maxPageNum = $currentPageNum +1;
      //現在のページが２の場合は左にリンク１、右に３だす
      }elseif($currentPageNum == 2 && $totalPageNum >= $pageColum){
        $minPageNum = $currentPageNum -1;
        $maxPageNum = $currentPageNum +3;
      //現在のページが１の場合、右に四つ
      }elseif($currentPageNum == 1 && $totalPageNum >= $pageColum){
        $minPageNum = $currentPageNum;
        $maxPageNum = $currentPageNum +4;
      //総ページ数が表示項目数よりも少ない場合はminを１に、maxをtotalにする  
      }elseif($totalPageNum <= $pageColum){
        $minPageNum = 1;
        $maxPageNum = $totalPageNum;
      //ページが３の場合は-2 +2
      }else{
        $minPageNum = $currentPageNum -2;
        $maxPageNum = $currentPageNum +2;
      }
      ?>
      <div class="main-pagenation">
        <ul class="main-page-wrap">
          <?php if($currentPageNum != 1): ?>
            <li><a href="index.php?p=<?php echo $minPageNum; ?>"><i class="fa-solid fa-chevron-left"></i></a></li>
          <?php 
            endif;
            for($i = $minPageNum;$i <= $maxPageNum;$i++):
          ?>
          <li class="<?php if($currentPageNum == $i) echo 'active'; ?>">
           <?php 
           $prefOk = $prefCategory !== '';
           $genreOk = $genreCategory !== '' && $genreCategory !== '0';
           $priceOk = $priceCategory !== '' && $priceCategory !== '0';
           $genrePriceOk = $genreOk && $priceOk;
           ?>
           <?php if($prefOk || $genrePriceOk){ ?>
              <a href="index.php?p=<?php echo $i 
                . ($prefOk ? '&pref=' . $prefCategory : '' ) 
                . ($genrePriceOk ? '&genre=' . $genreCategory : '') 
                . ($genrePriceOk ? '&price=' . $priceCategory : '');?>">
                <?php echo $i; ?>
              </a>
            <?php }else{ ?>  
              <a href="index.php?p=<?php echo $i; ?>"><?php echo $i; ?></a>
            <?php } ?>
          </li>
          <?php endfor;?>
          <?php if($currentPageNum != $totalPageNum): ?>
            <li><a href="index.php?p=<?php echo $maxPageNum; ?>"><i class="fa-solid fa-chevron-right"></i></a></li>
          <?php endif; ?>
          
        </ul>
      </div>
   </div>
  </div>
    <!--フッター-->
    <?php
     require('footer.php');
    ?>