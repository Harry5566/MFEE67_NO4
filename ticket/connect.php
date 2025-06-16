<?php
$servername = "localhost";
$username = "admin";
$password = "a12345";
$dbname = "viet_tk";
$port = 3306;

try{
    $pdo = new PDO(
        "mysql:host={$servername};port={$port};dbname={$dbname};charset=utf8",
        $username,
        $password);
        
}catch(PDOException $e){
    echo "資料庫連線失敗<br>";
    echo"Error:" .$e->getMessage();
    exit;
}

