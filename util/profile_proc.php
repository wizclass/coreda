<?php
include_once('./_common.php');
//print_R($_POST);
ob_clean();

$now = date("Y-m-d h:i:s", time());

function shift_hp($val)
{
	return preg_replace("/[^0-9]/", "", $val);
}

if (empty($_POST)) {
	//alert('Not Availabled. Please retry');
	echo (json_encode(array("result" => "error",  "code" => "0001", "sql" => '다시 시도해주세요. 문제가 계속되면 관리자에게 연락주세요.')));
	return false;
}

$category = $_POST['category'];

if ($category == 'email') {

	if (check_password($_POST['auth_pwd'], $member['mb_password'])) {
		//$email_up_sql = "UPDATE g5_member set mb_password = password('".$_POST['email3']."') where mb_id = $member['mb_id']";
		$email_up_sql = "UPDATE g5_member set mb_email = '{$_POST['email3']}', mb_1  = '1' , mb_email_certify = '{$now}' where mb_id = '{$member['mb_id']}' ";
		$email_up_result = sql_query($email_up_sql);

		if ($email_up_result) {
			echo (json_encode(array("result" => "success",  "code" => "0000", "sql" => '이메일이 변경되었습니다.')));
		}
	} else {
		echo (json_encode(array("result" => "error",  "code" => "0002", "sql" => '사용중인 비밀번호가 올바르지 않습니다.')));
		return false;
	}
}

if ($category == 'phone') {

	// $mb_hp = shift_hp($member['mb_hp']);
	// $post_hp = shift_hp($_POST['hp_num']);

	$new_hp = shift_hp($_POST['new_hp_num']);

	if (check_password($_POST['auth_pwd'], $member['mb_password'])) {
		$hp_up_sql = "UPDATE g5_member set mb_hp = '{$new_hp}', mb_certify = '1' where mb_id = '{$member['mb_id']}' ";
		$hp_up_result = sql_query($hp_up_sql);
		echo (json_encode(array("result" => "success",  "code" => "0000", "sql" => '휴대폰번호가 변경되었습니다.')));
	} else {
		echo (json_encode(array("result" => "error",  "code" => "0002", "sql" => '사용중인 비밀번호가 올바르지 않습니다.')));
		return false;
	}
}


if ($category == 'auto_purchase') {

	$purchase_have_sql = "select * from g5_shop_cart where where mb_id = '{$member['mb_id']}' ";
	$purchase_have = sql_fetch($purchase_have_sql);

	if ($purchase_have) {
		$purchase_sql = "UPDATE g5_shop_cart set ct_option = '{$_POST['q_chk']}' where mb_id = '{$member['mb_id']}' ";
		$purchase_result = sql_query($purchase_sql);
		echo (json_encode(array("result" => "success",  "code" => "0000", "sql" => '변경이 완료되었습니다.')));
	} else {
		echo (json_encode(array("result" => "error",  "code" => "0004", "sql" => '구매 내역이 없습니다.')));
	}
}


if ($category == 'pw') {

	if (strlen($_POST['new_pw']) < 4 || strlen($_POST['new_pw_re']) > 8) {
		echo (json_encode(array("result" => "error",  "code" => "0003", "sql" => '4~8자 이내 영문,숫자,특수문자 조합을 해주세요.')));
		return false;
	}

	$pattern = preg_match("/^((?=.*[a-zA-Z])|(?=.*[!@#$%^&*=+]))(?=.*[0-9]).{4,8}/", $_POST['new_pw_re']);
	if ($pattern == 0) {
		echo (json_encode(array("result" => "error",  "code" => "0003", "sql" => '4~8자 이내 숫자+영문 혹은 특수문자 조합을 해주세요.')));
		return false;
	}

	if (check_password($_POST['current_pw'], $member['mb_password'])) {

		$get_encrypt_string = get_encrypt_string($_POST['new_pw_re']);

		$pass_sql = " UPDATE g5_member set mb_password = '{$get_encrypt_string}' where mb_id = '{$member['mb_id']}' ";
		$pass_result = sql_query($pass_sql);

		if ($pass_result) {
			echo (json_encode(array("result" => "success",  "code" => "0000", "sql" => '비밀번호 변경이 완료되었습니다.')));
		} else {
			echo (json_encode(array("result" => "error",  "code" => "0003", "sql" => '죄송합니다. 문제가 발생하였습니다. 나중에 다시 시도해주세요.')));
			return false;
		}
	} else {
		//alert('The current email address is incorrect <br> Check current email.');
		echo (json_encode(array("result" => "error",  "code" => "0002", "sql" => '사용중인 비밀번호가 올바르지 않습니다.')));
		return false;
	}
}


