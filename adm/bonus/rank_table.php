<?php
$sub_menu = "600400";
include_once('./_common.php');
include_once(G5_PATH.'/util/package.php');

$g5['title'] = "패키지 상품 현황";
 
include_once(G5_ADMIN_PATH.'/admin.head.php');
include_once(G5_PLUGIN_PATH.'/jquery-ui/datepicker.php');

/* if (empty($fr_date)) $fr_date = date("Y-m-d", strtotime(date("Y-m-d")."-90 day"));
if (empty($to_date)) $to_date = G5_TIME_YMD; */


$rlevel = 'p3';
$rneth = 'ALL';
$promote = 'ALL';

if($_GET['rlevel']){
    $rlevel = $_GET['rlevel'];
}
if($_GET['rneth']){
    $rneth = $_GET['rneth'];
}
if($_GET['promote'] > -1){
    $promote = $_GET['promote'];
}


//주문번호
function order_number($val){
    $order_num = $val;
    $order_no = substr_replace($val,'-',8,0);
    return "<a href='/adm/shop_admin/orderlist.php?sel_field=od_id&search=".$order_num."' target='_blank'>".$order_no."</a>";
}
// 업그레이드 여부
function onPromote($val){
    if($val == 1){echo ' <span class=pro>업그레이드</span> ';}else{ echo ' - ';}
}

// 레벨선택
function onselect($val){
    global $rlevel;
    if($rlevel == $val){echo ' selected';}else{ echo '';}
}

function on_nth($val){
    global $rneth;
    if($val != 'ALL') { $nth = substr($val,1); }else{$nth = 'ALL';}
    if($rneth == $nth){echo ' selected';}else{ echo '';}
}

// 업데이트 날짜
function onPdate($val){
    if($val != '0000-00-00'){
        return $val;
    }
}


?>

<?
    $colspan = 9;
    // $to_date = date("Y-m-d", strtotime(date("Y-m-d")."+1 day"));
    $rlevel_table = 'package_'.$rlevel;
    // if($rlevel == 'r4'){
    //     $rlevel_table = 'package_r3';
    //     $rf = ' AND promote=1 ';
    // }else{
    //     $fr = '';
    // }

    $sql_common = " FROM {$rlevel_table} ";
    $sql_search = " where 1=1 ";

    if($rneth && $rneth != 'ALL'){
        $sql_search .= " AND nth = '{$rneth}'";
    }
    
    if($fr_id){
        $sql_search .= " AND mb_id = '{$fr_id}'";
    }
    
    
    if($promote != 'ALL'){
        $promote_search = " AND promote = '{$promote}' ";
    }else{
        $promote_search = '';
    }

    $sql_search .= $rf;

    $total_sql = " select count(*) as cnt {$sql_common} {$sql_search} ";
    $total_cnt = sql_fetch($total_sql)['cnt'];

    $waiting_sql = " select count(*) as cnt {$sql_common} {$sql_search} AND promote != 1";
    $waiting_cnt = sql_fetch($waiting_sql)['cnt'];

    $promote_sql = " select count(*) as cnt {$sql_common} {$sql_search} AND promote = 1";
    $promote_cnt = sql_fetch($promote_sql)['cnt'];


    $sql = " select count(*) as cnt
            {$sql_common}
            {$sql_search}{$promote_search}";
    $rows = sql_fetch($sql);
    $total_count = $rows['cnt'];

    $rows = 100;
    $total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
    if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
    $from_record = ($page - 1) * $rows; // 시작 열을 구함

    $sql = " select *
            {$sql_common}
            {$sql_search}
            {$promote_search}
            order by no asc
            limit {$from_record}, {$rows} ";

    $result = sql_query($sql);

    $cate_sql = "SELECT count(distinct nth) AS cate from {$rlevel_table} ";
    $cate_result = sql_fetch($cate_sql);
    $cate_cnt = $cate_result['cate']+1;

    $qstr = "fr_date=".$fr_date."&amp;to_date=".$to_date."&amp;to_id=".$fr_id."&rlevel=".$rlevel."&rneth=".$rneth;
    if($promote != 'ALL'){
        $qstr .= "&promote".$promote;
    }
    $query_string = $qstr ? '?'.$qstr : '';
?>

