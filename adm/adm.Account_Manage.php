<?php
$sub_menu = "700700";
include_once('./_common.php');
include_once(G5_THEME_PATH.'/_include/wallet.php');

auth_check($auth[$sub_menu], 'r');

$g5['title'] = "입금 계좌관리";
$searchbar = 'none';

include_once('./adm.header.php');
$account_list = array_bank_account();

?>

<!-- <link href="<?=G5_ADMIN_URL?>/css/scss/bonus/bonus_config2.css" rel="stylesheet"> -->
<style>
    .reg_text.border_blue{border:1px solid blue !important;}
    .reg_text.border_red{border:1px solid red !important;}
</style>
<form name="allowance" id="allowance" method="post" action="./adm.Account_proc.php" onsubmit="return frmconfig_check(this);" >

<div class="local_desc01 local_desc">
    <p>
        - 사용여부 체크된것만 사용자에게 노출.<br>
        - 코인입금주소는 최초 입력이후 변경 불가 => <strong>관리자에게 문의</strong><br>
        - 계좌수정시 기존 입/출금 요청건에는 영향 없음.<br>
	</p>
</div>

<div class="tbl_head01 tbl_wrap">
    <table class="regTb" id="table">
    <caption><?php echo $g5['title']; ?> 목록</caption>
    <thead>
    <tr>
        <th scope="col" width="30px">No</th>
        <th scope="col" width="40px">사용여부</th>
        <th scope="col" width="80px">구분</th>
        <th scope="col" width="40px">노출순서</th>	
        <th scope="col" width="120px">계좌명</th>
        <th scope="col" width="80px">은행명</th>
        <th scope="col" width="70px">계좌번호</th>
		<th scope="col" width="120px">예금주</th>
        <th scope="col" width="80px">등록/변경일</th>
    </tr>
    </thead>

    <tbody>
    <?for($i=0; $i < count($account_list); $i++){
        $row = $account_list[$i];
    ?>
        <tr class=''>
            <input type="hidden" name="type" value="update">
            <td style="text-align: center;"><input type="hidden" name="no[]" value="<?=$row['no']?>"><?=$row['no']?></td>
            <td style="text-align: center;"><input type='checkbox' class='checkbox' name='check' <?php echo $row['used'] > 0?'checked':''; ?>>
                <input type="hidden" name="used[]" class='used' value="<?=$row['used']?>">
            </td>
            <td style=""><input class='reg_text <?if($row['category_no'] == 1 || $row['category_no'] == 3){echo 'border_blue';}else{echo "border_red";}?>' name="category[]"  value="<?=$row['category']?>"></input></td>
            <td style=""><input class='reg_text' name="sequence[]"  value="<?=$row['sequence']?>"></input></td>
            <td style=""><input class='reg_text' name="account_name[]"  value="<?=$row['account_name']?>"></input></td>
            <td style=""><input class='reg_text' name="bank_name[]"  value="<?=$row['bank_name']?>"></input></td>
            <td style=""><input class='reg_text' name="bank_account[]"  value="<?=$row['bank_account']?>"></input></td>
            <td style=""><input class='reg_text' name="bank_account_name[]"  value="<?=$row['bank_account_name']?>"></input></td>
            <td style=""><?=$row['create_dt']?></td>
        </tr>
    <?}?>

    </tbody>
    
    <tfoot>
        <td colspan=12 height="100px" style="padding:20px 0px" class="btn_ly">
            <input style="background:#ff4081;" type="button" class="btn btn_confirm btn_submit" value="추가하기" id="con_add"></input>
            <input  style="background:cornflowerblue;" type="submit" class="btn btn_confirm btn_submit" value="저장하기" id="com_send"></input>
        </td>
    </tfoot>
</table>

</div>
</form>

<script>

    function frmconfig_check(f){
       
    }

    $(document).ready(function(){

        $(".checkbox" ).on( "click",function(){
            if($(this).is(":checked") == true){
                console.log( $(this).next().val() );
                $(this).next().val(1);
            }else{
                $(this).next().val(0);
            }
        });
        
    });

    document.querySelector('#con_add').addEventListener('click',()=>{
       $.ajax({
        url: "./adm.Account_proc.php",
        type: "POST",
        dataType: "json",
        data: {
            type: "create"
        },  
        async: false,
        cache: false,
        success : (res) => {
            if(res.code == '200'){
                window.location.reload();
            }else{
                alert("문제가 발생하였습니다. 나중에 다시 시도해주세요.");
            }
        }
       })
    });

</script>
</div>

<?php
include_once ('./admin.tail.php');
?>