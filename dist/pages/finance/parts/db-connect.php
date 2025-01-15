<?php

require __DIR__.'/config.php';


$dsn = sprintf(
  "mysql:host=%s;dbname=%s;port=%s;charset=utf8mb4",
  DB_HOST,
  DB_NAME,
  DB_PORT,
);

$pdo_options = [
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

$pdo = new PDO($dsn, DB_USER, DB_PASS, $pdo_options); # 建立連線物件