DELETE `tiki_object_relations`
FROM `tiki_object_relations`
	LEFT JOIN `tiki_sheets` `ps` on `ps`.`sheetId` = `tiki_object_relations`.`source_itemId`
	LEFT JOIN `tiki_sheets` `cs` on `cs`.`sheetId` = `tiki_object_relations`.`target_itemId`
WHERE
	(
		ISNULL(`ps`.`sheetId`)
		OR ISNULL(`cs`.`sheetId`)
	)
	AND `tiki_object_relations`.`source_type` = 'sheetId'
