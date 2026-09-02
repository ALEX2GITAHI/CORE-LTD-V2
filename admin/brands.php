<?php

session_start();

require_once __DIR__ . '/../includes/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| SEARCH
|--------------------------------------------------------------------------
*/

$search = trim($_GET['search'] ?? '');


/*
|--------------------------------------------------------------------------
| PAGINATION
|--------------------------------------------------------------------------
*/

$perPage = 8;

$page = isset($_GET['page'])
    ? (int) $_GET['page']
    : 1;

if ($page < 1) {
    $page = 1;
}


/*
|--------------------------------------------------------------------------
| COUNT TOTAL BRANDS
|--------------------------------------------------------------------------
| This counts ALL brands matching the search.
| It is NOT affected by LIMIT/OFFSET.
|--------------------------------------------------------------------------
*/

$countSql = "
    SELECT COUNT(*) AS total
    FROM brands b
";


$countParams = [];
$countTypes = "";


if ($search !== '') {

    $countSql .= "
        WHERE
            b.name LIKE ?
            OR b.slug LIKE ?
            OR b.description LIKE ?
    ";

    $term = "%" . $search . "%";

    $countParams[] = $term;
    $countParams[] = $term;
    $countParams[] = $term;

    $countTypes = "sss";
}


$countStmt = $conn->prepare($countSql);

if (!$countStmt) {
    die("Brand count query failed: " . $conn->error);
}


if (!empty($countParams)) {

    $countStmt->bind_param(
        $countTypes,
        ...$countParams
    );
}


$countStmt->execute();

$countResult = $countStmt->get_result();

$countRow = $countResult->fetch_assoc();

$totalMatchingBrands = (int) ($countRow['total'] ?? 0);

$countStmt->close();


/*
|--------------------------------------------------------------------------
| TOTAL PAGES
|--------------------------------------------------------------------------
*/

$totalPages = max(
    1,
    (int) ceil(
        $totalMatchingBrands / $perPage
    )
);


/*
|--------------------------------------------------------------------------
| MAKE SURE PAGE IS VALID
|--------------------------------------------------------------------------
*/

if ($page > $totalPages) {
    $page = $totalPages;
}


/*
|--------------------------------------------------------------------------
| OFFSET
|--------------------------------------------------------------------------
*/

$offset = ($page - 1) * $perPage;


/*
|--------------------------------------------------------------------------
| GET BRANDS + PRODUCT COUNT
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT
        b.id,
        b.name,
        b.slug,
        b.description,
        b.logo,
        b.status,
        b.created_at,

        COUNT(p.id) AS product_count

    FROM brands b

    LEFT JOIN products p
        ON p.brand_id = b.id
";


$params = [];
$types = "";


/*
|--------------------------------------------------------------------------
| SEARCH FILTER
|--------------------------------------------------------------------------
*/

if ($search !== '') {

    $sql .= "
        WHERE
            b.name LIKE ?
            OR b.slug LIKE ?
            OR b.description LIKE ?
    ";

    $term = "%" . $search . "%";

    $params[] = $term;
    $params[] = $term;
    $params[] = $term;

    $types = "sss";
}


/*
|--------------------------------------------------------------------------
| GROUP + ORDER + PAGINATION
|--------------------------------------------------------------------------
*/

$sql .= "
    GROUP BY
        b.id,
        b.name,
        b.slug,
        b.description,
        b.logo,
        b.status,
        b.created_at

    ORDER BY b.id DESC

    LIMIT ? OFFSET ?
";


$params[] = $perPage;
$params[] = $offset;

$types .= "ii";


/*
|--------------------------------------------------------------------------
| EXECUTE BRAND QUERY
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Database error: " . $conn->error);
}


$stmt->bind_param(
    $types,
    ...$params
);


$stmt->execute();

$result = $stmt->get_result();


/*
|--------------------------------------------------------------------------
| STORE CURRENT PAGE BRANDS
|--------------------------------------------------------------------------
*/

$brands = [];


while ($row = $result->fetch_assoc()) {

    $brands[] = $row;
}


$stmt->close();


/*
|--------------------------------------------------------------------------
| STATISTICS
|--------------------------------------------------------------------------
| These are calculated separately so they represent ALL brands,
| not only the 8 currently displayed.
|--------------------------------------------------------------------------
*/


