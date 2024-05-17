<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>時間別集中力グラフ</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
</head>
<body>
    <canvas id="graph_HoD1" height="250px"></canvas>
    <canvas id="graph_HoD2" height="250px"></canvas>
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
        $hour_of_day1 = [1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1];
        $hour_of_day1_count = [1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1];
        $result1 = $stm1 -> fetchAll(PDO::FETCH_ASSOC);
        foreach($result1 as $value1){
            $date1 = strtotime($value1["date"]);
            $date1_hour = date("H",$date1);
            if($date1_hour == 0){
                $hour_of_day1[0] = $hour_of_day1[0] + $value1["test_time1"];
                $hour_of_day1_count[0] += 1;
            }else if($date1_hour == 1){
                $hour_of_day1[1] = $hour_of_day1[1] + $value1["test_time1"];
                $hour_of_day1_count[1] += 1;
            }else if($date1_hour == 2){
                $hour_of_day1[2] = $hour_of_day1[2] + $value1["test_time1"];
                $hour_of_day1_count[2] += 1;
            }else if($date1_hour == 3){
                $hour_of_day1[3] = $hour_of_day1[3] + $value1["test_time1"];
                $hour_of_day1_count[3] += 1;
            }else if($date1_hour == 4){
                $hour_of_day1[4] = $hour_of_day1[4] + $value1["test_time1"];
                $hour_of_day1_count[4] += 1;
            }else if($date1_hour == 5){
                $hour_of_day1[5] = $hour_of_day1[5] + $value1["test_time1"];
                $hour_of_day1_count[5] += 1;
            }else if($date1_hour == 6){
                $hour_of_day1[6] = $hour_of_day1[6] + $value1["test_time1"];
                $hour_of_day1_count[6] += 1;
            }else if($date1_hour == 7){
                $hour_of_day1[7] = $hour_of_day1[7] + $value1["test_time1"];
                $hour_of_day1_count[7] += 1;
            }else if($date1_hour == 8){
                $hour_of_day1[8] = $hour_of_day1[8] + $value1["test_time1"];
                $hour_of_day1_count[8] += 1;
            }else if($date1_hour == 9){
                $hour_of_day1[9] = $hour_of_day1[9] + $value1["test_time1"];
                $hour_of_day1_count[9] += 1;
            }else if($date1_hour == 10){
                $hour_of_day1[10] = $hour_of_day1[10] + $value1["test_time1"];
                $hour_of_day1_count[10] += 1;
            }else if($date1_hour == 11){
                $hour_of_day1[11] = $hour_of_day1[11] + $value1["test_time1"];
                $hour_of_day1_count[11] += 1;
            }else if($date1_hour == 12){
                $hour_of_day1[12] = $hour_of_day1[12] + $value1["test_time1"];
                $hour_of_day1_count[12] += 1;
            }else if($date1_hour == 13){
                $hour_of_day1[13] = $hour_of_day1[13] + $value1["test_time1"];
                $hour_of_day1_count[13] += 1;
            }else if($date1_hour == 14){
                $hour_of_day1[14] = $hour_of_day1[14] + $value1["test_time1"];
                $hour_of_day1_count[14] += 1;
            }else if($date1_hour == 15){
                $hour_of_day1[15] = $hour_of_day1[15] + $value1["test_time1"];
                $hour_of_day1_count[15] += 1;
            }else if($date1_hour == 16){
                $hour_of_day1[16] = $hour_of_day1[16] + $value1["test_time1"];
                $hour_of_day1_count[16] += 1;
            }else if($date1_hour == 17){
                $hour_of_day1[17] = $hour_of_day1[17] + $value1["test_time1"];
                $hour_of_day1_count[17] += 1;
            }else if($date1_hour == 18){
                $hour_of_day1[18] = $hour_of_day1[18] + $value1["test_time1"];
                $hour_of_day1_count[18] += 1;
            }else if($date1_hour == 19){
                $hour_of_day1[19] = $hour_of_day1[19] + $value1["test_time1"];
                $hour_of_day1_count[19] += 1;
            }else if($date1_hour == 20){
                $hour_of_day1[20] = $hour_of_day1[20] + $value1["test_time1"];
                $hour_of_day1_count[20] += 1;
            }else if($date1_hour == 21){
                $hour_of_day1[21] = $hour_of_day1[21] + $value1["test_time1"];
                $hour_of_day1_count[21] += 1;
            }else if($date1_hour == 22){
                $hour_of_day1[22] = $hour_of_day1[22] + $value1["test_time1"];
                $hour_of_day1_count[22] += 1;
            }else if($date1_hour == 23){
                $hour_of_day1[23] = $hour_of_day1[23] + $value1["test_time1"];
                $hour_of_day1_count[23] += 1;
            }
        }
        for($i = 0;$i<count($hour_of_day1);$i++){
            if($hour_of_day1_count[$i]>1){
            $hour_of_day1[$i] = round(($hour_of_day1[$i]-1)/($hour_of_day1_count[$i]-1),1);
            }
        }
        $hour_of_day1 = json_encode($hour_of_day1);
        $hour_of_day1_count = json_encode($hour_of_day1_count);
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
            $hour_of_day2 = [1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1];
            $hour_of_day2_count = [1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1];
            $result2 = $stm2 -> fetchAll(PDO::FETCH_ASSOC);
            foreach($result2 as $value2){
                $date2 = strtotime($value2["date"]);
                $date2_week = date("H",$date2);
                if($date2_week == 0){
                    $hour_of_day2[0] = $hour_of_day2[0] + $value2["test_time2"];
                    $hour_of_day2_count[0] += 1;
                }else if($date2_week == 1){
                    $hour_of_day2[1] = $hour_of_day2[1] + $value2["test_time2"];
                    $hour_of_day2_count[1] += 1;
                }else if($date2_week == 2){
                    $hour_of_day2[2] = $hour_of_day2[2] + $value2["test_time2"];
                    $hour_of_day2_count[2] += 1;
                }else if($date2_week == 3){
                    $hour_of_day2[3] = $hour_of_day2[3] + $value2["test_time2"];
                    $hour_of_day2_count[3] += 1;
                }else if($date2_week == 4){
                    $hour_of_day2[4] = $hour_of_day2[4] + $value2["test_time2"];
                    $hour_of_day2_count[4] += 1;
                }else if($date2_week == 5){
                    $hour_of_day2[5] = $hour_of_day2[5] + $value2["test_time2"];
                    $hour_of_day2_count[5] += 1;
                }else if($date2_week == 6){
                    $hour_of_day2[6] = $hour_of_day2[6] + $value2["test_time2"];
                    $hour_of_day2_count[6] += 1;
                }else if($date2_week == 7){
                    $hour_of_day2[7] = $hour_of_day2[7] + $value2["test_time2"];
                    $hour_of_day2_count[7] += 1;
                }else if($date2_week == 8){
                    $hour_of_day2[8] = $hour_of_day2[8] + $value2["test_time2"];
                    $hour_of_day2_count[8] += 1;
                }else if($date2_week == 9){
                    $hour_of_day2[9] = $hour_of_day2[9] + $value2["test_time2"];
                    $hour_of_day2_count[9] += 1;
                }else if($date2_week == 10){
                    $hour_of_day2[10] = $hour_of_day2[10] + $value2["test_time2"];
                    $hour_of_day2_count[10] += 1;
                }else if($date2_week == 11){
                    $hour_of_day2[11] = $hour_of_day2[11] + $value2["test_time2"];
                    $hour_of_day2_count[11] += 1;
                }else if($date2_week == 12){
                    $hour_of_day2[12] = $hour_of_day2[12] + $value2["test_time2"];
                    $hour_of_day2_count[12] += 1;
                }else if($date2_week == 13){
                    $hour_of_day2[13] = $hour_of_day2[13] + $value2["test_time2"];
                    $hour_of_day2_count[13] += 1;
                }else if($date2_week == 14){
                    $hour_of_day2[14] = $hour_of_day2[14] + $value2["test_time2"];
                    $hour_of_day2_count[14] += 1;
                }else if($date2_week == 15){
                    $hour_of_day2[15] = $hour_of_day2[15] + $value2["test_time2"];
                    $hour_of_day2_count[15] += 1;
                }else if($date2_week == 16){
                    $hour_of_day2[16] = $hour_of_day2[16] + $value2["test_time2"];
                    $hour_of_day2_count[16] += 1;
                }else if($date2_week == 17){
                    $hour_of_day2[17] = $hour_of_day2[17] + $value2["test_time2"];
                    $hour_of_day2_count[17] += 1;
                }else if($date2_week == 18){
                    $hour_of_day2[18] = $hour_of_day2[18] + $value2["test_time2"];
                    $hour_of_day2_count[18] += 1;
                }else if($date2_week == 19){
                    $hour_of_day2[19] = $hour_of_day2[19] + $value2["test_time2"];
                    $hour_of_day2_count[19] += 1;
                }else if($date2_week == 20){
                    $hour_of_day2[20] = $hour_of_day2[20] + $value2["test_time2"];
                    $hour_of_day2_count[20] += 1;
                }else if($date2_week == 21){
                    $hour_of_day2[21] = $hour_of_day2[21] + $value2["test_time2"];
                    $hour_of_day2_count[21] += 1;
                }else if($date2_week == 22){
                    $hour_of_day2[22] = $hour_of_day2[22] + $value2["test_time2"];
                    $hour_of_day2_count[22] += 1;
                }else if($date2_week == 23){
                    $hour_of_day2[23] = $hour_of_day2[23] + $value2["test_time2"];
                    $hour_of_day2_count[23] += 1;
                }
            }
            for($j = 0;$j<count($hour_of_day2);$j++){
                if($hour_of_day2_count[$j]>1){
                $hour_of_day2[$j] = round(($hour_of_day2[$j]-1)/($hour_of_day2_count[$j]-1),1);
                }
            }
            $hour_of_day2 = json_encode($hour_of_day2);
            $hour_of_day2_count = json_encode($hour_of_day2_count);
        }
        }catch(Exception $e){
            echo  '<span class="error">エラーがありました。</span><br>';
            echo $e->getMessage();
        }
        //var_dump($hour_of_day1);
        //var_dump($hour_of_day2);
    ?>

    <script>
    let hourOfDay1 = JSON.parse(<?php echo json_encode($hour_of_day1);?>);
    let hourOfDay1Count = JSON.parse(<?php echo json_encode($hour_of_day1_count);?>);
    for(i=0;i<hourOfDay1Count.length;i++){
        if(hourOfDay1Count[i] == 1){
            hourOfDay1[i] -= 1;
        }
    }
    let ctx1 = document.getElementById("graph_HoD1").getContext("2d");
    let myLineChart1 = new Chart(ctx1,{
        type : "line",
        data:{
            labels:["00:00","01:00","02:00","03:00","04:00","05:00","06:00","07:00","08:00","09:00","10:00","11:00","12:00","13:00","14:00","15:00","16:00","17:00","18:00","19:00","20:00","21:00","22:00","23:00"],
            datasets:[{
                label:"テスト1平均",
                backgroundColor:"rgb(255,99,132)",
                borderColor:"rgb(255,99,132)",
                data: hourOfDay1
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
                text:'時間帯別テスト集計グラフ１'
            }
        }
    })
    let hourOfDay2 = JSON.parse(<?php echo json_encode($hour_of_day2);?>);
    let hourOfDay2Count = JSON.parse(<?php echo json_encode($hour_of_day2_count);?>);
    for(j=0;j<hourOfDay2Count.length;j++){
        if(hourOfDay2Count[j] == 1){
            hourOfDay2[j] -= 1;
        }
    }
    let ctx2 = document.getElementById("graph_HoD2").getContext("2d");
    let myLineChart2 = new Chart(ctx2,{
        type : "line",
        data:{
            labels:["00:00","01:00","02:00","03:00","04:00","05:00","06:00","07:00","08:00","09:00","10:00","11:00","12:00","13:00","14:00","15:00","16:00","17:00","18:00","19:00","20:00","21:00","22:00","23:00"],
            datasets:[{
                label:"テスト2平均",
                backgroundColor:"rgb(117,169,255)",
                borderColor:"rgb(117,169,255)",
                data: hourOfDay2
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
                text:'時間帯別テスト集計グラフ2'
            }
        }
    })

    </script>
</body>
</html>