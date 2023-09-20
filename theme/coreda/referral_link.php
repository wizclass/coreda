<?
include_once(G5_THEME_PATH.'/_include/head.php');
include_once(G5_THEME_PATH.'/_include/wallet.php');
include_once(G5_THEME_PATH.'/_include/gnb.php');

$short_URL = '';
// $url = 'http://khaninter.co.kr//go/?url='.G5_URL.'/bbs/register_form.php?recom_referral='.$member['mb_no']; //접속할 url 입력
$url = G5_URL.'/go/?url='.G5_URL.'/bbs/register_form.php?recom_referral='.$member['mb_no'];
//$header_data = array('Authorization: Bearer access_token_value'); //에러 발생

$ch = curl_init(); //curl 사용 전 초기화 필수(curl handle)

curl_setopt($ch, CURLOPT_URL, $url); //URL 지정하기
curl_setopt($ch, CURLOPT_POST, 0); //0이 default 값이며 POST 통신을 위해 1로 설정해야 함
curl_setopt ($ch, CURLOPT_POSTFIELDS, $post_data); //POST로 보낼 데이터 지정하기
curl_setopt($ch, CURLOPT_ENCODING ,"");

curl_setopt($ch, CURLOPT_HEADER, true);//헤더 정보를 보내도록 함(*필수)
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); //이 옵션이 0으로 지정되면 curl_exec의 결과값을 브라우저에 바로 보여줌. 이 값을 1로 하면 결과값을 return하게 되어 변수에 저장 가능(테스트 시 기본값은 1인듯?)
$res = curl_exec ($ch);

$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$header = substr($res, 0, $header_size);
$short_URL_row = substr($res, $header_size);

$short_URL_p = preg_replace("/\s+/", "", (substr($short_URL_row,0)));

$short_URL = str_replace(array('www.'), '', $short_URL_p);

curl_close($ch);
?>


<script src="<?=G5_THEME_URL?>/_common/js/qrcode.js"></script>


<div class="container nomargin nopadding">
	<div class="dash_contents content-box6">
		<section class="refer_link_wrap">
			<div class="qr_wrap mc_bit">
				<p class="title"><?=$member['mb_id']?></p>
				<div class="google-auth-top-qr" id="qrcode"></div>
				<p id="short_URL" class="url"><a href="<?=$short_URL_p?>"><?=$short_URL?></a></p>
				<input type="button" class="btn wd b_skyblue"  onclick="copyToClipboard(short_URL);" value="링크 복사" style="background: #f5f5f5;" >
			</div>
		</section>
	</div>
	<input type="hidden" id="clip_target">


	<div class="gnb_dim"></div>
</div>
	<script>

		$(function() {

			$('#qrcode').empty();
			$(".top_title h3").html("<span >내 추천인 링크</span>")

			$(".pop_open").click(function(){
				$(".exc_pop_wrap").css("display","block");
			});
		});

		$(document).ready(function(){
			$('.clipboardBtn').on('click', function(e) {
				console.log($('#short_URL').html());
				// a의 텍스트값을 가져옴
				var text = $('#short_URL').html(); //숨겨진 input박스 value값으로 text 변수 넣어줌.
				$('#clip_target').val(text); //input박스 value를 선택
				$('#clip_target').select(); // Use try & catch for unsupported browser
				try { // The important part (copy selected text)
					var successful = document.execCommand('copy');
					// if(successful) answer.innerHTML = 'Copied!';
					// else answer.innerHTML = 'Unable to copy!';
					alert('copy Complete');
				} catch (err) {
					 alert('이 브라우저는 지원하지 않습니다.');
				}
			});

		});


		function copyToClipboard(element) {

			dialogModal("링크 주소 복사","링크 주소를 클립보드에 복사했습니다.",'success');

		var $temp = $("<input>");
			$("body").append($temp);
			$temp.val($(element).text()).select();
			document.execCommand("copy");
			$temp.remove();
		}

		$(window).load(function(){
			var url = "<?=$short_URL_p?>";

			new QRCode(document.getElementById("qrcode"), {
				text: url,
				width: 200,
				height: 200,
				colorDark : "#000000",
				colorLight : "#ffffff",
				correctLevel : QRCode.CorrectLevel.H
			});
		});
	</script>





<? include_once(G5_THEME_PATH.'/_include/tail.php'); ?>
