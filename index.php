<?php
require_once __DIR__ . '/includes/db.php';

function e($v): string
{
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

function money($v): string
{
    return 'KSh ' . number_format((float)$v, 2);
}

function productUrl(array $p): string
{
    return 'product.php?slug=' . rawurlencode($p['slug']);
}

function productImage(?string $image): string
{
    return $image
        ? 'uploads/products/' . rawurlencode($image)
        : 'images/product/small-size/no-image.jpg';
}

/* =========================
   DATABASE CONTENT
========================= */

$categories = [];
$q = $conn->query("
    SELECT c.id, c.name, c.slug, c.description, c.image, c.sort_order,
           COUNT(p.id) AS product_count
    FROM categories c
    LEFT JOIN products p
      ON p.category_id = c.id AND p.status = 1
    WHERE c.status = 1 AND c.parent_id IS NULL
    GROUP BY c.id, c.name, c.slug, c.description, c.image, c.sort_order
    ORDER BY c.sort_order ASC, c.name ASC
");
if ($q) {
    while ($r = $q->fetch_assoc()) $categories[] = $r;
}

$featured = [];
$q = $conn->query("
    SELECT p.id,p.name,p.slug,p.sku,p.price,p.old_price,p.stock_quantity,
           p.main_image,p.featured,
           c.name AS category_name,c.slug AS category_slug,
           b.name AS brand_name
    FROM products p
    LEFT JOIN categories c ON c.id=p.category_id
    LEFT JOIN brands b ON b.id=p.brand_id
    WHERE p.status=1 AND p.featured=1
    ORDER BY p.updated_at DESC,p.id DESC
    LIMIT 8
");
if ($q) {
    while ($r = $q->fetch_assoc()) $featured[] = $r;
}

$latest = [];
$q = $conn->query("
    SELECT p.id,p.name,p.slug,p.sku,p.price,p.old_price,p.stock_quantity,
           p.main_image,p.featured,
           c.name AS category_name,c.slug AS category_slug,
           b.name AS brand_name
    FROM products p
    LEFT JOIN categories c ON c.id=p.category_id
    LEFT JOIN brands b ON b.id=p.brand_id
    WHERE p.status=1
    ORDER BY p.created_at DESC,p.id DESC
    LIMIT 8
");
if ($q) {
    while ($r = $q->fetch_assoc()) $latest[] = $r;
}

$brands = [];
$q = $conn->query("
    SELECT b.id, b.name, b.slug,
           COUNT(p.id) AS product_count
    FROM brands b
    LEFT JOIN products p
      ON p.brand_id = b.id AND p.status = 1
    WHERE b.status = 1
    GROUP BY b.id, b.name, b.slug
    ORDER BY b.name ASC
");
if ($q) {
    while ($r = $q->fetch_assoc()) $brands[] = $r;
}

if (!$featured) $featured = array_slice($latest, 0, 8);

$categoryProducts = [];
if ($categories) {
    foreach ($categories as $cat) {
        $id = (int)$cat['id'];
        $stmt = $conn->prepare("
            SELECT p.id,p.name,p.slug,p.sku,p.price,p.old_price,p.stock_quantity,
                   p.main_image,p.featured,
                   c.name AS category_name,c.slug AS category_slug,
                   b.name AS brand_name
            FROM products p
            LEFT JOIN categories c ON c.id=p.category_id
            LEFT JOIN brands b ON b.id=p.brand_id
            WHERE p.status=1 AND p.category_id=?
            ORDER BY p.featured DESC,p.updated_at DESC,p.id DESC
            LIMIT 8
        ");
        if ($stmt) {
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($r = $result->fetch_assoc()) {
                $categoryProducts[$id][] = $r;
            }
            $stmt->close();
        }
    }
}

$heroProducts = array_slice($featured, 0, 3);

include 'header.php';
?>

<!-- =========================================================
     CORE LTD HOME PAGE
========================================================= -->

<div class="slider-with-banner core-home-hero">
    <div class="container-fluid">
        <div class="row">

            <!-- DATABASE CATEGORY MENU -->
            <div class="col-lg-3">
                <div class="category-menu">
                    <div class="category-heading">
                        <h2 class="categories-toggle">
                            <span>Shop Categories</span>
                        </h2>
                    </div>

                    <div id="cate-toggle" class="category-menu-list category-scroll">
                        <ul>
                            <?php foreach ($categories as $cat): ?>
                            <li class="right-menu">
                                <a href="category.php?slug=<?= e($cat['slug']) ?>">
                                    <?= e($cat['name']) ?>

                                    <small class="core-cat-count">
                                        <?= (int)$cat['product_count'] ?>
                                    </small>
                                </a>

                                <?php $items = $categoryProducts[(int)$cat['id']] ?? []; ?>

                                <?php if ($items): ?>
                                <ul class="cat-mega-menu">
                                    <li class="right-menu cat-mega-title">
                                        <a href="category.php?slug=<?= e($cat['slug']) ?>">
                                            <?= e($cat['name']) ?>
                                        </a>

                                        <ul>
                                            <?php foreach (array_slice($items, 0, 8) as $item): ?>
                                            <li>
                                                <a href="<?= e(productUrl($item)) ?>">
                                                    <?= e($item['name']) ?>
                                                </a>
                                            </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </li>
                                </ul>
                                <?php endif; ?>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
            <style>
            /* Category menu - maximum 9 categories visible */
            .category-scroll {
                max-height: 540px;
                overflow-y: auto;
                overflow-x: hidden;
            }

            /* Keep scrollbar neat */
            .category-scroll::-webkit-scrollbar {
                width: 6px;
            }

            .category-scroll::-webkit-scrollbar-track {
                background: #f1f1f1;
            }

            .category-scroll::-webkit-scrollbar-thumb {
                background: #bbb;
                border-radius: 10px;
            }

            .category-scroll::-webkit-scrollbar-thumb:hover {
                background: #888;
            }

            /* Firefox */
            .category-scroll {
                scrollbar-width: thin;
            }
            </style>
            <!-- HERO -->
            <div class="col-lg-9">
                <div class="slider-area pt-sm-30 pt-xs-30">
                    <div class="slider-active owl-carousel">

                        <?php foreach ($heroProducts as $i => $p): ?>
                        <div
                            class="single-slide align-center-left animation-style-02 <?= $i === 1 ? 'bg-5' : 'bg-4' ?>">
                            <div class="slider-progress"></div>

                            <!-- <div class="slider-content core-hero-content">
                                <span class="core-hero-label">
                                    <?= $p['featured'] ? 'Featured Product' : 'CORE LTD' ?>
                                </span>

                                <h2><?= e($p['name']) ?></h2>

                                <p>
                                    <?= e($p['brand_name'] ?: 'CORE LTD') ?>
                                    <?php if ($p['category_name']): ?>
                                    • <?= e($p['category_name']) ?>
                                    <?php endif; ?>
                                </p>

                                <h3>
                                    Starting at
                                    <span><?= money($p['price']) ?></span>
                                </h3>

                                <div class="default-btn slide-btn">
                                    <a class="links" href="<?= e(productUrl($p)) ?>">
                                        View Product
                                    </a>
                                </div>
                            </div> -->
                        </div>
                        <?php endforeach; ?>

                        <?php if (!$heroProducts): ?>
                        <div class="single-slide align-center-left animation-style-02 bg-4">
                            <div class="slider-progress"></div>
                            <div class="slider-content core-hero-content">
                                <span class="core-hero-label">CORE LTD</span>
                                <h2>Networking • Fiber • CCTV • IT</h2>
                                <p>Technology products and solutions for homes, businesses and ISPs.</p>
                                <div class="default-btn slide-btn">
                                    <a class="links" href="shop.php">Shop Products</a>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Begin Li's Static Banner Area -->
<div class="li-static-banner pt-20 pt-sm-30 pt-xs-30">
    <div class="container-fluid">
        <div class="row">
            <!-- Begin Single Banner Area -->
            <div class="col-lg-4 col-md-4">
                <div class="single-banner pb-xs-30">
                    <a href="#">
                        <img src="images/banner/rb951-banner.png" alt="Li's Static Banner" />
                    </a>
                </div>
            </div>
            <!-- Single Banner Area End Here -->
            <!-- Begin Single Banner Area -->
            <div class="col-lg-4 col-md-4">
                <div class="single-banner pb-xs-30">
                    <a href="#">
                        <img src="images/banner/huawenew-banner.png" alt="Li's Static Banner" />
                    </a>
                </div>
            </div>
            <!-- Single Banner Area End Here -->
            <!-- Begin Single Banner Area -->
            <div class="col-lg-4 col-md-4">
                <div class="single-banner">
                    <a href="#">
                        <img src="images/banner/Crimping-tool-big-banner.png" alt="Li's Static Banner" />
                    </a>
                </div>
            </div>
            <!-- Single Banner Area End Here -->
        </div>
    </div>
</div>
<!-- Li's Static Banner Area End Here -->

<!-- FEATURED PRODUCTS -->
<section class="product-area li-laptop-product Special-product pt-45 pb-45">
    <div class="container-fluid">

        <div class="li-section-title core-section-heading">
            <h2><span>Featured Products</span></h2>
            <a href="shop.php"><i class="fa fa-angle-right"></i></a>
        </div>

        <div class="row">
            <div class="product-active owl-carousel">

                <?php foreach ($featured as $p): ?>
                <div class="col-lg-12">
                    <div class="single-product-wrap">

                        <div class="product-image">
                            <a href="<?= e(productUrl($p)) ?>">
                                <img src="<?= e(productImage($p['main_image'])) ?>" alt="<?= e($p['name']) ?>">
                            </a>

                            <?php if ($p['featured']): ?>
                            <span class="sticker">sale</span>
                            <?php elseif ($p['old_price'] !== null && $p['old_price'] > $p['price']): ?>
                            <span class="sticker">Sale</span>
                            <?php endif; ?>
                        </div>

                        <div class="product_desc">
                            <div class="product_desc_info">

                                <div class="product-review">
                                    <h5 class="manufacturer">
                                        <a href="category.php?slug=<?= e($p['category_slug'] ?? '') ?>">
                                            <?= e($p['brand_name'] ?: ($p['category_name'] ?: 'CORE LTD')) ?>
                                        </a>
                                    </h5>

                                    <!-- <div class="rating-box">
                                        <ul class="rating">
                                            <li><i class="fa fa-star"></i></li>
                                            <li><i class="fa fa-star"></i></li>
                                            <li><i class="fa fa-star"></i></li>
                                            <li><i class="fa fa-star"></i></li>
                                            <li><i class="fa fa-star"></i></li>
                                        </ul>
                                    </div> -->
                                </div>

                                <h4>
                                    <a class="product_name" href="<?= e(productUrl($p)) ?>">
                                        <?= e($p['name']) ?>
                                    </a>
                                </h4>

                                <div class="price-box">
                                    <span class="new-price"><?= money($p['price']) ?></span>

                                    <?php if ($p['old_price'] !== null && $p['old_price'] > $p['price']): ?>
                                    <span class="old-price"><?= money($p['old_price']) ?></span>
                                    <?php $discount = (($p['old_price'] - $p['price']) / $p['old_price']) * 100; ?>
                                    <span class="discount-percentage">-<?= round($discount) ?>%</span>
                                    <?php endif; ?>
                                </div>

                            </div>

                            <!-- <div class="add-actions">
                                <ul class="add-actions-link">
                                    <li class="add-to-cart">
                                        <a href="<?= e(productUrl($p)) ?>">
                                            <i class="fa fa-shopping-cart"></i> View Product
                                        </a>
                                    </li>
                                    <li>
                                        <a class="links-details" href="<?= e(productUrl($p)) ?>">
                                            <i class="fa fa-heart-o"></i>
                                        </a>
                                    </li>
                                </ul>
                            </div> -->
                        </div>

                    </div>
                </div>
                <?php endforeach; ?>

            </div>
        </div>
    </div>
</section>

<!-- SHOP BY BRAND -->
<section class="core-category-section">
    <div class="container-fluid">

        <div class="li-section-title core-section-heading">
            <h2><span>Shop by Brand</span></h2>
        </div>

        <div class="row">

            <?php foreach ($brands as $brand): ?>

            <div class="col-lg-3 col-md-4 col-sm-6 mb-20">

                <a href="brand.php?slug=<?= e($brand['slug']) ?>" class="core-category-card">

                    <!-- BRAND ICON -->
                    <div class="core-category-icon">

                        <?php
                            $brandIcons = [

                                'tenda'       => 'fa-wifi',
                                'ruijie'      => 'fa-network-wired',
                                'reyee'       => 'fa-network-wired',
                                'mikrotik'    => 'fa-server',
                                'tp-link'     => 'fa-wifi',
                                'd-link'      => 'fa-network-wired',
                                'huawei'      => 'fa-signal',
                                'ubiquiti'    => 'fa-broadcast-tower',
                                'hikvision'   => 'fa-video-camera',
                                'dahua'       => 'fa-video',
                                'jakemy'      => 'fa-tools',
                                'starlink'    => 'fa-satellite-dish'

                            ];

                            $brandSlug = strtolower(
                                trim($brand['slug'])
                            );

                            $icon = $brandIcons[$brandSlug]
                                ?? 'fa-cubes';
                            ?>

                        <i class="fa <?= e($icon) ?>"></i>

                    </div>


                    <!-- BRAND INFORMATION -->
                    <div class="core-category-info">

                        <h3>
                            <?= e($brand['name']) ?>
                        </h3>

                        <span>

                            <?= (int)$brand['product_count'] ?>

                            <?= (int)$brand['product_count'] === 1
                                    ? 'Product'
                                    : 'Products'
                                ?>

                        </span>

                    </div>


                    <!-- ARROW -->
                    <i class="fa fa-angle-right core-category-arrow"></i>

                </a>

            </div>

            <?php endforeach; ?>

        </div>

    </div>
</section>

<div class="li-banner-2 pt-15">
    <div class="row">
        <!-- Begin Single Banner Area -->
        <div class="col-lg-6 col-md-6">
            <div class="single-banner">
                <a href="#">
                    <img src="images/banner/Tenda-oap-1200-banner.png" alt="Li's Static Banner" />
                </a>
            </div>
        </div>
        <!-- Single Banner Area End Here -->
        <!-- Begin Single Banner Area -->
        <div class="col-lg-6 col-md-6">
            <div class="single-banner pt-xs-30">
                <a href="#">
                    <img src="images/banner/ruijie-6262g-banner.png" alt="Li's Static Banner" />
                </a>
            </div>
        </div>
        <!-- Single Banner Area End Here -->
    </div>
</div>

<!-- PRODUCTS PER DATABASE CATEGORY -->
<?php foreach ($categories as $cat): ?>
<?php $items = $categoryProducts[(int)$cat['id']] ?? []; ?>
<?php if (!$items) continue; ?>

<section class="product-area li-laptop-product li-laptop-product-2 pb-45 pt-30">
    <div class="container-fluid">

        <div class="li-section-title core-section-heading">
            <h2><span><?= e($cat['name']) ?></span></h2>
            <a href="category.php?slug=<?= e($cat['slug']) ?>">
                View Category <i class="fa fa-angle-right"></i>
            </a>
        </div>

        <div class="row">
            <div class="product-active owl-carousel">

                <?php foreach ($items as $p): ?>
                <div class="col-lg-12">
                    <div class="single-product-wrap">

                        <div class="product-image">
                            <a href="<?= e(productUrl($p)) ?>">
                                <img src="<?= e(productImage($p['main_image'])) ?>" alt="<?= e($p['name']) ?>">
                            </a>
                            <?php if ($p['featured']): ?>
                            <span class="sticker">sale</span>
                            <?php endif; ?>
                        </div>

                        <div class="product_desc">
                            <div class="product_desc_info">

                                <div class="product-review">
                                    <h5 class="manufacturer">
                                        <a href="category.php?slug=<?= e($cat['slug']) ?>">
                                            <?= e($p['brand_name'] ?: $cat['name']) ?>
                                        </a>
                                    </h5>

                                    <div class="rating-box">
                                        <ul class="rating">
                                            <li><i class="fa fa-star"></i></li>
                                            <li><i class="fa fa-star"></i></li>
                                            <li><i class="fa fa-star"></i></li>
                                            <li><i class="fa fa-star-o"></i></li>
                                            <li><i class="fa fa-star-o"></i></li>
                                        </ul>
                                    </div>
                                </div>

                                <h4>
                                    <a class="product_name" href="<?= e(productUrl($p)) ?>">
                                        <?= e($p['name']) ?>
                                    </a>
                                </h4>

                                <div class="price-box">
                                    <span class="new-price"><?= money($p['price']) ?></span>
                                    <?php if ($p['old_price'] !== null && $p['old_price'] > $p['price']): ?>
                                    <span class="old-price"><?= money($p['old_price']) ?></span>
                                    <?php endif; ?>
                                </div>

                            </div>

                            <div class="add-actions">
                                <ul class="add-actions-link">
                                    <li class="add-to-cart">
                                        <a href="<?= e(productUrl($p)) ?>">
                                            <i class="fa fa-shopping-cart"></i> View Product
                                        </a>
                                    </li>
                                    <li>
                                        <a class="links-details" href="<?= e(productUrl($p)) ?>">
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
</section>
<?php endforeach; ?>

<div class="li-banner-2 pt-15">
    <div class="row">
        <!-- Begin Single Banner Area -->
        <div class="col-lg-6 col-md-6">
            <div class="single-banner">
                <a href="#">
                    <img src="images/banner/tendapost.png" href="tenda-f3.php" />
                </a>
            </div>
        </div>
        <!-- Single Banner Area End Here -->
        <!-- Begin Single Banner Area -->
        <div class="col-lg-6 col-md-6">
            <div class="single-banner pt-xs-30">
                <a href="#">
                    <img src="images/banner/splicingpost.png" alt="Li's Static Banner" />
                </a>
            </div>
        </div>
        <!-- Single Banner Area End Here -->
    </div>
</div>

<!-- SERVICES / BRAND MESSAGE -->
<section class="core-services-section">
    <div class="container-fluid">
        <div class="row">

            <div class="col-lg-6">
                <div class="core-services-copy">
                    <span class="core-kicker">MORE THAN A SHOP</span>
                    <h2>Technology. Connectivity. Solutions.</h2>
                    <p>
                        CORE LTD provides networking equipment, fiber optic products,
                        CCTV solutions and technical services for homes, businesses
                        and internet service providers.
                    </p>

                    <div class="default-btn">
                        <a class="links" href="contact.php">Talk to Our Team</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="row">

                    <div class="col-md-6 mb-20">
                        <div class="core-service-card">
                            <i class="fa fa-wifi"></i>
                            <h3>Networking</h3>
                            <p>Routers, switches, access points and wireless equipment.</p>
                        </div>
                    </div>

                    <div class="col-md-6 mb-20">
                        <div class="core-service-card">
                            <i class="fa fa-sitemap"></i>
                            <h3>Fiber Optics</h3>
                            <p>Fiber cable, tools, splitters and installation accessories.</p>
                        </div>
                    </div>

                    <div class="col-md-6 mb-20">
                        <div class="core-service-card">
                            <i class="fa fa-video-camera"></i>
                            <h3>CCTV & Security</h3>
                            <p>Security cameras and accessories for homes and businesses.</p>
                        </div>
                    </div>

                    <div class="col-md-6 mb-20">
                        <div class="core-service-card">
                            <i class="fa fa-cogs"></i>
                            <h3>Technical Services</h3>
                            <p>Installation, configuration, troubleshooting and support.</p>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>

<!-- Small homepage-only additions; existing style.css remains the main stylesheet -->
<style>
.core-hero-content {
    max-width: 520px;
    padding-left: 35px
}

.core-hero-content h2 {
    max-width: 520px;
    font-size: 38px;
    line-height: 1.15;
    font-weight: 700;
    margin: 10px 0
}

.core-hero-content p {
    font-size: 16px;
    line-height: 1.6;
    color: #444;
    max-width: 480px
}

.core-hero-content h3 span {
    color: #0363cd
}

.core-hero-label,
.core-kicker {
    color: #0363cd;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase
}

.core-cat-count {
    float: right;
    color: #999;
    font-size: 11px
}

.core-value-strip {
    margin: 25px 0 10px
}

.core-value-box {
    display: flex;
    align-items: center;
    gap: 14px;
    background: #fff;
    border: 1px solid #eee;
    padding: 18px;
    min-height: 82px;
    transition: .3s
}

.core-value-box:hover {
    box-shadow: 0 4px 18px rgba(0, 0, 0, .08);
    transform: translateY(-2px)
}

.core-value-box>i {
    width: 42px;
    height: 42px;
    line-height: 42px;
    text-align: center;
    border-radius: 50%;
    background: #eaf6fb;
    color: #0363cd;
    font-size: 18px
}

.core-value-box strong,
.core-value-box span {
    display: block
}

.core-value-box strong {
    color: #242424;
    font-size: 14px
}

.core-value-box span {
    color: #888;
    font-size: 12px;
    margin-top: 3px
}

.core-section-heading {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px
}

.core-section-heading>a {
    color: #0363cd;
    font-size: 13px;
    font-weight: 500
}

.core-category-section {
    background: #f7f9fb;
    padding: 45px 0 30px
}

.core-category-card {
    min-height: 112px;
    display: flex;
    align-items: center;
    gap: 15px;
    position: relative;
    background: #fff;
    border: 1px solid #e8edf2;
    border-radius: 4px;
    padding: 18px 45px 18px 18px;
    color: #242424;
    transition: .3s
}

.core-category-card:hover {
    color: #0363cd;
    transform: translateY(-3px);
    box-shadow: 0 7px 22px rgba(0, 0, 0, .08)
}

.core-category-icon {
    flex: 0 0 54px;
    width: 54px;
    height: 54px;
    line-height: 54px;
    text-align: center;
    border-radius: 50%;
    background: #edf7fb;
    color: #0363cd;
    font-size: 21px
}

.core-category-info h3 {
    margin: 0 0 5px;
    color: #242424;
    font-size: 16px;
    font-weight: 500
}

.core-category-info span {
    color: #999;
    font-size: 12px
}

.core-category-arrow {
    position: absolute;
    right: 18px;
    top: 50%;
    transform: translateY(-50%);
    color: #aaa
}

.core-services-section {
    padding: 60px 0;
    background: #f7f9fb
}

.core-services-copy {
    padding: 25px 35px 25px 10px
}

.core-services-copy h2 {
    font-size: 32px;
    line-height: 1.25;
    margin: 10px 0 15px;
    color: #242424
}

.core-services-copy p {
    color: #777;
    line-height: 1.8;
    max-width: 560px;
    margin-bottom: 25px
}

.core-service-card {
    height: 100%;
    background: #fff;
    border: 1px solid #e9eef3;
    border-radius: 5px;
    padding: 25px;
    transition: .3s
}

.core-service-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 7px 22px rgba(0, 0, 0, .08)
}

.core-service-card>i {
    font-size: 24px;
    color: #0363cd;
    margin-bottom: 15px
}

.core-service-card h3 {
    color: #242424;
    font-size: 17px;
    margin-bottom: 8px
}

.core-service-card p {
    color: #888;
    font-size: 13px;
    line-height: 1.7;
    margin: 0
}

@media(max-width:991px) {
    .core-hero-content {
        padding: 20px
    }

    .core-hero-content h2 {
        font-size: 30px
    }

    .core-value-box {
        margin-bottom: 15px
    }
}

@media(max-width:575px) {
    .core-hero-content h2 {
        font-size: 25px
    }

    .core-section-heading {
        align-items: flex-start;
        flex-direction: column
    }
}
</style>

<?php include 'footer.php'; ?>