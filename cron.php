<?php
require("./db_connect.php"); //DBアクセス
//require("./db_connect_pre.php");
require("./Line_push.php"); //Linepush用関数読み込み
require("./return_id.php");//user_id,profile_id取得用関数読み込み
if (date_default_timezone_get() != "asia/Tokyo") { //タイムゾーンを東京にセット
    date_default_timezone_set("asia/Tokyo");
}

$time_now = time(); //unixtimestampで現在時間取得
//$time_now = 1632366000;
$date = date("H", $time_now);

if(isset($_GET["profile_id"])){
    $user_id = ret_user_id($_GET["profile_id"]);
}

//POSTで送られたユーザIDを変数に入れる
if(isset($_POST["user_id"])){
    $user_id = $_POST["user_id"];
}

//プロフィールを取得
try {
    $sql1 = "SELECT * FROM profile";
    $stm1 = $pdo->prepare($sql1);
    if ($stm1->execute()) {
        $result_profile = $stm1->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (Exception $e) {
    echo  '<span class="error">エラーがありました。</span><br>';
    echo $e->getMessage();
}
$sql6 = "SELECT * FROM profile WHERE user_id = :user_id";
		$stm6 = $pdo->prepare($sql6);
		$stm6->bindValue(":user_id", $user_id, PDO::PARAM_STR);
		if ($stm6->execute()) {
			$result = $stm6->fetchAll(PDO::FETCH_ASSOC);
		}
//var_dump($result);
//var_dump($notice);


//寝た時間を登録したか確認
foreach ($result_profile as $value) {
    $notice = $value["notice"];
    if($notice == 0){
    $user_id = $value["user_id"];
    $profile_id = ret_profile_id($user_id);
    $sql2 = "SELECT * FROM sleep WHERE user_id = :user_id ORDER BY in_time DESC LIMIT 0,1";
    $stm2 = $pdo->prepare($sql2);
    $stm2->bindValue(":user_id", $user_id, PDO::PARAM_STR);
    if ($stm2->execute()) {
        $result_sleep = $stm2->fetchAll(PDO::FETCH_ASSOC);
    }
    $in_time = (int)(strtotime($result_sleep[0]["in_time"])); //一番新しいレコードの寝た時間を取得
    $out_time = (int)(strtotime($result_sleep[0]["out_time"])); //一番新しいレコードの起きた時間を取得
    if ($date == 23 || $date == 00) {//夜23:00~00:59の間に実行
        if ($time_now - 43200 > $in_time) { //12:00~00:00までに寝ていないか、寝ていなかったもしくは入力せずにねてしまったら送信
            Line_push("はよねろー\nhttps://fukuiohr2.sakura.ne.jp/2021/Midare/Life_disorder/sleeptime_input.php?profile_id=$profile_id", $user_id);
            Line_push("睡眠アンケート\nhttps://fukuiohr2.sakura.ne.jp/2021/Midare/Life_disorder/question_input.php?profile_id=$profile_id", $user_id);
        }
    } else if ($date == 11 || $date == 12) {//昼11:00~12:59までの間に実行
        if ($time_now - 43200 > $out_time) {
            Line_push("起床時間の登録忘れてるよ\nhttps://fukuiohr2.sakura.ne.jp/2021/Midare/Life_disorder/outtime_input.php?profile_id=$profile_id", $user_id);
            Line_push("睡眠アンケート\nhttps://fukuiohr2.sakura.ne.jp/2021/Midare/Life_disorder/question_input.php?profile_id=$profile_id", $user_id);
        }
        Line_push("集中力テスト\nhttps://fukuiohr2.sakura.ne.jp/2021/Midare/Life_disorder/concentration_input.php?profile_id=$profile_id", $user_id);
    }
}
}
