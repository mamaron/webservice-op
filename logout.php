<?php 
//共通関数
/*
ログアウト機能
セッション変数を全て削除する。
ログイン機能に遷移する。
*/
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「ログアウト機能開始');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

if(!empty($_SESSION['login_date'])){
  debug('セッションとファイル削除します.セッションIDも');
  $_SESSION = array();
  session_destroy();
  debug('ログインページに遷移します.');
  header('Location:login.php');
  exit;
}else{
  debug('セッション変数にログイン日時がありません。未ログインユーザーの恐れがあります。');
  debug('ログインページに遷移します.');
  header('Location:login.php');
  exit;
}


?>