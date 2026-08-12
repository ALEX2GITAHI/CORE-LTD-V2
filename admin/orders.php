<?php
session_start();

require_once __DIR__ . '/../includes/db.php';

/*
|--------------------------------------------------------------------------
| ADMIN ACCESS
|--------------------------------------------------------------------------
*/
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| FILTERS
|--------------------------------------------------------------------------
*/
$search = trim($_GET['search'] ?? '');
$orderStatus = trim($_GET['order_status'] ?? '');
$paymentStatus = trim($_GET['payment_status'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 15;
$offset = ($page - 1) * $perPage;

/*
|--------------------------------------------------------------------------
| DASHBOARD COUNTS
|--------------------------------------------------------------------------
*/
function getCount(mysqli $conn, string $sql): int
{
    $result = $conn->query($sql);

    if (!$result) {
        return 0;
    }

    $row = $result->fetch_assoc();

    return (int)($row['total'] ?? 0);
}

$totalOrders = getCount(
    $conn,
    "SELECT COUNT(*) AS total FROM orders"
);

$pendingOrders = getCount(
    $conn,
    "SELECT COUNT(*) AS total
     FROM orders
     WHERE order_status IN ('Pending','New')"
);

$processingOrders = getCount(
    $conn,
    "SELECT COUNT(*) AS total
     FROM orders
     WHERE order_status IN ('Processing','Ready')"
);

$completedOrders = getCount(
    $conn,
    "SELECT COUNT(*) AS total
     FROM orders
     WHERE order_status = 'Completed'"
);

$todayOrders = getCount(
    $conn,
    "SELECT COUNT(*) AS total
     FROM orders
     WHERE DATE(created_at) = CURDATE()"
);

/*
|--------------------------------------------------------------------------
| BUILD WHERE CLAUSE
|--------------------------------------------------------------------------
*/
$where = [];
$params = [];
$types = '';

if ($search !== '') {
    $where[] = "(
        o.order_number LIKE ?
        OR o.customer_name LIKE ?
        OR o.customer_phone LIKE ?
        OR o.customer_email LIKE ?
    )";

    $searchValue = '%' . $search . '%';

    $params[] = $searchValue;
    $params[] = $searchValue;
    $params[] = $searchValue;
    $params[] = $searchValue;

    $types .= 'ssss';
}

if ($orderStatus !== '') {
    $where[] = "o.order_status = ?";
    $params[] = $orderStatus;
    $types .= 's';
}

if ($paymentStatus !== '') {
    $where[] = "o.payment_status = ?";
    $params[] = $paymentStatus;
    $types .= 's';
}

$whereSql = '';

if (!empty($where)) {
    $whereSql = 'WHERE ' . implode(' AND ', $where);
}

/*
|--------------------------------------------------------------------------
| TOTAL FILTERED ORDERS
|--------------------------------------------------------------------------
*/
$countSql = "
    SELECT COUNT(*) AS total
    FROM orders o
    $whereSql
";

$countStmt = $conn->prepare($countSql);

if (!$countStmt) {
    die("Unable to load orders.");
}

if (!empty($params)) {
    $countStmt->bind_param($types, ...$params);
}

$countStmt->execute();

$countResult = $countStmt->get_result();
$countRow = $countResult->fetch_assoc();

$filteredOrders = (int)($countRow['total'] ?? 0);

$countStmt->close();

$totalPages = max(
    1,
    (int)ceil($filteredOrders / $perPage)
);

/*
|--------------------------------------------------------------------------
| LOAD ORDERS
|--------------------------------------------------------------------------
*/
$sql = "
    SELECT
        o.id,
        o.order_number,
        o.customer_name,
        o.customer_phone,
        o.customer_email,
        o.delivery_address,
        o.whatsapp_number,
        o.subtotal,
        o.delivery_fee,
        o.total_amount,
        o.payment_method,
        o.payment_status,
        o.order_status,
        o.created_at,

        (
            SELECT COUNT(*)
            FROM order_items oi
            WHERE oi.order_id = o.id
        ) AS item_count

    FROM orders o

    $whereSql

    ORDER BY o.created_at DESC

    LIMIT ? OFFSET ?
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Unable to load orders: " . htmlspecialchars($conn->error));
}

