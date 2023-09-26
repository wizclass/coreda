<?php
$sub_menu = "200100";
include_once("./_common.php");
include_once(G5_THEME_PATH.'/_include/wallet.php');
include_once(G5_LIB_PATH."/register.lib.php");
include_once(G5_PLUGIN_PATH . '/Encrypt/rule.php');

if ($w == 'u')
	check_demo();

auth_check($auth[$sub_menu], 'w');

//check_admin_token();
// print_R($_POST);
$mb_id = trim($_POST['mb_id']);
if($_POST['mb_name'] == ''){
	$mb_name = $mb_id;
}else{
	$mb_name = $_POST['mb_name'];
}

$today = date("Y-m-d H:i:s",time());
$todate = date("Y-m-d",time());
$od_id = date("YmdHis",time());

// 휴대폰번호 체크
$mb_hp = hyphen_hp_number($_POST['mb_hp']);
/* if($mb_hp) {
	$result = exist_mb_hp($mb_hp, $mb_id);
	if ($result)
		alert($result);
} */

// 인증정보처리
if($_POST['mb_certify_case'] && $_POST['mb_certify']) {
	$mb_certify = $_POST['mb_certify_case'];
	$mb_adult = $_POST['mb_adult'];
} else {
	$mb_certify = '';
	$mb_adult = 0;
}

$mb_zip1 = substr($_POST['mb_zip'], 0, 3);
$mb_zip2 = substr($_POST['mb_zip'], 3);






$_POST['center_use'] != "" ? $center_use = $_POST['center_use'] : $center_use = 0;


/*레벨 처리*/
$mb = get_member($mb_id);
$mb_level = $mb['mb_level'];
$temp_mb_level = $_POST['mb_level'];

if($center_use > 0){
	
	if($_POST['mb_level'] < 2){
		$temp_mb_level = 2;
	}
}

if($mb_level < 10 &&  $temp_mb_level != $mb_level){
	$mb_level = $temp_mb_level;
}

if($_POST['kyc_admin'] > ""){
	$kyc_admin = $_POST['kyc_admin'];

	if($_POST['kyc_admin'] > 0){
		$kyc_admin_time = $today." 관리자승인";
	}else{
		$kyc_admin_time = "";
	}
}else{ 
	$kyc_admin = '';
};

$_POST['mb_center'] != "" ? $mb_center = $_POST['mb_center'] : $mb_center = '';
$_POST['mb_balance'] != "" ? $mb_balance = conv_number($_POST['mb_balance']) : $mb_balance = 0;
// $_POST['mb_deposit_point'] != "" ? $mb_deposit_point = conv_number($_POST['mb_deposit_point']) : $mb_deposit_point = 0;
$_POST['mb_block'] != "" ? $mb_block = $_POST['mb_block'] : $mb_block = 0;
$_POST['bank_name'] != "" ? $bank_name = $_POST['bank_name'] : $bank_name = '';
$_POST['bank_account'] != "" ? $bank_account = $_POST['bank_account'] : $bank_account = '';
$_POST['account_name'] != "" ? $account_name = $_POST['account_name'] : $account_name = '';
$temp_mp_9 = $_POST['temp_mb_9'];
$_POST['mb_week_dividend'] != "" ? $mb_week_dividend = $_POST['mb_week_dividend'] : $mb_week_dividen = '0';

$mb_wallet = $_POST['mb_wallet'] != "" ? Encrypt($_POST['mb_wallet'],$mb['mb_id'],"x") : '';
$eth_my_wallet = $_POST['eth_my_wallet'] != "" ? Encrypt($_POST['eth_my_wallet'],$mb['mb_id'],"x") : '';
$etc_my_wallet = $_POST['etc_my_wallet'] != "" ? Encrypt($_POST['etc_my_wallet'],$mb['mb_id'],"x") : '';
$usdt_my_wallet = $_POST['usdt_my_wallet'] != "" ? Encrypt($_POST['usdt_my_wallet'],$mb['mb_id'],"x") : '';


$use_limit_paid = 0;

if(isset($_POST['b_autopack'])){
	$use_limit_paid = 1;	
	$limited = $_POST['q_autopack'];
}

