<?php
if($member['mb_id'] == 'admin'){
    $menu['menu300'] = array (
        array('300000', '게시판관리', ''.G5_ADMIN_URL.'/board_list.php', 'board'),
        array('300100', '게시판관리', ''.G5_ADMIN_URL.'/board_list.php', 'bbs_board'),
        array('300200', '게시판그룹관리', ''.G5_ADMIN_URL.'/boardgroup_list.php', 'bbs_group'),
        array('300300', '인기검색어관리', ''.G5_ADMIN_URL.'/popular_list.php', 'bbs_poplist', 1),
        array('300400', '인기검색어순위', ''.G5_ADMIN_URL.'/popular_rank.php', 'bbs_poprank', 1),
        array('300500', '1:1문의설정', ''.G5_ADMIN_URL.'/qa_config.php', 'qa'),
        array('300600', '내용관리', G5_ADMIN_URL.'/contentlist.php', 'scf_contents', 1),
        array('300700', 'FAQ관리', G5_ADMIN_URL.'/faqmasterlist.php', 'scf_faq', 1),
        array('300001', '1:1문의 관리', G5_ADMIN_URL.'/bbs/qalist.php', '',1),
        array('300820', '글,댓글 현황', G5_ADMIN_URL.'/write_count.php', 'scf_write_count'),
        array('300250', '게시판관리', G5_ADMIN_URL.'/bbs/board.php?bo_table=notice','',1),
        array('300250', '공지사항', G5_ADMIN_URL.'/bbs/board.php?bo_table=notice','',1),
        array('300350', '뉴스관리', G5_ADMIN_URL.'/bbs/board.php?bo_table=news','',1),
        array('300450', 'FAQ관리', G5_ADMIN_URL.'/faqmasterlist.php', 'scf_faq', 1),
        array('300550', '서포트문의관리', G5_ADMIN_URL.'/board_support.php','',1),
        array('300650', 'KYC 회원인증', G5_ADMIN_URL.'/bbs/board.php?bo_table=kyc','',1),
        array('300750', '이용약관/개인정보', G5_ADMIN_URL.'/bbs/board.php?bo_table=agreement','',1)
    );
}else{
    $menu['menu300'] = array (
        array('300250', '게시판관리', G5_ADMIN_URL.'/bbs/board.php?bo_table=notice','',1),
        array('300250', '공지사항', G5_ADMIN_URL.'/bbs/board.php?bo_table=notice','',1),
        array('300001', '1:1문의 관리', G5_ADMIN_URL.'/bbs/qalist.php', '',1),
        array('300350', '뉴스관리', G5_ADMIN_URL.'/bbs/board.php?bo_table=news','',1),
        array('300450', 'FAQ관리', G5_ADMIN_URL.'/faqmasterlist.php', 'scf_faq', 1),
        array('300550', '서포트문의관리', G5_ADMIN_URL.'/board_support.php','',1),
        array('300650', 'KYC 회원인증', G5_ADMIN_URL.'/bbs/board.php?bo_table=kyc','',1),
        array('300750', '이용약관/개인정보', G5_ADMIN_URL.'/bbs/board.php?bo_table=agreement','',1)
    );
}