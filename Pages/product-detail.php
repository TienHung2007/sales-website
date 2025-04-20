<?php
session_start();
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}

if (!isset($_SESSION['avatar']) || empty($_SESSION['avatar'])) {
    $_SESSION['avatar'] = 'avatars/default-avatar.png';
}

require_once 'db_connect.php';

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Xử lý yêu thích khi nhận yêu cầu POST
if (isset($_SESSION['user_id']) && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_favorite'])) {
    $user_id = $_SESSION['user_id'];

    try {
        // Kiểm tra xem sản phẩm đã được yêu thích chưa
        $check_stmt = $pdo->prepare("SELECT id FROM user_favorites WHERE user_id = :user_id AND product_id = :product_id");
        $check_stmt->execute(['user_id' => $user_id, 'product_id' => $product_id]);
        $is_favorited = $check_stmt->fetch(PDO::FETCH_ASSOC);

        if ($is_favorited) {
            // Nếu đã yêu thích, xóa khỏi danh sách
            $delete_stmt = $pdo->prepare("DELETE FROM user_favorites WHERE user_id = :user_id AND product_id = :product_id");
            $delete_stmt->execute(['user_id' => $user_id, 'product_id' => $product_id]);
        } else {
            // Nếu chưa yêu thích, thêm vào danh sách
            $insert_stmt = $pdo->prepare("INSERT INTO user_favorites (user_id, product_id) VALUES (:user_id, :product_id)");
            $insert_stmt->execute(['user_id' => $user_id, 'product_id' => $product_id]);
        }
        // Chuyển hướng lại trang để tránh lặp lại POST
        header("Location: product-detail.php?id=$product_id");
        exit();
    } catch (PDOException $e) {
        // Ghi log lỗi để kiểm tra (không hiển thị trực tiếp cho người dùng)
        error_log("Lỗi khi xử lý yêu thích: " . $e->getMessage());
        die("Có lỗi xảy ra khi xử lý yêu thích. Vui lòng thử lại sau.");
    }
}

