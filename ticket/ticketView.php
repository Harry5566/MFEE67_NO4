<?php
require_once "./connect.php";
require_once "./utilities.php";

if (!isset($_GET["id"])) {
	alertGoTo("請從正常管道進入", "./ticketIndex.php");
	exit;
}

$id = $_GET["id"];
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
			LEFT JOIN status ON products.status_id = status.id
        WHERE products.id = ?
		ORDER BY products.id ASC";
$sqlRegion = "SELECT * FROM `regions`";
$sqlCity = "SELECT * FROM `cities`";
$sqlType = "SELECT * FROM `types`";
$sqlAct = "SELECT * FROM `acts`";
$sqlSta = "SELECT * FROM `status`";
$sqlImg = "SELECT * FROM `images` WHERE `product_id` = ?";


try {
	$stmt = $pdo->prepare($sql);
	$stmt->execute([$id]);
	$row = $stmt->fetch(PDO::FETCH_ASSOC);

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

	$stmtImg = $pdo->prepare($sqlImg);
	$stmtImg->execute([$id]);
	$rowsImg = $stmtImg->fetchAll(PDO::FETCH_ASSOC);

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

	<title>檢視票券商品</title>

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

	<script src="./assets/js/config.js"></script>

	<!-- Boxicons css -->
	<link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

	<!-- custom 自定義 CSS -->
	<style>
		.col-form-label {
			color: #D06224 !important;
		}

		.pd-id {
			color: #D06224;
			font-size: 18px;
		}

		.form-label {
			color: #D06224 !important;
		}

		.btn-outline-back {
			border-color: #f1e3d9;
			color: #ae431e;
		}

		.btn-outline-back:hover {
			border-color: #ae431e;
			color: #fff;
		}

		.app-brand {
			min-height: 150px;
		}

		.logo {
			max-height: 180px;
			width: auto;
			display: block;
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
						<span class="menu-text">後台功能</span>
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
								<a href="#" class="menu-link">
									<div class="menu-text" data-i18n="Analytics">優惠券列表</div>
								</a>
							</li>
							<li class="menu-item">
								<a href="#" class="menu-link">
									<div class="menu-text" data-i18n="Analytics">新增優惠券</div>
								</a>
							</li>
						</ul>
					</li>
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
								<a href="#" class="text-primary">Home</a>
							</li>
							<li class="breadcrumb-item">
								<a href="ticketIndex.php" class="text-primary">票券管理</a>
							</li>
							<li class="breadcrumb-item">
								<a href="ticketIndex.php" class="text-primary">票券列表</a>
							</li>
							<li class="breadcrumb-item active" class="text-primary">票券檢視</li>
						</ol>
					</nav>
				</div>
				<!-- / Navbar -->

				<div class="content-wrapper">

					<div class="container-xxl flex-grow-1 container-p-y">
						<div class="d-flex justify-content-between align-items-center">
							<h4 class="text-primary mb-1">票券商品-資訊檢視 &nbsp; #<?= $row["id"] ?></h4>
							<a class="btn btn-outline-back" href="./ticketIndex.php"><i class="fa-solid fa-ticket me-2 "></i>返回票券列表</a>
						</div>			
						<hr class="mb-5 mt-0  mb-8" style="color: #ae431e;" />

						<div class="row g-4">
							<!-- 圖片群 -->
							<div class="col-md-6">
								<div class="card h-100">
									<div class="card-body">
										<div class="">
											<label class="fs-6 form-label">圖片檔</label>
											<?php foreach ($rowsImg as $rowImg): ?>
												<div class="preview my-3 flex-direction-column align-items-center w-100">
													<img class="object-fit-cover rounded-2 border border-warning-subtle"
														src="./images/<?= $rowImg["path"] ?>" alt="">
												</div>
											<?php endforeach; ?>
										</div>
									</div>
								</div>
							</div>

							<!-- 資訊群 -->
							<div class="col-md-6">
								<div class="card h-100">
									<div class="card-body">
										<?php if (!$row): ?>
											資料不存在
											<!-- 返回按鍵需要修改 -->
											<a href="./ticketIndex.php?" class="btn btn-secondary btn-sm ms-2">返回</a>
										<?php else: ?>
											<form action="./doUpdate.php" method="post" enctype="multipart/form-data">
												<div class="row mb-6">

													<div class="col-md-12">
														<label class="fs-6 form-label">商品名稱</label>
														<input disabled required name="name" value="<?= $row["name"] ?>"
															type="text" class="form-control" id="basic-default-name"
															placeholder="請輸入商品名稱" />
													</div>
												</div>
												<!-- 下拉選單-地區-->
												<div class="row mb-6">

													<div class="col-md-6">
														<label class="fs-6 form-label">地區</label>
														<input disabled name="region" type="text" class="form-control"
															value="<?= $row["region_name"] ?>">
													</div>

													<div class="col-md-6">
														<label class="fs-6 form-label">城市</label>
														<input disabled name="city" type="text" class="form-control"
															value="<?= $row["city_name"] ?>">
													</div>
												</div>

												<div class="row mb-6">

													<div class="col-md-6">
														<label class="fs-6 form-label">活動類型</label>
														<input disabled name="type" type="text" class="form-control"
															value="<?= $row["type_name"] ?>">
													</div>

													<div class="col-md-6">
														<label class="fs-6 form-label">活動子分類</label>
														<input disabled name="act" type="text" class="form-control"
															value="<?= $row["act_name"] ?>">
													</div>
												</div>

												<div class="row mb-6">
													<div class="col-md-12">
														<label for="exampleFormControlSelect1"
															class="fs-6 form-label">價格</label>
														<div class="input-group" disabled>
															<span class="input-group-text">$</span>
															<input disabled name="price" type="text" class="form-control"
																aria-label="Dollar amount (with dot and two decimal places)"
																value="<?= $row['price'] ?>" />
															<span class="input-group-text">元</span>
														</div>
													</div>
												</div>

												<div class="row mb-6">
													<div class="col-md-12">
														<label for="" class="fs-6 form-label">庫存數量</label>
														<input disabled name="stock" type="number" class="form-control"
															value="<?= $row['stock'] ?>">
													</div>
												</div>

												<div class="mb-6 row">
													<div class="col-md-12">
														<label for="" class="fs-6 form-label">商品狀態</label>
														<input disabled name="status" type="text" class="form-control"
															value="<?= $row["status_name"] ?>">
													</div>
												</div>

												<div class="mb-6">
													<label class="fs-6 form-label" for="basic-default-message">活動介紹</label>
													<textarea disabled name="intro" id="basic-default-message"
														class="form-control" rows="10"
														placeholder="商品簡介"><?= $row["intro"] ?></textarea>
												</div>

												<hr class="border-success-subtle">
												<!-- 圖片檔案 -->

												<!-- /圖片檔案 -->

												<div class="row justify-content-end text-end mt-10">
													<div class="col-sm-11">
														<a class="btn btn-gradient-success ms-2"
															href="./ticketUpdate.php?id=<?= $row["id"] ?>">編輯商品</a>
														<a class="btn btn-gradient-info ms-2"
															href="./ticketIndex.php">返回列表</a>
													</div>
												</div>
											</form>
										<?php endif; ?>
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
				</div>
			</div>
			<!-- Content wrapper -->

			<!-- Overlay -->
			<div class="layout-overlay layout-menu-toggle"></div>
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
			document.documentElement.setAttribute('data-bs-theme', 'light');
		</script>

		<script>
			// 活動介紹高度隨內容增高
			document.addEventListener('DOMContentLoaded', function () {
				const introTextarea = document.querySelector("#basic-default-message");

				function adjustTextareaHeight(textarea) {
					if (textarea) {
						textarea.style.overflowY = 'hidden'; // 避免在高度重新計算時閃爍滾動條
						textarea.style.height = 'auto'; // 重設高度以便正確計算 scrollHeight
						textarea.style.height = textarea.scrollHeight + 'px'; // 設置為內容所需的高度
					}
				}

				if (introTextarea) {
					// 頁面載入時調整textarea的高度以顯示所有內容
					adjustTextareaHeight(introTextarea);
				}
			});

		</script>
</body>

</html>