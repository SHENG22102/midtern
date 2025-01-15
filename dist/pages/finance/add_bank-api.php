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
$required_fields = ['bn_id', 'donation_id', 'donor_name', 'transfer_amount', 'transfer_date', 'id_or_tax_id_number', 'reconciliation_status'];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        $output['code'] = 400; // 自訂錯誤代碼
        $output['error'] = "{$field} 欄位是必填的";
        echo json_encode($output, JSON_UNESCAPED_UNICODE);
        exit;
    }
}

// 檢查 donation_id 是否存在
$donation_id = $_POST['donation_id'];
$sql = "SELECT * FROM donations WHERE id = :donation_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['donation_id' => $donation_id]);
$donation = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$donation) {
    $output['code'] = 404;
    $output['error'] = '指定的捐款 ID 不存在';
    echo json_encode($output, JSON_UNESCAPED_UNICODE);
    exit;
}

// 檢查轉帳金額是否為有效數字
if (!is_numeric($_POST['transfer_amount']) || $_POST['transfer_amount'] <= 0) {
    $output['code'] = 403;
    $output['error'] = '請填寫正確的轉帳金額 !';
    echo json_encode($output, JSON_UNESCAPED_UNICODE);
    exit;
}

$bn_id = $_POST['bn_id'];
$transfer_amount = intval($_POST['transfer_amount']); // 轉為整數
$donor_name = $_POST['donor_name'];
$transfer_date = $_POST['transfer_date'];
$id_or_tax_id_number = $_POST['id_or_tax_id_number'];
$reconciliation_status = $_POST['reconciliation_status'];

// 更新 bank_transfer_details 表的資料
$sql = "UPDATE `bank_transfer_details` SET
    `donor_name` = ?, 
    `transfer_amount` = ?, 
    `transfer_date` = ?, 
    `id_or_tax_id_number` = ?, 
    `reconciliation_status` = ? 
    WHERE `id` = ?";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    $donor_name,
    $transfer_amount,
    $transfer_date,
    $id_or_tax_id_number,
    $reconciliation_status,
    $bn_id
]);

// 檢查 bank_transfer_details 表是否成功更新
if ($stmt->rowCount() > 0) {
    // 同步更新 donations 表的 reconciliation_status 字段
    $sql = "UPDATE `donations` SET `reconciliation_status` = ? WHERE `id` = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$reconciliation_status, $donation_id]);

    if ($stmt->rowCount() > 0) {
        $output['success'] = true;
        $output['lastInsertId'] = $bn_id;  // 此处可以传递修改后的记录 ID
    } else {
        $output['code'] = 500;
        $output['error'] = '更新 donations 表的資料失敗';
    }
} else {
    $output['code'] = 404;
    $output['error'] = '資料更新失敗，請稍後再試。';
}

echo json_encode($output, JSON_UNESCAPED_UNICODE);
