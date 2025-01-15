<?php

# 管理者登入驗證
// require __DIR__ . '/parts/admin-required.php'; 

require __DIR__ . '/../../parts/init.php';

header('content-Type: application/json');

$output = [
    'success' => false, #有沒有新增成功
    'bodyData' => $_POST, #除錯的用途
    'code' => 0, #自訂的編號 除錯用
    'error' => '', #回應給前端的錯誤訊息
    'lastInsertId' => 0 #最新拿到的primaryKey
];
//避免SQL注入 用?佔位符號
$sql = "INSERT INTO `pets` 
  ( `name`, `species`,
    `variety`, `gender`, `birthday`, `weight`, `chip_number`, `is_adopted`)
    VALUES ( ?, ?, ?, ?, ?, ?, ?, ?)";

# TODO: 欄位檢查 mobile birthday address

// $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
// if (!$email) {
//     $output['code'] = 401; #自行決定的除錯編碼
//     $output['error'] = '請填寫正確的email';
//     echo json_encode($output, JSON_UNESCAPED_UNICODE);
//     exit;
// }

$birthday = empty($_POST['birthday']) ?null:$_POST['birthday'];
# 處理日期
if (empty($_POST['birthday'])) {
    $birthday = null;
} else {
    $birthday = strtotime($_POST['birthday']); #轉換成timestamp
    if ($birthday === false) {
        //用==如果輸入0也會變成true，所以需要用嚴格等於
        //如果不能轉換成timestamp格式
        $birthday = null;
    } else {
        $birthday = date('Y-m-d', $birthday);
    }
}

// `name`, `species`, `variety`, `gender`, `birthday`, `weight`, `chip`, `is-adopted`
$stmt = $pdo->prepare($sql);
#因為還沒有值 不能直接query,用prepare先編譯成SQL語法
// execute會自動跳脫，避免SQL注入
$stmt->execute([
    $_POST['name'],
    $_POST['species'],
    $_POST['variety'],
    $_POST['gender'],
    $_POST['birthday'],
    $_POST['weight'],
    $_POST['chip'],
    $_POST['is-adopted']
]);

$output['success'] = !! $stmt->rowCount(); #新增幾筆，轉換成bool
$output['lastInsertId'] = $pdo->lastInsertId(); # 最新拿到的 PK

echo json_encode($output, JSON_UNESCAPED_UNICODE);
