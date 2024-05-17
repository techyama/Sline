<?php
require("./db_connect.php"); //ＤＢアクセス
$profile_id = $_POST["profile_id"];
//テーブルで表示する行の数ループ
for($i = 0; $i < $_POST['row_num']; $i++){
    $delete_id = $_POST['row'.$i];
    //値があればその行を削除
    if(is_null($delete_id)) {
        continue;
    } else {
        $sql = "DELETE FROM sleep where sleep_id = :delete_id";
        $stm = $pdo->prepare($sql);
        $stm->bindValue(":delete_id", $delete_id, PDO::PARAM_STR);
        $stm->execute();
        $j++;
    }
}
$delete_row = $j."行削除しました。";
header("Location: https://fukuiohr2.sakura.ne.jp/2021/Midare/Life_disorder/sleep_table.php?profile_id=$profile_id&delete_row=$delete_row");
exit;