ALTER TABLE `tiki_acct_item` ADD `itemBookId` INT UNSIGNED NOT NULL FIRST;
ALTER TABLE `tiki_acct_item` DROP PRIMARY KEY;
ALTER TABLE `tiki_acct_item` ADD PRIMARY KEY ( `itemBookId` , `itemJournalId` , `itemAccountId` , `itemType` );

ALTER TABLE `tiki_acct_stackitem` ADD `stackBookId` INT UNSIGNED NOT NULL FIRST;
ALTER TABLE `tiki_acct_stackitem` DROP PRIMARY KEY;
ALTER TABLE `tiki_acct_stackitem` ADD PRIMARY KEY ( `stackBookId` , `stackItemStackId` , `stackItemAccountId` , `stackItemType` );
