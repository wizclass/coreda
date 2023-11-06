<?php
$sub_menu = "200100";
include_once('./_common.php');
include_once(G5_THEME_PATH . '/_include/wallet.php');
include_once(G5_PATH . '/util/package.php');

auth_check($auth[$sub_menu], 'r');

$get_shop_item = get_g5_item();

if ($_GET['mode'] == 'del') {
	$sql_target = 'g5_member_del';
	$mode = 'del';
} else {
	$sql_target = 'g5_member';
}

$sub_sql = "";

if ($_GET['sst'] == "total_fund") {
	$sub_sql = " , (mb_deposit_point + mb_deposit_calc + mb_balance - mb_shift_amt) as total_fund";
}

if ($_GET['sst'] == "deposit_point") {
	$sub_sql = " , (mb_deposit_point) as deposit_point";
}

if ($_GET['sst'] == "mb_bonus_total") {
	$sub_sql = " , ((recom_mining + brecom_mining + brecom2_mining + super_mining)) as mb_bonus_total";
}

if ($_GET['sst'] == "mb_bonus_total_rate") {
	$sub_sql = " , ((recom_mining + brecom_mining + brecom2_mining + super_mining)/mb_rate) as mb_bonus_total_rate";
}

/* if ($_GET['sst'] == "mining") {
	$sub_sql = " , ($mining_target - $mining_amt_target) as mining";
} */




$sql_common = " {$sub_sql} from {$sql_target} ";

$sql_search = " where (1) ";
if ($stx) {
	$sql_search .= " and ( ";
	switch ($sfl) {
		case 'mb_point':
			$sql_search .= " ({$sfl} >= '{$stx}') ";
			break;
		case 'mb_level':
			$sql_search .= " ({$sfl} = '{$stx}') ";
			break;
		case 'mb_tel':
		case 'mb_hp':
			$sql_search .= " ({$sfl} like '%{$stx}%') ";
			break;

		default:
			$sql_search .= " ({$sfl} like '%{$stx}%') ";
			break;
	}
	$sql_search .= " ) ";
}

if ($_GET['level']) {
	$sql_search .= " and mb_level = " . $_GET['level'];
}



if ($_GET['nation']) {
	$sql_search .= " and nation_number = " . $_GET['nation'];
}

if ($_GET['block']) {
	$sql_search .= " and mb_block = 1 ";
}

if (!$sst) {
	$sst = "mb_datetime, mb_no";
	$sod = "desc";
}

if ($_GET['grade'] > -1) {
	$sql_search .= " and grade = " . $_GET['grade'];
}

if ($member['mb_id'] != 'admin') {
	$sql_search .= " AND mb_id != 'admin' ";
}

$sql_order = " order by {$sst} {$sod}";
$sql = " select count(*) as cnt {$sql_common} {$sql_search} {$sql_order} ";

$row = sql_fetch($sql);
$total_count = $row['cnt'];

// $rows = $config['cf_page_rows'];
if ($_GET['range'] == 'all') {
	$range = $total_count;
} else {
	$range = 50;
}
$rows = $range;

$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함


// 탈퇴회원수
$sql = " select count(*) as cnt {$sql_common} {$sql_search} and mb_leave_date <> '' {$sql_order} ";
$row = sql_fetch($sql);
$leave_count = $row['cnt'];


// 차단회원수
$sql = " select count(*) as cnt {$sql_common} {$sql_search} and mb_intercept_date <> '' {$sql_order} ";
$row = sql_fetch($sql);
$intercept_count = $row['cnt'];

$listall = '<a href="' . $_SERVER['SCRIPT_NAME'] . '" class="ov_listall" style="margin: 0px 12px 0px 0px">전체목록</a>';

$excel_down = true;

$g5['title'] = '회원관리';
include_once('./admin.head.php');

$sql = " select * {$sql_common} {$sql_search} {$sql_order} limit {$from_record}, {$rows} ";

$result = sql_query($sql);
$colspan = 17;


/* 레벨 */
$grade = "SELECT grade, count( grade ) as cnt FROM {$sql_target} GROUP BY grade order by grade";
$get_lc = sql_query($grade);

/* 국가 */
$nation_sql = "SELECT nation_number, count( nation_number ) as cnt FROM {$sql_target} GROUP BY nation_number";
$nation_row = sql_query($nation_sql);

$blockRec = sql_fetch("select count(mb_block) as cnt from {$sql_target} where mb_block = 1");



function mining_bonus_rate($mb_id, $mb_rate)
{
	// $member['recom_mining'] + $member['brecom_mining'] + $member['brecom2_mining'] + $member['super_mining']
	global $sql_target;

	$bonus_total_sql = "SELECT SUM(recom_mining + brecom_mining + brecom2_mining + super_mining) as total FROM {$sql_target} WHERE mb_id = '{$mb_id}' ";

	$bonus_total = sql_fetch($bonus_total_sql)['total'];

	if ($mb_rate > 0) {
		$bonus_rate_per =  Number_format($bonus_total / $mb_rate);
	} else {
		$bonus_rate_per =  0;
	}
	return array($bonus_total, $bonus_rate_per);
}

