<?php

session_start();

require_once __DIR__ . '/../includes/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}


/* =========================================================
   GET BRAND ID
========================================================= */

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header("Location: brands.php");
    exit;
}


/* =========================================================
   GET BRAND
========================================================= */

$stmt = $conn->prepare("
    SELECT
        id,
        name,
        logo
    FROM brands
    WHERE id = ?
    LIMIT 1
");

if (!$stmt) {
    die("Database error: " . $conn->error);
}

$stmt->bind_param("i", $id);

$stmt->execute();

$result = $stmt->get_result();

$brand = $result->fetch_assoc();

$stmt->close();


if (!$brand) {
    header("Location: brands.php");
    exit;
}


/* =========================================================
   CHECK PRODUCTS USING THIS BRAND
========================================================= */

$productCount = 0;

$checkProducts = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM products
    WHERE brand_id = ?
");

if ($checkProducts) {

    $checkProducts->bind_param(
        "i",
        $id
    );

    $checkProducts->execute();

    $productResult =
        $checkProducts->get_result();

    $productData =
        $productResult->fetch_assoc();

    $productCount =
        (int) ($productData['total'] ?? 0);

    $checkProducts->close();
}


/* =========================================================
   DELETE ONLY AFTER CONFIRMATION
========================================================= */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $confirm =
        $_POST['confirm'] ?? '';


    if ($confirm !== 'DELETE') {

        header(
            "Location: brands.php?error=delete_cancelled"
        );

        exit;
    }


    /* =====================================================
       DON'T DELETE IF PRODUCTS EXIST
    ===================================================== */

    if ($productCount > 0) {

        header(
            "Location: brands.php?error=brand_has_products"
        );

        exit;
    }


    /* =====================================================
       DELETE BRAND
    ===================================================== */

    $delete = $conn->prepare("
        DELETE FROM brands
        WHERE id = ?
        LIMIT 1
    ");


    if (!$delete) {

        header(
            "Location: brands.php?error=delete_failed"
        );

        exit;
    }


    $delete->bind_param(
        "i",
        $id
    );


    if ($delete->execute()) {

        $delete->close();


        /* =================================================
           DELETE BRAND LOGO FROM STORAGE
        ================================================= */

        if (!empty($brand['logo'])) {

            $logoPath =
                __DIR__
                . '/../uploads/brands/'
                . $brand['logo'];


            if (
                file_exists($logoPath)
            ) {

                unlink($logoPath);
            }
        }


        header(
            "Location: brands.php?success=deleted"
        );

        exit;
    } else {

        $delete->close();

        header(
            "Location: brands.php?error=delete_failed"
        );

        exit;
    }
}


/* =========================================================
   DETERMINE MESSAGE
========================================================= */

if ($productCount > 0) {

    $blocked = true;
} else {

    $blocked = false;
}

?>


<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        Delete Brand | CORE LTD Admin
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

        color: white;

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

        transition: .2s;
    }


    .sidebar a i {

        width: 20px;

        text-align: center;
    }


    .sidebar a:hover {

        background:
            rgba(255, 255, 255, .12);

        color: white;
    }


    .sidebar a.active {

        background: white;

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

        background: white;

        border-radius: 14px;

        padding:
            18px 22px;

        display: flex;

        align-items: center;

        justify-content: space-between;

        margin-bottom: 25px;

        box-shadow:
            0 5px 25px rgba(25, 65, 100, .06);
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


    /* =====================================================
           DELETE CARD
        ===================================================== */

    .delete-card {

        max-width: 650px;

        margin:
            50px auto;

        background: white;

        border-radius: 18px;

        padding: 35px;

        text-align: center;

        box-shadow:
            0 10px 35px rgba(25, 65, 100, .08);
    }


    .delete-icon {

        width: 75px;
        height: 75px;

        margin:
            0 auto 20px;

        border-radius: 50%;

        background: #fff1f2;

        color: #dc3545;

        display: flex;

        align-items: center;
        justify-content: center;

        font-size: 30px;
    }


    .brand-preview {

        display: flex;

        align-items: center;

        justify-content: center;

        gap: 15px;

        margin:
            25px 0;

        padding: 15px;

        border:
            1px solid #e5edf5;

        border-radius: 12px;

        background: #f8fbff;
    }


    .brand-preview img {

        width: 60px;
        height: 60px;

        object-fit: contain;

        background: white;

        border:
            1px solid #dce5ee;

        border-radius: 9px;

        padding: 5px;
    }


    .brand-name {

        font-size: 17px;

        font-weight: 750;

        color: #17365d;
    }


    .warning {

        padding: 15px;

        border-radius: 10px;

        background: #fff8e8;

        border:
            1px solid #ffe2a9;

        color: #8a5a00;

        font-size: 12px;

        margin-bottom: 25px;
    }


    .danger-box {

        padding: 18px;

        border-radius: 10px;

        background: #fff1f2;

        border:
            1px solid #ffd5d9;

        color: #a61b29;

        font-size: 12px;

        margin-bottom: 25px;
    }


    .actions {

        display: flex;

        justify-content: center;

        gap: 12px;
    }


    .btn-custom {

        border: none;

        border-radius: 8px;

        padding:
            11px 18px;

        font-size: 12px;

        font-weight: 700;

        text-decoration: none;

        display: inline-flex;

        align-items: center;

        gap: 7px;

        cursor: pointer;
    }


    .btn-cancel {

        background: #eef2f6;

        color: #52677d;
    }


    .btn-delete {

        background:
            linear-gradient(135deg,
                #dc3545,
                #b42332);

        color: white;
    }


    .btn-delete:hover {

        color: white;
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

            gap: 10px;
        }


        .delete-card {

            padding: 25px 18px;

        }


        .actions {

            flex-direction: column;

        }


        .btn-custom {

            justify-content: center;

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


        <a href="customers.php">

            <i class="fa-solid fa-users"></i>

            <span>
                Customers
            </span>

        </a>


        <div class="nav-title">
            Communication
        </div>


        <a href="inquiries.php">

            <i class="fa-solid fa-envelope"></i>

            <span>
                Inquiries
            </span>

        </a>


        <a href="reviews.php">

            <i class="fa-solid fa-star"></i>

            <span>
                Reviews
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


        <div class="topbar">

            <div>

                <h1 class="page-title">
                    Delete Brand
                </h1>

                <p class="page-subtitle">
                    Manage your CORE LTD product brands.
                </p>

            </div>

        </div>



        <div class="delete-card">


            <?php if ($blocked): ?>


            <div class="delete-icon">

                <i class="fa-solid fa-lock"></i>

            </div>


            <h2 style="
                    color:#17365d;
                    font-size:21px;
                    font-weight:800;
                ">

                Brand Cannot Be Deleted

            </h2>


            <p style="
                    color:#7a8998;
                    font-size:13px;
                    line-height:1.7;
                ">

                <strong>
                    <?= htmlspecialchars($brand['name']) ?>
                </strong>

                currently has

                <strong>
                    <?= $productCount ?>
                </strong>

                product<?= $productCount != 1 ? 's' : '' ?>
                assigned to it.

            </p>


            <div class="danger-box">

                <i class="fa-solid fa-triangle-exclamation"></i>

                You cannot delete this brand while
                products are using it.

                <br><br>

                Remove or reassign the products to
                another brand first.

            </div>


            <div class="actions">

                <a href="brands.php" class="btn-custom btn-cancel">

                    <i class="fa-solid fa-arrow-left"></i>

                    Back to Brands

                </a>


                <a href="products.php" class="btn-custom btn-delete">

                    <i class="fa-solid fa-box"></i>

                    View Products

                </a>

            </div>


            <?php else: ?>


            <div class="delete-icon">

                <i class="fa-solid fa-trash"></i>

            </div>


            <h2 style="
                    color:#17365d;
                    font-size:21px;
                    font-weight:800;
                ">

                Delete This Brand?

            </h2>


            <p style="
                    color:#7a8998;
                    font-size:13px;
                    line-height:1.7;
                ">

                You are about to permanently delete
                this brand.

            </p>


            <div class="brand-preview">


                <?php if (!empty($brand['logo'])): ?>

                <img src="../uploads/brands/<?= htmlspecialchars($brand['logo']) ?>"
                    alt="<?= htmlspecialchars($brand['name']) ?>">

                <?php else: ?>

                <div style="
                            width:60px;
                            height:60px;
                            border-radius:9px;
                            background:#eef4fa;
                            display:flex;
                            align-items:center;
                            justify-content:center;
                            color:#8fa0b1;
                        ">

                    <i class="fa-solid fa-tag"></i>

                </div>

                <?php endif; ?>


                <div class="brand-name">

                    <?= htmlspecialchars($brand['name']) ?>

                </div>


            </div>


            <div class="warning">

                <i class="fa-solid fa-triangle-exclamation"></i>

                <strong>
                    Warning:
                </strong>

                This action cannot be undone.

                The brand and its logo will be permanently
                removed.

            </div>


            <form method="POST" onsubmit="
                    return confirm(
                        'Are you sure you want to permanently delete this brand?'
                    );
                ">

                <input type="hidden" name="confirm" value="DELETE">


                <div class="actions">


                    <a href="brands.php" class="btn-custom btn-cancel">

                        <i class="fa-solid fa-xmark"></i>

                        Cancel

                    </a>


                    <button type="submit" class="btn-custom btn-delete">

                        <i class="fa-solid fa-trash"></i>

                        Delete Brand

                    </button>


                </div>

            </form>


            <?php endif; ?>


        </div>


    </main>


</body>

</html>