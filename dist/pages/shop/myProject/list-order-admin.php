<?php
require __DIR__ . '/parts/init.php';
$title = "商品訂單列表";
$pageName = "list_order";

$perPage = 5; # 每一頁有幾筆

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
if ($page < 1) {
  header('Location: ?page=1'); # 跳轉頁面 (後端), 也稱為 redirect (轉向)
  exit; # 離開 (結束) 程式 (以下的程式都不會執行)
  //die(); # 同 exit 的功能, 但可以回傳字串或編號
}

$keyword = empty($_GET['keyword']) ? '' : $_GET['keyword'];
$birth_begin = empty($_GET['birth_begin']) ? '' : $_GET['birth_begin'];
$birth_end = empty($_GET['birth_end']) ? '' : $_GET['birth_end'];

// 搜尋關鍵字
$where = ' WHERE 1 '; # SQL 條件的開頭

if ($keyword) {
  $keyword_ = $pdo->quote("%{$keyword}%"); # 字串內容做 SQL 引號的跳脫, 同時前後標單引號
  $where .= " AND ( product_name LIKE $keyword_ OR category_tag LIKE $keyword_ OR product_id LIKE $keyword_  OR product_description LIKE $keyword_ ) ";
}


// if ($birth_begin) {
//   $t = strtotime($birth_begin); # 把日期字串轉換成 timestamp
//   if ($t !== false) {
//     $where .= sprintf(" AND birthday >= '%s' ",   date('Y-m-d', $t));
//   }
// }
// if ($birth_end) {
//   $t = strtotime($birth_end); # 把日期字串轉換成 timestamp
//   if ($t !== false) {
//     $where .= sprintf(" AND birthday <= '%s' ",   date('Y-m-d', $t));
//   }
// }

$t_sql = "SELECT COUNT(1) 
          FROM  
            orders 
          $where";

# 總筆數
$totalRows = $pdo->query($t_sql)->fetch(PDO::FETCH_NUM)[0];
# 總頁數
$totalPages = ceil($totalRows / $perPage);
$rows = []; # 設定預設值
if ($totalRows > 0) {
  if ($page > $totalPages) {
    # 用戶要看的頁碼超出範圍, 跳到最後一頁
    header('Location: ?page=' . $totalPages);
    exit;
  }


  # 取第一頁的資料
  # 取第一頁的資料
  // products
  $sql = sprintf("SELECT 
    *
    FROM 
    orders o
    JOIN
    users u
    ON
    o.user_id=u.user_id
    %s 
    ORDER BY created_at DESC
    LIMIT %d, %d
    "
    ,
    $where,
    ($page - 1) * $perPage,
    $perPage
  );
  $rows = $pdo->query($sql)->fetchAll(); # 取得該分頁的資料

  // 點及查看
  $orderId = empty($_GET['orderId']) ? '' : $_GET['orderId'];
  $where_id = ' WHERE 1 '; # SQL 條件的開頭
  if ($orderId) {
    $orderId = $pdo->quote("%{$orderId}%"); # 字串內容做 SQL 引號的跳脫, 同時前後標單引號
    $where_id .= " AND order_id = $orderId ";
    $sql_v = sprintf("SELECT 
      *
      FROM 
      orders
      %s 
      "
      ,
      $where_id
    );
    $rows_v = $pdo->query($sql_v)->fetchAll(); # 取得該分頁的資料
  }
  
}else{
  echo "目前沒有訂單資料";
}




?>
<?php include __DIR__ . '/parts/html-head.php' ?>
<?php include __DIR__ . '/parts/html-navbar.php' ?>


