<?
	// $menubar = 1;
	include_once('./_common.php');
	include_once(G5_THEME_PATH.'/_include/wallet.php');
	include_once(G5_THEME_PATH.'/_include/gnb.php');
	
	if( $member['mb_id'] == 'admin'){
		$tree_id = $config['cf_admin'];
		$tree_no = 1;
	}else{
		$tree_id = $member['mb_id'];
		$tree_no = $member['mb_no'];
	}

	login_check($member['mb_id']);

	///bbs/level_structure_upgraded.list.php 로드
	///bbs/level_structure_upgraded.search.php 검색
	///bbs/level_structure_upgraded.mem.php
	///util/level_structure.leg.php 스택

?>



<link rel="stylesheet" href="<?=G5_THEME_URL?>/_common/css/level_structure.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/numeral.js/2.0.6/numeral.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.11/lodash.min.js"></script>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;500;600;700&display=swap" rel="stylesheet">
<script>

var levelMap = {
	0 : '10',
	1 : '',
	2 : 'lvl-three dl_3depth',
	3 : 'lvl-four dl_4depth',
	4 : 'lvl-five dl_5depth',
	5 : 'lvl-six dl_6depth',
	6 : 'lvl-six dl_7depth',
	7 : 'lvl-six dl_8depth',
	8 : 'lvl-six dl_9depth',
	9 : 'lvl-ten dl_10depth',
	10 : 'lvl-ten dl_11depth'
};


var depthMap = {
		0 : 'dl_1depth',
		1 : 'dl_2depth',
		2 : 'dl_3depth',
		3 : 'dl_4depth',
		4 : 'dl_5depth',
		5 : 'dl_6depth',
		6 : 'dl_7depth',
		7 : 'dl_8depth',
		8 : 'dl_9depth',
		9 : 'dl_10depth',
		10 : 'dl_11depth'
	};

