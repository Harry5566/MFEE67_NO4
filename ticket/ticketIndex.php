<?php
// 登入驗證及會員資訊
session_start();
if (!isset($_SESSION["members"])) {
  header("location: ../user/login.php");
  exit;
}

require_once "./connect.php";
// 類型頁數
$staId = intval($_GET["staid"] ?? 0);
$regionId = intval($_GET["regionid"] ?? 0);
$typeId = intval($_GET["typeid"] ?? 0);
$search = trim($_GET["search"] ?? "");
$idSort = $_GET["idsort"] ?? null;
$priceSort = $_GET["psort"] ?? null;
$stockSort = $_GET["ssort"] ?? null;
$viewSort = $_GET["vsort"] ?? null;

$conditions = [];
$params = [];
$conditions[] = "`products`.`is_valid` = 1";

if ($staId != 0) {
  $conditions[] = "`products`.`status_id` = :staid";
  $params[":staid"] = $staId;
}
if ($regionId != 0) {
  $conditions[] = "`products`.`region_id` = :regionid";
  $params[":regionid"] = $regionId;
}
if ($typeId != 0) {
  $conditions[] = "`products`.`type_id` = :typeid";
  $params[":typeid"] = $typeId;
}

if ($search !== "") {
  $search_condition = "(products.name LIKE :search OR regions.name LIKE :search OR cities.name LIKE :search OR types.name LIKE :search OR acts.name LIKE :search)";
  $conditions[] = $search_condition;
  $params[":search"] = "%" . $search . "%";
}

$whereClause = "";
if (!empty($conditions)) {
  $whereClause = "WHERE " . implode(" AND ", $conditions);
}

// 頁數分頁
$perPage = 25;
$page = intval($_GET["page"] ?? 1);
$pageStart = ($page - 1) * $perPage;

// id庫存瀏覽人次排序
$orderbySQL = "ORDER BY products.id ASC";
if (!empty($idSort)) {
  if ($idSort == "asc") {
    $orderbySQL = "ORDER BY products.id ASC";
  } elseif ($idSort == "desc") {
    $orderbySQL = "ORDER BY products.id DESC";
  }
}elseif (!empty($priceSort)) {
  if ($priceSort == "asc") {
    $orderbySQL = "ORDER BY products.price ASC, products.id ASC";
  } elseif ($priceSort == "desc") {
    $orderbySQL = "ORDER BY products.price DESC, products.id ASC";
  }
}elseif (!empty($stockSort)) {
  if ($stockSort == "asc") {
    $orderbySQL = "ORDER BY products.stock ASC, products.id ASC";
  } elseif ($stockSort == "desc") {
    $orderbySQL = "ORDER BY products.stock DESC, products.id ASC";
  }
}elseif (!empty($viewSort)) {
  if ($viewSort == "asc") {
    $orderbySQL = "ORDER BY products.view_count ASC, products.id ASC";
  } elseif ($viewSort == "desc") {
    $orderbySQL = "ORDER BY products.view_count DESC, products.id ASC";
  }
}

$sql = "SELECT
            products.*,
            regions.name AS region_name,
            cities.name AS city_name,
            types.name AS type_name,
            acts.name AS act_name,
            status.name AS status_name
        FROM products
        LEFT JOIN regions ON products.region_id = regions.id
        LEFT JOIN cities ON products.city_id = cities.id
        LEFT JOIN types ON products.type_id = types.id
        LEFT JOIN acts ON products.act_id = acts.id
        LEFT JOIN status ON products.status_id = status.id {$whereClause}
        {$orderbySQL}
        LIMIT {$pageStart}, {$perPage}";
$sqlCount = "SELECT COUNT(products.id) as total_count 
             FROM products
             LEFT JOIN regions ON products.region_id = regions.id
             LEFT JOIN cities ON products.city_id = cities.id
             LEFT JOIN types ON products.type_id = types.id
             LEFT JOIN acts ON products.act_id = acts.id 
             {$whereClause}";
$sqlSta = "SELECT * FROM `status` ORDER BY id";
$sqlRegions = "SELECT * FROM `regions` ORDER BY id";
$sqlTypes = "SELECT * FROM `types` ORDER BY id";



