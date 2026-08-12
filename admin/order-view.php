<?php
session_start();

require_once __DIR__ . '/../includes/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

function e($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function money($amount): string
{
    return 'KSh ' . number_format((float)$amount, 2);
}

function badgeClass(string $value): string
{
    $v = strtolower(trim($value));

    return match (true) {
        in_array($v, ['paid', 'completed'], true) => 'success',
        in_array($v, ['processing', 'ready'], true) => 'info',
        in_array($v, ['pending', 'new'], true) => 'warning',
        in_array($v, ['cancelled', 'failed', 'refunded'], true) => 'danger',
        default => 'neutral',
    };
}

$orderId = (int)($_GET['id'] ?? 0);

if ($orderId <= 0) {
    header("Location: orders.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| UPDATE ORDER STATUS
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? '';

    if ($action === 'update_status') {

        $newStatus = trim($_POST['order_status'] ?? '');

        $allowedStatuses = [
            'New',
            'Pending',
            'Paid',
            'Processing',
            'Ready',
            'Completed',
            'Cancelled'
        ];

        if (in_array($newStatus, $allowedStatuses, true)) {

            $stmt = $conn->prepare("
                UPDATE orders
                SET order_status = ?
                WHERE id = ?
            ");

            if ($stmt) {
                $stmt->bind_param(
                    "si",
                    $newStatus,
                    $orderId
                );

                $stmt->execute();
                $stmt->close();
            }
        }

        header(
            "Location: order-view.php?id=" .
                $orderId .
                "&updated=1"
        );
        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE PAYMENT STATUS
    |--------------------------------------------------------------------------
    */
    if ($action === 'update_payment') {

        $newPaymentStatus = trim(
            $_POST['payment_status'] ?? ''
        );

        $allowedPaymentStatuses = [
            'Pending',
            'Paid',
            'Failed',
            'Refunded'
        ];

        if (
            in_array(
                $newPaymentStatus,
                $allowedPaymentStatuses,
                true
            )
        ) {

            $stmt = $conn->prepare("
                UPDATE orders
                SET payment_status = ?
                WHERE id = ?
            ");

            if ($stmt) {
                $stmt->bind_param(
                    "si",
                    $newPaymentStatus,
                    $orderId
                );

                $stmt->execute();
                $stmt->close();
            }
        }

        header(
            "Location: order-view.php?id=" .
                $orderId .
                "&updated=1"
        );
        exit;
    }
}

/*
|--------------------------------------------------------------------------
| LOAD ORDER
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
    SELECT
        id,
        order_number,
        customer_name,
        customer_phone,
        customer_email,
        delivery_address,
        customer_notes,
        whatsapp_number,
        subtotal,
        delivery_fee,
        total_amount,
        payment_method,
        payment_status,
        order_status,
        created_at
    FROM orders
    WHERE id = ?
    LIMIT 1
");

if (!$stmt) {
    die("Unable to load order: " .
        e($conn->error));
}

$stmt->bind_param("i", $orderId);
$stmt->execute();

$result = $stmt->get_result();
$order = $result->fetch_assoc();

$stmt->close();

if (!$order) {
    header("Location: orders.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| LOAD ORDER ITEMS
|
| IMPORTANT:
| Your actual order_items table contains:
|
| id
| order_id
| product_id
| product_name
| product_reference
| product_image
| quantity
| unit_price
| subtotal
| created_at
|
| Therefore we DO NOT use oi.total_price.
|--------------------------------------------------------------------------
*/
$itemStmt = $conn->prepare("
    SELECT
        oi.id,
        oi.product_id,
        oi.product_name,
        oi.product_reference,
        oi.product_image,
        oi.quantity,
        oi.unit_price,
        oi.subtotal,
        oi.created_at
    FROM order_items oi
    WHERE oi.order_id = ?
    ORDER BY oi.id ASC
");

if (!$itemStmt) {
    die("Unable to load order items: " .
        e($conn->error));
}

$itemStmt->bind_param("i", $orderId);
$itemStmt->execute();

$itemResult = $itemStmt->get_result();

$items = [];

while ($row = $itemResult->fetch_assoc()) {
    $items[] = $row;
}

$itemStmt->close();

/*
|--------------------------------------------------------------------------
| WHATSAPP FOLLOW-UP
|--------------------------------------------------------------------------
*/
$whatsappNumber = trim(
    (string)($order['whatsapp_number'] ?? '')
);

$whatsappDigits = preg_replace(
    '/[^0-9]/',
    '',
    $whatsappNumber
);

$customerName = $order['customer_name'] ?? '';

$followupMessage =
    "Hello CORE LTD, I am following up on Order #" .
    $order['order_number'] .
    " for " .
    $customerName .
    ".";

$followupUrl = '';

if ($whatsappDigits !== '') {

    $followupUrl =
        'https://wa.me/' .
        $whatsappDigits .
        '?text=' .
        rawurlencode($followupMessage);
}

$updated = isset($_GET['updated']);

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        Order #<?= e($order['order_number']) ?> | CORE LTD Admin
    </title>

    <style>
    * {
        box-sizing: border-box;
    }

    body {
        margin: 0;
        font-family:
            Inter,
            Arial,
            Helvetica,
            sans-serif;

        background: #f4f8fc;
        color: #243b53;
    }

    a {
        text-decoration: none;
        color: inherit;
    }

    .page {
        min-height: 100vh;
        padding: 28px;
    }

    /* HEADER */

    .topbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        margin-bottom: 22px;
    }

    .title h1 {
        margin: 0;
        color: #183b62;
        font-size: 24px;
    }

    .title p {
        margin: 6px 0 0;
        color: #8294a8;
        font-size: 11px;
    }

    .back {
        padding: 10px 15px;
        border: 1px solid #dce7f1;
        border-radius: 8px;
        background: #fff;
        color: #0875df;
        font-size: 10px;
        font-weight: 800;
    }

    /* ALERT */

    .alert {
        margin-bottom: 18px;
        padding: 12px 15px;
        border-radius: 8px;
        background: #eaf8ef;
        border: 1px solid #c9edd5;
        color: #15803d;
        font-size: 10px;
        font-weight: 700;
    }

    /* LAYOUT */

    .grid {
        display: grid;
        grid-template-columns:
            minmax(0, 1.65fr) minmax(290px, .85fr);

        gap: 18px;
    }

    .card {
        background: #fff;
        border: 1px solid #e1eaf2;
        border-radius: 12px;

        box-shadow:
            0 5px 18px rgba(29, 78, 121, .05);

        overflow: hidden;
        margin-bottom: 18px;
    }

    .card:last-child {
        margin-bottom: 0;
    }

    .card-title {
        padding: 16px 19px;
        border-bottom: 1px solid #e7eef5;
        color: #183b62;
        font-size: 13px;
        font-weight: 800;
    }

    .card-body {
        padding: 18px 19px;
    }

    /* ORDER SUMMARY */

    .order-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 15px;
    }

    .order-number {
        color: #0875df;
        font-size: 18px;
        font-weight: 900;
    }

    .order-date {
        margin-top: 5px;
        color: #8b9aaa;
        font-size: 10px;
    }

    /* BADGES */

    .badge {
        display: inline-flex;
        align-items: center;
        padding: 6px 10px;
        border-radius: 20px;
        font-size: 8px;
        font-weight: 900;
        white-space: nowrap;
    }

    .badge.success {
        color: #15803d;
        background: #eaf8ef;
    }

    .badge.info {
        color: #0875df;
        background: #eaf4ff;
    }

    .badge.warning {
        color: #b45309;
        background: #fff6df;
    }

    .badge.danger {
        color: #dc2626;
        background: #fff0f0;
    }

    .badge.neutral {
        color: #64748b;
        background: #f1f5f9;
    }

    /* PRODUCTS */

    .item {
        display: grid;
        grid-template-columns:
            66px minmax(0, 1fr) auto;

        gap: 13px;
        align-items: center;

        padding: 13px 0;

        border-bottom:
            1px solid #edf2f6;
    }

    .item:last-child {
        border-bottom: 0;
    }

    .product-image {
        width: 66px;
        height: 66px;

        border-radius: 8px;
        border: 1px solid #e3ebf3;

        background: #f7fafd;

        object-fit: contain;
    }

    .product-name {
        color: #314e6b;
        font-size: 11px;
        font-weight: 800;
    }

    .product-reference {
        display: inline-block;
        margin-top: 4px;

        padding: 3px 6px;

        border-radius: 4px;

        background: #eef5fb;
        color: #587089;

        font-size: 8px;
        font-weight: 800;
    }

    .product-meta {
        margin-top: 5px;
        color: #8a9bad;
        font-size: 9px;
    }

    .product-price {
        color: #183b62;
        font-size: 11px;
        font-weight: 900;
        text-align: right;
        white-space: nowrap;
    }

    /* TOTALS */

    .totals {
        margin-top: 15px;
        border-top: 1px solid #e5edf4;
        padding-top: 13px;
    }

    .total-row {
        display: flex;
        justify-content: space-between;
        padding: 5px 0;

        color: #71849a;
        font-size: 10px;
    }

    .total-row.grand {
        margin-top: 7px;
        padding-top: 12px;

        border-top: 1px solid #e5edf4;

        color: #183b62;
        font-size: 14px;
        font-weight: 900;
    }

    /* DETAILS */

    .details {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
    }

    .detail-label {
        margin-bottom: 5px;

        color: #8a9bad;

        font-size: 8px;
        font-weight: 800;

        text-transform: uppercase;
        letter-spacing: .3px;
    }

    .detail-value {
        color: #405b75;

        font-size: 10px;
        font-weight: 700;

        line-height: 1.5;
    }

    .detail-full {
        grid-column: 1 / -1;
    }

    /* WHATSAPP */

    .whatsapp-box {
        background: #f0fbf4;

        border: 1px solid #d5f0dd;

        border-radius: 9px;

        padding: 15px;
    }

    .whatsapp-label {
        color: #6c8d77;

        font-size: 8px;
        font-weight: 800;

        text-transform: uppercase;
    }

    .whatsapp-number {
        margin-top: 5px;

        color: #15803d;

        font-size: 16px;
        font-weight: 900;
    }

    .whatsapp-btn {
        display: inline-flex;

        align-items: center;
        justify-content: center;

        width: 100%;

        margin-top: 12px;

        padding: 10px 13px;

        border-radius: 7px;

        background: #16a34a;
        color: #fff;

        font-size: 9px;
        font-weight: 900;
    }

    .whatsapp-btn:hover {
        background: #12833c;
    }

    /* FORMS */

    .form-group {
        margin-bottom: 13px;
    }

    .form-group:last-child {
        margin-bottom: 0;
    }

    .form-group label {
        display: block;

        margin-bottom: 6px;

        color: #71849a;

        font-size: 8px;
        font-weight: 800;

        text-transform: uppercase;
    }

    select {
        width: 100%;
        height: 38px;

        padding: 0 10px;

        border: 1px solid #d9e4ee;
        border-radius: 7px;

        background: #fbfdff;

        color: #405b75;

        font-size: 10px;

        outline: none;
    }

    select:focus {
        border-color: #0875df;
    }

    .btn {
        width: 100%;
        height: 38px;

        border: 0;
        border-radius: 7px;

        cursor: pointer;

        font-size: 9px;
        font-weight: 900;
    }

    .btn-blue {
        background: #0875df;
        color: #fff;
    }

    .btn-blue:hover {
        background: #0567c5;
    }

    /* NOTES */

    .notes {
        padding: 12px;

        border-radius: 8px;

        background: #f7fafd;

        color: #60758b;

        font-size: 10px;

        line-height: 1.6;
    }

    /* RESPONSIVE */

    @media (max-width: 900px) {

        .grid {
            grid-template-columns: 1fr;
        }

    }

    @media (max-width: 600px) {

        .page {
            padding: 15px;
        }

        .details {
            grid-template-columns: 1fr;
        }

        .detail-full {
            grid-column: auto;
        }

        .item {
            grid-template-columns:
                55px minmax(0, 1fr);
        }

        .product-price {
            grid-column: 2;
            text-align: left;
        }

    }
    </style>

</head>

<body>

    <div class="page">

        <!-- HEADER -->

        <div class="topbar">

            <div class="title">

                <h1>
                    Order Details
                </h1>

                <p>
                    View and manage customer order
                </p>

            </div>

            <div style="display:flex; gap:8px; align-items:center;">

                <a href="orders.php" class="back">
                    ← Back to Orders
                </a>

                <a href="order-print.php?id=<?= (int)$order['id'] ?>" class="back" target="_blank">
                    🖨 Print Order
                </a>

            </div>

        </div>


        <?php if ($updated): ?>

        <div class="alert">
            ✓ Order updated successfully.
        </div>

        <?php endif; ?>


        <div class="grid">

            <!-- LEFT COLUMN -->

            <div>

                <!-- ORDER SUMMARY -->

                <div class="card">

                    <div class="card-body">

                        <div class="order-head">

                            <div>

                                <div class="order-number">
                                    #<?= e($order['order_number']) ?>
                                </div>

                                <div class="order-date">

                                    <?= e(
                                        date(
                                            'd M Y, H:i',
                                            strtotime($order['created_at'])
                                        )
                                    ) ?>

                                </div>

                            </div>


                            <span class="badge <?= badgeClass($order['order_status']) ?>">
                                <?= e($order['order_status']) ?>
                            </span>

                        </div>

                    </div>

                </div>


                <!-- PRODUCTS -->

                <div class="card">

                    <div class="card-title">
                        Ordered Products
                    </div>

                    <div class="card-body">

                        <?php if (empty($items)): ?>

                        <div class="notes">
                            No order items were found for this order.
                        </div>

                        <?php else: ?>

                        <?php foreach ($items as $item): ?>

                        <?php

                                /*
                            --------------------------------------------------
                            PRODUCT IMAGE
                            --------------------------------------------------

                            Your order_items table already stores:

                            product_image

                            Example:
                            Tenda F3.jpg

                            We use that directly.
                            --------------------------------------------------
                            */

                                $image = trim(
                                    (string)($item['product_image'] ?? '')
                                );

                                if ($image !== '') {

                                    if (
                                        str_starts_with(
                                            $image,
                                            'http://'
                                        ) ||
                                        str_starts_with(
                                            $image,
                                            'https://'
                                        )
                                    ) {

                                        $imageUrl = $image;
                                    } else {

                                        /*
                                    Try the product upload locations.
                                    The first location should match the
                                    normal CORE LTD product image storage.
                                    */

                                        $imageUrl =
                                            '../uploads/products/' .
                                            ltrim($image, '/');
                                    }
                                } else {

                                    $imageUrl =
                                        '../assets/images/no-image.png';
                                }

                                ?>


                        <div class="item">

                            <img src="<?= e($imageUrl) ?>" alt="<?= e($item['product_name']) ?>" class="product-image"
                                onerror="
                                        if (!this.dataset.retry) {
                                            this.dataset.retry='1';
                                            this.src='../upload/product/' +
                                                encodeURIComponent(
                                                    '<?= e($item['product_image']) ?>'
                                                );
                                        } else {
                                            this.src='../assets/images/no-image.png';
                                        }
                                    ">


                            <div>

                                <div class="product-name">

                                    <?= e(
                                                $item['product_name']
                                            ) ?>

                                </div>


                                <?php if (
                                            !empty($item['product_reference'])
                                        ): ?>

                                <span class="product-reference">
                                    Ref:
                                    <?= e(
                                                    $item['product_reference']
                                                ) ?>
                                </span>

                                <?php endif; ?>


                                <div class="product-meta">

                                    Qty:
                                    <?= number_format(
                                                (int)$item['quantity']
                                            ) ?>

                                    &nbsp; × &nbsp;

                                    <?= money(
                                                $item['unit_price']
                                            ) ?>

                                </div>

                            </div>


                            <div class="product-price">

                                <?= money(
                                            $item['subtotal']
                                        ) ?>

                            </div>

                        </div>

                        <?php endforeach; ?>


                        <!-- TOTALS -->

                        <div class="totals">

                            <div class="total-row">

                                <span>
                                    Subtotal
                                </span>

                                <span>
                                    <?= money(
                                            $order['subtotal']
                                        ) ?>
                                </span>

                            </div>


                            <div class="total-row">

                                <span>
                                    Delivery
                                </span>

                                <span>
                                    <?= money(
                                            $order['delivery_fee']
                                        ) ?>
                                </span>

                            </div>


                            <div class="total-row grand">

                                <span>
                                    TOTAL
                                </span>

                                <span>
                                    <?= money(
                                            $order['total_amount']
                                        ) ?>
                                </span>

                            </div>

                        </div>

                        <?php endif; ?>

                    </div>

                </div>


                <!-- CUSTOMER -->

                <div class="card">

                    <div class="card-title">
                        Customer & Delivery Details
                    </div>

                    <div class="card-body">

                        <div class="details">

                            <div>

                                <div class="detail-label">
                                    Customer Name
                                </div>

                                <div class="detail-value">
                                    <?= e(
                                        $order['customer_name']
                                    ) ?>
                                </div>

                            </div>


                            <div>

                                <div class="detail-label">
                                    Phone
                                </div>

                                <div class="detail-value">
                                    <?= e(
                                        $order['customer_phone']
                                    ) ?>
                                </div>

                            </div>


                            <div>

                                <div class="detail-label">
                                    Email
                                </div>

                                <div class="detail-value">

                                    <?= e(
                                        $order['customer_email']
                                            ?: 'Not provided'
                                    ) ?>

                                </div>

                            </div>


                            <div>

                                <div class="detail-label">
                                    Payment Method
                                </div>

                                <div class="detail-value">

                                    <?= e(
                                        $order['payment_method']
                                            ?: 'WhatsApp'
                                    ) ?>

                                </div>

                            </div>


                            <div class="detail-full">

                                <div class="detail-label">
                                    Delivery Address
                                </div>

                                <div class="detail-value">

                                    <?= nl2br(
                                        e(
                                            $order['delivery_address']
                                        )
                                    ) ?>

                                </div>

                            </div>


                            <?php if (
                                !empty($order['customer_notes'])
                            ): ?>

                            <div class="detail-full">

                                <div class="detail-label">
                                    Customer Notes
                                </div>

                                <div class="notes">

                                    <?= nl2br(
                                            e(
                                                $order['customer_notes']
                                            )
                                        ) ?>

                                </div>

                            </div>

                            <?php endif; ?>

                        </div>

                    </div>

                </div>

            </div>


            <!-- RIGHT COLUMN -->

            <div>

                <!-- WHATSAPP -->

                <div class="card">

                    <div class="card-title">
                        WhatsApp Order
                    </div>

                    <div class="card-body">

                        <div class="whatsapp-box">

                            <div class="whatsapp-label">
                                Order received by
                            </div>


                            <?php if (
                                $whatsappNumber !== ''
                            ): ?>

                            <div class="whatsapp-number">

                                +<?= e(
                                            ltrim(
                                                $whatsappNumber,
                                                '+'
                                            )
                                        ) ?>

                            </div>


                            <?php if (
                                    $followupUrl !== ''
                                ): ?>

                            <a href="<?= e(
                                                    $followupUrl
                                                ) ?>" target="_blank" rel="noopener" class="whatsapp-btn">
                                💬 Follow Up on WhatsApp
                            </a>

                            <?php endif; ?>

                            <?php else: ?>

                            <div class="detail-value">
                                WhatsApp number was not recorded for this order.
                            </div>

                            <?php endif; ?>

                        </div>

                    </div>

                </div>


                <!-- ORDER STATUS -->

                <div class="card">

                    <div class="card-title">
                        Order Status
                    </div>

                    <div class="card-body">

                        <form method="POST">

                            <input type="hidden" name="action" value="update_status">


                            <div class="form-group">

                                <label>
                                    Current Order Status
                                </label>


                                <select name="order_status">

                                    <?php

                                    $statuses = [
                                        'New',
                                        'Pending',
                                        'Paid',
                                        'Processing',
                                        'Ready',
                                        'Completed',
                                        'Cancelled'
                                    ];

                                    ?>

                                    <?php foreach (
                                        $statuses
                                        as $status
                                    ): ?>

                                    <option value="<?= e($status) ?>" <?= (
                                                                                $order['order_status']
                                                                                === $status
                                                                            )
                                                                                ? 'selected'
                                                                                : ''
                                                                            ?>>
                                        <?= e($status) ?>
                                    </option>

                                    <?php endforeach; ?>

                                </select>

                            </div>


                            <button type="submit" class="btn btn-blue">
                                Update Order Status
                            </button>

                        </form>

                    </div>

                </div>


                <!-- PAYMENT -->

                <div class="card">

                    <div class="card-title">
                        Payment Status
                    </div>

                    <div class="card-body">

                        <form method="POST">

                            <input type="hidden" name="action" value="update_payment">


                            <div class="form-group">

                                <label>
                                    Current Payment Status
                                </label>


                                <select name="payment_status">

                                    <?php

                                    $payments = [
                                        'Pending',
                                        'Paid',
                                        'Failed',
                                        'Refunded'
                                    ];

                                    ?>

                                    <?php foreach (
                                        $payments
                                        as $payment
                                    ): ?>

                                    <option value="<?= e($payment) ?>" <?= (
                                                                                $order['payment_status']
                                                                                === $payment
                                                                            )
                                                                                ? 'selected'
                                                                                : ''
                                                                            ?>>
                                        <?= e($payment) ?>
                                    </option>

                                    <?php endforeach; ?>

                                </select>

                            </div>


                            <button type="submit" class="btn btn-blue">
                                Update Payment Status
                            </button>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>

</body>

</html>