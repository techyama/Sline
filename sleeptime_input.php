<?php session_start();?>
<!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8">
	<title>睡眠時間入力フォーム</title>
	<link rel="stylesheet" href="./css/style.css">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
	<div>
	<div class="logoimg">
		<img src="./image/logo2.jpg">
		<h2 class="title">睡眠時間入力フォーム</h2>
	</div>
		<?php
		require("return_id.php");
		if (date_default_timezone_get() != "asia/Tokyo") {
			date_default_timezone_set("asia/Tokyo");
		}
		if(!isset($_SESSION["user_id"])){
		$user_id = ret_user_id((int)$_GET["profile_id"]);
		$_SESSION["user_id"] = $user_id;
		}

		?>
		<form method="POST" action="sleeptime_output.php">
			<ul>

				<label>前日の睡眠時間</label><br>
				<label>何時に寝たか　<input type="number" value=<?php if((date('md') != "0101")) {echo date('Y');} else {echo date('Y', strtotime('last year'));} ?> name="in_timeY" placeholder="年" min="2020" max="2050">年
					<input type="number" value=<?php echo date('m', strtotime('-1 day')); ?> name="in_timeM" placeholder="月" min="1" max="12">月
					<input type="number" value=<?php echo date('d', strtotime('-1 day')); ?> name="in_timeD" placeholder="日" min="1" max="31">日
					<input type="number" name="in_timeH" placeholder="時" min="0" max="23">時
					<input type="number" name="in_timem" placeholder="分" min="0" max="59">分<br></label>
				<label>何時まで寝たか<input type="number" value=<?php echo date('Y'); ?> name="out_timeY" placeholder="年" min="2020" max="2050">年
					<input type="number" value=<?php echo date('m'); ?> name="out_timeM" placeholder="月" min="1" max="12">月
					<input type="number" value=<?php echo date('d'); ?> name="out_timeD" placeholder="日" min="1" max="31">日
					<input type="number" name="out_timeH" placeholder="時" min="0" max="23">時
					<input type="number" name="out_timem" placeholder="分" min="0" max="59">分<br>
					<a>0時や0分を入力するときは、00と入力してください。0は無効になります。</a><br></label>
				<?php echo "<input type=hidden name=user_id value=$user_id>"; ?>
				<input type="submit" value="送信">
			</ul>
		</form>

		<div>
</body>

</html>