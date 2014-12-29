CREATE TABLE IF NOT EXISTS `tiki_addon_profiles` (
  `addon` VARCHAR(100),
  `version` VARCHAR(100),
  `profile` VARCHAR(100),
  `install_date` TIMESTAMP,
  PRIMARY KEY (`addon`,`version`,`profile`)
) ENGINE=MyISAM;