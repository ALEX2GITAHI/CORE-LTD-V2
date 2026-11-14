-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 14, 2026 at 01:49 PM
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
(15, 'Crimping Tool', 'crimping-tool', '', NULL, '', 1, '2026-08-14 14:05:44', '2026-08-14 14:05:44'),
(16, 'Starlink', 'starlink', '', '1791631298_0f3711f41c.webp', 'https://starlink.com/?srsltid=AU7gw4X4tIPSdQEf383kitYFPhP7T3aZWU-U6ppJT7Sg_guCSOE3FaCR', 1, '2026-10-10 11:21:38', '2026-10-10 11:21:38');

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
(10, NULL, 'Receiver', 'receiver', '', NULL, 1, 0, '2026-08-22 12:48:24', '2026-08-22 12:48:24'),
(11, NULL, 'Stand-Alone Camera', 'atand-alone-camera', '', NULL, 1, 0, '2026-10-31 09:23:46', '2026-10-31 09:28:06'),
(12, NULL, 'Normal DVR Cameras', 'normal-dvr-cameras', '', NULL, 1, 0, '2026-10-31 09:24:25', '2026-10-31 09:24:25'),
(13, NULL, 'IP-NVR Cameras', 'ip-nvr-cameras', '', NULL, 1, 0, '2026-10-31 09:24:45', '2026-10-31 09:24:45');

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
(1, 'Ruijie Certified Training', 'ruijie-certified-training', '2026-07-25', '6:30 PM', 'PCEA Githurai Church School Hall', 'Ruijie Certified Training\r\n\r\nJoin us for an exciting Ruijie Certified Training designed for networking professionals, technicians, students, entrepreneurs, and anyone interested in building practical skills in modern networking solutions.\r\n\r\nThe training will provide an opportunity to learn, connect, and grow while exploring Ruijie/Reyee networking products and the Ruijie Cloud platform. Participants will gain practical insights into cloud-based network management, product configuration, deployment, monitoring, and solutions for Wi-Fi businesses and hotspots.\r\n\r\nWhether you are looking to improve your networking skills, start a Wi-Fi or hotspot business, or learn more about Ruijie/Reyee technologies, this training offers a valuable opportunity to interact with networking experts and discover real-world solutions.\r\n\r\nTopics & Highlights:\r\n\r\nRuijie/Reyee networking products and solutions\r\nRuijie Cloud network management\r\nWi-Fi and hotspot business opportunities\r\nPractical networking skills and deployment\r\nNetwork monitoring and management\r\nBuilding and growing your networking business\r\nInteraction with Ruijie products and solutions\r\n\r\nDate: 25th July 2025\r\nTime: From 6:00 PM\r\nVenue: PCEA Githurai Progressive School Hall\r\nAdmission: 100% FREE\r\n\r\nCome and learn, connect, and grow with Ruijie/Reyee and CORE LTD. Don\'t miss this opportunity to expand your networking knowledge and discover new business opportunities.', 'uploads/events/ruijie-certified-training-1787604867.jpg', 1, '2026-08-24 20:54:27', '2026-08-24 20:54:27'),
(2, 'Ruijie Certified Training 2.0', 'ruijie-certified-training-2-0', '2026-10-31', '6:30 PM', 'PCEA Githurai Church School Hall', 'CORE LTD invites you to an exciting FREE RUIJIE CERTIFIED TRAINING!\r\n\r\nCome and learn, explore Ruijie products, discover Ruijie Cloud management and sharpen your networking skills.', 'uploads/events/ruijie-certified-training-2-0-1794647275.png', 1, '2026-11-14 09:07:55', '2026-11-14 09:07:55');

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
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `module` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `description`, `module`, `created_at`) VALUES
(1, 'view_products', 'View products', 'Products', '2026-09-07 06:07:17'),
(2, 'add_products', 'Add products', 'Products', '2026-09-07 06:07:17'),
(3, 'edit_products', 'Edit products', 'Products', '2026-09-07 06:07:17'),
(4, 'delete_products', 'Delete products', 'Products', '2026-09-07 06:07:17'),
(5, 'view_categories', 'View categories', 'Categories', '2026-09-07 06:07:17'),
(6, 'add_categories', 'Add categories', 'Categories', '2026-09-07 06:07:17'),
(7, 'edit_categories', 'Edit categories', 'Categories', '2026-09-07 06:07:17'),
(8, 'delete_categories', 'Delete categories', 'Categories', '2026-09-07 06:07:17'),
(9, 'view_brands', 'View brands', 'Brands', '2026-09-07 06:07:17'),
(10, 'add_brands', 'Add brands', 'Brands', '2026-09-07 06:07:17'),
(11, 'edit_brands', 'Edit brands', 'Brands', '2026-09-07 06:07:17'),
(12, 'delete_brands', 'Delete brands', 'Brands', '2026-09-07 06:07:17'),
(13, 'view_orders', 'View orders', 'Orders', '2026-09-07 06:07:17'),
(14, 'edit_orders', 'Edit orders', 'Orders', '2026-09-07 06:07:17'),
(15, 'delete_orders', 'Delete orders', 'Orders', '2026-09-07 06:07:17'),
(16, 'view_reviews', 'View reviews', 'Reviews', '2026-09-07 06:07:17'),
(17, 'manage_reviews', 'Manage reviews', 'Reviews', '2026-09-07 06:07:17'),
(18, 'view_events', 'View events', 'Events', '2026-09-07 06:07:17'),
(19, 'add_events', 'Add events', 'Events', '2026-09-07 06:07:17'),
(20, 'edit_events', 'Edit events', 'Events', '2026-09-07 06:07:17'),
(21, 'delete_events', 'Delete events', 'Events', '2026-09-07 06:07:17'),
(22, 'view_users', 'View users', 'Users', '2026-09-07 06:07:17'),
(23, 'add_users', 'Add users', 'Users', '2026-09-07 06:07:17'),
(24, 'edit_users', 'Edit users', 'Users', '2026-09-07 06:07:17'),
(25, 'delete_users', 'Delete users', 'Users', '2026-09-07 06:07:17'),
(26, 'manage_permissions', 'Assign and manage user permissions', 'System', '2026-09-07 06:07:17'),
(27, 'system_settings', 'Manage system settings', 'System', '2026-09-07 06:07:17');

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
(13, 5, 14, 'Drop Cable 1KM 2 Core', 'drop-cable-1km-2-core', 'Drop 1KM', '', '', 12500.00, NULL, NULL, 16, 5, '1786687027_a780df006ccc.webp', 0, 1, 0, 'Drop 1KM', '', 'Drop', '2026-08-14 05:57:07', '2026-09-05 06:17:51'),
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
(32, 1, 1, 'Mikrotik RB951Ui-2HnD Router', 'mikrotik-rb951ui-2hnd-router', 'Mikrotik RB951', '', '', 9000.00, 9500.00, NULL, 0, 5, '1788361767_f55be6c4220a.jpg', 1, 1, 0, 'Mikrotik RB951', '', 'Mikrotik', '2026-09-02 15:08:02', '2026-09-02 15:09:27'),
(33, 10, 1, 'Mikrotik SXTsq Series', 'mikrotik-sxtsq-series', 'Mikrotik SXT', '', '', 8000.00, NULL, NULL, 10, 5, '1788363234_89658944764b.jpg', 1, 1, 0, 'Mikrotik SXT', '', 'receiver', '2026-09-02 15:33:54', '2026-09-02 15:33:54'),
(34, 1, 4, 'PowerBeam M5', 'powerbeam-m5', 'PowerBeam M5', '', '', 9500.00, 9800.00, NULL, 10, 5, '1788363934_39c5dbd3b571.jpeg', 1, 1, 0, 'PowerBeam M5', '', 'receiver', '2026-09-02 15:45:34', '2026-09-02 15:45:34'),
(35, 1, 13, 'RDCOM Cat 6 Indoor Cable', 'rdcom-cat-6-indoor-cable', 'RDCOM Cat 6 Indoor', '', '', 4800.00, NULL, NULL, 14, 5, '1788596567_7514c93693a5.webp', 1, 1, 0, 'RDCOM Cat 6 Indoor', '', '', '2026-09-05 08:22:47', '2026-09-05 08:22:47'),
(36, 2, 5, 'Ruijie | Reyee RG-EW300N Router', 'ruijie-reyee-rg-ew300n-router', 'Ruijie RG-EW300N Router', '', '', 1900.00, 2000.00, NULL, 10, 5, '1791555876_48a7efb0af6f.jpg', 1, 1, 0, 'Ruijie RG-EW300N Router', '', 'Ruijie RG-EW300N Router', '2026-10-09 14:24:36', '2026-10-09 14:24:36'),
(37, 3, 5, 'Ruijie Reyee RG-ES206GS-P PoE+ Switch', 'ruijie-reyee-rg-es206gs-p-poe-switch', 'Ruijie RG-ES206GS-P 4-PORT', '', '', 7500.00, 7800.00, NULL, 10, 5, '1791557099_2b69bef31022.webp', 1, 1, 0, 'Ruijie RG-ES206GS-P 4-PORT', '', 'Ruijie RG-ES206GS-P 4-PORT', '2026-10-09 14:44:59', '2026-10-09 14:44:59'),
(38, 2, 5, 'Ruijie Reyee RG-EW1200 AC1200 Dual-Band Wireless Router', 'ruijie-reyee-rg-ew1200-ac1200-dual-band-wireless-router', 'Ruijie RG-EW1200 Router', '', '', 3000.00, 3200.00, NULL, 10, 5, '1791559697_c674f4430369.png', 1, 1, 0, 'Ruijie RG-EW1200 Router', '', 'Ruijie RG-EW1200 Router', '2026-10-09 15:28:17', '2026-10-09 15:28:17'),
(39, 4, 5, 'Ruijie Reyee RG-RAP62-OD AX3000 WIFI 6 Outdoor Access Point', 'ruijie-reyee-rg-rap62-od-ax3000-wifi-6-outdoor-access-point', 'Ruijie RG-RAP62-OD', '', '', 13500.00, 14000.00, NULL, 10, 5, '1791560028_aa7a98d015f2.jpg', 1, 1, 0, 'Ruijie RG-RAP62-OD', '', 'Ruijie RG-RAP62-OD', '2026-10-09 15:33:48', '2026-10-09 15:33:48'),
(40, 1, 13, 'Scoop Cat 6 Indoor Cable', 'scoop-cat-6-indoor-cable', 'Scoop Cat 6 Indoor', '', '', 3800.00, 3900.00, NULL, 10, 5, '1791629404_ea8249a60023.webp', 1, 1, 0, 'Scoop Cat 6 Indoor', '', 'Scoop Cat 6 Indoor', '2026-10-10 10:50:04', '2026-10-10 10:50:04'),
(41, 5, 9, 'Signal Fire AI-9 Fusion Splicing Machine', 'signal-fire-ai-9-fusion-splicing-machine', 'Fusion Splicing Machine AI-9', '', '', 80000.00, 90000.00, NULL, 5, 5, '1791630536_f2f4d4e3980b.jpg', 1, 1, 0, 'Fusion Splicing Machine AI-9', '', 'Fusion Splicing Machine AI-9', '2026-10-10 11:08:56', '2026-10-10 11:08:56'),
(42, 1, 16, 'Starlink Gen 3 Standard Kit (V3)', 'starlink-gen-3-standard-kit-v3', 'Starlink Gen 3', '', '', 49000.00, 50000.00, NULL, 5, 5, '1791635511_29d52f5502c9.png', 1, 1, 0, 'Starlink Gen 3', '', 'Starlink Gen 3', '2026-10-10 12:31:51', '2026-10-30 11:42:21'),
(43, 4, 2, 'Tenda OAP1200 Outdoor Wi-Fi Access Point', 'tenda-oap1200-outdoor-wi-fi-access-point', 'Tenda OAP1200', '', '', 5300.00, 5500.00, NULL, 79, 5, '1791639670_6bd3aef87bc8.jpeg', 1, 1, 0, 'Tenda OAP1200', '', 'Tenda OAP1200', '2026-10-10 13:41:10', '2026-10-30 14:41:22'),
(44, 1, 16, 'Starlink Mini Satellite Internet Kit', 'starlink-mini-satellite-internet-kit', 'Starlink Mini Kit', '', '', 35000.00, NULL, 0.00, 5, 5, '1793361695_394cee4ae809.jpg', 1, 1, 0, 'Starlink Mini Kit', '', 'Starlink Mini Kit', '2026-10-30 12:01:35', '2026-10-30 12:01:35'),
(45, 2, 2, 'Tenda 4G03 N300 Wi-Fi 4G LTE Router.', 'tenda-4g03-n300-wi-fi-4g-lte-router', 'Tenda 4G LTE Router.', '', '', 5500.00, NULL, NULL, 9, 5, '1794649930_525a9c184ecd.jpg', 1, 1, 0, 'Tenda 4G LTE Router.', '', 'Tenda 4G LTE Router.', '2026-10-30 14:46:41', '2026-11-14 09:52:10'),
(46, 2, 2, 'Tenda AC10 AC1200 Dual-Band Gigabit Wi-Fi Router', 'tenda-ac10-ac1200-dual-band-gigabit-wi-fi-router', 'Tenda AC10', '', '', 4000.00, 5000.00, NULL, 10, 5, '1794649834_932adfda36b0.jpg', 1, 1, 0, 'Tenda AC10', '', 'Tenda AC10', '2026-10-30 14:53:27', '2026-11-14 09:50:34'),
(47, 2, 2, 'Tenda F6 N300 Wireless Wi-Fi Router', 'tenda-f6-n300-wireless-wi-fi-router', 'Tenda F6', '', '', 1600.00, NULL, NULL, 10, 5, '1793373120_40f766398ce5.jpg', 1, 1, 0, 'Tenda F6', '', 'Tenda F6', '2026-10-30 15:12:00', '2026-10-30 15:12:00'),
(48, 11, 2, 'Tenda CP7 5MP 3K Smart Wi-Fi Security Pan/Tilt Camera', 'tenda-cp7-5mp-3k-smart-wi-fi-security-pan-tilt-camera', 'Tenda CP7 Camera', '', '', 3000.00, 3500.00, NULL, 10, 5, '1793439336_c862b9dca88b.jpg', 1, 1, 0, 'Tenda CP7 Camera', '', 'Tenda CP7 Camera', '2026-10-31 09:35:36', '2026-10-31 09:35:36'),
(49, 11, 2, 'Tenda CP3 3MP 2K Smart Wi-Fi Security Pan/Tilt Camera', 'tenda-cp3-3mp-2k-smart-wi-fi-security-pan-tilt-camera', 'Tenda CP3 Camera', '', '', 2500.00, 2800.00, NULL, 10, 5, '1793440167_e80b52e12d78.jpg', 1, 1, 0, 'Tenda CP3 Camera', '', 'Tenda CP3 Camera', '2026-10-31 09:49:27', '2026-10-31 09:49:27'),
(50, 3, 2, 'Tenda SG108 8-Port Gigabit Ethernet Switch', 'tenda-sg108-8-port-gigabit-ethernet-switch', 'Tenda  8-Port Gigabit Switch', '', '', 2500.00, NULL, NULL, 10, 5, '1793441424_953989e29c01.jpeg', 1, 1, 0, 'Tenda  8-Port Gigabit Switch', '', 'Tenda  8-Port Gigabit Switch', '2026-10-31 10:10:24', '2026-10-31 10:10:24');

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
(383, 32, 'Secure Network', 'Protect your users with built-in firewall rules, access control, and network security features.', 8, '2026-09-02 15:09:27'),
(384, 33, 'Long-Distance Wireless Link', 'Connect two locations wirelessly over long distances without the need to run expensive cables.', 1, '2026-09-02 15:33:54'),
(385, 33, 'High-Gain Directional Antenna', 'Focuses the wireless signal toward another device for a stronger and more stable point-to-point connection.', 2, '2026-09-02 15:33:54'),
(386, 33, 'Weatherproof Outdoor Design', 'Built for outdoor installations and designed to withstand rain, dust, sun, and changing weather conditions.', 3, '2026-09-02 15:33:54'),
(387, 33, 'Compact & Lightweight', 'Small and easy to mount on poles or walls, making installation simple even in tight spaces.', 4, '2026-09-02 15:33:54'),
(388, 33, 'PoE Powered', 'Power and connect the device using one Ethernet cable, keeping outdoor installations neat and simple.', 5, '2026-09-02 15:33:54'),
(389, 33, 'Stable Point-to-Point Connection', 'Ideal for connecting offices, buildings, shops, CCTV locations, and other sites wirelessly.', 6, '2026-09-02 15:33:54'),
(390, 33, 'RouterOS Support', 'Provides advanced wireless and network management options for professional installations.', 7, '2026-09-02 15:33:54'),
(391, 33, 'Low Power Consumption', 'Designed to deliver reliable wireless performance while using minimal power.', 8, '2026-09-02 15:33:54'),
(392, 34, 'High-Speed Performance', 'Delivers up to 100+ Mbps throughput on the 5 GHz frequency band for point-to-point or CPE connections.', 1, '2026-09-02 15:45:34'),
(393, 34, 'Long-Distance Connectivity', 'Supports wireless links up to 30+ km with focused beam technology for reliable outdoor coverage.', 2, '2026-09-02 15:45:34'),
(394, 34, 'Directional Antenna Design', 'Integrated 23 dBi directional antenna provides strong signal quality with minimal interference.', 3, '2026-09-02 15:45:34'),
(395, 34, 'Lightweight & Compact', 'Weighs less than 1 kg with a sleek, highly efficient design—ideal for quick installations.', 4, '2026-09-02 15:45:34'),
(396, 34, 'AirOS & AirMax Technology', 'Features Ubiquiti’s AirOS for intuitive device management and AirMax TDMA protocol for scalable, low-latency networks.', 5, '2026-09-02 15:45:34'),
(397, 34, 'Robust Outdoor Build', 'Designed for harsh environments with UV-stabilized plastic and wind resistance up to 200 km/h.', 6, '2026-09-02 15:45:34'),
(398, 34, 'Power over Ethernet (PoE)', 'Powered via passive PoE adapter (24V), reducing cable clutter and simplifying deployment.', 7, '2026-09-02 15:45:34'),
(399, 13, 'Cable Type', '2 Core Fiber Optic Drop Cable (FTTH)', 1, '2026-09-05 06:17:51'),
(400, 13, 'Fiber Count', '2 Fibers (Single Mode)', 2, '2026-09-05 06:17:51'),
(401, 13, 'Application', 'Ideal for Fiber-to-the-Home (FTTH), last-mile connectivity, and outdoor installations.', 3, '2026-09-05 06:17:51'),
(402, 13, 'Length', '1000 Meters (1KM Roll)', 4, '2026-09-05 06:17:51'),
(403, 13, 'Strength Member', 'Reinforced with steel wire / FRP for added durability and tensile strength.', 5, '2026-09-05 06:17:51'),
(404, 13, 'Weather Resistance', 'Designed to withstand harsh environmental conditions including sun, rain, and wind.', 6, '2026-09-05 06:17:51'),
(405, 13, 'Low Signal Loss', 'Ensures stable and efficient data transmission over long distances.', 7, '2026-09-05 06:17:51'),
(406, 13, 'Lightweight & Flexible', 'Easy to handle, route, and install in residential and commercial setups.', 8, '2026-09-05 06:17:51'),
(407, 35, 'High-Speed Cat 6', 'Enjoy fast and reliable wired networking for internet, file sharing and other network applications.', 1, '2026-09-05 08:22:47'),
(408, 35, 'Gigabit Ethernet Support', 'Designed for Gigabit network connections, making it a great choice for modern home and business networks.', 2, '2026-09-05 08:22:47'),
(409, 35, 'Reliable Data Transmission', 'Provides a stable connection between computers, routers, switches, access points and other network devices.', 3, '2026-09-05 08:22:47'),
(410, 35, 'Ideal for CCTV & Networking', 'Perfect for CCTV installations, LAN networks, offices, homes, shops and structured cabling projects.', 4, '2026-09-05 08:22:47'),
(411, 35, '305M Full Roll', 'Comes in a 305-meter roll, giving you plenty of cable for multiple network points and larger installations.', 5, '2026-09-05 08:22:47'),
(412, 35, 'UTP Cable Design', 'Uses twisted pairs to help maintain dependable network communication.', 6, '2026-09-05 08:22:47'),
(413, 35, 'Indoor Installation', 'Suitable for network installations inside homes, offices, schools, shops and other buildings.', 7, '2026-09-05 08:22:47'),
(414, 35, 'Easy to Install', 'Easy to route and terminate, helping technicians complete installations efficiently.', 8, '2026-09-05 08:22:47'),
(415, 36, 'Wi‑Fi 4 (802.11n)', 'Delivers reliable wireless speeds up to 300 Mbps on the 2.4 GHz band with 2×2 MIMO architecture.', 1, '2026-10-09 14:24:36'),
(416, 36, 'Four Operational Modes', 'Can function as a Router, Access Point (AP), Repeater, or WISP, offering flexibility for home or SOHO setups.', 2, '2026-10-09 14:24:36'),
(417, 36, 'Lifetime Cloud Management', 'Manage remotely via Ruijie’s free cloud platform or mobile app.', 3, '2026-10-09 14:24:36'),
(418, 36, 'Advanced Family Controls', 'Parental Control, guest network, health mode, and blacklist/whitelist scheduling.', 4, '2026-10-09 14:24:36'),
(419, 36, 'Compact & Efficient Hardware', 'MT7628D CPU, 16 MB flash, 64 MB RAM; power consumption under 5 W.', 5, '2026-10-09 14:24:36'),
(420, 36, 'Wired Connectivity', '1×10/100 Mbps WAN port and 3×10/100 Mbps LAN ports.', 6, '2026-10-09 14:24:36'),
(421, 36, 'Antennae & Coverage', 'Two external 5 dBi antennas with 2×2 MIMO for strong signal.', 7, '2026-10-09 14:24:36'),
(422, 36, 'Designed for Your Space', 'Supports up to 8 users (max 16); compact size: 180×120×27 mm; weight: 0.18 kg.', 8, '2026-10-09 14:24:36'),
(423, 37, '4 Gigabit PoE+ Ports', 'Connect and power compatible IP cameras, wireless access points and other PoE devices using a single Ethernet cable for each device.', 1, '2026-10-09 14:44:59'),
(424, 37, 'Fast Gigabit Network', 'Enjoy fast data transfer for CCTV systems, office networks and business connectivity.', 2, '2026-10-09 14:44:59'),
(425, 37, '54W PoE Power Budget', 'Provides a combined power budget of up to 54W for compatible connected PoE devices.', 3, '2026-10-09 14:44:59'),
(426, 37, 'Easy Cloud Management', 'Manage and monitor your network remotely using Ruijie Cloud, making maintenance more convenient.', 4, '2026-10-09 14:44:59'),
(427, 37, 'Ideal for CCTV Installations', 'A practical solution for connecting IP cameras and network equipment in homes, shops, offices and small businesses.', 5, '2026-10-09 14:44:59'),
(428, 37, '6 Gigabit Ethernet Ports', 'Offers six Gigabit Ethernet ports for connecting network devices, including four PoE+ ports.', 6, '2026-10-09 14:44:59'),
(429, 37, 'PoE Watchdog', 'Helps detect unresponsive connected PoE devices and restart their power to restore operation.', 7, '2026-10-09 14:44:59'),
(430, 37, 'Automatic Loop Prevention', 'Helps protect network stability by preventing common network loop problems.', 8, '2026-10-09 14:44:59'),
(431, 38, 'AC1200 Dual-Band Wi-Fi', 'Enjoy combined wireless speeds of up to 1200 Mbps for smoother browsing, streaming, online learning and everyday internet use.', 1, '2026-10-09 15:28:17'),
(432, 38, '4 External Antennas', 'Helps provide strong, reliable Wi-Fi coverage throughout your home, office or shop.', 2, '2026-10-09 15:28:17'),
(433, 38, '2.4GHz & 5GHz Wi-Fi', 'Choose wider coverage on 2.4GHz or faster wireless performance on 5GHz, depending on your needs.', 3, '2026-10-09 15:28:17'),
(434, 38, 'Connect Multiple Devices', 'Supports multiple connected devices, making it suitable for families, small offices and shops.', 4, '2026-10-09 15:28:17'),
(435, 38, 'Easy App Management', 'Set up and manage your Wi-Fi conveniently using the Reyee Router App on your phone.', 5, '2026-10-09 15:28:17'),
(436, 38, '3 LAN Ports & 1 WAN Port', 'Connect wired devices such as computers, switches and other network equipment. The Ethernet ports support speeds up to 100 Mbps.', 6, '2026-10-09 15:28:17'),
(437, 38, 'Parental Controls', 'Manage internet access, set usage time limits and control access for selected devices.', 7, '2026-10-09 15:28:17'),
(438, 38, 'MU-MIMO Technology', 'Helps the router serve multiple compatible devices more efficiently for a smoother wireless experience.', 8, '2026-10-09 15:28:17'),
(439, 39, 'Wi-Fi 6 AX3000 Performance', 'Delivers ultra-fast speeds up to 2976 Mbps (574 Mbps @2.4 GHz + 2402 Mbps @5 GHz) for high-density outdoor environments.', 1, '2026-10-09 15:33:48'),
(440, 39, 'Rugged IP68 Outdoor Design', 'Fully weatherproof, dustproof, and waterproof housing with lightning protection for harsh outdoor conditions.', 2, '2026-10-09 15:33:48'),
(441, 39, 'High-Gain Omni Antennas', 'Built-in high-gain antennas provide wide and stable outdoor Wi-Fi coverage.', 3, '2026-10-09 15:33:48'),
(442, 39, 'Reyee Mesh Technology', 'Supports plug-and-play mesh networking for seamless expansion without complex configuration.', 4, '2026-10-09 15:33:48'),
(443, 39, 'Cloud & App Management', 'Free lifetime cloud management via Ruijie Cloud or Reyee mobile app—no controller required.', 5, '2026-10-09 15:33:48'),
(444, 39, 'High Client Capacity', 'Supports up to 256 concurrent clients, ideal for campuses, hotels, churches, and outdoor events.', 6, '2026-10-09 15:33:48'),
(445, 39, 'OFDMA & MU-MIMO', 'Advanced Wi-Fi 6 technologies ensure low latency and high efficiency in multi-user environments.', 7, '2026-10-09 15:33:48'),
(446, 39, 'Advanced Security', 'Includes WPA3, client isolation, rogue AP detection, and access control policies.', 8, '2026-10-09 15:33:48'),
(447, 40, 'Cat 6 Network Performance', 'Designed for fast and reliable wired connections in homes, offices and business networks.', 1, '2026-10-10 10:50:04'),
(448, 40, 'Gigabit Network Support', 'Suitable for Gigabit Ethernet connections, helping deliver smooth data transfer and internet connectivity.', 2, '2026-10-10 10:50:04'),
(449, 40, 'Ideal for CCTV & LAN', 'Great for connecting computers, routers, switches, IP cameras and other Ethernet devices.', 3, '2026-10-10 10:50:04'),
(450, 40, 'Easy Indoor Installation', 'Suitable for structured cabling inside homes, offices, shops, schools and other buildings.', 4, '2026-10-10 10:50:04'),
(451, 40, '305M Full Roll', 'Provides plenty of cable for multiple network points and larger installation projects.', 5, '2026-10-10 10:50:04'),
(452, 40, 'UTP Cable Design', 'Uses twisted pairs to support dependable network communication and help reduce signal interference.', 6, '2026-10-10 10:50:04'),
(453, 40, 'Cost-Effective Networking', 'A practical choice for installations where you need an affordable Cat 6 cabling solution.', 7, '2026-10-10 10:50:04'),
(454, 40, 'Grey PVC Outer Jacket', 'The outer jacket protects the cable during normal indoor handling and installation.', 8, '2026-10-10 10:50:04'),
(455, 40, 'Ideal for Network Installers', 'Suitable for LAN installations, office networking, CCTV projects and general structured cabling.', 9, '2026-10-10 10:50:04'),
(456, 41, 'Fast 5-Second Splicing', 'Splices optical fibres in approximately 5 seconds, helping technicians complete installations and repairs faster.', 1, '2026-10-10 11:08:56'),
(457, 41, '6-Motor Core Alignment', 'Automatically aligns fibre cores for accurate splicing and low signal loss.', 2, '2026-10-10 11:08:56'),
(458, 41, 'Built-in Power Meter & VFL', 'Test optical power and trace fibre faults using built-in tools, reducing the need to carry separate testing equipment.', 3, '2026-10-10 11:08:56'),
(459, 41, 'Long-Lasting 7800mAh Battery', 'Supports approximately 240–260 splicing and heating cycles per charge, depending on working conditions.', 4, '2026-10-10 11:08:56'),
(460, 41, '5-Inch Colour Display', 'Clearly displays fibre alignment and splicing results, making operation easier for technicians.', 5, '2026-10-10 11:08:56'),
(461, 41, '5-Inch Colour Display', 'Clearly displays fibre alignment and splicing results, making operation easier for technicians.', 6, '2026-10-10 11:08:56'),
(462, 41, '15-Second Heating', 'Quickly heats splice protection sleeves to help protect completed fibre joints.', 7, '2026-10-10 11:08:56'),
(463, 41, 'Supports Multiple Fibre Types', 'Suitable for common single-mode and multimode fibre applications, including FTTH and fibre network installations.', 8, '2026-10-10 11:08:56'),
(464, 41, 'Portable Field Design', 'Compact enough to carry between installation sites, customer premises and outdoor fibre repair jobs.', 9, '2026-10-10 11:08:56'),
(465, 41, 'Designed for ISP & FTTH Projects', 'A professional solution for fibre technicians, internet service providers, telecom contractors and network installers.', 10, '2026-10-10 11:08:56'),
(485, 42, 'High-Speed Satellite Internet', 'Enjoy fast internet for streaming, video calls, online gaming, browsing and business activities, even in areas with limited traditional internet coverage.', 1, '2026-10-30 11:42:21'),
(486, 42, 'Wi-Fi 6 Router', 'Get improved wireless performance and efficient connectivity for multiple devices around your home or business.', 2, '2026-10-30 11:42:21'),
(487, 42, 'Wide Wi-Fi Coverage', 'The Gen 3 router provides coverage of up to 297 m² under suitable conditions, helping connect devices across your space.', 3, '2026-10-30 11:42:21'),
(488, 42, 'Works in Remote Areas', 'Connect homes, farms, offices and sites where fibre or reliable terrestrial internet may not be available, provided there is Starlink service coverage and a clear view of the sky.', 4, '2026-10-30 11:42:21'),
(489, 42, 'Easy Self-Installation', 'Use the Starlink app to guide setup and find a suitable position for the dish, making installation straightforward.', 5, '2026-10-30 11:42:21'),
(490, 42, 'Two Ethernet LAN Ports', 'Connect compatible wired devices such as computers, TVs, network switches and other equipment directly to the router.', 6, '2026-10-30 11:42:21'),
(491, 42, 'Weather-Resistant Dish', 'The outdoor dish is designed to withstand challenging weather conditions, including rain and strong winds within its operating specifications.', 7, '2026-10-30 11:42:21'),
(492, 42, 'Connect Many Devices', 'The Gen 3 router supports up to 235 connected devices, depending on network conditions and usage.', 8, '2026-10-30 11:42:21'),
(493, 42, 'Mesh Wi-Fi Expansion', 'Expand coverage using compatible Starlink Gen 2 or Gen 3 mesh nodes to help reach areas with weaker Wi-Fi signals.', 9, '2026-10-30 11:42:21'),
(494, 42, 'Complete Standard Kit', 'Includes the Starlink dish, kickstand, Gen 3 router, 15-metre Starlink cable, power supply and power cable.', 10, '2026-10-30 11:42:21'),
(495, 42, 'Ideal for Homes & Businesses', 'A practical internet solution for homes, offices, farms, shops, remote work locations and businesses needing reliable connectivity.', 11, '2026-10-30 11:42:21'),
(496, 44, 'Portable Satellite Internet', 'Take your internet connection wherever you go. Ideal for travel, camping, remote work, farms and locations without reliable fibre coverage.', 1, '2026-10-30 12:01:35'),
(497, 44, 'Built-in Wi-Fi Router', 'Connect your phones, laptops, tablets and other devices directly without needing a separate Wi-Fi router.', 2, '2026-10-30 12:01:35'),
(498, 44, 'Compact & Lightweight Design', 'Weighs approximately 1.1kg, making it easy to carry, pack and set up at different locations.', 3, '2026-10-30 12:01:35'),
(499, 44, 'Weather-Resistant IP67 Design', 'Built to handle dust, rain and challenging outdoor conditions, making it suitable for outdoor use.', 4, '2026-10-30 12:01:35'),
(500, 44, 'Low Power Consumption', 'Uses an average of approximately 25–40W, making it suitable for portable power setups and off-grid applications.', 5, '2026-10-30 12:01:35'),
(501, 44, 'Wi-Fi 5 Dual-Band Technology', 'Supports both 2.4GHz and 5GHz Wi-Fi bands for convenient wireless connectivity.', 6, '2026-10-30 12:01:35'),
(502, 44, 'Connect Up to 128 Devices', 'Supports up to 128 connected devices, making it useful for families, small teams and mobile work setups.', 7, '2026-10-30 12:01:35'),
(503, 44, 'Built-in Ethernet Port', 'Connect compatible wired devices and additional networking equipment through its Ethernet LAN port.', 8, '2026-10-30 12:01:35'),
(504, 44, 'Wide Wi-Fi Coverage', 'Provides Wi-Fi coverage of up to approximately 112 m² under suitable conditions.', 9, '2026-10-30 12:01:35'),
(505, 44, 'Flexible Power Options', 'Supports 12–48V DC power input, making it suitable for compatible vehicle, battery and portable power systems.', 10, '2026-10-30 12:01:35'),
(506, 44, 'Easy Setup with Starlink App', 'Use the Starlink mobile app to set up your equipment, find a suitable position and manage your connection.', 11, '2026-10-30 12:01:35'),
(507, 44, 'Ideal for Travel & Remote Areas', 'A convenient internet solution for travellers, campers, field teams, rural homes, farms and remote businesses.', 12, '2026-10-30 12:01:35'),
(508, 43, 'AC1200 Dual-Band Speeds', 'Delivers concurrent throughput up to 1167 Mbps (300 Mbps @2.4 GHz + 867 Mbps @5 GHz).', 1, '2026-10-30 14:41:22'),
(509, 43, 'High-Gain External Antennas', 'Equipped with 2 omni-directional antennas covering up to 200 m in open areas.', 2, '2026-10-30 14:41:22'),
(510, 43, 'Wide Outdoor Wi-Fi Coverage', 'Designed for wireless coverage across open spaces, compounds, gardens, warehouses and business premises. Coverage can reach up to a 200-metre radius under suitable conditions.', 3, '2026-10-30 14:41:22'),
(511, 43, 'IP65 Weather Protection', 'Built to resist dust and water, making it suitable for outdoor installations in changing weather conditions.', 4, '2026-10-30 14:41:22'),
(512, 43, 'Gigabit Ethernet Port', 'Provides a Gigabit wired connection to your network for fast data transfer between the access point and network equipment.', 5, '2026-10-30 14:41:22'),
(513, 43, 'Flexible PoE Power Options', 'Supports standard 802.3af PoE and 24V passive PoE, helping simplify installation where a power socket is not nearby.', 6, '2026-10-30 14:41:22'),
(514, 43, 'Supports Multiple Users', 'Supports up to 256 client devices, with approximately 50 recommended for normal deployment.', 7, '2026-10-30 14:41:22'),
(515, 43, 'MU-MIMO Technology', 'Helps serve multiple compatible wireless devices more efficiently for a better shared Wi-Fi experience.', 8, '2026-10-30 14:41:22'),
(550, 47, '300Mbps Wireless Speed', 'Enjoy reliable Wi-Fi for browsing, social media, online learning, video streaming and everyday internet activities.', 1, '2026-10-30 15:12:00'),
(551, 47, '4 High-Gain 5dBi Antennas', 'Four external antennas help deliver stronger Wi-Fi signals and wider coverage throughout your home or office.', 2, '2026-10-30 15:12:00'),
(552, 47, 'Wide Wi-Fi Coverage', 'Designed to provide dependable wireless coverage in apartments, bedrooms, homes and small offices.', 3, '2026-10-30 15:12:00'),
(553, 47, 'Wi-Fi Extender Mode', 'Extend an existing Wi-Fi network to help eliminate weak signal areas and improve coverage in your space.', 4, '2026-10-30 15:12:00'),
(554, 47, 'Easy Setup & Management', 'Simple installation and user-friendly settings make it easy to get your home or office connected.', 5, '2026-10-30 15:12:00'),
(555, 47, '1 WAN & 3 LAN Ports', 'Connect your internet source and up to three wired devices such as computers, TVs and other network equipment. Ports support up to 100Mbps.', 6, '2026-10-30 15:12:00'),
(556, 47, '2.4GHz Wireless Connection', 'Provides reliable wireless connectivity with good coverage for everyday home and office use.', 7, '2026-10-30 15:12:00'),
(557, 47, 'Multiple Operating Modes', 'Supports Router, Access Point, WISP and Universal Repeater modes for flexible networking setups.', 8, '2026-10-30 15:12:00'),
(558, 48, '5MP Ultra-Clear 3K Video', 'Capture sharp, detailed footage with 3K resolution (2880 × 1620), making it easier to see what is happening around your home or business.', 1, '2026-10-31 09:35:36'),
(559, 48, '360° Panoramic Monitoring', 'Rotate the camera left, right, up and down using your phone to monitor almost every corner of a room and reduce blind spots.', 2, '2026-10-31 09:35:36'),
(560, 48, 'AI Human & Cry Detection', 'Smart detection identifies human activity and baby crying, helping alert you to important events around your home.', 3, '2026-10-31 09:35:36'),
(561, 48, 'Two-Way Audio & One-Touch Call', 'Listen and speak through the camera using your phone. The one-touch call button makes it easy for children or elderly family members to reach you.', 4, '2026-10-31 09:35:36'),
(562, 48, 'Wi-Fi 6 Connectivity', 'Enjoy smoother video viewing and responsive camera control with modern Wi-Fi 6 wireless technology.', 5, '2026-10-31 09:35:36'),
(563, 48, 'Clear Night Vision', 'Built-in infrared lights help you monitor your home or business even in darkness, with night vision up to approximately 10 metres.', 6, '2026-10-31 09:35:36'),
(564, 48, 'Sound & Light Alarm', 'Provides sound and light alerts to help warn of detected activity and discourage unwanted visitors.', 7, '2026-10-31 09:35:36'),
(565, 48, 'Automatic 360° Patrol', 'Automatically scans selected viewing areas to help monitor different parts of your room.', 8, '2026-10-31 09:35:36'),
(566, 48, 'MicroSD Card Storage', 'Supports MicroSD cards up to 512GB, allowing you to save and review recorded footage locally.', 9, '2026-10-31 09:35:36'),
(567, 48, 'Remote Viewing via Mobile App', 'View live footage, control camera movement and receive alerts remotely through the compatible Tenda TDSEE app.', 10, '2026-10-31 09:35:36'),
(568, 49, '3MP Ultra-Clear 2K Video', 'Capture clear and detailed footage in 2K resolution (2304 × 1296), helping you see what is happening around your home or business.', 1, '2026-10-31 09:49:27'),
(569, 49, '360° Panoramic Monitoring', 'Rotate the camera up to 355° horizontally and 70° vertically using your phone to monitor different areas and reduce blind spots.', 2, '2026-10-31 09:49:27'),
(570, 49, 'Smart Human Detection & Tracking', 'Detects human movement and automatically follows detected people, helping you keep important activity within view.', 3, '2026-10-31 09:49:27'),
(571, 49, 'Two-Way Audio', 'Detects human movement and automatically follows detected people, helping you keep important activity within view.', 4, '2026-10-31 09:49:27'),
(572, 49, 'Wi-Fi 6 Connectivity', 'Enjoy modern wireless connectivity for smoother live viewing and convenient remote camera control.', 5, '2026-10-31 09:49:27'),
(573, 49, 'Infrared Night Vision', 'Monitor your home or business even in darkness with infrared night vision reaching up to 10 metres under suitable conditions.', 6, '2026-10-31 09:49:27'),
(574, 49, 'Motion & Cry Detection', 'Detects movement and crying sounds to help alert you to important activity, especially useful for monitoring children and babies.', 7, '2026-10-31 09:49:27'),
(575, 49, 'Remote Viewing via TDSEE App', 'View live footage, control camera movement and receive alerts directly on your smartphone from wherever you are.', 8, '2026-10-31 09:49:27'),
(576, 49, 'MicroSD Card Storage', 'Supports MicroSD card storage for saving and reviewing recorded footage. Capacity depends on the camera version, up to 512GB on V3.1.', 9, '2026-10-31 09:49:27'),
(577, 50, '8 Gigabit Ethernet Ports', 'Connect up to 8 network devices such as computers, routers, CCTV recorders, access points and smart TVs.', 1, '2026-10-31 10:10:24'),
(578, 50, 'High-Speed 1000Mbps Connectivity', 'Enjoy network speeds of up to 1Gbps per port for faster file transfers, smooth video streaming and reliable networking.', 2, '2026-10-31 10:10:24'),
(579, 50, '16Gbps Switching Capacity', 'Handles network traffic efficiently, helping multiple connected devices communicate smoothly.', 3, '2026-10-31 10:10:24'),
(580, 50, 'Plug & Play Installation', 'No complicated configuration required. Simply connect your Ethernet cables and start expanding your network.', 4, '2026-10-31 10:10:24'),
(581, 50, 'Ideal for CCTV & Office Networks', 'Perfect for connecting computers, IP cameras, NVRs, routers and other network equipment in homes and businesses.', 5, '2026-10-31 10:10:24'),
(582, 50, 'Full-Duplex Transmission', 'Supports simultaneous sending and receiving of data for efficient network communication.', 6, '2026-10-31 10:10:24'),
(583, 50, 'Auto MDI/MDIX', 'Automatically detects cable connections, eliminating the need for special crossover cables.', 7, '2026-10-31 10:10:24'),
(584, 50, 'Compact Space-Saving Design', 'Its small design fits neatly on desks, shelves and network installation spaces.', 8, '2026-10-31 10:10:24');
INSERT INTO `product_features` (`id`, `product_id`, `feature`, `benefit`, `sort_order`, `created_at`) VALUES
(585, 46, 'AC1200 Dual-Band Wi-Fi', 'Enjoy combined wireless speeds of up to 1167 Mbps for smooth streaming, online gaming, browsing and everyday internet use.', 1, '2026-11-14 09:50:34'),
(586, 46, '4 High-Gain Antennas', 'Four external 6dBi antennas help deliver strong Wi-Fi signals and wider coverage throughout your home or office.', 2, '2026-11-14 09:50:34'),
(587, 46, 'Gigabit Ethernet Ports', 'Features 1 Gigabit WAN port and 3 Gigabit LAN ports for fast wired connections to computers, smart TVs and other network devices.', 3, '2026-11-14 09:50:34'),
(588, 46, 'Powerful Dual-Core Processor', 'The 1GHz dual-core processor helps handle network traffic efficiently for a smoother internet experience.', 4, '2026-11-14 09:50:34'),
(589, 46, 'Perfect for Fibre Internet', 'Ideal for fibre connections, homes, offices and small businesses that need fast and reliable wired and wireless networking.', 5, '2026-11-14 09:50:34'),
(590, 46, '2.4GHz & 5GHz Wi-Fi', 'Use 2.4GHz for wider coverage and 5GHz for faster speeds and smoother performance on compatible devices.', 6, '2026-11-14 09:50:34'),
(591, 46, 'Beamforming Technology', 'Helps direct Wi-Fi signals towards connected devices for improved wireless connectivity.', 7, '2026-11-14 09:50:34'),
(592, 46, 'MU-MIMO Technology', 'Helps the router communicate with multiple compatible devices more efficiently.', 8, '2026-11-14 09:50:34'),
(593, 46, 'Multiple Operating Modes', 'Supports router, access point, universal repeater and WISP modes for different networking needs.', 9, '2026-11-14 09:50:34'),
(594, 45, '4G LTE SIM Card Support', 'Insert your SIM card and enjoy fast internet anywhere with 4G coverage—no fiber or DSL required.', 1, '2026-11-14 09:52:10'),
(595, 45, '300Mbps Wireless Speed', 'Provides smooth browsing, online learning, HD streaming, and video calls for your family or office.', 2, '2026-11-14 09:52:10'),
(596, 45, 'Works with Most SIM Networks', 'Compatible with many 4G LTE mobile providers, giving you the freedom to choose your preferred network.', 3, '2026-11-14 09:52:10'),
(597, 45, 'Plug & Play Setup', 'Simply insert a SIM card, power it on, and connect your devices in minutes.', 4, '2026-11-14 09:52:10'),
(598, 45, 'Wired & Wireless Connections', 'Connect computers, Smart TVs, CCTV systems, or gaming consoles using Ethernet while enjoying Wi-Fi for mobile devices.', 5, '2026-11-14 09:52:10'),
(599, 45, 'Guest Wi-Fi Network', 'Create a separate Wi-Fi network for visitors while keeping your personal devices secure.', 6, '2026-11-14 09:52:10'),
(600, 45, 'Strong Security', 'Built-in wireless security helps protect your network from unauthorized access.', 7, '2026-11-14 09:52:10'),
(601, 45, 'Supports Multiple Devices', 'Connect smartphones, laptops, tablets, Smart TVs, and other Wi-Fi devices simultaneously.', 8, '2026-11-14 09:52:10');

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
(77, 32, '1788361767_a580ef0a8c89.png', 'Mikrotik RB951Ui-2HnD Router - Image 5', 5, 0, '2026-09-02 15:09:27'),
(78, 33, '1788363234_89658944764b.jpg', 'Mikrotik SXTsq Series', 1, 1, '2026-09-02 15:33:54'),
(79, 33, '1788363234_1ccf414163cf.jpg', 'Mikrotik SXTsq Series - Image 2', 2, 0, '2026-09-02 15:33:54'),
(80, 33, '1788363234_e6ea4aea4f90.jpg', 'Mikrotik SXTsq Series - Image 3', 3, 0, '2026-09-02 15:33:54'),
(81, 33, '1788363234_2be7be201069.png', 'Mikrotik SXTsq Series - Image 4', 4, 0, '2026-09-02 15:33:54'),
(82, 33, '1788363234_83d1f635d12a.webp', 'Mikrotik SXTsq Series - Image 5', 5, 0, '2026-09-02 15:33:54'),
(83, 34, '1788363934_39c5dbd3b571.jpeg', 'PowerBeam M5', 1, 1, '2026-09-02 15:45:34'),
(84, 34, '1788363934_6725b1e5873a.jpeg', 'PowerBeam M5 - Image 2', 2, 0, '2026-09-02 15:45:34'),
(85, 34, '1788363934_fccee6dc3fee.webp', 'PowerBeam M5 - Image 3', 3, 0, '2026-09-02 15:45:34'),
(86, 34, '1788363934_8daa1212fce9.jpg', 'PowerBeam M5 - Image 4', 4, 0, '2026-09-02 15:45:34'),
(87, 34, '1788363934_0d8048645185.jpg', 'PowerBeam M5 - Image 5', 5, 0, '2026-09-02 15:45:34'),
(88, 34, '1788363934_af06695bc0e2.webp', 'PowerBeam M5 - Image 6', 6, 0, '2026-09-02 15:45:34'),
(89, 35, '1788596567_7514c93693a5.webp', 'RDCOM Cat 6 Indoor Cable', 1, 1, '2026-09-05 08:22:47'),
(90, 35, '1788596567_28df3f8abb8c.webp', 'RDCOM Cat 6 Indoor Cable - Image 2', 2, 0, '2026-09-05 08:22:47'),
(91, 36, '1791555876_48a7efb0af6f.jpg', 'Ruijie | Reyee RG-EW300N Router', 1, 1, '2026-10-09 14:24:36'),
(92, 36, '1791555876_d7efedc1a83b.jpg', 'Ruijie | Reyee RG-EW300N Router - Image 2', 2, 0, '2026-10-09 14:24:36'),
(93, 36, '1791555876_3f192193e0f9.jpg', 'Ruijie | Reyee RG-EW300N Router - Image 3', 3, 0, '2026-10-09 14:24:36'),
(94, 36, '1791555876_b412db6208f5.jpg', 'Ruijie | Reyee RG-EW300N Router - Image 4', 4, 0, '2026-10-09 14:24:36'),
(95, 36, '1791555876_768a286be4c5.png', 'Ruijie | Reyee RG-EW300N Router - Image 5', 5, 0, '2026-10-09 14:24:36'),
(96, 37, '1791557099_2b69bef31022.webp', 'Ruijie Reyee RG-ES206GS-P PoE+ Switch', 1, 1, '2026-10-09 14:44:59'),
(97, 37, '1791557099_d05b6ff3d53b.png', 'Ruijie Reyee RG-ES206GS-P PoE+ Switch - Image 2', 2, 0, '2026-10-09 14:44:59'),
(98, 37, '1791557099_c96f57b426e6.png', 'Ruijie Reyee RG-ES206GS-P PoE+ Switch - Image 3', 3, 0, '2026-10-09 14:44:59'),
(99, 37, '1791557099_324fdb4ac2d9.webp', 'Ruijie Reyee RG-ES206GS-P PoE+ Switch - Image 4', 4, 0, '2026-10-09 14:44:59'),
(100, 37, '1791557099_cfca55183d4f.webp', 'Ruijie Reyee RG-ES206GS-P PoE+ Switch - Image 5', 5, 0, '2026-10-09 14:44:59'),
(101, 38, '1791559697_c674f4430369.png', 'Ruijie Reyee RG-EW1200 AC1200 Dual-Band Wireless Router', 1, 1, '2026-10-09 15:28:17'),
(102, 38, '1791559697_51e39e6adac5.png', 'Ruijie Reyee RG-EW1200 AC1200 Dual-Band Wireless Router - Image 2', 2, 0, '2026-10-09 15:28:17'),
(103, 38, '1791559697_f2773eac964e.png', 'Ruijie Reyee RG-EW1200 AC1200 Dual-Band Wireless Router - Image 3', 3, 0, '2026-10-09 15:28:17'),
(104, 38, '1791559697_f006caf9bd4f.png', 'Ruijie Reyee RG-EW1200 AC1200 Dual-Band Wireless Router - Image 4', 4, 0, '2026-10-09 15:28:17'),
(105, 38, '1791559697_560d08d8bf1b.png', 'Ruijie Reyee RG-EW1200 AC1200 Dual-Band Wireless Router - Image 5', 5, 0, '2026-10-09 15:28:17'),
(106, 38, '1791559697_07b09efbb39c.png', 'Ruijie Reyee RG-EW1200 AC1200 Dual-Band Wireless Router - Image 6', 6, 0, '2026-10-09 15:28:17'),
(107, 39, '1791560028_aa7a98d015f2.jpg', 'Ruijie Reyee RG-RAP62-OD AX3000 WIFI 6 Outdoor Access Point', 1, 1, '2026-10-09 15:33:48'),
(108, 39, '1791560028_16fd36af1eca.jpg', 'Ruijie Reyee RG-RAP62-OD AX3000 WIFI 6 Outdoor Access Point - Image 2', 2, 0, '2026-10-09 15:33:48'),
(109, 39, '1791560028_9d89dfb3b19a.png', 'Ruijie Reyee RG-RAP62-OD AX3000 WIFI 6 Outdoor Access Point - Image 3', 3, 0, '2026-10-09 15:33:48'),
(110, 39, '1791560028_b10398820226.jpg', 'Ruijie Reyee RG-RAP62-OD AX3000 WIFI 6 Outdoor Access Point - Image 4', 4, 0, '2026-10-09 15:33:48'),
(111, 39, '1791560028_b507d11a3f2b.webp', 'Ruijie Reyee RG-RAP62-OD AX3000 WIFI 6 Outdoor Access Point - Image 5', 5, 0, '2026-10-09 15:33:48'),
(112, 39, '1791560028_f6fc9e6e51ad.jpg', 'Ruijie Reyee RG-RAP62-OD AX3000 WIFI 6 Outdoor Access Point - Image 6', 6, 0, '2026-10-09 15:33:48'),
(113, 40, '1791629404_ea8249a60023.webp', 'Scoop Cat 6 Indoor Cable', 1, 1, '2026-10-10 10:50:04'),
(114, 40, '1791629404_876d2f549de6.webp', 'Scoop Cat 6 Indoor Cable - Image 2', 2, 0, '2026-10-10 10:50:04'),
(115, 41, '1791630536_f2f4d4e3980b.jpg', 'Signal Fire AI-9 Fusion Splicing Machine', 1, 1, '2026-10-10 11:08:56'),
(116, 41, '1791630536_f32003238199.jpg', 'Signal Fire AI-9 Fusion Splicing Machine - Image 2', 2, 0, '2026-10-10 11:08:56'),
(117, 41, '1791630536_968cc7a1f0e8.jpg', 'Signal Fire AI-9 Fusion Splicing Machine - Image 3', 3, 0, '2026-10-10 11:08:56'),
(118, 41, '1791630536_2636ed752068.jpg', 'Signal Fire AI-9 Fusion Splicing Machine - Image 4', 4, 0, '2026-10-10 11:08:56'),
(119, 41, '1791630536_eb8a94792050.webp', 'Signal Fire AI-9 Fusion Splicing Machine - Image 5', 5, 0, '2026-10-10 11:08:56'),
(120, 41, '1791630536_f059a42661be.jpg', 'Signal Fire AI-9 Fusion Splicing Machine - Image 6', 6, 0, '2026-10-10 11:08:56'),
(121, 42, '1791635511_29d52f5502c9.png', 'Starlink Gen 3 Standard Kit (V3)', 1, 1, '2026-10-10 12:31:51'),
(122, 42, '1791635511_a15391cdd1c5.jpg', 'Starlink Gen 3 Standard Kit (V3) - Image 2', 2, 0, '2026-10-10 12:31:51'),
(123, 42, '1791635511_a76292305dd5.jpg', 'Starlink Gen 3 Standard Kit (V3) - Image 3', 3, 0, '2026-10-10 12:31:51'),
(124, 42, '1791635511_b38d7f6d1468.webp', 'Starlink Gen 3 Standard Kit (V3) - Image 4', 4, 0, '2026-10-10 12:31:51'),
(125, 42, '1791635511_4e35dbdfcf92.png', 'Starlink Gen 3 Standard Kit (V3) - Image 5', 5, 0, '2026-10-10 12:31:51'),
(126, 42, '1791635511_8ca2ff482f33.webp', 'Starlink Gen 3 Standard Kit (V3) - Image 6', 6, 0, '2026-10-10 12:31:51'),
(127, 43, '1791639670_6bd3aef87bc8.jpeg', 'Tenda OAP1200 Outdoor Wi-Fi Access Point', 1, 1, '2026-10-10 13:41:10'),
(128, 43, '1791639670_64d5265f1b07.png', 'Tenda OAP1200 Outdoor Wi-Fi Access Point - Image 2', 2, 0, '2026-10-10 13:41:10'),
(129, 43, '1791639670_66cb3bb601f2.jpg', 'Tenda OAP1200 Outdoor Wi-Fi Access Point - Image 3', 3, 0, '2026-10-10 13:41:10'),
(130, 44, '1793361695_394cee4ae809.jpg', 'Starlink Mini Satellite Internet Kit', 1, 1, '2026-10-30 12:01:35'),
(131, 44, '1793361695_7e7ce440f55d.webp', 'Starlink Mini Satellite Internet Kit - Image 2', 2, 0, '2026-10-30 12:01:35'),
(132, 44, '1793361695_22ebd676441c.jpg', 'Starlink Mini Satellite Internet Kit - Image 3', 3, 0, '2026-10-30 12:01:35'),
(133, 44, '1793361695_85b3cec5d646.jpg', 'Starlink Mini Satellite Internet Kit - Image 4', 4, 0, '2026-10-30 12:01:35'),
(134, 44, '1793361695_30844ad2cd4e.png', 'Starlink Mini Satellite Internet Kit - Image 5', 5, 0, '2026-10-30 12:01:35'),
(135, 45, '1793371601_b62037429264.jpg', 'Tenda 4G03 N300 Wi-Fi 4G LTE Router. - Image 2', 2, 0, '2026-10-30 14:46:41'),
(136, 46, '1793372007_5fa4a5c58899.jpg', 'Tenda AC10 AC1200 Dual-Band Gigabit Wi-Fi Router - Image 2', 2, 0, '2026-10-30 14:53:27'),
(137, 46, '1793372144_22cb292eb2c3.jpg', 'Tenda AC10 AC1200 Dual-Band Gigabit Wi-Fi Router - Image 3', 3, 0, '2026-10-30 14:55:44'),
(138, 46, '1793372144_6bbaa47df861.jpg', 'Tenda AC10 AC1200 Dual-Band Gigabit Wi-Fi Router - Image 4', 4, 0, '2026-10-30 14:55:44'),
(139, 46, '1793372144_c47cd4989187.jpg', 'Tenda AC10 AC1200 Dual-Band Gigabit Wi-Fi Router - Image 5', 5, 0, '2026-10-30 14:55:44'),
(140, 46, '1793372144_886ba2bcbab2.png', 'Tenda AC10 AC1200 Dual-Band Gigabit Wi-Fi Router - Image 6', 6, 0, '2026-10-30 14:55:44'),
(141, 46, '1793372144_6a00ff0154b8.jpg', 'Tenda AC10 AC1200 Dual-Band Gigabit Wi-Fi Router - Image 7', 7, 0, '2026-10-30 14:55:44'),
(142, 45, '1793372203_ece262effab1.jpg', 'Tenda 4G03 N300 Wi-Fi 4G LTE Router. - Image 3', 3, 0, '2026-10-30 14:56:43'),
(143, 45, '1793372203_eb8fc1cb89f3.jpg', 'Tenda 4G03 N300 Wi-Fi 4G LTE Router. - Image 4', 4, 0, '2026-10-30 14:56:43'),
(144, 45, '1793372203_88a74b59c9fc.jpg', 'Tenda 4G03 N300 Wi-Fi 4G LTE Router. - Image 5', 5, 0, '2026-10-30 14:56:43'),
(145, 45, '1793372203_a61184454c95.png', 'Tenda 4G03 N300 Wi-Fi 4G LTE Router. - Image 6', 6, 0, '2026-10-30 14:56:43'),
(146, 45, '1793372203_8d40d142462b.jpg', 'Tenda 4G03 N300 Wi-Fi 4G LTE Router. - Image 7', 7, 0, '2026-10-30 14:56:43'),
(147, 45, '1793372203_f1f50b4dbc05.png', 'Tenda 4G03 N300 Wi-Fi 4G LTE Router. - Image 8', 8, 0, '2026-10-30 14:56:43'),
(148, 47, '1793373120_40f766398ce5.jpg', 'Tenda F6 N300 Wireless Wi-Fi Router', 1, 1, '2026-10-30 15:12:00'),
(149, 47, '1793373120_11db07267827.jpg', 'Tenda F6 N300 Wireless Wi-Fi Router - Image 2', 2, 0, '2026-10-30 15:12:00'),
(150, 47, '1793373120_d0d5d4268185.jpg', 'Tenda F6 N300 Wireless Wi-Fi Router - Image 3', 3, 0, '2026-10-30 15:12:00'),
(151, 47, '1793373120_0fcfbb3d141e.jpg', 'Tenda F6 N300 Wireless Wi-Fi Router - Image 4', 4, 0, '2026-10-30 15:12:00'),
(152, 47, '1793373120_4ae024843589.jpg', 'Tenda F6 N300 Wireless Wi-Fi Router - Image 5', 5, 0, '2026-10-30 15:12:00'),
(153, 47, '1793373120_02563a7f1af4.webp', 'Tenda F6 N300 Wireless Wi-Fi Router - Image 6', 6, 0, '2026-10-30 15:12:00'),
(154, 48, '1793439336_c862b9dca88b.jpg', 'Tenda CP7 5MP 3K Smart Wi-Fi Security Pan/Tilt Camera', 1, 1, '2026-10-31 09:35:36'),
(155, 48, '1793439336_e36f191c152d.jpg', 'Tenda CP7 5MP 3K Smart Wi-Fi Security Pan/Tilt Camera - Image 2', 2, 0, '2026-10-31 09:35:36'),
(156, 48, '1793439336_171a3d3298b6.jpg', 'Tenda CP7 5MP 3K Smart Wi-Fi Security Pan/Tilt Camera - Image 3', 3, 0, '2026-10-31 09:35:36'),
(157, 48, '1793439336_5572677ee292.jpg', 'Tenda CP7 5MP 3K Smart Wi-Fi Security Pan/Tilt Camera - Image 4', 4, 0, '2026-10-31 09:35:36'),
(158, 48, '1793439336_15a92763b571.jpg', 'Tenda CP7 5MP 3K Smart Wi-Fi Security Pan/Tilt Camera - Image 5', 5, 0, '2026-10-31 09:35:36'),
(159, 48, '1793439336_ae2a386c805c.jpg', 'Tenda CP7 5MP 3K Smart Wi-Fi Security Pan/Tilt Camera - Image 6', 6, 0, '2026-10-31 09:35:36'),
(160, 48, '1793439336_925e134c7e84.png', 'Tenda CP7 5MP 3K Smart Wi-Fi Security Pan/Tilt Camera - Image 7', 7, 0, '2026-10-31 09:35:36'),
(161, 49, '1793440167_e80b52e12d78.jpg', 'Tenda CP3 3MP 2K Smart Wi-Fi Security Pan/Tilt Camera', 1, 1, '2026-10-31 09:49:27'),
(162, 49, '1793440167_bbacd6a87f80.jpg', 'Tenda CP3 3MP 2K Smart Wi-Fi Security Pan/Tilt Camera - Image 2', 2, 0, '2026-10-31 09:49:27'),
(163, 49, '1793440167_15672dea89d5.jpg', 'Tenda CP3 3MP 2K Smart Wi-Fi Security Pan/Tilt Camera - Image 3', 3, 0, '2026-10-31 09:49:27'),
(164, 49, '1793440167_7361b8261e41.jpg', 'Tenda CP3 3MP 2K Smart Wi-Fi Security Pan/Tilt Camera - Image 4', 4, 0, '2026-10-31 09:49:27'),
(165, 49, '1793440167_7d9c6591ca3a.jpg', 'Tenda CP3 3MP 2K Smart Wi-Fi Security Pan/Tilt Camera - Image 5', 5, 0, '2026-10-31 09:49:27'),
(166, 49, '1793440167_f2b979b98a63.jpg', 'Tenda CP3 3MP 2K Smart Wi-Fi Security Pan/Tilt Camera - Image 6', 6, 0, '2026-10-31 09:49:27'),
(167, 49, '1793440167_4cab6e2b72c6.jpg', 'Tenda CP3 3MP 2K Smart Wi-Fi Security Pan/Tilt Camera - Image 7', 7, 0, '2026-10-31 09:49:27'),
(168, 50, '1793441424_953989e29c01.jpeg', 'Tenda SG108 8-Port Gigabit Ethernet Switch', 1, 1, '2026-10-31 10:10:24'),
(169, 50, '1793441424_ddc5a023d8f1.webp', 'Tenda SG108 8-Port Gigabit Ethernet Switch - Image 2', 2, 0, '2026-10-31 10:10:24'),
(170, 50, '1793441424_88b3951f7427.png', 'Tenda SG108 8-Port Gigabit Ethernet Switch - Image 3', 3, 0, '2026-10-31 10:10:24'),
(171, 50, '1793441424_7a3b0511ca18.jpeg', 'Tenda SG108 8-Port Gigabit Ethernet Switch - Image 4', 4, 0, '2026-10-31 10:10:24'),
(172, 50, '1793441424_6fb098ea5c6f.png', 'Tenda SG108 8-Port Gigabit Ethernet Switch - Image 5', 5, 0, '2026-10-31 10:10:24'),
(173, 46, '1794649834_932adfda36b0.jpg', 'Tenda AC10 AC1200 Dual-Band Gigabit Wi-Fi Router', 1, 1, '2026-11-14 09:50:34'),
(174, 45, '1794649930_525a9c184ecd.jpg', 'Tenda 4G03 N300 Wi-Fi 4G LTE Router.', 1, 1, '2026-11-14 09:52:10');

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
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'admin', 'Full access to the CORE LTD administration system', '2026-09-07 06:07:17'),
(2, 'manager', 'Access controlled by assigned permissions', '2026-09-07 06:07:17');

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

CREATE TABLE `role_permissions` (
  `id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL
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
(1, 'ALEX GITAHI', 'alex.gtahi@gmail.com', '0757130130', '$2y$10$H/d9FZ39dOCi28MtT6P8w.uYrZetgIHhMtbFsGLZeAS65U0b09/4.', 'admin', 1, '2026-11-14 11:49:24', '2026-08-09 18:22:42', '2026-11-14 08:49:24');

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
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

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
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_role_permission` (`role_id`,`permission_id`),
  ADD KEY `fk_role_permissions_permission` (`permission_id`);

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `product_features`
--
ALTER TABLE `product_features`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=602;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=175;

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
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `role_permissions`
--
ALTER TABLE `role_permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
-- Constraints for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `fk_role_permissions_permission` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_role_permissions_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
