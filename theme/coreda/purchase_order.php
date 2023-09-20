<?
	include_once('./_common.php');
	include_once(G5_THEME_PATH.'/_include/gnb.php'); 
	include_once(G5_THEME_PATH.'/_include/wallet.php'); 
	include_once(G5_THEME_PATH.'/_include/shop.php');
	//print_r($member);

	login_check($member['mb_id']);

	//print_r($_POST);

	$today = date("Y-m-d", time());


	if($_POST['b_it_id']){ // B팩
		$orderBitem = get_shop_item($_POST['b_it_id']);
		$b_it_auto = $_POST['b_chk'];
		$b_it_id =  $orderBitem['it_id'];
		$b_it_name = $orderBitem['it_name']; 
		$b_it_price = $orderBitem['it_price']; 
		$cnt = 1; 
	}


	if($_POST['q_it_id']){	// Q팩
		$orderQitem = get_shop_item($_POST['q_it_id']);
		$q_it_auto = $_POST['q_chk'];
		$q_it_id =  $orderQitem['it_id'];
		$q_it_name = $orderQitem['it_name']; 
		$q_it_price = $orderQitem['it_price']; 
		$cnt += 1;
	}
	
	$cp_price =  conv_number($b_it_price + $q_it_price) ; // 달러가격 
	$order_price = calc_price($b_it_price + $q_it_price, $btc_cost,'btc'); // btc환산가격 
	

	/*회원구매기록 변경*/

	$cart_b_sql = "SELECT * from g5_shop_cart where mb_id ='{$member['mb_id']}' and  DATE(ct_time) <= '{$today}' and  DATE(ct_select_time) > '{$today}' and it_sc_type = 10 order by ct_time desc limit 0,1";
	$cart_b_list = sql_fetch($cart_b_sql);

	$cart_q_sql = "SELECT * from g5_shop_cart where mb_id ='{$member['mb_id']}' and DATE(ct_time) <= '{$today}' and  DATE(ct_select_time) > '{$today}' and it_sc_type = 20 order by ct_time desc limit 0,1";
	$cart_q_list = sql_fetch($cart_q_sql);

	if($orderBitem && $cart_b_list){
		$expire_item_b = $cart_b_list['it_name'];
		$expire_date_b = $cart_b_list['ct_select_time'];
	}
	
	if($orderQitem && $cart_q_list){
		$expire_item_q = $cart_q_list['it_name'];
		$expire_date_q = $cart_q_list['ct_select_time'];
	}
	
?>

		<section class="v_center purchase1_wrap wrap">
		<form id="purchaseForm" name="purchaseForm" action="<?=G5_URL?>/page.php?id=purchase_order_proc"   method="post" >
			
			<input type="hidden" name="b_it_name" value="<?=$b_it_name?>">
			<input type="hidden" name="q_it_name" value="<?=$q_it_name?>">

			<input type="hidden" name="b_it_id" value="<?=$b_it_id?>">
			<input type="hidden" name="b_it_auto" value="<?=$b_it_auto?>">
			<input type="hidden" name="q_it_id" value="<?=$q_it_id?>">
			<input type="hidden" name="q_it_auto" value="<?=$q_it_auto?>">
			<input type="hidden" name="order_price" value="<?=$order_price?>">
			<input type="hidden" name="total_cp_price" value="<?=$cp_price?>">
			<input type="hidden" name="mb_id" value="<?=$member['mb_id']?>">
			<input type="hidden" name="coin" id="coin" value="">
			<input type="hidden" name="expire_date_q" value="<?=$expire_date_q?>">
			<input type="hidden" name="expire_date_b" value="<?=$expire_date_b?>">
			<input type="hidden" name="cnt" value="<?=$cnt?>">

			<h5 data-i18n="purchase.구매하는 팩 상품">Packs to purchase</h5>
			
			<table>
				<tbody>
				  <colgroup>
					<col style="width: 30%;">
					<col style="width: 30%;">
					<col style="width: 30%;">
					<col style="width: 10%;">
				  </colgroup>
				  <!--
					<tr>
						<td data-i18n="binary.바이너리 팩">B Pack</td>
						<td><?=$b_it_name?><?if($b_it_auto) echo " (Auto)";?></td>
						<td>&#36;<?=shift_doller($b_it_price)?></td>
						
					</tr>
				  -->
					<tr>
						<td data-i18n="binary.큐팩">Q Pack</td>
						<td><?=$q_it_name?> <?if($q_it_auto) echo " (Auto)";?></td>
						<td>&#36;<?=shift_doller($q_it_price)?></td>
						
					</tr>
					
				</tbody>
				<tfoot>
					<td data-i18n="purchase.합계"> Total:</td>
					<td></td>
					<td>&#36;<?=shift_doller($b_it_price + $q_it_price)?></td>
					<td></td>
				</tfoot>

			</table>

			<h5 data-i18n="purchase.지불 방식을 선택하세요">Select the method of payment</h5>
			<div class="payment_div">
				<p data-i18n="purchase.지갑 잔고로 지불">Pay with the wallet balance</p>
				<ul class="pay_coin bit_color">
					<li><img src="<?=G5_THEME_URL?>/_images/bit_round.gif" alt="비트코인"></li>
					<li>
						<div>
							<span data-i18n="purchase.잔고">Balance</span>
							<b><?=$btc_account?>BTC ($<?=$btc_rate?>)</b>
						</div>
						<div class="coin_color">
							<span data-i18n="purchase.지불할 코인 갯수">Amount to pay :</span>
							<b><?= $order_price?>BTC</b>
						</div>
					</li>
					<li>
						<input type="button" value="Pay" data-val="btc" id="purchase_btn" class="purchase_btn" onclick="pay_submit(); document.getElementById('coin').value = this.dataset.val; " data-i18n="[value]purchase.결제" >
					</li>
				</ul>
				<!--
				<ul class="pay_coin eth_color">
					<li><img src="<?=G5_THEME_URL?>/_images/eth_round.gif" alt="이더리움"></li>
					<li>
						<div>
							<span>잔고</span>
							<b>1.5215BTC ($12,000.52)</b>
						</div>
						<div class="coin_color">
							<span>지불할 코인 갯수:</span>
							<b>0.15425BTC</b>
						</div>
					</li>
					<li>
						<input type="button" value="결제" onclick="location.href='purchase_3.php'">
					</li>
				</ul>
				<ul class="pay_coin rock_color">
					<li><img src="<?=G5_THEME_URL?>/_images/rock_round.gif" alt="락우드"></li>
					<li>
						<div>
							<span>잔고</span>
							<b>1.5215BTC ($12,000.52)</b>
						</div>
						<div class="coin_color">
							<span>지불할 코인 갯수:</span>
							<b>0.15425BTC</b>
						</div>
					</li>
					<li>
						<input type="button" value="결제" onclick="location.href='purchase_3.php'">
					</li>
				</ul>
