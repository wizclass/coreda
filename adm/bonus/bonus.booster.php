<?php

$sub_menu = "600200";
include_once('./_common.php');
include_once('./bonus_inc.php');

$debug = FALSE;

auth_check($auth[$sub_menu], 'r');

$check_bonus_day_sql = "select count(day) as cnt from soodang_pay where day = '{$bonus_day}'";
$check_bonus_day = sql_fetch($check_bonus_day_sql)['cnt'];

if (!$check_bonus_day) {
    echo "<script>alert('{$bonus_day} Staking 보너스 기록이 없습니다.');
    history.back();</script>";
    return false;
}


// php 버전 대응 패치
if (!function_exists('array_column')) :

    function array_column(array $input, $column_key, $index_key = null)
    {

        $result = array();
        foreach ($input as $k => $v)
            $result[$index_key ? $v[$index_key] : $k] = $v[$column_key];

        return $result;
    }
endif;



// 스테이킹수당
$bonus_row = bonus_pick($code);

$bonus_rate = explode(",", $bonus_row['layer']);
$booster_bonus_rate = explode(",", $bonus_row['rate']);
$bonus_limit = $bonus_row['limit'];

$rate_text = "";
for ($i = 0; $i < count($bonus_rate); $i++) {
    if ($i % 2 == 0) {
        $rate_text .= "<br>|";
    }
    $rate_text .= " 지급대수 : {$bonus_rate[$i]} 대 ({$booster_bonus_rate[$i]}%) | ";
}

if ($debug) {
    echo "<code>";
    print_r($check_bonus_day_sql);
    echo "</code><br>";
}

// 설정로그 
echo "<strong>" . strtoupper($code) . " 지급비율 : " . $rate_text . "   </strong>     <br>지급조건 :" . $pre_condition . "  |    지급한계 : " . $bonus_limit_tx . " <br>";
echo "<strong>" . $bonus_day . "</strong><br><br>";
echo "<div class='btn' onclick='bonus_url();'>돌아가기</div>";
?>


<html>

