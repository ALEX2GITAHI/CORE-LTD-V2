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

function statusClass(string $value): string
{
    $v = strtolower(trim($value));

    if (in_array($v, ['paid', 'completed'], true)) return 'success';
    if (in_array($v, ['processing', 'ready'], true)) return 'info';
    if (in_array($v, ['cancelled', 'failed', 'refunded'], true)) return 'danger';
    return 'warning';
}

$orderId = (int)($_GET['id'] ?? 0);

if ($orderId <= 0) {
    header("Location: orders.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| Load order
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
    SELECT
        id,
        order_number,
        customer_name,
        order_status,
        payment_status,
        created_at
    FROM orders
    WHERE id = ?
    LIMIT 1
");

if (!$stmt) {
    die("Database error: " . e($conn->error));
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
| Activity table
|
| This page supports the following table:
|
| order_activity_logs
| -------------------
| id
| order_id
| admin_id
| action
| old_value
| new_value
| description
| created_at
|
| If the table does not exist yet, use the SQL supplied below this file.
|--------------------------------------------------------------------------
*/
$activities = [];

$tableCheck = $conn->query("
    SHOW TABLES LIKE 'order_activity_logs'
");

if ($tableCheck && $tableCheck->num_rows > 0) {

    $activityStmt = $conn->prepare("
        SELECT
            l.id,
            l.action,
            l.old_value,
            l.new_value,
            l.description,
            l.created_at,
            l.admin_id
        FROM order_activity_logs l
        WHERE l.order_id = ?
        ORDER BY l.id DESC
    ");

    if ($activityStmt) {

        $activityStmt->bind_param("i", $orderId);
        $activityStmt->execute();

        $activityResult = $activityStmt->get_result();

        while ($row = $activityResult->fetch_assoc()) {
            $activities[] = $row;
        }

        $activityStmt->close();
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>
    Order History #<?= e($order['order_number']) ?> | CORE LTD
</title>

<style>

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    background: #f4f8fc;
    color: #304b63;
    font-family: Arial, Helvetica, sans-serif;
}

.page {
    max-width: 900px;
    margin: 0 auto;
    padding: 28px;
}

.topbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
    margin-bottom: 20px;
}

h1 {
    margin: 0;
    color: #183b62;
    font-size: 23px;
}

.subtitle {
    margin-top: 5px;
    color: #8798a8;
    font-size: 10px;
}

.actions {
    display: flex;
    gap: 7px;
}

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 9px 13px;
    border-radius: 7px;
    background: #0875df;
    color: #fff;
    text-decoration: none;
    font-size: 9px;
    font-weight: 800;
}

.btn.secondary {
    background: #fff;
    color: #0875df;
    border: 1px solid #dce7f1;
}

.card {
    background: #fff;
    border: 1px solid #e0e9f1;
    border-radius: 11px;
    overflow: hidden;
    box-shadow: 0 5px 18px rgba(25, 67, 104, .05);
}

.order-summary {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    border-bottom: 1px solid #e7eef4;
}

.summary-item {
    padding: 16px 18px;
    border-right: 1px solid #e7eef4;
}

.summary-item:last-child {
    border-right: 0;
}

.label {
    margin-bottom: 6px;
    color: #8b9bab;
    font-size: 8px;
    font-weight: 800;
    text-transform: uppercase;
}

.value {
    color: #294761;
    font-size: 11px;
    font-weight: 800;
}

.order-number {
    color: #0875df;
    font-size: 14px;
}

.badge {
    display: inline-block;
    padding: 5px 9px;
    border-radius: 15px;
    font-size: 8px;
    font-weight: 900;
}

.badge.success {
    background: #e9f8ef;
    color: #15803d;
}

.badge.info {
    background: #eaf4ff;
    color: #0875df;
}

.badge.warning {
    background: #fff5df;
    color: #a45a00;
}

.badge.danger {
    background: #ffeded;
    color: #c62828;
}

.history-title {
    padding: 17px 19px;
    border-bottom: 1px solid #e7eef4;
    color: #183b62;
    font-size: 13px;
    font-weight: 900;
}

.timeline {
    padding: 24px;
}

.empty {
    padding: 30px 15px;
    text-align: center;
    color: #8a9aaa;
    font-size: 10px;
    background: #f8fbfd;
    border-radius: 8px;
}

.timeline-item {
    position: relative;
    display: grid;
    grid-template-columns: 28px 1fr;
    gap: 12px;
    min-height: 80px;
}

.timeline-item:last-child {
    min-height: 0;
}

.timeline-line {
    position: absolute;
    left: 13px;
    top: 28px;
    bottom: -4px;
    width: 2px;
    background: #e2eaf2;
}

.timeline-item:last-child .timeline-line {
    display: none;
}

.dot {
    position: relative;
    z-index: 2;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #eaf4ff;
    color: #0875df;
    font-size: 10px;
    font-weight: 900;
}

.activity {
    padding-bottom: 22px;
}

.activity-title {
    color: #294761;
    font-size: 11px;
    font-weight: 900;
}

.activity-description {
    margin-top: 5px;
    color: #6f8294;
    font-size: 10px;
    line-height: 1.5;
}

.activity-change {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin-top: 8px;
}

.old {
    padding: 4px 7px;
    border-radius: 5px;
    background: #fff1f1;
    color: #c62828;
    font-size: 8px;
    font-weight: 800;
}

.arrow {
    color: #9aabba;
    font-size: 10px;
}

.new {
    padding: 4px 7px;
    border-radius: 5px;
    background: #eaf8ef;
    color: #15803d;
    font-size: 8px;
    font-weight: 800;
}

.activity-meta {
    margin-top: 8px;
    color: #9aa9b7;
    font-size: 8px;
}

.warning-box {
    margin: 20px 24px 24px;
    padding: 13px 15px;
    border-radius: 7px;
    background: #fff8e8;
    border: 1px solid #f4dfaa;
    color: #8a6500;
    font-size: 9px;
    line-height: 1.6;
}

@media (max-width: 650px) {

    .page {
        padding: 15px;
    }

    .topbar {
        align-items: flex-start;
        flex-direction: column;
    }

    .order-summary {
        grid-template-columns: 1fr;
    }

    .summary-item {
        border-right: 0;
        border-bottom: 1px solid #e7eef4;
    }

    .summary-item:last-child {
        border-bottom: 0;
    }

}

</style>

</head>

<body>

<div class="page">

    <div class="topbar">

        <div>
            <h1>Order Activity</h1>

            <div class="subtitle">
                Complete history for order #<?= e($order['order_number']) ?>
            </div>
        </div>

        <div class="actions">

            <a
                href="order-view.php?id=<?= $orderId ?>"
                class="btn secondary"
            >
                ← Back to Order
            </a>

        </div>

    </div>


    <div class="card">

        <div class="order-summary">

            <div class="summary-item">

                <div class="label">
                    Order
                </div>

                <div class="value order-number">
                    #<?= e($order['order_number']) ?>
                </div>

            </div>


            <div class="summary-item">

                <div class="label">
                    Customer
                </div>

                <div class="value">
                    <?= e($order['customer_name']) ?>
                </div>

            </div>


            <div class="summary-item">

                <div class="label">
                    Current Status
                </div>

                <div class="value">

                    <span class="badge <?= statusClass($order['order_status']) ?>">
                        <?= e($order['order_status']) ?>
                    </span>

                </div>

            </div>

        </div>


        <div class="history-title">
            Activity History
        </div>


        <div class="timeline">

            <?php if (empty($activities)): ?>

                <div class="empty">

                    No activity has been recorded for this order yet.

                </div>

            <?php else: ?>

                <?php foreach ($activities as $activity): ?>

                    <?php
                    $action = trim((string)$activity['action']);

                    $title = match (strtolower($action)) {
                        'order_created' => 'Order Created',
                        'order_status_changed' => 'Order Status Changed',
                        'payment_status_changed' => 'Payment Status Changed',
                        default => ucwords(str_replace('_', ' ', $action))
                    };

                    $icon = match (strtolower($action)) {
                        'order_created' => '+',
                        'order_status_changed' => '↻',
                        'payment_status_changed' => 'KSh',
                        default => '•'
                    };
                    ?>

                    <div class="timeline-item">

                        <div class="timeline-line"></div>

                        <div class="dot">
                            <?= e($icon) ?>
                        </div>


                        <div class="activity">

                            <div class="activity-title">
                                <?= e($title) ?>
                            </div>


                            <?php if (!empty($activity['description'])): ?>

                                <div class="activity-description">

                                    <?= nl2br(
                                        e($activity['description'])
                                    ) ?>

                                </div>

                            <?php endif; ?>


                            <?php if (
                                $activity['old_value'] !== null &&
                                $activity['old_value'] !== '' &&
                                $activity['new_value'] !== null &&
                                $activity['new_value'] !== ''
                            ): ?>

                                <div class="activity-change">

                                    <span class="old">
                                        <?= e($activity['old_value']) ?>
                                    </span>

                                    <span class="arrow">
                                        →
                                    </span>

                                    <span class="new">
                                        <?= e($activity['new_value']) ?>
                                    </span>

                                </div>

                            <?php endif; ?>


                            <div class="activity-meta">

                                <?= e(
                                    date(
                                        'd M Y, H:i',
                                        strtotime($activity['created_at'])
                                    )
                                ) ?>

                                &nbsp; • &nbsp;

                                Admin ID:
                                <?= e($activity['admin_id']) ?>

                            </div>

                        </div>

                    </div>

                <?php endforeach; ?>

            <?php endif; ?>

        </div>


        <?php if (
            !(
                $tableCheck &&
                $tableCheck->num_rows > 0
            )
        ): ?>

            <div class="warning-box">

                <strong>Activity table not created yet.</strong>

                Create the table below before adding automatic logging
                to the order status and payment status updates.

            </div>

        <?php endif; ?>

    </div>

</div>

</body>
</html>
