<?php

require_once __DIR__ . '/../includes/db.php';


/*
|--------------------------------------------------------------------------
| GET EVENT ID
|--------------------------------------------------------------------------
*/

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    exit('Invalid event.');
}


/*
|--------------------------------------------------------------------------
| GET EVENT
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT *
    FROM events
    WHERE id = ?
    LIMIT 1
");

if (!$stmt) {
    exit('Database error.');
}

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();
    exit('Event not found.');
}

$event = $result->fetch_assoc();

$stmt->close();

$error = '';


/*
|--------------------------------------------------------------------------
| CREATE SLUG
|--------------------------------------------------------------------------
*/

function createSlug($text)
{
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);

    return trim($text, '-');
}


/*
|--------------------------------------------------------------------------
| UPDATE EVENT
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title = trim($_POST['title'] ?? '');
    $eventDate = trim($_POST['event_date'] ?? '');
    $eventTime = trim($_POST['event_time'] ?? '');
    $venue = trim($_POST['venue'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $status = isset($_POST['status']) ? 1 : 0;


    /*
    |--------------------------------------------------------------------------
    | VALIDATION
    |--------------------------------------------------------------------------
    */

    if ($title === '') {

        $error = 'Event title is required.';
    } else {

        /*
        |--------------------------------------------------------------------------
        | CREATE UNIQUE SLUG
        |--------------------------------------------------------------------------
        */

        $slug = createSlug($title);

        $baseSlug = $slug;
        $counter = 1;

        while (true) {

            $check = $conn->prepare("
                SELECT id
                FROM events
                WHERE slug = ?
                AND id != ?
                LIMIT 1
            ");

            $check->bind_param(
                "si",
                $slug,
                $id
            );

            $check->execute();

            $checkResult = $check->get_result();

            $exists = $checkResult->num_rows > 0;

            $check->close();

            if (!$exists) {
                break;
            }

            $counter++;

            $slug = $baseSlug . '-' . $counter;
        }


        /*
        |--------------------------------------------------------------------------
        | KEEP CURRENT IMAGE
        |--------------------------------------------------------------------------
        */

        $imagePath = $event['image'];


        /*
        |--------------------------------------------------------------------------
        | REPLACE IMAGE
        |--------------------------------------------------------------------------
        */

        if (
            isset($_FILES['image']) &&
            $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE
        ) {

            if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {

                $error = 'There was an error uploading the poster.';
            } else {

                $allowedTypes = [
                    'image/jpeg' => 'jpg',
                    'image/png'  => 'png',
                    'image/webp' => 'webp'
                ];

                $fileType = mime_content_type(
                    $_FILES['image']['tmp_name']
                );

                if (!isset($allowedTypes[$fileType])) {

                    $error = 'Only JPG, PNG and WEBP images are allowed.';
                } else {

                    $extension = $allowedTypes[$fileType];

                    $uploadDir = __DIR__ . '/../uploads/events/';

                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }

                    $filename =
                        $slug .
                        '-' .
                        time() .
                        '.' .
                        $extension;

                    $destination = $uploadDir . $filename;

                    if (
                        move_uploaded_file(
                            $_FILES['image']['tmp_name'],
                            $destination
                        )
                    ) {

                        /*
                        |--------------------------------------------------------------------------
                        | DELETE OLD POSTER
                        |--------------------------------------------------------------------------
                        */

                        if (!empty($event['image'])) {

                            $oldFile =
                                __DIR__ .
                                '/../' .
                                $event['image'];

                            if (is_file($oldFile)) {
                                @unlink($oldFile);
                            }
                        }

                        $imagePath =
                            'uploads/events/' .
                            $filename;
                    } else {

                        $error = 'Unable to save uploaded poster.';
                    }
                }
            }
        }


        /*
        |--------------------------------------------------------------------------
        | UPDATE DATABASE
        |--------------------------------------------------------------------------
        */

        if ($error === '') {

            $update = $conn->prepare("
                UPDATE events
                SET
                    title = ?,
                    slug = ?,
                    event_date = ?,
                    event_time = ?,
                    venue = ?,
                    description = ?,
                    image = ?,
                    status = ?
                WHERE id = ?
            ");

            if (!$update) {

                $error = 'Database error: ' . $conn->error;
            } else {

                $update->bind_param(
                    "sssssssii",
                    $title,
                    $slug,
                    $eventDate,
                    $eventTime,
                    $venue,
                    $description,
                    $imagePath,
                    $status,
                    $id
                );

                if ($update->execute()) {

                    $update->close();

                    header(
                        "Location: events.php?success=" .
                            urlencode('Event updated successfully.')
                    );

                    exit;
                } else {

                    $error =
                        'Unable to update event: ' .
                        $update->error;

                    $update->close();
                }
            }
        }
    }
}

