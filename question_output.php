<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>アンケート</title>
</head>
<body>
    

<?php
require("db_connect.php");
$id1 = $_POST["user_id"];
$count = (int)$_POST["count"];
$list = [];
$list[]=1;
$check = 0;
for ($i = 1; $i <= $count; $i += 1) {
    if (isset($_POST["ans$i"])) {
        $list[] = 1;
        $check += 1;
    }
    else {
        $list[] = 0;
    }
}
if (date_default_timezone_get() != "asia/tokyo") {
    date_default_timezone_set("asia/tokyo");
}
$sql6 = "SELECT * FROM sleep WHERE user_id = :user_id ORDER BY in_time DESC LIMIT 0,1";
		$stm6 = $pdo->prepare($sql6);
		$stm6->bindValue(":user_id", $id1, PDO::PARAM_STR);
		if ($stm6->execute()) {
			$result = $stm6->fetchAll(PDO::FETCH_ASSOC);
			$sleep_id = $result[0]['sleep_id'];
		}
$answer = implode($list);
try{
    $sql="UPDATE sleep SET question=$answer WHERE sleep_id=:sleep_id";
    $stm=$pdo->prepare($sql);
    $stm->bindValue(":sleep_id",$sleep_id,PDO::PARAM_STR);
    if($stm->execute()){
        echo "アンケートのご回答ありがとうございます。";
        
        }
    }
catch(Exception $e){
    echo  '<span class="error">エラーがありました。</span><br>';
    echo $e->getMessage();
    exit();
}

?>
</body>
</html>