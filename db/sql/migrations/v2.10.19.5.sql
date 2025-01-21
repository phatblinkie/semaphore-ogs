CREATE TABLE IF NOT EXISTS `system_status` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(48) NOT NULL COMMENT 'ip4 or ip6 address',
  `hostname` varchar(255) NOT NULL,
  `ansible_ping` varchar(255) NOT NULL,
  `disk_capacity` varchar(255) NOT NULL,
  `proc_usage` text NOT NULL,
  `app_check` varchar(255) NOT NULL,
  `last_updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `uptime` int DEFAULT NULL,
  `last_responded` datetime DEFAULT NULL COMMENT 'updated only when host is alive',
  `project_id` int DEFAULT NULL,
  `task_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_ip_project` (`ip_address`,`project_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1
