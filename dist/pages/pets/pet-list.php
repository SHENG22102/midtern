<?php
require __DIR__ . '/../parts/init.php';
$title = "寵物列表";
$pageName = "pet-list";

$perPage = 25; #每頁幾筆

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
if ($page < 1) {
  header('Location: ?page=1'); #redirect 跳轉頁面 (後端)
  exit; #離開程式 同die('字串');
}

$keyword = empty($_GET['keyword']) ? '' : $_GET['keyword'];
$birth_begin = empty($_GET['birth_begin']) ? '' : $_GET['birth_begin'];
$birth_end = empty($_GET['birth_end']) ? '' : $_GET['birth_end'];

$where = 'WHERE 1';
if (isset($_GET['keyword']) && !empty($keyword)) {
  $keyword = trim($_GET['keyword']); // 去除首尾空白
  $keyword = trim($keyword, '"'); // 去除可能已存在的引號
  $keyword_exact = $pdo->quote($keyword); // 用於精確匹配id
  $keyword = '"' . $keyword . '"'; // 添加新的引號
  $keyword_ = $pdo->quote("%" . $keyword . "%"); // 字串加上%之後跳脫引號，避免SQL注入
  $where .= " AND (id = $keyword_exact OR name LIKE $keyword_ OR species LIKE $keyword OR variety LIKE $keyword)";
}

if (!empty($birth_begin)) {
  $t = strtotime($birth_begin); #將字串轉換成時間戳記
  if ($t !== false) {
    $where .= sprintf(" AND birthday >= '%s' ", date('Y-m-d', $t));
  }
}
if (!empty($birth_end)) {
  $t = strtotime($birth_end); #將字串轉換成時間戳記
  if ($t !== false) {
    $where .= sprintf(" AND birthday <= '%s' ", date('Y-m-d', $t));
  }
}

// 總筆數查詢
$t_sql = "SELECT COUNT(1) 
FROM `pets` $where";

#總筆數
$totalRows = $pdo->query($t_sql)->fetch(PDO::FETCH_NUM)[0];  // 索引式陣列取值

#總頁數
$totalPages = ceil($totalRows / $perPage); #ceil無條件進位

$allowedColumns = ['id', 'name', 'species', 'variety', 'gender', 'birthday', 'weight', 'chip_number', 'is_adopted'];
$sort = isset($_GET['sort']) && in_array($_GET['sort'], $allowedColumns) ? $_GET['sort'] : 'id';

$sort = isset($_GET['sort']) ? $_GET['sort'] : 'id';
$order = isset($_GET['order']) && $_GET['order'] == 'asc' ? 'ASC' : 'DESC';

$rows = []; //設定預設值
if ($totalRows > 0) {
  if ($page > $totalPages) {
    # 用戶要看的頁數超出範圍
    header('Location: ?page=' . $totalPages);
    exit;
  }
  # SQL 查詢取第一頁資料
  $sql = sprintf(
    "SELECT * FROM pets 
    %s
    ORDER BY %s %s LIMIT %s,%s",
    $where,
    $sort,
    $order,
    $perPage * ($page - 1),
    $perPage
  );
  #取得該分頁的資料
  $rows = $pdo->query($sql)->fetchAll();
}

$qs = array_filter($_GET); #去除值為空的項目
?>



<?php include ROOT_PATH . 'dist/pages/parts/head.php' ?>
<!--begin::Body-->
<style>
  #pet-info th .d-flex {
    white-space: nowrap;
  }

  #pet-info th a {
    margin-left: 5px;
    color: inherit;
    text-decoration: none;
  }

  #pet-info th a.active-sort {
    color: initial;
    /* 當前排序的列使用默認顏色 */
  }
