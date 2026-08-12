<?php

/*
|--------------------------------------------------------------------------
| CORE LTD - SHOPPING CART
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/includes/cart-functions.php';

$cart = getCart();

$cartTotal = getCartTotal();

$cartCount = getCartCount();

include 'header.php';

?>


<!-- =========================================================
     BREADCRUMB
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

                <li class="active">
                    Shopping Cart
                </li>

            </ul>

        </div>

    </div>

</div>


<!-- =========================================================
     CART AREA
========================================================= -->

<div class="Shopping-cart-area pt-60 pb-60">

    <div class="container-fluid">

        <div class="row">

            <div class="col-12">


                <?php if (empty($cart)): ?>


                    <!-- =================================================
                         EMPTY CART
                    ================================================= -->

                    <div id="emptyCartMessage" style="
                            text-align:center;
                            padding:70px 20px;
                        ">

                        <i class="fa fa-shopping-cart" style="
                                font-size:70px;
                                color:#ddd;
                            "></i>


                        <h2 style="margin-top:20px;">

                            Your Cart is Empty

                        </h2>


                        <p>

                            You haven't added any products to your cart yet.

                        </p>


                        <a href="index.php" class="li-btn-2" style="
                                display:inline-block;
                                margin-top:20px;
                            ">

                            Continue Shopping

                        </a>

                    </div>


                <?php else: ?>


                    <!-- =================================================
                         CART TABLE
                    ================================================= -->

                    <div class="table-content table-responsive" id="cartTableContainer">

                        <table class="table" style="width:100%;">

                            <thead>

                                <tr>

                                    <th>
                                        Product
                                    </th>

                                    <th>
                                        Price
                                    </th>

                                    <th>
                                        Quantity
                                    </th>

                                    <th>
                                        Subtotal
                                    </th>

                                    <th>
                                        Remove
                                    </th>

                                </tr>

                            </thead>


                            <tbody>


                                <?php foreach ($cart as $item): ?>


                                    <?php

                                    $productId =
                                        (int) $item['id'];

                                    $quantity =
                                        (int) $item['quantity'];

                                    $price =
                                        (float) $item['price'];

                                    $subtotal =
                                        $price * $quantity;

                                    ?>


                                    <tr class="cart-item" data-product-id="<?= $productId ?>">


                                        <!-- PRODUCT -->

                                        <td>

                                            <div style="
                                                    display:flex;
                                                    align-items:center;
                                                    gap:15px;
                                                ">

                                                <?php if (!empty($item['image'])): ?>

                                                    <a href="product.php?slug=<?= urlencode($item['slug']) ?>">

                                                        <img src="uploads/products/<?= htmlspecialchars($item['image']) ?>"
                                                            alt="<?= htmlspecialchars($item['name']) ?>" style="
                                                                width:80px;
                                                                height:80px;
                                                                object-fit:contain;
                                                            ">

                                                    </a>

                                                <?php endif; ?>


                                                <a href="product.php?slug=<?= urlencode($item['slug']) ?>" style="
                                                        font-weight:600;
                                                    ">

                                                    <?= htmlspecialchars($item['name']) ?>

                                                </a>

                                            </div>

                                        </td>


                                        <!-- PRICE -->

                                        <td>

                                            Ksh.
                                            <?= number_format($price, 0) ?>

                                        </td>


                                        <!-- QUANTITY -->

                                        <td>

                                            <div style="
                                                    display:flex;
                                                    align-items:center;
                                                    justify-content:center;
                                                ">

                                                <button type="button" class="cart-quantity-minus" data-id="<?= $productId ?>"
                                                    style="
                                                        width:35px;
                                                        height:35px;
                                                        border:1px solid #ddd;
                                                        background:#f5f5f5;
                                                        cursor:pointer;
                                                    ">

                                                    −

                                                </button>


                                                <input type="text" class="cart-quantity-input" value="<?= $quantity ?>" readonly
                                                    style="
                                                        width:45px;
                                                        height:35px;
                                                        text-align:center;
                                                        border:1px solid #ddd;
                                                    ">


                                                <button type="button" class="cart-quantity-plus" data-id="<?= $productId ?>"
                                                    style="
                                                        width:35px;
                                                        height:35px;
                                                        border:1px solid #ddd;
                                                        background:#f5f5f5;
                                                        cursor:pointer;
                                                    ">

                                                    +

                                                </button>

                                            </div>

                                        </td>


                                        <!-- SUBTOTAL -->

                                        <td>

                                            <strong>

                                                Ksh.
                                                <?= number_format($subtotal, 0) ?>

                                            </strong>

                                        </td>


                                        <!-- REMOVE -->

                                        <td>

                                            <button type="button" class="remove-cart-item" data-id="<?= $productId ?>" style="
                                                    border:none;
                                                    background:none;
                                                    color:#dc3545;
                                                    font-size:20px;
                                                    cursor:pointer;
                                                ">

                                                <i class="fa fa-trash"></i>

                                            </button>

                                        </td>


                                    </tr>


                                <?php endforeach; ?>


                            </tbody>

                        </table>

                    </div>


                    <!-- =================================================
                         CART BUTTONS
                    ================================================= -->

                    <div style="
                            display:flex;
                            justify-content:space-between;
                            align-items:center;
                            margin-top:30px;
                            flex-wrap:wrap;
                            gap:15px;
                        ">

                        <a href="index.php" class="li-btn-2">

                            Continue Shopping

                        </a>


                        <button type="button" id="clearCart" class="li-btn-2" style="
                                border:none;
                                cursor:pointer;
                            ">

                            Clear Cart

                        </button>

                    </div>


                    <!-- =================================================
                         CART TOTAL
                    ================================================= -->

                    <div class="row" style="
                            margin-top:40px;
                        ">

                        <div class="col-md-6"></div>


                        <div class="col-md-6">

                            <div style="
                                    border:1px solid #ddd;
                                    padding:25px;
                                ">

                                <h3>
                                    Cart Totals
                                </h3>


                                <hr>


                                <div style="
                                        display:flex;
                                        justify-content:space-between;
                                        font-size:18px;
                                    ">

                                    <strong>
                                        Total:
                                    </strong>


                                    <strong id="cartTotalDisplay">

                                        Ksh.
                                        <?= number_format($cartTotal, 0) ?>

                                    </strong>

                                </div>


                                <a href="checkout.php" class="li-btn-2" style="
                                        display:block;
                                        text-align:center;
                                        margin-top:20px;
                                    ">

                                    Proceed to Checkout

                                </a>

                            </div>

                        </div>

                    </div>


                <?php endif; ?>


            </div>

        </div>

    </div>

</div>


<!-- =========================================================
     CART JAVASCRIPT
========================================================= -->

<script>
    $(document).ready(function() {


        /*
        |--------------------------------------------------------------------------
        | UPDATE HEADER COUNT
        |--------------------------------------------------------------------------
        */

        function updateHeaderCartCount(count) {

            count = parseInt(count) || 0;


            /*
            | Update whichever cart-count class
            | exists in the header.
            */

            $('.cart-item-count').text(count);

            $('.cart-count').text(count);

            $('.header-cart-count').text(count);


            /*
            | Also update elements using data-cart-count
            */

            $('[data-cart-count]').text(count);


            /*
            | Store the latest value locally.
            */



        }


        /*
        |--------------------------------------------------------------------------
        | UPDATE HEADER TOTAL
        |--------------------------------------------------------------------------
        */

        function updateHeaderCartTotal(total) {

            total = parseFloat(total) || 0;

            $('.header-cart-total').text(
                'Ksh. ' +
                Number(total).toLocaleString()
            );

        }


        /*
        |--------------------------------------------------------------------------
        | UPDATE SERVER CART
        |--------------------------------------------------------------------------
        */

        function sendCartAction(
            action,
            productId = 0
        ) {

            return $.ajax({

                url: 'cart-action.php',

                type: 'POST',

                dataType: 'json',

                data: {

                    action: action,

                    product_id: productId

                }

            });

        }


        /*
        |--------------------------------------------------------------------------
        | PLUS
        |--------------------------------------------------------------------------
        */

        $(document).on(
            'click',
            '.cart-quantity-plus',
            function() {

                const button = $(this);

                const productId =
                    parseInt(button.data('id'));


                button.prop(
                    'disabled',
                    true
                );


                sendCartAction(
                        'increase',
                        productId
                    )

                    .done(function(response) {

                        if (response.success) {

                            updateHeaderCartCount(
                                response.cart_count
                            );

                            updateHeaderCartTotal(
                                response.cart_total
                            );


                            /*
                            | Reload so the product quantity,
                            | subtotal and total are refreshed.
                            */

                            location.reload();

                        } else {

                            alert(response.message);

                        }

                    })

                    .fail(function() {

                        alert(
                            'Unable to update the cart. Please try again.'
                        );

                    })

                    .always(function() {

                        button.prop(
                            'disabled',
                            false
                        );

                    });

            }
        );


        /*
        |--------------------------------------------------------------------------
        | MINUS
        |--------------------------------------------------------------------------
        */

        $(document).on(
            'click',
            '.cart-quantity-minus',
            function() {

                const button = $(this);

                const productId =
                    parseInt(button.data('id'));


                button.prop(
                    'disabled',
                    true
                );


                sendCartAction(
                        'decrease',
                        productId
                    )

                    .done(function(response) {

                        if (response.success) {

                            updateHeaderCartCount(
                                response.cart_count
                            );

                            updateHeaderCartTotal(
                                response.cart_total
                            );


                            location.reload();

                        } else {

                            alert(response.message);

                        }

                    })

                    .fail(function() {

                        alert(
                            'Unable to update the cart. Please try again.'
                        );

                    })

                    .always(function() {

                        button.prop(
                            'disabled',
                            false
                        );

                    });

            }
        );


        /*
        |--------------------------------------------------------------------------
        | REMOVE
        |--------------------------------------------------------------------------
        */

        $(document).on(
            'click',
            '.remove-cart-item',
            function() {

                const button = $(this);

                const productId =
                    parseInt(button.data('id'));


                if (
                    !confirm(
                        'Remove this product from your cart?'
                    )
                ) {

                    return;

                }


                button.prop(
                    'disabled',
                    true
                );


                sendCartAction(
                        'remove',
                        productId
                    )

                    .done(function(response) {

                        if (response.success) {

                            updateHeaderCartCount(
                                response.cart_count
                            );

                            updateHeaderCartTotal(
                                response.cart_total
                            );


                            location.reload();

                        } else {

                            alert(response.message);

                        }

                    })

                    .fail(function() {

                        alert(
                            'Unable to remove the product. Please try again.'
                        );

                    });

            }
        );


        /*
        |--------------------------------------------------------------------------
        | CLEAR CART
        |--------------------------------------------------------------------------
        */

        $(document).on(
            'click',
            '#clearCart',
            function() {


                if (
                    !confirm(
                        'Are you sure you want to clear your cart?'
                    )
                ) {

                    return;

                }


                const button = $(this);


                button.prop(
                    'disabled',
                    true
                );


                sendCartAction(
                        'clear'
                    )

                    .done(function(response) {

                        if (response.success) {

                            /*
                            | Immediately update header to 0
                            */

                            updateHeaderCartCount(0);

                            updateHeaderCartTotal(0);


                            /*
                            | Reload page
                            */

                            location.reload();

                        } else {

                            alert(response.message);

                            button.prop(
                                'disabled',
                                false
                            );

                        }

                    })

                    .fail(function() {

                        alert(
                            'Unable to clear the cart. Please try again.'
                        );

                        button.prop(
                            'disabled',
                            false
                        );

                    });

            }
        );


        /*
        |--------------------------------------------------------------------------
        | GET CURRENT SERVER CART COUNT
        |--------------------------------------------------------------------------
        |
        | This makes sure the header is synchronized
        | with the PHP session whenever cart.php loads.
        |
        */

        sendCartAction(
                'get_count'
            )

            .done(function(response) {

                if (response.success) {

                    updateHeaderCartCount(
                        response.cart_count
                    );

                    updateHeaderCartTotal(
                        response.cart_total
                    );

                }

            });


    });
</script>


<?php

include 'footer.php';

?>