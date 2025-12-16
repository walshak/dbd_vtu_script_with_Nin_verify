-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 05, 2025 at 07:20 AM
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
  `aUserDiscount` decimal(5,2) NOT NULL DEFAULT 99.00,
  `aAgentDiscount` decimal(5,2) NOT NULL DEFAULT 98.00,
  `aVendorDiscount` decimal(5,2) NOT NULL DEFAULT 97.00
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
(1, '1', 'dstv', 'On', '2025-12-05 05:07:08', '2025-12-05 05:07:08'),
(2, '2', 'gotv', 'On', '2025-12-05 05:07:08', '2025-12-05 05:07:08'),
(3, '3', 'startimes', 'On', '2025-12-05 05:07:08', '2025-12-05 05:07:08');

-- --------------------------------------------------------

--
-- Table structure for table `cable_plans`
--

CREATE TABLE `cable_plans` (
  `cpId` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` varchar(255) NOT NULL,
  `userprice` varchar(255) NOT NULL,
  `agentprice` varchar(255) NOT NULL,
  `vendorprice` varchar(255) NOT NULL,
  `planid` varchar(255) NOT NULL,
  `type` varchar(255) DEFAULT NULL,
  `cableprovider` tinyint(4) NOT NULL,
  `day` varchar(255) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cable_plans`
--

INSERT INTO `cable_plans` (`cpId`, `name`, `price`, `userprice`, `agentprice`, `vendorprice`, `planid`, `type`, `cableprovider`, `day`, `status`, `created_at`, `updated_at`) VALUES
(1, 'DStv Padi', '2150', '2150', '2100', '2050', 'dstv-padi', NULL, 1, '30', 'active', '2025-12-05 05:07:08', '2025-12-05 05:07:08'),
(2, 'DStv Yanga', '2950', '2950', '2900', '2850', 'dstv-yanga', NULL, 1, '30', 'active', '2025-12-05 05:07:08', '2025-12-05 05:07:08'),
(3, 'DStv Confam', '5300', '5300', '5200', '5100', 'dstv-confam', NULL, 1, '30', 'active', '2025-12-05 05:07:08', '2025-12-05 05:07:08'),
(4, 'DStv Compact', '9000', '9000', '8900', '8800', 'dstv-compact', NULL, 1, '30', 'active', '2025-12-05 05:07:08', '2025-12-05 05:07:08'),
(5, 'DStv Compact Plus', '14250', '14250', '14100', '14000', 'dstv-compact-plus', NULL, 1, '30', 'active', '2025-12-05 05:07:08', '2025-12-05 05:07:08'),
(6, 'DStv Premium', '21000', '21000', '20800', '20600', 'dstv-premium', NULL, 1, '30', 'active', '2025-12-05 05:07:08', '2025-12-05 05:07:08'),
(7, 'GOtv Smallie', '900', '900', '880', '860', 'gotv-smallie', NULL, 2, '30', 'active', '2025-12-05 05:07:08', '2025-12-05 05:07:08'),
(8, 'GOtv Jinja', '1900', '1900', '1850', '1800', 'gotv-jinja', NULL, 2, '30', 'active', '2025-12-05 05:07:08', '2025-12-05 05:07:08'),
(9, 'GOtv Jolli', '2800', '2800', '2750', '2700', 'gotv-jolli', NULL, 2, '30', 'active', '2025-12-05 05:07:08', '2025-12-05 05:07:08'),
(10, 'GOtv Max', '4150', '4150', '4100', '4050', 'gotv-max', NULL, 2, '30', 'active', '2025-12-05 05:07:08', '2025-12-05 05:07:08'),
(11, 'GOtv Supa', '5500', '5500', '5400', '5300', 'gotv-supa', NULL, 2, '30', 'active', '2025-12-05 05:07:08', '2025-12-05 05:07:08'),
(12, 'Startimes Nova', '900', '900', '880', '860', 'startimes-nova', NULL, 3, '30', 'active', '2025-12-05 05:07:08', '2025-12-05 05:07:08'),
(13, 'Startimes Basic', '1850', '1850', '1800', '1750', 'startimes-basic', NULL, 3, '30', 'active', '2025-12-05 05:07:08', '2025-12-05 05:07:08'),
(14, 'Startimes Smart', '2480', '2480', '2400', '2350', 'startimes-smart', NULL, 3, '30', 'active', '2025-12-05 05:07:08', '2025-12-05 05:07:08'),
(15, 'Startimes Classic', '2750', '2750', '2700', '2650', 'startimes-classic', NULL, 3, '30', 'active', '2025-12-05 05:07:08', '2025-12-05 05:07:08'),
(16, 'Startimes Super', '4200', '4200', '4100', '4000', 'startimes-super', NULL, 3, '30', 'active', '2025-12-05 05:07:08', '2025-12-05 05:07:08');

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
('laravel-cache-config.alphaApi', 's:0:\"\";', 1764919065),
('laravel-cache-config.alphaProvider', 's:0:\"\";', 1764919065),
('laravel-cache-config.cableApi', 's:40:\"245141f6de9c0aa211b3a6baf1d1533c642caf24\";', 1764919030),
('laravel-cache-config.cableProvider', 's:35:\"https://uzobestgsm.com/api/cabletv/\";', 1764919030),
('laravel-cache-config.cableVerificationApi', 's:40:\"245141f6de9c0aa211b3a6baf1d1533c642caf24\";', 1764919030),
('laravel-cache-config.cableVerificationProvider', 's:45:\"https://uzobestgsm.com/api/validate-customer/\";', 1764919030),
('laravel-cache-config.dataPinApi', 's:0:\"\";', 1764919059),
('laravel-cache-config.dataPinProvider', 's:0:\"\";', 1764919059),
('laravel-cache-config.examApi', 's:40:\"245141f6de9c0aa211b3a6baf1d1533c642caf24\";', 1764919047),
('laravel-cache-config.logging', 'N;', 1764919146),
('laravel-cache-config.meterApi', 's:40:\"245141f6de9c0aa211b3a6baf1d1533c642caf24\";', 1764919038),
('laravel-cache-config.meterProvider', 's:39:\"https://uzobestgsm.com/api/electricity/\";', 1764919038),
('laravel-cache-config.meterVerificationApi', 's:40:\"245141f6de9c0aa211b3a6baf1d1533c642caf24\";', 1764919038),
('laravel-cache-config.meterVerificationProvider', 's:45:\"https://uzobestgsm.com/api/validate-customer/\";', 1764919038),
('laravel-cache-config.rechargePinApi', 's:0:\"\";', 1764919052),
('laravel-cache-config.rechargePinProvider', 's:0:\"\";', 1764919052),
('laravel-cache-health_check_airtime', 'a:8:{s:7:\"service\";s:7:\"airtime\";s:7:\"healthy\";b:0;s:6:\"status\";s:4:\"down\";s:7:\"message\";s:22:\"Service not configured\";s:13:\"response_time\";N;s:10:\"error_code\";s:12:\"CONFIG_ERROR\";s:7:\"details\";N;s:10:\"checked_at\";s:27:\"2025-12-05T06:19:40.609758Z\";}', 1764915585),
('laravel-cache-health_check_alpha_topup', 'a:8:{s:7:\"service\";s:11:\"alpha_topup\";s:7:\"healthy\";b:0;s:6:\"status\";s:4:\"down\";s:7:\"message\";s:151:\"Alpha topup health check failed: cURL error 3: URL rejected: Malformed input to a URL function (see https://curl.haxx.se/libcurl/c/libcurl-errors.html)\";s:13:\"response_time\";d:3434.99;s:10:\"error_code\";s:19:\"HEALTH_CHECK_FAILED\";s:7:\"details\";N;s:10:\"checked_at\";s:27:\"2025-12-05T06:19:39.802857Z\";}', 1764915584),
('laravel-cache-health_check_cable', 'a:8:{s:7:\"service\";s:5:\"cable\";s:7:\"healthy\";b:0;s:6:\"status\";s:4:\"down\";s:7:\"message\";s:28:\"Cable service not responding\";s:13:\"response_time\";d:5140.84;s:10:\"error_code\";s:12:\"SERVICE_DOWN\";s:7:\"details\";N;s:10:\"checked_at\";s:27:\"2025-12-05T06:19:46.944587Z\";}', 1764915591),
('laravel-cache-health_check_data', 'a:8:{s:7:\"service\";s:4:\"data\";s:7:\"healthy\";b:0;s:6:\"status\";s:4:\"down\";s:7:\"message\";s:22:\"Service not configured\";s:13:\"response_time\";N;s:10:\"error_code\";s:12:\"CONFIG_ERROR\";s:7:\"details\";N;s:10:\"checked_at\";s:27:\"2025-12-05T06:19:41.240663Z\";}', 1764915586),
('laravel-cache-health_check_data_pin', 'a:8:{s:7:\"service\";s:8:\"data_pin\";s:7:\"healthy\";b:0;s:6:\"status\";s:4:\"down\";s:7:\"message\";s:133:\"Pin service health check failed: cURL error 6: Could not resolve host: plans (see https://curl.haxx.se/libcurl/c/libcurl-errors.html)\";s:13:\"response_time\";d:5430.9;s:10:\"error_code\";s:19:\"HEALTH_CHECK_FAILED\";s:7:\"details\";N;s:10:\"checked_at\";s:27:\"2025-12-05T06:19:36.337511Z\";}', 1764915581),
('laravel-cache-health_check_electricity', 'a:8:{s:7:\"service\";s:11:\"electricity\";s:7:\"healthy\";b:0;s:6:\"status\";s:4:\"down\";s:7:\"message\";s:34:\"Electricity service not responding\";s:13:\"response_time\";d:4980.13;s:10:\"error_code\";s:12:\"SERVICE_DOWN\";s:7:\"details\";N;s:10:\"checked_at\";s:27:\"2025-12-05T06:19:52.189300Z\";}', 1764915597),
('laravel-cache-health_check_exam', 'a:8:{s:7:\"service\";s:4:\"exam\";s:7:\"healthy\";b:0;s:6:\"status\";s:4:\"down\";s:7:\"message\";s:26:\"Pin service not responding\";s:13:\"response_time\";d:4117.74;s:10:\"error_code\";s:12:\"SERVICE_DOWN\";s:7:\"details\";N;s:10:\"checked_at\";s:27:\"2025-12-05T06:19:56.750151Z\";}', 1764915601),
('laravel-cache-health_check_recharge_pin', 'a:8:{s:7:\"service\";s:12:\"recharge_pin\";s:7:\"healthy\";b:0;s:6:\"status\";s:4:\"down\";s:7:\"message\";s:133:\"Pin service health check failed: cURL error 6: Could not resolve host: plans (see https://curl.haxx.se/libcurl/c/libcurl-errors.html)\";s:13:\"response_time\";d:5462.29;s:10:\"error_code\";s:19:\"HEALTH_CHECK_FAILED\";s:7:\"details\";N;s:10:\"checked_at\";s:27:\"2025-12-05T06:20:02.357333Z\";}', 1764915607),
('laravel-cache-monitoring_dashboard', 'a:5:{s:12:\"generated_at\";s:27:\"2025-12-05T06:19:08.816866Z\";s:14:\"overall_status\";s:8:\"degraded\";s:15:\"services_status\";a:8:{s:7:\"airtime\";a:8:{s:7:\"service\";s:7:\"airtime\";s:7:\"healthy\";b:0;s:6:\"status\";s:4:\"down\";s:7:\"message\";s:22:\"Service not configured\";s:13:\"response_time\";N;s:10:\"error_code\";s:12:\"CONFIG_ERROR\";s:7:\"details\";N;s:10:\"checked_at\";s:27:\"2025-12-05T06:19:08.995197Z\";}s:4:\"data\";a:8:{s:7:\"service\";s:4:\"data\";s:7:\"healthy\";b:0;s:6:\"status\";s:4:\"down\";s:7:\"message\";s:22:\"Service not configured\";s:13:\"response_time\";N;s:10:\"error_code\";s:12:\"CONFIG_ERROR\";s:7:\"details\";N;s:10:\"checked_at\";s:27:\"2025-12-05T06:19:09.285388Z\";}s:5:\"cable\";a:8:{s:7:\"service\";s:5:\"cable\";s:7:\"healthy\";b:0;s:6:\"status\";s:4:\"down\";s:7:\"message\";s:28:\"Cable service not responding\";s:13:\"response_time\";d:4907.67;s:10:\"error_code\";s:12:\"SERVICE_DOWN\";s:7:\"details\";N;s:10:\"checked_at\";s:27:\"2025-12-05T06:19:14.367090Z\";}s:11:\"electricity\";a:8:{s:7:\"service\";s:11:\"electricity\";s:7:\"healthy\";b:0;s:6:\"status\";s:4:\"down\";s:7:\"message\";s:34:\"Electricity service not responding\";s:13:\"response_time\";d:4501.56;s:10:\"error_code\";s:12:\"SERVICE_DOWN\";s:7:\"details\";N;s:10:\"checked_at\";s:27:\"2025-12-05T06:19:19.661907Z\";}s:4:\"exam\";a:8:{s:7:\"service\";s:4:\"exam\";s:7:\"healthy\";b:0;s:6:\"status\";s:4:\"down\";s:7:\"message\";s:26:\"Pin service not responding\";s:13:\"response_time\";d:4907.48;s:10:\"error_code\";s:12:\"SERVICE_DOWN\";s:7:\"details\";N;s:10:\"checked_at\";s:27:\"2025-12-05T06:19:24.906894Z\";}s:12:\"recharge_pin\";a:8:{s:7:\"service\";s:12:\"recharge_pin\";s:7:\"healthy\";b:0;s:6:\"status\";s:4:\"down\";s:7:\"message\";s:133:\"Pin service health check failed: cURL error 6: Could not resolve host: plans (see https://curl.haxx.se/libcurl/c/libcurl-errors.html)\";s:13:\"response_time\";d:5472.76;s:10:\"error_code\";s:19:\"HEALTH_CHECK_FAILED\";s:7:\"details\";N;s:10:\"checked_at\";s:27:\"2025-12-05T06:19:30.612632Z\";}s:8:\"data_pin\";a:8:{s:7:\"service\";s:8:\"data_pin\";s:7:\"healthy\";b:0;s:6:\"status\";s:4:\"down\";s:7:\"message\";s:133:\"Pin service health check failed: cURL error 6: Could not resolve host: plans (see https://curl.haxx.se/libcurl/c/libcurl-errors.html)\";s:13:\"response_time\";d:5430.9;s:10:\"error_code\";s:19:\"HEALTH_CHECK_FAILED\";s:7:\"details\";N;s:10:\"checked_at\";s:27:\"2025-12-05T06:19:36.337511Z\";}s:11:\"alpha_topup\";a:8:{s:7:\"service\";s:11:\"alpha_topup\";s:7:\"healthy\";b:0;s:6:\"status\";s:4:\"down\";s:7:\"message\";s:151:\"Alpha topup health check failed: cURL error 3: URL rejected: Malformed input to a URL function (see https://curl.haxx.se/libcurl/c/libcurl-errors.html)\";s:13:\"response_time\";d:3434.99;s:10:\"error_code\";s:19:\"HEALTH_CHECK_FAILED\";s:7:\"details\";N;s:10:\"checked_at\";s:27:\"2025-12-05T06:19:39.802857Z\";}}s:14:\"system_metrics\";a:0:{}s:6:\"alerts\";a:0:{}}', 1764915590),
('laravel-cache-service.airtime', 'a:0:{}', 1764919030),
('laravel-cache-service.alpha_topup', 'a:2:{s:7:\"api_key\";s:0:\"\";s:8:\"provider\";s:0:\"\";}', 1764919065),
('laravel-cache-service.cable', 'a:7:{s:16:\"verification_api\";s:40:\"245141f6de9c0aa211b3a6baf1d1533c642caf24\";s:21:\"verification_provider\";s:45:\"https://uzobestgsm.com/api/validate-customer/\";s:7:\"api_key\";s:40:\"245141f6de9c0aa211b3a6baf1d1533c642caf24\";s:8:\"provider\";s:35:\"https://uzobestgsm.com/api/cabletv/\";s:13:\"provider_name\";s:7:\"Uzobest\";s:9:\"auth_type\";s:6:\"header\";s:11:\"auth_params\";a:2:{s:11:\"header_name\";s:13:\"Authorization\";s:13:\"header_prefix\";s:6:\"Token \";}}', 1764919030),
('laravel-cache-service.data', 'a:0:{}', 1764919030),
('laravel-cache-service.data_pin', 'a:3:{s:7:\"api_key\";s:0:\"\";s:8:\"provider\";s:0:\"\";s:9:\"auth_type\";s:5:\"Basic\";}', 1764919059),
('laravel-cache-service.electricity', 'a:7:{s:16:\"verification_api\";s:40:\"245141f6de9c0aa211b3a6baf1d1533c642caf24\";s:21:\"verification_provider\";s:45:\"https://uzobestgsm.com/api/validate-customer/\";s:7:\"api_key\";s:40:\"245141f6de9c0aa211b3a6baf1d1533c642caf24\";s:8:\"provider\";s:39:\"https://uzobestgsm.com/api/electricity/\";s:13:\"provider_name\";s:7:\"Uzobest\";s:9:\"auth_type\";s:6:\"header\";s:11:\"auth_params\";a:2:{s:11:\"header_name\";s:13:\"Authorization\";s:13:\"header_prefix\";s:6:\"Token \";}}', 1764919038),
('laravel-cache-service.exam', 'a:6:{s:7:\"api_key\";s:40:\"245141f6de9c0aa211b3a6baf1d1533c642caf24\";s:8:\"provider\";s:31:\"https://topupmate.com/api/exam/\";s:9:\"auth_type\";s:5:\"token\";s:8:\"user_url\";N;s:13:\"provider_name\";s:9:\"Topupmate\";s:11:\"auth_params\";N;}', 1764919047),
('laravel-cache-service.recharge_pin', 'a:3:{s:7:\"api_key\";s:0:\"\";s:8:\"provider\";s:0:\"\";s:9:\"auth_type\";s:5:\"Basic\";}', 1764919052);

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
  `dPlan` varchar(100) NOT NULL,
  `dAmount` varchar(50) NOT NULL,
  `dValidity` varchar(50) NOT NULL,
  `userPrice` int(11) NOT NULL,
  `agentPrice` int(11) NOT NULL,
  `apiPrice` int(11) NOT NULL,
  `dGroup` varchar(50) NOT NULL DEFAULT 'SME'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `data_plans`
--

INSERT INTO `data_plans` (`dId`, `dPlanId`, `nId`, `dPlan`, `dAmount`, `dValidity`, `userPrice`, `agentPrice`, `apiPrice`, `dGroup`) VALUES
(1, 'mtn-sme-500mb', 1, 'MTN 500MB', '500MB', '30 Days', 135, 125, 120, 'SME'),
(2, 'mtn-sme-1gb', 1, 'MTN 1GB', '1GB', '30 Days', 270, 250, 240, 'SME'),
(3, 'mtn-sme-2gb', 1, 'MTN 2GB', '2GB', '30 Days', 540, 500, 480, 'SME'),
(4, 'mtn-sme-5gb', 1, 'MTN 5GB', '5GB', '30 Days', 1350, 1250, 1200, 'SME'),
(5, 'mtn-sme-10gb', 1, 'MTN 10GB', '10GB', '30 Days', 2700, 2500, 2400, 'SME'),
(6, 'mtn-gift-500mb', 1, 'MTN 500MB Gifting', '500MB', '30 Days', 145, 135, 130, 'Gifting'),
(7, 'mtn-gift-1gb', 1, 'MTN 1GB Gifting', '1GB', '30 Days', 290, 270, 260, 'Gifting'),
(8, 'mtn-gift-2gb', 1, 'MTN 2GB Gifting', '2GB', '30 Days', 580, 540, 520, 'Gifting'),
(9, 'airtel-sme-500mb', 2, 'Airtel 500MB', '500MB', '30 Days', 140, 130, 125, 'SME'),
(10, 'airtel-sme-1gb', 2, 'Airtel 1GB', '1GB', '30 Days', 280, 260, 250, 'SME'),
(11, 'airtel-sme-2gb', 2, 'Airtel 2GB', '2GB', '30 Days', 560, 520, 500, 'SME'),
(12, 'airtel-sme-5gb', 2, 'Airtel 5GB', '5GB', '30 Days', 1400, 1300, 1250, 'SME'),
(13, 'glo-sme-500mb', 3, 'Glo 500MB', '500MB', '30 Days', 130, 120, 115, 'SME'),
(14, 'glo-sme-1gb', 3, 'Glo 1GB', '1GB', '30 Days', 260, 240, 230, 'SME'),
(15, 'glo-sme-2gb', 3, 'Glo 2GB', '2GB', '30 Days', 520, 480, 460, 'SME'),
(16, 'glo-sme-5gb', 3, 'Glo 5GB', '5GB', '30 Days', 1300, 1200, 1150, 'SME'),
(17, '9mobile-sme-500mb', 4, '9mobile 500MB', '500MB', '30 Days', 135, 125, 120, 'SME'),
(18, '9mobile-sme-1gb', 4, '9mobile 1GB', '1GB', '30 Days', 270, 250, 240, 'SME'),
(19, '9mobile-sme-2gb', 4, '9mobile 2GB', '2GB', '30 Days', 540, 500, 480, 'SME');

-- --------------------------------------------------------

--
-- Table structure for table `electricity`
--

CREATE TABLE `electricity` (
  `eId` bigint(20) UNSIGNED NOT NULL,
  `ePlan` varchar(255) NOT NULL,
  `eProviderId` varchar(255) DEFAULT NULL,
  `ePrice` decimal(10,2) NOT NULL DEFAULT 0.00,
  `eBuyingPrice` decimal(10,2) NOT NULL DEFAULT 0.00,
  `eStatus` tinyint(4) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(33, '2025_11_30_153213_add_monnify_account_number_to_users_table', 1);

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
('h9XJODlW7Gumh6ICUSHcR2LdxwLWVsimRFXTAuU0', 8133051779, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiQkZTMHJoRkl4elE4ZDlvdnJNQXlpOXdoV1hKbXBBZ2F5T05QUWl2SSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9kYXNoYm9hcmQiO3M6NToicm91dGUiO3M6OToiZGFzaGJvYXJkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO3M6MTE6IjA4MTMzMDUxNzc5Ijt9', 1764914965),
('phX5Z3iG4N4eDRGOokLMNIs40zNchFAhlShF4fQs', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'YTo4OntzOjY6Il90b2tlbiI7czo0MDoiTGtuY2U1Z1l3bFpkanJOQkYzVUpldzBIN1hUeWFEYTc4cHdCeVFsVSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9hZG1pbi9kYXNoYm9hcmQiO3M6NToicm91dGUiO3M6MTU6ImFkbWluLmRhc2hib2FyZCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTI6ImxvZ2luX2FkbWluXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO3M6MjE6IndhbHNoYWsxOTk5QGdtYWlsLmNvbSI7czo3OiJzeXNVc2VyIjtzOjIxOiJ3YWxzaGFrMTk5OUBnbWFpbC5jb20iO3M6Nzoic3lzUm9sZSI7aToxO3M6Nzoic3lzTmFtZSI7czoxMToiU3VwZXIgQWRtaW4iO3M6NToic3lzSWQiO2k6MTt9', 1764915227);

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
(1, 'Walshak Apollos', 'mikenenshimwa@gmail.com', '08133051779', 'Niger', 1, 0, 0, 1878, 1, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, 'Zim7d4IPXE36bBCqnTScpHVDQyfG1LOwv2ReYJ0lN5g8h9FoUx176491495908133051779', NULL, NULL, '2025-12-05 05:09:19', NULL, '$2y$12$1cEBfULXodE7F8GmzXhiWuLssfaaHktPBH.OFWeC55WwVjd74Vqki', NULL, '2025-12-05 05:09:19', '2025-12-05 05:09:19', NULL, NULL, NULL);

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
  MODIFY `dId` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `electricity`
--
ALTER TABLE `electricity`
  MODIFY `eId` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

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