function active_check($val, $target)
{
	$bool_check = $_GET[$target];
	if ($bool_check == $val) {
		return " active ";
	}
}

function out_check($val)
{
	$bonus_OUT_CALC = $val;

	if ($bonus_OUT_CALC > 100) {
		$class = 'over';
	} else {
		$class = '';
	}
	return "<span class=" . $class . ">" . number_format($bonus_OUT_CALC) . " % </span>";
}

// 통계수치
$stats_sql = "SELECT COUNT(*) as cnt, 
SUM(mb_deposit_point) AS deposit, 
SUM(mb_balance) AS balance,
SUM(mb_save_point) AS pv, 
SUM(mb_deposit_point + mb_deposit_calc + mb_balance ) AS able_with
{$sql_common} {$sql_search}";

/* echo "<br>";
echo "=====================";
print_R($stats_sql);
echo "=====================";
echo "<br>";
$stats_result = sql_fetch($stats_sql); */
?>


<style>
	.local_ov strong {
		color: red;
		font-weight: 600;
	}

	.local_ov {
		color: #777;
		font-weight: 500;
		line-height: 20px;
	}

	.local_ov a {
		margin-left: 20px;
	}

	.local_ov span {
		margin-left: 10px;
		padding-right: 5px;
	}

	.local_ov .bonus {
		margin-top: 5px;
		border-left: 3px solid green;
		background: white;
		display: inline-block;
		padding: 3px 5px;
		border-radius: 5px;
		color: black;
		box-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
	}

	.local_ov .bonus.mining {
		border-left: 3px solid purple;
		margin-left: 10px;
	}

	.local_ov .bonus.mining.before {
		border-left: 3px solid blue;
		margin-left: 10px;
	}

	select#sfl {
		padding: 9px 10px;
	}

	#stx {
		padding: 5px;
	}

	.local_sch .btn_submit {
		background: #ff3061 !important;
		color: #fff;
		font-size: 0.95em;
		width: 80px;
		height: 33px;
		text-indent: 0px;
	}

	.f_blue {
		color: blue !important;
		font-weight: 600
	}

	.icon,
	.icon img {
		width: 26px;
		height: 26px;
		font-size: 18px;
	}

	.badge.over {
		position: absolute;
		padding: 2px 5px;
		background: #eee;
		font-size: 12px;
		margin-left: -10px;
		margin-top: 12px;
		display: inline-block;
		font-weight: 600;
		color: black;
	}

	.icon {
		display: inline-block;
		vertical-align: bottom;
	}

	.icon i {
		vertical-align: -webkit-baseline-middle;
	}

	#member_depth {
		background: lightskyblue
	}

	#member_depth:hover {
		background: black;
		color: white;
	}

	.mem_icon {
		width: 20px;
		height: 20px;
		margin-right: 5px;
	}

	.area {
		display: inline-block;
		margin-right: 20px;
		vertical-align: middle
	}

	.area span {
		cursor: pointer
	}

	.area span:hover {
		text-decoration: underline
	}

	.area.nation {
		border-right: 3px solid black;
		padding-right: 20px;
	}

	.nation_item {
		display: inline-block;
		padding: 5px 10px;
		border: 1px solid #c8ced1;
		background: #d6dde1;
		text-decoration: none;
	}

	.nation_item:hover {
		background: #3e4452;
		color: white;
	}

	.nation_item.active {
		background: #f9a62e;
		border: 1px solid #f9a62e;
		color: black;
	}

	.nation_icon {
		vertical-align: bottom;
		margin-right: 3px;
	}

	.total {
		background: #555 !important;
		color: white !important;
	}

	.bonus_total {
		background: teal !important;
		color: white !important;
	}

	.bonus_usdt {
		background: crimson !important;
		color: white !important;
	}

	.bonus_usdt a {
		color: white !important;
		font-weight: 300
	}

	.bonus_eth {
		background: #0062cc !important;
		color: white !important;
	}

	.bonus_aa {
		background: yellowgreen !important
	}

	.bonus_bb {
		background: skyblue !important
	}

	.bonus_bb.bonus_out {
		background: deepskyblue !important
	}

	.bonus_bb.green {
		background: green !important;
		color: white
	}

	.bonus_bb.bonus_benefit {
		background: gold !important
	}

	.bonus_calc {
		background: #3e1f9c !important;
	}

	.bonus_calc a {
		color: white !important;
		font-weight: 400
	}

	.td_mbgrade select {
		min-width: 50px;
		padding: 5px 5px;
		color: #777;
	}

	.tbl_head02 tbody {
		color: #777;
	}

	.tbl_head02 tbody td {
		padding: 5px;
	}

	.over {
		color: red;
	}

	.td_mngsmall a {
		border: 1px solid #ccc;
		padding: 3px 10px;
		display: inline-block;
		text-decoration: none;
	}

	.td_mngsmall a:hover {
		background: black;
		border: 1px solid black;
		color: white;
	}

	.labelM {
		text-align: left;
	}

	.red {
		color: red;
		font-weight: 600;
	}

	.btn_add01 {
		padding-bottom: 10px;
		border-bottom: 1px solid #bbb
	}

	.center {
		text-align: center !important;
	}

	.td_mail {
		font-size: 11px;
		letter-spacing: -0.5px;
	}

	.td_name {
		min-width: 60px;
		width: 88px;
	}

	.bonus_eth a {
		color: white !important
	}

	.name {
		color: #777;
		font-size: 11px;
	}

	.icon,
	.icon img {
		width: 26px;
		height: 26px;
	}

	.grade_icon {
		width: 22px;
		height: 22px;
		opacity: 0.75;
	}

	.badge.over {
		position: absolute;
		padding: 2px 5px;
		background: #eee;
		font-size: 12px;
		margin-left: -10px;
		margin-top: 12px;
		display: inline-block;
		font-weight: 600;
		color: black;
	}

	.strong {
		font-weight: 600;
		color: black;
	}

	.td_mbstat {
		text-align: right;
		padding-right: 10px !important;
		font-size: 12px;
		width: 75px;
		color: #333;
	}

	.td_index {
		min-width: 40px;
		width: 40px;
		text-align: center !important;
	}

	.td_grade {
		width: 30px;
		min-width: 30px;
	}

	.user_icon i {
		vertical-align: -webkit-baseline-middle !important;
	}

	.no-swap {
		display: block;
		color: #bbb;
		font-size: 11px;
		font-weight: 300;
		width: 100%;
		margin: 0;
		padding: 0;
		line-height: 10px;
	}

	.bg0 {
		background: #fff
	}

	.bg1 {
		background: #f2f5f9
	}

	tbody td {
		border: 1px solid #ececec;
	}

	.badge {
		border-radius: 0.5rem;
		padding: 5px 8px;
		margin-left: 5px;
	}

	.local_ov01 {
		position: relative;
		padding: 10px 20px;
		border-bottom: 1px solid #e9e9e9;
		background: #f2f5f9;
	}
