<?php
date_default_timezone_set('Asia/Taipei');

error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "";       // ✅ XAMPP 預設是空密碼
$dbname = "topics";
$port = 3306;         // ✅ XAMPP 預設 port



try {
  $pdo = new PDO(
    "mysql:host=$servername;dbname=$dbname;port=$port;charset=utf8",
    $username,
    $password
  );
  // echo "✅ 資料庫連線成功";
} catch (PDOException $e) {
  echo "❌ 資料庫連線失敗<br>";
  echo "錯誤訊息：" . $e->getMessage() . "<br>";
  exit;
}




?>