-- 2009-11-26 mozilla upstream, multilingual dynamic variables
ALTER TABLE `tiki_dynamic_variables` ADD COLUMN `lang` VARCHAR(16) NULL;
ALTER TABLE `tiki_dynamic_variables` DROP PRIMARY KEY;