</style>
<link rel="stylesheet" href="<?= G5_THEME_URL ?>/css/scss/custom.css">
<style>

</style>

<div class="local_ov01 local_ov">
	<div style="display: flex; align-items: center">
		<?php echo $listall ?>
		총회원수 <strong><?php echo number_format($total_count) ?></strong>명|
		<?

		echo "<span >총 입금 합계 <strong>" . Number_format($stats_result['deposit']) . " " . $curencys[0] . " </strong></span> | ";
		echo "<span>총 매출(pv) 합계 <strong>" . Number_format($stats_result['pv']) . "</strong></span><br> ";
		?>
	</div>
	<?
	echo "<div class='bonus'>보너스<span> 보유량 : <strong>" . Number_format($stats_result['balance']) . " " . $curencys[1] . " </strong></span> | ";
	echo "<span>출금 가능 : <span class='f_blue'>" . Number_format($stats_result['balance']) . " " . $curencys[1] . "  </span></span></div>  ";

	// echo "<div class='bonus mining before'>미변환 <strong>".strtoupper($minings[$before_mining_coin])."</strong><span>보유량 : <strong>" . Number_format($stats_result['B1'], 8) .' '.strtoupper($minings[$before_mining_coin])." </strong></span> | ";
	// echo "<span>변환 가능 : <span class='f_blue'>" . Number_format($stats_result['B2'], 8) .' '.strtoupper($minings[$before_mining_coin])."  </span></span></div> ";
	// echo "<div class='bonus mining before'>미변환 <span class='f_blue'>".strtoupper($minings[$before_mining_coin])."</span><span>보유량 : <strong>" . Number_format($stats_result['B2'], 8) .' '.strtoupper($minings[$before_mining_coin])." </strong></span></div>";


	?>


	<!-- <a href="?sst=mb_intercept_date&amp;sod=desc&amp;sfl=<?php echo $sfl ?>&amp;stx=<?php echo $stx ?>">
	차단 <?php echo number_format($intercept_count) ?></a>명,
	<a href="?sst=mb_leave_date&amp;sod=desc&amp;sfl=<?php echo $sfl ?>&amp;stx=<?php echo $stx ?>">탈퇴 <?php echo number_format($leave_count) ?></a>명,
	<a href="?block=1">
		지급차단 <?php echo number_format($blockRec['cnt']) ?>명
	</a> -->
