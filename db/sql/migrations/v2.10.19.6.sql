CREATE TABLE IF NOT EXISTS `windows_update_history` (
  `id` int NOT NULL AUTO_INCREMENT,
  `hostname` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `date` datetime DEFAULT NULL,
  `operation` varchar(50) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `kb` varchar(255) DEFAULT NULL,
  `pc` varchar(255) DEFAULT NULL,
  `project_id` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1