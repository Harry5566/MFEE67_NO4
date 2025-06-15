<?php
require_once "./connect.php";
require_once "./utilities.php";

if(!isset($_POST["name"])){
  alertGoTo("請從正常管道進入", "./index.php");
  exit;
}

$name = $_POST["name"];
$region = $_POST["region"];
$city = $_POST["city"];
$type  = $_POST["type"];
$act = $_POST["act"];
$price = $_POST["price"];
$stock = $_POST["stock"];
$status = $_POST["status"];
$intro = $_POST["intro"];

if($name == ""){
  alertAndBack("請輸入商品名稱");
  exit;
};

if ($region == "") {
    alertAndBack("請選擇地區");
} else if ($city == "") {
    alertAndBack("請選擇城市");
};

if ($region == "") {
    alertAndBack("請選擇活動類型");
} else if ($city == "") {
    alertAndBack("請選擇活動子分類");
};

if($price == ""){
  alertAndBack("請輸入價格");
  exit;
};

if($stock == ""){
  alertAndBack("請輸入庫存數量");
  exit;
};

if(!isset($_POST["status"])){
  alertAndBack("請選擇地區與城市");
  exit;
}

$sql = "INSERT INTO `products` (name, price, stock, status_id, region_id, city_id, type_id, act_id, intro)VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?);";

try{
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$name, $price, $stock, $status, $region,$city, $type, $act, $intro]);
}catch(PDOException $e){
    echo "新資料失敗<br>";
    echo "Error:". $e->getMessage();
    exit;
}

echo "新增商品成功";
timeoutGoBack(1000);


