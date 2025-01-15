<?php
require __DIR__ . '/parts/init.php';
header('Content-Type: application/json');

$output = [
    'success' => false,
    'error' => '',
    'code' => 0
];

$id = (int) $_POST['expenses_id'] ?? 0;
$expense_purpose = $_POST['expense_purpose'] ?? '';
$amount = $_POST['amount'] ?? '';
$expense_date = $_POST['expense_date'] ?? '';
$e_description = $_POST['e_description'] ?? '';
$refund_id = $_POST['refund_id'] ?? '';
$created_by = $_POST['created_by'] ?? '';

// 資料檢查
if (empty($id) || empty($expense_purpose) || empty($amount) || empty($expense_date) || empty($e_description) || empty($created_by)) {
    $output['code'] = 400;
    $output['error'] = '請填寫所有必要的欄位';
    echo json_encode($output, JSON_UNESCAPED_UNICODE);
    exit;
}
if ($refund_id == ''){
    $refund_id = null;
}

// 更新審核資料
$sql_update_expenses = "UPDATE expenses SET 
    expense_purpose = ?, 
    amount = ?, 
    expense_date = ?, 
    e_description = ?,
    refund_id = ?,
    created_by = ?
    WHERE id = ?";

$stmt_update_expenses = $pdo->prepare($sql_update_expenses);
$stmt_update_expenses->execute([
    $expense_purpose,
    $amount,
    $expense_date,
    $e_description,
    $refund_id,
    $created_by,
    $id
]);

$expenses_updated = $stmt_update_expenses->rowCount() > 0;

// 總結果判斷
if ($expenses_updated) {
    $output['success'] = true;
} else {
    $output['error'] = '未做任何變更，請檢查資料';
}

echo json_encode($output, JSON_UNESCAPED_UNICODE);