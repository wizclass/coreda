<?php
ob_start();
$debug = FALSE;

define('ASSETS_NUMBER_POINT', 4); // 입금 단위
define('BONUS_NUMBER_POINT', 0); // 수당계산,정산기준단위
define('COIN_NUMBER_POINT', 4); // 코인 단위
define('KRW_NUMBER_POINT', 0);

$host_name = '127.0.0.1';
$user_name = 'root';
$user_pwd = 'willsoft0780!@';
// $user_pwd = 'wizclass235689!@';
$database = 'coreda';
$conn = mysqli_connect($host_name, $user_name, $user_pwd, $database);

$code = "direct";
$bonus_day = date('Y-m-d');

$check_booster_today_sql = "select count(day) as cnt from soodang_pay where day = '{$bonus_day}' and allowance_name = '{$code}'";
$check_booster_today_result  = mysqli_query($conn, $check_booster_today_sql);
$check_booster_today = mysqli_fetch_array($check_booster_today_result)['cnt'];

if ($check_booster_today > 0) {
  echo "<code>{$check_booster_today_sql}</code><br>";
  echo "{$bonus_day} {$code} 는 이미 지급되었습니다.";
  die;
}

//회원 리스트를 읽어 온다.
$sql_common = " FROM g5_order AS o, g5_member AS m ";
$sql_search = " WHERE o.mb_id=m.mb_id AND od_soodang_date ='" . $bonus_day . "' ";
$sql_mgroup = ' GROUP BY m.mb_id ORDER BY m.mb_no asc';

$pre_sql = "select count(*) 
            {$sql_common}
            {$sql_search}
            {$sql_mgroup}";


if ($debug) {
  echo "<code>";
  print_r($pre_sql);
  echo "</code><br>";
}

$bonus_sql = "select * from wallet_bonus_config WHERE used > 0 order by no asc";
$list = mysqli_query($conn, $bonus_sql);

$pre_setting = mysqli_fetch_array($list);
$pre_condition = '';

function bonus_pick($val)
{
  global $g5, $conn;
  $pick_sql = "select * from wallet_bonus_config where code = '{$val}' ";
  $pick_result = mysqli_query($conn, $pick_sql);
  $list = mysqli_fetch_array($pick_result);
  return $list;
}

function _get_coins_price()
{
  $result = array();
  $url_list = array(
    'https://api.upbit.com/v1/ticker?markets=KRW-ETH&markets=KRW-ETC&markets=USDT-ETH&markets=USDT-ETC',
    "https://pro-api.coinmarketcap.com/v1/tools/price-conversion?CMC_PRO_API_KEY=9a0e9663-df7f-431b-9561-d46935376d5b&amount=1&symbol=usdt",
    "https://api.mexc.com/api/v3/ticker/24hr?symbol=COREUSDT"
    // "https://api.bitforex.com/api/v1/market/ticker?symbol=coin-usdt-hja"
  );

  $data = _multi_curl($url_list);

  $eth_krw = $data[0][0]['trade_price'];
  $etc_krw = $data[0][1]['trade_price'];
  $usdt_eth = $data[0][2]['trade_price'];
  $usdt_etc = $data[0][3]['trade_price'];

  $result['usdt_krw'] = $eth_krw / $usdt_eth;

  $result['usdt_eth'] = $usdt_eth;
  $result['usdt_etc'] = $usdt_etc;
  $result['eth_krw'] = $eth_krw;
  $result['etc_krw'] = $etc_krw;
  $result['eth_usdt'] = $data[1]['data']['quote']['USD']['price'];
  $result['core_usdt'] = $data[2]['highPrice'];

  return $result;
}

function _multi_curl($url)
{
  $ch = array();
  $response = array();
  $curl_init = curl_multi_init();
  foreach ($url as $key => $value) {
    $ch[$key] = curl_init($value);
    curl_setopt($ch[$key], CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch[$key], CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch[$key], CURLOPT_SSL_VERIFYHOST, false);
    curl_multi_add_handle($curl_init, $ch[$key]);
  }

  do {
    curl_multi_exec($curl_init, $running);
    curl_multi_select($curl_init);
  } while ($running > 0);

  foreach (array_keys($ch) as $key) {
    $response[$key] = json_decode(curl_multi_getcontent($ch[$key]), true);
    curl_multi_remove_handle($curl_init, $ch[$key]);
  }

  curl_multi_close($curl_init);
  return $response;
}

