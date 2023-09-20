<?
include_once('./_common.php');
include_once(G5_THEME_PATH . '/_include/wallet.php');
include_once(G5_THEME_PATH . '/_include/gnb.php');
include_once(G5_PLUGIN_PATH.'/Encrypt/rule.php');

login_check($member['mb_id']);

$title = '마이닝';

$ordered_items = ordered_items($member['mb_id'], $_GET['item']);
$mining_cnt = count($ordered_items);


// 내 마이닝상품 리스트 기본값 1
$list_cnt  = 1;
if ($_GET['myitem'] == 'all') {
    $list_cnt = $mining_cnt;
}

// 마이닝출금 설정값
$withdrawal_mining = wallet_config('withdrawal_mining');
$fee = $withdrawal_mining['fee'];
$max_limit = $withdrawal_mining['amt_maximum'];
$min_limit = $withdrawal_mining['amt_minimum'];
$day_limit = $withdrawal_mining['day_limit'];
if($mining_total > 0){
    $max_mining_total = $mining_total;
}else{
    $max_mining_total = 0;
}

$recent_day = date("Y-m-d", strtotime(date("Y-m-d")."-3 month"));

/* 리스트 기본값*/
$mining_history_limit1 = " AND DAY IN (SELECT MAX(DAY) FROM soodang_mining WHERE mb_id ='{$member['mb_id']}')";
$mining_history_limit2 = " AND DAY IN (SELECT MAX(DAY) FROM soodang_mining WHERE mb_id ='{$member['mb_id']}') GROUP BY DAY";
$mining_history_limit_text = '+더보기(최근3개월)';

$mining_amt_limit = "limit 0,1 ";
$mining_amt_limit_text = '전체 내역보기';

if ($_GET['history_limit'] == 'all') {
    $mining_history_limit1 = " AND DAY > '{$recent_day}' ";
    $mining_history_order = "GROUP BY DAY ORDER BY day desc ";
    $mining_history_limit_text = "최근내역만보기";
}

if ($_GET['amt_limit'] == 'all') {
    $mining_amt_limit = "";
    $mining_history_order = "";
    $mining_amt_limit_text = "최근내역만보기";
}

// 마이닝 내역
// $mining_history_sql = "SELECT * from {$g5['mining']} WHERE mb_id = '{$member['mb_id']}'  {$mining_history_limit} GROUP BY allowance_name ";
$mining_history_sql = "SELECT *
    FROM soodang_mining
    WHERE mb_id = '{$member['mb_id']}' AND allowance_name != 'super_mining' {$mining_history_limit1} UNION
    SELECT NO, DAY,allowance_name,mb_id, SUM(mining) AS mining,currency,rate,rec,rec_adm, DATETIME,HASH,overcharge
    FROM soodang_mining
    WHERE mb_id = '{$member['mb_id']}' AND allowance_name = 'super_mining' {$mining_history_limit1} {$mining_history_order}
    ";

// print_R($mining_history_sql);

$mining_history = sql_query($mining_history_sql);
$mining_history_cnt = sql_num_rows($mining_history);

// 마이닝 출금 내역
$mining_amt_log = sql_query("SELECT * from {$g5['withdrawal']} WHERE mb_id = '{$member['mb_id']}' AND coin != '{$minings[0]}' ORDER BY uid desc ");
$mining_amt_cnt = sql_num_rows($mining_amt_log);

// 마이닝 출금 승인 내역 
$mining_amt_auth_log = sql_query("SELECT * from {$g5['withdrawal']} WHERE mb_id = '{$member['mb_id']}' AND coin = '{$minings[$now_mining_coin]}' AND status = 1 ");
$mining_amt_auth_cnt = sql_num_rows($mining_amt_auth_log);

//kyc인증
$kyc_cert = $member['kyc_cert'];

function category_badge($val)
{
    if ($val == 'mining') {
        return "<span class='badge b_green'>" . strtoupper($val) . "</span>";
    } else if ($val == 'mega_mining') {
        return "<span class='badge b_orange'>" . strtoupper($val) . "</span>";
    } else if ($val == 'zeta_mining') {
        return "<span class='badge b_pink'>" . strtoupper($val) . "</span>";
    } else if ($val == 'zetaplus_mining') {
        return "<span class='badge b_purple'>" . strtoupper($val) . "</span>";
    } else if ($val == 'super_mining') {
        return "<span class='badge b_blue'>" . strtoupper($val) . "</span>";
    }else if ($val == 'coin swap'){
        return "<span class='badge b_yellow'>" . strtoupper($val) . "</span>";
    }
}


function overcharge($val, $category)
{
    global $member;
    $target = $category;
    if ($category == 'super_mining') {
        $rate = 1;
    } else {
        $rate = 3;
    }

    if ((100 * $rate) <= remain_hash($member[$target], $rate, false)) {
        return "<span class='red'>" . $val . "</sapn>";
    } else {
        return $val;
    }
}


// 업비트 시세
$coin_array = [];
$coin_list_query = sql_query("SELECT * from coins WHERE open = 1 ORDER BY no");

$coin_cnt = sql_num_rows($coin_list_query);

while($row=sql_fetch_array($coin_list_query)){
    array_push($coin_array,[$row['no'],$row['id'],$row['name'],$row['krname']]);
}

if(strpos($member['withdraw_wallet'],'0x')){
    $with_addr = $member['withdraw_wallet'];
}else{
    $with_addr = Decrypt($member['withdraw_wallet'],$member['mb_id'],'x');
}
?>

