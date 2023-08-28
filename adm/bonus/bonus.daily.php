<?php

$sub_menu = "600200";
include_once('./_common.php');
include_once('./bonus_inc.php');

auth_check($auth[$sub_menu], 'r');

// 데일리수당
$debug = false;
$bonus_row = bonus_pick($code);

$bonus_rate = explode(",",$bonus_row['layer']);
$daily_bonus_rate = $bonus_row['rate'];

$reset_daily_benefit_sql = "update g5_member set mb_my_sales = 0";
if($debug){
	echo "<code>";
	print_r($reset_daily_benefit_sql);
	echo "</code><br>";
}else{
	$reset_result = sql_query($reset_daily_benefit_sql);
	if(!$reset_result){
		echo "<script>alert('DAILY 지급중 문제가 발생하였습니다.');
				history.back();</script>";
	}
}

$order_list_sql = "select s.*, m.mb_level, m.grade, m.mb_name, (m.mb_balance + m.mb_shop_point) as mb_balance, m.mb_balance_ignore, m.mb_deposit_point, m.mb_index
from g5_order s 
join g5_member m 
on s.mb_id = m.mb_id where m.mb_save_point > 0 and s.od_soodang_date <= curdate()";

$order_list_result = sql_query($order_list_sql);

if($debug){
	echo "<code>";
	print_r($order_list_sql);
	echo "</code><br>";
}

// 설정로그 
echo "<strong>".strtoupper($code)." 지급비율 : ". $daily_bonus_rate."%   </strong> | 지급한계 : ".$bonus_row['limited']."% <br>";
echo "<strong>".$bonus_day."</strong><br><br>";
echo "<div class='btn' onclick='bonus_url();'>돌아가기</div>";

?>

<html>
	<body>
		<header>정산시작</header>    
		<div>
	
<?php

