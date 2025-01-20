CREATE TABLE IF NOT EXISTS `patching_status` (
  `id` int NOT NULL AUTO_INCREMENT,
  `hostname` varchar(255) NOT NULL,
  `changed` tinyint(1) NOT NULL,
  `failed` tinyint(1) NOT NULL,
  `failed_update_count` int NOT NULL,
  `found_update_count` int NOT NULL,
  `installed_update_count` int NOT NULL,
  `reboot_required` tinyint(1) NOT NULL,
  `rebooted` tinyint(1) NOT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `project_id` int NOT NULL,
  `task_id` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1
