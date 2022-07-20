<?php
session_start();
session_regenerate_id(true);

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

?>

<!--ホームへ戻る-->
<strong><a href="index.php" id="homelink">Tampopoon</a></strong>

<?php
    if($_SESSION['email']){
?>
    <span style="color: rgba(246,241,214)">
        <?php
        //ユーザー名を表示
        $query = "SELECT name FROM users WHERE email = '{$_SESSION['email']}'";
        $result = mysqli_query($link,$query);
        $name_row = mysqli_fetch_array($result);
        $name = $name_row['name'];
        echo "$name でログイン中";
        ?>
    </span>
<?php 
    }else{
        header("Location: index.php");
    }

    //日記帳を開くボタン
    if(isset($_POST['yesNikki'])){
        header("Location: nikkityo.php");
        exit();
    }

    //記録フォームに値が入っているかチェック
    if(array_key_exists('textarea',$_POST)){
        if($_POST['textarea'] == ''){
            if(array_key_exists('title',$_POST)){
                if($_POST['title'] == ''){
                    ?>
                    <span style="color: rgba(246,241,214);">
                    <?php
                    echo "　平和な1日でしたね";
                    ?>
                    </span>
<?php
                }else{
                    ?>
                    <span style="color: rgba(246,241,214);">
                    <?php
                    echo "　タイトルがあれば物語もあります。あなたの物語を記録しましょう。";
                    ?>
                    </span>
<?php
                }
            }
        }else{
            //年月日取得、値の挿入
            $date= new DateTime();
            $query = "INSERT INTO `nikki_data` (`user_id`,`nikki_date`,`nikki_title`,`nikki_memory`) VALUES ('".$user_id."','".date_format($date,"Y/m/d")."','".mysqli_real_escape_string($link,$_POST['title'])."','".mysqli_real_escape_string($link,$_POST['textarea'])."')";
                if(mysqli_query($link,$query)){
                    header("Location: nikkisuccess.php");
            }
        }
    }

    //ログアウトボタン
    if(isset($_POST['yesOut'])){
        setcookie('token', $_COOKIE['token'], time()-1);
        $_SESSION['email'] = array();
        session_destroy();
        header("Location: index.php");
        exit();
    }

?>


<!DOCTYPE>

<html lang="ja">
    
    <head>
        
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
      
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        
    <link rel="icon" href="favicon.ico">
    <link rel="stylesheet" href="nikkirecord.css">
    <title>日記に記入 / Tampopoon</title>
    
    </head>
    
    
    <body>
        
        <div id="logform" class="container-fluid">
            <form method="post" id="linkButton">
                <!--補足文-->
                <span style="color: #74645C;">
                <h2>日記を記入中。。。</h2>
                </span>
                <p><strong>あなただけが残した記録を見ることが出来ます</strong></p>
                
                <!--タイトル入力フォーム-->
                <div class="mb-3">
                <input type="title" name="title" class="form-control" id="InputTitle" placeholder="タイトル（20字まで）" maxlength="20">
                </div>
                
                <!--本文入力フォーム-->
                <div class="form-group">
                <textarea id="textarea1" class="form-control" name="textarea" placeholder="記憶を入力"></textarea>
                </div>
                
                <!--各種ボタン-->
                <button id="saveButton" type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#staticBackdrop">保存</button>
                <p>
                <button id="nikkityoButton" type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#staticBackdrop-nikki">日記帳を開く</button>
                </p>
                <p>
                <button id="logout" type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staticBackdrop-out">ログアウト</button>
                </p>
            </form>
        </div>
        
        <form method="post">
        <!-- Modal -->
            <!-- 保存ボタン押下時 -->
                <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">
                            <span style="color: black;">日記を保存</span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                            <span style="color: black;">
                        保存してよろしいですか？
                            </span>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">いいえ</button>
                        <button type="submit" form="linkButton" class="btn btn-primary">はい</button>
                      </div>
                    </div>
                  </div>
                </div>
            <!-- 日記帳を開くボタン押下時 -->
                <div class="modal fade" id="staticBackdrop-nikki" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel1">
                            <span style="color: black;">日記帳を開く</span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                            <span style="color: black;">
                        日記帳を開きますか？
                            </span>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">いいえ</button>
                        <button name="yesNikki" type="submit" form="linkButton" class="btn btn-primary">はい</button>
                      </div>
                    </div>
                  </div>
                </div>
            <!-- ログアウトボタン押下時 -->
                <div class="modal fade" id="staticBackdrop-out" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                      <div class="modal-dialog">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="staticBackdropLabel2">
                                <span style="color: black;">ログアウト</span>
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <div class="modal-body">
                                <span style="color: black;">
                            ログアウトしますか？
                                </span>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">いいえ</button>
                            <button name="yesOut" type="submit" form="linkButton" class="btn btn-primary">はい</button>
                          </div>
                        </div>
                      </div>
                </div>
            
        </form>
        
    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
        
    </body>


</html>