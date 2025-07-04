<?php
session_start();
if (!isset($_SESSION["members"])) {
  header("location: ../login.php");
  exit;
}
require_once "./connect.php";


$perPage = 10;
$page = intval($_GET["page"] ?? 1);
$pageStart = ($page - 1) * $perPage;

$allowedSortFields = ['discount', 'quantity', 'start_date', 'end_date', 'status_id', 'usage_scope_id', 'created_at', 'updated_at'];
$sortField = $_GET['sort_field'] ?? '';
$sortOrder = $_GET['sort_order'] ?? '';

if (!in_array($sortField, $allowedSortFields)) {
  $sortField = '';
}
if ($sortOrder !== 'asc' && $sortOrder !== 'desc') {
  $sortOrder = '';
}

$orderBySql = '';
if ($sortField && $sortOrder) {
  $orderBySql = "ORDER BY `$sortField` $sortOrder";
}

// 篩選條件
$scope = isset($_GET['usage_scope_id']) ? intval($_GET['usage_scope_id']) : null;
$status = isset($_GET['status']) ? intval($_GET['status']) : null;
$discount_type_id = isset($_GET['discount_type_id']) ? intval($_GET['discount_type_id']) : null;
$keyword = trim($_GET['keyword'] ?? ''); // 🔍 取得搜尋關鍵字
$start_date = $_GET['start_date'] ?? null;
$end_date = $_GET['end_date'] ?? null;

// WHERE 條件
$whereArr = ["`is_valid` = 1"];
$params = [];

if ($scope) {
  $whereArr[] = "usage_scope_id = ?";
  $params[] = $scope;
}
if ($status) {
  $whereArr[] = "status_id = ?";
  $params[] = $status;
}
if ($discount_type_id) {
  $whereArr[] = "discount_type_id = ?";
  $params[] = $discount_type_id;
}
if (!empty($keyword)) {
  $whereArr[] = "`name` LIKE ?";
  $params[] = "%$keyword%";
}
if (!empty($start_date)) {
  $whereArr[] = "start_date >= ?";
  $params[] = $start_date;
}
if (!empty($end_date)) {
  $whereArr[] = "end_date <= ?";
  $params[] = $end_date;
}

$where = "WHERE " . implode(" AND ", $whereArr);

$sql = "SELECT * FROM `coupons` $where $orderBySql LIMIT $perPage OFFSET $pageStart";
$sqlCount = "SELECT COUNT(*) FROM `coupons` $where";

try {
  $stmt = $pdo->prepare($sql);
  $stmt->execute($params);
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

  $stmtCount = $pdo->prepare($sqlCount);
  $stmtCount->execute($params);
  $totalCount = $stmtCount->fetchColumn();

} catch (PDOException $e) {
  echo "錯誤: " . $e->getMessage();
  exit;
}

$totalPage = ceil($totalCount / $perPage);

// 對照表
$discount_type_idMap = [1 => "元", 2 => "%"];
$statusMap = [1 => "未啟用", 2 => "啟用中"];
$usage_scopeMap = [1 => "全站通用", 2 => "行程活動", 3 => "各式票卷"];
?>


<!doctype html>

<html lang="en" class="layout-menu-fixed layout-compact" data-assets-path="../assets/"
  data-template="vertical-menu-template-free">

