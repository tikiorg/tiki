ALTER TABLE tiki_pages ADD status VARCHAR(60) default '' AFTER keywords;
ALTER TABLE tiki_history ADD status VARCHAR(60) default '' AFTER is_html;