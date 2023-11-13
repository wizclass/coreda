<?php
include_once('./_common.php');

function replace_unicode_escape_sequence($match)
{
    return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');
}
function unicode_chr($chr)
{
    $x = explode("+", $chr);
    $str = end($x);
    return preg_replace_callback('/\\\\\\\\u([0-9a-f]{4})/i', 'replace_unicode_escape_sequence', $str);
}

$code = "300";
$msg = "잘못된 접근입니다.";

$type = isset($_POST['type']) ? $_POST['type'] : false;
$mb_id = isset($_POST['mb_id']) ? $_POST['mb_id'] : false;
$mb_email = isset($_POST['mb_email']) ? $_POST['mb_email'] : false;
$auth_number = isset($_POST['auth_number']) ? $_POST['auth_number'] : false;

if (!$type || !$mb_id || !$mb_email || !$auth_number) {
    echo json_encode(array("code" => $code, "msg" => $msg));
    return false;
}

$where = "where mb_id = '{$mb_id}' and mb_email = '{$mb_email}' and mb_lost_certify = '{$auth_number}'";

if ($type == "auth_number_check") {

    $sql = "select count(mb_id) as cnt from {$g5['member_table']} {$where}";
    $cnt = sql_fetch($sql)['cnt'];

    $msg = "인증번호가 일치하지 않습니다.";

    if ($cnt > 0) {
        $code = "200";
        $msg = "인증이 완료되었습니다.";
    }
} else if ($type == "change_password") {

    $auth_pw = isset($_POST['auth_pw']) ? unicode_chr($_POST['auth_pw']) : false;

    if ($auth_pw) {

        $sql = "select *,count(mb_id) as cnt from {$g5['member_table']} {$where}";
        $row = sql_fetch($sql);

        $msg = "정보가 일치하지 않습니다.";

        if ($row['cnt'] > 0) {

            $password = get_encrypt_string($auth_pw);
            $sql = "update {$g5['member_table']} set mb_password = '{$password}' {$where}";
            $result = sql_query($sql);

            $code = "500";
            $msg = "죄송합니다. 비밀번호 변경중 문제가 발생하였습니다. 문제가 지속되면 관리자에게 문의해주세요.";

            if ($result) {
                $code = "200";
                $msg = "비밀번호 변경이 완료되었습니다.";
            }
        }
    }
}

echo json_encode(array("code" => $code, "msg" => $msg));
