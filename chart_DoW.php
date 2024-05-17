<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>曜日別集中力グラフ</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
</head>
<body>
    <canvas id="graph_DoW1" height="250px"></canvas>
    <canvas id="graph_DoW2" height="250px"></canvas>
    <?php
        require("db_connect.php");
        require("return_id.php");
        $user_id = ret_user_id((int)$_GET["profile_id"]);
        //$user_id = ret_user_id(4);
    try{
    $sql1 = "SELECT * FROM concentration WHERE user_id = :user_id AND test_time1 IS NOT NULL";
    $stm1 = $pdo -> prepare($sql1);
    $stm1 -> bindValue(":user_id",$user_id,PDO::PARAM_STR);
    if($stm1 -> execute()){
        $day_of_week1 = [1,1,1,1,1,1,1];
        $day_of_week1_count = [1,1,1,1,1,1,1];
        $result1 = $stm1 -> fetchAll(PDO::FETCH_ASSOC);
        foreach($result1 as $value1){
            $date1 = strtotime($value1["date"]);
            $date1_week = date("w",$date1);
            if($date1_week == 0){
                $day_of_week1[0] = $day_of_week1[0] + $value1["test_time1"];
                $day_of_week1_count[0] += 1;
            }
            else if($date1_week == 1){
                $day_of_week1[1] = $day_of_week1[1] + $value1["test_time1"];
                $day_of_week1_count[1] += 1;
            }else if($date1_week == 2){
                $day_of_week1[2] = $day_of_week1[2] + $value1["test_time1"];
                $day_of_week1_count[2] += 1;
            }else if($date1_week == 3){
                $day_of_week1[3] = $day_of_week1[3] + $value1["test_time1"];
                $day_of_week1_count[3] += 1;
            }else if($date1_week == 4){
                $day_of_week1[4] = $day_of_week1[4] + $value1["test_time1"];
                $day_of_week1_count[4] += 1;
            }else if($date1_week == 5){
                $day_of_week1[5] = $day_of_week1[5] + $value1["test_time1"];
                $day_of_week1_count[5] += 1;
            }else if($date1_week == 6){
                $day_of_week1[6] = $day_of_week1[6] + $value1["test_time1"];
                $day_of_week1_count[6] += 1;
            }
        }

        for($i = 0;$i<count($day_of_week1);$i++){
            if($day_of_week1_count[$i]>1){
                $day_of_week1[$i] = round(($day_of_week1[$i]-1)/($day_of_week1_count[$i]-1),1);
            }
        }
        $day_of_week1 = json_encode($day_of_week1);
        $day_of_week1_count = json_encode($day_of_week1_count);
    }
    }catch(Exception $e){
        echo  '<span class="error">エラーがありました。</span><br>';
        echo $e->getMessage();
    }
    try{
        $sql2 = "SELECT * FROM concentration WHERE user_id = :user_id AND test_time2 IS NOT NULL";
        $stm2 = $pdo -> prepare($sql2);
        $stm2 -> bindValue(":user_id",$user_id,PDO::PARAM_STR);
        if($stm2 -> execute()){
            $day_of_week2 = [1,1,1,1,1,1,1];
            $day_of_week2_count = [1,1,1,1,1,1,1];
            $result2 = $stm2 -> fetchAll(PDO::FETCH_ASSOC);
            foreach($result2 as $value2){
                $date2 = strtotime($value2["date"]);
                $date2_week = date("w",$date2);
                if($date2_week == 0){
                    $day_of_week2[0] = $day_of_week2[0] + $value2["test_time2"];
                    $day_of_week2_count[0] += 1;
                }
                else if($date2_week == 1){
                    $day_of_week2[1] = $day_of_week2[1] + $value2["test_time2"];
                    $day_of_week2_count[1] += 1;
                }else if($date2_week == 2){
                    $day_of_week2[2] = $day_of_week2[2] + $value2["test_time2"];
                    $day_of_week2_count[2] += 1;
                }else if($date2_week == 3){
                    $day_of_week2[3] = $day_of_week2[3] + $value2["test_time2"];
                    $day_of_week2_count[3] += 1;
                }else if($date2_week == 4){
                    $day_of_week2[4] = $day_of_week2[4] + $value2["test_time2"];
                    $day_of_week2_count[4] += 1;
                }else if($date2_week == 5){
                    $day_of_week2[5] = $day_of_week2[5] + $value2["test_time2"];
                    $day_of_week2_count[5] += 1;
                }else if($date2_week == 6){
                    $day_of_week2[6] = $day_of_week2[6] + $value2["test_time2"];
                    $day_of_week2_count[6] += 1;
                }
            }
    
            for($j = 0;$j<count($day_of_week2);$j++){
                if($day_of_week2_count[$j]>1){
                $day_of_week2[$j] = round(($day_of_week2[$j]-1)/($day_of_week2_count[$j]-1),1);
                }
            }
            $day_of_week2 = json_encode($day_of_week2);
            $day_of_week2_count = json_encode($day_of_week2_count);
        }
        }catch(Exception $e){
            echo  '<span class="error">エラーがありました。</span><br>';
            echo $e->getMessage();
        }

        //var_dump($day_of_week1);
        //var_dump($day_of_week2);
    ?>

    <script>
    let dayOfWeek1 = JSON.parse(<?php echo json_encode($day_of_week1);?>);
    let dayOfWeek1Count = JSON.parse(<?php echo json_encode($day_of_week1_count);?>);
    for(i=0;i<dayOfWeek1Count.length;i++){
        if(dayOfWeek1Count[i] == 1){
            dayOfWeek1[i] -= 1;
        }
    }
    let ctx1 = document.getElementById("graph_DoW1").getContext("2d");
    let myLineChart1 = new Chart(ctx1,{
        type : "line",
        data:{
            labels:["日曜日","月曜日","火曜日","水曜日","木曜日","金曜日","土曜日"],
            datasets:[{
                label:"テスト1平均",
                backgroundColor:"rgb(255,99,132)",
                borderColor:"rgb(255,99,132)",
                data: dayOfWeek1
            }]
        },
        options:{
            scales:{
                yAxes:[
                    {
                        ticks:{
                            min:0,
                            max:20,
                            stepSize:2
                        }
                    }
                ]
            },
            title:{
                display:true,
                text:'曜日別テスト集計グラフ１'
            }
        }
    })
    let dayOfWeek2 = JSON.parse(<?php echo json_encode($day_of_week2);?>);
    let dayOfWeek2Count = JSON.parse(<?php echo json_encode($day_of_week2_count);?>);
    for(j=0;j<dayOfWeek2Count.length;j++){
        if(dayOfWeek2Count[j] == 1){
            dayOfWeek2[j] -= 1;
        }
    }
    let ctx2 = document.getElementById("graph_DoW2").getContext("2d");
    let myLineChart2 = new Chart(ctx2,{
        type : "line",
        data:{
            labels:["日曜日","月曜日","火曜日","水曜日","木曜日","金曜日","土曜日"],
            datasets:[{
                label:"テスト2平均",
                backgroundColor:"rgb(117,169,255)",
                borderColor:"rgb(117,169,255)",
                data: dayOfWeek2
            }]
        },
        options:{
            scales:{
                yAxes:[
                    {
                        ticks:{
                            min:0,
                            max:8,
                            stepSize:2
                        }
                    }
                ]
            },
            title:{
                display:true,
                text:'曜日別テスト集計グラフ2'
            }
        }
    })
    </script>
</body>
</html>