<?php require __DIR__ . '/../parts/init.php';
$title = "黑名單管理";
$pageName = "blacklist";

$perPage = 25;

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
if ($page < 1) {
  header('Location: ?page=1');
  exit;
}

$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
$birth_begin = empty($_GET['birth_begin']) ? '' : $_GET['birth_begin'];
$birth_end = empty($_GET['birth_end']) ? '' : $_GET['birth_end'];
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';

$where = ' WHERE 1 ';

if ($keyword) {
  $keyword_ = $pdo->quote("%{$keyword}%");
  $where .= " AND (users.user_name LIKE $keyword_ OR users.user_email LIKE $keyword_ OR users.user_status LIKE $keyword_) ";
}
if ($birth_begin) {
  $t = strtotime($birth_begin);
  if ($t !== false) {
    $where .= sprintf(" AND users.user_birthday >= '%s' ", date('Y-m-d', $t));
  }
}
if ($birth_end) {
  $t = strtotime($birth_end);
  if ($t !== false) {
    $where .= sprintf(" AND users.user_birthday <= '%s' ", date('Y-m-d', $t));
  }
}

if ($status_filter !== 'all') {
  $where .= " AND users.user_status = " . $pdo->quote($status_filter);
}

$t_sql = "SELECT COUNT(1) FROM `users` $where";

# 總筆數
$totalRows = $pdo->query($t_sql)->fetch(PDO::FETCH_NUM)[0];
# 總頁數
$totalPages = ceil($totalRows / $perPage);
$rows = [];
if ($totalRows > 0) {
  if ($page > $totalPages) {
    header('Location: ?page=' . $totalPages);
    exit;
  }

  # 取得該分頁的用戶資料
  $sql = sprintf("SELECT user_id, user_name, user_email, user_status FROM users
  %s ORDER BY user_id LIMIT %s, %s",
  $where, ($page - 1) * $perPage, $perPage);
  $rows = $pdo->query($sql)->fetchAll();
}

?>
<?php include ROOT_PATH . 'dist/pages/parts/head.php' ?>
<!--begin::Body-->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
  $('.status-select').change(function() {
    var select = $(this);
    var userId = select.data('user-id');
    var newStatus = select.val();
    var originalStatus = select.data('original-status');

    if (confirm('您確定要更改用戶狀態嗎？')) {
      $.ajax({
        url: 'update_user_status.php',
        method: 'POST',
        data: { user_id: userId, status: newStatus },
        dataType: 'json',
        success: function(response) {
          if (response.status === 'success') {
            alert('狀態已更新');
            select.data('original-status', newStatus);
          } else {
            alert('更新失敗：' + response.message);
            select.val(originalStatus);
          }
        },
        error: function() {
          alert('發生錯誤，請稍後再試');
          select.val(originalStatus);
        }
      });
    } else {
      select.val(originalStatus);
    }
  });
});
</script>

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
  <div class="app-wrapper">
    <?php include ROOT_PATH . 'dist/pages/parts/navbar.php' ?>
    <?php include ROOT_PATH . 'dist/pages/parts/sidebar.php' ?>
    <br>
    <main class="app-main pt-5">
      <div class="container-fluid">
        <div class="row justify-content-center">
          <div class="col-lg-10 col-xl-8"> <!-- 調整列寬度 -->
            <div class="card">
              <div class="card-body">
                <div class="row mb-3">
                  <div class="col-md-6 mb-2 mb-md-0">
                    <div class="btn-group w-100" role="group" aria-label="User status filter">
                      <a href="?status=all" class="btn btn-outline-primary <?= $status_filter === 'all' ? 'active' : '' ?>">所有用戶</a>
                      <a href="?status=正常" class="btn btn-outline-primary <?= $status_filter === '正常' ? 'active' : '' ?>">正常用戶</a>
                      <a href="?status=禁言" class="btn btn-outline-primary <?= $status_filter === '禁言' ? 'active' : '' ?>">禁言用戶</a>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <form class="d-flex" role="search" id="searchForm">
                      <input type="hidden" name="status" value="<?= htmlspecialchars($status_filter) ?>">
                      <input class="form-control me-2"
                        id="searchInput"
                        name="keyword"
                        value="<?= empty($_GET['keyword']) ? '' : htmlentities($_GET['keyword']) ?>"
                        type="search" placeholder="搜尋用戶名稱、郵箱、狀態等" aria-label="Search">
                      <button class="btn btn-outline-primary" type="submit">search</button>
                    </form>
                  </div>
                </div>

                <div class="table-responsive">
                  <table class="table table-bordered table-striped table-hover">
                    <thead class="table-light">
                      <tr>
                        <th>ID</th>
                        <th>暱稱</th>
                        <th>Email</th>
                        <th>狀態</th>
                      </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($rows as $r): ?>
                      <tr>
                        <td><?= $r['user_id'] ?></td>
                        <td><?= htmlentities($r['user_name']) ?></td>
                        <td><?= htmlentities($r['user_email']) ?></td>
                        <td>
                          <select class="form-select form-select-sm status-select" data-user-id="<?= $r['user_id'] ?>" data-original-status="<?= $r['user_status'] ?>">
                            <option value="正常" <?= $r['user_status'] == '正常' ? 'selected' : '' ?>>正常</option>
                            <option value="禁言" <?= $r['user_status'] == '禁言' ? 'selected' : '' ?>>禁言</option>
                          </select>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>

                <div class="d-flex justify-content-center mt-3">
                  <nav aria-label="Page navigation">
                    <ul class="pagination">
                      <!-- ... 分頁代碼保持不變 ... -->
                    </ul>
                  </nav>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
    <!--end::App Main-->
    <!--begin::Footer-->
    <?php include ROOT_PATH . 'dist/pages/parts/footer.php' ?>
    <!--end::Footer-->
  </div>
  <!--end::App Wrapper-->
  <!--begin::Script-->
  <!--begin::Third Party Plugin(OverlayScrollbars) 可自定義的覆蓋滾動條-->
  <script
    src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/browser/overlayscrollbars.browser.es6.min.js"
    integrity="sha256-dghWARbRe2eLlIJ56wNB+b760ywulqK3DzZYEpsg2fQ="
    crossorigin="anonymous"></script>
  <!--end::Third Party Plugin(OverlayScrollbars)-->
  <!--begin::Required Plugin(popperjs for Bootstrap 5) Bootstrap彈出元素"（如工具提示和彈出窗口）-->
  <script
    src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
    integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
    crossorigin="anonymous"></script>
  <!--end::Required Plugin(popperjs for Bootstrap 5)-->
  <!--begin::Required Plugin(Bootstrap 5)-->
  <script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
    integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy"
    crossorigin="anonymous"></script>
  <!--end::Required Plugin(Bootstrap 5)-->
  <!--begin::Required Plugin(AdminLTE)-->
  <script src="<?= ROOT_URL ?>/dist/js/adminlte.js"></script>
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
  <!--end::OverlayScrollbars Configure-->
  <script>
document.addEventListener('DOMContentLoaded', function() {
  const searchInput = document.getElementById('searchInput');
  const searchForm = document.getElementById('searchForm');

  if (searchInput && searchForm) {
    searchInput.addEventListener('search', function(event) {
      if (this.value === '') {
        event.preventDefault();
        window.location.href = 'post.php';
      }
    });

    searchForm.addEventListener('submit', function(event) {
      if (searchInput.value.trim() === '') {
        event.preventDefault();
        window.location.href = 'post.php';
      }
    });
  }
});
</script>

  <!--end::Script-->
</body>
<!--end::Body-->

</html>
