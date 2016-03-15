# After fixes r57755 and r57752, storage of blog posts has changed when wysiwyg_htmltowiki is set
# For smooth upgrade, we change contents here
SET @convert='n';
SELECT (@convert:=value) FROM tiki_preferences WHERE `name`='wysiwyg_htmltowiki';
UPDATE `tiki_blog_posts` SET `data` = REPLACE(`data`, '&gt;', '>') WHERE @convert='y';
UPDATE `tiki_blog_posts` SET `data` = REPLACE(`data`, '&lt;', '<') WHERE @convert='y';
UPDATE `tiki_blog_posts` SET `data` = REPLACE(`data`, '&amp;', '&') WHERE @convert='y';
