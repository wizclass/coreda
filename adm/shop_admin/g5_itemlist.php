<?php
$sub_menu = '600150';
include_once('./_common.php');
include_once('../adm.wallet.php');

auth_check($auth[$sub_menu], "r");

$g5['title'] = '패키지 상품관리';
include_once(G5_ADMIN_PATH . '/admin.head.php');

// 분류
$ca_list  = '<option value="">선택</option>' . PHP_EOL;
$sql = " select * from {$g5['g5_category_table']} ";
if ($is_admin != 'super')
    $sql .= " where ca_mb_id = '{$member['mb_id']}' ";
$sql .= " order by ca_order, ca_id ";

$result = sql_query($sql);
for ($i = 0; $row = sql_fetch_array($result); $i++) {
    $len = strlen($row['ca_id']) / 2 - 1;
    $nbsp = '';
    for ($i = 0; $i < $len; $i++) {
        $nbsp .= '&nbsp;&nbsp;&nbsp;';
    }
    $ca_list .= '<option value="' . $row['ca_id'] . '">' . $nbsp . $row['ca_name'] . '</option>' . PHP_EOL;
}

$where = " and ";
$sql_search = "";
if ($stx != "") {
    if ($sfl != "") {
        $sql_search .= " $where $sfl like '%$stx%' ";
        $where = " and ";
    }
    if ($save_stx != $stx)
        $page = 1;
}

if ($sca != "") {
    $sql_search .= " $where (a.ca_id like '$sca%' or a.ca_id2 like '$sca%' or a.ca_id3 like '$sca%') ";
}

if ($sfl == "")  $sfl = "it_name";

$sql_common = " from {$g5['g5_item_table']} a ,
                     {$g5['g5_category_table']} b
               where (a.ca_id = b.ca_id";
if ($is_admin != 'super')
    $sql_common .= " and b.ca_mb_id = '{$member['mb_id']}'";
$sql_common .= ") ";
$sql_common .= $sql_search;

// 테이블의 전체 레코드수만 얻음
$sql = " select count(*) as cnt " . $sql_common;
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) {
    $page = 1;
} // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

if (!$sst) {
    $sst  = "it_id";
    $sod = "desc";
}
$sql_order = "order by $sst $sod";


$sql  = " select *
           $sql_common
           $sql_order
           limit $from_record, $rows ";

$result = sql_query($sql);
//$qstr  = $qstr.'&amp;sca='.$sca.'&amp;page='.$page;
$qstr  = $qstr . '&amp;sca=' . $sca . '&amp;page=' . $page . '&amp;save_stx=' . $stx;

$listall = '<a href="' . $_SERVER['SCRIPT_NAME'] . '" class="ov_listall">전체목록</a>';

?>

<div class="local_ov01 local_ov">
    <?php echo $listall; ?>
    등록된 상품 <?php echo $total_count; ?>건
</div>

<script src="<?= G5_THEME_URL ?>/_common/js/common.js" crossorigin="anonymous"></script>

<!-- <form name="flist" class="local_sch01 local_sch">
<input type="hidden" name="page" value="<?php echo $page; ?>">
<input type="hidden" name="save_stx" value="<?php echo $stx; ?>">

<label for="sca" class="sound_only">분류선택</label>
<select name="sca" id="sca">
    <option value="">전체분류</option>
    <?php
    $sql1 = " select ca_id, ca_name from {$g5['g5_category_table']} order by ca_order, ca_id ";
    $result1 = sql_query($sql1);

    for ($i = 0; $row1 = sql_fetch_array($result1); $i++) {
        $len = strlen($row1['ca_id']) / 2 - 1;
        $nbsp = '';
        for ($i = 0; $i < $len; $i++) $nbsp .= '&nbsp;&nbsp;&nbsp;';
        echo '<option value="' . $row1['ca_id'] . '" ' . get_selected($sca, $row1['ca_id']) . '>' . $nbsp . $row1['ca_name'] . '</option>' . PHP_EOL;
    }
    ?>
</select>

