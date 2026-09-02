<?php

session_start();

require_once __DIR__ . '/../includes/db.php';


/*
|--------------------------------------------------------------------------
| ADMIN ACCESS
|--------------------------------------------------------------------------
*/

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
| CATEGORY QUERY
|--------------------------------------------------------------------------
|
| We also count products belonging to each category.
|
*/

$sql = "
    SELECT
        c.id,
        c.name,
        c.slug,
        c.description,
        c.status,
        c.created_at,
        COUNT(p.id) AS product_count
    FROM categories c
    LEFT JOIN products p
        ON p.category_id = c.id
";


$params = [];
$types = "";


if ($search !== '') {

    $sql .= "
        WHERE
            c.name LIKE ?
            OR c.slug LIKE ?
            OR c.description LIKE ?
    ";

    $searchTerm = '%' . $search . '%';

    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;

    $types = "sss";
}


$sql .= "
    GROUP BY
        c.id,
        c.name,
        c.slug,
        c.description,
        c.status,
        c.created_at
    ORDER BY
        c.id DESC
";


$stmt = $conn->prepare($sql);


if (!empty($params)) {

    $stmt->bind_param(
        $types,
        ...$params
    );
}


$stmt->execute();

$result = $stmt->get_result();


/*
|--------------------------------------------------------------------------
| TOTAL CATEGORIES
|--------------------------------------------------------------------------
*/

$totalCategories = 0;
$activeCategories = 0;
$inactiveCategories = 0;
$totalProducts = 0;

$categories = [];


while ($row = $result->fetch_assoc()) {

    $categories[] = $row;

    $totalCategories++;

    if ((int)$row['status'] === 1) {
        $activeCategories++;
    } else {
        $inactiveCategories++;
    }

    $totalProducts +=
        (int)$row['product_count'];
}


$stmt->close();

?>


