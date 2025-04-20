-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th4 03, 2025 lúc 03:43 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `good_smile_db`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  `sub_category` varchar(100) NOT NULL,
  `gender` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `old_price` decimal(10,2) DEFAULT NULL,
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `products`
--

INSERT INTO `products` (`id`, `name`, `category`, `sub_category`, `gender`, `price`, `old_price`, `image`) VALUES
(1, 'Pop Up Parade Kitagawa Marin', 'POP UP PARADE', 'L size', 'female', 900000.00, 1000000.00, 'Asset/Images/Products/product-4.webp'),
(2, 'Shizuku Kuroe Cosplay by Marin 1/6', 'Scale Figure', 'scale 1/6', 'Female', 3600000.00, 4000000.00, 'Asset/Images/Products/product-5.webp'),
(3, 'Nhồi bông Chocopuni Plush Mari', 'Gấu bông', 'Other Female', '', 1340000.00, 1400000.00, 'Asset/Images/Products/product-6.webp'),
(5, 'POP UP PARADE Yanami Anna L', 'POP UP PARADE', 'L size', 'Female', 1500000.00, 1600000.00, 'Asset/Images/Products/product-8.webp'),
(6, 'Kasugano Sora Pop Up Parade', 'POP UP PARADE', 'L size', 'Female', 1500000.00, 1850000.00, 'Asset/Images/Products/product-9.webp'),
(7, 'YoRHa Type A No.2 Pop Up Parade', 'POP UP PARADE', 'SP', 'Female', 960000.00, 0.00, 'Asset/Images/Products/product-10.jpg'),
(8, 'Nendoroid 2727 Sorasaki Hina', 'Nenderoid', 'Surprise', 'Female', 1450000.00, 1500000.00, 'Asset/Images/Products/product-11.jpg'),
(9, 'Frieren Blow Kiss Ver. Pop Up Parade', 'POP UP PARADE', 'SP', 'Female', 900000.00, 1000000.00, 'Asset/Images/Products/product-12.webp'),
(10, 'Nendoroid 2694 Robin', 'Nenderoid', 'Dolly', 'Female', 1550000.00, 1800000.00, 'Asset/Images/Products/product-13.webp'),
(11, 'POP UP PARADE Yuno Gasai', 'POP UP PARADE', 'L size', 'Female', 1500000.00, 1200000.00, 'Asset/Images/Products/product-14.webp'),
(12, 'Frieren Silly Face', 'Gấu bông', 'other Female', '', 1100000.00, 1200000.00, 'Asset/Images/Products/product-15.jpg'),
(13, 'Nishiki Kope 1/5', 'Scale Figure', 'Scale 1/6', 'Female', 3500000.00, 3900000.00, 'Asset/Images/Products/product-16.jpg'),
(14, 'POP UP PARADE Hojo Tokiyuki', 'POP UP PARADE', 'L size', 'Male', 1250000.00, 1400000.00, 'Asset/Images/Products/product-17.webp'),
(15, 'Nendoroid 2593 Ayase Saki', 'Nenderoid', 'More', 'Female', 1300000.00, 1400000.00, 'Asset/Images/Products/product-18.webp'),
(16, 'POP UP PARADE Rem', 'POP UP PARADE', 'L size', 'Female', 1790000.00, 1880000.00, 'Asset/Images/Products/product-19.webp'),
(17, 'Kana Arima Date Style Ver 1/6', 'Scale Figure', 'Scale 1/6', 'Female', 3600000.00, 3900000.00, 'Asset/Images/Products/product-20.webp'),
(18, 'POP UP PARADE Alpha', 'POP UP PARADE', 'SP', 'Female', 1760000.00, 1800000.00, 'Asset/Images/Products/product-21.webp'),
(19, 'Shizuku Kuroe Cosplay by Marin 1/6', 'Scale Figure', 'Scale 1/6', 'Female', 3400000.00, 3700000.00, 'Asset/Images/Products/product-22.webp'),
(20, 'Pop Up Parade Shujinkou Joker', 'POP UP PARADE', 'L size', 'Male', 960000.00, 1100000.00, 'Asset/Images/Products/product-23.webp'),
(21, 'POP UP PARADE Liliel 3rd Squad Outfit Ver', 'POP UP PARADE', 'L size', 'Female', 1800000.00, 2000000.00, 'Asset/Images/Products/product-24.webp'),
(22, 'POP UP PARADE Annie Leonhart Female Titan Ver', 'POP UP PARADE', 'XL size', 'Female', 2090000.00, 2300000.00, 'Asset/Images/Products/product-25.webp'),
(23, 'Nendoroid 2582 Zhongli', 'Nenderoid', 'EZ', 'Male', 1350000.00, 1400000.00, 'Asset/Images/Products/product-26.webp'),
(24, 'Utaha Kasumigaoka Animation Ver. 1/4', 'Scale Figure', 'Scale 1/4', 'Female', 2850000.00, 3000000.00, 'Asset/Images/Products/product-27.webp'),
(25, 'POP UP PARADE Chinatsu Kano L Size - Blue Box | Good Smile Company Figure', 'POP UP PARADE', 'L size', 'Female', 1600000.00, 2000000.00, 'Asset/Images/Products/product-28.webp'),
(26, 'Hatsune Miku 0x27 Eternal Stream 1/4 - Vocaloid', 'Scale Figure', 'Scale 1/4', 'Female', 6500000.00, 7200000.00, 'Asset/Images/Products/product-29.webp'),
(27, 'Tokisaki Kurumi Zafkiel', 'Scale Figure', 'other', 'Female', 510000.00, 600000.00, 'Asset/Images/Products/new1-1.webp'),
(28, 'Hatsune Miku BiCute Bunnies rurudo ver.', 'Scale Figure', 'other', 'Female', 630000.00, 750000.00, 'Asset/Images/Products/new2-1.webp'),
(29, 'Anya Forger Natsuyasumi Luminasta', 'Scale Figure', 'other', 'Female', 490000.00, 550000.00, 'Asset/Images/Products/new3-1.webp'),
(30, 'Elaina Trio-Try-iT', 'Scale Figure', 'other', 'Female', 450000.00, 500000.00, 'Asset/Images/Products/new4-1.webp'),
(31, 'Play with Paint Vol.1 Kaga Sumire', 'Scale Figure', 'other', 'Female', 540000.00, 600000.00, 'Asset/Images/Products/new5-1.webp'),
(32, 'Nakano Yotsuba Marine Look Trio-Try-iT', 'Scale Figure', 'other', 'Female', 420000.00, 4500000.00, 'Asset/Images/Products/new6-1.webp'),
(33, 'Kigurumi Plushie Noko Shikanoko', 'Gấu bông', 'Other Female', '', 1050000.00, 1200000.00, 'Asset/Images/Products/new7-1.webp'),
(34, 'Formidable 1/7 Scale Still Illustration Ver.', 'Scale Figure', 'Scale 1/7', 'Female', 2100000.00, 2500000.00, 'Asset/Images/Products/new8-1.webp'),
(35, 'Ui Swimsuit 1/7', 'Scale Figure', 'Scale 1/7', 'Female', 5600000.00, 6500000.00, 'Asset/Images/Products/new9-1.webp'),
(36, 'Houshou Marine 1/7', 'Scale Figure', 'Scale 1/7', 'Female', 5400000.00, 5900000.00, 'Asset/Images/Products/new10-1.webp'),
(37, 'Tenitol Tall Nachoneko', 'Scale Figure', 'other', 'Female', 1850000.00, 2100000.00, 'Asset/Images/Products/new11-1.webp'),
(38, 'Pop Up Parade Izayoi Nonomi Mischievous☆Straight Ver.', 'POP UP PARADE', 'L size', 'Female', 960000.00, 1200000.00, 'Asset/Images/Products/new12-1.webp'),
(39, 'Racing EL FAIL 1/7 - DJMAX RESPECT/V', 'Scale Figure', 'Scale 1/7', 'Female', 4290000.00, 5500000.00, 'Asset/Images/Products/list1-1.webp'),
(40, 'Misae Suzuhara Bunny Ver. 2nd 1/4 RAITA Original Character', 'Scale Figure', 'Scale 1/4', 'Female', 9900000.00, 11000000.00, 'Asset/Images/Products/list2-1.webp'),
(41, 'Nakano Nino Bunny Ver. Desktop Cute', 'Scale Figure', 'other', 'Female', 4200000.00, 4800000.00, 'Asset/Images/Products/list3-1.webp'),
(42, 'Evangelion Shin Gekijouban - Ayanami Rei ~ tentative name ~ 1/7', 'Scale Figure', 'Scale 1/7', 'Female', 4900000.00, 5200000.00, 'Asset/Images/Products/list4-1.webp'),
(43, 'Foreigner/Katsushika Hokusai: Travel Portrait Ver. 1/7', 'Scale Figure', 'Scale 1/7', 'Female', 5500000.00, 6000000.00, 'Asset/Images/Products/list5-1.webp'),
(44, 'Mahiru Shiina - The Angel Next Door Spoils Me Rotten F:Nex 1/7', 'Scale Figure', 'Scale 1/7', 'Female', 3612000.00, 3900000.00, 'Asset/Images/Products/list6-1.webp'),
(45, '753 Plain Clothes Ver Glitter & Glamours', 'Scale Figure', 'other', 'Female', 430000.00, 490000.00, 'Asset/Images/Products/list7-1.webp'),
(46, 'Hoshino Ai Espresto Excite Motions', 'Scale Figure', 'other', 'Female', 441000.00, 490000.00, 'Asset/Images/Products/list8-1.webp'),
(47, 'Hatsune Miku Aqua Float Girls - Piapro Characters', 'Scale Figure', 'other', 'Female', 450000.00, NULL, 'Asset/Images/Products/list9-1.webp'),
(48, 'Biya Yuna Bunny Girl Ver 1/4 - Original', 'Scale Figure', 'Scale 1/4', 'Female', 1950000.00, 2000000.00, 'Asset/Images/Products/list10-1.webp'),
(49, 'POP UP PARADE Hiiragi Utena Magia Baiser L size', 'POP UP PARADE', 'L size', 'Female', 1875000.00, 2500000.00, 'Asset/Images/Products/lis11-1.webp'),
(50, 'Kurosaki Ichigo - Figuarts ZERO - Bleach Sennen Kessen-hen', 'Scale Figure', 'other', 'Male', 1806000.00, 2100000.00, 'Asset/Images/Products/list12-1.jpeg'),
(51, 'POP UP PARADE Zero Two - DARLING in the FRANXX', 'POP UP PARADE', 'L size', 'Female', 900000.00, NULL, 'Asset/Images/Products/list13-1.webp'),
(52, 'Uzaki-chan wa Asobitai! 2nd Season Hana Uzaki Cow Bikini 1/7', 'Scale Figure', 'Scale 1/7', 'Female', 4300000.00, 4800000.00, 'Asset/Images/Products/list14-1.webp'),
(53, 'figma 638 Ninomae Ina\'nis', 'Figma', 'Figma', 'Female', 3200000.00, 3500000.00, 'Asset/Images/Products/list15-1.webp'),
(54, 'Gotoubun no Hanayome - Nakano Nino - Shibuya Scramble Figure - 1/7', 'Scale Figure', 'Scale 1/7', 'Female', 5800000.00, NULL, 'Asset/Images/Products/list16-1.webp');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_details`
--

CREATE TABLE `product_details` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `image_hover` varchar(255) DEFAULT NULL,
  `is_new_arrival` tinyint(1) DEFAULT 0,
  `is_best_seller` tinyint(1) DEFAULT 0,
  `is_top_rated` tinyint(1) DEFAULT 0,
  `is_daily_deal` tinyint(1) DEFAULT 0,
  `is_new_product` tinyint(1) DEFAULT 0,
  `stock` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deal_end_time` timestamp NULL DEFAULT NULL,
  `description` text DEFAULT NULL,
  `sold` int(11) DEFAULT 0,
  `rating` decimal(3,1) DEFAULT 4.0,
  `remaining` int(11) DEFAULT NULL,
  `deal_start_time` timestamp NULL DEFAULT NULL,
  `size` varchar(50) DEFAULT NULL,
  `scale` varchar(20) DEFAULT NULL,
  `material` varchar(100) DEFAULT NULL,
  `weight` varchar(50) DEFAULT NULL,
  `release_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `product_details`
--

INSERT INTO `product_details` (`id`, `product_id`, `image_hover`, `is_new_arrival`, `is_best_seller`, `is_top_rated`, `is_daily_deal`, `is_new_product`, `stock`, `created_at`, `updated_at`, `deal_end_time`, `description`, `sold`, `rating`, `remaining`, `deal_start_time`, `size`, `scale`, `material`, `weight`, `release_date`) VALUES
(1, 1, 'Asset/Images/Product-detail/detail-2-1.webp', 1, 0, 0, 0, 0, 30, '2025-03-22 22:48:27', '2025-04-02 09:21:02', NULL, 'Mô hình POP UP PARADE Kitagawa Marin từ anime nổi tiếng \"My Dress-Up Darling\" (tựa Nhật: Sono Bisque Doll wa Koi wo Suru), được sản xuất bởi Good Smile Company. Đây là phiên bản kích thước L với chiều cao khoảng 190mm, tái hiện chân thực nhân vật Kitagawa Marin trong bộ trang phục học sinh quen thuộc. Sản phẩm được chế tác từ nhựa PVC và ABS cao cấp, đi kèm đế đứng chắc chắn, phù hợp để trưng bày trong bộ sưu tập. Mô hình này nổi bật với chi tiết khuôn mặt sống động và màu sắc tươi sáng, là lựa chọn hoàn hảo cho các fan của series.', 15, 3.5, NULL, NULL, '190mm', 'L size', 'PVC, ABS', '', '2025-01-15'),
(2, 2, 'Asset/Images/Product-detail/detail-3-1.webp', 0, 1, 0, 0, 0, 15, '2025-03-23 06:01:29', '2025-03-28 13:41:34', NULL, 'Mô hình Shizuku Kuroe Cosplay by Marin tỷ lệ 1/6 thuộc dòng Scale Figure, lấy cảm hứng từ series \"My Dress-Up Darling\". Sản phẩm tái hiện nhân vật Shizuku Kuroe trong bộ trang phục cosplay do Marin thiết kế, với chiều cao khoảng 280mm. Được chế tác từ PVC và ABS, mô hình này nổi bật với các chi tiết tinh xảo như lớp vải áo, mái tóc đen mượt mà, và biểu cảm quyến rũ. Sản phẩm đi kèm phụ kiện nhỏ như đôi giày và đế đứng minh họa bối cảnh, mang đến trải nghiệm trưng bày ấn tượng cho người sưu tầm.', 0, 4.6, NULL, NULL, '280mm', 'Scale 1/6', 'PVC, ABS', '450g', '2025-02-10'),
(3, 3, 'Asset/Images/Product-detail/detail-4-1.webp\r\n', 0, 0, 1, 0, 0, 20, '2025-03-23 06:05:28', '2025-03-29 00:38:19', NULL, 'Gấu bông Chocopuni Plush Mari là sản phẩm nhồi bông cao cấp từ Good Smile Company, mang phong cách dễ thương đặc trưng của nhân vật Mari trong dòng sản phẩm Chocopuni. Với chiều cao khoảng 300mm, gấu bông được làm từ chất liệu polyester mềm mại, an toàn, mang lại cảm giác ôm thoải mái. Thiết kế nổi bật với đôi mắt to tròn, bộ lông màu nâu socola, và chiếc nơ xinh xắn trên đầu, phù hợp làm món quà hoặc vật trang trí cho các fan yêu thích sự đáng yêu.', 0, 3.1, NULL, NULL, '300mm', NULL, 'Polyester', '180g', '2025-03-01'),
(4, 5, 'Asset/Images/Product-detail/detail-5-1.webp', 1, 0, 0, 0, 0, 5, '2025-03-23 06:14:00', '2025-03-29 00:49:40', NULL, 'Mô hình POP UP PARADE Yanami Anna kích thước L từ \"My Dress-Up Darling\" là một sản phẩm nổi bật trong dòng POP UP PARADE của Good Smile Company. Với chiều cao khoảng 185mm, mô hình tái hiện nhân vật Yanami Anna trong trang phục thường ngày, với mái tóc dài và biểu cảm dịu dàng. Được làm từ PVC và ABS, sản phẩm có lớp sơn mịn màng và các chi tiết nhỏ được chăm chút kỹ lưỡng, đi kèm đế đứng đơn giản nhưng chắc chắn. Đây là lựa chọn lý tưởng cho những ai yêu thích sự tinh tế trong từng nhân vật phụ của series.', 0, 4.9, NULL, NULL, '185mm', 'L size', 'PVC, ABS', '230g', '2025-02-20'),
(5, 6, NULL, 0, 1, 0, 0, 0, 5, '2025-03-23 06:14:00', '2025-03-28 13:41:34', NULL, 'Mô hình Kasugano Sora POP UP PARADE kích thước L từ \"Yosuga no Sora\" mang đến hình ảnh nhân vật Kasugano Sora đầy cảm xúc. Với chiều cao khoảng 180mm, sản phẩm được chế tác từ PVC và ABS, tái hiện nhân vật trong bộ trang phục mùa hè nhẹ nhàng, mái tóc bạc đặc trưng và ánh mắt sâu thẳm. Đế đứng được thiết kế tối giản, giúp nổi bật vẻ đẹp thanh thoát của mô hình. Đây là một sản phẩm không thể thiếu trong bộ sưu tập của các fan yêu thích series này.', 0, 4.1, NULL, NULL, '180mm', 'L size', 'PVC, ABS', '220g', '2025-03-15'),
(6, 7, NULL, 0, 0, 1, 0, 0, 24, '2025-03-23 06:14:00', '2025-03-28 13:41:34', NULL, 'Mô hình YoRHa Type A No.2 (A2) POP UP PARADE SP từ \"NieR: Automata\" là phiên bản đặc biệt trong dòng POP UP PARADE của Good Smile Company. Với chiều cao khoảng 175mm, mô hình tái hiện nhân vật A2 trong bộ giáp chiến đấu rách rưới đầy cá tính, cùng thanh kiếm biểu tượng. Được làm từ PVC và ABS, sản phẩm có lớp sơn chi tiết với hiệu ứng bụi bẩn và vết trầy xước, đi kèm đế đứng mô phỏng chiến trường. Đây là món đồ sưu tầm hoàn hảo cho fan của tựa game đình đám.', 0, 4.4, NULL, NULL, '175mm', 'SP', 'PVC, ABS', '260g', '2025-01-25'),
(7, 8, NULL, 1, 0, 0, 0, 0, 30, '2025-03-23 06:14:00', '2025-03-28 13:41:34', NULL, 'Nendoroid Sorasaki Hina #2727 từ \"Blue Archive\" là phiên bản Surprise trong dòng Nendoroid nổi tiếng của Good Smile Company. Với chiều cao khoảng 100mm, sản phẩm mang đến hình ảnh Sorasaki Hina trong đồng phục học sinh, kèm theo các phụ kiện như súng, sách, và nhiều biểu cảm khuôn mặt thay đổi được. Được làm từ PVC và ABS, mô hình có khớp nối linh hoạt, cho phép người chơi tạo nhiều tư thế khác nhau. Đây là lựa chọn tuyệt vời cho fan của game mobile Blue Archive.', 0, 3.6, NULL, NULL, '100mm', 'Surprise', 'PVC, ABS', '120g', '2025-03-10'),
(8, 9, NULL, 0, 1, 0, 0, 0, 4, '2025-03-23 06:52:41', '2025-03-28 13:41:34', NULL, 'Mô hình Frieren Blow Kiss Ver. POP UP PARADE SP từ \"Frieren: Beyond Journey\'s End\" là phiên bản đặc biệt với chiều cao khoảng 170mm. Sản phẩm tái hiện nhân vật Frieren trong tư thế thổi nụ hôn, với bộ trang phục pháp sư quen thuộc và mái tóc dài trắng muốt. Được làm từ PVC và ABS, mô hình có lớp sơn mượt mà, chi tiết áo choàng được khắc họa tinh tế, đi kèm đế đứng đơn giản. Đây là món quà lý tưởng cho fan của series fantasy đầy cảm xúc này.', 0, 4.2, NULL, NULL, '170mm', 'SP', 'PVC, ABS', '240g', '2025-02-28'),
(9, 10, NULL, 1, 0, 1, 0, 0, 20, '2025-03-23 07:01:50', '2025-03-28 13:41:34', NULL, 'Nendoroid Robin #2694 từ \"Honkai: Star Rail\" thuộc phiên bản Dolly, do Good Smile Company sản xuất. Với chiều cao khoảng 100mm, mô hình tái hiện nhân vật Robin trong trang phục ca sĩ lộng lẫy, kèm theo micro, cánh thiên thần nhỏ, và các biểu cảm dễ thương. Được làm từ PVC và ABS, sản phẩm có khớp nối linh hoạt và phụ kiện đa dạng, phù hợp để tạo dáng hoặc trưng bày. Đây là món đồ sưu tầm không thể thiếu cho fan của tựa game đình đám này.', 0, 4.2, NULL, NULL, '100mm', 'Dolly', 'PVC, ABS', '130g', '2025-03-20'),
(10, 11, NULL, 1, 0, 1, 0, 0, 4, '2025-03-23 07:01:50', '2025-03-28 13:41:34', NULL, 'Mô hình POP UP PARADE Yuno Gasai kích thước L từ \"Future Diary\" (tựa Nhật: Mirai Nikki) mang đến hình ảnh nhân vật Yuno trong bộ đồng phục học sinh đầy ám ảnh. Với chiều cao khoảng 180mm, sản phẩm được làm từ PVC và ABS, nổi bật với mái tóc hồng dài và ánh mắt bí ẩn. Đế đứng được thiết kế tối giản, giúp làm nổi bật vẻ đẹp đầy cá tính của nhân vật. Đây là lựa chọn tuyệt vời cho fan của series tâm lý kịch tính này.', 0, 4.4, NULL, NULL, '180mm', 'L size', 'PVC, ABS', '230g', '2025-02-15'),
(11, 12, NULL, 0, 1, 0, 0, 0, 2, '2025-03-23 07:01:50', '2025-03-31 12:35:00', NULL, 'Mô hình Frieren Silly Face từ \"Frieren: Beyond Journey\'s End\" là một sản phẩm độc đáo với chiều cao khoảng 160mm. Được làm từ PVC, mô hình tái hiện nhân vật Frieren trong biểu cảm hài hước hiếm thấy, với bộ trang phục pháp sư đơn giản và mái tóc trắng đặc trưng. Sản phẩm đi kèm đế đứng nhỏ gọn, phù hợp để trưng bày trên bàn làm việc hoặc kệ sưu tầm. Đây là phiên bản vui nhộn dành cho fan yêu thích sự đa dạng trong phong cách của Frieren.', 0, 0.0, NULL, NULL, '160mm', NULL, 'PVC', '200g', '2025-03-05'),
(12, 13, NULL, 1, 0, 1, 0, 0, 15, '2025-03-23 07:01:50', '2025-03-28 13:41:34', NULL, 'Mô hình Nishiki Kope tỷ lệ 1/6 là sản phẩm cao cấp với chiều cao khoảng 290mm, tái hiện một nhân vật gốc đầy sáng tạo. Được làm từ PVC và ABS, mô hình nổi bật với bộ trang phục phức tạp, các chi tiết hoa văn tinh xảo trên áo, và mái tóc được điêu khắc tỉ mỉ. Sản phẩm đi kèm đế đứng mô phỏng nền đá, mang lại cảm giác huyền bí. Đây là món đồ sưu tầm dành cho những ai yêu thích các thiết kế độc đáo và chi tiết.', 0, 3.8, NULL, NULL, '290mm', 'Scale 1/6', 'PVC, ABS', '480g', '2025-02-25'),
(13, 14, NULL, 1, 0, 1, 0, 0, 11, '2025-03-23 07:01:50', '2025-03-28 13:41:34', NULL, 'Mô hình POP UP PARADE Hojo Tokiyuki kích thước L từ \"The Elusive Samurai\" mang đến hình ảnh nhân vật chính trong bộ trang phục samurai truyền thống. Với chiều cao khoảng 185mm, sản phẩm được làm từ PVC và ABS, tái hiện Hojo Tokiyuki với thanh kiếm và ánh mắt kiên định. Đế đứng đơn giản giúp nổi bật vẻ đẹp của nhân vật. Đây là món đồ lý tưởng cho fan của series lịch sử đầy kịch tính này.', 0, 3.8, NULL, NULL, '185mm', 'L size', 'PVC, ABS', '240g', '2025-03-12'),
(14, 15, NULL, 0, 1, 0, 0, 0, 5, '2025-03-23 07:01:50', '2025-03-28 13:41:34', NULL, 'Nendoroid Ayase Saki #2593 từ \"Blue Archive\" thuộc phiên bản More, với chiều cao khoảng 100mm. Sản phẩm tái hiện nhân vật Ayase Saki trong đồng phục học sinh, kèm theo súng ngắn và các phụ kiện như sách vở, cùng nhiều biểu cảm thay đổi được. Được làm từ PVC và ABS, mô hình có khớp nối linh hoạt, phù hợp để tạo dáng hoặc trưng bày. Đây là món đồ sưu tầm tuyệt vời cho fan của game mobile này.', 0, 4.8, NULL, NULL, '100mm', 'More', 'PVC, ABS', '125g', '2025-03-18'),
(15, 16, NULL, 1, 0, 1, 0, 0, 10, '2025-03-23 07:01:50', '2025-03-28 13:41:34', NULL, 'Mô hình POP UP PARADE Rem kích thước L từ \"Re:Zero - Starting Life in Another World\" tái hiện nhân vật Rem trong bộ trang phục hầu gái đặc trưng. Với chiều cao khoảng 180mm, sản phẩm được làm từ PVC và ABS, nổi bật với mái tóc xanh dương và biểu cảm dịu dàng. Đế đứng đơn giản giúp làm nổi bật vẻ đẹp thanh thoát của nhân vật. Đây là món đồ không thể thiếu cho fan của series nổi tiếng này.', 0, 4.7, NULL, NULL, '180mm', 'L size', 'PVC, ABS', '230g', '2025-02-22'),
(16, 17, NULL, 1, 0, 1, 0, 0, 5, '2025-03-23 07:03:25', '2025-03-28 13:41:34', NULL, 'Mô hình Kana Arima Date Style Ver tỷ lệ 1/6 từ \"Oshi no Ko\" là sản phẩm cao cấp với chiều cao khoảng 270mm. Được làm từ PVC và ABS, mô hình tái hiện Kana Arima trong bộ trang phục hẹn hò dễ thương, với mái tóc hồng ngắn và biểu cảm tinh nghịch. Sản phẩm đi kèm phụ kiện như túi xách nhỏ và đế đứng mô phỏng đường phố, mang lại cảm giác sống động. Đây là lựa chọn tuyệt vời cho fan của series đầy drama này.', 0, 4.9, NULL, NULL, '270mm', 'Scale 1/6', 'PVC, ABS', '460g', '2025-03-25'),
(17, 18, NULL, 0, 1, 0, 0, 0, 14, '2025-03-23 07:11:12', '2025-03-28 13:41:34', NULL, 'Mô hình POP UP PARADE Alpha SP từ \"The Eminence in Shadow\" là phiên bản đặc biệt với chiều cao khoảng 175mm. Sản phẩm tái hiện nhân vật Alpha trong bộ trang phục chiến đấu bó sát, với mái tóc vàng óng ánh và ánh mắt sắc lạnh. Được làm từ PVC và ABS, mô hình có lớp sơn mịn màng và chi tiết áo giáp được khắc họa tinh tế, đi kèm đế đứng tối giản. Đây là món đồ sưu tầm dành cho fan của series hài hước nhưng đầy kịch tính này.', 0, 3.3, NULL, NULL, '175mm', 'SP', 'PVC, ABS', '250g', '2025-03-08'),
(18, 19, NULL, 1, 0, 1, 0, 0, 8, '2025-03-23 07:11:12', '2025-03-28 13:41:34', NULL, 'Mô hình Shizuku Kuroe Cosplay by Marin tỷ lệ 1/6 từ \"My Dress-Up Darling\" tái hiện nhân vật Shizuku trong bộ trang phục cosplay quyến rũ. Với chiều cao khoảng 280mm, sản phẩm được làm từ PVC và ABS, nổi bật với lớp vải áo mỏng manh, mái tóc đen dài, và biểu cảm bí ẩn. Đi kèm phụ kiện như đôi giày cao gót và đế đứng mô phỏng sân khấu, mô hình này mang đến trải nghiệm trưng bày đầy ấn tượng cho fan của series.', 0, 4.5, NULL, NULL, '280mm', 'Scale 1/6', 'PVC, ABS', '450g', '2025-02-10'),
(19, 20, NULL, 1, 0, 1, 0, 0, 6, '2025-03-23 07:11:12', '2025-03-28 13:41:34', NULL, 'Mô hình POP UP PARADE Shujinkou Joker kích thước L từ \"Persona 5\" tái hiện nhân vật chính trong bộ trang phục Phantom Thief đầy phong cách. Với chiều cao khoảng 180mm, sản phẩm được làm từ PVC và ABS, nổi bật với chiếc mặt nạ đỏ và mái tóc đen rối. Đế đứng đơn giản giúp làm nổi bật vẻ đẹp của nhân vật. Đây là món đồ sưu tầm lý tưởng cho fan của tựa game nhập vai nổi tiếng này.', 0, 4.6, NULL, NULL, '180mm', 'L size', 'PVC, ABS', '240g', '2025-03-15'),
(20, 21, NULL, 0, 1, 0, 0, 0, 16, '2025-03-23 07:11:12', '2025-03-28 13:41:34', NULL, 'Mô hình POP UP PARADE Liliel 3rd Squad Outfit Ver kích thước L là sản phẩm độc đáo với chiều cao khoảng 185mm. Được làm từ PVC và ABS, mô hình tái hiện nhân vật Liliel trong bộ trang phục đội quân thứ 3, với mái tóc ngắn và ánh mắt quyết đoán. Sản phẩm đi kèm đế đứng đơn giản, phù hợp để trưng bày trong bộ sưu tập. Đây là lựa chọn tuyệt vời cho những ai yêu thích các thiết kế quân sự trong anime.', 0, 4.1, NULL, NULL, '185mm', 'L size', 'PVC, ABS', '250g', '2025-03-20'),
(21, 22, NULL, 1, 0, 1, 0, 0, 4, '2025-03-23 07:11:12', '2025-03-28 13:41:34', NULL, 'Mô hình POP UP PARADE Annie Leonhart Female Titan Ver kích thước XL từ \"Attack on Titan\" mang đến hình ảnh Annie trong dạng Titan Nữ đầy uy lực. Với chiều cao khoảng 220mm, sản phẩm được làm từ PVC và ABS, tái hiện cơ bắp rắn chắc và mái tóc vàng đặc trưng. Đế đứng được thiết kế mô phỏng mặt đất vỡ vụn, tăng thêm phần kịch tính. Đây là món đồ sưu tầm không thể bỏ qua cho fan của series hành động nổi tiếng này.', 0, 3.5, NULL, NULL, '220mm', 'XL size', 'PVC, ABS', '350g', '2025-03-10'),
(22, 23, NULL, 1, 0, 1, 0, 0, 5, '2025-03-23 07:11:12', '2025-03-28 13:41:34', NULL, 'Nendoroid Zhongli #2582 từ \"Genshin Impact\" thuộc phiên bản EZ, với chiều cao khoảng 100mm. Sản phẩm tái hiện nhân vật Zhongli trong bộ trang phục quý tộc, kèm theo cây thương và các biểu cảm thay đổi được như nghiêm nghị, mỉm cười. Được làm từ PVC và ABS, mô hình có khớp nối linh hoạt và phụ kiện đa dạng, phù hợp để tạo dáng. Đây là món đồ sưu tầm dành cho fan của tựa game phiêu lưu thế giới mở này.', 0, 3.1, NULL, NULL, '100mm', 'EZ', 'PVC, ABS', '130g', '2025-03-22'),
(23, 24, NULL, 0, 1, 0, 0, 0, 20, '2025-03-23 07:11:12', '2025-03-28 13:41:34', NULL, 'Mô hình Utaha Kasumigaoka Animation Ver tỷ lệ 1/4 từ \"Saekano: How to Raise a Boring Girlfriend\" là sản phẩm cao cấp với chiều cao khoảng 400mm. Được làm từ PVC và ABS, mô hình tái hiện Utaha trong bộ trang phục nữ sinh thanh lịch, với mái tóc đen dài và ánh mắt quyến rũ. Sản phẩm đi kèm đế đứng lớn mô phỏng sàn gỗ, mang lại cảm giác sang trọng. Đây là lựa chọn tuyệt vời cho fan của series lãng mạn này.', 0, 4.1, NULL, NULL, '400mm', 'Scale 1/4', 'PVC, ABS', '600g', '2025-03-05'),
(24, 25, 'Asset/Images/Product-detail/detail-1-1.webp', 0, 0, 1, 1, 0, 40, '2025-03-24 08:53:36', '2025-03-29 02:55:01', '2025-05-20 15:49:30', 'Mô hình POP UP PARADE Chinatsu Kano L Size từ series \"Blue Box\" (tựa Nhật: Ao no Hako) là sản phẩm nổi bật của Good Smile Company. Với chiều cao khoảng 210mm, mô hình tái hiện nhân vật Chinatsu Kano trong bộ đồ thể thao năng động, với mái tóc nâu buộc cao và biểu cảm tự tin. Được làm từ PVC và ABS cao cấp, sản phẩm có lớp sơn mịn màng, chi tiết quần áo được khắc họa tỉ mỉ, đi kèm đế đứng chắc chắn mô phỏng sân bóng rổ. Đây là món đồ sưu tầm lý tưởng cho fan của series thể thao lãng mạn này, mang đến cảm giác tươi mới và tràn đầy năng lượng.', 5, NULL, 40, '2025-03-10 03:00:00', '210mm', 'L size', 'PVC, ABS', '260g', '2025-03-15'),
(25, 26, NULL, 0, 0, 0, 1, 0, 22, '2025-03-24 08:53:36', '2025-03-28 13:41:34', '2025-04-22 15:49:30', 'Mô hình Hatsune Miku 0x27 Eternal Stream tỷ lệ 1/4 từ series \"Vocaloid\" là một kiệt tác của Good Smile Company. Với kích thước ấn tượng (chiều cao 412mm, chiều rộng 472mm, chiều dài 180mm), sản phẩm tái hiện Hatsune Miku trong bộ trang phục Eternal Stream đầy bay bổng, với mái tóc xanh dài tung bay và đôi mắt lấp lánh. Được làm từ PVC và ABS, mô hình có lớp sơn bóng bẩy, chi tiết váy được thiết kế tinh xảo với hiệu ứng trong suốt, đi kèm đế đứng lớn mô phỏng sân khấu ánh sáng. Đây là món đồ sưu tầm cao cấp dành cho fan của Vocaloid và những người yêu thích nghệ thuật mô hình.', 5, 4.7, 2, '2025-03-23 03:00:00', '412mm', 'Scale 1/4', 'PVC, ABS', '850g', '2025-03-25'),
(26, 27, 'Asset/Images/Products/new1-2.webp', 1, 0, 0, 0, 0, 25, '2025-03-24 20:42:46', '2025-03-28 13:41:34', NULL, 'Mô hình Tokisaki Kurumi Zafkiel từ \"Date A Live\" là sản phẩm với chiều cao khoảng 230mm, tái hiện nhân vật Kurumi trong bộ trang phục gothic đặc trưng. Được làm từ PVC, mô hình nổi bật với mái tóc đen buộc lệch, chiếc đồng hồ Zafkiel trên tay, và ánh mắt bí ẩn. Sản phẩm đi kèm đế đứng mô phỏng mặt đất nứt vỡ, tăng thêm phần kịch tính. Đây là lựa chọn tuyệt vời cho fan của series hành động pha lẫn yếu tố siêu nhiên này.', 10, 3.3, NULL, NULL, '230mm', NULL, 'PVC', '220g', '2025-03-01'),
(27, 28, 'Asset/Images/Products/new2-2.webp', 1, 0, 0, 0, 0, 15, '2025-03-24 20:42:46', '2025-03-28 13:41:34', NULL, 'Mô hình Hatsune Miku BiCute Bunnies rurudo ver. từ \"Vocaloid\" là sản phẩm dễ thương với chiều cao khoảng 300mm. Được làm từ PVC, mô hình tái hiện Hatsune Miku trong bộ trang phục thỏ trắng hồng, với đôi tai dài và mái tóc xanh đặc trưng. Lớp sơn mịn màng và chi tiết váy được khắc họa tinh tế, đi kèm đế đứng đơn giản. Đây là món đồ sưu tầm lý tưởng cho fan của Miku và những ai yêu thích phong cách kawaii.', 15, 2.2, NULL, NULL, '300mm', NULL, 'PVC', '250g', '2025-03-10'),
(28, 29, 'Asset/Images/Products/new3-2.webp', 0, 0, 0, 0, 0, 8, '2025-03-24 20:42:46', '2025-03-28 13:41:34', NULL, 'Mô hình Anya Forger Natsuyasumi Luminasta từ \"Spy x Family\" mang đến hình ảnh Anya trong bộ đồ mùa hè đáng yêu. Với chiều cao khoảng 180mm, sản phẩm được làm từ PVC, tái hiện nhân vật với nụ cười rạng rỡ, mái tóc hồng ngắn, và chiếc mũ rơm nhỏ. Đế đứng mô phỏng bãi cát, đi kèm phụ kiện như xô nước, mang lại cảm giác vui tươi. Đây là món đồ sưu tầm hoàn hảo cho fan của series hài hước này.', 8, 3.9, NULL, NULL, '180mm', NULL, 'PVC', '180g', '2025-03-15'),
(29, 30, 'Asset/Images/Products/new4-2.webp', 0, 0, 0, 0, 1, 15, '2025-03-24 20:42:46', '2025-03-28 13:41:34', NULL, 'Mô hình Elaina Trio-Try-iT từ \"Wandering Witch: The Journey of Elaina\" tái hiện nhân vật Elaina trong bộ trang phục phù thủy thanh lịch. Với chiều cao khoảng 200mm, sản phẩm được làm từ PVC, nổi bật với mái tóc xám tro và chiếc mũ rộng vành. Đế đứng đơn giản giúp làm nổi bật vẻ đẹp của nhân vật, đi kèm phụ kiện như cây chổi nhỏ. Đây là món đồ sưu tầm dành cho fan của series phiêu lưu kỳ thú này.', 3, 3.1, NULL, NULL, '200mm', NULL, 'PVC', '200g', '2025-03-20'),
(30, 31, 'Asset/Images/Products/new5-2.jpg', 0, 0, 0, 0, 1, 12, '2025-03-24 20:42:46', '2025-03-28 13:41:34', NULL, 'Mô hình Play with Paint Vol.1 Kaga Sumire là sản phẩm độc đáo với chiều cao khoảng 220mm. Được làm từ PVC, mô hình tái hiện nhân vật Kaga Sumire trong bộ trang phục nghệ sĩ, với bảng màu và cọ vẽ trên tay. Lớp sơn mịn màng và chi tiết quần áo được khắc họa tinh tế, đi kèm đế đứng mô phỏng sàn gỗ. Đây là lựa chọn tuyệt vời cho những ai yêu thích sự sáng tạo trong thiết kế mô hình.', 3, 4.7, NULL, NULL, '220mm', NULL, 'PVC', '230g', '2025-03-25'),
(31, 32, 'Asset/Images/Products/new6-2.webp', 0, 0, 0, 0, 1, 6, '2025-03-24 20:42:46', '2025-03-28 13:41:34', NULL, 'Mô hình Nakano Yotsuba Marine Look Trio-Try-iT từ \"The Quintessential Quintuplets\" tái hiện Yotsuba trong bộ trang phục thủy thủ mùa hè. Với chiều cao khoảng 210mm, sản phẩm được làm từ PVC, nổi bật với mái tóc cam ngắn và nụ cười rạng rỡ. Đế đứng đơn giản, đi kèm phụ kiện như túi đeo vai, mang lại cảm giác năng động. Đây là món đồ sưu tầm dành cho fan của series lãng mạn này.', 1, 3.3, NULL, NULL, '210mm', NULL, 'PVC', '190g', '2025-03-28'),
(32, 33, 'Asset/Images/Products/new7-2.webp', 0, 0, 0, 0, 0, 50, '2025-03-24 20:42:46', '2025-03-30 11:54:53', NULL, 'Gấu bông Kigurumi Plushie Noko Shikanoko từ \"My Deer Friend Nokotan\" là sản phẩm nhồi bông với chiều cao khoảng 250mm. Được làm từ polyester mềm mại, gấu bông tái hiện nhân vật Noko trong bộ đồ kigurumi hình hươu, với đôi sừng nhỏ và đôi mắt to tròn. Thiết kế dễ thương, phù hợp làm quà tặng hoặc vật trang trí cho fan của series hài hước này.', 20, 3.7, NULL, NULL, '250mm', NULL, 'Polyester', '220g', '2025-03-30'),
(33, 34, 'Asset/Images/Products/new8-2.webp', 0, 0, 0, 0, 1, 13, '2025-03-24 20:42:46', '2025-03-28 13:41:34', NULL, 'Mô hình Formidable tỷ lệ 1/7 Still Illustration Ver từ \"Azur Lane\" là sản phẩm cao cấp với chiều cao khoảng 250mm. Được làm từ PVC và ABS, mô hình tái hiện Formidable trong bộ trang phục quý tộc Anh, với mái tóc vàng óng ánh và ánh mắt kiêu sa. Sản phẩm đi kèm đế đứng lớn mô phỏng sàn đá cẩm thạch, mang lại cảm giác sang trọng. Đây là món đồ sưu tầm dành cho fan của game mobile này.', 3, 0.2, NULL, NULL, '250mm', 'Scale 1/7', 'PVC, ABS', '450g', '2025-04-01'),
(34, 35, 'Asset/Images/Products/new9-2.webp', 0, 0, 0, 0, 1, 10, '2025-03-24 20:42:46', '2025-03-28 13:41:34', NULL, 'Mô hình Ui Swimsuit tỷ lệ 1/7 từ \"Blue Archive\" tái hiện nhân vật Ui trong bộ đồ bơi mùa hè quyến rũ. Với chiều cao khoảng 240mm, sản phẩm được làm từ PVC và ABS, nổi bật với mái tóc dài và làn da trắng mịn. Đế đứng mô phỏng bãi biển, đi kèm phụ kiện như khăn tắm, mang lại cảm giác thư giãn. Đây là món đồ sưu tầm lý tưởng cho fan của game mobile này.', 10, 3.0, NULL, NULL, '240mm', 'Scale 1/7', 'PVC, ABS', '420g', '2025-04-05'),
(35, 36, 'Asset/Images/Products/new10-2.webp', 0, 0, 0, 0, 1, 8, '2025-03-24 20:42:46', '2025-03-28 13:41:34', NULL, 'Mô hình Houshou Marine tỷ lệ 1/7 từ Hololive là sản phẩm cao cấp với chiều cao khoảng 260mm. Được làm từ PVC và ABS, mô hình tái hiện Houshou Marine trong bộ trang phục thuyền trưởng quyến rũ, với mái tóc đỏ rực và ánh mắt tinh nghịch. Sản phẩm đi kèm đế đứng mô phỏng boong tàu, mang lại cảm giác phiêu lưu. Đây là món đồ sưu tầm dành cho fan của VTuber nổi tiếng này.', 2, 4.1, NULL, NULL, '260mm', 'Scale 1/7', 'PVC, ABS', '430g', '2025-04-10'),
(36, 37, 'Asset/Images/Products/new11-2.webp', 0, 0, 0, 0, 1, 15, '2025-03-24 20:42:46', '2025-03-28 13:41:34', NULL, 'Mô hình Tenitol Tall Nachoneko là sản phẩm độc đáo với chiều cao khoảng 220mm. Được làm từ PVC, mô hình tái hiện nhân vật Nachoneko trong bộ trang phục mèo dễ thương, với đôi tai lớn và đuôi dài. Lớp sơn mịn màng và chi tiết quần áo được khắc họa tinh tế, đi kèm đế đứng nhỏ gọn. Đây là lựa chọn tuyệt vời cho những ai yêu thích phong cách kawaii trong mô hình.', 4, 3.5, NULL, NULL, '220mm', NULL, 'PVC', '200g', '2025-04-15'),
(37, 38, 'Asset/Images/Products/new12-2.webp', 0, 0, 0, 0, 1, 17, '2025-03-24 20:42:46', '2025-03-28 13:41:34', NULL, 'Mô hình POP UP PARADE Izayoi Nonomi Mischievous☆Straight Ver kích thước L từ \"Blue Archive\" tái hiện Nonomi trong bộ trang phục học sinh nghịch ngợm. Với chiều cao khoảng 185mm, sản phẩm được làm từ PVC và ABS, nổi bật với mái tóc vàng và nụ cười tinh nghịch. Đế đứng đơn giản, đi kèm phụ kiện như túi sách, mang lại cảm giác sống động. Đây là món đồ sưu tầm dành cho fan của game mobile này.', 7, 4.1, NULL, NULL, '185mm', 'L size', 'PVC, ABS', '240g', '2025-04-20'),
(38, 39, NULL, 0, 0, 0, 0, 1, 10, '2025-03-28 00:00:00', '2025-03-28 13:41:34', NULL, 'Mô hình Racing EL FAIL tỷ lệ 1/7 từ \"DJMAX RESPECT V\" là sản phẩm cao cấp với chiều cao khoảng 250mm. Được làm từ PVC và ABS, mô hình tái hiện nhân vật EL FAIL trong bộ đồ đua xe năng động, với mái tóc tím và ánh mắt quyết đoán. Sản phẩm đi kèm đế đứng mô phỏng đường đua, mang lại cảm giác tốc độ. Đây là món đồ sưu tầm dành cho fan của game âm nhạc này.', 2, 3.6, NULL, NULL, '250mm', 'Scale 1/7', 'PVC, ABS', '400g', '2025-04-25'),
(39, 40, NULL, 0, 0, 0, 0, 1, 8, '2025-03-28 00:00:00', '2025-03-28 13:41:34', NULL, 'Mô hình Misae Suzuhara Bunny Ver. 2nd tỷ lệ 1/4 là sản phẩm gốc của RAITA, với chiều cao khoảng 420mm. Được làm từ PVC và ABS, mô hình tái hiện Misae trong bộ đồ thỏ gợi cảm, với mái tóc dài và đôi tai lớn. Sản phẩm đi kèm đế đứng lớn mô phỏng sàn gỗ, mang lại cảm giác sang trọng. Đây là món đồ sưu tầm cao cấp dành cho những ai yêu thích thiết kế nhân vật gốc.', 1, 0.5, NULL, NULL, '420mm', 'Scale 1/4', 'PVC, ABS', '650g', '2025-05-01'),
(40, 41, NULL, 0, 0, 0, 0, 1, 12, '2025-03-28 00:00:00', '2025-03-28 13:41:34', NULL, 'Mô hình Nakano Nino Bunny Ver. Desktop Cute từ \"The Quintessential Quintuplets\" tái hiện Nino trong bộ đồ thỏ dễ thương. Với chiều cao khoảng 200mm, sản phẩm được làm từ PVC, nổi bật với mái tóc hồng ngắn và nụ cười rạng rỡ. Đế đứng nhỏ gọn, đi kèm phụ kiện như khay đồ uống, mang lại cảm giác sinh động. Đây là món đồ sưu tầm dành cho fan của series lãng mạn này.', 3, 4.1, NULL, NULL, '200mm', NULL, 'PVC', '220g', '2025-05-05'),
(41, 42, NULL, 0, 0, 0, 0, 1, 15, '2025-03-28 00:00:00', '2025-03-28 13:41:34', NULL, 'Mô hình Ayanami Rei ~tentative name~ tỷ lệ 1/7 từ \"Evangelion Shin Gekijouban\" là sản phẩm cao cấp với chiều cao khoảng 230mm. Được làm từ PVC và ABS, mô hình tái hiện Rei trong bộ plugsuit trắng, với mái tóc xanh ngắn và ánh mắt tĩnh lặng. Sản phẩm đi kèm đế đứng mô phỏng buồng lái EVA, mang lại cảm giác chân thực. Đây là món đồ sưu tầm dành cho fan của series kinh điển này.', 4, 4.4, NULL, NULL, '230mm', 'Scale 1/7', 'PVC, ABS', '400g', '2025-05-10'),
(42, 43, NULL, 0, 0, 0, 0, 1, 10, '2025-03-28 00:00:00', '2025-03-28 13:41:34', NULL, 'Mô hình Foreigner/Katsushika Hokusai: Travel Portrait Ver tỷ lệ 1/7 từ \"Fate/Grand Order\" tái hiện Hokusai trong bộ kimono truyền thống. Với chiều cao khoảng 260mm, sản phẩm được làm từ PVC và ABS, nổi bật với mái tóc đen dài và cây cọ lớn. Đế đứng mô phỏng cảnh biển, đi kèm phụ kiện như cuộn tranh, mang lại cảm giác nghệ thuật. Đây là món đồ sưu tầm dành cho fan của game mobile này.', 2, 3.2, NULL, NULL, '260mm', 'Scale 1/7', 'PVC, ABS', '420g', '2025-05-15'),
(43, 44, NULL, 0, 0, 0, 0, 1, 20, '2025-03-28 00:00:00', '2025-03-28 13:41:34', NULL, 'Mô hình Mahiru Shiina tỷ lệ 1/7 từ \"The Angel Next Door Spoils Me Rotten\" bởi F:Nex tái hiện Mahiru trong bộ đồng phục học sinh thanh lịch. Với chiều cao khoảng 240mm, sản phẩm được làm từ PVC và ABS, nổi bật với mái tóc vàng óng ánh và nụ cười dịu dàng. Đế đứng mô phỏng sàn gỗ, đi kèm phụ kiện như sách vở, mang lại cảm giác ấm áp. Đây là món đồ sưu tầm dành cho fan của series lãng mạn này.', 5, 4.9, NULL, NULL, '240mm', 'Scale 1/7', 'PVC, ABS', '400g', '2025-05-20'),
(44, 45, NULL, 0, 0, 0, 0, 1, 25, '2025-03-28 00:00:00', '2025-03-28 13:41:34', NULL, 'Mô hình 753 Plain Clothes Ver Glitter & Glamours là sản phẩm với chiều cao khoảng 180mm. Được làm từ PVC, mô hình tái hiện nhân vật trong bộ trang phục thường ngày giản dị, với mái tóc dài và ánh mắt dịu dàng. Lớp sơn bóng bẩy và chi tiết quần áo được khắc họa tinh tế, đi kèm đế đứng đơn giản. Đây là lựa chọn tuyệt vời cho những ai yêu thích sự thanh lịch trong thiết kế mô hình.', 6, 3.5, NULL, NULL, '180mm', NULL, 'PVC', '190g', '2025-05-25'),
(45, 46, NULL, 0, 0, 0, 0, 1, 30, '2025-03-28 00:00:00', '2025-03-28 13:41:34', NULL, 'Mô hình Hoshino Ai Espresto Excite Motions từ \"Oshi no Ko\" tái hiện Ai trong bộ trang phục idol lộng lẫy. Với chiều cao khoảng 200mm, sản phẩm được làm từ PVC, nổi bật với mái tóc tím dài và nụ cười rạng rỡ. Đế đứng mô phỏng sân khấu, đi kèm phụ kiện như micro, mang lại cảm giác sống động. Đây là món đồ sưu tầm dành cho fan của series drama này.', 8, 3.2, NULL, NULL, '200mm', NULL, 'PVC', '210g', '2025-05-30'),
(46, 47, NULL, 0, 0, 0, 0, 1, 15, '2025-03-28 00:00:00', '2025-03-28 13:41:34', NULL, 'Mô hình Hatsune Miku Aqua Float Girls từ \"Piapro Characters\" tái hiện Miku trong bộ đồ bơi mùa hè dễ thương. Với chiều cao khoảng 180mm, sản phẩm được làm từ PVC, nổi bật với mái tóc xanh dài và phao bơi nhỏ. Đế đứng mô phỏng mặt nước, mang lại cảm giác thư giãn. Đây là món đồ sưu tầm dành cho fan của Vocaloid và phong cách mùa hè.', 3, 3.2, NULL, NULL, '180mm', NULL, 'PVC', '190g', '2025-06-01'),
(47, 48, NULL, 0, 0, 0, 0, 1, 10, '2025-03-28 00:00:00', '2025-03-28 13:41:34', NULL, 'Mô hình Biya Yuna Bunny Girl Ver tỷ lệ 1/4 là sản phẩm gốc với chiều cao khoảng 400mm. Được làm từ PVC và ABS, mô hình tái hiện Biya Yuna trong bộ đồ thỏ gợi cảm, với mái tóc dài và đôi tai lớn. Sản phẩm đi kèm đế đứng lớn mô phỏng sàn đá cẩm thạch, mang lại cảm giác sang trọng. Đây là món đồ sưu tầm cao cấp dành cho những ai yêu thích thiết kế nhân vật gốc.', 2, 4.7, NULL, NULL, '400mm', 'Scale 1/4', 'PVC, ABS', '600g', '2025-06-05'),
(48, 49, NULL, 0, 0, 0, 0, 1, 20, '2025-03-28 00:00:00', '2025-03-28 13:41:34', NULL, 'Mô hình POP UP PARADE Hiiragi Utena Magia Baiser kích thước L từ \"Gushing over Magical Girls\" tái hiện Utena trong bộ trang phục ma pháp đầy quyền lực. Với chiều cao khoảng 190mm, sản phẩm được làm từ PVC và ABS, nổi bật với mái tóc tím và ánh mắt kiên định. Đế đứng đơn giản, đi kèm phụ kiện như cây đũa phép, mang lại cảm giác huyền bí. Đây là món đồ sưu tầm dành cho fan của series này.', 5, 3.2, NULL, NULL, '190mm', 'L size', 'PVC, ABS', '250g', '2025-06-10'),
(49, 50, NULL, 0, 0, 0, 0, 1, 15, '2025-03-28 00:00:00', '2025-03-28 13:41:34', NULL, 'Mô hình Kurosaki Ichigo Figuarts ZERO từ \"Bleach: Sennen Kessen-hen\" tái hiện Ichigo trong tư thế chiến đấu đầy mạnh mẽ. Với chiều cao khoảng 250mm, sản phẩm được làm từ PVC, nổi bật với thanh kiếm Zangetsu và mái tóc cam rực rỡ. Đế đứng mô phỏng hiệu ứng khói bụi, mang lại cảm giác kịch tính. Đây là món đồ sưu tầm dành cho fan của series shounen kinh điển này.', 4, 3.6, NULL, NULL, '250mm', NULL, 'PVC', '230g', '2025-06-15'),
(50, 51, NULL, 0, 0, 0, 0, 1, 25, '2025-03-28 00:00:00', '2025-03-28 13:41:34', NULL, 'Mô hình POP UP PARADE Zero Two kích thước L từ \"DARLING in the FRANXX\" tái hiện Zero Two trong bộ đồng phục đỏ đặc trưng. Với chiều cao khoảng 180mm, sản phẩm được làm từ PVC và ABS, nổi bật với mái tóc hồng dài và ánh mắt kiêu ngạo. Đế đứng đơn giản, giúp làm nổi bật vẻ đẹp của nhân vật. Đây là món đồ sưu tầm dành cho fan của series mecha lãng mạn này.', 6, 4.9, NULL, NULL, '180mm', 'L size', 'PVC, ABS', '240g', '2025-06-20'),
(51, 52, NULL, 0, 0, 0, 0, 1, 12, '2025-03-28 00:00:00', '2025-03-28 13:41:34', NULL, 'Mô hình Hana Uzaki Cow Bikini tỷ lệ 1/7 từ \"Uzaki-chan wa Asobitai! 2nd Season\" tái hiện Hana trong bộ bikini họa tiết bò sữa. Với chiều cao khoảng 230mm, sản phẩm được làm từ PVC và ABS, nổi bật với mái tóc bạc ngắn và nụ cười rạng rỡ. Đế đứng mô phỏng bãi biển, đi kèm phụ kiện như kính râm, mang lại cảm giác vui tươi. Đây là món đồ sưu tầm dành cho fan của series hài hước này.', 3, 4.1, NULL, NULL, '230mm', 'Scale 1/7', 'PVC, ABS', '400g', '2025-06-25'),
(52, 53, NULL, 0, 0, 0, 0, 1, 10, '2025-03-28 00:00:00', '2025-03-28 13:41:34', NULL, 'Mô hình figma Ninomae Ina\'nis #638 từ Hololive là sản phẩm với chiều cao khoảng 150mm. Được làm từ PVC và ABS, mô hình tái hiện Ina trong bộ trang phục VTuber độc đáo, với mái tóc tím và xúc tu nhỏ. Sản phẩm có khớp nối linh hoạt, đi kèm phụ kiện như sách và bút, phù hợp để tạo dáng. Đây là món đồ sưu tầm dành cho fan của VTuber này.', 2, 3.2, NULL, NULL, '150mm', 'Figma', 'PVC, ABS', '160g', '2025-06-30'),
(53, 54, NULL, 0, 0, 0, 0, 1, 8, '2025-03-28 00:00:00', '2025-03-28 13:41:34', NULL, 'Mô hình Nakano Nino tỷ lệ 1/7 từ \"The Quintessential Quintuplets\" bởi Shibuya Scramble Figure tái hiện Nino trong bộ váy cưới lộng lẫy. Với chiều cao khoảng 250mm, sản phẩm được làm từ PVC và ABS, nổi bật với mái tóc hồng ngắn và ánh mắt dịu dàng. Đế đứng lớn mô phỏng sàn lễ đường, mang lại cảm giác lãng mạn. Đây là món đồ sưu tầm cao cấp dành cho fan của series này.', 1, 0.1, NULL, NULL, '250mm', 'Scale 1/7', 'PVC, ABS', '430g', '2025-07-05');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_images`
--

