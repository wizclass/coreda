<?php
include_once('./_common.php');
include_once('./bonus/bonus_inc.php');
//include_once(G5_LIB_PATH.'/mailer.lib.php');

// 자동후원인등록 시스템 
$auto_brecomend_system = false;

$now_datetime = date('Y-m-d H:i:s');
$now_date = date('Y-m-d');

// $debug = 1;

$uid = $_POST['uid'];
$status = $_POST['status'];
$refund = $_POST['refund'];
$coin = $_POST['coin'];
$in_amt = $_POST['amt'];
$func = $_POST['func'];

$count = 1;
$drain = 1;

// $bonus_row = bonus_pick('cycle');
// $bonus_rate = $bonus_row['rate'];
// echo "보너스 :: ".$bonus_row['rate'];


/* if ($debug) {
	$uid = 6;
	$status = 1;
	$refund = 'Y';
	$func = 'deposit';
	$coin = '원';
	$in_amt = '7000000';
} */


if ($func == 'withrawal') {
	if ($status == '4' && $refund == 'Y') {
		$get_row = "SELECT * from {$g5['withdrawal']} where uid = {$uid} ";

		$ret = sql_fetch($get_row);
		$mb_id = $ret['mb_id'];
		$in_amt_total = $ret['out_amt'];

		// 출금반환처리
		$update_member_return = "update g5_member set mb_shift_amt = mb_shift_amt - {$in_amt_total}  where mb_id='{$mb_id}' ";

		// else if($coin == strtolower($minings[1])){
		// 	$coin_target = "mb_mining_1";
		// 	$amt_target = "mb_mining_1_amt";
		// 	$update_member_return = "update g5_member set {$amt_target} = {$amt_target} - {$in_amt_total}  where mb_id='{$mb_id}' ";
		// } else if($coin == strtolower($minings[2])){
		// 	$coin_target = "mb_mining_2";
		// 	$amt_target = "mb_mining_2_amt";
		// 	$update_member_return = "UPDATE g5_member set {$amt_target} = {$amt_target} - {$in_amt_total}  where mb_id='{$mb_id}' ";
		// }

		if ($debug) {
			print_r($update_member_return);
			echo "<br>";
		} else {
			$return_result = sql_query($update_member_return);
		}
	} else {
		$return_result = 1;
	}

	$sql = "UPDATE {$g5['withdrawal']} set status = '{$status}' ";
	$sql .= ", update_dt = now() ";
	$sql .= " where uid = {$uid} ";

	if ($debug) {
		echo "<br><br>";
		print_r($sql);
		echo "<br>";
	} else {
		$msg = "출금정산";
		$result = sql_query($sql);
	}
} else if ($func == 'deposit') {

	if ($status == '1' && $coin != '') {
		$get_row = "SELECT * from {$g5['deposit']} where uid = {$uid} ";
		$ret = sql_fetch($get_row);
		$mb_id = $ret['mb_id'];

		$coin_target = "mb_deposit_point";

		if ($in_amt > 0 && $refund == 'Y') {
			$update_member_return = "UPDATE g5_member set mb_deposit_point = mb_deposit_point + {$in_amt}  where mb_id='{$mb_id}' ";

			if ($debug) {
				print_r($update_member_return);
				echo "<br>";
			} else {
				$return_result = sql_query($update_member_return);
			}
		}

		/* 후원인 자동등록 
			if($auto_brecomend_system === true){

			// 추천인 산하 후원인 자리 검색
			$recomm_sql = "SELECT mb_recommend,mb_brecommend FROM g5_member WHERE mb_id = '{$mb_id}' ";
			$recomm_result = sql_fetch($recomm_sql);

			$brecomm = $recomm_result['mb_brecommend'];

			if ($brecomm == '') {
				// $recomm = $recomm_result['mb_recommend'];
				// $recomm = $config['cf_admin'];
				$recomm = 'zbzzang';


				// 추천인의 회원 승급 
				$upgrade_target = sql_fetch("SELECT mb_id from rank WHERE mb_id = '{$recomm}'" );

						if(!$upgrade_target){
							$upgrade_sql = "UPDATE g5_member set mb_level = 1 WHERE mb_id = '{$recomm}' ";
							$rank_note = "recommend sales from ".$mb_id;
							$upgrade_log_sql = "INSERT INTO rank (mb_id,old_level, rank, rank_day, rank_note ) value ('{$recomm}',0,1,'{$now_date}','{$rank_note}') ";

							if ($debug) {
								echo "<br><br>추천인승급 :: ";
								print_r($upgrade_sql);
								echo "<br>";
								print_r($upgrade_log_sql);
							} else {
								$upgrade_result = sql_query($upgrade_sql);
								$upgrade_log_result = sql_query($upgrade_log_sql);
							}
						} 
					

				//직추천인 수 / 홀수 좌 / 짝수 우
				 $direct_recom_sql  =  "SELECT COUNT(mb_id) as cnt from g5_member WHERE mb_recommend  = '{$recomm}' ";
				$direct_recom_result = sql_fetch($direct_recom_sql);
				$direct_recom = $direct_recom_result['cnt'];

				if ($debug){
					echo "<br><br> direct Recommend:: ".$recomm."(".$direct_recom.")";
				}


				// 직후원인
				$direct_brecom_sql  =  "SELECT COUNT(mb_id) as cnt from g5_member WHERE mb_brecommend  = '{$recomm}' ";
				$direct_brecom_result = sql_fetch($direct_brecom_sql);
				$direct_brecom = $direct_brecom_result['cnt'];

				if ($debug) {
					echo "<br><br> direct Recommend:: " . $recomm . "(" . $direct_brecom . ")";
				}


				if ($direct_brecom == 1) {
					$under_brecomme_sql = "SELECT mb_id from g5_member WHERE mb_brecommend  = '{$recomm}'  and mb_brecommend_type = 'R' ";

					$under_brecomme_result = sql_fetch($under_brecomme_sql);

					$under_brecomme_code = 'R';
					$under_brecomme = $under_brecomme_result['mb_id'];
				} else if ($direct_brecom < 1) {
					$under_brecomme_sql = "SELECT mb_id from g5_member WHERE mb_brecommend  = '{$recomm}'  and mb_brecommend_type = 'L' ";
					$under_brecomme_result = sql_fetch($under_brecomme_sql);

					$under_brecomme_code = 'L';
					$under_brecomme = $under_brecomme_result['mb_id'];
				}

				if ($under_brecomme) {
					$recomm = $under_brecomme;
				}

				if ($debug) {
					echo "<br><h1>" . $recomm . "(" . $under_brecomme . ")</h1><br>";
				}

				$brecomme = array_brecommend($recomm, 1);
				$target_key = min(array_keys($brecomme));
				$now_brecom = $brecomme[$target_key];

				if ($debug) {
					echo "<br><br> 후원자찾기 :: ";
					print_R($now_brecom);
				}

				if ($now_brecom['cnt'] == 0) {
					$now_type = 'L';
					$mb_lr = '1';
				} else {
					$now_type = 'R';
					$mb_lr = '2';
				}

				// 후원인 기록 
				$recom_update_sql = "UPDATE g5_member set mb_brecommend='{$now_brecom['id']}', mb_brecommend_type='{$now_type}',mb_bre_time='{$now_datetime}',mb_lr = {$mb_lr} WHERE mb_id = '{$mb_id}' ";

				if ($debug) {
					echo "<br><br>후원인 기록 :: ";
					print_R($recom_update_sql);
					$recom_update_result = 1;
				} else {
					$recom_update_result = sql_query($recom_update_sql);
				}


				// 후원레그2 후원인 기록
				if ($recom_update_result) {
					$origin_number_sql = "SELECT mb_recommend from g5_member WHERE mb_id = '{$mb_id}' ";
					$origin_number_result = sql_fetch($origin_number_sql);
					$origin_recom = $origin_number_result['mb_recommend'];

					$brecomme2 = array_brecommend_binary($origin_recom, 1);
					$target_key2 = min(array_keys($brecomme2));
					$now_brecom2 = $brecomme2[$target_key2];

					if ($now_brecom2['cnt'] == 0) {
						$now_type2 = 'L';
					} else {
						$now_type2 = 'R';
					}

					if ($debug) {
						echo "<br><br> 슈퍼레그 후원자찾기 :: ";
						print_R($now_brecom2);
					}

					$random_recom_update_sql = "INSERT g5_member_binary set mb_id = '{$mb_id}',mb_recommend='{$origin_recom}', mb_brecommend='{$now_brecom2['id']}',mb_bre_time ='{$now_datetime}', mb_brecommend_type='{$now_type2}'";

					//중복아이디 없을때만
					$dup_check_sql = "SELECT count(*) as cnt from g5_member_binary WHERE mb_id = '{$mb_id}' ";
					$dup_check_result = sql_fetch($dup_check_sql);
					$dup_check = $dup_check_result['cnt'];

					if ($debug) {
						echo "<br><br>슈퍼레그 후원인 기록 :: ";
						print_R($random_recom_update_sql);
						$random_recom_update_result = 1;
					} else {
						if ($dup_check > 0) {
							$random_recom_update_result = 1;
						} else {
							$random_recom_update_result = sql_query($random_recom_update_sql);
						}
					}
				}

			// 아바타 생성
			if($now_type == 'R' && $recom_update_result){

				if ($debug){
					echo "<br><br>";
					echo $now_brecom['id'];
					echo "<br><br>";
				}

				// 아바타인지 회원마스터인지 판별
				if(strpos($now_brecom['id'],'_')){
					$master_id_raw = explode('_',$mb['mb_id']);
					$master_id = $master_id_raw[0];
				}else{
					$master_id = $now_brecom['id'];
				}
				

				$mem_sql = "SELECT * from g5_member where mb_id='{$master_id}' ";
				$mb = sql_fetch($mem_sql);

				if ($debug){
					echo "<br>대상찾기:".$mem_sql;
				}
				
				$mem_avatar_num = $mb['avatar_last'];
				$avatar_last_num = $mem_avatar_num+1;

				$avata_id = $mb['mb_id']."_".sprintf("%02d",$avatar_last_num);

				$avata_sql= "INSERT IGNORE INTO {$g5['member_table']}
					( mb_id,mb_password,mb_recommend,mb_name, mb_lr,mb_recommend_no,depth, mb_datetime,mb_open_date ) value
					( '{$avata_id}','{$mb['mb_password']}','{$mb['mb_recommend']}','{$mb['mb_name']}',1,{$mb['mb_recommend_no']},'{$mb['depth']}', '{$now_datetime}', '{$now_date}' )";

				
				if ($debug){
					echo "<br><br>아바타 생성 :: ";
					print_R($avata_sql);
					$avata_result =1;
				}else{
					$avata_result = sql_query($avata_sql);
				}

				// 아바타 생성기록
				if($avata_result){
					$avatar_log = "INSERT into g5_avatar_log (mb_id,avatar_id,create_dt,memo,count) value ('{$mb['mb_id']}', '{$avata_id}', '{$now_datetime}', '후원조건달성생성', $avatar_last_num )";
					
					if ($debug){
						echo "<br><br>아바타 로그 :: ";
						print_R($avatar_log);
					}else{
						sql_query($avatar_log);
					}
					
				}

				// 수당기록 
				// $bonus_sql = "insert into soodang_pay (allowance_name, day, mb_id, mb_no, benefit, level, grade, mb_name,rec_adm,datetime) value ('cycle','{$now_date}','{$mb['mb_id']}',) ";
				
				$now_deposit = $mb['mb_deposit_point'] + $mb['mb_deposit_calc'];
				$bonus_sql = "INSERT {$g5['bonus']} set allowance_name ='cycle'
								, day = '{$now_date}'
								, mb_id = '{$mb['mb_id']}'
								, mb_no = {$mb['mb_no']}
								, benefit = '{$bonus_rate}'
								, mb_level = {$mb['mb_level']}
								, grade = {$mb['grade']}
								, mb_name = '{$mb['mb_name']}'
								, rec = 'cycle bouns from {$mb_id}'
								, rec_adm = 'cycle bouns from {$mb_id}'
								, origin_balance = {$mb['mb_balance']}
								, origin_deposit = {$now_deposit}
								, datetime = '{$now_datetime}' ";

				if ($debug){
					echo "<br><br>수당기록 :: ";
					print_R($bonus_sql);
					$bonus_result =1;
				}else{
					$bonus_result = sql_query($bonus_sql);
				}


				// 대상자 업데이트
				if($bonus_result){
					$origin_mem_update = "UPDATE g5_member set avatar_last = {$avatar_last_num},mb_balance = (mb_balance + $bonus_rate )  WHERE mb_id ='{$mb['mb_id']}' ";

					if ($debug){
						echo "<br><br>대상자 업데이트 :: ";
						print_R($origin_mem_update);
						$origin_up_result =1;
					}else{
						$origin_up_result = sql_query($origin_mem_update);
					}
				}else{
					echo (json_encode(array("result" => "failed", "code" => "0005", "sql" => "대상자 업데이트 오류")));
				}


				// 아바타 입금요청 생성
				if($origin_up_result){
					$deposit_sql = "INSERT INTO wallet_deposit_request(mb_id, txhash, create_dt,create_d,status,coin) VALUES('{$avata_id}','AVATA','$now_datetime','$now_date',0,'eth')";
					
					if ($debug){
						echo "<br><br>아바타 입금요청 생성 :: ";
						print_R($deposit_sql);
					}else{
						$deposit_result = sql_query($deposit_sql);
					}
				}else{
					echo (json_encode(array("result" => "failed", "code" => "0005", "sql" => "아바타 자동입금기록 오류")));
					
				}

			} 
			// 아바타생성프로세스
			}	
		} */
	} // 승인인경우

	$sql = "UPDATE {$g5['deposit']} set status = '{$status}' ";
	$sql .= ", update_dt = '{$now_datetime}' ";
	$sql .= " where uid = {$uid} ";

	if ($debug) {
		echo "<br><br>";
		print_r($sql);
		echo "<br>";
	} else {
		$msg = "입금정산 ";
		$result = sql_query($sql);
	}
} else {
	echo (json_encode(array("result" => "failed", "code" => "9999", "sql" => "func can't find ERROR ")));
}



