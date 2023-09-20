<?
	$today = date("Y-m-d");

	function shop_item($stx){

		$sql_common ="FROM g5_shop_item";
		$sql_search = " WHERE ca_id = '$stx' ";
		$sql_search .= " AND it_use = 1 ";
		
		$sql = " select *
				{$sql_common}
				{$sql_search}
				order by it_order asc";

		$row = sql_query($sql);
		return $row;
	}

	function get_shop_item($id_num){

		$sql_common ="FROM g5_shop_item";
		$sql_search = " WHERE it_id = '$id_num' ";
		$sql_search .= " AND it_use = 1 ";
		
		$sql = " select *
				{$sql_common}
				{$sql_search}
				order by it_order asc";

		$row = sql_fetch($sql);
		return $row;
	}

	function autoYn($val){
		$result = "X";

		if($val){
			$result = "●";
		}
		return $result;
	}

	function calc_price($val, $rate,$coin){
		
		$shift = "shift_".$coin;

		return $shift( conv_number($val) / conv_number($rate)) ;
	}

	function shop_history($mb_id){
		$receent_sql = "select ct_time as recent from g5_shop_cart where mb_id = '{$mb_id}' ORDER BY ct_time desc limit 1";
		$recent = sql_fetch($receent_sql);
		$recenttime = $recent['recent'];

		$sql = "select * from g5_shop_cart where mb_id = '{$mb_id}' AND ct_time ='{$recenttime}'";
		$result = sql_query($sql);
		return $result;
	}

	
	function packImg($val){
		$packimg_sql = "select it_id from g5_shop_item where it_name = '{$val}'";
		$packimg_result = sql_fetch($packimg_sql);
	
		return $packimg_result['it_id'];   
	}
	

	function shift_date($val){
		return date("Y-m-d", strtotime($val));
	}

	function item_valid($mb_id, $it_type){
		global $today;
		$item_valid_sql = "select * FROM g5_member A left Join g5_shop_cart B on  A.mb_id = B.mb_id where A.mb_id = '{$mb_id}'  and B.ct_select_time > '{$today}' and B.it_sc_type = '{$it_type}' order by ct_time desc limit 0,1";
		$result = sql_query($item_valid_sql);
		$result_f = sql_fetch($item_valid_sql);

		if($result->num_rows > 0){
			return $result_f;
		}
		
	}

?>