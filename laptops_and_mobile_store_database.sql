-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 20, 2021 at 12:45 PM
-- Server version: 10.4.17-MariaDB
-- PHP Version: 7.4.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `laptops_and_mobile_store`
--
CREATE DATABASE IF NOT EXISTS `laptops_and_mobile_store` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `laptops_and_mobile_store`;

-- --------------------------------------------------------

--
-- Table structure for table `address`
--

CREATE TABLE `address` (
  `ID` mediumint(9) NOT NULL,
  `ADDRESS_LINE1` varchar(100) NOT NULL,
  `ADDRESS_LINE2` varchar(100) DEFAULT NULL,
  `DISTRICT` varchar(30) NOT NULL,
  `STATE` varchar(30) NOT NULL,
  `PINCODE` varchar(6) NOT NULL,
  `LANDMARK` varchar(100) DEFAULT NULL,
  `CUSTOMER_ID` mediumint(9) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `ID` mediumint(9) NOT NULL,
  `FIRST_NAME` varchar(30) NOT NULL,
  `LAST_NAME` varchar(30) NOT NULL,
  `GENDER` enum('MALE','FEMALE','OTHER') NOT NULL,
  `MOBILE_NUMBER` varchar(10) NOT NULL,
  `EMAIL` varchar(30) NOT NULL,
  `USER_ID` mediumint(9) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`ID`, `FIRST_NAME`, `LAST_NAME`, `GENDER`, `MOBILE_NUMBER`, `EMAIL`, `USER_ID`) VALUES
(24, 'Prajwal', 'J M', 'MALE', '1234567890', 'prajwaljm.191cs143@nitk.edu.in', 82),
(29, 'Shreya', 'K Loni', 'FEMALE', '2345678901', 'shreya@gamil.com', 88),
(31, 'Vidit', 'Gujurathi', 'MALE', '3456789012', 'vidit@gmail.com', 90),
(32, 'Anish', 'Giri', 'MALE', '6789012345', 'anish@gmail.com', 91);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `ID` mediumint(9) NOT NULL,
  `CUSTOMER_ID` mediumint(9) NOT NULL,
  `ORDER_DATE` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `ID` mediumint(9) NOT NULL,
  `ORDER_ID` mediumint(9) NOT NULL,
  `PRODUCT_ID` mediumint(9) NOT NULL,
  `SALE_RATE` double NOT NULL,
  `TAX_RATE` double NOT NULL,
  `STOCK` smallint(5) UNSIGNED NOT NULL,
  `DELIVERY_DATE` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `ID` mediumint(9) NOT NULL,
  `PRODUCT_TYPE` enum('LAPTOP','MOBILE') NOT NULL,
  `MODEL` varchar(100) NOT NULL,
  `MANUFACTURER` varchar(40) NOT NULL,
  `STOCK` smallint(5) UNSIGNED NOT NULL,
  `RELEASE_DATE` date NOT NULL,
  `CPU_TYPE` varchar(40) DEFAULT NULL,
  `OS` varchar(30) DEFAULT NULL,
  `RAM_SIZE` smallint(5) UNSIGNED NOT NULL,
  `HDD_SIZE` smallint(5) UNSIGNED NOT NULL,
  `DESCRIPTION` varchar(5000) DEFAULT NULL,
  `MRP_PRICE` mediumint(8) UNSIGNED NOT NULL,
  `DISCOUNT` float NOT NULL,
  `TAX_RATE` float NOT NULL,
  `IMAGE` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`ID`, `PRODUCT_TYPE`, `MODEL`, `MANUFACTURER`, `STOCK`, `RELEASE_DATE`, `CPU_TYPE`, `OS`, `RAM_SIZE`, `HDD_SIZE`, `DESCRIPTION`, `MRP_PRICE`, `DISCOUNT`, `TAX_RATE`, `IMAGE`) VALUES
