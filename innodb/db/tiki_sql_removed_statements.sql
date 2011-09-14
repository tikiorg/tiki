/*
Incorrect table definition; there can be only one auto column and it must be defined as a key

DROP TABLE IF EXISTS `tiki_user_bookmarks_folders`;
CREATE TABLE `tiki_user_bookmarks_folders` (
  `folderId` int(12) NOT NULL auto_increment,
  `parentId` int(12) default NULL,
  `user` varchar(200) NOT NULL default '',
  `name` varchar(30) default NULL,
  PRIMARY KEY (`user`,`folderId`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;
*/

/* 
MyISAM FULLTEXT indexing 
CREATE FULLTEXT INDEX ft_articles ON tiki_articles(`title`, `heading`, `body`);
CREATE FULLTEXT INDEX ft_blog_posts ON tiki_blog_posts(`data`, `title`);
CREATE FULLTEXT INDEX ft_blogs ON tiki_blogs(`title`, `description`);
CREATE FULLTEXT INDEX ft_calendar_items ON tiki_calendar_items(`name`,`description`);
CREATE FULLTEXT INDEX ft_comments ON tiki_comments(title,data);
CREATE FULLTEXT INDEX ftidx_directory_sites ON tiki_directory_sites(name,description);
CREATE FULLTEXT INDEX ftidx_faq_questions ON tiki_faq_questions(question,answer);
CREATE FULLTEXT INDEX ftidx_faqs ON tiki_faqs(title,description);
CREATE FULLTEXT INDEX ftidx_files ON tiki_files(name,description,search_data,filename);
CREATE FULLTEXT INDEX ftidx_galleries ON tiki_galleries(name,description);
CREATE FULLTEXT INDEX ftidx_images ON tiki_images(name,description);
CREATE FULLTEXT INDEX ftidx_pages ON tiki_pages(`pageName`,`description`,`data`);
CREATE FULLTEXT INDEX ftidx_tracker_item_fields ON tiki_tracker_item_fields(value);
*/
