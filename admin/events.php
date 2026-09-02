<?php

session_start();

require_once __DIR__ . '/../includes/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| SEARCH
|--------------------------------------------------------------------------
*/

$search = trim($_GET['search'] ?? '');

$status = $_GET['status'] ?? '';



/*
|--------------------------------------------------------------------------
| BUILD QUERY
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT
        id,
        title,
        slug,
        description,
        event_date,
        event_time,
        venue,
        image,
        status,
        created_at
    FROM events
    WHERE 1=1
";


$params = [];
$types = "";


/*
|--------------------------------------------------------------------------
| SEARCH FILTER
|--------------------------------------------------------------------------
*/

if ($search !== '') {

    $sql .= "
        AND (
            title LIKE ?
            OR slug LIKE ?
            OR description LIKE ?
            OR venue LIKE ?
        )
    ";

    $term = "%" . $search . "%";

    $params[] = $term;
    $params[] = $term;
    $params[] = $term;
    $params[] = $term;

    $types .= "ssss";
}


/*
|--------------------------------------------------------------------------
| STATUS FILTER
|--------------------------------------------------------------------------
*/

if ($status !== '' && ($status === '0' || $status === '1')) {

    $sql .= " AND status = ? ";

    $params[] = (int)$status;

    $types .= "i";
}


/*
|--------------------------------------------------------------------------
| ORDER
|--------------------------------------------------------------------------
*/

$sql .= "
    ORDER BY event_date DESC, id DESC
";


$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Database error: " . $conn->error);
}


if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}


$stmt->execute();

$result = $stmt->get_result();



/*
|--------------------------------------------------------------------------
| STATISTICS
|--------------------------------------------------------------------------
*/

$totalEvents = 0;
$activeEvents = 0;
$inactiveEvents = 0;
$upcomingEvents = 0;

$events = [];


$today = date('Y-m-d');


while ($row = $result->fetch_assoc()) {

    $events[] = $row;

    $totalEvents++;


    if ((int)$row['status'] === 1) {

        $activeEvents++;
    } else {

        $inactiveEvents++;
    }


    if (
        $row['event_date'] >= $today &&
        (int)$row['status'] === 1
    ) {

        $upcomingEvents++;
    }
}


