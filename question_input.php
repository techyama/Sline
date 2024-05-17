<?php
use function PHPSTORM_META\type;
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>アンケート</title>
</head>
<body>
<div>
    <?php
    require("./db_connect.php");
    require("return_id.php");
    //if(!isset($_SESSION["question"])){
    $filename="question.csv";
    if (isset($_GET["profile_id"])) {
        $user_id = ret_user_id((int)$_GET["profile_id"]);
    }
    if (isset($_POST["user_id"])) {
        $user_id = $_POST["user_id"];
    }
    try{
        $fileObj=new SplFileObject($filename,"rb");
    }
    catch(Exception $e){
        echo "<span class='error'>エラーがありました。</span>";
        echo $e->getMessage();
        exit();
    }

    $fileObj->setFlags(
        SplFileObject::READ_CSV
        |SplFileObject::READ_AHEAD
        |SplFileObject::SKIP_EMPTY
        |SplFileObject::DROP_NEW_LINE
    )
    ?>
    <form method="POST" action="question_output.php">
        <?php
        $count=0;
        foreach($fileObj as $row){
            list($no,$value)=$row;
            echo "<input type=checkbox name='ans$no' value=True>",$no,":",$value,"<br>";
            $count+=1;
        }
        echo "<input type=hidden name=count value=$count>";
        ?>
        <?php echo "<input type=hidden name=user_id value=$user_id>"; ?>
        <input type="submit" value="送信">
    </form>
</div>
</body>
</html>