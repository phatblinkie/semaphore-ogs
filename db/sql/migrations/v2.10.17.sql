CREATE TABLE `task_extra_data` (
  `id` int NOT NULL,
  `task_id` int NOT NULL,
  `data` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `facts_json` json DEFAULT NULL COMMENT 'for full fact data set'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='for storing extra information manually in plays';

--
-- Indexes for dumped tables
--

--
-- Indexes for table `task_extra_data`
--
ALTER TABLE `task_extra_data`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `task_extra_data`
--
ALTER TABLE `task_extra_data`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;
COMMIT;
