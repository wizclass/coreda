<?php
$sub_menu = '850150';
include_once('./_common.php');
include_once(G5_EDITOR_LIB);
auth_check($auth[$sub_menu], "r");


$g5['title'] = '부분 서비스 제어 설정';
include_once (G5_ADMIN_PATH.'/admin.head.php');


$sql = " select * from maintenance";
$nw = sql_fetch($sql);

/*
$nw['use'] = 'both';
$nw['title'] = 24;
$nw['contents']   = 10;
*/

include_once (G5_ADMIN_PATH.'/admin.head.php');
?>

<link rel="stylesheet" href="/adm/css/switch.css">
<link href="<?=G5_ADMIN_URL?>/css/scss/admin.sub.switch.css" rel="stylesheet">
<form name="frmnewwin" action="./admin.sub.switch.proc.php" onsubmit="return frmnewwin_check(this);" method="post">
<input type="hidden" name="w" value="<?php echo $w; ?>">

<div class="local_desc01 local_desc">
    <p> 서비스 사용설정을 안함으로 하면 해당 메뉴/서비스가 중지됩니다.<br>

	</p>
</div>

<div class="tbl_frm01 tbl_wrap">
    <table>
    <caption><?php echo $g5['title']; ?></caption>
    <colgroup>
        <col class="grid_4">
        <col>
    </colgroup>
    <tbody>
    
	<tr>
		<!-- <th scope="row"><label for="nw_change"> 입금 요청<strong class="sound_only"> 필수</strong></label></th>
		<td>
            <p style="padding:0;"><input type="checkbox" id="nw_change" name="nw_change" <?if($nw['nw_change'] == 'Y') {echo "checked";}?>/><label for="nw_change" style=""><span class="ui"></span><span class="nw_change_txt">사용 설정</span></label></p>
		</td>

		<th scope="row"><label for="nw_change"> 입금<strong class="sound_only"> 필수</strong></label></th>
		<td>
            <p style="padding:0;"><input type="checkbox" id="nw_change" name="nw_change" <?if($nw['nw_change'] == 'Y') {echo "checked";}?>/><label for="nw_change" style=""><span class="ui"></span><span class="nw_change_txt">사용 설정</span></label></p>
		</td> 
 		-->
		<th scope="row"><label for="nw_with">입출금<strong class="sound_only"> 필수</strong></label></th>
        <td>
            <p style="padding:0;"><input type="checkbox" id="nw_with" name="nw_with" <?if($nw['nw_with'] == 'Y') {echo "checked";}?>/><label for="nw_with" style=""><span class="ui"></span><span class="nw_with_txt">사용 설정</span></label></p>
        </td>
	</tr>
	
	<tr>
		<!-- <th scope="row"><label for="nw_upstair"> 예치금 전환 금지<strong class="sound_only"> 필수</strong></label></th>
		<td>
            <p style="padding:0;"><input type="checkbox" id="nw_upstair" name="nw_upstair" <?if($nw['nw_upstair'] == 'Y') {echo "checked";}?>/><label for="nw_upstair" style=""><span class="ui"></span><span class="nw_upstair_txt">사용 설정</span></label></p>
		</td>
		-->
		

		<th scope="row"><label for="nw_purchase"> 패키지 구매 <strong class="sound_only"> 필수</strong></label></th>
		<td>
            <p style="padding:0;"><input type="checkbox" id="nw_purchase" name="nw_purchase" <?if($nw['nw_purchase'] == 'Y') {echo "checked";}?>/><label for="nw_purchase" style=""><span class="ui"></span><span class="nw_purchase_txt">사용 설정</span></label></p>
		</td>
		
	</tr>
	

	<tr>
		<th scope="row"><label for="nw_enroll"> 회원가입 </label></th>
		<td>
            <p style="padding:0;"><input type="checkbox" id="nw_enroll" name="nw_enroll" <?if($nw['nw_enroll'] == 'Y') {echo "checked";}?>/><label for="nw_enroll" style=""><span class="ui"></span><span class="nw_enroll_txt">사용 설정</span></label></p>
		</td>
	</tr>

	<?if(SHOP_USE){?>
	<tr>
		<th scope="row"><label for="nw_shop"> 쇼핑몰 </label></th>
		<td>
            <p style="padding:0;"><input type="checkbox" id="nw_shop" name="nw_shop" <?if($nw['nw_shop'] == 'Y') {echo "checked";}?>/><label for="nw_shop" style=""><span class="ui"></span><span class="nw_shop_txt">사용 설정</span></label></p>
		</td>
	</tr>
	<?}?>

    </tbody>
    </table>
