<?php

/*
|--------------------------------------------------------------------------
| CORE LTD - CART ACTIONS
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/includes/cart-functions.php';

header('Content-Type: application/json; charset=utf-8');


/*
|--------------------------------------------------------------------------
| GET ACTION
|--------------------------------------------------------------------------
*/

$action = isset($_POST['action'])
    ? trim($_POST['action'])
    : '';


$productId = isset($_POST['product_id'])
    ? (int) $_POST['product_id']
    : 0;


/*
|--------------------------------------------------------------------------
| INCREASE QUANTITY
|--------------------------------------------------------------------------
*/

if ($action === 'increase') {

    if (
        $productId > 0 &&
        isset($_SESSION['cart'][$productId])
    ) {

        $_SESSION['cart'][$productId]['quantity']++;
    }


    echo json_encode([
        'success' => true,
        'message' => 'Quantity increased.',
        'cart_count' => getCartCount(),
        'cart_total' => getCartTotal()
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| DECREASE QUANTITY
|--------------------------------------------------------------------------
*/

if ($action === 'decrease') {

    if (
        $productId > 0 &&
        isset($_SESSION['cart'][$productId])
    ) {

        $_SESSION['cart'][$productId]['quantity']--;


        if (
            $_SESSION['cart'][$productId]['quantity'] <= 0
        ) {

            unset($_SESSION['cart'][$productId]);
        }
    }


    echo json_encode([
        'success' => true,
        'message' => 'Quantity decreased.',
        'cart_count' => getCartCount(),
        'cart_total' => getCartTotal()
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| REMOVE PRODUCT
|--------------------------------------------------------------------------
*/

if ($action === 'remove') {

    if (
        $productId > 0 &&
        isset($_SESSION['cart'][$productId])
    ) {

        unset(
            $_SESSION['cart'][$productId]
        );
    }


    echo json_encode([

        'success' => true,

        'message' =>
        'Product removed from cart.',

        'cart' =>
        $_SESSION['cart'] ?? [],

        'cart_count' =>
        getCartCount(),

        'cart_total' =>
        getCartTotal()

    ]);

    exit;
}
/*
|--------------------------------------------------------------------------
| CLEAR CART
|--------------------------------------------------------------------------
*/

if ($action === 'clear') {

    clearCart();


    echo json_encode([
        'success' => true,
        'message' => 'Cart cleared.',
        'cart_count' => 0,
        'cart_total' => 0
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| GET CART COUNT
|--------------------------------------------------------------------------
*/

if ($action === 'get_count') {

    echo json_encode([
        'success' => true,
        'cart_count' => getCartCount(),
        'cart_total' => getCartTotal()
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| INVALID ACTION
|--------------------------------------------------------------------------
*/

echo json_encode([
    'success' => false,
    'message' => 'Invalid cart action.',
    'cart_count' => getCartCount(),
    'cart_total' => getCartTotal()
]);

exit;
