<?php
require __DIR__ . '/parts/init.php'; // 確保資料庫連線已初始化

# 取得指定的捐款 ID
$dn_id = empty($_GET['id']) ? 0 : intval($_GET['id']);

# 驗證 ID 是否為正整數
if (empty($dn_id) || !filter_var($dn_id, FILTER_VALIDATE_INT)) {
    die('無效的捐款 ID');
}

# 檢查是否存在該捐款 ID
$sql = "SELECT COUNT(*) FROM donations WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $dn_id]);
$count = $stmt->fetchColumn();

if ($count == 0) {
    die('找不到該紀錄，無法刪除');
}

# 執行刪除操作
if ($dn_id) {
  $sql = "DELETE FROM donations WHERE id={$dn_id} ";
  $pdo->query($sql);
}

# 檢查刪除是否成功
if ($stmt->rowCount() > 0) {
    $message = '刪除成功';
} else {
    $message = '刪除失敗';
}


# 設定重導向的頁面
$come_from = 'donations.php';
if (isset($_SERVER['HTTP_REFERER'])) {
  $come_from = $_SERVER['HTTP_REFERER'];
}

# 重導向到原始頁面或預設頁面
// header("Location: $come_from");
header("Location: $come_from");
exit;