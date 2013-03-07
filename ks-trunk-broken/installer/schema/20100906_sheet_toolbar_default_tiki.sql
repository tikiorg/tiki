DELETE FROM `tiki_preferences`
WHERE `name` = 'toolbar_sheet';

INSERT IGNORE INTO `tiki_preferences` (`name` ,`value`)
VALUES ('toolbar_sheet', 'addrow,addrowbefore,addrowmulti,deleterow,-,addcolumn,addcolumnbefore,addcolumnmulti,deletecolumn,-,sheetgetrange,-,sheetsave,sheetrefresh,sheetfind,-,bold,italic,strike,center,-,color,bgcolor,-,tikilink,nonparsed|sheetclose/');