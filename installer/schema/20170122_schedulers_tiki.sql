CREATE TABLE IF NOT EXISTS `tiki_scheduler` (
  `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `name` VARCHAR(255),
  `description` VARCHAR(255),
  `task`VARCHAR(255),
  `params` VARCHAR(255),
  `run_time` VARCHAR(255),
  `status` VARCHAR(10),
  `re_run` TINYINT
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `tiki_scheduler_run` (
  `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `scheduler_id` INT NOT NULL,
  `start_time` INT(14),
  `end_time` INT(14),
  `status` VARCHAR(10),
  `output` TEXT
) ENGINE=MyISAM;
