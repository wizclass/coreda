<?php
$sub_menu = "600300";
include_once('./_common.php');
include_once('./inc.member.class.php');


/* gubun: 
fr_date: 2021-02-24
to_date: 2022-02-24
go_id: test1 */

/*슈퍼관리자인경우 최상위 관리자로 변경*/
if($member['mb_id'] == 'admin'){
	$tree_id = $config['cf_admin'];
}else{
	$tree_id = $member['mb_id'];
}


$go_id = $_GET['go_id'];

if ($member['mb_org_num']){
	$max_org_num = $member['mb_org_num'];
}else{
	$max_org_num = 10;
}

$max_org_num = 10;


if ($gubun=="B"){
	$class_name     = "g5_member_bclass";
	$recommend_name = "mb_brecommend";
}else{
	$class_name     = "g5_member_class";
	$recommend_name = "mb_recommend";
}

//$mrow = sql_fetch("select * from g5_member where mb_id='{$go_id}'");

$crow = sql_fetch("select c_class from {$class_name} where mb_id='{$tree_id}' and c_id='{$go_id}'");

$mdepth = (strlen($crow['c_class'])/2);


$sql       = "select c.c_id,c.c_class from g5_member m join ".$class_name." c on m.mb_id=c.mb_id where c.mb_id='{$tree_id}' and c.c_id='{$go_id}'";

$srow      = sql_fetch($sql);
$my_depth  = strlen($srow['c_class']);
$max_depth = ($my_depth+($max_org_num*2));

$sql = "select c.c_id,c.c_class,
(select mb_level from g5_member where mb_id=c.c_id) as mb_level
,(select grade from g5_member where mb_id=c.c_id) as grade
,(select count(*) from g5_member where mb_recommend=c.c_id) as c_child
,(select mb_b_child from g5_member where mb_id=c.c_id) as b_child
,(select mb_id from g5_member where mb_brecommend=c.c_id and mb_brecommend_type='L' limit 1) as b_recomm
,(select mb_id from g5_member where mb_brecommend=c.c_id and mb_brecommend_type='R' limit 1) as b_recomm2
,(select count(mb_no) from g5_member where ".$recommend_name."=c.c_id and mb_leave_date = '') as m_child
,(SELECT mb_rate FROM g5_member WHERE mb_id = c.c_id) AS mb_rate
,(SELECT mb_save_point FROM g5_member WHERE mb_id = c.c_id) AS mb_pv
,(SELECT recom_sales FROM g5_member WHERE mb_id = c.c_id) AS recom_sales
,(SELECT mb_child FROM g5_member WHERE mb_id=c.c_id) AS mb_children
,(SELECT mb_nick FROM g5_member WHERE mb_id=c.c_id) AS mb_nick
,(SELECT mb_center FROM g5_member WHERE mb_id=c.c_id) AS mb_center
 from g5_member m join ".$class_name." c on m.mb_id=c.mb_id where c.mb_id='{$tree_id}' and c.c_class like '{$crow['c_class']}%' and length(c.c_class)<".$max_depth." order by c.c_class";

$result = sql_query($sql);

for ($i=0; $row=sql_fetch_array($result); $i++) {

	

	if (strlen($row['c_class'])==2){
		$parent_id = 0;
	}else{
		$parent_id = substr($row['c_class'],0,strlen($row['c_class'])-2);
	}

	if (!$row['b_child']) $row['b_child']=1;
	//if (!$row['c_child']) $row['c_child']=1;

	if($row['mb_level'] == 2){
		$user_icon = "<i class='ri-team-fill'></i>";
	}else if($row['mb_level'] == 3){
		$user_icon = "<i class='ri-community-line'></i>";
	}else if($row['mb_level'] == 4){
		$user_icon = "<i class='ri-building-2-line'></i>";
	}else if($row['mb_level'] == 5){
		$user_icon = "<i class='ri-government-line'></i>";
	}else if($row['mb_level'] > 8){
		$user_icon = "<i class='ri-user-settings-line'></i>";
	}else{
		$user_icon = "<i class='ri-vip-crown-line'></i>";
	}

	$name_line =  "<p class='mb'><span class='user_icon lv".trim($row['mb_level'])."'>".$user_icon."</span>";
	$name_line .= "<span class='badge grade grade_{$row['grade']}'><i class='ri-star-fill' style='font-size:12px;vertical-align:text-bottom'></i> ". $row['grade'] ."</span>";
	$name_line .= "<span class='user_id' data-id='{$row['c_id']}'>". $row['c_id'] ."</span>";
	$name_line .= "<span class='user_name'>". $row['c_name'] ."</span>";
	if($row['mb_nick'] != ''){
		$name_line .= "<span class='user_nick'>[ ". $row['mb_nick'] ." ]</span>";
	}
	$name_line .= " | <span class='mb_pv'> D : ".Number_format($row['mb_habu_sum'])."</span>";
	// $name_line .= " | <span class='mb_pv'> MH : ".Number_format($row['mb_rate'])."</span>";
	$name_line .= " | <span class='mb_rate'> PV : ".Number_format($row['mb_pv'])."</span>";
	$name_line .= " | <span class='mb_acc'> ACC : ".Number_format($row['recom_sales'])."</span>";
	$name_line .= "</p>";

	$data .= '{ id:"'.$row['c_class'].'", pId:"'.$parent_id.'", name:"'.$name_line.'", open:true, click:false},';
}

?>

		<div class="zTreeDemoBackground left" >
			<ul id="treeDemo" class="ztree"></ul>
		</div>

		<SCRIPT type="text/javascript">
			var zNodes =[<?=$data?>];

			$(document).ready(function(){
				$.fn.zTree.init($("#treeDemo"), setting, zNodes);
			});
			
			$(function(){
				$('.user_id').on('click',function(){
					var target = $(this).data('id');
					console.log(target);
					go_member(target);

				});
			});
		</SCRIPT>