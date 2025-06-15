<?php
// 新增會員主要程式
require_once "connect.php";
require_once "Utilities.php";




// if (!isset($_POST["email"])) {
//   alertGoTo("請從正常管道進入", "./index.php");
//   exit;
// }



$name = $_POST["name"];
$discount_code = $_POST["discount_code"];
$discount = $_POST["discount"];
$discount_type_id = $_POST["discount_type_id"];
$quantity = $_POST["quantity"];
$start_date = $_POST["start_date"];
$end_date = $_POST["end_date"];
$status_id = $_POST["status_id"];
$usage_scope_id = $_POST["usage_scope_id"];
$created_at = $_POST["created_at"];
$updated_at = $_POST["updated_at"];

// echo "<pre>";
// var_dump($_POST);
// echo "</pre>";



$sql = "INSERT INTO coupons (`name`, `discount_code`, `discount`, `discount_type_id`, `quantity`, `start_date`, `end_date`, `status_id`, `usage_scope_id`, `created_at`, `updated_at`) 
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";

$values = [
  $name,
  $discount_code,
  $discount,
  $discount_type_id,
  $quantity,
  $start_date,
  $end_date,
  $status_id,
  $usage_scope_id,
  $created_at,
  $updated_at
];






// $sqlEmail = "SELECT COUNT(*) as count FROM `coupons` WHERE `email` = ?;";





try {
  $stmt = $pdo->prepare($sql);
  $stmt->execute($values);
} catch (PDOException $e) {
  echo "錯誤: {{$e->getMessage()}}";
  exit;
}

alertGoTo("新增資料成功", "index.php");


