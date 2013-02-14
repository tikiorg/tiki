CREATE TABLE `tiki_page_references` (
  `ref_id` INT(14) NOT NULL AUTO_INCREMENT,
  `page_id` INT(14) DEFAULT NULL,
  `biblio_code` VARCHAR(50) DEFAULT NULL,
  `author` VARCHAR(255) DEFAULT NULL,
  `title` VARCHAR(255) DEFAULT NULL,
  `part` VARCHAR(255) DEFAULT NULL,
  `uri` VARCHAR(255) DEFAULT NULL,
  `code` VARCHAR(255) DEFAULT NULL,
  `year` VARCHAR(255) DEFAULT NULL,
  `publisher` VARCHAR(255) DEFAULT NULL,
  `location` VARCHAR(255)  DEFAULT NULL,
  `style` VARCHAR(30) DEFAULT NULL,
  `template` varchar(255) DEFAULT NULL,
  `last_modified` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,  
  PRIMARY KEY (`ref_id`),
  KEY `PageId` (`page_id`)
) ENGINE=MyISAM;
ALTER TABLE tiki_page_references ADD UNIQUE INDEX uk1_tiki_page_ref_biblio_code (page_id, biblio_code);
ALTER TABLE tiki_page_references ADD INDEX idx_tiki_page_ref_title (title);
ALTER TABLE tiki_page_references ADD INDEX idx_tiki_page_ref_author (author);
