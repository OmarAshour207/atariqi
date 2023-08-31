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

-- Dumping structure for table atariqi.calling-key
CREATE TABLE IF NOT EXISTS `calling-key` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `call-key` int(11) NOT NULL,
  `country` varchar(50) NOT NULL,
  `date-of-add` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date-of-edit` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table atariqi.calling-key: ~0 rows (approximately)
/*!40000 ALTER TABLE `calling-key` DISABLE KEYS */;
INSERT INTO `calling-key` (`id`, `call-key`, `country`, `date-of-add`, `date-of-edit`) VALUES
	(1, 966, 'Saudi Arabia', '2023-08-16 16:35:36', NULL),
	(2, 20, 'Egypt', '2023-08-16 16:35:36', NULL);
/*!40000 ALTER TABLE `calling-key` ENABLE KEYS */;

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

-- Dumping structure for table atariqi.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table atariqi.migrations: ~13 rows (approximately)
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
	(13, '2023_08_28_222343_add_fcm_token_to_users_table', 3);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;

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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table atariqi.personal_access_tokens: ~0 rows (approximately)
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `created_at`, `updated_at`) VALUES
	(1, 'App\\Models\\User', 3, 'atariqi', '8c0ce2bbadef1a5b6365fb88b1dbbfc17818708c46ad8a1a39d9d568ad8d2abc', '["*"]', NULL, '2023-08-28 22:29:48', '2023-08-28 22:29:48');
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;

-- Dumping structure for table atariqi.services
CREATE TABLE IF NOT EXISTS `services` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `service` varchar(100) NOT NULL,
  `cost` bigint(20) NOT NULL,
  `date-of-add` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date-of-edit` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table atariqi.services: ~5 rows (approximately)
/*!40000 ALTER TABLE `services` DISABLE KEYS */;
INSERT INTO `services` (`id`, `service`, `cost`, `date-of-add`, `date-of-edit`) VALUES
	(1, 'immediately drive | توصيل لحظي', 20, '2023-08-16 16:51:58', NULL),
	(2, 'daily drive (go or back) | توصيل يومي (ذهاب أو عودة)', 18, '2023-08-16 16:51:58', NULL),
	(3, 'daily drive (go and back) | توصيل يومي (ذهاب و عودة)', 30, '2023-08-16 16:51:58', NULL),
	(4, 'weekly drive (go or back) | توصيل أسبوعي (ذهاب أو عودة)', 150, '2023-08-16 16:51:58', NULL),
	(5, 'weekly drive (go and back) | توصيل أسبوعي (ذهاب و عودة)', 300, '2023-08-16 16:51:58', NULL);
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

-- Dumping structure for table atariqi.university
CREATE TABLE IF NOT EXISTS `university` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name-ar` char(255) NOT NULL,
  `name-eng` char(255) NOT NULL,
  `country` varchar(100) NOT NULL,
  `city` varchar(100) NOT NULL,
  `location` text NOT NULL,
  `date-of-add` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date-of-edit` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table atariqi.university: ~0 rows (approximately)
/*!40000 ALTER TABLE `university` DISABLE KEYS */;
INSERT INTO `university` (`id`, `name-ar`, `name-eng`, `country`, `city`, `location`, `date-of-add`, `date-of-edit`) VALUES
	(1, 'جامعة الملك سعود', 'King Saud University', 'Saudi Arabia', 'riyadh', 'https://goo.gl/maps/XuXVx5GHLDzA3p9M9', '2023-08-16 16:34:53', NULL);
/*!40000 ALTER TABLE `university` ENABLE KEYS */;

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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table atariqi.users: ~4 rows (approximately)
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`id`, `user-first-name`, `user-last-name`, `call-key-id`, `phone-no`, `gender`, `university-id`, `user-stage-id`, `email`, `approval`, `user-type`, `date-of-add`, `date-of-edit`, `code`, `fcm_token`) VALUES
	(1, 'user1', 'family', 1, '504444444', 'girl', 1, 1, '123456@seu.edu.sa', 1, 'passenger', '2023-08-16 16:41:05', NULL, NULL, NULL),
	(4, 'Omar', 'Zizo', 1, '1007958182', 'male', 1, 1, 'omarzizo207@gmail.com', 1, 'passenger', '2023-08-29 18:43:00', NULL, '6034', NULL),
	(6, 'Omar', 'Zizo', 2, '1007958185', 'male', 1, 1, 'omarzizo207@gmail.com', 1, 'passenger', '2023-08-30 09:27:57', NULL, '6606', NULL),
	(7, 'Omar', 'Zizo', 2, '1007958188', 'male', 1, 1, 'omarzizo207@gmail.com', 1, 'passenger', '2023-08-30 18:11:19', NULL, '5218', NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;


-- Dumping structure for table atariqi.user-login
CREATE TABLE IF NOT EXISTS `user-login` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user-id` bigint(20) NOT NULL,
  `date-time` datetime DEFAULT NULL,
  `login-logout` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user-id` (`user-id`),
  CONSTRAINT `user-login_ibfk_1` FOREIGN KEY (`user-id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping data for table atariqi.user-login: ~0 rows (approximately)
/*!40000 ALTER TABLE `user-login` DISABLE KEYS */;
/*!40000 ALTER TABLE `user-login` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
