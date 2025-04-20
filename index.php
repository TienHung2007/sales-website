<?php
session_start();
if (isset($_GET['logout'])) {
  session_destroy();
  header("Location: pages/login.php");
  exit;
}

require_once 'pages/db_connect.php';
require_once 'pages/function.php'; // Bao gồm file functions.php

// Kết nối cơ sở dữ liệu
$host = '127.0.0.1';
$dbname = 'good_smile_db';
$username = 'root';
$password = '';

try {
  $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  die("Kết nối thất bại: " . $e->getMessage());
}

// Xử lý tìm kiếm
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$search_products = [];
if ($search) {
  $stmt_search = $pdo->prepare("
        SELECT p.id, p.name, p.price, p.old_price, p.image, pd.sold 
        FROM products p 
        JOIN product_details pd ON p.id = pd.product_id 
        WHERE p.name LIKE :search 
        ORDER BY p.name ASC 
        LIMIT 8
    ");
  $stmt_search->execute(['search' => "%$search%"]);
  $search_products = $stmt_search->fetchAll(PDO::FETCH_ASSOC);
}

// Truy vấn sản phẩm bán chạy (Best Sellers)
$stmt_bestsellers = $pdo->query("
    SELECT p.id, p.name, p.price, p.old_price, p.image, pd.sold 
    FROM products p 
    JOIN product_details pd ON p.id = pd.product_id 
    WHERE pd.sold > 0 
    ORDER BY pd.sold DESC 
    LIMIT 4
");
$bestsellers = $stmt_bestsellers->fetchAll(PDO::FETCH_ASSOC);

// Truy vấn sản phẩm mới (New Arrivals)
$stmt_new = $pdo->query("
    SELECT p.id, p.name, p.price, p.old_price, p.image 
    FROM products p 
    JOIN product_details pd ON p.id = pd.product_id 
    WHERE pd.is_new_arrival = 1 
    ORDER BY pd.created_at DESC 
    LIMIT 4
");
$new_products = $stmt_new->fetchAll(PDO::FETCH_ASSOC);

// Kiểm tra và gán avatar mặc định nếu chưa có
if (!isset($_SESSION['avatar']) || empty($_SESSION['avatar'])) {
  $_SESSION['avatar'] = 'avatars/default-avatar.png'; // Đường dẫn mặc định
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>

  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>GOOD SMILE FIGURE</title>

  <link rel="shortcut icon" href="Asset/Images/Logo/favicon.png" type="image/x-icon">

  <link rel="stylesheet" href="Asset/Css/styles.css">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

</head>

<body>

  <div class="overlay" data-overlay></div>

  <!--
      - CỬA SỔ POP-UP
    -->

  <div class="modal" data-modal>

    <div class="modal-close-overlay" data-modal-overlay></div>

    <div class="modal-content">

      <button class="modal-close-btn" data-modal-close>

        <ion-icon name="close-outline"></ion-icon>

      </button>

      <div class="newsletter-img">

        <img src="Asset/Images/newletter.png" alt="đăng ký nhận bản tin" width="700" height="300">

      </div>

      <div class="newsletter">

        <form action="#">

          <div class="newsletter-header">

            <h3 class="newsletter-title">THEO DÕI THÔNG TIN ƯU ĐÃI</h3>

            <p class="newsletter-desc">
              Theo dõi<b> GOOD SMILE FIGURE</b> để nhận thông tin cập nhật sản phẩm mới nhất và ưu đãi.
            </p>

          </div>

          <input type="email" name="email" class="email-field" placeholder="Địa Chỉ Email" required>

          <button type="submit" class="btn-newsletter">Theo dõi</button>

        </form>

      </div>

    </div>

  </div>

  <!--
      - THÔNG BÁO TOAST
    -->

  <!-- Toast tự động: "Có người vừa mua sản phẩm" -->

  <div class="notification-toast auto-toast" data-toast-auto>

    <button class="toast-close-btn" data-toast-close-auto>

      <ion-icon name="close-outline"></ion-icon>

    </button>

    <div class="toast-banner">

      <img src="Asset/Images/Products/Product-1.webp" alt="Sản phẩm" width="70" height="70">

    </div>

    <div class="toast-detail">

      <p class="toast-message">Có người vừa mua sản phẩm</p>
      <p class="toast-title">Hoshino Ruby Figure</p>
      <p class="toast-meta">5 phút trước</p>

    </div>

  </div>

  <!-- Toast khi theo dõi -->

  <div class="notification-toast follow-toast" data-toast-follow>

    <button class="toast-close-btn" data-toast-close-follow>

      <ion-icon name="close-outline"></ion-icon>

    </button>

    <div class="toast-banner">

      <img src="Asset/Images/Logo/favicon.png" alt="Thông báo" width="70" height="70">

    </div>

    <div class="toast-detail">

      <p class="toast-message">Theo dõi thành công</p>

    </div>

  </div>

  <!--
      - HEADER
    -->

  <header>

    <!-- HEADER 1 -->

    <div class="header-top">
      <div class="container">
        <ul class="header-social-container">
          <li>
            <a href="https://www.facebook.com/hung.hay.ho.705636/" class="social-link">
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

    <!-- HEADER 2 -->

    <div class="header-main">

      <div class="container">

        <a href="#" class="header-logo">

          <img src="Asset/Images/Logo/Logo.png" alt="Logo" width="160" height="46">

        </a>

        <div class="header-search-container">
          <form action="pages/product.php" method="GET">
            <input type="search" name="search" class="search-field" placeholder="Nhập tên sản phẩm...">
            <button class="search-btn"><ion-icon name="search-outline"></ion-icon></button>
          </form>
        </div>

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
            <a href="pages/login.php" class="action-btn">
              <ion-icon name="person-outline"></ion-icon>
            </a>
          <?php endif; ?>

          <a href="pages/like.php" class="action-btn">
            <ion-icon name="heart-outline"></ion-icon>
            <span class="count">0</span>
          </a>

          <a href="Pages/Payment.php" class="action-btn">
            <ion-icon name="cart-outline" style="color: var(--eerie-black);"></ion-icon>
            <span class="count">0</span>
          </a>
        </div>

      </div>

    </div>

    <!-- HEADER 3 -->

    <nav class="desktop-navigation-menu">

      <div class="container">

        <ul class="desktop-menu-category-list">

          <li class="menu-category">

            <a href="#" class="menu-title">Trang Chủ</a>

          </li>

          <li class="menu-category">

            <a href="#" class="menu-title">Danh Mục</a>

            <div class="dropdown-panel">

              <ul class="dropdown-panel-list">

                <li class="menu-title">

                  <a href="pages/product.php?category=Scale Figure">Figure</a>

                </li>

                <li class="panel-list-item">

                  <a href="pages/product.php?sub_category=Scale 1/4">Scale 1/4</a>

                </li>

                <li class="panel-list-item">

                  <a href="pages/product.php?sub_category=Scale 1/7">Scale 1/7</a>

                </li>

                <li class="panel-list-item">

                  <a href="pages/product.php?sub_category=scale 1/6">Scale 1/6</a>

                </li>

                <li class="panel-list-item">

                  <a href="pages/product.php?sub_category=FIGMA">FIGMA</a>

                </li>

                <li class="panel-list-item">

                  <a href="pages/product.php?category=other">Khác</a>

                </li>

                <li class="panel-list-item">

                  <a href="pages/Product.php">

                    <img src="Asset/Images/Banner/banner-1.webp" alt="Figure" width="250" height="119">

                  </a>

                </li>

              </ul>

              <ul class="dropdown-panel-list">

                <li class="menu-title">

                  <a href="pages/product.php?category=POP UP PARADE">POP UP PARADE</a>

                </li>

                <li class="panel-list-item">

                  <a href="pages/product.php?sub_category=L size">POP UP PARADE L size</a>

                </li>

                <li class="panel-list-item">

                  <a href="pages/product.php?sub_category=XL size">POP UP PARADE XL size</a>

                </li>

                <li class="panel-list-item">

                  <a href="pages/product.php?sub_category=SP">POP UP PARADE SP</a>

                </li>

                <li class="panel-list-item">

                  <a href="pages/product.php?sub_category=Swacchao!">POP UP PARADE Swacchao!</a>

                </li>

                <li class="panel-list-item">

                  <a href="pages/product.php?sub_category=BEACH QUEENS">POP UP PARADE BEACH QUEENS</a>

                </li>

                <li class="panel-list-item">

                  <a href="pages/Product.php">

                    <img src="Asset/Images/Banner/banner-3.webp" alt="Nendoroid" width="250" height="119">

                  </a>

                </li>

              </ul>

              <ul class="dropdown-panel-list">

                <li class="menu-title">

                  <a href="pages/product.php?category=Nenderoid">Nendoroid</a>

                </li>

                <li class="panel-list-item">

                  <a href="pages/product.php?sub_category=Surprise">Nendoroid Surprise</a>

                </li>

                <li class="panel-list-item">

                  <a href="pages/product.php?sub_category=Dolly">Nendoroid Dolly</a>

                </li>

                <li class="panel-list-item">

                  <a href="pages/product.php?sub_category=More">Nendoroid More</a>

                </li>

                <li class="panel-list-item">

                  <a href="pages/product.php?sub_category=EZ">Nendoroid EZ</a>

                </li>

                <li class="panel-list-item">

                  <a href="pages/product.php?sub_category=Jumbo">Nendoroid Jumbo</a>

                </li>

                <li class="panel-list-item">

                  <a href="pages/Product.php">

                    <img src="Asset/Images/Banner/banner-2.webp" alt="Nendoroid" width="250" height="119">

                  </a>

                </li>

              </ul>

              <ul class="dropdown-panel-list">

                <li class="menu-title">

                  <a href="pages/product.php?category=Mecha Smile">Mecha Smile</a>

                </li>

                <li class="panel-list-item">

                  <a href="pages/product.php?sub_category=THE GATTAI">THE GATTAI</a>

                </li>

                <li class="panel-list-item">

                  <a href="pages/product.php?sub_category=HAGANE WORKS">HAGANE WORKS</a>

                </li>

                <li class="panel-list-item">

                  <a href="pages/product.php?sub_category=MODEROID">MODEROID</a>

                </li>

                <li class="panel-list-item">

                  <a href="pages/product.php?sub_category=PartPart">Thay thế</a>

                </li>

                <li class="panel-list-item">

                  <a href="pages/product.php?sub_category=Other Mecha">Other (Mecha Smile)</a>

                </li>

                <li class="panel-list-item">

                  <a href="pages/Product.php">

                    <img src="Asset/Images/Banner/banner-4.webp" alt="Mecha Smile" width="250" height="119">

                  </a>

                </li>

              </ul>

            </div>

          </li>

          <li class="menu-category">

            <a href="pages/product.php?gender=Male" class="menu-title">Figure nam</a>

            <ul class="dropdown-list">

              <li class="dropdown-item">

                <a href="pages/Product.php">Hành động</a>

              </li>

              <li class="dropdown-item">

                <a href="pages/Product.php">Isekai</a>

              </li>

              <li class="dropdown-item">

                <a href="pages/Product.php">Đời thường</a>

              </li>

              <li class="dropdown-item">

                <a href="pages/Product.php">Khác</a>

              </li>

            </ul>

          </li>

          <li class="menu-category">

            <a href="pages/product.php?gender=Female" class="menu-title">Figure nữ</a>

            <ul class="dropdown-list">

              <li class="dropdown-item">

                <a href="pages/Product.php">Hành động</a>

              </li>

              <li class="dropdown-item">

                <a href="pages/Product.php">Isekai</a>

              </li>

              <li class="dropdown-item">

                <a href="pages/Product.php">Đời thường</a>

              </li>

              <li class="dropdown-item">

                <a href="pages/product.php?sub_category=Other Female">Khác</a>

              </li>

            </ul>

          </li>

          <li class="menu-category">

            <a href="pages/product.php?category=" class="menu-title">Sản phẩm</a>

            <ul class="dropdown-list">

              <li class="dropdown-item">

                <a href="pages/Product.php">Bán chạy</a>

              </li>

              <li class="dropdown-item">

                <a href="pages/Product.php">Mới về</a>

              </li>

              <li class="dropdown-item">

                <a href="pages/Product.php">Đánh giá cao</a>

              </li>

              <li class="dropdown-item">

                <a href="pages/Product.php">Ưu đãi</a>

              </li>

            </ul>

          </li>

          <li class="menu-category">

            <a href="pages/Product.php" class="menu-title">Hãng</a>

            <ul class="dropdown-list">

              <li class="dropdown-item">

                <a href="pages/Product.php">Good Smile Company</a>

              </li>

              <li class="dropdown-item">

                <a href="pages/Product.php">Max Factory</a>

              </li>

              <li class="dropdown-item">

                <a href="pages/Product.php">Bandai Namco</a>

              </li>

              <li class="dropdown-item">

                <a href="pages/Product.php">MegaHouse</a>

              </li>

            </ul>

          </li>

          <li class="menu-category">

            <a href="#" class="menu-title">Blog</a>

          </li>

          <li class="menu-category">

            <a href="pages/voucher.php" class="menu-title">Ưu Đãi Hấp Dẫn</a>


          </li>

          <li class="menu-category">

            <a href="pages/about.php" class="menu-title">Giới Thiệu</a>


          </li>

        </ul>

      </div>

    </nav>

    <!-- HEADER 4 -->

    <div class="mobile-bottom-navigation">

      <button class="action-btn" data-mobile-menu-open-btn>

        <ion-icon name="menu-outline"></ion-icon>

      </button>

      <button class="action-btn">

        <a href="Pages/Payment.php"><ion-icon name="cart-outline" style="color: var(--eerie-black);"></ion-icon></a>

        <span class=" count">0</span>

      </button>

      <button class="action-btn">

        <ion-icon name="home-outline"></ion-icon>

      </button>

      <button class="action-btn">

        <a href="pages/like.php" class="action-btn">
          <ion-icon name="heart-outline"></ion-icon>
          <span class="count">0</span>
        </a>

      </button>

      <button class="action-btn" data-mobile-menu-open-btn>

        <ion-icon name="grid-outline"></ion-icon>

      </button>

    </div>

    <!-- HEADER 5 -->

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

  <!--
      - PHẦN CHÍNH
    -->

  <main>
    <!--
        - BANNER
      -->

    <div class="banner">
      <div class="container">
        <div class="slider-container has-scrollbar">
          <div class="slider-item">
            <img src="Asset/Images/Banner/banner-8.webp" alt="giảm giá thời trang nữ mới nhất" class="banner-img">
            <div class="banner-content">
              <p class="banner-subtitle">Nendoroid</p>
              <h2 class="banner-title">Nenderoid giảm tới 40%</h2>
              <p class="banner-text">
                Chỉ từ <b>400</b>.000 vnd
              </p>
              <a href="pages/product.php?category=Nenderoid" class="banner-btn">Mua Ngay</a>
            </div>
          </div>
          <div class="slider-item">
            <img src="Asset/Images/Banner/banner-6.png" alt="kính mát hiện đại" class="banner-img">
            <div class="banner-content">
              <p class="banner-subtitle">Figure</p>
              <h2 class="banner-title">Figure mới về</h2>
              <p class="banner-text">
                Đẹp mới <b>100</b>% seal
              </p>
              <a href="#" class="banner-btn">Mua Ngay</a>
            </div>
          </div>
          <div class="slider-item">
            <img src="Asset/Images/Banner/banner-7.png" alt="giảm giá thời trang mùa hè mới" class="banner-img">
            <div class="banner-content">
              <p class="banner-subtitle">Collab</p>
              <h2 class="banner-title">Nijisanji collab figure</h2>
              <p class="banner-text">
                Giới hạn <b>290</b> sản phẩm
              </p>
              <a href="Pages/Product.php" class="banner-btn">Mua Ngay</a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!--
        - DANH MỤC
      -->

    <div class="category">
      <div class="container">
        <div class="category-item-container has-scrollbar">
          <?php
          // Lấy danh sách danh mục và số lượng từ cơ sở dữ liệu
          $stmt_categories = $pdo->query("SELECT category, COUNT(*) as count FROM products GROUP BY category");
          $categories = $stmt_categories->fetchAll(PDO::FETCH_ASSOC);

          // Danh sách danh mục cần hiển thị (theo thứ tự trong mã gốc)
          $display_categories = [
            '' => 'Asset/Images/category/category-1webp.webp', // Figure
            'Nenderoid' => 'Asset/Images/category/category-2.webp',
            'POP UP PARADE' => 'Asset/Images/category/category-3.webp',
            'Mecha Smile' => 'Asset/Images/category/category-4.webp',
            'Gấu bông' => 'Asset/Images/category/category-5.webp',
            'LOOK UP' => 'Asset/Images/category/category-6.webp',
            'Figma' => 'Asset/Images/category/category-7.webp',
            'Scale Figure' => 'Asset/Images/category/category-8.webp'
          ];

          foreach ($display_categories as $cat_name => $img_path):
            $display_name = ($cat_name === '') ? 'Figure' : $cat_name; // Đặt tên "Figure" nếu rỗng

            // Tìm số lượng sản phẩm từ cơ sở dữ liệu
            if ($cat_name === '') {
              // Nếu danh mục là Figure (rỗng), lấy tổng số sản phẩm
              $stmt_total = $pdo->query("SELECT COUNT(*) as count FROM products");
              $count = $stmt_total->fetch(PDO::FETCH_ASSOC)['count'];
            } else {
              $count = 0;
              foreach ($categories as $cat) {
                if ($cat['category'] === $cat_name) {
                  $count = $cat['count'];
                  break;
                }
              }
            }
          ?>

            <div class="category-item">
              <div class="category-img-box">
                <img src="<?php echo htmlspecialchars($img_path); ?>" alt="<?php echo htmlspecialchars($display_name); ?>" width="30">
              </div>
              <div class="category-content-box">
                <div class="category-content-flex">
                  <h3 class="category-item-title"><?php echo htmlspecialchars($display_name); ?></h3>
                  <p class="category-item-amount">(<?php echo $count; ?>)</p>
                </div>
                <a href="Pages/Product.php<?php echo ($cat_name === '') ? '' : '?category=' . urlencode($cat_name); ?>" class="category-btn">Xem tất cả</a>
              </div>
            </div>

          <?php endforeach; ?>
        </div>
      </div>
    </div>


    <!--
        - SẢN PHẨM
      -->

    <div class="product-container">
      <div class="container">

        <!--
              - THANH BÊN
            -->

        <div class="product-container">
          <div class="container">
            <!-- THANH BÊN -->
            <div class="sidebar has-scrollbar" data-mobile-menu>
              <div class="sidebar-category">
                <div class="sidebar-top">
                  <h2 class="sidebar-title">Danh Mục</h2>
                  <button class="sidebar-close-btn" data-mobile-menu-close-btn><ion-icon name="close-outline"></ion-icon></button>
                </div>
                <ul class="sidebar-menu-category-list">
                  <li class="sidebar-menu-category">
                    <button class="sidebar-accordion-menu" data-accordion-btn>
                      <div class="menu-title-flex">
                        <img src="Asset/Images/category/sidebar-1.jpg" alt="Figure" width="20" height="20" class="menu-title-img">
                        <p class="menu-title">Figure</p>
                      </div>
                      <div>
                        <ion-icon name="add-outline" class="add-icon"></ion-icon>
                        <ion-icon name="remove-outline" class="remove-icon"></ion-icon>
                      </div>
                    </button>
                    <ul class="sidebar-submenu-category-list" data-accordion>
                      <?php
                      $stmt_categories = $pdo->query("SELECT DISTINCT category FROM products");
                      $categories = $stmt_categories->fetchAll(PDO::FETCH_COLUMN);
                      foreach ($categories as $cat):
                      ?>
                        <li class="sidebar-submenu-category">
                          <a href="pages/Product.php?category=<?php echo urlencode($cat); ?>" class="sidebar-submenu-title">
                            <p class="product-name"><?php echo htmlspecialchars($cat); ?></p>
                            <?php
                            $stmt_stock = $pdo->prepare("SELECT SUM(pd.stock) FROM products p JOIN product_details pd ON p.id = pd.product_id WHERE p.category = :category");
                            $stmt_stock->execute(['category' => $cat]);
                            $stock = $stmt_stock->fetchColumn() ?: 0;
                            ?>
                            <data value="<?php echo $stock; ?>" class="stock" title="Số lượng có sẵn"><?php echo $stock; ?></data>
                          </a>
                        </li>
                      <?php endforeach; ?>
                    </ul>
                  </li>
                  <li class="sidebar-menu-category">
                    <button class="sidebar-accordion-menu" data-accordion-btn>
                      <div class="menu-title-flex">
                        <img src="Asset/Images/category/sidebar-2.webp" alt="PVC Figure" class="menu-title-img" width="20" height="20">
                        <p class="menu-title">PVC Figure</p>
                      </div>
                      <div>
                        <ion-icon name="add-outline" class="add-icon"></ion-icon>
                        <ion-icon name="remove-outline" class="remove-icon"></ion-icon>
                      </div>
                    </button>
                    <ul class="sidebar-submenu-category-list" data-accordion>
                      <?php
                      $stmt_subcategories = $pdo->prepare("SELECT DISTINCT sub_category FROM products WHERE category = 'Scale Figure'");
                      $stmt_subcategories->execute();
                      $subcategories = $stmt_subcategories->fetchAll(PDO::FETCH_COLUMN);
                      foreach ($subcategories as $subcat):
                      ?>
                        <li class="sidebar-submenu-category">
                          <a href="pages/Product.php?category=Scale+Figure&subcategory=<?php echo urlencode($subcat); ?>" class="sidebar-submenu-title">
                            <p class="product-name"><?php echo htmlspecialchars($subcat); ?></p>
                            <?php
                            $stmt_stock = $pdo->prepare("SELECT SUM(pd.stock) FROM products p JOIN product_details pd ON p.id = pd.product_id WHERE p.category = 'Scale Figure' AND p.sub_category = :subcategory");
                            $stmt_stock->execute(['subcategory' => $subcat]);
                            $stock = $stmt_stock->fetchColumn() ?: 0;
                            ?>
                            <data value="<?php echo $stock; ?>" class="stock" title="Số lượng có sẵn"><?php echo $stock; ?></data>
                          </a>
                        </li>
                      <?php endforeach; ?>
                    </ul>
                  </li>
                  <li class="sidebar-menu-category">
                    <button class="sidebar-accordion-menu" data-accordion-btn>
                      <div class="menu-title-flex">
                        <img src="Asset/Images/category/sidebar-3.webp" alt="Nenderoid" class="menu-title-img" width="20" height="20">
                        <p class="menu-title">Nendoroid Series</p>
                      </div>
                      <div>
                        <ion-icon name="add-outline" class="add-icon"></ion-icon>
                        <ion-icon name="remove-outline" class="remove-icon"></ion-icon>
                      </div>
                    </button>
                    <ul class="sidebar-submenu-category-list" data-accordion>
                      <?php
                      $stmt_subcategories = $pdo->prepare("SELECT DISTINCT sub_category FROM products WHERE category = 'Nenderoid'");
                      $stmt_subcategories->execute();
                      $subcategories = $stmt_subcategories->fetchAll(PDO::FETCH_COLUMN);
                      foreach ($subcategories as $subcat):
                      ?>
                        <li class="sidebar-submenu-category">
                          <a href="pages/Product.php?category=Nenderoid&subcategory=<?php echo urlencode($subcat); ?>" class="sidebar-submenu-title">
                            <p class="product-name"><?php echo htmlspecialchars($subcat); ?></p>
                            <?php
                            $stmt_stock = $pdo->prepare("SELECT SUM(pd.stock) FROM products p JOIN product_details pd ON p.id = pd.product_id WHERE p.category = 'Nenderoid' AND p.sub_category = :subcategory");
                            $stmt_stock->execute(['subcategory' => $subcat]);
                            $stock = $stmt_stock->fetchColumn() ?: 0;
                            ?>
                            <data value="<?php echo $stock; ?>" class="stock" title="Số lượng có sẵn"><?php echo $stock; ?></data>
                          </a>
                        </li>
                      <?php endforeach; ?>
                    </ul>
                  </li>
                  <li class="sidebar-menu-category">
                    <button class="sidebar-accordion-menu" data-accordion-btn>
                      <div class="menu-title-flex">
                        <img src="Asset/Images/category/sidebar-4.webp" alt="POP UP PARADE" class="menu-title-img" width="20" height="20">
                        <p class="menu-title">POP UP PARADE</p>
                      </div>
                      <div>
                        <ion-icon name="add-outline" class="add-icon"></ion-icon>
                        <ion-icon name="remove-outline" class="remove-icon"></ion-icon>
                      </div>
                    </button>
                    <ul class="sidebar-submenu-category-list" data-accordion>
                      <?php
                      $stmt_subcategories = $pdo->prepare("SELECT DISTINCT sub_category FROM products WHERE category = 'POP UP PARADE'");
                      $stmt_subcategories->execute();
                      $subcategories = $stmt_subcategories->fetchAll(PDO::FETCH_COLUMN);
                      foreach ($subcategories as $subcat):
                      ?>
                        <li class="sidebar-submenu-category">
                          <a href="pages/Product.php?category=POP+UP+PARADE&subcategory=<?php echo urlencode($subcat); ?>" class="sidebar-submenu-title">
                            <p class="product-name"><?php echo htmlspecialchars($subcat); ?></p>
                            <?php
                            $stmt_stock = $pdo->prepare("SELECT SUM(pd.stock) FROM products p JOIN product_details pd ON p.id = pd.product_id WHERE p.category = 'POP UP PARADE' AND p.sub_category = :subcategory");
                            $stmt_stock->execute(['subcategory' => $subcat]);
                            $stock = $stmt_stock->fetchColumn() ?: 0;
                            ?>
                            <data value="<?php echo $stock; ?>" class="stock" title="Số lượng có sẵn"><?php echo $stock; ?></data>
                          </a>
                        </li>
                      <?php endforeach; ?>
                    </ul>
                  </li>
                  <li class="sidebar-menu-category">
                    <button class="sidebar-accordion-menu" data-accordion-btn>
                      <div class="menu-title-flex">
                        <img src="Asset/Images/category/sidebar-5.webp" alt="Mecha Smile" class="menu-title-img" width="20" height="20">
                        <p class="menu-title">Mecha Smile</p>
                      </div>
                      <div>
                        <ion-icon name="add-outline" class="add-icon"></ion-icon>
                        <ion-icon name="remove-outline" class="remove-icon"></ion-icon>
                      </div>
                    </button>
                    <ul class="sidebar-submenu-category-list" data-accordion>
                      <?php
                      $stmt_subcategories = $pdo->prepare("SELECT DISTINCT sub_category FROM products WHERE category = 'Mecha Smile'");
                      $stmt_subcategories->execute();
                      $subcategories = $stmt_subcategories->fetchAll(PDO::FETCH_COLUMN);
                      foreach ($subcategories as $subcat):
                      ?>
                        <li class="sidebar-submenu-category">
                          <a href="pages/Product.php?category=Mecha+Smile&subcategory=<?php echo urlencode($subcat); ?>" class="sidebar-submenu-title">
                            <p class="product-name"><?php echo htmlspecialchars($subcat); ?></p>
                            <?php
                            $stmt_stock = $pdo->prepare("SELECT SUM(pd.stock) FROM products p JOIN product_details pd ON p.id = pd.product_id WHERE p.category = 'Mecha Smile' AND p.sub_category = :subcategory");
                            $stmt_stock->execute(['subcategory' => $subcat]);
                            $stock = $stmt_stock->fetchColumn() ?: 0;
                            ?>
                            <data value="<?php echo $stock; ?>" class="stock" title="Số lượng có sẵn"><?php echo $stock; ?></data>
                          </a>
                        </li>
                      <?php endforeach; ?>
                    </ul>
                  </li>
                  <li class="sidebar-menu-category">
                    <button class="sidebar-accordion-menu" data-accordion-btn>
                      <div class="menu-title-flex">
                        <img src="Asset/Images/category/sidebar-7.webp" alt="Goods" class="menu-title-img" width="20" height="20">
                        <p class="menu-title">Goods</p>
                      </div>
                      <div>
                        <ion-icon name="add-outline" class="add-icon"></ion-icon>
                        <ion-icon name="remove-outline" class="remove-icon"></ion-icon>
                      </div>
                    </button>
                    <ul class="sidebar-submenu-category-list" data-accordion>
                      <?php
                      $stmt_subcategories = $pdo->prepare("SELECT DISTINCT sub_category FROM products WHERE category = 'Goods'");
                      $stmt_subcategories->execute();
                      $subcategories = $stmt_subcategories->fetchAll(PDO::FETCH_COLUMN);
                      foreach ($subcategories as $subcat):
                      ?>
                        <li class="sidebar-submenu-category">
                          <a href="pages/Product.php?category=Goods&subcategory=<?php echo urlencode($subcat); ?>" class="sidebar-submenu-title">
                            <p class="product-name"><?php echo htmlspecialchars($subcat); ?></p>
                            <?php
                            $stmt_stock = $pdo->prepare("SELECT SUM(pd.stock) FROM products p JOIN product_details pd ON p.id = pd.product_id WHERE p.category = 'Goods' AND p.sub_category = :subcategory");
                            $stmt_stock->execute(['subcategory' => $subcat]);
                            $stock = $stmt_stock->fetchColumn() ?: 0;
                            ?>
                            <data value="<?php echo $stock; ?>" class="stock" title="Số lượng có sẵn"><?php echo $stock; ?></data>
                          </a>
                        </li>
                      <?php endforeach; ?>
                    </ul>
                  </li>
                  <li class="sidebar-menu-category">
                    <button class="sidebar-accordion-menu" data-accordion-btn>
                      <div class="menu-title-flex">
                        <img src="Asset/Images/category/category-7.webp" alt="figma Series" class="menu-title-img" width="20" height="20">
                        <p class="menu-title">figma Series</p>
                      </div>
                      <div>
                        <ion-icon name="add-outline" class="add-icon"></ion-icon>
                        <ion-icon name="remove-outline" class="remove-icon"></ion-icon>
                      </div>
                    </button>
                    <ul class="sidebar-submenu-category-list" data-accordion>
                      <?php
                      $stmt_subcategories = $pdo->prepare("SELECT DISTINCT sub_category FROM products WHERE category = 'Scale Figure' AND sub_category LIKE '%Figma%'");
                      $stmt_subcategories->execute();
                      $subcategories = $stmt_subcategories->fetchAll(PDO::FETCH_COLUMN);
                      foreach ($subcategories as $subcat):
                      ?>
                        <li class="sidebar-submenu-category">
                          <a href="pages/Product.php?category=Scale+Figure&subcategory=<?php echo urlencode($subcat); ?>" class="sidebar-submenu-title">
                            <p class="product-name"><?php echo htmlspecialchars($subcat); ?></p>
                            <?php
                            $stmt_stock = $pdo->prepare("SELECT SUM(pd.stock) FROM products p JOIN product_details pd ON p.id = pd.product_id WHERE p.category = 'Scale Figure' AND p.sub_category = :subcategory");
                            $stmt_stock->execute(['subcategory' => $subcat]);
                            $stock = $stmt_stock->fetchColumn() ?: 0;
                            ?>
                            <data value="<?php echo $stock; ?>" class="stock" title="Số lượng có sẵn"><?php echo $stock; ?></data>
                          </a>
                        </li>
                      <?php endforeach; ?>
                    </ul>
                  </li>
                </ul>
              </div>

              <!-- Bán chạy nhất -->
              <div class="product-showcase">
                <h3 class="showcase-heading">Bán chạy nhất</h3>
                <div class="showcase-wrapper">
                  <div class="showcase-container">
                    <?php foreach ($bestsellers as $bestseller): ?>
                      <div class="showcase">
                        <a href="product-detail.php?id=<?php echo $bestseller['id']; ?>" class="showcase-img-box">
                          <img src="<?php echo htmlspecialchars($bestseller['image']); ?>"
                            alt="<?php echo htmlspecialchars($bestseller['name']); ?>"
                            width="75" height="75"
                            class="showcase-img">
                        </a>
                        <div class="showcase-content">
                          <a href="pages/product-detail.php?id=<?php echo $bestseller['id']; ?>">
                            <h4 class="showcase-title"><?php echo htmlspecialchars($bestseller['name']); ?></h4>
                          </a>
                          <div class="price-box">
                            <p class="price"><?php echo number_format($bestseller['price'], 0, ',', '.') . 'đ'; ?></p>
                            <?php if ($bestseller['old_price'] > 0): ?>
                              <del><?php echo number_format($bestseller['old_price'], 0, ',', '.') . 'đ'; ?></del>
                            <?php endif; ?>
                          </div>
                        </div>
                      </div>
                    <?php endforeach; ?>
                  </div>
                </div>
              </div>
            </div>

            <div class="product-box">

              <!--
                - SẢN PHẨM NHỎ
              -->

              <div class="product-minimal">

                <?php
                displayProducts($pdo, "is_new_arrival = 1", "HÀNG MỚI VỀ");
                ?>

                <?php
                displayProducts($pdo, "is_best_seller = 1", "BÁN CHẠY");
                ?>

                <?php
                displayProducts($pdo, "is_top_rated = 1", "ĐÁNH GIÁ CAO");
                ?>

              </div>



              <!--
                - SẢN PHẨM LỚN
              -->

              <div class="product-featured">
                <h2 class="title">ƯU ĐÃI TRONG NGÀY</h2>
                <div class="showcase-wrapper has-scrollbar">
                  <?php
                  $stmt = $pdo->prepare("
                                SELECT p.`id`, p.`name`, p.`category`, p.`price`, p.`old_price`, p.`image`, 
                                       pd.`deal_end_time`, pd.`description`, pd.`sold`, pd.`remaining`
                                FROM `products` p
                                LEFT JOIN `product_details` pd ON p.`id` = pd.`product_id`
                                WHERE pd.`is_daily_deal` = 1
                            ");
                  $stmt->execute();
                  $dailyDeals = $stmt->fetchAll(PDO::FETCH_ASSOC);

                  foreach ($dailyDeals as $product):
                  ?>
                    <div class="showcase-container">
                      <div class="showcase">
                        <div class="showcase-banner">
                          <a href="pages/product-detail.php?id=<?php echo $product['id']; ?>">
                            <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="showcase-img">
                          </a>
                        </div>
                        <div class="showcase-content">
                          <div class="showcase-rating">
                            <ion-icon name="star"></ion-icon>
                            <ion-icon name="star"></ion-icon>
                            <ion-icon name="star"></ion-icon>
                            <ion-icon name="star"></ion-icon>
                            <ion-icon name="star-outline"></ion-icon>
                          </div>
                          <a href="#">
                            <h3 class="showcase-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                          </a>
                          <p class="showcase-desc">
                            <?php echo htmlspecialchars($product['description']); ?>
                          </p>
                          <div class="price-box">
                            <p class="price"><?php echo number_format((float)$product['price'] / 1000, 0) . 'k'; ?></p>
                            <?php if ($product['old_price']): ?>
                              <del><?php echo number_format((float)$product['old_price'] / 1000, 0) . 'k'; ?></del>
                            <?php endif; ?>
                          </div>
                          <button class="add-cart-btn" data-title="<?php echo htmlspecialchars($product['name']); ?>">Thêm vào giỏ</button>
                          <div class="showcase-status">
                            <div class="wrapper">
                              <p>Đã bán: <b><?php echo htmlspecialchars($product['sold']); ?></b></p>
                              <p>Còn: <b><?php echo htmlspecialchars($product['remaining']); ?></b></p>
                            </div>
                            <div class="showcase-status-bar"></div>
                          </div>
                          <?php if ($product['deal_end_time']): ?>
                            <div class="countdown-box">
                              <p class="countdown-desc">Sẽ kết thúc trong:</p>
                              <div class="countdown" data-end-time="<?php echo htmlspecialchars($product['deal_end_time']); ?>">
                                <div class="countdown-content">
                                  <p class="display-number days">0</p>
                                  <p class="display-text">Ngày</p>
                                </div>
                                <div class="countdown-content">
                                  <p class="display-number hours">0</p>
                                  <p class="display-text">Giờ</p>
                                </div>
                                <div class="countdown-content">
                                  <p class="display-number minutes">0</p>
                                  <p class="display-text">Phút</p>
                                </div>
                                <div class="countdown-content">
                                  <p class="display-number seconds">0</p>
                                  <p class="display-text">Giây</p>
                                </div>
                              </div>
                            </div>
                          <?php endif; ?>
                        </div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>


              <!--
                - PRODUCT GRID
              -->
              <?php displayNewProductsGrid($pdo); ?>

            </div>
          </div>

          <div>

            <div class="container">

              <div class="testimonials-box">

                <!--
                - TESTIMONIALS
              -->

                <div class="testimonial">

                  <h2 class="title">Chính sách</h2>

                  <div class="testimonial-card">

                    <img src="Asset/Images/Logo/images.jpg" alt="Hung Hay HoHo" class="testimonial-banner" width="80" height="80">

                    <p class="testimonial-name">Hưng Hay Ho</p>

                    <p class="testimonial-title">LEADER & WEB DESIGNER</p>


                    <p class="testimonial-desc">
                      Good Smile Figure có chính sách hỗ trợ bảo hành 1:1 đổi mới các sản phẩm lỗi nhà sản xuất. Chi tiết khách hàng vui lòng tham khảo trong mục chính sách đổi trả & bảo hành.
                    </p>

                  </div>

                </div>



                <!--
                - CTA
              -->

                <div class="cta-container">

                  <img src="Asset/Images/Banner/banner-9.jpg" alt="summer collection" class="cta-banner">

                  <a href="pages/Product.php" class="cta-content">

                    <p class="discount">Giảm 25%</p>

                    <h2 class="cta-title">Figure phát hành mùa hè</h2>

                    <p class="cta-text">Chỉ từ 200k</p>

                    <button class="cta-btn">Mua ngay</button>

                  </a>

                </div>



                <!--
                - SERVICE
              -->

                <div class="service">

                  <h2 class="title">Dịch vụ</h2>

                  <div class="service-container">

                    <a href="#" class="service-item">

                      <div class="service-icon">
                        <ion-icon name="boat-outline"></ion-icon>
                      </div>

                      <div class="service-content">

                        <h3 class="service-title">Giao hàng toàn nước</h3>
                        <p class="service-desc">Cho đơn hàng trên 500k</p>

                      </div>

                    </a>

                    <a href="#" class="service-item">

                      <div class="service-icon">
                        <ion-icon name="rocket-outline"></ion-icon>
                      </div>

                      <div class="service-content">

                        <h3 class="service-title">Giao hàng siêu tốc</h3>
                        <p class="service-desc">Nội thành Tp.HCM</p>

                      </div>

                    </a>

                    <a href="#" class="service-item">

                      <div class="service-icon">
                        <ion-icon name="call-outline"></ion-icon>
                      </div>

                      <div class="service-content">

                        <h3 class="service-title">Hỗ trợ online</h3>
                        <p class="service-desc">8h sáng - 10h tối</p>

                      </div>

                    </a>

                    <a href="#" class="service-item">

                      <div class="service-icon">
                        <ion-icon name="arrow-undo-outline"></ion-icon>
                      </div>

                      <div class="service-content">

                        <h3 class="service-title">Chính sách trả hàng</h3>
                        <p class="service-desc">Nhanh và dễ dàng</p>

                      </div>

                    </a>

                    <a href="#" class="service-item">

                      <div class="service-icon">
                        <ion-icon name="ticket-outline"></ion-icon>
                      </div>

                      <div class="service-content">

                        <h3 class="service-title">Hoàn tiền</h3>
                        <p class="service-desc">Cho đơn trên 2 triệu</p>

                      </div>

                    </a>

                  </div>

                </div>

              </div>

            </div>

          </div>

          <!--
        - BLOG
      -->

          <div class="blog">

            <div class="container">

              <div class="blog-container has-scrollbar">

                <div class="blog-card">

                  <a href="#">
                    <img src="Asset/Images/blog/blog-1.webp" alt="F:NEX giới thiệu mô hình Tokisaki Kurumi bán thân 1:1" width="300" class="blog-banner">
                  </a>

                  <div class="blog-content">

                    <a href="#" class="blog-category">Figure</a>

                    <a href="pages/new.php">
                      <h3 class="blog-title">F:NEX giới thiệu mô hình Tokisaki Kurumi 1:1</h3>
                    </a>

                    <p class="blog-meta">
                      Bởi <cite>Hưng Hay Ho</cite> / <time datetime="2022-04-06">3/4/2025</time>
                    </p>

                  </div>

                </div>

                <div class="blog-card">

                  <a href="#">
                    <img src="Asset/Images/blog/blog-2.webp" alt="Demon Slayer: Infinity Castle Movie công bố ngày phát hành vào tháng 7 với hình ảnh mới"
                      class="blog-banner" width="300">
                  </a>

                  <div class="blog-content">

                    <a href="#" class="blog-category">Anime</a>

                    <h3>
                      <a href="#" class="blog-title">Demon Slayer: Infinity Castle Movie công bố ngày phát hành vào tháng 7 với hình ảnh mới</a>
                    </h3>

                    <p class="blog-meta">
                      Bởi <cite>Dôn Lì</cite> / <time datetime="2022-01-18">1/3/2025</time>
                    </p>

                  </div>

                </div>

                <div class="blog-card">

                  <a href="#">
                    <img src="Asset/Images/blog/blog-4.jpg" alt="VS The Little Giant: Haikyu Final Movie 2 tiết lộ tiêu đề, hình ảnh teaser và đoạn giới thiệu"
                      class="blog-banner" width="300">
                  </a>

                  <div class="blog-content">

                    <a href="#" class="blog-category">Anime</a>

                    <h3>
                      <a href="#" class="blog-title">VS The Little Giant: Haikyu Final Movie 2 tiết lộ tiêu đề, hình ảnh teaser và đoạn giới thiệu</a>
                    </h3>

                    <p class="blog-meta">
                      Bởi <cite>Phốt Làng</cite> / <time datetime="2022-02-10">2/3/2025</time>
                    </p>

                  </div>

                </div>

                <div class="blog-card">

                  <a href="#">
                    <img src="Asset/Images/blog/blog-3.webp" alt="2024 Anime of the Year Awards – Winners"
                      class="blog-banner" width="300">
                  </a>

                  <div class="blog-content">

                    <a href="#" class="blog-category">Anime</a>

                    <h3>
                      <a href="#" class="blog-title">2024 Anime of the Year Awards – Winners</a>
                    </h3>

                    <p class="blog-meta">
                      Bởi <cite>Vôn Lình</cite> / <time datetime="2022-03-15">15/3/2025</time>
                    </p>

                  </div>

                </div>

              </div>

            </div>

          </div>

  </main>

  <!--
      - FOOTER
    -->

  <footer>

    <div class="footer-category">

      <div class="container">

        <h2 class="footer-category-title">Danh mục sản phẩm</h2>

        <div class="footer-category-box">

          <h3 class="category-box-title">Figure :</h3>

          <a href="pages/Product.php" class="footer-category-link">Figure</a>
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
            <a href="pages/Product.php" class="footer-nav-link">Figure</a>
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

            <a href="tel:+607936-8058" class="footer-nav-link">037-916-3407 </a>
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






  <script src="Asset/Js/script.js"></script>
  <script src="Asset/Js/favorites.js"></script>

  <!-- Google Translate -->
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

  <!-- Icon -->

  <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

</body>

</html>