try {
  $stmt = $pdo->prepare($sql);
  $stmt->execute($params);
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

  $stmtCount = $pdo->prepare($sqlCount);
  $stmtCount->execute($params);
  $totalLength = $stmtCount->fetchColumn();

  $stmtSta = $pdo->prepare($sqlSta);
  $stmtSta->execute();
  $rowsSta = $stmtSta->fetchAll(PDO::FETCH_ASSOC);

  $stmtRegions = $pdo->prepare($sqlRegions);
  $stmtRegions->execute();
  $rowsRegions = $stmtRegions->fetchAll(PDO::FETCH_ASSOC);

  $stmtTypes = $pdo->prepare($sqlTypes);
  $stmtTypes->execute();
  $rowsTypes = $stmtTypes->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  echo "錯誤: {{$e->getMessage()}}";
  exit;
}

$pageLenght = count($rows); //算當頁有幾筆
$totalPage = ceil($totalLength / $perPage); //算總頁數

function generate_filter_link($param_name, $param_value)
{
  $current_params = $_GET; 
  $new_params = [];

  if (isset($current_params['search']) && trim($current_params['search']) !== '') {
    $new_params['search'] = trim($current_params['search']);
  }

  if ($param_value == 0 || $param_value === '') {

  } else {
    
    $new_params[$param_name] = $param_value;
  }

  if (empty($new_params)) {
    return "./ticketIndex.php";
  }
  return "./ticketIndex.php?" . http_build_query($new_params);
}

$base_pagination_params = [];
if ($staId != 0)
  $base_pagination_params['staid'] = $staId;
if ($regionId != 0)
  $base_pagination_params['regionid'] = $regionId;
if ($typeId != 0)
  $base_pagination_params['typeid'] = $typeId;

if ($idSort !== null)
  $base_pagination_params['idsort'] = $idSort;
if ($priceSort !== null)
  $base_pagination_params['psort'] = $priceSort;
if ($stockSort !== null)
  $base_pagination_params['ssort'] = $stockSort;
if ($viewSort !== null)
  $base_pagination_params['vsort'] = $viewSort;
if ($search !== "")
  $base_pagination_params['search'] = $search;

// For the "All" button link
$all_link = "./ticketIndex.php";

?>

<!doctype html>

<html lang="en" class="layout-menu-fixed layout-compact" data-assets-path="../assets/"
  data-template="vertical-menu-template-free">

