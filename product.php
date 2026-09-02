<?php

/*
|--------------------------------------------------------------------------
| CORE LTD - DYNAMIC PRODUCT PAGE
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/product-data.php';

// IMPORTANT: product-data.php loads the requested product using the URL slug.
// Keep a protected copy before loading header.php because included files may use
// the generic $product variable and overwrite it.
$currentProduct = $product;
$currentImages = $images;
$currentFeatures = $features;
$currentSpecifications = $specifications;
$currentRelatedProducts = $relatedProducts;

include 'header.php';

?>

<!-- Begin Li's Breadcrumb Area -->
<div class="breadcrumb-area">

    <div class="container-fluid">

        <div class="breadcrumb-content">

            <ul>

                <li>
                    <a href="index.php">Home</a>
                </li>

                <?php if (!empty($currentProduct['category_name'])): ?>

                    <li>
                        <a href="category.php?slug=<?= urlencode($currentProduct['category_slug']) ?>">
                            <?= htmlspecialchars($currentProduct['category_name']) ?>
                        </a>
                    </li>

                <?php endif; ?>

                <li class="active">

                    <?= htmlspecialchars($currentProduct['name']) ?>

                </li>

            </ul>

        </div>

    </div>

</div>
<!-- Li's Breadcrumb Area End Here -->


<!-- content-wraper start -->

<div class="content-wraper">

    <div class="container-fluid">

        <div class="row single-product-area">


            <!-- =====================================================
                 PRODUCT IMAGES
            ====================================================== -->

            <div class="col-lg-5 col-md-6">

                <!-- Product Details Left -->

                <div class="product-details-left">


                    <!-- MAIN PRODUCT IMAGE SLIDER -->

                    <div class="product-details-images slider-navigation-1">

                        <?php foreach ($currentImages as $image): ?>

                            <div class="lg-image">

                                <a class="popup-img venobox vbox-item"
                                    href="uploads/products/<?= htmlspecialchars($image['image']) ?>" data-gall="myGallery">

                                    <img src="uploads/products/<?= htmlspecialchars($image['image']) ?>" alt="<?= htmlspecialchars(
                                                                                                                    !empty($image['alt_text'])
                                                                                                                        ? $image['alt_text']
                                                                                                                        : $currentProduct['name']
                                                                                                                ) ?>" />

                                </a>

                            </div>

                        <?php endforeach; ?>

                    </div>


                    <!-- THUMBNAILS -->

                    <div class="product-details-thumbs slider-thumbs-1">

                        <?php foreach ($currentImages as $image): ?>

                            <div class="sm-image">

                                <img src="uploads/products/<?= htmlspecialchars($image['image']) ?>" alt="<?= htmlspecialchars(
                                                                                                                !empty($image['alt_text'])
                                                                                                                    ? $image['alt_text']
                                                                                                                    : $currentProduct['name']
                                                                                                            ) ?>" />

                            </div>

                        <?php endforeach; ?>

                    </div>


                </div>

                <!--// Product Details Left -->

            </div>


            <!-- =====================================================
                 PRODUCT INFORMATION
            ====================================================== -->

            <div class="col-lg-7 col-md-6">

                <div class="product-details-view-content pt-60">

                    <div class="product-info">


                        <!-- PRODUCT NAME -->

                        <h4>

                            <a class="product_name">

                                <?= htmlspecialchars($currentProduct['name']) ?>

                            </a>

                        </h4>


                        <!-- REFERENCE -->

                        <?php if (!empty($currentProduct['sku'])): ?>

                            <span class="product-details-ref">

                                Reference:
                                <?= htmlspecialchars($currentProduct['sku']) ?>

                            </span>

                        <?php endif; ?>


                        <!-- RATING -->

                        <div class="rating-box pt-20">

                            <ul class="rating rating-with-review-item">

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

                                <li class="review-item">

                                    <a href="#">

                                        Read Review

                                    </a>

                                </li>

                                <li class="review-item">

                                    <a href="#">

                                        Write Review

                                    </a>

                                </li>

                            </ul>

                        </div>


                        <!-- PRICE -->

                        <div class="price-box pt-20">

                            <span class="new-price new-price-2">

                                Ksh.
                                <?= number_format($currentProduct['price'], 0) ?>

                            </span>

                            <!-- <?php if (
                                        !empty($currentProduct['old_price']) &&
                                        $currentProduct['old_price'] > $currentProduct['price']
                                    ): ?>

                                <span class="old-price">

                                    Ksh.
                                    <?= number_format($currentProduct['old_price'], 0) ?>

                                </span> -->

                        <?php endif; ?>

                        </div>


                        <!-- =================================================
                             FEATURES
                        ================================================== -->

                        <?php if (!empty($currentFeatures)): ?>

                            <div class="product-desc">

                                <div style="overflow-x:auto;">

                                    <h4>

                                        <b>

                                            Features:
                                            <?= htmlspecialchars($currentProduct['name']) ?>

                                        </b>

                                    </h4>


                                    <table style="
                                        width:100%;
                                        border-collapse:collapse;
                                        font-family:Arial,sans-serif;
                                        border:1px solid #ddd;
                                        ">

                                        <thead style="
                                            background:#f4f4f4;
                                            ">

                                            <tr>

                                                <th style="
                                                    text-align:left;
                                                    padding:12px;
                                                    border:1px solid #ddd;
                                                    width:30%;
                                                    ">

                                                    Feature

                                                </th>

                                                <th style="
                                                    text-align:left;
                                                    padding:12px;
                                                    border:1px solid #ddd;
                                                    ">

                                                    Benefit

                                                </th>

                                            </tr>

                                        </thead>


                                        <tbody>

                                            <?php foreach ($currentFeatures as $index => $feature): ?>

                                                <tr class="product-feature-row" style="
                                                    <?= $index >= 5
                                                        ? 'display:none;'
                                                        : '' ?>
                                                    ">

                                                    <td style="
                                                        padding:10px;
                                                        border:1px solid #ddd;
                                                        ">

                                                        <b>

                                                            <?= htmlspecialchars(
                                                                $feature['feature']
                                                            ) ?>

                                                        </b>

                                                    </td>


                                                    <td style="
                                                        padding:10px;
                                                        border:1px solid #ddd;
                                                        ">

                                                        <?= htmlspecialchars(
                                                            $feature['benefit']
                                                        ) ?>

                                                    </td>

                                                </tr>

                                            <?php endforeach; ?>

                                        </tbody>

                                    </table>


                                    <?php if (count($currentFeatures) > 5): ?>

                                        <div style="
                                            text-align:center;
                                            margin-top:10px;
                                            ">

                                            <button type="button" id="productFeatureBtn" onclick="toggleProductFeatures()"
                                                style="
                                                padding:8px 16px;
                                                background:#007BFF;
                                                color:#fff;
                                                border:none;
                                                border-radius:5px;
                                                cursor:pointer;
                                                ">

                                                View More

                                            </button>

                                        </div>

                                    <?php endif; ?>


                                </div>

                            </div>

                        <?php endif; ?>


                        <!-- =================================================
                             ADD TO CART
                        ================================================== -->

                        <div class="add-to-cart-area pt-30">

                            <button type="button" class="single-product-add-to-cart" id="addToCartButton"
                                data-product-id="<?= (int) $currentProduct['id'] ?>">

                                <i class="fa fa-shopping-cart"></i>

                                Add to Cart

                            </button>

                        </div>


                    </div>


                    <!-- =================================================
                         ADDITIONAL INFORMATION
                    ================================================== -->

                    <div class="single-add-to-cart">

                        <div class="product-additional-info pt-25">


                            <!-- WHATSAPP -->

                            <a href="#" id="whatsapp-link" target="_blank"
                                style="background-color:#25D366;color:#fff;padding:10px 15px;text-decoration:none;border-radius:5px;display:inline-block;">
                                Click to Order on WhatsApp
                            </a>
                            <script>
                                document.addEventListener("DOMContentLoaded", function() {

                                    const whatsappBtn = document.getElementById("whatsapp-link");

                                    if (!whatsappBtn) {
                                        console.log("WhatsApp button not found.");
                                        return;
                                    }

                                    whatsappBtn.addEventListener("click", async function(e) {

                                        e.preventDefault();

                                        const currentPageURL = window.location.href;

                                        const productName =
                                            document.querySelector(".product_name")?.textContent
                                            .trim() ||
                                            "this product";

                                        const productPrice =
                                            document.querySelector(".new-price")?.textContent.trim() ||
                                            "";

                                        try {

                                            const response = await fetch("whatsapp.php");

                                            if (!response.ok) {
                                                throw new Error("Failed to load whatsapp.php");
                                            }

                                            const phoneNumber = (await response.text()).trim();

                                            console.log("Using Number:", phoneNumber);

                                            const message = encodeURIComponent(
                                                `Hello CORE LTD
I'm interested in buying:

Product: ${productName}
Price: ${productPrice}

Link: ${currentPageURL}`
                                            );

                                            window.open(
                                                `https://wa.me/${phoneNumber}?text=${message}`,
                                                "_blank"
                                            );

                                        } catch (error) {

                                            console.error(error);
                                            alert("Error: " + error.message);

                                        }

                                    });

                                });
                            </script>

                            <br>
                            <br>


                            <!-- WISHLIST -->

                            <a class="wishlist-btn" href="#">

                                <i class="fa fa-heart-o"></i>

                                Add to wishlist

                            </a>


                            <!-- SOCIAL SHARING -->

                            <div class="product-social-sharing pt-25">

                                <ul>

                                    <li class="facebook">

                                        <a href="#">

                                            <i class="fa fa-facebook"></i>

                                            Facebook

                                        </a>

                                    </li>


                                    <li class="twitter">

                                        <a href="#">

                                            <i class="fa fa-twitter"></i>

                                            Twitter

                                        </a>

                                    </li>


                                    <li class="google-plus">

                                        <a href="#">

                                            <i class="fa fa-google-plus"></i>

                                            Google +

                                        </a>

                                    </li>


                                    <li class="instagram">

                                        <a href="#">

                                            <i class="fa fa-instagram"></i>

                                            Instagram

                                        </a>

                                    </li>

                                </ul>

                            </div>


                        </div>

                    </div>


                </div>

            </div>


        </div>

    </div>

</div>

<!-- content-wraper end -->


<!-- =========================================================
     RELATED PRODUCTS
========================================================= -->

<?php if (!empty($currentRelatedProducts)): ?>

    <section class="product-area li-laptop-product pt-30 pb-50">

        <div class="container-fluid">

            <div class="row">

                <div class="col-lg-12">

                    <div class="li-section-title">

                        <h2>

                            <span>
                                Related Products
                            </span>

                        </h2>

                    </div>


                    <div class="row">

                        <div class="product-active owl-carousel">


                            <?php foreach ($currentRelatedProducts as $related): ?>

                                <div class="col-lg-12">

                                    <div class="single-product-wrap">


                                        <!-- PRODUCT IMAGE -->

                                        <div class="product-image">

                                            <a href="product.php?slug=<?= urlencode(
                                                                            $related['slug']
                                                                        ) ?>">

                                                <?php if (!empty($related['main_image'])): ?>

                                                    <img src="uploads/products/<?= htmlspecialchars(
                                                                                    $related['main_image']
                                                                                ) ?>" alt="<?= htmlspecialchars(
                                                                                                $related['name']
                                                                                            ) ?>" />

                                                <?php endif; ?>

                                            </a>

                                            <span class="sticker">

                                                New

                                            </span>

                                        </div>


                                        <!-- PRODUCT DESCRIPTION -->

                                        <div class="product_desc">

                                            <div class="product_desc_info">


                                                <div class="product-review">

                                                    <h5 class="manufacturer">

                                                        <a href="category.php?slug=<?= urlencode(
                                                                                        $currentProduct['category_slug']
                                                                                    ) ?>">

                                                            <?= htmlspecialchars(
                                                                $currentProduct['category_name']
                                                            ) ?>

                                                        </a>

                                                    </h5>


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


                                                <h4>

                                                    <a class="product_name" href="product.php?slug=<?= urlencode(
                                                                                                        $related['slug']
                                                                                                    ) ?>">

                                                        <?= htmlspecialchars(
                                                            $related['name']
                                                        ) ?>

                                                    </a>

                                                </h4>


                                                <div class="price-box">

                                                    <span class="new-price">

                                                        Ksh.
                                                        <?= number_format(
                                                            $related['price'],
                                                            0
                                                        ) ?>

                                                    </span>

                                                </div>


                                            </div>


                                            <div class="add-actions">

                                                <ul class="add-actions-link">

                                                    <li class="add-to-cart">

                                                        <a href="#" class="add-to-cart-link">

                                                            <i class="fa fa-shopping-cart"></i>

                                                        </a>

                                                    </li>


                                                    <li>

                                                        <a class="links-details" href="#">

                                                            <i class="fa fa-heart-o"></i>

                                                        </a>

                                                    </li>

                                                </ul>

                                            </div>


                                        </div>

                                    </div>

                                </div>

                            <?php endforeach; ?>


                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>

<?php endif; ?>


<!-- =========================================================
     VIEW MORE FEATURES
========================================================= -->

<script>
    function toggleProductFeatures() {

        const rows =
            document.querySelectorAll('.product-feature-row');

        const button =
            document.getElementById('productFeatureBtn');


        let opening = false;


        rows.forEach(function(row, index) {

            if (index >= 5) {

                if (row.style.display === 'none') {

                    row.style.display = 'table-row';

                    opening = true;

                } else {

                    row.style.display = 'none';

                }

            }

        });


        if (button) {

            button.textContent =
                opening ? 'View Less' : 'View More';

        }

    }
</script>


<style>
    .product-details-images .lg-image img {
        width: 100%;
        height: 450px;
        object-fit: contain;
    }

    .product-details-images .lg-image {
        height: 450px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    @media (max-width: 767px) {

        .product-details-images .lg-image,
        .product-details-images .lg-image img {
            height: 350px;
        }

    }
</style>
<script>
    $(document).on(
        'click',
        '#addToCartButton',
        function(e) {

            e.preventDefault();

            const button = $(this);

            const productId =
                parseInt(
                    button.attr('data-product-id')
                );


            /*
            |--------------------------------------------------------------------------
            | CHECK PRODUCT ID
            |--------------------------------------------------------------------------
            */

            if (!productId) {

                alert(
                    'Unable to add this product. Product ID is missing.'
                );

                return;

            }


            /*
            |--------------------------------------------------------------------------
            | PREVENT DOUBLE CLICK
            |--------------------------------------------------------------------------
            */

            if (button.prop('disabled')) {

                return;

            }


            button.prop(
                'disabled',
                true
            );


            button.html(
                '<i class="fa fa-spinner fa-spin"></i> Adding...'
            );


            /*
            |--------------------------------------------------------------------------
            | SEND TO PHP SESSION CART
            |--------------------------------------------------------------------------
            */

            $.ajax({

                url: 'add-to-cart.php',

                type: 'POST',

                dataType: 'json',

                data: {

                    product_id: productId

                },


                /*
                |--------------------------------------------------------------------------
                | SUCCESS
                |--------------------------------------------------------------------------
                */

                success: function(response) {

                    console.log(
                        'ADD TO CART RESPONSE:',
                        response
                    );


                    if (
                        response.success
                    ) {


                        /*
                        |--------------------------------------------------------------------------
                        | UPDATE HEADER MINI CART
                        |--------------------------------------------------------------------------
                        */

                        if (
                            typeof window.updateHeaderMiniCart ===
                            'function'
                        ) {

                            window.updateHeaderMiniCart(

                                response.cart,

                                response.cart_count,

                                response.cart_total

                            );

                        }


                        /*
                        |--------------------------------------------------------------------------
                        | UPDATE CART COUNT
                        |--------------------------------------------------------------------------
                        */

                        $('.cart-item-count').text(
                            response.cart_count
                        );


                        $('.cart-count').text(
                            response.cart_count
                        );


                        $('.header-cart-count').text(
                            response.cart_count
                        );


                        $('[data-cart-count]').text(
                            response.cart_count
                        );


                        /*
                        |--------------------------------------------------------------------------
                        | UPDATE TOTAL
                        |--------------------------------------------------------------------------
                        */

                        $('#cart-total').text(

                            Number(
                                response.cart_total
                            ).toLocaleString()

                        );


                        $('#cart-subtotal').text(

                            Number(
                                response.cart_total
                            ).toLocaleString()

                        );


                        $('.header-cart-total').text(

                            'Ksh. ' +

                            Number(
                                response.cart_total
                            ).toLocaleString()

                        );


                        /*
                        |--------------------------------------------------------------------------
                        | BUTTON SUCCESS
                        |--------------------------------------------------------------------------
                        */

                        button.html(

                            '<i class="fa fa-check"></i> Added to Cart'

                        );


                        /*
                        |--------------------------------------------------------------------------
                        | RESTORE BUTTON
                        |--------------------------------------------------------------------------
                        */

                        setTimeout(

                            function() {

                                button.html(

                                    '<i class="fa fa-shopping-cart"></i> Add to Cart'

                                );

                                button.prop(
                                    'disabled',
                                    false
                                );

                            },

                            1500

                        );


                    } else {


                        alert(

                            response.message ||

                            'Unable to add product to cart.'

                        );


                        button.html(

                            '<i class="fa fa-shopping-cart"></i> Add to Cart'

                        );


                        button.prop(
                            'disabled',
                            false
                        );

                    }

                },


                /*
                |--------------------------------------------------------------------------
                | AJAX ERROR
                |--------------------------------------------------------------------------
                */

                error: function(xhr) {

                    console.error(
                        'ADD TO CART ERROR:',
                        xhr.responseText
                    );


                    alert(
                        'Unable to add this product to the cart.'
                    );


                    button.html(

                        '<i class="fa fa-shopping-cart"></i> Add to Cart'

                    );


                    button.prop(
                        'disabled',
                        false
                    );

                }

            });

        }

    );
</script>
<?php include 'footer.php'; ?>