<style>
    .red{color:red}
    .text-center{text-align:center}
    .sch_last{display:inline-block;}
    .rank_img{width:20px;height:20px;margin-right:10px;}
    .btn_submit{width:100px;height:30px; margin-left:20px;}
    .btn.reset_btn{width:100px;height:30px; margin-left:20px;background:black;border-radius:0;color:white}
    .black_btn{background:#333 !important; border:1px solid black !important; color:white;}

    .local_sch .btn_submit{height:30px;}
    .selectbox select{width:150px;height:30px;}
    .inline{display:inline-block;}
    .inline label {font-weight: 600;margin-left:10px;}
    .pro{color:red}

    .local_ov strong{color:red}
</style>


<script>
$(function(){
    $("#fr_date, #to_date").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", maxDate: "+0d" });

    /* $("#select_rlevel").change(search_param);
    $("#select_nth").change(search_param);

    function search_param(){
        var rlevel = $("#select_rlevel").val();
        var rneth = $("#select_nth").val();
    } */

    $('#membership_reset').on('click',function(){

        if (confirm('멤버쉽 구매 제한을 초기화하시겠습니까?')) {
			} else {
				return false;
            }
            
        $.ajax({
            url: "/util/limit_reset.php",
            type:"post",
            dataType: "json",
            data: {
                func : 'reset'
            },
            success: function(data){
                if(data.code == '0000'){
                    alert('정상적으로 초기화되었습니다.');
                    location.reload();
                }
            }
        })
    });

});

function fvisit_submit(act)
{
    var f = document.fvisit;
    f.action = act;
    f.submit();
}

function select_change(f){
   
    $('#input_rlevel').val($('#select_rlevel').val());
    
    $('#input_rneth').val($('#select_nth').val());
}
</script>
<!-- 
<a href='<?=$query_string?>&promote=ALL'><label>전체 : <?=$total_cnt?></label></a> / 
            <a href='<?=$query_string?>&promote=0'><label>승급대기: <?=$waiting_cnt?></label></a> / 
            <a href='<?=$query_string?>&promote=1'><label>승급 : <?=$promote_cnt?></label></a>
 -->
<!-- location.href='<?php echo $_SERVER['SCRIPT_NAME']?>?fg_no='+this.value; -->
<div class="local_ov01 local_ov">
	<a href="<?=$query_string?>&promote=ALL" class="ov_listall">전체상품목록</a><a href="<?=$query_string?>&promote=ALL">	총 <strong><?=$total_cnt?></strong> 중,</a>
    <strong><?=$total_cnt?></strong> 개
	<!-- <a href="<?=$query_string?>&promote=0" style='padding-left:10px;'>승급대기 :<strong><?=$waiting_cnt?></strong> 개,</a> 
	<a href="<?=$query_string?>&promote=1" style='padding-left:10px;'>승급 : <strong><?=$promote_cnt?></strong> 개</a> -->
</div>

<section class='rank_table'>

    <form name="frank" id="frank" class="local_sch02 local_sch" method="get">

    <input type='hidden' name='rlevel' id='input_rlevel' value='<?=$_GET['rlevel']?>'>
    <input type='hidden' name='rneth' id='input_rneth' value='<?=$_GET['rneth']?>'>

        <div class="selectbox inline">
            <label for='select_rlevel'>패키지 CLASS 선택 : </label>
            <select id='select_rlevel' onchange="select_change(this);">
                <!-- <option value='p1' <?=onselect('p1')?> >제주도</option>
                <option value='p2' <?=onselect('p2')?> >동남아</option>
                <option value='p3' <?=onselect('p3')?> >유럽</option>
                <option value='p4' <?=onselect('p4')?> >아프리카</option>
                <option value='p5' <?=onselect('p5')?> >알레스카(크루즈)</option> -->

                <?
                    $sql = "SELECT * FROM g5_item ";
                    $result = sql_query($sql);
                    while($row= sql_fetch_array($result)){
                        echo "<option value='{$row['it_maker']}'";
                        echo onselect("{$row['it_maker']}");
                        echo ">{$row['it_name']}</option>";
                    }
                ?>
            </select>
        </div>

        <div class="selectbox inline">
            <label for='select_nth'>회원 보유 수량별 선택 : </label>
            <select id='select_nth' onchange="select_change(this);" style='width:80px;'>
                <option value='ALL' <?=on_nth('ALL')?> >ALL</option>
                <?for($i=1; $i < $cate_cnt; $i++){?>
                    <option value='<?=$i?>' <?=on_nth('n'.$i)?> ><?=$i?></option>
                <?}?>
            </select>
        </div>
        
        <div class='inline'> 
            <label for="fr_id">멤버아이디</label>
            <input type="text" name="fr_id" value="<?php echo $fr_id ?>" id="fr_id" class="frm_input" size="15" style="width:120px" maxlength="10">
        </div>

        <div class='inline'> 
            <input type="submit" value="검색" class="btn_submit">
            <!-- <button type="button" id='membership_reset' class="btn reset_btn">멤버쉽제한리셋</button> -->
        </div>

    </form>

    

    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.3.0/fonts/remixicon.css" rel="stylesheet">
    <div class="tbl_head01 tbl_wrap">
        <table>
        <caption><?php echo $g5['title']; ?> 목록</caption>
        <thead>
        <tr>
            <th>no</th>
            <th>구매순서</th>
            <th>회원아이디</th>
            <th>보유수량</th>
            <th>해당상품코드</th>
            <th>상품구매일</th>
            <th>구매주문번호</th>
           <!--  <th>업그레이드</th>
            <th>업그레이드일</th> -->
        </tr>
        </thead>
        <tbody>
        <?php
        for ($i=0; $row=sql_fetch_array($result); $i++) {
            $bg = 'bg'.($i%2);
        ?>
        
        <tr class="<?php echo $bg; ?>">
            <td class='no text-center'><?=$row['no']?></td>
            <td class='no text-center'><?=$row['idx']?></td>
            <td class='text-center'><strong><?=$row['mb_id']?></strong></td>
            <td class='no text-center'><?=$row['nth']?></td>
            <td class='text-center'> <?=$row['it_name'];?></td>
            <td class='text-center'><?=$row['cdatetime'];?></td>	
            <td class='text-center'> <?=order_number($row['od_id']);?></td>
            <!-- <td class='text-center'><?=onPromote($row['promote']);?> </td>
            <td class='text-center'><?=onPdate($row['pdate']);?></td> -->
        </tr>

        <?php
        }
        if ($i == 0)
            echo '<tr><td colspan="'.$colspan.'" class="empty_table">자료가 없거나 관리자에 의해 삭제되었습니다.</td></tr>';
        ?>
        </tbody>
        </table>
    </div>

    <?php
    if (isset($domain))
        $qstr .= "&amp;domain=$domain";
        $qstr .= "&amp;page=";
        
    $pagelist = get_paging($config['cf_write_pages'], $page, $total_page, "{$_SERVER['SCRIPT_NAME']}?$qstr");
    echo $pagelist;
    ?>

</section>







<?
include_once (G5_ADMIN_PATH.'/admin.tail.php');
?>


