
<style>

canvas{ display: block; vertical-align: bottom; }
#particles-js{ position:absolute; width: 100%; height: 100%; top:0;
	background-color: transparent; background-image: url(""); background-repeat: no-repeat; background-size: cover; background-position: 50% 50%; }

#particles-js{visibility: hidden}

.container {
	margin:0;
	padding:30px;
	width:100%;
	display:block;
	height:100vh;
	background:linear-gradient(0deg, crimson, #7f0417, #7f0417,#7f0417,crimson );
	
	_background:linear-gradient(to bottom, #7f0417, #9198e5),url('<?=G5_THEME_URL?>/_images/launcher_loading.gif') no-repeat 50% 50%;
	_background-image:url('<?=G5_THEME_URL?>/img/launcher_loading.gif') no-repeat 50% 35%;
	_background-size:38%;
}

.background{
	width:100%;
	height:100%;
	display:block;
	background:url('<?=G5_THEME_URL?>/img/launcher_loading.gif') no-repeat center;
	background-size:38%;
}

.adm_title{background:#f9a62e;color:white;padding:5px 30px;font-size:1.2em; border-radius:25px;margin-bottom:20px;display:inline-block}

#btnDiv {
  display: none;
  text-align: center;
  position:absolute;
  bottom:10%;
  width:Calc(100% - 60px);
  z-index:1000;
}
.btn_ly{
  text-align:center;
  margin:0 auto;}

#myProgress {
  width: 100%;
}

#myBar {
  width: 1%;
  height: 3px;
  background-color: #00b6d3;
}
.loading_title{
	margin: 0 auto;
	width:90%;
	padding-top:50px;
	text-align:center;
}

.login_btn{color:white !important;}

.animate-bottom {
  position: relative;
  -webkit-animation-name: animatebottom;
  -webkit-animation-duration: 1s;
  animation-name: animatebottom;
  animation-duration: 1s
}

@-webkit-keyframes animatebottom {
  from { bottom:-10%; opacity:0 }
  to { bottom:10%; opacity:1 }
}

@keyframes animatebottom {
  from{ bottom:-10%; opacity:0 }
  to{ bottom:10%; opacity:1 }
}



@media screen and (max-width: 1600px) {

}

@media screen and (max-width: 1200px) {

}

@media screen and (max-width: 1024px) {

}
@media screen and (max-width: 993px) {

}

@media screen and (max-width: 767px){

}

@media screen and (max-width: 736px) {

}


@media (max-width: 414px) {

}

@media (max-width: 650px) {

}

@media (max-width: 768px) {
	.container{width:100%}
}

@media (min-width: 767px) {
	body{background:#f5f5f5}
	.container{width:767px;margin:0 auto;background-size:150px;}
	#btnDiv{width:Calc(767px - 80px);}
}
</style>




<body onload="myFunction();" style="margin:0;">
<div id="myBar"></div>

<div class="container">
	<div class='background'> 
	<div class='loading_title'><img src='<?=G5_THEME_URL?>/img/launcher_title.png'/></div>
	<div id="btnDiv" class="animate-bottom">
		<div class='btn_ly'>
	  		<a href="/bbs/login_pw.php" class="btn btn_wd btn_primary login_btn">LOG IN</a>
	  		<a href="/bbs/register_form.php" class="btn btn_wd btn_secondary signup_btn">SIGN UP</a>
		</div>
	</div>
	</div>
</div>

<div id="particles-js"></div>




<script>
	var myVar;
	var maintenance = "<?=$maintenance?>";

	function myFunction() {
	  move()
	}

	function showPage() {
	  document.getElementById("myBar").style.display = "none";
	  document.getElementById("btnDiv").style.display = "block";
	  document.getElementById("particles-js").style.visibility = "initial";
	}

	function move() {
	  var elem = document.getElementById("myBar");
	  var width = 1;
	  var id = setInterval(frame, 5);
	  function frame() {
		if (width >= 100) {
		  clearInterval(id);
		  //showPage();

		  if(maintenance == 'N'){
			showPage();
		  }
		} else {
		  width++;
		  elem.style.width = width + '%';
		}
	  }
	}
</script>
<script src="http://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
<script src="<?=G5_THEME_URL?>/_common/js/particle.js"></script>
