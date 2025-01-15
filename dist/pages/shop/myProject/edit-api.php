<?php
/************** 修改api頁面 *****************************************/ 

#管理者才可看到這頁面
// require __DIR__ . '/parts/admin-required.php';

require __DIR__ . '/parts/init.php';
header('content-Type: application/json');

$output = [
  'success' => false, # 有沒有新增成功
  'bodyData' => $_POST, # 除錯的用途
  'code' => 0, # 自訂的編號, 除錯的用途
  'error' => '', # 回應給前端的錯誤訊息
];

/************** 查詢商品id(修改規格頁) api***************************/ 
if (isset($_GET['product_id_check'])) {
  $product_id = intval($_GET['product_id_check']); // 確保是整數
  $sql = "SELECT product_name FROM products WHERE product_id = :product_id";
  $stmt = $pdo->prepare($sql);
  $stmt->execute(['product_id' => $product_id]);
  $row = $stmt->fetch();

  if ($row) {
      echo json_encode(['product_name' => $row['product_name']]);
  } else {
      echo json_encode(['product_name' => '查無此商品']);
  }
  exit;
}
/************** 查詢商品id END*****************/ 




/******************************修改商品 api***************************** */ 
if(!empty($_POST['variant_name']) || !empty($_POST['category']) || !empty($_POST['category_name']) || !empty($_POST['order_id']) || !empty($_POST['promotion_name'])){

/************** 修改商品規格 ***********************/ 
if(!empty($_POST['variant_name'])){
  $sql = "UPDATE `product_variants` SET 
  `product_id`=?,
  `variant_name`=?,
  `price`=?,
  `stock_quantity`=?,
  `image_url`=?
  WHERE `variant_id`=? ";

# ********* TODO: 欄位檢查 *************
  // 檢查有無此產品
  // 1.查詢資料庫是否存在該 product_id
  $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM products WHERE product_id = ?");
  // 2.執行準備好的 SQL 查詢，將變數綁定到 SQL 查詢中的 ? 參數。查詢的結果會保存在 $stmt_check 中。
  $stmt_check->execute([$_POST['product_id']]);
  // 3.獲取結果，使用 fetchColumn 方法取得查詢結果的第一列第一個值。因為 SQL 查詢是 SELECT COUNT(*)，所以返回的值是一個整數，表示符合條件的記錄數。若 product_id 存在於資料表中，$count 會是 1（或更多）；若不存在，$count 會是 0
  $count = $stmt_check->fetchColumn();

  if ($count == 0) {
      // 如果查無此產品，返回錯誤訊息
      $output = [
          'code' => 401, // 自行決定的除錯編號
          'error' => '查無此產品!'
      ];
      echo json_encode($output, JSON_UNESCAPED_UNICODE);
      exit;
  }
  elseif ($_POST['price'] < 0 ) {
    $output['code'] = 401; # 自行決定的除錯編號
    $output['error'] = '請填寫此規格價位!';
    echo json_encode($output, JSON_UNESCAPED_UNICODE);
    exit;
  }
  elseif ($_POST['stock'] < 0 ) {
    $output['code'] = 401; # 自行決定的除錯編號
    $output['error'] = '請填寫此規格的產品庫存!';
    echo json_encode($output, JSON_UNESCAPED_UNICODE);
    exit;
  }

  # ********* TODO END *************
  $stmt = $pdo->prepare($sql);
  $stmt->execute([
    $_POST['product_id'],
    $_POST['variant_name'],
    $_POST['price'],
    $_POST['stock'],
    $_POST['photo'],
    $_POST['variant_id']
  ]);



}

/************** 修改商品 ********************************/ 
elseif(!empty($_POST['category'])){
  $sql = "UPDATE `products` SET 
  `product_name`=?,
  `product_description`=?,
  `price`=?,
  `category_id`=?,
  `product_status`=?,
  `stock_quantity`=?,
  `image_url`=?
  WHERE `product_id`=? ";



  # ********* TODO: 欄位檢查 *************
  
  if (empty($_POST['product_name'])  ) {
    $output['code'] = 401; # 自行決定的除錯編號
    $output['error'] = '沒有填寫產品規格!';
    echo json_encode($output, JSON_UNESCAPED_UNICODE);
    exit;
  }
  elseif (!isset($_POST['category']) ) {
    $output['code'] = 401; # 自行決定的除錯編號
    $output['error'] = '沒有選取商品類別!';
    echo json_encode($output, JSON_UNESCAPED_UNICODE);
    exit;
  }
  elseif (!isset($_POST['product_status']) ) {
    $output['code'] = 401; # 自行決定的除錯編號
    $output['error'] = '沒有選取商品狀態!';
    echo json_encode($output, JSON_UNESCAPED_UNICODE);
    exit;
  }
  elseif (!isset($_POST['price']) ) {
    $output['code'] = 401; # 自行決定的除錯編號
    $output['error'] = '沒有填寫此規格價位!';
    echo json_encode($output, JSON_UNESCAPED_UNICODE);
    exit;
  }
  elseif ($_POST['price']<0  ) {
    $output['code'] = 401; # 自行決定的除錯編號
    $output['error'] = '價位格式錯誤!';
    echo json_encode($output, JSON_UNESCAPED_UNICODE);
    exit;
  }
  elseif (!isset($_POST['stock'])  ) {
    $output['code'] = 401; # 自行決定的除錯編號
    $output['error'] = '沒有填寫庫存!';
    echo json_encode($output, JSON_UNESCAPED_UNICODE);
    exit;
  }
  elseif ($_POST['stock'] < 0 ) {
    $output['code'] = 401; # 自行決定的除錯編號
    $output['error'] = '庫存格式錯誤!';
    echo json_encode($output, JSON_UNESCAPED_UNICODE);
    exit;
  }

  # *** 處理日期
  // if (empty($_POST['birthday'])) {
    //   $birthday = null;
    // } else {
      //   $birthday = strtotime($_POST['birthday']); # 轉換成 timestamp
      //   if ($birthday === false) {
        //     // 如果格式是錯的, 無法轉換
        //     $birthday = null;
        //   } else {
          //     $birthday = date("Y-m-d", $birthday);
          //   }
          // }
  # ********* TODO END *************
          
  $stmt = $pdo->prepare($sql);
  $stmt->execute([
    $_POST['product_name'],
    $_POST['description'],
    $_POST['price'],
    $_POST['category'],
    $_POST['product_status'],
    $_POST['stock'],
    $_POST['photo'],
    $_POST['product_id']
  ]);

}


/************** 修改商品類別********************************/ 
elseif(!empty($_POST['category_name'])){
  $sql = "UPDATE `categories` SET 
  `category_name`=?,
  `category_tag`=?,
  `category_description`=?
  WHERE `category_id`=? ";



  # ********* TODO: 欄位檢查 *************
  
  if (empty($_POST['category_name'])  ) {
    $output['code'] = 401; # 自行決定的除錯編號
    $output['error'] = '沒有填寫類別名稱!';
    echo json_encode($output, JSON_UNESCAPED_UNICODE);
    exit;
  }
  elseif (!isset($_POST['category_tag']) ) {
    $output['code'] = 401; # 自行決定的除錯編號
    $output['error'] = '沒有填寫類別標籤!';
    echo json_encode($output, JSON_UNESCAPED_UNICODE);
    exit;
  }
  
  # ********* TODO END *************
          
  $stmt = $pdo->prepare($sql);
  $stmt->execute([
    $_POST['category_name'],
    $_POST['category_tag'],
    $_POST['description'],
    $_POST['category_id']
  ]);

}


/************** 修改訂單資訊********************************/ 
elseif(!empty($_POST['order_id'])){
  $sql = "UPDATE `orders` SET 
  `recipient_name`=?,
  `recipient_phone`=?,
  `recipient_email`=?,
  `shipping_address`=?
  WHERE `order_id`=? ";



  # ********* TODO: 欄位檢查 *************
  
  if (!isset($_POST['recipient_name'])  ) {
    $output['code'] = 401; # 自行決定的除錯編號
    $output['error'] = '沒有填寫收件者名稱!';
    echo json_encode($output, JSON_UNESCAPED_UNICODE);
    exit;
  }
  elseif (!isset($_POST['recipient_phone']) ) {
    $output['code'] = 401; # 自行決定的除錯編號
    $output['error'] = '沒有填寫收件者電話!';
    echo json_encode($output, JSON_UNESCAPED_UNICODE);
    exit;
  }
  elseif (!isset($_POST['recipient_email']) ) {
    $output['code'] = 401; # 自行決定的除錯編號
    $output['error'] = '沒有填寫收件者信箱!';
    echo json_encode($output, JSON_UNESCAPED_UNICODE);
    exit;
  }
  elseif (!isset($_POST['shipping_address']) ) {
    $output['code'] = 401; # 自行決定的除錯編號
    $output['error'] = '沒有填寫收件地址!';
    echo json_encode($output, JSON_UNESCAPED_UNICODE);
    exit;
  }
  
  # ********* TODO END *************
          
  $stmt = $pdo->prepare($sql);
  $stmt->execute([
    $_POST['recipient_name'],
    $_POST['recipient_phone'],
    $_POST['recipient_email'],
    $_POST['shipping_address'],
    $_POST['order_id'],
  ]);

}



/************** 修改活動資訊********************************/ 
elseif(!empty($_POST['promotion_name'])){
  $sql = "UPDATE `promotions` SET 
  `promotion_name`=?,
  `promotion_description`=?,
  `discount_percentage`=?,
  `start_date`=?,
  `end_date`=?
  WHERE `promotion_id`=? ";



  # ********* TODO: 欄位檢查 *************
  
  if (empty($_POST['start_date']) ) {
    $output['code'] = 401; # 自行決定的除錯編號
    $output['error'] = '沒有填寫開始日期!';
    echo json_encode($output, JSON_UNESCAPED_UNICODE);
    exit;
  }else if(empty($_POST['end_date']) ){
    $output['code'] = 401; # 自行決定的除錯編號
    $output['error'] = '沒有填寫結束日期!';
    echo json_encode($output, JSON_UNESCAPED_UNICODE);
    exit;
  }
  else if(empty($_POST['discount_percentage']) ){
    $output['code'] = 401; # 自行決定的除錯編號
    $output['error'] = '沒有填寫折扣!';
    echo json_encode($output, JSON_UNESCAPED_UNICODE);
    exit;
  }else if($_POST['discount_percentage']<0 || $_POST['discount_percentage']>100){
    $output['code'] = 401; # 自行決定的除錯編號
    $output['error'] = '折扣 % 格式錯誤!';
    echo json_encode($output, JSON_UNESCAPED_UNICODE);
    exit;
  }

  # ********* TODO END *************
          
  $stmt = $pdo->prepare($sql);
  $stmt->execute([
    $_POST['promotion_name'],
        $_POST['description'] ?? null,
        $_POST['discount_percentage'],
        $_POST['start_date'],
        $_POST['end_date'],
        $_POST['promotion_id'],
  ]);

}


$output['success'] = !! $stmt->rowCount(); # 修改了幾筆, 轉布林值




echo json_encode($output, JSON_UNESCAPED_UNICODE);
}