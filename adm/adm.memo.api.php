<?
    include_once("./_common.php");

    $category = $_POST['category'];
    $uid = $_POST['uid'];
    $contents = $_POST['contents'];

    if($uid && $category){
        
        if($category == 'bonus'){
            $target = 'wallet_withdrawal_request';
        }else if ($category == 'mining'){
            $target = 'wallet_withdrawal_request';
        }else if ($category == 'deposit'){
            $target = 'wallet_deposit_request';
        }

        $update_item = "UPDATE {$target} set memo = '{$contents}' WHERE uid = {$uid} ";
        // print_R($update_item);
        $result = sql_query($update_item);

        if($result){
            echo (json_encode(array("result" => "success",  "code" => "999")));
        }else{
            echo (json_encode(array("result" => "failed",  "code" => "001")));
        }
    }
    

?>