var gradeMap = {
		0 : 'gr_0',
		1 : 'gr_1',
		2 : 'gr_2',
		3 : 'gr_3',
		4 : 'gr_4',
		5 : 'gr_5',
		6 : 'gr_6',
		7 : 'gr_7',
		8 : 'gr_8',
		9 : 'gr_9',
		10 : 'gr_10',
	};

	var $selected;
	var mb_no = '<?=$tree_no?>';
	//var xhr;

	$(function() {
		// 상세보기

		/* $(document).on('click','.lvl' ,function(e) {
			
			$(this).toggleClass('lvl-is-open');

			$selected = $(this).next();
			if($selected.css('max-height') != '0px' ){
				$selected.css('max-height','0px');
			}else{
				$selected.css('max-height', $selected.prop('scrollHeight') + 'px');
			}
			// console.log($(this).attr('mb_no'));

			if($(this).hasClass('lvl-is-open')){
				$.get( "/util/level_structure_upgraded.mem.php", {
					mb_no: $(this).attr('mb_no')
				}).done(function( data ) {
					if(data){
						$selected.find('.name').text(data.mb_id);
						$selected.find('.sponsor').text(data.mb_recommend);
						$selected.find('.enroll').text(daΩta.enrolled);
						if(data.mb_level > 1 && data.mb_level < 9){
							$selected.find('.rank').text((data.mb_level -2) + ' Star');
						}
						
					}
				}).fail(function(e) {
					console.log( e );
				});
			}
		}); */

		 $(document).on('click','#lvl-userid' ,function(e) {

			getList($(this).text(), 'name');
			getLeg('<?=$tree_id?>', $(this).text());
			$('.search_container').removeClass("active");
		 });


/*
		 $(document).on('click','.lv' ,function(e) {
			var search_mb_id = $(this).parent().find('.lvl-username').text();
			getList(search_mb_id, 'name');
			getLeg('<?=$tree_id?>', $(this).attr('mb_id'));
			e.stopPropagation();
		});
*/

		/* $(document).on('click','._lvl > .lv' ,togglebar);
		$(document).on('click','._lvl > .toggle' ,togglebar);

		function togglebar() {
			var con = $(this).parents('.lvl-container');
			var level = con.attr('class').replace('lvl-container ','');

			if(con.hasClass('closed')){
				con.nextUntil( "." + level ).removeClass('closed').show();
				con.removeClass('closed');
				// $(this).parent().find('.toggle').css('color','black');
			}else{
				// $(this).parent().find('.toggle').css('color','#ccc');
				con.nextUntil( "." + level ).hide();
				con.addClass('closed');
			}
			event.stopPropagation();
		} */



		$(document).on('click','.go' ,function(e) {
			var search_mb_id = $(this).parent().parent().find('.lvl-username').text();

			// console.log(search_mb_id);

			getList(search_mb_id, 'name');
			getLeg('<?=$tree_id?>', $(this).attr('mb_id'));
			event.stopPropagation();
		});

		

		// 엔터키
		$('#now_id').keydown(function (key) {
			if(key.keyCode == 13){
				key.preventDefault();
				//$('button.search-button').trigger('click');
				member_search();
			}
		});

		// 조직도 데이터 가져오기
		getList(Number(mb_no),'num');
		getLeg('<?=$tree_id?>', "<?=$tree_id?>");

	});


	function depthFirstTreeSort(arr, cmp) {

		function makeTree(arr) {
			var tree = {};
			for (var i = 0; i < arr.length; i++) {
				if (!tree[arr[i].mb_recommend_no]) tree[arr[i].mb_recommend_no] = [];
				tree[arr[i].mb_recommend_no].push(arr[i]);
			}
			return tree;
		}


		function depthFirstTraversal(tree, id, cmp, callback) {

			var children = tree[id];

			if (children) {
				children.sort(cmp);
				for (var i = 0; i < children.length; i++) {
					callback(children[i]);
					if(children[i].mb_no != mb_no){

							depthFirstTraversal(tree, children[i].mb_no, cmp, callback);
						}
					/*
					if(mb_no > 2){
						depthFirstTraversal(tree, children[i].mb_no, cmp, callback);
					}else{
						if(children[i].mb_no != mb_no){
							console.log(tree );
							depthFirstTraversal(tree, children[i].mb_no, cmp, callback);
						}
					}
					*/
				}

			}
		}

		var i = 0;
		var tree = makeTree(arr);
		depthFirstTraversal(tree, arr[0].mb_recommend_no, cmp, function(node) {
			arr[i++] = node;
		});
	}

	// function nameCmp(a, b) { return a.mb_id.localeCompare(b.mb_id); }
	nameCmp = function(a, b){ return a.mb_no < b.mb_no; }


	// 검색하는 부분
	function getMember(){
		
		var findemb_id = $("#now_id").val();

		getList( findemb_id, 'name' );
		getLeg('<?=$tree_id?>', mb_id);
	}

	function numberWithCommas(x) {
    	return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	}

	function member_search(){
		
			if($("#now_id").val() == ""){
				commonModal('회원 찾기','검색어를 입력해주세요.',80);
				$("#now_id").focus();
				return;
			}

			$.get("/util/level_structure_upgraded.search.php", {
				keyword: $("#now_id").val()
			}).done(function( data ) {
				
				$('.structure_search_container').addClass("active");
				var vHtml = $('<div class=rows>');
				$.each(data, function( index, member ) {
					var line = $('<div class=rows>').append($('<strong>').addClass('mbId').html(member.mb_id));

					/* if(member.mb_name != ''){
						line.append('<br>');
						line.append( '(' + member.mb_email + ')');
					}else{
						line.css('line-height','50px');
					} */
					vHtml.append(line);
				});
				$("#structure_search_result").html(vHtml.html());
				$(".structure_search_container .result_btn").click(function(){
					$('.structure_search_container').removeClass("active");
				});

				if ($("#structure_search_result").html() == ''){
					$("#structure_search_result").addClass('noData');
					$("#structure_search_result").html('검색결과가 없습니다.');
				} else {
					$("#structure_search_result").removeClass('noData');
				}
			}).fail(function(e) {
				console.log( e );
			});
		}
	

		// 검색결과 클릭
		$(document).on('click','.mbId' ,function(e) {
			getList($(this).text(), 'name');
			getLeg('<?=$tree_id?>', $(this).text());
			dimHide();
			$('.structure_search_container').removeClass("active");
		});
	

	function getList2(member_no, type){
		var url = "/util/level_structure_upgraded.list.php";
		// var url = "/util/structure_member_list.php";
		$.get( url, {
			mb_no: member_no,
			type : type
		}).done(function( data ) {
			//tt = data;
			// console.log(data );
			var minObj = _.minBy(data, function(o) { return Number(o.depth); });

			_.forEach(data, function(member) {
				member.treelvl = member.depth;
				member.gradelvl = member.grade;

				// 구매한 최고 패키지
				/* $.ajax({  
					url: "/util/ajax.max_package.php",  
					data: {mb_id : member.mb_id},  
					type: 'POST',
					dataType : 'json',
					async: false, 
					success: function(res){
						member.maxItem = res.result;
					}
				}); */

			});

			depthFirstTreeSort(data, nameCmp);

			$('#total').text(data.length);

			var vHtml = $('<div>');
			

			$.each(data, function( index, member ) {
				var row = $('#dup .lvl-container').clone();

				if(member.mb_block == '0'){
					var status = "Active";
				}else{
					var status = "Block";
				}

				row.addClass(depthMap[member.treelvl]);
				row.addClass(gradeMap[member.gradelvl]);

				var user_icon = '';
				if(member.mb_level == 0) {
					user_icon = "<img src='<?=G5_THEME_URL?>/img/user.png' alt='일반회원'>";
				}else if(member.mb_level > 9){
					user_icon = "<img src='<?=G5_THEME_URL?>/img/user_admin.png' alt='관리자'>";
				}else{
					if(member.mb_level == 2){
						user_icon  = "<img src='<?=G5_THEME_URL?>/img/user_2.png' alt='센터회원'>";
					}else if(member.mb_level == 3){
						// user_icon  = "<img src='<?=G5_THEME_URL?>/img/user_3.png' alt='인정회원'>";
						user_icon = "<img src='<?=G5_THEME_URL?>/img/user_general.png' alt='정회원'>";
					}else{
						user_icon = "<img src='<?=G5_THEME_URL?>/img/user_general.png' alt='정회원'>";
					}
				}
				// console.log(user_icon);
				row.find('._lvl').attr('data-depth',member.treelvl);
				row.find('.lvl-icon').html(user_icon);
				row.find('.lvl-username').text(member.mb_id);
				row.find('.level').addClass('color'+member.grade);
				row.find('.level').text(member.grade+' star');
				row.find('.lvl-container').attr('class',"gr_"+member.grade);
				row.find('.package').html(member.maxItem);
				
				// row.find('.package').html(max_package);

				/* 펼침 추가정보
				row.find('.lv').addClass('s_v'+member.mb_level);
				row.find('.lv').text('V'+ member.mb_level);

				row.find('.recommend_num').text(member.cnt);
				row.find('.Blevel_num').text(member.treelvl);

				row.find('.deposit_num').text(member.mb_deposit_point);
				row.find('.name').text(member.mb_name);
				row.find('.mb_level').text('V'+ member.mb_level);
				row.find('.recommend_name').text(member.mb_recommend);
				row.find('.email').text(member.mb_email);

				row.find('.legsale_num').text( numberWithCommas(member.stacks));
				row.find('.sales_day').text(member.sales_day);

				row.find('.block').text(status);
				*/
				vHtml.append(row);
			});

			$('#levelStructure').html(vHtml.html());
			$("html, body").animate({ scrollTop: 0 }, "fast");

			/*상세보기*/
			$('.accordion_wrap dl dd').css("display", "none");

			$('.accordion_wrap dt').click(function() {
				
				// console.log($(this).data('depth'));

				var target_under_num = $(this).data('depth')+2;
				for(i=10; i >= target_under_num; i-- ){
					$('.dl_'+i+'depth').stop().slideToggle();
				}
			});


		}).fail(function(e) {
			console.log( e );
		})
	}

	function getList(member_no, type){
		var url = "/util/structure_member_list.php";
		var dataResult =[];
		var curency = "<?=$curencys[0]?>";
		
		$.ajax({  
			url: url,  
			data: {
				mb_no: member_no,
				type : type
			},  
			type: 'POST',
			dataType : 'json',
			async: false, 
			success: function(data){
				
				$('#total').text(data.length);
				var vHtml = $('<div>');
				
				$.each(data, function( index, member ) {
					var row = $('#dup .lvl-container').clone();

					// 사용안함 - recommend에서 처리
					/* $.ajax({ 
						url: "/util/ajax.max_package.php",  
						data: {mb_id : member.mb_id},  
						type: 'POST',
						dataType : 'json',
						async: false, 
						success: function(res){
							member.maxItem = res.result;
						}
					}); */

					var user_icon = '';
					if(member.mb_level == 0) {
						// user_icon = "<img src='<?=G5_THEME_URL?>/img/user.png' alt='일반회원'>";
						user_icon ="<span class='user_icon lv0'><i class='ri-vip-crown-line'></i></span>";
					}else if(member.mb_level > 9){
						// user_icon = "<img src='<?=G5_THEME_URL?>/img/user_admin.png' alt='관리자'>";
						user_icon ="<span class='user_icon lv10'><i class='ri-vip-crown-fill'></i></span>";
					}else{
						if(member.mb_level == 2){
							// user_icon  = "<img src='<?=G5_THEME_URL?>/img/user_2.png' alt='센터회원'>";
							user_icon ="<span class='user_icon lv2'><i class='ri-vip-crown-line'></i></span>";
						}else if(member.mb_level == 3){
							// user_icon  = "<img src='<?=G5_THEME_URL?>/img/user_3.png' alt='인정회원'>";
							user_icon ="<span class='user_icon lv3'><i class='ri-vip-crown-line'></i></span>";
						}else{
							// user_icon = "<img src='<?=G5_THEME_URL?>/img/user_general.png' alt='정회원'>";
							user_icon ="<span class='user_icon lv1'><i class='ri-vip-crown-line'></i></span>";
						}
					}
					
					row.addClass(depthMap[member.depth -1 ]);
					row.addClass(gradeMap[member.grade]);
					row.find('._lvl').attr('data-depth',member.depth);

					row.find('.lvl-icon').html(user_icon);
					row.find('#lvl-userid').text(member.mb_id);
					row.find('#lvl-username').text(' [ ' + member.mb_name + ' ]');
					// row.find('.level').addClass('color'+member.grade);
					row.find('.level').html(' <i class="ri-star-fill pack_f_'+member.grade +'"></i> ' + member.grade);
					row.find('.lvl-container').attr('class',"gr_"+member.grade);
					row.find('.direct_cnt').html("<i class='ri-user-received-2-line'></i>" + member.cnt);
					row.find('.package').addClass('color'+member.grade);
					row.find('.package').html("<i class='ri-vip-diamond-fill'></i>" + member.rank);
					// row.find('.pv').html(" 매출 : <strong class='hp'>" + Price(member.mb_rate)+' mh/s </strong>');
					row.find('.acc').html("승급USDT : <strong class='pv'>"+ Price(Number(member.recom_sales)) + ' ' + curency  + "</strong>");

					vHtml.append(row);
				});

				$('#levelStructure').html(vHtml.html());
				$("html, body").animate({ scrollTop: 0 }, "fast");

				/*상세보기*/
				$('.accordion_wrap dl dd').css("display", "none");

				$('.accordion_wrap dt').click(function() {
					
					var target_under_num = $(this).data('depth')+1;
					console.log("depth ::" + target_under_num);

					for(i=12; i >= target_under_num; i-- ){
						$('.dl_'+i+'depth').slideToggle();
					}

				});
			},
			
		});
	}


	// 찾는 아이디에서 조상까지의 경로를 표시
	function getLeg(lastParent, findId){

		$.get("/util/level_structure.leg.php", {
			lastParent : lastParent,
			findId : findId
		}).done(function( data ) {
			var reversed = data.reverse();
			//console.log(reversed);

			var vHtml = $('<div>');
			$.each(reversed, function( index, str ) {
				if(vHtml.html() == ''){
					vHtml.append($('<span>').addClass('mbId').text(str));
				}else{
					vHtml.append(" <i class='ri-arrow-right-s-fill'></i> ").append($('<span>').addClass('mbId').text(str));
				}
			});
			$('.leg-view-container .gray').html(vHtml.html());
		}).fail(function(e) {
			console.log( e );
		});
	}
	</script>

	<main>		
		<div class="container nopadding nomargin">
			<section class="structure_wrap">
				<!--<p>데이터 크기로 인해 한번에 5대씩 화면에 나타납니다</p>-->
				<div class="btn_input_wrap" style='background:white'>
					<div class="bin_top">회원 검색</div>
					<ul class="row align-items-center">
						<li class="col-9 user_search_wrap">
							<input type="text" id="now_id" class="" style='background:#eff3f9;color:black;border:1px solid #d9dfe8' placeholder="회원찾기"/>
						</li>
						<li class="col-3 search_btn_wrap">
							<button type="button" class="btn wd b_skyblue" id="binary_search" style="padding: 12px 10px;" onclick="member_search();"><i class="ri-search-line"></i></button>
						</li>
					</ul>
				</div>

				<div class="btn_input_wrap">
					<div class="bin_top">추천 계보</div>
					<div class='legbox'>
						<div class="leg-view-container">
							<div class="gray"></div>
						</div>
					</div>
				</div>
				
				<!-- <div class="desc font_red" style='font-size:11px'>[ 금액단위 : 만원 ]</div> -->
				<div class="main-container content-box tree-container nomargin">
					<div class="bin_top">추천 조직도</div>					
					<div id="levelStructure" class="accordion_wrap"></div>
				</div>
				<div style="display:none;" id="dup">
					<dl class="lvl-container" >
						<dt class="_lvl">
							<!-- <p class="lv"></p> -->
							<!-- <i class="ri-vip-crown-line" style="font-size: 1.8em; margin-right: 5px; color: #e5c6b1;vertical-align:-webkit-baseline-middle;border: 3px solid #e5c6b1;border-radius:50%;"></i>
							<i class="ri-account-circle-line" style="font-size: 2.5em; margin-right: 5px;"></i> -->
							<div><span class="lvl-icon"></span></div>
							<div>
								<span id="lvl-userid" class="lvl-username"></span>
								<span id="lvl-username" class="lvl-username"></span>
								<span class="level badge"></span>
								<span class="direct_cnt badge"></span>
								<span class="badge package"></span>								
								<!-- <span class='divided'></span> -->
								<p class='mbpoint'>
									<!-- <span class="pv"></span>&nbsp -->
									<span class="acc"></span>
								</p>
							</div>
							<div style="margin-left:auto"><span class='toggle'><i class="ri-line-height"></i></span></div>
						</dt>
					</dl>
				</div>
			</section>
		</div>
	</main>

	<div class="structure_search_container">
		<div class="structure_search_result" id="structure_search_result"></div>
		<div class="b_skyblue result_btn">닫기</div>
	</div>

	<script>
		$(function(){
			$(".top_title h3").html("<span >추천조직도</span>")
		});


	</script>
<? include_once(G5_THEME_PATH.'/_include/tail.php'); ?>
