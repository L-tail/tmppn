<?php
session_start();

require('./nikkisql.php');

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

    if(isset($_POST['go'])){
        header("Location: nikkityo.php");
        exit();
    }
    //ログアウトボタン
    if(isset($_POST['yes'])){
        setcookie('token', $_COOKIE['token'], time()-1);
        $_SESSION['email'] = array();
        session_destroy();
        header("Location: index.php");
        exit();
    }


?>


<html lang="ja">
    
    <head>
        
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
      
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
       
    <link rel="icon" href="favicon.ico">
    <link rel="stylesheet" href="nikkisuccess.css">
    <title>保存完了 / Tampopoon</title>

    
    </head>
    
    <body>
        
        <div id="successform" class="container-fluid">
            <form method="post" id="linkButton">
                <span style="color: #74645C;">
                <h2>保存しました！</h2>
                </span>
                <p><strong>日記帳を開いて確認できます。<br>日記帳を開きますか？</strong></p>
                <p><img id="girl" src="clover_girl.png"></p>
                <button type="button" id="openNikki" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#staticBackdrop">日記帳を開く</button>
                <p>
                <button id="logout" type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staticBackdrop1">ログアウト</button>
                </p>
            </form>
        </div>
        
        <form method="post">
        <!-- Modal -->
            <!-- 日記帳を開くボタン押下時 -->
                <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">
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
                        <button name="go" type="submit" form="linkButton" class="btn btn-primary">はい</button>
                      </div>
                    </div>
                  </div>
                </div>
            <!-- ログアウトボタン押下時 -->
            <div class="modal fade" id="staticBackdrop1" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel1">
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
                        <button name="yes" type="submit" form="linkButton" class="btn btn-primary">はい</button>
                      </div>
                    </div>
                  </div>
                </div>
            
        </form>
        
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    
    </body>
    
    
</html>