<? include_once(G5_THEME_PATH . '/_include/breadcrumb.php'); ?>
<!-- <link href="<?= G5_THEME_URL ?>/css/scss/page/mining.css" rel="stylesheet"> -->
<style>
    .coin_icon{background:white;border-radius: 50%;padding:5px;}
    input[type='text'].modal_input {
        background: #ededed;
        margin-top: 10px;
        box-shadow: inset 1px 1px 1px rgb(0 0 0 / 50%);
        border: 0;
        text-align: center;
        width: 50%;
    }

    .time_remained {
        display: block;
        text-align: center
    }

    .processcode {
        color: red;
        display: block;
        text-align: center;
        font-size: 13px;
    }

    .red {
        color: red;
    }

    .result span {
        text-align: right
    }

    .color-up{color:#c84a31}
    .color-down{color:#1261c4}
    .color-even{color:black}

    .modal#SwapCoinModal{top:15%;z-index:1041;}
    .modal#SwapCoinModal .modal-close{}

    #SwapCoinModal .line_card {
        display: flex;
        height: 40px;
        line-height: 40px;
        height:60px;
        display: flex;
        flex-direction: row;
        align-items: left;
        justify-content: left;
        padding: 10px 16px 10px 16px;
        border-radius: 0.5rem;
        border: 2px solid #b5bfd9;
        background-color: #fff;
        transition: 0.15s ease;
        cursor: pointer;
        position: relative;
        font-family: "Inter", sans-serif;
        color: black;
        font-weight: 600;
    }

    #SwapCoinModal .line_card .checkbox-icon img {
        height: 100%;
        width: 100%;
    }

    #SwapCoinModal .prices {
        text-align: right;
        flex: auto;
        font-size: 18px;
    }

    #SwapCoinModal .checkbox-label,
    #SwapCoinModal .checkbox-labelkr {
        margin-left: 5px;
    }

    #SwapCoinModal .checkbox-labelkr {
    }

    .changer_ly {
        margin: 10px auto;
        text-align: center;
    }

    .modal-icon {
        font-size: 32px;
        color: #1261c4;
        vertical-align: baseline;
    }

    #SwapCoinModal #result_curency {
        margin-top: 30px;
        border: 2px solid #2260ff;
        height:70px;
    }

    #result_curency .prices {
        text-align: center !important;
        width:45%;
        font-size:0.8rem;
        line-height:20px;
        color:#777;
    }

    #result_currecy .prices.result-price {
        color: #12c438
    }

    #result_curency .fund{
        display:block;
        color:black;
        font-size:1rem;
        padding-bottom:3px;
        /* border-bottom:1px solid #5c5c5c; */
    }

    .desc {
        margin: 15px auto;
        padding: 0;
        text-align: center;
        line-height: 20px;
        width:100%;
        letter-spacing: 0;
    }

    .modal-close{    
        box-sizing: content-box;
        width: 1em;
        height: 1em;
        padding: 0.25em 0.25em;
        color: #000;
        background: transparent url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23000'%3e%3cpath d='M.293.293a1 1 0 011.414 0L8 6.586 14.293.293a1 1 0 111.414 1.414L9.414 8l6.293 6.293a1 1 0 01-1.414 1.414L8 9.414l-6.293 6.293a1 1 0 01-1.414-1.414L6.586 8 .293 1.707a1 1 0 010-1.414z'/%3e%3c/svg%3e") center/1em auto no-repeat;
        border: 0;
        border-radius: 0.25rem;
        opacity: .5;
    }
    .modal-btn{width:100%;margin:0;height:40px;}
    .btn-secondary{background:#f1f1f1;color:black}
    .modal-btn:hover{}
    #mining_history .badge.b_yellow{background:#FECE00;color:black}
    .upbit_logo{width:65px;display: inline-block;margin-left:10px;}
    .mymining_total{line-height:24px;}
    .mymining_total .before_fund{margin:0;padding:0;line-height:20px;font-size:0.8em;color:#566aad}
    .dark .mymining_total .before_fund{color:#999894}
    .dark .color-even{color:rgba(255, 255, 255, 0.692);}

    .refresh_btn{flex:auto;text-align:right;line-height:24px;}
    #coin_refresh{line-height:30px;}
    .dark #coin_refresh {color: #333;}
    .dark #coin_refresh:hover i{color:#FECE00}
</style>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap">
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
<!-- <link rel="stylesheet" href="<?= G5_ADMIN_URL ?>/css/modal.css"> -->
<main>
    <div id="mining" class="container mining">

        <section id='mymining' class="mt10">
            <h3 class="title">내 마이닝 상품 (<?= $mining_cnt ?>)</h3>
            <form name="myitemform" method="get" action="<?= G5_URL ?>/page.php?id=mining" style="display:contents;">
                <input type="hidden" name="id" value='mining' />
                <input type="hidden" id="myitem" name="myitem" value="<? if ($list_cnt == 1) {echo 'all';} else {echo '';} ?>" />
                <? if ($mining_cnt > 1) { ?><input type="submit" class="btn all_view" value="<?if($list_cnt==1){echo'전체보기';}else{echo'접어두기';}?>"></input><?}?>
            </form>

            <div class="mining_wrap">
                <?
                if ($mining_cnt < 1) {
                    echo "<div class='no_data box_on'>내 보유 상품이 존재하지 않습니다</div>";
                } else {

                    for ($i = 0; $i < $list_cnt; $i++) {
                        $color_num = substr($ordered_items[$i]['it_maker'], 1, 1);
                ?>
                        <div class="product_buy_wrap round r_card r_card_<?= $color_num ?> col-12">
                            <li class="row">
                                <p class="title col-12" style="font-size:14px;">

                                    <? if ($color_num > 0) {
                                        echo $ordered_items[$i]['it_option_subject'] . " -";
                                    } ?>
                                    <?= $ordered_items[$i]['it_name'] ?></span>
                                </p>

                                <p class="num col-12" style="font-size:12px;padding-left:0;padding-right:15px;line-height:12px;">No. <?= $ordered_items[$i]['row']['od_id'] ?></p>
                            </li>
                            <div class="b_line4"></div>
                            <li class="row">
                                <div class="value col-10">
                                    <div class="value1" style="font-size:18px;line-height:42px;">
                                        <?= $ordered_items[$i]['pv'] ?> mh/s
                                    </div>
                                    <div class="date"><?= $ordered_items[$i]['row']['cdate'] ?> ~ <?= expire_date($ordered_items[$i]['row']['cdate']) ?></div>
                                </div>
                                <div class="col-2" style='padding:8px 0px 0px;text-align:center'>
                                    <div class='mining_ico'><img src="https://static.upbit.com/logos/<?=strtoupper($ordered_items[$i]['it_brand'])?>.png" class='coin_icon'/></div>
                                </div>
                            </li>
                        </div>
                        <?php
                        // echo "<script>slide_color('$color_num')</script>";
                        ?>
                <?php }
                } ?>
            </div>
        </section>

        <div class="b_line5 mt10 mt10"></div>


        <h3 class="title" style="margin:30px 0 0;display:flex;line-height:44px;">주요 암호화폐 시세 By 
            <div class='upbit_logo'><img src="<?=G5_THEME_URL?>/img/icon_bi_upbit.svg"></div>
            <div class="refresh_btn">
                <a id="coin_refresh" class="btn inline"><i class="ri-restart-fill" style="font-size:28px;"></i></a>
            </div>
        </h3>
        <fieldset class="checkbox-group mt10" id='coin_dashboard'></fieldset>

        <?if($member['swaped'] == 0){?>
        <section id='mining_swap' class='col-12 content-box round mt20'>
            <div class="pop_btn" id="coin_swap">
                <div class="btn_title"><span class="wallet_title">마이닝 코인 스왑</span></div>
                <div class="max_with">
                    [ <?= $before_mining_total ?> <?= strtoupper($minings[$before_mining_coin]) ?> 스왑가능 ]
                </div>
                <div class="caret_box"><span class="btn inline"><i class="ri-share-forward-fill"></i></span></div>
            </div>
        </section>
        <?}?>

        

        <section id='mining_withdraw' class='col-12 content-box round mt40'>

            <div class="polding_btn">
                <div class="btn_title"><span class="wallet_title">마이닝 출금</span></div>
                <div class="max_with">[ <?= $max_mining_total ?> <?= strtoupper($minings[$now_mining_coin]) ?> 출금가능 ]</div>
                <div class="caret_box"><span class="btn inline"><i class="ri-arrow-down-s-line"></i></span></div>
            </div>

            <div class="polding mt10 hidden">

                <div class="input_address">
                    <label class="sub_title">- 출금주소</label><span class='comment'><?=strtoupper($minings[$now_mining_coin])?>출금 가능 주소를 입력해주세요.</span>
                    <input type="text" id="withdrawal-address" class="send_coin b_ghostwhite f_small" placeholder="<?= strtoupper($minings[$now_mining_coin]) ?> 출금 주소를 입력해주세요" value=<? if ($member['withdraw_wallet'] != '0') {
                                                                                                                                                                                        echo $with_addr;
                                                                                                                                                                                    } ?>>
                </div>

                <div class="input_shift_value">
                    <label class="sub_title">- 출금금액 (<?= $minings[$now_mining_coin] ?>)</label>
                    <div style='display:inline-block; float:right;'><button type='button' id='max_value' class='btn inline' value=''>max</button></div>

                    <div class='mt10'>
                        <input type="text" id="sendValue" class="send_coin b_ghostwhite" placeholder="출금 금액을 입력해주세요">
                        <label class='currency-right'><?= $minings[$now_mining_coin] ?></label>
                    </div>

                    <div class="row fee hidden" style='width:initial'>
                        <div class="col-5" style="text-align:left">
                            <i class="ri-exchange-fill"></i>
                            <span id="active_amt">0</span>
                        </div>

                        <div class="col-7" style="text-align:right">
                            <label class="fees">- 수수료(<?= $fee ?>%) :</label>
                            <i class="ri-coins-line"></i>
                            <span id="fee_val">0</span>
                        </div>
                    </div>
                </div>

                <div class="b_line5"></div>
                <div class="otp-auth-code-container mt20">
                    <div class="verifyContainerOTP">
                        <label class="sub_title">- 출금 비밀번호</label>
                        <input type="password" id="pin_auth_with" class="b_ghostwhite" name="pin_auth_code" maxlength="6" placeholder="6 자리 핀코드를 입력해주세요">

                    </div>
                </div>

                <div class="send-button-container row">
                    <div class="col-5">
                        <button id="pin_open" class="btn wd yellow form-send-button">인증</button>
                    </div>
                    <div class="col-7">
                        <button type="button" class="btn wd btn_wd form-send-button" id="withdrawal_btn" data-toggle="modal" data-target="" disabled>출금신청</button>
                    </div>
                </div>
            </div>
        </section>
        <section id='mining_history'>
            <div class="history_box content-box mt20">
                <div>
                    <h3 class="hist_tit title">내 마이닝 내역 <span class='mymining_total'><?= shift_coin($mining_acc) ?> <?= strtoupper($minings[$now_mining_coin]) ?></span></h3>

                    <? if ($mining_history_cnt < 1) { ?>
                        <ul class="row">
                            <li class="no_data">내 마이닝 내역이 존재하지 않습니다</li>
                        </ul>
                    <? } else { ?>
                        <? while ($row = sql_fetch_array($mining_history)) { ?>
                            <? if($row['no'] != ''){?>
                            <ul class="row">
                                <li class="col-3 hist_date"><?= $row['day'] ?></li>
                                <li class="col-5 hist_td"><?= category_badge($row['allowance_name']) ?>
                                    <? if ($row['allowance_name'] == 'super_mining') {
                                        echo "<a href='/dialog.php?id=mining_detail&day={$row['day']}' class='btn more_record' style='margin:0' data-day='" . $row['day'] . "'>
                                    <i class='ri-menu-add-line'></i></a>";
                                    } ?>
                                </li>
                                <li class="col-4 hist_value">
                                    <?= overcharge(shift_coin($row['mining']), $row['allowance_name']) ?> <?= strtoupper($row['currency']) ?></li>
                                <li class="col-12 hist_rec">
                                    <?
                                    if ($row['allowance_name'] != 'super_mining') {
                                        echo $row['rec'];
                                        if ($row['allowance_name'] == 'coin swap') {
                                            echo "<br>Swap rate : [ 1 ".strtoupper($minings[$before_mining_coin])." / ".$row['rate']." ".strtoupper($row['currency'])."]";
                                        }
                                    } else {
                                        echo "click more btn";
                                    }
                                    ?>
                                </li>
                            </ul>
                        <? }} ?>

                        <div><button type='button' id="mining_history_more" class="btn wd"><?= $mining_history_limit_text ?></button></div>

                    <? } ?>
                </div>

                <? if ($mining_amt_cnt > 0) { ?>
                    <div class="b_line6"></div>
                    <div id='mining_amt_log' class='mt20'>

                        <h3 class="hist_tit">마이닝 출금 내역 
                            <span class='mymining_total'> <?= shift_coin($mining_amt) ?> <?= strtoupper($minings[$now_mining_coin]) ?>
                            <?if($member['swaped'] > 0 && $member[$before_mining_amt_target] > 0){
                                echo "<br><span class='before_fund'>".calculate_math($member[$before_mining_amt_target],8).' '.strtoupper($minings[$before_mining_coin])."</span>";
                            }?>
                            </span>
                        </h3>
                        <? while ($row = sql_fetch_array($mining_amt_log)) { 
                            if($row['create_dt'] > '2023-01-09'){
                                $with_addr = Decrypt($row['addr'], $secret_key, $secret_iv);
                            }else{
                                $with_addr = $row['addr'];
                            }
                            
                            ?>
                            <ul class='row'>
                                <li class="col-12">
                                    <span class="col-8 nopadding"><i class="ri-calendar-check-fill"></i><?= $row['create_dt'] ?></span>
                                    <span class="col-4 nopadding text-right amt_total"><?= shift_coin($row['amt_total']) ?> <?= $row['coin'] ?></span>
                                </li>

                                <li class="col-12">
                                    <span class="col-8 nopadding amt"><i class="ri-coins-line"></i>수수료 : - <?= shift_coin($row['fee'])  ?> <?= $row['coin'] ?>
                                    </span>
                                    <span class="col-4 nopadding text-right amt"><i class="ri-refund-2-line"></i><?= shift_coin($row['out_amt']) ?> <?= $row['coin'] ?></span>
                                </li>

                                <li class="col-12 "><span class='hist_bank'><i class="ri-wallet-2-fill"></i><?= $with_addr ?></span></li>

                                <li class="col-12 mt10">
                                    <span class="col-6 nopadding amt"><i class="ri-survey-line"></i>처리결과</span>
                                    <span class="col-6 nopadding text-right result "><? string_shift_code($row['status']) ?></span>
                                </li>
                            </ul>
                        <? } ?>
                        <div><button type='button' id="mining_amt_more" class="btn wd"><?= $mining_amt_limit_text ?></button></div>
                    </div>
                <? } ?>

            </div>
        </section>
    </div> <!-- mining end-->
</main>
<div class="gnb_dim"></div>

<!-- 코인시세카드 -->
<section id='card_template' style='display: none'>
	<div class="checkbox">
		<label class="checkbox-wrapper" title="">
			<input type="checkbox" class="checkbox-input" data-id='' />
			<span class="checkbox-tile">
				<span class="checkbox-icon">
					<img src="https://static.upbit.com/logos/BTC.png"/>
				</span>
                <span class="checkbox-labelkr">ETH</span>
                <span class="checkbox-price prices">
                    <p class='price'>0</p>
                    <p class="change_price">0</p>
                </span>
			</span>
		</label>
	</div>
</section>

<!-- 스왑모달 -->
<div class="modal fade" id="SwapCoinModal" tabindex="-1" role="dialog" aria-hidden="true" style='_display:inherit;'>
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">마이닝 잔고 스왑(SWAP)</h3>
                <button type="button" class="btn-close close modal-close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body">

                <div class='line_card item' id='origin_curency'>
                    <span class="checkbox-icon"><img src="https://static.upbit.com/logos/<?= strtoupper($minings[$now_mining_coin]) ?>.png"></span>
                    <span class="checkbox-label"></span>
                    <span class="checkbox-labelkr"></span>
                    <span class="checkbox-label2 prices color-down" data-val=""></span>
                </div>

                <div class='changer_ly'><i class="ri-swap-fill modal-icon"></i></div>

                <div class='line_card item' id='select_curency'>
                    <span class="checkbox-icon"><img src="" /></span>
                    <span class="checkbox-label"></span>
                    <span class="checkbox-labelkr"></span>
                    <span class="checkbox-label2 prices color-down" data-val=""></span>
                </div>

                <div class='line_card' id='result_curency'>
                    <span class="checkbox-label2 prices shift_price" data-val=""></span>
                    <span class="changer_ly2"><i class="ri-arrow-right-circle-line modal-icon"></i></span>
                    <span class="checkbox-label2 prices result-price" data-val=""></span>
                </div>

               <!--  <form name="swap_form" id="swap_form" action="./coins_proc.php" method="post">
                    <input type='hidden' name='func' value='swap' />
                    <input type='hidden' name='origin_coin_id' value='' />
                    <input type='hidden' name='origin_coin_price' value='' />
                    <input type='hidden' name='change_coin_id' value='' />
                    <input type='hidden' name='change_coin_price' value='' />
                    <input type='hidden' name='change_val' value='' />
                </form> -->

                <div class='desc'></div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary modal-btn" data-dismiss="modal">취소</button>
                <button type="button" class="btn modal-btn " id="swap_exc" disabled>스왑(변경) 실행</button>
            </div>
        </div>
    </div>
</div>
</div>

<script>
    $(function() {
        $(".top_title h3").html("<span >마이닝</span>")
    });

    $(function() {

        $('.polding_btn').click(function() {
            /* var out_count = Number("<?= $mining_amt_cnt ?>");
            if(out_count < 1){
                dialogModal('KYC 인증', "<strong> 안전한 출금을 위해 최초 1회  KYC 인증을 진행해주세요<br><a href='/page.php?id=profile' class='btn btn-primary'>KYC인증</a></strong>", 'warning');
            } */
            var target = $(this).parent();
            target.find('.polding').slideToggle(300);
            target.toggleClass('open');

            if (target.hasClass('open')) {
                $(this).find('.caret_box i').attr('class', 'ri-arrow-up-s-line');
            } else {
                $(this).find('.caret_box i').attr('class', 'ri-arrow-down-s-line');
            }
        });


        $('#mining_history_more').on('click', function() {
            locationURL('history_limit', 'mining_history');
        });

        $('#mining_amt_more').on('click', function() {
            locationURL('amt_limit', 'mining_amt_log');
        });

        function locationURL(keyword, anchor = '') {
            var url = location.href.split('?')[0];
            var params = new URLSearchParams(location.search);

            if (params.get(keyword) == 'all') {
                params.set(keyword, '0');
            } else {
                params.set(keyword, 'all');
            }
            var queryString = params.toString();

            console.log(url + '?' + queryString);
            window.location.href = url + '?' + queryString + '#' + anchor;
        }

        onlyNumber('pin_auth_with');

        /* 출금*/
        var WITHDRAW_CURENCY = '<?= $minings[$now_mining_coin] ?>';
        var COIN_NUMBER_POINT = '<?= COIN_NUMBER_POINT ?>';

        var mb_block = Number("<?= $member['mb_block'] ?>"); // 차단
        var mb_id = '<?= $member['mb_id'] ?>';
        var fee_total = fee_calc = coin_amt = 0;

        // 출금설정
        var nw_with = '<?= $nw_with ?>'; // 출금서비스 가능여부
        var personal_with = '<?= $member['mb_leave_date'] ?>'; // 별도구분회원 여부

        var fee = (<?= $fee ?> * 0.01);
        var min_limit = '<?= $min_limit ?>';
        var max_limit = '<?= $max_limit ?>';
        var day_limit = '<?= $day_limit ?>';

        // 문자인증
        var time_reamin = false;
        var is_sms_submitted = false;
        var check_pin = false;
        var process_step = false;

        var mb_hp = '<?= $member['mb_hp'] ?>';

        function input_timer(time, where) {
            var time = time;
            var min = '';
            var serc = '';

            var x = setInterval(function() {
                min = parseInt(time / 50);
                sec = time % 60;

                $(where).html(min + "분 " + sec + "초");
                time--;

                if (time < 0) {
                    clearInterval(x);
                    $(where).html("시간초과");
                    time_reamin = false;
                }
            }, 1000)
        }

        function check_auth_mobile(val) {
            $.ajax({
                type: "POST",
                url: "./util/check_auth_sms.php",
                dataType: "json",
                cache: false,
                async: false,
                data: {
                    pin: val,
                },
                success: function(res) {
                    if (res.result == "success") {
                        check_pin = true;
                    } else {
                        check_pin = false;
                    }
                }
            });
        }


        // 최대출금가능금액
        var mb_max_limit = '<?= $max_mining_total ?>';

        // 스왑가능금액 
        var before_mining_total = '<?=$before_mining_total?>';
        // console.log(` min_limit : ${min_limit}\n max_limit:${max_limit}\n day_limit:${day_limit}\n fee: ${fee}`);

        function withdraw_value(num) {
            return Number(num).toFixed(COIN_NUMBER_POINT);
        }

        function withdraw_curency(num) {
            return Number(num) + ' ' + WITHDRAW_CURENCY;
        }

        // 출금금액 변경 
        function input_change() {
            // var inputValue = $('#sendValue').val().replace(/,/g, '');
            var inputValue = $('#sendValue').val();


            if (Number(inputValue) > Number(mb_max_limit)) {
                dialogModal('출금가능 금액확인', '<strong> 출금가능금액내에서 정수 단위로 입력해주세요 </strong>', 'warning');
            }

            fee_total = withdraw_value(inputValue * fee);
            fee_calc = Number(inputValue) - Number(fee_total);
            coin_amt = withdraw_value(fee_calc);

            // console.log(`fee : ${fee_total}\nfee_calc : ${fee_calc}\n실제출금계산액 :: ${fee_calc}`);

            $('.fee').css('display', 'flex');
            $('#fee_val').text(withdraw_curency(fee_total));
            $('#active_amt').text(withdraw_curency(coin_amt));
        }

        $('#sendValue').change(input_change);

        // 출금가능 맥스
        $('#max_value').on('click', function() {
            $("#sendValue").val(mb_max_limit.toLocaleString('ko-KR'));
            input_change();
        });


        /*핀 입력*/
        $('#pin_open').on('click', function(e) {

            // 회원가입시 핀입력안한경우
            if ("<?= $member['reg_tr_password'] ?>" == "") {
                dialogModal('출금 비밀번호(핀코드) 인증', '<p>출금 비밀번호(핀코드) 등록해주세요.</p>', 'warning');
                $('#modal_return_url').click(function() {
                    location.href = "./page.php?id=profile";
                })
                return;
            }

            if ($('#pin_auth_with').val() == "") {
                dialogModal('출금 비밀번호(핀코드) 인증', '<p>출금 비밀번호(핀코드) 입력해주세요.</p>', 'warning');
                return;
            }

            $.ajax({
                url: './util/pin_number_check_proc.php',
                type: 'POST',
                cache: false,
                async: false,
                data: {
                    "mb_id": mb_id,
                    "pin": $('#pin_auth_with').val()
                },
                dataType: 'json',
                success: function(result) {
                    if (result.response == "OK") {
                        dialogModal('출금 비밀번호(핀코드) 인증', '<p>출금 비밀번호가 인증되었습니다.</p>', 'success');
                        $('#withdrawal_btn').attr('disabled', false);
                        $('#pin_open').attr('disabled', true);
                        $("#pin_auth_with").attr("readonly", true);
                    } else {
                        dialogModal('출금 비밀번호(핀코드) 인증', '<p>출금 비밀번호가 일치 하지 않습니다.</p>', 'failed');
                    }
                },
                error: function(e) {
                    //console.log(e);
                }
            });
        });


        // 업비트코인시세
        var coin_list = <?=json_encode($coin_array)?>;
        var coin_name = '';
        var coinkr_name = '';
        var origin_curency = "<?= strtoupper($minings[1]) ?>";
        var select_curency = "<?= strtoupper($minings[2]) ?>";
        
        coin_currency_lnt(coin_list);

        $("#coin_refresh").on('click',function(){
            // $("#coin_dashboard").children().remove();
            var Theme = getCookie('mode');

            $("#coin_dashboard").prepend("<div class='refresh_loader'><img src='"+g5_theme_url+"/img/loader_294_"+Theme+".svg'></div>");
            setTimeout(coin_currency_lnt, 2000, coin_list);
        });

        function coin_currency_lnt(coin_list){
            $("#coin_dashboard").children().remove();
            if(coin_list.length > 0){
                // console.log(coin_list);

                $.each (coin_list, function (index, el) {

                    var upbit_api_url = 'https://api.upbit.com/v1/ticker?markets=KRW-' + el[1];
                    var upbit_img_src = "https://static.upbit.com/logos/" + el[1] + '.png';

                    $.ajax({
                        type: "GET",
                        url: upbit_api_url,
                        data: {
                        },
                        cache: false,
                        async: false,
                        dataType: 'json',
                        
                        success: function(res) {
                            if(res){
                                var temp_clone = $('#card_template').clone();
                                var temp_html = $(temp_clone.html());

                                // 템플릿 수정
                                temp_html.find('.checkbox-wrapper').attr('title',el[2]);
                                temp_html.find('.checkbox-wrapper').attr('id',el[1]);
                                
                                temp_html.find('.checkbox-input').attr('data-id',el[1]);
                                if(index == 1){
                                    
                                    temp_html.find('.checkbox-tile').addClass('active');
                                    temp_html.find('.checkbox-input').attr('checked',true);
                                }else if (index ==0){
                                    temp_html.find('.checkbox-input').attr('disabled',true);
                                }
                                temp_html.find('.checkbox-input').val(el[0]);
                                temp_html.find('img').attr('src',upbit_img_src);
                                // temp_html.find('.checkbox-label').html(res[0].market);
                                temp_html.find('.checkbox-labelkr').html(el[3]);

                                if(res[0].change.toLowerCase()  =='rise'){
                                    var colorstring = "color-up";
                                    var express = "<i class='ri-arrow-up-s-fill'></i>";
                                }else if (res[0].change.toLowerCase() == 'even'){
                                    var colorstring = "color-even";
                                    var express = "<i class='ri-subtract-line'></i>";
                                }else{
                                    var colorstring = "color-down";
                                    var express = "<i class='ri-arrow-down-s-fill'></i>";
                                }
                                
                                temp_html.find('.prices').addClass(colorstring);
                                temp_html.find('.prices > .price').attr('data-val',res[0].trade_price).html('￦' +Price(res[0].trade_price));
                                temp_html.find('.prices > .change_price').html(express + Price(res[0].change_price));

                                temp_clone.html(temp_html.prop('outerHTML'));
                                $('#coin_dashboard').append(temp_clone.html());
                            }
                        },error: function(e) {
                            alert(e);
                        }
                    });
                });
            }
        }


        $('#withdrawal_btn').on('click', function() {

            var inputVal = $('#sendValue').val().replace(/,/g, '');

            // 모바일 등록 여부 확인
            if (mb_hp == '' || mb_hp.length < 10) {
                dialogModal('정보수정', '<strong> 안전한 출금을 위해 인증가능한 모바일 번호를 등록해주세요.</strong>', 'warning');

                $('.closed').on('click', function() {
                    location.href = '/page.php?id=profile';
                })
                return false;
            }

            //KYC 인증
            var out_count = Number("<?= $mining_amt_auth_cnt ?>");
            var kyc_cert = Number("<?= $kyc_cert ?>");

            if (out_count < 1 && kyc_cert != 1) {
                dialogModal('KYC 인증 미등록/미승인 ', "<strong> KYC인증이 미등록 또는 미승인 상태입니다.<br>안전한 출금을 위해 최초 1회 KYC 인증을 진행해주세요<br><a href='/page.php?id=profile' class='btn btn-primary'>KYC인증</a></strong>", 'warning');
                return false;
            }


            // 출금주소 입력확인
            if ($('#withdrawal-address').val() == "") {
                dialogModal('출금주소확인', '<strong> 올바른 출금 주소를 입력해주세요.</strong>', 'warning');
                return false;
            }

            // 출금서비스 이용가능 여부 확인
            if (nw_with == 'N') {
                dialogModal('서비스이용제한', '<strong>현재 출금가능한 시간이 아닙니다.</strong>', 'warning');
                return false;
            }

            if (personal_with != '') {
                dialogModal('서비스이용제한', '<strong>관리자에게 연락주세요</strong>', 'warning');
                return false;
            }

            // 금액 입력 없을때 
            if (inputVal == '' || inputVal <= 0) {
                dialogModal('금액 입력 확인', '<strong>출금 금액을 확인해주세요.</strong>', 'warning');
                return false;
            }

            // 최소 금액 확인
            if (min_limit != 0 && inputVal < Number(min_limit)) {
                dialogModal('금액 입력 확인', '<strong> 최소가능금액은 ' + min_limit + WITHDRAW_CURENCY + ' 입니다.</strong>', 'warning');
                return false;
            }

            //최대 금액 확인
            if (max_limit != 0 && inputVal > Number(max_limit)) {
                dialogModal('금액 입력 확인', '<strong> 최대가능금액은 ' + max_limit + WITHDRAW_CURENCY + ' 입니다.</strong>', 'warning');
                return false;
            }

            // console.log(`input :${inputVal}`);
            // console.log(`max :${mb_max_limit}`);
            
            // 보유한도체크
            if (Number(inputVal) > Number(mb_max_limit)) {
                dialogModal('금액 입력 확인', '<strong>출금 가능 금액을 확인해주세요.</strong>', 'warning');
                return false;
            }

            process_pin_mobile().then(function() {
                $.ajax({
                    type: "POST",
                    url: "./util/withdrawal_coin_proc.php",
                    cache: false,
                    async: false,
                    dataType: "json",
                    data: {
                        mb_id: mb_id,
                        wallet_addr: $('#withdrawal-address').val(),
                        func: 'mining-withdraw',
                        amt: inputVal,
                        fee: fee_total,
                        coin_amt: coin_amt,
                        coin_cost: $('#' + select_curency + ' .checkbox-price .price').data('val'),
                        select_coin: WITHDRAW_CURENCY
                    },
                    success: function(res) {
                        if (res.result == "success") {
                            dialogModal('출금신청이 정상적으로 처리되었습니다.', '<p>실제 출금까지 24시간 이상 소요될수있습니다.</p>', 'success');

                            $('.closed').click(function() {
                                location.reload();
                            });
                        } else {
                            dialogModal('출금 신청 실패!', "<p>" + res.sql + "</p>", 'warning');
                        }
                    }
                });
            });

        });



        function process_pin_mobile() {

            return new Promise(
                function(resolve, reject) {
                    dialogModal('본인인증', "<p>" + maskingFunc.phone(mb_hp) + "<br>모바일로 전송된 인증코드 6자리를 입력해주세요<br><input type='text' class='modal_input' id='auth_mobile_pin' name='auth_mobile_pin'></input><span class='time_remained'></span><span class='processcode'></span></p>", 'confirm');

                    if (is_sms_submitted == false) {
                        is_sms_submitted = true;

                        $.ajax({
                            type: "POST",
                            url: "./util/send_auth_sms.php",
                            cache: false,
                            async: false,
                            dataType: "json",
                            data: {
                                mb_id: mb_id,
                            },
                            success: function(res) {
                                if (res.result == "success") {
                                    time_reamin = true;
                                    input_timer(res.time, '.time_remained');

                                    $('#modal_confirm').on('click', function() {

                                        if (!time_reamin) {
                                            is_sms_submitted = false;
                                            alert("시간초과로 다시 시도해주세요");
                                        } else {
                                            var input_pin_val = $("#auth_mobile_pin").val();
                                            check_auth_mobile(input_pin_val);

                                            if (!check_pin) {
                                                $(".processcode").html("인증코드가 일치하지 않습니다.");
                                                return false;
                                            } else {
                                                is_sms_submitted = false;
                                                process_step = true;
                                                resolve();
                                            }

                                        }
                                    });

                                    $('#dialogModal .cancle').on('click', function() {
                                        is_sms_submitted = false;
                                        location.reload();
                                    });

                                }
                            }
                        });

                    } else {
                        alert('잠시 후 다시 시도해주세요.');
                    }
                });
        }

        

        //버튼4 : 코인스왑
        $('#coin_swap').on('click', function() {
            $('#SwapCoinModal').modal("show");

            var exc_array = [
                ['origin_curency', origin_curency],
                ['select_curency', select_curency]
            ];
            var exc_price_array = [$('#' + origin_curency + ' .checkbox-price .price').data('val'), $('#' + select_curency + ' .checkbox-price .price').data('val')];

            for (var i = 0; i < exc_array.length; i++) {
                var target_contents = $('#' + exc_array[i][0]);
                var upbit_api_url = 'https://api.upbit.com/v1/ticker?markets=KRW-' + exc_array[i][0];
                var upbit_img_src = "https://static.upbit.com/logos/" + exc_array[i][1] + '.png';

                target_contents.find('.checkbox-icon img').attr('src', upbit_img_src);  
                target_contents.find('.checkbox-label').html(" " + exc_array[i][1]);
                target_contents.find('.checkbox-labelkr').html($('#' + exc_array[i][1] + ' .checkbox-labelkr').html());

                var coin_price_now = exc_price_array[i];
                target_contents.find('.checkbox-label2').attr('data-val', coin_price_now).html('￦' + Price(coin_price_now));
            }

            const result_val = calculate_math(Number(exc_price_array[0] / exc_price_array[1]), 4); // 소수점 2자리

            $('#result_curency .shift_price').attr('data-val',1).html("<span class='fund'>" +  before_mining_total + " " + exc_array[0][1]+ "</span>" + "(1 " + exc_array[0][1]+")" );
            $('#result_curency .result-price').attr('data-val', result_val).html("<span class='fund'>" + calculate_math( before_mining_total*result_val,4)+ " " + exc_array[1][1] +"</span>(" + Price(result_val) + ' ' + exc_array[1][1]+")");

            
            if (origin_curency != select_curency && result_val != 1 &&  before_mining_total > 0.001) {
                var result_msg = "<strong>주의!</strong><br>내 잔고(<strong>" + origin_curency + "</strong>)를 해당 코인(<strong>" + select_curency + "</strong>) 으로 변환하시겠습니까?<br> 스왑은 1회만 가능하며, 실행 이후에는 되돌릴수 없습니다."
                
                $('#SwapCoinModal #swap_exc').addClass('btn-primary').attr('disabled', false);
            }else{
                var result_msg = "<span class='red'>현재 스왑 가능한 잔고가 없거나 서비스 이용이 불가능합니다.</span>"
            }

            $('.desc').html(result_msg);
            /* $('#swap_form').find("input[name='origin_coin_id']").val(origin_curency);
            $('#swap_form').find("input[name='origin_coin_price']").val(exc_price_array[0]);
            $('#swap_form').find("input[name='change_coin_id']").val(select_curency);
            $('#swap_form').find("input[name='change_coin_price']").val(exc_price_array[1]);
            $('#swap_form').find("input[name='change_val']").val(result_val); */


            $("#SwapCoinModal #swap_exc").off('click').on('click', function() {
                if (!confirm("자산(마이닝코인) 잔고를 스왑(swap) 하시겠습니까?")) {
                    return false;
                } else {
                    $("#SwapCoinModal #swap_exc").attr('disabled', true);
                    $("#SwapCoinModal").modal('hide');

                    $.ajax({
                    type: "POST",
                    url: "<?=G5_URL?>/util/coinswap_proc.php",
                    data: {
                        func : 'swap',
                        origin_coin_id : origin_curency,
                        origin_coin_price : exc_price_array[0],
                        change_coin_id : select_curency,
                        change_coin_price :exc_price_array[1],
                        change_val : result_val
                    },
                    cache: false,
                    async : false,
                    dataType: 'json',
                    success: function(res) {
                        if(res.result == 'success') {
                            
                            dialogModal('코인SWAP',"내 자산(마이닝 코인)이 정상적으로 스왑 처리되었습니다.",'success');
                            $("#modal_return_url").on('click', function() {
                                location.reload(); 
                            });
                        }else{
                            dialogModal('코인스왑처리 에러',res.sql,'failed');
                            $("#modal_return_back").on('click', function() {
                                location.reload(); 
                            });
                            
                        }
                    }, 
                    error: function(e) {
                        alert(e);
                    }
                    });
                }
            });

        });

    });
</script>

<? include_once(G5_THEME_PATH . '/_include/tail.php'); ?>