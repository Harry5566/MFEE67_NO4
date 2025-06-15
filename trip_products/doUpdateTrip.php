<?php
require_once "./connect.php";
require_once "./utilities.php";


if (!isset($_POST["id"])) {
  alertGoTo("請從正常管道進入", "./index.php");
  exit;
}

date_default_timezone_set("Asia/Taipei");

$id = $_POST["id"];

$sqlOriginal = "SELECT * FROM `trips` WHERE id = ?";
$stmtOriginal = $pdo->prepare($sqlOriginal);
$stmtOriginal->execute([$id]);
$row = $stmtOriginal->fetch(PDO::FETCH_ASSOC);

$sqlOriNotice = "SELECT * FROM `notices` WHERE `trip_id` = ?";
$stmtOriNotice = $pdo->prepare($sqlOriNotice);
$stmtOriNotice->execute([$id]);
$rowsNotice = $stmtOriNotice->fetchAll(PDO::FETCH_ASSOC);


if (!$row) {
  alertGoTo("查無使用者");
  exit;
}

$name = trim($_POST["name"]);
$mainCate = $_POST["mainCate"];
$subCate = $_POST["subCate"];
$region = $_POST["region"];
$city = $_POST["city"];
$days = intval($_POST["days"]);
$stock = intval($_POST["stock"]);
$price = intval($_POST["price"]);
$info = trim($_POST["info"]);
$infoLength = mb_strlen($info);
$description = trim($_POST["description"]);
$descriptionLength = mb_strlen($description);

$now = date("Y-m-d H:i:s");

$inputStartTime = $_POST["start-at"];
$startDt = new DateTime($inputStartTime);
$startTime = $startDt->format("Y-m-d H:i:s");

$inputEndTime = $_POST["end-at"];
$endDt = new DateTime($inputEndTime);
$endTime = $endDt->format("Y-m-d H:i:s");

$inputPublishedTime = $_POST["published-at"];
$publishedDt = new DateTime($inputPublishedTime);
$publishedTime = $publishedDt->format("Y-m-d H:i:s");

//notices 目前是修改現有的 不刪減
$notices = $_POST["notice"];


// $newNotices = $_POST["newNotice"];
// echo print_r($newNotices);

$set = [];
$values = [":id" => $id];

// 哪些欄位有更新判斷
if ($name != $row["name"]) {
  $set[] = "`name` = :name";
  $values[":name"] = $name;
}
if ($mainCate != $row["main_cate_id"]) {
  $set[] = "`main_cate_id` = :main_cate_id";
  $values[":main_cate_id"] = $mainCate;
}

if ($subCate != $row["sub_cate_id"]) {
  $set[] = "`sub_cate_id` = :sub_cate_id";
  $values[":sub_cate_id"] = $subCate;
}

if ($region != $row["region_id"]) {
  $set[] = "`region_id` = :region_id";
  $values[":region_id"] = $region;
}

if ($city != $row["city_id"]) {
  $set[] = "`city_id` = :city_id";
  $values[":city_id"] = $city;
}

if (trim($info) != trim($row["info"])) {
  $set[] = "`info` = :info";
  $values[":info"] = $info;
}

if (trim($description) != trim($row["description"])) {
  $set[] = "`description` = :description";
  $values[":description"] = $description;
}

if ($price != $row["price"]) {
  $set[] = "`price` = :price";
  $values[":price"] = $price;
}

if ($stock != $row["stock"]) {
  $set[] = "`stock` = :stock";
  $values[":stock"] = $stock;
}

if ($days != $row["duration"]) {
  $set[] = "`duration` = :duration";
  $values[":duration"] = $days;
}

if ($startTime != $row["booking_start_at"]) {
  $set[] = "`booking_start_at` = :booking_start_at";
  $values[":booking_start_at"] = $startTime;
}

if ($endTime != $row["booking_end_at"]) {
  $set[] = "`booking_end_at` = :booking_end_at";
  $values[":booking_end_at"] = $endTime;
}

if ($publishedTime != $row["published_at"]) {
  $set[] = "`published_at` = :published_at";
  $values[":published_at"] = $publishedTime;
}

// 輸入的判斷
if ($days < 1) {
    alertAndBack("❌ 行程天數不可小於1");
    exit;
}

if ($price <= 0 || $price > 99999999) {
    alertAndBack("❌ 價格範圍不合理，請重新輸入");
    exit;
}

if ($infoLength < 20 || $infoLength > 100) {
    alertAndBack("⚠️ 行程簡介需填寫20~100個字元");
    exit;
}

