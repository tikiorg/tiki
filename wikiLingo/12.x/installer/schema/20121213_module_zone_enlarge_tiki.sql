ALTER TABLE `tiki_modules` MODIFY COLUMN `position` VARCHAR(20);
ALTER TABLE `tiki_user_assigned_modules` MODIFY COLUMN `position` VARCHAR(20);

UPDATE `tiki_modules` SET `position` = 'top' WHERE `position` = 't';
UPDATE `tiki_modules` SET `position` = 'topbar' WHERE `position` = 'o';
UPDATE `tiki_modules` SET `position` = 'pagetop' WHERE `position` = 'p';
UPDATE `tiki_modules` SET `position` = 'left' WHERE `position` = 'l';
UPDATE `tiki_modules` SET `position` = 'right' WHERE `position` = 'r';
UPDATE `tiki_modules` SET `position` = 'pagebottom' WHERE `position` = 'q';
UPDATE `tiki_modules` SET `position` = 'bottom' WHERE `position` = 'b';

ALTER TABLE `tiki_modules` ADD INDEX `namePosOrdParam` (`name`(100), `position`, `ord`, `params`(120));

UPDATE `tiki_user_assigned_modules` SET `position` = 'top' WHERE `position` = 't';
UPDATE `tiki_user_assigned_modules` SET `position` = 'topbar' WHERE `position` = 'o';
UPDATE `tiki_user_assigned_modules` SET `position` = 'pagetop' WHERE `position` = 'p';
UPDATE `tiki_user_assigned_modules` SET `position` = 'left' WHERE `position` = 'l';
UPDATE `tiki_user_assigned_modules` SET `position` = 'right' WHERE `position` = 'r';
UPDATE `tiki_user_assigned_modules` SET `position` = 'pagebottom' WHERE `position` = 'q';
UPDATE `tiki_user_assigned_modules` SET `position` = 'bottom' WHERE `position` = 'b';
