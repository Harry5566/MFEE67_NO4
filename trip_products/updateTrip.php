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

$id = $_GET["id"];
$sql = "SELECT * FROM `trips` WHERE `id` = ?";
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

date_default_timezone_set("Asia/Taipei");

$now = new DateTime();
$startAt = new DateTime($row["booking_start_at"]);
$disableStart = $startAt < $now ? "disabled" : "";
$endAt = new DateTime($row["booking_end_at"]);
$disableEnd = $endAt < $now ? "disabled" : "";
$publishedAt = new DateTime($row["published_at"]);
$disablePublished = $publishedAt < $now ? "disabled" : "";


?>

<!doctype html>

<html lang="en" class="layout-menu-fixed layout-compact" data-assets-path="../assets/"
	data-template="vertical-menu-template-free">

<head>
	<meta charset="utf-8" />
	<meta name="viewport"
		content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

	<title>編輯行程商品</title>

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
				<div class="app-brand demo d-flex justify-content-center align-items-center">
					<a href="./index.php" class="app-brand-link">
						<span>
							<span><img class="logo" src="../assets/img/favicon/vnlogo.png" alt=""></span>
						</span>
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
					<li class="menu-item">
						<a href="javascript:void(0);" class="menu-link menu-toggle ">
							<i class="fa-solid fa-users me-4 menu-text"></i>
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
							<i class="fa-solid fa-map-location-dot me-4 menu-text"></i>
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
							<i class="fa-solid fa-ticket me-4 menu-text"></i>
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
							<i class="fa-solid fa-tags me-4 menu-text"></i>
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
							<li class="breadcrumb-item active" class="text-primary">編輯行程</li>
						</ol>
					</nav>
				</div>

				<div class="content-wrapper">
					<div class="flex-grow-1 container-p-y custom-width">
						<div class="d-flex align-items-center justify-content-between">
							<div class="d-flex align-items-center ps-3">
								<i class="fs-4 bx bx-edit-alt me-1 text-primary me-2 mb-4"></i>
								<h4 class="text-primary ms-1">編輯行程</h4>
							</div>

							<div>
								<a class="btn btn-sm rounded-pill btn-warning me-3" href="./viewTrip.php?id=<?= $row["id"] ?>"
									alt="檢視行程"><i class="bx bx-show-alt me-1"></i>檢視行程</a>
							</div>
						</div>

						<div class="card">
							<div class="card-body">
								<?php if (!$row): ?>
									資料不存在
									<!-- 返回按鍵需要修改 -->
									<a href="./index.php?" class="btn btn-secondary btn-sm ms-2">返回</a>

								<?php else: ?>
									<form action="./doUpdateTrip.php" method="post" enctype="multipart/form-data">
										<input type="hidden" name="id" value="<?= $row["id"] ?>">
										<div class="row mb-6">
											<label class="col-sm-1 col-form-label text-primary">行程名稱</label>
											<div class="col-sm-11">
												<input required name="name" value="<?= $row["name"] ?>" type="text" class="form-control"
													id="basic-default-name" placeholder="請輸入行程名稱" />
											</div>
										</div>

										<!-- 做關聯下拉式選單: 選單的selected要再加上去-->
										<div class="row mb-6">
											<label class="col-sm-1 col-form-label text-primary">主類別</label>
											<div class="col-sm-5">
												<select required id="mainCateSelect" class="form-select" name="mainCate">
													<option selected disabled>請選擇</option>
													<?php foreach ($rowsMainCate as $rowMainCate): ?>
														<option value="<?= $rowMainCate["id"] ?>" <?= ($rowMainCate["id"]) == $row["main_cate_id"] ? "selected" : "" ?>>
															<?= $rowMainCate["name"] ?>
														</option>
													<?php endforeach; ?>
												</select>
											</div>
											<label class="col-sm-1 col-form-label text-primary">子類別</label>
											<div class="col-sm-5">
												<select required id="subCateSelect" class="form-select" name="subCate">
													<option selected disabled>請選擇</option>
													<?php foreach ($rowsSubCate as $rowSubCate): ?>
														<option value="<?= $rowSubCate["id"] ?>" <?= ($rowSubCate["id"] == $row["sub_cate_id"]) ? "selected" : "" ?>>
															<?= $rowSubCate["name"] ?>
														</option>
													<?php endforeach; ?>
												</select>
											</div>
										</div>
										<div class="row mb-6">
											<label class="col-sm-1 col-form-label text-primary">地區</label>
											<div class="col-sm-5">
												<select required id="regionSelect" class="form-select" name="region">
													<option selected disabled>請選擇</option>
													<?php foreach ($rowsRegion as $rowRegion): ?>
														<option value="<?= $rowRegion["id"] ?>" <?= ($rowRegion["id"] == $row["region_id"]) ? "selected" : "" ?>>
															<?= $rowRegion["name"] ?>
														</option>
													<?php endforeach; ?>
												</select>
											</div>
											<label class="col-sm-1 col-form-label text-primary">地點</label>
											<div class="col-sm-5">
												<select required id="citySelect" class="form-select" name="city">
													<option selected disabled>請選擇</option>
													<?php foreach ($rowsCity as $rowCity): ?>
														<option value="<?= $rowCity["id"] ?>" <?= ($rowCity["id"] == $row["region_id"]) ? "selected" : "" ?>>
															<?= $rowCity["name"] ?>
														</option>
													<?php endforeach; ?>
												</select>
											</div>
										</div>
										<!-- 做關聯下拉式選單-->

										<div class="row mb-6">
											<label class="col-sm-1 col-form-label text-primary">行程天數</label>
											<div class="col-md-2">
												<input required name="days" class="form-control" type="number" min="1" step="1"
													value="<?= $row["duration"] ?>" id="html5-number-input" />

											</div>
											<label class="col-sm-1 col-form-label text-primary">名額</label>
											<div class="col-md-2">
												<input required name="stock" class="form-control" type="number" min="0" step="1"
													value="<?= $row["stock"] ?>" id="html5-number-input" />
											</div>
											<label class="col-sm-1 col-form-label text-primary">價格</label>
											<div class="col-md-4">
												<div class="input-group">
													<span class="input-group-text">NT$</span>
													<input required name="price" type="number" class="form-control" min="0" step="1"
														value="<?= $row["price"] ?>" aria-label="Amount (to the nearest dollar)" />
													<span class="input-group-text">元</span>
												</div>
											</div>

										</div>

										<div class="row mb-6">
											<label class="col-sm-1 col-form-label text-primary">行程簡介</label>
											<div class="col-sm-11">
												<textarea required name="info" class="form-control info" id="basic-default-company" rows="5"
													placeholder="請簡述該行程特色"><?= $row["info"] ?></textarea>
												<div class="d-flex justify-content-between">
													<div class="form-text text-warning"><i
															class="fa-solid fa-circle-exclamation text-warning me-1"></i>請填寫20~100個字元。
													</div>
													<div class="form-text text-gray me-2"><span class="text-primary me-1 info-count"></span>
													</div>
												</div>
											</div>

										</div>
										<div class="row mb-6">
											<label class="col-sm-1 col-form-label text-primary">行程介紹</label>
											<div class="col-sm-11">
												<textarea required name="description" class="form-control desc" id="basic-default-company"
													rows="10" placeholder="請詳細介紹該行程內容及安排"><?= $row["description"] ?></textarea> </textarea>
												<div class="d-flex justify-content-between">
													<div class="form-text text-warning"><i
															class="fa-solid fa-circle-exclamation text-warning me-1"></i>請填寫200~1000個字元。
													</div>
													<div class="form-text text-gray me-2"><span class="text-primary me-1 desc-count"></span>
													</div>
												</div>
											</div>
										</div>

										<div class="row mb-6">
											<label class="col-sm-1 col-form-label text-primary">注意事項</label>
											<div class="col-sm-11 notice-area">
												<?php foreach ($rowsNotice as $index => $rowNotice): ?>
													<div class="row mb-2 notice-item">
														<div class="col-sm-11 mb-2 d-flex align-items-center justify-content-between">
															<input type="hidden" name="notice[<?= $index ?>][id]" value="<?= $rowNotice["id"] ?>">
															<input required name="notice[<?= $index ?>][text]" value="<?= $rowNotice["text"] ?>"
																type=" text" class="form-control" id="basic-default-company" placeholder="請列點說明注意事項" />
															<?php if ($index == 0): ?>
																<i class="fa-solid fa-plus text-primary ms-3" id="add-notice"></i>
															<?php elseif ($index >= 1): ?>
																<input type="hidden" value="<?= $rowNotice["id"] ?>">
																<i class="fa-regular fa-trash-can text-secondary ms-3 del-ori-notice"
																	data-id="<?= $rowNotice['id'] ?>"></i>
															<?php endif; ?>
														</div>

													</div>
												<?php endforeach; ?>
											</div>
										</div>

										<div class="row mb-6">
											<label class="col-sm-1 col-form-label text-primary">開始販售</label>
											<div class="col-md-5">
												<input required name="start-at" class="form-control start-at" type="datetime-local"
													value="<?= $row["booking_start_at"] ?>" id="html5-datetime-local-input"
													<?= $row["booking_start_at"] < $now ? $disableStart : "" ?> />
											</div>
											<label class="col-sm-1 col-form-label text-primary">結束販售</label>
											<div class="col-md-5">
												<input required name="end-at" class="form-control end-at" type="datetime-local"
													value="<?= $row["booking_end_at"] ?>" id="html5-datetime-local-input" <?= $row["booking_end_at"] < $now ? $disableEnd : "" ?> />
											</div>
										</div>

										<div class="row mb-6">
											<label class="col-sm-1 col-form-label text-primary">上架時間</label>
											<div class="col-md-11">
												<input required name="published-at" class="form-control published-at" type="datetime-local"
													value="<?= $row["published_at"] ?>" id="html5-datetime-local-input" <?= $row["published_at"] < $now ? $disablePublished : "" ?> />
											</div>
										</div>

										<!-- 需要設定限制整體容量 -->

										<div class="row mb-6">
											<label class="col-sm-1 col-form-label text-primary">封面圖片</label>
											<div class="col-md-11">
												<div class="input-group">
													<input name="tripFile[]" type="file" class="form-control" accept=".png,.jpg,.jpeg" multiple />
												</div>
											</div>
										</div>

										<hr class="border-success-subtle">

										<div class="row my-4">
											<?php foreach ($rowsImg as $rowImg): ?>
												<div class="preview col-12 col-sm-6 col-md-4 my-3">
													<img class="w-100 object-fit-cover rounded-2 border border-warning-subtle"
														src="./images/<?= $rowImg["file_name"] ?>" alt="">
												</div>
											<?php endforeach; ?>
										</div>

										<div class="d-flex justify-content-between">
											<?php if ($publishedAt < $now): ?>
												<button type="button" class="btn-off btn btn-gradient-success ms-2" data-bs-toggle="modal"
													data-bs-target="#offModal" data-id="<?= $row["id"] ?>" data-name="<?= $row["name"] ?>">下架</button>
												<div>
													<button type="submit" class="btn btn-gradient-warning">完成編輯</button>
													<a class="btn btn-gradient-info ms-2" href="./index.php">取消</a>
												</div>
											<?php elseif ($publishedAt > $now): ?>
												<button type="submit" class="btn btn-gradient-warning ms-auto">完成編輯</button>
												<a class="btn btn-gradient-info ms-2" href="./index.php">取消</a>
											<?php endif; ?>
										</div>
									</form>
								<?php endif; ?>
							</div>

						</div>
						<!-- 下架 Confirmation Modal -->



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

			<!-- Content wrapper -->

			<!-- Overlay -->
			<div class="layout-overlay layout-menu-toggle"></div>

			<div class="modal fade" id="offModal" tabindex="-1" aria-hidden="true">
				<div class="modal-dialog modal-dialog-centered">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title">
								<i class="fas fa-trash me-2"></i>下架商品
							</h5>
						</div>
						<div class="modal-body">
							<div class="text-center">
								<i class="fas fa-calender-xmark text-warning" style="font-size: 3rem;"></i>
								<h4 class="mt-3">確定要刪下架此商品行程嗎？</h4>
								<p class="text-muted text-warning">行程名稱：<span class="fw-bold" id="offTripName"></span></p>
							</div>
							<input type="hidden" id="offTripId">
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-gradient-primary" id="confirmOff">
								<i class="fas fa-trash me-2"></i>確認下架
							</button>
							<button type="button" class="btn btn-gradient-info" data-bs-dismiss="modal">取消</button>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- template 區域(用來新增notice) -->
		<template id="inputs-notice">
			<div class="row mb-2">
				<div class="col-sm-11 mb-2 d-flex align-items-center">
					<input required name="notice_new[]" type="text" class="form-control" id="basic-default-company"
						placeholder="請列點說明注意事項" />
					<i class="fa-regular fa-trash-can text-secondary ms-3 del-notice"></i></span>
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

			const infoInput = document.querySelector(".info");
			const descInput = document.querySelector(".desc");

			const infoCount = document.querySelector(".info-count");
			const descCount = document.querySelector(".desc-count");

			const noticeArea = document.querySelector(".notice-area");
			const addNotice = document.querySelector("#add-notice");
			const delOriNotice = document.querySelectorAll(".del-ori-notice");
			const template = document.querySelector("#inputs-notice");
			const delNoticeId = document.querySelectorAll("input[name=notice_delete]")

			const startInput = document.querySelector(".start-at");
			const endInput = document.querySelector(".end-at");
			const publishInput = document.querySelector(".published-at");

			const now = new Date();

			const btnOff = document.querySelector(".btn-off");
			const offModalElement = document.querySelector("#offModal");
			const offTripId = document.querySelector("#offTripId");
			const offTripName = document.querySelector("#offTripName");
			const btnConfirmOff = document.querySelector("#confirmOff");

			const offModal = new bootstrap.Modal(offModalElement);

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

			function updateCount(input, count, min, max) {
				if (!input || !count) return;

				const textNoSpace = input.value.replace(/\s/g, "");
				const length = textNoSpace.length;
				count.textContent = `${length}/${max}`;

				if (length < min || length > max) {
					count.classList.add("text-warning");
					count.classList.remove("text-success");
				} else {
					count.classList.add("text-success");
					count.classList.remove("text-warning");
				}
			}

			if (infoInput && infoCount) {
				updateCount(infoInput, infoCount, 20, 100);
				infoInput.addEventListener("input", () => {
					updateCount(infoInput, infoCount, 20, 100);
				});
			}

			if (descInput && descCount) {
				updateCount(descInput, descCount, 200, 1000);
				descInput.addEventListener("input", () => {
					updateCount(descInput, descCount, 200, 1000);
				});
			}

			delOriNotice.forEach(function (btn) {
				btn.addEventListener("click", function () {
					const noticeId = btn.dataset.id;

					if (noticeId) {
						const input = document.createElement("input");
						input.type = "hidden";
						input.name = "notice_delete[]";
						input.value = noticeId;
						document.querySelector("form").append(input);
					}

					btn.closest(".notice-item").remove();
				});
			});


			addNotice.addEventListener("click", e => {
				// e.preventDefault();
				const node = template.content.cloneNode(true);
				const newNotice = node.querySelector(".row");

				const notices = noticeArea.querySelectorAll("#inputs-notice");
				const delNotice = newNotice.querySelector(".del-notice");
				delNotice.addEventListener("click", e => {
					newNotice.remove();
				})

				noticeArea.appendChild(newNotice);
			})

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
				publishInput.max = startInput.value;
			});

			// 販售結束 不能小於 開始時間(如果先選結束日期，開始日期的max是結束日期)
			// 販售結束 不能小於 上架時間(如果先選結束日期，上架日期的max是結束日期)
			endInput.addEventListener("change", () => {
				startInput.max = endInput.value;
				if (startInput.value == "") {
					publishInput.max = endInput.value;
				}
			})

			// 上架日期 不能小於 開始時間(如果先選上架日期，開始日期的min是上架日期)
			publishInput.addEventListener("change", () => {
				startInput.min = publishInput.value;
				endInput.min = publishInput.value;
			});

			btnOff.addEventListener("click", function () {
				const tripId = this.dataset.id;
				const tripName = this.dataset.name;

				offTripId.value = tripId;
				offTripName.textContent = tripName;

				offModal.show();
			})


			btnConfirmOff.addEventListener("click", () => {
				const tripId = offTripId.value;
				window.location.href = `./doOff.php?id=${tripId}`;
			});

		</script>
</body>

</html>