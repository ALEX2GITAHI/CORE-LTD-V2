<?php

session_start();

require_once __DIR__ . '/../includes/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}


/* =========================================================
   SEARCH
========================================================= */

$search = trim($_GET['search'] ?? '');


/* =========================================================
   FETCH TAGS
========================================================= */

/* =========================================================
   FETCH TAGS
========================================================= */

if ($search !== '') {

    $stmt = $conn->prepare("
        SELECT
            t.id,
            t.name,
            t.slug,
            t.created_at,
            COUNT(pt.product_id) AS product_count
        FROM tags t
        LEFT JOIN product_tags pt
            ON pt.tag_id = t.id
        WHERE
            t.name LIKE ?
            OR t.slug LIKE ?
        GROUP BY
            t.id,
            t.name,
            t.slug,
            t.created_at
        ORDER BY
            t.id DESC
    ");

    $like = '%' . $search . '%';

    $stmt->bind_param(
        "ss",
        $like,
        $like
    );
} else {

    $stmt = $conn->prepare("
        SELECT
            t.id,
            t.name,
            t.slug,
            t.created_at,
            COUNT(pt.product_id) AS product_count
        FROM tags t
        LEFT JOIN product_tags pt
            ON pt.tag_id = t.id
        GROUP BY
            t.id,
            t.name,
            t.slug,
            t.created_at
        ORDER BY
            t.id DESC
    ");
}


if (!$stmt) {

    die("Database error: "
        . $conn->error);
}


$stmt->execute();

$result = $stmt->get_result();

$tags = [];

while ($row = $result->fetch_assoc()) {

    $tags[] = $row;
}

$stmt->close();

/* =========================================================
   COUNTS
========================================================= */

$totalTags = count($tags);

$usedTags = 0;

foreach ($tags as $tag) {

    if ((int)$tag['product_count'] > 0) {
        $usedTags++;
    }
}

$unusedTags = $totalTags - $usedTags;


/* =========================================================
   SUCCESS / ERROR MESSAGES
========================================================= */

$success = $_GET['success'] ?? '';
$error = $_GET['error'] ?? '';

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        Tags | CORE LTD Admin
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


        .admin-user {

            display: flex;

            align-items: center;

            gap: 10px;
        }


        .admin-avatar {

            width: 42px;
            height: 42px;

            border-radius: 50%;

            background:
                linear-gradient(135deg,
                    #0d6efd,
                    #0a58ca);

            color: white;

            display: flex;

            align-items: center;
            justify-content: center;

            font-weight: 700;
        }


        /* =====================================================
           STAT CARDS
        ===================================================== */

        .stats {

            display: grid;

            grid-template-columns:
                repeat(3, 1fr);

            gap: 18px;

            margin-bottom: 22px;
        }


        .stat-card {

            background: white;

            border-radius: 14px;

            padding: 19px;

            box-shadow:
                0 5px 25px rgba(25, 65, 100, .05);

            display: flex;

            align-items: center;

            gap: 14px;
        }


        .stat-icon {

            width: 46px;
            height: 46px;

            border-radius: 12px;

            display: flex;

            align-items: center;
            justify-content: center;

            font-size: 19px;
        }


        .stat-icon.blue {

            background: #e8f2ff;

            color: #0d6efd;
        }


        .stat-icon.green {

            background: #eaf9f0;

            color: #198754;
        }


        .stat-icon.orange {

            background: #fff4df;

            color: #f08c00;
        }


        .stat-label {

            color: #8795a5;

            font-size: 11px;

            margin-bottom: 2px;
        }


        .stat-number {

            color: #17365d;

            font-size: 21px;

            font-weight: 800;
        }


        /* =====================================================
           CONTENT CARD
        ===================================================== */

        .content-card {

            background: white;

            border-radius: 15px;

            box-shadow:
                0 5px 25px rgba(25, 65, 100, .06);

            overflow: hidden;
        }


        .content-header {

            padding:
                19px 22px;

            border-bottom:
                1px solid #edf1f5;

            display: flex;

            justify-content: space-between;

            align-items: center;

            gap: 15px;
        }


        .content-title {

            margin: 0;

            color: #17365d;

            font-size: 17px;

            font-weight: 750;
        }


        .content-title span {

            color: #8997a6;

            font-size: 11px;

            font-weight: 500;

            margin-left: 5px;
        }


        /* =====================================================
           SEARCH
        ===================================================== */

        .search-box {

            position: relative;

            width: 280px;
        }


        .search-box i {

            position: absolute;

            left: 12px;

            top: 50%;

            transform:
                translateY(-50%);

            color: #8b9aaa;

            font-size: 12px;
        }


        .search-box input {

            width: 100%;

            border:
                1px solid #dce5ee;

            border-radius: 8px;

            padding:
                9px 12px 9px 34px;

            outline: none;

            font-size: 12px;
        }


        .search-box input:focus {

            border-color: #0d6efd;

            box-shadow:
                0 0 0 3px rgba(13, 110, 253, .08);
        }


        /* =====================================================
           BUTTON
        ===================================================== */

        .btn-add {

            display: inline-flex;

            align-items: center;

            gap: 7px;

            background:
                linear-gradient(135deg,
                    #0d6efd,
                    #0754b8);

            color: white;

            text-decoration: none;

            border-radius: 8px;

            padding:
                10px 14px;

            font-size: 12px;

            font-weight: 700;

            border: none;
        }


        .btn-add:hover {

            color: white;

            transform:
                translateY(-1px);
        }


        /* =====================================================
           TABLE
        ===================================================== */

        .table-wrap {

            overflow-x: auto;
        }


        table {

            width: 100%;

            border-collapse: collapse;
        }


        th {

            background: #f8fafc;

            color: #758699;

            font-size: 10px;

            text-transform: uppercase;

            letter-spacing: .7px;

            font-weight: 750;

            padding:
                13px 18px;

            text-align: left;

            border-bottom:
                1px solid #e9eef3;

            white-space: nowrap;
        }


        td {

            padding:
                14px 18px;

            border-bottom:
                1px solid #edf1f5;

            font-size: 12px;

            color: #53677c;

            vertical-align: middle;
        }


        tbody tr {

            transition: .15s;
        }


        tbody tr:hover {

            background: #f8fbff;
        }


        .tag-id {

            color: #94a1af;

            font-size: 11px;

            font-weight: 600;
        }


        .tag-name {

            display: flex;

            align-items: center;

            gap: 9px;

            color: #234365;

            font-weight: 700;
        }


        .tag-icon {

            width: 31px;
            height: 31px;

            border-radius: 8px;

            background: #eaf3ff;

            color: #0d6efd;

            display: flex;

            align-items: center;
            justify-content: center;

            font-size: 12px;
        }


        .slug {

            font-family:
                Consolas,
                monospace;

            font-size: 11px;

            color: #78899b;

            background: #f5f7fa;

            padding:
                4px 7px;

            border-radius: 5px;
        }


        .product-count {

            display: inline-flex;

            align-items: center;

            gap: 5px;

            background: #edf6ff;

            color: #0b68c7;

            border-radius: 20px;

            padding:
                5px 9px;

            font-size: 10px;

            font-weight: 700;
        }


        .product-count.unused {

            background: #f3f5f7;

            color: #8795a5;
        }


        .date {

            color: #8795a5;

            font-size: 10px;
        }


        /* =====================================================
           ACTIONS
        ===================================================== */

        .actions {

            display: flex;

            gap: 6px;
        }


        .action-btn {

            width: 31px;
            height: 31px;

            border-radius: 7px;

            display: flex;

            align-items: center;
            justify-content: center;

            text-decoration: none;

            font-size: 11px;

            transition: .15s;
        }


        .action-edit {

            background: #eaf3ff;

            color: #0d6efd;
        }


        .action-edit:hover {

            background: #0d6efd;

            color: white;
        }


        .action-delete {

            background: #fff0f1;

            color: #dc3545;
        }


        .action-delete:hover {

            background: #dc3545;

            color: white;
        }


        /* =====================================================
           EMPTY
        ===================================================== */

        .empty-state {

            text-align: center;

            padding:
                65px 20px;
        }


        .empty-icon {

            width: 65px;
            height: 65px;

            margin:
                0 auto 15px;

            border-radius: 16px;

            background: #edf5ff;

            color: #0d6efd;

            display: flex;

            align-items: center;
            justify-content: center;

            font-size: 25px;
        }


        .empty-state h3 {

            color: #314b67;

            font-size: 16px;

            margin-bottom: 5px;
        }


        .empty-state p {

            color: #8997a6;

            font-size: 12px;

            margin-bottom: 18px;
        }


        /* =====================================================
           ALERT
        ===================================================== */

        .alert-message {

            border-radius: 9px;

            padding:
                12px 15px;

            margin-bottom: 20px;

            font-size: 12px;

            display: flex;

            align-items: center;

            gap: 9px;
        }


        .alert-success {

            background: #eaf9f0;

            border:
                1px solid #ccebd8;

            color: #167344;
        }


        .alert-error {

            background: #fff0f1;

            border:
                1px solid #ffd3d7;

            color: #b42332;
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


            .stats {

                grid-template-columns:
                    1fr 1fr;
            }

        }


        @media(max-width:650px) {

            .topbar {

                flex-direction: column;

                align-items: flex-start;

                gap: 12px;
            }


            .stats {

                grid-template-columns: 1fr;
            }


            .content-header {

                flex-direction: column;

                align-items: stretch;
            }


            .search-box {

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
                    Tags
                </h1>


                <p class="page-subtitle">

                    Organize products using searchable tags.

                </p>

            </div>


            <div class="admin-user">


                <div class="admin-avatar">

                    <?= htmlspecialchars(
                        strtoupper(
                            substr(
                                $_SESSION['admin_name']
                                    ?? 'A',
                                0,
                                1
                            )
                        )
                    ) ?>

                </div>


                <div>

                    <strong style="font-size:13px;">

                        <?= htmlspecialchars(
                            $_SESSION['admin_name']
                                ?? 'Administrator'
                        ) ?>

                    </strong>


                    <div style="
                        font-size:11px;
                        color:#8a98a8;
                    ">

                        Administrator

                    </div>

                </div>


            </div>


        </div>



        <!-- ALERTS -->

        <?php if ($success === 'added'): ?>

            <div class="alert-message alert-success">

                <i class="fa-solid fa-circle-check"></i>

                Tag added successfully.

            </div>

        <?php endif; ?>


        <?php if ($success === 'updated'): ?>

            <div class="alert-message alert-success">

                <i class="fa-solid fa-circle-check"></i>

                Tag updated successfully.

            </div>

        <?php endif; ?>


        <?php if ($success === 'deleted'): ?>

            <div class="alert-message alert-success">

                <i class="fa-solid fa-circle-check"></i>

                Tag deleted successfully.

            </div>

        <?php endif; ?>


        <?php if ($error === 'tag_has_products'): ?>

            <div class="alert-message alert-error">

                <i class="fa-solid fa-triangle-exclamation"></i>

                This tag cannot be deleted because it is being used by products.

            </div>

        <?php endif; ?>


        <?php if ($error === 'delete_failed'): ?>

            <div class="alert-message alert-error">

                <i class="fa-solid fa-circle-xmark"></i>

                The tag could not be deleted.

            </div>

        <?php endif; ?>



        <!-- STATS -->

        <div class="stats">


            <div class="stat-card">

                <div class="stat-icon blue">

                    <i class="fa-solid fa-tags"></i>

                </div>


                <div>

                    <div class="stat-label">
                        Total Tags
                    </div>

                    <div class="stat-number">
                        <?= $totalTags ?>
                    </div>

                </div>

            </div>



            <div class="stat-card">

                <div class="stat-icon green">

                    <i class="fa-solid fa-link"></i>

                </div>


                <div>

                    <div class="stat-label">
                        Used Tags
                    </div>

                    <div class="stat-number">
                        <?= $usedTags ?>
                    </div>

                </div>

            </div>



            <div class="stat-card">

                <div class="stat-icon orange">

                    <i class="fa-solid fa-tag"></i>

                </div>


                <div>

                    <div class="stat-label">
                        Unused Tags
                    </div>

                    <div class="stat-number">
                        <?= $unusedTags ?>
                    </div>

                </div>

            </div>


        </div>



        <!-- TAGS TABLE -->

        <div class="content-card">


            <div class="content-header">


                <div>

                    <h2 class="content-title">

                        All Tags

                        <span>
                            <?= $totalTags ?> total
                        </span>

                    </h2>

                </div>


                <div style="
                    display:flex;
                    gap:10px;
                    align-items:center;
                ">


                    <form method="GET" action="tags.php" style="margin:0;">

                        <div class="search-box">

                            <i class="fa-solid fa-search"></i>

                            <input type="text" name="search" placeholder="Search tags..."
                                value="<?= htmlspecialchars($search) ?>">

                        </div>

                    </form>


                    <a href="tag-add.php" class="btn-add">

                        <i class="fa-solid fa-plus"></i>

                        Add Tag

                    </a>


                </div>


            </div>



            <?php if (empty($tags)): ?>


                <div class="empty-state">


                    <div class="empty-icon">

                        <i class="fa-solid fa-tags"></i>

                    </div>


                    <h3>

                        <?= $search !== ''
                            ? 'No tags found'
                            : 'No tags yet'
                        ?>

                    </h3>


                    <p>

                        <?= $search !== ''
                            ? 'Try another search term.'
                            : 'Create your first product tag to get started.'
                        ?>

                    </p>


                    <?php if ($search === ''): ?>

                        <a href="tag-add.php" class="btn-add">

                            <i class="fa-solid fa-plus"></i>

                            Create First Tag

                        </a>

                    <?php endif; ?>


                </div>


            <?php else: ?>


                <div class="table-wrap">


                    <table>


                        <thead>

                            <tr>

                                <th>
                                    #
                                </th>

                                <th>
                                    Tag
                                </th>

                                <th>
                                    Slug
                                </th>

                                <th>
                                    Products
                                </th>

                                <th>
                                    Created
                                </th>

                                <th>
                                    Actions
                                </th>

                            </tr>

                        </thead>


                        <tbody>


                            <?php foreach ($tags as $tag): ?>


                                <tr>


                                    <td>

                                        <span class="tag-id">

                                            #<?= (int)$tag['id'] ?>

                                        </span>

                                    </td>


                                    <td>


                                        <div class="tag-name">


                                            <div class="tag-icon">

                                                <i class="fa-solid fa-tag"></i>

                                            </div>


                                            <?= htmlspecialchars(
                                                $tag['name']
                                            ) ?>


                                        </div>


                                    </td>


                                    <td>

                                        <span class="slug">

                                            <?= htmlspecialchars(
                                                $tag['slug']
                                            ) ?>

                                        </span>

                                    </td>


                                    <td>


                                        <?php

                                        $count =
                                            (int)
                                            $tag['product_count'];

                                        ?>


                                        <span class="
                                            product-count
                                            <?= $count === 0
                                                ? 'unused'
                                                : ''
                                            ?>
                                        ">

                                            <i class="fa-solid
                                                   fa-box"></i>

                                            <?= $count ?>

                                            product<?= $count !== 1
                                                        ? 's'
                                                        : ''
                                                    ?>

                                        </span>


                                    </td>


                                    <td>

                                        <span class="date">

                                            <?= !empty($tag['created_at'])
                                                ? date(
                                                    'd M Y',
                                                    strtotime(
                                                        $tag['created_at']
                                                    )
                                                )
                                                : '-'
                                            ?>

                                        </span>

                                    </td>


                                    <td>


                                        <div class="actions">


                                            <a href="tag-edit.php?id=<?= (int)$tag['id'] ?>" class="
                                                action-btn
                                                action-edit
                                            " title="Edit Tag">

                                                <i class="fa-solid
                                                       fa-pen"></i>

                                            </a>


                                            <a href="tag-delete.php?id=<?= (int)$tag['id'] ?>" class="
                                                action-btn
                                                action-delete
                                            " title="Delete Tag">

                                                <i class="fa-solid
                                                       fa-trash"></i>

                                            </a>


                                        </div>


                                    </td>


                                </tr>


                            <?php endforeach; ?>


                        </tbody>


                    </table>


                </div>


            <?php endif; ?>


        </div>


    </main>


</body>

</html>