</div>

<form id="fsearch" name="fsearch" class="local_sch01 local_sch" method="get">
	<label for="sfl" class="sound_only">검색대상</label>
	<select name="sfl" id="sfl" style="height: 36px">
		<option value="mb_id" <?php echo get_selected($_GET['sfl'], "mb_id"); ?>>회원아이디</option>
		<!-- <option value="mb_nick"<?php echo get_selected($_GET['sfl'], "mb_nick"); ?>>닉네임</option> -->
		<option value="mb_name" <?php echo get_selected($_GET['sfl'], "mb_name"); ?>>이름</option>
		<!-- <option value="mb_level"<?php echo get_selected($_GET['sfl'], "mb_level"); ?>>권한</option>
		<option value="mb_email"<?php echo get_selected($_GET['sfl'], "mb_email"); ?>>E-MAIL</option> -->
		<!-- <option value="mb_tel"<?php echo get_selected($_GET['sfl'], "mb_tel"); ?>>전화번호</option> -->
		<option value="mb_hp" <?php echo get_selected($_GET['sfl'], "mb_hp"); ?>>휴대폰번호</option>
		<!-- <option value="mb_point"<?php echo get_selected($_GET['sfl'], "mb_point"); ?>>PV</option> -->
		<option value="mb_datetime" <?php echo get_selected($_GET['sfl'], "mb_datetime"); ?>>가입일시</option>
		<option value="mb_ip" <?php echo get_selected($_GET['sfl'], "mb_ip"); ?>>IP</option>
		<option value="mb_recommend" <?php echo get_selected($_GET['sfl'], "mb_recommend"); ?>>추천인</option>
		<!-- <option value="mb_wallet"<?php echo get_selected($_GET['sfl'], "mb_wallet"); ?>>지갑</option> -->
	</select>

	<label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
	<input type="text" name="stx" value="<?php echo $stx ?>" id="stx" required class="required frm_input" style="height: 36px">
	<input type="submit" class="btn_submit" value="검색">
</form>



<div style="padding:8px 20px 10px;font-size:15px;margin-bottom:10px;float:left">
	<link rel="stylesheet" href="./css/scss/admin_custom.css?ver=220419">

	<form name="search_bar" id="search_bar" action="./member_list.php" method="get">
		<input type='hidden' name='nation' id='nation' value='' />
		<input type='hidden' name='level' id='level' value='' />
		<input type='hidden' name='grade' id='grade' value='' />
		<!-- <div class="area nation">
		<?
		while ($row = sql_fetch_array($nation_row)) {
			if ($row['nation_number'] == '1') {
				echo "<span onclick='nation_search(1);' class='nation_item " . active_check(1, 'nation') . "'><img src='./img/contry_3.png' class='nation_icon'/>" . $row['cnt'] . " </span> ";
			} else if ($row['nation_number'] == '62') {
				echo "<span onclick='nation_search(62);' class='nation_item " . active_check(62, 'nation') . "'> indo " . $row['cnt'] . " </span> ";
			} else if ($row['nation_number'] == '63') {
				echo "<span onclick='nation_search(63);' class='nation_item " . active_check(63, 'nation') . "'> Phil " . $row['cnt'] . " </span> ";
			} else if ($row['nation_number'] == '66') {
				echo "<span onclick='nation_search(66);' class='nation_item " . active_check(66, 'nation') . "'> THailand " . $row['cnt'] . " </span> ";
			} else if ($row['nation_number'] == '81') {
				echo "<span onclick='nation_search(81);' class='nation_item " . active_check(81, 'nation') . "'> JAPAN " . $row['cnt'] . " </span> ";
			} else if ($row['nation_number'] == '82') {
				echo "<span onclick='nation_search(82);' class='nation_item " . active_check(82, 'nation') . "'><img src='./img/contry_1.png' class='nation_icon'/>" . $row['cnt'] . " </span> ";
			} else {
				echo "<span onclick='nation_search(0);' class='nation_item  " . active_check(0, 'nation') . "'> ETC " . $row['cnt'] . " </span> ";
			}
		}
		?>
	</div> -->

		<!-- <div class="area level">
	<? while ($l_row = sql_fetch_array($get_lc)) {

		if ($l_row['grade'] == 6) {
			echo "<span onclick='grade_search(6);'><img src='/img/6.png' class='mem_icon'>" . $l_row['cnt'] . "명</span>";
		} else if ($l_row['grade'] == 5) {
			echo "<span onclick='grade_search(5);'><img src='/img/5.png' class='mem_icon'>" . $l_row['cnt'] . "명</span> | ";
		} else if ($l_row['grade'] == 4) {
			echo "<span onclick='grade_search(4);'><img src='/img/4.png' class='mem_icon'>" . $l_row['cnt'] . "명</span> | ";
		} else if ($l_row['grade'] == 3) {
			echo "<span onclick='grade_search(3);'><img src='/img/3.png' class='mem_icon'>" . $l_row['cnt'] . "명</span> | ";
		} else if ($l_row['grade'] == 2) {
			echo "<span onclick='grade_search(2);'><img src='/img/2.png' class='mem_icon'>" . $l_row['cnt'] . "명</span> | ";
		} else if ($l_row['grade'] == 1) {
			echo "<span onclick='grade_search(1);'><img src='/img/1.png' class='mem_icon'>" . $l_row['cnt'] . "명</span> | ";
		} else if ($l_row['grade'] == 0) {
			echo "<span onclick='grade_search(0);'><img src='/img/0.png' class='mem_icon'>" . $l_row['cnt'] . "명</span> | ";
		}
	} ?>
	</div> -->
	</form>
