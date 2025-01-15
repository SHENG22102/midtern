<?php
// FILEPATH: /c:/xampp/htdocs/midtern/dist/pages/forums/update_user_status.php

require __DIR__ . '/../parts/init.php';

// 設置響應頭為 JSON
header('Content-Type: application/json');

// 檢查是否為 POST 請求
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => '無效的請求方法']);
    exit;
}

// 獲取並驗證輸入
$user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
$status = isset($_POST['status']) ? $_POST['status'] : '';

// 驗證用戶 ID
if ($user_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => '無效的用戶 ID']);
    exit;
}

// 驗證狀態
$valid_statuses = ['正常', '禁言'];
if (!in_array($status, $valid_statuses)) {
    echo json_encode(['status' => 'error', 'message' => '無效的狀態']);
    exit;
}

try {
    // 準備 SQL 語句
    $sql = "UPDATE users SET user_status = :status WHERE user_id = :user_id";
    $stmt = $pdo->prepare($sql);

    // 綁定參數並執行
    $stmt->execute([
        ':status' => $status,
        ':user_id' => $user_id
    ]);

    // 檢查是否有行被更新
    if ($stmt->rowCount() > 0) {
        echo json_encode(['status' => 'success', 'message' => '用戶狀態已更新']);
    } else {
        echo json_encode(['status' => 'error', 'message' => '未找到用戶或狀態未變更']);
    }
} catch (PDOException $e) {
    // 捕獲數據庫錯誤
    error_log('Database error: ' . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => '數據庫錯誤，請稍後再試']);
}
