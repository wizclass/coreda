

<link rel="stylesheet" href="<?=G5_THEME_URL?>/css/scss/page/intro.css">

<script >
	var myVar;
	var maintenance = "<?=$maintenance?>";

	$(document).ready(function(){
	  move();
	});
	

	function temp_block(){
		commonModal("Notice",' 방문을 환영합니다.<br />사전 가입이 마감되었습니다.<br />가입하신 회원은 로그인 해주세요.<br /><br />Welcome to One-EtherNet.<br />Pre-subscription is closed.<br />If you are a registered member,<br />please log in.',220);
	}

	function showPage() {
	  document.getElementById("myBar").style.display = "none";
	  document.getElementById("btnDiv").style.display = "block";
	}

	function move() {
	  var elem = document.getElementById("myBar");
	  var width = 50;
	  var id = setInterval(frame, 1);
	  function frame() {
		if (width >= 100) {
		  clearInterval(id);
		  //showPage();

		  if(maintenance == 'N'){
			showPage();
		  }
		} else {
		  width = width + 10;
		  elem.style.width = width + '%';
		}
	  }
	}

	function auto_login(){

		if(typeof(web3) == 'undefined'){
    	window.location.href = "/bbs/login_pw.php";
  	}

		window.ethereum.enable().then((err) => {

    web3.eth.getAccounts((err, accounts) => {
    	if(accounts){
				$.ajax({
					url: "/bbs/login_check.php",
					async: false,
					type: "POST",
					dataType: "json",
					data:{
						trust : "trust",
						ether : accounts
					},
					success: function(res){
						if(res.result == "OK"){
							window.location.href = "/page.php?id=structure";

						}

						/* if(res.result == "FAIL"){
							alert("EHTEREUM ADDRESS is not registered. Please Sign In or Sign Up.");
							window.location.href = "/bbs/login_pw.php";
						} */

						if(res.result == "ERROR"){
							alert("ERROR");
						}


					}
				});
			}
    })

  });
	}


</script>

<html>
<script src="<?php echo G5_JS_URL ?>/common.js?ver=<?php echo G5_JS_VER; ?>"></script>
<body style="margin:0;">
	<div class="container">
		<div id="myBar"></div>
		<div id="btnDiv" class="animate-bottom">
			<div class='btn_ly'>
				<?
				if(Multi_languge_USE){
					include_once(G5_THEME_PATH.'/_include/lang.php');
				}
				?>
					<a href="/bbs/login_pw.php" class="btn btn_wd btn_secondary login_btn">LOG IN</a>
					<!-- <a href="javascript:auto_login()" class="btn btn_wd btn_primary login_btn">LOG IN</a> -->
						<a href="/bbs/register_form.php" class="btn btn_wd btn_primary signup_btn">SIGN UP</a>
					<!-- <a href="javascript:temp_block()" class="btn btn_wd btn_secondary signup_btn">SIGN UP</a> -->
			</div>
		</div>

		<div class='intro_title'>
			<p class='company'> <?=CONFIG_SUB_TITLE?> <br>이메일 : <?=$config['cf_admin_email']?></p>
			<p class='copyright'>Copyright ⓒ 2023. <?=CONFIG_TITLE?> Co. ALL right reserved.</p>
		</div>
	</div>
</body>
</html>
