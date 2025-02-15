<?php 
if (!isset($_SESSION)) {
    // 如果尚未啟動 session 的功能, 就啟動
   session_start();
}
require __DIR__. '/db-connect.php'; 



// 根目錄的系統路徑（用於 PHP 內文中）
define('ROOT_PATH', dirname(__DIR__, 3) . '/');
// __DIR__ 指向 config.php 所在目錄，dirname(__DIR__, 3) 往上三層到根目錄
/* 
使用ROOT_PATH引入 navbar.php
include ROOT_PATH . 'pages/parts/navbar.php';
*/

// 定義根目錄的 URL（用於 href、src 連結等）
define('ROOT_URL', '/midtern/'); 
/* 
使用 ROOT_URL 來建立連結
<a href="<?= ROOT_URL ?>/dist/pages/index.php">到首頁</a>
<a href="<?= ROOT_URL ?>/dist/pages/pets/pet_info.php">到寵物資訊</a>
<script src="<?=ROOT_URL?>dist/js/adminlte.js"></script>
*/
?>