function shift_coin($val)
{
  return Number_format($val, COIN_NUMBER_POINT);
}

function clean_coin_format($val, $decimal = COIN_NUMBER_POINT)
{
  $_num = (int)str_pad("1", $decimal + 1, "0", STR_PAD_RIGHT);
  return floor($val * $_num) / $_num;
}

function clean_number_format($val, $decimal = COIN_NUMBER_POINT)
{
  $_decimal = $decimal <= 0 ? 1 : $decimal;
  $_num = number_format(clean_coin_format($val, $decimal), $_decimal);
  $_num = rtrim($_num, 0);
  $_num = rtrim($_num, '.');

  return $_num;
}

function soodang_record($mb_id, $code, $bonus_val, $rec, $rec_adm, $bonus_day, $mb_no = '', $mb_level = '', $mb_name = '', $coin_curency)
{
  global $g5, $debug, $conn;

  $soodang_sql = " insert soodang_pay set day='" . $bonus_day . "'";
  $soodang_sql .= " ,mb_id			= '" . $mb_id . "'";
  $soodang_sql .= " ,mb_name		= '" . $mb_name . "'";
  $soodang_sql .= " ,allowance_name	= '" . $code . "'";
  $soodang_sql .= " ,benefit		=  " . $bonus_val;
  $soodang_sql .= " ,curency      = {$coin_curency} ";
  $soodang_sql .= " ,rec			= '" . $rec . "'";
  $soodang_sql .= " ,rec_adm		= '" . $rec_adm . "'";
  $soodang_sql .= " ,datetime		= now()";

  if ($mb_no != '') {
    $soodang_sql .= " ,mb_no		= '" . $mb_no . "'";
  }
  if ($mb_level != '') {
    $soodang_sql .= " ,mb_level		= '" . $mb_level . "'";
  }

  // 수당 푸시 메시지 설정
  /* $mb_push_data = sql_fetch("SELECT fcm_token,mb_sms from g5_member WHERE mb_id = '{$mb_id}' ");
  $push_agree = $mb_push_data['mb_sms'];
  $push_token = $mb_push_data['fcm_token'];

  $push_images = G5_URL.'/img/marker.png';
  if($push_token != '' && $push_agree == 1){
      setPushData("[DFINE] - ".$mb_id." 수당 지급 ", $code.' =  +'.$bonus_val.' ETH', $push_token,$push_images);
  } */

  if ($debug) {
    echo "<code>";
    print_r($soodang_sql);
    echo "</code>";
    return true;
  } else {
    return mysqli_query($conn, $soodang_sql);
  }
}

function bonus_limit_tx($bonus_limit)
{
  if ($bonus_limit == '' || $bonus_limit == 0) {
    $bonus_limit_tx = '상한제한없음';
  } else {
    $bonus_limit_tx = (Number_format($bonus_limit * 100)) . '% 까지 지급';
  }
  return $bonus_limit_tx;
}

if ($pre_setting['layer'] != '') {
  $pre_condition = ' and ' . $pre_setting['layer'];
  $pre_condition_in = $pre_setting['layer'];
} else {
  $pre_condition_in = ' mb_level < 9 and mb_rate > 0';
}

$pre_result = mysqli_query($conn, $pre_sql);
$result_cnt = mysqli_num_rows($pre_result);

$bonus_row = bonus_pick($code);
if ($bonus_row['limited'] > 0) {
  $bonus_limit = $bonus_row['limited'] / 100;
} else {
  $bonus_limit = $bonus_row['limited'];
}
$bonus_rate = $bonus_row['rate'];
$balance_limit = $bonus_row['limited'];

$pre_condition = '';
$bonus_limit_tx = bonus_limit_tx($bonus_limit);

$coin = _get_coins_price();
$coin['core_KRW'] = $coin['core_usdt'] *  $coin['usdt_krw'];
$coin_curency = shift_coin($coin['core_KRW'], 'krw');

// 설정로그 
echo "<span class ='title' style='font-size:20px;'>" . $bonus_row['name'] . " 수당 정산</span><br>";
echo "<strong> 1 CORE / 원화 시세 :: <span class='red'>" . shift_coin($coin['core_KRW'], 'krw') . "</span> 원</strong><br>";
echo "<strong>" . strtoupper($code) . " 수당 지급비율 : " . $bonus_row['rate'] . "%   </strong> |    지급조건 -" . $pre_condition . " | " . $bonus_limit_tx . "<br>";
echo "<strong>" . $bonus_day . "</strong><br>";
echo "<br><span class='red'> 기준대상자(매출발생자) : " . $result_cnt . "</span><br><br>";
echo "<div class='btn' onclick='bonus_url();'>돌아가기</div>";

