<?php
// 登入驗證及會員資訊
session_start();
if (!isset($_SESSION["members"])) {
	header("location: ./login.php");
	exit;
}

require_once "./connect.php";
require_once "./utilities.php";

if (!isset($_GET["id"])) {
	alertGoTo("請從正常管道進入", "./index.php");
	exit;
}

date_default_timezone_set("Asia/Taipei");
$now = time();
$nowDateTime = new DateTime();


$id = $_GET["id"];
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
				t.created_at,
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
      WHERE t.id = ?";
$sqlMainCate = "SELECT * FROM `main_cate`";
$sqlSubCate = "SELECT * FROM `sub_cate`";
$sqlRegion = "SELECT * FROM `regions`";
$sqlCity = "SELECT * FROM `cities`";
$sqlNotice = "SELECT * FROM `notices` WHERE `trip_id` = ?";
$sqlImg = "SELECT * FROM `trip_images` WHERE `trip_id` = ?";

try {
	$stmt = $pdo->prepare($sql);
	$stmt->execute([$id]);
	$row = $stmt->fetch(PDO::FETCH_ASSOC);

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

	$stmtNotice = $pdo->prepare($sqlNotice);
	$stmtNotice->execute([$id]);
	$rowsNotice = $stmtNotice->fetchAll(PDO::FETCH_ASSOC);

	$stmtImg = $pdo->prepare($sqlImg);
	$stmtImg->execute([$id]);
	$rowsImg = $stmtImg->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
	echo "錯誤: {{$e->getMessage()}}";
	exit;
}

$startAt = new DateTime($row["booking_start_at"]);
$startDate = $startAt->format("Y-m-d");
$startTime = $startAt->format("H:i");

$endAt = new DateTime($row["booking_end_at"]);
$endDate = $endAt->format("Y-m-d");
$endTime = $endAt->format("H:i");

$publishedAt = new DateTime($row["published_at"]);
$publishedDate = $publishedAt->format("Y-m-d");
$publishedTime = $publishedAt->format("H:i");

$unpublishedAt = null;
if (isset($row["unpublished_at"])) {
	$unpublishedAt = new DateTime($row["unpublished_at"]);
}
$createdAt = new DateTime($row["created_at"]);
$createdDate = $createdAt->format("Y-m-d");

$updatedAt = new DateTime($row["updated_at"]);
$updatedDate = $updatedAt->format("Y-m-d");


?>

<!doctype html>

<html lang="en" class="layout-menu-fixed layout-compact" data-assets-path="../assets/"
	data-template="vertical-menu-template-free">

