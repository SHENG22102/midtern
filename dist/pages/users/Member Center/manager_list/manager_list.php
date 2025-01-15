<?php
// 資料庫連接設定
$host = "127.0.0.1";
$db_username = "root";
$db_password = "P@ssw0rd";
$database = "membercenter";

// 建立資料庫連接
$link = mysqli_connect($host, $db_username, $db_password, $database);
if (!$link) {
    die("無法連接資料庫: " . mysqli_connect_error());
}

mysqli_query($link, "SET NAMES utf8"); // 設定資料庫編碼

// 取得所有管理者資料
$sql = "SELECT * FROM manager";
$result = mysqli_query($link, $sql);

?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理者名單</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        table {
            width: 90%;
            margin: 0 auto;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
        }
        th {
            background-color: #4CAF50;
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .button {
            padding: 8px 15px;
            color: white;
            background-color: #4CAF50;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
        }
        .button:hover {
            background-color: #45a049;
        }
        .button-delete {
            background-color: #f44336;
        }
        .button-delete:hover {
            background-color: #e53935;
        }
        .button-container {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<h1>管理者名單</h1>

<table>
    <thead>
        <tr>
            <th>管理者帳號</th>
            <th>管理者權限</th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?php echo $row['manager_account']; ?></td>
                <td><?php echo $row['manager_privileges']; ?></td>
                <td>
                    <a class="button" href="edit_manager.php?id=<?php echo $row['id']; ?>">編輯</a>
                    <a class="button button-delete" href="delete_manager.php?id=<?php echo $row['id']; ?>">刪除</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<div class="button-container">
    <a class="button" href="add_manager.php">新增管理者</a>
</div>

</body>
</html>

<?php
// 關閉資料庫連接
mysqli_close($link);
?>
