<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("127.0.0.1", "root", "", "good_smile_db");
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = (int)$_POST['product_id'];
    $user_id = (int)$_SESSION['user_id'];
    $rating = (float)$_POST['rating']; // Giá trị từ 1-5
    $review_text = htmlspecialchars($_POST['review_text']);
    $review_date = date("Y-m-d");

    // Lấy thông tin người dùng
    $user_sql = "SELECT full_name FROM users WHERE id = ?";
    $user_stmt = $conn->prepare($user_sql);
    $user_stmt->bind_param("i", $user_id);
    $user_stmt->execute();
    $user_result = $user_stmt->get_result();
    $user = $user_result->fetch_assoc();
    $reviewer_name = $user['full_name'];

    // Thêm đánh giá vào cơ sở dữ liệu
    $sql = "INSERT INTO reviews (product_id, user_id, reviewer_name, rating, review_text, review_date) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisdss", $product_id, $user_id, $reviewer_name, $rating, $review_text, $review_date);

    if ($stmt->execute()) {
        // Cập nhật rating trung bình trong product_details
        $avg_sql = "UPDATE product_details 
                    SET rating = (SELECT AVG(rating) FROM reviews WHERE product_id = ?) 
                    WHERE product_id = ?";
        $avg_stmt = $conn->prepare($avg_sql);
        $avg_stmt->bind_param("ii", $product_id, $product_id);
        $avg_stmt->execute();
        $avg_stmt->close();

        header("Location: product-detail.php?id=" . $product_id . "&success=1");
    } else {
        echo "Lỗi: " . $conn->error;
    }

    $stmt->close();
    $user_stmt->close();
}
$conn->close();
