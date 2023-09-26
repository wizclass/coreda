<?php
$sub_menu = "600200";
include_once('./_common.php');
// $debug = 1;
include_once('./bonus_inc.php');

auth_check($auth[$sub_menu], 'r');



//회원 리스트를 읽어 온다.
$sql_common = " FROM g5_order AS o, g5_member AS m ";
$sql_search=" WHERE o.mb_id=m.mb_id AND od_soodang_date ='".$bonus_day."' ";
$sql_mgroup=' GROUP BY m.mb_id ORDER BY m.mb_no asc';

$pre_sql = "select count(*) 
            {$sql_common}
            {$sql_search}
            {$sql_mgroup}";


if($debug){
    echo "<code>";
    print_r($pre_sql);
    echo "</code><br>";
}

$pre_result = sql_query($pre_sql);
$result_cnt = sql_num_rows($pre_result);

$bonus_rate = $bonus_row['rate'];
$balance_limit = $bonus_row['limit'];

$coin = _get_coins_price();
$coin['core_KRW'] = $coin['core_usdt'] *  $coin['usdt_krw'];
$coin_curency = shift_auto($coin['core_KRW'],'krw');

ob_start();

// 설정로그 
echo "<span class ='title' style='font-size:20px;'>".$bonus_row['name']." 수당 정산</span><br>";
echo "<strong> 1 CORE / 원화 시세 :: <span class='red'>".shift_auto($coin['core_KRW'],'krw')."</span> 원</strong><br>";
echo "<strong>".strtoupper($code)." 수당 지급비율 : ". $bonus_row['rate']."%   </strong> |    지급조건 -".$pre_condition." | ".$bonus_limit_tx."<br>";
echo "<strong>".$bonus_day."</strong><br>";
echo "<br><span class='red'> 기준대상자(매출발생자) : ".$result_cnt."</span><br><br>";
echo "<div class='btn' onclick='bonus_url();'>돌아가기</div>";

?>

<html><body>
<header>정산시작</header>    
<div>
<?

$price_cond=", SUM(pv) AS hap";

$sql = "SELECT o.od_date,o.pv,o.upstair,o.od_tno,o.od_name, m.mb_no, m.mb_id, m.mb_recommend, m.mb_name, m.mb_level, m.mb_deposit_point, m.mb_balance, m.grade
            {$sql_common}
            {$sql_search}
            ORDER BY m.mb_no asc ";
$result = sql_query($sql);

// 디버그 로그 
if($debug){
	echo "<code>";
    print_r($sql);
	echo "</code><br>";
}

excute();


function  excute(){

    global $result;
    global $g5, $bonus_day, $bonus_condition, $code, $bonus_rates, $bonus_rate,$pre_condition_in,$bonus_limit,$balance_limit,$coin_curency ;
    global $debug;


    for ($i=0; $row=sql_fetch_array($result); $i++) {   

        $mb_id=$row['mb_id'];
        $it_id = $row['od_tno'];
        $it_bonus = $row['upstair'];
        $it_name = $row['od_name'];

        echo "<br><br><span class='title block' style='font-size:30px;'>".$mb_id."</span><br>";

        // 추천, 후원 조건
        if($bonus_condition < 2){
            $recom= 'mb_recommend';
        }else{
            $recom= 'mb_brecommend';
        }
        
         /* $sql = "SELECT mb_no, mb_id, mb_name,grade,mb_level, mb_balance, mb_recommend, mb_brecommend, mb_deposit_point,
        (SELECT od_cart_price  FROM g5_shop_order WHERE A.mb_id = mb_id AND od_date = '{$bonus_day}') AS today_sale FROM g5_member AS A WHERE {$recom} = '{$mb_id}' "; */
       
        $sql = " SELECT mb_no, mb_id, mb_name, grade, mb_level, mb_balance, mb_recommend, mb_brecommend, mb_deposit_point FROM g5_member WHERE mb_id = '{$row[$recom]}' ";
        $sql_result = sql_query($sql);
        $sql_result_cnt = sql_num_rows($sql_result);

        while( $recommend = sql_fetch_array($sql_result) ){   
        
            $recom_id = $recommend['mb_id'];
            $mb_no=$recommend['mb_no'];
            $mb_name=$recommend['mb_name'];
            $mb_level=$recommend['mb_level'];
            $mb_deposit=$recommend['mb_deposit_point'];
            $grade=$recommend['grade'];

            echo "상품 : ".$it_name." | 직추천인 : <span class='red'> ".$recom_id."</span><br> ";
            /*if($recommend['today_sale'] > 0){
                $today_sales=$recommend['today_sale'];
            }else{$today_sales = 0;} */

            // 관리자 제외
            
            if($pre_condition_in){	
                
               /*  $rate_cnt = it_item_return($it_id,'model');
                
                if(!$bonus_rate){
                    $bonus_rate = $bonus_rates[$rate_cnt-1]*0.01;
                } */

                $bonus_rated = $bonus_rate * 0.01;
                
                $benefit=($it_bonus*$bonus_rated); // 매출자 * 수당비율

               /*  list($mb_balance,$balance_limit,$benefit_limit,$admin_cash) = bonus_limit_check($recom_id,$benefit);

                echo "<code>";
                echo "현재수당 : ".Number_format($mb_balance)."  | 수당한계 :". Number_format($balance_limit).' | ';
                echo "발생할수당: ".Number_format($benefit)." | 지급할수당 :".Number_format($benefit_limit);
                echo "</code><br>";
                if($admin_cash == 1){
                    $rec_adm= 'fall check';
                }
                 */

                $coin_benefit = $benefit/$coin_curency;
                $benefit_limit = clean_coin_format($coin_benefit,2);
                
                $rec=$code.' Recommend Bonus from  '.$mb_id.' | '.$it_name;
                $rec_adm= $it_name.' | '.clean_number_format($it_bonus).'*'.$bonus_rated.'='.clean_number_format($benefit).'('.clean_number_format($coin_benefit,2).')';

                
                echo $recom_id." | ".Number_format($it_bonus).'*'.$bonus_rated;

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

                echo "<span > ▶▶ 지급 수당: ".Number_format($benefit)." 원</span>";
                echo "<span class=blue> ▶▶▶ Core 지급 : ".clean_number_format($coin_benefit,2)." core</span><br>";


                if($benefit > 0 && $benefit_limit > 0){

                    $record_result = soodang_record($recom_id, $code, $benefit_limit,$rec,$rec_adm,$bonus_day,$mb_no,$mb_level,$mb_name,$coin_curency);

                    if($record_result){
                        $balance_up = "update g5_member set mb_balance = mb_balance + {$benefit_limit}  where mb_id = '".$recom_id."'";

                        // 디버그 로그
                        if($debug){
                            echo "<code>";
                            print_R($balance_up);
                            echo "</code>";
                        }else{
                            sql_query($balance_up);
                        }
                    }
                }
            }
        } // while
    } // for
}
?>

<?include_once('./bonus_footer.php');?>

<?
if($debug){}else{
    $html = ob_get_contents();
    //ob_end_flush();
    $logfile = G5_PATH.'/data/log/'.$code.'/'.$code.'_'.$bonus_day.'.html';
    fopen($logfile, "w");
    file_put_contents($logfile, ob_get_contents());
}
?>