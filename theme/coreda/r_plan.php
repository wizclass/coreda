<?
	include_once('./_common.php');
	include_once(G5_THEME_PATH.'/_include/gnb.php');
	include_once(G5_THEME_PATH.'/_include/wallet.php');

	/*	쇼핑몰 관련 확인
	include_once(G5_PATH.'/lib/shop.lib.php');
	include_once(G5_THEME_PATH.'/_include/shop.php');
	*/

	//매출액
	$mysales = $member['mb_deposit_point'];

	//보너스/예치금 퍼센트
	$bonus_per = bonus_state($member['mb_id']);

	//시세 업데이트 시간
	$next_rate_time = next_exchange_rate_time();

	//내 직추천인
	$direct_reffer_sql = "SELECT count(mb_id) as cnt from g5_member WHERE mb_recommend = '{$member['mb_id']}' ";
	$direct_reffer_result = sql_fetch($direct_reffer_sql);
	$direct_reffer = $direct_reffer_result['cnt'];
	
	//하부매출 
	$recom_sale =  refferer_habu_sales($member['mb_id']);
	$recom_sale_power = refferer_habu_sales_power($member['mb_id']);
	$recom_sale_weak = ($recom_sale - $recom_sale_power);

	// 공지사항
	// $notice_sql = "select * from g5_write_notice where wr_1 = '1' order by wr_datetime desc";
	// $notice_sql_query = sql_query($notice_sql);
	// $notice_result_num = sql_num_rows($notice_sql_query);

	$title = 'r_plan';
?>



<section class='breadcrumb'>
	<ol style='width:50%'>
		<li class="active title" data-i18n="rplan.메인화면"><?=$title?></li>
		<li class='home'><i class="ri-home-4-line"></i><a href="<?php echo G5_URL; ?>" data-i18n='rplan.홈'>Home</a></li>
		<li><a href="/page.php?id=<?=$title?>" data-i18n="rplan.메인화면"><?=$title?></a></li>
	</ol>

	<!-- <ol class='f_right black' id='timer'>
		<div class='counters '>
			<div class='counter tx'>
				<span class='exchange_tx'>Exchange Rate</span>
				<p class='time_left_tx'>Time LEFT</p>
			</div>

			<div class='counter'>
				<span id='hours' class='num'>12</span>
				
				<p>H</p>
			</div>
			
			<div class='counter'>
				<span id='minutes' class='num'>00</span>
				
				<p>M</p>
			</div>
			
			<div class='counter'>
				<span id='seconds' class='num'>00</span>
				
				<p>S</p>
			</div>
		</div>
	<ol> -->

</section>


<main class="r_plan">
	<div class='container'>
		<!-- 공지사항 있는경우 -->
	<!-- <?if($notice_result_num > 0){ ?>
		
		<div class="col-sm-12 col-12 content-box round dash_news" style='margin-bottom:-10px;'>
			<h5>
				<span class="title" data-i18n='dashboard.공지사항' >Notification</span>

				<img class="close_news f_right small" src="<?=G5_THEME_URL?>/_images/close_round.gif" alt="공지사항 닫기">
				
				<button class="close_today f_right btn line_btn" >
					<span data-i18n="dashboard.오늘하루 열지않기"> Close for 24hrs</span>
				</button>
			</h5>

			<?while( $row = sql_fetch_array($notice_sql_query) ){ ?>
			<div>
				<span><?=$row['wr_content']?></span>
			</div>

			<?}?>
		</div>
	<?}?> -->


		<div class="col-sm-12 col-12 content-box round primary">
			
			<div class='user-content'>
				<li><p class='userid grade_<?=$member['grade']?>'></p></li>
				<li><p class='userid user_level'>
					<?=$user?>
					<!-- <i class='ri-user-line icon_user'></i><i class='ri-number-8 level_txt'></i> -->
				</p>
				</li>
				<li>
					<h4><?=$member['mb_id']?></h4>
					<!-- <h6><?=$member['mb_name']?></h6> -->
				</li>
				<!-- <?if($notice_result_num > 0){ ?>
					<button class="btn inline notice_open b_indianred text-white f_right" >
						<span data-i18n="dashboard.공지사항"> Notification</span>
					</button>
				<?}?> -->
			</div>

			<!-- <div class="innerBox round mt20">
				<dt class='col-6'><span> BONUS <?=BALANCE_CURENCY?> </span></dt>
				<dd class='col-6 '><?=$total_balance_num?> <?=BALANCE_CURENCY?></dd>
			</div> -->

			<div class="innerBox round mt20 col-sm-12" >
				<div class='bonus_state_bar' id='total_B_bar'></div>
				<dt class='col-6'><span class='t_shadow_white'>BONUS ETH</span></dt>
				<dd class='col-6'><?=number_format($total_bonus,2)?> <?=$curencys[1]?></dd>
			</div>
			<div class="innerBox round mt20 col-sm-12" >
				<div class='bonus_state_bar' id='total_B_bar'></div>
				<dt class='col-6'><span class='t_shadow_white'>TOTAL BONUS ETH</span></dt>
				<dd class='col-6'><?=number_format($total_fund,2)?> <?=$curencys[1]?></dd>
			</div>
			<!-- <div class='exp_per'>
				<p class='start'>0%</p>
				<p class='end'>100%</p>
			</div> -->

		</div>

		<div class="content-box round primary">
            <div class="row r_card_wrap">
                <div class="col-6 col-md-3 r_card round">
                    <div class="r_card_list round">
                        <a href="/page.php?id=bonus_history">
                            <p class="title">R1</p>
                            <div class="text_wrap">
                                <p class="value1">100</p>
                                <p class="value2">300</p>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-6 col-md-3 r_card round">
                    <div class="r_card_list round">
                        <a href="/page.php?id=bonus_history">
                            <p class="title">R1</p>
                            <div class="text_wrap">
                                <p class="value1">100</p>
                                <p class="value2">300</p>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-6 col-md-3 r_card round">
                    <div class="r_card_list round">
                        <a href="/page.php?id=bonus_history">
                            <p class="title">R1</p>
                            <div class="text_wrap">
                                <p class="value1">100</p>
                                <p class="value2">300</p>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-6 col-md-3 r_card round">
                    <div class="r_card_list round">
                        <a href="/page.php?id=bonus_history">
                            <p class="title">R1</p>
                            <div class="text_wrap">
                                <p class="value1">100</p>
                                <p class="value2">300</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
		</div>
</main>


	<div class="gnb_dim"></div>
</section>



<script src="<?=G5_THEME_URL?>/_common/js/timer.js"></script>
<script>
	window.onload = function(){
		// move(<?=$bonus_per?>,1);
		// getTime("<?=$next_rate_time?>");
	}

	$(function(){
		
		
		var notice_open = getCookie('notice');

		if(notice_open == '1'){
			$('.dash_news').css("display","none");
		}else{
			$('.dash_news').css("display","block");
		}

		// 공지사항 닫기
		$('.close_news').click(function(){
			$('.dash_news').css("display","none");
			$('.notice_open').css("display","block");
		});

		$('.close_today').click(function(){
			setCookie('notice', '1', 1);
			$('.dash_news').css("display","none");
			$('.notice_open').css("display","block");
		});


		$('.notice_open').click(function(){
			$('.dash_news').css("display","block");
			$(this).css("display","none");
		});

	});
</script>

<? include_once(G5_THEME_PATH.'/_include/tail.php'); ?>
