ALTER TABLE `tiki_modules` DROP PRIMARY KEY, ADD INDEX  `namePosOrdParam` (  `name` ( 100 ) ,  `position` ,  `ord` ,  `params` ( 140 ) );
ALTER TABLE `tiki_modules` DROP INDEX  `moduleId`, ADD PRIMARY KEY (  `moduleId` );
ALTER TABLE `tiki_modules` CHANGE `params` `params` TEXT;
