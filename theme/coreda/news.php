<?php
include_once('./_common.php');
include_once(G5_THEME_PATH.'/_include/head.php');
include_once(G5_THEME_PATH.'/_include/wallet.php');
include_once(G5_THEME_PATH.'/_include/gnb.php');


$bo_table = "g5_write_notice";
$bo_table_java = "notice";

$list_cnt = sql_fetch("select count(*) as cnt from {$bo_table} where wr_id=wr_parent order by wr_datetime desc");
$cnt = $list_cnt['cnt'];

$sql = "select * from {$bo_table} where wr_id=wr_parent order by wr_datetime desc";
$list = sql_query($sql);

$title = 'News';
?>

	<!--<link rel="stylesheet" href= "<?=G5_THEME_URL?>/theme/css/style.css">-->

	<script>
		var open = '<?=$_GET['open']?>';
		var $selected;
		$(function() {
			var alterClass = function() {
				var ww = document.body.clientWidth;
				if (ww < 400) {
				$('.news-table').addClass('table-responsive');
				} else if (ww >= 401) {
				$('.news-table').removeClass('table-responsive');
				};
			};

			$(window).resize(function(){
				alterClass();
			});

			alterClass();

			$(document).on('click','.question' ,function(e) {
                var table = "<?=$bo_table_java?>";

				$selected = $(this).next();
				if($(this).hasClass('qa-open')){// 닫기
					$(this).removeClass('qa-open');
					 $selected.css('height','0px');
				}else{ // 열기
					$(this).addClass('qa-open');
                   //0px $(this).find('.views').text(Number($(this).find('.views').text()) + 1);

					$.get( g5_url + "/util/news_read.php", {
						bo_table : table,
						no : $(this).attr('no')
					}, function(data) {
                        //$('#notReadCnt').text(data.not_read_cnt);

						$selected.find('p.writing').html(data.writing);
						$selected.find('p.files').empty();
						$selected.find('div.images').empty();

						$.each(data.file_list, function( index, obj ) {
							if(obj.filename != ''){
								if(obj.bf_type == 0){
									var btn = $('<a>');
									btn.attr('href','/bbs/download.php?bo_table='+ table +'&wr_id=' + obj.wr_id + '&no=' + obj.bf_no);
									btn.html(obj.filename);
									$selected.find('p.files').append("<span class='font_red' style='font-weight:600'>Download : </span>").append(btn).append('<br>');
								}else {
									// console.log(obj)
									// var img = $('<img>');

									// img.attr('src','<?=G5_DATA_URL?>/file/'+table+'/' + obj.bf_file);
									// $selected.find('div.images').append(img).append('<br>');
                                }
							}
						});

						$selected.css('height', '100%');
					},'json');
				}
			});

			if(open) {
				$('.question').eq(0).trigger('click');
			}
		});
	</script>
<main>
		<script>
			$(document).ready(function() {
				var this_url = location.href;
				var str_arr = this_url.split('&');
				var result_url = this_url.replace(str_arr[0]+"&"," ");
			});
		</script>
    <div class='container nomargin nopadding'>
		<div class="news_wrap content-box6">
			<h3 class="title">공지사항</h3>
			<p class="sub_title"><?=$config['cf_title']?>에서 전하는 새로운 소식을 확인하세요.</p>
				<?if($cnt == 0){?>
					<div class="no_data box_on">공지사항이 존재하지 않습니다</div>
				<?}?>
			<?for($i; $row = sql_fetch_array($list); $i++){?>
			<div class="col-sm-12 col-12 news">
				<ul class="row question" no="<?echo $row['wr_id']?>">
					<li class="left_wrap">
						<div class="tit">새소식</div>
						<div class="date"><?echo date("Y-m-d", strtotime($row['wr_last']))?></div>
					</li>
					<li class="mid_wrap col"><?echo $row['wr_subject']?></li>
					<li class="right_wrap">
						<i class="ri-arrow-down-s-line"></i>
					</li>
				</ul>
				<div class="answer">
					<p class="writing"></p>
				</div>
			</div>
			<?}?>
		</div>
	</div>
    <div class="gnb_dim"></div>
</main>
</script>
<script>
	$(function(){
		$(".top_title h3").html("<span >공지사항</span>");
	});
</script>
<? include_once(G5_THEME_PATH.'/_include/tail.php'); ?>
