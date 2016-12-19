#sylvieg
CREATE TABLE `tiki_sefurl_regex_out` (
  `id` int(11) NOT NULL auto_increment,
  `left` varchar(256) NOT NULL,
  `right` varchar(256) NULL default NULL,
  `type` varchar(32) NULL default NULL,
  `silent` char(1) NULL default 'n',
  `feature` varchar(256) NULL default NULL,
  `comment` varchar(256),
  `order` int(11) NULL default 0,
  PRIMARY KEY(`id`),
  UNIQUE KEY `left`(`left`(256)),
  INDEX `idx1` (silent, type, feature(30))
);
INSERT INTO `tiki_sefurl_regex_out` (`left`, `right`, `type`, `feature`) VALUES('tiki-index.php\\?page=(.+)', '$1', 'wiki', 'feature_wiki');
INSERT INTO `tiki_sefurl_regex_out` (`left`, `right`, `type`, `feature`) VALUES('tiki-slideshow.php\\?page=(.+)', 'show:$1', '', 'feature_wiki');
INSERT INTO `tiki_sefurl_regex_out` (`left`, `right`, `type`, `feature`) VALUES('tiki-read_article.php\\?articleId=(\\d+)', 'article$1', 'article', 'feature_articles');
INSERT INTO `tiki_sefurl_regex_out` (`left`, `right`, `type`, `feature`) VALUES('tiki-browse_categories.php\\?parentId=(\\d+)', 'cat$1', 'category', 'feature_categories');
INSERT INTO `tiki_sefurl_regex_out` (`left`, `right`, `type`, `feature`) VALUES('tiki-view_blog.php\\?blogId=(\\d+)', 'blog$1', 'blog', 'feature_blogs');
INSERT INTO `tiki_sefurl_regex_out` (`left`, `right`, `type`, `feature`) VALUES('tiki-view_blog_post.php\\?postId=(\\d+)', 'blogpost$1', 'blogpost', 'feature_blogs');
INSERT INTO `tiki_sefurl_regex_out` (`left`, `right`, `type`, `feature`) VALUES('tiki-browse_image.php\\?imageId=(\\d+)', 'browseimage$1', 'image', 'feature_galleries');
INSERT INTO `tiki_sefurl_regex_out` (`left`, `right`, `type`, `feature`) VALUES('tiki-view_chart.php\\?chartId=(\\d+)', 'chart$1', 'chart', 'feature_charts');
INSERT INTO `tiki_sefurl_regex_out` (`left`, `right`, `type`, `feature`) VALUES('tiki-directory_browse.php\\?parent=(\\d+)', 'directory$1', 'directory', 'feature_directory');
INSERT INTO `tiki_sefurl_regex_out` (`left`, `right`, `type`, `feature`) VALUES('tiki-view_faq.php\\?faqId=(\\d+)', 'faq$1', 'faq', 'feature_faqs');
INSERT INTO `tiki_sefurl_regex_out` (`left`, `right`, `type`, `feature`) VALUES('tiki-list_file_gallery.php\\?galleryId=(\\d+)', 'file$1', 'file', 'feature_file_galleries');
INSERT INTO `tiki_sefurl_regex_out` (`left`, `right`, `type`, `feature`) VALUES('tiki-download_file.php\\?fileId=(\\d+)', 'dl$1', 'file', 'feature_file_galleries');
INSERT INTO `tiki_sefurl_regex_out` (`left`, `right`, `type`, `feature`) VALUES('tiki-view_forum.php\\?forumId=(\\d+)', 'forum$1', 'forum', 'feature_forums');
INSERT INTO `tiki_sefurl_regex_out` (`left`, `right`, `type`, `feature`) VALUES('tiki-browse_gallery.php\\?galleryId=(\\d+)', 'gallery$1', 'gallery', 'feature_galleries');
INSERT INTO `tiki_sefurl_regex_out` (`left`, `right`, `type`, `feature`) VALUES('show_image.php\\?id=(\\d+)', 'image$1', 'image', 'feature_galleries');
INSERT INTO `tiki_sefurl_regex_out` (`left`, `right`, `type`, `feature`) VALUES('show_image.php\\?id=(\\d+)&scalesize=(\\d+)', 'imagescale$1/$2', 'image', 'feature_galleries');
INSERT INTO `tiki_sefurl_regex_out` (`left`, `right`, `type`, `feature`) VALUES('tiki-newsletters.php\\?nlId=(\\d+)', 'newsletter$1', 'newsletter', 'feature_newsletters');
INSERT INTO `tiki_sefurl_regex_out` (`left`, `right`, `type`, `feature`) VALUES('tiki-take_quiz.php\\?quizId=(\\d+)', 'quiz$1', 'quiz', 'feature_quizzes');
INSERT INTO `tiki_sefurl_regex_out` (`left`, `right`, `type`, `feature`) VALUES('tiki-take_survey.php\\?surveyId=(\\d+)', 'survey$1', 'survey', 'feature_surveys');
INSERT INTO `tiki_sefurl_regex_out` (`left`, `right`, `type`, `feature`) VALUES('tiki-view_tracker.php\\?trackerId=(\\d+)', 'tracker$1', 'tracker', 'feature_trackers');
INSERT INTO `tiki_sefurl_regex_out` (`left`, `right`, `type`, `feature`) VALUES('tiki-integrator.php\\?repID=(\\d+)', 'int$1', '', 'feature_integrator');
INSERT INTO `tiki_sefurl_regex_out` (`left`, `right`, `type`, `feature`) VALUES('tiki-view_sheets.php\\?sheetId=(\\d+)', 'sheet$1', 'sheet', 'feature_sheet');
INSERT INTO `tiki_sefurl_regex_out` (`left`, `right`, `type`, `feature`) VALUES('tiki-directory_redirect.php\\?siteId=(\\d+)', 'dirlink$1', 'directory', 'feature_directory');
INSERT INTO `tiki_sefurl_regex_out` (`left`, `right`, `comment`, `type`, `feature`, `order`) VALUES('tiki-calendar.php\\?calIds\\[\\]=(\\d+)\&calIds\\[\\]=(\\d+)\&callIds\\[\\](\\d+)\&callIds\\[\\](\\d+)\&callIds\\[\\](\\d+)\&callIds\\[\\](\\d+)\&callIds\\[\\](\\d+)', 'cal$1,$2,$3,$4,$5,$6,$7', '7', 'calendar', 'feature_calendar', 100);
INSERT INTO `tiki_sefurl_regex_out` (`left`, `right`, `comment`, `type`, `feature`, `order`) VALUES('tiki-calendar.php\\?calIds\\[\\]=(\\d+)\&calIds\\[\\]=(\\d+)\&callIds\\[\\](\\d+)\&callIds\\[\\](\\d+)\&callIds\\[\\](\\d+)\&callIds\\[\\](\\d+)', 'cal$1,$2,$3,$4,$5,$6', '6', 'calendar', 'feature_calendar', 101);
INSERT INTO `tiki_sefurl_regex_out` (`left`, `right`, `comment`, `type`, `feature`, `order`) VALUES('tiki-calendar.php\\?calIds\\[\\]=(\\d+)\&calIds\\[\\]=(\\d+)\&callIds\\[\\](\\d+)\&callIds\\[\\](\\d+)\&callIds\\[\\](\\d+)', 'cal$1,$2,$3,$4,$5', '5', 'calendar', 'feature_calendar', 102);
INSERT INTO `tiki_sefurl_regex_out` (`left`, `right`, `comment`, `type`, `feature`, `order`) VALUES('tiki-calendar.php\\?calIds\\[\\]=(\\d+)\&calIds\\[\\]=(\\d+)\&callIds\\[\\](\\d+)\&callIds\\[\\](\\d+)', 'cal$1,$2,$3,$4', '4', 'calendar', 'feature_calendar', 103);
INSERT INTO `tiki_sefurl_regex_out` (`left`, `right`, `comment`, `type`, `feature`, `order`) VALUES('tiki-calendar.php\\?calIds\\[\\]=(\\d+)\&calIds\\[\\]=(\\d+)\&callIds\\[\\](\\d+)', 'cal$1,$2,$3', '3', 'calendar', 'feature_calendar', 104);
INSERT INTO `tiki_sefurl_regex_out` (`left`, `right`, `comment`, `type`, `feature`, `order`) VALUES('tiki-calendar.php\\?calIds\\[\\]=(\\d+)&calIds\\[\\]=(\\d+)', 'cal$1,$2', '2', 'calendar', 'feature_calendar', 105);
INSERT INTO `tiki_sefurl_regex_out` (`left`, `right`, `comment`, `type`, `feature`, `order`) VALUES('tiki-calendar.php\\?calIds\\[\\]=(\\d+)', 'cal$1', '1', 'calendar', 'feature_calendar', 106);
INSERT INTO `tiki_sefurl_regex_out` (`left`, `right`, `type`, `feature`, `order`) VALUES('tiki-calendar.php', 'calendar', 'calendar', 'feature_calendar', 200);
INSERT INTO `tiki_sefurl_regex_out` (`left`, `right`, `type`, `feature`, `order`) VALUES('tiki-view_articles.php', 'articles', '', 'feature_articles', 200);
INSERT INTO `tiki_sefurl_regex_out` (`left`, `right`, `type`, `feature`, `order`) VALUES('tiki-list_blogs.php', 'blogs', '', 'feature_blogs', 200);
INSERT INTO `tiki_sefurl_regex_out` (`left`, `right`, `type`, `feature`, `order`) VALUES('tiki-browse_categories.php', 'categories', '', 'feature_categories', 200);
INSERT INTO `tiki_sefurl_regex_out` (`left`, `right`, `type`, `feature`, `order`) VALUES('tiki-list_charts.php', 'charts', '', 'feature_charts', 200);
INSERT INTO `tiki_sefurl_regex_out` (`left`, `right`, `type`, `feature`, `order`) VALUES('tiki-contact.php', 'contact', '', 'feature_contact', 200);
INSERT INTO `tiki_sefurl_regex_out` (`left`, `right`, `type`, `feature`, `order`) VALUES('tiki-directory_browse.php', 'directories', '', 'feature_directory', 200);
INSERT INTO `tiki_sefurl_regex_out` (`left`, `right`, `type`, `feature`, `order`) VALUES('tiki-list_faqs.php', 'faqs', '', 'feature_faqs', 200);
INSERT INTO `tiki_sefurl_regex_out` (`left`, `right`, `type`, `feature`, `order`) VALUES('tiki-file_galleries.php', 'files', '', 'feature_file_galleries', 200);
INSERT INTO `tiki_sefurl_regex_out` (`left`, `right`, `type`, `feature`, `order`) VALUES('tiki-forums.php', 'forums', '', 'feature_forums', 200);
INSERT INTO `tiki_sefurl_regex_out` (`left`, `right`, `type`, `feature`, `order`) VALUES('tiki-galleries.php', 'galleries', '', 'feature_galleries', 200);
INSERT INTO `tiki_sefurl_regex_out` (`left`, `right`, `type`, `feature`, `order`) VALUES('tiki-login_scr.php', 'login', '', '', 200);
INSERT INTO `tiki_sefurl_regex_out` (`left`, `right`, `type`, `feature`, `order`) VALUES('tiki-my_tiki.php', 'my', '', '', 200);
INSERT INTO `tiki_sefurl_regex_out` (`left`, `right`, `type`, `feature`, `order`) VALUES('tiki-newsletters.php', 'newsletters', 'newsletter', 'feature_newsletters', 200);
INSERT INTO `tiki_sefurl_regex_out` (`left`, `right`, `type`, `feature`, `order`) VALUES('tiki-list_quizzes.php', 'quizzes', '', 'feature_quizzes', 200);
INSERT INTO `tiki_sefurl_regex_out` (`left`, `right`, `type`, `feature`, `order`) VALUES('tiki-stats.php', 'stats', '', 'feature_stats', 200);
INSERT INTO `tiki_sefurl_regex_out` (`left`, `right`, `type`, `feature`, `order`) VALUES('tiki-list_surveys.php', 'surveys', '', 'feature_surveys', 200);
INSERT INTO `tiki_sefurl_regex_out` (`left`, `right`, `type`, `feature`, `order`) VALUES('tiki-list_trackers.php', 'trackers', '', 'feature_trackers', 200);
INSERT INTO `tiki_sefurl_regex_out` (`left`, `right`, `type`, `feature`, `order`) VALUES('tiki-mobile.php', 'mobile', '', 'feature_mobile', 200);
INSERT INTO `tiki_sefurl_regex_out` (`left`, `right`, `type`, `feature`, `order`) VALUES('tiki-sheets.php', 'sheets', '', 'feature_sheet', 200);
