<?php

/*
|--------------------------------------------------------------------------
| CORE LTD - PLACE ORDER
|--------------------------------------------------------------------------
*/

session_start();

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/email.php';


/*
|--------------------------------------------------------------------------
| ONLY ACCEPT POST
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header("Location: checkout.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| GET CUSTOMER DETAILS
|--------------------------------------------------------------------------
*/

$fullName = trim(
    $_POST['full_name']
        ?? $_POST['name']
        ?? $_POST['customer_name']
        ?? ''
);

$phone = trim(
    $_POST['phone']
        ?? $_POST['customer_phone']
        ?? ''
);

$email = trim(
    $_POST['email']
        ?? $_POST['customer_email']
        ?? ''
);

$town = trim(
    $_POST['town']
        ?? $_POST['town_area']
        ?? $_POST['area']
        ?? ''
);

$deliveryAddress = trim(
    $_POST['delivery_address']
        ?? $_POST['address']
        ?? $_POST['delivery_location']
        ?? ''
);

$orderNotes = trim(
    $_POST['order_notes']
        ?? $_POST['notes']
        ?? $_POST['order_note']
        ?? ''
);


/*
|--------------------------------------------------------------------------
| VALIDATE CUSTOMER INFORMATION
|--------------------------------------------------------------------------
*/

if ($fullName === '') {

    die("Please enter your full name.");
}


if ($phone === '') {

    die("Please enter your phone number.");
}


if ($town === '') {

    die("Please enter your town or area.");
}


if ($deliveryAddress === '') {

    die("Please enter your delivery address or location.");
}


if (
    $email !== ''
    &&
    !filter_var($email, FILTER_VALIDATE_EMAIL)
) {

    die("Please enter a valid email address.");
}


/*
|--------------------------------------------------------------------------
| GET THE REAL CART FROM PHP SESSION
|--------------------------------------------------------------------------
|
| IMPORTANT:
| We do NOT use localStorage.
| We do NOT use cart_json.
|
| This is the exact same cart used by cart.php.
|
*/

$cart = $_SESSION['cart'] ?? [];


/*
|--------------------------------------------------------------------------
| CHECK CART
|--------------------------------------------------------------------------
*/

if (
    !is_array($cart)
    ||
    empty($cart)
) {

    die("Your cart is empty. Please add a product before placing your order.");
}


/*
|--------------------------------------------------------------------------
| PREPARE VALIDATED ITEMS
|--------------------------------------------------------------------------
*/

$validatedItems = [];

$subtotal = 0;


/*
|--------------------------------------------------------------------------
| PROCESS EVERY CART ITEM
|--------------------------------------------------------------------------
*/

