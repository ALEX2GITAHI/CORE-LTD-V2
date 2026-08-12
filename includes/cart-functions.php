<?php

/*
|--------------------------------------------------------------------------
| CORE LTD - CART FUNCTIONS
|--------------------------------------------------------------------------
*/

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


/*
|--------------------------------------------------------------------------
| MAKE SURE CART EXISTS
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}


/*
|--------------------------------------------------------------------------
| GET CART COUNT
|--------------------------------------------------------------------------
|
| Returns the total number of items.
| Example:
| Product A x 2
| Product B x 1
| Cart count = 3
|
*/

function getCartCount()
{
    $count = 0;

    if (!empty($_SESSION['cart'])) {

        foreach ($_SESSION['cart'] as $item) {

            $count += (int) ($item['quantity'] ?? 0);
        }
    }

    return $count;
}


/*
|--------------------------------------------------------------------------
| GET CART TOTAL
|--------------------------------------------------------------------------
*/

function getCartTotal()
{
    $total = 0;

    if (!empty($_SESSION['cart'])) {

        foreach ($_SESSION['cart'] as $item) {

            $price = (float) ($item['price'] ?? 0);

            $quantity = (int) ($item['quantity'] ?? 0);

            $total += $price * $quantity;
        }
    }

    return $total;
}


/*
|--------------------------------------------------------------------------
| GET CART
|--------------------------------------------------------------------------
*/

function getCart()
{
    return $_SESSION['cart'] ?? [];
}


/*
|--------------------------------------------------------------------------
| SET CART
|--------------------------------------------------------------------------
*/

function setCart($cart)
{
    $_SESSION['cart'] = is_array($cart) ? $cart : [];
}


/*
|--------------------------------------------------------------------------
| CLEAR CART
|--------------------------------------------------------------------------
*/

function clearCart()
{
    $_SESSION['cart'] = [];
}


/*
|--------------------------------------------------------------------------
| CART RESPONSE DATA
|--------------------------------------------------------------------------
*/

function getCartResponse()
{
    return [
        'cart_count' => getCartCount(),
        'cart_total' => getCartTotal()
    ];
}
