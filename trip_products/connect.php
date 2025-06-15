<!-- 還需要確認最終使用的資料庫名稱 -->

<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "my_test_db";
$port = 3306;

try {
  $pdo = new PDO(
    "mysql:host={$servername};
     dbname={$dbname};
     port={$port};
     charset=utf8",
    $username,
    $password
  );
  $pdo->exec("SET NAMES utf8mb4");
} catch (PDOException $e) {
  echo "資料庫連線失敗<br>";
  echo "Error: " . $e->getMessage() . "<br>";
  exit;
}
