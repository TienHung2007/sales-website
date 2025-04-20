<?php
session_start();
if (isset($_GET['logout'])) {
  session_destroy();
  header("Location: login.php");
  exit;
}

// Kết nối cơ sở dữ liệu
$host = '127.0.0.1';
$dbname = 'good_smile_db';
$username = 'root';
$password = '';

require_once 'db_connect.php';
require_once 'function.php'; // Bao gồm file functions.php

try {
  $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  die("Kết nối thất bại: " . $e->getMessage());
}

// Lấy tham số từ URL
$category = isset($_GET['category']) ? trim($_GET['category']) : '';
$sub_category = isset($_GET['sub_category']) ? trim($_GET['sub_category']) : '';
$gender = isset($_GET['gender']) ? trim($_GET['gender']) : '';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Xây dựng câu SQL động
$query = "SELECT p.id, p.name, p.category, p.price, p.old_price, p.image, 
                 pd.is_new_product, pd.stock, pd.sold, pd.image_hover 
          FROM products p 
          LEFT JOIN product_details pd ON p.id = pd.product_id 
          WHERE 1=1";
$params = [];

if ($category) {
  $query .= " AND p.category = :category";
  $params[':category'] = $category;
}
if ($sub_category) {
  $query .= " AND p.sub_category = :sub_category";
  $params[':sub_category'] = $sub_category;
}
if ($gender) {
  $query .= " AND p.gender = :gender";
  $params[':gender'] = $gender;
}
if ($search) {
  // Tìm kiếm theo tên hoặc ID
  if (is_numeric($search)) {
    $query .= " AND (p.id = :id OR p.name LIKE :search)";
    $params[':id'] = (int)$search;
  } else {
    $query .= " AND p.name LIKE :search";
  }
  $params[':search'] = "%$search%";
}

