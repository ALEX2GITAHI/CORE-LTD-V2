<?php
/* CORE LTD - Database Driven Header */
require_once __DIR__ . '/includes/cart-functions.php';

/* Load the project database connection. */
$dbFile = __DIR__ . '/includes/db.php';
if (file_exists($dbFile)) {
    require_once $dbFile;
}

$headerCartCount = getCartCount();
$headerCartTotal = getCartTotal();

/*
|--------------------------------------------------------------------------
| DATABASE CATALOGUE
|--------------------------------------------------------------------------
| categories: id, parent_id, name, slug, status, sort_order
| products:   id, category_id, name, slug, price, old_price, main_image,
|             featured, status
|--------------------------------------------------------------------------
*/
$headerCategories = [];
$headerProducts = [];

if (isset($conn) && $conn instanceof mysqli) {
    $categorySql = "SELECT id, parent_id, name, slug, status, sort_order
                    FROM categories
                    WHERE status = 1
                    ORDER BY sort_order ASC, name ASC";
    if ($result = $conn->query($categorySql)) {
        while ($row = $result->fetch_assoc()) {
            $headerCategories[] = $row;
        }
        $result->free();
    }

    $productSql = "SELECT id, category_id, name, slug, price, old_price, main_image
                   FROM products
                   WHERE status = 1
                   ORDER BY name ASC";
    if ($result = $conn->query($productSql)) {
        while ($row = $result->fetch_assoc()) {
            $headerProducts[] = $row;
        }
        $result->free();
    }
}

/* Build a parent/child category tree. */
$headerCategoryTree = [];
foreach ($headerCategories as $category) {
    $parentId = (int)($category['parent_id'] ?? 0);
    $headerCategoryTree[$parentId][] = $category;
}

function renderHeaderCategories(array $tree, int $parentId = 0, int $level = 0): void
{
    if (empty($tree[$parentId])) {
        return;
    }

    echo '<ul' . ($level === 0 ? ' class="header-db-category-list"' : '') . '>';

    foreach ($tree[$parentId] as $category) {
        $slug = rawurlencode($category['slug']);
        $name = htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8');
        $children = !empty($tree[(int)$category['id']]);
        $class = $children ? ' class="header-db-has-children"' : '';

        echo '<li' . $class . '>';
        echo '<a href="category.php?slug=' . $slug . '">' . $name . '</a>';

        if ($children) {
            renderHeaderCategories($tree, (int)$category['id'], $level + 1);
        }

        echo '</li>';
    }

    echo '</ul>';
}

$headerSearchJson = [];
foreach ($headerProducts as $product) {
    $headerSearchJson[] = [
        'name' => $product['name'],
        'url' => 'product.php?slug=' . rawurlencode($product['slug'])
    ];
}
?>
<!DOCTYPE html>
<html class="no-js" lang="zxx">
<!-- index-231:32-->

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <title>CORE LTD</title>
    <meta name="description" content="" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-5825696635101769"
        crossorigin="anonymous"></script>
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="images/core.png" />
    <!-- Material Design Iconic Font-V2.2.0 -->
    <link rel="stylesheet" href="css/material-design-iconic-font.min.css" />
    <!-- Font Awesome -->
    <link rel="stylesheet" href="css/font-awesome.min.css" />
    <!-- Font Awesome Stars-->
    <link rel="stylesheet" href="css/fontawesome-stars.css" />
    <!-- Meanmenu CSS -->
    <link rel="stylesheet" href="css/meanmenu.css" />
    <!-- owl carousel CSS -->
    <link rel="stylesheet" href="css/owl.carousel.min.css" />
    <!-- Slick Carousel CSS -->
    <link rel="stylesheet" href="css/slick.css" />
    <!-- Animate CSS -->
    <link rel="stylesheet" href="css/animate.css" />
    <!-- Jquery-ui CSS -->
    <link rel="stylesheet" href="css/jquery-ui.min.css" />
    <!-- Venobox CSS -->
    <link rel="stylesheet" href="css/venobox.css" />
    <!-- Nice Select CSS -->
    <link rel="stylesheet" href="css/nice-select.css" />
    <!-- Magnific Popup CSS -->
    <link rel="stylesheet" href="css/magnific-popup.css" />
    <!-- Bootstrap V4.1.3 Fremwork CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <!-- Helper CSS -->
    <link rel="stylesheet" href="css/helper.css" />
    <!-- Main Style CSS -->
    <link rel="stylesheet" href="style.css" />
    <!-- Responsive CSS -->
    <link rel="stylesheet" href="css/responsive.css" />
    <!-- Modernizr js -->
    <script src="js/vendor/modernizr-2.8.3.min.js"></script>
    <?php
    header('Content-Type: text/html; charset=UTF-8');
    ?>
