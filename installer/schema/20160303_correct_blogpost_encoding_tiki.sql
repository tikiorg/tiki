UPDATE `tiki_blog_posts` SET `data` = REPLACE(`data`, '&gt;', '>');
UPDATE `tiki_blog_posts` SET `data` = REPLACE(`data`, '&lt;', '<');
UPDATE `tiki_blog_posts` SET `data` = REPLACE(`data`, '&amp;', '&');
