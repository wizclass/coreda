<?php
$myLang = 'kor';

if($_COOKIE['myLang'])
{
	$myLang = $_COOKIE['myLang'];
}
?>

<script>
$(document).ready(function(){

	function setCookie(cookie_name, value, days) {
	  var exdate = new Date();
	  exdate.setDate(exdate.getDate() + days);
	  var cookie_value = escape(value) + ((days == null) ? '' : ';    expires=' + exdate.toUTCString());
	  document.cookie = cookie_name + '=' + cookie_value;
	}

	/* $.i18n.init({
		resGetPath: '/locales/my/__lng__.json',
		load: 'unspecific',
		fallbackLng: false,
		lng: 'kor'
	}, function (t){
		$('body').i18n();
	}); */

	/* $('#lang').on('change', function(e) {
		$.i18n.setLng($(this).val(), function(){
			$('body').i18n();
		});
		console.log($(this).val());
		setCookie('myLang',$(this).val(),1,'/');
		//localStorage.setItem('myLang',$(this).val());
	}); */

	// $('#lang').val("<?=$myLang?>").change();
});
</script>


<section id="wrapper" class="<?if($menubar){echo "menu_back_gnb";}?>" >
<header>
	<?if($menubar){?>
	<div class="menuback">
		<a href="javascript:history.back();" class='back_icon'><i class="ri-arrow-left-s-line"></i></a>
	</div>
	<?}else{?>
	<div class="menu">
		<a href="#" class='menu_icon'><i class="ri-menu-2-line"></i></a>
	</div>
	<?}?>
	
	<?if(!$menubar){?>
	<nav class="left_gnbWrap">		
		<div class="gnb_top">
			<button type="button" class="close"><img src="<?=G5_THEME_URL?>/img/gnb/close.png" alt="close"></button>
		</div>
		<div class='user-content'>
			<ul class="user_wrap">
				<li>
					<p class='userid user_level' style='margin-left:10px;'><?=$user_icon?></p>
				</li>
				<li>
					<h4 class="font_weight user_name"><?=$member['mb_name']?>님</h4>
					<h4 class='user_id'><?=$member['mb_id']?></h4>
					<!-- <h4 class="font_weight user_id"><?=$member['mb_id']?>님</h4> -->
				</li>
			</ul>
		</div>
		<div class="b_line3"></div>
		<ul class="left_gnb">
			<li class="dashboard_icon <? if($_SERVER['REQUEST_URI'] === '/') {echo 'active';}?>">
				<a href="/">
					<div class="gnb_img_wrap"></div>
					<div class="gnb_title_Wrap">
						<span >대쉬보드</span>
					</div>
				</a>
			</li>
			<li class="profile_icon <? if($_GET['id'] === 'profile') {echo 'active';}?>">
				<a href="/page.php?id=profile">
					<div class="gnb_img_wrap"></div>
					<div class="gnb_title_Wrap">
						<span >개인정보&보안설정</span>
					</div>
				</a>
			</li>
			<?if($nw['nw_with'] == 'Y'){?>
			<li class="mywallet_icon <? if($_GET['id'] === 'mywallet') {echo 'active';}?>">
				<a href="/page.php?id=mywallet">
					<div class="gnb_img_wrap"></div>
					<div class="gnb_title_Wrap">
						<span >USDT 거래소</span>
					</div>
				</a>
			</li> 
			<?}?>
			<!-- <li class="mining_icon <? if($_GET['id'] === 'mining') {echo 'active';}?>">
				<a href="/page.php?id=mining">
					<div class="gnb_img_wrap"></div>
					<div class="gnb_title_Wrap">
						<span >마이닝</span>
					</div>
				</a>
			</li>
			<li class="mypool_icon <? if($_GET['id'] === 'mypool') {echo 'active';}?>">
				<a href="/page.php?id=mypool">
					<div class="gnb_img_wrap"></div>
					<div class="gnb_title_Wrap">
						<span >마이풀</span>
					</div>
				</a>
			</li>
			-->
			<?if($nw['nw_purchase'] == 'Y'){?>
			<li class="upstairs_icon <? if($_GET['id'] === 'upstairs') {echo 'active';}?>">
				<a href="/page.php?id=upstairs">
					<div class="gnb_img_wrap"></div>
					<div class="gnb_title_Wrap">
						<span >패키지구매</span>
					</div>
				</a>
			</li> 
			<?}?>
			<li class="bonus_history_icon <? if($_GET['id'] === 'bonus_history') {echo 'active';}?>">
				<a href="/page.php?id=bonus_history">
					<div class="gnb_img_wrap"></div>
					<div class="gnb_title_Wrap">
						<span >보너스내역</span>
					</div>
				</a>
			</li>
			
			<!-- <?if($member['center_use'] == 1){?>
			<li class="center_page_icon <? if($_GET['id'] === 'center_page') {echo 'active';}?>">
				<a href="/page.php?id=center_page">
					<div class="gnb_img_wrap"></div>
					<div class="gnb_title_Wrap">
						<span >센터회원관리</span> 
					</div>
				</a>
			</li>
			<?}?>
			-->

			<? if($member['mb_level'] > 0){?>
			<li class="recommend_icon <? if($_GET['id'] === 'structure') {echo 'active';}?>">
				<a href="/page.php?id=structure">
					<div class="gnb_img_wrap"></div>
					<div class="gnb_title_Wrap">
						<span >조직도</span>
					</div>
				</a>
			</li>
			<?}?>

			<!-- <li class="support_icon <?// if($_GET['id'] === 'binary') {echo 'active';}?>">
				<a href="/page.php?id=binary">
					<div class="gnb_img_wrap"></div>
					<div class="gnb_title_Wrap">
						<span >후원조직도</span>
					</div>
				</a>
			</li>
			<li class="support_icon <? //if($_GET['id'] === 'binary2') {echo 'active';}?>">
				<a href="/page.php?id=binary2">
					<div class="gnb_img_wrap"></div>
					<div class="gnb_title_Wrap">
						<span >후원조직도2</span>
					</div>
				</a>
			</li> -->
			<li class="notice_icon <? if($_GET['id'] === 'news') {echo 'active';}?>">
				<a href="/page.php?id=news">
					<div class="gnb_img_wrap"></div>
					<div class="gnb_title_Wrap">
						<span >공지사항</span>
					</div>
				</a>
			</li>
			<li class="question_icon <? if($_GET['id'] === 'support_center' || $_GET['id'] === 'support_center.admin') {echo 'active';}?>">
				<a href="/page.php?id=support_center">
					<div class="gnb_img_wrap"></div>
					<div class="gnb_title_Wrap">
						<span >1:1문의사항</span>
					</div>
				</a>
			</li>
			<li class="reffer_icon <? if($_GET['id'] === 'referral_link') {echo 'active';}?>">
				<a href="/page.php?id=referral_link">
					<div class="gnb_img_wrap"></div>
					<div class="gnb_title_Wrap">
						<span >추천인링크</span>
					</div>
				</a>
			</li>
			<?if($nw['nw_shop'] == 'Y'){?>
			<li class="shop_icon">
				<a href="/shop">
					<div class="gnb_img_wrap"></div>
					<div class="gnb_title_Wrap">
						<span >쇼핑몰</span>
					</div>
				</a>
			</li>
			<?}?>

			<div class='gnb_bottom text-center hidden'><i class="ri-arrow-down-s-line" style='font-size:20px;vertical-align:top'></i></div>
			<div id='gnb_language'>
				<p class='f_small title'>언어선택</p>
				<?include_once(G5_THEME_PATH.'/_include/lang.html')?>
			</div>
			
			<div class="logout_wrap">
				<a href="javascript:void(0);" class="logout_pop_open"><i class="ri-logout-box-r-line"></i><span>로그아웃</span></a>
				<a href="/page.php?id=member_term"><span>회원약관</span></a>
			</div>	
			<!-- <ul class="logout_wrap row">
				<li class="foot_btn logout_icon">
					<div class="gnb_img_wrap"></div>
					<div class="gnb_title_Wrap">
						<a href="javascript:void(0);" class="logout_pop_open"><span >로그아웃</span></a>
					</div>
				</li>
				<li class="h_line"></li>
				<li class="foot_btn terms_icon">
					<a href="/page.php?id=member_term"><span >회원약관</span></a>
				</li>
			</ul> -->

			<!-- <div class='gnb-footer'>
				<p class='copyright'>Copyright ⓒ 2021. WIZCLASS Co. ALL right reserved.</p>
			</div> -->
		</ul>		
	</nav>
	<?}?>

	<div class="top_title">
		<h3>
			<a href="/"><img src= "<?=G5_THEME_URL?>/img/title.png" alt="logo"></a>
			<?if($member['mb_level'] >= 9){?><button type="button" class='btn adm_btn' onclick="location.href= '<?=G5_ADMIN_URL?>'" ><i class="ri-user-settings-line"></i>Admin</button><?}?>
		</h3>
		<select name="" id="mode_select" >
			<option value="white">화이트</option>
			<option value="dark">다크</option>
		</select>
	</div>
