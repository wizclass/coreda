<?
	include_once('./_common.php');
	include_once(G5_THEME_PATH.'/_include/gnb.php');
	include_once(G5_THEME_PATH.'/_include/wallet.php');

	//매출액
	$mysales = $member['mb_deposit_point'];

	//보너스/예치금 퍼센트
	$bonus_per = bonus_state($member['mb_id']);

	$title = 'Mywallet';
?>


    <section class='breadcrumb'>
        <ol>
            <li class="active title"><?=$title?></li>
            <li class='home'><i class="ri-home-4-line"></i><a href="<?php echo G5_URL; ?>">Home</a></li>
            <li><a href="/page.php?id=<?=$title?>"><?=$title?></a></li>
        </ol>
    </section>


    <main >
        <div class='container'>


        </div>
    </main>


	<div class="gnb_dim"></div>
</section>

<script>
$(function(){
		$(".top_title h3").html("<a href='/'><img src='<?=G5_THEME_URL?>/img/title.png' alt='logo'></a>");
});
</script>