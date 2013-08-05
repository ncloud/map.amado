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

CREATE TABLE IF NOT EXISTS `sites` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned DEFAULT NULL,
  `permalink` varchar(128) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `attaches` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) unsigned DEFAULT NULL,
  `filename` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `places` (
  `id` int(11) NOT NULL AUTO_INCREMENT,  
  `site_id` int(11) unsigned DEFAULT NULL,
  `type_id` int(11) unsigned DEFAULT NULL,
  `user_id` int(11) unsigned DEFAULT NULL,
  `status` enum('pending','rejected','approved') NOT NULL DEFAULT 'pending',
  `title` varchar(100) NOT NULL,
  `lat` double NOT NULL,
  `lng` double NOT NULL,
  `address` varchar(200) NOT NULL,
  `address_is_position` enum('yes','no') NOT NULL DEFAULT 'no',
  `url` varchar(200) NOT NULL,
  `description` varchar(255) NOT NULL,
  `attached` enum('image','file','no') NOT NULL DEFAULT 'no',
  `owner_name` varchar(100) NOT NULL,
  `owner_email` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `site_id` (`site_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `place_types` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `site_id` int(11) unsigned DEFAULT NULL,
  `icon_id` int(11) unsigned DEFAULT NULL,
  `name` varchar(32) NOT NULL,
  `order_index` tinyint(4) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `site_id` (`site_id`),
  UNIQUE KEY `site_id` (`site_id`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `place_types` (`id`, `site_id`, `icon_id`, `name`, `order_index`) VALUES 
	  ('1', '1', '1', '역사/유적', '1'),
	  ('2', '1', '2', '미술/전시', '2'),
	  ('3', '1', '3', '문화', '3'), 
	  ('4', '1', '4', '식당/맛집', '4'), 
	  ('5', '1', '5', '카페', '5'), 
	  ('6', '1', '6', '스팟', '6');

CREATE TABLE IF NOT EXISTS `course_targets` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `course_id` int(11) unsigned DEFAULT NULL,
  `target_id` int(11) unsigned DEFAULT NULL,  
  `title` varchar(255) DEFAULT NULL,
  `order_index` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	  
CREATE TABLE IF NOT EXISTS `courses` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `site_id` int(11) unsigned DEFAULT NULL,
  `user_id` int(11) unsigned DEFAULT NULL,
  `status` enum('approved','rejected','pending') DEFAULT 'pending',
  `permalink` varchar(128) DEFAULT NULL,
  `title` varchar(128) DEFAULT NULL,  
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`),  
  KEY `site_id` (`site_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	  
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
  `site_id` int(11) unsigned DEFAULT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `role_id` int(11) unsigned NOT NULL,
  UNIQUE KEY `site_id` (`site_id`,`user_id`,`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `level` int(11) unsigned DEFAULT NULL,
  `name` varchar(32) NOT NULL,
  `description` varchar(255)  DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `roles` (`id`, `name`, `level`) VALUES 
	  ('1', 'member', '1'), 
	  ('2', 'admin', '2'), 
	  ('3', 'super-admin', '3');

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

/* INSERT INTO `courses` (`site_id`, `user_id`, `title`) VALUES 
    ('1', '1', '기본'); */

INSERT INTO `role_users` (`site_id`, `user_id`, `role_id`) VALUES 
	  (null, '1', '3');
	  	  