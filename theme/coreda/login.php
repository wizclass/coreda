<? 
include_once(G5_THEME_PATH.'/_include/head.php');
include_once(G5_THEME_PATH.'/_include/lang.php');
?>

	<section id="wrapper" class="bg_white">
		<div class="v_center">
			<div class="login_wrap">
				<div class="logo_login_div">
					<img src="<?=G5_THEME_URL?>/_images/login_logo.gif" alt="v7 wallet logo">
				</div>

				<form action="">
					<div class="l_fp_div">
						<img src="<?=G5_THEME_URL?>/_images/login_fingerprint.png" alt="지문이미지">
					<p data-i18n='login.창닫기'>	지문으로 로그인</p>
						<p>센서를 터치하세요</p>
					</div>
					<div>
						<a href="/bbs/login_pw.php" class="font_deepblue">비밀번호로 로그인</a>
					</div>
					<div class="login_btn_bottom login_btn_bottom_index">
						<a href="<?=G5_THEME_URL?>/enroll.php" class="btn_basic_block btn_navy">신규 회원 등록하기</a>
					<!-- 	<a href="">지갑 복구하기</a> -->
						<a href="mailto:cs@v7wallet.com" class="support_a">Contact Support</a>
					</div>
				</form>
			</div>

		</div>
	</section>


<? include_once(G5_THEME_PATH.'/_include/tail.php'); ?>



