<?php require __DIR__ . '/../parts/init.php';
$title = "留言管理"; // 這個變數可修改，用在<head>的標題
$pageName = "demo"; // 這個變數可修改，用在sidebar的按鈕active

$perPage = 25; # 每一頁有幾筆
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
if ($page < 1) {
  header('Location: ?page=1');
  exit;
}

$keyword = empty($_GET['keyword']) ? '' : $_GET['keyword'];

$where = ' WHERE 1 '; # SQL 條件的開頭

if ($keyword) {
  $keyword_ = $pdo->quote("%{$keyword}%");
  $where .= " AND (
    comments.body LIKE $keyword_ OR
    users.user_name LIKE $keyword_ OR
    comments.status LIKE $keyword_ OR
    comments.created_at LIKE $keyword_ OR
    comments.updated_at LIKE $keyword_
  )";
}

$t_sql = "SELECT COUNT(1) FROM comments
          JOIN users ON comments.user_id = users.user_id
          $where";

# 總筆數
$totalRows = $pdo->query($t_sql)->fetch(PDO::FETCH_NUM)[0];
# 總頁數
$totalPages = ceil($totalRows / $perPage);

if ($totalRows > 0) {
  if ($page > $totalPages) {
    header('Location: ?page=' . $totalPages);
    exit;
  }

  # 取得該分頁的評論資料
  $sql = sprintf("SELECT comments.*, users.user_name
                  FROM comments
                  JOIN users ON comments.user_id = users.user_id
                  %s
                  ORDER BY comments.created_at DESC, comments.id DESC
                  LIMIT %s, %s",
                  $where, ($page - 1) * $perPage, $perPage);
  $rows = $pdo->query($sql)->fetchAll();
}
$pageRange = 2;
$startPage = max($page - $pageRange, 1);
$endPage = min($page + $pageRange, $totalPages);