$stmt->close();

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        Events | CORE LTD Admin
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


        /*
|--------------------------------------------------------------------------
| SIDEBAR
|--------------------------------------------------------------------------
*/

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

            color: #fff;

            padding: 25px 15px;

            box-shadow:
                4px 0 18px rgba(0, 61, 120, .18);

            z-index: 1000;

            overflow-y: auto;
        }


        .sidebar::-webkit-scrollbar {
            width: 4px;
        }


        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, .25);
        }


        .sidebar .brand {

            text-align: center;

            padding:
                4px 0 25px;

        }


        .brand-icon {

            width: 48px;
            height: 48px;

            margin:
                0 auto 10px;

            border-radius: 12px;

            background:
                rgba(255, 255, 255, .15);

            display: flex;

            align-items: center;
            justify-content: center;

            font-size: 22px;
        }


        .brand h2 {

            margin: 0;

            font-size: 19px;

            font-weight: 800;

            letter-spacing: .5px;
        }


        .brand span {

            display: block;

            margin-top: 4px;

            font-size: 10px;

            letter-spacing: 1.8px;

            opacity: .7;
        }


        .nav-title {

            margin:
                20px 12px 8px;

            font-size: 10px;

            text-transform: uppercase;

            letter-spacing: 1.3px;

            font-weight: 700;

            opacity: .6;
        }


        .sidebar>a {

            display: flex;

            align-items: center;

            gap: 12px;

            padding:
                12px 14px;

            margin-bottom: 4px;

            color:
                rgba(255, 255, 255, .84);

            text-decoration: none;

            border-radius: 8px;

            font-size: 13px;

            transition: .2s ease;
        }


        .sidebar>a:hover {

            background:
                rgba(255, 255, 255, .12);

            color: #fff;

            transform:
                translateX(2px);
        }


        .sidebar>a.active {

            background: #fff;

            color: #075eaa;

            font-weight: 700;

            box-shadow:
                0 5px 15px rgba(0, 0, 0, .12);
        }


        .sidebar>a i {

            width: 23px;
            height: 23px;

            display: flex;

            align-items: center;
            justify-content: center;

            border-radius: 6px;

            background:
                rgba(255, 255, 255, .12);

            font-size: 13px;
        }


        .sidebar>a.active i {

            background: #e7f2ff;
        }


        /*
|--------------------------------------------------------------------------
| MAIN
|--------------------------------------------------------------------------
*/

        .main {

            margin-left: 245px;

            min-height: 100vh;
        }


        /*
|--------------------------------------------------------------------------
| TOPBAR
|--------------------------------------------------------------------------
*/

        .topbar {

            height: 72px;

            background: #fff;

            border-bottom:
                1px solid #e5edf6;

            display: flex;

            align-items: center;

            justify-content: space-between;

            padding:
                0 30px;

            position: sticky;

            top: 0;

            z-index: 500;

            box-shadow:
                0 2px 10px rgba(20, 80, 130, .04);
        }


        .page-title {

            margin: 0;

            font-size: 21px;

            color: #17304e;

            font-weight: 700;
        }


        .page-subtitle {

            margin:
                4px 0 0;

            color: #8796a9;

            font-size: 12px;
        }


        .admin-user {

            display: flex;

            align-items: center;

            gap: 10px;
        }


        .admin-avatar {

            width: 40px;
            height: 40px;

            border-radius: 50%;

            background:
                linear-gradient(135deg,
                    #087ce9,
                    #064d93);

            color: #fff;

            display: flex;

            align-items: center;
            justify-content: center;

            font-weight: 800;

            box-shadow:
                0 4px 12px rgba(8, 124, 233, .25);
        }


        .admin-info strong {

            display: block;

            font-size: 13px;

            color: #26364a;
        }


        .admin-info span {

            display: block;

            margin-top: 2px;

            color: #8a98a8;

            font-size: 11px;
        }


        /*
|--------------------------------------------------------------------------
| CONTENT
|--------------------------------------------------------------------------
*/

        .content {

            padding: 30px;
        }


        /*
|--------------------------------------------------------------------------
| PAGE HEADER
|--------------------------------------------------------------------------
*/

        .content-header {

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 20px;

            margin-bottom: 25px;
        }


        .content-header h2 {

            margin: 0 0 5px;

            color: #17304e;

            font-size: 19px;
        }


        .content-header p {

            margin: 0;

            color: #8796a9;

            font-size: 12px;
        }


        .btn-add {

            display: inline-flex;

            align-items: center;

            gap: 8px;

            background:
                linear-gradient(135deg,
                    #087ce9,
                    #075eaa);

            color: #fff;

            text-decoration: none;

            border-radius: 8px;

            padding:
                11px 17px;

            font-size: 12px;

            font-weight: 700;

            box-shadow:
                0 5px 15px rgba(8, 124, 233, .22);

            transition: .2s ease;
        }


        .btn-add:hover {

            color: #fff;

            transform:
                translateY(-1px);

            box-shadow:
                0 8px 20px rgba(8, 124, 233, .28);
        }


        /*
|--------------------------------------------------------------------------
| STATISTICS
|--------------------------------------------------------------------------
*/

        .stats {

            display: grid;

            grid-template-columns:
                repeat(4, 1fr);

            gap: 16px;

            margin-bottom: 22px;
        }


        .stat-card {

            background: #fff;

            border:
                1px solid #e7edf5;

            border-radius: 10px;

            padding: 18px;

            display: flex;

            align-items: center;

            gap: 14px;

            box-shadow:
                0 3px 12px rgba(25, 80, 130, .04);
        }


        .stat-icon {

            width: 42px;
            height: 42px;

            border-radius: 10px;

            display: flex;

            align-items: center;
            justify-content: center;

            background: #eaf4ff;

            color: #0875df;

            font-size: 17px;
        }


        .stat-card strong {

            display: block;

            color: #17304e;

            font-size: 20px;

            line-height: 1;
        }


        .stat-card span {

            display: block;

            margin-top: 5px;

            color: #8796a9;

            font-size: 11px;
        }


        /*
|--------------------------------------------------------------------------
| SEARCH / FILTER
|--------------------------------------------------------------------------
*/

        .filter-box {

            background: #fff;

            border:
                1px solid #e7edf5;

            border-radius: 10px;

            padding: 16px;

            margin-bottom: 20px;

            box-shadow:
                0 3px 12px rgba(25, 80, 130, .04);
        }


        .filter-form {

            display: grid;

            grid-template-columns:
                2fr 1fr auto auto;

            gap: 10px;
        }


        .filter-form input,
        .filter-form select {

            height: 40px;

            border:
                1px solid #dce5ef;

            border-radius: 7px;

            padding:
                0 12px;

            outline: none;

            color: #344c66;

            font-size: 12px;

            background: #fff;
        }


        .filter-form input:focus,
        .filter-form select:focus {

            border-color: #087ce9;

            box-shadow:
                0 0 0 3px rgba(8, 124, 233, .08);
        }


        .btn-filter,
        .btn-clear {

            height: 40px;

            border-radius: 7px;

            padding:
                0 17px;

            border: none;

            display: inline-flex;

            align-items: center;

            justify-content: center;

            text-decoration: none;

            font-size: 12px;

            font-weight: 700;

            cursor: pointer;
        }


        .btn-filter {

            background: #087ce9;

            color: #fff;
        }


        .btn-clear {

            background: #eef3f8;

            color: #62758a;
        }


        /*
|--------------------------------------------------------------------------
| EVENTS BOX
|--------------------------------------------------------------------------
*/

        .events-box {

            background: #fff;

            border:
                1px solid #e7edf5;

            border-radius: 10px;

            overflow: hidden;

            box-shadow:
                0 3px 12px rgba(25, 80, 130, .04);
        }


        .events-header {

            padding:
                17px 20px;

            border-bottom:
                1px solid #edf1f5;

            display: flex;

            align-items: center;

            justify-content: space-between;
        }


        .events-header strong {

            font-size: 14px;

            color: #17304e;
        }


        .events-header span {

            color: #8796a9;

            font-size: 11px;
        }


        /*
|--------------------------------------------------------------------------
| TABLE
|--------------------------------------------------------------------------
*/

        .table-wrapper {

            width: 100%;

            overflow-x: auto;
        }


        table {

            width: 100%;

            border-collapse: collapse;

            min-width: 900px;
        }


        table th {

            background: #f8fafc;

            color: #718297;

            font-size: 10px;

            text-transform: uppercase;

            letter-spacing: .6px;

            font-weight: 700;

            padding:
                13px 15px;

            text-align: left;

            border-bottom:
                1px solid #e7edf5;
        }


        table td {

            padding:
                13px 15px;

            border-bottom:
                1px solid #edf1f5;

            vertical-align: middle;

            font-size: 12px;

            color: #52677d;
        }


        table tr:hover {

            background: #fbfdff;
        }


        /*
|--------------------------------------------------------------------------
| EVENT
|--------------------------------------------------------------------------
*/

        .event-info {

            display: flex;

            align-items: center;

            gap: 12px;
        }


        .event-image {

            width: 55px;
            height: 45px;

            border-radius: 7px;

            object-fit: cover;

            background: #edf4fb;

            border:
                1px solid #e2eaf2;
        }


        .event-placeholder {

            width: 55px;
            height: 45px;

            border-radius: 7px;

            background: #eaf4ff;

            color: #0875df;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 17px;
        }


        .event-name strong {

            display: block;

            color: #344c66;

            font-size: 13px;

            margin-bottom: 3px;
        }


        .event-name span {

            color: #98a6b5;

            font-size: 10px;
        }


        /*
|--------------------------------------------------------------------------
| DATE
|--------------------------------------------------------------------------
*/

        .event-date {

            color: #344c66;

            font-weight: 600;
        }


        .event-time {

            display: block;

            margin-top: 3px;

            color: #98a6b5;

            font-size: 10px;
        }


        /*
|--------------------------------------------------------------------------
| STATUS
|--------------------------------------------------------------------------
*/

        .status {

            display: inline-block;

            padding:
                5px 9px;

            border-radius: 20px;

            font-size: 10px;

            font-weight: 700;
        }


        .status-active {

            background: #e8f8ef;

            color: #159447;
        }


        .status-inactive {

            background: #fcebed;

            color: #d8495f;
        }


        /*
|--------------------------------------------------------------------------
| ACTIONS
|--------------------------------------------------------------------------
*/

        .actions {

            display: flex;

            align-items: center;

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

            transition: .2s ease;

            font-size: 11px;
        }


        .action-view {

            background: #eaf4ff;

            color: #0875df;
        }


        .action-edit {

            background: #fff5df;

            color: #d88a00;
        }


        .action-delete {

            background: #fdebed;

            color: #dc4658;
        }


        .action-btn:hover {

            transform:
                translateY(-1px);

            filter:
                brightness(.95);
        }


        /*
|--------------------------------------------------------------------------
| EMPTY
|--------------------------------------------------------------------------
*/

        .empty-state {

            text-align: center;

            padding:
                60px 20px;
        }


        .empty-icon {

            width: 60px;
            height: 60px;

            margin:
                0 auto 15px;

            border-radius: 50%;

            display: flex;

            align-items: center;
            justify-content: center;

            background: #eaf4ff;

            color: #0875df;

            font-size: 24px;
        }


        .empty-state h4 {

            margin:
                0 0 7px;

            color: #344c66;

            font-size: 16px;
        }


        .empty-state p {

            margin:
                0 0 18px;

            color: #8796a9;

            font-size: 12px;
        }


        /*
|--------------------------------------------------------------------------
| RESPONSIVE
|--------------------------------------------------------------------------
*/

        @media(max-width:1200px) {

            .stats {

                grid-template-columns:
                    repeat(2, 1fr);
            }

        }


        @media(max-width:900px) {

            .sidebar {

                width: 72px;

                padding:
                    20px 8px;
            }


            .brand h2,
            .brand span,
            .nav-title,
            .sidebar>a span {

                display: none;
            }


            .sidebar>a {

                justify-content: center;
            }


            .sidebar>a i {

                margin: 0;
            }


            .main {

                margin-left: 72px;
            }


            .content {

                padding: 20px;
            }


            .filter-form {

                grid-template-columns:
                    1fr 1fr;
            }

        }


        @media(max-width:650px) {

            .topbar {

                padding:
                    0 18px;
            }


            .content {

                padding: 18px;
            }


            .admin-info {

                display: none;
            }


            .content-header {

                flex-direction: column;

                align-items: flex-start;
            }


            .stats {

                grid-template-columns: 1fr;
            }


            .filter-form {

                grid-template-columns: 1fr;
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


        <a href="events.php" class="active">

            <i class="fa-solid fa-calendar-days"></i>

            <span>
                Events
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

        <header class="topbar">

            <div>

                <h1 class="page-title">
                    Events
                </h1>

                <p class="page-subtitle">
                    Manage CORE LTD events and announcements
                </p>

            </div>


            <div class="admin-user">

                <div class="admin-avatar">

                    <?= htmlspecialchars(
                        strtoupper(
                            substr(
                                $_SESSION['admin_name'] ?? 'A',
                                0,
                                1
                            )
                        )
                    ) ?>

                </div>


                <div class="admin-info">

                    <strong>

                        <?= htmlspecialchars(
                            $_SESSION['admin_name']
                                ?? 'Administrator'
                        ) ?>

                    </strong>

                    <span>
                        Administrator
                    </span>

                </div>

            </div>

        </header>



        <!-- CONTENT -->

        <div class="content">


            <!-- PAGE HEADER -->

            <div class="content-header">

                <div>

                    <h2>
                        Event Management
                    </h2>

                    <p>
                        Add, edit and manage CORE LTD events.
                    </p>

                </div>


                <a href="event-add.php" class="btn-add">

                    <i class="fa-solid fa-plus"></i>

                    Add Event

                </a>

            </div>



            <!-- STATISTICS -->

            <div class="stats">


                <div class="stat-card">

                    <div class="stat-icon">

                        <i class="fa-solid fa-calendar-days"></i>

                    </div>

                    <div>

                        <strong>
                            <?= $totalEvents ?>
                        </strong>

                        <span>
                            Total Events
                        </span>

                    </div>

                </div>



                <div class="stat-card">

                    <div class="stat-icon">

                        <i class="fa-solid fa-calendar-check"></i>

                    </div>

                    <div>

                        <strong>
                            <?= $upcomingEvents ?>
                        </strong>

                        <span>
                            Upcoming
                        </span>

                    </div>

                </div>



                <div class="stat-card">

                    <div class="stat-icon">

                        <i class="fa-solid fa-circle-check"></i>

                    </div>

                    <div>

                        <strong>
                            <?= $activeEvents ?>
                        </strong>

                        <span>
                            Active Events
                        </span>

                    </div>

                </div>



                <div class="stat-card">

                    <div class="stat-icon">

                        <i class="fa-solid fa-circle-xmark"></i>

                    </div>

                    <div>

                        <strong>
                            <?= $inactiveEvents ?>
                        </strong>

                        <span>
                            Inactive Events
                        </span>

                    </div>

                </div>

            </div>



            <!-- SEARCH -->

            <div class="filter-box">

                <form method="GET" class="filter-form">

                    <input type="text" name="search" placeholder="Search event, venue..."
                        value="<?= htmlspecialchars($search) ?>">


                    <select name="status">

                        <option value="">
                            All Status
                        </option>

                        <option value="1" <?= $status === '1'
                                                ? 'selected'
                                                : '' ?>>
                            Active
                        </option>

                        <option value="0" <?= $status === '0'
                                                ? 'selected'
                                                : '' ?>>
                            Inactive
                        </option>

                    </select>


                    <button type="submit" class="btn-filter">

                        <i class="fa-solid fa-filter"></i>

                        &nbsp;Filter

                    </button>


                    <a href="events.php" class="btn-clear">
                        Clear
                    </a>

                </form>

            </div>



            <!-- EVENTS -->

            <div class="events-box">


                <div class="events-header">

                    <strong>
                        Event List
                    </strong>

                    <span>

                        <?= count($events) ?>

                        event(s) found

                    </span>

                </div>



                <div class="table-wrapper">


                    <?php if (!empty($events)): ?>


                        <table>

                            <thead>

                                <tr>

                                    <th>
                                        Event
                                    </th>

                                    <th>
                                        Date / Time
                                    </th>

                                    <th>
                                        Venue
                                    </th>

                                    <th>
                                        Status
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


                                <?php foreach ($events as $event): ?>


                                    <tr>


                                        <!-- EVENT -->

                                        <td>

                                            <div class="event-info">


                                                <?php if (
                                                    !empty($event['image'])
                                                ): ?>

                                                    <img src="<?= htmlspecialchars(
                                                                    '../' .
                                                                        ltrim(
                                                                            $event['image'],
                                                                            '/'
                                                                        )
                                                                ) ?>" class="event-image" alt="<?= htmlspecialchars(
                                                                                $event['title']
                                                                            ) ?>">

                                                <?php else: ?>

                                                    <div class="event-placeholder">

                                                        <i class="fa-solid fa-calendar-days"></i>

                                                    </div>

                                                <?php endif; ?>


                                                <div class="event-name">

                                                    <strong>

                                                        <?= htmlspecialchars(
                                                            $event['title']
                                                        ) ?>

                                                    </strong>

                                                    <span>

                                                        <?= htmlspecialchars(
                                                            $event['slug']
                                                        ) ?>

                                                    </span>

                                                </div>

                                            </div>

                                        </td>



                                        <!-- DATE -->

                                        <td>

                                            <span class="event-date">

                                                <?= date(
                                                    'd M Y',
                                                    strtotime(
                                                        $event['event_date']
                                                    )
                                                ) ?>

                                            </span>


                                            <?php if (
                                                !empty($event['event_time'])
                                            ): ?>

                                                <span class="event-time">

                                                    <?= date(
                                                        'h:i A',
                                                        strtotime(
                                                            $event['event_time']
                                                        )
                                                    ) ?>

                                                </span>

                                            <?php endif; ?>

                                        </td>



                                        <!-- VENUE -->

                                        <td>

                                            <?= !empty($event['venue'])
                                                ? htmlspecialchars(
                                                    $event['venue']
                                                )
                                                : '<span style="color:#a0acb9;">—</span>'
                                            ?>

                                        </td>



                                        <!-- STATUS -->

                                        <td>

                                            <?php if (
                                                (int)$event['status'] === 1
                                            ): ?>

                                                <span class="status status-active">

                                                    Active

                                                </span>

                                            <?php else: ?>

                                                <span class="status status-inactive">

                                                    Inactive

                                                </span>

                                            <?php endif; ?>

                                        </td>



                                        <!-- CREATED -->

                                        <td>

                                            <span style="
                                    color:#8493a3;
                                    font-size:11px;
                                ">

                                                <?= date(
                                                    'd M Y',
                                                    strtotime(
                                                        $event['created_at']
                                                    )
                                                ) ?>

                                            </span>

                                        </td>



                                        <!-- ACTIONS -->

                                        <td>

                                            <div class="actions">


                                                <!-- VIEW -->

                                                <a href="../events.php" class="action-btn action-view" title="View Events">

                                                    <i class="fa-solid fa-eye"></i>

                                                </a>


                                                <!-- EDIT -->

                                                <a href="event-edit.php?id=<?= (int)$event['id'] ?>"
                                                    class="action-btn action-edit" title="Edit Event">

                                                    <i class="fa-solid fa-pen"></i>

                                                </a>


                                                <!-- DELETE -->

                                                <a href="event-delete.php?id=<?= (int)$event['id'] ?>"
                                                    class="action-btn action-delete" title="Delete Event" onclick="
                                            return confirm(
                                                'Are you sure you want to delete this event?'
                                            );
                                        ">

                                                    <i class="fa-solid fa-trash"></i>

                                                </a>

                                            </div>

                                        </td>


                                    </tr>


                                <?php endforeach; ?>


                            </tbody>

                        </table>


                    <?php else: ?>


                        <div class="empty-state">

                            <div class="empty-icon">

                                <i class="fa-solid fa-calendar-days"></i>

                            </div>


                            <h4>
                                No Events Found
                            </h4>


                            <p>

                                <?php if ($search !== ''): ?>

                                    No event matches
                                    "<strong><?= htmlspecialchars($search) ?></strong>".

                                <?php else: ?>

                                    Add your first CORE LTD event.

                                <?php endif; ?>

                            </p>


                            <?php if ($search !== ''): ?>

                                <a href="events.php" class="btn-add">

                                    <i class="fa-solid fa-rotate-left"></i>

                                    Clear Search

                                </a>

                            <?php else: ?>

                                <a href="event-add.php" class="btn-add">

                                    <i class="fa-solid fa-plus"></i>

                                    Add First Event

                                </a>

                            <?php endif; ?>

                        </div>


                    <?php endif; ?>


                </div>


            </div>


        </div>


    </main>


</body>

</html>