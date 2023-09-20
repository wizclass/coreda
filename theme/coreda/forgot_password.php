<?php include '_include/head.php'; ?>
<?php include '_include/gnb.php'; ?>

	<section id="wrapper">
		<div class="v_center">
			<div class="login_wrap forgot_pw_wrap">
				<div class="logo_login_div">
					<img src="_images/login_logo.gif" alt="v7 wallet logo">
				</div>

				<form action="">
					<div>
						<input type="text" placeholder="유저네임"/>
					</div>
					<div>
						<input type="text" placeholder="이메일 주소"/>
					</div>
					<div class="clear_fix ecode_div">
						<input type="text" placeholder="이메일 인증번호 입력"/>
						<a href="javascript:void(0)" class="code_send pop_open">
							<img src="_images/email_send_icon.gif" alt="이메일코드">
							인증번호 요청
						</a>
					</div>
					<div>
						<a href="javascript:void(0)" class="btn_basic_block btn_navy pop_open fg_pw_ok_pop_open">비밀번호 재설정</a>
					</div>
				</form>
			</div>

		</div>


	<?php include '_include/popup.php'; ?>
	</section>
	
		<div class="gnb_dim"></div>


<script>
		$(function() {
			$(".top_title h3").html("<img src='_images/top_support.png' alt='아이콘'> 비밀번호 재설정");
			
		});
	</script>


</body>
</html>





