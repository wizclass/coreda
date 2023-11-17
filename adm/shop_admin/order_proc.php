<?php
include_once('./_common.php');

$od_id = $_POST['od_id'];
// $od_id = '2021122416374801';

$od_item_sql = "SELECT * from g5_order WHERE od_id = {$od_id}";
$od_item = sql_fetch($od_item_sql);

$now_datetime = G5_TIME_YMDHIS;

function  od_name_return_rank($val)
{
    if (strlen($val) < 5) {
        return substr($val, 1, 1);
    } else {
        return 0;
    }
}

if ($od_item) {

    //상품생성 테이블 삭제 
    $rank_num = od_name_return_rank($od_item['od_name']);
    $package_group = "package_p" . $rank_num;
    $package_have_sql = "SELECT * from {$package_group} WHERE od_id = '{$od_item['od_id']}' ";
    $package_have = sql_fetch($package_have_sql);

    print_R($package_have_sql);
    echo "<br><br>";

    if ($package_have) {
        $del_package = "DELETE FROM {$package_group} WHERE od_id = '{$od_item['od_id']}' ";

        print_R($del_package);
        echo "<br><br>";

        $pack_del_result = sql_query($del_package);
    }

    // 금액반환처리
    $amt = $od_item['od_cart_price'];
    $upstair = $od_item['od_cash'];
    $pv = $od_item['pv'];
    $update_member_sql = "UPDATE g5_member set mb_deposit_calc= mb_deposit_calc + {$amt}, mb_save_point = mb_save_point - {$amt}, mb_rate = mb_rate - {$pv} ";

    if ($rank_num == 0) {
        $update_member_sql .= ", sales_day = '0000-00-00' , rank_note = '' , rank = 0 ";
    } else {
        $update_member_sql .= ", rank_note = 'Membership-PACK' , rank = 1 ";
    }
    $update_member_sql .= " WHERE mb_id = '{$od_item['mb_id']}' ";

    print_R($update_member_sql);
    echo "<br><br>";
    // $update_result = 1;
    $update_result = sql_query($update_member_sql);

    if ($update_result) {
        $de_data = $od_item['od_name'] . " | " . $amt . " | " . $od_item['od_status'] . ' 건 구매취소처리';
        $od_del_log_sql = "INSERT g5_order_delete set de_key = {$od_item['od_id']}
        , de_data = '{$de_data}'
        , mb_id = '{$od_item['mb_id']}'
        , de_ip = '{$_SERVER['REMOTE_ADDR']}'
        , de_datetime = '{$now_datetime}' ";

        print_R($od_del_log_sql);
        echo "<br><br>";
        // $result = 1;
        $result = sql_query($od_del_log_sql);

        if ($result) {
            $del_odlist_sql = "DELETE from g5_order WHERE od_id = {$od_id} ";

            echo $del_odlist_sql;
            echo "<br><br>";

            $del_odlist_result = sql_query($del_odlist_sql);
        }
    }
}


if ($del_odlist_result) {
    ob_end_clean();
    echo json_encode(array("response" => "OK", "data" => 'complete'));
} else {
    ob_end_clean();
    echo json_encode(array("response" => "FAIL", "data" => "<p>ERROR<br>Please try later</p>"));
}
