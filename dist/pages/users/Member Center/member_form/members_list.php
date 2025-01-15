<?php
// 設定資料庫連接參數
$host = "127.0.0.1";
$db_username = "root";
$db_password = "P@ssw0rd";
$database = "membercenter";

// 建立資料庫連接
$link = mysqli_connect($host, $db_username, $db_password, $database);

// 檢查連接是否成功
if (!$link) {
    die("資料庫連接失敗: " . mysqli_connect_error());
}

// 設置編碼，防止中文亂碼
mysqli_query($link, "SET NAMES utf8");

// 處理搜尋條件
$search_query = "";
$search_type = ""; // 搜尋類別

if (isset($_GET['search'])) {
    // 使用 mysqli_real_escape_string() 來防止 SQL 注入
    $search_query = mysqli_real_escape_string($link, $_GET['search']);
}

if (isset($_GET['search_type'])) {
    // 搜尋類別
    $search_type = $_GET['search_type'];
}

// 處理刪除請求
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // 建立刪除的 SQL 指令
    $delete_sql = "DELETE FROM users WHERE user_id = $delete_id";

    // 執行刪除操作
    if (mysqli_query($link, $delete_sql)) {
        echo "<script>alert('刪除成功'); window.location.href='members_list.php';</script>";
    } else {
        echo "<script>alert('刪除失敗'); window.location.href='members_list.php';</script>";
    }
}

// 撰寫 SQL 查詢指令來提取會員資料，並根據搜尋條件進行過濾
$sql = "SELECT user_id, user_email, user_password, user_name, user_number, user_address, user_birthday, user_level, profile_picture, user_status FROM users";

// 根據搜尋類別和搜尋字串動態修改SQL查詢
if (!empty($search_query) && !empty($search_type)) {
    switch ($search_type) {
        case 'email':
            $sql .= " WHERE user_email LIKE '%$search_query%'";
            break;
        case 'name':
            $sql .= " WHERE user_name LIKE '%$search_query%'";
            break;
        case 'phone':
            $sql .= " WHERE user_number LIKE '%$search_query%'";
            break;
        case 'address':
            $sql .= " WHERE user_address LIKE '%$search_query%'";
            break;
    }
}

// 執行 SQL 查詢
$result = mysqli_query($link, $sql);

// 檢查查詢是否成功
if (!$result) {
    die("查詢失敗: " . mysqli_error($link));
}
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>會員列表</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        h1 {
            color: #333;
            text-align: center;
        }
        .container {
            width: 80%;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .search-box {
            text-align: center;
            margin-bottom: 20px;
        }
        .search-box input[type="text"] {
            padding: 10px;
            width: 300px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .search-box select {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .search-box input[type="submit"] {
            padding: 10px 20px;
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .search-box input[type="submit"]:hover {
            background-color: #218838;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
        }
        th {
            background-color: #f8f9fa;
        }
        td img {
            border-radius: 50%;
        }
        .action-links a {
            color: #007bff;
            text-decoration: none;
            margin: 0 5px;
        }
        .action-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1>會員列表</h1>

    <div class="container">
        <!-- 搜尋表單 -->
        <div class="search-box">
            <form method="GET" action="members_list.php">
                <input type="text" name="search" placeholder="輸入搜尋內容" value="<?php echo htmlspecialchars($search_query); ?>">

                <select name="search_type">
                    <option value="">選擇搜尋類型</option>
                    <option value="email" <?php if ($search_type == 'email') echo 'selected'; ?>>Email</option>
                    <option value="name" <?php if ($search_type == 'name') echo 'selected'; ?>>姓名</option>
                    <option value="phone" <?php if ($search_type == 'phone') echo 'selected'; ?>>電話</option>
                    <option value="address" <?php if ($search_type == 'address') echo 'selected'; ?>>地址</option>
                </select>

                <input type="submit" value="搜尋">
            </form>
        </div>

        <table>
            <thead>
                <tr>
                    <th>會員ID</th>
                    <th>會員Email</th>
                    <th>密碼</th>
                    <th>姓名</th>
                    <th>電話</th>
                    <th>地址</th>
                    <th>生日</th>
                    <th>等級</th>
                    <th>頭像</th>
                    <th>狀態</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // 使用 while 迴圈來提取每一行數據並顯示在表格中
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $row['user_id'] . "</td>";
                    echo "<td>" . $row['user_email'] . "</td>";
                    echo "<td>" . $row['user_password'] . "</td>";
                    echo "<td>" . $row['user_name'] . "</td>";
                    echo "<td>" . $row['user_number'] . "</td>";
                    echo "<td>" . $row['user_address'] . "</td>";
                    echo "<td>" . $row['user_birthday'] . "</td>";
                    echo "<td>" . $row['user_level'] . "</td>";

                    // 顯示頭像，如果有圖片檔案
                    if (!empty($row['profile_picture'])) {
                        echo "<td><img src='" . $row['profile_picture'] . "' alt='Profile Picture' width='50' height='50'></td>";
                    } else {
                        echo "<td>無頭像</td>";
                    }

                    echo "<td>" . $row['user_status'] . "</td>";
                    echo "<td class='action-links'>";
                    echo "<a href='members_edit.php?user_id=" . $row['user_id'] . "'>編輯</a> | ";
                    echo "<a href='members_list.php?delete_id=" . $row['user_id'] . "' onclick='return confirm(\"確定要刪除此會員嗎？\");'>刪除</a>";
                    echo "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>

        <?php
        // 釋放結果集
        mysqli_free_result($result);

        // 關閉資料庫連接
        mysqli_close($link);
        ?>
    </div>
</body>
</html>
