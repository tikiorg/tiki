-- --------------------------------------------------------
-- Database : Tiki 
-- --------------------------------------------------------

ALTER DATABASE DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS `messu_messages`;
CREATE TABLE `messu_messages` (
  `msgId` int(14) NOT NULL auto_increment,
  `user` varchar(200) NOT NULL default '',
  `user_from` varchar(200) NOT NULL default '',
  `user_to` text,
  `user_cc` text,
  `user_bcc` text,
  `subject` varchar(255) default NULL,
  `body` text,
  `hash` varchar(32) default NULL,
  `replyto_hash` varchar(32) default NULL,
  `date` int(14) default NULL,
  `isRead` char(1) default NULL,
  `isReplied` char(1) default NULL,
  `isFlagged` char(1) default NULL,
  `priority` int(2) default NULL,
  PRIMARY KEY (`msgId`),
  KEY `userIsRead` (user, `isRead`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `messu_archive`;
CREATE TABLE `messu_archive` (
  `msgId` int(14) NOT NULL auto_increment,
  `user` varchar(40) NOT NULL default '',
  `user_from` varchar(40) NOT NULL default '',
  `user_to` text,
  `user_cc` text,
  `user_bcc` text,
  `subject` varchar(255) default NULL,
  `body` text,
  `hash` varchar(32) default NULL,
  `replyto_hash` varchar(32) default NULL,
  `date` int(14) default NULL,
  `isRead` char(1) default NULL,
  `isReplied` char(1) default NULL,
  `isFlagged` char(1) default NULL,
  `priority` int(2) default NULL,
  PRIMARY KEY (`msgId`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `messu_sent`;
CREATE TABLE `messu_sent` (
  `msgId` int(14) NOT NULL auto_increment,
  `user` varchar(40) NOT NULL default '',
  `user_from` varchar(40) NOT NULL default '',
  `user_to` text,
  `user_cc` text,
  `user_bcc` text,
  `subject` varchar(255) default NULL,
  `body` text,
  `hash` varchar(32) default NULL,
  `replyto_hash` varchar(32) default NULL,
  `date` int(14) default NULL,
  `isRead` char(1) default NULL,
  `isReplied` char(1) default NULL,
  `isFlagged` char(1) default NULL,
  `priority` int(2) default NULL,
  PRIMARY KEY (`msgId`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `sesskey` char(32) NOT NULL,
  `expiry` int(11) unsigned NOT NULL,
  `expireref` varchar(64),
  `data` text NOT NULL,
  PRIMARY KEY (`sesskey`),
  KEY `expiry` (expiry)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_actionlog`;
CREATE TABLE `tiki_actionlog` (
  `actionId` int(8) NOT NULL auto_increment,
  `action` varchar(255) NOT NULL default '',
  `lastModif` int(14) default NULL,
  `object` varchar(255) default NULL,
  `objectType` varchar(32) NOT NULL default '',
  `user` varchar(200) default '',
  `ip` varchar(39) default NULL,
  `comment` varchar(200) default NULL,
  `categId` int(12) NOT NULL default '0',
    `client` VARCHAR( 200 ) NULL DEFAULT NULL,
  PRIMARY KEY (`actionId`),
  KEY `lastModif` (`lastModif`),
  KEY `object` (`object`(100), `objectType`, `action`(100))
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_actionlog_params`;
CREATE TABLE `tiki_actionlog_params` (
  `actionId` int(8) NOT NULL,
  `name` varchar(40) NOT NULL,
  `value` text,
  KEY `actionId` (`actionId`),
  KEY `nameValue` (`name`, `value`(200))
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_articles`;
CREATE TABLE `tiki_articles` (
  `articleId` int(8) NOT NULL auto_increment,
  `topline` varchar(255) default NULL,
  `title` varchar(255) default NULL,
  `subtitle` varchar(255) default NULL,
  `linkto` varchar(255) default NULL,
  `lang` varchar(16) default NULL,
  `state` char(1) default 's',
  `authorName` varchar(60) default NULL,
  `topicId` int(14) default NULL,
  `topicName` varchar(40) default NULL,
  `size` int(12) default NULL,
  `useImage` char(1) default NULL,
  `image_name` varchar(80) default NULL,
  `image_caption` text default NULL,
  `image_type` varchar(80) default NULL,
  `image_size` int(14) default NULL,
  `image_x` int(4) default NULL,
  `image_y` int(4) default NULL,
  `list_image_x` int(4) default NULL,
  `image_data` longblob,
  `publishDate` int(14) default NULL,
  `expireDate` int(14) default NULL,
  `created` int(14) default NULL,
  `heading` text,
  `body` text,
  `hash` varchar(32) default NULL,
  `author` varchar(200) default NULL,
  `nbreads` int(14) default NULL,
  `votes` int(8) default NULL,
  `points` int(14) default NULL,
  `type` varchar(50) default NULL,
  `rating` decimal(3,2) default NULL,
  `isfloat` char(1) default NULL,
  `ispublished` char(1) NOT NULL DEFAULT 'y',
  PRIMARY KEY (`articleId`),
  KEY `title` (`title`),
  KEY `heading` (`heading`(255)),
  KEY `body` (`body`(255)),
  KEY `nbreads` (`nbreads`),
  KEY `author` (`author`(32)),
  KEY `topicId` (`topicId`),
  KEY `publishDate` (`publishDate`),
  KEY `expireDate` (`expireDate`),
  KEY `type` (`type`),
  FULLTEXT KEY `ft` (`title`, `heading`, `body`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_article_types`;
CREATE TABLE `tiki_article_types` (
  `type` varchar(50) NOT NULL,
  `use_ratings` varchar(1) default NULL,
  `show_pre_publ` varchar(1) default NULL,
  `show_post_expire` varchar(1) default 'y',
  `heading_only` varchar(1) default NULL,
  `allow_comments` varchar(1) default 'y',
  `show_image` varchar(1) default 'y',
  `show_avatar` varchar(1) default NULL,
  `show_author` varchar(1) default 'y',
  `show_pubdate` varchar(1) default 'y',
  `show_expdate` varchar(1) default NULL,
  `show_reads` varchar(1) default 'y',
  `show_size` varchar(1) default 'n',
  `show_topline` varchar(1) default 'n',
  `show_subtitle` varchar(1) default 'n',
  `show_linkto` varchar(1) default 'n',
  `show_image_caption` varchar(1) default 'n',
  `show_lang` varchar(1) default 'n',
  `creator_edit` varchar(1) default NULL,
  `comment_can_rate_article` char(1) default NULL,
  PRIMARY KEY (`type`),
  KEY `show_pre_publ` (`show_pre_publ`),
  KEY `show_post_expire` (`show_post_expire`)
) ENGINE=MyISAM ;

INSERT IGNORE INTO tiki_article_types(type) VALUES ('Article');
INSERT IGNORE INTO tiki_article_types(type,use_ratings) VALUES ('Review','y');
INSERT IGNORE INTO tiki_article_types(type,show_post_expire) VALUES ('Event','n');
INSERT IGNORE INTO tiki_article_types(type,show_post_expire,heading_only,allow_comments) VALUES ('Classified','n','y','n');

DROP TABLE IF EXISTS `tiki_banners`;
CREATE TABLE `tiki_banners` (
  `bannerId` int(12) NOT NULL auto_increment,
  `client` varchar(200) NOT NULL default '',
  `url` varchar(255) default NULL,
  `title` varchar(255) default NULL,
  `alt` varchar(250) default NULL,
  `which` varchar(50) default NULL,
  `imageData` longblob,
  `imageType` varchar(200) default NULL,
  `imageName` varchar(100) default NULL,
  `HTMLData` text,
  `fixedURLData` varchar(255) default NULL,
  `textData` text,
  `fromDate` int(14) default NULL,
  `toDate` int(14) default NULL,
  `useDates` char(1) default NULL,
  `mon` char(1) default NULL,
  `tue` char(1) default NULL,
  `wed` char(1) default NULL,
  `thu` char(1) default NULL,
  `fri` char(1) default NULL,
  `sat` char(1) default NULL,
  `sun` char(1) default NULL,
  `hourFrom` varchar(4) default NULL,
  `hourTo` varchar(4) default NULL,
  `created` int(14) default NULL,
  `maxImpressions` int(8) default NULL,
  `impressions` int(8) default NULL,
  `maxUserImpressions` int(8) default -1,
  `maxClicks` int(8) default NULL,
  `clicks` int(8) default NULL,
  `zone` varchar(40) default NULL,
  PRIMARY KEY (`bannerId`),
  INDEX ban1(zone,`useDates`,impressions,`maxImpressions`,`hourFrom`,`hourTo`,`fromDate`,`toDate`,mon,tue,wed,thu,fri,sat,sun)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_banning`;
CREATE TABLE `tiki_banning` (
  `banId` int(12) NOT NULL auto_increment,
  `mode` enum('user','ip') default NULL,
  `title` varchar(200) default NULL,
  `ip1` char(3) default NULL,
  `ip2` char(3) default NULL,
  `ip3` char(3) default NULL,
  `ip4` char(3) default NULL,
  `user` varchar(200) default '',
  `date_from` timestamp NOT NULL,
  `date_to` timestamp NOT NULL,
  `use_dates` char(1) default NULL,
  `created` int(14) default NULL,
  `message` text,
  PRIMARY KEY (`banId`),
  INDEX ban(`use_dates`, `date_from`, `date_to`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_banning_sections`;
CREATE TABLE `tiki_banning_sections` (
  `banId` int(12) NOT NULL default '0',
  `section` varchar(100) NOT NULL default '',
  PRIMARY KEY (`banId`,`section`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_blog_activity`;
CREATE TABLE `tiki_blog_activity` (
  `blogId` int(8) NOT NULL default '0',
  `day` int(14) NOT NULL default '0',
  `posts` int(8) default NULL,
  PRIMARY KEY (`blogId`,`day`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_blog_posts`;
CREATE TABLE `tiki_blog_posts` (
  `postId` int(8) NOT NULL auto_increment,
  `blogId` int(8) NOT NULL default '0',
  `data` text,
  `data_size` int(11) unsigned NOT NULL default '0',
  `excerpt` text default NULL,
  `created` int(14) default NULL,
  `user` varchar(200) default '',
  `trackbacks_to` text,
  `trackbacks_from` text,
  `title` varchar(255) default NULL,
  `priv` varchar(1) default 'n',
  `wysiwyg` varchar(1) default NULL,
  PRIMARY KEY (`postId`),
  KEY `data` (`data`(255)),
  KEY `blogId` (`blogId`),
  KEY `created` (`created`),
  FULLTEXT KEY `ft` (`data`, `title`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_blog_posts_images`;
CREATE TABLE `tiki_blog_posts_images` (
  `imgId` int(14) NOT NULL auto_increment,
  `postId` int(14) NOT NULL default '0',
  `filename` varchar(80) default NULL,
  `filetype` varchar(80) default NULL,
  `filesize` int(14) default NULL,
  `data` longblob,
  PRIMARY KEY (`imgId`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_blogs`;
CREATE TABLE `tiki_blogs` (
  `blogId` int(8) NOT NULL auto_increment,
  `created` int(14) default NULL,
  `lastModif` int(14) default NULL,
  `title` varchar(200) default NULL,
  `description` text,
  `user` varchar(200) default '',
  `public` char(1) default NULL,
  `posts` int(8) default NULL,
  `maxPosts` int(8) default NULL,
  `hits` int(8) default NULL,
  `activity` decimal(4,2) default NULL,
  `heading` text,
  `post_heading` text,
  `use_find` char(1) default NULL,
  `use_title` char(1) default 'y',
  `use_title_in_post` char(1) default 'y',
  `use_description` char(1) default 'y',
  `use_breadcrumbs` char(1) default 'n',
  `use_author` char(1) default NULL,
  `use_excerpt` char(1) default NULL,
  `add_date` char(1) default NULL,
  `add_poster` char(1) default NULL,
  `allow_comments` char(1) default NULL,
  `show_avatar` char(1) default NULL,
  `always_owner` char(1) default NULL,
  `show_related` char(1) default NULL,
  `related_max` int(4) default 5,
  PRIMARY KEY (`blogId`),
  KEY `title` (`title`),
  KEY `description` (`description`(255)),
  KEY `hits` (`hits`),
  FULLTEXT KEY `ft` (`title`, `description`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_calendar_categories`;
CREATE TABLE `tiki_calendar_categories` (
  `calcatId` int(11) NOT NULL auto_increment,
  `calendarId` int(14) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  PRIMARY KEY (`calcatId`),
  UNIQUE KEY `catname` (`calendarId`, `name`(16))
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_calendar_items`;
DROP TABLE IF EXISTS `tiki_calendar_recurrence`;
CREATE TABLE `tiki_calendar_recurrence` (
  `recurrenceId` int(14) NOT NULL auto_increment,
  `calendarId` int(14) NOT NULL default '0',
  `start` int(4) NOT NULL default '0',
  `end` int(4) NOT NULL default '2359',
  `allday` tinyint(1) NOT NULL default '0',
  `locationId` int(14) default NULL,
  `categoryId` int(14) default NULL,
  `nlId` int(12) NOT NULL default '0',
  `priority` enum('1','2','3','4','5','6','7','8','9') NOT NULL default '1',
  `status` enum('0','1','2') NOT NULL default '0',
  `url` varchar(255) default NULL,
  `lang` char(16) NOT NULL default 'en',
  `name` varchar(255) NOT NULL default '',
  `description` blob,
  `weekly` tinyint(1) default '0',
  `weekday` tinyint(1),
  `monthly` tinyint(1) default '0',
  `dayOfMonth` int(2),
  `yearly` tinyint(1) default '0',
  `dateOfYear` int(4),
  `nbRecurrences` int(8),
  `startPeriod` int(14),
  `endPeriod` int(14),
  `user` varchar(200) default '',
  `created` int(14) NOT NULL default '0',
  `lastmodif` int(14) NOT NULL default '0',
  PRIMARY KEY (`recurrenceId`),
  KEY `calendarId` (`calendarId`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

CREATE TABLE `tiki_calendar_items` (
  `calitemId` int(14) NOT NULL auto_increment,
  `calendarId` int(14) NOT NULL default '0',
  `start` int(14) NOT NULL default '0',
  `end` int(14) NOT NULL default '0',
  `locationId` int(14) default NULL,
  `categoryId` int(14) default NULL,
  `nlId` int(12) NOT NULL default '0',
  `priority` enum('0', '1','2','3','4','5','6','7','8','9') default '0',
  `status` enum('0','1','2') NOT NULL default '0',
  `url` varchar(255) default NULL,
  `lang` char(16) NOT NULL default 'en',
  `name` varchar(255) NOT NULL default '',
  `description` text,
  `recurrenceId` int(14),
  `changed` tinyint(1) DEFAULT '0',
  `user` varchar(200) default '',
  `created` int(14) NOT NULL default '0',
  `lastmodif` int(14) NOT NULL default '0',
  `allday` tinyint(1) NOT NULL default '0',
  PRIMARY KEY (`calitemId`),
  KEY `calendarId` (`calendarId`),
  FULLTEXT KEY `ft` (`name`,`description`),
  CONSTRAINT `fk_calitems_recurrence`
    FOREIGN KEY (`recurrenceId`) REFERENCES `tiki_calendar_recurrence`(`recurrenceId`)
    ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_calendar_locations`;
CREATE TABLE `tiki_calendar_locations` (
  `callocId` int(14) NOT NULL auto_increment,
  `calendarId` int(14) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `description` blob,
  PRIMARY KEY (`callocId`),
  UNIQUE KEY `locname` (`calendarId`, `name`(16))
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_calendar_roles`;
CREATE TABLE `tiki_calendar_roles` (
  `calitemId` int(14) NOT NULL default '0',
  `username` varchar(200) NOT NULL default '',
  `role` enum('0','1','2','3','6') NOT NULL default '0',
  PRIMARY KEY (`calitemId`,`username`(16),`role`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_calendars`;
CREATE TABLE `tiki_calendars` (
  `calendarId` int(14) NOT NULL auto_increment,
  `name` varchar(80) NOT NULL default '',
  `description` varchar(255) default NULL,
  `user` varchar(200) NOT NULL default '',
  `customlocations` enum('n','y') NOT NULL default 'n',
  `customcategories` enum('n','y') NOT NULL default 'n',
  `customlanguages` enum('n','y') NOT NULL default 'n',
  `custompriorities` enum('n','y') NOT NULL default 'n',
  `customparticipants` enum('n','y') NOT NULL default 'n',
  `customsubscription` enum('n','y') NOT NULL default 'n',
  `customstatus` enum('n','y') NOT NULL default 'y',
  `created` int(14) NOT NULL default '0',
  `lastmodif` int(14) NOT NULL default '0',
  `personal` enum ('n', 'y') NOT NULL default 'n',
  PRIMARY KEY (`calendarId`)
) ENGINE=MyISAM ;

DROP TABLE IF EXISTS `tiki_calendar_options`;
CREATE TABLE `tiki_calendar_options` (
    `calendarId` int(14) NOT NULL default 0,
    `optionName` varchar(120) NOT NULL default '',
    `value` varchar(255),
    PRIMARY KEY (`calendarId`,`optionName`)
) ENGINE=MyISAM ;

DROP TABLE IF EXISTS `tiki_categories`;
CREATE TABLE `tiki_categories` (
  `categId` int(12) NOT NULL auto_increment,
  `name` varchar(100) default NULL,
  `description` varchar(250) default NULL,
  `parentId` int(12) default NULL,
  `hits` int(8) default NULL,
  `rootCategId` int(12) default NULL,
  PRIMARY KEY (`categId`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_objects`;
CREATE TABLE `tiki_objects` (
  `objectId` int(12) NOT NULL auto_increment,
  `type` varchar(50) default NULL,
  `itemId` varchar(255) default NULL,
  `description` text,
  `created` int(14) default NULL,
  `name` varchar(200) default NULL,
  `href` varchar(200) default NULL,
  `hits` int(8) default NULL,
  `comments_locked` char(1) NOT NULL default 'n',
  PRIMARY KEY (`objectId`),
  KEY (`type`, `objectId`),
  KEY (`itemId`, `type`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_categorized_objects`;
CREATE TABLE `tiki_categorized_objects` (
  `catObjectId` int(11) NOT NULL default '0',
  PRIMARY KEY (`catObjectId`)
) ENGINE=MyISAM ;

DROP TABLE IF EXISTS `tiki_category_objects`;
CREATE TABLE `tiki_category_objects` (
  `catObjectId` int(12) NOT NULL default '0',
  `categId` int(12) NOT NULL default '0',
  PRIMARY KEY (`catObjectId`,`categId`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_object_ratings`;
CREATE TABLE `tiki_object_ratings` (
  `catObjectId` int(12) NOT NULL default '0',
  `pollId` int(12) NOT NULL default '0',
  PRIMARY KEY (`catObjectId`,`pollId`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_category_sites`;
CREATE TABLE `tiki_category_sites` (
  `categId` int(10) NOT NULL default '0',
  `siteId` int(14) NOT NULL default '0',
  PRIMARY KEY (`categId`,`siteId`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_chat_channels`;
CREATE TABLE `tiki_chat_channels` (
  `channelId` int(8) NOT NULL auto_increment,
  `name` varchar(30) default NULL,
  `description` varchar(250) default NULL,
  `max_users` int(8) default NULL,
  `mode` char(1) default NULL,
  `moderator` varchar(200) default NULL,
  `active` char(1) default NULL,
  `refresh` int(6) default NULL,
  PRIMARY KEY (`channelId`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_chat_messages`;
CREATE TABLE `tiki_chat_messages` (
  `messageId` int(8) NOT NULL auto_increment,
  `channelId` int(8) NOT NULL default '0',
  `data` varchar(255) default NULL,
  `poster` varchar(200) NOT NULL default 'anonymous',
  `timestamp` int(14) default NULL,
  PRIMARY KEY (`messageId`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_chat_users`;
CREATE TABLE `tiki_chat_users` (
  `nickname` varchar(200) NOT NULL default '',
  `channelId` int(8) NOT NULL default '0',
  `timestamp` int(14) default NULL,
  PRIMARY KEY (`nickname`,`channelId`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_comments`;
CREATE TABLE `tiki_comments` (
  `threadId` int(14) NOT NULL auto_increment,
  `object` varchar(255) NOT NULL default '',
  `objectType` varchar(32) NOT NULL default '',
  `parentId` int(14) default NULL,
  `userName` varchar(200) default '',
  `commentDate` int(14) default NULL,
  `hits` int(8) default NULL,
  `type` char(1) default NULL,
  `points` decimal(8,2) default NULL,
  `votes` int(8) default NULL,
  `average` decimal(8,4) default NULL,
  `title` varchar(255) default NULL,
  `data` text,
  `hash` varchar(32) default NULL,
  `email` varchar(200) default NULL,
  `website` varchar(200) default NULL,
  `user_ip` varchar(15) default NULL,
  `summary` varchar(240) default NULL,
  `smiley` varchar(80) default NULL,
  `message_id` varchar(128) default NULL,
  `in_reply_to` varchar(128) default NULL,
  `comment_rating` tinyint(2) default NULL,
  `archived` char(1) default NULL,
  `approved` char(1) NOT NULL default 'y',
  `locked` char(1) NOT NULL default 'n',
  PRIMARY KEY (`threadId`),
  UNIQUE KEY `no_repeats` (`parentId`, `userName`(40), `title`(100), `commentDate`, `message_id`(40), `in_reply_to`(40)),
  KEY `title` (`title`),
  KEY `data` (`data`(255)),
  KEY `hits` (hits),
  KEY `tc_pi` (`parentId`),
  KEY `objectType` (object, `objectType`),
  KEY `commentDate` (`commentDate`),
  KEY `threaded` (message_id, in_reply_to, `parentId`),
  FULLTEXT KEY `ft` (title,data)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_content`;
CREATE TABLE `tiki_content` (
  `contentId` int(8) NOT NULL auto_increment,
  `description` text,
  `contentLabel` varchar(255) NOT NULL default '',
  PRIMARY KEY (`contentId`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_content_templates`;
CREATE TABLE `tiki_content_templates` (
  `templateId` int(10) NOT NULL auto_increment,
  `template_type` VARCHAR( 20 ) NOT NULL DEFAULT 'static',
  `content` longblob,
  `name` varchar(200) default NULL,
  `created` int(14) default NULL,
  PRIMARY KEY (`templateId`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_content_templates_sections`;
CREATE TABLE `tiki_content_templates_sections` (
  `templateId` int(10) NOT NULL default '0',
  `section` varchar(250) NOT NULL default '',
  PRIMARY KEY (`templateId`,`section`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_cookies`;
CREATE TABLE `tiki_cookies` (
  `cookieId` int(10) NOT NULL auto_increment,
  `cookie` text,
  PRIMARY KEY (`cookieId`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_copyrights`;
CREATE TABLE `tiki_copyrights` (
  `copyrightId` int(12) NOT NULL auto_increment,
  `page` varchar(200) default NULL,
  `title` varchar(200) default NULL,
  `year` int(11) default NULL,
  `authors` varchar(200) default NULL,
  `copyright_order` int(11) default NULL,
  `userName` varchar(200) default '',
  PRIMARY KEY (`copyrightId`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_directory_categories`;
CREATE TABLE `tiki_directory_categories` (
  `categId` int(10) NOT NULL auto_increment,
  `parent` int(10) default NULL,
  `name` varchar(240) default NULL,
  `description` text,
  `childrenType` char(1) default NULL,
  `sites` int(10) default NULL,
  `viewableChildren` int(4) default NULL,
  `allowSites` char(1) default NULL,
  `showCount` char(1) default NULL,
  `editorGroup` varchar(200) default NULL,
  `hits` int(12) default NULL,
  PRIMARY KEY (`categId`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_directory_search`;
CREATE TABLE `tiki_directory_search` (
  `term` varchar(250) NOT NULL default '',
  `hits` int(14) default NULL,
  PRIMARY KEY (`term`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_directory_sites`;
CREATE TABLE `tiki_directory_sites` (
  `siteId` int(14) NOT NULL auto_increment,
  `name` varchar(240) default NULL,
  `description` text,
  `url` varchar(255) default NULL,
  `country` varchar(255) default NULL,
  `hits` int(12) default NULL,
  `isValid` char(1) default NULL,
  `created` int(14) default NULL,
  `lastModif` int(14) default NULL,
  `cache` longblob,
  `cache_timestamp` int(14) default NULL,
  PRIMARY KEY (`siteId`),
  KEY (`isValid`),
  KEY (url),
  FULLTEXT KEY `ft` (name,description)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_dsn`;
CREATE TABLE `tiki_dsn` (
  `dsnId` int(12) NOT NULL auto_increment,
  `name` varchar(200) NOT NULL default '',
  `dsn` varchar(255) default NULL,
  PRIMARY KEY (`dsnId`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_dynamic_variables`;
CREATE TABLE `tiki_dynamic_variables` (
  `name` varchar(40) NOT NULL,
  `data` text,
  `lang` VARCHAR(16) NULL
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_extwiki`;
CREATE TABLE `tiki_extwiki` (
  `extwikiId` int(12) NOT NULL auto_increment,
  `name` varchar(200) NOT NULL default '',
  `extwiki` varchar(255) default NULL,
  PRIMARY KEY (`extwikiId`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_faq_questions`;
CREATE TABLE `tiki_faq_questions` (
  `questionId` int(10) NOT NULL auto_increment,
  `faqId` int(10) default NULL,
  `position` int(4) default NULL,
  `question` text,
  `answer` text,
  `created` int(14) default NULL,
  PRIMARY KEY (`questionId`),
  KEY `faqId` (`faqId`),
  KEY `question` (question(255)),
  KEY `answer` (answer(255)),
  KEY `created` (`created`),
  FULLTEXT KEY `ft` (question,answer)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_faqs`;
CREATE TABLE `tiki_faqs` (
  `faqId` int(10) NOT NULL auto_increment,
  `title` varchar(200) default NULL,
  `description` text,
  `created` int(14) default NULL,
  `questions` int(5) default NULL,
  `hits` int(8) default NULL,
  `canSuggest` char(1) default NULL,
  PRIMARY KEY (`faqId`),
  KEY `title` (title),
  KEY `description` (description(255)),
  KEY `hits` (hits),
  FULLTEXT KEY `ft` (title,description)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_featured_links`;
CREATE TABLE `tiki_featured_links` (
  `url` varchar(200) NOT NULL default '',
  `title` varchar(200) default NULL,
  `description` text,
  `hits` int(8) default NULL,
  `position` int(6) default NULL,
  `type` char(1) default NULL,
  PRIMARY KEY (`url`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_file_galleries`;
CREATE TABLE `tiki_file_galleries` (
  `galleryId` int(14) NOT NULL auto_increment,
  `name` varchar(80) NOT NULL default '',
  `type` varchar(20) NOT NULL default 'default',
  `template` int(10) default NULL,
  `description` text,
  `created` int(14) default NULL,
  `visible` char(1) default NULL,
  `lastModif` int(14) default NULL,
  `user` varchar(200) default '',
  `hits` int(14) default NULL,
  `votes` int(8) default NULL,
  `points` decimal(8,2) default NULL,
  `maxRows` int(10) default NULL,
  `public` char(1) default NULL,
  `show_id` char(1) default NULL,
  `show_icon` char(1) default NULL,
  `show_name` char(1) default NULL,
  `show_size` char(1) default NULL,
  `show_description` char(1) default NULL,
  `max_desc` int(8) default NULL,
  `show_created` char(1) default NULL,
  `show_hits` char(1) default NULL,
  `show_lastDownload` char(1) default NULL,
  `parentId` int(14) NOT NULL default -1,
  `lockable` char(1) default 'n',
  `show_lockedby` char(1) default NULL,
  `archives` int(4) default 0,
  `sort_mode` char(20) default NULL,
  `show_modified` char(1) default NULL,
  `show_author` char(1) default NULL,
  `show_creator` char(1) default NULL,
  `subgal_conf` varchar(200) default NULL,
  `show_last_user` char(1) default NULL,
  `show_comment` char(1) default NULL,
  `show_files` char(1) default NULL,
  `show_explorer` char(1) default NULL,
  `show_path` char(1) default NULL,
  `show_slideshow` char(1) default NULL,
  `default_view` varchar(20) default NULL,
  `quota` int(8) default 0,
  `size` int(14) default NULL,
  `wiki_syntax` varchar(200) default NULL,
  `backlinkPerms` char(1) default 'n',
  `show_backlinks` char(1) default NULL,
  `show_deleteAfter` char(1) default NULL,
  `show_checked` char(1) default NULL,
  `show_share` char(1) default NULL,
  `image_max_size_x` int(8) NOT NULL default '0',
  `image_max_size_y` int(8) NOT NULL default '0',
  PRIMARY KEY (`galleryId`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

INSERT INTO `tiki_file_galleries` (`galleryId`, `name`, `type`, `description`, `visible`, `user`, `public`, `parentId`) VALUES ('1','File Galleries', 'system', '', 'y', 'admin', 'y', -1);
INSERT INTO `tiki_file_galleries` (`galleryId`, `name`, `type`, `description`, `visible`, `user`, `public`, `parentId`) VALUES ('2','Users File Galleries', 'system', '', 'y', 'admin', 'y', -1);
INSERT INTO `tiki_file_galleries` (`galleryId`, `name`, `type`, `description`, `visible`, `user`, `public`, `parentId`) VALUES ('3','Wiki Attachments', 'system', '', 'y', 'admin', 'y', -1);


DROP TABLE IF EXISTS `tiki_files`;
CREATE TABLE `tiki_files` (
  `fileId` int(14) NOT NULL auto_increment,
  `galleryId` int(14) NOT NULL default '0',
  `name` varchar(200) NOT NULL default '',
  `description` text,
  `created` int(14) default NULL,
  `filename` varchar(80) default NULL,
  `filesize` int(14) default NULL,
  `filetype` varchar(250) default NULL,
  `data` longblob,
  `user` varchar(200) default '',
  `author` varchar(40) default NULL,
  `hits` int(14) default NULL,
  `maxhits` INT( 14 ) default NULL, 
  `lastDownload` int(14) default NULL,
  `votes` int(8) default NULL,
  `points` decimal(8,2) default NULL,
  `path` varchar(255) default NULL,
  `reference_url` varchar(250) default NULL,
  `is_reference` char(1) default NULL,
  `hash` varchar(32) default NULL,
  `search_data` longtext,
  `lastModif` integer(14) DEFAULT NULL,
  `lastModifUser` varchar(200) DEFAULT NULL,
  `lockedby` varchar(200) default '',
  `comment` varchar(200) default NULL,
  `archiveId` int(14) default 0,
  `deleteAfter` int(14) default NULL,
  PRIMARY KEY (`fileId`),
  KEY `name` (name),
  KEY `description` (description(255)),
  KEY `created` (created),
  KEY `archiveId` (`archiveId`),
  KEY `galleryId` (`galleryId`),
  KEY `hits` (hits),
  FULLTEXT KEY `ft` (name,description,search_data,filename)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_file_drafts`;
CREATE TABLE `tiki_file_drafts` (
  `fileId` int(14) NOT NULL,
  `filename` varchar(80) default NULL,
  `filesize` int(14) default NULL,
  `filetype` varchar(250) default NULL,
  `data` longblob,
  `user` varchar(200) default '',
  `path` varchar(255) default NULL,
  `hash` varchar(32) default NULL,
  `lastModif` integer(14) DEFAULT NULL,
  `lockedby` varchar(200) default '',
  PRIMARY KEY (`fileId`, `user`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_forum_attachments`;
CREATE TABLE `tiki_forum_attachments` (
  `attId` int(14) NOT NULL auto_increment,
  `threadId` int(14) NOT NULL default '0',
  `qId` int(14) NOT NULL default '0',
  `forumId` int(14) default NULL,
  `filename` varchar(250) default NULL,
  `filetype` varchar(250) default NULL,
  `filesize` int(12) default NULL,
  `data` longblob,
  `dir` varchar(200) default NULL,
  `created` int(14) default NULL,
  `path` varchar(250) default NULL,
  PRIMARY KEY (`attId`),
  KEY `threadId` (`threadId`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_forum_reads`;
CREATE TABLE `tiki_forum_reads` (
  `user` varchar(200) NOT NULL default '',
  `threadId` int(14) NOT NULL default '0',
  `forumId` int(14) default NULL,
  `timestamp` int(14) default NULL,
  PRIMARY KEY (`user`,`threadId`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_forums`;
CREATE TABLE `tiki_forums` (
  `forumId` int(8) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `description` text,
  `created` int(14) default NULL,
  `lastPost` int(14) default NULL,
  `threads` int(8) default NULL,
  `comments` int(8) default NULL,
  `controlFlood` char(1) default NULL,
  `floodInterval` int(8) default NULL,
  `moderator` varchar(200) default NULL,
  `hits` int(8) default NULL,
  `mail` varchar(200) default NULL,
  `useMail` char(1) default NULL,
  `section` varchar(200) default NULL,
  `usePruneUnreplied` char(1) default NULL,
  `pruneUnrepliedAge` int(8) default NULL,
  `usePruneOld` char(1) default NULL,
  `pruneMaxAge` int(8) default NULL,
  `topicsPerPage` int(6) default NULL,
  `topicOrdering` varchar(100) default NULL,
  `threadOrdering` varchar(100) default NULL,
  `att` varchar(80) default NULL,
  `att_store` varchar(4) default NULL,
  `att_store_dir` varchar(250) default NULL,
  `att_max_size` int(12) default NULL,
  `att_list_nb` char(1) default NULL,
  `ui_level` char(1) default NULL,
  `forum_password` varchar(32) default NULL,
  `forum_use_password` char(1) default NULL,
  `moderator_group` varchar(200) default NULL,
  `approval_type` varchar(20) default NULL,
  `outbound_address` varchar(250) default NULL,
  `outbound_mails_for_inbound_mails` char(1) default NULL,
  `outbound_mails_reply_link` char(1) default NULL,
  `outbound_from` varchar(250) default NULL,
  `inbound_pop_server` varchar(250) default NULL,
  `inbound_pop_port` int(4) default NULL,
  `inbound_pop_user` varchar(200) default NULL,
  `inbound_pop_password` varchar(80) default NULL,
  `topic_smileys` char(1) default NULL,
  `ui_avatar` char(1) default NULL,
  `ui_flag` char(1) default NULL,
  `ui_posts` char(1) default NULL,
  `ui_email` char(1) default NULL,
  `ui_online` char(1) default NULL,
  `topic_summary` char(1) default NULL,
  `show_description` char(1) default NULL,
  `topics_list_replies` char(1) default NULL,
  `topics_list_reads` char(1) default NULL,
  `topics_list_pts` char(1) default NULL,
  `topics_list_lastpost` char(1) default NULL,
  `topics_list_lastpost_title` char(1) default NULL,
  `topics_list_lastpost_avatar` char(1) default NULL,
  `topics_list_author` char(1) default NULL,
  `topics_list_author_avatar` char(1) default NULL,
  `vote_threads` char(1) default NULL,
  `forum_last_n` int(2) default 0,
  `threadStyle` varchar(100) default NULL,
  `commentsPerPage` varchar(100) default NULL,
  `is_flat` char(1) default NULL,
  `mandatory_contribution` char(1) default NULL,
  PRIMARY KEY (`forumId`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_forums_queue`;
CREATE TABLE `tiki_forums_queue` (
  `qId` int(14) NOT NULL auto_increment,
  `object` varchar(32) default NULL,
  `parentId` int(14) default NULL,
  `forumId` int(14) default NULL,
  `timestamp` int(14) default NULL,
  `user` varchar(200) default '',
  `title` varchar(240) default NULL,
  `data` text,
  `type` varchar(60) default NULL,
  `hash` varchar(32) default NULL,
  `topic_smiley` varchar(80) default NULL,
  `topic_title` varchar(240) default NULL,
  `summary` varchar(240) default NULL,
  `in_reply_to` varchar(128) default NULL,
  `tags` varchar(255) default NULL,
  `email` varchar(255) default NULL,
  PRIMARY KEY (`qId`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_forums_reported`;
CREATE TABLE `tiki_forums_reported` (
  `threadId` int(12) NOT NULL default '0',
  `forumId` int(12) NOT NULL default '0',
  `parentId` int(12) NOT NULL default '0',
  `user` varchar(200) default '',
  `timestamp` int(14) default NULL,
  `reason` varchar(250) default NULL,
  PRIMARY KEY (`threadId`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_galleries`;
CREATE TABLE `tiki_galleries` (
  `galleryId` int(14) NOT NULL auto_increment,
  `name` varchar(80) NOT NULL default '',
  `description` text,
  `created` int(14) default NULL,
  `lastModif` int(14) default NULL,
  `visible` char(1) default NULL,
  `geographic` char(1) default NULL,
  `theme` varchar(60) default NULL,
  `user` varchar(200) default '',
  `hits` int(14) default NULL,
  `maxRows` int(10) default NULL,
  `rowImages` int(10) default NULL,
  `thumbSizeX` int(10) default NULL,
  `thumbSizeY` int(10) default NULL,
  `public` char(1) default NULL,
  `sortorder` varchar(20) NOT NULL default 'created',
  `sortdirection` varchar(4) NOT NULL default 'desc',
  `galleryimage` varchar(20) NOT NULL default 'first',
  `parentgallery` int(14) NOT NULL default -1,
  `showname` char(1) NOT NULL default 'y',
  `showimageid` char(1) NOT NULL default 'n',
  `showdescription` char(1) NOT NULL default 'n',
  `showcreated` char(1) NOT NULL default 'n',
  `showuser` char(1) NOT NULL default 'n',
  `showhits` char(1) NOT NULL default 'y',
  `showxysize` char(1) NOT NULL default 'y',
  `showfilesize` char(1) NOT NULL default 'n',
  `showfilename` char(1) NOT NULL default 'n',
  `defaultscale` varchar(10) NOT NULL DEFAULT 'o',
  `showcategories` char(1) NOT NULL default 'n', 
  PRIMARY KEY (`galleryId`),
  KEY `name` (name),
  KEY `description` (description(255)),
  KEY `hits` (hits),
  KEY `parentgallery` (parentgallery),
  KEY `visibleUser` (visible, user),
  FULLTEXT KEY `ft` (name,description)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_galleries_scales`;
CREATE TABLE `tiki_galleries_scales` (
  `galleryId` int(14) NOT NULL default '0',
  `scale` int(11) NOT NULL default '0',
  PRIMARY KEY (`galleryId`,`scale`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_group_inclusion`;
CREATE TABLE `tiki_group_inclusion` (
  `groupName` varchar(255) NOT NULL default '',
  `includeGroup` varchar(255) NOT NULL default '',
  PRIMARY KEY (`groupName`(120),`includeGroup`(120))
) ENGINE=MyISAM;
INSERT INTO  `tiki_group_inclusion` (`groupName` ,`includeGroup`) VALUES ('Registered','Anonymous');

DROP TABLE IF EXISTS `tiki_group_watches`;
CREATE TABLE `tiki_group_watches` (
  `watchId` int(12) NOT NULL auto_increment,
  `group` varchar(200) NOT NULL default '',
  `event` varchar(40) NOT NULL default '',
  `object` varchar(200) NOT NULL default '',
  `title` varchar(250) default NULL,
  `type` varchar(200) default NULL,
  `url` varchar(250) default NULL,
  PRIMARY KEY (`watchId`),
  INDEX `event-object-group` ( `event` , `object` ( 100 ) , `group` ( 50 ) ) 
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_history`;
CREATE TABLE `tiki_history` (
  `historyId` int(12) NOT NULL auto_increment,
  `pageName` varchar(160) NOT NULL default '',
  `version` int(8) NOT NULL default '0',
  `version_minor` int(8) NOT NULL default '0',
  `lastModif` int(14) default NULL,
  `description` varchar(200) default NULL,
  `user` varchar(200) not null default '',
  `ip` varchar(15) default NULL,
  `comment` varchar(200) default NULL,
  `data` longblob,
  `type` varchar(50) default NULL,
  `is_html` TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`pageName`,`version`),
  KEY `user` (`user`),
  KEY (`historyId`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_hotwords`;
CREATE TABLE `tiki_hotwords` (
  `word` varchar(40) NOT NULL default '',
  `url` varchar(255) NOT NULL default '',
  PRIMARY KEY (`word`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_html_pages`;
CREATE TABLE `tiki_html_pages` (
  `pageName` varchar(200) NOT NULL default '',
  `content` longblob,
  `refresh` int(10) default NULL,
  `type` char(1) default NULL,
  `created` int(14) default NULL,
  PRIMARY KEY (`pageName`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_html_pages_dynamic_zones`;
CREATE TABLE `tiki_html_pages_dynamic_zones` (
  `pageName` varchar(40) NOT NULL default '',
  `zone` varchar(80) NOT NULL default '',
  `type` char(2) default NULL,
  `content` text,
  PRIMARY KEY (`pageName`,`zone`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_images`;
CREATE TABLE `tiki_images` (
  `imageId` int(14) NOT NULL auto_increment,
  `galleryId` int(14) NOT NULL default '0',
  `name` varchar(200) NOT NULL default '',
  `description` text,
  `lon` float default NULL,
  `lat` float default NULL,
  `created` int(14) default NULL,
  `user` varchar(200) default '',
  `hits` int(14) default NULL,
  `path` varchar(255) default NULL,
  PRIMARY KEY (`imageId`),
  KEY `name` (name),
  KEY `description` (description(255)),
  KEY `hits` (hits),
  KEY `ti_gId` (`galleryId`),
  KEY `ti_cr` (created),
  KEY `ti_us` (user),
  FULLTEXT KEY `ft` (name,description)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_images_data`;
CREATE TABLE `tiki_images_data` (
  `imageId` int(14) NOT NULL default '0',
  `xsize` int(8) NOT NULL default '0',
  `ysize` int(8) NOT NULL default '0',
  `type` char(1) NOT NULL default '',
  `filesize` int(14) default NULL,
  `filetype` varchar(80) default NULL,
  `filename` varchar(80) default NULL,
  `data` longblob,
  `etag` varchar(32) default NULL,
  PRIMARY KEY (`imageId`,`xsize`,`ysize`,`type`),
  KEY `t_i_d_it` (`imageId`,type)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_language`;
CREATE TABLE `tiki_language` (
  `id` int(14) NOT NULL auto_increment,
  `source` text NOT NULL,
  `lang` char(16) NOT NULL default '',
  `tran` text,
  `changed` bool,
  `userId` int(8),
  `lastModif` int(14) NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_link_cache`;
CREATE TABLE `tiki_link_cache` (
  `cacheId` int(14) NOT NULL auto_increment,
  `url` varchar(250) default NULL,
  `data` longblob,
  `refresh` int(14) default NULL,
  PRIMARY KEY (`cacheId`),
  KEY `url` (url)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;
CREATE INDEX urlindex ON tiki_link_cache (url(250));

DROP TABLE IF EXISTS `tiki_links`;
CREATE TABLE `tiki_links` (
  `fromPage` varchar(160) NOT NULL default '',
  `toPage` varchar(160) NOT NULL default '',
  PRIMARY KEY (`fromPage`,`toPage`),
  KEY `toPage` (`toPage`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_live_support_events`;
CREATE TABLE `tiki_live_support_events` (
  `eventId` int(14) NOT NULL auto_increment,
  `reqId` varchar(32) NOT NULL default '',
  `type` varchar(40) default NULL,
  `seqId` int(14) default NULL,
  `senderId` varchar(32) default NULL,
  `data` text,
  `timestamp` int(14) default NULL,
  PRIMARY KEY (`eventId`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_live_support_message_comments`;
CREATE TABLE `tiki_live_support_message_comments` (
  `cId` int(12) NOT NULL auto_increment,
  `msgId` int(12) default NULL,
  `data` text,
  `timestamp` int(14) default NULL,
  PRIMARY KEY (`cId`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_live_support_messages`;
CREATE TABLE `tiki_live_support_messages` (
  `msgId` int(12) NOT NULL auto_increment,
  `data` text,
  `timestamp` int(14) default NULL,
  `user` varchar(200) not null default '',
  `username` varchar(200) default NULL,
  `priority` int(2) default NULL,
  `status` char(1) default NULL,
  `assigned_to` varchar(200) default NULL,
  `resolution` varchar(100) default NULL,
  `title` varchar(200) default NULL,
  `module` int(4) default NULL,
  `email` varchar(250) default NULL,
  PRIMARY KEY (`msgId`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_live_support_modules`;
CREATE TABLE `tiki_live_support_modules` (
  `modId` int(4) NOT NULL auto_increment,
  `name` varchar(90) default NULL,
  PRIMARY KEY (`modId`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

INSERT INTO tiki_live_support_modules(name) VALUES('wiki');
INSERT INTO tiki_live_support_modules(name) VALUES('forums');
INSERT INTO tiki_live_support_modules(name) VALUES('image galleries');
INSERT INTO tiki_live_support_modules(name) VALUES('file galleries');
INSERT INTO tiki_live_support_modules(name) VALUES('directory');

DROP TABLE IF EXISTS `tiki_live_support_operators`;
CREATE TABLE `tiki_live_support_operators` (
  `user` varchar(200) NOT NULL default '',
  `accepted_requests` int(10) default NULL,
  `status` varchar(20) default NULL,
  `longest_chat` int(10) default NULL,
  `shortest_chat` int(10) default NULL,
  `average_chat` int(10) default NULL,
  `last_chat` int(14) default NULL,
  `time_online` int(10) default NULL,
  `votes` int(10) default NULL,
  `points` int(10) default NULL,
  `status_since` int(14) default NULL,
  PRIMARY KEY (`user`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_live_support_requests`;
CREATE TABLE `tiki_live_support_requests` (
  `reqId` varchar(32) NOT NULL default '',
  `user` varchar(200) NOT NULL default '',
  `tiki_user` varchar(200) default NULL,
  `email` varchar(200) default NULL,
  `operator` varchar(200) default NULL,
  `operator_id` varchar(32) default NULL,
  `user_id` varchar(32) default NULL,
  `reason` text,
  `req_timestamp` int(14) default NULL,
  `timestamp` int(14) default NULL,
  `status` varchar(40) default NULL,
  `resolution` varchar(40) default NULL,
  `chat_started` int(14) default NULL,
  `chat_ended` int(14) default NULL,
  PRIMARY KEY (`reqId`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_logs`;
CREATE TABLE `tiki_logs` (
  `logId` int(8) NOT NULL auto_increment,
  `logtype` varchar(20) NOT NULL,
  `logmessage` text NOT NULL,
  `loguser` varchar(40) NOT NULL,
  `logip` varchar(200),
  `logclient` text NOT NULL,
  `logtime` int(14) NOT NULL,
  PRIMARY KEY (`logId`),
  KEY `logtype` (logtype)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_mail_events`;
CREATE TABLE `tiki_mail_events` (
  `event` varchar(200) default NULL,
  `object` varchar(200) default NULL,
  `email` varchar(200) default NULL
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_mailin_accounts`;
CREATE TABLE `tiki_mailin_accounts` (
  `accountId` int(12) NOT NULL auto_increment,
  `user` varchar(200) NOT NULL default '',
  `account` varchar(50) NOT NULL default '',
  `pop` varchar(255) default NULL,
  `port` int(4) default NULL,
  `username` varchar(100) default NULL,
  `pass` varchar(100) default NULL,
  `active` char(1) default NULL,
  `type` varchar(40) default NULL,
  `smtp` varchar(255) default NULL,
  `useAuth` char(1) default NULL,
  `smtpPort` int(4) default NULL,
  `anonymous` char(1) NOT NULL default 'y',
  `attachments` char(1) NOT NULL default 'n',
  `article_topicId` int(4) default NULL,
  `article_type` varchar(50) default NULL,
  `discard_after` varchar(255) default NULL,
  PRIMARY KEY (`accountId`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_menu_languages`;
CREATE TABLE `tiki_menu_languages` (
  `menuId` int(8) NOT NULL auto_increment,
  `language` char(16) NOT NULL default '',
  PRIMARY KEY (`menuId`,`language`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_menu_options`;
CREATE TABLE `tiki_menu_options` (
  `optionId` int(8) NOT NULL auto_increment,
  `menuId` int(8) default NULL,
  `type` char(1) default NULL,
  `name` varchar(200) default NULL,
  `url` varchar(255) default NULL,
  `position` int(4) default NULL,
  `section` text default NULL,
  `perm` text default NULL,
  `groupname` text default NULL,
  `userlevel` int(4) default 0,
  `icon` varchar(200),
  PRIMARY KEY (`optionId`),
  UNIQUE KEY `uniq_menu` (`menuId`,`name`(30),`url`(50),`position`,`section`(60),`perm`(50),`groupname`(50))
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

-- when adding new inserts, order commands by position
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Home','./',10,'','','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Search','tiki-searchresults.php',13,'feature_search_fulltext','tiki_p_search','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Search','tiki-searchindex.php',13,'feature_search','tiki_p_search','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Contact Us','tiki-contact.php',20,'feature_contact,feature_messages','','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Stats','tiki-stats.php',23,'feature_stats','tiki_p_view_stats','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Categories','tiki-browse_categories.php',25,'feature_categories','tiki_p_view_category','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Freetags','tiki-browse_freetags.php',27,'feature_freetags','tiki_p_view_freetags','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Calendar','tiki-calendar.php',35,'feature_calendar','tiki_p_view_calendar','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Users Map','tiki-gmap_usermap.php',36,'feature_gmap','','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Tiki Calendar','tiki-action_calendar.php',37,'feature_action_calendar','tiki_p_view_tiki_calendar','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','(debug)','javascript:toggle(\'debugconsole\')',40,'feature_debug_console','tiki_p_admin','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'s','MyTiki','tiki-my_tiki.php',50,'feature_mytiki','','Registered',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','MyTiki Home','tiki-my_tiki.php',51,'feature_mytiki','','Registered',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Preferences','tiki-user_preferences.php',55,'feature_mytiki,feature_userPreferences','','Registered',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Messages','messu-mailbox.php',60,'feature_mytiki,feature_messages','tiki_p_messages','Registered',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Tasks','tiki-user_tasks.php',65,'feature_mytiki,feature_tasks','tiki_p_tasks','Registered',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Bookmarks','tiki-user_bookmarks.php',70,'feature_mytiki,feature_user_bookmarks','tiki_p_create_bookmarks','Registered',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Modules','tiki-user_assigned_modules.php',75,'feature_mytiki,user_assigned_modules','tiki_p_configure_modules','Registered',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Webmail','tiki-webmail.php',85,'feature_mytiki,feature_webmail','tiki_p_use_webmail','Registered',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Contacts','tiki-contacts.php',87,'feature_mytiki,feature_contacts','','Registered',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Notepad','tiki-notepad_list.php',90,'feature_mytiki,feature_notepad','tiki_p_notepad','Registered',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','My Files','tiki-userfiles.php',95,'feature_mytiki,feature_userfiles','tiki_p_userfiles','Registered',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','User Menu','tiki-usermenu.php',100,'feature_mytiki,feature_usermenu','tiki_p_usermenu','Registered',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Mini Calendar','tiki-minical.php',105,'feature_mytiki,feature_minical','tiki_p_minical','Registered',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','My Watches','tiki-user_watches.php',110,'feature_mytiki,feature_user_watches','','Registered',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'s','Community','tiki-list_users.php',187,'feature_friends','tiki_p_list_users','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','User List','tiki-list_users.php',188,'feature_friends','tiki_p_list_users','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Friendship Network','tiki-friends.php',189,'feature_friends','','Registered',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'s','Wiki','tiki-index.php',200,'feature_wiki','tiki_p_view','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Wiki Home','tiki-index.php',202,'feature_wiki','tiki_p_view','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Last Changes','tiki-lastchanges.php',205,'feature_wiki,feature_lastChanges','tiki_p_view','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Dump','dump/new.tar',210,'feature_wiki,feature_dump','tiki_p_view','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Rankings','tiki-wiki_rankings.php',215,'feature_wiki,feature_wiki_rankings','tiki_p_view','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','List Pages','tiki-listpages.php',220,'feature_wiki,feature_listPages','tiki_p_view','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Create a Wiki Page','tiki-listpages.php?cookietab=2#tab2',222,'feature_wiki,feature_listPages','tiki_p_view,tiki_p_edit','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Orphan Pages','tiki-orphan_pages.php',225,'feature_wiki,feature_listorphanPages','tiki_p_view','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Sandbox','tiki-editpage.php?page=sandbox',230,'feature_wiki,feature_sandbox','tiki_p_view','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Multiple Print','tiki-print_pages.php',235,'feature_wiki,feature_wiki_multiprint','tiki_p_view','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Send Pages','tiki-send_objects.php',240,'feature_wiki,feature_comm','tiki_p_view,tiki_p_send_pages','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Received Pages','tiki-received_pages.php',245,'feature_wiki,feature_comm','tiki_p_view,tiki_p_admin_received_pages','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Structures','tiki-admin_structures.php',250,'feature_wiki,feature_wiki_structure','tiki_p_view','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Mind Map','tiki-mindmap.php',255,'feature_wiki_mindmap','tiki_p_view','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'s','Image Galleries','tiki-galleries.php',300,'feature_galleries','tiki_p_view_image_gallery','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Galleries','tiki-galleries.php',305,'feature_galleries','tiki_p_list_image_galleries','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Rankings','tiki-galleries_rankings.php',310,'feature_galleries,feature_gal_rankings','tiki_p_list_image_galleries','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Upload Image','tiki-upload_image.php',315,'feature_galleries','tiki_p_upload_images','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Directory Batch','tiki-batch_upload.php',318,'feature_galleries,feature_gal_batch','tiki_p_batch_upload','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','System Gallery','tiki-list_gallery.php?galleryId=0',320,'feature_galleries','tiki_p_admin_galleries','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'s','Articles','tiki-view_articles.php',350,'feature_articles','tiki_p_read_article','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'s','Articles','tiki-view_articles.php',350,'feature_articles','tiki_p_articles_read_heading','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Articles Home','tiki-view_articles.php',355,'feature_articles','tiki_p_read_article','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Articles Home','tiki-view_articles.php',355,'feature_articles','tiki_p_articles_read_heading','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','List Articles','tiki-list_articles.php',360,'feature_articles','tiki_p_read_article','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','List Articles','tiki-list_articles.php',360,'feature_articles','tiki_p_articles_read_heading','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Rankings','tiki-cms_rankings.php',365,'feature_articles,feature_cms_rankings','tiki_p_read_article','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Submit Article','tiki-edit_submission.php',370,'feature_articles,feature_submissions','tiki_p_read_article,tiki_p_submit_article','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','View submissions','tiki-list_submissions.php',375,'feature_articles,feature_submissions','tiki_p_read_article,tiki_p_submit_article','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','View submissions','tiki-list_submissions.php',375,'feature_articles,feature_submissions','tiki_p_read_article,tiki_p_approve_submission','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','View Submissions','tiki-list_submissions.php',375,'feature_articles,feature_submissions','tiki_p_read_article,tiki_p_remove_submission','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','New Article','tiki-edit_article.php',380,'feature_articles','tiki_p_read_article,tiki_p_edit_article','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Send Articles','tiki-send_objects.php',385,'feature_articles,feature_comm','tiki_p_read_article,tiki_p_send_articles','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Received Articles','tiki-received_articles.php',385,'feature_articles,feature_comm','tiki_p_read_article,tiki_p_admin_received_articles','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Admin Types','tiki-article_types.php',395,'feature_articles','tiki_p_articles_admin_types','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Admin Topics','tiki-admin_topics.php',390,'feature_articles','tiki_p_articles_admin_topics','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'s','Blogs','tiki-list_blogs.php',450,'feature_blogs','tiki_p_read_blog','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','List Blogs','tiki-list_blogs.php',455,'feature_blogs','tiki_p_read_blog','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Rankings','tiki-blog_rankings.php',460,'feature_blogs,feature_blog_rankings','tiki_p_read_blog','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Create Blog','tiki-edit_blog.php',465,'feature_blogs','tiki_p_read_blog,tiki_p_create_blogs','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','New Blog Post','tiki-blog_post.php',470,'feature_blogs','tiki_p_read_blog,tiki_p_blog_post','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','List Blog Posts','tiki-list_posts.php',475,'feature_blogs','tiki_p_read_blog,tiki_p_blog_admin','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'s','Forums','tiki-forums.php',500,'feature_forums','tiki_p_forum_read','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','List Forums','tiki-forums.php',505,'feature_forums','tiki_p_forum_read','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Rankings','tiki-forum_rankings.php',510,'feature_forums,feature_forum_rankings','tiki_p_forum_read','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Admin Forums','tiki-admin_forums.php',515,'feature_forums','tiki_p_forum_read,tiki_p_admin_forum','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'s','Directory','tiki-directory_browse.php',550,'feature_directory','tiki_p_view_directory','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Submit a new link','tiki-directory_add_site.php',555,'feature_directory','tiki_p_submit_link','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Browse Directory','tiki-directory_browse.php',560,'feature_directory','tiki_p_view_directory','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Admin Directory','tiki-directory_admin.php',565,'feature_directory','tiki_p_view_directory,tiki_p_admin_directory_cats','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Admin Directory','tiki-directory_admin.php',565,'feature_directory','tiki_p_view_directory,tiki_p_admin_directory_sites','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Admin Directory','tiki-directory_admin.php',565,'feature_directory','tiki_p_view_directory,tiki_p_validate_links','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'s','File Galleries','tiki-list_file_gallery.php',600,'feature_file_galleries','tiki_p_list_file_galleries|tiki_p_view_file_gallery|tiki_p_upload_files','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','List Galleries','tiki-list_file_gallery.php',605,'feature_file_galleries','tiki_p_list_file_galleries','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Rankings','tiki-file_galleries_rankings.php',610,'feature_file_galleries,feature_file_galleries_rankings','tiki_p_list_file_galleries','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Upload File','tiki-upload_file.php',615,'feature_file_galleries','tiki_p_upload_files','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Directory batch','tiki-batch_upload_files.php',617,'feature_file_galleries_batch','tiki_p_batch_upload_file_dir','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'s','FAQs','tiki-list_faqs.php',650,'feature_faqs','tiki_p_view_faqs','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','List FAQs','tiki-list_faqs.php',665,'feature_faqs','tiki_p_view_faqs','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Admin FAQs','tiki-list_faqs.php',660,'feature_faqs','tiki_p_admin_faqs','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'s','Maps','tiki-map.php',700,'feature_maps','tiki_p_map_view','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Mapfiles','tiki-map_edit.php',705,'feature_maps','tiki_p_map_view','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Layer Management','tiki-map_upload.php',710,'feature_maps','tiki_p_map_edit','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'s','Quizzes','tiki-list_quizzes.php',750,'feature_quizzes','tiki_p_take_quiz','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','List Quizzes','tiki-list_quizzes.php',755,'feature_quizzes','tiki_p_take_quiz','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Quiz Stats','tiki-quiz_stats.php',760,'feature_quizzes','tiki_p_view_quiz_stats','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Admin Quizzes','tiki-edit_quiz.php',765,'feature_quizzes','tiki_p_admin_quizzes','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'s','Spreadsheets','tiki-sheets.php',780,'feature_sheet','tiki_p_view_sheet','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','List Sheets','tiki-sheets.php',782,'feature_sheet','tiki_p_view_sheet','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'s','Trackers','tiki-list_trackers.php',800,'feature_trackers','tiki_p_list_trackers','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','List Trackers','tiki-list_trackers.php',805,'feature_trackers','tiki_p_list_trackers','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Admin Trackers','tiki-admin_trackers.php',810,'feature_trackers','tiki_p_admin_trackers','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'s','Surveys','tiki-list_surveys.php',850,'feature_surveys','tiki_p_take_survey','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','List Surveys','tiki-list_surveys.php',855,'feature_surveys','tiki_p_take_survey','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Stats','tiki-survey_stats.php',860,'feature_surveys','tiki_p_view_survey_stats','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Admin Surveys','tiki-admin_surveys.php',865,'feature_surveys','tiki_p_admin_surveys','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'s','Newsletters','tiki-newsletters.php',900,'feature_newsletters','tiki_p_subscribe_newsletters','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'s','Newsletters','tiki-newsletters.php',900,'feature_newsletters','tiki_p_send_newsletters','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'s','Newsletters','tiki-newsletters.php',900,'feature_newsletters','tiki_p_admin_newsletters','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'s','Newsletters','tiki-newsletters.php',900,'feature_newsletters','tiki_p_list_newsletters','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Send Newsletters','tiki-send_newsletters.php',905,'feature_newsletters','tiki_p_send_newsletters','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Admin Newsletters','tiki-admin_newsletters.php',910,'feature_newsletters','tiki_p_admin_newsletters','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'r','Admin','tiki-admin.php',1050,'','tiki_p_admin','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'r','Admin','tiki-admin.php',1050,'','tiki_p_admin_categories','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'r','Admin','tiki-admin.php',1050,'','tiki_p_admin_banners','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'r','Admin','tiki-admin.php',1050,'','tiki_p_edit_templates','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'r','Admin','tiki-admin.php',1050,'','tiki_p_edit_cookies','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'r','Admin','tiki-admin.php',1050,'','tiki_p_admin_dynamic','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'r','Admin','tiki-admin.php',1050,'','tiki_p_admin_mailin','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'r','Admin','tiki-admin.php',1050,'','tiki_p_edit_content_templates','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'r','Admin','tiki-admin.php',1050,'','tiki_p_edit_html_pages','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'r','Admin','tiki-admin.php',1050,'','tiki_p_view_referer_stats','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'r','Admin','tiki-admin.php',1050,'','tiki_p_admin_shoutbox','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'r','Admin','tiki-admin.php',1050,'','tiki_p_live_support_admin','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'r','Admin','tiki-admin.php',1050,'','user_is_operator','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'r','Admin','tiki-admin.php',1050,'feature_integrator','tiki_p_admin_integrator','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'r','Admin','tiki-admin.php',1050,'feature_edit_templates','tiki_p_edit_templates','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'r','Admin','tiki-admin.php',1050,'feature_view_tpl','tiki_p_edit_templates','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'r','Admin','tiki-admin.php',1050,'feature_editcss','tiki_p_create_css','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'r','Admin','tiki-admin.php',1050,'','tiki_p_admin_contribution','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'r','Admin','tiki-admin.php',1050,'','tiki_p_admin_users','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'r','Admin','tiki-admin.php',1050,'','tiki_p_admin_toolbars','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'r','Admin','tiki-admin.php',1050,'','tiki_p_edit_menu','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'r','Admin','tiki-admin.php',1050,'','tiki_p_clean_cache','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'r','Admin','tiki-admin.php',1050,'','tiki_p_admin_modules','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Admin Home','tiki-admin.php',1051,'','tiki_p_admin','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Live Support','tiki-live_support_admin.php',1055,'feature_live_support','tiki_p_live_support_admin','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Live Support','tiki-live_support_admin.php',1055,'feature_live_support','user_is_operator','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Banning','tiki-admin_banning.php',1060,'feature_banning','tiki_p_admin_banning','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Calendar','tiki-admin_calendars.php',1065,'feature_calendar','tiki_p_admin_calendar','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Users','tiki-adminusers.php',1070,'','tiki_p_admin_users','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Groups','tiki-admingroups.php',1075,'','tiki_p_admin','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','External Pages Cache','tiki-list_cache.php',1080,'cachepages','tiki_p_admin','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Modules','tiki-admin_modules.php',1085,'','tiki_p_admin_modules','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Hotwords','tiki-admin_hotwords.php',1095,'feature_hotwords','tiki_p_admin','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Edit languages','tiki-edit_languages.php',1098,'lang_use_db','tiki_p_edit_languages','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','External Feeds','tiki-admin_rssmodules.php',1100,'','tiki_p_admin_rssmodules','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Menus','tiki-admin_menus.php',1105,'','tiki_p_edit_menu','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Polls','tiki-admin_polls.php',1110,'feature_polls','tiki_p_admin_polls','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Mail Notifications','tiki-admin_notifications.php',1120,'','tiki_p_admin_notifications','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Search Stats','tiki-search_stats.php',1125,'feature_search_stats','tiki_p_admin','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Theme Control','tiki-theme_control.php',1130,'feature_theme_control','tiki_p_admin','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Toolbars','tiki-admin_toolbars.php',1135,'','tiki_p_admin_toolbars','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Transitions','tiki-admin_transitions.php',1140,'','tiki_p_admin','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Categories','tiki-admin_categories.php',1145,'feature_categories','tiki_p_admin_categories','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Banners','tiki-list_banners.php',1150,'feature_banners','tiki_p_admin_banners','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Edit Templates','tiki-edit_templates.php',1155,'feature_edit_templates','tiki_p_edit_templates','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','View Templates','tiki-edit_templates.php',1155,'feature_view_tpl','tiki_p_edit_templates','',2);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Edit CSS','tiki-edit_css.php',1158,'feature_editcss','tiki_p_create_css','',2);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Dynamic content','tiki-list_contents.php',1165,'feature_dynamic_content','tiki_p_admin_dynamic','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Mail-in','tiki-admin_mailin.php',1175,'feature_mailin','tiki_p_admin_mailin','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','HTML Pages','tiki-admin_html_pages.php',1185,'feature_html_pages','tiki_p_edit_html_pages','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Shoutbox','tiki-shoutbox.php',1190,'feature_shoutbox','tiki_p_admin_shoutbox','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Shoutbox Words','tiki-admin_shoutbox_words.php',1191,'feature_shoutbox','tiki_p_admin_shoutbox','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Referer Stats','tiki-referer_stats.php',1195,'feature_referer_stats','tiki_p_view_referer_stats','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Integrator','tiki-admin_integrator.php',1205,'feature_integrator','tiki_p_admin_integrator','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','phpinfo','tiki-phpinfo.php',1215,'','tiki_p_admin','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Tiki Cache/Sys Admin','tiki-admin_system.php',1230,'','tiki_p_clean_cache','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Tiki Importer','tiki-importer.php',1240,'','tiki_p_admin_importer','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Tiki Logs','tiki-syslog.php',1245,'','tiki_p_admin','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Security Admin','tiki-admin_security.php',1250,'','tiki_p_admin','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Action Log','tiki-admin_actionlog.php',1255,'feature_actionlog','tiki_p_admin','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Action Log','tiki-admin_actionlog.php',1255,'feature_actionlog','tiki_p_view_actionlog','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Action Log','tiki-admin_actionlog.php',1255,'feature_actionlog','tiki_p_view_actionlog_owngroups','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Content Templates','tiki-admin_content_templates.php',1256,'feature_wiki_templates','tiki_p_edit_content_templates','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Comments','tiki-list_comments.php',1260,'feature_wiki_comments','tiki_p_admin','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Comments','tiki-list_comments.php',1260,'feature_article_comments','tiki_p_admin','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Comments','tiki-list_comments.php',1260,'feature_file_galleries_comments','tiki_p_admin','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Comments','tiki-list_comments.php',1260,'feature_image_galleries_comments','tiki_p_admin','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Comments','tiki-list_comments.php',1260,'feature_poll_comments','tiki_p_admin','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Comments','tiki-list_comments.php',1260,'feature_faq_comments','tiki_p_admin','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Contribution','tiki-admin_contribution.php',1265,'feature_contribution','tiki_p_admin_contribution','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'s', 'Kaltura Video', 'tiki-list_kaltura_entries.php', 950, 'feature_kaltura', 'tiki_p_admin | tiki_p_admin_kaltura | tiki_p_list_videos', '', 0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o', 'List Entries', 'tiki-list_kaltura_entries.php', 952, 'feature_kaltura', 'tiki_p_admin | tiki_p_admin_kaltura | tiki_p_list_videos', '', 0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o', 'Upload Media', 'tiki-kaltura_upload.php', 954, 'feature_kaltura', 'tiki_p_admin | tiki_p_admin_kaltura | tiki_p_upload_videos', '', 0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Permissions','tiki-objectpermissions.php',1077,'','tiki_p_admin|tiki_p_admin_objects','',0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Social networks','tiki-socialnetworks.php',115,'feature_mytiki,feature_socialnetworks','tiki_p_socialnetworks|tiki_p_admin_socialnetworks','Registered',0);


DROP TABLE IF EXISTS `tiki_menus`;
CREATE TABLE `tiki_menus` (
  `menuId` int(8) NOT NULL auto_increment,
  `name` varchar(200) NOT NULL default '',
  `description` text,
  `type` char(1) default NULL,
  `icon` varchar(200) default NULL,
  `use_items_icons` char(1) NOT NULL DEFAULT 'n',
  PRIMARY KEY (`menuId`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

INSERT INTO tiki_menus (`menuId`,name,description,type) VALUES ('42','Application menu','Main extensive navigation menu','d');

DROP TABLE IF EXISTS `tiki_minical_events`;
CREATE TABLE `tiki_minical_events` (
  `user` varchar(200) default '',
  `eventId` int(12) NOT NULL auto_increment,
  `title` varchar(250) default NULL,
  `description` text,
  `start` int(14) default NULL,
  `end` int(14) default NULL,
  `security` char(1) default NULL,
  `duration` int(3) default NULL,
  `topicId` int(12) default NULL,
  `reminded` char(1) default NULL,
  PRIMARY KEY (`eventId`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_minical_topics`;
CREATE TABLE `tiki_minical_topics` (
  `user` varchar(200) default '',
  `topicId` int(12) NOT NULL auto_increment,
  `name` varchar(250) default NULL,
  `filename` varchar(200) default NULL,
  `filetype` varchar(200) default NULL,
  `filesize` varchar(200) default NULL,
  `data` longblob,
  `path` varchar(250) default NULL,
  `isIcon` char(1) default NULL,
  PRIMARY KEY (`topicId`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_modules`;
CREATE TABLE `tiki_modules` (
  `moduleId` int(8) NOT NULL auto_increment,
  `name` varchar(200) NOT NULL default '',
  `position` char(1) NOT NULL DEFAULT '',
  `ord` int(4) NOT NULL DEFAULT '0',
  `type` char(1) default NULL,
  `title` varchar(255) default NULL,
  `cache_time` int(14) default NULL,
  `rows` int(4) default NULL,
  `params` text,
  `groups` text,
  PRIMARY KEY (`moduleId`),
  KEY `positionType` (position, type),
  KEY `namePosOrdParam` (`name`(100), `position`, `ord`, `params`(140))
) ENGINE=MyISAM;

INSERT INTO `tiki_modules` (name,position,ord,cache_time,params,groups) VALUES
    ('menu','l',1,7200,'id=42&flip=y','a:1:{i:0;s:10:"Registered";}'),
    ('logo','t',1,7200,'nobox=y','a:0:{}'),
    ('login_box','t',2,0,'mode=popup&nobox=y','a:0:{}'),
    ('quickadmin','t',3,7200,'nobox=y','a:1:{i:0;s:6:"Admins";}'),
    ('rsslist','b',1,7200,'nobox=y','a:0:{}'),
    ('poweredby','b',2,7200,'nobox=y&icons=n&version=n','a:0:{}');

DROP TABLE IF EXISTS `tiki_newsletter_subscriptions`;
CREATE TABLE `tiki_newsletter_subscriptions` (
  `nlId` int(12) NOT NULL default '0',
  `email` varchar(255) NOT NULL default '',
  `code` varchar(32) default NULL,
  `valid` char(1) default NULL,
  `subscribed` int(14) default NULL,
  `isUser` char(1) NOT NULL default 'n',
  `included` char(1) NOT NULL default 'n',
  PRIMARY KEY (`nlId`,`email`,`isUser`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_newsletter_groups`;
CREATE TABLE `tiki_newsletter_groups` (
  `nlId` int(12) NOT NULL default '0',
  `groupName` varchar(255) NOT NULL default '',
  `code` varchar(32) default NULL,
  PRIMARY KEY (`nlId`,`groupName`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_newsletter_included`;
CREATE TABLE `tiki_newsletter_included` (
  `nlId` int(12) NOT NULL default '0',
  `includedId` int(12) NOT NULL default '0',
  PRIMARY KEY (`nlId`,`includedId`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_newsletter_pages`;
CREATE TABLE `tiki_newsletter_pages` (
    `nlId` INT( 12 ) NOT NULL ,
    `wikiPageName` VARCHAR( 160 ) NOT NULL ,
    `validateAddrs` CHAR( 1 ) NOT NULL DEFAULT 'n',
    `addToList` CHAR( 1 ) NOT NULL DEFAULT 'n',
    PRIMARY KEY ( `nlId` , `wikiPageName` )
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_newsletters`;
CREATE TABLE `tiki_newsletters` (
  `nlId` int(12) NOT NULL auto_increment,
  `name` varchar(200) default NULL,
  `description` text,
  `created` int(14) default NULL,
  `lastSent` int(14) default NULL,
  `editions` int(10) default NULL,
  `users` int(10) default NULL,
  `allowUserSub` char(1) default 'y',
  `allowAnySub` char(1) default NULL,
  `unsubMsg` char(1) default 'y',
  `validateAddr` char(1) default 'y',
  `frequency` int(14) default NULL,
  `allowTxt` char(1) default 'y',
  `author` varchar(200) default NULL,
  `allowArticleClip` char(1) default 'y',
  `autoArticleClip` char(1) default 'n',
  `articleClipTypes` text,
  `articleClipRange` int(14) default NULL,
  PRIMARY KEY (`nlId`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_page_footnotes`;
CREATE TABLE `tiki_page_footnotes` (
  `user` varchar(200) NOT NULL default '',
  `pageName` varchar(250) NOT NULL default '',
  `data` text,
  PRIMARY KEY (`user`(150),`pageName`(100))
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_pages`;
CREATE TABLE `tiki_pages` (
  `page_id` int(14) NOT NULL auto_increment,
  `pageName` varchar(160) NOT NULL default '',
  `hits` int(8) default NULL,
  `data` mediumtext,
  `description` varchar(200) default NULL,
  `lastModif` int(14) default NULL,
  `comment` varchar(200) default NULL,
  `version` int(8) NOT NULL default '0',
  `version_minor` int(8) NOT NULL default '0',
  `user` varchar(200) default '',
  `ip` varchar(15) default NULL,
  `flag` char(1) default NULL,
  `points` int(8) default NULL,
  `votes` int(8) default NULL,
  `cache` longtext,
  `wiki_cache` int(10) default NULL,
  `cache_timestamp` int(14) default NULL,
  `pageRank` decimal(4,3) default NULL,
  `creator` varchar(200) default NULL,
  `page_size` int(10) unsigned default '0',
  `lang` varchar(16) default NULL,
  `lockedby` varchar(200) default NULL,
  `is_html` tinyint(1) default 0,
  `created` int(14),
  `wysiwyg` char(1) default NULL,
  `wiki_authors_style` varchar(20) default '',
  `comments_enabled` char(1) default NULL,
  `keywords` TEXT, 
  PRIMARY KEY (`page_id`),
  UNIQUE KEY `pageName` (`pageName`),
  KEY `data` (`data`(255)),
  KEY `pageRank` (`pageRank`),
  FULLTEXT KEY `ft` (`pageName`,`description`,`data`),
  KEY `lastModif`(`lastModif`)
) ENGINE=MyISAM AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `tiki_pageviews`;
CREATE TABLE `tiki_pageviews` (
  `day` int(14) NOT NULL default '0',
  `pageviews` int(14) default NULL,
  PRIMARY KEY (`day`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_poll_objects`;
CREATE TABLE `tiki_poll_objects` (
  `catObjectId` int(11) NOT NULL default '0',
  `pollId` int(11) NOT NULL default '0',
  `title` varchar(255) default NULL,
  PRIMARY KEY (`catObjectId`,`pollId`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_poll_options`;
CREATE TABLE `tiki_poll_options` (
  `pollId` int(8) NOT NULL default '0',
  `optionId` int(8) NOT NULL auto_increment,
  `title` varchar(200) default NULL,
  `position` int(4) NOT NULL default '0',
  `votes` int(8) default NULL,
  PRIMARY KEY (`optionId`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_polls`;
CREATE TABLE `tiki_polls` (
  `pollId` int(8) NOT NULL auto_increment,
  `title` varchar(200) default NULL,
  `votes` int(8) default NULL,
  `active` char(1) default NULL,
  `publishDate` int(14) default NULL,
  `voteConsiderationSpan` int(4) default 0,
    `anonym` ENUM( 'a', 'u', 'i', 'c' ) NOT NULL DEFAULT 'u',
  PRIMARY KEY (`pollId`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;
ALTER TABLE tiki_polls ADD INDEX tiki_poll_lookup ( active , title );

CREATE TABLE IF NOT EXISTS `tiki_poll_votes` (
  `pollId` int(11) NOT NULL,
  `optionId` int(11) NOT NULL,
  `voteId` int(11) NOT NULL auto_increment,
  `identification` varchar(300) NOT NULL,
  `time` int(11) NOT NULL,
  PRIMARY KEY  (`voteId`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_preferences`;
CREATE TABLE `tiki_preferences` (
  `name` varchar(40) NOT NULL default '',
  `value` text,
  PRIMARY KEY (`name`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_private_messages`;
CREATE TABLE `tiki_private_messages` (
  `messageId` int(8) NOT NULL auto_increment,
  `toNickname` varchar(200) NOT NULL default '',
  `poster` varchar(200) NOT NULL default 'anonymous',
  `timestamp` int(14) default NULL,
  `received` tinyint(1) not null default 0,
  `message` varchar(255) default NULL,
  PRIMARY KEY (`messageId`),
  KEY (`received`),
  KEY (`timestamp`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_programmed_content`;
CREATE TABLE `tiki_programmed_content` (
  `pId` int(8) NOT NULL auto_increment,
  `contentId` int(8) NOT NULL default '0',
  `content_type` VARCHAR( 20 ) NOT NULL DEFAULT 'static',
  `publishDate` int(14) NOT NULL default '0',
  `data` text,
  PRIMARY KEY (`pId`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_quiz_question_options`;
CREATE TABLE `tiki_quiz_question_options` (
  `optionId` int(10) NOT NULL auto_increment,
  `questionId` int(10) default NULL,
  `optionText` text,
  `points` int(4) default NULL,
  PRIMARY KEY (`optionId`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_quiz_questions`;
CREATE TABLE `tiki_quiz_questions` (
  `questionId` int(10) NOT NULL auto_increment,
  `quizId` int(10) default NULL,
  `question` text,
  `position` int(4) default NULL,
  `type` char(1) default NULL,
  `maxPoints` int(4) default NULL,
  PRIMARY KEY (`questionId`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_quiz_results`;
CREATE TABLE `tiki_quiz_results` (
  `resultId` int(10) NOT NULL auto_increment,
  `quizId` int(10) default NULL,
  `fromPoints` int(4) default NULL,
  `toPoints` int(4) default NULL,
  `answer` text,
  PRIMARY KEY (`resultId`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_quiz_stats`;
CREATE TABLE `tiki_quiz_stats` (
  `quizId` int(10) NOT NULL default '0',
  `questionId` int(10) NOT NULL default '0',
  `optionId` int(10) NOT NULL default '0',
  `votes` int(10) default NULL,
  PRIMARY KEY (`quizId`,`questionId`,`optionId`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_quiz_stats_sum`;
CREATE TABLE `tiki_quiz_stats_sum` (
  `quizId` int(10) NOT NULL default '0',
  `quizName` varchar(255) default NULL,
  `timesTaken` int(10) default NULL,
  `avgpoints` decimal(5,2) default NULL,
  `avgavg` decimal(5,2) default NULL,
  `avgtime` decimal(5,2) default NULL,
  PRIMARY KEY (`quizId`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_quizzes`;
CREATE TABLE `tiki_quizzes` (
  `quizId` int(10) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `description` text,
  `canRepeat` char(1) default NULL,
  `storeResults` char(1) default NULL,
  `questionsPerPage` int(4) default NULL,
  `timeLimited` char(1) default NULL,
  `timeLimit` int(14) default NULL,
  `created` int(14) default NULL,
  `taken` int(10) default NULL,
  `immediateFeedback` char(1) default NULL,
  `showAnswers` char(1) default NULL,
  `shuffleQuestions` char(1) default NULL,
  `shuffleAnswers` char(1) default NULL,
  `publishDate` int(14) default NULL,
  `expireDate` int(14) default NULL,
  `bDeleted` char(1) default NULL,
  `nAuthor` int(4) default NULL,
  `bOnline` char(1) default NULL,
  `bRandomQuestions` char(1) default NULL,
  `nRandomQuestions` tinyint(4) default NULL,
  `bLimitQuestionsPerPage` char(1) default NULL,
  `nLimitQuestionsPerPage` tinyint(4) default NULL,
  `bMultiSession` char(1) default NULL,
  `nCanRepeat` tinyint(4) default NULL,
  `sGradingMethod` varchar(80) default NULL,
  `sShowScore` varchar(80) default NULL,
  `sShowCorrectAnswers` varchar(80) default NULL,
  `sPublishStats` varchar(80) default NULL,
  `bAdditionalQuestions` char(1) default NULL,
  `bForum` char(1) default NULL,
  `sForum` varchar(80) default NULL,
  `sPrologue` text,
  `sData` text,
  `sEpilogue` text,
  `passingperct` int(4) default 0,
  PRIMARY KEY (`quizId`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_received_articles`;
CREATE TABLE `tiki_received_articles` (
  `receivedArticleId` int(14) NOT NULL auto_increment,
  `receivedFromSite` varchar(200) default NULL,
  `receivedFromUser` varchar(200) default NULL,
  `receivedDate` int(14) default NULL,
  `title` varchar(80) default NULL,
  `authorName` varchar(60) default NULL,
  `size` int(12) default NULL,
  `useImage` char(1) default NULL,
  `image_name` varchar(80) default NULL,
  `image_type` varchar(80) default NULL,
  `image_size` int(14) default NULL,
  `image_x` int(4) default NULL,
  `image_y` int(4) default NULL,
  `image_data` longblob,
  `publishDate` int(14) default NULL,
  `expireDate` int(14) default NULL,
  `created` int(14) default NULL,
  `heading` text,
  `body` longblob,
  `hash` varchar(32) default NULL,
  `author` varchar(200) default NULL,
  `type` varchar(50) default NULL,
  `rating` decimal(3,2) default NULL,
  PRIMARY KEY (`receivedArticleId`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_received_pages`;
CREATE TABLE `tiki_received_pages` (
  `receivedPageId` int(14) NOT NULL auto_increment,
  `pageName` varchar(160) NOT NULL default '',
  `data` longblob,
  `description` varchar(200) default NULL,
  `comment` varchar(200) default NULL,
  `receivedFromSite` varchar(200) default NULL,
  `receivedFromUser` varchar(200) default NULL,
  `receivedDate` int(14) default NULL,
  `parent` varchar(255) default NULL,
  `position` tinyint(3) unsigned default NULL,
  `alias` varchar(255) default NULL,
  `structureName` varchar(250) default NULL,
  `parentName` varchar(250) default NULL,
  `page_alias` varchar(250) default '',
  `pos` int(4) default NULL,
  PRIMARY KEY (`receivedPageId`),
  KEY `structureName` (`structureName`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_referer_stats`;
CREATE TABLE `tiki_referer_stats` (
  `referer` varchar(255) NOT NULL default '',
  `hits` int(10) default NULL,
  `last` int(14) default NULL,
  PRIMARY KEY (`referer`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_related_categories`;
CREATE TABLE `tiki_related_categories` (
  `categId` int(10) NOT NULL default '0',
  `relatedTo` int(10) NOT NULL default '0',
  PRIMARY KEY (`categId`,`relatedTo`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_rss_modules`;
CREATE TABLE `tiki_rss_modules` (
  `rssId` int(8) NOT NULL auto_increment,
  `name` varchar(30) NOT NULL default '',
  `description` text,
  `url` varchar(255) NOT NULL default '',
  `refresh` int(8) default NULL,
  `lastUpdated` int(14) default NULL,
  `showTitle` char(1) default 'n',
  `showPubDate` char(1) default 'n',
  `sitetitle` VARCHAR(255),
  `siteurl` VARCHAR(255),
  `actions` TEXT,
  PRIMARY KEY (`rssId`),
  KEY `name` (name)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_rss_feeds`;
CREATE TABLE `tiki_rss_feeds` (
  `name` varchar(60) NOT NULL default '',
  `rssVer` char(1) NOT NULL default '1',
  `refresh` int(8) default '300',
  `lastUpdated` int(14) default NULL,
  `cache` longblob,
  PRIMARY KEY (`name`,`rssVer`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_searchindex`;
CREATE TABLE tiki_searchindex(
  `searchword` varchar(80) NOT NULL default '',
  `location` varchar(80) NOT NULL default '',
  `page` varchar(255) NOT NULL default '',
  `count` int(11) NOT NULL default '1',
  `last_update` int(11) NOT NULL default '0',
  PRIMARY KEY (`searchword`,`location`,`page`(80)),
  KEY `last_update` (last_update),
  KEY `location` (location(50), page(200))
) ENGINE=MyISAM;

-- LRU (last recently used) list for searching parts of words
DROP TABLE IF EXISTS `tiki_searchsyllable`;
CREATE TABLE tiki_searchsyllable(
  `syllable` varchar(80) NOT NULL default '',
  `lastUsed` int(11) NOT NULL default '0',
  `lastUpdated` int(11) NOT NULL default '0',
  PRIMARY KEY (`syllable`),
  KEY `lastUsed` (`lastUsed`)
) ENGINE=MyISAM;

-- searchword caching table for search syllables
DROP TABLE IF EXISTS `tiki_searchwords`;
CREATE TABLE tiki_searchwords(
  `syllable` varchar(80) NOT NULL default '',
  `searchword` varchar(80) NOT NULL default '',
  PRIMARY KEY (`syllable`,`searchword`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_search_stats`;
CREATE TABLE `tiki_search_stats` (
  `term` varchar(50) NOT NULL default '',
  `hits` int(10) default NULL,
  PRIMARY KEY (`term`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_secdb`;
CREATE TABLE tiki_secdb(
  `md5_value` varchar(32) NOT NULL,
  `filename` varchar(250) NOT NULL,
  `tiki_version` varchar(60) NOT NULL,
  `severity` int(4) NOT NULL default '0',
  PRIMARY KEY (`md5_value`,`filename`(100),`tiki_version`),
  KEY `sdb_fn` (filename)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_semaphores`;
CREATE TABLE `tiki_semaphores` (
  `semName` varchar(250) NOT NULL default '',
  `objectType` varchar(20) default 'wiki page',
  `user` varchar(200) NOT NULL default '',
  `timestamp` int(14) default NULL,
  PRIMARY KEY (`semName`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_sent_newsletters`;
CREATE TABLE `tiki_sent_newsletters` (
  `editionId` int(12) NOT NULL auto_increment,
  `nlId` int(12) NOT NULL default '0',
  `users` int(10) default NULL,
  `sent` int(14) default NULL,
  `subject` varchar(200) default NULL,
  `data` longblob,
  `datatxt` longblob,
  `wysiwyg` char(1) default NULL,
  PRIMARY KEY (`editionId`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_sent_newsletters_errors`;
CREATE TABLE `tiki_sent_newsletters_errors` (
  `editionId` int(12),
  `email` varchar(255),
  `login` varchar(40) default '',
  `error` char(1) default '',
  KEY (`editionId`)
) ENGINE=MyISAM ;

DROP TABLE IF EXISTS `tiki_sessions`;
CREATE TABLE `tiki_sessions` (
  `sessionId` varchar(32) NOT NULL default '',
  `user` varchar(200) default '',
  `timestamp` int(14) default NULL,
  `tikihost` varchar(200) default NULL,
  PRIMARY KEY (`sessionId`),
  KEY `user` (user),
  KEY `timestamp` (`timestamp`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_sheet_layout`;
CREATE TABLE `tiki_sheet_layout` (
  `sheetId` int(8) NOT NULL default '0',
  `begin` int(10) NOT NULL default '0',
  `end` int(10) default NULL,
  `headerRow` int(4) NOT NULL default '0',
  `footerRow` int(4) NOT NULL default '0',
  `className` varchar(64) default NULL,
  `parseValues` char( 1 ) NOT NULL default 'n',
  `clonedSheetId` int(8) NULL,
  UNIQUE KEY `sheetId` (`sheetId`, `begin`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_sheet_values`;
CREATE TABLE `tiki_sheet_values` (
  `sheetId` int(8) NOT NULL default '0',
  `begin` int(10) NOT NULL default '0',
  `end` int(10) default NULL,
  `rowIndex` int(4) NOT NULL default '0',
  `columnIndex` int(4) NOT NULL default '0',
  `value` varchar(255) default NULL,
  `calculation` varchar(255) default NULL,
  `width` int(4) NOT NULL default '1',
  `height` int(4) NOT NULL default '1',
  `format` varchar(255) default NULL,
  `user` varchar(200) default '',
  `style` varchar( 255 ) default '',
  `class` varchar( 255 ) default '',
  `clonedSheetId` int(8) NULL,
  UNIQUE KEY `sheetId` (`sheetId`,begin,`rowIndex`,`columnIndex`),
  KEY `sheetId_2` (`sheetId`,`rowIndex`,`columnIndex`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_sheets`;
CREATE TABLE `tiki_sheets` (
  `sheetId` int(8) NOT NULL auto_increment,
  `title` varchar(200) NOT NULL default '',
  `description` text,
  `author` varchar(200) NOT NULL default '',
  `parentSheetId` int(8) NULL,
  `clonedSheetId` int(8) NULL,
  PRIMARY KEY (`sheetId`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_shoutbox`;
CREATE TABLE `tiki_shoutbox` (
  `msgId` int(10) NOT NULL auto_increment,
  `message` varchar(255) default NULL,
  `timestamp` int(14) default NULL,
  `user` varchar(200) NULL default '',
  `hash` varchar(32) default NULL,
  `tweetId` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`msgId`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_shoutbox_words`;
CREATE TABLE `tiki_shoutbox_words` (
  `word` VARCHAR( 40 ) NOT NULL ,
  `qty` INT DEFAULT '0' NOT NULL ,
  PRIMARY KEY (`word`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_structure_versions`;
CREATE TABLE `tiki_structure_versions` (
  `structure_id` int(14) NOT NULL auto_increment,
  `version` int(14) default NULL,
  PRIMARY KEY (`structure_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_structures`;
CREATE TABLE `tiki_structures` (
  `page_ref_id` int(14) NOT NULL auto_increment,
  `structure_id` int(14) NOT NULL,
  `parent_id` int(14) default NULL,
  `page_id` int(14) NOT NULL,
  `page_version` int(8) default NULL,
  `page_alias` varchar(240) NOT NULL default '',
  `pos` int(4) default NULL,
  PRIMARY KEY (`page_ref_id`),
  KEY `pidpaid` (page_id,parent_id),
  KEY `page_id` (page_id)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_submissions`;
CREATE TABLE `tiki_submissions` (
  `subId` int(8) NOT NULL auto_increment,
  `topline` varchar(255) default NULL,
  `title` varchar(255) default NULL,
  `subtitle` varchar(255) default NULL,
  `linkto` varchar(255) default NULL,
  `lang` varchar(16) default NULL,
  `authorName` varchar(60) default NULL,
  `topicId` int(14) default NULL,
  `topicName` varchar(40) default NULL,
  `size` int(12) default NULL,
  `useImage` char(1) default NULL,
  `image_name` varchar(80) default NULL,
  `image_caption` text default NULL,
  `image_type` varchar(80) default NULL,
  `image_size` int(14) default NULL,
  `image_x` int(4) default NULL,
  `image_y` int(4) default NULL,
  `image_data` longblob,
  `publishDate` int(14) default NULL,
  `expireDate` int(14) default NULL,
  `created` int(14) default NULL,
  `bibliographical_references` text,
  `resume` text,
  `heading` text,
  `body` text,
  `hash` varchar(32) default NULL,
  `author` varchar(200) NOT NULL default '',
  `nbreads` int(14) default NULL,
  `votes` int(8) default NULL,
  `points` int(14) default NULL,
  `type` varchar(50) default NULL,
  `rating` decimal(3,2) default NULL,
  `isfloat` char(1) default NULL,
  PRIMARY KEY (`subId`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_suggested_faq_questions`;
CREATE TABLE `tiki_suggested_faq_questions` (
  `sfqId` int(10) NOT NULL auto_increment,
  `faqId` int(10) NOT NULL default '0',
  `question` text,
  `answer` text,
  `created` int(14) default NULL,
  `user` varchar(200) NOT NULL default '',
  PRIMARY KEY (`sfqId`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_survey_question_options`;
CREATE TABLE `tiki_survey_question_options` (
  `optionId` int(12) NOT NULL auto_increment,
  `questionId` int(12) NOT NULL default '0',
  `qoption` text,
  `votes` int(10) default NULL,
  PRIMARY KEY (`optionId`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_survey_questions`;
CREATE TABLE `tiki_survey_questions` (
  `questionId` int(12) NOT NULL auto_increment,
  `surveyId` int(12) NOT NULL default '0',
  `question` text,
  `options` text,
  `type` char(1) default NULL,
  `position` int(5) default NULL,
  `votes` int(10) default NULL,
  `value` int(10) default NULL,
  `average` decimal(4,2) default NULL,
  `mandatory` char(1) NOT NULL default 'n',
  `max_answers` int(5) NOT NULL default 0,
  `min_answers` int(5) NOT NULL default 0,
  PRIMARY KEY (`questionId`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_surveys`;
CREATE TABLE `tiki_surveys` (
  `surveyId` int(12) NOT NULL auto_increment,
  `name` varchar(200) default NULL,
  `description` text,
  `taken` int(10) default NULL,
  `lastTaken` int(14) default NULL,
  `created` int(14) default NULL,
  `status` char(1) default NULL,
  PRIMARY KEY (`surveyId`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_tags`;
CREATE TABLE `tiki_tags` (
  `tagName` varchar(80) NOT NULL default '',
  `pageName` varchar(160) NOT NULL default '',
  `hits` int(8) default NULL,
  `description` varchar(200) default NULL,
  `data` longblob,
  `lastModif` int(14) default NULL,
  `comment` varchar(200) default NULL,
  `version` int(8) NOT NULL default '0',
  `user` varchar(200) NOT NULL default '',
  `ip` varchar(15) default NULL,
  `flag` char(1) default NULL,
  PRIMARY KEY (`tagName`,`pageName`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_theme_control_categs`;
CREATE TABLE `tiki_theme_control_categs` (
  `categId` int(12) NOT NULL default '0',
  `theme` varchar(250) NOT NULL default '',
  PRIMARY KEY (`categId`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_theme_control_objects`;
CREATE TABLE `tiki_theme_control_objects` (
  `objId` varchar(250) NOT NULL default '',
  `type` varchar(250) NOT NULL default '',
  `name` varchar(250) NOT NULL default '',
  `theme` varchar(250) NOT NULL default '',
  PRIMARY KEY (`objId`(100), `type`(100))
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_theme_control_sections`;
CREATE TABLE `tiki_theme_control_sections` (
  `section` varchar(250) NOT NULL default '',
  `theme` varchar(250) NOT NULL default '',
  PRIMARY KEY (`section`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_topics`;
CREATE TABLE `tiki_topics` (
  `topicId` int(14) NOT NULL auto_increment,
  `name` varchar(40) default NULL,
  `image_name` varchar(80) default NULL,
  `image_type` varchar(80) default NULL,
  `image_size` int(14) default NULL,
  `image_data` longblob,
  `active` char(1) default NULL,
  `created` int(14) default NULL,
  PRIMARY KEY (`topicId`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_tracker_fields`;
CREATE TABLE `tiki_tracker_fields` (
  `fieldId` int(12) NOT NULL auto_increment,
  `trackerId` int(12) NOT NULL default '0',
  `name` varchar(255) default NULL,
  `options` text,
  `type` varchar(15) default NULL,
  `isMain` char(1) default NULL,
  `isTblVisible` char(1) default NULL,
  `position` int(4) default NULL,
  `isSearchable` char(1) NOT NULL default 'y',
  `isPublic` char(1) NOT NULL default 'n',
  `isHidden` char(1) NOT NULL default 'n',
  `isMandatory` char(1) NOT NULL default 'n',
  `description` text,
  `isMultilingual` char(1) default 'n',
  `itemChoices` text,
  `errorMsg` text,
  `visibleBy` text,
  `editableBy` text,
  `descriptionIsParsed` char(1) default 'n',
  `validation` varchar(255) default '',
  `validationParam` varchar(255) default '',
  `validationMessage` varchar(255) default '',
  PRIMARY KEY (`fieldId`),
  INDEX `trackerId` (`trackerId`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_tracker_item_attachments`;
CREATE TABLE `tiki_tracker_item_attachments` (
  `attId` int(12) NOT NULL auto_increment,
  `itemId` int(12) NOT NULL default 0,
  `filename` varchar(80) default NULL,
  `filetype` varchar(80) default NULL,
  `filesize` int(14) default NULL,
  `user` varchar(200) default NULL,
  `data` longblob,
  `path` varchar(255) default NULL,
  `hits` int(10) default NULL,
  `created` int(14) default NULL,
  `comment` varchar(250) default NULL,
  `longdesc` blob,
  `version` varchar(40) default NULL,
  PRIMARY KEY (`attId`),
  INDEX `itemId` (`itemId`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_tracker_item_comments`;
CREATE TABLE `tiki_tracker_item_comments` (
  `commentId` int(12) NOT NULL auto_increment,
  `itemId` int(12) NOT NULL default '0',
  `user` varchar(200) default NULL,
  `data` text,
  `title` varchar(200) default NULL,
  `posted` int(14) default NULL,
  PRIMARY KEY (`commentId`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_tracker_item_fields`;
CREATE TABLE `tiki_tracker_item_fields` (
  `itemId` int(12) NOT NULL default '0',
  `fieldId` int(12) NOT NULL default '0',
  `value` text,
  `lang` char(16) default NULL,
  PRIMARY KEY (`itemId`,`fieldId`,`lang`),
  INDEX `fieldId` (`fieldId`),
  INDEX value (value(250)),
  INDEX lang (lang),
  FULLTEXT KEY `ft` (value)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_tracker_item_field_logs`;
CREATE TABLE `tiki_tracker_item_field_logs` (
  `version` int(12) NOT NULL,
  `itemId` int(12) NOT NULL default '0',
  `fieldId` int(12) NOT NULL default '0',
  `value` text,
  `lang` char(16) default NULL,
  INDEX `version` (`version`),
  INDEX `itemId` (`itemId`),
  INDEX `fieldId` (`itemId`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_tracker_items`;
CREATE TABLE `tiki_tracker_items` (
  `itemId` int(12) NOT NULL auto_increment,
  `trackerId` int(12) NOT NULL default '0',
  `created` int(14) default NULL,
  `createdBy` varchar(200) default NULL,
  `status` char(1) default NULL,
  `lastModif` int(14) default NULL,
  `lastModifBy` varchar(200) default NULL,
  PRIMARY KEY (`itemId`),
  INDEX `trackerId` (`trackerId`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_tracker_options`;
CREATE TABLE `tiki_tracker_options` (
  `trackerId` int(12) NOT NULL default '0',
  `name` varchar(80) NOT NULL default '',
  `value` text default NULL,
  PRIMARY KEY (`trackerId`,`name`(30))
) ENGINE=MyISAM ;

DROP TABLE IF EXISTS `tiki_trackers`;
CREATE TABLE `tiki_trackers` (
  `trackerId` int(12) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `description` text,
  `descriptionIsParsed` varchar(1) NULL default '0',
  `created` int(14) default NULL,
  `lastModif` int(14) default NULL,
  `showCreated` char(1) default NULL,
  `showStatus` char(1) default NULL,
  `showLastModif` char(1) default NULL,
  `useComments` char(1) default NULL,
  `useAttachments` char(1) default NULL,
  `showAttachments` char(1) default NULL,
  `items` int(10) default NULL,
  `showComments` char(1) default NULL,
  `orderAttachments` varchar(255) NOT NULL default 'filename,created,filesize,hits,desc',
  PRIMARY KEY (`trackerId`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_untranslated`;
CREATE TABLE `tiki_untranslated` (
  `id` int(14) NOT NULL auto_increment,
  `source` tinyblob NOT NULL,
  `lang` char(16) NOT NULL default '',
  PRIMARY KEY (`source`(255),`lang`),
  UNIQUE KEY `id` (id),
  KEY `id_2` (id)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_user_answers`;
CREATE TABLE `tiki_user_answers` (
  `userResultId` int(10) NOT NULL default '0',
  `quizId` int(10) NOT NULL default '0',
  `questionId` int(10) NOT NULL default '0',
  `optionId` int(10) NOT NULL default '0',
  PRIMARY KEY (`userResultId`,`quizId`,`questionId`,`optionId`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_user_answers_uploads`;
CREATE TABLE `tiki_user_answers_uploads` (
  `answerUploadId` int(4) NOT NULL auto_increment,
  `userResultId` int(11) NOT NULL default '0',
  `questionId` int(11) NOT NULL default '0',
  `filename` varchar(255) NOT NULL default '',
  `filetype` varchar(64) NOT NULL default '',
  `filesize` varchar(255) NOT NULL default '',
  `filecontent` longblob NOT NULL,
  PRIMARY KEY (`answerUploadId`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_user_assigned_modules`;
CREATE TABLE `tiki_user_assigned_modules` (
  `moduleId` int(8) NOT NULL,
  `name` varchar(200) NOT NULL default '',
  `position` char(1) default NULL,
  `ord` int(4) default NULL,
  `type` char(1) default NULL,
  `user` varchar(200) NOT NULL default '',
  PRIMARY KEY (`name`(30),`user`,`position`, `ord`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_user_bookmarks_folders`;
CREATE TABLE `tiki_user_bookmarks_folders` (
  `folderId` int(12) NOT NULL auto_increment,
  `parentId` int(12) default NULL,
  `user` varchar(200) NOT NULL default '',
  `name` varchar(30) default NULL,
  PRIMARY KEY (`user`,`folderId`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_user_bookmarks_urls`;
CREATE TABLE `tiki_user_bookmarks_urls` (
  `urlId` int(12) NOT NULL auto_increment,
  `name` varchar(200) default NULL,
  `url` varchar(250) default NULL,
  `data` longblob,
  `lastUpdated` int(14) default NULL,
  `folderId` int(12) NOT NULL default '0',
  `user` varchar(200) NOT NULL default '',
  PRIMARY KEY (`urlId`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_user_login_cookies`;
CREATE TABLE `tiki_user_login_cookies` (
    `userId` INT NOT NULL,
    `secret` CHAR(64) NOT NULL,
    `expiration` TIMESTAMP NOT NULL,
    PRIMARY KEY (`userId`, `secret`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_user_mail_accounts`;
CREATE TABLE `tiki_user_mail_accounts` (
  `accountId` int(12) NOT NULL auto_increment,
  `user` varchar(200) NOT NULL default '',
  `account` varchar(50) NOT NULL default '',
  `pop` varchar(255) default NULL,
  `current` char(1) default NULL,
  `port` int(4) default NULL,
  `username` varchar(100) default NULL,
  `pass` varchar(100) default NULL,
  `msgs` int(4) default NULL,
  `smtp` varchar(255) default NULL,
  `useAuth` char(1) default NULL,
  `smtpPort` int(4) default NULL,
  `flagsPublic` char(1) default 'n',                -- COMMENT 'MatWho - Shared Group Mail box if y',
  `autoRefresh` int(4) NOT NULL default 0,        -- COMMENT 'seconds for mail list to refresh, 0 = none',
  `imap` varchar( 255 ) default NULL,
  `mbox` varchar( 255 ) default NULL,
  `maildir` varchar( 255 ) default NULL,
  `useSSL` char( 1 ) NOT NULL default 'n',
  `fromEmail` varchar( 255 ) NOT NULL default '',
  PRIMARY KEY (`accountId`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_user_menus`;
CREATE TABLE `tiki_user_menus` (
  `user` varchar(200) NOT NULL default '',
  `menuId` int(12) NOT NULL auto_increment,
  `url` varchar(250) default NULL,
  `name` varchar(40) default NULL,
  `position` int(4) default NULL,
  `mode` char(1) default NULL,
  PRIMARY KEY (`menuId`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_user_modules`;
CREATE TABLE `tiki_user_modules` (
  `name` varchar(200) NOT NULL default '',
  `title` varchar(40) default NULL,
  `data` longblob,
  `parse` char(1) default NULL,
  PRIMARY KEY (`name`)
) ENGINE=MyISAM;

INSERT INTO tiki_user_modules (name, title, data, parse) VALUES ('Application Menu', 'Menu', '{menu id=42 css=y type=vert}', 'n');

DROP TABLE IF EXISTS `tiki_user_notes`;
CREATE TABLE `tiki_user_notes` (
  `user` varchar(200) NOT NULL default '',
  `noteId` int(12) NOT NULL auto_increment,
  `created` int(14) default NULL,
  `name` varchar(255) default NULL,
  `lastModif` int(14) default NULL,
  `data` text,
  `size` int(14) default NULL,
  `parse_mode` varchar(20) default NULL,
  PRIMARY KEY (`noteId`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_user_postings`;
CREATE TABLE `tiki_user_postings` (
  `user` varchar(200) NOT NULL default '',
  `posts` int(12) default NULL,
  `last` int(14) default NULL,
  `first` int(14) default NULL,
  `level` int(8) default NULL,
  PRIMARY KEY (`user`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_user_preferences`;
CREATE TABLE `tiki_user_preferences` (
  `user` varchar(200) NOT NULL default '',
  `prefName` varchar(40) NOT NULL default '',
  `value` TEXT,
  PRIMARY KEY (`user`,`prefName`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_user_quizzes`;
CREATE TABLE `tiki_user_quizzes` (
  `user` varchar(200) default '',
  `quizId` int(10) default NULL,
  `timestamp` int(14) default NULL,
  `timeTaken` int(14) default NULL,
  `points` int(12) default NULL,
  `maxPoints` int(12) default NULL,
  `resultId` int(10) default NULL,
  `userResultId` int(10) NOT NULL auto_increment,
  PRIMARY KEY (`userResultId`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_user_taken_quizzes`;
CREATE TABLE `tiki_user_taken_quizzes` (
  `user` varchar(200) NOT NULL default '',
  `quizId` varchar(255) NOT NULL default '',
  PRIMARY KEY (`user`,`quizId`(50))
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_user_tasks_history`;
CREATE TABLE `tiki_user_tasks_history` (
  `belongs_to` integer(14) NOT NULL,                   -- the first task in a history it has the same id as the task id
  `task_version` integer(4) NOT NULL DEFAULT 0,        -- version number for the history it starts with 0
  `title` varchar(250) NOT NULL,                       -- title
  `description` text DEFAULT NULL,                     -- description
  `start` integer(14) DEFAULT NULL,                    -- date of the starting, if it is not set than there is no starting date
  `end` integer(14) DEFAULT NULL,                      -- date of the end, if it is not set than there is not dealine
  `lasteditor` varchar(200) NOT NULL,                  -- lasteditor: username of last editior
  `lastchanges` integer(14) NOT NULL,                  -- date of last changes
  `priority` integer(2) NOT NULL DEFAULT 3,                     -- priority
  `completed` integer(14) DEFAULT NULL,                -- date of the completation if it is null it is not yet completed
  `deleted` integer(14) DEFAULT NULL,                  -- date of the deleteation it it is null it is not deleted
  `status` char(1) DEFAULT NULL,                       -- null := waiting, o := open / in progress, c := completed -> (percentage = 100)
  `percentage` int(4) DEFAULT NULL,
  `accepted_creator` char(1) DEFAULT NULL,             -- y - yes, n - no, null - waiting
  `accepted_user` char(1) DEFAULT NULL,                -- y - yes, n - no, null - waiting
  PRIMARY KEY (`belongs_to`, `task_version`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_user_tasks`;
CREATE TABLE `tiki_user_tasks` (
  `taskId` integer(14) NOT NULL auto_increment,        -- task id
  `last_version` integer(4) NOT NULL DEFAULT 0,        -- last version of the task starting with 0
  `user` varchar(200) NOT NULL DEFAULT '',              -- task user
  `creator` varchar(200) NOT NULL,                     -- username of creator
  `public_for_group` varchar(30) DEFAULT NULL,         -- this group can also view the task, if it is null it is not public
  `rights_by_creator` char(1) DEFAULT NULL,            -- null the user can delete the task,
  `created` integer(14) NOT NULL,                      -- date of the creation
  `status` char(1) default NULL,
  `priority` int(2) default NULL,
  `completed` int(14) default NULL,
  `percentage` int(4) default NULL,
  PRIMARY KEY (`taskId`),
  UNIQUE(creator, created)
) ENGINE=MyISAM AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `tiki_user_votings`;
CREATE TABLE `tiki_user_votings` (
  `user` varchar(200) NOT NULL default '',
  `ip` varchar(15) default NULL,
  `id` varchar(255) NOT NULL default '',
  `optionId` int(10) NOT NULL default 0,
  `time` int(14) NOT NULL default 0,
  KEY (`user`(100),id(100)),
  KEY `ip` (`ip`),
  KEY `id` (`id`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_user_watches`;
CREATE TABLE `tiki_user_watches` (
  `watchId` int(12) NOT NULL auto_increment,
  `user` varchar(200) NOT NULL default '',
  `event` varchar(40) NOT NULL default '',
  `object` varchar(200) NOT NULL default '',
  `title` varchar(250) default NULL,
  `type` varchar(200) default NULL,
  `url` varchar(250) default NULL,
  `email` varchar(200) default NULL,
  PRIMARY KEY (`watchId`),
  INDEX `event-object-user` ( `event` , `object` ( 100 ) , `user` ( 50 ) )
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_userfiles`;
CREATE TABLE `tiki_userfiles` (
  `user` varchar(200) NOT NULL default '',
  `fileId` int(12) NOT NULL auto_increment,
  `name` varchar(200) default NULL,
  `filename` varchar(200) default NULL,
  `filetype` varchar(200) default NULL,
  `filesize` varchar(200) default NULL,
  `data` longblob,
  `hits` int(8) default NULL,
  `isFile` char(1) default NULL,
  `path` varchar(255) default NULL,
  `created` int(14) default NULL,
  PRIMARY KEY (`fileId`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_userpoints`;
CREATE TABLE `tiki_userpoints` (
  `user` varchar(200) NOT NULL default '',
  `points` decimal(8,2) default NULL,
  `voted` int(8) default NULL
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_webmail_contacts`;
CREATE TABLE `tiki_webmail_contacts` (
  `contactId` int(12) NOT NULL auto_increment,
  `firstName` varchar(80) default NULL,
  `lastName` varchar(80) default NULL,
  `email` varchar(250) default NULL,
  `nickname` varchar(200) default NULL,
  `user` varchar(200) NOT NULL default '',
  PRIMARY KEY (`contactId`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_webmail_contacts_groups`;
CREATE TABLE `tiki_webmail_contacts_groups` (
  `contactId` int(12) NOT NULL,
  `groupName` varchar(255) NOT NULL,
  PRIMARY KEY (`contactId`,`groupName`(200))
) ENGINE=MyISAM ;

DROP TABLE IF EXISTS `tiki_webmail_messages`;
CREATE TABLE `tiki_webmail_messages` (
  `accountId` int(12) NOT NULL default '0',
  `mailId` varchar(255) NOT NULL default '',
  `user` varchar(200) NOT NULL default '',
  `isRead` char(1) default NULL,
  `isReplied` char(1) default NULL,
  `isFlagged` char(1) default NULL,
  `flaggedMsg` varchar(50) default '',
  PRIMARY KEY (`accountId`,`mailId`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_wiki_attachments`;
CREATE TABLE `tiki_wiki_attachments` (
  `attId` int(12) NOT NULL auto_increment,
  `page` varchar(200) NOT NULL default '',
  `filename` varchar(255) default NULL,
  `filetype` varchar(80) default NULL,
  `filesize` int(14) default NULL,
  `user` varchar(200) NOT NULL default '',
  `data` longblob,
  `path` varchar(255) default NULL,
  `hits` int(10) default NULL,
  `created` int(14) default NULL,
  `comment` varchar(250) default NULL,
  PRIMARY KEY (`attId`),
  KEY `page` (page)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_zones`;
CREATE TABLE `tiki_zones` (
  `zone` varchar(40) NOT NULL default '',
  PRIMARY KEY (`zone`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_download`;
CREATE TABLE `tiki_download` (
  `id` int(11) NOT NULL auto_increment,
  `object` varchar(255) NOT NULL default '',
  `userId` int(8) NOT NULL default '0',
  `type` varchar(20) NOT NULL default '',
  `date` int(14) NOT NULL default '0',
  `IP` varchar(50) NOT NULL default '',
  PRIMARY KEY (`id`),
  KEY `object` (object,`userId`,type),
  KEY `userId` (`userId`),
  KEY `type` (type),
  KEY `date` (date)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `users_grouppermissions`;
CREATE TABLE `users_grouppermissions` (
  `groupName` varchar(255) NOT NULL default '',
  `permName` varchar(40) NOT NULL default '',
  `value` char(1) default '',
  PRIMARY KEY (`groupName`(30),`permName`)
) ENGINE=MyISAM;


INSERT INTO users_grouppermissions (`groupName`,`permName`)
	VALUES('Anonymous','tiki_p_view'),('Anonymous','tiki_p_search'),('Anonymous','tiki_p_download_files');

DROP TABLE IF EXISTS `users_groups`;
CREATE TABLE `users_groups` (
  `id` int(11) NOT NULL auto_increment,
  `groupName` varchar(255) NOT NULL default '',
  `groupDesc` varchar(255) default NULL,
  `groupHome` varchar(255),
  `usersTrackerId` int(11),
  `groupTrackerId` int(11),
  `usersFieldId` int(11),
  `groupFieldId` int(11),
  `registrationChoice` char(1) default NULL,
  `registrationUsersFieldIds` text,
  `userChoice` char(1) default NULL,
  `groupDefCat` int(12) default 0,
  `groupTheme` varchar(255) default '',
  `isExternal` char(1) default 'n',
  `expireAfter` int(14) default 0,
  `emailPattern`  varchar(255) default '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `groupName` (`groupName`),
  KEY `expireAfter` (`expireAfter`)
) ENGINE=MyISAM AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `users_objectpermissions`;
CREATE TABLE `users_objectpermissions` (
  `groupName` varchar(255) NOT NULL default '',
  `permName` varchar(40) NOT NULL default '',
  `objectType` varchar(20) NOT NULL default '',
  `objectId` varchar(32) NOT NULL default '',
  PRIMARY KEY (`objectId`, `objectType`, `groupName`(30),`permName`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `users_permissions`;
CREATE TABLE `users_permissions` (
  `permName` varchar(40) NOT NULL default '',
  `permDesc` varchar(250) default NULL,
  `level` varchar(80) default NULL,
  `type` varchar(20) default NULL,
  `admin` varchar(1) default NULL,
  `feature_check` varchar(255) default NULL,
  PRIMARY KEY  (`permName`),
  KEY `type` (`type`)
) ENGINE=MyISAM;

INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_admin_calendar', 'Can create/admin calendars', 'admin', 'calendar', 'y', 'feature_calendar');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_add_events', 'Can add events in the calendar', 'registered', 'calendar', NULL, 'feature_calendar');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_change_events', 'Can change events in the calendar', 'registered', 'calendar', NULL, 'feature_calendar');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_view_calendar', 'Can browse the calendar', 'basic', 'calendar', NULL, 'feature_calendar');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_view_events', 'Can view events details', 'registered', 'calendar', NULL, 'feature_calendar');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_calendar_add_my_particip', 'Can add own user to the participants', 'registered', 'calendar', NULL, 'feature_calendar');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_calendar_add_guest_particip', 'Can add guest to the participants', 'registered', 'calendar', NULL, 'feature_calendar');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_view_tiki_calendar', 'Can view Tiki tools calendar', 'basic', 'calendar', NULL, 'feature_calendar');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_admin_chat', 'Administrator, can create channels remove channels etc', 'editors', 'chat', 'y', 'feature_minichat,feature_live_support');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_chat', 'Can use the chat system', 'registered', 'chat', NULL, 'feature_minichat,feature_live_support');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_admin_cms', 'Can admin the articles', 'editors', 'cms', 'y', 'feature_articles');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_approve_submission', 'Can approve submissions', 'editors', 'cms', NULL, 'feature_articles');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_articles_admin_topics', 'Can admin article topics', 'editors', 'cms', NULL, 'feature_articles');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_articles_admin_types', 'Can admin article types', 'editors', 'cms', NULL, 'feature_articles');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_articles_read_heading', 'Can read article headings', 'basic', 'cms', NULL, 'feature_articles');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_autoapprove_submission', 'Submitted articles automatically approved', 'editors', 'cms', NULL, 'feature_articles');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_edit_article', 'Can edit articles', 'editors', 'cms', NULL, 'feature_articles');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_edit_submission', 'Can edit submissions', 'editors', 'cms', NULL, 'feature_articles');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_read_article', 'Can read articles', 'basic', 'cms', NULL, 'feature_articles');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_remove_article', 'Can remove articles', 'editors', 'cms', NULL, 'feature_articles');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_remove_submission', 'Can remove submissions', 'editors', 'cms', NULL, 'feature_articles');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_submit_article', 'Can submit articles', 'basic', 'cms', NULL, 'feature_articles');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_rate_article', 'Can rate articles', 'basic', 'cms', NULL, 'feature_articles');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_topic_read', 'Can read a topic (Applies only to individual topic perms)', 'basic', 'cms', NULL, 'feature_articles');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_admin_contribution', 'Can admin contributions', 'admin', 'contribution', 'y', 'feature_contribution');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_admin_directory', 'Can admin the directory', 'editors', 'directory', 'y', 'feature_directory');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_admin_directory_cats', 'Can admin directory categories', 'editors', 'directory', NULL, 'feature_directory');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_admin_directory_sites', 'Can admin directory sites', 'editors', 'directory', NULL, 'feature_directory');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_autosubmit_link', 'Submitted links are valid', 'editors', 'directory', NULL, 'feature_directory');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_submit_link', 'Can submit sites to the directory', 'basic', 'directory', NULL, 'feature_directory');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_validate_links', 'Can validate submitted links', 'editors', 'directory', NULL, 'feature_directory');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_view_directory', 'Can use the directory', 'basic', 'directory', NULL, 'feature_directory');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_admin_faqs', 'Can admin faqs', 'editors', 'faqs', 'y', 'feature_faqs');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_suggest_faq', 'Can suggest faq questions', 'basic', 'faqs', NULL, 'feature_faqs');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_view_faqs', 'Can view faqs', 'basic', 'faqs', NULL, 'feature_faqs');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_admin', 'Administrator, can manage users groups and permissions, and all features', 'admin', 'tiki', 'y', NULL);
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_admin_users', 'Can admin users', 'admin', 'tiki', NULL, NULL);
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_access_closed_site', 'Can access site when closed', 'admin', 'tiki', NULL, NULL);
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_admin_banners', 'Administrator, can admin banners', 'admin', 'tiki', NULL, NULL);
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_admin_banning', 'Can ban users or ips', 'admin', 'tiki', NULL, NULL);
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_admin_dynamic', 'Can admin the dynamic content system', 'editors', 'tiki', NULL, NULL);
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_admin_integrator', 'Can admin integrator repositories and rules', 'admin', 'tiki', NULL, NULL);
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_admin_mailin', 'Can admin mail-in accounts', 'admin', 'tiki', NULL, NULL);
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_admin_objects', 'Can edit object permissions', 'admin', 'tiki', NULL, NULL);
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_admin_rssmodules', 'Can admin external feeds', 'admin', 'tiki', NULL, NULL);
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_clean_cache', 'Can clean cache', 'editors', 'tiki', NULL, NULL);
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_create_css', 'Can create new css suffixed with -user', 'registered', 'tiki', NULL, NULL);
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_detach_translation', 'Can remove association between two pages in a translation set', 'registered', 'tiki', NULL, NULL);
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_edit_cookies', 'Can admin cookies', 'editors', 'tiki', NULL, NULL);
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_edit_languages', 'Can edit translations and create new languages', 'editors', 'tiki', NULL, NULL);
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_edit_menu', 'Can edit menu', 'admin', 'tiki', NULL, NULL);
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_edit_menu_option', 'Can edit menu option', 'admin', 'tiki', NULL, NULL);
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_edit_templates', 'Can edit site templates', 'admin', 'tiki', NULL, NULL);
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_search', 'Can search', 'basic', 'tiki', NULL, NULL);
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_site_report', 'Can report a link to the webmaster', 'basic', 'tiki', NULL, NULL);
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_subscribe_groups', 'Can subscribe to groups', 'registered', 'tiki', NULL, NULL);
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_tell_a_friend', 'Can send a link to a friend', 'Basic', 'tiki', NULL, NULL);
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_share', 'Can share a page (email, twitter, facebook, message, forums)', 'Basic', 'tiki', NULL, NULL);
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_use_HTML', 'Can use HTML in pages', 'editors', 'tiki', NULL, NULL);
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_view_actionlog', 'Can view action log', 'registered', 'tiki', NULL, NULL);
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_view_actionlog_owngroups', 'Can view action log for users of his own groups', 'registered', 'tiki', NULL, NULL);
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_view_integrator', 'Can view integrated repositories', 'basic', 'tiki', NULL, NULL);
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_view_referer_stats', 'Can view referrer stats', 'editors', 'tiki', NULL, NULL);
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_view_stats', 'Can view site stats', 'basic', 'tiki', NULL, NULL);
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_view_templates', 'Can view site templates', 'admin', 'tiki', NULL, NULL);
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_blog_admin', 'Can admin blogs', 'editors', 'blogs', 'y', 'feature_blogs');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_assign_perm_blog', 'Can assign perms to blog', 'admin', 'blogs', NULL, 'feature_blogs');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_blog_post', 'Can post to a blog', 'registered', 'blogs', NULL, 'feature_blogs');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_create_blogs', 'Can create a blog', 'editors', 'blogs', NULL, 'feature_blogs');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_read_blog', 'Can read blogs', 'basic', 'blogs', NULL, 'feature_blogs');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_blog_post_view_ref', 'Can view in module and feed the blog posts', 'basic', 'blogs', NULL, 'feature_blogs');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_blog_view_ref', 'Can view in module and feed the blog', 'basic', 'blogs', NULL, 'feature_blogs');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_admin_file_galleries', 'Can admin file galleries', 'editors', 'file galleries', 'y', 'feature_file_galleries');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_assign_perm_file_gallery', 'Can assign perms to file gallery', 'admin', 'file galleries', NULL, 'feature_file_galleries');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_batch_upload_file_dir', 'Can use Directory Batch Load', 'editors', 'file galleries', NULL, 'feature_file_galleries');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_batch_upload_files', 'Can upload zip files with files', 'editors', 'file galleries', NULL, 'feature_file_galleries');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_create_file_galleries', 'Can create file galleries', 'editors', 'file galleries', NULL, 'feature_file_galleries');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_download_files', 'Can download files', 'basic', 'file galleries', NULL, 'feature_file_galleries');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_edit_gallery_file', 'Can edit a gallery file', 'editors', 'file galleries', NULL, 'feature_file_galleries');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_list_file_galleries', 'Can list file galleries', 'basic', 'file galleries', NULL, 'feature_file_galleries');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_upload_files', 'Can upload files', 'registered', 'file galleries', NULL, 'feature_file_galleries');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_remove_files', 'Can remove files', 'registered', 'file galleries', NULL, 'feature_file_galleries');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_view_fgal_explorer', 'Can view file galleries explorer', 'basic', 'file galleries', NULL, 'feature_file_galleries');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_view_fgal_path', 'Can view file galleries path', 'basic', 'file galleries', NULL, 'feature_file_galleries');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_view_file_gallery', 'Can view file galleries', 'basic', 'file galleries', NULL, 'feature_file_galleries');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_admin_forum', 'Can admin forums', 'editors', 'forums', 'y', 'feature_forums');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_forum_attach', 'Can attach to forum posts', 'registered', 'forums', NULL, 'feature_forums');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_forum_autoapp', 'Auto approve forum posts', 'editors', 'forums', NULL, 'feature_forums');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_forum_edit_own_posts', 'Can edit own forum posts', 'registered', 'forums', NULL, 'feature_forums');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_forum_post', 'Can post in forums', 'registered', 'forums', NULL, 'feature_forums');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_forum_post_topic', 'Can start threads in forums', 'registered', 'forums', NULL, 'feature_forums');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_forum_read', 'Can read forums', 'basic', 'forums', NULL, 'feature_forums');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_forums_report', 'Can report posts to moderator', 'registered', 'forums', NULL, 'feature_forums');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_forum_vote', 'Can vote comments in forums', 'registered', 'forums', NULL, 'feature_forums');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_admin_freetags', 'Can admin freetags', 'admin', 'freetags', 'y', 'feature_freetags');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_admin_galleries', 'Can admin Image Galleries', 'editors', 'image galleries', 'y', 'feature_galleries');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_assign_perm_image_gallery', 'Can assign perms to image gallery', 'admin', 'image galleries', NULL, 'feature_galleries');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_batch_upload_image_dir', 'Can use Directory Batch Load', 'editors', 'image galleries', NULL, 'feature_galleries');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_batch_upload_images', 'Can upload zip files with images', 'editors', 'image galleries', NULL, 'feature_galleries');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_create_galleries', 'Can create image galleries', 'editors', 'image galleries', NULL, 'feature_galleries');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_freetags_tag', 'Can tag objects', 'registered', 'freetags', NULL, 'feature_freetags');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_list_image_galleries', 'Can list image galleries', 'basic', 'image galleries', NULL, 'feature_galleries');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_unassign_freetags', 'Can unassign tags from an object', 'basic', 'freetags', NULL, 'feature_freetags');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_upload_images', 'Can upload images', 'registered', 'image galleries', NULL, 'feature_galleries');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_view_freetags', 'Can browse freetags', 'basic', 'freetags', NULL, 'feature_freetags');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_view_image_gallery', 'Can view image galleries', 'basic', 'image galleries', NULL, 'feature_galleries');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_admin_newsletters', 'Can admin newsletters', 'admin', 'newsletters', 'y', 'feature_newsletters');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_batch_subscribe_email', 'Can subscribe many e-mails at once (requires tiki_p_subscribe email)', 'editors', 'newsletters', NULL, 'feature_newsletters');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_send_newsletters', 'Can send newsletters', 'editors', 'newsletters', NULL, 'feature_newsletters');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_subscribe_email', 'Can subscribe any email to newsletters', 'editors', 'newsletters', NULL, 'feature_newsletters');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_subscribe_newsletters', 'Can subscribe to newsletters', 'basic', 'newsletters', NULL, 'feature_newsletters');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_view_newsletter', 'Can view the archive of a newsletters', 'basic', 'newsletters', NULL, 'feature_newsletters');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_list_newsletters', 'Can list newsletters', 'basic', 'newsletters', NULL, 'feature_newsletters');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_admin_polls', 'Can admin polls', 'admin', 'polls', 'y', 'feature_polls');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_view_poll_results', 'Can view poll results', 'basic', 'polls', NULL, 'feature_polls');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_vote_poll', 'Can vote polls', 'basic', 'polls', NULL, 'feature_polls');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_view_poll_voters', 'Can view poll voters', 'basic', 'polls', NULL, 'feature_polls');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_admin_toolbars', 'Can admin toolbars', 'admin', 'tiki', NULL, NULL);
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_admin_quizzes', 'Can admin quizzes', 'editors', 'quizzes', 'y', 'feature_quizzes');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_take_quiz', 'Can take quizzes', 'basic', 'quizzes', NULL, 'feature_quizzes');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_view_quiz_stats', 'Can view quiz stats', 'basic', 'quizzes', NULL, 'feature_quizzes');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_view_user_results', 'Can view user quiz results', 'editors', 'quizzes', NULL, 'feature_quizzes');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_admin_sheet', 'Can admin sheet', 'admin', 'sheet', 'y', 'feature_sheet');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_edit_sheet', 'Can create and edit sheets', 'editors', 'sheet', NULL, 'feature_sheet');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_view_sheet', 'Can view sheet', 'basic', 'sheet', NULL, 'feature_sheet');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_view_sheet_history', 'Can view sheet history', 'admin', 'sheet', NULL, 'feature_sheet');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_admin_shoutbox', 'Can admin shoutbox (Edit/remove messages)', 'editors', 'shoutbox', 'y', 'feature_shoutbox');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_post_shoutbox', 'Can post messages in shoutbox', 'basic', 'shoutbox', NULL, 'feature_shoutbox');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_view_shoutbox', 'Can view shoutbox', 'basic', 'shoutbox', NULL, 'feature_shoutbox');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_admin_surveys', 'Can admin surveys', 'editors', 'surveys', 'y', 'feature_surveys');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_take_survey', 'Can take surveys', 'basic', 'surveys', NULL, 'feature_surveys');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_view_survey_stats', 'Can view survey stats', 'basic', 'surveys', NULL, 'feature_surveys');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_admin_trackers', 'Can admin trackers', 'editors', 'trackers', 'y', 'feature_trackers');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_attach_trackers', 'Can attach files to tracker items', 'registered', 'trackers', NULL, 'feature_trackers');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_tracker_view_attachments', 'Can view tracker items attachments and download', 'registered', 'trackers', NULL, 'feature_trackers');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_comment_tracker_items', 'Can insert comments for tracker items', 'basic', 'trackers', NULL, 'feature_trackers');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_tracker_view_comments', 'Can view tracker items comments', 'basic', 'trackers', NULL, 'feature_trackers');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_create_tracker_items', 'Can create new items for trackers', 'registered', 'trackers', NULL, 'feature_trackers');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_list_trackers', 'Can list trackers', 'basic', 'trackers', NULL, 'feature_trackers');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_modify_tracker_items', 'Can change tracker items', 'registered', 'trackers', NULL, 'feature_trackers');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_modify_tracker_items_pending', 'Can change pending tracker items', 'registered', 'trackers', NULL, 'feature_trackers');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_modify_tracker_items_closed', 'Can change closed tracker items', 'registered', 'trackers', NULL, 'feature_trackers');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_remove_tracker_items', 'Can remove tracker items', 'registered', 'trackers', NULL, 'feature_trackers');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_remove_tracker_items_pending', 'Can remove pending tracker items', 'registered', 'trackers', NULL, 'feature_trackers');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_remove_tracker_items_closed', 'Can remove closed tracker items', 'registered', 'trackers', NULL, 'feature_trackers');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_tracker_view_ratings', 'Can view rating result for tracker items', 'basic', 'trackers', NULL, 'feature_trackers');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_tracker_vote_ratings', 'Can vote a rating for tracker items', 'registered', 'trackers', NULL, 'feature_trackers');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_tracker_revote_ratings', 'Can re-vote a rating for tracker items', 'registered', 'trackers', NULL, 'feature_trackers');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_view_trackers', 'Can view trackers', 'basic', 'trackers', NULL, 'feature_trackers');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_view_trackers_closed', 'Can view closed trackers items', 'registered', 'trackers', NULL, 'feature_trackers');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_view_trackers_pending', 'Can view pending trackers items', 'editors', 'trackers', NULL, 'feature_trackers');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_watch_trackers', 'Can watch tracker', 'registered', 'trackers', NULL, 'feature_trackers');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_export_tracker', 'Can export tracker items', 'registered', 'trackers', NULL, 'feature_trackers');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_admin_wiki', 'Can admin the wiki', 'editors', 'wiki', 'y', 'feature_wiki');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_assign_perm_wiki_page', 'Can assign perms to wiki pages', 'admin', 'wiki', NULL, 'feature_wiki');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_edit', 'Can edit pages', 'registered', 'wiki', NULL, 'feature_wiki');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_edit_copyrights', 'Can edit copyright notices', 'editors', 'wiki', NULL, 'wiki_feature_copyrights');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_edit_dynvar', 'Can edit dynamic variables', 'editors', 'wiki', NULL, 'feature_wiki');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_edit_structures', 'Can create and edit structures', 'editors', 'wiki', NULL, 'feature_wiki_structure');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_export_wiki', 'Can export wiki pages using the export feature', 'admin', 'wiki', NULL, 'feature_wiki_export');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_lock', 'Can lock pages', 'editors', 'wiki', NULL, 'feature_wiki');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_minor', 'Can save as minor edit', 'registered', 'wiki', NULL, 'feature_wiki');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_remove', 'Can remove', 'editors', 'wiki', NULL, 'feature_wiki');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_rename', 'Can rename pages', 'editors', 'wiki', NULL, 'feature_wiki');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_rollback', 'Can rollback pages', 'editors', 'wiki', NULL, 'feature_wiki');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_upload_picture', 'Can upload pictures to wiki pages', 'registered', 'wiki', NULL, 'feature_wiki_pictures');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_use_as_template', 'Can use the page as a tracker template', 'basic', 'wiki', NULL, 'feature_wiki');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_view', 'Can view page/pages', 'basic', 'wiki', NULL, 'feature_wiki');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_wiki_view_ref', 'Can view in module and feed the wiki pages reference', 'basic', 'wiki', NULL, 'feature_wiki');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_watch_structure', 'Can watch structure', 'registered', 'wiki', NULL, 'feature_wiki_structure');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_wiki_admin_attachments', 'Can admin attachments on wiki pages', 'editors', 'wiki', NULL, 'feature_wiki_attachments');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_wiki_admin_ratings', 'Can add and change ratings on wiki pages', 'admin', 'wiki', NULL, 'feature_wiki_ratings');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_wiki_attach_files', 'Can attach files to wiki pages', 'registered', 'wiki', NULL, 'feature_wiki_attachments');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_wiki_view_attachments', 'Can view wiki attachments and download', 'registered', 'wiki', NULL, 'feature_wiki_attachments');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_wiki_view_comments', 'Can view wiki comments', 'basic', 'wiki', NULL, 'feature_wiki_comments');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_wiki_view_history', 'Can view wiki history', 'basic', 'wiki', NULL, 'feature_history');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_wiki_view_ratings', 'Can view rating of wiki pages', 'basic', 'wiki', NULL, 'feature_wiki_ratings');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_wiki_view_source', 'Can view source of wiki pages', 'basic', 'wiki', NULL, 'feature_source');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_wiki_vote_ratings', 'Can participate to rating of wiki pages', 'registered', 'wiki', NULL, 'feature_wiki_ratings');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_wiki_view_similar', 'Can view similar wiki pages', 'registered', 'wiki', NULL, 'feature_wiki');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_admin_received_articles', 'Can admin received articles', 'editors', 'comm', NULL, 'feature_comm');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_admin_received_pages', 'Can admin received pages', 'editors', 'comm', NULL, 'feature_comm');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_send_articles', 'Can send articles to other sites', 'editors', 'comm', NULL, 'feature_comm');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_sendme_articles', 'Can send articles to this site', 'registered', 'comm', NULL, 'feature_comm');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_sendme_pages', 'Can send pages to this site', 'registered', 'comm', NULL, 'feature_comm');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_send_pages', 'Can send pages to other sites', 'registered', 'comm', NULL, 'feature_comm');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_admin_tikitests', 'Can admin the TikiTests', 'admin', 'tikitests', NULL, 'feature_tikitests');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_edit_tikitests', 'Can edit TikiTests', 'editors', 'tikitests', NULL, 'feature_tikitests');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_play_tikitests', 'Can replay the TikiTests', 'registered', 'tikitests', NULL, 'feature_tikitests');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_cache_bookmarks', 'Can cache user bookmarks', 'admin', 'user', NULL, 'feature_user_bookmarks');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_configure_modules', 'Can configure modules', 'registered', 'user', NULL, 'feature_modulecontrols');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_create_bookmarks', 'Can create user bookmarks', 'registered', 'user', NULL, 'feature_user_bookmarks');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_minical', 'Can use the mini event calendar', 'registered', 'user', NULL, 'feature_minical');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_notepad', 'Can use the notepad', 'registered', 'user', NULL, 'feature_notepad');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_tasks_admin', 'Can admin public tasks', 'admin', 'user', NULL, 'feature_tasks');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_tasks', 'Can use tasks', 'registered', 'user', NULL, 'feature_tasks');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_tasks_receive', 'Can receive tasks from other users', 'registered', 'user', NULL, 'feature_tasks');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_tasks_send', 'Can send tasks to other users', 'registered', 'user', NULL, 'feature_tasks');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_userfiles', 'Can upload personal files', 'registered', 'user', NULL, 'feature_userfiles');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_usermenu', 'Can create items in personal menu', 'registered', 'user', NULL, 'feature_usermenu');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_broadcast_all', 'Can broadcast messages to all user', 'admin', 'messu', NULL, 'feature_messages');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_broadcast', 'Can broadcast messages to groups', 'admin', 'messu', NULL, 'feature_messages');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_messages', 'Can use the messaging system', 'registered', 'messu', NULL, 'feature_messages');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_admin_comments', 'Can admin comments', 'admin', 'comments', 'y', 'feature_wiki_comments,feature_blogposts_comments,feature_file_galleries_comments,feature_image_galleries_comments,feature_article_comments,feature_faq_comments,feature_poll_comments,map_comments');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_edit_comments', 'Can edit all comments', 'editors', 'comments', NULL, 'feature_wiki_comments,feature_blogposts_comments,feature_file_galleries_comments,feature_image_galleries_comments,feature_article_comments,feature_faq_comments,feature_poll_comments,map_comments');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_post_comments', 'Can post new comments', 'registered', 'comments', NULL, 'feature_wiki_comments,feature_blogposts_comments,feature_file_galleries_comments,feature_image_galleries_comments,feature_article_comments,feature_faq_comments,feature_poll_comments,map_comments');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_read_comments', 'Can read comments', 'basic', 'comments', NULL, 'feature_wiki_comments,feature_blogposts_comments,feature_file_galleries_comments,feature_image_galleries_comments,feature_article_comments,feature_faq_comments,feature_poll_comments,map_comments');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_remove_comments', 'Can delete comments', 'editors', 'comments', NULL, 'feature_wiki_comments,feature_blogposts_comments,feature_file_galleries_comments,feature_image_galleries_comments,feature_article_comments,feature_faq_comments,feature_poll_comments,map_comments');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_vote_comments', 'Can vote comments', 'registered', 'comments', NULL, 'feature_wiki_comments,feature_blogposts_comments,feature_file_galleries_comments,feature_image_galleries_comments,feature_article_comments,feature_faq_comments,feature_poll_comments,map_comments');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_admin_content_templates', 'Can admin content templates', 'admin', 'content templates', 'y', 'feature_wiki_templates,feature_cms_templates');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_edit_content_templates', 'Can edit content templates', 'editors', 'content templates', NULL, 'feature_wiki_templates,feature_cms_templates');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_use_content_templates', 'Can use content templates', 'registered', 'content templates', NULL, 'feature_wiki_templates,feature_cms_templates');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_edit_html_pages', 'Can edit HTML pages', 'editors', 'html pages', NULL, 'feature_html_pages');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_view_html_pages', 'Can view HTML pages', 'basic', 'html pages', NULL, 'feature_html_pages');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_list_users', 'Can list registered users', 'registered', 'user', NULL, NULL);
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_live_support_admin', 'Admin live support system', 'admin', 'support', 'y', 'feature_live_support');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_live_support', 'Can use live support system', 'basic', 'support', NULL, 'feature_live_support');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_map_create', 'Can create new mapfile', 'admin', 'maps', NULL, 'feature_maps');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_map_delete', 'Can delete mapfiles', 'admin', 'maps', NULL, 'feature_maps');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_map_edit', 'Can edit mapfiles', 'editors', 'maps', NULL, 'feature_maps');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_map_view', 'Can view mapfiles', 'basic', 'maps', NULL, 'feature_maps');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_map_view_mapfiles', 'Can view contents of mapfiles', 'registered', 'maps', NULL, 'feature_maps');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_use_webmail', 'Can use webmail', 'registered', 'webmail', NULL, 'feature_webmail,feature_contacts');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_use_group_webmail', 'Can use group webmail', 'registered', 'webmail', NULL, 'feature_webmail,feature_contacts');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_admin_group_webmail', 'Can admin group webmail accounts', 'registered', 'webmail', NULL, 'feature_webmail,feature_contacts');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_use_personal_webmail', 'Can use personal webmail accounts', 'registered', 'webmail', NULL, 'feature_webmail,feature_contacts');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_admin_personal_webmail', 'Can admin personal webmail accounts', 'registered', 'webmail', NULL, 'feature_webmail,feature_contacts');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_plugin_viewdetail', 'Can view unapproved plugin details', 'registered', 'wiki', NULL, 'feature_wiki');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_plugin_preview', 'Can execute unapproved plugin', 'registered', 'wiki', NULL, 'feature_wiki');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_plugin_approve', 'Can approve plugin execution', 'editors', 'wiki', NULL, 'feature_wiki');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_trust_input', 'Trust all user inputs including plugins (no security checks)', 'admin', 'tiki', NULL, NULL);
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_view_backlink', 'View page backlinks', 'basic', 'wiki', NULL, 'feature_wiki');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_admin_notifications', 'Can admin mail notifications', 'editors', 'tiki', NULL, NULL);
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_invite_to_my_groups', 'Can invite user to my groups', 'editors', 'tiki', NULL, NULL);
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_delete_account', 'Can delete his/her own account', 'admin', 'tiki', NULL, NULL);
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_admin_importer', 'Can use the importer', 'admin', 'tiki', NULL, NULL);
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_admin_categories', 'Can admin categories', 'editors', 'category', 'y', 'feature_categories');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_view_category', 'Can see the category in a listing', 'basic', 'category', NULL, 'feature_categories');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_modify_object_categories', 'Can change the categories of the object', 'editors', 'tiki', NULL, NULL);
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_add_object', 'Can add objects to the category (needs tiki_p_modify_object_categories)', 'editors', 'category', NULL, 'feature_categories');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_remove_object', 'Can remove objects from the category (needs tiki_p_modify_object_categories)', 'editors', 'category', NULL, 'feature_categories');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_create_category', 'Can create new categories', 'admin', 'category', NULL, 'feature_categories');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_perspective_view', 'Can view the perspective', 'basic', 'perspective', NULL, 'feature_perspective');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_perspective_edit', 'Can edit the perspective', 'basic', 'perspective', NULL, 'feature_perspective');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_perspective_create', 'Can create a perspective', 'basic', 'perspective', NULL, 'feature_perspective');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_perspective_admin', 'Can admin perspectives', 'admin', 'perspective', 'y', 'feature_perspective');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_group_view', 'Can view the group', 'basic', 'group', NULL, NULL);
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_group_view_members', 'Can view the group members', 'basic', 'group', NULL, NULL);
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_group_add_member', 'Can add group members', 'admin', 'group', NULL, NULL);
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_group_remove_member', 'Can remove group members', 'admin', 'group', NULL, NULL);
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_group_join', 'Can join or leave the group', 'admin', 'group', NULL, NULL);

INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_trigger_transition', 'Can trigger the transition between two states', 'admin', 'transition', NULL, 'feature_group_transition,feature_category_transition');

INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_admin_kaltura', 'Can admin kaltura feature', 'admin', 'kaltura', 'y', 'feature_kaltura');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_upload_videos', 'Can upload video on kaltura server', 'editors', 'kaltura', NULL, 'feature_kaltura');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_edit_videos', 'Can edit information of kaltura entry', 'editors', 'kaltura', NULL, 'feature_kaltura');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_remix_videos', 'Can create kaltura remix video', 'editors', 'kaltura', NULL, 'feature_kaltura');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_delete_videos', 'Can delete kaltura entry', 'editors', 'kaltura', NULL, 'feature_kaltura');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_download_videos', 'Can download kaltura entry', 'registered', 'kaltura', NULL, 'feature_kaltura');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_list_videos', 'Can list kaltura entries', 'basic', 'kaltura', NULL, 'feature_kaltura');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_view_videos', 'Can view kaltura entry', 'basic', 'kaltura', NULL, 'feature_kaltura');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_dsn_query', 'Can execute arbitrary queries on a given DSN', 'admin', 'dsn', NULL, NULL);

INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_payment_admin', 'Can administer payments', 'admin', 'payment', 'y', 'payment_feature');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_payment_view', 'Can view payment requests and details', 'admin', 'payment', NULL, 'payment_feature');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_payment_manual', 'Can enter manual payments', 'admin', 'payment', NULL, 'payment_feature');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_payment_request', 'Can request a payment', 'admin', 'payment', NULL, 'payment_feature');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES ('tiki_p_admin_modules', 'User can administer modules', 'registered', 'tiki', NULL, NULL);
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_tracker_dump', 'Can save a .CSV backup of entire trackers', 'admin', 'trackers', NULL, 'feature_trackers');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_bigbluebutton_join', 'Can join a meeting', 'basic', 'bigbluebutton', NULL, 'bigbluebutton_feature');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_bigbluebutton_moderate', 'Can moderate a meeting', 'admin', 'bigbluebutton', NULL, 'bigbluebutton_feature');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_bigbluebutton_create', 'Can create a meeting', 'admin', 'bigbluebutton', NULL, 'bigbluebutton_feature');

INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_socialnetworks', 'user can use social network integration', 'registered', 'socialnetworks', NULL, 'feature_socialnetworks');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_admin_socialnetworks', 'user can register this site with social networks', 'admin', 'socialnetworks', 'y', 'feature_socialnetworks');


INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_page_contribution_view', 'Can view contributions to a page', 'basic', 'wiki', NULL, 'feature_wiki');

INSERT INTO `users_permissions` (`permName` , `permDesc` , `level` , `type` , `admin` , `feature_check` ) VALUES ( 'tiki_p_acct_create_book', 'Can create/close a book', 'admin', 'accounting', 'y', 'feature_accounting');
INSERT INTO `users_permissions` (`permName` , `permDesc` , `level` , `type` , `admin` , `feature_check` ) VALUES ( 'tiki_p_acct_manage_accounts', 'Can create/edit/lock accounts', 'admin', 'accounting', 'y', 'feature_accounting' );
INSERT INTO `users_permissions` (`permName` , `permDesc` , `level` , `type` , `admin` , `feature_check` ) VALUES ( 'tiki_p_acct_book', 'Create a new transaction', 'editor', 'accounting', 'n', 'feature_accounting');
INSERT INTO `users_permissions` (`permName` , `permDesc` , `level` , `type` , `admin` , `feature_check` ) VALUES ( 'tiki_p_acct_view', 'Permission to view the journal', 'registered', 'accounting', 'n', 'feature_accounting' );
INSERT INTO `users_permissions` (`permName` , `permDesc` , `level` , `type` , `admin` , `feature_check` ) VALUES ( 'tiki_p_acct_book_stack', 'Can book into the stack, where statements can be changed', 'editor', 'accounting', 'n', 'feature_accounting');
INSERT INTO `users_permissions` (`permName` , `permDesc` , `level` , `type` , `admin` , `feature_check` ) VALUES ( 'tiki_p_acct_book_import', 'Can import statements from external accounts', 'editor', 'accounting', 'n', 'feature_accounting' );
INSERT INTO `users_permissions` (`permName` , `permDesc` , `level` , `type` , `admin` , `feature_check` ) VALUES ( 'tiki_p_acct_manage_template', 'Can manage templates for recurring transactions', 'editor', 'accounting', 'n', 'feature_accounting');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_wiki_view_latest', 'Can view unapproved revisions of pages', 'registered', 'wiki', NULL, 'flaggedrev_approval');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_wiki_approve', 'Can approve revisions of pages', 'editor', 'wiki', NULL, 'flaggedrev_approval');

DROP TABLE IF EXISTS `users_usergroups`;
CREATE TABLE `users_usergroups` (
  `userId` int(8) NOT NULL default '0',
  `groupName` varchar(255) NOT NULL default '',
  `created` int(14) default NULL,
  `expire` int(14) default NULL,
  PRIMARY KEY (`userId`,`groupName`(30))
) ENGINE=MyISAM;

INSERT INTO users_groups (`groupName`,`groupDesc`) VALUES ('Anonymous','Public users not logged');
INSERT INTO users_groups (`groupName`,`groupDesc`) VALUES ('Registered','Users logged into the system');
INSERT INTO users_groups (`groupName`,`groupDesc`) VALUES ('Admins','Administrator and accounts managers.');

DROP TABLE IF EXISTS `users_users`;
CREATE TABLE `users_users` (
  `userId` int(8) NOT NULL auto_increment,
  `email` varchar(200) default NULL,
  `login` varchar(200) NOT NULL default '',
  `password` varchar(30) default '',
  `provpass` varchar(30) default NULL,
  `default_group` varchar(255),
  `lastLogin` int(14) default NULL,
  `currentLogin` int(14) default NULL,
  `registrationDate` int(14) default NULL,
  `challenge` varchar(32) default NULL,
  `pass_confirm` int(14) default NULL,
  `email_confirm` int(14) default NULL,
  `hash` varchar(34) default NULL,
  `created` int(14) default NULL,
  `avatarName` varchar(80) default NULL,
  `avatarSize` int(14) default NULL,
  `avatarFileType` varchar(250) default NULL,
  `avatarData` longblob,
  `avatarLibName` varchar(200) default NULL,
  `avatarType` char(1) default NULL,
  `score` int(11) NOT NULL default 0,
  `valid` varchar(32) default NULL,
  `unsuccessful_logins` int(14) default 0,
  `openid_url` varchar(255) default NULL,
  `waiting` char(1) default NULL,
  PRIMARY KEY (`userId`),
  KEY `score` (score),
  UNIQUE KEY `login` (login),
  KEY `registrationDate` (`registrationDate`),
  KEY `openid_url` (openid_url)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

-- Administrator account
INSERT INTO users_users(email,login,password,hash) VALUES ('','admin','admin',md5('adminadmin'));
UPDATE users_users set `currentLogin`=`lastLogin`, `registrationDate`=`lastLogin`;
INSERT INTO tiki_user_preferences (user,`prefName`,value) VALUES ('admin','realName','System Administrator');
INSERT INTO users_usergroups (`userId`, `groupName`) VALUES(1,'Admins');
INSERT INTO users_grouppermissions (`groupName`, `permName`) VALUES ('Admins','tiki_p_admin');

DROP TABLE IF EXISTS `tiki_integrator_reps`;
CREATE TABLE `tiki_integrator_reps` (
  `repID` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `path` varchar(255) NOT NULL default '',
  `start_page` varchar(255) NOT NULL default '',
  `css_file` varchar(255) NOT NULL default '',
  `visibility` char(1) NOT NULL default 'y',
  `cacheable` char(1) NOT NULL default 'y',
  `expiration` int(11) NOT NULL default '0',
  `description` text NOT NULL,
  PRIMARY KEY (`repID`)
) ENGINE=MyISAM;

INSERT INTO tiki_integrator_reps VALUES ('1','Doxygened (1.3.4) Documentation','','index.html','doxygen.css','n','y','0','Use this repository as rule source for all your repositories based on doxygened docs. To setup yours just add new repository and copy rules from this repository :)');

DROP TABLE IF EXISTS `tiki_integrator_rules`;
CREATE TABLE `tiki_integrator_rules` (
  `ruleID` int(11) NOT NULL auto_increment,
  `repID` int(11) NOT NULL default '0',
  `ord` int(2) unsigned NOT NULL default '0',
  `srch` blob NOT NULL,
  `repl` blob NOT NULL,
  `type` char(1) NOT NULL default 'n',
  `casesense` char(1) NOT NULL default 'y',
  `rxmod` varchar(20) NOT NULL default '',
  `enabled` char(1) NOT NULL default 'n',
  `description` text NOT NULL,
  PRIMARY KEY (`ruleID`),
  KEY `repID` (repID)
) ENGINE=MyISAM;

INSERT INTO tiki_integrator_rules VALUES ('1','1','1','.*<body[^>]*?>(.*?)</body.*','\1','y','n','i','y','Extract code between <body> and </body> tags');
INSERT INTO tiki_integrator_rules VALUES ('2','1','2','img src=(\"|\')(?!http://)','img src=\1{path}/','y','n','i','y','Fix image paths');
INSERT INTO tiki_integrator_rules VALUES ('3','1','3','href=(\"|\')(?!(#|(http|ftp)://))','href=\1tiki-integrator.php?repID={repID}&file=','y','n','i','y','Replace internal links to integrator. Don not touch an external link.');

-- Translated objects table
DROP TABLE IF EXISTS `tiki_translated_objects`;
CREATE TABLE `tiki_translated_objects` (
  `traId` int(14) NOT NULL auto_increment,
  `type` varchar(50) NOT NULL,
  `objId` varchar(255) NOT NULL,
  `lang` varchar(16) default NULL,
  PRIMARY KEY (`type`, `objId`),
  KEY `traId` ( `traId` )
) ENGINE=MyISAM AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `tiki_friends`;
CREATE TABLE `tiki_friends` (
  `user` varchar(200) NOT NULL default '',
  `friend` varchar(200) NOT NULL default '',
  PRIMARY KEY (`user`(120),friend(120))
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_friendship_requests`;
CREATE TABLE `tiki_friendship_requests` (
  `userFrom` varchar(200) NOT NULL default '',
  `userTo` varchar(200) NOT NULL default '',
  `tstamp` timestamp NOT NULL,
  PRIMARY KEY (`userFrom`(120),`userTo`(120))
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_score`;
CREATE TABLE `tiki_score` (
  event varchar(40) NOT NULL default '',
  score int(11) NOT NULL default '0',
  expiration int(11) NOT NULL default '0',
  PRIMARY KEY (`event`)
) ENGINE=MyISAM;

INSERT INTO tiki_score (event, score, expiration) VALUES ('login',1,0);
INSERT INTO tiki_score (event, score, expiration) VALUES ('login_remain',2,60);
INSERT INTO tiki_score (event, score, expiration) VALUES ('profile_fill',10,0);
INSERT INTO tiki_score (event, score, expiration) VALUES ('profile_see',2,0);
INSERT INTO tiki_score (event, score, expiration) VALUES ('profile_is_seen',1,0);
INSERT INTO tiki_score (event, score, expiration) VALUES ('friend_new',10,0);
INSERT INTO tiki_score (event, score, expiration) VALUES ('message_receive',1,0);
INSERT INTO tiki_score (event, score, expiration) VALUES ('message_send',2,0);
INSERT INTO tiki_score (event, score, expiration) VALUES ('article_read',2,0);
INSERT INTO tiki_score (event, score, expiration) VALUES ('article_comment',5,0);
INSERT INTO tiki_score (event, score, expiration) VALUES ('article_new',20,0);
INSERT INTO tiki_score (event, score, expiration) VALUES ('article_is_read',1,0);
INSERT INTO tiki_score (event, score, expiration) VALUES ('article_is_commented',2,0);
INSERT INTO tiki_score (event, score, expiration) VALUES ('fgallery_new',10,0);
INSERT INTO tiki_score (event, score, expiration) VALUES ('fgallery_new_file',10,0);
INSERT INTO tiki_score (event, score, expiration) VALUES ('fgallery_download',5,0);
INSERT INTO tiki_score (event, score, expiration) VALUES ('fgallery_is_downloaded',5,0);
INSERT INTO tiki_score (event, score, expiration) VALUES ('igallery_new',10,0);
INSERT INTO tiki_score (event, score, expiration) VALUES ('igallery_new_img',6,0);
INSERT INTO tiki_score (event, score, expiration) VALUES ('igallery_see_img',3,0);
INSERT INTO tiki_score (event, score, expiration) VALUES ('igallery_img_seen',1,0);
INSERT INTO tiki_score (event, score, expiration) VALUES ('blog_new',20,0);
INSERT INTO tiki_score (event, score, expiration) VALUES ('blog_post',5,0);
INSERT INTO tiki_score (event, score, expiration) VALUES ('blog_read',2,0);
INSERT INTO tiki_score (event, score, expiration) VALUES ('blog_comment',2,0);
INSERT INTO tiki_score (event, score, expiration) VALUES ('blog_is_read',3,0);
INSERT INTO tiki_score (event, score, expiration) VALUES ('blog_is_commented',3,0);
INSERT INTO tiki_score (event, score, expiration) VALUES ('wiki_new',10,0);
INSERT INTO tiki_score (event, score, expiration) VALUES ('wiki_edit',5,0);
INSERT INTO tiki_score (event, score, expiration) VALUES ('wiki_attach_file',3,0);

DROP TABLE IF EXISTS `tiki_users_score`;
CREATE TABLE `tiki_users_score` (
  `user` char(200) NOT NULL default '',
  `event_id` char(200) NOT NULL default '',
  `expire` int(14) NOT NULL default '0',
  `tstamp` timestamp NOT NULL,
  PRIMARY KEY (`user`(110),`event_id`(110)),
  KEY `user` (user(110),event_id(110),expire)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_file_handlers`;
CREATE TABLE `tiki_file_handlers` (
  `mime_type` varchar(64) default NULL,
  `cmd` varchar(238) default NULL
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_stats`;
CREATE TABLE `tiki_stats` (
  `object` varchar(255) NOT NULL default '',
  `type` varchar(20) NOT NULL default '',
  `day` int(14) NOT NULL default '0',
  `hits` int(14) NOT NULL default '0',
  PRIMARY KEY (`object`(200),`type`,`day`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_registration_fields`;
CREATE TABLE `tiki_registration_fields` (
  `id` int(11) NOT NULL auto_increment,
  `field` varchar(255) NOT NULL default '',
  `name` varchar(255) default NULL,
  `type` varchar(255) NOT NULL default 'text',
  `show` tinyint(1) NOT NULL default '1',
  `size` varchar(10) default '10',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_actionlog_conf`;
CREATE TABLE `tiki_actionlog_conf` (
  `id` int(11) NOT NULL auto_increment,
  `action` varchar(32) NOT NULL default '',
  `objectType` varchar(32) NOT NULL default '',
  `status` char(1) default '',
  PRIMARY KEY (`action`, `objectType`),
  KEY (id)
) ENGINE=MyISAM;

INSERT IGNORE INTO tiki_actionlog_conf(action, `objectType`, status) VALUES ('Created', 'wiki page', 'y');
INSERT IGNORE INTO tiki_actionlog_conf(action, `objectType`, status) VALUES ('Updated', 'wiki page', 'y');
INSERT IGNORE INTO tiki_actionlog_conf(action, `objectType`, status) VALUES ('Removed', 'wiki page', 'y');
INSERT IGNORE INTO tiki_actionlog_conf(action, `objectType`, status) VALUES ('Viewed', 'wiki page', 'n');
INSERT IGNORE INTO tiki_actionlog_conf(action, `objectType`, status) VALUES ('Viewed', 'forum', 'n');
INSERT IGNORE INTO tiki_actionlog_conf(action, `objectType`, status) VALUES ('Posted', 'forum', 'n');
INSERT IGNORE INTO tiki_actionlog_conf(action, `objectType`, status) VALUES ('Replied', 'forum', 'n');
INSERT IGNORE INTO tiki_actionlog_conf(action, `objectType`, status) VALUES ('Updated', 'forum', 'n');
INSERT IGNORE INTO tiki_actionlog_conf(action, `objectType`, status) VALUES ('Viewed', 'file gallery', 'n');
INSERT IGNORE INTO tiki_actionlog_conf(action, `objectType`, status) VALUES ('Viewed', 'image gallery', 'n');
INSERT IGNORE INTO tiki_actionlog_conf(action, `objectType`, status) VALUES ('Uploaded', 'file gallery', 'n');
INSERT IGNORE INTO tiki_actionlog_conf(action, `objectType`, status) VALUES ('Uploaded', 'image gallery', 'n');
INSERT IGNORE INTO tiki_actionlog_conf(action, `objectType`, status) VALUES ('%', 'category', 'n');
INSERT IGNORE INTO tiki_actionlog_conf(action, `objectType`, status) VALUES ('login', 'system', 'y');
INSERT IGNORE INTO tiki_actionlog_conf(action, `objectType`, status) VALUES ('Posted', 'message', 'n');
INSERT IGNORE INTO tiki_actionlog_conf(action, `objectType`, status) VALUES ('Replied', 'message', 'n');
INSERT IGNORE INTO tiki_actionlog_conf(action, `objectType`, status) VALUES ('Viewed', 'message', 'n');
INSERT IGNORE INTO tiki_actionlog_conf(action, `objectType`, status) VALUES ('Removed version', 'wiki page', 'n');
INSERT IGNORE INTO tiki_actionlog_conf(action, `objectType`, status) VALUES ('Removed last version', 'wiki page', 'n');
INSERT IGNORE INTO tiki_actionlog_conf(action, `objectType`, status) VALUES ('Rollback', 'wiki page', 'n');
INSERT IGNORE INTO tiki_actionlog_conf(action, `objectType`, status) VALUES ('Removed', 'forum', 'n');
INSERT IGNORE INTO tiki_actionlog_conf(action, `objectType`, status) VALUES ('Downloaded', 'file gallery', 'n');
INSERT IGNORE INTO tiki_actionlog_conf(action, `objectType`, status) VALUES ('Posted', 'comment', 'n');
INSERT IGNORE INTO tiki_actionlog_conf(action, `objectType`, status) VALUES ('Replied', 'comment', 'n');
INSERT IGNORE INTO tiki_actionlog_conf(action, `objectType`, status) VALUES ('Updated', 'comment', 'n');
INSERT IGNORE INTO tiki_actionlog_conf(action, `objectType`, status) VALUES ('Removed', 'comment', 'n');
INSERT IGNORE INTO tiki_actionlog_conf(action, `objectType`, status) VALUES ('Renamed', 'wiki page', 'n');
INSERT IGNORE INTO tiki_actionlog_conf(action, `objectType`, status) VALUES ('Created', 'sheet', 'n');
INSERT IGNORE INTO tiki_actionlog_conf(action, `objectType`, status) VALUES ('Updated', 'sheet', 'n');
INSERT IGNORE INTO tiki_actionlog_conf(action, `objectType`, status) VALUES ('Removed', 'sheet', 'n');
INSERT IGNORE INTO tiki_actionlog_conf(action, `objectType`, status) VALUES ('Viewed', 'sheet', 'n');
INSERT IGNORE INTO tiki_actionlog_conf(action, `objectType`, status) VALUES ('Viewed', 'blog', 'n');
INSERT IGNORE INTO tiki_actionlog_conf(action, `objectType`, status) VALUES ('Posted', 'blog', 'n');
INSERT IGNORE INTO tiki_actionlog_conf(action, `objectType`, status) VALUES ('Updated', 'blog', 'n');
INSERT IGNORE INTO tiki_actionlog_conf(action, `objectType`, status) VALUES ('Removed', 'blog', 'n');
INSERT IGNORE INTO tiki_actionlog_conf(action, `objectType`, status) VALUES ('Removed', 'file', 'n');
INSERT IGNORE INTO tiki_actionlog_conf(action, `objectType`, status) VALUES ('Viewed', 'article', 'n');
INSERT IGNORE INTO tiki_actionlog_conf(action, `objectType`, status) VALUES ('%', 'system', 'y');
INSERT IGNORE INTO tiki_actionlog_conf(action, `objectType`, status) VALUES ('feature', 'system', 'y');
INSERT IGNORE INTO tiki_actionlog_conf(action, `objectType`, status) VALUES ('Updated', 'trackeritem', 'n');
INSERT IGNORE INTO tiki_actionlog_conf(action, `objectType`, status) VALUES ('Created', 'trackeritem', 'n');
INSERT IGNORE INTO tiki_actionlog_conf(action, `objectType`, status) VALUES ('Viewed', 'trackeritem', 'n');
INSERT IGNORE INTO tiki_actionlog_conf(action, `objectType`, status) VALUES ('Viewed', 'trackeritem', 'n');
INSERT IGNORE INTO tiki_actionlog_conf(action, `objectType`, status) VALUES ('Created', 'wiki page attachment', 'n');
INSERT IGNORE INTO tiki_actionlog_conf(action, `objectType`, status) VALUES ('Removed', 'wiki page attachment', 'n');
INSERT IGNORE INTO tiki_actionlog_conf(action, `objectType`, status) VALUES ('Categorized', 'wiki page', 'n');
INSERT IGNORE INTO tiki_actionlog_conf(action, `objectType`, status) VALUES ('Uncategorized', 'wiki page', 'n');
INSERT IGNORE INTO tiki_actionlog_conf(action, `objectType`, status) VALUES ('Flagged', 'wiki page', 'n');

DROP TABLE IF EXISTS `tiki_freetags`;
CREATE TABLE `tiki_freetags` (
  `tagId` int(10) unsigned NOT NULL auto_increment,
  `tag` varchar(128) NOT NULL default '',
  `raw_tag` varchar(150) NOT NULL default '',
  `lang` varchar(16) NULL,
  PRIMARY KEY (`tagId`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_freetagged_objects`;
CREATE TABLE `tiki_freetagged_objects` (
  `tagId` int(12) NOT NULL auto_increment,
  `objectId` int(11) NOT NULL default 0,
  `user` varchar(200) default '',
  `created` int(14) NOT NULL default '0',
  PRIMARY KEY (`tagId`,`user`,`objectId`),
  KEY (`tagId`),
  KEY (user),
  KEY (`objectId`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_contributions`;
CREATE TABLE `tiki_contributions` (
  `contributionId` int(12) NOT NULL auto_increment,
  `name` varchar(100) default NULL,
  `description` varchar(250) default NULL,
  PRIMARY KEY (`contributionId`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_contributions_assigned`;
CREATE TABLE `tiki_contributions_assigned` (
  `contributionId` int(12) NOT NULL,
  `objectId` int(12) NOT NULL,
  PRIMARY KEY (`objectId`, `contributionId`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_webmail_contacts_ext`;
CREATE TABLE `tiki_webmail_contacts_ext` (
  `contactId` int(11) NOT NULL,
  `value` varchar(255) NOT NULL,
  `hidden` tinyint(1) NOT NULL,
  `fieldId` int(10) unsigned NOT NULL,
  KEY `contactId` (`contactId`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_webmail_contacts_fields`;
CREATE TABLE `tiki_webmail_contacts_fields` (
  `user` VARCHAR( 200 ) NOT NULL ,
  `fieldname` VARCHAR( 255 ) NOT NULL ,
  `order` int(2) NOT NULL default '0',
  `show` char(1) NOT NULL default 'n',
  `fieldId` int(10) unsigned NOT NULL auto_increment,
  `flagsPublic` CHAR( 1 ) NOT NULL DEFAULT 'n',
  PRIMARY KEY ( `fieldId` ),
  INDEX ( `user` )
) ENGINE = MyISAM ;

DROP TABLE IF EXISTS `tiki_pages_translation_bits`;
CREATE TABLE `tiki_pages_translation_bits` (
  `translation_bit_id` int(14) NOT NULL auto_increment,
  `page_id` int(14) NOT NULL,
  `version` int(8) NOT NULL,
  `source_translation_bit` int(10) NULL,
  `original_translation_bit` int(10) NULL,
  `flags` SET('critical') NULL DEFAULT '',
  PRIMARY KEY (`translation_bit_id`),
  KEY (`page_id`),
  KEY (`original_translation_bit`),
  KEY (`source_translation_bit`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_pages_changes`;
CREATE TABLE `tiki_pages_changes` (
  `page_id` int(14),
  `version` int(10),
  `segments_added` int(10),
  `segments_removed` int(10),
  `segments_total` int(10),
  PRIMARY KEY (page_id, version)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_minichat`;
CREATE TABLE `tiki_minichat` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `channel` varchar(31),
  `ts` int(10) unsigned NOT NULL,
  `user` varchar(31) default NULL,
  `nick` varchar(31) default NULL,
  `msg` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `channel` (`channel`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_profile_symbols`;
CREATE TABLE `tiki_profile_symbols` (
  `domain` VARCHAR(50) NOT NULL,
  `profile` VARCHAR(100) NOT NULL,
  `object` VARCHAR(150) NOT NULL,
  `type` VARCHAR(20) NOT NULL,
  `value` VARCHAR(50) NOT NULL,
  `named` ENUM('y','n') NOT NULL,
  `creation_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY ( `domain`, `profile`, `object` ),
  INDEX(`named`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_feature`;
CREATE TABLE `tiki_feature` (
  `feature_id` mediumint(9) NOT NULL auto_increment,
  `feature_name` varchar(150) NOT NULL,
  `parent_id` mediumint(9) NOT NULL,
  `status` varchar(12) NOT NULL default 'active',
  `setting_name` varchar(50) default NULL,
  `feature_type` varchar(30) NOT NULL default 'feature',
  `template` varchar(50) default NULL,
  `permission` varchar(50) default NULL,
  `ordinal` mediumint(9) NOT NULL default '1',
  `depends_on` mediumint(9) default NULL,
  `keyword` varchar(30) default NULL,
  `tip` text NULL,
  `feature_count` mediumint(9) NOT NULL default '0',
  `feature_path` varchar(20) NOT NULL default '0',
  PRIMARY KEY (`feature_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `tiki_schema`;
CREATE TABLE `tiki_schema` (
  `patch_name` VARCHAR(100) PRIMARY KEY,
  `install_date` TIMESTAMP
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_semantic_tokens`;
CREATE TABLE `tiki_semantic_tokens` (
  `token` VARCHAR(15) PRIMARY KEY,
  `label` VARCHAR(25) NOT NULL,
  `invert_token` VARCHAR(15)
) ENGINE=MyISAM ;

INSERT INTO tiki_semantic_tokens (token, label) VALUES('alias', 'Page Alias');
INSERT INTO tiki_semantic_tokens (token, label) VALUES('prefixalias', 'Page Prefix Alias');
INSERT INTO tiki_semantic_tokens (token, label) VALUES('titlefieldid', 'Field Id for Page Title'),('trackerid', 'Id of Embedded Tracker');


DROP TABLE IF EXISTS `tiki_webservice`;
CREATE TABLE `tiki_webservice` (
  `service` VARCHAR(25) NOT NULL PRIMARY KEY,
  `url` VARCHAR(250),
  `wstype` CHAR(4),
  `operation` VARCHAR(250),
  `body` TEXT,
  `schema_version` VARCHAR(5),
  `schema_documentation` VARCHAR(250)
) ENGINE=MyISAM ;

DROP TABLE IF EXISTS `tiki_webservice_template`;
CREATE TABLE `tiki_webservice_template` (
  `service` VARCHAR(25) NOT NULL,
  `template` VARCHAR(25) NOT NULL,
  `engine` VARCHAR(15) NOT NULL,
  `output` VARCHAR(15) NOT NULL,
  `content` TEXT NOT NULL,
  `last_modif` INT,
  PRIMARY KEY ( service, template )
) ENGINE=MyISAM ;

DROP TABLE IF EXISTS `tiki_groupalert`;

CREATE TABLE `tiki_groupalert` (
  `groupName` varchar(255) NOT NULL default '',
  `objectType` varchar( 20 ) NOT NULL default '',
  `objectId` varchar(10) NOT NULL default '',
  `displayEachuser` char( 1 ) default NULL ,
  PRIMARY KEY (`groupName`, `objectType`, `objectId` )
) ENGINE=MyISAM ;

DROP TABLE IF EXISTS `tiki_sent_newsletters_files`;
CREATE TABLE `tiki_sent_newsletters_files` (
  `id` int(11) NOT NULL auto_increment,
  `editionId` int(11) NOT NULL,
  `name` varchar(256) NOT NULL,
  `type` varchar(64) NOT NULL,
  `size` int(11) NOT NULL,
  `filename` varchar(256) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `editionId` (`editionId`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_sefurl_regex_out`;
CREATE TABLE `tiki_sefurl_regex_out` (
  `id` int(11) NOT NULL auto_increment,
  `left` varchar(256) NOT NULL,
  `right` varchar(256) NULL default NULL,
  `type` varchar(32) NULL default NULL,
  `silent` char(1) NULL default 'n',
  `feature` varchar(256) NULL default NULL,
  `comment` varchar(256),
  `order` int(11) NULL default 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `left` (`left`(128)),
  INDEX `idx1` (silent, type, feature(30))
) ENGINE=MyISAM;

INSERT INTO `tiki_sefurl_regex_out` (`left`, `right`, `type`, `feature`) VALUES('tiki-index.php\\?page=(.+)', '$1', 'wiki', 'feature_wiki');
INSERT INTO `tiki_sefurl_regex_out` (`left`, `right`, `type`, `feature`) VALUES('tiki-slideshow.php\\?page=(.+)', 'show:$1', '', 'feature_wiki');
INSERT INTO `tiki_sefurl_regex_out` (`left`, `right`, `type`, `feature`) VALUES('tiki-read_article.php\\?articleId=(\\d+)', 'article$1', 'article', 'feature_articles');
INSERT INTO `tiki_sefurl_regex_out` (`left`, `right`, `type`, `feature`) VALUES('tiki-browse_categories.php\\?parentId=(\\d+)', 'cat$1', 'category', 'feature_categories');
INSERT INTO `tiki_sefurl_regex_out` (`left`, `right`, `type`, `feature`) VALUES('tiki-view_blog.php\\?blogId=(\\d+)', 'blog$1', 'blog', 'feature_blogs');
INSERT INTO `tiki_sefurl_regex_out` (`left`, `right`, `type`, `feature`) VALUES('tiki-view_blog_post.php\\?postId=(\\d+)', 'blogpost$1', 'blogpost', 'feature_blogs');
INSERT INTO `tiki_sefurl_regex_out` (`left`, `right`, `type`, `feature`) VALUES('tiki-browse_image.php\\?imageId=(\\d+)', 'browseimage$1', 'image', 'feature_galleries');
INSERT INTO `tiki_sefurl_regex_out` (`left`, `right`, `type`, `feature`) VALUES('tiki-directory_browse.php\\?parent=(\\d+)', 'directory$1', 'directory', 'feature_directory');
INSERT INTO `tiki_sefurl_regex_out` (`left`, `right`, `type`, `feature`) VALUES('tiki-view_faq.php\\?faqId=(\\d+)', 'faq$1', 'faq', 'feature_faqs');
INSERT INTO `tiki_sefurl_regex_out` (`left`, `right`, `type`, `feature`, `order`) VALUES('tiki-download_file.php\\?fileId=(\\d+)', 'dl$1', 'file', 'feature_file_galleries', 10);
INSERT INTO `tiki_sefurl_regex_out` (`left`, `right`, `type`, `feature`) VALUES('tiki-download_file.php\\?fileId=(\\d+)&amp;thumbnail', 'thumbnail$1', 'thumbnail', 'feature_file_galleries');
INSERT INTO `tiki_sefurl_regex_out` (`left`, `right`, `type`, `feature`) VALUES('tiki-download_file.php\\?fileId=(\\d+)&amp;display', 'display$1', 'display', 'feature_file_galleries');
INSERT INTO `tiki_sefurl_regex_out` (`left`, `right`, `type`, `feature`) VALUES('tiki-download_file.php\\?fileId=(\\d+)&amp;preview', 'preview$1', 'preview', 'feature_file_galleries');
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
INSERT INTO `tiki_sefurl_regex_out` (`left`, `right`, `type`, `feature`, `order`) VALUES('tiki-sheets.php', 'sheets', '', 'feature_sheet', 200);
INSERT INTO `tiki_sefurl_regex_out` (`left`, `right`, `type`, `feature`, `order`) VALUES('tiki-view_tracker_item.php\\?trackerId=(\\d+)\\&itemId=(\\d+)', 'item$2', 'trackeritem', 'feature_trackers', 200);
INSERT INTO `tiki_sefurl_regex_out` (`left`, `right`, `type`, `feature`, `order`) VALUES('tiki-view_tracker_item.php\\?itemId=(\\d+)', 'item$1', 'trackeritem', 'feature_trackers', 200);
INSERT INTO `tiki_sefurl_regex_out` (`left`, `right`, `type`, `feature`, `order`) VALUES('tiki-list_file_gallery.php\\?galleryId=(\\d+)', 'file$1', 'file gallery', 'feature_file_galleries', 200);
INSERT INTO `tiki_sefurl_regex_out` (`left`, `right`, `type`, `feature`, `order`) VALUES('tiki-user_information.php\\?userId=(\\d+)','user$1', '', '', 200);
 
UPDATE tiki_menu_options SET icon = 'icon-configuration48x48' WHERE name = 'Admin';
UPDATE tiki_menu_options SET icon = 'xfce4-appfinder48x48' WHERE name = 'Search';
UPDATE tiki_menu_options SET icon = 'wikipages48x48' WHERE name = 'Wiki';
UPDATE tiki_menu_options SET icon = 'blogs48x48' WHERE name = 'Blogs';
UPDATE tiki_menu_options SET icon = 'stock_select-color48x48' WHERE name = 'Image Galleries';
UPDATE tiki_menu_options SET icon = 'file-manager48x48' WHERE name = 'File Galleries';
UPDATE tiki_menu_options SET icon = 'stock_bold48x48' WHERE name = 'Articles';
UPDATE tiki_menu_options SET icon = 'stock_index48x48' WHERE name = 'Forums';
UPDATE tiki_menu_options SET icon = 'gnome-settings-font48x48' WHERE name = 'Trackers';
UPDATE tiki_menu_options SET icon = 'users48x48' WHERE name = 'Community';
UPDATE tiki_menu_options SET icon = 'stock_dialog_question48x48' WHERE name = 'FAQs';
UPDATE tiki_menu_options SET icon = 'maps48x48' WHERE name = 'Maps';
UPDATE tiki_menu_options SET icon = 'messages48x48' WHERE name = 'Newsletters';
UPDATE tiki_menu_options SET icon = 'vcard48x48' WHERE name = 'Freetags';
UPDATE tiki_menu_options SET icon = 'date48x48' WHERE name = 'Calendar' AND url = 'tiki-calendar.php';
UPDATE tiki_menu_options SET icon = 'userfiles48x48' WHERE name = 'MyTiki';
UPDATE tiki_menu_options SET icon = '' WHERE name = 'Quizzes';
UPDATE tiki_menu_options SET icon = '' WHERE name = 'Surveys';
UPDATE tiki_menu_options SET icon = '' WHERE name = 'TikiSheet';
UPDATE tiki_menu_options SET icon = '' WHERE name = 'Workflow';
UPDATE tiki_menus SET use_items_icons='y' WHERE `menuId`=42;

DROP TABLE IF EXISTS `tiki_plugin_security`;
CREATE TABLE `tiki_plugin_security` (
  `fingerprint` VARCHAR(200) NOT NULL PRIMARY KEY,
  `status` VARCHAR(10) NOT NULL,
  `added_by` VARCHAR(200) NULL,
  `approval_by` VARCHAR(200) NULL,
  `last_update` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_objectType` VARCHAR(20) NOT NULL,
  `last_objectId` VARCHAR(200) NOT NULL,
  KEY `last_object` (last_objectType, last_objectId)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_user_reports`;
CREATE TABLE `tiki_user_reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` varchar(200) NOT NULL,
  `interval` varchar(20) NOT NULL,
  `view` varchar(8) NOT NULL,
  `type` varchar(5) NOT NULL,
  `time_to_send` datetime NULL default 0,
  `always_email` tinyint(1) NOT NULL,
  `last_report` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_user_reports_cache`;
CREATE TABLE `tiki_user_reports_cache` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` varchar(200) NOT NULL,
  `event` varchar(200) NOT NULL,
  `data` text NOT NULL,
  `time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_perspectives`;
CREATE TABLE `tiki_perspectives` (
  `perspectiveId` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY ( `perspectiveId` )
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_perspective_preferences`;
CREATE TABLE `tiki_perspective_preferences` (
  `perspectiveId` int NOT NULL,
  `pref` varchar(40) NOT NULL,
  `value` text,
  PRIMARY KEY ( `perspectiveId`, pref )
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_transitions`;
CREATE TABLE `tiki_transitions` (
    `transitionId` int NOT NULL AUTO_INCREMENT,
    `preserve` int(1) NOT NULL DEFAULT 0,
    `name` varchar(50),
    `type` varchar(20) NOT NULL,
    `from` varchar(255) NOT NULL,
    `to` varchar(255) NOT NULL,
    `guards` text,
    PRIMARY KEY(`transitionId`),
    KEY `transition_lookup` (`type`, `from`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_auth_tokens`;
CREATE TABLE `tiki_auth_tokens` (
    `tokenId` INT NOT NULL AUTO_INCREMENT,
    `creation` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `timeout` INT NOT NULL DEFAULT 0,
    `hits` INT NOT NULL DEFAULT 1,
    `maxhits` INT NOT NULL DEFAULT 1, 
    `token` CHAR(32),
    `entry` VARCHAR(50),
    `email` varchar(255) NOT NULL,
    `parameters` VARCHAR(255),
    `groups` VARCHAR(255),
    PRIMARY KEY( `tokenId` ),
    KEY `tiki_auth_tokens_token` (`token`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `metrics_assigned`;
CREATE TABLE `metrics_assigned` (
    `assigned_id` int(11) NOT NULL AUTO_INCREMENT,
    `metric_id` int(11) NOT NULL,
    `tab_id` int(11) NOT NULL,
    PRIMARY KEY (`assigned_id`),
    KEY `metric_id` (`metric_id`),
    KEY `tab_id` (`tab_id`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `metrics_metric`;
CREATE TABLE `metrics_metric` (
    `metric_id` int(11) NOT NULL AUTO_INCREMENT,
    `metric_name` varchar(255) NOT NULL,
    `metric_range` varchar(1) NOT NULL DEFAULT '+' COMMENT 'values: + (daily), @ (monthly&weekly), - (weekly)',
    `metric_datatype` varchar(1) NOT NULL DEFAULT 'i' COMMENT 'values: i(nteger), %(percentage), f(loat), L(ist)',
    `metric_lastupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `metric_query` text,
    `metric_dsn` VARCHAR(200) NOT NULL DEFAULT 'local',
    PRIMARY KEY (`metric_id`),
    UNIQUE KEY `metric_name` (`metric_name`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `metrics_tab`;
CREATE TABLE `metrics_tab` (
    `tab_id` int(11) NOT NULL AUTO_INCREMENT,
    `tab_name` varchar(255) NOT NULL,
    `tab_order` int(11) NOT NULL DEFAULT '0',
    `tab_content` longtext NOT NULL,
    PRIMARY KEY (`tab_id`),
    UNIQUE KEY `tab_name` (`tab_name`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_file_backlinks`;
CREATE TABLE `tiki_file_backlinks` (
       `fileId` int(14) NOT NULL,
       `objectId` int(12) NOT NULL,
       KEY `objectId` (`objectId`),
       KEY `fileId` (`fileId`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_payment_requests`;
CREATE TABLE `tiki_payment_requests` (
    `paymentRequestId` INT NOT NULL AUTO_INCREMENT,
    `amount` DECIMAL(7,2) NOT NULL,
    `amount_paid` DECIMAL(7,2) NOT NULL DEFAULT 0.0,
    `currency` CHAR(3) NOT NULL,
    `request_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `due_date` TIMESTAMP NOT NULL,
    `cancel_date` TIMESTAMP NULL,
    `description` VARCHAR(100) NOT NULL,
    `actions` TEXT,
    `detail` TEXT,
    `userId` int(8),
    PRIMARY KEY( `paymentRequestId` )
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_payment_received`;
CREATE TABLE `tiki_payment_received` (
    `paymentReceivedId` INT NOT NULL AUTO_INCREMENT,
    `paymentRequestId` INT NOT NULL,
    `payment_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `amount` DECIMAL(7,2),
    `type` VARCHAR(15),
    `details` TEXT,
    `userId` int(8),
    PRIMARY KEY(`paymentReceivedId`),
    KEY `payment_request_ix` (`paymentRequestId`)
) ENGINE=MyISAM;
DROP TABLE IF EXISTS `tiki_discount`;
CREATE TABLE `tiki_discount`( 
    `id` INT NOT NULL AUTO_INCREMENT,
    `code` VARCHAR(255),
    `value` VARCHAR(255),
    `max` INT,
    `comment` TEXT,
    PRIMARY KEY(`id`),
    KEY `code` (`code`)
) ENGINE=MyISAM;
DROP TABLE IF EXISTS `tiki_translations_in_progress`;
CREATE TABLE IF NOT EXISTS `tiki_translations_in_progress` (
   `page_id` int(14) NOT NULL,
   `language` char(2) NOT NULL,
   KEY `page_id` (`page_id`),
   KEY `language` (`language`),
   UNIQUE (`page_id`, `language`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_rss_items`;
CREATE TABLE `tiki_rss_items` (
    `rssItemId` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `rssId` INT NOT NULL,
    `guid` VARCHAR(255) NOT NULL,
    `url` TEXT NOT NULL,
    `publication_date` INT UNSIGNED NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `author` VARCHAR(255),
    `description` TEXT,
    `content` TEXT,
    KEY `tiki_rss_items_rss` (`rssId`),
    KEY `tiki_rss_items_item` (`rssId`, `guid`(200))
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_object_attributes`;
CREATE TABLE `tiki_object_attributes` (
    `attributeId` INT PRIMARY KEY AUTO_INCREMENT,
    `type` varchar(50) NOT NULL,
    `itemId` varchar(160) NOT NULL,
    `attribute` varchar(70) NOT NULL,
    `value` varchar(255),
    UNIQUE `item_attribute_uq` ( `type`, `itemId`, `attribute` ),
    KEY `attribute_lookup_ix` (`attribute`, `value`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_rating_configs`;
CREATE TABLE `tiki_rating_configs` (
    `ratingConfigId` INT PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(50) NOT NULL,
    `expiry` INT NOT NULL DEFAULT 3600,
    `formula` TEXT NOT NULL,
    `callbacks` TEXT
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_rating_obtained`;
CREATE TABLE `tiki_rating_obtained` (
    `ratingId` INT PRIMARY KEY AUTO_INCREMENT,
    `ratingConfigId` INT NOT NULL,
    `type` VARCHAR(50) NOT NULL,
    `object` INT NOT NULL,
    `expire` INT NOT NULL,
    `value` FLOAT NOT NULL,
    UNIQUE `tiki_obtained_rating_uq` (`type`, `object`, `ratingConfigId`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_object_relations`;
CREATE TABLE `tiki_object_relations` (
    `relationId` INT PRIMARY KEY AUTO_INCREMENT,
    `relation` varchar(70) NOT NULL,
    `source_type` varchar(50) NOT NULL,
    `source_itemId` varchar(160) NOT NULL,
    `target_type` varchar(50) NOT NULL,
    `target_itemId` varchar(160) NOT NULL,
    KEY `relation_source_ix` (`source_type`, `source_itemId`),
    KEY `relation_target_ix` (`target_type`, `target_itemId`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_todo`;
CREATE TABLE `tiki_todo` (
    `todoId` INT(12) NOT NULL auto_increment,
    `after` INT(12) NOT NULL,
    `event` VARCHAR(50) NOT NULL,
    `objectType` VARCHAR(50),
    `objectId` VARCHAR(255) default NULL,
    `from` VARCHAR(255) default NULL,
    `to` VARCHAR(255) default NULL,
    PRIMARY KEY (`todoId`),
    KEY `what` (`objectType`, `objectId`),
    KEY `after` (`after`)
) ENGINE=MyISAM;
DROP TABLE IF EXISTS `tiki_todo_notif`;
CREATE TABLE `tiki_todo_notif` (
    `todoId` INT(12) NOT NULL,
    `objectType` VARCHAR(50),
    `objectId` VARCHAR(255) default NULL,
    KEY `todoId` (`todoId`),
    KEY `objectId` (`objectId`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_url_shortener`;
CREATE TABLE `tiki_url_shortener` (
  `urlId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user` varchar(200) NOT NULL,
  `longurl` tinytext NOT NULL,
  `longurl_hash` varchar(32) NOT NULL,
  `service` varchar(32) NOT NULL,
  `shorturl` varchar(63) NOT NULL,
  PRIMARY KEY (`urlId`),
  UNIQUE KEY `shorturl` (`shorturl`),
  KEY `longurl_hash` (`longurl_hash`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tiki_invite`;
CREATE TABLE `tiki_invite` (
  `id` int(11) NOT NULL auto_increment,
  `inviter` varchar(200) NOT NULL,
  `groups` varchar(255) default NULL,
  `ts` int(11) NOT NULL,
  `emailsubject` varchar(255) NOT NULL,
  `emailcontent` text NOT NULL,
  `wikicontent` text,
  `wikipageafter` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_invited`;
CREATE TABLE `tiki_invited` (
  `id` int(11) NOT NULL auto_increment,
  `id_invite` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `firstname` varchar(24) NOT NULL,
  `lastname` varchar(24) NOT NULL,
  `used` enum('no','registered','logged') NOT NULL,
  `used_on_user` varchar(200) default NULL,
  PRIMARY KEY  (`id`),
  KEY `id_invite` (`id_invite`),
  KEY `used_on_user` (`used_on_user`)
) ENGINE=MyISAM;

INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES ('tiki_p_invite', 'Can invite users by email, and include them in groups', 'registered', 'tiki', NULL, 'feature_invite');

DROP TABLE IF EXISTS `tiki_credits`;
CREATE TABLE `tiki_credits` (
    `creditId` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
    `userId` INT( 8 ) NOT NULL ,
    `credit_type` VARCHAR( 25 ) NOT NULL ,
    `creation_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
    `expiration_date` TIMESTAMP NULL ,
    `total_amount` FLOAT NOT NULL DEFAULT 0,
    `used_amount` FLOAT NOT NULL DEFAULT 0,
    `product_id` INT( 8 ) NULL ,
    PRIMARY KEY ( `creditId` ) ,
    INDEX ( `userId` , `credit_type` )
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_credits_usage`;
CREATE TABLE `tiki_credits_usage` (
    `usageId` INT NOT NULL AUTO_INCREMENT,
    `userId` INT NOT NULL,
    `usage_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `credit_type` VARCHAR( 25 ) NOT NULL,
    `used_amount` FLOAT NOT NULL DEFAULT 0,
    `product_id` INT( 8 ) NULL ,
    PRIMARY KEY ( `usageId` )
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_credits_types`;
CREATE TABLE `tiki_credits_types` (
    `credit_type` VARCHAR( 25 ) NOT NULL,
    `display_text` VARCHAR( 50 ) DEFAULT NULL,
    `unit_text` VARCHAR( 25 ) DEFAULT NULL,
    `is_static_level` CHAR( 1 ) DEFAULT 'n',
    `scaling_divisor` FLOAT NOT NULL DEFAULT 1,
    PRIMARY KEY ( `credit_type` ) 
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_acct_account`;
CREATE TABLE `tiki_acct_account` (
  `accountBookId` int(10) unsigned NOT NULL,
  `accountId` int(10) unsigned NOT NULL DEFAULT '0',
  `accountName` varchar(255) NOT NULL,
  `accountNotes` text NOT NULL,
  `accountBudget` double NOT NULL DEFAULT '0',
  `accountLocked` int(1) NOT NULL DEFAULT '0',
  `accountTax` int(11) NOT NULL DEFAULT '0',
  `accountUserId` int(8) NOT NULL DEFAULT '0',
  PRIMARY KEY (`accountBookId`,`accountId`),
  KEY `accountTax` (`accountTax`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_acct_bankaccount`; 
CREATE TABLE `tiki_acct_bankaccount` (
  `bankBookId` int(10) unsigned NOT NULL,
  `bankAccountId` int(10) unsigned NOT NULL,
  `externalNumber` int(10) NOT NULL,
  `bankCountry` varchar(2) NOT NULL,
  `bankCode` varchar(11) NOT NULL,
  `bankIBAN` varchar(63) NOT NULL,
  `bankBIC` varchar(63) NOT NULL,
  `bankDelimeter` varchar(15) NOT NULL DEFAULT ';',
  `bankDecPoint` varchar(1) NOT NULL DEFAULT ',',
  `bankThousand` varchar(1) NOT NULL DEFAULT '.',
  `bankHasHeader` tinyint(1) NOT NULL DEFAULT '1',
  `fieldNameAccount` varchar(63) NOT NULL,
  `fieldNameBookingDate` varchar(63) NOT NULL,
  `formatBookingDate` varchar(31) NOT NULL,
  `fieldNameValueDate` varchar(63) NOT NULL,
  `formatValueDate` varchar(31) NOT NULL,
  `fieldNameBookingText` varchar(63) NOT NULL,
  `fieldNameReason` varchar(63) NOT NULL,
  `fieldNameCounterpartName` varchar(63) NOT NULL,
  `fieldNameCounterpartAccount` varchar(63) NOT NULL,
  `fieldNameCounterpartBankCode` varchar(63) NOT NULL,
  `fieldNameAmount` varchar(63) NOT NULL,
  `amountType` int(10) unsigned NOT NULL,
  `fieldNameAmountSign` varchar(63) NOT NULL,
  `SignPositive` varchar(7) NOT NULL,
  `SignNegative` varchar(7) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`bankBookId`,`bankAccountId`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_acct_book`;
CREATE TABLE `tiki_acct_book` (
  `bookId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bookName` varchar(255) NOT NULL,
  `bookClosed` enum('y','n') NOT NULL DEFAULT 'n',
  `bookStartDate` date NOT NULL,
  `bookEndDate` date NOT NULL,
  `bookCurrency` varchar(3) NOT NULL DEFAULT 'EUR',
  `bookCurrencyPos` int(11) NOT NULL,
  `bookDecimals` int(11) NOT NULL DEFAULT '2',
  `bookDecPoint` varchar(1) NOT NULL DEFAULT ',',
  `bookThousand` varchar(1) NOT NULL DEFAULT '.',
  `exportSeparator` varchar(4) NOT NULL DEFAULT ';',
  `exportEOL` varchar(4) NOT NULL DEFAULT 'LF',
  `exportQuote` varchar(4) NOT NULL DEFAULT '"',
  `bookAutoTax` enum('y','n') NOT NULL DEFAULT 'y',
  PRIMARY KEY (`bookId`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_acct_item`; 
CREATE TABLE `tiki_acct_item` (
  `itemBookId` int(10) unsigned NOT NULL,
  `itemJournalId` int(10) unsigned NOT NULL DEFAULT '0',
  `itemAccountId` int(10) unsigned NOT NULL DEFAULT '0',
  `itemType` int(1) NOT NULL DEFAULT '-1',
  `itemAmount` double NOT NULL DEFAULT '0',
  `itemText` varchar(255) NOT NULL DEFAULT '',
  `itemTs` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`itemBookId`,`itemJournalId`,`itemAccountId`,`itemType`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_acct_journal`; 
CREATE TABLE `tiki_acct_journal` (
  `journalBookId` int(10) unsigned NOT NULL,
  `journalId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `journalDate` date NOT NULL DEFAULT '0000-00-00',
  `journalDescription` varchar(255) NOT NULL,
  `journalCancelled` int(1) NOT NULL DEFAULT '0',
  `journalTs` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`journalBookId`,`journalId`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_acct_stack`; 
CREATE TABLE `tiki_acct_stack` (
  `stackBookId` int(10) unsigned NOT NULL,
  `stackId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `stackDate` date NOT NULL DEFAULT '0000-00-00',
  `stackDescription` varchar(255) NOT NULL,
  `stackTs` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`stackBookId`,`stackId`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_acct_stackitem`; 
CREATE TABLE `tiki_acct_stackitem` (
  `stackBookId` int(10) unsigned NOT NULL,
  `stackItemStackId` int(10) unsigned NOT NULL DEFAULT '0',
  `stackItemAccountId` int(10) unsigned NOT NULL DEFAULT '0',
  `stackItemType` int(1) NOT NULL DEFAULT '-1',
  `stackItemAmount` double NOT NULL DEFAULT '0',
  `stackItemText` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`stackBookId`,`stackItemStackId`,`stackItemAccountId`,`stackItemType`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_acct_statement`;
CREATE TABLE `tiki_acct_statement` (
  `statementBookId` int(10) unsigned NOT NULL,
  `statementAccountId` int(10) unsigned NOT NULL DEFAULT '0',
  `statementId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `statementBookingDate` date NOT NULL,
  `statementValueDate` date NOT NULL,
  `statementBookingText` varchar(255) NOT NULL,
  `statementReason` varchar(255) NOT NULL,
  `statementCounterpart` varchar(63) NOT NULL,
  `statementCounterpartAccount` varchar(63) NOT NULL,
  `statementCounterpartBankCode` varchar(63) NOT NULL,
  `statementAmount` double NOT NULL,
  `statementJournalId` int(10) unsigned NOT NULL DEFAULT '0',
  `statementStackId` int(11) NOT NULL,
  PRIMARY KEY (`statementBookId`,`statementAccountId`,`statementId`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_acct_tax`; 
CREATE TABLE `tiki_acct_tax` (
  `taxBookId` int(10) unsigned NOT NULL,
  `taxId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `taxText` varchar(63) NOT NULL,
  `taxAmount` double NOT NULL DEFAULT '0',
  `taxIsFix` enum('y','n') NOT NULL DEFAULT 'n',
  PRIMARY KEY (`taxBookId`,`taxId`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `tiki_queue`;
CREATE TABLE `tiki_queue` (
    `entryId` INT PRIMARY KEY AUTO_INCREMENT,
    `queue` VARCHAR(25) NOT NULL,
    `timestamp` INT NOT NULL,
    `handler` VARCHAR(20) NULL,
    `message` TEXT NOT NULL,
    KEY `queue_name_ix` (`queue`),
    KEY `queue_handler_ix` (`handler`)
) ENGINE=MyISAM;
