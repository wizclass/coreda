<?php
$menubar = 1;
include_once('./_common.php');
$title = '비밀번호 재설정';

include_once(G5_THEME_PATH . '/_include/head.php');
include_once(G5_THEME_PATH . '/_include/gnb.php');
$lang_visible = 1;
include_once(G5_THEME_PATH . '/_include/lang.php');


$rand_num = sprintf("%06d", rand(000000, 999999));
// echo $rand_num;
?>

<html>

<style>
    .notice-red {
        color: red;
    }

    .top_title h3 {
        line-height: 20px;
        display: inline-block;
        width: auto;
        margin: 0 auto;
        padding-right: 13px;
        font-size: 15px !important;
    }

    .top_title {
        color: #000;
        text-align: center;
        box-sizing: border-box;
        padding: 15px 20px;
        /* box-shadow:0 1px 0px rgba(0,0,0,0.25) */
    }
</style>


<body class="bf-login">

    <div class="container mt-5 pt20">
        <div class="hp_form" id="hp_form">
            <input type="text" id="mb_id" class="b_radius_10 mb-2" placeholder="아이디를 입력해주세요" data-i18n="[placeholder]find_pw.아이디를 입력해주세요">
            <input type="text" id="hp_num" class="b_radius_10 mb-2" placeholder="이메일을 입력해주세요" data-i18n="[placeholder]find_pw.이메일을 입력해주세요">
            <input type="button" class="btn btn_wd btn--gray b_radius_10" id="hp_button" value="인증번호 받기" data-i18n="[value]find_pw.인증번호 받기">
        </div>

        <div class="notice-red" id="notice_phone" style="display:none;"></div>

        <div id="timer_auth" class="position-relative mt-4 mb-5" style="">
            <!-- <div class='timer-down' id='timer_down'></div> -->
            <input type="text" id='hp_auth' class="b_radius_10 border-bottom-0" placeholder="인증번호 입력" maxlength="6">
            <input type="button" class="btn btn_wd btn--gray b_radius_10" id="hp_auth_check" value="인증번호 확인">
        </div>

        <div id="pw_form" style="">
            <input type="password" id='auth_pw' class="b_radius_10 border" placeholder='비밀번호 재설정' data-i18n="[placeholder]find_pw.비밀번호 재설정">
            <input type="password" id='re_auth_pw' class="b_radius_10 border" placeholder='비밀번호 재설정 확인' data-i18n="[placeholder]find_pw.비밀번호를 재설정 확인">
            <input type="button" class="btn btn_wd btn-agree btn--blue b_radius_10" id='confirm_pw' value="확인" data-i18n="[value]find_pw.확인">
        </div>

        <div class="notice-red" id="notice_password" style="display:none;"></div>

        <div class="gnb_dim"></div>
    </div>

</body>

</html>
<script>
    $('#mode_select').on('change', function() {
        mode_change(this.value);
    })
    $('#mode_select').val(Theme).change();


    $(document).ready(function() {
        if ($('#wrapper').parent().hasClass('bf-login') == true) {
            $('#wrapper').css('margin-left', '0px').css('color', '#000');

        }
    });
