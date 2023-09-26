<?php
if($member['mb_id'] == 'admin' && APP_MSG_USE){
	$menu["menu800"] = array (
		array('800000', '앱/푸쉬관리', ''.G5_ADMIN_URL.'/app_push/fcm_memberlist.php', '', 1),
		// array('800000', '앱/푸쉬관리', ''.G5_ADMIN_URL.'/app_push/fcm_manager.php', '0'),
		array('800000', '앱/푸쉬관리', G5_ADMIN_URL.'/app_push/fcm_memberlist.php', '', 1),
		array('800200', '메세지관리', G5_ADMIN_URL.'/app_push/fcm_msg.php', '', 1),
		array('800300', '전송내역', G5_ADMIN_URL.'/app_push/fcm_send_list.php', '', 1),
	);
}
?>