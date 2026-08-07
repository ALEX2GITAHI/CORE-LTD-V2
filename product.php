<?php

/*
|--------------------------------------------------------------------------
| CORE LTD - DYNAMIC PRODUCT PAGE
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/product-data.php';

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

                <?php if (!empty($product['category_name'])): ?>

                <li>
                    <a href="category.php?slug=<?= urlencode($product['category_slug']) ?>">
                        <?= htmlspecialchars($product['category_name']) ?>
                    </a>
                </li>

                <?php endif; ?>

                <li class="active">

                    <?= htmlspecialchars($product['name']) ?>

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

                        <?php foreach ($images as $image): ?>

                        <div class="lg-image">

                            <a class="popup-img venobox vbox-item"
                                href="images/product/small-size/<?= htmlspecialchars($image['image']) ?>"
                                data-gall="myGallery">

                                <img src="images/product/small-size/<?= htmlspecialchars($image['image']) ?>"
                                    alt="<?= htmlspecialchars(
                                                                                                                            !empty($image['alt_text'])
                                                                                                                                ? $image['alt_text']
                                                                                                                                : $product['name']
                                                                                                                        ) ?>" />

                            </a>

                        </div>

                        <?php endforeach; ?>

                    </div>


                    <!-- THUMBNAILS -->

                    <div class="product-details-thumbs slider-thumbs-1">

                        <?php foreach ($images as $image): ?>

                        <div class="sm-image">

                            <img src="images/product/small-size/<?= htmlspecialchars($image['image']) ?>"
                                alt="<?= htmlspecialchars(
                                                                                                                        !empty($image['alt_text'])
                                                                                                                            ? $image['alt_text']
                                                                                                                            : $product['name']
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

                                <?= htmlspecialchars($product['name']) ?>

                            </a>

                        </h4>


                        <!-- REFERENCE -->

                        <?php if (!empty($product['sku'])): ?>

                        <span class="product-details-ref">

                            Reference:
                            <?= htmlspecialchars($product['sku']) ?>

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
                                <?= number_format($product['price'], 0) ?>

                            </span>

                            <?php if (
                                !empty($product['old_price']) &&
                                $product['old_price'] > $product['price']
                            ): ?>

                            <span class="old-price">

                                Ksh.
                                <?= number_format($product['old_price'], 0) ?>

                            </span>

                            <?php endif; ?>

                        </div>


                        <!-- =================================================
                             FEATURES
                        ================================================== -->

                        <?php if (!empty($features)): ?>

                        <div class="product-desc">

                            <div style="overflow-x:auto;">

                                <h4>

                                    <b>

                                        Features:
                                        <?= htmlspecialchars($product['name']) ?>

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

                                        <?php foreach ($features as $index => $feature): ?>

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


                                <?php if (count($features) > 5): ?>

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

                            <button type="button" class="single-product-add-to-cart">

                                <a href="#" class="add-to-cart-link">

                                    <i class="fa fa-shopping-cart"></i>

                                    Add to Cart

                                </a>

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

<?php if (!empty($relatedProducts)): ?>

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


                        <?php foreach ($relatedProducts as $related): ?>

                        <div class="col-lg-12">

                            <div class="single-product-wrap">


                                <!-- PRODUCT IMAGE -->

                                <div class="product-image">

                                    <a href="product.php?slug=<?= urlencode(
                                                                            $related['slug']
                                                                        ) ?>">

                                        <?php if (!empty($related['main_image'])): ?>

                                        <img src="images/product/small-size/<?= htmlspecialchars(
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
                                                                                        $product['category_slug']
                                                                                    ) ?>">

                                                    <?= htmlspecialchars(
                                                                $product['category_name']
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

<?php include 'footer.php'; ?>