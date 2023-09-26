<?php
define('G5_IS_ADMIN', true);
define('G5_IS_SHOP_ADMIN_PAGE', true);
include_once ('../../common.php');


include_once(G5_ADMIN_PATH.'/admin.lib.php');
include_once('./admin.shop.lib.php');

check_order_inicis_tmps();