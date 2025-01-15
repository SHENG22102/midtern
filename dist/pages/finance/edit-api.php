<?php
require __DIR__ . '/parts/init.php';
header('Content-Type: application/json');

$output = [
    'success' => false,
    'error' => '',
    'code' => 0
];

$dn_id = isset($_POST['dn_id']) ? intval($_POST['dn_id']) : 0;
$donor_name = isset($_POST['donor_name']) ? trim($_POST['donor_name']) : '';
$donor_phone = isset($_POST['donor_phone']) ? trim($_POST['donor_phone']) : '';
$donor_email = isset($_POST['donor_email']) ? trim($_POST['donor_email']) : '';
$amount = isset($_POST['amount']) ? intval($_POST['amount']) : 0;
$donation_type = isset($_POST['donation_type']) ? $_POST['donation_type'] : '';
$pet_id = isset($_POST['pet_id']) ? intval($_POST['pet_id']) : 0;
$donation_mode = isset($_POST['donation_mode']) ? $_POST['donation_mode'] : '';
$regular_payment_date = isset($_POST['regular_payment_date']) ? $_POST['regular_payment_date'] : '';
$payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : '';
$reconciliation_status = isset($_POST['reconciliation_status']) ? $_POST['reconciliation_status'] : '';
$is_receipt_needed = isset($_POST['is_receipt_needed']) ? intval($_POST['is_receipt_needed']) : 0;

// 收據相關資料
$receipt_name = isset($_POST['receipt_name']) ? trim($_POST['receipt_name']) : '';
$receipt_phone = isset($_POST['receipt_phone']) ? trim($_POST['receipt_phone']) : '';
$receipt_address = isset($_POST['receipt_address']) ? trim($_POST['receipt_address']) : '';

// 資料檢查
if (empty($dn_id) || empty($donor_name) || empty($donor_phone) || empty($donor_email) || $amount <= 0 || empty($donation_type) || empty($donation_mode) || empty($payment_method) || empty($reconciliation_status)) {
    $output['code'] = 400;
    $output['error'] = '請填寫所有必要的欄位';
    echo json_encode($output, JSON_UNESCAPED_UNICODE);
    exit;
}

if ($donation_type != '線上認養'){
  $pet_id = null;
}
if ($donation_mode != '定期捐款'){
  $regular_payment_date = null;
}
// 更新捐款資料
$sql_update_donation = "UPDATE donations SET 
    donor_name = ?, 
    donor_phone = ?, 
    donor_email = ?, 
    amount = ?, 
    donation_type = ?,
    pet_id = ?, 
    donation_mode = ?, 
    regular_payment_date = ?,
    payment_method = ?, 
    reconciliation_status = ?, 
    is_receipt_needed = ? 
    WHERE id = ?";

$stmt_update_donation = $pdo->prepare($sql_update_donation);
$stmt_update_donation->execute([
    $donor_name,
    $donor_phone,
    $donor_email,
    $amount,
    $donation_type,
    $pet_id,
    $donation_mode,
    $regular_payment_date,
    $payment_method,
    $reconciliation_status,
    $is_receipt_needed,
    $dn_id
]);

$donation_updated = $stmt_update_donation->rowCount() > 0;

// 收據資料處理
$receipt_updated = false;
if ($is_receipt_needed === 1) {
    // 檢查是否已存在收據
    $sql_check_receipt = "SELECT 1 FROM receipts WHERE donation_id = ?";
    $stmt_check_receipt = $pdo->prepare($sql_check_receipt);
    $stmt_check_receipt->execute([$dn_id]);

    if ($stmt_check_receipt->rowCount() > 0) {
        // 更新收據
        $sql_update_receipt = "UPDATE receipts SET 
            receipt_name = ?, 
            receipt_phone = ?, 
            receipt_address = ? 
            WHERE donation_id = ?";
        $stmt_update_receipt = $pdo->prepare($sql_update_receipt);
        $stmt_update_receipt->execute([
            $receipt_name,
            $receipt_phone,
            $receipt_address,
            $dn_id
        ]);
        $receipt_updated = $stmt_update_receipt->rowCount() > 0;
    } else {
        // 新增收據
        $sql_insert_receipt = "INSERT INTO receipts 
            (donation_id, receipt_name, receipt_phone, receipt_address) 
            VALUES (?, ?, ?, ?)";
        $stmt_insert_receipt = $pdo->prepare($sql_insert_receipt);
        $stmt_insert_receipt->execute([
            $dn_id,
            $receipt_name,
            $receipt_phone,
            $receipt_address
        ]);
        $receipt_updated = $stmt_insert_receipt->rowCount() > 0;
    }
} else {
    // 刪除收據資料
    $sql_delete_receipt = "DELETE FROM receipts WHERE donation_id = ?";
    $stmt_delete_receipt = $pdo->prepare($sql_delete_receipt);
    $stmt_delete_receipt->execute([$dn_id]);
    $receipt_updated = $stmt_delete_receipt->rowCount() > 0;
}

// 總結果判斷
if ($donation_updated || $receipt_updated) {
    $output['success'] = true;
} else {
    $output['error'] = '未做任何變更，請檢查資料';
}

echo json_encode($output, JSON_UNESCAPED_UNICODE);
