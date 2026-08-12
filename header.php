<?php

require_once __DIR__ . '/includes/cart-functions.php';

$headerCartCount = getCartCount();
$headerCartTotal = getCartTotal();

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

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="images/core.png" />
    <!-- Material Design Iconic Font-V2.2.0 -->
    <link rel="stylesheet" href="css/material-design-iconic-font.min.css" />
    <!-- Font Awesome -->
    <link rel="stylesheet" href="css/font-awesome.min.css" />
    <!-- Font Awesome Stars-->
    <link rel="stylesheet" href="css/fontawesome-stars.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
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
                    <di class="col-lg-9 pl-0 ml-sm-15 ml-xs-15">
                        <!-- Begin Header Middle Searchbox Area -->
                        <form action="#" class="hm-searchbox" onsubmit="return false;">

                            <input type="text" id="search-box" placeholder="Enter your search key ..."
                                autocomplete="off" />
                            <button class="li-btn" type="submit">
                                <i class="fa fa-search"></i>
                            </button>
                            <ul id="search-results"></ul>
                        </form>

                        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


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
                        <!-- Begin Header Bottom Menu Area -->
                        <div class="hb-menu hb-menu-2 d-xl-block">
                            <nav>
                                <ul>
                                    <li class="">
                                        <a href="index.php">Home</a>
                                    </li>
                                    <li class="megamenu-holder">
                                        <a href="index.php">Shop</a>
                                        <ul class="megamenu hb-megamenu">
                                            <li>
                                                <a href="index.php">Networking</a>
                                                <ul>
                                                    <li>
                                                        <a href="routers.php">Routers</a>
                                                    </li>
                                                    <li>
                                                        <a href="index.php">Switches</a>
                                                    </li>
                                                    <li>
                                                        <a href="access-points.php">Access Points</a>
                                                    </li>
                                                    <li>
                                                        <a href="miktrotik.php">Mikrotik</a>
                                                    </li>
                                                    <li><a href="index.php">Climbers</a></li>
                                                    <li>
                                                        <a href="index.php">Patch-Codes</a>
                                                    </li>
                                                    <li>
                                                        <a href="index.php">Safety Tools</a>
                                                    </li>
                                                </ul>
                                            </li>
                                            <li>
                                                <a href="index.php">Accessories</a>
                                                <ul>
                                                    <li>
                                                        <a href="index.php">Mobile Accessories</a>
                                                    </li>
                                                    <li>
                                                        <a href="index.php">Computer
                                                            Accessories</a>
                                                    </li>
                                                    <li>
                                                        <a href="index.php">CCTV Devices</a>
                                                    </li>
                                                    <li>
                                                        <a href="index.php">Tools</a>
                                                    </li>
                                                    <li>
                                                        <a href="index.php">Gaming</a>
                                                    </li>
                                                </ul>
                                            </li>
                                            <li>
                                                <a href="index.php">Stationery</a>
                                                <ul>
                                                    <li>
                                                        <a href="index.php">Envelops</a>
                                                    </li>
                                                    <li>
                                                        <a href="index.php">Exercise Books</a>
                                                    </li>
                                                    <li>
                                                        <a href="index.php">Rim Papers</a>
                                                    </li>
                                                    <li>
                                                        <a href="index.php">Sticker Papers</a>
                                                    </li>
                                                    <li>
                                                        <a href="index.php">PVC Papers</a>
                                                    </li>
                                                </ul>
                                            </li>
                                        </ul>
                                    </li>
                                    <!-- <li class=""><a href="blog-left-sidebar.html">Blog</a></li> -->
                                    <li class="megamenu-static-holder">
                                        <a href="index.php">Pages</a>
                                        <ul class="megamenu hb-megamenu">
                                            <li>
                                                <a href="index.php">Networking</a>
                                                <ul>
                                                    <li>
                                                        <a href="index.php">Mikrotik</a>
                                                    </li>
                                                    <li>
                                                        <a href="index.php">Access Points</a>
                                                    </li>
                                                    <li>
                                                        <a href="index.php">Routers</a>
                                                    </li>
                                                    <li>
                                                        <a href="cat6-cables.php">CAT-6 Cables</a>
                                                    </li>
                                                    <li><a href="index.php">Climbers</a></li>
                                                    <li>
                                                        <a href="index.php">Closures</a>
                                                    </li>
                                                    <li>
                                                        <a href="index.php">Switches</a>
                                                    </li>
                                                </ul>
                                            </li>
                                            <li>
                                                <a href="index.php">Stationaries</a>
                                                <ul>
                                                    <li>
                                                        <a href="index.php">Mobile Accessories</a>
                                                    </li>
                                                    <li>
                                                        <a href="index.php">Computer Accessories
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="index.php">Rim Papers</a>
                                                    </li>
                                                    <li>
                                                        <a href="index.php">Envelops</a>
                                                    </li>
                                                    <li>
                                                        <a href="index.php">Gaming</a>
                                                    </li>
                                                </ul>
                                            </li>
                                            <!-- <li>
                                                <a href="index.php">Account</a>
                                                <ul>
                                                    <li>
                                                        <a href="login-register.html">My Account</a>
                                                    </li>
                                                    <li><a href="checkout.html">Blog</a></li>
                                                    <li><a href="checkout.php">Checkout</a></li>
                                                    <li><a href="#">Wishlist</a></li>
                                                    <li>
                                                        <a href="shopping-cart.html">Shopping Cart</a>
                                                    </li>
                                                </ul>
                                            </li> -->
                                            <li>
                                                <a href="index.php">Profile</a>
                                                <ul>
                                                    <li><a href="contact.php">Contact</a></li>
                                                    <!--/<li><a href="about-us.php">About Us</a></li>-->
                                                    <li><a href="cart.php">Shopping Cart</a></li>
                                                    <li><a href="faq.php">FAQ</a></li>
                                                </ul>
                                            </li>
                                        </ul>
                                    </li>
                                    <!--<li><a href="about-us.php">About Us</a></li>-->
                                    <li><a href="contact.php">Contact</a></li>
                                    <!-- Begin Header Bottom Menu Information Area -->
                                    <!-- Header Bottom Menu Information Area End Here -->
                                </ul>
                            </nav>
                        </div>
                        <!-- Header Bottom Menu Area End Here -->
                    </div>
                </div>
            </div>
        </div>
        <!-- Header Bottom Area End Here -->
        <!-- Begin Mobile Menu Area -->
        <div class="mobile-menu-area d-lg-none d-xl-none col-12">
            <div class="container-fluid">
                <div class="row">
                    <div class="mobile-menu"></div>
                </div>
            </div>
        </div>
        <!-- Mobile Menu Area End Here -->
    </header>
    <!-- Header Area End Here -->
    <!-- Begin Slider With Category Menu Area -->
    <script>
        $(document).ready(function() {
            let items = [{
                    name: "Routers",
                    url: "routers.php"
                },
                {
                    name: "Easenet Cat 6 Indoor Cable",
                    url: "easenet.php"
                },
                {
                    name: "RDCOM Cat 6 Indoor Cable",
                    url: "rdcom.php"
                },
                {
                    name: "Splicing Machine",
                    url: "Optical-Fusion-splicer.php"
                },
                {
                    name: "CP Cat 6 Indoor Cable",
                    url: "cp-cable.php"
                },
                {
                    name: "RDCOM Cat 6 Outdoor Cable",
                    url: "rdcom-oudoor.php"
                },
                {
                    name: "TP-Link EAP-255 outdoor",
                    url: "tp-link-eap255-outdoor.php"
                },
                {
                    name: "SHK Cat 6 Indoor Cable",
                    url: "shk-cable.php"
                },
                {
                    name: "Gigalan Cat 6 Indoor Cable",
                    url: "gigalan-cable.php"
                },
                {
                    name: "Tenda OAP-1200",
                    url: "tenda-oap1200.php"
                },
                {
                    name: "Scoop Cat 6 Indoor Cable",
                    url: "scoop-cable.php"
                },
                {
                    name: "Drop Cable 1KM",
                    url: "drop-cable-1km.php"
                },
                {
                    name: "Drop Cable 1KM (4 Core)",
                    url: "drop-cable-1km-4core.php"
                },
                {
                    name: "Drop Cable 1KM (8 Core)",
                    url: "drop-cable-1km-8core.php"
                },
                {
                    name: "ADSS 12-CORE",
                    url: "adss-12-core.php"
                },
                {
                    name: "ADSS 24-CORE",
                    url: "adss-24-core.php"
                },
                {
                    name: "Drop Cable 2KM (Indoor)",
                    url: "drop-cable-2km-indoor.php"
                },
                {
                    name: "Starlink Gen 3 (Standard Kit)",
                    url: "startlink-gen-3.php"
                },
                {
                    name: "Drop Cable 2KM",
                    url: "drop-cable-2km.php"
                },

                {
                    name: "Drop Cable 2KM (4 Core)",
                    url: "drop-cable-2km-4core.php"
                },
                {
                    name: "Drop Cable 2KM (8 Core)",
                    url: "drop-cable-2km-8core.php"
                },
                {
                    name: "Tenda-F3",
                    url: "tenda-f3.php"
                },
                {
                    name: "Tenda-F6",
                    url: "tenda-f6.php"
                },
                {
                    name: "Ruijie RG-EW300N Router",
                    url: "ruijie-router.php"
                },
                {
                    name: "Ruijie RAP62-OD AX3000",
                    url: "ruijie-rap-62od.php"
                },
                {
                    name: "Tenda-AC5",
                    url: "tenda-ac5.php"
                },
                {
                    name: "Tenda-AC8",
                    url: "tenda-ac8.php"
                },
                {
                    name: "LiteBeam M5",
                    url: "lite-beam-m5.php"
                },
                {
                    name: "Ruijie-RAP6262(G)",
                    url: "ruijie-6262g.php"
                },
                {
                    name: "Power Beam",
                    url: "power-beam.php"
                },
                {
                    name: "Tenda-AC10",
                    url: "tenda-ac10.php"
                },
                {
                    name: "Access Points",
                    url: "access-points.php"
                },
                {
                    name: "Tenda-4G Lite",
                    url: "tenda-4g.php"
                },
                {
                    name: "D-Link Router",
                    url: "d-link.php"
                },
                {
                    name: "Tp-Link 844N",
                    url: "tp-link-844.php"
                },
                {
                    name: "Tp-Link 840N",
                    url: "tp-link-840.php"
                },
                {
                    name: "Huawei-Gpon",
                    url: "huawei-expon.php"
                },
                {
                    name: "Huawei EPon",
                    url: "huawei-expon.php"
                },

                {
                    name: "TP-Link EAP225-Indoor",
                    url: "tp-link-eap255-indoor.php"
                },
                {
                    name: "Mikrotik RB951",
                    url: "rb951.php"
                },
                {
                    name: "TP-Link EAP110-Indoor",
                    url: "tp-link-eap110-indoor.php"
                },
                {
                    name: "Mikrotik RB941-Hap-Lite",
                    url: "rb941.php"
                },
                {
                    name: "TP-Link EAP110-Outdoor",
                    url: "tp-link-eap110-0utdoor.php"
                },
                {
                    name: "Mikrotik RB760-IGS",
                    url: "rb760-igs.php"
                },
                {
                    name: "Mikrotik RB4011",
                    url: "rb4011.php"
                },
                {
                    name: "Crimping Tools",
                    url: "crimping-tools.php"
                },
                {
                    name: "Jakemy Crimping Tool",
                    url: "jakemy-crimping-tool.php"
                },
                {
                    name: "Dual-Modular Crimping Tool",
                    url: "dual-modular-crimping-tool.php"
                },
                {
                    name: "Mikrotik RB260GS",
                    url: "rbs60gs.php"
                },
                {
                    name: "Mikrotik SXTSQ Series",
                    url: "ssxtsq-series.php"
                },
                {
                    name: "Mikrotiks",
                    url: "mikrotik.php"
                },
                {
                    name: "Mikrotik L009",
                    url: "mikrotik-l009.php"
                }
            ];

            function showResults(query = "") {
                let resultsContainer = $("#search-results");
                resultsContainer.empty();

                // Always sort alphabetically before displaying
                let sortedItems = [...items].sort((a, b) => a.name.localeCompare(b.name));
                let filteredItems = sortedItems.filter(item => item.name.toLowerCase().includes(query));

                if (filteredItems.length > 0) {
                    filteredItems.forEach(item => {
                        resultsContainer.append(
                            `<li class="search-item" data-url="${item.url}">${item.name}</li>`);
                    });
                    resultsContainer.show();
                } else {
                    resultsContainer.hide();
                }
            }

            // Show all items in alphabetical order when clicking inside search box
            $("#search-box").on("focus", function() {
                showResults();
            });

            // Filter results while typing, still in alphabetical order
            $("#search-box").on("input", function() {
                let query = $(this).val().toLowerCase();
                showResults(query);
            });

            // Navigate when clicking a search result
            $(document).on("click", ".search-item", function() {
                window.location.href = $(this).data("url");
            });

            // Hide search results when clicking outside
            $(document).click(function(event) {
                if (!$(event.target).closest("#search-box, #search-results").length) {
                    $("#search-results").hide();
                }
            });
        });
    </script>
    <style>
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