<?php
include_once('./_common.php');
include_once(G5_LIB_PATH.'/Telegram/telegram_api.php');
include_once(G5_THEME_PATH . '/_include/wallet.php');

// 입금처리 PROCESS
// $debug = 1;


/*현재시간*/
$now_datetime = date('Y-m-d H:i:s');
$now_date = date('Y-m-d');

if($debug ==1){
  $mb_id = 'test1';
  $txhash = 3;
  $coin = '원';
  $d_price = 300000;
  $mb_name = '테스터1';
}else{
  $mb_id = $_POST['mb_id'];
  $txhash = $_POST['hash'];
  $coin = $_POST['coin'];
  $d_price = $_POST['d_price'];
  $mb_name = $member['mb_name'];
  $calc_coin = $_POST['calc_coin'];
}

// 입금계좌정보
$deposit_array = array_bank_account(1);
$deposit_info = $deposit_array[$txhash];
$deposit_infomation = $deposit_info['account_name'].' : '.$deposit_info['bank_name']." ".$deposit_info['bank_account']." ".$deposit_info['bank_account_name'];

/*기존건 확인*/
$pre_result = sql_fetch("SELECT count(*) as cnt from wallet_deposit_request 
WHERE mb_id ='{$mb_id}' AND create_d = '{$now_date}' AND in_amt = {$d_price} ");

if($pre_result['cnt'] < 1){

  $get_coins_price = get_coins_price();

  if ($coin == '원' || $coin == 'krw') {
    $usdt = shift_coin($get_coins_price['usdt_krw'],2);
    // $point = shift_coin($d_price/$usdt,2);
    $point = $calc_coin;

  }else{
    if($coin == 'etc') {
      $usdt = $get_coins_price['usdt_etc'];
    } else if ($coin == 'hja') {
      $result = sql_fetch("SELECT current_cost, used FROM wallet_coin_price WHERE idx = '1'");
      $usdt = $result['used'] == '1' ? $result['current_cost'] : 1;
    } else if ($coin == 'eth') {
      $usdt = $get_coins_price['usdt_eth'];
    } else if ($coin == 'usdt') {
      $usdt = 1;
    }else {
      $usdt = null;
    }
    $point = $usdt * $d_price;
  }

  $sql = "INSERT INTO wallet_deposit_request(mb_id,od_id, txhash, bank_account, create_dt,create_d,status,coin,cost,amt,in_amt) 
  VALUES('$mb_id',{$txhash},'$deposit_infomation','{$deposit_info['bank_account']}','$now_datetime','$now_date',0,'$coin', {$usdt},{$d_price},{$point})";
  
  if($debug){
    print_R($sql);
    $result = 1;
  }else{
    $result = sql_query($sql);
  }

  // 입금알림 텔레그램 API
  $msg = '[RED Banana][입금요청] '.$mb_id.'('.$mb_name.') 님의 '.shift_auto($d_price, $curencys[0]).' '.$curencys[0].' 입금요청이 있습니다.';

  if(TELEGRAM_ALERT_USE){  
    curl_tele_sent($msg);
  }else{
    if($debug ==1){
      echo "<br><code>".$msg."</code><br>";
    }
  }
  
  if($result){
    echo json_encode(array("response"=>"OK", "data"=>'complete'));
  }else{
    echo json_encode(array("response"=>"FAIL", "data"=>"<p>ERROR<br>Please try later</p>"));
  }
}else{
  echo json_encode(array("response"=>"FAIL", "data"=>"이미 해당 요청이 처리진행중입니다."),JSON_UNESCAPED_UNICODE);
}


?>