(1, 'MOBILE', 'Redmi Note 7 Pro - 4GB - 64GB - RED', 'MI', 20, '2019-02-01', 'Octa-core 2.02GHz', 'Android 10', 4, 64, 'Country Of Origin - India.#48MP rear camera with ultra-wide, super macro, portrait, night mode, 960fps slowmotion, AI scene recognition, pro color, HDR, pro mode | 16MP facing camera #16.9418 centimeters (6.67-inch) FHD+ full screen dot display LCD multi-touch capacitive touchscreen with 2400 x 1080 pixels resolution, 400 ppi pixel density and 20:9 aspect ratio | 2.5D curved glass#Android v10 operating system with 2.3GHz Qualcomm Snapdragon 720G with 8nm octa core processor#Memory, Storage & SIM: 4GB RAM | 64GB internal memory expandable up to 512GB with dedicated SD card slot | Dual SIM (nano+nano) dual-standby (4G+4G)#5020mAH lithium-polymer large battery providing talk-time of 29 hours and standby time of 492 hours | 18W fast charger in-box and Type-C connectivity#1 year manufacturer warranty for device and 6 months manufacturer warranty for in-box accessories including batteries from the date of purchase#Box also includes: Power adapter, USB cable, SIM eject tool, Warranty card, User guide, Clear soft case, Screen protector pre-applied on the phone', 12999, 10, 18, 'product_images/1_redmi_note7_red.png'),
(2, 'MOBILE', 'Redmi Note 7 Pro - 4GB - 64GB - BLUE', 'MI', 10, '2019-02-01', 'Octa-core 2.02GHz', 'Android 10', 4, 64, 'Country Of Origin - India.#48MP rear camera with ultra-wide, super macro, portrait, night mode, 960fps slowmotion, AI scene recognition, pro color, HDR, pro mode | 16MP facing camera #16.9418 centimeters (6.67-inch) FHD+ full screen dot display LCD multi-touch capacitive touchscreen with 2400 x 1080 pixels resolution, 400 ppi pixel density and 20:9 aspect ratio | 2.5D curved glass#Android v10 operating system with 2.3GHz Qualcomm Snapdragon 720G with 8nm octa core processor#Memory, Storage & SIM: 4GB RAM | 64GB internal memory expandable up to 512GB with dedicated SD card slot | Dual SIM (nano+nano) dual-standby (4G+4G)#5020mAH lithium-polymer large battery providing talk-time of 29 hours and standby time of 492 hours | 18W fast charger in-box and Type-C connectivity#1 year manufacturer warranty for device and 6 months manufacturer warranty for in-box accessories including batteries from the date of purchase#Box also includes: Power adapter, USB cable, SIM eject tool, Warranty card, User guide, Clear soft case, Screen protector pre-applied on the phone', 12499, 10, 18, 'product_images/2_redmi_nte_7_blue.jpg'),
(3, 'MOBILE', 'Redmi Note 7 Pro - 4GB - 64GB - GRAY', 'MI', 15, '2019-02-01', 'Octa-core 2.02GHz', 'Android 10', 4, 64, 'Country Of Origin - India.#48MP rear camera with ultra-wide, super macro, portrait, night mode, 960fps slowmotion, AI scene recognition, pro color, HDR, pro mode | 16MP facing camera #16.9418 centimeters (6.67-inch) FHD+ full screen dot display LCD multi-touch capacitive touchscreen with 2400 x 1080 pixels resolution, 400 ppi pixel density and 20:9 aspect ratio | 2.5D curved glass#Android v10 operating system with 2.3GHz Qualcomm Snapdragon 720G with 8nm octa core processor#Memory, Storage & SIM: 4GB RAM | 64GB internal memory expandable up to 512GB with dedicated SD card slot | Dual SIM (nano+nano) dual-standby (4G+4G)#5020mAH lithium-polymer large battery providing talk-time of 29 hours and standby time of 492 hours | 18W fast charger in-box and Type-C connectivity#1 year manufacturer warranty for device and 6 months manufacturer warranty for in-box accessories including batteries from the date of purchase#Box also includes: Power adapter, USB cable, SIM eject tool, Warranty card, User guide, Clear soft case, Screen protector pre-applied on the phone', 12299, 10, 18, 'product_images/3_redmi_note7_gray.jpg'),
(4, 'MOBILE', 'OnePlus 7 Pro (Nebula Blue, 8GB RAM, Fluid AMOLED Display, 256GB Storage, 4000mAH Battery)', 'OnePlus', 10, '2019-05-14', 'Snapdragon 855 - 2.84GHz', 'Android 10', 8, 256, 'Rear Camera; 48MP (Primary)+ 8MP (Tele-photo)+16MP (ultrawide); Front Camera;16 MP POP-UP Camera; You will need to chargethe phone when you first get it or if you have not used it for a long time#16.9 centimeters (6.67-inch) multi-touch capacitive touchscreen with 3120 x 1440 pixels resolution#Memory, Storage and SIM: 8GB RAM | 256GB internal memory | Dual SIM dual-standby (4G+4G)#Android Oxygen operating system with 2.84GHz Snapdragon 855 octa core processor#4000mAH lithium-ion battery, Buttons: Gestures and on-screen navigation support; Alert Slider#1 year manufacturer warranty for device and 6 months manufacturer warranty for in-box accessories including batteries from the date of purchase#Box also includes: Power Adapter, Type-C Cable (Support USB 2.0), Quick Start Guide, Welcome Letter, Safety Information and Warranty Card, Logo Sticker, Case, Screen Protector (pre-applied) and SIM Tray Ejector', 52999, 12, 18, 'product_images/4_oneplus7_pro_blue.jpg'),
(5, 'MOBILE', 'OnePlus 8T 5G (Aquamarine Green, 8GB RAM, 128GB Storage)', 'OnePlus', 25, '2020-10-14', 'Qualcomm Snapdragon 865 - 2.86GHz', 'Android 10', 8, 128, 'Rear quad camera setup having 48MP main camera with Sony IMX586 sensor, 16MP ultra wide angle Lends, 5MP macro lens, 2MP monochrome lens | 16MP front camera with Sony IMX471 sensor#16.63 centimeters (6.55 inch) 120Hz fluid AMOLED capacitive touchscreen with 2400 x 1080 pixels resolution, 402 ppi pixel density#OxygenOS based on Android v11 operating system with 2.86GHz Qualcomm Snapdragon 865TM with Adreno 650 GPU quad core processor#Memory, Storage & SIM: 8GB RAM | 128GB internal memory expandable up to 128GB | Dual SIM (nano+nano) dual-standby (5G+4G)#4500mAH lithium-polymer battery#1 year manufacturer warranty for device and 6 months manufacturer warranty for in-box accessories including batteries from the date of purchase#120 Hz fluid Amoled display, Qualcomm Snapdragon 865, Warp Charge 65 , OxygenOS based on Android 11, Adreno 650 GPU#Reading mode, night mode, vibrant color, face unlock, HDR, screen flash, face retouching#supports Alexa Hands-Free. Alexa on your phone lets you make phone calls, open apps, control smart home devices, access the library of Alexa skills, and more using just your voice while on-the-go. Download the Alexa app and complete hands-free setup to get started. Just ask - and Alexa will respond instantly.', 42999, 2, 18, 'product_images/5_oneplus8t_aquamarine_green.jpg'),
(6, 'MOBILE', 'Samsung Galaxy S21 Ultra 5G(	Phantom Black, 16GB RAM, 128GB Storage)', 'Samsung', 5, '2021-01-29', 'Octa-core', 'Android 11, One UI 3.1', 16, 128, '17.27 cm (6.8 inch) Quad HD+ Display|108MP + 12MP + 10MP + 10MP | 40MP Front Camera|5000 mAh Lithium-ion Battery|Exynos 2100 Processor|Click photos and record up to 8K videos like a pro on the Samsung Galaxy S21 Ultra smartphone. Get ready to capture images that are rich in color and feature incredible details, thanks to its 108 MP camera setup and 100x Space Zoom', 105999, 1, 18, 'product_images/6_samsungS21_black.jpg'),
(7, 'MOBILE', 'Samsung Galaxy S21 Ultra 5G(	Phantom Silver, 16GB RAM, 128GB Storage)', 'Samsung', 5, '2021-01-29', 'Octa-core', 'Android 11, One UI 3.1', 16, 128, '17.27 cm (6.8 inch) Quad HD+ Display|108MP + 12MP + 10MP + 10MP | 40MP Front Camera|5000 mAh Lithium-ion Battery|Exynos 2100 Processor|Click photos and record up to 8K videos like a pro on the Samsung Galaxy S21 Ultra smartphone. Get ready to capture images that are rich in color and feature incredible details, thanks to its 108 MP camera setup and 100x Space Zoom', 105999, 1, 18, 'product_images/7_samsungS21_silver.jpg'),
(8, 'MOBILE', 'Samsung Galaxy S20 FE (Cloud Lavender, 8GB RAM, 128GB Storage)', 'Samsung', 7, '2020-09-10', 'Exynos 990 octa core processor', 'Android 10', 8, 128, 'Triple rear camera setup - 8MP OIS F2.4 tele camera + 12MP F2.2 ultra wide + 12MP (2PD) OIS F1.8 wide rear camera | 32MP (2PD) OIS F2.2 front punch hole camera | Rear LED flash|16.40 centimeters (6.5-inch) dynamic AMOLED display, FHD+ capacitive multi-touch touchscreen, Quad HD+ resolution with 1080 x 2400 pixels resolution|4500mAH lithium-ion battery (Non-removable), face-unlock & finger print sensor|Android v10.0 operating system with 2.73GHz+2.5GHz+2GHz Exynos 990 octa core processor|Memory, Storage & SIM: 8GB RAM | 128GB internal memory expandable up to 1TB | Dual SIM (nano+nano) dual-standby (4G+4G)|1 year manufacturer warranty for device and 6 months manufacturer warranty for in-box accessories including batteries from the date of purchase|Box also includes: Non-removable battery included, travel adapter, USB cable and user manual|MicroSD card slot (Expandable upto 1 TB), dual nano sim, hybrid sim slot, 4G+4G dual stand by', 40998, 2, 18, 'product_images/8_samsungS20FE_lavender.jpg'),
(9, 'MOBILE', 'Samsung Galaxy S20 FE (Cloud Mint,8GB RAM, 128GB Storage)', 'Samsung', 7, '2020-09-10', 'Exynos 990 octa core processor', 'Android 10', 8, 128, 'Triple rear camera setup - 8MP OIS F2.4 tele camera + 12MP F2.2 ultra wide + 12MP (2PD) OIS F1.8 wide rear camera | 32MP (2PD) OIS F2.2 front punch hole camera | Rear LED flash|16.40 centimeters (6.5-inch) dynamic AMOLED display, FHD+ capacitive multi-touch touchscreen, Quad HD+ resolution with 1080 x 2400 pixels resolution|4500mAH lithium-ion battery (Non-removable), face-unlock & finger print sensor|Android v10.0 operating system with 2.73GHz+2.5GHz+2GHz Exynos 990 octa core processor|Memory, Storage & SIM: 8GB RAM | 128GB internal memory expandable up to 1TB | Dual SIM (nano+nano) dual-standby (4G+4G)|1 year manufacturer warranty for device and 6 months manufacturer warranty for in-box accessories including batteries from the date of purchase|Box also includes: Non-removable battery included, travel adapter, USB cable and user manual|MicroSD card slot (Expandable upto 1 TB), dual nano sim, hybrid sim slot, 4G+4G dual stand by', 40998, 2, 18, 'product_images/9_samsungS20FE_mint.jpg'),
(10, 'MOBILE', 'Samsung Galaxy M31 (Iceberg Blue, 6GB RAM, 128GB Storage)', 'Samsung', 13, '2020-09-01', 'Exynos 9611 Octa core processor', 'Android 10', 6, 128, 'Quad camera setup - 64MP (F1.8) main camera + 8MP (F2.2) ultra wide camera + 5MP (F2.2) depth camera + 5MP (F2.4) macro camera | 32MP (F2.0) front facing punch hole camera|16.21 centimeters (6.4-inch) Super Amoled - Infinity U cut display, FHD+ capacitive multi-touch touchscreen with 2340 x 1080 pixels resolution, 404 ppi with 16M color support|Android v10.0 operating system with 1.7GHz+2.3GHz Exynos 9611 Octa core processor|Memory, Storage & SIM: 6GB RAM | 128GB internal memory expandable up to 512GB | Dual SIM (nano+nano) dual-standby (4G+4G)|6000mAH lithium-ion battery with 3x fast charge | 15W Type-C fast charger in the box|1 year manufacturer warranty for device and 6 months manufacturer warranty for in-box accessories including batteries from the date of purchase|Box also includes: Travel adapter, USB cable, ejection pin and user manual|origin-India', 16499, 5, 18, 'product_images/10_samsungM31_iceberg_blue.jpg'),
(11, 'MOBILE', 'Samsung Galaxy M31 (space black, 6GB RAM, 128GB Storage)', 'Samsung', 13, '2020-09-01', 'Exynos 9611 Octa core processor', 'Android 10', 6, 128, 'Quad camera setup - 64MP (F1.8) main camera + 8MP (F2.2) ultra wide camera + 5MP (F2.2) depth camera + 5MP (F2.4) macro camera | 32MP (F2.0) front facing punch hole camera|16.21 centimeters (6.4-inch) Super Amoled - Infinity U cut display, FHD+ capacitive multi-touch touchscreen with 2340 x 1080 pixels resolution, 404 ppi with 16M color support|Android v10.0 operating system with 1.7GHz+2.3GHz Exynos 9611 Octa core processor|Memory, Storage & SIM: 6GB RAM | 128GB internal memory expandable up to 512GB | Dual SIM (nano+nano) dual-standby (4G+4G)|6000mAH lithium-ion battery with 3x fast charge | 15W Type-C fast charger in the box|1 year manufacturer warranty for device and 6 months manufacturer warranty for in-box accessories including batteries from the date of purchase|Box also includes: Travel adapter, USB cable, ejection pin and user manual|origin-India', 16499, 5, 18, 'product_images/11_samsungM31_black.jpg'),
(12, 'MOBILE', 'Apple iPhone 7 (128GB) - Rose Gold', 'Apple', 6, '2020-05-17', 'A10 Fusion chip ', 'iOS13', 2, 128, '4.7-inch Retina HD LCD display|Product Dimensions:13.8 x 0.7 x 6.7 cm; 136 Grams|Water and dust resistant (1 meter for up to 30 minutes, IP67)|Single 12MP Wide camera with Auto HDR and 4K video up to 30fps|7MP FaceTime HD camera with 1080p video|Touch ID for secure authentication and Apple Pay|A10 Fusion chip|iOS 13 with Dark Mode, new tools for editing photos and video, and brand new privacy features|Whats in the box:1 Handset, 1 EarPods with Lightning Connector (wired), 1 Lightning to 3.5mm Headphone Jack Adapter, 1 Lightning to USB Cable and 1 USB Power Adapter|Battery Power Rating:1960|Batteries:1 Lithium ion batteries required.(included)', 29999, 40, 18, 'product_images/12_iphone7_rose_gold.jpg'),
(13, 'MOBILE', 'Apple iPhone 7 (128GB) - Gold', 'Apple', 4, '2020-05-17', 'A10 Fusion chip ', 'iOS13', 2, 128, '4.7-inch Retina HD LCD display|Product Dimensions:13.8 x 0.7 x 6.7 cm; 136 Grams|Water and dust resistant (1 meter for up to 30 minutes, IP67)|Single 12MP Wide camera with Auto HDR and 4K video up to 30fps|7MP FaceTime HD camera with 1080p video|Touch ID for secure authentication and Apple Pay|A10 Fusion chip|iOS 13 with Dark Mode, new tools for editing photos and video, and brand new privacy features|Whats in the box:1 Handset, 1 EarPods with Lightning Connector (wired), 1 Lightning to 3.5mm Headphone Jack Adapter, 1 Lightning to USB Cable and 1 USB Power Adapter|Battery Power Rating:1960|Batteries:1 Lithium ion batteries required.(included)', 29999, 40, 18, 'product_images/13_iphone7_gold.jpg'),
(14, 'MOBILE', 'Apple iPhone 12 (64GB) - Blue', 'Apple', 11, '2020-10-13', 'A14 Bionic chip', 'IOS 14', 6, 64, '6.1-inch Super Retina XDR display|Height: 5.78 inches (146.7 mm),Width: 2.82 inches (71.5 mm),Depth: 0.29 inch (7.4 mm),Weight: 5.78 ounces (164 grams)|Ceramic Shield, tougher than any smartphone glass|A14 Bionic chip, the fastest chip ever in a smartphone|Advanced dual-camera system with 12MP Ultra Wide and Wide cameras; Night mode, Deep Fusion, Smart HDR 3, 4K Dolby Vision HDR recording|12MP TrueDepth front camera with Night mode, 4K Dolby Vision HDR recording|Industry-leading IP68 water resistance|Supports MagSafe accessories for easy attach and faster wireless charging|iOS with redesigned widgets on the Home screen, all-new App Library, App Clips and more|Video playback:Up to 17 hours, Video playback (streamed):Up to 11 hours, Audio playback:Up to 65 hours, 20W adapter or higher (sold separately), Fast-charge capable: Up to 50% charge in around 30 minutes with 20W adapter or higher|iPhone with iOS 14, USB-C to Lightning Cable, Documentation|1 year manufacturer warranty for device and 6 months manufacturer warranty for in-box accessories including batteries from the date of purchase', 79649, 0, 18, 'product_images/14_iphone12_blue.png'),
(15, 'MOBILE', 'Apple iPhone 12 (128GB) - Green', 'Apple', 11, '2020-10-13', 'A14 Bionic chip', 'IOS 14', 6, 128, '6.1-inch Super Retina XDR display|Height: 5.78 inches (146.7 mm),Width: 2.82 inches (71.5 mm),Depth: 0.29 inch (7.4 mm),Weight: 5.78 ounces (164 grams)|Ceramic Shield, tougher than any smartphone glass|A14 Bionic chip, the fastest chip ever in a smartphone|Advanced dual-camera system with 12MP Ultra Wide and Wide cameras; Night mode, Deep Fusion, Smart HDR 3, 4K Dolby Vision HDR recording|12MP TrueDepth front camera with Night mode, 4K Dolby Vision HDR recording|Industry-leading IP68 water resistance|Supports MagSafe accessories for easy attach and faster wireless charging|iOS with redesigned widgets on the Home screen, all-new App Library, App Clips and more|Video playback:Up to 17 hours, Video playback (streamed):Up to 11 hours, Audio playback:Up to 65 hours, 20W adapter or higher (sold separately), Fast-charge capable: Up to 50% charge in around 30 minutes with 20W adapter or higher|iPhone with iOS 14, USB-C to Lightning Cable, Documentation|1 year manufacturer warranty for device and 6 months manufacturer warranty for in-box accessories including batteries from the date of purchase', 84900, 0, 18, 'product_images/15_iphone12_green.jpg'),
(16, 'MOBILE', 'Apple iPhone 12 (128GB) - Red', 'Apple', 11, '2020-10-13', 'A14 Bionic chip', 'IOS 14', 6, 256, '6.1-inch Super Retina XDR display|Height: 5.78 inches (146.7 mm),Width: 2.82 inches (71.5 mm),Depth: 0.29 inch (7.4 mm),Weight: 5.78 ounces (164 grams)|Ceramic Shield, tougher than any smartphone glass|A14 Bionic chip, the fastest chip ever in a smartphone|Advanced dual-camera system with 12MP Ultra Wide and Wide cameras; Night mode, Deep Fusion, Smart HDR 3, 4K Dolby Vision HDR recording|12MP TrueDepth front camera with Night mode, 4K Dolby Vision HDR recording|Industry-leading IP68 water resistance|Supports MagSafe accessories for easy attach and faster wireless charging|iOS with redesigned widgets on the Home screen, all-new App Library, App Clips and more|Video playback:Up to 17 hours, Video playback (streamed):Up to 11 hours, Audio playback:Up to 65 hours, 20W adapter or higher (sold separately), Fast-charge capable: Up to 50% charge in around 30 minutes with 20W adapter or higher|iPhone with iOS 14, USB-C to Lightning Cable, Documentation|1 year manufacturer warranty for device and 6 months manufacturer warranty for in-box accessories including batteries from the date of purchase', 94900, 0, 18, 'product_images/16_iphone12_red.jpg'),
(17, 'MOBILE', 'Vivo Y20G (Purist Blue, 6GB, 128GB Storage)', 'vivo', 13, '2021-01-24', 'MediaTek Helio G80 octa core processor', 'Android 11', 6, 128, 'Product Dimensions:16.4 x 7.6 x 0.8 cm; 188 Grams|13MP+2MP+2MP rear camera | 8MP front camera|16.55 centimeters (6.51 inch) HD+ display with 1600 x 720 pixels resolution|Memory, Storage & SIM: 6GB RAM | 128GB internal memory expandable up to 1TB | Dual SIM (nano+nano) dual-standby (4G+4G)|Funtouch OS 11 based on Android 11 operating system with MediaTek Helio G80 octa core processor|5000mAH lithium-ion battery (Type C) with 18W fast charging.|1 year manufacturer warranty for device and 6 months manufacturer warranty for in-box accessories including batteries from the date of purchase| Box includes: Handset, Documentation, USB cable, USB power adapter, sim ejector pin, protective case, protective film (1 applied)', 14690, 1, 18, 'product_images/17_vivoY20G_purist_blue.jpg'),
(18, 'MOBILE', 'Vivo Y91i (Ocean Blue, 3GB RAM, 32GB Storage)', 'vivo', 10, '2021-01-18', '2.0GHz Mediatek Helio P22 octa core', 'Android 8.1', 3, 32, 'Resolution:1520 x 720|13MP rear camera with take photo, professional, face beauty, slow, time-lapse, ppt, palm capture, voice control | 5MP front camera| 15.8 centimeters (6.22-inch) HD+ halo fullview display with 1520 x 720 pixels resolution|Memory, Storage & SIM: 3GB RAM | 32GB internal memory expandable up to 256GB | Dual SIM (nano+nano) dual-standby (4G+4G)| Funtouch OS 4.5 (based on Android 8.1) operating system with 2.0GHz Mediatek Helio P22 octa core processor|Whats in the box:Handset, User Manual, microUSB to USB Cable, USB Power Adapter, SIM Ejector Pin, Protective Case, Protective Film (1 applied)|1 year manufacturer warranty for device and 6 months manufacturer warranty for in-box accessories including batteries from the date of purchase', 8989, 1, 18, 'product_images/18_vivoY91i_ocean_blue.jpg'),
(19, 'MOBILE', 'Moto G9+ Plus (128GB, 4GB, NFC) XT2087 (Dark Blue)', '	Motorola', 5, '2020-01-01', 'octa-core ', 'Android 9', 4, 128, 'product dimensions :	6.69 x 3.07 x 0.38 inches| Rear Camera: 64MP + 8MP + 2MP| Front Camera: 16MP, 5000mAh Battery, 48 hours on a single charge|6.84\" Max Vision display, FHD+ (2400*1080)|International model phone, Does not have US warranty. Factory Unlocked cellphones are compatible with most of the GSM carriers such as AT&T and T-Mobile, but are not compatible with CDMA carriers such as Verizon|warranty :1 year|Connectivity Technology: NFC', 21799, 1, 19, 'product_images/19_motoG9+_dark_blue.jpg'),
(20, 'MOBILE', 'VivoY31(Ocean Blue, 8GB, 128GB Storage)', 'vivo', 17, '2021-01-26', 'MediaTek Helio G80 octa core processor', 'Android 11', 8, 128, 'Product Dimensions:16.4 x 7.6 x 0.8 cm; 188 Grams|13MP+2MP+2MP rear camera | 8MP front camera|16.55 centimeters (6.51 inch) HD+ display with 1600 x 720 pixels resolution|Memory, Storage & SIM: 6GB RAM | 128GB internal memory expandable up to 1TB | Dual SIM (nano+nano) dual-standby (4G+4G)|Funtouch OS 11 based on Android 11 operating system with MediaTek Helio G80 octa core processor|5000mAH lithium-ion battery (Type C) with 18W fast charging.|1 year manufacturer warranty for device and 6 months manufacturer warranty for in-box accessories including batteries from the date of purchase| Box includes: Handset, Documentation, USB cable, USB power adapter, sim ejector pin, protective case, protective film (1 applied)', 16290, 1, 18, 'product_images/20_vivoY31_ocean_blue.jpg'),
(21, 'LAPTOP', 'Lenovo Legion Y540', 'Lenovo', 14, '2020-12-18', '9th Gen Intel Core i5-9300HF', 'Windows 10 Home', 8, 512, 'Processor: 9th Gen Intel Core i5-9300HF | Speed: 2.4 GHz (Base) - 4.1 GHz (Max) | 4 Cores | 8MB Cach|OS: Pre-Loaded Windows 10 Home with Lifetime Validity|Memory and Storage: 8GB RAM DDR4-2666, Upgradable up to 32GB | 512 GB SSD|Graphics: NVIDIA GeForce GTX 1650 4GB GDDR5 Dedicated Graphics|Display: 15.6\" Full HD (1920x1080) | Brightness: 250 nits | Anti-Glare | 45% NTSC Color Gamut | IPS Technology | 60 Hz Refresh Rate|Cooling: Coldfront with Dual Channel thermal mechanism | 4 thermal vents | Dedicated heat syncs|Battery Life: 4 Hours | 52.5 Wh Battery|Keyboard: Full-size Backlit Keyboard with 100% Anti-Ghosting | Less than 1 ms Response Time|Camera (Built in): HD 720p (1.0MP) Camera | Fixed Focus | Integrated Dual Array Microphone|Audio: 2 x 2W Harman Stereo Speakers | HD Audio | Dolby Atmos for Gaming Certification|Ports: 3 USB 3.1 Gen 1, 1 USB 3.1 Type-C Gen 1 (with the function of DisplayPort 1.2), Headphone/Mic combo jack, HDMI 2.0, Ethernet (RJ-45), Mini DisplayPort 1.4, Security keyhole|Xbox Game Pass: Access to over 100 high-quality PC games on Windows 10 | One-month subscription to Xbox Game Pass is included with the purchase of your device|In the box: Laptop, Power Adapter and User Manual|Warranty: This genuine Lenovo laptop comes with 1 Year Onsite Warranty with Premium Care and Accidental Damage Protection', 59990, 29, 18, 'product_images/21_lenovo_legion_Y540.jpg'),
(22, 'LAPTOP', 'Lenovo Legion 5i', 'Lenovo', 11, '2020-09-21', '10th Gen Intel Core i5-10300H', ' Windows 10 Home', 8, 512, 'Processor: 10th Gen Intel Core i5 (i5-10300H) | Speed: 2.5 GHz (Base) - 4.5 GHz (Max) | 4 Cores | 8MB Cache|OS: Pre-Loaded Windows 10 Home with Lifetime Validity|Memory and Storage: 8GB RAM DDR4-2933, Upgradable up to 16GB | 512GB SSD|Display: 15.6\" Full HD (1920x1080) | Wide Viewing Angle | Anti-Glare | IPS Technology | 120 Hz Refresh Rate|Graphics: NVIDIA GeForce GTX 1650 4GB GDDR6 Dedicated Graphics|Cooling: Coldfront 2.0 with Dual Channel thermal mechanism | Q Control 3.0 to select between Quiet, Balance and Performance thermal modes|Keyboard: Full-size Backlit Keyboard with 100% Anti-Ghosting and soft landing switches | 1.5 mm key travel|Camera (Built-in): HD 720p Camera with Privacy Shutter | Fixed Focus | Integrated Dual Array Microphone|Audio: 2 x 2W Harman Speakers | Dolby Audio for Gaming Certification|Battery Life: 5 Hours | Rapid Charge Pro (Up to 50% in 30 Minutes)|Ports: 4 USB 3.2 Gen 1, 1 USB 3.2 Type-C Gen 1, Headphone/Mic combo jack, HDMI 2.0, Ethernet (RJ-45)|Xbox Game Pass: Access to over 100 high-quality PC games on Windows 10 | One-month subscription to Xbox Game Pass is included with the purchase of your device|In the box: Laptop, Legion M300 RGB Gaming Mouse, Power Adapter and User Manual|Warranty: This genuine Lenovo laptop comes with 1 year onsite manufacturer warranty', 69990, 37, 18, 'product_images/22_lenovo_legion_5i.jpg'),
(23, 'LAPTOP', 'Lenovo Ideapad Gaming 3', 'Lenovo', 15, '2020-12-07', '4th Gen AMD Ryzen 5 4600H', 'Windows 10 Home', 8, 512, 'Processor: 4th Gen AMD Ryzen 5 4600H | Speed: 3.0 GHz (Base) - 4.0 GHz (Max) | 6 Cores | 3MB L2 & 8MB L3 Cache|OS: Pre-Loaded Windows 10 Home with Lifetime Validity|Memory and Storage: 8GB RAM DDR4-3200, Upgradable up to 16GB | 512 GB SSD|Graphics: NVIDIA GeForce GTX 1650 Ti 4GB GDDR6 Dedicated Graphics|Display: 15.6\" Full HD (1920x1080) | Brightness: 250 nits | Anti-Glare | IPS Technology | 60 Hz Refresh Rate|Cooling: 5th Generation Thermal Engineering with Q Control|Battery Life: 5 Hours | Rapid Charge (Up to 80% in 1 Hour)|Keyboard: Full-size Blue LED Backlit Keyboard ergonomically optimized for Gaming|Camera (Built in): HD 720p (1.0 MP) Camera with Fixed Focus | Privacy Shutter | Integrated Dual Array Microphone|Audio: 2 x 1.5W Stereo Speakers | HD Audio | Dolby Audio|Ports: 2 USB 3.2 Gen 1, 1 USB 3.2 Type-C Gen 1, Headphone/Mic combo jack, HDMI 2.0, Ethernet (RJ-45)|In the box: Laptop, Power Adapter and User Manual|Warranty: This genuine Lenovo laptop comes with 1 year Onsite Manufacturer Warranty with Accidental Damage Protection and 24/7 Premium Care', 64001, 24, 18, 'product_images/23_lenovo_ideapad_Gaming_3.jpg'),
(24, 'LAPTOP', 'Lenovo V15 ADA', 'Lenovo', 20, '2020-07-30', 'AMD Atom Z8700', 'Windows 10 Home', 4, 1000, 'Processor: AMD Ryzen 3 3250U processor, 2.6 Ghz base speed, 3.5 Ghz max speed, 2 cores, 4 Mb L3 Cache|Operating System: Pre-loaded Windows 10 Home with lifetime validity | Display: 15.6\" HD (1366x768) TN 220nits Anti-glare|Memory and Storage: 4GB Soldered DDR4-2400 | Storage 1 TB HDD|Design and battery: Thin and light Laptop| 180 Degree Hinge| Laptop weight 1.85 kg | Battery Life: Upto 5.5 hours as per MobileMark|Inside the box: Laptop, Charger, User Manual | Ports: One USB 2.0, two USB 3.1 Gen 1, HDMI 1.4b, 4-in-1 reader (MMC, SD, SDHC, SDXC), combo audio / microphone jack, AC power adapter jack|Speaker Description:HD Audio, Dolby Audio™, stereo speakers, 1.5W x 2|Graphics coprocessor: intel integrated', 30895, 18, 18, 'product_images/24_lenovo_V15_ADA.jpg'),
(25, 'LAPTOP', 'HP ENVY x360 15.6\"FHD Touchscreen 2-in-1', 'HP', 12, '2019-02-19', 'AMD Ryzen 5 2500U Quad-Core', 'Windows 10', 8, 1000, 'A 15.6\" Diagonal Full HD IPS WLED-Backlit Multitouch-Enabled display (1920 x 1080) Display, AMD Radeon Vega|AMD Ryzen 5 2500U Quad-Core Processor (2 GHz, up to 3.6 GHz, 6 MB cache)|8GB DDR4-2400 SDRAM, 256GB SSD (Boot) + 1TB HDD|802.11Ac (2x2), 2 SuperSpeed 10Gbs USB 3.1 Ports, 1 USB Type-C port, HDMI, Multi-Format Digital Media Reader, Headphone/Microphone combo jack, HD Webcam, 1x Multi-format digital card reader, Full Backlit Keyboard with 10-key numeric keyboard, Bluetooth', 112421, 26, 18, 'product_images/25_HP_ENVY_x360.jpg'),
(26, 'LAPTOP', 'HP 14s-dr2007TU', 'HP', 9, '2020-11-10', '11th Gen Intel Core i7-1165G7', 'Windows 10 Home', 8, 512, 'Processor: 11th Gen Intel Core i7-1165G7 (2.8 GHz base frequency, up to 4.7 GHz with Intel Turbo Boost Technology, 12 MB L3 cache, 4 cores)|Operating System: Pre-loaded Windows 10 Home Single Language | Pre-Loaded Microsoft Office 2019|Display: 14 -inch FHD, IPS, micro-edge, brightview | Brightness: 250 nits | Screen Resolution: 1920 x 1080|Memory & Storage: 8 GB DDR4-2666 SDRAM (2 x 4 GB) & Expandable Upto 16 GB | Storage: 512GB PCIe NVMe SSD Graphics: Intel UHD Graphic|Design & battery: Laptop weight: 1.47 kg | Battery life mixed usage = Up to 7 hours and 30 minutes | In the Box: Laptop, Battery, User Manual and charger|Camera & Microphone: HP TrueVision HD camera with integrated dual array digital microphone|orts: 1 SuperSpeed USB Type-C (5Gbps), 2 SuperSpeed USB Type-A (5Gbps), 1 HDMI 1.4b, 1 RJ-45|Warranty: This genuine HP laptop comes with 1-year domestic warranty from HP covering manufacturing defects and not covering physical damage.', 75482, 16, 18, 'product_images/26_HP_14s-dr2007TU.jpg'),
(27, 'LAPTOP', 'HP 14s-er0004TU', 'HP', 12, '2020-10-04', '10th Gen Intel Core i3-1005G1', 'Windows 10 Home', 8, 1000, 'Processor: 10th Gen Intel Core i3-1005G1 Processor (1.2 GHz base frequency, up to 3.4 GHz with Intel Turbo Boost Technology, 4 MB cache, 2 cores)|Operating System:Pre-loaded Windows 10 Home with lifetime validity|Display: 15.6-inch Full HD (1920 x 1080) Brightview Micro-Edge WLED Display, 250 nits|Memory & Storage: 8GB (1x8GB) DDR4 RAM, upgradable to 16GB | Storage: 1TB 5400 RPM HDD|Design & Battery: Thin & Light Design, Weight: 1.5 kg | Up to 9 Hours of battery backup, 3-cell, 70 Wh Li-ion battery, HP Fast Charge (50% in 45 mins)|Included Components:Laptop, Power Adapter|Warranty: This genuine HP laptop comes with 1-year domestic warranty from HP covering manufacturing defects and not covering physical damage.', 41700, 12, 18, 'product_images/27_HP_14s-er0004TU.jpg'),
(28, 'LAPTOP', 'HP 15s-du1052tu', 'HP', 20, '2020-09-04', 'Intel Pentium Gold 6405U', 'Windows 10 Home', 4, 1000, 'Processor: Intel Pentium Gold 6405U (2.4 GHz base frequency, 2 MB L3 cache, 2 cores)|Operating System: Pre-loaded Windows 10 Home with lifetime validity|Display: 15.6-Inch HD (1366 x 768), micro-edge, BrightView, 220 nits, 45% NTSC|Memory & Storage: 4 GB DDR4-2400 SDRAM (1 x 4 GB) | 1TB HDD 5400 RPM|Graphics: Intel UHD Graphics|Camera & Microphone: HP TrueVision HD camera with integrated dual array digital microphone|Ports: 1 SuperSpeed USB Type-C (5Gbps), 2 SuperSpeed USB Type-A (5Gbps), 1 microSD card reader | Without CD-Drive|Design & Battery: Thin and light design | Laptop weight: 1.74 kg | Average battery life = 7 hours, 3-cell, 41 Wh Li-ion Fast Charge Battery|Warranty: This genuine HP laptop comes with 1-year domestic warranty from HP covering manufacturing defects and not covering physical damage.', 26691, 10, 18, 'product_images/28_HP_15s-du1052tu.jpg'),
(29, 'LAPTOP', 'ASUS ROG Zephyrus G14', 'ASUS', 7, '2020-08-06', 'AMD Ryzen 5 4600HS', 'Windows 10 Home', 8, 512, 'Processor: AMD Ryzen 5 4600HS Processor, 3.0 GHz (8MB Cache, up to 4.0 GHz, 6 Cores, 12 Threads)|Memory: 8GB DDR4 3200MHz onboard RAM, Upgradeable up to 24GB using 1x SO-DIMM Slot with | Storage: 512GB M.2 NVMe PCIe 3.0 SSD|Graphics: Dedicated NVIDIA GeForce GTX 1650Ti GDDR6 4GB VRAM|Display: 14-inch (16:9) Full HD (1920 x 1080), 300 nits Brightness, Anti-Glare IPS-level panel, 120Hz Refresh Rate, 100% sRGB, Pantone Validated, Adaptive sync, 85% screen-to-body ratio|Software Included: Pre-installed MS Office Home and Student 2019 | Operating System: Pre-loaded Windows 10 Home (64bit) with lifetime validity', 95990, 26, 18, 'product_images/29_ASUS_ROG_Zephyrus_G14.jpg'),
(30, 'LAPTOP', 'ASUS ZenBook Pro Duo', 'ASUS', 5, '2021-02-19', '9th Gen i9-9980HK Processor', 'Windows 10 Home', 32, 1000, 'Processor: 9th Gen i9-9980HK Processor 2.4 GHz (16MB Cache, up to 5.0 GHz, 8 Cores, 16 Threads)|Memory: 32GB Onboard DDR4 2666MHz RAM | Storage: 1TB M.2 NVMe PCIe 3.0 x4 SSD|Graphics: Dedicated NVIDIA GeForce RTX 2060 GDDR6 6GB VRAM|Display: 15.6” OLED 4K UHD (3840 x 2160) 16:9 Touchscreen Panel, 89% screen-to-body ratio, 178° wide-view technology, 100% DCI-P3, PANTONE Validated|Operating System: Pre-loaded Windows 10 Home(64bit) with lifetime validity|Design & battery: Spun-Metal finish| 5mm-thin bezels | Laptop weight 2.5 Kg | 71Whrs 8-cell Li-Polymer Battery|ScreenPad Plus: 14-inch 4K (3840 x 1100) Touch Display with 178˚ wide-view technology | Keyboard : Full-size backlit keyboard, 1.4mm key travel, Integrated LED-backlit numeric keypad on touchpad, Precision touchpad (PTP) technology supports up to four-finger smart gestures|I/O Ports: 1 x Thunderbolt 3 USB Type C (support display/power delivery), 2 x USB 3.2 Gen 2 Type-A , 1x HDMI 1.4, 1x 3.5mm Audio combo jack, Comes without CD-Drive|Other: HD IR webcam | Face recognition login with Windows Hello support | Wi-Fi 6 (Gig+) (802.11ax) 2*2 + Bluetooth 5.0|Warranty: This genuine ASUS laptop comes with 1-year domestic warranty from HP covering manufacturing defects and not covering physical damage.', 289990, 4, 18, 'product_images/30_ASUS_ZenBook_Pro_Duo.jpg'),
(31, 'LAPTOP', 'ASUS VivoBook 14', 'ASUS', 20, '2020-08-05', '10th Gen Intel Core i5-1035G1', 'Windows 10 Home', 8, 512, 'Processor: 10th Gen Intel Core i5-1035G1 Processor, 1.0 GHz (6MB Cache, up to 3.6 GHz, 4 Cores, 8 Threads)|Memory & Storage: 8GB LPDDR4X 3733MHz onboard RAM | Storage: 512GB M.2 NVMe PCIe SSD with 32GB Intel Optane Memory (Intel H10 SSD)|Graphics: Integrated Intel UHD Graphics|Display: 14.0-inch (16:9) LED-backlit FHD (1920x1080) 60Hz, 300nits Brightness, Anti-Glare, IPS-level Panel, with 100% sRGB Color gamut, 87% Screen-to-body ratio and 178° viewing angles|Software Included: Pre-installed MS Office Home and Student 2019 | Operating System: Pre-loaded Windows 10 Home with lifetime validity|Design & battery: Metallic body |Thin and Light Laptop | Laptop weight: 1.35 kg | 72WHrs, 4-cell lithium-polymer battery | Up to 24 hours battery life ;Note: Battery life depends on conditions of usage|Keyboard: Full-size Backlit Chiclet Keyboard | 1.4mm key travel distance|I/O Ports: 1x HDMI 1.4, 1x 3.5mm Combo Audio Jack, 1x USB 2.0 Type-A, 1x USB 3.2 Gen 1 Type-A, 1x Thunderbolt 3 USB C (with supports for display/power delivery), 1x 2 in 1 card reader SD/MMC|Other: FingerPrint Reader with Windows Hello support | 720p HD web camera | Wi-Fi 6 (Gig+) (802.11ax) 2*2 | Bluetooth 5.0 | Built-in 2 W Stereo Speakers with Microphone | US MIL-STD 810G military-grade standard|Warranty: This genuine Asus laptop comes with 1 year manufacturer warranty on the device and 6 months manufacturer warranty on included accessories from the date of purchase.', 57970, 28, 18, 'product_images/31_ASUS_VivoBook_14.jpg'),
(32, 'LAPTOP', 'ASUS VivoBook 15', 'ASUS', 25, '2020-02-18', '10th Gen Intel Core i3-10110U', 'Windows 10 Home', 8, 512, 'Processor: 10th Gen Intel Core i3-10110U Processor 2.1 GHz (4MB Cache, up to 4.1 GHz, 2 Cores, 4 Threads)|Memory & Storage: 4GB Oboard DDR4 RAM upgradeable up to 12GB using 1x SO-DIMM Slot with | Storage: 512GB NVMe PCIe 3.0 M.2 SSD + empty 1x 2.5-inch SATA Slot for Storage Expansion.|Graphics: Intel Integrated UHD Graphics|Display: 15.6\" (16:9) LED-backlit FHD (1920x1080) Anti-Glare with 45% NTSC, 88% Screen-to-body ratio|Operating System: Pre-loaded Windows 10 Home(64bit) with lifetime validity|Design & Battery: 5.7mm Thin Bezels | Laptop weight 1.70 kg | Thin and Light Laptop | 37WHrs, Lithium-Polymer 2-cell battery|Keyboard: Full-Size Backlit Chiclet Keyboard | 1.4mm Key-travel Distance | 19mm full size key pitch | 2° ErgoLift Hinge for a comfortable typing position along with integrated Numeric keypad|I/O Ports : 1x 3.5mm Combo audio jack | 2x Type-A USB 2.0| 1x Type-A USB 3.2 (Gen 1)| 1x Type C USB 3.2 (Gen 1)| 1x HDMI 1.4|1x Micro-SD card Reader.|Warranty: This genuine Asus laptop comes with 1 year manufacturer warranty on the device and 6 months manufacturer warranty on included accessories from the date of purchase.', 40990, 16, 18, 'product_images/32_ASUS_VivoBook_15.jpg'),
(33, 'LAPTOP', 'dell Inspiron 15 3000 Laptop (3505)', 'Dell', 19, '2020-10-09', 'Ryzen 3 3250U', 'Windows 10 Home', 8, 1000, 'Processor: AMD Ryzen 3 3250U processor, 2.6 Ghz base speed, 3.5 Ghz max speed, 2 cores, 4 Mb L3 Cache|Operating System: Pre-loaded Windows 10 Home with Microsoft Office Home and Student 2019 lifetime validity | Display: 15.6-inch FHD (1920 x 1080) An ti-glare LED Backlight Non-Tou ch Narrow Border WVA Display|Memory & Storage: 8GB DDR4 RAM | Storage: 256GB M.2 PCIe NVMe Solid Stat e Drive (Boot) + 1TB 5400 rpm 2.5\" SATA Hard Drive (Storage) | Graphics: AMD Radeon Vega 3 Graphics|Design & battery: Laptop weight: 1.83 kg | battery: 3-Cell 42WHr Battery|Warranty: This genuine Dell laptop comes with 1 year domestic warranty from Dell covering Hardware Issues and not covering physical damage.', 40250, 11, 18, 'product_images/33_dell_Inspiron_15_3000_Laptop.jpeg'),
(34, 'LAPTOP', 'Dell VOSTRO 3401', 'Dell', 17, '2020-11-04', 'intel i3-1005G1', 'Windows 10 Home', 4, 1000, '14\" FHD AG / i3-1005G1 / 4GB / 1TB+256 SSD / Integrated graphics / 1 Yr NBD / Win 10 / Office H&S 2019|Processor: 10th Generation Intel Core i3-1005G1 Processor (4MB Cache, up to 3.4 GHz)|Memory & Storage:4GB RAM | 256GB M.2 PCIe NVMe Solid State Drive+1TB 5400 rpm 2.5\" SATA Hard Drive|Display:14.0-inch FHD (1920 x 1080) Anti-glare LED Backlight Narrow Border WVA Display|Graphics: Intel UHD Graphics with shared graphics memory|Operating System & Software: Windows 10 Home Single Language | Microsoft Office Home and Student 2019|Warranty: This genuine Dell laptop comes with 1 year domestic warranty from Dell covering Hardware Issues and not covering physical damage.', 39590, 8, 18, 'product_images/34_Dell_VOSTRO_3401.jpg'),
(35, 'LAPTOP', 'Dell Inspiron 5000', 'Dell', 22, '2020-08-05', 'Intel Core i5-10210U', 'Windows 10 Home', 8, 512, 'Processor:10th Generation Intel Core i5-10210U Processor (6MB Cache, up to 4.2 GHz,4 cores)|Memory & Storage:8GB RAM Single Channel DDR4 2666MHz | 512GB M.2 PCIe NVMe Solid State Drive|Display:13.3-inch FHD (1920 x 1080) IPS Anti-Glare Narrow Border 300nits 95% sRGB WVA Display|Graphics:Intel UHD Graphics with shared graphics memory|Operating System & Software:Windows 10 Home Single Language | Microsoft Office Home and Student 2019 | McAfee Security Center 15 month subscription|Keyboard & Battery:English-International Backlit Keyboard | 4-Cell Battery, 53WHr|I/O ports:HDMI 1.4 Port | 3x USB 3.2 Gen 1 Type-C, Micro SD card reader | 1xHeadphone & Microphone Audio Jack|Others:802.11ac 2x2 WiFi | Finger Print Reader | Waves MaxxAudio Pro |Warranty: This genuine Dell laptop comes with 1 year domestic warranty from Dell covering Hardware Issues and not covering physical damage.', 72990, 11, 18, 'product_images/35_Dell_Inspiron_5000.jpg'),
(36, 'LAPTOP', 'Dell G3 3500', 'Dell', 6, '2020-09-14', 'Intel Core i7-10750H', 'Windows 10 Home', 8, 512, 'Processor:10th Generation Intel Core i7-10750H (12MB Cache, up to 5.0 GHz, 6 cores)|Memory & Storage:8GB DDR4,Dual Channel 2933MHz | 512 GB M.2 PCIe NVMe Solid State Drive|Display:15.6 inch FHD (1920 x 1080) 120Hz 250 nits WVA Anti- Glare LED Backlit Narrow Border Display|Graphics:NVIDIA GeForce GTX 1650 4GB GDDR6 | Game Shift Technology|Operating System & Software:Windows 10 Home Plus Single Language | McAfee Security Center 15 month subscription|Keyboard & Battery: Backlit keyboard | 4 Cell Battery, 68 Whr,Upto 10 hours battery life | Laptop weight 2.3 Kg|I/O Ports:1xUSB 3.2 Gen 1 | 2x USB 2.0 ports | 1x HDMI 2.0 port | 1x SD-card slot | 1x RJ45 port | 1x headset port|Warranty: This genuine Dell laptop comes with 1 year domestic warranty from Dell covering Hardware Issues and not covering physical damage.', 82490, 12, 18, 'product_images/36_Dell_G3_3500.jpg'),
(37, 'LAPTOP', 'Mi Notebook Horizon Edition 14', 'MI', 15, '2020-06-17', '10th Gen Intel Core i7-10510U', 'Windows 10 Home', 8, 512, 'Processor: 10th Gen Intel Core i7-10510U processor,1.8 GHz base speed, 4.9 GHz max speed, 4 Cores, 8 threads|Operating System in the laptop: Windows 10 Home operating system | Pre-installed software : Office 365 – one month Trial subscription|Laptop Display: Horizon Display|14-Inch (1920X 1080 )Full HD Anti-Glare Screen, Nvidia MX350 2GB GDDR5 Graphics|Memory Specs (Laptop): 8GB DDR4-2666MHz RAM and  Storage: 512 GB PCIE Gen 3x4 NVMe SSD|Design and battery of Laptop: Robust metal body |Thin and light Laptop| Laptop weight 1.35kg | Battery Life: Up to 10 hours|Laptop Audio: Stereo Speakers + DTS Audio Processing | Ports: USB 3.1 – 2 ports, USB 2.0 – 1 port, USB type C – 1, HDMI Out -1 and 3.5mm jack|Inside the Laptop box: Notebook, Power Adaptor, Power Cord, User Manual|Warranty: 1 Year manufacturer warranty from the date of purchase for both Laptop and Webcam|Webcam Specs: 720p High Resolution | Light Weight | USB Plug & Play|This combo pack contains two items and they may be delivered separately', 59999, 1, 18, 'product_images/37_Mi_Notebook_Horizon_Edition_14.jpeg'),
(38, 'LAPTOP', 'Mi Notebook 14', 'MI', 13, '2020-06-07', 'Intel Core i5-10210U', 'Windows 10 Home', 8, 256, 'Laptop Processor: 10th Gen Intel Core i5-10210U processor, 1.6 GHz base speed, 4.2 GHz max speed, 4 Cores, 8 threads|Operating System in the laptop: Windows 10 Home operating system | Pre-installed software : Office 365 – one month Trial subscription|Laptop Display: 14-Inch (1920X 1080 )Full HD Anti-Glare Screen, Intel UHD Graphics|Memory Specs (Laptop): 8GB DDR4-2666MHz RAM and  Storage: 256 GB SSD|Design and battery of Laptop: Robust metal body | Thin and light Laptop| Laptop weight 1.5kg | Battery Life: Up to 10 hours|Laptop Audio: Stereo Speakers + DTS Audio Processing | Ports: USB 3.1 – 2 ports, USB 2.0 – 1 port, HDMI Out -1 and 3.5mm jack|Inside the Laptop box: Notebook, Power Adaptor, Power Cord, User Manual|Warranty: 1 Year manufacturer warranty from the date of purchase for both Laptop and Webcam|Webcam Specs: 720p High Resolution | Light Weight | USB Plug & Play|This combo pack contains two items and they may be delivered separately', 41999, 1, 18, 'product_images/38_mi_Notebook_14.jpg'),
(39, 'LAPTOP', 'Apple MacBook Air', 'Apple', 4, '2020-03-18', 'Intel Core i3', 'IOS', 8, 256, 'Display :13.3-inch (diagonal) LED-backlit Retina display with IPS technology; 2560-by-1600 native resolution at 227 pixels per inch with support for millions of colors, 16:10 aspect ratio|Processor :1.1GHz dual-core Intel Core i3, Turbo Boost up to 3.2GHz, with 4MB L3 cache|storage: 256GB SSD|Graphics and Video Support: Intel Iris Plus Graphics, Support for Thunderbolt 3–enabled external graphics processors (eGPUs)|Charging and Expansion:wo Thunderbolt 3 (USB-C) ports with support for:, Charging, DisplayPort, Thunderbolt (up to 40 Gbps), USB 3.1 Gen 2 (up to 10 Gbps)|Wireless: 802.11ac Wi-Fi wireless networking; IEEE 802.11a/b/g/n compatible. Bluetooth 5.0 wireless technology|Warranty :Apple-branded hardware product and accessories contained in the original packaging (“Apple Product”) come with a One-Year Limited Warranty. See apple.com/in/legal/warranty for more information|In the Box: 13-inch MacBook Air, 30W USB-C Power Adapter, USB-C Charge Cable (2 m)', 88702, 5, 18, 'product_images/39_Apple_MacBook_Air.jpg'),
(40, 'LAPTOP', 'Apple MacBook Pro', 'Apple', 2, '2019-11-13', 'core Intel Core i7', 'IOS', 16, 512, 'Display: 16.0-inch (diagonal) LED-backlit display with IPS technology; 3072x1920 native resolution at 226 pixels per inch with support for millions of colors|Processor: 2.6GHz 6-core Intel Core i7, Turbo Boost up to 4.5GHz, with 12MB shared L3 cache|storage: 512GB SSD|Graphics and Video Support: AMD Radeon Pro 5300M with 4GB of GDDR6 memory and automatic graphics switching, Intel UHD Graphics 630|Charging and Expansion: Four Thunderbolt 3 (USB-C) ports with support for: Charging, DisplayPort, Thunderbolt (up to 40 Gbps), USB 3.1 Gen 2 (up to 10 Gbps)|Wireless: Wi-Fi 802.11ac Wi-Fi wireless networking; IEEE 802.11a/b/g/n compatible, Bluetooth 5.0 wireless technology|In the Box: 16-inch MacBook Pro, 96W USB-C Power Adapter, USB-C Charge Cable (2 m)', 199900, 2, 18, 'product_images/40_Apple_MacBook_Pro.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `ID` mediumint(9) NOT NULL,
  `USER_NAME` varchar(30) NOT NULL,
  `USER_PASSWORD` varchar(255) NOT NULL,
  `USER_TYPE` enum('CUSTOMER','ADMIN') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`ID`, `USER_NAME`, `USER_PASSWORD`, `USER_TYPE`) VALUES
