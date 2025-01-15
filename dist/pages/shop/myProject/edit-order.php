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
    .title-top{
      margin-top: 20px;
      display: flex;
      justify-content: space-between;
    }
    
    form .td.error input.form-control {
      border: 2px solid red;
    }
    

    form .td.error .form-text {
      display: block;
      color: red;
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
        <form onsubmit="sendData(event)">
          <div class="title-top">
            <div>
              <h3 >修改訂單資訊</h3>
            </div>
            <div>
              <button type="submit" class="btn btn-primary">確定修改</button>
            </div>
          </div>
          <?php foreach ($rows as $rows): ?>
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
                <input class="form-control"  type="hidden" name="order_id" id="order_id" value="<?= htmlentities($rows['order_id']) ?>">
                <tr class="list-order">
                  <td><?= htmlentities($rows['order_id']) ?></td>
                  <td><?= htmlentities($rows['user_name']) ?></td>
                  <td><?= $rows['total_price'] ?></td>
                  <td><?= htmlentities($rows['order_status']) ?></td>
                  <td><?= htmlentities($rows['payment_method']) ?></td>
                  <td>
                    <?= htmlentities($rows['invoice_method']) ?> 
                    <?php if (!empty($rows['invoice'] )):?>
                    ( <?= htmlentities($rows['invoice']) ?> )
                    <?php endif?>
                  </td>
                  <td><?= htmlentities($rows['remark']) ?></td>
                </tr>
            </tbody>
          </table>
          <!-- 收件人資訊 -->
          <table class="table table-fix table-bordered  td">
            <h5 class="text-center">【【收件人資訊】</h5>
            <thead>
              <tr class="list-title">
                <th>收件人姓名</th>
                <th>收件人電話</th>
                <th>收件人信箱</th>
              </tr>
            </thead>
            <tbody id="order-accordion">
              <tr>
                <td class="td">
                  <input  class="form-control" id="recipient_name" name="recipient_name" type="text" value="<?= htmlentities($rows['recipient_name']) ?>" placeholder="<?= htmlentities($rows['recipient_name']) ?>"> 
                  <div class="form-text"></div>
                </td>
                <td class="td">
                  <input class="form-control"  id="recipient_phone" name="recipient_phone" type="text" value="<?= htmlentities($rows['recipient_phone']) ?>" placeholder="<?= htmlentities($rows['recipient_phone']) ?>">
                  <div class="form-text"></div> 
                </td>
                <td class="td">
                  <input type="email" class="form-control"  name="recipient_email" id="recipient_email" value="<?= htmlentities($rows['recipient_email']) ?>" placeholder="<?= htmlentities($rows['recipient_email']) ?>" >
                  <div class="form-text"></div>
                </td>
              </tr>
            </tbody>
          </table>
          <!-- 運送資訊 -->
          <table class="table table-fix table-bordered td">
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
                  <td class="td">
                    <input  class="form-control" type="text" name="shipping_address" id="shipping_address" value="<?= htmlentities($rows['shipping_address']) ?>" placeholder="<?= htmlentities($rows['shipping_address']) ?>"> 
                    <div class="form-text"></div>
                  </td>
                  
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
          
        </form>
      </div>
    </div>
    
    <!-- Modal -編輯結果-->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">編輯結果</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="alert alert-success" role="alert">
          資料編輯成功
        </div>
      </div>
      <div class="modal-footer">
        <a href="list-orderItem.php?order_id=<?= htmlentities($rows['order_id'])  ?>" class="btn btn-secondary" >關閉</a>
      </div>
    </div>
  </div>
</div>
<?php endforeach?>

  </div>
  
  <?php include __DIR__ . '/parts/html-scripts.php' ?>
  <script>
    const recipient_nameField = document.querySelector('#recipient_name');
    const recipient_phoneField = document.querySelector('#recipient_phone');
    const recipient_emailField = document.querySelector('#recipient_email');
    const shipping_addressField = document.querySelector('#shipping_address');
    const myModal = new bootstrap.Modal('#exampleModal');

    const sendData = e => {
      e.preventDefault(); // 不要讓表單以傳統的方式送出
      console.log(recipient_nameField);
      
      recipient_nameField.closest('.td').classList.remove('error');
      recipient_phoneField.closest('.td').classList.remove('error');
      recipient_emailField.closest('.td').classList.remove('error');
      shipping_addressField.closest('.td').classList.remove('error');
      
    let isPass = true; // 有沒有通過檢查, 預設值是 true
    // TODO: 資料欄位的檢查
    
    const regex = /^\+?[0-9]{7,15}$/;

    if (!recipient_nameField.value) {
      isPass = false;
      recipient_nameField.nextElementSibling.innerHTML = `<i class="fa-solid fa-triangle-exclamation"></i> 請填寫收件人名稱`;
      recipient_nameField.closest('.td').classList.add('error');
    }
    if (!recipient_emailField.value) {
      isPass = false;
      recipient_emailField.nextElementSibling.innerHTML = `<i class="fa-solid fa-triangle-exclamation"></i> 請填寫收件人名稱`;
      recipient_emailField.closest('.td').classList.add('error');
    }
    if (!recipient_phoneField.value) {
      isPass = false;
      recipient_phoneField.nextElementSibling.innerHTML = `<i class="fa-solid fa-triangle-exclamation"></i> 請填寫收件人電話`;
      recipient_phoneField.closest('.td').classList.add('error');
    }else if(!regex.test(recipient_phoneField.value)){
      isPass = false;
      recipient_phoneField.nextElementSibling.innerHTML = `<i class="fa-solid fa-triangle-exclamation"></i> 請填寫正確號碼`;
      recipient_phoneField.closest('.td').classList.add('error');
    }
    if (!shipping_addressField.value) {
      isPass = false;
      shipping_addressField.nextElementSibling.innerHTML = `<i class="fa-solid fa-triangle-exclamation"></i> 請填寫收件地址`;
      shipping_addressField.closest('.td').classList.add('error');
    }


    if (isPass) {
      const fd = new FormData(document.forms[0]);

      fetch(`edit-api.php`, {
          method: 'POST',
          body: fd
        }).then(r => r.json())
        .then(obj => {
          console.log(obj);
          if (obj.success) {
            myModal.show(); // 呈現 modal
          } else {
            alert('資料沒有修改')
          }

        }).catch(console.warn);
    }


  }
  </script>
</body>
</html>
<?php include __DIR__ . '/parts/html-tail.php' ?>