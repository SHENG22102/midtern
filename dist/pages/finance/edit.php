<?php
require __DIR__ . '/parts/init.php';
$title = "捐款資料修改";
$pageName = "edit";

// 讀取該筆資料
$dn_id = isset($_GET['dn_id']) ? intval($_GET['dn_id']) : 0;
$sql = "SELECT donations.*, bank_transfer_details.reconciliation_status
  FROM donations
  LEFT JOIN bank_transfer_details ON donations.id = bank_transfer_details.donation_id WHERE donations.id=$dn_id";
$r = $pdo->query($sql)->fetch();
if (empty($r)) {
  // 如果沒有對應的資料，就跳走
  header('Location: donations.php');
  exit;
}

$sql = "SELECT * FROM receipts WHERE donation_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$dn_id]);
$receipt = $stmt->fetch(PDO::FETCH_ASSOC);

?>
<?php include __DIR__ . '/parts/html-head.php' ?>
<?php include __DIR__ . '/parts/html-navbar.php' ?>

<div class="container">
  <div class="row mt-4">
    <div class="col-6">
      <h2>修改捐款資料</h2>
      <form id="editForm" action="edit-api.php" method="POST" novalidate>
        <input type="hidden" name="dn_id" value="<?= $r['id'] ?>">

        <div class="mb-3">
          <label for="donor_name" class="form-label">捐款人姓名</label>
          <input type="text" class="form-control" id="donor_name" name="donor_name" value="<?= $r['donor_name'] ?>"
            required>
        </div>

        <div class="mb-3">
          <label for="donor_phone" class="form-label">捐款人手機</label>
          <input type="text" class="form-control" id="donor_phone" name="donor_phone" value="<?= $r['donor_phone'] ?>"
            pattern="09\d{8}" required>
        </div>

        <div class="mb-3">
          <label for="donor_email" class="form-label">捐款人 Email</label>
          <input type="email" class="form-control" id="donor_email" name="donor_email" value="<?= $r['donor_email'] ?>"
            required>
        </div>

        <div class="mb-3">
          <label for="amount" class="form-label">捐款金額</label>
          <input type="number" class="form-control" id="amount" name="amount" value="<?= $r['amount'] ?>" required>
        </div>
        <div class="mb-3">
          <label for="donation_type" class="form-label">捐款類別</label>
          <select class="form-select" id="donation_type" name="donation_type" required>
            <option value="醫療救援" <?= $r['donation_type'] == '醫療救援' ? 'selected' : '' ?>>醫療救援</option>
            <option value="線上認養" <?= $r['donation_type'] == '線上認養' ? 'selected' : '' ?>>線上認養</option>
            <option value="捐予平台" <?= $r['donation_type'] == '捐予平台' ? 'selected' : '' ?>>捐予平台</option>
          </select>
        </div>
        <div class="mb-3" id="pet-id-container" style="display: none;">
          <label for="pet_id" class="form-label">寵物 ID</label>
          <input type="number" class="form-control" id="pet_id" name="pet_id" value="<?= $r['pet_id'] ?? '' ?>">
        </div>

        <div class="mb-3">
          <label for="donation_mode" class="form-label">捐款方式</label>
          <select class="form-select" id="donation_mode" name="donation_mode" required>
            <option value="一次性捐款" <?= $r['donation_mode'] == '一次性捐款' ? 'selected' : '' ?>>一次性捐款</option>
            <option value="定期捐款" <?= $r['donation_mode'] == '定期捐款' ? 'selected' : '' ?>>定期捐款</option>
          </select>
        </div>

        <div class="mb-3" id="paymentdate" style="display:none;">
          <label for="regular_payment_date" class="form-label">扣款日期</label>
          <input type="date" class="form-control" id="regular_payment_date" name="regular_payment_date"
            value="<?= $r['regular_payment_date'] ?>">
        </div>

        <div class="mb-3">
          <label for="payment_method" class="form-label">支付方式</label>
          <select class="form-select" id="payment_method" name="payment_method" required>
            <option value="信用卡" <?= $r['payment_method'] == '信用卡' ? 'selected' : '' ?>>信用卡</option>
            <option value="銀行轉帳" <?= $r['payment_method'] == '銀行轉帳' ? 'selected' : '' ?>>銀行轉帳</option>
            <option value="郵政劃撥" <?= $r['payment_method'] == '郵政劃撥' ? 'selected' : '' ?>>郵政劃撥</option>
          </select>
        </div>
        <div class="mb-3">
          <label for="reconciliation_status" class="form-label">對帳狀態</label>
          <input type="text" class="form-control" id="reconciliation_status" name="reconciliation_status"
          value="<?= $r['reconciliation_status'] ?>" required disabled>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="is_receipt_needed" value="1" id="status1"
            <?= $r['is_receipt_needed'] == '1' ? 'checked' : '' ?>>
          <label class="form-check-label" for="statusConfirmed">
            已開收據
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="is_receipt_needed" value="0" id="status0"
            <?= $r['is_receipt_needed'] == '0' ? 'checked' : '' ?>>
          <label class="form-check-label" for="statusPending">
            無收據
          </label>
        </div>

        <div id="receipt-info" style="display: none;">
          <div class="mb-3">
            <label for="receipt_name" class="form-label">收據人姓名</label>
            <input type="text" class="form-control" id="receipt_name" name="receipt_name"
              value="<?= $receipt['receipt_name'] ?? '' ?>">
          </div>
          <div class="mb-3">
            <label for="receipt_phone" class="form-label">收據人電話</label>
            <input type="text" class="form-control" id="receipt_phone" name="receipt_phone"
              value="<?= $receipt['receipt_phone'] ?? '' ?>" pattern="09\d{8}">
          </div>
          <div class="mb-3">
            <label for="receipt_address" class="form-label">收據地址</label>
            <textarea class="form-control" id="receipt_address"
              name="receipt_address"><?= $receipt['receipt_address'] ?? '' ?></textarea>
          </div>
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
        <a class="btn btn-primary" href="donations.php">回到列表頁</a>
      </div>
    </div>
  </div>
