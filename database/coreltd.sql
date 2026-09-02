-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 02, 2026 at 05:27 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `coreltd`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `action` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin_notifications`
--

CREATE TABLE `admin_notifications` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `type` varchar(50) NOT NULL DEFAULT 'general',
  `title` varchar(150) NOT NULL,
  `message` text DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `read_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_notifications`
--

INSERT INTO `admin_notifications` (`id`, `order_id`, `type`, `title`, `message`, `is_read`, `read_at`, `created_at`) VALUES
(1, 11, 'new_order', 'New Order Received', 'Order #CORE-20260811-DBC30F from ALEX GITAHI - KSh 6,200.00', 0, NULL, '2026-08-11 14:48:36'),
(2, 12, 'new_order', 'New Order Received', 'Order #CORE-20260811-4CC9CE from ALEX GITAHI - KSh 1,400.00', 0, NULL, '2026-08-11 14:51:59'),
(3, 13, 'new_order', 'New Order Received', 'Order #CORE-20260811-DC8451 from ALEX - KSh 1,400.00', 1, '2026-08-11 18:22:21', '2026-08-11 15:10:18'),
(4, 14, 'new_order', 'New Order Received', 'Order #CORE-20260814-E582DD from ALEX - KSh 51,400.00', 0, NULL, '2026-08-14 04:42:33');

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `slug` varchar(180) NOT NULL,
  `description` text DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`id`, `name`, `slug`, `description`, `logo`, `website`, `status`, `created_at`, `updated_at`) VALUES
(1, 'MikroTik', 'mikrotik', NULL, NULL, NULL, 1, '2026-08-07 09:45:40', '2026-08-07 09:45:40'),
(2, 'Tenda', 'tenda', NULL, NULL, NULL, 1, '2026-08-07 09:45:40', '2026-08-07 09:45:40'),
(3, 'TP-Link', 'tp-link', NULL, NULL, NULL, 1, '2026-08-07 09:45:40', '2026-08-07 09:45:40'),
(4, 'Ubiquiti', 'ubiquiti', NULL, NULL, NULL, 1, '2026-08-07 09:45:40', '2026-08-07 09:45:40'),
(5, 'Ruijie', 'ruijie', NULL, NULL, NULL, 1, '2026-08-07 09:45:40', '2026-08-07 09:45:40'),
(7, 'D-Link', 'd-link', NULL, NULL, NULL, 1, '2026-08-07 09:45:40', '2026-08-07 09:45:40'),
(8, 'Huawei', 'huawei', NULL, NULL, NULL, 1, '2026-08-07 09:45:40', '2026-08-07 09:45:40'),
(9, 'Signal Fire', 'signal-fire', NULL, NULL, NULL, 1, '2026-08-07 09:45:40', '2026-08-07 09:45:40'),
(10, 'Hioso', 'hioso', NULL, NULL, NULL, 1, '2026-08-07 09:45:40', '2026-08-07 09:45:40'),
(11, 'HikVision', 'hikvision', '', '1786443155_5b74cf3188.png', 'https://www.hikvision.com', 1, '2026-08-11 10:12:35', '2026-08-11 10:23:41'),
(12, 'Fiber', 'fiber', '', NULL, '', 1, '2026-08-14 05:27:24', '2026-08-14 05:27:24'),
(13, 'Cat-6 Cable', 'cp', '', NULL, '', 1, '2026-08-14 05:41:42', '2026-08-14 14:16:35'),
(14, 'Drop', 'drop', '', NULL, '', 1, '2026-08-14 05:48:16', '2026-08-14 05:48:16'),
(15, 'Crimping Tool', 'crimping-tool', '', NULL, '', 1, '2026-08-14 14:05:44', '2026-08-14 14:05:44');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `parent_id` int(10) UNSIGNED DEFAULT NULL,
  `name` varchar(150) NOT NULL,
  `slug` varchar(180) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `parent_id`, `name`, `slug`, `description`, `image`, `status`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Networking', 'networking', 'Networking equipment including routers, switches and wireless devices', NULL, 1, 0, '2026-08-07 09:45:40', '2026-08-07 09:45:40'),
(2, NULL, 'Routers', 'routers', 'Wireless and wired routers', NULL, 1, 0, '2026-08-07 09:45:40', '2026-08-07 09:45:40'),
(3, NULL, 'Switches', 'switches', 'Network switches and Ethernet switches', NULL, 1, 0, '2026-08-07 09:45:40', '2026-08-07 09:45:40'),
(4, NULL, 'Access Points', 'access-points', 'Indoor and outdoor wireless access points', NULL, 1, 0, '2026-08-07 09:45:40', '2026-08-07 09:45:40'),
(5, NULL, 'Fiber Optics', 'fiber-optics', 'Fiber optic networking equipment and accessories', NULL, 1, 0, '2026-08-07 09:45:40', '2026-08-07 09:45:40'),
(6, NULL, 'CCTV & Security', 'cctv-security', 'CCTV cameras, DVRs, NVRs and security equipment', NULL, 1, 0, '2026-08-07 09:45:40', '2026-08-07 09:45:40'),
(7, NULL, 'Tools', 'tools', 'Networking and fiber installation tools', NULL, 1, 0, '2026-08-07 09:45:40', '2026-08-07 09:45:40'),
(8, NULL, 'Accessories', 'accessories', 'Networking accessories and installation accessories', NULL, 1, 0, '2026-08-07 09:45:40', '2026-08-07 09:45:40'),
(10, NULL, 'Receiver', 'receiver', '', NULL, 1, 0, '2026-08-22 12:48:24', '2026-08-22 12:48:24');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text NOT NULL,
  `status` enum('new','read','replied','closed') DEFAULT 'new',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `event_date` date DEFAULT NULL,
  `event_time` varchar(100) DEFAULT NULL,
  `venue` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(500) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `title`, `slug`, `event_date`, `event_time`, `venue`, `description`, `image`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Ruijie Certified Training', 'ruijie-certified-training', '2026-07-25', '6:30 PM', 'PCEA Githurai Church School Hall', 'Ruijie Certified Training\r\n\r\nJoin us for an exciting Ruijie Certified Training designed for networking professionals, technicians, students, entrepreneurs, and anyone interested in building practical skills in modern networking solutions.\r\n\r\nThe training will provide an opportunity to learn, connect, and grow while exploring Ruijie/Reyee networking products and the Ruijie Cloud platform. Participants will gain practical insights into cloud-based network management, product configuration, deployment, monitoring, and solutions for Wi-Fi businesses and hotspots.\r\n\r\nWhether you are looking to improve your networking skills, start a Wi-Fi or hotspot business, or learn more about Ruijie/Reyee technologies, this training offers a valuable opportunity to interact with networking experts and discover real-world solutions.\r\n\r\nTopics & Highlights:\r\n\r\nRuijie/Reyee networking products and solutions\r\nRuijie Cloud network management\r\nWi-Fi and hotspot business opportunities\r\nPractical networking skills and deployment\r\nNetwork monitoring and management\r\nBuilding and growing your networking business\r\nInteraction with Ruijie products and solutions\r\n\r\nDate: 25th July 2025\r\nTime: From 6:00 PM\r\nVenue: PCEA Githurai Progressive School Hall\r\nAdmission: 100% FREE\r\n\r\nCome and learn, connect, and grow with Ruijie/Reyee and CORE LTD. Don\'t miss this opportunity to expand your networking knowledge and discover new business opportunities.', 'uploads/events/ruijie-certified-training-1787604867.jpg', 1, '2026-08-24 20:54:27', '2026-08-24 20:54:27');

-- --------------------------------------------------------

--
-- Table structure for table `inquiries`
--

CREATE TABLE `inquiries` (
  `id` int(10) UNSIGNED NOT NULL,
  `customer_name` varchar(150) NOT NULL,
  `customer_email` varchar(150) DEFAULT NULL,
  `customer_phone` varchar(50) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text NOT NULL,
  `product_id` int(10) UNSIGNED DEFAULT NULL,
  `status` enum('new','contacted','processing','completed','cancelled') DEFAULT 'new',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(10) UNSIGNED NOT NULL,
  `order_number` varchar(30) NOT NULL,
  `customer_name` varchar(150) NOT NULL,
  `customer_phone` varchar(30) NOT NULL,
  `customer_email` varchar(150) DEFAULT NULL,
  `delivery_address` text DEFAULT NULL,
  `customer_notes` text DEFAULT NULL,
  `whatsapp_number` varchar(20) DEFAULT NULL,
  `subtotal` decimal(12,2) NOT NULL DEFAULT 0.00,
  `delivery_fee` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `payment_method` varchar(50) NOT NULL DEFAULT 'Pending',
  `payment_status` varchar(30) NOT NULL DEFAULT 'Pending',
  `order_status` varchar(30) NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_number`, `customer_name`, `customer_phone`, `customer_email`, `delivery_address`, `customer_notes`, `whatsapp_number`, `subtotal`, `delivery_fee`, `total_amount`, `payment_method`, `payment_status`, `order_status`, `created_at`, `updated_at`) VALUES