<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        Categories | CORE LTD Admin
    </title>


    <!-- BOOTSTRAP -->

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">


    <!-- FONT AWESOME -->

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

        background:
            #f4f7fb;

        color:
            #26364a;
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

        padding:
            25px 15px;

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

        margin:
            0 auto 10px;

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

        color:
            #7a8998;

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
           STAT CARDS
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

        padding:
            18px;

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


    .stat-icon.blue {

        background: #e8f2ff;

        color: #0d6efd;
    }


    .stat-icon.green {

        background: #eaf8f0;

        color: #198754;
    }


    .stat-icon.red {

        background: #fff0f1;

        color: #dc3545;
    }


    .stat-icon.orange {

        background: #fff5e6;

        color: #fd7e14;
    }


    .stat-text span {

        display: block;

        color:
            #8493a3;

        font-size: 11px;

        margin-bottom: 3px;
    }


    .stat-text strong {

        color:
            #17365d;

        font-size: 21px;
    }


    /* =====================================================
           MAIN CARD
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

        color:
            #17365d;

        font-weight: 750;
    }


    .header-left p {

        margin:
            4px 0 0;

        color:
            #8a98a8;

        font-size: 12px;
    }


    /* =====================================================
           BUTTONS
        ===================================================== */

    .btn {

        border: none;

        padding:
            10px 15px;

        border-radius: 8px;

        font-size: 12px;

        font-weight: 700;

        display: inline-flex;

        align-items: center;

        justify-content: center;

        gap: 7px;

        text-decoration: none;

        transition: .2s;
    }


    .btn-primary-custom {

        background:
            linear-gradient(135deg,
                #0d6efd,
                #0754b8);

        color: #fff;
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

        background:
            #fbfcfe;

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

        color:
            #8c9aaa;

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

        border-color:
            #0d6efd;
    }


    .search-btn {

        border: none;

        background:
            #0d6efd;

        color: #fff;

        padding:
            0 17px;

        border-radius:
            0 8px 8px 0;

        font-size: 12px;

        font-weight: 700;
    }


    .clear-search {

        margin-left: 8px;

        padding:
            10px 13px;

        border-radius: 8px;

        background:
            #f1f3f5;

        color:
            #566574;

        text-decoration: none;

        font-size: 12px;

        display: inline-flex;

        align-items: center;
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

        background:
            #f7f9fc;

        color:
            #718092;

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

        padding:
            15px;

        border-bottom:
            1px solid #edf1f5;

        vertical-align: middle;

        font-size: 12px;

        color:
            #536476;
    }


    tbody tr:hover {

        background:
            #fbfdff;
    }


    tbody tr:last-child td {

        border-bottom: none;
    }


    /* CATEGORY NAME */

    .category-name {

        display: flex;

        align-items: center;

        gap: 11px;
    }


    .category-icon {

        width: 38px;
        height: 38px;

        border-radius: 9px;

        background:
            #e8f2ff;

        color:
            #0d6efd;

        display: flex;

        align-items: center;
        justify-content: center;
    }


    .category-name strong {

        display: block;

        color:
            #17365d;

        font-size: 13px;

        margin-bottom: 2px;
    }


    .category-name span {

        color:
            #91a0ae;

        font-size: 10px;
    }


    /* SLUG */

    .slug {

        background:
            #f5f7fa;

        color:
            #60758c;

        padding:
            5px 8px;

        border-radius: 5px;

        font-family:
            Consolas,
            monospace;

        font-size: 10px;
    }


    /* DESCRIPTION */

    .description {

        max-width: 260px;

        color:
            #718092;

        line-height:
            1.5;
    }


    /* PRODUCT COUNT */

    .product-count {

        display: inline-flex;

        align-items: center;

        gap: 6px;

        padding:
            6px 9px;

        border-radius: 7px;

        background:
            #eaf3ff;

        color:
            #0754b8;

        font-weight: 700;

        font-size: 11px;
    }


    /* STATUS */

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

        background:
            #eaf8f0;

        color:
            #198754;
    }


    .status.inactive {

        background:
            #fff0f1;

        color:
            #dc3545;
    }


    .status-dot {

        width: 6px;
        height: 6px;

        border-radius: 50%;

        background:
            currentColor;
    }


    /* ACTIONS */

    .actions {

        display: flex;

        align-items: center;

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

        background:
            #eaf8f0;

        color:
            #198754;
    }


    .action-view:hover {

        background:
            #198754;

        color: #fff;
    }


    .action-edit {

        background:
            #e8f2ff;

        color:
            #0d6efd;
    }


    .action-edit:hover {

        background:
            #0d6efd;

        color: #fff;
    }


    .action-delete {

        background:
            #fff0f1;

        color:
            #dc3545;
    }


    .action-delete:hover {

        background:
            #dc3545;

        color: #fff;
    }


    /* EMPTY */

    .empty-state {

        text-align: center;

        padding:
            70px 20px;
    }


    .empty-icon {

        width: 70px;
        height: 70px;

        margin:
            0 auto 15px;

        border-radius: 50%;

        background:
            #e8f2ff;

        color:
            #0d6efd;

        display: flex;

        align-items: center;
        justify-content: center;

        font-size: 27px;
    }


    .empty-state h4 {

        color:
            #17365d;

        font-size: 18px;

        margin-bottom: 7px;
    }


    .empty-state p {

        color:
            #8493a3;

        font-size: 12px;

        margin-bottom: 20px;
    }


    /* FOOTER */

    .table-footer {

        padding:
            15px 22px;

        border-top:
            1px solid #edf1f5;

        color:
            #8493a3;

        font-size: 11px;
    }


    /* ALERT */

    .alert-custom {

        border-radius: 10px;

        padding:
            13px 16px;

        font-size: 12px;

        margin-bottom: 20px;

        display: flex;

        align-items: center;

        gap: 10px;
    }


    .alert-success-custom {

        background:
            #eaf8f0;

        border:
            1px solid #b9e6ca;

        color:
            #146c43;
    }


    .alert-danger-custom {

        background:
            #fff0f1;

        border:
            1px solid #f4c7cb;

        color:
            #b02a37;
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

            flex-direction:
                column;

            align-items:
                flex-start;

            gap: 15px;
        }


        .stats-grid {

            grid-template-columns:
                1fr;
        }


        .card-header-custom {

            flex-direction:
                column;

            align-items:
                flex-start;
        }


        .toolbar {

            flex-direction:
                column;

            align-items:
                stretch;
        }


        .search-form {

            max-width:
                none;
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


        <a href="categories.php" class="active">

            <i class="fa-solid fa-layer-group"></i>

            <span>
                Categories
            </span>

        </a>


        <a href="brands.php">

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
                    Categories
                </h1>

                <p class="page-subtitle">

                    Organize your CORE LTD products by category

                </p>

            </div>


            <div class="admin-user">


                <div class="admin-avatar">

                    <?= strtoupper(
                        substr(
                            $_SESSION['admin_name'] ?? 'A',
                            0,
                            1
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
         ALERTS
    ===================================================== -->


        <?php if (
            ($_GET['success'] ?? '') ===
            'category_updated'
        ): ?>

        <div class="alert-custom
                   alert-success-custom">

            <i class="fa-solid
                       fa-circle-check"></i>

            Category updated successfully.

        </div>

        <?php endif; ?>


        <?php if (
            ($_GET['success'] ?? '') ===
            'category_deleted'
        ): ?>

        <div class="alert-custom
                   alert-success-custom">

            <i class="fa-solid
                       fa-circle-check"></i>

            Category deleted successfully.

        </div>

        <?php endif; ?>


        <?php if (
            ($_GET['success'] ?? '') ===
            'category_added'
        ): ?>

        <div class="alert-custom
                   alert-success-custom">

            <i class="fa-solid
                       fa-circle-check"></i>

            Category added successfully.

        </div>

        <?php endif; ?>


        <?php if (
            ($_GET['error'] ?? '') ===
            'category_in_use'
        ): ?>

        <div class="alert-custom
                   alert-danger-custom">

            <i class="fa-solid
                       fa-triangle-exclamation"></i>

            This category cannot be deleted because
            products are still assigned to it.

        </div>

        <?php endif; ?>


        <?php if (
            ($_GET['error'] ?? '') ===
            'category_not_found'
        ): ?>

        <div class="alert-custom
                   alert-danger-custom">

            <i class="fa-solid
                       fa-triangle-exclamation"></i>

            The requested category could not be found.

        </div>

        <?php endif; ?>



        <!-- =====================================================
         STATISTICS
    ===================================================== -->

        <div class="stats-grid">


            <div class="stat-card">

                <div class="stat-icon blue">

                    <i class="fa-solid
                           fa-layer-group"></i>

                </div>


                <div class="stat-text">

                    <span>
                        Total Categories
                    </span>

                    <strong>
                        <?= $totalCategories ?>
                    </strong>

                </div>

            </div>



            <div class="stat-card">

                <div class="stat-icon green">

                    <i class="fa-solid
                           fa-circle-check"></i>

                </div>


                <div class="stat-text">

                    <span>
                        Active Categories
                    </span>

                    <strong>
                        <?= $activeCategories ?>
                    </strong>

                </div>

            </div>



            <div class="stat-card">

                <div class="stat-icon red">

                    <i class="fa-solid
                           fa-circle-xmark"></i>

                </div>


                <div class="stat-text">

                    <span>
                        Inactive Categories
                    </span>

                    <strong>
                        <?= $inactiveCategories ?>
                    </strong>

                </div>

            </div>



            <div class="stat-card">

                <div class="stat-icon orange">

                    <i class="fa-solid
                           fa-box"></i>

                </div>


                <div class="stat-text">

                    <span>
                        Products Categorized
                    </span>

                    <strong>
                        <?= $totalProducts ?>
                    </strong>

                </div>

            </div>


        </div>



        <!-- =====================================================
         CATEGORY CARD
    ===================================================== -->

        <div class="content-card">


            <!-- HEADER -->

            <div class="card-header-custom">


                <div class="header-left">

                    <h3>
                        Product Categories
                    </h3>

                    <p>
                        Manage the categories used by your products.
                    </p>

                </div>


                <a href="category-add.php" class="btn btn-primary-custom">

                    <i class="fa-solid fa-plus"></i>

                    Add Category

                </a>


            </div>



            <!-- =================================================
             SEARCH TOOLBAR
        ================================================= -->

            <div class="toolbar">


                <form method="GET" action="categories.php" class="search-form">


                    <div class="search-box">

                        <i class="fa-solid
                               fa-magnifying-glass"></i>


                        <input type="text" name="search" placeholder="Search categories..." value="<?= htmlspecialchars(
                                                                                                        $search
                                                                                                    ) ?>">

                    </div>


                    <button type="submit" class="search-btn">

                        Search

                    </button>


                    <?php if ($search !== ''): ?>

                    <a href="categories.php" class="clear-search">

                        <i class="fa-solid
                                   fa-xmark"></i>

                    </a>

                    <?php endif; ?>


                </form>


                <?php if ($search !== ''): ?>

                <div style="
                        font-size:11px;
                        color:#8493a3;
                    ">

                    Search results for:

                    <strong>
                        <?= htmlspecialchars($search) ?>
                    </strong>

                </div>

                <?php endif; ?>


            </div>



            <!-- =================================================
             TABLE
        ================================================= -->

            <?php if (!empty($categories)): ?>


            <div class="table-wrap">


                <table>


                    <thead>

                        <tr>

                            <th>
                                Category
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


                        <?php foreach (
                                $categories
                                as $category
                            ): ?>


                        <tr>


                            <!-- CATEGORY -->

                            <td>


                                <div class="category-name">


                                    <div class="category-icon">

                                        <i class="fa-solid
                                                       fa-layer-group"></i>

                                    </div>


                                    <div>

                                        <strong>

                                            <?= htmlspecialchars(
                                                        $category['name']
                                                    ) ?>

                                        </strong>


                                        <span>

                                            ID #<?= (int)$category['id'] ?>

                                        </span>

                                    </div>


                                </div>


                            </td>



                            <!-- SLUG -->

                            <td>

                                <span class="slug">

                                    <?= htmlspecialchars(
                                                $category['slug']
                                            ) ?>

                                </span>

                            </td>



                            <!-- DESCRIPTION -->

                            <td>

                                <div class="description">

                                    <?php

                                            $desc =
                                                trim(
                                                    $category['description'] ?? ''
                                                );

                                            if ($desc === ''):

                                            ?>

                                    <span style="
                                                    color:#aab4bf;
                                                    font-style:italic;
                                                ">

                                        No description

                                    </span>

                                    <?php else: ?>

                                    <?= htmlspecialchars(
                                                    strlen($desc) > 100
                                                        ? substr(
                                                            $desc,
                                                            0,
                                                            100
                                                        ) . '...'
                                                        : $desc
                                                ) ?>

                                    <?php endif; ?>

                                </div>

                            </td>



                            <!-- PRODUCTS -->

                            <td>

                                <span class="product-count">

                                    <i class="fa-solid
                                                   fa-box"></i>

                                    <?= (int)$category['product_count'] ?>

                                </span>

                            </td>



                            <!-- STATUS -->

                            <td>


                                <?php if (
                                            (int)$category['status']
                                            === 1
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
                                                    $category['created_at']
                                                )
                                            ) ?>

                                </span>

                            </td>



                            <!-- ACTIONS -->

                            <td>


                                <div class="actions">


                                    <!-- VIEW PRODUCTS -->

                                    <a href="products.php?category=<?= (int)$category['id'] ?>" class="
                                                action-btn
                                                action-view
                                            " title="View Products">

                                        <i class="fa-solid
                                                       fa-box"></i>

                                    </a>



                                    <!-- EDIT -->

                                    <a href="category-edit.php?id=<?= (int)$category['id'] ?>" class="
                                                action-btn
                                                action-edit
                                            " title="Edit Category">

                                        <i class="fa-solid
                                                       fa-pen"></i>

                                    </a>



                                    <!-- DELETE -->

                                    <a href="category-delete.php?id=<?= (int)$category['id'] ?>" class="
                                                action-btn
                                                action-delete
                                            " title="Delete Category">

                                        <i class="fa-solid
                                                       fa-trash"></i>

                                    </a>


                                </div>


                            </td>


                        </tr>


                        <?php endforeach; ?>


                    </tbody>


                </table>


            </div>


            <div class="table-footer">

                Showing

                <strong>
                    <?= count($categories) ?>
                </strong>

                category(s)

                <?php if ($search !== ''): ?>

                matching your search.

                <?php else: ?>

                in your catalogue.

                <?php endif; ?>

            </div>


            <?php else: ?>


            <!-- =================================================
                 EMPTY STATE
            ================================================= -->

            <div class="empty-state">


                <div class="empty-icon">

                    <i class="fa-solid
                               fa-layer-group"></i>

                </div>


                <?php if ($search !== ''): ?>


                <h4>
                    No Categories Found
                </h4>


                <p>

                    No category matches
                    "<strong><?= htmlspecialchars($search) ?></strong>".

                </p>


                <a href="categories.php" class="btn btn-primary-custom">

                    <i class="fa-solid
                                   fa-rotate-left"></i>

                    Clear Search

                </a>


                <?php else: ?>


                <h4>
                    No Categories Yet
                </h4>


                <p>

                    Start organizing your CORE LTD
                    products by creating your first category.

                </p>


                <a href="category-add.php" class="btn btn-primary-custom">

                    <i class="fa-solid fa-plus"></i>

                    Create First Category

                </a>


                <?php endif; ?>


            </div>


            <?php endif; ?>


        </div>


    </main>


</body>

</html>