</div>


<!-- "excel download" -->

<!-- <script src="../excel/tabletoexcel/xlsx.core.min.js"></script>
<script src="../excel/tabletoexcel/FileSaver.min.js"></script>
<script src="../excel/tabletoexcel/tableExport.js"></script> -->




<?php if ($is_admin == 'super') { ?>
	<div class="btn_add01 btn_add">


		<a href="./member_table_depth.php" id="member_depth">회원추천/직추천갱신</a>
		<a href="./member_table_fixtest.php">추천관계검사</a>
		<a href="./del_member_list.php">삭제/탈퇴 회원보기</a>
		<a href="./member_form.php" id="member_add">회원직접추가</a>
		<? if ($range == 'all') { ?>
			<a href="./member_list.php?range=">회원전체보기</a>
		<? } else { ?>
			<a href="./member_list.php?range=all">회원전체보기</a>
		<? } ?>
		<a id="btnExport" data-name='member_info' class="excel" style="padding:10px 10px;">엑셀 다운로드</a>
	</div>
<?php } ?>

<!-- <div style="padding: 20px;font-size:15px;"> -->
<!-- ETH(총자산) = Deposit + 출금구매사용 + 총수당  -->
<?
$i = 0;
while ($l_row = sql_fetch_array($get_lc)) {

	if ($l_row['grade'] == $i) {
		echo $start . " " . $i . "star :" . $l_row['cnt'] . "명 | ";
	}
	++$i;
} ?>
</div>

<!-- <div class="local_desc01 local_desc">
    <p>
		- 인정회원의 정회원 전환은 인정회원 아래의 정회원으로 변경 사용 (표시는 같음).
	</p>
</div> -->