/*
|--------------------------------------------------------------------------
| TOTAL BRANDS
|--------------------------------------------------------------------------
*/

$totalBrands = 0;
$activeBrands = 0;
$inactiveBrands = 0;
$totalProducts = 0;


$statsSql = "
    SELECT

        COUNT(DISTINCT b.id) AS total_brands,

        COUNT(
            DISTINCT CASE
                WHEN b.status = 1
                THEN b.id
            END
        ) AS active_brands,

        COUNT(
            DISTINCT CASE
                WHEN b.status = 0
                THEN b.id
            END
        ) AS inactive_brands,

        COUNT(p.id) AS total_products

    FROM brands b

    LEFT JOIN products p
        ON p.brand_id = b.id
";


$statsStmt = $conn->prepare($statsSql);

if (!$statsStmt) {
    die("Statistics query failed: " . $conn->error);
}


$statsStmt->execute();

$statsResult = $statsStmt->get_result();

$stats = $statsResult->fetch_assoc();


$totalBrands =
    (int) ($stats['total_brands'] ?? 0);

$activeBrands =
    (int) ($stats['active_brands'] ?? 0);

$inactiveBrands =
    (int) ($stats['inactive_brands'] ?? 0);

$totalProducts =
    (int) ($stats['total_products'] ?? 0);


$statsStmt->close();


/*
|--------------------------------------------------------------------------
| PAGINATION RANGE
|--------------------------------------------------------------------------
*/

$startItem = 0;
$endItem = 0;


if ($totalMatchingBrands > 0) {

    $startItem =
        $offset + 1;

    $endItem =
        min(
            $offset + $perPage,
            $totalMatchingBrands
        );
}


/*
|--------------------------------------------------------------------------
| PAGE TITLE
|--------------------------------------------------------------------------
*/

$pageTitle = "Brands | CORE LTD Admin";

?>


