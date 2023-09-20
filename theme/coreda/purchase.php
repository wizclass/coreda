<?
	include_once('./_common.php');
	include_once(G5_THEME_PATH.'/_include/gnb.php'); 
	include_once(G5_THEME_PATH.'/_include/shop.php');
	
	login_check($member['mb_id']);

	echo "<br>";
	echo "=====================";
	print_R('sadfasdfasdf');
	echo "=====================";
	echo "<br>";
	print_r($nw['nw_purchase']);

	if($nw['nw_purchase'] == 'Y'){
		$nw_purchase = 'Y';
		include_once(G5_PATH.'/service_pop.php');
	}else{
		$nw_purchase = 'N';
		alert("현재 서비스를 이용할수없습니다.");
		break;
	}
?>
	<script>
		$(function() {
			var chb = ('.purchase_wrap input[type=checkbox]');

			$(chb).change(function(){

				var thisname = ($(this).data("name"));

				console.log( $(this).is(":checked") );
				/*
				if($(chb).is(":checked")){
					
				}else{
					$(this).closest('tr').find('input:radio').prop("checked",false);
				}
				*/

				if($(this).is(":checked")){
					
					$('.'+ thisname + ' .b_check').prop("checked",false);
					$(this).closest('tr').find('input:radio').prop("checked",true);
					$(this).prop("checked",true);
				}else{
					$(this).prop("checked",false);
				}
			});

			// X 취소
			$('.purchase_wrap table td:last-child').click(function(){
				$(this).parent().find('input:radio').prop("checked",false);
				$(this).parent().find('input:checkbox').prop("checked",false);
			});

			$('.purchase_wrap input:radio').click(function(){
				$(this).parent().find('input:checkbox').prop("checked",false);
			});

});
</script>

		<section class="v_center purchase_wrap">

		<form id="puchaseBForm" name="puchaseBForm" action="<?=G5_URL?>/page.php?id=purchase_order" onsubmit="return fitemlist_submit(this);" method="post" >
			<div >
				<!--
				<table class="bpack">
					<tr>
						<th> <span data-i18n="purchase.B 팩">B Packs</span></th>
						<th><span data-i18n="purchase.가격">Price</span></th>
						<th><span data-i18n="purchase.선택">Select</span></th>
						<th><span data-i18n="purchase.자동">Auto Repurchase</span></th>
						<th><span data-i18n="purchase.취소">Cancel</span></th>
					</tr>
					<?
						$result = shop_item('10');
						$i = 1;
						while( $row = sql_fetch_array($result)){
					?>
						<tr>
							<td><?=$row['it_name']?></td>
							<td >&#36;<?=$row['it_price']?></td>
							<td>
								<div class="radio_box"> 
									<input type="radio" id="b_rd<?=$i?>" data-name="bpack" class="b_radio" name="b_it_id" value="<?=$row['it_id']?>"> 
									<label for="b_rd<?=$i?>"></label> 
								</div>
							</td>
							<td>
								<div class="round_chkbox">
								<input type="checkbox" id="b_chk<?=$i?>" data-name="bpack" class="b_check" name="b_chk" value="<?=$i?>">
								<label for="b_chk<?=$i?>"><span></span></label>
								</div>
							</td>
							<td class="cancel">X</td>
						</tr>
						
					<? $i++;}?>

				</table>

				<p class="description">
				<span data-i18n="purchase.B팩 설명1"> Purchase B Pack for daily binary bonus qualification.</span><br>
				<span data-i18n="purchase.B팩 설명2"> The purchase is valid for 30 days from the date of the purchase.</span><br>
				<span data-i18n="purchase.B팩 설명3"> Only one pack is to select at a time. </span><br>
				<span data-i18n="purchase.B팩 설명4"> Enable Auto Repurchase if you want to activate automatic recurring purchase</span>
				</p>
				-->

				<table class="qpack">
					<tr>
						<th> <span data-i18n="purchase.Q 팩">Q Packs</span></th>
						<th><span data-i18n="purchase.가격">Price</span></th>
						<th><span data-i18n="purchase.선택">Select</span></th>
						<th><span data-i18n="purchase.자동">Auto Repurchase</span></th>
						<th><span data-i18n="purchase.취소">Cancel</span></th>
					</tr>
					<?
						$result = shop_item('20');
						$i = 1;
						while( $row = sql_fetch_array($result)){
					?>
						<tr>
							<td><?=$row['it_name']?></td>
							<td >&#36;<?=$row['it_price']?></td>
							<td>
								<div class="radio_box"> 
									<input type="radio" id="q_rd<?=$i?>" data-name="qpack" class="q_radio" name="q_it_id" value="<?=$row['it_id']?>"> 
									<label for="q_rd<?=$i?>"></label> 
								</div>
							</td>
							<td>
								<div class="round_chkbox">
								<input type="checkbox" id="q_chk<?=$i?>" data-name="qpack" class="q_radio" name="q_chk" value="<?=$i?>">
								<label for="q_chk<?=$i?>"><span></span></label>
								</div>
							</td>
							<td class="cancel">X</td>
						</tr>
						
					<? $i++;}?>

				</table>

				<p class="description">
				<span data-i18n="purchase.Q팩 설명1"> Purchase Q Pack to maintain your rank and receive Rank Bonus  and Share Bonus.</span><br>
				<span data-i18n="purchase.Q팩 설명2"> The purchase is valid for 30 days from the date of the purchase.</span><br>
				<span data-i18n="purchase.Q팩 설명3"> Only one pack is to select at a time. </span><br>
				<span data-i18n="purchase.Q팩 설명4"> Enable Auto Repurchase if you want to activate automatic recurring purchase</span>
				</p>
				
				<!--
				<table>
					<tr>
						<th>Q 팩</th>
						<th>가격</th>
						<th>선택</th>
						<th>자동 <span class="m_br">재구매</span></th>
						<th>취소</th>
					</tr>
					<tr>
						<td>Q1</td>
						<td>&#36;1,000</td>
						<td>
							<div class="radio_box"> 
								<input type="radio" id="p_rd4" name="q_rd1"> 
								<label for="p_rd4"></label> 
							</div>
						</td>
						<td>
							<div class="round_chkbox">
							  <input type="checkbox" id="q_chk4">
							  <label for="q_chk4"><span></span></label>
							</div>
						</td>
						<td>X</td>
					</tr>
					<tr>
						<td>Q2</td>
						<td>&#36;2,000</td>
						<td>
							<div class="radio_box"> 
								<input type="radio" id="p_rd5" name="q_rd1"> 
								<label for="p_rd5"></label> 
							</div>
						</td>
						<td>
							<div class="round_chkbox">
							  <input type="checkbox" id="q_chk5">
							  <label for="q_chk5"><span></span></label>
							</div>
						</td>
						<td>X</td>
					</tr>
					<tr>
						<td>Q3</td>
						<td>&#36;3,000</td>
						<td>
							<div class="radio_box"> 
								<input type="radio" id="p_rd6" name="q_rd1"> 
								<label for="p_rd6"></label> 
							</div>
						</td>
						<td>
							<div class="round_chkbox">
							  <input type="checkbox" id="q_chk6">
							  <label for="q_chk6"><span></span></label>
							</div>
						</td>
						<td>X</td>
					</tr>
					<tr>
						<td>Q4</td>
						<td>&#36;5,000</td>
						<td>
							<div class="radio_box"> 
								<input type="radio" id="p_rd7" name="q_rd1"> 
								<label for="p_rd7"></label> 
							</div>
						</td>
						<td>
							<div class="round_chkbox">
							  <input type="checkbox" id="q_chk7">
							  <label for="q_chk7"><span></span></label>
							</div>
						</td>
						<td>X</td>
					</tr>
					<tr>
						<td>Q5</td>
						<td>&#36;7,000</td>
						<td>
							<div class="radio_box"> 
								<input type="radio" id="p_rd8" name="q_rd1"> 
								<label for="p_rd8"></label> 
							</div>
						</td>
						<td>
							<div class="round_chkbox">
							  <input type="checkbox" id="q_chk8">
							  <label for="q_chk8"><span></span></label>
							</div>
						</td>
						<td>X</td>
					</tr>
					<tr>
						<td>Q6</td>
						<td>&#36;10,000</td>
						<td>
							<div class="radio_box"> 
								<input type="radio" id="p_rd9" name="q_rd1"> 
								<label for="p_rd9"></label> 
							</div>
						</td>
						<td>
							<div class="round_chkbox">
							  <input type="checkbox" id="q_chk9">
							  <label for="q_chk9"><span></span></label>
							</div>
						</td>
						<td>X</td>
					</tr>
					<tr>
						<td>Q7</td>
						<td>&#36;20,000</td>
						<td>
							<div class="radio_box"> 
								<input type="radio" id="p_rd10" name="q_rd1"> 
								<label for="p_rd10"></label> 
							</div>
						</td>
						<td>
							<div class="round_chkbox">
							  <input type="checkbox" id="q_chk10">
							  <label for="q_chk10"><span></span></label>
							</div>
						</td>
						<td>X</td>
					</tr>
				</table>
				<p>
					직급유지와 직급 수당, 공유 수당을 받으려면 Q 팩을 구매해야 합니다.<br/>
					구매일로 부터 30일간 유효합니다.<br/>
					한번에 한 개의 상품만 살 수 있습니다.<br/>
					자동 재구매를 활성화하면 매월 자동으로 재구매가 됩니다.
				</p>
				-->
				
			</div>
			
			
			<div class="btn2_wrap">
				<input type="button" value='' onclick=" history.back();" data-i18n="[value]취소">
				<input type="submit" value=''  data-i18n="[value]다음단계">
			</div>
			</form>
		</section>

		<div class="gnb_dim"></div>

	</section>



	<script>
		$(function() {
			$(".top_title h3").html("<img src='<?=G5_THEME_URL?>/_images/top_purchase.png' alt='아이콘'><span data-i18n='title.팩상품구매하기'> Purchase Packs </span>");
			
		});

		function fitemlist_submit(f)
		{
			/*
			if (!is_checked("chk[]")) {
				alert(document.pressed+" 하실 항목을 하나 이상 선택하세요.");
				return false;
			}
			*/
			return true;
		}

	</script>


<? include_once(G5_THEME_PATH.'/_include/tail.php'); ?>

