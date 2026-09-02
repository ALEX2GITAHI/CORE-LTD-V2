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

$errors = [];


/*
|--------------------------------------------------------------------------
| LOAD CATEGORIES
|--------------------------------------------------------------------------
*/

$categories = [];

$categoryQuery = $conn->query("
    SELECT id, name
    FROM categories
    ORDER BY name ASC
");

if ($categoryQuery) {

    while ($row = $categoryQuery->fetch_assoc()) {
        $categories[] = $row;
    }
}


/*
|--------------------------------------------------------------------------
| LOAD BRANDS
|--------------------------------------------------------------------------
*/

$brands = [];

$brandQuery = $conn->query("
    SELECT id, name
    FROM brands
    ORDER BY name ASC
");

if ($brandQuery) {

    while ($row = $brandQuery->fetch_assoc()) {
        $brands[] = $row;
    }
}


/*
|--------------------------------------------------------------------------
| CREATE SLUG
|--------------------------------------------------------------------------
*/

function createUniqueSlug($conn, $name)
{
    $slug = strtolower(
        trim(
            preg_replace(
                '/[^A-Za-z0-9-]+/',
                '-',
                $name
            ),
            '-'
        )
    );

    if ($slug === '') {
        $slug = 'product';
    }

    $originalSlug = $slug;
    $counter = 2;

    while (true) {

        $stmt = $conn->prepare("
            SELECT id
            FROM products
            WHERE slug = ?
            LIMIT 1
        ");

        $stmt->bind_param("s", $slug);

        $stmt->execute();

        $result = $stmt->get_result();

        $exists = $result->num_rows > 0;

        $stmt->close();

        if (!$exists) {
            break;
        }

        $slug = $originalSlug . '-' . $counter;

        $counter++;
    }

    return $slug;
}


/*
|--------------------------------------------------------------------------
| SAFE IMAGE NAME
|--------------------------------------------------------------------------
*/

function createSafeImageName($originalName)
{
    $extension = strtolower(
        pathinfo(
            $originalName,
            PATHINFO_EXTENSION
        )
    );

    return time() .
        '_' .
        bin2hex(random_bytes(6)) .
        '.' .
        $extension;
}


/*
|--------------------------------------------------------------------------
| FORM SUBMISSION
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    /*
    |--------------------------------------------------------------------------
    | PRODUCT INFORMATION
    |--------------------------------------------------------------------------
    */

    $name = trim(
        $_POST['name'] ?? ''
    );

    $sku = trim(
        $_POST['sku'] ?? ''
    );

    $categoryId = (int)(
        $_POST['category_id'] ?? 0
    );

    $brandId = (int)(
        $_POST['brand_id'] ?? 0
    );

    $shortDescription = trim(
        $_POST['short_description'] ?? ''
    );

    $description = trim(
        $_POST['description'] ?? ''
    );


    /*
    |--------------------------------------------------------------------------
    | PRICING
    |--------------------------------------------------------------------------
    */

    $price = (float)(
        $_POST['price'] ?? 0
    );

    $oldPrice =
        trim($_POST['old_price'] ?? '') !== ''
        ? (float)$_POST['old_price']
        : null;

    $costPrice =
        trim($_POST['cost_price'] ?? '') !== ''
        ? (float)$_POST['cost_price']
        : null;


    /*
    |--------------------------------------------------------------------------
    | INVENTORY
    |--------------------------------------------------------------------------
    */

    $stockQuantity = (int)(
        $_POST['stock_quantity'] ?? 0
    );

    $lowStockLevel = (int)(
        $_POST['low_stock_level'] ?? 5
    );


    /*
    |--------------------------------------------------------------------------
    | SETTINGS
    |--------------------------------------------------------------------------
    */

    $featured = isset(
        $_POST['featured']
    ) ? 1 : 0;

    /*
    | Status defaults to active.
    */

    $status = isset(
        $_POST['status']
    ) ? 1 : 0;


    /*
    |--------------------------------------------------------------------------
    | SEO
    |--------------------------------------------------------------------------
    */

    $metaTitle = trim(
        $_POST['meta_title'] ?? ''
    );

    $metaDescription = trim(
        $_POST['meta_description'] ?? ''
    );

    $metaKeywords = trim(
        $_POST['meta_keywords'] ?? ''
    );


    /*
    |--------------------------------------------------------------------------
    | VALIDATION
    |--------------------------------------------------------------------------
    */

    if ($name === '') {

        $errors[] =
            "Product name is required.";
    }


    if ($categoryId <= 0) {

        $errors[] =
            "Please select a category.";
    }


    if ($brandId <= 0) {

        $errors[] =
            "Please select a brand.";
    }


    if ($price <= 0) {

        $errors[] =
            "Selling price must be greater than zero.";
    }


    if ($stockQuantity < 0) {

        $errors[] =
            "Stock quantity cannot be negative.";
    }


    if ($lowStockLevel < 0) {

        $errors[] =
            "Low stock level cannot be negative.";
    }


    /*
    |--------------------------------------------------------------------------
    | CHECK SKU
    |--------------------------------------------------------------------------
    */

    if ($sku !== '') {

        $skuCheck = $conn->prepare("
            SELECT id
            FROM products
            WHERE sku = ?
            LIMIT 1
        ");

        $skuCheck->bind_param(
            "s",
            $sku
        );

        $skuCheck->execute();

        $skuResult =
            $skuCheck->get_result();

        if ($skuResult->num_rows > 0) {

            $errors[] =
                "The SKU already exists.";
        }

        $skuCheck->close();
    }


    /*
    |--------------------------------------------------------------------------
    | IMAGE SETTINGS
    |--------------------------------------------------------------------------
    */

    $allowedExtensions = [
        'jpg',
        'jpeg',
        'png',
        'webp'
    ];

    $maxFileSize =
        5 * 1024 * 1024;


    /*
    |--------------------------------------------------------------------------
    | MAIN IMAGE VALIDATION
    |--------------------------------------------------------------------------
    */

    if (
        isset($_FILES['main_image'])
        &&
        $_FILES['main_image']['error']
        !== UPLOAD_ERR_NO_FILE
    ) {

        if (
            $_FILES['main_image']['error']
            !== UPLOAD_ERR_OK
        ) {

            $errors[] =
                "There was a problem uploading the main image.";
        } else {

            $extension = strtolower(
                pathinfo(
                    $_FILES['main_image']['name'],
                    PATHINFO_EXTENSION
                )
            );

            if (
                !in_array(
                    $extension,
                    $allowedExtensions,
                    true
                )
            ) {

                $errors[] =
                    "Main image must be JPG, JPEG, PNG or WEBP.";
            }

            if (
                $_FILES['main_image']['size']
                >
                $maxFileSize
            ) {

                $errors[] =
                    "Main image must not exceed 5MB.";
            }
        }
    }


    /*
    |--------------------------------------------------------------------------
    | ADDITIONAL IMAGE VALIDATION
    |--------------------------------------------------------------------------
    */

    if (
        isset($_FILES['additional_images'])
        &&
        isset($_FILES['additional_images']['name'])
    ) {

        $additionalCount =
            count(
                array_filter(
                    $_FILES['additional_images']['name']
                )
            );


        if ($additionalCount > 10) {

            $errors[] =
                "You can upload a maximum of 10 additional images.";
        }


        foreach (
            $_FILES['additional_images']['name']
            as $key => $originalName
        ) {

            if (
                $originalName === ''
            ) {
                continue;
            }


            $uploadError =
                $_FILES['additional_images']['error'][$key];


            if (
                $uploadError
                !== UPLOAD_ERR_OK
            ) {

                $errors[] =
                    "One of the additional images could not be uploaded.";

                continue;
            }


            $extension = strtolower(
                pathinfo(
                    $originalName,
                    PATHINFO_EXTENSION
                )
            );


            if (
                !in_array(
                    $extension,
                    $allowedExtensions,
                    true
                )
            ) {

                $errors[] =
                    "Additional images must be JPG, JPEG, PNG or WEBP.";
            }


            if (
                $_FILES['additional_images']['size'][$key]
                >
                $maxFileSize
            ) {

                $errors[] =
                    "Each additional image must not exceed 5MB.";
            }
        }
    }


    /*
    |--------------------------------------------------------------------------
    | PREPARE UPLOAD DIRECTORY
    |--------------------------------------------------------------------------
    */

    $uploadDirectory =
        __DIR__ .
        '/../uploads/products/';


    /*
    |--------------------------------------------------------------------------
    | SAVE PRODUCT
    |--------------------------------------------------------------------------
    */

    if (empty($errors)) {


        /*
        |--------------------------------------------------------------------------
        | CREATE UPLOAD DIRECTORY
        |--------------------------------------------------------------------------
        */

        if (!is_dir($uploadDirectory)) {

            if (
                !mkdir(
                    $uploadDirectory,
                    0755,
                    true
                )
            ) {

                $errors[] =
                    "Unable to create product upload directory.";
            }
        }
    }


    /*
    |--------------------------------------------------------------------------
    | START DATABASE TRANSACTION
    |--------------------------------------------------------------------------
    */

    if (empty($errors)) {

        $conn->begin_transaction();

        $uploadedFiles = [];

        try {


            /*
            |--------------------------------------------------------------------------
            | CREATE SLUG
            |--------------------------------------------------------------------------
            */

            $slug =
                createUniqueSlug(
                    $conn,
                    $name
                );


            /*
            |--------------------------------------------------------------------------
            | MAIN IMAGE
            |--------------------------------------------------------------------------
            */

            $mainImageName = '';


            if (
                isset($_FILES['main_image'])
                &&
                $_FILES['main_image']['error']
                === UPLOAD_ERR_OK
            ) {

                $mainImageName =
                    createSafeImageName(
                        $_FILES['main_image']['name']
                    );


                $mainImageDestination =
                    $uploadDirectory .
                    $mainImageName;


                if (
                    !move_uploaded_file(
                        $_FILES['main_image']['tmp_name'],
                        $mainImageDestination
                    )
                ) {

                    throw new Exception(
                        "Unable to save the main product image."
                    );
                }


                $uploadedFiles[] =
                    $mainImageDestination;
            }


            /*
            |--------------------------------------------------------------------------
            | INSERT PRODUCT
            |--------------------------------------------------------------------------
            */

            $stmt = $conn->prepare("
                INSERT INTO products (
                    category_id,
                    brand_id,
                    name,
                    slug,
                    sku,
                    short_description,
                    description,
                    price,
                    old_price,
                    cost_price,
                    stock_quantity,
                    low_stock_level,
                    main_image,
                    featured,
                    status,
                    views,
                    meta_title,
                    meta_description,
                    meta_keywords,
                    created_at,
                    updated_at
                )
                VALUES (
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    0,
                    ?,
                    ?,
                    ?,
                    NOW(),
                    NOW()
                )
            ");


            if (!$stmt) {

                throw new Exception(
                    "Unable to prepare product database query."
                );
            }


            $stmt->bind_param(
                "iisssssdddiisiisss",
                $categoryId,
                $brandId,
                $name,
                $slug,
                $sku,
                $shortDescription,
                $description,
                $price,
                $oldPrice,
                $costPrice,
                $stockQuantity,
                $lowStockLevel,
                $mainImageName,
                $featured,
                $status,
                $metaTitle,
                $metaDescription,
                $metaKeywords
            );


            if (!$stmt->execute()) {

                throw new Exception(
                    "Unable to save product: "
                        . $stmt->error
                );
            }


            $productId =
                $stmt->insert_id;


            $stmt->close();


            /*
            |--------------------------------------------------------------------------
            | INSERT MAIN IMAGE INTO product_images
            |--------------------------------------------------------------------------
            */

            if ($mainImageName !== '') {


                $imageStmt = $conn->prepare("
                    INSERT INTO product_images (
                        product_id,
                        image,
                        alt_text,
                        sort_order,
                        is_primary,
                        created_at
                    )
                    VALUES (
                        ?,
                        ?,
                        ?,
                        ?,
                        ?,
                        NOW()
                    )
                ");


                if (!$imageStmt) {

                    throw new Exception(
                        "Unable to prepare main image query."
                    );
                }


                $mainAltText =
                    $name;


                $sortOrder = 1;

                $isPrimary = 1;


                $imageStmt->bind_param(
                    "issii",
                    $productId,
                    $mainImageName,
                    $mainAltText,
                    $sortOrder,
                    $isPrimary
                );


                if (!$imageStmt->execute()) {

                    throw new Exception(
                        "Unable to save main image."
                    );
                }


                $imageStmt->close();
            }


            /*
            |--------------------------------------------------------------------------
            | ADDITIONAL IMAGES
            |--------------------------------------------------------------------------
            */

            if (
                isset($_FILES['additional_images'])
                &&
                isset(
                    $_FILES['additional_images']['name']
                )
            ) {


                $imageStmt = $conn->prepare("
                    INSERT INTO product_images (
                        product_id,
                        image,
                        alt_text,
                        sort_order,
                        is_primary,
                        created_at
                    )
                    VALUES (
                        ?,
                        ?,
                        ?,
                        ?,
                        ?,
                        NOW()
                    )
                ");


                if (!$imageStmt) {

                    throw new Exception(
                        "Unable to prepare additional image query."
                    );
                }


                $sortOrder = 2;

                $isPrimary = 0;


                foreach (
                    $_FILES['additional_images']['name']
                    as $key => $originalName
                ) {


                    if (
                        $originalName === ''
                    ) {
                        continue;
                    }


                    $imageName =
                        createSafeImageName(
                            $originalName
                        );


                    $destination =
                        $uploadDirectory .
                        $imageName;


                    if (
                        !move_uploaded_file(
                            $_FILES['additional_images']['tmp_name'][$key],
                            $destination
                        )
                    ) {

                        throw new Exception(
                            "Unable to save one of the additional images."
                        );
                    }


                    $uploadedFiles[] =
                        $destination;


                    /*
                    |--------------------------------------------------------------------------
                    | ALT TEXT
                    |--------------------------------------------------------------------------
                    */

                    $altText =
                        $name .
                        ' - Image ' .
                        $sortOrder;


                    $imageStmt->bind_param(
                        "issii",
                        $productId,
                        $imageName,
                        $altText,
                        $sortOrder,
                        $isPrimary
                    );


                    if (
                        !$imageStmt->execute()
                    ) {

                        throw new Exception(
                            "Unable to save additional product image."
                        );
                    }


                    $sortOrder++;
                }


                $imageStmt->close();
            }


            /*
            |--------------------------------------------------------------------------
            | PRODUCT FEATURES + BENEFITS
            |--------------------------------------------------------------------------
            */

            $features = $_POST['feature'] ?? [];
            $benefits = $_POST['benefit'] ?? [];

            if (is_array($features) && is_array($benefits)) {

                $featureStmt = $conn->prepare("
                    INSERT INTO product_features (
                        product_id,
                        feature,
                        benefit,
                        sort_order,
                        created_at
                    )
                    VALUES (?, ?, ?, ?, NOW())
                ");

                if (!$featureStmt) {
                    throw new Exception(
                        "Unable to prepare product features query: " . $conn->error
                    );
                }

                $featureSortOrder = 1;

                foreach ($features as $index => $featureValue) {

                    $feature = trim((string)$featureValue);
                    $benefit = trim((string)($benefits[$index] ?? ''));

                    if ($feature === '' && $benefit === '') {
                        continue;
                    }

                    if ($feature === '') {
                        throw new Exception(
                            "Feature row " . $featureSortOrder . " is missing the feature name."
                        );
                    }

                    if ($benefit === '') {
                        throw new Exception(
                            "Feature row " . $featureSortOrder . " is missing the benefit."
                        );
                    }

                    $featureStmt->bind_param(
                        "issi",
                        $productId,
                        $feature,
                        $benefit,
                        $featureSortOrder
                    );

                    if (!$featureStmt->execute()) {
                        throw new Exception(
                            "Unable to save product feature: " . $featureStmt->error
                        );
                    }

                    $featureSortOrder++;
                }

                $featureStmt->close();
            }


            /*
            |--------------------------------------------------------------------------
            | COMMIT
            |--------------------------------------------------------------------------
            */

            $conn->commit();


            /*
            |--------------------------------------------------------------------------
            | SUCCESS
            |--------------------------------------------------------------------------
            */

            header(
                "Location: products.php?added=1"
            );

            exit;
        } catch (Throwable $e) {


            /*
            |--------------------------------------------------------------------------
            | ROLLBACK
            |--------------------------------------------------------------------------
            */

            $conn->rollback();


            /*
            |--------------------------------------------------------------------------
            | DELETE UPLOADED FILES
            |--------------------------------------------------------------------------
            */

            foreach (
                $uploadedFiles
                as $uploadedFile
            ) {

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
                $e->getMessage();
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
        CORE LTD - Add Product
    </title>


    <style>
        /* =========================================================
   GLOBAL
========================================================= */

        * {
            box-sizing: border-box;
        }

        body {

            margin: 0;

            font-family:
                Arial,
                Helvetica,
                sans-serif;

            background: #f3f7fc;

            color: #1d344e;

        }


        /* =========================================================
   SIDEBAR
========================================================= */

        .sidebar {

            position: fixed;

            left: 0;
            top: 0;

            width: 250px;

            height: 100vh;

            background:
                linear-gradient(180deg,
                    #063f78 0%,
                    #075eaa 55%,
                    #0875d1 100%);

            color: #fff;

            padding: 22px 15px;

            box-shadow:
                4px 0 18px rgba(0, 61, 120, .15);

            z-index: 1000;

        }


        .sidebar-logo {

            text-align: center;

            padding:
                7px 10px 25px;

            border-bottom:
                1px solid rgba(255, 255, 255, .15);

        }


        .sidebar-logo h2 {

            margin: 0;

            font-size: 25px;

            letter-spacing: 1.5px;

            font-weight: 800;

        }


        .sidebar-logo p {

            margin:
                6px 0 0;

            color:
                rgba(255, 255, 255, .75);

            font-size: 11px;

            letter-spacing: 1px;

        }


        .sidebar-menu {

            margin-top: 20px;

        }


        .menu-title {

            color:
                rgba(255, 255, 255, .55);

            font-size: 10px;

            text-transform: uppercase;

            letter-spacing: 1.3px;

            margin:
                20px 12px 8px;

            font-weight: 700;

        }


        .sidebar-menu a {

            display: flex;

            align-items: center;

            gap: 12px;

            padding:
                12px 14px;

            margin-bottom: 4px;

            color:
                rgba(255, 255, 255, .84);

            text-decoration: none;

            border-radius: 8px;

            font-size: 13px;

            transition: .2s ease;

        }


        .sidebar-menu a:hover {

            background:
                rgba(255, 255, 255, .12);

            color: #fff;

            transform:
                translateX(2px);

        }


        .sidebar-menu a.active {

            background: #fff;

            color: #075eaa;

            font-weight: 700;

            box-shadow:
                0 5px 15px rgba(0, 0, 0, .12);

        }


        .sidebar-menu .icon {

            width: 23px;

            height: 23px;

            display: flex;

            align-items: center;

            justify-content: center;

            border-radius: 6px;

            background:
                rgba(255, 255, 255, .12);

            font-size: 13px;

        }


        .sidebar-menu a.active .icon {

            background:
                #e7f2ff;

        }


        /* =========================================================
   MAIN
========================================================= */

        .main {

            margin-left: 250px;

            min-height: 100vh;

        }


        /* =========================================================
   TOPBAR
========================================================= */

        .topbar {

            height: 72px;

            background: #fff;

            border-bottom:
                1px solid #e5edf6;

            display: flex;

            align-items: center;

            justify-content: space-between;

            padding:
                0 30px;

            position: sticky;

            top: 0;

            z-index: 500;

            box-shadow:
                0 2px 10px rgba(20, 80, 130, .04);

        }


        .page-title h1 {

            margin: 0;

            font-size: 21px;

            color: #17304e;

        }


        .page-title p {

            margin:
                4px 0 0;

            color: #8796a9;

            font-size: 12px;

        }


        .admin-user {

            display: flex;

            align-items: center;

            gap: 10px;

        }


        .admin-avatar {

            width: 40px;

            height: 40px;

            border-radius: 50%;

            background:
                linear-gradient(135deg,
                    #087ce9,
                    #064d93);

            color: #fff;

            display: flex;

            align-items: center;

            justify-content: center;

            font-weight: 800;

        }


        .admin-info strong {

            display: block;

            font-size: 12px;

            color: #20334b;

        }


        .admin-info span {

            color: #8997a8;

            font-size: 10px;

        }


        /* =========================================================
   CONTENT
========================================================= */

        .content {

            padding: 30px;

        }


        .content-header {

            display: flex;

            align-items: center;

            justify-content: space-between;

            margin-bottom: 22px;

        }


        .content-header h2 {

            margin: 0;

            font-size: 22px;

            color: #193452;

        }


        .content-header p {

            margin:
                5px 0 0;

            color: #8796a9;

            font-size: 12px;

        }


        .back-button {

            display: inline-flex;

            align-items: center;

            gap: 7px;

            padding:
                11px 17px;

            background: #fff;

            border:
                1px solid #dce6f0;

            color: #526b84;

            border-radius: 7px;

            text-decoration: none;

            font-size: 12px;

            font-weight: 700;

        }


        .back-button:hover {

            color: #0875df;

            border-color: #0875df;

        }


        /* =========================================================
   ALERTS
========================================================= */

        .alert {

            padding:
                13px 16px;

            border-radius: 8px;

            margin-bottom: 20px;

            font-size: 12px;

            font-weight: 600;

        }


        .alert-error {

            background: #fff0f0;

            border:
                1px solid #ffd1d1;

            color: #bd3038;

        }


        .alert-error ul {

            margin:
                7px 0 0 18px;

            padding: 0;

        }


        /* =========================================================
   FORM
========================================================= */

        .form-layout {

            display: grid;

            grid-template-columns:
                minmax(0, 1fr) 320px;

            gap: 20px;

        }


        .card {

            background: #fff;

            border:
                1px solid #e3ecf5;

            border-radius: 10px;

            box-shadow:
                0 4px 18px rgba(25, 76, 120, .05);

            margin-bottom: 20px;

            overflow: hidden;

        }


        .card-header {

            padding:
                17px 20px;

            border-bottom:
                1px solid #edf2f7;

            display: flex;

            align-items: center;

            gap: 10px;

        }


        .card-header-icon {

            width: 34px;

            height: 34px;

            border-radius: 7px;

            display: flex;

            align-items: center;

            justify-content: center;

            background:
                #eaf4ff;

            color:
                #0875df;

            font-size: 15px;

        }


        .card-header h3 {

            margin: 0;

            font-size: 14px;

            color: #24415e;

        }


        .card-header p {

            margin:
                3px 0 0;

            font-size: 10px;

            color: #8a98a8;

        }


        .card-body {

            padding: 20px;

        }


        .form-grid {

            display: grid;

            grid-template-columns:
                repeat(2, 1fr);

            gap: 17px;

        }


        .form-group {

            margin-bottom: 17px;

        }


        .form-grid .form-group {

            margin-bottom: 0;

        }


        .form-group.full {

            grid-column: 1 / -1;

        }


        .form-group label {

            display: block;

            margin-bottom: 7px;

            color: #526b84;

            font-size: 10px;

            font-weight: 700;

            text-transform: uppercase;

            letter-spacing: .3px;

        }


        .required {

            color: #e03942;

        }


        .form-group input,
        .form-group select,
        .form-group textarea {

            width: 100%;

            border:
                1px solid #dce6f0;

            border-radius: 7px;

            background:
                #fbfdff;

            color: #344b64;

            font-family: inherit;

            font-size: 12px;

            outline: none;

            transition: .2s ease;

        }


        .form-group input,
        .form-group select {

            height: 43px;

            padding:
                0 12px;

        }


        .form-group textarea {

            min-height: 110px;

            padding: 12px;

            resize: vertical;

        }


        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {

            border-color: #0875df;

            background: #fff;

            box-shadow:
                0 0 0 3px rgba(8, 117, 223, .08);

        }


        .form-help {

            margin-top: 5px;

            color: #91a0af;

            font-size: 9px;

        }


        /* =========================================================
   PRICE
========================================================= */

        .price-input {

            position: relative;

        }


        .price-prefix {

            position: absolute;

            left: 12px;

            top: 50%;

            transform:
                translateY(-50%);

            color: #0875df;

            font-size: 11px;

            font-weight: 700;

            pointer-events: none;

        }


        .price-input input {

            padding-left: 48px;

        }


        /* =========================================================
   IMAGE UPLOAD
========================================================= */

        .image-upload {

            border:
                2px dashed #d6e5f3;

            background:
                #f8fbfe;

            border-radius: 9px;

            padding: 25px 15px;

            text-align: center;

            cursor: pointer;

            transition: .2s ease;

            display: block;

        }


        .image-upload:hover {

            border-color: #0875df;

            background: #f3f9ff;

        }


        .image-upload-icon {

            width: 50px;

            height: 50px;

            margin:
                0 auto 10px;

            display: flex;

            align-items: center;

            justify-content: center;

            background: #eaf4ff;

            color: #0875df;

            border-radius: 50%;

            font-size: 21px;

        }


        .image-upload strong {

            display: block;

            color: #405b75;

            font-size: 12px;

        }


        .image-upload span {

            display: block;

            color: #91a0af;

            font-size: 10px;

            margin-top: 5px;

        }


        .image-upload input {

            display: none;

        }


        /* =========================================================
   IMAGE PREVIEW
========================================================= */

        .preview-section {

            margin-top: 18px;

        }


        .preview-title {

            font-size: 10px;

            font-weight: 700;

            text-transform: uppercase;

            color: #526b84;

            margin-bottom: 10px;

        }


        .preview-grid {

            display: grid;

            grid-template-columns:
                repeat(2, 1fr);

            gap: 10px;

        }


        .preview-item {

            position: relative;

            border:
                1px solid #dfe8f1;

            border-radius: 8px;

            background: #fff;

            overflow: hidden;

        }


        .preview-item img {

            width: 100%;

            height: 120px;

            object-fit: contain;

            display: block;

            background: #f8fafc;

        }


        .preview-item span {

            display: block;

            padding: 7px;

            font-size: 9px;

            color: #63788e;

            white-space: nowrap;

            overflow: hidden;

            text-overflow: ellipsis;

        }


        .primary-badge {

            position: absolute;

            top: 7px;

            left: 7px;

            background: #0875df;

            color: #fff;

            padding:
                4px 7px;

            border-radius: 4px;

            font-size: 8px;

            font-weight: 700;

        }


        /* =========================================================
   PRODUCT FEATURES
========================================================= */

        .feature-card-note {
            margin: 0 0 15px;
            padding: 10px 12px;
            background: #f3f9ff;
            border: 1px solid #dcecff;
            border-radius: 7px;
            color: #647d95;
            font-size: 10px;
            line-height: 1.5;
        }

        .feature-row {
            background: #f8fbff;
            border: 1px solid #dfeaf5;
            border-radius: 9px;
            padding: 15px;
            margin-bottom: 12px;
        }

        .feature-row-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 13px;
        }

        .feature-row-header strong {
            color: #0875df;
            font-size: 11px;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .feature-grid .form-group {
            margin-bottom: 0;
        }

        .remove-feature {
            border: none;
            background: #fff0f0;
            color: #c9363f;
            padding: 6px 10px;
            border-radius: 5px;
            font-size: 9px;
            font-weight: 700;
            cursor: pointer;
        }

        .remove-feature:hover {
            background: #c9363f;
            color: #fff;
        }

        .add-feature-btn {
            border: 1px solid #0875df;
            background: #eaf4ff;
            color: #0875df;
            padding: 10px 14px;
            border-radius: 7px;
            font-size: 10px;
            font-weight: 700;
            cursor: pointer;
        }

        .add-feature-btn:hover {
            background: #0875df;
            color: #fff;
        }


        /* =========================================================
   SWITCH
========================================================= */

        .setting-row {

            display: flex;

            align-items: center;

            justify-content: space-between;

            padding:
                14px 0;

            border-bottom:
                1px solid #edf2f7;

        }


        .setting-row:last-child {

            border-bottom: none;

        }


        .setting-info strong {

            display: block;

            color: #405b75;

            font-size: 12px;

        }


        .setting-info span {

            display: block;

            margin-top: 4px;

            color: #91a0af;

            font-size: 9px;

        }


        .switch {

            position: relative;

            width: 42px;

            height: 23px;

        }


        .switch input {

            opacity: 0;

            width: 0;

            height: 0;

        }


        .slider {

            position: absolute;

            cursor: pointer;

            inset: 0;

            background: #ccd7e1;

            border-radius: 30px;

            transition: .2s;

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

            transition: .2s;

            box-shadow:
                0 1px 3px rgba(0, 0, 0, .2);

        }


        .switch input:checked+.slider {

            background: #0875df;

        }


        .switch input:checked+.slider:before {

            transform:
                translateX(19px);

        }


        /* =========================================================
   BUTTONS
========================================================= */

        .form-actions {

            background: #fff;

            border:
                1px solid #e3ecf5;

            border-radius: 10px;

            padding:
                17px 20px;

            display: flex;

            justify-content: flex-end;

            gap: 10px;

            box-shadow:
                0 4px 18px rgba(25, 76, 120, .05);

        }


        .btn-cancel {

            height: 43px;

            padding:
                0 20px;

            display: inline-flex;

            align-items: center;

            justify-content: center;

            border-radius: 7px;

            background:
                #f1f5f9;

            color:
                #61748b;

            text-decoration: none;

            font-size: 12px;

            font-weight: 700;

        }


        .btn-save {

            height: 43px;

            padding:
                0 24px;

            border: none;

            border-radius: 7px;

            background:
                linear-gradient(135deg,
                    #087ce9,
                    #0756a5);

            color: #fff;

            font-size: 12px;

            font-weight: 700;

            cursor: pointer;

            box-shadow:
                0 5px 14px rgba(8, 124, 233, .22);

        }


        .btn-save:hover {

            transform:
                translateY(-1px);

        }


        /* =========================================================
   RESPONSIVE
========================================================= */

        @media (max-width: 1100px) {

            .form-layout {

                grid-template-columns: 1fr;

            }

        }


        @media (max-width: 900px) {

            .sidebar {

                width: 72px;

                padding:
                    20px 8px;

            }


            .sidebar-logo h2 {

                font-size: 14px;

            }


            .sidebar-logo p,
            .menu-title,
            .sidebar-menu a span:not(.icon) {

                display: none;

            }


            .sidebar-menu a {

                justify-content: center;

            }


            .main {

                margin-left: 72px;

            }

        }


        @media (max-width: 650px) {

            .content {

                padding: 18px;

            }


            .topbar {

                padding:
                    0 18px;

            }


            .admin-info {

                display: none;

            }


            .content-header {

                align-items:
                    flex-start;

                gap: 15px;

                flex-direction: column;

            }


            .form-grid {

                grid-template-columns: 1fr;

            }


            .form-group.full {

                grid-column: auto;

            }

            .feature-grid {
                grid-template-columns: 1fr;
            }

        }
    </style>

</head>


<body>


    <!-- =========================================================
     SIDEBAR
========================================================= -->

    <aside class="sidebar">

        <div class="sidebar-logo">

            <h2>
                CORE LTD
            </h2>

            <p>
                ADMINISTRATION
            </p>

        </div>


        <nav class="sidebar-menu">


            <div class="menu-title">
                Main
            </div>


            <a href="index.php">

                <span class="icon">
                    📊
                </span>

                <span>
                    Dashboard
                </span>

            </a>


            <a href="products.php">

                <span class="icon">
                    📦
                </span>

                <span>
                    Products
                </span>

            </a>


            <a href="product-add.php" class="active">

                <span class="icon">
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

                <span class="icon">
                    📁
                </span>

                <span>
                    Categories
                </span>

            </a>


            <a href="brands.php">

                <span class="icon">
                    🏷️
                </span>

                <span>
                    Brands
                </span>

            </a>


            <a href="tags.php">

                <span class="icon">
                    #
                </span>

                <span>
                    Tags
                </span>

            </a>


            <div class="menu-title">
                Sales
            </div>


            <a href="orders.php">

                <span class="icon">
                    🛒
                </span>

                <span>
                    Orders
                </span>

            </a>


            <a href="reviews.php">

                <span class="icon">
                    ⭐
                </span>

                <span>
                    Reviews
                </span>

            </a>


            <div class="menu-title">
                System
            </div>


            <a href="../index.php">

                <span class="icon">
                    ↗
                </span>

                <span>
                    View Website
                </span>

            </a>


            <a href="logout.php">

                <span class="icon">
                    ⇥
                </span>

                <span>
                    Logout
                </span>

            </a>


        </nav>

    </aside>



    <!-- =========================================================
     MAIN
========================================================= -->

    <main class="main">


        <header class="topbar">


            <div class="page-title">

                <h1>
                    Add Product
                </h1>

                <p>
                    Create a new product for the CORE LTD catalogue
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


                <div class="admin-info">

                    <strong>

                        <?= htmlspecialchars(
                            $_SESSION['admin_name']
                                ?? 'Administrator'
                        ) ?>

                    </strong>

                    <span>
                        Administrator
                    </span>

                </div>


            </div>


        </header>



        <div class="content">


            <div class="content-header">


                <div>

                    <h2>
                        Create Product
                    </h2>

                    <p>
                        Add product details, pricing, stock and images.
                    </p>

                </div>


                <a href="products.php" class="back-button">

                    ← Back to Products

                </a>


            </div>



            <?php if (!empty($errors)): ?>

                <div class="alert alert-error">

                    <strong>
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


                <div class="form-layout">


                    <!-- =========================================================
     LEFT COLUMN
========================================================= -->

                    <div>


                        <!-- PRODUCT INFORMATION -->

                        <div class="card">


                            <div class="card-header">

                                <div class="card-header-icon">
                                    📦
                                </div>

                                <div>

                                    <h3>
                                        Product Information
                                    </h3>

                                    <p>
                                        Basic product details
                                    </p>

                                </div>

                            </div>


                            <div class="card-body">


                                <div class="form-grid">


                                    <div class="form-group full">

                                        <label>

                                            Product Name
                                            <span class="required">*</span>

                                        </label>

                                        <input type="text" name="name"
                                            placeholder="e.g. Tenda F6 N300 Wireless Wi-Fi Router" value="<?= htmlspecialchars(
                                                                                                                $_POST['name'] ?? ''
                                                                                                            ) ?>"
                                            required>

                                    </div>



                                    <div class="form-group">

                                        <label>
                                            SKU
                                        </label>

                                        <input type="text" name="sku" placeholder="e.g. TENDA-F6" value="<?= htmlspecialchars(
                                                                                                                $_POST['sku'] ?? ''
                                                                                                            ) ?>">

                                        <div class="form-help">
                                            Optional product stock keeping unit.
                                        </div>

                                    </div>



                                    <div class="form-group">

                                        <label>

                                            Category
                                            <span class="required">*</span>

                                        </label>

                                        <select name="category_id" required>

                                            <option value="">
                                                Select Category
                                            </option>

                                            <?php foreach ($categories as $category): ?>

                                                <option value="<?= (int)$category['id'] ?>" <?= (int)($_POST['category_id'] ?? 0)
                                                                                                === (int)$category['id']
                                                                                                ? 'selected'
                                                                                                : '' ?>>

                                                    <?= htmlspecialchars(
                                                        $category['name']
                                                    ) ?>

                                                </option>

                                            <?php endforeach; ?>

                                        </select>

                                    </div>



                                    <div class="form-group">

                                        <label>

                                            Brand
                                            <span class="required">*</span>

                                        </label>

                                        <select name="brand_id" required>

                                            <option value="">
                                                Select Brand
                                            </option>

                                            <?php foreach ($brands as $brand): ?>

                                                <option value="<?= (int)$brand['id'] ?>" <?= (int)($_POST['brand_id'] ?? 0)
                                                                                                === (int)$brand['id']
                                                                                                ? 'selected'
                                                                                                : '' ?>>

                                                    <?= htmlspecialchars(
                                                        $brand['name']
                                                    ) ?>

                                                </option>

                                            <?php endforeach; ?>

                                        </select>

                                    </div>



                                    <div class="form-group full">

                                        <label>
                                            Short Description
                                        </label>

                                        <textarea name="short_description" placeholder="Short product description..."
                                            style="min-height:85px;"><?= htmlspecialchars(
                                                                            $_POST['short_description'] ?? ''
                                                                        ) ?></textarea>

                                    </div>



                                    <div class="form-group full">

                                        <label>
                                            Full Description
                                        </label>

                                        <textarea name="description"
                                            placeholder="Enter the complete product description..."
                                            style="min-height:180px;"><?= htmlspecialchars(
                                                                            $_POST['description'] ?? ''
                                                                        ) ?></textarea>

                                    </div>


                                </div>


                            </div>


                        </div>



                        <!-- PRODUCT FEATURES -->

                        <?php
                        $postedFeatures = $_POST['feature'] ?? [''];
                        $postedBenefits = $_POST['benefit'] ?? [''];

                        if (!is_array($postedFeatures) || count($postedFeatures) === 0) {
                            $postedFeatures = [''];
                        }

                        if (!is_array($postedBenefits)) {
                            $postedBenefits = [];
                        }

                        $featureRowCount = max(1, count($postedFeatures));
                        ?>

                        <div class="card">

                            <div class="card-header">
                                <div class="card-header-icon">★</div>
                                <div>
                                    <h3>Product Features</h3>
                                    <p>Add a feature and explain its benefit.</p>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="feature-card-note">
                                    Add as many feature and benefit pairs as you need. Empty rows are ignored when the
                                    product is saved.
                                </div>

                                <div id="features-container">

                                    <?php for ($featureIndex = 0; $featureIndex < $featureRowCount; $featureIndex++): ?>

                                        <div class="feature-row">

                                            <div class="feature-row-header">
                                                <strong>Feature <?= $featureIndex + 1 ?></strong>

                                                <button type="button" class="remove-feature" onclick="removeFeature(this)"
                                                    style="<?= $featureRowCount > 1 ? '' : 'display:none;' ?>">
                                                    Remove
                                                </button>
                                            </div>

                                            <div class="feature-grid">

                                                <div class="form-group">
                                                    <label>Feature</label>
                                                    <input type="text" name="feature[]"
                                                        placeholder="e.g. 300Mbps Wireless Speed"
                                                        value="<?= htmlspecialchars($postedFeatures[$featureIndex] ?? '') ?>">
                                                </div>

                                                <div class="form-group">
                                                    <label>Benefit</label>
                                                    <input type="text" name="benefit[]"
                                                        placeholder="e.g. Enjoy smooth browsing and HD streaming"
                                                        value="<?= htmlspecialchars($postedBenefits[$featureIndex] ?? '') ?>">
                                                </div>

                                            </div>
                                        </div>

                                    <?php endfor; ?>

                                </div>

                                <button type="button" class="add-feature-btn" onclick="addFeature()">
                                    + Add Another Feature
                                </button>

                            </div>
                        </div>


                        <!-- PRICING -->

                        <div class="card">


                            <div class="card-header">

                                <div class="card-header-icon">
                                    K
                                </div>

                                <div>

                                    <h3>
                                        Pricing
                                    </h3>

                                    <p>
                                        Product selling and cost prices
                                    </p>

                                </div>

                            </div>


                            <div class="card-body">


                                <div class="form-grid">


                                    <div class="form-group">

                                        <label>

                                            Selling Price
                                            <span class="required">*</span>

                                        </label>

                                        <div class="price-input">

                                            <span class="price-prefix">
                                                Ksh.
                                            </span>

                                            <input type="number" name="price" step="0.01" min="0" placeholder="0.00"
                                                value="<?= htmlspecialchars(
                                                            $_POST['price'] ?? ''
                                                        ) ?>" required>

                                        </div>

                                    </div>



                                    <div class="form-group">

                                        <label>
                                            Old Price
                                        </label>

                                        <div class="price-input">

                                            <span class="price-prefix">
                                                Ksh.
                                            </span>

                                            <input type="number" name="old_price" step="0.01" min="0" placeholder="0.00"
                                                value="<?= htmlspecialchars(
                                                            $_POST['old_price'] ?? ''
                                                        ) ?>">

                                        </div>

                                    </div>



                                    <div class="form-group">

                                        <label>
                                            Cost Price
                                        </label>

                                        <div class="price-input">

                                            <span class="price-prefix">
                                                Ksh.
                                            </span>

                                            <input type="number" name="cost_price" step="0.01" min="0"
                                                placeholder="0.00" value="<?= htmlspecialchars(
                                                                                $_POST['cost_price'] ?? ''
                                                                            ) ?>">

                                        </div>

                                    </div>


                                </div>


                            </div>


                        </div>



                        <!-- SEO -->

                        <div class="card">


                            <div class="card-header">

                                <div class="card-header-icon">
                                    ◎
                                </div>

                                <div>

                                    <h3>
                                        Search Engine Optimization
                                    </h3>

                                    <p>
                                        Information used by search engines
                                    </p>

                                </div>

                            </div>


                            <div class="card-body">


                                <div class="form-group">

                                    <label>
                                        Meta Title
                                    </label>

                                    <input type="text" name="meta_title" placeholder="SEO title..." value="<?= htmlspecialchars(
                                                                                                                $_POST['meta_title'] ?? ''
                                                                                                            ) ?>">

                                </div>



                                <div class="form-group">

                                    <label>
                                        Meta Description
                                    </label>

                                    <textarea name="meta_description" placeholder="SEO description..."
                                        style="min-height:100px;"><?= htmlspecialchars(
                                                                        $_POST['meta_description'] ?? ''
                                                                    ) ?></textarea>

                                </div>



                                <div class="form-group">

                                    <label>
                                        Meta Keywords
                                    </label>

                                    <input type="text" name="meta_keywords"
                                        placeholder="router, wifi, tenda, networking..." value="<?= htmlspecialchars(
                                                                                                    $_POST['meta_keywords'] ?? ''
                                                                                                ) ?>">

                                </div>


                            </div>


                        </div>


                    </div>



                    <!-- =========================================================
     RIGHT COLUMN
========================================================= -->

                    <div>


                        <!-- MAIN IMAGE -->

                        <div class="card">


                            <div class="card-header">

                                <div class="card-header-icon">
                                    ▧
                                </div>

                                <div>

                                    <h3>
                                        Main Product Image
                                    </h3>

                                    <p>
                                        Primary image shown first
                                    </p>

                                </div>

                            </div>


                            <div class="card-body">


                                <label class="image-upload" for="main_image">

                                    <div class="image-upload-icon">
                                        ↑
                                    </div>

                                    <strong>
                                        Upload Main Image
                                    </strong>

                                    <span>
                                        JPG, JPEG, PNG or WEBP
                                    </span>

                                    <span>
                                        Maximum 5MB
                                    </span>

                                    <input type="file" id="main_image" name="main_image" accept=".jpg,.jpeg,.png,.webp">

                                </label>


                                <div class="preview-section" id="mainPreviewSection" style="display:none;">

                                    <div class="preview-title">
                                        Main Image Preview
                                    </div>

                                    <div class="preview-grid">

                                        <div class="preview-item">

                                            <span class="primary-badge">
                                                PRIMARY
                                            </span>

                                            <img id="mainPreview" src="" alt="Main image preview">

                                            <span id="mainFileName">
                                            </span>

                                        </div>

                                    </div>

                                </div>


                            </div>


                        </div>



                        <!-- ADDITIONAL IMAGES -->

                        <div class="card">


                            <div class="card-header">

                                <div class="card-header-icon">
                                    ▧
                                </div>

                                <div>

                                    <h3>
                                        Additional Images
                                    </h3>

                                    <p>
                                        Add up to 10 more product images
                                    </p>

                                </div>

                            </div>


                            <div class="card-body">


                                <label class="image-upload" for="additional_images">

                                    <div class="image-upload-icon">
                                        +
                                    </div>

                                    <strong>
                                        Upload Additional Images
                                    </strong>

                                    <span>
                                        Select multiple images at once
                                    </span>

                                    <span>
                                        Maximum 10 images • 5MB each
                                    </span>

                                    <input type="file" id="additional_images" name="additional_images[]"
                                        accept=".jpg,.jpeg,.png,.webp" multiple>

                                </label>


                                <div class="preview-section" id="additionalPreviewSection" style="display:none;">

                                    <div class="preview-title">
                                        Additional Image Preview
                                    </div>

                                    <div class="preview-grid" id="additionalPreview">
                                    </div>

                                </div>


                            </div>


                        </div>



                        <!-- INVENTORY -->

                        <div class="card">


                            <div class="card-header">

                                <div class="card-header-icon">
                                    🛒
                                </div>

                                <div>

                                    <h3>
                                        Inventory
                                    </h3>

                                    <p>
                                        Product stock management
                                    </p>

                                </div>

                            </div>


                            <div class="card-body">


                                <div class="form-group">

                                    <label>
                                        Stock Quantity
                                    </label>

                                    <input type="number" name="stock_quantity" min="0" value="<?= htmlspecialchars(
                                                                                                    $_POST['stock_quantity']
                                                                                                        ?? '0'
                                                                                                ) ?>">

                                </div>



                                <div class="form-group">

                                    <label>
                                        Low Stock Level
                                    </label>

                                    <input type="number" name="low_stock_level" min="0" value="<?= htmlspecialchars(
                                                                                                    $_POST['low_stock_level']
                                                                                                        ?? '5'
                                                                                                ) ?>">

                                    <div class="form-help">
                                        Product is considered low stock at or below this number.
                                    </div>

                                </div>


                            </div>


                        </div>



                        <!-- SETTINGS -->

                        <div class="card">


                            <div class="card-header">

                                <div class="card-header-icon">
                                    ⚙
                                </div>

                                <div>

                                    <h3>
                                        Product Settings
                                    </h3>

                                    <p>
                                        Control product visibility
                                    </p>

                                </div>

                            </div>


                            <div class="card-body">


                                <div class="setting-row">


                                    <div class="setting-info">

                                        <strong>
                                            Featured Product
                                        </strong>

                                        <span>
                                            Show this product as featured.
                                        </span>

                                    </div>


                                    <label class="switch">

                                        <input type="checkbox" name="featured" value="1" <?= isset($_POST['featured'])
                                                                                                ? 'checked'
                                                                                                : '' ?>>

                                        <span class="slider">
                                        </span>

                                    </label>


                                </div>



                                <div class="setting-row">


                                    <div class="setting-info">

                                        <strong>
                                            Product Status
                                        </strong>

                                        <span>
                                            Active products appear on the website.
                                        </span>

                                    </div>


                                    <label class="switch">

                                        <input type="checkbox" name="status" value="1" <?= !isset($_POST['status'])
                                                                                            || isset($_POST['status'])
                                                                                            ? 'checked'
                                                                                            : '' ?>>

                                        <span class="slider">
                                        </span>

                                    </label>


                                </div>


                            </div>


                        </div>


                    </div>


                </div>



                <!-- ACTIONS -->

                <div class="form-actions">


                    <a href="products.php" class="btn-cancel">
                        Cancel
                    </a>


                    <button type="submit" class="btn-save">
                        ✓ &nbsp; Save Product
                    </button>


                </div>


            </form>


        </div>


    </main>



    <script>
        /*
|--------------------------------------------------------------------------
| MAIN IMAGE PREVIEW
|--------------------------------------------------------------------------
*/

        const mainImageInput =
            document.getElementById(
                'main_image'
            );

        const mainPreviewSection =
            document.getElementById(
                'mainPreviewSection'
            );

        const mainPreview =
            document.getElementById(
                'mainPreview'
            );

        const mainFileName =
            document.getElementById(
                'mainFileName'
            );


        mainImageInput.addEventListener(
            'change',
            function() {

                const file =
                    this.files[0];


                if (!file) {

                    mainPreviewSection.style.display =
                        'none';

                    return;

                }


                const reader =
                    new FileReader();


                reader.onload =
                    function(event) {

                        mainPreview.src =
                            event.target.result;

                        mainFileName.textContent =
                            file.name;

                        mainPreviewSection.style.display =
                            'block';

                    };


                reader.readAsDataURL(file);

            }
        );


        /*
        |--------------------------------------------------------------------------
        | ADDITIONAL IMAGE PREVIEW
        |--------------------------------------------------------------------------
        */

        const additionalInput =
            document.getElementById(
                'additional_images'
            );

        const additionalPreviewSection =
            document.getElementById(
                'additionalPreviewSection'
            );

        const additionalPreview =
            document.getElementById(
                'additionalPreview'
            );


        additionalInput.addEventListener(
            'change',
            function() {


                additionalPreview.innerHTML =
                    '';


                const files =
                    Array.from(
                        this.files
                    );


                if (files.length === 0) {

                    additionalPreviewSection.style.display =
                        'none';

                    return;

                }


                if (files.length > 10) {

                    alert(
                        'You can upload a maximum of 10 additional images.'
                    );

                    this.value = '';

                    additionalPreviewSection.style.display =
                        'none';

                    return;

                }


                files.forEach(
                    function(file, index) {


                        const reader =
                            new FileReader();


                        reader.onload =
                            function(event) {


                                const item =
                                    document.createElement(
                                        'div'
                                    );


                                item.className =
                                    'preview-item';


                                const image =
                                    document.createElement(
                                        'img'
                                    );


                                image.src =
                                    event.target.result;


                                image.alt =
                                    file.name;


                                const filename =
                                    document.createElement(
                                        'span'
                                    );


                                filename.textContent =
                                    (index + 2) +
                                    '. ' +
                                    file.name;


                                item.appendChild(
                                    image
                                );


                                item.appendChild(
                                    filename
                                );


                                additionalPreview.appendChild(
                                    item
                                );


                            };


                        reader.readAsDataURL(file);

                    }
                );


                additionalPreviewSection.style.display =
                    'block';

            }
        );


        /*
        |--------------------------------------------------------------------------
        | PRODUCT FEATURES
        |--------------------------------------------------------------------------
        */

        function addFeature() {

            const container = document.getElementById('features-container');

            if (!container) {
                return;
            }

            const row = document.createElement('div');
            row.className = 'feature-row';

            row.innerHTML = `
            <div class="feature-row-header">
                <strong>Feature</strong>
                <button type="button" class="remove-feature" onclick="removeFeature(this)">
                    Remove
                </button>
            </div>

            <div class="feature-grid">
                <div class="form-group">
                    <label>Feature</label>
                    <input type="text" name="feature[]" placeholder="e.g. Three High-Gain Antennas">
                </div>

                <div class="form-group">
                    <label>Benefit</label>
                    <input type="text" name="benefit[]" placeholder="e.g. Provides stronger Wi-Fi coverage">
                </div>
            </div>
        `;

            container.appendChild(row);
            updateFeatureNumbers();
        }


        function removeFeature(button) {

            const row = button.closest('.feature-row');

            if (!row) {
                return;
            }

            const container = document.getElementById('features-container');

            row.remove();

            if (container && container.querySelectorAll('.feature-row').length === 0) {
                addFeature();
                return;
            }

            updateFeatureNumbers();
        }


        function updateFeatureNumbers() {

            const rows = document.querySelectorAll('.feature-row');

            rows.forEach(function(row, index) {

                const title = row.querySelector('.feature-row-header strong');

                if (title) {
                    title.textContent = 'Feature ' + (index + 1);
                }

                const removeButton = row.querySelector('.remove-feature');

                if (removeButton) {
                    removeButton.style.display = rows.length > 1 ? 'inline-block' : 'none';
                }
            });
        }


        updateFeatureNumbers();
    </script>


</body>

</html>