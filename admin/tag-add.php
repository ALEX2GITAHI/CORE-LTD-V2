<?php

session_start();

require_once __DIR__ . '/../includes/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$name = '';
$slug = '';
$error = '';

/* =========================================================
   CREATE SLUG
========================================================= */

function createSlug($text)
{
    $text = strtolower(trim($text));

    $text = preg_replace('/[^a-z0-9]+/', '-', $text);

    $text = trim($text, '-');

    return $text;
}


/* =========================================================
   FORM SUBMISSION
========================================================= */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name'] ?? '');

    if ($name === '') {

        $error = 'Please enter a tag name.';
    } else {

        $slug = createSlug($name);

        if ($slug === '') {

            $error = 'The tag name cannot be converted into a valid slug.';
        } else {

            /* =================================================
               CHECK DUPLICATE NAME
            ================================================= */

            $check = $conn->prepare("
                SELECT id
                FROM tags
                WHERE name = ?
                LIMIT 1
            ");

            if (!$check) {

                $error = 'Database error: ' . $conn->error;
            } else {

                $check->bind_param(
                    "s",
                    $name
                );

                $check->execute();

                $checkResult = $check->get_result();

                $exists = $checkResult->num_rows > 0;

                $check->close();


                if ($exists) {

                    $error = 'A tag with this name already exists.';
                } else {

                    /* =========================================
                       CHECK DUPLICATE SLUG
                    ========================================= */

                    $checkSlug = $conn->prepare("
                        SELECT id
                        FROM tags
                        WHERE slug = ?
                        LIMIT 1
                    ");

                    if (!$checkSlug) {

                        $error =
                            'Database error: '
                            . $conn->error;
                    } else {

                        $checkSlug->bind_param(
                            "s",
                            $slug
                        );

                        $checkSlug->execute();

                        $slugResult =
                            $checkSlug->get_result();

                        $slugExists =
                            $slugResult->num_rows > 0;

                        $checkSlug->close();


                        if ($slugExists) {

                            $error =
                                'A tag with this slug already exists.';
                        } else {

                            /* =================================
                               INSERT TAG
                            ================================= */

                            $insert = $conn->prepare("
                                INSERT INTO tags
                                (
                                    name,
                                    slug
                                )
                                VALUES
                                (
                                    ?,
                                    ?
                                )
                            ");

                            if (!$insert) {

                                $error =
                                    'Database error: '
                                    . $conn->error;
                            } else {

                                $insert->bind_param(
                                    "ss",
                                    $name,
                                    $slug
                                );


                                if ($insert->execute()) {

                                    $insert->close();

                                    header(
                                        "Location: tags.php?success=added"
                                    );

                                    exit;
                                } else {

                                    $error =
                                        'Unable to create the tag. '
                                        . $insert->error;

                                    $insert->close();
                                }
                            }
                        }
                    }
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
        Add Tag | CORE LTD Admin
    </title>


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">


    <style>
    * {
        box-sizing: border-box;
    }


    body {

        margin: 0;

        font-family:
            "Segoe UI",
            Arial,
            sans-serif;

        background: #f4f7fb;

        color: #26364a;
    }


    /* =====================================================
           SIDEBAR
        ===================================================== */

    .sidebar {

        position: fixed;

        top: 0;
        left: 0;

        width: 245px;

        height: 100vh;

        background:
            linear-gradient(180deg,
                #073b75 0%,
                #0b5cad 55%,
                #0878d1 100%);

        color: white;

        padding: 25px 15px;

        box-shadow:
            4px 0 18px rgba(0, 61, 120, .12);

        z-index: 1000;
    }


    .brand {

        text-align: center;

        padding:
            5px 10px 25px;

        border-bottom:
            1px solid rgba(255, 255, 255, .15);

        margin-bottom: 20px;
    }


    .brand-icon {

        width: 55px;
        height: 55px;

        margin:
            0 auto 10px;

        border-radius: 15px;

        background:
            rgba(255, 255, 255, .15);

        display: flex;

        align-items: center;
        justify-content: center;

        font-size: 25px;
    }


    .brand h2 {

        font-size: 20px;

        font-weight: 800;

        margin: 0;
    }


    .brand span {

        display: block;

        margin-top: 4px;

        font-size: 11px;

        opacity: .75;

        letter-spacing: 1px;
    }


    .nav-title {

        font-size: 10px;

        text-transform: uppercase;

        letter-spacing: 1.5px;

        opacity: .6;

        margin:
            20px 10px 8px;
    }


    .sidebar a {

        display: flex;

        align-items: center;

        gap: 12px;

        color:
            rgba(255, 255, 255, .85);

        text-decoration: none;

        padding:
            11px 13px;

        border-radius: 9px;

        margin-bottom: 5px;

        font-size: 14px;

        transition: .2s;
    }


    .sidebar a i {

        width: 20px;

        text-align: center;
    }


    .sidebar a:hover {

        background:
            rgba(255, 255, 255, .12);

        color: white;
    }


    .sidebar a.active {

        background: white;

        color: #07549a;

        font-weight: 700;
    }


    /* =====================================================
           MAIN
        ===================================================== */

    .main {

        margin-left: 245px;

        min-height: 100vh;

        padding: 28px;
    }


    /* =====================================================
           TOPBAR
        ===================================================== */

    .topbar {

        background: white;

        border-radius: 14px;

        padding:
            18px 22px;

        display: flex;

        align-items: center;

        justify-content: space-between;

        margin-bottom: 25px;

        box-shadow:
            0 5px 25px rgba(25, 65, 100, .06);
    }


    .page-title {

        margin: 0;

        font-size: 25px;

        font-weight: 800;

        color: #17365d;
    }


    .page-subtitle {

        margin:
            4px 0 0;

        color: #7a8998;

        font-size: 13px;
    }


    /* =====================================================
           FORM CARD
        ===================================================== */

    .form-card {

        max-width: 760px;

        margin:
            30px auto;

        background: white;

        border-radius: 17px;

        box-shadow:
            0 8px 30px rgba(25, 65, 100, .07);

        overflow: hidden;
    }


    .form-header {

        padding:
            22px 25px;

        border-bottom:
            1px solid #edf1f5;

        background:
            linear-gradient(135deg,
                #f8fbff,
                #ffffff);
    }


    .form-header h2 {

        margin: 0;

        font-size: 18px;

        color: #17365d;

        font-weight: 800;
    }


    .form-header p {

        margin:
            5px 0 0;

        font-size: 12px;

        color: #8493a3;
    }


    .form-body {

        padding: 28px 25px;
    }


    .form-group {

        margin-bottom: 22px;
    }


    .form-label {

        display: block;

        margin-bottom: 8px;

        font-size: 12px;

        color: #344f6c;

        font-weight: 750;
    }


    .required {

        color: #dc3545;
    }


    .form-control-custom {

        width: 100%;

        border:
            1px solid #dbe4ed;

        border-radius: 9px;

        padding:
            12px 13px;

        outline: none;

        font-size: 13px;

        color: #26364a;

        transition: .2s;

        background: #fff;
    }


    .form-control-custom:focus {

        border-color: #0d6efd;

        box-shadow:
            0 0 0 3px rgba(13, 110, 253, .08);
    }


    .slug-preview {

        margin-top: 8px;

        padding:
            9px 11px;

        border-radius: 7px;

        background: #f5f8fb;

        border:
            1px solid #e5ebf1;

        font-family:
            Consolas,
            monospace;

        font-size: 11px;

        color: #6f8194;
    }


    .slug-preview strong {

        color: #0d6efd;
    }


    .info-box {

        background: #edf6ff;

        border:
            1px solid #d5e9ff;

        border-radius: 9px;

        padding:
            13px 15px;

        color: #28669e;

        font-size: 11px;

        line-height: 1.6;

        margin-bottom: 25px;
    }


    .info-box i {

        margin-right: 6px;
    }


    /* =====================================================
           BUTTONS
        ===================================================== */

    .form-actions {

        display: flex;

        justify-content: flex-end;

        gap: 10px;

        padding-top: 8px;
    }


    .btn-custom {

        display: inline-flex;

        align-items: center;

        justify-content: center;

        gap: 7px;

        border: none;

        border-radius: 8px;

        padding:
            11px 17px;

        font-size: 12px;

        font-weight: 700;

        text-decoration: none;

        cursor: pointer;

        transition: .2s;
    }


    .btn-cancel {

        background: #eef2f6;

        color: #52677d;
    }


    .btn-cancel:hover {

        background: #e2e7ec;

        color: #40566c;
    }


    .btn-save {

        background:
            linear-gradient(135deg,
                #0d6efd,
                #0754b8);

        color: white;

        box-shadow:
            0 4px 12px rgba(13, 110, 253, .18);
    }


    .btn-save:hover {

        color: white;

        transform:
            translateY(-1px);

        box-shadow:
            0 6px 16px rgba(13, 110, 253, .25);
    }


    /* =====================================================
           ERROR
        ===================================================== */

    .error-box {

        background: #fff0f1;

        border:
            1px solid #ffd2d7;

        color: #b42332;

        border-radius: 9px;

        padding:
            12px 14px;

        font-size: 12px;

        margin-bottom: 20px;

        display: flex;

        align-items: center;

        gap: 9px;
    }


    /* =====================================================
           RESPONSIVE
        ===================================================== */

    @media(max-width:900px) {

        .sidebar {

            width: 70px;

            padding:
                20px 8px;
        }


        .brand h2,
        .brand span,
        .sidebar a span,
        .nav-title {

            display: none;
        }


        .brand {

            border: none;
        }


        .sidebar a {

            justify-content: center;
        }


        .sidebar a i {

            width: auto;
        }


        .main {

            margin-left: 70px;

            padding: 18px;
        }

    }


    @media(max-width:600px) {

        .topbar {

            flex-direction: column;

            align-items: flex-start;

            gap: 10px;
        }


        .form-body {

            padding:
                22px 18px;
        }


        .form-actions {

            flex-direction: column-reverse;
        }


        .btn-custom {

            width: 100%;
        }

    }
    </style>

</head>


<body>


    <!-- =========================================================
     SIDEBAR
========================================================= -->

    <aside class="sidebar">


        <div class="brand">

            <div class="brand-icon">

                <i class="fa-solid fa-network-wired"></i>

            </div>


            <h2>
                CORE LTD
            </h2>


            <span>
                ADMIN PANEL
            </span>

        </div>


        <div class="nav-title">
            Main
        </div>


        <a href="index.php">

            <i class="fa-solid fa-gauge"></i>

            <span>
                Dashboard
            </span>

        </a>


        <a href="products.php">

            <i class="fa-solid fa-box"></i>

            <span>
                Products
            </span>

        </a>


        <a href="categories.php">

            <i class="fa-solid fa-layer-group"></i>

            <span>
                Categories
            </span>

        </a>


        <a href="brands.php">

            <i class="fa-solid fa-tags"></i>

            <span>
                Brands
            </span>

        </a>


        <a href="tags.php" class="active">

            <i class="fa-solid fa-tag"></i>

            <span>
                Tags
            </span>

        </a>


        <div class="nav-title">
            Sales
        </div>


        <a href="orders.php">

            <i class="fa-solid fa-cart-shopping"></i>

            <span>
                Orders
            </span>

        </a>


        <a href="customers.php">

            <i class="fa-solid fa-users"></i>

            <span>
                Customers
            </span>

        </a>


        <div class="nav-title">
            Communication
        </div>


        <a href="inquiries.php">

            <i class="fa-solid fa-envelope"></i>

            <span>
                Inquiries
            </span>

        </a>


        <a href="reviews.php">

            <i class="fa-solid fa-star"></i>

            <span>
                Reviews
            </span>

        </a>


        <div class="nav-title">
            System
        </div>


        <a href="settings.php">

            <i class="fa-solid fa-gear"></i>

            <span>
                Settings
            </span>

        </a>


        <a href="logout.php">

            <i class="fa-solid fa-right-from-bracket"></i>

            <span>
                Logout
            </span>

        </a>


    </aside>



    <!-- =========================================================
     MAIN
========================================================= -->

    <main class="main">


        <!-- TOPBAR -->

        <div class="topbar">


            <div>

                <h1 class="page-title">
                    Add Tag
                </h1>


                <p class="page-subtitle">

                    Create a tag that can be assigned to your products.

                </p>

            </div>


        </div>



        <!-- FORM -->

        <div class="form-card">


            <div class="form-header">

                <h2>

                    <i class="fa-solid fa-tag" style="color:#0d6efd;"></i>

                    New Product Tag

                </h2>


                <p>

                    Tags help customers find related products more easily.

                </p>

            </div>



            <div class="form-body">


                <?php if ($error !== ''): ?>

                <div class="error-box">

                    <i class="fa-solid fa-circle-exclamation"></i>

                    <?= htmlspecialchars($error) ?>

                </div>

                <?php endif; ?>



                <div class="info-box">

                    <i class="fa-solid fa-circle-info"></i>

                    Enter the tag name below.
                    The system will automatically create the
                    SEO-friendly slug.

                </div>



                <form method="POST" action="" autocomplete="off">


                    <!-- TAG NAME -->

                    <div class="form-group">


                        <label class="form-label" for="name">

                            Tag Name

                            <span class="required">
                                *
                            </span>

                        </label>


                        <input type="text" id="name" name="name" class="form-control-custom"
                            placeholder="e.g. WiFi Router" value="<?= htmlspecialchars($name) ?>" required autofocus>


                        <div class="slug-preview">

                            Slug:

                            <strong id="slugPreview">

                                <?= htmlspecialchars(
                                    $slug !== ''
                                        ? $slug
                                        : 'wifi-router'
                                ) ?>

                            </strong>

                        </div>


                    </div>



                    <!-- ACTIONS -->

                    <div class="form-actions">


                        <a href="tags.php" class="btn-custom btn-cancel">

                            <i class="fa-solid fa-arrow-left"></i>

                            Cancel

                        </a>


                        <button type="submit" class="btn-custom btn-save">

                            <i class="fa-solid fa-plus"></i>

                            Create Tag

                        </button>


                    </div>


                </form>


            </div>


        </div>


    </main>



    <script>
    const nameInput =
        document.getElementById('name');

    const slugPreview =
        document.getElementById('slugPreview');


    function createSlug(text) {

        return text
            .toLowerCase()
            .trim()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');

    }


    nameInput.addEventListener(
        'input',
        function() {

            const slug =
                createSlug(this.value);

            slugPreview.textContent =
                slug || 'your-tag-slug';

        }
    );
    </script>


</body>

</html>