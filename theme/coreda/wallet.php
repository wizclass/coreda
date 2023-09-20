<?
include_once(G5_THEME_PATH.'/_include/head.php'); 
include_once(G5_THEME_PATH.'/_include/gnb.php'); 

include_once(G5_THEME_PATH.'/_include/wallet.php'); 


?>

		<div class="v_center dash_contents">

			<section class="wallet_wrap">
				<h5><span data-i18n='wallet.지갑 총 잔고'>Total wallet balance : </span> <span><?=$total_rate?> USD</span></h5>
				<div>
					<div class="color_block bit_block">
						<a href="transaction_bit.php">
							<div class="clear_fix">
								<strong class="f_left">Bitcoin</strong>
								<b class="f_right"><?=$btc_account?> BTC</b>
							</div>
							<div class="clear_fix">
								<span class="f_left">= <?=$btc_cost?> USD</span>
								<p class="f_right"><?= $btc_rate?>USD</p>
							</div>
						</a>
					</div>

					<div class="color_block v7_block">
						<a href="transaction_v7.php">
							<div class="clear_fix">
								<strong class="f_left">V7</strong>
								<b class="f_right"><?=$v7_account?> V7</b>
							</div>
							<div class="clear_fix">
								<span class="f_left">= <?=$v7_cost?> USD</span>
								<p class="f_right"><?=$v7_rate?> USD</p>
							</div>
						</a>
					</div>

					
					<div class="color_block eth_block">
						<!--<a href="transaction_eth.php">-->
						<a href="javascript:serviceModal();">
							<div class="clear_fix">
								<strong class="f_left">Ethereum</strong>
								<b class="f_right"><?=$eth_account?> ETH</b>
							</div>
							<div class="clear_fix">
								<!--<span class="f_left">=<?=$eth_cost?> USD</span>-->
								<p class="f_right"><?=$eth_rate?> USD</p>
							</div>
						</a>
					</div>
					<div class="color_block rock_block">
						<!--<a href="transaction_rock.php">-->
						<a href="javascript:serviceModal();">
							<div class="clear_fix">
								<strong class="f_left">Rockwood</strong>
								<b class="f_right"><?=$rwd_account?> RWD</b>
							</div>
							<div class="clear_fix">
								<!--<span class="f_left">=<?=$rwd_cost?> USD</span>-->
								<p class="f_right"><?=$rwd_rate?> USD</p>
							</div>
						</a>
					</div>
					<div class="color_block look_block">
						<!--<a href="transaction_look.php">-->
						<a href="javascript:serviceModal();">
							<div class="clear_fix">
								<strong class="f_left">Lukiu</strong>
								<b class="f_right"><?=$lok_account?> LKU</b>
							</div>
							<div class="clear_fix">
								<!--<span class="f_left">=<?=$lok_cost?> USD</span>-->
								<p class="f_right"><?=$lok_rate?> USD</p>
							</div>
						</a>
					</div>
					
							
				</div>
			</section>
		</div>
		
		<div class="gnb_dim"></div>
	</section>



	<script>
		$(function(){
			$(".top_title h3").html("<img src='<?=G5_THEME_URL?>/_images/top_wallet.png' alt='아이콘'> <span data-i18n='title.크립토 월렛'>Crypto Wallets</span>");
		});
	</script>

	<script type='text/javascript'>

		$(document).ready(function(){

			var wallet = '<?=$member['mb_wallet']?>';
			var mb_id = '<?=$member['mb_id']?>';
			var mb_email = '<?=$member['mb_email']?>';
			
			console.log(wallet.length);

			if(wallet.length < 20){
				console.log('지갑생성');
				
				$('.loader').css("display", "block");
				$('.loader .comment').html("Generating wallet address.<br>please wait moment.");
				
				
				$.ajax({
					type: 'POST',
					url: g5_url + '/wallet_create.php',
					async: true,
					dataType: 'json',
					data:  {
						'mb_id' : mb_id,
						'mb_email' : mb_email
					},
					success: function(data) {
						$('.loader').css('display','none');
						$('.dim').css("display", "none");
						$('.dim').empty();
						$('body').css({
							"overflow": "auto",
							"height": "inherit"
						});
						
						if(data.error == '' || data.result == 'success' ){
							commonModal(' New wallet create','<strong> Congratulations! Your wallet has been successfully created.</strong>',80);	
							$('#closeModal').on('click', function(){
								location.href = "/wallet/wallet.php?id=wallet";
							});
						}else{
							commonModal('Generate Wallet Failed.','<strong> Please Retry / If the problem persists, please contact the administrator.</strong>',80);	
						}
					},
					error:function(error){
						console.log('error : ' + error);
					}
				});
				
			}
		});
		
	</script>

<? include_once(G5_THEME_PATH.'/_include/tail.php'); ?>

