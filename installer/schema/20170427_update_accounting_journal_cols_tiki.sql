ALTER TABLE tiki_acct_item DROP primary key;
ALTER TABLE tiki_acct_item ADD itemId int NOT NULL AUTO_INCREMENT primary key FIRST;