// 確保始終顯示第一頁和最後一頁
if ($startPage > 1) {
    $startPage = max($startPage, 2);
}
if ($endPage < $totalPages) {
    $endPage = min($endPage, $totalPages - 1);
}
?>
<?php include ROOT_PATH . 'dist/pages/parts/head.php' ?>
<!--begin::Body-->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
  $('.status-select').change(function() {
    var select = $(this);
    var commentId = select.data('comment-id');
    var newStatus = select.val();
    var originalStatus = select.data('original-status');

    if (confirm('您確定要更改留言狀態嗎？')) {
      $.ajax({
        url: 'update_comment_status.php',
        method: 'POST',
        data: { comment_id: commentId, status: newStatus },
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

<style>
.custom-pagination-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-top: 2rem;
}

.custom-pagination .pagination {
    display: inline-flex;
    flex-wrap: wrap;
    justify-content: center;
    background-color: #f8f9fa;
    padding: 5px;
    border-radius: 5px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.custom-pagination .page-item {
    margin: 2px;
}

.custom-pagination .page-link {
    border: none;
    color: #495057;
    border-radius: 3px;
    padding: 6px 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    min-width: 38px;
    text-align: center;
}

.custom-pagination .page-item.active .page-link,
.custom-pagination .page-link:hover:not(.disabled) {
    background-color: #007bff;
    color: #fff;
}

.custom-pagination .page-item.disabled .page-link {
    color: #6c757d;
    pointer-events: none;
    background-color: #e9ecef;
}

.pagination-info {
    margin-top: 1rem;
    color: #6c757d;
}

/* 三角形圖示樣式 */
.triangle-left,
.triangle-right {
    width: 0;
    height: 0;
    border-top: 8px solid transparent;
    border-bottom: 8px solid transparent;
    position: relative;
    top: 0px; /* 微調三角形位置 */
}

.triangle-left {
    border-right: 10px solid #495057;
}

.triangle-right {
    border-left: 10px solid #495057;
}

.page-item:not(.disabled):hover .triangle-left {
    border-right-color: #fff;
}

.page-item:not(.disabled):hover .triangle-right {
    border-left-color: #fff;
}

.page-item.disabled .triangle-left {
    border-right-color: #6c757d;
}

.page-item.disabled .triangle-right {
    border-left-color: #6c757d;
}

.prev-page,
.next-page {
    padding: 6px 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%; /* 確保高度與其他按鈕一致 */
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
    <br>
    <main class="app-main pt-5">
    <div class="container">
  <div class="row mt-2">
    <div class="col-6"></div>
    <div class="col-6">
  <form class="d-flex" role="search" id="searchForm">
    <input class="form-control me-2"
      id="searchInput"
      name="keyword"
      value="<?= empty($_GET['keyword']) ? '' : htmlentities($_GET['keyword']) ?>"
      type="search" placeholder="搜尋留言內容、作者、狀態等" aria-label="Search">
    <button class="btn btn-outline-primary" type="submit">Search</button>
  </form>
</div>
<div class="custom-pagination-container">
    <nav aria-label="Page navigation" class="custom-pagination">
        <ul class="pagination">
            <li class="page-item <?= $page == 1 ? 'disabled' : '' ?>">
                <a class="page-link" href="?<?php $qs['page'] = 1; echo http_build_query($qs); ?>" aria-label="First">
                    第一頁
                </a>
            </li>
            <li class="page-item <?= $page == 1 ? 'disabled' : '' ?>">
                <a class="page-link prev-page" href="?<?php $qs['page'] = max(1, $page - 1); echo http_build_query($qs); ?>" aria-label="Previous">
                    <span class="triangle-left"></span>
                </a>
            </li>

            <?php if ($startPage > 1): ?>
                <li class="page-item"><a class="page-link" href="?<?php $qs['page'] = 1; echo http_build_query($qs); ?>">1</a></li>
                <?php if ($startPage > 2): ?>
                    <li class="page-item disabled"><span class="page-link">...</span></li>
                <?php endif; ?>
            <?php endif; ?>

            <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                    <a class="page-link" href="?<?php $qs['page'] = $i; echo http_build_query($qs); ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>

            <?php if ($endPage < $totalPages): ?>
                <?php if ($endPage < $totalPages - 1): ?>
                    <li class="page-item disabled"><span class="page-link">...</span></li>
                <?php endif; ?>
                <li class="page-item"><a class="page-link" href="?<?php $qs['page'] = $totalPages; echo http_build_query($qs); ?>"><?= $totalPages ?></a></li>
            <?php endif; ?>

            <li class="page-item <?= $page == $totalPages ? 'disabled' : '' ?>">
                <a class="page-link next-page" href="?<?php $qs['page'] = min($totalPages, $page + 1); echo http_build_query($qs); ?>" aria-label="Next">
                    <span class="triangle-right"></span>
                </a>
            </li>
            <li class="page-item <?= $page == $totalPages ? 'disabled' : '' ?>">
                <a class="page-link" href="?<?php $qs['page'] = $totalPages; echo http_build_query($qs); ?>" aria-label="Last">
                    最後一頁
                </a>
            </li>
        </ul>
    </nav>
    <div class="pagination-info">
        第 <?= $page ?> 頁，共 <?= $totalPages ?> 頁
    </div>
</div>

  <div class="row">
    <div class="col">
      <table class="table table-bordered table-striped">
        <thead>
          <tr>
          <th>#</th>
            <th>留言內容</th>
            <th>作者id</th>
            <th>作者暱稱</th>
            <th>按讚數</th>
            <th>建立時間</th>
            <th>更新時間</th>
            <th>狀態</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($rows as $r): ?>
            <tr>
              <td><?= $r['id'] ?></td>
              <td><?= htmlentities($r['body']) ?></td>
              <td><?= htmlentities($r['user_id']) ?></td>
              <td><?= htmlentities($r['user_name']) ?></td>
              <td><?= $r['likes_count'] ?></td>
              <td><?= $r['created_at'] ?></td>
              <td><?= $r['updated_at'] ?></td>
              <td>
                <select class="form-select status-select" data-comment-id="<?= $r['id'] ?>" data-original-status="<?= $r['status'] ?>">
                <option value="已留言" <?= $r['status'] == '已留言' ? 'selected' : '' ?>>已留言</option>
                <option value="被檢舉" <?= $r['status'] == '被檢舉' ? 'selected' : '' ?>>被檢舉</option>
                <option value="已刪除" <?= $r['status'] == '已刪除' ? 'selected' : '' ?>>已刪除</option>
              </select>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
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
        window.location.href = 'comment.php';
      }
    });

    searchForm.addEventListener('submit', function(event) {
      if (searchInput.value.trim() === '') {
        event.preventDefault();
        window.location.href = 'comment.php';
      }
    });
  }
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  const searchInput = document.getElementById('searchInput');
  const searchForm = document.getElementById('searchForm');

  if (searchInput && searchForm) {
    searchInput.addEventListener('search', function(event) {
      if (this.value === '') {
        event.preventDefault();
        window.location.href = 'comment.php';
      }
    });

    searchForm.addEventListener('submit', function(event) {
      if (searchInput.value.trim() === '') {
        event.preventDefault();
        window.location.href = 'comment.php';
      }
    });
  }
});
</script>
  <!--end::Script-->
</body>
<!--end::Body-->

</html>
