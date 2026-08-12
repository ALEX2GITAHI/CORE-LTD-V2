<?php

/*
|--------------------------------------------------------------------------
| CORE LTD - ADD TO CART
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/includes/cart-functions.php';

require_once __DIR__ . '/includes/db.php';

header('Content-Type: application/json; charset=utf-8');


/*
|--------------------------------------------------------------------------
| PRODUCT ID
|--------------------------------------------------------------------------
*/

$productId = isset($_POST['product_id'])
    ? (int) $_POST['product_id']
    : 0;


if ($productId <= 0) {

    echo json_encode([

        'success' => false,

        'message' => 'Invalid product.'

    ]);

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
        price,
        old_price,
        main_image,
        status
    FROM products
    WHERE id = ?
    AND status = 1
    LIMIT 1
");


$stmt->bind_param(
    "i",
    $productId
);


$stmt->execute();


$result =
    $stmt->get_result();


/*
|--------------------------------------------------------------------------
| PRODUCT NOT FOUND
|--------------------------------------------------------------------------
*/

if ($result->num_rows === 0) {

    echo json_encode([

        'success' => false,

        'message' => 'Product not found.'

    ]);

    exit;
}


$product =
    $result->fetch_assoc();


$stmt->close();


/*
|--------------------------------------------------------------------------
| ADD TO CART
|--------------------------------------------------------------------------
*/

if (
    isset(
        $_SESSION['cart'][$productId]
    )
) {

    $_SESSION['cart'][$productId]['quantity']++;
} else {

    $_SESSION['cart'][$productId] = [

        'id' =>
        (int) $product['id'],

        'name' =>
        $product['name'],

        'slug' =>
        $product['slug'],

        'price' =>
        (float) $product['price'],

        'old_price' =>
        (float) ($product['old_price'] ?? 0),

        'image' =>
        $product['main_image'],

        'quantity' =>
        1

    ];
}

/*
|--------------------------------------------------------------------------
| RESPONSE
|--------------------------------------------------------------------------
*/

echo json_encode([

    'success' => true,

    'message' =>
    $product['name'] .
        ' added to cart.',

    /*
    |--------------------------------------------------------------------------
    | SEND THE COMPLETE UPDATED CART
    |--------------------------------------------------------------------------
    */

    'cart' =>
    $_SESSION['cart'] ?? [],

    /*
    |--------------------------------------------------------------------------
    | SEND UPDATED COUNT
    |--------------------------------------------------------------------------
    */

    'cart_count' =>
    getCartCount(),

    /*
    |--------------------------------------------------------------------------
    | SEND UPDATED TOTAL
    |--------------------------------------------------------------------------
    */

    'cart_total' =>
    getCartTotal()

]);

exit;

exit;
exit;
