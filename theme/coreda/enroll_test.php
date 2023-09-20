<?php include '_include/head.php'; ?>
<?php include '_include/gnb.php'; ?>
<script type="text/javascript">
	var captcha;
	var key;
$(function(){
			$('#nation_number').on('change',function(e){
				console.log($(this));
				if(['1','81','82'].indexOf($(this).val()) !== -1 ){
					// sms 인증 사용
					$('.verify_phone').show();
					
				}else{
					$('.verify_phone').hide();

				}
			});
			$('#sendSms').on('click', function(e){
				if(!$('#reg_mb_hp').val()){
					commonModal('Mobile authentication','<p>Please enter your Mobile Number</p>',80);
					return;
				}
				var reg_mb_hp = + ($('#reg_mb_hp').val().replace(/-/gi,''));
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
						commonModal('SMS authentication','<p>Sent a authentication code to your Mobile.</p>',80);
					},
					error: function(e){
						console.log(e);
					}
				});
			});
			$('#sendMail').on('click', function(e){
				if(!$('#reg_mb_email').val()){
					//commonModal('Mail authentication','<p>Please enter your mail</p>',80);
					alert("Please enter your mail!!!!");
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

						click_code_send();
						alert("Sent a authentication code to your mail!!!");

					//commonModal('Mail authentication','<p>Sent a authentication code to your mail.</p>',80);
					},
					error: function(e){
						console.log(e);
					}
				});
			});
});

	// submit 최종 폼체크
	function fregisterform_submit(){
		var f = $('#fregisterform')[0];
		if(!$('#agree').prop('checked')){
			commonModal('check the box!!','<strong>check the box!!</strong>',80);
			alert('<strong>check the box!!</strong>');
			return false;
		}
		// if(key != sha256($('#vCode').val())){
		// 	commonModal('Do not match','<p>Please enter the correct code</p>',80);
		// 	return false;
		// }
		if (f.mb_password.value != f.mb_password_re.value) {
			commonModal('check password','<strong>비밀번호가 같지 않습니다.</strong>',80);
			alert('<strong>비밀번호가 같지 않습니다.</strong>');
			f.mb_password_re.focus();
			return false;
		}
		if (f.mb_password.value.length > 0) {
			if (f.mb_password_re.value.length < 3) {
				commonModal('check password','<strong>비밀번호를 3글자 이상 입력하십시오.</strong>',80);
				alert('<strong>비밀번호를 3글자 이상 입력하십시오.</strong>');
				f.mb_password_re.focus();
				return false;
			}
		}
		if (typeof(f.mb_recommend) != "undefined" && f.mb_recommend.value) {
			if (f.mb_id.value == f.mb_recommend.value) {
				commonModal('check recommend','<strong>본인을 추천할 수 없습니다.</strong>',80);
				alert('<strong>본인을 추천할 수 없습니다.</strong>');
				f.mb_recommend.focus();
				return false;
			}
		}
		$('#fregisterform').submit();
//				return false;
	}
	function commonModal(title, htmlBody, bodyHeight){
		$('#commonModal').modal('show');
		$('#commonModal .modal-header .modal-title').html(title);
		$('#commonModal .modal-body').html(htmlBody);
		if(bodyHeight){
			$('#commonModal .modal-body').css('height',bodyHeight+'px');
		} 
		$('#closeModal').focus();
	}

		function click_code_send()
		{
			$('.email_pop_wrap').css("display", "block");
			$('.email_pop_wrap div').text("이메일 인증 번호가 전송되었습니다.");
		}
	</script>
