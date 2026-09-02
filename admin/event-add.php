<?php

/*
|--------------------------------------------------------------------------
| CORE LTD ADMIN - ADD EVENT
|--------------------------------------------------------------------------
*/

session_start();

require_once __DIR__ . '/../includes/db.php';

/*
|--------------------------------------------------------------------------
| ADMIN AUTHENTICATION
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$error = '';

/*
|--------------------------------------------------------------------------
| GENERATE SLUG
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
| FORM SUBMISSION
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title       = trim($_POST['title'] ?? '');
    $eventDate   = trim($_POST['event_date'] ?? '');
    $eventTime   = trim($_POST['event_time'] ?? '');
    $venue       = trim($_POST['venue'] ?? '');
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
        | CREATE SLUG
        |--------------------------------------------------------------------------
        */

        $slug = createSlug($title);

        if ($slug === '') {
            $slug = 'event';
        }

        /*
        |--------------------------------------------------------------------------
        | MAKE SLUG UNIQUE
        |--------------------------------------------------------------------------
        */

        $baseSlug = $slug;
        $counter  = 1;

        while (true) {

            $check = $conn->prepare("
                SELECT id
                FROM events
                WHERE slug = ?
                LIMIT 1
            ");

            if (!$check) {
                $error = 'Unable to check event slug.';
                break;
            }

            $check->bind_param("s", $slug);
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
        | IMAGE UPLOAD
        |--------------------------------------------------------------------------
        */

        $imagePath = null;

        if (
            $error === '' &&
            isset($_FILES['image']) &&
            $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE
        ) {

            if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {

                $error = 'There was an error uploading the image.';
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

                    $error =
                        'Only JPG, PNG and WEBP images are allowed.';
                } else {

                    $extension = $allowedTypes[$fileType];

                    $uploadDir =
                        __DIR__ . '/../uploads/events/';

                    if (!is_dir($uploadDir)) {

                        mkdir(
                            $uploadDir,
                            0755,
                            true
                        );
                    }

                    $filename =
                        $slug . '-' .
                        time() . '.' .
                        $extension;

                    $destination =
                        $uploadDir . $filename;

                    if (
                        move_uploaded_file(
                            $_FILES['image']['tmp_name'],
                            $destination
                        )
                    ) {

                        $imagePath =
                            'uploads/events/' . $filename;
                    } else {

                        $error =
                            'Unable to save uploaded image.';
                    }
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | INSERT EVENT
        |--------------------------------------------------------------------------
        */

        if ($error === '') {

            $stmt = $conn->prepare("
                INSERT INTO events (
                    title,
                    slug,
                    event_date,
                    event_time,
                    venue,
                    description,
                    image,
                    status
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");

            if (!$stmt) {

                $error =
                    'Database error: ' .
                    $conn->error;
            } else {

                $stmt->bind_param(
                    "sssssssi",
                    $title,
                    $slug,
                    $eventDate,
                    $eventTime,
                    $venue,
                    $description,
                    $imagePath,
                    $status
                );

                if ($stmt->execute()) {

                    $stmt->close();

                    header(
                        "Location: events.php?success=" .
                            urlencode(
                                'Event added successfully.'
                            )
                    );

                    exit;
                } else {

                    $error =
                        'Unable to add event: ' .
                        $stmt->error;

                    $stmt->close();
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
        Add Event | CORE LTD Admin
    </title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #f4f7fb;
            font-family:
                Arial,
                Helvetica,
                sans-serif;
            color: #172033;
        }

        /*
        |--------------------------------------------------------------------------
        | PAGE
        |--------------------------------------------------------------------------
        */

        .core-admin-page {
            min-height: 100vh;
            padding: 30px;
        }

        .core-admin-container {
            max-width: 1250px;
            margin: auto;
        }

        /*
        |--------------------------------------------------------------------------
        | TOP HEADER
        |--------------------------------------------------------------------------
        */

        .core-page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 28px;
        }

        .core-page-heading {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .core-page-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;

            display: flex;
            align-items: center;
            justify-content: center;

            background:
                linear-gradient(135deg,
                    #0d6efd,
                    #6f42c1);

            color: #fff;

            font-size: 21px;

            box-shadow:
                0 8px 20px rgba(13, 110, 253, .20);
        }

        .core-page-heading h1 {
            margin: 0 0 5px;
            font-size: 26px;
            font-weight: 700;
        }

        .core-page-heading p {
            margin: 0;
            color: #7b8798;
            font-size: 14px;
        }

        .core-back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;

            padding: 11px 17px;

            border-radius: 9px;

            text-decoration: none;

            color: #475569;
            background: #fff;

            border: 1px solid #e2e8f0;

            font-size: 14px;
            font-weight: 600;

            transition: .2s ease;
        }

        .core-back-btn:hover {
            color: #0d6efd;
            border-color: #0d6efd;
        }

        /*
        |--------------------------------------------------------------------------
        | ERROR
        |--------------------------------------------------------------------------
        */

        .core-error {
            display: flex;
            align-items: center;
            gap: 12px;

            padding: 14px 17px;

            margin-bottom: 22px;

            border-radius: 10px;

            background: #fff1f2;
            color: #be123c;

            border: 1px solid #fecdd3;

            font-size: 14px;
            font-weight: 600;
        }

        /*
        |--------------------------------------------------------------------------
        | MAIN GRID
        |--------------------------------------------------------------------------
        */

        .core-event-layout {
            display: grid;

            grid-template-columns:
                minmax(0, 1fr) 360px;

            gap: 25px;

            align-items: start;
        }

        /*
        |--------------------------------------------------------------------------
        | CARDS
        |--------------------------------------------------------------------------
        */

        .core-card {
            background: #fff;

            border-radius: 16px;

            border: 1px solid #e8edf4;

            box-shadow:
                0 8px 30px rgba(15, 23, 42, .05);
        }

        .core-card-header {
            padding: 22px 25px;

            border-bottom:
                1px solid #edf1f6;
        }

        .core-card-header h2 {
            margin: 0;

            font-size: 17px;
            font-weight: 700;
        }

        .core-card-header p {
            margin: 5px 0 0;

            color: #8a96a8;

            font-size: 13px;
        }

        .core-card-body {
            padding: 25px;
        }

        /*
        |--------------------------------------------------------------------------
        | FORM
        |--------------------------------------------------------------------------
        */

        .core-form-group {
            margin-bottom: 20px;
        }

        .core-form-group:last-child {
            margin-bottom: 0;
        }

        .core-form-label {
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

            border:
                1px solid #dbe3ec;

            background: #fbfcfe;

            border-radius: 9px;

            padding: 12px 14px;

            font-size: 14px;

            color: #1e293b;

            outline: none;

            transition:
                border-color .2s ease,
                box-shadow .2s ease,
                background .2s ease;
        }

        .core-input {
            height: 45px;
        }

        .core-textarea {
            min-height: 145px;

            resize: vertical;

            line-height: 1.6;
        }

        .core-input:focus,
        .core-textarea:focus {

            background: #fff;

            border-color: #0d6efd;

            box-shadow:
                0 0 0 3px rgba(13, 110, 253, .09);
        }

        .core-form-row {

            display: grid;

            grid-template-columns:
                1fr 1fr;

            gap: 18px;
        }

        /*
        |--------------------------------------------------------------------------
        | UPLOAD AREA
        |--------------------------------------------------------------------------
        */

        .core-upload-area {

            position: relative;

            border:
                2px dashed #d5deea;

            border-radius: 13px;

            background: #fafcff;

            padding: 28px 20px;

            text-align: center;

            cursor: pointer;

            transition: .2s ease;
        }

        .core-upload-area:hover {

            border-color: #0d6efd;

            background:
                #f7faff;
        }

        .core-upload-icon {

            width: 58px;
            height: 58px;

            margin: 0 auto 13px;

            border-radius: 14px;

            display: flex;

            align-items: center;
            justify-content: center;

            background:
                linear-gradient(135deg,
                    #e8f1ff,
                    #f0eaff);

            color: #0d6efd;

            font-size: 22px;
        }

        .core-upload-area h3 {

            margin: 0 0 6px;

            font-size: 15px;
        }

        .core-upload-area p {

            margin: 0;

            color: #94a3b8;

            font-size: 12px;
        }

        .core-upload-area input[type="file"] {

            position: absolute;

            inset: 0;

            width: 100%;
            height: 100%;

            opacity: 0;

            cursor: pointer;
        }

        /*
        |--------------------------------------------------------------------------
        | IMAGE PREVIEW
        |--------------------------------------------------------------------------
        */

        .core-preview {

            display: none;

            margin-top: 15px;

            position: relative;

            border-radius: 12px;

            overflow: hidden;

            background: #f1f5f9;
        }

        .core-preview img {

            width: 100%;

            max-height: 300px;

            object-fit: cover;

            display: block;
        }

        .core-preview-name {

            padding: 9px 12px;

            background: #fff;

            border-top:
                1px solid #e2e8f0;

            color: #64748b;

            font-size: 12px;
        }

        /*
        |--------------------------------------------------------------------------
        | STATUS CARD
        |--------------------------------------------------------------------------
        */

        .core-status-box {

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 15px;

            padding: 15px;

            border-radius: 11px;

            background: #f8fafc;

            border:
                1px solid #e7edf4;
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

            margin-bottom: 3px;
        }

        .core-status-info span {

            display: block;

            color: #94a3b8;

            font-size: 11px;
        }

        /*
        |--------------------------------------------------------------------------
        | SWITCH
        |--------------------------------------------------------------------------
        */

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

            background: #cbd5e1;

            border-radius: 30px;

            cursor: pointer;

            transition: .25s;
        }

        .core-slider:before {

            content: "";

            position: absolute;

            width: 20px;
            height: 20px;

            left: 3px;
            top: 3px;

            background: #fff;

            border-radius: 50%;

            transition: .25s;

            box-shadow:
                0 2px 5px rgba(0, 0, 0, .15);
        }

        .core-switch input:checked+.core-slider {

            background: #16a34a;
        }

        .core-switch input:checked+.core-slider:before {

            transform:
                translateX(22px);
        }

        /*
        |--------------------------------------------------------------------------
        | SIDE INFO
        |--------------------------------------------------------------------------
        */

        .core-side-card {

            padding: 25px;
        }

        .core-side-icon {

            width: 48px;
            height: 48px;

            border-radius: 12px;

            display: flex;

            align-items: center;
            justify-content: center;

            background:
                linear-gradient(135deg,
                    #fff1f2,
                    #fff7ed);

            color: #ef4444;

            font-size: 19px;

            margin-bottom: 15px;
        }

        .core-side-card h3 {

            margin: 0 0 8px;

            font-size: 16px;
        }

        .core-side-card p {

            margin: 0;

            color: #7b8798;

            font-size: 13px;

            line-height: 1.6;
        }

        .core-info-list {

            margin-top: 20px;

            display: flex;

            flex-direction: column;

            gap: 12px;
        }

        .core-info-item {

            display: flex;

            align-items: center;

            gap: 10px;

            color: #64748b;

            font-size: 13px;
        }

        .core-info-item i {

            color: #0d6efd;

            width: 18px;

            text-align: center;
        }

        /*
        |--------------------------------------------------------------------------
        | ACTIONS
        |--------------------------------------------------------------------------
        */

        .core-form-actions {

            display: flex;

            justify-content: flex-end;

            align-items: center;

            gap: 10px;

            margin-top: 25px;

            padding-top: 20px;

            border-top:
                1px solid #edf1f6;
        }

        .core-btn {

            height: 43px;

            padding: 0 19px;

            border-radius: 9px;

            border: 0;

            display: inline-flex;

            align-items: center;

            justify-content: center;

            gap: 8px;

            font-size: 13px;

            font-weight: 700;

            cursor: pointer;

            text-decoration: none;

            transition: .2s ease;
        }

        .core-btn-cancel {

            background: #f1f5f9;

            color: #475569;
        }

        .core-btn-cancel:hover {

            background: #e2e8f0;
        }

        .core-btn-save {

            color: #fff;

            background:
                linear-gradient(135deg,
                    #0d6efd,
                    #635bff);

            box-shadow:
                0 7px 18px rgba(13, 110, 253, .20);
        }

        .core-btn-save:hover {

            transform: translateY(-1px);

            box-shadow:
                0 10px 22px rgba(13, 110, 253, .27);
        }

        /*
        |--------------------------------------------------------------------------
        | RESPONSIVE
        |--------------------------------------------------------------------------
        */

        @media (max-width: 1000px) {

            .core-event-layout {

                grid-template-columns: 1fr;
            }

        }

        @media (max-width: 700px) {

            .core-admin-page {

                padding: 18px;
            }

            .core-page-header {

                align-items: flex-start;

                flex-direction: column;
            }

            .core-form-row {

                grid-template-columns: 1fr;
            }

            .core-card-body,
            .core-card-header {

                padding: 20px;
            }

            .core-form-actions {

                flex-direction: column-reverse;
            }

            .core-btn {

                width: 100%;
            }

        }
    </style>

</head>


<body>


    <div class="core-admin-page">

        <div class="core-admin-container">


            <!-- =====================================================
             PAGE HEADER
        ====================================================== -->

            <div class="core-page-header">

                <div class="core-page-heading">

                    <div class="core-page-icon">

                        <i class="fa-solid fa-calendar-plus"></i>

                    </div>

                    <div>

                        <h1>
                            Add Event
                        </h1>

                        <p>
                            Create and publish a new CORE LTD event.
                        </p>

                    </div>

                </div>


                <a href="events.php" class="core-back-btn">

                    <i class="fa-solid fa-arrow-left"></i>

                    Back to Events

                </a>

            </div>


            <!-- =====================================================
             ERROR
        ====================================================== -->

            <?php if ($error !== ''): ?>

                <div class="core-error">

                    <i class="fa-solid fa-circle-exclamation"></i>

                    <span>
                        <?= htmlspecialchars($error); ?>
                    </span>

                </div>

            <?php endif; ?>


            <!-- =====================================================
             FORM LAYOUT
        ====================================================== -->

            <div class="core-event-layout">


                <!-- =================================================
                 MAIN FORM
            ================================================== -->

                <div class="core-card">


                    <div class="core-card-header">

                        <h2>
                            Event Information
                        </h2>

                        <p>
                            Enter the details that will appear on the website.
                        </p>

                    </div>


                    <div class="core-card-body">


                        <form method="POST" enctype="multipart/form-data">


                            <!-- EVENT TITLE -->

                            <div class="core-form-group">

                                <label class="core-form-label">

                                    Event Title

                                    <span class="core-required">
                                        *
                                    </span>

                                </label>

                                <input type="text" name="title" class="core-input" required
                                    value="<?= htmlspecialchars($_POST['title'] ?? ''); ?>"
                                    placeholder="e.g. Ruijie Certified Training">

                            </div>


                            <!-- DATE + TIME -->

                            <div class="core-form-row">


                                <div class="core-form-group">

                                    <label class="core-form-label">

                                        Event Date

                                    </label>

                                    <input type="date" name="event_date" class="core-input"
                                        value="<?= htmlspecialchars($_POST['event_date'] ?? ''); ?>">

                                </div>


                                <div class="core-form-group">

                                    <label class="core-form-label">

                                        Event Time

                                    </label>

                                    <input type="text" name="event_time" class="core-input"
                                        value="<?= htmlspecialchars($_POST['event_time'] ?? ''); ?>"
                                        placeholder="e.g. 6:00 PM">

                                </div>


                            </div>


                            <!-- VENUE -->

                            <div class="core-form-group">

                                <label class="core-form-label">

                                    Venue

                                </label>

                                <input type="text" name="venue" class="core-input"
                                    value="<?= htmlspecialchars($_POST['venue'] ?? ''); ?>"
                                    placeholder="e.g. PCEA Githurai Progressive School Hall">

                            </div>


                            <!-- DESCRIPTION -->

                            <div class="core-form-group">

                                <label class="core-form-label">

                                    Event Description

                                </label>

                                <textarea name="description" class="core-textarea"
                                    placeholder="Enter event details..."><?= htmlspecialchars($_POST['description'] ?? ''); ?></textarea>

                            </div>


                            <!-- STATUS -->

                            <div class="core-form-group">

                                <label class="core-form-label">

                                    Publishing Status

                                </label>


                                <div class="core-status-box">


                                    <div class="core-status-info">

                                        <div class="core-status-icon">

                                            <i class="fa-solid fa-eye"></i>

                                        </div>

                                        <div>

                                            <strong>
                                                Active Event
                                            </strong>

                                            <span>
                                                Show this event on the website
                                            </span>

                                        </div>

                                    </div>


                                    <label class="core-switch">

                                        <input type="checkbox" name="status" value="1" checked>

                                        <span class="core-slider"></span>

                                    </label>


                                </div>

                            </div>


                            <!-- ACTIONS -->

                            <div class="core-form-actions">


                                <a href="events.php" class="core-btn core-btn-cancel">

                                    <i class="fa-solid fa-xmark"></i>

                                    Cancel

                                </a>


                                <button type="submit" class="core-btn core-btn-save">

                                    <i class="fa-solid fa-floppy-disk"></i>

                                    Save Event

                                </button>


                            </div>


                        </form>


                    </div>

                </div>


                <!-- =================================================
                 RIGHT SIDEBAR
            ================================================== -->

                <div>


                    <!-- POSTER -->

                    <div class="core-card">

                        <div class="core-card-header">

                            <h2>
                                Event Poster
                            </h2>

                            <p>
                                Upload the event artwork.
                            </p>

                        </div>


                        <div class="core-card-body">


                            <div class="core-upload-area" id="uploadArea">

                                <div class="core-upload-icon">

                                    <i class="fa-solid fa-cloud-arrow-up"></i>

                                </div>


                                <h3>
                                    Upload Poster
                                </h3>


                                <p>
                                    JPG, PNG or WEBP
                                </p>


                                <input type="file" name="image" id="imageInput" form="eventForm"
                                    accept="image/jpeg,image/png,image/webp">

                            </div>


                            <div class="core-preview" id="imagePreview">

                                <img src="" id="previewImage" alt="Event poster preview">

                                <div class="core-preview-name" id="previewName"></div>

                            </div>


                        </div>

                    </div>


                    <!-- EVENT TIPS -->

                    <div class="core-card" style="margin-top:25px;">

                        <div class="core-side-card">


                            <div class="core-side-icon">

                                <i class="fa-solid fa-lightbulb"></i>

                            </div>


                            <h3>
                                Event Tips
                            </h3>


                            <p>
                                Use a clear event poster and provide the correct venue and event details.
                            </p>


                            <div class="core-info-list">


                                <div class="core-info-item">

                                    <i class="fa-solid fa-image"></i>

                                    <span>
                                        Use a clear poster
                                    </span>

                                </div>


                                <div class="core-info-item">

                                    <i class="fa-solid fa-location-dot"></i>

                                    <span>
                                        Enter the correct venue
                                    </span>

                                </div>


                                <div class="core-info-item">

                                    <i class="fa-solid fa-calendar"></i>

                                    <span>
                                        Confirm event date
                                    </span>

                                </div>


                                <div class="core-info-item">

                                    <i class="fa-solid fa-eye"></i>

                                    <span>
                                        Activate when ready
                                    </span>

                                </div>


                            </div>


                        </div>

                    </div>


                </div>


            </div>


        </div>

    </div>


    <script>
        /*
|--------------------------------------------------------------------------
| CONNECT FILE INPUT TO FORM
|--------------------------------------------------------------------------
|
| The form is located in the main card.
| We give it an ID so the upload input can remain in
| the right-hand card.
|
*/

        const form = document.querySelector('form');

        if (form) {
            form.id = 'eventForm';
        }


        /*
        |--------------------------------------------------------------------------
        | IMAGE PREVIEW
        |--------------------------------------------------------------------------
        */

        const imageInput =
            document.getElementById('imageInput');

        const imagePreview =
            document.getElementById('imagePreview');

        const previewImage =
            document.getElementById('previewImage');

        const previewName =
            document.getElementById('previewName');


        if (imageInput) {

            imageInput.addEventListener(
                'change',
                function() {

                    const file = this.files[0];

                    if (!file) {

                        imagePreview.style.display = 'none';

                        return;
                    }


                    const allowed = [
                        'image/jpeg',
                        'image/png',
                        'image/webp'
                    ];


                    if (!allowed.includes(file.type)) {

                        alert(
                            'Only JPG, PNG and WEBP images are allowed.'
                        );

                        this.value = '';

                        imagePreview.style.display = 'none';

                        return;
                    }


                    const reader =
                        new FileReader();


                    reader.onload = function(event) {

                        previewImage.src =
                            event.target.result;

                        previewName.textContent =
                            file.name;

                        imagePreview.style.display =
                            'block';

                    };


                    reader.readAsDataURL(file);

                }
            );

        }
    </script>


</body>

</html>