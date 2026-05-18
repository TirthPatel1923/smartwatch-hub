-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: smartwatch_db
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `cart`
--

DROP TABLE IF EXISTS `cart`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `session_id` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cart`
--

LOCK TABLES `cart` WRITE;
/*!40000 ALTER TABLE `cart` DISABLE KEYS */;
INSERT INTO `cart` VALUES (14,953,1,'5kfo3bmr3s0t8v6tjiobr9pcn1','2026-04-14 09:30:09');
/*!40000 ALTER TABLE `cart` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(150) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
INSERT INTO `order_items` VALUES (3,2,24,'Withings ScanWatch',3,279.99),(4,2,956,'Mobvoi TicWatch E3',4,149.99),(5,3,1,'Apple Watch Ultra',1,799.99),(6,4,2,'Samsung Galaxy Watch 6',1,399.99),(7,5,1,'Apple Watch Ultra',1,799.99),(8,6,2,'Samsung Galaxy Watch 6',1,399.99),(9,7,4,'Fitbit Sense 2',7,299.99);
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_name` varchar(100) NOT NULL,
  `customer_email` varchar(150) NOT NULL,
  `customer_phone` varchar(30) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `total_price` decimal(12,2) NOT NULL,
  `status` enum('pending','processing','completed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (2,'tirth patel','tirthkpatel51@gmail.com','0423155452','12 Thames cresent',1594.92,'completed','2026-04-24 07:44:26'),(3,'Test Customer 1','customer1@example.com','111-222-3333','123 Test St',899.99,'completed','2026-04-24 07:58:59'),(4,'Test Customer 2','customer2@example.com','444-555-6666','456 Test Ave',599.99,'completed','2026-04-24 07:58:59'),(5,'Test Customer 1','customer1@example.com','111-222-3333','123 Test St',899.99,'completed','2026-04-24 08:00:04'),(6,'Test Customer 2','customer2@example.com','444-555-6666','456 Test Ave',599.99,'completed','2026-04-24 08:00:04'),(7,'tirth patel','tirthkpatel51@gmail.com','0423155452','12 Thames cresent',2320.92,'completed','2026-04-24 11:59:56');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `brand` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `features` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `stock` int(11) DEFAULT 10,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `category` varchar(50) NOT NULL DEFAULT 'General',
  `colors` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=3438 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,'Apple Watch Ultra','Apple',799.99,'Premium sports smartwatch with titanium body','Always-on display, Fitness+, ECG, Blood oxygen','apple-watch-ultra.jpg',15,'2026-03-27 09:28:25','Luxury',NULL),(2,'Samsung Galaxy Watch 6','Samsung',399.99,'Advanced health & fitness tracker','AMOLED display, SpO2, Sleep tracking, 5+ days battery','samsung-galaxy-watch-6.jpg',20,'2026-03-27 09:28:25','Fitness',NULL),(3,'Garmin Epix Gen 2','Garmin',599.99,'Premium outdoor smartwatch','AMOLED display, Multi-GNSS, Training metrics, 11 days battery','garmin-epix-gen2.jpg',12,'2026-03-27 09:28:25','Sports',NULL),(4,'Fitbit Sense 2','Fitbit',299.99,'Health-focused wearable','EDA sensor, Stress management, 6-day battery','fitbit-sense2.jpg',18,'2026-03-27 09:28:25','Fitness',NULL),(5,'Huawei Watch 4','Huawei',349.99,'Sleek AMOLED smartwatch','Health monitoring, Sleep tracking, 2-week battery','huawei-watch4.jpg',18,'2026-03-27 09:28:25','Everyday',NULL),(6,'Amazfit GTS 4','Amazfit',179.99,'Budget-friendly AMOLED watch','150+ sports modes, 14-day battery, SpO2 tracking','amazfit-gts4.jpg',30,'2026-03-27 09:28:25','Budget',NULL),(7,'Wear OS by Google','Fossil',249.99,'Google Wear OS powered smartwatch','Google Assistant, Google Maps integration, 24hr battery','fossil-wear-os.jpg',16,'2026-03-27 09:28:25','Everyday',NULL),(8,'OnePlus Watch 2','OnePlus',429.99,'Gaming-focused smartwatch','Snapdragon 4100+ processor, 500 apps, 100+ sport modes','oneplus-watch2.jpg',13,'2026-03-27 09:28:25','Performance',NULL),(17,'Apple Watch Series 9','Apple',399.99,'Latest Apple Watch with advanced health features','Double Tap gesture, Precision Finding, Crash Detection','https://images.unsplash.com/photo-1551816230-ef5deaed4a26?auto=format&fit=crop&w=1200&q=80',10,'2026-03-29 04:35:55','Luxury','Midnight,Starlight,Product Red'),(18,'Samsung Galaxy Watch 5','Samsung',279.99,'Compact fitness tracker with bioelectrical impedance analysis','Body composition analysis, Advanced sleep coaching, 40+ exercise modes','https://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&w=1200&q=80',21,'2026-03-29 04:35:55','Fitness','Graphite,Silver,Sapphire'),(19,'Garmin Forerunner 265','Garmin',449.99,'GPS running watch with advanced training metrics','Wrist-based heart rate, PacePro technology, 13+ hours GPS battery','https://images.unsplash.com/photo-1544117519-31a4b719223d?auto=format&fit=crop&w=1200&q=80',8,'2026-03-29 04:35:55','Sports','Black,White,Blue'),(20,'Fitbit Versa 4','Fitbit',229.99,'Advanced fitness and health tracker','Active Zone Minutes, Cardio Fitness, 6+ day battery','https://images.unsplash.com/photo-1508685096489-7aacd43bd3b1?auto=format&fit=crop&w=1200&q=80',10,'2026-03-29 04:35:55','Fitness','Black,Lunar White,Pink Clay'),(21,'Huawei Watch GT 3','Huawei',199.99,'Long-lasting battery smartwatch for daily health tracking','14-day battery, 100+ sports modes, SpO2 monitoring','https://images.unsplash.com/photo-1524592094714-0f0654e20314?auto=format&fit=crop&w=1200&q=80',17,'2026-03-29 04:35:55','Everyday','Black,Brown,Green'),(22,'Amazfit Bip 3 Pro','Amazfit',79.99,'Affordable fitness tracker with AMOLED display','14-day battery, 60+ sports modes, Blood oxygen monitoring','https://images.unsplash.com/photo-1542496658-e33a6dcca2e6?auto=format&fit=crop&w=1200&q=80',35,'2026-03-29 04:35:55','Budget','Black,Blue,Pink'),(23,'TicWatch Pro 3','Mobvoi',299.99,'Dual-display smartwatch with Snapdragon Wear 4100','TicMotion, 3-day battery, Google services integration','https://images.unsplash.com/photo-1583394838336-acd977736f90?auto=format&fit=crop&w=1200&q=80',11,'2026-03-29 04:35:55','Performance','Gunmetal,Flame Red'),(24,'Withings ScanWatch','Withings',279.99,'Hybrid smartwatch focused on health monitoring','ECG, SpO2, Temperature sensor, 30-day battery','https://images.unsplash.com/photo-1434494878577-86c23bcb06b9?auto=format&fit=crop&w=1200&q=80',6,'2026-03-29 04:35:55','Health','White,Black'),(953,'Coros Pace 3','Coros',249.99,'Lightweight GPS running watch with long battery life','7-day battery, Training status, Recovery time, 200+ sports modes','https://images.unsplash.com/photo-1575311373937-040b8e1fd5b6?auto=format&fit=crop&w=1200&q=80',13,'2026-03-31 09:17:12','Sports','Black,White,Blue'),(954,'Polar Vantage V3','Polar',499.99,'Advanced sports watch with AI-powered training guidance','Training Load Pro, Nightly Recharge, FuelWise, 7-day battery','https://images.unsplash.com/photo-1508685096489-7aacd43bd3b1?auto=format&fit=crop&w=1200&q=80',7,'2026-03-31 09:17:12','Sports','Black,White,Orange'),(955,'Suunto 9 Peak','Suunto',399.99,'Rugged outdoor GPS watch for extreme conditions','Titanium bezel, Sapphire glass, 25-day battery, Deep depth gauge','https://images.unsplash.com/photo-1544117519-31a4b719223d?auto=format&fit=crop&w=1200&q=80',6,'2026-03-31 09:17:12','Sports','Titanium,Black'),(956,'Mobvoi TicWatch E3','Mobvoi',149.99,'Affordable Wear OS smartwatch with Google Assistant','Google services, 2-day battery, Swim-proof, Health monitoring','https://images.unsplash.com/photo-1583394838336-acd977736f90?auto=format&fit=crop&w=1200&q=80',17,'2026-03-31 09:17:12','Budget','Black,Silver,Gold');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `quiz_results`
