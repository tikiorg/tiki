#sylvieg 2008-10-24
ALTER TABLE `tiki_files` DROP KEY ft;
ALTER TABLE `tiki_files` ADD FULLTEXT KEY ft (name,description,search_data,filename);