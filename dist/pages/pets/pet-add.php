<?php
# 管理者登入驗證
# require __DIR__ . '/parts/admin-required.php'; 
require __DIR__ . '/../parts/init.php';
$title = "新增寵物資訊"; // 這個變數可修改，用在<head>的標題
$pageName = "pet-add"; // 這個變數可修改，用在sidebar的按鈕active
?>
<?php include ROOT_PATH . 'dist/pages/parts/head.php' ?>
<!--begin::Body-->
<style>
  form .mb-3 .form-text {
    display: none;
    color: red;
  }

  form .mb-3.error input.form-control {
    border: 2px solid red;
  }

  form .mb-3.error .form-text {
    display: block;
    color: red;
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
    <main class="app-main pt-5">
      <!--begin::App Content Header-->
      <div class="app-content-header">
        <!--begin::Container-->
        <div class="container-fluid">
          <!--begin::Row-->
          <div class="row">
            <div class="col-sm-6">
              <h3 class="mb-0">新增寵物資訊</h3>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-end">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">新增寵物資訊</li>
              </ol>
            </div>
          </div>
          <!--end::Row-->
        </div>
        <!--end::Container-->
      </div>
      <!--end::App Content Header-->
      <!--begin::App Content-->
      <div class="app-content">
        <!--begin::Container-->
        <div class="container-fluid">
          <div class="row">
            <div class="col-6">
              <div class="card card-primary card-outline mb-4">
                <!--begin::Header-->
                <div class="card-header">
                  <div class="card-title">表單</div>
                </div>
                <!--end::Header-->
                <!--begin::Form-->
                <form onsubmit="sendData(event)">
                  <div class="card-body">
                    <div class="row mb-3 align-items-center">
                      <label for="name" class="col-sm-2 col-form-label">名字 **</label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" id="name" name="name">
                      </div>
                      <div class="form-text"></div>
                    </div>
                    <div class="row mb-3 align-items-center">
                      <label for="species" class="col-sm-2 col-form-label">物種 **</label>
                      <div class="col-sm-10">
                        <select class="form-select" aria-label="select species" name="species">
                          <option value="null" selected>請選擇</option>
                          <option value="狗">狗</option>
                          <option value="貓">貓</option>
                        </select>
                      </div>
                      <div class="form-text"></div>
                    </div>
                    <div class="row mb-3 align-items-center">
                      <label for="variety" class="col-sm-2 col-form-label">品種</label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" id="variety" name="variety" placeholder="請輸入品種">
                      </div>
                    </div>
                    <div class="row mb-3 align-items-center">
                      <div class="col-sm-2 col-form-label">性別 **</div>
                      <div class="col-sm-1">
                        <div class="form-check">
                          <input
                            class="form-check-input"
                            type="radio"
                            name="gender"
                            id="genderRadios1"
                            value="公"
                            checked />
                          <label class="form-check-label" for="genderRadios1"> 公 </label>
                        </div>
                      </div>
                      <div class="col-sm-1">
                        <div class="form-check">
                          <input
                            class="form-check-input"
                            type="radio"
                            name="gender"
                            id="genderRadios2"
                            value="母" />
                          <label class="form-check-label" for="genderRadios2"> 母 </label>
                        </div>
                      </div>
                    </div>
                    <div class="row mb-3 align-items-center">
                      <label for="birthday" class="col-sm-2 col-form-label">生日</label>
                      <div class="col-sm-10">
                        <input type="date" class="form-control" id="birthday" name="birthday">
                      </div>
                      <div class="form-text"></div>
                    </div>
                    <div class="row mb-3 align-items-center">
                      <label for="weight" class="col-sm-2 col-form-label">體重</label>
                      <div class="col-sm-10">
                        <input type="number" class="form-control" id="weight" name="weight" step="0.01">
                      </div>
                    </div>
                    <div class="row mb-3 align-items-center">
                      <label for="chip" class="col-sm-2 col-form-label">晶片號碼</label>
                      <div class="col-sm-10">
                        <input type="number" class="form-control" id="chip" name="chip" value="">
                      </div>
                    </div>
                    <div class="row mb-3 align-items-center">
                      <div class="col-sm-2 col-form-label">是否領養 **</div>
                      <div class="col-sm-1">
                        <div class="form-check">
                          <input
                            class="form-check-input"
                            type="radio"
                            name="is-adopted"
                            id="is-adopted1"
                            value="0"
                            checked />
                          <label class="form-check-label" for="is-adopted1"> 否 </label>
                        </div>
                      </div>
                      <div class="col-sm-1">
                        <div class="form-check">
                          <input
                            class="form-check-input"
                            type="radio"
                            name="is-adopted"
                            id="is-adopted2"
                            value="1" />
                          <label class="form-check-label" for="is-adopted2"> 是 </label>
                        </div>
                      </div>
                    </div>
                    <div class="row mb-3 align-items-center">
                      <label for="adopted-date" class="col-sm-2 col-form-label">待確認</label>
                      <div class="col-sm-10">
                        是否還有新增欄位?
                      </div>
                    </div>
                    <div class="card-footer">
                      <button type="submit" class="btn btn-warning">提交</button>
                      <button type="button" class="btn btn-light float-end" onclick="goBack()">取消</button>
                    </div>
                </form>
                <!--end::Form-->
              </div>

            </div>
          </div>
        </div>
        <!--end::Container-->
      </div>
      <!--end::App Content-->
    </main>
    <!--end::App Main-->

    <!-- Modal -->
    <div class="modal fade" id="pet-add-modal" tabindex="-1" aria-labelledby="pet-add-modal" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">新增結果</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="close"></button>
          </div>
          <div class="modal-body">
            <div class="alert alert-success" role="alert">
              資料新增成功!
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">關閉</button>
            <a href="pet-list.php" class="btn btn-primary">回到列表</a>
          </div>
        </div>
      </div>
    </div>
    <!-- End Modal -->

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
  <!--begin::Required Plugin(Bootstrap 5)-->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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
  <!-- Customized Script -->
  <script>
    const nameField = document.querySelector('#name');
    // const emailField = document.querySelector('#email');
    const myModal = new bootstrap.Modal('#pet-add-modal');

    // function validateEmail(email) {
    //   // 使用 regular expression 檢查 email 格式正不正確
    //   const pattern = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zAZ]{2,}))$/;
    //   return pattern.test(email);
    // }

    const sendData = e => {
      e.preventDefault(); //不要讓表單送出

      nameField.closest('.mb-3').classList.remove('error');
      // emailField.closest('.mb-3').classList.remove('error');

      let isPass = true; //有沒有通過檢查，預設true
      // TODO: 資料欄位的檢查 birthday species variety

      if (nameField.value.length < 2) {
        isPass = false;
        nameField.nextElementSibling.innerHTML = '名字至少要兩個字';
        nameField.closest('.mb-3').classList.add('error');
      }

      // if (!validateEmail(emailField.value)) {
      //   isPass = false;
      //   emailField.nextElementSibling.innerHTML = '請填寫正確的email';
      //   emailField.closest('.mb-3').classList.add('error');
      // }

      //isPass = false;
      if (isPass) {
        //先做一個空的表單
        const fd = new FormData(document.forms[0]);

        fetch(`./api/pet-add-api.php`, {
            method: 'POST',
            body: fd
          }).then(r => r.json())
          .then(obj => {
            console.log(obj);
            if (!obj.success && obj.error) {
              alert(obj.error);
            } else if (obj.success) {
              myModal.show();
            }
          }).catch(console.warn);
      }

    }

    function goBack() {
      window.history.back();
    }
  </script>
  <!--end::Script-->
</body>
<!--end::Body-->

</html>