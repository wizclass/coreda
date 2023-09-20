<?php
include_once('./_common.php');
include_once(G5_THEME_PATH.'/_include/head.php');
include_once(G5_THEME_PATH.'/_include/wallet.php');
include_once(G5_THEME_PATH.'/_include/gnb.php');

if($is_admin){
	header('Location: /page.php?id=support_center.admin');
}
?>


	<script>								
		var $idx='<?=$_GET['idx']?>';
		var topicOption = {
			0 : '일반',
			1 : '해킹',
			2 : '보너스',
			3 : '지갑',
			4 : '계좌'
		};
		var $selected;

		var $msg='<?=$_GET['msg']?>';
		if($msg){
			commonModal('Alert','<i class="fas fa-exclamation-triangle red"><br>'+$msg);
		}

		$(function() {
			$(document).on('click','.btn.send' ,function(e) {
				if (!$(this).parents('.chat-input').find('.message').val()) {
					dialogModal("문의 알림", "문의하실 내용을 적어주세요.", 'warning');
					return false;
				} else {
					$('#ticketChildForm [name=idx]').val($(this).attr('idx'));
					$('#ticketChildForm [name=content]').val($(this).parents('.chat-input').find('.message').val());
					$('#ticketChildForm').append($(this).parents('.chat-input').find('.messageFile').clone());
					$('#ticketChildForm').submit();
				}
			});

			$(document).on('keypress', function(event){
				if (event.which == '13') {
					event.preventDefault();
				}
			});

			// 댓글 펼치기
			$(document).on('click','.ticket-header' ,function(e) {
				$selected = $(this).next();
				$(this).toggleClass('active');
				$(this).siblings('div').removeClass('active');

				if($(this).hasClass('active')) {
					$selected.addClass('active');
				} else {
					$selected.removeClass('active');
				}

				getComment($(this).attr('idx'));
			});



			// 탭클릭
			$('.support-panels .support-tabs li').on('click', function(e) {
				$('.support-panels .support-tabs li').removeClass('active');
				$('.support-panels .panel').removeClass('active').hide();

				$(this).addClass('active');
				$('#' + $(this).attr('rel')).addClass('active').fadeIn(300);

				if($(this).attr('rel') == 'active-tickets'){
					// 액티브 티켓 선택
					$.get( "/util/support_center.ticket.php",{
						is_closed : 0
					}).done(function( data ) {
						// console.log(data);
						var vHtml = $('<div>');
						$.each(data, function( index, ticket ) {
							var row = $('#dup').clone();
							row.find('.ticket-header').attr('idx', ticket.idx);
							row.find('.topic').append('['+ topicOption[ticket.topic] +'] ');
							row.find('.subject').append(ticket.subject);
							row.find('.create_date').text(ticket.create_date);
							row.find('.messageFile').attr({
								id : 'add_messageFile' + ticket.idx,
								onchange: `FileSizeChk('add_messageFile${ticket.idx}')`
							});
							row.find('.file_label').attr('for', 'add_messageFile' + ticket.idx);
							row.find('.btn.send').attr('idx', ticket.idx);
							row.find('.btn.cl').attr('idx', ticket.idx);
							vHtml.append(row.html());
						});
						$('#active-tickets').html(vHtml.html());

						if($idx){
							$('.ticket-header[idx='+$idx+']').trigger('click');
						}

					}).fail(function(e) {
						console.log( e );
					});
				}else if($(this).attr('rel') == 'closed-tickets'){
					// 클로즈드 티켓 선택
					$.get( "/util/support_center.ticket.php",{
						is_closed : 1
					}).done(function( data ) {
						var vHtml = $('<div>');
						$.each(data, function( index, ticket ) {
							var row = $('#dup').clone();
							row.find('.ticket-header').attr('idx', ticket.idx);
							row.find('.topic').append('['+ topicOption[ticket.topic] +'] ');
							row.find('.subject').append(ticket.subject);
							row.find('.create_date').text(ticket.create_date);
							row.find('.chat-input').remove();
							vHtml.append(row.html());
						});
						$('#closed-tickets').html(vHtml.html());
					}).fail(function(e) {
						console.log( e );
					});
				}
			});

			// submit ticket
			$('#ticket').on('click', function(e) {

				if($('#subject').val() != '' && $('#content').val() != ''){

					$('#ticketForm [name=lang]').val($('#lang').val());
					dialogModal("등록성공", "1:1 문의사항이 등록되었습니다.", 'success');

					$('#modal_return_url, #dialogModal').on('click',function() {
						$('#ticketForm').submit();
					})

				} else{
					// commonModal('Alert','<i class="fas fa-exclamation-triangle red"><h4><br>Please fill in the details.</h4>',200);
					dialogModal("문의 알림", "문의하실 내용을 적어주세요.", 'warning');
				}
			});

			$(document).on('keydown','.message' ,function(e) {
				if(e.which == 13) {
					e.preventDefault();
					$(this).parent().siblings('.input-group').find('.send').trigger('click');
				}
			});

			// 티켓종료
			$(document).on('click','.btn.cl' ,function(e) {
				console.log("closed");
				$.ajax({
					url: '/util/support_center.ticket.php',
					type: 'PUT',
					data: {
						idx : $(this).attr('idx')
					},
					success: function(result) {
						$('.support-panels .support-tabs li[rel=active-tickets]').trigger('click');
						dialogModal("문의 종료","해당 문의를 종료하였습니다.","success");

						$('#modal_return_url, #dialogModal').click(function () {
							location.reload();
						});
					}
				});
			});

			if($idx){
				$('.support-panels .support-tabs li[rel=active-tickets]').trigger('click');
			}
		});

		// 댓글 내용 가져오기
		function getComment(paramIdx){
			$selected.find('.chat').empty();
			$.get( "/util/support_center.ticket.child.php",{
				idx : paramIdx
			}).done(function( data ) {
				var vHtml = $('<div>');

				$.each(data.list , function( index, obj ) {
					var row = $('#dup2').clone();
					if(obj.mb_no == 1){ // 관리자
						row.find('.message').addClass('support-message');
						row.find('.name').text('FIJI Support');
					}else{
						row.find('.message').addClass('member-message');
						row.find('.name').text(obj.mb_id);
					}
					row.find('.content').text(obj.content);
					row.find('.time').text(obj.create_date);

					if(obj.bf_source){
						var btn = $('<a class="file_addon">');
						btn.attr('href','<?=G5_URL?>/bbs/download.php?bo_table=supportCenterChild&wr_id=' + obj.wr_id + '&no=' + obj.bf_no);
						// btn.text(obj.bf_source);
						btn.html(`<i class="ri-download-2-line"></i>${obj.bf_source}`)
						row.find('.writer').prepend(btn);
					}
					vHtml.append(row.html());
				});

				// if(data.file){
				// 	// console.log(data.file);
				// 	var btn = $('<a class="file_addon">');
				// 	btn.attr('href','<?=G5_URL?>/bbs/download.php?bo_table=supportCenter&wr_id=' + data.file.wr_id + '&no=' + data.file.bf_no);
				// 	// btn.text(data.file.bf_source);
				// 	btn.html(`<i class="ri-download-2-line"></i>${data.file.bf_source}`);
				// 	vHtml.find('.message.member-message .writer').prepend(btn);
				// }

				if(data.file){
					var btn = $('<a class="file_addon">');
					btn.attr('href','<?=G5_URL?>/bbs/download.php?bo_table=supportCenter&wr_id=' + data.file.wr_id + '&no=' + data.file.bf_no);
					btn.html(`<i class="ri-download-2-line"></i>${data.file.bf_source}`);
					// btn.text(data.file.bf_source);
					// vHtml.find('.message.member-message').last().append(btn);
					vHtml.find('.message.member-message .file').last().html(btn);
				}

				$selected.find('.chat').append(vHtml.html());
				//$selected.css('height', $selected.prop('scrollHeight') + 'px');

			}).fail(function(e) {
				console.log( e );
			});
		}

		function FileSizeChk(param) {
			var File_Size = document.getElementById(param).files[0].size;

			if( Number(File_Size) >= 5242880){
				alert("첨부파일이 5MB 이상입니다. <?=$config['cf_admin_email']?> 로 보내주세요.");
				$("#"+param).val("");
			}

		}

		function LoadImg(value) {
			if(value.files && value.files[0]) {
				var reader = new FileReader();
					reader.onload = function (e) {
						$('#LoadImg').attr('src', e.target.result);
					}
				reader.readAsDataURL(value.files[0]);
			}
		}


	</script>
