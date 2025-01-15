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

// 檢查必填欄位
$required_fields = ['donor_name', 'donor_phone', 'donor_email', 'amount', 'donation_type', 'donation_mode', 'payment_method'];
foreach ($required_fields as $field) {
  if (empty($_POST[$field])) {
    $output['code'] = 400; // 自訂錯誤代碼
    $output['error'] = "{$field} 欄位是必填的";
    echo json_encode($output, JSON_UNESCAPED_UNICODE);
    exit;
  }
}
$pet_id = $_POST['pet_id'] ?? null;

// 驗證 email 格式
$email = filter_var($_POST['donor_email'], FILTER_VALIDATE_EMAIL);
if (!$email) {
  $output['code'] = 401;
  $output['error'] = '請填寫正確的 Email !';
  echo json_encode($output, JSON_UNESCAPED_UNICODE);
  exit;
}

// 驗證手機格式 (台灣手機)
$phone = $_POST['donor_phone'];
if (!preg_match('/^09\d{8}$/', $phone)) {
  $output['code'] = 402;
  $output['error'] = '請填寫正確的手機號碼 !';
  echo json_encode($output, JSON_UNESCAPED_UNICODE);
  exit;
}

// 檢查金額是否為有效數字
if (!is_numeric($_POST['amount']) || $_POST['amount'] <= 0) {
  $output['code'] = 403;
  $output['error'] = '請填寫正確的捐款金額 !';
  echo json_encode($output, JSON_UNESCAPED_UNICODE);
  exit;
}
$amount = intval($_POST['amount']); // 轉為整數


// 取得其他 POST 資料
$donor_name = $_POST['donor_name'];
$donor_phone = $_POST['donor_phone'] ?? '';
$donor_email = $_POST['donor_email'] ?? '';
$amount = $_POST['amount'];
$donation_type = $_POST['donation_type'];
$pet_id = $_POST['pet_id'];
$donation_mode = $_POST['donation_mode'];
$regular_payment_date = $_POST['regular_payment_date'];
$payment_method = $_POST['payment_method'];
$is_receipt_needed = $_POST['is_receipt_needed'];

if ($donation_type != '線上認養') {
  $pet_id = null;
}
if ($donation_mode != '定期捐款') {
  $regular_payment_date = null;
}

// 資料庫新增 SQL
$sql = "INSERT INTO `donations` 
    (`donor_name`, `donor_phone`, `donor_email`, `amount`, donation_type, `pet_id`, `donation_mode`, `regular_payment_date`, `payment_method`,`is_receipt_needed`) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $pdo->prepare($sql);
$stmt->execute([
  $donor_name,
  $donor_phone,
  $donor_email,
  $amount,
  $donation_type,
  $pet_id,
  $donation_mode,
  $regular_payment_date,
  $payment_method,
  $is_receipt_needed
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