?>

<html>

<body>
  <header>정산시작</header>
  <div>
    <?

    $price_cond = ", SUM(pv) AS hap";

    $sql = "SELECT o.od_date,o.pv,o.upstair,o.od_tno,o.od_name, m.mb_no, m.mb_id, m.mb_recommend, m.mb_name, m.mb_level, m.mb_deposit_point, m.mb_balance, m.grade
            {$sql_common}
            {$sql_search}
            ORDER BY m.mb_no asc ";
    $result = mysqli_query($conn, $sql);

    // 디버그 로그 
    if ($debug) {
      echo "<code>";
      print_r($sql);
      echo "</code><br>";
    }

    excute();


    function  excute()
    {

      global $result, $conn;
      global $g5, $bonus_day, $bonus_condition, $code, $bonus_rates, $bonus_rate, $pre_condition_in, $bonus_limit, $balance_limit, $coin_curency;
      global $debug;


      for ($i = 0; $row = mysqli_fetch_array($result); $i++) {

        $mb_id = $row['mb_id'];
        $it_id = $row['od_tno'];
        $it_bonus = $row['upstair'];
        $it_name = $row['od_name'];

        echo "<br><br><span class='title block' style='font-size:30px;'>" . $mb_id . "</span><br>";

        // 추천, 후원 조건
        if ($bonus_condition < 2) {
          $recom = 'mb_recommend';
        } else {
          $recom = 'mb_brecommend';
        }

        /* $sql = "SELECT mb_no, mb_id, mb_name,grade,mb_level, mb_balance, mb_recommend, mb_brecommend, mb_deposit_point,
        (SELECT od_cart_price  FROM g5_shop_order WHERE A.mb_id = mb_id AND od_date = '{$bonus_day}') AS today_sale FROM g5_member AS A WHERE {$recom} = '{$mb_id}' "; */

        $sql = " SELECT mb_no, mb_id, mb_name, grade, mb_level, mb_balance, mb_recommend, mb_brecommend, mb_deposit_point FROM g5_member WHERE mb_id = '{$row[$recom]}' ";
        $sql_result = mysqli_query($conn, $sql);
        $sql_result_cnt = mysqli_num_rows($sql_result);

        while ($recommend = mysqli_fetch_array($sql_result)) {

          $recom_id = $recommend['mb_id'];
          $mb_no = $recommend['mb_no'];
          $mb_name = $recommend['mb_name'];
          $mb_level = $recommend['mb_level'];
          $mb_deposit = $recommend['mb_deposit_point'];
          $grade = $recommend['grade'];

          echo "상품 : " . $it_name . " | 직추천인 : <span class='red'> " . $recom_id . "</span><br> ";
          /*if($recommend['today_sale'] > 0){
                $today_sales=$recommend['today_sale'];
            }else{$today_sales = 0;} */

          // 관리자 제외

          if ($pre_condition_in) {

            /*  $rate_cnt = it_item_return($it_id,'model');
                
                if(!$bonus_rate){
                    $bonus_rate = $bonus_rates[$rate_cnt-1]*0.01;
                } */

            $bonus_rated = $bonus_rate * 0.01;

            $benefit = ($it_bonus * $bonus_rated); // 매출자 * 수당비율

            /*  list($mb_balance,$balance_limit,$benefit_limit,$admin_cash) = bonus_limit_check($recom_id,$benefit);

                echo "<code>";
                echo "현재수당 : ".Number_format($mb_balance)."  | 수당한계 :". Number_format($balance_limit).' | ';
                echo "발생할수당: ".Number_format($benefit)." | 지급할수당 :".Number_format($benefit_limit);
                echo "</code><br>";
                if($admin_cash == 1){
                    $rec_adm= 'fall check';
                }
                 */

            $coin_benefit = $benefit;
            $benefit_limit = clean_number_format($coin_benefit, 4);

            $rec = $code . ' Recommend Bonus from  ' . $mb_id . ' | ' . $it_name;
            $rec_adm = $it_name . ' | ' . clean_number_format($it_bonus) . '*' . $bonus_rated . '=' . clean_number_format($benefit) . '(' . clean_number_format($coin_benefit, 4) . ')';


            echo $recom_id . " | " . Number_format($it_bonus) . '*' . $bonus_rated;

            /* 
                if($benefit > $benefit_limit && $balance_limit != 0 ){

                    $rec_adm .= "<span class=red> |  Bonus overflow :: ".Number_format($benefit_limit - $benefit)."</span>";
                    echo "<span class=blue> ▶▶ 수당 지급 : ".Number_format($benefit)."</span>";
                    echo "<span class=red> ▶▶▶ 수당 초과 (한계까지만 지급) : ".Number_format($benefit_limit)." </span><br>";
                }else if($benefit != 0 && $balance_limit == 0 && $benefit_limit == 0){

                    $rec_adm .= "<span class=red> | Sales zero :: ".Number_format($benefit_limit - $benefit)."</span>";
                    echo "<span class=blue> ▶▶ 수당 지급 : ".Number_format($benefit)."</span>";
                    echo "<span class=red> ▶▶▶ 수당 초과 (기준매출없음) : ".Number_format($benefit_limit)." </span><br>";
                }else if($benefit == 0){

                    echo "<span class=blue> ▶▶ 수당 미발생 </span>";
                }else{
                    

                } */

            // echo "<span > ▶▶ 지급 수당: " . Number_format($benefit) . " 원</span>";
            echo "<span class=blue> ▶▶▶ Core 지급 : " . clean_number_format($coin_benefit, 4) . " core</span><br>";


            if ($benefit > 0 && $benefit_limit > 0) {

              $record_result = soodang_record($recom_id, $code, $benefit_limit, $rec, $rec_adm, $bonus_day, $mb_no, $mb_level, $mb_name, $coin_curency);

              if ($record_result) {
                $balance_up = "update g5_member set mb_balance = mb_balance + {$benefit_limit}  where mb_id = '" . $recom_id . "'";

                // 디버그 로그
                if ($debug) {
                  echo "<code>";
                  print_R($balance_up);
                  echo "</code>";
                } else {
                  mysqli_query($conn, $balance_up);
                }
              }
            }
          }
        } // while
      } // for
    }
    ?>

  </div>

  <footer> 정산 완료</footer>

  <div class='btn' onclick="bonus_url('<?= $category ?>');">돌아가기</div>

  <body>

