#gillesm 2008-10-27
ALTER TABLE tiki_file_galleries ADD `groupforAlert` varchar(255) default NULL;
ALTER TABLE tiki_file_galleries DROP `groupforAlert`;
