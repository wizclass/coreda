<?php
$sub_menu = "700300";
include_once('./_common.php');
include_once(G5_THEME_PATH.'/_include/wallet.php');

auth_check($auth[$sub_menu], 'r');

$g5['title'] = "입금 요청 내역";
$excel_down = true;

include_once('./adm.header.php');

// tx hash - 사용안함
/* function short_code($string, $char = 8){
	return substr($string,0,$char)." ... ".substr($string,-8);
} */

$status_string = array('요청확인중','승인','대기','불가','취소');

function status($val){
    global $status_string;
    return $status_string[$val];
}


/* 조건검색*/
if($_GET['fr_id']){
	$sql_condition .= " and A.mb_id = '{$_GET['fr_id']}' ";
	$qstr .= "&fr_id=".$_GET['fr_id'];
}

if($fr_date && $to_date){
	$sql_condition .= " and DATE_FORMAT(A.create_dt, '%Y-%m-%d') between '{$fr_date}' and '{$to_date}' ";
	$qstr = "fr_date=".$fr_date."&amp;to_date=".$to_date."&amp;to_id=".$fr_id;
}

if($_GET['update_dt']){
	$sql_condition .= " and DATE_FORMAT(A.update_dt, '%Y-%m-%d') = '".$_GET['update_dt']."'";
	$qstr .= "&update_dt=".$_GET['update_dt'];
}

if($_GET['status'] != ''){
	// echo $_GET['status']."<Br><br>";
	$sql_condition .= " and A.status = '".$_GET['status']."'";
	$qstr .= "&status=".$_GET['status'];
}


if($_GET['ord']!=null && $_GET['ord_word']!=null){
	$sql_ord = "order by ".$_GET['ord_word']." ".$_GET['ord'];
}


$colspan = 13;
// $to_date = date("Y-m-d", strtotime(date("Y-m-d")."+1 day"));

$sql_common = " from {$g5['deposit']} as A";
$sql_search = " WHERE 1=1 ".$sql_condition;
$sql = " select count(create_d) as cnt, sum(in_amt) as hap
{$sql_common}
{$sql_search}";
$rows = sql_fetch($sql);
$total_count = $rows['cnt'];
$total_hap = $rows['hap'];

$rows = 100;
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql = " select *
            {$sql_common}
            {$sql_search}
            order by create_dt desc
            limit {$from_record}, {$rows} ";
$result = sql_query($sql);

?>



<script>
	$(function(){
        $('.admin_memo').on('change',function(){

        $contents = $(this).val();

        $.ajax({
            url: './adm.memo.api.php',
            type: 'POST',
            cache: false,
            async: false,
            data: {
                "contents": $contents,
                "uid": $(this).data('uid'),
                "category" : $(this).data('category')
            },
            dataType: 'json',
            success: function(result) {
                if (result.result == "success") {
                    
                }else {
                    alert("정상처리되지 않았습니다.");
                }
            },
            error: function(e) {
                alert("정상처리되지 않았습니다.");
            }
        });
        });

        // 바이너리 추가
        /* $('.add_binary').on('click',function(){
            var mb_id = $(this).data('id');
            var func = $(this).data('func');

            $.ajax({
                url: '/adm/adm.add_binary.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    mb_id : mb_id,
                    func : func
                },
                success: function(res) {
                    if(res.result == "success"){
                        alert(res.msg);
                        location.reload();
                    }else{
                        alert("처리되지 않았습니다.");
                    }
                },
                error: function(e){
                    if(debug) dialogModal('ajax ERROR','IO ERROR','failed'); 
                }
                
            });

        }); */

		$('.regTb [name=status]').on('change',function(e){
            var refund = 'N';
            var _target = $(this).parent().parent();

            var coin = _target.find('.coin').text();
            var amt = _target.find('.input_amt_val').val().replace(/,/g,'');

			if (confirm('상태값을 변경하시겠습니까?')) {
			} else {
				return false;
			}

			if($(this).val() == '1' && coin != '' && amt > 0){
				if (confirm('입금액을 반영하시겠습니까?')) {
					refund = 'Y';	
				} else {
					refund = 'N';
				}
			}

            console.log( `id : ${$(this).attr('uid')} / coin : ${coin} / amt_val : ${amt} / refund : ${refund}`);

			/* $.post( "/adm/adm.request_proc.php", {
				uid : $(this).attr('uid'),
				status : $(this).val(),
                refund : refund,
                coin : coin,
                amt : amt,
                func : 'deposit'
			}, function(data) {
				if(data.result =='success'){
                    if(data.code == 0001 || data.code == 0002){
                        alert('변경되었습니다.');
                    }else{
					    alert('변경되었습니다.');
                    }
					location.reload();
				}else{
					alert("처리되지 않았습니다.");
				}
			},'json'); */

            $.ajax({
                url: '/adm/adm.request_proc.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    uid : $(this).attr('uid'),
                    status : $(this).val(),
                    refund : refund,
                    coin : coin,
                    amt : amt,
                    func : 'deposit'
                },
                success: function(res) {
                    if(res.result == "success"){
                        alert(res.msg);
                        location.reload();
                    }else{
                        alert("처리되지 않았습니다.");
                    }
                },
                error: function(e){
                    if(debug) dialogModal('ajax ERROR','IO ERROR','failed'); 
                }
                
            });
		});


		$.datepicker.regional["ko"] = {
			closeText: "close",
			prevText: "이전달",
			nextText: "다음달",
			currentText: "오늘",
			monthNames: ["1월(JAN)","2월(FEB)","3월(MAR)","4월(APR)","5월(MAY)","6월(JUN)", "7월(JUL)","8월(AUG)","9월(SEP)","10월(OCT)","11월(NOV)","12월(DEC)"],
			monthNamesShort: ["1월","2월","3월","4월","5월","6월", "7월","8월","9월","10월","11월","12월"],
			dayNames: ["일","월","화","수","목","금","토"],
			dayNamesShort: ["일","월","화","수","목","금","토"],
			dayNamesMin: ["일","월","화","수","목","금","토"],
			weekHeader: "Wk",
			dateFormat: "yymmdd",
			firstDay: 0,
			isRTL: false,
			showMonthAfterYear: true,
			yearSuffix: ""
		};
		$.datepicker.setDefaults($.datepicker.regional["ko"]);

		$("#create_dt_fr,#create_dt_to, #update_dt").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", maxDate: "+0d" });
    });
