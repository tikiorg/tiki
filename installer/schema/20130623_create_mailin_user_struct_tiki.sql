CREATE TABLE IF NOT EXISTS `tiki_user_mailin_struct` (
	`mailin_struct_id` int(12) NOT NULL auto_increment,
	`username` varchar(200) NOT NULL,
	`subj_pattern` varchar(255) NULL,
	`body_pattern` varchar(255) NULL,
	`structure_id` int(14) NOT NULL,
	`page_id` int(14) NULL,
	`is_active` char(1) NULL DEFAULT 'n',
   PRIMARY KEY (`mailin_struct_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1;
