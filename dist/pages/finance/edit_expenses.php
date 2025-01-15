<?php
require __DIR__ . '/parts/init.php';
$title = "平台支出編輯";
$pageName = "edit";

// 讀取該筆資料
$bn_id = isset($_GET['bn_id']) ? intval($_GET['bn_id']) : 0;
$sql = "SELECT * FROM expenses WHERE id=$bn_id";
$r = $pdo->query($sql)->fetch();
if (empty($r)) {
  // 如果沒有對應的資料，就跳走
  header('Location: expenses.php');
  exit;
}

?>
<?php include __DIR__ . '/parts/html-head.php' ?>
<?php include __DIR__ . '/parts/html-navbar.php' ?>

<div class="container">
  <div class="row mt-4">
    <div class="col-6">
      <h2>編輯支出資料</h2>
      <form id="editForm" action="edit_expenses-api.php" method="POST" novalidate>
        <div class="mb-3">
          <label for="expenses_id" class="form-label">支出編號</label> 
          <input type="text" class="form-control" id="expenses_id" name="expenses_id" value="<?= $r['id'] ?>"
            readonly>
        </div>
        <div class="mb-3">
          <label for="expense_purpose" class="form-label">支出項目</label>
          <select class="form-select" id="expense_purpose" name="expense_purpose" required>
            <option value="線上認養" <?= $r['expense_purpose'] == '線上認養' ? 'selected' : '' ?>>線上認養</option>
            <option value="食品採購" <?= $r['expense_purpose'] == '食品採購' ? 'selected' : '' ?>>食品採購</option>
            <option value="醫療費用" <?= $r['expense_purpose'] == '醫療費用' ? 'selected' : '' ?>>醫療費用</option>
            <option value="行政開支" <?= $r['expense_purpose'] == '行政開支' ? 'selected' : '' ?>>行政開支</option>
            <option value="活動費用" <?= $r['expense_purpose'] == '活動費用' ? 'selected' : '' ?>>活動費用</option>
            <option value="器材採購" <?= $r['expense_purpose'] == '器材採購' ? 'selected' : '' ?>>器材採購</option>
            <option value="設備維護" <?= $r['expense_purpose'] == '設備維護' ? 'selected' : '' ?>>設備維護</option>
          </select>
        </div>
        <div class="mb-3">
          <label for="amount" class="form-label">支出金額</label>
          <input type="number" class="form-control" id="amount" name="amount" value="<?= $r['amount'] ?>" required>
        </div>
        <div class="mb-3">
          <label for="expense_date" class="form-label">支出日期</label>
          <input type="date" class="form-control" id="expense_date" name="expense_date" value="<?= $r['expense_date'] ?>" required>
        </div>
        <div class="mb-3" id="e_description">
          <label for="e_description" class="form-label">支出描述</label>
          <textarea class="form-control" id="e_description" name="e_description"
            value="<?= $r['e_description'] ?>" required><?= $r['e_description'] ?></textarea>
        </div>
        <div class="mb-3" style="display: none;">
          <label for="refund_id" class="form-label">退款編號</label>
          <input type="text" class="form-control" id="refund_id" name="refund_id" value="<?= $r['refund_id'] ?>"
            readonly>
        </div>
        <div class="mb-3">
          <label for="created_by" class="form-label">記錄人員</label>
          <input type="text" class="form-control" id="created_by" name="created_by" value="<?= $r['created_by'] ?>" readonly>
        </div>
        <button type="submit" class="btn btn-primary">更新資料</button>
      </form>
    </div>
  </div>
</div>
<!-- Modal -->
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
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">關閉</button>
        <a class="btn btn-primary" href="expenses.php">回到列表頁</a>
      </div>
    </div>
  </div>
</div>
<?php include __DIR__ . '/parts/html-scripts.php' ?>

<script>
  const myModal = new bootstrap.Modal('#exampleModal');

  const sendData = e => {
    e.preventDefault(); // 不要讓表單以傳統的方式送出
    const form = document.getElementById('editForm');
    let isPass = true; // 用來判斷表單是否通過驗證
    // 如果表單驗證通過，提交表單
    if (isPass) {
      const fd = new FormData(document.forms[0]);

      // 輸出表單資料到控制台以進行調試
      for (let [key, value] of fd.entries()) {
        console.log(key + ": " + value);
      }

      fetch(`edit_expenses-api.php`, {
          method: 'POST',
          body: fd
        })
        .then(r => r.json())
        .then(obj => {
          console.log(obj); // 查看伺服器回應
          if (obj.success) {
            myModal.show(); // 顯示 modal
          } else {
            alert(obj.message || '資料沒有修改');
          }
        })
        .catch(console.warn);
    }
  };

  // 將事件綁定到表單提交事件
  const form = document.getElementById('editForm');
  form.addEventListener('submit', sendData);
</script>
<?php include __DIR__ . '/parts/html-tail.php' ?>