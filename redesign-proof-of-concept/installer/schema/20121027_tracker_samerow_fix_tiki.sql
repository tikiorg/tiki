UPDATE `tiki_tracker_fields`
	SET `options`= CONCAT('1', RIGHT(`options`, CHAR_LENGTH(`options`) - 1))
WHERE
	`type` IN ('t','n','b') AND
	NOT ISNULL(`options`) AND
	LEFT(`options`, 1) = '0';