if ($descriptionLength < 200 || $descriptionLength > 1000) {
    alertAndBack("⚠️ 行程介紹需填寫200~1000個字元");
    exit;
}

if ($_FILES["tripFile"]["name"] == "") {
    alertAndBack("⚠️ 行程封面需要選擇至少一張圖片");
    exit;
}

if ($notices < 1) {
    alertAndBack("⚠️ 至少需填寫一項注意事項");
    exit;
}

// 以防萬一的時間判斷
if ($startTime < $now) {
    alertAndBack("⛔ 開始販售時間不能早於現在");
    exit;
}

if ($endTime < $startTime) {
    alertAndBack("⛔ 結束販售時間不能早於開始販售時間");
    exit;
}

if ($publishedTime > $startTime) {
    alertAndBack("⛔ 上架時間不能晚於開始販售時間");
    exit;
}

if ($publishedTime > $endTime) {
    alertAndBack("⛔ 上架時間不能大於開始販售時間");
    exit;
}

if ($publishedTime < $now) {
    alertAndBack("⚠️ 上架時間不能早於現在時間");
    exit;
}

// 注意事項的修改與否判斷
$noticeChanged = false;

if ($notices !== "") {
  foreach ($notices as $notice) {
    $nid = $notice["id"];
    $text = $notice["text"];
    $newText = trim($text);

    foreach ($rowsNotice as $rowNotice) {
      if ($rowNotice["id"] == $nid) {
        $oldText = trim($rowNotice["text"]);
        if ($newText != $oldText) {
          $noticeChanged = true;
        }
      }
    }
  }
}

if (count($notices)  < 1) {
    alertAndBack("⚠️ 至少需填寫一項注意事項");
    exit;
}



$imgs = [];
$imagesChanged = false;
$sqlImg = "SELECT `file_name` FROM `trip_images` WHERE `trip_id` = ?;";


// 修改前先刪除全部資料庫中的圖片
if (isset($_FILES["tripFile"])) {
  $countFile = count($_FILES["tripFile"]["name"]);
  $timestamp = time();
  for ($i = 0; $i < $countFile; $i++) {
    if ($_FILES["tripFile"]["error"][$i] == 0 && $_FILES["tripFile"]["name"][$i] !== "") {
      $ext = pathinfo($_FILES["tripFile"]["name"][$i], PATHINFO_EXTENSION);
      $newFile = ($timestamp + $i) . ".{$ext}";
      $file = "./images/{$newFile}";

      if (move_uploaded_file($_FILES["tripFile"]["tmp_name"][$i], $file)) {
        array_push($imgs, $newFile);
      }
    }
  }
  if (count($imgs) > 0) {
    $imagesChanged = true;
  }
}

// 欄位修改判斷
if (count($set) == 0 && !$noticeChanged && !$imagesChanged) {
  alertAndBack("沒有修改任何欄位");
  exit;
}

try {
  // 如果有重傳新圖才會刪圖再重放
  if ($imagesChanged) {
    $stmtImg = $pdo->prepare($sqlImg);
    $stmtImg->execute([$id]);
    $rowOldImg = $stmtImg->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rowOldImg as $img) {
      $path = "./images/{$img["file_name"]}";
      if (file_exists($path)) {
        unlink($path);
      }
    }

    $sqlDelImg = "DELETE FROM `trip_images` WHERE `trip_id` = ?";
    $stmtDelImg = $pdo->prepare($sqlDelImg);
    $stmtDelImg->execute([$id]);

    for ($ii = 0; $ii < count($imgs); $ii++) {
      $fileName = $imgs[$ii];
      $sortOder = $ii;
      $sqlInsertImg = "INSERT INTO `trip_images` (trip_id, file_name, sort_order) VALUES (?, ?, ?)";
      $stmtInsertImg = $pdo->prepare($sqlInsertImg);
      $stmtInsertImg->execute([$id, $fileName, $sortOder]);
    }
  }

  if (count($set) > 0) {
    $sql = "UPDATE `trips` SET " . implode(", ", $set) . " WHERE `id` = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($values);
  }

  foreach ($notices as $notice) {
    $nid = $notice["id"];
    $text = trim($notice["text"]);

    if (isset($nid)) {
      $sqlNotice = "UPDATE `notices` SET `text` = ? WHERE `id` = ?";
      $stmtNotice = $pdo->prepare($sqlNotice);
      $stmtNotice->execute([$text, $nid]);
    }
  }
} catch (PDOException $e) {
  echo "錯誤: {{$e->getMessage()}}";
  exit;
}



alertGoTo("😸 修改資料成功", "./updateTrip.php?id={$id}");