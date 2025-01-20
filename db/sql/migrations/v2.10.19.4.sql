CREATE TABLE IF NOT EXISTS `patching_updates` (
  `id` int NOT NULL AUTO_INCREMENT,
  `patching_status_id` int NOT NULL,
  `update_id` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `categories` text NOT NULL,
  `downloaded` tinyint(1) NOT NULL,
  `installed` tinyint(1) NOT NULL,
  `kb` text,
  `update_type` varchar(10) NOT NULL,
  `project_id` int NOT NULL,
  `task_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `patching_status_id` (`patching_status_id`),
  CONSTRAINT `patching_updates_ibfk_1` FOREIGN KEY (`patching_status_id`) REFERENCES `patching_status` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1
