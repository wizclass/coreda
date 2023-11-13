<?php
error_reporting(E_ALL ^ E_NOTICE);

define('ASSETS_NUMBER_POINT', 4); // 입금 단위
define('BONUS_NUMBER_POINT', 0); // 수당계산,정산기준단위
define('COIN_NUMBER_POINT', 4); // 코인 단위
define('KRW_NUMBER_POINT', 0);

ob_start();
$debug = FALSE;
function bonus_pick($val)
{
	global $conn;
	$pick_sql = "select * from wallet_bonus_config where code = '{$val}' ";
	$pick_result = mysqli_query($conn, $pick_sql);
	$list = mysqli_fetch_array($pick_result);
	return $list;
}

function clean_coin_format($val, $decimal = COIN_NUMBER_POINT)
{
	$_num = (int)str_pad("1", $decimal + 1, "0", STR_PAD_RIGHT);
	return floor($val * $_num) / $_num;
}

function clean_number_format($val, $decimal = COIN_NUMBER_POINT)
{
	$_decimal = $decimal <= 0 ? 1 : $decimal;
	$_num = number_format(clean_coin_format($val, $decimal), $_decimal);
	$_num = rtrim($_num, 0);
	$_num = rtrim($_num, '.');

	return $_num;
}

function _get_coins_price()
{
	$result = array();
	$url_list = array(
		'https://api.upbit.com/v1/ticker?markets=KRW-ETH&markets=KRW-ETC&markets=USDT-ETH&markets=USDT-ETC',
		"https://pro-api.coinmarketcap.com/v1/tools/price-conversion?CMC_PRO_API_KEY=9a0e9663-df7f-431b-9561-d46935376d5b&amount=1&symbol=usdt",
		"https://api.mexc.com/api/v3/ticker/24hr?symbol=COREUSDT"
		// "https://api.bitforex.com/api/v1/market/ticker?symbol=coin-usdt-hja"
	);

	$data = _multi_curl($url_list);

	$eth_krw = $data[0][0]['trade_price'];
	$etc_krw = $data[0][1]['trade_price'];
	$usdt_eth = $data[0][2]['trade_price'];
	$usdt_etc = $data[0][3]['trade_price'];

	$result['usdt_krw'] = $eth_krw / $usdt_eth;

	$result['usdt_eth'] = $usdt_eth;
	$result['usdt_etc'] = $usdt_etc;
	$result['eth_krw'] = $eth_krw;
	$result['etc_krw'] = $etc_krw;
	$result['eth_usdt'] = $data[1]['data']['quote']['USD']['price'];
	$result['core_usdt'] = $data[2]['highPrice'];

	return $result;
}

function _multi_curl($url)
{
	$ch = array();
	$response = array();
	$curl_init = curl_multi_init();
	foreach ($url as $key => $value) {
		$ch[$key] = curl_init($value);
		curl_setopt($ch[$key], CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch[$key], CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch[$key], CURLOPT_SSL_VERIFYHOST, false);
		curl_multi_add_handle($curl_init, $ch[$key]);
	}

	do {
		curl_multi_exec($curl_init, $running);
		curl_multi_select($curl_init);
	} while ($running > 0);

	foreach (array_keys($ch) as $key) {
		$response[$key] = json_decode(curl_multi_getcontent($ch[$key]), true);
		curl_multi_remove_handle($curl_init, $ch[$key]);
	}

	curl_multi_close($curl_init);
	return $response;
}

function shift_coin($val)
{
	return Number_format($val, COIN_NUMBER_POINT);
}

$code = "staking";
$bonus_day = date('Y-m-d');

$host_name = '127.0.0.1';
$user_name = 'root';
$user_pwd = 'willsoft0780!@';
// $user_pwd = 'wizclass235689!@';
$database = 'coreda';
$conn = mysqli_connect($host_name, $user_name, $user_pwd, $database);


$dupl_check_sql = "select count(mb_id) as cnt from soodang_pay where day='{$bonus_day}' and allowance_name = '{$code}' ";
$dupl_check_result = mysqli_query($conn, $dupl_check_sql);
$get_today = mysqli_fetch_array($dupl_check_result)['cnt'];
if ($get_today > 0) {
	echo "{$bonus_day} {$code} 수당은 이미 지급되었습니다.";
	die;
}