<head>
	<meta charset="utf-8" />
	<meta name="viewport"
		content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

	<title>檢視行程商品</title>

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

	<!-- custom 自定義 CSS -->
	<link rel="stylesheet" href="../assets/css/custom.css">
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
							<span><img class="w-40px h-40px" src="../assets/img/favicon/vnlogo.png" alt=""></span>
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
						<a href="javascript:void(0);" class="menu-link menu-toggle ">
							<i class="fa-solid fa-users me-3 menu-text"></i>
							<div class="menu-text fs-5 fw-bold" data-i18n="Dashboards">會員管理</div>
						</a>
						<ul class="menu-sub">
							<li class="menu-item active">
								<a href="../user/index.php" class="menu-link">
									<div class="menu-text fw-bold" data-i18n="Analytics">會員列表</div>
								</a>
							</li>
							<li class="menu-item">
								<a href="../user/add.php" class="menu-link">
									<div class="menu-text fw-bold" data-i18n="Analytics">新增會員</div>
								</a>
							</li>
						</ul>
					</li>

					<!-- 商品管理 -->
					<li class="menu-item active open">
						<a href="javascript:void(0);" class="menu-link menu-toggle">
							<i class="fa-solid fa-map-location-dot me-3 menu-text"></i>
							<div class="menu-text fs-5 fw-bold" data-i18n="Layouts">商品管理</div>
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
						<a href="javascript:void(0);" class="menu-link menu-toggle">
							<i class="fa-solid fa-ticket me-3 menu-text"></i>
							<div class="menu-text fs-5 fw-bold" data-i18n="Dashboards">票券管理</div>
						</a>
						<ul class="menu-sub">
							<li class="menu-item active">
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
					<li class="menu-item">
						<a href="javascript:void(0);" class="menu-link menu-toggle">
							<i class="fa-solid fa-tags me-3 menu-text"></i>
							<div class="menu-text fs-5 fw-bold" data-i18n="Dashboards">優惠券管理</div>
						</a>
						<ul class="menu-sub">
							<li class="menu-item active">
								<a href="../coupons/index.php" class="menu-link">
									<div class="menu-text fw-bold" data-i18n="Analytics">優惠券列表</div>
								</a>
							</li>
							<li class="menu-item">
								<a href="../coupons/add.php" class="menu-link">
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
								<a href="../user/index.php" class="text-primary">Home</a>
							</li>
							<li class="breadcrumb-item">
								<a href="./index.php" class="text-primary">商品管理</a>
							</li>
							<li class="breadcrumb-item">
								<a href="./index.php" class="text-primary">商品列表</a>
							</li>
							<li class="breadcrumb-item active" class="text-primary">檢視行程</li>
						</ol>
					</nav>
				</div>

				<div class="content-wrapper">
					<div class="flex-grow-1 container-p-y custom-width">
						<div class="text-end me-2 mb-3">
							<span class="text-secondary fs-14px">建立日期:&nbsp;<?= $createdDate ?></span>
							<?php if ($row["updated_at"] !== null): ?>
								<span class="text-secondary fs-14px ms-3">更新日期:&nbsp;<?= $updatedDate ?></span>
							<?php endif ?>
						</div>
						<div class="card">
							<div class="card-body">
								<?php if (!$row): ?>
									資料不存在
									<!-- 返回按鍵需要修改 -->
									<a href="./index.php?" class="btn btn-secondary btn-sm ms-2">返回</a>

								<?php else: ?>
									<input type="hidden" name="id" value="<?= $row["id"] ?>">
									<div class="d-flex align-items-start justify-content-between">
										<div class="d-flex align-items-start ms-3">
											<a href="./index.php"><i
													class="fa-solid fa-map-location-dot text-primary me-3 mb-4 mt-2 menu-icon"></i></a>
											<h4 class="trip-name text-primary"><?= $row["name"] ?></h4>
										</div>
										<div class="mb-3 mt-2 ms-2 d-flex justify-content-center">
											<?php
											$startAt = strtotime($row["booking_start_at"]);
											$endAt = strtotime($row["booking_end_at"]);
											$offAt = strtotime($row["unpublished_at"]);
											$stock = intval($row["stock"]);
											if ($startAt > $now): ?>
												<span class="badge bg-label-info rounded-pill me-2 fs-6">尚未販售</span>
											<?php elseif ($startAt < $now && $now < $endAt && $offAt == null && $stock !== 0): ?>
												<span class="badge bg-label-warning rounded-pill me-2 fs-6">販售中</span>
											<?php elseif ($stock == 0): ?>
												<span class="badge bg-label-primary rounded-pill me-2 fs-6">售完</span>
											<?php elseif ($offAt !== null && $stock !== 0): ?>
												<span class="badge bg-label-success rounded-pill me-2 fs-6">未售完</span>
											<?php endif; ?>
											<?php
											$onAt = strtotime($row["published_at"]);
											$offAt = strtotime($row["unpublished_at"]);
											if ($onAt <= $now && $offAt == null): ?>
												<span class="badge bg-label-warning rounded-pill me-2 fs-6">上架中</span>
											<?php elseif ($onAt > $now): ?>
												<span class="badge bg-label-info rounded-pill me-2 fs-6">未上架</span>
											<?php elseif ($offAt !== null): ?>
												<span class="badge bg-label-success rounded-pill me-2 fs-6">已下架</span>
											<?php endif; ?>
										</div>
									</div>


									<hr class="border-info mt-0">

									<!-- 輪播圖片檢視 -->
									<div id="carouselExampleIndicators" class="carousel slide mb-5" data-bs-ride="carousel">
										<div class="carousel-indicators">
											<?php foreach ($rowsImg as $index => $rowImg): ?>
												<button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="<?= $index ?>"
													class="<?= $index === 0 ? 'active' : '' ?>" <?= $index === 0 ? 'aria-current="true"' : '' ?>
													aria-label="Slide <?= $index + 1 ?>">
												</button>
											<?php endforeach; ?>
										</div>
										<div class="carousel-inner rounded-4">
											<?php foreach ($rowsImg as $index => $rowImg): ?>
												<div class="carousel-item <?= $index === 0 ? 'active"' : '' ?>">
													<img class="d-block w-100" src="./images/<?= $rowImg["file_name"] ?>" />
													<div class="carousel-caption d-none d-md-block">
														<p>&nbsp;<?= $index + 1 ?>&nbsp;</p>
													</div>
												</div>
											<?php endforeach; ?>
										</div>
										<a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-bs-slide="prev">
											<span class="carousel-control-prev-icon" aria-hidden="true"></span>
											<span class="visually-hidden">Previous</span>
										</a>
										<a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-bs-slide="next">
											<span class="carousel-control-next-icon" aria-hidden="true"></span>
											<span class="visually-hidden">Next</span>
										</a>
									</div>

									<div class="mt-3 px-5">
										<div class="row mb-3">
											<div class="col-md-3">
												<span class="text-primary fw-bold me-3"><i
														class="fa-solid fa-heart me-2"></i>主類別：</span><?= $row["main_cate_name"] ?>
											</div>
											<div class="col-md-3">
												<span class="text-primary fw-bold me-3"><i
														class="fa-solid fa-heart me-2"></i>子類別：</span><?= $row["sub_cate_name"] ?>
											</div>
											<div class="col-md-3">
												<span class="text-primary fw-bold me-3"><i
														class="fa-solid fa-location-dot me-2"></i>地區：</span><?= $row["region_name"] ?>
											</div>
											<div class="col-md-3">
												<span class="text-primary fw-bold me-3"><i
														class="fa-solid fa-location-dot me-2"></i>地點：</span><?= $row["city_name"] ?>
											</div>
										</div>
										<div class="row mb-3">
											<div class="col-md-3">
												<span class="text-primary fw-bold me-3"><i
														class="fa-solid fa-clock me-2"></i>行程天數：</span><?= $row["duration"] ?>
											</div>
											<div class="col-md-3">
												<span class="text-primary fw-bold me-3"><i
														class="fa-solid fa-user-large me-2"></i>名額：</span><?= $row["stock"] ?>
											</div>
											<div class="col-md-3">
												<span class="text-primary fw-bold me-3"><i
														class="fa-solid fa-dollar-sign me-2"></i>價格：</span>NT$&nbsp;<?= $row["price"] ?>&nbsp;元
											</div>
										</div>
										<hr>
										<div class="mb-4">

											<h6 class="text-primary fw-bold"><i class="fa-solid fa-flag me-2"></i></i>行程簡介</h6>
											<p class="px-5 pre-wrap-text"><?= htmlspecialchars(ltrim($row["info"])) ?></p>

										</div>

										<!-- 行程內容 -->
										<div class="mb-4">

											<h6 class="text-primary fw-bold"><i class="fa-solid fa-flag me-2"></i></i>行程介紹</h6>
											<p class="px-5 pre-wrap-text"><?= htmlspecialchars(htmlspecialchars(ltrim($row["description"]))) ?>
											</p>

										</div>

										<!-- 注意事項 -->
										<div class="mb-4">
											<h6 class="text-primary fw-bold"><i class="fa-solid fa-circle-exclamation me-2"></i>注意事項</h6>
											<ul class="list-group list-group-flush">

												<?php foreach ($rowsNotice as $rowNotice): ?>
													<li class="list-group-item d-flex align-items-center">
														<i class="fa-solid fa-check me-3 text-success"></i>
														<?= $rowNotice["text"] ?>
													</li>
													</li>
												<?php endforeach; ?>


											</ul>
										</div>
										<!-- 販售與上架時間 -->
										<div class="row mb-3">
											<div class="col-md-6">
												<span class="text-primary fw-bold me-3 ms-1"><i
														class="fa-solid fa-calendar-days me-2"></i>開始販售時間：</span><?= $startDate ?>&nbsp;&nbsp;&nbsp;<?= $startTime ?>
											</div>
											<div class="col-md-6">
												<span class="text-primary fw-bold me-3 ms-1"><i
														class="fa-solid fa-calendar-days me-2"></i>結束販售時間：</span><?= $endDate ?>&nbsp;&nbsp;&nbsp;<?= $endTime ?>
											</div>

										</div>

										<div class="row mb-3">
											<div class="col-md-6">
												<span class="text-primary fw-bold me-3 ms-1"><i
														class="fa-solid fa-calendar-days me-2"></i>上架時間：</span><?= $publishedDate ?>&nbsp;&nbsp;&nbsp;<?= $publishedTime ?>
											</div>


											<div class="col-md-6">
												<?php if ($row["unpublished_at"] !== null):
													$unpublishedAt = new DateTime($row["unpublished_at"]);
													$unpublishedDate = $unpublishedAt->format("Y-m-d");
													$unpublishedTime = $unpublishedAt->format("H:i");
													?>
													<span class="text-primary fw-bold me-3"><i
															class="fa-solid fa-calendar-days me-2"></i>下架時間：</span><?= $unpublishedDate ?>&nbsp;&nbsp;&nbsp;<?= $unpublishedTime ?>
												<?php endif; ?>
											</div>

										</div>

										<div class="row mt-6">
											<div class="col-sm-12 d-flex justify-content-end">
												<?php if ($unpublishedAt == null): ?>
													<button type="submit" class="btn btn-gradient-warning"><a class="text-white"
															href="./updateTrip.php?id=<?= $row["id"] ?>">進行編輯</a></button>
													<a class="btn btn-gradient-info ms-2" href="./index.php">返回商品列表</a>
												<?php elseif ($unpublishedAt !== null): ?>
													<a class="btn btn-gradient-info ms-2 ms-auto" href="./index.php">返回商品列表</a>
												<?php endif; ?>
											</div>
										</div>
										</form>
									<?php endif; ?>
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
				<!-- Content wrapper -->

				<!-- Overlay -->
				<div class="layout-overlay layout-menu-toggle"></div>
			</div>

			<!-- template 區域(用來新增notice) -->
			<template id="inputs-notice">
				<div class="row mb-2">
					<div class="col-sm-11 mb-2 d-flex align-items-center">
						<input required name="newNotice[]" type="text" class="form-control" id="basic-default-company"
							placeholder="請列點說明注意事項" />
					</div>
				</div>
			</template>

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
				let subs = [];
				let cities = [];

				subs = <?php echo json_encode($rowsSubCate) ?>;
				cities = <?php echo json_encode($rowsCity) ?>;

				const selectMain = document.querySelector("select[name=mainCate]");
				const selectSub = document.querySelector("select[name=subCate]");

				const selectRegion = document.querySelector("select[name=region]");
				const selectCity = document.querySelector("select[name=city]");

				const noticeArea = document.querySelector(".notice-area");
				const addNotice = document.querySelector("#add-notice");
				const template = document.querySelector("#inputs-notice")


				selectMain.addEventListener("change", function () {
					setSubMenu(this.value)
				})

				selectRegion.addEventListener("change", function () {
					setCityMenu(this.value)
				})

				function setSubMenu(id) {
					const ary = subs.filter(sub => sub.main_cate_id == id);
					selectSub.innerHTML = "<option value selected disabled>請選擇</option>";
					ary.forEach(sub => {
						const option = document.createElement("option");
						option.value = sub.id;
						option.innerHTML = sub.name;
						selectSub.append(option);
					});
				}

				function setCityMenu(id) {
					const ary = cities.filter(city => city.region_id == id);
					selectCity.innerHTML = "<option value selected disabled>請選擇</option>";
					ary.forEach(city => {
						const option = document.createElement("option");
						option.value = city.id;
						option.innerHTML = city.name;
						selectCity.append(option);
					});
				}

				addNotice.addEventListener("click", e => {
					// e.preventDefault();
					const node = template.content.cloneNode(true);
					noticeArea.append(node);
				})

			</script>
</body>

</html>