<?php
require_once "./connect.php";
require_once "./utilities.php";

if(!isset($_POST["name"])){
  alertGoTo("請從正常管道進入", "./ticketAdd.php");
  exit;
}

$name = htmlspecialchars($_POST["name"]);
$region = intval($_POST["region"]);
$city = intval($_POST["city"]);
$type = intval($_POST["type"]);
$act = intval($_POST["act"]);
$price = intval($_POST["price"]);
$stock = intval($_POST["stock"]);
$status = intval($_POST["status"]);
$intro = htmlspecialchars($_POST["intro"]);

if($name == ""){
  alertAndBack("請輸入商品名稱");
  exit;
};

if ($region == "") {
    alertAndBack("請選擇地區");
    exit;
} else if ($city == "") {
    alertAndBack("請選擇城市");
    exit;
};

if ($type == "") {
    alertAndBack("請選擇活動類型");
    exit;
} else if ($act == "") {
    alertAndBack("請選擇活動子分類");
    exit;
};

if($price == ""|| intval($price) < 0){
  alertAndBack("請輸入有效價格");
  exit;
};

if($stock == ""|| intval($stock) < 0){
  alertAndBack("請輸入有效庫存數量");
  exit;
};

if(!isset($_POST["status"])){
  alertAndBack("請選擇商品狀態");
  exit;
}

if (empty($_FILES["imagesFile"]["name"][0]) || $_FILES["imagesFile"]["error"][0] == UPLOAD_ERR_NO_FILE) {
  alertAndBack("請至少選擇一張圖片");
  exit;
}

$imgs = [];
$pathCount = count($_FILES["imagesFile"]["name"]);
$timestamp = time(); //設定變數為時間戳記


for($i = 0; $i < $pathCount ;$i++){
    if($_FILES["imagesFile"]["error"][$i] == 0){
      $ext = pathinfo($_FILES["imagesFile"]["name"][$i], PATHINFO_EXTENSION); //pathinfo()取檔案名功能裡選PATHINFO_EXTENSION為取副檔名
      $newFilename = ($timestamp + $i) .".{$ext}"; //新檔名為 時間戳記+加副檔名($ext)
      $file = "./images/{$newFilename}";
      if(move_uploaded_file($_FILES["imagesFile"]["tmp_name"][$i], $file)){
          array_push($imgs, $newFilename);
      }    
    }
}

$sql = "INSERT INTO `products` (name, price, stock, status_id, region_id, city_id, type_id, act_id, intro) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
$sqlImg = "INSERT INTO `images` (`path`, `product_id`) VALUES (?, ?)";  


try{
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$name, $price, $stock, $status, $region, $city, $type, $act, $intro]);
    $newProductId = $pdo->lastInsertId();

    if ($newProductId && count($imgs) > 0) {
        $stmtImg = $pdo->prepare($sqlImg);
        for ($i = 0; $i < count($imgs); $i++) {
            $path = $imgs[$i];
            $stmtImg->execute([$path, $newProductId]);
        }
    }

}catch(PDOException $e){
    echo "新資料失敗:". $e->getMessage();
    exit;
}

echo "新增商品成功";
echo "<script>
  setTimeout(()=>window.location = './ticketView.php?id={$newProductId}', 1000);
</script>";


