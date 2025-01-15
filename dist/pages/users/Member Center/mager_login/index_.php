<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>管理者首頁</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background-color: #f4f4f9;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }
        .alert {
            padding: 15px;
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
        .btn {
            margin-top: 20px;
            width: 100%;
            padding: 12px;
            font-size: 16px;
            background-color: #4CAF50;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            text-align: center;
        }
        .btn:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

<div class="container">
    <?php
    session_start();
    ob_start();
    // 檢查是否已登入
    if (!isset($_SESSION['login_session']) || $_SESSION['login_session'] !== true) {
        header("Location: login.php");  // 如果未登入，跳轉回登入頁面
        exit;
    }
    ?>

    <h1>歡迎來到管理者頁面！</h1>

    <div class="alert success">
        <strong>您已成功登入!</strong>
    </div>

    <div class="d-flex justify-content-center">
        <!-- 這裡的 href 指向 demo.php -->
        <a href="demo.php" class="btn">進入管理首頁</a>
    </div>

    <div class="d-flex justify-content-center mt-3">
        <a href="logout.php" class="btn" style="background-color: #f44336;">登出</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
