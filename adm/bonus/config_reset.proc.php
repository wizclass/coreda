<?php
include_once('./_common.php');
// include_once('./bonus_inc.php');
include_once('../../util/purchase_proc.php');

$today = date("Y-m-d H:i:s",time());
$todate = date("Y-m-d",time());
$datemd = date("mdis",time());

if($_GET['debug']) $debug = 1;

if($_POST['nw_soodang_reset'] == 'on'){
    $trunc1 = sql_query(" TRUNCATE TABLE `soodang_pay` ");

    $trunc14 = sql_query(" TRUNCATE TABLE `brecom_bonus_noo` ");
    $trunc14 = sql_query(" TRUNCATE TABLE `brecom_bonus_today` ");
    $trunc14 = sql_query(" TRUNCATE TABLE `brecom_bonus_week` ");

    $trunc15 = sql_query(" TRUNCATE TABLE `recom_bonus_noo` ");
    $trunc15 = sql_query(" TRUNCATE TABLE `recom_bonus_today` ");
    $trunc15 = sql_query(" TRUNCATE TABLE `recom_bonus_week` ");

    $trunc16 = sql_query(" TRUNCATE TABLE `iwol` ");

    $member_update_sql = " UPDATE g5_member set  mb_balance = 0,mb_shop_point=0,mb_shop_calc=0 WHERE mb_level < 9 ";
    sql_query($member_update_sql);
    

    if($trunc16){
        $result = 1;
    }
}

if($_POST['nw_member_reset'] == 'on'){

    
    $trunc15 = sql_query(" TRUNCATE TABLE `rank` ");

    $sql_member_reset2 = " UPDATE g5_member set  mb_shop_point=0,mb_shop_calc=0,mb_deposit_point = 0,grade = 0, mb_level = 0, mb_deposit_calc=0, mb_balance = 0,mb_save_point=0,mb_shift_amt=0, mb_rate=0,mb_my_sales=0,mb_brecommend='',mb_brecommend_type='',mb_lr = 3,mb_4='',mb_5='',mb_6='',mb_7='',mb_8='',mb_9='' WHERE mb_level < 9 ";
    sql_query($sql_member_reset2);

    if($sql_member_reset2){
        $result = 1;
    }
}


if($_POST['nw_order_reset'] == 'on'){

    $trunc5 = sql_query(" TRUNCATE TABLE `g5_order` ");
    $trunc6 = sql_query(" TRUNCATE TABLE `package_log`; ");
    
    $pack_cnt = sql_fetch("SELECT count(it_id) as cnt from g5_item WHERE it_use > 0")['cnt'];
    $pack_name_sql = sql_fetch("SELECT it_maker from g5_item WHERE it_use > 0 limit 0,1 ")['it_maker'];
    $pack_name = substr($pack_name_sql,0,1);
    
    for($i=0;$i<=$pack_cnt;$i++){
        $pack_where = "package_".$pack_name.$i;
        sql_query(" TRUNCATE TABLE {$pack_where}; ");
        
    }
    $trunc15 = sql_query(" TRUNCATE TABLE `rank` ");

    $order_reset_sql = " UPDATE g5_member set  sales_day='0000-00-00', rank_note='', rank=0, recom_sales=0, mb_index=0  WHERE mb_level < 9 ";
    sql_query($order_reset_sql);

    if($order_reset_sql){
        $result = 1;
    }
}

if($_POST['nw_asset_reset'] == 'on'){

    $trunc2 = sql_query(" TRUNCATE TABLE `{$g5['withdrawal']}` ");
    $trunc3 = sql_query(" TRUNCATE TABLE `{$g5['deposit']}` ");

    if($trunc3){
        $result = 1;
    }
}

if($_POST['nw_mining_reset'] == 'on'){

    $trunc2 = sql_query(" TRUNCATE TABLE `{$g5['mining']}` ");
    $update_member = sql_query("update g5_member set {$mining_target} = 0, {$mining_amt_target} = 0 WHERE mb_no > 1 ");

    if($update_member){
        $result = 1;
    }
}

