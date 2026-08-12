<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
function e($v)
{
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}
function safeImageName($name)
{
    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    return time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
}
function uniqueSlug($conn, $name, $id)
{
    // Convert to lowercase FIRST
    $name = strtolower(trim($name));

    // Replace anything that is not a-z or 0-9 with a hyphen
    $base = preg_replace('/[^a-z0-9]+/', '-', $name);

    // Remove hyphens from beginning and end
    $base = trim($base, '-');

    // Fallback
    if ($base === '') {
        $base = 'product';
    }

    $slug = $base;
    $counter = 1;

    $stmt = $conn->prepare("
        SELECT id
        FROM products
        WHERE slug = ?
        AND id != ?
        LIMIT 1
    ");

    while (true) {

        $stmt->bind_param("si", $slug, $id);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            break;
        }

        $counter++;
        $slug = $base . '-' . $counter;
    }

    $stmt->close();

    return $slug;
}
$id = (int)($_GET['id'] ?? $_POST['product_id'] ?? 0);
if ($id < 1) {
    header('Location: products.php');
    exit;
}
$dir = __DIR__ . '/../uploads/products/';
if (!is_dir($dir)) mkdir($dir, 0755, true);
$allowed = ['jpg', 'jpeg', 'png', 'webp'];
$max = 5 * 1024 * 1024;
$categories = [];
$q = $conn->query('SELECT id,name FROM categories ORDER BY name');
while ($r = $q->fetch_assoc()) $categories[] = $r;
$brands = [];
$q = $conn->query('SELECT id,name FROM brands ORDER BY name');
while ($r = $q->fetch_assoc()) $brands[] = $r;
function loadProduct($conn, $id)
{
    $s = $conn->prepare('SELECT * FROM products WHERE id=? LIMIT 1');
    $s->bind_param('i', $id);
    $s->execute();
    $r = $s->get_result()->fetch_assoc();
    $s->close();
    return $r;
}
function loadImages($conn, $id)
{
    $a = [];
    $s = $conn->prepare('SELECT * FROM product_images WHERE product_id=? ORDER BY sort_order,id');
    $s->bind_param('i', $id);
    $s->execute();
    $r = $s->get_result();
    while ($x = $r->fetch_assoc()) $a[] = $x;
    $s->close();
    return $a;
}
function loadFeatures($conn, $id)
{
    $a = [];
    $s = $conn->prepare('SELECT * FROM product_features WHERE product_id=? ORDER BY sort_order,id');
    $s->bind_param('i', $id);
    $s->execute();
    $r = $s->get_result();
    while ($x = $r->fetch_assoc()) $a[] = $x;
    $s->close();
    return $a;
}
$product = loadProduct($conn, $id);
if (!$product) {
    header('Location: products.php');
    exit;
}
$images = loadImages($conn, $id);
$features = loadFeatures($conn, $id);
if (!$features) $features = [['feature' => '', 'benefit' => '']];
$errors = [];
$updated = isset($_GET['updated']);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uploaded = [];
    $deleteFiles = [];
    try {
        $category = (int)($_POST['category_id'] ?? 0);
        $brand = (int)($_POST['brand_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $sku = trim($_POST['sku'] ?? '');
        $short = trim($_POST['short_description'] ?? '');
        $desc = trim($_POST['description'] ?? '');
        $price = (float)($_POST['price'] ?? 0);
        $old = trim($_POST['old_price'] ?? '') === '' ? null : (float)$_POST['old_price'];
        $cost = trim($_POST['cost_price'] ?? '') === '' ? null : (float)$_POST['cost_price'];
        $stock = (int)($_POST['stock_quantity'] ?? 0);
        $low = (int)($_POST['low_stock_level'] ?? 5);
        $featured = isset($_POST['featured']) ? 1 : 0;
        $status = isset($_POST['status']) ? 1 : 0;
        $mt = trim($_POST['meta_title'] ?? '');
        $md = trim($_POST['meta_description'] ?? '');
        $mk = trim($_POST['meta_keywords'] ?? '');
        if (!$name) throw new Exception('Product name is required.');
        if ($category < 1) throw new Exception('Please select a category.');
        if ($brand < 1) throw new Exception('Please select a brand.');
        if ($price < 0 || $stock < 0) throw new Exception('Price and stock cannot be negative.');
        /*
|--------------------------------------------------------------------------
| SLUG
|--------------------------------------------------------------------------
| Always generate the correct slug from the current product name.
| This also repairs old/corrupted slugs such as:
|
| enda-5-3-1200-ireless-ual-and-i-i-outer
|
| into:
|
| tenda-ac5-v3-ac1200-wireless-dual-band-wi-fi-router
|--------------------------------------------------------------------------
*/

        $correctSlug = uniqueSlug($conn, $name, $id);

        $currentSlug = trim((string)($product['slug'] ?? ''));

        if ($currentSlug === '' || $currentSlug !== $correctSlug) {
            $slug = $correctSlug;
        } else {
            $slug = $currentSlug;
        }
        $conn->begin_transaction();
        $main = $product['main_image'];
        $newMain = null;
        if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['main_image']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed, true)) throw new Exception('Invalid main image format.');
            if ($_FILES['main_image']['size'] > $max) throw new Exception('Main image must not exceed 5MB.');
            $newMain = safeImageName($_FILES['main_image']['name']);
            $dest = $dir . $newMain;
            if (!move_uploaded_file($_FILES['main_image']['tmp_name'], $dest)) throw new Exception('Unable to save main image.');
            $uploaded[] = $dest;
            if ($main) $deleteFiles[] = $dir . $main;
            $main = $newMain;
        }
        $s = $conn->prepare('UPDATE products SET category_id=?,brand_id=?,name=?,slug=?,sku=?,short_description=?,description=?,price=?,old_price=?,cost_price=?,stock_quantity=?,low_stock_level=?,main_image=?,featured=?,status=?,meta_title=?,meta_description=?,meta_keywords=?,updated_at=NOW() WHERE id=?');
        $s->bind_param('iisssssdddiisiisssi', $category, $brand, $name, $slug, $sku, $short, $desc, $price, $old, $cost, $stock, $low, $main, $featured, $status, $mt, $md, $mk, $id);
        if (!$s->execute()) throw new Exception($s->error);
        $s->close();
        if ($newMain !== null) {
            $alt = $name;
            $s = $conn->prepare('UPDATE product_images SET image=?,alt_text=?,sort_order=1,is_primary=1 WHERE product_id=? AND is_primary=1 LIMIT 1');
            $s->bind_param('ssi', $main, $alt, $id);
            $s->execute();
            $affected = $s->affected_rows;
            $s->close();
            if (!$affected) {
                $s = $conn->prepare('INSERT INTO product_images(product_id,image,alt_text,sort_order,is_primary,created_at) VALUES(?,?,?,1,1,NOW())');
                $s->bind_param('iss', $id, $main, $alt);
                if (!$s->execute()) throw new Exception($s->error);
                $s->close();
            }
        }
        $remove = $_POST['remove_images'] ?? [];
        if (is_array($remove) && $remove) {
            $get = $conn->prepare('SELECT image FROM product_images WHERE id=? AND product_id=? AND is_primary=0 LIMIT 1');
            $del = $conn->prepare('DELETE FROM product_images WHERE id=? AND product_id=? AND is_primary=0');
            foreach ($remove as $iid) {
                $iid = (int)$iid;
                $get->bind_param('ii', $iid, $id);
                $get->execute();
                $row = $get->get_result()->fetch_assoc();
                if ($row) {
                    $del->bind_param('ii', $iid, $id);
                    $del->execute();
                    if (!empty($row['image'])) $deleteFiles[] = $dir . $row['image'];
                }
            }
            $get->close();
            $del->close();
        }
        if (isset($_FILES['additional_images']['name']) && is_array($_FILES['additional_images']['name'])) {
            $s = $conn->prepare('SELECT COALESCE(MAX(sort_order),1) m FROM product_images WHERE product_id=?');
            $s->bind_param('i', $id);
            $s->execute();
            $sort = (int)$s->get_result()->fetch_assoc()['m'] + 1;
            $s->close();
            $ins = $conn->prepare('INSERT INTO product_images(product_id,image,alt_text,sort_order,is_primary,created_at) VALUES(?,?,?,?,0,NOW())');
            foreach ($_FILES['additional_images']['name'] as $k => $orig) {
                if ($orig === '') continue;
                if ($_FILES['additional_images']['error'][$k] !== UPLOAD_ERR_OK) throw new Exception('Unable to upload an additional image.');
                $ext = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
                if (!in_array($ext, $allowed, true)) throw new Exception('Invalid additional image format.');
                if ($_FILES['additional_images']['size'][$k] > $max) throw new Exception('An additional image exceeds 5MB.');
                $img = safeImageName($orig);
                $dest = $dir . $img;
                if (!move_uploaded_file($_FILES['additional_images']['tmp_name'][$k], $dest)) throw new Exception('Unable to save additional image.');
                $uploaded[] = $dest;
                $alt = $name . ' - Image ' . $sort;
                $ins->bind_param('issi', $id, $img, $alt, $sort);
                if (!$ins->execute()) throw new Exception($ins->error);
                $sort++;
            }
            $ins->close();
        }
        $s = $conn->prepare('DELETE FROM product_features WHERE product_id=?');
        $s->bind_param('i', $id);
        if (!$s->execute()) throw new Exception($s->error);
        $s->close();
        $fs = $_POST['feature'] ?? [];
        $bs = $_POST['benefit'] ?? [];
        $ins = $conn->prepare('INSERT INTO product_features(product_id,feature,benefit,sort_order,created_at) VALUES(?,?,?,?,NOW())');
        $sort = 1;
        if (is_array($fs)) foreach ($fs as $k => $fv) {
            $f = trim((string)$fv);
            $b = trim((string)($bs[$k] ?? ''));
            if ($f === '' && $b === '') continue;
            if ($f === '' || $b === '') throw new Exception('Each feature must have both a Feature and a Benefit.');
            $ins->bind_param('issi', $id, $f, $b, $sort);
            if (!$ins->execute()) throw new Exception($ins->error);
            $sort++;
        }
        $ins->close();
        $conn->commit();
        foreach ($deleteFiles as $f) if (is_file($f)) @unlink($f);
        header('Location: product-edit.php?id=' . $id . '&updated=1');
        exit;
    } catch (Throwable $e) {
        $conn->rollback();
        foreach ($uploaded as $f) if (is_file($f)) @unlink($f);
        $errors[] = $e->getMessage();
        $product = loadProduct($conn, $id);
        $images = loadImages($conn, $id);
        $features = loadFeatures($conn, $id);
        if (!$features) $features = [['feature' => '', 'benefit' => '']];
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>CORE LTD - Edit Product</title>
    <style>
    * {
        box-sizing: border-box
    }

    body {
        margin: 0;
        font-family: Arial, Helvetica, sans-serif;
        background: #f3f7fc;
        color: #1d344e
    }

    .sidebar {
        position: fixed;
        left: 0;
        top: 0;
        width: 250px;
        height: 100vh;
        background: linear-gradient(180deg, #063f78, #075eaa 55%, #0875d1);
        color: #fff;
        padding: 22px 15px;
        box-shadow: 4px 0 18px rgba(0, 61, 120, .15);
        z-index: 10
    }

    .sidebar-logo {
        text-align: center;
        padding: 7px 10px 25px;
        border-bottom: 1px solid rgba(255, 255, 255, .15)
    }

    .sidebar-logo h2 {
        margin: 0;
        font-size: 25px;
        letter-spacing: 1.5px
    }

    .sidebar-logo p {
        margin: 6px 0 0;
        color: rgba(255, 255, 255, .75);
        font-size: 11px;
        letter-spacing: 1px
    }

    .menu-title {
        color: rgba(255, 255, 255, .55);
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: 1.3px;
        margin: 20px 12px 8px;
        font-weight: 700
    }

    .sidebar-menu a {
        display: flex;
        align-items: center;
        gap: 11px;
        padding: 11px 12px;
        margin-bottom: 4px;
        border-radius: 7px;
        color: rgba(255, 255, 255, .86);
        text-decoration: none;
        font-size: 12px;
        font-weight: 600
    }

    .sidebar-menu a:hover,
    .sidebar-menu a.active {
        background: rgba(255, 255, 255, .14);
        color: #fff
    }

    .icon {
        width: 20px;
        text-align: center
    }

    .main {
        margin-left: 250px;
        min-height: 100vh
    }

    .topbar {
        height: 76px;
        background: #fff;
        border-bottom: 1px solid #e6edf5;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 30px
    }

    .page-title h1 {
        margin: 0;
        font-size: 21px;
        color: #143b61
    }

    .page-title p {
        margin: 4px 0 0;
        font-size: 11px;
        color: #7890a7
    }

    .admin-user {
        display: flex;
        align-items: center;
        gap: 10px
    }

    .admin-avatar {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: linear-gradient(135deg, #087ce9, #0756a5);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        font-weight: 800
    }

    .admin-info strong,
    .admin-info span {
        display: block
    }

    .admin-info strong {
        color: #244b6f;
        font-size: 12px
    }

    .admin-info span {
        color: #8ba0b4;
        font-size: 10px;
        margin-top: 2px
    }

    .content {
        padding: 28px
    }

    .content-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 22px
    }

    .content-header h2 {
        margin: 0;
        color: #153f65;
        font-size: 20px
    }

    .content-header p {
        margin: 5px 0 0;
        color: #7890a7;
        font-size: 11px
    }

    .back {
        padding: 11px 16px;
        border: 1px solid #dce7f2;
        border-radius: 7px;
        background: #fff;
        color: #315a7e;
        text-decoration: none;
        font-size: 11px;
        font-weight: 700
    }

    .alert {
        border-radius: 8px;
        padding: 14px 16px;
        margin-bottom: 18px;
        font-size: 12px
    }

    .success {
        background: #eaf8f0;
        color: #1c7b4b;
        border: 1px solid #c8edd8
    }

    .error {
        background: #fff0f1;
        color: #b52d3a;
        border: 1px solid #f2cbd0
    }

    .layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 330px;
        gap: 20px
    }

    .card {
        background: #fff;
        border: 1px solid #e2eaf3;
        border-radius: 10px;
        margin-bottom: 20px;
        box-shadow: 0 4px 18px rgba(25, 76, 120, .05);
        overflow: hidden
    }

    .head {
        display: flex;
        align-items: center;
        gap: 11px;
        padding: 17px 19px;
        border-bottom: 1px solid #edf2f7
    }

    .head .ico {
        width: 34px;
        height: 34px;
        border-radius: 7px;
        background: #eaf4ff;
        color: #087ce9;
        display: flex;
        align-items: center;
        justify-content: center
    }

    .head h3 {
        margin: 0;
        color: #244d72;
        font-size: 13px
    }

    .head p {
        margin: 3px 0 0;
        color: #8ba0b4;
        font-size: 10px
    }

    .body {
        padding: 20px
    }

    .grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px
    }

    .full {
        grid-column: 1/-1
    }

    label {
        display: block;
        margin-bottom: 7px;
        color: #355b7d;
        font-size: 11px;
        font-weight: 700
    }

    input,
    textarea,
    select {
        width: 100%;
        border: 1px solid #d9e4ee;
        border-radius: 7px;
        background: #fff;
        color: #254967;
        padding: 11px 12px;
        font-family: inherit;
        font-size: 12px;
        outline: 0
    }

    input:focus,
    textarea:focus,
    select:focus {
        border-color: #0a80e9;
        box-shadow: 0 0 0 3px rgba(8, 124, 233, .08)
    }

    textarea {
        min-height: 110px;
        resize: vertical
    }

    .hint {
        color: #91a2b3;
        font-size: 9px;
        margin-top: 5px
    }

    .switch {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid #edf2f7
    }

    .switch:last-child {
        border: 0
    }

    .switch strong,
    .switch span {
        display: block
    }

    .switch strong {
        font-size: 11px;
        color: #315a7e
    }

    .switch span {
        font-size: 9px;
        color: #91a2b3;
        margin-top: 3px
    }

    .switch input {
        width: 18px;
        height: 18px;
        accent-color: #087ce9
    }

    .features {
        background: #f8fbff;
        border: 1px solid #dfeaf5;
        border-radius: 9px;
        padding: 15px;
        margin-bottom: 12px
    }

    .fhead {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px
    }

    .fhead strong {
        font-size: 11px;
        color: #0875df
    }

    .remove,
    .add {
        border: 0;
        border-radius: 6px;
        padding: 8px 11px;
        font-size: 9px;
        font-weight: 700;
        cursor: pointer
    }

    .remove {
        background: #fff0f0;
        color: #c23c47
    }

    .add {
        background: #eaf4ff;
        color: #0875df;
        border: 1px solid #cfe5fb
    }

    .fgrid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px
    }

    .gallery {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px
    }

    .gitem {
        position: relative;
        border: 1px solid #dfe8f1;
        border-radius: 8px;
        padding: 7px;
        background: #fff
    }

    .gitem.primary {
        border-color: #087ce9
    }

    .gitem img {
        width: 100%;
        height: 100px;
        object-fit: contain;
        border-radius: 5px
    }

    .gname {
        display: block;
        font-size: 8px;
        color: #5f7890;
        word-break: break-word;
        margin-top: 5px
    }

    .primary {
        position: absolute;
        top: 10px;
        left: 10px;
        background: #087ce9;
        color: #fff;
        padding: 4px 6px;
        border-radius: 4px;
        font-size: 8px;
        font-weight: 700
    }

    .delete {
        display: flex;
        gap: 5px;
        align-items: center;
        color: #c33d49;
        font-size: 9px;
        margin-top: 7px
    }

    .delete input {
        width: auto
    }

    .savebar {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding: 16px 20px;
        background: #fff;
        border: 1px solid #e2eaf3;
        border-radius: 10px
    }

    .cancel,
    .save {
        height: 43px;
        padding: 0 22px;
        border-radius: 7px;
        display: inline-flex;
        align-items: center;
        text-decoration: none;
        font-size: 11px;
        font-weight: 700
    }

    .cancel {
        background: #f1f5f9;
        color: #61748b
    }

    .save {
        border: 0;
        background: linear-gradient(135deg, #087ce9, #0756a5);
        color: #fff;
        cursor: pointer
    }

    @media(max-width:1050px) {
        .layout {
            grid-template-columns: 1fr
        }
    }

    @media(max-width:900px) {
        .sidebar {
            width: 72px;
            padding: 20px 8px
        }

        .sidebar-logo h2 {
            font-size: 15px
        }

        .sidebar-logo p,
        .menu-title,
        .sidebar-menu a span:not(.icon) {
            display: none
        }

        .sidebar-menu a {
            justify-content: center
        }

        .main {
            margin-left: 72px
        }
    }

    @media(max-width:650px) {
        .content {
            padding: 18px
        }

        .topbar {
            padding: 0 18px
        }

        .admin-info {
            display: none
        }

        .content-header {
            align-items: flex-start;
            flex-direction: column
        }

        .grid,
        .fgrid {
            grid-template-columns: 1fr
        }

        .gallery {
            grid-template-columns: repeat(2, 1fr)
        }
    }
    </style>
</head>

<body>
    <aside class="sidebar">
        <div class="sidebar-logo">
            <h2>CORE LTD</h2>
            <p>ADMINISTRATION</p>
        </div>
        <nav class="sidebar-menu">
            <div class="menu-title">Main</div><a href="index.php"><span
                    class="icon">▦</span><span>Dashboard</span></a><a href="products.php" class="active"><span
                    class="icon">▣</span><span>Products</span></a><a href="product-add.php"><span
                    class="icon">＋</span><span>Add Product</span></a>
            <div class="menu-title">Catalogue</div><a href="categories.php"><span
                    class="icon">◫</span><span>Categories</span></a><a href="brands.php"><span
                    class="icon">◇</span><span>Brands</span></a><a href="tags.php"><span
                    class="icon">#</span><span>Tags</span></a>
            <div class="menu-title">Sales</div><a href="orders.php"><span class="icon">▤</span><span>Orders</span></a><a
                href="reviews.php"><span class="icon">☆</span><span>Reviews</span></a>
            <div class="menu-title">System</div><a href="../index.php"><span class="icon">↗</span><span>View
                    Website</span></a><a href="logout.php"><span class="icon">⇥</span><span>Logout</span></a>
        </nav>
    </aside>
    <main class="main">
        <header class="topbar">
            <div class="page-title">
                <h1>Edit Product</h1>
                <p>Update your CORE LTD product catalogue</p>
            </div>
            <div class="admin-user">
                <div class="admin-avatar"><?= strtoupper(substr($_SESSION['admin_name'] ?? 'A', 0, 1)) ?></div>
                <div class="admin-info">
                    <strong><?= e($_SESSION['admin_name'] ?? 'Administrator') ?></strong><span>Administrator</span>
                </div>
            </div>
        </header>
        <div class="content">
            <div class="content-header">
                <div>
                    <h2>Edit: <?= e($product['name']) ?></h2>
                    <p>Update details, pricing, stock, images, features and benefits.</p>
                </div><a class="back" href="products.php">← Back to Products</a>
            </div>
            <?php if ($updated): ?><div class="alert success">Product updated successfully.</div>
            <?php endif; ?><?php if ($errors): ?><div class="alert error"><strong>Please correct the following:</strong>
                <ul><?php foreach ($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?></ul>
            </div><?php endif; ?>
            <form method="POST" enctype="multipart/form-data"><input type="hidden" name="product_id" value="<?= $id ?>">
                <div class="layout">
                    <div>
                        <div class="card">
                            <div class="head">
                                <div class="ico">▣</div>
                                <div>
                                    <h3>Product Information</h3>
                                    <p>Basic product details</p>
                                </div>
                            </div>
                            <div class="body">
                                <div class="grid">
                                    <div class="full"><label>Product Name *</label><input name="name"
                                            value="<?= e($product['name']) ?>" required></div>
                                    <div><label>SKU</label><input name="sku" value="<?= e($product['sku']) ?>"></div>
                                    <div><label>Category *</label><select name="category_id" required>
                                            <option value="">Select Category</option>
                                            <?php foreach ($categories as $c): ?><option value="<?= $c['id'] ?>"
                                                <?= $product['category_id'] == $c['id'] ? 'selected' : '' ?>>
                                                <?= e($c['name']) ?></option><?php endforeach; ?>
                                        </select></div>
                                    <div><label>Brand *</label><select name="brand_id" required>
                                            <option value="">Select Brand</option><?php foreach ($brands as $b): ?>
                                            <option value="<?= $b['id'] ?>"
                                                <?= $product['brand_id'] == $b['id'] ? 'selected' : '' ?>>
                                                <?= e($b['name']) ?></option><?php endforeach; ?>
                                        </select></div>
                                    <div class="full"><label>Short Description</label><textarea
                                            name="short_description"><?= e($product['short_description']) ?></textarea>
                                    </div>
                                    <div class="full"><label>Full Description</label><textarea name="description"
                                            rows="8"><?= e($product['description']) ?></textarea></div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="head">
                                <div class="ico">K</div>
                                <div>
                                    <h3>Pricing & Stock</h3>
                                    <p>Manage price and inventory</p>
                                </div>
                            </div>
                            <div class="body">
                                <div class="grid">
                                    <div><label>Selling Price *</label><input type="number" name="price" min="0"
                                            step="0.01" value="<?= e($product['price']) ?>" required></div>
                                    <div><label>Old Price</label><input type="number" name="old_price" min="0"
                                            step="0.01" value="<?= e($product['old_price']) ?>"></div>
                                    <div><label>Cost Price</label><input type="number" name="cost_price" min="0"
                                            step="0.01" value="<?= e($product['cost_price']) ?>"></div>
                                    <div><label>Stock Quantity</label><input type="number" name="stock_quantity" min="0"
                                            value="<?= $product['stock_quantity'] ?>"></div>
                                    <div><label>Low Stock Level</label><input type="number" name="low_stock_level"
                                            min="0" value="<?= $product['low_stock_level'] ?>"></div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="head">
                                <div class="ico">★</div>
                                <div>
                                    <h3>Product Features</h3>
                                    <p>Edit feature and benefit pairs</p>
                                </div>
                            </div>
                            <div class="body">
                                <div id="features"><?php foreach ($features as $i => $f): ?><div class="features">
                                        <div class="fhead"><strong>Feature <?= ($i + 1) ?></strong><button type="button"
                                                class="remove" onclick="removeFeature(this)">Remove</button></div>
                                        <div class="fgrid">
                                            <div><label>Feature</label><input name="feature[]"
                                                    value="<?= e($f['feature'] ?? '') ?>"
                                                    placeholder="e.g. 300Mbps Wireless Speed"></div>
                                            <div><label>Benefit</label><input name="benefit[]"
                                                    value="<?= e($f['benefit'] ?? '') ?>"
                                                    placeholder="e.g. Enjoy smooth browsing and streaming"></div>
                                        </div>
                                    </div><?php endforeach; ?></div><button type="button" class="add"
                                    onclick="addFeature()">+ Add Another Feature</button>
                            </div>
                        </div>
                        <div class="card">
                            <div class="head">
                                <div class="ico">▧</div>
                                <div>
                                    <h3>Product Images</h3>
                                    <p>Manage existing images and upload new ones</p>
                                </div>
                            </div>
                            <div class="body"><label>Current Images</label><?php if ($images): ?><div class="gallery">
                                    <?php foreach ($images as $im): ?><div
                                        class="gitem <?= $im['is_primary'] ? 'primary' : '' ?>">
                                        <?php if ($im['is_primary']): ?><span
                                            class="primary">MAIN</span><?php endif; ?><img
                                            src="../uploads/products/<?= e($im['image']) ?>"
                                            alt="<?= e($im['alt_text']) ?>"><span
                                            class="gname"><?= e($im['image']) ?></span><?php if (!$im['is_primary']): ?><label
                                            class="delete"><input type="checkbox" name="remove_images[]"
                                                value="<?= $im['id'] ?>"> Remove</label><?php endif; ?></div>
                                    <?php endforeach; ?></div><?php else: ?><div class="hint">No product images found.
                                </div><?php endif; ?>
                                <hr style="border:0;border-top:1px solid #edf2f7;margin:20px 0"><label>Replace Main
                                    Image</label><input type="file" name="main_image" id="mainImage"
                                    accept=".jpg,.jpeg,.png,.webp">
                                <div class="hint">JPG, JPEG, PNG or WEBP. Maximum 5MB.</div>
                                <div id="mainPreview" style="display:none;margin-top:10px"><img id="mainPreviewImg"
                                        style="width:110px;height:110px;object-fit:contain;border:1px solid #dfe8f1;border-radius:7px">
                                </div>
                                <hr style="border:0;border-top:1px solid #edf2f7;margin:20px 0"><label>Add More
                                    Images</label><input type="file" name="additional_images[]" id="additional"
                                    accept=".jpg,.jpeg,.png,.webp" multiple>
                                <div class="hint">Select multiple images at once. Maximum 5MB per image.</div>
                                <div id="newGallery" class="gallery" style="margin-top:12px"></div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="head">
                                <div class="ico">SEO</div>
                                <div>
                                    <h3>SEO Information</h3>
                                    <p>Search engine information</p>
                                </div>
                            </div>
                            <div class="body">
                                <div class="grid">
                                    <div class="full"><label>Meta Title</label><input name="meta_title"
                                            value="<?= e($product['meta_title']) ?>"></div>
                                    <div class="full"><label>Meta Description</label><textarea
                                            name="meta_description"><?= e($product['meta_description']) ?></textarea>
                                    </div>
                                    <div class="full"><label>Meta Keywords</label><input name="meta_keywords"
                                            value="<?= e($product['meta_keywords']) ?>"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="card">
                            <div class="head">
                                <div class="ico">✓</div>
                                <div>
                                    <h3>Product Status</h3>
                                    <p>Control website visibility</p>
                                </div>
                            </div>
                            <div class="body">
                                <div class="switch">
                                    <div><strong>Active</strong><span>Show product on website</span></div><input
                                        type="checkbox" name="status" value="1"
                                        <?= $product['status'] ? 'checked' : '' ?>>
                                </div>
                                <div class="switch">
                                    <div><strong>Featured</strong><span>Highlight product in catalogue</span></div>
                                    <input type="checkbox" name="featured" value="1"
                                        <?= $product['featured'] ? 'checked' : '' ?>>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="head">
                                <div class="ico">ℹ</div>
                                <div>
                                    <h3>Product Summary</h3>
                                    <p>Current information</p>
                                </div>
                            </div>
                            <div class="body">
                                <div class="hint">Product ID</div><strong>#<?= $product['id'] ?></strong>
                                <hr style="border:0;border-top:1px solid #edf2f7;margin:14px 0">
                                <div class="hint">Current Slug</div><strong
                                    style="font-size:11px;word-break:break-word"><?= e($product['slug']) ?></strong>
                                <hr style="border:0;border-top:1px solid #edf2f7;margin:14px 0">
                                <div class="hint">Views</div>
                                <strong><?= number_format((int)$product['views']) ?></strong>
                                <hr style="border:0;border-top:1px solid #edf2f7;margin:14px 0">
                                <div class="hint">Last Updated</div><strong
                                    style="font-size:10px"><?= e($product['updated_at']) ?></strong>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="savebar"><a class="cancel" href="products.php">Cancel</a><button class="save"
                        type="submit">Save Product Changes</button></div>
            </form>
        </div>
    </main>
    <script>
    function renumber() {
        document.querySelectorAll('.features').forEach((r, i) => r.querySelector('.fhead strong').textContent =
            'Feature ' + (i + 1));
    }

    function addFeature() {
        let c = document.getElementById('features'),
            r = document.createElement('div');
        r.className = 'features';
        r.innerHTML =
            '<div class="fhead"><strong>Feature</strong><button type="button" class="remove" onclick="removeFeature(this)">Remove</button></div><div class="fgrid"><div><label>Feature</label><input name="feature[]" placeholder="e.g. 300Mbps Wireless Speed"></div><div><label>Benefit</label><input name="benefit[]" placeholder="e.g. Enjoy smooth browsing and streaming"></div></div>';
        c.appendChild(r);
        renumber();
    }

    function removeFeature(b) {
        let rows = document.querySelectorAll('.features');
        if (rows.length > 1) b.closest('.features').remove();
        else {
            b.closest('.features').querySelectorAll('input').forEach(i => i.value = '');
        }
        renumber();
    }
    const mi = document.getElementById('mainImage');
    mi?.addEventListener('change', () => {
        let f = mi.files[0],
            p = document.getElementById('mainPreview'),
            im = document.getElementById('mainPreviewImg');
        if (!f) {
            p.style.display = 'none';
            return
        }
        let r = new FileReader();
        r.onload = e => {
            im.src = e.target.result;
            p.style.display = 'block'
        };
        r.readAsDataURL(f)
    });
    const ai = document.getElementById('additional'),
        ng = document.getElementById('newGallery');
    ai?.addEventListener('change', () => {
        ng.innerHTML = '';
        Array.from(ai.files).forEach(f => {
            let r = new FileReader();
            r.onload = e => {
                let d = document.createElement('div');
                d.className = 'gitem';
                d.innerHTML = '<img src="' + e.target.result + '"><span class="gname">' + f.name +
                    '</span>';
                ng.appendChild(d)
            };
            r.readAsDataURL(f)
        })
    });
    </script>
</body>

</html>