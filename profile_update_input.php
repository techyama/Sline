<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>プロフィール更新画面</title>
    <link rel="stylesheet" href="./css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
    <div>
	<div class="logoimg">	
	<img src="./image/logo2.jpg">
    <h2 class="title">プロフィール更新画面</h2>
	</div>
        <?php 
        require("return_id.php");
        $user_id = ret_user_id((int)$_GET["profile_id"]);
  

        echo "<form method=POST action=./profile_update_output.php>";
        echo "<ul>";
        echo "<h2>更新後のプロフィールを入力してください</h2>";
        echo "<label>名前　　　<input type=text name=name></label><br>";
        echo "<label>通知　   <input type=radio name=notice value=0 checked>あり
                             <input type=radio name=notice value=1>なし</label><br>";
        echo "<label>普段起きる時間";
        echo "<input type=hidden name=user_id value=$user_id>";
        echo "<input type=number name=timeH placeholder=時 min=0 max=23>時";
        echo "<input type=number name=timem placeholder=分 min=0 max=59>分<br></label>";
        echo " <a>0時や0分を入力するときは、00と入力してください。0は無効になります。</a><br></label>";
        echo "<input type=submit value=登録>";
        echo "</ul>";
        echo  "</form>";
        ?>
    </div>
</body>

</html>