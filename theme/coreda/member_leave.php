<?php
include_once('./_common.php');
include_once(G5_THEME_PATH.'/_include/wallet.php');
include_once(G5_THEME_PATH.'/_include/gnb.php');
?>

<style>
  #wrapper {
    background: #fff;
  }
</style>

<div class='container member_leave'>
  <h1 class="main_title">회원(계정) 탈퇴</h1>
  <p class="info_text">회원(계정) 탈퇴를 하시면 회원님의 모든 정보와 활동 기록, <br />USDT/보너스/잔고 및 사용 내역이 삭제됩니다. 삭제된 정보는<br /> 복구할 수 없으니 신중하게 결정해주세요.</p>
  <p class="info_text red">
  ! 계정탈퇴 및 USDT 관련 문의는 회원탈퇴 전 반드시 1:1 문의사항을 이용해주세요. 회원 탈퇴 진행후에는 처리되지 않습니다.
  </p>

  <div class="leave_info_wrap">
    <form action="">
      <div class="input_wrap mb10">
        <label for="mb_name"> 이름
          <input type="text" id="mb_name" name="mb_name" placeholder="이름을 입력해주세요." value="<?=$member['mb_name']?>" disabled>
        </label>
      </div>
      <div class="input_wrap mb10">
        <label for="mb_id"> 아이디
          <input type="text" id="mb_id" name="mb_id" placeholder="아이디를 입력해주세요." value="<?=$member['mb_id']?>" disabled>
        </label>
      </div>
      <div class="input_wrap mb10">
        <label for="mb_password"> 로그인 비밀번호
          <input type="password" id="reg_mb_password" name="reg_mb_password" placeholder="로그인 비밀번호를 입력해주세요.">
        </label>
      </div>
      <div class="input_wrap">
        <label for="mb_tr_password"> 핀번호
          <input type="password" id="reg_tr_password" name="reg_tr_password" placeholder="출금비밀번호(핀코드)를 입력해주세요." minlength="6" maxlength="6">
        </label>
      </div>
    </form>
  </div>
  <div class="btn_wrap" style="margin-bottom: 28px">
    <a href="javascript:history.back()" class="btn_cancel">취소</a>
    <a href="javascript:void(0)" class="btn_leave b_sub" onclick="memberLeave()">회원(계정)탈퇴</a>
  </div>
</div>
<script>
  $(".top_title h3").html("<p>회원탈퇴</p>");

  function memberLeave() {
    if(!$('#reg_mb_password').val()) {
      dialogModal("로그인 비밀번호 확인", "로그인 비밀번호를 입력해주세요.", "warning");
      return false;
    };

    if(!$('#reg_tr_password').val()) {
      dialogModal("출금비밀번호(핀코드) 확인", "출금비밀번호(핀코드)를 입력해주세요.", "warning");
      return false;
    };

    $.ajax({
				type: "POST",
				url: "/bbs/member_leave.php",
				dataType: "json",
				data:  {
					reg_mb_password: $("#reg_mb_password").val(),
          reg_tr_password: $("#reg_tr_password").val()
				},
				success: function(data) {
					if(data.code == '300'){
						dialogModal('비밀번호 확인', data.msg, 'warning');
					} else{
						location.replace(data.url);
					}
				},
				error:function(e){
          console.log(e);
					dialogModal('처리 실패!','<strong> 다시 시도해주세요. 문제가 계속되면 관리자에게 연락주세요.</strong>','failed');
				}
			});
  }

  // 핀번호 (오직 숫자만)
  document.getElementById('reg_tr_password').oninput = function() {
			// if empty
			if (!this.value) return;

			// if non numeric
			let isNum = this.value[this.value.length - 1].match(/[0-9]/g);
			if (!isNum) this.value = this.value.substring(0, this.value.length - 1);
		}

</script>

<? include_once(G5_THEME_PATH.'/_include/tail.php'); ?>