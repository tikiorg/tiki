ALTER TABLE `tiki_acct_journal` DROP PRIMARY KEY , ADD PRIMARY KEY ( `journalId` );
ALTER TABLE `tiki_acct_stack` DROP PRIMARY KEY , ADD PRIMARY KEY ( `stackId` );
ALTER TABLE `tiki_acct_statement` DROP PRIMARY KEY , ADD PRIMARY KEY ( `statementId` );
ALTER TABLE `tiki_acct_tax` DROP PRIMARY KEY , ADD PRIMARY KEY ( `taxId` );
    