?>


<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        Edit Event | CORE LTD Admin
    </title>


    <!-- FONT AWESOME -->

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">


    <style>
        * {
            box-sizing: border-box;
        }


        body {

            margin: 0;

            background:
                linear-gradient(135deg,
                    #f8fafc,
                    #eef4ff);

            font-family:
                "Segoe UI",
                Arial,
                sans-serif;

            color: #1e293b;
        }


        /* =========================================================
           PAGE
        ========================================================= */

        .core-event-page {

            width: 100%;

            max-width: 1180px;

            margin: 0 auto;

            padding: 35px 25px 60px;
        }


        /* =========================================================
           PAGE HEADER
        ========================================================= */

        .core-page-header {

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 20px;

            margin-bottom: 25px;
        }


        .core-page-title {

            display: flex;

            align-items: center;

            gap: 15px;
        }


        .core-page-title-icon {

            width: 52px;

            height: 52px;

            display: flex;

            align-items: center;

            justify-content: center;

            border-radius: 14px;

            background:
                linear-gradient(135deg,
                    #2563eb,
                    #06b6d4);

            color: white;

            font-size: 21px;

            box-shadow:
                0 10px 25px rgba(37, 99, 235, .20);
        }


        .core-page-title h1 {

            margin: 0;

            font-size: 27px;

            font-weight: 700;

            color: #0f172a;
        }


        .core-page-title p {

            margin: 4px 0 0;

            color: #64748b;

            font-size: 14px;
        }


        .core-back-btn {

            display: inline-flex;

            align-items: center;

            gap: 8px;

            padding: 11px 17px;

            border-radius: 10px;

            background: white;

            border: 1px solid #e2e8f0;

            color: #334155;

            text-decoration: none;

            font-weight: 600;

            transition: .2s;
        }


        .core-back-btn:hover {

            color: #2563eb;

            border-color: #bfdbfe;

            transform: translateY(-1px);
        }


        /* =========================================================
           ALERT
        ========================================================= */

        .core-error {

            display: flex;

            align-items: center;

            gap: 12px;

            padding: 15px 18px;

            margin-bottom: 22px;

            border-radius: 12px;

            background: #fff1f2;

            border: 1px solid #fecdd3;

            color: #be123c;

            font-size: 14px;

            font-weight: 600;
        }


        /* =========================================================
           MAIN GRID
        ========================================================= */

        .core-event-grid {

            display: grid;

            grid-template-columns:
                minmax(0, 1fr) 360px;

            gap: 25px;

            align-items: start;
        }


        /* =========================================================
           CARD
        ========================================================= */

        .core-card {

            background: rgba(255, 255, 255, .96);

            border: 1px solid #e5e7eb;

            border-radius: 18px;

            box-shadow:
                0 15px 40px rgba(15, 23, 42, .07);

            overflow: hidden;
        }


        .core-card-header {

            padding: 20px 23px;

            border-bottom: 1px solid #eef2f7;

            display: flex;

            align-items: center;

            justify-content: space-between;
        }


        .core-card-header h2 {

            margin: 0;

            font-size: 17px;

            color: #0f172a;
        }


        .core-card-header span {

            font-size: 12px;

            color: #64748b;

            background: #f1f5f9;

            padding: 6px 10px;

            border-radius: 20px;
        }


        .core-card-body {

            padding: 24px;
        }


        /* =========================================================
           FORM
        ========================================================= */

        .core-form-group {

            margin-bottom: 20px;
        }


        .core-form-group label {

            display: block;

            margin-bottom: 8px;

            font-size: 13px;

            font-weight: 700;

            color: #334155;
        }


        .core-required {

            color: #ef4444;
        }


        .core-input,
        .core-textarea {

            width: 100%;

            border: 1px solid #dbe3ed;

            background: #fff;

            border-radius: 10px;

            padding: 12px 14px;

            color: #0f172a;

            font-size: 14px;

            outline: none;

            transition: .2s;
        }


        .core-input {

            height: 46px;
        }


        .core-textarea {

            min-height: 155px;

            resize: vertical;

            line-height: 1.6;
        }


        .core-input:focus,
        .core-textarea:focus {

            border-color: #3b82f6;

            box-shadow:
                0 0 0 3px rgba(59, 130, 246, .10);
        }


        .core-two-columns {

            display: grid;

            grid-template-columns: 1fr 1fr;

            gap: 18px;
        }


        /* =========================================================
           POSTER
        ========================================================= */

        .core-poster-box {

            background:
                linear-gradient(145deg,
                    #eff6ff,
                    #f8fafc);

            border: 1px solid #dbeafe;

            border-radius: 15px;

            padding: 15px;

            text-align: center;
        }


        .core-poster-box img {

            display: block;

            width: 100%;

            max-height: 430px;

            object-fit: contain;

            border-radius: 10px;

            background: white;

            box-shadow:
                0 8px 25px rgba(15, 23, 42, .08);
        }


        .core-poster-empty {

            min-height: 280px;

            display: flex;

            flex-direction: column;

            align-items: center;

            justify-content: center;

            color: #94a3b8;
        }


        .core-poster-empty i {

            font-size: 45px;

            margin-bottom: 12px;
        }


        .core-poster-label {

            display: flex;

            align-items: center;

            gap: 8px;

            margin-bottom: 10px;

            color: #334155;

            font-size: 13px;

            font-weight: 700;
        }


        .core-file-input {

            width: 100%;

            padding: 10px;

            background: white;

            border: 1px dashed #cbd5e1;

            border-radius: 10px;

            font-size: 13px;
        }


        .core-file-note {

            margin-top: 8px;

            color: #64748b;

            font-size: 11px;

            text-align: left;
        }


        /* =========================================================
           STATUS
        ========================================================= */

        .core-status-box {

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 15px;

            padding: 14px 16px;

            background: #f8fafc;

            border: 1px solid #e2e8f0;

            border-radius: 12px;
        }


        .core-status-info {

            display: flex;

            align-items: center;

            gap: 11px;
        }


        .core-status-icon {

            width: 38px;

            height: 38px;

            border-radius: 10px;

            display: flex;

            align-items: center;

            justify-content: center;

            background: #dcfce7;

            color: #16a34a;
        }


        .core-status-info strong {

            display: block;

            font-size: 13px;

            color: #1e293b;
        }


        .core-status-info small {

            color: #64748b;

            font-size: 11px;
        }


        /* =========================================================
           SWITCH
        ========================================================= */

        .core-switch {

            position: relative;

            width: 48px;

            height: 26px;
        }


        .core-switch input {

            opacity: 0;

            width: 0;

            height: 0;
        }


        .core-slider {

            position: absolute;

            inset: 0;

            cursor: pointer;

            background: #cbd5e1;

            border-radius: 30px;

            transition: .25s;
        }


        .core-slider:before {

            content: "";

            position: absolute;

            width: 20px;

            height: 20px;

            left: 3px;

            top: 3px;

            background: white;

            border-radius: 50%;

            transition: .25s;

            box-shadow:
                0 2px 5px rgba(0, 0, 0, .15);
        }


        .core-switch input:checked+.core-slider {

            background: #16a34a;
        }


        .core-switch input:checked+.core-slider:before {

            transform: translateX(22px);
        }


        /* =========================================================
           ACTIONS
        ========================================================= */

        .core-form-actions {

            display: flex;

            align-items: center;

            justify-content: flex-end;

            gap: 10px;

            margin-top: 25px;

            padding-top: 20px;

            border-top: 1px solid #eef2f7;
        }


        .core-btn {

            display: inline-flex;

            align-items: center;

            justify-content: center;

            gap: 8px;

            min-height: 44px;

            padding: 0 18px;

            border-radius: 10px;

            border: 0;

            cursor: pointer;

            text-decoration: none;

            font-size: 13px;

            font-weight: 700;

            transition: .2s;
        }


        .core-btn-primary {

            color: white;

            background:
                linear-gradient(135deg,
                    #2563eb,
                    #06b6d4);

            box-shadow:
                0 8px 18px rgba(37, 99, 235, .18);
        }


        .core-btn-primary:hover {

            transform: translateY(-2px);

            box-shadow:
                0 12px 25px rgba(37, 99, 235, .25);
        }


        .core-btn-secondary {

            color: #475569;

            background: #f1f5f9;

            border: 1px solid #e2e8f0;
        }


        .core-btn-secondary:hover {

            background: #e2e8f0;
        }


        /* =========================================================
           RESPONSIVE
        ========================================================= */

        @media(max-width: 900px) {

            .core-event-grid {

                grid-template-columns: 1fr;
            }

        }


        @media(max-width: 650px) {

            .core-event-page {

                padding:
                    22px 15px 45px;
            }


            .core-page-header {

                align-items: flex-start;

                flex-direction: column;
            }


            .core-page-title h1 {

                font-size: 22px;
            }


            .core-two-columns {

                grid-template-columns: 1fr;
            }


            .core-card-body {

                padding: 18px;
            }


            .core-form-actions {

                flex-direction: column-reverse;

                align-items: stretch;
            }


            .core-btn {

                width: 100%;
            }

        }
    </style>

</head>


<body>


    <div class="core-event-page">


        <!-- =========================================================
         PAGE HEADER
    ========================================================= -->

        <div class="core-page-header">

            <div class="core-page-title">

                <div class="core-page-title-icon">

                    <i class="fa-solid fa-calendar-pen"></i>

                </div>

                <div>

                    <h1>
                        Edit Event
                    </h1>

                    <p>
                        Update event details and poster.
                    </p>

                </div>

            </div>


            <a href="events.php" class="core-back-btn">

                <i class="fa-solid fa-arrow-left"></i>

                Back to Events

            </a>

        </div>


        <!-- =========================================================
         ERROR
    ========================================================= -->

        <?php if ($error !== ''): ?>

            <div class="core-error">

                <i class="fa-solid fa-circle-exclamation"></i>

                <?= htmlspecialchars($error); ?>

            </div>

        <?php endif; ?>


        <!-- =========================================================
         FORM GRID
    ========================================================= -->

        <form method="POST" enctype="multipart/form-data">

            <div class="core-event-grid">


                <!-- =================================================
                 EVENT DETAILS
            ================================================= -->

                <div class="core-card">

                    <div class="core-card-header">

                        <h2>

                            <i class="fa-solid fa-circle-info"></i>

                            Event Details

                        </h2>

                        <span>
                            Event #<?= $id; ?>
                        </span>

                    </div>


                    <div class="core-card-body">


                        <!-- TITLE -->

                        <div class="core-form-group">

                            <label>

                                Event Title

                                <span class="core-required">
                                    *
                                </span>

                            </label>

                            <input type="text" name="title" class="core-input" required
                                value="<?= htmlspecialchars($event['title']); ?>" placeholder="Enter event title">

                        </div>


                        <!-- DATE + TIME -->

                        <div class="core-two-columns">


                            <div class="core-form-group">

                                <label>
                                    <i class="fa-regular fa-calendar"></i>
                                    Event Date
                                </label>

                                <input type="date" name="event_date" class="core-input"
                                    value="<?= htmlspecialchars($event['event_date'] ?? ''); ?>">

                            </div>


                            <div class="core-form-group">

                                <label>
                                    <i class="fa-regular fa-clock"></i>
                                    Event Time
                                </label>

                                <input type="text" name="event_time" class="core-input"
                                    value="<?= htmlspecialchars($event['event_time'] ?? ''); ?>"
                                    placeholder="e.g. 10:00 AM">

                            </div>


                        </div>


                        <!-- VENUE -->

                        <div class="core-form-group">

                            <label>

                                <i class="fa-solid fa-location-dot"></i>

                                Venue

                            </label>

                            <input type="text" name="venue" class="core-input"
                                value="<?= htmlspecialchars($event['venue'] ?? ''); ?>" placeholder="Event venue">

                        </div>


                        <!-- DESCRIPTION -->

                        <div class="core-form-group">

                            <label>

                                <i class="fa-solid fa-align-left"></i>

                                Description

                            </label>

                            <textarea name="description" class="core-textarea"
                                placeholder="Enter event description..."><?= htmlspecialchars($event['description'] ?? ''); ?></textarea>

                        </div>


                        <!-- STATUS -->

                        <div class="core-status-box">

                            <div class="core-status-info">

                                <div class="core-status-icon">

                                    <i class="fa-solid fa-eye"></i>

                                </div>

                                <div>

                                    <strong>
                                        Event Visibility
                                    </strong>

                                    <small>
                                        Active events are visible on the website.
                                    </small>

                                </div>

                            </div>


                            <label class="core-switch">

                                <input type="checkbox" name="status" value="1"
                                    <?= (int)$event['status'] === 1 ? 'checked' : ''; ?>>

                                <span class="core-slider"></span>

                            </label>

                        </div>


                        <!-- ACTIONS -->

                        <div class="core-form-actions">

                            <a href="events.php" class="core-btn core-btn-secondary">

                                <i class="fa-solid fa-xmark"></i>

                                Cancel

                            </a>


                            <button type="submit" class="core-btn core-btn-primary">

                                <i class="fa-solid fa-floppy-disk"></i>

                                Save Changes

                            </button>

                        </div>


                    </div>

                </div>


                <!-- =================================================
                 POSTER
            ================================================= -->

                <div class="core-card">

                    <div class="core-card-header">

                        <h2>

                            <i class="fa-solid fa-image"></i>

                            Event Poster

                        </h2>

                    </div>


                    <div class="core-card-body">


                        <div class="core-form-group">

                            <div class="core-poster-label">

                                <i class="fa-solid fa-image"></i>

                                Current Poster

                            </div>


                            <div class="core-poster-box">

                                <?php if (!empty($event['image'])): ?>

                                    <img src="../<?= htmlspecialchars($event['image']); ?>"
                                        alt="<?= htmlspecialchars($event['title']); ?>">

                                <?php else: ?>

                                    <div class="core-poster-empty">

                                        <i class="fa-regular fa-image"></i>

                                        <span>
                                            No poster uploaded
                                        </span>

                                    </div>

                                <?php endif; ?>

                            </div>

                        </div>


                        <!-- REPLACE POSTER -->

                        <div class="core-form-group">

                            <label>

                                <i class="fa-solid fa-cloud-arrow-up"></i>

                                Replace Poster

                            </label>


                            <input type="file" name="image" class="core-file-input"
                                accept="image/jpeg,image/png,image/webp">


                            <div class="core-file-note">

                                JPG, PNG or WEBP.
                                Recommended: high-quality event poster.

                            </div>

                        </div>


                    </div>

                </div>


            </div>

        </form>


    </div>


</body>

</html>