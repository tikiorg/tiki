ALTER TABLE tiki_history ADD status VARCHAR(60) default '' AFTER is_html;
ALTER TABLE tiki_user_modules ADD status VARCHAR(60) default '' AFTER parse;
ALTER TABLE tiki_pages ADD status VARCHAR(60) default '' AFTER keywords;
