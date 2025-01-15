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
$pet_id = isset($_POST['pet_id']) ? $_POST['pet_id'] : '';
$donation_mode = isset($_POST['donation_mode']) ? $_POST['donation_mode'] : '';
$payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : '';
$reconciliation_status = isset($_POST['reconciliation_status']) ? $_POST['reconciliation_status'] : '';
$is_receipt_needed = isset($_POST['is_receipt_needed']) ? $_POST['is_receipt_needed'] : '';

//取得收據資料
$receipt_name = isset($_POST['receipt_name']) ? trim($_POST['receipt_name']) : '';
$receipt_phone = isset($_POST['receipt_phone']) ? trim($_POST['receipt_phone']) : '';
$receipt_address = isset($_POST['receipt_address']) ? trim($_POST['receipt_address']) : '';

// 資料檢查
if (
  $donor_name === '' || 
  $donor_phone === '' || 
  $donor_email === '' || 
  $amount <= 0 || 
  $donation_type === '' || 
  $pet_id === '' || 
  $donation_mode === '' || 
  $payment_method === '' || 
  $reconciliation_status === '' || 
  $is_receipt_needed === ''
){
  $output['code'] = 400;
  $output['error'] = '請填寫所有必要的欄位';
  echo json_encode($output, JSON_UNESCAPED_UNICODE);
  exit;
}

// 更新捐款資料
$sql = "UPDATE donations SET 
  donor_name = ?, 
  donor_phone = ?, 
  donor_email = ?, 
  amount = ?, 
  donation_type = ?, 
  pet_id = ?, 
  donation_mode = ?, 
  payment_method = ?,
  reconciliation_status = ?,
  is_receipt_needed = ?
  WHERE id = ?";

$stmt = $pdo->prepare($sql);
$stmt->execute([
  $donor_name,
  $donor_phone,
  $donor_email,
  $amount,
  $donation_type,
  $pet_id,
  $donation_mode,
  $payment_method,
  $reconciliation_status,
  $is_receipt_needed,
  $dn_id
]);

// 收據資料新增
if ($is_receipt_needed == 1) {
  // 檢查是否已存在對應的收據資料
  $sql = "SELECT * FROM receipts WHERE donation_id = ?";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([$dn_id]);

  if ($stmt->rowCount() > 0) {
    // 更新收據資料
    $sql = "UPDATE receipts SET 
        receipt_name = ?, 
        receipt_phone = ?, 
        receipt_address = ? 
        WHERE donation_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
      $receipt_name,
      $receipt_phone,
      $receipt_address,
      $dn_id
    ]);
  } else {
    // 新增收據資料
    $sql = "INSERT INTO receipts 
        (donation_id, receipt_name, receipt_phone, receipt_address) 
        VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
      $dn_id,
      $receipt_name,
      $receipt_phone,
      $receipt_address
    ]);
  }
} else {
  // 刪除對應的收據資料
  $sql = "DELETE FROM receipts WHERE donation_id = ?";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([$dn_id]);
}

if ($stmt->rowCount() || $is_receipt_needed == 1) {
  $output['success'] = true;
} else {
  $output['error'] = '資料沒有變更或更新失敗，請檢查輸入內容';
}

echo json_encode($output, JSON_UNESCAPED_UNICODE);
