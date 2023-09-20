<?
include_once('./_common.php');
include_once(G5_THEME_PATH . '/_include/wallet.php');
include_once(G5_THEME_PATH . '/_include/gnb.php');
include_once(G5_PLUGIN_PATH . '/Encrypt/rule.php');

$title = 'Mywallet';
login_check($member['mb_id']);

if ($nw['nw_with'] == 'N') {
  alert("현재 서비스를 이용할수없습니다.");
}

$coin = get_coins_price();
// print_R($coin);

$withdrawal_price = shift_coin($coin['usdt_krw'],KRW_NUMBER_POINT);
$withdrawal_price_sell = shift_coin($default['de_token_sell_price'],KRW_NUMBER_POINT);
$shift_auto_withdrawal_price = shift_auto($coin['usdt_krw'], 'krw');
$shift_auto_withdrawal_price_sell = shift_auto($default['de_token_sell_price'],'krw');

$coin_eth_usdt = $coin['eth_usdt'];
$coin_usdt_etc = $coin['usdt_etc'];


// 입금설정
$deposit_setting = wallet_config('deposit');
$deposit_fee = $deposit_setting['fee'];
// $deposit_min_limit = shift_auto($deposit_setting['amt_minimum'] / $coin['usdt_krw'], $currencys[0]);
$deposit_min_limit = $deposit_setting['amt_minimum'];
$deposit_max_limit = $deposit_setting['amt_maximum'];
$deposit_day_limit = $deposit_setting['day_limit'];

// 출금설정
$withdrwal_setting = wallet_config('withdrawal');
$withdrwal_fee = $withdrwal_setting['fee'];
$withdrwal_min_limit = $withdrwal_setting['amt_minimum'];
$withdrwal_max_limit = $withdrwal_setting['amt_maximum'];
$withdrwal_day_limit = $withdrwal_setting['day_limit'];

// $company_wallet = wallet_config('wallet_addr')['wallet_addr'];

// $wallet_addr1 = Decrypt($member['mb_wallet'], $member['mb_id'], 'x'); // erc20
// $wallet_addr2 = Decrypt($member['eth_my_wallet'], $member['mb_id'], 'x'); // eth
// $wallet_addr3 = Decrypt($member['etc_my_wallet'], $member['mb_id'], 'x'); // etc
// $wallet_addr4 = Decrypt($member['usdt_my_wallet'], $member['mb_id'], 'x'); // usdt

// 수수료제외 실제 출금가능금액
$withdrwal_total = $total_withraw;

if ($withdrwal_max_limit != 0 && ($total_withraw * $withdrwal_max_limit * 0.01) < $withdrwal_total) {
  $withdrwal_total = $total_withraw * ($withdrwal_max_limit * 0.01);
}

// 출금 정보 수정 불가 추가 
if($member['bank_name'] != '' && $member['bank_account'] != ''){
  $bank_withrawal_info = true;
}else{
  $bank_withrawal_info = false;
}

// P2P 입금계좌
$deposit_array = array_bank_account(1,'use');
$withdrawal_array = array_bank_account(2,'use');

/* 계좌정보
  $bank_setting = wallet_config('bank_account');
  $bank_name = $bank_setting['bank_name'];
  $bank_account = $bank_setting['bank_account'];
  $account_name = $bank_setting['account_name'];
*/

//시세 업데이트 시간
// $next_rate_time = next_exchange_rate_time();

//보너스/예치금 퍼센트
// $bonus_per = bonus_state($member['mb_id']);


// 패키지 선택하고 들어왔으면 입금할 가격표시
if ($_GET['sel_price']) {
  $sel_price = $_GET['sel_price'];
}


// 입금 OR 출금
if ($_GET['view'] == 'withdraw') {

  $view = 'withdraw';
  $history_target = $g5['withdrawal'];
} else {
  $view = 'deposit';
  $history_target = $g5['deposit'];
}

//kyc인증
$kyc_cert = $member['kyc_cert'];


/* 지갑 생성
  $callback = G5_URL . "/plugin/blocksdk/point-callback.php";
  $blocksdk_conf = Crypto::GetConfig();

  if(empty($member['mb_9'])==true && $blocksdk_conf['de_eth_use'] == 1){
    $address = Crypto::GetClient("eth")->createAddress([
      "name" => "member_no_".$member['mb_no']
    ]);
    
    Crypto::CreateWebHook($callback,"eth",$address['address']);
    
    // $update_sql .= empty($update_sql) ? "" : ","; 
    $update_sql = "mb_9='{$address['address']}'";
    $member['mb_9'] = $address['address'];
    
    $sql = "
    insert into 
    blocksdk_member_eth_addresses (id, address, private_key) 
    values ('{$address['id']}', '{$address['address']}','{$address['private_key']}')
    ";
    sql_fetch($sql);
  }

  if(empty($update_sql) == false){
    $sql = "UPDATE {$g5['member_table']} SET {$update_sql} WHERE mb_no={$member['mb_no']}";
    sql_query($sql);
  } 

  $wallet_sql = "SELECT private_key FROM blocksdk_member_eth_addresses WHERE address = '{$member['mb_9']}'";
  $wallet_row = sql_fetch($wallet_sql);
  $private_key = $wallet_row['private_key'];
  $mb_id = $member['mb_id'];


  if($member['eth_download'] == "0"){      
      include_once(G5_LIB_PATH."/download_key/set_private_key.php"); 
  }

  if($member['eth_download'] == "1"){
    include_once(G5_LIB_PATH."/download_key/get_private_key.php");

  }
*/





/*날짜계산*/
$qstr = "stx=" . $stx . "&fr_date=" . $fr_date . "&amp;to_date=" . $to_date;
$query_string = $qstr ? '?' . $qstr : '';

$fr_date = date("Y-m-d", strtotime(date("Y-m-d") . "-1 day"));
$to_date = date("Y-m-d", strtotime(date("Y-m-d") . "+1 day"));

$sql_search_deposit = " WHERE mb_id = '{$member['mb_id']}' ";
$sql_search_deposit .= " AND create_dt between '{$fr_date}' and '{$to_date}' ";

$rows = 15; //한페이지 목록수


