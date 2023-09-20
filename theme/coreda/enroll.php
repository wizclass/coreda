<?
$menubar = 1;
$email_auth = 1;
$phone_auth = 0;


include_once(G5_THEME_PATH.'/_include/head.php');
include_once(G5_THEME_PATH.'/_include/gnb.php');
include_once(G5_THEME_PATH.'/_include/lang.php');

if($nw['nw_enroll'] == 'Y'){
}else{
	alert("현재 서비스를 이용할수없습니다.");
}

$service_term = get_write("g5_write_agreement", 1);
$private_term = get_write("g5_write_agreement", 2);

// 추천인링크 타고 넘어온경우
if ($_GET['recom_referral']){
	$recom_sql = "select mb_id,mb_nick from g5_member where mb_no = '{$_GET['recom_referral']}'";
	$recom_result = sql_fetch($recom_sql);
	$mb_recommend = $recom_result['mb_id'];

	if($recom_result['mb_nick'] != ''){
		$mb_center = $mb_recommend;
	}
}
?>

<link href="<?= G5_THEME_URL ?>/css/scss/enroll.css" rel="stylesheet">
<script src="https://use.fontawesome.com/releases/v5.2.0/js/all.js"></script>

<style>
	.gflag{display:none !important;}
</style>


<script type="text/javascript">
	$('#mode_select').on('change',function() {
		mode_change(this.value);	
	})
	$('#mode_select').val(Theme).change();

	var captcha;
	var key;
	var verify = false;
	var recommned = "<?= $mb_recommend ?>";
	var recommend_search = false;
	
	var center_search = false;

	if (recommned) {
		recommend_search = true;
	}
	// console.log(`센터검색 : ${center_search}`);

	$(function() {

		// onlyNumber('reg_mb_hp');
		$('.cabinet').on('click',function(){
			$(this).next().css('display','contents');
		});

		$('.cabinet').on('mouseout',function(){
			$(this).next().css('display','none');
		})


		/*초기설정*/
		//$('.agreement_ly').hide();
		$('#verify_txt').hide();


		/* 핸드폰 SMS 문자인증 사용 */
		$('#nation_number').on('change', function(e) {
			// $('#reg_mb_hp').val($(this).val());
		});

		var phone_auth = "<?= $phone_auth ?>";
		if (phone_auth > 0) {
			$('.verify_phone').hide();

			//SMS발송
			$('#sendSms').on('click', function(e) {
				if (!$('#reg_mb_hp').val()) {
					dialogModal("모바일 본인 인증", "연락가능한 모바일 번호를 등록해주세요.", 'failed');
					return;
				}
				var reg_mb_hp = +($('#reg_mb_hp').val().replace(/-/gi, ''));
				$.ajax({
					url: '/bbs/register.sms.verify.php',
					type: 'post',
					async: false,
					data: {
						"nation_no": $('#nation_number').val(),
						"mb_hp": reg_mb_hp
					},
					dataType: 'json',
					success: function(result) {
						// console.log(result);
						smsKey = result.key;
						dialogModal("SMS 인증", "모바일로 인증 코드를 보냈습니다.", 'success');
					},
					error: function(e) {
						console.log(e);
					}
				});
			});
		}
	
	/* 메일발송 로더 */
	/* var loading = $('<div id="loading" class="loading"></div><img id="loading_img" src="/img/Spinner-1s-200px2.gif" />');
	loading.appendTo(document.body).hide(); */
 	

		/*이메일 체크*/
		$('#EmailChcek').on('click',function(){
			var email = $('#reg_mb_email').val();
			var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;


			if (email == '' || !re.test(email)) {
				dialogModal("이메일 인증","사용가능한 이메일 주소를 입력해주세요.", "failed");
				return false;
			}

			// loading.show();

			$.ajax({
				type: "POST",
				url: "/mail/send_mail_smtp.php",
				dataType: "json",
				data: {
					user_email: email
				},
				complete: function() {
					// loading.hide();
					dialogModal("인증메일발송", "인증메일이 발송되었습니다.<br>메일인증확인후 돌아와 완료해주세요", 'success');
				}

			});

		});

		/* 로더 테스트
		$(document).ajaxStart(function() {
			// loading.show();
			console.log('loading-start');

		}).ajaxStop(function() {
			console.log('loading-end');
			// loading.hide();
		}); */


		/* 사전인증 메일발송 - 사용안함 */
		/* $('#sendMail').on('click', function(e) {
			//console.log('sendmail');
			if (!$('#reg_mb_email').val()) {
				commonModal('Mail authentication', '<p>Please enter your mail</p>', 80);
				return;
			}
			$.ajax({
				url: '/bbs/register.mail.verify.php',
				type: 'GET',
				async: false,
				data: {
					"mb_email": $('#reg_mb_email').val()
				},
				dataType: 'json',
				success: function(result) {
					console.log(result);
					key = result.key;

					commonModal('Mail authentication', '<p>Sent a authentication code to your mail.</p>', 80);
				},
				error: function(e) {
					console.log(e);
				}
			});
		}); */



		// 메일 인증 코드 성공
		$('#vCode').on('change', function(e) {
			console.log($('#vCode').val().trim());
			if (key == sha256($('#vCode').val().trim())) {

				console.log("verify OK");
				verify = true;
				$('#verify_txt').show();
				$('#reg_mb_email').css('background-color', '#ccc').prop('readonly', true);;

			} else {
				dialogModal("인증 실패","이메일로 전송된 인증코드를 다시 확인 후 입력해주세요.", "failed");
			}
		});

		// 핀번호 (오직 숫자만)
		document.getElementById('reg_tr_password').oninput = function() {
			// if empty
			if (!this.value) return;

			// if non numeric
			let isNum = this.value[this.value.length - 1].match(/[0-9]/g);
			if (!isNum) this.value = this.value.substring(0, this.value.length - 1);

			chkPwd_2($('#reg_tr_password').val(), $('#reg_tr_password_re').val());
		}

		document.getElementById('reg_tr_password_re').oninput = function() {
			// if empty
			if (!this.value) return;

			// if non numeric
			let isNum = this.value[this.value.length - 1].match(/[0-9]/g);
			if (!isNum) this.value = this.value.substring(0, this.value.length - 1);

			chkPwd_2($('#reg_tr_password').val(), $('#reg_tr_password_re').val());
		}

		check_id = 0;
		check_wallet = 0;
		check_email = 0;
		wallet = "";

		// 아이디 중복 체크
		$('#id_check').click(function() {
			

			var registerId = $('#reg_mb_id').val();

			var idReg = /^[a-z]+[a-z0-9]{5,19}$/g;
			if( !idReg.test( registerId ) ) {
				dialogModal("아이디 확인","아이디는 영문자로 시작하는 6~20자 영문자 또는 숫자이어야 합니다.", "failed");
				return;
			}

			if (registerId.length < 5) {
				dialogModal("아이디 확인", "아이디는 최소 5글자 이상만 사용 가능합니다.", "failed");
			} else {
				$.ajax({
					type: "POST",
					dataType: "json",
					url: "/bbs/register_check_id.php",
					data: {
						"registerId": registerId,
						check: "id"
					},
					success: function(res) {
						if (res.code == '000') {
							check_id = 0;
							dialogModal("아이디 확인", res.response, 'failed');
						} else {
							check_id = 1;
							dialogModal("아이디 확인", '해당아이디는 사용가능합니다.', 'success');
						}
					}
				});
			}
		});

		$("#reg_mb_id").bind("keyup", function() {
			re = /[~!@\#$%^&*\()\-=+_']/gi;
			var temp = $("#reg_mb_id").val();
			if (re.test(temp)) { //특수문자가 포함되면 삭제하여 값으로 다시셋팅
				$("#reg_mb_id").val(temp.replace(re, ""));
			}
			check_id = 0;
		});

		/*이용약관동의*/
		/* $('.agreement_ly').click(function() {
			if ($("#agree").is(":checked") == true) {
				$('#agree').prop("checked", false);
				$('.agreement_ly > span').css('text-decoration', 'none');
			} else {
				$('#agree').prop("checked", true);
				$('.agreement_ly > span').css('text-decoration', 'underline');
			}
		}); */


		$('#reg_mb_password').on('keyup', function(e) {
			chkPwd_1($('#reg_mb_password').val(), $('#reg_mb_password_re').val());
		});
		$('#reg_mb_password_re').on('keyup', function(e) {
			chkPwd_1($('#reg_mb_password').val(), $('#reg_mb_password_re').val());
		});

		/* 
		지갑주소 입력
		$('#wallet_addr_check').click(function() {
			var wallet_addr_len = $('#wallet_addr').val().length;
			console.log(wallet_addr_len);
			if (wallet_addr_len <= 40) {
				dialogModal("wallet check", "Please check wallet address again ", 'failed');
			} else {

				$.ajax({
					type: "POST",
					dataType: "json",
					url: "/bbs/register_check_id.php",
					data: {
						"registerId": $('#wallet_addr').val(),
						check: "wallet"
					},
					success: function(res) {

						if (res.code == '000') {
							check_wallet = 0;
							wallet = "";
							dialogModal("WALLET CHECK", res.response, 'failed');
						} else {
							check_wallet = 1;
							wallet = res.wallet;
							dialogModal("WALLET CHECK", res.response, 'success');
						}
					}
				});


			}
		}); */

	});

	/* 패스워드 확인*/
	function chkPwd_1(str, str2) {
		var pw = str;
		var pw_rule = 0;
		var num = pw.search(/[0-9]/g);
		var eng = pw.search(/[a-z][A-Z]/ig);
		//var eng_large = pw.search(/[A-Z]/ig);
		var spe = pw.search(/[`~!@@#$%^&*|₩₩₩'₩";:₩/?]/gi);

		var pattern = /^((?=.*[a-zA-Z])|(?=.*[!@#$%^&*=+]))(?=.*[0-9]).{4,8}/;

		if (pw.length < 4) {
			$("#pm_1").attr('class', 'x_li');
		} else {
			$("#pm_1").attr('class', 'o_li');
			pw_rule += 1;
		}

		// if(eng < 0 || num < 0){
		// 	$("#pm_3").attr('class','x_li');
		// }else{
		// 	$("#pm_3").attr('class','o_li');
		// 	pw_rule += 1;
		// }

		if (!pattern.test(pw)) {
			$("#pm_3").attr('class', 'x_li');
		} else {
			$("#pm_3").attr('class', 'o_li');
			pw_rule += 1;
		}


		// if(spe < 0 ){
		// 	$("#pm_3").attr('class','x_li');
		// }else{
		// 	$("#pm_3").attr('class','o_li');
		// 	pw_rule += 1;
		// }


		if (pw_rule == 2 && str == str2) {
			$("#pm_5").attr('class', 'o_li');
			pw_rule += 1;
		} else {
			$("#pm_5").attr('class', 'x_li');
		}

		if (pw_rule == 3) {
			return true;
		} else {
			return false;
		}
	}

	function chkPwd_2(str, str2) {
		var pw_rule = 0;

		if (str.length < 6) {
			$("#pt_1").attr('class', 'x_li');
		} else {
			$("#pt_1").attr('class', 'o_li');
			pw_rule += 1;
		}

		if (str == str2) {
			$("#pt_2").attr('class', 'o_li');
			pw_rule += 1;
		} else {
			$("#pt_2").attr('class', 'x_li');
		}

		if (isNaN(str) && isNaN(str2)) {
			$("#pt_3").attr('class', 'x_li');
		} else {
			$("#pt_3").attr('class', 'o_li');
			pw_rule += 1;
		}

		if (pw_rule >= 3) {
			return true;
		} else {
			return false;
		}
	}


	/*추천인, 센터멤버 등록*/
	function getUser(etarget, type) {
		var target = etarget;
		if (type == 1) {
			var target_type = "#referral";
		}else if(type == 2) {
			var target_type = "#center";
		}else {
			var target_type = "#director";
		}
		console.log(target + ' === ' + type);

		$.ajax({
			type: 'POST',
			url: '/util/ajax.recommend.user.php',
			data: {
				mb_id: $(target).val(),
				type: type
			},
			success: function(data) {
				var list = JSON.parse(data);

				if (list.length > 0) {
					$(target_type).modal('show');
					var vHtml = $('<div>');

					$.each(list, function(index, obj) {
						// vHtml.append($("<div>").addClass('user').html(obj.mb_id));
						
						if(type == 2){
							if(obj.mb_level > 0){
								vHtml.append($("<div style='text-indent:-999px'>").addClass('user').html(obj.mb_id));
								vHtml.append($("<label>").addClass('mb_nick').html(obj.mb_nick));
							}else{
								vHtml.append($("<div style='color:red;text-indent:-999px'>").addClass('non_user').html(obj.mb_id));
								vHtml.append($("<label style='color:red'>").addClass('mb_nick').html(obj.mb_nick));
							}
						}else{
							if(obj.mb_level >= 0){
								vHtml.append($("<div>").addClass('user').html(obj.mb_id));
							}else{
								vHtml.append($("<div style='color:red;>").addClass('non_user').html(obj.mb_id));
							}
						}
						/* 
						if (obj.mb_level > 0) {
							

							if(type == 2){
								vHtml.append($("<label>").addClass('mb_nick').html(obj.mb_nick));
							}

						} else {
							if(type == 2){
								
							}else{
								vHtml.append($("<div style='color:red;>").addClass('non_user').html(obj.mb_id));
							}
						} */


					});
			
					$(target_type + ' .modal-body').html(vHtml.html());
					first_select();


					/* 첫번째 선택되어있게 */
					function first_select() {
						$(target_type + ' .modal-body .user:nth-child(1)').addClass('selected');

						if(type == 2){
							$('#reg_mb_center_nick').val($(target_type + ' .modal-body .user.selected').html())
							$(target).val($(target_type + ' .modal-body .user.selected + .mb_nick').html());
						}else{
							$(target).val($(target_type + ' .modal-body .user.selected').html());
						}
					}


					$(target_type + ' .modal-body .user').click(function() {
						// console.log('user click');
						$(target_type + ' .modal-body .user').removeClass('selected');
						$(target + ' .modal-body .user').removeClass('selected');
						$(this).addClass('selected');
					});


					$(target_type + ' .modal-footer #btnSave').click(function() {

						if(type == 2){
							$('#reg_mb_center_nick').val($(target_type + ' .modal-body .user.selected').html());
							$(target).val($(target_type + ' .modal-body .user.selected + .mb_nick').html());
							center_search = true;
						}else{
							$(target).val($(target_type + ' .modal-body .user.selected').html());
							recommend_search = true;
							$('#reg_mb_center').val($(target_type + ' .modal-body .user.selected').html());
						}
						$(target).attr("readonly",true);
						$(target_type).modal('hide');
					});

				} else {

					dialogModal('처리 결과', '해당되는 회원이 없습니다.', 'failed');
				}
			}
		});

	} ///*추천인등록*/



	// submit 최종 폼체크
	function fregisterform_submit() {
		var f = $('#fregisterform')[0];
		//console.log(recommend_search);
		/*
		if(key != sha256($('#vCode').val())){
		 	commonModal('Do not match','<p>Please enter the correct code</p>',80);
		 	return false;
		}
		*/

		/* 국가선택 검사*/
		/* if($('#nation_number').val() == "country"){
			commonModal('country check', '<strong>please select country.</strong>', 80);
			return false;
		} */

		/* 이사멤버 검사
		if (f.mb_director.value == '' || f.mb_director.value == 'undefined') {
			commonModal('recommend check', '<strong>please check recommend search Button and choose recommend.</strong>', 80);
			return false;
		}
		*/

		/* console.log(`센터검색 : ${center_search}\n센터: ${f.mb_center.value}`); */
		

		//추천인 검사
		if (f.mb_recommend.value == '' || f.mb_recommend.value == 'undefined') {
			dialogModal('추천인 정보 확인', "<strong>추천인 아이디를 검색하여 목록에서 선택해주세요.</strong>", 'warning');
			return false;
		}
		if (!recommend_search) {
			dialogModal('추천인 정보 확인', "<strong>추천인 아이디를 검색하여 목록에서 선택해주세요.</strong>", 'warning');
			return false;
		}


		//센터멤버 검사
		/* if (f.mb_center.value == '' || f.mb_center.value == 'undefined') {
			dialogModal('센터정보 확인', "<strong>센터명 또는 센터 아이디를 검색하여 목록에서 선택해주세요.</strong>", 'warring');
			return false;
		}
		if (!center_search) {
			commonModal('센터정보 확인', '<strong>센터정보를 검색하여 선택해 주세요.</strong>', 80);
			return false;
		} */
		
		//추천인이 본인인지 확인
		if (f.mb_id.value == f.mb_recommend.value) {
			commonModal('조직 관계 입력 확인', '<strong> 자신을 추천인으로 등록할수없습니다. </strong>', 80);
			f.mb_recommend.focus();
			return false;
		}

		// 이름
		if (f.mb_name.value == '' || f.mb_name.value == 'undefined') {
			commonModal('이름입력확인', '<strong>이름을 확인해주세요.</strong>', 80);
			return false;
		}
		
		//아이디 중복체크
		if (check_id == 0) {
			commonModal('ID 중복확인', '<strong>아이디 중복확인을 해주세요. </strong>', 80);
			return false;
		}

		// 연락처
		if (f.mb_hp.value == '' || f.mb_hp.value == 'undefined') {
			commonModal('휴대폰번호확인', '<strong>휴대폰 번호가 잘못되거나 누락되었습니다. </strong>', 80);
			return false;
		}

		// 패스워드
		if (!chkPwd_1($('#reg_mb_password').val(), $('#reg_mb_password_re').val())) {
			commonModal('비밀번호 규칙 확인', '<strong> 로그인 패스워드를 확인해주세요.</strong>', 80);
			return false;
		}

		// 핀코드
		if (!chkPwd_2($('#reg_tr_password').val(), $('#reg_tr_password_re').val())) {
			commonModal('출금비밀번호(핀코드) 규칙 확인', '<strong> 출금비밀번호(핀코드)가 일치하지 않습니다.</strong>', 80);
			return false;
		}

		/*이용약관 체크*/
		for (var i = 0; i < $("input[name=term_required]:checkbox").length; i++) {
			if ($("input[name=term_required]:checkbox")[i].checked == false) {
				dialogModal('이용약관 동의', '이용약관과 개인정보 수집처리방침에 동의해주세요', 'warning');
				return false;
			}
		}

		// 메일인증 체크
		$.ajax({
			type: "POST",
			url: "/mail/check_mail_for_register.php",
			cache: false,
			async: false,
			dataType: "json",
			data: {
				user_email: $('#reg_mb_email').val()
			},
			success: function(res) {
				if (res.result == "OK") {
					mail_check = 1;
					f.submit();
				} else {
					mail_check = 0;
					dialogModal("Email Auth", res.res, 'failed');

				}

			},
			error: function(e) {
				console.log(e)
			}
		});
	}
</script>

<div class="v_center">

	<div class="enroll_wrap">
		<form id="fregisterform" name="fregisterform" action="/bbs/register_form_update.php" method="post" enctype="multipart/form-data" autocomplete="off">

			<!-- 국가선택시
			<div>
				<select id="nation_number" name="nation_number" required >
					<option value="country" data-i18n="signUp.국가를 선택해주세요" >Select Country</option>
					<option value="1">001 - USA</option>
					<option value="81">081 - Japan</option>
					<option value="82">082 - Korea</option>
					<option value="84">084 - Vietnam</option>
					<option value="86">086 - China</option>
					<option value="62">062 - Indonesia</option>
					<option value="63">063 - Philippines</option>
					<option value="66">066 - Thailand</option>
				</select>
			</div> -->

			<!-- 추천인 정보 -->
			<p class="check_appear_title mt10"><span>추천인정보</span></p>
			<section class='referzone'>
				<div class="btn_input_wrap">
					<input type="text" name="mb_recommend" id="reg_mb_recommend" value="<?= $mb_recommend ?>" required placeholder="추천인 아이디" />
					<div class='in_btn_ly2'>
						<button type='button' class="btn_round check " onclick="getUser('#reg_mb_recommend',1);" ><span>검색</span></button>
					</div>
					
				</div>
				
			</section>

			<!-- 센터 정보 -->
			<!-- <p class="check_appear_title mt20"><span >센터정보</span></p>
				<section class='referzone'>
					<div class="btn_input_wrap">
						<input type="hidden" name="mb_center_nick" id="reg_mb_center_nick" value=""  required  />
						<input type="text" name="mb_center" id="reg_mb_center" value="<? if($mb_center){echo $mb_center;}?>" placeholder="센터명 또는 센터아이디" required  />

						<div class='in_btn_ly2'>
							<button type='button' class="btn_round check " onclick="getUser('#reg_mb_center',2);"
							><span>검색</span></button>
						</div>
					</div>
				</section>
				<i style="color:rgba(255,255,255,0.4)">※센터정보 검색후 선택해주세요.</i> -->
				<em class="info_text">※추천회원 검색후 선택해주세요.</em>
			<!-- <p class="check_appear_title mt40"><span data-i18n='signUp.일반정보'>General Information</span></p> -->
			<p class="check_appear_title mt30"><span>개인 정보 & 인증</span></p>
			<div>
				<input type="text" minlength="5" maxlength="20" name="mb_name" id="reg_mb_name" required placeholder="이름"  />
				<!-- <div class='in_btn_ly'><input type="button" id='name_check' class='btn_round check' value="중복확인"></div> -->

				<input type="text" minlength="5" maxlength="20" name="mb_id" class='cabinet' id="reg_mb_id" required placeholder="아이디"/>
				<span class='cabinet_inner' style=''>※영문+숫자조합 6자리 이상 입력해주세요</span>
				<div class='in_btn_ly'><input type="button" id='id_check' class='btn_round check' value="중복확인"></div>

				<input type="email"  id="reg_mb_email" name="mb_email" class='cabinet' required placeholder="이메일 주소" />
				<span class='cabinet_inner' style=''>※수신가능한 이메일주소를 직접 입력해주세요</span>
				<div class='in_btn_ly'><input type="button" id='EmailChcek' class='btn_round check' value="이메일 전송"></div>
				
				<input type="text" name="mb_hp"  id="reg_mb_hp" class='cabinet'  pattern="[0-9]*" required  placeholder="휴대폰번호"/>
				<span class='cabinet_inner' style=''>※'-'를 제외한 숫자만 입력해주세요</span>
				<!-- <label class='prev_icon'><i class="ri-smartphone-line"></i></label> -->
				
			</div>

			

			<ul class="pw_ul mt20">
				<li>
					<input type="password" name="mb_password" id="reg_mb_password" minlength="4" placeholder="로그인 비밀번호" />
					<input type="password" name="mb_password_re" id="reg_mb_password_re" minlength="4" placeholder="로그인 비밀번호 확인" />

					<strong><span class='mb10' style='display:block;font-size:13px;'>비밀번호 설정 조건</span></strong>
					<ul>
						<li class="x_li" id="pm_1" >4자 이상 8자 이하</li>
						<li class="x_li" id="pm_3" >숫자+영문 혹은 특수문자</li>
						<li class="x_li" id="pm_5" >비밀번호 비교</li>
					</ul>
				</li>
				<li style='margin-left:5px'>
					<input type="password" minlength="6" maxlength="6" id="reg_tr_password" name="reg_tr_password" placeholder="출금비밀번호(핀코드)" />
					<input type="password" minlength="6" maxlength="6" id="reg_tr_password_re" name="reg_tr_password_re" placeholder="출금비밀번호(핀코드) 확인" />

					<strong><span class='mb10' style='display:block;font-size:13px;' >핀코드 설정 조건</span></strong>
					<ul>
						<li class="x_li" id="pt_1" >6 자리</li>
						<li class="x_li" id="pt_3" >숫자</li>
						<li class="x_li" id="pt_2" >핀코드 비교</li>
					</ul>
				</li>
			</ul>


			<!-- 폰인증
			<section id="personal">
				<div>
					<span style='display:block;margin-left:10px;' class='' data-i18n='signUp.핸드폰 번호'> Phone number</span>
					<input type="text" name="mb_hp"  id="reg_mb_hp"  pattern="[09]*" placeholder="Phone number" value='' data-i18n='[placeholder]signUp.핸드폰 번호'/>
					<label class='phone_num'><i class="ri-smartphone-line"></i></label>
				</div>
					<?if($phone_auth > 1){?>
					<div class="clear_fix ecode_div">
					<div class="verify_phone">
						<input type="text" placeholder="Enter Phone Authtication Code"/>
						<a href="javascript:void(0)" class=""  id="sendSms">
							<img src="<?= G5_THEME_URL ?>/_images/email_send_icon.gif" alt="이메일코드">
							Enter Phone Authtication Code
						</a>
						</div>
					</div>
					<?}?>
			</section>
			-->

			<!--
			<hr>
			<div class="agreement_btn"> <button type="button" class="agreeement_show btn"><span data-i18n='register.회원가입 약관보기'>Read Terms and Conditions</span></button></div>
			-->

			<p class="check_appear_title mt40"><span >회원가입 약관동의 </span></p>
			<div class="mt20">
				<div class="term_space">
					<input type="checkbox" id="service_checkbox" class="term_none" name="term_required" >
					<label for="service_checkbox">
						<span><?= $service_term['wr_subject'] ?> 및 서약서 동의 (필수)</span>
						<a id="service" href="javascript:collapse('#service');" style="width:25px;height:25px;position:absolute;right:0;"><i class="fas fa-angle-down"></i></a>
					</label>
				</div>
				
				<textarea id="service_term" class="term_textarea term_none"><?= $service_term['wr_content'] ?></textarea>

				<div class="term_space">
					<input type="checkbox" id="private_checkbox" class="term_none" name="mb_sms" value="1">
					<label for="private_checkbox">
						<span><?= $private_term['wr_subject'] ?> 동의 (선택)</span>
						<a id="private" href="javascript:collapse('#private');" style="width:25px;height:25px;position:absolute;right:0;"><i class="fas fa-angle-down"></i></a>
					</label>
				</div>
				<textarea id="private_term" class="term_textarea term_none"><?= $private_term['wr_content'] ?></textarea>
			</div>
			

			<div class="btn2_wrap mb40" style='width:100%;height:60px'>
				<input class="btn btn_double enroll_cancel_pop_open btn_cancle pop_open" type="button" value="취소">
				<input class="btn btn_double btn_primary" type="button" onclick="fregisterform_submit();" value="신규 회원 등록하기">
			</div>


		</form>
	</div>

</div>

<div class='footer' style="position:relative;bottom:25px;">
		<p class='company mb10'> <?=CONFIG_SUB_TITLE?> <br>이메일 : <?=$config['cf_admin_email']?>
		<p class='copyright'>Copyright ⓒ 2023. <?=CONFIG_TITLE?> Co. ALL right reserved.</p>
	</div>
</section>

<div class="gnb_dim"></div>



<script>
	$(function() {
		$(".top_title h3").html("<span style='font-size:16px;margin-left:20px'>신규 회원등록</span>");

		$("#reg_mb_email").on("click", function() {
			if(check_id != 1){
				dialogModal('ID 중복확인', '<strong>아이디 중복확인을 해주세요. </strong>', 'warning');
				return false;
			}
		});
		
	});

	
	function collapse(id) {
		if ($(id + "_term").css("display") == "none") {
			$(id + "_term").css("display", "block");
			$(id + "_term").animate({
				height: "150px"
			}, 100, function() {
				$(id + ' .svg-inline--fa').css('transform', "rotate(180deg)");
			});
		} else {
			$(id + "_term").animate({
				height: "0px"
			}, 100, function() {
				$(id + "_term").css("display", "none");
				$(id + ' .svg-inline--fa').css('transform', "rotate(360deg)");
			});
		}
	}


</script>
