<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Lấy danh sách voucher của người dùng
$stmt = $pdo->prepare("
    SELECT v.id, v.code, v.discount, v.description, v.min_order_value, v.expiry_date
    FROM user_vouchers uv
    JOIN vouchers v ON uv.voucher_id = v.id
    WHERE uv.user_id = ? AND v.status = 'active'
");
$stmt->execute([$user_id]);
$vouchers_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Lấy thông tin người dùng hiện tại TRƯỚC TIÊN
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute(['id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (empty($user)) {
    session_destroy();
    header("Location: login.php");
    exit;
}

if (empty($user['avatar'])) {
    $user['avatar'] = 'avatars/default-avatar.png';
}

// Xử lý xóa tài khoản
if (isset($_POST['delete_account'])) {
    $stmt = $pdo->prepare("SELECT avatar FROM users WHERE id = :id");
    $stmt->execute(['id' => $user_id]);
    $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user_data['avatar'] && $user_data['avatar'] !== 'avatars/default-avatar.png' && file_exists("../Asset/Images/" . $user_data['avatar'])) {
        unlink("../Asset/Images/" . $user_data['avatar']);
    }

    $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
    $stmt->execute(['id' => $user_id]);

    session_destroy();
    header("Location: ../index.php");
    exit;
}

// Xử lý xóa sản phẩm (Admin)
if ($user['role'] === 'admin' && isset($_POST['delete_product'])) {
    $product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);

    if ($product_id && $product_id > 0) {
        // Xóa ảnh sản phẩm nếu tồn tại
        $stmt = $pdo->prepare("SELECT image FROM products WHERE id = :id");
        $stmt->execute(['id' => $product_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product && $product['image'] && file_exists("../" . $product['image'])) {
            unlink("../" . $product['image']);
        }

        // Xóa sản phẩm từ bảng products
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = :id");
        $stmt->execute(['id' => $product_id]);

        // Tính lại số trang sau khi xóa
        $items_per_page = 5;
        $page_products = isset($_GET['page_products']) ? max(1, (int)$_GET['page_products']) : 1;
        $total_products = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
        $total_pages_products = ceil($total_products / $items_per_page);
        $current_page = min($page_products, $total_pages_products ?: 1);

        header("Location: " . $_SERVER['PHP_SELF'] . "?tab=products&page_products=" . $current_page);
        exit;
    } else {
        echo "ID sản phẩm không hợp lệ.";
    }
}

// Xử lý cập nhật thông tin cá nhân
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $full_name = filter_input(INPUT_POST, 'full_name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
    $gender = $_POST['gender'] ?? $user['gender'];
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $user['password'];

    $avatar = $user['avatar'];
    $upload_dir = '../Asset/Images/avatars/';
    if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);

    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $file_name = $_FILES['avatar']['name'];
        $file_tmp = $_FILES['avatar']['tmp_name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $new_file_name = 'avatar_' . $user_id . '_' . time() . '.' . $file_ext;
        $new_file_path = $upload_dir . $new_file_name;

        if ($avatar && $avatar !== 'avatars/default-avatar.png' && file_exists("../Asset/Images/" . $avatar)) {
            unlink("../Asset/Images/" . $avatar);
        }
        if (move_uploaded_file($file_tmp, $new_file_path)) {
            $avatar = 'avatars/' . $new_file_name;
        }
    }

    $stmt = $pdo->prepare("UPDATE users SET full_name = :full_name, email = :email, password = :password, address = :address, gender = :gender, avatar = :avatar WHERE id = :id");
    $stmt->execute([
        'full_name' => $full_name,
        'email' => $email,
        'password' => $password,
        'address' => $address,
        'gender' => $gender,
        'avatar' => $avatar,
        'id' => $user_id
    ]);

    $_SESSION['full_name'] = $full_name;
    $_SESSION['avatar'] = $avatar;

    header("Location: " . $_SERVER['PHP_SELF'] . "?tab=profile");
    exit;
}

