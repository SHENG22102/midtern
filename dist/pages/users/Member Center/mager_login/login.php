<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>管理者登入頁面</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">

<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header text-center">
                    <h3>管理者登入頁面</h3>
                </div>
                <div class="card-body">
                    <?php
                    $host = "127.0.0.1";
                    $db_username = "root";
                    $db_password = "P@ssw0rd";
                    $database = "membercenter";

                    session_start();  // 啟用交談期
                    $manager_account = "";  
                    $manager_password = "";

                    // 取得表單欄位值
                    if (isset($_POST["manager_account"]))
                        $manager_account = $_POST["manager_account"];
                    if (isset($_POST["manager_password"]))
                        $manager_password = $_POST["manager_password"];

                    // 檢查是否有表單提交
                    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                        // 檢查是否有輸入帳號和密碼
                        if (!empty($manager_account) && !empty($manager_password)) {
                            // 建立MySQL的資料庫連接 
                            $link = mysqli_connect($host, $db_username, $db_password, $database)
                                or die("無法開啟MySQL資料庫連接!<br/>");

                            // 送出UTF8編碼的MySQL指令
                            mysqli_query($link, 'SET NAMES utf8');

                            // 建立SQL指令字串，只查詢帳號（不直接比對密碼）
                            $sql = "SELECT * FROM manager WHERE manager_account = '$manager_account'";

                            // 執行SQL查詢
                            $result = mysqli_query($link, $sql);
                            $manager = mysqli_fetch_assoc($result);  // 取得單筆管理者資料

                            // 檢查是否有查詢到該帳號
                            if ($manager) {
                                // 驗證輸入的密碼是否正確
                                if (password_verify($manager_password, $manager['manager_password'])) {
                                    // 成功登入，指定Session變數
                                    $_SESSION["login_session"] = true;
                                    header("Location: index_.php");  // 導向登入後的頁面
                                } else {
                                    // 密碼錯誤
                                    echo "<div class='alert alert-danger text-center'>密碼錯誤!</div>";
                                    $_SESSION["login_session"] = false;
                                }
                            } else {
                                // 帳號不存在
                                echo "<div class='alert alert-danger text-center'>帳號不存在!</div>";
                            }
                            mysqli_close($link);  // 關閉資料庫連接
                        } else {
                            echo "<div class='alert alert-warning text-center'>請輸入帳號和密碼</div>";
                        }
                    }
                    ?>
                    <form action="login.php" method="post">
                        <div class="mb-3">
                            <label for="manager_account" class="form-label">帳號:</label>
                            <input type="text" name="manager_account" id="manager_account" class="form-control" required autofocus />
                        </div>
                        <div class="mb-3">
                            <label for="manager_password" class="form-label">密碼:</label>
                            <input type="password" name="manager_password" id="manager_password" class="form-control" required />
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">登入</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap 5 JS and Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>