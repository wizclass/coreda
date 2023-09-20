<?php
include_once('./_common.php');
include_once(G5_THEME_PATH.'/_include/gnb.php');
include_once(G5_THEME_PATH.'/_include/wallet.php');
/*
$math_percent_sql = "select  sum(mb_balance / mb_deposit_point) * 50 as percent from g5_member where mb_id =  '".$member['mb_id']."'";
$math_percent = sql_fetch($math_percent_sql);

$mb_benefit_total = number_format($member['mb_balance'],5); // 수당
$mb_upstair_acc = number_format($member['mb_deposit_acc'],5); // 누적된 업스테어 금액

if($mb_benefit_total != 0 ){ // 그래프 퍼센트

		$mb_out = number_format($math_percent['percent'],1);
		if($mb_out > 100){
			$mb_out = 100;
		}
	}else{
		$mb_out = 0;
	}
*/
?>


<!DOCTYPE html>
<html>

<head>
	<link rel="stylesheet" href="<?=G5_THEME_CSS_URL?>/history.css">
	<link rel="stylesheet" href="<?=G5_THEME_CSS_URL?>/upstairs.css">
</head>



<style>
.account{background:lightslategrey;margin-bottom:1em;}
.wallet{}
	.account_w{background:steelblue;}
	.trade{padding:30px 15px;background:steelblue;}
	hr{width:95%;border-top-style:dashed}
	.up_gage_box{background:teal;width:100%;margin:0 auto;}
	.guide_txt{margin:0;padding:0;font-size:14px;line-height:13px;text-align:right;color:white;margin-right:15px;margin-top:5px;font-weight:400;}
	.btnOut2{min-height:60px;width:70%}
	.modal .user.selected{background:#f9a62e;border:1px solid #f9a62e;color:black;font-weight:600}
	.pg_page, .pg_current{color:white;}
	.pg_current{color:black}
	</style>

	<body>

		<!-- <?include_once('mypage_head.php')?> -->

		<div class="main-container">
			<div id="body-wrapper" class="big-container-wrapper">
				<div class="crypto-wallets-container">
					<!-- <h2 class="gray" data-i18n="up.balance">BALANCE</h2> -->
					<div class="wallets-left-container">
						<!-- //BALANCE -->
						<section class="wallet">
							<div class="wallet_inner">

								<h3 class="balance"><span data-i18n="upstair.총 액수">Total Balance</span>  <span class="total_balance">
									<strong><?=$total_balance?></strong> </span> USDT</h3>

									<div class="coin_list">
										<div class="coin_img">
											<img src="" alt="">
											<p class="coin_name">USDT</p>
										</div>
										<div class="eos_balance">
											<p><strong> <?=$total_balance?></strong> USDT</p>
										</div>
									</div>

									<div class="coin_list">
										<p class="eos_up_name" >UPSTAIRS</p>
										<div class="eos_up_balance">
											<p> <strong><?=$mb_upstair;?></strong> USDT</p>
										</div>
									</div>

								</div>
							</section>
							<!-- //BALANCE -->


							<!-- GAUGE2 -->

							<div class="up_gage_box">
								<h2 data-i18n="upstair.보너스 수당">My Bonus For OUT</h2>
								<h3><strong><?=$mb_out?></strong> %</h3>
								<div class="progress2 progress-moved">
									<div class="progress-bar2" >
									</div>
								</div>
								<div class="gage_legend">
									<span>0%</span>
									<span>50%</span>
									<span>100%</span>
								</div>

								<!-- <?
								if($_SERVER['REMOTE_ADDR'] == "14.63.32.29" || $_SERVER['REMOTE_ADDR'] == "::1"){
								/*
								echo "upstar :". $EOS_UPSTAIR;
								echo "<br> EOS_OUT :". $EOS_OUT;
								*/
								echo "willsoft ADMIN ::".$member['mb_balance']."/".$member['mb_deposit_point'];
								echo "<br>".$mb_out;
								?>
								<?}?> -->

								<?if( $mb_out >= 100 ){?>
									<div style="margin:1em 0;"><button id="reset_btn" class="btnOut2">Upstair Reset</button></div>
									<?}?>


								</div>

								<script>
								$(function(){

									var uwp = '<?=$mb_out?>';
									var bcolor = '#318bc8';

									if(uwp > 0 && uwp < 30){
										bcolor= "#22a7f0";
									}else if (uwp >= 30 && uwp < 55){

										bcolor= "#f9a62e";
									}else if (uwp >= 55 && uwp < 70){

										bcolor= "#5333ed";
									}else if (uwp >= 70 && uwp < 100){
										bcolor= "#ff6600";

									}else if (uwp == 100){
										bcolor= "#f62459";
									}

									$(".progress-bar2").css( {"width" : uwp+"%", "background-color" : bcolor });
								});
								</script>
								<!-- //GAUGE2 -->

								<!-- 	Upstairs -->
								<h2 class="gray" style="margin-top:30px;" data-i18n="upstair.대문자 투자">UPSTAIRS</h2>
								<section class="trade">
									<div class="trade_inner">
										<div class="coin_img">
											<img src="" alt="">
											<p class="coin_name">USDT</p>
										</div>
										<div class="trade_info">
											<input type="text" id="trade_money_1" class="trade_money" placeholder="0" min=5 onkeyup="this.value=this.value.replace(/[^0-9]/g,'');">USDT
										</div>
									</div>
									<!--<p class="guide_txt" style="">It's only available in five units</p>-->

									<div class="trade_arrow">
										<i class="fas fa-angle-double-down"></i>
									</div>

									<div class="trade_inner">
										<div class="coin_img">
											<img src="" alt="">
											<p class="coin_name">UPSTAIRS</p>
										</div>
										<div class="trade_info">
											<input type="text" id="trade_money_2" class="trade_money" placeholder="0" min=5 readonly> USDT
										</div>
									</div>

									<!-- SUBMIT_BTN -->
									<div class="submit">
										<button id="exchange" class="btnOut2" data-i18n="upstair.대문자 투자" >UPSTAIRS</button>
									</div>
									<!-- //SUBMIT_BTN -->


								</div>
							</section>
							<!-- 	// Upstairs -->




							<?
							/*날짜선택 기본값 지정*/
							if (empty($fr_date)) {$fr_date = date("Y-m-d", strtotime(date("Y-m-d")."-3 month"));}
							if (empty($to_date)){$to_date =  date("Y-m-d", strtotime(date("Y-m-d")."+1 day"));}

							/*날짜계산*/
							$qstr = "stx=".$stx."&fr_date=".$fr_date."&amp;to_date=".$to_date;
							$query_string = $qstr ? '?'.$qstr : '';

							$sql_common ="FROM g5_shop_order";
							$sql_search = " WHERE mb_id = '{$member['mb_id']}' ";
							$sql_search .= " AND od_receipt_time between '{$fr_date}' and '{$to_date}' ";

							$reset_sql = "
							SELECT count(*) as cnt
							FROM g5_shop_upstair_reset_log
							WHERE mb_id = '{$member['mb_id']}'  AND od_date between '{$fr_date}' and '{$to_date}'";
							$reset_row = sql_fetch($reset_sql);
							$reset_count = $reset_row['cnt'];


							$sql = " select count(*) as cnt
							{$sql_common}
							{$sql_search} ";

							//print_r($sql);
							$row = sql_fetch($sql);
							$total_count = $row['cnt'] + $reset_count;

							$rows = 20; //한페이지 목록수
							$total_page  = ceil($total_count / $rows);
							if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지
							$from_record = ($page - 1) * $rows; // 시작 열

							$sql = " select mb_id, od_receipt_time, pv, od_status
							{$sql_common}
							{$sql_search} ";
							$sql .= "UNION
							SELECT mb_id,od_date, acc_num, current_deposit
							FROM g5_shop_upstair_reset_log
							WHERE mb_id = '{$member['mb_id']}'  AND od_date between '{$fr_date}' and '{$to_date}'";
							$sql .= "order by od_receipt_time desc
							limit {$from_record}, {$rows} ";
							$result = sql_query($sql);

							//print_R($sql );

							?>


							<!-- UPSTAIRS HISTORY -->
							<section class="history_box" style="margin-top:80px;">
								<h3 class="hist_tit" data-i18n="upstair.투자 내역" >Upstairs History</h3>

								<?while( $row = sql_fetch_array($result) ){?>
									<div class="hist_con">
										<div class="hist_con_row1">
											<div class="row1_left">
												<span class="hist_name">Upstairs</span><br>
												<span class="hist_date"><?=$row['od_receipt_time']?></span>
											</div>
											<div class="row1_right">

												<?if($row['od_status'] != '입금'){?>
													<span class="hist_value"><span style='color:red;font-weight:600'><strong> RESET  - <?=Number_format($row['pv'],0)?></strong> USDT</span></span>
													<?}else{?>
														<span class="hist_value"><strong><?=Number_format($row['pv'],5)?></strong> USDT</span>
														<?}?>
													</div>
												</div>
											</div>
											<?}?>

											<?php
											$pagelist = get_paging($config['cf_write_pages'], $page, $total_page, "{$_SERVER['SCRIPT_NAME']}?id=upstairs&$qstr");
											echo $pagelist;
											?>
											<div class="gnb_dim"></div>
										</section>

									</div>
									<!-- 	// Upstairs -->



									<!-- // 스폰서 추가 190619_soo-->

									<!-- <?
									$countbre = get_brecommend($member['mb_id']);
									if(!$countbre && $member['mb_deposit_point'] != 0){
									?> -->

									<!-- <script type="text/javascript">
									$( document ).ready(function(){
									$('#btnSearch2').click(function () {
									getUser('#reg_mb_brecommend','#sponsor');
								});


								/*추천인 후원인 찾기*/
								function getUser(etarget,type){
								console.log('sponsor');

								var target = etarget;
								var target_type = type;
								var target_modal = target_type + ' .modal-body .user';
								//console.log($(target).val() + " / "+ target_type);

								$.ajax({
								type:'GET',
								url:'ajax_sponsor_register.php',
								async: false,
								dataType: 'json',
								data: {
								input_id : $(target).val()
							} ,
							success: function(data){
							var list = (data);
							//console.log(data);

							if(list.length > 0){

							$(target_type).modal('show');

							var vHtml = $('<div>');
							$.each(list, function( index, obj ) {

							vHtml.append($('<div>').addClass('user').html(obj.mb_id));
						});

						$(target_type + ' .modal-body').html(vHtml.html());
					}else {
					//alert('MEMBER NOT FOUND');
					commonModal('Notice','MEMBER NOT FOUND',80);
				}
			}

		});

		$(document).on('click',target_modal,function(e) {
		$(target_modal).removeClass('selected');
		$(this).addClass('selected');
	});


	$('.btnSave').on('click',function(e) {
	$(target).val( $( target_modal + '.selected').html());
	$(target).attr("readonly",true);
	$('.register_btn_set').addClass('view');
	$(target_type).modal('hide');
});

}
});



function sponsor_confirm(e){
// Vaild Check 추가 할것!
var f = $('#Sponsorform');

//console.log(f.val())

if (typeof(f.mb_brecommend) != "undefined" && f.mb_recommend.value) {
if (f.mb_id.value == f.mb_recommend.value) {
commonModal('check recommend','<strong>please retry.</strong>',80);
f.mb_recommend.focus();
return false;
}
}

f.submit();

}

// 스폰서 리셋
function sponsor_reset(){
console.log('reset');
$('#reg_mb_brecommend').val('');
$('#reg_mb_brecommend').removeAttr("readonly");
$('.register_btn_set').removeClass('view');
}


</script> -->

<!-- <style>
#reg_mb_brecommend::placeholder{color:white;font-size:0.8em;}
#reg_mb_brecommend{font-size:1.2em;}
.search-button.reset{background:rgba(0,0,0,0.3)}
.search-button.send_btn{display:block}
.register_btn_set{display:none;}
.register_btn_set.view{display:block;}
</style> -->

<!-- <div class="roundbox" style="margin-bottom:20px;">
<h2 class="sponsor_tit"> Sponsor Register</h2>

<div class="sponsor_input">
<form id="Sponsorform" name="Sponsorform" action="./sponsor_proc.php" method="post" enctype="multipart/form-data" >
<div>
<input type="hidden"  name="link" value="<?=$_SERVER['PHP_SELF']?>" />
<input class="input-search" value="<?php echo $mb_brecommend ?>" type="text" name="mb_brecommend" id="reg_mb_brecommend"  placeholder="Sponsor's Username" data-i18n="[placeholder]register.Sponsor"  required >
<button class="search-button" type="button" data-i18n="register.search" id="btnSearch2" >Search</button>

<div class="register_btn_set">
<button type="button" class="search-button reset" id="register_reset" onclick="sponsor_reset();"> Reset</button>
<button type="button" class="search-button send_btn" id="register_btn" onclick="sponsor_confirm(this);"> Register Sponsor</button>
</div>

</div>
</form>
</div>


</div> -->
<!-- <?}?> -->
</div>

</div>
<!-- </div>
</div>
</div> -->

<div class="modal fade" id="ethereumAddressModalCenter" tabindex="-1" role="dialog"
aria-labelledby="ethereumAddressModalCenterTitle" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered" role="document">
	<div class="modal-content">
		<div class="modal-header">
			<h5 class="modal-title" id="ethereumAddressModalLongTitle"></h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<div class="modal-body">
			<i class="far fa-check-circle"></i>
			<h4></h4>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
		</div>
	</div>
</div>
</div>

</body>

<script>

$(function () {
	$(".top_title h3").html("<img src='<?=G5_THEME_URL?>/_images/top_wallet.png' alt='아이콘'> <span data-i18n='upstair.투자'>Upstairs</span>")

	var mb_id = "<?=$member['mb_id']?>";
	var mb_no = "<?=$member['mb_no']?>";
	var upstair_acc = "<?=$mb_upstair_acc?>";
	var mb_upstair = "<?=$mb_upstair?>";




	// 업스테어 금액입력하면 동비율로 입력
	$('#trade_money_1').on('keyup',function(e){
		$('#trade_money_2').val(Number($('#trade_money_1').val()));
	});

	// 업스테어 금액이 1000 넘으면안되고
	// $('#trade_money_1').change(function(){
	// 	if($('#trade_money_1').val() > 3000){
	// 		commonModal('check input amount','<strong> The amount cannot exceed 3,000.</strong>',80);
	// 	}
	// });



	$('#reset_btn').on('click', function(){


		$.ajax({
			type: "POST",
			url: "/util/upstairs_reset.php",
			cache: false,
			async: false,
			dataType: "json",
			data:  {
				"mb_id" : mb_id,
				"amount" : mb_upstair,
				"upstair_acc" : upstair_acc
			},
			success: function(data) {
				commonModal('Complete Upstair','<strong> Complete Upstair reset. <br> Available upstairs now!</strong>',80);

				$('#closeModal').on('click', function(){
					location.reload();
				});

			},
			error:function(e){

			}
		});

	});



	$('#exchange').on('click', function(){
		//console.log("upstair click");

		var balance = "<?=$total_balance ?>";
		var mb_out = "<?=$mb_out ?>";
		// var eos_upstair = "<?=$member['mb_deposit_point'] ?>";
		var mb_level = "<?=$member['mb_level']?>";

		var input_val = $('#trade_money_1').val();
		var calc_point = Number(mb_upstair) + Number(input_val);
		var nw_upstair = '<?=$nw_upstair?>';

		if(nw_upstair == 'Y'){
			//commonModal('service not available','<strong> the service will be avaiable shortly.</strong>',80);
			commonModal('<strong>Not available right now</strong>', '<i class="fas fa-exclamation-triangle red" style="font-size:2em;"></i><h4>Not available right now</h4>',120);
			return false;
		}

		// 업스테어 금액이 0이면안되고
		if($('#trade_money_1').val() <=0 ){
			commonModal('check input amount','<strong> Please check the input amount.</strong>',80);
			return false;
		}else if($('#trade_money_1').val() < 300 && mb_level <= 0){ // 레벨 0 회원은 최소 300불이상 부터 입금 가능
			commonModal('check input amount','<strong> The minimum deposit amount is $300.</strong>',80);
			return false;
		}

		// 업스테어  + 입력금액 합계가 1000 넘으면 안되고
		// if(calc_point > 3000){
		// 	commonModal('check input amount','<strong> The total points cannot exceed 3000.</strong>',80);
		// 	return false;
		// }

		if( mb_out >= 100 ){ // 리셋버튼 활성화 중 일때 업스테어 추가 금액 입력시
			commonModal(' You achieved 100% Upstairs','<strong> Please reset button for Upstair.</strong>',80);
			return false;
		}

		if(Number(conv_number(balance))<$('#trade_money_1').val()){// 업스테어 금액이 토탈 잔고를 넘으면 안되고
			commonModal('check your balance (USDT)','<strong> Not enough balance (USDT).</strong>',80);
			return false;
		}else{

			var save_usdt = Number($('#trade_money_1').val()); // 업스테어 신청금액

			$.ajax({
				type: "POST",
				url: "/util/upstairs_proc.php",
				cache: false,
				async: false,
				dataType: "json",
				data:  {
					"save_usdt": save_usdt,
					"mb_no" : mb_no,
					"mb_id" : mb_id,
					"coin_symbol" : "USDT"
				},
				success: function(data) {
					commonModal('Congratulation! Complete Deposit EOS','<strong> Congratulation! Complete Deposit USDT.</strong>',80);
					$('#closeModal').on('click', function(){
						location.reload();
					});

				},
				error:function(e){
					commonModal('처리 실패!','<strong> 다시 시도해주세요. 문제가 계속되면 관리자에게 연락주세요.</strong>',80);
				}
			});
		}
	});
});

/*콤마제거숫자표시*/
function conv_number(val) {
	number = val.replace(',', '');
	return number;
}
</script>
</html>
