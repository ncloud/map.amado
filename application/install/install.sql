CREATE TABLE IF NOT EXISTS `sessions` (
  `session_id` varchar(40) NOT NULL DEFAULT '0',
  `ip_address` varchar(45) NOT NULL DEFAULT '0',
  `user_agent` varchar(120) NOT NULL,
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `user_data` text NOT NULL,
  PRIMARY KEY (`session_id`),
  KEY `last_activity_idx` (`last_activity`),
  KEY `session_id` (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS CREATE TABLE `sites` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned DEFAULT NULL,
  `permalink` varchar(128) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) unsigned DEFAULT NULL,
  `approved` enum('yes','no') NOT NULL DEFAULT 'no',
  `image` varchar(255) DEFAULT NULL,
  `title` varchar(100) NOT NULL,
  `lat` double NOT NULL,
  `lng` double NOT NULL,
  `description` varchar(255) NOT NULL,
  `owner_name` varchar(100) NOT NULL,
  `owner_email` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `site_id` (`site_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `places` (
  `id` int(11) NOT NULL AUTO_INCREMENT,  
  `site_id` int(11) unsigned DEFAULT NULL,
  `type_id` int(11) unsigned DEFAULT NULLL,
  `user_id` int(11) unsigned DEFAULT NULL,
  `approved` enum('yes','no') NOT NULL DEFAULT 'no',
  `title` varchar(100) NOT NULL,
  `lat` double NOT NULL,
  `lng` double NOT NULL,
  `address` varchar(200) NOT NULL,
  `address_is_position` enum('yes','no') NOT NULL DEFAULT 'no',
  `uri` varchar(200) NOT NULL,
  `description` varchar(255) NOT NULL,
  `sector` varchar(50) NOT NULL,
  `owner_name` varchar(100) NOT NULL,
  `owner_email` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `site_id` (`site_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `place_types` (
  `id` tinyint(4) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) unsigned DEFAULT NULL,
  `icon_id` int(11) unsigned DEFAULT NULL,
  `name` varchar(32) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `site_id` (`site_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `place_types` (`id`, `site_id`, `icon_id`, `name`) VALUES 
	  ('1', '1', '1', '역사/유적'),
	  ('2', '1', '2,, '미술/전시'),
	  ('3', '1', '3', '문화'), 
	  ('4', '1', '4', '식당/맛집'), 
	  ('5', '1', '5', '카페'), 
	  ('6', '1', '6', '스팟');
	  
CREATE TABLE IF NOT EXISTS `courses` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `site_id` int(11) unsigned DEFAULT NULL,
  `user_id` int(11) unsigned DEFAULT NULL,
  `permalink` varchar(128) DEFAULT NULL,
  `name` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`),  
  UNIQUE KEY `permalink` (`permalink`),
  KEY `site_id` (`site_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `courses` (`site_id`, `permalink`, `name`) VALUES 
	  ('1', 'basic', '기본');
	  
CREATE TABLE IF NOT EXISTS `user_tokens` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `vendor_id` int(11) NOT NULL DEFAULT '0',
  `token` varchar(128) NOT NULL,
  `secret` varchar(128) DEFAULT NULL,
  `user_agent` varchar(200) DEFAULT NULL,
  `expires` int(11) NOT NULL,
  `can_use` enum('no','yes') DEFAULT 'yes',
  `created` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`vendor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `role_users` (
  `site_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `role_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`site_id`,`user_id`,`role_id`),
  KEY `fk_role_id` (`site_id`, `role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `description` varchar(255)  DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `roles` (`id`, `name`) VALUES 
	  ('1', 'login'), 
	  ('2', 'guest'), 
	  ('3', 'admin'), 
	  ('4', 'super-admin');

CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `vendor_id` int(11) DEFAULT NULL,
  `vendor_user_id` varchar(32) DEFAULT NULL,
  `profile` varchar(200) DEFAULT NULL,
  `username` varchar(100) NOT NULL DEFAULT '',
  `password` varchar(100) NOT NULL DEFAULT '',
  `random_password` varchar(100) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `display_name` varchar(100) NOT NULL DEFAULT '',
  `email` varchar(200) NOT NULL DEFAULT '',
  `is_verified` enum('yes','no') DEFAULT 'no',
  `activation_key` varchar(64) DEFAULT NULL,
  `login_count` int(11) DEFAULT '0',
  `last_login_time` datetime NOT NULL,
  `last_ip_address` varchar(100) NOT NULL DEFAULT '',
  `last_user_agent` varchar(200) NOT NULL DEFAULT '',
  `create_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modify_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `vendor_id` (`vendor_id`,`vendor_user_id`) USING BTREE,
  KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `users` (`id`, `username`, `password`, `name`, `display_name`, `email`, `activation_key`) VALUES 
	  ('1', 'email_admin@example.com', 'eab9w5ad7083f70b822653b5ba5cae5bcaadab5438a', '관리자', '관리자', 'admin@example.com', 'ibloepduwdldkqzcfy7pyezfenvxkmvw');

INSERT INTO `sites` (`id`, `user_id`, `permalink`, `name`) VALUES 
	  ('1', '1', 'basic', '기본');
	  
INSERT INTO `role_users` (`site_id`, `user_id`, `role_id`) VALUES 
	  ('1', '1', '4');
