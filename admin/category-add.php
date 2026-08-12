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
| VARIABLES
|--------------------------------------------------------------------------
*/

$name = '';
$slug = '';
$description = '';
$status = 1;

$errors = [];


/*
|--------------------------------------------------------------------------
| ADD CATEGORY
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $status = isset($_POST['status']) ? (int)$_POST['status'] : 1;


    /*
    |--------------------------------------------------------------------------
    | VALIDATION
    |--------------------------------------------------------------------------
    */

    if ($name === '') {

        $errors[] = 'Category name is required.';
    }


    /*
    |--------------------------------------------------------------------------
    | GENERATE SLUG IF EMPTY
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
    | CHECK DUPLICATE CATEGORY NAME
    |--------------------------------------------------------------------------
    */

    if ($name !== '') {

        $check = $conn->prepare("
            SELECT id
            FROM categories
            WHERE name = ?
            LIMIT 1
        ");

        $check->bind_param(
            "s",
            $name
        );

        $check->execute();

        $checkResult = $check->get_result();

        if ($checkResult->num_rows > 0) {

            $errors[] =
                'A category with this name already exists.';
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
            LIMIT 1
        ");

        $checkSlug->bind_param(
            "s",
            $slug
        );

        $checkSlug->execute();

        $slugResult =
            $checkSlug->get_result();

        if ($slugResult->num_rows > 0) {

            $errors[] =
                'A category with this slug already exists.';
        }

        $checkSlug->close();
    }


    /*
    |--------------------------------------------------------------------------
    | INSERT CATEGORY
    |--------------------------------------------------------------------------
    */

    if (empty($errors)) {

        $stmt = $conn->prepare("
            INSERT INTO categories
            (
                name,
                slug,
                description,
                status
            )
            VALUES
            (
                ?,
                ?,
                ?,
                ?
            )
        ");

        if (!$stmt) {

            $errors[] =
                'Database error: ' .
                $conn->error;
        } else {

            $stmt->bind_param(
                "sssi",
                $name,
                $slug,
                $description,
                $status
            );


            if ($stmt->execute()) {

                $stmt->close();

                header(
                    "Location: categories.php?success=category_added"
                );

                exit;
            } else {

                $errors[] =
                    'Unable to save the category: ' .
                    $stmt->error;

                $stmt->close();
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
        Add Category | CORE LTD Admin
    </title>


    <!-- Bootstrap -->

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">


    <!-- Font Awesome -->

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


    /*
        |--------------------------------------------------------------------------
        | SIDEBAR
        |--------------------------------------------------------------------------
        */

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

        color: #ffffff;

        padding: 25px 15px;

        box-shadow:
            4px 0 18px rgba(0, 61, 120, 0.12);

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

        color: #ffffff;
    }


    .sidebar a.active {

        background:
            #ffffff;

        color:
            #07549a;

        font-weight: 700;

        box-shadow:
            0 5px 15px rgba(0, 0, 0, .08);
    }


    /*
        |--------------------------------------------------------------------------
        | MAIN
        |--------------------------------------------------------------------------
        */

    .main {

        margin-left:
            245px;

        min-height:
            100vh;

        padding:
            28px;
    }


    /*
        |--------------------------------------------------------------------------
        | TOP BAR
        |--------------------------------------------------------------------------
        */

    .topbar {

        background:
            #ffffff;

        border-radius:
            14px;

        padding:
            18px 22px;

        display:
            flex;

        align-items:
            center;

        justify-content:
            space-between;

        box-shadow:
            0 5px 25px rgba(25, 65, 100, .06);

        margin-bottom:
            25px;
    }


    .page-title {

        margin: 0;

        font-size:
            25px;

        font-weight:
            800;

        color:
            #17365d;
    }


    .page-subtitle {

        margin:
            4px 0 0;

        color:
            #7a8998;

        font-size:
            13px;
    }


    .admin-user {

        display:
            flex;

        align-items:
            center;

        gap:
            10px;
    }


    .admin-avatar {

        width:
            42px;

        height:
            42px;

        border-radius:
            50%;

        background:
            linear-gradient(135deg,
                #0d6efd,
                #0a58ca);

        color:
            #ffffff;

        display:
            flex;

        align-items:
            center;

        justify-content:
            center;

        font-weight:
            700;
    }


    /*
        |--------------------------------------------------------------------------
        | FORM CARD
        |--------------------------------------------------------------------------
        */

    .form-card {

        max-width:
            900px;

        margin:
            0 auto;

        background:
            #ffffff;

        border-radius:
            15px;

        box-shadow:
            0 5px 25px rgba(25, 65, 100, .06);

        overflow:
            hidden;
    }


    .form-header {

        padding:
            22px 25px;

        border-bottom:
            1px solid #edf1f5;

        display:
            flex;

        align-items:
            center;

        gap:
            15px;
    }


    .form-header-icon {

        width:
            48px;

        height:
            48px;

        border-radius:
            12px;

        background:
            #e8f2ff;

        color:
            #0d6efd;

        display:
            flex;

        align-items:
            center;

        justify-content:
            center;

        font-size:
            19px;
    }


    .form-header h3 {

        margin:
            0;

        color:
            #17365d;

        font-size:
            19px;

        font-weight:
            750;
    }


    .form-header p {

        margin:
            3px 0 0;

        color:
            #8493a3;

        font-size:
            12px;
    }


    .form-body {

        padding:
            30px;
    }


    /*
        |--------------------------------------------------------------------------
        | FORM ELEMENTS
        |--------------------------------------------------------------------------
        */

    .form-label {

        font-size:
            13px;

        font-weight:
            700;

        color:
            #34495e;

        margin-bottom:
            7px;
    }


    .required {

        color:
            #dc3545;
    }


    .form-control {

        border:
            1px solid #dce5ee;

        border-radius:
            8px;

        padding:
            11px 13px;

        font-size:
            13px;

        color:
            #26364a;
    }


    .form-control:focus {

        border-color:
            #0d6efd;

        box-shadow:
            0 0 0 3px rgba(13, 110, 253, .08);
    }


    textarea.form-control {

        min-height:
            130px;

        resize:
            vertical;
    }


    .form-help {

        display:
            block;

        margin-top:
            6px;

        font-size:
            11px;

        color:
            #8a98a8;
    }


    .slug-preview {

        margin-top:
            8px;

        padding:
            9px 12px;

        background:
            #f7faff;

        border:
            1px solid #e5edf7;

        border-radius:
            7px;

        font-size:
            12px;

        color:
            #60758c;
    }


    .slug-preview strong {

        color:
            #0d6efd;
    }


    /*
        |--------------------------------------------------------------------------
        | STATUS
        |--------------------------------------------------------------------------
        */

    .status-options {

        display:
            flex;

        gap:
            10px;

        flex-wrap:
            wrap;
    }


    .status-option {

        position:
            relative;
    }


    .status-option input {

        position:
            absolute;

        opacity:
            0;
    }


    .status-option label {

        display:
            flex;

        align-items:
            center;

        gap:
            8px;

        padding:
            10px 15px;

        border:
            1px solid #dce5ee;

        border-radius:
            8px;

        cursor:
            pointer;

        font-size:
            13px;

        transition:
            .2s;
    }


    .status-option input:checked+label {

        border-color:
            #0d6efd;

        background:
            #eaf3ff;

        color:
            #0754b8;

        font-weight:
            700;
    }


    /*
        |--------------------------------------------------------------------------
        | BUTTONS
        |--------------------------------------------------------------------------
        */

    .form-actions {

        margin-top:
            30px;

        padding-top:
            22px;

        border-top:
            1px solid #edf1f5;

        display:
            flex;

        justify-content:
            flex-end;

        gap:
            10px;
    }


    .btn-cancel {

        padding:
            11px 18px;

        border-radius:
            8px;

        background:
            #f1f3f5;

        color:
            #566574;

        text-decoration:
            none;

        font-size:
            13px;

        font-weight:
            600;
    }


    .btn-cancel:hover {

        background:
            #e2e6ea;

        color:
            #343a40;
    }


    .btn-save {

        border:
            none;

        padding:
            11px 20px;

        border-radius:
            8px;

        background:
            linear-gradient(135deg,
                #0d6efd,
                #0754b8);

        color:
            #ffffff;

        font-size:
            13px;

        font-weight:
            700;

        display:
            inline-flex;

        align-items:
            center;

        gap:
            7px;

        cursor:
            pointer;
    }


    .btn-save:hover {

        transform:
            translateY(-1px);

        box-shadow:
            0 5px 15px rgba(13, 110, 253, .18);
    }


    /*
        |--------------------------------------------------------------------------
        | ERROR
        |--------------------------------------------------------------------------
        */

    .error-box {

        background:
            #fff0f1;

        border:
            1px solid #f4c7cb;

        color:
            #b02a37;

        padding:
            14px 17px;

        border-radius:
            9px;

        margin-bottom:
            22px;

        font-size:
            13px;
    }


    .error-box ul {

        margin:
            7px 0 0;

        padding-left:
            20px;
    }


    /*
        |--------------------------------------------------------------------------
        | RESPONSIVE
        |--------------------------------------------------------------------------
        */

    @media (max-width: 900px) {

        .sidebar {

            width:
                70px;

            padding:
                20px 8px;
        }


        .brand h2,
        .brand span,
        .sidebar a span,
        .nav-title {

            display:
                none;
        }


        .brand {

            border:
                none;
        }


        .sidebar a {

            justify-content:
                center;
        }


        .sidebar a i {

            width:
                auto;
        }


        .main {

            margin-left:
                70px;

            padding:
                18px;
        }

    }


    @media (max-width: 600px) {

        .topbar {

            align-items:
                flex-start;

            flex-direction:
                column;
        }


        .form-body {

            padding:
                20px;
        }


        .form-actions {

            flex-direction:
                column-reverse;
        }


        .btn-save,
        .btn-cancel {

            width:
                100%;

            justify-content:
                center;

            text-align:
                center;
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
     MAIN CONTENT
========================================================= -->

    <main class="main">


        <!-- TOP BAR -->

        <div class="topbar">


            <div>

                <h1 class="page-title">
                    Add Category
                </h1>


                <p class="page-subtitle">

                    Create a new product category

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



        <!-- FORM CARD -->

        <div class="form-card">


            <div class="form-header">


                <div class="form-header-icon">

                    <i class="fa-solid fa-layer-group"></i>

                </div>


                <div>

                    <h3>
                        Category Information
                    </h3>

                    <p>
                        Enter the details for your new category.
                    </p>

                </div>


            </div>



            <div class="form-body">


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


                    <!-- CATEGORY NAME -->

                    <div class="mb-4">


                        <label for="name" class="form-label">

                            Category Name
                            <span class="required">*</span>

                        </label>


                        <input type="text" name="name" id="name" class="form-control"
                            value="<?= htmlspecialchars($name) ?>" placeholder="e.g. Routers" required>


                        <span class="form-help">

                            Enter the name customers will see.

                        </span>


                    </div>



                    <!-- SLUG -->

                    <div class="mb-4">


                        <label for="slug" class="form-label">

                            Category Slug

                        </label>


                        <input type="text" name="slug" id="slug" class="form-control"
                            value="<?= htmlspecialchars($slug) ?>" placeholder="e.g. routers">


                        <div class="slug-preview">

                            Website URL:

                            <strong>
                                /category/<span
                                    id="slugPreview"><?= htmlspecialchars($slug ?: 'category-name') ?></span>
                            </strong>

                        </div>


                        <span class="form-help">

                            Leave empty and the system will
                            automatically generate the slug.

                        </span>


                    </div>



                    <!-- DESCRIPTION -->

                    <div class="mb-4">


                        <label for="description" class="form-label">

                            Description

                        </label>


                        <textarea name="description" id="description" class="form-control"
                            placeholder="Briefly describe this category..."><?= htmlspecialchars($description) ?></textarea>


                        <span class="form-help">

                            A short description can help customers
                            understand the category.

                        </span>


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


                        <span class="form-help">

                            Inactive categories will not be
                            available for normal product assignment.

                        </span>

                    </div>



                    <!-- ACTIONS -->

                    <div class="form-actions">


                        <a href="categories.php" class="btn-cancel">

                            <i class="fa-solid fa-arrow-left"></i>

                            Cancel

                        </a>


                        <button type="submit" class="btn-save">

                            <i class="fa-solid fa-floppy-disk"></i>

                            Save Category

                        </button>


                    </div>


                </form>


            </div>


        </div>


    </main>



    <script>
    /*
|--------------------------------------------------------------------------
| AUTOMATIC SLUG GENERATION
|--------------------------------------------------------------------------
*/

    const nameInput =
        document.getElementById("name");

    const slugInput =
        document.getElementById("slug");

    const slugPreview =
        document.getElementById("slugPreview");


    let slugManuallyChanged = false;


    /*
    |--------------------------------------------------------------------------
    | DETECT MANUAL SLUG EDIT
    |--------------------------------------------------------------------------
    */

    slugInput.addEventListener(
        "input",
        function() {

            slugManuallyChanged = true;

            updateSlugPreview();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | GENERATE SLUG FROM NAME
    |--------------------------------------------------------------------------
    */

    nameInput.addEventListener(
        "input",
        function() {

            if (!slugManuallyChanged) {

                let slug =
                    this.value
                    .toLowerCase()
                    .trim()
                    .replace(
                        /[^a-z0-9]+/g,
                        "-"
                    )
                    .replace(
                        /^-+|-+$/g,
                        ""
                    );

                slugInput.value =
                    slug;

            }

            updateSlugPreview();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | PREVIEW
    |--------------------------------------------------------------------------
    */

    function updateSlugPreview() {

        slugPreview.textContent =
            slugInput.value ||
            "category-name";
    }


    updateSlugPreview();
    </script>


</body>

</html>