CREATE TABLE `product_images` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `product_images`
--

INSERT INTO `product_images` (`id`, `product_id`, `image_url`, `created_at`) VALUES
(8, 2, 'Asset\\Images\\Product-detail\\detail-3-2.webp', '2025-03-26 11:03:36'),
(9, 2, 'Asset\\Images\\Product-detail\\detail-3-3.webp', '2025-03-26 11:03:36'),
(10, 1, 'Asset/Images/Product-detail/detail-2-2.webp', '2025-03-28 13:28:10'),
(11, 1, 'Asset/Images/Product-detail/detail-2-3.webp', '2025-03-28 13:28:10'),
(12, 5, 'Asset/Images/Product-detail/detail-5-2.webp', '2025-03-29 00:50:20'),
(13, 5, 'Asset/Images/Product-detail/detail-5-3.webp', '2025-03-29 00:50:20');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `reviewer_name` varchar(100) NOT NULL,
  `rating` float NOT NULL,
  `review_text` text NOT NULL,
  `review_date` date NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `reviews`
--

INSERT INTO `reviews` (`id`, `product_id`, `reviewer_name`, `rating`, `review_text`, `review_date`, `user_id`) VALUES
(17, 1, 'USER 1', 3, 'đẹp', '2025-03-30', 32);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `avatar` varchar(255) DEFAULT 'avatars/default-avatar.png',
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT 'other',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `role` enum('user','admin') NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `avatar`, `full_name`, `email`, `password`, `address`, `gender`, `created_at`, `updated_at`, `role`) VALUES
(3, 'avatars/avatar_3_1743410547.gif', 'Đặng Tiến Hưng', 'hungdarlingch@gmail.com', '$2y$10$yocz1QTCRT5RBrkGvbc9we2u07ywRbHJOqBybfD/VCUkEuwhCcO4W', '', 'male', '2025-03-22 02:33:10', '2025-03-31 08:42:27', 'admin'),
(32, 'avatars/default-avatar.png', 'USER 1', 'User1@gmail.com', '$2y$10$iSZeqK9f4pHszmRph2WgturmaIXvhAv/S0H5q39EZfOF7WXTtvGg6', NULL, 'other', '2025-03-30 07:31:39', '2025-03-30 07:34:13', 'user'),
(33, 'avatars/default-avatar.png', 'USER 2', 'User2@gmail.com', '$2y$10$nHDJqU4A9WxCI1lcSRgSGOULDauA46GAqtz6kFADUeea/k2cDAq8m', NULL, 'other', '2025-03-30 07:31:57', '2025-03-30 07:31:57', 'user');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `user_cart`
--