if(!$get_today){

	$unit = "usdt";

	$member_start_sql = "update g5_member set ";
	$member_balance_column_sql = "";
	$member_my_sales_cloumn_sql = "";
	$member_my_shop_cloumn_sql ="";

	$member_where_sql = " where mb_id in (";
	
	$log_start_sql = "insert into soodang_pay(`allowance_name`,`day`,`mb_id`,`mb_no`,`benefit`,`mb_level`,`grade`,`mb_name`,`rec`,`rec_adm`,`origin_balance`,`origin_deposit`,`datetime`) values";
	$log_values_sql = "";
	
	$total_paid_list = array();
	
	for($i = 0; $i < $order_list_row = sql_fetch_array($order_list_result); $i++){
		$goods_price = $order_list_row['upstair'];
		$mb_balance = $order_list_row['mb_balance'];
		$mb_balance_ignore = $order_list_row['mb_balance_ignore'];
		$mb_index = $order_list_row['mb_index'];
		$benefit = $goods_price *(0.01 * $daily_bonus_rate);
		
		$total_benefit = $mb_balance + $benefit + $total_paid_list[$order_list_row['mb_id']]['total_benefit'];

		$clean_number_goods_price = clean_number_format($goods_price);
		$clean_number_mb_balance = clean_number_format($mb_balance - $mb_balance_ignore);
		$clean_number_mb_index = clean_number_format($mb_index);
		
		$total_paid_list[$order_list_row['mb_id']]['total_benefit'] += $benefit;
		$total_paid_list[$order_list_row['mb_id']]['real_benefit'] += $benefit;

		$over_benefit_log  = "";

		if( $total_benefit > $mb_index ){
			$remaining_benefit = $total_benefit - $mb_index;
			$cut_benefit = $mb_index - $mb_balance <= 0 ? 0 : clean_coin_format($mb_index-$mb_balance,2);
			
			$origin_benefit = $benefit;
			if($benefit - $remaining_benefit > 0) {
				$benefit -= $remaining_benefit;		
			}else{
				$benefit = 0;
			}

			$over_benefit = $origin_benefit - $benefit;
			$clean_over_benefit = clean_number_format($over_benefit);
			$clean_origin_benefit = clean_number_format($origin_benefit);

			$total_paid_list[$order_list_row['mb_id']]['real_benefit'] = $cut_benefit;
			$over_benefit_log = " (over benefit : {$clean_over_benefit} / {$clean_origin_benefit})";
		}
		
		$clean_shop_benefit = clean_number_format($benefit * $shop_bonus_rate);

		$clean_number_benefit  = clean_number_format($benefit);
		$_benefit = clean_coin_format($benefit * $live_bonus_rate,2);
		$_clean_number_benefit  = clean_number_format($_benefit);

		$rec = "Daily bonus {$order_list_row['pv']}% : {$_clean_number_benefit} {$unit}, Shop Bonus : {$clean_shop_benefit} {$unit} {$over_benefit_log}";
		$benefit_log = "{$clean_number_goods_price}(상품가격) * ( {$order_list_row['pv']}% [상품지급률] ) ){$over_benefit_log}";
		
		$total_paid_list[$order_list_row['mb_id']]['log'] .= "<br><span>{$benefit_log} = </span><span class='blue'>{$clean_number_benefit}</span>";
		$total_paid_list[$order_list_row['mb_id']]['sub_log'] = "<span>현재총수당 : {$clean_number_mb_balance}, 수당한계점 : {$clean_number_mb_index} </span>";
	
		$log_values_sql .= "('{$code}','{$bonus_day}','{$order_list_row['mb_id']}',{$order_list_row['mb_no']},{$_benefit},{$order_list_row['mb_level']},{$order_list_row['grade']},
							'{$order_list_row['mb_name']}','{$rec}','{$benefit_log}={$_clean_number_benefit} {$unit}, {$clean_shop_benefit} {$unit}(expected : {$clean_number_benefit} {$unit})',{$mb_balance},{$order_list_row['mb_deposit_point']},now()),";
	
	}
	
	foreach($total_paid_list as $key=>$value){
		if($member_balance_column_sql == "") $member_balance_column_sql = "mb_balance = case mb_id ";
		if($member_my_shop_cloumn_sql == "") $member_my_shop_cloumn_sql = ",mb_shop_point = case mb_id ";
		if($member_my_sales_cloumn_sql == "") $member_my_sales_cloumn_sql = ",mb_my_sales = case mb_id ";

		$live_benefit = $value['real_benefit'] * $live_bonus_rate;
		$shop_benefit = $value['real_benefit'] * $shop_bonus_rate;
		

		$member_balance_column_sql .= "when '{$key}' then mb_balance + {$live_benefit} ";
		$member_my_shop_cloumn_sql .= "when '{$key}' then mb_shop_point + {$shop_benefit} ";
		$member_my_sales_cloumn_sql .= "when '{$key}' then {$value['real_benefit']} ";
		
		$member_where_sql .= "'{$key}',";
		echo "<span class='title'>{$key}</span>{$value['sub_log']}<br>{$value['log']}<div style='color:orange;'>발생 수당 : {$value['total_benefit']}</div><div style='color:red;'>▶ 수당지급 : {$live_benefit} <br> ▶ 쇼핑몰포인트지급 : {$shop_benefit} </div><br><br>";
	}
	
	$member_balance_column_sql .= "else mb_balance end ";
	$member_my_shop_cloumn_sql .= "else mb_shop_point end ";
	$member_my_sales_cloumn_sql .= "else mb_my_sales end ";
	

	$member_sql = "";
	$log_sql ="";

	if($member_where_sql != "" && $log_values_sql != ""){
		$member_where_sql = substr($member_where_sql,0,-1).")";
		$log_values_sql = substr($log_values_sql,0,-1);

		$member_sql = $member_start_sql.$member_balance_column_sql.$member_my_shop_cloumn_sql.$member_my_sales_cloumn_sql.$member_where_sql;
		$log_sql = $log_start_sql.$log_values_sql;
	}
	
	if($member_sql != "" && $log_sql != ""){	
		// 디버그 로그
		if($debug){
			echo "<code>";
			print_R($member_sql);
			echo "</code>";
			echo "<br>";
			echo "<code>";
			print_R($log_sql);
			echo "</code>";
		}else{
			$result = sql_query($log_sql);
			if($result){
				$result = sql_query($member_sql);
				if(!$result){
					echo "<code>ERROR:: MEMBER SQL -> {$member_sql}</code>";
				}
			}else{
				echo "<code>ERROR:: LOG SQL -> {$log_sql}</code>";
			}
		}
	}else{
		echo "<span style='display: flex;justify-content: center; color:red;'>정산할 회원이 존재하지 않습니다.</span>";
	}
}
	include_once('./bonus_footer.php');
	
	//로그 기록
	if($debug){}else{
		$html = ob_get_contents();
		//ob_end_flush();
		$logfile = G5_PATH.'/data/log/'.$code.'/'.$code.'_'.$bonus_day.'.html';
		fopen($logfile, "w");
		file_put_contents($logfile, ob_get_contents());
	}
 ?>