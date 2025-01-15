<?php
require __DIR__ . '/parts/init.php';
$title = "訂單資訊列表";
$pageName = "list_orderItem";



# 取得指定的 PK
$order_id = empty($_GET['order_id']) ? 0 : $_GET['order_id'];

if (empty($order_id)) {
  header('Location: list-order-item.php');
  exit;
}

  # 取資料
  // products
  $sql = "SELECT 
        *
        FROM 
        orders o
        JOIN
        users u
        ON
        o.user_id = u.user_id
        WHERE
        o.order_id = :order_id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['order_id' => $order_id]);
    $rows = $stmt->fetchAll();
  
  # 取得item
  $sql_item = sprintf("SELECT 
    i.product_id,
    i.quantity,
    i.price,
    i.return_status,
    i.returned_quantity,
    p.image_url AS product_img,
    p.product_name,
    v.image_url AS variant_img,
    v.variant_name
    FROM 
    order_items i
    JOIN
    products p
    ON
    i.product_id =p.product_id 
    LEFT JOIN
    product_variants v
    ON
    i.variant_id =v.variant_id 
    WHERE
    i.order_id = :order_id
    ",
    $order_id
  );
  $stmt_item = $pdo->prepare($sql_item);
  $stmt_item->execute(['order_id' => $order_id]);
  $rows_item = $stmt_item->fetchAll();


  
  





?>
<?php include __DIR__ . '/parts/html-head.php' ?>
<?php include __DIR__ . '/parts/html-navbar.php' ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    .non_img{
      width: 100px;
      height: 100px;
      background-color:gainsboro;
      color:floralwhite;
      text-align: center;
      line-height: 100px;
    }

    .table-fix  {
        table-layout: fixed; /* 強制固定表格佈局 */
        width: 100%; /* 表格寬度占滿容器 */
    }
    .table-fix  th,
    .table-fix  td {
        overflow: hidden; /* 如果內容過多，隱藏溢出的部分 */
        /* white-space: nowrap; 不換行 */
    }
    .table th,
    .table td {
        text-align: center;
    }
    h5.text-center{
      margin-top: 50px;
      font-weight: bolder;
    }
  </style>
