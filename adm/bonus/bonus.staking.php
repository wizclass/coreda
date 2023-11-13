<?php

$sub_menu = "600200";
include_once('./_common.php');
include_once('./bonus_inc.php');

auth_check($auth[$sub_menu], 'r');

// 스테이킹지급
$debug = FALSE;
$bonus_row = bonus_pick($code);

$bonus_rate = explode(",", $bonus_row['layer']);
$staking_bonus_rate = $bonus_row['rate'];
$bonus_limit = $bonus_row['limit'];

$reset_staking_benefit_sql = "update g5_member set mb_my_sales = 0";

if ($debug) {
	echo "<code>";
	print_r($reset_staking_benefit_sql);
	echo "</code><br>";
} else {
	$reset_result = sql_query($reset_staking_benefit_sql);
	if (!$reset_result) {
		echo "<script>alert('스테이킹 지급중 문제가 발생하였습니다.');
				history.back();</script>";
	}
}

$coin = _get_coins_price();
$coin['core_KRW'] = $coin['core_usdt'] *  $coin['usdt_krw'];
$coin_curency = shift_auto($coin['core_KRW'], 'krw');

$order_list_sql = "select o.*, m.mb_level, m.grade, m.mb_name, (m.mb_balance + m.mb_shop_point) as mb_balance, m.mb_balance_ignore, m.mb_deposit_point, m.mb_index
from g5_order o 
join g5_member m 
on o.mb_id = m.mb_id where m.mb_save_point > 0 and o.od_soodang_date <= CURDATE()";

// $order_list_sql = "select o.*, SUM(s.benefit) AS compound_benefit, m.mb_level, m.grade, m.mb_name, (m.mb_balance + m.mb_shop_point) as mb_balance, m.mb_balance_ignore, m.mb_deposit_point, m.mb_index
// from g5_order o 
// JOIN soodang_pay s ON s.od_id = o.od_id
// join g5_member m 
// on o.mb_id = m.mb_id where m.mb_save_point > 0 and o.od_soodang_date <= CURDATE() GROUP BY o.mb_no";

$order_list_result = sql_query($order_list_sql);

if ($debug) {
	echo "<code>";
	print_r($order_list_sql);
	echo "</code><br>";
}


// 코인 소수점 특정자리수 버림 계산값 
function calculate_math($val, $point)
{
	$cal1 = $val * (POW(10, $point + 1));
	$cal2 = round($cal1);
	$cal3 = $cal2 / (POW(10, $point + 1));
	return $cal3;
}

// 설정로그 
echo "<strong> 1 CORE / 원화 시세 :: <span class='red'>" . shift_auto($coin['core_KRW'], 'krw') . "</span> 원</strong><br>";
echo "<strong>" . strtoupper($code) . " 지급비율 : " . $staking_bonus_rate . "%   </strong> | 지급한계 : " . $bonus_row['limited'] . "% <br>";
echo "<strong>" . $bonus_day . "</strong><br><br>";
echo "<div class='btn' onclick='bonus_url();'>돌아가기</div>";

?>

<html>

