-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 15, 2023 at 02:19 PM
-- Server version: 10.3.29-MariaDB-log
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dbdconce_apps`
--

-- --------------------------------------------------------

--
-- Table structure for table `airtime`
--

CREATE TABLE `airtime` (
  `aId` int(100) NOT NULL,
  `aNetwork` varchar(10) NOT NULL,
  `aBuyDiscount` float NOT NULL DEFAULT 96,
  `aUserDiscount` float NOT NULL,
  `aAgentDiscount` float NOT NULL,
  `aVendorDiscount` float NOT NULL,
  `aType` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `airtime`
--

INSERT INTO `airtime` (`aId`, `aNetwork`, `aBuyDiscount`, `aUserDiscount`, `aAgentDiscount`, `aVendorDiscount`, `aType`) VALUES
(1, '1', 96, 99, 98, 97, 'VTU'),
(2, '2', 96, 99, 98, 97, 'VTU'),
(3, '3', 96, 99, 98, 97, 'VTU'),
(4, '4', 96, 99, 98, 97, 'VTU'),
(5, '1', 96, 99, 98, 97, 'Share And Sell'),
(6, '2', 96, 99, 98, 97, 'Share And Sell'),
(7, '3', 96, 99, 98, 97, 'Share And Sell'),
(8, '4', 96, 99, 98, 97, 'Share And Sell');

-- --------------------------------------------------------

--
-- Table structure for table `airtimepinprice`
--

CREATE TABLE `airtimepinprice` (
  `aId` int(100) NOT NULL,
  `aNetwork` varchar(10) NOT NULL,
  `aUserDiscount` float NOT NULL,
  `aAgentDiscount` float NOT NULL,
  `aVendorDiscount` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `airtimepinprice`
--

INSERT INTO `airtimepinprice` (`aId`, `aNetwork`, `aUserDiscount`, `aAgentDiscount`, `aVendorDiscount`) VALUES
(1, '1', 99, 98, 97),
(2, '2', 99, 98, 97),
(3, '3', 99, 98, 97),
(4, '4', 99, 98, 97);

-- --------------------------------------------------------

--
-- Table structure for table `alphatopupprice`
--

CREATE TABLE `alphatopupprice` (
  `alphaId` int(200) NOT NULL,
  `buyingPrice` int(100) NOT NULL,
  `sellingPrice` int(100) NOT NULL,
  `agent` int(100) NOT NULL,
  `vendor` int(100) NOT NULL,
  `dPosted` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `apiconfigs`
--

CREATE TABLE `apiconfigs` (
  `aId` int(200) NOT NULL,
  `name` varchar(30) NOT NULL,
  `value` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `apiconfigs`
--

INSERT INTO `apiconfigs` (`aId`, `name`, `value`) VALUES
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
(13, 'mtnVtuKey', 'e5199989c9df406e8f78f9b255ab5620e131e2b4'),
(14, 'mtnVtuProvider', 'https://maskawasub.com/api/topup/'),
(15, 'mtnSharesellKey', 'xyC83kC2e7comxlCCBwDbG4dxBC28A9C32xCdBprsf3114vCABAACbFAA9At1692429001'),
(16, 'mtnSharesellProvider', 'https://maskawasub.com/api/topup/'),
(17, 'airtelVtuKey', ''),
(18, 'airtelVtuProvider', ''),
(19, 'airtelSharesellKey', ''),
(20, 'airtelSharesellProvider', ''),
(21, 'gloVtuKey', ''),
(22, 'gloVtuProvider', ''),
(23, 'gloSharesellKey', ''),
(24, 'gloSharesellProvider', ''),
(25, '9mobileVtuKey', ''),
(26, '9mobileVtuProvider', ''),
(27, '9mobileSharesellKey', ''),
(28, '9mobileSharesellProvider', ''),
(29, 'mtnSmeApi', ''),
(30, 'mtnSmeProvider', ''),
(31, 'mtnGiftingApi', ''),
(32, 'mtnGiftingProvider', ''),
(33, 'mtnCorporateApi', ''),
(34, 'mtnCorporateProvider', ''),
(35, 'airtelSmeApi', 'd2e6053122fa5cf21f'),
(36, 'airtelSmeProvider', 'https://maskawasub.com/api/data/'),
(37, 'airtelGiftingApi', 'd2e6053122fa5cf21f'),
(38, 'airtelGiftingProvider', 'https://maskawasub.com/api/data/'),
(39, 'airtelCorporateApi', 'd2e6053122fa5cf2'),
(40, 'airtelCorporateProvider', 'https://maskawasub.com/api/data/'),
(41, 'gloSmeApi', 'd2e6053122fa5cf2'),
(42, 'gloSmeProvider', 'https://maskawasub.com/api/data/'),
(43, 'gloGiftingApi', 'd2e6053122fa5cf2'),
(44, 'gloGiftingProvider', 'https://maskawasub.com/api/data/'),
(45, 'gloCorporateApi', 'd2e6053122fa5cf21f7d'),
(46, 'gloCorporateProvider', 'https://maskawasub.com/api/data/'),
(47, '9mobileSmeApi', 'd2e6053122fa5cf2'),
(48, '9mobileSmeProvider', 'https://maskawasub.com/api/data/'),
(49, '9mobileGiftingApi', 'd2e6053122fa5cf21f7de'),
(50, '9mobileGiftingProvider', 'https://maskawasub.com/api/data/'),
(51, '9mobileCorporateApi', 'd2e6053122fa5cf21f7'),
(52, '9mobileCorporateProvider', 'https://maskawasub.com/api/data/'),
(53, 'cableVerificationApi', 'Z'),
(54, 'cableVerificationProvider', 'https://maskawasub.com/api/validateiuc'),
(55, 'cableApi', 'V'),
(56, 'cableProvider', 'https://husmodataapi.com/api/cablesub/'),
(57, 'meterVerificationApi', 'G'),
(58, 'meterVerificationProvider', 'https://gongozconcept.com/api/validatemeter'),
(59, 'meterApi', 'N'),
(60, 'meterProvider', 'https://maskawasub.com/api/billpayment/'),
(61, 'examApi', 'T'),
(62, 'examProvider', 'https://maskawasub.com/api/epin/'),
(63, 'rechargePinApi', '54a5dadf75b948338ceaeb2c49484f0aa2ebbfdef7cc8f277b040082b841'),
(64, 'rechargePinProvider', 'ncwallet.ng/api/rechargepin/'),
(65, 'walletOneApi', 'd2e6053122fa5cf21f7de6bb389'),
(66, 'walletOneProvider', 'https://maskawasub.com/api/user/'),
(67, 'walletOneProviderName', 'Maskawasub'),
(68, 'walletTwoApi', 'G'),
(69, 'walletTwoProvider', 'https://topupmate.com/api/user/'),
(70, 'walletTwoProviderName', 'T'),
(71, 'walletThreeApi', 'CkgA5Ddt3B7bly53xAn9q1hbivA9zp4CAAcc7wCe80sHC4Cx3m612IJ62BCC1691503949'),
(72, 'walletThreeProvider', 'https://aabaxztech.com/api/user/'),
(73, 'walletThreeProviderName', 'H'),
(74, 'dataPinApi', 'H'),
(75, 'dataPinProvider', 'https://husmodata.com/api/data_card/'),
(76, 'alphaApi', 'J'),
(77, 'alphaProvider', 'Maskawasub.com.');

-- --------------------------------------------------------

--
-- Table structure for table `apilinks`
--

CREATE TABLE `apilinks` (
  `aId` int(200) NOT NULL,
  `name` varchar(30) NOT NULL,
  `value` varchar(100) DEFAULT NULL,
  `type` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `apilinks`
--

INSERT INTO `apilinks` (`aId`, `name`, `value`, `type`) VALUES
(1, 'Topupmate', 'https://topupmate.com/api/user/', 'Wallet'),
(2, 'Topupmate', 'https://topupmate.com/api/airtime/', 'Airtime'),
(3, 'Topupmate', 'https://topupmate.com/api/data/', 'Data'),
(4, 'Topupmate', 'https://topupmate.com/api/cabletv/verify/', 'CableVer'),
(5, 'Topupmate', 'https://topupmate.com/api/cabletv/', 'Cable'),
(6, 'Topupmate', 'https://topupmate.com/api/electricity/verify/', 'ElectricityVer'),
(7, 'Topupmate', 'https://topupmate.com/api/electricity/', 'Electricity'),
(8, 'Topupmate', 'https://topupmate.com/api/exam/', 'Exam'),
(9, 'N3T Data', 'https://n3tdata.com/api/user/', 'Wallet'),
(10, 'N3T Data', 'https://n3tdata.com/api/topup/', 'Airtime'),
(11, 'N3T Data', 'https://n3tdata.com/api/data/', 'Data'),
(12, 'N3T Data', 'https://n3tdata.com/api/cable/cable-validation', 'CableVer'),
(13, 'N3T Data', 'https://n3tdata.com/api/cable/', 'Cable'),
(14, 'N3T Data', 'https://n3tdata.com/api/bill/bill-validation', 'ElectricityVer'),
(15, 'N3T Data', 'https://n3tdata.com/api/bill/', 'Electricity'),
(16, 'N3T Data', 'https://n3tdata.com/api/exam/', 'Exam'),
(17, 'Bilalsadasub', 'https://bilalsadasub.com/api/user/', 'Wallet'),
(18, 'Bilalsadasub', 'https://bilalsadasub.com/api/topup/', 'Airtime'),
(19, 'Bilalsadasub', 'https://bilalsadasub.com/api/data/', 'Data'),
(20, 'Bilalsadasub', 'https://bilalsadasub.com/api/cable/cable-validation', 'CableVer'),
(21, 'Bilalsadasub', 'https://bilalsadasub.com/api/cable/', 'Cable'),
(22, 'Bilalsadasub', 'https://bilalsadasub.com/api/bill/bill-validation', 'ElectricityVer'),
(23, 'Bilalsadasub', 'https://bilalsadasub.com/api/bill/', 'Electricity'),
(24, 'Bilalsadasub', 'https://bilalsadasub.com/api/exam/', 'Exam'),
(25, 'Aabaxztech', 'https://aabaxztech.com/api/user/', 'Wallet'),
(26, 'Aabaxztech', 'https://aabaxztech.com/api/topup/', 'Airtime'),
(27, 'Aabaxztech', 'https://aabaxztech.com/api/data/', 'Data'),
(28, 'Aabaxztech', 'https://aabaxztech.com/api/validateiuc', 'CableVer'),
(29, 'Aabaxztech', 'https://aabaxztech.com/api/cablesub/', 'Cable'),
(30, 'Aabaxztech', 'https://aabaxztech.com/api/validatemeter', 'ElectricityVer'),
(31, 'Aabaxztech', 'https://aabaxztech.com/api/billpayment/', 'Electricity'),
(32, 'Aabaxztech', 'https://aabaxztech.com/api/epin/', 'Exam'),
(33, 'Maskawasub', 'https://maskawasub.com/api/user/', 'Wallet'),
(34, 'Maskawasub', 'https://maskawasub.com/api/topup/', 'Airtime'),
(35, 'Maskawasub', 'https://maskawasub.com/api/data/', 'Data'),
(36, 'Maskawasub', 'https://maskawasub.com/api/validateiuc', 'CableVer'),
(37, 'Maskawasub', 'https://maskawasub.com/api/cablesub/', 'Cable'),
(38, 'Maskawasub', 'https://maskawasub.com/api/validatemeter', 'ElectricityVer'),
(39, 'Maskawasub', 'https://maskawasub.com/api/billpayment/', 'Electricity'),
(40, 'Maskawasub', 'https://maskawasub.com/api/epin/', 'Exam'),
(41, 'Husmodataapi', 'https://husmodataapi.com/api/user/', 'Wallet'),
(42, 'Husmodataapi', 'https://husmodataapi.com/api/topup/', 'Airtime'),
(43, 'Husmodataapi', 'https://husmodataapi.com/api/data/', 'Data'),
(44, 'Husmodataapi', 'https://husmodataapi.com/api/validateiuc', 'CableVer'),
(45, 'Husmodataapi', 'https://husmodataapi.com/api/cablesub/', 'Cable'),
(46, 'Husmodataapi', 'https://husmodataapi.com/api/validatemeter', 'ElectricityVer'),
(47, 'Husmodataapi', 'https://husmodataapi.com/api/billpayment/', 'Electricity'),
(48, 'Husmodataapi', 'https://husmodataapi.com/api/epin/', 'Exam'),
(49, 'Gongozconcept', 'https://gongozconcept.com/api/user/', 'Wallet'),
(50, 'Gongozconcept', 'https://gongozconcept.com/api/topup/', 'Airtime'),
(51, 'Gongozconcept', 'https://gongozconcept.com/api/data/', 'Data'),
(52, 'Gongozconcept', 'https://gongozconcept.com/api/validateiuc', 'CableVer'),
(53, 'Gongozconcept', 'https://gongozconcept.com/api/cablesub/', 'Cable'),
(54, 'Gongozconcept', 'https://gongozconcept.com/api/validatemeter', 'ElectricityVer'),
(55, 'Gongozconcept', 'https://gongozconcept.com/api/billpayment/', 'Electricity'),
(56, 'Gongozconcept', 'https://gongozconcept.com/api/epin/', 'Exam'),
(57, 'Sabrdataapi', 'https://sabrdataapi.com/api/user/', 'Wallet'),
(58, 'Sabrdataapi', 'https://sabrdataapi.com/api/topup/', 'Airtime'),
(59, 'Sabrdataapi', 'https://sabrdataapi.com/api/data/', 'Data'),
(60, 'Sabrdataapi', 'https://sabrdataapi.com/ajax/validate_iuc', 'CableVer'),
(61, 'Sabrdataapi', 'https://sabrdataapi.com/api/cablesub/', 'Cable'),
(62, 'Sabrdataapi', 'https://sabrdataapi.com/api/validatemeter', 'ElectricityVer'),
(63, 'Sabrdataapi', 'https://sabrdataapi.com/api/billpayment/', 'Electricity'),
(64, 'Sabrdataapi', 'https://sabrdataapi.com/api/epin/', 'Exam'),
(65, 'Sabrdataapi', 'https://husmodata.com/api/user/', 'Wallet'),
(66, 'Sabrdataapi', 'https://husmodata.com/api/topup/', 'Airtime'),
(67, 'Sabrdataapi', 'https://husmodata.com/api/data/', 'Data'),
(68, 'Sabrdataapi', 'https://husmodata.com/ajax/validate_iuc', 'CableVer'),
(69, 'Sabrdataapi', 'https://husmodata.com/api/cablesub/', 'Cable'),
(70, 'Sabrdataapi', 'https://husmodata.com/api/validatemeter', 'ElectricityVer'),
(71, 'Sabrdataapi', 'https://husmodata.com/api/billpayment/', 'Electricity'),
(72, 'Sabrdataapi', 'https://husmodata.com/api/epin/', 'Exam'),
(73, 'Beensade', 'https://husmodata.com/api/data_card/', 'Data Pin'),
(74, 'Sabrdataapi', 'https://gladtidings.com/api/user/', 'Wallet'),
(75, 'Sabrdataapi', 'https://gladtidings.com/api/topup/', 'Airtime'),
(76, 'Sabrdataapi', 'https://gladtidings.com/api/data/', 'Data'),
(77, 'Sabrdataapi', 'https://gladtidings.com/ajax/validate_iuc', 'CableVer'),
(78, 'Sabrdataapi', 'https://gladtidings.com/api/cablesub/', 'Cable'),
(79, 'Sabrdataapi', 'https://gladtidings.com/api/validatemeter', 'ElectricityVer'),
(80, 'Sabrdataapi', 'https://gladtidings.com/api/billpayment/', 'Electricity'),
(81, 'Sabrdataapi', 'https://gladtidings.com/api/epin/', 'Exam'),
(82, 'Beensade', 'https://gladtidings.com/api/data_card/', 'Data Pin');

-- --------------------------------------------------------

--
-- Table structure for table `cableid`
--

CREATE TABLE `cableid` (
  `cId` int(11) NOT NULL,
  `cableid` varchar(10) DEFAULT NULL,
  `provider` varchar(10) NOT NULL,
  `providerStatus` varchar(10) NOT NULL DEFAULT 'On'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cableid`
