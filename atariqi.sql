-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               5.7.24 - MySQL Community Server (GPL)
-- Server OS:                    Win64
-- HeidiSQL Version:             10.2.0.5599
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Dumping database structure for atariqi
CREATE DATABASE IF NOT EXISTS `atariqi` /*!40100 DEFAULT CHARACTER SET utf8mb4 */;
USE `atariqi`;

-- Dumping structure for table atariqi.announce
CREATE TABLE IF NOT EXISTS `announce` (
  `id` bigint(20) NOT NULL,
  `title-ar` char(255) NOT NULL,
  `title-eng` char(255) NOT NULL,
  `contant-ar` text NOT NULL,
  `contant-eng` text NOT NULL,
  `date-of-add` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date-of-edit` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping data for table atariqi.announce: ~0 rows (approximately)
/*!40000 ALTER TABLE `announce` DISABLE KEYS */;
/*!40000 ALTER TABLE `announce` ENABLE KEYS */;

-- Dumping structure for table atariqi.calling-key
CREATE TABLE IF NOT EXISTS `calling-key` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `call-key` int(11) NOT NULL,
  `country` varchar(50) NOT NULL,
  `date-of-add` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date-of-edit` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table atariqi.calling-key: ~2 rows (approximately)
/*!40000 ALTER TABLE `calling-key` DISABLE KEYS */;
INSERT INTO `calling-key` (`id`, `call-key`, `country`, `date-of-add`, `date-of-edit`) VALUES
	(1, 966, 'Saudi Arabia', '2023-08-16 16:35:36', NULL),
	(2, 20, 'Egypt', '2023-08-16 16:35:36', NULL);
/*!40000 ALTER TABLE `calling-key` ENABLE KEYS */;

-- Dumping structure for table atariqi.cities
CREATE TABLE IF NOT EXISTS `cities` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `city-ar` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `city-en` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table atariqi.cities: ~0 rows (approximately)
/*!40000 ALTER TABLE `cities` DISABLE KEYS */;
INSERT INTO `cities` (`id`, `city-ar`, `city-en`) VALUES
	(1, 'الرياض', 'Riyadh');
/*!40000 ALTER TABLE `cities` ENABLE KEYS */;

-- Dumping structure for table atariqi.counter
CREATE TABLE IF NOT EXISTS `counter` (
  `id` bigint(20) NOT NULL,
  `counting-feild` varchar(50) NOT NULL,
  `count` bigint(20) NOT NULL,
  `date-of-add` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date-of-edit` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping data for table atariqi.counter: ~0 rows (approximately)
/*!40000 ALTER TABLE `counter` DISABLE KEYS */;
/*!40000 ALTER TABLE `counter` ENABLE KEYS */;

-- Dumping structure for table atariqi.document
CREATE TABLE IF NOT EXISTS `document` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `title-ar` varchar(100) NOT NULL,
  `title-eng` varchar(100) NOT NULL,
  `file-link` char(255) NOT NULL,
  `date-of-add` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date-of-edit` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table atariqi.document: ~2 rows (approximately)
/*!40000 ALTER TABLE `document` DISABLE KEYS */;
INSERT INTO `document` (`id`, `title-ar`, `title-eng`, `file-link`, `date-of-add`, `date-of-edit`) VALUES
	(1, 'الشروط  و الأحكام', 'Terms and Conditions', 'documents/conditions.pdf', '2023-08-25 14:38:49', NULL),
	(2, 'سياسة الخصوصية', 'privacy policy', 'documents/policies.pdf', '2023-08-25 14:38:49', NULL);
/*!40000 ALTER TABLE `document` ENABLE KEYS */;

-- Dumping structure for table atariqi.driver-info
CREATE TABLE IF NOT EXISTS `driver-info` (
  `id` bigint(20) NOT NULL,
  `driver-id` bigint(20) NOT NULL,
  `car-brand` varchar(50) NOT NULL,
  `car-model` bigint(20) NOT NULL,
  `car-number` int(11) NOT NULL,
  `car-letters` varchar(10) NOT NULL,
  `car-color` varchar(50) NOT NULL,
  `driver-neighborhood` varchar(50) NOT NULL,
  `driver-rate` int(11) NOT NULL DEFAULT '0',
  `driver-license-link` varchar(225) NOT NULL,
  `allow-disabilities` varchar(25) NOT NULL DEFAULT 'yes',
  `date-of-add` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date-of-edit` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `driver-id` (`driver-id`),
  CONSTRAINT `driver-info_ibfk_1` FOREIGN KEY (`driver-id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping data for table atariqi.driver-info: ~0 rows (approximately)
/*!40000 ALTER TABLE `driver-info` DISABLE KEYS */;
INSERT INTO `driver-info` (`id`, `driver-id`, `car-brand`, `car-model`, `car-number`, `car-letters`, `car-color`, `driver-neighborhood`, `driver-rate`, `driver-license-link`, `allow-disabilities`, `date-of-add`, `date-of-edit`) VALUES
	(1, 2, 'شيفروليه كابتيفا', 2023, 111, 'abc', 'white', 'حي القدس', 3, 'license/504774391_license_link.png', 'yes', '2023-09-04 01:58:06', NULL);
/*!40000 ALTER TABLE `driver-info` ENABLE KEYS */;

-- Dumping structure for table atariqi.drivers-neighborhoods
CREATE TABLE IF NOT EXISTS `drivers-neighborhoods` (
  `id` bigint(20) NOT NULL,
  `driver-id` bigint(20) NOT NULL,
  `neighborhoods-to` text NOT NULL,
  `neighborhoods-from` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user-id` (`driver-id`),
  CONSTRAINT `drivers-neighborhoods_ibfk_1` FOREIGN KEY (`driver-id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping data for table atariqi.drivers-neighborhoods: ~0 rows (approximately)
/*!40000 ALTER TABLE `drivers-neighborhoods` DISABLE KEYS */;
INSERT INTO `drivers-neighborhoods` (`id`, `driver-id`, `neighborhoods-to`, `neighborhoods-from`) VALUES
	(1, 2, '1 | 2 | 3', '1 | 2 | 3');
/*!40000 ALTER TABLE `drivers-neighborhoods` ENABLE KEYS */;

-- Dumping structure for table atariqi.drivers-schedule
CREATE TABLE IF NOT EXISTS `drivers-schedule` (
  `id` bigint(20) NOT NULL,
  `driver-id` bigint(20) NOT NULL,
  `Saturday-to` time DEFAULT NULL,
  `Saturday-from` time DEFAULT NULL,
  `Sunday-to` time DEFAULT NULL,
  `Sunday-from` time DEFAULT NULL,
  `Monday-to` time DEFAULT NULL,
  `Monday-from` time DEFAULT NULL,
  `Tuesday-to` time DEFAULT NULL,
  `Tuesday-from` time DEFAULT NULL,
  `Wednesday-to` time DEFAULT NULL,
  `Wednesday-from` time DEFAULT NULL,
  `Thursday-to` time DEFAULT NULL,
  `Thursday-from` time DEFAULT NULL,
  `date-of-add` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date-of-edit` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user-id` (`driver-id`),
  CONSTRAINT `drivers-schedule_ibfk_1` FOREIGN KEY (`driver-id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping data for table atariqi.drivers-schedule: ~0 rows (approximately)
/*!40000 ALTER TABLE `drivers-schedule` DISABLE KEYS */;
INSERT INTO `drivers-schedule` (`id`, `driver-id`, `Saturday-to`, `Saturday-from`, `Sunday-to`, `Sunday-from`, `Monday-to`, `Monday-from`, `Tuesday-to`, `Tuesday-from`, `Wednesday-to`, `Wednesday-from`, `Thursday-to`, `Thursday-from`, `date-of-add`, `date-of-edit`) VALUES
	(1, 2, '21:00:00', '14:00:00', '08:00:00', '14:00:00', '09:00:00', '15:00:00', '08:00:00', '13:00:00', '08:00:00', '13:00:00', NULL, NULL, '2023-08-16 17:07:19', NULL);
/*!40000 ALTER TABLE `drivers-schedule` ENABLE KEYS */;

-- Dumping structure for table atariqi.drivers-services
CREATE TABLE IF NOT EXISTS `drivers-services` (
  `id` bigint(20) NOT NULL,
  `driver-id` bigint(20) NOT NULL,
  `service-id` bigint(20) NOT NULL,
  `date-of-add` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `driver-id` (`driver-id`),
  KEY `service-id` (`service-id`),
  CONSTRAINT `drivers-services_ibfk_1` FOREIGN KEY (`driver-id`) REFERENCES `users` (`id`),
  CONSTRAINT `drivers-services_ibfk_2` FOREIGN KEY (`service-id`) REFERENCES `services` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping data for table atariqi.drivers-services: ~2 rows (approximately)
/*!40000 ALTER TABLE `drivers-services` DISABLE KEYS */;
INSERT INTO `drivers-services` (`id`, `driver-id`, `service-id`, `date-of-add`) VALUES
	(1, 2, 1, '2023-08-16 16:55:13'),
	(2, 2, 3, '2023-08-16 16:55:13');
/*!40000 ALTER TABLE `drivers-services` ENABLE KEYS */;

-- Dumping structure for table atariqi.first-announce
CREATE TABLE IF NOT EXISTS `first-announce` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `title-ar` char(255) NOT NULL,
  `title-eng` char(255) NOT NULL,
  `contant-ar` text NOT NULL,
  `contant-eng` text NOT NULL,
  `date-of-add` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date-of-edit` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table atariqi.first-announce: ~2 rows (approximately)
/*!40000 ALTER TABLE `first-announce` DISABLE KEYS */;
INSERT INTO `first-announce` (`id`, `title-ar`, `title-eng`, `contant-ar`, `contant-eng`, `date-of-add`, `date-of-edit`) VALUES
	(1, 'التوصيل المباشر', 'Direct delivery', 'جميع كباتن عطريقي يوفرون لكم خدمة التوصيل اللحظي ب20 ريال فقط. كل ما عليك هو تحديد موقعك وسيقوم التطبيق بربطك بأقرب كابتن إليك', 'All Atriqi captains provide instant delivery service for only 20 riyals. All you have to do is specify your location and the application will connect you with the nearest captain to you', '2023-09-03 23:19:32', NULL),
	(2, 'التوصيل المجدول', 'Scheduled delivery', 'بعض كباتن عطريقي يوفرون خدمة التوصيل المجدولة لفترة زمية محددة ذهاب وعودة و برسوم ثابته كـ: جدولة توصيل لمدة يوم ب35 ريال / جدولة توصيل لمده أسبوع ب300 ريال.\r\n\r\nكل ما عليك هو تحديد جدول التوصيل الذي ترغب به وسيقوم التطبيق بعرض قائمة الكباتن القريبين منك الذين يوفرون هذه الخدمة ', 'Some Atriqi captains provide a scheduled delivery service for a specific period of time, round-trip, with fixed fees, such as: scheduling a delivery for a day for 35 riyals / scheduling a delivery for a week for 300 riyals.\r\n\r\nAll you have to do is select the delivery schedule you want and the application will display a list of captains near you who provide this service', '2023-09-03 23:19:32', NULL);
/*!40000 ALTER TABLE `first-announce` ENABLE KEYS */;

-- Dumping structure for table atariqi.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table atariqi.migrations: ~41 rows (approximately)
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '2023_08_26_182811_create_calling_key_table', 0),
	(2, '2023_08_26_182811_create_document_table', 0),
	(3, '2023_08_26_182811_create_opening_table', 0),
	(4, '2023_08_26_182811_create_services_table', 0),
	(5, '2023_08_26_182811_create_stages_table', 0),
	(6, '2023_08_26_182811_create_university_table', 0),
	(7, '2023_08_26_182811_create_user_login_table', 0),
	(8, '2023_08_26_182811_create_users_table', 0),
	(9, '2023_08_26_182814_add_foreign_keys_to_user_login_table', 0),
	(10, '2023_08_26_182814_add_foreign_keys_to_users_table', 0),
	(11, '2019_12_14_000001_create_personal_access_tokens_table', 1),
	(12, '2023_08_28_203115_add_code_to_users_table', 2),
	(13, '2023_08_28_222343_add_fcm_token_to_users_table', 3),
	(14, '2023_09_06_175115_create_announce_table', 0),
	(15, '2023_09_06_175115_create_calling_key_table', 0),
	(16, '2023_09_06_175115_create_counter_table', 0),
	(17, '2023_09_06_175115_create_document_table', 0),
	(18, '2023_09_06_175115_create_driver_info_table', 0),
	(19, '2023_09_06_175115_create_drivers_neighborhoods_table', 0),
	(20, '2023_09_06_175115_create_drivers_schedule_table', 0),
	(21, '2023_09_06_175115_create_drivers_services_table', 0),
	(22, '2023_09_06_175115_create_first_announce_table', 0),
	(23, '2023_09_06_175115_create_opening_table', 0),
	(24, '2023_09_06_175115_create_personal_access_tokens_table', 0),
	(25, '2023_09_06_175115_create_ride_booking_table', 0),
	(27, '2023_09_06_175115_create_services_table', 0),
	(28, '2023_09_06_175115_create_stages_table', 0),
	(29, '2023_09_06_175115_create_suggestions_drivers_table', 0),
	(30, '2023_09_06_175115_create_uni_driving_services_table', 0),
	(31, '2023_09_06_175115_create_university_table', 0),
	(32, '2023_09_06_175115_create_user_login_table', 0),
	(33, '2023_09_06_175115_create_users_table', 0),
	(34, '2023_09_06_175118_add_foreign_keys_to_driver_info_table', 0),
	(35, '2023_09_06_175118_add_foreign_keys_to_drivers_neighborhoods_table', 0),
	(36, '2023_09_06_175118_add_foreign_keys_to_drivers_schedule_table', 0),
	(38, '2023_09_06_175118_add_foreign_keys_to_user_login_table', 0),
	(39, '2023_09_06_175118_add_foreign_keys_to_users_table', 0),
	(41, '2023_09_06_171734_create_cities_table', 4),
	(42, '2023_09_06_175115_create_neighborhoods_table', 4),
	(43, '2023_09_07_105834_add_city_id_to_university_table', 5),
	(44, '2023_09_19_180427_add_lat_lng_to_ride_booking_table', 6),
	(45, '2023_09_21_002758_add_image_to_users_table', 7),
	(46, '2023_09_21_003244_add_lang_to_services_table', 7);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;

-- Dumping structure for table atariqi.neighborhoods
CREATE TABLE IF NOT EXISTS `neighborhoods` (
  `id` bigint(20) NOT NULL,
  `neighborhood-ar` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `neighborhood-eng` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `city_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `neighborhoods_city_id_foreign` (`city_id`),
  CONSTRAINT `neighborhoods_city_id_foreign` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table atariqi.neighborhoods: ~10 rows (approximately)
/*!40000 ALTER TABLE `neighborhoods` DISABLE KEYS */;
INSERT INTO `neighborhoods` (`id`, `neighborhood-ar`, `neighborhood-eng`, `city_id`) VALUES
	(1, 'حي الربيع', 'Al-Rabiea', 1),
	(2, 'حي الندى', 'Al-Nada', 1),
	(3, 'حي الصحافة', 'Al-Sahafa', 1),
	(4, 'حي النرجس', 'Al-Narges', 1),
	(5, 'حي النفل', 'Al-Nafl', 1),
	(6, 'حي العقيق', 'Al-Aqiq', 1),
	(7, 'حي الوادي', 'Al-Wadi', 1),
	(8, 'حي الغدير', 'Al-Ghadeer', 1),
	(9, 'حي الياسمين', 'Al-Yasamin', 1),
	(10, 'حي العارض', 'Al-Arid', 1);
/*!40000 ALTER TABLE `neighborhoods` ENABLE KEYS */;

-- Dumping structure for table atariqi.opening
CREATE TABLE IF NOT EXISTS `opening` (
  `id` bigint(20) NOT NULL,
  `title-ar` varchar(50) NOT NULL,
  `title-eng` varchar(50) NOT NULL,
  `contant-ar` text NOT NULL,
  `contant-eng` text NOT NULL,
  `date-of-add` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date-of-edit` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping data for table atariqi.opening: ~5 rows (approximately)
/*!40000 ALTER TABLE `opening` DISABLE KEYS */;
INSERT INTO `opening` (`id`, `title-ar`, `title-eng`, `contant-ar`, `contant-eng`, `date-of-add`, `date-of-edit`) VALUES
	(1, 'من سيوصلك؟', 'Who will drive you?', 'أحصل على توصيلة من زميل لك من نفس جامعتك و وسع دائرة معارفك', 'Get a ride from a colleague from the same university as you and expand your circle of acquaintances', '2023-08-25 14:34:05', NULL),
	(2, 'شارك المركبة', 'Share the car', 'قم بمشاركة التوصيلة مع زملائك الجامعيين لتصبح الرحلة ممتعة', 'Share the ride with your fellow students to make the trip fun', '2023-08-25 14:34:05', NULL),
	(3, 'إلى أين؟', 'To where?', 'اذهب من وإلى الجامعة مع زملائك القريبين من منطقتك السكنية', 'Go to and from university with classmates close to your neighbourhood', '2023-08-25 14:34:05', NULL),
	(4, 'كم التكلفة؟', 'how much?', 'استمتع بالرحلة و أنسى هموم تكاليف التوصيل المرتفعة', 'Enjoy the trip and forget the worries of high delivery costs', '2023-08-25 14:34:05', NULL),
	(5, 'ابدأ معنا', 'Start with us', 'نرحب بكم في عائلتنا ويسرنا خدمتكم', 'We welcome you to our family and we are pleased to serve you', '2023-08-25 14:34:05', NULL);
/*!40000 ALTER TABLE `opening` ENABLE KEYS */;

-- Dumping structure for table atariqi.personal_access_tokens
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table atariqi.personal_access_tokens: ~3 rows (approximately)
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `created_at`, `updated_at`) VALUES
	(1, 'App\\Models\\User', 3, 'atariqi', '8c0ce2bbadef1a5b6365fb88b1dbbfc17818708c46ad8a1a39d9d568ad8d2abc', '["*"]', NULL, '2023-08-29 00:29:48', '2023-08-29 00:29:48'),
	(2, 'App\\Models\\User', 4, 'atariqi', 'fcf0383807aab8d374ca8d8d7d0b81ab04ee321aab49b340e842a6478a9c93ff', '["*"]', NULL, '2023-09-07 12:55:35', '2023-09-07 12:55:35'),
	(3, 'App\\Models\\User', 4, 'atariqi', '01d97313f5469996d5236618bd2216c271bfa90d4cab0bc78cd744217cf05fdf', '["*"]', '2023-09-23 23:20:03', '2023-09-18 18:52:35', '2023-09-23 23:20:03');
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;

-- Dumping structure for table atariqi.ride-booking
CREATE TABLE IF NOT EXISTS `ride-booking` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `passenger-id` bigint(20) NOT NULL,
  `neighborhood-id` bigint(20) NOT NULL,
  `location` text,
  `service-id` bigint(20) NOT NULL,
  `action` int(11) NOT NULL,
  `date-of-add` datetime NOT NULL,
  `lat` varchar(255) DEFAULT NULL,
  `lng` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `passenger-id` (`passenger-id`),
  KEY `service-id` (`service-id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table atariqi.ride-booking: ~15 rows (approximately)
/*!40000 ALTER TABLE `ride-booking` DISABLE KEYS */;
INSERT INTO `ride-booking` (`id`, `passenger-id`, `neighborhood-id`, `location`, `service-id`, `action`, `date-of-add`, `lat`, `lng`) VALUES
	(2, 1, 1, NULL, 1, 3, '2023-09-21 00:15:01', '123', '1234'),
	(3, 1, 1, NULL, 1, 3, '2023-09-21 01:33:03', '123', '1234'),
	(4, 1, 1, NULL, 1, 3, '2023-09-21 01:35:23', '123', '1234'),
	(5, 1, 1, NULL, 1, 3, '2023-09-21 01:35:40', '123', '1234'),
	(6, 1, 1, NULL, 1, 2, '2023-09-23 17:53:58', '123', '1234'),
	(7, 1, 1, NULL, 1, 2, '2023-09-23 17:54:37', '123', '1234'),
	(8, 1, 1, NULL, 1, 2, '2023-09-23 18:00:12', '123', '1234'),
	(9, 1, 1, NULL, 1, 3, '2023-09-23 18:08:23', '123', '1234'),
	(10, 1, 1, NULL, 1, 2, '2023-09-23 18:30:33', '123', '1234'),
	(11, 1, 1, NULL, 1, 2, '2023-09-23 18:32:07', '123', '1234'),
	(12, 1, 1, NULL, 1, 2, '2023-09-23 23:17:17', '123', '1234'),
	(13, 1, 1, NULL, 1, 3, '2023-09-23 23:18:23', '123', '1234'),
	(14, 1, 1, NULL, 1, 2, '2023-09-23 23:18:32', '123', '1234'),
	(15, 1, 1, NULL, 1, 2, '2023-09-23 23:19:28', '123', '1234'),
	(16, 1, 1, NULL, 1, 3, '2023-09-23 23:20:03', '123', '1234');
/*!40000 ALTER TABLE `ride-booking` ENABLE KEYS */;

-- Dumping structure for table atariqi.services
CREATE TABLE IF NOT EXISTS `services` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `service` varchar(100) NOT NULL,
  `cost` bigint(20) NOT NULL,
  `date-of-add` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date-of-edit` datetime DEFAULT NULL,
  `service-ar` varchar(255) DEFAULT NULL,
  `service-eng` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table atariqi.services: ~5 rows (approximately)
/*!40000 ALTER TABLE `services` DISABLE KEYS */;
INSERT INTO `services` (`id`, `service`, `cost`, `date-of-add`, `date-of-edit`, `service-ar`, `service-eng`) VALUES
	(1, 'immediately drive | توصيل لحظي', 20, '2023-08-16 16:51:58', NULL, 'توصيل لحظي', 'immediately drive'),
	(2, 'daily drive (go or back) | توصيل يومي (ذهاب أو عودة)', 18, '2023-08-16 16:51:58', NULL, 'توصيل يومي (ذهاب أو عودة)', 'daily drive (go or back)'),
	(3, 'daily drive (go and back) | توصيل يومي (ذهاب و عودة)', 30, '2023-08-16 16:51:58', NULL, 'توصيل يومي (ذهاب و عودة)', 'daily drive (go and back)'),
	(4, 'weekly drive (go or back) | توصيل أسبوعي (ذهاب أو عودة)', 150, '2023-08-16 16:51:58', NULL, 'توصيل أسبوعي (ذهاب أو عودة)', 'weekly drive (go or back)'),
	(5, 'weekly drive (go and back) | توصيل أسبوعي (ذهاب و عودة)', 300, '2023-08-16 16:51:58', NULL, 'توصيل أسبوعي (ذهاب و عودة)', 'weekly drive (go and back)');
/*!40000 ALTER TABLE `services` ENABLE KEYS */;

-- Dumping structure for table atariqi.stages
CREATE TABLE IF NOT EXISTS `stages` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name-ar` varchar(50) NOT NULL,
  `name-eng` varchar(50) NOT NULL,
  `date-of-add` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date-of-edit` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table atariqi.stages: ~5 rows (approximately)
/*!40000 ALTER TABLE `stages` DISABLE KEYS */;
INSERT INTO `stages` (`id`, `name-ar`, `name-eng`, `date-of-add`, `date-of-edit`) VALUES
	(1, 'طالب', 'student', '2023-08-16 16:39:27', NULL),
	(2, 'دكتور جامعي', 'Professor', '2023-08-16 16:39:27', NULL),
	(3, 'مساعد دكتور', 'assistant', '2023-08-16 16:39:27', NULL),
	(4, 'موظف', 'employee', '2023-08-16 16:39:27', NULL),
	(5, 'إداري', 'administrative', '2023-08-16 16:39:27', NULL);
/*!40000 ALTER TABLE `stages` ENABLE KEYS */;

-- Dumping structure for table atariqi.suggestions-drivers
CREATE TABLE IF NOT EXISTS `suggestions-drivers` (
  `id` bigint(20) NOT NULL,
  `booking-id` bigint(20) NOT NULL,
  `driver-id` bigint(20) NOT NULL,
  `action` int(11) NOT NULL DEFAULT '0',
  `date-of-add` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date-of-edit` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping data for table atariqi.suggestions-drivers: ~0 rows (approximately)
/*!40000 ALTER TABLE `suggestions-drivers` DISABLE KEYS */;
/*!40000 ALTER TABLE `suggestions-drivers` ENABLE KEYS */;

-- Dumping structure for table atariqi.uni-driving-services
CREATE TABLE IF NOT EXISTS `uni-driving-services` (
  `id` bigint(20) NOT NULL,
  `university-id` bigint(20) NOT NULL,
  `service-id` bigint(20) NOT NULL,
  `date-of-add` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping data for table atariqi.uni-driving-services: ~2 rows (approximately)
/*!40000 ALTER TABLE `uni-driving-services` DISABLE KEYS */;
INSERT INTO `uni-driving-services` (`id`, `university-id`, `service-id`, `date-of-add`) VALUES
	(1, 1, 1, '2023-08-16 16:56:16'),
	(2, 1, 3, '2023-08-16 16:56:16');
/*!40000 ALTER TABLE `uni-driving-services` ENABLE KEYS */;

-- Dumping structure for table atariqi.university
CREATE TABLE IF NOT EXISTS `university` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name-ar` char(255) NOT NULL,
  `name-eng` char(255) NOT NULL,
  `country` varchar(100) NOT NULL,
  `city` varchar(100) NOT NULL,
  `city_id` bigint(20) unsigned DEFAULT NULL,
  `location` text NOT NULL,
  `date-of-add` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date-of-edit` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `university_city_id_foreign` (`city_id`),
  CONSTRAINT `university_city_id_foreign` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table atariqi.university: ~0 rows (approximately)
/*!40000 ALTER TABLE `university` DISABLE KEYS */;
INSERT INTO `university` (`id`, `name-ar`, `name-eng`, `country`, `city`, `city_id`, `location`, `date-of-add`, `date-of-edit`) VALUES
	(1, 'جامعة الملك سعود', 'King Saud University', 'Saudi Arabia', 'riyadh', 1, 'https://goo.gl/maps/XuXVx5GHLDzA3p9M9', '2023-08-16 16:34:53', NULL);
/*!40000 ALTER TABLE `university` ENABLE KEYS */;

-- Dumping structure for table atariqi.user-login
CREATE TABLE IF NOT EXISTS `user-login` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user-id` bigint(20) NOT NULL,
  `date-time` datetime DEFAULT NULL,
  `login-logout` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user-id` (`user-id`),
  CONSTRAINT `user-login_ibfk_1` FOREIGN KEY (`user-id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table atariqi.user-login: ~2 rows (approximately)
/*!40000 ALTER TABLE `user-login` DISABLE KEYS */;
INSERT INTO `user-login` (`id`, `user-id`, `date-time`, `login-logout`) VALUES
	(1, 4, '2023-09-07 12:55:35', 1),
	(2, 4, '2023-09-18 18:52:35', 1);
/*!40000 ALTER TABLE `user-login` ENABLE KEYS */;

-- Dumping structure for table atariqi.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user-first-name` varchar(20) NOT NULL,
  `user-last-name` varchar(20) NOT NULL,
  `call-key-id` bigint(20) NOT NULL,
  `phone-no` varchar(20) NOT NULL,
  `gender` varchar(50) NOT NULL,
  `university-id` bigint(20) NOT NULL,
  `user-stage-id` bigint(20) NOT NULL,
  `email` char(255) NOT NULL,
  `approval` tinyint(1) NOT NULL DEFAULT '1',
  `user-type` varchar(50) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `date-of-add` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date-of-edit` datetime DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL,
  `fcm_token` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `university-id` (`university-id`),
  KEY `user-stage-id` (`user-stage-id`),
  KEY `call-key-id` (`call-key-id`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`university-id`) REFERENCES `university` (`id`),
  CONSTRAINT `users_ibfk_2` FOREIGN KEY (`user-stage-id`) REFERENCES `stages` (`id`),
  CONSTRAINT `users_ibfk_3` FOREIGN KEY (`call-key-id`) REFERENCES `calling-key` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table atariqi.users: ~3 rows (approximately)
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`id`, `user-first-name`, `user-last-name`, `call-key-id`, `phone-no`, `gender`, `university-id`, `user-stage-id`, `email`, `approval`, `user-type`, `image`, `date-of-add`, `date-of-edit`, `code`, `fcm_token`) VALUES
	(1, 'user1', 'family', 1, '504444444', 'girl', 1, 1, '123456@seu.edu.sa', 1, 'passenger', NULL, '2023-08-16 16:41:05', NULL, NULL, NULL),
	(2, 'haneen', 'almaliki', 1, '504774391', 'girl', 1, 1, '123@ksu.edu.sa', 1, 'driver', 'avatar.png', '2023-09-03 23:07:05', NULL, NULL, NULL),
	(4, 'Omar', 'Zizo', 2, '1007958185', 'male', 1, 1, 'omarzizo207@gmail.com', 1, 'passenger', NULL, '2023-08-29 18:43:00', NULL, '6377', 'sda');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