<head>
  <meta charset="utf-8" />
  <meta name="viewport"
    content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

  <title>優惠券管理</title>

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

    .select-hover-primary:hover {
      color: #0d6efd;
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
        <div class="app-brand demo d-flex justify-content-center align-items-center">
          <a href="index.html" class="app-brand-link">
            <img class="logo" src="../assets/img/favicon/vnlogo.png" alt="">
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
          <li class="menu-item ">
            <a href="../user/index.php" class="menu-link menu-toggle">
              <i class=" fa-solid fa-users menu-text me-4"></i>
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
              <i class="fa-solid fa-map-location-dot me-4 menu-text"></i>
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
          <li class="menu-item ">
            <a href="../ticket/ticketIndex.php" class="menu-link menu-toggle">
              <i class="fa-solid fa-ticket me-4 menu-text"></i>
              <div class="menu-text fw-bold  fs-5" data-i18n="Dashboards">票券管理</div>
            </a>
            <ul class="menu-sub">
              <li class="menu-item">
                <a href="../ticket/ticketIndex.php" class="menu-link">
                  <div class="menu-text fw-bold" data-i18n="Analytics">票券列表</div>
                </a>
              </li>
              <li class="menu-item">
                <a href="../ticket/ticketAdd.php" class="menu-link">
                  <div class="menu-text fw-bold" data-i18n="Analytics">新增票券</div>
                </a>
              </li>
            </ul>
          </li>

          <!-- 優惠券管理 -->
          <li class="menu-item active open">
            <a href="index.php" class="menu-link menu-toggle">
              <i class="fa-solid fa-tags me-4 menu-text"></i>
              <div class="menu-text fw-bold fs-5" data-i18n="Dashboards">優惠券管理</div>
            </a>
            <ul class="menu-sub">
              <li class="menu-item active">
                <a href="index.php" class="menu-link">
                  <div class="menu-text fw-bold" data-i18n="Analytics">優惠券列表</div>
                </a>
              </li>
              <li class="menu-item">
                <a href="add.php" class="menu-link">
                  <div class="menu-text fw-bold" data-i18n="Analytics">新增優惠券</div>
                </a>
              </li>
            </ul>
          </li>
          <!-- 登出 -->
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
        </ul>
      </aside>
      <!-- / Menu -->

      <!-- Layout container -->
      <div class="layout-page">

        <!-- Navbar -->
        <div class="d-flex ">
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
            <div class="nav-align-topp">
              <form method="get" action="" class="mb-2 d-flex align-items-center gap-2" style="max-width:400px;">
                <label for="keyword" class="mb-0"></label>
                <input type="text" id="keyword" name="keyword" placeholder="優惠券名稱"
                  value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>" class="form-control form-control-sm"
                  style="width:50%;" />
                <button type="submit" class="btn btn-sm btn-primary">搜尋</button>
              </form>
              <form method="get" action="" class="mb-4 d-flex align-items-center gap-2" style="max-width:400px;">
                <label for="start_date" class="mb-0">起</label>
                <input type="date" id="start_date" name="start_date"
                  value="<?= htmlspecialchars($_GET['start_date'] ?? '') ?>" class="form-control form-control-sm" />

                <label for="end_date" class="mb-0">訖</label>
                <input type="date" id="end_date" name="end_date"
                  value="<?= htmlspecialchars($_GET['end_date'] ?? '') ?>" class="form-control form-control-sm"
                  style="width:50%;" />

                <button type="submit" class="btn btn-sm btn-primary">搜尋</button>
              </form>

            </div>
            <div class="nav-align-top">
              <ul class="nav nav-pills mb-4" role="tablist">

                <span class="ms-6 my-2 text-primary">目前共<?= $totalCount ?> 筆資料
                </span>
                <a class="btn btn-sm btn-gradient-success ms-auto" href="add.php"><i
                    class="fa-solid fa-plus text-white me-2" href=""></i>新增優惠券</a>
              </ul>
              <div class="tab-content">
                <div class="tab-pane fade show active table-responsive full-width-card" id="navs-pills-top-home"
                  role="tabpanel">
                  <table class="table text-nowrap w-100">
                    <thead>
                      <tr>
                        <th class="text-primary text-center">#</th>
                        <th class="text-primary text-center">優惠券名稱</th>
                        <th class="text-primary text-center">折扣碼</th>
                        <th class="text-primary text-center">
                          <form id="filterForm" method="get" class="d-flex align-items-center gap-2">
                            <label for="discount_type_id" class="form-label mb-0"></label>
                            <select name="discount_type_id" id="discount_type_id"
                              class="form-select form-select-sm w-auto text-primary"
                              style="height:25px; padding-top:2px; padding-bottom:2px; font-size:0.8rem; line-height:1.1;"
                              onchange="this.form.submit()">
                              <option value="" <?= (!isset($_GET['discount_type_id']) || $_GET['discount_type_id'] === '') ? 'selected' : '' ?>>折扣類型</option>
                              <option value="1" <?= (isset($_GET['discount_type_id']) && $_GET['discount_type_id'] == 1) ? 'selected' : '' ?>>現金折扣</option>
                              <option value="2" <?= (isset($_GET['discount_type_id']) && $_GET['discount_type_id'] == 2) ? 'selected' : '' ?>>百分比折扣</option>
                            </select>


                          </form>
                        </th>
                        <th class="text-primary text-center">
                          <div class="d-flex justify-content-center align-items-center">
                            <span class="me-2">發行數量</span>
                            <span style="cursor:pointer;" onclick="applyColumnSort('quantity', 'asc')">
                              <i class="fa-solid fa-caret-up fs-12px"></i>
                            </span>
                            <span style="cursor:pointer;" onclick="applyColumnSort('quantity', 'desc')">
                              <i class="fa-solid fa-caret-down fs-12px"></i>
                            </span>
                          </div>
                        </th>
                        <th class="text-primary text-center">開始日期</th>
                        <th class="text-primary text-center">截止日期</th>
                        <th class="text-primary text-center">狀態</th>
                        <th class="text-primary text-center">
                          <form id="filterForm" method="get" class="d-flex align-items-center gap-2">
                            <label for="usage_scope_id" class="form-label mb-0"></label>
                            <select name="usage_scope_id" id="usage_scope_id"
                              class="form-select form-select-sm w-auto text-primary"
                              style="height:25px; padding-top:2px; padding-bottom:2px; font-size:0.8rem; line-height:1.1;"
                              onchange="this.form.submit()">
                              <option value="" <?= (!isset($_GET['usage_scope_id']) || $_GET['usage_scope_id'] === '') ? 'selected' : '' ?>>使用範圍</option>
                              <option value="1" <?= (isset($_GET['usage_scope_id']) && $_GET['usage_scope_id'] == 1) ? 'selected' : '' ?>>全站通用</option>
                              <option value="2" <?= (isset($_GET['usage_scope_id']) && $_GET['usage_scope_id'] == 2) ? 'selected' : '' ?>>行程活動</option>
                              <option value="3" <?= (isset($_GET['usage_scope_id']) && $_GET['usage_scope_id'] == 3) ? 'selected' : '' ?>>各式票卷</option>
                            </select>
                          </form>

                        </th>
                        <th class="text-primary text-center">
                          <div class="d-flex justify-content-center align-items-center">
                            <span class="me-2">建立時間</span>
                            <span style="cursor:pointer;" onclick="applyColumnSort('created_at', 'asc')">
                              <i class="fa-solid fa-caret-up fs-12px"></i>
                            </span>
                            <span style="cursor:pointer;" onclick="applyColumnSort('created_at', 'desc')">
                              <i class="fa-solid fa-caret-down fs-12px"></i>
                            </span>
                          </div>
                        </th>
                        <th class="text-primary text-center">
                          <div class="d-flex justify-content-center align-items-center">
                            <span class="me-2">最後更新時間</span>
                            <span style="cursor:pointer;" onclick="applyColumnSort('updated_at', 'asc')">
                              <i class="fa-solid fa-caret-up fs-12px"></i>
                            </span>
                            <span style="cursor:pointer;" onclick="applyColumnSort('updated_at', 'desc')">
                              <i class="fa-solid fa-caret-down fs-12px"></i>
                            </span>
                          </div>
                        </th>
                        <th class="text-primary text-center">操作</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $discount_type_idMap = [1 => "元", 2 => "%"];
                      $statusMap = [1 => "未啟用", 2 => "啟用中"];
                      $usage_scopeMap = [1 => "全站通用", 2 => "行程活動", 3 => "各式票卷"];

                      foreach ($rows as $index => $row):
                        ?>
                        <tr>
                          <td class="text-center"><?= $index + 1 + ($page - 1) * $perPage ?></td>
                          <td class="fw-bold text-center"><?= $row["name"] ?></td>
                          <td class="text-center"><?= $row["discount_code"] ?></td>
                          <td class="text-center">
                            <?= intval($row["discount"]) . ($discount_type_idMap[$row["discount_type_id"]] ?? "") ?>
                          </td>
                          <td class="text-center"><?= $row["quantity"] ?></td>
                          <td class="text-center"><?= $row["start_date"] ?></td>
                          <td class="text-center"><?= $row["end_date"] ?></td>
                          <td class="text-center"><?= $statusMap[$row["status_id"]] ?? "" ?></td>
                          <td class="text-center"><?= $usage_scopeMap[$row["usage_scope_id"]] ?? "" ?></td>
                          <td class="text-center"><?= $row["created_at"] ?></td>
                          <td class="text-center"><?= $row["updated_at"] ?></td>
                          <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                              <a href="update.php?id=<?= $row["id"] ?> " class="btn btn-sm rounded-pill btn-info">
                                <i class="fas fa-edit"></i></a>
                              <button type="button" class="btn btn-sm rounded-pill btn-success btn-del"
                                data-id="<?= $row["id"] ?>">
                                <i class="fas fa-trash"></i>
                              </button>
                            </div>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                  <?php
                  // 保留現有 GET 參數（除了 page）
                  $queryParams = $_GET;
                  unset($queryParams['page']);
                  $baseQuery = http_build_query($queryParams);

                  // 安全限制：頁碼不得超出範圍
                  $prevPage = max($page - 1, 1);
                  $nextPage = min($page + 1, $totalPage);
                  ?>

                  <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">


                      <?php for ($i = 1; $i <= $totalPage; $i++): ?>
                        <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                          <a class="page-link" href="?<?= $baseQuery ?>&page=<?= $i ?>"><?= $i ?></a>
                        </li>
                      <?php endfor; ?>


                    </ul>
                  </nav>





                  <div class="content-backdrop fade"></div>
                </div>
                <!-- Content wrapper -->
              </div>
              <!-- / Layout page -->

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
            <!-- Overlay -->
            <div class="layout-overlay layout-menu-toggle"></div>
            </>
            <!-- / Layout wrapper -->

            <!-- Core JS -->
            <script src="../assets/vendor/libs/jquery/jquery.js"></script>
            <script src="../assets/vendor/libs/popper/popper.js"></script>
            <script src="../assets/vendor/js/bootstrap.js"></script>
            <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
            <script src="../assets/vendor/js/menu.js"></script>

            <!-- Main JS -->
            <script src="../assets/js/main.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
              integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq"
              crossorigin="anonymous"></script>
            <script>
              document.addEventListener('DOMContentLoaded', function () {
                // 排序函式，這個可以全域用，但放在這也可以
                window.applyColumnSort = function (field, order) {
                  const url = new URL(window.location.href);
                  url.searchParams.set('sort_field', field);
                  url.searchParams.set('sort_order', order);
                  url.searchParams.set('page', 1);
                  window.location.href = url.toString();
                };

                // 綁定刪除按鈕事件
                const btnDels = document.querySelectorAll(".btn-del");
                btnDels.forEach((btn) => {
                  btn.addEventListener("click", function () {
                    if (confirm("確定要刪除嗎?")) {
                      window.location.href = `doDelete.php?id=${btn.dataset.id}`;
                    }
                  });
                });

                // 這裡一定要確定你有 HTML 元素存在
                const trigger = document.querySelector('.select-trigger');  // 依你實際 class 或 id 改名
                const options = document.querySelector('.select-options');
                const hiddenInput = document.querySelector('input[name="your-hidden-input-name"]');
                const select = document.querySelector('.custom-select');

                if (trigger && options && hiddenInput && select) {
                  trigger.addEventListener('click', () => {
                    options.classList.toggle('active');
                  });

                  options.querySelectorAll('.custom-option').forEach(option => {
                    option.addEventListener('click', () => {
                      const value = option.getAttribute('data-value');
                      const text = option.textContent;
                      trigger.textContent = text;
                      hiddenInput.value = value;
                      options.classList.remove('active');
                      console.log('選擇了：', value, text);
                    });
                  });

                  document.addEventListener('click', (e) => {
                    if (!select.contains(e.target)) {
                      options.classList.remove('active');
                    }
                  });
                } else {
                  console.warn('某些下拉元素未找到，請檢查 HTML');
                }
              });
            </script>



</body>

</html>