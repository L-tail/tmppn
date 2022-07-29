<?php
session_start();

require('./nikkisql.php');

if(mysqli_connect_error()){
    die("データベースとの接続に失敗しました");
}

//cookieが存在する場合
if(isset($_COOKIE['token'])){
    $query = "SELECT email FROM users WHERE token = '".mysqli_real_escape_string($link,$_COOKIE['token'])."'";
    $result = mysqli_query($link,$query);
    $row = mysqli_fetch_array($result);
    $_SESSION['email'] = $row['email'];
    header("Location: nikkirecord.php");
    exit();
}

    if(array_key_exists('email',$_POST) OR array_key_exists('password',$_POST)){
        if($_POST['email'] == ''){
        }elseif($_POST['password'] == ''){
        }else{
            
            $setPassword = $_POST['password'];
            $query = "SELECT `password` FROM `users` WHERE email ='".mysqli_real_escape_string($link,$_POST['email'])."'";
            $result = mysqli_query($link,$query);
            $row = mysqli_fetch_array($result);
            $setDBHash = $row['password'];
            
            //認証
            if(password_verify($setPassword, $setDBHash)){
                $query = "SELECT `id` FROM `users` WHERE email = '".mysqli_real_escape_string($link,$_POST['email'])."' AND password = '".mysqli_real_escape_string($link,$setDBHash)."'";
                $result = mysqli_query($link,$query);
                //トークンのハッシュ化
                if(mysqli_num_rows($result) > 0){
                    $TOKEN_LENGTH = 32;
                    $bytes = random_bytes($TOKEN_LENGTH);
                    $token = bin2hex($bytes);
                        //Cookieの設定
                        if($_POST['check'] == 'on') {
                            $query = "UPDATE `users` SET `token` = '".mysqli_real_escape_string($link,$token)."' WHERE email ='".mysqli_real_escape_string($link,$_POST['email'])."'";
                            $result = mysqli_query($link,$query);
                            setcookie('token', $token, time()+60*60*24*14, "", "", true, true);
                        }else{
                        }
                        
                    
                $_SESSION['email']=$_POST['email'];
                header("Location: nikkirecord.php");
                exit();
                }
            }else{
                echo "ログイン情報が正しくありません";
            }
        }
    }


?>


<html lang="ja">
    <head>
        
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        
        <link rel="icon" href="favicon.ico">
        <link rel="stylesheet" href="nikkilogin.css">
        <title>Tampopoon / ログイン</title>
    
    </head>
    
    <body>
        <!-- 背景動画 -->
        <div id="v-area">
        <video id="video" src="dandelion_-_80155.mp4" poster="dandelion-g49ef4391c_1920.jpg" autoplay muted loop></video>
        </div>
        
        <div id="mainform" class="container-fluid">
            <h1 id="Tampopoon">Tampopoon</h1>
            <p><strong>あなたの日記にログインしましょう。</strong></p>
            
            <form method="post" id="linkButton" class="needs-validation" novalidate>
                <div class="form-floating mb-3">
                    <input type="email" name="email" class="form-control" id="InputEmail" placeholder="Email" required>
                    <label for="floatingTnputInvalid">Email</label>
                    <div class="invalid-feedback">
                        Emailアドレスを入力してください
                    </div>
                </div>
                <div class="form-floating mb-3">
                    <input type="password" name="password" class="form-control" id="InputPassword" placeholder="Password" required>
                    <label for="floatingInputInvalid">Password</label>
                    <div class="invalid-feedback">
                        Passwordを入力してください
                    </div>
                </div>
                <div class="mb-3 form-check">
                <label class="form-check-label" for="exampleCheck1">
                    <input type="checkbox" class="form-check-input" id="Check" name="check">
                    ログイン状態を保持する
                </label>
                </div>
                <p id="space">
                <button type="button" id="login" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staticBackdrop">ログイン</button>
                </p>
            </form>
        </div>
        
        <!-- Modal -->
          <form method="post">
              <!-- ログイン押下時モーダル -->
                    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                      <div class="modal-dialog">
                        <div class="modal-content">
                          <div class="modal-header">
                              <span style="color: black;">
                            <h5 class="modal-title" id="staticBackdropLabel">ログインの確認</h5>
                                </span>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <div class="modal-body">
                                <span style="color: black;">
                            ログインしますか？
                                </span>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">いいえ</button>
                              <!--formとsubmitしたいボタンを同じidで紐づける-->
                            <button id="yes" type="submit" name="yes" form="linkButton" class="btn btn-primary">はい</button>
                          </div>
                        </div>
                      </div>
                    </div>
          </form>
        
        <!-- Option 1: Bootstrap Bundle with Popper -->
         <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    </body>

</html>