$queryParams = $params;
$queryTypes = $types . 'ii';

$queryParams[] = $perPage;
$queryParams[] = $offset;

$stmt->bind_param(
    $queryTypes,
    ...$queryParams
);

$stmt->execute();

$result = $stmt->get_result();

$orders = [];

while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}

$stmt->close();

/*
|--------------------------------------------------------------------------
| HELPERS
|--------------------------------------------------------------------------
*/
function e($value): string
{
    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES,
        'UTF-8'
    );
}

function money($amount): string
{
    return 'KSh ' . number_format(
        (float)$amount,
        2
    );
}

function statusClass(string $status): string
{
    $value = strtolower(trim($status));

    return match (true) {
        in_array($value, ['completed', 'paid'], true) => 'success',
        in_array($value, ['processing', 'ready'], true) => 'info',
        in_array($value, ['cancelled', 'failed'], true) => 'danger',
        in_array($value, ['pending', 'new'], true) => 'warning',
        default => 'neutral',
    };
}

function queryString(array $changes = []): string
{
    $query = $_GET;

    foreach ($changes as $key => $value) {
        if ($value === null || $value === '') {
            unset($query[$key]);
        } else {
            $query[$key] = $value;
        }
    }

    return '?' . http_build_query($query);
}
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
        Orders | CORE LTD Admin
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
            color: inherit;
            text-decoration: none;
        }

        .page {
            min-height: 100vh;
            padding: 28px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            margin-bottom: 24px;
        }

        .title-area h1 {
            margin: 0;
            font-size: 25px;
            color: #183b62;
        }

        .title-area p {
            margin: 6px 0 0;
            color: #8294a8;
            font-size: 12px;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 15px;
            border-radius: 8px;
            background: #fff;
            border: 1px solid #dce7f1;
            color: #0875df;
            font-size: 11px;
            font-weight: 700;
        }

        .back-btn:hover {
            background: #eef7ff;
        }

        /*
        ----------------------------------------------------------
        STAT CARDS
        ----------------------------------------------------------
        */

        .stats {
            display: grid;
            grid-template-columns:
                repeat(5, minmax(0, 1fr));

            gap: 14px;
            margin-bottom: 20px;
        }

        .stat {
            background: #fff;
            border: 1px solid #e1eaf2;
            border-radius: 12px;
            padding: 18px;
            box-shadow:
                0 5px 18px
                rgba(29, 78, 121, .05);
        }

        .stat-label {
            color: #8a9bad;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .4px;
        }

        .stat-value {
            margin-top: 8px;
            font-size: 25px;
            font-weight: 800;
            color: #183b62;
        }

        .stat-blue {
            border-top: 3px solid #0875df;
        }

        .stat-orange {
            border-top: 3px solid #f59e0b;
        }

        .stat-purple {
            border-top: 3px solid #7c3aed;
        }

        .stat-green {
            border-top: 3px solid #16a34a;
        }

        .stat-dark {
            border-top: 3px solid #405b75;
        }

        /*
        ----------------------------------------------------------
        FILTER BAR
        ----------------------------------------------------------
        */

        .filter-card {
            background: #fff;
            border: 1px solid #e1eaf2;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 18px;
        }

        .filters {
            display: grid;
            grid-template-columns:
                minmax(240px, 1fr)
                180px
                180px
                auto
                auto;

            gap: 10px;
            align-items: end;
        }

        .field label {
            display: block;
            margin-bottom: 6px;
            color: #71849a;
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .field input,
        .field select {
            width: 100%;
            height: 38px;
            border:
                1px solid #d9e4ee;

            border-radius: 7px;
            padding: 0 11px;

            background: #fbfdff;
            color: #405b75;

            font-size: 11px;
            outline: none;
        }

        .field input:focus,
        .field select:focus {
            border-color: #0875df;
            box-shadow:
                0 0 0 3px
                rgba(8,117,223,.08);
        }

        .btn {
            height: 38px;
            padding: 0 16px;
            border: 0;
            border-radius: 7px;
            font-size: 10px;
            font-weight: 800;
            cursor: pointer;
        }

        .btn-primary {
            background: #0875df;
            color: #fff;
        }

        .btn-primary:hover {
            background: #0567c5;
        }

        .btn-light {
            background: #eef5fb;
            color: #587089;
        }

        /*
        ----------------------------------------------------------
        TABLE
        ----------------------------------------------------------
        */

        .table-card {
            background: #fff;
            border: 1px solid #e1eaf2;
            border-radius: 12px;
            overflow: hidden;
            box-shadow:
                0 5px 18px
                rgba(29, 78, 121, .05);
        }

        .table-header {
            padding: 17px 20px;
            border-bottom: 1px solid #e7eef5;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .table-header h2 {
            margin: 0;
            color: #183b62;
            font-size: 14px;
        }

        .table-header span {
            color: #91a0af;
            font-size: 10px;
        }

        .table-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            min-width: 1100px;
            border-collapse: collapse;
        }

        th {
            padding: 12px 14px;
            text-align: left;
            background: #f7fafd;
            color: #71849a;
            border-bottom: 1px solid #e4edf5;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: .35px;
        }

        td {
            padding: 14px;
            border-bottom: 1px solid #edf2f6;
            font-size: 11px;
            vertical-align: middle;
        }

        tr:last-child td {
            border-bottom: 0;
        }

        tbody tr:hover {
            background: #fbfdff;
        }

        .order-number {
            color: #0875df;
            font-weight: 800;
        }

        .customer-name {
            color: #314e6b;
            font-weight: 750;
        }

        .customer-phone {
            color: #91a0af;
            font-size: 9px;
            margin-top: 3px;
        }

        .total {
            color: #183b62;
            font-weight: 800;
            white-space: nowrap;
        }

        .items {
            color: #71849a;
            font-weight: 700;
        }

        /*
        ----------------------------------------------------------
        STATUS BADGES
        ----------------------------------------------------------
        */

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 5px 8px;
            border-radius: 20px;
            font-size: 8px;
            font-weight: 800;
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

        .whatsapp-number {
            color: #15803d;
            font-size: 10px;
            font-weight: 800;
            white-space: nowrap;
        }

        .whatsapp-label {
            display: block;
            margin-bottom: 3px;
            color: #91a0af;
            font-size: 8px;
            text-transform: uppercase;
        }

        .date {
            color: #71849a;
            white-space: nowrap;
            font-size: 10px;
        }

        /*
        ----------------------------------------------------------
        VIEW BUTTON
        ----------------------------------------------------------
        */

        .view-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 11px;
            border-radius: 7px;
            background: #eef7ff;
            color: #0875df;
            font-size: 9px;
            font-weight: 800;
        }

        .view-btn:hover {
            background: #0875df;
            color: #fff;
        }

        /*
        ----------------------------------------------------------
        EMPTY STATE
        ----------------------------------------------------------
        */

        .empty {
            padding: 60px 20px;
            text-align: center;
        }

        .empty-icon {
            width: 54px;
            height: 54px;
            margin: 0 auto 12px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #eef7ff;
            color: #0875df;
            font-size: 22px;
        }

        .empty h3 {
            margin: 0;
            color: #526b84;
            font-size: 14px;
        }

        .empty p {
            margin: 6px 0 0;
            color: #9aa9b8;
            font-size: 10px;
        }

        /*
        ----------------------------------------------------------
        PAGINATION
        ----------------------------------------------------------
        */

        .pagination {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 5px;
            padding: 15px 18px;
            border-top: 1px solid #e7eef5;
        }

        .page-link {
            min-width: 30px;
            height: 30px;
            padding: 0 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            color: #71849a;
            background: #f5f8fb;
            font-size: 9px;
            font-weight: 800;
        }

        .page-link.active {
            color: #fff;
            background: #0875df;
        }

        .page-link:hover:not(.active) {
            background: #eaf4ff;
            color: #0875df;
        }

        /*
        ----------------------------------------------------------
        RESPONSIVE
        ----------------------------------------------------------
        */

        @media (max-width: 1100px) {

            .stats {
                grid-template-columns:
                    repeat(3, 1fr);
            }

            .filters {
                grid-template-columns:
                    1fr 1fr;
            }

        }

        @media (max-width: 700px) {

            .page {
                padding: 15px;
            }

            .topbar {
                align-items: flex-start;
                flex-direction: column;
            }

            .stats {
                grid-template-columns:
                    repeat(2, 1fr);
            }

            .filters {
                grid-template-columns: 1fr;
            }

        }

    </style>

