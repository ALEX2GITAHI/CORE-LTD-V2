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
   GENERATE SLUG
========================================================= */

function generateSlug($text)
{
    $text = strtolower(trim($text));

    $text = preg_replace('/[^a-z0-9]+/', '-', $text);

    return trim($text, '-');
}


/* =========================================================
   GET BRAND
========================================================= */

$stmt = $conn->prepare("
    SELECT
        id,
        name,
        slug,
        description,
        logo,
        website,
        status,
        created_at,
        updated_at
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
   DEFAULT FORM VALUES
========================================================= */

$name = $brand['name'];
$slug = $brand['slug'];
$description = $brand['description'] ?? '';
$website = $brand['website'] ?? '';
$status = (int) $brand['status'];
$currentLogo = $brand['logo'];

$errors = [];


/* =========================================================
   FORM SUBMISSION
========================================================= */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name'] ?? '');

    $slug = trim($_POST['slug'] ?? '');

    $description = trim(
        $_POST['description'] ?? ''
    );

    $website = trim(
        $_POST['website'] ?? ''
    );

    $status = isset($_POST['status']) ? 1 : 0;


    /* =====================================================
       VALIDATION
    ===================================================== */

    if ($name === '') {

        $errors[] =
            "Brand name is required.";
    }


    if ($slug === '' && $name !== '') {

        $slug = generateSlug($name);
    }


    if ($slug === '') {

        $errors[] =
            "Slug could not be generated.";
    }


    /* =====================================================
       CHECK DUPLICATE NAME
       EXCLUDE CURRENT BRAND
    ===================================================== */

    if (empty($errors)) {

        $check = $conn->prepare("
            SELECT id
            FROM brands
            WHERE name = ?
            AND id != ?
            LIMIT 1
        ");

        if (!$check) {

            $errors[] =
                "Database error: "
                . $conn->error;
        } else {

            $check->bind_param(
                "si",
                $name,
                $id
            );

            $check->execute();

            $checkResult =
                $check->get_result();

            if (
                $checkResult->num_rows > 0
            ) {

                $errors[] =
                    "Another brand with this name already exists.";
            }

            $check->close();
        }
    }


    /* =====================================================
       CHECK DUPLICATE SLUG
       EXCLUDE CURRENT BRAND
    ===================================================== */

    if (empty($errors)) {

        $checkSlug = $conn->prepare("
            SELECT id
            FROM brands
            WHERE slug = ?
            AND id != ?
            LIMIT 1
        ");

        if (!$checkSlug) {

            $errors[] =
                "Database error: "
                . $conn->error;
        } else {

            $checkSlug->bind_param(
                "si",
                $slug,
                $id
            );

            $checkSlug->execute();

            $slugResult =
                $checkSlug->get_result();

            if (
                $slugResult->num_rows > 0
            ) {

                $errors[] =
                    "Another brand is already using this slug.";
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
       NEW LOGO
    ===================================================== */

    $newLogo = null;

    $oldLogoToDelete = null;


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

            if (
                $fileSize >
                2 * 1024 * 1024
            ) {

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


            /* Verify actual image */

            if (
                empty($errors) &&
                @getimagesize($tmpName) === false
            ) {

                $errors[] =
                    "The uploaded logo is not a valid image.";
            }


            /* =================================================
               SAVE NEW LOGO
            ================================================= */

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


                $newLogo =
                    time()
                    . '_'
                    . bin2hex(
                        random_bytes(5)
                    )
                    . '.'
                    . $extension;


                $destination =
                    $uploadDirectory
                    . $newLogo;


                if (
                    !move_uploaded_file(
                        $tmpName,
                        $destination
                    )
                ) {

                    $errors[] =
                        "Failed to save the new logo.";

                    $newLogo = null;
                } else {

                    /*
                     * Delete old logo only AFTER
                     * the database update succeeds.
                     */

                    $oldLogoToDelete =
                        $currentLogo;
                }
            }
        }
    }


    /* =====================================================
       DETERMINE LOGO TO SAVE
    ===================================================== */

    $logoToSave =
        $currentLogo;


    if ($newLogo !== null) {

        $logoToSave =
            $newLogo;
    }


    /* =====================================================
       UPDATE BRAND
    ===================================================== */

    if (empty($errors)) {

        $update = $conn->prepare("
            UPDATE brands
            SET
                name = ?,
                slug = ?,
                description = ?,
                logo = ?,
                website = ?,
                status = ?,
                updated_at = NOW()
            WHERE id = ?
        ");


        if (!$update) {

            $errors[] =
                "Database error: "
                . $conn->error;
        } else {

            $update->bind_param(
                "sssssii",
                $name,
                $slug,
                $description,
                $logoToSave,
                $website,
                $status,
                $id
            );


            if ($update->execute()) {

                $update->close();


                /* =================================================
                   DELETE OLD LOGO
                ================================================= */

                if (
                    $newLogo !== null &&
                    $oldLogoToDelete !== null &&
                    $oldLogoToDelete !== ''
                ) {

                    $oldFile =
                        __DIR__
                        . '/../uploads/brands/'
                        . $oldLogoToDelete;


                    if (
                        file_exists($oldFile)
                    ) {

                        unlink($oldFile);
                    }
                }


                header(
                    "Location: brands.php?success=updated"
                );

                exit;
            } else {

                /*
                 * If database update failed,
                 * keep the old logo and remove
                 * the newly uploaded logo.
                 */

                if (
                    $newLogo !== null
                ) {

                    $newFile =
                        __DIR__
                        . '/../uploads/brands/'
                        . $newLogo;


                    if (
                        file_exists($newFile)
                    ) {

                        unlink($newFile);
                    }
                }


                $errors[] =
                    "Failed to update brand: "
                    . $update->error;

                $update->close();
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
        Edit Brand | CORE LTD Admin
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
           CARD
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
           FORM
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


    .form-grid {

        display: grid;

        grid-template-columns:
            1fr 1fr;

        gap: 20px;
    }


    /* =====================================================
           LOGO
        ===================================================== */

    .logo-section {

        display: flex;

        gap: 20px;

        align-items: center;

        padding: 18px;

        background: #f8fbff;

        border:
            1px solid #e3edf8;

        border-radius: 11px;
    }


    .current-logo {

        width: 105px;
        height: 105px;

        flex-shrink: 0;

        background: white;

        border:
            1px solid #dce5ee;

        border-radius: 10px;

        padding: 8px;

        display: flex;

        align-items: center;
        justify-content: center;
    }


    .current-logo img {

        max-width: 100%;

        max-height: 100%;

        object-fit: contain;
    }


    .no-logo {

        color: #9aa8b6;

        text-align: center;

        font-size: 11px;
    }


    .upload-area {

        flex: 1;

        border:
            2px dashed #d8e4f0;

        border-radius: 10px;

        padding: 18px;

        text-align: center;

        cursor: pointer;

        transition: .2s;

        background: white;
    }


    .upload-area:hover {

        border-color: #0d6efd;

        background: #f4f9ff;
    }


    .upload-icon {

        color: #0d6efd;

        font-size: 20px;

        margin-bottom: 7px;
    }


    .upload-area strong {

        display: block;

        color: #314b67;

        font-size: 12px;
    }


    .upload-area span {

        color: #94a1af;

        font-size: 10px;
    }


    #logo {

        display: none;
    }


    .new-preview {

        display: none;

        margin-top: 12px;

        text-align: center;
    }


    .new-preview img {

        width: 75px;

        height: 75px;

        object-fit: contain;

        border:
            1px solid #dce5ee;

        border-radius: 8px;

        background: white;

        padding: 5px;
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
           ALERT
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


        .logo-section {

            flex-direction: column;
        }


        .upload-area {

            width: 100%;
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
                    Edit Brand
                </h1>


                <p class="page-subtitle">

                    Update brand information and settings.

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



        <div class="form-card">


            <div class="form-header">

                <h3>

                    <i class="fa-solid fa-pen-to-square" style="
                        color:#0d6efd;
                        margin-right:7px;
                    "></i>

                    <?= htmlspecialchars($brand['name']) ?>

                </h3>


                <p>

                    Update the information for this brand.

                </p>

            </div>



            <div class="form-body">


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


                    <!-- NAME + SLUG -->

                    <div class="form-grid">


                        <div class="form-group">

                            <label class="form-label">

                                Brand Name
                                <span class="required">*</span>

                            </label>


                            <input type="text" name="name" id="name" class="form-control-custom"
                                value="<?= htmlspecialchars($name) ?>" required>

                        </div>



                        <div class="form-group">

                            <label class="form-label">

                                Slug

                            </label>


                            <input type="text" name="slug" id="slug" class="form-control-custom"
                                value="<?= htmlspecialchars($slug) ?>">


                            <small class="form-help">

                                Used in website URLs.

                            </small>

                        </div>


                    </div>



                    <!-- WEBSITE -->

                    <div class="form-group">

                        <label class="form-label">

                            Official Website

                        </label>


                        <input type="url" name="website" class="form-control-custom"
                            placeholder="https://www.example.com" value="<?= htmlspecialchars($website) ?>">

                    </div>



                    <!-- DESCRIPTION -->

                    <div class="form-group">

                        <label class="form-label">

                            Description

                        </label>


                        <textarea name="description"
                            class="form-control-custom"><?= htmlspecialchars($description) ?></textarea>

                    </div>



                    <!-- LOGO -->

                    <div class="form-group">

                        <label class="form-label">

                            Brand Logo

                        </label>


                        <div class="logo-section">


                            <div class="current-logo">

                                <?php if (!empty($currentLogo)): ?>

                                <img src="../uploads/brands/<?= htmlspecialchars($currentLogo) ?>"
                                    alt="<?= htmlspecialchars($name) ?>">

                                <?php else: ?>

                                <div class="no-logo">

                                    <i class="fa-solid fa-image" style="
                                            font-size:25px;
                                            display:block;
                                            margin-bottom:5px;
                                        "></i>

                                    No Logo

                                </div>

                                <?php endif; ?>

                            </div>



                            <label for="logo" class="upload-area">

                                <div class="upload-icon">

                                    <i class="fa-solid
                                           fa-cloud-arrow-up"></i>

                                </div>


                                <strong>

                                    Click to replace logo

                                </strong>


                                <span>

                                    Leave empty to keep the current logo.

                                </span>


                                <div class="new-preview" id="newPreview">

                                    <img id="previewImage" src="" alt="New Logo">

                                </div>

                            </label>


                        </div>


                        <input type="file" name="logo" id="logo" accept=".jpg,.jpeg,.png,.webp">


                        <small class="form-help">

                            JPG, JPEG, PNG or WEBP · Maximum 2MB

                        </small>

                    </div>



                    <!-- STATUS -->

                    <div class="form-group">

                        <label class="form-label">

                            Brand Status

                        </label>


                        <div class="status-box">


                            <label class="switch">

                                <input type="checkbox" name="status" id="status" value="1" <?= $status === 1
                                                                                                ? 'checked'
                                                                                                : ''
                                                                                            ?>>


                                <span class="slider"></span>

                            </label>


                            <div class="status-text">

                                <strong id="statusLabel">

                                    <?= $status === 1
                                        ? 'Active'
                                        : 'Inactive'
                                    ?>

                                </strong>


                                <span>

                                    Active brands can be assigned
                                    to products.

                                </span>

                            </div>


                        </div>

                    </div>



                    <!-- ACTIONS -->

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

                            <i class="fa-solid fa-floppy-disk"></i>

                            Save Changes

                        </button>


                    </div>


                </form>


            </div>


        </div>


    </main>



    <script>
    /* =========================================================
   LOGO PREVIEW
========================================================= */

    const logoInput =
        document.getElementById('logo');

    const newPreview =
        document.getElementById('newPreview');

    const previewImage =
        document.getElementById('previewImage');


    logoInput.addEventListener(
        'change',
        function() {

            const file =
                this.files[0];


            if (!file) {

                newPreview.style.display =
                    'none';

                return;

            }


            const reader =
                new FileReader();


            reader.onload =
                function(event) {

                    previewImage.src =
                        event.target.result;

                    newPreview.style.display =
                        'block';

                };


            reader.readAsDataURL(file);

        }
    );


    /* =========================================================
       STATUS
    ========================================================= */

    const statusInput =
        document.getElementById('status');

    const statusLabel =
        document.getElementById('statusLabel');


    statusInput.addEventListener(
        'change',
        function() {

            statusLabel.textContent =
                this.checked ?
                'Active' :
                'Inactive';

        }
    );
    </script>


</body>

</html>