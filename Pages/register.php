<?php
session_start();
require_once 'db_connect.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = filter_input(INPUT_POST, 'full_name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = isset($_POST['role']) ? $_POST['role'] : 'user'; // Mặc định là user

    if ($password !== $confirm_password) {
        $error = "Mật khẩu xác nhận không khớp!";
    } elseif (!in_array($role, ['user', 'admin'])) {
        $error = "Vai trò không hợp lệ!";
    } else {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        if ($stmt->fetchColumn() > 0) {
            $error = "Email đã được sử dụng!";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password, role) VALUES (:full_name, :email, :password, :role)");
            try {
                $stmt->execute([
                    'full_name' => $full_name,
                    'email' => $email,
                    'password' => $hashed_password,
                    'role' => $role
                ]);
                header("Location: login.php");
                exit;
            } catch (PDOException $e) {
                $error = "Đăng ký thất bại: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Ký - GOOD SMILE FIGURE</title>
    <link rel="shortcut icon" href="../Asset/Images/Logo/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="../Asset/Css/styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
</head>

<body>
    <!-- HEADER -->
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
                <h1 style="color: orange; text-align:center; letter-spacing: 3px;">ĐĂNG KÝ</h1>
                <div class="header-user-actions">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <span style="margin-bottom:11px"><?php echo htmlspecialchars($_SESSION['full_name'] ?? 'Khách'); ?></span>
                        <a href="pages/edit-profile.php" class="action-btn">
                            <img src="Asset/Images/<?php echo htmlspecialchars($_SESSION['avatar']); ?>"
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
            <button class="action-btn">
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
                <li class="menu-category"><a href="#" class="menu-title">Trang Chủ</a></li>
                <li class="menu-category">
                    <button class="accordion-menu" data-accordion-btn>
                        <p class="menu-title">Figure nam</p>
                        <div>
                            <ion-icon name="add-outline" class="add-icon"></ion-icon>
                            <ion-icon name="remove-outline" class="remove-icon"></ion-icon>
                        </div>
                    </button>
                    <ul class="submenu-category-list" data-accordion>
                        <li class="submenu-category"><a href="Product.php" class="submenu-title">Hành động</a></li>
                        <li class="submenu-category"><a href="Product.php" class="submenu-title">Isekai</a></li>
                        <li class="submenu-category"><a href="Product.php" class="submenu-title">Đời thường</a></li>
                        <li class="submenu-category"><a href="Product.php" class="submenu-title">Khác</a></li>
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
                        <li class="submenu-category"><a href="Product.php" class="submenu-title">Hành động</a></li>
                        <li class="submenu-category"><a href="Product.php" class="submenu-title">Isekai</a></li>
                        <li class="submenu-category"><a href="Product.php" class="submenu-title">Đời thường</a></li>
                        <li class="submenu-category"><a href="Product.php" class="submenu-title">Khác</a></li>
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
                        <li class="submenu-category"><a href="#" class="submenu-title">Bán chạy</a></li>
                        <li class="submenu-category"><a href="#" class="submenu-title">Mới về</a></li>
                        <li class="submenu-category"><a href="#" class="submenu-title">Đánh giá cao</a></li>
                        <li class="submenu-category"><a href="#" class="submenu-title">Ưu đãi</a></li>
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
                        <li class="submenu-category"><a href="#" class="submenu-title">Good Smile Company</a></li>
                        <li class="submenu-category"><a href="#" class="submenu-title">Max Factory</a></li>
                        <li class="submenu-category"><a href="#" class="submenu-title">Bandai Namco</a></li>
                        <li class="submenu-category"><a href="#" class="submenu-title">MegaHouse</a></li>
                    </ul>
                </li>
                <li class="menu-category"><a href="#" class="menu-title">Blog</a></li>
                <li class="menu-category"><a href="#" class="menu-title">Ưu Đãi Hấp Dẫn</a></li>
            </ul>
            <div class="menu-bottom">
                <ul class="menu-category-list">
                    <li class="menu-category">
                        <button class="accordion-menu" data-accordion-btn>
                            <p class="menu-title">Ngôn Ngữ</p>
                            <ion-icon name="caret-back-outline" class="caret-back"></ion-icon>
                        </button>
                        <ul class="submenu-category-list" data-accordion>
                            <li class="submenu-category"><a href="#" class="submenu-title">Tiếng Việt</a></li>
                            <li class="submenu-category"><a href="#" class="submenu-title">Tiếng Anh</a></li>
                        </ul>
                    </li>
                    <li class="menu-category">
                        <button class="accordion-menu" data-accordion-btn>
                            <p class="menu-title">Tiền Tệ</p>
                            <ion-icon name="caret-back-outline" class="caret-back"></ion-icon>
                        </button>
                        <ul class="submenu-category-list" data-accordion>
                            <li class="submenu-category"><a href="#" class="submenu-title">VND</a></li>
                            <li class="submenu-category"><a href="#" class="submenu-title">USD</a></li>
                        </ul>
                    </li>
                </ul>
                <ul class="menu-social-container">
                    <li><a href="https://www.facebook.com/hung.hay.ho.705636" class="social-link"><ion-icon name="logo-facebook"></ion-icon></a></li>
                    <li><a href="#" class="social-link"><ion-icon name="logo-twitter"></ion-icon></a></li>
                    <li><a href="#" class="social-link"><ion-icon name="logo-instagram"></ion-icon></a></li>
                    <li><a href="https://docs.google.com/document/d/11eb-9DV3taTwuMiuHT6DhwzeZ-Ky7Q-uobBnBAp2mn4/edit?tab=t.0" class="social-link"><ion-icon name="mail-outline"></ion-icon></a></li>
                </ul>
            </div>
        </nav>
    </header>

    <!-- MAIN ĐĂNG KÝ -->
    <main>
        <div class="container payment-container">
            <h2 class="title">Đăng Ký Tài Khoản</h2>
            <div class="payment-grid">
                <div class="payment-form">
                    <h3 class="payment-section-title">Thông Tin Đăng Ký</h3>

                    <form id="registerForm" method="POST">
                        <div class="form-group">
                            <label for="full_name">Họ và tên</label>
                            <input type="text" id="full_name" name="full_name" placeholder="Nhập họ và tên" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" placeholder="Nhập email của bạn" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Mật khẩu</label>
                            <input type="password" id="password" name="password" placeholder="Nhập mật khẩu" required>
                        </div>
                        <div class="form-group">
                            <label for="confirm_password">Xác nhận mật khẩu</label>
                            <input type="password" id="confirm_password" name="confirm_password" placeholder="Nhập lại mật khẩu" required>
                        </div>
                        <div class="form-group">
                            <label for="role">Vai trò</label>
                            <div class="role-options">
                                <input type="radio" id="user" name="role" value="user" <?php echo ($role ?? 'user') === 'user' ? 'checked' : ''; ?> required>
                                <label for="user" class="role-label">Người dùng</label>

                                <input type="radio" id="admin" name="role" value="admin" <?php echo ($role ?? '') === 'admin' ? 'checked' : ''; ?>>
                                <label for="admin" class="role-label">Quản trị viên</label>
                            </div>
                        </div>
                        <button type="submit" class="payment-btn">Đăng Ký</button>
                    </form>
                    <p class="login-link">Đã có tài khoản? <a href="login.php" class="category-btn">Đăng nhập ngay</a></p>
                </div>

                <div class="order-summary">
                    <h3 class="payment-section-title">Thông Tin Hỗ Trợ</h3>
                    <div class="summary-item">
                        <span>Liên hệ:</span>
                        <span><a href="mailto:hungdarlingch@gmail.com">hungdarlingch@gmail.com</a></span>
                    </div>
                    <div class="summary-item">
                        <span>Số điện thoại:</span>
                        <span><a href="tel:0379163407">037-916-3407</a></span>
                    </div>
                    <div class="summary-item">
                        <span>Địa chỉ:</span>
                        <span>Thành phố Hồ Chí Minh</span>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- FOOTER -->
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
                    <li class="footer-nav-item"><a href="#" class="footer-nav-link">POP UP PARADE</a></li>
                    <li class="footer-nav-item"><a href="#" class="footer-nav-link">figma Series</a></li>
                    <li class="footer-nav-item"><a href="#" class="footer-nav-link">Plushie</a></li>
                    <li class="footer-nav-item"><a href="#" class="footer-nav-link">Goods</a></li>
                    <li class="footer-nav-item"><a href="Product.php" class="footer-nav-link">Figure</a></li>
                </ul>
                <ul class="footer-nav-list">
                    <li class="footer-nav-item">
                        <h2 class="nav-title">Sản phẩm</h2>
                    </li>
                    <li class="footer-nav-item"><a href="#" class="footer-nav-link">Giảm giá</a></li>
                    <li class="footer-nav-item"><a href="#" class="footer-nav-link">Sản phẩm mới</a></li>
                    <li class="footer-nav-item"><a href="#" class="footer-nav-link">Bán chạy</a></li>
                    <li class="footer-nav-item"><a href="#" class="footer-nav-link">Liên hệ</a></li>
                    <li class="footer-nav-item"><a href="#" class="footer-nav-link">Vị trí cửa hàng</a></li>
                </ul>
                <ul class="footer-nav-list">
                    <li class="footer-nav-item">
                        <h2 class="nav-title">Công ty</h2>
                    </li>
                    <li class="footer-nav-item"><a href="#" class="footer-nav-link">Giao hàng</a></li>
                    <li class="footer-nav-item"><a href="#" class="footer-nav-link">Uy tín</a></li>
                    <li class="footer-nav-item"><a href="#" class="footer-nav-link">Điều khoản</a></li>
                    <li class="footer-nav-item"><a href="#" class="footer-nav-link">Về chúng tôi</a></li>
                    <li class="footer-nav-item"><a href="#" class="footer-nav-link">Thanh toán</a></li>
                </ul>
                <ul class="footer-nav-list">
                    <li class="footer-nav-item">
                        <h2 class="nav-title">Dịch vụ</h2>
                    </li>
                    <li class="footer-nav-item"><a href="#" class="footer-nav-link">Giao hàng nhanh</a></li>
                    <li class="footer-nav-item"><a href="#" class="footer-nav-link">Đổi trả</a></li>
                    <li class="footer-nav-item"><a href="#" class="footer-nav-link">Ưu đãi</a></li>
                    <li class="footer-nav-item"><a href="#" class="footer-nav-link">Hỏa tốc nội thành</a></li>
                    <li class="footer-nav-item"><a href="#" class="footer-nav-link">Hỗ trợ online</a></li>
                </ul>
                <ul class="footer-nav-list">
                    <li class="footer-nav-item">
                        <h2 class="nav-title">Liên hệ</h2>
                    </li>
                    <li class="footer-nav-item flex">
                        <div class="icon-box"><ion-icon name="location-outline"></ion-icon></div>
                        <address class="content">Thành phố Hồ Chí Minh</address>
                    </li>
                    <li class="footer-nav-item flex">
                        <div class="icon-box"><ion-icon name="call-outline"></ion-icon></div>
                        <a href="tel:+607936-8058" class="footer-nav-link">037-916-3407</a>
                    </li>
                    <li class="footer-nav-item flex">
                        <div class="icon-box"><ion-icon name="mail-outline"></ion-icon></div>
                        <a href="mailto:hungdarlingch@gmail.com" class="footer-nav-link">hungdarlingch@gmail.com</a>
                    </li>
                </ul>
                <ul class="footer-nav-list">
                    <li class="footer-nav-item">
                        <h2 class="nav-title">Theo dõi</h2>
                    </li>
                    <li>
                        <ul class="social-link">
                            <li class="footer-nav-item"><a href="#" class="footer-nav-link"><ion-icon name="logo-facebook"></ion-icon></a></li>
                            <li class="footer-nav-item"><a href="#" class="footer-nav-link"><ion-icon name="logo-twitter"></ion-icon></a></li>
                            <li class="footer-nav-item"><a href="#" class="footer-nav-link"><ion-icon name="logo-linkedin"></ion-icon></a></li>
                            <li class="footer-nav-item"><a href="#" class="footer-nav-link"><ion-icon name="logo-instagram"></ion-icon></a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="container">
                <p class="copyright">
                    Copyright © <a href="#">HungHayho</a> all rights reserved.
                </p>
                <p class="copyright">
                    Bản quyền © <a href="#">HungHayho</a> bảo lưu mọi quyền.
                </p>
            </div>
        </div>
    </footer>


    <!-- Scripts -->
    <script src="../Asset/Js/script.js"></script>
    <script type="text/javascript">
        function googleTranslateElementInit() {
            new google.translate.TranslateElement({
                pageLanguage: 'vi',
                includedLanguages: 'vi,en,zh-CN,ja',
                layout: google.translate.TranslateElement.InlineLayout.SIMPLE,
                autoDisplay: false,
                gaTrack: false
            }, 'google_translate_element');
        }
    </script>
    <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>

</html>