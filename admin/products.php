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
| SEARCH & FILTERS
|--------------------------------------------------------------------------
*/

$search = trim($_GET['search'] ?? '');
$categoryId = (int)($_GET['category'] ?? 0);
$brandId = (int)($_GET['brand'] ?? 0);
$status = $_GET['status'] ?? '';


/*
|--------------------------------------------------------------------------
| LOAD CATEGORIES
|--------------------------------------------------------------------------
*/

$categories = [];

$categoryQuery = $conn->query("
    SELECT id, name
    FROM categories
    ORDER BY name ASC
");

while ($row = $categoryQuery->fetch_assoc()) {
    $categories[] = $row;
}


/*
|--------------------------------------------------------------------------
| LOAD BRANDS
|--------------------------------------------------------------------------
*/

$brands = [];

$brandQuery = $conn->query("
    SELECT id, name
    FROM brands
    ORDER BY name ASC
");

while ($row = $brandQuery->fetch_assoc()) {
    $brands[] = $row;
}


/*
|--------------------------------------------------------------------------
| BUILD PRODUCT QUERY
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| PAGINATION
|--------------------------------------------------------------------------
*/

$productsPerPage = 8;

$currentPage = isset($_GET['page'])
    ? (int)$_GET['page']
    : 1;

if ($currentPage < 1) {
    $currentPage = 1;
}


/*
|--------------------------------------------------------------------------
| BUILD PRODUCT QUERY
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT
        p.id,
        p.category_id,
        p.brand_id,
        p.name,
        p.slug,
        p.sku,
        p.short_description,
        p.price,
        p.old_price,
        p.stock_quantity,
        p.low_stock_level,
        p.main_image,
        p.featured,
        p.status,
        p.views,
        p.created_at,

        c.name AS category_name,
        b.name AS brand_name

    FROM products p

    LEFT JOIN categories c
        ON p.category_id = c.id

    LEFT JOIN brands b
        ON p.brand_id = b.id

    WHERE 1=1
";


$params = [];
$types = '';


/*
|--------------------------------------------------------------------------
| SEARCH
|--------------------------------------------------------------------------
*/

if ($search !== '') {

    $sql .= "
        AND (
            p.name LIKE ?
            OR p.sku LIKE ?
            OR p.slug LIKE ?
        )
    ";

    $searchValue = '%' . $search . '%';

    $params[] = $searchValue;
    $params[] = $searchValue;
    $params[] = $searchValue;

    $types .= 'sss';
}


/*
|--------------------------------------------------------------------------
| CATEGORY FILTER
|--------------------------------------------------------------------------
*/

if ($categoryId > 0) {

    $sql .= "
        AND p.category_id = ?
    ";

    $params[] = $categoryId;

    $types .= 'i';
}


/*
|--------------------------------------------------------------------------
| BRAND FILTER
|--------------------------------------------------------------------------
*/

if ($brandId > 0) {

    $sql .= "
        AND p.brand_id = ?
    ";

    $params[] = $brandId;

    $types .= 'i';
}


/*
|--------------------------------------------------------------------------
| STATUS FILTER
|--------------------------------------------------------------------------
*/

if ($status !== '' && ($status === '0' || $status === '1')) {

    $sql .= "
        AND p.status = ?
    ";

    $params[] = (int)$status;

    $types .= 'i';
}


/*
|--------------------------------------------------------------------------
| COUNT FILTERED PRODUCTS
|--------------------------------------------------------------------------
|
| This counts only the products matching the current
| search/filter conditions.
|
*/

$countSql = "
    SELECT COUNT(*) AS total
    FROM products p

    LEFT JOIN categories c
        ON p.category_id = c.id

    LEFT JOIN brands b
        ON p.brand_id = b.id

    WHERE 1=1
";


$countParams = [];
$countTypes = '';


/*
|--------------------------------------------------------------------------
| SEARCH COUNT
|--------------------------------------------------------------------------
*/

if ($search !== '') {

    $countSql .= "
        AND (
            p.name LIKE ?
            OR p.sku LIKE ?
            OR p.slug LIKE ?
        )
    ";

    $searchValue = '%' . $search . '%';

    $countParams[] = $searchValue;
    $countParams[] = $searchValue;
    $countParams[] = $searchValue;

    $countTypes .= 'sss';
}


/*
|--------------------------------------------------------------------------
| CATEGORY COUNT
|--------------------------------------------------------------------------
*/

if ($categoryId > 0) {

    $countSql .= "
        AND p.category_id = ?
    ";

    $countParams[] = $categoryId;

    $countTypes .= 'i';
}


/*
|--------------------------------------------------------------------------
| BRAND COUNT
|--------------------------------------------------------------------------
*/

if ($brandId > 0) {

    $countSql .= "
        AND p.brand_id = ?
    ";

    $countParams[] = $brandId;

    $countTypes .= 'i';
}


/*
|--------------------------------------------------------------------------
| STATUS COUNT
|--------------------------------------------------------------------------
*/

if ($status !== '' && ($status === '0' || $status === '1')) {

    $countSql .= "
        AND p.status = ?
    ";

    $countParams[] = (int)$status;

    $countTypes .= 'i';
}


/*
|--------------------------------------------------------------------------
| EXECUTE COUNT QUERY
|--------------------------------------------------------------------------
*/

$countStmt = $conn->prepare($countSql);

if (!$countStmt) {
    die("Count query failed: " . $conn->error);
}

if (!empty($countParams)) {

    $countStmt->bind_param(
        $countTypes,
        ...$countParams
    );
}

$countStmt->execute();

$countResult = $countStmt->get_result();

$totalFilteredProducts =
    (int)$countResult->fetch_assoc()['total'];

$countStmt->close();


/*
|--------------------------------------------------------------------------
| CALCULATE PAGES
|--------------------------------------------------------------------------
*/

$totalPages = max(
    1,
    (int)ceil(
        $totalFilteredProducts / $productsPerPage
    )
);


/*
|--------------------------------------------------------------------------
| FIX PAGE IF TOO HIGH
|--------------------------------------------------------------------------
*/

if ($currentPage > $totalPages) {

    $currentPage = $totalPages;
}


/*
|--------------------------------------------------------------------------
| OFFSET
|--------------------------------------------------------------------------
*/

$offset =
    ($currentPage - 1)
    * $productsPerPage;


/*
|--------------------------------------------------------------------------
| ORDER + PAGINATION
|--------------------------------------------------------------------------
*/

$sql .= "
    ORDER BY p.id DESC
    LIMIT ? OFFSET ?
";


$params[] = $productsPerPage;
$params[] = $offset;

$types .= 'ii';


/*
|--------------------------------------------------------------------------
| EXECUTE PRODUCT QUERY
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Product query failed: " . $conn->error);
}

$stmt->bind_param(
    $types,
    ...$params
);

$stmt->execute();

$result = $stmt->get_result();


/*
|--------------------------------------------------------------------------
| STATISTICS
|--------------------------------------------------------------------------
*/

$totalProductsQuery = $conn->query("
    SELECT COUNT(*) AS total
    FROM products
");

$totalProducts =
    (int)$totalProductsQuery
        ->fetch_assoc()['total'];


$activeProductsQuery = $conn->query("
    SELECT COUNT(*) AS total
    FROM products
    WHERE status = 1
");

$activeProducts =
    (int)$activeProductsQuery
        ->fetch_assoc()['total'];


$inactiveProductsQuery = $conn->query("
    SELECT COUNT(*) AS total
    FROM products
    WHERE status = 0
");

$inactiveProducts =
    (int)$inactiveProductsQuery
        ->fetch_assoc()['total'];


$outOfStockQuery = $conn->query("
    SELECT COUNT(*) AS total
    FROM products
    WHERE stock_quantity <= 0
");

$outOfStock =
    (int)$outOfStockQuery
        ->fetch_assoc()['total'];


/*
|--------------------------------------------------------------------------
| LOW STOCK
|--------------------------------------------------------------------------
*/

$lowStockQuery = $conn->query("
    SELECT COUNT(*) AS total
    FROM products
    WHERE stock_quantity > 0
    AND stock_quantity <= low_stock_level
");

$lowStockProducts =
    (int)$lowStockQuery
        ->fetch_assoc()['total'];

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        CORE LTD - Products
    </title>


    <style>
        * {
            box-sizing: border-box;
        }


        body {

            margin: 0;

            font-family:
                Arial,
                Helvetica,
                sans-serif;

            background:
                #f3f7fc;

            color:
                #1c2b3f;

        }


        /*
    |--------------------------------------------------------------------------
    | SIDEBAR
    |--------------------------------------------------------------------------
    */

        .sidebar {

            position:
                fixed;

            left:
                0;

            top:
                0;

            width:
                250px;

            height:
                100vh;

            background:
                linear-gradient(180deg,
                    #063f78 0%,
                    #075eaa 55%,
                    #0875d1 100%);

            color:
                #fff;

            padding:
                22px 15px;

            overflow-y:
                auto;

            box-shadow:
                4px 0 18px rgba(0, 61, 120, 0.15);

            z-index:
                1000;

        }


        .sidebar-logo {

            text-align:
                center;

            padding:
                7px 10px 25px;

            border-bottom:
                1px solid rgba(255, 255, 255, .15);

        }


        .sidebar-logo h2 {

            margin:
                0;

            font-size:
                25px;

            letter-spacing:
                1.5px;

            font-weight:
                800;

        }


        .sidebar-logo p {

            margin:
                6px 0 0;

            color:
                rgba(255, 255, 255, .75);

            font-size:
                11px;

            letter-spacing:
                1px;

        }


        .sidebar-menu {

            margin-top:
                20px;

        }


        .menu-title {

            color:
                rgba(255, 255, 255, .55);

            font-size:
                10px;

            text-transform:
                uppercase;

            letter-spacing:
                1.3px;

            margin:
                20px 12px 8px;

            font-weight:
                700;

        }


        .sidebar-menu a {

            display:
                flex;

            align-items:
                center;

            gap:
                12px;

            padding:
                12px 14px;

            margin-bottom:
                4px;

            color:
                rgba(255, 255, 255, .84);

            text-decoration:
                none;

            border-radius:
                8px;

            font-size:
                13px;

            transition:
                .2s ease;

        }


        .sidebar-menu a:hover {

            background:
                rgba(255, 255, 255, .12);

            color:
                #fff;

            transform:
                translateX(2px);

        }


        .sidebar-menu a.active {

            background:
                #ffffff;

            color:
                #075eaa;

            font-weight:
                700;

            box-shadow:
                0 5px 15px rgba(0, 0, 0, .12);

        }


        .sidebar-menu .icon {

            width:
                23px;

            height:
                23px;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            border-radius:
                6px;

            background:
                rgba(255, 255, 255, .12);

            font-size:
                13px;

        }


        .sidebar-menu a.active .icon {

            background:
                #e7f2ff;

        }


        /*
    |--------------------------------------------------------------------------
    | MAIN
    |--------------------------------------------------------------------------
    */

        .main {

            margin-left:
                250px;

            min-height:
                100vh;

        }


        /*
    |--------------------------------------------------------------------------
    | TOPBAR
    |--------------------------------------------------------------------------
    */

        .topbar {

            height:
                72px;

            background:
                #ffffff;

            border-bottom:
                1px solid #e5edf6;

            display:
                flex;

            align-items:
                center;

            justify-content:
                space-between;

            padding:
                0 30px;

            position:
                sticky;

            top:
                0;

            z-index:
                500;

            box-shadow:
                0 2px 10px rgba(20, 80, 130, .04);

        }


        .page-title h1 {

            margin:
                0;

            font-size:
                21px;

            color:
                #17304e;

        }


        .page-title p {

            margin:
                4px 0 0;

            color:
                #8796a9;

            font-size:
                12px;

        }


        .admin-user {

            display:
                flex;

            align-items:
                center;

            gap:
                10px;

        }


        .admin-avatar {

            width:
                40px;

            height:
                40px;

            border-radius:
                50%;

            background:
                linear-gradient(135deg,
                    #087ce9,
                    #064d93);

            color:
                #fff;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            font-weight:
                800;

            box-shadow:
                0 4px 12px rgba(8, 124, 233, .25);

        }


        .admin-info strong {

            display:
                block;

            font-size:
                12px;

            color:
                #20334b;

        }


        .admin-info span {

            color:
                #8997a8;

            font-size:
                10px;

        }


        /*
    |--------------------------------------------------------------------------
    | CONTENT
    |--------------------------------------------------------------------------
    */

        .content {

            padding:
                30px;

        }


        .content-header {

            display:
                flex;

            justify-content:
                space-between;

            align-items:
                center;

            margin-bottom:
                22px;

        }


        .content-header h2 {

            margin:
                0;

            font-size:
                22px;

            color:
                #193452;

        }


        .content-header p {

            margin:
                5px 0 0;

            color:
                #8796a9;

            font-size:
                12px;

        }


        .btn-add {

            display:
                inline-flex;

            align-items:
                center;

            gap:
                7px;

            padding:
                12px 18px;

            background:
                linear-gradient(135deg,
                    #087ce9,
                    #0756a5);

            color:
                #fff;

            border-radius:
                7px;

            text-decoration:
                none;

            font-size:
                12px;

            font-weight:
                700;

            box-shadow:
                0 5px 14px rgba(8, 124, 233, .22);

            transition:
                .2s ease;

        }


        .btn-add:hover {

            color:
                #fff;

            transform:
                translateY(-2px);

            box-shadow:
                0 8px 18px rgba(8, 124, 233, .28);

        }


        /*
    |--------------------------------------------------------------------------
    | STATISTICS
    |--------------------------------------------------------------------------
    */

        .stats {

            display:
                grid;

            grid-template-columns:
                repeat(5, 1fr);

            gap:
                15px;

            margin-bottom:
                22px;

        }


        .stat-card {

            position:
                relative;

            overflow:
                hidden;

            background:
                #fff;

            border:
                1px solid #e4edf6;

            border-radius:
                10px;

            padding:
                18px;

            box-shadow:
                0 4px 15px rgba(25, 76, 120, .05);

        }


        .stat-card:after {

            content:
                "";

            position:
                absolute;

            right:
                -20px;

            bottom:
                -30px;

            width:
                80px;

            height:
                80px;

            border-radius:
                50%;

            background:
                #eaf4ff;

        }


        .stat-icon {

            width:
                36px;

            height:
                36px;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            border-radius:
                8px;

            background:
                #eaf4ff;

            color:
                #0875df;

            margin-bottom:
                12px;

            font-size:
                15px;

            position:
                relative;

            z-index:
                2;

        }


        .stat-label {

            color:
                #8291a4;

            font-size:
                10px;

            text-transform:
                uppercase;

            letter-spacing:
                .5px;

            margin-bottom:
                7px;

            font-weight:
                700;

        }


        .stat-number {

            font-size:
                24px;

            font-weight:
                800;

            color:
                #1d3552;

        }


        .stat-card.active-card .stat-icon {

            background:
                #e8f8ef;

            color:
                #179653;

        }


        .stat-card.inactive-card .stat-icon {

            background:
                #fff3e3;

            color:
                #d88900;

        }


        .stat-card.stock-card .stat-icon {

            background:
                #fff0f0;

            color:
                #d52d38;

        }


        .stat-card.low-card .stat-icon {

            background:
                #fff7df;

            color:
                #c48800;

        }


        /*
    |--------------------------------------------------------------------------
    | FILTER BOX
    |--------------------------------------------------------------------------
    */

        .filter-box {

            background:
                #ffffff;

            border:
                1px solid #e4edf6;

            border-radius:
                10px;

            padding:
                20px;

            margin-bottom:
                22px;

            box-shadow:
                0 4px 15px rgba(25, 76, 120, .04);

        }


        .filter-heading {

            display:
                flex;

            align-items:
                center;

            gap:
                8px;

            margin-bottom:
                15px;

            color:
                #25415f;

            font-size:
                13px;

            font-weight:
                700;

        }


        .filter-heading span {

            width:
                28px;

            height:
                28px;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            background:
                #eaf4ff;

            color:
                #0875df;

            border-radius:
                6px;

        }


        .filter-form {

            display:
                grid;

            grid-template-columns:
                2fr 1fr 1fr 1fr auto auto;

            gap:
                10px;

            align-items:
                end;

        }


        .form-group label {

            display:
                block;

            margin-bottom:
                6px;

            color:
                #61748b;

            font-size:
                10px;

            font-weight:
                700;

            text-transform:
                uppercase;

        }


        .form-group input,
        .form-group select {

            width:
                100%;

            height:
                40px;

            border:
                1px solid #dce6f0;

            border-radius:
                6px;

            padding:
                0 11px;

            background:
                #fbfdff;

            color:
                #344b64;

            font-size:
                12px;

            outline:
                none;

        }


        .form-group input:focus,
        .form-group select:focus {

            border-color:
                #0875df;

            box-shadow:
                0 0 0 3px rgba(8, 117, 223, .08);

        }


        .btn-filter {

            height:
                40px;

            padding:
                0 18px;

            border:
                none;

            background:
                #0875df;

            color:
                #fff;

            border-radius:
                6px;

            cursor:
                pointer;

            font-size:
                12px;

            font-weight:
                700;

        }


        .btn-filter:hover {

            background:
                #075cae;

        }


        .btn-clear {

            height:
                40px;

            display:
                flex;

            align-items:
                center;

            padding:
                0 15px;

            background:
                #f1f6fb;

            color:
                #61748b;

            text-decoration:
                none;

            border-radius:
                6px;

            font-size:
                12px;

            font-weight:
                600;

        }


        .btn-clear:hover {

            background:
                #e5eef7;

            color:
                #075cae;

        }


        /*
    |--------------------------------------------------------------------------
    | PRODUCTS BOX
    |--------------------------------------------------------------------------
    */

        .products-box {

            background:
                #ffffff;

            border:
                1px solid #e3ecf5;

            border-radius:
                10px;

            overflow:
                hidden;

            box-shadow:
                0 4px 18px rgba(25, 76, 120, .05);

        }


        .products-header {

            padding:
                17px 20px;

            border-bottom:
                1px solid #edf2f7;

            display:
                flex;

            justify-content:
                space-between;

            align-items:
                center;

        }


        .products-header strong {

            font-size:
                14px;

            color:
                #213b58;

        }


        .products-header span {

            color:
                #8796a9;

            font-size:
                11px;

        }


        .table-wrapper {

            overflow-x:
                auto;

        }


        table {

            width:
                100%;

            border-collapse:
                collapse;

            min-width:
                1000px;

        }


        th {

            padding:
                13px 18px;

            background:
                #f4f8fc;

            border-bottom:
                1px solid #e2ebf4;

            color:
                #60758d;

            font-size:
                10px;

            text-align:
                left;

            white-space:
                nowrap;

            text-transform:
                uppercase;

            letter-spacing:
                .4px;

        }


        td {

            padding:
                14px 18px;

            border-bottom:
                1px solid #edf2f6;

            font-size:
                12px;

            vertical-align:
                middle;

        }


        tbody tr {

            transition:
                .15s ease;

        }


        tbody tr:hover {

            background:
                #f8fbff;

        }


        tr:last-child td {

            border-bottom:
                none;

        }


        /*
    |--------------------------------------------------------------------------
    | PRODUCT CELL
    |--------------------------------------------------------------------------
    */

        .product-cell {

            display:
                flex;

            align-items:
                center;

            gap:
                12px;

            min-width:
                290px;

        }


        .product-image {

            width:
                58px;

            height:
                58px;

            object-fit:
                contain;

            background:
                #f8fbfe;

            border:
                1px solid #e4edf5;

            border-radius:
                8px;

            flex-shrink:
                0;

            padding:
                4px;

        }


        .no-image {

            width:
                58px;

            height:
                58px;

            display:
                flex;

            flex-direction:
                column;

            align-items:
                center;

            justify-content:
                center;

            background:
                #f3f7fb;

            border:
                1px solid #e4edf5;

            border-radius:
                8px;

            color:
                #9aabba;

            font-size:
                9px;

            flex-shrink:
                0;

        }


        .no-image-icon {

            font-size:
                18px;

            margin-bottom:
                3px;

        }


        .product-name {

            font-weight:
                700;

            line-height:
                1.4;

            color:
                #253e59;

            max-width:
                270px;

        }


        .product-sku {

            color:
                #8c9bab;

            font-size:
                10px;

            margin-top:
                4px;

        }


        /*
    |--------------------------------------------------------------------------
    | CATEGORY / BRAND
    |--------------------------------------------------------------------------
    */

        .category-brand {

            line-height:
                1.6;

        }


        .category-brand strong {

            display:
                block;

            font-size:
                11px;

            color:
                #36506b;

        }


        .category-brand span {

            color:
                #8b99a9;

            font-size:
                10px;

        }


        /*
    |--------------------------------------------------------------------------
    | PRICE
    |--------------------------------------------------------------------------
    */

        .price {

            color:
                #0875df;

            font-weight:
                800;

            white-space:
                nowrap;

        }


        .old-price {

            color:
                #9ba7b4;

            text-decoration:
                line-through;

            font-size:
                10px;

            margin-top:
                3px;

        }


        /*
    |--------------------------------------------------------------------------
    | STOCK
    |--------------------------------------------------------------------------
    */

        .stock {

            display:
                inline-flex;

            align-items:
                center;

            padding:
                5px 9px;

            border-radius:
                20px;

            font-weight:
                700;

            font-size:
                10px;

            white-space:
                nowrap;

        }


        .stock-good {

            background:
                #e9f8ef;

            color:
                #19864d;

        }


        .stock-low {

            background:
                #fff5df;

            color:
                #bd7800;

        }


        .stock-out {

            background:
                #ffeded;

            color:
                #ca3039;

        }


        /*
    |--------------------------------------------------------------------------
    | FEATURED
    |--------------------------------------------------------------------------
    */

        .featured {

            width:
                30px;

            height:
                30px;

            display:
                inline-flex;

            align-items:
                center;

            justify-content:
                center;

            border-radius:
                7px;

            background:
                #fff6da;

            color:
                #e1a300;

            font-size:
                16px;

        }


        .not-featured {

            width:
                30px;

            height:
                30px;

            display:
                inline-flex;

            align-items:
                center;

            justify-content:
                center;

            border-radius:
                7px;

            background:
                #f2f5f8;

            color:
                #bdc7d1;

            font-size:
                16px;

        }


        /*
    |--------------------------------------------------------------------------
    | STATUS
    |--------------------------------------------------------------------------
    */

        .status {

            display:
                inline-block;

            padding:
                6px 10px;

            border-radius:
                20px;

            font-size:
                10px;

            font-weight:
                700;

        }


        .status-active {

            background:
                #e8f8ef;

            color:
                #19864d;

        }


        .status-inactive {

            background:
                #f0f3f6;

            color:
                #7a8794;

        }


        /*
    |--------------------------------------------------------------------------
    | ACTIONS
    |--------------------------------------------------------------------------
    */

        .actions {

            display:
                flex;

            gap:
                6px;

        }


        .action-btn {

            display:
                inline-flex;

            align-items:
                center;

            justify-content:
                center;

            width:
                34px;

            height:
                34px;

            border-radius:
                7px;

            text-decoration:
                none;

            font-size:
                14px;

            transition:
                .15s ease;

        }


        .edit-btn {

            background:
                #eaf4ff;

            color:
                #0875df;

        }


        .edit-btn:hover {

            background:
                #0875df;

            color:
                #fff;

        }


        .delete-btn {

            background:
                #fff0f0;

            color:
                #d0323c;

        }


        .delete-btn:hover {

            background:
                #d0323c;

            color:
                #fff;

        }


        /*
    |--------------------------------------------------------------------------
    | EMPTY
    |--------------------------------------------------------------------------
    */

        .empty {

            padding:
                75px 20px;

            text-align:
                center;

            color:
                #8997a8;

        }


        .empty-icon {

            width:
                60px;

            height:
                60px;

            margin:
                0 auto 15px;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            border-radius:
                50%;

            background:
                #eaf4ff;

            color:
                #0875df;

            font-size:
                25px;

        }


        .empty h3 {

            margin:
                0 0 7px;

            color:
                #344c66;

            font-size:
                16px;

        }


        .empty p {

            margin:
                0 0 18px;

            font-size:
                12px;

        }


        /*
    |--------------------------------------------------------------------------
    | RESPONSIVE
    |--------------------------------------------------------------------------
    */

        @media (max-width: 1200px) {

            .stats {

                grid-template-columns:
                    repeat(3, 1fr);

            }

            .filter-form {

                grid-template-columns:
                    2fr 1fr 1fr;

            }

        }


        @media (max-width: 900px) {

            .sidebar {

                width:
                    72px;

                padding:
                    20px 8px;

            }


            .sidebar-logo h2 {

                font-size:
                    14px;

            }


            .sidebar-logo p,
            .menu-title,
            .sidebar-menu a span:not(.icon) {

                display:
                    none;

            }


            .sidebar-menu a {

                justify-content:
                    center;

            }


            .main {

                margin-left:
                    72px;

            }


            .stats {

                grid-template-columns:
                    repeat(2, 1fr);

            }

        }


        @media (max-width: 650px) {

            .content {

                padding:
                    18px;

            }


            .topbar {

                padding:
                    0 18px;

            }


            .admin-info {

                display:
                    none;

            }


            .content-header {

                align-items:
                    flex-start;

                gap:
                    15px;

                flex-direction:
                    column;

            }


            .stats {

                grid-template-columns:
                    1fr;

            }


            .filter-form {

                grid-template-columns:
                    1fr;

            }

        }

        /*
|--------------------------------------------------------------------------
| PAGINATION
|--------------------------------------------------------------------------
*/

        .pagination-wrapper {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            padding: 18px 20px;
            border-top: 1px solid #edf2f7;
            background: #ffffff;
            flex-wrap: wrap;
        }

        .pagination-info {
            color: #8796a9;
            font-size: 11px;
        }

        .pagination {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }

        .pagination a,
        .pagination span {
            min-width: 34px;
            height: 34px;
            padding: 0 10px;

            display: inline-flex;
            align-items: center;
            justify-content: center;

            border-radius: 7px;

            text-decoration: none;

            font-size: 11px;
            font-weight: 700;

            border: 1px solid #dfe8f1;

            color: #60758d;

            background: #ffffff;

            transition: .2s ease;
        }

        .pagination a:hover {
            background: #eaf4ff;
            color: #0875df;
            border-color: #bcdcff;
        }

        .pagination .active {
            background: linear-gradient(135deg,
                    #087ce9,
                    #0756a5);

            color: #ffffff;

            border-color: #0875df;
        }

        .pagination .disabled {
            opacity: .45;
            cursor: not-allowed;
        }

        .pagination .prev-next {
            padding-left: 14px;
            padding-right: 14px;
        }
    </style>

</head>


<body>


    <!-- =========================================================
     SIDEBAR
========================================================= -->

    <aside class="sidebar">

        <div class="sidebar-logo">

            <h2>
                CORE LTD
            </h2>

            <p>
                ADMINISTRATION
            </p>

        </div>


        <nav class="sidebar-menu">


            <div class="menu-title">
                Main
            </div>


            <a href="index.php">

                <span class="icon">
                    📊
                </span>

                <span>
                    Dashboard
                </span>

            </a>


            <a href="products.php" class="active">

                <span class="icon">
                    📦
                </span>

                <span>
                    Products
                </span>

            </a>


            <a href="product-add.php">

                <span class="icon">
                    ➕
                </span>

                <span>
                    Add Product
                </span>

            </a>


            <div class="menu-title">
                Catalogue
            </div>


            <a href="categories.php">

                <span class="icon">
                    📁
                </span>

                <span>
                    Categories
                </span>

            </a>


            <a href="brands.php">

                <span class="icon">
                    🏷️
                </span>

                <span>
                    Brands
                </span>

            </a>


            <a href="tags.php">

                <span class="icon">
                    #
                </span>

                <span>
                    Tags
                </span>

            </a>


            <div class="menu-title">
                Sales
            </div>


            <a href="orders.php">

                <span class="icon">
                    🛒
                </span>

                <span>
                    Orders
                </span>

            </a>


            <a href="reviews.php">

                <span class="icon">
                    ⭐
                </span>

                <span>
                    Reviews
                </span>

            </a>


            <div class="menu-title">
                System
            </div>


            <a href="../index.php">

                <span class="icon">
                    ↗
                </span>

                <span>
                    View Website
                </span>

            </a>


            <a href="logout.php">

                <span class="icon">
                    ⇥
                </span>

                <span>
                    Logout
                </span>

            </a>


        </nav>

    </aside>



    <!-- =========================================================
     MAIN
========================================================= -->

    <main class="main">


        <!-- TOPBAR -->

        <header class="topbar">


            <div class="page-title">

                <h1>
                    Products
                </h1>

                <p>
                    Manage your CORE LTD product catalogue
                </p>

            </div>


            <div class="admin-user">


                <div class="admin-avatar">

                    <?= strtoupper(
                        substr(
                            $_SESSION['admin_name'] ?? 'A',
                            0,
                            1
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
                        Product Catalogue
                    </h2>

                    <p>
                        Add, edit and manage all CORE LTD products.
                    </p>

                </div>


                <a href="product-add.php" class="btn-add">

                    <span>
                        ➕
                    </span>

                    Add Product

                </a>


            </div>



            <!-- =================================================
             STATISTICS
        ================================================== -->

            <div class="stats">


                <!-- TOTAL -->

                <div class="stat-card">

                    <div class="stat-icon">
                        📦
                    </div>

                    <div class="stat-label">
                        Total Products
                    </div>

                    <div class="stat-number">
                        <?= $totalProducts ?>
                    </div>

                </div>


                <!-- ACTIVE -->

                <div class="stat-card active-card">

                    <div class="stat-icon">
                        ✓
                    </div>

                    <div class="stat-label">
                        Active Products
                    </div>

                    <div class="stat-number">
                        <?= $activeProducts ?>
                    </div>

                </div>


                <!-- INACTIVE -->

                <div class="stat-card inactive-card">

                    <div class="stat-icon">
                        ◐
                    </div>

                    <div class="stat-label">
                        Inactive Products
                    </div>

                    <div class="stat-number">
                        <?= $inactiveProducts ?>
                    </div>

                </div>


                <!-- LOW STOCK -->

                <div class="stat-card low-card">

                    <div class="stat-icon">
                        !
                    </div>

                    <div class="stat-label">
                        Low Stock
                    </div>

                    <div class="stat-number">
                        <?= $lowStockProducts ?>
                    </div>

                </div>


                <!-- OUT OF STOCK -->

                <div class="stat-card stock-card">

                    <div class="stat-icon">
                        ×
                    </div>

                    <div class="stat-label">
                        Out of Stock
                    </div>

                    <div class="stat-number">
                        <?= $outOfStock ?>
                    </div>

                </div>


            </div>



            <!-- =================================================
             FILTERS
        ================================================== -->

            <div class="filter-box">


                <div class="filter-heading">

                    <span>
                        🔎
                    </span>

                    Search & Filter Products

                </div>


                <form method="GET" class="filter-form">


                    <div class="form-group">

                        <label>
                            Search
                        </label>

                        <input type="text" name="search" placeholder="Product name, SKU or slug..."
                            value="<?= htmlspecialchars($search) ?>">

                    </div>


                    <div class="form-group">

                        <label>
                            Category
                        </label>

                        <select name="category">

                            <option value="">
                                All Categories
                            </option>


                            <?php foreach ($categories as $category): ?>

                                <option value="<?= (int)$category['id'] ?>" <?= $categoryId === (int)$category['id']
                                                                                ? 'selected'
                                                                                : '' ?>>

                                    <?= htmlspecialchars(
                                        $category['name']
                                    ) ?>

                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>


                    <div class="form-group">

                        <label>
                            Brand
                        </label>

                        <select name="brand">

                            <option value="">
                                All Brands
                            </option>


                            <?php foreach ($brands as $brand): ?>

                                <option value="<?= (int)$brand['id'] ?>" <?= $brandId === (int)$brand['id']
                                                                                ? 'selected'
                                                                                : '' ?>>

                                    <?= htmlspecialchars(
                                        $brand['name']
                                    ) ?>

                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>


                    <div class="form-group">

                        <label>
                            Status
                        </label>

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

                    </div>


                    <button type="submit" class="btn-filter">
                        Filter
                    </button>


                    <a href="products.php" class="btn-clear">
                        Clear
                    </a>


                </form>

            </div>



            <!-- =================================================
             PRODUCTS
        ================================================== -->

            <div class="products-box">


                <div class="products-header">

                    <strong>
                        Product Inventory
                    </strong>

                    <span>

                        <?= $totalFilteredProducts ?>
                        product(s) found

                    </span>

                </div>


                <div class="table-wrapper">


                    <?php if ($result->num_rows > 0): ?>


                        <table>


                            <thead>

                                <tr>

                                    <th>
                                        Product
                                    </th>

                                    <th>
                                        Category / Brand
                                    </th>

                                    <th>
                                        Price
                                    </th>

                                    <th>
                                        Stock
                                    </th>

                                    <th>
                                        Featured
                                    </th>

                                    <th>
                                        Status
                                    </th>

                                    <th>
                                        Actions
                                    </th>

                                </tr>

                            </thead>


                            <tbody>


                                <?php while ($product = $result->fetch_assoc()): ?>


                                    <tr>


                                        <!-- PRODUCT -->

                                        <td>


                                            <div class="product-cell">


                                                <?php

                                                $imageName =
                                                    trim(
                                                        $product['main_image']
                                                            ?? ''
                                                    );


                                                $imageUrl = '';


                                                if ($imageName !== '') {

                                                    $imageUrl =
                                                        '../uploads/products/'
                                                        . $imageName;
                                                }

                                                ?>


                                                <?php if ($imageUrl !== ''): ?>

                                                    <img src="<?= htmlspecialchars($imageUrl) ?>" class="product-image" alt="<?= htmlspecialchars(
                                                                                                                                    $product['name']
                                                                                                                                ) ?>" onerror="
                                        this.style.display='none';
                                        this.nextElementSibling.style.display='flex';
                                    ">


                                                    <div class="no-image" style="display:none;">

                                                        <div class="no-image-icon">
                                                            ▧
                                                        </div>

                                                        No Image

                                                    </div>


                                                <?php else: ?>


                                                    <div class="no-image">

                                                        <div class="no-image-icon">
                                                            ▧
                                                        </div>

                                                        No Image

                                                    </div>


                                                <?php endif; ?>


                                                <div>


                                                    <div class="product-name">

                                                        <?= htmlspecialchars(
                                                            $product['name']
                                                        ) ?>

                                                    </div>


                                                    <div class="product-sku">

                                                        SKU:

                                                        <?= htmlspecialchars(
                                                            $product['sku']
                                                                ?? 'N/A'
                                                        ) ?>

                                                    </div>


                                                </div>


                                            </div>


                                        </td>



                                        <!-- CATEGORY / BRAND -->

                                        <td>

                                            <div class="category-brand">


                                                <strong>

                                                    <?= htmlspecialchars(
                                                        $product['category_name']
                                                            ?? 'Uncategorized'
                                                    ) ?>

                                                </strong>


                                                <span>

                                                    <?= htmlspecialchars(
                                                        $product['brand_name']
                                                            ?? 'No Brand'
                                                    ) ?>

                                                </span>


                                            </div>

                                        </td>



                                        <!-- PRICE -->

                                        <td>


                                            <div class="price">

                                                Ksh.

                                                <?= number_format(
                                                    (float)$product['price'],
                                                    0
                                                ) ?>

                                            </div>


                                            <?php if (
                                                !empty($product['old_price'])
                                                &&
                                                $product['old_price']
                                                >
                                                $product['price']
                                            ): ?>

                                                <div class="old-price">

                                                    Ksh.

                                                    <?= number_format(
                                                        (float)$product['old_price'],
                                                        0
                                                    ) ?>

                                                </div>

                                            <?php endif; ?>


                                        </td>



                                        <!-- STOCK -->

                                        <td>


                                            <?php

                                            $stock =
                                                (int)$product['stock_quantity'];

                                            $lowStock =
                                                (int)$product['low_stock_level'];

                                            ?>


                                            <?php if ($stock <= 0): ?>


                                                <span class="stock stock-out">

                                                    Out of Stock

                                                </span>


                                            <?php elseif ($stock <= $lowStock): ?>


                                                <span class="stock stock-low">

                                                    <?= $stock ?>

                                                    — Low

                                                </span>


                                            <?php else: ?>


                                                <span class="stock stock-good">

                                                    <?= $stock ?>

                                                    Available

                                                </span>


                                            <?php endif; ?>


                                        </td>



                                        <!-- FEATURED -->

                                        <td>


                                            <?php if (
                                                (int)$product['featured'] === 1
                                            ): ?>


                                                <span class="featured" title="Featured Product">
                                                    ★
                                                </span>


                                            <?php else: ?>


                                                <span class="not-featured" title="Not Featured">
                                                    ★
                                                </span>


                                            <?php endif; ?>


                                        </td>



                                        <!-- STATUS -->

                                        <td>


                                            <?php if (
                                                (int)$product['status'] === 1
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



                                        <!-- ACTIONS -->

                                        <td>


                                            <div class="actions">


                                                <a href="product-edit.php?id=<?= (int)$product['id'] ?>"
                                                    class="action-btn edit-btn" title="Edit Product">

                                                    ✎

                                                </a>


                                                <a href="product-delete.php?id=<?= (int)$product['id'] ?>"
                                                    class="action-btn delete-btn" title="Delete Product" onclick="
                                        return confirm(
                                            'Are you sure you want to delete this product?'
                                        );
                                    ">

                                                    ×

                                                </a>


                                            </div>


                                        </td>


                                    </tr>


                                <?php endwhile; ?>


                            </tbody>


                        </table>


                    <?php else: ?>


                        <div class="empty">


                            <div class="empty-icon">
                                📦
                            </div>


                            <h3>
                                No Products Found
                            </h3>


                            <p>
                                Try changing your search or filters,
                                or add your first product.
                            </p>


                            <a href="product-add.php" class="btn-add">

                                ➕ Add Product

                            </a>


                        </div>


                    <?php endif; ?>
                    <?php if ($totalPages > 1): ?>

                        <div class="pagination-wrapper">

                            <!-- PAGINATION INFO -->

                            <div class="pagination-info">

                                Showing

                                <strong>
                                    <?= $totalFilteredProducts > 0
                                        ? (($currentPage - 1) * $productsPerPage + 1)
                                        : 0
                                    ?>
                                </strong>

                                -

                                <strong>
                                    <?= min(
                                        $currentPage * $productsPerPage,
                                        $totalFilteredProducts
                                    ) ?>
                                </strong>

                                of

                                <strong>
                                    <?= $totalFilteredProducts ?>
                                </strong>

                                products

                            </div>


                            <!-- PAGINATION LINKS -->

                            <div class="pagination">

                                <?php

                                /*
            |--------------------------------------------------------------------------
            | PREVIOUS
            |--------------------------------------------------------------------------
            */

                                if ($currentPage > 1):

                                    $previousParams = $_GET;

                                    $previousParams['page'] =
                                        $currentPage - 1;

                                ?>

                                    <a class="prev-next" href="?<?= htmlspecialchars(
                                                                    http_build_query($previousParams)
                                                                ) ?>">
                                        ← Previous
                                    </a>

                                <?php else: ?>

                                    <span class="disabled prev-next">
                                        ← Previous
                                    </span>

                                <?php endif; ?>


                                <?php

                                /*
            |--------------------------------------------------------------------------
            | PAGE NUMBERS
            |--------------------------------------------------------------------------
            */

                                $startPage = max(
                                    1,
                                    $currentPage - 2
                                );

                                $endPage = min(
                                    $totalPages,
                                    $currentPage + 2
                                );

                                ?>


                                <?php if ($startPage > 1): ?>

                                    <?php

                                    $firstParams = $_GET;

                                    $firstParams['page'] = 1;

                                    ?>

                                    <a href="?<?= htmlspecialchars(
                                                    http_build_query($firstParams)
                                                ) ?>">
                                        1
                                    </a>

                                    <?php if ($startPage > 2): ?>

                                        <span>
                                            ...
                                        </span>

                                    <?php endif; ?>

                                <?php endif; ?>


                                <?php for (
                                    $page = $startPage;
                                    $page <= $endPage;
                                    $page++
                                ): ?>

                                    <?php

                                    $pageParams = $_GET;

                                    $pageParams['page'] = $page;

                                    ?>

                                    <?php if ($page === $currentPage): ?>

                                        <span class="active">
                                            <?= $page ?>
                                        </span>

                                    <?php else: ?>

                                        <a href="?<?= htmlspecialchars(
                                                        http_build_query($pageParams)
                                                    ) ?>">
                                            <?= $page ?>
                                        </a>

                                    <?php endif; ?>

                                <?php endfor; ?>


                                <?php if ($endPage < $totalPages): ?>

                                    <?php if ($endPage < $totalPages - 1): ?>

                                        <span>
                                            ...
                                        </span>

                                    <?php endif; ?>


                                    <?php

                                    $lastParams = $_GET;

                                    $lastParams['page'] =
                                        $totalPages;

                                    ?>

                                    <a href="?<?= htmlspecialchars(
                                                    http_build_query($lastParams)
                                                ) ?>">
                                        <?= $totalPages ?>
                                    </a>

                                <?php endif; ?>


                                <?php

                                /*
            |--------------------------------------------------------------------------
            | NEXT
            |--------------------------------------------------------------------------
            */

                                if ($currentPage < $totalPages):

                                    $nextParams = $_GET;

                                    $nextParams['page'] =
                                        $currentPage + 1;

                                ?>

                                    <a class="prev-next" href="?<?= htmlspecialchars(
                                                                    http_build_query($nextParams)
                                                                ) ?>">
                                        Next →
                                    </a>

                                <?php else: ?>

                                    <span class="disabled prev-next">
                                        Next →
                                    </span>

                                <?php endif; ?>

                            </div>

                        </div>

                    <?php endif; ?>


                </div>


            </div>


        </div>


    </main>


</body>

</html>


<?php

$stmt->close();

?>