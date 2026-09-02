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
    header("Location: categories.php?error=category_not_found");
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
        slug,
        description,
        status
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
| VARIABLES
|--------------------------------------------------------------------------
*/

$name = $category['name'];
$slug = $category['slug'];
$description = $category['description'];
$status = (int)$category['status'];

$errors = [];


/*
|--------------------------------------------------------------------------
| UPDATE CATEGORY
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $status = isset($_POST['status'])
        ? (int)$_POST['status']
        : 1;


    /*
    |--------------------------------------------------------------------------
    | VALIDATE NAME
    |--------------------------------------------------------------------------
    */

    if ($name === '') {

        $errors[] =
            'Category name is required.';
    }


    /*
    |--------------------------------------------------------------------------
    | GENERATE SLUG
    |--------------------------------------------------------------------------
    */

    if ($slug === '' && $name !== '') {

        $slug = strtolower($name);

        $slug = preg_replace(
            '/[^a-z0-9]+/',
            '-',
            $slug
        );

        $slug = trim(
            $slug,
            '-'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | CLEAN SLUG
    |--------------------------------------------------------------------------
    */

    if ($slug !== '') {

        $slug = strtolower($slug);

        $slug = preg_replace(
            '/[^a-z0-9-]+/',
            '-',
            $slug
        );

        $slug = preg_replace(
            '/-+/',
            '-',
            $slug
        );

        $slug = trim(
            $slug,
            '-'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | CHECK DUPLICATE NAME
    |--------------------------------------------------------------------------
    */

    if ($name !== '') {

        $check = $conn->prepare("
            SELECT id
            FROM categories
            WHERE name = ?
            AND id != ?
            LIMIT 1
        ");

        $check->bind_param(
            "si",
            $name,
            $categoryId
        );

        $check->execute();

        $checkResult =
            $check->get_result();

        if ($checkResult->num_rows > 0) {

            $errors[] =
                'Another category with this name already exists.';
        }

        $check->close();
    }


    /*
    |--------------------------------------------------------------------------
    | CHECK DUPLICATE SLUG
    |--------------------------------------------------------------------------
    */

    if ($slug !== '') {

        $checkSlug = $conn->prepare("
            SELECT id
            FROM categories
            WHERE slug = ?
            AND id != ?
            LIMIT 1
        ");

        $checkSlug->bind_param(
            "si",
            $slug,
            $categoryId
        );

        $checkSlug->execute();

        $slugResult =
            $checkSlug->get_result();

        if ($slugResult->num_rows > 0) {

            $errors[] =
                'Another category with this slug already exists.';
        }

        $checkSlug->close();
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE DATABASE
    |--------------------------------------------------------------------------
    */

    if (empty($errors)) {

        $update = $conn->prepare("
            UPDATE categories
            SET
                name = ?,
                slug = ?,
                description = ?,
                status = ?
            WHERE id = ?
        ");

        if (!$update) {

            $errors[] =
                'Database error: ' .
                $conn->error;
        } else {

            $update->bind_param(
                "sssii",
                $name,
                $slug,
                $description,
                $status,
                $categoryId
            );


            if ($update->execute()) {

                $update->close();

                header(
                    "Location: categories.php?success=category_updated"
                );

                exit;
            } else {

                $errors[] =
                    'Unable to update category: ' .
                    $update->error;

                $update->close();
            }
        }
    }
}


/*
|--------------------------------------------------------------------------
| GET PRODUCT COUNT
|--------------------------------------------------------------------------
*/

$productCount = 0;

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

$countResult =
    $countStmt->get_result();

$countRow =
    $countResult->fetch_assoc();

$productCount =
    (int)$countRow['total'];

$countStmt->close();

?>


<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        Edit Category | CORE LTD Admin
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


    /* CARD */

    .form-card {

        max-width: 900px;

        margin: 0 auto;

        background: #fff;

        border-radius: 15px;

        box-shadow:
            0 5px 25px rgba(25, 65, 100, .06);

        overflow: hidden;
    }


    .form-header {

        padding:
            22px 25px;

        border-bottom:
            1px solid #edf1f5;

        display: flex;

        align-items: center;

        gap: 15px;
    }


    .form-header-icon {

        width: 48px;
        height: 48px;

        border-radius: 12px;

        background: #e8f2ff;

        color: #0d6efd;

        display: flex;

        align-items: center;
        justify-content: center;

        font-size: 19px;
    }


    .form-header h3 {

        margin: 0;

        color: #17365d;

        font-size: 19px;

        font-weight: 750;
    }


    .form-header p {

        margin:
            3px 0 0;

        color: #8493a3;

        font-size: 12px;
    }


    .form-body {

        padding: 30px;
    }


    /* INFO */

    .category-info {

        display: grid;

        grid-template-columns:
            repeat(2, 1fr);

        gap: 15px;

        margin-bottom: 25px;
    }


    .info-box {

        background:
            #f7faff;

        border:
            1px solid #e5edf7;

        border-radius: 10px;

        padding: 14px 16px;
    }


    .info-box span {

        display: block;

        color:
            #8493a3;

        font-size: 11px;

        margin-bottom: 4px;
    }


    .info-box strong {

        color:
            #17365d;

        font-size: 17px;
    }


    /* FORM */

    .form-label {

        font-size: 13px;

        font-weight: 700;

        color:
            #34495e;

        margin-bottom: 7px;
    }


    .required {

        color:
            #dc3545;
    }


    .form-control {

        border:
            1px solid #dce5ee;

        border-radius: 8px;

        padding:
            11px 13px;

        font-size: 13px;
    }


    .form-control:focus {

        border-color:
            #0d6efd;

        box-shadow:
            0 0 0 3px rgba(13, 110, 253, .08);
    }


    textarea.form-control {

        min-height: 130px;

        resize: vertical;
    }


    .form-help {

        display: block;

        margin-top: 6px;

        font-size: 11px;

        color:
            #8a98a8;
    }


    .slug-preview {

        margin-top: 8px;

        padding:
            9px 12px;

        background:
            #f7faff;

        border:
            1px solid #e5edf7;

        border-radius: 7px;

        font-size: 12px;

        color:
            #60758c;
    }


    .slug-preview strong {

        color:
            #0d6efd;
    }


    /* STATUS */

    .status-options {

        display: flex;

        gap: 10px;
    }


    .status-option {

        position: relative;
    }


    .status-option input {

        position: absolute;

        opacity: 0;
    }


    .status-option label {

        display: flex;

        align-items: center;

        gap: 8px;

        padding:
            10px 15px;

        border:
            1px solid #dce5ee;

        border-radius: 8px;

        cursor: pointer;

        font-size: 13px;
    }


    .status-option input:checked+label {

        border-color:
            #0d6efd;

        background:
            #eaf3ff;

        color:
            #0754b8;

        font-weight: 700;
    }


    /* ACTIONS */

    .form-actions {

        margin-top: 30px;

        padding-top: 22px;

        border-top:
            1px solid #edf1f5;

        display: flex;

        justify-content: space-between;

        align-items: center;

        gap: 10px;
    }


    .left-actions,
    .right-actions {

        display: flex;

        gap: 10px;
    }


    .btn {

        border: none;

        padding:
            11px 18px;

        border-radius: 8px;

        font-size: 13px;

        font-weight: 700;

        text-decoration: none;

        display: inline-flex;

        align-items: center;

        justify-content: center;

        gap: 7px;
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


    .btn-save {

        background:
            linear-gradient(135deg,
                #0d6efd,
                #0754b8);

        color: #fff;

        cursor: pointer;
    }


    .btn-save:hover {

        color: #fff;

        transform:
            translateY(-1px);
    }


    .btn-view {

        background:
            #e8f8ef;

        color:
            #198754;
    }


    .btn-view:hover {

        background:
            #198754;

        color: #fff;
    }


    /* ERROR */

    .error-box {

        background:
            #fff0f1;

        border:
            1px solid #f4c7cb;

        color:
            #b02a37;

        padding:
            14px 17px;

        border-radius: 9px;

        margin-bottom: 22px;

        font-size: 13px;
    }


    .error-box ul {

        margin:
            7px 0 0;

        padding-left:
            20px;
    }


    @media (max-width:900px) {

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


    @media (max-width:600px) {

        .topbar {

            flex-direction: column;

            align-items: flex-start;

            gap: 15px;
        }


        .form-body {

            padding: 20px;
        }


        .category-info {

            grid-template-columns: 1fr;
        }


        .form-actions {

            flex-direction: column;

            align-items: stretch;
        }


        .left-actions,
        .right-actions {

            width: 100%;
        }


        .btn {

            flex: 1;
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


        <div class="topbar">


            <div>

                <h1 class="page-title">
                    Edit Category
                </h1>

                <p class="page-subtitle">

                    Update category information

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



        <div class="form-card">


            <div class="form-header">


                <div class="form-header-icon">

                    <i class="fa-solid fa-pen"></i>

                </div>


                <div>

                    <h3>
                        Edit Category
                    </h3>

                    <p>
                        Update the details of this category.
                    </p>

                </div>


            </div>



            <div class="form-body">


                <!-- CATEGORY INFORMATION -->

                <div class="category-info">


                    <div class="info-box">

                        <span>
                            Category ID
                        </span>

                        <strong>
                            #<?= $categoryId ?>
                        </strong>

                    </div>


                    <div class="info-box">

                        <span>
                            Products Using This Category
                        </span>

                        <strong>

                            <?= $productCount ?>

                        </strong>

                    </div>


                </div>



                <!-- ERRORS -->

                <?php if (!empty($errors)): ?>

                <div class="error-box">

                    <strong>

                        <i class="fa-solid fa-circle-exclamation"></i>

                        Please correct the following:

                    </strong>


                    <ul>

                        <?php foreach ($errors as $error): ?>

                        <li>

                            <?= htmlspecialchars($error) ?>

                        </li>

                        <?php endforeach; ?>

                    </ul>

                </div>

                <?php endif; ?>



                <form method="POST" action="">


                    <!-- NAME -->

                    <div class="mb-4">


                        <label for="name" class="form-label">

                            Category Name

                            <span class="required">
                                *
                            </span>

                        </label>


                        <input type="text" name="name" id="name" class="form-control"
                            value="<?= htmlspecialchars($name) ?>" required>

                    </div>



                    <!-- SLUG -->

                    <div class="mb-4">


                        <label for="slug" class="form-label">

                            Category Slug

                        </label>


                        <input type="text" name="slug" id="slug" class="form-control"
                            value="<?= htmlspecialchars($slug) ?>">


                        <div class="slug-preview">

                            Website URL:

                            <strong>

                                /category/

                                <span id="slugPreview">

                                    <?= htmlspecialchars(
                                        $slug
                                    ) ?>

                                </span>

                            </strong>

                        </div>


                        <span class="form-help">

                            Use lowercase letters, numbers and
                            hyphens only.

                        </span>

                    </div>



                    <!-- DESCRIPTION -->

                    <div class="mb-4">


                        <label for="description" class="form-label">

                            Description

                        </label>


                        <textarea name="description" id="description"
                            class="form-control"><?= htmlspecialchars($description) ?></textarea>


                    </div>



                    <!-- STATUS -->

                    <div class="mb-4">


                        <label class="form-label">
                            Status
                        </label>


                        <div class="status-options">


                            <div class="status-option">


                                <input type="radio" name="status" id="active" value="1" <?= $status === 1
                                                                                            ? 'checked'
                                                                                            : '' ?>>


                                <label for="active">

                                    <i class="fa-solid fa-circle-check"></i>

                                    Active

                                </label>


                            </div>



                            <div class="status-option">


                                <input type="radio" name="status" id="inactive" value="0" <?= $status === 0
                                                                                                ? 'checked'
                                                                                                : '' ?>>


                                <label for="inactive">

                                    <i class="fa-solid fa-circle-xmark"></i>

                                    Inactive

                                </label>


                            </div>


                        </div>

                    </div>



                    <!-- ACTIONS -->

                    <div class="form-actions">


                        <div class="left-actions">


                            <a href="categories.php" class="btn btn-back">

                                <i class="fa-solid fa-arrow-left"></i>

                                Back

                            </a>


                        </div>


                        <div class="right-actions">


                            <?php if ($productCount > 0): ?>

                            <a href="products.php?category=<?= $categoryId ?>" class="btn btn-view">

                                <i class="fa-solid fa-box"></i>

                                View Products

                            </a>

                            <?php endif; ?>


                            <button type="submit" class="btn btn-save">

                                <i class="fa-solid fa-floppy-disk"></i>

                                Save Changes

                            </button>


                        </div>


                    </div>


                </form>


            </div>


        </div>


    </main>



    <script>
    /*
|--------------------------------------------------------------------------
| SLUG PREVIEW
|--------------------------------------------------------------------------
*/

    const slugInput =
        document.getElementById("slug");

    const slugPreview =
        document.getElementById("slugPreview");


    function updateSlugPreview() {

        slugPreview.textContent =
            slugInput.value ||
            "category-name";
    }


    slugInput.addEventListener(
        "input",
        updateSlugPreview
    );


    updateSlugPreview();
    </script>


</body>

</html>