<?php
session_start();
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}

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
    <title>F:NEX giới thiệu mô hình Tokisaki Kurumi 1:1 - Good Smile Figure</title>
    <link rel="shortcut icon" href="../Asset/Images/Logo/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="../Asset/Css/styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
</head>

<body>
    <!-- HEADER -->
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

                <a href="../index.php" class="header-logo">

                    <img src="../Asset/Images/Logo/Logo.png" alt="Logo" width="160" height="46">

                </a>

                <div class="header-search-container">

                    <input type="search" name="search" class="search-field" placeholder="Nhập tên sản phẩm...">

                    <button class="search-btn">

                        <ion-icon name="search-outline"></ion-icon>

                    </button>

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

    <!-- MAIN CONTENT -->
    <main>
        <div class="container blog">
            <h1 class="title">F:NEX giới thiệu mô hình Tokisaki Kurumi 1:1</h1>
            <div class="blog-card">
                <img src="../Asset/Images/blog/blog-1.webp" alt="Mô hình Tokisaki Kurumi 1:1" class="blog-banner-new">
                <div class="blog-content">
                    <a href="#" class="blog-category">Figure</a>
                    <p class="blog-meta">Bởi <cite>Hưng Hay Ho</cite> / <time datetime="2025-04-03">3/4/2025</time></p>
                    <div class="blog-desc">
                        <p>F:NEX, một thương hiệu nổi tiếng trong lĩnh vực sản xuất mô hình figure cao cấp, vừa công bố sản phẩm mới nhất của mình: mô hình Tokisaki Kurumi với tỷ lệ 1:1. Đây là một trong những sản phẩm được mong chờ nhất bởi các fan của series anime "Date A Live", nơi nhân vật Tokisaki Kurumi đã chiếm được trái tim của hàng triệu người hâm mộ nhờ vẻ ngoài quyến rũ và tính cách bí ẩn.</p>
                        <p>Mô hình này không chỉ gây ấn tượng bởi kích thước thực tế (tỷ lệ 1:1 so với nhân vật trong anime) mà còn bởi độ chi tiết đáng kinh ngạc. Từ mái tóc dài óng ả, đôi mắt đỏ rực đầy ma mị, đến bộ trang phục đặc trưng của Kurumi, tất cả đều được tái hiện một cách hoàn hảo. Sản phẩm được làm từ chất liệu nhựa cao cấp ABS và PVC, đảm bảo độ bền và tính thẩm mỹ.</p>
                        <img src="../Asset/Images/blog/blog-1-2.jpg" alt="Tokisaki Kurumi Zafkiel" class="blog-banner">
                        <p>Theo thông tin từ F:NEX, mô hình Tokisaki Kurumi 1:1 sẽ có chiều cao khoảng 1m60, tương đương với chiều cao của một người thật. Sản phẩm đi kèm với một đế đứng chắc chắn và một số phụ kiện đặc biệt, chẳng hạn như khẩu súng thời gian biểu tượng của Kurumi. Đây chắc chắn là một món đồ sưu tầm không thể bỏ qua đối với các tín đồ figure.</p>
                        <p>Dự kiến, sản phẩm sẽ được mở đặt trước vào cuối tháng 4 năm 2025 với mức giá chưa được công bố chính thức. Tuy nhiên, dựa trên các sản phẩm tương tự trước đây của F:NEX, nhiều người dự đoán rằng mức giá có thể rơi vào khoảng từ 15 triệu đến 20 triệu VND, tùy thuộc vào số lượng phụ kiện đi kèm.</p>
                        <p>Hãy theo dõi Good Smile Figure để cập nhật thêm thông tin chi tiết về sản phẩm này và đừng bỏ lỡ cơ hội sở hữu một tuyệt phẩm trong bộ sưu tập của bạn!</p>
                    </div>
                </div>
            </div>

            <!-- RATING SECTION -->
            <div class="rating-section">
                <h2 class="title">Đánh giá bài viết</h2>
                <form class="rating-form">
                    <div class="stars">
                        <ion-icon name="star-outline"></ion-icon>
                        <ion-icon name="star-outline"></ion-icon>
                        <ion-icon name="star-outline"></ion-icon>
                        <ion-icon name="star-outline"></ion-icon>
                        <ion-icon name="star-outline"></ion-icon>
                    </div>
                    <button type="submit">Gửi đánh giá</button>
                </form>
                <p class="blog-meta">Đánh giá trung bình: <span>4.5/5 (20 lượt)</span></p>
            </div>

            <!-- COMMENT SECTION -->
            <div class="comment-section">
                <h2 class="title">Bình luận</h2>
                <form class="comment-form">
                    <textarea placeholder="Viết bình luận của bạn..." required></textarea>
                    <button type="submit">Gửi bình luận</button>
                </form>
                <div class="comment-list">
                    <div class="comment-item">
                        <p class="comment-meta"><cite>Nguyễn Văn A</cite> - <time datetime="2025-04-05">5/4/2025</time></p>
                        <p class="comment-text">Bài viết rất chi tiết, mình rất hào hứng chờ ngày đặt trước mô hình này!</p>
                    </div>
                    <div class="comment-item">
                        <p class="comment-meta"><cite>Trần Thị B</cite> - <time datetime="2025-04-06">6/4/2025</time></p>
                        <p class="comment-text">Kurumi là nhân vật yêu thích của mình, cảm ơn shop đã cập nhật thông tin nhanh chóng.</p>
                    </div>
                </div>
            </div>

            <!-- RELATED PRODUCTS -->
            <div class="related-products">
                <h2 class="title">Sản phẩm bạn có thể quan tâm</h2>
                <div class="product-grid">
                    <div class="showcase">
                        <div class="showcase-banner">
                            <img src="../Asset/Images/Products/new1-1.webp" alt="Tokisaki Kurumi Figure" class="product-img default">
                            <img src="../Asset/Images/Products/new1-2.webp" alt="Tokisaki Kurumi Figure Hover" class="product-img hover">
                            <p class="showcase-badge">Mới</p>
                            <div class="showcase-actions">
                                <button class="btn-action"><ion-icon name="heart-outline"></ion-icon></button>
                                <button class="btn-action"><ion-icon name="eye-outline"></ion-icon></button>
                            </div>
                        </div>
                        <div class="showcase-content">
                            <a href="#" class="showcase-category">Figure</a>
                            <a href="#">
                                <h3 class="showcase-title">Tokisaki Kurumi Zafkiel</h3>
                            </a>
                            <div class="showcase-rating">
                                <ion-icon name="star"></ion-icon>
                                <ion-icon name="star"></ion-icon>
                                <ion-icon name="star"></ion-icon>
                                <ion-icon name="star"></ion-icon>
                                <ion-icon name="star-outline"></ion-icon>
                            </div>
                            <div class="price-box">
                                <p class="price">15,000,000đ</p>
                                <del>18,000,000đ</del>
                            </div>
                        </div>
                    </div>

                    <div class="showcase">
                        <div class="showcase-banner">
                            <img src="../Asset/Images/Products/list8-1.webp" alt="Figure khác" class="product-img default">
                            <img src="../Asset/Images/Products/list8-2.webp" alt="Figure khác Hover" class="product-img hover">
                            <p class="showcase-badge pink">Giảm giá</p>
                            <div class="showcase-actions">
                                <button class="btn-action"><ion-icon name="heart-outline"></ion-icon></button>
                                <button class="btn-action"><ion-icon name="eye-outline"></ion-icon></button>
                            </div>
                        </div>
                        <div class="showcase-content">
                            <a href="#" class="showcase-category">Figure</a>
                            <a href="#">
                                <h3 class="showcase-title">Figure Anime Khác</h3>
                            </a>
                            <div class="showcase-rating">
                                <ion-icon name="star"></ion-icon>
                                <ion-icon name="star"></ion-icon>
                                <ion-icon name="star"></ion-icon>
                                <ion-icon name="star-half-outline"></ion-icon>
                                <ion-icon name="star-outline"></ion-icon>
                            </div>
                            <div class="price-box">
                                <p class="price">12,000,000đ</p>
                                <del>15,000,000đ</del>
                            </div>
                        </div>
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
    <script src="../Asset/Js/script.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <script type="text/javascript">
        function googleTranslateElementInit() {
            new google.translate.TranslateElement({
                pageLanguage: 'vi',
                includedLanguages: 'vi,en,zh-CN,ja',
                layout: google.translate.TranslateElement.InlineLayout.SIMPLE,
                autoDisplay: false
            }, 'google_translate_element');
        }
    </script>
    <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
</body>

</html>