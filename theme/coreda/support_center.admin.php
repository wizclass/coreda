<?php
include_once('./_common.php');
include_once(G5_THEME_PATH.'/_include/head.php');
include_once(G5_THEME_PATH.'/_include/wallet.php');
include_once(G5_THEME_PATH.'/_include/gnb.php');

if(!$is_admin){
	header('Location: /page.php?id=support_center');
}
?>

	<script>
		var topicOption = {
			0 : '일반',
			1 : '해킹',
			2 : '보너스',
			3 : '지갑',
			4 : '계좌'
		};

		$(function() {

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

			// 코멘트 달기
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

						$('#modal_return_url, #dialogModal').on('click',function() {
							location.reload();
						});
					}
				});
			});

			$('.support-panels .support-tabs li').on('click', function(e) {
				$('.support-panels .support-tabs li').removeClass('active');
				$('.support-panels .panel').removeClass('active').hide();

				$(this).addClass('active');
				$('#' + $(this).attr('rel')).addClass('active').fadeIn(300);

				if($(this).attr('rel') == 'active-tickets'){
					$.get( "/util/support_center.ticket.php",{
						is_closed : 0
					}).done(function( data ) {
						// console.log(data);
						makeList('#active-tickets',data);
					});
				}else if($(this).attr('rel') == 'closed-tickets'){
					$.get( "/util/support_center.ticket.php",{
						is_closed : 1
					}).done(function( data ) {
						makeList('#closed-tickets',data);
					});
				}else if($(this).attr('rel') == 'answered-tickets'){
					$.get( "/util/support_center.ticket.php",{
						is_closed : 1,
						is_answer : 1
					}).done(function( data ) {
						makeList('#answered-tickets > .list',data);
					});
				}
			});

			$('.support-panels .support-tabs li[rel=active-tickets]').trigger('click');
		});

		function makeList(tabId, data){
			var vHtml = $('<div>');
			$.each(data, function( index, ticket ) {
				var row = $('#dup').clone();
				row.find('.ticket-header').attr('idx', ticket.idx);
				row.find('.topic').append('['+ topicOption[ticket.topic] +'] ');
				row.find('.subject').append(ticket.subject);
				row.find('.create_date').text(ticket.create_date);

				if(tabId == '#active-tickets') {
					row.find('.custom-file input[name="bf_file[]"]').attr({
						id: 'customFile' + ticket.idx,
						onchange: `FileSizeChk('customFile${ticket.idx}')`
					});
					row.find('.file_label').attr('for', 'customFile' + ticket.idx);
				}

				if(tabId == '#answered-tickets > .list') {
					row.find('.custom-file input[name="bf_file[]"]').attr({
						id: 'answeredCustomFile' + ticket.idx,
						onchange: `FileSizeChk('answeredCustomFile${ticket.idx}')`
					});
					row.find('.file_label').attr('for', 'answeredCustomFile' + ticket.idx);
				}				

				if(Number(ticket.is_closed)){
					row.find('.ticket-header').addClass('closed');
					row.find('.chat-input').remove();
				}else{
					row.find('.btn.send').attr('idx', ticket.idx);
					row.find('.btn.cl').attr('idx', ticket.idx);
				}

				vHtml.append(row.html());
			});
			$(tabId).html(vHtml.html());
		}

		function getComment(paramIdx){
			$selected.find('.chat').empty();
			// $selected.find('.chat-input .message').val('');
			$.get( "/util/support_center.ticket.child.php",{
				idx : paramIdx
			}).done(function( data ) {
				// console.log(data);
				var vHtml = $('<div>');

				$.each(data.list, function( index, obj ) {
					var row = $('#dup2').clone();

					if(obj.mb_no == 1){ // 관리자
						row.find('.message').addClass('support-message');
						row.find('.name').text('관리자');
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

				if(data.file){
					var row = $('#dup2').clone();
					var btn = $('<a class="file_addon">');

					btn.attr('href','<?=G5_URL?>/bbs/download.php?bo_table=supportCenter&wr_id=' + data.file.wr_id + '&no=' + data.file.bf_no);
					btn.html(`<i class="ri-download-2-line"></i>${data.file.bf_source}`);
					// btn.text(data.file.bf_source);
					// vHtml.find('.message.member-message').last().append(btn);
					vHtml.find('.message.member-message .file').last().html(btn);
					// row.find('.writer').prepend(btn);
				}
				$selected.find('.chat').append(vHtml.html());
				
			}).fail(function(e) {
				console.log( e );
			});
		}
    </script>
<main>
	<section class="con90_wrap">
		<div class="main-container dash_contents">
			<div id="body-wrapper">
				<div class="support-container">
					<div class="support-panels">
						<ul class="support-tabs content-box">
							<li rel="active-tickets" data-i18n='support.활성화 티켓'>활성화 티켓</li>
							<li rel="answered-tickets" data-i18n='support.답변 티켓'>답변 티켓</li>
							<li rel="closed-tickets" data-i18n='support.비활성화 티켓'>비활성화 티켓</li>
						</ul>
						<div id="active-tickets" class="container panel active"></div>
						<div id="answered-tickets" class="container panel">
							<div class="list"></div>
						</div>
						<div id="closed-tickets" class="container panel"></div>
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
						<input type="text" class="form-control message" placeholder="내용입력" aria-label="Message" aria-describedby="basic-addon2">
					</div>
					<div class="custom-file mb-2">
						<input class="upload-name2" placeholder="선택된 파일 없음" readonly>
						<input type="file" id="" class="messageFile" name="bf_file[]" onChange="" accept=".jpg, .png, .pdf" accept="image/*;capture=camera">
						<label class="file_label" for="">파일선택</label> 
						<button type="button" class="btn_del">&times;</button>
					</div>				
					<div class="input-group mb-2 noborder">
						<div class="input-group-append" style="flex-grow: 1">
							<button class="btn wd btn-primary send main_btn" type="button">티켓전송</button>
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
				<span class="content"> </span><br>
				<a class="file"></a>
				<p class="writer"><span class="name">Support</span> | <span class="time" >12:40 PM</span></p>
			</div>
		</div>
		<div style="display:none;" >
			<form id="ticketChildForm" action ="/util/support_center.ticket.child.php" method="post" enctype="multipart/form-data">
				<input type="hidden" name="idx">
				<input type="hidden" name="content">
			</form>
		</div>
		<div class="gnb_dim"></div>
	</section>
</main>

	<script>
		$(function(){
			$(".top_title h3").html("<span >1:1문의사항</span>");
		});

		function FileSizeChk(param) {		
			var File_Size = document.getElementById(param).files[0].size;

			if( Number(File_Size) >= 5242880){
				alert("첨부파일이 5MB 이상입니다. <?=$config['cf_admin_email']?> 로 보내주세요.");
				$("#"+param).val("");
			}
		}
		
		$(document).on('change','.messageFile',function(e) {
			if(e.target.files.length > 0) {
				$(this).siblings('.upload-name2').val(e.target.files[0].name);
				$(this).siblings('.btn_del').show();
			}

			$('.btn_del').on('click', function(){
				$(this).hide();
				$(this).siblings('.upload-name2').val('');
			});
		});		
	</script>
<? include_once(G5_THEME_PATH.'/_include/tail.php'); ?>