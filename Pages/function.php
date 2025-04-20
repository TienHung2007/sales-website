<?php

// Hàm hiển thị sản phẩm theo mục
function displayProducts($pdo, $condition, $title)
{
    $stmt = $pdo->prepare("
        SELECT p.`id`, p.`name`, p.`category`, p.`price`, p.`old_price`, p.`image`, 
               pd.`stock`, pd.`sold`
        FROM `products` p
        LEFT JOIN `product_details` pd ON p.`id` = pd.`product_id`
        WHERE $condition LIMIT 8
    ");
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $column1 = array_slice($products, 0, 4); // 4 sản phẩm cho cột 1
    $column2 = array_slice($products, 4, 4); // 4 sản phẩm cho cột 2
?>
    <div class="product-showcase">
        <h2 class="title"><?php echo htmlspecialchars($title); ?></h2>
        <div class="showcase-wrapper has-scrollbar">
            <!-- Cột 1 -->
            <div class="showcase-container">
                <?php foreach ($column1 as $product):
                    $is_out_of_stock = ($product['stock'] > 0 && $product['sold'] >= $product['stock']);
                ?>
                    <div class="showcase">
                        <a href="pages/product-detail.php?id=<?php echo $product['id']; ?>" class="showcase-img-box">
                            <img src="<?php echo htmlspecialchars($product['image']); ?>"
                                alt="<?php echo htmlspecialchars($product['name']); ?>"
                                width="70" class="showcase-img">
                        </a>
                        <div class="showcase-content">
                            <a href="pages/product-detail.php?id=<?php echo $product['id']; ?>">
                                <h4 class="showcase-title"><?php echo htmlspecialchars($product['name']); ?></h4>
                            </a>
                            <a href="pages/product.php?$product['category'];" class="showcase-category"><?php echo htmlspecialchars($product['category']); ?></a>
                            <div class="price-box">
                                <p class="price"><?php echo number_format((float)$product['price'] / 1000, 0) . 'k'; ?></p>
                                <?php if ($product['old_price']): ?>
                                    <del><?php echo number_format((float)$product['old_price'] / 1000, 0) . 'k'; ?></del>
                                <?php endif; ?>
                            </div>
                            <?php if ($is_out_of_stock): ?>
                                <div class="stock-status">
                                    <ion-icon name="close-circle-outline"></ion-icon> <strong>Hết hàng</strong>
                                </div>
                            <?php else: ?>
                                <div class="stock-status">
                                    <ion-icon name="checkmark-circle-outline"></ion-icon> Còn hàng:
                                    <strong><?php echo htmlspecialchars($product['stock'] - $product['sold']); ?></strong>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <!-- Cột 2 -->
            <div class="showcase-container">
                <?php foreach ($column2 as $product):
                    $is_out_of_stock = ($product['stock'] > 0 && $product['sold'] >= $product['stock']);
                ?>
                    <div class="showcase">
                        <a href="pages/product-detail.php?id=<?php echo $product['id']; ?>" class="showcase-img-box">
                            <img src="<?php echo htmlspecialchars($product['image']); ?>"
                                alt="<?php echo htmlspecialchars($product['name']); ?>"
                                width="70" class="showcase-img">
                        </a>
                        <div class="showcase-content">
                            <a href="pages/product-detail.php?id=<?php echo $product['id']; ?>">
                                <h4 class="showcase-title"><?php echo htmlspecialchars($product['name']); ?></h4>
                            </a>
                            <a href="#" class="showcase-category"><?php echo htmlspecialchars($product['category']); ?></a>
                            <div class="price-box">
                                <p class="price"><?php echo number_format((float)$product['price'] / 1000, 0) . 'k'; ?></p>
                                <?php if ($product['old_price']): ?>
                                    <del><?php echo number_format((float)$product['old_price'] / 1000, 0) . 'k'; ?></del>
                                <?php endif; ?>
                            </div>
                            <?php if ($is_out_of_stock): ?>
                                <div class="stock-status">
                                    <ion-icon name="close-circle-outline"></ion-icon> <strong>Hết hàng</strong>
                                </div>
                            <?php else: ?>
                                <div class="stock-status">
                                    <ion-icon name="checkmark-circle-outline"></ion-icon> Còn hàng:
                                    <strong><?php echo htmlspecialchars($product['stock'] - $product['sold']); ?></strong>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php
}

// Hàm hiển thị sản phẩm mới trong phần PRODUCT GRID
function displayNewProductsGrid($pdo, $offset = 0, $limit = 12)
{
    $stmt = $pdo->prepare("
        SELECT p.`id`, p.`name`, p.`category`, p.`price`, p.`old_price`, p.`image`, 
               pd.`image_hover`, pd.`is_new_product`, pd.`stock`, pd.`sold`, pd.`rating`
        FROM `products` p
        LEFT JOIN `product_details` pd ON p.`id` = pd.`product_id`
        ORDER BY RAND()
        LIMIT :limit OFFSET :offset
    ");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Đếm tổng số sản phẩm để kiểm tra xem có cần nút "Xem thêm" không
    $totalStmt = $pdo->query("SELECT COUNT(*) FROM products");
    $totalProducts = $totalStmt->fetchColumn();
    $showLoadMore = ($offset + $limit) < $totalProducts;
?>
    <div class="product-box">
        <div class="product-main">
            <h2 class="title">SẢN PHẨM MỚI</h2>
            <div class="product-grid" id="productGrid">
                <?php foreach ($products as $product): ?>
                    <div class="showcase">
                        <div class="showcase-banner">
                            <img src="<?php echo htmlspecialchars($product['image']); ?>"
                                alt="<?php echo htmlspecialchars($product['name']); ?>"
                                width="300" height="250" class="product-img default">
                            <!-- Sửa để luôn hiển thị ảnh hover, dùng image nếu image_hover không có -->
                            <img src="<?php echo htmlspecialchars($product['image_hover'] ?: $product['image']); ?>"
                                alt="<?php echo htmlspecialchars($product['name']); ?>"
                                width="300" height="250" class="product-img hover">

                            <!-- Nhãn -->
                            <?php if ($product['is_new_product'] == 1): ?>
                                <p class="showcase-badge angle pink">Mới</p>
                            <?php elseif ($product['stock'] > 0 && $product['sold'] >= $product['stock']): ?>
                                <p class="showcase-badge angle black">Đã bán</p>
                            <?php elseif ($product['old_price'] > 0 && $product['price'] < $product['old_price']):
                                $discount = round((($product['old_price'] - $product['price']) / $product['old_price']) * 100); ?>
                                <p class="showcase-badge"><?php echo $discount; ?>%</p>
                            <?php endif; ?>
                            <div class="showcase-actions">
                                <button class="btn-action"><ion-icon name="heart-outline"></ion-icon></button>
                                <button class="btn-action"><ion-icon name="eye-outline"></ion-icon></button>
                                <button class="btn-action"><ion-icon name="repeat-outline"></ion-icon></button>
                                <button class="btn-action"><ion-icon name="bag-add-outline"></ion-icon></button>
                            </div>
                        </div>

                        <div class="showcase-content">
                            <a href="pages/product.php?id=<?php echo $product['category']; ?>" class="showcase-category"><?php echo htmlspecialchars($product['category']); ?></a>
                            <a href="pages/product-detail.php?id=<?php echo $product['id']; ?>">
                                <h3 class="showcase-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                            </a>
                            <div class="showcase-rating">
                                <?php
                                $rating = isset($product['rating']) ? (float)$product['rating'] : 4.0;
                                for ($i = 1; $i <= 5; $i++):
                                    if ($i <= floor($rating)) {
                                        echo '<ion-icon name="star"></ion-icon>';
                                    } elseif ($i == ceil($rating) && $rating - floor($rating) >= 0.5) {
                                        echo '<ion-icon name="star-half-outline"></ion-icon>';
                                    } else {
                                        echo '<ion-icon name="star-outline"></ion-icon>';
                                    }
                                endfor;
                                ?>
                            </div>
                            <div class="price-box">
                                <p class="price"><?php echo number_format((float)$product['price'] / 1000, 0) . 'k'; ?></p>
                                <?php if ($product['old_price'] > 0): ?>
                                    <del><?php echo number_format((float)$product['old_price'] / 1000, 0) . 'k'; ?></del>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php
}


function searchProduct($keyword, $conn)
{
    $sql = "SELECT * FROM products WHERE id = ? OR name LIKE ?";
    $stmt = $conn->prepare($sql);
    $searchTerm = "%" . $keyword . "%";
    $stmt->bind_param("ss", $keyword, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }

    return $products;
}
?>