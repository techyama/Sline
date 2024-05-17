<div class="logoimg"></div>
<div align="center">
    <h1>集中力テスト</h1>
    <?php
    //タイムゾーンを東京にセット
    if (date_default_timezone_get() != "asia/Tokyo") {
        date_default_timezone_set("asia/Tokyo");
    }
    //ポストされたユーザIDを変数に入れる
    if(isset($_POST["user_id"])){
        $user_id = $_POST["user_id"];
    }
    //変数の初期化
    $startTime = 0;//テストを始めた時間
    $outTime = 0;//テストを終了した時間
    $testcount = 5;//テストをする回数

    //答えがポストされたときの処理
    if (isset($_POST['ans'])) {
        if ($_POST["ans"] == 8) {
            $outTime = time();
            $cleartime = $outTime - $_POST["test2"];
            echo "<h3>正解</h3>";
            echo "<h3>".$cleartime. "秒</h3>";

            //クリアタイムの合計
            if (!isset($_POST["clearTime"])) {//１回目のテストが終わったとき
                $_POST["clearTime"] = $cleartime;
            } elseif (isset($_POST["clearTime"])) {//２回目以降のテストが終わったとき
                $_POST["clearTime"] = $_POST["clearTime"] + $cleartime;
            }

            //指定回数実行するためのカウント
            if (!isset($_POST["flgcount"])) {//１回目のテストが終わったとき
                $_POST["flgcount"] = 1;
            } elseif (isset($_POST["flgcount"])) {//2回目以降
                $_POST["flgcount"] = $_POST["flgcount"] + 1;
            }
            //テストを始めた時間のアンセット
            unset($_POST["test2"]);
            $average_time = 0;

            //指定回数内のときの処理
            if ($_POST["flgcount"] < $testcount) {//もう一回スタートするためのボタンと必要なデータをポスト
                echo "<form method=POST action=concentration_start.php>";
                echo "<input type=hidden value=$user_id name=user_id>";
                echo "<input type=hidden value=$_POST[flgcount] name=flgcount>";
                echo "<input type=hidden value=$_POST[clearTime] name=clearTime>";
                echo "<input type=submit value=スタートへ>";
                echo "</form>";

            } else {//終了時の処理(指定回数の平均時間の出力)
                $test_time = (float)$_POST["clearTime"] / $testcount;
                $test_time = round($test_time, 3);
                echo "<h3><br>平均　$test_time 秒";
                $time_now = time();
                $time_now = date("Y-m-d H:i:s",$time_now);
                //データベースに保存する処理
                try{
                    require("db_connect.php");
                    $sql1 = "SELECT * FROM concentration WHERE user_id = :user_id AND test_time2 IS NOT NULL"; 
                    $sql = "INSERT INTO concentration (user_id,date,test_time2) VALUE (:user_id,:date,:test_time2)";
                    $stm1 = $pdo->prepare($sql1);
                    $stm = $pdo -> prepare($sql);
                    $stm1 -> bindValue(":user_id",$user_id,PDO::PARAM_STR);
                    $stm -> bindValue(":user_id",$user_id,PDO::PARAM_STR);
                    $stm -> bindValue(":date",$time_now,PDO::PARAM_STR);
                    $stm -> bindValue(":test_time2",$test_time,PDO::PARAM_STR);
                    if($stm1 -> execute()){
                        $result = $stm1->fetchAll(PDO::FETCH_ASSOC);
                        foreach($result as $value){
                            $test_time_sum += $value["test_time2"];
                        }
                        $test_time_count = count($result);
                        $test_time_ave = (float)($test_time_sum/$test_time_count);

                        //前回までの結果と合わせて出力する処理
                        if($test_time < $test_time_ave){
                            echo "前回までのテスト結果の平均".round($test_time_ave,3)."秒<br>集中できてるね</h3>";
                        }else if($test_time_count == 0){
                            echo "はじめて集中力テストをしたよ。</h3>";
                        }else{
                            echo "前回までのテスト結果の平均".round($test_time_ave,3)."秒<br>少し集中できていないよ</h3>";
                        }
                    }

                    //データベースに保存が成功したときの処理
                    if($stm -> execute()){
                        //echo "成功だー";
                    }
                }catch(Exception $e){//何かエラーがあったときの処理
                    echo  '<span class="error">エラーがありました。</span><br>';
                    echo $e->getMessage();
                }

                //ポストしていたものをアンセット
                unset($_POST["user_id"]);
                unset($_POST["test_time"]);
                unset($_POST["flgcount"]);
                unset($_POST["clearTime"]);
            }
        }
    }
    //スタートを押したときの処理
    if (isset($_POST["start"])) {
        if (!isset($_POST["test2"])) {
            $_POST["test2"] = time();
        }
    }


    ?>

    <?php
    //ボタンオブジェクトを作るクラスを作成
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
            $this->button = "<button style=width:30px;height:35px;font-size:17px;color:red;padding:0px; name=ans value=$this->value>$this->value</button>";
            echo $this->button;
        }
    }

    //9のボタンを作るクラスを作成
    class Nine extends Concentration
    {
        public function __construct()
        {
            parent::__construct(9);
        }
    }

    //8のボタンを作るクラスの作成
    class Eight extends Concentration
    {
        public function __construct()
        {
            parent::__construct(8);
        }
    }

    ?>
    <!DOCTYPE html>
    <html lang="ja">

    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
        <link rel="stylesheet" href="./css/style.css">
    </head>

    <body>
        <div align="center">
            <?php
            //スタートが押されて開始時間がtest2に入っているときの処理
            if (isset($_POST["test2"])) {
                echo "<form method=POST action=concentration_input2.php>";
                if (isset($_POST["start"])) {//スタートが押されたときに答えの設定
                    $target = mt_rand(1, 60);
                } elseif (isset($_POST["target"])) {//ポストされた答えの変数への代入
                    $target = $_POST["target"];
                }

                ///ボタンの設置
                echo "<div style=width:300;>";
                for ($i = 1; $i <= 60; $i += 1) {
                    if (!($i == $target)) {
                        $button = new Nine();
                        $button->Echo();
                    } else {
                        $button1 = new Eight();
                        $button1->Echo();
                    }
                }
                echo "</div>";

                //回数をカウントするために今何回目かポスト
                if(isset($_POST["flgcount"])){
                    echo "<input type=hidden value=$_POST[flgcount] name=flgcount>";
                }

                //今現在のクリア時間の合計をポスト
                if(isset($_POST["clearTime"])){
                    echo "<input type=hidden value=$_POST[clearTime] name=clearTime>";
                }
                echo "<input type=hidden value=$user_id name=user_id>";
                echo "<input type=hidden value=$_POST[test2] name=test2>";
                echo "<input type=hidden value=$target name=target>";

                echo "</form>";
            }
            ?>

        </div>
    </body>

    </html>