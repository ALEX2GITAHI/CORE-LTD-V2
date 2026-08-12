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
| GET PRODUCT ID
|--------------------------------------------------------------------------
*/

$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($productId <= 0) {
    header("Location: products.php?error=invalid_product");
    exit;
}


/*
|--------------------------------------------------------------------------
| GET PRODUCT
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT
        id,
        name,
        slug,
        main_image,
        price,
        stock_quantity
    FROM products
    WHERE id = ?
    LIMIT 1
");

$stmt->bind_param("i", $productId);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();

    header("Location: products.php?error=product_not_found");
    exit;
}

$product = $result->fetch_assoc();

$stmt->close();


/*
|--------------------------------------------------------------------------
| DELETE PRODUCT
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $confirm = $_POST['confirm_delete'] ?? '';

    if ($confirm !== 'YES') {
        header("Location: products.php?error=delete_cancelled");
        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | COLLECT PRODUCT IMAGES BEFORE DELETING DATABASE RECORDS
    |--------------------------------------------------------------------------
    */

    $images = [];

    $imageStmt = $conn->prepare("
        SELECT image
        FROM product_images
        WHERE product_id = ?
    ");

    $imageStmt->bind_param("i", $productId);
    $imageStmt->execute();

    $imageResult = $imageStmt->get_result();

    while ($image = $imageResult->fetch_assoc()) {

        if (!empty($image['image'])) {
            $images[] = $image['image'];
        }
    }

    $imageStmt->close();


    /*
    |--------------------------------------------------------------------------
    | ALSO COLLECT MAIN IMAGE
    |--------------------------------------------------------------------------
    */

    if (!empty($product['main_image'])) {
        $images[] = $product['main_image'];
    }

    /*
    |--------------------------------------------------------------------------
    | REMOVE DUPLICATES
    |--------------------------------------------------------------------------
    */

    $images = array_unique($images);


    /*
    |--------------------------------------------------------------------------
    | START TRANSACTION
    |--------------------------------------------------------------------------
    */

    $conn->begin_transaction();

    try {

        /*
        |--------------------------------------------------------------------------
        | DELETE PRODUCT IMAGES
        |--------------------------------------------------------------------------
        */

        $stmt = $conn->prepare("
            DELETE FROM product_images
            WHERE product_id = ?
        ");

        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $stmt->close();


        /*
        |--------------------------------------------------------------------------
        | DELETE PRODUCT FEATURES
        |--------------------------------------------------------------------------
        */

        $stmt = $conn->prepare("
            DELETE FROM product_features
            WHERE product_id = ?
        ");

        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $stmt->close();


        /*
        |--------------------------------------------------------------------------
        | DELETE PRODUCT SPECIFICATIONS
        |--------------------------------------------------------------------------
        */

        $stmt = $conn->prepare("
            DELETE FROM product_specifications
            WHERE product_id = ?
        ");

        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $stmt->close();


        /*
        |--------------------------------------------------------------------------
        | DELETE PRODUCT TAGS
        |--------------------------------------------------------------------------
        */

        $stmt = $conn->prepare("
            DELETE FROM product_tags
            WHERE product_id = ?
        ");

        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $stmt->close();


        /*
        |--------------------------------------------------------------------------
        | DELETE PRODUCT VARIANTS
        |--------------------------------------------------------------------------
        */

        $stmt = $conn->prepare("
            DELETE FROM product_variants
            WHERE product_id = ?
        ");

        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $stmt->close();


        /*
        |--------------------------------------------------------------------------
        | DELETE REVIEWS
        |--------------------------------------------------------------------------
        */

        $stmt = $conn->prepare("
            DELETE FROM reviews
            WHERE product_id = ?
        ");

        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $stmt->close();


        /*
        |--------------------------------------------------------------------------
        | DELETE WISHLISTS
        |--------------------------------------------------------------------------
        */

        $stmt = $conn->prepare("
            DELETE FROM wishlists
            WHERE product_id = ?
        ");

        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $stmt->close();


        /*
        |--------------------------------------------------------------------------
        | DELETE STOCK MOVEMENTS
        |--------------------------------------------------------------------------
        */

        $stmt = $conn->prepare("
            DELETE FROM stock_movements
            WHERE product_id = ?
        ");

        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $stmt->close();


        /*
        |--------------------------------------------------------------------------
        | DELETE PRODUCT
        |--------------------------------------------------------------------------
        */

        $stmt = $conn->prepare("
            DELETE FROM products
            WHERE id = ?
        ");

        $stmt->bind_param("i", $productId);
        $stmt->execute();

        if ($stmt->affected_rows !== 1) {

            $stmt->close();

            throw new Exception("Product could not be deleted.");
        }

        $stmt->close();


        /*
        |--------------------------------------------------------------------------
        | COMMIT
        |--------------------------------------------------------------------------
        */

        $conn->commit();


        /*
        |--------------------------------------------------------------------------
        | DELETE PHYSICAL IMAGE FILES
        |--------------------------------------------------------------------------
        */

        foreach ($images as $image) {

            $image = basename($image);

            $imagePath = __DIR__ . '/../uploads/products/' . $image;

            if (is_file($imagePath)) {
                @unlink($imagePath);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | REDIRECT SUCCESS
        |--------------------------------------------------------------------------
        */

        header("Location: products.php?success=product_deleted");
        exit;
    } catch (Throwable $e) {

        /*
        |--------------------------------------------------------------------------
        | ROLLBACK
        |--------------------------------------------------------------------------
        */

        $conn->rollback();

        header("Location: products.php?error=delete_failed");
        exit;
    }
}

?>


<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        Delete Product | CORE LTD Admin
    </title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
    body {
        margin: 0;
        background: #f4f7fb;
        font-family: Arial, sans-serif;
    }

    .delete-wrapper {
        min-height: 100vh;

        display: flex;
        align-items: center;
        justify-content: center;

        padding: 30px;
    }

    .delete-card {
        width: 100%;
        max-width: 550px;

        background: #ffffff;

        border-radius: 18px;

        padding: 40px;

        box-shadow:
            0 15px 45px rgba(0, 51, 102, 0.12);

        text-align: center;
    }

    .warning-icon {
        width: 85px;
        height: 85px;

        margin: 0 auto 20px;

        border-radius: 50%;

        background: #fff1f1;

        color: #dc3545;

        display: flex;
        align-items: center;
        justify-content: center;

        font-size: 42px;
    }

    .delete-card h1 {
        color: #17365d;

        font-size: 28px;

        margin-bottom: 12px;
    }

    .delete-card p {
        color: #6c757d;

        line-height: 1.6;
    }

    .product-preview {
        margin: 25px 0;

        padding: 20px;

        border-radius: 12px;

        background: #f7faff;

        border: 1px solid #e4edf7;
    }

    .product-preview img {
        width: 120px;
        height: 120px;

        object-fit: contain;

        background: #ffffff;

        border-radius: 10px;

        padding: 10px;

        margin-bottom: 15px;
    }

    .product-name {
        font-size: 20px;

        font-weight: 700;

        color: #17365d;
    }

    .product-price {
        margin-top: 5px;

        font-weight: 600;

        color: #0d6efd;
    }

    .warning-message {
        background: #fff3cd;

        border: 1px solid #ffe69c;

        color: #664d03;

        padding: 14px;

        border-radius: 10px;

        margin: 20px 0;

        font-size: 14px;
    }

    .btn-delete {
        background: #dc3545;

        border: none;

        color: #ffffff;

        padding: 13px 25px;

        border-radius: 8px;

        font-weight: 700;
    }

    .btn-delete:hover {
        background: #bb2d3b;

        color: #ffffff;
    }

    .btn-cancel {
        background: #17365d;

        color: #ffffff;

        padding: 13px 25px;

        border-radius: 8px;

        font-weight: 700;

        text-decoration: none;

        display: inline-block;
    }

    .btn-cancel:hover {
        background: #0d6efd;

        color: #ffffff;
    }

    .buttons {
        display: flex;

        justify-content: center;

        gap: 12px;

        flex-wrap: wrap;

        margin-top: 25px;
    }
    </style>

</head>


<body>


    <div class="delete-wrapper">

        <div class="delete-card">


            <div class="warning-icon">

                ⚠

            </div>


            <h1>
                Delete Product?
            </h1>


            <p>

                You are about to permanently delete this
                product and its related information.

            </p>


            <div class="product-preview">


                <?php if (!empty($product['main_image'])): ?>

                <img src="../uploads/products/<?= htmlspecialchars($product['main_image']) ?>"
                    alt="<?= htmlspecialchars($product['name']) ?>">

                <?php else: ?>

                <div style="
                        height:120px;
                        display:flex;
                        align-items:center;
                        justify-content:center;
                        color:#999;
                    ">

                    No Image

                </div>

                <?php endif; ?>


                <div class="product-name">

                    <?= htmlspecialchars($product['name']) ?>

                </div>


                <div class="product-price">

                    Ksh.
                    <?= number_format((float)$product['price'], 2) ?>

                </div>


            </div>


            <div class="warning-message">

                <strong>Warning:</strong>

                This action cannot be undone.

                All product images, features,
                specifications, tags, variants,
                reviews, wishlist records and
                stock movement records associated
                with this product will also be removed.

            </div>


            <form method="POST">


                <input type="hidden" name="confirm_delete" value="YES">


                <div class="buttons">


                    <a href="products.php" class="btn-cancel">

                        Cancel

                    </a>


                    <button type="submit" class="btn-delete" onclick="
                        return confirm(
                            'Are you absolutely sure you want to delete this product?'
                        );
                    ">

                        🗑 Delete Product

                    </button>


                </div>


            </form>


        </div>

    </div>


</body>

</html>