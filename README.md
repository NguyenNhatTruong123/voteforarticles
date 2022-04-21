# voteforarticles
Extension rating articles for Joomla 4.

Download as zip or clone it then place it in the path /joomla/plugins/contents

NOTE: You must create a new table database in your xampp database. In this case, I named it star_rating

SQL for creating table:

				  CREATE DATABASE IF NOT EXISTS `joomla_db`
				  /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;
				  USE `joomla_db`;

				  -- Dumping structure for table joomla_db.j_content_rating

				  CREATE TABLE IF NOT EXISTS `star_rating` (

					`content_id` int(11) NOT NULL DEFAULT 0,

					`rating_sum` int(10) unsigned NOT NULL DEFAULT 0,

					`rating_count` int(10) unsigned NOT NULL DEFAULT 0,

					`ip_address` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',

					`id_rating` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',

					PRIMARY KEY (`id_rating`)

				  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

