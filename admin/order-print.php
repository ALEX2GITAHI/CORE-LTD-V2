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

function statusClass(string $value): string
{
    $value = strtolower(trim($value));

    if (in_array($value, ['paid', 'completed'], true)) {
        return 'green';
    }

    if (in_array($value, ['processing', 'ready'], true)) {
        return 'blue';
    }

    if (in_array($value, ['cancelled', 'failed', 'refunded'], true)) {
        return 'red';
    }

    return 'orange';
}

$orderId = (int)($_GET['id'] ?? 0);

if ($orderId <= 0) {
    header("Location: orders.php");
    exit;
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
    die("Unable to prepare order query: " . e($conn->error));
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
| Actual order_items structure:
| id, order_id, product_id, product_name,
| product_reference, product_image, quantity,
| unit_price, subtotal, created_at
|--------------------------------------------------------------------------
*/
$itemStmt = $conn->prepare("
    SELECT
        id,
        product_id,
        product_name,
        product_reference,
        product_image,
        quantity,
        unit_price,
        subtotal
    FROM order_items
    WHERE order_id = ?
    ORDER BY id ASC
");

if (!$itemStmt) {
    die("Unable to prepare order items query: " . e($conn->error));
}

$itemStmt->bind_param("i", $orderId);
$itemStmt->execute();

$itemResult = $itemStmt->get_result();

$items = [];

while ($row = $itemResult->fetch_assoc()) {
    $items[] = $row;
}

$itemStmt->close();

$whatsappNumber = trim((string)($order['whatsapp_number'] ?? ''));

$printedDate = date(
    'd M Y, H:i',
    strtotime($order['created_at'])
);

?>
<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>
    Order <?= e($order['order_number']) ?> | CORE LTD
</title>

<style>

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    background: #eef3f8;
    color: #26384a;
    font-family:
        Arial,
        Helvetica,
        sans-serif;
    font-size: 12px;
}

.page {
    width: 900px;
    max-width: calc(100% - 30px);
    margin: 25px auto;
}

.toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.toolbar-left {
    display: flex;
    gap: 8px;
}

.toolbar a,
.toolbar button {
    border: 0;
    border-radius: 6px;
    padding: 9px 13px;
    background: #0b74d1;
    color: #fff;
    text-decoration: none;
    cursor: pointer;
    font-size: 11px;
    font-weight: 700;
}

.toolbar .back {
    background: #fff;
    color: #0b74d1;
    border: 1px solid #d8e4ef;
}

.invoice {
    background: #fff;
    border: 1px solid #dbe5ee;
    box-shadow: 0 5px 22px rgba(23, 61, 95, .08);
    padding: 38px;
}

.header {
    display: flex;
    justify-content: space-between;
    gap: 25px;
    padding-bottom: 25px;
    border-bottom: 3px solid #0875df;
}

.company-name {
    color: #123b64;
    font-size: 25px;
    font-weight: 900;
    letter-spacing: .5px;
}

.company-tagline {
    margin-top: 5px;
    color: #73879b;
    font-size: 10px;
}

.company-details {
    margin-top: 13px;
    color: #536b81;
    font-size: 10px;
    line-height: 1.7;
}

.invoice-title {
    text-align: right;
}

.invoice-title h1 {
    margin: 0;
    color: #0875df;
    font-size: 25px;
    letter-spacing: 1px;
}

.invoice-number {
    margin-top: 7px;
    color: #536b81;
    font-size: 10px;
    font-weight: 700;
}

.info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 25px;
    margin: 25px 0;
}

.info-box {
    border: 1px solid #e2eaf1;
    border-radius: 7px;
    padding: 15px;
}

.info-title {
    margin-bottom: 9px;
    color: #0875df;
    font-size: 9px;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: .5px;
}

.info-value {
    color: #344e66;
    font-size: 10px;
    line-height: 1.7;
}

.status-row {
    display: flex;
    gap: 7px;
    margin-top: 8px;
}

.status {
    display: inline-block;
    padding: 5px 9px;
    border-radius: 15px;
    font-size: 8px;
    font-weight: 900;
}

.status.green {
    background: #e8f8ee;
    color: #16803c;
}

