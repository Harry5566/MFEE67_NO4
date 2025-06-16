<?php
require_once "connect.php";

if (!isset($_GET["id"])) {
  die("缺少 id");
}

$id = intval($_GET["id"]);

$sql = "SELECT * FROM `coupons` WHERE id = ? AND is_valid = 1";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);




if (!$row) {
  die("找不到資料");
}



?>
<!doctype html>

<html lang="en" class="layout-menu-fixed layout-compact" data-assets-path="../assets/"
  data-template="vertical-menu-template-free">

<head>
  <meta charset="utf-8" />
  <meta name="viewport"
    content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

  <title>Xin Chào心橋</title>

  <meta name="description" content="" />

  <!-- Favicon -->
  <link rel="icon" type="image/x-icon" href="../assets/img/favicon/favicon.ico" />

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
    rel="stylesheet" />

  <link rel="stylesheet" href="../assets/vendor/fonts/iconify-icons.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
    integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />

  <!-- Core CSS -->
  <link rel="stylesheet" href="../assets/vendor/css/core.css" />
  <link rel="stylesheet" href="../assets/css/demo.css" />

  <!-- custom CSS -->
  <link rel="stylesheet" href="../assets/css/custom.css">

  <style>
    .action-buttons .btn {
      margin: 2px;
      width: 35px;
      height: 35px;
    }
  </style>


  <script src="../assets/vendor/js/helpers.js"></script>
  <script src="../assets/js/config.js"></script>
</head>

