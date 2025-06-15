<?php
require_once "./connect.php";
require_once "./utilities.php";

if (!isset($_POST["id"])) {
  alertGoTo("請從正常管道進入", "./ticketIndex.php");
  exit;
}

if(!isset($_POST["region"])){
  alertAndBack("請選擇地區");
  exit;
}

if(!isset($_POST["city"])){
  alertAndBack("請選擇城市");
  exit;
}

if(!isset($_POST["type"])){
  alertAndBack("請選擇活動類型");
  exit;
}

if(!isset($_POST["act"])){
  alertAndBack("請選擇活動子分類");
  exit;
}

if(!isset($_POST["status"])){
  alertAndBack("請選擇商品狀態");
  exit;
}

$required = ["name", "price", "stock"];
$wordings = ["請填寫商品名稱", "請填寫價格", "請填寫庫存"];

foreach($required as $index => $value){
  if($_POST[$value] == ""){
    echo $wordings[$index];
    goBack();
    exit;
  }
}

$id = $_POST["id"];
$name = htmlspecialchars($_POST["name"]);
$region = intval($_POST["region"]);
$city = intval($_POST["city"]);
$type = intval($_POST["type"]);
$act = intval($_POST["act"]);
$price = htmlspecialchars($_POST["price"]);
$stock = htmlspecialchars($_POST["stock"]);
$status = intval($_POST["status"]);
$intro = htmlspecialchars($_POST["intro"]);

$sql = "UPDATE `products` SET `name` = ?, `region_id` = ?, `city_id` = ?, `type_id` = ?, `act_id` = ?, `price` = ?, `stock` = ?, `status_id` = ?, `intro` = ? WHERE `id` = ?";

$pathCount = count($_FILES["imagesFile"]["name"]);
$sqlImg = "INSERT INTO `images` (`product_id`, `path`) VALUES (?, ?)";
$susCount = 0;
$timestamp = time();

try{
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$name, $region, $city, $type, $act, $price, $stock, $status, $intro, $id]);
    
    for($i = 0; $i < $pathCount ;$i++){
    if($_FILES["imagesFile"]["error"][$i] == 0){
      $ext = pathinfo($_FILES["imagesFile"]["name"][$i], PATHINFO_EXTENSION);
      $newFile = ($timestamp + $i) .".{$ext}";
      $file = "./images/{$newFile}";
      if(move_uploaded_file($_FILES["imagesFile"]["tmp_name"][$i], $file)){
        $stmtImg = $pdo->prepare($sqlImg);
        $stmtImg->execute([$id, $newFile]);
        $susCount++;
      }
    }
  }
}catch(PDOException $e){
    echo "錯誤: {{$e->getMessage()}}";
    exit;
}

echo "修改商品成功";
echo "<script>
  setTimeout(()=>window.location = './ticketView.php?id={$id}', 1000);
</script>";