CREATE TABLE IF NOT EXISTS `linux_installed_updates` (
  `id` int NOT NULL AUTO_INCREMENT,
  `hostname` varchar(255) NOT NULL,
  `update_name` varchar(255) NOT NULL,
  `version` varchar(255) NOT NULL,
  `repo` varchar(255) NOT NULL,
  `project_id` int NOT NULL,
  `task_id` int NOT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_installed_update` (`hostname`,`project_id`,`update_name`,`version`,`repo`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1
