
<?php
	include_once('./_common.php');
	include_once(G5_THEME_PATH.'/_include/wallet.php');
	include_once(G5_THEME_PATH.'/_include/gnb.php');
	include_once(G5_PLUGIN_PATH.'/Encrypt/rule.php');

	login_check($member['mb_id']);
	
	$title = 'profile';
	// $str = '한은수,8301131058000';


	/* 추천 링크 */
	/* $refferal_sql = "select short_code from url_shorten where mb_no ='{$member['mb_no']}'";
	$refferal_result = sql_fetch($refferal_sql);
	$ref_url = G5_URL."/go/".$refferal_result['short_code']; */


	// 임의의수까지 숫자 랜덤
	function generate_code($length = 6) {
		$numbers  = "0123456789";
		$svcTxSeqno = date("YmdHis");
		$nmr_loops = 6;
		while ($nmr_loops--) {
			$svcTxSeqno .= $numbers[mt_rand(0, strlen($numbers))];
		}
		return $svcTxSeqno;
	}

	//  print_R(Encrypt($str,$secret_key,$secret_iv));
	


	// $security_code= generateRandomString(6);
	// $security_num_code = generate_code(8);

	function format_phone($phone){ $phone = preg_replace("/[^0-9]/", "", $phone); $length = strlen($phone); switch($length){ case 11 : return preg_replace("/([0-9]{3})([0-9]{4})([0-9]{4})/", "$1-$2-$3", $phone); break; case 10: return preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "$1-$2-$3", $phone); break; default : return $phone; break; } }


	// 자동 팩 구매
	// $pack_sql = "select * from g5_shop_cart where mb_id = '{$member['mb_id']}' order by ct_time desc limit 0,1";
	// $pack_res = sql_fetch($pack_sql);
	// $pack_name = substr($pack_res['it_name'],-1,1);

	// $pack_auto = $pack_res['ct_option'];
	// if($pack_auto != ''){
	// 	$pack_check =  'checked';
	// }


	//kyc 인증
	$kyc_sql = "select * from g5_write_kyc where mb_id = '{$member['mb_id']}' order by wr_last desc limit 0,1";
	$kyc_res = sql_fetch($kyc_sql);
	if($kyc_res ){
		$kyc_cert = $kyc_res['wr_2'];
	}else{
		if($member['kyc_cert'] > 0)
		$kyc_cert = $member['kyc_cert'];
	}



	//길이만큼 영문숫자 랜덤코드 /lib/common.php
	/*
	function generateRandomString($length = 10) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';

		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}
	*/

