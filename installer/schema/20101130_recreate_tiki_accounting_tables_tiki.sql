CREATE TABLE IF NOT EXISTS `tiki_acct_journal` (
  `journalBookId` int(10) unsigned NOT NULL,
  `journalId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `journalDate` date NOT NULL DEFAULT '0000-00-00',
  `journalDescription` varchar(255) NOT NULL,
  `journalCancelled` int(1) NOT NULL DEFAULT '0',
  `journalTs` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`journalId`)
);

CREATE TABLE IF NOT EXISTS `tiki_acct_stack` (
  `stackBookId` int(10) unsigned NOT NULL,
  `stackId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `stackDate` date NOT NULL DEFAULT '0000-00-00',
  `stackDescription` varchar(255) NOT NULL,
  `stackTs` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`stackId`)
);

CREATE TABLE IF NOT EXISTS `tiki_acct_statement` (
  `statementBookId` int(10) unsigned NOT NULL,
  `statementAccountId` int(10) unsigned NOT NULL DEFAULT '0',
  `statementId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `statementBookingDate` date NOT NULL,
  `statementValueDate` date NOT NULL,
  `statementBookingText` varchar(255) NOT NULL,
  `statementReason` varchar(255) NOT NULL,
  `statementCounterpart` varchar(63) NOT NULL,
  `statementCounterpartAccount` varchar(63) NOT NULL,
  `statementCounterpartBankCode` varchar(63) NOT NULL,
  `statementAmount` double NOT NULL,
  `statementJournalId` int(10) unsigned NOT NULL DEFAULT '0',
  `statementStackId` int(11) NOT NULL,
  PRIMARY KEY (`statementId`)
);

CREATE TABLE IF NOT EXISTS `tiki_acct_tax` (
  `taxBookId` int(10) unsigned NOT NULL,
  `taxId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `taxText` varchar(63) NOT NULL,
  `taxAmount` double NOT NULL DEFAULT '0',
  `taxIsFix` enum('y','n') NOT NULL DEFAULT 'n',
  PRIMARY KEY (`taxId`)
);
