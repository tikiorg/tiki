CREATE TABLE IF NOT EXISTS `tiki_acct_account` (
  `accountBookId` int(10) unsigned NOT NULL,
  `accountId` int(10) unsigned NOT NULL DEFAULT '0',
  `accountName` varchar(255) NOT NULL,
  `accountNotes` text NOT NULL,
  `accountBudget` double NOT NULL DEFAULT '0',
  `accountLocked` int(1) NOT NULL DEFAULT '0',
  `accountTax` int(11) NOT NULL DEFAULT '0',
  `accountUserId` int(8) NOT NULL DEFAULT '0',
  PRIMARY KEY (`accountBookId`,`accountId`),
  KEY `accountTax` (`accountTax`)
);

CREATE TABLE IF NOT EXISTS `tiki_acct_bankaccount` (
  `bankBookId` int(10) unsigned NOT NULL,
  `bankAccountId` int(10) unsigned NOT NULL,
  `externalNumber` int(10) NOT NULL,
  `bankCountry` varchar(2) NOT NULL,
  `bankCode` varchar(11) NOT NULL,
  `bankIBAN` varchar(63) NOT NULL,
  `bankBIC` varchar(63) NOT NULL,
  `bankDelimeter` varchar(15) NOT NULL DEFAULT ';',
  `bankDecPoint` varchar(1) NOT NULL DEFAULT ',',
  `bankThousand` varchar(1) NOT NULL DEFAULT '.',
  `bankHasHeader` tinyint(1) NOT NULL DEFAULT '1',
  `fieldNameAccount` varchar(63) NOT NULL,
  `fieldNameBookingDate` varchar(63) NOT NULL,
  `formatBookingDate` varchar(31) NOT NULL,
  `fieldNameValueDate` varchar(63) NOT NULL,
  `formatValueDate` varchar(31) NOT NULL,
  `fieldNameBookingText` varchar(63) NOT NULL,
  `fieldNameReason` varchar(63) NOT NULL,
  `fieldNameCounterpartName` varchar(63) NOT NULL,
  `fieldNameCounterpartAccount` varchar(63) NOT NULL,
  `fieldNameCounterpartBankCode` varchar(63) NOT NULL,
  `fieldNameAmount` varchar(63) NOT NULL,
  `amountType` int(10) unsigned NOT NULL,
  `fieldNameAmountSign` varchar(63) NOT NULL,
  `SignPositive` varchar(7) NOT NULL,
  `SignNegative` varchar(7) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`bankBookId`,`bankAccountId`)
);

CREATE TABLE IF NOT EXISTS `tiki_acct_book` (
  `bookId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bookName` varchar(255) NOT NULL,
  `bookClosed` enum('y','n') NOT NULL DEFAULT 'n',
  `bookStartDate` date NULL,
  `bookEndDate` date NULL,
  `bookCurrency` varchar(3) NOT NULL DEFAULT 'EUR',
  `bookCurrencyPos` int(11) NOT NULL,
  `bookDecimals` int(11) NOT NULL DEFAULT '2',
  `bookDecPoint` varchar(1) NOT NULL DEFAULT ',',
  `bookThousand` varchar(1) NOT NULL DEFAULT '.',
  `exportSeparator` varchar(4) NOT NULL DEFAULT ';',
  `exportEOL` varchar(4) NOT NULL DEFAULT 'LF',
  `exportQuote` varchar(4) NOT NULL DEFAULT '"',
  `bookAutoTax` enum('y','n') NOT NULL DEFAULT 'y',
  PRIMARY KEY (`bookId`)
);

CREATE TABLE IF NOT EXISTS `tiki_acct_item` (
  `itemJournalId` int(10) unsigned NOT NULL DEFAULT '0',
  `itemAccountId` int(10) unsigned NOT NULL DEFAULT '0',
  `itemType` int(1) NOT NULL DEFAULT '-1',
  `itemAmount` double NOT NULL DEFAULT '0',
  `itemText` varchar(255) NOT NULL DEFAULT '',
  `itemTs` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`itemJournalId`,`itemAccountId`,`itemType`)
);

CREATE TABLE IF NOT EXISTS `tiki_acct_stackitem` (
  `stackItemStackId` int(10) unsigned NOT NULL DEFAULT '0',
  `stackItemAccountId` int(10) unsigned NOT NULL DEFAULT '0',
  `stackItemType` int(1) NOT NULL DEFAULT '-1',
  `stackItemAmount` double NOT NULL DEFAULT '0',
  `stackItemText` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`stackItemStackId`,`stackItemAccountId`,`stackItemType`)
);

INSERT IGNORE INTO `users_permissions` (`permName` , `permDesc` , `level` , `type` , `admin` , `feature_check` ) VALUES 
( 'tiki_p_acct_create_book', 'Can create/close a book', 'admin', 'accounting', 'y', 'feature_accounting'),
( 'tiki_p_acct_manage_accounts', 'Can create/edit/lock accounts', 'admin', 'accounting', 'y', 'feature_accounting' ),
( 'tiki_p_acct_book', 'Create a new transaction', 'editor', 'accounting', 'n', 'feature_accounting'),
( 'tiki_p_acct_view', 'Permission to view the journal', 'registered', 'accounting', 'n', 'feature_accounting' ),
( 'tiki_p_acct_book_stack', 'Can book into the stack, where statements can be changed', 'editor', 'accounting', 'n', 'feature_accounting'),
( 'tiki_p_acct_book_import', 'Can import statements from external accounts', 'editor', 'accounting', 'n', 'feature_accounting' ),
( 'tiki_p_acct_manage_template', 'Can manage templates for recurring transactions', 'editor', 'accounting', 'n', 'feature_accounting');
