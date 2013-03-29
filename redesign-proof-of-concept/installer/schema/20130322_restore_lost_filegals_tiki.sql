UPDATE `tiki_file_galleries`
SET `parentId` = 1
WHERE `parentId` = -1 AND `type` = 'default';