// 데일리수당
$bonus_row = bonus_pick($code);

$bonus_rate = explode(",", $bonus_row['layer']);
$staking_bonus_rate = $bonus_row['rate'];
$bonus_limit = $bonus_row['limit'];

$reset_daily_benefit_sql = "update g5_member set mb_my_sales = 0";
if ($debug) {
	echo "<code>";
	print_r($reset_daily_benefit_sql);
	echo "</code><br>";
} else {
	$reset_result = mysqli_query($conn, $reset_daily_benefit_sql);
	if (!$reset_result) {
		echo "<code>";
		print_r($reset_daily_benefit_sql);
		echo "</code><br>";
		die;
	}
}

$coin = _get_coins_price();
$coin['core_KRW'] = $coin['core_usdt'] *  $coin['usdt_krw'];
$coin_curency = shift_coin($coin['core_KRW'], 'krw');

$order_list_sql = "select o.*, m.mb_level, m.grade, m.mb_name, (m.mb_balance + m.mb_shop_point) as mb_balance, m.mb_balance_ignore, m.mb_deposit_point, m.mb_index
from g5_order o 
join g5_member m 
on o.mb_id = m.mb_id where m.mb_save_point > 0 and o.od_soodang_date <= CURDATE()";

$order_list_result = mysqli_query($conn, $order_list_sql);

if ($debug) {
	echo "<code>";
	print_r($order_list_sql);
	echo "</code><br>";
}

// 설정로그 
echo "<strong> 1 CORE / 원화 시세 :: <span class='red'>" . shift_coin($coin['core_KRW'], 'krw') . "</span> 원</strong><br>";
echo "<strong>" . strtoupper($code) . " 지급비율 : " . $staking_bonus_rate . "%   </strong> | 지급한계 : " . $bonus_row['limited'] . "% <br>";
echo "<strong>" . $bonus_day . "</strong><br><br>";
echo "<div class='btn' onclick='bonus_url();'>돌아가기</div>";

?>

<html>

