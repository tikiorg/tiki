ALTER TABLE tiki_search_queries 
	ADD COLUMN `description` TEXT NULL,
	ADD UNIQUE KEY `tiki_user_query_uq` (`userId`, `label`);