<body>
  <!-- Layout wrapper -->
  <div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">

      <!-- Menu -->
      <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
        <div class="app-brand demo">
          <a href="index.php" class="app-brand-link">
            <span>
              <span><img class="w-40px h-40px" src="../logo.png" alt=""></span>
            </span>
            <span class="fs-4 fw-bold ms-2 app-brand-text demo menu-text align-items-center">心橋</span>
          </a>

          <a href="javascript:void(0);" class="layout-menu-toggle menu-link ms-auto">
            <i class="bx bx-chevron-left d-block d-xl-none align-middle"></i>
          </a>
        </div>

        <div class="menu-divider mt-0"></div>

        <div class="menu-inner-shadow"></div>

        <ul class="menu-inner py-1">

          <!-- 會員管理 -->
          <li class="menu-header small text-uppercase">
            <span class="menu-text fw-bold">後台功能</span>
          </li>
          <li class="menu-item active open">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
              <i class=" fa-solid fa-users me-4"></i>
              <div class="menu-text fw-bold" data-i18n="Dashboards">會員管理</div>
            </a>
            <ul class="menu-sub">
              <li class="menu-item active">
                <a href="index.php" class="menu-link">
                  <div class="menu-text fw-bold">會員列表</div>
                </a>
              </li>
              <li class="menu-item">
                <a href="add.php" class="menu-link">
                  <div class="menu-text fw-bold">新增會員</div>
                </a>
              </li>
              <li class="menu-item">
                <a href="deletedMembers.php" class="menu-link">
                  <div class="menu-text fw-bold">已刪除會員</div>
                </a>
              </li>
            </ul>
          </li>
          <!-- 商品管理 -->
          <li class="menu-item">
            <a href="productTrip-Index.php" class="menu-link menu-toggle">
              <i class="fa-solid fa-map-location-dot me-2 menu-text"></i>
              <div class="menu-text fw-bold" data-i18n="Layouts">商品管理</div>
            </a>

            <ul class="menu-sub">
              <li class="menu-item ">
                <a href="productTrip-Index.php" class="menu-link">
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
              <i class="fa-solid fa-ticket me-2 menu-text"></i>
              <div class="menu-text fw-bold" data-i18n="Dashboards">票券管理</div>
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
              <i class="fa-solid fa-tags me-2 menu-text"></i>
              <div class="menu-text fw-bold" data-i18n="Dashboards">優惠券管理</div>
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
        </ul>
        </ul>

      </aside>
      <!-- / Menu -->

      <!-- Layout container -->
      <div class="layout-page">

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
                <a href="productTrip-Index.php" class="text-primary">優惠券管理</a>
              </li>
              <li class="breadcrumb-item active" class="text-primary">優惠券列表</li>
            </ol>
          </nav>
        </div>
        <!-- Content wrapper -->
        <div class="content-wrapper">
          <div class="container-fluid flex-grow-1 container-p-y">
            <div class="nav-align-top">
              <h4 class="text-primary text-start">修改優惠券</h4>
              <div class="container mt-3">
                <div class="p-4 bg-white shadow rounded">
                  <form action="doUpdate.php" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= $row["id"] ?>">
                    <div class="input-group mb-1">
                      <span class="input-group-text">優惠券名稱</span>
                      <input value="<?= $row["name"] ?>" name="name" type="text" class="form-control" placeholder="">
                    </div>
                    <div class="input-group mb-1">
                      <span class="input-group-text">折扣碼</span>
                      <input value="<?= $row["discount_code"] ?>" name="discount_code" type="text" class="form-control"
                        placeholder="">
                    </div>
                    <div class="input-group mb-1">
                      <span class="input-group-text">折扣數值</span>
                      <input value="<?= $row["discount"] ?>" name="discount" type="text" class="form-control"
                        placeholder="">
                    </div>
                    <div class="input-group mb-1">
                      <span class="input-group-text">折扣類型</span>
                      <div class="form-control">
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="radio" name="discount_type_id" id="typeCash" value="1"
                            <?= $row["discount_type_id"] == 1 ? "checked" : "" ?>>
                          <label class="form-check-label" for="typeCash">現金折扣</label>
                        </div>
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="radio" name="discount_type_id" id="typePercentage"
                            value="2" <?= $row["discount_type_id"] == 2 ? "checked" : "" ?>>
                          <label class="form-check-label" for="typePercentage">百分比折扣</label>
                        </div>
                      </div>
                    </div>
                    <div class="input-group mb-1">
                      <span class="input-group-text">發行數量</span>
                      <input value="<?= $row["quantity"] ?>" name="quantity" type="text" class="form-control"
                        placeholder="">
                    </div>
                    <div class="input-group mb-1">
                      <span class="input-group-text">開始日期</span>
                      <input value="<?= $row["start_date"] ?>" name="start_date" type="text" class="form-control"
                        placeholder="">
                    </div>
                    <div class="input-group mb-1">
                      <span class="input-group-text">截止日期</span>
                      <input value="<?= $row["end_date"] ?>" name="end_date" type="text" class="form-control"
                        placeholder="">
                    </div>
                    <div class="input-group mb-1">
                      <span class="input-group-text">優惠券狀態</span>
                      <div class="form-control">
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="radio" name="status_id" id="statusActive" value="1"
                            <?= (isset($row["status_id"]) && $row["status_id"] == 1) ? "checked" : "" ?>>
                          <label class="form-check-label" for="statusActive">未啟用</label>
                        </div>
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="radio" name="status_id" id="statusInactive" value="2"
                            <?= (isset($row["status_id"]) && $row["status_id"] == 2) ? "checked" : "" ?>>
                          <label class="form-check-label" for="statusInactive">啟用</label>
                        </div>
                      </div>
                    </div>

                    <div class="input-group mb-1">
                      <span class="input-group-text">使用範圍</span>
                      <input value="<?= $row["usage_scope_id"] ?>" name="usage_scope_id" type="text"
                        class="form-control" placeholder="">
                    </div>
                    <div class="input-group mb-1">
                      <span class="input-group-text">建立時間</span>
                      <input value="<?= $row["created_at"] ?>" name="created_at" type="text" class="form-control"
                        placeholder="">
                    </div>
                    <div class="input-group mb-1">
                      <span class="input-group-text">最後更新時間</span>
                      <input value="<?= date('Y-m-d H:i:s') ?>" name="updated_at" type="text" class="form-control"
                        placeholder="">
                    </div>


                    <div class="mt-1 text-end">
                      <button type="submit" class="btn btn-info btn-send">送出</button>
                      <a class="btn btn-primary" href="index.php">取消</a>
                    </div>

                  </form>
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

      <!-- Core JS -->
      <script src="../assets/vendor/libs/jquery/jquery.js"></script>
      <script src="../assets/vendor/libs/popper/popper.js"></script>
      <script src="../assets/vendor/js/bootstrap.js"></script>
      <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
      <script src="../assets/vendor/js/menu.js"></script>

      <!-- Main JS -->
      <script src="../assets/js/main.js"></script>

</body>

</html>