<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>相関グラフ</title>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.3.0/Chart.bundle.min.js"></script>
</head>

<body>
<div class="container" style="width:100%">
    <canvas id="canvas"></canvas>
</div>
    <?php
    require("db_connect.php");
    require("return_id.php");
    $user_id = ret_user_id((int)$_GET["profile_id"]);
    //$user_id ="Ueafe182d5df6af9674a974b35c52b3d8";
    $labels = [];
    $data = [];
    $question_count = [];
    try {
        $sql1 = "SELECT * FROM (SELECT * FROM sleep WHERE user_id=:user_id ORDER BY out_time  DESC LIMIT 30) AS A ORDER BY out_time";
        $stm1 = $pdo->prepare($sql1);
        $stm1->bindValue(":user_id", $user_id, PDO::PARAM_STR);
        if ($stm1->execute()) {
            $result1 = $stm1->fetchAll(PDO::FETCH_ASSOC);
            foreach($result1 as $value){
                $tmp = explode(" ",$value["out_time"]);
                $labels[] = $tmp[0];
                $time = explode(":",$tmp[1]);
                $hour = $time[0];
                $minutes = $time[1];
                $data[] = abs(($hour * 60 + $minutes) - $value["wake_up_time"]);
                if(!is_null($value["question"])){
                    $count = 0;
                    $question = str_split($value["question"]);
                    for($i = 0;$i<count($question);$i++){
                        if($question[$i] == 1){
                            $count += 1;
                        }
                    }
                    $question_count[] = $count;
                }else{
                    $question_count[] = 1;
                }
            }
            $labels = json_encode($labels);
            $data = json_encode($data);
            $question_count = json_encode($question_count);
        }
    } catch (Exception $e) {
        echo  '<span class="error">エラーがありました。</span><br>';
        echo $e->getMessage();
    }
    ?>
       





<script>
window.onload = function() {
    ctx = document.getElementById("canvas").getContext("2d");
    window.myBar = new Chart(ctx, {
        type: 'bar',
        data: barChartData,
        options: complexChartOption
    });
};
</script>



<script>
    let label = JSON.parse(<?php echo json_encode($labels);?>);
    let data_outtime = JSON.parse(<?php echo json_encode($data);?>)
    let count_data = JSON.parse(<?php echo json_encode($question_count);?>);

    for(let i = 0;i<count_data.length;i++){
       count_data[i] = count_data[i] - 1;
    }
    console.log(count_data);

var barChartData = {
    labels:label,
    datasets: [
    {
        type: 'line',
        label: 'アンケートチェック数',
        data: count_data,
        borderColor : "rgba(254,97,132,0.8)",
                pointBackgroundColor    : "rgba(254,97,132,0.8)",
                fill: false,
        yAxisID: "y-axis-1",// 追加
    },
    {
        type: 'bar',
        label: '起床時間ズレ',
        data: data_outtime,
        borderColor : "rgba(54,164,235,0.8)",
        backgroundColor : "rgba(54,164,235,0.5)",
        yAxisID: "y-axis-2",
    },
    ],
};
</script>



<script>
var complexChartOption = {
    responsive: true,
    scales: {
        yAxes: [{
            id: "y-axis-1",
            type: "linear", 
            position: "left",
            ticks: {
                max: 8,
                min: 0,
                stepSize: 1
            },
        }, {
            id: "y-axis-2",
            type: "linear", 
            position: "right",
            ticks: {
                max: 240,
                min: 0,
                stepSize: 60
            },
            gridLines: {
                drawOnChartArea: false, 
            },
        }],
    }
};
</script>

</body>

</html>