<?php 

require("./Line_push.php");

$msg =["体内時計の乱れている人の特徴","https://fukuiohr2.sakura.ne.jp/2021/Midare/Life_disorder/judge1.html"];
Line_push($msg[0]."\n".$msg[1],"U587ecb664ad32caea6ce9af3661d554b");

require("return_id.php");
$result = ret_user_id(1);
var_dump($msg[0]);
var_dump($result);
$result = ret_profile_id($result);
var_dump($result);
/*
require("db_connect.php");
try {
    $id1 = "U56b0b4f8d30e72b5b3cf24d963c961da";
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
var_dump($wake_up_time);
*/
?>