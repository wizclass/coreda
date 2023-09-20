<?php
// 이 파일은 새로운 파일 생성시 반드시 포함되어야 함
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

$begin_time = get_microtime();

if (!isset($g5['title'])) {
    $g5['title'] = $config['cf_title'];
    $g5_head_title = $g5['title'];
}
else {
    $g5_head_title = $g5['title']; // 상태바에 표시될 제목
    $g5_head_title .= " | ".$config['cf_title'];
}

$g5['title'] = strip_tags(get_text($g5['title']));
$g5_head_title = strip_tags(get_text($g5_head_title));

// 현재 접속자
// 게시판 제목에 ' 포함되면 오류 발생
$g5['lo_location'] = addslashes($g5['title']);
if (!$g5['lo_location'])
    $g5['lo_location'] = addslashes(clean_xss_tags($_SERVER['REQUEST_URI']));
$g5['lo_url'] = addslashes(clean_xss_tags($_SERVER['REQUEST_URI']));
if (strstr($g5['lo_url'], '/'.G5_ADMIN_DIR.'/') || $is_admin == 'super') $g5['lo_url'] = '';

/*
// 만료된 페이지로 사용하시는 경우
header("Cache-Control: no-cache"); // HTTP/1.1
header("Expires: 0"); // rfc2616 - Section 14.21
header("Pragma: no-cache"); // HTTP/1.0
*/
?>

<!doctype html>
<html lang="ko">
<head>

<meta charset="utf-8">
<?php
if (G5_IS_MOBILE) {
    echo '<meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=0,maximum-scale=10,user-scalable=yes">'.PHP_EOL;
    echo '<meta name="HandheldFriendly" content="true">'.PHP_EOL;
    echo '<meta name="format-detection" content="telephone=no">'.PHP_EOL;
} else {
    echo '<meta http-equiv="imagetoolbar" content="no">'.PHP_EOL;
    echo '<meta http-equiv="X-UA-Compatible" content="IE=edge">'.PHP_EOL;
}

if($config['cf_add_meta'])
    echo $config['cf_add_meta'].PHP_EOL;
