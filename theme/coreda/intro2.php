<style>

.container {
	margin:0;
	padding:0;
	width:100%;
	display:block; 
	height:100vh; 
	background:url('<?=G5_THEME_URL?>/img/launcher.png') no-repeat 50% 50%;
	background-size:cover;
}

.adm_title{background:#f9a62e;color:white;padding:5px 30px;font-size:1.2em; border-radius:25px;margin-bottom:20px;display:inline-block}

#btnDiv {
  display: none;
  text-align: center;
  position:absolute;
  bottom:10%;
  width:100%;
  z-index:1000;
}
.btn_ly{width:85%;
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
}

@media (min-width: 767px) {
	body{background:#f5f5f5}
	.container{width:767px;margin:0 auto;}
	#btnDiv{width:767px;}
}

</style>



<script >
	var myVar;
	var maintenance = "<?=$maintenance?>";

	function myFunction() {
	  move()
	}

	function showPage() {
	  document.getElementById("myBar").style.display = "none";
	  document.getElementById("btnDiv").style.display = "block";
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


<body onload="myFunction();" style="margin:0;">

<div class="container">
	<div id="myBar"></div>
	
	<div id="btnDiv" class="animate-bottom">
		<div class='btn_ly'>
	  		<a href="/bbs/login_pw.php" class="btn btn_wd btn_primary login_btn">LOG IN</a>
	  		<a href="/bbs/register_form.php" class="btn btn_wd btn_secondary signup_btn">SIGN UP</a>
		</div>
	</div>
</div>
	
