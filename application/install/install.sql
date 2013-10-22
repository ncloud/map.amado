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

CREATE TABLE IF NOT EXISTS `maps` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,    
  `description` text,
  `permalink` varchar(128) DEFAULT NULL,
  `privacy` enum('public','private') DEFAULT 'public',  
  `preview_map_url` varchar(255) DEFULT NULL,
  `add_role` enum('guest','member','workman','admin') DEFAULT 'member',
  `default_menu` enum('course','type') DEFAULT 'course',
  `is_viewed_home` enum('yes','no') DEFAULT 'yes',
  `create_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
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
  `map_id` int(11) unsigned DEFAULT NULL,
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
  KEY `map_id` (`map_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `place_types` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `map_id` int(11) unsigned DEFAULT NULL,
  `icon_id` int(11) unsigned DEFAULT NULL,
  `name` varchar(32) NOT NULL,
  `order_index` tinyint(4) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `map_id` (`map_id`),
  UNIQUE KEY `map_id_2` (`map_id`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `place_types` (`id`, `map_id`, `icon_id`, `name`, `order_index`) VALUES 
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
  `map_id` int(11) unsigned DEFAULT NULL,
  `user_id` int(11) unsigned DEFAULT NULL,
  `status` enum('approved','rejected','pending') DEFAULT 'pending',
  `permalink` varchar(128) DEFAULT NULL,
  `title` varchar(128) DEFAULT NULL,  
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`),  
  KEY `map_id` (`map_id`)
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
  `map_id` int(11) unsigned DEFAULT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `role_id` int(11) unsigned NOT NULL,
  `invite_email` varchar(128) NOT NULL DEFAULT '',  
  `invite_code` varchar(32) DEFAULT NULL,
  `invite_status` enum('invited','send_email','no') DEFAULT 'no',  
  `insert_time` datetime DEFAULT '0000-00-00 00:00:00',
  UNIQUE KEY (`map_id`,`role_id`,`user_id`,`invite_email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `level` int(11) unsigned DEFAULT NULL,
  `name` varchar(32) NOT NULL,
  `description` varchar(255)  DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `roles` (`id`, `name`, `level`, `description`) VALUES 
	  ('1', 'member', '1', '(비공개 지도일때) 장소 보기와 인증 후 장소 추가 가능'), 
    ('2', 'workman', '2', '인증 없이 장소 추가 가능'), 
    ('3', 'admin', '3', '관리자'), 
	  ('4', 'super-admin', '4', '전체 관리자');

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

INSERT INTO `maps` (`id`, `user_id`, `permalink`, `name`, `create_time`, `update_time`) VALUES 
	  ('1', '1', 'basic', '기본', NOW(), NOW());

/* INSERT INTO `courses` (`map_id`, `user_id`, `title`) VALUES 
    ('1', '1', '기본'); */

INSERT INTO `role_users` (`map_id`, `user_id`, `role_id`) VALUES 
	  (null, '1', '3');
	  	  