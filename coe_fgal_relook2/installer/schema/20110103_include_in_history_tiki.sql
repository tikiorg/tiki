INSERT IGNORE INTO `tiki_history`(`pageName`, `version`, `version_minor`, `lastModif`, `user`, `ip`, `comment`, `data`, `description`,`is_html`)
	SELECT `pageName`, `version`, `version_minor`, `lastModif`, `user`, `ip`, `comment`, `data`, `description`,`is_html`
	FROM tiki_pages;