?>
<title><?php echo $g5_head_title; ?></title>
	<!--<link rel="stylesheet" href="<?php echo G5_THEME_CSS_URL; ?>/<?php echo G5_IS_MOBILE ? 'mobile' : 'default'; ?>.css?ver=<?php echo G5_CSS_VER; ?>">-->

	<link rel="shortcut icon" href="/favicon.ico" />
	<link rel="icon" href="/favicon.ico">
	<meta name="Robots" content="ALL">

	<!-- 기본 공유 설정 //-->
	<meta name="title" content="V7 WALLET" />
	<meta name="subject" content="V7 WALLET" />
	<meta name="keywords" content="V7 WALLET" />
	<meta name="description" content="V7 WALLET" />
	<link rel="image_src" href="" />
	<!--대표 이미지 URL (이미지를 여러 개 지정할 수 있음) //-->
	<meta name="apple-mobile-web-app-title" content="" />
	<meta name="format-detection" content="telephone=no" />

	<!-- 페이스북 공유 + 카카오톡 설정 //-->
	<meta property="fb:app_id" content="" />
	<meta property="og:type" content="website" />
	<meta property="og:title" content="V7 WALLET" />
	<meta property="og:description" content="V7 WALLET" />
	<meta property="og:site_name" content="V7 WALLET" />
	<meta property="og:image" content="" />
	<meta property="og:url" content="" />

	<!-- 트위터 공유 설정 //-->
	<meta name="twitter:card" content="summary" /><!-- 트위터 카드 summary는 웹페이지에 대한 요약정보를 보여주는 카드로 우측에 썸네일을 보여주고 그 옆에 페이지의 제목과 요약 내용을 보여준다.//-->
	<meta name="twitter:url" content="" />
	<meta name="twitter:title" content="V7 WALLET" />
	<meta name="twitter:description" content="V7 WALLET" />
	<meta name="twitter:image" content="" />
	<meta name="twitter:site" content="" /><!--  트위터 카드에 사이트 배포자 트위터아이디 //-->
	<meta name="twitter:creator" content="" /><!--  트위터 카드에 배포자 트위터아이디//-->

	<!-- 네이트온 공유 설정 //-->
	<meta name="nate:title" content="V7 WALLET" />
	<meta name="nate:description" content="V7 WALLET" />
	<meta name="nate:site_name" content="V7 WALLET" />
	<meta name="nate:url" content="" />
	<meta name="nate:image" content="" />


	<!-- 기본 설정 //-->
	<link href= "<?=G5_THEME_URL?>/_common/css/normalize.css" rel="stylesheet">
	<link href="<?=G5_THEME_URL?>/_common/css/jquery-ui.min.css" rel="stylesheet">
	<link href="<?=G5_THEME_URL?>/_common/css/gnb.css?ver=20220502" rel="stylesheet">
	<link href="<?=G5_THEME_URL?>/_common/css/common.css?ver=20200429" rel="stylesheet">


	<!-- JQuery  기본 설정 //-->
	<script src="http://code.jquery.com/jquery-latest.min.js"></script>
	<script src="<?=G5_THEME_URL?>/_common/js/jquery-ui.min.js"></script>
	<script src="<?=G5_THEME_URL?>/_common/js/common.js"></script>
	<script src="<?=G5_THEME_URL?>/_common/js/gnb.js"></script>
	

	<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.0/normalize.css">
	<!-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous"> -->
	<link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">
	<!--
	<link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css" integrity="sha384-hWVjflwFxL6sNzntih27bfxkr27PmbbK/iSvJ+a4+0owXq79v+lsFkW54bOGbiDQ" crossorigin="anonymous">
	-->

	<!-- <script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous"></script> -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
	<script src='/js/jquery-captcha.min.js'></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/i18next/1.9.0/i18next.min.js" type="text/javascript"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/js-sha256/0.9.0/sha256.js" type="text/javascript"></script>

	<!--[if lte IE 8]>
	<script src="<?php echo G5_JS_URL ?>/html5.js"></script>
	<![endif]-->
	<script>
	// 자바스크립트에서 사용하는 전역변수 선언
	var g5_url       = "<?php echo G5_URL ?>";
	var g5_bbs_url   = "<?php echo G5_BBS_URL ?>";
	var g5_is_member = "<?php echo isset($is_member)?$is_member:''; ?>";
	var g5_is_admin  = "<?php echo isset($is_admin)?$is_admin:''; ?>";
	var g5_is_mobile = "<?php echo G5_IS_MOBILE ?>";
	var g5_bo_table  = "<?php echo isset($bo_table)?$bo_table:''; ?>";
	var g5_sca       = "<?php echo isset($sca)?$sca:''; ?>";
	var g5_editor    = "<?php echo ($config['cf_editor'] && $board['bo_use_dhtml_editor'])?$config['cf_editor']:''; ?>";
	var g5_cookie_domain = "<?php echo G5_COOKIE_DOMAIN ?>";
	</script>

	<!--<script src="<?php echo G5_JS_URL ?>/jquery-1.8.3.min.js"></script>-->
	<script src="<?php echo G5_JS_URL ?>/jquery.menu.js?ver=<?php echo G5_JS_VER; ?>"></script>
	<script src="<?php echo G5_JS_URL ?>/common.js?ver=<?php echo G5_JS_VER; ?>"></script>
	<script src="<?php echo G5_JS_URL ?>/wrest.js?ver=<?php echo G5_JS_VER; ?>"></script>
	<script src="<?php echo G5_JS_URL ?>/placeholders.min.js"></script>
	<!--<link rel="stylesheet" href="<?php echo G5_JS_URL ?>/font-awesome/css/font-awesome.min.css">-->

	<?php
	if(G5_IS_MOBILE) {
		echo '<script src="'.G5_JS_URL.'/modernizr.custom.70111.js"></script>'.PHP_EOL; // overflow scroll 감지
	}

	if(!defined('G5_IS_ADMIN'))
		echo $config['cf_add_script'];
	?>
</head>

<body <?php echo isset($g5['body_script']) ? $g5['body_script'] : ''; ?> >
<?php
if ($is_member) { // 회원이라면 로그인 중이라는 메세지를 출력해준다.
    $sr_admin_msg = '';
    if ($is_admin == 'super') $sr_admin_msg = "최고관리자 ";
    else if ($is_admin == 'group') $sr_admin_msg = "그룹관리자 ";
    else if ($is_admin == 'board') $sr_admin_msg = "게시판관리자 ";
	/*
    echo '<div id="hd_login_msg">'.$sr_admin_msg.get_text($member['mb_nick']).'님 로그인 중 ';
    echo '<a href="'.G5_BBS_URL.'/logout.php">로그아웃</a></div>';
	*/
}
?>