<body>
	<header>정산시작</header>
	<div>

		<?php

		// if(!$get_today){

		// 	$member_start_sql = "update g5_member set ";
		// 	$member_balance_column_sql = "";
		// 	$member_my_sales_cloumn_sql = "";
		// 	$member_where_sql = " where mb_id in (";

		// 	$log_start_sql = "insert into soodang_pay(`allowance_name`,`day`,`mb_id`,`mb_no`,`benefit`,`mb_level`,`grade`,`mb_name`,`rec`,`rec_adm`,`origin_balance`,`origin_deposit`,`datetime`) values";
		// 	$log_values_sql = "";

		// 	$total_paid_list = array();

		// 	for($i = 0; $i < $order_list_row = mysqli_fetch_array($order_list_result); $i++){
		// 		$goods_price = $order_list_row['upstair'];
		// 		$mb_balance = $order_list_row['mb_balance'];
		// 		$mb_balance_ignore = $order_list_row['mb_balance_ignore'];
		// 		$mb_index = $order_list_row['mb_index'];
		// 		$benefit = clean_coin_format($goods_price *((($order_list_row['pv'] * 0.01)/30) * $daily_bonus_rate),2);

		// 		$total_benefit = $mb_balance + $benefit + $total_paid_list[$order_list_row['mb_id']]['total_benefit'];

		// 		$clean_number_goods_price = clean_number_format($goods_price);
		// 		$clean_number_mb_balance = clean_number_format($mb_balance - $mb_balance_ignore);
		// 		$clean_number_mb_index = clean_number_format($mb_index);

		// 		$total_paid_list[$order_list_row['mb_id']]['total_benefit'] += $benefit;
		// 		$total_paid_list[$order_list_row['mb_id']]['real_benefit'] += $benefit;

		// 		$over_benefit_log  = "";

		// 		if( $total_benefit > $mb_index ){
		// 			$remaining_benefit = $total_benefit - $mb_index;
		// 			$cut_benefit = $mb_index - $mb_balance <= 0 ? 0 : clean_coin_format($mb_index-$mb_balance);

		// 			$origin_benefit = $benefit;
		// 			if($benefit - $remaining_benefit > 0) {
		// 				$benefit -= $remaining_benefit;		
		// 			}else{
		// 				$benefit = 0;
		// 			}
		// 			$over_benefit = $origin_benefit - $benefit;
		// 			$clean_over_benefit = clean_number_format($over_benefit);
		// 			$clean_origin_benefit = clean_number_format($origin_benefit);

		// 			$total_paid_list[$order_list_row['mb_id']]['real_benefit'] = $cut_benefit;
		// 			$over_benefit_log = " (over benefit : {$clean_over_benefit} / {$clean_origin_benefit})";
		// 		}

		// 		$clean_number_benefit = clean_number_format($benefit);
		// 		$rec = "Daily bonus {$order_list_row['pv']}% : {$clean_number_benefit} usdt payment{$over_benefit_log}";
		// 		$benefit_log = "{$clean_number_goods_price}(상품가격) * ( ( {$order_list_row['pv']}% [상품지급률]) / 30 ) * {$daily_bonus_rate} ){$over_benefit_log}";

		// 		$total_paid_list[$order_list_row['mb_id']]['log'] .= "<br><span>{$benefit_log} = </span><span class='blue'>{$clean_number_benefit}</span>";
		// 		$total_paid_list[$order_list_row['mb_id']]['sub_log'] = "<span>현재총수당 : {$clean_number_mb_balance}, 수당한계점 : {$clean_number_mb_index} </span>";

		// 		$log_values_sql .= "('{$code}','{$bonus_day}','{$order_list_row['mb_id']}',{$order_list_row['mb_no']},{$benefit},{$order_list_row['mb_level']},{$order_list_row['grade']},
		// 							'{$order_list_row['mb_name']}','{$rec}','{$benefit_log}={$clean_number_benefit}',{$mb_balance},{$order_list_row['mb_deposit_point']},now()),";

		// 	}

		// 	foreach($total_paid_list as $key=>$value){
		// 		if($member_balance_column_sql == "") $member_balance_column_sql = "mb_balance = case mb_id ";
		// 		if($member_my_sales_cloumn_sql == "") $member_my_sales_cloumn_sql = ",mb_my_sales = case mb_id ";

		// 		$member_balance_column_sql .= "when '{$key}' then mb_balance + {$value['real_benefit']} ";
		// 		$member_my_sales_cloumn_sql .= "when '{$key}' then {$value['real_benefit']} ";

		// 		$member_where_sql .= "'{$key}',";
		// 		echo "<span class='title'>{$key}</span>{$value['sub_log']}<br>{$value['log']}<div style='color:orange;'>발생 수당 : {$value['total_benefit']}</div><div style='color:red;'>▶ 수당지급 : {$value['real_benefit']}</div><br><br>";
		// 	}

		// 	$member_balance_column_sql .= "else mb_balance end ";
		// 	$member_my_sales_cloumn_sql .= "else mb_my_sales end ";

		// 	$member_sql = "";
		// 	$log_sql ="";

		// 	if($member_where_sql != "" && $log_values_sql != ""){
		// 		$member_where_sql = substr($member_where_sql,0,-1).")";
		// 		$log_values_sql = substr($log_values_sql,0,-1);

		// 		$member_sql = $member_start_sql.$member_balance_column_sql.$member_my_sales_cloumn_sql.$member_where_sql;
		// 		$log_sql = $log_start_sql.$log_values_sql;
		// 	}

		// 	if($member_sql != "" && $log_sql != ""){	
		// 		// 디버그 로그
		// 		if($debug){
		// 			echo "<code>";
		// 			print_R($member_sql);
		// 			echo "</code>";
		// 			echo "<br>";
		// 			echo "<code>";
		// 			print_R($log_sql);
		// 			echo "</code>";
		// 		}else{
		// 			$result = mysqli_query($conn, $log_sql);
		// 			if($result){
		// 				$result = mysqli_query($conn, $member_sql);
		// 				if(!$result){
		// 					echo "<code>ERROR:: MEMBER SQL -> {$member_sql}</code>";
		// 				}
		// 			}else{
		// 				echo "<code>ERROR:: LOG SQL -> {$log_sql}</code>";
		// 			}
		// 		}
		// 	}else{
		// 		echo "<span style='display: flex;justify-content: center; color:red;'>정산할 회원이 존재하지 않습니다.</span>";
		// 	}
		// }

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

			$live_bonus_rate = 1;

			$total_paid_list = array();

			for ($i = 0; $i < $order_list_row = mysqli_fetch_array($order_list_result); $i++) {
				$goods_price = $order_list_row['upstair'];
				$compound_interest = $order_list_row['compound_benefit'];
				$mb_balance = $order_list_row['mb_balance'];
				$mb_balance_ignore = $order_list_row['mb_balance_ignore'];
				$mb_index = $order_list_row['mb_index'];
				$benefit = $goods_price * (0.01 * $staking_bonus_rate);

				$total_benefit = ($mb_balance - $mb_balance_ignore) + $benefit + $total_paid_list[$order_list_row['mb_id']]['total_benefit'];

				$clean_number_goods_price = clean_number_format($goods_price, COIN_NUMBER_POINT);
				$clean_number_mb_balance = clean_number_format($mb_balance - $mb_balance_ignore, COIN_NUMBER_POINT);
				$clean_number_mb_index = clean_number_format($mb_index, COIN_NUMBER_POINT);

				$total_paid_list[$order_list_row['mb_id']]['total_benefit'] += $benefit;
				$total_paid_list[$order_list_row['mb_id']]['real_benefit'] += $benefit;

				$over_benefit_log  = "";

				// 수당한계 계산
				if ($total_benefit > $mb_index && $bonus_limit > 0) {
					$remaining_benefit = $total_benefit - $mb_index;
					$cut_benefit = ($mb_index - $mb_balance + $mb_balance_ignore) <= 0 ? 0 : clean_number_format($mb_index - $mb_balance + $mb_balance_ignore, 2);

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
				$_benefit = clean_number_format($benefit * $live_bonus_rate, COIN_NUMBER_POINT);
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

				$log_values_sql = rtrim($log_values_sql, ",");

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
					$result = mysqli_query($conn, $log_sql);
					if ($result) {
						$result = mysqli_query($conn, $member_sql);
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
		?>
	</div>

	<footer> 정산 완료</footer>

	<div class='btn' onclick="bonus_url('<?= $category ?>');">돌아가기</div>

	<body>

</html>

<style>
	body {
		font-size: 14px;
		line-height: 18px;
		letter-spacing: 0px;
	}

	code {
		color: green;
		display: block;
		margin-bottom: 5px;
		font-size: 11px;
	}

	.red {
		color: red;
		font-weight: 600;
	}

	.blue {
		color: blue;
		font-weight: 600;
	}

	.big {
		font-size: 16px;
		font-weight: 600;
	}

	.title {
		font-weight: 800;
		color: black;
		font-size: 16px;
		display: block;
	}

	.box {
		background: ghostwhite;
		margin-top: 30px;
		border-bottom: 1px solid #eee;
		padding-left: 5px;
		width: 100%;
		display: block;
	}

	.block {
		font-size: 26px;
		background: turquoise;
		display: block;
		height: 30px;
		line-height: 30px;
	}

	.block.coral {
		background: lightcoral
	}

	.indent {
		text-indent: 20px;
		display: inline-block;
	}

	.btn {
		background: black;
		padding: 5px 20px;
		display: inline-block;
		color: white;
		font-weight: 600;
		cursor: pointer;
		margin-bottom: 20px;
	}

	footer,
	header {
		margin: 20px 0;
		background: black;
		color: white;
		text-align: center
	}

	.error {
		display: block;
		width: 100%;
		text-align: center;
		height: 150px;
		line-height: 150px
	}

	.hidden {
		display: none;
	}

	.desc {
		font-size: 11px;
		color: #777;
	}

	.subtitle {
		font-size: 20px;
	}

	.sys_log {
		margin-bottom: 30px;
	}
</style>


<script>
	function bonus_url($val) {
		if ($val == 'mining') {
			location.href = '/adm/bonus/bonus_mining.php?to_date=<?= $bonus_day ?>';
		} else {
			location.href = '/adm/bonus/bonus_list.php?to_date=<?= $bonus_day ?>';
		}

	}
</script>
<?php
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