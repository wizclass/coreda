<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가
include_once(G5_PLUGIN_PATH.'/Encrypt/rule.php');
include_once(G5_LIB_PATH.'/thumbnail.lib.php');


// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style.css">', 0);

function confirm_check($bool){
    global $write;
    if($write['wr_2'] == $bool){
        return 'checked';
    }
};

function wallet_type($val){
    if($val == 1){
        $result = "국내거래소 지갑";
    }else if($val == 2){
        $result = "해외거래소 지갑";
    }else if($val == 3){
        $result = "개인/기타 지갑";
    }
    return $result;
}

?>
<link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css" integrity="sha384-hWVjflwFxL6sNzntih27bfxkr27PmbbK/iSvJ+a4+0owXq79v+lsFkW54bOGbiDQ" crossorigin="anonymous">
<script src="<?php echo G5_JS_URL; ?>/viewimageresize.js"></script>
<style>
        #container{max-width:1200px;}
        #bo_v_con{min-height:100px !important}
        .holder {
            width: 100%;
            text-align: center;
            margin: 0px auto;
        }

        input[type="checkbox"] {
            display: none;
        }


        input[id^="checkbox-2-"] + label {
            text-align:left;
            background-color: dodgerblue;
            padding: 18px 20px 18px 23px;
            box-shadow: inset 0 50px 37px -30px rgba(255, 222, 197, 0.3);
            border-radius: 1000px;
            display: inline-block;
            position: relative;
            border-top: 1px solid dodgerblue;
            margin-right: 30px;
            font-size:14px;
            font-weight: 500;
            letter-spacing: 0px;
            color: #FFF;
            width: 213px;
            text-shadow: 0 1px 0 rgba(0, 0, 0, 0.2);
            border-bottom: 1px solid dodgerblue;
        }

        [id^="checkbox-2-"] + label:hover  {
            border-top: 1px solid royalblue;
            background: royalblue;
            box-shadow: inset 0 -50px 37px -30px rgba(255, 222, 197, 0.07);
        }

        [id^="checkbox-2-"] + label:active  {
            border-top: none;
            background: royalblue;
            padding: 19px 20px 18px 23px;
            box-shadow: inset 0 3px 8px rgba(129, 69, 13, 0.3), inset 0 -50px 37px -30px rgba(255, 222, 197, 0.07)	
        }

        [id^="checkbox-2-"] + label:after {
            content: ' ';
            border-radius: 100px;
            width: 32px;
            position: absolute;
            top: 12px;
            right: 12px;
            box-shadow: inset 0px 16px 40px rgba(0, 0, 0, 0.4);
            height: 32px;
        }

        [id^="checkbox-2-"] + label:before {
            content: ' ';
            border-radius: 100px;
            width: 20px;
            position: absolute;
            top: 18px;
            right: 18px;
            z-index: 999;
            box-shadow: inset 0px 16px 40px #FFF;
            height: 20px;
            display: none;
        }

        [id^="checkbox-2-"]:checked + label{
            border-top: 1px solid #28a745;
            background: #28a745;
            box-shadow: inset 0 -50px 37px -30px rgba(255, 222, 197, 0.07);
            
        }

        [id^="checkbox-2-"]:checked + label:before {
            display: block;
        }

        .kyc_reject_label{background-color:indianred !important;border:1px solid indianred !important;}
        .regdt{display: block;font-size:11px;margin:10px;}
        #bo_v_con{font-weight:600;line-height:24px;font-size:18px;font-family: "Inter", sans-serif;}
        .wallet_type{display:table;border:1px solid #ccc;padding:5px 10px;font-size:15px;margin-top:10px;}
    </style>

    <SCRIPT>
        function checkOnlyOne(element) {
        const checkboxes 
            = document.getElementsByName("kyc_result");
        
        checkboxes.forEach((cb) => {
            cb.checked = false;
        })
        element.checked = true;
        }

        // 삭제 검사 확인
        function del(href)
        {
            if(confirm("한번 삭제한 자료는 복구할 방법이 없습니다.\n\n정말 삭제하시겠습니까?")) {
                var iev = -1;
                if (navigator.appName == 'Microsoft Internet Explorer') {
                    var ua = navigator.userAgent;
                    var re = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
                    if (re.exec(ua) != null)
                        iev = parseFloat(RegExp.$1);
                }

                // IE6 이하에서 한글깨짐 방지
                if (iev != -1 && iev < 7) {
                    document.location.href = encodeURI(href);
                } else {
                    document.location.href = href;
                }
            }
        }
    </SCRIPT>

<link rel="stylesheet" href="<?=G5_THEME_URL?>/css/default.css">
<link rel="stylesheet" href="<?=$board_skin_url?>/style.css">

<!-- 게시물 읽기 시작 { -->

<article id="bo_v" style="width:<?php echo $width; ?>">
    <header>
        <h2 id="bo_v_title">
            <?php if ($category_name) { ?>
            <span class="bo_v_cate"><?php echo $view['ca_name']; // 분류 출력 끝 ?></span> 
            <?php } ?>
            <span class="bo_v_tit">
            <?php
            echo cut_str(get_text($view['wr_subject']), 70); // 글제목 출력
            ?>
			 <a href="/adm/member_form.php?sst=&sod=&sfl=&stx=&page=&w=u&mb_id=<?=$view['wr_subject']?>" target="_blank" style="display:inline-block; font-size:20px;"> | <i class="fas fa-user-circle"></i> 회원정보</a>
			</span>

			
        </h2>
    </header>

    <section id="bo_v_info">
        <h2>페이지 정보</h2>
        <span class="sound_only">작성자</span> <strong><?php echo $view['name'] ?><?php if ($is_ip_view) { echo "&nbsp;($ip)"; } ?></strong>
        <span class="sound_only">댓글</span><strong><a href="#bo_vc"> <i class="fa fa-commenting-o" aria-hidden="true"></i> <?php echo number_format($view['wr_comment']) ?>건</a></strong>
        <span class="sound_only">조회</span><strong><i class="fa fa-eye" aria-hidden="true"></i> <?php echo number_format($view['wr_hit']) ?>회</strong>
        <strong class="if_date"><span class="sound_only">작성일</span><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo date("y-m-d H:i", strtotime($view['wr_datetime'])) ?></strong>

    </section>

    <section id="bo_v_atc">
        <h2 id="bo_v_atc_title">본문</h2>

        <?php
        // 파일 출력
        $v_img_count = count($view['file']);
        if($v_img_count) {
            echo "<div id=\"bo_v_img\">\n";

            for ($i=0; $i<=count($view['file']); $i++) {
                if ($view['file'][$i]['view']) {
                    //echo $view['file'][$i]['view'];
                    echo get_view_thumbnail($view['file'][$i]['view']);
                }
            }

            echo "</div>\n";
        }
         ?>

        <!-- 본문 내용 시작 { -->
        <div id="bo_v_con">
            
            <?echo $view['wr_subject']."<br>"?>
            <?php echo get_view_thumbnail( Decrypt($view['content'],$secret_key,$secret_iv)); ?>
            <!-- <?print_R($view) ?> -->
            <div class='wallet_type'><?=wallet_type($view['wr_5'])?></div>
        </div>
        <?php //echo $view['rich_content']; // {이미지:0} 과 같은 코드를 사용할 경우 ?>
        <!-- } 본문 내용 끝 -->

        <?php if ($is_signature) { ?><p><?php echo $signature ?></p><?php } ?>


        <!--  추천 비추천 시작 { -->
        <?php if ( $good_href || $nogood_href) { ?>
        <div id="bo_v_act">
            <?php if ($good_href) { ?>
            <span class="bo_v_act_gng">
                <a href="<?php echo $good_href.'&amp;'.$qstr ?>" id="good_button" class="bo_v_good"><span class="sound_only">추천</span><strong><?php echo number_format($view['wr_good']) ?></strong></a>
                <b id="bo_v_act_good"></b>
            </span>
            <?php } ?>
            <?php if ($nogood_href) { ?>
            <span class="bo_v_act_gng">
                <a href="<?php echo $nogood_href.'&amp;'.$qstr ?>" id="nogood_button" class="bo_v_nogood"><span class="sound_only">비추천</span><strong><?php echo number_format($view['wr_nogood']) ?></strong></a>
                <b id="bo_v_act_nogood"></b>
            </span>
            <?php } ?>
        </div>
        <?php } else {
            if($board['bo_use_good'] || $board['bo_use_nogood']) {
        ?>
        <div id="bo_v_act">
            <?php if($board['bo_use_good']) { ?><span class="bo_v_good"><span class="sound_only">추천</span><strong><?php echo number_format($view['wr_good']) ?></strong></span><?php } ?>
            <?php if($board['bo_use_nogood']) { ?><span class="bo_v_nogood"><span class="sound_only">비추천</span><strong><?php echo number_format($view['wr_nogood']) ?></strong></span><?php } ?>
        </div>
        <?php
            }
        }
        ?>
        <!-- }  추천 비추천 끝 -->
    </section>

    <div id="bo_v_share">
        <!--<?php if ($scrap_href) { ?><a href="<?php echo $scrap_href;  ?>" target="_blank" class="btn btn_b03" onclick="win_scrap(this.href); return false;"><i class="fa fa-thumb-tack" aria-hidden="true"></i> 스크랩</a><?php } ?>-->

        <?php
        include_once(G5_SNS_PATH."/view.sns.skin.php");
        ?>
    </div>

    <?php
    $cnt = 0;
    if ($view['file']['count']) {
        for ($i=0; $i<count($view['file']); $i++) {
            if (isset($view['file'][$i]['source']) && $view['file'][$i]['source'] && !$view['file'][$i]['view'])
                $cnt++;
        }
    }
     ?>

    <?php if($cnt) { ?>
    <!-- 첨부파일 시작 { -->
    <section id="bo_v_file">
        <h2>첨부파일</h2>
        <ul>
        <?php
        // 가변 파일
        for ($i=0; $i<count($view['file']); $i++) {
            if (isset($view['file'][$i]['source']) && $view['file'][$i]['source'] && !$view['file'][$i]['view']) {
         ?>
            <li>
                <i class="fa fa-download" aria-hidden="true"></i>
                <a href="<?php echo $view['file'][$i]['href'];  ?>" class="view_file_download">
                    <strong><?php echo $view['file'][$i]['source'] ?></strong>
                </a>
                <?php echo $view['file'][$i]['content'] ?> (<?php echo $view['file'][$i]['size'] ?>)
                <span class="bo_v_file_cnt"><?php echo $view['file'][$i]['download'] ?>회 다운로드 | DATE : <?php echo $view['file'][$i]['datetime'] ?></span>
            </li>
        <?php
            }
        }
         ?>
        </ul>
    </section>
    <!-- } 첨부파일 끝 -->
    <?php } ?>

    <?php if(array_filter($view['link'])) { ?>
    <!-- 관련링크 시작 { -->
    <section id="bo_v_link">
        <h2>관련링크</h2>
        <ul>
        <?php
        // 링크
        $cnt = 0;
        for ($i=1; $i<=count($view['link']); $i++) {
            if ($view['link'][$i]) {
                $cnt++;
                $link = cut_str($view['link'][$i], 70);
            ?>
            <li>
                <i class="fa fa-link" aria-hidden="true"></i> <a href="<?php echo $view['link_href'][$i] ?>" target="_blank">
                    
                    <strong><?php echo $link ?></strong>
                </a>
                <span class="bo_v_link_cnt"><?php echo $view['link_hit'][$i] ?>회 연결</span>
            </li>
            <?php
            }
        }
        ?>
        </ul>
    </section>
    <!-- } 관련링크 끝 -->
    <?php } ?>

    <!-- 게시물 상단 버튼 시작 { -->
    <div id="bo_v_top">
        <?php
        ob_start();
        ?>
        <div class="holder" >
        <!-- onclick='checkOnlyOne(this)' -->
            <input type="checkbox" name='kyc_result' id="checkbox-2-1" class="kyc_result_btn kyc_confirm" <?=confirm_check(1)?> data-val="1" data-id="<?=$view['wr_id']?>"/>
            <label for="checkbox-2-1">KYC 인증 승인</label> 
            
            <input type="checkbox" name='kyc_result' id="checkbox-2-2" class="kyc_result_btn kyc_reject" <?=confirm_check(2)?> data-val="2" data-id="<?=$view['wr_id']?>"/>
            <label for="checkbox-2-2" class='kyc_reject_label'>KYC 인증 재등록요망</label>
            <span class='regdt'><?=$view['wr_4']?></span>
        </div>
       
        <div style="border-top:1px dashed #ccc;margin-top:30px;">
            <ul class="bo_v_left">
                <?php if ($update_href) { ?><li><a href="<?php echo $update_href ?>" class="btn_b01 btn"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> 수정</a></li><?php } ?>

                <?php if ($delete_href) { ?><li><a href="<?php echo $delete_href ?>" class="btn_b01 btn" onclick="del(this.href); return false;"><i class="fa fa-trash-o" aria-hidden="true"></i> 삭제</a></li><?php } ?>
                <!--<?php if ($copy_href) { ?><li><a href="<?php echo $copy_href ?>" class="btn_admin btn" onclick="board_move(this.href); return false;"><i class="fa fa-files-o" aria-hidden="true"></i> 복사</a></li><?php } ?>
                <?php if ($move_href) { ?><li><a href="<?php echo $move_href ?>" class="btn_admin btn" onclick="board_move(this.href); return false;"><i class="fa fa-arrows" aria-hidden="true"></i> 이동</a></li><?php } ?>-->
                <?php if ($search_href) { ?><li><a href="<?php echo $search_href ?>" class="btn_b01 btn"><i class="fa fa-search" aria-hidden="true"></i> 검색</a></li><?php } ?>
            </ul>
        </div>

        <ul class="bo_v_com">
           <li><a href="<?php echo $list_href ?>" class="btn_b01 btn"><i class="fa fa-list" aria-hidden="true"></i> 목록</a></li>
            <!--<?php if ($reply_href) { ?><li><a href="<?php echo $reply_href ?>" class="btn_b01 btn"><i class="fa fa-reply" aria-hidden="true"></i> 답변</a></li><?php } ?>-->
            <?php if ($write_href) { ?><li><a href="<?php echo $write_href ?>" class="btn_b02 btn"><i class="fa fa-pencil" aria-hidden="true"></i> 글쓰기</a></li><?php } ?>
        </ul>

        <?php if ($prev_href || $next_href) { ?>
        <ul class="bo_v_nb">
            <?php if ($prev_href) { ?><li class="btn_prv"><span class="nb_tit"><i class="fa fa-caret-up" aria-hidden="true"></i> 이전글</span><a href="<?php echo $prev_href ?>"><?php echo $prev_wr_subject;?></a> <span class="nb_date"><?php echo str_replace('-', '.', substr($prev_wr_date, '2', '8')); ?></span></li><?php } ?>
            <?php if ($next_href) { ?><li class="btn_next"><span class="nb_tit"><i class="fa fa-caret-down" aria-hidden="true"></i> 다음글</span><a href="<?php echo $next_href ?>"><?php echo $next_wr_subject;?></a>  <span class="nb_date"><?php echo str_replace('-', '.', substr($next_wr_date, '2', '8')); ?></span></li><?php } ?>
        </ul>
        <?php } ?>
        <?php
        $link_buttons = ob_get_contents();
        ob_end_flush();
         ?>
    </div>
    <!-- } 게시물 상단 버튼 끝 -->

        
    <?php
    // 코멘트 입출력
    //include_once(G5_BBS_PATH.'/view_comment.php');
     ?>
     


</article>
<!-- } 게시판 읽기 끝 -->

<script>
<?php if ($board['bo_download_point'] < 0) { ?>
$(function() {
    $("a.view_file_download").click(function() {
        if(!g5_is_member) {
            alert("다운로드 권한이 없습니다.\n회원이시라면 로그인 후 이용해 보십시오.");
            return false;
        }

        var msg = "파일을 다운로드 하시면 포인트가 차감(<?php echo number_format($board['bo_download_point']) ?>점)됩니다.\n\n포인트는 게시물당 한번만 차감되며 다음에 다시 다운로드 하셔도 중복하여 차감하지 않습니다.\n\n그래도 다운로드 하시겠습니까?";

        if(confirm(msg)) {
            var href = $(this).attr("href")+"&js=on";
            $(this).attr("href", href);

            return true;
        } else {
            return false;
        }
    });
});
<?php } ?>

function board_move(href)
{
    window.open(href, "boardmove", "left=50, top=50, width=500, height=550, scrollbars=1");
}
</script>

<script>
$(function() {
    $("a.view_image").click(function() {
        window.open(this.href, "large_image", "location=yes,links=no,toolbar=no,top=10,left=10,width=10,height=10,resizable=yes,scrollbars=no,status=no");
        return false;
    });

    $(".kyc_result_btn").on("click", function(){
        
        if($(this).prop('checked')){
            $('.kyc_result_btn').prop('checked',false);
            $(this).prop('checked',true);
        }

        var dataval = $(this).data("val");
        var dataid = $(this).data("id");
        console.log(`val : ${dataval}\nid : ${dataid}`);

        $.ajax({
			type: "POST",
			url: "/util/kyc_result.php",
			cache: false,
			async: false,
			dataType: "json",
			data: {
                "id" : dataid,
				"value" : dataval
			},
			success: function(res) {
				if (res.result == "OK") {
                    location.reload();
				} else {
                    alert("처리되지 않았습니다.\n문제가 지속되면 관리자에게 연락주세요");
                }

			},
			error: function(e) {
				console.log(e)
			}
		});

    });

    // 추천, 비추천
    $("#good_button, #nogood_button").click(function() {
        var $tx;
        if(this.id == "good_button")
            $tx = $("#bo_v_act_good");
        else
            $tx = $("#bo_v_act_nogood");

        excute_good(this.href, $(this), $tx);
        return false;
    });

    // 이미지 리사이즈
    $("#bo_v_atc").viewimageresize();

    //sns공유
    $(".btn_share").click(function(){
        $("#bo_v_sns").fadeIn();
   
    });

    $(document).mouseup(function (e) {
        var container = $("#bo_v_sns");
        if (!container.is(e.target) && container.has(e.target).length === 0){
        container.css("display","none");
        }	
    });
});

function excute_good(href, $el, $tx)
{
    $.post(
        href,
        { js: "on" },
        function(data) {
            if(data.error) {
                alert(data.error);
                return false;
            }

            if(data.count) {
                $el.find("strong").text(number_format(String(data.count)));
                if($tx.attr("id").search("nogood") > -1) {
                    $tx.text("이 글을 비추천하셨습니다.");
                    $tx.fadeIn(200).delay(2500).fadeOut(200);
                } else {
                    $tx.text("이 글을 추천하셨습니다.");
                    $tx.fadeIn(200).delay(2500).fadeOut(200);
                }
            }
        }, "json"
    );
}
</script>
<!-- } 게시글 읽기 끝 -->