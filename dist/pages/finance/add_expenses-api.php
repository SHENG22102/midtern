<?php
require __DIR__ . '/parts/init.php';
header('Content-Type: application/json');

$output = [
    'success' => false,  // 是否成功新增資料
    'bodyData' => $_POST, // 除錯用途
    'code' => 0,          // 自訂編號
    'error' => '',        // 錯誤訊息
    'lastInsertId' => 0,  // 最新插入資料的 ID
];

$expense_purpose = $_POST['expense_purpose'] ?? '';
$amount = $_POST['amount'] ?? '';
$expense_date = $_POST['expense_date'] ?? '';
$e_description = $_POST['e_description'] ?? '';
$refund_id = $_POST['refund_id'] ?? '';
$created_by = $_POST['created_by'] ?? '';

if ($refund_id == ''){
    $refund_id = null;
}

// 檢查必填欄位
$required_fields = ['expense_purpose', 'amount', 'expense_date', 'e_description', 'created_by'];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        $output['code'] = 400; // 自訂錯誤代碼
        $output['error'] = "{$field} 欄位是必填的";
        echo json_encode($output, JSON_UNESCAPED_UNICODE);
        exit;
    }
};

// 檢查轉帳金額是否為有效數字
if (!is_numeric($_POST['amount']) || $_POST['amount'] <= 0) {
    $output['code'] = 403;
    $output['error'] = '請填寫正確的支出金額 !';
    echo json_encode($output, JSON_UNESCAPED_UNICODE);
    exit;
};

// 資料庫新增 SQL
$sql = "INSERT INTO `expenses` 
    (`expense_purpose`, `amount`, `expense_date`, `refund_id`, `e_description`, created_by) 
    VALUES ( ?, ?, ?, ?, ?, ?)";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    $expense_purpose,
    $amount,
    $expense_date,
    $refund_id,
    $e_description,
    $created_by,
]);

// 檢查資料是否新增成功
if ($stmt->rowCount() > 0) {
    $output['success'] = true;
    $output['lastInsertId'] = $pdo->lastInsertId();
} else {
    $output['code'] = 403;
    $output['error'] = '資料新增失敗，請稍後再試。';
}

echo json_encode($output, JSON_UNESCAPED_UNICODE);
