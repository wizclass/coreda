<?
	include_once('./_common.php');
	include_once(G5_THEME_PATH.'/_include/wallet.php');
	include_once(G5_THEME_PATH.'/_include/gnb.php');
	//print_r($member);

	login_check($member['mb_id']);

    $binary_location = 'g5_member_binary';

	if($_GET['start_id']){
		$start_id = $_GET['start_id'];
	}else{
		$start_id = $member['mb_id'];
	}

	function milloin_number($val){
		return Number_format($val/10000);		
	}


	function get_left_bottom($start_id){

		$sql = "select mb_id FROM {$binary_location} WHERE  mb_brecommend='".$start_id."' and mb_brecommend_type='L'";
		$rst = sql_fetch($sql);
		$temp = $rst['mb_id'];

		if($temp==null || $temp==""){return '';}
		$left_bottom  = $temp;

		while(true){
			$sql2 = "select mb_id FROM {$binary_location} WHERE  mb_brecommend='".$temp."' and mb_brecommend_type='L'";
			$rst2 = sql_fetch($sql2);

			if($rst2['mb_id']!=null &&  $rst2!=""){
				$temp = $rst2['mb_id'];
				$left_bottom  = $temp;
			}
			else
			{
				break;
			}

		}
		return $left_bottom;
	}

	function get_right_bottom($start_id){

		$sql = "select mb_id FROM {$binary_location} WHERE  mb_brecommend='".$start_id."' and mb_brecommend_type='R' ";
		$rst = sql_fetch($sql);
		$temp = $rst['mb_id'];
		if($temp==null || $temp==""){return '';}
		$right_bottom  = $temp;
		while(true){
			$sql2 = "select mb_id FROM {$binary_location} WHERE  mb_brecommend='".$temp."' and mb_brecommend_type='R' ";
			$rst2 = sql_fetch($sql2);

			if($rst2['mb_id']!=null && $rst2!=""){
				$temp = $rst2['mb_id'];
				$right_bottom  = $temp;
			}
			else
			{
				break;
			}

		}
		return $right_bottom;
	}

	$left_bottom = get_left_bottom($start_id);
	$right_bottom = get_right_bottom($start_id);

/* ____________________________________________________________________________*/



$sql = "select mb_id as b_recomm FROM {$binary_location} WHERE  mb_brecommend='".$start_id."' and mb_brecommend_type='L'";
$sql_r = "select mb_id as b_recomm2 FROM {$binary_location} WHERE  mb_brecommend='".$start_id."' and mb_brecommend_type='R'";

$brst = sql_fetch($sql);
$brst_r = sql_fetch($sql_r);



$rows = 12;
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함


$b_recom_arr =  array();
array_push($b_recom_arr, $start_id);
array_push($b_recom_arr, $start_id);

array_push($b_recom_arr, $brst['b_recomm']);
array_push($b_recom_arr, $brst_r['b_recomm2']);

//왼쪽1
if($brst['b_recomm']){
	$brst2 = sql_fetch("select mb_id as b_recomm FROM {$binary_location} WHERE  mb_brecommend='".$brst['b_recomm']."' and mb_brecommend_type='L'");
	$brst2_r = sql_fetch("select mb_id as b_recomm2 FROM {$binary_location} WHERE  mb_brecommend='".$brst['b_recomm']."' and mb_brecommend_type='R'");
}
	array_push($b_recom_arr,$brst2['b_recomm']);
	array_push($b_recom_arr,$brst2_r['b_recomm2']);

//오른쪽1
if($brst_r['b_recomm2']){
	$brst3 = sql_fetch("select mb_id as b_recomm FROM {$binary_location} WHERE  mb_brecommend='".$brst_r['b_recomm2']."' and mb_brecommend_type='L'");
	$brst3_r = sql_fetch("select mb_id as b_recomm2 FROM {$binary_location} WHERE  mb_brecommend='".$brst_r['b_recomm2']."' and mb_brecommend_type='R'");
}
	array_push($b_recom_arr,$brst3['b_recomm']);
	array_push($b_recom_arr,$brst3_r['b_recomm2']);