// Xử lý thêm sản phẩm mới (Admin)
if ($user['role'] === 'admin' && isset($_POST['add_product'])) {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_STRING);
    $sub_category = filter_input(INPUT_POST, 'sub_category', FILTER_SANITIZE_STRING);
    $gender = filter_input(INPUT_POST, 'gender', FILTER_SANITIZE_STRING);
    $price = floatval($_POST['price'] ?? 0);
    $old_price = floatval($_POST['old_price'] ?? 0);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
    $stock = intval($_POST['stock'] ?? 0);
    $rating = floatval($_POST['rating'] ?? 0);
    $scale = filter_input(INPUT_POST, 'scale', FILTER_SANITIZE_STRING);
    $material = filter_input(INPUT_POST, 'material', FILTER_SANITIZE_STRING);
    $weight = filter_input(INPUT_POST, 'weight', FILTER_SANITIZE_STRING);

    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../Asset/Images/Products/';
        if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
        $file_name = uniqid('product-') . '.' . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $upload_path = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
            $image = 'Asset/Images/Products/' . $file_name;
        }
    }

    $stmt = $pdo->prepare("INSERT INTO products (name, category, sub_category, gender, price, old_price, image) 
                           VALUES (:name, :category, :sub_category, :gender, :price, :old_price, :image)");
    $stmt->execute([
        'name' => $name,
        'category' => $category,
        'sub_category' => $sub_category,
        'gender' => $gender,
        'price' => $price,
        'old_price' => $old_price,
        'image' => $image
    ]);

    $product_id = $pdo->lastInsertId();

    $stmt = $pdo->prepare("INSERT INTO product_details (product_id, stock, rating, scale, material, weight, description) 
                           VALUES (:product_id, :stock, :rating, :scale, :material, :weight, :description)");
    $stmt->execute([
        'product_id' => $product_id,
        'stock' => $stock,
        'rating' => $rating,
        'scale' => $scale,
        'material' => $material,
        'weight' => $weight,
        'description' => $description
    ]);

    header("Location: " . $_SERVER['PHP_SELF'] . "?tab=products");
    exit;
}

// Xác định tab hiện tại từ URL
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'profile';

// Phân trang
$items_per_page = 5;
$page_users = isset($_GET['page_users']) ? max(1, (int)$_GET['page_users']) : 1;
$page_products = isset($_GET['page_products']) ? max(1, (int)$_GET['page_products']) : 1;
$page_reviews = isset($_GET['page_reviews']) ? max(1, (int)$_GET['page_reviews']) : 1;

$offset_users = ($page_users - 1) * $items_per_page;
$offset_products = ($page_products - 1) * $items_per_page;
$offset_reviews = ($page_reviews - 1) * $items_per_page;

// Xử lý quản lý người dùng (Admin)
if ($user['role'] === 'admin' && isset($_POST['manage_users'])) {
    $action = $_POST['action'] ?? '';
    $target_user_id = $_POST['user_id'] ?? '';

    if ($action === 'delete') {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
        $stmt->execute(['id' => $target_user_id]);
    } elseif ($action === 'update') {
        $full_name = filter_input(INPUT_POST, 'user_full_name', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'user_email', FILTER_SANITIZE_EMAIL);
        $password = !empty($_POST['user_password']) ? password_hash($_POST['user_password'], PASSWORD_DEFAULT) : null;

        $params = ['full_name' => $full_name, 'email' => $email, 'id' => $target_user_id];
        $sql = "UPDATE users SET full_name = :full_name, email = :email";
        if ($password) {
            $sql .= ", password = :password";
            $params['password'] = $password;
        }
        $sql .= " WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
    }
}

// Xử lý quản lý sản phẩm (Admin)
if ($user['role'] === 'admin' && isset($_POST['manage_products'])) {
    $product_id = $_POST['product_id'] ?? '';
    $name = filter_input(INPUT_POST, 'product_name', FILTER_SANITIZE_STRING);
    $type = filter_input(INPUT_POST, 'product_type', FILTER_SANITIZE_STRING);
    $gender = filter_input(INPUT_POST, 'product_gender', FILTER_SANITIZE_STRING);
    $stock = filter_input(INPUT_POST, 'product_stock', FILTER_VALIDATE_INT);
    $sold = filter_input(INPUT_POST, 'product_sold', FILTER_VALIDATE_INT);
    $rating = filter_input(INPUT_POST, 'product_rating', FILTER_VALIDATE_FLOAT);
    $scale = filter_input(INPUT_POST, 'product_scale', FILTER_SANITIZE_STRING);
    $material = filter_input(INPUT_POST, 'product_material', FILTER_SANITIZE_STRING);
    $weight = filter_input(INPUT_POST, 'product_weight', FILTER_VALIDATE_FLOAT);

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE id = :id");
    $stmt->execute(['id' => $product_id]);
    $product_exists = $stmt->fetchColumn();

    if ($product_exists) {
        $stmt = $pdo->prepare("UPDATE products SET name = :name, category = :category, gender = :gender WHERE id = :id");
        $stmt->execute([
            'name' => $name,
            'category' => $type,
            'gender' => $gender,
            'id' => $product_id
        ]);

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM product_details WHERE product_id = :product_id");
        $stmt->execute(['product_id' => $product_id]);
        $details_exist = $stmt->fetchColumn();

        if ($details_exist) {
            $stmt = $pdo->prepare("
                UPDATE product_details 
                SET stock = :stock, sold = :sold, rating = :rating, scale = :scale, material = :material, weight = :weight 
                WHERE product_id = :product_id
            ");
            $stmt->execute([
                'stock' => $stock,
                'sold' => $sold,
                'rating' => $rating,
                'scale' => $scale,
                'material' => $material,
                'weight' => $weight,
                'product_id' => $product_id
            ]);
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO product_details (product_id, stock, sold, rating, scale, material, weight)
                VALUES (:product_id, :stock, :sold, :rating, :scale, :material, :weight)
            ");
            $stmt->execute([
                'product_id' => $product_id,
                'stock' => $stock,
                'sold' => $sold,
                'rating' => $rating,
                'scale' => $scale,
                'material' => $material,
                'weight' => $weight
            ]);
        }
    } else {
        error_log("Product ID $product_id không tồn tại trong bảng products.");
    }

    header("Location: " . $_SERVER['PHP_SELF'] . "?tab=products");
    exit;
}

// Xử lý quản lý đánh giá (Admin)
if ($user['role'] === 'admin' && isset($_POST['manage_reviews'])) {
    $review_id = $_POST['review_id'] ?? '';
    $stmt = $pdo->prepare("DELETE FROM reviews WHERE id = :id");
    $stmt->execute(['id' => $review_id]);
    header("Location: " . $_SERVER['PHP_SELF'] . "?tab=reviews");
    exit;
}

// Xử lý bỏ lưu voucher
if (isset($_POST['remove_voucher'])) {
    $voucher_id = filter_input(INPUT_POST, 'voucher_id', FILTER_VALIDATE_INT);
    if ($voucher_id) {
        $stmt = $pdo->prepare("DELETE FROM user_vouchers WHERE user_id = ? AND voucher_id = ?");
        $stmt->execute([$user_id, $voucher_id]);
    }
    header("Location: " . $_SERVER['PHP_SELF'] . "?tab=vouchers");
    exit;
}

// Lấy dữ liệu với phân trang
$total_users = $pdo->query("SELECT COUNT(*) FROM users WHERE role != 'admin'")->fetchColumn();
$total_pages_users = ceil($total_users / $items_per_page);

$total_products = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$total_pages_products = ceil($total_products / $items_per_page);

$total_reviews = $pdo->query("SELECT COUNT(*) FROM reviews")->fetchColumn();
$total_pages_reviews = ceil($total_reviews / $items_per_page);

$users_list = $user['role'] === 'admin' ? $pdo->query("SELECT * FROM users WHERE role != 'admin' LIMIT $offset_users, $items_per_page")->fetchAll(PDO::FETCH_ASSOC) : [];
$products_list = [];
if ($user['role'] === 'admin') {
    $stmt = $pdo->prepare("
        SELECT p.*, pd.stock, pd.sold, pd.rating, pd.scale, pd.material, pd.weight
        FROM products p
        LEFT JOIN product_details pd ON p.id = pd.product_id
        LIMIT :offset, :limit
    ");
    $stmt->bindValue(':offset', $offset_products, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $items_per_page, PDO::PARAM_INT);
    $stmt->execute();
    $products_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
$reviews_list = $user['role'] === 'admin' ? $pdo->query("
    SELECT r.*, u.full_name, p.name AS product_name
    FROM reviews r 
    JOIN users u ON r.user_id = u.id 
    JOIN products p ON r.product_id = p.id 
    LIMIT $offset_reviews, $items_per_page
")->fetchAll(PDO::FETCH_ASSOC) : [];

?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa thông tin cá nhân</title>
    <link rel="stylesheet" href="../Asset/Css/styles.css">
    <link rel="shortcut icon" href="../Asset/Images/Logo/favicon.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/ionicons@5.5.2/dist/css/ionicons.min.css">
</head>

<body>
    <header>
        <div class="header-top">
            <div class="container">
                <ul class="header-social-container">
                    <li>
                        <a href="https://www.facebook.com/hung.hay.ho.705636" class="social-link">
                            <ion-icon name="logo-facebook"></ion-icon>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="social-link">
                            <ion-icon name="logo-twitter"></ion-icon>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="social-link">
                            <ion-icon name="logo-instagram"></ion-icon>
                        </a>
                    </li>
                    <li>
                        <a href="https://docs.google.com/document/d/11eb-9DV3taTwuMiuHT6DhwzeZ-Ky7Q-uobBnBAp2mn4/edit?usp=sharing" class="social-link">
                            <ion-icon name="mail-outline"></ion-icon>
                        </a>
                    </li>
                </ul>
                <div class="header-alert-news">
                    <p>
                        <b>Miễn Phí Vận Chuyển</b>
                        Cho Đơn Hàng Trên 500k
                    </p>
                </div>
                <div class="header-top-actions">
                    <select name="currency">
                        <option value="usd">VND</option>
                        <option value="eur">USD</option>
                    </select>
                    <div id="google_translate_element"></div>
                </div>
            </div>
        </div>
        <div class="header-main">
            <div class="container">
                <a href="../index.php" class="header-logo">
                    <img src="../Asset/Images/Logo/Logo.png" alt="Logo của Good Smile Figure" width="160" height="46">
                </a>
                <h1 style="color: orange; text-align:center; letter-spacing: 3px; "> THÔNG TIN CÁ NHÂN </h1>
                <div class="header-user-actions">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <span style="margin-bottom:11px"><?php echo htmlspecialchars($_SESSION['full_name'] ?? 'Khách'); ?></span>
                        <a href="../pages/edit-profile.php" class="action-btn">
                            <img src="../Asset/Images/<?php echo htmlspecialchars($_SESSION['avatar']); ?>"
                                alt="#" class="user-avatar" style="width: 40px; height: 40px; border-radius: 50%;">
                        </a>
                        <a href="?logout=true" class="action-btn">
                            <ion-icon name="log-out-outline"></ion-icon>
                        </a>
                    <?php else: ?>
                        <a href="login.php" class="action-btn">
                            <ion-icon name="person-outline"></ion-icon>
                        </a>
                    <?php endif; ?>
                    <a href="like.php" class="action-btn">
                        <ion-icon name="heart-outline"></ion-icon>
                        <span class="count">0</span>
                    </a>
                    <a href="Payment.php" class="action-btn">
                        <ion-icon name="cart-outline" style="color: var(--eerie-black);"></ion-icon>
                        <span class="count">0</span>
                    </a>
                </div>
            </div>
        </div>

        <nav class="desktop-navigation-menu" style="border-bottom: 1px solid orange;">

            <div class="container">

                <ul class="desktop-menu-category-list">

                    <li class="menu-category">

                        <a href="../index.php" class="menu-title">Trang Chủ</a>

                    </li>

                    <li class="menu-category">

                        <a href="#" class="menu-title">Danh Mục</a>

                        <div class="dropdown-panel">

                            <ul class="dropdown-panel-list">

                                <li class="menu-title">

                                    <a href="Product.php">Figure</a>

                                </li>

                                <li class="panel-list-item">

                                    <a href="product.php?category=Scale 1/4">Scale 1/4</a>

                                </li>

                                <li class="panel-list-item">

                                    <a href="product.php?category=Scale 1/7">Scale 1/7</a>

                                </li>

                                <li class="panel-list-item">

                                    <a href="product.php?sub_category=scale 1/6">Scale 1/6</a>

                                </li>

                                <li class="panel-list-item">

                                    <a href="product.php?category=FIGMA">FIGMA</a>

                                </li>

                                <li class="panel-list-item">

                                    <a href="Product.php">Khác</a>

                                </li>

                                <li class="panel-list-item">

                                    <a href="Product.php">

                                        <img src="../Asset/Images/Banner/banner-1.webp" alt="Figure" width="250" height="119">

                                    </a>

                                </li>

                            </ul>

                            <ul class="dropdown-panel-list">

                                <li class="menu-title">

                                    <a href="product.php?category=POP UP PARADE">POP UP PARADE</a>

                                </li>

                                <li class="panel-list-item">

                                    <a href="product.php?sub_category=L size">POP UP PARADE L size</a>

                                </li>

                                <li class="panel-list-item">

                                    <a href="product.php?sub_category=XL size">POP UP PARADE XL size</a>

                                </li>

                                <li class="panel-list-item">

                                    <a href="product.php?sub_category=SP">POP UP PARADE SP</a>

                                </li>

                                <li class="panel-list-item">

                                    <a href="product.php?sub_category=Swacchao!">POP UP PARADE Swacchao!</a>

                                </li>

                                <li class="panel-list-item">

                                    <a href="product.php?sub_category=BEACH QUEENS">POP UP PARADE BEACH QUEENS</a>

                                </li>

                                <li class="panel-list-item">

                                    <a href="Product.php">

                                        <img src="../Asset/Images/Banner/banner-3.webp" alt="Nendoroid" width="250" height="119">

                                    </a>

                                </li>

                            </ul>

                            <ul class="dropdown-panel-list">

                                <li class="menu-title">

                                    <a href="product.php?category=Nenderoid">Nendoroid</a>

                                </li>

                                <li class="panel-list-item">

                                    <a href="product.php?sub_category=Surprise">Nendoroid Surprise</a>

                                </li>

                                <li class="panel-list-item">

                                    <a href="product.php?sub_category=Dolly">Nendoroid Dolly</a>

                                </li>

                                <li class="panel-list-item">

                                    <a href="product.php?sub_category=More">Nendoroid More</a>

                                </li>

                                <li class="panel-list-item">

                                    <a href="product.php?sub_category=More">Nendoroid EZ</a>

                                </li>

                                <li class="panel-list-item">

                                    <a href="product.php?sub_category=Jumbo">Nendoroid Jumbo</a>

                                </li>

                                <li class="panel-list-item">

                                    <a href="Product.php">

                                        <img src="../Asset/Images/Banner/banner-2.webp" alt="Nendoroid" width="250" height="119">

                                    </a>

                                </li>

                            </ul>

                            <ul class="dropdown-panel-list">

                                <li class="menu-title">

                                    <a href="product.php?category=Mecha Smile">Mecha Smile</a>

                                </li>

                                <li class="panel-list-item">

                                    <a href="product.php?sub_category=THE GATTAI">THE GATTAI</a>

                                </li>

                                <li class="panel-list-item">

                                    <a href="product.php?sub_category=HAGANE WORKS">HAGANE WORKS</a>

                                </li>

                                <li class="panel-list-item">

                                    <a href="product.php?sub_category=MODEROID">MODEROID</a>

                                </li>

                                <li class="panel-list-item">

                                    <a href="product.php?sub_category=PartPart">Thay thế</a>

                                </li>

                                <li class="panel-list-item">

                                    <a href="product.php?sub_category=Other">Other (Mecha Smile)</a>

                                </li>

                                <li class="panel-list-item">

                                    <a href="Product.php">

                                        <img src="../Asset/Images/Banner/banner-4.webp" alt="Mecha Smile" width="250" height="119">

                                    </a>

                                </li>

                            </ul>

                        </div>

                    </li>

                    <li class="menu-category">

                        <a href="product.php?gender=Male" class="menu-title">Figure nam</a>

                        <ul class="dropdown-list">

                            <li class="dropdown-item">

                                <a href="Product.php">Hành động</a>

                            </li>

                            <li class="dropdown-item">

                                <a href="Product.php">Isekai</a>

                            </li>

                            <li class="dropdown-item">

                                <a href="Product.php">Đời thường</a>

                            </li>

                            <li class="dropdown-item">

                                <a href="Product.php">Khác</a>

                            </li>

                        </ul>

                    </li>

                    <li class="menu-category">

                        <a href="product.php?gender=Female" class="menu-title">Figure nữ</a>

                        <ul class="dropdown-list">

                            <li class="dropdown-item">

                                <a href="Product.php">Hành động</a>

                            </li>

                            <li class="dropdown-item">

                                <a href="Product.php">Isekai</a>

                            </li>

                            <li class="dropdown-item">

                                <a href="Product.php">Đời thường</a>

                            </li>

                            <li class="dropdown-item">

                                <a href="Product.php">Khác</a>

                            </li>

                        </ul>

                    </li>

                    <li class="menu-category">

                        <a href="product.php?category=" class="menu-title">Sản phẩm</a>

                        <ul class="dropdown-list">

                            <li class="dropdown-item">

                                <a href="Product.php">Bán chạy</a>

                            </li>

                            <li class="dropdown-item">

                                <a href="Product.php">Mới về</a>

                            </li>

                            <li class="dropdown-item">

                                <a href="Product.php">Đánh giá cao</a>

                            </li>

                            <li class="dropdown-item">

                                <a href="Product.php">Ưu đãi</a>

                            </li>

                        </ul>

                    </li>

                    <li class="menu-category">

                        <a href="Product.php" class="menu-title">Hãng</a>

                        <ul class="dropdown-list">

                            <li class="dropdown-item">

                                <a href="Product.php">Good Smile Company</a>

                            </li>

                            <li class="dropdown-item">

                                <a href="Product.php">Max Factory</a>

                            </li>

                            <li class="dropdown-item">

                                <a href="Product.php">Bandai Namco</a>

                            </li>

                            <li class="dropdown-item">

                                <a href="Product.php">MegaHouse</a>

                            </li>

                        </ul>

                    </li>

                    <li class="menu-category">

                        <a href="#" class="menu-title">Blog</a>

                    </li>

                    <li class="menu-category">

                        <a href="voucher.php" class="menu-title">Ưu Đãi Hấp Dẫn</a>


                    </li>

                    <li class="menu-category">

                        <a href="about.php" class="menu-title">Giới Thiệu</a>


                    </li>

                </ul>

            </div>

        </nav>


        <div class="mobile-bottom-navigation">

            <button class="action-btn" data-mobile-menu-open-btn>

                <ion-icon name="menu-outline"></ion-icon>

            </button>

            <button class="action-btn">

                <a href="Payment.php" class="action-btn">
                    <ion-icon name="cart-outline"></ion-icon>
                    <span class="count">0</span>
                </a>

            </button>

            <button class="action-btn">

                <a href="../index.php"><ion-icon name="home-outline" style="color: var(--eerie-black);"></ion-icon></a>

            </button>

            <button class=" action-btn">

                <a href="like.php" class="action-btn">
                    <ion-icon name="heart-outline"></ion-icon>
                    <span class="count">0</span>
                </a>

            </button>

            <button class="action-btn" data-mobile-menu-open-btn>

                <ion-icon name="grid-outline"></ion-icon>

            </button>

        </div>

        <nav class="mobile-navigation-menu has-scrollbar" data-mobile-menu>

            <div class="menu-top">

                <h2 class="menu-title">Menu</h2>

                <button class="menu-close-btn" data-mobile-menu-close-btn>

                    <ion-icon name="close-outline"></ion-icon>

                </button>

            </div>

            <ul class="mobile-menu-category-list">

                <li class="menu-category">

                    <a href="#" class="menu-title">Trang Chủ</a>

                </li>

                <li class="menu-category">

                    <button class="accordion-menu" data-accordion-btn>

                        <p class="menu-title">Figure nam</p>

                        <div>

                            <ion-icon name="add-outline" class="add-icon"></ion-icon>

                            <ion-icon name="remove-outline" class="remove-icon"></ion-icon>

                        </div>

                    </button>

                    <ul class="submenu-category-list" data-accordion>

                        <li class="submenu-category">

                            <a href="Product.php" class="submenu-title">Hành động</a>

                        </li>

                        <li class="submenu-category">

                            <a href="Product.php" class="submenu-title">Isekai</a>

                        </li>

                        <li class="submenu-category">

                            <a href="Product.php" class="submenu-title">Đời thường</a>

                        </li>

                        <li class="submenu-category">

                            <a href="Product.php" class="submenu-title">Khác</a>

                        </li>

                    </ul>

                </li>

                <li class="menu-category">

                    <button class="accordion-menu" data-accordion-btn>

                        <p class="menu-title">Figure nữ</p>

                        <div>

                            <ion-icon name="add-outline" class="add-icon"></ion-icon>

                            <ion-icon name="remove-outline" class="remove-icon"></ion-icon>

                        </div>

                    </button>

                    <ul class="submenu-category-list" data-accordion>

                        <li class="submenu-category">

                            <a href="Product.php" class="submenu-title">Hành động</a>

                        </li>

                        <li class="submenu-category">

                            <a href="Product.php" class="submenu-title">Isekai</a>

                        </li>

                        <li class="submenu-category">

                            <a href="Product.php" class="submenu-title">Đời thường</a>

                        </li>

                        <li class="submenu-category">

                            <a href="Product.php" class="submenu-title">Khác</a>

                        </li>

                    </ul>

                </li>

                <li class="menu-category">

                    <button class="accordion-menu" data-accordion-btn>

                        <p class="menu-title">Sản phẩm</p>

                        <div>

                            <ion-icon name="add-outline" class="add-icon"></ion-icon>

                            <ion-icon name="remove-outline" class="remove-icon"></ion-icon>

                        </div>

                    </button>

                    <ul class="submenu-category-list" data-accordion>

                        <li class="submenu-category">

                            <a href="#" class="submenu-title">Bán chạy</a>

                        </li>

                        <li class="submenu-category">

                            <a href="#" class="submenu-title">Mới về</a>

                        </li>

                        <li class="submenu-category">

                            <a href="#" class="submenu-title">Đánh giá cao</a>

                        </li>

                        <li class="submenu-category">

                            <a href="#" class="submenu-title">Ưu đãi</a>

                        </li>

                    </ul>

                </li>

                <li class="menu-category">

                    <button class="accordion-menu" data-accordion-btn>

                        <p class="menu-title">Hãng</p>

                        <div>

                            <ion-icon name="add-outline" class="add-icon"></ion-icon>

                            <ion-icon name="remove-outline" class="remove-icon"></ion-icon>

                        </div>

                    </button>

                    <ul class="submenu-category-list" data-accordion>

                        <li class="submenu-category">

                            <a href="#" class="submenu-title">Good Smile Company</a>

                        </li>

                        <li class="submenu-category">

                            <a href="#" class="submenu-title">Max Factory</a>

                        </li>

                        <li class="submenu-category">

                            <a href="#" class="submenu-title">Bandai Namco</a>

                        </li>

                        <li class="submenu-category">

                            <a href="#" class="submenu-title">MegaHouse</a>

                        </li>

                    </ul>

                </li>

                <li class="menu-category">

                    <a href="#" class="menu-title">Blog</a>

                </li>

                <li class="menu-category">

                    <a href="#" class="menu-title">Ưu Đãi Hấp Dẫn</a>

                </li>

            </ul>

            <div class="menu-bottom">

                <ul class="menu-category-list">

                    <li class="menu-category">

                        <button class="accordion-menu" data-accordion-btn>

                            <p class="menu-title">Ngôn Ngữ</p>

                            <ion-icon name="caret-back-outline" class="caret-back"></ion-icon>

                        </button>

                        <ul class="submenu-category-list" data-accordion>

                            <li class="submenu-category">

                                <a href="#" class="submenu-title">Tiếng Việt</a>

                            </li>

                            <li class="submenu-category">

                                <a href="#" class="submenu-title">Tiếng Anh</a>

                            </li>

                        </ul>

                    </li>

                    <li class="menu-category">

                        <button class="accordion-menu" data-accordion-btn>

                            <p class="menu-title">Tiền Tệ</p>

                            <ion-icon name="caret-back-outline" class="caret-back"></ion-icon>

                        </button>

                        <ul class="submenu-category-list" data-accordion>

                            <li class="submenu-category">

                                <a href="#" class="submenu-title">VND</a>

                            </li>

                            <li class="submenu-category">

                                <a href="#" class="submenu-title">USD</a>

                            </li>

                        </ul>

                    </li>

                </ul>

                <ul class="menu-social-container">

                    <li>

                        <a href="https://www.facebook.com/hung.hay.ho.705636" class="social-link">

                            <ion-icon name="logo-facebook"></ion-icon>

                        </a>

                    </li>

                    <li>

                        <a href="#" class="social-link">

                            <ion-icon name="logo-twitter"></ion-icon>

                        </a>

                    </li>

                    <li>

                        <a href="#" class="social-link">

                            <ion-icon name="logo-instagram"></ion-icon>

                        </a>

                    </li>

                    <li>

                        <a href="https://docs.google.com/document/d/11eb-9DV3taTwuMiuHT6DhwzeZ-Ky7Q-uobBnBAp2mn4/edit?tab=t.0" class="social-link">

                            <ion-icon name="mail-outline"></ion-icon>

                        </a>

                    </li>

                </ul>

            </div>

        </nav>

    </header>

    <main>
        <div class="container">
            <div class="edit-profile-container">
                <div class="profile-layout">
                    <?php if ($user['role'] === 'admin'): ?>
                        <div class="profile-tabs">
                            <div class="tab-container">
                                <div class="tab-buttons">
                                    <button class="tab-btn <?php echo $active_tab === 'profile' ? 'active' : ''; ?>" data-tab="profile">Thông tin cá nhân</button>
                                    <button class="tab-btn <?php echo $active_tab === 'vouchers' ? 'active' : ''; ?>" data-tab="vouchers">Voucher</button>
                                    <button class="tab-btn <?php echo $active_tab === 'users' ? 'active' : ''; ?>" data-tab="users">Người dùng</button>
                                    <button class="tab-btn <?php echo $active_tab === 'products' ? 'active' : ''; ?>" data-tab="products">Sản phẩm</button>
                                    <button class="tab-btn <?php echo $active_tab === 'reviews' ? 'active' : ''; ?>" data-tab="reviews">Đánh giá</button>
                                </div>

                                <!-- Tab Thông tin cá nhân -->
                                <div class="tab-content <?php echo $active_tab === 'profile' ? 'active' : ''; ?>" id="profile">
                                    <h3>Thông tin cá nhân</h3>
                                    <form method="POST" enctype="multipart/form-data" id="profileForm">
                                        <input type="hidden" name="update_profile" value="1">
                                        <div class="profile-avatar">
                                            <img src="../Asset/Images/<?php echo htmlspecialchars($user['avatar']); ?>" alt="Avatar" class="avatar-img" id="avatarPreview">
                                            <label for="avatar" class="avatar-label">
                                                <ion-icon name="camera-outline"></ion-icon> Thay đổi ảnh đại diện
                                            </label>
                                            <input type="file" id="avatar" name="avatar" accept="image/*" style="display: none;">
                                        </div>
                                        <div class="form-group">
                                            <label for="full_name">Họ và tên</label>
                                            <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="email">Email</label>
                                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="password">Mật khẩu mới (để trống nếu không đổi)</label>
                                            <input type="password" id="password" name="password">
                                        </div>
                                        <div class="form-group">
                                            <label for="address">Địa chỉ (có thể để trống)</label>
                                            <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>">
                                        </div>
                                        <div class="form-group">
                                            <label>Giới tính</label>
                                            <div class="gender-options">
                                                <label><input type="radio" name="gender" value="male" <?php echo $user['gender'] === 'male' ? 'checked' : ''; ?>> Nam</label>
                                                <label><input type="radio" name="gender" value="female" <?php echo $user['gender'] === 'female' ? 'checked' : ''; ?>> Nữ</label>
                                                <label><input type="radio" name="gender" value="other" <?php echo $user['gender'] === 'other' ? 'checked' : ''; ?>> Khác</label>
                                            </div>
                                        </div>
                                        <div class="button-group">
                                            <button type="submit" class="save-btn">Lưu thay đổi</button>
                                            <button type="button" class="delete-btn" id="deleteAccountBtn">Xóa tài khoản</button>
                                        </div>
                                    </form>
                                </div>

                                <!-- Tab Voucher -->
                                <div class="tab-content <?php echo $active_tab === 'vouchers' ? 'active' : ''; ?>" id="vouchers">
                                    <h3 style="margin-bottom: 20px; font-size: 24px; color: #333;">Danh sách Voucher đã nhận</h3>
                                    <div class="voucher-grid">
                                        <?php if (empty($vouchers_list)): ?>
                                            <p style="text-align: center; color: #777; font-size: 16px; grid-column: span 3;">Bạn chưa nhận voucher nào.</p>
                                        <?php else: ?>
                                            <?php foreach ($vouchers_list as $voucher): ?>
                                                <?php
                                                $expiry_date = new DateTime($voucher['expiry_date']);
                                                $now = new DateTime();
                                                $interval = $now->diff($expiry_date);
                                                $days_left = $now > $expiry_date ? "Hết hạn" : "Còn " . $interval->days . " ngày";
                                                $is_expired = $now > $expiry_date;
                                                ?>
                                                <div class="voucher-card2 <?php echo $is_expired ? 'expired' : ''; ?>">
                                                    <div class="voucher-header">
                                                        <span class="voucher-discount"><?php echo htmlspecialchars($voucher['discount']); ?>% OFF</span>
                                                    </div>
                                                    <div class="voucher-body">
                                                        <h4 class="voucher-code"><?php echo htmlspecialchars($voucher['code']); ?></h4>
                                                        <h4 class="voucher-condition"><?php echo htmlspecialchars($voucher['description']); ?></h4>
                                                        <p class="voucher-condition">Đơn tối thiểu: <?php echo number_format($voucher['min_order_value'], 0, ',', '.'); ?> VNĐ</p>
                                                        <p class="voucher-expiry <?php echo $is_expired ? 'expired-text' : ''; ?>">
                                                            <?php echo $days_left; ?>
                                                        </p>
                                                    </div>
                                                    <div class="voucher-footer">
                                                        <form method="POST">
                                                            <input type="hidden" name="remove_voucher" value="1">
                                                            <input type="hidden" name="voucher_id" value="<?php echo $voucher['id']; ?>">
                                                            <button type="submit" class="btn-remove-voucher" <?php echo $is_expired ? 'disabled' : ''; ?>
                                                                onclick="return confirm('Bạn có chắc muốn bỏ lưu voucher này không?');">
                                                                Bỏ lưu
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Tab Người dùng -->
                                <div class="tab-content <?php echo $active_tab === 'users' ? 'active' : ''; ?>" id="users">
                                    <h3>Quản lý người dùng</h3>
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Tên</th>
                                                <th>Email</th>
                                                <th>Mật khẩu</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($users_list as $u): ?>
                                                <tr>
                                                    <td><?php echo $u['id']; ?></td>
                                                    <td><input type="text" value="<?php echo htmlspecialchars($u['full_name']); ?>" form="userForm<?php echo $u['id']; ?>" name="user_full_name"></td>
                                                    <td><input type="email" value="<?php echo htmlspecialchars($u['email']); ?>" form="userForm<?php echo $u['id']; ?>" name="user_email"></td>
                                                    <td>
                                                        <form id="userForm<?php echo $u['id']; ?>" method="POST">
                                                            <input type="hidden" name="manage_users" value="1">
                                                            <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                                            <input type="password" name="user_password" placeholder="Mật khẩu mới">
                                                            <button type="submit" name="action" value="update">Cập nhật</button>
                                                            <button type="submit" name="action" value="delete" onclick="return confirm('Xóa người dùng này?');">Xóa</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                    <div class="pagination">
                                        <?php for ($i = 1; $i <= $total_pages_users; $i++): ?>
                                            <a href="?tab=users&page_users=<?php echo $i; ?>" class="<?php echo $i === $page_users && $active_tab === 'users' ? 'active' : ''; ?>"><?php echo $i; ?></a>
                                        <?php endfor; ?>
                                    </div>
                                </div>

                                <!-- Tab Sản phẩm -->
                                <div class="tab-content <?php echo $active_tab === 'products' ? 'active' : ''; ?>" id="products">
                                    <h3 class="tab-title">Quản lý sản phẩm</h3>
                                    <button id="addProductBtn" class="add-product-btn">Thêm sản phẩm</button>

                                    <!-- Form thêm sản phẩm -->
                                    <div id="addProductForm" class="add-product-container" style="display: none;">
                                        <form method="POST" enctype="multipart/form-data" class="add-product-form">
                                            <input type="hidden" name="add_product" value="1">
                                            <div class="form-group">
                                                <label for="name">Tên sản phẩm:</label>
                                                <input type="text" id="name" name="name" placeholder="Nhập tên sản phẩm" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="category">Danh mục:</label>
                                                <select id="category" name="category" required>
                                                    <option value="" disabled selected>Chọn danh mục</option>
                                                    <?php
                                                    $stmt = $pdo->query("SELECT DISTINCT category FROM products");
                                                    $categories = $stmt->fetchAll(PDO::FETCH_COLUMN);
                                                    foreach ($categories as $cat) {
                                                        echo "<option value='" . htmlspecialchars($cat) . "'>" . htmlspecialchars($cat) . "</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="sub_category">Danh mục phụ:</label>
                                                <input type="text" id="sub_category" name="sub_category" placeholder="Nhập danh mục phụ">
                                            </div>
                                            <div class="form-group">
                                                <label for="gender">Giới tính:</label>
                                                <select id="gender" name="gender" required>
                                                    <option value="" disabled selected>Chọn giới tính</option>
                                                    <option value="Male">Nam</option>
                                                    <option value="Female">Nữ</option>
                                                    <option value="Other">Khác</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="image">Ảnh sản phẩm:</label>
                                                <div class="custom-file-upload">
                                                    <button type="button" class="upload-btn" id="uploadImageBtn">
                                                        <ion-icon name="camera-outline"></ion-icon> Chọn ảnh sản phẩm
                                                    </button>
                                                    <input type="file" id="image" name="image" accept="image/*" required style="display: none;">
                                                </div>
                                                <div class="image-preview" id="imagePreview" style="display: none;">
                                                    <img src="#" alt="Ảnh xem trước" id="previewImg" style="max-width: 200px; margin-top: 10px;">
                                                    <button type="button" class="remove-preview-btn" id="removePreviewBtn">Xóa ảnh</button>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="price">Giá hiện tại (VNĐ):</label>
                                                <input type="number" id="price" name="price" min="0" step="1000" placeholder="Nhập giá" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="old_price">Giá cũ (VNĐ, nếu có):</label>
                                                <input type="number" id="old_price" name="old_price" min="0" step="1000" placeholder="Nhập giá cũ">
                                            </div>
                                            <div class="form-group">
                                                <label for="description">Mô tả sản phẩm:</label>
                                                <textarea id="description" name="description" placeholder="Nhập mô tả sản phẩm"></textarea>
                                            </div>
                                            <div class="form-group">
                                                <label for="stock">Số lượng tồn kho:</label>
                                                <input type="number" id="stock" name="stock" min="0" placeholder="Nhập số lượng" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="rating">Đánh giá (0-5):</label>
                                                <input type="number" id="rating" name="rating" min="0" max="5" step="0.1" value="0" placeholder="Nhập đánh giá">
                                            </div>
                                            <div class="form-group">
                                                <label for="scale">Tỷ lệ:</label>
                                                <input type="text" id="scale" name="scale" placeholder="Nhập tỷ lệ (ví dụ: 1:6)">
                                            </div>
                                            <div class="form-group">
                                                <label for="material">Chất liệu:</label>
                                                <input type="text" id="material" name="material" placeholder="Nhập chất liệu">
                                            </div>
                                            <div class="form-group">
                                                <label for="weight">Trọng lượng:</label>
                                                <input type="text" id="weight" name="weight" placeholder="Nhập trọng lượng">
                                            </div>
                                            <div class="button-group">
                                                <button type="submit" class="save-btn">Thêm sản phẩm</button>
                                                <button type="button" id="cancelAddProductBtn" class="cancel-btn">Hủy</button>
                                            </div>
                                        </form>
                                    </div>

                                    <!-- Danh sách sản phẩm -->
                                    <h3 class="tab-title">Chỉnh sửa thông tin sản phẩm</h3>
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Tên</th>
                                                <th>Loại</th>
                                                <th>Giới tính</th>
                                                <th>Còn</th>
                                                <th>bán</th>
                                                <th>sao</th>
                                                <th>Tỉ lệ</th>
                                                <th>Chất liệu</th>
                                                <th>Trọng lượng</th>
                                                <th>Chức năng</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($products_list as $p): ?>
                                                <tr>
                                                    <td><?php echo $p['id']; ?></td>
                                                    <td><input type="text" value="<?php echo htmlspecialchars($p['name'] ?? ''); ?>" form="productForm<?php echo $p['id']; ?>" name="product_name"></td>
                                                    <td><input type="text" value="<?php echo htmlspecialchars($p['category'] ?? ''); ?>" form="productForm<?php echo $p['id']; ?>" name="product_type"></td>
                                                    <td>
                                                        <select name="product_gender" form="productForm<?php echo $p['id']; ?>">
                                                            <option value="<?php echo htmlspecialchars($p['gender']); ?>" selected><?php echo htmlspecialchars($p['gender']); ?></option>
                                                            <option value="Male">Male</option>
                                                            <option value="Female">Female</option>
                                                            <option value="Other">Other</option>
                                                        </select>
                                                    </td>
                                                    <td><input type="number" value="<?php echo $p['stock'] ?? 0; ?>" form="productForm<?php echo $p['id']; ?>" name="product_stock"></td>
                                                    <td><input type="number" value="<?php echo $p['sold'] ?? 0; ?>" form="productForm<?php echo $p['id']; ?>" name="product_sold"></td>
                                                    <td><input type="number" step="0.1" value="<?php echo $p['rating'] ?? 0.0; ?>" form="productForm<?php echo $p['id']; ?>" name="product_rating"></td>
                                                    <td><input type="text" value="<?php echo htmlspecialchars($p['scale'] ?? ''); ?>" form="productForm<?php echo $p['id']; ?>" name="product_scale"></td>
                                                    <td><input type="text" value="<?php echo htmlspecialchars($p['material'] ?? ''); ?>" form="productForm<?php echo $p['id']; ?>" name="product_material"></td>
                                                    <td><input type="number" step="0.1" value="<?php echo $p['weight'] ?? 0.0; ?>" form="productForm<?php echo $p['id']; ?>" name="product_weight"></td>
                                                    <td>
                                                        <form id="productForm<?php echo $p['id']; ?>" method="POST" class="inline-form">
                                                            <input type="hidden" name="manage_products" value="1">
                                                            <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
                                                            <button type="submit" class="action-btn update-btn">Cập nhật</button>
                                                        </form>
                                                        <form method="POST" class="inline-form">
                                                            <input type="hidden" name="delete_product" value="1">
                                                            <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
                                                            <button type="submit" class="action-btn delete-btn" onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?');">Xóa</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                    <div class="pagination">
                                        <?php for ($i = 1; $i <= $total_pages_products; $i++): ?>
                                            <a href="?tab=products&page_products=<?php echo $i; ?>" class="<?php echo $i === $page_products && $active_tab === 'products' ? 'active' : ''; ?>"><?php echo $i; ?></a>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <!-- Tab Đánh giá -->
                                <div class="tab-content <?php echo $active_tab === 'reviews' ? 'active' : ''; ?>" id="reviews">
                                    <h3>Quản lý đánh giá</h3>
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Người dùng</th>
                                                <th>Sản phẩm</th>
                                                <th>Rating</th>
                                                <th>Nội dung</th>
                                                <th>Ngày đánh giá</th>
                                                <th>Hành động</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($reviews_list)): ?>
                                                <tr>
                                                    <td colspan="7">Chưa có đánh giá nào.</td>
                                                </tr>
                                            <?php else: ?>
                                                <?php foreach ($reviews_list as $r): ?>
                                                    <tr>
                                                        <td><?php echo $r['id']; ?></td>
                                                        <td><?php echo htmlspecialchars($r['full_name']); ?></td>
                                                        <td><?php echo htmlspecialchars($r['product_name']); ?></td>
                                                        <td><?php echo htmlspecialchars($r['rating'] ?? 'N/A'); ?></td>
                                                        <td><?php echo htmlspecialchars($r['review_text']); ?></td>
                                                        <td><?php echo htmlspecialchars($r['review_date']); ?></td>
                                                        <td>
                                                            <form method="POST">
                                                                <input type="hidden" name="manage_reviews" value="1">
                                                                <input type="hidden" name="review_id" value="<?php echo $r['id']; ?>">
                                                                <button type="submit" onclick="return confirm('Xóa đánh giá này?');">Xóa</button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                    <div class="pagination">
                                        <?php for ($i = 1; $i <= $total_pages_reviews; $i++): ?>
                                            <a href="?tab=reviews&page_reviews=<?php echo $i; ?>" class="<?php echo $i === $page_reviews && $active_tab === 'reviews' ? 'active' : ''; ?>"><?php echo $i; ?></a>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Nếu không phải admin, hiển thị form thông tin cá nhân như trước -->
                        <div class="profile-tabs">
                            <div class="tab-container">
                                <div class="tab-buttons">
                                    <button class="tab-btn <?php echo $active_tab === 'profile' ? 'active' : ''; ?>" data-tab="profile">Thông tin cá nhân</button>
                                    <button class="tab-btn <?php echo $active_tab === 'vouchers' ? 'active' : ''; ?>" data-tab="vouchers">Voucher</button>
                                </div>

                                <!-- Tab Thông tin cá nhân -->
                                <div class="tab-content <?php echo $active_tab === 'profile' ? 'active' : ''; ?>" id="profile">
                                    <h3>Thông tin cá nhân</h3>
                                    <form method="POST" enctype="multipart/form-data" id="profileForm">
                                        <input type="hidden" name="update_profile" value="1">
                                        <div class="profile-avatar">
                                            <img src="../Asset/Images/<?php echo htmlspecialchars($user['avatar']); ?>" alt="Avatar" class="avatar-img" id="avatarPreview">
                                            <label for="avatar" class="avatar-label">
                                                <ion-icon name="camera-outline"></ion-icon> Thay đổi ảnh đại diện
                                            </label>
                                            <input type="file" id="avatar" name="avatar" accept="image/*" style="display: none;">
                                        </div>
                                        <div class="form-group">
                                            <label for="full_name">Họ và tên</label>
                                            <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="email">Email</label>
                                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="password">Mật khẩu mới (để trống nếu không đổi)</label>
                                            <input type="password" id="password" name="password">
                                        </div>
                                        <div class="form-group">
                                            <label for="address">Địa chỉ (có thể để trống)</label>
                                            <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>">
                                        </div>
                                        <div class="form-group">
                                            <label>Giới tính</label>
                                            <div class="gender-options">
                                                <label><input type="radio" name="gender" value="male" <?php echo $user['gender'] === 'male' ? 'checked' : ''; ?>> Nam</label>
                                                <label><input type="radio" name="gender" value="female" <?php echo $user['gender'] === 'female' ? 'checked' : ''; ?>> Nữ</label>
                                                <label><input type="radio" name="gender" value="other" <?php echo $user['gender'] === 'other' ? 'checked' : ''; ?>> Khác</label>
                                            </div>
                                        </div>
                                        <div class="button-group">
                                            <button type="submit" class="save-btn">Lưu thay đổi</button>
                                            <button type="button" class="delete-btn" id="deleteAccountBtn">Xóa tài khoản</button>
                                        </div>
                                    </form>
                                </div>

                                <!-- Tab Voucher -->
                                <div class="tab-content <?php echo $active_tab === 'vouchers' ? 'active' : ''; ?>" id="vouchers">
                                    <h3 style="margin-bottom: 20px; font-size: 24px; color: #333;">Danh sách Voucher đã nhận</h3>
                                    <div class="voucher-grid">
                                        <?php if (empty($vouchers_list)): ?>
                                            <p style="text-align: center; color: #777; font-size: 16px; grid-column: span 3;">Bạn chưa nhận voucher nào.</p>
                                        <?php else: ?>
                                            <?php foreach ($vouchers_list as $voucher): ?>
                                                <?php
                                                $expiry_date = new DateTime($voucher['expiry_date']);
                                                $now = new DateTime();
                                                $interval = $now->diff($expiry_date);
                                                $days_left = $now > $expiry_date ? "Hết hạn" : "Còn " . $interval->days . " ngày";
                                                $is_expired = $now > $expiry_date;
                                                ?>
                                                <div class="voucher-card2 <?php echo $is_expired ? 'expired' : ''; ?>">
                                                    <div class="voucher-header">
                                                        <span class="voucher-discount"><?php echo htmlspecialchars($voucher['discount']); ?>% OFF</span>
                                                    </div>
                                                    <div class="voucher-body">
                                                        <h4 class="voucher-code"><?php echo htmlspecialchars($voucher['code']); ?></h4>
                                                        <h4 class="voucher-condition"><?php echo htmlspecialchars($voucher['description']); ?></h4>
                                                        <p class="voucher-condition">Đơn tối thiểu: <?php echo number_format($voucher['min_order_value'], 0, ',', '.'); ?> VNĐ</p>
                                                        <p class="voucher-expiry <?php echo $is_expired ? 'expired-text' : ''; ?>">
                                                            <?php echo $days_left; ?>
                                                        </p>
                                                    </div>
                                                    <div class="voucher-footer">
                                                        <form method="POST">
                                                            <input type="hidden" name="remove_voucher" value="1">
                                                            <input type="hidden" name="voucher_id" value="<?php echo $voucher['id']; ?>">
                                                            <button type="submit" class="btn-remove-voucher" <?php echo $is_expired ? 'disabled' : ''; ?>
                                                                onclick="return confirm('Bạn có chắc muốn bỏ lưu voucher này không?');">
                                                                Bỏ lưu
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>

                            </div>
                        </div>


                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <!-- Toast thông báo -->
    <div class="notification-toast" data-toast-profile>
        <button class="toast-close-btn" data-toast-close-profile>
            <ion-icon name="close-outline"></ion-icon>
        </button>
        <div class="toast-detail">
            <p class="toast-message" id="toastMessageProfile"></p>
        </div>
    </div>

    <footer>

        <div class="footer-category">

            <div class="container">

                <h2 class="footer-category-title">Danh mục sản phẩm</h2>

                <div class="footer-category-box">

                    <h3 class="category-box-title">Figure :</h3>

                    <a href="Product.php" class="footer-category-link">Figure</a>
                    <a href="#" class="footer-category-link">Soft vinyl</a>
                    <a href="#" class="footer-category-link">Scale Figure</a>
                    <a href="#" class="footer-category-link">Action Figure</a>
                    <a href="#" class="footer-category-link">ACT MODE</a>
                    <a href="#" class="footer-category-link">ex:ride</a>
                    <a href="#" class="footer-category-link">Action Figure</a>

                </div>

                <div class="footer-category-box">
                    <h3 class="category-box-title">Nendoroid Series :</h3>

                    <a href="#" class="footer-category-link">Nendoroid</a>
                    <a href="#" class="footer-category-link">Nendoroid Surprise</a>
                    <a href="#" class="footer-category-link">Nendoroid Doll</a>
                    <a href="#" class="footer-category-link">Nendoroid More</a>
                    <a href="#" class="footer-category-link">Nendoroid Swacchao</a>
                    <a href="#" class="footer-category-link">Nendoroid Petite</a>
                    <a href="#" class="footer-category-link">Nendoroid Plus</a>
                </div>

                <div class="footer-category-box">
                    <h3 class="category-box-title">POP UP PARADE :</h3>

                    <a href="#" class="footer-category-link">POP UP PARADE L size</a>
                    <a href="#" class="footer-category-link">POP UP PARADE XL size</a>
                    <a href="#" class="footer-category-link">POP UP PARADE SP rings</a>
                    <a href="#" class="footer-category-link">POP UP PARADE Swacchao!</a>
                    <a href="#" class="footer-category-link">POP UP PARADE BEACH QUEENS</a>
                </div>

                <div class="footer-category-box">
                    <h3 class="category-box-title">Mecha Smile :</h3>

                    <a href="#" class="footer-category-link">THE GATTAI</a>
                    <a href="#" class="footer-category-link">HAGANE WORKS</a>
                    <a href="#" class="footer-category-link">MODEROID</a>
                    <a href="#" class="footer-category-link">Other (Mecha Smile)</a>
                </div>

            </div>

        </div>

        <div class="footer-nav">

            <div class="container">

                <ul class="footer-nav-list">

                    <li class="footer-nav-item">
                        <h2 class="nav-title">Danh mục nổi bật</h2>
                    </li>

                    <li class="footer-nav-item">
                        <a href="#" class="footer-nav-link">POP UP PARADE</a>
                    </li>

                    <li class="footer-nav-item">
                        <a href="#" class="footer-nav-link">figma Series</a>
                    </li>

                    <li class="footer-nav-item">
                        <a href="#" class="footer-nav-link">Plushie</a>
                    </li>

                    <li class="footer-nav-item">
                        <a href="#" class="footer-nav-link">Goods</a>
                    </li>

                    <li class="footer-nav-item">
                        <a href="Product.php" class="footer-nav-link">Figure</a>
                    </li>

                </ul>

                <ul class="footer-nav-list">

                    <li class="footer-nav-item">
                        <h2 class="nav-title">Sản phẩm</h2>
                    </li>

                    <li class="footer-nav-item">
                        <a href="#" class="footer-nav-link">Giảm giá</a>
                    </li>

                    <li class="footer-nav-item">
                        <a href="#" class="footer-nav-link">Sản phẩm mới</a>
                    </li>

                    <li class="footer-nav-item">
                        <a href="#" class="footer-nav-link">Bán chạy</a>
                    </li>

                    <li class="footer-nav-item">
                        <a href="#" class="footer-nav-link">Liên hệ</a>
                    </li>

                    <li class="footer-nav-item">
                        <a href="#" class="footer-nav-link">Vị trí cửa hàng</a>
                    </li>

                </ul>

                <ul class="footer-nav-list">

                    <li class="footer-nav-item">
                        <h2 class="nav-title">Công ty</h2>
                    </li>

                    <li class="footer-nav-item">
                        <a href="#" class="footer-nav-link">Giao hàng</a>
                    </li>

                    <li class="footer-nav-item">
                        <a href="#" class="footer-nav-link">Uy tín</a>
                    </li>

                    <li class="footer-nav-item">
                        <a href="#" class="footer-nav-link">Điều khoản</a>
                    </li>

                    <li class="footer-nav-item">
                        <a href="#" class="footer-nav-link">Về chúng tôi</a>
                    </li>

                    <li class="footer-nav-item">
                        <a href="#" class="footer-nav-link">Thanh toán</a>
                    </li>

                </ul>

                <ul class="footer-nav-list">

                    <li class="footer-nav-item">
                        <h2 class="nav-title">Dịch vụ</h2>
                    </li>

                    <li class="footer-nav-item">
                        <a href="#" class="footer-nav-link">Giao hàng nhanh</a>
                    </li>

                    <li class="footer-nav-item">
                        <a href="#" class="footer-nav-link">Đổi trả</a>
                    </li>

                    <li class="footer-nav-item">
                        <a href="#" class="footer-nav-link">Ưu đãi</a>
                    </li>

                    <li class="footer-nav-item">
                        <a href="#" class="footer-nav-link">Hỏa tốc nội thành</a>
                    </li>

                    <li class="footer-nav-item">
                        <a href="#" class="footer-nav-link">Hỗ trợ online</a>
                    </li>

                </ul>

                <ul class="footer-nav-list">

                    <li class="footer-nav-item">
                        <h2 class="nav-title">Liên hệ</h2>
                    </li>

                    <li class="footer-nav-item flex">
                        <div class="icon-box">
                            <ion-icon name="location-outline"></ion-icon>
                        </div>

                        <address class="content">
                            Thành phố Hồ Chí Minh
                        </address>
                    </li>

                    <li class="footer-nav-item flex">
                        <div class="icon-box">
                            <ion-icon name="call-outline"></ion-icon>
                        </div>

                        <a href="tel:+607936-8058" class="footer-nav-link">037-916-3407</a>
                    </li>

                    <li class="footer-nav-item flex">
                        <div class="icon-box">
                            <ion-icon name="mail-outline"></ion-icon>
                        </div>

                        <a href="mailto:example@gmail.com" class="footer-nav-link">hungdarlingch@gmail.com</a>
                    </li>

                </ul>

                <ul class="footer-nav-list">

                    <li class="footer-nav-item">
                        <h2 class="nav-title">Theo dõi</h2>
                    </li>

                    <li>
                        <ul class="social-link">

                            <li class="footer-nav-item">
                                <a href="#" class="footer-nav-link">
                                    <ion-icon name="logo-facebook"></ion-icon>
                                </a>
                            </li>

                            <li class="footer-nav-item">
                                <a href="#" class="footer-nav-link">
                                    <ion-icon name="logo-twitter"></ion-icon>
                                </a>
                            </li>

                            <li class="footer-nav-item">
                                <a href="#" class="footer-nav-link">
                                    <ion-icon name="logo-linkedin"></ion-icon>
                                </a>
                            </li>

                            <li class="footer-nav-item">
                                <a href="#" class="footer-nav-link">
                                    <ion-icon name="logo-instagram"></ion-icon>
                                </a>
                            </li>

                        </ul>
                    </li>

                </ul>

            </div>

        </div>

        <div class="footer-bottom">

            <div class="container">

                <p class="copyright">
                    Copyright &copy; <a href="#">HungHayho</a> all rights reserved.
                </p>

                <p class="copyright">
                    Bản quyền &copy; <a href="#">HungHayho</a> bảo lưu mọi quyền.
                </p>

            </div>

        </div>

    </footer>

    <script>
        const tabButtons = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.tab-content');

        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                tabButtons.forEach(btn => btn.classList.remove('active'));
                tabContents.forEach(content => content.classList.remove('active'));

                button.classList.add('active');
                document.getElementById(button.dataset.tab).classList.add('active');
            });
        });

        document.getElementById('avatar').addEventListener('change', function(event) {
            const input = event.target;
            const preview = document.getElementById('avatarPreview');

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    document.getElementById('profileForm').submit();
                };
                reader.readAsDataURL(input.files[0]);
            }
        });

        // Toggle form thêm sản phẩm
        document.getElementById('addProductBtn').addEventListener('click', function() {
            document.getElementById('addProductForm').style.display = 'block';
        });

        document.getElementById('cancelAddProductBtn').addEventListener('click', function() {
            document.getElementById('addProductForm').style.display = 'none';
        });

        // Xử lý chọn ảnh và xem trước
        document.getElementById('uploadImageBtn').addEventListener('click', function() {
            document.getElementById('image').click();
        });

        document.getElementById('image').addEventListener('change', function(event) {
            const file = event.target.files[0];
            const previewContainer = document.getElementById('imagePreview');
            const previewImg = document.getElementById('previewImg');

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    previewContainer.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });

        document.getElementById('removePreviewBtn').addEventListener('click', function() {
            const previewContainer = document.getElementById('imagePreview');
            const previewImg = document.getElementById('previewImg');
            const fileInput = document.getElementById('image');

            previewImg.src = '#';
            previewContainer.style.display = 'none';
            fileInput.value = ''; // Xóa file đã chọn
        });
    </script>
    <script type="text/javascript">
        function googleTranslateElementInit() {
            new google.translate.TranslateElement({
                    pageLanguage: 'vi', // Ngôn ngữ mặc định là Tiếng Việt
                    includedLanguages: 'vi,en,zh-CN,ja', // Chỉ gồm Tiếng Việt, Tiếng Anh, Tiếng Trung (giản thể), Tiếng Nhật
                    layout: google.translate.TranslateElement.InlineLayout.SIMPLE, // Giao diện menu thả xuống đơn giản
                    autoDisplay: false, // Không tự động hiển thị
                    gaTrack: false // Tắt Google Analytics tracking (tùy chọn)
                },
                'google_translate_element'
            );
        }
    </script>

    <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
    <script src="../Asset/Js/edit-profile.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>

</html>