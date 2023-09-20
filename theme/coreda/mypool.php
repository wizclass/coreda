<?
include_once('./_common.php');
include_once(G5_THEME_PATH . '/_include/wallet.php');
include_once(G5_THEME_PATH . '/_include/gnb.php');
login_check($member['mb_id']);

$title = 'My Pool';
$chart_express_cnt = 5;

if ($_GET['stage']) {
    $stage = $_GET['stage'];
} else {
    $stage = 'mega';
}


if (!isset($history_limit)) {
    $history_limit = $chart_express_cnt;
}

$chart_data = [];
$pool_data_sql = "SELECT * FROM g5_member_info WHERE mb_id = '{$member['mb_id']}' order by date desc limit 0,{$chart_express_cnt}";
$pool_data = sql_query($pool_data_sql);
while ($row = sql_fetch_array($pool_data)) {
    array_push($chart_data, $row);
}


$mining_data = [];
$mining_data_sql = sql_query("SELECT day,mining,rate FROM soodang_mining WHERE mb_id = '{$member['mb_id']}' AND allowance_name ='mining' ORDER by day desc LIMIT 0,{$chart_express_cnt}");
while ($row = sql_fetch_array($mining_data_sql)) {
    array_push($mining_data, $row);
}

$mega_data = [];
$mega_mining_sql = sql_query("SELECT day,mining,rate,rec_adm FROM soodang_mining WHERE mb_id = '{$member['mb_id']}' AND allowance_name ='mega_mining' ORDER by day desc LIMIT 0,{$chart_express_cnt}");
while ($row = sql_fetch_array($mega_mining_sql)) {
    array_push($mega_data, $row);
}

$zeta_data = [];
$zeta_mining_sql = sql_query("SELECT day,mining,rate,rec_adm FROM soodang_mining WHERE mb_id = '{$member['mb_id']}' AND allowance_name ='zeta_mining' ORDER by day desc LIMIT 0,{$chart_express_cnt}");
while ($row = sql_fetch_array($zeta_mining_sql)) {
    array_push($zeta_data, $row);
}

$zetaplus_data = [];
$zetaplus_mining_sql = sql_query("SELECT day,mining,rate,rec_adm FROM soodang_mining WHERE mb_id = '{$member['mb_id']}' AND allowance_name ='zetaplus_mining' ORDER by day desc LIMIT 0,{$chart_express_cnt}");
while ($row = sql_fetch_array($zetaplus_mining_sql)) {
    array_push($zetaplus_data, $row);
}

$super_data = [];
$super_mining_sql = sql_query("SELECT day,sum(mining)as mining,rate,rec_adm FROM soodang_mining WHERE mb_id = '{$member['mb_id']}' AND allowance_name ='super_mining' Group by day ORDER by day desc LIMIT 0,{$chart_express_cnt}");
while ($row = sql_fetch_array($super_mining_sql)) {
    array_push($super_data, $row);
}


function round_number($value)
{
    return Number_format(Round($value));
}
?>

<? include_once(G5_THEME_PATH . '/_include/breadcrumb.php'); ?>

<?
function remain_bonus($value, $rate)
{
    global $member;

    $limit = $member['mb_rate'] * $rate;
    $remain_bonus = Number_format($value / $limit);
    
    return $remain_bonus;
    
}

$bonus_data = [remain_bonus($member['recom_mining'], 3), remain_bonus($member['brecom_mining'], 3), remain_bonus($member['brecom2_mining'], 3), remain_bonus($member['super_mining'], 1), '100'];
?>

<link rel="stylesheet" href="<?= G5_THEME_URL ?>/css/default.css">
<script src="<?= G5_URL ?>/js/common.js"></script>
<style>
.sparkboxes .box h5{margin:10px 0; font-size:14px;}
/* 2022-06-09 테마 버튼 추가 */
.dark .ad_btn{
    background:rgba(0,0,0,0.3);
    border:1px solid rgba(0,0,0,0.5);
}
.ad_btn{
    background:rgba(0,0,0,0.3);
    border:1px solid rgba(0,0,0,0.5);
}