(1, 'CORE-20260808-F9EF4C', 'ALEX GITAHI', '0757130130', 'alex.gtahi@gmail.com', 'NAIROBI, please call before deliverying', '', NULL, 2700.00, 0.00, 2700.00, 'Pending', 'Pending', 'Pending', '2026-08-08 12:30:58', '2026-08-08 12:30:58'),
(2, 'CORE-20260808-05346E', 'ALEX GITAHI', '0740731806', 'alex.gtahi@gmail.com', 'NAIROBI, please call be fore deliverying', '', NULL, 1350.00, 0.00, 1350.00, 'Pending', 'Pending', 'Pending', '2026-08-08 12:58:22', '2026-08-08 12:58:22'),
(3, 'CORE-20260808-B413C0', 'ALEX GITAHI', '0740731806', 'alex.gtahi@gmail.com', 'NAIROBI, please call be fore deliverying', '', NULL, 13500.00, 0.00, 13500.00, 'Pending', 'Pending', 'Pending', '2026-08-08 15:21:07', '2026-08-08 15:21:07'),
(4, 'CORE-20260808-D005C7', 'ALEX GITAHI', '0740731806', 'alex.gtahi@gmail.com', 'NAIROBI, please call be fore deliverying', '', NULL, 6750.00, 0.00, 6750.00, 'Pending', 'Pending', 'Pending', '2026-08-08 16:12:36', '2026-08-08 16:12:36'),
(5, 'CORE-20260808-28FCA3', 'ALEX GITAHI', '0740731806', 'alex.gtahi@gmail.com', 'NAIROBI, please call be fore deliverying', '', NULL, 4050.00, 0.00, 4050.00, 'Pending', 'Pending', 'Pending', '2026-08-08 16:22:27', '2026-08-08 16:22:27'),
(6, 'CORE-20260808-AEA9DC', 'ALEX GITAHI', '0740731806', 'alex.gtahi@gmail.com', 'NAIROBI, please call be fore deliverying', '', NULL, 2700.00, 0.00, 2700.00, 'Pending', 'Pending', 'Pending', '2026-08-08 16:29:14', '2026-08-08 16:29:14'),
(7, 'CORE-20260808-6B9421', 'ALEX GITAHI', '0740731806', 'alex.gtahi@gmail.com', 'NAIROBI, please call be fore deliverying', '', NULL, 1350.00, 0.00, 1350.00, 'Pending', 'Pending', 'Pending', '2026-08-08 16:32:02', '2026-08-08 16:32:02'),
(8, 'CORE-20260808-6A659C', 'ALEX GITAHI', '0740731806', 'alex.gtahi@gmail.com', 'NAIROBI, please call be fore deliverying', '', NULL, 1350.00, 0.00, 1350.00, 'Pending', 'Pending', 'Pending', '2026-08-08 16:34:33', '2026-08-08 16:34:33'),
(9, 'CORE-20260810-F6A2ED', 'ALEX GITAHI', '0740731806', 'alex.gtahi@gmail.com', 'NAIROBI, please call be fore deliverying', '', NULL, 4050.00, 0.00, 4050.00, 'Pending', 'Pending', 'Pending', '2026-08-10 08:26:38', '2026-08-10 08:26:38'),
(10, 'CORE-20260811-4FC1FA', 'ALEX GITAHI', '0740731806', 'alex.gtahi@gmail.com', 'NAIROBI, please call be fore deliverying', '', '254742363507', 3100.00, 0.00, 3100.00, 'Pending', 'Paid', 'Completed', '2026-08-11 11:20:22', '2026-08-11 11:46:08'),
(11, 'CORE-20260811-DBC30F', 'ALEX GITAHI', '0740731806', 'alex.gtahi@gmail.com', 'NAIROBI, MOI AVENUE', 'please call be fore deliverying', NULL, 6200.00, 0.00, 6200.00, 'Pending', 'Pending', 'Pending', '2026-08-11 14:48:36', '2026-08-11 14:48:36'),
(12, 'CORE-20260811-4CC9CE', 'ALEX GITAHI', '0740731806', 'alex.gtahi@gmail.com', 'NAIROBI, MOI AVENUE', 'please call be fore deliverying', '254792570000', 1400.00, 0.00, 1400.00, 'Pending', 'Pending', 'Pending', '2026-08-11 14:51:59', '2026-08-11 15:09:10'),
(13, 'CORE-20260811-DC8451', 'ALEX', '0740731806', 'alex.gtahi@gmail.com', 'NAIROBI, MOI AVENUE', '', '254792580000', 1400.00, 0.00, 1400.00, 'Pending', 'Pending', 'Pending', '2026-08-11 15:10:18', '2026-08-11 15:10:22'),
(14, 'CORE-20260814-E582DD', 'ALEX', '0740731806', 'alex.gtahi@gmail.com', 'NAIROBI, MOI AVENUE', '', '254720525132', 51400.00, 0.00, 51400.00, 'Pending', 'Pending', 'Processing', '2026-08-14 04:42:33', '2026-08-14 04:46:01');

-- --------------------------------------------------------

--
-- Table structure for table `order_activity_logs`
--

CREATE TABLE `order_activity_logs` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `old_value` varchar(255) DEFAULT NULL,
  `new_value` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED DEFAULT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_reference` varchar(100) DEFAULT NULL,
  `product_image` varchar(500) DEFAULT NULL,
  `quantity` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `unit_price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `subtotal` decimal(12,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `product_reference`, `product_image`, `quantity`, `unit_price`, `subtotal`, `created_at`) VALUES
