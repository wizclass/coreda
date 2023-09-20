
<?
include_once(G5_THEME_PATH.'/_include/head.php'); 
include_once(G5_THEME_PATH.'/_include/gnb.php');
include_once(G5_THEME_PATH.'/_include/wallet.php'); 
//print_r($member);


$sql = " select * from wallet_income";
$nw = sql_fetch($sql);

if($nw['nw_with'] == 'Y'){
	$nw_with = 'Y';
}else{
	$nw_with = 'N';
}

if($nw_with == 'Y'){
	include_once(G5_PATH.'/service_pop.php');
}

$address  = $member['mb_wallet'];
?>

			<section class="con90_wrap">
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

				
            <script src="../js/qrcode.js"></script>

            <script type="text/javascript">
                $(function(){
                    $('#qr_code').empty();
                    new QRCode(document.getElementById("qr_code"), {
                    text: '<?=$address?>',
                    width: 300,
                    height: 300,
                    colorDark : "#000000",
                    colorLight : "#ffffff",
                    correctLevel : QRCode.CorrectLevel.H
                    });	
                });
            </script>

            <style>
                #qr_code img{text-align:center;display:inline !important;}
                .btn.primary{background:#00bff3;color:white;}
                .btn.primary:hover{background:royalblue;}
                .wallet_addr{width:auto; padding:0 20px;font-size:1.1em; word-break:break-all;}
            </style>

            

            <?if($address){?>
            <section class="con90_wrap" style="text-align:center">
            
				<div class="qr_wrap mc_bit" id='qr_code' style="text-align:center">
					<!-- <img src="_images/qr_img.gif" alt="큐알"> -->
					<!-- <input type="text" placeholder="받을 코인 숫자 입력"> -->
				</div>		
                <p>Wallet Address </p>
                <h5 class="wallet_addr"><?=$address?></h5>
                <p style="margin-top:10%;"><input type="button" class="btn primary" onclick="copyToClipboard1('.wallet_addr');" style="width:200px;height:40px;box-shadow:none;" value="Copy Address"/></p>
            </section>
            <?}else{?>
                <div style="width:90%;text-align:center;display:table;margin:30px auto;">
                    <div style="width:100%;height:400px; display:table-row;">
                    <p style="display:table-cell;vertical-align:middle;background:#f5f5f5;border-radius:15px;line-height:30px;">
                    Service is being prepared.<br>
                    this Service Will be avaiable shortly.</p>
                    </div>
                </div>
            <?}?>

	<script>
		$(function() {
			$(".top_title h3").html("<img src='<?=G5_THEME_URL?>/_images/top_send.png' alt='아이콘'> <span data-i18n='title.비트코인 보내기'>Receive Bitcoin</span>");
			
        });
        
        function copyToClipboard1(element) {

        commonModal("Address copy",'Your Wallet address is copied!',100);
        var $temp = $("<input>");
        $("body").append($temp);
        
        $temp.val($(element).text()).select();
            document.execCommand("copy");
        $temp.remove();
        }

	</script>


<? include_once(G5_THEME_PATH.'/_include/tail.php'); ?>