// Thêm phân trang
$limit = 16; // Số sản phẩm mỗi trang
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;
$query .= " LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($query);
foreach ($params as $key => $value) {
  $stmt->bindValue($key, $value);
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$count_query = "SELECT COUNT(*) FROM products p LEFT JOIN product_details pd ON p.id = pd.product_id WHERE 1=1";

$count_params = []; // Tạo một mảng mới để tránh lỗi

if ($category) {
  $count_query .= " AND p.category = :category";
  $count_params[':category'] = $category;
}
if ($sub_category) {
  $count_query .= " AND p.sub_category = :sub_category";
  $count_params[':sub_category'] = $sub_category;
}
if ($gender) {
  $count_query .= " AND p.gender = :gender";
  $count_params[':gender'] = $gender;
}
if ($search) {
  if (is_numeric($search)) {
    $count_query .= " AND (p.id = :id OR p.name LIKE :search)";
    $count_params[':id'] = (int)$search;
  } else {
    $count_query .= " AND p.name LIKE :search";
  }
  $count_params[':search'] = "%$search%";
}

$stmt_count = $pdo->prepare($count_query);
foreach ($count_params as $key => $value) {
  $stmt_count->bindValue($key, $value);
}
$stmt_count->execute();
$total_products = $stmt_count->fetchColumn();
$total_pages = ceil($total_products / $limit);





// Truy vấn sản phẩm bán chạy
$stmt_bestsellers = $pdo->query("SELECT p.id, p.name, p.price, p.old_price, p.image, pd.sold 
                                FROM products p 
                                JOIN product_details pd ON p.id = pd.product_id 
                                WHERE pd.sold > 0
                                ORDER BY pd.sold DESC 
                                LIMIT 4");
$bestsellers = $stmt_bestsellers->fetchAll(PDO::FETCH_ASSOC);



// Kiểm tra avatar
if (!isset($_SESSION['avatar']) || empty($_SESSION['avatar'])) {
  $_SESSION['avatar'] = 'avatars/default-avatar.png';
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Figure - Good Smile Company</title>
  <link rel="shortcut icon" href="../Asset/Images/Logo/favicon.png" type="image/x-icon">
  <link rel="stylesheet" href="../Asset/Css/styles.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap"
    rel="stylesheet">

</head>

<body>

  <div class="overlay" data-overlay></div>

  <!-- THÔNG BÁO TOAST -->
  <div class="notification-toast auto-toast" data-toast-auto>
    <button class="toast-close-btn" data-toast-close-auto>
      <ion-icon name="close-outline"></ion-icon>
    </button>
    <div class="toast-banner">
      <img src="../Asset/Images/Products/Product-1.webp" alt="Sản phẩm" width="70" height="70">
    </div>
    <div class="toast-detail">
      <p class="toast-message">Có người vừa mua sản phẩm</p>
      <p class="toast-title">Hoshino Ruby Figure</p>
      <p class="toast-meta">5 phút trước</p>
    </div>
  </div>


  <!-- ĐẦU TRANG -->
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
          <img src="../Asset/Images/Logo/Logo.png" alt="Logo của Anon" width="160" height="46">
        </a>
        <div class="header-search-container">
          <form action="product.php" method="GET">
            <input type="search" name="search" class="search-field" placeholder="Nhập tên sản phẩm..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" class="search-btn"><ion-icon name="search-outline"></ion-icon></button>
          </form>
        </div>
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
          <a href="../pages/like.php" class="action-btn">
            <ion-icon name="heart-outline"></ion-icon>
            <span class="count">0</span>
          </a>
          </button>
          <button class="action-btn">
            <a href="Payment.php"><ion-icon name="cart-outline" style="color: var(--eerie-black);"></ion-icon></a>
            <span class="count">0</span>
          </button>
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
        <a href="Payment.php"><ion-icon name="cart-outline" style="color: var(--eerie-black);"></ion-icon></a>
        <span class="count">0</span>
      </button>

      <button class="action-btn">

        <a href="../index.php"><ion-icon name="home-outline" style="color: var(--eerie-black);"></ion-icon></a>

      </button>

      <button class=" action-btn">

        <a href="../pages/like.php" class="action-btn">
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

  <!-- PHẦN CHÍNH -->
  <main>
    <div class="product-container">
      <div class="container">
        <div class="sidebar has-scrollbar" style="margin-top: 15px; padding: 0px 20px; padding-bottom:10px" data-mobile-menu>
          <div class="sidebar-category">
            <div class="sidebar-top">
              <h2 class="sidebar-title">Danh Mục</h2>
              <button class="sidebar-close-btn" data-mobile-menu-close-btn><ion-icon name="close-outline"></ion-icon></button>
            </div>
            <ul class="sidebar-menu-category-list">
              <li class="sidebar-menu-category">
                <button class="sidebar-accordion-menu" data-accordion-btn>
                  <div class="menu-title-flex">
                    <img src="../Asset/Images/category/sidebar-1.jpg" alt="Figure" width="20" height="20" class="menu-title-img">
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
                      <a href="Product.php?category=<?php echo urlencode($cat); ?>" class="sidebar-submenu-title">
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
                    <img src="../Asset/Images/category/sidebar-2.webp" alt="PVC Figure" class="menu-title-img" width="20" height="20">
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
                      <a href="Product.php?category=Scale+Figure&subcategory=<?php echo urlencode($subcat); ?>" class="sidebar-submenu-title">
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
                    <img src="../Asset/Images/category/sidebar-3.webp" alt="Nenderoid" class="menu-title-img" width="20" height="20">
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
                      <a href="Product.php?category=Nenderoid&subcategory=<?php echo urlencode($subcat); ?>" class="sidebar-submenu-title">
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
                    <img src="../Asset/Images/category/sidebar-4.webp" alt="POP UP PARADE" class="menu-title-img" width="20" height="20">
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
                      <a href="Product.php?category=POP+UP+PARADE&subcategory=<?php echo urlencode($subcat); ?>" class="sidebar-submenu-title">
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
                    <img src="../Asset/Images/category/sidebar-5.webp" alt="Mecha Smile" class="menu-title-img" width="20" height="20">
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
                      <a href="Product.php?category=Mecha+Smile&subcategory=<?php echo urlencode($subcat); ?>" class="sidebar-submenu-title">
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
                    <img src="../Asset/Images/category/sidebar-7.webp" alt="Goods" class="menu-title-img" width="20" height="20">
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
                      <a href="Product.php?category=Goods&subcategory=<?php echo urlencode($subcat); ?>" class="sidebar-submenu-title">
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
                    <img src="../Asset/Images/category/category-7.webp" alt="figma Series" class="menu-title-img" width="20" height="20">
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
                      <a href="Product.php?category=Scale+Figure&subcategory=<?php echo urlencode($subcat); ?>" class="sidebar-submenu-title">
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
                    <a href="pages/product-detail.php?id=<?php echo $bestseller['id']; ?>" class="showcase-img-box">
                      <img src="../<?php echo htmlspecialchars($bestseller['image']); ?>"
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
          <div class="product-main">
            <h2 class="title">
              <?php
              if ($search) {
                echo "Kết quả tìm kiếm: '" . htmlspecialchars($search) . "'";
              } elseif ($category) {
                echo htmlspecialchars($category);
              } elseif ($sub_category) {
                echo htmlspecialchars($sub_category);
              } elseif ($gender) {
                echo "Figure " . htmlspecialchars($gender);
              } else {
                echo "Tất cả sản phẩm";
              }
              ?>
            </h2>
            <div class="product-grid" id="productGrid">
              <?php if (count($products) > 0): ?>
                <?php foreach ($products as $product): ?>
                  <div class="showcase">
                    <div class="showcase-banner">
                      <img src="../<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-img default" width="300" height="250">
                      <img src="../<?php echo htmlspecialchars($product['image_hover'] ?? $product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-img hover" width="300" height="250">
                      <?php
                      if ($product['is_new_product'] == 1) {
                        echo '<p class="showcase-badge angle pink">Mới</p>';
                      } elseif ($product['stock'] > 0 && $product['sold'] >= $product['stock']) {
                        echo '<p class="showcase-badge angle black">Hết hàng</p>';
                      } elseif ($product['old_price'] > 0 && $product['price'] < $product['old_price']) {
                        $discount = round((($product['old_price'] - $product['price']) / $product['old_price']) * 100);
                        echo '<p class="showcase-badge">' . $discount . '%</p>';
                      }
                      ?>
                      <div class="showcase-actions">
                        <button class="btn-action"><ion-icon name="heart-outline"></ion-icon></button>
                        <button class="btn-action"><ion-icon name="eye-outline"></ion-icon></button>
                        <button class="btn-action"><ion-icon name="repeat-outline"></ion-icon></button>
                        <button class="btn-action"><ion-icon name="bag-add-outline"></ion-icon></button>
                      </div>
                    </div>
                    <div class="showcase-content">
                      <a href="product.php?category=<?php echo urlencode($product['category']); ?>" class="showcase-category"><?php echo htmlspecialchars($product['category']); ?></a>
                      <a href="product-detail.php?id=<?php echo $product['id']; ?>">
                        <h3 class="showcase-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                      </a>
                      <div class="showcase-rating">
                        <?php
                        $rating = isset($product['rating']) ? (float)$product['rating'] : 4.0;
                        for ($i = 1; $i <= 5; $i++) {
                          echo '<ion-icon name="' . ($i <= $rating ? 'star' : 'star-outline') . '"></ion-icon>';
                        }
                        ?>
                      </div>
                      <div class="price-box">
                        <p class="price"><?php echo number_format($product['price'], 0, ',', '.') . 'đ'; ?></p>
                        <?php if ($product['old_price'] > 0): ?>
                          <del><?php echo number_format($product['old_price'], 0, ',', '.') . 'đ'; ?></del>
                        <?php endif; ?>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              <?php else: ?>
                <p>Không tìm thấy sản phẩm nào phù hợp.</p>
              <?php endif; ?>
            </div>
            <?php if ($total_pages > 1): ?>
              <div class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                  <a href="product.php?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>" class="<?php echo $i === $page ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                  </a>
                <?php endfor; ?>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- CHÂN TRANG -->
  <footer>
    <div class="footer-category">
      <div class="container">
        <h2 class="footer-category-title">Danh mục sản phẩm</h2>
        <div class="footer-category-box">
          <h3 class="category-box-title">Figure :</h3>
          <a href="#" class="footer-category-link">Figure</a>
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
            <a href="#" class="footer-nav-link">Figure</a>
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
            <a href="tel:+607936-8058" class="footer-nav-link">(607) 936-8058</a>
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
      </div>
    </div>
  </footer>

  <script src="../Asset/Js/script.js"></script>
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
  <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

</body>

</html>