(1, 1, 1, 'Tenda F3 N300 Wireless Wi-Fi Router', 'N300', 'Tenda F3.jpg', 2, 1350.00, 2700.00, '2026-08-08 12:30:58'),
(2, 2, 1, 'Tenda F3 N300 Wireless Wi-Fi Router', 'N300', 'Tenda F3.jpg', 1, 1350.00, 1350.00, '2026-08-08 12:58:22'),
(3, 3, 1, 'Tenda F3 N300 Wireless Wi-Fi Router', 'N300', 'Tenda F3.jpg', 10, 1350.00, 13500.00, '2026-08-08 15:21:07'),
(4, 4, 1, 'Tenda F3 N300 Wireless Wi-Fi Router', 'N300', 'Tenda F3.jpg', 5, 1350.00, 6750.00, '2026-08-08 16:12:36'),
(5, 5, 1, 'Tenda F3 N300 Wireless Wi-Fi Router', 'N300', 'Tenda F3.jpg', 3, 1350.00, 4050.00, '2026-08-08 16:22:27'),
(6, 6, 1, 'Tenda F3 N300 Wireless Wi-Fi Router', 'N300', 'Tenda F3.jpg', 2, 1350.00, 2700.00, '2026-08-08 16:29:14'),
(7, 7, 1, 'Tenda F3 N300 Wireless Wi-Fi Router', 'N300', 'Tenda F3.jpg', 1, 1350.00, 1350.00, '2026-08-08 16:32:02'),
(8, 8, 1, 'Tenda F3 N300 Wireless Wi-Fi Router', 'N300', 'Tenda F3.jpg', 1, 1350.00, 1350.00, '2026-08-08 16:34:33'),
(9, 9, 1, 'Tenda F3 N300 Wireless Wi-Fi Router', 'N300', 'Tenda F3.jpg', 3, 1350.00, 4050.00, '2026-08-10 08:26:38'),
(10, 10, 3, 'Tenda F6 N300 Wireless Wi-Fi Router', 'Tenda F6', '1786428454_4bcf589782b1.jpg', 1, 1700.00, 1700.00, '2026-08-11 11:20:22'),
(11, 10, 1, 'Tenda F3 N300 Wireless Wi-Fi Router', 'N300', 'Tenda F3.jpg', 1, 1400.00, 1400.00, '2026-08-11 11:20:22'),
(12, 11, 1, 'Tenda F3 N300 Wireless Wi-Fi Router', 'N300', 'Tenda F3.jpg', 2, 1400.00, 2800.00, '2026-08-11 14:48:36'),
(13, 11, 3, 'Tenda F6 N300 Wireless Wi-Fi Router', 'Tenda F6', '1786428454_4bcf589782b1.jpg', 2, 1700.00, 3400.00, '2026-08-11 14:48:36'),
(14, 12, 1, 'Tenda F3 N300 Wireless Wi-Fi Router', 'N300', 'Tenda F3.jpg', 1, 1400.00, 1400.00, '2026-08-11 14:51:59'),
(15, 13, 1, 'Tenda F3 N300 Wireless Wi-Fi Router', 'N300', 'Tenda F3.jpg', 1, 1400.00, 1400.00, '2026-08-11 15:10:18'),
(16, 14, 9, 'Ruijie Reyee RG‑RAP6262(G) Outdoor AP', 'Ruijie RAP6262(G)', '1786608737_6bf4f86d25f2.jpg', 2, 23500.00, 47000.00, '2026-08-14 04:42:33'),
(17, 14, 8, 'Tenda AC5 V3 AC1200 Wireless Dual Band Wi-Fi Router', 'Tenda AC5', '1786463284_b9c83d63cfe6.webp', 1, 3000.00, 3000.00, '2026-08-14 04:42:33'),
(18, 14, 1, 'Tenda F3 N300 Wireless Wi-Fi Router', 'N300', 'Tenda F3.jpg', 1, 1400.00, 1400.00, '2026-08-14 04:42:33');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(10) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED DEFAULT NULL,
  `brand_id` int(10) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `sku` varchar(100) DEFAULT NULL,
  `short_description` text DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `price` decimal(12,2) DEFAULT 0.00,
  `old_price` decimal(12,2) DEFAULT NULL,
  `cost_price` decimal(12,2) DEFAULT NULL,
  `stock_quantity` int(11) DEFAULT 0,
  `low_stock_level` int(11) DEFAULT 5,
  `main_image` varchar(255) DEFAULT NULL,
  `featured` tinyint(1) DEFAULT 0,
  `status` tinyint(1) DEFAULT 1,
  `views` int(10) UNSIGNED DEFAULT 0,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_keywords` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `brand_id`, `name`, `slug`, `sku`, `short_description`, `description`, `price`, `old_price`, `cost_price`, `stock_quantity`, `low_stock_level`, `main_image`, `featured`, `status`, `views`, `meta_title`, `meta_description`, `meta_keywords`, `created_at`, `updated_at`) VALUES
(1, 2, 2, 'Tenda F3 N300 Wireless Wi-Fi Router', 'tenda-f3-n300-wireless-wi-fi-router', 'N300', 'Tenda F3 N300 Wireless Wi-Fi Router', 'Tenda F3 N300 Wireless Wi-Fi Router with 300Mbps wireless speed, three high-gain antennas, easy setup, multiple device support, smart bandwidth control, guest Wi-Fi and secure wireless connectivity.', 1350.00, NULL, NULL, 10, 5, 'Tenda F3.jpg', 1, 1, 0, '', '', '', '2026-08-07 15:12:13', '2026-08-16 15:23:40'),
(8, 2, 2, 'Tenda AC5 V3 AC1200 Wireless Dual Band Wi-Fi Router', 'tenda-ac5-v3-ac1200-wireless-dual-band-wi-fi-router', 'Tenda AC5', '', '', 3000.00, NULL, NULL, 10, 5, '1786463284_b9c83d63cfe6.webp', 1, 1, 0, 'Tenda AC5', '', 'router', '2026-08-11 15:48:04', '2026-08-11 16:11:57'),
(9, 4, 5, 'Ruijie Reyee RG‑RAP6262(G) Outdoor AP', 'ruijie-reyee-rg-rap6262-g-outdoor-ap', 'Ruijie RAP6262(G)', '', '', 23500.00, NULL, NULL, 10, 5, '1786608737_6bf4f86d25f2.jpg', 1, 1, 0, 'Ruijie RAP6262(G)', '', 'access point', '2026-08-13 08:12:17', '2026-08-13 08:12:43'),
(10, 5, 12, 'ADSS Fiber Cable 12 Core 4KM', 'adss-fiber-cable-12-core-4km', 'ADSS', '', '', 200000.00, NULL, NULL, 10, 5, '1786685544_b15751479818.webp', 1, 1, 0, 'ADSS 12-CORE', '', 'ADSS', '2026-08-14 05:32:24', '2026-08-14 05:32:24'),
(11, 5, 12, 'ADSS Fiber Cable 24 Core 4KM', 'adss-fiber-cable-24-core-4km', 'ADSS-24-CORE', '', '', 240000.00, NULL, NULL, 10, 5, '1786687102_69290e3433c3.webp', 1, 1, 0, 'ADSS 24-CORE', '', 'ADSS', '2026-08-14 05:37:43', '2026-08-14 05:58:22'),
(12, 1, 13, 'CP Cat 6 Indoor Cable', 'cp-cat-6-indoor-cable', 'CP Cable', '', '', 4000.00, NULL, NULL, 0, 5, '1786686432_bbb705bd5ad7.jpg', 0, 1, 0, 'CP Cable', '', 'Cat 6 Cable', '2026-08-14 05:47:12', '2026-08-14 05:47:12'),
(13, 5, 14, 'Drop Cable 1KM 2 Core', 'drop-cable-1km-2-core', 'Drop 1KM', '', '', 9500.00, NULL, NULL, 16, 5, '1786687027_a780df006ccc.webp', 0, 1, 0, 'Drop 1KM', '', 'Drop', '2026-08-14 05:57:07', '2026-08-14 05:57:07'),
(14, 5, 14, 'Drop Cable 1KM 4 Core', 'drop-cable-1km-4-core', 'Drop 1KM 4Core', '', '', 15500.00, NULL, NULL, 10, 5, '1786687728_cdd9269d45c8.jpg', 0, 1, 0, 'Drop 1KM 4Core', '', 'Drop', '2026-08-14 06:08:48', '2026-08-14 06:08:48'),
(15, 5, 14, 'Drop Cable 1KM 8 Core', 'drop-cable-1km-8-core', 'Drop 1KM 8Core', '', '', 19500.00, NULL, NULL, 10, 5, '1786688027_4cd325187c46.jpg', 1, 1, 0, 'Drop 1KM 8Core', '', 'Drop', '2026-08-14 06:13:47', '2026-08-14 06:13:47'),
(16, 5, 14, 'Drop Cable 2KM 2 Core', 'drop-cable-2km-2-core', 'Drop 2KM', '', '', 18500.00, NULL, NULL, 10, 5, '1786713198_a141830117f6.jpg', 0, 1, 0, 'Drop 2KM', '', 'Drop', '2026-08-14 13:13:18', '2026-08-14 13:13:18'),
(17, 5, 14, 'Drop Cable 2KM 4-Core', 'drop-cable-2km-4-core', 'Drop 2KM 4Core', '', '', 22500.00, NULL, NULL, 10, 5, '1786713704_64d0d99b3a16.jpg', 1, 1, 0, 'Drop 2KM 4Core', '', 'Drop 2KM 4Core', '2026-08-14 13:21:44', '2026-08-14 13:21:44'),
(18, 5, 14, 'Drop Cable 2KM 8 Core', 'drop-cable-2km-8-core', 'Drop 2KM 8Core', '', '', 26500.00, NULL, NULL, 10, 5, '1786715050_d57f689bf730.jpg', 1, 1, 0, 'Drop 2KM 8Core', '', 'Drop', '2026-08-14 13:44:10', '2026-08-14 13:44:10'),
(19, 5, 14, 'Drop Cable 2KM Indoor', 'drop-cable-2km-indoor', 'Drop 2KM Indoor', '', '', 15800.00, NULL, NULL, 11, 5, '1786716056_e41a3d9a407c.jpg', 1, 1, 0, 'Drop 2KM Indoor', '', 'Drop', '2026-08-14 14:00:56', '2026-08-14 14:00:56'),
(20, 1, 15, 'Dual-Modular Crimping Tool', 'dual-modular-crimping-tool', 'Crimping Tool Yellow', '', '', 500.00, NULL, NULL, 10, 5, '1786716759_6d673c4dffb1.jpg', 1, 1, 0, 'Crimping Tool Yellow', '', 'Crimping Tool', '2026-08-14 14:12:39', '2026-08-14 14:12:57'),
(21, 1, 13, 'Easenet Cat 6 Indoor Cable', 'easenet-cat-6-indoor-cable', 'Easenet Cat6', '', '', 5600.00, NULL, NULL, 10, 5, '1786717263_521e8372be18.jpg', 1, 1, 0, 'Easenet Cat6', '', 'Cat 6 Cable', '2026-08-14 14:21:03', '2026-08-14 14:21:03'),
(22, 1, 13, 'Gigalan CAT 6 Indoor Cable', 'gigalan-cat-6-indoor-cable', 'Gigalan Indoor', '', '', 3900.00, NULL, NULL, 10, 5, '1787390261_64f15ec485da.png', 1, 1, 0, 'Gigalan Indoor', '', 'Cat 6 Cable', '2026-08-22 09:17:41', '2026-08-22 09:17:41'),
(23, 5, 8, 'Huawei XPON Echolife Router HG8546M', 'huawei-xpon-echolife-router-hg8546m', 'Huawei Xpon', '', '', 1450.00, NULL, NULL, 10, 5, '1787390807_775f3b9e78dc.webp', 1, 1, 0, 'Huawei Xpon', '', 'router', '2026-08-22 09:26:47', '2026-08-22 09:26:47'),
(24, 1, 15, 'Jakemy JM-CT4-3 Crimping Tool', 'jakemy-jm-ct4-3-crimping-tool', 'Jakemy Crimping Tool', '', '', 650.00, NULL, NULL, 10, 5, '1787391581_05097d62e17f.jpg', 1, 1, 0, 'Jakemy Crimping Tool', '', '', '2026-08-22 09:39:41', '2026-08-22 09:39:41'),
(25, 10, 4, 'LiteBeam M5-Ubiquiti', 'litebeam-m5-ubiquiti', 'LiteBeam M5', '', '', 6500.00, NULL, NULL, 10, 5, '1787403313_dbf2a416d9c7.png', 1, 1, 0, 'LiteBeam M5', '', 'receiver', '2026-08-22 12:55:13', '2026-08-22 13:12:24'),
(26, 1, 1, 'Mikrotik L009UIGS-2HAXD-IN Router', 'mikrotik-l009uigs-2haxd-in-router', 'Mikrotik L009', '', '', 23000.00, NULL, NULL, 10, 5, '1787405888_6c3039f08601.png', 1, 1, 0, 'Mikrotik L009', '', 'Mikrotik', '2026-08-22 13:38:08', '2026-08-22 13:38:08'),
(27, 1, 1, 'Mikrotik RB260GS Series', 'mikrotik-rb260gs-series', 'Mikrotik RB260G', '', '', 6500.00, NULL, NULL, 10, 5, '1787410253_35f94ef7738e.png', 1, 1, 0, 'Mikrotik RB260G', '', 'Mikrotik', '2026-08-22 14:50:53', '2026-08-22 14:52:50'),
(28, 1, 1, 'MikroTik RB4011iGS+RM Ethernet Router', 'mikrotik-rb4011igs-rm-ethernet-router', 'MikroTik RB4011', '', '', 30000.00, NULL, NULL, 10, 5, '1787411338_dbf5ed274a57.jpg', 1, 1, 0, 'MikroTik RB4011', '', 'Mikrotik', '2026-08-22 15:08:58', '2026-08-22 15:08:58'),
(30, 1, 1, 'Mikrotik RB760iGS hEX S 5x', 'mikrotik-rb760igs-hex-s-5x', 'Mikrotik RB760i', '', '', 12000.00, NULL, NULL, 10, 5, '1787412428_dd55de52efe2.jpg', 1, 1, 0, 'Mikrotik RB760i', '', 'Mikrotik', '2026-08-22 15:25:27', '2026-08-22 15:27:08'),
(31, 1, 1, 'Mikrotik hAP Lite TC RB941', 'mikrotik-hap-lite-tc-rb941', 'Mikrotik RB941', '', '', 4500.00, 5000.00, NULL, 0, 5, '1787752575_cec87816fd03.jpg', 1, 1, 0, 'Mikrotik RB941', '', 'Mikrotik', '2026-08-26 13:56:15', '2026-08-26 14:00:33'),
(32, 1, 1, 'Mikrotik RB951Ui-2HnD Router', 'mikrotik-rb951ui-2hnd-router', 'Mikrotik RB951', '', '', 9000.00, 9500.00, NULL, 0, 5, '1788361767_f55be6c4220a.jpg', 1, 1, 0, 'Mikrotik RB951', '', 'Mikrotik', '2026-09-02 15:08:02', '2026-09-02 15:09:27');

-- --------------------------------------------------------

--
-- Table structure for table `product_features`
--

CREATE TABLE `product_features` (
  `id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `feature` text NOT NULL,
  `benefit` text DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_features`
--

INSERT INTO `product_features` (`id`, `product_id`, `feature`, `benefit`, `sort_order`, `created_at`) VALUES
(97, 8, 'AC1200 Dual-Band Wi-Fi', 'Enjoy faster and more reliable Wi-Fi for streaming, browsing, online classes, video calls, and gaming.', 1, '2026-08-11 16:11:57'),
(98, 8, '2.4GHz + 5GHz Wi-Fi', 'Use 2.4GHz for wider coverage or 5GHz for faster connections with compatible devices.', 2, '2026-08-11 16:11:57'),
(99, 8, '4 High-Gain Antennas', 'Four external 6dBi antennas help provide stronger and more consistent Wi-Fi coverage.', 3, '2026-08-11 16:11:57'),
(100, 8, 'Easy Setup', 'Get your home or office network running quickly with a simple and user-friendly setup.', 4, '2026-08-11 16:11:57'),
(101, 8, 'Parental Controls', 'Control internet access and manage which devices can connect to your network.', 5, '2026-08-11 16:11:57'),
(102, 8, 'Wi-Fi Repeater Mode', 'Extend an existing Wi-Fi signal to reach areas with weak coverage.', 6, '2026-08-11 16:11:57'),
(103, 8, 'Access Point Mode', 'Use the router to add Wi-Fi to an existing wired network.', 7, '2026-08-11 16:11:57'),
(104, 8, 'Guest Wi-Fi', 'Give visitors internet access while keeping your main network separate.', 8, '2026-08-11 16:11:57'),
(111, 9, 'Wi-Fi 6 AX1800 Performance', 'Delivers up to 1775 Mbps (574 Mbps @2.4 GHz + 1201 Mbps @5 GHz) with 2×2 MU-MIMO for dual-band throughput.', 1, '2026-08-13 08:12:43'),
(112, 9, 'Omni-Directional Coverage', 'Four built-in high-gain antennas provide up to ~100 m range on 2.4 GHz and 300 m on 5 GHz.', 2, '2026-08-13 08:12:43'),
(113, 9, 'Rugged IP68 Rated Casing', 'Durable, weatherproof (dustproof, waterproof), flame-retardant housing rated for −30 °C to +65 °C and 4 kV lightning protection.', 3, '2026-08-13 08:12:43'),
(114, 9, 'Unique “UFO” Design', 'Distinctive form factor that blends aesthetics with outdoor functionality.', 4, '2026-08-13 08:12:43'),
(115, 9, 'Reyee Mesh & Cloud Management', 'Supports mesh networking, plug-and-play deployment, and remote monitoring via Reyee app or Ruijie Cloud—with no external controller required.', 5, '2026-08-13 08:12:43'),
(116, 9, 'High Client Capacity & Roaming', 'Supports up to 256 clients, layer-2/3 roaming, OFDMA, TWT, automatic channel/power tuning, and client isolation/security policies.', 6, '2026-08-13 08:12:43'),
(126, 10, 'Cable Type', 'ADSS (All-Dielectric Self-Supporting) Fiber Optic Cable', 1, '2026-08-14 05:32:24'),
(127, 10, 'Fiber Count', '12 Core Single Mode Fibers', 2, '2026-08-14 05:32:24'),
(128, 10, 'Length', '4000 Meters (4KM Roll)', 3, '2026-08-14 05:32:24'),
(129, 10, 'Application', 'Ideal for aerial backbone networks, ISP deployments, utility poles, and long-distance outdoor installations.', 4, '2026-08-14 05:32:24'),
(130, 10, 'All-Dielectric Construction', 'Non-metallic design provides immunity to lightning and electromagnetic interference.', 5, '2026-08-14 05:32:24'),
(131, 10, 'Low Signal Loss', 'Supports stable, high-speed transmission over long distances.', 6, '2026-08-14 05:32:24'),
(132, 10, 'High Tensile Strength', 'Designed for suspended aerial spans with excellent mechanical durability.', 7, '2026-08-14 05:32:24'),
(138, 12, 'Category', 'CAT6, 4 Pair Unshielded Twisted Pair (UTP) Cable (Eco Series)', 1, '2026-08-14 05:47:12'),
(139, 12, 'Conductor', 'Solid Bare Copper', 2, '2026-08-14 05:47:12'),
(140, 12, 'Insulation', 'High-Density Polyethylene (HDPE)', 3, '2026-08-14 05:47:12'),
(141, 12, 'Cross Filler', 'The Cross Separator for enhanced signal stability', 4, '2026-08-14 05:47:12'),
(142, 12, 'Sheath Color', 'Gray', 5, '2026-08-14 05:47:12'),
(143, 12, 'Sequential Marking', 'Printed marking at every meter', 6, '2026-08-14 05:47:12'),
(144, 12, 'Length', '305 Meters (Full Box)', 7, '2026-08-14 05:47:12'),
(145, 13, 'Cable Type', '2 Core Fiber Optic Drop Cable (FTTH)', 1, '2026-08-14 05:57:07'),
(146, 13, 'Fiber Count', '2 Fibers (Single Mode)', 2, '2026-08-14 05:57:07'),
(147, 13, 'Application', 'Ideal for Fiber-to-the-Home (FTTH), last-mile connectivity, and outdoor installations.', 3, '2026-08-14 05:57:07'),
(148, 13, 'Length', '1000 Meters (1KM Roll)', 4, '2026-08-14 05:57:07'),
(149, 13, 'Strength Member', 'Reinforced with steel wire / FRP for added durability and tensile strength.', 5, '2026-08-14 05:57:07'),
(150, 13, 'Weather Resistance', 'Designed to withstand harsh environmental conditions including sun, rain, and wind.', 6, '2026-08-14 05:57:07'),
(151, 13, 'Low Signal Loss', 'Ensures stable and efficient data transmission over long distances.', 7, '2026-08-14 05:57:07'),
(152, 13, 'Lightweight & Flexible', 'Easy to handle, route, and install in residential and commercial setups.', 8, '2026-08-14 05:57:07'),
(153, 11, '24 Core High Capacity', 'Connect more homes, offices, CCTV systems, or network points using one cable.', 1, '2026-08-14 05:58:22'),
(154, 11, '4KM Full Roll', 'Long distance coverage with fewer joints, saving time and installation costs.', 2, '2026-08-14 05:58:22'),
(155, 11, 'Perfect for Aerial Use', 'Designed for pole-to-pole installations without needing extra support wire.', 3, '2026-08-14 05:58:22'),
(156, 11, 'Strong Outdoor Build', 'Handles sun, rain, wind, and harsh weather conditions with confidence.', 4, '2026-08-14 05:58:22'),
(157, 11, 'Fast & Stable Internet', 'Supports high-speed fiber connections with reliable signal performance.', 5, '2026-08-14 05:58:22'),
(158, 14, 'Cable Type', '4 Core Fiber Optic Drop Cable (FTTH)', 1, '2026-08-14 06:08:48'),
(159, 14, 'Fiber Count', '4 Fibers (Single Mode)', 2, '2026-08-14 06:08:48'),
(160, 14, 'Length', '1000 Meters (1KM Roll)', 3, '2026-08-14 06:08:48'),
(161, 14, 'Application', 'Suitable for FTTH deployments, last-mile connectivity, and distribution networks.', 4, '2026-08-14 06:08:48'),
(162, 14, 'Strength Member', 'Reinforced with steel wire / FRP for enhanced tensile strength and durability.', 5, '2026-08-14 06:08:48'),
(163, 14, 'Weather Resistance', 'Engineered to withstand harsh outdoor conditions including UV exposure, rain, and wind.', 6, '2026-08-14 06:08:48'),
(164, 14, 'Low Signal Attenuation', 'Ensures reliable, high-speed data transmission over long distances.', 7, '2026-08-14 06:08:48'),
(165, 14, 'Flexible & Easy Installation', 'Lightweight design allows for easy routing and installation in residential and commercial setups.', 8, '2026-08-14 06:08:48'),
(166, 15, 'Cable Type', '8 Core Fiber Optic Drop Cable (FTTH)', 1, '2026-08-14 06:13:47'),
(167, 15, 'Fiber Count', '8 Fibers (Single Mode)', 2, '2026-08-14 06:13:47'),
(168, 15, 'Length', '1000 Meters (1KM Roll)', 3, '2026-08-14 06:13:47'),
(169, 15, 'Strength Member', 'Reinforced with steel wire / FRP for superior tensile strength and durability.', 4, '2026-08-14 06:13:47'),
(170, 15, 'Application', 'Ideal for FTTH deployments, distribution networks, estates, and multi-user connections.', 5, '2026-08-14 06:13:47'),
(171, 15, 'Flexible & Easy Installation', 'Lightweight and flexible design allows easy routing and deployment in various environments.', 6, '2026-08-14 06:13:47'),
(172, 15, 'Installation Type', 'Supports aerial, duct, and wall installations.', 7, '2026-08-14 06:13:47'),
(173, 15, 'Low Signal Attenuation', 'Ensures efficient, high-speed data transmission over long distances.', 8, '2026-08-14 06:13:47'),
(174, 16, 'Cable Type', '2 Core Fiber Optic Drop Cable (FTTH)', 1, '2026-08-14 13:13:18'),
(175, 16, 'Fiber Count', '2 Fibers (Single Mode)', 2, '2026-08-14 13:13:18'),
(176, 16, 'Application', 'Ideal for long-distance FTTH connections, last-mile links, and rural or estate deployments.', 3, '2026-08-14 13:13:18'),
(177, 16, 'Length', '2000 Meters (2KM Roll)', 4, '2026-08-14 13:13:18'),
(178, 16, 'Strength Member', 'Reinforced with steel wire / FRP for enhanced tensile strength over longer distances.', 5, '2026-08-14 13:13:18'),
(179, 16, 'Low Signal Attenuation', 'Ensures stable and efficient data transmission over extended distances.', 6, '2026-08-14 13:13:18'),
(180, 16, 'Weather Resistance', 'Designed to withstand harsh outdoor conditions including UV, rain, and wind.', 7, '2026-08-14 13:13:18'),
(181, 16, 'Flexible & Easy Installation', 'Lightweight and flexible for easy routing across long spans and complex layouts.', 8, '2026-08-14 13:13:18'),
(182, 17, 'Cable Type', '4 Core Fiber Optic Drop Cable (FTTH)', 1, '2026-08-14 13:21:44'),
(183, 17, 'Fiber Count', '4 Fibers (Single Mode)', 2, '2026-08-14 13:21:44'),
(184, 17, 'Application', 'Ideal for long-distance FTTH deployments, estates, and multi-user distribution networks.', 3, '2026-08-14 13:21:44'),
(185, 17, 'Strength Member', 'Reinforced with steel wire / FRP for enhanced durability and tensile strength over long runs.', 4, '2026-08-14 13:21:44'),
(186, 17, 'Length', '2000 Meters (2KM Roll)', 5, '2026-08-14 13:21:44'),
(187, 17, 'Installation Type', 'Supports aerial, duct, and wall-mounted installations.', 6, '2026-08-14 13:21:44'),
(188, 17, 'Weather Resistance', 'Designed to withstand harsh outdoor conditions including UV exposure, rain, and wind.', 7, '2026-08-14 13:21:44'),
(189, 17, 'Flexible & Easy Installation', 'Lightweight and flexible design allows easy routing across long spans and complex layouts.', 8, '2026-08-14 13:21:44'),
(190, 18, 'Cable Type', '8 Core Fiber Optic Drop Cable (FTTH)', 1, '2026-08-14 13:44:10'),
(191, 18, 'Fiber Count', '8 Fibers (Single Mode)', 2, '2026-08-14 13:44:10'),
(192, 18, 'Application', 'Ideal for long-distance FTTH deployments, estates, campuses, and high-density distribution networks.', 3, '2026-08-14 13:44:10'),
(193, 18, 'Strength Member', 'Reinforced with steel wire / FRP for superior tensile strength and durability over extended runs.', 4, '2026-08-14 13:44:10'),
(194, 18, 'Length', '2000 Meters (2KM Roll)', 5, '2026-08-14 13:44:10'),
(195, 18, 'Outer Jacket', 'UV-resistant LSZH / PE sheath suitable for indoor and outdoor installations.', 6, '2026-08-14 13:44:10'),
(196, 18, 'Installation Type', 'Supports aerial, duct, and wall-mounted installations.', 7, '2026-08-14 13:44:10'),
(197, 18, 'Weather Resistance', 'Designed to withstand UV exposure, rain, wind, and harsh outdoor conditions.', 8, '2026-08-14 13:44:10'),
(198, 19, 'Cable Type', '2 Core Fiber Optic Drop Cable (FTTH - Indoor)', 1, '2026-08-14 14:00:56'),
(199, 19, 'Fiber Count', '2 Fibers (Single Mode)', 2, '2026-08-14 14:00:56'),
(200, 19, 'Application', 'Designed for indoor FTTH installations, apartments, offices, and structured cabling.', 3, '2026-08-14 14:00:56'),
(201, 19, 'Length', '2000 Meters (2KM Roll)', 4, '2026-08-14 14:00:56'),
(202, 19, 'Jacket Type', 'LSZH (Low Smoke Zero Halogen) for safe indoor use.', 5, '2026-08-14 14:00:56'),
(203, 19, 'Fire Safety', 'Low smoke and halogen-free materials reduce toxic emissions in case of fire.', 6, '2026-08-14 14:00:56'),
(204, 19, 'Installation Type', 'Suitable for wall routing, trunking, conduits, and ceiling installations.', 7, '2026-08-14 14:00:56'),
(205, 19, 'Flexible Design', 'Easy to bend and route within tight indoor spaces.', 8, '2026-08-14 14:00:56'),
(211, 20, 'Versatile Crimping', 'Compatible with 8P8C (RJ45), 6P6C (RJ12), 6P4C (RJ11), 4P4C, and 4P2C connectors', 1, '2026-08-14 14:12:57'),
(212, 20, 'All-in-One Tool', 'Capable of cutting, stripping, and crimping CAT5, CAT5e, and CAT6 cables.', 2, '2026-08-14 14:12:57'),
(213, 20, 'Durable Build', 'Constructed from middle carbon steel with a black oxide finish for enhanced durability.', 3, '2026-08-14 14:12:57'),
(214, 20, 'Ergonomic Handles', 'Features green, non-slip handles designed for comfort and reduced hand fatigue.', 4, '2026-08-14 14:12:57'),
(215, 20, 'Compact Design', 'Lightweight and compact, making it easy to handle and transport.', 5, '2026-08-14 14:12:57'),
(216, 21, 'High-Speed Data Transmission', 'Supports up to 1 Gbps speeds with a 250 MHz bandwidth for reliable performance.', 1, '2026-08-14 14:21:03'),
(217, 21, 'Pure Copper Conductors', 'Ensures minimal signal loss and enhanced durability compared to CCA cables.', 2, '2026-08-14 14:21:03'),
(218, 21, 'Enhanced Shielding', 'Reduces interference and crosstalk, providing stable network connections.', 3, '2026-08-14 14:21:03'),
(219, 21, 'Durable PVC Jacket', 'Designed for indoor use with flexibility and resistance to wear and tear.', 4, '2026-08-14 14:21:03'),
(220, 21, 'Universal Compatibility', 'Works with routers, switches, patch panels, and other networking devices.', 5, '2026-08-14 14:21:03'),
(221, 1, '300Mbps Wireless Speed', 'Enjoy smooth web browsing, HD video streaming, online classes, and video calls.', 1, '2026-08-16 15:23:40'),
(222, 1, 'Three High-Gain Antennas', 'Provides stronger Wi-Fi coverage and better signal throughout your home or office.', 2, '2026-08-16 15:23:40'),
(223, 1, 'Easy Setup', 'Get connected in minutes with a simple installation process and user-friendly interface.', 3, '2026-08-16 15:23:40'),
(224, 1, 'Multiple Device Support', 'Connect smartphones, laptops, Smart TVs, and other Wi-Fi devices simultaneously.', 4, '2026-08-16 15:23:40'),
(225, 1, 'Smart Bandwidth Control', 'Manage internet speeds for connected users to ensure a stable network experience.', 5, '2026-08-16 15:23:40'),
(226, 1, 'Guest Wi-Fi Network', 'Create a separate network for visitors without exposing your private devices.', 6, '2026-08-16 15:23:40'),
(227, 1, 'Secure Wireless Connection', 'Protect your network with advanced Wi-Fi security to keep your data safe.', 7, '2026-08-16 15:23:40'),
(228, 1, 'Energy Efficient', 'Designed to deliver reliable performance while consuming minimal power.', 8, '2026-08-16 15:23:40'),
(229, 1, 'Perfect for Home & Small Office', 'An affordable solution for everyday internet use in homes, apartments, and small businesses.', 9, '2026-08-16 15:23:40'),
(230, 22, 'CAT6 Performance', 'Supports fast and reliable network connections for homes, offices, and business networks.', 1, '2026-08-22 09:17:41'),
(231, 22, 'Gigabit Speed Support', 'Ideal for high-speed internet, file sharing, CCTV, access points, and other network devices.', 2, '2026-08-22 09:17:41'),
(232, 22, 'Solid Copper Conductor', 'Provides dependable signal transmission and stable network performance.', 3, '2026-08-22 09:17:41'),
(233, 22, 'Reduced Signal Interference', 'Twisted-pair construction helps maintain a clean and stable connection with less interference.', 4, '2026-08-22 09:17:41'),
(234, 22, '305M Roll', 'Provides plenty of cable for complete network installations and multiple connection points.', 5, '2026-08-22 09:17:41'),
(235, 22, 'Indoor Installation', 'Suitable for structured cabling in homes, offices, shops, schools, and server rooms.', 6, '2026-08-22 09:17:41'),
(236, 22, 'Reliable Data Transmission', 'Helps deliver consistent network performance for internet, CCTV, VoIP, and LAN connections.', 7, '2026-08-22 09:17:41'),
(237, 22, 'Great for ISP & Networking Projects', 'A practical choice for professional network installations, customer connections, and office cabling.', 8, '2026-08-22 09:17:41'),
(238, 23, 'EPON & GPON Compatible', 'Works with most fiber internet providers, making it an excellent choice for FTTH installations.', 1, '2026-08-22 09:26:47'),
(239, 23, 'Built-in Wi-Fi', 'Share your fiber internet wirelessly with phones, laptops, Smart TVs, and other Wi-Fi devices.', 2, '2026-08-22 09:26:47'),
(240, 23, 'Multiple LAN Ports', 'Connect computers, CCTV systems, switches, and other wired devices at the same time.', 3, '2026-08-22 09:26:47'),
(241, 23, 'Voice (Telephone) Support', 'Supports VoIP telephone services, allowing both internet and voice on one device.', 4, '2026-08-22 09:26:47'),
(242, 23, 'Fast & Stable Fiber Connection', 'Delivers reliable internet performance for streaming, video calls, online learning, and business use.', 5, '2026-08-22 09:26:47'),
(243, 23, 'Easy FTTH Installation', 'Simple to install and ideal for homes, apartments, offices, and business fiber connections.', 6, '2026-08-22 09:26:47'),
(244, 23, 'Supports Multiple Users', 'Allows several users and devices to stay connected with stable performance.', 7, '2026-08-22 09:26:47'),
(245, 23, 'Ideal for Home & Business', 'Perfect for homes, offices, schools, hotels, and ISP fiber deployments.', 8, '2026-08-22 09:26:47'),
(246, 24, 'Multi-Purpose Crimping Tool', 'Designed for crimping RJ45, RJ11, and RJ12 connectors with precision.', 1, '2026-08-22 09:39:41'),
(247, 24, 'Built-in Wire Cutter & Stripper', 'Integrated cutting and stripping functions reduce the need for extra tools.', 2, '2026-08-22 09:39:41'),
(248, 24, 'High-Quality Steel Construction', 'Made from durable hardened steel for long-lasting performance and reliability.', 3, '2026-08-22 09:39:41'),
(249, 24, 'Ergonomic Non-Slip Handles', 'Comfortable rubberized grip ensures firm handling and reduced hand fatigue.', 4, '2026-08-22 09:39:41'),
(250, 24, 'Precision Crimping Mechanism', 'Ensures accurate, secure crimping for stable network and telephone connections.', 5, '2026-08-22 09:39:41'),
(251, 24, 'Suitable for Network & Telecom Use', 'Ideal for Ethernet cable installation, phone lines, and structured cabling tasks.', 6, '2026-08-22 09:39:41'),
(252, 24, 'Compact & Lightweight Design', 'Easy to carry in toolkits, making it perfect for technicians and field installers.', 7, '2026-08-22 09:39:41'),
(253, 24, 'Professional & DIY Friendly', 'Suitable for both professional network technicians and home users.', 8, '2026-08-22 09:39:41'),
(270, 25, 'High Performance', 'Delivers up to 150+ Mbps real TCP/IP throughput using 5 GHz frequency band.', 1, '2026-08-22 13:12:24'),
(271, 25, 'Long-Range Connectivity', 'Supports wireless links up to 25+ km with superior signal quality for outdoor bridging.', 2, '2026-08-22 13:12:24'),
(272, 25, 'Integrated Dish Antenna', 'Equipped with a high-gain directional dish (25 dBi or higher depending on model) for maximum link efficiency.', 3, '2026-08-22 13:12:24'),
(273, 25, 'AirMax Technology', 'Uses Ubiquiti’s TDMA protocol to reduce latency, improve throughput, and allow scalability.', 4, '2026-08-22 13:12:24'),
(274, 25, 'Rugged Outdoor Design', 'Built to withstand harsh environments with weatherproof housing and wind resistance up to 200 km/h.', 5, '2026-08-22 13:12:24'),
(275, 25, 'Simple Installation', 'Quick to deploy with an integrated ball joint mount for flexible alignment.', 6, '2026-08-22 13:12:24'),
(276, 25, 'Power over Ethernet (PoE)', 'Powered via passive PoE (24V) for easy installation with fewer cables.', 7, '2026-08-22 13:12:24'),
(277, 25, 'AirOS Management', 'Includes Ubiquiti’s AirOS interface for device configuration, monitoring, and network management.', 8, '2026-08-22 13:12:24'),
(278, 26, 'High-Performance Router', 'Delivers fast and reliable networking for homes, offices, ISPs, and growing businesses.', 1, '2026-08-22 13:38:08'),
(279, 26, '8 Gigabit Ethernet Ports', 'Connect multiple computers, switches, CCTV systems, and access points with high-speed wired connections.', 2, '2026-08-22 13:38:08'),
(280, 26, '2.5G SFP Port', 'Supports high-speed fiber uplinks, making it ideal for modern ISP and business networks.', 3, '2026-08-22 13:38:08'),
(281, 26, 'Advanced RouterOS', 'Includes powerful features such as firewall, VPN, hotspot, bandwidth management, and load balancing.', 4, '2026-08-22 13:38:08'),
(282, 26, 'Fast & Reliable Performance', 'Designed to handle multiple users and heavy network traffic with stable performance.', 5, '2026-08-22 13:38:08'),
(283, 26, 'PoE Support', 'Supports PoE input and PoE output for powering compatible network devices with fewer cables.', 6, '2026-08-22 13:38:08'),
(284, 26, 'USB Port', 'Connect supported LTE modems or storage devices for added flexibility.', 7, '2026-08-22 13:38:08'),
(285, 26, 'Built for Professional Networks', 'Perfect for ISPs, schools, hotels, offices, and enterprise network installations.', 8, '2026-08-22 13:38:08'),
(302, 27, '5 Gigabit Ethernet Ports', 'Connect computers, access points, CCTV systems, routers, and other network devices at high speed.', 1, '2026-08-22 14:52:50'),
(303, 27, 'Gigabit Network Performance', 'Provides fast and stable wired connections for demanding network applications.', 2, '2026-08-22 14:52:50'),
(304, 27, 'SFP Port', 'Connect fiber modules for longer-distance network links and easy network expansion.', 3, '2026-08-22 14:52:50'),
(305, 27, 'Easy Network Management', 'Manage and configure the switch easily through its web-based interface.', 4, '2026-08-22 14:52:50'),
(306, 27, 'Compact & Reliable', 'Small, space-saving design that fits neatly into homes, offices, shops, and network cabinets.', 5, '2026-08-22 14:52:50'),
(307, 27, 'Power over Ethernet Support', 'Can be powered through compatible Ethernet connections, making installations cleaner and easier.', 6, '2026-08-22 14:52:50'),
(308, 27, 'VLAN Support', 'Helps separate network traffic for better organization, security, and network management.', 7, '2026-08-22 14:52:50'),
(309, 27, 'Fiber & Copper Connectivity', 'Use Ethernet for nearby devices and the SFP port for fiber connections over longer distances.', 8, '2026-08-22 14:52:50'),
(310, 28, 'High-Performance Routing', 'Handles busy networks smoothly, making it ideal for businesses, ISPs, offices, and networks with many users.', 1, '2026-08-22 15:08:58'),
(311, 28, '10 Gigabit Ethernet Ports', 'Connect multiple switches, access points, computers, CCTV systems, and other devices without needing an additional switch.', 2, '2026-08-22 15:08:58'),
(312, 28, '10G SFP+ Fiber Port', 'Ready for high-speed fiber uplinks, giving your network faster backbone connections and room to grow.', 3, '2026-08-22 15:08:58'),
(313, 28, 'Advanced Network Management', 'Control bandwidth, users, VPNs, firewall rules, routing, and hotspots from one powerful device.', 4, '2026-08-22 15:08:58'),
(314, 28, 'Built for Growing Networks', 'A reliable choice for networks that need more users, higher speeds, and advanced features as they expand.', 5, '2026-08-22 15:08:58'),
(315, 28, 'Powerful Security', 'Built-in firewall and VPN features help protect your network and control who can access it.', 6, '2026-08-22 15:08:58'),
(316, 28, 'Reliable Multi-User Performance', 'Designed to keep multiple users and devices connected with stable network performance.', 7, '2026-08-22 15:08:58'),
(317, 28, 'Rack-Mount Ready', 'Fits neatly into professional network racks for clean and organized installations.', 8, '2026-08-22 15:08:58'),
(318, 28, 'USB 3.0 Connectivity', 'Provides additional flexibility for supported USB devices such as storage and LTE modems.', 9, '2026-08-22 15:08:58'),
(319, 28, 'Perfect for ISPs & Businesses', 'Excellent for ISPs, hotels, schools, offices, CCTV networks, and other demanding network environments.', 10, '2026-08-22 15:08:58'),
(335, 30, '5 Gigabit Ethernet Ports', 'Connect computers, switches, CCTV systems, access points, and other devices using fast and reliable wired connections.', 1, '2026-08-22 15:27:08'),
(336, 30, 'Powerful RouterOS', 'Gives you advanced control over bandwidth, firewall, VPN, routing, hotspot, and other network features.', 2, '2026-08-22 15:27:08'),
(337, 30, 'SFP Fiber Port', 'Connect a fiber module for long-distance links and easy expansion of your network.', 3, '2026-08-22 15:27:08'),
(338, 30, 'PoE Support', 'Power compatible network devices through Ethernet, helping reduce cables and simplify installations.', 4, '2026-08-22 15:27:08'),
(339, 30, 'Ideal for Small & Growing Networks', 'A reliable solution for homes, offices, shops, CCTV installations, and small ISP networks.', 5, '2026-08-22 15:27:08'),
(340, 30, 'Dual-Core Performance', 'Provides smooth routing performance when handling multiple users and network connections.', 6, '2026-08-22 15:27:08'),
(341, 30, 'Advanced Security', 'Built-in firewall and VPN capabilities help protect your network and control access.', 7, '2026-08-22 15:27:08'),
(342, 30, 'Compact Design', 'Small and space-saving, making it easy to install in homes, offices, cabinets, and network rooms.', 8, '2026-08-22 15:27:08'),
(343, 30, 'USB Connectivity', 'Supports compatible USB devices such as storage and LTE modems for additional network flexibility.', 9, '2026-08-22 15:27:08'),
(360, 31, 'Reliable 2.4GHz Wi-Fi', 'Provides dependable wireless connectivity for browsing, streaming, phones, laptops, and everyday internet use.', 1, '2026-08-26 14:00:33'),
(361, 31, '4 Ethernet Ports', 'Connect computers, Smart TVs, CCTV systems, switches, and other wired network devices with ease.', 2, '2026-08-26 14:00:33'),
(362, 31, 'Powerful RouterOS', 'Gives you advanced control over your network, including firewall, bandwidth management, hotspot, and VPN features.', 3, '2026-08-26 14:00:33'),
(363, 31, 'Compact & Space-Saving', 'Its small design fits easily on a desk, shelf, or other limited spaces without taking up much room.', 4, '2026-08-26 14:00:33'),
(364, 31, 'Easy to Manage', 'Configure and control your network using MikroTik\'s simple management tools for reliable everyday operation.', 5, '2026-08-26 14:00:33'),
(365, 31, '300Mbps Wireless Speed', 'Provides enough wireless speed for everyday browsing, social media, email, video streaming, and online work.', 6, '2026-08-26 14:00:33'),
(366, 31, 'Guest Network Support', 'Create a separate network for visitors while keeping your main network protected.', 7, '2026-08-26 14:00:33'),
(367, 31, 'Network Security', 'Built-in firewall and security features help protect connected devices and control network access.', 8, '2026-08-26 14:00:33'),
(376, 32, 'Fast & Stable Wi-Fi', 'Provides reliable 2.4GHz wireless coverage for homes, offices, and small businesses.', 1, '2026-09-02 15:09:27'),
(377, 32, '5 Ethernet Ports', 'Connect multiple computers, CCTV systems, switches, or access points with ease.', 2, '2026-09-02 15:09:27'),
(378, 32, 'Powerful RouterOS', 'Enjoy advanced networking features including firewall, bandwidth management, VPN, and hotspot support.', 3, '2026-09-02 15:09:27'),
(379, 32, 'Perfect for Hotspot Networks', 'Ideal for cyber cafés, schools, hotels, offices, and ISP hotspot installations.', 4, '2026-09-02 15:09:27'),
(380, 32, 'Compact & Reliable', 'Small, durable design that fits neatly in any home or office environment.', 5, '2026-09-02 15:09:27'),
(381, 32, 'Easy to Manage', 'Configure and monitor your network easily using WinBox, WebFig, or the MikroTik mobile app.', 6, '2026-09-02 15:09:27'),
(382, 32, 'Supports PoE Input', 'Can be powered through Ethernet, making installation cleaner and more flexible.', 7, '2026-09-02 15:09:27'),
(383, 32, 'Secure Network', 'Protect your users with built-in firewall rules, access control, and network security features.', 8, '2026-09-02 15:09:27');

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `image` varchar(255) NOT NULL,
  `alt_text` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_primary` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_images`
--

INSERT INTO `product_images` (`id`, `product_id`, `image`, `alt_text`, `sort_order`, `is_primary`, `created_at`) VALUES
(1, 1, 'Tenda F3.jpg', 'Tenda F3 N300 Wireless Wi-Fi Router', 1, 1, '2026-08-07 15:15:56'),
(2, 1, 'tenda f3 front.jpg', 'Tenda F3 N300 Router Front View', 2, 0, '2026-08-07 15:15:57'),
(3, 1, 'tenda f3 side.jpg', 'Tenda F3 N300 Router Side View', 3, 0, '2026-08-07 15:15:57'),
(4, 1, 'tenda f3 back.jpg', 'Tenda F3 N300 Router Back View', 4, 0, '2026-08-07 15:15:57'),
(5, 1, 'tenda f3 all.jpg', 'Tenda F3 N300 Router Complete View', 5, 0, '2026-08-07 15:15:57'),
(23, 8, '1786463284_b9c83d63cfe6.webp', 'Tenda AC5 V3 AC1200 Wireless Dual Band Wi-Fi Router', 1, 1, '2026-08-11 15:48:04'),
(24, 8, '1786463284_b1719a33b7d2.webp', 'Tenda AC5 V3 AC1200 Wireless Dual Band Wi-Fi Router - Image 2', 2, 0, '2026-08-11 15:48:04'),
(25, 8, '1786463284_73eb094942ff.jpg', 'Tenda AC5 V3 AC1200 Wireless Dual Band Wi-Fi Router - Image 3', 3, 0, '2026-08-11 15:48:04'),
(26, 8, '1786463284_ce2aa4c6cdc6.webp', 'Tenda AC5 V3 AC1200 Wireless Dual Band Wi-Fi Router - Image 4', 4, 0, '2026-08-11 15:48:04'),
(27, 8, '1786463284_973bdd89ec0e.jpg', 'Tenda AC5 V3 AC1200 Wireless Dual Band Wi-Fi Router - Image 5', 5, 0, '2026-08-11 15:48:04'),
(28, 9, '1786608737_6bf4f86d25f2.jpg', 'Ruijie Reyee RG‑RAP6262(G) Outdoor AP', 1, 1, '2026-08-13 08:12:17'),
(29, 9, '1786608737_2c30e593ede8.jpg', 'Ruijie Reyee RG‑RAP6262(G) Outdoor AP - Image 2', 2, 0, '2026-08-13 08:12:17'),
(30, 9, '1786608737_e42de28aa4ca.jpg', 'Ruijie Reyee RG‑RAP6262(G) Outdoor AP - Image 3', 3, 0, '2026-08-13 08:12:17'),
(31, 10, '1786685544_b15751479818.webp', 'ADSS Fiber Cable 12 Core 4KM', 1, 1, '2026-08-14 05:32:24'),
(32, 12, '1786686432_bbb705bd5ad7.jpg', 'CP Cat 6 Indoor Cable', 1, 1, '2026-08-14 05:47:12'),
(33, 13, '1786687027_a780df006ccc.webp', 'Drop Cable 1KM 2 Core', 1, 1, '2026-08-14 05:57:07'),
(34, 11, '1786687102_69290e3433c3.webp', 'ADSS Fiber Cable 24 Core 4KM', 1, 1, '2026-08-14 05:58:22'),
(35, 14, '1786687728_cdd9269d45c8.jpg', 'Drop Cable 1KM 4 Core', 1, 1, '2026-08-14 06:08:48'),
(36, 15, '1786688027_4cd325187c46.jpg', 'Drop Cable 1KM 8 Core', 1, 1, '2026-08-14 06:13:47'),
(37, 16, '1786713198_a141830117f6.jpg', 'Drop Cable 2KM 2 Core', 1, 1, '2026-08-14 13:13:18'),
(38, 17, '1786713704_64d0d99b3a16.jpg', 'Drop Cable 2KM 4-Core', 1, 1, '2026-08-14 13:21:44'),
(39, 18, '1786715050_d57f689bf730.jpg', 'Drop Cable 2KM 8 Core', 1, 1, '2026-08-14 13:44:10'),
(40, 19, '1786716056_e41a3d9a407c.jpg', 'Drop Cable 2KM Indoor', 1, 1, '2026-08-14 14:00:56'),
(41, 20, '1786716759_6d673c4dffb1.jpg', 'Dual-Modular Crimping Tool', 1, 1, '2026-08-14 14:12:39'),
(42, 21, '1786717263_521e8372be18.jpg', 'Easenet Cat 6 Indoor Cable', 1, 1, '2026-08-14 14:21:03'),
(43, 22, '1787390261_64f15ec485da.png', 'Gigalan CAT 6 Indoor Cable', 1, 1, '2026-08-22 09:17:41'),
(44, 23, '1787390807_775f3b9e78dc.webp', 'Huawei XPON Echolife Router HG8546M', 1, 1, '2026-08-22 09:26:47'),
(45, 24, '1787391581_05097d62e17f.jpg', 'Jakemy JM-CT4-3 Crimping Tool', 1, 1, '2026-08-22 09:39:41'),
(46, 25, '1787403313_dbf2a416d9c7.png', 'LiteBeam M5-Ubiquiti', 1, 1, '2026-08-22 12:55:13'),
(47, 25, '1787403313_2c21e3d67fdf.webp', 'LiteBeam M5-Ubiquiti - Image 2', 2, 0, '2026-08-22 12:55:13'),
(48, 25, '1787403313_8c1f3d4adc71.webp', 'LiteBeam M5-Ubiquiti - Image 3', 3, 0, '2026-08-22 12:55:13'),
(49, 25, '1787403313_d22fbbccbe06.webp', 'LiteBeam M5-Ubiquiti - Image 4', 4, 0, '2026-08-22 12:55:13'),
(50, 25, '1787403313_6af582a959ce.webp', 'LiteBeam M5-Ubiquiti - Image 5', 5, 0, '2026-08-22 12:55:13'),
(51, 25, '1787403313_ee73824fa7be.png', 'LiteBeam M5-Ubiquiti - Image 6', 6, 0, '2026-08-22 12:55:13'),
(52, 25, '1787403313_2257111376a7.jpg', 'LiteBeam M5-Ubiquiti - Image 7', 7, 0, '2026-08-22 12:55:13'),
(53, 25, '1787403313_1cc96e0d0345.webp', 'LiteBeam M5-Ubiquiti - Image 8', 8, 0, '2026-08-22 12:55:13'),
(54, 26, '1787405888_6c3039f08601.png', 'Mikrotik L009UIGS-2HAXD-IN Router', 1, 1, '2026-08-22 13:38:08'),
(55, 26, '1787405888_300325ab2597.png', 'Mikrotik L009UIGS-2HAXD-IN Router - Image 2', 2, 0, '2026-08-22 13:38:08'),
(56, 26, '1787405888_a48e0de8fdfb.png', 'Mikrotik L009UIGS-2HAXD-IN Router - Image 3', 3, 0, '2026-08-22 13:38:08'),
(57, 27, '1787410253_35f94ef7738e.png', 'Mikrotik RB260GS Series', 1, 1, '2026-08-22 14:50:53'),
(58, 27, '1787410253_5d5e48de2690.png', 'Mikrotik RB260GS Series - Image 2', 2, 0, '2026-08-22 14:50:53'),
(61, 27, '1787410331_6de9dfc9d59a.webp', 'Mikrotik RB260GS Series - Image 5', 5, 0, '2026-08-22 14:52:11'),
(62, 28, '1787411338_dbf5ed274a57.jpg', 'MikroTik RB4011iGS+RM Ethernet Router', 1, 1, '2026-08-22 15:08:58'),
(63, 28, '1787411338_4616db14f43d.jpg', 'MikroTik RB4011iGS+RM Ethernet Router - Image 2', 2, 0, '2026-08-22 15:08:58'),
(64, 28, '1787411338_d20f301a53c6.png', 'MikroTik RB4011iGS+RM Ethernet Router - Image 3', 3, 0, '2026-08-22 15:08:58'),
(65, 28, '1787411338_dbd0043482e8.jpg', 'MikroTik RB4011iGS+RM Ethernet Router - Image 4', 4, 0, '2026-08-22 15:08:58'),
(67, 30, '1787412428_dd55de52efe2.jpg', 'Mikrotik RB760iGS hEX S 5x', 1, 1, '2026-08-22 15:27:08'),
(68, 30, '1787412428_a2de3a6d710c.webp', 'Mikrotik RB760iGS hEX S 5x - Image 2', 2, 0, '2026-08-22 15:27:08'),
(69, 31, '1787752575_cec87816fd03.jpg', 'Mikrotik hAP Lite TC RB941', 1, 1, '2026-08-26 13:56:15'),
(70, 31, '1787752575_15fc4e358ab1.jpg', 'Mikrotik hAP Lite TC RB941 - Image 2', 2, 0, '2026-08-26 13:56:15'),
(71, 31, '1787752575_aacc2cbaa11b.jpg', 'Mikrotik hAP Lite TC RB941 - Image 3', 3, 0, '2026-08-26 13:56:15'),
(72, 31, '1787752575_30e9c49bcde5.webp', 'Mikrotik hAP Lite TC RB941 - Image 4', 4, 0, '2026-08-26 13:56:15'),
(73, 32, '1788361767_f55be6c4220a.jpg', 'Mikrotik RB951Ui-2HnD Router', 1, 1, '2026-09-02 15:09:27'),
(74, 32, '1788361767_504efd7ebbb1.jpg', 'Mikrotik RB951Ui-2HnD Router - Image 2', 2, 0, '2026-09-02 15:09:27'),
(75, 32, '1788361767_f817b1ac320e.jpg', 'Mikrotik RB951Ui-2HnD Router - Image 3', 3, 0, '2026-09-02 15:09:27'),
(76, 32, '1788361767_fd8df57c8d01.webp', 'Mikrotik RB951Ui-2HnD Router - Image 4', 4, 0, '2026-09-02 15:09:27'),
(77, 32, '1788361767_a580ef0a8c89.png', 'Mikrotik RB951Ui-2HnD Router - Image 5', 5, 0, '2026-09-02 15:09:27');

-- --------------------------------------------------------

--
-- Table structure for table `product_specifications`
--

CREATE TABLE `product_specifications` (
  `id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `specification_group` varchar(150) DEFAULT NULL,
  `specification_name` varchar(150) NOT NULL,
  `specification_value` text NOT NULL,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_tags`
--

CREATE TABLE `product_tags` (
  `product_id` int(10) UNSIGNED NOT NULL,
  `tag_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_variants`
--

CREATE TABLE `product_variants` (
  `id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `variant_name` varchar(150) NOT NULL,
  `sku` varchar(100) DEFAULT NULL,
  `price` decimal(12,2) DEFAULT NULL,
  `stock_quantity` int(11) DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quote_items`
--

CREATE TABLE `quote_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `inquiry_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `quantity` int(10) UNSIGNED DEFAULT 1,
  `price` decimal(12,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `redirects`
--

CREATE TABLE `redirects` (
  `id` int(10) UNSIGNED NOT NULL,
  `old_url` varchar(255) NOT NULL,
  `new_url` varchar(255) NOT NULL,
  `redirect_type` smallint(6) DEFAULT 301,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `customer_name` varchar(150) NOT NULL,
  `customer_email` varchar(150) DEFAULT NULL,
  `rating` tinyint(3) UNSIGNED NOT NULL,
  `review` text NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(10) UNSIGNED NOT NULL,
  `setting_key` varchar(150) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES
(1, 'site_name', 'CORE LTD', '2026-08-07 09:45:40'),
(2, 'site_description', 'Networking, Fiber, CCTV and IT Solutions', '2026-08-07 09:45:40'),
(3, 'site_phone', '', '2026-08-07 09:45:40'),
(4, 'site_email', '', '2026-08-07 09:45:40'),
(5, 'site_address', '', '2026-08-07 09:45:40'),
(6, 'whatsapp_number', '', '2026-08-07 09:45:40'),
(7, 'currency', 'KES', '2026-08-07 09:45:40'),
(8, 'currency_symbol', 'KSh', '2026-08-07 09:45:40'),
(9, 'facebook_url', '', '2026-08-07 09:45:40'),
(10, 'instagram_url', '', '2026-08-07 09:45:40'),
(11, 'youtube_url', '', '2026-08-07 09:45:40'),
(12, 'tiktok_url', '', '2026-08-07 09:45:40'),
(13, 'site_logo', '', '2026-08-07 09:45:40'),
(14, 'site_favicon', '', '2026-08-07 09:45:40');

-- --------------------------------------------------------

--
-- Table structure for table `stock_movements`
--

CREATE TABLE `stock_movements` (
  `id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `movement_type` enum('purchase','sale','adjustment','return','damage') NOT NULL,
  `quantity` int(11) NOT NULL,
  `previous_quantity` int(11) DEFAULT 0,
  `new_quantity` int(11) DEFAULT 0,
  `reference` varchar(150) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE `tags` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tags`
--

INSERT INTO `tags` (`id`, `name`, `slug`, `created_at`) VALUES
(3, 'Wifi Router', 'wifi-router', '2026-08-11 10:53:40');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','manager','staff') DEFAULT 'staff',
  `status` tinyint(1) DEFAULT 1,
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `password`, `role`, `status`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'ALEX GITAHI', 'alex.gtahi@gmail.com', '0757130130', '$2y$10$H/d9FZ39dOCi28MtT6P8w.uYrZetgIHhMtbFsGLZeAS65U0b09/4.', 'admin', 1, '2026-09-02 17:59:21', '2026-08-09 18:22:42', '2026-09-02 14:59:21');

-- --------------------------------------------------------

--
-- Table structure for table `wishlists`
--

CREATE TABLE `wishlists` (
  `id` int(10) UNSIGNED NOT NULL,
  `session_id` varchar(255) DEFAULT NULL,
  `customer_email` varchar(150) DEFAULT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_activity_user` (`user_id`);

