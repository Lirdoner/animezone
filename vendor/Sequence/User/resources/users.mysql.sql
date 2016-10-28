

CREATE TABLE IF NOT EXISTS `groups` (
  `name` varchar(255) NOT NULL,
  `roles` text NOT NULL,
  `real_name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `email` char(64) NOT NULL,
  `password` char(64) NOT NULL,
  `enabled` tinyint(4) unsigned NOT NULL DEFAULT '0',
  `role` varchar(30) NOT NULL DEFAULT 'ROLE_USER',
  `ip` varchar(40) NOT NULL,
  `last_login` datetime NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `name` (`name`),
  KEY `password` (`password`),
  KEY `enabled` (`enabled`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `users_custom_field` (
  `user_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `users_groups` (
  `group_name` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  KEY `user_id` (`user_id`),
  KEY `group_name` (`group_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `users_online` (
  `sess_id` char(64) NOT NULL,
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `user_ip` varchar(40) NOT NULL,
  `user_role` varchar(30) NOT NULL DEFAULT 'ROLE_GUEST',
  `user_agent` varchar(255) NOT NULL,
  `last_active` datetime NOT NULL,
  UNIQUE KEY `sess_id` (`sess_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;

-- --------------------------------------------------------