<body>
	<section id="wrapper" class="bg_white">
		<div class="v_center">
			<div class="enroll_wrap">

				<form id="fregisterform" name="fregisterform" action="/bbs/register_form_update.php" method="post" enctype="multipart/form-data" autocomplete="off">
				<div>
					<div>
						<input type="text" minlength="4"  name="mb_id"  id="reg_mb_id"  placeholder="유저네임 (4~ 10자리, 특수기호 사용불가)"/>
					</div>
					<ul class="clear_fix pw_ul">
						<li>
							<input type="password" name="mb_password" id="reg_mb_password"  minlength="8" maxlength="20" placeholder="로그인 비밀번호"/>
							<input type="password" name="mb_password_re" id="reg_mb_password_re" minlength="8" maxlength="20" placeholder="비밀번호 확인"/>
							
							<strong>강도 높은 비밀번호 설정 조건</strong>
							<ul>
								<li class="o_li">8자 이상 20자 이하</li>
								<li class="x_li">영문 대문자와 소문자 조합</li>
								<li class="o_li">숫자</li>
								<li class="x_li">특수 기호</li>
							</ul>
						</li>
						<li>
							<input type="password" minlength="8" maxlength="20" placeholder="트랜잭션 비밀번호"/>
							<input type="password" minlength="8" maxlength="20" placeholder="트랜잭션 비밀번호 확인"/>
							
							<strong>강도 높은 비밀번호 설정 조건</strong>
							<ul>
								<li class="o_li">8자 이상 20자 이하</li>
								<li class="o_li">영문 대문자와 소문자 조합</li>
								<li class="o_li">숫자</li>
								<li class="o_li">특수 기호</li>
							</ul>
						</li>
					</ul>
				</div>
				
				
				<div class="check_appear">
					<p class="check_appear_title">개인 정보와 인증 (KYC 요령) <small class="f_right font_red kyc_pop_btn pop_open">KYC 요령</small></p>
					<input type="text" placeholder="이름 (신분증에 기록된 이름과 동일해야 함)"/>
					<input type="text" placeholder="성 (신분증에 기록된 이름과 동일해야 함)"/>
					<div class="clear_fix id_file_wrap">
						<span>신분증을 든 사진 업로드</span>
						<div class="filebox"> 
							<input class="upload-name" value="파일선택" disabled="disabled">
							<label for="ex_filename">파일업로드</label>
							<input type="file" id="ex_filename" class="upload-hidden">
						</div>
					</div>
<!--					<p class="text_right font_green mb20">업로드 성공</p>-->
 					<p class="text_right font_red mb20"></p> 
					
					<input type="email" name="mb_email" id="reg_mb_email"  placeholder="이메일 주소"/>
					<div class="clear_fix ecode_div">
						<input type="text" placeholder="이메일 승인 번호"/>
						<a href="javascript:void(0)" class="code_send" id="sendMail">
							<img src="_images/email_send_icon.gif" alt="이메일코드">
							이메일인증번호
						</a>
					</div>
					<div class="div46">
			<select id="nation_number" name="nation_number" required >
				<option value="country" data-i18n="register.select" >Select Country</option>
				<option value="1">001 - USA</option>
				<option value="61">061 - Australia</option>
				<option value="81">081 - Japan</option>
				<option value="82">082 - Korea</option>
				<option value="84">084 - Vietnam</option>
				<option value="86">086 - China</option>
			</select>
<!-- 						<input type="text" placeholder="거주 국가 선택"/> -->
						<input type="text" name="mb_hp"  id="reg_mb_hp"  pattern="[09]*" placeholder="전화번호"/>
					</div>
					<div class="clear_fix ecode_div">
					<div class="verify_phone">
						<input type="text" placeholder="문자 인증 번호"/>
						<a href="javascript:void(0)" class=""  id="sendSms">
							<img src="_images/email_send_icon.gif" alt="이메일코드">
							문자인증번호
						</a>
						</div>
					</div>
					<div class="btn_input_wrap mb10">
						<input type="text" name="mb_recommend" id="reg_mb_recommend"   placeholder="추천인 유저네임"/>
						<a href="javascript:void(0);" class="search_result_btn pop_open">추천인 검색</a>
					</div>
					<div class="btn_input_wrap mb10">
						<input type="text" pattern="[09]*" placeholder="센터번호"/>
						<a href="javascript:void(0);" class="search_result_btn pop_open">센터 검색</a>
					</div>
					
					<div class="mb20">
						<div class="checkbox_wrap"><input type="checkbox" id="agree"  class="checkbox"><label for="agree"></label></div>
						본인은 약관을 읽었으며 이에 동의합니다. 본인은 V7사업을 완전히 이해 하였으며 반품이나 반환이 불가능한 것을 알고 있습니다. 추천인과 후원인의 변경 또한 불가능한 것을 알고 있으며 이에 동의 합니다.
					</div>
					
<!-- 					<div style="height:100px; text-align: center; background:#eee;">
						캡챠영역
					</div> -->
					
					<div class="btn2_wrap">
						<!-- <input class="btn_basic mt20" type="button" value="취소" onClick="history.back(-1);">
						<input class="btn_basic mt20" type="button" value="신규 회원 등록하기" onClick="location.href='dashboard.php'"> -->
						
						<input class="btn_basic mt20 enroll_cancel_pop_open pop_open" type="button" value="취소">
						<input class="btn_basic mt20" type="button" onclick="fregisterform_submit();" value="신규 회원 등록하기">
					</div>
				</div>
					
				</form>
			</div>

		</div>

	</section>
	
	
	<div class="gnb_dim"></div>
	<?php include '_include/popup.php'; ?>

	<script>
		$(function() {
			$(".top_title h3").html("<img src='_images/top_enroll.png' alt='아이콘'> 신규 회원등록");
			
		});
	</script>

</body>
</html>





