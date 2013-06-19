CREATE TABLE IF NOT EXISTS `images` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `approved` enum('yes','no') NOT NULL DEFAULT 'no',
  `image` varchar(255) DEFAULT NULL,
  `title` varchar(100) NOT NULL,
  `lat` double NOT NULL,
  `lng` double NOT NULL,
  `description` varchar(255) NOT NULL,
  `owner_name` varchar(100) NOT NULL,
  `owner_email` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `places` (
  `id` int(9) NOT NULL AUTO_INCREMENT,  
  `approved` enum('yes','no') NOT NULL DEFAULT 'no',
  `title` varchar(100) NOT NULL,
  `type` varchar(20) NOT NULL,
  `lat` double NOT NULL,
  `lng` double NOT NULL,
  `address` varchar(200) NOT NULL,
  `address_is_position` int(1) NOT NULL,
  `uri` varchar(200) NOT NULL,
  `description` varchar(255) NOT NULL,
  `sector` varchar(50) NOT NULL,
  `owner_name` varchar(100) NOT NULL,
  `owner_email` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `place_types` (
  `id` tinyint(4) NOT NULL AUTO_INCREMENT,
  `key` varchar(12) NOT NULL,
  `name` varchar(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `place_types` (`key`, `name`) VALUES 
	  ('heritage', '역사/유적'),
	  ('gallery','미술/전시'),
	  ('cultural', '문화'), 
	  ('restaurant', '식당/맛집'), 
	  ('cafe', '카페'), 
	  ('spot', '스팟');
	  
CREATE TABLE IF NOT EXISTS `courses` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `url` varchar(128) DEFAULT NULL,
  `name` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`),  
  UNIQUE KEY `url` (`url`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `courses` (`url`, `name`) VALUES 
	  ('basic', '기본');