//왼쪽2
if($brst2['b_recomm']){
	$brst4 = sql_fetch("select mb_id as b_recomm FROM {$binary_location} WHERE  mb_brecommend='".$brst2['b_recomm']."' and mb_brecommend_type='L'");
	$brst4_r = sql_fetch("select mb_id as b_recomm2 FROM {$binary_location} WHERE  mb_brecommend='".$brst2['b_recomm']."' and mb_brecommend_type='R'");
}
	array_push($b_recom_arr,$brst4['b_recomm']);
	array_push($b_recom_arr,$brst4_r['b_recomm2']);

//오른쪽2
if($brst2_r['b_recomm2']){
	$brst5 = sql_fetch("select mb_id as b_recomm FROM {$binary_location} WHERE  mb_brecommend='".$brst2_r['b_recomm2']."' and mb_brecommend_type='L'");
	$brst5_r = sql_fetch("select mb_id as b_recomm2 FROM {$binary_location} WHERE  mb_brecommend='".$brst2_r['b_recomm2']."' and mb_brecommend_type='R'");
}
	array_push($b_recom_arr,$brst5['b_recomm']);
	array_push($b_recom_arr,$brst5_r['b_recomm2']);




//왼쪽3
if($brst3['b_recomm']){
	$brst6 = sql_fetch("select mb_id as b_recomm FROM {$binary_location} WHERE  mb_brecommend='".$brst3['b_recomm']."' and mb_brecommend_type='L'");
	$brst6_r = sql_fetch("select mb_id as b_recomm2 FROM {$binary_location} WHERE  mb_brecommend='".$brst3['b_recomm']."' and mb_brecommend_type='R'");
}
	array_push($b_recom_arr,$brst6['b_recomm']);
	array_push($b_recom_arr,$brst6_r['b_recomm2']);

//오른쪽3
if($brst3_r['b_recomm2']){
	$brst7 = sql_fetch("select mb_id as b_recomm FROM {$binary_location} WHERE  mb_brecommend='".$brst3_r['b_recomm2']."' and mb_brecommend_type='L'");
	$brst7_r = sql_fetch("select mb_id as b_recomm2 FROM {$binary_location} WHERE  mb_brecommend='".$brst3_r['b_recomm2']."' and mb_brecommend_type='R'");
}
	array_push($b_recom_arr,$brst7['b_recomm']);
	array_push($b_recom_arr,$brst7_r['b_recomm2']);



$member_info = array();
$left_point = array();$right_point = array();


for($i=1;$i<=15;$i++){

	$left = sql_fetch("select mb_id  FROM {$binary_location} WHERE  mb_brecommend='".$b_recom_arr[$i]."' and mb_brecommend_type='L'")['mb_id'];
	$left_acc = sql_fetch("select IFNULL(SUM(noo + (SELECT sum(pv) FROM g5_shop_order WHERE mb_id ='{$left}') ),0) as hap from brecom_bonus_noo where mb_id ='{$left}' order by day desc limit 0 ,1");

	$right = sql_fetch("select mb_id  FROM {$binary_location} WHERE  mb_brecommend='".$b_recom_arr[$i]."' and mb_brecommend_type='R'")['mb_id'];
	$right_acc = sql_fetch("select IFNULL(SUM(noo + (SELECT sum(pv) FROM g5_shop_order WHERE mb_id ='{$right}') ),0) as hap from brecom_bonus_noo where mb_id ='{$right}' order by day desc limit 0 ,1");

	array_push($left_point, $left_acc['hap']);
	array_push($right_point, $right_acc['hap']);


	$sql = "select mb_id,mb_level,grade,mb_rate,(SELECT count(mb_id) FROM {$binary_location} WHERE  mb_recommend = '{$b_recom_arr[$i]}') as direct_cnt FROM {$binary_location} WHERE   mb_id ='{$b_recom_arr[$i]}' ";
	$rem_info = sql_fetch($sql);

	$member_info[$i]['mb_id'] = $rem_info['mb_id'];
	$member_info[$i]['mb_level'] = $rem_info['mb_level']+1;
	$member_info[$i]['grade']= $rem_info['grade'];
	$member_info[$i]['mb_rate'] = $rem_info['mb_rate'];
	$member_info[$i]['direct_cnt'] =$rem_info['direct_cnt'];
}