</head>

<body>
    <!--[if lt IE 8]>
      <p class="browserupgrade">
        You are using an <strong>outdated</strong> browser. Please
        <a href="http://browsehappy.com/">upgrade your browser</a> to improve
        your experience.
      </p>
    <![endif]-->
    <!-- Begin Body Wrapper -->

    <!-- Begin Header Area -->
    <header>

        <!-- Header Top Area End Here -->
        <!-- Begin Header Middle Area -->
        <div class="header-middle pl-sm-0 pr-sm-0 pl-xs-0 pr-xs-0">
            <div class="container-fluid">
                <div class="row">
                    <!-- Begin Header Logo Area -->
                    <div class="col-lg-3">
                        <div class="logo pb-sm-30 pb-xs-30">
                            <a href="index.php">
                                <img src="images/core_logo.png" alt="Core Logo" width="100" height="50" />
                            </a>
                        </div>
                    </div>
                    <!-- Header Logo Area End Here -->
                    <!-- Begin Header Middle Right Area -->
                    <div class="col-lg-9 pl-0 ml-sm-15 ml-xs-15">
                        <!-- Begin Header Middle Searchbox Area -->
                        <form action="#" class="hm-searchbox" id="header-search-form" onsubmit="return false;">

                            <input type="text" id="search-box" placeholder="Enter your search key ..."
                                autocomplete="off" />

                            <button class="li-btn" type="submit">
                                <i class="fa fa-search"></i>
                            </button>

                            <ul id="search-results"></ul>

                        </form>

                        <script>
                        document.addEventListener("DOMContentLoaded", function() {

                            const searchBox = document.getElementById("search-box");
                            const searchResults = document.getElementById("search-results");
                            const searchForm = document.getElementById("header-search-form");

                            /* All products loaded from PHP */
                            const allProducts = <?= json_encode(
                                                        $headerSearchJson,
                                                        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
                                                    ) ?>;

                            /*
                             * Display products
                             * If searchTerm is empty -> show ALL products
                             * If searchTerm has text -> filter products
                             */
                            function displaySearchResults(searchTerm = "") {

                                const term = searchTerm.trim().toLowerCase();

                                let products = allProducts;

                                /* Filter when user types */
                                if (term !== "") {
                                    products = allProducts.filter(function(product) {
                                        return product.name.toLowerCase().includes(term);
                                    });
                                }

                                /* Clear old results */
                                searchResults.innerHTML = "";

                                /* No results */
                                if (products.length === 0) {

                                    const noResults = document.createElement("li");

                                    noResults.className = "search-no-results";
                                    noResults.textContent = "No products found.";

                                    searchResults.appendChild(noResults);

                                    searchResults.style.display = "block";

                                    return;
                                }

                                /*
                                 * Show count at the top
                                 */
                                const count = document.createElement("li");

                                count.className = "search-count";

                                if (term === "") {
                                    count.textContent = "All Products (" + products.length + ")";
                                } else {
                                    count.textContent = products.length + " product(s) found";
                                }

                                searchResults.appendChild(count);

                                /*
                                 * Show EVERY product
                                 *
                                 * IMPORTANT:
                                 * There is NO array_slice()
                                 * There is NO limit such as 10, 20 or 50.
                                 */
                                products.forEach(function(product) {

                                    const li = document.createElement("li");

                                    const link = document.createElement("a");

                                    link.href = product.url;
                                    link.textContent = product.name;

                                    li.appendChild(link);

                                    searchResults.appendChild(li);
                                });

                                /* Show dropdown */
                                searchResults.style.display = "block";
                            }


                            /*
                             * When user clicks/focuses on search box,
                             * show ALL products.
                             */
                            searchBox.addEventListener("focus", function() {
                                displaySearchResults(searchBox.value);
                            });


                            /*
                             * When user types,
                             * filter the complete product list.
                             */
                            searchBox.addEventListener("input", function() {
                                displaySearchResults(searchBox.value);
                            });


                            /*
                             * Keep dropdown open when clicking inside it.
                             */
                            searchResults.addEventListener("mousedown", function(event) {
                                event.preventDefault();
                            });


                            /*
                             * Hide results when clicking somewhere else.
                             */
                            document.addEventListener("click", function(event) {

                                if (
                                    !searchForm.contains(event.target)
                                ) {
                                    searchResults.style.display = "none";
                                }

                            });


                            /*
                             * If user presses ESC, close the dropdown.
                             */
                            searchBox.addEventListener("keydown", function(event) {

                                if (event.key === "Escape") {
                                    searchResults.style.display = "none";
                                    searchBox.blur();
                                }

                            });

                        });
                        </script>


                        <!-- Header Middle Searchbox Area End Here -->
                        <!-- Begin Header Middle Right Area -->
                        <div class="header-middle-right">
                            <ul class="hm-menu">
                                <!-- Begin Header Middle Wishlist Area -->
                                <!-- <li class="hm-wishlist">
                                    <a href="#">
                                        <span class="cart-item-count wishlist-item-count">0</span>
                                        <i class="fa fa-heart-o"></i>
                                    </a>
                                </li> -->
                                <!-- Header Middle Wishlist Area End Here -->
                                <!-- Begin Header Mini Cart Area -->
                                <?php
                                /*
|--------------------------------------------------------------------------
| MINI CART DATA
|--------------------------------------------------------------------------
*/

                                $miniCart = getCart();
                                $miniCartCount = getCartCount();
                                $miniCartTotal = getCartTotal();
                                ?>

                                <li class="hm-minicart">

                                    <!-- CART HEADER -->
                                    <div class="hm-minicart-trigger">

                                        <span class="item-icon"></span>

                                        <span class="item-text">

                                            Ksh.

                                            <span id="cart-total">
                                                <?= number_format($miniCartTotal, 0) ?>
                                            </span>

                                            <span class="cart-item-count">
                                                <?= (int) $miniCartCount ?>
                                            </span>

                                        </span>

                                    </div>


                                    <!-- MINI CART DROPDOWN -->
                                    <div class="minicart">

                                        <ul class="minicart-product-list" id="cart-items">

                                            <?php if (empty($miniCart)): ?>

                                            <li class="mini-cart-empty" style="
                        padding:15px 0;
                        text-align:left;
                    ">
                                                Your cart is empty.
                                            </li>

                                            <?php else: ?>

                                            <?php foreach ($miniCart as $item): ?>

                                            <?php

                                                    /*
                    |--------------------------------------------------------------------------
                    | PRODUCT IMAGE
                    |--------------------------------------------------------------------------
                    */

                                                    $miniImage = $item['image'] ?? '';

                                                    if ($miniImage !== '') {

                                                        /*
                        If database only stores:
                        Tenda F3.jpg

                        use:
                        uploads/products/Tenda F3.jpg
                        */

                                                        if (
                                                            strpos(
                                                                $miniImage,
                                                                'images/'
                                                            ) === 0
                                                        ) {

                                                            $miniImageUrl = $miniImage;
                                                        } else {

                                                            $miniImageUrl =
                                                                'uploads/products/' .
                                                                $miniImage;
                                                        }
                                                    } else {

                                                        $miniImageUrl =
                                                            'uploads/products/default.png';
                                                    }

                                                    ?>

                                            <li class="mini-cart-product" data-product-id="<?= (int) $item['id'] ?>"
                                                style="
                            display:flex;
                            align-items:center;
                            gap:12px;
                            padding:12px 0;
                            border-bottom:1px solid #eee;
                        ">

                                                <!-- IMAGE -->

                                                <div style="
                                width:48px;
                                min-width:48px;
                                height:48px;
                                display:flex;
                                align-items:center;
                                justify-content:center;
                            ">

                                                    <img src="<?= htmlspecialchars($miniImageUrl) ?>"
                                                        alt="<?= htmlspecialchars($item['name']) ?>" style="
                                    width:48px;
                                    height:48px;
                                    object-fit:contain;
                                    display:block;
                                " onerror="
                                    this.onerror=null;
                                    this.src='uploads/products/default.png';
                                ">

                                                </div>


                                                <!-- PRODUCT DETAILS -->

                                                <div style="
                                flex:1;
                                min-width:0;
                            ">

                                                    <h6 style="
                                    margin:0 0 5px;
                                    font-size:14px;
                                    line-height:1.35;
                                    font-weight:500;
                                ">

                                                        <?= htmlspecialchars($item['name']) ?>

                                                    </h6>

                                                    <span style="
                                    font-size:13px;
                                    color:#555;
                                ">

                                                        Ksh.
                                                        <?= number_format($item['price'], 0) ?>

                                                        ×

                                                        <?= (int) $item['quantity'] ?>

                                                    </span>

                                                </div>


                                                <!-- REMOVE X -->

                                                <button type="button" class="mini-cart-remove"
                                                    data-product-id="<?= (int) $item['id'] ?>" title="Remove item"
                                                    style="
                                border:none;
                                background:none;
                                color:#aaa;
                                font-size:18px;
                                line-height:1;
                                padding:5px;
                                cursor:pointer;
                            ">

                                                    ×

                                                </button>

                                            </li>

                                            <?php endforeach; ?>

                                            <?php endif; ?>

                                        </ul>


                                        <!-- SUBTOTAL -->

                                        <p class="minicart-total" style="
                display:flex;
                justify-content:space-between;
                align-items:center;
                margin:15px 0;
                padding-top:10px;
                border-top:1px solid #ddd;
            ">

                                            <strong>
                                                SUBTOTAL:
                                            </strong>

                                            <strong>

                                                Ksh.

                                                <span id="cart-subtotal">
                                                    <?= number_format($miniCartTotal, 0) ?>
                                                </span>

                                            </strong>

                                        </p>


                                        <!-- BUTTONS -->

                                        <div class="minicart-button">

                                            <a href="cart.php" id="view-full-cart"
                                                class="li-button li-button-dark li-button-fullwidth li-button-sm">

                                                <span>
                                                    View Full Cart
                                                </span>

                                            </a>


                                            <a href="checkout.php" class="li-button li-button-fullwidth li-button-sm">

                                                <span>
                                                    Checkout
                                                </span>

                                            </a>

                                        </div>

                                    </div>

                                </li>
                            </ul>
                        </div>
                        <!-- Header Middle Right Area End Here -->
                    </div>
                    <!-- Header Middle Right Area End Here -->
                </div>
            </div>
        </div>
        <!-- Header Middle Area End Here -->
        <!-- Begin Header Bottom Area -->

        <div class="header-bottom header-sticky d-none d-lg-block">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">

                        <div class="hb-menu hb-menu-1 core-header-menu">
                            <nav>
                                <ul class="core-main-navigation">

                                    <!-- HOME -->
                                    <li>
                                        <a href="index.php">Home</a>
                                    </li>

                                    <!-- SHOP - THE ONLY DROPDOWN -->
                                    <li class="core-shop-megamenu-holder">
                                        <a href="products.php" class="core-shop-link">
                                            Shop
                                        </a>

                                        <ul class="megamenu hb-megamenu core-shop-megamenu">

                                            <?php if (!empty($headerCategoryTree[0])): ?>

                                            <?php foreach ($headerCategoryTree[0] as $topCategory):

                                                    $topId = (int)$topCategory['id'];

                                                    $topName = htmlspecialchars(
                                                        $topCategory['name'],
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    );

                                                    $topSlug = rawurlencode($topCategory['slug']);

                                                ?>

                                            <li class="core-shop-column">

                                                <a href="category.php?slug=<?= $topSlug ?>"
                                                    class="core-shop-main-category">
                                                    <?= $topName ?>
                                                </a>

                                                <?php if (!empty($headerCategoryTree[$topId])): ?>

                                                <ul class="core-shop-subcategories">

                                                    <?php foreach ($headerCategoryTree[$topId] as $childCategory):

                                                                    $childId = (int)$childCategory['id'];

                                                                    $childName = htmlspecialchars(
                                                                        $childCategory['name'],
                                                                        ENT_QUOTES,
                                                                        'UTF-8'
                                                                    );

                                                                    $childSlug = rawurlencode(
                                                                        $childCategory['slug']
                                                                    );

                                                                ?>

                                                    <li>

                                                        <a href="category.php?slug=<?= $childSlug ?>">
                                                            <?= $childName ?>
                                                        </a>

                                                        <?php if (!empty($headerCategoryTree[$childId])): ?>

                                                        <ul class="core-shop-third-level">

                                                            <?php foreach ($headerCategoryTree[$childId] as $grandChildCategory):

                                                                                    $grandChildName = htmlspecialchars(
                                                                                        $grandChildCategory['name'],
                                                                                        ENT_QUOTES,
                                                                                        'UTF-8'
                                                                                    );

                                                                                    $grandChildSlug = rawurlencode(
                                                                                        $grandChildCategory['slug']
                                                                                    );

                                                                                ?>

                                                            <li>
                                                                <a href="category.php?slug=<?= $grandChildSlug ?>">
                                                                    <?= $grandChildName ?>
                                                                </a>
                                                            </li>

                                                            <?php endforeach; ?>

                                                        </ul>

                                                        <?php endif; ?>

                                                    </li>

                                                    <?php endforeach; ?>

                                                </ul>

                                                <?php endif; ?>

                                            </li>

                                            <?php endforeach; ?>

                                            <?php else: ?>

                                            <li class="core-shop-column">
                                                <a href="products.php">
                                                    All Products
                                                </a>
                                            </li>

                                            <?php endif; ?>

                                        </ul>
                                    </li>

                                    <!-- ABOUT US -->
                                    <li>
                                        <a href="about.php">About Us</a>
                                    </li>

                                    <!-- SERVICES -->
                                    <li>
                                        <a href="services.php">Services</a>
                                    </li>

                                    <!-- CONTACT -->
                                    <li>
                                        <a href="contact.php">Contact</a>
                                    </li>

                                    <!-- EVENTS -->
                                    <li>
                                        <a href="events.php">Events</a>
                                    </li>

                                </ul>
                            </nav>
                        </div>

                    </div>
                </div>
            </div>
        </div>


        <!-- MOBILE MENU -->
        <div class="mobile-menu-area d-lg-none d-xl-none col-12">
            <div class="container-fluid">
                <div class="row">
                    <div class="mobile-menu"></div>
                </div>
            </div>
        </div>


        <style>
        /* ==========================================
   CORE LTD - HEADER NAVIGATION
========================================== */

        .core-header-menu {
            display: block !important;
            width: 100%;
            position: relative;
            z-index: 1000;
        }

        .core-header-menu nav {
            width: 100%;
        }

        .core-header-menu nav>ul.core-main-navigation {
            display: flex !important;
            align-items: center !important;
            justify-content: flex-start !important;
            gap: 34px !important;

            list-style: none !important;
            margin: 0 !important;
            padding: 0 10px !important;

            width: 100%;
            min-height: 55px;
        }

        .core-main-navigation>li {
            display: flex !important;
            align-items: center !important;
            position: relative;
            padding: 0 !important;
            margin: 0 !important;
            list-style: none !important;
            flex: 0 0 auto !important;
        }

        /* ==========================================
   TOP LEVEL LINKS
========================================== */

        .core-main-navigation>li>a {
            display: inline-flex !important;
            align-items: center !important;

            padding: 17px 0 !important;
            margin: 0 !important;

            color: #17324d !important;
            font-size: 14px !important;
            font-weight: 600 !important;

            text-transform: uppercase !important;
            text-decoration: none !important;
            white-space: nowrap !important;

            line-height: 20px !important;
            letter-spacing: 0.2px;

            transition: color 0.25s ease;
        }

        /* Remove all theme-generated dropdown arrows */

        .core-main-navigation>li>a::before,
        .core-main-navigation>li>a::after {
            content: none !important;
            display: none !important;
            background: none !important;
        }

        /* Hover */

        .core-main-navigation>li>a:hover {
            color: #078bdb !important;
        }

        /* ==========================================
   SHOP DROPDOWN ARROW ONLY
========================================== */

        .core-main-navigation>li.core-shop-megamenu-holder>a {
            gap: 12px !important;
        }

        .core-main-navigation>li.core-shop-megamenu-holder>a::after {
            content: "" !important;
            display: inline-block !important;

            width: 9px;
            height: 9px;

            border-right: 2px solid #17324d;
            border-bottom: 2px solid #17324d;

            transform: rotate(45deg);
            margin-top: -5px;

            transition: transform 0.25s ease;
        }

        .core-shop-megamenu-holder:hover>a::after,
        .core-shop-megamenu-holder:focus-within>a::after {
            transform: rotate(225deg);
            margin-top: 5px;
        }

        /* ==========================================
   SHOP MEGA MENU
========================================== */

        .core-shop-megamenu-holder {
            position: relative !important;
        }

        .core-shop-megamenu-holder>.core-shop-megamenu {
            position: absolute !important;

            top: 100% !important;
            left: 0 !important;

            width: min(1100px, calc(100vw - 40px)) !important;
            min-width: 0 !important;
            max-width: calc(100vw - 40px) !important;

            display: grid !important;
            grid-template-columns: repeat(4, minmax(0, 1fr));

            gap: 28px !important;

            padding: 30px !important;
            margin: 0 !important;

            background: #ffffff !important;

            border: none !important;
            border-top: 3px solid #5ccdf0 !important;

            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.13) !important;

            visibility: hidden;
            opacity: 0;
            pointer-events: none;

            transform: translateY(10px);

            transition: opacity 0.25s ease,
                visibility 0.25s ease,
                transform 0.25s ease;

            z-index: 99999 !important;
        }

        /* Show Shop menu only on hover or keyboard focus */

        .core-shop-megamenu-holder:hover>.core-shop-megamenu,
        .core-shop-megamenu-holder:focus-within>.core-shop-megamenu {
            visibility: visible;
            opacity: 1;
            pointer-events: auto;
            transform: translateY(0);
        }

        /* ==========================================
   CATEGORY COLUMNS
========================================== */

        .core-shop-megamenu>.core-shop-column {
            display: block !important;
            width: auto !important;
            padding: 0 !important;
            margin: 0 !important;
            position: relative !important;
            list-style: none !important;
        }

        /* Main category */

        .core-shop-main-category {
            display: block !important;

            padding: 0 0 12px !important;
            margin-bottom: 10px !important;

            color: #17324d !important;
            font-size: 15px !important;
            font-weight: 700 !important;

            text-transform: uppercase !important;

            border-bottom: 2px solid #eeeeee !important;
            white-space: normal !important;
        }

        .core-shop-main-category:hover {
            color: #078bdb !important;
        }

        /* ==========================================
   SECOND LEVEL CATEGORIES
========================================== */

        .core-shop-subcategories {
            display: block !important;

            position: static !important;

            width: 100% !important;
            min-width: 0 !important;

            padding: 0 !important;
            margin: 0 !important;

            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
        }

        .core-shop-subcategories>li {
            display: block !important;
            width: 100% !important;

            padding: 0 !important;
            margin: 0 !important;

            position: relative !important;
            list-style: none !important;
        }

        .core-shop-subcategories>li>a {
            display: block !important;

            padding: 6px 0 !important;

            color: #555555 !important;
            font-size: 13px !important;
            font-weight: 400 !important;

            text-transform: none !important;
            white-space: normal !important;

            transition: all 0.2s ease;
        }

        .core-shop-subcategories>li>a:hover {
            color: #078bdb !important;
            padding-left: 6px !important;
        }

        /* ==========================================
   THIRD LEVEL CATEGORIES
========================================== */

        .core-shop-third-level {
            display: block !important;

            position: static !important;

            width: 100% !important;

            padding: 0 0 0 12px !important;
            margin: 0 !important;

            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
        }

        .core-shop-third-level li {
            display: block !important;
            width: 100% !important;

            padding: 0 !important;
            margin: 0 !important;

            list-style: none !important;
        }

        .core-shop-third-level li a {
            display: block !important;

            padding: 5px 0 !important;

            color: #777777 !important;
            font-size: 12px !important;
            font-weight: 400 !important;

            text-transform: none !important;
        }

        .core-shop-third-level li a:hover {
            color: #078bdb !important;
            padding-left: 5px !important;
        }

        /* ==========================================
   PREVENT OTHER NAVIGATION DROPDOWNS
========================================== */

        .core-main-navigation>li:not(.core-shop-megamenu-holder)>ul {
            display: none !important;
        }

        /* ==========================================
   MEDIUM DESKTOP
========================================== */

        @media (min-width: 992px) and (max-width: 1199px) {

            .core-main-navigation {
                gap: 23px !important;
            }

            .core-shop-megamenu-holder>.core-shop-megamenu {
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 20px !important;
                padding: 25px !important;
            }

        }

        /* ==========================================
   SMALL DESKTOP
========================================== */

        @media (min-width: 992px) and (max-width: 1050px) {

            .core-main-navigation {
                gap: 16px !important;
            }

            .core-main-navigation>li>a {
                font-size: 12px !important;
            }

        }
        </style>

    </header>
    <!-- Header Area End Here -->
    <!-- Begin Slider With Category Menu Area -->
    <script>
    $(document).ready(function() {
        const items =
            <?php echo json_encode($headerSearchJson, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>;
        const $searchBox = $('#search-box');
        const $results = $('#search-results');

        function escapeHtml(value) {
            return $('<div>').text(value || '').html();
        }

        function showResults(query = '') {
            const cleanQuery = query.trim().toLowerCase();
            const filtered = items
                .filter(item => item.name.toLowerCase().includes(cleanQuery))
                .slice(0, 15);

            $results.empty();

            if (!filtered.length) {
                $results.hide();
                return;
            }

            filtered.forEach(item => {
                $results.append(
                    `<li class="search-item" data-url="${escapeHtml(item.url)}">${escapeHtml(item.name)}</li>`
                );
            });

            $results.show();
        }

        $searchBox.on('focus input', function() {
            showResults($(this).val());
        });

        $(document).on('click', '.search-item', function(e) {
            e.preventDefault();
            window.location.href = $(this).data('url');
        });

        $(document).on('click', function(event) {
            if (!$(event.target).closest('#search-box, #search-results').length) {
                $results.hide();
            }
        });
    });
    </script>
    <style>
    .header-db-shop-menu {
        min-width: 100%;
    }

    .header-db-shop-menu>li>a {
        font-weight: 600;
    }

    .header-db-shop-menu li a {
        white-space: nowrap;
    }

    .header-db-shop-menu ul {
        margin-top: 5px;
    }

    .header-db-shop-menu li ul li a {
        display: block;
    }

    .header-db-category-list {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .header-db-category-list li {
        position: relative;
    }

    .hm-searchbox {
        position: relative;
        width: 60%;
        max-width: 400px;
    }

    #search-results {
        position: absolute;
        top: 100%;
        left: 0;
        width: 100%;
        background: white;
        border: 1px solid #ccc;
        display: none;
        max-height: 200px;
        overflow-y: auto;
        z-index: 1000;
        border-radius: 5px;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
    }

    .search-item {
        padding: 10px;
        cursor: pointer;
        border-bottom: 1px solid #eee;
        transition: background 0.3s, color 0.3s;
    }

    .search-item:hover {
        background: #5ccdf0;
        color: white;
    }

    /* =========================================================
   CORE LTD HEADER SEARCH
   ========================================================= */

    .hm-searchbox {
        position: relative;
    }

    #search-results {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;

        margin: 0;
        padding: 0;

        background: #fff;
        border: 1px solid #e5e5e5;

        max-height: 420px;
        overflow-y: auto;
        overflow-x: hidden;

        z-index: 99999;

        list-style: none;

        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.12);
    }

    /* Each product */
    #search-results li {
        margin: 0;
        padding: 0;
        list-style: none;
        border-bottom: 1px solid #eeeeee;
    }

    /* Product link */
    #search-results li a {
        display: block;

        padding: 12px 16px;

        color: #333;
        background: #fff;

        font-size: 14px;
        line-height: 1.4;

        text-decoration: none;

        transition: background 0.2s ease;
    }

    /* Hover */
    #search-results li a:hover {
        background: #f5f5f5;
        color: #007bff;
    }

    /* Scrollbar */
    #search-results::-webkit-scrollbar {
        width: 7px;
    }

    #search-results::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    #search-results::-webkit-scrollbar-thumb {
        background: #bbb;
        border-radius: 10px;
    }

    #search-results::-webkit-scrollbar-thumb:hover {
        background: #999;
    }

    /* Firefox */
    #search-results {
        scrollbar-width: thin;
    }

    /* No results */
    #search-results li.search-no-results {
        padding: 14px 16px;
        color: #777;
        font-size: 14px;
        background: #fff;
    }

    /* Number of results */
    #search-results li.search-count {
        padding: 9px 16px;
        background: #f8f8f8;
        color: #888;
        font-size: 12px;
        font-weight: 600;
        position: sticky;
        top: 0;
        z-index: 2;
    }
    </style>
    </style>
    <script>
    (function() {

        /*
        |--------------------------------------------------------------------------
        | FORMAT MONEY
        |--------------------------------------------------------------------------
        */

        function formatMoney(amount) {

            amount = parseFloat(amount) || 0;

            return amount.toLocaleString();

        }


        /*
        |--------------------------------------------------------------------------
        | BUILD IMAGE URL
        |--------------------------------------------------------------------------
        */

        function getCartImage(image) {

            if (!image) {

                return 'uploads/products/default.png';

            }


            if (image.indexOf('images/') === 0) {

                return image;

            }


            return 'uploads/products/' + image;

        }


        /*
        |--------------------------------------------------------------------------
        | UPDATE HEADER MINI CART
        |--------------------------------------------------------------------------
        */

        window.updateHeaderMiniCart = function(cart, cartCount, cartTotal) {

            const container =
                $('#cart-items');


            if (!container.length) {

                return;

            }


            container.empty();


            /*
            |--------------------------------------------------------------------------
            | EMPTY CART
            |--------------------------------------------------------------------------
            */

            if (!cart || Object.keys(cart).length === 0) {

                container.append(`

                <li
                    class="mini-cart-empty"
                    style="
                        padding:15px 0;
                        text-align:left;
                    "
                >

                    Your cart is empty.

                </li>

            `);

            } else {


                /*
                |--------------------------------------------------------------------------
                | PRODUCTS
                |--------------------------------------------------------------------------
                */

                Object.values(cart).forEach(function(item) {


                    const image =
                        getCartImage(item.image);


                    container.append(`

                    <li
                        class="mini-cart-product"
                        data-product-id="${item.id}"
                        style="
                            display:flex;
                            align-items:center;
                            gap:12px;
                            padding:12px 0;
                            border-bottom:1px solid #eee;
                        "
                    >

                        <div
                            style="
                                width:48px;
                                min-width:48px;
                                height:48px;
                                display:flex;
                                align-items:center;
                                justify-content:center;
                            "
                        >

                            <img
                                src="${image}"
                                alt="${escapeHtml(item.name)}"
                                style="
                                    width:48px;
                                    height:48px;
                                    object-fit:contain;
                                    display:block;
                                "
                                onerror="
                                    this.onerror=null;
                                    this.src='uploads/products/default.png';
                                "
                            >

                        </div>


                        <div
                            style="
                                flex:1;
                                min-width:0;
                            "
                        >

                            <h6
                                style="
                                    margin:0 0 5px;
                                    font-size:14px;
                                    line-height:1.35;
                                    font-weight:500;
                                "
                            >

                                ${escapeHtml(item.name)}

                            </h6>


                            <span
                                style="
                                    font-size:13px;
                                    color:#555;
                                "
                            >

                                Ksh.
                                ${formatMoney(item.price)}

                                ×

                                ${item.quantity}

                            </span>

                        </div>


                        <button
                            type="button"
                            class="mini-cart-remove"
                            data-product-id="${item.id}"
                            title="Remove item"
                            style="
                                border:none;
                                background:none;
                                color:#aaa;
                                font-size:18px;
                                line-height:1;
                                padding:5px;
                                cursor:pointer;
                            "
                        >

                            ×

                        </button>

                    </li>

                `);

                });

            }


            /*
            |--------------------------------------------------------------------------
            | UPDATE COUNT
            |--------------------------------------------------------------------------
            */

            $('.cart-item-count').text(
                parseInt(cartCount) || 0
            );


            /*
            |--------------------------------------------------------------------------
            | UPDATE TOTAL
            |--------------------------------------------------------------------------
            */

            $('#cart-total').text(
                formatMoney(cartTotal)
            );


            $('#cart-subtotal').text(
                formatMoney(cartTotal)
            );

        };


        /*
        |--------------------------------------------------------------------------
        | HTML ESCAPE
        |--------------------------------------------------------------------------
        */

        function escapeHtml(text) {

            return $('<div>')
                .text(text || '')
                .html();

        }


        /*
        |--------------------------------------------------------------------------
        | REMOVE FROM MINI CART
        |--------------------------------------------------------------------------
        */

        $(document).on(
            'click',
            '.mini-cart-remove',
            function(e) {

                e.preventDefault();

                e.stopPropagation();


                const button =
                    $(this);


                const productId =
                    parseInt(
                        button.data('product-id')
                    );


                if (!productId) {

                    return;

                }


                button.prop(
                    'disabled',
                    true
                );


                button.html(
                    '<i class="fa fa-spinner fa-spin"></i>'
                );


                $.ajax({

                    url: 'cart-action.php',

                    type: 'POST',

                    dataType: 'json',

                    data: {

                        action: 'remove',

                        product_id: productId

                    },


                    success: function(response) {


                        if (response.success) {


                            /*
                            |--------------------------------------------------------------------------
                            | UPDATE MINI CART WITHOUT REFRESH
                            |--------------------------------------------------------------------------
                            */

                            updateHeaderMiniCart(
                                response.cart,
                                response.cart_count,
                                response.cart_total
                            );


                            /*
                            |--------------------------------------------------------------------------
                            | UPDATE OTHER CART COUNTERS
                            |--------------------------------------------------------------------------
                            */

                            $('.cart-item-count').text(
                                response.cart_count
                            );


                            $('#cart-total').text(
                                formatMoney(
                                    response.cart_total
                                )
                            );


                            $('#cart-subtotal').text(
                                formatMoney(
                                    response.cart_total
                                )
                            );


                        } else {

                            alert(
                                response.message ||
                                'Unable to remove item.'
                            );


                            button.prop(
                                'disabled',
                                false
                            );


                            button.text('×');

                        }

                    },


                    error: function() {


                        alert(
                            'Unable to remove this item. Please try again.'
                        );


                        button.prop(
                            'disabled',
                            false
                        );


                        button.text('×');

                    }

                });

            }

        );

    })();
    </script>