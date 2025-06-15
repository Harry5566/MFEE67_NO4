<?php
require_once "./connect.php";
require_once "./utilities.php";


date_default_timezone_set("Asia/Taipei");

$sqlUpdatetime = "UPDATE `trips`
                SET unpublished_at = 
                    CASE 
                        WHEN NOW() >= booking_end_at THEN booking_end_at
                        ELSE NULL
                    END
                WHERE booking_end_at IS NOT NULL AND unpublished_at IS NULL";
$pdo->exec($sqlUpdatetime);

$conditions[] = "t.is_valid = 1";
$statuses = [];
$values = [];
$sortConditions = [];

$status = $_GET["status"] ?? "";
$search = $_GET["search"] ?? "";
$mcid = intval($_GET["mcid"] ?? 0);
$scid = intval($_GET["scid"] ?? 0);
$rid = intval($_GET["rid"] ?? 0);
$cid = intval($_GET["cid"] ?? 0);
$days = intval($_GET["days"] ?? 0);
$sTime = $_GET["sTime"] ?? null;
$eTime = $_GET["eTime"] ?? null;
$pDate = $_GET["pDate"] ?? null;

$bfilter = $_GET["bfilter"] ?? null;
$priceSort = $_GET["psort"] ?? null;
$stockSort = $_GET["ssort"] ?? null;
$viewSort = $_GET["vsort"] ?? null;

$queryParams = $_GET;
unset($queryParams['page']);
$queryString = http_build_query($queryParams);

$linkBase = "./index.php?";
$pageLink = $queryString ? "$linkBase?$queryString" : $linkBase;

$now = time();

if (isset($status) && $status !== "") {
  switch ($status) {
    case "on":
      $statuses[] = "(t.unpublished_at IS NULL) AND (t.published_at <= NOW())";
      break;
    case "not-yet":
      $statuses[] = "published_at > NOW()";
      break;
    case 'off':
      $statuses[] = "t.unpublished_at IS NOT NULL";
      break;
  }
}

if (!empty($statuses)) {
  $conditions[] = implode(" AND ", $statuses);
} else {
  $conditions[] = "(t.unpublished_at IS NULL) AND (t.published_at <= NOW())";
}

if (!empty($search)) {
  $conditions[] = "(t.name LIKE :searchName OR t.description LIKE :searchContent)";
  $keyword = "%$search%";
  $values["searchName"] = $keyword;
  $values["searchContent"] = $keyword;
}

if ($mcid !== 0) {
  $conditions[] = "t.main_cate_id = :mcid";
  $values["mcid"] = $mcid;
}

if ($scid !== 0) {
  $conditions[] = "t.sub_cate_id = :scid";
  $values["scid"] = $scid;
}

if ($rid !== 0) {
  $conditions[] = "t.region_id = :rid";
  $values["rid"] = $rid;
}

if ($cid !== 0) {
  $conditions[] = "t.city_id = :cid";
  $values["cid"] = $cid;
}

if ($days !== 0) {
  $conditions[] = "t.duration = :days";
  $values["days"] = $days;
}

if (!empty($sTime) && !empty($eTime)) {
  $sTimeSwitch = new DateTime($sTime);
  $eTimeSwitch = new DateTime($eTime);

  $sTimeFormatted = $sTimeSwitch->format("Y-m-d H:i:s");
  $eTimeFormatted = $eTimeSwitch->format("Y-m-d H:i:s");

  $conditions[] = "(t.booking_start_at >= :sTime) AND (t.booking_end_at <= :eTime)";
  $values["sTime"] = $sTimeFormatted;
  $values["eTime"] = $eTimeFormatted;
} elseif (!empty($sTime) || !empty($eTime)) {
  alertAndBack("⚠️ 請同時選擇販售開始與販售結束時間進行查詢");
  exit;
}

if (!empty($pDate)) {
  $conditions[] = "DATE(t.published_at) = :pDate";
  $values["pDate"] = $pDate;
}

if (!empty($bfilter)) {
  switch ($bfilter) {
    case "on":
      $conditions[] = "(t.booking_start_at < NOW()) AND (t.booking_end_at > NOW()) AND (t.unpublished_at IS NULL) AND (t.stock <> 0)";
      break;
    case "not-yet":
      $conditions[] = "t.booking_start_at > NOW()";
      break;
    case 'off':
      $conditions[] = "t.stock = 0";
      break;
  }
}

if (!empty($priceSort)) {
  switch ($priceSort) {
    case "asc":
      $sortConditions[] = "t.price ASC";
      break;
    case 'desc':
      $sortConditions[] = "t.price DESC";
      break;
  }
}

if (!empty($stockSort)) {
  switch ($stockSort) {
    case "asc":
      $sortConditions[] = "t.stock ASC";
      break;
    case 'desc':
      $sortConditions[] = "t.stock DESC";
      break;
  }
}

