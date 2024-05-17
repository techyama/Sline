<?php // 入力ストリームからパラメータを取得 
$raw = file_get_contents('php://input');
$receive = json_decode($raw, true); // イベントを取得
$event = $receive['events'][0]; // 返信するトークンを取得 
$timestamp = $event['timestamp']; //送信した時間をタイムスタンプで取得
$replyToken = $event['replyToken']; // 返事するメッセージを作成  
$user_id = $event['source']['userId']; //ユーザーID取得
$receive_message = $event['message']['text']; //送られてきたメッセージ取得
$message = []; //メッセージの初期化
require("./db_connect.php"); //ＤＢアクセス
require("Line_push.php"); //LinePush読み込み（Push関数）
require("result.php"); //result.php読み込み(平均起床時間、テスト平均)
require("sleep_judge.php"); //sleep_judge.php読み込み(悪化or改善の傾向出力)
require("return_id.php"); //user_id,profile_id取得用関数読み込み
//タイムゾーンを東京にセット
if (date_default_timezone_get() != "asia/Tokyo") {
    date_default_timezone_set("asia/Tokyo");
}
$profile_id = ret_profile_id($user_id);


//プロフィールを登録してあるか確認
try {
    $sql1 = "SELECT count(*) as count_profile FROM profile WHERE user_id = :user_id ";
    $stm1 = $pdo->prepare($sql1);
    $stm1->bindValue(":user_id", $user_id, PDO::PARAM_STR);
    if ($stm1->execute()) {
        $result = $stm1->fetchAll(PDO::FETCH_ASSOC);
        $count_profile = $result[0]['count_profile'];
    }
} catch (Exception $e) {
    echo  '<span class="error">エラーがありました。</span><br>';
    echo $e->getMessage();
}
if ($count_profile == '0') { //プロフィール登録されていない場合　
    //プロフィール設定のためのリンクをメッセージに設定
    $message = [
        'type' => 'text',
        'text' => "プロフィールを設定してください。\n下のリンクから登録画面にとべます。\nhttps://fukuiohr2.sakura.ne.jp/2021/Midare/Life_disorder/profile_input.php?user_id=$user_id",
    ];
} else { //プロフィール登録されている場合
    try {
        $sql5 = "SELECT * FROM profile WHERE user_id = :user_id";
        $stm5 = $pdo->prepare($sql5);
        $stm5->bindValue(":user_id", $user_id, PDO::PARAM_STR);
        if ($stm5->execute()) {
            $result_profile = $stm5->fetchAll(PDO::FETCH_ASSOC);
            $wake_up_time = $result_profile[0]["wake_up_time"];
        }
    } catch (Exception $e) {
        echo  '<span class="error">エラーがありました。</span><br>';
        echo $e->getMessage();
    }
    if ($receive_message == "おやすみ") { //就寝時間の登録
        try {
            $sql3 = "SELECT * FROM sleep WHERE user_id = :user_id ORDER BY in_time DESC LIMIT 0,1";

            $stm3 = $pdo->prepare($sql3);

            $stm3->bindValue(":user_id", $user_id, PDO::PARAM_STR);

            if ($stm3->execute()) {
                $result = $stm3->fetchAll(PDO::FETCH_ASSOC);
                $sleep_id = $result[0]['sleep_id'];
                $out_time = $result[0]['out_time'];
            }
        } catch (Exception $e) {
            echo  '<span class="error">エラーがありました。</span><br>';
            echo $e->getMessage();
        }
        if (!is_null($out_time)) {
            $date = date("Y-m-d H:i:s", (int)($timestamp / 1000));
            $sql2 = "INSERT INTO sleep (user_id,in_time) VALUE (:user_id,:in_time)";
            $stm2 = $pdo->prepare($sql2);
            $stm2->bindValue(":user_id", $user_id, PDO::PARAM_STR);
            $stm2->bindValue(":in_time", $date, PDO::PARAM_STR);
            if ($stm2->execute()) {
                $message = [
                    'type' => 'text',
                    'text' => "今日もお疲れ様\n$date に就寝",
                ];
            }
        } else {
            $message = [
                'type' => 'text',
                'text' => "前回の起床時間の登録がされていないため、登録できません。\n登録（起床）\nhttps://fukuiohr2.sakura.ne.jp/2021/Midare/Life_disorder/outtime_input.php?profile_id=$profile_id",
            ];
            //Line_push("登録（起床）\nhttps://fukuiohr2.sakura.ne.jp/2021/Midare/Life_disorder/outtime_input.php?profile_id=$profile_id", $user_id);
        }
    } else if ($receive_message == "おはよう") { //起床時間の登録
        try {
            $sql3 = "SELECT * FROM sleep WHERE user_id = :user_id ORDER BY in_time DESC LIMIT 0,1";

            $stm3 = $pdo->prepare($sql3);

            $stm3->bindValue(":user_id", $user_id, PDO::PARAM_STR);

            if ($stm3->execute()) {
                $result = $stm3->fetchAll(PDO::FETCH_ASSOC);
                $sleep_id = $result[0]['sleep_id'];
                $out_time = $result[0]['out_time'];
            }
        } catch (Exception $e) {
            echo  '<span class="error">エラーがありました。</span><br>';
            echo $e->getMessage();
        }
        if (count($result) > 0) {
            if (is_null($out_time)) { //起床時間が入っていないか判定（入っている場合就寝時間の登録がしてない）
                $date = date("Y-m-d H:i:s", (int)($timestamp / 1000));
                $sql4 = "UPDATE sleep SET out_time = :out_time, wake_up_time = :wake_up_time WHERE sleep_id = :sleep_id";
                $stm4 = $pdo->prepare($sql4);
                $stm4->bindValue(":out_time", $date, PDO::PARAM_STR);
                $stm4->bindValue(":sleep_id", $sleep_id, PDO::PARAM_INT);
                $stm4->bindvalue(":wake_up_time", $wake_up_time, PDO::PARAM_STR);
                $out_time_unixtime = strtotime($date);
                $out_time_hour = date("H", $out_time_unixtime);
                $out_time_minutes = date("i", $out_time_unixtime);
                $out_time_result = $out_time_hour * 60 + $out_time_minutes;
                $wake_up_time_result = (int)$wake_up_time - (int)$out_time_result;
                $wake_up_time_result = abs($wake_up_time_result);
                $result_hour = (int)($wake_up_time_result / 60);
                $result_minutes = $wake_up_time_result % 60;
                if ($stm4->execute()) {
                    $message = [
                        'type' => 'text',
                        'text' => "おはようございます。\n今日も一日元気に行きましょう！！\n$date に起床\n普段起きる時間と$result_hour 時間$result_minutes 分ズレがあるよ。\n睡眠アンケート\nhttps://fukuiohr2.sakura.ne.jp/2021/Midare/Life_disorder/question_input.php?profile_id=$profile_id",
                    ];
                    //Line_push("睡眠アンケート\nhttps://fukuiohr2.sakura.ne.jp/2021/Midare/Life_disorder/question_input.php?profile_id=$profile_id", $user_id);
                }
                $judge_msg = sleep_judge($user_id);
                for ($i = 0; $i < 2; $i++) {
                    if ($judge_msg[$i] != "") {
                        if ($i == 1) {
                            $msg = $judge_msg[$i + 1];
                            Line_push($msg . "\n" . $judge_msg[$i], $user_id);
                        } else {
                            Line_push($judge_msg[$i], $user_id);
                        }
                    }
                }
            } else { //就寝時間を入れ忘れた時
                $message = [
                    'type' => 'text',
                    'text' => "今日の就寝時間の登録がされていません。",
                ];
            }
        } else {
            $message = [
                'type' => 'text',
                'text' => "今日の就寝時間の登録がされていません。",
            ];
        }
    } else if ($receive_message == "結果") { //最大1月分の結果を読みだす。
        $result = db_result($user_id);
        $minutes_up = (int)($result[0] / 60);
        $hour = (int)($result[0] / 3600);
        $sleep_result = $result[0] % 3600;
        $minutes = (int)($sleep_result / 60);
        $sleep_result = $sleep_result % 60;
        $dif = abs($minutes_up - $wake_up_time);
        $hour_dif = (int)($dif / 60);
        $minutes_dif = (int)($dif % 60);
        if ($minutes_up == 0) {
            $message = [
                'type' => 'text',
                'text' => "一度も起床時間の登録がされていません。",
            ];
        } else {
            //Line_push("https://fukuiohr2.sakura.ne.jp/2021/Midare/Life_disorder/chart_LoB.php?profile_id=$profile_id", $user_id);
            $message = [
                'type' => 'text',
                'text' => "平均起床時間　$hour 時　$minutes 分 $sleep_result 秒\n起床時間差　$hour_dif 時間 $minutes_dif 分\n相関グラフ\nhttps://fukuiohr2.sakura.ne.jp/2021/Midare/Life_disorder/chart_LoB.php?profile_id=$profile_id",
            ];
            if ((int)$dif > 30) {
                Line_push("普段起きる時間とずれすぎているよ。きちんと起きなさい！！", $user_id);
            } else {
                Line_push("きちんと規則正しく生活できているね、その調子でつづけよう！！", $user_id);
            }
        }
    } else if ($receive_message == "テスト結果") {
        $result = db_result($user_id);
        if (is_float($result[1])) {
            $test_result1 = $result[1] . "秒";
        } else {
            $test_result1 = $result[1];
        }
        if (is_float($result[2])) {
            $test_result2 = $result[2] . "秒";
        } else {
            $test_result2 = $result[2];
        }
        $message = [
            'type' => 'text',
            'text' => "集中力テスト1平均：$test_result1\n集中力テスト2平均：$test_result2\nテスト1グラフ\nhttps://fukuiohr2.sakura.ne.jp/2021/Midare/Life_disorder/chart_DoW.php?profile_id=$profile_id\nテスト２グラフ\nhttps://fukuiohr2.sakura.ne.jp/2021/Midare/Life_disorder/chart_HoD.php?profile_id=$profile_id",
        ];
        //Line_push("https://fukuiohr2.sakura.ne.jp/2021/Midare/Life_disorder/chart_DoW.php?profile_id=$profile_id", $user_id);
        //Line_push("https://fukuiohr2.sakura.ne.jp/2021/Midare/Life_disorder/chart_HoD.php?profile_id=$profile_id", $user_id);
    } else if ($receive_message == "テスト1") {
        $message = [
            'type' => 'text',
            'text' => "集中力テスト１\nhttps://fukuiohr2.sakura.ne.jp/2021/Midare/Life_disorder/concentration_input.php?profile_id=$profile_id",
        ];
        //Line_push("集中力テスト１\nhttps://fukuiohr2.sakura.ne.jp/2021/Midare/Life_disorder/concentration_input.php?profile_id=$profile_id", $user_id);
    } else if ($receive_message == "テスト2") {
        $message = [
            'type' => 'text',
            'text' => "集中力テスト２\nhttps://fukuiohr2.sakura.ne.jp/2021/Midare/Life_disorder/concentration_start.php?profile_id=$profile_id",
        ];
        //Line_push("集中力テスト２\nhttps://fukuiohr2.sakura.ne.jp/2021/Midare/Life_disorder/concentration_start.php?profile_id=$profile_id", $user_id);
    } else if ($receive_message == "グラフ") {
        $result = db_result($user_id);
        if ($result[0] != 0) {
            $message = [
                'type' => 'text',
                'text' => "グラフ\nhttps://fukuiohr2.sakura.ne.jp/2021/Midare/Life_disorder/graph_image.php?profile_id=$profile_id",
            ];
            //Line_push("グラフ\nhttps://fukuiohr2.sakura.ne.jp/2021/Midare/Life_disorder/graph_image.php?profile_id=$profile_id", $user_id);
        } else {
            $message = [
                'type' => 'text',
                'text' => "一度も睡眠時間の入力がされていないよ",
            ];
            //Line_push("一度も睡眠時間の入力がされていないよ", $user_id);
        }
    } else if ($receive_message == "更新") {
        $message = [
            'type' => 'text',
            'text' => "プロフィール更新ページ\nhttps://fukuiohr2.sakura.ne.jp/2021/Midare/Life_disorder/profile_update_input.php?profile_id=$profile_id",
        ];
        //Line_push("プロフィール更新ページ\nhttps://fukuiohr2.sakura.ne.jp/2021/Midare/Life_disorder/profile_update_input.php?profile_id=$profile_id", $user_id);
    } else if ($receive_message == "テーブル") {
        $message = [
            'type' => 'text',
            'text' => "睡眠記録表\nhttps://fukuiohr2.sakura.ne.jp/2021/Midare/Life_disorder/sleep_table.php?profile_id=$profile_id",
        ];
        //Line_push("睡眠記録表\nhttps://fukuiohr2.sakura.ne.jp/2021/Midare/Life_disorder/sleep_table.php?profile_id=$profile_id", $user_id);
    } else if ($receive_message == "テストリセット") {
        $message = [
            'type' => 'text',
            'text' => "集中力テストリセット\nhttps://fukuiohr2.sakura.ne.jp/2021/Midare/Life_disorder/concentration_reset.php?profile_id=$profile_id",
        ];
        //Line_push("集中力テストリセット\nhttps://fukuiohr2.sakura.ne.jp/2021/Midare/Life_disorder/concentration_reset.php?profile_id=$profile_id", $user_id);
    } else if ($receive_message == "登録") {
        $message = [
            'type' => 'text',
            'text' => "登録（就寝、起床）\nhttps://fukuiohr2.sakura.ne.jp/2021/Midare/Life_disorder/sleeptime_input.php?profile_id=$profile_id",
        ];
        //Line_push("登録（就寝、起床）\nhttps://fukuiohr2.sakura.ne.jp/2021/Midare/Life_disorder/sleeptime_input.php?profile_id=$profile_id", $user_id);
    } else if ($receive_message == "起床登録") {
        $message = [
            'type' => 'text',
            'text' => "登録（起床）\nhttps://fukuiohr2.sakura.ne.jp/2021/Midare/Life_disorder/outtime_input.php?profile_id=$profile_id",
        ];
        //Line_push("登録（起床）\nhttps://fukuiohr2.sakura.ne.jp/2021/Midare/Life_disorder/outtime_input.php?profile_id=$profile_id", $user_id);
    } else if ($event["postback"]["data"] == "richMenuChanged" || $event["postback"]["data"] == "richmenuChanged") {
        //何もしない
    } else { //登録された言葉以外が出た場合にマニュアルを送信

        $message = [
            'type' => 'text',
            'text' => "マニュアル\nおやすみ -> 就寝時間登録\nおはよう -> 起床時間登録\n結果 -> 平均起床時間\n普段起きる時間と平均起床時間との差\nテスト結果->集中力テスト1,2の集計\nテスト1->集中力テスト1のリンク送信\nテスト2->集中力テスト2のリンク送信\nテストリセット->集中力テストの履歴をリセット\nグラフ->グラフリンク送信\n更新->プロフィール更新リンク送信\nテーブル->睡眠記録表リンク送信\n登録->登録（就寝、起床）\n\n登録->起床登録（起床）",
        ];
        
    }
}
// アクセストークン
$accessToken = 'GV2pIonPa4E/JGQP4O4pNCw0yLXtQQhbtsZ9jLM1Yrj9KtCcsRXXN06RJTfqUU/HcuhLN4igCsDdS3HbFVAIxjkpHdfO38CoZjJqkx0Up3uVeFrFdkWUjy8Uz0tIuxtFwug3Xq6FftPWLytTq7EiPgdB04t89/1O/w1cDnyilFU=';

