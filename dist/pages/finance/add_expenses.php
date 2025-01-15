<?php
require __DIR__ . '/parts/init.php';
$title = "新增平台支出項目";
$pageName = "add_expenses";
?>
<?php include __DIR__ . '/parts/html-head.php' ?>
<?php include __DIR__ . '/parts/html-navbar.php' ?>

<div class="container">
  <div class="row mt-4">
    <div class="col-6">
      <h2>新增支出資料</h2>
      <form onsubmit="sendData(event)" novalidate>
        <div class="mb-3">
          <label for="expense_purpose" class="form-label">支出項目</label>
          <select class="form-select" id="expense_purpose" name="expense_purpose" required>
            <option value="食品採購">食品採購</option>
            <option value="行政開支">行政開支</option>
            <option value="醫療費用">醫療費用</option>
            <option value="活動費用">活動費用</option>
            <option value="器材採購">器材採購</option>
            <option value="設備維護">設備維護</option>
          </select>
        </div>
        <div class="mb-3">
          <label for="amount" class="form-label">支出金額</label>
          <input type="number" class="form-control" id="amount" name="amount" required>
          <div class="form-text"></div>
        </div>
        <div class="mb-3">
          <label for="expense_date" class="form-label">支出日期</label>
          <input type="date" class="form-control" id="expense_date" name="expense_date" required>
          <div class="form-text"></div>
        </div>
        <div class="mb-3" id="e_description">
          <label for="e_description" class="form-label">支出描述</label>
          <textarea class="form-control" id="e_description_textarea" name="e_description"required></textarea>
          <div class="form-text"></div>
        </div>
        <div class="mb-3"style="display: none;">
          <label for="refund_id" class="form-label">退款編號</label>
          <input type="text" class="form-control" id="refund_id" name="refund_id">
        </div>
        <div class="mb-3">
          <label for="created_by" class="form-label">記錄人員</label>
          <input type="text" class="form-control" id="created_by" name="created_by" require>
          <div class="form-text"></div>
        </div>
        <button type="submit" class="btn btn-primary">新增資料</button>
      </form>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">新增結果</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="alert alert-success" role="alert">
          資料新增成功
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
  e.preventDefault();

  // 確保這些字段是正確的欄位
  const expensePurposeField = document.querySelector('#expense_purpose');
  const amountField = document.querySelector('#amount');
  const expenseDateField = document.querySelector('#expense_date');
  const eDescriptionField = document.querySelector('#e_description_textarea');
  const createdByField = document.querySelector('#created_by');

  let isPass = true;

  // Validate expense_purpose
  if (expensePurposeField.value.trim() === '') {
    isPass = false;
    expensePurposeField.closest('.mb-3').classList.add('error');
    expensePurposeField.nextElementSibling.innerHTML = "請選擇支出項目";
  } else {
    expensePurposeField.closest('.mb-3').classList.remove('error');
  }

  // Validate amount
  if (amountField.value.trim() === '' || amountField.value <= 0) {
    isPass = false;
    amountField.closest('.mb-3').classList.add('error');
    amountField.nextElementSibling.innerHTML = "請填寫正確的支出金額";
  } else {
    amountField.closest('.mb-3').classList.remove('error');
  }

  // Validate expense_date
  if (expenseDateField.value.trim() === '') {
    isPass = false;
    expenseDateField.closest('.mb-3').classList.add('error');
    expenseDateField.nextElementSibling.innerHTML = "請選擇支出日期";
  } else {
    expenseDateField.closest('.mb-3').classList.remove('error');
  }

  // Validate e_description
  if (eDescriptionField.value.trim() === '') {
    isPass = false;
    eDescriptionField.closest('.mb-3').classList.add('error');
    eDescriptionField.nextElementSibling.innerHTML = "請填寫支出描述";
  } else {
    eDescriptionField.closest('.mb-3').classList.remove('error');
  }

  // Validate created_by
  if (createdByField.value.trim() === '') {
    isPass = false;
    createdByField.closest('.mb-3').classList.add('error');
    createdByField.nextElementSibling.innerHTML = "請填寫記錄人員";
  } else {
    createdByField.closest('.mb-3').classList.remove('error');
  }

  // 如果所有驗證都通過，提交表單
  if (isPass) {
    const fd = new FormData(document.forms[0]);

    fetch('add_expenses-api.php', {
        method: 'POST',
        body: fd
      }).then(r => r.json())
      .then(obj => {
        if (!obj.success) {
          alert(`錯誤：${obj.error}`);
        } else {
          myModal.show(); // 顯示成功訊息
        }
      }).catch(err => {
        console.error('伺服器錯誤:', err);
        alert('伺服器發生錯誤，請稍後再試');
      });
  }
};

</script>

<?php include __DIR__ . '/parts/html-tail.php' ?>