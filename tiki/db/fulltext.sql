# $Id: fulltext.sql,v 1.5 2006-09-05 19:36:09 rlpowell Exp $

ALTER TABLE tiki_pages MODIFY data text;
CREATE FULLTEXT INDEX ft ON tiki_pages (pageName,data);
CREATE FULLTEXT INDEX ft ON tiki_galleries (name,description);
CREATE FULLTEXT INDEX ft ON tiki_faqs (title,description);
CREATE FULLTEXT INDEX ft ON tiki_faq_questions (question,answer);
CREATE FULLTEXT INDEX ft ON tiki_images (name,description);
CREATE FULLTEXT INDEX ft ON tiki_comments (title,data);
CREATE FULLTEXT INDEX ft ON tiki_files (name,description);
CREATE FULLTEXT INDEX ft ON tiki_blogs (title,description);
ALTER TABLE tiki_articles MODIFY body text;
CREATE FULLTEXT INDEX ft ON tiki_articles (title,heading,body);
CREATE FULLTEXT INDEX ft ON tiki_blog_posts (data,title);
CREATE FULLTEXT INDEX ft ON tiki_directory_sites (name,description);
CREATE FULLTEXT INDEX ft ON tiki_tracker_item_fields (value);