</script>



<div class="local_ov01 local_ov" style="display: flex; align-items: center">
	<a href="./adm.deposit_request.php?<?=$qstr?>" class="ov_listall"> 결과통계 <?=$total_count?> 건 = <?=shift_auto($total_hap,$curencys[0])?> <?=$curencys[0]?></a> 
	<?
		// 현재 통계치
		$stats_sql = "SELECT status, sum(in_amt) as hap, count(in_amt) as cnt from {$g5['deposit']} as A WHERE 1=1 ".$sql_condition. " GROUP BY status";
		$stats_result = sql_query($stats_sql);

		while($stats = sql_fetch_array($stats_result)){
			// $Nresult = $total_result['hap'] ? round($total_result['hap'],2) : '0';
			// $Ncount =  $total_result['cnt'];
			echo "<a href='./adm.deposit_request.php?".$qstr."&status=".$stats['status']."'><span class='tit'>";
			echo status($stats['status']);
			echo "</span> : ".$stats['cnt'];
			echo "건 = <strong>".shift_auto($stats['hap'],$curencys[0])." ".$curencys[0]."</strong></a>";
		}
	?>
</div>

<div class="local_desc01 local_desc">
    <p>
        <strong>- 요청확인중 :</strong> 기본값 | <strong>승인 :</strong> 입금금액 포인트 반영 | <strong>대기 :</strong> 확인처리중 | <strong>불가 :</strong> 입금자, 입금액 불일치 - 입금액변경하여 처리가능 | <strong>취소 :</strong> 미승인처리<br>
        <!-- <strong>- 후원레그2 추가 : </strong> 기존회원중 후원레그2 수동 추가 (신규입금자는 입금승인시 자동처리) -->
	</p>
    <p>
        <strong>- 입금요청금액 합계 :</strong> 관리자 지급/차감 제외하고 계산
    </p>
</div>