--

DROP TABLE IF EXISTS `quiz_results`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `quiz_results` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(100) NOT NULL,
  `answers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`answers`)),
  `score` int(11) NOT NULL,
  `total_questions` int(11) NOT NULL,
  `recommendations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`recommendations`)),
  `completed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `quiz_results`
--

LOCK TABLES `quiz_results` WRITE;
/*!40000 ALTER TABLE `quiz_results` DISABLE KEYS */;
INSERT INTO `quiz_results` VALUES (1,'tirth','[2,2,0,0,2]',5,5,'[{\"name\":\"Garmin Epix Gen 2\",\"reason\":\"Ultimate outdoor and sports companion\"},{\"name\":\"Garmin Forerunner 265\",\"reason\":\"GPS running watch with training metrics\"}]','2026-03-31 08:21:11'),(2,'tirth','[0,3,0,0,3]',5,5,'[{\"name\":\"Apple Watch Ultra\",\"reason\":\"Premium titanium build with extreme durability\"},{\"name\":\"Apple Watch Series 9\",\"reason\":\"Latest Apple innovation\"}]','2026-03-31 08:28:17');
/*!40000 ALTER TABLE `quiz_results` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_submissions`
--

DROP TABLE IF EXISTS `user_submissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_submissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(30) NOT NULL,
  `favorite_model` varchar(100) NOT NULL,
  `message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_submissions`
--

LOCK TABLES `user_submissions` WRITE;
/*!40000 ALTER TABLE `user_submissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_submissions` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-04-24 21:55:25