<label for="sfl" class="sound_only">검색대상</label>
<select name="sfl" id="sfl">
    <option value="it_name" <?php echo get_selected($sfl, 'it_name'); ?>>상품명</option>
    <option value="it_id" <?php echo get_selected($sfl, 'it_id'); ?>>상품코드</option>
    <option value="it_maker" <?php echo get_selected($sfl, 'it_maker'); ?>>제조사</option>
    <option value="it_origin" <?php echo get_selected($sfl, 'it_origin'); ?>>원산지</option>
    <option value="it_sell_email" <?php echo get_selected($sfl, 'it_sell_email'); ?>>판매자 e-mail</option>
</select>

<label for="stx" class="sound_only">검색어</label>
<input type="text" name="stx" value="<?php echo $stx; ?>" id="stx" class="frm_input">
<input type="submit" value="검색" class="btn_submit">

</form> -->

<!-- <div class="btn_add01 btn_add">
    <a href="./itemform.php">상품등록</a>
    <a href="./itemexcel.php" onclick="return excelform(this.href);" target="_blank">상품일괄등록</a>
</div> -->

<form name="fitemlistupdate" method="post" action="./g5_itemlistupdate.php" onsubmit="return fitemlist_submit(this);" autocomplete="off">
    <input type="hidden" name="sca" value="<?php echo $sca; ?>">
    <input type="hidden" name="sst" value="<?php echo $sst; ?>">
    <input type="hidden" name="sod" value="<?php echo $sod; ?>">
    <input type="hidden" name="sfl" value="<?php echo $sfl; ?>">
    <input type="hidden" name="stx" value="<?php echo $stx; ?>">
    <input type="hidden" name="page" value="<?php echo $page; ?>">

    <style>
        thead th {
            width: 10%
        }

        thead th.num {
            width: 20px !important;
        }

        input[type="text"] {
            padding-left: 5px;
        }
    </style>

    <div class="tbl_head01 tbl_wrap">
        <table>
            <caption><?php echo $g5['title']; ?> 목록</caption>
            <thead>
                <tr>
                    <th scope="col" class="num">
                        <label for="chkall" class="sound_only">상품 전체</label>
                        <input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
                    </th>
                    <th scope="col">상품코드</th>
                    <!-- <th scope="col" >상품분류</th> -->
                    <!-- <th scope="col" id="th_img">이미지</th> -->
                    <th scope="col" id="th_pc_title"><?php echo subject_sort_link('it_name', 'sca=' . $sca); ?>지급상품명</a></th>
                    <th scope="col" id="th_pc_title"><?php echo subject_sort_link('it_option_subject', 'sca=' . $sca); ?>노출상품명</a></th>
                    <th scope="col" id="th_amt" style='width:100px;'><?php echo subject_sort_link('it_price', 'sca=' . $sca); ?>상품가격 (<?= $curencys[0] ?>)</a></th>
                    <th scope="col" id="th_pt" style='width:140px;'><?php echo subject_sort_link('it_cust_price', 'sca=' . $sca); ?>판매가격 (<?= $curencys[0] ?>)</a></th>
                    <th scope="col" id="th_pt" style='width:100px;'><?php echo subject_sort_link('it_point', 'sca=' . $sca); ?>실적(PV)</a></th>
                    <th scope="col" id="th_pt" style='width:100px;'><?php echo subject_sort_link('it_supply_point', 'sca=' . $sca); ?>수익률</a></th>
                    <th scope="col" style='width:30px;'><?php echo subject_sort_link('it_order', 'sca=' . $sca); ?>노출순서</a></th>
                    <th scope="col" style='width:20px;'><?php echo subject_sort_link('it_use', 'sca=' . $sca, 1); ?>판매</a></th>
                    <!-- <th scope="col" style='width:30px;'><?php echo subject_sort_link('it_hit', 'sca=' . $sca, 1); ?>조회</a></th> -->
                    <th scope="col" style='width:70px;'>관리</th>

                </tr>
            </thead>
            <tbody>
                <?php
                for ($i = 0; $row = sql_fetch_array($result); $i++) {
                    $href = '/item.php?it_id=' . $row['it_id'];
                    $bg = 'bg' . ($i % 2);

                    $it_point = $row['it_point'];
                    if ($row['it_point_type'])
                        $it_point .= '%';
                ?>

                    <tr class="<?php echo $bg; ?>">

                        <!--체크박스-->
                        <td class="td_chk">
                            <label for="chk_<?php echo $i; ?>" class="sound_only"><?php echo get_text($row['it_name']); ?></label>
                            <input type="checkbox" name="chk[]" value="<?php echo $i ?>" id="chk_<?php echo $i; ?>">
                        </td>

                        <!--상품아이디-->
                        <td class="td_num">
                            <input type="hidden" name="it_id[<?php echo $i; ?>]" value="<?php echo $row['it_id']; ?>">
                            <?php echo $row['it_id']; ?>
                        </td>

                        <!--상품분류-->
                        <!--  <td >
            <label for="ca_id_<?php echo $i; ?>" class="sound_only"><?php echo get_text($row['it_name']); ?> 기본분류</label>
            <select name="ca_id[<?php echo $i; ?>]" id="ca_id_<?php echo $i; ?>" style="min-width:150px;line-height:34px;height:34px;">
                <?php echo conv_selected_option($ca_list, $row['ca_id']); ?>
            </select>
        </td> -->

                        <!--이미지-->
                        <!-- <td  class="td_img"><?php echo get_it_image($row['it_id'], 50, 50); ?></td> -->


                        <!--상품명-->
                        <td headers="th_pc_title" class="td_input">
                            <label for="name_<?php echo $i; ?>" class="sound_only">지급상품명</label>
                            <input type="text" name="it_name[<?php echo $i; ?>]" value="<?php echo htmlspecialchars2(cut_str($row['it_name'], 250, "")); ?>" id="name_<?php echo $i; ?>" required class="frm_input required" size="30">
                        </td>

                        <!--노출상품명-->
                        <td headers="th_pc_title" class="td_input">
                            <label for="name_<?php echo $i; ?>" class="sound_only">노출상품명</label>
                            <input type="text" name="it_option_subject[<?php echo $i; ?>]" value="<?php echo htmlspecialchars2(cut_str($row['it_option_subject'], 250, "")); ?>" id="name_<?php echo $i; ?>" required class="frm_input required" size="30">
                        </td>


                        <!--상품가격-->
                        <td headers="th_amt" class="td_numbig td_input">
                            <label for="price_<?php echo $i; ?>" class="sound_only">상품가격</label>
                            <input type="text" name="it_price[<?php echo $i; ?>]" value="<?= shift_auto($row['it_price']) ?>" id="price_<?php echo $i; ?>" class="frm_input sit_amt" size="7" style="width:100px;padding-right:5px;" inputmode="numeric">
                        </td>

                        <!--판매가격-->
                        <td headers="th_pt" class="td_numbig td_input" id="it_cust_price">
                            <label for="it_cust_price" class="sound_only">판매가격</label>
                            <!-- <button type='button' class='vat_calc' data-num = '<?= $i ?>' >vat</button> -->
                            <input type="text" name="it_cust_price[<?php echo $i; ?>]" value="<?= shift_auto($row['it_cust_price']) ?>" id="cust_price_<?php echo $i; ?>" class="frm_input sit_amt" size="5" style="padding-right:5px;" inputmode="numeric">
                        </td>


                        <!--판매가격-->
                        <td headers="th_pt" class="td_numbig td_input" id="it_point">
                            <label for="it_point" class="sound_only">판매실적(PV)</label>
                            <input type="text" name="it_point[<?php echo $i; ?>]" value="<?= shift_auto($row['it_point']) ?>" id="point_<?php echo $i; ?>" class="frm_input sit_amt" size="5" style="padding-right:5px;background:#f4ffe8 !important" inputmode="numeric">
                        </td>


                        <!--판매가격-->
                        <td headers="th_amt" class="td_numbig td_input">
                            <label for="price_<?php echo $i; ?>" class="sound_only">MP</label>
                            <input type="text" name="it_supply_point[<?php echo $i; ?>]" value="<?= $row['it_supply_point']; ?>" id="supply_<?php echo $i; ?>" class="frm_input sit_amt" size="7" style="width:100px;padding-right:5px;background:#f8e8ff !important"> %
                        </td>



                        <!--순서-->
                        <td class="td_mngsmall">
                            <label for="order_<?php echo $i; ?>" class="sound_only">노출순서</label>
                            <input type="text" name="it_order[<?php echo $i; ?>]" value="<?php echo $row['it_order']; ?>" id="order_<?php echo $i; ?>" class="frm_input" size="3">
                        </td>

                        <!--판매여부-->
                        <td class="td_chk">
                            <label for="use_<?php echo $i; ?>" class="sound_only">판매여부</label>
                            <input type="checkbox" name="it_use[<?php echo $i; ?>]" <?php echo ($row['it_use'] ? 'checked' : ''); ?> value="1" id="use_<?php echo $i; ?>">
                        </td>


                        <!--판매량-->
                        <!-- <td class="td_num">
            <label for="order_<?php echo $i; ?>" class="sound_only">판매량</label>
            <?php echo $row['it_hit']; ?>
        </td> -->


                        <!--관리-->
                        <td class="td_mng">
                            <!-- <a href="./itemform.php?w=u&amp;it_id=<?php echo $row['it_id']; ?>&amp;ca_id=<?php echo $row['ca_id']; ?>&amp;<?php echo $qstr; ?>"><span class="sound_only"><?php echo htmlspecialchars2(cut_str($row['it_name'], 250, "")); ?> </span>수정</a>
            <a href="./itemcopy.php?it_id=<?php echo $row['it_id']; ?>&amp;ca_id=<?php echo $row['ca_id']; ?>" class="itemcopy" target="_blank"><span class="sound_only"><?php echo htmlspecialchars2(cut_str($row['it_name'], 250, "")); ?> </span>복사</a>
            <a href="<?php echo $href; ?>"><span class="sound_only"><?php echo htmlspecialchars2(cut_str($row['it_name'], 250, "")); ?> </span>보기</a> -->
                        </td>
                    </tr>

                <?php
                }
                if ($i == 0)
                    echo '<tr><td colspan="12" class="empty_table">자료가 한건도 없습니다.</td></tr>';
                ?>
            </tbody>
        </table>
    </div>

    <div class="btn_list01 btn_list">
        <input type="submit" name="act_button" value="선택수정" onclick="document.pressed=this.value">
        <!-- <?php if ($is_admin == 'super') { ?>
    <input type="submit" name="act_button" value="선택삭제" onclick="document.pressed=this.value">
    <?php } ?> -->
    </div>
    <!-- <div class="btn_confirm01 btn_confirm">
    <input type="submit" value="일괄수정" class="btn_submit" accesskey="s">