<main>
	<section class="con90_wrap support_center">
		<div class="main-container dash_contents">
			<div id="body-wrapper">
				<div class="support-container">
					<div class="support-panels">
						<ul class="support-tabs content-box">
							<li rel="open-new-ticket" class="active">새 티켓 열기</li>
							<li rel="active-tickets">활성화 티켓</li>
							<li rel="closed-tickets">비활성화 티켓</li>
						</ul>
						<div id="open-new-ticket" class="panel active">
							<form id="ticketForm" action ="/util/support_center.ticket.php" method="post" enctype="multipart/form-data">
								<input type="hidden" name="lang" >
								<div class="input-group mb-3">
									<div class="input-group-prepend">
										<label class="input-group-text" for="topic">주제 선택</label>
									</div>
									<select class="custom-select" name="topic" id="topic">
										<option value="0" selected >일반</option>
										<option value="1" >해킹</option>
										<option value="2" >보너스</option>
										<option value="3" >지갑</option>
										<option value="4" >계좌</option>
									</select>
								</div>
								<div class="input-group mb-3">
									<div class="input-group-prepend">
										<span class="input-group-text" id="basic-addon1"> 제목</span>
									</div>
									<input type="text" class="form-control" aria-label="Subject" aria-describedby="basic-addon1" name="subject" id="subject" placeholder="문의 제목 입력">
								</div>
								<div class="input-group mb-3">
									<div class="input-group-prepend">
										<span class="input-group-text">무엇을<br> 도와 드릴까요</span>
									</div>
									<textarea class="form-control" aria-label="With textarea" name="content" id="content" ></textarea>
								</div>
								<div class="input-group" style="border: none">
									<input class="upload-name1" placeholder="선택된 파일 없음" readonly>
									<input type="file" multiple id="addFile" name="bf_file[]" onChange="FileSizeChk('addFile')" accept=".jpg, .png, .pdf" accept="image/*;capture=camera">
									<label class="file_label2" for="addFile">파일선택</label> 
									<button type="button" class="btn_del del1">&times;</button>
								</div>
								<span class="guide_text">5MB 미만 jpg, png, pdf 파일만 첨부 가능합니다</span>
								<div class="submit-button">
									<div class="btn wd btn_send font_white" id="ticket"> 티켓 제출</div>
								</div>
							</form>
						</div>
						<div id="active-tickets" class="panel"></div>
						<div id="closed-tickets" class="panel"></div>
					</div>
				</div>
			</div>
		</div>
		<div style="display:none;" id="dup">
			<div class="ticket-header">
				<div class="dp-flex">
					<strong class="topic"></strong>
					<span class="ticket-title subject"></span>
				</div>
				<span class="ticket-time create_date"></span>
			</div>
			<div class="chat-box">
				<div class="chat"></div>
				<div class="chat-input">
						<div class="input-group mb-2">
							<input type="text" class="form-control message" placeholder="내용입력" aria-label="Message" aria-describedby="basic-addon2" data-i18n="[placeholder]support.메시지">
						</div>
						<div class="custom-file mb-2">
							<input class="upload-name2" placeholder="선택된 파일 없음" readonly>
							<input type="file" id="" class="messageFile" name="bf_file[]" onChange="" accept=".jpg, .png, .pdf" accept="image/*;capture=camera">
							<label class="file_label" for="">파일선택</label> 
							<button type="button" class="btn_del del2">&times;</button>
						</div>
						<div class="input-group mb-2 noborder">
							<div class="input-group-append" style="flex-grow: 1">
								<button class="btn wd btn-primary send main_btn" type="button" data-i18n='support.보내기'>티켓제출</button>
							</div>
						</div>
						<div>
							<button class="btn wd cl" type="button">티켓비활성화</button>
						</div>
					</div>
			</div>
		</div>
		<div style="display:none;" id="dup2" >
		<div class="message mb-2">
			<span class="content"></span><br>
			<a class="file" href=""></a>
			<p class="writer"><span class="name">V7Wallet Support</span> | <span class="time" >12:40 PM</span></p>
		</div>
		</div>
		<div style="display:none;" >
			<form id="ticketChildForm" action ="/util/support_center.ticket.child.php" method="post" enctype="multipart/form-data" >
				<input type="hidden" name="idx" >
				<input type="hidden" name="content" >
			</form>
		</div>
		<div class="gnb_dim"></div>
	</section>
</main>

	<script>
		$(function(){
			$(".top_title h3").html("<span >1:1문의사항</span>");
		});

		let btnDel1 = $('.btn_del.del1');

		$('#addFile').on('change', function(e) {
			if(e.target.files.length > 0) {
				$('.upload-name1').val(e.target.files[0].name);
				btnDel1.show();
			}
		});
		btnDel1.on('click', function(){
			$('.upload-name1').val('');
			btnDel1.hide();
		})

		$(document).on('change','.messageFile',function(e) {
			let btnDel2 = $('.btn_del.del2');

			if(e.target.files.length > 0) {
				$(this).siblings('.upload-name2').val(e.target.files[0].name);
				$(this).siblings(btnDel2).show();
			}

			btnDel2.on('click', function(){
				$(this).hide();
				$(this).siblings('.upload-name2').val('');
			});
		});
	</script>

<? include_once(G5_THEME_PATH.'/_include/tail.php'); ?>
