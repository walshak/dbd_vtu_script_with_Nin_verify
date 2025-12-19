-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 16, 2025 at 08:07 PM
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
-- Database: `laravel_vtu`
--

-- --------------------------------------------------------

--
-- Table structure for table `airtimepinprice`
--

CREATE TABLE `airtimepinprice` (
  `aId` bigint(20) UNSIGNED NOT NULL,
  `aNetwork` varchar(10) NOT NULL,
  `cost_percentage` decimal(5,2) DEFAULT NULL COMMENT 'Cost % from Uzobest (e.g., 98.5)',
  `selling_percentage` decimal(5,2) DEFAULT NULL COMMENT 'Selling % admin sets (e.g., 99.0)',
  `aUserDiscount` decimal(5,2) NOT NULL DEFAULT 99.00,
  `profit_margin` decimal(5,2) DEFAULT NULL COMMENT 'Calculated: selling_percentage - cost_percentage',
  `aAgentDiscount` decimal(5,2) DEFAULT NULL,
  `aVendorDiscount` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `airtimes`
--

CREATE TABLE `airtimes` (
  `aId` bigint(20) UNSIGNED NOT NULL,
  `nId` int(11) NOT NULL,
  `airtimeAmount` int(11) NOT NULL,
  `userDiscount` decimal(5,2) NOT NULL DEFAULT 0.00,
  `agentDiscount` decimal(5,2) NOT NULL DEFAULT 0.00,
  `apiDiscount` decimal(5,2) NOT NULL DEFAULT 0.00,
  `airtimeType` varchar(50) NOT NULL DEFAULT 'VTU'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `airtimes`
--

INSERT INTO `airtimes` (`aId`, `nId`, `airtimeAmount`, `userDiscount`, `agentDiscount`, `apiDiscount`, `airtimeType`) VALUES
(1, 1, 100, 2.50, 3.00, 3.50, 'VTU'),
(2, 1, 200, 2.50, 3.00, 3.50, 'VTU'),
(3, 1, 500, 2.50, 3.00, 3.50, 'VTU'),
(4, 1, 1000, 2.50, 3.00, 3.50, 'VTU'),
(5, 1, 100, 4.00, 5.00, 6.00, 'Share and Sell'),
(6, 1, 200, 4.00, 5.00, 6.00, 'Share and Sell'),
(7, 1, 500, 4.00, 5.00, 6.00, 'Share and Sell'),
(8, 2, 100, 2.00, 2.50, 3.00, 'VTU'),
(9, 2, 200, 2.00, 2.50, 3.00, 'VTU'),
(10, 2, 500, 2.00, 2.50, 3.00, 'VTU'),
(11, 2, 1000, 2.00, 2.50, 3.00, 'VTU'),
(12, 3, 100, 3.00, 3.50, 4.00, 'VTU'),
(13, 3, 200, 3.00, 3.50, 4.00, 'VTU'),
(14, 3, 500, 3.00, 3.50, 4.00, 'VTU'),
(15, 3, 1000, 3.00, 3.50, 4.00, 'VTU'),
(16, 4, 100, 2.50, 3.00, 3.50, 'VTU'),
(17, 4, 200, 2.50, 3.00, 3.50, 'VTU'),
(18, 4, 500, 2.50, 3.00, 3.50, 'VTU'),
(19, 4, 1000, 2.50, 3.00, 3.50, 'VTU');

-- --------------------------------------------------------

--
-- Table structure for table `alphatopupprice`
--

CREATE TABLE `alphatopupprice` (
  `alphaId` bigint(20) UNSIGNED NOT NULL,
  `buyingPrice` decimal(10,2) NOT NULL,
  `sellingPrice` decimal(10,2) NOT NULL,
  `agent` decimal(10,2) NOT NULL,
  `vendor` decimal(10,2) NOT NULL,
  `dPosted` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `apilinks`
--

CREATE TABLE `apilinks` (
  `aId` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `value` text NOT NULL,
  `type` varchar(20) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `priority` int(11) NOT NULL DEFAULT 1,
  `auth_type` varchar(20) NOT NULL DEFAULT 'token',
  `auth_params` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`auth_params`)),
  `success_rate` decimal(5,2) NOT NULL DEFAULT 100.00,
  `response_time` int(11) NOT NULL DEFAULT 0,
  `last_checked` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `apilinks`
--

INSERT INTO `apilinks` (`aId`, `name`, `value`, `type`, `is_active`, `priority`, `auth_type`, `auth_params`, `success_rate`, `response_time`, `last_checked`, `created_at`, `updated_at`) VALUES
(1, 'Topupmate', 'https://topupmate.com/api/user/', 'Wallet', 1, 1, 'token', NULL, 100.00, 0, NULL, '2025-12-05 05:07:04', '2025-12-05 05:07:04'),
(2, 'Topupmate', 'https://topupmate.com/api/airtime/', 'Airtime', 1, 1, 'token', NULL, 100.00, 0, NULL, '2025-12-05 05:07:04', '2025-12-05 05:07:04'),
(3, 'Topupmate', 'https://topupmate.com/api/data/', 'Data', 1, 1, 'token', NULL, 100.00, 0, NULL, '2025-12-05 05:07:04', '2025-12-05 05:07:04'),
(4, 'Topupmate', 'https://topupmate.com/api/cabletv/verify/', 'CableVer', 1, 1, 'token', NULL, 100.00, 0, NULL, '2025-12-05 05:07:04', '2025-12-05 05:07:04'),
(5, 'Topupmate', 'https://topupmate.com/api/cabletv/', 'Cable', 1, 1, 'token', NULL, 100.00, 0, NULL, '2025-12-05 05:07:04', '2025-12-05 05:07:04'),
(6, 'Topupmate', 'https://topupmate.com/api/electricity/verify/', 'ElectricityVer', 1, 1, 'token', NULL, 100.00, 0, NULL, '2025-12-05 05:07:04', '2025-12-05 05:07:04'),
(7, 'Topupmate', 'https://topupmate.com/api/electricity/', 'Electricity', 1, 1, 'token', NULL, 100.00, 0, NULL, '2025-12-05 05:07:04', '2025-12-05 05:07:04'),
(8, 'Topupmate', 'https://topupmate.com/api/exam/', 'Exam', 1, 1, 'token', NULL, 100.00, 0, NULL, '2025-12-05 05:07:04', '2025-12-05 05:07:04'),
(9, 'N3T Data', 'https://n3tdata.com/api/user/', 'Wallet', 1, 2, 'token', NULL, 100.00, 0, NULL, '2025-12-05 05:07:04', '2025-12-05 05:07:04'),
(10, 'N3T Data', 'https://n3tdata.com/api/topup/', 'Airtime', 1, 2, 'token', NULL, 100.00, 0, NULL, '2025-12-05 05:07:04', '2025-12-05 05:07:04'),
(11, 'N3T Data', 'https://n3tdata.com/api/data/', 'Data', 1, 2, 'token', NULL, 100.00, 0, NULL, '2025-12-05 05:07:04', '2025-12-05 05:07:04'),
(12, 'N3T Data', 'https://n3tdata.com/api/cable/cable-validation', 'CableVer', 1, 2, 'token', NULL, 100.00, 0, NULL, '2025-12-05 05:07:04', '2025-12-05 05:07:04'),
(13, 'N3T Data', 'https://n3tdata.com/api/cable/', 'Cable', 1, 2, 'token', NULL, 100.00, 0, NULL, '2025-12-05 05:07:04', '2025-12-05 05:07:04'),
(14, 'N3T Data', 'https://n3tdata.com/api/bill/bill-validation', 'ElectricityVer', 1, 2, 'token', NULL, 100.00, 0, NULL, '2025-12-05 05:07:04', '2025-12-05 05:07:04'),
(15, 'N3T Data', 'https://n3tdata.com/api/bill/', 'Electricity', 1, 2, 'token', NULL, 100.00, 0, NULL, '2025-12-05 05:07:04', '2025-12-05 05:07:04'),
(16, 'N3T Data', 'https://n3tdata.com/api/exam/', 'Exam', 1, 2, 'token', NULL, 100.00, 0, NULL, '2025-12-05 05:07:04', '2025-12-05 05:07:04'),
(17, 'Bilalsadasub', 'https://bilalsadasub.com/api/user/', 'Wallet', 1, 3, 'token', NULL, 100.00, 0, NULL, '2025-12-05 05:07:04', '2025-12-05 05:07:04'),
(18, 'Bilalsadasub', 'https://bilalsadasub.com/api/topup/', 'Airtime', 1, 3, 'token', NULL, 100.00, 0, NULL, '2025-12-05 05:07:04', '2025-12-05 05:07:04'),
(19, 'Bilalsadasub', 'https://bilalsadasub.com/api/data/', 'Data', 1, 3, 'token', NULL, 100.00, 0, NULL, '2025-12-05 05:07:04', '2025-12-05 05:07:04'),
(20, 'Bilalsadasub', 'https://bilalsadasub.com/api/cable/cable-validation', 'CableVer', 1, 3, 'token', NULL, 100.00, 0, NULL, '2025-12-05 05:07:04', '2025-12-05 05:07:04'),
(21, 'Bilalsadasub', 'https://bilalsadasub.com/api/cable/', 'Cable', 1, 3, 'token', NULL, 100.00, 0, NULL, '2025-12-05 05:07:04', '2025-12-05 05:07:04'),
(22, 'Bilalsadasub', 'https://bilalsadasub.com/api/bill/bill-validation', 'ElectricityVer', 1, 3, 'token', NULL, 100.00, 0, NULL, '2025-12-05 05:07:04', '2025-12-05 05:07:04'),
(23, 'Bilalsadasub', 'https://bilalsadasub.com/api/bill/', 'Electricity', 1, 3, 'token', NULL, 100.00, 0, NULL, '2025-12-05 05:07:04', '2025-12-05 05:07:04'),
(24, 'Bilalsadasub', 'https://bilalsadasub.com/api/exam/', 'Exam', 1, 3, 'token', NULL, 100.00, 0, NULL, '2025-12-05 05:07:04', '2025-12-05 05:07:04'),
(25, 'Aabaxztech', 'https://aabaxztech.com/api/user/', 'Wallet', 1, 4, 'token', NULL, 100.00, 0, NULL, '2025-12-05 05:07:04', '2025-12-05 05:07:04'),
(26, 'Aabaxztech', 'https://aabaxztech.com/api/topup/', 'Airtime', 1, 4, 'token', NULL, 100.00, 0, NULL, '2025-12-05 05:07:04', '2025-12-05 05:07:04'),
(27, 'Aabaxztech', 'https://aabaxztech.com/api/data/', 'Data', 1, 4, 'token', NULL, 100.00, 0, NULL, '2025-12-05 05:07:04', '2025-12-05 05:07:04'),
(28, 'Aabaxztech', 'https://aabaxztech.com/api/validateiuc', 'CableVer', 1, 4, 'token', NULL, 100.00, 0, NULL, '2025-12-05 05:07:04', '2025-12-05 05:07:04'),
(29, 'Aabaxztech', 'https://aabaxztech.com/api/cablesub/', 'Cable', 1, 4, 'token', NULL, 100.00, 0, NULL, '2025-12-05 05:07:04', '2025-12-05 05:07:04'),
(30, 'Aabaxztech', 'https://aabaxztech.com/api/validatemeter', 'ElectricityVer', 1, 4, 'token', NULL, 100.00, 0, NULL, '2025-12-05 05:07:04', '2025-12-05 05:07:04'),
(31, 'Aabaxztech', 'https://aabaxztech.com/api/billpayment/', 'Electricity', 1, 4, 'token', NULL, 100.00, 0, NULL, '2025-12-05 05:07:04', '2025-12-05 05:07:04'),
(32, 'Aabaxztech', 'https://aabaxztech.com/api/epin/', 'Exam', 1, 4, 'token', NULL, 100.00, 0, NULL, '2025-12-05 05:07:04', '2025-12-05 05:07:04'),
(33, 'Maskawasub', 'https://maskawasub.com/api/user/', 'Wallet', 1, 5, 'token', NULL, 100.00, 0, NULL, '2025-12-05 05:07:04', '2025-12-05 05:07:04'),
(34, 'Maskawasub', 'https://maskawasub.com/api/topup/', 'Airtime', 1, 5, 'token', NULL, 100.00, 0, NULL, '2025-12-05 05:07:04', '2025-12-05 05:07:04'),
(35, 'Maskawasub', 'https://maskawasub.com/api/data/', 'Data', 1, 5, 'token', NULL, 100.00, 0, NULL, '2025-12-05 05:07:04', '2025-12-05 05:07:04'),
(36, 'Maskawasub', 'https://maskawasub.com/api/validateiuc', 'CableVer', 1, 5, 'token', NULL, 100.00, 0, NULL, '2025-12-05 05:07:04', '2025-12-05 05:07:04'),
(37, 'Maskawasub', 'https://maskawasub.com/api/cablesub/', 'Cable', 1, 5, 'token', NULL, 100.00, 0, NULL, '2025-12-05 05:07:04', '2025-12-05 05:07:04'),
(38, 'Maskawasub', 'https://maskawasub.com/api/validatemeter', 'ElectricityVer', 1, 5, 'token', NULL, 100.00, 0, NULL, '2025-12-05 05:07:04', '2025-12-05 05:07:04'),
(39, 'Maskawasub', 'https://maskawasub.com/api/billpayment/', 'Electricity', 1, 5, 'token', NULL, 100.00, 0, NULL, '2025-12-05 05:07:04', '2025-12-05 05:07:04'),
(40, 'Maskawasub', 'https://maskawasub.com/api/epin/', 'Exam', 1, 5, 'token', NULL, 100.00, 0, NULL, '2025-12-05 05:07:04', '2025-12-05 05:07:04'),
(41, 'Husmodataapi', 'https://husmodataapi.com/api/user/', 'Wallet', 1, 6, 'token', NULL, 100.00, 0, NULL, '2025-12-05 05:07:04', '2025-12-05 05:07:04'),
(42, 'Husmodataapi', 'https://husmodataapi.com/api/topup/', 'Airtime', 1, 6, 'token', NULL, 100.00, 0, NULL, '2025-12-05 05:07:04', '2025-12-05 05:07:04'),
(43, 'Husmodataapi', 'https://husmodataapi.com/api/data/', 'Data', 1, 6, 'token', NULL, 100.00, 0, NULL, '2025-12-05 05:07:04', '2025-12-05 05:07:04'),
(44, 'Husmodataapi', 'https://husmodataapi.com/api/validateiuc', 'CableVer', 1, 6, 'token', NULL, 100.00, 0, NULL, '2025-12-05 05:07:04', '2025-12-05 05:07:04'),
(45, 'Husmodataapi', 'https://husmodataapi.com/api/cablesub/', 'Cable', 1, 6, 'token', NULL, 100.00, 0, NULL, '2025-12-05 05:07:04', '2025-12-05 05:07:04'),
(46, 'Husmodataapi', 'https://husmodataapi.com/api/validatemeter', 'ElectricityVer', 1, 6, 'token', NULL, 100.00, 0, NULL, '2025-12-05 05:07:04', '2025-12-05 05:07:04'),
(47, 'Husmodataapi', 'https://husmodataapi.com/api/billpayment/', 'Electricity', 1, 6, 'token', NULL, 100.00, 0, NULL, '2025-12-05 05:07:04', '2025-12-05 05:07:04'),
(48, 'Husmodataapi', 'https://husmodataapi.com/api/epin/', 'Exam', 1, 6, 'token', NULL, 100.00, 0, NULL, '2025-12-05 05:07:04', '2025-12-05 05:07:04'),
(49, 'Gongozconcept', 'https://gongozconcept.com/api/user/', 'Wallet', 1, 7, 'token', NULL, 100.00, 0, NULL, '2025-12-05 05:07:04', '2025-12-05 05:07:04'),
(50, 'Gongozconcept', 'https://gongozconcept.com/api/topup/', 'Airtime', 1, 7, 'token', NULL, 100.00, 0, NULL, '2025-12-05 05:07:04', '2025-12-05 05:07:04'),
(51, 'Gongozconcept', 'https://gongozconcept.com/api/data/', 'Data', 1, 7, 'token', NULL, 100.00, 0, NULL, '2025-12-05 05:07:04', '2025-12-05 05:07:04'),
(52, 'DataPin Provider', 'https://datapinapi.com/api/', 'Data Pin', 1, 1, 'token', NULL, 100.00, 0, NULL, '2025-12-05 05:07:04', '2025-12-05 05:07:04'),
(53, 'N3TDATA', 'https://n3tdata.com/api/topup', 'Airtime', 1, 1, 'token', NULL, 100.00, 0, NULL, '2025-12-05 05:07:04', '2025-12-05 05:07:04'),
(54, 'BilalSadaSub', 'https://bilalsadasub.com/api/topup', 'Airtime', 1, 1, 'token', NULL, 100.00, 0, NULL, '2025-12-05 05:07:04', '2025-12-05 05:07:04'),
(55, 'N3TDATA', 'https://n3tdata.com/api/data', 'Data', 1, 1, 'token', NULL, 100.00, 0, NULL, '2025-12-05 05:07:04', '2025-12-05 05:07:04'),
(56, 'BilalSadaSub', 'https://bilalsadasub.com/api/data', 'Data', 1, 1, 'token', NULL, 100.00, 0, NULL, '2025-12-05 05:07:04', '2025-12-05 05:07:04'),
(57, 'N3TDATA', 'https://n3tdata.com/api/cablesub', 'Cable', 1, 1, 'token', NULL, 100.00, 0, NULL, '2025-12-05 05:07:04', '2025-12-05 05:07:04'),
(58, 'BilalSadaSub', 'https://bilalsadasub.com/api/cable', 'Cable', 1, 1, 'token', NULL, 100.00, 0, NULL, '2025-12-05 05:07:04', '2025-12-05 05:07:04'),
(59, 'N3TDATA', 'https://n3tdata.com/api/validate-customer', 'CableVer', 1, 1, 'token', NULL, 100.00, 0, NULL, '2025-12-05 05:07:04', '2025-12-05 05:07:04'),
(60, 'N3TDATA', 'https://n3tdata.com/api/electricity', 'Electricity', 1, 1, 'token', NULL, 100.00, 0, NULL, '2025-12-05 05:07:04', '2025-12-05 05:07:04'),
(61, 'BilalSadaSub', 'https://bilalsadasub.com/api/electricity', 'Electricity', 1, 1, 'token', NULL, 100.00, 0, NULL, '2025-12-05 05:07:04', '2025-12-05 05:07:04'),
(62, 'N3TDATA', 'https://n3tdata.com/api/validate-customer', 'ElectricityVer', 1, 1, 'token', NULL, 100.00, 0, NULL, '2025-12-05 05:07:04', '2025-12-05 05:07:04'),
(63, 'N3TDATA', 'https://n3tdata.com/api/exam', 'Exam', 1, 1, 'token', NULL, 100.00, 0, NULL, '2025-12-05 05:07:04', '2025-12-05 05:07:04'),
(64, 'N3TDATA', 'https://n3tdata.com/api/datapin', 'Data Pin', 1, 1, 'token', NULL, 100.00, 0, NULL, '2025-12-05 05:07:04', '2025-12-05 05:07:04'),
(65, 'Uzobest', 'https://uzobestgsm.com/api', 'Airtime', 1, 1, 'header', '{\"header_name\":\"Authorization\",\"header_prefix\":\"Token \"}', 100.00, 0, NULL, '2025-12-05 05:07:05', '2025-12-05 05:07:05'),
(66, 'Uzobest', 'https://uzobestgsm.com/api/data/', 'Data', 1, 1, 'header', '{\"header_name\":\"Authorization\",\"header_prefix\":\"Token \"}', 100.00, 0, NULL, '2025-12-05 05:07:05', '2025-12-05 05:07:05'),
(67, 'Uzobest', 'https://uzobestgsm.com/api/cabletv/', 'Cable', 1, 1, 'header', '{\"header_name\":\"Authorization\",\"header_prefix\":\"Token \"}', 100.00, 0, NULL, '2025-12-05 05:07:05', '2025-12-05 05:07:05'),
(68, 'Uzobest', 'https://uzobestgsm.com/api/electricity/', 'Electricity', 1, 1, 'header', '{\"header_name\":\"Authorization\",\"header_prefix\":\"Token \"}', 100.00, 0, NULL, '2025-12-05 05:07:05', '2025-12-05 05:07:05'),
(69, 'Uzobest', 'https://uzobestgsm.com/api/exam/', 'Exam', 1, 1, 'header', '{\"header_name\":\"Authorization\",\"header_prefix\":\"Token \"}', 100.00, 0, NULL, '2025-12-05 05:07:05', '2025-12-05 05:07:05'),
(70, 'Uzobest', 'https://uzobestgsm.com/api/validate-customer/', 'CableVer', 1, 1, 'header', '{\"header_name\":\"Authorization\",\"header_prefix\":\"Token \"}', 100.00, 0, NULL, '2025-12-05 05:07:05', '2025-12-05 05:07:05'),
(71, 'Uzobest', 'https://uzobestgsm.com/api/validate-customer/', 'ElectricityVer', 1, 1, 'header', '{\"header_name\":\"Authorization\",\"header_prefix\":\"Token \"}', 100.00, 0, NULL, '2025-12-05 05:07:05', '2025-12-05 05:07:05');

-- --------------------------------------------------------

--
-- Table structure for table `api_configs`
--

CREATE TABLE `api_configs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `config_key` varchar(100) NOT NULL,
  `config_value` text NOT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `api_configurations`
--

CREATE TABLE `api_configurations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `config_key` varchar(255) NOT NULL,
  `config_value` text DEFAULT NULL,
  `service_type` varchar(255) DEFAULT NULL,
  `network` varchar(255) DEFAULT NULL,
  `provider_type` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `api_configurations`
--

INSERT INTO `api_configurations` (`id`, `config_key`, `config_value`, `service_type`, `network`, `provider_type`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'mtnVTUKey', '245141f6de9c0aa211b3a6baf1d1533c642caf24', 'airtime', 'MTN', 'VTU', 'MTN VTU API Key', 1, '2025-12-05 05:07:04', '2025-12-05 05:07:05'),
(2, 'mtnVTUProvider', 'https://uzobestgsm.com/api', 'airtime', 'MTN', 'VTU', 'MTN VTU Provider URL', 1, '2025-12-05 05:07:04', '2025-12-05 05:07:05'),
(3, 'mtnShareSellKey', '245141f6de9c0aa211b3a6baf1d1533c642caf24', 'airtime', 'MTN', 'ShareSell', 'MTN ShareSell API Key', 1, '2025-12-05 05:07:04', '2025-12-05 05:07:05'),
(4, 'mtnShareSellProvider', 'https://uzobestgsm.com/api', 'airtime', 'MTN', 'ShareSell', 'MTN ShareSell Provider URL', 1, '2025-12-05 05:07:04', '2025-12-05 05:07:05'),
(5, 'mtnSMEApi', '245141f6de9c0aa211b3a6baf1d1533c642caf24', 'data', 'MTN', 'SME', 'MTN SME Data API Key', 1, '2025-12-05 05:07:04', '2025-12-05 05:07:05'),
(6, 'mtnSMEProvider', 'https://uzobestgsm.com/api/data/', 'data', 'MTN', 'SME', 'MTN SME Data Provider URL', 1, '2025-12-05 05:07:04', '2025-12-05 05:07:05'),
(7, 'mtnCorporateApi', '245141f6de9c0aa211b3a6baf1d1533c642caf24', 'data', 'MTN', 'Corporate', 'MTN Corporate Data API Key', 1, '2025-12-05 05:07:04', '2025-12-05 05:07:05'),
(8, 'mtnCorporateProvider', 'https://uzobestgsm.com/api/data/', 'data', 'MTN', 'Corporate', 'MTN Corporate Data Provider URL', 1, '2025-12-05 05:07:04', '2025-12-05 05:07:05'),
(9, 'mtnGiftingApi', '245141f6de9c0aa211b3a6baf1d1533c642caf24', 'data', 'MTN', 'Gifting', 'MTN Gifting Data API Key', 1, '2025-12-05 05:07:04', '2025-12-05 05:07:05'),
(10, 'mtnGiftingProvider', 'https://uzobestgsm.com/api/data/', 'data', 'MTN', 'Gifting', 'MTN Gifting Data Provider URL', 1, '2025-12-05 05:07:04', '2025-12-05 05:07:05'),
(11, 'airtelVTUKey', '245141f6de9c0aa211b3a6baf1d1533c642caf24', 'airtime', 'AIRTEL', 'VTU', 'AIRTEL VTU API Key', 1, '2025-12-05 05:07:04', '2025-12-05 05:07:05'),
(12, 'airtelVTUProvider', 'https://uzobestgsm.com/api', 'airtime', 'AIRTEL', 'VTU', 'AIRTEL VTU Provider URL', 1, '2025-12-05 05:07:04', '2025-12-05 05:07:05'),
(13, 'airtelShareSellKey', '245141f6de9c0aa211b3a6baf1d1533c642caf24', 'airtime', 'AIRTEL', 'ShareSell', 'AIRTEL ShareSell API Key', 1, '2025-12-05 05:07:04', '2025-12-05 05:07:05'),
(14, 'airtelShareSellProvider', 'https://uzobestgsm.com/api', 'airtime', 'AIRTEL', 'ShareSell', 'AIRTEL ShareSell Provider URL', 1, '2025-12-05 05:07:04', '2025-12-05 05:07:05'),
(15, 'airtelSMEApi', '245141f6de9c0aa211b3a6baf1d1533c642caf24', 'data', 'AIRTEL', 'SME', 'AIRTEL SME Data API Key', 1, '2025-12-05 05:07:04', '2025-12-05 05:07:05'),
(16, 'airtelSMEProvider', 'https://uzobestgsm.com/api/data/', 'data', 'AIRTEL', 'SME', 'AIRTEL SME Data Provider URL', 1, '2025-12-05 05:07:05', '2025-12-05 05:07:05'),
(17, 'airtelCorporateApi', '245141f6de9c0aa211b3a6baf1d1533c642caf24', 'data', 'AIRTEL', 'Corporate', 'AIRTEL Corporate Data API Key', 1, '2025-12-05 05:07:05', '2025-12-05 05:07:05'),
(18, 'airtelCorporateProvider', 'https://uzobestgsm.com/api/data/', 'data', 'AIRTEL', 'Corporate', 'AIRTEL Corporate Data Provider URL', 1, '2025-12-05 05:07:05', '2025-12-05 05:07:05'),
(19, 'airtelGiftingApi', '245141f6de9c0aa211b3a6baf1d1533c642caf24', 'data', 'AIRTEL', 'Gifting', 'AIRTEL Gifting Data API Key', 1, '2025-12-05 05:07:05', '2025-12-05 05:07:05'),
(20, 'airtelGiftingProvider', 'https://uzobestgsm.com/api/data/', 'data', 'AIRTEL', 'Gifting', 'AIRTEL Gifting Data Provider URL', 1, '2025-12-05 05:07:05', '2025-12-05 05:07:05'),
(21, 'gloVTUKey', '245141f6de9c0aa211b3a6baf1d1533c642caf24', 'airtime', 'GLO', 'VTU', 'GLO VTU API Key', 1, '2025-12-05 05:07:05', '2025-12-05 05:07:05'),
(22, 'gloVTUProvider', 'https://uzobestgsm.com/api', 'airtime', 'GLO', 'VTU', 'GLO VTU Provider URL', 1, '2025-12-05 05:07:05', '2025-12-05 05:07:05'),
(23, 'gloShareSellKey', '245141f6de9c0aa211b3a6baf1d1533c642caf24', 'airtime', 'GLO', 'ShareSell', 'GLO ShareSell API Key', 1, '2025-12-05 05:07:05', '2025-12-05 05:07:05'),
(24, 'gloShareSellProvider', 'https://uzobestgsm.com/api', 'airtime', 'GLO', 'ShareSell', 'GLO ShareSell Provider URL', 1, '2025-12-05 05:07:05', '2025-12-05 05:07:05'),
(25, 'gloSMEApi', '245141f6de9c0aa211b3a6baf1d1533c642caf24', 'data', 'GLO', 'SME', 'GLO SME Data API Key', 1, '2025-12-05 05:07:05', '2025-12-05 05:07:05'),
(26, 'gloSMEProvider', 'https://uzobestgsm.com/api/data/', 'data', 'GLO', 'SME', 'GLO SME Data Provider URL', 1, '2025-12-05 05:07:05', '2025-12-05 05:07:05'),
(27, 'gloCorporateApi', '245141f6de9c0aa211b3a6baf1d1533c642caf24', 'data', 'GLO', 'Corporate', 'GLO Corporate Data API Key', 1, '2025-12-05 05:07:05', '2025-12-05 05:07:05'),
(28, 'gloCorporateProvider', 'https://uzobestgsm.com/api/data/', 'data', 'GLO', 'Corporate', 'GLO Corporate Data Provider URL', 1, '2025-12-05 05:07:05', '2025-12-05 05:07:05'),
(29, 'gloGiftingApi', '245141f6de9c0aa211b3a6baf1d1533c642caf24', 'data', 'GLO', 'Gifting', 'GLO Gifting Data API Key', 1, '2025-12-05 05:07:05', '2025-12-05 05:07:05'),
(30, 'gloGiftingProvider', 'https://uzobestgsm.com/api/data/', 'data', 'GLO', 'Gifting', 'GLO Gifting Data Provider URL', 1, '2025-12-05 05:07:05', '2025-12-05 05:07:05'),
(31, '9mobileVTUKey', '245141f6de9c0aa211b3a6baf1d1533c642caf24', 'airtime', '9MOBILE', 'VTU', '9MOBILE VTU API Key', 1, '2025-12-05 05:07:05', '2025-12-05 05:07:05'),
(32, '9mobileVTUProvider', 'https://uzobestgsm.com/api', 'airtime', '9MOBILE', 'VTU', '9MOBILE VTU Provider URL', 1, '2025-12-05 05:07:05', '2025-12-05 05:07:05'),
(33, '9mobileShareSellKey', '245141f6de9c0aa211b3a6baf1d1533c642caf24', 'airtime', '9MOBILE', 'ShareSell', '9MOBILE ShareSell API Key', 1, '2025-12-05 05:07:05', '2025-12-05 05:07:05'),
(34, '9mobileShareSellProvider', 'https://uzobestgsm.com/api', 'airtime', '9MOBILE', 'ShareSell', '9MOBILE ShareSell Provider URL', 1, '2025-12-05 05:07:05', '2025-12-05 05:07:05'),
(35, '9mobileSMEApi', '245141f6de9c0aa211b3a6baf1d1533c642caf24', 'data', '9MOBILE', 'SME', '9MOBILE SME Data API Key', 1, '2025-12-05 05:07:05', '2025-12-05 05:07:05'),
(36, '9mobileSMEProvider', 'https://uzobestgsm.com/api/data/', 'data', '9MOBILE', 'SME', '9MOBILE SME Data Provider URL', 1, '2025-12-05 05:07:05', '2025-12-05 05:07:05'),
(37, '9mobileCorporateApi', '245141f6de9c0aa211b3a6baf1d1533c642caf24', 'data', '9MOBILE', 'Corporate', '9MOBILE Corporate Data API Key', 1, '2025-12-05 05:07:05', '2025-12-05 05:07:05'),
(38, '9mobileCorporateProvider', 'https://uzobestgsm.com/api/data/', 'data', '9MOBILE', 'Corporate', '9MOBILE Corporate Data Provider URL', 1, '2025-12-05 05:07:05', '2025-12-05 05:07:05'),
(39, '9mobileGiftingApi', '245141f6de9c0aa211b3a6baf1d1533c642caf24', 'data', '9MOBILE', 'Gifting', '9MOBILE Gifting Data API Key', 1, '2025-12-05 05:07:05', '2025-12-05 05:07:05'),
(40, '9mobileGiftingProvider', 'https://uzobestgsm.com/api/data/', 'data', '9MOBILE', 'Gifting', '9MOBILE Gifting Data Provider URL', 1, '2025-12-05 05:07:05', '2025-12-05 05:07:05'),
(41, 'cableVerificationApi', '245141f6de9c0aa211b3a6baf1d1533c642caf24', 'cable', NULL, NULL, 'Cable TV IUC Verification API Key', 1, '2025-12-05 05:07:05', '2025-12-05 05:07:05'),
(42, 'cableVerificationProvider', 'https://uzobestgsm.com/api/validate-customer/', 'cable', NULL, NULL, 'Cable TV IUC Verification Provider URL', 1, '2025-12-05 05:07:05', '2025-12-05 05:07:05'),
(43, 'cableApi', '245141f6de9c0aa211b3a6baf1d1533c642caf24', 'cable', NULL, NULL, 'Cable TV API Key', 1, '2025-12-05 05:07:05', '2025-12-05 05:07:05'),
(44, 'cableProvider', 'https://uzobestgsm.com/api/cabletv/', 'cable', NULL, NULL, 'Cable TV Provider URL', 1, '2025-12-05 05:07:05', '2025-12-05 05:07:05'),
(45, 'meterVerificationApi', '245141f6de9c0aa211b3a6baf1d1533c642caf24', 'electricity', NULL, NULL, 'Electricity Meter Verification API Key', 1, '2025-12-05 05:07:05', '2025-12-05 05:07:05'),
(46, 'meterVerificationProvider', 'https://uzobestgsm.com/api/validate-customer/', 'electricity', NULL, NULL, 'Electricity Meter Verification Provider URL', 1, '2025-12-05 05:07:05', '2025-12-05 05:07:05'),
(47, 'meterApi', '245141f6de9c0aa211b3a6baf1d1533c642caf24', 'electricity', NULL, NULL, 'Electricity API Key', 1, '2025-12-05 05:07:05', '2025-12-05 05:07:05'),
(48, 'meterProvider', 'https://uzobestgsm.com/api/electricity/', 'electricity', NULL, NULL, 'Electricity Provider URL', 1, '2025-12-05 05:07:05', '2025-12-05 05:07:05'),
(49, 'examApi', '245141f6de9c0aa211b3a6baf1d1533c642caf24', 'exam', NULL, NULL, 'Exam Checker API Key', 1, '2025-12-05 05:07:05', '2025-12-05 05:07:05'),
(50, 'examProvider', 'https://uzobestgsm.com/api/exam/', 'exam', NULL, NULL, 'Exam Checker Provider URL', 1, '2025-12-05 05:07:05', '2025-12-05 05:07:05'),
(51, 'rechargePinApi', '', 'recharge_pin', NULL, NULL, 'Recharge Card API Key', 1, '2025-12-05 05:07:05', '2025-12-05 05:07:05'),
(52, 'rechargePinProvider', '', 'recharge_pin', NULL, NULL, 'Recharge Card Provider URL', 1, '2025-12-05 05:07:05', '2025-12-05 05:07:05'),
(53, 'dataPinApi', '', 'data_pin', NULL, NULL, 'Data Pin API Key', 1, '2025-12-05 05:07:05', '2025-12-05 05:07:05'),
(54, 'dataPinProvider', '', 'data_pin', NULL, NULL, 'Data Pin Provider URL', 1, '2025-12-05 05:07:05', '2025-12-05 05:07:05'),
(55, 'alphaApi', '', 'alpha_topup', NULL, NULL, 'Alpha Topup API Key', 1, '2025-12-05 05:07:05', '2025-12-05 05:07:05'),
(56, 'alphaProvider', '', 'alpha_topup', NULL, NULL, 'Alpha Topup Provider URL', 1, '2025-12-05 05:07:05', '2025-12-05 05:07:05');

-- --------------------------------------------------------

--
-- Table structure for table `bulk_sms`
--

CREATE TABLE `bulk_sms` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `recipients` text NOT NULL,
  `total_recipients` int(11) NOT NULL,
  `sent_count` int(11) NOT NULL DEFAULT 0,
  `cost_per_sms` decimal(5,2) NOT NULL,
  `total_cost` decimal(10,2) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `scheduled_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cableid`
--

CREATE TABLE `cableid` (
  `cId` bigint(20) UNSIGNED NOT NULL,
  `cableid` varchar(10) DEFAULT NULL,
  `provider` varchar(10) NOT NULL,
  `providerStatus` varchar(10) NOT NULL DEFAULT 'On',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cable_ids`
--

CREATE TABLE `cable_ids` (
  `cId` bigint(20) UNSIGNED NOT NULL,
  `cableid` varchar(10) DEFAULT NULL,
  `provider` varchar(10) NOT NULL,
  `providerStatus` varchar(10) NOT NULL DEFAULT 'On',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cable_ids`
--

INSERT INTO `cable_ids` (`cId`, `cableid`, `provider`, `providerStatus`, `created_at`, `updated_at`) VALUES
(1, '2', 'dstv', 'On', '2025-12-05 05:07:08', '2025-12-05 05:07:08'),
(2, '1', 'gotv', 'On', '2025-12-05 05:07:08', '2025-12-05 05:07:08'),
(3, '3', 'startime', 'On', '2025-12-05 05:07:08', '2025-12-05 05:07:08');

-- --------------------------------------------------------

--
-- Table structure for table `cable_plans`
--

CREATE TABLE `cable_plans` (
  `cpId` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` varchar(255) NOT NULL,
  `userprice` varchar(255) NOT NULL,
  `cost_price` decimal(10,2) DEFAULT NULL COMMENT 'Cost from Uzobest API',
  `selling_price` decimal(10,2) DEFAULT NULL COMMENT 'Admin-configured selling price',
  `agentprice` varchar(255) NOT NULL,
  `vendorprice` varchar(255) NOT NULL,
  `planid` varchar(255) NOT NULL,
  `type` varchar(255) DEFAULT NULL,
  `cableprovider` tinyint(4) NOT NULL,
  `uzobest_cable_id` int(11) DEFAULT NULL COMMENT 'Uzobest API cable provider ID',
  `day` varchar(255) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `uzobest_plan_id` varchar(100) DEFAULT NULL,
  `profit_margin` decimal(10,2) DEFAULT NULL COMMENT 'Calculated: selling_price - cost_price'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cable_plans`
--

INSERT INTO `cable_plans` (`cpId`, `name`, `price`, `userprice`, `cost_price`, `selling_price`, `agentprice`, `vendorprice`, `planid`, `type`, `cableprovider`, `uzobest_cable_id`, `day`, `status`, `created_at`, `updated_at`, `uzobest_plan_id`, `profit_margin`) VALUES
(1, 'DStv Padi', '2150', '2200', 2150.00, 2200.00, '2100', '2050', 'dstv-padi', NULL, 1, 2, '30', 'active', '2025-12-05 05:07:08', '2025-12-16 17:10:19', '20', 50.00),
(2, 'DStv Yanga', '2950', '2950', 2950.00, 2950.00, '2900', '2850', 'dstv-yanga', NULL, 1, 2, '30', 'active', '2025-12-05 05:07:08', '2025-12-16 17:10:19', '6', 0.00),
(3, 'DStv Confam', '5300', '5300', 5300.00, 5300.00, '5200', '5100', 'dstv-confam', NULL, 1, 2, '30', 'active', '2025-12-05 05:07:08', '2025-12-16 17:10:19', '19', 0.00),
(4, 'DStv Compact', '9000', '9000', 9000.00, 9000.00, '8900', '8800', 'dstv-compact', NULL, 1, 2, '30', 'active', '2025-12-05 05:07:08', '2025-12-16 17:10:19', '7', 0.00),
(5, 'DStv Compact Plus', '14250', '14250', 14250.00, 14250.00, '14100', '14000', 'dstv-compact-plus', NULL, 1, 2, '30', 'active', '2025-12-05 05:07:08', '2025-12-16 17:10:19', '8', 0.00),
(6, 'DStv Premium', '21000', '21000', 21000.00, 21000.00, '20800', '20600', 'dstv-premium', NULL, 1, 2, '30', 'active', '2025-12-05 05:07:08', '2025-12-16 17:10:19', '9', 0.00),
(7, 'GOtv Smallie', '900', '900', 900.00, 900.00, '880', '860', 'gotv-smallie', NULL, 2, 1, '30', 'active', '2025-12-05 05:07:08', '2025-12-16 17:10:19', '34', 0.00),
(8, 'GOtv Jinja', '1900', '1900', 1900.00, 1900.00, '1850', '1800', 'gotv-jinja', NULL, 2, 1, '30', 'active', '2025-12-05 05:07:08', '2025-12-16 17:10:19', '16', 0.00),
(9, 'GOtv Jolli', '2800', '2800', 2800.00, 2800.00, '2750', '2700', 'gotv-jolli', NULL, 2, 1, '30', 'active', '2025-12-05 05:07:08', '2025-12-16 17:10:19', '17', 0.00),
(10, 'GOtv Max', '4150', '4150', 4150.00, 4150.00, '4100', '4050', 'gotv-max', NULL, 2, 1, '30', 'active', '2025-12-05 05:07:08', '2025-12-16 17:10:19', '2', 0.00),
(11, 'GOtv Supa', '5500', '5500', 5500.00, 5500.00, '5400', '5300', 'gotv-supa', NULL, 2, 1, '30', 'active', '2025-12-05 05:07:08', '2025-12-16 17:10:19', '47', 0.00),
(12, 'Startimes Nova', '900', '900', 900.00, 900.00, '880', '860', 'startimes-nova', NULL, 3, 3, '30', 'active', '2025-12-05 05:07:08', '2025-12-16 17:10:19', '14', 0.00),
(13, 'Startimes Basic', '1850', '1850', 1850.00, 1850.00, '1800', '1750', 'startimes-basic', NULL, 3, 3, '30', 'active', '2025-12-05 05:07:08', '2025-12-16 17:10:19', '12', 0.00),
(14, 'Startimes Smart', '2480', '2480', 2480.00, 2480.00, '2400', '2350', 'startimes-smart', NULL, 3, 3, '30', 'active', '2025-12-05 05:07:08', '2025-12-16 17:10:19', '13', 0.00),
(15, 'Startimes Classic', '2750', '2750', 2750.00, 2750.00, '2700', '2650', 'startimes-classic', NULL, 3, 3, '30', 'active', '2025-12-05 05:07:08', '2025-12-16 17:10:19', '11', 0.00),
(16, 'Startimes Super', '4200', '4200', 4200.00, 4200.00, '4100', '4000', 'startimes-super', NULL, 3, 3, '30', 'active', '2025-12-05 05:07:08', '2025-12-16 17:10:19', '15', 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel-cache-api_performance_metrics', 'a:6:{s:19:\"service_performance\";a:0:{}s:13:\"recent_errors\";a:0:{}s:13:\"hourly_trends\";a:0:{}s:20:\"overall_health_score\";i:95;s:16:\"slowest_services\";a:0:{}s:16:\"fastest_services\";a:0:{}}', 1765901499),
('laravel-cache-config.alphaApi', 's:0:\"\";', 1765903884),
('laravel-cache-config.alphaProvider', 's:0:\"\";', 1765903884),
('laravel-cache-config.cableApi', 's:40:\"245141f6de9c0aa211b3a6baf1d1533c642caf24\";', 1765903884),
('laravel-cache-config.cableProvider', 's:35:\"https://uzobestgsm.com/api/cabletv/\";', 1765903884),
('laravel-cache-config.cableVerificationApi', 's:40:\"245141f6de9c0aa211b3a6baf1d1533c642caf24\";', 1765903884),
('laravel-cache-config.cableVerificationProvider', 's:45:\"https://uzobestgsm.com/api/validate-customer/\";', 1765903884),
('laravel-cache-config.dataPinApi', 's:0:\"\";', 1765903884),
('laravel-cache-config.dataPinProvider', 's:0:\"\";', 1765903884),
('laravel-cache-config.examApi', 's:40:\"245141f6de9c0aa211b3a6baf1d1533c642caf24\";', 1765903884),
('laravel-cache-config.logging', 'N;', 1765914568),
('laravel-cache-config.meterApi', 's:40:\"245141f6de9c0aa211b3a6baf1d1533c642caf24\";', 1765903884),
('laravel-cache-config.meterProvider', 's:39:\"https://uzobestgsm.com/api/electricity/\";', 1765903884),
('laravel-cache-config.meterVerificationApi', 's:40:\"245141f6de9c0aa211b3a6baf1d1533c642caf24\";', 1765903884),
('laravel-cache-config.meterVerificationProvider', 's:45:\"https://uzobestgsm.com/api/validate-customer/\";', 1765903884),
('laravel-cache-config.rechargePinApi', 's:0:\"\";', 1765903884),
('laravel-cache-config.rechargePinProvider', 's:0:\"\";', 1765903884),
('laravel-cache-dashboard_realtime_metrics', 'a:8:{s:12:\"transactions\";a:8:{s:11:\"today_total\";i:0;s:16:\"today_successful\";i:0;s:12:\"today_failed\";i:0;s:13:\"today_pending\";i:0;s:9:\"this_hour\";i:0;s:8:\"last_24h\";i:0;s:18:\"success_rate_today\";i:100;s:27:\"pending_requiring_attention\";i:0;}s:5:\"users\";a:8:{s:11:\"total_users\";i:1;s:12:\"active_users\";i:1;s:19:\"today_registrations\";i:0;s:18:\"week_registrations\";i:0;s:19:\"month_registrations\";i:1;s:14:\"verified_users\";i:0;s:11:\"kyc_pending\";i:0;s:15:\"active_sessions\";i:0;}s:7:\"revenue\";a:8:{s:13:\"today_revenue\";i:0;s:17:\"yesterday_revenue\";i:0;s:13:\"month_revenue\";i:0;s:18:\"last_month_revenue\";i:0;s:12:\"daily_growth\";i:0;s:14:\"monthly_growth\";i:0;s:16:\"today_commission\";i:0;s:25:\"average_transaction_value\";i:0;}s:8:\"services\";a:4:{s:18:\"top_services_today\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}s:20:\"service_availability\";a:5:{s:7:\"airtime\";s:6:\"online\";s:4:\"data\";s:6:\"online\";s:8:\"cable_tv\";s:6:\"online\";s:11:\"electricity\";s:6:\"online\";s:9:\"exam_pins\";s:6:\"online\";}s:18:\"api_response_times\";a:0:{}s:14:\"service_errors\";O:29:\"Illuminate\\Support\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}s:6:\"system\";a:9:{s:13:\"server_status\";s:6:\"online\";s:20:\"database_connections\";i:7;s:12:\"memory_usage\";a:3:{s:7:\"current\";s:5:\"24 MB\";s:4:\"peak\";s:5:\"24 MB\";s:5:\"limit\";s:4:\"512M\";}s:10:\"disk_usage\";a:2:{s:15:\"used_percentage\";d:66.26;s:10:\"free_space\";s:8:\"80.25 GB\";}s:10:\"api_health\";a:3:{s:14:\"overall_status\";s:7:\"healthy\";s:15:\"services_online\";i:5;s:14:\"services_total\";i:5;}s:13:\"recent_errors\";i:0;s:6:\"uptime\";s:5:\"99.9%\";s:16:\"monitoring_stats\";a:5:{s:12:\"generated_at\";s:27:\"2025-12-16T16:06:39.223411Z\";s:14:\"overall_status\";s:8:\"degraded\";s:15:\"services_status\";a:8:{s:7:\"airtime\";a:8:{s:7:\"service\";s:7:\"airtime\";s:7:\"healthy\";b:0;s:6:\"status\";s:4:\"down\";s:7:\"message\";s:22:\"Service not configured\";s:13:\"response_time\";N;s:10:\"error_code\";s:12:\"CONFIG_ERROR\";s:7:\"details\";N;s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.228295Z\";}s:4:\"data\";a:8:{s:7:\"service\";s:4:\"data\";s:7:\"healthy\";b:0;s:6:\"status\";s:4:\"down\";s:7:\"message\";s:22:\"Service not configured\";s:13:\"response_time\";N;s:10:\"error_code\";s:12:\"CONFIG_ERROR\";s:7:\"details\";N;s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.232870Z\";}s:5:\"cable\";a:8:{s:7:\"service\";s:5:\"cable\";s:7:\"healthy\";b:1;s:6:\"status\";s:11:\"operational\";s:7:\"message\";s:35:\"Service operational (cached status)\";s:13:\"response_time\";d:0.14;s:10:\"error_code\";N;s:7:\"details\";a:3:{s:6:\"status\";s:11:\"operational\";s:8:\"provider\";s:7:\"Uzobest\";s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.236713Z\";}s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.236849Z\";}s:11:\"electricity\";a:8:{s:7:\"service\";s:11:\"electricity\";s:7:\"healthy\";b:1;s:6:\"status\";s:11:\"operational\";s:7:\"message\";s:35:\"Service operational (cached status)\";s:13:\"response_time\";d:0.2;s:10:\"error_code\";N;s:7:\"details\";a:3:{s:6:\"status\";s:11:\"operational\";s:8:\"provider\";s:7:\"Uzobest\";s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.242100Z\";}s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.242288Z\";}s:4:\"exam\";a:8:{s:7:\"service\";s:4:\"exam\";s:7:\"healthy\";b:1;s:6:\"status\";s:11:\"operational\";s:7:\"message\";s:35:\"Service operational (cached status)\";s:13:\"response_time\";d:0.18;s:10:\"error_code\";N;s:7:\"details\";a:3:{s:6:\"status\";s:11:\"operational\";s:8:\"provider\";s:9:\"Topupmate\";s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.247688Z\";}s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.247871Z\";}s:12:\"recharge_pin\";a:8:{s:7:\"service\";s:12:\"recharge_pin\";s:7:\"healthy\";b:1;s:6:\"status\";s:11:\"operational\";s:7:\"message\";s:35:\"Service operational (cached status)\";s:13:\"response_time\";d:0.21;s:10:\"error_code\";N;s:7:\"details\";a:3:{s:6:\"status\";s:11:\"operational\";s:8:\"provider\";s:7:\"uzobest\";s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.252358Z\";}s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.252559Z\";}s:8:\"data_pin\";a:8:{s:7:\"service\";s:8:\"data_pin\";s:7:\"healthy\";b:1;s:6:\"status\";s:11:\"operational\";s:7:\"message\";s:35:\"Service operational (cached status)\";s:13:\"response_time\";d:0.12;s:10:\"error_code\";N;s:7:\"details\";a:3:{s:6:\"status\";s:11:\"operational\";s:8:\"provider\";s:7:\"uzobest\";s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.256937Z\";}s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.257053Z\";}s:11:\"alpha_topup\";a:8:{s:7:\"service\";s:11:\"alpha_topup\";s:7:\"healthy\";b:1;s:6:\"status\";s:11:\"operational\";s:7:\"message\";s:35:\"Service operational (cached status)\";s:13:\"response_time\";d:0.19;s:10:\"error_code\";N;s:7:\"details\";a:3:{s:6:\"status\";s:11:\"operational\";s:8:\"provider\";s:7:\"uzobest\";s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.262270Z\";}s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.262465Z\";}}s:14:\"system_metrics\";a:0:{}s:6:\"alerts\";a:0:{}}s:20:\"service_availability\";a:8:{s:7:\"airtime\";a:8:{s:7:\"service\";s:7:\"airtime\";s:7:\"healthy\";b:0;s:6:\"status\";s:4:\"down\";s:7:\"message\";s:22:\"Service not configured\";s:13:\"response_time\";N;s:10:\"error_code\";s:12:\"CONFIG_ERROR\";s:7:\"details\";N;s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.228295Z\";}s:4:\"data\";a:8:{s:7:\"service\";s:4:\"data\";s:7:\"healthy\";b:0;s:6:\"status\";s:4:\"down\";s:7:\"message\";s:22:\"Service not configured\";s:13:\"response_time\";N;s:10:\"error_code\";s:12:\"CONFIG_ERROR\";s:7:\"details\";N;s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.232870Z\";}s:5:\"cable\";a:8:{s:7:\"service\";s:5:\"cable\";s:7:\"healthy\";b:1;s:6:\"status\";s:11:\"operational\";s:7:\"message\";s:35:\"Service operational (cached status)\";s:13:\"response_time\";d:0.14;s:10:\"error_code\";N;s:7:\"details\";a:3:{s:6:\"status\";s:11:\"operational\";s:8:\"provider\";s:7:\"Uzobest\";s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.236713Z\";}s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.236849Z\";}s:11:\"electricity\";a:8:{s:7:\"service\";s:11:\"electricity\";s:7:\"healthy\";b:1;s:6:\"status\";s:11:\"operational\";s:7:\"message\";s:35:\"Service operational (cached status)\";s:13:\"response_time\";d:0.2;s:10:\"error_code\";N;s:7:\"details\";a:3:{s:6:\"status\";s:11:\"operational\";s:8:\"provider\";s:7:\"Uzobest\";s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.242100Z\";}s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.242288Z\";}s:4:\"exam\";a:8:{s:7:\"service\";s:4:\"exam\";s:7:\"healthy\";b:1;s:6:\"status\";s:11:\"operational\";s:7:\"message\";s:35:\"Service operational (cached status)\";s:13:\"response_time\";d:0.18;s:10:\"error_code\";N;s:7:\"details\";a:3:{s:6:\"status\";s:11:\"operational\";s:8:\"provider\";s:9:\"Topupmate\";s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.247688Z\";}s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.247871Z\";}s:12:\"recharge_pin\";a:8:{s:7:\"service\";s:12:\"recharge_pin\";s:7:\"healthy\";b:1;s:6:\"status\";s:11:\"operational\";s:7:\"message\";s:35:\"Service operational (cached status)\";s:13:\"response_time\";d:0.21;s:10:\"error_code\";N;s:7:\"details\";a:3:{s:6:\"status\";s:11:\"operational\";s:8:\"provider\";s:7:\"uzobest\";s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.252358Z\";}s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.252559Z\";}s:8:\"data_pin\";a:8:{s:7:\"service\";s:8:\"data_pin\";s:7:\"healthy\";b:1;s:6:\"status\";s:11:\"operational\";s:7:\"message\";s:35:\"Service operational (cached status)\";s:13:\"response_time\";d:0.12;s:10:\"error_code\";N;s:7:\"details\";a:3:{s:6:\"status\";s:11:\"operational\";s:8:\"provider\";s:7:\"uzobest\";s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.256937Z\";}s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.257053Z\";}s:11:\"alpha_topup\";a:8:{s:7:\"service\";s:11:\"alpha_topup\";s:7:\"healthy\";b:1;s:6:\"status\";s:11:\"operational\";s:7:\"message\";s:35:\"Service operational (cached status)\";s:13:\"response_time\";d:0.19;s:10:\"error_code\";N;s:7:\"details\";a:3:{s:6:\"status\";s:11:\"operational\";s:8:\"provider\";s:7:\"uzobest\";s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.262270Z\";}s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.262465Z\";}}}s:9:\"providers\";a:4:{s:9:\"providers\";a:1:{s:7:\"uzobest\";a:6:{s:6:\"status\";s:11:\"operational\";s:13:\"response_time\";i:200;s:12:\"success_rate\";d:99.5;s:10:\"last_check\";O:25:\"Illuminate\\Support\\Carbon\":3:{s:4:\"date\";s:26:\"2025-12-16 16:06:40.292620\";s:13:\"timezone_type\";i:3;s:8:\"timezone\";s:3:\"UTC\";}s:15:\"is_circuit_open\";b:0;s:18:\"available_services\";a:4:{i:0;s:7:\"airtime\";i:1;s:4:\"data\";i:2;s:5:\"cable\";i:3;s:11:\"electricity\";}}}s:14:\"overall_health\";s:4:\"good\";s:16:\"active_failovers\";a:0:{}s:22:\"circuit_breaker_status\";s:10:\"all_closed\";}s:15:\"api_performance\";a:6:{s:19:\"service_performance\";a:0:{}s:13:\"recent_errors\";a:0:{}s:13:\"hourly_trends\";a:0:{}s:20:\"overall_health_score\";i:95;s:16:\"slowest_services\";a:0:{}s:16:\"fastest_services\";a:0:{}}s:8:\"security\";a:6:{s:15:\"security_events\";a:0:{}s:19:\"suspicious_activity\";a:0:{}s:20:\"authentication_stats\";a:0:{}s:17:\"failed_logins_24h\";i:0;s:22:\"unique_login_users_24h\";i:0;s:14:\"security_score\";i:95;}}', 1765901260),
('laravel-cache-dashboard_realtime_metrics_no_providers', 'a:7:{s:12:\"transactions\";a:8:{s:11:\"today_total\";i:0;s:16:\"today_successful\";i:0;s:12:\"today_failed\";i:0;s:13:\"today_pending\";i:0;s:9:\"this_hour\";i:0;s:8:\"last_24h\";i:0;s:18:\"success_rate_today\";i:100;s:27:\"pending_requiring_attention\";i:0;}s:5:\"users\";a:8:{s:11:\"total_users\";i:1;s:12:\"active_users\";i:1;s:19:\"today_registrations\";i:0;s:18:\"week_registrations\";i:0;s:19:\"month_registrations\";i:1;s:14:\"verified_users\";i:0;s:11:\"kyc_pending\";i:0;s:15:\"active_sessions\";i:0;}s:7:\"revenue\";a:8:{s:13:\"today_revenue\";i:0;s:17:\"yesterday_revenue\";i:0;s:13:\"month_revenue\";i:0;s:18:\"last_month_revenue\";i:0;s:12:\"daily_growth\";i:0;s:14:\"monthly_growth\";i:0;s:16:\"today_commission\";i:0;s:25:\"average_transaction_value\";i:0;}s:8:\"services\";a:4:{s:18:\"top_services_today\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}s:20:\"service_availability\";a:5:{s:7:\"airtime\";s:6:\"online\";s:4:\"data\";s:6:\"online\";s:8:\"cable_tv\";s:6:\"online\";s:11:\"electricity\";s:6:\"online\";s:9:\"exam_pins\";s:6:\"online\";}s:18:\"api_response_times\";a:0:{}s:14:\"service_errors\";O:29:\"Illuminate\\Support\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}s:6:\"system\";a:9:{s:13:\"server_status\";s:6:\"online\";s:20:\"database_connections\";i:7;s:12:\"memory_usage\";a:3:{s:7:\"current\";s:5:\"24 MB\";s:4:\"peak\";s:5:\"24 MB\";s:5:\"limit\";s:4:\"512M\";}s:10:\"disk_usage\";a:2:{s:15:\"used_percentage\";d:66.26;s:10:\"free_space\";s:8:\"80.25 GB\";}s:10:\"api_health\";a:3:{s:14:\"overall_status\";s:7:\"healthy\";s:15:\"services_online\";i:5;s:14:\"services_total\";i:5;}s:13:\"recent_errors\";i:0;s:6:\"uptime\";s:5:\"99.9%\";s:16:\"monitoring_stats\";a:5:{s:12:\"generated_at\";s:27:\"2025-12-16T16:06:39.223411Z\";s:14:\"overall_status\";s:8:\"degraded\";s:15:\"services_status\";a:8:{s:7:\"airtime\";a:8:{s:7:\"service\";s:7:\"airtime\";s:7:\"healthy\";b:0;s:6:\"status\";s:4:\"down\";s:7:\"message\";s:22:\"Service not configured\";s:13:\"response_time\";N;s:10:\"error_code\";s:12:\"CONFIG_ERROR\";s:7:\"details\";N;s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.228295Z\";}s:4:\"data\";a:8:{s:7:\"service\";s:4:\"data\";s:7:\"healthy\";b:0;s:6:\"status\";s:4:\"down\";s:7:\"message\";s:22:\"Service not configured\";s:13:\"response_time\";N;s:10:\"error_code\";s:12:\"CONFIG_ERROR\";s:7:\"details\";N;s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.232870Z\";}s:5:\"cable\";a:8:{s:7:\"service\";s:5:\"cable\";s:7:\"healthy\";b:1;s:6:\"status\";s:11:\"operational\";s:7:\"message\";s:35:\"Service operational (cached status)\";s:13:\"response_time\";d:0.14;s:10:\"error_code\";N;s:7:\"details\";a:3:{s:6:\"status\";s:11:\"operational\";s:8:\"provider\";s:7:\"Uzobest\";s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.236713Z\";}s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.236849Z\";}s:11:\"electricity\";a:8:{s:7:\"service\";s:11:\"electricity\";s:7:\"healthy\";b:1;s:6:\"status\";s:11:\"operational\";s:7:\"message\";s:35:\"Service operational (cached status)\";s:13:\"response_time\";d:0.2;s:10:\"error_code\";N;s:7:\"details\";a:3:{s:6:\"status\";s:11:\"operational\";s:8:\"provider\";s:7:\"Uzobest\";s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.242100Z\";}s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.242288Z\";}s:4:\"exam\";a:8:{s:7:\"service\";s:4:\"exam\";s:7:\"healthy\";b:1;s:6:\"status\";s:11:\"operational\";s:7:\"message\";s:35:\"Service operational (cached status)\";s:13:\"response_time\";d:0.18;s:10:\"error_code\";N;s:7:\"details\";a:3:{s:6:\"status\";s:11:\"operational\";s:8:\"provider\";s:9:\"Topupmate\";s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.247688Z\";}s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.247871Z\";}s:12:\"recharge_pin\";a:8:{s:7:\"service\";s:12:\"recharge_pin\";s:7:\"healthy\";b:1;s:6:\"status\";s:11:\"operational\";s:7:\"message\";s:35:\"Service operational (cached status)\";s:13:\"response_time\";d:0.21;s:10:\"error_code\";N;s:7:\"details\";a:3:{s:6:\"status\";s:11:\"operational\";s:8:\"provider\";s:7:\"uzobest\";s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.252358Z\";}s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.252559Z\";}s:8:\"data_pin\";a:8:{s:7:\"service\";s:8:\"data_pin\";s:7:\"healthy\";b:1;s:6:\"status\";s:11:\"operational\";s:7:\"message\";s:35:\"Service operational (cached status)\";s:13:\"response_time\";d:0.12;s:10:\"error_code\";N;s:7:\"details\";a:3:{s:6:\"status\";s:11:\"operational\";s:8:\"provider\";s:7:\"uzobest\";s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.256937Z\";}s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.257053Z\";}s:11:\"alpha_topup\";a:8:{s:7:\"service\";s:11:\"alpha_topup\";s:7:\"healthy\";b:1;s:6:\"status\";s:11:\"operational\";s:7:\"message\";s:35:\"Service operational (cached status)\";s:13:\"response_time\";d:0.19;s:10:\"error_code\";N;s:7:\"details\";a:3:{s:6:\"status\";s:11:\"operational\";s:8:\"provider\";s:7:\"uzobest\";s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.262270Z\";}s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.262465Z\";}}s:14:\"system_metrics\";a:0:{}s:6:\"alerts\";a:0:{}}s:20:\"service_availability\";a:8:{s:7:\"airtime\";a:8:{s:7:\"service\";s:7:\"airtime\";s:7:\"healthy\";b:0;s:6:\"status\";s:4:\"down\";s:7:\"message\";s:22:\"Service not configured\";s:13:\"response_time\";N;s:10:\"error_code\";s:12:\"CONFIG_ERROR\";s:7:\"details\";N;s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.228295Z\";}s:4:\"data\";a:8:{s:7:\"service\";s:4:\"data\";s:7:\"healthy\";b:0;s:6:\"status\";s:4:\"down\";s:7:\"message\";s:22:\"Service not configured\";s:13:\"response_time\";N;s:10:\"error_code\";s:12:\"CONFIG_ERROR\";s:7:\"details\";N;s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.232870Z\";}s:5:\"cable\";a:8:{s:7:\"service\";s:5:\"cable\";s:7:\"healthy\";b:1;s:6:\"status\";s:11:\"operational\";s:7:\"message\";s:35:\"Service operational (cached status)\";s:13:\"response_time\";d:0.14;s:10:\"error_code\";N;s:7:\"details\";a:3:{s:6:\"status\";s:11:\"operational\";s:8:\"provider\";s:7:\"Uzobest\";s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.236713Z\";}s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.236849Z\";}s:11:\"electricity\";a:8:{s:7:\"service\";s:11:\"electricity\";s:7:\"healthy\";b:1;s:6:\"status\";s:11:\"operational\";s:7:\"message\";s:35:\"Service operational (cached status)\";s:13:\"response_time\";d:0.2;s:10:\"error_code\";N;s:7:\"details\";a:3:{s:6:\"status\";s:11:\"operational\";s:8:\"provider\";s:7:\"Uzobest\";s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.242100Z\";}s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.242288Z\";}s:4:\"exam\";a:8:{s:7:\"service\";s:4:\"exam\";s:7:\"healthy\";b:1;s:6:\"status\";s:11:\"operational\";s:7:\"message\";s:35:\"Service operational (cached status)\";s:13:\"response_time\";d:0.18;s:10:\"error_code\";N;s:7:\"details\";a:3:{s:6:\"status\";s:11:\"operational\";s:8:\"provider\";s:9:\"Topupmate\";s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.247688Z\";}s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.247871Z\";}s:12:\"recharge_pin\";a:8:{s:7:\"service\";s:12:\"recharge_pin\";s:7:\"healthy\";b:1;s:6:\"status\";s:11:\"operational\";s:7:\"message\";s:35:\"Service operational (cached status)\";s:13:\"response_time\";d:0.21;s:10:\"error_code\";N;s:7:\"details\";a:3:{s:6:\"status\";s:11:\"operational\";s:8:\"provider\";s:7:\"uzobest\";s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.252358Z\";}s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.252559Z\";}s:8:\"data_pin\";a:8:{s:7:\"service\";s:8:\"data_pin\";s:7:\"healthy\";b:1;s:6:\"status\";s:11:\"operational\";s:7:\"message\";s:35:\"Service operational (cached status)\";s:13:\"response_time\";d:0.12;s:10:\"error_code\";N;s:7:\"details\";a:3:{s:6:\"status\";s:11:\"operational\";s:8:\"provider\";s:7:\"uzobest\";s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.256937Z\";}s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.257053Z\";}s:11:\"alpha_topup\";a:8:{s:7:\"service\";s:11:\"alpha_topup\";s:7:\"healthy\";b:1;s:6:\"status\";s:11:\"operational\";s:7:\"message\";s:35:\"Service operational (cached status)\";s:13:\"response_time\";d:0.19;s:10:\"error_code\";N;s:7:\"details\";a:3:{s:6:\"status\";s:11:\"operational\";s:8:\"provider\";s:7:\"uzobest\";s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.262270Z\";}s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.262465Z\";}}}s:15:\"api_performance\";a:6:{s:19:\"service_performance\";a:0:{}s:13:\"recent_errors\";a:0:{}s:13:\"hourly_trends\";a:0:{}s:20:\"overall_health_score\";i:95;s:16:\"slowest_services\";a:0:{}s:16:\"fastest_services\";a:0:{}}s:8:\"security\";a:6:{s:15:\"security_events\";a:0:{}s:19:\"suspicious_activity\";a:0:{}s:20:\"authentication_stats\";a:0:{}s:17:\"failed_logins_24h\";i:0;s:22:\"unique_login_users_24h\";i:0;s:14:\"security_score\";i:95;}}', 1765901259),
('laravel-cache-error_pattern_ErrorException:0', 'i:1;', 1765902614),
('laravel-cache-health_check_airtime', 'a:8:{s:7:\"service\";s:7:\"airtime\";s:7:\"healthy\";b:0;s:6:\"status\";s:4:\"down\";s:7:\"message\";s:22:\"Service not configured\";s:13:\"response_time\";N;s:10:\"error_code\";s:12:\"CONFIG_ERROR\";s:7:\"details\";N;s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.228295Z\";}', 1765901204),
('laravel-cache-health_check_alpha_topup', 'a:8:{s:7:\"service\";s:11:\"alpha_topup\";s:7:\"healthy\";b:1;s:6:\"status\";s:11:\"operational\";s:7:\"message\";s:35:\"Service operational (cached status)\";s:13:\"response_time\";d:0.19;s:10:\"error_code\";N;s:7:\"details\";a:3:{s:6:\"status\";s:11:\"operational\";s:8:\"provider\";s:7:\"uzobest\";s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.262270Z\";}s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.262465Z\";}', 1765901204),
('laravel-cache-health_check_cable', 'a:8:{s:7:\"service\";s:5:\"cable\";s:7:\"healthy\";b:1;s:6:\"status\";s:11:\"operational\";s:7:\"message\";s:35:\"Service operational (cached status)\";s:13:\"response_time\";d:0.14;s:10:\"error_code\";N;s:7:\"details\";a:3:{s:6:\"status\";s:11:\"operational\";s:8:\"provider\";s:7:\"Uzobest\";s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.236713Z\";}s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.236849Z\";}', 1765901204),
('laravel-cache-health_check_data', 'a:8:{s:7:\"service\";s:4:\"data\";s:7:\"healthy\";b:0;s:6:\"status\";s:4:\"down\";s:7:\"message\";s:22:\"Service not configured\";s:13:\"response_time\";N;s:10:\"error_code\";s:12:\"CONFIG_ERROR\";s:7:\"details\";N;s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.232870Z\";}', 1765901204),
('laravel-cache-health_check_data_pin', 'a:8:{s:7:\"service\";s:8:\"data_pin\";s:7:\"healthy\";b:1;s:6:\"status\";s:11:\"operational\";s:7:\"message\";s:35:\"Service operational (cached status)\";s:13:\"response_time\";d:0.12;s:10:\"error_code\";N;s:7:\"details\";a:3:{s:6:\"status\";s:11:\"operational\";s:8:\"provider\";s:7:\"uzobest\";s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.256937Z\";}s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.257053Z\";}', 1765901204),
('laravel-cache-health_check_electricity', 'a:8:{s:7:\"service\";s:11:\"electricity\";s:7:\"healthy\";b:1;s:6:\"status\";s:11:\"operational\";s:7:\"message\";s:35:\"Service operational (cached status)\";s:13:\"response_time\";d:0.2;s:10:\"error_code\";N;s:7:\"details\";a:3:{s:6:\"status\";s:11:\"operational\";s:8:\"provider\";s:7:\"Uzobest\";s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.242100Z\";}s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.242288Z\";}', 1765901204),
('laravel-cache-health_check_exam', 'a:8:{s:7:\"service\";s:4:\"exam\";s:7:\"healthy\";b:1;s:6:\"status\";s:11:\"operational\";s:7:\"message\";s:35:\"Service operational (cached status)\";s:13:\"response_time\";d:0.18;s:10:\"error_code\";N;s:7:\"details\";a:3:{s:6:\"status\";s:11:\"operational\";s:8:\"provider\";s:9:\"Topupmate\";s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.247688Z\";}s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.247871Z\";}', 1765901204),
('laravel-cache-health_check_monnify', 'a:8:{s:7:\"service\";s:7:\"monnify\";s:7:\"healthy\";b:0;s:6:\"status\";s:4:\"down\";s:7:\"message\";s:26:\"Credentials not configured\";s:13:\"response_time\";N;s:10:\"error_code\";s:12:\"CONFIG_ERROR\";s:7:\"details\";N;s:10:\"checked_at\";s:27:\"2025-12-16T16:06:42.459754Z\";}', 1765901207),
('laravel-cache-health_check_paystack', 'a:8:{s:7:\"service\";s:8:\"paystack\";s:7:\"healthy\";b:0;s:6:\"status\";s:4:\"down\";s:7:\"message\";s:25:\"Secret key not configured\";s:13:\"response_time\";N;s:10:\"error_code\";s:12:\"CONFIG_ERROR\";s:7:\"details\";N;s:10:\"checked_at\";s:27:\"2025-12-16T16:06:42.455281Z\";}', 1765901207),
('laravel-cache-health_check_recharge_pin', 'a:8:{s:7:\"service\";s:12:\"recharge_pin\";s:7:\"healthy\";b:1;s:6:\"status\";s:11:\"operational\";s:7:\"message\";s:35:\"Service operational (cached status)\";s:13:\"response_time\";d:0.21;s:10:\"error_code\";N;s:7:\"details\";a:3:{s:6:\"status\";s:11:\"operational\";s:8:\"provider\";s:7:\"uzobest\";s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.252358Z\";}s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.252559Z\";}', 1765901204),
('laravel-cache-health_check_uzobest_vtu', 'a:8:{s:7:\"service\";s:7:\"uzobest\";s:7:\"healthy\";b:0;s:6:\"status\";s:4:\"down\";s:7:\"message\";s:31:\"Uzobest API returned error: 404\";s:13:\"response_time\";d:1649.82;s:10:\"error_code\";s:9:\"API_ERROR\";s:7:\"details\";N;s:10:\"checked_at\";s:27:\"2025-12-16T16:06:42.449495Z\";}', 1765901207),
('laravel-cache-hourly_trends_2025-12-16', 'a:24:{i:0;a:5:{s:4:\"hour\";i:0;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:1;a:5:{s:4:\"hour\";i:1;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:2;a:5:{s:4:\"hour\";i:2;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:3;a:5:{s:4:\"hour\";i:3;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:4;a:5:{s:4:\"hour\";i:4;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:5;a:5:{s:4:\"hour\";i:5;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:6;a:5:{s:4:\"hour\";i:6;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:7;a:5:{s:4:\"hour\";i:7;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:8;a:5:{s:4:\"hour\";i:8;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:9;a:5:{s:4:\"hour\";i:9;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:10;a:5:{s:4:\"hour\";i:10;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:11;a:5:{s:4:\"hour\";i:11;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:12;a:5:{s:4:\"hour\";i:12;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:13;a:5:{s:4:\"hour\";i:13;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:14;a:5:{s:4:\"hour\";i:14;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:15;a:5:{s:4:\"hour\";i:15;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:16;a:5:{s:4:\"hour\";i:16;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:17;a:5:{s:4:\"hour\";i:17;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:18;a:5:{s:4:\"hour\";i:18;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:19;a:5:{s:4:\"hour\";i:19;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:20;a:5:{s:4:\"hour\";i:20;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:21;a:5:{s:4:\"hour\";i:21;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:22;a:5:{s:4:\"hour\";i:22;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:23;a:5:{s:4:\"hour\";i:23;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}}', 1765901499),
('laravel-cache-monitoring_dashboard', 'a:5:{s:12:\"generated_at\";s:27:\"2025-12-16T16:06:39.223411Z\";s:14:\"overall_status\";s:8:\"degraded\";s:15:\"services_status\";a:8:{s:7:\"airtime\";a:8:{s:7:\"service\";s:7:\"airtime\";s:7:\"healthy\";b:0;s:6:\"status\";s:4:\"down\";s:7:\"message\";s:22:\"Service not configured\";s:13:\"response_time\";N;s:10:\"error_code\";s:12:\"CONFIG_ERROR\";s:7:\"details\";N;s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.228295Z\";}s:4:\"data\";a:8:{s:7:\"service\";s:4:\"data\";s:7:\"healthy\";b:0;s:6:\"status\";s:4:\"down\";s:7:\"message\";s:22:\"Service not configured\";s:13:\"response_time\";N;s:10:\"error_code\";s:12:\"CONFIG_ERROR\";s:7:\"details\";N;s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.232870Z\";}s:5:\"cable\";a:8:{s:7:\"service\";s:5:\"cable\";s:7:\"healthy\";b:1;s:6:\"status\";s:11:\"operational\";s:7:\"message\";s:35:\"Service operational (cached status)\";s:13:\"response_time\";d:0.14;s:10:\"error_code\";N;s:7:\"details\";a:3:{s:6:\"status\";s:11:\"operational\";s:8:\"provider\";s:7:\"Uzobest\";s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.236713Z\";}s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.236849Z\";}s:11:\"electricity\";a:8:{s:7:\"service\";s:11:\"electricity\";s:7:\"healthy\";b:1;s:6:\"status\";s:11:\"operational\";s:7:\"message\";s:35:\"Service operational (cached status)\";s:13:\"response_time\";d:0.2;s:10:\"error_code\";N;s:7:\"details\";a:3:{s:6:\"status\";s:11:\"operational\";s:8:\"provider\";s:7:\"Uzobest\";s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.242100Z\";}s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.242288Z\";}s:4:\"exam\";a:8:{s:7:\"service\";s:4:\"exam\";s:7:\"healthy\";b:1;s:6:\"status\";s:11:\"operational\";s:7:\"message\";s:35:\"Service operational (cached status)\";s:13:\"response_time\";d:0.18;s:10:\"error_code\";N;s:7:\"details\";a:3:{s:6:\"status\";s:11:\"operational\";s:8:\"provider\";s:9:\"Topupmate\";s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.247688Z\";}s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.247871Z\";}s:12:\"recharge_pin\";a:8:{s:7:\"service\";s:12:\"recharge_pin\";s:7:\"healthy\";b:1;s:6:\"status\";s:11:\"operational\";s:7:\"message\";s:35:\"Service operational (cached status)\";s:13:\"response_time\";d:0.21;s:10:\"error_code\";N;s:7:\"details\";a:3:{s:6:\"status\";s:11:\"operational\";s:8:\"provider\";s:7:\"uzobest\";s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.252358Z\";}s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.252559Z\";}s:8:\"data_pin\";a:8:{s:7:\"service\";s:8:\"data_pin\";s:7:\"healthy\";b:1;s:6:\"status\";s:11:\"operational\";s:7:\"message\";s:35:\"Service operational (cached status)\";s:13:\"response_time\";d:0.12;s:10:\"error_code\";N;s:7:\"details\";a:3:{s:6:\"status\";s:11:\"operational\";s:8:\"provider\";s:7:\"uzobest\";s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.256937Z\";}s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.257053Z\";}s:11:\"alpha_topup\";a:8:{s:7:\"service\";s:11:\"alpha_topup\";s:7:\"healthy\";b:1;s:6:\"status\";s:11:\"operational\";s:7:\"message\";s:35:\"Service operational (cached status)\";s:13:\"response_time\";d:0.19;s:10:\"error_code\";N;s:7:\"details\";a:3:{s:6:\"status\";s:11:\"operational\";s:8:\"provider\";s:7:\"uzobest\";s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.262270Z\";}s:10:\"checked_at\";s:27:\"2025-12-16T16:06:39.262465Z\";}}s:14:\"system_metrics\";a:0:{}s:6:\"alerts\";a:0:{}}', 1765901209),
('laravel-cache-provider_health_metrics', 'a:4:{s:9:\"providers\";a:1:{s:7:\"uzobest\";a:6:{s:6:\"status\";s:11:\"operational\";s:13:\"response_time\";i:200;s:12:\"success_rate\";d:99.5;s:10:\"last_check\";O:25:\"Illuminate\\Support\\Carbon\":3:{s:4:\"date\";s:26:\"2025-12-16 16:06:40.292620\";s:13:\"timezone_type\";i:3;s:8:\"timezone\";s:3:\"UTC\";}s:15:\"is_circuit_open\";b:0;s:18:\"available_services\";a:4:{i:0;s:7:\"airtime\";i:1;s:4:\"data\";i:2;s:5:\"cable\";i:3;s:11:\"electricity\";}}}s:14:\"overall_health\";s:4:\"good\";s:16:\"active_failovers\";a:0:{}s:22:\"circuit_breaker_status\";s:10:\"all_closed\";}', 1765901500),
('laravel-cache-security_metrics', 'a:6:{s:15:\"security_events\";a:0:{}s:19:\"suspicious_activity\";a:0:{}s:20:\"authentication_stats\";a:0:{}s:17:\"failed_logins_24h\";i:0;s:22:\"unique_login_users_24h\";i:0;s:14:\"security_score\";i:95;}', 1765901499),
('laravel-cache-service.airtime', 'a:0:{}', 1765903884),
('laravel-cache-service.alpha_topup', 'a:2:{s:7:\"api_key\";s:0:\"\";s:8:\"provider\";s:0:\"\";}', 1765903884),
('laravel-cache-service.cable', 'a:7:{s:16:\"verification_api\";s:40:\"245141f6de9c0aa211b3a6baf1d1533c642caf24\";s:21:\"verification_provider\";s:45:\"https://uzobestgsm.com/api/validate-customer/\";s:7:\"api_key\";s:40:\"245141f6de9c0aa211b3a6baf1d1533c642caf24\";s:8:\"provider\";s:35:\"https://uzobestgsm.com/api/cabletv/\";s:13:\"provider_name\";s:7:\"Uzobest\";s:9:\"auth_type\";s:6:\"header\";s:11:\"auth_params\";a:2:{s:11:\"header_name\";s:13:\"Authorization\";s:13:\"header_prefix\";s:6:\"Token \";}}', 1765903884),
('laravel-cache-service.data', 'a:0:{}', 1765903884),
('laravel-cache-service.data_pin', 'a:3:{s:7:\"api_key\";s:0:\"\";s:8:\"provider\";s:0:\"\";s:9:\"auth_type\";s:5:\"Basic\";}', 1765903884),
('laravel-cache-service.electricity', 'a:7:{s:16:\"verification_api\";s:40:\"245141f6de9c0aa211b3a6baf1d1533c642caf24\";s:21:\"verification_provider\";s:45:\"https://uzobestgsm.com/api/validate-customer/\";s:7:\"api_key\";s:40:\"245141f6de9c0aa211b3a6baf1d1533c642caf24\";s:8:\"provider\";s:39:\"https://uzobestgsm.com/api/electricity/\";s:13:\"provider_name\";s:7:\"Uzobest\";s:9:\"auth_type\";s:6:\"header\";s:11:\"auth_params\";a:2:{s:11:\"header_name\";s:13:\"Authorization\";s:13:\"header_prefix\";s:6:\"Token \";}}', 1765903884),
('laravel-cache-service.exam', 'a:6:{s:7:\"api_key\";s:40:\"245141f6de9c0aa211b3a6baf1d1533c642caf24\";s:8:\"provider\";s:31:\"https://topupmate.com/api/exam/\";s:9:\"auth_type\";s:5:\"token\";s:8:\"user_url\";N;s:13:\"provider_name\";s:9:\"Topupmate\";s:11:\"auth_params\";N;}', 1765903884),
('laravel-cache-service.recharge_pin', 'a:3:{s:7:\"api_key\";s:0:\"\";s:8:\"provider\";s:0:\"\";s:9:\"auth_type\";s:5:\"Basic\";}', 1765903884);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `configurations`
--

CREATE TABLE `configurations` (
  `cId` bigint(20) UNSIGNED NOT NULL,
  `config_key` varchar(255) NOT NULL,
  `config_value` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `configurations`
--

INSERT INTO `configurations` (`cId`, `config_key`, `config_value`) VALUES
(1, 'monifyCharges', '1.075'),
(2, 'monifyApi', 'MK_PROD_JAJ'),
(3, 'monifySecrete', '4YBNAZ8XY1'),
(4, 'monifyContract', '5812563'),
(5, 'monifyWeStatus', 'On'),
(6, 'monifyMoStatus', 'On'),
(7, 'monifyFeStatus', 'Off'),
(8, 'monifySaStatus', 'On'),
(9, 'monifyStatus', 'On'),
(10, 'paystackCharges', '1.5'),
(11, 'paystackApi', ''),
(12, 'paystackStatus', 'Off'),
(13, 'walletOneProviderName', 'Maskawasub'),
(14, 'walletOneApi', 'e5199989c9df406e8f78f9b255ab5620e131e2b4'),
(15, 'walletOneProvider', 'https://maskawasub.com/api/user/'),
(16, 'walletTwoProviderName', 'Topupmate'),
(17, 'walletTwoApi', ''),
(18, 'walletTwoProvider', 'https://topupmate.com/api/user/'),
(19, 'walletThreeProviderName', 'Aabaxztech'),
(20, 'walletThreeApi', ''),
(21, 'walletThreeProvider', 'https://aabaxztech.com/api/user/');

-- --------------------------------------------------------

--
-- Table structure for table `contact`
--

CREATE TABLE `contact` (
  `cId` bigint(20) UNSIGNED NOT NULL,
  `cName` varchar(255) NOT NULL,
  `cEmail` varchar(255) NOT NULL,
  `cSubject` varchar(255) NOT NULL,
  `cMessage` text NOT NULL,
  `cPhone` varchar(255) DEFAULT NULL,
  `cStatus` tinyint(4) NOT NULL DEFAULT 0,
  `dPosted` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `datapins`
--

CREATE TABLE `datapins` (
  `dId` bigint(20) UNSIGNED NOT NULL,
  `sId` int(11) NOT NULL,
  `network` varchar(10) NOT NULL,
  `dataPlan` text NOT NULL,
  `dataPin` text NOT NULL,
  `serialNumber` text NOT NULL,
  `quantity` int(11) NOT NULL,
  `transref` varchar(50) NOT NULL,
  `amount` int(11) NOT NULL,
  `buyprice` int(11) NOT NULL,
  `api` varchar(50) NOT NULL,
  `status` varchar(10) NOT NULL,
  `date` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `datatokens`
--

CREATE TABLE `datatokens` (
  `dId` bigint(20) UNSIGNED NOT NULL,
  `sId` int(11) NOT NULL,
  `network` varchar(10) NOT NULL,
  `planName` text NOT NULL,
  `serialNumber` text NOT NULL,
  `pin` text NOT NULL,
  `date` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `data_plans`
--

CREATE TABLE `data_plans` (
  `dId` bigint(20) UNSIGNED NOT NULL,
  `dPlanId` varchar(100) NOT NULL,
  `nId` int(11) NOT NULL,
  `uzobest_plan_id` int(11) DEFAULT NULL COMMENT 'Uzobest API plan ID',
  `dPlan` varchar(100) NOT NULL,
  `dAmount` varchar(50) NOT NULL,
  `dValidity` varchar(50) NOT NULL,
  `userPrice` int(11) NOT NULL,
  `cost_price` decimal(10,2) DEFAULT NULL COMMENT 'Cost from Uzobest API',
  `selling_price` decimal(10,2) DEFAULT NULL COMMENT 'Admin-configured selling price',
  `agentPrice` int(11) NOT NULL,
  `apiPrice` int(11) NOT NULL,
  `dGroup` varchar(50) NOT NULL DEFAULT 'SME',
  `profit_margin` decimal(10,2) DEFAULT NULL COMMENT 'Calculated: selling_price - cost_price',
  `agent_price` decimal(10,2) DEFAULT NULL,
  `vendor_price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `data_plans`
--

INSERT INTO `data_plans` (`dId`, `dPlanId`, `nId`, `uzobest_plan_id`, `dPlan`, `dAmount`, `dValidity`, `userPrice`, `cost_price`, `selling_price`, `agentPrice`, `apiPrice`, `dGroup`, `profit_margin`, `agent_price`, `vendor_price`) VALUES
(1, 'mtn-sme-500mb', 1, NULL, 'MTN 500MB', '500MB', '30 Days', 550, NULL, 550.00, 125, 120, 'SME', 50.00, NULL, NULL),
(2, 'mtn-sme-1gb', 1, NULL, 'MTN 1GB', '1GB', '30 Days', 270, NULL, NULL, 250, 240, 'SME', NULL, NULL, NULL),
(3, 'mtn-sme-2gb', 1, NULL, 'MTN 2GB', '2GB', '30 Days', 540, NULL, NULL, 500, 480, 'SME', NULL, NULL, NULL),
(4, 'mtn-sme-5gb', 1, NULL, 'MTN 5GB', '5GB', '30 Days', 1350, NULL, NULL, 1250, 1200, 'SME', NULL, NULL, NULL),
(5, 'mtn-sme-10gb', 1, NULL, 'MTN 10GB', '10GB', '30 Days', 2700, NULL, NULL, 2500, 2400, 'SME', NULL, NULL, NULL),
(6, 'mtn-gift-500mb', 1, NULL, 'MTN 500MB Gifting', '500MB', '30 Days', 145, NULL, NULL, 135, 130, 'Gifting', NULL, NULL, NULL),
(7, 'mtn-gift-1gb', 1, NULL, 'MTN 1GB Gifting', '1GB', '30 Days', 290, NULL, NULL, 270, 260, 'Gifting', NULL, NULL, NULL),
(8, 'mtn-gift-2gb', 1, NULL, 'MTN 2GB Gifting', '2GB', '30 Days', 580, NULL, NULL, 540, 520, 'Gifting', NULL, NULL, NULL),
(9, 'airtel-sme-500mb', 2, NULL, 'Airtel 500MB', '500MB', '30 Days', 140, NULL, NULL, 130, 125, 'SME', NULL, NULL, NULL),
(10, 'airtel-sme-1gb', 2, NULL, 'Airtel 1GB', '1GB', '30 Days', 280, NULL, NULL, 260, 250, 'SME', NULL, NULL, NULL),
(11, 'airtel-sme-2gb', 2, NULL, 'Airtel 2GB', '2GB', '30 Days', 560, NULL, NULL, 520, 500, 'SME', NULL, NULL, NULL),
(12, 'airtel-sme-5gb', 2, NULL, 'Airtel 5GB', '5GB', '30 Days', 1400, NULL, NULL, 1300, 1250, 'SME', NULL, NULL, NULL),
(13, 'glo-sme-500mb', 3, NULL, 'Glo 500MB', '500MB', '30 Days', 130, NULL, NULL, 120, 115, 'SME', NULL, NULL, NULL),
(14, 'glo-sme-1gb', 3, NULL, 'Glo 1GB', '1GB', '30 Days', 260, NULL, NULL, 240, 230, 'SME', NULL, NULL, NULL),
(15, 'glo-sme-2gb', 3, NULL, 'Glo 2GB', '2GB', '30 Days', 520, NULL, NULL, 480, 460, 'SME', NULL, NULL, NULL),
(16, 'glo-sme-5gb', 3, NULL, 'Glo 5GB', '5GB', '30 Days', 1300, NULL, NULL, 1200, 1150, 'SME', NULL, NULL, NULL),
(17, '9mobile-sme-500mb', 4, NULL, '9mobile 500MB', '500MB', '30 Days', 135, NULL, NULL, 125, 120, 'SME', NULL, NULL, NULL),
(18, '9mobile-sme-1gb', 4, NULL, '9mobile 1GB', '1GB', '30 Days', 270, NULL, NULL, 250, 240, 'SME', NULL, NULL, NULL),
(19, '9mobile-sme-2gb', 4, NULL, '9mobile 2GB', '2GB', '30 Days', 540, NULL, NULL, 500, 480, 'SME', NULL, NULL, NULL),
(20, '385', 1, 385, '75.0MB', '75.0MB', '1 Day', 79, 75.00, 78.75, 79, 79, 'GIFTING', 5.00, NULL, NULL),
(21, '463', 1, 463, '110.0MB', '110.0MB', '1 Day', 105, 100.00, 105.00, 105, 105, 'GIFTING', 5.00, NULL, NULL),
(22, '464', 1, 464, '230.0MB', '230.0MB', '1 Day', 210, 200.00, 210.00, 210, 210, 'GIFTING', 5.00, NULL, NULL),
(23, '592', 1, 592, '1.0GB', '1.0GB', 'Hot Deal - 1 Day', 257, 245.00, 257.25, 257, 257, 'AWOOF DATA', 5.00, NULL, NULL),
(24, '495', 1, 495, '500.0MB', '500.0MB', '1 Day', 360, 343.00, 360.15, 360, 360, 'GIFTING', 5.00, NULL, NULL),
(25, '558', 1, 558, '500.0MB', '500.0MB', '1 Week (DT)', 394, 375.00, 393.75, 394, 394, 'SME', 5.00, NULL, NULL),
(26, '579', 1, 579, '500.0MB', '500.0MB', '30 DAYS  (*461*4#)', 404, 385.00, 404.25, 404, 404, 'DATA SHARE', 5.00, NULL, NULL),
(27, '390', 1, 390, '750.0MB', '750.0MB', '3 Days', 462, 440.00, 462.00, 462, 462, 'GIFTING', 5.00, NULL, NULL),
(28, '458', 1, 458, '750.0MB', '750.0MB', '3 Days', 462, 440.00, 462.00, 462, 462, 'AWOOF DATA', 5.00, NULL, NULL),
(29, '486', 1, 486, '1.2GB', '1.2GB', '30 Days - Social Apps Only', 462, 440.00, 462.00, 462, 462, 'GIFTING', 5.00, NULL, NULL),
(30, '583', 1, 583, '1.0GB', '1.0GB', '1 Week (DT)', 499, 475.00, 498.75, 499, 499, 'SME', 5.00, NULL, NULL),
(31, '387', 1, 387, '1.0GB', '1.0GB', '1 Day', 509, 485.00, 509.25, 509, 509, 'GIFTING', 5.00, NULL, NULL),
(32, '461', 1, 461, '500.0MB', '500.0MB', '7 Days', 509, 485.00, 509.25, 509, 509, 'GIFTING', 5.00, NULL, NULL),
(33, '462', 1, 462, '500.0MB', '500.0MB', '1 Week', 509, 485.00, 509.25, 509, 509, 'AWOOF DATA', 5.00, NULL, NULL),
(34, '216', 1, 216, '500.0MB', '500.0MB', '1 Week', 509, 485.00, 509.25, 509, 509, 'SME', 5.00, NULL, NULL),
(35, '593', 1, 593, '2.5GB', '2.5GB', 'Hot Deal - 1 Day', 572, 545.00, 572.25, 572, 572, 'AWOOF DATA', 5.00, NULL, NULL),
(36, '491', 1, 491, '1.0GB', '1.0GB', '30 Days (DT)', 604, 575.00, 603.75, 604, 604, 'SME', 5.00, NULL, NULL),
(37, '451', 1, 451, '1.5GB', '1.5GB', '2 Days', 614, 585.00, 614.25, 614, 614, 'GIFTING', 5.00, NULL, NULL),
(38, '475', 1, 475, '1.5GB', '1.5GB', '2 Days', 614, 585.00, 614.25, 614, 614, 'AWOOF DATA', 5.00, NULL, NULL),
(39, '580', 1, 580, '1.0GB', '1.0GB', '30 DAYS  (*461*4#)', 735, 700.00, 735.00, 735, 735, 'DATA SHARE', 5.00, NULL, NULL),
(40, '442', 1, 442, '2.0GB', '2.0GB', '2 Days', 767, 730.00, 766.50, 767, 767, 'AWOOF DATA', 5.00, NULL, NULL),
(41, '595', 1, 595, '1.2GB', '1.2GB', '1 Week', 772, 735.00, 771.75, 772, 772, 'DATA SHARE', 5.00, NULL, NULL),
(42, '598', 1, 598, '1.2GB', '1.2GB', '7 Days', 772, 735.00, 771.75, 772, 772, 'DATA SHARE', 5.00, NULL, NULL),
(43, '388', 1, 388, '2.0GB', '2.0GB', '2 Days', 772, 735.00, 771.75, 772, 772, 'GIFTING', 5.00, NULL, NULL),
(44, '467', 1, 467, '2.5GB', '2.5GB', '1 Day', 772, 735.00, 771.75, 772, 772, 'GIFTING', 5.00, NULL, NULL),
(45, '456', 1, 456, '1.0GB', '1.0GB', '7 Days', 819, 780.00, 819.00, 819, 819, 'GIFTING', 5.00, NULL, NULL),
(46, '217', 1, 217, '1.0GB', '1.0GB', '1 Week', 819, 780.00, 819.00, 819, 819, 'SME', 5.00, NULL, NULL),
(47, '594', 1, 594, '2.0GB', '2.0GB', '1 Week (DT)', 893, 850.00, 892.50, 893, 893, 'SME', 5.00, NULL, NULL),
(48, '389', 1, 389, '2.5GB', '2.5GB', '2 Days', 924, 880.00, 924.00, 924, 924, 'GIFTING', 5.00, NULL, NULL),
(49, '395', 1, 395, '1.5GB', '1.5GB', '7 Days', 1024, 975.00, 1023.75, 1024, 1024, 'GIFTING', 5.00, NULL, NULL),
(50, '443', 1, 443, '3.2GB', '3.2GB', '2 Days', 1024, 975.00, 1023.75, 1024, 1024, 'AWOOF DATA', 5.00, NULL, NULL),
(51, '450', 1, 450, '1.5GB', '1.5GB', '1 Week', 1024, 975.00, 1023.75, 1024, 1024, 'AWOOF DATA', 5.00, NULL, NULL),
(52, '452', 1, 452, '3.2GB', '3.2GB', '2 Days', 1024, 975.00, 1023.75, 1024, 1024, 'GIFTING', 5.00, NULL, NULL),
(53, '492', 1, 492, '2.0GB', '2.0GB', '30 Days (DT)', 1049, 999.00, 1048.95, 1049, 1049, 'SME', 5.00, NULL, NULL),
(54, '398', 1, 398, '2.0GB', '2.0GB', '30 Days', 1538, 1465.00, 1538.25, 1538, 1538, 'GIFTING', 5.00, NULL, NULL),
(55, '218', 1, 218, '2.0GB', '2.0GB', '30 Days', 1538, 1465.00, 1538.25, 1538, 1538, 'SME', 5.00, NULL, NULL),
(56, '482', 1, 482, '1.8GB', '1.8GB', '30 Days ThryveData + 35Minutes', 1538, 1465.00, 1538.25, 1538, 1538, 'GIFTING', 5.00, NULL, NULL),
(57, '527', 1, 527, '3.5GB', '3.5GB', '7 Days', 1544, 1470.00, 1543.50, 1544, 1544, 'GIFTING', 5.00, NULL, NULL),
(58, '532', 1, 532, '3.5GB', '3.5GB', '1 Week', 1544, 1470.00, 1543.50, 1544, 1544, 'AWOOF DATA', 5.00, NULL, NULL),
(59, '569', 1, 569, '3.5GB', '3.5GB', '1 Week', 1544, 1470.00, 1543.50, 1544, 1544, 'SME', 5.00, NULL, NULL),
(60, '493', 1, 493, '3.0GB', '3.0GB', '30 Days (DT)', 1549, 1475.00, 1548.75, 1549, 1549, 'SME', 5.00, NULL, NULL),
(61, '596', 1, 596, '7.0GB', '7.0GB', '2 Days', 1853, 1765.00, 1853.25, 1853, 1853, 'AWOOF DATA', 5.00, NULL, NULL),
(62, '597', 1, 597, '7.0GB', '7.0GB', '2 Days', 1853, 1765.00, 1853.25, 1853, 1853, 'GIFTING', 5.00, NULL, NULL),
(63, '399', 1, 399, '2.7GB', '2.7GB', '30 Days', 2058, 1960.00, 2058.00, 2058, 2058, 'GIFTING', 5.00, NULL, NULL),
(64, '494', 1, 494, '5.0GB', '5.0GB', '30 Days (DT)', 2095, 1995.00, 2094.75, 2095, 2095, 'SME', 5.00, NULL, NULL),
(65, '219', 1, 219, '3.5GB', '3.5GB', '30 Days', 2552, 2430.00, 2551.50, 2552, 2552, 'SME', 5.00, NULL, NULL),
(66, '396', 1, 396, '6.0GB', '6.0GB', '7 Days', 2557, 2435.00, 2556.75, 2557, 2557, 'GIFTING', 5.00, NULL, NULL),
(67, '460', 1, 460, '3.5GB', '3.5GB', '30 Days', 2557, 2435.00, 2556.75, 2557, 2557, 'GIFTING', 5.00, NULL, NULL),
(68, '551', 1, 551, '6.75GB', '6.75GB', '30 Days - XtraValue', 3087, 2940.00, 3087.00, 3087, 3087, 'AWOOF DATA', 5.00, NULL, NULL),
(69, '570', 1, 570, '5.0GB', '5.0GB', '30 Days ThryveData + 90Minutes', 3087, 2940.00, 3087.00, 3087, 3087, 'GIFTING', 5.00, NULL, NULL),
(70, '571', 1, 571, '5.0GB', '5.0GB', '30 Days ThryveData + 90Minutes', 3087, 2940.00, 3087.00, 3087, 3087, 'AWOOF DATA', 5.00, NULL, NULL),
(71, '470', 1, 470, '7.0GB', '7.0GB', '30 Days', 3565, 3395.00, 3564.75, 3565, 3565, 'GIFTING', 5.00, NULL, NULL),
(72, '220', 1, 220, '7.0GB', '7.0GB', '30 Days', 3565, 3395.00, 3564.75, 3565, 3565, 'SME', 5.00, NULL, NULL),
(73, '397', 1, 397, '11.0GB', '11.0GB', '7 Days', 3602, 3430.00, 3601.50, 3602, 3602, 'GIFTING', 5.00, NULL, NULL),
(74, '499', 1, 499, '11.0GB', '11.0GB', '1 Week', 3602, 3430.00, 3601.50, 3602, 3602, 'AWOOF DATA', 5.00, NULL, NULL),
(75, '221', 1, 221, '10.0GB', '10.0GB', '30 Days', 4583, 4365.00, 4583.25, 4583, 4583, 'SME', 5.00, NULL, NULL),
(76, '402', 1, 402, '10.0GB', '10.0GB', '30 Days', 4620, 4400.00, 4620.00, 4620, 4620, 'GIFTING', 5.00, NULL, NULL),
(77, '552', 1, 552, '14.5GB', '14.5GB', '30 Days - XtraValue', 5145, 4900.00, 5145.00, 5145, 5145, 'AWOOF DATA', 5.00, NULL, NULL),
(78, '496', 1, 496, '20.0GB', '20.0GB', '7 Days', 5145, 4900.00, 5145.00, 5145, 5145, 'GIFTING', 5.00, NULL, NULL),
(79, '498', 1, 498, '20.0GB', '20.0GB', '1 Week', 5145, 4900.00, 5145.00, 5145, 5145, 'AWOOF DATA', 5.00, NULL, NULL),
(80, '403', 1, 403, '12.5GB', '12.5GB', '30 Days', 5602, 5335.00, 5601.75, 5602, 5602, 'GIFTING', 5.00, NULL, NULL),
(81, '404', 1, 404, '16.5GB', '16.5GB', '30 Days', 6689, 6370.00, 6688.50, 6689, 6689, 'GIFTING', 5.00, NULL, NULL),
(82, '468', 1, 468, '20.0GB', '20.0GB', '30 Days', 7639, 7275.00, 7638.75, 7639, 7639, 'GIFTING', 5.00, NULL, NULL),
(83, '469', 1, 469, '25.0GB', '25.0GB', '30 Days', 9167, 8730.00, 9166.50, 9167, 9167, 'GIFTING', 5.00, NULL, NULL),
(84, '407', 1, 407, '36.0GB', '36.0GB', '30 Days', 11319, 10780.00, 11319.00, 11319, 11319, 'GIFTING', 5.00, NULL, NULL),
(85, '408', 1, 408, '75.0GB', '75.0GB', '30 Days', 18333, 17460.00, 18333.00, 18333, 18333, 'GIFTING', 5.00, NULL, NULL),
(86, '526', 1, 526, '90.0GB', '90.0GB', '2 Months', 25725, 24500.00, 25725.00, 25725, 25725, 'GIFTING', 5.00, NULL, NULL),
(87, '409', 1, 409, '165.0GB', '165.0GB', '30 Days', 36015, 34300.00, 36015.00, 36015, 36015, 'GIFTING', 5.00, NULL, NULL),
(88, '525', 1, 525, '150.0GB', '150.0GB', '2 Months', 41160, 39200.00, 41160.00, 41160, 41160, 'GIFTING', 5.00, NULL, NULL),
(89, '438', 1, 438, '250.0GB', '250.0GB', '30 Days', 56595, 53900.00, 56595.00, 56595, 56595, 'GIFTING', 5.00, NULL, NULL),
(90, '490', 1, 490, '800.0GB', '800.0GB', '1 Year', 128625, 122500.00, 128625.00, 128625, 128625, 'GIFTING', 5.00, NULL, NULL),
(91, '546', 3, 546, '40.0MB', '40.0MB', '1 Day', 53, 50.00, 52.50, 53, 53, 'GIFTING', 5.00, NULL, NULL),
(92, '305', 3, 305, '200.0MB', '200.0MB', '2 Weeks', 95, 90.00, 94.50, 95, 95, 'CORPORATE GIFTING', 5.00, NULL, NULL),
(93, '500', 3, 500, '100.0MB', '100.0MB', '1 Day', 105, 100.00, 105.00, 105, 105, 'GIFTING', 5.00, NULL, NULL),
(94, '375', 3, 375, '750.0MB', '750.0MB', '1 Day', 205, 195.00, 204.75, 205, 205, 'SME', 5.00, NULL, NULL),
(95, '306', 3, 306, '500.0MB', '500.0MB', '30 Days', 210, 200.00, 210.00, 210, 210, 'CORPORATE GIFTING', 5.00, NULL, NULL),
(96, '501', 3, 501, '210.0MB', '210.0MB', '2 Days', 210, 200.00, 210.00, 210, 210, 'GIFTING', 5.00, NULL, NULL),
(97, '559', 3, 559, '1.0GB', '1.0GB', '3 Days', 293, 279.00, 292.95, 293, 293, 'CORPORATE GIFTING', 5.00, NULL, NULL),
(98, '376', 3, 376, '1.5GB', '1.5GB', '1 Day', 310, 295.00, 309.75, 310, 310, 'SME', 5.00, NULL, NULL),
(99, '562', 3, 562, '1.0GB', '1.0GB', '1 Week', 341, 325.00, 341.25, 341, 341, 'CORPORATE GIFTING', 5.00, NULL, NULL),
(100, '506', 3, 506, '1.0GB', '1.0GB', '1 Day', 357, 340.00, 357.00, 357, 357, 'GIFTING', 5.00, NULL, NULL),
(101, '307', 3, 307, '1.0GB', '1.0GB', '30 Days', 431, 410.00, 430.50, 431, 431, 'CORPORATE GIFTING', 5.00, NULL, NULL),
(102, '377', 3, 377, '2.5GB', '2.5GB', '2 Days', 509, 485.00, 509.25, 509, 509, 'SME', 5.00, NULL, NULL),
(103, '502', 3, 502, '500.0MB', '500.0MB', '7 Days + 1GB Night', 509, 485.00, 509.25, 509, 509, 'GIFTING', 5.00, NULL, NULL),
(104, '507', 3, 507, '1.0GB', '1.0GB', '1 Day + 1GB Night', 509, 485.00, 509.25, 509, 509, 'GIFTING', 5.00, NULL, NULL),
(105, '508', 3, 508, '1.55GB', '1.55GB', '2 Days + 2GB Night', 611, 582.00, 611.10, 611, 611, 'GIFTING', 5.00, NULL, NULL),
(106, '503', 3, 503, '1.1GB', '1.1GB', '14 Days', 764, 728.00, 764.40, 764, 764, 'GIFTING', 5.00, NULL, NULL),
(107, '308', 3, 308, '2.0GB', '2.0GB', '30 Days', 861, 820.00, 861.00, 861, 861, 'CORPORATE GIFTING', 5.00, NULL, NULL),
(108, '560', 3, 560, '3.0GB', '3.0GB', '3 Days', 879, 837.00, 878.85, 879, 879, 'CORPORATE GIFTING', 5.00, NULL, NULL),
(109, '509', 3, 509, '3.1GB', '3.1GB', '2 Days + 2GB Night', 1019, 970.00, 1018.50, 1019, 1019, 'GIFTING', 5.00, NULL, NULL),
(110, '511', 3, 511, '1.1GB', '1.1GB', '30 Days + 1.5GB Night', 1019, 970.00, 1018.50, 1019, 1019, 'GIFTING', 5.00, NULL, NULL),
(111, '563', 3, 563, '3.0GB', '3.0GB', '1 Week', 1024, 975.00, 1023.75, 1024, 1024, 'CORPORATE GIFTING', 5.00, NULL, NULL),
(112, '309', 3, 309, '3.0GB', '3.0GB', '30 Days', 1292, 1230.00, 1291.50, 1292, 1292, 'CORPORATE GIFTING', 5.00, NULL, NULL),
(113, '561', 3, 561, '5.0GB', '5.0GB', '3 Days', 1465, 1395.00, 1464.75, 1465, 1465, 'CORPORATE GIFTING', 5.00, NULL, NULL),
(114, '512', 3, 512, '2.0GB', '2.0GB', '30 Days + 3GB Night', 1528, 1455.00, 1527.75, 1528, 1528, 'GIFTING', 5.00, NULL, NULL),
(115, '510', 3, 510, '3.9GB', '3.9GB', '7 Days + 2GB Night', 1528, 1455.00, 1527.75, 1528, 1528, 'GIFTING', 5.00, NULL, NULL),
(116, '564', 3, 564, '5.0GB', '5.0GB', '1 Week', 1706, 1625.00, 1706.25, 1706, 1706, 'CORPORATE GIFTING', 5.00, NULL, NULL),
(117, '513', 3, 513, '3.15GB', '3.15GB', '30 Days + 3GB Night', 2037, 1940.00, 2037.00, 2037, 2037, 'GIFTING', 5.00, NULL, NULL),
(118, '378', 3, 378, '10.0GB', '10.0GB', '1 Week', 2048, 1950.00, 2047.50, 2048, 2048, 'SME', 5.00, NULL, NULL),
(119, '310', 3, 310, '5.0GB', '5.0GB', '30 Days', 2153, 2050.00, 2152.50, 2153, 2153, 'CORPORATE GIFTING', 5.00, NULL, NULL),
(120, '514', 3, 514, '4.25GB', '4.25GB', '30 Days + 3GB Night', 2546, 2425.00, 2546.25, 2546, 2546, 'GIFTING', 5.00, NULL, NULL),
(121, '515', 3, 515, '8.0GB', '8.0GB', '30 Days + 2GB Night', 3087, 2940.00, 3087.00, 3087, 3087, 'GIFTING', 5.00, NULL, NULL),
(122, '516', 3, 516, '10.5GB', '10.5GB', '30 Days + 2GB Night', 4074, 3880.00, 4074.00, 4074, 4074, 'GIFTING', 5.00, NULL, NULL),
(123, '311', 3, 311, '10.0GB', '10.0GB', '30 Days', 4305, 4100.00, 4305.00, 4305, 4305, 'CORPORATE GIFTING', 5.00, NULL, NULL),
(124, '517', 3, 517, '13.5GB', '13.5GB', '30 Days + 2.5GB Night', 5093, 4850.00, 5092.50, 5093, 5093, 'GIFTING', 5.00, NULL, NULL),
(125, '518', 3, 518, '26.0GB', '26.0GB', '30 Days + 2GB Night', 8148, 7760.00, 8148.00, 8148, 8148, 'GIFTING', 5.00, NULL, NULL),
(126, '519', 3, 519, '36.0GB', '36.0GB', '30 Days + 2GB Night', 10185, 9700.00, 10185.00, 10185, 10185, 'GIFTING', 5.00, NULL, NULL),
(127, '520', 3, 520, '62.0GB', '62.0GB', '30 Days + 2GB Night', 15278, 14550.00, 15277.50, 15278, 15278, 'GIFTING', 5.00, NULL, NULL),
(128, '521', 3, 521, '105.0GB', '105.0GB', '30 Days + 2GB Night', 20370, 19400.00, 20370.00, 20370, 20370, 'GIFTING', 5.00, NULL, NULL),
(129, '522', 3, 522, '1.0TB', '1.0TB', '1 Year', 152775, 145500.00, 152775.00, 152775, 152775, 'GIFTING', 5.00, NULL, NULL),
(130, '528', 2, 528, '150.0MB', '150.0MB', '1Day- Don\'t Sell To Sim Owing!', 63, 60.00, 63.00, 63, 63, 'AWOOF DATA', 5.00, NULL, NULL),
(131, '544', 2, 544, '75.0MB', '75.0MB', '1 Day', 79, 75.00, 78.75, 79, 79, 'GIFTING', 5.00, NULL, NULL),
(132, '479', 2, 479, '110.0MB', '110.0MB', '1 Day', 105, 100.00, 105.00, 105, 105, 'GIFTING', 5.00, NULL, NULL),
(133, '476', 2, 476, '300.0MB', '300.0MB', '2Days-Don\'t Sell To Sim Owing!', 121, 115.00, 120.75, 121, 121, 'AWOOF DATA', 5.00, NULL, NULL),
(134, '480', 2, 480, '230.0MB', '230.0MB', '2 Days', 210, 200.00, 210.00, 210, 210, 'GIFTING', 5.00, NULL, NULL),
(135, '447', 2, 447, '600.0MB', '600.0MB', '2Days-Don\'t Sell To Sim Owing!', 226, 215.00, 225.75, 226, 226, 'AWOOF DATA', 5.00, NULL, NULL),
(136, '542', 2, 542, '1.0GB', '1.0GB', '3 Days - Social Apps Only', 315, 300.00, 315.00, 315, 315, 'GIFTING', 5.00, NULL, NULL),
(137, '481', 2, 481, '300.0MB', '300.0MB', '2 Days', 315, 300.00, 315.00, 315, 315, 'GIFTING', 5.00, NULL, NULL),
(138, '584', 2, 584, '500.0MB', '500.0MB', '1 Day', 360, 343.00, 360.15, 360, 360, 'GIFTING', 5.00, NULL, NULL),
(139, '577', 2, 577, '1.5GB', '1.5GB', '1Day- Don\'t Sell To Sim Owing!', 436, 415.00, 435.75, 436, 436, 'AWOOF DATA', 5.00, NULL, NULL),
(140, '534', 2, 534, '500.0MB', '500.0MB', '1 Week', 515, 490.00, 514.50, 515, 515, 'SME', 5.00, NULL, NULL),
(141, '448', 2, 448, '1.0GB', '1.0GB', '1 Day', 515, 490.00, 514.50, 515, 515, 'GIFTING', 5.00, NULL, NULL),
(142, '478', 2, 478, '500.0MB', '500.0MB', '7 Days', 515, 490.00, 514.50, 515, 515, 'GIFTING', 5.00, NULL, NULL),
(143, '543', 2, 543, '1.5GB', '1.5GB', '7 Days - Social Apps Only', 520, 495.00, 519.75, 520, 520, 'GIFTING', 5.00, NULL, NULL),
(144, '410', 2, 410, '1.5GB', '1.5GB', '2 Days', 617, 588.00, 617.40, 617, 617, 'GIFTING', 5.00, NULL, NULL),
(145, '550', 2, 550, '2.0GB', '2.0GB', '2Days-Don\'t Sell To Sim Owing!', 646, 615.00, 645.75, 646, 646, 'AWOOF DATA', 5.00, NULL, NULL),
(146, '411', 2, 411, '3.0GB', '3.0GB', '2 Days', 772, 735.00, 771.75, 772, 772, 'GIFTING', 5.00, NULL, NULL),
(147, '581', 2, 581, '3.0GB', '3.0GB', '2 Days', 788, 750.00, 787.50, 788, 788, 'AWOOF DATA', 5.00, NULL, NULL),
(148, '535', 2, 535, '1.0GB', '1.0GB', '1 Week', 824, 785.00, 824.25, 824, 824, 'SME', 5.00, NULL, NULL),
(149, '414', 2, 414, '1.0GB', '1.0GB', '7 Days', 824, 785.00, 824.25, 824, 824, 'GIFTING', 5.00, NULL, NULL),
(150, '412', 2, 412, '3.2GB', '3.2GB', '2 Days', 1029, 980.00, 1029.00, 1029, 1029, 'GIFTING', 5.00, NULL, NULL),
(151, '415', 2, 415, '1.5GB', '1.5GB', '7 Days', 1029, 980.00, 1029.00, 1029, 1029, 'GIFTING', 5.00, NULL, NULL),
(152, '536', 2, 536, '2.0GB', '2.0GB', '30 Days', 1544, 1470.00, 1543.50, 1544, 1544, 'SME', 5.00, NULL, NULL),
(153, '565', 2, 565, '3.5GB', '3.5GB', '1 Week', 1544, 1470.00, 1543.50, 1544, 1544, 'SME', 5.00, NULL, NULL),
(154, '413', 2, 413, '5.0GB', '5.0GB', '2 Days', 1544, 1470.00, 1543.50, 1544, 1544, 'GIFTING', 5.00, NULL, NULL),
(155, '416', 2, 416, '3.5GB', '3.5GB', '7 Days', 1544, 1470.00, 1543.50, 1544, 1544, 'GIFTING', 5.00, NULL, NULL),
(156, '420', 2, 420, '2.0GB', '2.0GB', '30 Days', 1544, 1470.00, 1543.50, 1544, 1544, 'GIFTING', 5.00, NULL, NULL),
(157, '578', 2, 578, '5.0GB', '5.0GB', '7Days-Don\'t Sell To Sim Owing!', 1591, 1515.00, 1590.75, 1591, 1591, 'AWOOF DATA', 5.00, NULL, NULL),
(158, '537', 2, 537, '3.0GB', '3.0GB', '30 Days', 2058, 1960.00, 2058.00, 2058, 2058, 'SME', 5.00, NULL, NULL),
(159, '421', 2, 421, '3.0GB', '3.0GB', '30 Days', 2058, 1960.00, 2058.00, 2058, 2058, 'GIFTING', 5.00, NULL, NULL),
(160, '538', 2, 538, '4.0GB', '4.0GB', '30 Days', 2573, 2450.00, 2572.50, 2573, 2573, 'SME', 5.00, NULL, NULL),
(161, '566', 2, 566, '6.0GB', '6.0GB', '1 Week', 2573, 2450.00, 2572.50, 2573, 2573, 'SME', 5.00, NULL, NULL),
(162, '417', 2, 417, '6.0GB', '6.0GB', '7 Days', 2573, 2450.00, 2572.50, 2573, 2573, 'GIFTING', 5.00, NULL, NULL),
(163, '422', 2, 422, '4.0GB', '4.0GB', '30 Days', 2573, 2450.00, 2572.50, 2573, 2573, 'GIFTING', 5.00, NULL, NULL),
(164, '539', 2, 539, '8.0GB', '8.0GB', '30 Days', 3087, 2940.00, 3087.00, 3087, 3087, 'SME', 5.00, NULL, NULL),
(165, '567', 2, 567, '10.0GB', '10.0GB', '1 Week', 3087, 2940.00, 3087.00, 3087, 3087, 'SME', 5.00, NULL, NULL),
(166, '418', 2, 418, '10.0GB', '10.0GB', '7 Days', 3087, 2940.00, 3087.00, 3087, 3087, 'GIFTING', 5.00, NULL, NULL),
(167, '423', 2, 423, '8.0GB', '8.0GB', '30 Days', 3087, 2940.00, 3087.00, 3087, 3087, 'GIFTING', 5.00, NULL, NULL),
(168, '365', 2, 365, '10.0GB', '10.0GB', '30Days-Don\'t Sell To Sim Owing', 3166, 3015.00, 3165.75, 3166, 3166, 'AWOOF DATA', 5.00, NULL, NULL),
(169, '540', 2, 540, '10.0GB', '10.0GB', '30 Days', 4116, 3920.00, 4116.00, 4116, 4116, 'SME', 5.00, NULL, NULL),
(170, '424', 2, 424, '10.0GB', '10.0GB', '30 Days', 4116, 3920.00, 4116.00, 4116, 4116, 'GIFTING', 5.00, NULL, NULL),
(171, '531', 2, 531, '13.0GB', '13.0GB', '30 Days', 5145, 4900.00, 5145.00, 5145, 5145, 'SME', 5.00, NULL, NULL),
(172, '419', 2, 419, '18.0GB', '18.0GB', '7 Days', 5145, 4900.00, 5145.00, 5145, 5145, 'GIFTING', 5.00, NULL, NULL),
(173, '425', 2, 425, '13.0GB', '13.0GB', '30 Days', 5145, 4900.00, 5145.00, 5145, 5145, 'GIFTING', 5.00, NULL, NULL),
(174, '575', 2, 575, '18.0GB', '18.0GB', '30 Days', 6174, 5880.00, 6174.00, 6174, 6174, 'SME', 5.00, NULL, NULL),
(175, '426', 2, 426, '18.0GB', '18.0GB', '30 Days', 6174, 5880.00, 6174.00, 6174, 6174, 'GIFTING', 5.00, NULL, NULL),
(176, '427', 2, 427, '25.0GB', '25.0GB', '30 Days', 8232, 7840.00, 8232.00, 8232, 8232, 'GIFTING', 5.00, NULL, NULL),
(177, '428', 2, 428, '35.0GB', '35.0GB', '30 Days', 10290, 9800.00, 10290.00, 10290, 10290, 'GIFTING', 5.00, NULL, NULL),
(178, '429', 2, 429, '60.0GB', '60.0GB', '30 Days', 15435, 14700.00, 15435.00, 15435, 15435, 'GIFTING', 5.00, NULL, NULL),
(179, '430', 2, 430, '100.0GB', '100.0GB', '30 Days', 20580, 19600.00, 20580.00, 20580, 20580, 'GIFTING', 5.00, NULL, NULL),
(180, '576', 2, 576, '160.0GB', '160.0GB', '30 Days', 30870, 29400.00, 30870.00, 30870, 30870, 'GIFTING', 5.00, NULL, NULL),
(181, '431', 2, 431, '300.0GB', '300.0GB', '3 Months', 51450, 49000.00, 51450.00, 51450, 51450, 'GIFTING', 5.00, NULL, NULL),
(182, '529', 2, 529, '350.0GB', '350.0GB', '4 Months', 61740, 58800.00, 61740.00, 61740, 61740, 'GIFTING', 5.00, NULL, NULL),
(183, '530', 2, 530, '650.0GB', '650.0GB', '1 Year', 102900, 98000.00, 102900.00, 102900, 102900, 'GIFTING', 5.00, NULL, NULL),
(184, '330', 4, 330, '500.0MB', '500.0MB', '30 Days', 189, 180.00, 189.00, 189, 189, 'CORPORATE GIFTING', 5.00, NULL, NULL),
(185, '332', 4, 332, '1.0GB', '1.0GB', '30 Days', 378, 360.00, 378.00, 378, 378, 'CORPORATE GIFTING', 5.00, NULL, NULL),
(186, '333', 4, 333, '1.5GB', '1.5GB', '30 Days', 567, 540.00, 567.00, 567, 567, 'CORPORATE GIFTING', 5.00, NULL, NULL),
(187, '334', 4, 334, '2.0GB', '2.0GB', '30 Days', 756, 720.00, 756.00, 756, 756, 'CORPORATE GIFTING', 5.00, NULL, NULL),
(188, '335', 4, 335, '3.0GB', '3.0GB', '30 Days', 1134, 1080.00, 1134.00, 1134, 1134, 'CORPORATE GIFTING', 5.00, NULL, NULL),
(189, '337', 4, 337, '5.0GB', '5.0GB', '30 Days', 1890, 1800.00, 1890.00, 1890, 1890, 'CORPORATE GIFTING', 5.00, NULL, NULL),
(190, '338', 4, 338, '10.0GB', '10.0GB', '30 Days', 3780, 3600.00, 3780.00, 3780, 3780, 'CORPORATE GIFTING', 5.00, NULL, NULL),
(191, '340', 4, 340, '15.0GB', '15.0GB', '30 Days', 5670, 5400.00, 5670.00, 5670, 5670, 'CORPORATE GIFTING', 5.00, NULL, NULL),
(192, '341', 4, 341, '20.0GB', '20.0GB', '30 Days', 7560, 7200.00, 7560.00, 7560, 7560, 'CORPORATE GIFTING', 5.00, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `electricity`
--

CREATE TABLE `electricity` (
  `eId` bigint(20) UNSIGNED NOT NULL,
  `ePlan` varchar(255) NOT NULL,
  `uzobest_disco_id` int(11) DEFAULT NULL COMMENT 'Uzobest API disco ID',
  `uzobest_disco_name` varchar(100) DEFAULT NULL,
  `eProviderId` varchar(255) DEFAULT NULL,
  `ePrice` decimal(10,2) NOT NULL DEFAULT 0.00,
  `eBuyingPrice` decimal(10,2) NOT NULL DEFAULT 0.00,
  `cost_price` decimal(10,2) DEFAULT NULL COMMENT 'Cost from Uzobest API',
  `selling_price` decimal(10,2) DEFAULT NULL COMMENT 'Admin-configured selling price',
  `eStatus` tinyint(4) NOT NULL DEFAULT 1,
  `profit_margin` decimal(10,2) DEFAULT NULL COMMENT 'Calculated: selling_price - cost_price'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `electricity`
--

INSERT INTO `electricity` (`eId`, `ePlan`, `uzobest_disco_id`, `uzobest_disco_name`, `eProviderId`, `ePrice`, `eBuyingPrice`, `cost_price`, `selling_price`, `eStatus`, `profit_margin`) VALUES
(1, 'EKEDC', 2, 'Eko Electric', 'ekedc', 50.00, 45.00, NULL, NULL, 1, NULL),
(2, 'IKEDC', 1, 'Ikeja Electric', 'ikedc', 50.00, 45.00, NULL, NULL, 1, NULL),
(3, 'AEDC', 3, 'Abuja Electric', 'aedc', 50.00, 45.00, NULL, NULL, 1, NULL),
(4, 'KEDCO', 4, 'Kano Electric', 'kedco', 50.00, 45.00, NULL, NULL, 1, NULL),
(5, 'PHED', 6, 'Port Harcourt Electric', 'phed', 50.00, 45.00, NULL, NULL, 1, NULL),
(6, 'JED', 9, 'Jos Electric', 'jed', 50.00, 45.00, NULL, NULL, 1, NULL),
(7, 'IBEDC', 7, 'Ibadan Electric', 'ibedc', 50.00, 45.00, NULL, NULL, 1, NULL),
(8, 'KAEDCO', 8, 'Kaduna Electric', 'kaedco', 50.00, 45.00, NULL, NULL, 1, NULL),
(9, 'EEDC', 5, 'Enugu Electric', 'eedc', 50.00, 45.00, NULL, NULL, 1, NULL),
(10, 'BEDC', 10, 'Benin Electric', 'bedc', 50.00, 45.00, NULL, NULL, 1, NULL),
(11, 'YEDC', 11, 'Yola Electric', 'yedc', 50.00, 45.00, NULL, NULL, 1, NULL),
(12, 'ABA', 12, 'Aba Electric', 'aba', 50.00, 45.00, NULL, NULL, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `electricityid`
--

CREATE TABLE `electricityid` (
  `eId` bigint(20) UNSIGNED NOT NULL,
  `electricityid` varchar(10) NOT NULL,
  `provider` varchar(50) NOT NULL,
  `providerStatus` varchar(10) NOT NULL DEFAULT 'On',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `examid`
--

CREATE TABLE `examid` (
  `eId` bigint(20) UNSIGNED NOT NULL,
  `examid` varchar(10) NOT NULL,
  `provider` varchar(50) NOT NULL,
  `price` int(11) NOT NULL,
  `providerStatus` varchar(10) NOT NULL DEFAULT 'On',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exampin`
--

CREATE TABLE `exampin` (
  `eId` bigint(20) UNSIGNED NOT NULL,
  `ePlan` varchar(255) NOT NULL,
  `eProviderId` varchar(255) DEFAULT NULL,
  `ePrice` decimal(10,2) NOT NULL DEFAULT 0.00,
  `eBuyingPrice` decimal(10,2) NOT NULL DEFAULT 0.00,
  `eStatus` tinyint(4) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feature_toggles`
--

CREATE TABLE `feature_toggles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `feature_name` varchar(255) NOT NULL,
  `feature_key` varchar(255) NOT NULL,
  `is_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `description` text DEFAULT NULL,
  `environment` varchar(255) NOT NULL DEFAULT 'production',
  `rollout_percentage` int(11) NOT NULL DEFAULT 0,
  `target_users` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`target_users`)),
  `start_date` timestamp NULL DEFAULT NULL,
  `end_date` timestamp NULL DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `feature_toggles`
--

INSERT INTO `feature_toggles` (`id`, `feature_name`, `feature_key`, `is_enabled`, `description`, `environment`, `rollout_percentage`, `target_users`, `start_date`, `end_date`, `created_by`, `metadata`, `created_at`, `updated_at`) VALUES
(1, 'KYC Verification', 'kyc_verification', 1, NULL, 'production', 100, NULL, NULL, NULL, NULL, '\"{\\\"description\\\":\\\"Enable KYC verification for user accounts\\\",\\\"category\\\":\\\"security\\\",\\\"required_permissions\\\":[\\\"admin\\\",\\\"kyc_manager\\\"]}\"', '2025-12-05 05:07:04', '2025-12-05 05:07:04'),
(2, 'Referral System', 'referral_system', 1, NULL, 'production', 100, NULL, NULL, NULL, NULL, '\"{\\\"description\\\":\\\"Enable user referral system with rewards\\\",\\\"category\\\":\\\"marketing\\\",\\\"reward_percentage\\\":5}\"', '2025-12-05 05:07:04', '2025-12-05 05:07:04'),
(3, 'Wallet to Wallet Transfer', 'wallet_to_wallet', 0, NULL, 'production', 0, NULL, NULL, NULL, NULL, '\"{\\\"description\\\":\\\"Enable wallet-to-wallet transfers between users\\\",\\\"category\\\":\\\"payments\\\",\\\"min_amount\\\":100,\\\"max_amount\\\":50000,\\\"daily_limit\\\":200000}\"', '2025-12-05 05:07:04', '2025-12-05 05:07:04'),
(4, 'API Monitoring', 'api_monitoring', 1, NULL, 'production', 100, NULL, NULL, NULL, NULL, '\"{\\\"description\\\":\\\"Monitor API performance and usage\\\",\\\"category\\\":\\\"system\\\",\\\"alert_threshold\\\":95}\"', '2025-12-05 05:07:04', '2025-12-05 05:07:04'),
(5, 'Real-time Notifications', 'real_time_notifications', 1, NULL, 'production', 80, NULL, NULL, NULL, NULL, '\"{\\\"description\\\":\\\"Enable real-time push notifications\\\",\\\"category\\\":\\\"communication\\\",\\\"channels\\\":[\\\"push\\\",\\\"email\\\",\\\"sms\\\"]}\"', '2025-12-05 05:07:04', '2025-12-05 05:07:04'),
(6, 'Advanced Analytics', 'advanced_analytics', 1, NULL, 'production', 100, NULL, NULL, NULL, NULL, '\"{\\\"description\\\":\\\"Enable advanced analytics and reporting\\\",\\\"category\\\":\\\"analytics\\\",\\\"retention_days\\\":365}\"', '2025-12-05 05:07:04', '2025-12-05 05:07:04'),
(7, 'Auto Reconciliation', 'auto_reconciliation', 0, NULL, 'production', 0, NULL, NULL, NULL, NULL, '\"{\\\"description\\\":\\\"Automatically reconcile transactions\\\",\\\"category\\\":\\\"financial\\\",\\\"reconcile_interval\\\":\\\"hourly\\\"}\"', '2025-12-05 05:07:04', '2025-12-05 05:07:04'),
(8, 'Bulk Operations', 'bulk_operations', 1, NULL, 'production', 100, NULL, NULL, NULL, NULL, '\"{\\\"description\\\":\\\"Enable bulk operations for admins\\\",\\\"category\\\":\\\"admin\\\",\\\"max_batch_size\\\":1000}\"', '2025-12-05 05:07:04', '2025-12-05 05:07:04'),
(9, 'Maintenance Mode', 'maintenance_mode', 0, NULL, 'production', 0, NULL, NULL, NULL, NULL, '\"{\\\"description\\\":\\\"Put system in maintenance mode\\\",\\\"category\\\":\\\"system\\\",\\\"bypass_ips\\\":[\\\"127.0.0.1\\\"]}\"', '2025-12-05 05:07:04', '2025-12-05 05:07:04'),
(10, 'Debug Mode', 'debug_mode', 0, NULL, 'production', 0, NULL, NULL, NULL, NULL, '\"{\\\"description\\\":\\\"Enable debug mode for development\\\",\\\"category\\\":\\\"development\\\",\\\"log_level\\\":\\\"debug\\\"}\"', '2025-12-05 05:07:04', '2025-12-05 05:07:04');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kyc_verification`
--

CREATE TABLE `kyc_verification` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `nin` varchar(11) DEFAULT NULL,
  `bvn` varchar(11) DEFAULT NULL,
  `document_type` varchar(20) DEFAULT NULL,
  `document_path` varchar(255) DEFAULT NULL,
  `verification_status` varchar(20) NOT NULL DEFAULT 'pending',
  `verification_response` text DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `message_system`
--

CREATE TABLE `message_system` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2024_11_11_000001_add_virtual_accounts_to_users_table', 1),
(5, '2025_11_04_135053_create_subscribers_table', 1),
(6, '2025_11_04_135059_create_sysusers_table', 1),
(7, '2025_11_04_135105_create_userlogin_table', 1),
(8, '2025_11_04_154421_create_transactions_table', 1),
(9, '2025_11_04_154502_create_network_ids_table', 1),
(10, '2025_11_04_154503_create_airtimes_table', 1),
(11, '2025_11_04_154504_create_data_plans_table', 1),
(12, '2025_11_04_154505_create_cable_plans_table', 1),
(13, '2025_11_04_154515_create_api_configs_table', 1),
(14, '2025_11_04_163947_create_sitesettings_table', 1),
(15, '2025_11_04_164033_create_notifications_table', 1),
(16, '2025_11_04_164043_create_contact_table', 1),
(17, '2025_11_04_164056_create_electricity_table', 1),
(18, '2025_11_04_164110_create_exampin_table', 1),
(19, '2025_11_04_164122_create_alphatopupprice_table', 1),
(20, '2025_11_04_164145_create_airtimepinprice_table', 1),
(21, '2025_11_04_213657_update_transactions_table_for_compatibility', 1),
(22, '2025_11_05_001_create_missing_tables_from_php_app', 1),
(23, '2025_11_05_002_enhance_existing_tables_for_php_app_compatibility', 1),
(24, '2025_11_05_163340_update_apilinks_table_structure', 1),
(25, '2025_11_05_170102_add_network_status_fields_to_network_ids_table', 1),
(26, '2025_11_05_171303_create_cable_ids_table', 1),
(27, '2025_11_05_171347_update_cable_plans_table_structure', 1),
(28, '2025_11_05_171941_recreate_cable_plans_table_for_php_compatibility', 1),
(29, '2025_11_05_173332_create_recharge_pin_history_table', 1),
(30, '2025_11_11_150723_create_configurations_table', 1),
(31, '2025_11_11_171512_create_feature_toggles_table', 1),
(32, '2025_11_12_164942_create_api_configurations_table', 1),
(33, '2025_11_30_153213_add_monnify_account_number_to_users_table', 1),
(34, '2025_12_05_000001_create_service_sync_status_table', 2),
(35, '2024_12_05_000001_add_uzobest_pricing_columns', 3);

-- --------------------------------------------------------

--
-- Table structure for table `network_ids`
--

CREATE TABLE `network_ids` (
  `nId` int(11) NOT NULL,
  `network` varchar(50) NOT NULL,
  `networkid` int(11) NOT NULL DEFAULT 1,
  `smeId` varchar(10) DEFAULT NULL,
  `giftingId` varchar(10) DEFAULT NULL,
  `corporateId` varchar(10) DEFAULT NULL,
  `networkStatus` enum('On','Off') NOT NULL DEFAULT 'On',
  `vtuStatus` enum('On','Off') NOT NULL DEFAULT 'On',
  `sharesellStatus` enum('On','Off') NOT NULL DEFAULT 'On',
  `smeStatus` enum('On','Off') NOT NULL DEFAULT 'On',
  `giftingStatus` enum('On','Off') NOT NULL DEFAULT 'On',
  `corporateStatus` enum('On','Off') NOT NULL DEFAULT 'On',
  `airtimepinStatus` enum('On','Off') NOT NULL DEFAULT 'On',
  `datapinStatus` enum('On','Off') NOT NULL DEFAULT 'On',
  `airtimeId` varchar(10) DEFAULT NULL,
  `status` enum('On','Off') NOT NULL DEFAULT 'On'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `network_ids`
--

INSERT INTO `network_ids` (`nId`, `network`, `networkid`, `smeId`, `giftingId`, `corporateId`, `networkStatus`, `vtuStatus`, `sharesellStatus`, `smeStatus`, `giftingStatus`, `corporateStatus`, `airtimepinStatus`, `datapinStatus`, `airtimeId`, `status`) VALUES
(1, 'MTN', 1, '1', '2', '3', 'On', 'On', 'On', 'On', 'On', 'On', 'On', 'On', '1', 'On'),
(2, 'Airtel', 1, '4', '5', '6', 'On', 'On', 'On', 'On', 'On', 'On', 'On', 'On', '2', 'On'),
(3, 'Glo', 1, '7', '8', '9', 'On', 'On', 'On', 'On', 'On', 'On', 'On', 'On', '3', 'On'),
(4, '9mobile', 1, '10', '11', '12', 'On', 'On', 'On', 'On', 'On', 'On', 'On', 'On', '4', 'On');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `nId` bigint(20) UNSIGNED NOT NULL,
  `nSubject` varchar(255) NOT NULL,
  `nMessageFor` enum('all','users','agents','vendors') NOT NULL DEFAULT 'all',
  `nMessage` text NOT NULL,
  `nStatus` tinyint(4) NOT NULL DEFAULT 1,
  `dPosted` timestamp NOT NULL DEFAULT current_timestamp(),
  `dUpdated` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `profit_tracking`
--

CREATE TABLE `profit_tracking` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `transaction_ref` varchar(50) NOT NULL,
  `revenue` decimal(10,2) NOT NULL,
  `cost` decimal(10,2) NOT NULL,
  `profit` decimal(10,2) NOT NULL,
  `service_type` varchar(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rechargepins`
--

CREATE TABLE `rechargepins` (
  `rId` bigint(20) UNSIGNED NOT NULL,
  `sId` int(11) NOT NULL,
  `network` varchar(10) NOT NULL,
  `amount` int(11) NOT NULL,
  `pins` text NOT NULL,
  `serialNumbers` text NOT NULL,
  `quantity` int(11) NOT NULL,
  `transref` varchar(50) NOT NULL,
  `totalPrice` int(11) NOT NULL,
  `buyprice` int(11) NOT NULL,
  `api` varchar(50) NOT NULL,
  `status` varchar(10) NOT NULL,
  `date` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rechargetokens`
--

CREATE TABLE `rechargetokens` (
  `rId` bigint(20) UNSIGNED NOT NULL,
  `sId` int(11) NOT NULL,
  `network` varchar(10) NOT NULL,
  `amount` int(11) NOT NULL,
  `pin` text NOT NULL,
  `serialNumber` text NOT NULL,
  `date` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `recharge_pin_history`
--

CREATE TABLE `recharge_pin_history` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `reference` varchar(255) NOT NULL,
  `network` varchar(255) NOT NULL,
  `denomination` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `business_name` varchar(255) DEFAULT NULL,
  `pins_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`pins_data`)),
  `status` varchar(255) NOT NULL DEFAULT 'successful',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `referral_bonuses`
--

CREATE TABLE `referral_bonuses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `referrer_id` int(11) NOT NULL,
  `referred_id` int(11) NOT NULL,
  `service_type` varchar(20) NOT NULL,
  `bonus_amount` decimal(10,2) NOT NULL,
  `transaction_ref` varchar(50) NOT NULL,
  `paid` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `service_sync_status`
--

CREATE TABLE `service_sync_status` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `service_type` varchar(50) NOT NULL,
  `last_sync_at` timestamp NULL DEFAULT NULL,
  `sync_status` enum('never','success','partial','failed') NOT NULL DEFAULT 'never',
  `total_synced` int(11) NOT NULL DEFAULT 0,
  `total_created` int(11) NOT NULL DEFAULT 0,
  `total_updated` int(11) NOT NULL DEFAULT 0,
  `total_errors` int(11) NOT NULL DEFAULT 0,
  `last_error` text DEFAULT NULL,
  `api_source` varchar(100) NOT NULL DEFAULT 'uzobest',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `service_sync_status`
--

INSERT INTO `service_sync_status` (`id`, `service_type`, `last_sync_at`, `sync_status`, `total_synced`, `total_created`, `total_updated`, `total_errors`, `last_error`, `api_source`, `created_at`, `updated_at`) VALUES
(1, 'cable_plans', NULL, 'never', 16, 0, 0, 0, 'Uzobest does not provide cable plan listing API', 'manual', '2025-12-16 14:54:54', '2025-12-16 14:54:54'),
(2, 'data_plans', '2025-12-16 14:56:07', 'failed', 0, 0, 0, 0, 'SQLSTATE[HY000]: General error: 1364 Field \'nId\' doesn\'t have a default value (Connection: mysql, SQL: insert into `network_ids` (`network`) values (MTN_PLAN))', 'uzobest', '2025-12-16 14:56:07', '2025-12-16 14:56:07'),
(3, 'electricity', '2025-12-16 15:56:12', 'success', 9, 9, 0, 0, NULL, 'uzobest', '2025-12-16 15:56:12', '2025-12-16 15:56:12');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('BluP6gMVB9SMymEfsWkIyUaXECnnbgk4rXafWM7L', 8133051779, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoidlF1dVBIN0ZiNzV4Z0Myb3didUpkdXJ1Zmk4TUlqMzRVb3RibzhVMCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzM6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9lbGVjdHJpY2l0eSI7czo1OiJyb3V0ZSI7czoxMToiZWxlY3RyaWNpdHkiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7czoxMToiMDgxMzMwNTE3NzkiO30=', 1765910973),
('FtlqMlrnm7e8oo98ndy9syiCBG76uZu4r4LWmfuE', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', 'YTo4OntzOjY6Il90b2tlbiI7czo0MDoib1VLMGxYR21od3B0cDNNYXVYeVJ0dDR4cnR5ZU1mNDZzb1l0eE9RNSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzk6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9hZG1pbi9jYWJsZS1wbGFucyI7czo1OiJyb3V0ZSI7czoyMzoiYWRtaW4uY2FibGUtcGxhbnMuaW5kZXgiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUyOiJsb2dpbl9hZG1pbl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtzOjIxOiJ3YWxzaGFrMTk5OUBnbWFpbC5jb20iO3M6Nzoic3lzVXNlciI7czoyMToid2Fsc2hhazE5OTlAZ21haWwuY29tIjtzOjc6InN5c1JvbGUiO2k6MTtzOjc6InN5c05hbWUiO3M6MTE6IlN1cGVyIEFkbWluIjtzOjU6InN5c0lkIjtpOjE7fQ==', 1765906928),
('pFTZ8R6rQGdovjEVKvo75mWwLkVgpXLwwvbKlFe4', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiOHJHWW1jSml6aWhheTZIR3ZhWTN3NG5Va0xrUGpPMXNSVzdMRmlNRCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1765898890);

-- --------------------------------------------------------

--
-- Table structure for table `sitesettings`
--

CREATE TABLE `sitesettings` (
  `sId` bigint(20) UNSIGNED NOT NULL,
  `sitename` varchar(255) DEFAULT NULL,
  `siteurl` varchar(255) DEFAULT NULL,
  `apidocumentation` text DEFAULT NULL,
  `referalupgradebonus` decimal(10,2) NOT NULL DEFAULT 0.00,
  `referalairtimebonus` decimal(10,2) NOT NULL DEFAULT 0.00,
  `referaldatabonus` decimal(10,2) NOT NULL DEFAULT 0.00,
  `referalwalletbonus` decimal(10,2) NOT NULL DEFAULT 0.00,
  `referalcablebonus` decimal(10,2) NOT NULL DEFAULT 0.00,
  `referalexambonus` decimal(10,2) NOT NULL DEFAULT 0.00,
  `referalmeterbonus` decimal(10,2) NOT NULL DEFAULT 0.00,
  `wallettowalletcharges` decimal(10,2) NOT NULL DEFAULT 0.00,
  `agentupgrade` decimal(10,2) NOT NULL DEFAULT 0.00,
  `vendorupgrade` decimal(10,2) NOT NULL DEFAULT 0.00,
  `accountname` varchar(255) DEFAULT NULL,
  `accountno` varchar(255) DEFAULT NULL,
  `bankname` varchar(255) DEFAULT NULL,
  `electricitycharges` decimal(10,2) NOT NULL DEFAULT 0.00,
  `airtimemin` int(11) NOT NULL DEFAULT 50,
  `airtimemax` int(11) NOT NULL DEFAULT 10000,
  `sitecolor` varchar(255) NOT NULL DEFAULT 'blue',
  `loginstyle` varchar(255) NOT NULL DEFAULT 'default',
  `homestyle` varchar(255) NOT NULL DEFAULT 'default'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subscribers`
--

CREATE TABLE `subscribers` (
  `sId` bigint(20) UNSIGNED NOT NULL,
  `sApiKey` varchar(200) NOT NULL,
  `sFname` varchar(50) NOT NULL,
  `sLname` varchar(50) NOT NULL,
  `sEmail` varchar(50) DEFAULT NULL,
  `sPhone` varchar(20) NOT NULL,
  `sPass` varchar(150) NOT NULL,
  `sState` varchar(50) NOT NULL,
  `sPin` int(11) NOT NULL DEFAULT 1234,
  `sPinStatus` tinyint(4) NOT NULL DEFAULT 0,
  `sType` tinyint(4) NOT NULL DEFAULT 1,
  `sWallet` double NOT NULL DEFAULT 0,
  `sRefWallet` double NOT NULL DEFAULT 0,
  `sBankNo` varchar(20) DEFAULT NULL,
  `sRolexBank` varchar(20) DEFAULT NULL,
  `sSterlingBank` varchar(20) DEFAULT NULL,
  `sFidelityBank` varchar(20) DEFAULT NULL,
  `sBankName` varchar(30) DEFAULT NULL,
  `sRegStatus` tinyint(4) NOT NULL DEFAULT 3,
  `sVerCode` smallint(6) NOT NULL DEFAULT 0,
  `sRegDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `sLastActivity` timestamp NULL DEFAULT NULL,
  `sReferal` varchar(15) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sysusers`
--

CREATE TABLE `sysusers` (
  `sysId` bigint(20) UNSIGNED NOT NULL,
  `sysName` varchar(100) NOT NULL,
  `sysRole` tinyint(4) NOT NULL,
  `sysUsername` varchar(100) NOT NULL,
  `sysToken` varchar(255) NOT NULL,
  `sysStatus` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sysusers`
--

INSERT INTO `sysusers` (`sysId`, `sysName`, `sysRole`, `sysUsername`, `sysToken`, `sysStatus`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 1, 'walshak1999@gmail.com', '$2y$12$gdUou7mpKZqEGdxkhFwAQ.QdpSpzEHMw/FYIMZDPBerJwVpI0b9wm', 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `tId` bigint(20) UNSIGNED NOT NULL,
  `sId` int(11) NOT NULL,
  `transref` varchar(255) NOT NULL,
  `servicename` varchar(100) NOT NULL,
  `servicedesc` varchar(255) NOT NULL,
  `amount` varchar(100) NOT NULL,
  `service_name` varchar(50) NOT NULL,
  `service_description` text NOT NULL,
  `old_balance` decimal(15,2) NOT NULL,
  `new_balance` decimal(15,2) NOT NULL,
  `api_response` text DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0,
  `oldbal` varchar(100) NOT NULL,
  `newbal` varchar(100) NOT NULL,
  `profit` double NOT NULL DEFAULT 0,
  `date` datetime NOT NULL DEFAULT '2025-12-05 06:07:01'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `userlogin`
--

CREATE TABLE `userlogin` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `state` varchar(50) DEFAULT NULL,
  `user_type` tinyint(4) NOT NULL DEFAULT 1,
  `reg_status` tinyint(4) NOT NULL DEFAULT 3,
  `ver_code` smallint(6) NOT NULL DEFAULT 0,
  `transaction_pin` int(11) NOT NULL DEFAULT 1234,
  `pin_status` tinyint(4) NOT NULL DEFAULT 0,
  `wallet_balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `referral_wallet` decimal(15,2) NOT NULL DEFAULT 0.00,
  `bank_account` varchar(20) DEFAULT NULL,
  `rolex_bank` varchar(20) DEFAULT NULL,
  `sterling_bank` varchar(20) DEFAULT NULL,
  `fidelity_bank` varchar(20) DEFAULT NULL,
  `bank_name` varchar(30) DEFAULT NULL,
  `api_key` varchar(200) DEFAULT NULL,
  `referral_code` varchar(15) DEFAULT NULL,
  `referred_by` int(11) DEFAULT NULL,
  `last_activity` timestamp NULL DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `virtual_accounts` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`virtual_accounts`)),
  `monnify_reference` varchar(255) DEFAULT NULL,
  `monnify_account_number` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `state`, `user_type`, `reg_status`, `ver_code`, `transaction_pin`, `pin_status`, `wallet_balance`, `referral_wallet`, `bank_account`, `rolex_bank`, `sterling_bank`, `fidelity_bank`, `bank_name`, `api_key`, `referral_code`, `referred_by`, `last_activity`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `virtual_accounts`, `monnify_reference`, `monnify_account_number`) VALUES
(1, 'Walshak Apollos', 'mikenenshimwa@gmail.com', '08133051779', 'Niger', 1, 0, 0, 1878, 1, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, 'Zim7d4IPXE36bBCqnTScpHVDQyfG1LOwv2ReYJ0lN5g8h9FoUx176491495908133051779', NULL, NULL, '2025-12-16 14:28:41', NULL, '$2y$12$1cEBfULXodE7F8GmzXhiWuLssfaaHktPBH.OFWeC55WwVjd74Vqki', NULL, '2025-12-05 05:09:19', '2025-12-16 14:28:41', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `uservisits`
--

CREATE TABLE `uservisits` (
  `vId` bigint(20) UNSIGNED NOT NULL,
  `ipAddress` varchar(50) NOT NULL,
  `userAgent` varchar(255) NOT NULL,
  `visitDate` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wallet_providers`
--

CREATE TABLE `wallet_providers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `provider_name` varchar(50) NOT NULL,
  `api_key` varchar(255) NOT NULL,
  `api_url` varchar(255) NOT NULL,
  `balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `priority` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `webhook_logs`
--

CREATE TABLE `webhook_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `provider` varchar(50) NOT NULL,
  `event_type` varchar(50) NOT NULL,
  `payload` text NOT NULL,
  `response` text DEFAULT NULL,
  `status` varchar(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `airtimepinprice`
--
ALTER TABLE `airtimepinprice`
  ADD PRIMARY KEY (`aId`);

--
-- Indexes for table `airtimes`
--
ALTER TABLE `airtimes`
  ADD PRIMARY KEY (`aId`),
  ADD KEY `airtimes_nid_foreign` (`nId`);

--
-- Indexes for table `alphatopupprice`
--
ALTER TABLE `alphatopupprice`
  ADD PRIMARY KEY (`alphaId`);

--
-- Indexes for table `apilinks`
--
ALTER TABLE `apilinks`
  ADD PRIMARY KEY (`aId`),
  ADD KEY `apilinks_type_is_active_index` (`type`,`is_active`),
  ADD KEY `apilinks_priority_success_rate_index` (`priority`,`success_rate`);

--
-- Indexes for table `api_configs`
--
ALTER TABLE `api_configs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `api_configs_config_key_unique` (`config_key`);

--
-- Indexes for table `api_configurations`
--
ALTER TABLE `api_configurations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `api_configurations_config_key_unique` (`config_key`),
  ADD KEY `api_configurations_service_type_network_provider_type_index` (`service_type`,`network`,`provider_type`),
  ADD KEY `api_configurations_config_key_index` (`config_key`);

--
-- Indexes for table `bulk_sms`
--
ALTER TABLE `bulk_sms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cableid`
--
ALTER TABLE `cableid`
  ADD PRIMARY KEY (`cId`);

--
-- Indexes for table `cable_ids`
--
ALTER TABLE `cable_ids`
  ADD PRIMARY KEY (`cId`);

--
-- Indexes for table `cable_plans`
--
ALTER TABLE `cable_plans`
  ADD PRIMARY KEY (`cpId`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `configurations`
--
ALTER TABLE `configurations`
  ADD PRIMARY KEY (`cId`),
  ADD UNIQUE KEY `configurations_config_key_unique` (`config_key`),
  ADD KEY `configurations_config_key_index` (`config_key`);

--
-- Indexes for table `contact`
--
ALTER TABLE `contact`
  ADD PRIMARY KEY (`cId`);

--
-- Indexes for table `datapins`
--
ALTER TABLE `datapins`
  ADD PRIMARY KEY (`dId`);

--
-- Indexes for table `datatokens`
--
ALTER TABLE `datatokens`
  ADD PRIMARY KEY (`dId`);

--
-- Indexes for table `data_plans`
--
ALTER TABLE `data_plans`
  ADD PRIMARY KEY (`dId`),
  ADD UNIQUE KEY `data_plans_dplanid_unique` (`dPlanId`),
  ADD KEY `data_plans_nid_foreign` (`nId`);

--
-- Indexes for table `electricity`
--
ALTER TABLE `electricity`
  ADD PRIMARY KEY (`eId`);

--
-- Indexes for table `electricityid`
--
ALTER TABLE `electricityid`
  ADD PRIMARY KEY (`eId`);

--
-- Indexes for table `examid`
--
ALTER TABLE `examid`
  ADD PRIMARY KEY (`eId`);

--
-- Indexes for table `exampin`
--
ALTER TABLE `exampin`
  ADD PRIMARY KEY (`eId`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `feature_toggles`
--
ALTER TABLE `feature_toggles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `feature_toggles_feature_key_unique` (`feature_key`),
  ADD KEY `feature_toggles_feature_key_is_enabled_index` (`feature_key`,`is_enabled`),
  ADD KEY `feature_toggles_environment_is_enabled_index` (`environment`,`is_enabled`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kyc_verification`
--
ALTER TABLE `kyc_verification`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `message_system`
--
ALTER TABLE `message_system`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `network_ids`
--
ALTER TABLE `network_ids`
  ADD PRIMARY KEY (`nId`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`nId`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `profit_tracking`
--
ALTER TABLE `profit_tracking`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `profit_tracking_transaction_ref_unique` (`transaction_ref`);

--
-- Indexes for table `rechargepins`
--
ALTER TABLE `rechargepins`
  ADD PRIMARY KEY (`rId`);

--
-- Indexes for table `rechargetokens`
--
ALTER TABLE `rechargetokens`
  ADD PRIMARY KEY (`rId`);

--
-- Indexes for table `recharge_pin_history`
--
ALTER TABLE `recharge_pin_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `recharge_pin_history_user_id_created_at_index` (`user_id`,`created_at`),
  ADD KEY `recharge_pin_history_reference_index` (`reference`);

--
-- Indexes for table `referral_bonuses`
--
ALTER TABLE `referral_bonuses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `service_sync_status`
--
ALTER TABLE `service_sync_status`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `service_sync_status_service_type_unique` (`service_type`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `sitesettings`
--
ALTER TABLE `sitesettings`
  ADD PRIMARY KEY (`sId`);

--
-- Indexes for table `subscribers`
--
ALTER TABLE `subscribers`
  ADD PRIMARY KEY (`sId`),
  ADD UNIQUE KEY `subscribers_sphone_unique` (`sPhone`),
  ADD KEY `subscribers_sphone_index` (`sPhone`),
  ADD KEY `subscribers_semail_index` (`sEmail`);

--
-- Indexes for table `sysusers`
--
ALTER TABLE `sysusers`
  ADD PRIMARY KEY (`sysId`),
  ADD UNIQUE KEY `sysusers_sysusername_unique` (`sysUsername`),
  ADD KEY `sysusers_sysusername_index` (`sysUsername`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`tId`),
  ADD KEY `transactions_sid_index` (`sId`),
  ADD KEY `transactions_transref_index` (`transref`),
  ADD KEY `transactions_status_index` (`status`),
  ADD KEY `transactions_date_index` (`date`);

--
-- Indexes for table `userlogin`
--
ALTER TABLE `userlogin`
  ADD PRIMARY KEY (`id`),
  ADD KEY `userlogin_user_index` (`user`),
  ADD KEY `userlogin_token_index` (`token`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_phone_unique` (`phone`),
  ADD KEY `users_phone_index` (`phone`),
  ADD KEY `users_user_type_index` (`user_type`),
  ADD KEY `users_referral_code_index` (`referral_code`),
  ADD KEY `users_api_key_index` (`api_key`),
  ADD KEY `users_monnify_account_number_index` (`monnify_account_number`);

--
-- Indexes for table `uservisits`
--
ALTER TABLE `uservisits`
  ADD PRIMARY KEY (`vId`);

--
-- Indexes for table `wallet_providers`
--
ALTER TABLE `wallet_providers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `webhook_logs`
--
ALTER TABLE `webhook_logs`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `airtimepinprice`
--
ALTER TABLE `airtimepinprice`
  MODIFY `aId` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `airtimes`
--
ALTER TABLE `airtimes`
  MODIFY `aId` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `alphatopupprice`
--
ALTER TABLE `alphatopupprice`
  MODIFY `alphaId` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `apilinks`
--
ALTER TABLE `apilinks`
  MODIFY `aId` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `api_configs`
--
ALTER TABLE `api_configs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `api_configurations`
--
ALTER TABLE `api_configurations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `bulk_sms`
--
ALTER TABLE `bulk_sms`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cableid`
--
ALTER TABLE `cableid`
  MODIFY `cId` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cable_ids`
--
ALTER TABLE `cable_ids`
  MODIFY `cId` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `cable_plans`
--
ALTER TABLE `cable_plans`
  MODIFY `cpId` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `configurations`
--
ALTER TABLE `configurations`
  MODIFY `cId` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `contact`
--
ALTER TABLE `contact`
  MODIFY `cId` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `datapins`
--
ALTER TABLE `datapins`
  MODIFY `dId` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `datatokens`
--
ALTER TABLE `datatokens`
  MODIFY `dId` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `data_plans`
--
ALTER TABLE `data_plans`
  MODIFY `dId` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=193;

--
-- AUTO_INCREMENT for table `electricity`
--
ALTER TABLE `electricity`
  MODIFY `eId` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `electricityid`
--
ALTER TABLE `electricityid`
  MODIFY `eId` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `examid`
--
ALTER TABLE `examid`
  MODIFY `eId` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exampin`
--
ALTER TABLE `exampin`
  MODIFY `eId` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `feature_toggles`
--
ALTER TABLE `feature_toggles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kyc_verification`
--
ALTER TABLE `kyc_verification`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `message_system`
--
ALTER TABLE `message_system`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `nId` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `profit_tracking`
--
ALTER TABLE `profit_tracking`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rechargepins`
--
ALTER TABLE `rechargepins`
  MODIFY `rId` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rechargetokens`
--
ALTER TABLE `rechargetokens`
  MODIFY `rId` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `recharge_pin_history`
--
ALTER TABLE `recharge_pin_history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `referral_bonuses`
--
ALTER TABLE `referral_bonuses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `service_sync_status`
--
ALTER TABLE `service_sync_status`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `sitesettings`
--
ALTER TABLE `sitesettings`
  MODIFY `sId` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subscribers`
--
ALTER TABLE `subscribers`
  MODIFY `sId` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sysusers`
--
ALTER TABLE `sysusers`
  MODIFY `sysId` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `tId` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `userlogin`
--
ALTER TABLE `userlogin`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `uservisits`
--
ALTER TABLE `uservisits`
  MODIFY `vId` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wallet_providers`
--
ALTER TABLE `wallet_providers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `webhook_logs`
--
ALTER TABLE `webhook_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `airtimes`
--
ALTER TABLE `airtimes`
  ADD CONSTRAINT `airtimes_nid_foreign` FOREIGN KEY (`nId`) REFERENCES `network_ids` (`nId`) ON DELETE CASCADE;

--
-- Constraints for table `data_plans`
--
ALTER TABLE `data_plans`
  ADD CONSTRAINT `data_plans_nid_foreign` FOREIGN KEY (`nId`) REFERENCES `network_ids` (`nId`) ON DELETE CASCADE;

--
-- Constraints for table `recharge_pin_history`
--
ALTER TABLE `recharge_pin_history`
  ADD CONSTRAINT `recharge_pin_history_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `subscribers` (`sId`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
