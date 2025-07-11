<?php
session_start();
if (!isset($_SESSION["members"])) {
  header("location: ../user/login.php");
  exit;
}

require_once "./connect.php";
require_once "./utilities.php";

$sqlRegion = "SELECT * FROM `regions`";
$sqlCity = "SELECT * FROM `cities`";
$sqlType = "SELECT * FROM `types`";
$sqlAct = "SELECT * FROM `acts`";
$sqlSta = "SELECT * FROM `status`";
$sqlImg = "SELECT * FROM `images` WHERE `product_id` = ?";

try {
  $stmtRegion = $pdo->prepare($sqlRegion);
  $stmtRegion->execute();
  $rowsRegion = $stmtRegion->fetchAll(PDO::FETCH_ASSOC);

  $stmtCity = $pdo->prepare($sqlCity);
  $stmtCity->execute();
  $rowsCity = $stmtCity->fetchAll(PDO::FETCH_ASSOC);

  $stmtType = $pdo->prepare($sqlType);
  $stmtType->execute();
  $rowsType = $stmtType->fetchAll(PDO::FETCH_ASSOC);

  $stmtAct = $pdo->prepare($sqlAct);
  $stmtAct->execute();
  $rowsAct = $stmtAct->fetchAll(PDO::FETCH_ASSOC);

  $stmtSta = $pdo->prepare($sqlSta);
  $stmtSta->execute();
  $rowsSta = $stmtSta->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  echo "錯誤: {{$e->getMessage()}}";
  exit;
}
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
    form {
      width: 100%;
    }

    . .content {
      box-sizing: border-box;
      max-width: 1000px;
      width: 100%;
      margin: auto;
      /* margin-right: auto; */

    }

    .card-body select,
    .card-body .form-control,
    .card-body textarea {
      border: var(--bs-border-width) solid #846848;
    }

    .btn-finish {
      background-color: #accab2;
      color: #d44720;
    }

    .btn-finish:hover {
      border: 1px solid #d44720 !important;
      background-color: #accab2 !important;
      color: #d44720 !important;
    }

    .btn-cancel {
      background-color: #d44720;
      color: #accab2;
    }

    .form-label {
			color: #D06224 !important;
	
		}

    /* 登出區 */
    .head {
      width: 35px;
      height: 35px;
      border-radius: 50%;
      margin-right: 2px;
      object-fit: cover;
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
              <i class="fa-solid fa-map-location-dot me-4 menu-text "></i>
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
              <i class="fa-solid fa-ticket me-4 menu-text "></i>
              <div class="menu-text fw-bold fs-5" data-i18n="Dashboards">票券管理</div>
            </a>
            <ul class="menu-sub">
              <li class="menu-item">
                <a href="ticketIndex.php" class="menu-link">
                  <div class="menu-text fw-bold" data-i18n="Analytics">票券列表</div>
                </a>
              </li>
              <li class="menu-item active">
                <a href="./ticketAdd.php" class="menu-link">
                  <div class="menu-text fw-bold" data-i18n="Analytics">新增票券</div>
                </a>
              </li>
            </ul>
          </li>

          <!-- 優惠券管理 -->
          <li class="menu-item">
            <a href="#" class="menu-link menu-toggle">
              <i class="fa-solid fa-tags me-4 menu-text "></i>
              <div class="menu-text fw-bold fs-5" data-i18n="Dashboards">優惠券管理</div>
            </a>
            <ul class="menu-sub">
              <li class="menu-item">
                <a href="../coupons/index.php" class="menu-link">
                  <div class="menu-text" data-i18n="Analytics">優惠券列表</div>
                </a>
              </li>
              <li class="menu-item">
                <a href="../coupons/add.php" class="menu-link">
                  <div class="menu-text " data-i18n="Analytics">新增優惠券</div>
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
      <div class="layout-page container">
        <!-- Navbar -->
        <div class="d-flex align-items-center">
          <span class="layout-menu-toggle align-items-xl-center m-4 d-xl-none">
            <a class="me-xl-6 text-primary" href="javascript:void(0)">
              <i class="icon-base bx bx-menu icon-md"></i>
            </a>
          </span>
          <nav aria-label="breadcrumb" class="mt-4 m-xl-6">
            <ol class="breadcrumb">
              <li class="breadcrumb-item">
                <a href="../user/index.php" class="text-primary">Home</a>
              </li>
              <li class="breadcrumb-item">
                <a href="ticketIndex.php" class="text-primary">票券管理</a>
              </li>
              <li class="breadcrumb-item active" class="text-primary">新增票券</li>
            </ol>
          </nav>
        </div>
        <!-- / Navbar -->

        <div class="content-wrapper">
          <div class="container-xxl flex-grow-1 container-p-y no-padding-container">
            <h4 class="text-primary ms-1">票券商品-新增</h4>
            <hr class="mb-5 mt-0  mb-8" style="color: #ae431e;" />
            <form action="./doAdd.php" method="post" enctype="multipart/form-data">
              <div class="row mb-6 gy-6">
                <div class="col-xl">
                  <div class="card">

                    <div class="card-body">
                      <div class="mb-6">
                        <label class="form-label fs-6" for="basic-default-fullname">商品名稱</label>
                        <input name="name" type="text" class="form-control" id="basic-default-fullname" />
                      </div>

                      <div class="mb-6 row">
                        <div class="col-md-6">
                          <label for="regionSelect" class="form-label fs-6">地區</label>
                          <select name="region" class="form-select">
                            <option value selected disabled>請選地區</option>
                            <?php foreach ($rowsRegion as $rowRegion): ?>
                              <option value="<?= $rowRegion["id"] ?>"><?= $rowRegion["name"] ?></option>
                            <?php endforeach; ?>
                          </select>
                        </div>
                        <div class="col-md-6">
                          <label for="citySelect" class="form-label fs-6">城市</label>
                          <select name="city" class="form-select">
                            <option selected disabled value="">請先選擇地區</option>
                          </select>
                        </div>
                      </div>

                      <div class="mb-6 row">
                        <div class="col-md-6">
                          <label for="typeSelect" class="form-label fs-6">活動類型</label>
                          <select name="type" class="form-select">
                            <option value selected disabled>請選擇</option>
                            <?php foreach ($rowsType as $rowType): ?>
                              <option value="<?= $rowType["id"] ?>"><?= $rowType["name"] ?></option>
                            <?php endforeach; ?>
                          </select>
                        </div>
                        <div class="col-md-6">
                          <label for="actSelect" class="form-label fs-6">活動子分類</label>
                          <select name="act" class="form-select">
                            <option selected disabled value="">請先選擇活動類型</option>
                          </select>
                        </div>
                      </div>

                      <div class="mb-6 row">
                        <div class="col-md-4">
                          <label for="exampleFormControlSelect1" class="form-label fs-6">價格</label>
                          <input name="price" type="text" class="form-control">
                        </div>
                        <div class="col-md-4">
                          <label for="" class="form-label fs-6">庫存數量</label>
                          <input name="stock" type="number" class="form-control">
                        </div>
                        <div class="col-md-4">
                          <label for="statusSelect" class="form-label fs-6">狀態</label>
                          <select name="status" class="form-select" id="statusSelect">
                            <option selected disabled>請選擇狀態</option>
                            <?php foreach ($rowsSta as $rowSta): ?>
                              <option value="<?= $rowSta["id"] ?>"><?= htmlspecialchars($rowSta["name"]) ?></option>
                            <?php endforeach; ?>
                          </select>
                        </div>
                      </div>

                      <div class="mb-6">
                        <label class="form-label fs-6" for="basic-default-message">活動介紹</label>
                        <textarea name="intro" id="basic-default-message" class="form-control" rows="10"
                          placeholder="商品簡介"></textarea>
                      </div>
                      <!-- 插入圖片 -->
                      <div class="row mb-6">
                        <label class="form-label fs-6">新增圖片</label>
                        <div class="col-md-12">
                          <div class="input-group">
                            <input name="imagesFile[]" type="file" class="form-control" accept=".png,.jpg,.jpeg"
                              multiple />
                          </div>
                        </div>
                      </div>
                      <!-- /插入圖片 -->
                      <hr class="border-success-subtle">
                      <div class="text-end" ;>
                        <button type="submit" class="btn btn-warning">確認新增</button>
                        <a href="./ticketIndex.php" class="btn btn-info">取消新增</a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </form>

            <div class="content-backdrop fade"></div>
          </div>
        </div>
        <!-- Content wrapper -->

      </div>
      <!-- / Layout page -->
    </div>

    <!-- Overlay -->
    <div class="layout-overlay layout-menu-toggle"></div>
  </div>
  <!-- / Layout wrapper -->


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
    // 地區城市選擇
    let cities = [];
    let acts = [];

    cities = <?php echo json_encode($rowsCity); ?>;
    acts = <?php echo json_encode($rowsAct); ?>;

    const selectRegion = document.querySelector("select[name=region]");
    const selectCity = document.querySelector("select[name=city]");
    const selectType = document.querySelector("select[name=type]");
    const selectAct = document.querySelector("select[name=act]");

    selectRegion.addEventListener("change", function () {
      setCityMenu(this.value)
    })
    selectType.addEventListener("change", function () {
      setActMenu(this.value)
    })

    function setCityMenu(id) {
      const ary = cities.filter(city => city.region_id == id);
      selectCity.innerHTML = "<option value selected disabled>請選擇城市</option>";
      ary.forEach(city => {
        const option = document.createElement("option");
        option.value = city.id;
        option.innerHTML = city.name;
        selectCity.append(option);
      });
    }

    function setActMenu(id) {
      const ary = acts.filter(act => act.type_id == id);
      selectAct.innerHTML = "<option value selected disabled>請選擇活動子分類</option>";
      ary.forEach(act => {
        const option = document.createElement("option");
        option.value = act.id;
        option.innerHTML = act.name;
        selectAct.append(option);
      });
    }

  </script>

</body>

</html>