<body>
    <header>정산시작</header>
    <div>

        <?php

        $member_for_paying_sql = "select mb_id as id, mb_name, mb_no, mb_level, grade, (mb_balance + mb_shop_point) as mb_balance, mb_index,mb_balance_ignore, mb_deposit_point, (select count(*) from g5_member where mb_recommend = id and mb_level > 0) as cnt from g5_member where mb_save_point >= 0 and mb_level > 0";

        if ($debug) {
            echo "<code>{$member_for_paying_sql}</code>";
        }

        $unit = "core";
        $shop_unit = "";

        $member_for_paying_result = sql_query($member_for_paying_sql);

        $mem_list = array();

        $start_member_update_sql = "update g5_member set ";
        $update_mb_balance_sql = "";
        $update_recom_sales = '';
        $update_mb_shop_sql = '';
        $update_where_sql = " where mb_id in(";

        $log_start_sql = "insert into soodang_pay(`allowance_name`,`day`,`mb_id`,`mb_no`,`benefit`,`mb_level`,`grade`,`mb_name`,`rec`,`rec_adm`,`origin_balance`,`origin_deposit`,`datetime`) values";
        $log_values_sql = "";

        for ($i = 0; $i < $row = sql_fetch_array($member_for_paying_result); $i++) {

            $mb_id = $row['id'];
            $recommended_cnt = $row['cnt'];

            if ($recommended_cnt >= 1) {
                $recommended_cnt = 1;
            }

            $recommended_cnt = $bonus_rate[$recommended_cnt - 1];

            $booster_member = return_down_manager($row['mb_no'], $recommended_cnt);

            $recom_member = return_down_manager($row['mb_no'], 20);

            $recom_sales = array_int_sum($recom_member, 'mb_save_point', 'int');




            if (!$recom_sales) {
                $recom_sales = 0;
            }

            if ($debug) {
                echo "<code>";
                echo "하부매출 : " . $recom_sales;
                echo "</code>";
            }


            $sort_arr = array();
            foreach ($booster_member as $key => $value) {
                $sort_arr[$key] = $value['depth'];
            }
            array_multisort($sort_arr, SORT_ASC, $booster_member);

            $add_benefit = 0;

            $mb_index = $row['mb_index'];
            $mb_balance = $row['mb_balance'];
            $mb_balance_ignore = $row['mb_balance_ignore'];

            $total_left_benefit = $mb_index - $mb_balance + $mb_balance_ignore <= 0 ? 0 : clean_number_format($mb_index - $mb_balance + $mb_balance_ignore);


            $clean_total_left_benefit = clean_number_format($total_left_benefit);
            $clean_number_mb_balance = clean_number_format($mb_balance - $mb_balance_ignore);

            $clean_number_mb_index = clean_number_format($mb_index);

            echo "<div><span class='title'>{$mb_id} ( 추천인수 : {$row['cnt']}명 [{$recommended_cnt}대] )</span> 받은총보너스 : {$clean_number_mb_balance}";

            if ($bonus_limit > 0) {
                echo ", 수당한계점 : {$clean_number_mb_index} => 총 수용가능 수당 : {$clean_total_left_benefit}</div><br>";
            }
            echo "<br>";

            foreach ($booster_member as $key => $value) {

                if ($value['mb_id'] == $mb_id) {
                    continue;
                }

                $depth = $value['depth'];

                $bonus_benefit_rate = get_bonus_rate($depth);

                $booster_benefit = $value['mb_my_sales'] * ($bonus_benefit_rate * 0.01);
                $add_benefit += $booster_benefit;
                $clean_mb_my_sales = clean_number_format($value['mb_my_sales']);
                echo "<br><span>{$value['mb_id']} ( {$depth} 대 ) => {$clean_mb_my_sales} (Staking) * ( {$bonus_benefit_rate} * 0.01 ) = </span><span class='blue'>{$booster_benefit}</span><br>";
            }

            $origin_benefit = $add_benefit;
            $over_benefit_log = "";

            // 수당한계
            if ($total_left_benefit < $add_benefit && $bonus_limit > 0) {

                $add_benefit = $total_left_benefit;
                $over_benefit = $origin_benefit - $total_left_benefit;
                $clean_over_benefit = clean_number_format($over_benefit);
                $over_benefit_log = " (over benefit : {$clean_over_benefit} / {$clean_number_mb_index})";
            }

            if ($add_benefit < 0) {
                $add_benefit = 0;
            }

            $live_benefit =  $add_benefit * $live_bonus_rate;
            $shop_benefit =  $add_benefit * $shop_bonus_rate;

            echo "<div style='color:orange;'>발생 수당 : {$origin_benefit}</div><div style='color:red;'> ▶ 수당 지급: {$live_benefit}</div>";

            if ($shop_bonus_rate > 0) {
                echo "<br><div style='color:red;'> ▶▶ 쇼핑몰포인트지급 : {$shop_benefit} </div>";
            }
            echo "<br><br><br>";

            if ($update_mb_balance_sql == "") {
                $update_mb_balance_sql .= "mb_balance = case mb_id ";
            }
            if ($update_recom_sales == "") {
                $update_recom_sales .= ", recom_sales = case mb_id ";
            }
            if ($update_mb_shop_sql == "") {
                $update_mb_shop_sql .= ", mb_shop_point = case mb_id ";
            }

            $update_mb_balance_sql .= "when '{$mb_id}' then mb_balance + {$live_benefit} ";
            $update_mb_shop_sql .= "when '{$mb_id}' then mb_shop_point + {$shop_benefit} ";
            $update_recom_sales .= "WHEN '{$mb_id}' then {$recom_sales} ";
            $update_where_sql .= "'{$mb_id}',";

            $clean_live_benefit = clean_number_format($live_benefit, 3);
            $clean_shop_benefit = clean_number_format($shop_benefit);

            $rec = "Matching bonus by step {$recommended_cnt} :: {$clean_live_benefit} {$unit}";
            $rec_adm = "{$value['mb_id']} - {$recommended_cnt}대 : {$clean_live_benefit} ";

            $log_values_sql .= "('{$code}','{$bonus_day}','{$mb_id}',{$row['mb_no']},{$live_benefit},{$row['mb_level']},{$row['grade']},
            '{$row['mb_name']}','{$rec}','{$rec_adm}',{$mb_balance},{$row['mb_deposit_point']},now()),";
        }

        $update_mb_balance_sql .= " else mb_balance end ";
        $update_mb_shop_sql .= " else mb_shop_point end ";
        $update_recom_sales .= " ELSE recom_sales END ";



        $update_where_sql = substr($update_where_sql, 0, -1) . ")";
        $log_values_sql = substr($log_values_sql, 0, -1);

        $update_sql = $start_member_update_sql . $update_mb_balance_sql . $update_mb_shop_sql .  $update_recom_sales . $update_where_sql;
        $log_sql = $log_start_sql . $log_values_sql;

        if ($debug) {
            echo "<code>{$update_sql}</code>";
            echo "<code>{$log_sql}</code>";
        } else {

            $result = sql_query($log_sql);
            if ($result) {
                $result = sql_query($update_sql);
                if (!$result) {
                    echo "<code>ERROR:: MEMBER SQL -> {$update_sql}</code>";
                }
            } else {
                echo "<code>ERROR:: LOG SQL -> {$log_sql}</code>";
            }
        }


        function get_bonus_rate($depth)
        {
            global $booster_bonus_rate;

            if ($depth <= 3) {
                $bonus_benefit_rate = $booster_bonus_rate[$depth - 1];
            } else if ($depth >= 4 || $depth <= 10) {
                $bonus_benefit_rate = $booster_bonus_rate[3];
            } else if ($depth >= 11) {
                $bonus_benefit_rate = $booster_bonus_rate[4];
            }

            return $bonus_benefit_rate;
        }

        /* 추천하부매니저 검색 */
        function return_down_manager($mb_no, $cnt = 0)
        {
            global $config, $g5, $mem_list;

            $mb_result = sql_fetch("SELECT count(*) as cnt, mb_id,mb_name,mb_level,grade,mb_rate,mb_save_point,rank,recom_sales,mb_my_sales from g5_member WHERE mb_no = '{$mb_no}'");

            $list = [];
            $list['mb_id'] = $mb_result['mb_id'];
            $list['mb_name'] = $mb_result['mb_name'];
            $list['mb_level'] = $mb_result['mb_level'];
            $list['mb_save_point'] = $mb_result['mb_save_point'];
            $list['grade'] = $mb_result['grade'];
            $list['depth'] = 0;
            $list['mb_rate'] = $mb_result['mb_rate'];
            $list['recom_sales'] = $mb_result['recom_sales'];
            $list['rank'] = $mb_result['rank'];
            $list['mb_my_sales'] = $mb_result['mb_my_sales'];

            // $mb_add = sql_fetch("SELECT COUNT(mb_id) as cnt,IFNULL( (SELECT noo  from  recom_bonus_noo WHERE mb_id = '{$mb_result['mb_id']}' ) ,0) AS noo FROM g5_member WHERE mb_recommend = '{$mb_result['mb_id']}' ");

            // $list['cnt'] = $mb_add['cnt'];
            // $list['noo'] = $mb_add['noo'];

            $mem_list = [$list];
            $result = recommend_downtree($mb_result['mb_id'], 0, $cnt);

            return $result;
        }


        function recommend_downtree($mb_id, $count = 0, $cnt = 0)
        {
            global $mem_list;

            if ($cnt == 0 || ($cnt != 0 && $count < $cnt)) {

                $limit_level = "and mb_level > 0";

                // if($count == 1){
                //     $limit_level = "";
                // }

                $recommend_tree_result = sql_query("SELECT mb_id,mb_name,mb_level,grade,mb_rate,mb_save_point,rank,recom_sales,mb_my_sales from g5_member WHERE mb_recommend = '{$mb_id}' {$limit_level}");
                $recommend_tree_cnt = sql_num_rows($recommend_tree_result);
                if ($recommend_tree_cnt > 0) {
                    ++$count;

                    while ($row = sql_fetch_array($recommend_tree_result)) {

                        if ($row['mb_level'] > 0) {
                            $list['mb_id'] = $row['mb_id'];
                            $list['mb_name'] = $row['mb_name'];
                            $list['mb_level'] = $row['mb_level'];
                            $list['grade'] = $row['grade'];
                            $list['mb_rate'] = $row['mb_rate'];
                            $list['recom_sales'] = $row['recom_sales'];
                            $list['mb_save_point'] = $row['mb_save_point'];
                            $list['rank'] = $row['rank'];
                            $list['mb_my_sales'] = $row['mb_my_sales'];
                            $list['depth'] = $count;
                            array_push($mem_list, $list);
                        }
                        recommend_downtree($row['mb_id'], $count, $cnt);
                    }
                }
            }
            return $mem_list;
        }


        /* 결과 합계 */
        function array_int_sum($list, $key)
        {
            return array_sum(array_column($list, $key));
        }
        ?>

        <?php
        include_once('./bonus_footer.php');
        //로그 기록
        if ($debug) {
        } else {
            $html = ob_get_contents();
            //ob_end_flush();
            $logfile = G5_PATH . '/data/log/' . $code . '/' . $code . '_' . $bonus_day . '.html';
            fopen($logfile, "w");
            file_put_contents($logfile, ob_get_contents());
        }
        ?>