// ヘッダーを設定
$headers = [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $accessToken,
];

// ボディーを設定
$body = json_encode([
    'replyToken' => $replyToken,
    'messages'   => [
        $message,
    ]
]);

// CURLオプションを設定
$options = [
    CURLOPT_URL            => 'https://api.line.me/v2/bot/message/reply',
    CURLOPT_CUSTOMREQUEST  => 'POST',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER     => $headers,
    CURLOPT_POSTFIELDS     => $body,
];

// 返信
$curl = curl_init();
curl_setopt_array($curl, $options);
curl_exec($curl);
curl_close($curl);
?>
<!--
</pre>
<p>プログラムはこれで完成です。</p>
<h3>LINEアカウントを友達登録してテストする</h3>
<p>ではLINEで実際に動くかテストをしてみましょう。</p>
<p>&nbsp;</p>
<p>LINEの友達登録でQR読取画面を開き、管理画面の Messaging API にある 「QR code」を読み取りましょう。</p>
<p>&nbsp;</p>
<p>友達登録をしたら、適当なメッセージを送信してみると・・・・どうでしょうか？</p>
<p>&nbsp;</p>
<p>「こんにちは！」 と返ってきたら成功です。</p>
<h3>メッセージをカスタマイズしてみる</h3>
<p>返信するメッセージは様々なカスタマイズができます。<br>
下記は一例ですので参考程度にどうぞ。-->