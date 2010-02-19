CREATE TABLE `tiki_user_login_cookies` (
	`userId` INT NOT NULL,
	`secret` TEXT NOT NULL,
	`expiration`  TIMESTAMP NOT NULL,
	PRIMARY KEY (`userId`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;
