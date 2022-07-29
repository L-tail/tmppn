<?php
session_start();

require('./nikkisql.php');
    
if(mysqli_connect_error()){
    die("データベースとの接続に失敗しました");
}

//cookieが存在する場合
if(isset($_COOKIE['token'])){
    $query = "SELECT token FROM users WHERE token= '".mysqli_real_escape_string($link,$_COOKIE['token'])."'";
    $result = mysqli_query($link,$query);
    $row = mysqli_fetch_array($result);
    //DBに保存されているCookieとブラウザに保存されているCookieが一致しない場合
    if($_COOKIE['token'] !== $row['token']){
        setcookie('token', $_COOKIE['token'], time()-1);
        $_SESSION['email'] = array();
        session_destroy();
    }else{
    $query = "SELECT email FROM users WHERE token = '".mysqli_real_escape_string($link,$_COOKIE['token'])."'";
    $result = mysqli_query($link,$query);
    $row = mysqli_fetch_array($result);
    $_SESSION['email'] = $row['email'];
    header("Location: nikkirecord.php");
    exit();
    }
}
//モーダルで「はい」を押下時
if(isset($_POST['yes'])){

    if(array_key_exists('email',$_POST) OR array_key_exists('password',$_POST)){

        if($_POST['name'] == ''){
?>
        <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
            <symbol id="exclamation-triangle-fill" fill="currentColor" viewBox="0 0 16 16">
            <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
          </symbol>
        </svg>
        <div class="alert alert-danger d-flex align-items-center container container1" role="alert">
          <svg class="bi flex-shrink-0 me-2" width="24" height="24"><use xlink:href="#exclamation-triangle-fill"/></svg>
          <div>
<?php
            echo "名前を入力してください";
?>
          </div>
        </div>
<?php
        }elseif($_POST['email'] == ''){
?>
        <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
            <symbol id="exclamation-triangle-fill" fill="currentColor" viewBox="0 0 16 16">
            <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
          </symbol>
        </svg>
        <div class="alert alert-danger d-flex align-items-center container container1" role="alert">
          <svg class="bi flex-shrink-0 me-2" width="24" height="24"><use xlink:href="#exclamation-triangle-fill"/></svg>
          <div>
<?php
            echo "Eメールアドレスを入力してください";
?>
          </div>
        </div>
<?php
        }elseif($_POST['password'] == ''){
?>
        <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
            <symbol id="exclamation-triangle-fill" fill="currentColor" viewBox="0 0 16 16">
            <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
          </symbol>
        </svg>
        <div class="alert alert-danger d-flex align-items-center container container1" role="alert">
          <svg class="bi flex-shrink-0 me-2" width="20" height="20"><use xlink:href="#exclamation-triangle-fill"/></svg>
          <div>
<?php
            echo "パスワードを入力してください";
?>
            </div>
        </div>
<?php
        }else{
            $query = "SELECT `id` FROM `users` WHERE email = '".mysqli_real_escape_string($link,$_POST['email'])."'";
            $result = mysqli_query($link,$query);
            if(mysqli_num_rows($result) > 0){
?>
                <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
                <symbol id="exclamation-triangle-fill" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                </symbol>
                </svg>
            <div class="alert alert-warning d-flex align-items-center container container1" role="alert">
                  <svg class="bi flex-shrink-0 me-2" width="24" height="24"><use xlink:href="#exclamation-triangle-fill"/></svg>
                <div>
<?php
                echo "既にそのメールアドレスは使用されています";
?>
                </div>
            </div>
<?php
            }else{
                //未使用の場合の処理
                $setPassword = $_POST['password'];
                //パスワードのハッシュ化
                $setHash = password_hash($setPassword, PASSWORD_DEFAULT);
                $query = "INSERT INTO `users` (`email`,`password`,`name`) VALUES ('".mysqli_real_escape_string($link,$_POST['email'])."','".mysqli_real_escape_string($link,$setHash)."','".mysqli_real_escape_string($link,$_POST['name'])."')";
                
                //トークンのハッシュ化
                if(mysqli_query($link,$query)){
                    $TOKEN_LENGTH = 32;
                    $bytes = random_bytes($TOKEN_LENGTH);
                    $token = bin2hex($bytes);
                //Cookieの設定
                if($_POST['check'] == 'on'){
                    $query = "UPDATE `users` SET `token` = '".mysqli_real_escape_string($link,$token)."' WHERE email ='".mysqli_real_escape_string($link,$_POST['email'])."'";
                    $result = mysqli_query($link,$query);
                    setcookie('token', $token, time()+60*60*24*14, "", "", true, true);
                }
                    
                    $_SESSION['email']=$_POST['email'];
                    header("Location: nikkirecord.php");
                    exit();
?>
<?php
                }else{
?>
            <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
                <symbol id="exclamation-triangle-fill" fill="currentColor" viewBox="0 0 16 16">
                <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
              </symbol>
            </svg>
                <div class="alert alert-danger d-flex align-items-center container" role="alert">
                    <svg class="bi flex-shrink-0 me-2" width="24" height="24"><use xlink:href="#exclamation-triangle-fill"/></svg>
                    <div>
<?php
                    echo "登録に失敗しました";
?>
                    </div>
                </div>
<?php
                }
            }
        }
    }
}
    //ログインページへ遷移
    if(isset($_POST['login'])){
        header("Location: nikkilogin.php");
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
      <link rel="stylesheet" href="index.css">
      <title>Tampopoon</title>
      
  </head>
    
  <body>
      <!-- 背景動画 -->
      <div id="v-area">
        <video id="video" src="dandelion_-_80155.mp4" poster="dandelion-g49ef4391c_1920.jpg" autoplay muted loop></video>
      </div>
      
      <div id="mainform" class="container-fluid">
    
        <h1 id="Tampopoon">Tampopoon</h1>
        <p><strong>記憶は徐々に飛んでいきます、<br class="sp">タンポポの種のように。<br>あなたの記憶はこの場所へ保管しましょう。</strong></p>
          
        <form method="post" id="linkButton" class="needs-validation" novalidate>
            
            <p>興味がありますか？ <br class="sp">今すぐアカウントを作成しましょう。</p>
              <div class="form-floating mb-3">
                <input type="name" name="name" class="form-control" id="InputName" placeholder="名前を入力してください" required>
                <label for="floatingInputInvalid">Name</label>
                <div class="invalid-feedback">
                  名前を入力してください
                </div>
              </div>

              <div class="form-floating mb-3">
                <input type="email" name="email" class="form-control" id="InputEmail" placeholder="Email" required>
                <label for="floatingInputInvalid">Email</label>
                <div class="invalid-feedback">
                  Emailアドレスを入力してください
                </div>
              </div>

             <div class="form-floating mb-3">
                <input type="password" name="password" class="form-control" id="InputPassword" placeholder="Password" required>
                <label for="floatingInputInvalid">Password</label>
                <div class="invalid-feedback">
                  パスワードを入力してください
                </div>
              </div>
              <div class="mb-3 form-check">
                <label class="form-check-label" for="exampleCheck1">
                    <input type="checkbox" class="form-check-input" id="Check" name="check">
                    ログイン状態を保持する
                </label>
              </div>
              <button type="button" id="signup" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#staticBackdrop">アカウントを作成</button>

            </form>
          
            <form method="post" id="linkButtonAnother" class="needs-validation" novalidate>
            <p id="space">
                <button type="submit" id="login" name="login" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staticBackdrop1">ログインはこちら</button>
            </p>
            </form>
      </div>
      
      <!-- Modal -->
      <form method="post">
          <!-- アカウントを作成押下時モーダル -->
                <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                          <span style="color: black;">
                        <h5 class="modal-title" id="staticBackdropLabel">登録の確認</h5>
                            </span>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                            <span style="color: black;">
                        登録してよろしいですか？
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
      
    <!-- アカウントを作成ボタン押下時に未入力項目が存在する場合アラート表示 -->
     <script type="text/javascript">
         const buttonOpen = document.getElementById('signup');
         buttonOpen.addEventListener('click',signup);
         function showMessage(){
             staticBackdrop.style.display = 'block';
         };      

            (function () {
              'use strict'

              var forms = document.querySelectorAll('.needs-validation')

              Array.prototype.slice.call(forms)
                .forEach(function (form) {
                  form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                      event.preventDefault()
                      event.stopPropagation()
                    }

                    form.classList.add('was-validated')
                  }, false)
                })
            })()
    </script>
    
  </body>
</html>