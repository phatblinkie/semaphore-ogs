CREATE TABLE IF NOT EXISTS `linux_pending_updates` (
  `id` int NOT NULL AUTO_INCREMENT,
  `hostname` varchar(255) NOT NULL,
  `update_name` varchar(255) NOT NULL,
  `project_id` int NOT NULL,
  `task_id` int NOT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `version` varchar(255) NOT NULL,
  `repo` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_pending_update` (`hostname`,`project_id`,`update_name`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1