$mb_index = "mb_index = (select ifnull(sum(od_cart_price),0)*({$limited}/100) from g5_order where mb_id = '{$mb_id}')";
$mb_no_sql = "select count(mb_no) as cnt, mb_no from g5_member where mb_id = '{$_POST['mb_recommend']}'";

$mb_no_row = sql_fetch($mb_no_sql);

if($mb_no_row['cnt'] <= 0){
	alert($mb_no_sql);

	// alert('존재하지 않는 추천인 정보 입니다.');
}

if($_POST['reg_tr_password']){
	$sql_tr_password = ", reg_tr_password = '".get_encrypt_string($_POST['reg_tr_password'])."'";
}else{
	$sql_tr_password = "";
}

$sql_common = "  mb_name = '{$_POST['mb_name']}',
				 mb_nick = '{$_POST['mb_name']}',
				 mb_email = '{$_POST['mb_email']}',
				 mb_homepage = '{$_POST['mb_homepage']}',
				 mb_tel = '{$_POST['mb_tel']}',
				 mb_hp = '{$mb_hp}',
				 mb_certify = '{$mb_certify}',
				 mb_adult = '{$mb_adult}',
				 mb_zip1 = '$mb_zip1',
				 mb_zip2 = '$mb_zip2',
				 mb_addr1 = '{$_POST['mb_addr1']}',
				 mb_addr2 = '{$_POST['mb_addr2']}',
				 mb_addr3 = '{$_POST['mb_addr3']}',
				 mb_addr_jibeon = '{$_POST['mb_addr_jibeon']}',
				 mb_signature = '{$_POST['mb_signature']}',
				 mb_leave_date = '{$_POST['mb_leave_date']}',
				 mb_divide_date = '{$_POST['mb_divide_date']}',
				 mb_intercept_date='{$_POST['mb_intercept_date']}',
				 mb_memo = '{$_POST['mb_memo']}',
				 mb_mailling = '{$_POST['mb_mailling']}',
				 mb_sms = '{$_POST['mb_sms']}',
				 mb_open = '{$_POST['mb_open']}',
				 mb_profile = '{$_POST['mb_profile']}',
				 grade = '{$_POST['grade']}',
				 mb_level = '{$mb_level}',
				 mb_recommend = '{$_POST['mb_recommend']}',
				 mb_recommend_no = '{$mb_no_row['mb_no']}',
			  	 first_name = '{$_POST['first_name']}',
  			 	 last_name = '{$_POST['last_name']}',
				 mb_1 = '{$_POST['mb_1']}',
				 mb_2 = '{$_POST['mb_2']}',
				 mb_3 = '{$_POST['mb_3']}',
				 mb_4 = '{$_POST['mb_4']}',
				 mb_5 = '{$_POST['mb_5']}',
				 mb_6 = '{$_POST['mb_6']}',
				 mb_7 = '{$_POST['mb_7']}',
				 mb_8 = '{$_POST['mb_8']}',
				 mb_9 = '{$temp_mp_9}',
				 mb_balance = '{$mb_balance}',
				 bank_name = '{$bank_name}',
				 bank_account = '{$bank_account}',
				 account_name = '{$account_name}',
				 center_use = '{$center_use}',
				 mb_center = '{$mb_center}',
				 mb_block = '{$mb_block}',
				 kyc_cert = {$kyc_admin},
				 kyc_regdt = '{$kyc_admin_time}',
				 mb_week_dividend = '{$mb_week_dividend}',
				 mb_wallet = '{$mb_wallet}',
				 eth_my_wallet = '{$eth_my_wallet}',
				 etc_my_wallet = '{$etc_my_wallet}',
				 usdt_my_wallet = '{$usdt_my_wallet}',
				 {$mb_index},
				 b_autopack = {$use_limit_paid},
				 q_autopack = {$limited}";