if ($category == 'tpw') {

	if (check_password($_POST['auth_pwd'], $member['mb_password'])) {

		if (check_password($_POST['current_tpw'], $member['reg_tr_password'])) {

			$get_encrypt_string = get_encrypt_string($_POST['new_tpw_re']);

			$pass_sql = " UPDATE g5_member set reg_tr_password = '{$get_encrypt_string}' where mb_id = '{$member['mb_id']}' ";
			$pass_result = sql_query($pass_sql);

			if ($pass_result) {
				echo (json_encode(array("result" => "success",  "code" => "0000", "sql" => '변경이 완료되었습니다.')));
			} else {
				echo (json_encode(array("result" => "error",  "code" => "0003", "sql" => '죄송합니다. 문제가 발생하였습니다. 나중에 다시 시도해주세요.')));
				return false;
			}
		} else {
			//alert('The current email address is incorrect <br> Check current email.');
			echo (json_encode(array("result" => "error",  "code" => "0002", "sql" => '현재 출금 비밀번호와 일치하지 않습니다.')));
			return false;
		}
	} else {
		echo (json_encode(array("result" => "error",  "code" => "0002", "sql" => '사용중인 비밀번호가 올바르지 않습니다.')));
		return false;
	}
}

if ($category == 'name') {

	if (check_password($_POST['auth_pwd'], $member['mb_password']) && check_password($_POST['auth_pin_pwd'], $member['reg_tr_password'])) {

		$name_sql = "UPDATE g5_member set mb_name = '{$_POST['new_first_name']}', first_name = '{$_POST['new_first_name']}' WHERE mb_id = '{$member['mb_id']}'";
		$name_result = sql_query($name_sql);

		if ($name_result) {
			echo (json_encode(array("result" => "success",  "code" => "0000", "sql" => '지갑 주소 변경이 완료되었습니다.')));
		} else {
			echo (json_encode(array("result" => "error",  "code" => "0003", "sql" => '죄송합니다. 문제가 발생하였습니다. 나중에 다시 시도해주세요.')));
			return false;
		}
	} else {
		echo (json_encode(array("result" => "error",  "code" => "0002", "sql" => '현재 로그인 비밀번호 또는 PIN 코드가 일치하지 않습니다.')));
		return false;
	}
}

if ($category == 'addr') {

	if ($_POST['hja_addr'] != '') {
		$addr_sql = "UPDATE g5_member set hja_addr = '{$_POST['hja_addr']}' WHERE mb_id = '{$member['mb_id']}'";
		$addr_result = sql_query($addr_sql);

		if ($addr_result) {
			echo (json_encode(array("result" => "success",  "code" => "0000", "sql" => '지갑 주소 변경이 완료되었습니다.')));
		} else {
			echo (json_encode(array("result" => "error",  "code" => "0003", "sql" => '죄송합니다. 문제가 발생하였습니다. 나중에 다시 시도해주세요.')));
			return false;
		}
	} else {
		echo (json_encode(array("result" => "error",  "code" => "0002", "sql" => '주소를 정확히 입력해주세요')));
		return false;
	}
}
