<?php
// 資料庫連接設定
$host = "127.0.0.1";
$db_username = "root";
$db_password = "P@ssw0rd";
$database = "membercenter";

// 檢查是否有傳遞管理者ID
if (isset($_GET['id'])) {
    $manager_id = $_GET['id'];

    // 建立資料庫連接
    $link = mysqli_connect($host, $db_username, $db_password, $database);
    if (!$link) {
        die("無法連接資料庫: " . mysqli_connect_error());
    }

    mysqli_query($link, "SET NAMES utf8");

    // 取得管理者資料
    $sql = "SELECT * FROM manager WHERE id = '$manager_id'";
    $result = mysqli_query($link, $sql);
    $manager = mysqli_fetch_assoc($result);
    if (!$manager) {
        die("管理者資料不存在");
    }

    // 如果表單提交，更新資料庫
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $manager_account = $_POST['manager_account'];
        $manager_privileges = $_POST['manager_privileges'];

        $update_sql = "UPDATE manager SET manager_account='$manager_account', manager_privileges='$manager_privileges' 
                       WHERE id='$manager_id'";

        if (mysqli_query($link, $update_sql)) {
            echo "<div class='alert success'>管理者資料更新成功！</div>";
            echo "<a href='manager_list.php' class='button'>返回管理者名單</a>";
        } else {
            echo "<div class='alert error'>更新失敗: " . mysqli_error($link) . "</div>";
        }
    }

    mysqli_close($link);
} else {
    echo "<div class='alert error'>無效的管理者ID</div>";
}
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>編輯管理者</title>
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
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        label {
            font-size: 16px;
            margin-bottom: 5px;
            display: block;
        }
        input[type="text"], input[type="submit"] {
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
    <h1>編輯管理者</h1>

    <form action="edit_manager.php?id=<?php echo $manager['id']; ?>" method="POST">
        <label for="manager_account">管理者帳號:</label>
        <input type="text" name="manager_account" id="manager_account" value="<?php echo $manager['manager_account']; ?>" required><br>

        <label for="manager_privileges">管理者權限:</label>
        <input type="text" name="manager_privileges" id="manager_privileges" value="<?php echo $manager['manager_privileges']; ?>" required><br>

        <input type="submit" value="更新管理者資料">
    </form>
</div>

</body>
</html>