foreach ($cart as $cartItem) {


    /*
    |--------------------------------------------------------------------------
    | PRODUCT ID
    |--------------------------------------------------------------------------
    */

    $productId = (int)(
        $cartItem['id']
        ?? 0
    );


    /*
    |--------------------------------------------------------------------------
    | QUANTITY
    |--------------------------------------------------------------------------
    */

    $quantity = (int)(
        $cartItem['quantity']
        ?? 0
    );


    if (
        $productId <= 0
        ||
        $quantity <= 0
    ) {

        continue;
    }


    /*
    |--------------------------------------------------------------------------
    | GET REAL PRODUCT FROM DATABASE
    |--------------------------------------------------------------------------
    */

    $productStmt = $conn->prepare("
        SELECT
            id,
            name,
            sku,
            price,
            main_image,
            status
        FROM products
        WHERE id = ?
        AND status = 1
        LIMIT 1
    ");


    if (!$productStmt) {

        die("Database error while checking the product.");
    }


    $productStmt->bind_param(
        "i",
        $productId
    );


    $productStmt->execute();


    $productResult =
        $productStmt->get_result();


    /*
    |--------------------------------------------------------------------------
    | PRODUCT NO LONGER AVAILABLE
    |--------------------------------------------------------------------------
    */

    if (
        $productResult->num_rows === 0
    ) {

        $productStmt->close();

        die("One of the products in your cart is no longer available.");
    }


    $product =
        $productResult->fetch_assoc();


    $productStmt->close();


    /*
    |--------------------------------------------------------------------------
    | USE DATABASE PRICE
    |--------------------------------------------------------------------------
    */

    $unitPrice =
        (float)$product['price'];


    /*
    |--------------------------------------------------------------------------
    | CALCULATE ITEM SUBTOTAL
    |--------------------------------------------------------------------------
    */

    $itemSubtotal =
        $unitPrice * $quantity;


    $subtotal +=
        $itemSubtotal;


    /*
    |--------------------------------------------------------------------------
    | SAVE VALIDATED ITEM
    |--------------------------------------------------------------------------
    */

    $validatedItems[] = [

        'product_id' =>
        (int)$product['id'],

        'product_name' =>
        $product['name'],

        'product_reference' =>
        $product['sku'] ?? '',

        'product_image' =>
        $product['main_image'] ?? '',

        'quantity' =>
        $quantity,

        'unit_price' =>
        $unitPrice,

        'subtotal' =>
        $itemSubtotal

    ];
}


/*
|--------------------------------------------------------------------------
| MAKE SURE THERE ARE VALID ITEMS
|--------------------------------------------------------------------------
*/

if (
    empty($validatedItems)
) {

    die("Your cart does not contain any valid products.");
}


/*
|--------------------------------------------------------------------------
| DELIVERY FEE
|--------------------------------------------------------------------------
|
| Currently FREE.
|
*/

$deliveryFee = 0;


/*
|--------------------------------------------------------------------------
| TOTAL
|--------------------------------------------------------------------------
*/

$totalAmount =
    $subtotal + $deliveryFee;


/*
|--------------------------------------------------------------------------
| DELIVERY LOCATION
|--------------------------------------------------------------------------
*/

$fullDeliveryAddress =
    $town . ', ' . $deliveryAddress;


/*
|--------------------------------------------------------------------------
| GENERATE ORDER NUMBER
|--------------------------------------------------------------------------
*/

$orderNumber =
    'CORE-' .
    date('Ymd') .
    '-' .
    strtoupper(
        substr(
            bin2hex(
                random_bytes(4)
            ),
            0,
            6
        )
    );


/*
|--------------------------------------------------------------------------
| DATABASE TRANSACTION
|--------------------------------------------------------------------------
*/

$conn->begin_transaction();


try {


    /*
    |--------------------------------------------------------------------------
    | INSERT ORDER
    |--------------------------------------------------------------------------
    */

    $orderStmt = $conn->prepare("
        INSERT INTO orders (

            order_number,

            customer_name,

            customer_phone,

            customer_email,

            delivery_address,

            customer_notes,

            subtotal,

            delivery_fee,

            total_amount,

            payment_method,

            payment_status,

            order_status

        )

        VALUES (

            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?

        )
    ");


    if (!$orderStmt) {

        throw new Exception(
            "Unable to prepare order."
        );
    }


    /*
    |--------------------------------------------------------------------------
    | INITIAL STATUS
    |--------------------------------------------------------------------------
    */

    $paymentMethod =
        'Pending';

    $paymentStatus =
        'Pending';

    $orderStatus =
        'Pending';


    /*
    |--------------------------------------------------------------------------
    | BIND ORDER DATA
    |--------------------------------------------------------------------------
    */

    $orderStmt->bind_param(

        "ssssssdddsss",

        $orderNumber,

        $fullName,

        $phone,

        $email,

        $fullDeliveryAddress,

        $orderNotes,

        $subtotal,

        $deliveryFee,

        $totalAmount,

        $paymentMethod,

        $paymentStatus,

        $orderStatus

    );


    /*
    |--------------------------------------------------------------------------
    | SAVE ORDER
    |--------------------------------------------------------------------------
    */

    if (
        !$orderStmt->execute()
    ) {

        throw new Exception(
            "Unable to save order."
        );
    }


    /*
    |--------------------------------------------------------------------------
    | GET ORDER ID
    |--------------------------------------------------------------------------
    */

    $orderId =
        $conn->insert_id;


    $orderStmt->close();


    /*
    |--------------------------------------------------------------------------
    | INSERT ORDER ITEMS
    |--------------------------------------------------------------------------
    */

    $itemStmt = $conn->prepare("
        INSERT INTO order_items (

            order_id,

            product_id,

            product_name,

            product_reference,

            product_image,

            quantity,

            unit_price,

            subtotal

        )

        VALUES (

            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?

        )
    ");


    if (!$itemStmt) {

        throw new Exception(
            "Unable to prepare order items."
        );
    }


    /*
    |--------------------------------------------------------------------------
    | SAVE EACH PRODUCT
    |--------------------------------------------------------------------------
    */

    foreach (
        $validatedItems
        as $item
    ) {


        $productId =
            $item['product_id'];


        $productName =
            $item['product_name'];


        $productReference =
            $item['product_reference'];


        $productImage =
            $item['product_image'];


        $quantity =
            $item['quantity'];


        $unitPrice =
            $item['unit_price'];


        $itemSubtotal =
            $item['subtotal'];


        $itemStmt->bind_param(

            "iisssidd",

            $orderId,

            $productId,

            $productName,

            $productReference,

            $productImage,

            $quantity,

            $unitPrice,

            $itemSubtotal

        );


        if (
            !$itemStmt->execute()
        ) {

            throw new Exception(
                "Unable to save order item."
            );
        }
    }


    $itemStmt->close();


    /*
    |--------------------------------------------------------------------------
    | CREATE ADMIN ORDER NOTIFICATION
    |--------------------------------------------------------------------------
    |
    | This records the new order for the admin notification bell.
    | It is inside the same transaction as the order.
    |
    */
    $notificationTitle = 'New Order Received';
    $notificationMessage =
        'Order #' . $orderNumber .
        ' from ' . $fullName .
        ' - KSh ' . number_format($totalAmount, 2);

    $notificationType = 'new_order';
    $notificationIsRead = 0;

    $notificationStmt = $conn->prepare("
        INSERT INTO admin_notifications (
            order_id,
            type,
            title,
            message,
            is_read,
            created_at
        )
        VALUES (?, ?, ?, ?, ?, NOW())
    ");

    if (!$notificationStmt) {
        throw new Exception(
            "Unable to prepare admin notification."
        );
    }

    $notificationStmt->bind_param(
        "isssi",
        $orderId,
        $notificationType,
        $notificationTitle,
        $notificationMessage,
        $notificationIsRead
    );

    if (!$notificationStmt->execute()) {
        $notificationStmt->close();

        throw new Exception(
            "Unable to create admin notification."
        );
    }

    $notificationStmt->close();


    /*
    |--------------------------------------------------------------------------
    | COMMIT ORDER
    |--------------------------------------------------------------------------
    */

    $conn->commit();


    /*
|--------------------------------------------------------------------------
| SEND EMAIL NOTIFICATIONS
|--------------------------------------------------------------------------
*/

    $emailResult = sendOrderEmails(
        $orderNumber,
        $fullName,
        $phone,
        $email,
        $town,
        $deliveryAddress,
        $orderNotes,
        $validatedItems,
        $subtotal,
        $deliveryFee,
        $totalAmount
    );


    /*
|--------------------------------------------------------------------------
| CLEAR CART
|--------------------------------------------------------------------------
*/

    unset($_SESSION['cart']);

    /*
    |--------------------------------------------------------------------------
    | SAVE ORDER DETAILS FOR SUCCESS PAGE
    |--------------------------------------------------------------------------
    */
    $_SESSION['last_order_number'] = $orderNumber;

    $_SESSION['last_order_total'] = $totalAmount;

    $_SESSION['last_customer_name'] = $fullName;

    $_SESSION['last_customer_phone'] = $phone;

    $_SESSION['last_order_items'] = $validatedItems;

    /*
    |--------------------------------------------------------------------------
    | REDIRECT TO SUCCESS PAGE
    |--------------------------------------------------------------------------
    */

    header(
        "Location: order-success.php?order=" .
            urlencode(
                $orderNumber
            )
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| ERROR
|--------------------------------------------------------------------------
*/ catch (
    Exception $e
) {


    /*
    |--------------------------------------------------------------------------
    | ROLLBACK
    |--------------------------------------------------------------------------
    */

    $conn->rollback();


    echo '

    <!DOCTYPE html>

    <html>

    <head>

        <title>
            CORE LTD - Order Error
        </title>

        <meta
            name="viewport"
            content="width=device-width, initial-scale=1"
        >

        <style>

            body {

                font-family: Arial, sans-serif;

                background: #f5f5f5;

                padding: 50px 20px;

            }

            .error-box {

                max-width: 600px;

                margin: auto;

                background: white;

                padding: 40px;

                text-align: center;

                border-radius: 8px;

                box-shadow:
                    0 5px 25px
                    rgba(0,0,0,.08);

            }

            h1 {

                color: #dc3545;

            }

            a {

                display: inline-block;

                margin-top: 20px;

                padding: 12px 25px;

                background: #222;

                color: white;

                text-decoration: none;

                border-radius: 4px;

            }

        </style>

    </head>

    <body>

        <div class="error-box">

            <h1>
                Order Could Not Be Completed
            </h1>

            <p>
                We were unable to process your order.
            </p>

            <a href="checkout.php">
                Return to Checkout
            </a>

        </div>

    </body>

    </html>

    ';

    exit;
}