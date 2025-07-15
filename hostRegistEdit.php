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
  validRequired((!empty($_FILES['pic1']['name'])) ? $_FILES['pic1']['name'] : $_POST['img_prev1'],'pic1');
  validRequired((!empty($_FILES['pic2']['name'])) ? $_FILES['pic1']['name'] : $_POST['img_prev2'],'pic2');
  validRequired($_POST['comment'],'comment');

  if(empty($err_msg)){
    debug('未入力バリデOK。変数詰め込み');
    $hostName = $_POST['hostname'];
    $zip = (int)$_POST['zip'];
    $prefecture = $_POST['prefecture'];
    $municipalities = $_POST['municipalities'];
    $street = $_POST['street'];
    $building = $_POST['building'];
    $station = $_POST['station'];
    $able_dog = (int)$_POST['able_dog'];
    $price1 = (int)$_POST['price1'];
    $price2 = (int)$_POST['price2'];
    $pic1 = (!empty($_FILES['pic1']['name'])) ?  uploadImg($_FILES['pic1'],'pic1') : $_POST['img_prev1'];
    $pic2 = (!empty($_FILES['pic2']['name'])) ?  uploadImg($_FILES['pic2'],'pic2') : $_POST['img_prev2'];
    $comment = $_POST['comment'];

    if(empty($err_msg)){
      debug('変数格納&&画像アップロード成功');
      debug('DB情報と異なる場合はバリデーション。');
      //初手入力バリデーション
      if(empty($dbFormData)){
        debug('初回バリデーション,');
        //ホスト名前
        validMaxLen($hostName,'hostName',50);
        //郵便番号
        validHalf($zip,'zip');
        if(empty($err_msg)){
          $math = 7;
          validMathMatch($zip,$math,'zip');
        }
        //都道府県
        validMaxLen($prefecture,'prefecture',10);
        //市区町村
        validMaxLen($municipalities,'municipalities',50);
        //建物
        validMaxLen($building,'building',50);
        //駅
        validMaxLen($station,'station',50);
        //対応可能サイズ
        validMisMatch($able_dog,'able_dog');
        if(empty($err_msg['able_dog'])){
          //セレクトボックスのvalueの値
          validSelect($able_dog,'able_dog');
        }
        //金額1
        validHalf($price1,'price1');
        if(empty($err_msg)){
          validMaxLen($price1,'price1');
        }
        //金額2
        validHalf($price2,'price2');
        if(empty($err_msg)){
          validMaxLen($price2,'price2');
        }
        //自己紹介
        validMaxLen($comment,'comment',500);
      }else{
        debug('編集バリデーション,');
        //ホスト名前
        if($dbFormData['hostname'] !== $hostName){
          validMaxLen($hostName,'hostName',50);
        }
        //郵便番号
        if((int)$dbFormData['zip'] !== $zip){
          validHalf($zip,'zip');
          if(empty($err_msg)){
            $math = 7;
            validMathMatch($zip,$math,'zip');
          }
        }
        //都道府県
      if($dbFormData['prefecture'] !== $prefecture){
        validMaxLen($prefecture,'prefecture',10);
      }
      //市区町村
      if($dbFormData['municipalities'] !== $municipalities){
        validMaxLen($municipalities,'municipalities',50);
      }
      //番地
      if($dbFormData['street'] !== $street){
        validMaxLen($street,'street',50);
      }
      //建物
      if($dbFormData['building'] !== $building){
        validMaxLen($building,'building',50);
      }
      //駅
      if($dbFormData['station'] !== $station){
        validMaxLen($station,'station',50);
      }
      //対応可能サイズ
      if($dbFormData['able_dog'] !== $able_dog){
        validMisMatch($able_dog,'able_dog');
        if(empty($err_msg)){
          //セレクトボックスのvalueの値
          validSelect($able_dog,'able_dog');
        }
      }
      //金額
      if($dbFormData['price1'] !== $price1){
        validHalf($price1,'price1');
        if(empty($err_msg)){
          validMaxLen($price1,'price1');
        }
      }
      if($dbFormData['price2'] !== $price2){
        validHalf($price2,'price2');
        if(empty($err_msg)){
          validMaxLen($price2,'price2');
        }
      }
      //自己紹介
      if($dbFormData['comment'] !== $comment){
        validMaxLen($comment,'comment',500);
      }

      debug('DB情報とPOST情報が同じ場合はエラー出す');
        $db_data = [
          'hostname' => $dbFormData['hostname'],
          'zip' => (int)$dbFormData['zip'],
          'prefecture' => $dbFormData['prefecture'],
          'municipalities' => $dbFormData['municipalities'],
          'street' => $dbFormData['street'],
          'building' => $dbFormData['building'],
          'station' => $dbFormData['station'],
          'able_dog' => $dbFormData['able_dog'],
          'price1' => (int)$dbFormData['price1'],
          'price2' => (int)$dbFormData['price2'],
          'pic1' => $dbFormData['pic1'],
          'pic2' => $dbFormData['pic2'],
          'comment' => $dbFormData['comment']
        ];
        $post_data = [
          'hostname' => $hostName,
          'zip' => $zip,
          'prefecture' => $prefecture,
          'municipalities' => $municipalities,
          'street' => $street,
          'building' => $building,
          'station' => $station,
          'able_dog' => $able_dog,
          'price1' => $price1,
          'price2' => $price2,
          'pic1' => $pic1,
          'pic2' => $pic2,
          'comment' => $comment
        ];
        //$diff = array_diff_assoc($db_data,$post_data);
        //debug('差分'.print_r($diff,true));
        if($db_data === $post_data){
          $err_msg['common'] = 'DB情報と同じです。';
        }
      }
        if(empty($err_msg)){
          debug('バリデーションOK');
          debug('DB情報と完全には一致しません。');
          debug('DB接続しちゃいます。');
          //例外処理
          try{
            //DB接続
            $dbh = dbConnect();
            if(!empty($dbFormData)){
              debug('アップデートします。');
              $sql = 'UPDATE host SET hostname = :h_name, zip = :zip, prefecture = :pref, municipalities = :munis, street = :street,
              building = :building, station = :station, able_dog = :able_dog, price1 = :price1, price2 = :price2, pic1 = :pic1, pic2 = :pic2, comment = :comment WHERE user_id = :u_id';
              $data = array(
                      ':h_name' => $hostName,
                      ':zip' => $zip,
                      ':pref' => $prefecture,
                      ':munis' => $municipalities,
                      ':street' => $street,
                      ':building' => $building,
                      ':station' => $station,
                      ':able_dog' => $able_dog,
                      ':price1' => $price1,
                      ':price2' => $price2,
                      ':pic1' => $pic1,
                      ':pic2' => $pic2,
                      ':comment' => $comment,
                      ':u_id' => $_SESSION['user_id']
              );
              //クエリ実行
              $stmt = queryPost($dbh,$sql,$data);
              $result = $stmt->rowCount();
              debug('クエリ結果:'.$result);
              if($result > 0){
                debug('アップデート成功');
                $_SESSION['msg_success'] = SUC07;
                debug('マイページに遷移します。');
                header("Location:mypage.php");
                exit;
              }else{
                debug('アップデート失敗');
                $err_msg['common'] = MSG07;
              }
            }else{
              debug('新規登録します。');
              $sql = 'INSERT INTO host(user_id, hostname, zip, prefecture, municipalities, street, building, station,
              able_dog, price1, price2, pic1, pic2, comment, create_date) VALUES(:u_id, :h_name, :zip, :pref, :munis, :street, :building, :station,
              :able_dog, :price1, :price2, :pic1, :pic2, :comment, :c_date)';
              $data = array(
                      ':u_id' => $_SESSION['user_id'],
                      ':h_name' => $hostName,
                      ':zip' => $zip,
                      ':pref' => $prefecture,
                      ':munis' => $municipalities,
                      ':street' => $street,
                      ':building' => $building,
                      ':station' => $station,
                      ':able_dog' => $able_dog,
                      ':price1' => $price1,
                      ':price2' => $price2,
                      ':pic1' => $pic1,
                      ':pic2' => $pic2,
                      ':comment' => $comment,
                       ':c_date' => date('Y-m-d H:i:s')
              );
              //クエリ実行
              $stmt = queryPost($dbh,$sql,$data);
              $result = $stmt->rowCount();
              if($result > 0){
                debug('登録成功');
                $_SESSION['msg_success'] = SUC08;
                debug('マイページに遷移します。');
                header("Location:mypage.php");
                exit;
              }else{
                debug('登録失敗');
                $err_msg['common'] = MSG07;
              }
            }
          }catch(Exception $e){
            debug('エラー発生：'.$e->getMessage());
            $err_msg['common'] = MSG07;
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
          <?php if(!empty($dbFormData['able_dog'])){ ?>
            <option value="1" <?php if($dbFormData['able_dog'] === 1) echo 'selected'; ?>>小型犬のみ</option>
            <option value="2" <?php if($dbFormData['able_dog']  === 2) echo 'selected'; ?>>小型犬〜中型犬</option>
            <option value="3" <?php if($dbFormData['able_dog']  === 3) echo 'selected'; ?>>全てのサイズ対応可</option>
          <?php }else{ ?> 
            <option value="1" <?php if(!empty($able_dog) && $able_dog === 1) echo 'selected'; ?>>小型犬のみ</option>
            <option value="2" <?php if(!empty($able_dog) && $able_dog === 2) echo 'selected'; ?>>小型犬〜中型犬</option>
            <option value="3" <?php if(!empty($able_dog) && $able_dog === 3) echo 'selected'; ?>>全てのサイズ対応可</option>
          <?php } ?>
        </select>
      </label>
      <div class="area-msg">
        <?php if(!empty($err_msg['able_dog'])) echo $err_msg['able_dog']; ?>
      </div>

      <label class="label-input">
        プラン料金<span class="lab-asterisk">*</span><br>
        <span>おさんぽ</span>
        <?php
          $price1_int = (int)getFormData('price1');
         ?>
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
      <?php 
        $pic1_msg = (!empty($err_msg['pic1'])) ? $err_msg['pic1'] : '';
        $pic2_msg = (!empty($err_msg['pic2'])) ? $err_msg['pic2'] : '';
      ?>
      <?php if($pic1_msg || $pic2_msg): ?>
      <div class="area-msg">
        <?php if($pic1_msg && $pic1_msg === $pic2_msg){ ?>
          <?php echo $err_msg['pic1']; ?>
        <?php }else{ ?>
          <?php if($pic1_msg) echo $err_msg['pic1'] . '<br>'; ?>
          <?php if($pic2_msg) echo $err_msg['pic2']; ?>
        <?php }?>  
      </div>
      <?php endif; ?>

      <label class="host-input" for="">
        自己紹介<span class="lab-asterisk">*</span>
        <textarea name="comment" class="comment js-valid-text js-valid-comment" id=""><?php echo getFormData('comment'); ?></textarea>
        <div class="count"><span class="js-count-text">0</span>/500</div>
      </label>
      <div class="area-msg">
        <?php if(!empty($err_msg['comment'])) echo $err_msg['comment']; ?>
      </div>
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
      $timestamp = strtotime($ym . '-01');
      if($timestamp === false){//エラー対策として形式チェックを追加
        //falseが返ってきた時は現在の年月、タイムスタンプを取得
        $ym = date('Y-m');
        $timestamp = strtotime($ym . '-01');
      }
      //今月の日付　フォーマット 例)2025-07-15
      $today = date('Y-m-j');
      ?>
      

      <div class="calender-head">
        <h3><a href="hostRegistEdit.php?ym=<?php echo $prev;?>" class="left">&lt;</a>
        <span class="center"><?php echo $html_title; ?></span>
        <a href="hostRegistEdit.php?ym=<?php echo $next; ?>" class="right">&gt;</a></h3>
      </div>
      <label for="" style="color:#E76F51;">
        ※対応可能日を選択してください
      </label>
      <div class="cale-container">
        <table class="table table-bordered">
          <tr>
            <th>日</th>
            <th>月</th>
            <th>火</th>
            <th>水</th>
            <th>木</th>
            <th>金</th>
            <th>土</th>
          </tr>
          <tr>
            <td></td>
            <td></td>
            <td>1</td>
            <td>2</td>
            <td>3</td>
            <td>4</td>
            <td>5</td>
          </tr>
          <tr>
            <td>6</td>
            <td>7</td>
            <td>8</td>
            <td>9</td>
            <td>10</td>
            <td>11</td>
            <td>12</td>
          </tr>
          <tr>
            <td>13</td>
            <td>14</td>
            <td>15</td>
            <td>16</td>
            <td>17</td>
            <td>18</td>
            <td>19</td>
          </tr>
          <tr>
            <td>20</td>
            <td>21</td>
            <td>22</td>
            <td>23</td>
            <td>24</td>
            <td>25</td>
            <td>26</td>
          </tr>
          <tr>
            <td>27</td>
            <td>28</td>
            <td>29</td>
            <td>30</td>
            <td>31</td>
            <td></td>
            <td></td>
          </tr>
        </table>
      </div>
      

      <input type="submit" name="submit" value=" <?php echo (!empty($dbFormData)) ? '編集!' : '登録！'; ?>" class="simple-btn">
    </form>
   </div>
  </div>
    <!--フッター-->
    <?php
    require('footer.php');
    ?>