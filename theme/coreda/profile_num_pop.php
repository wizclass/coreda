<?php include '_include/head.php'; ?>
<?php include '_include/gnb.php'; ?>

			<section class="profile_wrap">
			
				<div class="prof_1st">
					<h5>개인정보</h5>
					<div>유저네임: Coolrunning</div>
					<hr>
					<div>
						이름: Chris Yang
						<p class="f_right">
							KYC <span class="font_red">실패</span>
<!--							KYC <span class="font_green">성공</span>-->
						</p>
					</div>
					<hr>
					<div class="clear_fix id_file_wrap">
						<span>신분증을 든 사진 업로드</span>
						<div class="filebox"> 
							<input class="upload-name" value="파일선택" disabled="disabled">
							<label for="ex_filename">파일업로드</label>
							<input type="file" id="ex_filename" class="upload-hidden">
						</div>
					</div>
					<p class="font_green">업로드 성공</p>
<!--					<p class="font_red">업로드 실패</p>-->
					<hr>
					<ul>
						<li>이메일: nicemail1202@yahoo.com <img src="_images/okay_icon.gif" alt="인증됨" style="width:15px;"></li>
						<li><input type="button" value="변경" class="email_pop_open"></li>
					</ul>
					<hr>
					<ul>
						<li>전화번호: +1 255 555 4135 <img src="_images/x_icon.gif" alt="인증안됨" style="width:15px;"></li>
						<li><input type="button" value="변경" class="num_pop_open pop_open"></li>
					</ul>
					<hr>
					<ul>
						<li>바이너리 팩 (B 팩) 자동 재구매</li>
						<li>
							<div class="round_chkbox">
							  <input type="checkbox" id="b_chk" checked disabled>
							  <label for="b_chk"><span></span></label>
							</div>
						</li>
					</ul>
					<hr>
					<ul>
						<li>수당 팩 (Q 팩) 자동 재구매</li>
						<li>
							<div class="round_chkbox">
							  <input type="checkbox" id="q_chk" disabled>
							  <label for="q_chk"><span></span></label>
							</div>
						</li>
					</ul>
					
				</div>
				
				<div>
					<h5>보안설정</h5>
					<ul>
						<li>로그인 비밀번호 변경</li>
						<li><input type="button" value="변경" class="ch_pw_open"></li>
					</ul>
					<hr>
					<ul>
						<li>트랜젝션 비밀번호 변경</li>
						<li><input type="button" value="변경" class="ch_tpw_open"></li>
					</ul>
					<hr>
					<ul>
						<li>지문으로 지갑열기</li>
						<li>
							<div class="round_chkbox">
							  <input type="checkbox" id="f_chk">
							  <label for="f_chk"><span></span></label>
							</div>
						</li>
					</ul>
					<hr>
					<ul>
						<li>
							이중보안 (2-Factor)
						</li>
						<li>
							<div class="round_chkbox">
							  <input type="checkbox" id="o_chk">
							  <label for="o_chk"><span></span></label>
							</div>
						</li>
					</ul>
					<div class="opt_wrap">
						<small>이중보안을 설정하여 지갑을 외부로부터 보다 안전하게 지킬 수 있습니다.</small>
						<img src="_images/otp_qr.gif" alt="otp 이미지">
						<p>
							1. Authy App으로 QR 코드를 스캔합니다.<br>
							2. 화면에 나오는 6자리 숫자를 아래에 입력합니다.
						</p>
						<input type="text">
						<input type="button" value="코드 확인">
						앱이 없으시다면? <a href=" https://play.google.com/store/apps/details?id=com.authy.authy">여기서 다운 받으세요.</a>
					</div>
				</div>
				
				<div>
					<h5>추천인 정보</h5>
					<ul>
						<li>나의 추천 링크</li>
						<li><input type="button" value="복사"></li>
					</ul>
					<hr>
					<ul>
						<li>링크 QR 코드:</li>
						<li><input type="button" value="공유"></li>
					</ul>
					<img src="_images/qr_img.gif" alt="링크 qr">
				</div>
				
			</section>


<style>
	.dim{display:block;}
	.input_pop_css{display:block;}
</style>

			<!-- 전화번호 변경 -->
			<div class="pop_wrap num_pop_wrap input_pop_css">
				<form action="">
					<label for="">사용중인 전화번호</label>
					<div class="num_pop_div clear_fix">
						<input type="input">
						<input type="input">
					</div>
					<div>
						<label for="">보안코드 입력</label>
						<p class="code_btn go_num1"><img src="_images/email_send_icon.gif" alt="이미지">코드요청</p>
					</div>
					<input type="text" style="margin-bottom:25px;">
					<div class="btn2_btm_wrap">
						<input type="button" value="취소" class="cancel pop_close" >
						<input type="button" value="다음으로" class="save go_num2">
					</div>
				</form>
			</div>

			<div class="pop_wrap num1_pop_wrap notice_img_pop">
				<p class="pop_title">전화번호 인증</p>	
				<div>
					<img src="<?=G5_URL?>/img/check_basics.png" alt="체크 이미지">
					인증번호가 전송되었습니다.
				</div>
				<div class="pop_close_wrap">
					<a href="javascript:void(0);" class="num1_pop_close">Close</a>
				</div>
			</div>	

			<div class="pop_wrap num2_pop_wrap notice_img_pop">
				<p class="pop_title">전화번호 변경</p>	
				<div>
					<img src="<?=G5_URL?>/img/check_basics.png" alt="체크 이미지">
					변경이 성공적으로 완료되었습니다.
				</div>
				<div class="pop_close_wrap">
					<a href="javascript:void(0);" class="pop_close">Close</a>
				</div>
			</div>

		




		<div class="dim"></div>

		<div class="gnb_dim"></div>
			
	</section>



	<script>
		$(function() {
			$(".top_title h3").html("<img src='_images/top_setting.png' alt='아이콘'> 개인정보와 보안 설정");
			

			//이메일 변경
			$('.num_pop_open').click(function(){
				$('.num_pop_wrap').css("display","block");
			});
			$('.go_num1').click(function(){
				$('.num_pop_wrap').css("display","none");
				$('.num1_pop_wrap').css("display","block");
			});
			$('.num1_pop_close').click(function(){
				$('.num_pop_wrap').css("display","block");
				$('.num1_pop_wrap').css("display","none");
			});
			$('.go_num2').click(function(){
				$('.num_pop_wrap').css("display","none");
				$('.num2_pop_wrap').css("display","block");
			});
			


			 




		});
	</script>



</body></html>
