<?php

function sleep_judge($user_id){
require("db_connect.php");
$count = 1;
$i = 0;
$judge = "";
$judge_msg = "";
$warning = "";
$warning_msg = "";

try {
    $sql = "SELECT * FROM(SELECT * FROM sleep WHERE user_id=:user_id ORDER BY in_time desc LIMIT 0,30) as result";
    $stm = $pdo->prepare($sql);
    $stm->bindValue(":user_id", $user_id, PDO::PARAM_STR);
    if ($stm->execute()) {
        $result = $stm->fetchAll(PDO::FETCH_ASSOC);
        if (count($result) > 1) {
            foreach ($result as $value) { //30件分のレコード表示
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
                    $sleep_out_time_pre = $sleep_out_time_pre - 1440;
                }
                //１件前のレコードと比べての評価
                $msg = abs($sleep_out_time - $value["wake_up_time"]) < abs($sleep_out_time_pre - $result[$i]["wake_up_time"]) ? "改善" : "悪化";

                $ap_time = abs($sleep_out_time - $value["wake_up_time"]);

                if($ap_time <= 10 && $i ==1){
                    break;
                }
                if ($msg == "悪化" || $msg == "改善") {
                    if($judge == ""){
                        $judge = $msg;
                    }else{
                        if($judge == $msg){
                            $count += 1;
                        }else{
                            break;
                        }
                    }
                }
            }

            if($ap_time <= 10 && $i == 1){
                $judge_msg = "規則正しく起きれています。\nその調子で頑張りましょう！！";
            }else{
            if($judge == "悪化"){
                switch($count){
                    case 1 :
                        $judge_msg = "悪化に移行";
                        break;
                    default :
                        $judge_msg .= "悪化継続"."$count"."日連続悪化中";
                        break;
                }
                if($count >= 7){
                    $warning_msg = "体内時計の乱れがひき起こす症状や病気";
                    $warning = "https://fukuiohr2.sakura.ne.jp/2021/Midare/Life_disorder/judge3.html";
                }
                else if($count >= 5){
                    $warning_msg = "体内時計のリセット方法";
                    $warning = "https://fukuiohr2.sakura.ne.jp/2021/Midare/Life_disorder/judge2.html";
                }
                else if($count >=3){
                    $warning_msg = "体内時計の乱れている人の特徴";
                    $warning = "https://fukuiohr2.sakura.ne.jp/2021/Midare/Life_disorder/judge1.html";
                }


                
                
            }else{
                switch($count){
                    case 1 :
                        $judge_msg = "改善に移行";
                        break;
                    default :
                        $judge_msg .= "改善継続"."$count"."日連続改善中";
                        break;
                }

            }
        }
        }
    }
} catch (Exception $e) {
    echo 'エラーが発生しました。:' . $e->getMessage();
}
return [$judge_msg,$warning,$warning_msg];
}
//var_dump(sleep_judge("U587ecb664ad32caea6ce9af3661d554b"));
?>
