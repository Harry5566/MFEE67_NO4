<?php
require_once "./connect.php";
require_once "./utilities.php";

if (!isset($_GET["id"])) {
  alertGoTo("請從正常管道進入", "./ticketIndex.php");
  exit;
}

$id = $_GET["id"];

$sql = "UPDATE `products` SET `is_valid` = 0 WHERE `id` = ?";
try {
  $stmt = $pdo->prepare($sql);
  $stmt->execute([$id]);
} catch (PDOException $e) {
  echo "錯誤: {{$e->getMessage()}}";
  exit;
}
alertGoBack("刪除資料成功");