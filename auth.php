<?php 
//自動認証
/*
$_SESSION['user_id]
$_SESSION['login_date]
$_SESSION['login_limit]
*/
//ログイン日時があるかどうか
debug('自動認証開始します');
if(!empty($_SESSION['login_date'])){
  //現在時刻がログイン日時＋ログインリミットを超えているか
  if(time() > ($_SESSION['login_date'] + $_SESSION['login_limit'])){
    debug('ログイン有効期限切れです。');
    session_unset();
    //今がlogin.phpじゃない場合は遷移
    if(basename($_SERVER['PHP_SELF']) !== 'login.php'){
      debug('ログインページに遷移します.');
      header('Location:login.php');
      exit;
    }
  }else{
    debug('ログイン有効期限内です。');
    $_SESSION['login_date'] = time();
    //現在のページがlogin.phpだったら遷移
    if(basename($_SERVER['PHP_SELF']) === 'login.php'){
      debug('マイページに遷移します。');
      header('Location:mypage.php');
      exit;
    }
  }
}else{
  debug('未ログインユーザーです。');
  //今がlogin.phpじゃない場合は遷移
  if(basename($_SERVER['PHP_SELF']) !== 'login.php'){
    header('Location:login.php');
    exit;
  }
}


/*
mypageから入る。

*/
?>