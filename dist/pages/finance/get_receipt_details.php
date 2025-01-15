<?php
require __DIR__ . '/parts/init.php';

// 確保獲得正確的 ID 參數
$donationId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($donationId > 0) {
    // 查詢收據資料
    $sql = "SELECT * FROM receipts WHERE donation_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$donationId]);
    $receipt = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($receipt) {
        // 返回收據資料
        echo json_encode([
            'success' => true,
            'receipt_name' => $receipt['receipt_name'],
            'receipt_phone' => $receipt['receipt_phone'],
            'receipt_address' => $receipt['receipt_address']
        ]);
    } else {
        // 如果找不到收據資料
        echo json_encode(['success' => false]);
    }
} else {
    echo json_encode(['success' => false]);
}
?>
