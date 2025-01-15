<?php
require __DIR__ . '/parts/init.php';
$title = "捐款資料修改";
$pageName = "edit";

// 讀取該筆資料
$bn_id = isset($_GET['bn_id']) ? intval($_GET['bn_id']) : 0;
$sql = "SELECT * FROM bank_transfer_details WHERE id=$bn_id";
$r = $pdo->query($sql)->fetch();
if (empty($r)) {
  // 如果沒有對應的資料，就跳走
  header('Location: bank.php');
  exit;
}

?>
<?php include __DIR__ . '/parts/html-head.php' ?>
<?php include __DIR__ . '/parts/html-navbar.php' ?>

<div class="container">
  <div class="row mt-4">
    <div class="col-6">
      <h2>審核轉帳資料</h2>
      <form id="editForm" action="edit_bank-api.php" method="POST" novalidate>
        <input type="hidden" name="bn_id" value="<?= $r['id'] ?>">

        <div class="mb-3">
          <label for="donation_id" class="form-label">捐款編號</label>
          <input type="text" class="form-control" id="donation_id" name="donation_id" value="<?= $r['donation_id'] ?>"
            required readonly>
        </div>
        <div class="mb-3">
          <label for="donor_name" class="form-label">捐款人姓名</label>
          <input type="text" class="form-control" id="donor_name" name="donor_name" value="<?= $r['donor_name'] ?>"
            required>
        </div>
        <div class="mb-3">
          <label for="transfer_amount" class="form-label">捐款金額</label>
          <input type="number" class="form-control" id="transfer_amount" name="transfer_amount" value="<?= $r['transfer_amount'] ?>" required>
        </div>
        <div class="mb-3">
          <label for="transfer_date" class="form-label">匯款日期</label>
          <input type="date" class="form-control" id="transfer_date" name="transfer_date" value="<?= $r['transfer_date'] ?>" required>
        </div>
        <div class="mb-3" id="account_last_5">
          <label for="account_last_5" class="form-label">帳號末五碼</label>
          <input type="text" class="form-control" id="account_last_5" name="account_last_5"
            value="<?= $r['account_last_5'] ?>" required>
        </div>
        <div class="mb-3">
          <label for="reconciliation_status" class="form-label">對帳狀態</label>
          <select class="form-select" id="reconciliation_status" name="reconciliation_status" required>
            <option value="已核對" <?= $r['reconciliation_status'] == '已完成' ? 'selected' : '' ?>>已核對</option>
            <option value="未核對" <?= $r['reconciliation_status'] == '未完成' ? 'selected' : '' ?>>未核對</option>
            <option value="不成立" <?= $r['reconciliation_status'] == '不成立' ? 'selected' : '' ?>>不成立</option>
          </select>
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
        <a class="btn btn-primary" href="bank.php">回到列表頁</a>
      </div>
    </div>
  </div>
</div>
<?php include __DIR__ . '/parts/html-scripts.php' ?>

<script>
  const myModal = new bootstrap.Modal('#exampleModal');

  const sendData = e => {
    e.preventDefault(); // 不要讓表單以傳統的方式送出

    const donor_name = document.getElementById('donor_name');
    const form = document.getElementById('editForm');

    // 清除先前的錯誤提示
    donor_name.closest('.mb-3').classList.remove('error');

    let isPass = true; // 用來判斷表單是否通過驗證

    // 如果表單驗證通過，提交表單
    if (isPass) {
      const fd = new FormData(document.forms[0]);

      // 輸出表單資料到控制台以進行調試
      for (let [key, value] of fd.entries()) {
        console.log(key + ": " + value);
      }

      fetch(`edit_bank-api.php`, {
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