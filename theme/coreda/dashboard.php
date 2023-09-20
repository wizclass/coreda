<?
	include_once('./_common.php');
    require_once(G5_THEME_PATH.'/_include/head.php');
	include_once(G5_THEME_PATH.'/_include/wallet.php');
	include_once(G5_THEME_PATH.'/_include/gnb.php');
	include_once(G5_PATH.'/util/package.php');
	include_once(G5_LIB_PATH.'/fcm_push/set_fcm_token.php');

	login_check($member['mb_id']);
    $member_info = sql_fetch("SELECT * FROM g5_member_info WHERE mb_id ='{$member['mb_id']}' order by date desc limit 0,1 ");
    
?>

<link rel="stylesheet" href="<?=G5_THEME_URL?>/css/default.css">
<script src="<?=G5_URL?>/js/common.js"></script>


<?php
		if(defined('_INDEX_')) { // index에서만 실행
			include G5_BBS_PATH.'/newwin.inc.php'; // 팝업레이어
		}
		$package = package_have_return($member['mb_id']);

	?>


<?include_once(G5_THEME_PATH.'/_include/breadcrumb.php');?>
<main>
    <div class='container dashboard'>
        <div class="my_btn_wrap">
            <div class='row'>
                <div class='col-lg-6 col-12'>
                    <button type='button' class='btn wd main_btn b_sub' onclick="go_to_url('mywallet');"> USDT 거래소</button>
                </div>
                <div class='col-lg-6 col-12'>
                    <button type='button' class='btn wd main_btn b_main' onclick="go_to_url('upstairs');">패키지구매</button>
                </div>
                <!-- <div class='col-lg-12 col-12'>
							<button type='button' class='btn wd main_btn b_third' onclick="move_to_shop()" >쇼핑몰바로가기</button>
						</div> -->
            </div>
        </div>

<!-- 
        <div style="clear:both;"></div>
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        <div class='r_card_wrap content-box round mb30 mt30'>      
            
            <div style='max-width:700px;width:100%;text-align:center;margin:0 auto;'>
                <div id="myChart2"></div>
            </div>
            
        </div>

        

        <script>
            var chart_data = JSON.parse('<?=($member_info['hash_info'])?>');
            $(function(){
                
                $(window).scroll(function () {
                    var wrapper_height = $(window).scrollTop();
                    
                    if(wrapper_height > 110){
                    chart();
                    chart = function(){};
                    }else{

                    }
                }); 

                function chart(){
                    var chart = new ApexCharts(document.querySelector("#myChart2"), options);
                        chart.render();
                }

                $('#mode_select').on('change',function(e) {
                    mode_colorset2(this.value);
                });
            });
        </script>
        <script src="<?=G5_THEME_URL?>/_common/js/chart/apexchart.js"></script> -->

        <div class='r_card_wrap content-box round mt30'>
            <?$ordered_items = ordered_items($member['mb_id']);?>
            <div class="card_title">보유 패키지 (<?=count($ordered_items)?>) <a href='<?=G5_URL?>/page.php?id=upstairs'
                    class='f_right inline more'><span>더보기<i class="ri-add-circle-fill"></i></span></a></div>
            <?
					if(count($ordered_items) < 1) { 
							echo "<div class='no_data'>내 보유 상품이 존재하지 않습니다</div>";
					}else{
						
						for($i = 0; $i < count($ordered_items); $i++){
							$row = $ordered_items[$i];
							?>
							<div class="col-12 r_card_box">
							<a href='/page.php?id=upstairs'>
							
								<div class="r_card r_card_<?=substr($row['od_name'],1,1)?>">
									<p class="title">
										<span style='font-size:14px;'><?=$ordered_items[$i]['it_option_subject']?></span> 
										- <?=$ordered_items[$i]['it_name']?>
										<span class='f_right more_arrow'><img src="<?=G5_THEME_URL?>/img/arrow.png" alt=""></span>
									</p>
									<div class="b_blue_bottom"></div>
									<div class="text_wrap">
										<span class="value1"><?=$row['od_time']?></span>
									</div>
								</div>
							</a>
							</div>
						<?}
					}
					?>
        </div>


        <div class='r_card_wrap content-box round history_latest'>
            <div class="card_title_wrap">
                <div class="card_title">최근 발생 보너스 </div>
                <a href='<?=G5_URL?>/page.php?id=bonus_history'
                    class='inline more'><span>더보기<i class="ri-add-circle-fill"></i></span></a>
            </div>
            <?
					$bonus_history_sql	 = "SELECT * from `{$g5['bonus']}` WHERE mb_id = '{$member['mb_id']}' order by day desc limit 0,5";
					$bonus_history_result = sql_query($bonus_history_sql);
					$bonus_history_cnt = sql_num_rows($bonus_history_result);
					if($bonus_history_cnt > 0){
						while($row = sql_fetch_array($bonus_history_result)){
					?>

            <div class="line row">
                <div class='col-7'>
                    <span class='day'><?=timeshift($row['day'])?> </span>
                    <span class='category'><?=strtoupper($row['allowance_name'] == "booster" ? "matching" : $row['allowance_name'])?> BONUS</span>
                </div>
                <div class='col-5 text-right'>
                    <span class='price'><?=shift_auto($row['benefit'],$curencys[0])?> <?=$curencys[0]?> </span>
                </div>
            </div>

            <?}?>
            <?}else{
						echo "<div class='no_data'>보너스 내역이 존재하지 않습니다</div>";
					}?>
        </div>

        <div class='r_card_wrap content-box round regist_latest'>
            <div class="card_title_wrap">
                <div class="card_title">최근 추천 등록 회원 </div>
                <a href='<?=G5_URL?>/page.php?id=structure'
                    class='inline more'><span>더보기<i class="ri-add-circle-fill"></i></span>
                </a>
            </div>
            
            <?
					$bonus_history_sql	 = "SELECT * from `{$g5['member_table']}` WHERE mb_recommend = '{$member['mb_id']}' order by mb_open_date desc limit 0,3";
					$bonus_history_result = sql_query($bonus_history_sql);
					$bonus_history_cnt = sql_num_rows($bonus_history_result);
					if($bonus_history_cnt > 0){
						while($row = sql_fetch_array($bonus_history_result)){
					?>

            <div class="line row">
                <div class='col-9'>
                    <span class='badge'><?=$member_level_array[$row['mb_level']]?> </span>
                    <span class='badge color<?=user_grade($member['mb_id'])?>'><?=$row['grade'].' star'?> </span>
                    <span class='id'><?=$row['mb_id']?> </span>

                </div>
                <div class='col-3 text-right'>
                    <span class='day'><?=timeshift($row['mb_open_date'])?> </span>
                </div>
            </div>

            <?}?>
            <?}else{
						echo "<div class='no_data'>추천 등록 회원이 존재하지 않습니다</div>";
					}?>
        </div>
    </div>
</main>

<? include_once(G5_THEME_PATH.'/_include/tail.php'); ?>