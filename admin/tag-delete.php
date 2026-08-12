<?php

session_start();

require_once __DIR__ . '/../includes/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}


/* =========================================================
   GET TAG ID
========================================================= */

$id = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);

if (!$id || $id <= 0) {
    header("Location: tags.php?error=invalid_id");
    exit;
}


/* =========================================================
   FETCH TAG + PRODUCT COUNT
========================================================= */

$stmt = $conn->prepare("
    SELECT
        t.id,
        t.name,
        t.slug,
        t.created_at,
        COUNT(pt.product_id) AS product_count
    FROM tags t
    LEFT JOIN product_tags pt
        ON pt.tag_id = t.id
    WHERE t.id = ?
    GROUP BY
        t.id,
        t.name,
        t.slug,
        t.created_at
    LIMIT 1
");

if (!$stmt) {
    die("Database error: " . $conn->error);
}

$stmt->bind_param("i", $id);

$stmt->execute();

$result = $stmt->get_result();

$tag = $result->fetch_assoc();

$stmt->close();


if (!$tag) {
    header("Location: tags.php?error=not_found");
    exit;
}


$productCount = (int)$tag['product_count'];

$error = '';


/* =========================================================
   DELETE
========================================================= */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /*
     * Never allow deletion if the tag is being used
     * by products.
     */

    if ($productCount > 0) {

        $error =
            'This tag cannot be deleted because it is currently '
            . 'assigned to '
            . $productCount
            . ' product'
            . ($productCount !== 1 ? 's.' : '.');
    } else {

        $delete = $conn->prepare("
            DELETE FROM tags
            WHERE id = ?
            LIMIT 1
        ");

        if (!$delete) {

            $error =
                'Database error: '
                . $conn->error;
        } else {

            $delete->bind_param(
                "i",
                $id
            );


            if ($delete->execute()) {

                $delete->close();

                header(
                    "Location: tags.php?success=deleted"
                );

                exit;
            } else {

                $error =
                    'Unable to delete the tag. '
                    . $delete->error;

                $delete->close();
            }
        }
    }
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        Delete Tag | CORE LTD Admin
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

        box-shadow:
            0 10px 35px rgba(25, 65, 100, .08);

        overflow: hidden;

        text-align: center;
    }


    .delete-header {

        padding:
            32px 25px 20px;
    }


    .delete-icon {

        width: 70px;
        height: 70px;

        margin:
            0 auto 18px;

        border-radius: 18px;

        display: flex;

        align-items: center;
        justify-content: center;

        font-size: 28px;
    }


    .delete-icon.warning {

        background: #fff3e0;

        color: #f08c00;
    }


    .delete-icon.danger {

        background: #fff0f1;

        color: #dc3545;
    }


    .delete-header h2 {

        margin: 0;

        color: #17365d;

        font-size: 21px;

        font-weight: 800;
    }


    .delete-header p {

        margin:
            8px auto 0;

        max-width: 500px;

        color: #7b8b9c;

        font-size: 12px;

        line-height: 1.7;
    }


    /* =====================================================
           TAG DISPLAY
        ===================================================== */

    .tag-box {

        margin:
            10px 25px 25px;

        padding:
            18px;

        border:
            1px solid #e4ebf2;

        border-radius: 12px;

        background:
            linear-gradient(135deg,
                #f8fbff,
                #ffffff);
    }


    .tag-name {

        font-size: 18px;

        font-weight: 800;

        color: #17365d;

        margin-bottom: 6px;
    }


    .tag-slug {

        display: inline-block;

        font-family:
            Consolas,
            monospace;

        background: #edf4fb;

        color: #718398;

        border-radius: 6px;

        padding:
            5px 9px;

        font-size: 10px;
    }


    /* =====================================================
           WARNING
        ===================================================== */

    .warning-box {

        margin:
            0 25px 25px;

        padding:
            13px 15px;

        border-radius: 9px;

        background: #fff8e8;

        border:
            1px solid #ffe5aa;

        color: #916300;

        font-size: 11px;

        line-height: 1.6;

        text-align: left;
    }


    .error-box {

        margin:
            0 25px 25px;

        padding:
            13px 15px;

        border-radius: 9px;

        background: #fff0f1;

        border:
            1px solid #ffd2d7;

        color: #b42332;

        font-size: 11px;

        text-align: left;
    }


    /* =====================================================
           ACTIONS
        ===================================================== */

    .delete-actions {

        padding:
            18px 25px 25px;

        display: flex;

        justify-content: center;

        gap: 10px;

        border-top:
            1px solid #edf1f5;
    }


    .btn-custom {

        display: inline-flex;

        align-items: center;

        justify-content: center;

        gap: 7px;

        border: none;

        border-radius: 8px;

        padding:
            11px 18px;

        font-size: 12px;

        font-weight: 700;

        text-decoration: none;

        cursor: pointer;

        transition: .2s;
    }


    .btn-cancel {

        background: #eef2f6;

        color: #52677d;
    }


    .btn-cancel:hover {

        background: #e2e7ec;

        color: #40566c;
    }


    .btn-delete {

        background:
            linear-gradient(135deg,
                #dc3545,
                #b42332);

        color: white;

        box-shadow:
            0 4px 12px rgba(220, 53, 69, .18);
    }


    .btn-delete:hover {

        color: white;

        transform:
            translateY(-1px);
    }


    /* =====================================================
           RESPONSIVE
        ===================================================== */

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

        .delete-actions {

            flex-direction: column-reverse;
        }


        .btn-custom {

            width: 100%;
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


        <a href="brands.php">

            <i class="fa-solid fa-tags"></i>

            <span>
                Brands
            </span>

        </a>


        <a href="tags.php" class="active">

            <i class="fa-solid fa-tag"></i>

            <span>
                Tags
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

            <h1 class="page-title">
                Delete Tag
            </h1>

            <p class="page-subtitle">
                Remove an unused product tag.
            </p>

        </div>



        <div class="delete-card">


            <?php if ($productCount > 0): ?>


            <!-- =============================================
                 TAG IN USE
            ============================================== -->

            <div class="delete-header">

                <div class="delete-icon warning">

                    <i class="fa-solid fa-triangle-exclamation"></i>

                </div>


                <h2>
                    Tag Cannot Be Deleted
                </h2>


                <p>

                    This tag is currently assigned to
                    <strong>
                        <?= $productCount ?>
                        product<?= $productCount !== 1 ? 's' : '' ?>
                    </strong>.
                    Deleting it could affect your product
                    categorization.

                </p>

            </div>



            <div class="tag-box">

                <div class="tag-name">

                    <i class="fa-solid fa-tag" style="color:#0d6efd;"></i>

                    <?= htmlspecialchars(
                            $tag['name']
                        ) ?>

                </div>


                <span class="tag-slug">

                    <?= htmlspecialchars(
                            $tag['slug']
                        ) ?>

                </span>

            </div>



            <div class="warning-box">

                <i class="fa-solid fa-circle-info"></i>

                Please remove this tag from the products using it
                before deleting the tag.

            </div>



            <div class="delete-actions">

                <a href="tags.php" class="btn-custom btn-cancel">

                    <i class="fa-solid fa-arrow-left"></i>

                    Back to Tags

                </a>

            </div>


            <?php else: ?>


            <!-- =============================================
                 TAG CAN BE DELETED
            ============================================== -->

            <div class="delete-header">

                <div class="delete-icon danger">

                    <i class="fa-solid fa-trash"></i>

                </div>


                <h2>
                    Delete This Tag?
                </h2>


                <p>

                    You are about to permanently delete this
                    tag. This action cannot be undone.

                </p>

            </div>



            <div class="tag-box">

                <div class="tag-name">

                    <i class="fa-solid fa-tag" style="color:#0d6efd;"></i>

                    <?= htmlspecialchars(
                            $tag['name']
                        ) ?>

                </div>


                <span class="tag-slug">

                    <?= htmlspecialchars(
                            $tag['slug']
                        ) ?>

                </span>

            </div>



            <?php if ($error !== ''): ?>

            <div class="error-box">

                <i class="fa-solid fa-circle-exclamation"></i>

                <?= htmlspecialchars($error) ?>

            </div>

            <?php endif; ?>



            <div class="warning-box">

                <i class="fa-solid fa-triangle-exclamation"></i>

                This will permanently remove the tag from your
                database. Make sure you really want to continue.

            </div>



            <form method="POST" action="">

                <div class="delete-actions">


                    <a href="tags.php" class="btn-custom btn-cancel">

                        <i class="fa-solid fa-arrow-left"></i>

                        Cancel

                    </a>


                    <button type="submit" class="btn-custom btn-delete">

                        <i class="fa-solid fa-trash"></i>

                        Yes, Delete Tag

                    </button>


                </div>

            </form>


            <?php endif; ?>


        </div>


    </main>


</body>

</html>