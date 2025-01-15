<?php
require __DIR__ . '/parts/init.php';
$title = "商品列表";
$pageName = "list_promotion";

$perPage = 50; # 每一頁有幾筆

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
  $where .= " AND ( promotion_name LIKE $keyword_ ) ";
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
              promotions 
          LEFT JOIN 
              promotion_products
          ON 
              promotions.promotion_id = promotion_products.promotion_id 
            
          $where";

# 總筆數
$totalRows = $pdo->query($t_sql)->fetch(PDO::FETCH_NUM)[0];
# 總頁數
$totalPages = ceil($totalRows / $perPage);
$promotionows = []; # 設定預設值
if ($totalRows > 0) {
  if ($page > $totalPages) {
    # 用戶要看的頁碼超出範圍, 跳到最後一頁
    header('Location: ?page=' . $totalPages);
    exit;
  }

  // 排序
  $orderBy = 'promotion_id  DESC';
  
  // 定義允許的排序欄位
  $allowedOrderBy = [
    'promotion_id  DESC',
    'promotion_id',
    'start_date DESC',
    'start_date',
    'end_date DESC',
    'end_date'
  ];
  // 驗證 orderBy 是否為允許的選項
  if (!empty($_GET['orderBy']) && in_array($_GET['orderBy'], $allowedOrderBy)) {
  $orderBy = $_GET['orderBy'];
  }



 


  # 取第一頁的資料
//   $sql = sprintf("SELECT 
//     *
//     FROM 
//         promotions
//     %s
//     ORDER BY 
//         $orderBy
//     LIMIT %d, %d",
//     $where,
//     ($page - 1) * $perPage,
//     $perPage
// );
// $promotionows = $pdo->query($sql)->fetchAll(); # 取得該分頁的資料


//   $sql_p = sprintf("SELECT 
//     COUNT(*)
//     FROM 
//         promotion_products AS product
//     ");
    
//   $promotionows_p = $pdo->query($sql_p)->fetchAll(); # 取得該分頁的資料


/***************************************** */
$sql = sprintf("SELECT 
    promotions.*, 
    promotion_products.product_id, 
    promotion_products.variant_id, 
    promotion_products.category_id
    FROM 
        promotions
    LEFT JOIN 
        promotion_products
    ON 
        promotions.promotion_id = promotion_products.promotion_id
    %s
    ORDER BY 
        $orderBy
    LIMIT %d, %d",
    $where,
    ($page - 1) * $perPage,
    $perPage
);
$rows = $pdo->query($sql)->fetchAll(); # 取得該分頁的資料


$lastPromotionId = null;  // 用來存儲上次顯示的促銷活動 ID
$promotionData = [];      // 用來儲存促銷活動與搭配商品的資料

// 首先整理促銷活動資料
foreach ($rows as $r) {
    if ($r['promotion_id'] != $lastPromotionId) {
        $lastPromotionId = $r['promotion_id'];
        // 收集該促銷活動的資料
        $promotionData[$r['promotion_id']] = [
            'promotion_name' => $r['promotion_name'],
            'promotion_description' => $r['promotion_description'],
            'promotion_id' => $r['promotion_id'], // 儲存 promotion_id
            'start_date' => $r['start_date'],
            'end_date' => $r['end_date'],
            'updated_at' => $r['updated_at'],
            'has_products' => false, // 標記是否有搭配商品、變體或類別
        ];
    }

    // 檢查是否有搭配商品、變體或類別，並標記
    if ($r['product_id'] || $r['variant_id'] || $r['category_id']) {
        $promotionData[$r['promotion_id']]['has_products'] = true;
    }
}

/***************************************** */

}


?>
<?php include __DIR__ . '/parts/html-head.php' ?>
<?php include __DIR__ . '/parts/html-navbar.php' ?>