</style>

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
  <!--begin::App Wrapper 網頁的主要內容在這-->
  <div class="app-wrapper">
    <!--begin::Header-->
    <?php include ROOT_PATH . 'dist/pages/parts/navbar.php' ?>
    <!--end::Header-->
    <!--begin::Sidebar-->
    <?php include ROOT_PATH . 'dist/pages/parts/sidebar.php' ?>
    <!--end::Sidebar-->
    <!--begin::App Main-->
    <main class="app-main pt-5">
      <!--begin::App Content Header-->
      <div class="app-content-header">
        <!--begin::Container-->
        <div class="container-fluid">
          <!--begin::Row-->
          <div class="row">
            <div class="col-sm-6">
              <h3 class="mb-0">這裡是標題</h3>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-end">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">breadcrumb</li>
              </ol>
            </div>
          </div>
          <!--end::Row-->
        </div>
        <!--end::Container-->
      </div>
      <!--end::App Content Header-->
      <!--begin::App Content-->
      <div class="app-content">
        <!--begin::Container-->
        <div class="container-fluid">
          <!-- 這裡是內容 -->
          <div class="row align-items-end">
          <div class="col-8">
              <ul class="pagination">
                <li class="page-item <?= $page === 1 ? 'disabled' : '' ?>">
                  <a class="page-link "
                    href="?<?= $qs['page'] = 1;
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
                <!-- for迴圈產生按鈕 -->
                <?php for (
                  $i = ($page >= ($totalPages - 1)) ?
                    (($page == $totalPages - 1) ? $page - 3 : $page - 4) : ($page - 2);
                  $i <= (($page <= 2) ? 5 : ($page + 2));
                  $i++
                ):
                  // 限制分頁按鈕的邊界
                  if ($i >= 1 and $i <= $totalPages):
                    $qs = array_filter($_GET); #去除值為空的項目
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
                  <a class="page-link" href="?
                            <?php $qs['page'] = $totalPages;
                            echo http_build_query($qs) ?>">
                    <i class="fa-solid fa-angles-right"></i>
                  </a>
                </li>
              </ul>
            </div>
            <div class="col-4 mb-2">
              <form role="search" method="GET">
                <div class="d-flex mb-2">
                  <input class="form-control me-2" name="keyword"
                    value="<?= isset($_GET['keyword']) ? htmlspecialchars(trim($_GET['keyword'], '"')) : '' ?>"
                    type="search" placeholder="搜尋" aria-label="Search">
                  <button class="btn btn-outline-primary" type="submit">
                    <i class="fa-solid fa-magnifying-glass"></i>
                  </button>
                  <button class="btn btn-outline-secondary ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#advancedSearch" aria-expanded="false" aria-controls="advancedSearch">
                    <i class="fa-solid fa-filter"></i>
                  </button>
                </div>

                <div class="collapse mt-3" id="advancedSearch">
                  <div class="card card-body">
                    <div class="mb-3">
                      <label for="birth_begin" class="form-label">出生日期（起始）</label>
                      <input type="date" class="form-control" id="birth_begin" name="birth_begin"
                        value="<?= isset($_GET['birth_begin']) ? htmlspecialchars($_GET['birth_begin']) : '' ?>">
                    </div>
                    <div class="mb-3">
                      <label for="birth_end" class="form-label">出生日期（結束）</label>
                      <input type="date" class="form-control" id="birth_end" name="birth_end"
                        value="<?= isset($_GET['birth_end']) ? htmlspecialchars($_GET['birth_end']) : '' ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">搜索</button>
                  </div>
                </div>
              </form>
            </div>
            
          </div>
          <div class="row">
            <div class="col-sm-12">
              <table id="pet-info" class="table table-bordered table-hover">
                <thead>
                  <tr>
                    <th><i class="fa-regular fa-trash-can"></i></th>
                    <?php
                    $columns = ['id', 'name', 'species', 'variety', 'gender', 'birthday', 'weight', 'chip_number', 'is_adopted'];
                    $currentSort = isset($_GET['sort']) ? $_GET['sort'] : '';
                    $currentOrder = isset($_GET['order']) ? $_GET['order'] : '';

                    foreach ($columns as $col) {
                      $sortClass = 'fa-arrows-up-down';
                      $linkClass = '';
                      $nextOrder = 'desc';

                      if ($currentSort === $col) {
                        $linkClass = 'text-primary';
                        if ($currentOrder === 'desc') {
                          $sortClass = 'fa-arrow-down-wide-short';
                          $nextOrder = 'asc'; // 如果當前是降序，下一個就是升序
                        } else {
                          $sortClass = 'fa-arrow-up-short-wide';
                          $nextOrder = 'desc'; // 如果當前是升序，下一個就是降序
                        }
                      }

                      echo "<th>
              <div class='d-flex justify-content-between align-items-center'>
                $col
                <a href='?sort=$col&order=$nextOrder' class='$linkClass'>
                  <i class='fa-solid $sortClass'></i>
                </a>
              </div>
            </th>";
                    }
                    ?>
                    <th><i class="fa-regular fa-pen-to-square"></i></th>
                    <th>main_photo</th>
                  </tr>

                </thead>
                <tbody>
                  <?php
                  foreach ($rows as $r):
                  ?>
                    <tr>
                      <td><a href="javascript:" onclick="deleteOne(event)">
                          <i class="fa-regular fa-trash-can"></i>
                        </a></td>
                      <td><?= $r['id'] ?></td>
                      <td><?= $r['name'] ?></td>
                      <td><?= $r['species'] ?></td>
                      <td><?= $r['variety'] ?></td>
                      <td><?= $r['gender'] ?></td>
                      <td><?= $r['birthday'] ?></td>
                      <td><?= $r['weight'] ?></td>
                      <td><?= $r['chip_number'] ?></td>
                      <td><?= $r['is_adopted'] ?></td>
                      <td><a href="pet-edit.php?id=<?= $r['id'] ?>"><i class="fa-regular fa-pen-to-square"></i></a></td>
                      <th><?php if (!empty($r['main_photo'])):
                          ?>
                          <img src="./pets_photo/<?= $r['main_photo'] ?>" alt="" width="100px">
                        <?php endif; ?>
                      </th>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
                <tfoot>
                  <tr>
                    <th><i class="fa-regular fa-trash-can"></i></th>
                    <th>id</th>
                    <th>name</th>
                    <th>species</th>
                    <th>variety</th>
                    <th>gender</th>
                    <th>birthday</th>
                    <th>weight</th>
                    <th>chip_number</th>
                    <th>is_adopted</th>
                    <th><i class="fa-regular fa-pen-to-square"></i></th>
                    <th>main_photo</th>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
        </div>
        <!--end::Container-->
      </div>
      <!--end::App Content-->
    </main>
    <!--end::App Main-->
    <!--begin::Footer-->
    <?php include ROOT_PATH . 'dist/pages/parts/footer.php' ?>
    <!--end::Footer-->
  </div>
  <!--end::App Wrapper-->
  <!--begin::Script-->
  <script>
    const deleteOne = e => {
      e.preventDefault(); //取消超連結導向
      const tr = e.target.closest('tr');
      const [, td_id, td_name] = tr.querySelectorAll('td'); //陣列的解構賦值
      const id = parseInt(td_id.innerHTML);
      const name = td_name.innerHTML;
      console.log('刪除', id, name);
      if (confirm(`是否要刪除編號為 ${id} 名字為 ${name} 的資料?`)) {
        // 使用javascript做跳轉頁面
        location.href = `pet-del.php?id=${id}`;
      }
    }
  </script>
  <!--begin::Third Party Plugin(OverlayScrollbars) 可自定義的覆蓋滾動條-->
  <script
    src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/browser/overlayscrollbars.browser.es6.min.js"
    integrity="sha256-dghWARbRe2eLlIJ56wNB+b760ywulqK3DzZYEpsg2fQ="
    crossorigin="anonymous"></script>
  <!--end::Third Party Plugin(OverlayScrollbars)-->
  <!--begin::Required Plugin(Bootstrap 5)-->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <!--end::Required Plugin(Bootstrap 5)-->
  <!--begin::Required Plugin(AdminLTE)-->
  <script src="<?= ROOT_URL ?>dist/js/adminlte.js"></script>
  <!--end::Required Plugin(AdminLTE)-->
  <!--begin::OverlayScrollbars Configure 設定滾動條-->
  <script>
    const SELECTOR_SIDEBAR_WRAPPER = '.sidebar-wrapper';
    const Default = {
      // 當鼠標離開滾動區域時，滾動條會自動隱藏；允許用戶通過點擊滾動條來進行滾動
      scrollbarTheme: 'os-theme-light',
      scrollbarAutoHide: 'leave',
      scrollbarClickScroll: true,
    };
    // DOMContentLoaded確保在DOM完全加載後執行代碼
    document.addEventListener('DOMContentLoaded', function() {
      const sidebarWrapper = document.querySelector(SELECTOR_SIDEBAR_WRAPPER);
      if (sidebarWrapper && typeof OverlayScrollbarsGlobal?.OverlayScrollbars !== 'undefined') {
        // 初始化滾動條，並傳遞配置選項，如主題和自動隱藏行為
        OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
          scrollbars: {
            theme: Default.scrollbarTheme,
            autoHide: Default.scrollbarAutoHide,
            clickScroll: Default.scrollbarClickScroll,
          },
        });
      }
    });
  </script>
  <?php
  // 如果有設定標誌就顯示alert
  if (isset($_SESSION['show_alert']) && $_SESSION['show_alert']) {
    echo "<script>
            window.onload = function() {
                alert('已刪除 ' + {$_SESSION['deleted_id']} + ' ' + '{$_SESSION['deleted_name']}');
            }
          </script>";
    unset($_SESSION['show_alert']); // 使用後清除標誌
    unset($_SESSION['deleted_id']); // 清除 id
    unset($_SESSION['deleted_name']); // 清除 name
  }
  ?>
  <!--end::OverlayScrollbars Configure-->
  <!-- OPTIONAL SCRIPTS 額外功能&實作-->
  <!-- 排序功能 -->
  <script>
    document.querySelectorAll('th a').forEach(link => {
      link.addEventListener('click', function(e) {
        e.preventDefault();
        const url = new URL(this.href);
        const sort = url.searchParams.get('sort');
        const order = url.searchParams.get('order');

        // 更新所有圖示為預設狀態
        document.querySelectorAll('th a i').forEach(icon => {
          icon.className = 'fa-solid fa-arrows-up-down';
        });

        // 更新當前列的圖示
        if (order === 'asc') {
          this.querySelector('i').className = 'fa-solid fa-solid fa-arrow-up-short-wide';
        } else {
          this.querySelector('i').className = 'fa-solid fa-arrow-down-wide-short';
        }

        // 添加排序參數到當前URL並跳轉
        const currentUrl = new URL(window.location.href);
        currentUrl.searchParams.set('sort', sort);
        currentUrl.searchParams.set('order', order);
        window.location.href = currentUrl.toString();
      });
    });
  </script>

  <!--end::Script-->
</body>
<!--end::Body-->

</html>