-->
			</div>
			<!--
			<hr>
			<div class="payment_div">
				<p>외부 지갑에서 지불</p>
				<ul class="pay_coin bit_color">
					<li><img src="<?=G5_THEME_URL?>/_images/bit_round.gif" alt="비트코인"></li>
					<li>
						<div class="coin_color">
							<span>지불할 코인 갯수:</span>
							<b>0.15425BTC</b>
						</div>
					</li>
					<li>
						<input type="button" value="인보이스 생성" onclick="location.href='purchase_3_ext.php'">
					</li>
				</ul>
				<ul class="pay_coin eth_color">
					<li><img src="<?=G5_THEME_URL?>/_images/eth_round.gif" alt="이더리움"></li>
					<li>
						<div class="coin_color">
							<span>지불할 코인 갯수:</span>
							<b>0.15425BTC</b>
						</div>
					</li>
					<li>
						<input type="button" value="인보이스 생성" onclick="location.href='purchase_3_ext.php'">
					</li>
				</ul>
				<ul class="pay_coin rock_color">
					<li><img src="<?=G5_THEME_URL?>/_images/rock_round.gif" alt="락우드"></li>
					<li>
						<div class="coin_color">
							<span>지불할 코인 갯수:</span>
							<b>0.15425BTC</b>
						</div>
					</li>
					<li>
						<input type="button" value="인보이스 생성" onclick="location.href='purchase_3_ext.php'">
					</li>
				</ul>
			</div>
-->


			<div>
				<input type="button" value="Cancel the purchase" class="btn_basic_block" onclick="history.back();" data-i18n="[value]purchase.구매 취소">
			</div>

		</section>

		<div class="gnb_dim"></div>

	</section>



	<script>
		$(function() {
			$(".top_title h3").html("<img src='<?=G5_THEME_URL?>/_images/top_purchase.png' alt='아이콘'> <span data-i18n='title.팩상품구매하기'> Purchase Packs </span>");
			
		});


		function pay_submit(){

			var btc_account = "<?=$btc_account?>";
			var order_price = "<?=$order_price?>";
			
			var item_q = "<?=$expire_item_q?>";
			var expire_q = "<?=$expire_date_q?>";
			var order_q = "<?=$q_it_name?>";
			
			var item_b = "<?=$expire_item_b?>";
			var expire_b = "<?=$expire_date_b?>";
			var order_b = "<?=$b_it_name?>";

			var have_msg ='';
			
			if(btc_account < order_price){
                commonModal('check wallet balance','<strong>please check wallet balance.</strong>',80);
                return false;
            }

			if( (item_q && expire_q) || (item_b && expire_b) ){

				if(item_b && expire_b){
					have_msg += "<br> Pack Item : " + item_b + " <br> Expire date : " + expire_b;
				}
				if(item_q && expire_q){
					have_msg += "<br> Pack Item : " + item_q + "<br> Expire date : "+ expire_q;
				}
				
				purchaseModal('You already have the purchase history.', have_msg + " <br><br>you have still a valid period of time for the product. <br>Do you want to extend period?" , 'confirm');

				$('#purchaseModal #modal_confirm').on('click', function () {
					//console.log('ok');
					document.getElementById('purchaseForm').submit();
				});
			}else{
				document.getElementById('purchaseForm').submit();
			}
			
		}
	</script>



<? include_once(G5_THEME_PATH.'/_include/tail.php'); ?>
