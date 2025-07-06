//jQUery
$(function(){

//===================================
//メッセージ
//===================================
const MSG01 = '入力必須になります。';
const MSG02 = 'email形式でお願いします。';
const MSG03 = '入力された文字数が長すぎます';
const MSG04 = '6文字以上でお願いします.';
const MSG05 = '半角英数字で記入お願いします。';
const MSG06 = '入力された値と再入力された値が一致しません';
const MSG07 = '半角数字でお願いします';


//===================================
//関数
//===================================
//未入力
function validRequired($var,MSG){
  const val = $var.val();
  const msg = $var.parent().next('.area-msg');
  if(val.length === 0){
    msg.text(MSG);
    return false;
  }else{
    msg.text('');
    return true;
  }
}

//最大文字数
function validMaxLen($target,msgText,max = 255){
  const val = $target.val();
  const msg = $target.parent().next('.area-msg');
  if(val.length > max){
    msg.text(msgText);
    return;
  }else{
    msg.text('');
  }
}
//最小文字数
function validMinLen($target,msgText){
  const val = $target.val();
  const msg = $target.parent().next('.area-msg');
  if(val.length < 6){
    msg.text(msgText);
    return false;
  }else{
    msg.text('');
    return true;
  }
}
//email正規表現
function validEmail($target,msgText){
  const val = $target.val();
  const msg = $target.parent().next('.area-msg');
  if(!val.match(/^[a-zA-Z0-9_.+-]+@([a-zA-Z0-9][a-zA-Z0-9-]*[a-zA-Z0-9]*\.)+[a-zA-Z]{2,}$/)){
    msg.text(msgText);
    return;
  }else{
    msg.text('');
  }
}
//半角英数字
function validNumber($target,msgText){
  const val = $target.val();
  const msg = $target.parent().next('.area-msg');
  if(!val.match(/^[a-zA-Z0-9]+$/)){
    msg.text(MSG05);
    return false;
  }else{
    msg.text('');
    return true;
  }
}
//半角数字
function validHalf($target,msgText){
  const val = $target.val();
  const msg = $target.parent().next('.area-msg');
  if(!val.match(/^[0-9]+$/)){
    msg.text(msgText);
    return false;
  }else{
    return true;
  }
}
//パスワードとパスワード再入力が一致するか
function validMatch($target1,$target2,MSG06){
  const val1 = $target1.val();
  const val2 = $target2.val();
  const msg = $target2.parent().next('.area-msg');
  if(val1 !== val2){
    msg.text(MSG06);
    return false;
  }else{
    msg.text('');
    return true;
  }
}



//===================================
//バリデーション
//===================================

//未入力
$('.js-valid-text').on('blur',function(e){
  if(!validRequired($(this),MSG01)){
    e.stopImmediatePropagation();
  } return ;
});

//最大文字数 email,pass,pass_re
$('.js-valid-email, .js-valid-pass, .js-valid-pass-re').on('keyup',function(){
  validMaxLen($(this),MSG03);
});
//最大文字数 dog-name,dog犬種,host-name,都道府県、市区町村、番地、建物、最寄えき
$('.js-valid-name, .js-valid-dog-breed, .js-valid-pref').on('keyup',function(){
  validMaxLen($(this),MSG03,50);
});
//最大文字数 ホスト自己紹介
$('.js-valid-comment').on('keyup',function(){
  validMaxLen($(this),MSG03,50);
});

//email正規表現
$('.js-valid-email').on('blur',function(){
  //email正規表現
  validEmail($(this),MSG02);
});

//emailとemail_reが一致するか
$('.js-valid-email-new-re').on('blur',function(){
  //再と一致するかどうか
  const emailVal = $('.js-valid-email-new').val();
  const emailValRe = $('.js-valid-email-new-re').val();
  console.log(emailValRe);
  console.log('ああああ');
  if(emailVal !== '' && emailValRe !== ''){
    if(!validMatch($('.js-valid-email-new'),$('.js-valid-email-new-re'),MSG06)) return;
  }
});

//未入力 最小文字数、半角英数字、パスワードとパスワード(再)の一致
$('.js-valid-pass1, .js-valid-pass-new, .js-valid-pass-new-re').on('keyup',function(){
  //最小文字数
  if(!validMinLen($(this),MSG04)) return;
  //最大文字数
  if(!validMaxLen($(this),MSG03)) return;
})
$('.js-valid-pass1, .js-valid-pass-new, .js-valid-pass-new-re').on('blur',function(){
  //半角英数字
  if(!validNumber($(this),MSG05)) return;
})

$('.js-valid-pass-new-re').on('blur',function(){
  //パスワード(再と一致するかどうか)
  const passVal = $('.js-valid-pass-new').val();
  const passValRe = $('.js-valid-pass-new-re').val();
  if(passVal !== '' && passValRe !== ''){
    if(!validMatch($('.js-valid-pass-new'),$('.js-valid-pass-new-re'),MSG06)) return;
  }
});
//半角数字 pet年齢、ホストage
$('.js-valid-age, .js-valid-zip, .js-valid-price').on('blur',function(){
  //半角数字
  if(!validHalf($(this),MSG07)) return;
});


//===================================
//カウント
//===================================

//カウント実装
$('.comment').on('keyup',function(){
  var count = $(this).val().length;
  $('.js-count-text').text(count);
});
//===================================
//ふわっとメッセージ表示
//===================================

//ふわっと表示
$('.js-msg-flash').fadeIn('slow',function(){
  $(this).delay('1500').fadeOut(1000);
});

//===================================
//footer位置自動補正
//===================================

/*
・[A]footerの長さ取得
・[B]footerの上からの位置を取得
・[C]サイトの長さ取得
C > A + B{
  footerの位置を変更:C -A
}
*/
$footer = $('.js-footer');
//[A]footerの長さ
var footerHeight = $footer.outerHeight();
//[B]サイトの長さ
var siteHeight = $(window).height();
//[C]footerの上からの位置
var total = siteHeight - footerHeight;
if(siteHeight > ($footer.offset().top + footerHeight)){
  $footer.css({
        'position': 'fixed',
        'top':total + 'px',
        'width': '100%'
  });
}

//===================================
//画像プレビュー ドラッグ&ドロップ
//===================================
//ドラッグ中処理
$('.js-area-drop').on('dragover',function(e){
  //e.preventDefault();
  $(this).css('border-color','#E76F51');
});
//ドラッグ外れる
$('.js-area-drop').on('dragleave',function(e){
  e.preventDefault();
  $(this).css('border-color','transparent');
});
//ドロップ
$('.js-file-left, .js-file-right').on('change',function(e){
  //e.preventDefault();
  $('.js-area-drop').css('border-color','transparent');
  //ファイルを取得
  var file = this.files[0];

  var input = this;
  /*
  File Reader()
  onloadでimgいじる
  readAsDataURL
  */
  var reader = new FileReader();
  reader.onload = function(e){
    $(input).siblings('.js-img').attr('src',e.target.result);
    $(input).siblings('.js-img').css('display','block');
  }
  reader.readAsDataURL(file);
});
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