</div>

<div class="btn_confirm01 btn_confirm" style="margin-top:30px;">
    <input type="submit" value="확인" class="btn_submit" accesskey="s">

</div>
</form>

<script>

$(document).ready(function(){
	$('#nw_with').on('click',function(){
		if($('#nw_with').is(":checked")){
			$('.nw_with_txt').html('사용함');
		}else{
			$('.nw_with_txt').html('사용안함');
		}
	});

	$('#nw_upstair').on('click',function(){
		if($('#nw_upstair').is(":checked")){
			$('.nw_upstair_txt').html('사용함');
		}else{
			$('.nw_upstair_txt').html('사용안함');
		}
	});

	$('#nw_change').on('click',function(){
		if($('#nw_change').is(":checked")){
			$('.nw_change_txt').html('사용함');
		}else{
			$('.nw_change_txt').html('사용안함');
		}
	});
	
	$('#nw_purchase').on('click',function(){
		if($('#nw_purchase').is(":checked")){
			$('.nw_purchase_txt').html('사용함');
		}else{
			$('.nw_purchase_txt').html('사용안함');
		}
	});

	$('#nw_enroll').on('click',function(){
		if($('#nw_enroll').is(":checked")){
			$('.nw_enroll_txt').html('사용함');
		}else{
			$('.nw_enroll_txt').html('사용안함');
		}
	});

	$('#nw_shop').on('click',function(){
		if($('#nw_shop').is(":checked")){
			$('.nw_shop_txt').html('사용함');
		}else{
			$('.nw_shop_txt').html('사용안함');
		}
	});

});


function frmnewwin_check(f)
{
    errmsg = "";
    errfld = "";

	if ($('input[name=nw_with]').is(":checked")) {
		$('#nw_with').val('Y');
	} else {
		$('#nw_with').val = 'N';
	}

	f.nw_with = $('#nw_with').val();


	if ($('input[name=nw_upstair]').is(":checked")) {
    $('input[name=nw_upstair]').val('Y');
	} else {
		$('input[name=nw_upstair]').val('N');
	}

	f.nw_upstair = $('#nw_change').val();

	if ($('input[name=nw_change]').is(":checked")) {
    $('input[name=nw_change]').val('Y');
	} else {
		$('input[name=nw_change]').val('N');
	}

	f.nw_change = $('#nw_change').val();


	if ($('input[name=nw_purchase]').is(":checked")) {
    $('input[name=nw_purchase]').val('Y');
	} else {
		$('input[name=nw_purchase]').val('N');
	}

	f.nw_purchase = $('#nw_purchase').val();

	if ($('input[name=nw_enroll]').is(":checked")) {
    $('input[name=nw_enroll]').val('Y');
	} else {
		$('input[name=nw_enroll]').val('N');
	}

	f.nw_enroll = $('#nw_enroll').val();

	if ($('input[name=nw_shop]').is(":checked")) {
    $('input[name=nw_shop]').val('Y');
	} else {
		$('input[name=nw_shop]').val('N');
	}

	f.nw_shop = $('#nw_shop').val();


    if (errmsg != "") {
        alert(errmsg);
        errfld.focus();
        return false;
    }
    return true;
}
</script>

<?php
include_once (G5_ADMIN_PATH.'/admin.tail.php');
?>