//입금내역
$sql_common_deposit = "FROM {$g5['deposit']}";

$sql_deposit = " select count(*) as cnt {$sql_common_deposit} {$sql_search_deposit} ";
$row_deposit = sql_fetch($sql_deposit);

$total_count_deposit = $row_deposit['cnt'];
$total_page_deposit  = ceil($total_count_deposit / $rows);

if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지
$from_record_deposit = ($page - 1) * $rows; // 시작 열

$sql_deposit = " select * {$sql_common_deposit} {$sql_search_deposit} order by create_dt desc limit {$from_record_deposit}, {$rows} ";
$result_deposit = sql_query($sql_deposit);

//출금내역
$sql_common = "FROM {$g5['withdrawal']}";
// $sql_common ="FROM wallet_withdrawal_request";

$sql_search = " WHERE mb_id = '{$member['mb_id']}' ";
// $sql_search .= " AND create_dt between '{$fr_date}' and '{$to_date}' ";

$sql = " select count(*) as cnt {$sql_common} {$sql_search} ";
if ($debug) echo "<code>" . $sql . "</code>";

$row = sql_fetch($sql);
$total_count = $row['cnt'];
$withdrawal_count = $row['cnt'];

$total_page  = ceil($total_count / $rows);
if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지
$from_record = ($page - 1) * $rows; // 시작 열

$sql = " select * {$sql_common} {$sql_search} order by create_dt desc limit {$from_record}, {$rows} ";
$result_withdraw = sql_query($sql);

//  출금 승인 내역 
$amt_auth_log = sql_query("SELECT * from {$g5['withdrawal']} WHERE mb_id = '{$member['mb_id']}'  AND status = 1 ");
$auth_cnt = sql_num_rows($amt_auth_log);


// 코인종류 TX
function curency_txt($value, $kind = 'deposit')
{
  $result = '';

  if ($kind == 'deposit') {
    switch (strtolower($value)) {
      case 'eth':
        $result = 'Etherium (BEP-20)';
        break;
      case 'etc':
        $result = 'EtheriumClassic (ETC)';
        break;
      case 'usdt':
        $result = 'USDT (TetherUS BEP-20)';
        break;
      case 'hja':
        $result = 'Hwajo-asset (HJA BEP-20)';
        break;
    }
  } else {
    switch (strtolower($value)) {
      case 'eth':
        $result = 'Etherium (BEP-20)';
        break;
      case 'etc':
        $result = 'EtheriumClassic (ETC)';
        break;
      case 'usdt':
        $result = 'USDT (TetherUS BEP-20)';
        break;
      case 'hja':
        $result = 'Hwajo-asset (HJA BEP-20)';
        break;
    }
  }
  return $result;
}
?>


<!-- <link rel="stylesheet" href="<?= G5_THEME_CSS_URL ?>/withdrawal.css"> -->
<!-- <script type="text/javascript" src="./js/qrcode.js"></script> -->

<? include_once(G5_THEME_PATH . '/_include/breadcrumb.php'); ?>
<!-- <link rel="stylesheet" href="<?= G5_THEME_CSS_URL ?>/scss/page/<?= $_GET['id'] ?>.css"> -->

<style>
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

  #upbit_curency {
    margin-bottom: 20px;
    margin-top: -20px;
  }

  .coin_icon {
    background: white;
    border-radius: 50%;
    padding: 5px;
  }

  .upbit_logo {
    width: 65px;
    display: inline-block;
    margin-left: 10px;
  }

  .checkbox-tile {
    width: 100%
  }

  .refresh_btn {
    flex: auto;
    text-align: right;
    line-height: 24px;
  }

  #coin_refresh {
    line-height: 30px;
  }

  .checkbox-tile {
    display: block;
    text-align: center
  }

  .dark #coin_refresh:hover i {
    color: #FECE00
  }

  .dark #upbit_curency {
    color: white;
  }

  .dark .checkbox-tile {
    color: rgba(255, 255, 255, 0.75);
  }

  #curency_usdt_eth {
    display: inline;
  }

  #select_deposit_coin,
  #select_coin {
    height: 44px;
    border: 2px solid #1f9df5
  }

  .txt-box {
    font-size: 12px;
    color: red;
  }

  .dark .txt-box {
    color: #fac707;
  }

  .in_coin i {
    font-size: 16px;
    line-height:20px;
  }

  .in_coin {
    margin-top: -10px;
    font-size: 13px;
  }

  .account_select {
    margin-bottom: 10px;
  }

  .card_title {
    text-align: center;
  }

  .cabinet {
    padding: 10px 0;
    text-align: center;
    font-size: 20px;
  }
  .account_card{
    cursor: pointer;
  }

  .account_card.active {
    background: #3b86ff;
    color: white;
  }

  .account_card.active p {
    color: white;
    font-weight: bold;
  }

  .dark .account_card.active {
    background: #fac707;
  }

  .dark .account_card.active p {
    color: black;
    font-weight: bold;
  }

  .transparent {
    background: transparent !important;
    box-shadow: none;
  }

  #accountCopy {
    padding: 10px;
  }
  
  .swap_amt{font-size:16px;text-align:left;letter-spacing: -1px;}
  #active_amt{font-weight:bold}
  #active_in{font-weight:bold}
</style>