<div class="container">
  <div class="row mt-2 mb-2">
    <!-- 排序選單 *有空再用-->
    
    <div class="col-6 d-flex">
      <a href="./add-promotion.php" class="btn btn-outline-secondary me-3" >新增活動</a>
      <form action=""  method="GET">
        <select name="orderBy" id="orderBy" onchange="this.form.submit()" class="form-select">
          <option value="promotion_id DESC" <?php echo isset($_GET['orderBy']) &&  $_GET['orderBy']=='promotion_id  DESC'  ? 'selected':'';?>>最新 (排序)</option>
          <option value="promotion_id" <?php echo isset($_GET['orderBy']) &&  $_GET['orderBy']=='promotion_id '   ? 'selected':''?>>最舊</option>
          <option value="start_date DESC" <?php echo isset($_GET['orderBy']) &&  $_GET['orderBy']=='start_date DESC'   ? 'selected':''?>>活動開始時間</option>

          <option value="end_date DESC" <?php echo isset($_GET['orderBy']) &&  $_GET['orderBy']=='end_date DESC'   ? 'selected':''?>>活動結束時間</option>

          <option value="updated_at" <?php echo isset($_GET['orderBy']) &&  $_GET['orderBy']=='updated_at DESC'   ? 'selected':''?>>最近更新</option>

        </select>
      </form>
    </div>
    <!-- 搜尋框 -->
    <div class="col-6">
      
      <form class="d-flex" role="search">
        <input class="form-control me-2" name="keyword"
          value="<?= empty($_GET['keyword']) ? '' : htmlentities($_GET['keyword']) ?>" type="search"
          placeholder="活動名稱" aria-label="Search">
        <button class="btn btn-outline-success" type="submit">Search</button>
      </form>
    </div>
  </div>


  <div class="row">
    <div class="col">
      <table class="table table-bordered ">
        <thead>
          <tr class="list-title">
            <th><i class="fa-solid fa-trash"></i></th>
            <th>活動編號</th>
            <th>活動名稱</th>
            <th>描述</th>
            <th>搭配商品</th>
            <!-- <th>狀態</th> -->
            <th>開始日期</th>
            <th>結束日期</th>
            <th>更新時間</th>
            <th><i class="fa-solid fa-pen-to-square"></i></th>
          </tr>
        </thead>
        <tbody>

<?php foreach ($promotionData as $promotion):?>
  <tr class="list-promotion">
        <td>
          <a href="javascript:" onclick="deleteOne(event)">
            <i class="fa-solid fa-trash"></i>
          </a>
        </td>
        <td><?= $promotion['promotion_id'] ?></td> 
        <td><?= $promotion['promotion_name'] ?></td>
        <td><?= $promotion['promotion_description'] ?></td>
        <td>
          <?php if ($promotion['has_products']): ?>
              <a href="edit-promotionProducts.php?promotion_id=<?= $promotion['promotion_id'] ?>">查看</a>
          <?php else: ?>
            <a href="add-promotionProducts.php?promotion_id=<?= $promotion['promotion_id'] ?>"><i class="fa-solid fa-square-plus"></i></a>
          <?php endif; ?>
        </td>
        <td><?= $promotion['start_date'] ?></td>
        <td><?= $promotion['end_date'] ?></td>
        <td><?= $promotion['updated_at'] ?></td>
        <td>
            <a href="edit-promotion.php?promotion_id=<?= $promotion['promotion_id'] ?>">
                <i class="fa-solid fa-pen-to-square"></i>
            </a>
        </td>

        <!-- 顯示搭配商品、變體或類別 -->
        <!-- <?php foreach ($promotion['products'] as $product): ?>
            <td>
                <a href="edit-promotionProducts.php?promotion_id=<?= $promotion['promotion_id'] ?>&product_id=<?= $product['product_id'] ?>">查看商品</a>
            </td>
            <td>
                <a href="edit-promotionProducts.php?promotion_id=<?= $promotion['promotion_id'] ?>&variant_id=<?= $product['variant_id'] ?>">查看變體</a>
            </td>
            <td>
                <a href="edit-promotionProducts.php?promotion_id=<?= $promotion['promotion_id'] ?>&category_id=<?= $product['category_id'] ?>">查看類別</a>
            </td>
        <?php endforeach; ?> -->
    </tr>
<?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

    <!-- 頁碼 -->
  <div class="row mt-2">
    <div class="col"></div>
    <div class="col">
      <?php
      $qs = array_filter($_GET); # 去除值是空字串的項目
      ?>
      <nav aria-label="Page navigation example">
        <ul class="pagination">
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
  </div>

</div>

<?php include __DIR__ . '/parts/html-scripts.php' ?>
<script>
  const deleteOne = e => {
    e.preventDefault(); // 沒有要連到某處
    const tr = e.target.closest('tr');
    const [
      ,
      td_promotion_id ,
      td_promotion_name,
      td_add_variant,
      td_category_tag,
      td_promotion_intro,
      td_promotion_price,
      td_promotion_stock,
      td_promotion_status,
      ,
      ,
      ,
    ] = tr.querySelectorAll('td');
    const promotion_id  = td_promotion_id .innerHTML;
    const promotion_name = td_promotion_name.innerHTML;
    const category_tag = td_category_tag.innerHTML;
    console.log([td_promotion_id .innerHTML, promotion_name.innerHTML]);
    if (confirm(`是否要刪除編號 ${promotion_id } 的商品【 ${promotion_name} 】?`)) {
      // 使用 JS 做跳轉頁面
      location.href = `del.php?promotion_id =${promotion_id }`;
    }
  }
  const deleteVariant = e => {
    e.preventDefault(); // 沒有要連到某處


    const tr = e.target.closest('tr');
    const [
      , //delete
      td_promotion_name,
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
    const promotion_name = td_promotion_name.innerHTML;
    const variant_id = td_variant_id.innerHTML;
    const variant_name = td_variant_name.innerHTML;
    if (confirm(`是否要刪除商品 ${promotion_name} 的規格 【 ${variant_name} 】 ?`)) {
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