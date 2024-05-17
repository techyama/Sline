<?php session_start();?>
<!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8">
	<title>睡眠時間入力フォーム</title>
	<link rel="stylesheet" href="./css/style.css">
</head>

<body>

	<div class="logoimg">
		<img src="./image/logo2.jpg">
	</div>
	<div>
		<?php
		require("db_connect.php");
		require("sleep_judge.php");
		$gobackURL = "sleeptime_input.php";
		if(isset($_SESSION["user_id"])){
			$id1 = $_SESSION["user_id"];
		}

		$errors = [];
		?>

		<?php
		//タイムゾーンを東京にセット
		if (date_default_timezone_get() != "asia/Tokyo") {
			date_default_timezone_set("asia/Tokyo");
		}
		$_POST["in_timeY"] = preg_replace("/( |　)/", "", $_POST["in_timeY"]);
		$_POST["in_timeM"] = preg_replace("/( |　)/", "", $_POST["in_timeM"]);
		$_POST["in_timeD"] = preg_replace("/( |　)/", "", $_POST["in_timeD"]);
		$_POST["in_timeH"] = preg_replace("/( |　)/", "", $_POST["in_timeH"]);
		$_POST["in_timem"] = preg_replace("/( |　)/", "", $_POST["in_timem"]);
		$_POST["out_timeY"] = preg_replace("/( |　)/", "", $_POST["out_timeY"]);
		$_POST["out_timeM"] = preg_replace("/( |　)/", "", $_POST["out_timeM"]);
		$_POST["out_timeD"] = preg_replace("/( |　)/", "", $_POST["out_timeD"]);
		$_POST["out_timeH"] = preg_replace("/( |　)/", "", $_POST["out_timeH"]);
		$_POST["out_timem"] = preg_replace("/( |　)/", "", $_POST["out_timem"]);
		//var_dump($_POST);
		//入力チェック
		if (empty($_POST["in_timeY"]) | $_POST == ("　" | " ")) {
			$errors[] = "就寝、年を入力してください。";
		}
		if (empty($_POST["in_timeM"]) | $_POST == ("　" | " ")) {
			$errors[] = "就寝、月を入力してください。";
		}
		if (empty($_POST["in_timeD"]) | $_POST == ("　" | " ")) {
			$errors[] = "就寝、日を入力してください。";
		}
		if (empty($_POST["in_timeH"]) | $_POST == ("　" | " ")) {
			if ($_POST["in_timem"] == "0") {
				$errors[] = "0を入力する場合00と入力してください。";
			} else {
				$errors[] = "就寝、時を入力してください。";
			}
		}
		if (empty($_POST["in_timem"]) | $_POST == ("　" | " ")) {
			if ($_POST["in_timem"] == "0") {
				$errors[] = "0を入力する場合00と入力してください。";
			} else {
				$errors[] = "就寝、分を入力してください。";
			}
		}
		if (empty($_POST["out_timeY"]) | $_POST == ("　" | " ")) {
			$errors[] = "起床、年を入力してください。";
		}
		if (empty($_POST["out_timeM"]) | $_POST == ("　" | " ")) {
			$errors[] = "起床、月を入力してください。";
		}
		if (empty($_POST["out_timeD"]) | $_POST == ("　" | " ")) {
			$errors[] = "起床、日を入力してください。";
		}
		if (empty($_POST["out_timeH"]) | $_POST == ("　" | " ")) {
			if ($_POST["in_timem"] == "0") {
				$errors[] = "0を入力する場合00と入力してください。";
			} else {
				$errors[] = "起床、時を入力してください。";
			}
		}
		if (empty($_POST["out_timem"]) | $_POST == ("　" | " ")) {
			if ($_POST["in_timem"] == "0") {
				$errors[] = "0を入力する場合00と入力してください。";
			} else {
				$errors[] = "起床、分を入力してください。";
			}
		}

		$_POST["in_timeM"] = sprintf("%02s", $_POST["in_timeM"]);
		$_POST["in_timeD"] = sprintf("%02s", $_POST["in_timeD"]);
		$_POST["in_timeH"] = sprintf("%02s", $_POST["in_timeH"]);
		$_POST["in_timem"] = sprintf("%02s", $_POST["in_timem"]);
		$_POST["out_timeM"] = sprintf("%02s", $_POST["out_timeM"]);
		$_POST["out_timeD"] = sprintf("%02s", $_POST["out_timeD"]);
		$_POST["out_timeH"] = sprintf("%02s", $_POST["out_timeH"]);
		$_POST["out_timem"] = sprintf("%02s", $_POST["out_timem"]);


		//入力値に空白があった場合、空白の除去
		$in_date = "";
		$out_date = "";
		$in_date .= preg_replace("/( |　)/", "", $_POST["in_timeY"]);
		$in_date .= preg_replace("/( |　)/", "", $_POST["in_timeM"]);
		$in_date .= preg_replace("/( |　)/", "", $_POST["in_timeD"]);
		$in_date .= preg_replace("/( |　)/", "", $_POST["in_timeH"]);
		$in_date .= preg_replace("/( |　)/", "", $_POST["in_timem"]);
		$out_date .= preg_replace("/( |　)/", "", $_POST["out_timeY"]);
		$out_date .= preg_replace("/( |　)/", "", $_POST["out_timeM"]);
		$out_date .= preg_replace("/( |　)/", "", $_POST["out_timeD"]);
		$out_date .= preg_replace("/( |　)/", "", $_POST["out_timeH"]);
		$out_date .= preg_replace("/( |　)/", "", $_POST["out_timem"]);
		//文字列を時間に変換
		//var_dump($in_date);
		$in_date = strtotime($in_date);
		$out_date = strtotime($out_date);

		if($in_date == $out_date){
			$errors[] = "一分も寝てないよ大丈夫？";
		}
		if (count($errors) > 0) {
			foreach ($errors as $values) {
				echo "<span class='error'>$values</span><br>";
			}
			echo "<hr>";
			echo "<p><a href=$gobackURL>戻る</a></p>";
			exit();
		}
		try {
			$sql6 = "SELECT * FROM profile WHERE user_id = :user_id";
			$stm6 = $pdo->prepare($sql6);
			$stm6->bindValue(":user_id", $id1, PDO::PARAM_STR);
			if ($stm6->execute()) {
				$result_profile = $stm6->fetchAll(PDO::FETCH_ASSOC);
				$wake_up_time = (int)$result_profile[0]["wake_up_time"];
			}
		} catch (Exception $e) {
			echo  '<span class="error">エラーがありました。</span><br>';
			echo $e->getMessage();
		}
		if ($in_date > $out_date) {
			echo "<span class=error>就寝時間より起床時間の方が早くなっています。<br>入力し直してください。</span>";
			echo "<p><a href=$gobackURL>戻る</a></p>";
			exit();
		}
		if ($out_date - $in_date >= 86400) {
			echo "<span class=error>睡眠時間が1日を超えています。入力し直してください。</span>";
			echo "<p><a href=$gobackURL>戻る</a></p>";
			exit();
		}
		$in_datetime = date("Y-m-d H:i:s", $in_date);
		$out_datetime = date("Y-m-d H:i:s", $out_date);
		try {
			$sql2 = "SELECT count(*) as cnt2  FROM sleep where in_time between :in_time2 and :out_time2 and user_id = :user_id";
			$stm2 = $pdo->prepare($sql2);
			$sql3 = "SELECT count(*) as cnt3 FROM sleep where out_time between :in_time3 and :out_time3 and user_id = :user_id";
			$stm3 = $pdo->prepare($sql3);
			$sql4 = "SELECT count(*) as cnt4  FROM sleep where :in_time4 > ANY(SELECT in_time FROM sleep where :in_time5 < out_time and user_id = :user_id)";
			$stm4 = $pdo->prepare($sql4);
			$sql5 = "SELECT count(*) as cnt5  FROM sleep where :out_time4 > ANY(SELECT in_time FROM sleep where :out_time5 < out_time and user_id = :user_id)";
			$stm5 = $pdo->prepare($sql5);
			$stm2->bindValue(':user_id', $id1, PDO::PARAM_STR);
			$stm2->bindValue(':in_time2', $in_datetime, PDO::PARAM_STR);
			$stm2->bindValue(':out_time2', $out_datetime, PDO::PARAM_STR);
			$stm3->bindValue(':user_id', $id1, PDO::PARAM_STR);
			$stm3->bindValue(':in_time3', $in_datetime, PDO::PARAM_STR);
			$stm3->bindValue(':out_time3', $out_datetime, PDO::PARAM_STR);
			$stm4->bindValue(':user_id', $id1, PDO::PARAM_STR);
			$stm4->bindValue(':in_time4', $in_datetime, PDO::PARAM_STR);
			$stm4->bindValue(':in_time5', $in_datetime, PDO::PARAM_STR);
			$stm5->bindValue(':user_id', $id1, PDO::PARAM_STR);
			$stm5->bindValue(':out_time4', $out_datetime, PDO::PARAM_STR);
			$stm5->bindValue(':out_time5', $out_datetime, PDO::PARAM_STR);
			$stm2->execute();
			$stm3->execute();
			$stm4->execute();
			$stm5->execute();
			$result2 = $stm2->fetchAll(PDO::FETCH_ASSOC);
			$result3 = $stm3->fetchAll(PDO::FETCH_ASSOC);
			$result4 = $stm4->fetchAll(PDO::FETCH_ASSOC);
			$result5 = $stm5->fetchAll(PDO::FETCH_ASSOC);
		} catch (Exception $e) {
			echo  '<span class="error">エラーがありました。</span><br>';
			echo $e->getMessage();
			exit();
		}
		if ($result2[0]["cnt2"] > 0 | $result3[0]["cnt3"] > 0 | $result4[0]["cnt4"] > 0 | $result5[0]["cnt5"] > 0) {
			echo "入力されたデータ範囲にすでに入力された範囲が含まれています。やり直してください。";
		} else {

			try {
				$sql = "INSERT INTO sleep (user_id, in_time, out_time ,wake_up_time) VALUES (:user_id,:in_time, :out_time, :wake_up_time)";
				$stm = $pdo->prepare($sql);
				$stm->bindValue(':user_id', $id1, PDO::PARAM_INT);
				$stm->bindValue(':in_time', $in_datetime, PDO::PARAM_STR);
				$stm->bindValue(':out_time', $out_datetime, PDO::PARAM_STR);
				$stm->bindValue(':wake_up_time', $wake_up_time, PDO::PARAM_STR);

				if ($stm->execute()) {
					echo "睡眠時間の入力が完了しました。<br>";
					session_destroy();
				}
			} catch (Exception $e) {
				echo  '<span class="error">エラーがありました。</span><br>';
				echo $e->getMessage();
			}
		}
		$out_time_hour = date("H", $out_date);
		$out_time_minutes = date("i", $out_date);
		$out_time_result = $out_time_hour * 60 + $out_time_minutes;
		$wake_up_time_result = (int)$wake_up_time - (int)$out_time_result;

		$wake_up_time_result = abs($wake_up_time_result);
		$result_hour = (int)($wake_up_time_result / 60);
		$result_minutes = $wake_up_time_result % 60;
		echo "普段起きる時間と$result_hour 時間$result_minutes 分ズレがあるよ。<br>";

		$judge_msg = sleep_judge($id1);
		for ($i = 0; $i < 2; $i++) {
			if ($judge_msg[$i] != "") {
				if ($i == 1) {
					$link = $judge_msg[$i];
					$msg = $judge_msg[$i + 1];
					echo "<h3><a href=$link>$msg</a></h3>";
				} else {
					echo  "<h3>".$judge_msg[$i] . "</h3><br>";
				}
			}
		}

		?>
	</div>

</body>

</html>