<main>
  <div class='container mywallet'>
    <div class="my_btn_wrap">
      <div class="row mywallet_btn">
        <div class='col-lg-6 col-12'>
          <button type='button' class='btn wd main_btn b_darkblue round' onclick="switch_func('deposit')"> 입금 (USDT구매)</button>
        </div>
        <div class='col-lg-6 col-12'>
          <button type='button' class='btn wd main_btn b_skyblue round' onclick="switch_func('withdraw')">출금 (USDT판매)</button>
        </div>
      </div>
    </div>

    <!-- 업비트 시세 -->
    <section id='upbit_curency' class='upbit_curency'>
      <!-- <h3 class="wallet_title" style="margin:30px 0 0;display:flex;line-height:44px;">코인시세 By -->
        <!-- <div class='upbit_logo'><img src="<?= G5_THEME_URL ?>/img/icon_bi_upbit.svg"></div> -->
        <!-- <div class="refresh_btn">
            <a id="coin_refresh" class="btn inline"><i class="ri-restart-fill" style="font-size:28px;"></i></a>
        </div> -->
      <!-- </h3> -->
      <div class="checkbox-group">
        <div class='checkbox-tile'>1 USDT = <span id='curency_usdt_eth'><?= $shift_auto_withdrawal_price ?></span> <?= $curencys[1] ?> </div>
      </div>
    </section>

    <!-- 입금 -->
    <section id='deposit' class='loadable'>

      <h3 class="wallet_title">입금(구매)계좌 선택</h3>

      <section id="account_select" class="account_select">
        <?
        
        $i = 0;
        while ($i < count($deposit_array)) { ?>

          <div class="content-box round account_card" data-id=<?= $deposit_array[$i]['no'] ?>>
            <p class="card_title"><?= $deposit_array[$i]['account_name'] ?></p>

            <p class="cabinet">

              <span class="bank_name"><?= $deposit_array[$i]['bank_name'] ?></span>
              <span class="bank_account_num"><?= $deposit_array[$i]['bank_account'] ?></span>
              <span class="bank_account_name"><?= $deposit_array[$i]['bank_account_name'] ?></span>

              <button class="btn wd line_btn mt20 " id="accountCopy" style="background: #212429" onclick="copy_bank_account_num(this)">
                <span style="color: #fff"> 계좌복사 </span>
              </button>
            </p>
          </div>

        <? $i++;
        } ?>
      </section>

      <!-- 미사용
        <div class="content-box round transparent">
          <div class="row ">
            
            <div class='col-12 text-center bank_info'>
            <?= $bank_name ?> : <input type="text" id="bank_account" class="bank_account" value="<?= $bank_account ?>" title='bank_account' disabled />(<?= $account_name ?>)
              <? if ($sel_price) { ?>
                <div class='sel_price'>입금액 : <span class='price'><?= Number_format($sel_price) ?><?= ASSETS_CURENCY ?></span></div>
              <? } ?>
            </div>

            <div class='col-12'>
              <button class="btn wd line_btn " style="background: #f5f5f5;" id="accountCopy" onclick="copyURL('#bank_account')">
                <span > 계좌복사 </span>
              </button>
            </div>
                    
            <div class="wallet qrBox col-12">
                <div class="eth_qr_img qr_img" id="my_eth_qr"></div>
            </div> 
            

            <? if ($sel_price) { ?>
              <div class='sel_price col-12'>입금액 : <span class='price'><?= shift_auto(($sel_price / $coin['usdt_eth']), $curencys[0]) ?> <?= $curencys[0] ?></span>
              <br>
              <span style="font-size:12px;line-height:14px;font-weight:300;letter-spacing:0px;">= <?= shift_auto(($sel_price / $coin['usdt_eth']) * $coin['eth_krw'], 'krw') ?> 원 </span>
              </div>
            <? } ?>
          
          <div class='qrBox_right col-12'>
              <input type="text" id="my_eth_wallet" class="wallet_addr text-center" value="<?= $company_wallet ?>" title='my address' disabled />
              <button class="btn wd line_btn" id="accountCopy" onclick="copyURL('#my_eth_wallet')">
                <span>주소복사</span>
              </button>
            </div> 

          </div>
        </div> 
      -->

      <hr class="hr_dash"></hr>

      <div class="col-sm-12 col-12 content-box round mt20" id="eth">
        <h3 class="wallet_title">입금(구매)신청 </h3> <span class='desc'> - 선택된 구좌로 1회만 요청해주세요.</span>
        <div style="clear:both"></div>
        <div class="row">

          <!-- <div class="col-12 coin_select_wrap mb20">
            <label class="sub_title">- 입금코인 선택</label>
            <select class="form-control" name="" id="select_deposit_coin">
              <option value="<?= $curencys[0] ?>" selected><?= curency_txt($curencys[0]) ?></option>
              <option value="<?= $curencys[4] ?>"><?= curency_txt($curencys[4]) ?></option>
              <option value="<?= $curencys[1] ?>"><?= curency_txt($curencys[1]) ?></option>
            </select>
          </div>

          <div class='txt-box deposit_alert col-12'>
            선택된 입금코인은 ETH (BEP-20) 입니다.<br> ETH (ERC-20) 입금시 처리되지 않으니 반드시 확인후 입금하시기 바랍니다.
          </div> -->


          <div class="btn_ly qrBox_right "></div>
          <div class="col-sm-12 col-12 withdraw mt20">
            <!-- <input type="text" id="deposit_name" class='b_ghostwhite' placeholder="TXID를 입력해주세요"> -->

            <input type="text" id="deposit_value" class='b_ghostwhite' placeholder="입금액을 입력해주세요" inputmode=numeric>
            <label class='currency-right' id="deposit-currency-right"><?= $curencys[1] ?></label>
            <div class="row in_coin hidden" style='width:initial;'>
              <div class="col-12" style="display: flex;justify-content: end;">
                <i class="ri-exchange-fill"></i>
                <span id="active_in">0</span>
              </div>
            </div>
          </div>

          <div class='col-sm-12 col-12 '>
            <button class="btn btn_wd font_white deposit_request deposit_value" data-currency="<?= $curencys[1] ?>">
              <span>입금(구매)신청</span>
            </button>

            <div class='txt-box deposit_alert col-12'>
              입금(구매)신청 후 내역에 입금(구매) 정보가 확인됩니다.<br>
              신청금액과 실제 입금액이 다른경우 처리가 지연될수 있습니다.
            </div>
          </div>
        </div>
      </div>



      <!-- 입금 요청 내역 -->
      <div class="history_box content-box">
        <h3 class="hist_tit wallet_title">입금(구매) 신청 내역 <span style="font-size:11px;font-weight:300;">- 내역의 계좌번호 클릭시 복사</span></h3>

        <div class="b_line2"></div>
        <? if (sql_num_rows($result_deposit) == 0) { ?>
          <div class="no_data"> 입금내역이 존재하지 않습니다.</div>
        <? } ?>

        <? while ($row = sql_fetch_array($result_deposit)) { ?>
          <div class='hist_con'>
            <div class="hist_con_row1">
              <div class="row">
                <span class="hist_date"><?= $row['create_dt'] ?></span>
                <?php
                switch (strtolower($row['coin'])) {
                  case 'eth':
                    $result = '(ERC-20)';
                    break;
                  case 'etc':
                    $result = '(ETC)';
                    break;
                  case 'usdt':
                    $result = '(TetherUS)';
                    break;
                  case 'hja':
                    $result = '(HJA)';
                    break;
                }
                ?>
                <span class="hist_value"><?= $row['amt'] == '0' ? '' : shift_auto($row['amt']) . ' ' . $curencys[1];?></span>
                <span class="curency_value" > <?= $row['in_amt'] . ' ' . $curencys[0] ?></span>

              </div>

              <div class="row">
                <!-- <span class='hist_name'>TXID : <?= $row['txhash'] ?></span> -->
                <?
                  $bank_info = explode(':',$row['txhash']);
                ?>
                <span class='hist_name'><?=$bank_info[0] ?></span>
                <span class="hist_value status"><? string_shift_code($row['status']) ?></span>
                <input type="hidden" class="bank_account_num" name="bank_account" value="<?=$row['bank_account']?>">
                <div class="bank_info_desc " onclick="copy_order_bank_account_num(this);"><?=$bank_info[1]?></div>
              </div>
              
            </div>
          </div>
        <? } ?>
        <?php
        $pagelist = get_paging($config['cf_write_pages'], $page, $total_page_deposit, "{$_SERVER['SCRIPT_NAME']}?id=mywallet&$qstr&view=deposit");
        echo $pagelist;
        ?>
      </div>
    </section>

    <!-- 출금 -->
    <section id='withdraw' class='loadable'>
      <!-- <h3 class="wallet_title">출금(판매)계좌 선택</h3>
      <section id="account_select" class="account_select">
        <?
        
        $i = 0;
        while ($i < count($withdrawal_array )) { ?>

          <div class="content-box round account_card" data-id=<?= $withdrawal_array [$i]['no'] ?>>
            <p class="card_title"><?= $withdrawal_array [$i]['account_name'] ?></p>

            <p class="cabinet">

              <span class="bank_name"><?= $withdrawal_array [$i]['bank_name'] ?></span>
              <span class="bank_account_num"><?= $withdrawal_array [$i]['bank_account'] ?></span>
              <span class="bank_account_name"><?= $withdrawal_array [$i]['bank_account_name'] ?></span>

              <button class="btn wd line_btn mt20 " id="accountCopy" onclick="copy_bank_account_num(this)">
                <span> 계좌복사 </span>
              </button>
            </p>
          </div>

        <? $i++;
        } ?>
      </section> 

      <hr class="hr_dash"></hr>
      -->

      <div class="col-sm-12 col-12 content-box round mt20">
        <h3 class="wallet_title">출금(판매) 신청</h3>
        <span class="desc"> 총 출금 가능액 : <?= shift_auto($withdrwal_total, $curencys[1]) ?> <?= $curencys[0] ?></span>
        
        <div class="row">
          <!-- <div class="col-12 coin_select_wrap mb20">
            <label class="sub_title">- 출금코인 선택</label>
            <select class="form-control" name="" id="select_coin">
              <option value="<?= $curencys[0] ?>" selected><?= curency_txt($curencys[0], 'withdraw') ?></option>
              <option value="<?= $curencys[4] ?>"><?= curency_txt($curencys[4], 'withdraw') ?></option>
              <option value="<?= $curencys[1] ?>"><?= curency_txt($curencys[1], 'withdraw') ?></option>
            </select>
          </div>

          <div class='txt-box withrawal_alert col-12 mb10'>
            선택된 출금코인은 ETH (BEP-20) 입니다.<br> ETH (ERC-20) 주소 기재시 정상출금 되지 않으니 반드시 주소 확인후 기재 바랍니다.
          </div> -->

          <div class='col-12'><label class="sub_title">- 출금정보 (최초 1회입력)</label></div>
          <div class='col-6'>
            <input type="text" id="withdrawal_bank_name" class="b_ghostwhite " placeholder="은행명" value="<?= $member['bank_name'] ?>" <?if($bank_withrawal_info){echo " readonly ";}?>>
          </div>
          <div class='col-6'>
            <input type="text" id="withdrawal_account_name" class="b_ghostwhite " placeholder="예금주" value="<?= $member['account_name'] ?>" <?if($bank_withrawal_info){echo " readonly ";}?>>
          </div>
          <div class='col-12'>
            <!-- <input type="text" id="withdrawal_bank_account" class="b_ghostwhite " placeholder="출금 계좌번호를 입력해주세요" value="<?= Decrypt($member['eth_my_wallet'], $member['mb_id'], 'x'); ?>"> -->
            <input type="text" id="withdrawal_bank_account" class="b_ghostwhite " placeholder="출금 계좌번호를 입력해주세요" value="<?= $member['bank_account'] ?>" <?if($bank_withrawal_info){echo " readonly ";}?>>
          </div>
        </div>

        <div class="input_shift_value mb10 pb5">
          <label class="sub_title">- 출금금액 (수수료:<?= $withdrwal_fee ?>%)</label>
          <span style='display:inline-block;float:right;'><button type='button' id='max_value' class='btn inline' value=''>max</button></span>
          <input type="text" id="sendValue" class="send_coin b_ghostwhite " placeholder="출금 수량을 입력해주세요">
          <label class='currency-right'><?= $curencys[0] ?></label>
          <!-- <div class='fee' style='color:black;padding-right:3px;letter-spacing:-0.5px'>
              <span>실 출금 금액(수수료 제외) : </span><span id='fee_val' style='color:red;margin-right:10px;font-size:14px;font-weight:bold'></span>
            </div> -->

          <div class="row fee hidden mt10" style='width:initial'>
            
            <div class="col-4 swap_amt"></div>

            <div class="col-8">
              <i class="ri-exchange-fill"></i><span id="active_amt">0</span>
              <br>
              <label class="fees">- 수수료(<?= $withdrwal_fee ?>%) :</label><i class="ri-coins-line"></i><span id="fee_val">0</span>
            </div>
          </div>

        </div>

        <div class="b_line5"></div>
        <div class="otp-auth-code-container mt20 pt10">
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
            <button type="button" class="btn wd btn_wd form-send-button" id="Withdrawal_btn" data-toggle="modal" data-target="" data-currency="<?= $curencys[0]; ?>" disabled>출금(판매) 신청</button>
          </div>
        </div>
      </div>

      <!-- 출금내역 -->
      <div class="history_box content-box">
        <h3 class="hist_tit wallet_title">출금(판매) 신청내역</h3>
        <div class="b_line2"></div>

        <? if (sql_num_rows($result_withdraw) == 0) { ?>
          <div class="no_data">출금(판매) 신청내역이 존재하지 않습니다</div>
        <? } ?>

        <? while ($row = sql_fetch_array($result_withdraw)) {
          $coin_curency = $row['coin'] == $curencys[1] ? BONUS_NUMBER_POINT : COIN_NUMBER_POINT;

        ?>
          <div class='hist_con'>
            <div class="hist_con_row1">
              <div class="row">
                <span class="hist_date"><?= $row['create_dt'] ?></span>
                <?php
                switch (strtolower($row['coin'])) {
                  case 'eth':
                    $result = '(BEP-20)';
                    break;
                  case 'etc':
                    $result = '(ETC)';
                    break;
                  case 'usdt':
                    $result = '(TetherUS)';
                    break;
                  case 'hja':
                    $result = '(HJA)';
                    break;
                }
                ?>
                <span class="hist_value "> <?= shift_auto($row['amt']) ?> <?= $row['coin'] . ' ' . $result ?></span>
              </div>

              <div class="row">
                <span class="hist_withval">  신청금액 : <?= shift_auto($row['amt_total']) ?> <?= $row['coin'] ?> / <label>수수료 : </label> <?= shift_auto($row['fee']) ?> <?= $row['coin'] ?></span>
                <span class="hist_value status"><?= shift_auto($row['out_amt']) ?> <?= $curencys[0] . ' ' . $result ?></span>
              </div>

              <!-- <div class="row">
              <span class='hist_bank'><label>Address : </label><?= $row['addr'] ?></span>
            </div> -->

              <div class="row">
                <span class="hist_withval f_small"></span>
                <span class="hist_value status"><? string_shift_code($row['status']) ?></span>
              </div>


            </div>
          </div>
        <? } ?>

        <?php
        $pagelist = get_paging($config['cf_write_pages'], $page, $total_page, "{$_SERVER['SCRIPT_NAME']}?id=mywallet&$qstr&view=withdraw");
        echo $pagelist;
        ?>
      </div>
    </section>
  </div>