//본인 데이터 고정
$left_sql = " SELECT mb_rate, (SELECT noo FROM brecom_bonus_noo WHERE mb_id ='{$brst['b_recomm']}' ) AS noo FROM {$binary_location} WHERE  mb_id = '{$brst['b_recomm']}' ";
$mb_self_left_result = sql_fetch($left_sql);
$mb_self_left_acc = $mb_self_left_result['mb_rate'] + $mb_self_left_result['noo'];
$mb_self_left_noo_result = $mb_self_left_acc ;

$right_sql = " SELECT mb_rate, (SELECT noo FROM brecom_bonus_noo WHERE mb_id ='{$brst_r['b_recomm2']}' ) AS noo FROM {$binary_location} WHERE  mb_id = '{$brst_r['b_recomm2']}' ";
$mb_self_right_result = sql_fetch($right_sql);
$mb_self_right_acc = $mb_self_right_result['mb_rate'] + $mb_self_right_result['noo'];
$mb_self_right_noo_result = $mb_self_right_acc ;

$mem_self = $mb_self_left_noo_result - $mb_self_right_noo_result;

if($mem_self <= 0){
	$mem_self_left = 0;
	$mem_self_right = (-1*$mem_self);
}else{
	$mem_self_left = $mem_self;
	$mem_self_right = 0;
}

// 조직도 개편
// list($l_list, $r_list) = brecom_list('dangun23');

/* ____________________________________________________________________________*/

?>

<style>
	.material-icons{vertical-align:bottom;}
	.material-icons.grade1{color:black}
		.material-icons.grade2{color:red}
			.material-icons.grade3{color:blue}
				.material-icons.grade4{color:green}
