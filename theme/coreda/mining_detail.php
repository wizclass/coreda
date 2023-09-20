<?
    include_once('./_common.php');
    include_once(G5_THEME_PATH.'/_include/wallet.php');
    $menubar =1;
    include_once(G5_THEME_PATH.'/_include/gnb.php');
   
    $title = 'Mining_record';
    $mining_day = $_GET['day'];
    $category = 'super_mining';
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

<link href="<?=G5_THEME_URL?>/css/scss/page/mining.css" rel="stylesheet">
<style>
    .overcharge{color:red; font-size:12px;}
    .origin_total{color:red;font-size:16px;font-weight:500}
    .over_desc{color:red;font-size:14px;font-weight:500}
</style>

<main>
    <div class='container'>

        <div class="col-sm-12 col-12 content-box mining_detail round mt20" id="<?= $cate ?>">
            <div class="box-header row">
                <?
                    $sub_sql = "SELECT *,SUM(mining) as total_mining, SUM(CASE WHEN overcharge = 0 THEN mining ELSE overcharge END) AS origin_total FROM soodang_mining WHERE day = '{$mining_day}' AND mb_id = '{$member['mb_id']}' and allowance_name='{$category}' GROUP BY day";
                    $sub_result = sql_fetch($sub_sql);
                ?>
                <div class='col-12 text-left'>
                    <span><?= $sub_result['day'] ?></span>
                    <span class='m_hist_exp'> [<?= strtoupper($sub_result['allowance_name']) ?> BONUS ]</span>
                </div>
            </div>
            <div class='row'>
                <div class='col-12 text-right hist_value'>
                    <span style="color: #ffd965;"><?=shift_auto($sub_result['total_mining'],'eth') ?> <?=$minings[$now_mining_coin]?></span>
                    <?if($sub_result['total_mining'] < $sub_result['origin_total']){?>
                        <br><span class='over_desc'>초과로 받지못한 보너스 : </span> <span class='origin_total'><?=($sub_result['origin_total'] - $sub_result['total_mining'])?> <?=$minings[$now_mining_coin]?></span>
                    <?}?>
                </div>
            </div>
        </div>

        <div class="col-sm-12 col-12 content-box mining_history round  mb20">
            <div class="box-header">
                <h4><i class="ri-calendar-event-line"></i> <?= $mining_day ?></h4>
            </div>

            <div class="box-body">
                <?
                    $detail_sql = "SELECT * FROM soodang_mining WHERE day = '{$mining_day}' AND mb_id = '{$member['mb_id']}' and allowance_name='{$category}' ";
                    $detail_result = sql_query($detail_sql);
                    while($rows = sql_fetch_array($detail_result) ){
                        $detail = explode('|',$rows['rec']);    
                    ?>
                <div class="block row">
                    <div class='col-7 text-left' style='padding-right:0;'>
                        <span class='m_hist_exp'>
                            <?= $detail[0] ?><br>
                            <?= $detail[1] ?>
                            
                        </span>
                    </div>
                    <div class='col-5 text-right '>
                        <span> <i class="ri-add-line"></i></span>
                        <span class='hist_value2'><?=shift_auto($rows['mining'],'eth') ?> <?=$minings[$now_mining_coin]?></span>
                        <?if($rows['overcharge'] != 0){?>
                            <br><span class='overcharge'><?=$rows['overcharge']?> <?=$minings[$now_mining_coin]?></span>
                        <?}?>
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