<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/style.css">
    <script src="css_browser_selector.js" type="text/javascript"></script>
    <title>集中力テスト１</title>
</head>

<body>
    <div align="center" class="concentration">
        <div class="logoimg"></div>
        <h1>集中力テスト１</h1>

        <?php
        //タイムゾーンを東京にセット
        if (date_default_timezone_get() != "asia/Tokyo") {
            date_default_timezone_set("asia/Tokyo");
        }
        require("./db_connect.php");//DBに接続するためのプログラムを読み込み
        require("return_id.php");//user_id,profile_id取得用関数読み込み
        $numList = [];//表示する数字列を入れておく配列
        $list = "";//数字列を文字列に変えるときの変数
        $target = 0;//消す数字を入れる変数
        $startTime = 0;//始めた時間
        $flgcount = 0;//今何回目か
        $testcount = 5;//合計何回するか
        $test_time = 0;//今回どんだけかかったか
        $test_time_sum = 0;//今合計どんだけ時間かかったか

        //数字列の生成
        function createnum($target)
        {
            global $numList;
            for ($j = 1; $j <= 40; $j += 1) {
                if ($j != $target) {
                    $numList[] = $j;
                }
            }
        }

        //GETで送られたユーザIDを変数に入れる
        if(isset($_GET["profile_id"])){
            $user_id = ret_user_id($_GET["profile_id"]);
        }

        //POSTで送られたユーザIDを変数に入れる
        if(isset($_POST["user_id"])){
            $user_id = $_POST["user_id"];
        }

        //POSTされた開始時間を変数に入れる
        if (isset($_POST["starttime"])) {
            $startTime = $_POST["starttime"];
        }

        //POSTされた消した数字を変数に入れる
        if (isset($_POST["target"])) {
            $target = $_POST["target"];
        }

        //POSTされた文字列化した数字列を変数に入れる
        if (isset($_POST["list"])) {
            $list = $_POST["list"];
        }

        //POSTされた今何回目かを変数に入れる
        if (isset($_POST["flg"])) {
            $flgcount = (int)$_POST["flg"];
        }

        //スタートが押された場合一つ数字を消して１〜４０までの数字列の生成
        if (isset($_POST["start"])) {
            if (!isset($_POST["list"])) {
                $target = mt_rand(5, 38);
                createnum($target);
                $list = implode(",", $numList);
                //var_dump($numList);
                //var_dump("list :",$list);
                $startTime = time();
                $flgcount += 1;
            }
        }

        //消した数字と押されたボタンが一致していた場合の処理
        if (isset($_POST["target"]) && $_POST["target"] == $_POST["ans"]) {
            $list = "";//数字列の初期化
            $endTime = time();//終了時間の決定
            $clearTime = $endTime - $startTime;//クリア時間の計算
            echo $clearTime, "秒";//クリア時間の表示
            echo "<h2>正解</h2>";

            //テスト時間の合計
            if (!isset($_POST["test_time"])) {//１回目のテスト終了
                $_POST["test_time"] = $clearTime;
            } else {//2回目以降のテストの終了
                $_POST["test_time"] = (int)$_POST["test_time"] + $clearTime;
            }

            //答え、数字列、開始時間などアンセット
            unset($_POST["target"]);
            unset($_POST["ans"]);
            unset($_POST["list"]);
            unset($_POST["starttime"]);
            unset($_POST["start"]);
            $startTime = null;
            $target = null;
            $average_time = 0;

            //指定回数終了時の処理
            if ($flgcount == $testcount) {
                $test_time = (float)($_POST["test_time"] / $testcount);
                $test_time = round($test_time, 3);
                unset($_POST["test_time"]);
                echo "今回のテスト結果".$test_time."秒<br>";
                $time_now = time();
                $time_now = date("Y-m-d H:i:s",$time_now);
                //データベースに保存
                try{
                    $sql1 = "SELECT * FROM concentration WHERE user_id = :user_id AND test_time1 IS NOT NULL"; 
                    $sql = "INSERT INTO concentration (user_id,date,test_time1) VALUE (:user_id,:date,:test_time1)";
                    $stm1 = $pdo->prepare($sql1);
                    $stm = $pdo -> prepare($sql);
                    $stm1 -> bindValue(":user_id",$user_id,PDO::PARAM_STR);
                    $stm -> bindValue(":user_id",$user_id,PDO::PARAM_STR);
                    $stm -> bindValue(":date",$time_now,PDO::PARAM_STR);
                    $stm -> bindValue(":test_time1",$test_time,PDO::PARAM_STR);
                    if($stm1 -> execute()){
                        $result = $stm1->fetchAll(PDO::FETCH_ASSOC);
                        foreach($result as $value){
                            $test_time_sum += $value["test_time1"];
                        }

                        //前回までの結果と比べての出力
                        $test_time_count = count($result);
                        $test_time_ave = (float)($test_time_sum/$test_time_count);
                        if($test_time < $test_time_ave){
                            echo "前回までのテスト結果の平均".round($test_time_ave,3)."秒<br>集中できてるね";
                        }else if($test_time_count == 0){
                            echo "はじめて集中力テストをしたよ。";
                        }else{
                            echo "前回までのテスト結果の平均".round($test_time_ave,3)."秒<br>少し集中できていないよ";
                        }
                    }
                    if($stm -> execute()){
                        //echo "成功だー";
                        unset($_POST);
                    }
                }catch(Exception $e){
                    echo  '<span class="error">エラーがありました。</span><br>';
                    echo $e->getMessage();
                }
            }
        }

        //１回目のスタートを押す前
        if ($flgcount == 0) {
            echo "<h2>集中力を測定します。<br>表示される1~40の数字の中で存在しない数字を<br>画面下のボタンから選択し、押してください。</h2>";
            echo "<h3>５回測定しその平均で判定します。</h3>";
        } else {
            $count = 5 - $flgcount;
            echo "<h2>あと$count 回</h2>";
        }
        ?>
        <div class="number" style="word-break: normal;">
            <?php

            //数字列の表示
            if(!$list == ""){
            $list_output = explode(",",$list);
            foreach($list_output as $value){
                echo $value."、";
            }
        }
            ?>
        </div>
        <?php

        //答えのボタンを押して答えと違ったときの処理
        if (!isset($_POST["start"]) && $flgcount < $testcount) {


        ?>

            <form method="POST" action="concentration_input.php">
                <input type="submit" value="スタート" name="start">
                <?php
                if (isset($flgcount)) {
                    echo "<input type=hidden value=$flgcount name=flg>";
                }
                if(isset($_POST["test_time"])){
                    echo "<input type=hidden value=$_POST[test_time] name=test_time>";
                }
                    echo "<input type=hidden value=$user_id name=user_id>";
                ?>
            </form>
        <?php


        }
        ?>
    </div>
    <?php

    //ボタンクラスの設定
    class Concentration
    {
        public $value;
        public $button;

        public function __construct(int $values)
        {
            $this->value = $values;
        }

        public function Echo()
        {
            $this->button = "<button name=ans value=$this->value>$this->value</button>";
            echo $this->button;
        }
    }
    ?>
    <div align="center" class="concentration">
        <hr>
        <?php
        
        //答えるときに使うボタンの設置
        if (isset($_POST["start"])) {
            echo "<form method=POST action=concentration_input.php>";
            for ($i = 1; $i <= 40; $i += 1) {
                $button = new Concentration($i);
                $button->Echo();
                if ($i % 10 == 0) {
                    echo "<br>";
                }
            }
        }

        //当てる答えのPOST
        echo "<input type=hidden value=$target name=target>";

        //数字列のPOST
        echo "<input type=hidden value=$list name=list>";

        //開始時間のPOST
        echo "<input type=hidden value=$startTime name=starttime>";

        //今何回目かPOST
        if (isset($flgcount)) {
            echo "<input type=hidden value=$flgcount name=flg>";
        }

        //スタートボタンを押したかPOST
        if (isset($_POST["start"])) {
            echo "<input type=hidden value=$_POST[start] name=start>";
        }

        //合計時間のPOST
        if(isset($_POST["test_time"])){
            echo "<input type=hidden value=$_POST[test_time] name=test_time>";
        }

        //ユーザIDのPOST
        echo "<input type=hidden value=$user_id name=user_id>";
        echo "</form>";

        
        ?>
    </div>

</body>

</html>