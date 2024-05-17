<?php
function Line_push($msg,$user_id){
$access_token = 'GV2pIonPa4E/JGQP4O4pNCw0yLXtQQhbtsZ9jLM1Yrj9KtCcsRXXN06RJTfqUU/HcuhLN4igCsDdS3HbFVAIxjkpHdfO38CoZjJqkx0Up3uVeFrFdkWUjy8Uz0tIuxtFwug3Xq6FftPWLytTq7EiPgdB04t89/1O/w1cDnyilFU=';

$url = 'https://api.line.me/v2/bot/message/push';

// データの受信(するものないので不要?)
$raw = file_get_contents('php://input');
$receive = json_decode($raw, true);
// イベントデータのパース(不要？)
$event = $receive['events'][0];

// ヘッダーの作成
$headers = array('Content-Type: application/json',
                 'Authorization: Bearer ' . $access_token);

// 送信するメッセージ作成
$message = array('type' => 'text',
                 'text' => $msg);

$body = json_encode(array('to' => $user_id,
                          'messages'   => array($message)));  // 複数送る場合は、array($mesg1,$mesg2) とする。


// 送り出し用
$options = array(CURLOPT_URL            => $url,
                 CURLOPT_CUSTOMREQUEST  => 'POST',
                 CURLOPT_RETURNTRANSFER => true,
                 CURLOPT_HTTPHEADER     => $headers,
                 CURLOPT_POSTFIELDS     => $body);
$curl = curl_init();
curl_setopt_array($curl, $options);
curl_exec($curl);
curl_close($curl);
}
?>