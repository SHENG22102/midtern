<?php
if (! isset($pageName)) {
  $pageName = '';
}
?>
<style>
  nav.navbar a.nav-link.active {
    color: white;
    background-color: blue;
    border-radius: 6px;
  }
</style>
<div class="container">
  <nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container-fluid">
      <a class="navbar-brand" href="index_.php">Navbar</a>
      <button
        class="navbar-toggler"
        type="button"
        data-bs-toggle="collapse"
        data-bs-target="#navbarSupportedContent"
        aria-controls="navbarSupportedContent"
        aria-expanded="false"
        aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link me-3 <?= $pageName == 'donations' ? 'active' : '' ?>"
              href="donations.php">捐款列表</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= $pageName == 'bank' ? 'active' : '' ?>"
              href="bank.php">對帳列表</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= $pageName == 'expenses' ? 'active' : '' ?>"
              href="expenses.php">支出列表</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</div>