</style>

<link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,700" rel="stylesheet" />

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<link href="<?= G5_THEME_URL ?>/assets/chart.css" rel="stylesheet">

<main>
    <div class='container mypool'>

        <div class="row sparkboxes mt-5">
            <div class="col-md-12">
                <div class="box box5">

                    <div class="details" style='transform:scale(0.9) translate(-14px, 10px)'>
                        <a href="#myminings" class="card_btn">
                            <h2><?= round(($total_hash / 100), 2) ?></h2>
                            <h4>Total Mining <br></h4>
                        </a>
                    </div>

                    <div id="spark5"></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="box box1">
                    <div class="details">
                        <a href="#megaminings" class="card_btn">
                            <h2><?= round(($member['recom_mining'] / 100), 2) ?></h2>
                            <h4>MegaMining</h4>
                        </a>
                    </div>
                    <div id="spark1"></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="box box2">
                    <div class="details">
                        <a href="#zetaminings" class="card_btn">
                            <h2><?= round(($member['brecom_mining'] / 100), 2) ?></h2>
                            <h4>ZetaMining</h4>
                        </a>
                    </div>
                    <div id="spark2"></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="box box3">
                    <div class="details">
                        <a href="#zetaplusminings" class="card_btn">
                            <h2><?= round(($member['brecom2_mining'] / 100), 2) ?></h2>
                            <h4>Zeta Plus</h4>
                        </a>
                    </div>
                    <div id="spark3"></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="box box4">
                    <div class="details">
                        <a href="#superminings" class="card_btn">
                            <h2><?= round(($member['super_mining'] / 100), 2) ?></h2>
                            <h4>SuperMining</h4>
                        </a>
                    </div>
                    <div id="spark4"></div>
                </div>
            </div>
        </div>

        <div class='container'>
            <div class="row half">
                <div class='r_card_wrap content-box round mt30 col-lg-6'>
                    <p class='title'> 총 마이닝 보너스 구성 및 상한 </p>
                    <div style='max-width:400px;width:100%;text-align:center;margin:0 auto;'>
                        <div id="circleChart"></div>
                    </div>
                </div>

                <div class='r_card_wrap content-box round col-lg-6' id="myminings" style="margin-top: 30px;">
                    <p class='title'> 데일리 마이닝 보너스</p>
                    <div style='max-width:400px;width:100%;text-align:center;margin:0 auto;'>
                        <div id="mychart"></div>
                    </div>
                </div>
            </div>
        </div>


        <div class='r_card_wrap content-box round mt30' id="minings">

            <div class='nav_set'>
                <ul>
                    <li class='nav mega active' data-id="mega">메가</li>
                    <li class='nav zeta' data-id="zeta">제타</li>
                    <li class='nav zetaplus' data-id="zetaplus">제타플러스</li>
                    <li class='nav super' data-id="super">슈퍼</li>
                    <li class='nav my' data-id="my">마이닝</li>
                </ul>
            </div>


            <!-- 메가마이닝 -->
            <div class="nav_con" id="container_mega" data-id="mega">

                <div class="box-header">

                    <div class="block row sparkboxes">
                        <?
                        $recom_data = json_decode($chart_data[0]['recom_info'], true);
                        $recom_cnt = $recom_data['cnt'];
                        $recom_sales_10 = $recom_data['sales_10'];
                        $recom_sales_3 = $recom_data['sales_3'];

                        ?>
                        <div class="col-12 mb20">
                            <div class="box">
                                <ul>
                                    <li class='col-4'>
                                        <h3><?= round_number($member['recom_mining']) ?></h3>
                                        <h6>메가풀 해시 </h6>
                                    </li>
                                    <li class='col-4'>
                                        <h3><?= round_number($member['mb_rate'] * 300) ?></h3>
                                        <h6>목표해시 </h6>
                                    </li>
                                    <li class='col-4'>
                                        <h3><?= $recom_cnt ?></h3>
                                        <h6>참여인원 </h6>
                                    </li>
                                </ul>
                                <div id="megaspark1" class="sparkline"></div>
                            </div>
                        </div>

                        <div id="megachart" class="mt30" style="width:100%;margin:0 15px; "></div>

                        <div class="col-6">
                            <div class="box  white">
                                <h3><?= round_number($recom_sales_10) ?></h3>
                                <h6>10대 매출 </h6>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="box  white">
                                <h3><?= round_number($recom_sales_3) ?></h3>
                                <h6>승급대상포인트(3대)</h6>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="box-body mt20"></div>

                <div><button type="button" class="btn wd more_btn mt20">더보기</button></div>

            </div>




            <!-- 제타마이닝 -->
            <div class="nav_con" id="container_zeta" data-id="zeta">
                <div class="box-header">
                    <div class="block row sparkboxes">
                        <?
                        $brecom_data = json_decode($chart_data[0]['brecom_info'], true);
                        $brecom_cnt = $brecom_data['cnt'];
                        $brecom_L = $brecom_data['LEFT'];
                        $brecom_R = $brecom_data['RIGHT'];
                        ?>
                        <div class="col-12 mb20">
                            <div class="box">
                                <ul>
                                    <li class='col-4'>
                                        <h3><?= round_number($member['brecom_mining']) ?></h3>
                                        <h6>제타풀 해시 </h6>
                                    </li>
                                    <li class='col-4'>
                                        <h3><?= round_number($member['mb_rate'] * 300) ?></h3>
                                        <h6>목표해시 </h6>
                                    </li>
                                    <li class='col-4'>
                                        <h3><?= $brecom_cnt ?></h3>
                                        <h6>참여인원 </h6>
                                    </li>
                                </ul>
                                <div id="zetaspark1" class="sparkline"></div>
                            </div>
                        </div>

                        <div id="zetachart" class="mt30" style="width:100%;margin:0 15px; "></div>


                        <div class="col-6">
                            <div class="box  white">
                                <h2>LEFT </h2>
                                <h3 class='mt10'><?= round_number($brecom_L['sales']) ?></h3>
                                <h6>매출</h6>
                                <h3 class='mt10'><?= round_number($brecom_L['hash']) ?></h3>
                                <h6>해시 </h6>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="box  white">
                                <h2>RIGHT </h2>
                                <h3 class='mt10'><?= round_number($brecom_R['sales']) ?></h3>
                                <h6>매출</h6>
                                <h3 class='mt10'><?= round_number($brecom_R['hash']) ?></h3>
                                <h6>해시 </h6>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="box-body mt20"></div>

                <div><button type="button" class="btn wd more_btn mt20">더보기</button></div>
            </div>



            <!-- 제타+마이닝 -->
            <div class="nav_con" id="container_zetaplus" data-id="zetaplus">
                <div class="box-header">
                    <div class="block row sparkboxes">
                        <?
                        $brecom2_data = json_decode($chart_data[0]['brecom2_info'], true);
                        $brecom2_cnt = $brecom2_data['cnt'];
                        $brecom2_L = $brecom2_data['LEFT'];
                        $brecom2_R = $brecom2_data['RIGHT'];
                        ?>
                        <div class="col-12 mb20">
                            <div class="box">
                                <ul>
                                    <li class='col-4'>
                                        <h3><?= round_number($member['brecom2_mining']) ?></h3>
                                        <h6>제타+ 해시 </h6>
                                    </li>
                                    <li class='col-4'>
                                        <h3><?= round_number($member['mb_rate'] * 300) ?></h3>
                                        <h6>목표해시 </h6>
                                    </li>
                                    <li class='col-4'>
                                        <h3><?= $brecom2_cnt ?></h3>
                                        <h6>참여인원 </h6>
                                    </li>
                                </ul>
                                <div id="zetaplusspark1" class="sparkline"></div>
                            </div>
                        </div>

                        <div id="zetapluschart" class="mt30" style="width:100%;margin:0 15px; "></div>


                        <div class="col-6">
                            <div class="box  white">
                                <h2>LEFT </h2>
                                <h3 class='mt10'><?= round_number($brecom2_L['sales']) ?></h3>
                                <h6>매출</h6>
                                <h3 class='mt10'><?= round_number($brecom2_L['hash']) ?></h3>
                                <h6>해시 </h6>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="box  white">
                                <h2>RIGHT </h2>
                                <h3 class='mt10'><?= round_number($brecom2_R['sales']) ?></h3>
                                <h6>매출</h6>
                                <h3 class='mt10'><?= round_number($brecom2_R['hash']) ?></h3>
                                <h6>해시 </h6>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="box-body mt20"></div>

                <div><button type="button" class="btn wd more_btn mt20">더보기</button></div>
            </div>

            <!-- 슈퍼 마이닝 -->
            <div class="nav_con" id="container_super" data-id="super">
                <div class="box-header">
                    <div class="block row sparkboxes">
                        <?
                        $super_info = sql_fetch("SELECT day,COUNT(NO) AS cnt FROM soodang_mining WHERE mb_id = '{$member['mb_id']}' and allowance_name='super_mining' GROUP BY DAY ORDER BY DAY DESC LIMIT 0,1");

                        ?>
                        <div class="col-12 mb20">
                            <div class="box">
                                <ul>
                                    <li class='col-4'>
                                        <h3><?= round_number($member['super_mining']) ?></h3>
                                        <h6>슈퍼 해시 </h6>
                                    </li>
                                    <li class='col-4'>
                                        <h3><?= round_number($member['mb_rate'] * 100) ?></h3>
                                        <h6>목표해시 </h6>
                                    </li>
                                    <li class='col-4'>
                                        <h3><?= $super_info['cnt'] ?></h3>
                                        <h6>참여인원 </h6>
                                    </li>
                                </ul>
                                <div id="superspark1" class="sparkline"></div>
                            </div>
                        </div>

                        <div id="superchart" class="mt30" style="width:100%;margin:0 15px; "></div>


                        <div class="col-12">
                            <div class="box  white">
                                <h3>보너스 달성률</h3>
                                <? if (remain_hash($member['super_mining'], 1, false) >= 100) { ?>
                                    <h2>" <span class='font_red'><?= remain_hash($member['super_mining'], 1, false) ?>%</span> 초과달성 "</h2>
                                    
                                    <h5> 보유해시량을 늘리면 일 / <span class="point"> <?= Number_format((($member['super_mining'] - ($member['mb_rate'] * 100)) * $day_mint_value * 0.01), 8) ?> <?=strtoupper($minings[$now_mining_coin])?> </span> 만큼 마이닝 보너스를 매일매일 더 받을수 있어요</h5>
                                    <a href="<?=G5_URL?>/page.php?id=upstairs" class='btn inline ad_btn'>
                                    <!-- <i class="ri-add-fill" style='font-size:15px;'></i> -->
                                    마이닝 해시 추가 구매</a>
                                <? } else { ?>
                                    <h3>" <?= (100 - remain_hash($member['super_mining'], 1, false)) ?> % 미달 "</h3>
                                <? } ?>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="box-body mt20"></div>

                <div><button type="button" class="btn wd more_btn mt20">더보기</button></div>
            </div>

            <!-- 마이 마이닝 -->
            <div class="nav_con" id="container_my" data-id="my">
                <div class="box-header">
                    <div class="block row sparkboxes">
                        <?
                        // $super_info = sql_fetch("SELECT day,COUNT(NO) AS cnt FROM soodang_mining WHERE mb_id = '{$member['mb_id']}' and allowance_name='super_mining' GROUP BY DAY ORDER BY DAY DESC LIMIT 0,1");
                        ?>
                        <div class="col-12 mb20">
                            <div class="box">
                                <!-- <ul>
                                    <li class='col-4'>
                                        <h3><?= round_number($member['my_mining']) ?></h3>
                                        <h6>슈퍼 해시 </h6>
                                    </li>
                                    <li  class='col-4'>
                                        <h3><?= round_number($member['mb_rate'] * 100) ?></h3>
                                        <h6>목표해시 </h6>
                                    </li>
                                    <li  class='col-4'>
                                        <h3><?= $super_info['cnt'] ?></h3>    
                                        <h6>참여인원 </h6>
                                    </li>
                                </ul> -->
                                <div id="myspark1" class="sparkline"></div>
                            </div>
                        </div>


                        <!-- <div class="col-6">
                            <div class="box  white" >
                                <h2>LEFT </h2>
                                <h3 class='mt10'><?= round_number($brecom2_L['sales']) ?></h3>
                                <h6>매출</h6>
                                <h3 class='mt10'><?= round_number($brecom2_L['hash']) ?></h3>
                                <h6>해시 </h6>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="box  white" >
                                <h2>RIGHT </h2>
                                <h3 class='mt10'><?= round_number($brecom2_R['sales']) ?></h3>
                                <h6>매출</h6>
                                <h3 class='mt10'><?= round_number($brecom2_R['hash']) ?></h3>
                                <h6>해시 </h6>
                            </div>
                        </div> -->
                    </div>
                </div>

                <div class="box-body mt20"></div>

                <div><button type="button" class="btn wd more_btn mt20">더보기</button></div>
            </div>
        </div>

