<?php
include_once('./_common.php');
include_once(G5_THEME_PATH.'/_include/head.php');
?>


<style>
  .container {
      position: absolute;
      left: 50%;
      top: 50%;
      transform: translate(-50%, -50%);
  }
  .refferal{background:#f1f1f1;width:60%;margin:20px auto;padding:10px;border-radius:15px;}
  .refferal span{display:block}
  .refferal .btn{background:black;color:white;}
  .logo-login-div {margin: 0 0 20px; text-align: center;}
  .logo-login-div img {width: 70px;}
  .btn--blue {
      background: #384b97;
      color: midnightblue;
  }
  .btn.btn-agree {
      position: fixed;
      bottom: 20px;
      left: 15px;
      width: calc(100% - 30px);
      padding: 18px 0;    
  }
  .btn_wd{width:100%;padding:10px 15px;display:block;}
  .register-result {
      text-align: center;
  }
  .register-result .title {
      font-size: 18px;
      font-weight: bold;
  }
  .register-result .title + p {
      margin: 0 auto;
  }
  body{
      height:0%
  }
</style>
<body>
  <div class="register-result">
      <div class="container">
        <div class="logo-login-div">
            <img src="<?=G5_URL?>/img/check_basics.png" alt="체크 이미지">
            <?if(strpos($url,'adm')){echo "<br><span class='adm_title'>For Administrator</span>";}?>
        </div>
        <p class="title mb-3">회원탈퇴 완료</p>
        <p>회원탈퇴가 완료되었습니다.</p>
        <p>감사합니다.</p>
      </div>
      <a href="/" class="btn btn_wd btn-agree main_btn" style="background: #3b86ff; color: #fff">처음으로</a>
  </div>
</body>