if (!empty($viewSort)) {
  switch ($viewSort) {
    case "asc":
      $sortConditions[] = "t.views ASC";
      break;
    case 'desc':
      $sortConditions[] = "t.views DESC";
      break;
  }
}


// 之後是否調整下拉式選單，可選10, 25, 50
$perPage = 25;
$page = intval($_GET["page"] ?? 1);
$pageStart = ($page - 1) * $perPage;

$whereSQL = "";
if (!empty($conditions)) {
  $whereSQL = "WHERE " . implode(" AND ", $conditions);
}

$orderbySQL = "";
if (!empty($sortConditions)) {
  $orderbySQL = "ORDER BY " . implode(", ", $sortConditions);
}

$sql = "SELECT 
          t.id, t.name, 
          t.main_cate_id, 
          t.sub_cate_id, 
          t.region_id, 
          t.city_id, 
          t.info, 
          t.description, 
          t.duration,
          t.price, 
          t.stock, 
          t.booking_start_at,
          t.booking_end_at,
          t.views,
          t.published_at,
          t.unpublished_at,
          t.updated_at,
          t.is_valid, 
          main_cate.name AS main_cate_name,
          sub_cate.name AS sub_cate_name,
          regions.name AS region_name,
          cities.name AS city_name
        FROM trips AS t
        LEFT JOIN main_cate
        ON t.main_cate_id = main_cate.id
        LEFT JOIN sub_cate 
        ON t.sub_cate_id = sub_cate.id
        LEFT JOIN regions
        ON t.region_id = regions.id
        LEFT JOIN cities
        ON t.city_id = cities.id
        $whereSQL $orderbySQL
        LIMIT $perPage OFFSET $pageStart";
$sqlAll = "SELECT * FROM `trips` AS t $whereSQL";
$sqlMainCate = "SELECT * FROM `main_cate`";
$sqlSubCate = "SELECT * FROM `sub_cate`";
$sqlRegion = "SELECT * FROM `regions`";
$sqlCity = "SELECT * FROM `cities`";
$sqlDays = "SELECT * FROM `trips` AS t $whereSQL GROUP BY t.duration";

try {
  $stmt = $pdo->prepare($sql);
  $stmt->execute($values);
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

  $stmtAll = $pdo->prepare($sqlAll);
  $stmtAll->execute($values);
  $totalCount = $stmtAll->rowCount();

  $stmtMainCate = $pdo->prepare($sqlMainCate);
  $stmtMainCate->execute();
  $rowsMainCate = $stmtMainCate->fetchAll(PDO::FETCH_ASSOC);

  $stmtSubCate = $pdo->prepare($sqlSubCate);
  $stmtSubCate->execute();
  $rowsSubCate = $stmtSubCate->fetchAll(PDO::FETCH_ASSOC);

  $stmtRegion = $pdo->prepare($sqlRegion);
  $stmtRegion->execute();
  $rowsRegion = $stmtRegion->fetchAll(PDO::FETCH_ASSOC);

  $stmtCity = $pdo->prepare($sqlCity);
  $stmtCity->execute();
  $rowsCity = $stmtCity->fetchAll(PDO::FETCH_ASSOC);

  $stmtDays = $pdo->prepare($sqlDays);
  $stmtDays->execute($values);
  $rowsDays = $stmtDays->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  echo "錯誤: {{$e->getMessage()}}";
  exit;
}

$totalPage = ceil($totalCount / $perPage);
$pageEnd = min($pageStart + $perPage, $totalCount);

// 分頁顯示範圍(5)
$startPage = max(1, $page - 2);
$endPage = min($totalPage, $page + 2);

?>

<!doctype html>

<html lang="en" class="layout-menu-fixed layout-compact" data-assets-path="../assets/"
  data-template="vertical-menu-template-free">

<head>
  <meta charset="utf-8" />
  <meta name="viewport"
    content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

  <title>商品管理</title>

  <meta name="description" content="" />

  <!-- Favicon -->
  <link rel="icon" type="image/x-icon" href="../logo.png" />

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
    rel="stylesheet" />

  <link rel="stylesheet" href="../assets/vendor/fonts/iconify-icons.css" />


  <!-- build:css assets/vendor/css/theme.css  -->

  <link rel="stylesheet" href="../assets/css/demo.css" />

  <!-- Vendors CSS -->

  <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

  <!-- endbuild -->

  <link rel="stylesheet" href="../assets/vendor/libs/apex-charts/apex-charts.css" />

  <!-- font awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
    integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />

  <!-- Helpers -->
  <script src="../assets/vendor/js/helpers.js"></script>
  <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->

  <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->

  <script src="../assets/js/config.js"></script>


  <!-- Boxicons css -->
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

  <!-- Core CSS -->
  <link rel="stylesheet" href="../assets/vendor/css/core.css" />

  <!-- custom 自定義 CSS -->
  <link rel="stylesheet" href="../custom.css">
