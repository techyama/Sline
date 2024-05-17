<!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8">
	<title>プロフィール更新画面</title>
	<link rel="stylesheet" href="./css/style.css">
</head>

<body>
<div class="logoimg">	
	<img src="./image/logo2.jpg">
	</div>
	<div>
		
		<?php

		$user_id = $_POST["user_id"];

		?>
		<?php
		$gobackURL = "profile_input.php";
		$name;
		$timeH;
		$timem;
		$wake_up_time;
		?>
		<?php
		$name = preg_replace("/( |　)/", "", $_POST["name"]);
		$notice = $_POST["notice"];
		$timeH = preg_replace("/( |　)/", "", $_POST["timeH"]);
		$timem = preg_replace("/( |　)/", "", $_POST["timem"]);
		$errors = [];
		if (empty($_POST["name"]) | $_POST == ("　" | " ")) {
			$errors[] = "名前を入力してください。";
		}
		if (empty($_POST["timeH"])) {
			$errors[] = "普段起きる時間を入力してください";
		}
		if (empty($_POST["timem"])) {
			$errors[] = "普段起きる時間を入力してください";
		}
		if (count($errors) > 0) {
			foreach ($errors as $values) {
				echo "<span class='error'>$values</span><br>";
			}
			echo "<hr>";
			echo "<p><a href=$gobackURL>戻る</a></p>";
			exit();
		}
		$wake_up_time = (int)($timeH * 60 + $timem);

		require("db_connect.php");
		require("Line_push.php");
		try {
			$sql = "UPDATE profile SET wake_up_time = :wake_up_time , notice = :notice ,name = :name WHERE user_id = :user_id";
			$stm = $pdo->prepare($sql);
			$stm->bindValue(':user_id', $user_id, PDO::PARAM_STR);
			$stm->bindValue(':name', $name, PDO::PARAM_STR);
			$stm->bindValue(':notice', $notice, PDO::PARAM_INT);
			$stm->bindValue(':wake_up_time', $wake_up_time, PDO::PARAM_INT);
			if ($stm->execute()) {
				echo "プロフィールの更新が完了しました。";
				//Line_push("マニュアル\nおやすみ -> 就寝時間登録\nおはよう -> 起床時間登録\n結果 -> 平均起床時間\n普段起きる時間と平均起床時間との差\nテスト->集中力テストリンク送信\nグラフ->グラフリンク送信",$user_id);
			}
		} catch (Exception $e) {
			echo  '<span class="error">エラーがありました。</span><br>';
			echo $e->getMessage();
		}

		?>

	</div>
</body>

</html>