<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div>

    <?php
    function db_result($user_id){
    require("db_connect.php");
    $count = 0;
    $ave = 0;
    $test_result = 0;
    $test_time_sum = 0;
    $sum_test = 0;
    $id1 = $user_id;
    try{
        $sql = "SELECT * FROM(SELECT * FROM sleep WHERE user_id=:user_id ORDER BY in_time desc LIMIT 0,30) result";
        $sql1 = "SELECT * FROM(SELECT * FROM concentration WHERE user_id=:user_id AND test_time1 IS NOT NULL ORDER BY test_id desc LIMIT 0,30) result";
        $sql2 = "SELECT * FROM(SELECT * FROM concentration WHERE user_id=:user_id AND test_time2 IS NOT NULL ORDER BY test_id desc LIMIT 0,30) result";
        $stm = $pdo->prepare($sql);
        $stm1 = $pdo -> prepare($sql1);
        $stm2 = $pdo -> prepare($sql2);
        $stm -> bindValue(':user_id', $id1, PDO::PARAM_STR);
        $stm1 -> bindValue(':user_id', $id1, PDO::PARAM_STR);
        $stm2 -> bindValue(':user_id', $id1, PDO::PARAM_STR);
        if($stm->execute()){
        $result = $stm->fetchAll(PDO::FETCH_ASSOC);
        foreach($result as $value){
			$time_out = strtotime($value["out_time"]);
            $time_out_hour = date("H",$time_out);
            $time_out_minutes = date("i",$time_out);
            $time_out_second = date("s",$time_out);
            $sum_time =$time_out_hour * 3600 + $time_out_minutes * 60 + $time_out_second; 
            $count += 1;
            $sum = $sum + $sum_time;
        }
        $ave = $sum / $count;
    }
    if($stm2 -> execute()){
        $result2 = $stm2 -> fetchAll(PDO::FETCH_ASSOC);
        if(count($result2) > 0){
        foreach($result2 as $value2){
            $sum_test += $value2["test_time2"]; 
        }
        $test_result2 = round((float)($sum_test / count($result2)),3);
    }else{
        $test_result2 = "一度もやってないよ";
    }
    }
    if($stm1 -> execute()){
        $result1 = $stm1 -> fetchAll(PDO::FETCH_ASSOC);
        if(count($result1) != 0){
            foreach($result1 as $value1){
                $test_time_sum += $value1["test_time1"];
            }
            $test_result = (float)($test_time_sum / count($result1));
            return [(int)$ave,round($test_result,3),$test_result2];
        }
        else{
            return [(int)$ave,"一度もやっていないよ",$test_result2];
        }
    }

        
    } catch (Exception $e) { 
        echo 'エラーが発生しました。:' . $e->getMessage();
    }

}
    
    //var_dump(db_result("Ueafe182d5df6af9674a974b35c52b3d8"))
    ?>
    </div>
</body>
</html>