--

INSERT INTO `cableid` (`cId`, `cableid`, `provider`, `providerStatus`) VALUES
(1, '1', 'GOTV', 'On'),
(2, '2', 'DSTV', 'On'),
(3, '3', 'STARTIMES', 'On');

-- --------------------------------------------------------

--
-- Table structure for table `cableplans`
--

CREATE TABLE `cableplans` (
  `cpId` int(100) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` varchar(255) NOT NULL,
  `userprice` varchar(255) NOT NULL,
  `agentprice` varchar(255) NOT NULL,
  `vendorprice` varchar(255) NOT NULL,
  `planid` varchar(255) NOT NULL,
  `type` varchar(255) DEFAULT NULL,
  `cableprovider` tinyint(10) NOT NULL,
  `day` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cableplans`
--

INSERT INTO `cableplans` (`cpId`, `name`, `price`, `userprice`, `agentprice`, `vendorprice`, `planid`, `type`, `cableprovider`, `day`) VALUES
(1, 'Gotv Smallie', '900', '1000', '970', '950', '55', NULL, 1, '30'),
(2, 'Gotv Jinja', '1900', '2000', '1950', '1930', '61', NULL, 1, '30'),
(3, 'Gotv Jolli', '2800', '3000', '2900', '2900', '60', NULL, 1, '30'),
(4, 'Gotv Max', '4150', '4300', '4250', '4200', '59', NULL, 1, '30'),
(5, 'Gotv Supa', '5500', '5600', '5550', '5550', '58', NULL, 1, '30'),
(6, 'Dstv Padi', '2150', '2300', '2200', '2170', '1', NULL, 2, '30'),
(7, 'Dstv Yanga', '2950', '3100', '3000', '2970', '2', NULL, 2, '30'),
(8, 'Dstv Confam', '5300', '5400', '5370', '5350', '3', NULL, 2, '30'),
(9, 'Dstv Compact', '9000', '9100', '9070', '9050', '4', NULL, 2, '30'),
(10, 'Dstv Compact Plus', '14250', '14400', '14350', '14300', '7', NULL, 2, '30'),
(11, 'Dstv Premiun', '21000', '21100', '21050', '21030', '5', NULL, 2, '30'),
(12, 'Dstv Premiun Asia', '23500', '23600', '23550', '23530', '9', NULL, 2, '30'),
(13, 'Nova', '900', '1000', '950', '930', '66', NULL, 3, '30'),
(14, 'Basic (Antenna)', '1700', '1800', '1750', '1730', '67', NULL, 3, '30'),
(15, 'Smart (Dish)', '2600', '2700', '2750', '2730', '68', NULL, 3, '30'),
(16, 'Classic (Antenna)', '2750', '2850', '2800', '2780', '69', NULL, 3, '30'),
(17, 'Super (Dish)', '4900', '5000', '4950', '4930', '70', NULL, 3, '30');

-- --------------------------------------------------------

--
-- Table structure for table `contact`
--

CREATE TABLE `contact` (
  `msgId` int(200) NOT NULL,
  `sId` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `contact` varchar(200) NOT NULL,
  `subject` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `dPosted` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `contact`
--

INSERT INTO `contact` (`msgId`, `sId`, `name`, `contact`, `subject`, `message`, `dPosted`) VALUES
(1, 0, 'Topupmate', 'ibyusuf31@gmail.com', 'Testing', 'Test', '2022-06-21 17:06:56'),
(2, 0, 'Ibrahim Ahmed', 'ibyusuf31@gmail.com', 'Test From Landing Page', 'Test From Landing Page', '2022-06-23 13:08:11');

-- --------------------------------------------------------

--
-- Table structure for table `datapins`
--

CREATE TABLE `datapins` (
  `dpId` int(100) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` varchar(255) NOT NULL,
  `userprice` varchar(255) NOT NULL,
  `agentprice` varchar(255) NOT NULL,
  `vendorprice` varchar(255) NOT NULL,
  `planid` varchar(255) NOT NULL,
  `type` varchar(255) DEFAULT NULL,
  `datanetwork` tinyint(10) NOT NULL,
  `day` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `datapins`
--

INSERT INTO `datapins` (`dpId`, `name`, `price`, `userprice`, `agentprice`, `vendorprice`, `planid`, `type`, `datanetwork`, `day`) VALUES
(1, '1.5GB', '200', '300', '300', '300', '1', 'Gifting', 1, '30'),
(2, '500 MB', '108', '120', '120', '120', '2', 'SME', 1, '30'),
(3, '1GB', '215', '220', '220', '220', '3', 'SME', 1, '30'),
(4, '2GB', '430', '450', '450', '450', '4', 'SME', 1, '30'),
(5, '3GB', '645', '650', '650', '650', '5', 'SME', 1, '30'),
(6, '5GB', '1075', '1090', '1090', '1090', '6', 'SME', 1, '30'),
(7, '10GB', '2150', '2200', '2200', '2200', '7', 'SME', 1, '30'),
(8, '500 MB', '100', '120', '120', '120', '8', 'Corporate', 2, '30'),
(9, '1GB', '200', '220', '220', '220', '9', 'Corporate', 2, '30'),
(10, '2GB', '400', '420', '420', '420', '10', 'Corporate', 2, '30'),
(11, '5GB', '1000', '1200', '1200', '1200', '11', 'Corporate', 2, '30'),
(12, '10GB', '2000', '2200', '2200', '2200', '12', 'Corporate', 2, '30');

-- --------------------------------------------------------

--
-- Table structure for table `dataplans`
--

CREATE TABLE `dataplans` (
  `pId` int(100) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` varchar(255) NOT NULL,
  `userprice` varchar(255) NOT NULL,
  `agentprice` varchar(255) NOT NULL,
  `vendorprice` varchar(255) NOT NULL,
  `planid` varchar(255) NOT NULL,
  `type` varchar(255) DEFAULT NULL,
  `datanetwork` tinyint(10) NOT NULL,
  `day` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `dataplans`
--

INSERT INTO `dataplans` (`pId`, `name`, `price`, `userprice`, `agentprice`, `vendorprice`, `planid`, `type`, `datanetwork`, `day`) VALUES
(1, '500 MB', '115', '150', '120', '110', '1', 'SME', 1, '30'),
(2, '1GB', '225', '250', '230', '230', '2', 'SME', 1, '30'),
(3, '2GB', '450', '500', '480', '450', '3', 'SME', 1, '30'),
(4, '3GB', '675', '700', '685', '650', '4', 'SME', 1, '30'),
(5, '5GB', '1125', '1200', '1150', '1100', '5', 'SME', 1, '30'),
(6, '10GB', '2250', '2500', '2350', '2300', '6', 'SME', 1, '30'),
(7, '200MB', '46', '100', '100', '100', '325', 'Corporate', 2, '30');

-- --------------------------------------------------------

--
-- Table structure for table `datatokens`
--

CREATE TABLE `datatokens` (
  `tId` int(100) NOT NULL,
  `sId` int(100) NOT NULL,
  `tRef` varchar(255) NOT NULL,
  `business` varchar(30) NOT NULL,
  `network` varchar(30) NOT NULL,
  `datasize` varchar(30) NOT NULL,
  `quantity` int(100) NOT NULL,
  `serial` text NOT NULL,
  `tokens` text NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `electricityid`
--

CREATE TABLE `electricityid` (
  `eId` int(11) NOT NULL,
  `electricityid` varchar(50) DEFAULT NULL,
  `provider` varchar(50) NOT NULL,
  `abbreviation` varchar(5) NOT NULL,
  `providerStatus` varchar(10) NOT NULL DEFAULT 'On'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `electricityid`
--

INSERT INTO `electricityid` (`eId`, `electricityid`, `provider`, `abbreviation`, `providerStatus`) VALUES
(1, '1', 'Ikeja Electric', 'IE', 'On'),
(2, '2', 'Eko Electric', 'EKEDC', 'On'),
(3, '3', 'Kano Electric', 'KEDCO', 'On'),
(4, '4', 'Port Harcourt Electric', 'PHEDC', 'On'),
(5, '5', 'Jos Electric', 'JED', 'On'),
(6, '6', 'Ibadan Electric', 'IBEDC', 'On'),
(7, '7', 'Kaduna Electric', 'KEDC', 'On'),
(8, '8', 'Abuja Electric', 'AEDC', 'On'),
(9, '9', 'Enugu Electric', 'ENUGU', 'On'),
(10, '10', 'Benin Electric', 'BENIN', 'On'),
(11, '11', 'Yola Electric', 'YOLA', 'On');

-- --------------------------------------------------------

--
-- Table structure for table `examid`
--

CREATE TABLE `examid` (
  `eId` int(11) NOT NULL,
  `examid` varchar(10) DEFAULT NULL,
  `provider` varchar(50) NOT NULL,
  `price` int(20) NOT NULL DEFAULT 0,
  `buying_price` int(20) NOT NULL DEFAULT 0,
  `providerStatus` varchar(10) NOT NULL DEFAULT 'On'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `examid`
--

INSERT INTO `examid` (`eId`, `examid`, `provider`, `price`, `buying_price`, `providerStatus`) VALUES
(1, '1', 'WAEC', 1800, 0, 'On'),
(2, '2', 'NECO', 800, 0, 'On'),
(3, '3', 'NABTEB', 950, 0, 'On');

-- --------------------------------------------------------

--
-- Table structure for table `networkid`
--

CREATE TABLE `networkid` (
  `nId` int(11) NOT NULL,
  `networkid` varchar(10) NOT NULL,
  `smeId` varchar(10) NOT NULL,
  `giftingId` varchar(10) NOT NULL,
  `corporateId` varchar(10) NOT NULL,
  `vtuId` varchar(10) NOT NULL,
  `sharesellId` varchar(10) NOT NULL,
  `network` varchar(20) NOT NULL,
  `networkStatus` varchar(10) NOT NULL DEFAULT 'Off',
  `vtuStatus` varchar(10) NOT NULL DEFAULT 'Off',
  `sharesellStatus` varchar(10) NOT NULL DEFAULT 'Off',
  `airtimepinStatus` varchar(10) NOT NULL DEFAULT 'Off',
  `smeStatus` varchar(10) NOT NULL DEFAULT 'Off',
  `giftingStatus` varchar(10) NOT NULL DEFAULT 'Off',
  `corporateStatus` varchar(10) NOT NULL DEFAULT 'Off',
  `datapinStatus` varchar(10) NOT NULL DEFAULT 'Off'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `networkid`
--

INSERT INTO `networkid` (`nId`, `networkid`, `smeId`, `giftingId`, `corporateId`, `vtuId`, `sharesellId`, `network`, `networkStatus`, `vtuStatus`, `sharesellStatus`, `airtimepinStatus`, `smeStatus`, `giftingStatus`, `corporateStatus`, `datapinStatus`) VALUES
(1, '1', '1', '1', '1', '1', '1', 'MTN', 'On', 'On', 'On', 'On', 'On', 'On', 'On', 'On'),
(2, '2', '2', '2', '2', '2', '2', 'GLO', 'On', 'On', 'On', 'On', 'On', 'On', 'On', 'On'),
(3, '3', '3', '3', '3', '3', '3', '9MOBILE', 'On', 'On', 'On', 'On', 'On', 'On', 'On', 'On'),
(4, '4', '4', '4', '4', '4', '4', 'AIRTEL', 'On', 'On', 'On', 'On', 'On', 'On', 'On', 'On');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `msgId` int(200) NOT NULL,
  `msgfor` tinyint(4) NOT NULL,
  `subject` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `status` tinyint(3) NOT NULL DEFAULT 0,
  `dPosted` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`msgId`, `msgfor`, `subject`, `message`, `status`, `dPosted`) VALUES
(1, 3, 'Welcome Message', 'Hi There! You are welcome, we are your one-stop platform for all for bills payment, airtime, data plans, and cable tv subscription. All our services are available to you at a discount rate. Our customer support team is available to you 24/7.', 0, '2022-06-21 17:05:02');

-- --------------------------------------------------------

--
-- Table structure for table `rechargepins`
--

CREATE TABLE `rechargepins` (
  `Id` int(100) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` varchar(255) NOT NULL,
  `userprice` varchar(255) NOT NULL,
  `agentprice` varchar(255) NOT NULL,
  `vendorprice` varchar(255) NOT NULL,
  `planid` varchar(255) NOT NULL,
  `type` varchar(255) DEFAULT NULL,
  `datanetwork` tinyint(10) NOT NULL,
  `day` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `rechargepins`
--

INSERT INTO `rechargepins` (`Id`, `name`, `price`, `userprice`, `agentprice`, `vendorprice`, `planid`, `type`, `datanetwork`, `day`) VALUES
(1, '1.5GB', '309', '320', '300', '300', '71', 'Gifting', 1, '30'),
(2, '500 MB', '108', '120', '120', '120', '2', 'SME', 1, '30'),
(3, '1GB', '215', '220', '220', '220', '3', 'SME', 1, '30'),
(4, '2GB', '430', '450', '450', '450', '4', 'SME', 1, '30'),
(5, '3GB', '645', '650', '650', '650', '5', 'SME', 1, '30'),
(6, '5GB', '1075', '1090', '1090', '1090', '6', 'SME', 1, '30'),
(7, '10GB', '2150', '2200', '2200', '2200', '7', 'SME', 1, '30'),
(8, '500 MB', '100', '120', '120', '120', '8', 'Corporate', 2, '30'),
(9, '1GB', '200', '220', '220', '220', '9', 'Corporate', 2, '30'),
(10, '2GB', '400', '420', '420', '420', '10', 'Corporate', 2, '30'),
(11, '5GB', '1000', '1200', '1200', '1200', '11', 'Corporate', 2, '30'),
(12, '10GB', '2000', '2200', '2200', '2200', '12', 'Corporate', 2, '30');

-- --------------------------------------------------------

--
-- Table structure for table `rechargetokens`
--

CREATE TABLE `rechargetokens` (
  `tId` int(100) NOT NULL,
  `sId` int(100) NOT NULL,
  `tRef` varchar(255) NOT NULL,
  `business` varchar(30) NOT NULL,
  `network` varchar(30) NOT NULL,
  `datasize` text NOT NULL,
  `quantity` int(100) NOT NULL,
  `serial` text NOT NULL,
  `tokens` text NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `rechargetokens`
--

INSERT INTO `rechargetokens` (`tId`, `sId`, `tRef`, `business`, `network`, `datasize`, `quantity`, `serial`, `tokens`, `date`) VALUES
(0, 77, 'TR-1690644948-94f834ec31ccf203', 'Okay ', 'GLO', 'tyyuuiibbh', 1, '5434556787_90', '2541455678', '2023-07-29 15:35:49'),
(0, 77, 'TR-1690645788-b3b83a81c3223891', 'Dannvtu', '9MOBILE', 'tyyuuiibbh', 1, '5434556787_90', '2541455678', '2023-07-29 15:49:48'),
(0, 77, 'TR-1690645884-306e456321a34474', 'Dannvtu', 'AIRTEL', 'tyyuuiibbh', 1, '5434556787_90', '2541455678', '2023-07-29 15:51:24'),
(0, 77, 'TR-1690646935-e47d9d8ebb1decd7', 'Wanda ', 'MTN', '*556#', 1, '5434556787_90', '2541455678', '2023-07-29 16:08:55'),
(0, 77, 'TR-1690647669-44f81c65e46ff8c8', 'Wanda ', 'AIRTEL', '*556#', 1, '5434556787_90', '2541455678', '2023-07-29 16:21:09'),
(0, 77, 'TR-1690648992-d5dd340c81e2ef44', 'Okay ', 'MTN', '*556#', 1, '5434556787_90', '2541455678', '2023-07-29 16:43:12'),
(0, 77, 'TR-1690653960-16bb705c4a8a3fc6', 'Okay ', 'MTN', '*556#', 1, '5434556787_90', '2541455678', '2023-07-29 18:06:00'),
(0, 77, 'TR-1690654863-30b4d06f1f0c4399', 'Okay ', 'MTN', '*556#', 1, '5434556787_90', '2541455678', '2023-07-29 18:21:03'),
(0, 77, 'TR-1690661407-18dbcb594684b271', 'Okay ', 'MTN', '*556#', 1, '5434556787_90', '2541455678', '2023-07-29 20:10:08'),
(0, 77, 'TR-1690661615-cd48ae1cbae97146', 'Dannvtu', 'MTN', '*555*PIN#', 1, '00000024173063480', '18688479088314361', '2023-07-29 20:13:49'),
(0, 77, 'TR-1690803558-d2373eabf4cecd5a', 'Okay ', 'MTN', '*556#', 1, '5434556787_90', '2541455678', '2023-07-31 11:39:18'),
(0, 77, 'TR-1690803762-d014610287bd2362', 'Okay ', 'MTN', '*556#', 1, '5434556787_90', '2541455678', '2023-07-31 11:42:42'),
(0, 77, 'TR-1690804084-bf11c6f1170cb7eb', 'Wanda ', 'MTN', '*556#', 1, '5434556787_90', '2541455678', '2023-07-31 11:48:04'),
(0, 77, '1690804205', 'name_on_card', 'MTN', '*556#', 1, '5434556787_90', '2541455678', '2023-07-31 11:50:05'),
(0, 77, '1690806349', 'name_on_card', 'MTN', '*556#', 1, '5434556787_90', '2541455678', '2023-07-31 12:25:50'),
(0, 71, 'TR-1690852007-50706da8d63f2b1d', 'Dann', 'MTN', '*556#', 1, '5434556787_90', '2541455678', '2023-08-01 01:06:47'),
(0, 77, 'TR-1690858909-59c4ae571046c664', 'Okay ', 'MTN', '*556#', 1, '5434556787_90', '2541455678', '2023-08-01 03:01:49'),
(0, 77, 'TR-1690907057-2d47709da056c2e1', 'Dannvtu', 'MTN', '*556#', 1, '5434556787_90', '2541455678', '2023-08-01 16:24:17'),
(0, 77, '1690909102', 'name_on_card', 'MTN', '*556#', 1, '5434556787_90', '2541455678', '2023-08-01 16:58:22'),
(0, 94, 'TR-1691053036-d1a325cd5f9d1bc9', 'Wanda ', 'MTN', '*556#', 1, '5434556787_90', '2541455678', '2023-08-03 08:57:16'),
(0, 94, 'TR-1691150915-95bfa3b04195baf8', 'Okay ', 'MTN', '*556#', 1, '5434556787_90', '2541455678', '2023-08-04 12:08:35'),
(0, 94, 'TR-1691152570-f44b32483aa8664a', '100', 'MTN', '*556#', 1, '5434556787_90', '2541455678', '2023-08-04 12:36:10'),
(0, 94, 'TR-1691152776-f92c2f46cd85288b', '100', 'MTN', '*556#', 1, '5434556787_90', '2541455678', '2023-08-04 12:39:36'),
(0, 94, 'TR-1691153834-6315d8130c7a403f', '100', 'MTN', '*556#', 1, '5434556787_90', '2541455678', '2023-08-04 12:57:14');

-- --------------------------------------------------------

--
-- Table structure for table `sitesettings`
--

CREATE TABLE `sitesettings` (
  `sId` int(200) NOT NULL,
  `sitename` varchar(20) DEFAULT NULL,
  `siteurl` varchar(100) DEFAULT NULL,
  `agentupgrade` varchar(20) DEFAULT NULL,
  `vendorupgrade` varchar(20) DEFAULT NULL,
  `apidocumentation` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `whatsapp` varchar(20) DEFAULT NULL,
  `whatsappgroup` varchar(100) DEFAULT NULL,
  `facebook` varchar(10) DEFAULT NULL,
  `twitter` varchar(200) DEFAULT NULL,
  `instagram` varchar(200) DEFAULT NULL,
  `telegram` varchar(100) DEFAULT NULL,
  `referalupgradebonus` float NOT NULL DEFAULT 100,
  `referalairtimebonus` float NOT NULL DEFAULT 1,
  `referaldatabonus` float NOT NULL DEFAULT 1,
  `referalwalletbonus` float NOT NULL DEFAULT 1,
  `referalcablebonus` float NOT NULL DEFAULT 1,
  `referalexambonus` float NOT NULL DEFAULT 1,
  `referalmeterbonus` float NOT NULL DEFAULT 1,
  `wallettowalletcharges` float NOT NULL DEFAULT 50,
  `sitecolor` varchar(10) NOT NULL DEFAULT '#0000e6',
  `logindesign` varchar(10) NOT NULL DEFAULT '5',
  `homedesign` varchar(10) NOT NULL DEFAULT '5',
  `notificationStatus` varchar(5) NOT NULL DEFAULT 'Off',
  `accountname` varchar(50) DEFAULT NULL,
  `accountno` varchar(15) DEFAULT NULL,
  `bankname` varchar(20) DEFAULT NULL,
  `electricitycharges` varchar(5) DEFAULT NULL,
  `airtimemin` varchar(10) NOT NULL DEFAULT '50',
  `airtimemax` varchar(10) NOT NULL DEFAULT '500'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `sitesettings`
--

INSERT INTO `sitesettings` (`sId`, `sitename`, `siteurl`, `agentupgrade`, `vendorupgrade`, `apidocumentation`, `phone`, `email`, `whatsapp`, `whatsappgroup`, `facebook`, `twitter`, `instagram`, `telegram`, `referalupgradebonus`, `referalairtimebonus`, `referaldatabonus`, `referalwalletbonus`, `referalcablebonus`, `referalexambonus`, `referalmeterbonus`, `wallettowalletcharges`, `sitecolor`, `logindesign`, `homedesign`, `notificationStatus`, `accountname`, `accountno`, `bankname`, `electricitycharges`, `airtimemin`, `airtimemax`) VALUES
(1, 'DBDCONCEPTS', 'https://dbdconcepts.com.ng/', '1000', '2000', 'https://maskawawasub.com/api/', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 100, 1, 1, 1, 1, 1, 1, 50, '#0000e6', '5', '5', 'On', 'Fredrick izuogu ', '2266972561', 'Uba', '50', '50', '5000');

-- --------------------------------------------------------

--
-- Table structure for table `subscribers`
--

CREATE TABLE `subscribers` (
  `sId` int(200) NOT NULL,
  `sApiKey` varchar(200) NOT NULL,
  `sFname` varchar(50) NOT NULL,
  `sLname` varchar(50) NOT NULL,
  `sEmail` varchar(50) DEFAULT NULL,
  `sPhone` varchar(20) NOT NULL,
  `sPass` varchar(150) NOT NULL,
  `sState` varchar(50) NOT NULL,
  `sPin` int(10) NOT NULL DEFAULT 1234,
  `sPinStatus` tinyint(3) DEFAULT 0,
  `sType` tinyint(10) NOT NULL DEFAULT 1,
  `sWallet` float NOT NULL DEFAULT 0,
  `sRefWallet` float NOT NULL DEFAULT 0,
  `sBankNo` varchar(20) DEFAULT NULL,
  `sRolexBank` varchar(20) DEFAULT NULL,
  `sSterlingBank` varchar(20) DEFAULT NULL,
  `sFidelityBank` varchar(20) DEFAULT NULL,
  `sBankName` varchar(30) DEFAULT NULL,
  `sRegStatus` tinyint(5) NOT NULL DEFAULT 3,
  `sVerCode` smallint(20) NOT NULL DEFAULT 0,
  `sRegDate` datetime NOT NULL DEFAULT current_timestamp(),
  `sLastActivity` datetime DEFAULT NULL,
  `sReferal` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `subscribers`
--

INSERT INTO `subscribers` (`sId`, `sApiKey`, `sFname`, `sLname`, `sEmail`, `sPhone`, `sPass`, `sState`, `sPin`, `sPinStatus`, `sType`, `sWallet`, `sRefWallet`, `sBankNo`, `sRolexBank`, `sSterlingBank`, `sFidelityBank`, `sBankName`, `sRegStatus`, `sVerCode`, `sRegDate`, `sLastActivity`, `sReferal`) VALUES
(8, 'Bqtn6pskdC84i7aAC5IBCb3A0rby8fCC9lChCx39AAx2DF1gA3c651BAdmxx1698342265', 'ADERIBIGBE', 'ISAAC PAMILERIN', 'aderibigbe959@gmail.com', '08063098058', 'b536e98f06', 'Osun', 1234, 0, 1, 0, 0, '9521303625', '6418929071', '9364437946', '4556710279', 'Wema bank', 0, 7540, '2023-10-26 18:44:25', '2023-11-04 07:16:32', '');

-- --------------------------------------------------------

--
-- Table structure for table `sysusers`
--

CREATE TABLE `sysusers` (
  `sysId` int(100) NOT NULL,
  `sysName` varchar(50) NOT NULL,
  `sysRole` tinyint(2) NOT NULL,
  `sysUsername` varchar(20) NOT NULL,
  `sysToken` varchar(30) NOT NULL,
  `sysStatus` tinyint(2) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `sysusers`
--

INSERT INTO `sysusers` (`sysId`, `sysName`, `sysRole`, `sysUsername`, `sysToken`, `sysStatus`) VALUES
(1, 'DBDCONCEPTS', 1, 'admin', 'admin', 0);

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `tId` int(200) NOT NULL,
  `sId` int(100) NOT NULL,
  `transref` varchar(255) NOT NULL,
  `servicename` varchar(100) NOT NULL,
  `servicedesc` varchar(255) NOT NULL,
  `amount` varchar(100) NOT NULL,
  `status` tinyint(5) NOT NULL,
  `oldbal` varchar(100) NOT NULL,
  `newbal` varchar(100) NOT NULL,
  `profit` float NOT NULL DEFAULT 0,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`tId`, `sId`, `transref`, `servicename`, `servicedesc`, `amount`, `status`, `oldbal`, `newbal`, `profit`, `date`) VALUES
(6, 0, 'MNFY|07|20231001174701|000367', 'Wallet Topup', 'Wallet funding of N500 via Monnify bank transfer with a service charges of 1.075%. You wallet have been credited with 494.625', '494.625', 0, '0', '494.625', 0, '2023-10-01 16:47:02'),
(7, 7, '25661696938713', 'Wallet Credit', 'Wallet Credit of N1000 for user aderibigbe959@gmail.com. Reason: bonus', '1000', 0, '0', '1000', 0, '2023-10-10 12:51:53'),
(8, 7, '96301697752820', 'Data', 'Purchase of GLO 200MB Corporate 30 Days Plan for phone number 07054107893', '100', 0, '1000', '900', 54, '2023-10-19 23:01:05');

-- --------------------------------------------------------

--
-- Table structure for table `userlogin`
--

CREATE TABLE `userlogin` (
  `id` int(200) NOT NULL,
  `user` int(100) NOT NULL,
  `token` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `userlogin`
--

INSERT INTO `userlogin` (`id`, `user`, `token`) VALUES
(10, 7, '1696933404yxsroGEwAB192'),
(11, 7, '1696934867xFDrAqCJny670'),
(12, 7, '1696938397pkDoEqHFnJ112'),
(13, 7, '1697752230AInmDyxtFo853'),
(14, 7, '1697753294JpzxoyFrkq437'),
(15, 7, '1698338620tpCsBlxqyk915'),
(16, 8, '1699077682kqyCpolwsA793'),
(17, 8, '1699078591HrDsyACtkv746');

-- --------------------------------------------------------

--
-- Table structure for table `uservisits`
--

CREATE TABLE `uservisits` (
  `id` int(200) NOT NULL,
  `user` int(100) NOT NULL,
  `state` varchar(10) NOT NULL,
  `visitTime` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `uservisits`
--

INSERT INTO `uservisits` (`id`, `user`, `state`, `visitTime`) VALUES
(7, 7, 'Osun', '1696933421'),
(8, 7, 'Osun', '1696933421'),
(9, 7, 'Osun', '1696938398'),
(10, 7, 'Osun', '1697753296'),
(11, 7, 'Osun', '1698338621');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `airtime`
--
ALTER TABLE `airtime`
  ADD PRIMARY KEY (`aId`);

--
-- Indexes for table `airtimepinprice`
--
ALTER TABLE `airtimepinprice`
  ADD PRIMARY KEY (`aId`);

--
-- Indexes for table `alphatopupprice`
--
ALTER TABLE `alphatopupprice`
  ADD PRIMARY KEY (`alphaId`);

--
-- Indexes for table `apiconfigs`
--
ALTER TABLE `apiconfigs`
  ADD PRIMARY KEY (`aId`);

--
-- Indexes for table `apilinks`
--
ALTER TABLE `apilinks`
  ADD PRIMARY KEY (`aId`);

--
-- Indexes for table `cableid`
--
ALTER TABLE `cableid`
  ADD PRIMARY KEY (`cId`);

--
-- Indexes for table `cableplans`
--
ALTER TABLE `cableplans`
  ADD PRIMARY KEY (`cpId`);

--
-- Indexes for table `contact`
--
ALTER TABLE `contact`
  ADD PRIMARY KEY (`msgId`);

--
-- Indexes for table `datapins`
--
ALTER TABLE `datapins`
  ADD PRIMARY KEY (`dpId`);

--
-- Indexes for table `dataplans`
--
ALTER TABLE `dataplans`
  ADD PRIMARY KEY (`pId`);

--
-- Indexes for table `datatokens`
--
ALTER TABLE `datatokens`
  ADD PRIMARY KEY (`tId`);

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
-- Indexes for table `networkid`
--
ALTER TABLE `networkid`
  ADD PRIMARY KEY (`nId`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`msgId`);

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
  ADD UNIQUE KEY `sApiKey` (`sApiKey`),
  ADD UNIQUE KEY `sPhone` (`sPhone`),
  ADD UNIQUE KEY `sEmail` (`sEmail`);

--
-- Indexes for table `sysusers`
--
ALTER TABLE `sysusers`
  ADD PRIMARY KEY (`sysId`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`tId`),
  ADD UNIQUE KEY `transref` (`transref`);

--
-- Indexes for table `userlogin`
--
ALTER TABLE `userlogin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `uservisits`
--
ALTER TABLE `uservisits`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `airtime`
--
ALTER TABLE `airtime`
  MODIFY `aId` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `airtimepinprice`
--
ALTER TABLE `airtimepinprice`
  MODIFY `aId` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `alphatopupprice`
--
ALTER TABLE `alphatopupprice`
  MODIFY `alphaId` int(200) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `apiconfigs`
--
ALTER TABLE `apiconfigs`
  MODIFY `aId` int(200) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT for table `apilinks`
--
ALTER TABLE `apilinks`
  MODIFY `aId` int(200) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT for table `cableid`
--
ALTER TABLE `cableid`
  MODIFY `cId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `cableplans`
--
ALTER TABLE `cableplans`
  MODIFY `cpId` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `contact`
--
ALTER TABLE `contact`
  MODIFY `msgId` int(200) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `datapins`
--
ALTER TABLE `datapins`
  MODIFY `dpId` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `dataplans`
--
ALTER TABLE `dataplans`
  MODIFY `pId` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `datatokens`
--
ALTER TABLE `datatokens`
  MODIFY `tId` int(100) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `electricityid`
--
ALTER TABLE `electricityid`
  MODIFY `eId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `examid`
--
ALTER TABLE `examid`
  MODIFY `eId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `networkid`
--
ALTER TABLE `networkid`
  MODIFY `nId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `msgId` int(200) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `sitesettings`
--
ALTER TABLE `sitesettings`
  MODIFY `sId` int(200) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `subscribers`
--
ALTER TABLE `subscribers`
  MODIFY `sId` int(200) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `sysusers`
--
ALTER TABLE `sysusers`
  MODIFY `sysId` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `tId` int(200) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `userlogin`
--
ALTER TABLE `userlogin`
  MODIFY `id` int(200) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `uservisits`
--
ALTER TABLE `uservisits`
  MODIFY `id` int(200) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