</head>

<body>
  <!-- Layout wrapper -->
  <div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">


      <!-- Menu -->

      <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
        <div class="app-brand demo">
          <a href="./index.php" class="app-brand-link">
            <span>
              <span><img class="w-40px h-40px" src="../logo.png" alt=""></span>
            </span>
            <span class="fs-4 fw-bold ms-2 app-brand-text demo menu-text align-items-center">xin_chào</span>
          </a>

          <a href="javascript:void(0);" class="layout-menu-toggle menu-link ms-auto">
            <i class="bx bx-chevron-left d-block d-xl-none align-middle"></i>
          </a>
        </div>

        <div class="menu-divider mt-0"></div>

        <div class="menu-inner-shadow"></div>

        <ul class="menu-inner py-1">
          <!-- 會員管理 -->
          <li class="menu-item">
            <a href="#" class="menu-link menu-toggle">
              <i class="fa-solid fa-users me-3 menu-text"></i>
              <div class="menu-text fs-5" data-i18n="Dashboards">會員管理</div>
            </a>
            <ul class="menu-sub">
              <li class="menu-item active">
                <a href="#" class="menu-link">
                  <div class="menu-text fs-6" data-i18n="Analytics">會員列表</div>
                </a>
              </li>
              <li class="menu-item">
                <a href="#" class="menu-link">
                  <div class="menu-text fs-6" data-i18n="Analytics">停權會員帳號</div>
                </a>
              </li>
            </ul>
          </li>

          <!-- 商品管理 -->
          <li class="menu-item active open">
            <a href=".index.php" class="menu-link menu-toggle">
              <i class="fa-solid fa-map-location-dot me-3 menu-text"></i>
              <div class="menu-text fs-5" data-i18n="Layouts">商品管理</div>
            </a>

            <ul class="menu-sub">
              <li class="menu-item active">
                <a href="./index.php" class="menu-link">
                  <div class="menu-text fw-bold" data-i18n="Without menu">行程列表</div>
                </a>
              </li>
              <li class="menu-item">
                <a href="./addTrip.php" class="menu-link">
                  <div class="menu-text fw-bold" data-i18n="Without menu">新增行程</div>
                </a>
              </li>
            </ul>
          </li>

          <!-- 票券管理 -->
          <li class="menu-item">
            <a href="#" class="menu-link menu-toggle">
              <i class="fa-solid fa-ticket me-3 menu-text"></i>
              <div class="menu-text fs-5" data-i18n="Dashboards">票券管理</div>
            </a>
            <ul class="menu-sub">
              <li class="menu-item active">
                <a href="#" class="menu-link">
                  <div class="menu-text fw-bold" data-i18n="Analytics">票券列表</div>
                </a>
              </li>
              <li class="menu-item">
                <a href="#" class="menu-link">
                  <div class="menu-text fw-bold" data-i18n="Analytics">新增票券</div>
                </a>
              </li>
            </ul>
          </li>

          <!-- 優惠券管理 -->
          <li class="menu-item">
            <a href="#" class="menu-link menu-toggle">
              <i class="fa-solid fa-tags me-3 menu-text"></i>
              <div class="menu-text fs-5" data-i18n="Dashboards">優惠券管理</div>
            </a>
            <ul class="menu-sub">
              <li class="menu-item active">
                <a href="#" class="menu-link">
                  <div class="menu-text fw-bold" data-i18n="Analytics">優惠券列表</div>
                </a>
              </li>
              <li class="menu-item">
                <a href="#" class="menu-link">
                  <div class="menu-text fw-bold" data-i18n="Analytics">新增優惠券</div>
                </a>
              </li>
            </ul>
          </li>
          <!-- 登出(要等建立帳號) -->
          <!-- <li class="menu-header small text-uppercase">
            <span class="menu-text fw-bold">會員資訊</span>
          </li>
          <div class="container text-center">
            <div class="d-flex justify-content-center gap-3 mb-3">
              <img class="head" src="../logo.png?>" alt="">
              <div class="menu-text fw-bold align-self-center">suxing測試</div>
            </div>
            <li class="menu-item row justify-content-center">
              <a href="../doLogout.php"
                class="btn rounded-pill btn-gradient-success btn-ban col-10 justify-content-center">
                <div class="menu-text fw-bold"><i class="fa-solid fa-arrow-right-from-bracket me-2"></i>登出</div>
              </a>
            </li>
          </div> -->
        </ul>
      </aside>
      <!-- / Menu -->

      <!-- Layout container -->
      <div class="layout-page container">
        <!-- Navbar -->
        <div class="d-flex align-items-center">
          <span class="layout-menu-toggle align-items-xl-center m-4 d-xl-none">
            <a class="me-xl-6 text-primary" href="javascript:void(0)">
              <i class="icon-base bx bx-menu icon-md"></i>
            </a>
          </span>
          <nav aria-label="breadcrumb" class="mt-4 m-xl-6">
            <!-- 需要調整文字和active的顏色 -->
            <ol class="breadcrumb">
              <li class="breadcrumb-item">
                <a href="#" class="text-primary">Home</a>
              </li>
              <li class="breadcrumb-item">
                <a href="./index.php" class="text-primary">商品管理</a>
              </li>
              <li class="breadcrumb-item active" class="text-primary">商品列表</li>
            </ol>
          </nav>
        </div>

        <div class="content-wrapper">
          <div class="container-xxl flex-grow-1 container-p-y no-padding-container">
            <!-- 訊息狀態切換選單 -->
            <ul class="nav nav-pills mb-2" role="tablist">
              <li class="nav-item me-1">
                <button type="button" name="status" data-status="on"
                  class="nav-link <?= $status == "on" || $status == "" ? "active" : "" ?>" role="tab"
                  data-bs-toggle="tab" data-bs-target="#navs-pills-top-home" aria-controls="navs-pills-top-home"
                  aria-selected="true">
                  上架中
                </button>
              </li>
              <li class="nav-item me-1">
                <button type="button" name="status" data-status="not-yet"
                  class="nav-link <?= $status == "not-yet" ? "active" : "" ?>" role="tab" data-bs-toggle="tab"
                  data-bs-target="#navs-pills-top-profile" aria-controls="navs-pills-top-profile" aria-selected="false">
                  未上架
                </button>
              </li>
              <li class="nav-item">
                <button type="button" name="status" data-status="off"
                  class="nav-link <?= $status == "off" ? "active" : "" ?>" role="tab" data-bs-toggle="tab"
                  data-bs-target="#navs-pills-top-messages" aria-controls="navs-pills-top-messages"
                  aria-selected="false">
                  下架
                </button>
              </li>
            </ul>

            <!-- 篩選/搜尋 -->
            <div class="sticky-top bg-white my-5 px-5 py-3 rounded-3">
              <div class="align-items-center mt-3">

                <!-- 搜尋 -->
                <div class="row">
                  <div class="col-xl-3 col-lg-12">
                    <div class="input-group rounded-pill search-box mb-3">
                      <span class="input-group-text border border-warning"><i
                          class="fas fa-search text-warning"></i></span>
                      <input type="text" class="form-control placeholder-color border border-warning text-primary w-50"
                        placeholder="輸入行程標題或內文關鍵字" name="search" value="<?= htmlspecialchars($search) ?>">
                    </div>
                  </div>

                  <!-- 主類別篩選 -->
                  <div class="col-xl-2 col-lg-2 col-md-2 col-sm-6 mb-3">
                    <select class="form-select fw-bold text-primary" name="mainCate">
                      <option selected disabled value="">主類別</option>
                      <?php foreach ($rowsMainCate as $rowMainCate): ?>
                        <option value="<?= $rowMainCate["id"] ?>"><?= $rowMainCate["name"] ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>

                  <!-- 子類別篩選 -->
                  <div class="col-xl-2 col-lg-2 col-md-2 col-sm-6 mb-3">
                    <select class="form-select fw-bold text-primary" name="subCate">
                      <option selected disabled value="">子分類</option>
                    </select>
                  </div>

                  <!-- 地區篩選 -->
                  <div class="col-xl-2 col-lg-2 col-md-2 col-sm-6 mb-3">
                    <select class="form-select fw-bold text-primary" name="region">
                      <option selected disabled value="">地區</option>
                      <?php foreach ($rowsRegion as $rowRegion): ?>
                        <option value="<?= $rowRegion["id"] ?>"><?= $rowRegion["name"] ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>

                  <!-- 城市篩選 -->
                  <div class="col-xl-2 col-lg-2 col-md-2 col-sm-6 mb-3">
                    <select class="form-select fw-bold text-primary" name="city">
                      <option selected disabled value="">城市</option>
                    </select>
                  </div>


                  <!-- 行程天數篩選 -->
                  <div class="col-xl-1 col-lg-4 col-md-4 col-sm-6 mb-3">
                    <select class="form-select fw-bold text-primary" name="days">
                      <option selected disabled value="">天數</option>
                      <?php foreach ($rowsDays as $rowDays): ?>
                        <option value="<?= $rowDays["duration"] ?>"><?= $rowDays["duration"] ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>

                </div>

                <hr class="mt-2">
                <div class="row my-4">
                  <label class="col-sm-1 col-form-label text-primary fw-bold"><i
                      class="fa-solid fa-calendar-days me-2"></i>販售開始</label>
                  <div class="col-md-5">
                    <div class="d-flex">
                      <input required name="start-at" class="form-control start-at" type="datetime-local"
                        value="<?= $sTime ?>" id="html5-datetime-local-input" />
                    </div>
                  </div>
                  <label class="col-sm-1 col-form-label text-primary fw-bold"><i
                      class="fa-solid fa-calendar-days me-2"></i>販售結束</label>
                  <div class="col-md-5">
                    <div class="d-flex">
                      <input required name="end-at" class="form-control end-at" type="datetime-local"
                        value="<?= $eTime ?>" id="html5-datetime-local-input" />
                    </div>
                  </div>
                </div>

                <div class="row mb-3">
                  <label class="col-sm-1 col-form-label text-primary fw-bold"><i
                      class="fa-solid fa-calendar-days me-2"></i>上架日期</label>
                  <div class="col-md-5">
                    <input required name="published-at" class="form-control published-at" type="date"
                      value="<?= $pDate ?>" id="html5-datetime-local-input" />
                  </div>
                </div>


              </div>

              <div>
                <div class="row justify-content-end text-end">
                  <div class="col-sm-11">
                    <button type="button" class="btn btn-outline-primary fw-bold me-3 btn-search">
                      <i class="fas fa-search me-1"></i>搜尋
                    </button>
                    <a href="./index.php" class="btn btn-outline-success fw-bold me-3">清除</a>
                    <a href="./addTrip.php" class="btn btn-primary">
                      <i class="fa-solid fa-heart-circle-plus me-2"></i>新增行程
                    </a>
                  </div>
                </div>
              </div>
            </div>


            <!-- 商品列表 -->
            <div class="nav-align-top">
              <div class="tab-content py-2 rounded-3">
                <div class="d-flex align-items-center justify-content-between mb-3">
                  <div class=" fw-bold fs-5 text-primary mb-3 mt-0"><i class="fa-solid fa-location-dot me-3"></i>行程列表
                  </div>
                  <!-- 分頁 -->
                  <div class="mt-2">
                    <nav aria-label="Page navigation">
                      <ul class="pagination pagination-sm justify-content-center">

                        <li class="page-item prev <?= $page <= 1 ? 'disabled' : '' ?>">
                          <a class="page-link" href="<?= $pageLink ?>&page=<?= max(1, $page - 1) ?>"><i
                              class="icon-base bx bx-chevrons-left icon-xs"></i></a>
                        </li>
                        <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                          <li class="page-item <?= $page == $i ? "active" : "" ?>">
                            <a class=" page-link" href="<?= $pageLink ?>&page=<?= $i ?>"><?= $i ?></a>
                          </li>
                        <?php endfor; ?>
                        <li class="page-item next <?= $page >= $totalPage ? 'disabled' : '' ?>">
                          <a class="page-link" href="<?= $pageLink ?>&page=<?= min($totalPage, $page + 1) ?>"><i
                              class="icon-base bx bx-chevrons-right icon-xs"></i></a>
                        </li>

                      </ul>
                    </nav>
                  </div>
                  <div class="mb-3 mt-0">
                    <span class="ms-6 my-2 text-primary">目前共&nbsp;<?= $totalCount ?>&nbsp;筆資料</span>
                  </div>
                </div>
                <div class="tab-pane fade show active table-responsive full-width-card" id="navs-pills-top-home"
                  role="tabpanel">
                  <table class="table align-middle text-nowrap w-100 mb-4">
                    <thead>
                      <tr>
                        <th class="text-primary text-center fs-6">#</th>
                        <th class="text-primary text-center fs-6">
                          <div class="d-flex align-items-center justify-content-center">
                            <span>販售狀態</span>
                            <?php if ($status !== "not-yet" && $status !== "off"): ?>
                              <div class="d-flex flex-column bfilter-button ms-2" data-sort="" role="button">
                                <i class="fa-solid fa-caret-up fs-8px"></i>
                                <i class="fa-solid fa-caret-down fs-8px"></i>
                              </div>
                            <?php endif; ?>
                          </div>
                        </th>
                        <th class="text-primary text-center fs-6">行程名稱</th>
                        <th class="text-primary text-center fs-6">主類別</th>
                        <th class="text-primary text-center fs-6">子類別</th>
                        <th class="text-primary text-center fs-6">地區</th>
                        <th class="text-primary text-center fs-6">城市</th>
                        <th class="text-primary text-center fs-6">天數</th>
                        <th class="text-primary text-center fs-6">
                          <div class="d-flex align-items-center justify-content-center">
                            <span class="me-2">價格</span>
                            <div class="d-flex flex-column psort-button" data-sort="" role="button">
                              <i class="fa-solid fa-caret-up fs-8px"></i>
                              <i class="fa-solid fa-caret-down fs-8px"></i>
                            </div>
                          </div>
                        </th>
                        <th class="text-primary text-center fs-6">
                          <div class="d-flex align-items-center justify-content-center">
                            <span class="me-2">名額</span>
                            <div class="d-flex flex-column ssort-button" data-sort="" role="button">
                              <i class="fa-solid fa-caret-up fs-8px"></i>
                              <i class="fa-solid fa-caret-down fs-8px"></i>
                            </div>
                          </div>
                        </th>
                        <th class="text-primary">
                          <div class="d-flex align-items-center justify-content-center fs-6">
                            <span class="me-2">瀏覽人次</span>
                            <div class="d-flex flex-column vsort-button" data-sort="" role="button">
                              <i class="fa-solid fa-caret-up fs-8px"></i>
                              <i class="fa-solid fa-caret-down fs-8px"></i>
                            </div>
                          </div>
                        </th>
                        <th class="text-primary text-center fs-6">操作</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">

                      <?php foreach ($rows as $index => $row): ?>
                        <tr>
                          <td class="text-center"><?= $index + 1 + ($page - 1) * $perPage ?></td>

                          <td class="text-center">
                            <?php
                            $startTime = strtotime($row["booking_start_at"]);
                            $endTime = strtotime($row["booking_end_at"]);
                            $offTime = strtotime($row["unpublished_at"]);
                            $stock = intval($row["stock"]);
                            if ($startTime > $now): ?>
                              <span class="badge bg-label-info rounded-pill me-1">尚未販售</span>
                            <?php elseif ($startTime < $now && $now < $endTime && $offTime == null && $stock !== 0): ?>
                              <span class="badge bg-label-warning rounded-pill me-1">販售中</span>
                            <?php elseif ($stock == 0): ?>
                              <span class="badge bg-label-primary rounded-pill me-1">售完</span>
                            <?php elseif ($now > $endTime || $offTime !== null): ?>
                              <span class="badge bg-label-success rounded-pill me-1">販售結束</span>
                            <?php endif; ?>
                          </td>

                          <td class="fw-bold text-start text-truncate truncate-td"><?= $row["name"] ?></td>
                          <td class="text-center"><?= $row["main_cate_name"] ?></td>
                          <td class="text-center"><?= $row["sub_cate_name"] ?></td>
                          <td class="text-center"><?= $row["region_name"] ?></td>
                          <td class="text-center"><?= $row["city_name"] ?></td>
                          <td class="text-center"><?= $row["duration"] ?></td>
                          <td class="text-center"><?= $row["price"] ?>元</td>
                          <td class="text-center"><?= $row["stock"] ?></td>
                          <td class="text-center"><?= $row["views"] ?></td>
                          <td class="text-center">
                            <div class="action-buttons d-flex align-items-center gap-2">
                              <a class="btn-circle btn btn-warning" href="./viewTrip.php?id=<?= $row["id"] ?>"><i
                                  class="bx bx-show-alt me-1 text-white"></i></a>
                              <?php if ($row["unpublished_at"] === null): ?>
                                <a class="btn-circle btn btn-info" href="./updateTrip.php?id=<?= $row["id"] ?>"><i
                                    class="bx bx-edit-alt me-1"></i></a>
                              <?php endif; ?>
                              <button type="button" class="btn-circle btn-del btn btn-success" data-bs-toggle="modal"
                                data-bs-target="#deleteModal" data-id="<?= $row["id"] ?>"
                                data-name="<?= $row["name"] ?>"><i class="bx bx-trash"></i></button>
                            </div>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>

                <div class="d-flex align-items-center justify-content-between mb-3">
                  <div class="mb-0 mt-3">

                    <span class="ms-2 text-primary">顯示第&nbsp;<?= $pageStart + 1 ?> -
                      <?= $pageEnd ?>&nbsp;筆資料，共&nbsp;<?= $totalCount ?>&nbsp;筆資料</span>

                  </div>
                  <!-- 分頁 -->
                  <div class="demo-inline-spacing">
                    <nav aria-label="Page navigation">
                      <ul class="pagination pagination-sm justify-content-end">
                        <li class="page-item prev <?= $page <= 1 ? 'disabled' : '' ?>">
                          <a class="page-link" href="<?= $pageLink ?>&page=<?= max(1, $page - 1) ?>"><i
                              class="icon-base bx bx-chevrons-left icon-xs"></i></a>
                        </li>
                        <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                          <li class="page-item <?= $page == $i ? "active" : "" ?>">
                            <a class=" page-link" href="<?= $pageLink ?>&page=<?= $i ?>"><?= $i ?></a>
                          </li>
                        <?php endfor; ?>
                        <li class="page-item next <?= $page >= $totalPage ? 'disabled' : '' ?>">
                          <a class="page-link" href="<?= $pageLink ?>&page=<?= min($totalPage, $page + 1) ?>"><i
                              class="icon-base bx bx-chevrons-right icon-xs"></i></a>
                        </li>
                      </ul>
                    </nav>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- Footer -->
          <footer class="content-footer footer bg-footer-theme">
            <div class="container-fluid">
              <div
                class="footer-container d-flex align-items-center justify-content-between py-4 flex-md-row flex-column">
                <div class="mb-2 mb-md-0">
                  Copyright ©
                  <script>
                    document.write(new Date().getFullYear());
                  </script>
                  <a href="./index.php" target="_blank" class="footer-link">心橋❤️</a>
                  by 前端67-第四組
                </div>
                <div class="d-none d-lg-inline-block">
                  <a href="./index.php" target="_blank" class="footer-link me-4">關於我們</a>
                  <a href="./index.php" class="footer-link me-4" target="_blank">相關服務</a>
                  <a href="./index.php" target="_blank" class="footer-link">進階設定</a>
                </div>
              </div>
            </div>
          </footer>
          <!-- / Footer -->

        </div>
      </div>
    </div>




    <!-- Overlay -->
    <div class="layout-overlay layout-menu-toggle"></div>

    <!-- Delete Confirmation Modal -->

    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              <i class="fas fa-trash me-2"></i>刪除商品
            </h5>
          </div>
          <div class="modal-body">
            <div class="text-center">
              <i class="fas fa-exclamation-triangle text-warning" style="font-size: 3rem;"></i>
              <h4 class="mt-3">確定要刪除此商品行程嗎？</h4>
              <p class="text-muted text-warning">行程名稱：<span class="fw-bold" id="deleteTripName">從js放</span></p>
            </div>
            <input type="hidden" id="deleteTripId">
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-gradient-primary" id="confirmDelete">
              <i class="fas fa-trash me-2"></i>確認刪除
            </button>
            <button type="button" class="btn btn-gradient-info" data-bs-dismiss="modal">取消</button>
          </div>
        </div>
      </div>
    </div>

  </div>


  <!-- Core JS -->

  <script src="../assets/vendor/libs/jquery/jquery.js"></script>

  <script src="../assets/vendor/libs/popper/popper.js"></script>
  <script src="../assets/vendor/js/bootstrap.js"></script>

  <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>

  <script src="../assets/vendor/js/menu.js"></script>

  <!-- endbuild -->

  <!-- Vendors JS -->
  <script src="../assets/vendor/libs/apex-charts/apexcharts.js"></script>

  <!-- Main JS -->

  <script src="../assets/js/main.js"></script>

  <!-- Page JS -->
  <script src="../assets/js/dashboards-analytics.js"></script>

  <!-- Place this tag before closing body tag for github widget button. -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>

  <script>
    const statusBtn = document.querySelectorAll("button[name=status]");
    const inputContent = document.querySelector("input[name=search]");
    const searchMainCate = document.querySelector("select[name=mainCate]")
    const searchSubCate = document.querySelector("select[name=subCate]")
    const searchRegion = document.querySelector("select[name=region]")
    const searchCity = document.querySelector("select[name=city]")
    const searchDays = document.querySelector("select[name=days]")
    const searchStartTime = document.querySelector("input[name='start-at']")
    const searchEndTime = document.querySelector("input[name='end-at']")
    const searchPublishedTime = document.querySelector("input[name='published-at']")
    const btnSearch = document.querySelector(".btn-search");
    const bFilterBtn = document.querySelector(".bfilter-button");
    const psortBtn = document.querySelector(".psort-button");
    const ssortBtn = document.querySelector(".ssort-button");
    const vsortBtn = document.querySelector(".vsort-button");

    //主/子選單製作
    let subs = [];
    let cities = [];

    subs = <?php echo json_encode($rowsSubCate) ?>;
    cities = <?php echo json_encode($rowsCity) ?>;

    const selectMain = document.querySelector("select[name=mainCate]");
    const selectSub = document.querySelector("select[name=subCate]");
    const selectRegion = document.querySelector("select[name=region]");
    const selectCity = document.querySelector("select[name=city]");

    const startInput = document.querySelector(".start-at");
    const endInput = document.querySelector(".end-at");
    const publishInput = document.querySelector(".published-at");

    const btnDels = document.querySelectorAll(".btn-del");
    const deleteModalElement = document.querySelector("#deleteModal");
    const deleteTripId = document.querySelector("#deleteTripId");
    const deleteTripName = document.querySelector("#deleteTripName");
    const btnConfirmDels = document.querySelector("#confirmDelete");

    const deleteModal = new bootstrap.Modal(deleteModalElement);


    //上下架狀態判斷功能
    statusBtn.forEach(btn => {
      btn.addEventListener("click", function () {
        const status = btn.dataset.status;
        window.location.href = `./index.php?status=${status}`;
      })
    });

    // 設定交叉搜尋
    btnSearch.addEventListener("click", function () {
      const urlParams = new URLSearchParams(window.location.search);
      const status = urlParams.get("status");

      const query = inputContent.value.trim();
      const maincate = searchMainCate.value;
      const subcate = searchSubCate.value;
      const region = searchRegion.value;
      const city = searchCity.value;
      const days = searchDays.value;
      const startTime = searchStartTime.value;
      const endTime = searchEndTime.value;
      const publishedTime = searchPublishedTime.value;

      updateParam(urlParams, "search", query);
      updateParam(urlParams, "mcid", maincate);
      updateParam(urlParams, "scid", subcate);
      updateParam(urlParams, "rid", region);
      updateParam(urlParams, "cid", city);
      updateParam(urlParams, "days", days);
      updateParam(urlParams, "sTime", startTime);
      updateParam(urlParams, "eTime", endTime);
      updateParam(urlParams, "pDate", publishedTime);

      urlParams.set("page", 1);

      window.location.href = `./index.php?${urlParams.toString()}`;
    });

    // 網址參數判斷
    function updateParam(urlParams, key, value) {
      if (value) {
        urlParams.set(key, value);
      } else {
        urlParams.delete(key);
      }
    }

    // 販售狀態的分類篩選
    if (bFilterBtn) {
      bFilterBtn.addEventListener("click", () => {
        const urlParams = new URLSearchParams(window.location.search);
        const currentFilter = urlParams.get("bfilter") || "";
        let newFilter = setFilterData(currentFilter);

        urlParams.delete("psort");
        urlParams.delete("ssort");
        urlParams.delete("vsort");

        urlParams.set("bfilter", newFilter);
        urlParams.set("page", 1);
        window.location.href = `./index.php?${urlParams.toString()}`;
      })
    }


    function setFilterData(current) {
      if (current === "" || current === "off") {
        return "on";
      } else if (current === "on") {
        return "not-yet";
      } else if (current === "not-yet") {
        return "off";
      }
    }

    // 設定排序
    psortBtn.addEventListener("click", () => {
      sorts("psort");
    });

    ssortBtn.addEventListener("click", () => {
      sorts("ssort");
    });

    vsortBtn.addEventListener("click", () => {
      sorts("vsort");
    });

    function sorts(getkey) {
      const urlParams = new URLSearchParams(window.location.search);

      const currentSort = urlParams.get(getkey) || "";
      let newSort = setSortData(currentSort);

      urlParams.delete("psort");
      urlParams.delete("ssort");
      urlParams.delete("vsort");

      urlParams.set("page", 1);
      urlParams.set(getkey, newSort); //加入前面判定後的網址參數
      window.location.href = `./index.php?${urlParams.toString()}`;
    }

    function setSortData(current) {
      if (current === "" || current === "desc") {
        return "asc";
      } else {
        return "desc";
      }
    }

    selectMain.addEventListener("change", function () {
      setSubMenu(this.value);
    })

    selectRegion.addEventListener("change", function () {
      setCityMenu(this.value);
    })

    function setSubMenu(id) {
      const ary = subs.filter(sub => sub.main_cate_id == id);
      selectSub.innerHTML = "<option value selected disabled>子分類</option>";
      ary.forEach(sub => {
        const option = document.createElement("option");
        option.value = sub.id;
        option.innerHTML = sub.name;
        selectSub.append(option);
      });
    }

    function setCityMenu(id) {
      const ary = cities.filter(city => city.region_id == id);
      selectCity.innerHTML = "<option value selected disabled>城市</option>";
      ary.forEach(city => {
        const option = document.createElement("option");
        option.value = city.id;
        option.innerHTML = city.name;
        selectCity.append(option);
      });
    }

    // 設定日曆選擇時間限制
    function toDatetimeLocal(dt) {
      function pad(num) {
        return String(num).padStart(2, "0")
      }
      return `${dt.getFullYear()}-${pad(dt.getMonth() + 1)}-${pad(dt.getDate())}T${pad(dt.getHours())}:${pad(dt.getMinutes())}`;
    }

    // 販售結束 不能小於 販售開始(如果先選開始日期，結束日期的min是開始日期)
    // 販售開始 不能小於 上架時間(如果先選開始日期，上架日期的man是開始日期)
    startInput.addEventListener("change", () => {
      endInput.min = startInput.value;
    })

    // 販售結束 不能小於 開始時間(如果先選結束日期，開始日期的max是結束日期)
    // 販售結束 不能小於 上架時間(如果先選結束日期，上架日期的max是結束日期)
    endInput.addEventListener("change", () => {
      startInput.max = endInput.value;
    })

    btnDels.forEach((btn) => {
      btn.addEventListener("click", function () {
        const tripId = this.dataset.id;
        const tripName = this.dataset.name;

        deleteTripId.value = tripId;
        deleteTripName.textContent = tripName;

        deleteModal.show();
      })
    })

    btnConfirmDels.addEventListener("click", () => {
      const tripId = deleteTripId.value;
      window.location.href = `./doDelete.php?id=${tripId}`;
    });
  </script>
</body>

</html>