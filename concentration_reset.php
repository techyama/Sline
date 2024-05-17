<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>集中力テストリセット</title>
</head>
<body>
<?php
require("return_id.php");

if (isset($_POST["user_id"])) {
    $user_id = $_POST["user_id"];
}
if(isset($_GET["profile_id"])){
    $user_id = ret_user_id($_GET["profile_id"]);
}
    try{
        require("db_connect.php");
        $stm = $pdo->prepare("DELETE FROM concentration WHERE user_id = :user_id");
        $stm -> bindValue(':user_id', $user_id, PDO::PARAM_STR);
        if($stm->execute()){
            echo "集中力テストの履歴を削除しました。";
        }
    }catch(Exception $e){//何かエラーがあったときの処理
        echo  '<span class="error">エラーがありました。</span><br>';
        echo $e->getMessage();
    }

    ?>
</body>
</html>