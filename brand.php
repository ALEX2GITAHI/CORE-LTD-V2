<?php

/*
|--------------------------------------------------------------------------
| CORE LTD - BRAND PRODUCTS PAGE
|--------------------------------------------------------------------------
|
| Examples:
|
| brand.php?slug=tenda
| brand.php?slug=ruijie
| brand.php?slug=mikrotik
| brand.php?slug=tp-link
|
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/includes/db.php';


/*
|--------------------------------------------------------------------------
| HELPER FUNCTIONS
|--------------------------------------------------------------------------
*/

/**
 * Escape HTML safely
 */
function e($value)
{
    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES,
        'UTF-8'
    );
}


/**
 * Resolve product main image.
 *
 * The database may contain:
 *
 * images/product/small-size/image.jpg
 * /images/product/small-size/image.jpg
 * product/image.jpg
 * image.jpg
 *
 * This function checks the actual files inside the website folder
 * and returns a browser-ready URL.
 */
function resolveProductImage($image)
{
    $image = trim((string)$image);

    /*
    |--------------------------------------------------------------------------
    | NO IMAGE STORED
    |--------------------------------------------------------------------------
    */

    if ($image === '') {

        $fallback = 'images/no-image.jpg';

        if (file_exists(__DIR__ . '/' . $fallback)) {
            return $fallback;
        }

        return $fallback;
    }


    /*
    |--------------------------------------------------------------------------
    | EXTERNAL IMAGE
    |--------------------------------------------------------------------------
    */

    if (
        preg_match(
            '/^https?:\/\//i',
            $image
        )
    ) {

        return $image;
    }


    /*
    |--------------------------------------------------------------------------
    | CLEAN PATH
    |--------------------------------------------------------------------------
    */

    $cleanImage = str_replace(
        '\\',
        '/',
        $image
    );

    $cleanImage = ltrim(
        $cleanImage,
        '/'
    );


    /*
    |--------------------------------------------------------------------------
    | POSSIBLE IMAGE LOCATIONS
    |--------------------------------------------------------------------------
    |
    | We check several possible structures used by CORE LTD.
    |
    */

    $possiblePaths = [];


    /*
    | Exact database path
    */

    $possiblePaths[] = $cleanImage;


    /*
    | images/filename.jpg
    */

    $possiblePaths[] =
        'images/' . basename($cleanImage);


    /*
    | images/product/filename.jpg
    */

    $possiblePaths[] =
        'images/product/' . basename($cleanImage);


    /*
    | images/product/small-size/filename.jpg
    */

    $possiblePaths[] =
        'uploads/products/' .
        basename($cleanImage);


    /*
    | images/product/large-size/filename.jpg
    */

    $possiblePaths[] =
        'images/product/large-size/' .
        basename($cleanImage);


    /*
    | product/filename.jpg
    */

    $possiblePaths[] =
        'product/' . basename($cleanImage);


    /*
    | product/small-size/filename.jpg
    */

    $possiblePaths[] =
        'product/small-size/' .
        basename($cleanImage);


    /*
    |--------------------------------------------------------------------------
    | REMOVE DUPLICATES
    |--------------------------------------------------------------------------
    */

    $possiblePaths = array_unique(
        $possiblePaths
    );


    /*
    |--------------------------------------------------------------------------
    | CHECK WHICH FILE ACTUALLY EXISTS
    |--------------------------------------------------------------------------
    */

    foreach ($possiblePaths as $path) {

        $fullPath =
            __DIR__ .
            '/' .
            $path;

        if (
            file_exists($fullPath) &&
            is_file($fullPath)
        ) {

            return $path;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | TRY CASE-INSENSITIVE SEARCH
    |--------------------------------------------------------------------------
    |
    | Windows/XAMPP can sometimes make this confusing when the database
    | contains a slightly different filename.
    |
    */

    $fileName = basename($cleanImage);

    $searchDirectories = [

        __DIR__ . '/uploads/products/',
        __DIR__ . '/images/product/',
        __DIR__ . '/images/',
        __DIR__ . '/product/',
        __DIR__ . '/product/small-size/'

    ];


    foreach ($searchDirectories as $directory) {

        if (!is_dir($directory)) {
            continue;
        }


        $files = scandir($directory);


        if ($files === false) {
            continue;
        }


        foreach ($files as $file) {

            if (
                $file === '.' ||
                $file === '..'
            ) {
                continue;
            }


            if (
                strtolower($file) ===
                strtolower($fileName)
            ) {

                /*
                | Convert filesystem path to browser path
                */

                $relativePath =
                    str_replace(
                        __DIR__ . '/',
                        '',
                        $directory . $file
                    );

                $relativePath =
                    str_replace(
                        '\\',
                        '/',
                        $relativePath
                    );

                return $relativePath;
            }
        }
    }


    /*
    |--------------------------------------------------------------------------
    | FALLBACK
    |--------------------------------------------------------------------------
    */

    if (
        file_exists(
            __DIR__ . '/images/no-image.jpg'
        )
    ) {

        return 'images/no-image.jpg';
    }


    return 'images/no-image.jpg';
}


/*
|--------------------------------------------------------------------------
| GET BRAND SLUG
|--------------------------------------------------------------------------
*/

$slug = isset($_GET['slug'])
    ? trim($_GET['slug'])
    : '';


if ($slug === '') {

    http_response_code(404);

    exit('Brand not found.');
}


/*
|--------------------------------------------------------------------------
| GET BRAND
|--------------------------------------------------------------------------
*/

$brandStmt = $conn->prepare("
    SELECT
        id,
        name,
        slug,
        description
    FROM brands
    WHERE slug = ?
    LIMIT 1
");


if (!$brandStmt) {

    die("Brand query failed: " .
        $conn->error);
}


$brandStmt->bind_param(
    "s",
    $slug
);


$brandStmt->execute();


$brandResult =
    $brandStmt->get_result();


if (
    $brandResult->num_rows === 0
) {

    $brandStmt->close();

    http_response_code(404);

    exit('Brand not found.');
}


$brand =
    $brandResult->fetch_assoc();


$brandStmt->close();


/*
|--------------------------------------------------------------------------
| GET PRODUCTS FOR THIS BRAND
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

        c.name AS category_name,
        c.slug AS category_slug

    FROM products p

    LEFT JOIN brands b
        ON p.brand_id = b.id

    LEFT JOIN categories c
        ON p.category_id = c.id

    WHERE p.brand_id = ?
      AND p.status = 1

    ORDER BY p.created_at DESC
");


if (!$productStmt) {

    die("Product query failed: " .
        $conn->error);
}


$productStmt->bind_param(
    "i",
    $brand['id']
);


$productStmt->execute();


$productResult =
    $productStmt->get_result();


$products = [];


while (
    $row =
    $productResult->fetch_assoc()
) {

    $products[] = $row;
}


$productStmt->close();


/*
|--------------------------------------------------------------------------
| PAGE TITLE
|--------------------------------------------------------------------------
*/

$pageTitle =
    e($brand['name']) .
    ' Products | CORE LTD';


/*
|--------------------------------------------------------------------------
| HEADER
|--------------------------------------------------------------------------
|
| IMPORTANT:
| HEADER IS INCLUDED ONLY ONCE.
|
*/

include __DIR__ . '/header.php';

?>


<!-- =========================================================
     FONT AWESOME
========================================================= -->

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">


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

                    <?= e($brand['name']); ?>

                </li>

            </ul>

        </div>

    </div>

</div>


<!-- =========================================================
     CONTENT WRAPPER
========================================================= -->




<!-- =================================================
                     PRODUCTS
                ================================================= -->

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
                                    urlencode(
                                        $product['slug']
                                    );


                                /*
                                                |--------------------------------------------------------------------------
                                                | RESOLVE MAIN IMAGE
                                                |--------------------------------------------------------------------------
                                                */

                                $image =
                                    resolveProductImage(
                                        $product['main_image']
                                    );


                                /*
                                                |--------------------------------------------------------------------------
                                                | PRICE
                                                |--------------------------------------------------------------------------
                                                */

                                $price =
                                    (float)
                                    $product['price'];


                                /*
                                                |--------------------------------------------------------------------------
                                                | OLD PRICE
                                                |--------------------------------------------------------------------------
                                                */

                                $oldPrice = 0;


                                if (
                                    isset(
                                        $product['old_price']
                                    ) &&
                                    $product['old_price'] !==
                                    null &&
                                    $product['old_price'] !==
                                    ''
                                ) {

                                    $oldPrice =
                                        (float)
                                        $product['old_price'];
                                }


                                /*
                                                |--------------------------------------------------------------------------
                                                | DISCOUNT
                                                |--------------------------------------------------------------------------
                                                */

                                $discount = 0;


                                if (
                                    $oldPrice > $price &&
                                    $oldPrice > 0
                                ) {

                                    $discount =
                                        round(
                                            (
                                                (
                                                    $oldPrice -
                                                    $price
                                                )
                                                /
                                                $oldPrice
                                            )
                                                *
                                                100
                                        );
                                }

                                ?>


                        <!-- =================================================
                                                     PRODUCT COLUMN
                                                ================================================= -->

                        <div class="col-lg-3 col-md-4 col-sm-6 mt-40">


                            <!-- =================================================
                                                         PRODUCT CARD
                                                    ================================================= -->

                            <div class="single-product-wrap">


                                <!-- =============================================
                                                             PRODUCT IMAGE
                                                        ============================================== -->

                                <div class="product-image">

                                    <a href="<?= e($productUrl); ?>">

                                        <img src="<?= e($image); ?>" alt="<?= e($product['name']); ?>" loading="lazy"
                                            onerror="this.onerror=null;this.src='images/no-image.jpg';">

                                    </a>


                                    <?php if ($discount > 0): ?>

                                    <span class="discount-percentage">

                                        -<?= $discount; ?>%

                                    </span>

                                    <?php endif; ?>

                                </div>


                                <!-- =============================================
                                                             PRODUCT DESCRIPTION
                                                        ============================================== -->

                                <div class="product_desc">

                                    <div class="product_desc_info">


                                        <!-- =====================================
                                                                     BRAND
                                                                ====================================== -->

                                        <div class="product-review">

                                            <h5 class="manufacturer">

                                                <?php if (
                                                            !empty($product['brand_name'])
                                                        ): ?>

                                                <a href="brand.php?slug=<?= urlencode(
                                                                                        $product['brand_slug']
                                                                                    ); ?>">

                                                    <?= e(
                                                                    $product['brand_name']
                                                                ); ?>

                                                </a>

                                                <?php else: ?>

                                                <a href="#">

                                                    CORE LTD

                                                </a>

                                                <?php endif; ?>

                                            </h5>


                                            <!-- =================================
                                                                         RATING
                                                                    ================================== -->

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


                                        <!-- =====================================
                                                                     PRODUCT NAME
                                                                ====================================== -->

                                        <h4>

                                            <a class="product_name" href="<?= e($productUrl); ?>">

                                                <?= e(
                                                            $product['name']
                                                        ); ?>

                                            </a>

                                        </h4>


                                        <!-- =====================================
                                                                     CATEGORY
                                                                ====================================== -->

                                        <?php if (
                                                    !empty($product['category_name'])
                                                ): ?>

                                        <div class="product-category-name">

                                            <a href="category.php?slug=<?= urlencode(
                                                                                        $product['category_slug']
                                                                                    ); ?>">

                                                <?= e(
                                                                $product['category_name']
                                                            ); ?>

                                            </a>

                                        </div>

                                        <?php endif; ?>


                                        <!-- =====================================
                                                                     SHORT DESCRIPTION
                                                                ====================================== -->

                                        <?php if (
                                                    !empty($product['short_description'])
                                                ): ?>

                                        <p class="product-short-description">

                                            <?= e(
                                                            $product['short_description']
                                                        ); ?>

                                        </p>

                                        <?php endif; ?>


                                        <!-- =====================================
                                                                     PRICE
                                                                ====================================== -->

                                        <div class="price-box">


                                            <?php if (
                                                        $oldPrice >
                                                        $price
                                                    ): ?>

                                            <span class="new-price new-price-2">

                                                Ksh.
                                                <?= number_format(
                                                                $price,
                                                                2
                                                            ); ?>

                                            </span>


                                            <span class="old-price">

                                                Ksh.
                                                <?= number_format(
                                                                $oldPrice,
                                                                2
                                                            ); ?>

                                            </span>


                                            <span class="discount-percentage">

                                                -<?= $discount; ?>%

                                            </span>


                                            <?php else: ?>


                                            <span class="new-price">

                                                Ksh.
                                                <?= number_format(
                                                                $price,
                                                                2
                                                            ); ?>

                                            </span>


                                            <?php endif; ?>


                                        </div>


                                    </div>


                                    <!-- =====================================
                                                                 ACTIONS
                                                            ====================================== -->

                                    <div class="add-actions">

                                        <ul class="add-actions-link">


                                            <!-- ADD TO CART -->

                                            <li class="add-to-cart">

                                                <a href="#" class="add-to-cart-link"
                                                    data-product-id="<?= (int)$product['id']; ?>">

                                                    <i class="fa fa-shopping-cart"></i>

                                                </a>

                                            </li>


                                            <!-- WISHLIST -->

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


                        <!-- =================================================
                                                 NO PRODUCTS
                                            ================================================= -->

                        <div class="col-lg-12">

                            <div class="no-products text-center">

                                <div class="no-products-icon">

                                    <i class="fa fa-cubes"></i>

                                </div>


                                <h2>

                                    No Products Found

                                </h2>


                                <p>

                                    There are currently no

                                    <?= e(
                                            $brand['name']
                                        ); ?>

                                    products available.

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