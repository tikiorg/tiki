UPDATE `users_users`
	SET
		`users_users`.`created` = (SELECT UNIX_TIMESTAMP(MIN(`tiki_schema`.`install_date`)) FROM `tiki_schema`),
		`users_users`.`registrationDate` = `users_users`.`created`
	WHERE
		`users_users`.`userId`=1 AND
		`users_users`.`created` IS NULL;
