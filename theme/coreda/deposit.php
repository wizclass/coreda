<?
include_once(G5_THEME_PATH.'/_include/head.php');
include_once(G5_THEME_PATH.'/_include/gnb.php');
?>
<script type="text/javascript" src="./js/qrcode.js"></script>


<section class="v_center structure_wrap">

  <!-- WALLET_ADRESS
  <section class="wallet_box">

    <h1 class="wallet_title">Ethereum address</h1>
    <div class="wallet qrBox">
      <div class="eth_qr_img qr_img" id="eth_qr_img"></div>
      <input type="text" id="eth_wallet_addr" class="wallet_addr" value="" title='my address'/>
    </div>

    <div class="btn_ly">
      <button class="btn wide blue" id="accountCopy" onclick="copyURL('eth_wallet_addr')"><i class="ri-file-copy-2-line"></i>Copy</button>
    </div>
  </section>
  -->

  <section class="wallet_box">
      <!-- WALLET_ADRESS -->
      <h1 class="wallet_title" data-i18n="deposit.USDT 입금 지갑 주소">USDT Deposit Wallet Address</h1>

      <!-- WALLET_ADRESS_QR -->
      <section class="wallet qrBox">
        <div class="usdt_qr_img qr_img" id="usdt_qr_img"></div>
        <input type="text" id="usdt_wallet_addr" class="wallet_addr" value="" title='my address'/>
      </section>

      <div class="btn_ly">
        <button class="btn wide blue" id="accountCopy" onclick="copyURL('usdt_wallet_addr')"><i class="ri-file-copy-2-line" style='float:left'></i><div data-i18n="deposit.복사">

        </div></button>
      </div>
  </section>


  <div class="gnb_dim"></div>

	</section>


<script type="text/javascript">
$(function(){
	$(".top_title h3").html("<img src='<?=G5_THEME_URL?>/_images/top_wallet.png' alt='아이콘'> <span data-i18n='deposit.입금'>Deposit</span>")

  var eth_wallet_addr = "0x286704d10f39a874148ff3d3ee70c1c76fd9bfb0";
  var usdt_wallet_addr = "0xD566877a3bC69671051443f295648c4fdfCCec36";

  //$('#eth_wallet_addr').val(eth_wallet_addr);
  $('#usdt_wallet_addr').val(usdt_wallet_addr);

  //generateQrCode("eth_qr_img",eth_wallet_addr, 150, 150);
  generateQrCode("usdt_qr_img",usdt_wallet_addr, 150, 150);

});

function copyURL(addr){
  $('#'+addr).select();
  document.execCommand("copy");
  alert("지갑 주소가 복사 되었습니다");
}

function generateQrCode(qrImg, text, width, height){
  return new QRCode(document.getElementById(qrImg), {
    text: text,
    width: width,
    height: height,
    colorDark : "#000000",
    colorLight : "#ffffff",
    correctLevel : QRCode.CorrectLevel.H
  });
}
</script>

<? include_once(G5_THEME_PATH.'/_include/tail.php'); ?>
