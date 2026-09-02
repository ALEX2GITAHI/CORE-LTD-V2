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
| DASHBOARD STATISTICS
|--------------------------------------------------------------------------
*/

$totalProducts = 0;
$activeProducts = 0;
$lowStockProducts = 0;
$outOfStockProducts = 0;
$totalCategories = 0;
$totalBrands = 0;
$totalOrders = 0;
$pendingOrders = 0;


/*
|--------------------------------------------------------------------------
| PRODUCTS
|--------------------------------------------------------------------------
*/

$result = $conn->query("
    SELECT COUNT(*) AS total
    FROM products
");

if ($result) {
    $totalProducts = (int)$result->fetch_assoc()['total'];
}


$result = $conn->query("
    SELECT COUNT(*) AS total
    FROM products
    WHERE status = 1
");

if ($result) {
    $activeProducts = (int)$result->fetch_assoc()['total'];
}


$result = $conn->query("
    SELECT COUNT(*) AS total
    FROM products
    WHERE stock_quantity > 0
    AND stock_quantity <= low_stock_level
");

if ($result) {
    $lowStockProducts = (int)$result->fetch_assoc()['total'];
}


$result = $conn->query("
    SELECT COUNT(*) AS total
    FROM products
    WHERE stock_quantity <= 0
");

if ($result) {
    $outOfStockProducts = (int)$result->fetch_assoc()['total'];
}


/*
|--------------------------------------------------------------------------
| CATEGORIES
|--------------------------------------------------------------------------
*/

$result = $conn->query("
    SELECT COUNT(*) AS total
    FROM categories
");

if ($result) {
    $totalCategories = (int)$result->fetch_assoc()['total'];
}


/*
|--------------------------------------------------------------------------
| BRANDS
|--------------------------------------------------------------------------
*/

$result = $conn->query("
    SELECT COUNT(*) AS total
    FROM brands
");

if ($result) {
    $totalBrands = (int)$result->fetch_assoc()['total'];
}


/*
|--------------------------------------------------------------------------
| ORDERS
|--------------------------------------------------------------------------
*/

$result = $conn->query("
    SELECT COUNT(*) AS total
    FROM orders
");

if ($result) {
    $totalOrders = (int)$result->fetch_assoc()['total'];
}


/*
|--------------------------------------------------------------------------
| PENDING ORDERS
|--------------------------------------------------------------------------
*/

$result = $conn->query("
    SELECT COUNT(*) AS total
    FROM orders
    WHERE order_status = 'pending'
");

if ($result) {
    $pendingOrders = (int)$result->fetch_assoc()['total'];
}


/*
|--------------------------------------------------------------------------
| RECENT PRODUCTS
|--------------------------------------------------------------------------
*/

$recentProducts = [];

$productQuery = $conn->query("
    SELECT
        p.id,
        p.name,
        p.price,
        p.stock_quantity,
        p.main_image,
        p.status,
        c.name AS category_name,
        b.name AS brand_name
    FROM products p

    LEFT JOIN categories c
        ON p.category_id = c.id

    LEFT JOIN brands b
        ON p.brand_id = b.id

    ORDER BY p.id DESC

    LIMIT 6
");

if ($productQuery) {

    while ($row = $productQuery->fetch_assoc()) {
        $recentProducts[] = $row;
    }
}


/*
|--------------------------------------------------------------------------
| RECENT ORDERS
|--------------------------------------------------------------------------
*/

$recentOrders = [];

$orderQuery = $conn->query("
    SELECT
        id,
        order_number,
        customer_name,
        total_amount,
        order_status,
        payment_status,
        created_at
    FROM orders
    ORDER BY id DESC
    LIMIT 6
");

if ($orderQuery) {

    while ($row = $orderQuery->fetch_assoc()) {
        $recentOrders[] = $row;
    }
}


/*
|--------------------------------------------------------------------------
| ADMIN NAME
|--------------------------------------------------------------------------
*/

$adminName =
    $_SESSION['admin_name']
    ?? 'Administrator';

$adminInitial =
    strtoupper(
        substr(
            trim($adminName),
            0,
            1
        )
    );

$unreadNotifications = 0;
$notificationCountResult = $conn->query("SELECT COUNT(*) AS total FROM admin_notifications WHERE is_read = 0");
if ($notificationCountResult) {
    $unreadNotifications = (int)$notificationCountResult->fetch_assoc()['total'];
}


?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        CORE LTD | Admin Dashboard
    </title>


    <style>
    * {
        box-sizing: border-box;
    }


    body {

        margin: 0;

        font-family:
            Arial,
            Helvetica,
            sans-serif;

        background: #f4f7fb;

        color: #172033;
    }


    a {
        text-decoration: none;
    }


    /*
        |--------------------------------------------------------------------------
        | SIDEBAR
        |--------------------------------------------------------------------------
        */

    .sidebar {

        position: fixed;

        top: 0;
        left: 0;

        width: 250px;

        height: 100vh;

        background:
            linear-gradient(180deg,
                #082b5c 0%,
                #0b4d96 52%,
                #06376e 100%);

        color: #fff;

        padding: 22px 14px;

        overflow-y: auto;

        box-shadow:
            4px 0 18px rgba(8, 43, 92, 0.15);

        z-index: 1000;
    }


    /*
        |--------------------------------------------------------------------------
        | LOGO
        |--------------------------------------------------------------------------
        */

    .logo {

        padding:
            10px 12px 25px;

        text-align: center;

        border-bottom:
            1px solid rgba(255, 255, 255, 0.12);
    }


    .logo-main {

        font-size: 27px;

        font-weight: 800;

        letter-spacing: 1.5px;
    }


    .logo-sub {

        margin-top: 5px;

        font-size: 10px;

        color:
            rgba(255, 255, 255, 0.65);

        letter-spacing: 2px;
    }


    /*
        |--------------------------------------------------------------------------
        | MENU
        |--------------------------------------------------------------------------
        */

    .menu-title {

        margin:
            25px 12px 9px;

        font-size: 10px;

        text-transform: uppercase;

        letter-spacing: 1.4px;

        color:
            rgba(255, 255, 255, 0.50);
    }


    .menu a {

        display: flex;

        align-items: center;

        gap: 12px;

        padding:
            12px 13px;

        margin-bottom: 4px;

        border-radius: 8px;

        color:
            rgba(255, 255, 255, 0.78);

        font-size: 13px;

        transition:
            0.2s ease;
    }


    .menu a:hover {

        background:
            rgba(255, 255, 255, 0.11);

        color: #fff;

        transform:
            translateX(2px);
    }


    .menu a.active {

        background: #fff;

        color: #0a4d98;

        font-weight: 700;

        box-shadow:
            0 5px 15px rgba(0, 0, 0, 0.12);
    }


    .menu-icon {

        width: 24px;

        height: 24px;

        display: flex;

        align-items: center;

        justify-content: center;

        border-radius: 6px;

        font-size: 15px;
    }


    .menu a.active .menu-icon {

        background: #eaf3ff;
    }


    /*
        |--------------------------------------------------------------------------
        | MAIN
        |--------------------------------------------------------------------------
        */

    .main {

        margin-left: 250px;

        min-height: 100vh;
    }


    /*
        |--------------------------------------------------------------------------
        | TOPBAR
        |--------------------------------------------------------------------------
        */

    .topbar {

        height: 72px;

        background: #fff;

        border-bottom:
            1px solid #e6ebf2;

        padding:
            0 30px;

        display: flex;

        align-items: center;

        justify-content: space-between;

        position: sticky;

        top: 0;

        z-index: 900;
    }


    .top-title h1 {

        margin: 0;

        font-size: 20px;

        color: #152238;
    }


    .top-title p {

        margin:
            4px 0 0;

        color: #8491a5;

        font-size: 12px;
    }


    .admin-profile {

        display: flex;

        align-items: center;

        gap: 11px;
    }


    .avatar {

        width: 40px;

        height: 40px;

        border-radius: 50%;

        background:
            linear-gradient(135deg,
                #087cf0,
                #063b83);

        color: #fff;

        display: flex;

        align-items: center;

        justify-content: center;

        font-weight: 800;

        box-shadow:
            0 4px 12px rgba(0, 91, 190, 0.25);
    }


    .profile-name strong {

        display: block;

        font-size: 12px;

        color: #1c2940;
    }


    .profile-name span {

        display: block;

        margin-top: 2px;

        color: #8a96a8;

        font-size: 10px;
    }


    /*
        |--------------------------------------------------------------------------
        | CONTENT
        |--------------------------------------------------------------------------
        */

    .content {

        padding: 30px;
    }


    /*
        |--------------------------------------------------------------------------
        | WELCOME BANNER
        |--------------------------------------------------------------------------
        */

    .welcome {

        position: relative;

        overflow: hidden;

        background:
            linear-gradient(120deg,
                #0755a5,
                #087fe8);

        color: #fff;

        border-radius: 14px;

        padding:
            27px 30px;

        margin-bottom: 25px;

        box-shadow:
            0 10px 28px rgba(5, 83, 163, 0.18);
    }


    .welcome:after {

        content: "";

        position: absolute;

        width: 220px;

        height: 220px;

        border-radius: 50%;

        background:
            rgba(255, 255, 255, 0.08);

        right: -60px;

        top: -90px;
    }


    .welcome:before {

        content: "";

        position: absolute;

        width: 130px;

        height: 130px;

        border-radius: 50%;

        background:
            rgba(255, 255, 255, 0.06);

        right: 110px;

        bottom: -75px;
    }


    .welcome-content {

        position: relative;

        z-index: 2;
    }


    .welcome h2 {

        margin: 0;

        font-size: 25px;
    }


    .welcome p {

        margin:
            8px 0 0;

        color:
            rgba(255, 255, 255, 0.82);

        font-size: 13px;
    }


    /*
        |--------------------------------------------------------------------------
        | STAT CARDS
        |--------------------------------------------------------------------------
        */

    .stats {

        display: grid;

        grid-template-columns:
            repeat(4, 1fr);

        gap: 17px;

        margin-bottom: 25px;
    }


    .stat-card {

        position: relative;

        overflow: hidden;

        background: #fff;

        border-radius: 12px;

        padding: 20px;

        border:
            1px solid #e8edf4;

        box-shadow:
            0 4px 16px rgba(25, 60, 100, 0.05);
    }


    .stat-card:after {

        content: "";

        position: absolute;

        right: -30px;

        top: -35px;

        width: 90px;

        height: 90px;

        border-radius: 50%;

        opacity: 0.10;
    }


    .stat-top {

        display: flex;

        align-items: center;

        justify-content: space-between;

        margin-bottom: 16px;
    }


    .stat-icon {

        width: 43px;

        height: 43px;

        border-radius: 10px;

        display: flex;

        align-items: center;

        justify-content: center;

        font-size: 19px;

        font-weight: 700;
    }


    .stat-label {

        color: #7e8a9d;

        font-size: 11px;

        font-weight: 700;

        text-transform: uppercase;

        letter-spacing: .5px;
    }


    .stat-value {

        font-size: 27px;

        font-weight: 800;

        color: #162338;
    }


    .stat-blue .stat-icon {

        background: #e8f3ff;

        color: #0875df;
    }


    .stat-blue:after {
        background: #0875df;
    }


    .stat-green .stat-icon {

        background: #e9f8f0;

        color: #169b57;
    }


    .stat-green:after {
        background: #169b57;
    }


    .stat-orange .stat-icon {

        background: #fff4df;

        color: #e38a08;
    }


    .stat-orange:after {
        background: #e38a08;
    }


    .stat-red .stat-icon {

        background: #ffebeb;

        color: #d93030;
    }


    .stat-red:after {
        background: #d93030;
    }


    .stat-purple .stat-icon {

        background: #f1eaff;

        color: #7648d8;
    }


    .stat-cyan .stat-icon {

        background: #e5f8fb;

        color: #008da4;
    }


    /*
        |--------------------------------------------------------------------------
        | SECONDARY STATS
        |--------------------------------------------------------------------------
        */

    .secondary-stats {

        display: grid;

        grid-template-columns:
            repeat(4, 1fr);

        gap: 17px;

        margin-bottom: 25px;
    }


    .small-stat {

        background: #fff;

        border:
            1px solid #e8edf4;

        border-radius: 11px;

        padding: 17px;

        display: flex;

        align-items: center;

        gap: 13px;
    }


    .small-icon {

        width: 42px;

        height: 42px;

        border-radius: 10px;

        display: flex;

        align-items: center;

        justify-content: center;

        font-size: 17px;
    }


    .small-info span {

        display: block;

        color: #8894a7;

        font-size: 11px;

        margin-bottom: 4px;
    }


    .small-info strong {

        font-size: 20px;

        color: #1a2940;
    }


    /*
        |--------------------------------------------------------------------------
        | DASHBOARD GRID
        |--------------------------------------------------------------------------
        */

    .dashboard-grid {

        display: grid;

        grid-template-columns:
            1.45fr 1fr;

        gap: 20px;

        margin-bottom: 25px;
    }


    .panel {

        background: #fff;

        border:
            1px solid #e7edf4;

        border-radius: 12px;

        overflow: hidden;

        box-shadow:
            0 4px 15px rgba(25, 60, 100, 0.04);
    }


    .panel-header {

        padding:
            17px 20px;

        border-bottom:
            1px solid #edf0f4;

        display: flex;

        align-items: center;

        justify-content: space-between;
    }


    .panel-header h3 {

        margin: 0;

        font-size: 14px;

        color: #1c2a40;
    }


    .panel-header a {

        font-size: 11px;

        color: #0875df;

        font-weight: 700;
    }


    /*
        |--------------------------------------------------------------------------
        | PRODUCTS
        |--------------------------------------------------------------------------
        */

    .product-list {

        padding: 5px 20px;
    }


    .product-row {

        display: flex;

        align-items: center;

        gap: 12px;

        padding: 13px 0;

        border-bottom:
            1px solid #f0f2f5;
    }


    .product-row:last-child {

        border-bottom: none;
    }


    .product-image {

        width: 48px;

        height: 48px;

        border-radius: 8px;

        border:
            1px solid #e8edf3;

        background: #f8fafc;

        object-fit: contain;

        flex-shrink: 0;
    }


    .no-image {

        width: 48px;

        height: 48px;

        border-radius: 8px;

        background: #edf3f9;

        display: flex;

        align-items: center;

        justify-content: center;

        color: #8b9aab;

        font-size: 9px;

        flex-shrink: 0;
    }


    .product-details {

        flex: 1;

        min-width: 0;
    }


    .product-name {

        font-size: 12px;

        font-weight: 700;

        color: #24334a;

        white-space: nowrap;

        overflow: hidden;

        text-overflow: ellipsis;
    }


    .product-meta {

        color: #8a96a7;

        font-size: 10px;

        margin-top: 4px;
    }


    .product-price {

        text-align: right;

        font-size: 12px;

        font-weight: 800;

        color: #0869c8;

        white-space: nowrap;
    }


    /*
        |--------------------------------------------------------------------------
        | ORDERS
        |--------------------------------------------------------------------------
        */

    .order-list {

        padding: 5px 20px;
    }


    .order-row {

        display: flex;

        align-items: center;

        gap: 12px;

        padding: 13px 0;

        border-bottom:
            1px solid #f0f2f5;
    }


    .order-row:last-child {

        border-bottom: none;
    }


    .order-icon {

        width: 42px;

        height: 42px;

        border-radius: 9px;

        background: #eaf3ff;

        color: #0875df;

        display: flex;

        align-items: center;

        justify-content: center;

        font-weight: 800;

        font-size: 15px;

        flex-shrink: 0;
    }


    .order-info {

        flex: 1;

        min-width: 0;
    }


    .order-number {

        font-size: 11px;

        font-weight: 800;

        color: #23334a;
    }


    .customer-name {

        font-size: 10px;

        color: #8a96a7;

        margin-top: 4px;

        white-space: nowrap;

        overflow: hidden;

        text-overflow: ellipsis;
    }


    .order-right {

        text-align: right;
    }


    .order-amount {

        font-size: 11px;

        font-weight: 800;

        color: #17263d;
    }


    .order-status {

        display: inline-block;

        margin-top: 4px;

        padding: 4px 7px;

        border-radius: 20px;

        font-size: 8px;

        font-weight: 800;

        text-transform: uppercase;
    }


    .status-pending {

        background: #fff3dc;

        color: #bb7200;
    }


    .status-completed {

        background: #e6f7ed;

        color: #19864a;
    }


    .status-cancelled {

        background: #ffebeb;

        color: #c72b2b;
    }


    .status-processing {

        background: #e7f1ff;

        color: #1767b1;
    }


    .status-default {

        background: #f0f2f5;

        color: #687586;
    }


    /*
        |--------------------------------------------------------------------------
        | QUICK ACTIONS
        |--------------------------------------------------------------------------
        */

    .quick-actions {

        display: grid;

        grid-template-columns:
            repeat(4, 1fr);

        gap: 14px;

        margin-top: 20px;
    }


    .quick-action {

        background: #fff;

        border:
            1px solid #e7edf4;

        border-radius: 10px;

        padding: 18px;

        display: flex;

        align-items: center;

        gap: 12px;

        color: #26354b;

        transition: .2s ease;
    }


    .quick-action:hover {

        border-color: #a9cef3;

        transform:
            translateY(-2px);

        box-shadow:
            0 8px 18px rgba(10, 90, 170, 0.08);
    }


    .quick-icon {

        width: 40px;

        height: 40px;

        background: #eaf3ff;

        color: #0875df;

        border-radius: 9px;

        display: flex;

        align-items: center;

        justify-content: center;

        font-size: 17px;
    }


    .quick-action strong {

        display: block;

        font-size: 11px;
    }


    .quick-action span {

        display: block;

        color: #8a96a7;

        font-size: 9px;

        margin-top: 3px;
    }


    /*
        |--------------------------------------------------------------------------
        | EMPTY
        |--------------------------------------------------------------------------
        */

    .empty {

        padding: 35px 20px;

        text-align: center;

        color: #96a1b0;

        font-size: 12px;
    }


    /*
        |--------------------------------------------------------------------------
        | NOTIFICATIONS
        |--------------------------------------------------------------------------
        */
    .topbar-actions {
        display: flex;
        align-items: center;
        gap: 18px;
    }

    .notification-wrap {
        position: relative;
    }

    .notification-btn {
        position: relative;
        width: 42px;
        height: 42px;
        border: 1px solid #e5ebf2;
        border-radius: 10px;
        background: #fff;
        color: #31516f;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 19px;
        cursor: pointer;
    }

    .notification-btn:hover,
    .notification-btn.open {
        background: #eaf3ff;
        border-color: #bcd9f6;
        color: #0875df;
    }

    .notification-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        min-width: 18px;
        height: 18px;
        padding: 0 5px;
        border-radius: 20px;
        background: #e53935;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 9px;
        font-weight: 800;
        border: 2px solid #fff;
    }

    .notification-badge.hidden {
        display: none;
    }

    .notification-dropdown {
        position: absolute;
        top: 51px;
        right: 0;
        width: 360px;
        background: #fff;
        border: 1px solid #e2e9f1;
        border-radius: 12px;
        box-shadow: 0 14px 35px rgba(20, 54, 90, .16);
        overflow: hidden;
        display: none;
        z-index: 1200;
    }

    .notification-dropdown.show {
        display: block;
    }

    .notification-head {
        padding: 15px 16px;
        border-bottom: 1px solid #edf1f5;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .notification-head strong {
        color: #1c2a40;
        font-size: 12px;
    }

    .notification-head a,
    .notification-footer a {
        color: #0875df;
        font-size: 9px;
        font-weight: 800;
    }

    .notification-list {
        max-height: 350px;
        overflow-y: auto;
    }

    .notification-item {
        display: flex;
        gap: 11px;
        padding: 13px 16px;
        border-bottom: 1px solid #f0f3f6;
        color: inherit;
    }

    .notification-item:hover {
        background: #f7fbff;
    }

    .notification-item.unread {
        background: #f2f8ff;
    }

    .notification-dot {
        flex: 0 0 34px;
        width: 34px;
        height: 34px;
        border-radius: 9px;
        background: #eaf3ff;
        color: #0875df;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 900;
        font-size: 13px;
    }

    .notification-content {
        min-width: 0;
        flex: 1;
    }

    .notification-title {
        color: #24344b;
        font-size: 10px;
        font-weight: 800;
    }

    .notification-message {
        margin-top: 4px;
        color: #7d8da0;
        font-size: 9px;
        line-height: 1.45;
    }

    .notification-time {
        margin-top: 5px;
        color: #a0adba;
        font-size: 8px;
    }

    .notification-empty {
        padding: 30px 18px;
        text-align: center;
        color: #8c9bab;
        font-size: 10px;
    }

    .notification-footer {
        border-top: 1px solid #edf1f5;
        padding: 11px 16px;
        text-align: center;
    }

    /*
        |--------------------------------------------------------------------------
        | MOBILE
        |--------------------------------------------------------------------------
        */

    @media (max-width: 1150px) {

        .stats {

            grid-template-columns:
                repeat(2, 1fr);
        }


        .secondary-stats {

            grid-template-columns:
                repeat(2, 1fr);
        }


        .dashboard-grid {

            grid-template-columns: 1fr;
        }


        .quick-actions {

            grid-template-columns:
                repeat(2, 1fr);
        }

    }


    @media (max-width: 800px) {

        .sidebar {

            width: 72px;

            padding:
                18px 8px;
        }


        .logo-main {

            font-size: 14px;
        }


        .logo-sub,
        .menu-title,
        .menu a span:last-child {

            display: none;
        }


        .menu a {

            justify-content: center;
        }


        .main {

            margin-left: 72px;
        }


        .topbar {

            padding:
                0 18px;
        }


        .content {

            padding: 18px;
        }


        .profile-name {

            display: none;
        }

    }


    @media (max-width: 560px) {

        .stats,
        .secondary-stats {

            grid-template-columns: 1fr;
        }


        .quick-actions {

            grid-template-columns: 1fr;
        }


        .welcome h2 {

            font-size: 20px;
        }


        .top-title p {

            display: none;
        }

    }
    </style>

</head>


<body>


    <!--
|--------------------------------------------------------------------------
| SIDEBAR
|--------------------------------------------------------------------------
-->

    <aside class="sidebar">


        <div class="logo">

            <div class="logo-main">
                CORE LTD
            </div>

            <div class="logo-sub">
                ADMINISTRATION
            </div>

        </div>


        <nav class="menu">


            <div class="menu-title">
                Main
            </div>


            <a href="index.php" class="active">

                <span class="menu-icon">
                    📊
                </span>

                <span>
                    Dashboard
                </span>

            </a>


            <a href="products.php">

                <span class="menu-icon">
                    📦
                </span>

                <span>
                    Products
                </span>

            </a>


            <a href="product-add.php">

                <span class="menu-icon">
                    ➕
                </span>

                <span>
                    Add Product
                </span>

            </a>


            <div class="menu-title">
                Catalogue
            </div>


            <a href="categories.php">

                <span class="menu-icon">
                    📁
                </span>

                <span>
                    Categories
                </span>

            </a>


            <a href="brands.php">

                <span class="menu-icon">
                    🏷️
                </span>

                <span>
                    Brands
                </span>

            </a>


            <a href="tags.php">

                <span class="menu-icon">
                    #️⃣
                </span>

                <span>
                    Tags
                </span>

            </a>


            <div class="menu-title">
                Sales
            </div>


            <a href="orders.php">

                <span class="menu-icon">
                    🛒
                </span>

                <span>
                    Orders
                </span>

            </a>


            <a href="reviews.php">

                <span class="menu-icon">
                    ⭐
                </span>

                <span>
                    Reviews
                </span>

            </a>


            <a href="events.php">

                <span class="menu-icon">
                    📅
                </span>

                <span>
                    Events
                </span>

            </a>


            <div class="menu-title">
                System
            </div>


            <a href="../index.php">

                <span class="menu-icon">
                    ↗
                </span>

                <span>
                    View Website
                </span>

            </a>


            <a href="logout.php">

                <span class="menu-icon">
                    ⇥
                </span>

                <span>
                    Logout
                </span>

            </a>


        </nav>


    </aside>



    <!--
|--------------------------------------------------------------------------
| MAIN
|--------------------------------------------------------------------------
-->

    <main class="main">


        <!-- TOPBAR -->

        <header class="topbar">


            <div class="top-title">

                <h1>
                    Dashboard
                </h1>

                <p>
                    CORE LTD Management System
                </p>

            </div>


            <div class="topbar-actions">

                <div class="notification-wrap">
                    <button type="button" class="notification-btn" id="notificationBtn" aria-label="Notifications"
                        aria-expanded="false">
                        🔔
                        <span class="notification-badge <?= $unreadNotifications > 0 ? '' : 'hidden' ?>"
                            id="notificationBadge">
                            <?= $unreadNotifications > 99 ? '99+' : $unreadNotifications ?>
                        </span>
                    </button>

                    <div class="notification-dropdown" id="notificationDropdown">
                        <div class="notification-head">
                            <strong>Notifications</strong>
                            <a href="notifications.php">View All</a>
                        </div>
                        <div class="notification-list" id="notificationList">
                            <div class="notification-empty">Loading notifications...</div>
                        </div>
                        <div class="notification-footer">
                            <a href="notifications.php">View All Notifications →</a>
                        </div>
                    </div>
                </div>

                <div class="admin-profile">


                    <div class="avatar">

                        <?= htmlspecialchars($adminInitial) ?>

                    </div>


                    <div class="profile-name">

                        <strong>

                            <?= htmlspecialchars($adminName) ?>

                        </strong>

                        <span>
                            Administrator
                        </span>

                    </div>


                </div>

            </div>


        </header>



        <!-- CONTENT -->

        <div class="content">


            <!-- WELCOME -->

            <section class="welcome">


                <div class="welcome-content">

                    <h2>
                        Welcome back, <?= htmlspecialchars($adminName) ?> 👋
                    </h2>

                    <p>
                        Here's what's happening with your CORE LTD store today.
                    </p>

                </div>


            </section>



            <!-- MAIN STATS -->

            <section class="stats">


                <div class="stat-card stat-blue">

                    <div class="stat-top">

                        <span class="stat-label">
                            Total Products
                        </span>

                        <div class="stat-icon">
                            📦
                        </div>

                    </div>

                    <div class="stat-value">
                        <?= number_format($totalProducts) ?>
                    </div>

                </div>



                <div class="stat-card stat-green">

                    <div class="stat-top">

                        <span class="stat-label">
                            Active Products
                        </span>

                        <div class="stat-icon">
                            ✓
                        </div>

                    </div>

                    <div class="stat-value">
                        <?= number_format($activeProducts) ?>
                    </div>

                </div>



                <div class="stat-card stat-orange">

                    <div class="stat-top">

                        <span class="stat-label">
                            Low Stock
                        </span>

                        <div class="stat-icon">
                            !
                        </div>

                    </div>

                    <div class="stat-value">
                        <?= number_format($lowStockProducts) ?>
                    </div>

                </div>



                <div class="stat-card stat-red">

                    <div class="stat-top">

                        <span class="stat-label">
                            Out of Stock
                        </span>

                        <div class="stat-icon">
                            ×
                        </div>

                    </div>

                    <div class="stat-value">
                        <?= number_format($outOfStockProducts) ?>
                    </div>

                </div>


            </section>



            <!-- SECONDARY STATS -->

            <section class="secondary-stats">


                <div class="small-stat">

                    <div class="small-icon" style="
                        background:#f0eaff;
                        color:#7044d4;
                    ">
                        📁
                    </div>

                    <div class="small-info">

                        <span>
                            Categories
                        </span>

                        <strong>
                            <?= number_format($totalCategories) ?>
                        </strong>

                    </div>

                </div>



                <div class="small-stat">

                    <div class="small-icon" style="
                        background:#e4f8fb;
                        color:#008ca3;
                    ">
                        🏷️
                    </div>

                    <div class="small-info">

                        <span>
                            Brands
                        </span>

                        <strong>
                            <?= number_format($totalBrands) ?>
                        </strong>

                    </div>

                </div>



                <div class="small-stat">

                    <div class="small-icon" style="
                        background:#e7f1ff;
                        color:#0875df;
                    ">
                        🛒
                    </div>

                    <div class="small-info">

                        <span>
                            Total Orders
                        </span>

                        <strong>
                            <?= number_format($totalOrders) ?>
                        </strong>

                    </div>

                </div>



                <div class="small-stat">

                    <div class="small-icon" style="
                        background:#fff3dc;
                        color:#c47a00;
                    ">
                        ⏳
                    </div>

                    <div class="small-info">

                        <span>
                            Pending Orders
                        </span>

                        <strong>
                            <?= number_format($pendingOrders) ?>
                        </strong>

                    </div>

                </div>


            </section>



            <!-- PRODUCT + ORDER PANELS -->

            <section class="dashboard-grid">


                <!-- RECENT PRODUCTS -->

                <div class="panel">


                    <div class="panel-header">

                        <h3>
                            Recently Added Products
                        </h3>

                        <a href="products.php">
                            View All →
                        </a>

                    </div>


                    <div class="product-list">


                        <?php if (!empty($recentProducts)): ?>


                        <?php foreach ($recentProducts as $product): ?>


                        <div class="product-row">


                            <?php

                                    $imageName =
                                        trim(
                                            $product['main_image'] ?? ''
                                        );

                                    /*
                                |--------------------------------------------------------------------------
                                | IMPORTANT
                                |--------------------------------------------------------------------------
                                | Images are stored outside the admin folder.
                                | Therefore the browser path is:
                                |
                                | ../uploads/products/
                                |
                                */

                                    $imageUrl = '';

                                    if ($imageName !== '') {

                                        $imageUrl =
                                            '../uploads/products/' .
                                            $imageName;
                                    }

                                    ?>


                            <?php if ($imageUrl !== ''): ?>


                            <img src="<?= htmlspecialchars($imageUrl) ?>" class="product-image"
                                alt="<?= htmlspecialchars($product['name']) ?>" onerror="
                                            this.style.display='none';
                                            this.nextElementSibling.style.display='flex';
                                        ">


                            <div class="no-image" style="display:none;">
                                IMG
                            </div>


                            <?php else: ?>


                            <div class="no-image">
                                IMG
                            </div>


                            <?php endif; ?>


                            <div class="product-details">


                                <div class="product-name">

                                    <?= htmlspecialchars(
                                                $product['name']
                                            ) ?>

                                </div>


                                <div class="product-meta">

                                    <?= htmlspecialchars(
                                                $product['brand_name']
                                                    ?? 'No Brand'
                                            ) ?>

                                    &nbsp; • &nbsp;

                                    <?= htmlspecialchars(
                                                $product['category_name']
                                                    ?? 'Uncategorized'
                                            ) ?>

                                    &nbsp; • &nbsp;

                                    Stock:
                                    <?= (int)$product['stock_quantity'] ?>

                                </div>


                            </div>


                            <div class="product-price">

                                Ksh.
                                <?= number_format(
                                            (float)$product['price'],
                                            0
                                        ) ?>

                            </div>


                        </div>


                        <?php endforeach; ?>


                        <?php else: ?>


                        <div class="empty">

                            No products have been added yet.

                        </div>


                        <?php endif; ?>


                    </div>


                </div>



                <!-- RECENT ORDERS -->

                <div class="panel">


                    <div class="panel-header">

                        <h3>
                            Recent Orders
                        </h3>

                        <a href="orders.php">
                            View All →
                        </a>

                    </div>


                    <div class="order-list">


                        <?php if (!empty($recentOrders)): ?>


                        <?php foreach ($recentOrders as $order): ?>


                        <?php

                                $orderStatus =
                                    strtolower(
                                        trim(
                                            $order['order_status']
                                                ?? ''
                                        )
                                    );


                                $statusClass =
                                    'status-default';


                                if (
                                    $orderStatus === 'pending'
                                ) {

                                    $statusClass =
                                        'status-pending';
                                } elseif (
                                    $orderStatus === 'completed'
                                ) {

                                    $statusClass =
                                        'status-completed';
                                } elseif (
                                    $orderStatus === 'cancelled'
                                    ||
                                    $orderStatus === 'canceled'
                                ) {

                                    $statusClass =
                                        'status-cancelled';
                                } elseif (
                                    $orderStatus === 'processing'
                                ) {

                                    $statusClass =
                                        'status-processing';
                                }

                                ?>


                        <div class="order-row">


                            <div class="order-icon">
                                #
                            </div>


                            <div class="order-info">

                                <div class="order-number">

                                    <?= htmlspecialchars(
                                                $order['order_number']
                                            ) ?>

                                </div>


                                <div class="customer-name">

                                    <?= htmlspecialchars(
                                                $order['customer_name']
                                            ) ?>

                                </div>

                            </div>


                            <div class="order-right">

                                <div class="order-amount">

                                    Ksh.
                                    <?= number_format(
                                                (float)$order['total_amount'],
                                                0
                                            ) ?>

                                </div>


                                <span class="order-status <?= $statusClass ?>">

                                    <?= htmlspecialchars(
                                                $order['order_status']
                                            ) ?>

                                </span>

                            </div>


                        </div>


                        <?php endforeach; ?>


                        <?php else: ?>


                        <div class="empty">

                            No orders have been placed yet.

                        </div>


                        <?php endif; ?>


                    </div>


                </div>


            </section>



            <!-- QUICK ACTIONS -->

            <div class="panel">


                <div class="panel-header">

                    <h3>
                        Quick Actions
                    </h3>

                </div>


                <div style="
                    padding:20px;
                ">


                    <div class="quick-actions">


                        <a href="product-add.php" class="quick-action">

                            <div class="quick-icon">
                                ➕
                            </div>

                            <div>

                                <strong>
                                    Add Product
                                </strong>

                                <span>
                                    Add a new item
                                </span>

                            </div>

                        </a>



                        <a href="products.php" class="quick-action">

                            <div class="quick-icon">
                                📦
                            </div>

                            <div>

                                <strong>
                                    Manage Products
                                </strong>

                                <span>
                                    View your catalogue
                                </span>

                            </div>

                        </a>



                        <a href="orders.php" class="quick-action">

                            <div class="quick-icon">
                                🛒
                            </div>

                            <div>

                                <strong>
                                    View Orders
                                </strong>

                                <span>
                                    Manage customer orders
                                </span>

                            </div>

                        </a>



                        <a href="../index.php" class="quick-action">

                            <div class="quick-icon">
                                ↗
                            </div>

                            <div>

                                <strong>
                                    View Website
                                </strong>

                                <span>
                                    Open customer website
                                </span>

                            </div>

                        </a>


                    </div>


                </div>


            </div>


        </div>


    </main>


    <script>
    (function() {
        const btn = document.getElementById('notificationBtn');
        const dropdown = document.getElementById('notificationDropdown');
        const list = document.getElementById('notificationList');
        const badge = document.getElementById('notificationBadge');

        if (!btn || !dropdown || !list) return;

        function escapeHtml(value) {
            const div = document.createElement('div');
            div.textContent = value == null ? '' : String(value);
            return div.innerHTML;
        }

        function formatDate(value) {
            if (!value) return '';
            const d = new Date(value.replace(' ', 'T'));
            if (Number.isNaN(d.getTime())) return value;
            return d.toLocaleString('en-GB', {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        async function loadNotifications() {
            try {
                const response = await fetch('notification-api.php', {
                    method: 'GET',
                    credentials: 'same-origin',
                    cache: 'no-store'
                });

                if (!response.ok) throw new Error('Notification request failed');

                const data = await response.json();
                if (!data.success) throw new Error(data.message || 'Unable to load notifications');

                const unread = Number(data.unread || 0);
                if (badge) {
                    badge.textContent = unread > 99 ? '99+' : unread;
                    badge.classList.toggle('hidden', unread === 0);
                }

                if (!Array.isArray(data.notifications) || data.notifications.length === 0) {
                    list.innerHTML = '<div class="notification-empty">No notifications yet.</div>';
                    return;
                }

                list.innerHTML = data.notifications.map(n => {
                    const orderUrl = n.order_id ?
                        'order-view.php?id=' + encodeURIComponent(n.order_id) :
                        'notifications.php';

                    return `
                    <a href="${orderUrl}" class="notification-item ${Number(n.is_read) === 0 ? 'unread' : ''}">
                        <div class="notification-item-icon">🔔</div>
                        <div class="notification-item-body">
                            <strong>${escapeHtml(n.title || 'Notification')}</strong>
                            <span>${escapeHtml(n.message || '')}</span>
                            <small>${escapeHtml(formatDate(n.created_at))}</small>
                        </div>
                    </a>
                `;
                }).join('');
            } catch (error) {
                console.error('Notification error:', error);
                list.innerHTML =
                    '<div class="notification-empty">Unable to load notifications. Check notification-api.php.</div>';
            }
        }

        btn.addEventListener('click', function(event) {
            event.stopPropagation();
            const isOpen = dropdown.classList.toggle('show');
            btn.classList.toggle('open', isOpen);
            btn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');

            if (isOpen) loadNotifications();
        });

        dropdown.addEventListener('click', function(event) {
            event.stopPropagation();
        });

        document.addEventListener('click', function() {
            dropdown.classList.remove('show');
            btn.classList.remove('open');
            btn.setAttribute('aria-expanded', 'false');
        });

        loadNotifications();
        setInterval(loadNotifications, 30000);
    })();
    </script>
</body>

</html>