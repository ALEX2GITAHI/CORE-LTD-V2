<?php

session_start();

require_once __DIR__ . '/includes/db.php';

$orderNumber = trim($_GET['order'] ?? '');

if ($orderNumber === '') {
    header("Location: index.php");
    exit;
}



/*
|--------------------------------------------------------------------------
| WHATSAPP SHOP NUMBER
|--------------------------------------------------------------------------
|
| The selected shop number is permanently tied to this order.
| We do NOT call whatsapp.php again when the page is refreshed.
|
*/
$whatsappNumber = '';

/*
|--------------------------------------------------------------------------
| GET ORDER
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
        subtotal,
        delivery_fee,
        total_amount,
        payment_status,
        order_status,
        whatsapp_number,
        created_at
    FROM orders
    WHERE order_number = ?
    LIMIT 1
");

$stmt->bind_param("s", $orderNumber);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();

    header("Location: index.php");
    exit;
}

$order = $result->fetch_assoc();

$stmt->close();


/*
|--------------------------------------------------------------------------
| GET / PRESERVE THE WHATSAPP TELLER NUMBER
|--------------------------------------------------------------------------
*/

$whatsappNumber = trim(
    (string)($order['whatsapp_number'] ?? '')
);

/*
|--------------------------------------------------------------------------
| ONLY SELECT A NEW SHOP NUMBER IF THIS ORDER HAS NONE
|--------------------------------------------------------------------------
*/

if ($whatsappNumber === '') {

    $whatsappFile = __DIR__ . '/whatsapp.php';

    if (file_exists($whatsappFile)) {

        ob_start();

        include $whatsappFile;

        $whatsappNumber = trim(
            ob_get_clean()
        );
    }

    /*
    |--------------------------------------------------------------------------
    | SAVE THE EXACT NUMBER SELECTED FOR THIS ORDER
    |--------------------------------------------------------------------------
    */

    if ($whatsappNumber !== '') {

        $saveWhatsappStmt = $conn->prepare("
            UPDATE orders
            SET whatsapp_number = ?
            WHERE id = ?
            LIMIT 1
        ");

        if ($saveWhatsappStmt) {

            $saveWhatsappStmt->bind_param(
                "si",
                $whatsappNumber,
                $order['id']
            );

            $saveWhatsappStmt->execute();
            $saveWhatsappStmt->close();

            $order['whatsapp_number'] = $whatsappNumber;
        }
    }
}


/*
|--------------------------------------------------------------------------
| GET ORDER ITEMS
|--------------------------------------------------------------------------
*/

$itemStmt = $conn->prepare("
    SELECT
        product_name,
        quantity,
        unit_price,
        subtotal
    FROM order_items
    WHERE order_id = ?
    ORDER BY id ASC
");

$itemStmt->bind_param("i", $order['id']);

$itemStmt->execute();

$itemResult = $itemStmt->get_result();

$orderItems = [];

while ($item = $itemResult->fetch_assoc()) {
    $orderItems[] = $item;
}

$itemStmt->close();


include 'header.php';

?>

<style>
.order-success-wrapper {
    padding: 70px 20px;
    background: #f8f9fa;
}

.order-success-box {
    max-width: 850px;
    margin: 0 auto;
    background: #ffffff;
    padding: 45px;
    border-radius: 10px;
    box-shadow: 0 5px 25px rgba(0, 0, 0, 0.08);
    text-align: center;
}

.success-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 20px;
    background: #28a745;
    color: #fff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 40px;
}

.order-success-box h1 {
    font-size: 32px;
    margin-bottom: 10px;
    color: #222;
}

.order-number {
    display: inline-block;
    background: #f1f1f1;
    padding: 12px 25px;
    border-radius: 5px;
    font-size: 18px;
    font-weight: 700;
    margin: 20px 0;
}

.order-table {
    width: 100%;
    margin-top: 30px;
    border-collapse: collapse;
    text-align: left;
}

.order-table th,
.order-table td {
    padding: 12px;
    border-bottom: 1px solid #ddd;
}

.order-total-row td {
    font-size: 20px;
    font-weight: bold;
}

.order-actions {
    margin-top: 35px;
}

.order-actions a {
    display: inline-block;
    padding: 13px 25px;
    margin: 5px;
    text-decoration: none;
    border-radius: 4px;
}

.btn-home {
    background: #222;
    color: #fff;
}

.btn-whatsapp {
    background: #25D366;
    color: #fff;
}
</style>


<div class="order-success-wrapper">

    <div class="order-success-box">

        <div class="success-icon">
            <i class="fa fa-check"></i>
        </div>

        <h1>Order Successfully Placed!</h1>

        <p>
            Thank you for shopping with <strong>CORE LTD</strong>.
        </p>

        <p>
            Your order has been received and is currently
            <strong>Pending</strong>.
        </p>


        <div class="order-number">

            Order Number:
            <?= htmlspecialchars($order['order_number']) ?>

        </div>


        <div style="margin-top:20px;">

            <strong>
                Customer:
            </strong>

            <?= htmlspecialchars($order['customer_name']) ?>

            <br>

            <strong>
                Phone:
            </strong>

            <?= htmlspecialchars($order['customer_phone']) ?>

        </div>


        <table class="order-table">

            <thead>

                <tr>

                    <th>
                        Product
                    </th>

                    <th>
                        Qty
                    </th>

                    <th>
                        Price
                    </th>

                    <th>
                        Total
                    </th>

                </tr>

            </thead>


            <tbody>

                <?php foreach ($orderItems as $item): ?>

                <tr>

                    <td>
                        <?= htmlspecialchars($item['product_name']) ?>
                    </td>

                    <td>
                        <?= (int)$item['quantity'] ?>
                    </td>

                    <td>
                        Ksh.
                        <?= number_format($item['unit_price'], 0) ?>
                    </td>

                    <td>
                        Ksh.
                        <?= number_format($item['subtotal'], 0) ?>
                    </td>

                </tr>

                <?php endforeach; ?>

            </tbody>


            <tfoot>

                <tr>

                    <td colspan="3">
                        <strong>Subtotal</strong>
                    </td>

                    <td>
                        <strong>
                            Ksh.
                            <?= number_format($order['subtotal'], 0) ?>
                        </strong>
                    </td>

                </tr>


                <tr>

                    <td colspan="3">
                        <strong>Delivery</strong>
                    </td>

                    <td>
                        <strong>
                            Ksh.
                            <?= number_format($order['delivery_fee'], 0) ?>
                        </strong>
                    </td>

                </tr>


                <tr class="order-total-row">

                    <td colspan="3">
                        GRAND TOTAL
                    </td>

                    <td>
                        Ksh.
                        <?= number_format($order['total_amount'], 0) ?>
                    </td>

                </tr>

            </tfoot>

        </table>
        <?php if (!empty($whatsappNumber)): ?>

        <a href="#" id="whatsapp-confirm-btn" class="whatsapp-confirm-btn" target="_blank" rel="noopener noreferrer">
            🟢 Confirm Order on WhatsApp
        </a>

        <?php endif; ?>

        <div class="order-actions">

            <a href="index.php" class="btn-home">
                Continue Shopping
            </a>

        </div>

    </div>

</div>
<style>
.whatsapp-confirm-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;

    margin-top: 20px;

    padding: 14px 24px;

    background: #25D366;

    color: #ffffff;

    text-decoration: none;

    border-radius: 8px;

    font-size: 16px;

    font-weight: 700;

    transition: 0.2s ease;
}