</main>

<?php include_once(G5_THEME_PATH . '/_include/tail.php'); ?>

<div class="gnb_dim"></div>
</section>


<!-- <script src="<?= G5_THEME_URL ?>/_common/js/timer.js"></script> -->

<script>
  // 초기화
  window.onload = function() {
    switch_func("<?= $view ?>");
    
    onlyNumber('pin_auth_with');
    $('.cabinet').hide(); 

    // move(<?= $bonus_per ?>); 
    // getTime("<?= $next_rate_time ?>");
    
  }

  function switch_func(n) {
    $('.loadable').removeClass('active');
    $('#' + n).toggleClass('active');
    $('#curency_usdt_eth').text(n == "deposit" ? '<?=$shift_auto_withdrawal_price?>' : '<?=$shift_auto_withdrawal_price_sell?>');
  }

  function switch_func_paging(n) {
    $('.loadable').removeClass('active');
    $('#' + n).toggleClass('active');
    window.location.href = window.location.pathname + "?id=mywallet&'<?= $qstr ?>'&page=1&view=" + n;
  }

  function number_with_commas(x) { // 3자리 콤마
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
  }

  // 계좌번호 복사
  function copy_bank_account_num(e) {
    var temp = $("<input>");
    var bank_account_num = $(e).parent().find('.bank_account_num').text();
    console.log(bank_account_num);

    $("body").append(temp);
    temp.val( onlyNumberReturn(bank_account_num) ).select();
    document.execCommand("copy");
    temp.remove();

    dialogModal("", "<p>계좌번호가 복사 되었습니다.</p>", "success");
  }

  // (입금)계좌번호 복사
  function copy_order_bank_account_num(e) {
    var temp = $("<input>");
    var bank_account_num = $(e).parent().find('.bank_account_num').val();
    console.log(bank_account_num);

    $("body").append(temp);
    temp.val( onlyNumberReturn(bank_account_num) ).select();
    document.execCommand("copy");
    temp.remove();

    dialogModal("", "<p>계좌번호가 복사 되었습니다.</p>", "success");
  }

  /* 코인지갑주소 복사
    function copyURL(addr) {
      dialogModal("", "<p>지갑주소가 복사 되었습니다.</p>", "success");

      var temp = $("<input>");
      $("body").append(temp);
      temp.val($(addr).val()).select();
      document.execCommand("copy");
      temp.remove();
    } 
  */

  /* QR코드 사용안함
    function generateQrCode(qrImg, text, width, height) {
      return new QRCode(document.getElementById(qrImg), {
        text: text,
        width: width,
        height: height,
        colorDark: "#000000",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.H
      });
    } 
  */

  collapseClosed();
