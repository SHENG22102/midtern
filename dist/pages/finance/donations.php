<?php
require __DIR__ . '/parts/init.php';
$title = "捐款管理";
$pageName = "donations";

$perPage = 25; # 每一頁有幾筆

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
if ($page < 1) {
  header('Location: ?page=1'); # 跳轉頁面 (後端), 也稱為 redirect (轉向)
  exit; # 離開 (結束) 程式 (以下的程式都不會執行)
  die(); # 同 exit 的功能, 但可以回傳字串或編號
}

$t_sql = "SELECT COUNT(*) FROM donations";

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

  # 取第一頁的資料
  # 取第一頁的資料，並與 bank_transfer_details 進行聯結查詢
  $sql = sprintf("SELECT donations.*, bank_transfer_details.reconciliation_status
  FROM donations
  LEFT JOIN bank_transfer_details ON donations.id = bank_transfer_details.donation_id 
  LIMIT %d, %d", ($page - 1) * $perPage, $perPage);

  $rows = $pdo->query($sql)->fetchAll(); # 取得該分頁的資料
};
?>
<?php include __DIR__ . '/parts/html-head.php' ?>
<?php include __DIR__ . '/parts/html-navbar.php' ?>

<div class="container">
  <div class="row mt-4 align-items-center">
    <div class="col-11">
      <nav aria-label="Page navigation">
        <ul class="pagination">
          <li class="page-item <?= $page == 1 ? 'disabled' : '' ?>">
            <a class="page-link" href="?page=1">
              <i class="fa-solid fa-angles-left"></i>
            </a>
          </li>
          <li class="page-item <?= $page == 1 ? 'disabled' : '' ?>">
            <a class="page-link" href="?page=<?= $page - 1 ?>">
              <i class="fa-solid fa-angle-left"></i>
            </a>
          </li>

          <?php for ($i = $page - 5; $i <= $page + 5; $i++):
            if ($i >= 1 and $i <= $totalPages):
          ?>
              <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
              </li>
          <?php endif;
          endfor; ?>

          <li class="page-item <?= $page == $totalPages ? 'disabled' : '' ?>">
            <a class="page-link" href="?page=<?= $page + 1 ?>">
              <i class="fa-solid fa-angle-right"></i>
            </a>
          </li>
          <li class="page-item <?= $page == $totalPages ? 'disabled' : '' ?>">
            <a class="page-link" href="?page=<?= $totalPages ?>">
              <i class="fa-solid fa-angles-right"></i>
            </a>
          </li>
        </ul>
      </nav>
    </div>
    <div class="col mb-0">
      <a href="add.php"><i class="fa-solid fa-plus " style="border:1px solid black; padding:3px"></i></a>
    </div>
  </div>
  <div class="row">
    <div class="col">
      <table class="table table-bordered table-striped">
        <thead>
          <tr>
            <th><i class="fa-solid fa-trash"></i></th>
            <th>捐款編號</th>
            <th>會員編號</th>
            <th>捐款人姓名</th>
            <th>捐款人電話</th>
            <th>捐款人mail</th>
            <th>捐款金額</th>
            <th>捐款類別</th>
            <th>寵物id</th>
            <th>捐款種類</th>
            <th>扣款日期</th>
            <th>捐款方式</th>
            <th>資料成立時間</th>
            <th>對帳狀態</th>
            <th>收據明細</th>
            <th><i class="fa-solid fa-pen-to-square"></i></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($rows as $r): ?>
            <tr>
              <td><a href="javascript:" onclick="deleteOne(event)">
                  <i class="fa-solid fa-trash"></i>
                </a></td>
              <td style="display:none"><?= $r['id'] ?></td>
              <td><?= $r['id'] ?></td>
              <td><?= $r['user_id'] ?></td>
              <td><?= $r['donor_name'] ?></td>
              <td><?= $r['donor_phone'] ?></td>
              <td><?= $r['donor_email'] ?></td>
              <td><?= $r['amount'] ?></td>
              <td><?= $r['donation_type'] ?></td>
              <td><?= $r['pet_id'] ?></td>
              <td><?= $r['donation_mode'] ?></td>
              <td><?= $r['regular_payment_date'] ?></td>
              <td><?= $r['payment_method'] ?></td>
              <td><?= $r['create_datetime'] ?></td>
              <td><?= $r['reconciliation_status'] ?></td>
              <td>
                <?= $r['is_receipt_needed'] == 1 ? '<a href="javascript:void(0);" class="receipt-link" data-receipt_id="' . $r['id'] . '" data-receipt_name="' . htmlentities($r['donor_name']) . '">收據</a>' : '無收據'; ?>
              </td>
              <td><a href="edit.php?dn_id=<?= $r['id'] ?>">
                  <i class="fa-solid fa-pen-to-square"></i>
                </a></td>

            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php include __DIR__ . '/parts/html-scripts.php' ?>
<!-- Model -->
<div class="modal fade" id="receiptModal" tabindex="-1" role="dialog" aria-labelledby="receiptModalLabel"
  aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="receiptModalLabel">收據詳細資料</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- 收據內容會顯示在這裡 -->
        <p id="receiptDetails">正在加載收據資料...</p>
      </div>
      <div class="modal-footer">
      </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const modal = new bootstrap.Modal(document.getElementById('receiptModal')); // 建立 Modal 實例

    // 當點擊"收據"鏈接時
    document.querySelectorAll('.receipt-link').forEach(function(link) {
      link.addEventListener('click', function(e) {
        e.preventDefault();
        const receiptId = e.target.getAttribute('data-receipt_id'); // 取得捐款ID

        // 使用 AJAX 請求收據資料
        fetch(`get_receipt_details.php?id=${receiptId}`)
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              // 填充模態視窗的內容
              document.getElementById('receiptDetails').innerHTML = ` 
              <strong>收據人姓名:</strong> ${data.receipt_name}<br>
              <strong>收據人電話:</strong> ${data.receipt_phone}<br>
              <strong>收據地址:</strong> ${data.receipt_address}<br>
            `;
              // 顯示模態視窗
              modal.show();
            } else {
              document.getElementById('receiptDetails').innerHTML = '無收據資料';
            }
          })
          .catch(error => {
            console.error('Error loading receipt details:', error);
          });
      });
    });
  });
</script>
<script>
  const deleteOne = e => {
    e.preventDefault(); // 沒有要連到某處
    const tr = e.target.closest('tr');
    const [, td_id, td_name, , , , , , ] = tr.querySelectorAll('td');
    const dn_name = td_name.innerHTML;
    const dn_id = td_id.innerHTML.trim();
    console.log([dn_name.innerHTML]);
    if (confirm(`是否要刪除捐款人id為 ${dn_id} ，姓名為 ${dn_name} 的捐款紀錄?`)) {
      // 使用 JS 做跳轉頁面
      location.href = `del.php?id=${dn_id}`;
    }
  };
</script>
<?php include __DIR__ . '/parts/html-tail.php' ?>