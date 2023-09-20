<?
	include_once('./_common.php');
	include_once(G5_THEME_PATH.'/_include/wallet.php');
	include_once(G5_THEME_PATH.'/_include/gnb.php');
	include_once(G5_PATH.'/util/package.php');
	include_once(G5_LIB_PATH.'/fcm_push/set_fcm_token.php');

	login_check($member['mb_id']);
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
                    <button type='button' class='btn wd main_btn b_sub' onclick="go_to_url('mywallet');"
                        data-i18n="dashboard.내 지갑"> MY WALLET</button>
                </div>
                <div class='col-lg-6 col-12'>
                    <button type='button' class='btn wd main_btn b_main' onclick="go_to_url('upstairs');"
                        data-i18n="dashboard.패키지구매">패키지구매</button>
                </div>
                <!-- <div class='col-lg-12 col-12'>
							<button type='button' class='btn wd main_btn b_third' onclick="move_to_shop()" >쇼핑몰바로가기</button>
						</div> -->
            </div>
        </div>


        <div style="clear:both;"></div>

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
<style>
    .color0 {
        background: #cacaca !important
    }

    .color1 {
        background: #b55dccd9 !important
    }

    .color2 {
        background: #516feb !important;
    }

    .color3 {
        background: #5ec1df !important;
    }

    .color4 {
        background: #5ed2dc !important;
    }

    .color5 {
        background: #373cbc !important;
    }

    .color6 {
        background: #2b3a6d !important;
    }

    .color9 {
        background: #6214ab !important;
    }

    .color10 {
        background: #6214ab !important;
    }

    #myChart{
        width: 100%;
        margin: 0 auto;
    }

    #myChart1{
        width: 100%;
        margin: 0 auto;
    }

    .apexcharts-legend{
        text-align:left !important;
    }
</style>


        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        <div class='r_card_wrap content-box round mt30'>      
        
            <div style='max-width:400px;width:100%;text-align:center;margin:0 auto;'>
                <div id="myChart"></div>
            </div>
            
        </div>

        <script>
        var options = {
          series: [100, 47, 51, 75, 40],
          chart: {
          height: 390,
          type: 'radialBar',
        },
        plotOptions: {
          radialBar: {
            offsetY: 0,
            startAngle: 0,
            endAngle: 270,
            hollow: {
              margin: 5,
              size: '30%',
              background: 'transparent',
              image: undefined,
            },
            dataLabels: {
              name: {
                show: false,
              },
              value: {
                show: false,
              }
            }
          }
        },
        colors: ['#ff4500', '#ef21fd', '#6f00ff', '#0260b9','#008000'],
        labels: ['Mega', 'Zeta', 'ZetaPlus', 'Super','My Mining'],
        legend: {
          show: true,
          floating: true,
          fontSize: '14px',
          position: 'left',
          offsetX: 0,
          offsetY: 5,
          labels: {
            useSeriesColors: true,
          },
          markers: {
            size: 0
          },
          formatter: function(seriesName, opts) {
            return seriesName + ":  " + opts.w.globals.series[opts.seriesIndex]+"%"
          },
          itemMargin: {
            vertical: 3
          }
        },
        responsive: [{
          breakpoint: 480,
          options: {
            legend: {
                show: true
            }
          }
        }]
        };

        var chart = new ApexCharts(document.querySelector("#myChart"), options);
        chart.render();

        </script>


        <div class='r_card_wrap content-box round mt30'>      
            
            <div style='max-width:400px;width:100%;text-align:center;margin:0 auto;'>
                <div id="myChart1"></div>
            </div>
            
        </div>

        <script>
                 
        var options = {
          series: [100, 47, 51, 75, 40],
          chart: {
            height: '600px',
            type: 'polarArea',
        },
        colors: ['#ff4500', '#ef21fd', '#6f00ff', '#0260b9','#008000'],
        labels: ['Mega', 'Zeta', 'ZetaPlus', 'Super','My'],
        stroke: {
          colors: ['#fff']
        },
        fill: {
          opacity: 0.8
        },
        legend: {
          show: true,
          floating: false,
          fontSize: '14px',
          position: 'bottom',
          offsetX: 0,
          offsetY: 8,
          labels: {
            useSeriesColors: true,
          },
          markers: {
            size: 0
          },
          formatter: function(seriesName, opts) {
            return seriesName + ":  " + opts.w.globals.series[opts.seriesIndex]+"%"
          },
          itemMargin: {
            vertical: 3
          }
        },
        responsive: [{
          breakpoint: 480,
          options: {
            chart: {
              width: 360
            },
            legend: {
              position: 'bottom'
            }
          }
        }]
        };

        var chart = new ApexCharts(document.querySelector("#myChart1"), options);
        chart.render();
        </script>

