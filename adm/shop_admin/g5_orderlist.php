<?php
$sub_menu = '700200';
include_once('./_common.php');

auth_check_menu($auth, $sub_menu, "r");

$g5['title'] = '주문내역';
include_once (G5_ADMIN_PATH.'/admin.head.php');
include_once(G5_PLUGIN_PATH.'/jquery-ui/datepicker.php');


if (empty($fr_date)) $fr_date = date("Y-m-d", strtotime(date("Y-m-d")."-3 months"));
if (empty($to_date)) $to_date = G5_TIME_YMD;

$where = array();

$sql_search = "";
if ($search != "") {
    if ($sel_field != "") {
        $where[] = " $sel_field like '%$search%' and od_status <>'재구매' ";
    }

    if ($save_search != $search) {
        $page = 1;
    }
}

if ($od_status) {
    switch($od_status) {
        case '전체취소':
            $where[] = " od_status = '취소' ";
            break;
        case '부분취소':
            $where[] = " od_status IN('주문', '입금', '준비', '배송', '완료') and od_cancel_price > 0 ";
            break;
        default:
            $where[] = " od_status = '$od_status' ";
            break;
    }

    switch ($od_status) {
        case '주문' :
            $sort1 = "od_time";
            $sort2 = "desc";
            break;
        case '입금' :   // 결제완료
            $sort1 = "od_time";
            $sort2 = "desc";
            break;
        case '배송' :   // 배송중
            $sort1 = "od_time";
            $sort2 = "desc";
            break;
    }
}

if ($od_settle_case) {
    $where[] = " od_settle_case = '$od_settle_case' ";
}

if ($od_name) {
    $where[] = " od_name = '$od_name' ";
}

if ($od_misu) {
    $where[] = " od_misu != 0 ";
}

if ($od_cancel_price) {
    $where[] = " od_cancel_price != 0 ";
}

if ($od_refund_price) {
    $where[] = " od_refund_price != 0 ";
}

if ($od_receipt_point) {
    $where[] = " od_receipt_point != 0 ";
}

if ($od_coupon) {
    $where[] = " ( od_cart_coupon > 0 or od_coupon > 0 or od_send_coupon > 0 ) ";
}

if ($od_escrow) {
    $where[] = " od_escrow = 1 ";
}

if ($fr_date && $to_date) {
    $where[] = " od_time between '$fr_date 00:00:00' and '$to_date 23:59:59' ";
}

if ($where) {
    $sql_search = ' where '.implode(' and ', $where);
}

if ($sel_field == "")  $sel_field = "mb_id";
if ($sort1 == "") $sort1 = "od_time";
if ($sort2 == "") $sort2 = "desc";

$sql_common = " from g5_order $sql_search ";

$sql = " select count(od_id) as cnt " . $sql_common;


$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = 100;
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql  = " select *
           $sql_common
           order by $sort1 $sort2
           limit $from_record, $rows ";
$result = sql_query($sql);

$qstr1 = "od_status=".urlencode($od_status)."&amp;od_settle_case=".urlencode($od_settle_case)."&amp;od_misu=$od_misu&amp;od_cancel_price=$od_cancel_price&amp;od_refund_price=$od_refund_price&amp;od_receipt_point=$od_receipt_point&amp;od_coupon=$od_coupon&amp;fr_date=$fr_date&amp;to_date=$to_date&amp;sel_field=$sel_field&amp;search=$search&amp;save_search=$search";
if($default['de_escrow_use'])
    $qstr1 .= "&amp;od_escrow=$od_escrow";
$qstr = "$qstr1&amp;sort1=$sort1&amp;sort2=$sort2&amp;page=$page";

$listall = '<a href="'.$_SERVER['SCRIPT_NAME'].'" class="ov_listall" style="margin: 0px 20px">전체목록</a>';

// 통계 데이터 산출
$stats_sql = "select od_name, COUNT(*) AS cnt, SUM(od_cash) AS amt ".$sql_common."group by od_name ";
$stats_result = sql_query($stats_sql);

// 구매상품명 리턴

