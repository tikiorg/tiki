
ALTER TABLE tiki_programmed_content ADD COLUMN content_type VARCHAR( 20 ) NOT NULL DEFAULT 'static' AFTER contentId;