<div class="tbl_head01 tbl_wrap">
    <table class='regTb' id='table'>
    <caption><?php echo $g5['title']; ?> 목록</caption>
    <thead>
    <tr>
        <th scope="col" width='5%'>no</th>
        <th scope="col" width='7%'>아이디</th>
        <th scope="col" width='5%'>이름</th>
        <th scope="col" width='7%'>추천인</th>
        <!-- <th scope="col" width='5%'>센터</th> -->
        <th scope="col" width='15%'>입금정보</th>
        <th scope="col" width='5%'>입금요청금액</th>
        <th scope="col" width='4%'>입금종류</th>
        <th scope="col" width='5%'>입금처리금액(<?=$curencys[0]?>)</th>
        <th scope="col" width='5%'>시세(<?=$curencys[0]?>)</th>
        <th scope="col" width='10%'>승인여부</th>
        <th scope="col" width='8%'>요청시간</th>
        <th scope="col" width='8%'>상태변경일</th>
        <!-- <th scope="col" width='6%'>조직도등록</th> -->
        <!-- <th scope="col" width='10%'>추가항목2</th> -->
        <th scope="col" style="width:10%;">관리자메모</th>
    </tr>
    </thead>
    <tbody>
    <?php
    for ($i=0; $row=sql_fetch_array($result); $i++) {
        $bg = 'bg'.($i%2);
        $duplicate_sql ="select COUNT(*) as cnt from wallet_deposit_request WHERE mb_id='{$row['mb_id']}' ";
        $duplicate_result = sql_fetch($duplicate_sql);
        $duplicate = $duplicate_result['cnt'];
        if($duplicate > 1){$row_dup = 'row_dup';}else{$row_dup = '';}

        $member_sql = "SELECT A.mb_name,A.mb_recommend,A.mb_sponsor,B.mb_brecommend,(SELECT mb_nick from g5_member WHERE mb_id = A.mb_center) as mb_center from g5_member A, g5_member B WHERE A.mb_id = '{$row['mb_id']}' AND B.mb_id = A.mb_recommend";
        $member_result = sql_fetch($member_sql);

        $member_binary_sql = sql_fetch("SELECT A.mb_brecommend,B.mb_id FROM g5_member A  LEFT JOIN g5_member_binary B ON A.mb_id = B.mb_id WHERE A.mb_id ='{$row['mb_id']}' ");
        $member_binary = $member_binary_sql['mb_brecommend'];
        $member_binary2 = $member_binary_sql['mb_id'];
        $in_amt = shift_auto($row['in_amt']);

        
    ?>
   
    <tr class="bg0">
        <td ><?php echo $row['uid'] ?></td>
        <td class='td_id'><a href='/adm/member_form.php?sst=&sod=&sfl=&stx=&page=&w=u&mb_id=<?=$row['mb_id']?>' target='_blank'><?=$row['mb_id'] ?></a></td>
        <td style='color:#666'><?=$member_result['mb_name']?></td>
        <td style='color:#666'><?=$member_result['mb_recommend']?></td>
        <!-- <td style='color:#666'><?=$member_result['mb_center']?></td> -->

        <td style='color:#666'>
            <?=retrun_tx_func($row['txhash'],$row['coin'])?>
        </td>

        <td><?=shift_auto($row['amt'])?></td>
        <td class='coin'><?=$row['coin']?></td>
        <td><input type='text' class='reg_text input_amt_val' style='font-weight:600;color:blue;text-align:right' value='<?=shift_auto($row['in_amt'],$curencys[0])?>'></td>
        <td><?=shift_auto($row['cost'],'krw')?></td>
        <td>
            <!-- <?=status($row['status'])?> -->
            <select name="status" uid="<?=$row['uid']?>" class='sel_<?=$row['status']?>' <?=$row['status'] == 1 ? 'disabled':'';?>>
                <option <?=$row['status'] == 0 ? 'selected':'';?> value=0>요청확인중</option>
                <option <?=$row['status'] == 1 ? 'selected':'';?> value=1>승인</option>
                <option <?=$row['status'] == 2 ? 'selected':'';?> value=2>대기</option>
                <option <?=$row['status'] == 3 ? 'selected':'';?> value=3>불가</option>
            </select>	
        </td>
        <td class='time'><?=$row['create_dt']?></td>
        <td class='time'><?=$row['update_dt']?></td>
        <!-- <td>
           
            <?if(!$member_binary){?>
                <input type="button" class="inline_btn add_binary btn1" value='후원레그+' data-id='<?=$row['mb_id']?>' data-func='1'></input>
            <?}?>

            <?if(!$member_binary2){?>
                <input type="button" class="inline_btn add_binary btn2" value='후원레그2+' data-id='<?=$row['mb_id']?>' data-func='2'></input>
            <?}?>
        </td> -->
        <!-- <td>
        <?if($member_binary || $member_binary2){?>
            <input type="button" class="inline_btn add_binary btn3" value='후원레그삭제' data-id='<?=$row['mb_id']?>' data-func='3'></input>
        <?}?></td> -->

        <td>
            <textArea id='' class='admin_memo' name='memo' data-uid="<?=$row['uid']?>" data-category='deposit' ><?=$row['memo']?></textArea>
        </td>
    </tr>

    <?php
    $total_amt += $row['amt'];
    $total_inamt += $row['in_amt'];
    }
    if ($i == 0)
        echo '<tr><td colspan="'.$colspan.'" class="empty_table">자료가 없거나 관리자에 의해 삭제되었습니다.</td></tr>';
    ?>
    <tfoot>
    <tr>
        <td>합계</td>
        <td colspan="3"></td>
        <td class="text-right"><?=shift_auto($total_amt)?></td>
        <td></td>
        <td class="text-right"><?=shift_auto($total_inamt,2)?></td>
        <td colspan="6"></td>
    </tr>
    </tfoot>
    </tbody>
    </table>
</div>

<?php
if (isset($domain))
    $qstr .= "&amp;domain=$domain";
$qstr .= "&amp;page=";

$pagelist = get_paging($config['cf_write_pages'], $page, $total_page, "{$_SERVER['SCRIPT_NAME']}?$qstr");
echo $pagelist;


include_once('./admin.tail.php');
?>
