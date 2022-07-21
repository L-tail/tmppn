<?php
session_start();

require('./nikkisql.php');

//Emailフォームに入力したアドレスと一致するemailカラム内にあるアドレスの行が持つidを選択する
$query = "SELECT id FROM users WHERE email='{$_SESSION['email']}'";
//$linkで指定したDBから$queryに選択して入ったデータを問い合わせる
$result = mysqli_query($link,$query);
//配列として問い合わせたデータを取り出す
$row = mysqli_fetch_array($result);
$user_id = $row['id'];

//Cookieが存在する場合
if(isset($_COOKIE['token'])){
    $query = "SELECT token FROM users WHERE token= '".mysqli_real_escape_string($link,$_COOKIE['token'])."'";
    $result = mysqli_query($link,$query);
    $row = mysqli_fetch_array($result);
    //DBに保存されているCookieとブラウザに保存されているCookieが一致しない場合
    if($_COOKIE['token'] !== $row['token']){
        header("Location: index.php");
        exit();
    }
}

    if($_SESSION['email']){
    ?>

<!--ホームへ戻る-->
<strong><a href="index.php" id="homelink">Tampopoon</a></strong>

        <span style="color: rgba(246,241,214)">
            <?php
            $query = "SELECT name FROM users WHERE email = '{$_SESSION['email']}'";
            $result = mysqli_query($link,$query);
            $name_row = mysqli_fetch_array($result);
            $name = $name_row['name'];
            echo "$name でログイン中";
            ?>
        </span>
<?php
    }else{
        header("Location: nikkirecord.php");
    }
?>

<html lang="ja">

    <head>
        
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
      
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        
    <link rel="icon" href="favicon.ico">
    <link rel="stylesheet" href="nikkityo.css">
    <title>日記帳 / Tampopoon</title>
        
    </head>
    
    <body>
        
        <div class="container-fluid">
            
            <form class="searchForm" method="post" id="linkButton">
                <div class="row">
                <input type="date" name="date1" class="form-control day col-md-4" id="InputDate"><br>から
                <input type="date" name="date2" class="form-control day col-md-4" id="InputDate1">
                <input type="text" name="word" class="form-control col-md-4" id="InputText" placeholder="思い出したい記憶を入力">
                <button type="submit" id="search" name="search" class="btn btn-success col-md-4">思い出す</button>
                </div>
            </form>
<?php
    //フォームに値が入力されたかチェックして結果を出力
    if(isset($_POST['search'])){
        if(!isset($_POST['date1']) && !isset($_POST['date2']) && !isset($_POST['word'])){
            //処理中止
            return;
        }
        if(strlen($_POST['date1']) == 0 && strlen($_POST['date2']) == 0 && strlen($_POST['word']) == 0){
?>
            <form class="searchForm" method="post">
            <?php echo "条件を指定して下さい"; ?>
            </form>
<?php
            return;
        }else{
        
        $query = "SELECT ";
        $query .= "`nikki_date`,`nikki_title`,`nikki_memory` ";
        $query .= "FROM `nikki_data` ";
        
        $case = "";
        //日付検索
        if(strlen($_POST['date1']) > 0 && strlen($_POST['date2']) > 0){
            $day1 = $_POST['date1'];
            $day2 = $_POST['date2'];
            $case .= " nikki_date BETWEEN '".mysqli_real_escape_string($link,$day1)."' AND '".mysqli_real_escape_string($link,$day2)."' AND";
        }elseif(strlen($_POST['date1']) > 0 && strlen($_POST['date2']) == 0){
            $day1 = $_POST['date1'];
            $case .= " nikki_date = '".mysqli_real_escape_string($link,$day1)."' AND";
        }elseif(strlen($_POST['date1']) == 0 && strlen($_POST['date2']) > 0){
            $day2 = $_POST['date2'];
            $case .= " nikki_date = '".mysqli_real_escape_string($link,$day2)."' AND";
        }
            
        //文字検索
        if(strlen($_POST['word']) > 0){
            $case .= " nikki_memory LIKE '%".mysqli_real_escape_string($link,$_POST['word'])."%' AND";
        }
        
        //where文追加
        $query .= "WHERE ";
        $query .= "user_id = ".$user_id." ";
        //caseから条件ごとにwhere文の続きを追加
        if(strlen($case) > 0){
            $query .= "AND";
            //日付・文字検索の条件を連結
            $query .= $case;
            //sql文末に余る" AND"の4文字を削除
            $query = mb_substr($query, 0, mb_strlen($query) - 4);
        }
        $result = mysqli_query($link,$query);
?>
            <form class="searchForm" method="post" id="form2">
                <table id="nikki_table">
                <thead>
                    <tr>
                        <th>日付</th>
                        <th>タイトル</th>
                        <th>記憶</th>
                    </tr>
                </thead>
                <tbody id="table_b">
<?php
                while($row = mysqli_fetch_assoc($result)){
                if((isset($row['nikki_date']) == true) && (isset($row['nikki_title']) == true) && (isset($row['nikki_memory']) == true)){
?>
                <tr>
                    <td><?php echo $row['nikki_date'] ?></td>
                    <td><?php echo $row['nikki_title'] ?></td>
                    <td><?php echo $row['nikki_memory'] ?></td>
                </tr>
<?php
                }
            }
?>
                </tbody>
                </table>
            </form>
        </div>
<?php
        }
        }
?>
    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
        
    </body>

</html>