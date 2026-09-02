<?php

/*
|--------------------------------------------------------------------------
| CORE LTD - DATABASE DRIVEN CATEGORY PAGE
|--------------------------------------------------------------------------
| Example:
|
| category.php?slug=routers
| category.php?slug=access-points
| category.php?slug=switches
|
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/includes/db.php';

/*
|--------------------------------------------------------------------------
| GET CATEGORY SLUG
|--------------------------------------------------------------------------
*/

$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';

if ($slug === '') {
    http_response_code(404);
    exit('Category not found.');
}


/*
|--------------------------------------------------------------------------
| GET CATEGORY
|--------------------------------------------------------------------------
*/

$categoryStmt = $conn->prepare("
    SELECT
        id,
        name,
        slug,
        description,
        image
    FROM categories
    WHERE slug = ?
    LIMIT 1
");

if (!$categoryStmt) {
    die("Category query failed: " . $conn->error);
}

$categoryStmt->bind_param("s", $slug);
$categoryStmt->execute();

$categoryResult = $categoryStmt->get_result();

if ($categoryResult->num_rows === 0) {

    $categoryStmt->close();

    http_response_code(404);
    exit('Category not found.');
}

$category = $categoryResult->fetch_assoc();

$categoryStmt->close();


/*
|--------------------------------------------------------------------------
| CATEGORY INFORMATION
|--------------------------------------------------------------------------
*/

$categoryId   = (int) $category['id'];
$categoryName = $category['name'];
$categorySlug = $category['slug'];

$pageTitle = htmlspecialchars($categoryName) . ' | CORE LTD';


/*
|--------------------------------------------------------------------------
| GET PRODUCTS IN THIS CATEGORY
|--------------------------------------------------------------------------
|
| We also LEFT JOIN product_images so that if main_image is empty or
| incorrect, we can use the primary product image.
|
|--------------------------------------------------------------------------
*/

$productStmt = $conn->prepare("
    SELECT
        p.id,
        p.name,
        p.slug,
        p.price,
        p.old_price,
        p.main_image,
        p.short_description,

        b.name AS brand_name,
        b.slug AS brand_slug,

        pi.image AS primary_image

    FROM products p

    LEFT JOIN brands b
        ON p.brand_id = b.id

    LEFT JOIN product_images pi
        ON pi.product_id = p.id
        AND pi.is_primary = 1

    WHERE p.category_id = ?
      AND p.status = 1

    ORDER BY p.created_at DESC
");

if (!$productStmt) {
    die("Product query failed: " . $conn->error);
}

$productStmt->bind_param("i", $categoryId);

$productStmt->execute();

$productResult = $productStmt->get_result();

$products = [];

while ($row = $productResult->fetch_assoc()) {
    $products[] = $row;
}

$productStmt->close();


/*
|--------------------------------------------------------------------------
| IMAGE PATH HELPER
|--------------------------------------------------------------------------
|
| Your database may contain:
|
| images/product/...
| images/product/small-size/...
| uploads/...
| just-a-filename.jpg
|
| This function makes the category page more tolerant of those formats.
|
|--------------------------------------------------------------------------
*/

function categoryProductImage($mainImage, $primaryImage)
{
    /*
    |--------------------------------------------------------------------------
    | FIRST PRIORITY - main_image
    |--------------------------------------------------------------------------
    */

    $image = '';

    if (!empty($mainImage)) {
        $image = trim($mainImage);
    }

    /*
    |--------------------------------------------------------------------------
    | SECOND PRIORITY - product_images primary image
    |--------------------------------------------------------------------------
    */

    if ($image === '' && !empty($primaryImage)) {
        $image = trim($primaryImage);
    }

    /*
    |--------------------------------------------------------------------------
    | NOTHING FOUND
    |--------------------------------------------------------------------------
    */

    if ($image === '') {
        return 'images/no-image.jpg';
    }

    /*
    |--------------------------------------------------------------------------
    | REMOVE LEADING SLASH
    |--------------------------------------------------------------------------
    */

    $image = ltrim($image, '/');

    /*
    |--------------------------------------------------------------------------
    | IF DATABASE ALREADY HAS A FULL URL
    |--------------------------------------------------------------------------
    */

    if (
        strpos($image, 'http://') === 0 ||
        strpos($image, 'https://') === 0
    ) {
        return $image;
    }

    /*
    |--------------------------------------------------------------------------
    | IF IMAGE IS ALREADY A NORMAL WEBSITE PATH
    |--------------------------------------------------------------------------
    */

    if (
        strpos($image, 'images/') === 0 ||
        strpos($image, 'uploads/') === 0 ||
        strpos($image, 'assets/') === 0
    ) {
        return $image;
    }

    /*
    |--------------------------------------------------------------------------
    | IF ONLY THE FILENAME WAS SAVED
    |--------------------------------------------------------------------------
    |
    | Try the most common CORE LTD product image locations.
    |
    */

    $possiblePaths = [

        'images/product/small-size/' . $image,

        'images/product/' . $image,

        'images/' . $image,

        'uploads/products/' . $image,

        'uploads/' . $image

    ];

    /*
    |--------------------------------------------------------------------------
    | CHECK WHICH FILE ACTUALLY EXISTS
    |--------------------------------------------------------------------------
    */

    foreach ($possiblePaths as $path) {

        $fullPath = __DIR__ . '/' . $path;

        if (file_exists($fullPath)) {
            return $path;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | RETURN ORIGINAL PATH
    |--------------------------------------------------------------------------
    |
    | Even if PHP cannot find it, return it because it may be a path
    | generated/handled elsewhere.
    |
    */

    return $image;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= $pageTitle; ?></title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

</head>

<body>


    <?php
    /*
|--------------------------------------------------------------------------
| HEADER
|--------------------------------------------------------------------------
|
| IMPORTANT:
| HEADER IS INCLUDED ONLY ONCE.
|
|--------------------------------------------------------------------------
*/

    include __DIR__ . '/header.php';

    ?>


    <!-- =========================================================
     BREADCRUMB
========================================================= -->

    <div class="breadcrumb-area">

        <div class="container-fluid">

            <div class="breadcrumb-content">

                <ul>

                    <li>

                        <a href="index.php">
                            Home
                        </a>

                    </li>

                    <li class="active">

                        <?= htmlspecialchars($categoryName); ?>

                    </li>

                </ul>

            </div>

        </div>

    </div>


    <!-- =========================================================
     CONTENT WRAPPER
========================================================= -->




    <!-- =====================================================
                     PRODUCTS WRAPPER
                ====================================================== -->

    <div class="product-grid">

        <div class="shop-products-wrapper">

            <div class="tab-content">

                <div id="grid-view" class="tab-pane fade active show" role="tabpanel">

                    <div class="product-area shop-product-area">

                        <div class="row">


                            <?php if (!empty($products)): ?>


                                <?php foreach ($products as $product): ?>

                                    <?php

                                    /*
|--------------------------------------------------------------------------
| PRODUCT URL
|--------------------------------------------------------------------------
*/

                                    $productUrl =
                                        'product.php?slug=' .
                                        urlencode($product['slug']);


                                    /*
|--------------------------------------------------------------------------
| PRODUCT IMAGE
|--------------------------------------------------------------------------
*/

                                    $image = categoryProductImage(
                                        $product['main_image'] ?? '',
                                        $product['primary_image'] ?? ''
                                    );


                                    /*
|--------------------------------------------------------------------------
| PRICE
|--------------------------------------------------------------------------
*/

                                    $price = isset($product['price'])
                                        ? (float) $product['price']
                                        : 0;


                                    /*
|--------------------------------------------------------------------------
| OLD PRICE
|--------------------------------------------------------------------------
*/

                                    $oldPrice = 0;

                                    if (
                                        isset($product['old_price']) &&
                                        $product['old_price'] !== null &&
                                        $product['old_price'] !== ''
                                    ) {

                                        $oldPrice = (float) $product['old_price'];
                                    }


                                    /*
|--------------------------------------------------------------------------
| DISCOUNT
|--------------------------------------------------------------------------
*/

                                    $discount = 0;

                                    if ($oldPrice > $price && $oldPrice > 0) {

                                        $discount = round(
                                            (($oldPrice - $price) / $oldPrice) * 100
                                        );
                                    }

                                    ?>


                                    <!-- =====================================================
     PRODUCT COLUMN
====================================================== -->

                                    <div class="col-lg-3 col-md-4 col-sm-6 mt-40">


                                        <!-- =================================================
         SINGLE PRODUCT
    ================================================== -->

                                        <div class="single-product-wrap">


                                            <!-- =================================================
             PRODUCT IMAGE
        ================================================== -->

                                            <div class="product-image">

                                                <a href="<?= htmlspecialchars($productUrl); ?>">

                                                    <img src="<?= htmlspecialchars($image); ?>"
                                                        alt="<?= htmlspecialchars($product['name']); ?>" loading="lazy">

                                                </a>


                                                <?php if ($discount > 0): ?>

                                                    <span class="discount-percentage">

                                                        -<?= $discount; ?>%

                                                    </span>

                                                <?php endif; ?>

                                            </div>


                                            <!-- =================================================
             PRODUCT DESCRIPTION
        ================================================== -->

                                            <div class="product_desc">

                                                <div class="product_desc_info">


                                                    <!-- =========================================
                     BRAND / CATEGORY
                ========================================== -->

                                                    <div class="product-review">

                                                        <h5 class="manufacturer">

                                                            <?php if (!empty($product['brand_name'])): ?>

                                                                <a href="brand.php?slug=<?= urlencode($product['brand_slug']); ?>">

                                                                    <?= htmlspecialchars(
                                                                        $product['brand_name']
                                                                    ); ?>

                                                                </a>

                                                            <?php else: ?>

                                                                <a href="#️⃣">

                                                                    <?= htmlspecialchars(
                                                                        $categoryName
                                                                    ); ?>

                                                                </a>

                                                            <?php endif; ?>

                                                        </h5>


                                                        <!-- =====================================
                         RATING
                    ====================================== -->

                                                        <div class="rating-box">

                                                            <ul class="rating">

                                                                <li>
                                                                    <i class="fa fa-star-o"></i>
                                                                </li>

                                                                <li>
                                                                    <i class="fa fa-star-o"></i>
                                                                </li>

                                                                <li>
                                                                    <i class="fa fa-star-o"></i>
                                                                </li>

                                                                <li class="no-star">
                                                                    <i class="fa fa-star-o"></i>
                                                                </li>

                                                                <li class="no-star">
                                                                    <i class="fa fa-star-o"></i>
                                                                </li>

                                                            </ul>

                                                        </div>

                                                    </div>


                                                    <!-- =========================================
                     PRODUCT NAME
                ========================================== -->

                                                    <h4>

                                                        <a class="product_name" href="<?= htmlspecialchars($productUrl); ?>">

                                                            <?= htmlspecialchars(
                                                                $product['name']
                                                            ); ?>

                                                        </a>

                                                    </h4>


                                                    <!-- =========================================
                     PRICE
                ========================================== -->

                                                    <div class="price-box">


                                                        <?php if ($oldPrice > $price && $price > 0): ?>


                                                            <span class="new-price new-price-2">

                                                                Ksh.
                                                                <?= number_format($price, 2); ?>

                                                            </span>


                                                            <span class="old-price">

                                                                Ksh.
                                                                <?= number_format($oldPrice, 2); ?>

                                                            </span>


                                                            <span class="discount-percentage">

                                                                -<?= $discount; ?>%

                                                            </span>


                                                        <?php else: ?>


                                                            <span class="new-price">

                                                                Ksh.
                                                                <?= number_format($price, 2); ?>

                                                            </span>


                                                        <?php endif; ?>


                                                    </div>


                                                </div>


                                                <!-- =================================================
                 PRODUCT ACTIONS
            ================================================== -->

                                                <div class="add-actions">

                                                    <ul class="add-actions-link">


                                                        <!-- =====================================
                         ADD TO CART
                    ====================================== -->

                                                        <li class="add-to-cart">

                                                            <a href="#" class="add-to-cart-link"
                                                                data-product-id="<?= (int)$product['id']; ?>">

                                                                <i class="fa fa-shopping-cart"></i>

                                                            </a>

                                                        </li>


                                                        <!-- =====================================
                         WISHLIST
                    ====================================== -->

                                                        <li>

                                                            <a class="links-details" href="#"
                                                                data-product-id="<?= (int)$product['id']; ?>">

                                                                <i class="fa fa-heart-o"></i>

                                                            </a>

                                                        </li>


                                                    </ul>

                                                </div>


                                            </div>


                                        </div>


                                    </div>


                                <?php endforeach; ?>


                            <?php else: ?>


                                <!-- =====================================================
     NO PRODUCTS
====================================================== -->

                                <div class="col-lg-12">

                                    <div class="no-products text-center">

                                        <h2>
                                            No Products Found
                                        </h2>

                                        <p>

                                            There are currently no products
                                            available in

                                            <strong>
                                                <?= htmlspecialchars($categoryName); ?>
                                            </strong>.

                                        </p>

                                        <a href="index.php" class="back-home-btn">

                                            Continue Shopping

                                        </a>

                                    </div>

                                </div>


                            <?php endif; ?>


                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    </div>

    </div>

    </div>

    </div>


    <?php

    /*
|--------------------------------------------------------------------------
| FOOTER
|--------------------------------------------------------------------------
*/

    include __DIR__ . '/footer.php';

    ?>


</body>

</html>