function  od_name_return_rank($val){
    if(strlen($val) < 5){
        return substr($val,1,1);
    }else{
        return 0;
    }
}

// 주문삭제 히스토리 테이블 필드 추가
if(!sql_query(" select mb_id from {$g5['g5_shop_order_delete_table']} limit 1 ", false)) {
    sql_query(" ALTER TABLE `{$g5['g5_shop_order_delete_table']}`
                    ADD `mb_id` varchar(20) NOT NULL DEFAULT '' AFTER `de_data`,
                    ADD `de_ip` varchar(255) NOT NULL DEFAULT '' AFTER `mb_id`,
                    ADD `de_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `de_ip` ", true);
}
?>

<style>
	.local_ov strong{color:red; font-weight:600;}
	.local_ov .tit{color:black; font-weight:600;}
	.local_ov a{margin-left:20px;padding-right:10px;}

    .od_cancle{border:1px solid #ccc;background:white;border-radius: 0;padding:5px 10px;}
    .od_cancle:hover{background: black;;border:1px solid black;color:white}
    .cancle_log_btn{border-radius: 0;}
</style>
<link rel="stylesheet" href="/adm/css/scss/admin_custom.css">
<script src="../../excel/tabletoexcel/xlsx.core.min.js"></script>
<script src="../../excel/tabletoexcel/FileSaver.min.js"></script>
<script src="../../excel/tabletoexcel/tableExport.js"></script>

<form name="frmorderlist" class="local_sch01 local_sch">
<input type="hidden" name="doc" value="<?php echo $doc; ?>">
<input type="hidden" name="sort1" value="<?php echo $sort1; ?>">
<input type="hidden" name="sort2" value="<?php echo $sort2; ?>">
<input type="hidden" name="page" value="<?php echo $page; ?>">
<input type="hidden" name="save_search" value="<?php echo $search; ?>">
<input type="hidden" name="fr_date" value="<?=$fr_date?>">
<input type="hidden" name="to_date" value="<?=$to_date?>">

<label for="sel_field" class="sound_only">검색대상</label>
<select name="sel_field" id="sel_field">
    <option value="od_id" <?php echo get_selected($sel_field, 'od_id'); ?>>주문번호</option>
    <option value="mb_id" <?php echo get_selected($sel_field, 'mb_id'); ?>>회원 ID</option>
    <option value="od_name" <?php echo get_selected($sel_field, 'od_name'); ?>>주문자</option>
    <option value="od_tel" <?php echo get_selected($sel_field, 'od_tel'); ?>>주문자전화</option>
    <option value="od_hp" <?php echo get_selected($sel_field, 'od_hp'); ?>>주문자핸드폰</option>
    <option value="od_b_name" <?php echo get_selected($sel_field, 'od_b_name'); ?>>받는분</option>
    <option value="od_b_tel" <?php echo get_selected($sel_field, 'od_b_tel'); ?>>받는분전화</option>
    <option value="od_b_hp" <?php echo get_selected($sel_field, 'od_b_hp'); ?>>받는분핸드폰</option>
    <option value="od_deposit_name" <?php echo get_selected($sel_field, 'od_deposit_name'); ?>>입금자</option>
    <option value="od_invoice" <?php echo get_selected($sel_field, 'od_invoice'); ?>>운송장번호</option>
</select>

<label for="search" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
<input type="text" name="search" value="<?php echo $search; ?>" id="search" required class="required frm_input" autocomplete="off">
<!-- 구매상품 : 
<input type="text" name="od_name" value="<?php echo $od_name; ?>" id="od_name" class="required frm_input"> -->
<input type="submit" value="검색" class="btn_submit">


</form>

<form class="local_sch02 local_sch">
<!-- 
<div>
    <strong>주문상태</strong>
    <input type="radio" name="od_status" value="" id="od_status_all"    <?php echo get_checked($od_status, '');     ?>>
    <label for="od_status_all">전체</label>
    <input type="radio" name="od_status" value="주문" id="od_status_odr" <?php echo get_checked($od_status, '주문'); ?>>
    <label for="od_status_odr">주문</label>
    <input type="radio" name="od_status" value="입금" id="od_status_income" <?php echo get_checked($od_status, '입금'); ?>>
    <label for="od_status_income">입금</label>
    <input type="radio" name="od_status" value="준비" id="od_status_rdy" <?php echo get_checked($od_status, '준비'); ?>>
    <label for="od_status_rdy">준비</label>
    <input type="radio" name="od_status" value="배송" id="od_status_dvr" <?php echo get_checked($od_status, '배송'); ?>>
    <label for="od_status_dvr">배송</label>
    <input type="radio" name="od_status" value="완료" id="od_status_done" <?php echo get_checked($od_status, '완료'); ?>>
    <label for="od_status_done">완료</label>
    <input type="radio" name="od_status" value="전체취소" id="od_status_cancel" <?php echo get_checked($od_status, '전체취소'); ?>>
    <label for="od_status_cancel">전체취소</label>
    <input type="radio" name="od_status" value="부분취소" id="od_status_pcancel" <?php echo get_checked($od_status, '부분취소'); ?>>
    <label for="od_status_pcancel">부분취소</label>
</div>

<div>
    <strong>결제수단</strong>
    <input type="radio" name="od_settle_case" value="" id="od_settle_case01"        <?php echo get_checked($od_settle_case, '');          ?>>
    <label for="od_settle_case01">전체</label>

    <input type="radio" name="od_settle_case" value="현금" id="od_settle_case11"   <?php echo get_checked($od_settle_case, '현금');    ?>>
    <label for="od_settle_case11">현금</label>
    <input type="radio" name="od_settle_case" value="매장카드" id="od_settle_case12"   <?php echo get_checked($od_settle_case, '매장카드');    ?>>
    <label for="od_settle_case12">매장카드</label>
	



    <input type="radio" name="od_settle_case" value="무통장" id="od_settle_case02"   <?php echo get_checked($od_settle_case, '무통장');    ?>>
    <label for="od_settle_case02">무통장</label>
    <input type="radio" name="od_settle_case" value="가상계좌" id="od_settle_case03" <?php echo get_checked($od_settle_case, '가상계좌');  ?>>
    <label for="od_settle_case03">가상계좌</label>
    <input type="radio" name="od_settle_case" value="계좌이체" id="od_settle_case04" <?php echo get_checked($od_settle_case, '계좌이체');  ?>>
    <label for="od_settle_case04">계좌이체</label>
    <input type="radio" name="od_settle_case" value="휴대폰" id="od_settle_case05"   <?php echo get_checked($od_settle_case, '휴대폰');    ?>>
    <label for="od_settle_case05">휴대폰</label>
    <input type="radio" name="od_settle_case" value="신용카드" id="od_settle_case06" <?php echo get_checked($od_settle_case, '신용카드');  ?>>
    <label for="od_settle_case06">신용카드</label>
    <input type="radio" name="od_settle_case" value="간편결제" id="od_settle_case07" <?php echo get_checked($od_settle_case, '간편결제');  ?>>
    <label for="od_settle_case07">PG간편결제</label>
    <input type="radio" name="od_settle_case" value="KAKAOPAY" id="od_settle_case08" <?php echo get_checked($od_settle_case, 'KAKAOPAY');  ?>>
    <label for="od_settle_case08">KAKAOPAY</label>
</div>

<div>
    <strong>기타선택</strong>
    <input type="checkbox" name="od_misu" value="Y" id="od_misu01" <?php echo get_checked($od_misu, 'Y'); ?>>
    <label for="od_misu01">미수금</label>
    <input type="checkbox" name="od_cancel_price" value="Y" id="od_misu02" <?php echo get_checked($od_cancel_price, 'Y'); ?>>
    <label for="od_misu02">반품,품절</label>
    <input type="checkbox" name="od_refund_price" value="Y" id="od_misu03" <?php echo get_checked($od_refund_price, 'Y'); ?>>
    <label for="od_misu03">환불</label>
    <input type="checkbox" name="od_receipt_point" value="Y" id="od_misu04" <?php echo get_checked($od_receipt_point, 'Y'); ?>>
    <label for="od_misu04">PV주문</label>
    <input type="checkbox" name="od_coupon" value="Y" id="od_misu05" <?php echo get_checked($od_coupon, 'Y'); ?>>
    <label for="od_misu05">쿠폰</label>
    <?php if($default['de_escrow_use']) { ?>
    <input type="checkbox" name="od_escrow" value="Y" id="od_misu06" <?php echo get_checked($od_escrow, 'Y'); ?>>
    <label for="od_misu06">에스크로</label>
    <?php } ?>
</div>  -->


<div class="sch_last">
    <strong>주문일자</strong>
    <input type="text" id="fr_date"  name="fr_date" value="<?php echo $fr_date; ?>" class="frm_input" size="10" maxlength="10"> ~
    <input type="text" id="to_date"  name="to_date" value="<?php echo $to_date; ?>" class="frm_input" size="10" maxlength="10">
    <button type="button" onclick="javascript:set_date('오늘');">오늘</button>
    <button type="button" onclick="javascript:set_date('어제');">어제</button>
    <button type="button" onclick="javascript:set_date('이번주');">이번주</button>
    <button type="button" onclick="javascript:set_date('이번달');">이번달</button>
    <button type="button" onclick="javascript:set_date('지난주');">지난주</button>
    <button type="button" onclick="javascript:set_date('지난달');">지난달</button>
    <button type="button" onclick="javascript:set_date('전체');">전체</button>
    <input type="submit" style="padding: 5px; width: 100px" value="검색" class="btn_submit" style='width:100px;'> | 
    <input type="button" style="padding: 5px" class="btn_submit excel" id="btnExport"  data-name='hwajo_order_list' value="엑셀 다운로드" />
    <button type='button' class="btn cancle_log_btn" style='margin-left:10px'>취소 내역보기</button>
</div>

</form>


<div class="local_ov01 local_ov" style="display: flex; align-items: center">
    <?php echo $listall; ?>
    전체 주문내역 <?php echo number_format($total_count); ?>건
    
    <?
    while($stats = sql_fetch_array($stats_result)){
        echo "<a href='./orderlist.php?".$qstr."&od_name=".$stats['od_name']." '><span class='tit'>";
        echo $stats['od_name'];
        echo "</span> : ".$stats['cnt'];
        echo "건 = <strong>".Number_format($stats['amt'])."</strong></a>|";
    }
    ?>
</div>

<form name="forderlist" id="forderlist" onsubmit="return forderlist_submit(this);" method="post" autocomplete="off">
<input type="hidden" name="search_od_status" value="<?php echo $od_status; ?>">

<div class="tbl_head02 tbl_wrap">
    <table id="table">
    <caption>주문 내역 목록</caption>
    <thead>
    <tr>
        <th scope="col" >no</th>
        <th scope="col" id="th_odrid" >회원ID</th>
        <th scope="col" id="odrstat" >구매(매출)일자</th>
        <th scope="col" width="10%" id="th_odrnum"><a href="<?php echo title_sort("od_id", 1)."&amp;$qstr1"; ?>">주문번호</a></th>
        <th scope="col" id="odrstat" >주문상태</th>
        <th scope="col" id="odrstat" ><a href="<?php echo title_sort("od_name", 1)."&amp;$qstr1"; ?>">구매상품</th>
        <th scope="col" id="th_odrall" >결제금액</th>
        <th scope="col" id="odrpay" >결제수단</th>
		<th scope="col" id="th_odrcnt" >구매가격</th>
		<th scope="col" id="th_odrcnt" >판매실적(pv)</th>
        <!-- <th scope="col" >마이닝(MH/s)</th> -->
        <th scope="col" width="7%">관리</th>
    </tr>
    <tr></tr>
    <tr></tr>
    </thead>
    <tbody>
    <?php
    for ($i=0; $row=sql_fetch_array($result); $i++)
    {
        // 결제 수단
        $s_receipt_way = $s_br = "";
        if ($row['od_settle_case'])
        {
            $s_receipt_way = $row['od_settle_case'];
            $s_br = '<br />';

            // 간편결제
            if($row['od_settle_case'] == '간편결제') {
                switch($row['od_pg']) {
                    case 'lg':
                        $s_receipt_way = 'PAYNOW';
                        break;
                    case 'inicis':
                        $s_receipt_way = 'KPAY';
                        break;
                    case 'kcp':
                        $s_receipt_way = 'PAYCO';
                        break;
                    default:
                        $s_receipt_way = $row['od_settle_case'];
                        break;
                }
            }
        }
        else
        {
            $s_receipt_way = '결제수단없음';
            $s_br = '<br />';
        }

        if ($row['od_receipt_point'] > 0)
            $s_receipt_way .= $s_br."PV";

        $mb_nick = get_sideview($row['mb_id'], get_text($row['od_name']), $row['od_email'], '');

        $od_cnt = 0;
        if ($row['mb_id'])
        {
            $sql2 = " select count(*) as cnt from {$g5['g5_shop_order_table']} where mb_id = '{$row['mb_id']}' ";
            $row2 = sql_fetch($sql2);
            $od_cnt = $row2['cnt'];
        }

        // 주문 번호에 device 표시
        $od_mobile = '';
        if($row['od_mobile'])
            $od_mobile = '(M)';

        // 주문번호에 - 추가
        switch(strlen($row['od_id'])) {
            case 16:
                $disp_od_id = substr($row['od_id'],0,8).'-'.substr($row['od_id'],8);
                break;
            default:
                $disp_od_id = substr($row['od_id'],0,8).'-'.substr($row['od_id'],8);
                break;
        }

        // 주문 번호에 에스크로 표시
        $od_paytype = '';
        if($row['od_test'])
            $od_paytype .= '<span class="list_test">테스트</span>';

        if($default['de_escrow_use'] && $row['od_escrow'])
            $od_paytype .= '<span class="list_escrow">에스크로</span>';

        $uid = md5($row['od_id'].$row['od_time'].$row['od_ip']);

        $invoice_time = is_null_time($row['od_invoice_time']) ? G5_TIME_YMDHIS : $row['od_invoice_time'];
        $delivery_company = $row['od_delivery_company'] ? $row['od_delivery_company'] : $default['de_delivery_company'];

        $bg = 'bg'.($i%2);
        $td_color = 0;
        if($row['od_cancel_price'] > 0) {
            $bg .= 'cancel';
            $td_color = 1;
        }
        $od_settle_case = $row['od_settle_case'];
    ?>


	<style>
	/* td{width:140px !important;} */
	</style>
    <tr class="orderlist<?php echo ' '.$bg; ?>">
        <!-- <td rowspan="2" class="td_chk">
            <input type="hidden" name="od_id[<?php echo $i ?>]" value="<?php echo $row['od_id'] ?>" id="od_id_<?php echo $i ?>">
            <label for="chk_<?php echo $i; ?>" class="sound_only">주문번호 <?php echo $row['od_id']; ?></label>
            <input type="checkbox" name="chk[]" value="<?php echo $i ?>" id="chk_<?php echo $i ?>">
        </td> -->
        <td><?= $i + 1 ?></td>
		<td>
            <?php if ($row['mb_id']) { ?>
            <a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>?sort1=<?php echo $sort1; ?>&amp;sort2=<?php echo $sort2; ?>&amp;sel_field=mb_id&amp;search=<?php echo $row['mb_id']; ?>"><?php echo $row['mb_id']; ?></a>
            <?php } else { ?>
            비회원
            <?php } ?>
        </td>
        <td ><?php echo $row['od_date']; ?></td>
        <td headers="th_ordnum" class="td_odrnum2">
            <a href="<?php echo G5_SHOP_URL; ?>/orderinquiryview.php?od_id=<?php echo $row['od_id']; ?>&amp;uid=<?php echo $uid; ?>" class="orderitem"><?php echo $disp_od_id; ?></a>
            <?php echo $od_mobile; ?>
            <?php echo $od_paytype; ?>
        </td>
		<td class="td_odrstatus" style="width:150px;">
			<?php echo $row['od_status']; ?>
        </td>
        <td class="td_numsum " ><span class='badge t_white color<?=od_name_return_rank($row['od_name'])?>' ><?=$row['od_name']?></span></td>
        <td class="td_numsum" style='text-align:right'> <?=$od_settle_case?></td>
        <td style="text-align:center"><?php echo $row['od_settle_case'] ?></td>
		<td style="text-align:right;font-weight:600"> <?=$od_settle_case?></td>
		<td style="text-align:right"><?=number_format($row['upstair'])?> </td>
        <!-- <td > <?php echo $row['pv']; ?></td> -->
        <td style="text-align:center"><input type='button' class='btn od_cancle' value='구매취소' data-id="<?=$row['od_id']?>"></td>
       
    </tr>

    <tr class="<?php echo $bg; ?>">

    </tr>


    <?php
        $tot_itemcount     = $i+1;
        $tot_orderprice    += ($row['od_cart_price'] + $row['od_send_cost'] + $row['od_send_cost2']);
        $tot_ordercancel   += $row['od_cancel_price'];
        $tot_receiptprice  += $row['od_cash'];
		/*##  ################################################*/
        $tot_receiptcash  += $row['od_receipt_cash'];
        $tot_pv  += $row['pv'];
        $tot_bv  += $row['bv'];
		/*@@End.  #####*/

        $tot_couponprice   += $row['couponprice'];
        $tot_misu          += $row['od_misu'];
		$tot_odcount     = $i+1;
    }
    sql_free_result($result);
    if ($i == 0)
        echo '<tr><td colspan="13" class="empty_table">자료가 없습니다.</td></tr>';
    ?>
    </tbody>
    <tfoot>
    <tr class="orderlist">
        <th scope="row" colspan="3">&nbsp;</th>
        <td><?php echo number_format($tot_odcount); ?>건</td>
        <td>
            <!-- <?php echo number_format($tot_itemcount); ?>건 -->
        </td>
        <th scope="row">합 계</th>
        <td class="td_numsum" style='text-align:right; padding: 7px 5px'><?=$curencys[1]?></td>
        <td></td>
        <td style='text-align:right; padding: 7px 5px'><?=$curencys[1]?></td>
        <td></td>
        <td></td>
    </tr>
    </tfoot>
    </table>
</div>

<div class="local_cmd01 local_cmd">
<?php if (($od_status == '' || $od_status == '완료' || $od_status == '전체취소' || $od_status == '부분취소') == false) {
    // 검색된 주문상태가 '전체', '완료', '전체취소', '부분취소' 가 아니라면
?>
    <label for="od_status" class="cmd_tit">주문상태 변경</label>
    <?php
    $change_status = "";
    if ($od_status == '주문') $change_status = "입금";
    if ($od_status == '입금') $change_status = "준비";
    if ($od_status == '준비') $change_status = "배송";
    if ($od_status == '배송') $change_status = "완료";
    ?>
    <label><input type="checkbox" name="od_status" value="<?php echo $change_status; ?>"> '<?php echo $od_status ?>'상태에서 '<strong><?php echo $change_status ?></strong>'상태로 변경합니다.</label>
    <?php if($od_status == '주문' || $od_status == '준비') { ?>
    <input type="checkbox" name="od_send_mail" value="1" id="od_send_mail" checked="checked">
    <label for="od_send_mail"><?php echo $change_status; ?>안내 메일</label>
    <input type="checkbox" name="send_sms" value="1" id="od_send_sms" checked="checked">
    <label for="od_send_sms"><?php echo $change_status; ?>안내 SMS</label>
    <?php } ?>
    <?php if($od_status == '준비') { ?>
    <input type="checkbox" name="send_escrow" value="1" id="od_send_escrow">
    <label for="od_send_escrow">에스크로배송등록</label>
    <?php } ?>
    <input type="submit" value="선택수정" class="btn_submit" onclick="document.pressed=this.value">
<?php } ?>
    <?php if ($od_status == '주문') { ?> <span>주문상태에서만 삭제가 가능합니다.</span> <input type="submit" value="선택삭제" class="btn_submit" onclick="document.pressed=this.value"><?php } ?>
</div>
<!-- 
<div class="local_desc02 local_desc">
<p>
    &lt;무통장&gt;인 경우에만 &lt;주문&gt;에서 &lt;입금&gt;으로 변경됩니다. 가상계좌는 입금시 자동으로 &lt;입금&gt;처리됩니다.<br>
    &lt;준비&gt;에서 &lt;배송&gt;으로 변경시 &lt;에스크로배송등록&gt;을 체크하시면 에스크로 주문에 한해 PG사에 배송정보가 자동 등록됩니다.<br>
    <strong>주의!</strong> 주문번호를 클릭하여 나오는 주문상세내역의 주소를 외부에서 조회가 가능한곳에 올리지 마십시오.
</p>
</div> -->

</form>

<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, "{$_SERVER['SCRIPT_NAME']}?$qstr&amp;page="); ?>

<script>
$(function(){
    $("#fr_date, #to_date").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", maxDate: "+0d" });

    // 주문상품보기
    $(".orderitem").on("click", function() {
        var $this = $(this);
        var od_id = $this.text().replace(/[^0-9]/g, "");

        if($this.next("#orderitemlist").size())
            return false;

        $("#orderitemlist").remove();

        $.post(
            "./ajax.orderitem.php",
            { od_id: od_id },
            function(data) {
                $this.after("<div id=\"orderitemlist\"><div class=\"itemlist\"></div></div>");
                $("#orderitemlist .itemlist")
                    .html(data)
                    .append("<div id=\"orderitemlist_close\"><button type=\"button\" id=\"orderitemlist-x\" class=\"btn_frmline\">닫기</button></div>");
            }
        );

        return false;
    });

    // 상품리스트 닫기
    $(".orderitemlist-x").on("click", function() {
        $("#orderitemlist").remove();
    });

    $("body").on("click", function() {
        $("#orderitemlist").remove();
    });

    // 엑셀배송처리창
    $("#order_delivery").on("click", function() {
        var opt = "width=600,height=450,left=10,top=10";
        window.open(this.href, "win_excel", opt);
        return false;
    });


    // 구매취소 추가 
    $(".od_cancle").on('click',function(){

        if (confirm("해당구매건을 취소하시겠습니까?\n구매시 사용되었던금액이 반환됩니다.")) {
        } else {
            return false;
        }

        var od_id = $(this).data('id');
        
        $.ajax({
        url: './order_proc.php',
        type: 'POST',
        cache: false,
        dataType: 'json',
        data: {
          "od_id": od_id
        },
        success: function(result) {
          if (result.response == "OK") {
            alert("해당건 구매가 취소되었습니다.");
            location.reload();
          }else{
            alert("정상처리되지 않았습니다.");
            location.reload();
          }
        },
        error: function(e) {
            alert("시스템오류로 정상처리되지 않았습니다.");
        }

      });

    });

    $('.cancle_log_btn').on('click',function(){
        location.href = "../order_delete.php";
    });
});

function set_date(today)
{
    <?php
    $date_term = date('w', G5_SERVER_TIME);
    $week_term = $date_term + 7;
    $last_term = strtotime(date('Y-m-01', G5_SERVER_TIME));
    ?>
    if (today == "오늘") {
        document.getElementById("fr_date").value = "<?php echo G5_TIME_YMD; ?>";
        document.getElementById("to_date").value = "<?php echo G5_TIME_YMD; ?>";
    } else if (today == "어제") {
        document.getElementById("fr_date").value = "<?php echo date('Y-m-d', G5_SERVER_TIME - 86400); ?>";
        document.getElementById("to_date").value = "<?php echo date('Y-m-d', G5_SERVER_TIME - 86400); ?>";
    } else if (today == "이번주") {
        document.getElementById("fr_date").value = "<?php echo date('Y-m-d', strtotime('-'.$date_term.' days', G5_SERVER_TIME)); ?>";
        document.getElementById("to_date").value = "<?php echo date('Y-m-d', G5_SERVER_TIME); ?>";
    } else if (today == "이번달") {
        document.getElementById("fr_date").value = "<?php echo date('Y-m-01', G5_SERVER_TIME); ?>";
        document.getElementById("to_date").value = "<?php echo date('Y-m-d', G5_SERVER_TIME); ?>";
    } else if (today == "지난주") {
        document.getElementById("fr_date").value = "<?php echo date('Y-m-d', strtotime('-'.$week_term.' days', G5_SERVER_TIME)); ?>";
        document.getElementById("to_date").value = "<?php echo date('Y-m-d', strtotime('-'.($week_term - 6).' days', G5_SERVER_TIME)); ?>";
    } else if (today == "지난달") {
        document.getElementById("fr_date").value = "<?php echo date('Y-m-01', strtotime('-1 Month', $last_term)); ?>";
        document.getElementById("to_date").value = "<?php echo date('Y-m-t', strtotime('-1 Month', $last_term)); ?>";
    } else if (today == "전체") {
        document.getElementById("fr_date").value = "";
        document.getElementById("to_date").value = "";
    }
}
</script>

<script>
function forderlist_submit(f)
{
    if (!is_checked("chk[]")) {
        alert(document.pressed+" 하실 항목을 하나 이상 선택하세요.");
        return false;
    }

    /*
    switch (f.od_status.value) {
        case "" :
            alert("변경하실 주문상태를 선택하세요.");
            return false;
        case '주문' :

        default :

    }
    */

    if(document.pressed == "선택삭제") {
        if(confirm("선택한 자료를 정말 삭제하시겠습니까?")) {
            f.action = "./orderlistdelete.php";
            return true;
        }
        return false;
    }

    var change_status = f.od_status.value;

    if (f.od_status.checked == false) {
        alert("주문상태 변경에 체크하세요.");
        return false;
    }

    var chk = document.getElementsByName("chk[]");

    for (var i=0; i<chk.length; i++)
    {
        if (chk[i].checked)
        {
            var k = chk[i].value;
            var current_settle_case = f.elements['current_settle_case['+k+']'].value;
            var current_status = f.elements['current_status['+k+']'].value;

            switch (change_status)
            {
                case "입금" :
                    if (!(current_status == "주문" && current_settle_case == "무통장")) {
                        alert("'주문' 상태의 '무통장'(결제수단)인 경우에만 '입금' 처리 가능합니다.");
                        return false;
                    }
                    break;

                case "준비" :
                    if (current_status != "입금") {
                        alert("'입금' 상태의 주문만 '준비'로 변경이 가능합니다.");
                        return false;
                    }
                    break;

                case "배송" :
                    if (current_status != "준비") {
                        alert("'준비' 상태의 주문만 '배송'으로 변경이 가능합니다.");
                        return false;
                    }

                    var invoice      = f.elements['od_invoice['+k+']'];
                    var invoice_time = f.elements['od_invoice_time['+k+']'];
                    var delivery_company = f.elements['od_delivery_company['+k+']'];

                    if ($.trim(invoice_time.value) == '') {
                        alert("배송일시를 입력하시기 바랍니다.");
                        invoice_time.focus();
                        return false;
                    }

                    if ($.trim(delivery_company.value) == '') {
                        alert("배송업체를 입력하시기 바랍니다.");
                        delivery_company.focus();
                        return false;
                    }

                    if ($.trim(invoice.value) == '') {
                        alert("운송장번호를 입력하시기 바랍니다.");
                        invoice.focus();
                        return false;
                    }

                    break;
            }
        }
    }

    if (!confirm("선택하신 주문서의 주문상태를 '"+change_status+"'상태로 변경하시겠습니까?"))
        return false;

    f.action = "./orderlistupdate.php";
    return true;
}
</script>

<?php
include_once (G5_ADMIN_PATH.'/admin.tail.php');
?>
