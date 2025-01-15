<?php
/************** 新增產品頁面 ****************/

# 要是管理者才可以看到這個頁面
// require __DIR__ . '/parts/admin-required.php';

require __DIR__ . '/parts/init.php';
$title = "新增活動";
$pageName = "add-promotion";

// 查詢商品類別
$sql_category = "SELECT * FROM Categories ORDER BY category_id ";
$rows_category = $pdo->query($sql_category)->fetchAll();

?>
<?php include __DIR__ . '/parts/html-head.php' ?>
<?php include __DIR__ . '/parts/html-navbar.php' ?>
<style>
  form .mb-3 .form-text {
    display: none;
    /* color: red; */
  }

  form .mb-3.error input.form-control {
    border: 2px solid red;
  }

  form .mb-3.error .form-text {
    display: block;
    color: red;
  }
</style>
<div class="container">
  <div class="row">
    <div class="col-6">
      <div class="card">

        <div class="card-body">
          <h5 class="card-title">新增活動</h5>
          
          <form onsubmit="sendData(event)">
            <div class="mb-3">
              <label for="promotion_name" class="form-label">活動名稱**</label>
              <input type="text" class="form-control" id="promotion_name" name="promotion_name">
              <div class="form-text"></div>
            </div>
            
            <div class="mb-3">
              <label for="description" class="form-label">描述</label>
              <textarea class="form-control"
                id="description" name="description"></textarea>
              <div class="form-text"></div>
            </div>
            <div class="mb-3">
              <label for="discount_percentage" class="form-label">打折(%)</label>
              <input type="number" class="form-control" id="discount_percentage" name="discount_percentage">
              <div class="form-text"></div>
            </div>
            <div class="mb-3">
              <label for="start_date" class="form-label">開始日期**</label>
              <input type="date" class="form-control" id="start_date" name="start_date" >
              <div class="form-text"></div>
            </div>
            <div class="mb-3">
              <label for="end_date" class="form-label">結束日期**</label>
              <input type="date" class="form-control" id="end_date" name="end_date" >
              <div class="form-text"></div>
            </div>
            
            <button type="submit" class="btn btn-primary">新增</button>
          </form>

          <form name="upload_form" hidden>
            <input type="file" name="photo" accept="image/jpeg,image/png" />
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal -新增結果-->
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
        <a class="btn btn-primary" href="list.php">回到列表頁</a>
      </div>
    </div>
  </div>
</div>
<?php include __DIR__ . '/parts/html-scripts.php' ?>

<script>
  const promotionNameField = document.querySelector('#promotion_name ');
  const descriptionField = document.querySelector('#description');
  const startDateField = document.querySelector('#start_date');
  const endDateField = document.querySelector('#end_date');
  const discountField = document.querySelector('#discount_percentage');
  const myModal = new bootstrap.Modal('#exampleModal');

  const sendData = e => {
    e.preventDefault(); // 不要讓表單以傳統的方式送出

    promotionNameField.closest('.mb-3').classList.remove('error');
    descriptionField.closest('.mb-3').classList.remove('error');
    startDateField.closest('.mb-3').classList.remove('error');
    endDateField.closest('.mb-3').classList.remove('error');

    let isPass = true; // 有沒有通過檢查, 預設值是 true
    // TODO: 資料欄位的檢查
    // --------------------------------------------------------
    if (!promotionNameField.value) {
      isPass = false;
      promotionNameField.nextElementSibling.innerHTML = `<i class="fa-solid fa-triangle-exclamation"></i> 請填寫活動名稱`;
      promotionNameField.closest('.mb-3').classList.add('error');
    }
    if (!startDateField.value) {
      isPass = false;
      startDateField.nextElementSibling.innerHTML = `<i class="fa-solid fa-triangle-exclamation"></i> 請填寫活動開始日期`;
      startDateField.closest('.mb-3').classList.add('error');
    }
    if (!endDateField.value) {
      isPass = false;
      endDateField.nextElementSibling.innerHTML = `<i class="fa-solid fa-triangle-exclamation"></i> 請填寫活動結束日期`;
      endDateField.closest('.mb-3').classList.add('error');
    }
    if (!discountField.value) {
      isPass = false;
      discountField.nextElementSibling.innerHTML = `<i class="fa-solid fa-triangle-exclamation"></i> 請填寫活動折扣`;
      discountField.closest('.mb-3').classList.add('error');
    }else if(discountField.value<0 || discountField.value>100){
      isPass = false;
      discountField.nextElementSibling.innerHTML = `<i class="fa-solid fa-triangle-exclamation"></i> 請填寫正確折扣`;
      discountField.closest('.mb-3').classList.add('error');
    }
    // --------------------------------------------------------

    if (isPass) {
      const fd = new FormData(document.forms[0]);

      fetch(`add-upload-api.php`, {
          method: 'POST',
          body: fd
        }).then(r => r.json())
        .then(obj => {
          console.log(obj);
          if (!obj.success && obj.error) {
            alert(obj.error)
          }
          if (obj.success) {
            myModal.show(); // 呈現 modal
          }

        }).catch(console.warn);
    }


  }

</script>
<?php include __DIR__ . '/parts/html-tail.php' ?>