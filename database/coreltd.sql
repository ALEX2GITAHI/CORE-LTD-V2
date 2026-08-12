-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 12, 2026 at 07:38 AM
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
(3, 13, 'new_order', 'New Order Received', 'Order #CORE-20260811-DC8451 from ALEX - KSh 1,400.00', 1, '2026-08-11 18:22:21', '2026-08-11 15:10:18');

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
(11, 'HikVision', 'hikvision', '', '1786443155_5b74cf3188.png', 'https://www.hikvision.com', 1, '2026-08-11 10:12:35', '2026-08-11 10:23:41');

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
(8, NULL, 'Accessories', 'accessories', 'Networking accessories and installation accessories', NULL, 1, 0, '2026-08-07 09:45:40', '2026-08-07 09:45:40');

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
(13, 'CORE-20260811-DC8451', 'ALEX', '0740731806', 'alex.gtahi@gmail.com', 'NAIROBI, MOI AVENUE', '', '254792580000', 1400.00, 0.00, 1400.00, 'Pending', 'Pending', 'Pending', '2026-08-11 15:10:18', '2026-08-11 15:10:22');

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
(15, 13, 1, 'Tenda F3 N300 Wireless Wi-Fi Router', 'N300', 'Tenda F3.jpg', 1, 1400.00, 1400.00, '2026-08-11 15:10:18');

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
(1, 2, 2, 'Tenda F3 N300 Wireless Wi-Fi Router', 'tenda-f3-n300-wireless-wi-fi-router', 'N300', 'Tenda F3 N300 Wireless Wi-Fi Router', 'Tenda F3 N300 Wireless Wi-Fi Router with 300Mbps wireless speed, three high-gain antennas, easy setup, multiple device support, smart bandwidth control, guest Wi-Fi and secure wireless connectivity.', 1400.00, NULL, NULL, 10, 5, 'Tenda F3.jpg', 1, 1, 0, '', '', '', '2026-08-07 15:12:13', '2026-08-11 09:11:27'),
(3, 2, 2, 'Tenda F6 N300 Wireless Wi-Fi Router', 'tenda-f6-n300-wireless-wi-fi-router', 'Tenda F6', '', '', 1700.00, NULL, NULL, 10, 5, '1786428454_4bcf589782b1.jpg', 1, 1, 0, '', '', '', '2026-08-11 06:07:34', '2026-08-11 09:10:53'),
(8, 2, 2, 'Tenda AC5 V3 AC1200 Wireless Dual Band Wi-Fi Router', 'tenda-ac5-v3-ac1200-wireless-dual-band-wi-fi-router', 'Tenda AC5', '', '', 3000.00, NULL, NULL, 10, 5, '1786463284_b9c83d63cfe6.webp', 1, 1, 0, 'Tenda AC5', '', 'router', '2026-08-11 15:48:04', '2026-08-11 16:11:57');

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
(40, 3, '300Mbps Wireless Speed', 'Enjoy fast and reliable internet for browsing, HD streaming, online classes, and video calls.', 1, '2026-08-11 09:10:53'),
(41, 3, '4 High-Gain Antennas', 'Provides stronger Wi-Fi coverage and stable signals, even in larger homes and offices.', 2, '2026-08-11 09:10:53'),
(42, 3, 'Easy Setup', 'Quick installation lets you get your network up and running within minutes.', 3, '2026-08-11 09:10:53'),
(43, 3, 'Wi-Fi Extender Mode', 'Expand the coverage of your existing Wi-Fi network without purchasing another access point.', 4, '2026-08-11 09:10:53'),
(44, 3, 'Smart Bandwidth Control', 'Allocate internet speeds fairly to ensure smooth performance for every connected user.', 5, '2026-08-11 09:10:53'),
(45, 3, 'Parental Controls', 'Control internet access and manage online time for children and family members.', 6, '2026-08-11 09:10:53'),
(46, 3, 'Multiple Operating Modes', 'Works as a Router, Access Point, Repeater, or WISP to suit different networking needs.', 7, '2026-08-11 09:10:53'),
(47, 1, '300Mbps Wireless Speed', 'Enjoy smooth web browsing, HD video streaming, online classes, and video calls.', 1, '2026-08-11 09:11:27'),
(48, 1, 'Three High-Gain Antennas', 'Provides stronger Wi-Fi coverage and better signal throughout your home or office.', 2, '2026-08-11 09:11:27'),
(49, 1, 'Easy Setup', 'Get connected in minutes with a simple installation process and user-friendly interface.', 3, '2026-08-11 09:11:27'),
(50, 1, 'Multiple Device Support', 'Connect smartphones, laptops, Smart TVs, and other Wi-Fi devices simultaneously.', 4, '2026-08-11 09:11:27'),
(51, 1, 'Smart Bandwidth Control', 'Manage internet speeds for connected users to ensure a stable network experience.', 5, '2026-08-11 09:11:27'),
(52, 1, 'Guest Wi-Fi Network', 'Create a separate network for visitors without exposing your private devices.', 6, '2026-08-11 09:11:27'),
(53, 1, 'Secure Wireless Connection', 'Protect your network with advanced Wi-Fi security to keep your data safe.', 7, '2026-08-11 09:11:27'),
(54, 1, 'Energy Efficient', 'Designed to deliver reliable performance while consuming minimal power.', 8, '2026-08-11 09:11:27'),
(55, 1, 'Perfect for Home & Small Office', 'An affordable solution for everyday internet use in homes, apartments, and small businesses.', 9, '2026-08-11 09:11:27'),
(97, 8, 'AC1200 Dual-Band Wi-Fi', 'Enjoy faster and more reliable Wi-Fi for streaming, browsing, online classes, video calls, and gaming.', 1, '2026-08-11 16:11:57'),
(98, 8, '2.4GHz + 5GHz Wi-Fi', 'Use 2.4GHz for wider coverage or 5GHz for faster connections with compatible devices.', 2, '2026-08-11 16:11:57'),
(99, 8, '4 High-Gain Antennas', 'Four external 6dBi antennas help provide stronger and more consistent Wi-Fi coverage.', 3, '2026-08-11 16:11:57'),
(100, 8, 'Easy Setup', 'Get your home or office network running quickly with a simple and user-friendly setup.', 4, '2026-08-11 16:11:57'),
(101, 8, 'Parental Controls', 'Control internet access and manage which devices can connect to your network.', 5, '2026-08-11 16:11:57'),
(102, 8, 'Wi-Fi Repeater Mode', 'Extend an existing Wi-Fi signal to reach areas with weak coverage.', 6, '2026-08-11 16:11:57'),
(103, 8, 'Access Point Mode', 'Use the router to add Wi-Fi to an existing wired network.', 7, '2026-08-11 16:11:57'),
(104, 8, 'Guest Wi-Fi', 'Give visitors internet access while keeping your main network separate.', 8, '2026-08-11 16:11:57');

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
(6, 3, '1786428454_4bcf589782b1.jpg', 'Tenda F6 N300 Wireless Wi-Fi Router', 1, 1, '2026-08-11 06:07:34'),
(7, 3, '1786428454_1bda418274be.jpg', 'Tenda F6 N300 Wireless Wi-Fi Router - Image 2', 2, 0, '2026-08-11 06:07:34'),
(8, 3, '1786428454_7848cd5faa74.jpg', 'Tenda F6 N300 Wireless Wi-Fi Router - Image 3', 3, 0, '2026-08-11 06:07:34'),
(9, 3, '1786428454_187dfa1c75b1.jpg', 'Tenda F6 N300 Wireless Wi-Fi Router - Image 4', 4, 0, '2026-08-11 06:07:34'),
(10, 3, '1786428454_84d110eef8e5.jpg', 'Tenda F6 N300 Wireless Wi-Fi Router - Image 5', 5, 0, '2026-08-11 06:07:34'),
(11, 3, '1786428454_dcc6f0178942.webp', 'Tenda F6 N300 Wireless Wi-Fi Router - Image 6', 6, 0, '2026-08-11 06:07:34'),
(23, 8, '1786463284_b9c83d63cfe6.webp', 'Tenda AC5 V3 AC1200 Wireless Dual Band Wi-Fi Router', 1, 1, '2026-08-11 15:48:04'),
(24, 8, '1786463284_b1719a33b7d2.webp', 'Tenda AC5 V3 AC1200 Wireless Dual Band Wi-Fi Router - Image 2', 2, 0, '2026-08-11 15:48:04'),
(25, 8, '1786463284_73eb094942ff.jpg', 'Tenda AC5 V3 AC1200 Wireless Dual Band Wi-Fi Router - Image 3', 3, 0, '2026-08-11 15:48:04'),
(26, 8, '1786463284_ce2aa4c6cdc6.webp', 'Tenda AC5 V3 AC1200 Wireless Dual Band Wi-Fi Router - Image 4', 4, 0, '2026-08-11 15:48:04'),
(27, 8, '1786463284_973bdd89ec0e.jpg', 'Tenda AC5 V3 AC1200 Wireless Dual Band Wi-Fi Router - Image 5', 5, 0, '2026-08-11 15:48:04');

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
(1, 'ALEX GITAHI', 'alex.gtahi@gmail.com', '0757130130', '$2y$10$H/d9FZ39dOCi28MtT6P8w.uYrZetgIHhMtbFsGLZeAS65U0b09/4.', 'admin', 1, '2026-08-12 07:46:23', '2026-08-09 18:22:42', '2026-08-12 04:46:23');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inquiries`
--
ALTER TABLE `inquiries`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `order_activity_logs`
--
ALTER TABLE `order_activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `product_features`
--
ALTER TABLE `product_features`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

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
