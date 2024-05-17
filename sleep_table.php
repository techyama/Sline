<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>テーブル</title>
    <link rel="stylesheet" href="./css/table.css">
</head>

<body>
<div class="logoimg">	
	<img src="./image/logo2.jpg">
    <h2 class="title">睡眠記録表</h2>
	</div>
    <?php
    require("db_connect.php");
    require("return_id.php");
    $i = 0;
    $num = 0;
    if (isset($_POST["add"])) {
        $num = (int)$_POST["add"] + 1;
    }
    if (isset($_POST["remove"])) {
        $num = (int)$_POST["remove"] - 1;
    }
    if (isset($_GET["profile_id"])) {
        $user_id = ret_user_id((int)$_GET["profile_id"]);
        $profile_id = $_GET["profile_id"];
    }
    if (isset($_POST["user_id"])) {
        $user_id = $_POST["user_id"];
    }
    //$user_id = "U587ecb664ad32caea6ce9af3661d554b";
    try {
        $sql = "SELECT * FROM(SELECT * FROM sleep WHERE user_id=:user_id ORDER BY in_time desc LIMIT $num,8) as result";
        $sql_max = "SELECT * FROM(SELECT * FROM sleep WHERE user_id=:user_id ORDER BY in_time desc LIMIT 0,1) as maxresult";
        $sql_min = "SELECT * FROM(SELECT * FROM sleep WHERE user_id=:user_id ORDER BY in_time LIMIT 0,1) as minresult";
        $stm = $pdo->prepare($sql);
        $stm_max = $pdo->prepare($sql_max);
        $stm_min = $pdo->prepare($sql_min);
        $stm->bindValue(":user_id", $user_id, PDO::PARAM_STR);
        $stm_max->bindValue(":user_id", $user_id, PDO::PARAM_STR);
        $stm_min->bindValue(":user_id", $user_id, PDO::PARAM_STR);
        if ($stm_max->execute()) {
            $result_max = $stm_max->fetchAll(PDO::FETCH_ASSOC);
            $max_sleep_id = $result_max[0]["sleep_id"];
        }
        if ($stm_min->execute()) {
            $result_min = $stm_min->fetchAll(PDO::FETCH_ASSOC);
            $min_sleep_id = $result_min[0]["sleep_id"];
        }
        if ($stm->execute()) {
            $result = $stm->fetchAll(PDO::FETCH_ASSOC);
            if (count($result) > 0) {
                echo "<div class=buttan>";
                echo "<table border=1>";
                echo "<th>就寝時間</th><th>起床時間</th><th>普段起きる時間</th><th>評価</th><th></th>";
                foreach ($result as $value) { //7件分のレコード表示
                    //起きる時間をhh:mmに変換
                    $wake_up_time_hour = sprintf("%02d", (int)($value["wake_up_time"] / 60));
                    $wake_up_time_minutes = sprintf("%02d", (int)($value["wake_up_time"] % 60));
                    //小数点切り捨て
                    $in_time = explode(".",$value["in_time"]);
                    $out_time = explode(".",$value["out_time"]);
                    //削除する行を識別するためのid取得
                    $sleep_id = $value["sleep_id"];
                    if (!($value["sleep_id"] == $min_sleep_id || $i >= 7)) { //最初のレコードor評価に使う８件目のレコード以外表示

                        $i += 1;

                        //datetimeで入っているout_timeを評価しやすい形に変形
                        $sleep_out_time = strtotime($value["out_time"]);
                        $sleep_out_time_hour = date("H", $sleep_out_time);
                        $sleep_out_time_minutes = date("i", $sleep_out_time);
                        $sleep_out_time = (int)((int)$sleep_out_time_hour * 60 + (int)$sleep_out_time_minutes);
                        //起きた時間が日付をまたいでなかった場合
                        if ((int)$sleep_out_time_hour > 20) {
                            $sleep_out_time = $sleep_out_time - 1440;
                        }
                        //ひとつ前のレコードも同じように変形
                        $sleep_out_time_pre = strtotime($result[$i]["out_time"]);
                        $sleep_out_time_pre_hour = date("H", $sleep_out_time_pre);
                        $sleep_out_time_pre_minutes = date("i", $sleep_out_time_pre);
                        $sleep_out_time_pre = (int)((int)$sleep_out_time_pre_hour * 60 + (int)$sleep_out_time_pre_minutes);
                        //起きた時間が日付をまたいでなかった場合
                        if ((int)$sleep_out_time_pre_hour > 20) {
                            $sleep_out_time = $sleep_out_time - 1440;
                        }
                        //１件前のレコードと比べての評価
                        $msg_pre1 = abs($sleep_out_time - $value["wake_up_time"]);
                        $msg_pre2 = abs($sleep_out_time_pre - $result[$i]["wake_up_time"]);
                        $msg_abs = abs($msg_pre1 - $msg_pre2);
                        if($msg_abs < 10){
                            $msg = "維持";
                        }else {
                        if($msg_pre1 <= $msg_pre2){
                            $msg = "改善";
                        }else {
                            $msg = "悪化";
                        }
                    }
                        //$msg = abs($sleep_out_time - $value["wake_up_time"]) <= abs($sleep_out_time_pre - $result[$i]["wake_up_time"]) ? "改善" : "悪化";
                        //var_dump(abs($sleep_out_time - $value["wake_up_time"]));
                        //var_dump(abs($sleep_out_time_pre - $result[$i+1]["wake_up_time"]));
                        echo "<tr><td>$in_time[0]</td><td>$out_time[0]</td><td>$wake_up_time_hour:$wake_up_time_minutes</td><td>$msg</td>";
                        ?>
                        <!-- 削除する行をフォーム送信 -->
                        <form method="POST" action="delete_row.php">
                    <?php
                        echo "<td><input type=checkbox name=row$i value=$sleep_id></td>";
                    } else if ($value["sleep_id"] == $min_sleep_id && $i <= 6) {
                        echo "<tr><td>$in_time[0]</td><td>$out_time[0]</td><td>$wake_up_time_hour:$wake_up_time_minutes</td><td>最初</td>";
                        echo "<td><input type=checkbox name=row$i value=$sleep_id></td>";
                    }

                }
                echo "<tr><td colspan=2 class=delete>";
                echo "<input type=hidden value=$i name=row_num>";
                echo "<input type=hidden value=$profile_id name=profile_id>";
                echo "<input type=submit value=削除>";
                ?>
                </form>

                <?php
                echo "</td>";
                echo "<tr><td colspan=2 class=remove>";
                if ($result[0]["sleep_id"] != $max_sleep_id) {
                    echo "<form method=POST action=sleep_table.php class=remove>";
                    echo "<input type=hidden value=$user_id name=user_id>";
                    echo "<input type=hidden value=$num name=remove>";
                    echo "<input type=submit value=一日後>";
                    echo "</form>";
                }
                echo "</td><td colspan=2 class=add>";
                if (count($result) == 8) {
                    if ($result[count($result) - 2]["sleep_id"] != $min_sleep_id) {
                        echo "<form method=POST action=sleep_table.php class=add>";
                        echo "<input type=hidden value=$user_id name=user_id>";
                        echo "<input type=hidden value=$num name=add>";
                        echo "<input type=submit value=一日前>";
                        echo "</form>";
                    }
                } else {
                    if ($result[count($result) - 1]["sleep_id"] != $min_sleep_id) {
                        echo "<form method=POST action=sleep_table.php class=add>";
                        echo "<input type=hidden value=$user_id name=user_id>";
                        echo "<input type=hidden value=$num name=add>";
                        echo "<input type=submit value=一日前>";
                        echo "</form>";
                    }
                }
                echo "</td></table>";
                
                echo "</div>";
            } else {
                echo "データが一件も入力されていません";
            }
        }
    } catch (Exception $e) {
        echo 'エラーが発生しました。:' . $e->getMessage();
    }
    ?>
</body>

</html>