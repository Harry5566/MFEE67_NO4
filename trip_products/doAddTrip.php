<?php
require_once "./connect.php";
require_once "./utilities.php";

if (!isset($_POST["name"])) {
    alertGoTo("🫵 請從正常管道進入");
    exit;
}

date_default_timezone_set("Asia/Taipei");

// 提示框的部分需要再找好看的

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


// 判斷須調整，看能不能一次性的列出沒有填寫的選項
if ($name == "") {
    alertAndBack("⚠️ 請輸入行程名稱");
    exit;
}

if ($mainCate == "") {
    alertAndBack("⚠️ 請選擇主分類");
} else if ($subCate == "") {
    alertAndBack("⚠️ 請選擇子分類");
}

if ($region == "") {
    alertAndBack("⚠️ 請選擇區域");
} else if ($city == "") {
    alertAndBack("⚠️ 請選擇城市");
}

if ($days < 1) {
    alertAndBack("❌ 行程天數不可小於1");
    exit;
}

if ($price <= 0 || $price > 99999999) {
    alertAndBack("❌ 價格範圍不合理，請重新輸入");
    exit;
}

if ($info == "") {
    alertAndBack("⚠️ 請輸入行程簡介");
    exit;
}

if ($infoLength < 20 || $infoLength > 100) {
    alertAndBack("⚠️ 行程簡介需填寫20~100個字元");
    exit;
}

if ($description == "") {
    alertAndBack("⚠️ 請輸入行程介紹");
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

$notices = [];
$noticeCount = count($_POST["notice"]);

for ($i = 0; $i < $noticeCount; $i++) {
    if ($_POST["notice"][$i] !== "") {
        array_push($notices, $_POST["notice"][$i]);
    }
}

if (count($notices) < 1) {
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

$imgs = [];
$countFile = count($_FILES["tripFile"]["name"]);
$timestamp = time();

for ($ii = 0; $ii < $countFile; $ii++) {

    if ($_FILES["tripFile"]["error"][$ii] == 0) {
        $ext = pathinfo($_FILES["tripFile"]["name"][$ii], PATHINFO_EXTENSION);
        $newFile = ($timestamp + $ii) . ".{$ext}";
        $file = "./images/{$newFile}";
        if (move_uploaded_file($_FILES["tripFile"]["tmp_name"][$ii], $file)) {
            array_push($imgs, $newFile);
        }
    }
}

if (count($imgs) < 1) {
    alertAndBack("⚠️ 圖片搬移失敗，請重新嘗試");
    exit;
}

$sql = "INSERT INTO `trips` 
(`name`, `main_cate_id`, `sub_cate_id`, `region_id`, `city_id`, `info`, `description`, `price`, `stock`, `duration`, `booking_start_at`, `booking_end_at`, `published_at`) 
VALUES 
(:name, :main_cate_id, :sub_cate_id, :region_id, :city_id, :info, :description, :price, :stock, :duration, :booking_start_at, :booking_end_at, :published_at)";

$sqlNotice = "INSERT INTO `notices` (`trip_id`, `text`, `sort_order`) VALUES (?, ?, ?)";
$sqlImg = "INSERT INTO `trip_images` (`trip_id`, `file_name`, `sort_order`) VALUES (?, ?, ?)";




// 預處理器的寫法 + 複數的插入
try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ":name" => $name,
        ":main_cate_id" => $mainCate,
        ":sub_cate_id" => $subCate,
        ":region_id" => $region,
        ":city_id" => $city,
        ":info" => $info,
        ":description" => $description,
        ":price" => $price,
        ":stock" => $stock,
        ":duration" => $days,
        ":booking_start_at" => $startTime,
        ":booking_end_at" => $endTime,
        ":published_at" => $publishedTime
    ]);

    $tripId = $pdo->lastInsertId(); //拿到處理完的trip.id
    $stmtNotice = $pdo->prepare($sqlNotice);
    for ($i = 0; $i < $noticeCount; $i++) {
        $text = $notices[$i];
        $sortOrder = $i;
        $stmtNotice->execute([$tripId, $text, $sortOrder]);
    }

    $stmtImg = $pdo->prepare($sqlImg);
    for ($ii = 0; $ii < $countFile; $ii++) {
        $fileName = $imgs[$ii];
        $sortOrder = $ii;
        $stmtImg->execute([$tripId, $fileName, $sortOrder]);
    }

} catch (PDOException $e) {
    echo "錯誤: {{$e->getMessage()}}";
    exit;
}

alertGoBack("😻 新增資料成功");