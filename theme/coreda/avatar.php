<?
include_once(G5_THEME_PATH.'/_include/head.php'); 
include_once(G5_THEME_PATH.'/_include/gnb.php'); 

if($_GET['recom_referral']){
	$recom_sql = "select mb_id from g5_member where mb_no = '{$_GET['recom_referral']}'";
	$recom_result = sql_fetch($recom_sql);

	$mb_recommend = $recom_result['mb_id'];
}

function timeshift($time){
	return date("d/m/Y ",strtotime($time));
}
function shift_doller($val){
	return Number_format($val, 2);
}

$mb_id = $member['mb_id'];
$avatar_no = '1';

$avatar_cnt_sql = "select * from avatar_savings where mb_id = '{$mb_id}' and status != '1' order by update_date desc limit 0,1";
$av_result = sql_fetch($avatar_cnt_sql);

if(!$av_result['idx']){
	$idx = '1';
	$mode = 'w';
}else{
	$idx = $av_result['idx'];
	$mode = 'u';
}

$avatar_sql = "select * from avatar_savings where mb_id = '{$mb_id}' order by update_date asc";
$avatar_info = sql_query($avatar_sql);
?>

		
			<section class="avatar_wrap">
				<div class="ava_1st clear_fix">
					<span data-i18n="avatar.적립 목표">Savings target :</span>
					<select name="saving_target" id="saving_target">
						<?=option_selected(3000, $av_result['saving_target'], '3,000')?>
						<?=option_selected(4000, $av_result['saving_target'], '4,000')?>
						<?=option_selected(5000, $av_result['saving_target'], '5,000')?>
						<?=option_selected(6000, $av_result['saving_target'], '6,000')?>
						<?=option_selected(7000, $av_result['saving_target'], '7,000')?>
						<?=option_selected(8000, $av_result['saving_target'], '8,000')?>
						<?=option_selected(9000, $av_result['saving_target'], '9,000')?>
						<?=option_selected(10000, $av_result['saving_target'], '10,000')?>
					</select> USD
					<div style="margin:10px 0;"><span data-i18n="avatar.적립금" >Total savings</span> : <span class="font_green"><strong>$ <?=shift_doller($av_result['current_saving'])?></strong></span></div>
				</div>

				<hr>

				<div class="ava_select_wrap v_center">
					<!-- <p class="ava_title"><img src="_images/set_icon.gif" alt="이미지"> 적립 설정</p> -->
					<div class="img_select">
						<img src="<?=G5_THEME_URL?>/_images/ava_big_icon.png" alt="이미지">
						<b data-i18n="avatar.아바타 적금">Avatar Savings</b>
						<select name="saving_rate" id="saving_rate">
							<?=option_selected(10, $av_result['saving_rate'], '10%')?>
							<?=option_selected(20, $av_result['saving_rate'], '20%')?>
							<?=option_selected(30, $av_result['saving_rate'], '30%')?>
							<?=option_selected(40, $av_result['saving_rate'], '40%')?>
							<?=option_selected(50, $av_result['saving_rate'], '50%')?>
							<?=option_selected(60, $av_result['saving_rate'], '60%')?>
							<?=option_selected(70, $av_result['saving_rate'], '70%')?>
							<?=option_selected(80, $av_result['saving_rate'], '80%')?>
							<?=option_selected(90, $av_result['saving_rate'], '90%')?>
							<?=option_selected(100, $av_result['saving_rate'], '100%')?>
						</select>
						<p class="font_white" data-i18n="avatar.적립비율">Savings Rate</p>
					</div>
					<!--<input type="button" value="Save change" class="btn_basic ava_pop_open pop_open" data-i18n="[value]avartar.설정 저장">-->
					<input type="button" value="Save change" id="save_change" class="btn_basic"  data-i18n="[value]avartar.설정 저장">
				</div>

				<hr>

				<div class="ava_history_wrap v_center">
					<p class="ava_title"><img src="<?=G5_THEME_URL?>/_images/ava_icon.gif" alt="이미지" data-i18n="avatar.아바타 생성 기록"> Avatar Creation list</p>
					<ul class="clear_fix">
						<?
							while( $row = sql_fetch_array($avatar_info)){
						?>
							<?if($row['status'] == '1'){?>
								<li class="avatar_card active">
									<p class="av_num">Avatar <?=$row['avatar_no']?> </p>
									<p><span data-i18n="avatar.유저네임">Username</span> : <span class="av_id"><?=$row['avatar_id']?></span></p>
									<p><span data-i18n="avatar.생성일">Created on</span> : <span class="av_date"><?= timeshift($row['create_date'])?></span></p>
									<!--<p><span data-i18n="avatar.누적금액">Saving account</span> : <span class="av_id"><?=$row['current_saving']?></span></p>-->
								</li>
							<?}else{?>
								<li class="avatar_card">
								<p class="av_num">Avatar <?=$row['avatar_no']?> </p>
								<p><span data-i18n="avatar.적립금">Total savings</span> :<span class="av_id"> <span class="av_target"> <?=shift_doller($row['saving_target'])?> </span> / <?=shift_doller($row['current_saving'])?></span></p>
								<p><span class="av_id"> <span class="av_target"><?=$av_result['saving_rate']?>% </span></span></p>
							</li>
							<?}?>
						<?}?>

					</ul>
				</div>
			</section>
	
	
	<div class="gnb_dim"></div>

	<script>
		$(function() {
			$(".top_title h3").html("<img src='<?=G5_THEME_URL?>/_images/top_avatar.png' alt='아이콘'> <span data-i18n='title.아바타 적금'>Avatar Savings Account</span>");
			
		});
		function avatar_submit(f)
		{
			/*
			if (!is_checked("chk[]")) {
				alert(document.pressed+" 하실 항목을 하나 이상 선택하세요.");
				return false;
			}
			*/
			return true;
		}

		/*초기설정*/
		$('.agreement_ly').hide();
		$('.verify_phone').hide();
		$('#verify_txt').hide();



		/*이메일 체크*/
		validateEmail = function (email) {
			var email = email;
			var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;

			if (email == '' || !re.test(email)) {
				alert("올바른 이메일 주소를 입력하세요")
				return false;
			}
		}


		$('#btnSave').on('click',function(e) {
			$('#reg_mb_recommend').val($('#referral .modal-body .user.selected').html());
			$('#referral').modal('hide');
		});


		$('#save_change').on('click', function(e){
			/*
			if(!$('#reg_mb_hp').val()){
				commonModal('Mobile authentication','<p>Please enter your Mobile Number</p>',80);
				return;
			}
			*/

			console.log('saving_avatar');

			var mb_id = "<?=$mb_id?>";
			var avatar_no = "<?=$avatar_no?>";
			var idx = "<?=$av_result['idx']?>";
			var avatar_target = $('#saving_target').val();
			var avatar_rate = $('#saving_rate').val();
			var mode = "<?=$mode?>";

			$.ajax({
				url: '/util/avatar_saving.php',
				type: 'post',
				async: false,
				data: {
					"mb_id": mb_id,
					"avatar_no": avatar_no,
					"avatar_target" : avatar_target,
					"avatar_rate" : avatar_rate,
					"idx" : idx,
					"mode" : mode
				},
				dataType: 'json',
				success: function(result) {
					console.log(result.result);
					if(result.code != '0001'){
						dimShow();
						purchaseModal('Avatar setting','<p>'+result.sql+'</p>','success');
						
						$('#purchaseModal #modal_return_url').on('click', function () {
							location.reload();
						});

					}else{
						purchaseModal('Avatar setting','<p>Check and retry</p>','failed');
					}
				},
				error: function(e){
					console.log(e);
				}
			});
		});
	</script>


<? include_once(G5_THEME_PATH.'/_include/tail.php'); ?>

