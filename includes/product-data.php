<?php

/*
|--------------------------------------------------------------------------
| CORE LTD - PRODUCT DATA
|--------------------------------------------------------------------------
*/

if (!isset($conn)) {
    require_once __DIR__ . '/db.php';
}

$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';

if ($slug === '') {
    http_response_code(404);
    exit('Product not found.');
}


/*
|--------------------------------------------------------------------------
| PRODUCT
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT
        p.*,
        b.name AS brand_name,
        b.slug AS brand_slug,
        c.name AS category_name,
        c.slug AS category_slug
    FROM products p
    LEFT JOIN brands b ON p.brand_id = b.id
    LEFT JOIN categories c ON p.category_id = c.id
    WHERE p.slug = ?
    AND p.status = 1
    LIMIT 1
");

$stmt->bind_param("s", $slug);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(404);
    exit('Product not found.');
}

$product = $result->fetch_assoc();

$stmt->close();


/*
|--------------------------------------------------------------------------
| PRODUCT IMAGES
|--------------------------------------------------------------------------
*/

$imageStmt = $conn->prepare("
    SELECT
        id,
        image,
        alt_text,
        sort_order,
        is_primary
    FROM product_images
    WHERE product_id = ?
    ORDER BY sort_order ASC
");

$imageStmt->bind_param("i", $product['id']);
$imageStmt->execute();

$imageResult = $imageStmt->get_result();

$images = [];

while ($row = $imageResult->fetch_assoc()) {
    $images[] = $row;
}

$imageStmt->close();


/*
|--------------------------------------------------------------------------
| PRODUCT FEATURES
|--------------------------------------------------------------------------
*/

$featureStmt = $conn->prepare("
    SELECT
        id,
        feature,
        benefit,
        sort_order
    FROM product_features
    WHERE product_id = ?
    ORDER BY sort_order ASC
");

$featureStmt->bind_param("i", $product['id']);
$featureStmt->execute();

$featureResult = $featureStmt->get_result();

$features = [];

while ($row = $featureResult->fetch_assoc()) {
    $features[] = $row;
}

$featureStmt->close();


/*
|--------------------------------------------------------------------------
| PRODUCT SPECIFICATIONS
|--------------------------------------------------------------------------
*/

$specStmt = $conn->prepare("
    SELECT
        id,
        specification_group,
        specification_name,
        specification_value,
        sort_order
    FROM product_specifications
    WHERE product_id = ?
    ORDER BY sort_order ASC
");

$specStmt->bind_param("i", $product['id']);
$specStmt->execute();

$specResult = $specStmt->get_result();

$specifications = [];

while ($row = $specResult->fetch_assoc()) {
    $specifications[] = $row;
}

$specStmt->close();


/*
|--------------------------------------------------------------------------
| RELATED PRODUCTS
|--------------------------------------------------------------------------
*/

$relatedProducts = [];

if (!empty($product['category_id'])) {

    $relatedStmt = $conn->prepare("
        SELECT
            p.id,
            p.name,
            p.slug,
            p.price,
            p.old_price,
            p.main_image,
            b.name AS brand_name
        FROM products p
        LEFT JOIN brands b ON p.brand_id = b.id
        WHERE p.category_id = ?
        AND p.id != ?
        AND p.status = 1
        ORDER BY p.created_at DESC
        LIMIT 8
    ");

    $relatedStmt->bind_param(
        "ii",
        $product['category_id'],
        $product['id']
    );

    $relatedStmt->execute();

    $relatedResult = $relatedStmt->get_result();

    while ($row = $relatedResult->fetch_assoc()) {
        $relatedProducts[] = $row;
    }

    $relatedStmt->close();
}
