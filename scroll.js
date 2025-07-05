$(function(){
  //===================================
//LINE風スクロールの最新表示
//===================================
const $chat = $('.js-chat-body');
if(location.pathname.includes('adInterview.php')){
  if($chat.length > 0){
    $chat.scrollTop($chat[0].scrollHeight);
  }
}

});