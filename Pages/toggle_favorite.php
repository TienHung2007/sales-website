<?php
session_start();
require_once 'db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập!']);
    exit();
}

$user_id = $_SESSION['user_id'];
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

if ($product_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID sản phẩm không hợp lệ!']);
    exit();
}

try {
    // Kiểm tra và xóa sản phẩm khỏi danh sách yêu thích
    $stmt = $pdo->prepare("DELETE FROM user_favorites WHERE user_id = :user_id AND product_id = :product_id");
    $result = $stmt->execute(['user_id' => $user_id, 'product_id' => $product_id]);

    if ($result && $stmt->rowCount() > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Sản phẩm không có trong danh sách yêu thích!']);
    }
} catch (PDOException $e) {
    error_log("Lỗi khi xóa yêu thích: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra khi xóa yêu thích!']);
}