<div class='r_card_wrap content-box round mt30'>      
            
            <div style='max-width:400px;width:100%;text-align:center;margin:0 auto;'>
                <div id="myChart2"></div>
            </div>
            
        </div>

        <script>
                 
        var options = {
          series: [100, 47, 51, 75, 40],
        // series: [{
        //     data: [{
        //         x: 'Apple',
        //         y: 54
        //     }, {
        //         x: 'Orange',
        //         y: 66
        //     }],
        // }],

        chart: {
            height: '600px',
            type: 'donut',
        },
        colors: ['#ff4500', '#ef21fd', '#6f00ff', '#0260b9','#008000'],
        labels: ['Mega', 'Zeta', 'ZetaPlus', 'Super','My'],
        
        plotOptions: {
          pie: {
            // size: ['65%','50%','30%','100','50'],
            startAngle: 0,
            endAngle: 360,
            expandOnClick: true,
            offsetX: 0,
            offsetY: 0,
            customScale: 0.8,
            dataLabels: {
                offset: 0,
                minAngleToShowLabel: 10
            },
            
            donut: {
                size: '60%',
                background: 'transparent',
                labels: {
                    show: true,
                    name: {
                    show: true,
                    fontSize: '24px',
                    fontFamily: 'Helvetica, Arial, sans-serif',
                    fontWeight: 600,
                    color: '#3b86ff',
                    offsetY: -10,
                        formatter: function (val) {
                            return val;
                        }
                    },
                    value: {
                    show: true,
                    fontSize: '16px',
                    fontFamily: 'Helvetica, Arial, sans-serif',
                    fontWeight: 400,
                    color: '#000',
                    offsetY: 16,
                        formatter: function (val) {
                            return val;
                        }
                    },
                    total: {
                    show: true,
                    showAlways: false,
                    label: 'Total',
                    fontSize: '22px',
                    fontFamily: 'Helvetica, Arial, sans-serif',
                    fontWeight: 600,
                    color: '#3b86ff',
                        formatter: function (w) {
                            return w.globals.seriesTotals.reduce((a, b) => {
                            return a + b
                            }, 0)
                        }
                    }
                }
            } // donut end
          }
        },

        fill: {
          opacity: 0.8
        },
        legend: {
          show: true,
          floating: false,
          fontSize: '14px',
          position: 'bottom',
          offsetX: 0,
          offsetY: 8,
          labels: {
            useSeriesColors: true,
          },
          markers: {
            size: 0
          },
          formatter: function(seriesName, opts) {
            return seriesName + ":  " + opts.w.globals.series[opts.seriesIndex]+"%"
          },
          itemMargin: {
            vertical: 3
          }
        },
        responsive: [{
          breakpoint: 480,
          options: {
            chart: {
              width: 360
            },
            legend: {
              position: 'bottom'
            }
          }
        }]
        };

        var chart = new ApexCharts(document.querySelector("#myChart2"), options);
        chart.render();
        </script>

<style>
    #container {
    height: 400px;
    }

    .highcharts-figure,
    .highcharts-data-table table {
    max-width: 400px;
    margin: 1em auto;
    
    }

    .highcharts-data-table table {
    font-family: Verdana, sans-serif;
    border-collapse: collapse;
    border: 1px solid #ebebeb;
    margin: 10px auto;
    text-align: center;
    width: 100%;
    max-width: 400px;
    }

    .highcharts-data-table caption {
    padding: 1em 0;
    font-size: 1.2em;
    color: #555;
    }

    .highcharts-data-table th {
    font-weight: 600;
    padding: 0.5em;
    }

    .highcharts-data-table td,
    .highcharts-data-table th,
    .highcharts-data-table caption {
    padding: 0.5em;
    }

    .highcharts-data-table thead tr,
    .highcharts-data-table tr:nth-child(even) {
    background: #f8f8f8;
    }

    .highcharts-data-table tr:hover {
    background: #f1f7ff;
    }