</div>
<?php include __DIR__ . '/parts/html-scripts.php' ?>

<script>
  const myModal = new bootstrap.Modal('#exampleModal');
  // 收據明細區
  document.querySelectorAll('[name="is_receipt_needed"]').forEach(el => {
    el.addEventListener('change', () => {
      const receiptInfo = document.getElementById('receipt-info');
      receiptInfo.style.display = el.value === '1' ? 'block' : 'none';
    });
  });

  document.addEventListener('DOMContentLoaded', () => {
    // 頁面載入時初始化狀態
    const isReceiptNeeded = document.querySelector('[name="is_receipt_needed"]:checked').value;
    const receiptInfo = document.getElementById('receipt-info');
    receiptInfo.style.display = isReceiptNeeded === '1' ? 'block' : 'none';
  });

  // 線上認養
  const donationTypeSelect = document.getElementById('donation_type');
  const petIdContainer = document.getElementById('pet-id-container');

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
      document.getElementById('regular_payment_date').value = null; // 清空日期
    }
  });
  const sendData = e => {
    e.preventDefault(); // 不要讓表單以傳統的方式送出

    const donor_name = document.getElementById('donor_name');
    const phoneField = document.getElementById('donor_phone');
    const emailField = document.getElementById('donor_email');
    const form = document.getElementById('editForm');

    // 清除先前的錯誤提示
    donor_name.closest('.mb-3').classList.remove('error');
    phoneField.closest('.mb-3').classList.remove('error');
    emailField.closest('.mb-3').classList.remove('error');

    let isPass = true; // 用來判斷表單是否通過驗證

    // 驗證手機號碼
    if (donor_name.value == '匿名') {
      // 如果是匿名捐款，設置手機號碼和 Email 為 '匿名'
      phoneField.value = "匿名";
      emailField.value = "匿名";
      phoneField.removeAttribute('pattern');
    } else {
      // 不是匿名捐款，正常驗證手機號碼和 Email
      form.removeAttribute('novalidate'); // 如果不是 匿名，啟用驗證
      if (!/^09\d{8}$/.test(phoneField.value)) {
        isPass = false;
        phoneField.nextElementSibling.innerHTML = "請輸入有效的手機號碼";
        phoneField.closest('.mb-3').classList.add('error');
      }
      // 驗證電子郵件格式
      if (!/\S+@\S+\.\S+/.test(emailField.value)) {
        isPass = false;
        emailField.nextElementSibling.innerHTML = "請填寫正確的 Email";
        emailField.closest('.mb-3').classList.add('error');
      }
    }

    // 如果表單驗證通過，提交表單
    if (isPass) {
      const fd = new FormData(document.forms[0]);

      // 輸出表單資料到控制台以進行調試
      for (let [key, value] of fd.entries()) {
        console.log(key + ": " + value);
      }

      fetch(`edit-api.php`, {
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