<?php
session_start();

require_once __DIR__ . '/../includes/db.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

try {

    // Check admin login
    if (!isset($_SESSION['admin_id'])) {
        http_response_code(401);

        echo json_encode([
            'success' => false,
            'message' => 'Unauthorized'
        ]);

        exit;
    }

    // Get unread count
    $stmt = $conn->prepare("
        SELECT COUNT(*) AS unread
        FROM admin_notifications
        WHERE is_read = 0
    ");

    if (!$stmt) {
        throw new Exception($conn->error);
    }

    $stmt->execute();

    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $unread = (int)($row['unread'] ?? 0);

    $stmt->close();


    // Get latest notifications
    $stmt = $conn->prepare("
        SELECT
            id,
            order_id,
            type,
            title,
            message,
            is_read,
            read_at,
            created_at
        FROM admin_notifications
        ORDER BY created_at DESC, id DESC
        LIMIT 10
    ");

    if (!$stmt) {
        throw new Exception($conn->error);
    }

    $stmt->execute();

    $result = $stmt->get_result();

    $notifications = [];

    while ($row = $result->fetch_assoc()) {

        $notifications[] = [
            'id' => (int)$row['id'],
            'order_id' => $row['order_id'] !== null
                ? (int)$row['order_id']
                : null,
            'type' => $row['type'],
            'title' => $row['title'],
            'message' => $row['message'] ?? '',
            'is_read' => (int)$row['is_read'],
            'read_at' => $row['read_at'],
            'created_at' => $row['created_at']
        ];
    }

    $stmt->close();


    echo json_encode([
        'success' => true,
        'unread' => $unread,
        'notifications' => $notifications
    ]);
} catch (Throwable $e) {

    http_response_code(500);

    echo json_encode([
        'success' => false,
        'message' => 'Unable to load notifications.',
        'error' => $e->getMessage()
    ]);
}