CREATE TABLE `user_cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `user_favorites`
--

CREATE TABLE `user_favorites` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `user_favorites`
--

INSERT INTO `user_favorites` (`id`, `user_id`, `product_id`, `created_at`) VALUES
(13, 32, 2, '2025-03-31 07:29:17');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `user_vouchers`
--

CREATE TABLE `user_vouchers` (
  `user_id` int(11) NOT NULL,
  `voucher_id` int(11) NOT NULL,
  `claimed_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `user_vouchers`
--

INSERT INTO `user_vouchers` (`user_id`, `voucher_id`, `claimed_at`) VALUES
(24, 4, '2025-03-26 20:46:55'),
(24, 5, '2025-03-26 20:46:56'),
(24, 1, '2025-03-26 20:46:57'),
(25, 5, '2025-03-26 20:47:39'),
(25, 4, '2025-03-26 20:47:40'),
(3, 5, '2025-03-27 04:06:35'),
(3, 3, '2025-04-02 13:51:54'),
(3, 2, '2025-04-02 13:51:51'),
(34, 2, '2025-03-30 07:38:05'),
(34, 5, '2025-03-30 07:38:06');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `vouchers`
--

CREATE TABLE `vouchers` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `discount` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `min_order_value` int(11) DEFAULT 0,
  `expiry_date` date NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `vouchers`