</head>

<body>

<div class="page">

    <!-- HEADER -->

    <div class="topbar">

        <div class="title-area">

            <h1>
                Orders
            </h1>

            <p>
                Manage customer orders and WhatsApp orders
            </p>

        </div>

        <a
            href="index.php"
            class="back-btn"
        >
            ← Dashboard
        </a>

    </div>


    <!-- STATISTICS -->

    <div class="stats">

        <div class="stat stat-blue">

            <div class="stat-label">
                Total Orders
            </div>

            <div class="stat-value">
                <?= number_format($totalOrders) ?>
            </div>

        </div>


        <div class="stat stat-dark">

            <div class="stat-label">
                Today's Orders
            </div>

            <div class="stat-value">
                <?= number_format($todayOrders) ?>
            </div>

        </div>


        <div class="stat stat-orange">

            <div class="stat-label">
                Pending
            </div>

            <div class="stat-value">
                <?= number_format($pendingOrders) ?>
            </div>

        </div>


        <div class="stat stat-purple">

            <div class="stat-label">
                Processing
            </div>

            <div class="stat-value">
                <?= number_format($processingOrders) ?>
            </div>

        </div>


        <div class="stat stat-green">

            <div class="stat-label">
                Completed
            </div>

            <div class="stat-value">
                <?= number_format($completedOrders) ?>
            </div>

        </div>

    </div>


    <!-- FILTERS -->

    <div class="filter-card">

        <form
            method="GET"
            class="filters"
        >

            <div class="field">

                <label>
                    Search Orders
                </label>

                <input
                    type="text"
                    name="search"
                    value="<?= e($search) ?>"
                    placeholder="Order number, customer, phone..."
                >

            </div>


            <div class="field">

                <label>
                    Order Status
                </label>

                <select name="order_status">

                    <option value="">
                        All statuses
                    </option>

                    <?php
                    $orderStatuses = [
                        'Pending',
                        'New',
                        'Paid',
                        'Processing',
                        'Ready',
                        'Completed',
                        'Cancelled'
                    ];
                    ?>

                    <?php foreach ($orderStatuses as $status): ?>

                        <option
                            value="<?= e($status) ?>"
                            <?= $orderStatus === $status ? 'selected' : '' ?>
                        >
                            <?= e($status) ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <div class="field">

                <label>
                    Payment Status
                </label>

                <select name="payment_status">

                    <option value="">
                        All payments
                    </option>

                    <?php
                    $paymentStatuses = [
                        'Pending',
                        'Paid',
                        'Failed',
                        'Refunded'
                    ];
                    ?>

                    <?php foreach ($paymentStatuses as $status): ?>

                        <option
                            value="<?= e($status) ?>"
                            <?= $paymentStatus === $status ? 'selected' : '' ?>
                        >
                            <?= e($status) ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <button
                type="submit"
                class="btn btn-primary"
            >
                Search
            </button>


            <a
                href="orders.php"
                class="btn btn-light"
                style="
                    display:flex;
                    align-items:center;
                    justify-content:center;
                "
            >
                Clear
            </a>

        </form>

    </div>


    <!-- ORDERS TABLE -->

    <div class="table-card">

        <div class="table-header">

            <h2>
                Customer Orders
            </h2>

            <span>
                <?= number_format($filteredOrders) ?>
                order<?= $filteredOrders === 1 ? '' : 's' ?>
            </span>

        </div>


        <?php if (empty($orders)): ?>

            <div class="empty">

                <div class="empty-icon">
                    🛒
                </div>

                <h3>
                    No orders found
                </h3>

                <p>
                    Orders placed by customers will appear here.
                </p>

            </div>

        <?php else: ?>

            <div class="table-wrap">

                <table>

                    <thead>

                    <tr>

                        <th>
                            Order
                        </th>

                        <th>
                            Customer
                        </th>

                        <th>
                            Items
                        </th>

                        <th>
                            Total
                        </th>

                        <th>
                            WhatsApp Shop
                        </th>

                        <th>
                            Payment
                        </th>

                        <th>
                            Order Status
                        </th>

                        <th>
                            Date
                        </th>

                        <th>
                            Action
                        </th>

                    </tr>

                    </thead>


                    <tbody>

                    <?php foreach ($orders as $order): ?>

                        <tr>

                            <td>

                                <div class="order-number">
                                    #<?= e($order['order_number']) ?>
                                </div>

                            </td>


                            <td>

                                <div class="customer-name">
                                    <?= e($order['customer_name']) ?>
                                </div>

                                <div class="customer-phone">
                                    <?= e($order['customer_phone']) ?>
                                </div>

                            </td>


                            <td>

                                <span class="items">
                                    <?= number_format((int)$order['item_count']) ?>
                                    item<?= (int)$order['item_count'] === 1 ? '' : 's' ?>
                                </span>

                            </td>


                            <td>

                                <span class="total">
                                    <?= money($order['total_amount']) ?>
                                </span>

                            </td>


                            <td>

                                <?php if (!empty($order['whatsapp_number'])): ?>

                                    <span class="whatsapp-label">
                                        Received by
                                    </span>

                                    <span class="whatsapp-number">
                                        +<?= e(ltrim($order['whatsapp_number'], '+')) ?>
                                    </span>

                                <?php else: ?>

                                    <span class="badge neutral">
                                        Not recorded
                                    </span>

                                <?php endif; ?>

                            </td>


                            <td>

                                <span class="badge <?= statusClass($order['payment_status']) ?>">
                                    <?= e($order['payment_status']) ?>
                                </span>

                            </td>


                            <td>

                                <span class="badge <?= statusClass($order['order_status']) ?>">
                                    <?= e($order['order_status']) ?>
                                </span>

                            </td>


                            <td>

                                <span class="date">

                                    <?= e(
                                        date(
                                            'd M Y',
                                            strtotime($order['created_at'])
                                        )
                                    ) ?>

                                    <br>

                                    <?= e(
                                        date(
                                            'H:i',
                                            strtotime($order['created_at'])
                                        )
                                    ) ?>

                                </span>

                            </td>


                            <td>

                                <a
                                    href="order-view.php?id=<?= (int)$order['id'] ?>"
                                    class="view-btn"
                                >
                                    View Order
                                </a>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            </div>


            <?php if ($totalPages > 1): ?>

                <div class="pagination">

                    <?php if ($page > 1): ?>

                        <a
                            class="page-link"
                            href="<?= e(
                                queryString([
                                    'page' => $page - 1
                                ])
                            ) ?>"
                        >
                            ‹
                        </a>

                    <?php endif; ?>


                    <?php

                    $startPage = max(
                        1,
                        $page - 2
                    );

                    $endPage = min(
                        $totalPages,
                        $page + 2
                    );

                    ?>

                    <?php for ($i = $startPage; $i <= $endPage; $i++): ?>

                        <a
                            class="page-link <?= $i === $page ? 'active' : '' ?>"
                            href="<?= e(
                                queryString([
                                    'page' => $i
                                ])
                            ) ?>"
                        >
                            <?= $i ?>
                        </a>

                    <?php endfor; ?>


                    <?php if ($page < $totalPages): ?>

                        <a
                            class="page-link"
                            href="<?= e(
                                queryString([
                                    'page' => $page + 1
                                ])
                            ) ?>"
                        >
                            ›
                        </a>

                    <?php endif; ?>

                </div>

            <?php endif; ?>

        <?php endif; ?>

    </div>

</div>

</body>
</html>
