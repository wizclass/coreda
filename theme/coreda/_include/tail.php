
<?php 
$notice_sql = "select wr_subject from g5_write_notice order by wr_id desc limit 0,1";
$notice_result = sql_query($notice_sql);
$notice_row = sql_fetch($notice_sql);
?>

<?php if(sql_num_rows($notice_result) > 0 && $_GET['id'] != "news"){?>
<section class="notice_wrap">
	<div class='notice_box notice_oneline'>
		<a href="/page.php?id=news">
			<ul>
				<li>
					<span>새소식</span>
					<?=$notice_row['wr_subject']?>
				</li>				
			</ul>
		</a>
	</div>
</section>
<?php } ?>
	<div class="gnb_dim"></div>
</section>

<div class="dim"></div>
</body>
</html>
<script>
	// 테마변경 초기화
	$(function() {
		mode_init();
	});
</script>

<!-- <script src="<?=G5_THEME_URL?>/_common/js/jquery.vticker.min.js"></script>	 -->
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/i18next/1.9.0/i18next.min.js" type="text/javascript"></script> -->
<!-- <script>

//window.onload=function(){
$(document).ready(function(){
	i18n.init({ 
		lng: 'en',
		load: 'languageOnly',
		resGetPath: '/locales/__lng__.json', 
		//fallbackLng: false, 
		debug : false
	}, function (t){ 
		$("html").i18n(); 
	});
});
</script> -->

<!-- 공지사항 롤링 ticker -->
<!-- <script>
	$(document).ready(function() {
		$('.notice_oneline').vTicker({
			showItems: 1,
			mousePause: true,
			padding:20
		});
	});
</script> -->