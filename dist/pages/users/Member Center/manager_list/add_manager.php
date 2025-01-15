<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 資料庫連接設定
    $host = "127.0.0.1";
    $db_username = "root";
    $db_password = "P@ssw0rd";
    $database = "membercenter";
    
    // 取得表單資料
    $manager_account = $_POST['manager_account'];
    $manager_privileges = $_POST['manager_privileges'];
    $manager_password = $_POST['manager_password'];
    $confirm_password = $_POST['confirm_password'];

    // 建立資料庫連接
    $link = mysqli_connect($host, $db_username, $db_password, $database);
    if (!$link) {
        die("無法連接資料庫: " . mysqli_connect_error());
    }

    mysqli_query($link, "SET NAMES utf8");

    // 檢查帳號是否已存在
    $check_sql = "SELECT * FROM manager WHERE manager_account = '$manager_account'";
    $check_result = mysqli_query($link, $check_sql);

    if (mysqli_num_rows($check_result) > 0) {
        // 如果帳號已經存在，顯示錯誤訊息
        echo "<div class='alert error'>該帳號已經存在，請選擇其他帳號。</div>";
    } else {
        // 確認密碼和確認密碼是否一致
        if ($manager_password !== $confirm_password) {
            echo "<div class='alert error'>密碼和確認密碼不一致，請重新輸入。</div>";
            exit;
        }

        // 加密密碼
        $hashed_password = password_hash($manager_password, PASSWORD_DEFAULT);

        // 插入資料庫
        $sql = "INSERT INTO manager (manager_account, manager_privileges, manager_password) 
                VALUES ('$manager_account', '$manager_privileges', '$hashed_password')";

        if (mysqli_query($link, $sql)) {
            echo "<div class='alert success'>新增管理者成功！</div>";
            echo "<a href='manager_list.php' class='button'>返回管理者名單</a>";
        } else {
            echo "<div class='alert error'>新增管理者失敗: " . mysqli_error($link) . "</div>";
        }
    }

    // 關閉資料庫連接
    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>新增管理者</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 20px;
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        label {
            font-size: 16px;
            margin-bottom: 5px;
            display: block;
        }
        input[type="text"], input[type="password"], input[type="submit"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        .alert {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
        }
        .alert.success {
            background-color: #d4edda;
            color: #155724;
        }
        .alert.error {
            background-color: #f8d7da;
            color: #721c24;
        }
        .button {
            display: inline-block;
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
            text-align: center;
        }
        .button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>新增管理者</h1>

    <form action="add_manager.php" method="POST">
        <label for="manager_account">管理者帳號:</label>
        <input type="text" name="manager_account" id="manager_account" required><br>

        <label for="manager_privileges">管理者權限:</label>
        <input type="text" name="manager_privileges" id="manager_privileges" required><br>

        <label for="manager_password">管理者密碼:</label>
        <input type="password" name="manager_password" id="manager_password" required><br>

        <label for="confirm_password">確認密碼:</label>
        <input type="password" name="confirm_password" id="confirm_password" required><br>

        <input type="submit" value="新增管理者">
    </form>
</div>

</body>
</html>