<head>
  <meta charset="utf-8" />
  <meta name="viewport"
    content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

  <title>票券管理</title>

  <meta name="description" content="" />

  <!-- Favicon -->
  <link rel="icon" type="image/x-icon" href="../assets/img/favicon/vnlogo-ic.ico" />

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
    rel="stylesheet" />

  <link rel="stylesheet" href="../assets/vendor/fonts/iconify-icons.css" />

  <!-- Core CSS -->
  <!-- build:css assets/vendor/css/theme.css  -->

  <link rel="stylesheet" href="../assets/vendor/css/core.css" />
  <link rel="stylesheet" href="../assets/css/demo.css" />

  <!-- Vendors CSS -->

  <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
  <link rel="stylesheet" href="../assets/css/custom.css">

  <!-- font awesom -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
    integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />

  <!-- endbuild -->

  <!-- Page CSS -->

  <!-- Helpers -->
  <script src="../assets/vendor/js/helpers.js"></script>
  <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->

  <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->

  <script src="../assets/js/config.js"></script>
  <style>
    .id {
      width: 1rem;
      padding: 1.161rem 0.1rem 1.161rem 1.25rem !important;
    }

    .name {
      width: 50rem;
      white-space: normal;
    }

    .table-hover td.cont6 .dropdown-item {
      display: inline-flex;
      align-items: center;
      vertical-align: middle;
    }

    .form-control-s {
      color: #fafafa;
      border-color: #fff;
      background-color: #ffffff1c;
    }

    .form-control-s:focus {
      color: #fff;
      background-color: #ffffff42;
      border-color: #fff;
      /* 邊框顏色變化 */
      outline: 0;
    }

    .in-bg {
      color: #eac891;
      width: 16rem;
      height: 50px;
      background: linear-gradient(to right, #d06224 0%, #8a8635 70%, #ae431e 100%);
      border-radius: 30px;
    }

    .w200px .form-control-s::placeholder {
      color: #fff;
      opacity: 1;
    }

    .litotal {
      color: #ae431e;
      font-size: 16px;
    }

    .page-a {
      text-decoration: none;
    }

    .page-li,
    .page-a {
      font-size: 14px;
    }

    .pacontainer {
      width: 100%;
      max-width: 940px;
      margin: 0 auto;
      position: relative;
      text-align: center;
    }

    .nvbar {
      background-color: #6e5432;
      height: 60px;
    }

    .filter-button-item:nth-child(-n + 8)::after {
      position: absolute;
      right: 0;
      top: 0;
      bottom: 0;
      margin: auto;
      content: "";
      height: calc(100% - 1.5rem);
      border-left: 2px solid #eac891;
    }

    .filter-button-item {
      height: 100%;
      flex-grow: 1;
      position: relative;
    }

    .top-btn {
      color: #eac891;
      display: inline-flex;
      width: 100%;
      height: 100%;
      align-items: center;
      justify-content: center;
      text-decoration: none;
    }

    .top-btn:hover {
      background-color: #eac891;
      color: #6e5432;
    }

    .tbact.active {
      background-color: #eac891;
      color: #6e5432;
    }

    /* search欄位 */
    .nv-search {
      border: 2px solid #eac891;
      background-color: #6e5432;
      display: block;
      width: 100%;
      padding: 0.543rem 0.9375rem;
      font-size: 0.9375rem;
      font-weight: 400;
      line-height: 1.375;
      color: #eac891;
      appearance: none;
    }

    .nv-search::placeholder {
      color: #eac891;
      opacity: 1;
    }

    .nv-search:active,
    .nv-search:focus,
    .nv-search:focus-visible {
      border-color: #eac891;
      outline: 0;
      /* 移除瀏覽器預設的 outline */
    }

    .search-i {
      margin-top: 5px;
      left: -0.5rem;
      color: #eac891;
    }

    .app-brand {
      min-height: 150px;
    }

    .logo {
      max-height: 180px;
      width: auto;
      display: block;
    }

    /* 操作區圓形按鈕 */
    .action-buttons .btn {
      width: 30px;         
      height: 30px;        
      padding: 0;
      margin: 0 2px; 
      display: inline-flex;
      align-items: center;
      justify-content: center;
      line-height: 1;
    }

    .action-buttons .btn .bx { 
      font-size: 15px;
      vertical-align: middle;
    }

    /* 登出區 */
    .head {
      width: 35px;
      height: 35px;
      border-radius: 50%;
      margin-right: 2px;
      object-fit: cover;
    }

    .pd-name {
      color: #4a403c !important;
    }
  </style>
</head>

<body>
  <!-- Layout wrapper -->
  <div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">

      <!-- Menu -->
      <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
        <div class="app-brand demo d-flex justify-content-center align-items-center">
          <a href="index.html" class="app-brand-link">
            <img class="logo" src="../assets/img/favicon/vnlogo.png" alt="">
          </a>
        </div>

        <div class="menu-divider mt-0"></div>

        <div class="menu-inner-shadow"></div>

        <ul class="menu-inner py-1">
          <!-- Forms & Tables -->
          <li class="menu-header small text-uppercase">
            <span class="menu-text fw-bold">後台功能</span>
          </li>
          <!-- 會員管理 -->
          <li class="menu-item">
            <a href="../user/index.php" class="menu-link menu-toggle">
              <i class=" fa-solid fa-users me-4 text-white"></i>
              <div class="menu-text fw-bold fs-5" data-i18n="Dashboards">會員管理</div>
            </a>
            <ul class="menu-sub">
              <li class="menu-item">
                <a href="../user/index.php" class="menu-link">
                  <div class="menu-text fw-bold">會員列表</div>
                </a>
              </li>
              <li class="menu-item">
                <a href="../user/add.php" class="menu-link">
                  <div class="menu-text fw-bold">新增會員</div>
                </a>
              </li>
            </ul>
          </li>


          <!-- 商品管理 -->
          <li class="menu-item">
            <a href="../trip_products/index.php" class="menu-link menu-toggle">
              <i class="fa-solid fa-map-location-dot me-2 menu-text "></i>
              <div class="menu-text fw-bold fs-5" data-i18n="Layouts">商品管理</div>
            </a>

            <ul class="menu-sub">
              <li class="menu-item">
                <a href="../trip_products/index.php" class="menu-link">
                  <div class="menu-text fw-bold" data-i18n="Without menu">行程列表</div>
                </a>
              </li>
              <li class="menu-item">
                <a href="../trip_products/addTrip.php" class="menu-link">
                  <div class="menu-text fw-bold" data-i18n="Without menu">新增行程</div>
                </a>
              </li>
            </ul>
          </li>

          <!-- 票券管理 -->
          <li class="menu-item active open">
            <a href="./ticketIndex.php" class="menu-link menu-toggle">
              <i class="fa-solid fa-ticket me-2 menu-text "></i>
              <div class="menu-text fw-bold fs-5" data-i18n="Dashboards">票券管理</div>
            </a>
            <ul class="menu-sub">
              <li class="menu-item active">
                <a href="ticketIndex.php" class="menu-link">
                  <div class="menu-text fw-bold" data-i18n="Analytics">票券列表</div>
                </a>
              </li>
              <li class="menu-item">
                <a href="./ticketAdd.php" class="menu-link">
                  <div class="menu-text fw-bold" data-i18n="Analytics">新增票券</div>
                </a>
              </li>
            </ul>
          </li>

          <!-- 優惠券管理 -->
          <li class="menu-item">
            <a href="#" class="menu-link menu-toggle">
              <i class="fa-solid fa-tags me-2 menu-text "></i>
              <div class="menu-text fw-bold fs-5" data-i18n="Dashboards">優惠券管理</div>
            </a>
            <ul class="menu-sub">
              <li class="menu-item active">
                <a href="../coupons/index.php" class="menu-link">
                  <div class="menu-text" data-i18n="Analytics">優惠券列表</div>
                </a>
              </li>
              <li class="menu-item">
                <a href="../coupons/add.php" class="menu-link">
                  <div class="menu-text" data-i18n="Analytics">新增優惠券</div>
                </a>
              </li>
            </ul>
          </li>

          <!-- menu登出 -->
          <li class="menu-header small text-uppercase">
            <span class="menu-text fw-bold">會員資訊</span>
          </li>
          <div class="container text-center">

            <div class="d-flex justify-content-center gap-3 mb-3">
              <img class="head" src="../user/img/<?= $_SESSION["members"]["avatar"] ?>" alt="">
              <div class="menu-text fw-bold align-self-center"><?= $_SESSION["members"]["name"] ?></div>
            </div>

            <li class="menu-item row justify-content-center">
              <a href="../user/doLogout.php"
                class="btn rounded-pill btn-gradient-success btn-ban col-10 justify-content-center">
                <div class="menu-text fw-bold"><i class="fa-solid fa-arrow-right-from-bracket me-2"></i>登出</div>
              </a>
            </li>
          </div>
          <!-- /menu登出 -->
        </ul>

        
      </aside>
      <!-- / Menu -->

      <!-- Layout container -->
      <div class="layout-page">
        <!-- Content wrapper -->
        <div class="content-wrapper">
          <div class="d-flex align-items-center">
            <span class="layout-menu-toggle align-items-xl-center m-4 d-xl-none">
              <a class="me-xl-6 text-primary" href="javascript:void(0)">
                <i class="icon-base bx bx-menu icon-md"></i>
              </a>
            </span>
            <nav aria-label="breadcrumb" class="mt-4 m-xl-6">

              <ol class="breadcrumb">
                <li class="breadcrumb-item">
                  <a href="#" class="text-primary">Home</a>
                </li>
                <li class="breadcrumb-item active" class="text-primary">票券管理</li>
              </ol>
            </nav>
          </div>
          <!-- Content -->
          <div class="container-xxl flex-grow-1 container-p-y">
            <div>
              <h4 class="text-primary mb-1">票券列表</h4>
              <hr class="mb-5 mt-0  mb-8" style="color: #ae431e;" />
              <div
                class="d-flex align-items-center position-relative flex-nowrap justify-content-between filter-button-row nvbar">
                <div class="filter-button-item filter-btn-up">
                  <a class="top-btn fs-5  <?= ($staId == 0 && $regionId == 0 && $typeId == 0) ? "active" : "" ?>"
                    href="<?= $all_link ?>"><i class="fa-solid fa-broom fs-6 me-1"></i>清除</a>
                </div>
                <!-- 狀態分頁 -->
                <?php foreach ($rowsSta as $rowSta): ?>
                  <div class="filter-button-item filter-btn-up">
                    <a class="top-btn tbact fs-5  <?= ($staId == $rowSta["id"]) ? "active" : "" ?>"
                      href="<?= generate_filter_link('staid', $rowSta["id"]) ?>">
                      <?= htmlspecialchars($rowSta["name"]) ?>
                    </a>
                  </div>
                <?php endforeach; ?>

                <!-- 地區分頁 -->
                <?php foreach ($rowsRegions as $rowRegion): ?>
                  <div class="filter-button-item filter-btn-up">
                    <a class="top-btn tbact fs-5  <?= ($regionId == $rowRegion["id"]) ? "active" : "" ?>"
                      href="<?= generate_filter_link('regionid', $rowRegion["id"]) ?>">
                      <?= htmlspecialchars($rowRegion["name"]) ?>
                    </a>
                  </div>
                <?php endforeach; ?>

                <!-- 活動分頁 -->
                <?php foreach ($rowsTypes as $rowType): ?>
                  <div class="filter-button-item filter-btn-up">
                    <a class="top-btn tbact fs-5 <?= ($typeId == $rowType["id"]) ? "active" : "" ?>"
                      href="<?= generate_filter_link('typeid', $rowType["id"]) ?>">
                      <?= htmlspecialchars($rowType["name"]) ?>
                    </a>
                  </div>
                <?php endforeach; ?>

                <!-- search -->
                <div class="position-relative w200px me-2 ms-2">
                  <i class="btn btn-search fa-solid fa-magnifying-glass position-absolute search-i"></i>
                  <input name="search" type="text" class="rounded-pill ps-8 nv-search" placeholder="| Search"
                    value="<?= htmlspecialchars($search) ?>">
                </div>
              </div>

              <div class="nvbar-b d-flex justify-content-between align-items-center mt-6 mb-3">
                <div class="d-flex mb-1">
                  <span class="ms-2 text-primary">顯示第&nbsp;<?= $pageStart + 1 ?> -
                      <?= $pageLenght ?>&nbsp;筆資料，共&nbsp;<?= $totalLength ?>&nbsp;筆資料</span>
                </div>

                <!--/ navbar新增票券按鈕 -->
                <div class="navbar-nav flex-row align-items-center ms-md-auto">
                  <a class="btn btn-sm btn-primary ms-auto" href="./ticketAdd.php"><i
                      class="fa-solid fa-plus text-white me-2"></i>新增票券</a>
                </div>
              </div>


              <!-- Hoverable Table rows -->
              <div class="card mt-3">
                <div class="table-responsive text-nowrap">
                  <table class="table table-hover">
                    <!--頁數-->
                    <div class="demo-inline-spacing">
                      <nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm justify-content-center">

                          <!--上一頁-->
                          <li class="page-item prev <?= ($page <= 1) ? 'disabled' : '' ?>">
                            <?php
                            $prev_page_link_params = $base_pagination_params;
                            if ($page > 1) {
                              $prev_page_link_params['page'] = $page - 1;
                              $prev_link = './ticketIndex.php?' . http_build_query($prev_page_link_params);
                            } else {
                              $prev_link = 'javascript:void(0);';
                            }
                            ?>
                            <a class="page-link" href="<?= $prev_link ?>">
                              <i class="icon-base bx bx-chevrons-left icon-xs"></i>
                            </a>
                          </li>
                          <!--/上一頁-->

                          <?php for ($i = 1; $i <= $totalPage; $i++): ?>
                            <li class="page-item <?= $page == $i ? "active" : "" ?>">
                              <?php
                              $page_i_link_params = $base_pagination_params;
                              $page_i_link_params['page'] = $i;
                              $link = './ticketIndex.php?' . http_build_query($page_i_link_params);
                              ?>
                              <a class="page-link" href="<?= $link ?>"><?= $i ?></a>
                            </li>
                          <?php endfor; ?>

                          <!--下一頁-->
                          <li class="page-item next <?= ($page >= $totalPage) ? 'disabled' : '' ?>">
                            <?php
                            $next_page_link_params = $base_pagination_params;
                            if ($page < $totalPage) {
                              $next_page_link_params['page'] = $page + 1;
                              $next_link = './ticketIndex.php?' . http_build_query($next_page_link_params);
                            } else {
                              $next_link = 'javascript:void(0);';
                            }
                            ?>
                            <a class="page-link" href="<?= $next_link ?>">
                              <i class="icon-base bx bx-chevrons-right icon-xs"></i>
                            </a>
                          </li>
                          <!--/下一頁-->
                        </ul>
                      </nav>
                    </div>
                    <!--/頁數-->
                    <thead>
                      <tr>
                        <!-- id加順序按鈕 -->
                        <th class="id text-primary text-center fw-bold fs-6">
                          <div class="d-flex align-items-center justify-content-center fs-6">
                            <span class="text-primary text-center fw-bold fs-6">#</span>
                            <div class="d-flex flex-column id-up-down ms-1" data-sort="" role="button">
                              <i class="fa-solid fa-caret-up fs-8px"></i>
                              <i class="fa-solid fa-caret-down fs-8px"></i>
                            </div>
                          </div>
                        </th>
                        <th class="status text-primary text-center fw-bold fs-6">狀態</th>
                        <th class="name text-primary text-center fw-bold fs-6">商品名稱</th>
                        <th class="region text-primary text-center fw-bold fs-6">地區</th>
                        <th class="city text-primary text-center fw-bold fs-6">城市</th>
                        <th class="type text-primary text-center fw-bold fs-6">類型1</th>
                        <th class="act text-primary text-center fw-bold fs-6">類型2</th>
                        <!-- 價格加順序按鈕 -->
                        <th class="price text-primary text-center fw-bold fs-6">
                          <div class="d-flex align-items-center justify-content-center fs-6">
                            <span class="text-primary text-center fw-bold fs-6">價格</span>
                            <div class="d-flex flex-column price-up-down ms-1" data-sort="" role="button">
                              <i class="fa-solid fa-caret-up fs-8px"></i>
                              <i class="fa-solid fa-caret-down fs-8px"></i>
                            </div>
                          </div>
                        </th>
                        <!-- 庫存加順序按鈕 -->
                        <th class="stock text-primary text-center fw-bold fs-6">
                          <div class="d-flex align-items-center justify-content-center fs-6">
                            <span class="text-primary text-center fw-bold fs-6">庫存</span>
                            <div class="d-flex flex-column stock-up-down ms-1" data-sort="" role="button">
                              <i class="fa-solid fa-caret-up fs-8px"></i>
                              <i class="fa-solid fa-caret-down fs-8px"></i>
                            </div>
                          </div>
                        </th>
                        <!-- 瀏覽人次加順序按鈕 -->
                        <th class="view text-primary">
                          <div class="d-flex align-items-center justify-content-center fs-6">
                            <span class="text-primary text-center fw-bold fs-6">瀏覽人次</span>
                            <div class="d-flex flex-column view-up-down ms-1" data-sort="" role="button">
                              <i class="fa-solid fa-caret-up fs-8px"></i>
                              <i class="fa-solid fa-caret-down fs-8px"></i>
                            </div>
                          </div>
                        </th>
                        <th class="text-primary text-center fw-bold fs-6">操作</th>
                      </tr>
                    </thead>

                    <tbody class="table-border-bottom-0">
                      <?php foreach ($rows as $row): ?>
                        <tr>
                          <td class="id"><?= $row["id"] ?></td>
                          <td class="status">
                            <?php
                            $status_name = $row["status_name"] ?? 'N/A';
                            $badge_class = 'bg-label-secondary';

                            switch ($status_name) {
                              case '上架':
                                $badge_class = 'bg-label-warning rounded-pill';
                                break;
                              case '下架':
                                $badge_class = 'bg-label-success rounded-pill';
                                break;
                              case '售完':
                                $badge_class = 'bg-label-primary rounded-pill';
                                break;
                            }
                            ?>
                            <span class="badge <?= $badge_class ?> me-1"><?= htmlspecialchars($status_name) ?></span>
                          </td>
                          
                          <td class="name fw-bold" id="tkname" ><a class="pd-name" href="./ticketView.php?id=<?= $row["id"] ?>"><?= $row["name"] ?></a></td>
                          <td class="region text-center"><?= $row["region_name"] ?? 'N/A' ?></td>
                          <td class="city text-center"><?= $row["city_name"] ?? 'N/A' ?></td>
                          <td class="type text-center"><?= $row["type_name"] ?? 'N/A' ?></td>
                          <td class="act text-center"><?= $row["act_name"] ?? 'N/A' ?></td>
                          <td class="price text-center">$<?= $row["price"] ?></td>
                          <td class="stock text-center"><?= $row["stock"] ?></td>
                          <td class="text-center"><?= $row["view_count"] ?></td>
                          <td class="text-center"> <!-- 操作區三按鈕 -->
                            <div class="action-buttons">
                              <span class="btn btn-sm rounded-pill btn-warning"><a href="./ticketView.php?id=<?= $row["id"] ?>"><i
                                    class="bx bx-show-alt text-white"></i></a></span>
                              <span class="btn btn-sm rounded-pill btn-info"><a href="./ticketUpdate.php?id=<?= $row["id"] ?>"><i
                                    class="bx bx-edit-alt " style="color: #50402c;"></i></a></span>
                              <button class="btn btn-sm rounded-pill btn-success" data-id="<?= $row["id"] ?>"
                                data-name="<?= htmlspecialchars($row["name"]) ?>">
                                <i class="bx bx-trash"></i>
                              </button>
                            </div>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>

                  <!--頁數-->
                  <div class="demo-inline-spacing">
                    <nav aria-label="Page navigation">
                      <ul class="pagination pagination-sm justify-content-center">

                        <!--上一頁-->
                        <li class="page-item prev <?= ($page <= 1) ? 'disabled' : '' ?>">
                          <?php
                          $prev_page_link_params = $base_pagination_params;
                          if ($page > 1) {
                            $prev_page_link_params['page'] = $page - 1;
                            $prev_link = './ticketIndex.php?' . http_build_query($prev_page_link_params);
                          } else {
                            $prev_link = 'javascript:void(0);';
                          }
                          ?>
                          <a class="page-link" href="<?= $prev_link ?>">
                            <i class="icon-base bx bx-chevrons-left icon-xs"></i>
                          </a>
                        </li>
                        <!--/上一頁-->

                        <?php for ($i = 1; $i <= $totalPage; $i++): ?>
                          <li class="page-item <?= $page == $i ? "active" : "" ?>">
                            <?php
                            $page_i_link_params = $base_pagination_params;
                            $page_i_link_params['page'] = $i;
                            $link = './ticketIndex.php?' . http_build_query($page_i_link_params);
                            ?>
                            <a class="page-link" href="<?= $link ?>"><?= $i ?></a>
                          </li>
                        <?php endfor; ?>

                        <!--下一頁-->
                        <li class="page-item next <?= ($page >= $totalPage) ? 'disabled' : '' ?>">
                          <?php
                          $next_page_link_params = $base_pagination_params;
                          if ($page < $totalPage) {
                            $next_page_link_params['page'] = $page + 1;
                            $next_link = './ticketIndex.php?' . http_build_query($next_page_link_params);
                          } else {
                            $next_link = 'javascript:void(0);';
                          }
                          ?>
                          <a class="page-link" href="<?= $next_link ?>">
                            <i class="icon-base bx bx-chevrons-right icon-xs"></i>
                          </a>
                        </li>
                        <!--/下一頁-->
                      </ul>
                    </nav>
                  </div>
                  <!--/頁數-->
                </div>
              </div>
            </div>
            <!--/ Hoverable Table rows -->
            <!-- / Content -->

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
                    <a href="../user/index.php" target="_blank" class="footer-link">心橋❤️</a>
                    by 前端67-第四組
                  </div>
                  <div class="d-none d-lg-inline-block">
                    <a href="../user/index.php" target="_blank" class="footer-link me-4">關於我們</a>
                    <a href="../user/index.php" class="footer-link me-4" target="_blank">相關服務</a>
                    <a href="../user/index.php" target="_blank" class="footer-link">進階設定</a>
                  </div>
                </div>
              </div>
            </footer>
            <!-- / Footer -->

            <div class="content-backdrop fade"></div>
          </div>
          <!-- Content wrapper -->
        </div>
        <!-- / Layout page -->
      </div>

      <!-- Overlay -->
      <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <!-- / Layout wrapper -->

    <!-- 刪除Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header"></div>
          <div class="modal-body">
            <div class="text-center">
              <i class="fa-solid fa-circle-exclamation" style="font-size: 2rem;"></i>

              <h4 class="mt-3">確定要刪除此票券商品嗎？</h4>
              <p class="text-muted text-warning">【 <span id="deltkName"></span> 】</p>
            </div>
            <input type="hidden" id="deletetkId">
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
    <!-- / 刪除Modal -->

    <!-- Core JS -->

    <script src="../assets/vendor/libs/jquery/jquery.js"></script>

    <script src="../assets/vendor/libs/popper/popper.js"></script>
    <script src="../assets/vendor/js/bootstrap.js"></script>

    <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>

    <script src="../assets/vendor/js/menu.js"></script>

    <!-- endbuild -->

    <!-- Vendors JS -->

    <!-- Main JS -->

    <script src="../assets/js/main.js"></script>

    <!-- Page JS -->

    <!-- Place this tag before closing body tag for github widget button. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>

    <script>
      //   刪除
      const btnDels = document.querySelectorAll(".btn-del");
      const deleteModalElement = document.querySelector("#deleteModal");
      const deletetkId = document.querySelector("#deletetkId");
      const deletetkName = document.querySelector("#deltkName");
      const btnConfirmDels = document.querySelector("#confirmDelete");
      const deleteModal = new bootstrap.Modal(deleteModalElement);
      const idBtn = document.querySelector(".id-up-down");
      const priceBtn = document.querySelector(".price-up-down");
      const stockBtn = document.querySelector(".stock-up-down");
      const viewBtn = document.querySelector(".view-up-down");

      btnDels.forEach(function (btn) {
        btn.addEventListener("click", function () {
          const tkId = this.dataset.id;
          const tkName = this.dataset.name;

          deletetkId.value = tkId;
          deletetkName.textContent = tkName;

          deleteModal.show();
        })
      })

      btnConfirmDels.addEventListener("click", () => {
        const tkId = deletetkId.value;
        window.location.href = `./doDelete.php?id=${tkId}`;
      });


      const btnSearch = document.querySelector(".btn-search");
      const inputText = document.querySelector("input[name=search]");

      btnSearch.addEventListener("click", function () {
        const query = inputText.value;
        window.location.href = `./ticketIndex.php?search=${encodeURIComponent(query)}`;

      });

      inputText.addEventListener("keypress", function (event) {
        if (event.key === "Enter" || event.keyCode === 13) {
          event.preventDefault();
          btnSearch.click();
        }
      });

      // id 庫存 瀏覽順序按鈕
      idBtn.addEventListener("click", () => {
        sorts("idsort");
      });

      priceBtn.addEventListener("click", () => {
        sorts("psort");
      });

      stockBtn.addEventListener("click", () => {
        sorts("ssort");
      });

      viewBtn.addEventListener("click", () => {
        sorts("vsort");
      });

      function sorts(getkey) {
        const urlParams = new URLSearchParams(window.location.search);

        const currentSort = urlParams.get(getkey) || "";
        let newSort = setSortData(currentSort);

        urlParams.delete("idsort");
        urlParams.delete("psort");
        urlParams.delete("ssort");
        urlParams.delete("vsort");

        urlParams.set("page", 1);
        urlParams.set(getkey, newSort); //加入前面判定後的網址參數
        window.location.href = `./ticketIndex.php?${urlParams.toString()}`;
      }

      function setSortData(current) {
        if (current === "" || current === "desc") {
          return "asc";
        } else {
          return "desc";
        }
      }

    </script>
</body>

</html>