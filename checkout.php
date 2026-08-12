<?php

session_start();

$cart = $_SESSION['cart'] ?? [];

/*
|--------------------------------------------------------------------------
| CORE LTD - CHECKOUT PAGE
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/includes/cart-functions.php';


/*
|--------------------------------------------------------------------------
| GET CURRENT CART
|--------------------------------------------------------------------------
*/

$cart = getCart();

$cartTotal = getCartTotal();

$cartCount = getCartCount();


/*
|--------------------------------------------------------------------------
| REDIRECT IF CART IS EMPTY
|--------------------------------------------------------------------------
*/

if (empty($cart)) {

    header('Location: cart.php');

    exit;
}


/*
|--------------------------------------------------------------------------
| PAGE HEADER
|--------------------------------------------------------------------------
*/

include 'header.php';

?>

<!-- =========================================================
     CHECKOUT BREADCRUMB
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

                <li>

                    <a href="cart.php">
                        Shopping Cart
                    </a>

                </li>

                <li class="active">

                    Checkout

                </li>

            </ul>

        </div>

    </div>

</div>


<!-- =========================================================
     CHECKOUT AREA
========================================================= -->

<div class="checkout-area" style="
        padding:60px 0;
        background:#fff;
    ">

    <div class="container">

        <!-- =================================================
             TITLE
        ================================================== -->

        <div class="checkout-title" style="
                margin-bottom:35px;
            ">

            <h1 style="
                    font-size:32px;
                    font-weight:600;
                    margin-bottom:10px;
                ">
                Checkout
            </h1>

            <p style="
                    color:#777;
                    margin:0;
                ">
                Complete your details below to place your order.
            </p>

        </div>


        <form action="place-order.php" method="POST" id="checkout-form">

            <div class="row">

                <!-- =================================================
                     CUSTOMER DETAILS
                ================================================== -->

                <div class="col-lg-7 col-md-7">

                    <div class="checkout-box" style="
                            border:1px solid #e5e5e5;
                            padding:30px;
                            margin-bottom:30px;
                        ">

                        <h3 style="
                                font-size:22px;
                                font-weight:600;
                                margin-bottom:25px;
                            ">

                            Customer & Delivery Details

                        </h3>


                        <!-- FULL NAME -->

                        <div class="form-group" style="margin-bottom:20px;">

                            <label for="customer_name" style="
                                    font-weight:600;
                                    margin-bottom:8px;
                                ">

                                Full Name
                                <span style="color:red;">
                                    *
                                </span>

                            </label>


                            <input type="text" id="customer_name" name="customer_name" class="form-control"
                                placeholder="Enter your full name" required>

                        </div>


                        <!-- PHONE -->

                        <div class="form-group" style="margin-bottom:20px;">

                            <label for="phone" style="
                                    font-weight:600;
                                    margin-bottom:8px;
                                ">

                                Phone Number
                                <span style="color:red;">
                                    *
                                </span>

                            </label>


                            <input type="tel" id="phone" name="phone" class="form-control" placeholder="e.g. 0712345678"
                                required>

                        </div>


                        <!-- EMAIL -->

                        <div class="form-group" style="margin-bottom:20px;">

                            <label for="email" style="
                                    font-weight:600;
                                    margin-bottom:8px;
                                ">

                                Email Address

                            </label>


                            <input type="email" id="email" name="email" class="form-control"
                                placeholder="Enter your email address">

                        </div>


                        <!-- TOWN / AREA -->

                        <div class="form-group" style="margin-bottom:20px;">

                            <label for="town" style="
                                    font-weight:600;
                                    margin-bottom:8px;
                                ">

                                Town / Area
                                <span style="color:red;">
                                    *
                                </span>

                            </label>


                            <input type="text" id="town" name="town" class="form-control"
                                placeholder="e.g. Githurai, Nairobi" required>

                        </div>


                        <!-- DELIVERY ADDRESS -->

                        <div class="form-group" style="margin-bottom:20px;">

                            <label for="delivery_address" style="
                                    font-weight:600;
                                    margin-bottom:8px;
                                ">

                                Delivery Address / Location
                                <span style="color:red;">
                                    *
                                </span>

                            </label>


                            <textarea id="delivery_address" name="delivery_address" class="form-control" rows="4"
                                placeholder="Enter estate, building, street, landmark or other delivery details"
                                required></textarea>

                        </div>


                        <!-- ORDER NOTES -->

                        <div class="form-group" style="margin-bottom:0;">

                            <label for="order_notes" style="
                                    font-weight:600;
                                    margin-bottom:8px;
                                ">

                                Order Notes
                                <span style="
                                        font-weight:400;
                                        color:#999;
                                    ">
                                    (Optional)
                                </span>

                            </label>


                            <textarea id="order_notes" name="order_notes" class="form-control" rows="4"
                                placeholder="Any additional information about your order?"></textarea>

                        </div>

                    </div>


                    <!-- =================================================
                         DELIVERY INFORMATION
                    ================================================== -->

                    <div class="checkout-box" style="
                            border:1px solid #e5e5e5;
                            padding:25px 30px;
                            margin-bottom:30px;
                        ">

                        <h4 style="
                                font-size:18px;
                                font-weight:600;
                                margin-bottom:15px;
                            ">

                            <i class="fa fa-truck" style="margin-right:8px;"></i>

                            Delivery Information

                        </h4>


                        <p style="
                                margin:0;
                                color:#666;
                                line-height:1.7;
                            ">

                            Delivery charges will be confirmed based
                            on your location before delivery.

                        </p>

                    </div>

                </div>


                <!-- =================================================
                     ORDER SUMMARY
                ================================================== -->

                <div class="col-lg-5 col-md-5">

                    <div class="checkout-box" style="
                            border:1px solid #ddd;
                            padding:30px;
                            position:sticky;
                            top:20px;
                        ">

                        <h3 style="
                                font-size:22px;
                                font-weight:600;
                                margin-bottom:25px;
                            ">

                            Your Order

                        </h3>


                        <!-- PRODUCTS -->

                        <div class="checkout-products" style="
                                border-bottom:1px solid #ddd;
                                padding-bottom:15px;
                                margin-bottom:20px;
                            ">

                            <?php foreach ($cart as $item): ?>

                                <?php

                                $itemId =
                                    (int) ($item['id'] ?? 0);

                                $itemName =
                                    $item['name'] ?? 'Product';

                                $itemPrice =
                                    (float) ($item['price'] ?? 0);

                                $itemQuantity =
                                    (int) ($item['quantity'] ?? 1);

                                $itemImage =
                                    $item['image'] ?? '';

                                ?>

                                <div class="checkout-product" style="
                                        display:flex;
                                        align-items:center;
                                        gap:15px;
                                        padding:12px 0;
                                    ">

                                    <!-- IMAGE -->

                                    <div style="
                                            width:65px;
                                            min-width:65px;
                                            height:65px;
                                            border:1px solid #eee;
                                            display:flex;
                                            align-items:center;
                                            justify-content:center;
                                            background:#fff;
                                        ">

                                        <?php if (!empty($itemImage)): ?>

                                            <img src="uploads/products/<?= htmlspecialchars($itemImage) ?>"
                                                alt="<?= htmlspecialchars($itemName) ?>" style="
                                                    width:55px;
                                                    height:55px;
                                                    object-fit:contain;
                                                " onerror="
                                                    this.onerror=null;
                                                    this.src='uploads/products/default.png';
                                                ">

                                        <?php else: ?>

                                            <i class="fa fa-shopping-cart" style="
                                                    color:#ccc;
                                                    font-size:25px;
                                                "></i>

                                        <?php endif; ?>

                                    </div>


                                    <!-- DETAILS -->

                                    <div style="
                                            flex:1;
                                        ">

                                        <h6 style="
                                                margin:0 0 5px;
                                                font-size:15px;
                                                font-weight:600;
                                                line-height:1.4;
                                            ">

                                            <?= htmlspecialchars($itemName) ?>

                                        </h6>


                                        <span style="
                                                font-size:13px;
                                                color:#777;
                                            ">

                                            Ksh.
                                            <?= number_format($itemPrice, 0) ?>

                                            ×

                                            <?= $itemQuantity ?>

                                        </span>

                                    </div>


                                    <!-- SUBTOTAL -->

                                    <strong style="
                                            white-space:nowrap;
                                            font-size:14px;
                                        ">

                                        Ksh.
                                        <?= number_format(
                                            $itemPrice * $itemQuantity,
                                            0
                                        ) ?>

                                    </strong>

                                </div>

                            <?php endforeach; ?>

                        </div>


                        <!-- =================================================
                             SUBTOTAL
                        ================================================== -->

                        <div style="
                                display:flex;
                                justify-content:space-between;
                                padding:8px 0;
                            ">

                            <span>
                                Subtotal
                            </span>


                            <strong>

                                Ksh.
                                <?= number_format(
                                    $cartTotal,
                                    0
                                ) ?>

                            </strong>

                        </div>


                        <!-- =================================================
                             DELIVERY
                        ================================================== -->

                        <div style="
                                display:flex;
                                justify-content:space-between;
                                padding:8px 0;
                            ">

                            <span>
                                Delivery
                            </span>


                            <span style="
                                    color:#777;
                                    font-size:14px;
                                ">

                                To be confirmed

                            </span>

                        </div>


                        <hr style="
                                margin:15px 0;
                            ">


                        <!-- =================================================
                             TOTAL
                        ================================================== -->

                        <div style="
                                display:flex;
                                justify-content:space-between;
                                align-items:center;
                                margin-bottom:25px;
                            ">

                            <strong style="
                                    font-size:20px;
                                ">

                                Total

                            </strong>


                            <strong style="
                                    font-size:24px;
                                    color:#e60000;
                                ">

                                Ksh.
                                <?= number_format(
                                    $cartTotal,
                                    0
                                ) ?>

                            </strong>

                        </div>


                        <!-- =================================================
                             TERMS
                        ================================================== -->

                        <div style="
                                margin-bottom:20px;
                            ">

                            <label style="
                                    display:flex;
                                    align-items:flex-start;
                                    gap:10px;
                                    cursor:pointer;
                                    font-size:14px;
                                    color:#555;
                                ">

                                <input type="checkbox" name="terms" value="1" required style="
                                        margin-top:4px;
                                    ">

                                <span>

                                    I confirm that the information
                                    provided is correct and I agree
                                    to proceed with this order.

                                </span>

                            </label>

                        </div>


                        <!-- =================================================
                             PLACE ORDER
                        ================================================== -->

                        <button type="submit" id="placeOrderButton" style="
                                width:100%;
                                border:none;
                                background:#22c55e;
                                color:#fff;
                                padding:16px 20px;
                                font-size:16px;
                                font-weight:600;
                                cursor:pointer;
                                text-transform:uppercase;
                            ">

                            <i class="fa fa-check-circle" style="margin-right:7px;"></i>

                            Place Order

                        </button>


                        <!-- BACK TO CART -->

                        <a href="cart.php" style="
                                display:block;
                                text-align:center;
                                margin-top:15px;
                                color:#555;
                                font-size:14px;
                            ">

                            <i class="fa fa-arrow-left" style="margin-right:5px;"></i>

                            Back to Cart

                        </a>

                    </div>

                </div>

            </div>

        </form>

    </div>

</div>


<!-- =========================================================
     CHECKOUT JAVASCRIPT
========================================================= -->

<script>
    document.addEventListener("DOMContentLoaded", function() {

        const checkoutForm =
            document.getElementById("checkout-form");

        const cartInput =
            document.getElementById("cart_json");


        if (!checkoutForm || !cartInput) {
            return;
        }


        checkoutForm.addEventListener("submit", function(event) {

            /*
            |--------------------------------------------------------------------------
            | GET CURRENT CART
            |--------------------------------------------------------------------------
            */


            /*
            |--------------------------------------------------------------------------
            | SEND CART TO PHP
            |--------------------------------------------------------------------------
            */

            cartInput.value =
                JSON.stringify(cart);


            /*
            |--------------------------------------------------------------------------
            | PREVENT DOUBLE ORDER
            |--------------------------------------------------------------------------
            */

            const button =
                document.getElementById(
                    "place-order-button"
                );


            if (button) {

                button.disabled = true;

                if (
                    button.tagName.toLowerCase() ===
                    "input"
                ) {

                    button.value =
                        "Processing Order...";

                } else {

                    button.innerHTML =
                        "Processing Order...";

                }

            }

        });

    });
</script>

include 'footer.php';

?>