</html>

<style>
  body {
    font-size: 14px;
    line-height: 18px;
    letter-spacing: 0px;
  }

  code {
    color: green;
    display: block;
    margin-bottom: 5px;
    font-size: 11px;
  }

  .red {
    color: red;
    font-weight: 600;
  }

  .blue {
    color: blue;
    font-weight: 600;
  }

  .big {
    font-size: 16px;
    font-weight: 600;
  }

  .title {
    font-weight: 800;
    color: black;
    font-size: 16px;
    display: block;
  }

  .box {
    background: ghostwhite;
    margin-top: 30px;
    border-bottom: 1px solid #eee;
    padding-left: 5px;
    width: 100%;
    display: block;
  }

  .block {
    font-size: 26px;
    background: turquoise;
    display: block;
    height: 30px;
    line-height: 30px;
  }

  .block.coral {
    background: lightcoral
  }

  .indent {
    text-indent: 20px;
    display: inline-block;
  }

  .btn {
    background: black;
    padding: 5px 20px;
    display: inline-block;
    color: white;
    font-weight: 600;
    cursor: pointer;
    margin-bottom: 20px;
  }

  footer,
  header {
    margin: 20px 0;
    background: black;
    color: white;
    text-align: center
  }

  .error {
    display: block;
    width: 100%;
    text-align: center;
    height: 150px;
    line-height: 150px
  }

  .hidden {
    display: none;
  }

  .desc {
    font-size: 11px;
    color: #777;
  }

  .subtitle {
    font-size: 20px;
  }

  .sys_log {
    margin-bottom: 30px;
  }
</style>

<?php
//로그 기록
if ($debug) {
} else {
    $html = ob_get_contents();
    //ob_end_flush();
    $dir = "/var/www/html/coreda/data/log/{$code}/";

    if(!is_dir($dir)){
        mkdir($dir, '777');
    }
    $logfile = "/var/www/html/coreda/data/log/{$code}/{$code}_{$bonus_day}.html";
    fopen($logfile, "w");
    file_put_contents($logfile, ob_get_contents());
}
?>