.whatsapp-confirm-btn:hover {
    background: #1ebe5d;

    color: #ffffff;

    transform: translateY(-1px);
}
</style>


<script>
document.addEventListener(
    "DOMContentLoaded",
    function() {

        /*
        |--------------------------------------------------------------------------
        | CLEAR BROWSER CART COPY
        |--------------------------------------------------------------------------
        */

        localStorage.removeItem("cart");


        /*
        |--------------------------------------------------------------------------
        | RESET HEADER DISPLAY
        |--------------------------------------------------------------------------
        */

        $(".cart-item-count").text("0");

        $("#cart-total").text("0");

        $("#cart-subtotal").text("0");


        /*
        |--------------------------------------------------------------------------
        | EMPTY MINI CART
        |--------------------------------------------------------------------------
        */

        $("#cart-items").html(
            '<li><p>Your cart is empty.</p></li>'
        );

    }
);
</script>

<script>
document.addEventListener("DOMContentLoaded", function() {

    const whatsappButton =
        document.getElementById("whatsapp-confirm-btn");

    if (!whatsappButton) {
        return;
    }

    /*
    |--------------------------------------------------------------------------
    | WHATSAPP NUMBER
    |--------------------------------------------------------------------------
    */

    const whatsappNumber =
        <?= json_encode($whatsappNumber) ?>;


    /*
    |--------------------------------------------------------------------------
    | ORDER DETAILS FROM DATABASE
    |--------------------------------------------------------------------------
    */

    const orderNumber =
        <?= json_encode($order['order_number']) ?>;

    const customerName =
        <?= json_encode($order['customer_name']) ?>;

    const customerPhone =
        <?= json_encode($order['customer_phone']) ?>;

    const grandTotal =
        <?= json_encode((float)$order['total_amount']) ?>;


    /*
    |--------------------------------------------------------------------------
    | BUILD ORDER ITEMS
    |--------------------------------------------------------------------------
    */

    let itemsMessage = "";

    <?php if (!empty($orderItems)): ?>

    <?php foreach ($orderItems as $index => $item): ?>

    itemsMessage +=
        "<?= ($index + 1) ?>. " +
        <?= json_encode($item['product_name']) ?> +
        "\n" +

        "   Quantity: " +
        <?= (int)$item['quantity'] ?> +
        "\n" +

        "   Price: Ksh. " +
        <?= json_encode(number_format($item['unit_price'], 2)) ?> +
        "\n" +

        "   Total: Ksh. " +
        <?= json_encode(number_format($item['subtotal'], 2)) ?> +
        "\n\n";

    <?php endforeach; ?>

    <?php endif; ?>


    /*
    |--------------------------------------------------------------------------
    | BUILD COMPLETE WHATSAPP MESSAGE
    |--------------------------------------------------------------------------
    */

    const message =

        "Hello CORE LTD,\n\n" +

        "I have placed an order through your website.\n\n" +

        "*Order No:* " +
        orderNumber +
        "\n" +

        "*Name:* " +
        customerName +
        "\n" +

        "*Phone:* " +
        customerPhone +
        "\n\n" +

        "*Items Purchased:*\n\n" +

        itemsMessage +

        "*Grand Total:* Ksh. " +
        Number(grandTotal).toLocaleString(
            "en-KE", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }
        ) +

        "\n\n" +

        "I would like to confirm my order and receive payment instructions.\n\n" +

        "Thank you.";


    /*
    |--------------------------------------------------------------------------
    | CREATE WHATSAPP LINK
    |--------------------------------------------------------------------------
    */

    whatsappButton.href =
        "https://wa.me/" +
        whatsappNumber +
        "?text=" +
        encodeURIComponent(message);

});
</script>

<?php

include 'footer.php';

?>