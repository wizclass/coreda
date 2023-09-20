<?php 
// include_once(G5_SMS5_PATH.'/sms5.lib.php');
include_once(G5_LIB_PATH.'/icode.sms.lib.php');
$sms_content = "[화조글로벌에셋] 본인확인 인증번호[{$rand_num}]를 화면에 입력해주세요.";
$recv_number = str_replace("-","",$mb_hp);
$SMS = new SMS;
$SMS -> SMS_con($config['cf_icode_server_ip'], $config['cf_icode_id'], $config['cf_icode_pw'], $config['cf_icode_server_port']);
$SMS -> Add($recv_number, $config['cf_1'], $config['cf_icode_id'], iconv("utf-8", "euc-kr", stripslashes($sms_content)), "");
$SMS->Send();
?>