--
-- Indexes for table `admin_notifications`
--
ALTER TABLE `admin_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_order_id` (`order_id`),
  ADD KEY `idx_is_read` (`is_read`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `fk_category_parent` (`parent_id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_event_date` (`event_date`);

--
-- Indexes for table `inquiries`
--
ALTER TABLE `inquiries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_inquiry_product` (`product_id`),
  ADD KEY `idx_inquiries_status` (`status`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_number` (`order_number`),
  ADD KEY `idx_order_number` (`order_number`),
  ADD KEY `idx_customer_phone` (`customer_phone`),
  ADD KEY `idx_order_status` (`order_status`),
  ADD KEY `idx_payment_status` (`payment_status`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `order_activity_logs`
--
ALTER TABLE `order_activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `admin_id` (`admin_id`),
  ADD KEY `created_at` (`created_at`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_order_id` (`order_id`),
  ADD KEY `idx_product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD UNIQUE KEY `sku` (`sku`),
  ADD KEY `idx_products_category` (`category_id`),
  ADD KEY `idx_products_brand` (`brand_id`),
  ADD KEY `idx_products_status` (`status`),
  ADD KEY `idx_products_featured` (`featured`),
  ADD KEY `idx_products_name` (`name`),
  ADD KEY `idx_products_created` (`created_at`);

--
-- Indexes for table `product_features`
--
ALTER TABLE `product_features`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_product_features_product` (`product_id`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_product_images_product` (`product_id`);

--
-- Indexes for table `product_specifications`
--
ALTER TABLE `product_specifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_product_specs_product` (`product_id`);

--
-- Indexes for table `product_tags`
--
ALTER TABLE `product_tags`
  ADD PRIMARY KEY (`product_id`,`tag_id`),
  ADD KEY `fk_product_tags_tag` (`tag_id`);

--
-- Indexes for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sku` (`sku`),
  ADD KEY `fk_variant_product` (`product_id`);

--
-- Indexes for table `quote_items`
--
ALTER TABLE `quote_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_quote_item_inquiry` (`inquiry_id`),
  ADD KEY `fk_quote_item_product` (`product_id`);

--
-- Indexes for table `redirects`
--
ALTER TABLE `redirects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `old_url` (`old_url`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_reviews_product` (`product_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_stock_product` (`product_id`),
  ADD KEY `fk_stock_user` (`created_by`);

--
-- Indexes for table `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `wishlists`
--
ALTER TABLE `wishlists`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_wishlist_product` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `admin_notifications`
--
ALTER TABLE `admin_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `inquiries`
--
ALTER TABLE `inquiries`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `order_activity_logs`
--
ALTER TABLE `order_activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `product_features`
--
ALTER TABLE `product_features`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=384;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT for table `product_specifications`
--
ALTER TABLE `product_specifications`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_variants`
--
ALTER TABLE `product_variants`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quote_items`
--
ALTER TABLE `quote_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `redirects`
--
ALTER TABLE `redirects`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `stock_movements`
--
ALTER TABLE `stock_movements`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tags`
--
ALTER TABLE `tags`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `wishlists`
--
ALTER TABLE `wishlists`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `fk_activity_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `fk_category_parent` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `inquiries`
--
ALTER TABLE `inquiries`
  ADD CONSTRAINT `fk_inquiry_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_order_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_product_brand` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_product_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `product_features`
--
ALTER TABLE `product_features`
  ADD CONSTRAINT `fk_product_features_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `fk_product_images_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `product_specifications`
--
ALTER TABLE `product_specifications`
  ADD CONSTRAINT `fk_product_specs_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `product_tags`
--
ALTER TABLE `product_tags`
  ADD CONSTRAINT `fk_product_tags_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_product_tags_tag` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD CONSTRAINT `fk_variant_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `quote_items`
--
ALTER TABLE `quote_items`
  ADD CONSTRAINT `fk_quote_item_inquiry` FOREIGN KEY (`inquiry_id`) REFERENCES `inquiries` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_quote_item_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `fk_review_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD CONSTRAINT `fk_stock_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_stock_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `wishlists`
--
ALTER TABLE `wishlists`
  ADD CONSTRAINT `fk_wishlist_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
