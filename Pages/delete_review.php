<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập để xóa đánh giá!']);
    exit();
}

$conn = new mysqli("127.0.0.1", "root", "", "good_smile_db");
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Kết nối cơ sở dữ liệu thất bại!']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $review_id = isset($_POST['review_id']) ? (int)$_POST['review_id'] : 0;
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $user_id = (int)$_SESSION['user_id'];

    // Kiểm tra xem đánh giá có thuộc về người dùng hiện tại không
    $check_sql = "SELECT user_id FROM reviews WHERE id = ? AND product_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $review_id, $product_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    $review = $result->fetch_assoc();

    if ($review && $review['user_id'] == $user_id) {
        // Xóa đánh giá
        $delete_sql = "DELETE FROM reviews WHERE id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $review_id);

        if ($delete_stmt->execute()) {
            // Cập nhật rating trung bình
            $avg_sql = "UPDATE product_details 
                        SET rating = (SELECT AVG(rating) FROM reviews WHERE product_id = ?) 
                        WHERE product_id = ?";
            $avg_stmt = $conn->prepare($avg_sql);
            $avg_stmt->bind_param("ii", $product_id, $product_id);
            $avg_stmt->execute();
            $avg_stmt->close();

            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Không thể xóa đánh giá!']);
        }
        $delete_stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Bạn không có quyền xóa đánh giá này!']);
    }
    $check_stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Yêu cầu không hợp lệ!']);
}

$conn->close();
