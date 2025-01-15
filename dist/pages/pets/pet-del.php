<?php
# 管理者登入驗證
// require __DIR__ . '/parts/admin-required.php'; 
require __DIR__ . '/../parts/init.php';

// 如果網址列有id就轉換成整數(避免SQL注入)
$id = empty($_GET['id']) ? 0 : intval($_GET['id']);

$result = [
  'status' => 'error',
  'message' => '發生錯誤',
];
if ($id) {
  if ($id > 0) {
    $stmt = $pdo->prepare("DELETE FROM pets WHERE id = ?");
    $stmt->execute([$id]);

    $affected_rows = $stmt->rowCount(); // 取得受影響的列數，用來判斷是否有刪到資料
    if ($affected_rows > 0) {
      $result['status'] = 'success';
      $result['message'] = '資料已成功刪除';
      $_SESSION['show_alert'] = true; // 設置一個標誌來顯示 alert
      $_SESSION['deleted_id'] = $id;  // 假設 $id 是被刪除項目的 id
      $_SESSION['deleted_name'] = $name;  // 假設 $name 是被刪除項目的 name
    } else {
      $result['message'] = '查無此筆資料';
    }
  }
} else {
  $result['message'] = '缺少 id 參數';
}

$_SESSION['message'] = $result['message'];
$_SESSION['type'] = $result['status'];

// 刪除後導向回原本的頁面
$come_from = 'pet-list.php';
if (isset($_SERVER['HTTP_REFERER'])) {
  $come_from = $_SERVER['HTTP_REFERER'];
}
header("Location:$come_from");
