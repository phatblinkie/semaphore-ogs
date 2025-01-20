CREATE TABLE IF NOT EXISTS `task_extra_data` (
  `id` int NOT NULL AUTO_INCREMENT,
  `task_id` int NOT NULL,
  `data` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `facts_json` json DEFAULT NULL COMMENT 'for full fact data set',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='for storing extra information manually in plays'