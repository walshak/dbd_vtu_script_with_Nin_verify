-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 18, 2026 at 03:40 PM
-- Server version: 10.11.15-MariaDB
-- PHP Version: 8.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `vastlead_main`
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
(65, 'Uzobest', 'https://uzobestgsm.com/api/topup/', 'Airtime', 1, 1, 'header', '{\"token\":\"57adc15fd67d8fec5bcf7b268ca01bde460bedcd\",\"header_name\":\"Authorization\",\"header_prefix\":\"Token \"}', 100.00, 0, NULL, '2025-12-05 05:07:05', '2025-12-19 11:49:55'),
(66, 'Uzobest', 'https://uzobestgsm.com/api/data/', 'Data', 1, 1, 'header', '{\"token\":\"57adc15fd67d8fec5bcf7b268ca01bde460bedcd\",\"header_name\":\"Authorization\",\"header_prefix\":\"Token \"}', 100.00, 0, NULL, '2025-12-05 05:07:05', '2025-12-19 11:49:55'),
(67, 'Uzobest', 'https://uzobestgsm.com/api/cabletv/', 'Cable', 1, 1, 'header', '{\"token\":\"57adc15fd67d8fec5bcf7b268ca01bde460bedcd\",\"header_name\":\"Authorization\",\"header_prefix\":\"Token \"}', 100.00, 0, NULL, '2025-12-05 05:07:05', '2025-12-19 11:49:55'),
(68, 'Uzobest', 'https://uzobestgsm.com/api/billpayment/', 'Electricity', 1, 1, 'header', '{\"token\":\"57adc15fd67d8fec5bcf7b268ca01bde460bedcd\",\"header_name\":\"Authorization\",\"header_prefix\":\"Token \"}', 100.00, 0, NULL, '2025-12-05 05:07:05', '2025-12-19 11:49:55'),
(69, 'Uzobest', 'https://uzobestgsm.com/api/exam/', 'Exam', 1, 1, 'header', '{\"header_name\":\"Authorization\",\"header_prefix\":\"Token \"}', 100.00, 0, NULL, '2025-12-05 05:07:05', '2025-12-05 05:07:05'),
(70, 'Uzobest', 'https://uzobestgsm.com/api/validate-customer/', 'CableVer', 1, 1, 'header', '{\"header_name\":\"Authorization\",\"header_prefix\":\"Token \"}', 100.00, 0, NULL, '2025-12-05 05:07:05', '2025-12-05 05:07:05'),
(71, 'Uzobest', 'https://uzobestgsm.com/api/validate-customer/', 'ElectricityVer', 1, 1, 'header', '{\"header_name\":\"Authorization\",\"header_prefix\":\"Token \"}', 100.00, 0, NULL, '2025-12-05 05:07:05', '2025-12-05 05:07:05'),
(72, 'Uzobest', 'https://uzobestgsm.com/api', 'primary', 1, 1, 'header', '{\"token\":\"57adc15fd67d8fec5bcf7b268ca01bde460bedcd\",\"header_name\":\"Authorization\",\"header_prefix\":\"Token \"}', 100.00, 0, NULL, '2025-12-19 11:49:55', '2025-12-19 11:49:55');

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
('laravel-cache-service.recharge_pin', 'a:3:{s:7:\"api_key\";s:0:\"\";s:8:\"provider\";s:0:\"\";s:9:\"auth_type\";s:5:\"Basic\";}', 1765903884),
('vastlead-cache-api_performance_metrics', 'a:6:{s:19:\"service_performance\";a:0:{}s:13:\"recent_errors\";a:0:{}s:13:\"hourly_trends\";a:0:{}s:20:\"overall_health_score\";i:95;s:16:\"slowest_services\";a:0:{}s:16:\"fastest_services\";a:0:{}}', 1768591540),
('vastlead-cache-config.alphaApi', 's:0:\"\";', 1768594840),
('vastlead-cache-config.alphaProvider', 's:0:\"\";', 1768594840),
('vastlead-cache-config.cableApi', 's:40:\"245141f6de9c0aa211b3a6baf1d1533c642caf24\";', 1768594840),
('vastlead-cache-config.cableProvider', 's:35:\"https://uzobestgsm.com/api/cabletv/\";', 1768594840),
('vastlead-cache-config.cableVerificationApi', 's:40:\"245141f6de9c0aa211b3a6baf1d1533c642caf24\";', 1768594840),
('vastlead-cache-config.cableVerificationProvider', 's:45:\"https://uzobestgsm.com/api/validate-customer/\";', 1768594840),
('vastlead-cache-config.dataPinApi', 's:0:\"\";', 1768594840),
('vastlead-cache-config.dataPinProvider', 's:0:\"\";', 1768594840),
('vastlead-cache-config.examApi', 's:40:\"245141f6de9c0aa211b3a6baf1d1533c642caf24\";', 1768594840),
('vastlead-cache-config.logging', 'N;', 1768659910),
('vastlead-cache-config.meterApi', 's:40:\"245141f6de9c0aa211b3a6baf1d1533c642caf24\";', 1768594840),
('vastlead-cache-config.meterProvider', 's:39:\"https://uzobestgsm.com/api/electricity/\";', 1768594840),
('vastlead-cache-config.meterVerificationApi', 's:40:\"245141f6de9c0aa211b3a6baf1d1533c642caf24\";', 1768594840),
('vastlead-cache-config.meterVerificationProvider', 's:45:\"https://uzobestgsm.com/api/validate-customer/\";', 1768594840),
('vastlead-cache-config.rechargePinApi', 's:0:\"\";', 1768594840),
('vastlead-cache-config.rechargePinProvider', 's:0:\"\";', 1768594840),
('vastlead-cache-dashboard_realtime_metrics', 'a:8:{s:12:\"transactions\";a:8:{s:11:\"today_total\";i:0;s:16:\"today_successful\";i:0;s:12:\"today_failed\";i:0;s:13:\"today_pending\";i:0;s:9:\"this_hour\";i:0;s:8:\"last_24h\";i:0;s:18:\"success_rate_today\";i:100;s:27:\"pending_requiring_attention\";i:3;}s:5:\"users\";a:8:{s:11:\"total_users\";i:6;s:12:\"active_users\";i:6;s:19:\"today_registrations\";i:0;s:18:\"week_registrations\";i:0;s:19:\"month_registrations\";i:2;s:14:\"verified_users\";i:0;s:11:\"kyc_pending\";i:0;s:15:\"active_sessions\";i:0;}s:7:\"revenue\";a:8:{s:13:\"today_revenue\";i:0;s:17:\"yesterday_revenue\";i:0;s:13:\"month_revenue\";i:0;s:18:\"last_month_revenue\";s:5:\"29550\";s:12:\"daily_growth\";i:0;s:14:\"monthly_growth\";d:-100;s:16:\"today_commission\";i:0;s:25:\"average_transaction_value\";i:0;}s:8:\"services\";a:4:{s:18:\"top_services_today\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}s:20:\"service_availability\";a:5:{s:7:\"airtime\";s:6:\"online\";s:4:\"data\";s:6:\"online\";s:8:\"cable_tv\";s:6:\"online\";s:11:\"electricity\";s:6:\"online\";s:9:\"exam_pins\";s:6:\"online\";}s:18:\"api_response_times\";a:0:{}s:14:\"service_errors\";O:29:\"Illuminate\\Support\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}s:6:\"system\";a:9:{s:13:\"server_status\";s:6:\"online\";s:20:\"database_connections\";s:1:\"3\";s:12:\"memory_usage\";a:3:{s:7:\"current\";s:5:\"24 MB\";s:4:\"peak\";s:5:\"24 MB\";s:5:\"limit\";s:4:\"128M\";}s:10:\"disk_usage\";a:2:{s:15:\"used_percentage\";d:13.62;s:10:\"free_space\";s:10:\"9455.25 GB\";}s:10:\"api_health\";a:3:{s:14:\"overall_status\";s:7:\"healthy\";s:15:\"services_online\";i:5;s:14:\"services_total\";i:5;}s:13:\"recent_errors\";i:0;s:6:\"uptime\";s:5:\"99.9%\";s:16:\"monitoring_stats\";a:5:{s:12:\"generated_at\";s:27:\"2026-01-16T19:20:40.005318Z\";s:14:\"overall_status\";s:8:\"degraded\";s:15:\"services_status\";a:8:{s:7:\"airtime\";a:8:{s:7:\"service\";s:7:\"airtime\";s:7:\"healthy\";b:0;s:6:\"status\";s:4:\"down\";s:7:\"message\";s:22:\"Service not configured\";s:13:\"response_time\";N;s:10:\"error_code\";s:12:\"CONFIG_ERROR\";s:7:\"details\";N;s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.006948Z\";}s:4:\"data\";a:8:{s:7:\"service\";s:4:\"data\";s:7:\"healthy\";b:0;s:6:\"status\";s:4:\"down\";s:7:\"message\";s:22:\"Service not configured\";s:13:\"response_time\";N;s:10:\"error_code\";s:12:\"CONFIG_ERROR\";s:7:\"details\";N;s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.008729Z\";}s:5:\"cable\";a:8:{s:7:\"service\";s:5:\"cable\";s:7:\"healthy\";b:1;s:6:\"status\";s:11:\"operational\";s:7:\"message\";s:35:\"Service operational (cached status)\";s:13:\"response_time\";d:0.06;s:10:\"error_code\";N;s:7:\"details\";a:3:{s:6:\"status\";s:11:\"operational\";s:8:\"provider\";s:7:\"Uzobest\";s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.017915Z\";}s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.017976Z\";}s:11:\"electricity\";a:8:{s:7:\"service\";s:11:\"electricity\";s:7:\"healthy\";b:1;s:6:\"status\";s:11:\"operational\";s:7:\"message\";s:35:\"Service operational (cached status)\";s:13:\"response_time\";d:0.06;s:10:\"error_code\";N;s:7:\"details\";a:3:{s:6:\"status\";s:11:\"operational\";s:8:\"provider\";s:7:\"uzobest\";s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.024303Z\";}s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.024361Z\";}s:4:\"exam\";a:8:{s:7:\"service\";s:4:\"exam\";s:7:\"healthy\";b:1;s:6:\"status\";s:11:\"operational\";s:7:\"message\";s:35:\"Service operational (cached status)\";s:13:\"response_time\";d:0.06;s:10:\"error_code\";N;s:7:\"details\";a:3:{s:6:\"status\";s:11:\"operational\";s:8:\"provider\";s:9:\"Topupmate\";s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.028827Z\";}s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.028887Z\";}s:12:\"recharge_pin\";a:8:{s:7:\"service\";s:12:\"recharge_pin\";s:7:\"healthy\";b:1;s:6:\"status\";s:11:\"operational\";s:7:\"message\";s:35:\"Service operational (cached status)\";s:13:\"response_time\";d:0.06;s:10:\"error_code\";N;s:7:\"details\";a:3:{s:6:\"status\";s:11:\"operational\";s:8:\"provider\";s:7:\"uzobest\";s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.033160Z\";}s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.033218Z\";}s:8:\"data_pin\";a:8:{s:7:\"service\";s:8:\"data_pin\";s:7:\"healthy\";b:1;s:6:\"status\";s:11:\"operational\";s:7:\"message\";s:35:\"Service operational (cached status)\";s:13:\"response_time\";d:0.06;s:10:\"error_code\";N;s:7:\"details\";a:3:{s:6:\"status\";s:11:\"operational\";s:8:\"provider\";s:7:\"uzobest\";s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.037843Z\";}s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.037901Z\";}s:11:\"alpha_topup\";a:8:{s:7:\"service\";s:11:\"alpha_topup\";s:7:\"healthy\";b:1;s:6:\"status\";s:11:\"operational\";s:7:\"message\";s:35:\"Service operational (cached status)\";s:13:\"response_time\";d:0.06;s:10:\"error_code\";N;s:7:\"details\";a:3:{s:6:\"status\";s:11:\"operational\";s:8:\"provider\";s:7:\"uzobest\";s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.042037Z\";}s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.042096Z\";}}s:14:\"system_metrics\";a:0:{}s:6:\"alerts\";a:0:{}}s:20:\"service_availability\";a:8:{s:7:\"airtime\";a:8:{s:7:\"service\";s:7:\"airtime\";s:7:\"healthy\";b:0;s:6:\"status\";s:4:\"down\";s:7:\"message\";s:22:\"Service not configured\";s:13:\"response_time\";N;s:10:\"error_code\";s:12:\"CONFIG_ERROR\";s:7:\"details\";N;s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.006948Z\";}s:4:\"data\";a:8:{s:7:\"service\";s:4:\"data\";s:7:\"healthy\";b:0;s:6:\"status\";s:4:\"down\";s:7:\"message\";s:22:\"Service not configured\";s:13:\"response_time\";N;s:10:\"error_code\";s:12:\"CONFIG_ERROR\";s:7:\"details\";N;s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.008729Z\";}s:5:\"cable\";a:8:{s:7:\"service\";s:5:\"cable\";s:7:\"healthy\";b:1;s:6:\"status\";s:11:\"operational\";s:7:\"message\";s:35:\"Service operational (cached status)\";s:13:\"response_time\";d:0.06;s:10:\"error_code\";N;s:7:\"details\";a:3:{s:6:\"status\";s:11:\"operational\";s:8:\"provider\";s:7:\"Uzobest\";s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.017915Z\";}s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.017976Z\";}s:11:\"electricity\";a:8:{s:7:\"service\";s:11:\"electricity\";s:7:\"healthy\";b:1;s:6:\"status\";s:11:\"operational\";s:7:\"message\";s:35:\"Service operational (cached status)\";s:13:\"response_time\";d:0.06;s:10:\"error_code\";N;s:7:\"details\";a:3:{s:6:\"status\";s:11:\"operational\";s:8:\"provider\";s:7:\"uzobest\";s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.024303Z\";}s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.024361Z\";}s:4:\"exam\";a:8:{s:7:\"service\";s:4:\"exam\";s:7:\"healthy\";b:1;s:6:\"status\";s:11:\"operational\";s:7:\"message\";s:35:\"Service operational (cached status)\";s:13:\"response_time\";d:0.06;s:10:\"error_code\";N;s:7:\"details\";a:3:{s:6:\"status\";s:11:\"operational\";s:8:\"provider\";s:9:\"Topupmate\";s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.028827Z\";}s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.028887Z\";}s:12:\"recharge_pin\";a:8:{s:7:\"service\";s:12:\"recharge_pin\";s:7:\"healthy\";b:1;s:6:\"status\";s:11:\"operational\";s:7:\"message\";s:35:\"Service operational (cached status)\";s:13:\"response_time\";d:0.06;s:10:\"error_code\";N;s:7:\"details\";a:3:{s:6:\"status\";s:11:\"operational\";s:8:\"provider\";s:7:\"uzobest\";s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.033160Z\";}s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.033218Z\";}s:8:\"data_pin\";a:8:{s:7:\"service\";s:8:\"data_pin\";s:7:\"healthy\";b:1;s:6:\"status\";s:11:\"operational\";s:7:\"message\";s:35:\"Service operational (cached status)\";s:13:\"response_time\";d:0.06;s:10:\"error_code\";N;s:7:\"details\";a:3:{s:6:\"status\";s:11:\"operational\";s:8:\"provider\";s:7:\"uzobest\";s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.037843Z\";}s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.037901Z\";}s:11:\"alpha_topup\";a:8:{s:7:\"service\";s:11:\"alpha_topup\";s:7:\"healthy\";b:1;s:6:\"status\";s:11:\"operational\";s:7:\"message\";s:35:\"Service operational (cached status)\";s:13:\"response_time\";d:0.06;s:10:\"error_code\";N;s:7:\"details\";a:3:{s:6:\"status\";s:11:\"operational\";s:8:\"provider\";s:7:\"uzobest\";s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.042037Z\";}s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.042096Z\";}}}s:9:\"providers\";a:4:{s:9:\"providers\";a:1:{s:7:\"uzobest\";a:6:{s:6:\"status\";s:11:\"operational\";s:13:\"response_time\";i:200;s:12:\"success_rate\";d:99.5;s:10:\"last_check\";O:25:\"Illuminate\\Support\\Carbon\":3:{s:4:\"date\";s:26:\"2026-01-16 19:20:42.228361\";s:13:\"timezone_type\";i:3;s:8:\"timezone\";s:3:\"UTC\";}s:15:\"is_circuit_open\";b:0;s:18:\"available_services\";a:4:{i:0;s:7:\"airtime\";i:1;s:4:\"data\";i:2;s:5:\"cable\";i:3;s:11:\"electricity\";}}}s:14:\"overall_health\";s:4:\"good\";s:16:\"active_failovers\";a:0:{}s:22:\"circuit_breaker_status\";s:10:\"all_closed\";}s:15:\"api_performance\";a:6:{s:19:\"service_performance\";a:0:{}s:13:\"recent_errors\";a:0:{}s:13:\"hourly_trends\";a:0:{}s:20:\"overall_health_score\";i:95;s:16:\"slowest_services\";a:0:{}s:16:\"fastest_services\";a:0:{}}s:8:\"security\";a:6:{s:15:\"security_events\";a:0:{}s:19:\"suspicious_activity\";a:0:{}s:20:\"authentication_stats\";a:0:{}s:17:\"failed_logins_24h\";i:0;s:22:\"unique_login_users_24h\";i:0;s:14:\"security_score\";i:95;}}', 1768591302);
INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('vastlead-cache-dashboard_realtime_metrics_no_providers', 'a:7:{s:12:\"transactions\";a:8:{s:11:\"today_total\";i:0;s:16:\"today_successful\";i:0;s:12:\"today_failed\";i:0;s:13:\"today_pending\";i:0;s:9:\"this_hour\";i:0;s:8:\"last_24h\";i:0;s:18:\"success_rate_today\";i:100;s:27:\"pending_requiring_attention\";i:3;}s:5:\"users\";a:8:{s:11:\"total_users\";i:6;s:12:\"active_users\";i:6;s:19:\"today_registrations\";i:0;s:18:\"week_registrations\";i:0;s:19:\"month_registrations\";i:2;s:14:\"verified_users\";i:0;s:11:\"kyc_pending\";i:0;s:15:\"active_sessions\";i:0;}s:7:\"revenue\";a:8:{s:13:\"today_revenue\";i:0;s:17:\"yesterday_revenue\";i:0;s:13:\"month_revenue\";i:0;s:18:\"last_month_revenue\";s:5:\"29550\";s:12:\"daily_growth\";i:0;s:14:\"monthly_growth\";d:-100;s:16:\"today_commission\";i:0;s:25:\"average_transaction_value\";i:0;}s:8:\"services\";a:4:{s:18:\"top_services_today\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}s:20:\"service_availability\";a:5:{s:7:\"airtime\";s:6:\"online\";s:4:\"data\";s:6:\"online\";s:8:\"cable_tv\";s:6:\"online\";s:11:\"electricity\";s:6:\"online\";s:9:\"exam_pins\";s:6:\"online\";}s:18:\"api_response_times\";a:0:{}s:14:\"service_errors\";O:29:\"Illuminate\\Support\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}s:6:\"system\";a:9:{s:13:\"server_status\";s:6:\"online\";s:20:\"database_connections\";s:1:\"1\";s:12:\"memory_usage\";a:3:{s:7:\"current\";s:5:\"22 MB\";s:4:\"peak\";s:5:\"24 MB\";s:5:\"limit\";s:4:\"128M\";}s:10:\"disk_usage\";a:2:{s:15:\"used_percentage\";d:13.62;s:10:\"free_space\";s:10:\"9455.25 GB\";}s:10:\"api_health\";a:3:{s:14:\"overall_status\";s:7:\"healthy\";s:15:\"services_online\";i:5;s:14:\"services_total\";i:5;}s:13:\"recent_errors\";i:0;s:6:\"uptime\";s:5:\"99.9%\";s:16:\"monitoring_stats\";a:5:{s:12:\"generated_at\";s:27:\"2026-01-16T19:20:40.005318Z\";s:14:\"overall_status\";s:8:\"degraded\";s:15:\"services_status\";a:8:{s:7:\"airtime\";a:8:{s:7:\"service\";s:7:\"airtime\";s:7:\"healthy\";b:0;s:6:\"status\";s:4:\"down\";s:7:\"message\";s:22:\"Service not configured\";s:13:\"response_time\";N;s:10:\"error_code\";s:12:\"CONFIG_ERROR\";s:7:\"details\";N;s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.006948Z\";}s:4:\"data\";a:8:{s:7:\"service\";s:4:\"data\";s:7:\"healthy\";b:0;s:6:\"status\";s:4:\"down\";s:7:\"message\";s:22:\"Service not configured\";s:13:\"response_time\";N;s:10:\"error_code\";s:12:\"CONFIG_ERROR\";s:7:\"details\";N;s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.008729Z\";}s:5:\"cable\";a:8:{s:7:\"service\";s:5:\"cable\";s:7:\"healthy\";b:1;s:6:\"status\";s:11:\"operational\";s:7:\"message\";s:35:\"Service operational (cached status)\";s:13:\"response_time\";d:0.06;s:10:\"error_code\";N;s:7:\"details\";a:3:{s:6:\"status\";s:11:\"operational\";s:8:\"provider\";s:7:\"Uzobest\";s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.017915Z\";}s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.017976Z\";}s:11:\"electricity\";a:8:{s:7:\"service\";s:11:\"electricity\";s:7:\"healthy\";b:1;s:6:\"status\";s:11:\"operational\";s:7:\"message\";s:35:\"Service operational (cached status)\";s:13:\"response_time\";d:0.06;s:10:\"error_code\";N;s:7:\"details\";a:3:{s:6:\"status\";s:11:\"operational\";s:8:\"provider\";s:7:\"uzobest\";s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.024303Z\";}s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.024361Z\";}s:4:\"exam\";a:8:{s:7:\"service\";s:4:\"exam\";s:7:\"healthy\";b:1;s:6:\"status\";s:11:\"operational\";s:7:\"message\";s:35:\"Service operational (cached status)\";s:13:\"response_time\";d:0.06;s:10:\"error_code\";N;s:7:\"details\";a:3:{s:6:\"status\";s:11:\"operational\";s:8:\"provider\";s:9:\"Topupmate\";s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.028827Z\";}s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.028887Z\";}s:12:\"recharge_pin\";a:8:{s:7:\"service\";s:12:\"recharge_pin\";s:7:\"healthy\";b:1;s:6:\"status\";s:11:\"operational\";s:7:\"message\";s:35:\"Service operational (cached status)\";s:13:\"response_time\";d:0.06;s:10:\"error_code\";N;s:7:\"details\";a:3:{s:6:\"status\";s:11:\"operational\";s:8:\"provider\";s:7:\"uzobest\";s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.033160Z\";}s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.033218Z\";}s:8:\"data_pin\";a:8:{s:7:\"service\";s:8:\"data_pin\";s:7:\"healthy\";b:1;s:6:\"status\";s:11:\"operational\";s:7:\"message\";s:35:\"Service operational (cached status)\";s:13:\"response_time\";d:0.06;s:10:\"error_code\";N;s:7:\"details\";a:3:{s:6:\"status\";s:11:\"operational\";s:8:\"provider\";s:7:\"uzobest\";s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.037843Z\";}s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.037901Z\";}s:11:\"alpha_topup\";a:8:{s:7:\"service\";s:11:\"alpha_topup\";s:7:\"healthy\";b:1;s:6:\"status\";s:11:\"operational\";s:7:\"message\";s:35:\"Service operational (cached status)\";s:13:\"response_time\";d:0.06;s:10:\"error_code\";N;s:7:\"details\";a:3:{s:6:\"status\";s:11:\"operational\";s:8:\"provider\";s:7:\"uzobest\";s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.042037Z\";}s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.042096Z\";}}s:14:\"system_metrics\";a:0:{}s:6:\"alerts\";a:0:{}}s:20:\"service_availability\";a:8:{s:7:\"airtime\";a:8:{s:7:\"service\";s:7:\"airtime\";s:7:\"healthy\";b:0;s:6:\"status\";s:4:\"down\";s:7:\"message\";s:22:\"Service not configured\";s:13:\"response_time\";N;s:10:\"error_code\";s:12:\"CONFIG_ERROR\";s:7:\"details\";N;s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.006948Z\";}s:4:\"data\";a:8:{s:7:\"service\";s:4:\"data\";s:7:\"healthy\";b:0;s:6:\"status\";s:4:\"down\";s:7:\"message\";s:22:\"Service not configured\";s:13:\"response_time\";N;s:10:\"error_code\";s:12:\"CONFIG_ERROR\";s:7:\"details\";N;s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.008729Z\";}s:5:\"cable\";a:8:{s:7:\"service\";s:5:\"cable\";s:7:\"healthy\";b:1;s:6:\"status\";s:11:\"operational\";s:7:\"message\";s:35:\"Service operational (cached status)\";s:13:\"response_time\";d:0.06;s:10:\"error_code\";N;s:7:\"details\";a:3:{s:6:\"status\";s:11:\"operational\";s:8:\"provider\";s:7:\"Uzobest\";s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.017915Z\";}s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.017976Z\";}s:11:\"electricity\";a:8:{s:7:\"service\";s:11:\"electricity\";s:7:\"healthy\";b:1;s:6:\"status\";s:11:\"operational\";s:7:\"message\";s:35:\"Service operational (cached status)\";s:13:\"response_time\";d:0.06;s:10:\"error_code\";N;s:7:\"details\";a:3:{s:6:\"status\";s:11:\"operational\";s:8:\"provider\";s:7:\"uzobest\";s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.024303Z\";}s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.024361Z\";}s:4:\"exam\";a:8:{s:7:\"service\";s:4:\"exam\";s:7:\"healthy\";b:1;s:6:\"status\";s:11:\"operational\";s:7:\"message\";s:35:\"Service operational (cached status)\";s:13:\"response_time\";d:0.06;s:10:\"error_code\";N;s:7:\"details\";a:3:{s:6:\"status\";s:11:\"operational\";s:8:\"provider\";s:9:\"Topupmate\";s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.028827Z\";}s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.028887Z\";}s:12:\"recharge_pin\";a:8:{s:7:\"service\";s:12:\"recharge_pin\";s:7:\"healthy\";b:1;s:6:\"status\";s:11:\"operational\";s:7:\"message\";s:35:\"Service operational (cached status)\";s:13:\"response_time\";d:0.06;s:10:\"error_code\";N;s:7:\"details\";a:3:{s:6:\"status\";s:11:\"operational\";s:8:\"provider\";s:7:\"uzobest\";s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.033160Z\";}s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.033218Z\";}s:8:\"data_pin\";a:8:{s:7:\"service\";s:8:\"data_pin\";s:7:\"healthy\";b:1;s:6:\"status\";s:11:\"operational\";s:7:\"message\";s:35:\"Service operational (cached status)\";s:13:\"response_time\";d:0.06;s:10:\"error_code\";N;s:7:\"details\";a:3:{s:6:\"status\";s:11:\"operational\";s:8:\"provider\";s:7:\"uzobest\";s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.037843Z\";}s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.037901Z\";}s:11:\"alpha_topup\";a:8:{s:7:\"service\";s:11:\"alpha_topup\";s:7:\"healthy\";b:1;s:6:\"status\";s:11:\"operational\";s:7:\"message\";s:35:\"Service operational (cached status)\";s:13:\"response_time\";d:0.06;s:10:\"error_code\";N;s:7:\"details\";a:3:{s:6:\"status\";s:11:\"operational\";s:8:\"provider\";s:7:\"uzobest\";s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.042037Z\";}s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.042096Z\";}}}s:15:\"api_performance\";a:6:{s:19:\"service_performance\";a:0:{}s:13:\"recent_errors\";a:0:{}s:13:\"hourly_trends\";a:0:{}s:20:\"overall_health_score\";i:95;s:16:\"slowest_services\";a:0:{}s:16:\"fastest_services\";a:0:{}}s:8:\"security\";a:6:{s:15:\"security_events\";a:0:{}s:19:\"suspicious_activity\";a:0:{}s:20:\"authentication_stats\";a:0:{}s:17:\"failed_logins_24h\";i:0;s:22:\"unique_login_users_24h\";i:0;s:14:\"security_score\";i:95;}}', 1768591300),
('vastlead-cache-error_pattern_ErrorException:0', 'i:1;', 1766067493),
('vastlead-cache-feature_toggle_maintenance_mode', 'b:0;', 1768591545),
('vastlead-cache-health_check_airtime', 'a:8:{s:7:\"service\";s:7:\"airtime\";s:7:\"healthy\";b:0;s:6:\"status\";s:4:\"down\";s:7:\"message\";s:22:\"Service not configured\";s:13:\"response_time\";N;s:10:\"error_code\";s:12:\"CONFIG_ERROR\";s:7:\"details\";N;s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.006948Z\";}', 1768591245),
('vastlead-cache-health_check_alpha_topup', 'a:8:{s:7:\"service\";s:11:\"alpha_topup\";s:7:\"healthy\";b:1;s:6:\"status\";s:11:\"operational\";s:7:\"message\";s:35:\"Service operational (cached status)\";s:13:\"response_time\";d:0.06;s:10:\"error_code\";N;s:7:\"details\";a:3:{s:6:\"status\";s:11:\"operational\";s:8:\"provider\";s:7:\"uzobest\";s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.042037Z\";}s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.042096Z\";}', 1768591245),
('vastlead-cache-health_check_cable', 'a:8:{s:7:\"service\";s:5:\"cable\";s:7:\"healthy\";b:1;s:6:\"status\";s:11:\"operational\";s:7:\"message\";s:35:\"Service operational (cached status)\";s:13:\"response_time\";d:0.06;s:10:\"error_code\";N;s:7:\"details\";a:3:{s:6:\"status\";s:11:\"operational\";s:8:\"provider\";s:7:\"Uzobest\";s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.017915Z\";}s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.017976Z\";}', 1768591245),
('vastlead-cache-health_check_data', 'a:8:{s:7:\"service\";s:4:\"data\";s:7:\"healthy\";b:0;s:6:\"status\";s:4:\"down\";s:7:\"message\";s:22:\"Service not configured\";s:13:\"response_time\";N;s:10:\"error_code\";s:12:\"CONFIG_ERROR\";s:7:\"details\";N;s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.008729Z\";}', 1768591245),
('vastlead-cache-health_check_data_pin', 'a:8:{s:7:\"service\";s:8:\"data_pin\";s:7:\"healthy\";b:1;s:6:\"status\";s:11:\"operational\";s:7:\"message\";s:35:\"Service operational (cached status)\";s:13:\"response_time\";d:0.06;s:10:\"error_code\";N;s:7:\"details\";a:3:{s:6:\"status\";s:11:\"operational\";s:8:\"provider\";s:7:\"uzobest\";s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.037843Z\";}s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.037901Z\";}', 1768591245),
('vastlead-cache-health_check_electricity', 'a:8:{s:7:\"service\";s:11:\"electricity\";s:7:\"healthy\";b:1;s:6:\"status\";s:11:\"operational\";s:7:\"message\";s:35:\"Service operational (cached status)\";s:13:\"response_time\";d:0.06;s:10:\"error_code\";N;s:7:\"details\";a:3:{s:6:\"status\";s:11:\"operational\";s:8:\"provider\";s:7:\"uzobest\";s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.024303Z\";}s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.024361Z\";}', 1768591245),
('vastlead-cache-health_check_exam', 'a:8:{s:7:\"service\";s:4:\"exam\";s:7:\"healthy\";b:1;s:6:\"status\";s:11:\"operational\";s:7:\"message\";s:35:\"Service operational (cached status)\";s:13:\"response_time\";d:0.06;s:10:\"error_code\";N;s:7:\"details\";a:3:{s:6:\"status\";s:11:\"operational\";s:8:\"provider\";s:9:\"Topupmate\";s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.028827Z\";}s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.028887Z\";}', 1768591245),
('vastlead-cache-health_check_monnify', 'a:8:{s:7:\"service\";s:7:\"monnify\";s:7:\"healthy\";b:0;s:6:\"status\";s:4:\"down\";s:7:\"message\";s:26:\"Credentials not configured\";s:13:\"response_time\";N;s:10:\"error_code\";s:12:\"CONFIG_ERROR\";s:7:\"details\";N;s:10:\"checked_at\";s:27:\"2026-01-16T19:20:42.370109Z\";}', 1768591247),
('vastlead-cache-health_check_paystack', 'a:8:{s:7:\"service\";s:8:\"paystack\";s:7:\"healthy\";b:0;s:6:\"status\";s:4:\"down\";s:7:\"message\";s:25:\"Secret key not configured\";s:13:\"response_time\";N;s:10:\"error_code\";s:12:\"CONFIG_ERROR\";s:7:\"details\";N;s:10:\"checked_at\";s:27:\"2026-01-16T19:20:42.367736Z\";}', 1768591247),
('vastlead-cache-health_check_recharge_pin', 'a:8:{s:7:\"service\";s:12:\"recharge_pin\";s:7:\"healthy\";b:1;s:6:\"status\";s:11:\"operational\";s:7:\"message\";s:35:\"Service operational (cached status)\";s:13:\"response_time\";d:0.06;s:10:\"error_code\";N;s:7:\"details\";a:3:{s:6:\"status\";s:11:\"operational\";s:8:\"provider\";s:7:\"uzobest\";s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.033160Z\";}s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.033218Z\";}', 1768591245),
('vastlead-cache-health_check_uzobest_vtu', 'a:8:{s:7:\"service\";s:7:\"uzobest\";s:7:\"healthy\";b:0;s:6:\"status\";s:4:\"down\";s:7:\"message\";s:31:\"Uzobest API returned error: 404\";s:13:\"response_time\";d:117.2;s:10:\"error_code\";s:9:\"API_ERROR\";s:7:\"details\";N;s:10:\"checked_at\";s:27:\"2026-01-16T19:20:42.363413Z\";}', 1768591247),
('vastlead-cache-hourly_trends_2025-12-16', 'a:24:{i:0;a:5:{s:4:\"hour\";i:0;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:1;a:5:{s:4:\"hour\";i:1;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:2;a:5:{s:4:\"hour\";i:2;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:3;a:5:{s:4:\"hour\";i:3;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:4;a:5:{s:4:\"hour\";i:4;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:5;a:5:{s:4:\"hour\";i:5;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:6;a:5:{s:4:\"hour\";i:6;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:7;a:5:{s:4:\"hour\";i:7;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:8;a:5:{s:4:\"hour\";i:8;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:9;a:5:{s:4:\"hour\";i:9;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:10;a:5:{s:4:\"hour\";i:10;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:11;a:5:{s:4:\"hour\";i:11;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:12;a:5:{s:4:\"hour\";i:12;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:13;a:5:{s:4:\"hour\";i:13;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:14;a:5:{s:4:\"hour\";i:14;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:15;a:5:{s:4:\"hour\";i:15;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:16;a:5:{s:4:\"hour\";i:16;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:17;a:5:{s:4:\"hour\";i:17;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:18;a:5:{s:4:\"hour\";i:18;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:19;a:5:{s:4:\"hour\";i:19;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:20;a:5:{s:4:\"hour\";i:20;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:21;a:5:{s:4:\"hour\";i:21;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:22;a:5:{s:4:\"hour\";i:22;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:23;a:5:{s:4:\"hour\";i:23;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}}', 1765914364),
('vastlead-cache-hourly_trends_2025-12-18', 'a:24:{i:0;a:5:{s:4:\"hour\";i:0;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:1;a:5:{s:4:\"hour\";i:1;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:2;a:5:{s:4:\"hour\";i:2;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:3;a:5:{s:4:\"hour\";i:3;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:4;a:5:{s:4:\"hour\";i:4;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:5;a:5:{s:4:\"hour\";i:5;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:6;a:5:{s:4:\"hour\";i:6;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:7;a:5:{s:4:\"hour\";i:7;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:8;a:5:{s:4:\"hour\";i:8;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:9;a:5:{s:4:\"hour\";i:9;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:10;a:5:{s:4:\"hour\";i:10;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:11;a:5:{s:4:\"hour\";i:11;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:12;a:5:{s:4:\"hour\";i:12;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:13;a:5:{s:4:\"hour\";i:13;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:14;a:5:{s:4:\"hour\";i:14;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:15;a:5:{s:4:\"hour\";i:15;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:16;a:5:{s:4:\"hour\";i:16;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:17;a:5:{s:4:\"hour\";i:17;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:18;a:5:{s:4:\"hour\";i:18;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:19;a:5:{s:4:\"hour\";i:19;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:20;a:5:{s:4:\"hour\";i:20;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:21;a:5:{s:4:\"hour\";i:21;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:22;a:5:{s:4:\"hour\";i:22;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:23;a:5:{s:4:\"hour\";i:23;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}}', 1766090242),
('vastlead-cache-hourly_trends_2025-12-19', 'a:24:{i:0;a:5:{s:4:\"hour\";i:0;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:1;a:5:{s:4:\"hour\";i:1;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:2;a:5:{s:4:\"hour\";i:2;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:3;a:5:{s:4:\"hour\";i:3;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:4;a:5:{s:4:\"hour\";i:4;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:5;a:5:{s:4:\"hour\";i:5;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:6;a:5:{s:4:\"hour\";i:6;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:7;a:5:{s:4:\"hour\";i:7;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:8;a:5:{s:4:\"hour\";i:8;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:9;a:5:{s:4:\"hour\";i:9;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:10;a:5:{s:4:\"hour\";i:10;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:11;a:5:{s:4:\"hour\";i:11;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:12;a:5:{s:4:\"hour\";i:12;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:13;a:5:{s:4:\"hour\";i:13;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:14;a:5:{s:4:\"hour\";i:14;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:15;a:5:{s:4:\"hour\";i:15;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:16;a:5:{s:4:\"hour\";i:16;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:17;a:5:{s:4:\"hour\";i:17;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:18;a:5:{s:4:\"hour\";i:18;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:19;a:5:{s:4:\"hour\";i:19;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:20;a:5:{s:4:\"hour\";i:20;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:21;a:5:{s:4:\"hour\";i:21;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:22;a:5:{s:4:\"hour\";i:22;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:23;a:5:{s:4:\"hour\";i:23;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}}', 1766138073),
('vastlead-cache-hourly_trends_2026-01-16', 'a:24:{i:0;a:5:{s:4:\"hour\";i:0;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:1;a:5:{s:4:\"hour\";i:1;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:2;a:5:{s:4:\"hour\";i:2;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:3;a:5:{s:4:\"hour\";i:3;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:4;a:5:{s:4:\"hour\";i:4;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:5;a:5:{s:4:\"hour\";i:5;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:6;a:5:{s:4:\"hour\";i:6;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:7;a:5:{s:4:\"hour\";i:7;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:8;a:5:{s:4:\"hour\";i:8;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:9;a:5:{s:4:\"hour\";i:9;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:10;a:5:{s:4:\"hour\";i:10;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:11;a:5:{s:4:\"hour\";i:11;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:12;a:5:{s:4:\"hour\";i:12;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:13;a:5:{s:4:\"hour\";i:13;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:14;a:5:{s:4:\"hour\";i:14;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:15;a:5:{s:4:\"hour\";i:15;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:16;a:5:{s:4:\"hour\";i:16;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:17;a:5:{s:4:\"hour\";i:17;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:18;a:5:{s:4:\"hour\";i:18;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:19;a:5:{s:4:\"hour\";i:19;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:20;a:5:{s:4:\"hour\";i:20;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:21;a:5:{s:4:\"hour\";i:21;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:22;a:5:{s:4:\"hour\";i:22;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}i:23;a:5:{s:4:\"hour\";i:23;s:5:\"total\";i:0;s:10:\"successful\";i:0;s:6:\"failed\";i:0;s:7:\"revenue\";i:0;}}', 1768591540),
('vastlead-cache-monitoring_dashboard', 'a:5:{s:12:\"generated_at\";s:27:\"2026-01-16T19:20:40.005318Z\";s:14:\"overall_status\";s:8:\"degraded\";s:15:\"services_status\";a:8:{s:7:\"airtime\";a:8:{s:7:\"service\";s:7:\"airtime\";s:7:\"healthy\";b:0;s:6:\"status\";s:4:\"down\";s:7:\"message\";s:22:\"Service not configured\";s:13:\"response_time\";N;s:10:\"error_code\";s:12:\"CONFIG_ERROR\";s:7:\"details\";N;s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.006948Z\";}s:4:\"data\";a:8:{s:7:\"service\";s:4:\"data\";s:7:\"healthy\";b:0;s:6:\"status\";s:4:\"down\";s:7:\"message\";s:22:\"Service not configured\";s:13:\"response_time\";N;s:10:\"error_code\";s:12:\"CONFIG_ERROR\";s:7:\"details\";N;s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.008729Z\";}s:5:\"cable\";a:8:{s:7:\"service\";s:5:\"cable\";s:7:\"healthy\";b:1;s:6:\"status\";s:11:\"operational\";s:7:\"message\";s:35:\"Service operational (cached status)\";s:13:\"response_time\";d:0.06;s:10:\"error_code\";N;s:7:\"details\";a:3:{s:6:\"status\";s:11:\"operational\";s:8:\"provider\";s:7:\"Uzobest\";s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.017915Z\";}s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.017976Z\";}s:11:\"electricity\";a:8:{s:7:\"service\";s:11:\"electricity\";s:7:\"healthy\";b:1;s:6:\"status\";s:11:\"operational\";s:7:\"message\";s:35:\"Service operational (cached status)\";s:13:\"response_time\";d:0.06;s:10:\"error_code\";N;s:7:\"details\";a:3:{s:6:\"status\";s:11:\"operational\";s:8:\"provider\";s:7:\"uzobest\";s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.024303Z\";}s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.024361Z\";}s:4:\"exam\";a:8:{s:7:\"service\";s:4:\"exam\";s:7:\"healthy\";b:1;s:6:\"status\";s:11:\"operational\";s:7:\"message\";s:35:\"Service operational (cached status)\";s:13:\"response_time\";d:0.06;s:10:\"error_code\";N;s:7:\"details\";a:3:{s:6:\"status\";s:11:\"operational\";s:8:\"provider\";s:9:\"Topupmate\";s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.028827Z\";}s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.028887Z\";}s:12:\"recharge_pin\";a:8:{s:7:\"service\";s:12:\"recharge_pin\";s:7:\"healthy\";b:1;s:6:\"status\";s:11:\"operational\";s:7:\"message\";s:35:\"Service operational (cached status)\";s:13:\"response_time\";d:0.06;s:10:\"error_code\";N;s:7:\"details\";a:3:{s:6:\"status\";s:11:\"operational\";s:8:\"provider\";s:7:\"uzobest\";s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.033160Z\";}s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.033218Z\";}s:8:\"data_pin\";a:8:{s:7:\"service\";s:8:\"data_pin\";s:7:\"healthy\";b:1;s:6:\"status\";s:11:\"operational\";s:7:\"message\";s:35:\"Service operational (cached status)\";s:13:\"response_time\";d:0.06;s:10:\"error_code\";N;s:7:\"details\";a:3:{s:6:\"status\";s:11:\"operational\";s:8:\"provider\";s:7:\"uzobest\";s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.037843Z\";}s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.037901Z\";}s:11:\"alpha_topup\";a:8:{s:7:\"service\";s:11:\"alpha_topup\";s:7:\"healthy\";b:1;s:6:\"status\";s:11:\"operational\";s:7:\"message\";s:35:\"Service operational (cached status)\";s:13:\"response_time\";d:0.06;s:10:\"error_code\";N;s:7:\"details\";a:3:{s:6:\"status\";s:11:\"operational\";s:8:\"provider\";s:7:\"uzobest\";s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.042037Z\";}s:10:\"checked_at\";s:27:\"2026-01-16T19:20:40.042096Z\";}}s:14:\"system_metrics\";a:0:{}s:6:\"alerts\";a:0:{}}', 1768591250),
('vastlead-cache-monnify_access_token_66538f9a1fc5ac2a2d347d1ab9abc79b', 's:1050:\"eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJhdWQiOlsibW9ubmlmeS12YWx1ZS1hZGRlZC1zZXJ2aWNlIiwibW9ubmlmeS1wYXltZW50LWVuZ2luZSIsIm1vbm5pZnktZGlzYnVyc2VtZW50LXNlcnZpY2UiLCJtb25uaWZ5LW9mZmxpbmUtcGF5bWVudC1zZXJ2aWNlIl0sInNjb3BlIjpbInByb2ZpbGUiXSwiZXhwIjoxNzY4NzQ4Mjk2LCJhdXRob3JpdGllcyI6WyJNUEVfTUFOQUdFX0xJTUlUX1BST0ZJTEUiLCJNUEVfVVBEQVRFX1JFU0VSVkVEX0FDQ09VTlQiLCJNUEVfSU5JVElBTElaRV9QQVlNRU5UIiwiTVBFX1JFU0VSVkVfQUNDT1VOVCIsIk1QRV9DQU5fUkVUUklFVkVfVFJBTlNBQ1RJT04iLCJNUEVfUkVUUklFVkVfUkVTRVJWRURfQUNDT1VOVCIsIk1QRV9ERUxFVEVfUkVTRVJWRURfQUNDT1VOVCIsIk1QRV9SRVRSSUVWRV9SRVNFUlZFRF9BQ0NPVU5UX1RSQU5TQUNUSU9OUyJdLCJqdGkiOiI1MjhkMjhjOC05NDY3LTQyZTItYTcxYy1iNDQwNGVmYTUxY2YiLCJjbGllbnRfaWQiOiJNS19QUk9EX1YzRkE2VkxFTFAifQ.M5aq8f0-KBeK4s-3KVTrIjNcweWpIASzuEwJVp8ACT9l4TIDMjlLiN_srSp7G0Vfp-m6n52_skLj77-mZD4rIQrPpCE3agN8-6gwCSkV3eemspEVakRJtruXIaaNR_m_7Mm8rGWfVrhMnh1iVZheMev-yoJpsJWIXlDyxbUp6Ezriawnb_ckqz6u1boNWSffR3wEMH4SRhPti8hUlF9hQZZm5R1YBsMqR2lUcoaPivcGPiSF_VUNYoG2fStiWSsGJiELDv2gMYRPJmE_8osNPvLXbN01rvg6oJ6Ex3e47YPwXl4FTUxlydhsAP0bfINcsLAud46xcbQdidp9vS_Rgg\";', 1768747999),
('vastlead-cache-monnify_access_token_f6f34779b40634697c31e14238f4e910', 'N;', 1768050467),
('vastlead-cache-provider_health_metrics', 'a:4:{s:9:\"providers\";a:1:{s:7:\"uzobest\";a:6:{s:6:\"status\";s:11:\"operational\";s:13:\"response_time\";i:200;s:12:\"success_rate\";d:99.5;s:10:\"last_check\";O:25:\"Illuminate\\Support\\Carbon\":3:{s:4:\"date\";s:26:\"2026-01-16 19:20:42.228361\";s:13:\"timezone_type\";i:3;s:8:\"timezone\";s:3:\"UTC\";}s:15:\"is_circuit_open\";b:0;s:18:\"available_services\";a:4:{i:0;s:7:\"airtime\";i:1;s:4:\"data\";i:2;s:5:\"cable\";i:3;s:11:\"electricity\";}}}s:14:\"overall_health\";s:4:\"good\";s:16:\"active_failovers\";a:0:{}s:22:\"circuit_breaker_status\";s:10:\"all_closed\";}', 1768591542),
('vastlead-cache-security_metrics', 'a:6:{s:15:\"security_events\";a:0:{}s:19:\"suspicious_activity\";a:0:{}s:20:\"authentication_stats\";a:0:{}s:17:\"failed_logins_24h\";i:0;s:22:\"unique_login_users_24h\";i:0;s:14:\"security_score\";i:95;}', 1768591540),
('vastlead-cache-service.airtime', 'a:0:{}', 1768594840),
('vastlead-cache-service.alpha_topup', 'a:2:{s:7:\"api_key\";s:0:\"\";s:8:\"provider\";s:0:\"\";}', 1768594840),
('vastlead-cache-service.cable', 'a:7:{s:16:\"verification_api\";s:40:\"245141f6de9c0aa211b3a6baf1d1533c642caf24\";s:21:\"verification_provider\";s:45:\"https://uzobestgsm.com/api/validate-customer/\";s:7:\"api_key\";s:40:\"245141f6de9c0aa211b3a6baf1d1533c642caf24\";s:8:\"provider\";s:35:\"https://uzobestgsm.com/api/cabletv/\";s:13:\"provider_name\";s:7:\"Uzobest\";s:9:\"auth_type\";s:6:\"header\";s:11:\"auth_params\";a:3:{s:5:\"token\";s:40:\"57adc15fd67d8fec5bcf7b268ca01bde460bedcd\";s:11:\"header_name\";s:13:\"Authorization\";s:13:\"header_prefix\";s:6:\"Token \";}}', 1768594840),
('vastlead-cache-service.data', 'a:0:{}', 1768594840),
('vastlead-cache-service.data_pin', 'a:3:{s:7:\"api_key\";s:0:\"\";s:8:\"provider\";s:0:\"\";s:9:\"auth_type\";s:5:\"Basic\";}', 1768594840),
('vastlead-cache-service.electricity', 'a:4:{s:16:\"verification_api\";s:40:\"245141f6de9c0aa211b3a6baf1d1533c642caf24\";s:21:\"verification_provider\";s:45:\"https://uzobestgsm.com/api/validate-customer/\";s:7:\"api_key\";s:40:\"245141f6de9c0aa211b3a6baf1d1533c642caf24\";s:8:\"provider\";s:39:\"https://uzobestgsm.com/api/electricity/\";}', 1768594840),
('vastlead-cache-service.exam', 'a:6:{s:7:\"api_key\";s:40:\"245141f6de9c0aa211b3a6baf1d1533c642caf24\";s:8:\"provider\";s:31:\"https://topupmate.com/api/exam/\";s:9:\"auth_type\";s:5:\"token\";s:8:\"user_url\";N;s:13:\"provider_name\";s:9:\"Topupmate\";s:11:\"auth_params\";N;}', 1768594840),
('vastlead-cache-service.recharge_pin', 'a:3:{s:7:\"api_key\";s:0:\"\";s:8:\"provider\";s:0:\"\";s:9:\"auth_type\";s:5:\"Basic\";}', 1768594840);

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
(1, 'monifyCharges', '1.7'),
(2, 'monifyApi', 'MK_PROD_V3FA6VLELP'),
(3, 'monifySecrete', '8ZPSJMRSBT291RKW74AJNG9JQL6M84YU'),
(4, 'monifyContract', '5812563'),
(5, 'monifyWeStatus', 'On'),
(6, 'monifyMoStatus', 'On'),
(7, 'monifyFeStatus', 'Off'),
(8, 'monifySaStatus', 'On'),
(9, 'monifyStatus', 'On'),
(10, 'paystackCharges', '1.5'),
(11, 'paystackApi', 'sk_test_4cd65c1fe9a4c551fc0635c313f88f1180232c54'),
(12, 'paystackStatus', 'On'),
(13, 'walletOneProviderName', 'Maskawasub'),
(14, 'walletOneApi', 'e5199989c9df406e8f78f9b255ab5620e131e2b4'),
(15, 'walletOneProvider', 'https://maskawasub.com/api/user/'),
(16, 'walletTwoProviderName', 'Topupmate'),
(17, 'walletTwoApi', ''),
(18, 'walletTwoProvider', 'https://topupmate.com/api/user/'),
(19, 'walletThreeProviderName', 'Aabaxztech'),
(20, 'walletThreeApi', ''),
(21, 'walletThreeProvider', 'https://aabaxztech.com/api/user/'),
(22, 'monifyEnvironment', 'sandbox');

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
(193, '385', 1, 385, '75.0MB', '75', '1', 79, NULL, NULL, 77, 77, 'GIFTING', NULL, NULL, NULL),
(194, '463', 1, 463, '110.0MB', '100', '1', 105, NULL, NULL, 103, 102, 'GIFTING', NULL, NULL, NULL),
(195, '464', 1, 464, '230.0MB', '200', '1', 210, NULL, NULL, 206, 204, 'GIFTING', NULL, NULL, NULL),
(196, '495', 1, 495, '500.0MB', '343', '1', 360, NULL, NULL, 353, 350, 'GIFTING', NULL, NULL, NULL),
(197, '390', 1, 390, '750.0MB', '440', '3', 462, NULL, NULL, 453, 449, 'GIFTING', NULL, NULL, NULL),
(198, '486', 1, 486, '1.2GB', '440', '30', 462, NULL, NULL, 453, 449, 'GIFTING', NULL, NULL, NULL),
(199, '387', 1, 387, '1.0GB', '485', '1', 509, NULL, NULL, 500, 495, 'GIFTING', NULL, NULL, NULL),
(200, '461', 1, 461, '500.0MB', '485', '7', 509, NULL, NULL, 500, 495, 'GIFTING', NULL, NULL, NULL),
(201, '451', 1, 451, '1.5GB', '585', '2', 614, NULL, NULL, 603, 597, 'GIFTING', NULL, NULL, NULL),
(202, '388', 1, 388, '2.0GB', '735', '2', 772, NULL, NULL, 757, 750, 'GIFTING', NULL, NULL, NULL),
(203, '467', 1, 467, '2.5GB', '735', '1', 772, NULL, NULL, 757, 750, 'GIFTING', NULL, NULL, NULL),
(204, '456', 1, 456, '1.0GB', '780', '7', 819, NULL, NULL, 803, 796, 'GIFTING', NULL, NULL, NULL),
(205, '389', 1, 389, '2.5GB', '880', '2', 924, NULL, NULL, 906, 898, 'GIFTING', NULL, NULL, NULL),
(206, '395', 1, 395, '1.5GB', '975', '7', 1024, NULL, NULL, 1004, 995, 'GIFTING', NULL, NULL, NULL),
(207, '452', 1, 452, '3.2GB', '975', '2', 1024, NULL, NULL, 1004, 995, 'GIFTING', NULL, NULL, NULL),
(208, '398', 1, 398, '2.0GB', '1465', '30', 1538, NULL, NULL, 1509, 1494, 'GIFTING', NULL, NULL, NULL),
(209, '482', 1, 482, '1.8GB', '1465', '30', 1538, NULL, NULL, 1509, 1494, 'GIFTING', NULL, NULL, NULL),
(210, '527', 1, 527, '3.5GB', '1470', '7', 1544, NULL, NULL, 1514, 1499, 'GIFTING', NULL, NULL, NULL),
(211, '597', 1, 597, '7.0GB', '1765', '2', 1853, NULL, NULL, 1818, 1800, 'GIFTING', NULL, NULL, NULL),
(212, '399', 1, 399, '2.7GB', '1960', '30', 2058, NULL, NULL, 2019, 1999, 'GIFTING', NULL, NULL, NULL),
(213, '396', 1, 396, '6.0GB', '2435', '7', 2557, NULL, NULL, 2508, 2484, 'GIFTING', NULL, NULL, NULL),
(214, '460', 1, 460, '3.5GB', '2435', '30', 2557, NULL, NULL, 2508, 2484, 'GIFTING', NULL, NULL, NULL),
(215, '570', 1, 570, '5.0GB', '2940', '30', 3087, NULL, NULL, 3028, 2999, 'GIFTING', NULL, NULL, NULL),
(216, '470', 1, 470, '7.0GB', '3395', '30', 3565, NULL, NULL, 3497, 3463, 'GIFTING', NULL, NULL, NULL),
(217, '397', 1, 397, '11.0GB', '3430', '7', 3602, NULL, NULL, 3533, 3499, 'GIFTING', NULL, NULL, NULL),
(218, '402', 1, 402, '10.0GB', '4400', '30', 4620, NULL, NULL, 4532, 4488, 'GIFTING', NULL, NULL, NULL),
(219, '496', 1, 496, '20.0GB', '4900', '7', 5145, NULL, NULL, 5047, 4998, 'GIFTING', NULL, NULL, NULL),
(220, '403', 1, 403, '12.5GB', '5335', '30', 5602, NULL, NULL, 5495, 5442, 'GIFTING', NULL, NULL, NULL),
(221, '404', 1, 404, '16.5GB', '6370', '30', 6689, NULL, NULL, 6561, 6497, 'GIFTING', NULL, NULL, NULL),
(222, '468', 1, 468, '20.0GB', '7275', '30', 7639, NULL, NULL, 7493, 7421, 'GIFTING', NULL, NULL, NULL),
(223, '469', 1, 469, '25.0GB', '8730', '30', 9167, NULL, NULL, 8992, 8905, 'GIFTING', NULL, NULL, NULL),
(224, '407', 1, 407, '36.0GB', '10780', '30', 11319, NULL, NULL, 11103, 10996, 'GIFTING', NULL, NULL, NULL),
(225, '408', 1, 408, '75.0GB', '17460', '30', 18333, NULL, NULL, 17984, 17809, 'GIFTING', NULL, NULL, NULL),
(226, '526', 1, 526, '90.0GB', '24500', '60', 25725, NULL, NULL, 25235, 24990, 'GIFTING', NULL, NULL, NULL),
(227, '409', 1, 409, '165.0GB', '34300', '30', 36015, NULL, NULL, 35329, 34986, 'GIFTING', NULL, NULL, NULL),
(228, '525', 1, 525, '150.0GB', '39200', '60', 41160, NULL, NULL, 40376, 39984, 'GIFTING', NULL, NULL, NULL),
(229, '438', 1, 438, '250.0GB', '53900', '30', 56595, NULL, NULL, 55517, 54978, 'GIFTING', NULL, NULL, NULL),
(230, '490', 1, 490, '800.0GB', '122500', '30', 128625, NULL, NULL, 126175, 124950, 'GIFTING', NULL, NULL, NULL),
(231, '592', 1, 592, '1.0GB', '245', '1', 257, NULL, NULL, 252, 250, 'AWOOF DATA', NULL, NULL, NULL),
(232, '458', 1, 458, '750.0MB', '440', '3', 462, NULL, NULL, 453, 449, 'AWOOF DATA', NULL, NULL, NULL),
(233, '462', 1, 462, '500.0MB', '485', '7', 509, NULL, NULL, 500, 495, 'AWOOF DATA', NULL, NULL, NULL),
(234, '593', 1, 593, '2.5GB', '545', '1', 572, NULL, NULL, 561, 556, 'AWOOF DATA', NULL, NULL, NULL),
(235, '475', 1, 475, '1.5GB', '585', '2', 614, NULL, NULL, 603, 597, 'AWOOF DATA', NULL, NULL, NULL),
(236, '442', 1, 442, '2.0GB', '730', '2', 767, NULL, NULL, 752, 745, 'AWOOF DATA', NULL, NULL, NULL),
(237, '443', 1, 443, '3.2GB', '975', '2', 1024, NULL, NULL, 1004, 995, 'AWOOF DATA', NULL, NULL, NULL),
(238, '450', 1, 450, '1.5GB', '975', '7', 1024, NULL, NULL, 1004, 995, 'AWOOF DATA', NULL, NULL, NULL),
(239, '532', 1, 532, '3.5GB', '1470', '7', 1544, NULL, NULL, 1514, 1499, 'AWOOF DATA', NULL, NULL, NULL),
(240, '596', 1, 596, '7.0GB', '1765', '2', 1853, NULL, NULL, 1818, 1800, 'AWOOF DATA', NULL, NULL, NULL),
(241, '551', 1, 551, '6.75GB', '2940', '30', 3087, NULL, NULL, 3028, 2999, 'AWOOF DATA', NULL, NULL, NULL),
(242, '571', 1, 571, '5.0GB', '2940', '30', 3087, NULL, NULL, 3028, 2999, 'AWOOF DATA', NULL, NULL, NULL),
(243, '499', 1, 499, '11.0GB', '3430', '7', 3602, NULL, NULL, 3533, 3499, 'AWOOF DATA', NULL, NULL, NULL),
(244, '552', 1, 552, '14.5GB', '4900', '30', 5145, NULL, NULL, 5047, 4998, 'AWOOF DATA', NULL, NULL, NULL),
(245, '498', 1, 498, '20.0GB', '4900', '7', 5145, NULL, NULL, 5047, 4998, 'AWOOF DATA', NULL, NULL, NULL),
(246, '558', 1, 558, '500.0MB', '375', '7', 394, NULL, NULL, 386, 383, 'SME', NULL, NULL, NULL),
(247, '583', 1, 583, '1.0GB', '475', '7', 499, NULL, NULL, 489, 485, 'SME', NULL, NULL, NULL),
(248, '216', 1, 216, '500.0MB', '485', '7', 509, NULL, NULL, 500, 495, 'SME', NULL, NULL, NULL),
(249, '491', 1, 491, '1.0GB', '575', '30', 604, NULL, NULL, 592, 587, 'SME', NULL, NULL, NULL),
(250, '217', 1, 217, '1.0GB', '780', '7', 819, NULL, NULL, 803, 796, 'SME', NULL, NULL, NULL),
(251, '594', 1, 594, '2.0GB', '850', '7', 893, NULL, NULL, 876, 867, 'SME', NULL, NULL, NULL),
(252, '492', 1, 492, '2.0GB', '999', '30', 1049, NULL, NULL, 1029, 1019, 'SME', NULL, NULL, NULL),
(253, '218', 1, 218, '2.0GB', '1465', '30', 1538, NULL, NULL, 1509, 1494, 'SME', NULL, NULL, NULL),
(254, '569', 1, 569, '3.5GB', '1470', '7', 1544, NULL, NULL, 1514, 1499, 'SME', NULL, NULL, NULL),
(255, '493', 1, 493, '3.0GB', '1475', '30', 1549, NULL, NULL, 1519, 1505, 'SME', NULL, NULL, NULL),
(256, '494', 1, 494, '5.0GB', '1995', '30', 2095, NULL, NULL, 2055, 2035, 'SME', NULL, NULL, NULL),
(257, '219', 1, 219, '3.5GB', '2430', '30', 2552, NULL, NULL, 2503, 2479, 'SME', NULL, NULL, NULL),
(258, '220', 1, 220, '7.0GB', '3395', '30', 3565, NULL, NULL, 3497, 3463, 'SME', NULL, NULL, NULL),
(259, '221', 1, 221, '10.0GB', '4365', '30', 4583, NULL, NULL, 4496, 4452, 'SME', NULL, NULL, NULL),
(260, '579', 1, 579, '500.0MB', '385', '30', 404, NULL, NULL, 397, 393, 'DATA SHARE', NULL, NULL, NULL),
(261, '580', 1, 580, '1.0GB', '700', '30', 735, NULL, NULL, 721, 714, 'DATA SHARE', NULL, NULL, NULL),
(262, '595', 1, 595, '1.2GB', '735', '7', 772, NULL, NULL, 757, 750, 'DATA SHARE', NULL, NULL, NULL),
(263, '598', 1, 598, '1.2GB', '735', '7', 772, NULL, NULL, 757, 750, 'DATA SHARE', NULL, NULL, NULL),
(264, '546', 2, 546, '40.0MB', '50', '1', 53, NULL, NULL, 52, 51, 'GIFTING', NULL, NULL, NULL),
(265, '500', 2, 500, '100.0MB', '100', '1', 105, NULL, NULL, 103, 102, 'GIFTING', NULL, NULL, NULL),
(266, '501', 2, 501, '210.0MB', '200', '2', 210, NULL, NULL, 206, 204, 'GIFTING', NULL, NULL, NULL),
(267, '506', 2, 506, '1.0GB', '340', '1', 357, NULL, NULL, 350, 347, 'GIFTING', NULL, NULL, NULL),
(268, '502', 2, 502, '500.0MB', '485', '7', 509, NULL, NULL, 500, 495, 'GIFTING', NULL, NULL, NULL),
(269, '507', 2, 507, '1.0GB', '485', '1', 509, NULL, NULL, 500, 495, 'GIFTING', NULL, NULL, NULL),
(270, '508', 2, 508, '1.55GB', '582', '2', 611, NULL, NULL, 599, 594, 'GIFTING', NULL, NULL, NULL),
(271, '503', 2, 503, '1.1GB', '728', '14', 764, NULL, NULL, 750, 743, 'GIFTING', NULL, NULL, NULL),
(272, '509', 2, 509, '3.1GB', '970', '2', 1019, NULL, NULL, 999, 989, 'GIFTING', NULL, NULL, NULL),
(273, '511', 2, 511, '1.1GB', '970', '30', 1019, NULL, NULL, 999, 989, 'GIFTING', NULL, NULL, NULL),
(274, '512', 2, 512, '2.0GB', '1455', '30', 1528, NULL, NULL, 1499, 1484, 'GIFTING', NULL, NULL, NULL),
(275, '510', 2, 510, '3.9GB', '1455', '7', 1528, NULL, NULL, 1499, 1484, 'GIFTING', NULL, NULL, NULL),
(276, '513', 2, 513, '3.15GB', '1940', '30', 2037, NULL, NULL, 1998, 1979, 'GIFTING', NULL, NULL, NULL),
(277, '514', 2, 514, '4.25GB', '2425', '30', 2546, NULL, NULL, 2498, 2474, 'GIFTING', NULL, NULL, NULL),
(278, '515', 2, 515, '8.0GB', '2940', '30', 3087, NULL, NULL, 3028, 2999, 'GIFTING', NULL, NULL, NULL),
(279, '516', 2, 516, '10.5GB', '3880', '30', 4074, NULL, NULL, 3996, 3958, 'GIFTING', NULL, NULL, NULL),
(280, '517', 2, 517, '13.5GB', '4850', '30', 5093, NULL, NULL, 4996, 4947, 'GIFTING', NULL, NULL, NULL),
(281, '518', 2, 518, '26.0GB', '7760', '30', 8148, NULL, NULL, 7993, 7915, 'GIFTING', NULL, NULL, NULL),
(282, '519', 2, 519, '36.0GB', '9700', '30', 10185, NULL, NULL, 9991, 9894, 'GIFTING', NULL, NULL, NULL),
(283, '520', 2, 520, '62.0GB', '14550', '30', 15278, NULL, NULL, 14987, 14841, 'GIFTING', NULL, NULL, NULL),
(284, '521', 2, 521, '105.0GB', '19400', '30', 20370, NULL, NULL, 19982, 19788, 'GIFTING', NULL, NULL, NULL),
(285, '522', 2, 522, '1.0TB', '145500', '30', 152775, NULL, NULL, 149865, 148410, 'GIFTING', NULL, NULL, NULL),
(286, '305', 2, 305, '200.0MB', '90', '14', 95, NULL, NULL, 93, 92, 'CORPORATE GIFTING', NULL, NULL, NULL),
(287, '306', 2, 306, '500.0MB', '200', '30', 210, NULL, NULL, 206, 204, 'CORPORATE GIFTING', NULL, NULL, NULL),
(288, '559', 2, 559, '1.0GB', '279', '3', 293, NULL, NULL, 287, 285, 'CORPORATE GIFTING', NULL, NULL, NULL),
(289, '562', 2, 562, '1.0GB', '325', '7', 341, NULL, NULL, 335, 332, 'CORPORATE GIFTING', NULL, NULL, NULL),
(290, '307', 2, 307, '1.0GB', '410', '30', 431, NULL, NULL, 422, 418, 'CORPORATE GIFTING', NULL, NULL, NULL),
(291, '308', 2, 308, '2.0GB', '820', '30', 861, NULL, NULL, 845, 836, 'CORPORATE GIFTING', NULL, NULL, NULL),
(292, '560', 2, 560, '3.0GB', '837', '3', 879, NULL, NULL, 862, 854, 'CORPORATE GIFTING', NULL, NULL, NULL),
(293, '563', 2, 563, '3.0GB', '975', '7', 1024, NULL, NULL, 1004, 995, 'CORPORATE GIFTING', NULL, NULL, NULL),
(294, '309', 2, 309, '3.0GB', '1230', '30', 1292, NULL, NULL, 1267, 1255, 'CORPORATE GIFTING', NULL, NULL, NULL),
(295, '561', 2, 561, '5.0GB', '1395', '3', 1465, NULL, NULL, 1437, 1423, 'CORPORATE GIFTING', NULL, NULL, NULL),
(296, '564', 2, 564, '5.0GB', '1625', '7', 1706, NULL, NULL, 1674, 1658, 'CORPORATE GIFTING', NULL, NULL, NULL),
(297, '310', 2, 310, '5.0GB', '2050', '30', 2153, NULL, NULL, 2112, 2091, 'CORPORATE GIFTING', NULL, NULL, NULL),
(298, '311', 2, 311, '10.0GB', '4100', '30', 4305, NULL, NULL, 4223, 4182, 'CORPORATE GIFTING', NULL, NULL, NULL),
(299, '375', 2, 375, '750.0MB', '195', '1', 205, NULL, NULL, 201, 199, 'SME', NULL, NULL, NULL),
(300, '376', 2, 376, '1.5GB', '295', '1', 310, NULL, NULL, 304, 301, 'SME', NULL, NULL, NULL),
(301, '377', 2, 377, '2.5GB', '485', '2', 509, NULL, NULL, 500, 495, 'SME', NULL, NULL, NULL),
(302, '378', 2, 378, '10.0GB', '1950', '7', 2048, NULL, NULL, 2009, 1989, 'SME', NULL, NULL, NULL),
(303, '528', 3, 528, '150.0MB', '60', '1', 63, NULL, NULL, 62, 61, 'AWOOF DATA', NULL, NULL, NULL),
(304, '476', 3, 476, '300.0MB', '115', '2', 121, NULL, NULL, 118, 117, 'AWOOF DATA', NULL, NULL, NULL),
(305, '447', 3, 447, '600.0MB', '215', '2', 226, NULL, NULL, 221, 219, 'AWOOF DATA', NULL, NULL, NULL),
(306, '577', 3, 577, '1.5GB', '415', '1', 436, NULL, NULL, 427, 423, 'AWOOF DATA', NULL, NULL, NULL),
(307, '550', 3, 550, '2.0GB', '615', '2', 646, NULL, NULL, 633, 627, 'AWOOF DATA', NULL, NULL, NULL),
(308, '581', 3, 581, '3.0GB', '750', '2', 788, NULL, NULL, 773, 765, 'AWOOF DATA', NULL, NULL, NULL),
(309, '578', 3, 578, '5.0GB', '1515', '7', 1591, NULL, NULL, 1560, 1545, 'AWOOF DATA', NULL, NULL, NULL),
(310, '365', 3, 365, '10.0GB', '3015', '30', 3166, NULL, NULL, 3105, 3075, 'AWOOF DATA', NULL, NULL, NULL),
(311, '544', 3, 544, '75.0MB', '75', '1', 79, NULL, NULL, 77, 77, 'GIFTING', NULL, NULL, NULL),
(312, '479', 3, 479, '110.0MB', '100', '1', 105, NULL, NULL, 103, 102, 'GIFTING', NULL, NULL, NULL),
(313, '480', 3, 480, '230.0MB', '200', '2', 210, NULL, NULL, 206, 204, 'GIFTING', NULL, NULL, NULL),
(314, '542', 3, 542, '1.0GB', '300', '3', 315, NULL, NULL, 309, 306, 'GIFTING', NULL, NULL, NULL),
(315, '481', 3, 481, '300.0MB', '300', '2', 315, NULL, NULL, 309, 306, 'GIFTING', NULL, NULL, NULL),
(316, '584', 3, 584, '500.0MB', '343', '1', 360, NULL, NULL, 353, 350, 'GIFTING', NULL, NULL, NULL),
(317, '448', 3, 448, '1.0GB', '490', '1', 515, NULL, NULL, 505, 500, 'GIFTING', NULL, NULL, NULL),
(318, '478', 3, 478, '500.0MB', '490', '7', 515, NULL, NULL, 505, 500, 'GIFTING', NULL, NULL, NULL),
(319, '543', 3, 543, '1.5GB', '495', '7', 520, NULL, NULL, 510, 505, 'GIFTING', NULL, NULL, NULL),
(320, '410', 3, 410, '1.5GB', '588', '2', 617, NULL, NULL, 606, 600, 'GIFTING', NULL, NULL, NULL),
(321, '411', 3, 411, '3.0GB', '735', '2', 772, NULL, NULL, 757, 750, 'GIFTING', NULL, NULL, NULL),
(322, '414', 3, 414, '1.0GB', '785', '7', 824, NULL, NULL, 809, 801, 'GIFTING', NULL, NULL, NULL),
(323, '412', 3, 412, '3.2GB', '980', '2', 1029, NULL, NULL, 1009, 1000, 'GIFTING', NULL, NULL, NULL),
(324, '415', 3, 415, '1.5GB', '980', '7', 1029, NULL, NULL, 1009, 1000, 'GIFTING', NULL, NULL, NULL),
(325, '413', 3, 413, '5.0GB', '1470', '2', 1544, NULL, NULL, 1514, 1499, 'GIFTING', NULL, NULL, NULL),
(326, '416', 3, 416, '3.5GB', '1470', '7', 1544, NULL, NULL, 1514, 1499, 'GIFTING', NULL, NULL, NULL),
(327, '420', 3, 420, '2.0GB', '1470', '30', 1544, NULL, NULL, 1514, 1499, 'GIFTING', NULL, NULL, NULL),
(328, '421', 3, 421, '3.0GB', '1960', '30', 2058, NULL, NULL, 2019, 1999, 'GIFTING', NULL, NULL, NULL),
(329, '417', 3, 417, '6.0GB', '2450', '7', 2573, NULL, NULL, 2524, 2499, 'GIFTING', NULL, NULL, NULL),
(330, '422', 3, 422, '4.0GB', '2450', '30', 2573, NULL, NULL, 2524, 2499, 'GIFTING', NULL, NULL, NULL),
(331, '418', 3, 418, '10.0GB', '2940', '7', 3087, NULL, NULL, 3028, 2999, 'GIFTING', NULL, NULL, NULL),
(332, '423', 3, 423, '8.0GB', '2940', '30', 3087, NULL, NULL, 3028, 2999, 'GIFTING', NULL, NULL, NULL),
(333, '424', 3, 424, '10.0GB', '3920', '30', 4116, NULL, NULL, 4038, 3998, 'GIFTING', NULL, NULL, NULL),
(334, '419', 3, 419, '18.0GB', '4900', '7', 5145, NULL, NULL, 5047, 4998, 'GIFTING', NULL, NULL, NULL),
(335, '425', 3, 425, '13.0GB', '4900', '30', 5145, NULL, NULL, 5047, 4998, 'GIFTING', NULL, NULL, NULL),
(336, '426', 3, 426, '18.0GB', '5880', '30', 6174, NULL, NULL, 6056, 5998, 'GIFTING', NULL, NULL, NULL),
(337, '427', 3, 427, '25.0GB', '7840', '30', 8232, NULL, NULL, 8075, 7997, 'GIFTING', NULL, NULL, NULL),
(338, '428', 3, 428, '35.0GB', '9800', '30', 10290, NULL, NULL, 10094, 9996, 'GIFTING', NULL, NULL, NULL),
(339, '429', 3, 429, '60.0GB', '14700', '30', 15435, NULL, NULL, 15141, 14994, 'GIFTING', NULL, NULL, NULL),
(340, '430', 3, 430, '100.0GB', '19600', '30', 20580, NULL, NULL, 20188, 19992, 'GIFTING', NULL, NULL, NULL),
(341, '576', 3, 576, '160.0GB', '29400', '30', 30870, NULL, NULL, 30282, 29988, 'GIFTING', NULL, NULL, NULL),
(342, '431', 3, 431, '300.0GB', '49000', '90', 51450, NULL, NULL, 50470, 49980, 'GIFTING', NULL, NULL, NULL),
(343, '529', 3, 529, '350.0GB', '58800', '120', 61740, NULL, NULL, 60564, 59976, 'GIFTING', NULL, NULL, NULL),
(344, '530', 3, 530, '650.0GB', '98000', '30', 102900, NULL, NULL, 100940, 99960, 'GIFTING', NULL, NULL, NULL),
(345, '534', 3, 534, '500.0MB', '490', '7', 515, NULL, NULL, 505, 500, 'SME', NULL, NULL, NULL),
(346, '535', 3, 535, '1.0GB', '785', '7', 824, NULL, NULL, 809, 801, 'SME', NULL, NULL, NULL),
(347, '536', 3, 536, '2.0GB', '1470', '30', 1544, NULL, NULL, 1514, 1499, 'SME', NULL, NULL, NULL),
(348, '565', 3, 565, '3.5GB', '1470', '7', 1544, NULL, NULL, 1514, 1499, 'SME', NULL, NULL, NULL),
(349, '537', 3, 537, '3.0GB', '1960', '30', 2058, NULL, NULL, 2019, 1999, 'SME', NULL, NULL, NULL),
(350, '538', 3, 538, '4.0GB', '2450', '30', 2573, NULL, NULL, 2524, 2499, 'SME', NULL, NULL, NULL),
(351, '566', 3, 566, '6.0GB', '2450', '7', 2573, NULL, NULL, 2524, 2499, 'SME', NULL, NULL, NULL),
(352, '539', 3, 539, '8.0GB', '2940', '30', 3087, NULL, NULL, 3028, 2999, 'SME', NULL, NULL, NULL),
(353, '567', 3, 567, '10.0GB', '2940', '7', 3087, NULL, NULL, 3028, 2999, 'SME', NULL, NULL, NULL),
(354, '540', 3, 540, '10.0GB', '3920', '30', 4116, NULL, NULL, 4038, 3998, 'SME', NULL, NULL, NULL),
(355, '531', 3, 531, '13.0GB', '4900', '30', 5145, NULL, NULL, 5047, 4998, 'SME', NULL, NULL, NULL),
(356, '575', 3, 575, '18.0GB', '5880', '30', 6174, NULL, NULL, 6056, 5998, 'SME', NULL, NULL, NULL),
(357, '330', 4, 330, '500.0MB', '180', '30', 189, NULL, NULL, 185, 184, 'CORPORATE GIFTING', NULL, NULL, NULL),
(358, '332', 4, 332, '1.0GB', '360', '30', 378, NULL, NULL, 371, 367, 'CORPORATE GIFTING', NULL, NULL, NULL),
(359, '333', 4, 333, '1.5GB', '540', '30', 567, NULL, NULL, 556, 551, 'CORPORATE GIFTING', NULL, NULL, NULL),
(360, '334', 4, 334, '2.0GB', '720', '30', 756, NULL, NULL, 742, 734, 'CORPORATE GIFTING', NULL, NULL, NULL),
(361, '335', 4, 335, '3.0GB', '1080', '30', 1134, NULL, NULL, 1112, 1102, 'CORPORATE GIFTING', NULL, NULL, NULL),
(362, '337', 4, 337, '5.0GB', '1800', '30', 1890, NULL, NULL, 1854, 1836, 'CORPORATE GIFTING', NULL, NULL, NULL),
(363, '338', 4, 338, '10.0GB', '3600', '30', 3780, NULL, NULL, 3708, 3672, 'CORPORATE GIFTING', NULL, NULL, NULL),
(364, '340', 4, 340, '15.0GB', '5400', '30', 5670, NULL, NULL, 5562, 5508, 'CORPORATE GIFTING', NULL, NULL, NULL),
(365, '341', 4, 341, '20.0GB', '7200', '30', 7560, NULL, NULL, 7416, 7344, 'CORPORATE GIFTING', NULL, NULL, NULL);

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
(2, 'data_plans', '2025-12-19 11:47:27', 'failed', 0, 0, 0, 0, 'SQLSTATE[HY000]: General error: 1364 Field \'nId\' doesn\'t have a default value (Connection: mysql, SQL: insert into `network_ids` (`network`) values (MTN_PLAN))', 'uzobest', '2025-12-16 14:56:07', '2025-12-19 11:47:27'),
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
('FROdfK2s8IgogOeWnqpdBh6NsrdPUfQSAzP6tjSo', NULL, '37.120.213.204', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiMkZSajBOdmI1ZGxMSFV3Qk5yWlJGOUtnUjdGNXc4eUtESjVIUjlmbyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo0MjoiaHR0cHM6Ly93d3cudmFzdGxlYWRsdGQuY29tLm5nL2Z1bmQtd2FsbGV0Ijt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzY6Imh0dHBzOi8vd3d3LnZhc3RsZWFkbHRkLmNvbS5uZy9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1768744671),
('NUyhswwzTmskeDioxzITiwFMXjHKDFpb6qWJN0LJ', 8133051779, '37.120.213.205', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiY004bGw2eERmY1M0RDVFQjhzR3JlS2hzYm1FOElWTUt1amVBMzJJbiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo0MjoiaHR0cHM6Ly93d3cudmFzdGxlYWRsdGQuY29tLm5nL2Z1bmQtd2FsbGV0Ijt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzA6Imh0dHBzOi8vd3d3LnZhc3RsZWFkbHRkLmNvbS5uZyI7czo1OiJyb3V0ZSI7czo3OiJ3ZWxjb21lIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO3M6MTE6IjA4MTMzMDUxNzc5Ijt9', 1768747157),
('Pl8UvYgsUYfd8FbgIcoM43krOtBjeIs0yUnBuxsO', NULL, '188.43.69.4', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.7499.170 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidmxaaHdUaFRMemxHbHZWcUo4OVVoNjA0ellNb1ZLRGY5Z0lSZWNMQSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzY6Imh0dHBzOi8vd3d3LnZhc3RsZWFkbHRkLmNvbS5uZy9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1768744684),
('Wt2sFKgztf0fNWYt1PH6J96fugSr0tT3CHw2T2E2', 8039676596, '105.117.128.152', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiaTRjbDZVUDNjUG8xaXFEMUxjZGdKTU1VcU5NMERWdnlCcElGWHpWSSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vdmFzdGxlYWRsdGQuY29tLm5nL2Z1bmQtd2FsbGV0IjtzOjU6InJvdXRlIjtzOjExOiJmdW5kLXdhbGxldCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtzOjExOiIwODAzOTY3NjU5NiI7fQ==', 1768745249),
('WylLbzNRGXCaofU5FqSy9UXiHVmU6fwX3EGO3SYM', NULL, '105.117.128.152', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMHNMWm5PTlE5dzQ1UVpUZ3ZpUDdqeERjMXBCazdaUHh1R1dmdUlsbSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzI6Imh0dHBzOi8vdmFzdGxlYWRsdGQuY29tLm5nL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1768744933);

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

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`tId`, `sId`, `transref`, `servicename`, `servicedesc`, `amount`, `service_name`, `service_description`, `old_balance`, `new_balance`, `api_response`, `status`, `oldbal`, `newbal`, `profit`, `date`) VALUES
(1, 1, 'PAY_1765915889_6941bcf1b7182', 'Wallet Topup', 'Wallet funding of ₦20,000.00 via Paystack. Charges: ₦300.00', '19700', 'Wallet Topup', 'Wallet funding of ₦20,000.00 via Paystack. Charges: ₦300.00', 0.00, 19700.00, NULL, 0, '0', '19700', 0, '2025-12-16 20:13:15'),
(2, 2, 'PAY_1765992450_6942e80249605', 'Wallet Topup', 'Wallet funding of ₦5,000.00 via Paystack. Charges: ₦75.00', '4925', 'Wallet Topup', 'Wallet funding of ₦5,000.00 via Paystack. Charges: ₦75.00', 0.00, 4925.00, NULL, 0, '0', '4925', 0, '2025-12-17 17:28:03'),
(3, 2, 'PAY_1765994675_6942f0b33f520', 'Wallet Topup', 'Wallet funding of ₦5,000.00 via Paystack. Charges: ₦75.00', '4925', 'Wallet Topup', 'Wallet funding of ₦5,000.00 via Paystack. Charges: ₦75.00', 4925.00, 9850.00, NULL, 0, '4925', '9850', 0, '2025-12-17 18:04:49'),
(4, 2, '257411768047121', 'Data', 'Purchase of Airtel 1.5GB data bundle for phone number 08028276500', '310', 'data', 'Purchase of Airtel 1.5GB data bundle for phone number 08028276500', 9850.00, 9540.00, NULL, 1, '9850', '9540', 15, '2026-01-10 12:12:01'),
(5, 2, '125511768656310', 'Data', 'Purchase of Airtel 1.5GB data bundle for phone number 08028276500', '310', 'data', 'Purchase of Airtel 1.5GB data bundle for phone number 08028276500', 9850.00, 9540.00, NULL, 1, '9850', '9540', 15, '2026-01-17 13:25:10'),
(6, 2, 'PAY_1768745030_696ce8462658b', 'Wallet Topup', 'Wallet funding of ₦100.00 via Paystack. Charges: ₦1.50', '98.5', 'Wallet Topup', 'Wallet funding of ₦100.00 via Paystack. Charges: ₦1.50', 9850.00, 9948.50, NULL, 0, '9850', '9948.5', 0, '2026-01-18 14:07:28');

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
(1, 'Walshak Apollos', 'mikenenshimwa@gmail.com', '08133051779', 'Niger', 1, 0, 0, 1878, 1, 19700.00, 0.00, NULL, NULL, NULL, NULL, NULL, 'Zim7d4IPXE36bBCqnTScpHVDQyfG1LOwv2ReYJ0lN5g8h9FoUx176491495908133051779', NULL, NULL, '2026-01-18 12:58:03', NULL, '$2y$12$1cEBfULXodE7F8GmzXhiWuLssfaaHktPBH.OFWeC55WwVjd74Vqki', NULL, '2025-12-05 05:09:19', '2026-01-18 12:58:03', NULL, NULL, NULL),
(2, 'samuel david sambo', 'sammysambo88@gmail.com', '08039676596', 'Lagos', 1, 0, 0, 3589, 1, 9948.50, 0.00, NULL, NULL, NULL, NULL, NULL, 'ZNYyjdWEhlVxPg10LvO5fc3SIF4KsqQr9CJ7pX2MzDkbmGHaBT176591330708039676596', NULL, NULL, '2026-01-18 13:02:29', NULL, '$2y$12$YL/kRShRUqKeUrLYlIfqYuckU3clZFae6kUz/o59AFV3/tVt4gO0y', NULL, '2025-12-16 18:28:27', '2026-01-18 13:07:28', NULL, NULL, NULL),
(3, 'Ernest Inyang', 'ernestinyang748@gmail.com', '08063463088', 'Akwa Ibom', 1, 0, 0, 3530, 1, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, 'Fy6PJu9WL8TimC70OZBUS2a1n5lrQx4cj3vIogbqDYeNfRHXkA176596354208063463088', NULL, NULL, '2025-12-23 20:54:06', NULL, '$2y$12$wK1AHpXgiN1nTGwmYaA/eOpTDAj1ZO.QWjjBE7d4PBjTuWZEJGBDu', NULL, '2025-12-17 08:25:42', '2025-12-23 20:54:06', NULL, NULL, NULL),
(4, 'Ekomobong Idio', 'praiseraph@gmail.com', '08032032519', 'Enugu', 1, 0, 0, 2791, 1, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, 'jbYhCyuT3pfzOPQZ2Iod1v0tqAcJK8GrL9UgF76kmnHxVXls4e176597765508032032519', NULL, NULL, '2025-12-29 07:00:21', NULL, '$2y$12$o/Yr/2fPeCCO1FX7BO/Nr.uXnFUH5j6R0GqNrl5F0xoiCK3K1LWTi', NULL, '2025-12-17 12:20:55', '2025-12-29 07:00:21', NULL, NULL, NULL),
(5, 'Anna Danfulani', 'annadanfulani38@gmail.com', '08031852185', 'Akwa Ibom', 2, 0, 0, 2015, 1, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, 'JHMipF5kq6IgnUNXWthvlBTwyj2DzQmYb94GadO73KeVsEuZoS176730337608031852185', NULL, NULL, '2026-01-01 20:36:16', NULL, '$2y$12$2g79mX4qKlxcCQqVVjgjsOvjmedti8J8StGXqQ6OcOdBwQGddmAC.', NULL, '2026-01-01 20:36:16', '2026-01-01 20:36:16', NULL, NULL, NULL),
(6, 'Luwa Iyanu', 'ajuwonoluwa@gmail.com', '08125847393', 'Lagos', 1, 0, 0, 1223, 1, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, 'VehsTL1ZobdKEX2Y3F85B9jtmDJIPOpvzNQHUgaiuWRnkl7rA4176796891108125847393', NULL, NULL, '2026-01-09 13:28:31', NULL, '$2y$12$AXyKfI15kJx5onWZEiTDresUy.EwbPlVhkuyhM7iRU2x5SKemai5S', NULL, '2026-01-09 13:28:31', '2026-01-09 13:28:31', NULL, NULL, NULL);

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
  MODIFY `aId` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

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
  MODIFY `cId` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

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
  MODIFY `dId` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=366;

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
  MODIFY `tId` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `userlogin`
--
ALTER TABLE `userlogin`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