</script>



<script>
  $(function() {
    $(".top_title h3").html("<span >입출금</span>");
    
    /* 
      var debug = "<?= $is_debug ?>";
      if(debug){
      console.log('[ Mode : debug ]');
      $('#Withdrawal_btn').attr('disabled',false);
    } */

    
    /* 회사 지갑사용
      var eth_wallet_addr = '<?= $company_wallet ?>';
      if (eth_wallet_addr != '') {
        $('#eth_wallet_addr').val(eth_wallet_addr);
        generateQrCode("my_eth_qr", eth_wallet_addr, 80, 80);
      }
    */

    
    /* 입금 전용 지갑사용
      var my_eth_wallet = "<?= $member['mb_9'] ?>"
      if(my_eth_wallet != ''){
        $('#my_eth_wallet').val(my_eth_wallet);
          generateQrCode("my_eth_qr",my_eth_wallet, 80, 80);
      } 
    */

    /* 출금*/

    var curency_tmp = '<?= $curencys[1] ?>';
    var usdt_curency = '<?= $curencys[0] ?>';
    var eth_curency = '<?= $curencys[0] ?>';
    var etc_curency = '<?= $curencys[4] ?>';
    var erc20_curency = '<?= $curencys[3] ?>';
    
    /* 입출금 서비스 사용여부*/
    var nw_with = '<?= $nw_with ?>'; // 출금서비스 가능여부
    var mb_block = Number("<?= $member['mb_block'] ?>"); // 차단회원 여부
    var personal_with = '<?= $member['mb_leave_date'] ?>'; // 별도구분회원 여부
    var mb_id = '<?=$member['mb_id']?>';
    
    // 주 통화설정


    // 출금설정
    var out_fee = (<?= $withdrwal_fee ?> * 0.01);
    var out_min_limit = '<?= $withdrwal_min_limit ?>';
    var out_max_limit = '<?= $withdrwal_max_limit ?>';
    var out_day_limit = '<?= $withdrwal_day_limit ?>';

    var withdrawal_price = <?= $withdrawal_price ?>;
    var coin_eth_usdt = <?= $coin_eth_usdt ?>;
    var coin_usdt_etc = <?= $coin_usdt_etc ?>;


    // 최대출금가능금액
    var out_mb_max_limit = Number('<?= $withdrwal_total ?>'.replace(/,/g, ''));
    let fixed_amt = 0, fixed_fee = 0;


    // 출금금액 변경 
    function input_change() {

      const input_value = Number(conv_number(document.querySelector('#sendValue').value));
      const real_fee_val = Number(input_value * out_fee);
      const real_withdraw_val = input_value - real_fee_val;

      let shift_coin_value = <?= BONUS_NUMBER_POINT ?>;
      /* let swap_coin_price = (real_withdraw_val * <?= $coin['usdt_krw'] ?>) / withdrawal_price;
      let swap_fee_val = (real_fee_val * <?= $coin['usdt_krw'] ?>) / withdrawal_price;

      if (curency_tmp == eth_curency) {
        shift_coin_value = <?= ASSETS_NUMBER_POINT ?>;
        swap_coin_price = real_withdraw_val / coin_eth_usdt;
        swap_fee_val = real_fee_val / coin_eth_usdt;
      } else if (curency_tmp == etc_curency) {
        shift_coin_value = <?= ASSETS_NUMBER_POINT ?>;
        swap_coin_price = real_withdraw_val / coin_usdt_etc;
        swap_fee_val = real_fee_val / coin_usdt_etc;
      } else if (curency_tmp == usdt_curency) {
        shift_coin_value = <?= ASSETS_NUMBER_POINT ?>;
        swap_coin_price = real_withdraw_val;
        swap_fee_val = real_fee_val;
      } */
      
      if (curency_tmp == '원' && curency_tmp != usdt_curency) {
        shift_coin_value = 0;
        swap_coin_price = (real_withdraw_val * <?= $withdrawal_price_sell ?>);
        swap_fee_val = (real_fee_val * <?= $withdrawal_price_sell ?>);
      }
      
      swap_val = Number(input_value*<?= $withdrawal_price_sell ?>).toFixed(shift_coin_value)
      fixed_amt = Number(swap_coin_price).toFixed(shift_coin_value);
      fixed_fee = Number(swap_fee_val).toFixed(shift_coin_value);

      if (input_value != "") {
        $(".swap_amt").text("= " +Comma_Number(swap_val) + ' ' +curency_tmp);
        $('.fee').css('display', 'flex');
        $('#fee_val').text(`${Comma_Number(fixed_fee)} ${curency_tmp}`);
        $('#active_amt').text(`실 출금 금액(수수료 제외) : ${Comma_Number(fixed_amt)} ${curency_tmp}`);
      }
    }


    $('#sendValue').change(input_change);

    // 출금가능 맥스
    $('#max_value').on('click', function() {
      $("#sendValue").val(out_mb_max_limit);
      input_change('sendValue');
    });

    
    /* document.querySelector('#select_coin').addEventListener('change', (e) => {
      curency_tmp = e.target.value;
      let wallet_addr = '<?= $wallet_addr2 ?>';
      if (curency_tmp == erc20_curency) {
        wallet_addr = '<?= $wallet_addr1 ?>';
      } else if (curency_tmp == etc_curency) {
        wallet_addr = '<?= $wallet_addr3 ?>';
      } else if (curency_tmp == usdt_curency) {
        wallet_addr = '<?= $wallet_addr4 ?>';
      }

      $('#withdrawal_bank_account').val(wallet_addr);
      $('.fee').css('display', 'none');
      document.querySelector('#sendValue').value = "";
      $('#Withdrawal_btn').attr('data-currency', curency_tmp);


      if ($('#select_coin').val() == 'eth') {
        $('.withrawal_alert').css('display', 'block');
      } else {
        $('.withrawal_alert').css('display', 'none');
      }
    }); */



    /* $('#select_deposit_coin').on('change', function() {
      if (this.value == 'eth') {
        $('.deposit_alert').css('display', 'block');
      } else {
        $('.deposit_alert').css('display', 'none');
      }
    }); */

    /* $('#select_coin').on('change',function(){
      
    }); */

    // 입력금액 체크 - ETH
    /* function input_change_eth(obj) {
      let pattern = /^\d+(\.)?(\d{0,8})?$/;
      let korean_check_pattern = /[가-힣ㄱ-ㅎㅏ-ㅣ\x20]/g;
      let zero_check_pattern = /^(0)\d+/g;

      obj.value = obj.value.replace(zero_check_pattern, "");
      obj.value = obj.value.replace(korean_check_pattern, "");
      if (!pattern.test(obj.value)) {
        obj.value = obj.value.slice(0, -1);
        return false;
      }
    } */
    

    /* document.querySelector('#select_deposit_coin').addEventListener('change', (e) => {
      currency_tmp = e.target.value;
      $('#deposit-currency-right').text(currency_tmp);
      $('.deposit_request').attr('data-currency', currency_tmp);
      $('#deposit_value').val('');
      $('.in_coin').css('display', 'none');
    }); */


    // 출금 핀 번호 입력
    $('#pin_open').on('click', function(e) {

      // 회원가입시 핀입력안한경우
      if ("<?= $member['reg_tr_password'] ?>" == "") {
        dialogModal('출금 비밀번호(핀코드) 인증', '<p>출금 비밀번호(핀코드) 등록해주세요.</p>', 'warning');

        $('#modal_return_url').click(function() {
          location.href = "./page.php?id=profile"
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

            $('#Withdrawal_btn').attr('disabled', false);
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



    // 문자인증관련 설정
      
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
    //



    // 출금요청
    $('#Withdrawal_btn').on('click', function() {

      var inputVal = $('#sendValue').val().replace(/,/g, '');
      // console.log(` out_min_limit : ${out_min_limit}\n out_max_limit:${out_max_limit}\n out_day_limit:${out_day_limit}\n out_fee: ${out_fee}`);

      // 출금계좌정보확인
      var withdrawal_bank_name = $('#withdrawal_bank_name').val();
      var withdrawal_account_name = $('#withdrawal_account_name').val();
      var withdrawal_bank_account = $('#withdrawal_bank_account').val();

      // 모바일 등록 여부 확인
      if(mb_hp == '' || mb_hp.length < 10){
        dialogModal('정보수정', '<strong> 안전한 출금을 위해 인증가능한 모바일 번호를 등록해주세요.</strong>', 'warning');

        $('.closed').on('click',function(){
          location.href='/page.php?id=profile';
        })
        return false;
      }

      //KYC 인증 
      /* var out_count = Number("<?= $auth_cnt ?>");
      var kyc_cert = Number("<?= $kyc_cert ?>");

      if (out_count < 1 && kyc_cert != 1) {
        dialogModal('KYC 인증 미등록/미승인 ', "<strong> KYC인증이 미등록 또는 미승인 상태입니다.<br>안전한 출금을 위해 최초 1회 KYC 인증을 진행해주세요<br><a href='/page.php?id=profile' class='btn btn-primary'>KYC인증</a></strong>", 'warning');
        return false;
      } */

      // 출금서비스 이용가능 여부 확인
      if (nw_with == 'N') {
        dialogModal('서비스이용제한', '<strong>현재 출금가능한 시간이 아닙니다.</strong>', 'warning');
        return false;
      }

      // 서비스 이용제한 여부 확인
      if (personal_with != '') {
        dialogModal('서비스이용제한', '<strong>관리자에게 연락주세요</strong>', 'warning');
        return false;
      }


      // 계좌정보 입력 확인
      if (withdrawal_bank_account == '' || withdrawal_account_name == '' || withdrawal_bank_name =='') {
        dialogModal('출금지갑확인', '<strong> 출금정보를 입력해주세요.</strong>', 'warning');
        return false;
      }
      

      // 금액 입력 없거나 출금가능액 이상일때  
      if (inputVal == '' || inputVal > out_mb_max_limit) {
        console.log(`input : ${inputVal} \n max : ${out_mb_max_limit}`);
        dialogModal('금액 입력 확인', '<strong>출금 금액을 확인해주세요.</strong>', 'warning');
        return false;
      }

      // 최소 금액 확인
      if (out_min_limit != 0 && inputVal < Number(out_min_limit)) {
        dialogModal('금액 입력 확인', '<strong> 최소가능금액은 ' + Price(out_min_limit) + ' ' + usdt_curency + '입니다.</strong>', 'warning');
        return false;
      }

      //최대 금액 확인
      if (out_max_limit != 0 && inputVal > Number(out_max_limit)) {
        dialogModal('금액 입력 확인', '<strong> 1회 출금 가능금액은 ' + Price(out_max_limit) + ' ' + usdt_curency + '입니다.</strong>', 'warning');
        return false;
      }

    process_pin_mobile().then(function (){

      $.ajax({
        type: "POST",
        url: "./util/withdrawal_proc.php",
        cache: false,
        dataType: "json",
        data: {
          mb_id: mb_id,
          func: 'withdraw',
          total_amt: inputVal,
          select_coin: curency_tmp,
          fixed_fee: fixed_fee,
          fixed_amt: fixed_amt,
          bank_name: withdrawal_bank_name,
          bank_account: withdrawal_bank_account,
          account_name: withdrawal_account_name,
          rate:<?=$withdrawal_price_sell?>

        },
        success: function(res) {
          if (res.result == "success") {
            dialogModal('출금신청이 정상적으로 처리되었습니다.', '<p>실제 출금까지 24시간 이상 소요될수있습니다.</p>', 'success');

            $('.closed').click(function() {
              location.href = '/page.php?id=mywallet&view=withdraw';
            });
          } else {
            dialogModal('Withdraw Failed', "<p>" + res.sql + "</p>", 'warning');
          }
        }
      });

    });

      /* if (!mb_block) {
      } else {
        dialogModal('Withdraw Failed', "<p>Not available right now</p>", 'failed');
      } */
    });


    function process_pin_mobile(){

      return new Promise(
        function(resolve,reject){
        dialogModal('본인인증', "<p>"+maskingFunc.phone(mb_hp)+"<br>모바일로 전송된 인증코드 6자리를 입력해주세요<br><input type='text' class='modal_input' id='auth_mobile_pin' name='auth_mobile_pin'></input><span class='time_remained'></span><span class='processcode'></span></p>", 'confirm');

        if( is_sms_submitted == false ){
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
                input_timer(res.time,'.time_remained');

                $('#modal_confirm').on('click',function(){

                  if(!time_reamin){
                    is_sms_submitted = false;
                    alert("시간초과로 다시 시도해주세요");
                  }else{
                    var input_pin_val = $("#auth_mobile_pin").val();
                    check_auth_mobile(input_pin_val);

                    if(!check_pin){
                      $(".processcode").html("인증코드가 일치하지 않습니다.");
                      return false;
                    }else{
                      is_sms_submitted = false;
                      process_step = true;
                      resolve();
                    }

                  }
                });

                $('#dialogModal .cancle').on('click',function(){
                  is_sms_submitted = false;
                  location.reload();
                });

              }
            }
          });

        }else{
          alert('잠시 후 다시 시도해주세요.');
        }
      });
    } 


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  });
</script>






<script>

  /* 입금 */
  $(function() {
  
    // P2P 입금 계좌 카드 선택
    var select_account_card_id = -1;
    var deposit_array = <?= json_encode($deposit_array)?>;

    // P2P 형태 계좌
    function account_card_open() {
      var card_id = $(this).data('id');
      if (select_account_card_id != card_id) {
        $('.cabinet').css('display', 'none');
        $('.account_card').removeClass('active');
        // 계좌 노출 미사용
        // $(this).children('.cabinet').slideDown();
        $(this).addClass('active');
        $(this).children('.cabinet').css('display', 'block');
      }
      select_account_card_id = card_id;
    }

    $('.account_card').bind('click', account_card_open);

    // 입금액 대비 환산
    document.querySelector('#deposit_value').addEventListener('keyup', (e) => {

      // input_change_eth(e.target);
      // let symbol = $('#deposit-currency-right').text();

      let symbol = $('#deposit-currency-right').text(); // 원화 수동처리
      let deposit_amount = uncomma(e.target.value);
      let swap_coin_price = deposit_amount * <?= $withdrawal_price ?>;

      if(symbol == '원') {
        swap_coin_price = deposit_amount/<?=$withdrawal_price?>;
        fixed_amt = Number(swap_coin_price).toFixed(2);
        $('.in_coin').css('display', 'block');
        $('#active_in').text(`${number_with_commas(fixed_amt)} USDT`);

      }else{

        if (symbol == eth_curency) {
          swap_coin_price = deposit_amount * <?= $coin['eth_krw'] ?>;
        } else if (symbol == etc_curency) {
          swap_coin_price = deposit_amount * <?= $coin['etc_krw'] ?>;
        } else if (symbol == usdt_curency) {
          swap_coin_price = deposit_amount * <?= $withdrawal_price ?>;
        } 

        fixed_amt = Number(swap_coin_price).toFixed(0);
        $('.in_coin').css('display', 'block');
        $('#active_in').text(`${number_with_commas(fixed_amt)} 원`);
      }

    });



    /*입금 확인 요청 - coin */
    /* $('.deposit_request.eth').on('click', function (e) {
      var d_price = $('#deposit_value').val();

      if($('.d_price').text() != ""){
          d_price = $('.d_price').text();
      }
      
      var coin = $(this).data('currency');
      var hash_target = $(this).parent().parent().find('.confirm_hash');
      
      if(hash_target.val()==""){
          dialogModal('Deposit Confirmation Request','<p>Transaction Hash is empty!</p>','warning');
          return;
      }

      if(debug) console.log('입금 : '+ coin +' || tx :' + hash_target.val());

      $.ajax({
        url: '/util/request_deposit.php',
        type: 'POST',
        cache: false,
        async: false,
        data: {
          "mb_id" : mb_id,
          "coin" : coin,
          "hash": hash_target.val(),
          "d_price" : d_price
        },
        dataType: 'json',
        success: function(result) {
          if(result.response == "OK"){
            dialogModal('Deposit Request', 'Deposit Request success', 'success');
            $('.closed').click(function(){
              location.reload();
            });
          }else{
            if(debug) dialogModal('Deposit Request',result.data,'failed'); 
            else dialogModal('Deposit Request','<p>ERROR<br>Please try later</p>','failed');
          }
        },
        error: function(e){
          if(debug) dialogModal('ajax ERROR','IO ERROR','failed'); 
        }
        
      });
    });  */


  // 입금확인요청 
    $('.deposit_request').on('click', function(e) {
      
      var mb_id = '<?=$member['mb_id']?>';

      if(Number(select_account_card_id) < 1){
        dialogModal('<p>입금(구매) 구좌 선택</p>', '<p>입금(구매)구좌를 선택해주세요.</p>', 'warning');
        return false;
      }

      var selected_account_card = deposit_array[select_account_card_id-1];
      
      // var d_name = $('#deposit_name').val(); // 입금자
      var d_name = selected_account_card.no;
      var d_price = conv_number($('#deposit_value').val()); // 입금액
      var coin = $(this).data('currency');

      // 입금설정
      var in_fee = (<?= $deposit_fee ?> * 0.01);
      var in_min_limit = <?= $deposit_min_limit ?> * <?=shift_coin($withdrawal_price,KRW_NUMBER_POINT)?>;
      var in_max_limit = <?= $deposit_max_limit ?> * <?=shift_coin($withdrawal_price,KRW_NUMBER_POINT)?>;
      var in_day_limit = '<?= $deposit_day_limit ?>';

      console.log(` in_min_limit : ${in_min_limit}\n in_max_limit:${in_max_limit}\n in_day_limit:${in_day_limit}\n in_fee: ${in_fee}`);
      console.log(' 선택계좌 : ' + selected_account_card.bank_account_name + ' || 입금액 :' + d_price);
      
      if (d_price == '') {
        dialogModal('<p>입금 요청값 확인</p>', '<p>입금요청 항목을 입력해주시고 다시 시도해주세요.</p>', 'warning');
        return false;
      }

      // 입금액 fixed_amt d_price
      if (in_min_limit > 0 && Number(d_price) < Number(in_min_limit)) {
        dialogModal('<p>최소 입금액 확인</p>', '<p>최소 입금 확인 금액은 ' + Price(Number(in_min_limit)) + ' 원 ('+ '<?= $deposit_min_limit ?>' + ' USDT) 입니다. </p>', 'warning');
        return false;
      }

      $.ajax({
        url: '/util/request_deposit.php',
        type: 'POST',
        cache: false,
        dataType: 'json',
        data: {
          "mb_id": mb_id,
          "coin": coin,
          "hash": d_name,
          "d_price": d_price,
          "calc_coin" : fixed_amt
        },
        success: function(result) {
          if (result.response == "OK") {
            dialogModal('입금(구매) 신청', '입금(구매) 신청이 정상처리되었습니다.', 'success');
            $('.closed').click(function() {
              location.reload();
            });
          } else {
            dialogModal('Deposit Request', result.data, 'failed');
          }
        },
        error: function(e) {
          if (debug) dialogModal('ajax ERROR', 'IO ERROR', 'failed');
        }
      });
    });
  ///////////

  });
</script>



  