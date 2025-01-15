<?php
require __DIR__ . '/parts/init.php';
$title = "商品列表";
$pageName = "list_product";

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
            Products p
          JOIN 
            Categories c
          ON 
            c.category_id = p.category_id 
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

  // 排序
  $orderBy = 'p.product_id DESC';
  
  // 定義允許的排序欄位
  $allowedOrderBy = [
    'p.product_id DESC',
    'p.product_id',
    'p.product_name',
    'c.category_tag',
    'p.price DESC',
    'p.price',
    'p.updated_at DESC'
  ];
  // 驗證 orderBy 是否為允許的選項
  if (!empty($_GET['orderBy']) && in_array($_GET['orderBy'], $allowedOrderBy)) {
  $orderBy = $_GET['orderBy'];
  }

  # 取第一頁的資料
  # 取第一頁的資料
  // products
  $sql = sprintf("SELECT 
    c.category_id,
    c.category_tag,
    p.product_id,
    p.product_name,
    p.price AS product_price,
    p.stock_quantity AS product_stock,
    p.product_description,
    p.product_status,
    p.image_url,
    p.created_at,
    p.updated_at
    FROM 
        Products p
    JOIN 
        Categories c
    ON 
        c.category_id = p.category_id
    %s
    ORDER BY $orderBy  LIMIT %d, %d
    "
    ,
    $where,
    ($page - 1) * $perPage,
    $perPage
  );
  $rows = $pdo->query($sql)->fetchAll(); # 取得該分頁的資料

  // 變體
  $sql_v = "SELECT 
    product_id,
    variant_id,
    variant_name,
    price AS variant_price,
    stock_quantity AS variant_stock,
    image_url AS variant_img
    FROM 
        Product_Variants
    ORDER BY product_id ";
  $rows_v = $pdo->query($sql_v)->fetchAll(); # 取得該分頁的資料
}


?>
<?php include __DIR__ . '/parts/html-head.php' ?>
<?php include __DIR__ . '/parts/html-navbar.php' ?>


<div class="container">
  <div class="row mt-2 mb-2">
    <!-- 排序選單 -->
    
    <div class="col-6 d-flex">
      <a href="./add-product.php" class="btn btn-outline-secondary me-3" >新增商品</a>
      <form action=""  method="GET">
        <select name="orderBy" id="orderBy" onchange="this.form.submit()" class="form-select">
          <option value="p.product_id DESC" <?php echo isset($_GET['orderBy']) &&  $_GET['orderBy']=='p.product_id DESC'  ? 'selected':'';?>>最新 (排序)</option>
          <option value="p.product_id" <?php echo isset($_GET['orderBy']) &&  $_GET['orderBy']=='p.product_id'   ? 'selected':''?>>最舊</option>
          <option value="p.product_name" <?php echo isset($_GET['orderBy']) &&  $_GET['orderBy']=='p.product_name'   ? 'selected':''?>>名稱</option>
          <option value="c.category_tag" <?php echo isset($_GET['orderBy']) &&  $_GET['orderBy']=='c.category_tag'  ? 'selected':''?>>類別
          </option>
          <option value="p.price DESC" <?php echo isset($_GET['orderBy']) &&  $_GET['orderBy']=='p.price DESC'   ? 'selected':''?>>價格(高->低)</option>
          <option value="p.price" <?php echo isset($_GET['orderBy']) &&  $_GET['orderBy']=='p.price'  ? 'selected':''?>>價格(低->高)</option>
          <option value="p.updated_at DESC" <?php echo isset($_GET['orderBy']) &&  $_GET['orderBy']=='p.updated_at  DESC'  ? 'selected':''?>>最近更新</option>
          
        </select>
      </form>
    </div>
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
            <th><i class="fa-solid fa-trash"></i></th>
            <th>編號</th>
            <th>商品</th>
            <th>規格</th>
            <th>類別</th>
            <th>介紹</th>
            <th>價格</th>
            <th>庫存</th>
            <th>狀態</th>
            <th>照片</th>
            <!-- <th>照片</th> -->
            <th>創建時間</th>
            <th>更新時間</th>
            <th><i class="fa-solid fa-pen-to-square"></i></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($rows as $r): ?>
            <tr class="list-product">
              <td>
                <a href="javascript:" onclick="deleteOne(event)">
                  <i class="fa-solid fa-trash"></i>
                </a>
              </td>
              <td><?= $r['product_id'] ?></td>
              <td><?= htmlentities($r['product_name']) ?></td>
              <td><a href="add-variant.php?product_id=<?= $r['product_id'] ?>"><i class="fa-solid fa-square-plus"></i></a></td>
              <td><?= $r['category_tag'] ?></td>
              <td><?= htmlentities($r['product_description']) ?></td>
              <td><?= $r['product_price'] ?></td>
              <td>
                <?php if (array_filter($rows_v, fn($v) => $v['product_id'] === $r['product_id'])): ?>
                  --
                <?php else:
                  echo $r['product_stock'] ?>
                <?php endif ?>
              </td>
              <td><?= htmlentities($r['product_status']) ?></td>
              <!-- <td><?= htmlentities($r['image_url']) ?></td> -->
              <td>
                <?php if (!empty($r['image_url'])): ?>
                  <img src="<?= $r['image_url'] ?>" alt="" width="100px">
                <?php endif; ?>
              </td>
              
              <td><?= $r['created_at'] ?></td>
              <td><?= $r['updated_at'] ?></td>
              <td>
                <a href="edit-product.php?product_id=<?= $r['product_id'] ?>">
                  <i class="fa-solid fa-pen-to-square"></i>
                </a>
              </td>
            </tr>
            
            <!-- 變體 -->
            <?php foreach ($rows_v as $v):
              if ($v['product_id'] === $r['product_id']): ?>
                <tr class="list-variant">
                  <td>
                    <a href="javascript:" onclick="deleteVariant(event)">
                      <i class="fa-solid fa-trash text-warning"></i>
                    </a>
                  </td>
                  <td hidden><?= $r['product_name'] ?></td>
                  <td hidden><?= $v['variant_id'] ?></td>
                  <td></td>
                  <td></td>
                  <td><?= htmlentities($v['variant_name']) ?></td>
                  <td></td>
                  <td></td>
                  <td><?= $v['variant_price'] ?></td>
                  <td><?= $v['variant_stock'] ?></td>
                  <td></td>
                  <?php if (!empty($v['variant_img'])): ?>
                    <td>
                      <img src="<?= $v['variant_img'] ?>" alt="" width="100px">
                    </td>
                  <?php endif; ?>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td>
                    <a href="edit-variant.php?variant_id=<?= $v['variant_id'] ?>">
                      <i class="fa-solid fa-pen-to-square text-warning"></i>
                    </a>
                  </td>
                </tr>
              <?php endif; endforeach; ?>

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