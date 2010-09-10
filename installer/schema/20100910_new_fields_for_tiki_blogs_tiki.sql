ALTER TABLE tiki_blogs ADD COLUMN `use_title` char(1) default 'y' AFTER `use_find`;
ALTER TABLE tiki_blogs ADD COLUMN `use_title_in_post` char(1) default 'y' AFTER `use_title`;
ALTER TABLE tiki_blogs ADD COLUMN `use_description` char(1) default 'y' AFTER `use_title_in_post`;
ALTER TABLE tiki_blogs ADD COLUMN `use_breadcrumbs` char(1) default 'n' AFTER `use_description`;
