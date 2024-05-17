<?php

// 1日=86400秒
define("DSEC", 86400);

// db query
require("db_connect.php");
require("return_id.php");
$user_id = ret_user_id($_GET["profile_id"]);
$stm = $pdo->prepare("select * from sleep where user_id = :user_id order by in_time desc LIMIT 0,7");
//$stm = $pdo->query("select in_time, out_time from sleep where user_id = ". "2". " order by out_time desc");
$stm->bindValue(":user_id", $user_id, PDO::PARAM_STR);
if ($stm->execute()) {
  $result = $stm->fetchAll(PDO::FETCH_ASSOC);
}
$result = array_slice($result, 0, 7, true);
asort($result);

// グラフ向けのデータ生成所
$graphdata = [];

foreach ($result as $row) {
  // in_timeとout_timeをunix化
  $row["in_time"] = strtotime($row["in_time"]);
  $row["out_time"] = strtotime($row["out_time"]);

  if (date("y/m/d", $row["in_time"]) == date("y/m/d", $row["out_time"])) {
    // if you check assertion
    if ($row["in_time"] % DSEC > $row["out_time"] % DSEC) {
      var_dump($row);
      die("整合性のとれないデータがありました。\n");
    }
    // 日付を跨がないデータ
    $graphdata[date("y/m/d", $row["in_time"])][] = [
      "start" => $row["in_time"] % DSEC,
      "end" => $row["out_time"] % DSEC,
      "type" => "sleep"
    ];
  } else {
    // if you check assertion
    if ($row["in_time"] > $row["out_time"]) {
      var_dump($row);
      die("整合性のとれないデータがありました。\n");
    }
    // 日付を跨ぐデータ
    for ($start = intval(floor($row["in_time"] / 86400) * DSEC); $start <= $row["out_time"]; $start += DSEC) {
      $end = $start + DSEC - 1;
      $graphdata[date("Y/m/d", $start)][] = [
        "start" => max($start, $row["in_time"]) % DSEC,
        "end" => min($end, $row["out_time"]) % DSEC,
        "type" => "sleep"
      ];
    }
  }
}
// sleepでない箇所をawakeで埋める
$maxswitch = 0;
foreach (array_keys($graphdata) as $day) {

  sort($graphdata[$day]);
  // [0]["start"]が0でない時、その日は起床から始まる
  if ($graphdata[$day][0]["start"] != 0) {
    $graphdata[$day][] = [
      "start" => 0, "end" => $graphdata[$day][0]["start"], "type" => "awake"
    ];
    sort($graphdata[$day]);
  }

  // sleepデータの間をawakeで埋める
  for ($i = 0; $i < count($graphdata[$day]) - 1; $i++) {
    if ($graphdata[$day][$i]["type"] == "sleep" && $graphdata[$day][$i + 1]["type"] == "sleep") {
      $graphdata[$day][] = [
        "start" => $graphdata[$day][$i]["end"],
        "end" => $graphdata[$day][$i + 1]["start"],
        "type" => "awake"
      ];
    }
    sort($graphdata[$day]);
  }

  // [count($graphdata[$day]) - 1]["end"] がDSEC - 1でない時
  if ($graphdata[$day][count($graphdata[$day]) - 1]["end"] != DSEC - 1) {
    $x = count($graphdata[$day]) - 1;
    $graphdata[$day][] = [
      "start" => $graphdata[$day][$x]["end"], "end" => DSEC, "type" => "awake"
    ];
    sort($graphdata[$day]);
  }

  // awakeから始まる日は長さ0のsleepを追加しておく
  if ($graphdata[$day][0]["type"] == "awake") {
    $graphdata[$day][] = [
      "start" => 0, "end" => 0, "type" => "sleep"
    ];
    sort($graphdata[$day]);
  }

  // 切り替わり回数の最大値を取得
  $maxswitch = max($maxswitch, count($graphdata[$day]));

  // durationを計算して追加
  for ($i = 0; $i < count($graphdata[$day]); $i++) {
    $graphdata[$day][$i]["duration"] = $graphdata[$day][$i]["end"] - $graphdata[$day][$i]["start"];
  }
}

// graph用の配列を作成する
$graph_y = [];
for ($i = 0; $i < $maxswitch; $i++) {
  $graph_y[] = [];
  foreach (array_keys($graphdata) as $day) {
    if (isset($graphdata[$day][$i]["duration"])) {
      $graph_y[$i][] = $graphdata[$day][$i]["duration"];
    } else {
      $graph_y[$i][] = 0;
    }
  }
}

// jpgraph including
include("src/jpgraph.php");
include("src/jpgraph_bar.php");

// graph setup
$graph = new Graph(500, 400);
$graph->SetFrame(true);
$graph->SetScale("textlin", 0, 86400);
$graph->yscale->ticks->Set(7200, 3600);
$graph->Set90AndMargin(100, 50, 50, 50);

// barplotを作成
$barplots = [];
foreach ($graph_y as $key => $gp) {
  $barplot = new BarPlot($gp);
  $barplots[] = $barplot;
}

// accbarplotを作成
$accbarplot = new AccBarPlot($barplots);

// add data
$graph->Add($accbarplot);

// set color of barplot
foreach ($barplots as $i => $bp) {
  if (!($i % 2)) {
    $bp->SetColor("red");
    $bp->SetFillColor("red");
  } else {
    $bp->SetColor("gray");
    $bp->SetFillColor("gray");
  }
}

// set label x
$graph->xaxis->SetTickLabels(array_keys($graphdata));

// make label y
$ylabels = [];
for ($i = 0; $i <= 86400; $i++) {
  $ylabels[] = date("H:i:s", $i);
}
// set label y
$graph->yaxis->SetTickLabels($ylabels);
$graph->yaxis->SetLabelAngle(45);

//finalize
$graph->Stroke();
