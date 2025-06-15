<?php
require_once "./connect.php";
require_once "./utilities.php";

if (!isset($_GET["id"])) {
  alertGoTo("請從正常管道進入");
  exit;
}

$id = $_GET["id"];
$sql = "UPDATE `trips` SET `is_valid` = 0 WHERE `id` = ?";
// $values = [$id];

try {
  $stmt = $pdo->prepare($sql);
  // $stmt->execute($values);
  $stmt->execute([$id]);
} catch (PDOException $e) {
  echo "錯誤: {{$e->getMessage()}}";
  exit;
}

alertGoTo("😿 刪除資料成功", "./index.php");