--

INSERT INTO `vouchers` (`id`, `code`, `discount`, `description`, `image`, `min_order_value`, `expiry_date`, `status`) VALUES
(1, 'SAVE10', 10, 'Giảm 10% cho đơn hàng trên 200k', '', 200000, '2025-04-30', 'active'),
(3, 'WELCOME20', 20, 'Giảm 20% cho khách hàng mới', NULL, 0, '2025-05-15', 'active'),
(4, 'SALE30', 30, 'Giảm 30% cho đơn hàng trên 1 triệu', NULL, 1000000, '2025-04-15', 'active'),
(2, 'FIGURETHANG4', 10, 'THÁNG TƯ LÀ NGÀY GIẢM GIÁ CỦA EM', NULL, 100000, '2025-04-30', 'active');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `product_details`
--
ALTER TABLE `product_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_details_ibfk_1` (`product_id`);

--
-- Chỉ mục cho bảng `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `fk_user_id` (`user_id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Chỉ mục cho bảng `user_cart`
--
ALTER TABLE `user_cart`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_cart` (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `user_favorites`
--
ALTER TABLE `user_favorites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_favorite` (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `user_vouchers`
--
ALTER TABLE `user_vouchers`
  ADD PRIMARY KEY (`user_id`,`voucher_id`),
  ADD KEY `voucher_id` (`voucher_id`);

--
-- Chỉ mục cho bảng `vouchers`
--
ALTER TABLE `vouchers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT cho bảng `product_details`
--
ALTER TABLE `product_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=93;

--
-- AUTO_INCREMENT cho bảng `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT cho bảng `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT cho bảng `user_cart`
--
ALTER TABLE `user_cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `user_favorites`
--
ALTER TABLE `user_favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT cho bảng `vouchers`
--
ALTER TABLE `vouchers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `product_details`
--
ALTER TABLE `product_details`
  ADD CONSTRAINT `product_details_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Các ràng buộc cho bảng `user_cart`
--
ALTER TABLE `user_cart`
  ADD CONSTRAINT `user_cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `user_cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Các ràng buộc cho bảng `user_favorites`
--
ALTER TABLE `user_favorites`
  ADD CONSTRAINT `user_favorites_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_favorites_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