</style>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/variable-pie.js"></script>

        <div class='r_card_wrap content-box round mt30' style='padding-left:0;padding-right:0;'>      
        
            <div style='width:100%;text-align:center;margin:0 auto;'>

            <figure class="highcharts-figure">
                <div id="container"></div>
                <p class="highcharts-description">
                </p>
            </figure>
            
            </div>

        </div>

                <script>
                Highcharts.chart('container', {
                chart: {
                    type: 'variablepie',
                    pane: {
                        backgroundColor: '#000',
                        borderWidth :1,
                        borderColor : '#000'
                    }
                },
                title: {
                    text: ' ZETABYTE MY MINING STATUS ',
					show : true
                },
                tooltip: {
                    headerFormat: '',
                    pointFormat: '<span style="color:{point.color}">\u25CF</span> <b> {point.name}</b><br/>' +
                    'Hash Power (hp/s): <b>{point.y}</b><br/>' +
                    'Total bonus : <b>{point.z}</b><br/>'
                },
                
                series: [
                    {
                    minPointSize: 10,
                    innerSize: '30%',
                    zMin: 0,
                    name: 'countries',
                    data: [{
                    name: 'Mega',
                    y: 300,
                    x: 0,
                    z: 100,
					color: '#ff4500'
                    }, {
                    name: 'Zeta',
                    y: 300,
                    x: 0,
                    z: 47,
					color: '#ef21fd'
                    }, {
                    name: 'ZetaPlus',
                    y: 300,
                    x: 0,
                    z: 51,
					color: '#6f00ff'
                    }, {
                    name: 'Super',
                    y: 100,
                    x: 0,
                    z: 75,
					color: '#0260b9'
                    }, {
                    name: 'Mining',
                    y: 100,
                    x: 0,
                    z: 40,
					color: '#008000'
                    }]
                }
                ]
                });
                </script>
            




        <div class='r_card_wrap content-box round history_latest mt30'>
            <div class="card_title">최근 발생 보너스 <a href='<?=G5_URL?>/page.php?id=bonus_history'
                    class='f_right inline more'><span>더보기<i class="ri-add-circle-fill"></i></span></a></div>
            <?
					$bonus_history_sql	 = "SELECT * from `{$g5['bonus']}` WHERE mb_id = '{$member['mb_id']}' order by day desc limit 0,5";
					$bonus_history_result = sql_query($bonus_history_sql);
					$bonus_history_cnt = sql_num_rows($bonus_history_result);
					if($bonus_history_cnt > 0){
						while($row = sql_fetch_array($bonus_history_result)){
					?>

            <div class="line row">
                <div class='col-8'>
                    <span class='day'><?=timeshift($row['day'])?> </span>
                    <span class='category'><?=strtoupper($row['allowance_name'].' Bonus')?> </span>
                </div>
                <div class='col-4 text-right'>
                    <span class='price'><?=Number_format($row['benefit'])?> <?=$curencys[1]?> </span>
                </div>
            </div>

            <?}?>
            <?}else{
						echo "<div class='no_data'>보너스 내역이 존재하지 않습니다</div>";
					}?>
        </div>

        <div class='r_card_wrap content-box round regist_latest'>
            <div class="card_title">최근 추천 등록 회원 <a href='<?=G5_URL?>/page.php?id=structure'
                    class='f_right inline more'><span>더보기<i class="ri-add-circle-fill"></i></span></a></div>
            <?
					$bonus_history_sql	 = "SELECT * from `{$g5['member_table']}` WHERE mb_recommend = '{$member['mb_id']}' order by mb_open_date desc limit 0,3";
					$bonus_history_result = sql_query($bonus_history_sql);
					$bonus_history_cnt = sql_num_rows($bonus_history_result);
					if($bonus_history_cnt > 0){
						while($row = sql_fetch_array($bonus_history_result)){
					?>

            <div class="line row">
                <div class='col-8'>
                    <span class='badge'><?=$member_level_array[$row['mb_level']]?> </span>
                    <span class='badge b_skyblue'><?=$row['grade'].' star'?> </span>
                    <span class='id'><?=$row['mb_id']?> </span>

                </div>
                <div class='col-4 text-right'>
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
<script>
$(function() {
    // $(".top_title h3").html("<span >대시보드</span>");

    var img_src_up = "<?php echo G5_THEME_URL?>/img/arrow_up.png";
    $('.collap p ').css('display', 'none');
    $('.updown').attr('src', img_src_up);
    $('.fold_img_wrap img').css('vertical-align', 'baseline');

    $('.total_view_top').addClass('show');
});
</script>

<? include_once(G5_THEME_PATH.'/_include/tail.php'); ?>