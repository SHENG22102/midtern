<?php
require __DIR__ . '/parts/init.php';
header('Content-Type: application/json');

$output = [
    'success' => false,
    'error' => '',
    'code' => 0
];

$bn_id = isset($_POST['bn_id']) ? intval($_POST['bn_id']) : 0;
$donor_name = isset($_POST['donor_name']) ? trim($_POST['donor_name']) : '';
$transfer_amount = isset($_POST['transfer_amount']) ? trim($_POST['transfer_amount']) : '';
$transfer_date = isset($_POST['transfer_date']) ? trim($_POST['transfer_date']) : '';
$id_or_tax_id_number = isset($_POST['id_or_tax_id_number']) ? trim($_POST['id_or_tax_id_number']) : '';
$reconciliation_status = isset($_POST['reconciliation_status']) ? $_POST['reconciliation_status'] : '';

// 資料檢查
if (empty($donor_name) || empty($transfer_amount) || empty($transfer_date) || empty($id_or_tax_id_number) || empty($reconciliation_status)) {
    $output['code'] = 400;
    $output['error'] = '請填寫所有必要的欄位';
    echo json_encode($output, JSON_UNESCAPED_UNICODE);
    exit;
}

if (!is_numeric($transfer_amount) || $transfer_amount <= 0) {
    $output['code'] = 400;
    $output['error'] = '捐款金額必須為正數字';
    echo json_encode($output, JSON_UNESCAPED_UNICODE);
    exit;
}

// 更新審核資料
$sql_update_donation = "UPDATE bank_transfer_details SET 
    donor_name = ?, 
    transfer_amount = ?, 
    transfer_date = ?, 
    id_or_tax_id_number = ?, 
    reconciliation_status = ?
    WHERE id = ?";

$stmt_update_donation = $pdo->prepare($sql_update_donation);
$stmt_update_donation->execute([
    $donor_name,
    $transfer_amount,
    $transfer_date,
    $id_or_tax_id_number,
    $reconciliation_status,
    $bn_id
]);

$donation_updated = $stmt_update_donation->rowCount() > 0;

// 總結果判斷
if ($donation_updated) {
    $output['success'] = true;
} else {
    $output['error'] = '未做任何變更，請檢查資料';
}

echo json_encode($output, JSON_UNESCAPED_UNICODE);