// Lấy thông tin sản phẩm
$stmt = $pdo->prepare("
    SELECT p.`id`, p.`name`, p.`category`, p.`price`, p.`old_price`, p.`image`, 
           pd.`image_hover`, pd.`stock`, pd.`description`, pd.`sold`, pd.`rating`, pd.`remaining`,
           pd.`size`, pd.`scale`, pd.`material`, pd.`weight`, pd.`release_date`
    FROM `products` p
    LEFT JOIN `product_details` pd ON p.`id` = pd.`product_id`
    WHERE p.`id` = :id
");
$stmt->execute(['id' => $product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    die("Sản phẩm không tồn tại.");
}

$is_out_of_stock = ($product['stock'] === 0 || $product['sold'] >= $product['stock']);

// Kiểm tra trạng thái yêu thích của sản phẩm (nếu người dùng đã đăng nhập)
$is_favorited = false;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $check_stmt = $pdo->prepare("SELECT id FROM user_favorites WHERE user_id = :user_id AND product_id = :product_id");
    $check_stmt->execute(['user_id' => $user_id, 'product_id' => $product_id]);
    $is_favorited = $check_stmt->fetch(PDO::FETCH_ASSOC) !== false;
}

// Truy vấn hình ảnh sản phẩm
$stmt_images = $pdo->prepare("
    SELECT `image_url` 
    FROM `product_images` 
    WHERE `product_id` = :product_id
");
$stmt_images->execute(['product_id' => $product_id]);
$product_images = $stmt_images->fetchAll(PDO::FETCH_ASSOC);

// Truy vấn đánh giá
$stmt_reviews = $pdo->prepare("
    SELECT r.*, u.full_name, u.avatar 
    FROM reviews r 
    JOIN users u ON r.user_id = u.id 
    WHERE r.product_id = :product_id 
    ORDER BY r.review_date DESC
");
$stmt_reviews->execute(['product_id' => $product_id]);
$reviews = $stmt_reviews->fetchAll(PDO::FETCH_ASSOC);

// Truy vấn sản phẩm liên quan ngẫu nhiên
$stmt_related = $pdo->prepare("
    SELECT p.`id`, p.`name`, p.`category`, p.`price`, p.`old_price`, p.`image`, 
           pd.`stock`, pd.`sold`
    FROM `products` p
    LEFT JOIN `product_details` pd ON p.`id` = pd.`product_id`
    WHERE p.`id` != :current_id
    ORDER BY RAND()
    LIMIT 4
");
$stmt_related->execute(['current_id' => $product_id]);
$related_products = $stmt_related->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GOOD SMILE FIGURE - Chi Tiết Sản Phẩm</title>
    <link rel="shortcut icon" href="../Asset/Images/Logo/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="../Asset/Css/styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
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
                <h1 style="color: orange; text-align:center; letter-spacing: 3px; "> SẢN PHẨM </h1>
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

                                    <a href="product.php?sub_category=FIGMA">FIGMA</a>

                                </li>

                                <li class="panel-list-item">

                                    <a href="product.php?sub_category=other">Khác</a>

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

                                <a href="product.php?sub_category=other Female">Khác</a>

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

    <!-- MAIN CONTENT -->

    <main>
        <div class="container product-detail-container">
            <!-- Breadcrumb -->

            <div class="breadcrumb">
                <a href="index.php">Trang chủ</a> &gt;
                <a href="category.php?cat=<?php echo htmlspecialchars($product['category']); ?>">
                    <?php echo htmlspecialchars($product['category']); ?>
                </a> &gt;
                <?php echo htmlspecialchars($product['name']); ?>
            </div>

            <div class="product-detail">
                <div class="product-images">
                    <div class="main-image">
                        <img src="../<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="zoomable">
                    </div>
                    <?php if (!empty($product_images)): ?>
                        <div class="thumbnail-images">
                            <?php foreach ($product_images as $index => $image): ?>
                                <img src="../<?php echo htmlspecialchars($image['image_url']); ?>" alt="Thumbnail <?php echo $index + 1; ?>" class="thumbnail <?php echo $index === 0 ? 'active' : ''; ?>">
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="product-info">
                    <h1 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h1>
                    <p class="product-subtitle"><?php echo htmlspecialchars($product['category']); ?></p>

                    <div class="price-box">
                        <span class="price"><?php echo number_format($product['price'], 0, ',', '.'); ?> VNĐ</span>
                        <?php if ($product['old_price']): ?>
                            <span class="old-price"><?php echo number_format($product['old_price'], 0, ',', '.'); ?> VNĐ</span>
                            <span class="discount-label"><?php echo round((($product['old_price'] - $product['price']) / $product['old_price']) * 100); ?>% OFF</span>
                        <?php endif; ?>
                    </div>

                    <div class="stock-status">
                        <?php if ($is_out_of_stock): ?>
                            <ion-icon name="close-circle-outline"></ion-icon> Hết hàng
                        <?php else: ?>
                            <ion-icon name="checkmark-circle-outline"></ion-icon> Còn hàng
                        <?php endif; ?>
                    </div>

                    <p class="short-desc"><?php echo htmlspecialchars($product['description']); ?></p>

                    <div class="product-actions">
                        <?php if ($is_out_of_stock): ?>
                            <button class="out-of-stock-btn">Hết hàng</button>
                        <?php else: ?>
                            <div class="quantity-selector">
                                <button class="qty-btn" onclick="updateQuantity(-1)">-</button>
                                <input type="number" value="1" min="1" max="<?php echo $product['stock'] - $product['sold']; ?>" class="quantity">
                                <button class="qty-btn" onclick="updateQuantity(1)">+</button>
                            </div>
                            <button class="add-to-cart-btn">
                                <ion-icon name="cart-outline"></ion-icon> Thêm vào giỏ
                            </button>
                        <?php endif; ?>

                        <!-- Nút yêu thích -->
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <form method="POST" action="">
                                <button type="submit" name="toggle_favorite" class="favorite-btn <?php echo $is_favorited ? 'favorited' : ''; ?>" title="<?php echo $is_favorited ? 'Bỏ yêu thích' : 'Thêm vào yêu thích'; ?>">
                                    <ion-icon name="<?php echo $is_favorited ? 'heart' : 'heart-outline'; ?>"></ion-icon>
                                </button>
                            </form>
                        <?php else: ?>
                            <button class="favorite-btn" disabled onclick="alert('Vui lòng đăng nhập để sử dụng tính năng này!');" title="Yêu thích">
                                <ion-icon name="heart-outline"></ion-icon>
                            </button>
                        <?php endif; ?>
                    </div>

                    <div class="quick-info">
                        <p><strong>Kích thước:</strong> <?php echo htmlspecialchars($product['size']); ?></p>
                        <p><strong>Tỷ lệ:</strong> <?php echo htmlspecialchars($product['scale']); ?></p>
                        <p><strong>Chất liệu:</strong> <?php echo htmlspecialchars($product['material']); ?></p>
                        <p><strong>Trọng lượng:</strong> <?php echo htmlspecialchars($product['weight']); ?></p>
                        <p><strong>Ngày phát hành:</strong> <?php echo htmlspecialchars($product['release_date']); ?></p>
                    </div>

                    <div class="share-buttons">
                        <p>Chia sẻ:</p>
                        <a href="#" class="social-link"><ion-icon name="logo-facebook"></ion-icon></a>
                        <a href="#" class="social-link"><ion-icon name="logo-twitter"></ion-icon></a>
                        <a href="#" class="social-link"><ion-icon name="logo-instagram"></ion-icon></a>
                    </div>
                </div>
            </div>


            <!-- Tabs -->
            <section class="product-tabs">
                <div class="tab-nav">
                    <button class="tab-btn active" data-tab="description">Mô tả</button>
                    <button class="tab-btn" data-tab="specs">Thông số kỹ thuật</button>
                    <button class="tab-btn" data-tab="reviews">Đánh giá</button>
                </div>
                <div class="tab-content">
                    <!-- Tab Mô tả -->
                    <div class="tab-pane active" id="description">
                        <h3>Mô tả sản phẩm</h3>
                        <p><?php echo htmlspecialchars($product['description'] ?? 'Mô tả sản phẩm đang được cập nhật.'); ?></p>
                    </div>

                    <!-- Tab Thông số kỹ thuật -->
                    <div class="tab-pane" id="specs">
                        <h3>Thông số kỹ thuật</h3>
                        <table class="specs-table">
                            <tr>
                                <th>Nhân vật :</th>
                                <td><?php echo htmlspecialchars($product['name'] ?? 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <th>Series :</th>
                                <td><?php echo htmlspecialchars($product['category'] ?? 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <th>Hãng sản xuất :</th>
                                <td>Good Smile Company</td>
                            </tr>
                            <tr>
                                <th>Tỷ lệ :</th>
                                <td><?php echo htmlspecialchars($product['scale'] ?? 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <th>Kích thước :</th>
                                <td><?php echo htmlspecialchars($product['size'] ?? 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <th>Chất liệu :</th>
                                <td><?php echo htmlspecialchars($product['material'] ?? 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <th>Trọng lượng :</th>
                                <td><?php echo htmlspecialchars($product['weight'] ?? 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <th>Ngày phát hành</th>
                                <td><?php echo $product['release_date'] ? date('d/m/Y', strtotime($product['release_date'])) : 'N/A'; ?></td>
                            </tr>
                        </table>
                    </div>

                    <!-- Tab Đánh giá -->
                    <div class="tab-pane" id="reviews">
                        <div class="product-reviews">
                            <h3>Đánh giá sản phẩm</h3>
                            <div class="reviews-list">
                                <?php if (count($reviews) > 0): ?>
                                    <?php foreach ($reviews as $row): ?>
                                        <div class="review-item" data-review-id="<?php echo $row['id']; ?>">
                                            <div class="review-header">
                                                <img src="../Asset/Images/<?php echo htmlspecialchars($row['avatar']); ?>" alt="Avatar" class="user-avatar" onerror="this.src='../Asset/Images/avatars/default-avatar.png'">
                                                <span class="reviewer-name"><?php echo htmlspecialchars($row['full_name']); ?></span>
                                                <div class="review-rating">
                                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                                        <ion-icon name="<?php echo $i <= $row['rating'] ? 'star' : 'star-outline'; ?>"></ion-icon>
                                                    <?php endfor; ?>
                                                </div>
                                                <span class="review-date"><?php echo $row['review_date']; ?></span>
                                                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $row['user_id']): ?>
                                                    <button class="delete-review-btn" data-review-id="<?php echo $row['id']; ?>"><ion-icon name="trash-outline"></ion-icon></button>
                                                <?php endif; ?>
                                            </div>
                                            <p class="review-text"><?php echo htmlspecialchars($row['review_text']); ?></p>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p>Chưa có đánh giá nào cho sản phẩm này.</p>
                                <?php endif; ?>
                            </div>

                            <!-- Form gửi đánh giá -->
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <div class="add-review">
                                    <h4>Viết đánh giá của bạn</h4>
                                    <form action="submit_review.php" method="POST">
                                        <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                                        <div class="rating-input">
                                            <label for="rating">Đánh giá sao:</label>
                                            <div class="stars" id="star-rating">
                                                <ion-icon name="star-outline" data-value="1"></ion-icon>
                                                <ion-icon name="star-outline" data-value="2"></ion-icon>
                                                <ion-icon name="star-outline" data-value="3"></ion-icon>
                                                <ion-icon name="star-outline" data-value="4"></ion-icon>
                                                <ion-icon name="star-outline" data-value="5"></ion-icon>
                                            </div>
                                            <input type="hidden" name="rating" id="rating-value" value="0" required>
                                        </div>
                                        <textarea name="review_text" placeholder="Nhập nội dung đánh giá của bạn..." required></textarea>
                                        <button type="submit" class="submit-review-btn">Gửi đánh giá</button>
                                    </form>
                                </div>
                            <?php else: ?>
                                <div class="login-to-review">
                                    <p>Bạn cần đăng nhập để gửi đánh giá.</p>
                                    <a href="login.php" class="login-review-btn">Đăng nhập ngay</a>
                                </div>
                                <p class="login-link">Chưa có tài khoản? <a href="register.php" class="category-btn">Đăng ký ngay</a></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Related Products -->
            <section class="related-products" style="margin-top: 40px;">
                <div class="product-showcase" style="max-width: 1300px; margin: 0 auto;">
                    <h2 class="title" style="text-align: center; margin-bottom: 20px; font-size: 24px; color: #333;">Sản phẩm khác</h2>
                    <div class="showcase-wrapper has-scrollbar" style="overflow-x: auto; padding-bottom: 10px;">
                        <div class="showcase-container" style="display: flex; gap: 15px; justify-content: center;">
                            <?php foreach ($related_products as $related): ?>
                                <div class="showcase" style="flex: 0 0 auto; width: 300px; position: relative; text-align: center;">
                                    <div class="showcase-banner" style="position: relative; width: 300px; height: 250px; margin: 0 auto;">
                                        <img src="../<?php echo htmlspecialchars($related['image']); ?>"
                                            alt="<?php echo htmlspecialchars($related['name']); ?>"
                                            class="product-img default"
                                            style="width: 100%; height: 100%; object-fit: cover; position: absolute; top: 0; left: 0;">

                                        <!-- Nhãn -->
                                        <?php if (!empty($related['is_new_product']) && $related['is_new_product'] == 1): ?>
                                            <p class="showcase-badge angle pink" style="position: absolute; top: 10px; left: -10px; background: orange; color: #fff; padding: 5px 10px; font-size: 12px; transform: rotate(-45deg); transform-origin: 0 0;">Mới</p>
                                        <?php elseif ($related['stock'] > 0 && $related['sold'] >= $related['stock']): ?>
                                            <p class="showcase-badge angle black" style="position: absolute; top: 10px; left: -10px; background: #333; color: #fff; padding: 5px 10px; font-size: 12px; transform: rotate(-45deg); transform-origin: 0 0;">Đã bán</p>
                                        <?php elseif ($related['old_price'] > 0 && $related['price'] < $related['old_price']):
                                            $discount = round((($related['old_price'] - $related['price']) / $related['old_price']) * 100); ?>
                                            <p class="showcase-badge" style="position: absolute; top: 10px; right: 10px; background: orange; color: #fff; padding: 5px 10px; font-size: 12px; border-radius: 50%;"><?php echo $discount; ?>%</p>
                                        <?php endif; ?>

                                        <div class="showcase-actions" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); display: none; gap: 10px;">
                                            <button class="btn-action" style="background: #fff; border: none; padding: 8px; border-radius: 50%; cursor: pointer; box-shadow: 0 2px 5px rgba(0,0,0,0.2);" onmouseover="this.parentElement.style.display='flex';" onmouseout="this.parentElement.style.display='none';">
                                                <ion-icon name="heart-outline" style="font-size: 18px; color: #333;"></ion-icon>
                                            </button>
                                            <button class="btn-action" style="background: #fff; border: none; padding: 8px; border-radius: 50%; cursor: pointer; box-shadow: 0 2px 5px rgba(0,0,0,0.2);" onmouseover="this.parentElement.style.display='flex';" onmouseout="this.parentElement.style.display='none';">
                                                <ion-icon name="eye-outline" style="font-size: 18px; color: #333;"></ion-icon>
                                            </button>
                                            <button class="btn-action" style="background: #fff; border: none; padding: 8px; border-radius: 50%; cursor: pointer; box-shadow: 0 2px 5px rgba(0,0,0,0.2);" onmouseover="this.parentElement.style.display='flex';" onmouseout="this.parentElement.style.display='none';">
                                                <ion-icon name="repeat-outline" style="font-size: 18px; color: #333;"></ion-icon>
                                            </button>
                                            <button class="btn-action" style="background: #fff; border: none; padding: 8px; border-radius: 50%; cursor: pointer; box-shadow: 0 2px 5px rgba(0,0,0,0.2);" onmouseover="this.parentElement.style.display='flex';" onmouseout="this.parentElement.style.display='none';">
                                                <ion-icon name="bag-add-outline" style="font-size: 18px; color: #333;"></ion-icon>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="showcase-content" style="padding: 10px 0;">
                                        <a href="product.php?category=<?php echo urlencode($related['category']); ?>"
                                            class="showcase-category"
                                            style="text-decoration: none; color: #666; font-size: 12px; display: block; margin-bottom: 5px;">
                                            <?php echo htmlspecialchars($related['category']); ?>
                                        </a>
                                        <a href="product-detail.php?id=<?php echo htmlspecialchars($related['id']); ?>"
                                            style="text-decoration: none; color: #333; font-size: 16px; display: block; margin-bottom: 5px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                            <h3 class="showcase-title" style="margin: 0; font-size: 16px;"><?php echo htmlspecialchars($related['name']); ?></h3>
                                        </a>
                                        <div class="showcase-rating" style="display: flex; justify-content: center; gap: 2px; margin-bottom: 5px;">
                                            <?php
                                            $rating = isset($related['rating']) ? (float)$related['rating'] : 4.0;
                                            for ($i = 1; $i <= 5; $i++):
                                                if ($i <= floor($rating)) {
                                                    echo '<ion-icon name="star" style="color: #ff6f61; font-size: 12px;"></ion-icon>';
                                                } elseif ($i == ceil($rating) && $rating - floor($rating) >= 0.5) {
                                                    echo '<ion-icon name="star-half-outline" style="color: #ff6f61; font-size: 12px;"></ion-icon>';
                                                } else {
                                                    echo '<ion-icon name="star-outline" style="color: #ff6f61; font-size: 12px;"></ion-icon>';
                                                }
                                            endfor;
                                            ?>
                                        </div>
                                        <div class="price-related" style="font-size: 16px; color: #ff6f61; font-weight: bold; text-align: center;">
                                            <p class="price" style="display: inline; margin-right: 5px;"><?php echo number_format((float)$related['price'] / 1000, 0) . 'k'; ?></p>
                                            <?php if ($related['old_price'] > 0): ?>
                                                <del style="color: #999; font-size: 14px; font-weight: normal;"><?php echo number_format((float)$related['old_price'] / 1000, 0) . 'k'; ?></del>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>

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

    <!-- SCRIPTS -->
    <script src="../Asset/Js/favorites.js"></script>
    <script src="../Asset/Js/script.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Chuyển đổi hình ảnh thumbnail
            const thumbnails = document.querySelectorAll('.thumbnail');
            const mainImage = document.querySelector('.main-image img');

            if (thumbnails.length && mainImage) {
                thumbnails.forEach(thumb => {
                    thumb.addEventListener('click', function() {
                        thumbnails.forEach(t => t.classList.remove('active'));
                        this.classList.add('active');
                        mainImage.src = this.src;
                    });
                });
            }

            // Điều chỉnh số lượng
            const qtyInput = document.querySelector('.quantity');
            const maxQty = <?php echo $product['stock'] - $product['sold']; ?>;

            function updateQuantity(change) {
                if (!qtyInput) return;

                let value = parseInt(qtyInput.value) || 1;
                value += change;

                if (value < 1) value = 1;
                if (value > maxQty) value = maxQty;

                qtyInput.value = value;
            }

            // Gán sự kiện cho nút tăng/giảm
            const increaseBtn = document.querySelector('.qty-btn:nth-child(3)');
            const decreaseBtn = document.querySelector('.qty-btn:nth-child(1)');

            if (increaseBtn) {
                increaseBtn.addEventListener('click', () => updateQuantity(1));
            }
            if (decreaseBtn) {
                decreaseBtn.addEventListener('click', () => updateQuantity(-1));
            }

            // Chuyển đổi tab
            const tabButtons = document.querySelectorAll('.tab-btn');
            if (tabButtons.length) {
                tabButtons.forEach(btn => {
                    btn.addEventListener('click', function() {
                        tabButtons.forEach(b => b.classList.remove('active'));
                        document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));

                        this.classList.add('active');
                        const targetTab = document.getElementById(this.dataset.tab);
                        if (targetTab) targetTab.classList.add('active');
                    });
                });
            }
        });

        // Xử lý đánh giá sao
        const stars = document.querySelectorAll('#star-rating ion-icon');
        const ratingInput = document.getElementById('rating-value');

        stars.forEach(star => {
            star.addEventListener('click', function() {
                const rating = parseInt(this.getAttribute('data-value'));
                ratingInput.value = rating;
                stars.forEach(s => {
                    const value = parseInt(s.getAttribute('data-value'));
                    s.setAttribute('name', value <= rating ? 'star' : 'star-outline');
                    s.classList.toggle('selected', value <= rating);
                });
            });

            star.addEventListener('mouseover', function() {
                const hoverRating = parseInt(this.getAttribute('data-value'));
                stars.forEach(s => {
                    const value = parseInt(s.getAttribute('data-value'));
                    s.classList.toggle('hover', value <= hoverRating);
                });
            });

            star.addEventListener('mouseout', function() {
                stars.forEach(s => s.classList.remove('hover'));
            });
        });

        // Xử lý gửi đánh giá
        const reviewForm = document.querySelector('#reviews form');
        if (reviewForm) {
            reviewForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const rating = parseInt(ratingInput.value);
                const reviewText = this.querySelector('textarea').value.trim();

                if (rating > 0 && reviewText) {
                    this.submit();
                } else {
                    alert('Vui lòng chọn số sao và viết nội dung đánh giá!');
                }
            });
        }

        // Xử lý xóa đánh giá
        document.querySelectorAll('.delete-review-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                if (confirm('Bạn có chắc muốn xóa đánh giá này?')) {
                    const reviewId = this.getAttribute('data-review-id');
                    const productId = document.querySelector('input[name="product_id"]').value;

                    fetch('delete_review.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: `review_id=${reviewId}&product_id=${productId}`
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                document.querySelector(`.review-item[data-review-id="${reviewId}"]`).remove();
                                if (document.querySelectorAll('.review-item').length === 0) {
                                    document.querySelector('.reviews-list').innerHTML = '<p>Chưa có đánh giá nào cho sản phẩm này.</p>';
                                }
                            } else {
                                alert(data.message || 'Xóa đánh giá thất bại!');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Có lỗi xảy ra khi xóa đánh giá!');
                        });
                }
            });
        });
    </script>
</body>

</html>