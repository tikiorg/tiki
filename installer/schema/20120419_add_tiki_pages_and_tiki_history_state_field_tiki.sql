ALTER TABLE tiki_pages ADD status VARCHAR(60) AFTER keywords;
ALTER TABLE tiki_history ADD status VARCHAR(60) AFTER is_html;