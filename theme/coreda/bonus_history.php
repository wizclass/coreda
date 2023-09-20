<?
	include_once('./_common.php');
    include_once(G5_THEME_PATH.'/_include/wallet.php');
	include_once(G5_THEME_PATH.'/_include/gnb.php');

	//매출액
	$mysales = $member['mb_deposit_point'];

	//보너스/예치금 퍼센트
	$bonus_per = bonus_state($member['mb_id']);
    $title = 'bonus_history';

    if (empty($stx)) $stx = 'daily';

/*
    $sql_common ="FROM {$g5['bonus']} ";
	$sql_search = " WHERE ";
	$sql_search .= "day between '{$fr_date}' and '{$to_date}' ";
	$sql_search .= "AND mb_id = '{$member['mb_id']}' GROUP BY allowance_name ";

	$sql = " select allowance_name, COUNT(*) AS cnt
			{$sql_common}
			{$sql_search} ";
    print_R($sql);
*/


$sql ="SELECT a.cate, a.day,COUNT(DAY) AS cnt, SUM(a.c_sum) AS d_sum FROM
(
SELECT allowance_name AS cate, DAY, SUM(benefit) AS c_sum  FROM soodang_pay WHERE day between '{$fr_date}' and '{$to_date}' AND mb_id = '{$member['mb_id']}' GROUP BY allowance_name,DAY
) a GROUP BY a.cate";
    $result = sql_query($sql);
?>

<!-- <script src="http://code.jquery.com/jquery-1.11.1.min.js"></script> -->
<!-- <link rel="stylesheet" href="http://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css" />
<script src="http://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script> -->

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<?include_once(G5_THEME_PATH.'/_include/breadcrumb.php');?>
    <main>
        <div class='container bonus_history'>
            <!-- SEARCH -->
            <section class="col-sm-12 col-12 mt30 mb30" id="search-container">
                <form name="fsearch" id="fsearch" action="/page.php" method="GET">
                    <input type="hidden" name="id" id="" value="bonus_history">
                    <input type="hidden" name="stx" id="stx" value="">
                    <div class="row">
                        <li class="col-5"><input type="text" id="fr_date" name="fr_date" class='date_picker' placeholder="시작일" value=<?=$fr_date?> /></li>
                        <li class="col-5"><input type="text" id="to_date" name="to_date" class='date_picker' placeholder="종료일" value=<?=$to_date?> /></li>
                        <li class="col-2"><button type='button' class="btn wd inline blue filter_btn b_skyblue_round" onclick="search_submit();"><i class="ri-search-line"></i></button></li>
                    </div>
                </form>
            </section>
            <!-- //SEARCH -->
            <?php if(sql_num_rows($result) == 0) {?>
            <div class="no_data box_on">보너스 내역이 존재하지 않습니다</div>
            <?}?>
            <!-- 수당 -->
            <? while($row = sql_fetch_array($result) ){
                $row['cate'] = $row['cate'] == "booster" ? "matching" : $row['cate'];
            ?>
            <div class="col-sm-12 col-12 content-box round" id="<?=$row['cate']?>">

                <div class="box-header row">
                    <div class='col-7 text-left' style='font-size:13px;font-weight:600'>
                        <span><?=strtoupper($row['cate'])?> Bonus </span>
                        <span class='badge' style='font-size:12px;font-weight:300'><?=$row['cnt']?></span>
                    </div>

                    <div class='col-5 text-right nopadding'>
                        <span class='d_sum font_skyblue'> + <?=shift_auto($row['d_sum'],$curencys[0])?></span>
                        <span class='btn inline caret'><i class="ri-arrow-down-s-line"></i></span>
                    </div>
                </div>

                <div class="box-body history_detail">
                    <?
                        $row['cate'] = $row['cate'] == "matching" ? "booster" : $row['cate'];
                        $sub_sql = "SELECT *,SUM(benefit) as total_benefit FROM soodang_pay WHERE day between '{$fr_date}' and '{$to_date}' AND mb_id = '{$member['mb_id']}' and allowance_name='{$row['cate']}' GROUP BY DAY";
                        $sub_result = sql_query($sub_sql);
                        while($row_ = sql_fetch_array($sub_result) ){?>

                        <div class='inblock row' id="<?=$row['cate']?>_detail " data-target="<?=$row['cate']?>" data-day="<?=$row_['day']?>">
                            <dt><?=$row_['day']?></dt>
                            <dd>
                                <span> <i class="ri-add-line"></i></span>
                                <span><?=shift_auto($row_['total_benefit'],$curencys[0])?></span>
                                <a href="/dialog.php?id=bonus_detail&cate=<?=$row['cate']?>&day=<?=$row_['day']?>" dat-rel='dialog' data-transition="slideup"><span class='btn inline more_btn'><i class="ri-more-2-line"></i></span></a>
                            </dd>
                        </div>
                    <?}?>
                </div>

            </div>
            <?}?>
            <!-- // 수당 -->
    </main>
    <?php include_once(G5_THEME_PATH.'/_include/tail.php'); ?>

	<div class="gnb_dim"></div>
</section>

<script>
	$(function(){
		$(".top_title h3").html("<span >보너스내역</span>")
	});
</script>



<script>

var mb_balance = '<?=$member["mb_balance"]?>';

window.onload = function(){
  move(<?=$bonus_per?>);
}


function search_submit(act = null)
{
    // console.log('search');
    var f = document.fsearch;
    f.stx.value = act;
    f.submit();
}

$(function(){
    $('.hist').click(function () {
        var target = $(this).data('target');
    });

    $('.caret').click(function(){
        $(this).parent().parent().parent().find('.history_detail').slideToggle(300);
    });

    /*상단 분류 탭*/
    $('ul.tabs li').click(function () {
        search_submit($(this).attr('data-category'));
    });

    /*날짜선택 피커*/
    $("#fr_date, #to_date").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", maxDate: "+0d" });
});


jQuery(function($){
    $.datepicker.regional["ko"] = {
        closeText: "닫기",
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
});
</script>

