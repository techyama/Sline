
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>集中力テスト2</title>
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
    
    <div align="center" class="concentration">
        <div class="logoimg"></div>
        <h1>集中力テスト2</h1>
        <?php
        //user_id,profile_id取得用関数読み込み
        require("return_id.php");
        //あと何回するか出力
        if (isset($_POST["flgcount"])) {
            $count = 5 - $_POST["flgcount"];
            echo "<h2>あと$count 回してください。</h2>";
        } else {//１回目のスタートを押す前
        ?>
            <h2>集中力を測定します。<br>大量の9のボタンの中にひとつだけ8のボタンがあります。<br>8のボタンを探し押してください。</h2>
            <h3>５回測定しその平均で判定します。</h3>
        <?php
        }

        //GETで送られてきたユーザIDを変数に入れる
        if (isset($_GET["profile_id"])) {
            $user_id = ret_user_id((int)$_GET["profile_id"]);
            //var_dump($user_id);
        }

        //POSTされたユーザIDを変数に入れる
        if (isset($_POST["user_id"])) {
            $user_id = $_POST["user_id"];
            //var_dump($user_id);
        }

        ?>
        <form method="POST" action="concentration_input2.php">
            <?php

            //POSTされた合計時間をPOSTする
            if (isset($_POST["clearTime"])) {
                echo "<input type=hidden value=$_POST[clearTime] name=clearTime>";
            }

            //POSTされた現在の回数をPOSTする
            if (isset($_POST["flgcount"])) {
                echo "<input type=hidden value=$_POST[flgcount] name=flgcount>";
            }

            //ユーザIDのPOST
            echo "<input type=hidden value=$user_id name=user_id>";
            ?>
            <input type="submit" name="start" value=スタート>
        </form>
    </div>
</body>
</html>