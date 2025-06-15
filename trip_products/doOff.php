<?php
require_once "./connect.php";
require_once "./utilities.php";

if (!isset($_GET["id"])) {
  alertGoTo("請從正常管道進入");
  exit;
}

$id = $_GET["id"];
$time = time();
$now = date("Y-m-d H:i:s");
$sql = "UPDATE `trips` SET `unpublished_at` = ? WHERE `id` = ?";
// $values = [$id];

try {
  $stmt = $pdo->prepare($sql);
  // $stmt->execute($values);
  $stmt->execute([$now, $id]);
} catch (PDOException $e) {
  echo "錯誤: {{$e->getMessage()}}";
  exit;
}

alertGoTo("😼 下架成功", "./index.php");