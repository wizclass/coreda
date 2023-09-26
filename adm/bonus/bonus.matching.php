<?php

$sub_menu = "600200";
include_once('./_common.php');
include_once('./bonus_inc.php');

auth_check($auth[$sub_menu], 'r');

$debug = 1;


// 직추천 수당

$bonus_row = bonus_pick($code);

$bonus_limit = $bonus_row['limited']/100;
$bonus_rate = $bonus_row['rate']*0.01;

$bonus_condition = $bonus_row['source'];
$bonus_condition_tx = bonus_condition_tx($bonus_condition);

$bonus_layer = $bonus_row['layer'];
$bonus_layer_tx = bonus_layer_tx($bonus_layer);

//회원 리스트를 읽어 온다.
$sql_common = " FROM g5_shop_order AS o, g5_member AS m ";
$sql_search=" WHERE o.mb_id=m.mb_id AND od_date ='".$bonus_day."'";
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

ob_start();

// 설정로그 
echo "<strong>".strtoupper($code)." 지급비율 : ". $bonus_row['rate']."%   </strong> |    지급조건 :".$pre_condition.' | '.$bonus_condition_tx." | ".$bonus_layer_tx."<br>";
echo "<strong>".$bonus_day."</strong><br>";
echo "<br><span class='red'> 기준대상자(매출발생자) : ".$result_cnt."</span><br><br>";
echo "<div class='btn' onclick='bonus_url();'>돌아가기</div>";

?>

<html><body>
<header>정산시작</header>    
<div>
<?

$price_cond=", SUM(pv) AS hap";

$sql = "SELECT DATE_FORMAT(o.od_time,'%Y-%m-%d') AS od_time, m.mb_id, m.mb_recommend,m.mb_name
            $price_cond 
            {$sql_common}
            {$sql_search}
            {$sql_mgroup}";
$result = sql_query($sql);

// 디버그 로그 
if($debug){
	echo "<code>";
    print_r($sql);
	echo "</code><br>";
}


$history_cnt=0;
$rec='';

excute();

function  excute(){

    global $result;
    global $g5, $bonus_day, $bonus_condition, $code, $bonus_rate,$pre_condition_in,$bonus_limit,$bonus_layer ;
    global $debug;


for ($i=0; $row=sql_fetch_array($result); $i++) {   
    $today_sales2=0;
    $confirm_exit1=0;
    $today=$row['od_time'];

    $comp=$row['mb_id'];
    $first=0; 
    $firstname='';
    $firstid='';
    $today_sales=$row['hap'];

    echo "<br><br><span class='title' style='font-size:30px;'>".$comp."</span><br>";

    while(  ($comp!='admin')  || ($comp != $config['cf_admin']) ){   
        $sql = " SELECT mb_no, mb_id, mb_name,grade,mb_level, mb_balance, mb_recommend, mb_brecommend, mb_deposit_point FROM g5_member WHERE mb_id= '{$comp}' ";
        $recommend = sql_fetch($sql);

        $mb_no=$recommend['mb_no'];
        $mb_id=$recommend['mb_id'];
        $mb_name=$recommend['mb_name'];
        $mb_level=$recommend['mb_level'];
        $mb_deposit=$recommend['mb_deposit_point'];
        $mb_balance=$recommend['mb_balance'];
        $grade=$recommend['grade'];


        // 추천, 후원 조건
        if($bonus_condition < 2){
            $recom=$recommend['mb_recommend'];
        }else{
            $recom=$recommend['mb_brecommend'];
        }

        // 관리자 제외
        if($mb_id != 'admin' && $mb_level > 9 ){ break;} 
            
            if( $history_cnt==0 ){ // 본인
                $firstname=$mb_name;
                $firstid=$mb_id;

                $sql_sale = " update g5_member set sales_day= '{$today}' where mb_id= '{$mb_id}' "; // 마지막 매출일
                sql_query($sql_sale);

            }else if($history_cnt <= $bonus_layer){                 // 본인 제외 - 지정대수까지
            
                if($pre_condition_in){	

                $hist = $history_cnt-1;	
                $benefit=($today_sales*$bonus_rate);// 매출자 * 수당비율

                $balance_limit = $bonus_limit * $mb_deposit; // 수당한계선
                $benefit_limit = $mb_balance + $benefit; // 수당합계
                
                $rec=$code.' Bonus from '.$firstid.'('. $firstname.') :: step : '.$history_cnt.')';
                $rec_adm= ''.$firstid.' - '.$history_cnt.'대 :'.$today_sales.'*'.number_format($bonus_rate,5).'='.$benefit;


                // 디버그 로그
                if($debug){
                    echo "<code>";
                    echo "현재수당 : ".$mb_balance."  | 수당한계 :". $balance_limit;
                    echo "</code><br>";
                }
                
                if($benefit_limit > $balance_limit){
                    $benefit_limit = $balance_limit;
                    $rec_adm = "benefit overflow";
                    echo " <span class=red> ▶▶ 수당 초과 (한계까지만 지급)".$benefit_limit." </span><br>";
                    
                }else{

                    // 수당 로그
                    echo $mb_id." | ".$history_cnt." 단계 :: ".$today_sales.'*'.$bonus_rate;
                    echo "<span class=blue> ▶▶▶ 수당 지급 : ".$benefit."</span><br>";
                }
 
                

                //**** 수당이 있다면 함께 DB에 저장 한다.
                $bonus_sql = " insert `{$g5['bonus']}` set day='".$bonus_day."'";
                $bonus_sql .= " ,mb_no			= ".$mb_no;
                $bonus_sql .= " ,mb_id			= '".$mb_id."'";
                $bonus_sql .= " ,mb_name		= '".$mb_name."'";
                $bonus_sql .= " ,mb_level      = ".$mb_level;
                $bonus_sql .= " ,grade      = ".$grade;
                $bonus_sql .= " ,allowance_name	= '".$code."'";
                $bonus_sql .= " ,benefit		=  ".$benefit;	
                $bonus_sql .= " ,rec			= '".$rec."'";
                $bonus_sql .= " ,rec_adm		= '".$rec_adm."'";
                $bonus_sql .= " ,origin_balance	= '".$mb_balance."'";
                $bonus_sql .= " ,origin_deposit	= '".$mb_deposit."'";
                $bonus_sql .= " ,datetime		= '".date("Y-m-d H:i:s")."'";


                // 디버그 로그
                if($debug){
                    echo "<code>";
                    print_R($bonus_sql);
                    echo "</code>";
                }else{
                    sql_query($bonus_sql);
                }


                $balance_up = "update g5_member set mb_balance = '".$benefit_limit."'  where mb_id = '".$mb_id."'";

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

        $rec='';
        $comp=$recom;
        $history_cnt++;
    } // while

    $history_cnt=0;
    $today_sales=0;
    
    }
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