/* 완료 리턴 처리*/

if ($return_result) {
	$msg .= " 및 금액반영";
}
if ($result) {
	$msg .= " ,업데이트";
}

if ($auto_brecomend_system === true) {
	if ($recom_update_result) {
		$msg .= "\n[ {$mb_id} ]\n자동 후원인 등록 = {$now_brecom['id']} - $now_type \n";
	}

	if ($random_recom_update_result) {
		$msg .= "후원2 레그 등록 = {$now_brecom2['id']} - $now_type2 \n";
	}
}

$msg .= "처리가 완료되었습니다.";

echo (json_encode(array("result" => "success", "code" => "0001", "msg" => $msg), JSON_UNESCAPED_UNICODE));



$brcomm_arr = [];
// 후원인 빈자리 찾기
function array_brecommend($recom_id, $count)
{
	global $brcomm_arr, $debug;


	// $new_arr = array();
	$b_recom_sql = "SELECT mb_id from g5_member WHERE mb_brecommend='{$recom_id}' ORDER BY mb_brecommend_type ";
	$b_recom_result = sql_query($b_recom_sql);
	$cnt = sql_num_rows($b_recom_result);

	if ($cnt < 2) {
		if ($debug) {

			print_R($count . ' :: ' . $recom_id . ' :: ' . $cnt);
			echo "<br><br>";
		}
		if (!$brcomm_arr[$count]) {
			$brcomm_arr[$count]['id'] = $recom_id;
			$brcomm_arr[$count]['cnt'] = $cnt;
		}
	} else {
		++$count;
		while ($row = sql_fetch_array($b_recom_result)) {
			array_brecommend($row['mb_id'], $count);
		}
	}
	return $brcomm_arr;
}


$brcomm_arr2 = [];
// 후원인 빈자리 찾기
function array_brecommend_binary($recom_id, $count)
{
	global $brcomm_arr2, $debug;


	// $new_arr = array();
	$b_recom_sql = "SELECT mb_id from g5_member_binary WHERE mb_brecommend='{$recom_id}' ";
	$b_recom_result = sql_query($b_recom_sql);
	$cnt = sql_num_rows($b_recom_result);

	if ($cnt < 2) {
		if ($debug) {
			echo "<br><br><br><br>";
			print_R($count . ' :: ' . $recom_id . ' :: ' . $cnt);
		}
		if (!$brcomm_arr2[$count]) {
			$brcomm_arr2[$count]['id'] = $recom_id;
			$brcomm_arr2[$count]['cnt'] = $cnt;
		}
	} else {
		++$count;
		while ($row = sql_fetch_array($b_recom_result)) {
			array_brecommend_binary($row['mb_id'], $count);
		}
	}
	return $brcomm_arr2;
}
