<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>起床時間入力フォーム</title>
    <link rel="stylesheet" href="./css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
<div class="logoimg">	
	<img src="./image/logo2.jpg">
    <h2 class="title">起床時間入力フォーム</h2>
	</div>
    <div class="out">

        <?php
        require("./db_connect.php");
        require("Line_push.php");
        require("return_id.php");
        if (date_default_timezone_get() != "asia/Tokyo") {
            date_default_timezone_set("asia/Tokyo");
        }
        $user_id = ret_user_id((int)$_GET["profile_id"]);
        $date_now = time();
        $date_now = $date_now - 86400;
        $date_now = date("Y-m-d H:i:s",$date_now);
        try{
        $sql = "SELECT count(*)as cnt FROM sleep WHERE user_id = :user_id and in_time > :in_time";
        $stm = $pdo->prepare($sql);
        $stm->bindValue(":in_time",$date_now,PDO::PARAM_STR);
        $stm->bindValue(":user_id",$user_id,PDO::PARAM_STR);
        if($stm->execute()){
            $result = $stm->fetchAll(PDO::FETCH_ASSOC);
        }
    }catch(Exception $e){
        echo  '<span class="error">エラーがありました。</span><br>';
        echo $e->getMessage();
    }
    //var_dump($result);
        if($result[0]["cnt"] == 0){
            echo "<h3>就寝時間の登録からされていません。<br>下のリンクから就寝時間の登録と合わせて起床時間の登録をしてください。<br><a href=https://fukuiohr2.sakura.ne.jp/2021/Midare/Life_disorder/sleeptime_input.php?user_id=$user_id>こちら！！！</a></h3>";
            exit();
        }
        ?>
        <form method="POST" action="outtime_output.php">
            <ul>
                <h2>睡眠時間入力フォーム</h2>
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