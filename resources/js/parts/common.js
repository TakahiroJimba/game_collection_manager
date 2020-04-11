//送信ボタンを押した際に送信ボタンを無効化する（連打による多数送信回避）
$(function(){
	$('[type="submit"]').click(function(){
		$(this).prop('disabled',true);        //ボタンを無効化する
		$(this).closest('form').submit();     //フォームを送信する
	});
});