?>
	<style>
		.half{width:calc(50% - 10px);display: inline-block;}
		.half + .half{width:calc(50% - 10px);}
		.half + .half ::after{clear: both;}
		#ch_tax [type="checkbox"]{width:auto;width:20px;height:20px;vertical-align:middle}
		.person_agree_view{font-size:11px;border:1px solid gray;color:gray;border-radius:5px;padding:1px 5px 2px;vertical-align:middle;}
		.dark .person_agree_view{color:white;border:1px solid rgba(255,255,255,0.3);}
		.b_line{padding-bottom:10px;}
		.profile-box{margin-bottom:45px;}
		.textbox{width:100%;min-height:200px;text-align:left;font-size:11px;}
		.preclose{display:none;}
		.open{display:block;}
 
		.filebox{width:100%;padding:8px 15px; border-radius:5px;text-align:center;font-size:14px;background:white}
		.dark .filebox{color:white;background:rgba(0,0,0,0.3);}
		#tax_person_file{font-size:14px;}

		.kyc_label{color:#2b3a6d !important;}
		.dark .kyc_label{color:#fac707 !important;}

		.status_1{background: url('<?=G5_THEME_URL?>/_images/okay_icon.png') no-repeat center left;background-size: 13px;padding-left:10px;}
		.status_2{background: url('<?=G5_THEME_URL?>/_images/x_icon.png') no-repeat center left;background-size: 13px;padding-left:10px;}

		.person_info{font-size:11px;font-weight:300}

		.dark .kyc_icon{color:#ffd965;}
		.kyc_icon{color:#2b3a6d;}

		.box + .box{margin-top:20px;}
		.hja_addr{color:black;min-width:80%;color:black !important;background:#f1f4fb !important;border:1px solid #dae0ef !important}
		.dark .hja_addr{color:black;min-width:80%;background:rgba(255,255,255,0.9)}

	</style>

	<link href="<?=G5_THEME_URL?>/css/scss/radio_set_<?=$_COOKIE['mode']?>.css" rel="stylesheet">
    <main>
        <div class='container profile nomargin nopadding'>
			<section class="profile_wrap content-box6" style="height: 100%">
				<div class="col-sm-12 col-12 profile-box">
					<h3 class='title b_line'>
						<i class='p1'><img src="<?=G5_THEME_URL?>/img/personl_information.png" alt=""></i>
						<span >개인정보</span>
					</h3>

					<ul class='row person_info'>
						<li class='col-12 inline user_box'>
							<span class='userid user_level inline' ><?=$user_icon?></span>
							<h4 class='myid inline'>
								<span class="name bold"><?=$member['mb_name']?>님</span>
								<span class="id"><?=$member['mb_id']?></span>
							</h4>
							<!-- <h4 class='mygrade badge inline'><?=$user_level?></h4> -->
						</li>
					</ul>

					<ul class='row person_info'>
						<li class='col-12'>
							<label>모바일</label>  
							<div class='row'>
								<div class='col-8'><p><?=format_phone($member['mb_hp'])?></p></div>
								<?if($member['mb_hp'] == '' || $member['mb_certify'] != 1){?>
									<div class='col-4 text-right'><input type="button" value="수정/변경" class="btn inline num_pop_open pop_open" ></div>
								<?}?>
							</div>
						</li>
					

						<li class='col-12 mt20'>
							<label>이메일</label>  
							<p><?=$member['mb_email']?></p>
							<!-- <?if($member['mb_email_certify'] != ''){?>
								<img src="<?=G5_THEME_URL?>/_images/okay_icon.gif" alt="인증됨" style="width:15px;">
							<?}else{?>
								<img src="<?=G5_THEME_URL?>/_images/x_icon.gif" alt="인증안됨" style="width:15px;">
							<?}?> -->
						</li>
						<!-- 
							<li class='col-sm-3 col-4 text-right grid'>
								<input type="button" value="Change" class="btn inline white email_pop_open pop_open" value="변경">
							</li>
						 -->

						 
					</ul>
					<?php if($member['bank_name'] && $member['bank_account'] && $member['account_name']){?>
					<ul class='row person_info'>
						<li class='col-12'>
							<label>출금정보</label>
							<p><?=$member['bank_name']?> : <?=$member['bank_account']?> (<?=$member['account_name']?>)</p>	
						</li>
					</ul>
					<?php } ?>
				</div>
				<div class='col-sm-12 col-12 profile-box security'>
					<h3 class='title b_line'>
						<i class="p2"><img src="<?=G5_THEME_URL?>/img/security_setting.png" alt=""></i>
						<span >보안설정</span>
					</h3>
					<ul class='row'>
						<li class='col-sm-9 col-8'><span >로그인 비밀번호 변경</span></li>
						<li class='col-sm-3 col-4 text-right grid'>
							<i class="ri-arrow-drop-right-line ch_pw_open pop_open"></i>
						</li>
					</ul>

					<ul class='row'>
						<li class='col-sm-9 col-8'><span >출금 비밀번호 변경</span></li>
						<li class='col-sm-3 col-4 text-right grid'>
							<i class="ri-arrow-drop-right-line ch_tpw_open pop_open"></i>
						</li>
					</ul>
				</div>

				<!-- <div class='col-sm-12 col-12 profile-box certificate'>
					<h3 class='title b_line'>
						<i class="ri-account-box-line kyc_icon"></i>
						<span >KYC 인증 정보</span>
					</h3>
					<ul class='row mt10'>
						<li class='col-sm-9 col-8'>
							<label >KYC 인증 등록 정보</label>
							<p class='status_<?=$kyc_cert?>' style="width:100%">
								<?if($kyc_res){
									echo person_key($kyc_res['wr_subject'],$kyc_cert,$kyc_res['wr_content']);
								}else{
									if($member['kyc_cert']>0){
										echo person_key($member['mb_name'],$member['kyc_cert']);
									}else{
										echo "미등록";
									}
								}?>
							</p>
						</li>
						<li class='col-sm-3 col-4 text-right'>
							<button class="reg_btn btn" data-name="ch_tax"> 등록/변경</button>
						</li>
					</ul>

					<ul class='row'>
						<li class='col-sm-9 col-8'>
							<label >환불계좌(실명계좌) 등록</label>
							<p ><?=get_name($member['mb_center'])?></p>
						</li>
						<li class='col-sm-3 col-4 text-right'>
							<span class="reg_btn" data-name="ch_cert_bank"> 변경</span>
						</li>
					</ul>

					<div class="google-auth-top-qr" id="qrcode"></div>
				</div> -->

				<div class='col-sm-12 col-12 profile-box certificate'>
					<!-- <h3 class='title b_line'>
						<i class="ri-wallet-2-line kyc_icon" style="font-weight:300;"></i>
						<span >HJA 주소 등록</span>
					</h3>
					<ul class='row mt10'>
						<li class='col-sm-9 col-9' style="margin-right:0;padding-right:0;">
							<label >※ HJA BEP-20 주소 등록</label>
							<input type='text' id='hja_addr' class='hja_addr' placeholder="<?=$member['hja_addr']?>" />
							
						</li>
						<li class='col-sm-3 col-3 text-right' style="">
							<button class="addr_btn btn" data-name="ch_addr" style="margin:30px 5px 10px 0;"> 등록/변경</button>
						</li>
					</ul> -->

					<!-- <ul class='row'>
						<li class='col-sm-9 col-8'>
							<label >환불계좌(실명계좌) 등록</label>
							<p ><?=get_name($member['mb_center'])?></p>
						</li>
						<li class='col-sm-3 col-4 text-right'>
							<span class="reg_btn" data-name="ch_cert_bank"> 변경</span>
						</li>
					</ul> -->

					<!-- <div class="google-auth-top-qr" id="qrcode"></div> -->
				</div>

				<!-- <div class='col-sm-12 col-12 profile-box'>
					<h3 class='title b_line'>
						<i><img src="<?=G5_THEME_URL?>/img/alert_setting.png" alt=""></i>
						<span >알림설정<span>
					</h3>
					
					<ul class='row'>
						<li class='col-sm-9 col-8'>
							<p >알림알림</p>
						</li>
						<li class='col-sm-3 col-4 text-right grid'>
							<div class='switch_box'>
								<label class="switch">
									<input type="checkbox" <?if($member['mb_sms']>0) echo 'checked';?> class='noti_check'>
									<span class="slider round"></span>
								</label>
								<p>끄기</p><p style="display:none;" >켜기</p>
							</div>
						</li>
					</ul>
				</div> -->

				<div class='col-sm-12 col-12 profile-box'>
					<h3 class='title b_line'>
						<i class="p3"><img src="<?=G5_THEME_URL?>/img/recommendation_information.png" alt=""></i>
						<span >추천인 정보</span>
					</h3>
					<ul class='row mt10'>
						<li class='col-sm-12 col-12'>
							<label >나의 추천</label>
							<p ><?=$member['mb_recommend']?></p>
						</li>
					</ul>

					<!-- <ul class='row'>
						<li class='col-sm-12 col-12'>
							<label >나의 센터</label>
							<p ><?=get_name($member['mb_center'])?></p>
						</li>
					</ul> -->
				</div>

				<div class='col-sm-12 col-12 profile-box'>
					
					<ul class='row mt10'>
						
						<li class='col-12 '>
							<button class="btn wd" onclick="location.href='page.php?id=member_leave'">회원 탈퇴</button>
						</li>
					</ul>
				</div>

				


			</section>
    	</div>
	</main>
	<?php include_once(G5_THEME_PATH.'/_include/tail.php'); ?>
	<div class="gnb_dim"></div>
</section>

<script>
	$(function(){
		var agree_content = $("#argee_content");
			agree_content.load("user_agree.html");

		$(".top_title h3").html("<span>개인정보&보안설정</span>")

		$('.reg_btn').click(function(){
			var target = $(this).data("name");
			dimShow();
			$('#'+target).css("display","block");
			
		});

		$('.addr_btn').click(function(){
			var hja_addr = $('#hja_addr').val();

			$.ajax({
			type: "POST",
			url: "/util/profile_proc.php",
			dataType: "json",
			data:  {
				"category" : "addr",
				"hja_addr" : hja_addr
			},
			success: function(data) {
				if(data.result =='success'){
					dialogModal('주소 등록','<strong> 주소가 등록되었습니다.</strong>','failed',false);
				}else{
					dialogModal('처리 실패!','<strong> '+ data.sql+'</strong>','failed',false);
				}
			},
			error:function(e){
				dialogModal('처리 실패!','<strong> 다시 시도해주세요. 문제가 계속되면 관리자에게 연락주세요.</strong>','failed',false);
			}
		});
		});

		$('.person_agree_view').on('click',function(){
			// $(this).next('div').slideToggle()
			$('#commonModal').addClass('agree');
			commonModal("고유식별정보 처리방침",agree_content.html(),"confirm");
		})

			$('.modal-footer .btn').on('click', function(){
				if($('#commonModal').hasClass('agree')) {
					$('#commonModal').removeClass('agree');
				}
			});	

	});
</script>

		<!-- 트랜잭션 비밀번호 변경 -->
		<div class="pop_wrap chage_tpw_pop">
			<p class="pop_title font_red">경고</p>
			<div>
				본인의 비밀번호는 블록체인에 공유되거나 서버에 저장되지 않습니다. 즉, 우리는 회원의 비밀번호를 알 수도 없고 초기화 시킬 수도 없습니다. 회원의 지갑을 복구하기 위한 유일한 방법은 백업 구절을 통한 방법입니다. 비밀번호 분실시 지갑을 복구할 수 있는 유일한 방법인 백업 구절을 꼭 안전한 장소에 보관하시기 바랍니다.
			</div>
			<div class="pop_close_wrap">
				<a href="javascript:void(0);" class="go_tpw1">계속</a>
			</div>
		</div>

		<div class="pop_wrap chage_tpw_pop1 input_pop_css">
			<form action="">
				<div class="reset_input_box">
					<label for="" >사용중인 출금 비밀번호</label>
					<input type="password" id="current_tpw" maxlength="6">
					<hr class="hr_dash">
					<label for="" >새로운 출금 비밀번호</label>
					<input type="password" id="new_tpw" maxlength="6">
					<label for="" >새로운 출금 비밀번호 확인</label>
					<input type="password" id="new_tpw_re" maxlength="6">
					<label for="" >로그인 비밀번호</label>
					<input type="password" id="auth_pwd" minlength='4' maxlength="20">
				</div>
				<!--
				<div>
					<label for="" >보안코드 입력</label>
					<p class="code_btn code_btn_tpw"><img src="<?=G5_THEME_URL?>/_images/email_send_icon.gif" alt="이미지">코드요청</p>
				</div>
				<input type="text" style="margin-bottom:25px;">
				-->

				<div class="btn2_btm_wrap">
					<input type="button" value="취소" class="btn btn_double default_btn cancel pop_close" >
					<input type="button" value="저장" class="btn btn_double blue save go_tpw3">
				</div>
			</form>
		</div>

		<div class="pop_wrap chage_tpw_pop2 notice_img_pop">
			<p class="pop_title" >인증번호 전송</p>
			<img src="<?=G5_URL?>/img/check_basics.png" alt="체크 이미지">
			<div >인증번호가 이메일로 전송되었습니다</div>
			<a href="javascript:void(0);" class="back_tpw1 gray_close f_right" >닫기</a>
		</div>

		<div class="pop_wrap chage_tpw_pop3 notice_img_pop">
			<p class="pop_title" >거래 비밀번호 변경</p>
			<div>
				<img src="<?=G5_URL?>/img/check_basics.png" alt="체크 이미지">
				<p >변경이 성공적으로 완료되었습니다.</p>
			</div>
			<div class="pop_close_wrap">
				<a href="javascript:void(0);" id="pin_close" class="btn wd inline pop_close">닫기</a>
			</div>
		</div>
<script>

$(function() {
	//  트랜잭션 비밀번호변경
	$('.ch_tpw_open').click(function(){
		//$('.chage_tpw_pop').css("display","block");
		$('.chage_tpw_pop1').css("display","block");
	});

	onlyNumber('current_tpw');
	onlyNumber('new_tpw');
	onlyNumber('new_tpw_re');

	$('.chage_tpw_pop1 .save').click(function(){
		// console.log('tpw');

		var current_tpw = $('.chage_tpw_pop1 #current_tpw').val();
		var new_tpw = $('.chage_tpw_pop1 #new_tpw').val();
		var new_tpw_re = $('.chage_tpw_pop1 #new_tpw_re').val();


		if(new_tpw.length < 6){
			dialogModal('입력확인','<strong> 출금비밀번호(핀코드)는 6자리 숫자입니다.</strong>','failed',false);
			return false;
		}

		if(new_tpw != new_tpw_re){
			dialogModal('입력확인','<strong> 입력한 출금비밀번호(핀코드)가 일치하지 않습니다.</strong>','failed',false);
			return false;
		}

		// console.log(current_tpw + '/' + new_tpw + '/' + new_tpw_re);

		$.ajax({
				type: "POST",
				url: "/util/profile_proc.php",
				dataType: "json",
				data:  {
					"current_tpw" : current_tpw,
					"new_tpw" : new_tpw,
					"new_tpw_re" : new_tpw_re,
					"auth_pwd" : $('.chage_tpw_pop1 #auth_pwd').val(),
					"category" : "tpw"
				},
				success: function(data) {
					if(data.result =='success'){
						$('.chage_tpw_pop1').css("display","none");
						$('.chage_tpw_pop3').css("display","block");
						$('#pin_close').click(function(){
							window.location.reload();
						})
					}else{
						dialogModal('처리에러!','<strong> '+ data.sql+'</strong>','failed',false);
					}
				},
				error:function(e){
					dialogModal('처리 실패!','<strong> 다시 시도해주세요. 문제가 계속되면 관리자에게 연락주세요.</strong>','failed',false);
				}
			});

	});
});

</script>




<!--  비밀번호 변경 -->

	<div class="pop_wrap chage_pw_pop">
		<p class="pop_title font_red">경고</p>
		<div>
			본인의 비밀번호는 블록체인에 공유되거나 서버에 저장되지 않습니다. 즉, 우리는 회원의 비밀번호를 알 수도 없고 초기화 시킬 수도 없습니다. 회원의 지갑을 복구하기 위한 유일한 방법은 백업 구절을 통한 방법입니다. 비밀번호 분실시 지갑을 복구할 수 있는 유일한 방법인 백업 구절을 꼭 안전한 장소에 보관하시기 바랍니다.
		</div>
		<div class="pop_close_wrap">
			<a href="javascript:void(0);" class="go_ch_pw1">계속</a>
		</div>
	</div>

	<div class="pop_wrap chage_pw_pop1 input_pop_css">
		<form action="">
			<div class="reset_input_box">
				<label for="" >사용중인 비밀번호</label>
				<input type="password" id="current_pw" minlength='4' maxlength="20">
				<hr class="hr_dash">
				<label for="" >새로운 비밀번호</label>
				<input type="password" id="new_pw" minlength='4' maxlength="20">
				<label for="" >새로운 비밀번호 확인</label>
				<input type="password" id="new_pw_re" minlength='4' maxlength="20">
			</div>
			<!--
			<div>
				<label for="" >보안코드 입력</label>
				<p class="code_btn code_btn_pw"><img src="<?=G5_THEME_URL?>/_images/email_send_icon.gif" alt="이미지" >코드요청</p>
			</div>

			<input type="text" style="margin-bottom:25px;">
			-->
			<div class="btn2_btm_wrap">
				<input type="button" value="취소" class="btn btn_double default_btn cancel pop_close" >
				<input type="button" value="저장" class="btn btn_double default_btn blue save go_ch_pw3">
			</div>
		</form>
	</div>

	<div class="pop_wrap chage_pw_pop3 notice_img_pop">
		<p class="pop_title" >비밀번호 변경</p>
		<div>
			<img src="<?=G5_URL?>/img/check_basics.png" alt="체크 이미지">
			<span >비밀번호가 성공적으로 변경되었습니다.
		</div>
		<div class="pop_close_wrap">
			<a href="javascript:void(0);" id="pass_close" class="btn wd inline pop_close">닫기</a>
		</div>
	</div>

<script>



$(function() {
	
	$('.ch_pw_open').click(function(){
			//$('.chage_pw_pop').css("display","block");
		$('.chage_pw_pop1').css("display","block");
	});


	$('.chage_pw_pop1 .save').click(function(){

		var current_pw = $('.chage_pw_pop1 #current_pw').val();
		var new_pw = $('.chage_pw_pop1 #new_pw').val();
		var check_new_pw = CheckPass(document.getElementById('new_pw').value);
		var new_pw_re = $('.chage_pw_pop1 #new_pw_re').val();

		if(check_new_pw == false){
			dialogModal('입력확인','<p>4~12자 이내 영문,숫자,특수문자 조합을 해주세요.</p>','failed',false);
			return false;
		}

		if(new_pw != new_pw_re){
			dialogModal('입력확인','<p>새로운 비밀번호가 일치하지 않습니다.</p>','failed',false);
			return false;
		}

		// console.log(current_pw + '/' + new_pw + '/' + new_pw_re);

		$.ajax({
			type: "POST",
			url: "/util/profile_proc.php",
			dataType: "json",
			data:  {
				"current_pw" : current_pw,
				"new_pw" : new_pw,
				"new_pw_re" : new_pw_re,
				"category" : "pw"
			},
			success: function(data) {
				if(data.result =='success'){
					$('.chage_pw_pop1').css("display","none");
					$('.chage_pw_pop3').css("display","block");
					$('#pass_close').click(function(){
						window.location.reload();
					})
				}else{
					dialogModal('처리 실패!','<strong> '+ data.sql+'</strong>','failed',false);
				}
			},
			error:function(e){
				dialogModal('처리 실패!','<strong> 다시 시도해주세요. 문제가 계속되면 관리자에게 연락주세요.</strong>','failed',false);
			}
		});

	});
});

</script>



<!-- 이메일 주소 변경 -->
	<div class="pop_wrap chage_email_pop input_pop_css">
		<form>
			<label for="" >사용중인 이메일 주소</label>
			<div class='current'><?=$member['mb_email']?></div>

			<label for="" >새로운 이메일 주소</label>
			<input type="text"  name="email_new" id="email_new" value="" onchange="validateEmail(this.value);">

			<label for="" >새로운 이메일 주소 확인</label>
			<input type="text"  name="email_new_re" id="email_new_re" value="">

			<label for="" >로그인 비밀번호</label>
			<input type="password" id="auth_pwd" minlength='4' maxlength="20">

			<div class="btn2_btm_wrap">
				<input type="button" value="취소" class="btn btn_double deault_btn cancel pop_close" >
				<input type="button" value="저장" class="btn btn_double blue save">
			</div>
		</form>
	</div>


	<div class="pop_wrap chage_email_pop1 notice_img_pop">
		<p class="pop_title" >이메일 변경</p>
		<div>
			<img src="<?=G5_URL?>/img/check_basics.png" alt="체크 이미지">
			<p> 변경이 성공적으로 완료되었습니다.</p>
		</div>
		<div class="pop_close_wrap">
			<a href="javascript:parent.location.reload();" class="btn inline wd pop_close" >닫기</a>
		</div>
	</div>


	<script>


	$(function() {

		$('.email_pop_open').click(function(){
			$('.chage_email_pop').css("display","block");

		validateEmail = function (email) {
		var email = email;
		var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;

		if (email == '' || !re.test(email)) {
			alert("wrong E-mail type! please check e-mail");
			return false;
		}
	}
		});

		$('.go_ch_em1').click(function(){
			$('.chage_email_pop').css("display","none");
			$('.chage_email_pop1').css("display","block");
		});



		$('.chage_email_pop .save').click(function(){
			var email2 = $('.chage_email_pop #email_new').val();
			var email3 = $('.chage_email_pop #email_new_re').val();

			if( email2 != email3){
				dialogModal('입력확인','<strong> 입력한 메일주소가 일치하지 않습니다.</strong>','failed',false);
				return false;
			}

			$.ajax({
					type: "POST",
					url: "/util/profile_proc.php",
					dataType: "json",
					data:  {
						"email2" : email2,
						"email3" : email3,
						// "confirm" : email_vaild_code,
						"auth_pwd" : $('.chage_email_pop #auth_pwd').val(),
						"category" : "email"

					},
					success: function(data) {
						if(data.result =='success'){
							$('.chage_email_pop2').css("display","none");
							$('.chage_email_pop1').css("display","block");
						}else{
							dialogModal('처리 실패!','<strong> '+ data.sql+'</strong>','failed',false);
						}
					},
					error:function(e){
						dialogModal('처리 실패!','<strong> 다시 시도해주세요. 문제가 계속되면 관리자에게 연락주세요.</strong>','failed',false);
					}
				});

		});
	});
	</script>
<!-- 이메일 주소 변경 -->



<!-- 전화번호 변경 -->
	<div class="pop_wrap num_pop_wrap input_pop_css">
		<form action="">
			<label for="" >사용중인 전화번호</label>
			<div class="num_pop_div clear_fix" >
				<!-- <input type="input" id="nation_num" value='' placeholder="Country" maxlength="3" readonly>
				<input type="input" id="hp_num" value='<?=substr(str_replace('-','',$member['mb_hp']), 1);?>' placeholder="Phone Number(Number only)" readonly>
					-->
				<!-- <div class='current'><?=" + ".$member['nation_number']." ".$member['mb_hp']?></div> -->
				<div class='current'><?=" + ".$member['nation_number']." ".$member['mb_hp']?></div>
			</div>


			<label for="" >새로운 전화번호 (ex: 01011112222)</label>
			<div class="num_pop_div clear_fix">
				<!-- <input type="text" id="new_nation_num" value="<?=$member['nation_number']?>"  maxlength="3" placeholder="국가번호" readonly> -->
				<input type="text" id="new_hp_num" value="" style="width:100%;margin-left:0;color: black;" placeholder="전화번호(숫자만)" >
			</div>


			<label for="" >로그인 비밀번호</label>
			<input type="password" id="auth_pwd" minlength='4' maxlength="20">


			<div class="btn2_btm_wrap" >
				<input type="button" value="취소" class="btn btn_double default_btn cancel pop_close" >
				<input type="button" value="저장" class="btn btn_double blue save proceed">
			</div>

		</form>
	</div>

	<!-- 변경완료 -->
	<div class="pop_wrap num2_pop_wrap notice_img_pop">
		<p class="pop_title" >전화번호 변경</p>
		<div>
			<img src="<?=G5_URL?>/img/check_basics.png" alt="체크 이미지">
			<p >변경이 성공적으로 완료되었습니다.</p>
		</div>
		<div class="pop_close_wrap">
			<a href="javascript:void(0);" class="btn inline wd pop_close" >닫기</a>
		</div>
	</div>

	<script>
		$(function() {
			onlyNumber('new_hp_num');

			$('.num_pop_open').click(function(){
				$('.num_pop_wrap').css("display","block");
			});

			$('.num1_pop_close').click(function(){
				$('.num_pop_wrap').css("display","block");
				$('.num1_pop_wrap').css("display","none");
			});

			$('.proceed').click(function(){
				var new_hp_num = $('.num_pop_wrap #new_hp_num').val();

				$.ajax({
					type: "POST",
					url: "/util/profile_proc.php",
					dataType: "json",
					data:  {
						// "hp_num" : hp_num,
						// "new_nation_num" : new_nation_num,
						"new_hp_num" : new_hp_num,
						"auth_pwd" : $('.num_pop_wrap #auth_pwd').val(),
						"category" : "phone"

					},
					success: function(data) {
						if(data.result =='success'){
							$('.num_pop_wrap').css("display","none");
							$('.num2_pop_wrap').css("display","block");
							$('.num2_pop_wrap .pop_close').click(function(){
								parent.location.reload();
							});
						}else{
							dialogModal('입력확인','<strong>'+data.sql+'</strong>','failed',false);
						}
					},
					error:function(e){
						dialogModal('처리 실패!','<strong> 다시 시도해주세요. 문제가 계속되면 관리자에게 연락주세요.</strong>','failed',false);
					}
				});
			});
		});
	</script>
<!-- 전화번호 변경 -->





<!-- 이름변경 -->
	<div class="pop_wrap chage_name_pop1 input_pop_css">
	<form action="">

		<label for="" >현재 성함</label>
			<!-- <input type="text" id="current_name" value='<?=$member['first_name']." ".$member['last_name']?>' readonly> -->
			<div class='current'><?=$member['first_name']." ".$member['last_name']?></div>

		<label for="" >변경하실 성</label>
			<input type="text" id="new_last_name">

		<label for="" >변경하실 이름</label>
			<input type="text" id="new_first_name">

		<label for="" >로그인 비밀번호</label>
			<input type="password" id="auth_pwd" minlength='4' maxlength="20">


		<div class="btn2_btm_wrap">
			<input type="button" value="취소" class="btn btn_double default_btn cancel pop_close" >
			<input type="button" value="저장" class="btn btn_double blue save go_ch_name">
		</div>
	</form>
	</div>


	<div class="pop_wrap chage_name_pop3 notice_img_pop">
		<p class="pop_title" >이름 변경</p>
		<div>
			<img src="<?=G5_URL?>/img/check_basics.png" alt="체크 이미지">
			<span>변경 처리되었습니다.</span>
		</div>
		<div class="pop_close_wrap">
			<a href="javascript:void(0);" class="btn inline wd pop_close">닫기</a>
		</div>
	</div>

	<script>
	$(function() {
		
		/* var noti_check = <?=$member['mb_sms']?>;
		if(noti_check > 0){
			$('.switch_box p').toggle();
		}

		$("input[type='checkbox']").on('click',function(){
			$(this).parent().parent().find('p').toggle();
			
			if($(this).is(":checked")){
				var check_value = 1;
			}else{
				var check_value = 0;
			}
			$.ajax({
				type: "POST",
				url: "/util/profile_proc.php",
				dataType: "json",
				data:  {
					"check_value" : check_value,
					"category" : "notification"
				},
				success: function(data) {
					if(data.result =='success'){
						
					}else{
						dialogModal('처리 실패!','<strong> '+ data.sql+'</strong>','failed');
					}
				},
				error:function(e){
					dialogModal('처리 실패!','<strong> 다시 시도해주세요. 문제가 계속되면 관리자에게 연락주세요.</strong>','failed');
				}
			});
		}); */
	

		$('.ch_name_open').click(function(){
			$('.chage_name_pop1').css("display","block");
		});

		$('.chage_name_pop1 .save').click(function(){
			var new_last_name = $('.chage_name_pop1 #new_last_name').val();
			var new_first_name = $('.chage_name_pop1 #new_first_name').val();
			var auth_pwd = $('.chage_name_pop1 #auth_pwd').val();

			$.ajax({
					type: "POST",
					url: "/util/profile_proc.php",
					dataType: "json",
					data:  {
						"new_last_name" : new_last_name,
						"new_first_name" : new_first_name,
						"auth_pwd" : auth_pwd,
						"category" : "name"
					},
					success: function(data) {
						if(data.result =='success'){
							$('.chage_name_pop1').css("display","none");
							$('.chage_name_pop3').css("display","block");
						}else{
							dialogModal('처리 실패!','<strong> '+ data.sql+'</strong>','failed',false);
						}
					},
					error:function(e){
						dialogModal('처리 실패!','<strong> 다시 시도해주세요. 문제가 계속되면 관리자에게 연락주세요.</strong>','failed',false);
					}
				});
		});
	});
	</script>
<!-- 이름변경 -->




<!--세금신고-->
<div class="pop_wrap input_pop_css" id="ch_tax">
		

	<form method="post" action="">
		<div class="reset_input_box">
			<label for="" >성명</label>
			<input type="text" id="tax_name" maxlength="6" value="" style="color: #000">
			<label for="" >주민등록번호</label>
			<input type="text" pattern="\d*" id="tax_person_number_1" maxlength="6" class="half" inputmode="number" style="color: #000"> 
			<label style="display:inline;font-size:22px">-</label>
			<input type="password" pattern="\d*" id="tax_person_number_2" maxlength="7" class="half" inputmode="number">
			<input type="hidden" id="tax_person_number_3" maxlength="7" class="half" >
		

			<label>KYC신분증 첨부 </label>
				<input type="file" accept="image/*" class='filebox' name="bf_file[1]"  >
				<label for="bf_file[1]" class='kyc_label' style="font-size:11px;margin:3px;">신분확인 가능한 주민등록증, 운전면허증 사진을 첨부해주세요.</label>

			<hr class="hr_dash">

			<label class="mt20">출금지갑주소 첨부 </label>
			<input type="file"  accept="image/*" class='filebox' name="bf_file[2]">
			<label for="bf_file[2]" class='kyc_label' style="font-size:11px;margin:3px;">출금 지갑주소가 확인되는 캡쳐이미지,사진을 첨부해주세요.</label>

			<!-- <div class="radio_set">
				<input type="radio" id="wallet_type1" name="wallet_type" value="0"/><label for="wallet_type1">국내거래소 지갑</lable>
				<input type="radio" id="wallet_type2" name="wallet_type" value="1"/><label for="wallet_type2">해외거래소 지갑</lable>
				<input type="radio" id="wallet_type3" name="wallet_type" value="2"/><label for="wallet_type3">기타/개인 지갑</lable>
			</div> -->

			<label class="mt30">출금지갑 종류선택 </label>
				<div class="radio_set">
					<label>
						<input type="radio" id="wallet_type1" name="wallet_type" class="selector-item_radio" value=1>
						<span>국내거래소 지갑</span>
					</label>
					<label>
						<input type="radio" id="wallet_type1" name="wallet_type" class="selector-item_radio" value=2>
						<span>해외거래소 지갑</span>
					</label>
					<label>
						<input type="radio" id="wallet_type1" name="wallet_type" class="selector-item_radio" value=3>
						<span>개인/기타 지갑(메타마스크등)</span>
					</label>
				</div>

			<hr class="hr_dash">
			<div class='mb15'>
				<input type="checkbox" id="tax_person_number_agree" class="inline" name="tax_person_number_agree" value=""/>
				<label for="tax_person_number_agree" class="inline">고유식별정보 처리 동의</label>
				<a href="javascript:void(0);" class="inline_btn person_agree_view" >전문보기</a>
				<div class="preclose">
					<textarea id="tax_person_agree_content" class="textbox">
					</textarea>
				</div>
				<div id="argee_content" style="display: none">
				</div>
			</div>
		</div>

		<div class="btn2_btm_wrap">
			<input type="button" value="취소" class="btn btn_double default_btn cancel pop_close" >
			<input type="button" value="등록" class="btn btn_double blue save" id="kyc_rec_btn">
		</div>
	</form>
</div>


<script type="text/javascript">
	$(function(){  
		$("#kyc_rec_btn").on('click',function(){
			
			var kyc_name = $("#tax_name").val();
			var kyc_person_number = $("#tax_person_number_1").val()+'-'+$('#tax_person_number_3').val();
			
			var fileInput = $(".filebox");
			
			// var upload_name = $(".upload-name").val();
			var kyc_agree = $("#tax_person_number_agree").is(':checked');
			if(!kyc_agree){
				dialogModal('KYC 인증','<strong> 고유식별정보 처리방침에 동의해주세요. </strong>','warning',false);
				return false;
			}

			var rule = 0;
			var person_number_rule1 = /^(?:[0-9]{2}(?:0[1-9]|1[0-2])(?:0[1-9]|[1,2][0-9]|3[0,1]))/;
			if(!$("#tax_person_number_1").val().match(person_number_rule1)){
				dialogModal('KYC 인증','<strong> 올바른 주민등록번호를 입력해주세요. </strong>','warning',false);
				return false;
			}else{
				rule += 1;
			}
			var person_number_rule2 = /^[1-4][0-9]{6}$/;
			if(!$("#tax_person_number_2").val().match(person_number_rule2)){
				dialogModal('KYC 인증','<strong> 주민등록번호 형식이 맞지 않습니다.\n올바른 주민등록번호를 입력해주세요. </strong>','warning',false);
				return false;
			}else{
				rule += 1;
			}			

			// console.log("파일업로드1 :: " + fileInput[0].files.length);
			// console.log("파일업로드2 :: " + fileInput[1].files.length);
			
			var wallet_type = $('input[name=wallet_type]:checked').val();

			if(!wallet_type){
				alert(" 출금지갑 종류를 선택해주세요 ");
				return false;
			}else{
				rule += 1;
			}


			if(fileInput.val() != "") {		
				var ext = fileInput.val().split(".").pop().toLowerCase();		    
				if($.inArray(ext, ["jpg", "jpeg", "png", "gif", "bmp", "pdf"]) == -1) {
					alert("첨부파일은 이미지 파일만 등록 가능합니다.");
					fileInput.val("");
					return false;
				}
			
				var maxSize = 10 * 1024 * 1024; // 10MB

				var fileSize = fileInput[0].files[0].size;
				if(fileSize > maxSize){
					alert("첨부파일 사이즈는 10MB 이내로 등록 가능합니다.");
					fileInput.val("");
					return false;
				}
			}

			
			if(fileInput[0].files.length < 1 || fileInput[1].files.length < 1){

				dialogModal('KYC 인증','<strong> 신분확인이 가능한 사진과 출금지갑 인증파일을 첨부해주세요. </strong>','warning',false);
				return false;
			}else{
			
				const formData = new FormData();
				formData.append("w",'');
				formData.append("wr_subject",kyc_name);
				formData.append("wr_content", kyc_person_number);
				formData.append("wr_wallet_type", wallet_type);


				for (var i = 0; i < fileInput.length; i++) {
					if (fileInput[i].files.length > 0) {
						
						var ext = fileInput.val().split(".").pop().toLowerCase();		    
						if($.inArray(ext, ["jpg", "jpeg", "png", "gif", "bmp", "pdf"]) == -1) {
							alert("첨부파일은 이미지 파일만 등록 가능합니다.");
							fileInput.val("");
							return false;
						}
					
						var maxSize = 10 * 1024 * 1024; // 10MB

						var fileSize = fileInput[0].files[0].size;
						if(fileSize > maxSize){
							alert("첨부파일 사이즈는 10MB 이내로 등록 가능합니다.");
							fileInput.val("");
							return false;
						}


						for (var j = 0; j < fileInput[i].files.length; j++) {
							
							// console.log(" fileInput["+i+"].files["+j+"] ::: "+ fileInput[i].files[j]);
							// formData에 'file'이라는 키값으로 fileInput 값을 append 시킨다.  
							formData.append("bf_file[]", fileInput[i].files[j]);
						}
					}
				}

				$.ajax({
					type: "POST",
					url: "/util/file_upload.php",
					data: formData,
					cache: false,
					processData: false,
					contentType: false,
					enctype: "multipart/form-data",
					dataType: "json",
					success: function(data) {
						if(data.result =='success'){
							dialogModal('KYC 인증처리',"<strong>등록되었습니다.<br>관리자 승인까지 최대 24시간 소요될수 있습니다.</strong>",'success',false);

							$('.closed').click(function(){
								window.location.reload();
							})
						}else{
							dialogModal('처리에러!','','failed');
						}
					},
					error:function(e){
						dialogModal('처리 실패!','<strong>다시 시도해주세요. 문제가 계속되면 관리자에게 연락주세요.</strong>','failed',false);
					}
				});
			} 
		});

		$("#tax_person_number_2").on('change', function(e){
			$("#tax_person_number_3").val($("#tax_person_number_2").val()); 
		});
	});
</script>