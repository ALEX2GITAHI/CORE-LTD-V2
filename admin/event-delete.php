<?php

require_once __DIR__ . '/../includes/db.php';

$id = isset($_GET['id'])
    ? (int)$_GET['id']
    : 0;

if ($id <= 0) {

    header("Location: events.php");

    exit;
}


/*
|--------------------------------------------------------------------------
| GET EVENT
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT image
    FROM events
    WHERE id = ?
    LIMIT 1
");

$stmt->bind_param("i", $id);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {

    $stmt->close();

    header("Location: events.php");

    exit;
}

$event = $result->fetch_assoc();

$stmt->close();


/*
|--------------------------------------------------------------------------
| DELETE IMAGE
|--------------------------------------------------------------------------
*/

if (!empty($event['image'])) {

    $imageFile =
        __DIR__ .
        '/../' .
        $event['image'];

    if (is_file($imageFile)) {

        @unlink($imageFile);
    }
}


/*
|--------------------------------------------------------------------------
| DELETE EVENT
|--------------------------------------------------------------------------
*/

$delete = $conn->prepare("
    DELETE FROM events
    WHERE id = ?
");

$delete->bind_param("i", $id);

$delete->execute();

$delete->close();


header(
    "Location: events.php?success=" .
        urlencode('Event deleted successfully.')
);

exit;
