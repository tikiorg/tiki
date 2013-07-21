-- Definitions of all fulltext indexes.
-- The file is installed, if the engine supports fulltext indexing
-- $Id$
CREATE FULLTEXT INDEX ft ON tiki_articles(`title`, `heading`, `body`);
CREATE FULLTEXT INDEX ft ON tiki_blog_posts(`data`, `title`);
CREATE FULLTEXT INDEX ft ON tiki_blogs(`title`, `description`);
CREATE FULLTEXT INDEX ft ON tiki_calendar_items(`name`,`description`);
CREATE FULLTEXT INDEX ft ON tiki_comments(`title`,`data`);
CREATE FULLTEXT INDEX ft ON tiki_directory_sites(`name`,`description`);
CREATE FULLTEXT INDEX ft ON tiki_faq_questions(`question`,`answer`);
CREATE FULLTEXT INDEX ft ON tiki_faqs(`title`,`description`);
CREATE FULLTEXT INDEX ft ON tiki_files(`name`,`description`,`search_data`,`filename`);
CREATE FULLTEXT INDEX ft ON tiki_galleries(`name`,`description`);
CREATE FULLTEXT INDEX ft ON tiki_images(`name`,`description`);
CREATE FULLTEXT INDEX ft ON tiki_pages(`pageName`,`description`,`data`);
CREATE FULLTEXT INDEX ft ON tiki_tracker_item_fields(`value`);