if($_POST['nw_binary_reset'] == 'on'){
    $copy_table = "g5_member_".$datemd;
    $table_copy_sql = "CREATE TABLE IF NOT EXISTS {$copy_table} SELECT * FROM `g5_member` ";
    
    $table_copy_result = sql_query($table_copy_sql);

    if($table_copy_result){
        $update_member = sql_query("update g5_member set mb_brecommend = '', mb_brecommend_type = '',mb_bre_time='',mb_lr = '0' WHERE mb_no > 1 AND mb_id !='zbzzang' ");
        $trunc14 = sql_query(" TRUNCATE TABLE `g5_member_bclass` ");
        $del_binary2 = sql_query("DELETE from g5_member_binary WHERE NO > 2 ");
        $auto_count = sql_query("ALTER TABLE g5_member_binary AUTO_INCREMENT = 3");
    }
    
    if($auto_count){
        $result = 1;
    }
}


if($_POST['nw_data_test'] == 'on'){
    
    $mb_deposit_point = 3000;
    $member_update_sql = " UPDATE g5_member set mb_deposit_point = {$mb_deposit_point}, mb_deposit_calc = 0 WHERE mb_no > 0 ";
    $update_member = sql_query($member_update_sql);
    
   if($update_member){

    $insert_order_sql = " INSERT INTO `g5_order` (od_id, mb_id, mb_no, od_cart_price, upstair, od_cash, od_name, od_tno, pv, od_time, od_date, od_settle_case, od_email, od_tel, od_hp, od_zip1, od_zip2, od_addr1, od_addr2, od_addr3, od_addr_jibeon, od_b_name, od_b_tel, od_b_hp, od_b_zip1, od_b_zip2, od_b_addr1, od_b_addr2, od_b_addr3, od_b_addr_jibeon, od_memo, od_cart_count, od_cart_coupon, od_send_cost, od_send_coupon, od_receipt_price, od_cancel_price, od_receipt_point, od_receipt_cash, od_refund_price, od_bank_account, od_receipt_time, od_coupon, od_misu, od_shop_memo, od_mod_history, od_status, od_hope_date, od_test, od_mobile, od_pg, od_app_no, od_escrow, od_casseqno, od_tax_flag, od_tax_mny, od_vat_mny, od_free_mny, od_delivery_company, od_invoice, od_invoice_time, od_cash_no, od_cash_info, od_pwd, od_ip,od_soodang_date) VALUE " ;

    for($i=5; $i <= 30 ; $i++){
        $orderid = date("YmdHis",time()).mt_rand(0000,9999);
        $member_id = 'test'.($i);
        $logic = purchase_package($member_id,2023040403,1);
        $insert_order_sql_arry .= " ({$orderid}, '{$member_id}', 0, 1000, 1000, 1000, 'P3', 2023040403, 6, '{$today}', '{$todate}', 'usdt', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', NULL, 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '{$today}', 0, 0, NULL, NULL, '패키지구매', '0000-00-00', 0, 0, '', '', 0, '', 0, 0, 0, 0, '0', '', '0000-00-00 00:00:00', NULL, NULL, '', '',curdate()),";
        sql_query("update g5_member set mb_index = 3000 where mb_id = '{$member_id}'");
    }

    $result_insert_sql = substr($insert_order_sql.$insert_order_sql_arry, 0, -1);

    if($debug){
        // print_R($result_insert_sql);
        $result = 1;
    }else{
        $result = sql_query($result_insert_sql);
    }
   }
}



if($_POST['nw_data_del'] == 'on'){
    
    $del_member = " DELETE from `g5_member` WHERE mb_no > 1; ";
    
    if($debug){
        print_R($del_member);
        $del_result = 1;
    }else{ 
        $del_result = sql_query($del_member);
    }


    if($del_result){
        $alter_table_query = " ALTER TABLE `g5_member` set AUTO_INCREMENT = 2; ";

        if($debug){
            print_R($alter_table_query);
        }else{ 
            sql_query($alter_table_query);
        }
        
    }
    
}

if($debug){}else{
    if($result){
        alert('정상 처리되었습니다.');
        goto_url('./config_reset.php');
    }
}
?>