.status.blue {
    background: #eaf4ff;
    color: #0875df;
}

.status.orange {
    background: #fff4dc;
    color: #a65d00;
}

.status.red {
    background: #ffeded;
    color: #c92a2a;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}

thead th {
    padding: 11px 9px;
    background: #123b64;
    color: #fff;
    font-size: 9px;
    text-align: left;
    text-transform: uppercase;
}

thead th.right,
tbody td.right {
    text-align: right;
}

tbody td {
    padding: 12px 9px;
    border-bottom: 1px solid #e4ebf1;
    color: #3e566c;
    font-size: 10px;
    vertical-align: top;
}

.product-name {
    color: #203f5d;
    font-weight: 800;
}

.reference {
    margin-top: 3px;
    color: #8495a5;
    font-size: 8px;
}

.totals {
    width: 310px;
    margin-left: auto;
    margin-top: 20px;
}

.total-line {
    display: flex;
    justify-content: space-between;
    padding: 7px 0;
    color: #63788c;
    font-size: 10px;
}

.total-line.final {
    margin-top: 5px;
    padding-top: 12px;
    border-top: 2px solid #123b64;
    color: #123b64;
    font-size: 15px;
    font-weight: 900;
}

.notes {
    margin-top: 25px;
    padding: 13px 15px;
    background: #f5f9fc;
    border-left: 4px solid #0875df;
    color: #566e84;
    font-size: 10px;
    line-height: 1.6;
}

.footer {
    margin-top: 35px;
    padding-top: 18px;
    border-top: 1px solid #e1e8ef;
    text-align: center;
    color: #8798a8;
    font-size: 9px;
    line-height: 1.7;
}

@media (max-width: 700px) {

    .page {
        width: auto;
        max-width: calc(100% - 16px);
        margin: 8px auto;
    }

    .invoice {
        padding: 20px;
    }

    .header,
    .info-grid {
        grid-template-columns: 1fr;
        display: grid;
    }

    .invoice-title {
        text-align: left;
    }

    .totals {
        width: 100%;
    }

    table {
        min-width: 650px;
    }

    .table-wrap {
        overflow-x: auto;
    }

}

@media print {

    @page {
        size: A4;
        margin: 12mm;
    }

    body {
        background: #fff;
    }

    .page {
        width: auto;
        max-width: none;
        margin: 0;
    }

    .toolbar {
        display: none !important;
    }

    .invoice {
        border: 0;
        box-shadow: none;
        padding: 0;
    }

    .header {
        border-bottom: 3px solid #0875df;
    }

}

</style>

</head>

<body>

<div class="page">

    <div class="toolbar">

        <div class="toolbar-left">

            <a
                href="order-view.php?id=<?= $orderId ?>"
                class="back"
            >
                ← Back to Order
            </a>

            <a href="orders.php" class="back">
                Orders
            </a>

        </div>

        <button
            type="button"
            onclick="window.print()"
        >
            🖨 Print Order
        </button>

    </div>


    <div class="invoice">

        <!-- COMPANY HEADER -->

        <div class="header">

            <div>

                <div class="company-name">
                    CORE LTD
                </div>

                <div class="company-tagline">
                    Networking • Internet • CCTV • Fiber • ICT Solutions
                </div>

                <div class="company-details">
                    Kenya<br>
                    Phone: CORE LTD Customer Service<br>
                    Email: info@coreltd.co.ke
                </div>

            </div>


            <div class="invoice-title">

                <h1>
                    ORDER
                </h1>

                <div class="invoice-number">
                    #<?= e($order['order_number']) ?>
                </div>

                <div class="invoice-number">
                    <?= e($printedDate) ?>
                </div>

            </div>

        </div>


        <!-- CUSTOMER INFORMATION -->

        <div class="info-grid">

            <div class="info-box">

                <div class="info-title">
                    Customer
                </div>

                <div class="info-value">

                    <strong>
                        <?= e($order['customer_name']) ?>
                    </strong>

                    <br>

                    Phone:
                    <?= e($order['customer_phone']) ?>

                    <?php if (!empty($order['customer_email'])): ?>

                        <br>

                        Email:
                        <?= e($order['customer_email']) ?>

                    <?php endif; ?>

                </div>

            </div>


            <div class="info-box">

                <div class="info-title">
                    Delivery / Order
                </div>

                <div class="info-value">

                    <strong>
                        Delivery Address
                    </strong>

                    <br>

                    <?= nl2br(
                        e($order['delivery_address'])
                    ) ?>

                    <br><br>

                    Payment:
                    <?= e(
                        $order['payment_method']
                        ?: 'WhatsApp'
                    ) ?>

                    <div class="status-row">

                        <span
                            class="status <?= e(
                                statusClass(
                                    $order['order_status']
                                )
                            ) ?>"
                        >
                            Order:
                            <?= e(
                                $order['order_status']
                            ) ?>
                        </span>

                        <span
                            class="status <?= e(
                                statusClass(
                                    $order['payment_status']
                                )
                            ) ?>"
                        >
                            Payment:
                            <?= e(
                                $order['payment_status']
                            ) ?>
                        </span>

                    </div>

                </div>

            </div>

        </div>


        <!-- PRODUCTS -->

        <div class="table-wrap">

            <table>

                <thead>

                    <tr>

                        <th style="width: 45px;">
                            #
                        </th>

                        <th>
                            Product
                        </th>

                        <th style="width: 90px;">
                            Reference
                        </th>

                        <th
                            style="width: 70px;"
                            class="right"
                        >
                            Qty
                        </th>

                        <th
                            style="width: 110px;"
                            class="right"
                        >
                            Unit Price
                        </th>

                        <th
                            style="width: 120px;"
                            class="right"
                        >
                            Subtotal
                        </th>

                    </tr>

                </thead>


                <tbody>

                    <?php if (empty($items)): ?>

                        <tr>

                            <td
                                colspan="6"
                                style="text-align:center;"
                            >
                                No products found for this order.
                            </td>

                        </tr>

                    <?php else: ?>

                        <?php foreach (
                            $items as $index => $item
                        ): ?>

                            <tr>

                                <td>
                                    <?= $index + 1 ?>
                                </td>


                                <td>

                                    <div class="product-name">

                                        <?= e(
                                            $item['product_name']
                                        ) ?>

                                    </div>

                                </td>


                                <td>

                                    <?php if (
                                        !empty(
                                            $item['product_reference']
                                        )
                                    ): ?>

                                        <div class="reference">

                                            <?= e(
                                                $item['product_reference']
                                            ) ?>

                                        </div>

                                    <?php else: ?>

                                        —

                                    <?php endif; ?>

                                </td>


                                <td class="right">

                                    <?= number_format(
                                        (int)$item['quantity']
                                    ) ?>

                                </td>


                                <td class="right">

                                    <?= money(
                                        $item['unit_price']
                                    ) ?>

                                </td>


                                <td class="right">

                                    <strong>

                                        <?= money(
                                            $item['subtotal']
                                        ) ?>

                                    </strong>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>


        <!-- TOTALS -->

        <div class="totals">

            <div class="total-line">

                <span>
                    Subtotal
                </span>

                <span>
                    <?= money(
                        $order['subtotal']
                    ) ?>
                </span>

            </div>


            <div class="total-line">

                <span>
                    Delivery Fee
                </span>

                <span>
                    <?= money(
                        $order['delivery_fee']
                    ) ?>
                </span>

            </div>


            <div class="total-line final">

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


        <?php if (
            !empty(
                $order['customer_notes']
            )
        ): ?>

            <div class="notes">

                <strong>
                    Customer Notes
                </strong>

                <br>

                <?= nl2br(
                    e(
                        $order['customer_notes']
                    )
                ) ?>

            </div>

        <?php endif; ?>


        <?php if ($whatsappNumber !== ''): ?>

            <div class="notes">

                <strong>
                    WhatsApp Order Received By
                </strong>

                <br>

                <?= e(
                    $whatsappNumber
                ) ?>

            </div>

        <?php endif; ?>


        <!-- FOOTER -->

        <div class="footer">

            <strong>
                CORE LTD
            </strong>

            <br>

            Thank you for choosing CORE LTD.

            <br>

            This document is an order record generated from the CORE LTD
            administration system.

        </div>

    </div>

</div>

</body>
</html>
