CREATE TABLE `tiki_user_login_cookies` (
	`userId` INT NOT NULL,
	`secret` CHAR(64) NOT NULL,
	`expiration` TIMESTAMP NOT NULL,
	PRIMARY KEY (`userId`, `secret`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;