<form name="fmemberlist" id="fmemberlist" action="./member_list_update.php" onsubmit="return fmemberlist_submit(this);" method="post">
	<input type="hidden" name="sst" value="<?php echo $sst ?>">
	<input type="hidden" name="sod" value="<?php echo $sod ?>">
	<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
	<input type="hidden" name="stx" value="<?php echo $stx ?>">
	<input type="hidden" name="page" value="<?php echo $page ?>">
	<input type="hidden" name="token" value="">

	<div class="tbl_head02 tbl_wrap" style="clear:both">
		<table id='table'>
			<caption><?php echo $g5['title']; ?> 목록</caption>

			<thead>
				<tr>
					<th scope="col" rowspan="2" id="mb_list_chk">
						<label for="chkall" class="sound_only">회원 전체</label>
						<input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
					</th>
					<th scope="col" rowspan="2" id="" class="td_chk" style='max-width:30px;width:40px !important;'>등급</th>
					<th scope="col" id="mb_list_authcheck" style='min-width:130px;' rowspan="2"><?php echo subject_sort_link('mb_level', '', 'desc') ?>직급</a></th>
					<th scope="col" rowspan="2" id="mb_list_id" class="td_name center" style="width:150px"><?php echo subject_sort_link('mb_id') ?>아이디</a></th>
					<th scope="col" rowspan="2" id="mb_list_id" class="td_name center" style="width:50px"><?php echo subject_sort_link('mb_name') ?>이름</a></th>
					<!--<th scope="col" rowspan="2"  id="mb_list_cert"><?php echo subject_sort_link('mb_certify', '', 'desc') ?>메일인증확인</a></th>-->
					<? if ($mode == 'del') { ?><th scope="col" rowspan="2" id="mb_list_member" class="td_leave_date"><?php echo subject_sort_link('mb_name') ?>탈퇴일</a></th><? } ?>
					<th scope="col" rowspan="2" id="mb_list_mobile" class="center"><?php echo subject_sort_link('mb_recommend') ?>추천인</th>
					<th scope="col" rowspan="2" id="mb_list_mobile" class="center"><?php echo subject_sort_link('mb_habu_sum') ?>직추천</th>
					<!-- <th scope="col" rowspan="2" id="mb_list_mobile" class="center">후원인</th> -->
					<!-- <th scope="col" rowspan="2" id="mb_list_mobile" class="td_mail">메일주소</th> -->
					<th scope="col" id="mb_list_auth" class="bonus_eth" rowspan="2"><?php echo subject_sort_link('total_fund') ?>현재잔고<br></a></th>
					<th scope="col" id="mb_list_auth2" class="bonus_calc" rowspan="2"><?php echo subject_sort_link('deposit_point') ?>총입금액 <br></th>
					<th scope="col" id="mb_list_auth2" class="bonus_bb" rowspan="2"><?php echo subject_sort_link('mb_deposit_calc') ?>사용금액</th>
					<th scope="col" id="mb_list_auth2" class="bonus_usdt" style='color:white !important' rowspan="2"><?php echo subject_sort_link('mb_shift_amt') ?>출금총액<br>(+수수료)<br></th>
					<!-- <th scope="col" id="mb_list_auth2" class="bonus_bb bonus_calc"  rowspan="2"><?php echo subject_sort_link('deposit_calc') ?>USE <br>출금 및 구매사용</th> -->
					<th scope="col" id="mb_list_auth2" class="bonus_bb bonus_benefit" rowspan="2"><?php echo subject_sort_link('mb_balance') ?> 수당합계</th>
					<th scope="col" id="mb_list_auth2" class="bonus_aa" rowspan="2"><?php echo subject_sort_link('mb_save_point') ?> 누적매출<br>(PV)</th>
					<!-- <th scope="col" id="mb_list_auth2" class="" rowspan="2"><?php echo subject_sort_link('mb_rate') ?>마이닝<br>(MH/s)</th>
					<th scope="col" id="mb_list_auth2" class="bonus_bb green font_white" rowspan="2"> <?php echo subject_sort_link('mining') ?> <span style='color:white'>마이닝보유<br>(<?= $minings[$now_mining_coin] ?>)</span></th>
					<th scope="col" id="mb_list_auth2" class="bonus_aa" style='background:white !important' rowspan="2"><?php echo subject_sort_link('mb_bonus_total') ?>마이닝<br>총보너스 (mh/s)</th>
					<th scope="col" id="mb_list_auth2" class="bonus_aa" style='background:white !important' rowspan="2"><?php echo subject_sort_link('mb_bonus_total_rate') ?>마이닝<br>보너스율 (%)</th> -->
					<th scope="col" rowspan="2" id="" class="item_title" style='min-width:60px;'><?php echo subject_sort_link('rank') ?>상위보유패키지</th>

					<th scope="col" id="mb_list_member"><?php echo subject_sort_link('mb_today_login', '', 'desc') ?>최종접속</a></th>
					<th scope="col" rowspan="3" id="mb_list_mng">관리</th>
				</tr>

				<tr>
					<!--<th scope="col" id="mb_list_mailc"><?php echo subject_sort_link('mb_email_certify', '', 'desc') ?>메일<br>인증</a></th>-->
					<th scope="col" id="mb_list_join"><?php echo subject_sort_link('mb_datetime', '', 'desc') ?>가입일</a></th>
				</tr>

			</thead>

			<tbody>
				<?php
				for ($i = 0; $row = sql_fetch_array($result); $i++) {

					// 접근가능한 그룹수
					$sql2 = " select count(*) as cnt from {$g5['group_member_table']} where mb_id = '{$row['mb_id']}' ";
					$row2 = sql_fetch($sql2);

					$group = '';
					if ($row2['cnt'])
						$group = '<a href="./boardgroupmember_form.php?mb_id=' . $row['mb_id'] . '">' . $row2['cnt'] . '</a>';

					if ($is_admin == 'group') {
						$s_mod = '';
					} else {
						$s_mod = '<a href="./member_form.php?' . $qstr . '&amp;w=u&amp;mb_id=' . $row['mb_id'] . '">회원수정</a>';
						// $s_mod_binary = '<a href="./modify_binary.php?'.$qstr.'&amp;w=u&amp;mb_id='.$row['mb_id'].'">바이너리 수정</a>';

					}
					// $s_grp = '<a href="./boardgroupmember_form.php?mb_id='.$row['mb_id'].'">그룹</a>';

					$leave_date = $row['mb_leave_date'] ? $row['mb_leave_date'] : date('Ymd', G5_SERVER_TIME);
					$divide_date = $row['mb_divide_date'] ? $row['mb_divide_date'] : date('Ymd', G5_SERVER_TIME);
					$intercept_date = $row['mb_intercept_date'] ? $row['mb_intercept_date'] : date('Ymd', G5_SERVER_TIME);

					$mb_nick = get_sideview($row['mb_id'], get_text($row['mb_nick']), $row['mb_email'], $row['mb_homepage']);

					$mb_id = $row['mb_id'];

					$total_deposit = $row['mb_deposit_point'] + $row['mb_deposit_calc'];
					$total_bonus = $row['mb_balance'];
					$total_fund = $total_deposit + $total_bonus - $row['mb_shift_amt'];


					// 보너스 수당 - 한계 
					/* if($row['mb_balance'] != 0 && $row['mb_save_point']!= 0){
						$bonus_per = ($row['mb_balance']/($row['mb_save_point'] * $limited_per));
					} */

					$bonus_per = bonus_per($row['mb_id'], $row['mb_balance'], $row['mb_save_point']);



					$leave_msg = '';
					$intercept_msg = '';
					$intercept_title = '';
					if ($row['mb_leave_date']) {
						$mb_id = $mb_id;
						$leave_msg = '<span class="mb_leave_msg">탈퇴함</span>';
					} else if ($row['mb_intercept_date']) {
						$mb_id = $mb_id;
						$intercept_msg = '<span class="mb_intercept_msg">차단됨</span>';
						$intercept_title = '차단해제';
					}
					if ($intercept_title == '')
						$intercept_title = '차단하기';

					$address = $row['mb_zip1'] ? print_address($row['mb_addr1'], $row['mb_addr2'], $row['mb_addr3'], $row['mb_addr_jibeon']) : '';

					$bg = 'bg' . ($i % 2);

					switch ($row['mb_certify']) {
						case 'hp':
							$mb_certify_case = '휴대폰';
							$mb_certify_val = 'hp';
							break;
						case 'ipin':
							$mb_certify_case = '아이핀';
							$mb_certify_val = '';
							break;
						case 'admin':
							$mb_certify_case = '관리자';
							$mb_certify_val = 'admin';
							break;
						default:
							$mb_certify_case = '&nbsp;';
							$mb_certify_val = 'admin';
							break;
					}

					list($total_mining, $total_mining_rate) = mining_bonus_rate($row['mb_id'], $row['mb_rate'])
				?>


					<tr class="<?php echo $bg; ?>">
						<td headers="mb_list_chk" class="td_chk" rowspan="2">
							<input type="hidden" name="mb_id[<?php echo $i ?>]" value="<?php echo $row['mb_id'] ?>" id="mb_id_<?php echo $i ?>">
							<label for="chk_<?php echo $i; ?>" class="sound_only"><?php echo get_text($row['mb_name']); ?> <?php echo get_text($row['mb_nick']); ?>님</label>
							<input type="checkbox" name="chk[]" value="<?php echo $i ?>" id="chk_<?php echo $i ?>">
						</td>
						<td headers="mb_list_id" rowspan="2" class="td_grade">
							<input type='hidden' name='grade[]' value="<?= $row['grade'] ?>" />
							<? echo "<img src='/img/" . $row['grade'] . ".png' class='grade_icon'>"; ?>
							<div class='badge over'><?= $row['grade'] ?></div>
						</td>

						<td headers="mb_list_member" class="td_mbgrade" rowspan="2" style="width:110px;max-width:110px;">
							<span class='icon'><?= user_icon($row['mb_id'], 'icon') ?></span>
							<?php echo get_member_level_select("mb_level[$i]", 0, $member['mb_level'], $row['mb_level']) ?>
						</td>

						<td headers="mb_list_id" rowspan="2" class="td_name td_id <? if ($row['mb_divide_date'] != '') {
																																				echo 'red';
																																			} ?>" style="min-width:110px; width:auto">
							<?php echo $mb_id ?>
						</td>
						<td rowspan="2" class="td_name name" style='width:70px;'><?php echo get_text($row['mb_name']); ?></td>
						<? if ($mode == 'del') { ?><th scope="col" rowspan="2" class="td_mbstat" style='letter-spacing:0;'><?= $row['mb_leave_date'] ?></th><? } ?>
						<td rowspan="2" class="td_name name" style='width:70px;'><?php echo $row['mb_recommend'] ?></td>
						<td rowspan="2" class="td_name td_index <? if ($row['mb_habu_sum'] >= 2) {
																											echo 'strong';
																										} ?>"><?= $row['mb_habu_sum'] ?></td>

						<td headers="mb_list_auth" class="td_mbstat" rowspan="2"><?= shift_auto($total_fund) ?></td>
						<td headers="mb_list_auth" class="td_mbstat" rowspan="2"><?= shift_auto($row['mb_deposit_point']) ?></td>
						<td headers="mb_list_auth" class="td_mbstat" style='color:red' rowspan="2"><?= shift_auto($row['mb_deposit_calc']) ?></td>
						<td headers="mb_list_auth" class="td_mbstat" style='color:red' rowspan="2"><?= shift_auto($row['mb_shift_amt']) ?></td>
						<td headers="mb_list_auth" class="td_mbstat" rowspan="2"><?= shift_auto($total_bonus) ?></td>
						<td headers="mb_list_auth" class="td_mbstat" rowspan="2"><?= shift_auto($row['mb_save_point']) ?></td>
						<!-- <td headers="mb_list_auth" class="td_mbstat" rowspan="2" style="min-width:50px;width:50px;"><?= shift_auto($row['mb_rate']) ?></td>
						<td headers="mb_list_auth" class="td_mbstat strong" rowspan="2" style="min-width:70px;color:black">
							
							<?= shift_auto_zero(($row[$mining_target] - $row[$mining_amt_target]), $minings[$now_mining_coin]) ?> 
							<? if ($row['swaped'] == 0 && $row[$before_mining_target] > 0) {
								echo "<span class='no-swap'>" . shift_auto_zero(($row[$before_mining_target] - $row[$before_mining_amt_target]), $minings[$before_mining_coin]) . "</span>";
							} ?>
						</td>
						<td headers="mb_list_auth" class="td_mbstat" rowspan="2" ><?= $total_mining ?></td>
						<td headers="mb_list_auth" class="td_mbstat" rowspan="2"><?= $total_mining_rate ?> %</td> -->
						<td headers="mb_list_auth" class="text-center" style='width:40px;' rowspan="2"><span class='badge t_white color<?= $row['rank'] ?>'>
								<? if ($row['rank']) {
									echo 'P' . $row['rank'];
								} ?></span></td>


						<!-- <td headers="mb_list_member" class="td_mbgrade" rowspan="2">
							<span class='icon'><?= user_icon($row['mb_id'], 'icon') ?></span>
							<?php echo get_member_level_select("mb_level[$i]", 0, $member['mb_level'], $row['mb_level']) ?>
						</td> -->
						<td headers="mb_list_lastcall" class="td_date"><?php echo substr($row['mb_today_login'], 2, 8); ?></td>
						<!--<td headers="mb_list_grp" rowspan="1" class="td_numsmall"><?php echo $group ?></td>-->
						<td headers="mb_list_mng" rowspan="2" class="td_mngsmall" style="width:100px;"><?php echo $s_mod ?> <?php echo $s_grp ?></br> <?php echo $s_mod_binary ?></td>

					</tr>
					<tr class="<?php echo $bg; ?>">
						<td headers="mb_list_join" class="td_date"><?php echo substr($row['mb_datetime'], 2, 8); ?></td>
					</tr>

				<?php
				}
				if ($i == 0)
					echo "<tr><td colspan=\"" . $colspan . "\" class=\"empty_table\">자료가 없습니다.</td></tr>";
				?>
			</tbody>
		</table>
	</div>

	<div class="btn_list01 btn_list">
		<input type="submit" name="act_button" value="선택수정" onclick="document.pressed=this.value">
		<!-- <input type="submit" name="act_button" value="선택삭제" onclick="document.pressed=this.value"> -->
	</div>

