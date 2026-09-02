<?php

session_start();

require_once __DIR__ . '/../includes/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$errors = [];
$success = false;


/* =========================================================
   GENERATE SLUG
========================================================= */

function generateSlug($text)
{
    $text = strtolower(trim($text));

    $text = preg_replace('/[^a-z0-9]+/', '-', $text);

    $text = trim($text, '-');

    return $text;
}


/* =========================================================
   FORM SUBMISSION
========================================================= */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $website = trim($_POST['website'] ?? '');
    $status = isset($_POST['status']) ? 1 : 0;


    /* =====================================================
       VALIDATION
    ===================================================== */

    if ($name === '') {
        $errors[] = "Brand name is required.";
    }


    /* Automatically create slug if empty */

    if ($slug === '' && $name !== '') {
        $slug = generateSlug($name);
    }


    if ($slug === '') {
        $errors[] = "Slug could not be generated.";
    }


    /* =====================================================
       CHECK DUPLICATE NAME
    ===================================================== */

    if (empty($errors)) {

        $check = $conn->prepare("
            SELECT id
            FROM brands
            WHERE name = ?
            LIMIT 1
        ");

        if (!$check) {
            $errors[] = "Database error: " . $conn->error;
        } else {

            $check->bind_param("s", $name);

            $check->execute();

            $checkResult = $check->get_result();

            if ($checkResult->num_rows > 0) {
                $errors[] = "A brand with this name already exists.";
            }

            $check->close();
        }
    }


    /* =====================================================
       CHECK DUPLICATE SLUG
    ===================================================== */

    if (empty($errors)) {

        $checkSlug = $conn->prepare("
            SELECT id
            FROM brands
            WHERE slug = ?
            LIMIT 1
        ");

        if (!$checkSlug) {
            $errors[] = "Database error: " . $conn->error;
        } else {

            $checkSlug->bind_param("s", $slug);

            $checkSlug->execute();

            $slugResult = $checkSlug->get_result();

            if ($slugResult->num_rows > 0) {
                $errors[] = "This slug is already being used.";
            }

            $checkSlug->close();
        }
    }


    /* =====================================================
       WEBSITE VALIDATION
    ===================================================== */

    if ($website !== '') {

        if (
            !filter_var(
                $website,
                FILTER_VALIDATE_URL
            )
        ) {

            $errors[] =
                "Please enter a valid website URL.";
        }
    }


    /* =====================================================
       LOGO UPLOAD
    ===================================================== */

    $logoName = null;


    if (
        isset($_FILES['logo']) &&
        $_FILES['logo']['error'] !== UPLOAD_ERR_NO_FILE
    ) {

        if (
            $_FILES['logo']['error']
            !== UPLOAD_ERR_OK
        ) {

            $errors[] =
                "There was a problem uploading the logo.";
        } else {

            $allowedExtensions = [
                'jpg',
                'jpeg',
                'png',
                'webp'
            ];

            $fileName =
                $_FILES['logo']['name'];

            $tmpName =
                $_FILES['logo']['tmp_name'];

            $fileSize =
                $_FILES['logo']['size'];

            $extension =
                strtolower(
                    pathinfo(
                        $fileName,
                        PATHINFO_EXTENSION
                    )
                );


            /* Maximum 2MB */

            if ($fileSize > 2 * 1024 * 1024) {

                $errors[] =
                    "Logo must not exceed 2MB.";
            }


            if (
                !in_array(
                    $extension,
                    $allowedExtensions,
                    true
                )
            ) {

                $errors[] =
                    "Logo must be JPG, JPEG, PNG or WEBP.";
            }


            /* Check actual image */

            if (
                empty($errors) &&
                @getimagesize($tmpName) === false
            ) {

                $errors[] =
                    "The uploaded logo is not a valid image.";
            }


            if (empty($errors)) {

                $uploadDirectory =
                    __DIR__
                    . '/../uploads/brands/';


                if (
                    !is_dir(
                        $uploadDirectory
                    )
                ) {

                    mkdir(
                        $uploadDirectory,
                        0755,
                        true
                    );
                }


                /*
                 * Create a unique filename.
                 */

                $logoName =
                    time()
                    . '_'
                    . bin2hex(
                        random_bytes(5)
                    )
                    . '.'
                    . $extension;


                $destination =
                    $uploadDirectory
                    . $logoName;


                if (
                    !move_uploaded_file(
                        $tmpName,
                        $destination
                    )
                ) {

                    $errors[] =
                        "Failed to save the uploaded logo.";

                    $logoName = null;
                }
            }
        }
    }


    /* =====================================================
       INSERT BRAND
    ===================================================== */

    if (empty($errors)) {

        $stmt = $conn->prepare("
            INSERT INTO brands
            (
                name,
                slug,
                description,
                logo,
                website,
                status,
                created_at,
                updated_at
            )
            VALUES
            (
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                NOW(),
                NOW()
            )
        ");


        if (!$stmt) {

            $errors[] =
                "Database error: "
                . $conn->error;
        } else {

            $stmt->bind_param(
                "sssssi",
                $name,
                $slug,
                $description,
                $logoName,
                $website,
                $status
            );


            if ($stmt->execute()) {

                $stmt->close();

                header(
                    "Location: brands.php?success=added"
                );

                exit;
            } else {

                /*
                 * If database insertion fails
                 * after uploading the logo,
                 * remove the uploaded file.
                 */

                if (
                    $logoName !== null
                ) {

                    $uploadedFile =
                        __DIR__
                        . '/../uploads/brands/'
                        . $logoName;

                    if (
                        file_exists(
                            $uploadedFile
                        )
                    ) {

                        unlink(
                            $uploadedFile
                        );
                    }
                }


                $errors[] =
                    "Failed to add brand: "
                    . $stmt->error;

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
        Add Brand | CORE LTD Admin
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

            color: white;

            display: flex;

            align-items: center;
            justify-content: center;

            font-weight: 700;
        }


        /* =====================================================
           FORM CARD
        ===================================================== */

        .form-card {

            background: white;

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

            background:
                linear-gradient(135deg,
                    #ffffff,
                    #f7fbff);
        }


        .form-header h3 {

            margin: 0;

            color: #17365d;

            font-size: 18px;

            font-weight: 750;
        }


        .form-header p {

            margin:
                5px 0 0;

            color: #8493a3;

            font-size: 12px;
        }


        .form-body {

            padding: 28px 25px;
        }


        /* =====================================================
           FORM GROUP
        ===================================================== */

        .form-group {

            margin-bottom: 21px;
        }


        .form-label {

            display: block;

            color: #314b67;

            font-size: 12px;

            font-weight: 700;

            margin-bottom: 7px;
        }


        .required {

            color: #dc3545;
        }


        .form-control-custom {

            width: 100%;

            border:
                1px solid #dce5ee;

            border-radius: 8px;

            padding:
                11px 13px;

            font-size: 13px;

            color: #34495e;

            outline: none;

            transition: .2s;

            background: white;
        }


        .form-control-custom:focus {

            border-color: #0d6efd;

            box-shadow:
                0 0 0 3px rgba(13, 110, 253, .08);
        }


        textarea.form-control-custom {

            min-height: 120px;

            resize: vertical;
        }


        .form-help {

            display: block;

            color: #94a1af;

            font-size: 10px;

            margin-top: 5px;
        }


        /* =====================================================
           TWO COLUMNS
        ===================================================== */

        .form-grid {

            display: grid;

            grid-template-columns:
                1fr 1fr;

            gap: 20px;
        }


        /* =====================================================
           LOGO UPLOAD
        ===================================================== */

        .upload-area {

            border:
                2px dashed #d8e4f0;

            border-radius: 11px;

            padding: 25px;

            text-align: center;

            background: #fafcff;

            cursor: pointer;

            transition: .2s;
        }


        .upload-area:hover {

            border-color: #0d6efd;

            background: #f4f9ff;
        }


        .upload-icon {

            width: 50px;
            height: 50px;

            margin:
                0 auto 10px;

            border-radius: 12px;

            background: #e8f2ff;

            color: #0d6efd;

            display: flex;

            align-items: center;
            justify-content: center;

            font-size: 20px;
        }


        .upload-area strong {

            display: block;

            color: #314b67;

            font-size: 12px;

            margin-bottom: 4px;
        }


        .upload-area span {

            color: #94a1af;

            font-size: 10px;
        }


        #logo {

            display: none;
        }


        .logo-preview {

            display: none;

            margin-top: 15px;

            text-align: center;
        }


        .logo-preview img {

            width: 100px;

            height: 100px;

            object-fit: contain;

            border:
                1px solid #dce5ee;

            border-radius: 10px;

            background: white;

            padding: 8px;
        }


        /* =====================================================
           STATUS
        ===================================================== */

        .status-box {

            display: flex;

            align-items: center;

            gap: 12px;

            padding:
                13px 15px;

            background: #f7faff;

            border:
                1px solid #e3edf8;

            border-radius: 9px;
        }


        .switch {

            position: relative;

            width: 42px;
            height: 23px;

            flex-shrink: 0;
        }


        .switch input {

            display: none;
        }


        .slider {

            position: absolute;

            inset: 0;

            background: #ccd6e0;

            border-radius: 30px;

            cursor: pointer;

            transition: .25s;
        }


        .slider:before {

            content: "";

            position: absolute;

            width: 17px;
            height: 17px;

            left: 3px;
            top: 3px;

            background: white;

            border-radius: 50%;

            transition: .25s;

            box-shadow:
                0 1px 3px rgba(0, 0, 0, .2);
        }


        .switch input:checked+.slider {

            background: #0d6efd;
        }


        .switch input:checked+.slider:before {

            transform:
                translateX(19px);
        }


        .status-text strong {

            display: block;

            color: #314b67;

            font-size: 12px;
        }


        .status-text span {

            color: #94a1af;

            font-size: 10px;
        }


        /* =====================================================
           ALERTS
        ===================================================== */

        .alert-custom {

            padding:
                13px 16px;

            border-radius: 9px;

            margin-bottom: 20px;

            font-size: 12px;
        }


        .alert-error {

            background: #fff1f2;

            border:
                1px solid #ffd5d9;

            color: #b4232f;
        }


        .alert-error ul {

            margin:
                5px 0 0 18px;

            padding: 0;
        }


        /* =====================================================
           BUTTONS
        ===================================================== */

        .form-actions {

            display: flex;

            justify-content: space-between;

            align-items: center;

            padding-top: 10px;

            margin-top: 10px;

            border-top:
                1px solid #edf1f5;
        }


        .btn {

            border: none;

            border-radius: 8px;

            padding:
                11px 17px;

            font-size: 12px;

            font-weight: 700;

            text-decoration: none;

            display: inline-flex;

            align-items: center;

            gap: 7px;

            cursor: pointer;
        }


        .btn-primary-custom {

            background:
                linear-gradient(135deg,
                    #0d6efd,
                    #0754b8);

            color: white;
        }


        .btn-primary-custom:hover {

            color: white;

            transform:
                translateY(-1px);
        }


        .btn-secondary-custom {

            background: #eef2f6;

            color: #52677d;
        }


        .btn-secondary-custom:hover {

            color: #314b67;

            background: #e2e8ee;
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


        @media(max-width:650px) {

            .topbar {

                flex-direction: column;

                align-items: flex-start;

                gap: 15px;
            }


            .form-grid {

                grid-template-columns: 1fr;
            }


            .form-body {

                padding: 20px 17px;
            }


            .form-actions {

                flex-direction: column-reverse;

                align-items: stretch;

                gap: 10px;
            }


            .form-actions .btn {

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
                    Add Brand
                </h1>


                <p class="page-subtitle">

                    Add a new manufacturer or product
                    brand to the CORE LTD catalogue.

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
         FORM
    ===================================================== -->

        <div class="form-card">


            <div class="form-header">

                <h3>

                    <i class="fa-solid fa-tag" style="
                        color:#0d6efd;
                        margin-right:7px;
                    "></i>

                    Brand Information

                </h3>


                <p>

                    Enter the basic details of the manufacturer.

                </p>

            </div>



            <div class="form-body">


                <!-- ERRORS -->

                <?php if (!empty($errors)): ?>

                    <div class="
                        alert-custom
                        alert-error
                    ">

                        <strong>

                            <i class="fa-solid
                                   fa-circle-exclamation"></i>

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



                <form method="POST" enctype="multipart/form-data">


                    <!-- =================================================
                     NAME + SLUG
                ================================================= -->

                    <div class="form-grid">


                        <div class="form-group">

                            <label class="form-label">

                                Brand Name

                                <span class="required">
                                    *
                                </span>

                            </label>


                            <input type="text" name="name" id="name" class="form-control-custom"
                                placeholder="e.g. Tenda" value="<?= htmlspecialchars(
                                                                    $_POST['name'] ?? ''
                                                                ) ?>" required>


                            <small class="form-help">

                                The official name of the manufacturer.

                            </small>

                        </div>



                        <div class="form-group">

                            <label class="form-label">

                                Slug

                            </label>


                            <input type="text" name="slug" id="slug" class="form-control-custom" placeholder="tenda"
                                value="<?= htmlspecialchars(
                                            $_POST['slug'] ?? ''
                                        ) ?>">


                            <small class="form-help">

                                Automatically generated from the brand
                                name. You can edit it if necessary.

                            </small>

                        </div>


                    </div>



                    <!-- =================================================
                     WEBSITE
                ================================================= -->

                    <div class="form-group">

                        <label class="form-label">

                            Official Website

                        </label>


                        <input type="url" name="website" class="form-control-custom"
                            placeholder="https://www.example.com" value="<?= htmlspecialchars(
                                                                                $_POST['website'] ?? ''
                                                                            ) ?>">


                        <small class="form-help">

                            Optional. Add the manufacturer's official
                            website.

                        </small>

                    </div>



                    <!-- =================================================
                     DESCRIPTION
                ================================================= -->

                    <div class="form-group">

                        <label class="form-label">

                            Description

                        </label>


                        <textarea name="description" class="form-control-custom"
                            placeholder="Brief description about this brand..."><?= htmlspecialchars(
                                                                                    $_POST['description'] ?? ''
                                                                                ) ?></textarea>


                        <small class="form-help">

                            Optional. This can later be displayed
                            on the website brand page.

                        </small>

                    </div>



                    <!-- =================================================
                     LOGO + STATUS
                ================================================= -->

                    <div class="form-grid">


                        <!-- LOGO -->

                        <div class="form-group">

                            <label class="form-label">

                                Brand Logo

                            </label>


                            <label for="logo" class="upload-area">

                                <div class="upload-icon">

                                    <i class="fa-solid fa-cloud-arrow-up"></i>

                                </div>


                                <strong>

                                    Click to upload logo

                                </strong>


                                <span>

                                    JPG, PNG or WEBP · Maximum 2MB

                                </span>


                            </label>


                            <input type="file" name="logo" id="logo" accept=".jpg,.jpeg,.png,.webp">


                            <div class="logo-preview" id="logoPreview">

                                <img id="previewImage" src="" alt="Logo Preview">

                            </div>

                        </div>



                        <!-- STATUS -->

                        <div class="form-group">

                            <label class="form-label">

                                Brand Status

                            </label>


                            <div class="status-box">


                                <label class="switch">

                                    <input type="checkbox" name="status" id="status" value="1" <?= (
                                                                                                    !isset($_POST['status'])
                                                                                                    ||
                                                                                                    (int)$_POST['status'] === 1
                                                                                                )
                                                                                                    ? 'checked'
                                                                                                    : ''
                                                                                                ?>>


                                    <span class="slider"></span>

                                </label>


                                <div class="status-text">

                                    <strong id="statusLabel">

                                        Active

                                    </strong>


                                    <span>

                                        Active brands can be assigned
                                        to products.

                                    </span>

                                </div>


                            </div>

                        </div>


                    </div>



                    <!-- =================================================
                     ACTIONS
                ================================================= -->

                    <div class="form-actions">


                        <a href="brands.php" class="
                            btn
                            btn-secondary-custom
                        ">

                            <i class="fa-solid fa-arrow-left"></i>

                            Back to Brands

                        </a>


                        <button type="submit" class="
                            btn
                            btn-primary-custom
                        ">

                            <i class="fa-solid fa-plus"></i>

                            Add Brand

                        </button>


                    </div>


                </form>


            </div>


        </div>


    </main>



    <script>
        /* =========================================================
   AUTO SLUG
========================================================= */

        const nameInput =
            document.getElementById('name');

        const slugInput =
            document.getElementById('slug');


        let slugManuallyChanged = false;


        slugInput.addEventListener(
            'input',
            function() {

                slugManuallyChanged = true;

            }
        );


        nameInput.addEventListener(
            'input',
            function() {

                if (slugManuallyChanged) {
                    return;
                }


                let slug =
                    this.value
                    .toLowerCase()
                    .trim()
                    .replace(
                        /[^a-z0-9]+/g,
                        '-'
                    )
                    .replace(
                        /^-+|-+$/g,
                        ''
                    );


                slugInput.value = slug;

            }
        );


        /* =========================================================
           LOGO PREVIEW
        ========================================================= */

        const logoInput =
            document.getElementById('logo');

        const logoPreview =
            document.getElementById('logoPreview');

        const previewImage =
            document.getElementById('previewImage');


        logoInput.addEventListener(
            'change',
            function() {

                const file =
                    this.files[0];


                if (!file) {

                    logoPreview.style.display =
                        'none';

                    return;

                }


                const reader =
                    new FileReader();


                reader.onload =
                    function(event) {

                        previewImage.src =
                            event.target.result;

                        logoPreview.style.display =
                            'block';

                    };


                reader.readAsDataURL(file);

            }
        );


        /* =========================================================
           STATUS LABEL
        ========================================================= */

        const statusInput =
            document.getElementById('status');

        const statusLabel =
            document.getElementById('statusLabel');


        statusInput.addEventListener(
            'change',
            function() {

                if (this.checked) {

                    statusLabel.textContent =
                        'Active';

                } else {

                    statusLabel.textContent =
                        'Inactive';

                }

            }
        );
    </script>


</body>

</html>