</head>
<body>
  
  
  <div class="container">
    <div class="row">
      <div class="col">
        <!-- <?php foreach ($rows as $rows): ?> -->
        <a href="./edit-order.php?order_id=<?= $rows['order_id'] ?>" class="btn btn-outline-secondary mt-3">修改收件人/運送資訊</a>
        <!-- 訂單資訊 -->
        <table class="table table-bordered my-3">
            <thead>
            <h5 class="text-center">【訂單資訊】</h5>
            <tr class="list-title">
              <th>訂單編號</th>
              <th>買家</th>
              <th>金額</th>
              <th>訂單狀態</th>
              <th>支付方式</th>
              <th>發票</th>
              <th>訂單備註</th>
            </tr>
          </thead>
          <tbody id="order-accordion">
            
              <tr class="list-order">
                <td><?= $rows['order_id'] ?></td>
                <td><?= htmlentities($rows['user_name']) ?></td>
                <td><?= $rows['total_price'] ?></td>
                <td><?= htmlentities($rows['order_status']) ?></td>
                <td><?= htmlentities($rows['payment_method']) ?></td>
                <td>
                  <?php if (!empty($rows['invoice'] )):?>
                  <?= htmlentities($rows['invoice']) ?>
                  <?php endif?>
                </td>
                <td><?= htmlentities($rows['remark']) ?></td>
              </tr>
          </tbody>
        </table>
        <!-- 收件人資訊 -->
        <table class="table table-fix table-bordered  mb-3">
          <h5 class="text-center">【收件人資訊】</h5>
          <thead>
            <tr class="list-title">
              <th>收件人姓名</th>
              <th>收件人電話</th>
              <th>收件人信箱</th>
            </tr>
          </thead>
          <tbody id="order-accordion">
            <tr>
              <td><?= htmlentities($rows['recipient_name']) ?></td>
              <td><?= htmlentities($rows['recipient_phone']) ?></td>
              <td><?= htmlentities($rows['recipient_email']) ?></td>
            </tr>
          </tbody>
        </table>
        <!-- 運送資訊 -->
        <table class="table table-fix table-bordered mb-3">
          <h5 class="text-center">【運送資訊】</h5>
          <thead>
            <tr class="list-title">
              <th>追蹤編號</th>
              <th>運送方式</th>
              <th>運送地址 / 門市</th>
            </tr>
          </thead>
          <tbody id="order-accordion">
            <tr class="list-order">
                <td>
                <?php echo !empty($rows['tracking_number']) ? htmlentities($rows['tracking_number']) : '--'; ?>
                </td>  
                <td><?= htmlentities($rows['shipping_method']) ?></td>
                <td><?= htmlentities($rows['shipping_address']) ?></td>
                
              </tr>
          </tbody>
        </table>
  
        <!-- 時間資訊 -->
        <table class="table table-fix table-bordered ">
        <h5 class="text-center">【時間資訊】</h5>
        <thead>
            <tr class="list-title">
              <th>發貨時間</th>
              <th>創建時間</th>
              <th>完成時間</th>
              <th>更新時間</th>
            </tr>
          </thead>
          <tbody id="order-accordion">
              <tr class="list-order">
                <td>
                  <?php echo !empty($rows['shipped_at']) ? htmlentities($rows['shipped_at']) : '--'; ?>
                </td>
                <td><?= htmlentities($rows['created_at']) ?></td>
                <td>
                  <?php echo !empty($rows['finish_at']) ? htmlentities($rows['finish_at']) : '--'; ?>
                </td>
                <td><?= htmlentities($rows['updated_at']) ?></td>
              </tr>
          </tbody>
        </table>
        <!-- 商品資訊 -->
        <table class="table table-bordered ">
        <h5 class="text-center">【商品】
        </h5>
          <thead>
            <tr class="list-title">
              <th>編號</th>
              <th>照片</th>
              <th>商品</th>
              <th>規格</th>
              <th>數量</th>
              <th>單價</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($rows_item as $i): ?>
              <tr class="list-product">
                <td><?= $i['product_id'] ?></td>
                <td>
                  <?php if (!empty($i['variant_img'])): ?>
                    <img src="<?= $i['variant_img'] ?>" alt="" width="100px">
                  <?php elseif(!empty($i['product_img'])): ?>
                    <img src="<?= $i['product_img'] ?>" alt="" width="100px">
                  <?php else:?>
                    <div class="non_img ">無照片</div>
                  <?php endif; ?>
                </td>
                <td><?= htmlentities($i['product_name']) ?></td>
                <td>
                  <?php if (!empty($i['variant_name'])): ?>
                    <?= htmlentities($i['variant_name']) ?>
                  <?php endif; ?>
                </td>
                <td><?= $i['quantity'] ?></td>
                <td><?= $i['price'] ?></td>
              </tr>
              
            <?php endforeach; ?>
          </tbody>
        </table>
        
        <!-- <?php endforeach?> -->
      </div>
    </div>
  
      
  
  </div>
  
  <?php include __DIR__ . '/parts/html-scripts.php' ?>
  <script>
    const deleteOne = e => {
      e.preventDefault(); // 沒有要連到某處
      const tr = e.target.closest('tr');
      const [
        ,
        td_product_id,
        td_product_name,
        td_add_variant,
        td_category_tag,
        td_product_intro,
        td_product_price,
        td_product_stock,
        td_product_status,
        ,
        ,
        ,
      ] = tr.querySelectorAll('td');
      const product_id = td_product_id.innerHTML;
      const product_name = td_product_name.innerHTML;
      const category_tag = td_category_tag.innerHTML;
      console.log([td_product_id.innerHTML, product_name.innerHTML]);
      if (confirm(`是否要刪除編號 ${product_id} 的商品【 ${product_name} 】?`)) {
        // 使用 JS 做跳轉頁面
        location.href = `del.php?product_id=${product_id}`;
      }
    }
    const deleteVariant = e => {
      e.preventDefault(); // 沒有要連到某處
  
  
      const tr = e.target.closest('tr');
      const [
        , //delete
        td_product_name,
        td_variant_id,
        ,
        ,
        td_variant_name,
        ,
        ,
        td_variant_price,
        td_variant_stock,
        td_variant_img,
        ,
        ,
        ,
        , //edit 
      ] = tr.querySelectorAll('td');
      const product_name = td_product_name.innerHTML;
      const variant_id = td_variant_id.innerHTML;
      const variant_name = td_variant_name.innerHTML;
      if (confirm(`是否要刪除商品 ${product_name} 的規格 【 ${variant_name} 】 ?`)) {
        // 使用 JS 做跳轉頁面
        location.href = `del.php?variant_id=${variant_id}`;
      }
    }
    /*
    const deleteOne = ab_id => {
      if (confirm(`是否要刪除編號為 ${ab_id} 的資料?`)) {
        // 使用 JS 做跳轉頁面
        location.href = `del.php?ab_id=${ab_id}`;
      }
    }
    */
  </script>
</body>
</html>
<?php include __DIR__ . '/parts/html-tail.php' ?>