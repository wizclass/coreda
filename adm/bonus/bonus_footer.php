</div>
<footer> 정산 완료</footer>

<div class='btn' onclick="bonus_url('<?= $category ?>');">돌아가기</div>

<body>

	</html>

	<style>
		body {
			font-size: 14px;
			line-height: 18px;
			letter-spacing: 0px;
		}

		code {
			color: green;
			display: block;
			margin-bottom: 5px;
			font-size: 11px;
		}

		.red {
			color: red;
			font-weight: 600;
		}

		.blue {
			color: blue;
			font-weight: 600;
		}

		.big {
			font-size: 16px;
			font-weight: 600;
		}

		.title {
			font-weight: 800;
			color: black;
			font-size: 16px;
			display: block;
		}

		.box {
			background: ghostwhite;
			margin-top: 30px;
			border-bottom: 1px solid #eee;
			padding-left: 5px;
			width: 100%;
			display: block;
		}

		.block {
			font-size: 26px;
			background: turquoise;
			display: block;
			height: 30px;
			line-height: 30px;
		}

		.block.coral {
			background: lightcoral
		}

		.indent {
			text-indent: 20px;
			display: inline-block;
		}

		.btn {
			background: black;
			padding: 5px 20px;
			display: inline-block;
			color: white;
			font-weight: 600;
			cursor: pointer;
			margin-bottom: 20px;
		}

		footer,
		header {
			margin: 20px 0;
			background: black;
			color: white;
			text-align: center
		}

		.error {
			display: block;
			width: 100%;
			text-align: center;
			height: 150px;
			line-height: 150px
		}

		.hidden {
			display: none;
		}

		.desc {
			font-size: 11px;
			color: #777;
		}

		.subtitle {
			font-size: 20px;
		}

		.sys_log {
			margin-bottom: 30px;
		}
	</style>


	<script>
		function bonus_url($val) {
			if ($val == 'staking') {
				location.href = '/adm/bonus/bonus_staking.php?to_date=<?= $bonus_day ?>';
			} else {
				location.href = '/adm/bonus/bonus_list.php?to_date=<?= $bonus_day ?>';
			}

		}
	</script>