<body>
	<header>정산시작</header>
	<div>

		<?php

		if (!$get_today) {

			$unit = "core";
			$shop_unit = "usdp";

			$member_start_sql = "update g5_member set ";
			$member_balance_column_sql = "";
			$member_my_sales_cloumn_sql = "";
			$member_my_shop_cloumn_sql = "";

			$member_where_sql = " where mb_id in (";

			$log_start_sql = "insert into soodang_pay(`allowance_name`,`day`,`od_id`, `mb_id`,`mb_no`,`benefit`,`curency`,`rate`,`mb_level`,`grade`,`mb_name`,`rec`,`rec_adm`,`origin_balance`,`origin_deposit`,`datetime`) values";
			$log_values_sql = "";

			$total_paid_list = array();

			for ($i = 0; $i < $order_list_row = sql_fetch_array($order_list_result); $i++) {
				$goods_price = $order_list_row['upstair'];
				$compound_interest = $order_list_row['compound_benefit'];
				$mb_balance = $order_list_row['mb_balance'];
				$mb_balance_ignore = $order_list_row['mb_balance_ignore'];
				$mb_index = $order_list_row['mb_index'];
				$benefit = $goods_price * (0.01 * $staking_bonus_rate);

				$total_benefit = ($mb_balance - $mb_balance_ignore) + $benefit + $total_paid_list[$order_list_row['mb_id']]['total_benefit'];

				$clean_number_goods_price = clean_number_format($goods_price, COIN_NUMBER_POINT);
				$clean_number_compound_interest = clean_number_format($compound_interest, COIN_NUMBER_POINT);
				$clean_number_mb_balance = clean_number_format($mb_balance - $mb_balance_ignore, COIN_NUMBER_POINT);
				$clean_number_mb_index = clean_number_format($mb_index, COIN_NUMBER_POINT);

				$total_paid_list[$order_list_row['mb_id']]['total_benefit'] += $benefit;
				$total_paid_list[$order_list_row['mb_id']]['real_benefit'] += $benefit;

				$over_benefit_log  = "";

				// 수당한계 계산
				if ($total_benefit > $mb_index && $bonus_limit > 0) {
					$remaining_benefit = $total_benefit - $mb_index;
					$cut_benefit = ($mb_index - $mb_balance + $mb_balance_ignore) <= 0 ? 0 : clean_coin_format($mb_index - $mb_balance + $mb_balance_ignore, 2);

					$origin_benefit = $benefit;
					if ($benefit - $remaining_benefit > 0) {
						$benefit -= $remaining_benefit;
					} else {
						$benefit = 0;
					}

					$over_benefit = $origin_benefit - $benefit;
					$clean_over_benefit = clean_number_format($over_benefit);
					$clean_origin_benefit = clean_number_format($origin_benefit);

					$total_paid_list[$order_list_row['mb_id']]['real_benefit'] = $cut_benefit;
					$over_benefit_log = " (over benefit : {$clean_over_benefit} / {$clean_origin_benefit})";
				}

				$clean_shop_benefit = clean_number_format($benefit * $shop_bonus_rate);

				$clean_number_benefit  = clean_number_format($benefit, COIN_NUMBER_POINT);
				$_benefit = clean_coin_format($benefit * $live_bonus_rate, COIN_NUMBER_POINT);
				$_clean_number_benefit  = clean_number_format($_benefit, COIN_NUMBER_POINT);

				$rec = "staking bonus {$staking_bonus_rate}% : {$_clean_number_benefit} {$unit} ";
				// "Shop Bonus : {$clean_shop_benefit} {$shop_unit} {$over_benefit_log}";
				$benefit_log = "상품 가격 : {$clean_number_goods_price} + 해당 상품 이자 합계 : {$clean_number_compound_interest} (PV) * ( {$staking_bonus_rate}% ){$over_benefit_log}";

				$total_paid_list[$order_list_row['mb_id']]['log'] .= "<br><span>{$benefit_log} = </span><span class='blue'>{$clean_number_benefit}</span>";
				$total_paid_list[$order_list_row['mb_id']]['sub_log'] = "<span>현재총수당 : {$clean_number_mb_balance}";
				//  수당한계점 : {$clean_number_mb_index} </span>";

				$log_values_sql .= "('{$code}','{$bonus_day}', '{$order_list_row['od_id']}', '{$order_list_row['mb_id']}',{$order_list_row['mb_no']},{$_clean_number_benefit},{$coin_curency},{$staking_bonus_rate},{$order_list_row['mb_level']},{$order_list_row['grade']},
							'{$order_list_row['mb_name']}','{$rec}','{$benefit_log}={$_clean_number_benefit} {$unit}',{$mb_balance},{$order_list_row['mb_deposit_point']},now()),";
			}

			foreach ($total_paid_list as $key => $value) {
				if ($member_balance_column_sql == "") $member_balance_column_sql = "mb_balance = case mb_id ";
				// if($member_my_shop_cloumn_sql == "") $member_my_shop_cloumn_sql = ",mb_shop_point = case mb_id ";
				if ($member_my_sales_cloumn_sql == "") $member_my_sales_cloumn_sql = ",mb_my_sales = case mb_id ";

				$live_benefit = clean_number_format(($value['real_benefit'] * $live_bonus_rate), 4);

				if ($shop_bonus_rate > 0) {
					$shop_benefit = $value['real_benefit'] * $shop_bonus_rate;
				}


				$member_balance_column_sql .= "when '{$key}' then mb_balance + {$live_benefit} ";
				// $member_my_shop_cloumn_sql .= "when '{$key}' then mb_shop_point + {$shop_benefit} ";
				$member_my_sales_cloumn_sql .= "when '{$key}' then {$live_benefit} ";

				$member_where_sql .= "'{$key}',";
				echo "<span class='title'>{$key}</span>{$value['sub_log']}<br>{$value['log']}<div style='color:orange;'>발생 수당 : {$value['total_benefit']}</div><div style='color:red;'>▶ 수당지급 : {$live_benefit} ";

				if ($shop_bonus_rate > 0) {
					echo "<br> ▶ 쇼핑몰포인트지급 : {$shop_benefit} ";
				}

				echo "</div><br><br>";
			}

			$member_balance_column_sql .= "else mb_balance end ";
			// $member_my_shop_cloumn_sql .= "else mb_shop_point end ";
			$member_my_sales_cloumn_sql .= "else mb_my_sales end ";


			$member_sql = "";
			$log_sql = "";

			if ($member_where_sql != "" && $log_values_sql != "") {
				$member_where_sql = substr($member_where_sql, 0, -1) . ")";
				$log_values_sql = substr($log_values_sql, 0, -1);

				$member_sql = $member_start_sql . $member_balance_column_sql . $member_my_shop_cloumn_sql . $member_my_sales_cloumn_sql . $member_where_sql;
				$log_sql = $log_start_sql . $log_values_sql;
			}

			if ($member_sql != "" && $log_sql != "") {
				// 디버그 로그
				if ($debug) {
					echo "<code>";
					print_R($member_sql);
					echo "</code>";
					echo "<br>";
					echo "<code>";
					print_R($log_sql);
					echo "</code>";
				} else {
					$result = sql_query($log_sql);
					if ($result) {
						$result = sql_query($member_sql);
						if (!$result) {
							echo "<code>ERROR:: MEMBER SQL -> {$member_sql}</code>";
						}
					} else {
						echo "<code>ERROR:: LOG SQL -> {$log_sql}</code>";
					}
				}
			} else {
				echo "<span style='display: flex;justify-content: center; color:red;'>정산할 회원이 존재하지 않습니다.</span>";
			}
		}
		include_once('./bonus_footer.php');

		//로그 기록
		if ($debug) {
		} else {
				$html = ob_get_contents();
				//ob_end_flush();
				$dir = "/var/www/html/coreda/data/log/{$code}/";

				if(!is_dir($dir)){
						mkdir($dir, '777');
				}
				$logfile = "/var/www/html/coreda/data/log/{$code}/{$code}_{$bonus_day}.html";
				fopen($logfile, "w");
				file_put_contents($logfile, ob_get_contents());
		}
		?>