<div class="container">
  <div class="row mt-2 mb-2">
    <div class="col-6 d-flex"></div>
    <!-- 搜尋框 -->
    <div class="col-6">
      
      <form class="d-flex" role="search">
        <input class="form-control me-2" name="keyword"
          value="<?= empty($_GET['keyword']) ? '' : htmlentities($_GET['keyword']) ?>" type="search"
          placeholder="編號/名稱/類別/介紹" aria-label="Search">
        <button class="btn btn-outline-success" type="submit">Search</button>
      </form>
    </div>
  </div>


  <div class="row">
    <div class="col">
      <table class="table table-bordered ">
        <thead>
          <tr class="list-title">
            <th><i class="fa-solid fa-list"></i></th>
            <th>訂單編號</th>
            <th>買家</th>
            <th>狀態</th>
            <th>金額</th>
            <th>支付方式</th>
            <!-- 發票 -->
            <th>發票</th>
            <!-- 收件人 -->
            <th>收件人資訊</th>
            <th>備註</th>
            <!-- 運送 -->
            <th>運送資訊</th>
            <!-- 時間 -->
            <!-- <th>發貨時間</th> -->
            <th>創建時間</th>
            <!-- <th>完成時間</th> -->
            <th>更新時間</th>
            <th><i class="fa-solid fa-pen-to-square"></i></th>
          </tr>
        </thead>
        <tbody id="order-accordion">
          <?php foreach ($rows as $r): ?>
            <tr class="list-order">
              <td>
                <a href="list-orderItem.php?order_id=<?= $r['order_id'] ?>">
                <i class="fa-solid fa-list"></i>
                </a>
              </td>
              <td><?= $r['order_id'] ?></td>
              <td><?= htmlentities($r['user_name']) ?></td>
              <td><?= htmlentities($r['order_status']) ?></td>
              <td><?= $r['total_price'] ?></td>
              <td><?= htmlentities($r['payment_method']) ?></td>
              <td>
                <?php if (!empty($r['invoice'] )):?>
                <?= htmlentities($r['invoice']) ?>
                <?php endif?>
              </td>
              <td>
                <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#recipient-<?= $r['order_id'] ?>" aria-expanded="false" aria-controls="recipient-<?= $r['order_id'] ?>">
                  查看
                </button>
              </td>
              <td><?= htmlentities($r['remark']) ?></td>
              <td>
                <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#shipping-<?= $r['order_id'] ?>" aria-expanded="false" aria-controls="shipping-<?= $r['order_id'] ?>">
                  查看
                </button>
              </td>
              <!-- <td><?= htmlentities($r['shipped_at']) ?></td> -->
              <td><?= htmlentities($r['created_at']) ?></td>
              <!-- <td><?= htmlentities($r['finish_at']) ?></td> -->
              <td><?= htmlentities($r['updated_at']) ?></td>
              <td>
                <a href="./edit-order.php?order_id=<?= $r['order_id'] ?>">
                  <i class="fa-solid fa-pen-to-square"></i>
                </a>
              </td>
            </tr>
            <!-- 隱藏詳細資訊行 -->
            <tr class="collapse" id="recipient-<?= $r['order_id'] ?>" data-bs-parent="#order-accordion" >
                <td colspan="20"  class="bg-secondary-subtle">
                    <div class="p-3 ">
                        <strong >收件人資訊：</strong>
                        <ul class="mt-2">
                            <li><strong>姓名 : </strong> <?= htmlentities($r['recipient_name']) ?></li>
                            <li><strong>電話 : </strong> <?= htmlentities($r['recipient_phone']) ?></li>
                            <li><strong>信箱 : </strong> <?= htmlentities($r['recipient_email']) ?></li>
                            <!-- 可以添加更多詳細資訊 -->
                        </ul>
                    </div>
                </td>
            </tr>
            <!-- 隱藏詳細資訊行 -->
            <tr class="collapse" id="shipping-<?= $r['order_id'] ?>" data-bs-parent="#order-accordion">
                <td colspan="20"  class="bg-secondary-subtle">
                    <div class="p-3">
                        <strong>運送資訊：</strong>
                        <ul>
                            <li><strong>運送方式 : </strong> <?= htmlentities($r['shipping_method']) ?></li>
                            <li><strong>地址/門市 : </strong> <?= htmlentities($r['shipping_address']) ?></li>
                            <li><strong>追蹤號碼 : </strong> <?= htmlentities($r['tracking_number']) ?></li>
                            <!-- 可以添加更多詳細資訊 -->
                        </ul>
                    </div>
                </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

    <!-- 頁碼 -->
  <div class="row mt-2">
    <!-- <div class="col "></div> -->
    <div class="col ">
      <?php
      $qs = array_filter($_GET); # 去除值是空字串的項目
      ?>
      <nav aria-label="Page navigation example">
        <ul class="pagination d-flex justify-content-center">
          <li class="page-item <?= $page == 1 ? 'disabled' : '' ?>">
            <a class="page-link" href="?<?php $qs['page'] = 1;
            echo http_build_query($qs) ?>">
              <i class="fa-solid fa-angles-left"></i>
            </a>
          </li>
          <li class="page-item <?= $page == 1 ? 'disabled' : '' ?>">
            <a class="page-link" href="?<?php $qs['page'] = $page - 1;
            echo http_build_query($qs) ?>">
              <i class="fa-solid fa-angle-left"></i>
            </a>
          </li>

          <?php for ($i = $page - 5; $i <= $page + 5; $i++):
            if ($i >= 1 and $i <= $totalPages):
              # $qs = array_filter($_GET); # 去除值是空字串的項目
              $qs['page'] = $i;
              ?>
              <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                <a class="page-link" href="?<?= http_build_query($qs) ?>"><?= $i ?></a>
              </li>
            <?php endif;
          endfor; ?>

          <li class="page-item <?= $page == $totalPages ? 'disabled' : '' ?>">
            <a class="page-link" href="?<?php $qs['page'] = $page + 1;
            echo http_build_query($qs) ?>">
              <i class="fa-solid fa-angle-right"></i>
            </a>
          </li>
          <li class="page-item <?= $page == $totalPages ? 'disabled' : '' ?>">
            <a class="page-link" href="?<?php $qs['page'] = $totalPages;
            echo http_build_query($qs) ?>">
              <i class="fa-solid fa-angles-right"></i>
            </a>
          </li>

        </ul>
      </nav>
    </div>
    <!-- <div class="col "></div> -->
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
<?php include __DIR__ . '/parts/html-tail.php' ?>