(82, 'Prajwaljm', '$2y$10$XXXlGHgpkv9Z.iDx3wsknOcfZy1qp1IaYpj28.Q9G9j.vOklVr.M.', 'CUSTOMER'),
(88, 'ishreyaloni', '$2y$10$E28HerMwtyNSAsS2D31pvOgBlyj77n0MBYw8O9IXYTZDMfn3pu1ju', 'CUSTOMER'),
(90, 'vidityt', '$2y$10$F1BkUAfixHHfVp.Lnhrq0e0i37jggV6lMW..2a0tqb7ZIDwP4Z8Em', 'CUSTOMER'),
(91, 'anishonyt', '$2y$10$ui//CxKCWsE5NvFdUG6dLe924M8GEGtdKTDrHehjFExHG1yHtLPXa', 'CUSTOMER');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `address`
--
ALTER TABLE `address`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `FK_CUSTOMER` (`CUSTOMER_ID`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `email` (`FIRST_NAME`),
  ADD UNIQUE KEY `phone` (`MOBILE_NUMBER`),
  ADD KEY `FK_USER` (`USER_ID`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `FK_CUSTOMERS` (`CUSTOMER_ID`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `FK_ORDERS` (`ORDER_ID`),
  ADD KEY `FK_PRODUCTS` (`PRODUCT_ID`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `username` (`USER_NAME`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `ID` mediumint(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `ID` mediumint(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `ID` mediumint(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `address`
--
ALTER TABLE `address`
  ADD CONSTRAINT `FK_CUSTOMER` FOREIGN KEY (`CUSTOMER_ID`) REFERENCES `customer` (`ID`);

--
-- Constraints for table `customer`
--
ALTER TABLE `customer`
  ADD CONSTRAINT `FK_USER` FOREIGN KEY (`USER_ID`) REFERENCES `user` (`ID`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `FK_CUSTOMERS` FOREIGN KEY (`CUSTOMER_ID`) REFERENCES `customer` (`ID`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `FK_ORDERS` FOREIGN KEY (`ORDER_ID`) REFERENCES `orders` (`ID`),
  ADD CONSTRAINT `FK_PRODUCTS` FOREIGN KEY (`PRODUCT_ID`) REFERENCES `products` (`ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