</div> -->
</form>


<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, "{$_SERVER['SCRIPT_NAME']}?$qstr&amp;page="); ?>

<script>
    function fitemlist_submit(f) {
        if (!is_checked("chk[]")) {
            alert(document.pressed + " 하실 항목을 하나 이상 선택하세요.");
            return false;
        }

        if (document.pressed == "선택삭제") {
            if (!confirm("선택한 자료를 정말 삭제하시겠습니까?")) {
                return false;
            }
        }

        return true;
    }

    $(function() {
        $(".itemcopy").click(function() {
            var href = $(this).attr("href");
            window.open(href, "copywin", "left=100, top=100, width=300, height=200, scrollbars=0");
            return false;
        });

        $(".vat_calc").on('click', function() {
            var select_num = $(this).data('num');
            var it_price = $("#price_" + select_num).val().replace(/,/g, '');
            var it_sell_price = 1.1 * it_price;

            var cust_price = $("#cust_price_" + select_num);
            console.log(cust_price.val());
            cust_price.val(Price(it_sell_price.toFixed()));
        });
    });

    // 숫자에 콤마 찍기
    function Price(x) {
        return String(x).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    function excelform(url) {
        var opt = "width=600,height=450,left=10,top=10";
        window.open(url, "win_excel", opt);
        return false;
    }
</script>

<?php
include_once(G5_ADMIN_PATH . '/admin.tail.php');
?>