</script>
<script>
    function notice_lang(lang, num) {
        if (lang == "eng") {
            if (num == 1) {
                return "Please fill in the blanks"
            }
            if (num == 2) {
                return "No such member was found"
            }
            if (num == 3) {
                return "An email has been sent"
            }
            if (num == 4) {
                return "Please enter a password"
            }
            if (num == 5) {
                return "Please enter at least 4 letters and less than 8 digits using a combination of English, numbers or special characters"
            }
            if (num == 6) {
                return "Please double check your password"
            }
            if (num == 7) {
                return "There are spaces in the password"
            }
        }

        if (lang == "kor") {
            if (num == 1) {
                return "빈칸을 채워주세요"
            }
            if (num == 2) {
                return "해당회원을 찾지 못했습니다"
            }
            if (num == 3) {
                return "이메일로 인증번호를 발송하였습니다"
            }
            if (num == 4) {
                return "비밀번호를 입력해주세요"
            }
            if (num == 5) {
                return "영문, 숫자 또는 특수 문자 조합을 사용하여 최소 4 자 이상 8 자리 이하 입력주세요"
            }
            if (num == 6) {
                return "비밀번호를 재확인 해주세요"
            }
            if (num == 7) {
                return "비밀번호에 공백이 포함 되어있습니다"
            }
        }

        if (lang == "chn") {
            if (num == 1) {
                return "请填写空白"
            }
            if (num == 2) {
                return "找不到这样的成员"
            }
            if (num == 3) {
                return "我通过电子邮件发送了验证码"
            }
            if (num == 4) {
                return "请输入密码"
            }
            if (num == 5) {
                return "请使用英文，数字或特殊字符的组合输入至少4个字母且少于8位数字"
            }
            if (num == 6) {
                return "请仔细检查您的密码"
            }
            if (num == 7) {
                return "密码包含空格 "
            }
        }
    }



    var select_lang = "<?= $myLang ?>";

    $('#lang').on('change', function(e) {
        select_lang = $(this).val()
    });

    $(".top_title h3").html("<span>비밀번호 재설정</span>");

    $('#timer_auth').hide();
    $('#pw_form').hide();


    $('#hp_button').click(function() {

        if ($('#hp_num').val() == "" || $('#mb_id').val() == "") {
            $('#notice_phone').show();
            $('#notice_phone').text(notice_lang(select_lang, 1));
            return;
        }

        $.ajax({

            url: "/mail/find_pw_mail.php",
            type: "POST",
            dataType: "json",
            async: false,
            data: {
                mb_id: $('#mb_id').val(),
                mb_email: $('#hp_num').val(),
            },
            success: function(res) {




                // $('#notice_phone').hide();
                // $("#mb_id").attr("readonly", true);
                // $("#hp_num").attr("readonly", true);
                // $('#hp_button').attr('disabled', true);
                // $('#timer_auth').show();
                // $('#timer_down').css('color','red');

                // var count_down = setInterval(function() {
                //     min = parseInt(time / 60);
                //     sec = time % 60;
                //     if(sec < 10){
                //         sec_temp = "0";
                //     }else{
                //         sec_temp = "";
                //     }
                //     document.getElementById('timer_down').innerHTML = "남은시간 : 0" +min + ":"+sec_temp + sec;
                //     time--;

                //     if (time < 0) {
                //         clearInterval(count_down);
                //         dialogModal('휴대폰 인증', '시간이 초과되었습니다.', 'failed');
                //         $('#modal_return_back').click(function(){
                //             window.location.reload();
                //         })
                //     }

                // }, 1000);


            },
            complete: function(res) {
                var check = res.hasOwnProperty("responseJSON")

                if (check) {
                    $('#notice_phone').show();
                    $('#notice_phone').text(notice_lang(select_lang, 2));
                    return;
                } else {
                    $('#notice_phone').show();
                    $('#notice_phone').text(notice_lang(select_lang, 3))
                    $("#mb_id").attr("readonly", true);
                    $("#hp_num").attr("readonly", true);
                    $('#hp_button').attr('disabled', true);
                    $('#timer_auth').show();
                }
            }
        })
    })

    $('#hp_auth_check').click(function() {

        if ($('#hp_auth_check').val() == '') {
            alert("인증번호를 입력해주세요.");
        }

        $.ajax({

            url: "/util/find_pw_proc.php",
            type: "POST",
            dataType: "json",
            async: false,
            data: {
                type: "auth_number_check",
                mb_id: $('#mb_id').val(),
                mb_email: $('#hp_num').val(),
                auth_number: $('#hp_auth').val()
            },
            success: function(res) {
                if (res.code == 200) {
                    $('#pw_form').show();
                    $("#mb_id").attr("readonly", true);
                    $("#hp_num").attr("readonly", true);
                    $('#hp_auth').attr('disabled', true);
                    $('#hp_auth_check').attr('disabled', true);
                } else alert("인증번호가 일치하지 않습니다.")
            },
        })
    })

    $('#confirm_pw').click(function() {
        console.log("DD");
        var auth_pw = $('#auth_pw').val();
        var re_auth_pw = $('#re_auth_pw').val();
        var blank = /[\s]/g;
        var notice_password = $(document.getElementById("notice_password"));
        var pattern = /^(?!((?:[0-9]+)|(?:[a-zA-Z]+)|(?:[\[\]\^\$\.\|\?\*\+\(\)\\~`\!@#%&\-_+={}'""<>:;,\n]+))$)(.){4,}$/;

        if (auth_pw == "" || re_auth_pw == "") {
            $('#notice_password').show();
            notice_password.text(notice_lang(select_lang, 4));
            notice_password.html(notice_password.html().replace(/\n/g, '<br/>'));
            return;
        }

        if ((auth_pw.length < 4 || auth_pw.length > 12 || !pattern.test(auth_pw))) {
            $('#notice_password').show();
            notice_password.text(notice_lang(select_lang, 5));
            notice_password.html(notice_password.html().replace(/\n/g, '<br/>'));
            return;
        }

        if (auth_pw != re_auth_pw) {
            $('#notice_password').show();
            notice_password.text(notice_lang(select_lang, 6));
            notice_password.html(notice_password.html().replace(/\n/g, '<br/>'));
            return;
        }

        if (blank.test(auth_pw) == true) {
            $('#notice_password').show();
            notice_password.text(notice_lang(select_lang, 7));
            notice_password.html(notice_password.html().replace(/\n/g, '<br/>'));
            return;
        }

        $.ajax({
            url: "/util/find_pw_proc.php",
            type: "POST",
            dataType: "json",
            async: false,
            data: {
                type: "change_password",
                auth_pw: auth_pw,
                mb_email: $('#hp_num').val(),
                mb_id: $('#mb_id').val(),
                auth_number: $('#hp_auth').val()
            },
            success: function(res) {
                if (res.code == "200") {
                    dialogModal('RESET PASSWORD', 'Your password has been changed', 'success');
                    $('#modal_return_url').click(function() {
                        location.replace('/bbs/login_pw.php');
                    })
                }
            }
        })


    })
</script>