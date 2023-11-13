<?php
include_once("../common.php");

$mb_id = $_POST['mb_id'];
$to_email = $_POST['mb_email'];

$sql = "SELECT mb_id,mb_email, count(mb_id) as cnt FROM {$g5['member_table']} WHERE mb_id = '{$mb_id}' AND mb_email = '{$to_email}'";
$row = sql_fetch($sql);

if ($row['cnt'] <= 0) {
    echo json_encode(array("code" => "00002"));
    return;
}

$to_id = $row['mb_id'];

$auth_number = sprintf("%06d", rand(000000, 999999));
$sql = "update {$g5['member_table']} set mb_lost_certify = '{$auth_number}' where mb_id = '{$mb_id}' and mb_email = '{$to_email}'";
sql_query($sql);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require "./PHPMailer.php";
require "./SMTP.php";
require "./Exception.php";

$mail = new PHPMailer(true);

try {

    // 서버세팅
    $mail->SMTPDebug = 2;    // 디버깅 설정
    $mail->isSMTP();        // SMTP 사용 설정

    $mail->Host = "smtp.gmail.com";                // email 보낼때 사용할 서버를 지정
    $mail->SMTPAuth = true;                        // SMTP 인증을 사용함
    $mail->Username = CONFIG_MAIL_ACCOUNT;    // 메일 계정
    $mail->Password = CONFIG_MAIL_PW;               // 메일 비밀번호
    $mail->SMTPSecure = "ssl";                    // SSL을 사용함
    $mail->Port = 465;                            // email 보낼때 사용할 포트를 지정
    $mail->CharSet = "utf-8";                        // 문자셋 인코딩

    // 보내는 메일
    // $mail -> setFrom("willsoftkr@gmail.com", "The Binary");
    $mail->setFrom(CONFIG_MAIL_ADDR, CONFIG_TITLE);

    // 받는 메일
    $mail->addAddress($to_email, $to_id);


    // 메일 내용
    $mail->isHTML(true);                                               // HTML 태그 사용 여부
    $mail->Subject = "[" . CONFIG_TITLE . "] MEMBER PASSWORD CERTIFICATION";             // 메일 제목
    // $mail -> Body = $auth_md5;    // 메일 내용

    $hostname = $_SERVER["HTTP_HOST"];

    // 본문 이미지 첨부 및 내용
    $image = G5_THEME_PATH . '/img/email_top_logo.png';
    $mail->AddEmbeddedImage($image, "keyImage");
    $mail->MsgHTML("<div>
    <img src='cid:keyImage'
    style='margin: 0 auto 20px;
    display:block;
    margin-top: 20px;'>
    <p 
    style='color:black;font-size: 32px;font-weight: bold;line-height:initial;width: 500px;margin: 0 auto;margin-top: 50px;'>
    비밀번호 메일인증 안내입니다.
    </p>
    <p
    style='color:black;line-height: 22px;width: 500px;margin: 0 auto;margin-top: 40px;'>
    안녕하세요.<br>
    [ " . CONFIG_TITLE . " ]을 이용해 주셔서 진심으로 감사드립니다.<br>
    <span style='font-weight: bold;'>인증번호는 {$auth_number} 입니다.</span><br>" . CONFIG_TITLE . " 페이지에서 비밀번호 재설정을 진행해주세요.<br>
    </p>
    <div 
    style='color: #969696;font-size: 11px;margin-top: 40px;text-align:center;'>
    본 메일은 발신 전용입니다. 회신하실 경우 답변되지 않습니다.
    </div>
    </div>");



    // Gmail로 메일을 발송하기 위해서는 CA인증이 필요하다.
    // CA 인증을 받지 못한 경우에는 아래 설정하여 인증체크를 해지하여야 한다.
    $mail->SMTPOptions = array(
        "ssl" => array(
            "verify_peer" => false, "verify_peer_name" => false, "allow_self_signed" => true
        )
    );
    // 메일 전송
    $mail->send();
} catch (Exception $e) {
}
