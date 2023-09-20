<?
    include_once('./_common.php');
    include_once(G5_THEME_PATH.'/_include/wallet.php');
    $menubar =1;
    include_once(G5_THEME_PATH.'/_include/gnb.php');
    
    
    $title = 'bonus_history';
    // $sub_title = 'Bonus detail';
    $BONUS_NUMBER_POINT = BONUS_NUMBER_POINT;
?>

<!-- <style>
body{background:#efefef}
header{
	position: relative;
	width: 100%;
	background:white;
	color: #000;
	text-align: left;
	box-sizing:border-box;
	padding:10px 15px;
    box-shadow:0 1px 0px rgba(0,0,0,0.25);
    font-size:24px;
    line-height: 24px;
    display: flex;
}
header .back_btn i{vertical-align: middle}
header h5{line-height: 28px;}
</style> -->


<section class='breadcrumb'>
    <!-- <ol>
        <li class="active title" data-i18n='bonus.보너스 내역'><?= $title ?></li>
        <li class='home'><i class="ri-home-4-line"></i><a href="<?php echo G5_URL; ?>" data-i18n='bonus.홈'>Home</a></li>
        <li><a href="/page.php?id=<?= $title ?>" data-i18n='bonus.보너스 내역'><?= $title ?></a></li>
    </ol> -->
</section>

<main>
    <div class='container'>

        <div class="col-sm-12 col-12 mt30 content-box round" id="<?= $cate ?>">
            <div class="box-header row">
                <?
                    $sub_sql = "SELECT *,SUM(benefit) as total_benefit FROM soodang_pay WHERE day = '{$val['day']}' AND mb_id = '{$member['mb_id']}' and allowance_name='{$val['cate']}' GROUP BY DAY";
                    $sub_result = sql_query($sub_sql);
                    while($row_ = sql_fetch_array($sub_result) ){?>
                <div class='col-7 text-left'>
                    <span style="font-size:15px"><?= $row_['day'] ?></span>
                    <span style="font-size:13px"> [ <?= strtoupper($row_['allowance_name'] == "booster" ? "matching" : $row_['allowance_name']) ?> ]</span>
                </div>

                <div class='col-5 text-right'>
                    <span> <i class="ri-add-line"></i></span>
                    <span><?=shift_auto($row_['total_benefit'],$curencys[0]) ?> </span>
                </div>
                <?}?>
            </div>
        </div>

        <div class="col-sm-12 col-12 content-box round history_detail mb20">
            <div class="box-header">
                <h4><i class="ri-calendar-event-line"></i> <?= $val['day'] ?></h4>
            </div>

            <div class="box-body">
                <?
                    $detail_sql = "SELECT * FROM soodang_pay WHERE day = '{$val['day']}' AND mb_id = '{$member['mb_id']}' and allowance_name='{$val['cate']}' ";
                    $detail_result = sql_query($detail_sql);
                    while($rows = sql_fetch_array($detail_result) ){?>
                <div class="block row">
                    <div class='col-8 text-left' >
                        <span class='b_hist_exp' style='font-size:12px;'><?= $rows['rec'] ?></span>
                    </div>
                    <div class='col-4 text-right'>
                        <span> <i class="ri-add-line"></i></span>
                        <span><?=Number_format($rows['benefit'],BONUS_NUMBER_POINT) ?> <?=$curencys[0]?></span>
                    </div>
                </div>
                <?}?>
            </div>

        </div>
</main>


<div class="gnb_dim"></div>
</section>


<script>
    $(function() {


        $('.back_btn').click(function() {
            //location.href='page.php?id=bonus_history';
            /*
            pageContainerElement.page({ domCache: false });
            $.domCache().remove();
            $.mobile.page.prototype.options.domCache = false;
            */
        });

    });
</script>

<? include_once(G5_THEME_PATH.'/_include/tail.php'); ?>