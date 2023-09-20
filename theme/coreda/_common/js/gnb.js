$(document).ready(function () {
	mobile_menu();
});

function mobile_menu() {
	/* 변수 선언 */
	var $menu = null;
	var $left_gnb = null; // 영역 전체
	var $depth1_wrap = null;
	var $depth1 = null;
	var $depth1_btn = null;

	/* 시작 함수 */
	function start() {
		init();
		init_event();
	}
	/* 변수 초기화 함수 */
	function init() {
		$menu = $('.menu');
		$left_gnb = $('.left_gnbWrap'); // 영역 전체
		$depth1_wrap = $('.left_gnb>li');
		$depth1 = $depth1_wrap.children('ul');
		$depth1_btn = $depth1_wrap.children('a.menu_tree');
	}
	/* 이벤트 함수 */
	function init_event() {

		/* 모바일 메뉴 버튼 클릭했을때 모바일 메뉴영역 나오게 하기 */
		$menu.click(function (event) {
			event.preventDefault();
			$left_gnb.addClass('on');
			$('.gnb_dim').css("display", "block");
			$('body').css({ "overflow": "hidden", "height": "100%" });
			/*
						$('.dim').on('scroll touchmove mousewheel', function(event) {
						  event.preventDefault();
						  event.stopPropagation();
						  return false;
						});
			*/
		});

		/* x버튼 눌렀을때 모바일 메뉴 닫기 */
		$('.close').click(function (event) {
			event.preventDefault();

			$left_gnb.removeClass('on');
			$('.gnb_dim').css("display", "none");
			$('body').css({ "overflow": "auto", "height": "inherit" });
			//	$('.dim').off('scroll touchmove mousewheel');

			// x버튼 누르면 시간차 약간두고 소메뉴 닫히게 하기
			setTimeout(function () {
				$depth1_btn.removeClass('on');
				$depth1.slideUp();
			}, 300)
		});

		$('.gnb_dim').click(function (event) {
			event.preventDefault();

			$left_gnb.removeClass('on');
			$('.gnb_dim').css("display", "none");
			$('body').css({ "overflow": "auto", "height": "inherit" });

			// x버튼 누르면 시간차 약간두고 소메뉴 닫히게 하기
			setTimeout(function () {
				$depth1_btn.removeClass('on');
				$depth1.slideUp();
			}, 300)
		});

		/* depth1의 각메뉴 클릭시 depth2 나오게 하기 */
		$depth1_btn.click(function (event) {
			console.log("click");
			event.preventDefault();

			var $this = $(this);
			var $this_ul = $this.siblings('ul');

			var check = $this.hasClass('on');

			if (check) {
				$this.removeClass('on');
				$this_ul.stop(true, true).slideUp();

			} else {
				$depth1_btn.removeClass('on');
				$depth1.stop().slideUp();
				$this.addClass('on');
				$this_ul.stop(true, true).slideDown();
			}



		});

		/* 디바이스 크기 변경시 모바일 메뉴영역 숨기기 */

		/*
				$(window).resize(function () {
					$left_gnb.removeClass('on');
				});
		 */

	}

	start(); // 시작 호출
}

// 슬라이드 컬러 자동생성 
function slide_color(index) {
	var num = index - 1;

	var backgrounds = ['#516feb', '#09c3fd', '#5ed2dc', '#373cbc', '#2b3a6d', '#afb6c9'];
	var colors = ['#516feb', '#09c3fd', '#5ed2dc', '#373cbc', '#2b3a6d', '#afb6c9'];
	$('.product_buy_wrap_' + num).css('background', backgrounds[num]);
	$('.product_buy_wrap_' + num).find('input').css('color', colors[num]);
}

// upstairs.php - upstair 내 보유 상품 존재하지 않을 시 슬라이드 숨김처리 및 박스추가
$(document).ready(function () {
	if ($('.no_data').text() != '') {
		$('.slide_product').hide();
		// $('.history_box').addClass('round').css('margin-left', '-15px').css('margin-right', '-15px');
	}
});