<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        Brands | CORE LTD Admin
    </title>


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">


    <style>
    * {
        box-sizing: border-box;
    }


    body {

        margin: 0;

        font-family:
            "Segoe UI",
            Arial,
            sans-serif;

        background: #f4f7fb;

        color: #26364a;
    }


    /* =====================================================
           SIDEBAR
        ===================================================== */

    .sidebar {

        position: fixed;

        top: 0;
        left: 0;

        width: 245px;

        height: 100vh;

        background:
            linear-gradient(180deg,
                #073b75 0%,
                #0b5cad 55%,
                #0878d1 100%);

        color: #fff;

        padding: 25px 15px;

        box-shadow:
            4px 0 18px rgba(0, 61, 120, .12);

        z-index: 1000;
    }


    .brand {

        text-align: center;

        padding:
            5px 10px 25px;

        border-bottom:
            1px solid rgba(255, 255, 255, .15);

        margin-bottom: 20px;
    }


    .brand-icon {

        width: 55px;
        height: 55px;

        margin: 0 auto 10px;

        border-radius: 15px;

        background:
            rgba(255, 255, 255, .15);

        display: flex;

        align-items: center;
        justify-content: center;

        font-size: 25px;
    }


    .brand h2 {

        font-size: 20px;

        font-weight: 800;

        margin: 0;
    }


    .brand span {

        display: block;

        margin-top: 4px;

        font-size: 11px;

        opacity: .75;

        letter-spacing: 1px;
    }


    .nav-title {

        font-size: 10px;

        text-transform: uppercase;

        letter-spacing: 1.5px;

        opacity: .6;

        margin:
            20px 10px 8px;
    }


    .sidebar a {

        display: flex;

        align-items: center;

        gap: 12px;

        color:
            rgba(255, 255, 255, .85);

        text-decoration: none;

        padding:
            11px 13px;

        border-radius: 9px;

        margin-bottom: 5px;

        font-size: 14px;

        transition: .2s;
    }


    .sidebar a i {

        width: 20px;

        text-align: center;
    }


    .sidebar a:hover {

        background:
            rgba(255, 255, 255, .12);

        color: #fff;
    }


    .sidebar a.active {

        background: #fff;

        color: #07549a;

        font-weight: 700;
    }


    /* =====================================================
           MAIN
        ===================================================== */

    .main {

        margin-left: 245px;

        min-height: 100vh;

        padding: 28px;
    }


    /* =====================================================
           TOPBAR
        ===================================================== */

    .topbar {

        background: #fff;

        border-radius: 14px;

        padding:
            18px 22px;

        display: flex;

        align-items: center;

        justify-content: space-between;

        box-shadow:
            0 5px 25px rgba(25, 65, 100, .06);

        margin-bottom: 25px;
    }


    .page-title {

        margin: 0;

        font-size: 25px;

        font-weight: 800;

        color: #17365d;
    }


    .page-subtitle {

        margin:
            4px 0 0;

        color: #7a8998;

        font-size: 13px;
    }


    .admin-user {

        display: flex;

        align-items: center;

        gap: 10px;
    }


    .admin-avatar {

        width: 42px;
        height: 42px;

        border-radius: 50%;

        background:
            linear-gradient(135deg,
                #0d6efd,
                #0a58ca);

        color: #fff;

        display: flex;

        align-items: center;
        justify-content: center;

        font-weight: 700;
    }


    /* =====================================================
           STATISTICS
        ===================================================== */

    .stats-grid {

        display: grid;

        grid-template-columns:
            repeat(4, 1fr);

        gap: 16px;

        margin-bottom: 22px;
    }


    .stat-card {

        background: #fff;

        border-radius: 13px;

        padding: 18px;

        box-shadow:
            0 5px 20px rgba(25, 65, 100, .05);

        display: flex;

        align-items: center;

        gap: 14px;
    }


    .stat-icon {

        width: 48px;
        height: 48px;

        border-radius: 11px;

        display: flex;

        align-items: center;
        justify-content: center;

        font-size: 18px;
    }


    .blue {

        background: #e8f2ff;

        color: #0d6efd;
    }


    .green {

        background: #eaf8f0;

        color: #198754;
    }


    .red {

        background: #fff0f1;

        color: #dc3545;
    }


    .orange {

        background: #fff5e6;

        color: #fd7e14;
    }


    .stat-text span {

        display: block;

        color: #8493a3;

        font-size: 11px;

        margin-bottom: 3px;
    }


    .stat-text strong {

        color: #17365d;

        font-size: 21px;
    }


    /* =====================================================
           CONTENT CARD
        ===================================================== */

    .content-card {

        background: #fff;

        border-radius: 15px;

        box-shadow:
            0 5px 25px rgba(25, 65, 100, .06);

        overflow: hidden;
    }


    .card-header-custom {

        padding:
            20px 22px;

        border-bottom:
            1px solid #edf1f5;

        display: flex;

        align-items: center;

        justify-content: space-between;

        gap: 15px;
    }


    .header-left h3 {

        margin: 0;

        font-size: 18px;

        color: #17365d;

        font-weight: 750;
    }


    .header-left p {

        margin:
            4px 0 0;

        color: #8a98a8;

        font-size: 12px;
    }


    /* =====================================================
           BUTTON
        ===================================================== */

    .btn-primary-custom {

        background:
            linear-gradient(135deg,
                #0d6efd,
                #0754b8);

        color: #fff;

        border: none;

        padding:
            10px 15px;

        border-radius: 8px;

        font-size: 12px;

        font-weight: 700;

        text-decoration: none;

        display: inline-flex;

        align-items: center;

        gap: 7px;
    }


    .btn-primary-custom:hover {

        color: #fff;

        transform:
            translateY(-1px);
    }


    /* =====================================================
           SEARCH
        ===================================================== */

    .toolbar {

        padding:
            17px 22px;

        background: #fbfcfe;

        border-bottom:
            1px solid #edf1f5;

        display: flex;

        justify-content: space-between;

        align-items: center;

        gap: 15px;
    }


    .search-form {

        display: flex;

        max-width: 450px;

        width: 100%;
    }


    .search-box {

        position: relative;

        flex: 1;
    }


    .search-box i {

        position: absolute;

        left: 13px;

        top: 50%;

        transform:
            translateY(-50%);

        color: #8c9aaa;

        font-size: 13px;
    }


    .search-box input {

        width: 100%;

        padding:
            10px 12px 10px 37px;

        border:
            1px solid #dce5ee;

        border-radius:
            8px 0 0 8px;

        outline: none;

        font-size: 12px;
    }


    .search-box input:focus {

        border-color: #0d6efd;
    }


    .search-btn {

        border: none;

        background: #0d6efd;

        color: #fff;

        padding:
            0 17px;

        border-radius:
            0 8px 8px 0;

        font-size: 12px;

        font-weight: 700;
    }


    /* =====================================================
           TABLE
        ===================================================== */

    .table-wrap {

        overflow-x: auto;
    }


    table {

        width: 100%;

        border-collapse: collapse;
    }


    thead th {

        background: #f7f9fc;

        color: #718092;

        font-size: 10px;

        text-transform: uppercase;

        letter-spacing: .5px;

        padding:
            13px 15px;

        border-bottom:
            1px solid #e7edf3;

        white-space: nowrap;
    }


    tbody td {

        padding: 15px;

        border-bottom:
            1px solid #edf1f5;

        vertical-align: middle;

        font-size: 12px;

        color: #536476;
    }


    tbody tr:hover {

        background: #fbfdff;
    }


    /* =====================================================
           BRAND
        ===================================================== */

    .brand-info {

        display: flex;

        align-items: center;

        gap: 11px;
    }


    .brand-logo {

        width: 42px;
        height: 42px;

        border-radius: 9px;

        background: #f0f5fa;

        display: flex;

        align-items: center;
        justify-content: center;

        overflow: hidden;

        color: #0d6efd;

        font-weight: 800;

        font-size: 14px;
    }


    .brand-logo img {

        width: 100%;
        height: 100%;

        object-fit: contain;
    }


    .brand-info strong {

        display: block;

        color: #17365d;

        font-size: 13px;

        margin-bottom: 2px;
    }


    .brand-info span {

        color: #91a0ae;

        font-size: 10px;
    }


    /* =====================================================
           SLUG
        ===================================================== */

    .slug {

        background: #f5f7fa;

        color: #60758c;

        padding:
            5px 8px;

        border-radius: 5px;

        font-family:
            Consolas,
            monospace;

        font-size: 10px;
    }


    /* =====================================================
           DESCRIPTION
        ===================================================== */

    .description {

        max-width: 270px;

        line-height: 1.5;

        color: #718092;
    }


    /* =====================================================
           PRODUCT COUNT
        ===================================================== */

    .product-count {

        display: inline-flex;

        align-items: center;

        gap: 6px;

        padding:
            6px 9px;

        border-radius: 7px;

        background: #eaf3ff;

        color: #0754b8;

        font-weight: 700;

        font-size: 11px;
    }


    /* =====================================================
           STATUS
        ===================================================== */

    .status {

        display: inline-flex;

        align-items: center;

        gap: 6px;

        padding:
            6px 9px;

        border-radius: 20px;

        font-size: 10px;

        font-weight: 700;
    }


    .status.active {

        background: #eaf8f0;

        color: #198754;
    }


    .status.inactive {

        background: #fff0f1;

        color: #dc3545;
    }


    .status-dot {

        width: 6px;
        height: 6px;

        border-radius: 50%;

        background: currentColor;
    }


    /* =====================================================
           ACTIONS
        ===================================================== */

    .actions {

        display: flex;

        gap: 5px;
    }


    .action-btn {

        width: 32px;
        height: 32px;

        border-radius: 7px;

        display: inline-flex;

        align-items: center;
        justify-content: center;

        text-decoration: none;

        font-size: 11px;

        transition: .2s;
    }


    .action-view {

        background: #eaf8f0;

        color: #198754;
    }


    .action-view:hover {

        background: #198754;

        color: #fff;
    }


    .action-edit {

        background: #e8f2ff;

        color: #0d6efd;
    }


    .action-edit:hover {

        background: #0d6efd;

        color: #fff;
    }


    .action-delete {

        background: #fff0f1;

        color: #dc3545;
    }


    .action-delete:hover {

        background: #dc3545;

        color: #fff;
    }


    /* =====================================================
           EMPTY
        ===================================================== */

    .empty-state {

        text-align: center;

        padding: 70px 20px;
    }


    .empty-icon {

        width: 70px;
        height: 70px;

        margin:
            0 auto 15px;

        border-radius: 50%;

        background: #e8f2ff;

        color: #0d6efd;

        display: flex;

        align-items: center;
        justify-content: center;

        font-size: 27px;
    }


    .empty-state h4 {

        color: #17365d;

        font-size: 18px;

        margin-bottom: 7px;
    }


    .empty-state p {

        color: #8493a3;

        font-size: 12px;

        margin-bottom: 20px;
    }


    /* =====================================================
           FOOTER
        ===================================================== */

    .table-footer {

        padding:
            15px 22px;

        border-top:
            1px solid #edf1f5;

        color: #8493a3;

        font-size: 11px;
    }


    /* =====================================================
           RESPONSIVE
        ===================================================== */

    @media(max-width:1100px) {

        .stats-grid {

            grid-template-columns:
                repeat(2, 1fr);
        }

    }


    @media(max-width:900px) {

        .sidebar {

            width: 70px;

            padding:
                20px 8px;
        }


        .brand h2,
        .brand span,
        .sidebar a span,
        .nav-title {

            display: none;
        }


        .brand {

            border: none;
        }


        .sidebar a {

            justify-content: center;
        }


        .sidebar a i {

            width: auto;
        }


        .main {

            margin-left: 70px;

            padding: 18px;
        }

    }


    @media(max-width:650px) {

        .topbar {

            flex-direction: column;

            align-items: flex-start;

            gap: 15px;
        }


        .stats-grid {

            grid-template-columns: 1fr;
        }


        .card-header-custom {

            flex-direction: column;

            align-items: flex-start;
        }


        .toolbar {

            flex-direction: column;

            align-items: stretch;
        }


        .search-form {

            max-width: none;
        }

    }

    /* =====================================================
   PAGINATION
===================================================== */

    .pagination-wrapper {
        padding: 18px 22px;
        border-top: 1px solid #edf1f5;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        flex-wrap: wrap;
    }

    .pagination-info {
        color: #8493a3;
        font-size: 11px;
    }

    .pagination-info strong {
        color: #17365d;
    }

    .pagination {
        display: flex;
        align-items: center;
        gap: 5px;
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .pagination a,
    .pagination span {
        min-width: 32px;
        height: 32px;
        padding: 0 9px;
        border-radius: 7px;

        display: inline-flex;
        align-items: center;
        justify-content: center;

        text-decoration: none;

        font-size: 11px;
        font-weight: 700;

        border: 1px solid #e1e8ef;

        background: #fff;
        color: #526579;

        transition: .2s;
    }

    .pagination a:hover {
        background: #e8f2ff;
        border-color: #0d6efd;
        color: #0d6efd;
    }

    .pagination .active {
        background: #0d6efd;
        border-color: #0d6efd;
        color: #fff;
    }

    .pagination .disabled {
        opacity: .45;
        cursor: not-allowed;
    }

    .pagination .dots {
        border: none;
        background: transparent;
        min-width: 20px;
    }

    @media(max-width:650px) {

        .pagination-wrapper {
            flex-direction: column;
            align-items: flex-start;
        }

        .pagination {
            width: 100%;
            flex-wrap: wrap;
        }

    }
    </style>

</head>


<body>


    <!-- =========================================================
     SIDEBAR
========================================================= -->

    <aside class="sidebar">


        <div class="brand">

            <div class="brand-icon">

                <i class="fa-solid fa-network-wired"></i>

            </div>


            <h2>
                CORE LTD
            </h2>


            <span>
                ADMIN PANEL
            </span>

        </div>


        <div class="nav-title">
            Main
        </div>


        <a href="index.php">

            <i class="fa-solid fa-gauge"></i>

            <span>
                Dashboard
            </span>

        </a>


        <a href="products.php">

            <i class="fa-solid fa-box"></i>

            <span>
                Products
            </span>

        </a>


        <a href="categories.php">

            <i class="fa-solid fa-layer-group"></i>

            <span>
                Categories
            </span>

        </a>


        <a href="brands.php" class="active">

            <i class="fa-solid fa-tags"></i>

            <span>
                Brands
            </span>

        </a>


        <div class="nav-title">
            Sales
        </div>


        <a href="orders.php">

            <i class="fa-solid fa-cart-shopping"></i>

            <span>
                Orders
            </span>

        </a>







        <div class="nav-title">
            System
        </div>


        <a href="settings.php">

            <i class="fa-solid fa-gear"></i>

            <span>
                Settings
            </span>

        </a>


        <a href="logout.php">

            <i class="fa-solid fa-right-from-bracket"></i>

            <span>
                Logout
            </span>

        </a>


    </aside>



    <!-- =========================================================
     MAIN
========================================================= -->

    <main class="main">


        <!-- TOPBAR -->

        <div class="topbar">


            <div>

                <h1 class="page-title">
                    Brands
                </h1>


                <p class="page-subtitle">

                    Manage the manufacturers and brands
                    attached to your CORE LTD products.

                </p>

            </div>


            <div class="admin-user">


                <div class="admin-avatar">

                    <?= htmlspecialchars(
                        strtoupper(
                            substr(
                                $_SESSION['admin_name']
                                    ?? 'A',
                                0,
                                1
                            )
                        )
                    ) ?>

                </div>


                <div>

                    <strong style="font-size:13px;">

                        <?= htmlspecialchars(
                            $_SESSION['admin_name']
                                ?? 'Administrator'
                        ) ?>

                    </strong>


                    <div style="
                        font-size:11px;
                        color:#8a98a8;
                    ">

                        Administrator

                    </div>

                </div>


            </div>


        </div>



        <!-- =====================================================
         STATISTICS
    ===================================================== -->

        <div class="stats-grid">


            <div class="stat-card">

                <div class="stat-icon blue">

                    <i class="fa-solid fa-tags"></i>

                </div>


                <div class="stat-text">

                    <span>
                        Total Brands
                    </span>

                    <strong>
                        <?= $totalBrands ?>
                    </strong>

                </div>

            </div>



            <div class="stat-card">

                <div class="stat-icon green">

                    <i class="fa-solid fa-circle-check"></i>

                </div>


                <div class="stat-text">

                    <span>
                        Active Brands
                    </span>

                    <strong>
                        <?= $activeBrands ?>
                    </strong>

                </div>

            </div>



            <div class="stat-card">

                <div class="stat-icon red">

                    <i class="fa-solid fa-circle-xmark"></i>

                </div>


                <div class="stat-text">

                    <span>
                        Inactive Brands
                    </span>

                    <strong>
                        <?= $inactiveBrands ?>
                    </strong>

                </div>

            </div>



            <div class="stat-card">

                <div class="stat-icon orange">

                    <i class="fa-solid fa-box"></i>

                </div>


                <div class="stat-text">

                    <span>
                        Products by Brands
                    </span>

                    <strong>
                        <?= $totalProducts ?>
                    </strong>

                </div>

            </div>


        </div>



        <!-- =====================================================
         CONTENT
    ===================================================== -->

        <div class="content-card">


            <div class="card-header-custom">


                <div class="header-left">

                    <h3>
                        Product Brands
                    </h3>


                    <p>

                        Manage Tenda, TP-Link, MikroTik,
                        Huawei, D-Link and other brands.

                    </p>

                </div>


                <a href="brand-add.php" class="btn-primary-custom">

                    <i class="fa-solid fa-plus"></i>

                    Add Brand

                </a>


            </div>



            <!-- SEARCH -->

            <div class="toolbar">


                <form method="GET" action="brands.php" class="search-form">


                    <div class="search-box">

                        <i class="fa-solid
                               fa-magnifying-glass"></i>


                        <input type="text" name="search" placeholder="Search brands..."
                            value="<?= htmlspecialchars($search) ?>">

                    </div>


                    <button type="submit" class="search-btn">

                        Search

                    </button>


                </form>


                <?php if ($search !== ''): ?>

                <a href="brands.php" style="
                        font-size:11px;
                        color:#0d6efd;
                        text-decoration:none;
                    ">

                    <i class="fa-solid fa-xmark"></i>

                    Clear Search

                </a>

                <?php endif; ?>


            </div>



            <!-- =================================================
             TABLE
        ================================================= -->

            <?php if (!empty($brands)): ?>


            <div class="table-wrap">


                <table>


                    <thead>

                        <tr>

                            <th>
                                Brand
                            </th>

                            <th>
                                Slug
                            </th>

                            <th>
                                Description
                            </th>

                            <th>
                                Products
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Created
                            </th>

                            <th>
                                Actions
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                        <?php foreach ($brands as $brand): ?>


                        <tr>


                            <!-- BRAND -->

                            <td>


                                <div class="brand-info">


                                    <div class="brand-logo">


                                        <?php

                                                $logo =
                                                    trim(
                                                        $brand['logo']
                                                            ?? ''
                                                    );

                                                ?>


                                        <?php if ($logo !== ''): ?>

                                        <img src="../uploads/brands/<?= htmlspecialchars($logo) ?>"
                                            alt="<?= htmlspecialchars($brand['name']) ?>"
                                            onerror="this.style.display='none';">

                                        <?php else: ?>

                                        <?= htmlspecialchars(
                                                        strtoupper(
                                                            substr(
                                                                $brand['name'],
                                                                0,
                                                                2
                                                            )
                                                        )
                                                    ) ?>

                                        <?php endif; ?>


                                    </div>


                                    <div>

                                        <strong>

                                            <?= htmlspecialchars(
                                                        $brand['name']
                                                    ) ?>

                                        </strong>


                                        <span>

                                            ID #<?= (int)$brand['id'] ?>

                                        </span>

                                    </div>


                                </div>


                            </td>



                            <!-- SLUG -->

                            <td>

                                <span class="slug">

                                    <?= htmlspecialchars(
                                                $brand['slug']
                                            ) ?>

                                </span>

                            </td>



                            <!-- DESCRIPTION -->

                            <td>

                                <div class="description">


                                    <?php

                                            $description =
                                                trim(
                                                    $brand['description']
                                                        ?? ''
                                                );

                                            if ($description === ''):

                                            ?>

                                    <span style="
                                                color:#aab4bf;
                                                font-style:italic;
                                            ">

                                        No description

                                    </span>

                                    <?php else: ?>

                                    <?= htmlspecialchars(
                                                    strlen($description) > 100
                                                        ? substr(
                                                            $description,
                                                            0,
                                                            100
                                                        ) . '...'
                                                        : $description
                                                ) ?>

                                    <?php endif; ?>


                                </div>

                            </td>



                            <!-- PRODUCTS -->

                            <td>

                                <span class="product-count">

                                    <i class="fa-solid fa-box"></i>

                                    <?= (int)$brand['product_count'] ?>

                                </span>

                            </td>



                            <!-- STATUS -->

                            <td>


                                <?php if (
                                            (int)$brand['status'] === 1
                                        ): ?>


                                <span class="
                                            status
                                            active
                                        ">

                                    <span class="status-dot"></span>

                                    Active

                                </span>


                                <?php else: ?>


                                <span class="
                                            status
                                            inactive
                                        ">

                                    <span class="status-dot"></span>

                                    Inactive

                                </span>


                                <?php endif; ?>


                            </td>



                            <!-- CREATED -->

                            <td>

                                <span style="
                                        color:#8493a3;
                                        font-size:11px;
                                    ">

                                    <?= date(
                                                'd M Y',
                                                strtotime(
                                                    $brand['created_at']
                                                )
                                            ) ?>

                                </span>

                            </td>



                            <!-- ACTIONS -->

                            <td>


                                <div class="actions">


                                    <!-- VIEW PRODUCTS -->

                                    <a href="products.php?brand=<?= (int)$brand['id'] ?>" class="
                                            action-btn
                                            action-view
                                        " title="View Products">

                                        <i class="fa-solid fa-box"></i>

                                    </a>


                                    <!-- EDIT -->

                                    <a href="brand-edit.php?id=<?= (int)$brand['id'] ?>" class="
                                            action-btn
                                            action-edit
                                        " title="Edit Brand">

                                        <i class="fa-solid fa-pen"></i>

                                    </a>


                                    <!-- DELETE -->

                                    <a href="brand-delete.php?id=<?= (int)$brand['id'] ?>" class="
                                            action-btn
                                            action-delete
                                        " title="Delete Brand" onclick="
                                            return confirm(
                                                'Are you sure you want to delete this brand?'
                                            );
                                        ">

                                        <i class="fa-solid fa-trash"></i>

                                    </a>


                                </div>


                            </td>


                        </tr>


                        <?php endforeach; ?>


                    </tbody>


                </table>


            </div>


            <div class="pagination-wrapper">

                <div class="pagination-info">

                    <?php if ($totalMatchingBrands > 0): ?>

                    Showing
                    <strong><?= $startItem; ?></strong>
                    -
                    <strong><?= $endItem; ?></strong>

                    of

                    <strong><?= $totalMatchingBrands; ?></strong>

                    brand<?= $totalMatchingBrands === 1 ? '' : 's'; ?>

                    <?php else: ?>

                    No brands found.

                    <?php endif; ?>

                </div>


                <?php if ($totalPages > 1): ?>

                <ul class="pagination">

                    <!-- PREVIOUS -->

                    <?php if ($page > 1): ?>

                    <li>
                        <a href="?<?= http_build_query([
                                                    'search' => $search,
                                                    'page'   => $page - 1
                                                ]); ?>">

                            <i class="fa-solid fa-chevron-left"></i>

                        </a>
                    </li>

                    <?php else: ?>

                    <li>
                        <span class="disabled">

                            <i class="fa-solid fa-chevron-left"></i>

                        </span>
                    </li>

                    <?php endif; ?>


                    <?php

                            /*
            |--------------------------------------------------------------------------
            | PAGE NUMBERS
            |--------------------------------------------------------------------------
            */

                            $visiblePages = [];


                            // Always show first page
                            $visiblePages[] = 1;


                            // Pages around current page
                            for (
                                $i = max(2, $page - 2);
                                $i <= min($totalPages - 1, $page + 2);
                                $i++
                            ) {

                                $visiblePages[] = $i;
                            }


                            // Always show last page
                            if ($totalPages > 1) {

                                $visiblePages[] = $totalPages;
                            }


                            $visiblePages = array_unique(
                                $visiblePages
                            );

                            sort($visiblePages);


                            $previousPage = 0;


                            foreach ($visiblePages as $paginationPage):

                                /*
                |--------------------------------------------------------------------------
                | DOTS
                |--------------------------------------------------------------------------
                */

                                if (
                                    $previousPage > 0 &&
                                    $paginationPage > $previousPage + 1
                                ):
                            ?>

                    <li>
                        <span class="dots">
                            ...
                        </span>
                    </li>

                    <?php
                                endif;
                                ?>


                    <!-- PAGE -->

                    <li>

                        <?php if ($paginationPage == $page): ?>

                        <span class="active">

                            <?= $paginationPage; ?>

                        </span>

                        <?php else: ?>

                        <a href="?<?= http_build_query([
                                                        'search' => $search,
                                                        'page'   => $paginationPage
                                                    ]); ?>">

                            <?= $paginationPage; ?>

                        </a>

                        <?php endif; ?>

                    </li>


                    <?php

                                $previousPage = $paginationPage;

                            endforeach;
                            ?>


                    <!-- NEXT -->

                    <?php if ($page < $totalPages): ?>

                    <li>

                        <a href="?<?= http_build_query([
                                                    'search' => $search,
                                                    'page'   => $page + 1
                                                ]); ?>">

                            <i class="fa-solid fa-chevron-right"></i>

                        </a>

                    </li>

                    <?php else: ?>

                    <li>

                        <span class="disabled">

                            <i class="fa-solid fa-chevron-right"></i>

                        </span>

                    </li>

                    <?php endif; ?>

                </ul>

                <?php endif; ?>

            </div>


            <?php else: ?>


            <!-- EMPTY -->

            <div class="empty-state">


                <div class="empty-icon">

                    <i class="fa-solid fa-tags"></i>

                </div>


                <?php if ($search !== ''): ?>


                <h4>
                    No Brands Found
                </h4>


                <p>

                    No brand matches
                    "<strong><?= htmlspecialchars($search) ?></strong>".

                </p>


                <a href="brands.php" class="btn-primary-custom">

                    <i class="fa-solid fa-rotate-left"></i>

                    Clear Search

                </a>


                <?php else: ?>


                <h4>
                    No Brands Yet
                </h4>


                <p>

                    Add your first manufacturer or
                    product brand to begin organizing
                    your catalogue.

                </p>


                <a href="brand-add.php" class="btn-primary-custom">

                    <i class="fa-solid fa-plus"></i>

                    Add First Brand

                </a>


                <?php endif; ?>


            </div>


            <?php endif; ?>


        </div>


    </main>


</body>

</html>