</main>


<div class="gnb_dim"></div>
</section>


<script>
    //     var loading = $('<div id="pre_loading" class="pre_loading"><img id="loading_img" src="'+g5_theme_url+'/img/loader_808.svg" /></div>');

    // loading.appendTo(document.body);

    var chartData = <?= json_encode($chart_data) ?>;
    var bonusData = <?= json_encode($bonus_data) ?>;

    var miningData = <?= json_encode($mining_data) ?>;
    var megaData = <?= json_encode($mega_data) ?>;
    var zetaData = <?= json_encode($zeta_data) ?>;
    var zetaplusData = <?= json_encode($zetaplus_data) ?>;
    var superData = <?= json_encode($super_data) ?>;

    // var left_brecom =  dataset(chartData,'brecom_info','LEFT','sales',0);
    // var right_brecom = dataset(chartData,'brecom_info','RIGHT','sales',0);
    // console.log(left_brecom);

    function dataset(array, key, key2 = '', key3 = '', math = 1) {
        var returnData = [];

        for (i = 0; i < array.length; i++) {
            if (key2 == '') {
                returnData.push(array[i][key]);
            } else {
                var arrayIn = JSON.parse(array[i][key]);

                if (math == 0) {
                    if (key3 == '') {
                        returnData.push((arrayIn[key2]));
                    } else {
                        returnData.push((arrayIn[key2][key3]));
                    }
                } else {
                    if (key3 == '') {
                        returnData.push((arrayIn[key2] * math).toFixed(2));
                    } else {
                        returnData.push((arrayIn[key2][key3] * math).toFixed(2));
                    }
                }
            }
        }
        return returnData.reverse();
    }

    $(function() {
        
        // loading.show();

        var stage = '<?= $stage ?>';

        var limited = <?= $history_limit ?>;
        var start = 0;
        var limit = 0;

        var megachart_exc = false;
        var zetachart_exc = false;
        var zetapluschart_exc = false;
        var superchart_exc = false;
        var mychart_exc = false;

        nav_active(stage);

        $(".nav").on('click', function() {
            var id = $(this).data("id");

            start = 0;
            limit = <?= $chart_express_cnt ?>;

            var contarget = "#container_" + stage;
            var data_target = $(contarget + " .box-body")
            $(data_target).empty();

            if (id != stage) {
                nav_active(id);
            }


        });

        $(".more_btn").on("click", function() {
            var id = $(this).parent().parent().data("id");
            limit = 10;

            if (start == 0) {
                start = 5;
            } else if (start >= 5) {
                start += 10;
            }

            console.log(`more :: start: ${start}\nend: ${limit}`);
            load_data(id, start, limit);
        });

        function nav_active(id) {
            stage = id;

            var navtarget = ".nav." + stage;
            var contarget = "#container_" + stage;

            $(".nav_con").removeClass("active");
            $(contarget).addClass("active");

            $(".nav").removeClass("active");
            $(navtarget).addClass("active");

            console.log(`start: ${start}\nend: ${limit}`);
            load_data(stage, start, limited);

        }

        function Comparison(A, B) {
            var value = Number(A - B).toFixed(8);
            var result = '';


            if (value > 0) {
                result = "<span class='comp plus'><i class='ri-arrow-up-s-fill'></i></span>";

            } else if (value < 0) {

                result = "<span class='comp minus'><i class='ri-arrow-down-s-fill'></i></span>";

            } else {
                result = "<span class='comp'><i class='ri-subtract-line'></i></span>";
            }

            return result;
        }

        function load_data(stage, start, limit) {
            // loading.show()
            var contarget = "#container_" + stage;
            var data_target = $(contarget + " .box-body");

            $.ajax({
                type: "POST",
                url: "./util/mining_data.php",
                cache: false,
                async: false,
                dataType: "json",
                data: {
                    category: stage,
                    start: start,
                    limited: limit
                },
                success: function(res) {
                    if (res.code == "0000") {
                        html = '';
                        var cnt = res.data.length;
                        for (var i = 0; i < cnt; i++) {
                            html += "<ul>";
                            html += "<li class='date'>" + res.data[i]['day'] + "</li>";
                            if (stage == 'super') {
                                html += "<li class='from_id'><i class='ri-user-line'></i>" + res.data[i]['from_id'] + "</li>";
                            }

                            html += "<li class='mining'>" + coin_val(res.data[i]['mining']) + " " + res.data[i]['currency'];

                            if (stage != 'super') {
                                html += Comparison(res.data[i]['mining'], res.data[i]['prev']);
                            }
                            html += "</li>";
                            html += "<li class='rec_adm'>" + res.data[i]['rec_adm'] + "</li>";
                            html += "</ul>";
                        }
                        
                        $(data_target).append(html);
                        
                        if (stage == 'mega') {
                            if (!megachart_exc) {
                                chart_mega_spark1.render();
                                megachart_exc = true;
                                megachart.render();
                            }
                        } else if (stage == 'zeta') {
                            if (!zetachart_exc) {
                                chart_zeta_spark1.render();
                                zetachart_exc = true;
                                zetachart.render();
                            }
                        } else if (stage == 'zetaplus') {
                            if (!zetapluschart_exc) {
                                chart_zetaplus_spark1.render();
                                zetapluschart_exc = true;
                                zetapluschart.render();
                            }
                        } else if (stage == 'super') {
                            if (!superchart_exc) {
                                chart_super_spark1.render();
                                superchart_exc = true;
                                superchart.render();
                            }
                        } else if (stage == 'my') {
                            if (!mychart_exc) {
                                chart_my_spark1.render();
                                mychart_exc = true;
                                
                            }
                        }

                        
                        
                    }
                    
                },complete: function(res){
                    console.log('done');
                }
            });

        }

        $(".details").on('click', function() {

        });

        function once(fn, context) {
            var result;

            return function() {
                if (fn) {
                    result = fn.apply(context || this, arguments);
                    fn = null;
                }
                
                return result;
                
            };
        }
    });
</script>

<script src="<?= G5_THEME_URL ?>/assets/chart_scripts.js?ver=20220609_11"></script>


<? include_once(G5_THEME_PATH . '/_include/tail.php'); ?>