<script>

/*
$(function(){
		 
		//파일올리기
		 var fileTarget = $('.filebox .upload-hidden'); 
		 fileTarget.on('change', function(){ // 값이 변경되면 
			 if(window.FileReader){ // modern browser 
				 var filename = $(this)[0].files[0].name; 
			 } else { // old IE 
				 var filename = $(this).val().split('/').pop().split('\\').pop(); // 파일명만 추출 
			 } // 추출한 파일명 삽입 
			 $(this).siblings('.upload-name').val(filename); 
		 }); 
});

*/
// var debug = '<?=$is_debug?>';
var debug = "";

// 인풋 세자리콤마
$(document).on('keyup', 'input[inputmode=numeric]', function (event) {
	this.value = this.value.replace(/[^0-9]/g, '');   // 입력값이 숫자가 아니면 공백
	this.value = this.value.replace(/,/g, '');          // ,값 공백처리
	this.value = this.value.replace(/\B(?=(\d{3})+(?!\d))/g, ","); // 정규식을 이용해서 3자리 마다 , 추가 	
});

// 인풋 숫자 + -
$(document).on('keyup', 'input[inputmode=price]', function (event) {
	this.value = this.value.replace(/[^0-9]/g, '');   // 입력값이 숫자가 아니면 공백
	this.value = this.value.replace(/,/g, '');          // ,값 공백처리
	this.value = this.value.replace(/\B(?=(\d{3})+(?!\d))/g, ","); // 정규식을 이용해서 3자리 마다 , 추가 	
});

// 숫자에 콤마 찍기
function Price(x) {
	return String(x).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

// 숫자에 콤마 제거
function conv_number(val) {
	number = val.replace(/,/g, '');
	return number;
}

// 숫자만 입력
function onlyNumber(id) {
	document.getElementById(id).oninput = function () {
		// if empty
		if (!this.value) return;

		// if non numeric
		let isNum = this.value[this.value.length - 1].match(/[0-9.]/g);
		if (!isNum) this.value = this.value.substring(0, this.value.length - 1);
	}
}


// 보너스게이지
function move(bonus_per, main = 0) {
	var total_bonus_point = 0;
	if (bonus_per != undefined) {
		var total_bonus_point = bonus_per;
	}

	if (debug) { console.log(' total_bonus_point  =  ' + total_bonus_point); }

	if (total_bonus_point >= '100') { total_bonus_point = '100' };

	var elem = document.getElementById("total_B_bar");
	var width = 1;
	var id = setInterval(frame, 20);
	function frame() {
		if (width >= 100) {
			$('#total_B_bar').addClass('deg100');
			$('#total_B_bar').addClass('active');
			$('.bonus_per').addClass('active')
		}

		if (width >= 75) {
			$('#total_B_bar').addClass('deg75');
		}
		if (width >= 50) {
			$('#total_B_bar').addClass('deg50');
		}
		if (width >= 25) {
			$('#total_B_bar').addClass('deg25');
		}

		if (width >= total_bonus_point) {
			clearInterval(id);

			// 수당초과시 팝업 
			/* if(total_bonus_point > 75 && main == 1){dialogModal('Total Bonus', 'Total Bonus more than 75%', 'warning');} */

		} else {
			width++;
			elem.style.width = width + '%';
		}
	}
}

function go_to_url(target) {
	location.href = "/page.php?id=" + target;
}

function getCookie(name) {

	var i, x, y, ARRcookies = document.cookie.split(";");
	for (i = 0; i < ARRcookies.length; i++) {

		x = ARRcookies[i].substr(0, ARRcookies[i].indexOf("="));

		y = ARRcookies[i].substr(ARRcookies[i].indexOf("=") + 1);

		x = x.replace(/^\s+|\s+$/g, "");

		if (x == name) {

			return unescape(y);

		}
	}

}

function setCookie(name, value, days) {
	if (days) {
		var date = new Date();

		date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));

		var expires = "; expires=" + date.toGMTString();
	} else {
		var expires = "";
	}
	document.cookie = name + "=" + value + expires + "; path=/";
}


function commonModal(title, htmlBody, bodyHeight) {
	$('#commonModal').modal('show');
	$('#commonModal .modal-header .modal-title').html(title);
	$('#commonModal .modal-body').html(htmlBody);
	if (bodyHeight) {
		$('#commonModal .modal-body').css('height', bodyHeight + 'px');
	}
	$('#closeModal').focus();
}

function confirmModal(title, htmlBody, bodyHeight) {
	$('#confirmModal').modal('show');
	$('#confirmModal .modal-header .modal-title').html(title);
	$('#confirmModal .modal-body').html(htmlBody);
	if (bodyHeight) {
		$('#confirmModal .modal-body').css('height', bodyHeight + 'px');
	}
	$('#confirmModal').focus();
}

function dialogModal(title, htmlBody, category, dim = true) {

	$('#dialogModal').modal('show');
	$('#dialogModal .modal-header .modal-title').html(title);

	if (dim == false) {
		var dimhide = '';
	} else {
		var dimhide = "dimHide();";
	}

	if (category == 'success') {
		$('#dialogModal .modal-body').html("<div class=chkimg><img src='" + g5_url + "/img/check_basics.png'></div>" + htmlBody);
		$('#dialogModal .modal-footer').html("<button type='button' class='btn wd btn_defualt closed' data-dismiss='modal' id='modal_return_url' onclick='" + dimhide + "'>닫기</button>");
	}
	else if (category == 'confirm') {
		$('#dialogModal .modal-body').html("<div class=chkimg><img src='" + g5_url + "/img/notice.png'></div>" + htmlBody);
		$('#dialogModal .modal-footer').html("<button type='button' class='btn btn-secondary cancle' data-dismiss='modal' onclick='" + dimhide + "'>취소</button> <button type='button' class='btn btn-primary confirm' id='modal_confirm' data-dismiss='modal' >확인</button>");

	} else if (category == 'warning') {
		$('#dialogModal .modal-body').html("<div class=chkimg><img src='" + g5_url + "/img/notice.png'></div>" + htmlBody);
		$('#dialogModal .modal-footer').html("<button type='button' class='btn wd btn_defualt closed' data-dismiss='modal' id='modal_return_url' onclick='" + dimhide + "'>닫기</button>");
	} else if (category == 'input_confirm') {
		$('#dialogModal .modal-body').html("<div class=chkimg><img src='" + g5_url + "/img/notice.png'></div>" + htmlBody);
		$('#dialogModal .modal-footer').html("<button type='button' class='btn btn-secondary cancle' data-dismiss='modal' onclick='" + dimhide + "'>취소</button> <button type='button' class='btn btn-primary confirm' id='modal_confirm' data-dismiss='modal' >확인</button>");
	}
	else if (category == 'failed') {
		$('#dialogModal .modal-body').html("<div class=chkimg><img src='" + g5_url + "/img/notice.png'></div>" + htmlBody);
		$('#dialogModal .modal-footer').html("<button type='button' class='btn wd btn_defualt closed' data-dismiss='modal' id='modal_return_back' onclick='" + dimhide + "'>닫기</button>");
	}

	$('#dialogModal').focus();

}

</script>