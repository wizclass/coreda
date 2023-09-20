<?
include_once('./_common.php');
include_once(G5_THEME_PATH.'/_include/wallet.php');
include_once(G5_THEME_PATH.'/_include/gnb.php');


// login_check($member['mb_id']);
 
$sql = "select wr_content from g5_write_agreement where wr_id <= 2 order by wr_id asc";
$result = sql_query($sql);

$array = array();

for($i = 0; $i < $row = sql_fetch_array($result); $i++){
    array_push($array,$row);
}

$title = '회원약관';

?>
	
</body>
<style>
    .member_term .contents{font-size:12px;overflow-y: scroll;}
    .footer {position: static; padding: 0 0 20px;}
</style>
<main>
	<div class="container member_term pt20">
		<p class="mb-2">서비스 이용약관</p>
        <div class="white mb-4 contents"><?=conv_content($array[0]['wr_content'],2)?></div>
        <p class="mb-2">개인정보 수집 및 이용약관</p>
        <div class="white mb-4 contents"><?=conv_content($array[1]['wr_content'],2)?></div>
	</div>    
    <div class='footer'>
        <p class='company'> <?=CONFIG_SUB_TITLE?> <br>이메일 : <?=$config['cf_admin_email']?></p>
        <p class='copyright'>Copyright ⓒ 2023. <?=CONFIG_TITLE?> Co. ALL right reserved.</p>
    </div>
</main>




<? include_once(G5_THEME_PATH.'/_include/tail.php'); ?>