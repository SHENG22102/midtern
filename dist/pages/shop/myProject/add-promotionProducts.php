<?php
require __DIR__ . '/parts/init.php';
$title = "商品列表";
$pageName = "list_product";


$keyword = empty($_GET['keyword']) ? '' : $_GET['keyword'];

// 搜尋關鍵字
$where = ' WHERE 1 '; # SQL 條件的開頭

if ($keyword) {
  $keyword_ = $pdo->quote("%{$keyword}%"); # 字串內容做 SQL 引號的跳脫, 同時前後標單引號
  $where .= " AND ( product_name LIKE $keyword_ OR category_tag LIKE $keyword_ OR product_id LIKE $keyword_  OR product_description LIKE $keyword_ ) ";
}



$t_sql = "SELECT COUNT(1) 
          FROM  
            Products p
          JOIN 
            Categories c
          ON 
            c.category_id = p.category_id 
          $where";

  # 取第一頁的資料
  # 取第一頁的資料
  // products
  $sql = sprintf("SELECT 
    c.category_id,
    c.category_tag,
    c.category_name,
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
    "
    ,
    $where
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

  $sql_categories = "SELECT DISTINCT c.category_id, c.category_name 
  FROM Categories c
  JOIN Products p ON c.category_id = p.category_id";
$categories = $pdo->query($sql_categories)->fetchAll();

?>
<?php include __DIR__ . '/parts/html-head.php' ?>
<?php include __DIR__ . '/parts/html-navbar.php' ?>


<div class="container">
  <div class="row mt-2 mb-2">
    <!-- 排序選單 -->
    
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
    <!-- 商品類別 -->
    <div class="col-3">
    <div class="list-group">
    <?php foreach ($categories as $category): ?>
      <button type="button" 
              class="list-group-item list-group-item-action" 
              aria-current="true" 
              data-category-id="<?= $category['category_id'] ?>">
        <?= htmlentities($category['category_name']) ?>
      </button>
    <?php endforeach; ?>
</div>

    </div>
    <!-- 商品 -->
    <div class="col">
      <table class="table table-bordered ">
        <thead>
          <tr class="list-title">
            <th>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
              </div>
            </th>
            <th>編號</th>
            <th>商品</th>
            <th>規格</th>
            <th>價格</th>
            <th>庫存</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($rows as $r): ?>
            <tr class="list-product"  data-category-id="<?= $r['category_id'] ?>" data-product-id="<?= $r['product_id'] ?>" >
              <td>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
                </div>
              </td>
              <td><?= $r['product_id'] ?></td>
              <td><?= htmlentities($r['product_name']) ?></td>
              <td>--</td>
              <td><?= $r['product_price'] ?></td>
              <td>
                <?php if (array_filter($rows_v, fn($v) => $v['product_id'] === $r['product_id'])): ?>
                  <!-- 所有變體的庫存加總 -->--
                <?php else:
                  echo $r['product_stock'] ?>
                <?php endif ?>
              </td>
            </tr>
            
            <!-- 變體 -->
            <?php foreach ($rows_v as $v):
              if ($v['product_id'] === $r['product_id']): ?>
                <tr class="list-variant" data-category-id="<?= $r['category_id'] ?>" data-product-id="<?= $v['product_id'] ?>">
                  <td hidden><?= $r['product_name'] ?></td>
                  <td hidden><?= $v['variant_id'] ?></td>
                  <td>
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
                    </div>
                  </td>
                  <td></td>
                  <td></td>
                  <td><?= htmlentities($v['variant_name']) ?></td>
                  <td><?= $v['variant_price'] ?></td>
                  <td><?= $v['variant_stock'] ?></td>
                </tr>
              <?php endif; endforeach; ?>

          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
   
  </div><!-- row end -->
</div> <!-- container end -->
<?php include __DIR__ . '/parts/html-scripts.php' ?>
<script>
  document.addEventListener("DOMContentLoaded", () => {
  // 儲存已選擇商品的 localStorage key
  const STORAGE_KEY = "selectedProducts";

  // 綁定類別按鈕點擊事件
  document.querySelectorAll(".list-group-item").forEach(button => {
    button.addEventListener("click", () => {
      const categoryId = button.getAttribute("data-category-id");
      filterProductsByCategory(categoryId);
    });
  });

  // 初始載入全部商品
  filterProductsByCategory(null);

  // 篩選商品
  function filterProductsByCategory(categoryId) {
  const products = document.querySelectorAll(".list-product");
  const variants = document.querySelectorAll(".list-variant");

  products.forEach(product => {
    const productCategoryId = product.getAttribute("data-category-id");
    const productId = product.getAttribute("data-product-id");

    if (!categoryId || productCategoryId === categoryId) {
      product.style.display = ""; // 顯示商品
      // 顯示對應的變體
      variants.forEach(variant => {
        if (variant.getAttribute("data-product-id") === productId) {
          variant.style.display = ""; // 顯示變體
        }
      });
    } else {
      product.style.display = "none"; // 隱藏商品
      // 隱藏對應的變體
      variants.forEach(variant => {
        if (variant.getAttribute("data-product-id") === productId) {
          variant.style.display = "none"; // 隱藏變體
        }
      });
    }
  });
}


  // 處理商品選擇
  function handleProductSelection(productId, button) {
    let selectedProducts = JSON.parse(localStorage.getItem(STORAGE_KEY)) || [];
    if (!selectedProducts.includes(productId)) {
      selectedProducts.push(productId);
      localStorage.setItem(STORAGE_KEY, JSON.stringify(selectedProducts));
    }
    // 更新按鈕狀態
    button.textContent = "已選擇";
    button.disabled = true;
  }

  // 初始化已選商品的狀態
  function initializeSelectedProducts() {
    const selectedProducts = JSON.parse(localStorage.getItem(STORAGE_KEY)) || [];
    document.querySelectorAll(".select-product").forEach(button => {
      const productId = button.getAttribute("data-product-id");
      if (selectedProducts.includes(productId)) {
        button.textContent = "已選擇";
        button.disabled = true;
      }
    });
  }

  // 綁定商品選擇事件
  document.querySelectorAll(".select-product").forEach(button => {
    button.addEventListener("click", () => {
      const productId = button.getAttribute("data-product-id");
      handleProductSelection(productId, button);
    });
  });
});

</script>
<?php include __DIR__ . '/parts/html-tail.php' ?>