</header>

<div id="loading" class="wrap-loading display-none"><span class="loading_img"></span></div>
<script>
	$( document ).ajaxStart(function() { 
		$('.wrap-loading').removeClass('display-none');
	});
	$( document ).ajaxStop(function() { 
		$('.wrap-loading').addClass('display-none');
	});

	$(function(){		
		let left_gnb = $('.left_gnb');
		let gHeight = $(window).height() - 270;

		if(left_gnb.height() >= gHeight) {
			$(".gnb_bottom").css('display','block');

			$(left_gnb).scroll(function () {
				var gnb_height = $(left_gnb).scrollTop();
				
				if(gnb_height > 10){
					$(".gnb_bottom i").attr('class','ri-arrow-up-s-line')
				}else if(gnb_height < 10){
					$(".gnb_bottom i").attr('class','ri-arrow-down-s-line')
				}
			}); 
		}
	});

	function move_to_shop(){
		<?php if(strpos($_SERVER['HTTP_USER_AGENT'],'webview//1.0') == true){ ?>
			App.moveToShop()
		<?php }else{?>

			var shop_url = "<?=SHOP_URL?>";
			var form = document.createElement("form");

				form.setAttribute("charset", "UTF-8");
				form.setAttribute("method", "Post");  //Post 방식
				form.setAttribute("action", "<?=SHOP_URL?>"); //요청 보낼 주소

				var hiddenField = document.createElement("input");
				hiddenField.setAttribute("type", "hidden");
				hiddenField.setAttribute("name", "mb_id");
				hiddenField.setAttribute("value", "<?=$member['mb_id']?>");
				form.appendChild(hiddenField);

				hiddenField = document.createElement("input");
				hiddenField.setAttribute("type", "hidden");
				hiddenField.setAttribute("name", "mb_password");
				hiddenField.setAttribute("value", "<?=$member['mb_password']?>");
				form.appendChild(hiddenField);

				hiddenField = document.createElement("input");
				hiddenField.setAttribute("type", "hidden");
				hiddenField.setAttribute("name", "gnu_to_mall");
				hiddenField.setAttribute("value", "gnu_to_mall");
				form.appendChild(hiddenField);

				document.body.appendChild(form);
				form.submit();
		<?php } ?>
	}
</script>