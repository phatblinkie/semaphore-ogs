CREATE TABLE IF NOT EXISTS `linux_updates` (
  `id` int NOT NULL AUTO_INCREMENT,
  `hostname` varchar(255) NOT NULL,
  `pending_updates` int NOT NULL,
  `installed_updates` int NOT NULL,
  `project_id` int NOT NULL,
  `task_id` int NOT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_update` (`hostname`,`project_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1
