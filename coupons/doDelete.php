<?php
require_once "connect.php";  // 載入資料庫連線設定
require_once "Utilities.php"; // 載入 alertGoTo 函式

// 檢查網址是否有帶 id
if (!isset($_GET["id"])) {
    alertGoTo("請從正常管道進入", "index.php");
    exit;
}

$id = $_GET["id"];

// 準備 SQL：把 coupons 表中指定 id 的 is_valid 設為 0
$sql = "UPDATE `coupons` SET is_valid = 0 WHERE id = ?";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $count = $stmt->rowCount();

    echo "影響筆數：" . $count . "<br>";

    if ($count > 0) {
        alertGoTo("刪除資料成功", "index.php");
    } else {
        alertGoTo("找不到這筆資料或已刪除", "index.php");
    }
} catch (PDOException $e) {
    echo "錯誤：" . $e->getMessage();
    exit;
}