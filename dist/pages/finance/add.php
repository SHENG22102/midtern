<?php
require __DIR__ . '/parts/init.php';
$title = "新增捐款資料";
$pageName = "add";

?>
<?php include __DIR__ . '/parts/html-head.php' ?>
<?php include __DIR__ . '/parts/html-navbar.php' ?>

<div class="container">
  <div class="row mt-4">
    <div class="col-6">
      <h2>新增捐款資料</h2>
      <form onsubmit="sendData(event)" novalidate>
        <div class="mb-3">
          <label for="donor_name" class="form-label">捐款人姓名</label>
          <input type="text" class="form-control" id="donor_name" name="donor_name" required>
          <div class="form-text"></div>
        </div>

        <div class="mb-3">
          <label for="donor_phone" class="form-label">捐款人手機</label>
          <input type="text" class="form-control" id="donor_phone" name="donor_phone" pattern="09\d{8}" required>
          <div class="form-text"></div>
        </div>

        <div class="mb-3">
          <label for="donor_email" class="form-label">捐款人 Email</label>
          <input type="email" class="form-control" id="donor_email" name="donor_email" required>
          <div class="form-text"></div>
        </div>

        <div class="mb-3">
          <label for="amount" class="form-label">捐款金額</label>
          <input type="number" class="form-control" id="amount" name="amount" required>
        </div>
        <div class="mb-3">
          <label for="donation_type" class="form-label">捐款類別</label>
          <select class="form-select" id="donation_type" name="donation_type" required>
            <option value="醫療救援">醫療救援</option>
            <option value="線上認養">線上認養</option>
            <option value="捐予平台">捐予平台</option>
          </select>
        </div>

        <div class="mb-3" id="pet-id-container" style="display: none;">
          <label for="pet_id" class="form-label">寵物 ID</label>
          <input type="number" class="form-control" id="pet_id" name="pet_id">
        </div>

        <div class="mb-3">
          <label for="donation_mode" class="form-label">捐款方式</label>
          <select class="form-select" id="donation_mode" name="donation_mode" required>
            <option value="一次性捐款">一次性捐款</option>
            <option value="定期捐款">定期捐款</option>
          </select>
        </div>

        <div class="mb-3" id="paymentdate" style="display:none;">
          <label for="regular_payment_date" class="form-label">扣款日期</label>
          <input type="date" class="form-control" id="regular_payment_date" name="regular_payment_date"
>
        </div>

        <div class="mb-3">
          <label for="payment_method" class="form-label">支付方式</label>
          <select class="form-select" id="payment_method" name="payment_method" required>
            <option value="信用卡">信用卡</option>
            <option value="銀行轉帳">銀行轉帳</option>
            <option value="郵政劃撥">郵政劃撥</option>
          </select>
        </div>
        <div class="mb-3">
          <label for="reconciliation_status" class="form-label">對帳狀態</label>
          <select class="form-select" id="reconciliation_status" name="reconciliation_status" required>
            <option value="已完成">已完成</option>
            <option value="未完成">未完成</option>
          </select>
        </div>
        <div class="form-check" style="display:none;">
          <input class="form-check-input" type="radio" name="is_receipt_needed" value="0" id="statusPending" checked>
          <label class="form-check-label" for="statusPending">
            無收據
          </label>
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
        <a class="btn btn-primary" href="donations.php">回到列表頁</a>
      </div>
    </div>
  </div>
</div>
<?php include __DIR__ . '/parts/html-scripts.php' ?>
<script>
  // 線上認養
  const donationTypeSelect = document.getElementById('donation_type');
  const petIdContainer = document.getElementById('pet-id-container');
  if (donationTypeSelect.value === '線上認養' && !document.getElementById('pet_id').value) {
    isPass = false;
    document.getElementById('pet_id').closest('.mb-3').classList.add('error');
    document.getElementById('pet_id').nextElementSibling.innerHTML = "請填寫寵物 ID";
  }
  donationTypeSelect.addEventListener('change', () => {
    if (donationTypeSelect.value === '線上認養') {
      petIdContainer.style.display = 'block';
    } else {
      petIdContainer.style.display = 'none';
      document.getElementById('pet_id').value = ''; // 清空寵物 ID
    }
  });
    // 定期捐款
    const donationModeSelect = document.getElementById('donation_mode');
  const paymentDate = document.getElementById('paymentdate');

  donationModeSelect.addEventListener('change', () => {
    if (donationModeSelect.value === '定期捐款') {
      paymentDate.style.display = 'block';
    } else {
      paymentDate.style.display = 'none';
      document.getElementById('regular_payment_date').value = ''; // 清空日期
    }
  });
  const nameField = document.querySelector('#donor_name');
  const emailField = document.querySelector('#donor_email');
  const phoneField = document.querySelector('#donor_phone');
  const amountField = document.querySelector('#amount');
  const myModal = new bootstrap.Modal('#exampleModal');

  function validateEmail(email) {
    // 使用 regular expression 檢查 email 格式正不正確
    const pattern = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return pattern.test(email);
  }

  const sendData = e => {
    e.preventDefault(); // 不要讓表單以傳統的方式送出


    let isPass = true; // 有沒有通過檢查, 預設值是 true
    // TODO: 資料欄位的檢查

    if (nameField.value.length < 2) {
      isPass = false;
      nameField.nextElementSibling.innerHTML = "請填寫正確的姓名";
      nameField.closest('.mb-3').classList.add('error');
    }else{
      nameField.closest('.mb-3').classList.remove('error');
      emailField.closest('.mb-3').classList.remove('error');
    }

    if (!validateEmail(emailField.value)) {
      isPass = false;
      emailField.nextElementSibling.innerHTML = "請填寫正確的 Email";
      emailField.closest('.mb-3').classList.add('error');
    }

    if (!/^09\d{8}$/.test(phoneField.value)) {
      isPass = false;
      phoneField.nextElementSibling.innerHTML = "請填寫正確的手機號碼";
      phoneField.closest('.mb-3').classList.add('error');
    }

    if (amountField.value <= 0) {
      isPass = false;
      amountField.nextElementSibling.innerHTML = "請填寫正確的捐款金額";
      amountField.closest('.mb-3').classList.add('error');
    }

    if (isPass) {
      const fd = new FormData(document.forms[0]);

      fetch(`add-api.php`, {
          method: 'POST',
          body: fd
        }).then(r => r.json())
        .then(obj => {
          console.log(obj);
          if (!obj.success) {
            if (obj.error) {
              alert(`錯誤：${obj.error}`);
            } else {
              alert('新增失敗，請稍後再試');
            }
          } else {
            myModal.show(); // 呈現 modal
          }
        }).catch(err => {
          console.error('伺服器錯誤:', err);
          alert('伺服器發生錯誤，請稍後再試');
        });

    }


  }
</script>
<?php include __DIR__ . '/parts/html-tail.php' ?>