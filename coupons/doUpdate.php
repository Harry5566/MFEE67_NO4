<?php
// 修改會員主要程式
require_once "connect.php";
require_once "Utilities.php";

// if(!isset($_POST["id"])){
//   alertGoTo("請從正常管道進入", "./");
//   exit;
// }

$id = $_POST["id"];
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

$set = [];
$values = [":id" => $id];

if ($name !== "") {
  $set[] = "`name` = :name";
  $values[":name"] = $name;
}
if ($discount_code !== "") {
  $set[] = "`discount_code` = :discount_code";
  $values[":discount_code"] = $discount_code;
}
if ($discount !== "") {
  $set[] = "`discount` = :discount";
  $values[":discount"] = $discount;
}
if ($discount_type_id !== "") {
  $set[] = "`discount_type_id` = :discount_type_id";
  $values[":discount_type_id"] = $discount_type_id;
}
if ($quantity !== "") {
  $set[] = "`quantity` = :quantity";
  $values[":quantity"] = $quantity;
}
if ($start_date !== "") {
  $set[] = "`start_date` = :start_date";
  $values[":start_date"] = $start_date;
}
if ($end_date !== "") {
  $set[] = "`end_date` = :end_date";
  $values[":end_date"] = $end_date;
}
if (isset($_POST["status_id"])) {
  $set[] = "`status_id` = :status_id";
  $values[":status_id"] = $status_id;
}
if ($usage_scope_id !== "") {
  $set[] = "`usage_scope_id` = :usage_scope_id";
  $values[":usage_scope_id"] = $usage_scope_id;
}
if ($updated_at !== "") {
  $set[] = "`updated_at` = :updated_at";
  $values[":updated_at"] = $updated_at;
}

if (count($set) == 0) {
  alertAndBack("沒有修改任何欄位");
}

$sql = "UPDATE `coupons` SET " . implode(", ", $set) . " WHERE `id` = :id";

try {
  $stmt = $pdo->prepare($sql);
  $stmt->execute($values);

  $affectedRows = $stmt->rowCount();

  if ($affectedRows === 0) {
    alertGoTo("⚠️ 沒有資料被修改，可能是你送出的值與原本資料相同。", "update.php?id={$id}");
  }

  alertGoTo("✅ 修改資料成功", "update.php?id={$id}");

} catch (PDOException $e) {
  echo "錯誤: " . $e->getMessage();
  exit;
}
