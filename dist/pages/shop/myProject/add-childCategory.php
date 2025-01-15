<?php
/************** 新增產品規格頁面 ****************/

# 要是管理者才可以看到這個頁面
// require __DIR__ . '/parts/admin-required.php';

require __DIR__ . '/parts/init.php';
$title = "新增子類別";
$pageName = "add-categoryChild";


$parent_id  = empty($_GET['parent_id']) ? 0 : intval($_GET['parent_id']);


# 讀取該筆資料
$sql = "SELECT * FROM categories WHERE category_id = $parent_id";
$r = $pdo->query($sql)->fetch();
if (empty($r)) {
  # 如果沒有對應的資料, 就跳走
  header('Location: list-category-admin.php');
  exit;
}


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
          <h5 class="card-title">新增子類別
          </h5>

          <form onsubmit="sendData(event)">
            
            <input type="hidden" name="parent_id" value="<?= $parent_id ?>">
            <div class="mb-3">
              <label class="form-label">類別
              </label>
              <input type="text" class="form-control"  value="<?= $r['category_name'] ?>" disabled>
            </div>
            <div class="mb-3">
              <label for="category_name" class="form-label">子類別名稱**</label>
              <input type="text" class="form-control" id="category_name" name="category_name">
              <div class="form-text"></div>
            </div>
            <div class="mb-3">
              <label for="category_tag" class="form-label">類別標籤**</label>
              <input type="text" class="form-control" id="category_tag" name="category_tag">
              <div class="form-text"></div>
            </div>
            <div class="mb-3">
              <label for="description" class="form-label">描述</label>
              <textarea class="form-control"
                id="description" name="description"></textarea>
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
        <a class="btn btn-primary" href="list-category-admin.php">回到列表頁</a>
      </div>
    </div>
  </div>
</div>
<?php include __DIR__ . '/parts/html-scripts.php' ?>

<script>
  const NameField = document.querySelector('#category_name');
  const descriptionField = document.querySelector('#description');
  const tagField = document.querySelector('#category_tag');
  const myModal = new bootstrap.Modal('#exampleModal');

  /**************** 檢查類別名稱是否重複 ************** */
  NameField.addEventListener('input', function () {
    
    if (NameField.value) {
        // 發送 AJAX 請求
        fetch(`add-upload-api.php?category_name_check=${NameField.value}`)
          .then(response => response.json())
          .then(data => {
            // 更新商品名稱欄位
            console.log(data.count);
            
            if(data.count !== "0"){
              NameField.nextElementSibling.innerHTML = `<i class="fa-solid fa-triangle-exclamation"></i> 已有此類別名稱，名稱不可重複`;
              NameField.closest('.mb-3').classList.add('error');
            }else{
              NameField.closest('.mb-3').classList.remove('error');
            NameField.nextElementSibling.innerHTML = '';
            }
            
          })
          .catch(error => {
            console.error('發生錯誤:', error);
          });
      } 
    });
  /****************************** */



  const sendData = e => {
    e.preventDefault(); // 不要讓表單以傳統的方式送出

    NameField.closest('.mb-3').classList.remove('error');
    tagField.closest('.mb-3').classList.remove('error');

    let isPass = true; // 有沒有通過檢查, 預設值是 true
    // TODO: 資料欄位的檢查
    // --------------------------------------------------------
    
    
    if (!NameField.value) {
      isPass = false;
      NameField.nextElementSibling.innerHTML = `<i class="fa-solid fa-triangle-exclamation"></i> 請填寫商品名稱`;
      NameField.closest('.mb-3').classList.add('error');
    }
    if (!tagField.value) {
      isPass = false;
      tagField.nextElementSibling.innerHTML = `<i class="fa-solid fa-triangle-exclamation"></i> 請填寫標籤名稱`;
      tagField.closest('.mb-3').classList.add('error');
    }else if(tagField.value.length>5){
      isPass = false;
      tagField.nextElementSibling.innerHTML = `<i class="fa-solid fa-triangle-exclamation"></i> 標籤名稱需小於五個字元`;
      tagField.closest('.mb-3').classList.add('error');
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