</form>

<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, '?' . $qstr . '&amp;page='); ?>

<script>
	function fmemberlist_submit(f) {
		if (!is_checked("chk[]")) {
			alert(document.pressed + " 하실 항목을 하나 이상 선택하세요.");
			return false;
		}

		if (document.pressed == "선택삭제") {
			if (!confirm("선택한 자료를 정말 삭제하시겠습니까?")) {
				return false;
			}
		}
		return true;
	}

	function level_search(param) {
		$('#search_bar #level').val(param);
		//console.log($('#search_bar #level').val());
		$('#search_bar').submit();
	}

	function grade_search(param) {
		$('#search_bar #grade').val(param);
		//console.log($('#search_bar #level').val());
		$('#search_bar').submit();
	}

	function nation_search(param) {
		$('#search_bar #nation').val(param);
		//console.log($('#search_bar #nation').val());
		$('#search_bar').submit();
	}

	// 엑셀 다운로드
	$('#excel_btn').on("click", function() {

		var s_date = $('#s_date').val();
		var e_date = $('#e_date').val();
		//var idx_num = $('.select-btn').val();
		var idx_num = '';
		var ck_box = true;
		$('.ckbox').each(function() {
			if ($(this).prop('checked')) {
				if (ck_box == true) {
					ck_box = false;
					idx_num += $(this).val();
				} else {
					idx_num += '_' + $(this).val();
				}
			}
		})
		//console.log("/excel/metal.php?s_date="+s_date+"&e_date="+e_date+"&idx_num="+idx_num+"&idx=<?= $idx ?>");

		window.open("/excel/metal.php?s_date=" + s_date + "&e_date=" + e_date + "&idx_num=" + idx_num + "&idx=<?= $idx ?>");
	});
</script>

<?php
include_once('./admin.tail.php');
?>