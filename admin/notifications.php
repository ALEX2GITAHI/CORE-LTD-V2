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

/* -------------------------------------------------------------
   ACTIONS
------------------------------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? '';

    if ($action === 'mark_all_read') {

        $stmt = $conn->prepare("
            UPDATE admin_notifications
            SET is_read = 1,
                read_at = NOW()
            WHERE is_read = 0
        ");

        if ($stmt) {
            $stmt->execute();
            $stmt->close();
        }

        header("Location: notifications.php");
        exit;
    }

    if ($action === 'mark_read') {

        $notificationId = (int)($_POST['notification_id'] ?? 0);

        if ($notificationId > 0) {
            $stmt = $conn->prepare("
                UPDATE admin_notifications
                SET is_read = 1,
                    read_at = NOW()
                WHERE id = ?
                LIMIT 1
            ");

            if ($stmt) {
                $stmt->bind_param("i", $notificationId);
                $stmt->execute();
                $stmt->close();
            }
        }

        $orderId = (int)($_POST['order_id'] ?? 0);

        if ($orderId > 0) {
            header("Location: order-view.php?id=" . $orderId);
        } else {
            header("Location: notifications.php");
        }
        exit;
    }
}

/* -------------------------------------------------------------
   NOTIFICATIONS
------------------------------------------------------------- */
$notifications = [];

$stmt = $conn->prepare("
    SELECT
        n.id,
        n.order_id,
        n.type,
        n.title,
        n.message,
        n.is_read,
        n.created_at,
        o.order_number,
        o.customer_name,
        o.total_amount
    FROM admin_notifications n
    LEFT JOIN orders o
        ON o.id = n.order_id
    ORDER BY n.id DESC
    LIMIT 100
");

if ($stmt) {
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $notifications[] = $row;
    }

    $stmt->close();
}

/* -------------------------------------------------------------
   COUNTS
------------------------------------------------------------- */
$unread = 0;
$total = count($notifications);