if ($w == '')
{
	// $mb = get_member($mb_id);
	if ($mb['mb_id'])
		alert('이미 존재하는 회원아이디입니다.\\nＩＤ : '.$mb['mb_id'].'\\n이름 : '.$mb['mb_name'].'\\n닉네임 : '.$mb['mb_nick'].'\\n메일 : '.$mb['mb_email']);

	// 닉네임중복체크
	//$sql = " select mb_id, mb_name, mb_nick, mb_email from {$g5['member_table']} where mb_nick = '{$_POST['mb_nick']}' ";
	//$row = sql_fetch($sql);
	//if ($row['mb_id'])
	//    alert('이미 존재하는 닉네임입니다.\\nＩＤ : '.$row['mb_id'].'\\n이름 : '.$row['mb_name'].'\\n닉네임 : '.$row['mb_nick'].'\\n메일 : '.$row['mb_email']);

	// 이메일중복체크
	/*
	$sql = " select mb_id, mb_name, mb_nick, mb_email from {$g5['member_table']} where mb_email = '{$_POST['mb_email']}' ";
	$row = sql_fetch($sql);
	if ($row['mb_id'])
		alert('이미 존재하는 이메일입니다.\\nＩＤ : '.$row['mb_id'].'\\n이름 : '.$row['mb_name'].'\\n닉네임 : '.$row['mb_nick'].'\\n메일 : '.$row['mb_email']);
	*/
	$insert_member = " insert into {$g5['member_table']} set mb_id = '{$mb_id}', mb_password = '".get_encrypt_string($mb_password)."', mb_datetime = '".G5_TIME_YMDHIS."', mb_ip = '{$_SERVER['REMOTE_ADDR']}', mb_email_certify = '".G5_TIME_YMDHIS."', {$sql_common} {$sql_tr_password} ";
	sql_query($insert_member);
	alert('가입처리되었습니다.');

}
else if ($w == 'u')
{
	// $mb = get_member($mb_id);
	if (!$mb['mb_id'])
		alert('존재하지 않는 회원자료입니다.');

	if ($is_admin != 'super' && $mb['mb_level'] >= $member['mb_level'])
		alert('자신보다 권한이 높거나 같은 회원은 수정할 수 없습니다.');

	if ($_POST['mb_id'] == $member['mb_id'] && $_POST['mb_level'] != $mb['mb_level'])
		alert($mb['mb_id'].' : 로그인 중인 관리자 레벨은 수정 할 수 없습니다.');

	// 닉네임중복체크
	//$sql = " select mb_id, mb_name, mb_nick, mb_email from {$g5['member_table']} where mb_nick = '{$_POST['mb_nick']}' and mb_id <> '$mb_id' ";
	//$row = sql_fetch($sql);
	//if ($row['mb_id'])
	//    alert('이미 존재하는 닉네임입니다.\\nＩＤ : '.$row['mb_id'].'\\n이름 : '.$row['mb_name'].'\\n닉네임 : '.$row['mb_nick'].'\\n메일 : '.$row['mb_email']);

	// 이메일중복체크
	/*
	$sql = " select mb_id, mb_name, mb_nick, mb_email from {$g5['member_table']} where mb_email = '{$_POST['mb_email']}' and mb_id <> '$mb_id' ";
	$row = sql_fetch($sql);
	if ($row['mb_id'])
		alert('이미 존재하는 이메일입니다.\\nＩＤ : '.$row['mb_id'].'\\n이름 : '.$row['mb_name'].'\\n닉네임 : '.$row['mb_nick'].'\\n메일 : '.$row['mb_email']);
	*/
	$mb_dir = substr($mb_id,0,2);

	// 회원 아이콘 삭제
	if ($del_mb_icon)
		@unlink(G5_DATA_PATH.'/member/'.$mb_dir.'/'.$mb_id.'.gif');

	// 아이콘 업로드
	if (is_uploaded_file($_FILES['mb_icon']['tmp_name'])) {
		if (!preg_match("/(\.gif)$/i", $_FILES['mb_icon']['name'])) {
			alert($_FILES['mb_icon']['name'] . '은(는) gif 파일이 아닙니다.');
		}

		if (preg_match("/(\.gif)$/i", $_FILES['mb_icon']['name'])) {
			@mkdir(G5_DATA_PATH.'/member/'.$mb_dir, G5_DIR_PERMISSION);
			@chmod(G5_DATA_PATH.'/member/'.$mb_dir, G5_DIR_PERMISSION);

			$dest_path = G5_DATA_PATH.'/member/'.$mb_dir.'/'.$mb_id.'.gif';

			move_uploaded_file($_FILES['mb_icon']['tmp_name'], $dest_path);
			chmod($dest_path, G5_FILE_PERMISSION);

			if (file_exists($dest_path)) {
				$size = getimagesize($dest_path);
				// 아이콘의 폭 또는 높이가 설정값 보다 크다면 이미 업로드 된 아이콘 삭제
				if ($size[0] > $config['cf_member_icon_width'] || $size[1] > $config['cf_member_icon_height']) {
					@unlink($dest_path);
				}
			}
		}
	}


	// 수동입금처리
	$mb_no = $_POST['mb_no'];

	$deposit_adm = conv_number($_POST['mb_deposit_point_add']);
	$deposit_adm_content = $_POST['mb_deposit_point_content'];
	$deposit_code = $_POST['mb_deposit_point_math'];
	$origin_deposit_point = $mb['mb_deposit_point'];
	
	

	// 수동 입금
	if($deposit_adm != 0){

		// 최초입금구분
		if($origin_deposit_point == 0){
			$process_code = 0;
		}else{
			$process_code = 1;
		}

		if($deposit_code == '+'){
			if($deposit_adm_content === '') {
				$deposit_adm_code = '관리자 지급';
				$admin_states = "0";
			} else {
				$deposit_adm_code = $deposit_adm_content;
				$admin_states = "1";
			}
		}else{
			if($deposit_adm_content === '') {
				$deposit_adm_code = '관리자 차감';
				$admin_states = "0";
			} else {
				$deposit_adm_code = $deposit_adm_content;
				$admin_states = "1";
			}
		}

		$coin = get_coins_price();
		// $deposit_adm_value = ($deposit_code.$deposit_adm) / $coin['usdt_eth'];
		$in_deposit_adm_value = $deposit_code.$deposit_adm;

		$deposit_adm_sql = "insert wallet_deposit_request set
				mb_id             = '{$mb_id}'
				, txhash     =  '{$deposit_adm_code}'
				, create_dt         = '{$today}'
				, create_d    		= '{$today}'
				, status   			= {$process_code}
				, update_dt         = '{$todate}'
				, coin          	= '{$curencys[0]}'
				, fee    			= 0
				, cost         		= 0
				, amt    			= 0
				, in_amt			= {$in_deposit_adm_value}
				, admin_states 		= '{$admin_states}'";

		$deposit_adm_result = sql_query($deposit_adm_sql);
		
		if($process_code == 1 && $deposit_adm_result){
			$update_sql = "UPDATE g5_member set mb_deposit_point = mb_deposit_point  {$in_deposit_adm_value} WHERE mb_id = '{$mb_id}' ";
			echo $update_sql;
			sql_query($update_sql);
		}
	}



	/* if($upstair_2 > 0){
		$upstair_sql_2 = "insert g5_shop_order set
				od_id				= '{$od_id}'
				, mb_no             = '{$mb_no}'
				, mb_id             = '{$mb_id}'
				, od_cart_price     =  {$upstair_2}
				, od_name           = 'MBM'
				, od_cash    		= '1'
				, od_receipt_time   = '{$today}'
				, od_time           = '{$today}'
				, od_date           = '{$todate}'
				, od_settle_case    = 'admin'
				, od_status         = '매출'
				, upstair    		= {$upstair}
				, pv				= {$upstair}
				, od_memo 			= 'admin'  ";
		
		sql_query($upstair_sql_2);
	} */

	
	if ($mb_password)
		$sql_password = " , mb_password = '".get_encrypt_string($mb_password)."' ";
	else
		$sql_password = "";

	if ($passive_certify)
		$sql_certify = " , mb_email_certify = '".G5_TIME_YMDHIS."' ";
	else
		$sql_certify = "";

	$sql = " update {$g5['member_table']}
				set {$sql_common}
					 {$sql_password}
					 {$sql_tr_password}
					 {$sql_certify}
				where mb_id = '{$mb_id}' ";
	sql_query($sql);
}
else
	alert('제대로 된 값이 넘어오지 않았습니다.');
	goto_url('./member_form.php?'.$qstr.'&amp;w=u&amp;mb_id='.$mb_id, false);
?>
