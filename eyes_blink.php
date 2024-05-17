<?php

set_time_limit(43200);
$start_time = time();
$output = [];
$cmd = "eye_blink_detector_dlib4.exe";
exec($cmd,$output,$result);

$stop_time = time();
$test_time = (float)(($stop_time - $start_time) / 600);
$cpm = (int)((int)$output[0] / $test_time);
var_dump($output);
var_dump($result);
var_dump($cpm);
var_dump($start_time);
var_dump($stop_time);
var_dump($test_time);

//echo $result;

?>