$countResult = $conn->query("
    SELECT COUNT(*) AS total
    FROM admin_notifications
    WHERE is_read = 0
");

if ($countResult) {
    $unread = (int)$countResult->fetch_assoc()['total'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Notifications | CORE LTD Admin</title>

<style>
* {
    box-sizing: border-box;
}

body {
    margin: 0;
    background: #f4f7fb;
    color: #304b63;
    font-family: Arial, Helvetica, sans-serif;
}

/* =========================================================
   LAYOUT
========================================================= */

.page {
    width: 100%;
    max-width: 1180px;
    margin: 0 auto;
    padding: 30px;
}

/* =========================================================
   HEADER
========================================================= */

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
    margin-bottom: 22px;
}

.page-title h1 {
    margin: 0;
    color: #173b61;
    font-size: 25px;
    font-weight: 800;
}

.page-title p {
    margin: 6px 0 0;
    color: #8a9aaa;
    font-size: 11px;
}

.header-actions {
    display: flex;
    align-items: center;
    gap: 9px;
}

.btn {
    border: 0;
    border-radius: 7px;
    padding: 10px 15px;
    font-size: 10px;
    font-weight: 800;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.btn-primary {
    background: #0875df;
    color: #fff;
}

.btn-primary:hover {
    background: #0667c5;
}

.btn-light {
    background: #fff;
    color: #0875df;
    border: 1px solid #dce6ef;
}

.btn-light:hover {
    background: #f5f9fd;
}

/* =========================================================
   SUMMARY
========================================================= */

.summary {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 14px;
    margin-bottom: 18px;
}

.summary-card {
    background: #fff;
    border: 1px solid #e2e9f0;
    border-radius: 10px;
    padding: 17px;
}

.summary-label {
    color: #8999a9;
    font-size: 9px;
    text-transform: uppercase;
    letter-spacing: .4px;
    font-weight: 800;
}

.summary-value {
    margin-top: 7px;
    color: #173b61;
    font-size: 21px;
    font-weight: 900;
}

.summary-value.unread {
    color: #0875df;
}

/* =========================================================
   NOTIFICATION CARD
========================================================= */

.notification-card {
    background: #fff;
    border: 1px solid #e0e8f0;
    border-radius: 11px;
    overflow: hidden;
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 17px 19px;
    border-bottom: 1px solid #e6edf3;
}

.card-header h2 {
    margin: 0;
    color: #294761;
    font-size: 13px;
}

.card-header span {
    color: #91a0ad;
    font-size: 9px;
}

/* =========================================================
   NOTIFICATION ROW
========================================================= */

.notification {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 17px 19px;
    border-bottom: 1px solid #e8eef3;
    background: #fff;
}

.notification:last-child {
    border-bottom: 0;
}

.notification.unread {
    background: #f4f9ff;
}

.notification-icon {
    flex: 0 0 40px;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #eaf4ff;
    color: #0875df;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 17px;
    font-weight: 900;
}

.notification.unread .notification-icon {
    background: #0875df;
    color: #fff;
}

.notification-content {
    flex: 1;
    min-width: 0;
}

.notification-title {
    color: #294761;
    font-size: 11px;
    font-weight: 900;
}

.notification-message {
    margin-top: 5px;
    color: #6e8294;
    font-size: 10px;
    line-height: 1.5;
}

.notification-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 7px;
    color: #9aa9b7;
    font-size: 8px;
}

.order-number {
    color: #0875df;
    font-weight: 900;
}

.notification-action {
    flex: 0 0 auto;
}

.view-order {
    border: 0;
    background: #edf6ff;
    color: #0875df;
    border-radius: 6px;
    padding: 9px 12px;
    font-size: 9px;
    font-weight: 900;
    cursor: pointer;
}

.view-order:hover {
    background: #dceeff;
}

/* =========================================================
   EMPTY
========================================================= */

.empty {
    padding: 65px 20px;
    text-align: center;
}

.empty-icon {
    font-size: 35px;
    margin-bottom: 12px;
    opacity: .65;
}

.empty h3 {
    margin: 0;
    color: #526a7e;
    font-size: 13px;
}

.empty p {
    margin: 6px 0 0;
    color: #9aa9b7;
    font-size: 10px;
}

/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 750px) {

    .page {
        padding: 18px;
    }

    .page-header {
        align-items: flex-start;
        flex-direction: column;
    }

    .header-actions {
        width: 100%;
    }

    .header-actions .btn {
        flex: 1;
    }

    .summary {
        grid-template-columns: 1fr;
    }

    .notification {
        align-items: flex-start;
    }

    .notification-action {
        align-self: center;
    }
}

@media (max-width: 520px) {

    .notification {
        padding: 14px;
    }

    .notification-icon {
        flex-basis: 34px;
        width: 34px;
        height: 34px;
        font-size: 14px;
    }

    .notification-action {
        display: none;
    }
}
</style>
</head>

<body>

<div class="page">

    <!-- =====================================================
         PAGE HEADER
    ====================================================== -->

    <div class="page-header">

        <div class="page-title">

            <h1>🔔 Notifications</h1>

            <p>
                View and manage CORE LTD admin notifications.
            </p>

        </div>

        <div class="header-actions">

            <a href="index.php" class="btn btn-light">
                ← Dashboard
            </a>

            <?php if ($unread > 0): ?>

                <form method="POST" style="margin:0;">

                    <input
                        type="hidden"
                        name="action"
                        value="mark_all_read"
                    >

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        Mark All Read
                    </button>

                </form>

            <?php endif; ?>

        </div>

    </div>


    <!-- =====================================================
         SUMMARY
    ====================================================== -->

    <div class="summary">

        <div class="summary-card">

            <div class="summary-label">
                Total Notifications
            </div>

            <div class="summary-value">
                <?= $total ?>
            </div>

        </div>


        <div class="summary-card">

            <div class="summary-label">
                Unread
            </div>

            <div class="summary-value unread">
                <?= $unread ?>
            </div>

        </div>


        <div class="summary-card">

            <div class="summary-label">
                Notification Type
            </div>

            <div class="summary-value">
                Orders
            </div>

        </div>

    </div>


    <!-- =====================================================
         NOTIFICATIONS
    ====================================================== -->

    <div class="notification-card">

        <div class="card-header">

            <h2>
                Recent Notifications
            </h2>

            <span>
                Latest 100 notifications
            </span>

        </div>


        <?php if (empty($notifications)): ?>

            <div class="empty">

                <div class="empty-icon">
                    🔔
                </div>

                <h3>
                    No notifications yet
                </h3>

                <p>
                    New order notifications will appear here automatically.
                </p>

            </div>

        <?php else: ?>

            <?php foreach ($notifications as $notification): ?>

                <?php
                    $notificationId =
                        (int)$notification['id'];

                    $orderId =
                        (int)($notification['order_id'] ?? 0);

                    $date =
                        date(
                            'd M Y, H:i',
                            strtotime($notification['created_at'])
                        );
                ?>

                <div class="notification <?= !$notification['is_read'] ? 'unread' : '' ?>">

                    <div class="notification-icon">
                        <?= $notification['is_read'] ? '✓' : '!' ?>
                    </div>


                    <div class="notification-content">

                        <div class="notification-title">

                            <?= e($notification['title']) ?>

                        </div>


                        <div class="notification-message">

                            <?= e($notification['message']) ?>

                        </div>


                        <div class="notification-meta">

                            <?php if (!empty($notification['order_number'])): ?>

                                <span class="order-number">

                                    Order #<?= e($notification['order_number']) ?>

                                </span>

                            <?php endif; ?>


                            <?php if (!empty($notification['customer_name'])): ?>

                                <span>

                                    Customer:
                                    <?= e($notification['customer_name']) ?>

                                </span>

                            <?php endif; ?>


                            <?php if ($notification['total_amount'] !== null): ?>

                                <span>

                                    KSh
                                    <?= number_format(
                                        (float)$notification['total_amount'],
                                        2
                                    ) ?>

                                </span>

                            <?php endif; ?>


                            <span>
                                <?= e($date) ?>
                            </span>

                        </div>

                    </div>


                    <?php if ($orderId > 0): ?>

                        <div class="notification-action">

                            <form
                                method="POST"
                                style="margin:0;"
                            >

                                <input
                                    type="hidden"
                                    name="action"
                                    value="mark_read"
                                >

                                <input
                                    type="hidden"
                                    name="notification_id"
                                    value="<?= $notificationId ?>"
                                >

                                <input
                                    type="hidden"
                                    name="order_id"
                                    value="<?= $orderId ?>"
                                >

                                <button
                                    type="submit"
                                    class="view-order"
                                >
                                    View Order →
                                </button>

                            </form>

                        </div>

                    <?php endif; ?>

                </div>

            <?php endforeach; ?>

        <?php endif; ?>

    </div>

</div>

</body>
</html>
