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
| GET CATEGORY ID
|--------------------------------------------------------------------------
*/

$categoryId = (int)($_GET['id'] ?? 0);

if ($categoryId <= 0) {
    header("Location: categories.php?error=invalid_category");
    exit;
}


/*
|--------------------------------------------------------------------------
| GET CATEGORY
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT
        id,
        name,
        slug
    FROM categories
    WHERE id = ?
    LIMIT 1
");

$stmt->bind_param("i", $categoryId);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {

    $stmt->close();

    header("Location: categories.php?error=category_not_found");
    exit;
}

$category = $result->fetch_assoc();

$stmt->close();


/*
|--------------------------------------------------------------------------
| COUNT PRODUCTS
|--------------------------------------------------------------------------
*/

$countStmt = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM products
    WHERE category_id = ?
");

$countStmt->bind_param(
    "i",
    $categoryId
);

$countStmt->execute();

$countResult = $countStmt->get_result();

$countRow = $countResult->fetch_assoc();

$productCount = (int)$countRow['total'];

$countStmt->close();


/*
|--------------------------------------------------------------------------
| DELETE CATEGORY
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /*
    |--------------------------------------------------------------------------
    | CHECK AGAIN BEFORE DELETE
    |--------------------------------------------------------------------------
    */

    $checkStmt = $conn->prepare("
        SELECT COUNT(*) AS total
        FROM products
        WHERE category_id = ?
    ");

    $checkStmt->bind_param(
        "i",
        $categoryId
    );

    $checkStmt->execute();

    $checkResult = $checkStmt->get_result();

    $checkRow = $checkResult->fetch_assoc();

    $currentProductCount = (int)$checkRow['total'];

    $checkStmt->close();


    /*
    |--------------------------------------------------------------------------
    | BLOCK IF PRODUCTS EXIST
    |--------------------------------------------------------------------------
    */

    if ($currentProductCount > 0) {

        header(
            "Location: categories.php?error=category_in_use"
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    $deleteStmt = $conn->prepare("
        DELETE FROM categories
        WHERE id = ?
        LIMIT 1
    ");

    $deleteStmt->bind_param(
        "i",
        $categoryId
    );


    if ($deleteStmt->execute()) {

        $deleteStmt->close();

        header(
            "Location: categories.php?success=category_deleted"
        );

        exit;
    } else {

        $deleteError =
            "Unable to delete category: " .
            $deleteStmt->error;

        $deleteStmt->close();
    }
}

?>


<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        Delete Category | CORE LTD Admin
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

        background:
            #f4f7fb;

        color:
            #26364a;
    }


    /* SIDEBAR */

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


    /* MAIN */

    .main {

        margin-left: 245px;

        min-height: 100vh;

        padding: 28px;
    }


    /* TOPBAR */

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


    /* DELETE CARD */

    .delete-card {

        max-width: 650px;

        margin:
            50px auto;

        background: #fff;

        border-radius: 16px;

        box-shadow:
            0 8px 30px rgba(25, 65, 100, .08);

        overflow: hidden;

        text-align: center;
    }


    .delete-header {

        padding:
            35px 30px 20px;
    }


    .warning-icon {

        width: 75px;
        height: 75px;

        margin:
            0 auto 20px;

        border-radius: 50%;

        background:
            #fff0f1;

        color:
            #dc3545;

        display: flex;

        align-items: center;
        justify-content: center;

        font-size: 30px;
    }


    .delete-header h2 {

        margin: 0;

        font-size: 23px;

        font-weight: 800;

        color:
            #17365d;
    }


    .delete-header p {

        margin:
            10px auto 0;

        max-width: 480px;

        color:
            #718092;

        font-size: 14px;

        line-height: 1.6;
    }


    /* CATEGORY DETAILS */

    .category-details {

        margin:
            10px 30px 25px;

        padding:
            18px;

        background:
            #f7faff;

        border:
            1px solid #e4edf7;

        border-radius: 10px;

        text-align: left;
    }


    .detail-row {

        display: flex;

        justify-content: space-between;

        padding:
            8px 0;

        border-bottom:
            1px solid #e9eff5;

        font-size: 13px;
    }


    .detail-row:last-child {

        border-bottom: none;
    }


    .detail-label {

        color:
            #8493a3;
    }


    .detail-value {

        font-weight:
            700;

        color:
            #17365d;
    }


    /* WARNING */

    .blocked-warning {

        margin:
            0 30px 25px;

        padding:
            16px;

        border-radius:
            10px;

        background:
            #fff8e6;

        border:
            1px solid #ffe3a3;

        color:
            #856404;

        text-align: left;

        font-size:
            13px;

        line-height:
            1.6;
    }


    .blocked-warning strong {

        display:
            block;

        margin-bottom:
            5px;

        color:
            #664d03;
    }


    .success-warning {

        margin:
            0 30px 25px;

        padding:
            16px;

        border-radius:
            10px;

        background:
            #eaf8f0;

        border:
            1px solid #b9e6ca;

        color:
            #146c43;

        text-align: left;

        font-size:
            13px;

        line-height:
            1.6;
    }


    /* ACTIONS */

    .actions {

        padding:
            20px 30px 30px;

        display:
            flex;

        justify-content:
            center;

        gap:
            10px;

        border-top:
            1px solid #edf1f5;
    }


    .btn {

        padding:
            11px 20px;

        border-radius:
            8px;

        font-size:
            13px;

        font-weight:
            700;

        text-decoration:
            none;

        border: none;

        display:
            inline-flex;

        align-items:
            center;

        justify-content:
            center;

        gap:
            7px;
    }


    .btn-back {

        background:
            #f1f3f5;

        color:
            #566574;
    }


    .btn-back:hover {

        background:
            #e2e6ea;

        color:
            #343a40;
    }


    .btn-delete {

        background:
            #dc3545;

        color:
            #fff;

        cursor:
            pointer;
    }


    .btn-delete:hover {

        background:
            #bb2d3b;

        color:
            #fff;
    }


    /* ERROR */

    .delete-error {

        margin:
            0 30px 20px;

        padding:
            14px;

        background:
            #fff0f1;

        border:
            1px solid #f4c7cb;

        border-radius:
            9px;

        color:
            #b02a37;

        font-size:
            13px;

        text-align:
            left;
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


    @media(max-width:600px) {

        .topbar {

            flex-direction: column;

            align-items: flex-start;

            gap: 15px;
        }


        .delete-card {

            margin:
                25px auto;
        }


        .actions {

            flex-direction: column;
        }


        .btn {

            width: 100%;
        }

    }
    </style>

</head>


<body>


    <!-- SIDEBAR -->

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

            <span>Dashboard</span>

        </a>


        <a href="products.php">

            <i class="fa-solid fa-box"></i>

            <span>Products</span>

        </a>


        <a href="categories.php" class="active">

            <i class="fa-solid fa-layer-group"></i>

            <span>Categories</span>

        </a>


        <a href="brands.php">

            <i class="fa-solid fa-tags"></i>

            <span>Brands</span>

        </a>


        <div class="nav-title">
            Sales
        </div>


        <a href="orders.php">

            <i class="fa-solid fa-cart-shopping"></i>

            <span>Orders</span>

        </a>


        <a href="customers.php">

            <i class="fa-solid fa-users"></i>

            <span>Customers</span>

        </a>


        <div class="nav-title">
            Communication
        </div>


        <a href="inquiries.php">

            <i class="fa-solid fa-envelope"></i>

            <span>Inquiries</span>

        </a>


        <a href="reviews.php">

            <i class="fa-solid fa-star"></i>

            <span>Reviews</span>

        </a>


        <div class="nav-title">
            System
        </div>


        <a href="settings.php">

            <i class="fa-solid fa-gear"></i>

            <span>Settings</span>

        </a>


        <a href="logout.php">

            <i class="fa-solid fa-right-from-bracket"></i>

            <span>Logout</span>

        </a>


    </aside>



    <!-- MAIN -->

    <main class="main">


        <!-- TOPBAR -->

        <div class="topbar">


            <div>

                <h1 class="page-title">
                    Delete Category
                </h1>

                <p class="page-subtitle">
                    Manage category records safely
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



        <!-- DELETE CARD -->

        <div class="delete-card">


            <div class="delete-header">


                <div class="warning-icon">

                    <i class="fa-solid fa-trash-can"></i>

                </div>


                <h2>
                    Delete Category?
                </h2>


                <p>

                    You are about to delete this category.
                    Please review the information below before
                    continuing.

                </p>


            </div>



            <!-- CATEGORY DETAILS -->

            <div class="category-details">


                <div class="detail-row">

                    <span class="detail-label">
                        Category
                    </span>

                    <span class="detail-value">

                        <?= htmlspecialchars(
                            $category['name']
                        ) ?>

                    </span>

                </div>


                <div class="detail-row">

                    <span class="detail-label">
                        Category ID
                    </span>

                    <span class="detail-value">

                        #<?= $categoryId ?>

                    </span>

                </div>


                <div class="detail-row">

                    <span class="detail-label">
                        Slug
                    </span>

                    <span class="detail-value">

                        <?= htmlspecialchars(
                            $category['slug']
                        ) ?>

                    </span>

                </div>


                <div class="detail-row">

                    <span class="detail-label">
                        Products Assigned
                    </span>

                    <span class="detail-value">

                        <?= $productCount ?>

                    </span>

                </div>


            </div>



            <?php if ($productCount > 0): ?>


            <!-- BLOCKED -->

            <div class="blocked-warning">


                <strong>

                    <i class="fa-solid fa-triangle-exclamation"></i>

                    Category cannot be deleted

                </strong>


                This category currently has

                <strong style="display:inline;">

                    <?= $productCount ?>

                </strong>

                product(s) assigned to it.

                <br><br>

                Please assign those products to another
                category before deleting this category.

                <br><br>

                This protection prevents products such as
                your Tenda F3 and Tenda F6 from losing their
                category relationship.

            </div>


            <?php else: ?>


            <!-- SAFE TO DELETE -->

            <div class="success-warning">


                <strong>

                    <i class="fa-solid fa-circle-check"></i>

                    Safe to delete

                </strong>


                No products are currently assigned to this
                category. You can safely remove it.

            </div>


            <?php endif; ?>



            <?php if (isset($deleteError)): ?>

            <div class="delete-error">

                <i class="fa-solid fa-circle-exclamation"></i>

                <?= htmlspecialchars(
                        $deleteError
                    ) ?>

            </div>

            <?php endif; ?>



            <!-- ACTIONS -->

            <div class="actions">


                <a href="categories.php" class="btn btn-back">

                    <i class="fa-solid fa-arrow-left"></i>

                    Cancel

                </a>



                <?php if ($productCount === 0): ?>


                <form method="POST" action="" onsubmit="
                        return confirm(
                            'Are you sure you want to permanently delete this category?'
                        );
                    ">

                    <button type="submit" class="btn btn-delete">

                        <i class="fa-solid fa-trash"></i>

                        Delete Category

                    </button>

                </form>


                <?php else: ?>


                <a href="products.php?category=<?= $categoryId ?>" class="btn btn-delete" style="
                        background:#0d6efd;
                    ">

                    <i class="fa-solid fa-box"></i>

                    View Products

                </a>


                <?php endif; ?>


            </div>


        </div>


    </main>


</body>

</html>