</style>

	<link rel="stylesheet" href="<?=G5_THEME_URL?>/_common/css/level_structure.css">
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	
		<main class="binary_wrap">
			<div class="container nomargin nopadding">
				<section class="binary_wrap">
				
					<div class="btn_input_wrap binary_search">
						<div class="bin_top">회원 검색</div>
						<form id="sForm" name="sForm" method="post" >
							<ul class="row">
								<li class="col-9 user_search_wrap">
									<input type="text" class="" style="background:#eff3f9;color:black;border:1px solid #d9dfe8" placeholder="Member Search" name="binary_seach" id="binary_seach" data-i18n='[placeholder]binary.회원찾기'/>
								</li>
								<li class="col-3 search_btn_wrap">
									<button type="button" class="btn wd b_skyblue b_radius"  id="search_btn"><i class="ri-search-line"></i></button>
								</li>
							</ul>
						</form>

					</div>
						
					<div class='btn_input_wrap'>
						<div class="bin_top" data-i18n='binary.후원계보'> Member Stack </div>
						<div class="legbox">
						<div class="leg-view-container">
								<?$leg_stack = array();?>
								<?
									if($start_id!=$member['mb_id']){
										$get_list_higher  = "select mb_brecommend FROM {$binary_location} WHERE  mb_id='".$start_id."'";
										$higher_id = sql_fetch($get_list_higher);
										array_push($leg_stack, $higher_id['mb_brecommend']);
								?>

								<?
									while(true){
										if($higher_id['mb_brecommend'] != $member['mb_id']){
										$get_list_higher  = "select mb_brecommend FROM {$binary_location} WHERE  mb_id='".$higher_id['mb_brecommend']."'";
										$higher_id = sql_fetch($get_list_higher);
								?>
										<? array_push($leg_stack, $higher_id['mb_brecommend']);?>

								<?
										}
										else{
											break;
										}
									}
									$reverse_stack  = array_reverse($leg_stack);
									$cnt = count($reverse_stack) ;

									for($i=0;$cnt > $i; $i++){
										if($i == $cnt - 1){ ?>
											<span class="leg-name mbId" name="<?echo $reverse_stack[$i];?>"><?echo $reverse_stack[$i];?> </span>
										<?}else{?>
											<span class="leg-name mbId" name="<?echo $reverse_stack[$i];?>"><?echo $reverse_stack[$i];?></span>
											<span><i class="ri-arrow-right-s-fill"></i></span>
										<?}?>
									<?
									}
								}
								?>
							</div>
						</div>
					</div>
					</div>

					<div class="desc font_red" style='font-size:11px'>[ 금액단위 : 만원 ]</div>

					<div class='btn_input_wrap'>
						<div class="sumary_lr mt20">
							<li class="left">
								LEFT : <?=milloin_number($mem_self_left)?><br>
								<hr><span> ACC : <?=milloin_number(($mb_self_left_noo_result ? $mb_self_left_noo_result : 0),0)?></span>
							</li>
							<li class="right">
								RIGHT : <?=milloin_number($mem_self_right)?><br>
								<hr><span> ACC : <?= milloin_number(($mb_self_right_noo_result ? $mb_self_right_noo_result : 0),0) ;?></span>
							</li>
						</div>
					</div>

					<style>
						.lvl img{margin:0;padding:0;}
					</style>
					<div class="tree-container">
					<div class="bin_top" >후원 조직도</div>
						<div class="tree">

							<div class="lvl1"> <!--1단계-->
								<div class="box lvl" id="1" align="center">
									<!-- 템플릿 -->
									<span class="lvl-icon" style='margin-right:0'><?=user_icon($b_recom_arr[1],'icon')?></span>
									<div class='userid'><?echo $b_recom_arr[1]?></div>
									<div class='grade_package'>
										<!-- <span class='level'>[ <?=user_icon($b_recom_arr[1],'level')?> ]</span> -->
										<span class='badge grade color<?=user_grade($b_recom_arr[1])?>'><?=user_grade($b_recom_arr[1])?> S</span>
										<span class='direct_cnt badge'><i class='ri-user-star-line'></i> <?=$member_info[1]['direct_cnt']?></span>
										<span class='badge pv'>
										<!-- <?=max_item_level_array($b_recom_arr[1],'name')?> -->
										<?=milloin_number($member_info[1]['mb_rate'])?>
										</span>
									</div>
									
									
									<div class='pointed left'><?=milloin_number($left_point[0])?> </div><div class='pointed right'><?=milloin_number($right_point[0])?></div>
								</div>
							</div>
							<!--line-->
							<div class="line_1">
								<div class="line1-1"></div>
								<div class="line2"></div>
							</div>

							<div class="lvl2"> <!--2단계-->
							<?for($i=2; $i<4;$i++){
								if($b_recom_arr[$i]){?>

								<div class="box lvl" id="<?echo $i ;?>" >
									<!-- 템플릿 -->
									<span class="lvl-icon" style='margin-right:0'><?=user_icon($b_recom_arr[$i],'icon')?></span>
									<div class='userid'><?echo $b_recom_arr[$i]?></div>
									<div class='grade_package'>
										<!-- <span class='level'>[ <?=user_icon($b_recom_arr[$i],'level')?> ]</span> -->
										<span class='badge grade color<?=user_grade($b_recom_arr[$i])?>'><?=user_grade($b_recom_arr[$i])?> S</span>
										<span class='direct_cnt badge'><i class='ri-user-star-line'></i> <?=$member_info[$i]['direct_cnt']?></span>
										<span class='badge pv'>
										<!-- <?=max_item_level_array($b_recom_arr[$i],'name')?></span> -->
										<?=milloin_number($member_info[$i]['mb_rate'])?>
										</span>
									</div>
									
									<div class='pointed left'><?=milloin_number($left_point[$i-1])?> </div><div class='pointed right'> <?=milloin_number($right_point[$i-1])?></div>
								</div>

								<?}else{?>
								<div class="lvl-open" id="<?echo $i ;?>" >
									<select class="form-control">
										<option selected value="" data-i18n='binary.회원선택하기'>Select Member</option>
									</select>
									<button class="addMem b_skyblue b_radius_5"><span data-i18n='binary.등록하기'>Add member</span></button>
								</div>
								<?}//else end
								}//for end?>
							</div>

							<!--line-->
							<div class="line_2_con">
								<div class="line_2">
								<div class="line1-1"></div>
									<div class="line2"></div>
								</div>
								<div class="line_2">
									<div class="line1-1"></div>
									<div class="line2"></div>
								</div>
							</div>

							<div class="lvl3"> <!--3단계-->
								<?for($i=4; $i<8 ;$i++){
									if($b_recom_arr[$i]){
								?>
								<div class="box lvl" id="<?echo $i ;?>" >
									<!-- 템플릿 -->
									<span class="lvl-icon" style='margin-right:0'><?=user_icon($b_recom_arr[$i],'icon')?></span>
									<div class='userid'><?echo $b_recom_arr[$i]?></div>
									<div class='grade_package'>
										<span class='badge grade color<?=user_grade($b_recom_arr[$i])?>'><?=user_grade($b_recom_arr[$i])?> S</span>
										<span class='direct_cnt badge'><i class='ri-user-star-line'></i> <?=$member_info[$i]['direct_cnt']?></span>
										<span class='badge pv'>
										<!-- <?=max_item_level_array($b_recom_arr[$i],'name')?></span> -->
										<?=milloin_number($member_info[$i]['mb_rate'])?>
										</span>
									</div>
									<div class='pointed left'><?=milloin_number($left_point[$i-1])?> </div><div class='pointed right'> <?=milloin_number($right_point[$i-1])?></div>
								</div>
								<?//if end}
								}
								else{?>
								<div class="lvl-open" id="<?echo $i ;?>" >
									<select class="form-control">
										<option selected="" value="" data-i18n='binary.회원선택하기'>Select Member</option>
									</select>
									<button class="addMem b_skyblue b_radius_5"><span data-i18n='binary.등록하기'>등록하기</span></button>
								</div>
								<?}//else end
								}//for end?>
							</div>

							<div class="b_line4"></div>
							<!--line-->
							<!-- <div class="line_2_con">
								<div class="line_2">
								<div class="line1-1"></div>
									<div class="line2"></div>
								</div>
								<div class="line_2">
									<div class="line1-1"></div>
									<div class="line2"></div>
								</div>
							</div> -->
							<!--4단계-->
						</div>
						<div class="b_line3"></div>
						<div class="page-scroll">
							<span id="left_top" class="b_skyblue b_radius_5" data-i18n='binary.왼쪽 맨 아래로'>Left bottom</span>
							<span id="go_top" class="b_skyblue b_radius_5" data-i18n='binary.맨 위로 가기'>Back to top</span>
							<span id="go_up_one" class="b_skyblue b_radius_5" data-i18n='binary.한 단계 위로 가기'>One level up</span>
							<span id="right_top" class="b_skyblue b_radius_5" data-i18n='binary.오른쪽 맨 아래로'>Right bottom</span>
						</div>
					</div>

					<!--
					<?
					$mb_id = $start_id;
					$distance=0;

					while($mb_id!=$start_id){
						$get_recommend  = "select mb_recommend FROM {$binary_location} WHERE  mb_id='".$mb_id."'";
						$rst_recom = sql_fetch($get_recommend);
						$mb_id = $rst_recom['mb_recommend'];
						$distance++;
					}

					$now_member = get_member($start_id); //get member info
					$member_nation = "select * from pinna_nation_code where code = ".$now_member['nation_number'];
					//SELECT * FROM  `pinna_nation_code`
					$nation_rst = sql_fetch($member_nation);

					$get_recom = "select count(mb_recommend) as recom_cnt FROM {$binary_location} WHERE  mb_recommend = '".$start_id."'";
					$recom_rst = sql_fetch($get_recom);

					?>


					<div class="member-info">
					<div class="member-details">
						<h5><span data-i18n="tree.info" >Member Information</span> - <?echo $now_member['mb_id'];?> </h5>
						<table class="table table-striped table-bordered">
							<tbody>
							<tr>
								<th scope="row" data-i18n="tree.fName" >First Name</th>
								<td><?echo $now_member['first_name'];?> </td>
							</tr>
							<tr>
								<th scope="row" data-i18n="tree.lName" >Last Name</th>
								<td><?echo $now_member['last_name'];?></td>
							</tr>
							<tr>
								<th scope="row" data-i18n="tree.country" >Country</th>
								<td><?echo $nation_rst['nationv_en'];?></td>
							</tr>
							<tr>
								<th scope="row" data-i18n="tree.spon" >Sponsor</th>
								<td><?echo $now_member['mb_recommend'];?></td>
							</tr>

							<tr>
								<th scope="row" data-i18n="tree.pack" >Packages</th>
								<td>
									<?echo $my_pool_lv?>
								</td>
							</tr>

							<tr>
								<th scope="row" data-i18n="tree.rank">Rank</th>
								<td><?echo $my_rank_img?></td>
							</tr>

							<tr>
								<th scope="row" data-i18n="tree.stat">Status</th>
								<td>Active</td>
							</tr>
							<tr>
								<th scope="row" data-i18n="tree.left">Left Volume</th>
								<td><?echo round($total_l_value/1000)?></td>
							</tr>
							<tr>
								<th scope="row" data-i18n="tree.right" >Right Volume</th>
								<td><?echo round($total_r_value/1000)?></td>
							</tr>
							<tr>
								<th scope="row" data-i18n="tree.dist" >Distance From Me</th>
								<td><?echo $distance?></td>
							</tr>

							<tr>
								<th scope="row" data-i18n="tree.sponsored" >Total Sponsored</th>
								<td><?echo $recom_rst['recom_cnt'];?></td>
							</tr>
							<tr>
								<th scope="row" data-i18n="tree.members" >Total Binary Members</th>
								<td><?echo $now_member['mb_b_child'];?> </td>
							</tr>
							<tr>
								<th scope="row" data-i18n="tree.enroll" >Enrollment Date</th>
								<td><?echo $now_member['mb_open_date'];?></td>
							</tr>

							<tr>
								<th scope="row" data-i18n="tree.place" >Placement Date</th>
								<td><?echo $now_member['mb_bre_time'];?></td>
							</tr>
							</tbody>
						</table>
					</div>
					-->


						<!-- <div class="member-volume">
							<h5 data-i18n="binary.바이너리 볼륨" >Binary Volume</h5>

								<?
									$res_point = sql_query($get_point2);
								?>
								<table class="table table-hover">
									<thead>
										<tr>
											<th scope="col" >Day</th>
											<th scope="col" >Left Point</th>
											<th scope="col" >Right Point</th>
										</tr>
									</thead>
									<tbody>
									<?while($list=sql_fetch_array($res_point)){?>
									<tr>
									<td><?echo $list['iwolday'];?></td>
									<td><?echo round( $list['l_pv']/1000,2);?></td>
									<td><?echo round($list['r_pv']/1000,2);?></td>
									</tr>
									<?}?>
									</tbody>
								</table>

									<?php
										$qstr = 'start_id='.$start_id;
										$pagelist = get_paging_new($config['cf_write_pages'], $page, $total_page, $_SERVER['SCRIPT_NAME'].'?'.$qstr.'&amp;domain='.$domain.'&amp;page=');
										if ($pagelist) {
											echo $pagelist;
										}
									?>

								<nav class="pagination-container"></nav>
						</div> -->
				</section>
			</div>
		</main>
		<div class="structure_search_container">
			<div class="structure_search_result" id="structure_search_result"></div>
			<div class="result_btn">Close</div>
		</div>
		
		<? include_once(G5_THEME_PATH.'/_include/tail.php'); ?>

			

	</section>

	<!-- SELECT TEMPLATE -->
	<select style="display:none;" id="dup" >
		<option value=""></option>
	</select>

	<script>
		$(function(){
			$(".top_title h3").html("<span >후원도보기</span>")
			$('#wrapper').css("background", "#fff");
		});
	</script>


	<script>
	var b_recom_arr = JSON.parse('<? echo json_encode($b_recom_arr);?>');
	var $div = $('<div>');
	var data1 = {};

	$(function() {

		// 리스트 호출 바로윗단계기준 호출
		/*
		$( ".lvl-open" ).each(function( index ) {
			var upperId = Math.floor($(this).attr("id")/2);
			console.log("upperId : " +  upperId);

			var id = $(this).attr("id");

			if(b_recom_arr['upperId']){ // 상위 회원이 있을때
				$.ajax({
					url: g5_url+'/util/binary_tree_mem.php',
					type: 'GET',
					async: false,
					data: {
						mb_id: b_recom_arr['upperId']
					},
					dataType: 'json',
					success: function(result) {
						 //console.log(result);
						$div.empty();
						$.each(result, function( index, obj ) {
							var opt = $('#dup > option').clone();
							opt.attr('value', obj.mb_id);
							opt.html(obj.mb_id + '(' + obj.first_name + ' ' + obj.last_name + ')');
							$div.append(opt);
						});
						$('#'+id+'.lvl-open').find('select').append($div.html());
					}
				});

			}
		});
		*/

		// 리스트 호출 로그인멤버기준
		$( ".lvl-open" ).each(function( index ) {
			var upperId = Math.floor($(this).attr("id")/2);
			var id = $(this).attr("id");
			var mem_id = "<?=$member['mb_id']?>";

			console.log("upperId : " +  upperId + " | mem : "+ mem_id + " | " + b_recom_arr[upperId]);
			//console.log("success : "+ b_recom_arr[upperId]);
			if(b_recom_arr[upperId]){
				$.ajax({
					url: g5_url+'/util/binary_tree_mem.php',
					type: 'POST',
					async: false,
					data: {
						mb_id: mem_id
					},
					dataType: 'json',
					success: function(result) {

						console.log("success" +result);

						$div.empty();
						$.each(result, function( index, obj ) {
							var opt = $('#dup > option').clone();
							opt.attr('value', obj.mb_id);
							opt.html(obj.mb_id);
							$div.append(opt);
						});
						$('#'+id+'.lvl-open').find('select').append($div.html());
					}
				});
			}
		});


		// 후원인 추가 등록 버튼
		$('.addMem').click(function(){
			//console.log('후원인등록');

			var no = $(this).parent().attr('id');
			var upperId = Math.floor(no/2);

			if(!b_recom_arr[upperId]){ // 상위 회원이 없을때
				commonModal('Error',"Can not place this position.",80);
				return;
			}


			if(!$(this).siblings('select').val()){
				commonModal('Error',"Select Member",80);
				return;
			}

			var set_type = "";
			if(no%2 == 0){ // 나머지가 0이면 좌측 노드
				set_type = "L";
			}else{
				set_type = "R";
			}
			 //console.log(set_type);
			 //console.log($(this).siblings('select').val());
			data1 = {
				"set_id": b_recom_arr[upperId],
				"set_type": set_type,
				"recommend_id": $(this).siblings('select').val()
			};
			$('#confirmModal').modal('show');
		});


		// 후원인 추가 등록 확인 > 저장
		$('#confirmModal #btnSave').on('click',function(e){
			$.ajax({
				url: g5_url+'/util/binary_tree_add.php',
				type: 'POST',
				async: false,
				data: data1,
				dataType: 'json',
				success: function(result) {
					//console.log(result);
					location.reload();
				},
				error: function(e){
					console.log(e);
				}
			});
		});

		//상단 나열이름 클릭
		$('.leg-name').click(function(){
			var move_id = $(this).attr("name");
			if(move_id){
				location.replace(g5_url + "/page.php?id=binary2&start_id="+move_id);
			}
		});

		//회원카드 클릭
		$('.lvl').click(function(){
			var id_check = $(this).attr("id");
			var add_id = Math.floor(id_check/2);
			var add_id2 = id_check%2;
			//나머지가 0이면 Left //나머지가 1이면 Right
			//alert (b_recom_arr[id_check]);
			if(id_check!=1){
				location.replace(g5_url + "/page.php?id=binary2&start_id="+b_recom_arr[id_check]);
			}
			//alert (add_id);
		});


		//회원검색 SET
		$('#search_btn').click(function(){
			if($("#binary_seach").val() == ""){
				commonModal('Error','Please enter a keyword.',80);
				$("#binary_seach").focus();
			}else{
				$.post(g5_url + "/util/ajax_get_tree_member.php", $("#sForm").serialize(),function(data){
					dimShow();
					$('.structure_search_container').addClass("active");
					$("#structure_search_result").html(data);
				});
			}

		});


		$('.result_btn').click(function(){
			$('.structure_search_container').removeClass('active');
			dimHide();
		});
	/*
		$('#binary_seach').on('keydown',function(e){
			if(e.which == 13) {
				e.preventDefault();
				$('#search_btn').trigger('click');
			}
		});
	*/


	// 하단 4단계 버튼


	$("#left_top").click(function(){
//		var left_bottom = $('.8').val();
		var left_bottom =  "<?=$left_bottom?>";
		if(left_bottom!=null && left_bottom!=""){
			location.replace(g5_url + "/page.php?id=binary2&start_id="+left_bottom);
		}
		else
			//alert("Can't move left bottom");
			commonModal('Error',"Can't move left bottom.",80);
	});

	$("#go_top").click(function(){
		location.replace(g5_url + "/page.php?id=binary2&start_id=<?=$member['mb_id']?>");
	});

	$("#go_up_one").click(function(){

		var id = "<?=$start_id?>";
		//console.log(id);
		$.ajax({
			type: "POST",
			url: g5_url + "/util/binary_tree_uptree.php",
			cache: false,
			async: false,
			dataType: "json",
			data:  {
				start_id : id
			},
			success: function(data) {
					//alert(data.result);
					if(data.result!="")
						location.replace(g5_url + "/page.php?id=binary2&start_id="+data.result);
					else
						 //alert("Now member is Top");
						 commonModal('Notice',"Now member is Top",80);
			}
		});
	});

	$("#right_top").click(function(){
		var right_bottom = "<?=$right_bottom?>";
		if(right_bottom!=null && right_bottom!=""){
			location.replace(g5_url + "/page.php?id=binary2&start_id="+right_bottom);
		}
		else
			//alert("Can't move left bottom");
			commonModal('Error',"Can't move left bottom.",80);
	});

});

function go_member(go_id){
	//location.replace(g5_url + "/page.php?id=binary&start_id="+data.result);
	location.replace(g5_url + "/page.php?id=binary2&start_id="+go_id);
}
</script>

