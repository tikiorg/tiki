set quoted_identifier on
go

-- $Rev$
-- $Date: 2008-03-16 00:06:59 $
-- $Author: nyloth $
-- $Name: not supported by cvs2svn $
-- phpMyAdmin MySQL-Dump
-- version 2.5.1
-- http://www.phpmyadmin.net/ (download page)
--
-- Host: localhost
-- Generation Time: Jul 13, 2003 at 02:09 AM
-- Server version: 4.0.13
-- PHP Version: 4.2.3
-- Database : tikiwiki
-- --------------------------------------------------------

--
-- Table structure for table galaxia_activities
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "galaxia_activities"
go


CREATE TABLE "galaxia_activities" (
  "activityId" numeric(14 ,0) identity,
  "name" varchar(80) default NULL NULL,
  "normalized_name" varchar(80) default NULL NULL,
  "pId" numeric(14,0) default '0' NOT NULL,
  "type" varchar(12) default NULL NULL CHECK ("type" IN ('start','end','split','switch','join','activity','standalone')),
  "isAutoRouted" char(1) default NULL NULL,
  "flowNum" numeric(10,0) default NULL NULL,
  "isInteractive" char(1) default NULL NULL,
  "lastModif" numeric(14,0) default NULL NULL,
  "description" text default '',
  "expirationTime" numeric(6,0) default '0' NOT NULL,
  PRIMARY KEY ("activityId")
) ENGINE=MyISAM  
go


-- --------------------------------------------------------

--
-- Table structure for table galaxia_activity_roles
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "galaxia_activity_roles"
go


CREATE TABLE "galaxia_activity_roles" (
  "activityId" numeric(14,0) default '0' NOT NULL,
  "roleId" numeric(14,0) default '0' NOT NULL,
  PRIMARY KEY ("activityId","roleId")
) ENGINE=MyISAM
go


-- --------------------------------------------------------

--
-- Table structure for table galaxia_instance_activities
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "galaxia_instance_activities"
go


CREATE TABLE "galaxia_instance_activities" (
  "instanceId" numeric(14,0) default '0' NOT NULL,
  "activityId" numeric(14,0) default '0' NOT NULL,
  "started" numeric(14,0) default '0' NOT NULL,
  "ended" numeric(14,0) default '0' NOT NULL,
  "user" varchar(200) default '',
  "status" varchar(11) default NULL NULL CHECK ("status" IN ('running','completed')),
  PRIMARY KEY ("instanceId","activityId")
) ENGINE=MyISAM
go


-- --------------------------------------------------------

--
-- Table structure for table galaxia_instance_comments
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "galaxia_instance_comments"
go


CREATE TABLE "galaxia_instance_comments" (
  "cId" numeric(14 ,0) identity,
  "instanceId" numeric(14,0) default '0' NOT NULL,
  "user" varchar(200) default '',
  "activityId" numeric(14,0) default NULL NULL,
  "hash" varchar(34) default NULL NULL,
  "title" varchar(250) default NULL NULL,
  "comment" text default '',
  "activity" varchar(80) default NULL NULL,
  "timestamp" numeric(14,0) default NULL NULL,
  PRIMARY KEY ("cId")
) ENGINE=MyISAM  
go


-- --------------------------------------------------------

--
-- Table structure for table galaxia_instances
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "galaxia_instances"
go


CREATE TABLE "galaxia_instances" (
  "instanceId" numeric(14 ,0) identity,
  "pId" numeric(14,0) default '0' NOT NULL,
  "started" numeric(14,0) default NULL NULL,
  "name" varchar(200) default 'No Name' NOT NULL,
  "owner" varchar(200) default NULL NULL,
  "nextActivity" numeric(14,0) default NULL NULL,
  "nextUser" varchar(200) default NULL NULL,
  "ended" numeric(14,0) default NULL NULL,
  "status" varchar(11) default NULL NULL CHECK ("status" IN ('active','exception','aborted','completed')),
  "properties" image default '',
  PRIMARY KEY ("instanceId")
) ENGINE=MyISAM  
go


-- --------------------------------------------------------

--
-- Table structure for table galaxia_processes
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "galaxia_processes"
go


CREATE TABLE "galaxia_processes" (
  "pId" numeric(14 ,0) identity,
  "name" varchar(80) default NULL NULL,
  "isValid" char(1) default NULL NULL,
  "isActive" char(1) default NULL NULL,
  "version" varchar(12) default NULL NULL,
  "description" text default '',
  "lastModif" numeric(14,0) default NULL NULL,
  "normalized_name" varchar(80) default NULL NULL,
  PRIMARY KEY ("pId")
) ENGINE=MyISAM  
go


-- --------------------------------------------------------

--
-- Table structure for table galaxia_roles
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "galaxia_roles"
go


CREATE TABLE "galaxia_roles" (
  "roleId" numeric(14 ,0) identity,
  "pId" numeric(14,0) default '0' NOT NULL,
  "lastModif" numeric(14,0) default NULL NULL,
  "name" varchar(80) default NULL NULL,
  "description" text default '',
  PRIMARY KEY ("roleId")
) ENGINE=MyISAM  
go


-- --------------------------------------------------------

--
-- Table structure for table galaxia_transitions
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "galaxia_transitions"
go


CREATE TABLE "galaxia_transitions" (
  "pId" numeric(14,0) default '0' NOT NULL,
  "actFromId" numeric(14,0) default '0' NOT NULL,
  "actToId" numeric(14,0) default '0' NOT NULL,
  PRIMARY KEY ("actFromId","actToId")
) ENGINE=MyISAM
go


-- --------------------------------------------------------

--
-- Table structure for table galaxia_user_roles
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "galaxia_user_roles"
go


CREATE TABLE "galaxia_user_roles" (
  "pId" numeric(14,0) default '0' NOT NULL,
  "roleId" numeric(14 ,0) identity,
  "user" varchar(200) default '' NOT NULL,
  PRIMARY KEY ("roleId","user")
) ENGINE=MyISAM  
go


-- --------------------------------------------------------

--
-- Table structure for table galaxia_workitems
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "galaxia_workitems"
go


CREATE TABLE "galaxia_workitems" (
  "itemId" numeric(14 ,0) identity,
  "instanceId" numeric(14,0) default '0' NOT NULL,
  "orderId" numeric(14,0) default '0' NOT NULL,
  "activityId" numeric(14,0) default '0' NOT NULL,
  "properties" image default '',
  "started" numeric(14,0) default NULL NULL,
  "ended" numeric(14,0) default NULL NULL,
  "user" varchar(200) default '',
  PRIMARY KEY ("itemId")
) ENGINE=MyISAM  
go


-- --------------------------------------------------------

--
-- Table structure for table messu_messages
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 08:29 PM
--

-- DROP TABLE "messu_messages"
go


CREATE TABLE "messu_messages" (
  "msgId" numeric(14 ,0) identity,
  "user" varchar(200) default '' NOT NULL,
  "user_from" varchar(200) default '' NOT NULL,
  "user_to" text default '',
  "user_cc" text default '',
  "user_bcc" text default '',
  "subject" varchar(255) default NULL NULL,
  "body" text default '',
  "hash" varchar(32) default NULL NULL,
  "replyto_hash" varchar(32) default NULL NULL,
  "date" numeric(14,0) default NULL NULL,
  "isRead" char(1) default NULL NULL,
  "isReplied" char(1) default NULL NULL,
  "isFlagged" char(1) default NULL NULL,
  "priority" numeric(2,0) default NULL NULL,
  PRIMARY KEY ("msgId")
) ENGINE=MyISAM  
go


CREATE  INDEX "messu_messages_userIsRead" ON "messu_messages"("user" "isRead")
go
-- --------------------------------------------------------

--
-- Table structure for table messu_archive (same structure as messu_messages)
-- desc: user may archive his messages to this table to speed up default msg handling
--
-- Creation: Feb 26, 2005 at 03:00 PM
-- Last update: Feb 26, 2005 at 03:00 PM
--

-- DROP TABLE "messu_archive"
go


CREATE TABLE "messu_archive" (
  "msgId" numeric(14 ,0) identity,
  "user" varchar(40) default '' NOT NULL,
  "user_from" varchar(40) default '' NOT NULL,
  "user_to" text default '',
  "user_cc" text default '',
  "user_bcc" text default '',
  "subject" varchar(255) default NULL NULL,
  "body" text default '',
  "hash" varchar(32) default NULL NULL,
  "replyto_hash" varchar(32) default NULL NULL,
  "date" numeric(14,0) default NULL NULL,
  "isRead" char(1) default NULL NULL,
  "isReplied" char(1) default NULL NULL,
  "isFlagged" char(1) default NULL NULL,
  "priority" numeric(2,0) default NULL NULL,
  PRIMARY KEY ("msgId")
) ENGINE=MyISAM  
go


-- --------------------------------------------------------

--
-- Table structure for table messu_sent (same structure as messu_messages)
-- desc: user may archive his messages to this table to speed up default msg handling
--
-- Creation: Feb 26, 2005 at 11:00 PM
-- Last update: Feb 26, 2005 at 11:00 PM
--

-- DROP TABLE "messu_sent"
go


CREATE TABLE "messu_sent" (
  "msgId" numeric(14 ,0) identity,
  "user" varchar(40) default '' NOT NULL,
  "user_from" varchar(40) default '' NOT NULL,
  "user_to" text default '',
  "user_cc" text default '',
  "user_bcc" text default '',
  "subject" varchar(255) default NULL NULL,
  "body" text default '',
  "hash" varchar(32) default NULL NULL,
  "replyto_hash" varchar(32) default NULL NULL,
  "date" numeric(14,0) default NULL NULL,
  "isRead" char(1) default NULL NULL,
  "isReplied" char(1) default NULL NULL,
  "isFlagged" char(1) default NULL NULL,
  "priority" numeric(2,0) default NULL NULL,
  PRIMARY KEY ("msgId")
) ENGINE=MyISAM  
go


-- --------------------------------------------------------

-- DROP TABLE "sessions"
go


CREATE TABLE "sessions"(
  "sesskey" char(32) NOT NULL,
  "expiry" numeric(11,0) NOT NULL,
  "expireref" varchar(64) default '',
  "data" text NOT NULL,
  PRIMARY KEY ("sesskey")
) ENGINE=MyISAM
go


CREATE  INDEX "sessions_expiry" ON "sessions"("expiry")
go

--
-- Table structure for table tiki_actionlog
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 13, 2003 at 12:29 AM
--

-- DROP TABLE "tiki_actionlog"
go


CREATE TABLE "tiki_actionlog" (
  "actionId" numeric(8 ,0) identity,
  "action" varchar(255) default '' NOT NULL,
  "lastModif" numeric(14,0) default NULL NULL,
  "object" varchar(255) default NULL NULL,
  "objectType" varchar(32) default '' NOT NULL,
  "user" varchar(200) default '',
  "ip" varchar(15) default NULL NULL,
  "comment" varchar(200) default NULL NULL,
  "categId" numeric(12,0) default '0' NOT NULL,
  PRIMARY KEY ("actionId")
) ENGINE=MyISAM
go



-- DROP TABLE "tiki_actionlog_params"
go


CREATE TABLE "tiki_actionlog_params" (
  "actionId" numeric(8,0) NOT NULL,
  "name" varchar(40) NOT NULL,
  "value" text default '',
  KEY (actionId)
) ENGINE=MyISAM
go


CREATE  INDEX "tiki_actionlog_params_nameValue" ON "tiki_actionlog_params"("name" "value")
go
-- --------------------------------------------------------

--
-- Table structure for table tiki_articles
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Nov 27, 2006 at 21:53 PM
-- Last check: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_articles"
go


CREATE TABLE "tiki_articles" (
  "articleId" numeric(8 ,0) identity,
  "topline" varchar(255) default NULL NULL,
  "title" varchar(255) default NULL NULL,
  "subtitle" varchar(255) default NULL NULL,
  "linkto" varchar(255) default NULL NULL,
  "lang" varchar(16) default NULL NULL,
  "state" char(1) default 's',
  "authorName" varchar(60) default NULL NULL,
  "topicId" numeric(14,0) default NULL NULL,
  "topicName" varchar(40) default NULL NULL,
  "size" numeric(12,0) default NULL NULL,
  "useImage" char(1) default NULL NULL,
  "image_name" varchar(80) default NULL NULL,
  "image_caption" text default NULL NULL,
  "image_type" varchar(80) default NULL NULL,
  "image_size" numeric(14,0) default NULL NULL,
  "image_x" numeric(4,0) default NULL NULL,
  "image_y" numeric(4,0) default NULL NULL,
  "image_data" image default '',
  "publishDate" numeric(14,0) default NULL NULL,
  "expireDate" numeric(14,0) default NULL NULL,
  "created" numeric(14,0) default NULL NULL,
  "heading" text default '',
  "body" text default '',
  "hash" varchar(32) default NULL NULL,
  "author" varchar(200) default NULL NULL,
  "nbreads" numeric(14,0) default NULL NULL,
  "votes" numeric(8,0) default NULL NULL,
  "points" numeric(14,0) default NULL NULL,
  "type" varchar(50) default NULL NULL,
  "rating" decimal(3,2) default NULL NULL,
  "isfloat" char(1) default NULL NULL,
  PRIMARY KEY ("articleId")
) ENGINE=MyISAM  
go


CREATE  INDEX "tiki_articles_title" ON "tiki_articles"("title")
go
CREATE  INDEX "tiki_articles_heading" ON "tiki_articles"("heading")
go
CREATE  INDEX "tiki_articles_body" ON "tiki_articles"("body")
go
CREATE  INDEX "tiki_articles_author" ON "tiki_articles"("author")
go
CREATE  INDEX "tiki_articles_nbreads" ON "tiki_articles"("nbreads")
go
CREATE  INDEX "tiki_articles_topicId" ON "tiki_articles"("topicId")
go
CREATE  INDEX "tiki_articles_publishDate" ON "tiki_articles"("publishDate")
go
CREATE  INDEX "tiki_articles_expireDate" ON "tiki_articles"("expireDate")
go
CREATE  INDEX "tiki_articles_type" ON "tiki_articles"("type")
go
CREATE  INDEX "tiki_articles_ft" ON "tiki_articles"("title","heading","body")
go
-- --------------------------------------------------------

-- DROP TABLE "tiki_article_types"
go


CREATE TABLE "tiki_article_types" (
  "type" varchar(50) NOT NULL,
  "use_ratings" varchar(1) default NULL NULL,
  "show_pre_publ" varchar(1) default NULL NULL,
  "show_post_expire" varchar(1) default 'y',
  "heading_only" varchar(1) default NULL NULL,
  "allow_comments" varchar(1) default 'y',
  "show_image" varchar(1) default 'y',
  "show_avatar" varchar(1) default NULL NULL,
  "show_author" varchar(1) default 'y',
  "show_pubdate" varchar(1) default 'y',
  "show_expdate" varchar(1) default NULL NULL,
  "show_reads" varchar(1) default 'y',
  "show_size" varchar(1) default 'n',
  "show_topline" varchar(1) default 'n',
  "show_subtitle" varchar(1) default 'n',
  "show_linkto" varchar(1) default 'n',
  "show_image_caption" varchar(1) default 'n',
  "show_lang" varchar(1) default 'n',
  "creator_edit" varchar(1) default NULL NULL,
  "comment_can_rate_article" char(1) default NULL NULL,
  PRIMARY KEY ("type")
) ENGINE=MyISAM 
go


CREATE  INDEX "tiki_article_types_show_pre_publ" ON "tiki_article_types"("show_pre_publ")
go
CREATE  INDEX "tiki_article_types_show_post_expire" ON "tiki_article_types"("show_post_expire")
go

INSERT INTO "tiki_article_types" ("type") VALUES ('Article')
go


INSERT INTO "tiki_article_types" ("type","use_ratings") VALUES ('Review','y')
go


INSERT INTO "tiki_article_types" ("type","show_post_expire") VALUES ('Event','n')
go


INSERT INTO "tiki_article_types" ("type","show_post_expire","heading_only","allow_comments") VALUES ('Classified','n','y','n')
go



--
-- Table structure for table tiki_banners
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_banners"
go


CREATE TABLE "tiki_banners" (
  "bannerId" numeric(12 ,0) identity,
  "client" varchar(200) default '' NOT NULL,
  "url" varchar(255) default NULL NULL,
  "title" varchar(255) default NULL NULL,
  "alt" varchar(250) default NULL NULL,
  "which" varchar(50) default NULL NULL,
  "imageData" image default '',
  "imageType" varchar(200) default NULL NULL,
  "imageName" varchar(100) default NULL NULL,
  "HTMLData" text default '',
  "fixedURLData" varchar(255) default NULL NULL,
  "textData" text default '',
  "fromDate" numeric(14,0) default NULL NULL,
  "toDate" numeric(14,0) default NULL NULL,
  "useDates" char(1) default NULL NULL,
  "mon" char(1) default NULL NULL,
  "tue" char(1) default NULL NULL,
  "wed" char(1) default NULL NULL,
  "thu" char(1) default NULL NULL,
  "fri" char(1) default NULL NULL,
  "sat" char(1) default NULL NULL,
  "sun" char(1) default NULL NULL,
  "hourFrom" varchar(4) default NULL NULL,
  "hourTo" varchar(4) default NULL NULL,
  "created" numeric(14,0) default NULL NULL,
  "maxImpressions" numeric(8,0) default NULL NULL,
  "impressions" numeric(8,0) default NULL NULL,
  "clicks" numeric(8,0) default NULL NULL,
  "zone" varchar(40) default NULL NULL,
  PRIMARY KEY ("bannerId")
) ENGINE=MyISAM  
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_banning
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_banning"
go


CREATE TABLE "tiki_banning" (
  "banId" numeric(12 ,0) identity,
  "mode" varchar(6) default NULL NULL CHECK ("mode" IN ('user','ip')),
  "title" varchar(200) default NULL NULL,
  "ip1" char(3) default NULL NULL,
  "ip2" char(3) default NULL NULL,
  "ip3" char(3) default NULL NULL,
  "ip4" char(3) default NULL NULL,
  "user" varchar(200) default '',
  "date_from" timestamp NOT NULL,
  "date_to" timestamp NOT NULL,
  "use_dates" char(1) default NULL NULL,
  "created" numeric(14,0) default NULL NULL,
  "message" text default '',
  PRIMARY KEY ("banId")
) ENGINE=MyISAM  
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_banning_sections
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_banning_sections"
go


CREATE TABLE "tiki_banning_sections" (
  "banId" numeric(12,0) default '0' NOT NULL,
  "section" varchar(100) default '' NOT NULL,
  PRIMARY KEY ("banId","section")
) ENGINE=MyISAM
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_blog_activity
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 04:52 PM
--

-- DROP TABLE "tiki_blog_activity"
go


CREATE TABLE "tiki_blog_activity" (
  "blogId" numeric(8,0) default '0' NOT NULL,
  "day" numeric(14,0) default '0' NOT NULL,
  "posts" numeric(8,0) default NULL NULL,
  PRIMARY KEY ("blogId","day")
) ENGINE=MyISAM
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_blog_posts
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 04:52 PM
-- Last check: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_blog_posts"
go


CREATE TABLE "tiki_blog_posts" (
  "postId" numeric(8 ,0) identity,
  "blogId" numeric(8,0) default '0' NOT NULL,
  "data" text default '',
  "data_size" numeric(11,0) default '0' NOT NULL,
  "created" numeric(14,0) default NULL NULL,
  "user" varchar(200) default '',
  "trackbacks_to" text default '',
  "trackbacks_from" text default '',
  "title" varchar(255) default NULL NULL,
  "priv" varchar(1) default NULL NULL,
  PRIMARY KEY ("postId")
) ENGINE=MyISAM  
go


CREATE  INDEX "tiki_blog_posts_data" ON "tiki_blog_posts"("data")
go
CREATE  INDEX "tiki_blog_posts_blogId" ON "tiki_blog_posts"("blogId")
go
CREATE  INDEX "tiki_blog_posts_created" ON "tiki_blog_posts"("created")
go
CREATE  INDEX "tiki_blog_posts_ft" ON "tiki_blog_posts"("data","title")
go
-- --------------------------------------------------------

--
-- Table structure for table tiki_blog_posts_images
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_blog_posts_images"
go


CREATE TABLE "tiki_blog_posts_images" (
  "imgId" numeric(14 ,0) identity,
  "postId" numeric(14,0) default '0' NOT NULL,
  "filename" varchar(80) default NULL NULL,
  "filetype" varchar(80) default NULL NULL,
  "filesize" numeric(14,0) default NULL NULL,
  "data" image default '',
  PRIMARY KEY ("imgId")
) ENGINE=MyISAM  
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_blogs
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 13, 2003 at 01:07 AM
-- Last check: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_blogs"
go


CREATE TABLE "tiki_blogs" (
  "blogId" numeric(8 ,0) identity,
  "created" numeric(14,0) default NULL NULL,
  "lastModif" numeric(14,0) default NULL NULL,
  "title" varchar(200) default NULL NULL,
  "description" text default '',
  "user" varchar(200) default '',
  "public" char(1) default NULL NULL,
  "posts" numeric(8,0) default NULL NULL,
  "maxPosts" numeric(8,0) default NULL NULL,
  "hits" numeric(8,0) default NULL NULL,
  "activity" decimal(4,2) default NULL NULL,
  "heading" text default '',
  "use_find" char(1) default NULL NULL,
  "use_title" char(1) default NULL NULL,
  "add_date" char(1) default NULL NULL,
  "add_poster" char(1) default NULL NULL,
  "allow_comments" char(1) default NULL NULL,
  "show_avatar" char(1) default NULL NULL,
  PRIMARY KEY ("blogId")
) ENGINE=MyISAM  
go


CREATE  INDEX "tiki_blogs_title" ON "tiki_blogs"("title")
go
CREATE  INDEX "tiki_blogs_description" ON "tiki_blogs"("description")
go
CREATE  INDEX "tiki_blogs_hits" ON "tiki_blogs"("hits")
go
CREATE  INDEX "tiki_blogs_ft" ON "tiki_blogs"("title","description")
go
-- --------------------------------------------------------

--
-- Table structure for table tiki_calendar_categories
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 07:05 AM
--

-- DROP TABLE "tiki_calendar_categories"
go


CREATE TABLE "tiki_calendar_categories" (
  "calcatId" numeric(11 ,0) identity,
  "calendarId" numeric(14,0) default '0' NOT NULL,
  "name" varchar(255) default '' NOT NULL,
  PRIMARY KEY ("calcatId")
) ENGINE=MyISAM  
go


CREATE UNIQUE INDEX "tiki_calendar_categories_catname" ON "tiki_calendar_categories"("calendarId","name")
go
-- --------------------------------------------------------

--
-- Table structure for table tiki_calendar_items
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 07:43 AM
--

-- DROP TABLE "tiki_calendar_items"
go


CREATE TABLE "tiki_calendar_items" (
  "calitemId" numeric(14 ,0) identity,
  "calendarId" numeric(14,0) default '0' NOT NULL,
  "start" numeric(14,0) default '0' NOT NULL,
  "end" numeric(14,0) default '0' NOT NULL,
  "locationId" numeric(14,0) default NULL NULL,
  "categoryId" numeric(14,0) default NULL NULL,
  "nlId" numeric(12,0) default '0' NOT NULL,
  "priority" varchar(3) default '1' NOT NULL CHECK ("priority" IN ('1','2','3','4','5','6','7','8','9')),
  "status" varchar(3) default '0' NOT NULL CHECK ("status" IN ('0','1','2')),
  "url" varchar(255) default NULL NULL,
  "lang" char(16) default 'en' NOT NULL,
  "name" varchar(255) default '' NOT NULL,
  "description" image default '',
  "user" varchar(200) default '',
  "created" numeric(14,0) default '0' NOT NULL,
  "lastmodif" numeric(14,0) default '0' NOT NULL,
  PRIMARY KEY ("calitemId")
) ENGINE=MyISAM  
go


CREATE  INDEX "tiki_calendar_items_calendarId" ON "tiki_calendar_items"("calendarId")
go
-- --------------------------------------------------------

--
-- Table structure for table tiki_calendar_locations
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 07:05 AM
--

-- DROP TABLE "tiki_calendar_locations"
go


CREATE TABLE "tiki_calendar_locations" (
  "callocId" numeric(14 ,0) identity,
  "calendarId" numeric(14,0) default '0' NOT NULL,
  "name" varchar(255) default '' NOT NULL,
  "description" image default '',
  PRIMARY KEY ("callocId")
) ENGINE=MyISAM  
go


CREATE UNIQUE INDEX "tiki_calendar_locations_locname" ON "tiki_calendar_locations"("calendarId","name")
go
-- --------------------------------------------------------

--
-- Table structure for table tiki_calendar_roles
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_calendar_roles"
go


CREATE TABLE "tiki_calendar_roles" (
  "calitemId" numeric(14,0) default '0' NOT NULL,
  "username" varchar(200) default '' NOT NULL,
  "role" varchar(3) default '0' NOT NULL CHECK ("role" IN ('0','1','2','3','6')),
  PRIMARY KEY ("calitemId","username","role")
) ENGINE=MyISAM
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_calendars
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 05, 2003 at 02:03 PM
--

-- DROP TABLE "tiki_calendars"
go


CREATE TABLE "tiki_calendars" (
  "calendarId" numeric(14 ,0) identity,
  "name" varchar(80) default '' NOT NULL,
  "description" varchar(255) default NULL NULL,
  "user" varchar(200) default '' NOT NULL,
  "customlocations" varchar(3) default 'n' NOT NULL CHECK ("customlocations" IN ('n','y')),
  "customcategories" varchar(3) default 'n' NOT NULL CHECK ("customcategories" IN ('n','y')),
  "customlanguages" varchar(3) default 'n' NOT NULL CHECK ("customlanguages" IN ('n','y')),
  "custompriorities" varchar(3) default 'n' NOT NULL CHECK ("custompriorities" IN ('n','y')),
  "customparticipants" varchar(3) default 'n' NOT NULL CHECK ("customparticipants" IN ('n','y')),
  "customsubscription" varchar(3) default 'n' NOT NULL CHECK ("customsubscription" IN ('n','y')),
  "created" numeric(14,0) default '0' NOT NULL,
  "lastmodif" numeric(14,0) default '0' NOT NULL,
  "personal" enum ('n', 'y') default 'n' NOT NULL,
  PRIMARY KEY ("calendarId")
) ENGINE=MyISAM 
go


-- --------------------------------------------------------

-- DROP TABLE "tiki_calendar_options"
go


CREATE TABLE "tiki_calendar_options" (
  "calendarId" numeric(14,0) default 0 NOT NULL,
  "optionName" varchar(120) default '' NOT NULL,
  "value" varchar(255) default '',
  PRIMARY KEY (calendarId,optionName)
) ENGINE=MyISAM 
go


-- --------------------------------------------------------
--
-- Table structure for table tiki_categories
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 04, 2003 at 09:47 PM
--

-- DROP TABLE "tiki_categories"
go


CREATE TABLE "tiki_categories" (
  "categId" numeric(12 ,0) identity,
  "name" varchar(100) default NULL NULL,
  "description" varchar(250) default NULL NULL,
  "parentId" numeric(12,0) default NULL NULL,
  "hits" numeric(8,0) default NULL NULL,
  PRIMARY KEY ("categId")
) ENGINE=MyISAM  
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_categorized_objects
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Dec 06, 2005 
--

-- DROP TABLE "tiki_objects"
go


CREATE TABLE "tiki_objects" (
  "objectId" numeric(12 ,0) identity,
  "type" varchar(50) default NULL NULL,
  "itemId" varchar(255) default NULL NULL,
  "description" text default '',
  "created" numeric(14,0) default NULL NULL,
  "name" varchar(200) default NULL NULL,
  "href" varchar(200) default NULL NULL,
  "hits" numeric(8,0) default NULL NULL,
  PRIMARY KEY ("objectId")
  KEY (type, objectId),
  KEY (itemId, type)
) ENGINE=MyISAM  
go


-- --------------------------------------------------------

-- Table structure for table `tiki_categorized_objects`
--

-- DROP TABLE `tiki_categorized_objects`
go


CREATE TABLE `tiki_categorized_objects` (
  `catObjectId` numeric(11,0) default '0' NOT NULL,
  PRIMARY KEY ("`catObjectId`")
) ENGINE=MyISAM 
go




--
-- Table structure for table tiki_category_objects
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 13, 2003 at 01:09 AM
--

-- DROP TABLE "tiki_category_objects"
go


CREATE TABLE "tiki_category_objects" (
  "catObjectId" numeric(12,0) default '0' NOT NULL,
  "categId" numeric(12,0) default '0' NOT NULL,
  PRIMARY KEY ("catObjectId","categId")
) ENGINE=MyISAM
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_category_sites
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 07, 2003 at 01:53 AM
--

-- DROP TABLE "tiki_object_ratings"
go


CREATE TABLE "tiki_object_ratings" (
  "catObjectId" numeric(12,0) default '0' NOT NULL,
  "pollId" numeric(12,0) default '0' NOT NULL,
  PRIMARY KEY ("catObjectId","pollId")
) ENGINE=MyISAM
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_category_sites
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 07, 2003 at 01:53 AM
--

-- DROP TABLE "tiki_category_sites"
go


CREATE TABLE "tiki_category_sites" (
  "categId" numeric(10,0) default '0' NOT NULL,
  "siteId" numeric(14,0) default '0' NOT NULL,
  PRIMARY KEY ("categId","siteId")
) ENGINE=MyISAM
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_chart_items
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_chart_items"
go


CREATE TABLE "tiki_chart_items" (
  "itemId" numeric(14 ,0) identity,
  "title" varchar(250) default NULL NULL,
  "description" text default '',
  "chartId" numeric(14,0) default '0' NOT NULL,
  "created" numeric(14,0) default NULL NULL,
  "URL" varchar(250) default NULL NULL,
  "votes" numeric(14,0) default NULL NULL,
  "points" numeric(14,0) default NULL NULL,
  "average" decimal(4,2) default NULL NULL,
  PRIMARY KEY ("itemId")
) ENGINE=MyISAM  
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_charts
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 06, 2003 at 08:14 AM
--

-- DROP TABLE "tiki_charts"
go


CREATE TABLE "tiki_charts" (
  "chartId" numeric(14 ,0) identity,
  "title" varchar(250) default NULL NULL,
  "description" text default '',
  "hits" numeric(14,0) default NULL NULL,
  "singleItemVotes" char(1) default NULL NULL,
  "singleChartVotes" char(1) default NULL NULL,
  "suggestions" char(1) default NULL NULL,
  "autoValidate" char(1) default NULL NULL,
  "topN" numeric(6,0) default NULL NULL,
  "maxVoteValue" numeric(4,0) default NULL NULL,
  "frequency" numeric(14,0) default NULL NULL,
  "showAverage" char(1) default NULL NULL,
  "isActive" char(1) default NULL NULL,
  "showVotes" char(1) default NULL NULL,
  "useCookies" char(1) default NULL NULL,
  "lastChart" numeric(14,0) default NULL NULL,
  "voteAgainAfter" numeric(14,0) default NULL NULL,
  "created" numeric(14,0) default NULL NULL,
  PRIMARY KEY ("chartId")
) ENGINE=MyISAM  
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_charts_rankings
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_charts_rankings"
go


CREATE TABLE "tiki_charts_rankings" (
  "chartId" numeric(14,0) default '0' NOT NULL,
  "itemId" numeric(14,0) default '0' NOT NULL,
  "position" numeric(14,0) default '0' NOT NULL,
  "timestamp" numeric(14,0) default '0' NOT NULL,
  "lastPosition" numeric(14,0) default '0' NOT NULL,
  "period" numeric(14,0) default '0' NOT NULL,
  "rvotes" numeric(14,0) default '0' NOT NULL,
  "raverage" decimal(4,2) default '0.00' NOT NULL,
  PRIMARY KEY ("chartId","itemId","period")
) ENGINE=MyISAM
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_charts_votes
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_charts_votes"
go


CREATE TABLE "tiki_charts_votes" (
  "user" varchar(200) default '' NOT NULL,
  "itemId" numeric(14,0) default '0' NOT NULL,
  "timestamp" numeric(14,0) default NULL NULL,
  "chartId" numeric(14,0) default NULL NULL,
  PRIMARY KEY ("user","itemId")
) ENGINE=MyISAM
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_chat_channels
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_chat_channels"
go


CREATE TABLE "tiki_chat_channels" (
  "channelId" numeric(8 ,0) identity,
  "name" varchar(30) default NULL NULL,
  "description" varchar(250) default NULL NULL,
  "max_users" numeric(8,0) default NULL NULL,
  "mode" char(1) default NULL NULL,
  "moderator" varchar(200) default NULL NULL,
  "active" char(1) default NULL NULL,
  "refresh" numeric(6,0) default NULL NULL,
  PRIMARY KEY ("channelId")
) ENGINE=MyISAM  
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_chat_messages
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_chat_messages"
go


CREATE TABLE "tiki_chat_messages" (
  "messageId" numeric(8 ,0) identity,
  "channelId" numeric(8,0) default '0' NOT NULL,
  "data" varchar(255) default NULL NULL,
  "poster" varchar(200) default 'anonymous' NOT NULL,
  "timestamp" numeric(14,0) default NULL NULL,
  PRIMARY KEY ("messageId")
) ENGINE=MyISAM  
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_chat_users
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_chat_users"
go


CREATE TABLE "tiki_chat_users" (
  "nickname" varchar(200) default '' NOT NULL,
  "channelId" numeric(8,0) default '0' NOT NULL,
  "timestamp" numeric(14,0) default NULL NULL,
  PRIMARY KEY ("nickname","channelId")
) ENGINE=MyISAM
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_comments
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 10:56 PM
-- Last check: Jul 11, 2003 at 01:52 AM
--

-- DROP TABLE "tiki_comments"
go


CREATE TABLE "tiki_comments" (
  "threadId" numeric(14 ,0) identity,
  "object" varchar(255) default '' NOT NULL,
  "objectType" varchar(32) default '' NOT NULL,
  "parentId" numeric(14,0) default NULL NULL,
  "userName" varchar(200) default '',
  "commentDate" numeric(14,0) default NULL NULL,
  "hits" numeric(8,0) default NULL NULL,
  "type" char(1) default NULL NULL,
  "points" decimal(8,2) default NULL NULL,
  "votes" numeric(8,0) default NULL NULL,
  "average" decimal(8,4) default NULL NULL,
  "title" varchar(255) default NULL NULL,
  "data" text default '',
  "hash" varchar(32) default NULL NULL,
  "user_ip" varchar(15) default NULL NULL,
  "summary" varchar(240) default NULL NULL,
  "smiley" varchar(80) default NULL NULL,
  "message_id" varchar(128) default NULL NULL,
  "in_reply_to" varchar(128) default NULL NULL,
  "comment_rating" numeric(2,0) default NULL NULL,
  "archived" char(1) default NULL NULL,
  PRIMARY KEY ("threadId")
) ENGINE=MyISAM  
go


CREATE  INDEX "tiki_comments_title" ON "tiki_comments"("title")
go
CREATE  INDEX "tiki_comments_data" ON "tiki_comments"("data")
go
CREATE  INDEX "tiki_comments_tc_pi" ON "tiki_comments"("parentId")
go
CREATE  INDEX "tiki_comments_objectType" ON "tiki_comments"("object" "objectType")
go
CREATE  INDEX "tiki_comments_commentDate" ON "tiki_comments"("commentDate")
go
CREATE  INDEX "tiki_comments_hits" ON "tiki_comments"("hits")
go
CREATE  INDEX "tiki_comments_threaded" ON "tiki_comments"("message_id" "in_reply_to" "parentId")
go
CREATE  INDEX "tiki_comments_ft" ON "tiki_comments"("title","data")
go
CREATE UNIQUE INDEX "tiki_comments_no_repeats" ON "tiki_comments"("parentId" "userName" "title" "commentDate" "message_id" "in_reply_to")
go
-- --------------------------------------------------------

--
-- Table structure for table tiki_content
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_content"
go


CREATE TABLE "tiki_content" (
  "contentId" numeric(8 ,0) identity,
  "description" text default '',
  "contentLabel" varchar(255) default '' NOT NULL,
  PRIMARY KEY ("contentId")
) ENGINE=MyISAM  
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_content_templates
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 12:37 AM
--

-- DROP TABLE "tiki_content_templates"
go


CREATE TABLE "tiki_content_templates" (
  "templateId" numeric(10 ,0) identity,
  "content" image default '',
  "name" varchar(200) default NULL NULL,
  "created" numeric(14,0) default NULL NULL,
  PRIMARY KEY ("templateId")
) ENGINE=MyISAM  
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_content_templates_sections
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 12:37 AM
--

-- DROP TABLE "tiki_content_templates_sections"
go


CREATE TABLE "tiki_content_templates_sections" (
  "templateId" numeric(10,0) default '0' NOT NULL,
  "section" varchar(250) default '' NOT NULL,
  PRIMARY KEY ("templateId","section")
) ENGINE=MyISAM
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_cookies
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 10, 2003 at 04:00 AM
--

-- DROP TABLE "tiki_cookies"
go


CREATE TABLE "tiki_cookies" (
  "cookieId" numeric(10 ,0) identity,
  "cookie" text default '',
  PRIMARY KEY ("cookieId")
) ENGINE=MyISAM  
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_copyrights
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_copyrights"
go


CREATE TABLE "tiki_copyrights" (
  "copyrightId" numeric(12 ,0) identity,
  "page" varchar(200) default NULL NULL,
  "title" varchar(200) default NULL NULL,
  "year" numeric(11,0) default NULL NULL,
  "authors" varchar(200) default NULL NULL,
  "copyright_order" numeric(11,0) default NULL NULL,
  "userName" varchar(200) default '',
  PRIMARY KEY ("copyrightId")
) ENGINE=MyISAM  
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_directory_categories
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 08:59 PM
--

-- DROP TABLE "tiki_directory_categories"
go


CREATE TABLE "tiki_directory_categories" (
  "categId" numeric(10 ,0) identity,
  "parent" numeric(10,0) default NULL NULL,
  "name" varchar(240) default NULL NULL,
  "description" text default '',
  "childrenType" char(1) default NULL NULL,
  "sites" numeric(10,0) default NULL NULL,
  "viewableChildren" numeric(4,0) default NULL NULL,
  "allowSites" char(1) default NULL NULL,
  "showCount" char(1) default NULL NULL,
  "editorGroup" varchar(200) default NULL NULL,
  "hits" numeric(12,0) default NULL NULL,
  PRIMARY KEY ("categId")
) ENGINE=MyISAM  
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_directory_search
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_directory_search"
go


CREATE TABLE "tiki_directory_search" (
  "term" varchar(250) default '' NOT NULL,
  "hits" numeric(14,0) default NULL NULL,
  PRIMARY KEY ("term")
) ENGINE=MyISAM
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_directory_sites
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 07:32 PM
--

-- DROP TABLE "tiki_directory_sites"
go


CREATE TABLE "tiki_directory_sites" (
  "siteId" numeric(14 ,0) identity,
  "name" varchar(240) default NULL NULL,
  "description" text default '',
  "url" varchar(255) default NULL NULL,
  "country" varchar(255) default NULL NULL,
  "hits" numeric(12,0) default NULL NULL,
  "isValid" char(1) default NULL NULL,
  "created" numeric(14,0) default NULL NULL,
  "lastModif" numeric(14,0) default NULL NULL,
  "cache" image default '',
  "cache_timestamp" numeric(14,0) default NULL NULL,
  PRIMARY KEY ("siteId")
  KEY (isValid),
  KEY (url)
) ENGINE=MyISAM  
go


CREATE  INDEX "tiki_directory_sites_ft" ON "tiki_directory_sites"("name","description")
go
-- --------------------------------------------------------

--
-- Table structure for table tiki_drawings
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 08, 2003 at 05:02 AM
--

-- DROP TABLE "tiki_drawings"
go


CREATE TABLE "tiki_drawings" (
  "drawId" numeric(12 ,0) identity,
  "version" numeric(8,0) default NULL NULL,
  "name" varchar(250) default NULL NULL,
  "filename_draw" varchar(250) default NULL NULL,
  "filename_pad" varchar(250) default NULL NULL,
  "timestamp" numeric(14,0) default NULL NULL,
  "user" varchar(200) default '',
  PRIMARY KEY ("drawId")
) ENGINE=MyISAM  
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_dsn
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_dsn"
go


CREATE TABLE "tiki_dsn" (
  "dsnId" numeric(12 ,0) identity,
  "name" varchar(200) default '' NOT NULL,
  "dsn" varchar(255) default NULL NULL,
  PRIMARY KEY ("dsnId")
) ENGINE=MyISAM  
go


-- --------------------------------------------------------


-- DROP TABLE "tiki_dynamic_variables"
go


CREATE TABLE "tiki_dynamic_variables" (
  "name" varchar(40) NOT NULL,
  "data" text default '',
  PRIMARY KEY ("name")
)
go



-- --------------------------------------------------------
--
-- Table structure for table tiki_extwiki
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_extwiki"
go


CREATE TABLE "tiki_extwiki" (
  "extwikiId" numeric(12 ,0) identity,
  "name" varchar(200) default '' NOT NULL,
  "extwiki" varchar(255) default NULL NULL,
  PRIMARY KEY ("extwikiId")
) ENGINE=MyISAM  
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_faq_questions
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
-- Last check: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_faq_questions"
go


CREATE TABLE "tiki_faq_questions" (
  "questionId" numeric(10 ,0) identity,
  "faqId" numeric(10,0) default NULL NULL,
  "position" numeric(4,0) default NULL NULL,
  "question" text default '',
  "answer" text default '',
  PRIMARY KEY ("questionId")
) ENGINE=MyISAM  
go


CREATE  INDEX "tiki_faq_questions_faqId" ON "tiki_faq_questions"("faqId")
go
CREATE  INDEX "tiki_faq_questions_question" ON "tiki_faq_questions"("question")
go
CREATE  INDEX "tiki_faq_questions_answer" ON "tiki_faq_questions"("answer")
go
CREATE  INDEX "tiki_faq_questions_ft" ON "tiki_faq_questions"("question","answer")
go
-- --------------------------------------------------------

--
-- Table structure for table tiki_faqs
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 09:09 PM
-- Last check: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_faqs"
go


CREATE TABLE "tiki_faqs" (
  "faqId" numeric(10 ,0) identity,
  "title" varchar(200) default NULL NULL,
  "description" text default '',
  "created" numeric(14,0) default NULL NULL,
  "questions" numeric(5,0) default NULL NULL,
  "hits" numeric(8,0) default NULL NULL,
  "canSuggest" char(1) default NULL NULL,
  PRIMARY KEY ("faqId")
) ENGINE=MyISAM  
go


CREATE  INDEX "tiki_faqs_title" ON "tiki_faqs"("title")
go
CREATE  INDEX "tiki_faqs_description" ON "tiki_faqs"("description")
go
CREATE  INDEX "tiki_faqs_hits" ON "tiki_faqs"("hits")
go
CREATE  INDEX "tiki_faqs_ft" ON "tiki_faqs"("title","description")
go
-- --------------------------------------------------------

--
-- Table structure for table tiki_featured_links
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 11:08 PM
--

-- DROP TABLE "tiki_featured_links"
go


CREATE TABLE "tiki_featured_links" (
  "url" varchar(200) default '' NOT NULL,
  "title" varchar(200) default NULL NULL,
  "description" text default '',
  "hits" numeric(8,0) default NULL NULL,
  "position" numeric(6,0) default NULL NULL,
  "type" char(1) default NULL NULL,
  PRIMARY KEY ("url")
) ENGINE=MyISAM
go


-- --------------------------------------------------------
-- Table structure for table tiki_file_galleries
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 13, 2003 at 01:13 AM
--

-- DROP TABLE "tiki_file_galleries"
go


CREATE TABLE "tiki_file_galleries" (
  "galleryId" numeric(14 ,0) identity,
  "name" varchar(80) default '' NOT NULL,
  "type" varchar(20) default 'default' NOT NULL,
  "description" text default '',
  "created" numeric(14,0) default NULL NULL,
  "visible" char(1) default NULL NULL,
  "lastModif" numeric(14,0) default NULL NULL,
  "user" varchar(200) default '',
  "hits" numeric(14,0) default NULL NULL,
  "votes" numeric(8,0) default NULL NULL,
  "points" decimal(8,2) default NULL NULL,
  "maxRows" numeric(10,0) default NULL NULL,
  "public" char(1) default NULL NULL,
  "show_id" char(1) default NULL NULL,
  "show_icon" char(1) default NULL NULL,
  "show_name" char(1) default NULL NULL,
  "show_size" char(1) default NULL NULL,
  "show_description" char(1) default NULL NULL,
  "max_desc" numeric(8,0) default NULL NULL,
  "show_created" char(1) default NULL NULL,
  "show_hits" char(1) default NULL NULL,
  "parentId" numeric(14,0) default -1 NOT NULL,
  "lockable" char(1) default 'n',
  "show_lockedby" char(1) default NULL NULL,
  "archives" numeric(4,0) default -1,
  "sort_mode" char(20) default NULL NULL,
  "show_modified" char(1) default NULL NULL,
  "show_author" char(1) default NULL NULL,
  "show_creator" char(1) default NULL NULL,
  "subgal_conf" varchar(200) default NULL NULL,
  "show_last_user" char(1) default NULL NULL,
  "show_comment" char(1) default NULL NULL,
  "show_files" char(1) default NULL NULL,
  "show_explorer" char(1) default NULL NULL,
  "show_path" char(1) default NULL NULL,
  PRIMARY KEY ("galleryId")
) ENGINE=MyISAM  
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_files
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Nov 02, 2004 at 05:59 PM
-- Last check: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_files"
go


CREATE TABLE "tiki_files" (
  "fileId" numeric(14 ,0) identity,
  "galleryId" numeric(14,0) default '0' NOT NULL,
  "name" varchar(200) default '' NOT NULL,
  "description" text default '',
  "created" numeric(14,0) default NULL NULL,
  "filename" varchar(80) default NULL NULL,
  "filesize" numeric(14,0) default NULL NULL,
  "filetype" varchar(250) default NULL NULL,
  "data" image default '',
  "user" varchar(200) default '',
  "author" varchar(40) default NULL NULL,
  "hits" numeric(14,0) default NULL NULL,
  "votes" numeric(8,0) default NULL NULL,
  "points" decimal(8,2) default NULL NULL,
  "path" varchar(255) default NULL NULL,
  "reference_url" varchar(250) default NULL NULL,
  "is_reference" char(1) default NULL NULL,
  "hash" varchar(32) default NULL NULL,
  "search_data" longtext,
  "lastModif" integer(14) DEFAULT NULL NULL,
  "lastModifUser" varchar(200) DEFAULT NULL NULL,
  "lockedby" varchar(200) default '',
  "comment" varchar(200) default NULL NULL,
  "archiveId" numeric(14,0) default 0,
  PRIMARY KEY ("fileId")
) ENGINE=MyISAM  
go


CREATE  INDEX "tiki_files_name" ON "tiki_files"("name")
go
CREATE  INDEX "tiki_files_description" ON "tiki_files"("description")
go
CREATE  INDEX "tiki_files_hits" ON "tiki_files"("hits")
go
CREATE  INDEX "tiki_files_created" ON "tiki_files"("created")
go
CREATE  INDEX "tiki_files_archiveId" ON "tiki_files"("archiveId")
go
CREATE  INDEX "tiki_files_galleryId" ON "tiki_files"("galleryId")
go
CREATE  INDEX "tiki_files_ft" ON "tiki_files"("name","description","search_data")
go
-- --------------------------------------------------------

--
-- Table structure for table tiki_forum_attachments
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_forum_attachments"
go


CREATE TABLE "tiki_forum_attachments" (
  "attId" numeric(14 ,0) identity,
  "threadId" numeric(14,0) default '0' NOT NULL,
  "qId" numeric(14,0) default '0' NOT NULL,
  "forumId" numeric(14,0) default NULL NULL,
  "filename" varchar(250) default NULL NULL,
  "filetype" varchar(250) default NULL NULL,
  "filesize" numeric(12,0) default NULL NULL,
  "data" image default '',
  "dir" varchar(200) default NULL NULL,
  "created" numeric(14,0) default NULL NULL,
  "path" varchar(250) default NULL NULL,
  PRIMARY KEY ("attId")
) ENGINE=MyISAM  
go


CREATE  INDEX "tiki_forum_attachments_threadId" ON "tiki_forum_attachments"("threadId")
go
-- --------------------------------------------------------

--
-- Table structure for table tiki_forum_reads
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 07:17 PM
--

-- DROP TABLE "tiki_forum_reads"
go


CREATE TABLE "tiki_forum_reads" (
  "user" varchar(200) default '' NOT NULL,
  "threadId" numeric(14,0) default '0' NOT NULL,
  "forumId" numeric(14,0) default NULL NULL,
  "timestamp" numeric(14,0) default NULL NULL,
  PRIMARY KEY ("user","threadId")
) ENGINE=MyISAM
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_forums
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 11:14 PM
--

-- DROP TABLE "tiki_forums"
go


CREATE TABLE "tiki_forums" (
  "forumId" numeric(8 ,0) identity,
  "name" varchar(255) default NULL NULL,
  "description" text default '',
  "created" numeric(14,0) default NULL NULL,
  "lastPost" numeric(14,0) default NULL NULL,
  "threads" numeric(8,0) default NULL NULL,
  "comments" numeric(8,0) default NULL NULL,
  "controlFlood" char(1) default NULL NULL,
  "floodInterval" numeric(8,0) default NULL NULL,
  "moderator" varchar(200) default NULL NULL,
  "hits" numeric(8,0) default NULL NULL,
  "mail" varchar(200) default NULL NULL,
  "useMail" char(1) default NULL NULL,
  "section" varchar(200) default NULL NULL,
  "usePruneUnreplied" char(1) default NULL NULL,
  "pruneUnrepliedAge" numeric(8,0) default NULL NULL,
  "usePruneOld" char(1) default NULL NULL,
  "pruneMaxAge" numeric(8,0) default NULL NULL,
  "topicsPerPage" numeric(6,0) default NULL NULL,
  "topicOrdering" varchar(100) default NULL NULL,
  "threadOrdering" varchar(100) default NULL NULL,
  "att" varchar(80) default NULL NULL,
  "att_store" varchar(4) default NULL NULL,
  "att_store_dir" varchar(250) default NULL NULL,
  "att_max_size" numeric(12,0) default NULL NULL,
  "ui_level" char(1) default NULL NULL,
  "forum_password" varchar(32) default NULL NULL,
  "forum_use_password" char(1) default NULL NULL,
  "moderator_group" varchar(200) default NULL NULL,
  "approval_type" varchar(20) default NULL NULL,
  "outbound_address" varchar(250) default NULL NULL,
  "outbound_mails_for_inbound_mails" char(1) default NULL NULL,
  "outbound_mails_reply_link" char(1) default NULL NULL,
  "outbound_from" varchar(250) default NULL NULL,
  "inbound_pop_server" varchar(250) default NULL NULL,
  "inbound_pop_port" numeric(4,0) default NULL NULL,
  "inbound_pop_user" varchar(200) default NULL NULL,
  "inbound_pop_password" varchar(80) default NULL NULL,
  "topic_smileys" char(1) default NULL NULL,
  "ui_avatar" char(1) default NULL NULL,
  "ui_flag" char(1) default NULL NULL,
  "ui_posts" char(1) default NULL NULL,
  "ui_email" char(1) default NULL NULL,
  "ui_online" char(1) default NULL NULL,
  "topic_summary" char(1) default NULL NULL,
  "show_description" char(1) default NULL NULL,
  "topics_list_replies" char(1) default NULL NULL,
  "topics_list_reads" char(1) default NULL NULL,
  "topics_list_pts" char(1) default NULL NULL,
  "topics_list_lastpost" char(1) default NULL NULL,
  "topics_list_author" char(1) default NULL NULL,
  "vote_threads" char(1) default NULL NULL,
  "forum_last_n" numeric(2,0) default 0,
  "mandatory_contribution" char(1) default NULL NULL,
  "threadStyle" varchar(100) default NULL NULL,
  "commentsPerPage" varchar(100) default NULL NULL,
  "is_flat" char(1) default NULL NULL,
  PRIMARY KEY ("forumId")
) ENGINE=MyISAM  
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_forums_queue
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_forums_queue"
go


CREATE TABLE "tiki_forums_queue" (
  "qId" numeric(14 ,0) identity,
  "object" varchar(32) default NULL NULL,
  "parentId" numeric(14,0) default NULL NULL,
  "forumId" numeric(14,0) default NULL NULL,
  "timestamp" numeric(14,0) default NULL NULL,
  "user" varchar(200) default '',
  "title" varchar(240) default NULL NULL,
  "data" text default '',
  "type" varchar(60) default NULL NULL,
  "hash" varchar(32) default NULL NULL,
  "topic_smiley" varchar(80) default NULL NULL,
  "topic_title" varchar(240) default NULL NULL,
  "summary" varchar(240) default NULL NULL,
  "in_reply_to" varchar(128) default NULL NULL,
  PRIMARY KEY ("qId")
) ENGINE=MyISAM  
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_forums_reported
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_forums_reported"
go


CREATE TABLE "tiki_forums_reported" (
  "threadId" numeric(12,0) default '0' NOT NULL,
  "forumId" numeric(12,0) default '0' NOT NULL,
  "parentId" numeric(12,0) default '0' NOT NULL,
  "user" varchar(200) default '',
  "timestamp" numeric(14,0) default NULL NULL,
  "reason" varchar(250) default NULL NULL,
  PRIMARY KEY ("threadId")
) ENGINE=MyISAM
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_galleries
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Sep 18, 2004 at 11:56 PM
-- Last check: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_galleries"
go


CREATE TABLE "tiki_galleries" (
  "galleryId" numeric(14 ,0) identity,
  "name" varchar(80) default '' NOT NULL,
  "description" text default '',
  "created" numeric(14,0) default NULL NULL,
  "lastModif" numeric(14,0) default NULL NULL,
  "visible" char(1) default NULL NULL,
  "geographic" char(1) default NULL NULL,
  "theme" varchar(60) default NULL NULL,
  "user" varchar(200) default '',
  "hits" numeric(14,0) default NULL NULL,
  "maxRows" numeric(10,0) default NULL NULL,
  "rowImages" numeric(10,0) default NULL NULL,
  "thumbSizeX" numeric(10,0) default NULL NULL,
  "thumbSizeY" numeric(10,0) default NULL NULL,
  "public" char(1) default NULL NULL,
  "sortorder" varchar(20) default 'created' NOT NULL,
  "sortdirection" varchar(4) default 'desc' NOT NULL,
  "galleryimage" varchar(20) default 'first' NOT NULL,
  "parentgallery" numeric(14,0) default -1 NOT NULL,
  "showname" char(1) default 'y' NOT NULL,
  "showimageid" char(1) default 'n' NOT NULL,
  "showdescription" char(1) default 'n' NOT NULL,
  "showcreated" char(1) default 'n' NOT NULL,
  "showuser" char(1) default 'n' NOT NULL,
  "showhits" char(1) default 'y' NOT NULL,
  "showxysize" char(1) default 'y' NOT NULL,
  "showfilesize" char(1) default 'n' NOT NULL,
  "showfilename" char(1) default 'n' NOT NULL,
  "defaultscale" varchar(10) DEFAULT 'o' NOT NULL,
  PRIMARY KEY ("galleryId")
) ENGINE=MyISAM  
go


CREATE  INDEX "tiki_galleries_name" ON "tiki_galleries"("name")
go
CREATE  INDEX "tiki_galleries_description" ON "tiki_galleries"("description")
go
CREATE  INDEX "tiki_galleries_hits" ON "tiki_galleries"("hits")
go
CREATE  INDEX "tiki_galleries_parentgallery" ON "tiki_galleries"("parentgallery")
go
CREATE  INDEX "tiki_galleries_visibleUser" ON "tiki_galleries"("visible" "user")
go
CREATE  INDEX "tiki_galleries_ft" ON "tiki_galleries"("name","description")
go
-- --------------------------------------------------------

--
-- Table structure for table tiki_galleries_scales
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_galleries_scales"
go


CREATE TABLE "tiki_galleries_scales" (
  "galleryId" numeric(14,0) default '0' NOT NULL,
  "scale" numeric(11,0) default '0' NOT NULL,
  PRIMARY KEY ("galleryId","scale")
) ENGINE=MyISAM
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_games
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 05, 2003 at 08:23 PM
--

-- DROP TABLE "tiki_games"
go


CREATE TABLE "tiki_games" (
  "gameName" varchar(200) default '' NOT NULL,
  "hits" numeric(8,0) default NULL NULL,
  "votes" numeric(8,0) default NULL NULL,
  "points" numeric(8,0) default NULL NULL,
  PRIMARY KEY ("gameName")
) ENGINE=MyISAM
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_group_inclusion
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 05, 2003 at 02:03 AM
--

-- DROP TABLE "tiki_group_inclusion"
go


CREATE TABLE "tiki_group_inclusion" (
  "groupName" varchar(255) default '' NOT NULL,
  "includeGroup" varchar(255) default '' NOT NULL,
  PRIMARY KEY ("groupName","includeGroup")
) ENGINE=MyISAM
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_history
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Mar 30, 2005 at 10:21 PM
--

-- DROP TABLE "tiki_history"
go


CREATE TABLE "tiki_history" (
  "historyId" numeric(12 ,0) identity,
  "pageName" varchar(160) default '' NOT NULL,
  "version" numeric(8,0) default '0' NOT NULL,
  "version_minor" numeric(8,0) default '0' NOT NULL,
  "lastModif" numeric(14,0) default NULL NULL,
  "description" varchar(200) default NULL NULL,
  "user" varchar(200) default '' not null,
  "ip" varchar(15) default NULL NULL,
  "comment" varchar(200) default NULL NULL,
  "data" image default '',
  "type" varchar(50) default NULL NULL,
  PRIMARY KEY ("pageName","version")
  KEY `user` (`user`),
  KEY(historyId)
) ENGINE=MyISAM
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_hotwords
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 10, 2003 at 11:04 PM
--

-- DROP TABLE "tiki_hotwords"
go


CREATE TABLE "tiki_hotwords" (
  "word" varchar(40) default '' NOT NULL,
  "url" varchar(255) default '' NOT NULL,
  PRIMARY KEY ("word")
) ENGINE=MyISAM
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_html_pages
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_html_pages"
go


CREATE TABLE "tiki_html_pages" (
  "pageName" varchar(200) default '' NOT NULL,
  "content" image default '',
  "refresh" numeric(10,0) default NULL NULL,
  "type" char(1) default NULL NULL,
  "created" numeric(14,0) default NULL NULL,
  PRIMARY KEY ("pageName")
) ENGINE=MyISAM
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_html_pages_dynamic_zones
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_html_pages_dynamic_zones"
go


CREATE TABLE "tiki_html_pages_dynamic_zones" (
  "pageName" varchar(40) default '' NOT NULL,
  "zone" varchar(80) default '' NOT NULL,
  "type" char(2) default NULL NULL,
  "content" text default '',
  PRIMARY KEY ("pageName","zone")
) ENGINE=MyISAM
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_images
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Sep 18, 2004 at 08:29 PM
-- Last check: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_images"
go


CREATE TABLE "tiki_images" (
  "imageId" numeric(14 ,0) identity,
  "galleryId" numeric(14,0) default '0' NOT NULL,
  "name" varchar(200) default '' NOT NULL,
  "description" text default '',
  "lon" float default NULL NULL,
  "lat" float default NULL NULL,
  "created" numeric(14,0) default NULL NULL,
  "user" varchar(200) default '',
  "hits" numeric(14,0) default NULL NULL,
  "path" varchar(255) default NULL NULL,
  PRIMARY KEY ("imageId")
) ENGINE=MyISAM  
go


CREATE  INDEX "tiki_images_name" ON "tiki_images"("name")
go
CREATE  INDEX "tiki_images_description" ON "tiki_images"("description")
go
CREATE  INDEX "tiki_images_hits" ON "tiki_images"("hits")
go
CREATE  INDEX "tiki_images_ti_gId" ON "tiki_images"("galleryId")
go
CREATE  INDEX "tiki_images_ti_cr" ON "tiki_images"("created")
go
CREATE  INDEX "tiki_images_ti_us" ON "tiki_images"("user")
go
CREATE  INDEX "tiki_images_ft" ON "tiki_images"("name","description")
go
-- --------------------------------------------------------

--
-- Table structure for table tiki_images_data
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 12:49 PM
-- Last check: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_images_data"
go


CREATE TABLE "tiki_images_data" (
  "imageId" numeric(14,0) default '0' NOT NULL,
  "xsize" numeric(8,0) default '0' NOT NULL,
  "ysize" numeric(8,0) default '0' NOT NULL,
  "type" char(1) default '' NOT NULL,
  "filesize" numeric(14,0) default NULL NULL,
  "filetype" varchar(80) default NULL NULL,
  "filename" varchar(80) default NULL NULL,
  "data" image default '',
  "etag" varchar(32) default NULL NULL,
  PRIMARY KEY ("imageId","xsize","ysize","type")
) ENGINE=MyISAM
go


CREATE  INDEX "tiki_images_data_t_i_d_it" ON "tiki_images_data"("imageId","type")
go
-- --------------------------------------------------------

--
-- Table structure for table tiki_language
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_language"
go


CREATE TABLE "tiki_language" (
  "source" image NOT NULL,
  "lang" char(16) default '' NOT NULL,
  "tran" image default '',
  PRIMARY KEY ("source","lang")
) ENGINE=MyISAM
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_languages
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_languages"
go


CREATE TABLE "tiki_languages" (
  "lang" char(16) default '' NOT NULL,
  "language" varchar(255) default NULL NULL,
  PRIMARY KEY ("lang")
) ENGINE=MyISAM
go


-- --------------------------------------------------------
INSERT INTO tiki_languages(lang, language) VALUES('en','English')
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_link_cache
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 06:06 PM
--

-- DROP TABLE "tiki_link_cache"
go


CREATE TABLE "tiki_link_cache" (
  "cacheId" numeric(14 ,0) identity,
  "url" varchar(250) default NULL NULL,
  "data" image default '',
  "refresh" numeric(14,0) default NULL NULL,
  PRIMARY KEY ("cacheId")
) ENGINE=MyISAM  
go


CREATE  INDEX "tiki_link_cache_url" ON "tiki_link_cache"("url")
go
CREATE INDEX urlindex ON tiki_link_cache (url(250))
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_links
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 11:39 PM
--

-- DROP TABLE "tiki_links"
go


CREATE TABLE "tiki_links" (
  "fromPage" varchar(160) default '' NOT NULL,
  "toPage" varchar(160) default '' NOT NULL,
  PRIMARY KEY ("fromPage","toPage")
) ENGINE=MyISAM
go


CREATE  INDEX "tiki_links_toPage" ON "tiki_links"("toPage")
go
-- --------------------------------------------------------

--
-- Table structure for table tiki_live_support_events
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_live_support_events"
go


CREATE TABLE "tiki_live_support_events" (
  "eventId" numeric(14 ,0) identity,
  "reqId" varchar(32) default '' NOT NULL,
  "type" varchar(40) default NULL NULL,
  "seqId" numeric(14,0) default NULL NULL,
  "senderId" varchar(32) default NULL NULL,
  "data" text default '',
  "timestamp" numeric(14,0) default NULL NULL,
  PRIMARY KEY ("eventId")
) ENGINE=MyISAM  
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_live_support_message_comments
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_live_support_message_comments"
go


CREATE TABLE "tiki_live_support_message_comments" (
  "cId" numeric(12 ,0) identity,
  "msgId" numeric(12,0) default NULL NULL,
  "data" text default '',
  "timestamp" numeric(14,0) default NULL NULL,
  PRIMARY KEY ("cId")
) ENGINE=MyISAM  
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_live_support_messages
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_live_support_messages"
go


CREATE TABLE "tiki_live_support_messages" (
  "msgId" numeric(12 ,0) identity,
  "data" text default '',
  "timestamp" numeric(14,0) default NULL NULL,
  "user" varchar(200) default '' not null,
  "username" varchar(200) default NULL NULL,
  "priority" numeric(2,0) default NULL NULL,
  "status" char(1) default NULL NULL,
  "assigned_to" varchar(200) default NULL NULL,
  "resolution" varchar(100) default NULL NULL,
  "title" varchar(200) default NULL NULL,
  "module" numeric(4,0) default NULL NULL,
  "email" varchar(250) default NULL NULL,
  PRIMARY KEY ("msgId")
) ENGINE=MyISAM  
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_live_support_modules
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_live_support_modules"
go


CREATE TABLE "tiki_live_support_modules" (
  "modId" numeric(4 ,0) identity,
  "name" varchar(90) default NULL NULL,
  PRIMARY KEY ("modId")
) ENGINE=MyISAM  
go


-- --------------------------------------------------------
INSERT INTO tiki_live_support_modules(name) VALUES('wiki')
go


INSERT INTO tiki_live_support_modules(name) VALUES('forums')
go


INSERT INTO tiki_live_support_modules(name) VALUES('image galleries')
go


INSERT INTO tiki_live_support_modules(name) VALUES('file galleries')
go


INSERT INTO tiki_live_support_modules(name) VALUES('directory')
go


INSERT INTO tiki_live_support_modules(name) VALUES('workflow')
go


INSERT INTO tiki_live_support_modules(name) VALUES('charts')
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_live_support_operators
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_live_support_operators"
go


CREATE TABLE "tiki_live_support_operators" (
  "user" varchar(200) default '' NOT NULL,
  "accepted_requests" numeric(10,0) default NULL NULL,
  "status" varchar(20) default NULL NULL,
  "longest_chat" numeric(10,0) default NULL NULL,
  "shortest_chat" numeric(10,0) default NULL NULL,
  "average_chat" numeric(10,0) default NULL NULL,
  "last_chat" numeric(14,0) default NULL NULL,
  "time_online" numeric(10,0) default NULL NULL,
  "votes" numeric(10,0) default NULL NULL,
  "points" numeric(10,0) default NULL NULL,
  "status_since" numeric(14,0) default NULL NULL,
  PRIMARY KEY ("user")
) ENGINE=MyISAM
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_live_support_requests
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_live_support_requests"
go


CREATE TABLE "tiki_live_support_requests" (
  "reqId" varchar(32) default '' NOT NULL,
  "user" varchar(200) default '' NOT NULL,
  "tiki_user" varchar(200) default NULL NULL,
  "email" varchar(200) default NULL NULL,
  "operator" varchar(200) default NULL NULL,
  "operator_id" varchar(32) default NULL NULL,
  "user_id" varchar(32) default NULL NULL,
  "reason" text default '',
  "req_timestamp" numeric(14,0) default NULL NULL,
  "timestamp" numeric(14,0) default NULL NULL,
  "status" varchar(40) default NULL NULL,
  "resolution" varchar(40) default NULL NULL,
  "chat_started" numeric(14,0) default NULL NULL,
  "chat_ended" numeric(14,0) default NULL NULL,
  PRIMARY KEY ("reqId")
) ENGINE=MyISAM
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_logs
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_logs"
go


CREATE TABLE "tiki_logs" (
  "logId" numeric(8 ,0) identity,
  "logtype" varchar(20) NOT NULL,
  "logmessage" text NOT NULL,
  "loguser" varchar(40) NOT NULL,
  "logip" varchar(200) default '',
  "logclient" text NOT NULL,
  "logtime" numeric(14,0) NOT NULL,
  PRIMARY KEY ("logId")
) ENGINE=MyISAM
go


CREATE  INDEX "tiki_logs_logtype" ON "tiki_logs"("logtype")
go

-- --------------------------------------------------------

--
-- Table structure for table tiki_mail_events
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 11, 2003 at 05:28 AM
--

-- DROP TABLE "tiki_mail_events"
go


CREATE TABLE "tiki_mail_events" (
  "event" varchar(200) default NULL NULL,
  "object" varchar(200) default NULL NULL,
  "email" varchar(200) default NULL
) ENGINE=MyISAM
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_mailin_accounts
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jun 17, 2004 at 03:06 PM EST
--

-- DROP TABLE "tiki_mailin_accounts"
go


CREATE TABLE "tiki_mailin_accounts" (
  "accountId" numeric(12 ,0) identity,
  "user" varchar(200) default '' NOT NULL,
  "account" varchar(50) default '' NOT NULL,
  "pop" varchar(255) default NULL NULL,
  "port" numeric(4,0) default NULL NULL,
  "username" varchar(100) default NULL NULL,
  "pass" varchar(100) default NULL NULL,
  "active" char(1) default NULL NULL,
  "type" varchar(40) default NULL NULL,
  "smtp" varchar(255) default NULL NULL,
  "useAuth" char(1) default NULL NULL,
  "smtpPort" numeric(4,0) default NULL NULL,
  "anonymous" char(1) default 'y' NOT NULL,
  "attachments" char(1) default 'n' NOT NULL,
  "article_topicId" numeric(4,0) default NULL NULL,
  "article_type" varchar(50) default NULL NULL,
  "discard_after" varchar(255) default NULL NULL,
  PRIMARY KEY ("accountId")
) ENGINE=MyISAM  
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_menu_languages
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_menu_languages"
go


CREATE TABLE "tiki_menu_languages" (
  "menuId" numeric(8 ,0) identity,
  "language" char(16) default '' NOT NULL,
  PRIMARY KEY ("menuId","language")
) ENGINE=MyISAM  
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_menu_options
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Nov 21, 2003 at 07:05 AM
--

-- DROP TABLE "tiki_menu_options"
go


CREATE TABLE "tiki_menu_options" (
  "optionId" numeric(8 ,0) identity,
  "menuId" numeric(8,0) default NULL NULL,
  "type" char(1) default NULL NULL,
  "name" varchar(200) default NULL NULL,
  "url" varchar(255) default NULL NULL,
  "position" numeric(4,0) default NULL NULL,
  "section" varchar(255) default NULL NULL,
  "perm" varchar(255) default NULL NULL,
  "groupname" varchar(255) default NULL NULL,
  "userlevel" numeric(4,0) default 0,
  PRIMARY KEY ("optionId")
) ENGINE=MyISAM  
go


CREATE UNIQUE INDEX "tiki_menu_options_uniq_menu" ON "tiki_menu_options"("menuId","name","url","position","section","perm","groupname")
go
-- --------------------------------------------------------
INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Home','./',10,'','','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Search','tiki-searchindex.php',13,'feature_search','tiki_p_search','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Contact us','tiki-contact.php',20,'feature_contact','','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Stats','tiki-stats.php',23,'feature_stats','tiki_p_view_stats','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Categories','tiki-browse_categories.php',25,'feature_categories','tiki_p_view_categories','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Freetags','tiki-browse_freetags.php',27,'feature_freetags','tiki_p_view_freetags','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Games','tiki-list_games.php',30,'feature_games','tiki_p_play_games','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Calendar','tiki-calendar.php',35,'feature_calendar','tiki_p_view_calendar','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Tiki Calendar','tiki-action_calendar.php',36,'feature_action_calendar','tiki_p_view_tiki_calendar','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Users map','tiki-gmap_usermap.php',36,'feature_gmap','','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Mobile','tiki-mobile.php',37,'feature_mobile','','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','(debug)','javascript:toggle(\'debugconsole\')',40,'feature_debug_console','tiki_p_admin','')
go



INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','MyTiki','tiki-my_tiki.php',50,'feature_mytiki','','Registered')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','MyTiki home','tiki-my_tiki.php',51,'feature_mytiki','','Registered')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Preferences','tiki-user_preferences.php',55,'feature_mytiki,feature_userPreferences','','Registered')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Messages','messu-mailbox.php',60,'feature_mytiki,feature_messages','tiki_p_messages','Registered')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Tasks','tiki-user_tasks.php',65,'feature_mytiki,feature_tasks','tiki_p_tasks','Registered')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Bookmarks','tiki-user_bookmarks.php',70,'feature_mytiki,feature_user_bookmarks','tiki_p_create_bookmarks','Registered')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Modules','tiki-user_assigned_modules.php',75,'feature_mytiki,user_assigned_modules','tiki_p_configure_modules','Registered')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Newsreader','tiki-newsreader_servers.php',80,'feature_mytiki,feature_newsreader','tiki_p_newsreader','Registered')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Webmail','tiki-webmail.php',85,'feature_mytiki,feature_webmail','tiki_p_use_webmail','Registered')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Contacts','tiki-contacts.php',87,'feature_mytiki,feature_contacts','','Registered')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Notepad','tiki-notepad_list.php',90,'feature_mytiki,feature_notepad','tiki_p_notepad','Registered')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','My files','tiki-userfiles.php',95,'feature_mytiki,feature_userfiles','tiki_p_userfiles','Registered')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','User menu','tiki-usermenu.php',100,'feature_mytiki,feature_usermenu','','Registered')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Mini calendar','tiki-minical.php',105,'feature_mytiki,feature_minical','','Registered')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','My watches','tiki-user_watches.php',110,'feature_mytiki,feature_user_watches','','Registered')
go



INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','Workflow','tiki-g-user_processes.php',150,'feature_workflow','tiki_p_use_workflow','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','User processes','tiki-g-user_processes.php',152,'feature_workflow','tiki_p_use_workflow','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','User activities','tiki-g-user_activities.php',153,'feature_workflow','tiki_p_use_workflow','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','User instances','tiki-g-user_instances.php',154,'feature_workflow','tiki_p_use_workflow','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Admin processes','tiki-g-admin_processes.php',155,'feature_workflow','tiki_p_admin_workflow','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Monitor processes','tiki-g-monitor_processes.php',160,'feature_workflow','tiki_p_admin_workflow','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Monitor activities','tiki-g-monitor_activities.php',165,'feature_workflow','tiki_p_admin_workflow','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Monitor instances','tiki-g-monitor_instances.php',170,'feature_workflow','tiki_p_admin_workflow','')
go



INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','Community','','187','feature_friends','tiki_p_list_users','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','User list','tiki-list_users.php','188','feature_friends','tiki_p_list_users','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Friendship Network','tiki-friends.php','189','feature_friends','','')
go



INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','Wiki','tiki-index.php',200,'feature_wiki','tiki_p_view','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Wiki Home','tiki-index.php',202,'feature_wiki','tiki_p_view','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Last Changes','tiki-lastchanges.php',205,'feature_wiki,feature_lastChanges','tiki_p_view','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Dump','dump/new.tar',210,'feature_wiki,feature_dump','tiki_p_view','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Rankings','tiki-wiki_rankings.php',215,'feature_wiki,feature_wiki_rankings','tiki_p_view','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','List pages','tiki-listpages.php',220,'feature_wiki,feature_listPages','tiki_p_view','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Orphan pages','tiki-orphan_pages.php',225,'feature_wiki,feature_listPages','tiki_p_view','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Sandbox','tiki-editpage.php?page=sandbox',230,'feature_wiki,feature_sandbox','tiki_p_view','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Multiple Print','tiki-print_pages.php',235,'feature_wiki,feature_wiki_multiprint','tiki_p_view','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Send pages','tiki-send_objects.php',240,'feature_wiki,feature_comm','tiki_p_view,tiki_p_send_pages','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Received pages','tiki-received_pages.php',245,'feature_wiki,feature_comm','tiki_p_view,tiki_p_admin_received_pages','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Structures','tiki-admin_structures.php',250,'feature_wiki','tiki_p_view','')
go




INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','Image Galleries','tiki-galleries.php',300,'feature_galleries','tiki_p_list_image_galleries','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Galleries','tiki-galleries.php',305,'feature_galleries','tiki_p_list_image_galleries','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Rankings','tiki-galleries_rankings.php',310,'feature_galleries,feature_gal_rankings','tiki_p_list_image_galleries','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Upload image','tiki-upload_image.php',315,'feature_galleries','tiki_p_upload_images','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Directory batch','tiki-batch_upload.php',318,'feature_galleries,feature_gal_batch','tiki_p_batch_upload','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','System gallery','tiki-list_gallery.php?galleryId=0',320,'feature_galleries','tiki_p_admin_galleries','')
go



INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','Articles','tiki-view_articles.php',350,'feature_articles','tiki_p_read_article','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Articles home','tiki-view_articles.php',355,'feature_articles','tiki_p_read_article','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','List articles','tiki-list_articles.php',360,'feature_articles','tiki_p_read_article','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Rankings','tiki-cms_rankings.php',365,'feature_articles,feature_cms_rankings','tiki_p_read_article','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Submit article','tiki-edit_submission.php',370,'feature_articles,feature_submissions','tiki_p_read_article,tiki_p_submit_article','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','View submissions','tiki-list_submissions.php',375,'feature_articles,feature_submissions','tiki_p_read_article,tiki_p_submit_article','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','View submissions','tiki-list_submissions.php',375,'feature_articles,feature_submissions','tiki_p_read_article,tiki_p_approve_submission','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','View submissions','tiki-list_submissions.php',375,'feature_articles,feature_submissions','tiki_p_read_article,tiki_p_remove_submission','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','New article','tiki-edit_article.php',380,'feature_articles','tiki_p_read_article,tiki_p_edit_article','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Send articles','tiki-send_objects.php',385,'feature_articles,feature_comm','tiki_p_read_article,tiki_p_send_articles','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Received articles','tiki-received_articles.php',385,'feature_articles,feature_comm','tiki_p_read_article,tiki_p_admin_received_articles','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Admin topics','tiki-admin_topics.php',390,'feature_articles','tiki_p_read_article,tiki_p_admin_cms','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Admin types','tiki-article_types.php',395,'feature_articles','tiki_p_read_article,tiki_p_admin_cms','')
go



INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','Blogs','tiki-list_blogs.php',450,'feature_blogs','tiki_p_read_blog','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','List blogs','tiki-list_blogs.php',455,'feature_blogs','tiki_p_read_blog','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Rankings','tiki-blog_rankings.php',460,'feature_blogs,feature_blog_rankings','tiki_p_read_blog','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Create/Edit blog','tiki-edit_blog.php',465,'feature_blogs','tiki_p_read_blog,tiki_p_create_blogs','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Post','tiki-blog_post.php',470,'feature_blogs','tiki_p_read_blog,tiki_p_blog_post','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Admin posts','tiki-list_posts.php',475,'feature_blogs','tiki_p_read_blog,tiki_p_blog_admin','')
go



INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','Forums','tiki-forums.php',500,'feature_forums','tiki_p_forum_read','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','List forums','tiki-forums.php',505,'feature_forums','tiki_p_forum_read','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Rankings','tiki-forum_rankings.php',510,'feature_forums,feature_forum_rankings','tiki_p_forum_read','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Admin forums','tiki-admin_forums.php',515,'feature_forums','tiki_p_forum_read,tiki_p_admin_forum','')
go



INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','Directory','tiki-directory_browse.php',550,'feature_directory','tiki_p_view_directory','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Browse directory','tiki-directory_browse.php',552,'feature_directory','tiki_p_view_directory','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Submit a new link','tiki-directory_add_site.php',555,'feature_directory','tiki_p_submit_link','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Admin directory','tiki-directory_admin.php',565,'feature_directory','tiki_p_view_directory,tiki_p_admin_directory_cats','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Admin directory','tiki-directory_admin.php',565,'feature_directory','tiki_p_view_directory,tiki_p_admin_directory_sites','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Admin directory','tiki-directory_admin.php',565,'feature_directory','tiki_p_view_directory,tiki_p_validate_links','')
go



INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','File Galleries','tiki-file_galleries.php',600,'feature_file_galleries','tiki_p_view_file_gallery','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','List galleries','tiki-file_galleries.php',605,'feature_file_galleries','tiki_p_list_file_galleries','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Rankings','tiki-file_galleries_rankings.php',610,'feature_file_galleries,feature_file_galleries_rankings','tiki_p_list_file_galleries','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Upload file','tiki-upload_file.php',615,'feature_file_galleries','tiki_p_view_file_gallery,tiki_p_upload_files','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Directory batch','tiki-batch_upload_files.php',617,'feature_file_galleries_batch','tiki_p_batch_upload_file_dir','')
go



INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','FAQs','tiki-list_faqs.php',650,'feature_faqs','tiki_p_view_faqs','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','List FAQs','tiki-list_faqs.php',665,'feature_faqs','tiki_p_view_faqs','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Admin FAQs','tiki-list_faqs.php',660,'feature_faqs','tiki_p_admin_faqs','')
go



INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','Maps','tiki-map.php',700,'feature_maps','tiki_p_map_view','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','View Maps','tiki-map.php',703,'feature_maps','tiki_p_map_view','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Mapfiles','tiki-map_edit.php',705,'feature_maps','tiki_p_map_view','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Layer management','tiki-map_upload.php',710,'feature_maps','tiki_p_map_edit','')
go



INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','Quizzes','tiki-list_quizzes.php',750,'feature_quizzes','','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','List quizzes','tiki-list_quizzes.php',755,'feature_quizzes','','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Quiz stats','tiki-quiz_stats.php',760,'feature_quizzes','tiki_p_view_quiz_stats','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Admin quiz','tiki-edit_quiz.php',765,'feature_quizzes','tiki_p_admin_quizzes','')
go



INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','TikiSheet','tiki-sheets.php',780,'feature_sheet','tiki_p_view_sheet','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','List TikiSheets','tiki-sheets.php',782,'feature_sheet','tiki_p_view_sheet','')
go



INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','Trackers','tiki-list_trackers.php',800,'feature_trackers','tiki_p_list_trackers','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','List trackers','tiki-list_trackers.php',805,'feature_trackers','tiki_p_list_trackers','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Admin trackers','tiki-admin_trackers.php',810,'feature_trackers','tiki_p_admin_trackers','')
go



INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','Surveys','tiki-list_surveys.php',850,'feature_surveys','','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','List surveys','tiki-list_surveys.php',855,'feature_surveys','','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Stats','tiki-survey_stats.php',860,'feature_surveys','tiki_p_view_survey_stats','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Admin surveys','tiki-admin_surveys.php',865,'feature_surveys','tiki_p_admin_surveys','')
go



INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','Newsletters','tiki-newsletters.php',900,'feature_newsletters','tiki_p_subscribe_newsletters','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','Newsletters','tiki-newsletters.php',900,'feature_newsletters','tiki_p_send_newsletters','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','Newsletters','tiki-newsletters.php',900,'feature_newsletters','tiki_p_admin_newsletters','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Newsletters','tiki-newsletters.php',903,'feature_newsletters','tiki_p_subscribe_newsletters','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Newsletters','tiki-newsletters.php',903,'feature_newsletters','tiki_p_send_newsletters','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Newsletters','tiki-newsletters.php',903,'feature_newsletters','tiki_p_admin_newsletters','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Send newsletters','tiki-send_newsletters.php',905,'feature_newsletters','tiki_p_send_newsletters','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Admin newsletters','tiki-admin_newsletters.php',910,'feature_newsletters','tiki_p_admin_newsletters','')
go




INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','Charts','tiki-charts.php',1000,'feature_charts','','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Charts','tiki-charts.php',1003,'feature_charts','','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Admin charts','tiki-admin_charts.php',1005,'feature_charts','tiki_p_admin_charts','')
go



INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'r','Admin','tiki-admin.php',1050,'','tiki_p_admin','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'r','Admin','tiki-admin.php',1050,'','tiki_p_admin_chat','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'r','Admin','tiki-admin.php',1050,'','tiki_p_admin_categories','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'r','Admin','tiki-admin.php',1050,'','tiki_p_admin_banners','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'r','Admin','tiki-admin.php',1050,'','tiki_p_edit_templates','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'r','Admin','tiki-admin.php',1050,'','tiki_p_edit_cookies','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'r','Admin','tiki-admin.php',1050,'','tiki_p_admin_dynamic','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'r','Admin','tiki-admin.php',1050,'','tiki_p_admin_mailin','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'r','Admin','tiki-admin.php',1050,'','tiki_p_edit_content_templates','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'r','Admin','tiki-admin.php',1050,'','tiki_p_edit_html_pages','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'r','Admin','tiki-admin.php',1050,'','tiki_p_view_referer_stats','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'r','Admin','tiki-admin.php',1050,'','tiki_p_admin_drawings','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'r','Admin','tiki-admin.php',1050,'','tiki_p_admin_quicktags','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'r','Admin','tiki-admin.php',1050,'','tiki_p_admin_shoutbox','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'r','Admin','tiki-admin.php',1050,'','tiki_p_live_support_admin','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'r','Admin','tiki-admin.php',1050,'','user_is_operator','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'r','Admin','tiki-admin.php',1050,'feature_integrator','tiki_p_admin_integrator','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'r','Admin','tiki-admin.php',1050,'','tiki_p_admin_contribution','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'r','Admin','tiki-admin.php',1050,'','tiki_p_admin_users','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'r','Admin','tiki-admin.php',1050,'','tiki_p_edit_menu','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'r','Admin','tiki-admin.php',1050,'','tiki_p_clean_cache','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Admin home','tiki-admin.php',1051,'','tiki_p_admin','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Live support','tiki-live_support_admin.php',1055,'feature_live_support','tiki_p_live_support_admin','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Live support','tiki-live_support_admin.php',1055,'feature_live_support','user_is_operator','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Banning','tiki-admin_banning.php',1060,'feature_banning','tiki_p_admin_banning','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Calendar','tiki-admin_calendars.php',1065,'feature_calendar','tiki_p_admin_calendar','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Users','tiki-adminusers.php',1070,'','tiki_p_admin_users','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Groups','tiki-admingroups.php',1075,'','tiki_p_admin','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Wiki Cache','tiki-list_cache.php',1080,'cachepages','tiki_p_admin','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Modules','tiki-admin_modules.php',1085,'','tiki_p_admin','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Links','tiki-admin_links.php',1090,'feature_featuredLinks','tiki_p_admin','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Hotwords','tiki-admin_hotwords.php',1095,'feature_hotwords','tiki_p_admin','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','RSS modules','tiki-admin_rssmodules.php',1100,'','tiki_p_admin','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Menus','tiki-admin_menus.php',1105,'','tiki_p_edit_menu','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Polls','tiki-admin_polls.php',1110,'feature_polls','tiki_p_admin','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Mail notifications','tiki-admin_notifications.php',1120,'','tiki_p_admin','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Search stats','tiki-search_stats.php',1125,'feature_search_stats','tiki_p_admin','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Theme control','tiki-theme_control.php',1130,'feature_theme_control','tiki_p_admin','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','QuickTags','tiki-admin_quicktags.php',1135,'','tiki_p_admin,tiki_p_admin_quicktags','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Categories','tiki-admin_categories.php',1145,'feature_categories','tiki_p_admin_categories','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Banners','tiki-list_banners.php',1150,'feature_banners','tiki_p_admin_banners','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Edit templates','tiki-edit_templates.php',1155,'feature_edit_templates','tiki_p_edit_templates','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Drawings','tiki-admin_drawings.php',1160,'feature_drawings','tiki_p_admin_drawings','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Dynamic content','tiki-list_contents.php',1165,'feature_dynamic_content','tiki_p_admin_dynamic','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Cookies','tiki-admin_cookies.php',1170,'','tiki_p_edit_cookies','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Mail-in','tiki-admin_mailin.php',1175,'feature_mailin','tiki_p_admin_mailin','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Content templates','tiki-admin_content_templates.php',1180,'','tiki_p_edit_content_templates','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','HTML pages','tiki-admin_html_pages.php',1185,'feature_html_pages','tiki_p_edit_html_pages','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Shoutbox','tiki-shoutbox.php',1190,'feature_shoutbox','tiki_p_admin_shoutbox','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Shoutbox Words','tiki-admin_shoutbox_words.php',1191,'feature_shoutbox','tiki_p_admin_shoutbox','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Referer stats','tiki-referer_stats.php',1195,'feature_referer_stats','tiki_p_view_referer_stats','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Edit languages','tiki-edit_languages.php',1200,'lang_use_db','tiki_p_edit_languages','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Integrator','tiki-admin_integrator.php',1205,'feature_integrator','tiki_p_admin_integrator','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','phpinfo','tiki-phpinfo.php',1215,'','tiki_p_admin','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Tiki Cache/Sys Admin','tiki-admin_system.php',1230,'','tiki_p_clean_cache','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Score','tiki-admin_score.php',1235,'feature_score','tiki_p_admin','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Admin mods','tiki-mods.php',1240,'','tiki_p_admin','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Tiki Logs','tiki-syslog.php',1245,'','tiki_p_admin','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Security Admin','tiki-admin_security.php',1250,'','tiki_p_admin','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Action Log','tiki-admin_actionlog.php',1255,'feature_actionlog','tiki_p_admin','')
go



INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Comments','tiki-list_comments.php',1260,'feature_wiki_comments','tiki_p_admin','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Comments','tiki-list_comments.php',1260,'feature_article_comments','tiki_p_admin','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Comments','tiki-list_comments.php',1260,'feature_blog_comments','tiki_p_admin','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Comments','tiki-list_comments.php',1260,'feature_file_galleries_comments','tiki_p_admin','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Comments','tiki-list_comments.php',1260,'feature_image_galleries_comments','tiki_p_admin','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Comments','tiki-list_comments.php',1260,'feature_poll_comments','tiki_p_admin','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Comments','tiki-list_comments.php',1260,'feature_faq_comments','tiki_p_admin','')
go


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Contribution','tiki-admin_contribution.php',1265,'feature_contribution','tiki_p_admin_contribution','')
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_menus
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_menus"
go


CREATE TABLE "tiki_menus" (
  "menuId" numeric(8 ,0) identity,
  "name" varchar(200) default '' NOT NULL,
  "description" text default '',
  "type" char(1) default NULL NULL,
  PRIMARY KEY ("menuId")
) ENGINE=MyISAM  
go


-- --------------------------------------------------------
INSERT INTO "tiki_menus" ("menuId","name","description","type") VALUES ('42','Application menu','Main extensive navigation menu','d')
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_minical_events
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 09, 2003 at 04:06 AM
--

-- DROP TABLE "tiki_minical_events"
go


CREATE TABLE "tiki_minical_events" (
  "user" varchar(200) default '',
  "eventId" numeric(12 ,0) identity,
  "title" varchar(250) default NULL NULL,
  "description" text default '',
  "start" numeric(14,0) default NULL NULL,
  "end" numeric(14,0) default NULL NULL,
  "security" char(1) default NULL NULL,
  "duration" numeric(3,0) default NULL NULL,
  "topicId" numeric(12,0) default NULL NULL,
  "reminded" char(1) default NULL NULL,
  PRIMARY KEY ("eventId")
) ENGINE=MyISAM  
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_minical_topics
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_minical_topics"
go


CREATE TABLE "tiki_minical_topics" (
  "user" varchar(200) default '',
  "topicId" numeric(12 ,0) identity,
  "name" varchar(250) default NULL NULL,
  "filename" varchar(200) default NULL NULL,
  "filetype" varchar(200) default NULL NULL,
  "filesize" varchar(200) default NULL NULL,
  "data" image default '',
  "path" varchar(250) default NULL NULL,
  "isIcon" char(1) default NULL NULL,
  PRIMARY KEY ("topicId")
) ENGINE=MyISAM  
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_modules
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 11:44 PM
--

-- DROP TABLE "tiki_modules"
go


CREATE TABLE "tiki_modules" (
  "moduleId" numeric(8 ,0) identity,
  "name" varchar(200) default '' NOT NULL,
  "position" char(1) default NULL NULL,
  "ord" numeric(4,0) default NULL NULL,
  "type" char(1) default NULL NULL,
  "title" varchar(255) default NULL NULL,
  "cache_time" numeric(14,0) default NULL NULL,
  "rows" numeric(4,0) default NULL NULL,
  "params" varchar(255) default NULL NULL,
  "groups" text default '',
  PRIMARY KEY ("name","position","ord","params")
) ENGINE=MyISAM
go


CREATE  INDEX "tiki_modules_positionType" ON "tiki_modules"("position" "type")
go
CREATE  INDEX "tiki_modules_moduleId" ON "tiki_modules"("moduleId")
go
-- --------------------------------------------------------
INSERT INTO "tiki_modules" ("name","position","ord","cache_time","groups") VALUES ('login_box','r',1,0,'a:2:{i:0;s:10:"Registered";i:1;s:9:"Anonymous";}')
go


INSERT INTO "tiki_modules" ("name","position","ord","cache_time","params","groups") VALUES ('mnu_application_menu','l',1,0,'flip=y','a:2:{i:0;s:10:"Registered";i:1;s:9:"Anonymous";}')
go


INSERT INTO "tiki_modules" ("name","position","ord","cache_time","groups") VALUES ('quick_edit','l',2,0,'a:1:{i:0;s:6:\"Admins\";}')
go


INSERT INTO "tiki_modules" ("name","position","ord","cache_time","groups") VALUES ('assistant','l',10,0,'a:2:{i:0;s:10:"Registered";i:1;s:9:"Anonymous";}')
go


INSERT INTO "tiki_modules" ("name","position","ord","cache_time","groups") VALUES ('since_last_visit_new','r',40,0,'a:1:{i:0;s:6:\"Admins\";}')
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_newsletter_subscriptions
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_newsletter_subscriptions"
go


CREATE TABLE "tiki_newsletter_subscriptions" (
  "nlId" numeric(12,0) default '0' NOT NULL,
  "email" varchar(255) default '' NOT NULL,
  "code" varchar(32) default NULL NULL,
  "valid" char(1) default NULL NULL,
  "subscribed" numeric(14,0) default NULL NULL,
  "isUser" char(1) default 'n' NOT NULL,
  PRIMARY KEY ("nlId","email","isUser")
) ENGINE=MyISAM
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_newsletter_groups
--
-- Creation: Jan 18, 2005
-- Last update: Jan 18, 2005
--

-- DROP TABLE "tiki_newsletter_groups"
go


CREATE TABLE "tiki_newsletter_groups" (
  "nlId" numeric(12,0) default '0' NOT NULL,
  "groupName" varchar(255) default '' NOT NULL,
  "code" varchar(32) default NULL NULL,
  PRIMARY KEY ("nlId","groupName")
) ENGINE=MyISAM
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_newsletter_included
--
-- Creation: Sep 25, 2007
-- Last update: Sep 25, 2007
--

-- DROP TABLE "tiki_newsletter_included"
go


CREATE TABLE "tiki_newsletter_included" (
  "nlId" numeric(12,0) default '0' NOT NULL,
  "includedId" numeric(12,0) default '0' NOT NULL,
  PRIMARY KEY ("nlId","includedId")
) ENGINE=MyISAM
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_newsletters
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_newsletters"
go


CREATE TABLE "tiki_newsletters" (
  "nlId" numeric(12 ,0) identity,
  "name" varchar(200) default NULL NULL,
  "description" text default '',
  "created" numeric(14,0) default NULL NULL,
  "lastSent" numeric(14,0) default NULL NULL,
  "editions" numeric(10,0) default NULL NULL,
  "users" numeric(10,0) default NULL NULL,
  "allowUserSub" char(1) default 'y',
  "allowTxt" char(1) default 'y',
  "allowAnySub" char(1) default NULL NULL,
  "unsubMsg" char(1) default 'y',
  "validateAddr" char(1) default 'y',
  "frequency" numeric(14,0) default NULL NULL,
  "author" varchar(200) default NULL NULL,
  PRIMARY KEY ("nlId")
) ENGINE=MyISAM  
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_newsreader_marks
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_newsreader_marks"
go


CREATE TABLE "tiki_newsreader_marks" (
  "user" varchar(200) default '' NOT NULL,
  "serverId" numeric(12,0) default '0' NOT NULL,
  "groupName" varchar(255) default '' NOT NULL,
  "timestamp" numeric(14,0) default '0' NOT NULL,
  PRIMARY KEY ("`user`","serverId","groupName")
) ENGINE=MyISAM
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_newsreader_servers
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_newsreader_servers"
go


CREATE TABLE "tiki_newsreader_servers" (
  "user" varchar(200) default '' NOT NULL,
  "serverId" numeric(12 ,0) identity,
  "server" varchar(250) default NULL NULL,
  "port" numeric(4,0) default NULL NULL,
  "username" varchar(200) default NULL NULL,
  "password" varchar(200) default NULL NULL,
  PRIMARY KEY ("serverId")
) ENGINE=MyISAM  
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_page_footnotes
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 10:00 AM
-- Last check: Jul 12, 2003 at 10:00 AM
--

-- DROP TABLE "tiki_page_footnotes"
go


CREATE TABLE "tiki_page_footnotes" (
  "user" varchar(200) default '' NOT NULL,
  "pageName" varchar(250) default '' NOT NULL,
  "data" text default '',
  PRIMARY KEY ("`user`","pageName")
) ENGINE=MyISAM
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_pages
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 13, 2003 at 01:52 AM
-- Last check: Jul 12, 2003 at 10:01 AM
--

-- DROP TABLE "tiki_pages"
go


CREATE TABLE "tiki_pages" (
  "page_id" numeric(14 ,0) identity,
  "pageName" varchar(160) default '' NOT NULL,
  "hits" numeric(8,0) default NULL NULL,
  "data" mediumtext,
  "description" varchar(200) default NULL NULL,
  "lastModif" numeric(14,0) default NULL NULL,
  "comment" varchar(200) default NULL NULL,
  "version" numeric(8,0) default '0' NOT NULL,
  "user" varchar(200) default '',
  "ip" varchar(15) default NULL NULL,
  "flag" char(1) default NULL NULL,
  "points" numeric(8,0) default NULL NULL,
  "votes" numeric(8,0) default NULL NULL,
  "cache" longtext,
  "wiki_cache" numeric(10,0) default NULL NULL,
  "cache_timestamp" numeric(14,0) default NULL NULL,
  "pageRank" decimal(4,3) default NULL NULL,
  "creator" varchar(200) default NULL NULL,
  "page_size" numeric(10,0) default '0',
  "lang" varchar(16) default NULL NULL,
  "lockedby" varchar(200) default NULL NULL,
  "is_html" numeric(1,0) default 0,
  "created" numeric(14,0) default NULL NULL,
  "wysiwyg" char(1) default NULL NULL,
  PRIMARY KEY ("page_id")
  KEY lastModif(lastModif)
) ENGINE=MyISAM 
go


CREATE  INDEX "tiki_pages_data" ON "tiki_pages"("data")
go
CREATE  INDEX "tiki_pages_pageRank" ON "tiki_pages"("pageRank")
go
CREATE  INDEX "tiki_pages_ft" ON "tiki_pages"("pageName","description","data")
go
CREATE UNIQUE INDEX "tiki_pages_pageName" ON "tiki_pages"("pageName")
go
-- --------------------------------------------------------

--
-- Table structure for table tiki_page_drafts
--
-- Creation: March 12, 2006 at 
--

-- DROP TABLE "tiki_page_drafts"
go


CREATE TABLE "tiki_page_drafts" (
  "user" varchar(200) default '',
  "pageName" varchar(255) NOT NULL,
  "data" mediumtext,
  "description" varchar(200) default NULL NULL,
  "comment" varchar(200) default NULL NULL,
  "lastModif" numeric(14,0) default NULL NULL,
  PRIMARY KEY ("pageName","`user`")
) ENGINE=MyISAM
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_pageviews
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 13, 2003 at 01:52 AM
--

-- DROP TABLE "tiki_pageviews"
go


CREATE TABLE "tiki_pageviews" (
  "day" numeric(14,0) default '0' NOT NULL,
  "pageviews" numeric(14,0) default NULL NULL,
  PRIMARY KEY ("day")
) ENGINE=MyISAM
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_poll_objects
--

-- DROP TABLE "tiki_poll_objects"
go


CREATE TABLE `tiki_poll_objects` (
  `catObjectId` numeric(11,0) default '0' NOT NULL,
  `pollId` numeric(11,0) default '0' NOT NULL,
  `title` varchar(255) default NULL NULL,
  PRIMARY KEY ("`catObjectId`","`pollId`")
) ENGINE=MyISAM
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_poll_options
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 06, 2003 at 07:57 PM
--

-- DROP TABLE "tiki_poll_options"
go


CREATE TABLE "tiki_poll_options" (
  "pollId" numeric(8,0) default '0' NOT NULL,
  "optionId" numeric(8 ,0) identity,
  "title" varchar(200) default NULL NULL,
  "position" numeric(4,0) default '0' NOT NULL,
  "votes" numeric(8,0) default NULL NULL,
  PRIMARY KEY ("optionId")
) ENGINE=MyISAM  
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_polls
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 06, 2003 at 07:57 PM
--

-- DROP TABLE "tiki_polls"
go


CREATE TABLE "tiki_polls" (
  "pollId" numeric(8 ,0) identity,
  "title" varchar(200) default NULL NULL,
  "votes" numeric(8,0) default NULL NULL,
  "active" char(1) default NULL NULL,
  "publishDate" numeric(14,0) default NULL NULL,
  PRIMARY KEY ("pollId")
) ENGINE=MyISAM  
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_preferences
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 12:04 PM
--

-- DROP TABLE "tiki_preferences"
go


CREATE TABLE "tiki_preferences" (
  "name" varchar(40) default '' NOT NULL,
  "value" text default '',
  PRIMARY KEY ("name")
) ENGINE=MyISAM
go


INSERT INTO "," ("name","value") VALUES ('pref_syntax', '1.10')
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_private_messages
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_private_messages"
go


CREATE TABLE "tiki_private_messages" (
  "messageId" numeric(8 ,0) identity,
  "toNickname" varchar(200) default '' NOT NULL,
  "message" varchar(255) default NULL NULL,
  "poster" varchar(200) default 'anonymous' NOT NULL,
  "timestamp" numeric(14,0) default NULL NULL,
  "received" numeric(1,0) default 0 not null,
  "key"(received),
  "key"(timestamp),
  PRIMARY KEY ("messageId")
) ENGINE=MyISAM  
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_programmed_content
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_programmed_content"
go


CREATE TABLE "tiki_programmed_content" (
  "pId" numeric(8 ,0) identity,
  "contentId" numeric(8,0) default '0' NOT NULL,
  "publishDate" numeric(14,0) default '0' NOT NULL,
  "data" text default '',
  PRIMARY KEY ("pId")
) ENGINE=MyISAM  
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_quiz_question_options
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_quiz_question_options"
go


CREATE TABLE "tiki_quiz_question_options" (
  "optionId" numeric(10 ,0) identity,
  "questionId" numeric(10,0) default NULL NULL,
  "optionText" text default '',
  "points" numeric(4,0) default NULL NULL,
  PRIMARY KEY ("optionId")
) ENGINE=MyISAM  
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_quiz_questions
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_quiz_questions"
go


CREATE TABLE "tiki_quiz_questions" (
  "questionId" numeric(10 ,0) identity,
  "quizId" numeric(10,0) default NULL NULL,
  "question" text default '',
  "position" numeric(4,0) default NULL NULL,
  "type" char(1) default NULL NULL,
  "maxPoints" numeric(4,0) default NULL NULL,
  PRIMARY KEY ("questionId")
) ENGINE=MyISAM  
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_quiz_results
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_quiz_results"
go


CREATE TABLE "tiki_quiz_results" (
  "resultId" numeric(10 ,0) identity,
  "quizId" numeric(10,0) default NULL NULL,
  "fromPoints" numeric(4,0) default NULL NULL,
  "toPoints" numeric(4,0) default NULL NULL,
  "answer" text default '',
  PRIMARY KEY ("resultId")
) ENGINE=MyISAM  
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_quiz_stats
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_quiz_stats"
go


CREATE TABLE "tiki_quiz_stats" (
  "quizId" numeric(10,0) default '0' NOT NULL,
  "questionId" numeric(10,0) default '0' NOT NULL,
  "optionId" numeric(10,0) default '0' NOT NULL,
  "votes" numeric(10,0) default NULL NULL,
  PRIMARY KEY ("quizId","questionId","optionId")
) ENGINE=MyISAM
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_quiz_stats_sum
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_quiz_stats_sum"
go


CREATE TABLE "tiki_quiz_stats_sum" (
  "quizId" numeric(10,0) default '0' NOT NULL,
  "quizName" varchar(255) default NULL NULL,
  "timesTaken" numeric(10,0) default NULL NULL,
  "avgpoints" decimal(5,2) default NULL NULL,
  "avgavg" decimal(5,2) default NULL NULL,
  "avgtime" decimal(5,2) default NULL NULL,
  PRIMARY KEY ("quizId")
) ENGINE=MyISAM
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_quizzes
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: April 29, 2004
--

-- DROP TABLE "tiki_quizzes"
go


CREATE TABLE "tiki_quizzes" (
  "quizId" numeric(10 ,0) identity,
  "name" varchar(255) default NULL NULL,
  "description" text default '',
  "canRepeat" char(1) default NULL NULL,
  "storeResults" char(1) default NULL NULL,
  "questionsPerPage" numeric(4,0) default NULL NULL,
  "timeLimited" char(1) default NULL NULL,
  "timeLimit" numeric(14,0) default NULL NULL,
  "created" numeric(14,0) default NULL NULL,
  "taken" numeric(10,0) default NULL NULL,
  "immediateFeedback" char(1) default NULL NULL,
  "showAnswers" char(1) default NULL NULL,
  "shuffleQuestions" char(1) default NULL NULL,
  "shuffleAnswers" char(1) default NULL NULL,
  "publishDate" numeric(14,0) default NULL NULL,
  "expireDate" numeric(14,0) default NULL NULL,
  "bDeleted" char(1) default NULL NULL,
  "nVersion" numeric(4,0) NOT NULL,
  "nAuthor" numeric(4,0) default NULL NULL,
  "bOnline" char(1) default NULL NULL,
  "bRandomQuestions" char(1) default NULL NULL,
  "nRandomQuestions" numeric(4,0) default NULL NULL,
  "bLimitQuestionsPerPage" char(1) default NULL NULL,
  "nLimitQuestionsPerPage" numeric(4,0) default NULL NULL,
  "bMultiSession" char(1) default NULL NULL,
  "nCanRepeat" numeric(4,0) default NULL NULL,
  "sGradingMethod" varchar(80) default NULL NULL,
  "sShowScore" varchar(80) default NULL NULL,
  "sShowCorrectAnswers" varchar(80) default NULL NULL,
  "sPublishStats" varchar(80) default NULL NULL,
  "bAdditionalQuestions" char(1) default NULL NULL,
  "bForum" char(1) default NULL NULL,
  "sForum" varchar(80) default NULL NULL,
  "sPrologue" text default '',
  "sData" text default '',
  "sEpilogue" text default '',
  "passingperct" numeric(4,0) default 0,
  PRIMARY KEY ("quizId","nVersion")
) ENGINE=MyISAM  
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_received_articles
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_received_articles"
go


CREATE TABLE "tiki_received_articles" (
  "receivedArticleId" numeric(14 ,0) identity,
  "receivedFromSite" varchar(200) default NULL NULL,
  "receivedFromUser" varchar(200) default NULL NULL,
  "receivedDate" numeric(14,0) default NULL NULL,
  "title" varchar(80) default NULL NULL,
  "authorName" varchar(60) default NULL NULL,
  "size" numeric(12,0) default NULL NULL,
  "useImage" char(1) default NULL NULL,
  "image_name" varchar(80) default NULL NULL,
  "image_type" varchar(80) default NULL NULL,
  "image_size" numeric(14,0) default NULL NULL,
  "image_x" numeric(4,0) default NULL NULL,
  "image_y" numeric(4,0) default NULL NULL,
  "image_data" image default '',
  "publishDate" numeric(14,0) default NULL NULL,
  "expireDate" numeric(14,0) default NULL NULL,
  "created" numeric(14,0) default NULL NULL,
  "heading" text default '',
  "body" image default '',
  "hash" varchar(32) default NULL NULL,
  "author" varchar(200) default NULL NULL,
  "type" varchar(50) default NULL NULL,
  "rating" decimal(3,2) default NULL NULL,
  PRIMARY KEY ("receivedArticleId")
) ENGINE=MyISAM  
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_received_pages
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 09, 2003 at 03:56 AM
--

-- DROP TABLE "tiki_received_pages"
go


CREATE TABLE "tiki_received_pages" (
  "receivedPageId" numeric(14 ,0) identity,
  "pageName" varchar(160) default '' NOT NULL,
  "data" image default '',
  "description" varchar(200) default NULL NULL,
  "comment" varchar(200) default NULL NULL,
  "receivedFromSite" varchar(200) default NULL NULL,
  "receivedFromUser" varchar(200) default NULL NULL,
  "receivedDate" numeric(14,0) default NULL NULL,
  "parent" varchar(255) default NULL NULL,
  "position" numeric(3,0) unsigned default NULL NULL,
  "alias" varchar(255) default NULL NULL,
  "structureName"  varchar(250) default NULL NULL,
  "parentName"  varchar(250) default NULL NULL,
  "page_alias" varchar(250) default '',
  "pos" numeric(4,0) default NULL NULL,
  PRIMARY KEY ("receivedPageId")
) ENGINE=MyISAM  
go


CREATE  INDEX "tiki_received_pages_structureName" ON "tiki_received_pages"("structureName")
go
-- --------------------------------------------------------

--
-- Table structure for table tiki_referer_stats
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 13, 2003 at 01:30 AM
--

-- DROP TABLE "tiki_referer_stats"
go


CREATE TABLE "tiki_referer_stats" (
  "referer" varchar(255) default '' NOT NULL,
  "hits" numeric(10,0) default NULL NULL,
  "last" numeric(14,0) default NULL NULL,
  PRIMARY KEY ("referer")
) ENGINE=MyISAM
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_related_categories
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_related_categories"
go


CREATE TABLE "tiki_related_categories" (
  "categId" numeric(10,0) default '0' NOT NULL,
  "relatedTo" numeric(10,0) default '0' NOT NULL,
  PRIMARY KEY ("categId","relatedTo")
) ENGINE=MyISAM
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_rss_modules
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 10:19 AM
--

-- DROP TABLE "tiki_rss_modules"
go


CREATE TABLE "tiki_rss_modules" (
  "rssId" numeric(8 ,0) identity,
  "name" varchar(30) default '' NOT NULL,
  "description" text default '',
  "url" varchar(255) default '' NOT NULL,
  "refresh" numeric(8,0) default NULL NULL,
  "lastUpdated" numeric(14,0) default NULL NULL,
  "showTitle" char(1) default 'n',
  "showPubDate" char(1) default 'n',
  "content" image default '',
  PRIMARY KEY ("rssId")
) ENGINE=MyISAM  
go


CREATE  INDEX "tiki_rss_modules_name" ON "tiki_rss_modules"("name")
go
-- --------------------------------------------------------

--
-- Table structure for table tiki_rss_feeds
--
-- Creation: Oct 14, 2003 at 20:34 PM
-- Last update: Oct 14, 2003 at 20:34 PM
--

-- DROP TABLE "tiki_rss_feeds"
go


CREATE TABLE "tiki_rss_feeds" (
  "name" varchar(30) default '' NOT NULL,
  "rssVer" char(1) default '1' NOT NULL,
  "refresh" numeric(8,0) default '300',
  "lastUpdated" numeric(14,0) default NULL NULL,
  "cache" image default '',
  PRIMARY KEY ("name","rssVer")
) ENGINE=MyISAM
go


-- --------------------------------------------------------

-- DROP TABLE "tiki_searchindex"
go


CREATE TABLE "tiki_searchindex"(
  "searchword" varchar(80) default '' NOT NULL,
  "location" varchar(80) default '' NOT NULL,
  "page" varchar(255) default '' NOT NULL,
  "count" numeric(11,0) default '1' NOT NULL,
  "last_update" numeric(11,0) default '0' NOT NULL,
  PRIMARY KEY ("searchword","location","page")
) ENGINE=MyISAM
go


CREATE  INDEX "tiki_searchindex_last_update" ON "tiki_searchindex"("last_update")
go
CREATE  INDEX "tiki_searchindex_location" ON "tiki_searchindex"("location" "page")
go

-- LRU (last recently used) list for searching parts of words
-- DROP TABLE "tiki_searchsyllable"
go


CREATE TABLE "tiki_searchsyllable"(
  "syllable" varchar(80) default '' NOT NULL,
  "lastUsed" numeric(11,0) default '0' NOT NULL,
  "lastUpdated" numeric(11,0) default '0' NOT NULL,
  PRIMARY KEY ("syllable")
) ENGINE=MyISAM
go


CREATE  INDEX "tiki_searchsyllable_lastUsed" ON "tiki_searchsyllable"("lastUsed")
go

-- searchword caching table for search syllables
-- DROP TABLE "tiki_searchwords"
go


CREATE TABLE "tiki_searchwords"(
  "syllable" varchar(80) default '' NOT NULL,
  "searchword" varchar(80) default '' NOT NULL,
  PRIMARY KEY ("syllable","searchword")
) ENGINE=MyISAM
go



--
-- Table structure for table tiki_search_stats
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 10:55 PM
--

-- DROP TABLE "tiki_search_stats"
go


CREATE TABLE "tiki_search_stats" (
  "term" varchar(50) default '' NOT NULL,
  "hits" numeric(10,0) default NULL NULL,
  PRIMARY KEY ("term")
) ENGINE=MyISAM
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_secdb
--
--

-- DROP TABLE "tiki_secdb"
go


CREATE TABLE "tiki_secdb"(
  "md5_value" varchar(32) NOT NULL,
  "filename" varchar(250) NOT NULL,
  "tiki_version" varchar(60) NOT NULL,
  "severity" numeric(4,0) default '0' NOT NULL,
  PRIMARY KEY ("md5_value","filename","tiki_version")
) ENGINE=MyISAM
go


CREATE  INDEX "tiki_secdb_sdb_fn" ON "tiki_secdb"("filename")
go

--
-- Table structure for table tiki_semaphores
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 13, 2003 at 01:52 AM
--

-- DROP TABLE "tiki_semaphores"
go


CREATE TABLE "tiki_semaphores" (
  "semName" varchar(250) default '' NOT NULL,
  "objectType" varchar(20) default 'wiki page',
  "user" varchar(200) default NULL NULL,
  "timestamp" numeric(14,0) default NULL NULL,
  PRIMARY KEY ("semName")
) ENGINE=MyISAM
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_sent_newsletters
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_sent_newsletters"
go


CREATE TABLE "tiki_sent_newsletters" (
  "editionId" numeric(12 ,0) identity,
  "nlId" numeric(12,0) default '0' NOT NULL,
  "users" numeric(10,0) default NULL NULL,
  "sent" numeric(14,0) default NULL NULL,
  "subject" varchar(200) default NULL NULL,
  "data" image default '',
  "datatxt" image default '',
  PRIMARY KEY ("editionId")
) ENGINE=MyISAM  
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_sent_newsletters_errors
--

-- DROP TABLE "tiki_sent_newsletters_errors"
go


CREATE TABLE "tiki_sent_newsletters_errors" (
  "editionId" numeric(12,0) default NULL NULL,
  "email" varchar(255) default '',
  "login" varchar(40) default '',
  "error" char(1) default '',
  KEY  (editionId)
) ENGINE=MyISAM 
go


-- --------------------------------------------------------


--
-- Table structure for table tiki_sessions
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 13, 2003 at 01:52 AM
--

-- DROP TABLE "tiki_sessions"
go


CREATE TABLE "tiki_sessions" (
  "sessionId" varchar(32) default '' NOT NULL,
  "user" varchar(200) default NULL NULL,
  "timestamp" numeric(14,0) default NULL NULL,
  "tikihost" varchar(200) default NULL NULL,
  PRIMARY KEY ("sessionId")
) ENGINE=MyISAM
go


CREATE  INDEX "tiki_sessions_user" ON "tiki_sessions"("user")
go
CREATE  INDEX "tiki_sessions_timestamp" ON "tiki_sessions"("timestamp")
go
-- --------------------------------------------------------

-- Tables for TikiSheet
-- DROP TABLE "tiki_sheet_layout"
go


CREATE TABLE "tiki_sheet_layout" (
  "sheetId" numeric(8,0) default '0' NOT NULL,
  "begin" numeric(10,0) default '0' NOT NULL,
  "end" numeric(10,0) default NULL NULL,
  "headerRow" numeric(4,0) default '0' NOT NULL,
  "footerRow" numeric(4,0) default '0' NOT NULL,
  "className" varchar(64) default NULL NULL
) ENGINE=MyISAM
go


CREATE UNIQUE INDEX "tiki_sheet_layout_sheetId" ON "tiki_sheet_layout"("sheetId","begin")
go

-- DROP TABLE "tiki_sheet_values"
go


CREATE TABLE "tiki_sheet_values" (
  "sheetId" numeric(8,0) default '0' NOT NULL,
  "begin" numeric(10,0) default '0' NOT NULL,
  "end" numeric(10,0) default NULL NULL,
  "rowIndex" numeric(4,0) default '0' NOT NULL,
  "columnIndex" numeric(4,0) default '0' NOT NULL,
  "value" varchar(255) default NULL NULL,
  "calculation" varchar(255) default NULL NULL,
  "width" numeric(4,0) default '1' NOT NULL,
  "height" numeric(4,0) default '1' NOT NULL,
  "format" varchar(255) default NULL NULL,
  "user" varchar(200) default NULL NULL
) ENGINE=MyISAM
go


CREATE  INDEX "tiki_sheet_values_sheetId_2" ON "tiki_sheet_values"("sheetId","rowIndex","columnIndex")
go
CREATE UNIQUE INDEX "tiki_sheet_values_sheetId" ON "tiki_sheet_values"("sheetId","begin","rowIndex","columnIndex")
go

-- DROP TABLE "tiki_sheets"
go


CREATE TABLE "tiki_sheets" (
  "sheetId" numeric(8 ,0) identity,
  "title" varchar(200) default '' NOT NULL,
  "description" text default '',
  "author" varchar(200) default '' NOT NULL,
  PRIMARY KEY ("sheetId")
) ENGINE=MyISAM
go



--
-- Table structure for table tiki_shoutbox
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 08:21 PM
--

-- DROP TABLE "tiki_shoutbox"
go


CREATE TABLE "tiki_shoutbox" (
  "msgId" numeric(10 ,0) identity,
  "message" varchar(255) default NULL NULL,
  "timestamp" numeric(14,0) default NULL NULL,
  "user" varchar(200) default NULL NULL,
  "hash" varchar(32) default NULL NULL,
  PRIMARY KEY ("msgId")
) ENGINE=MyISAM  
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_shoutbox_words
--

-- DROP TABLE "tiki_shoutbox_words"
go


CREATE TABLE "tiki_shoutbox_words" (
  "word" VARCHAR( 40 ) NOT NULL ,
  "qty" INT DEFAULT '0' NOT NULL ,
  PRIMARY KEY ("word")
) ENGINE=MyISAM
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_structure_versions
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_structure_versions"
go


CREATE TABLE "tiki_structure_versions" (
  "structure_id" numeric(14 ,0) identity,
  "version" numeric(14,0) default NULL NULL,
  PRIMARY KEY ("structure_id")
) ENGINE=MyISAM  
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_structures
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_structures"
go


CREATE TABLE "tiki_structures" (
  "page_ref_id" numeric(14 ,0) identity,
  "structure_id" numeric(14,0) NOT NULL,
  "parent_id" numeric(14,0) default NULL NULL,
  "page_id" numeric(14,0) NOT NULL,
  "page_version" numeric(8,0) default NULL NULL,
  "page_alias" varchar(240) default '' NOT NULL,
  "pos" numeric(4,0) default NULL NULL,
  PRIMARY KEY ("page_ref_id")
) ENGINE=MyISAM  
go


CREATE  INDEX "tiki_structures_pidpaid" ON "tiki_structures"("page_id","parent_id")
go
CREATE  INDEX "tiki_structures_page_id" ON "tiki_structures"("page_id")
go
-- --------------------------------------------------------

--
-- Table structure for table tiki_submissions
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Nov 29, 2006 at 08:46 PM
--

-- DROP TABLE "tiki_submissions"
go


CREATE TABLE "tiki_submissions" (
  "subId" numeric(8 ,0) identity,
  "topline" varchar(255) default NULL NULL,
  "title" varchar(255) default NULL NULL,
  "subtitle" varchar(255) default NULL NULL,
  "linkto" varchar(255) default NULL NULL,
  "lang" varchar(16) default NULL NULL,
  "authorName" varchar(60) default NULL NULL,
  "topicId" numeric(14,0) default NULL NULL,
  "topicName" varchar(40) default NULL NULL,
  "size" numeric(12,0) default NULL NULL,
  "useImage" char(1) default NULL NULL,
  "image_name" varchar(80) default NULL NULL,
  "image_caption" text default NULL NULL,
  "image_type" varchar(80) default NULL NULL,
  "image_size" numeric(14,0) default NULL NULL,
  "image_x" numeric(4,0) default NULL NULL,
  "image_y" numeric(4,0) default NULL NULL,
  "image_data" image default '',
  "publishDate" numeric(14,0) default NULL NULL,
  "expireDate" numeric(14,0) default NULL NULL,
  "created" numeric(14,0) default NULL NULL,
  "bibliographical_references" text default '',
  "resume" text default '',
  "heading" text default '',
  "body" text default '',
  "hash" varchar(32) default NULL NULL,
  "nbreads" numeric(14,0) default NULL NULL,
  "author" varchar(200) default NULL NULL,
  "votes" numeric(8,0) default NULL NULL,
  "points" numeric(14,0) default NULL NULL,
  "type" varchar(50) default NULL NULL,
  "rating" decimal(3,2) default NULL NULL,
  "isfloat" char(1) default NULL NULL,
  PRIMARY KEY ("subId")
) ENGINE=MyISAM  
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_suggested_faq_questions
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 11, 2003 at 08:52 PM
--

-- DROP TABLE "tiki_suggested_faq_questions"
go


CREATE TABLE "tiki_suggested_faq_questions" (
  "sfqId" numeric(10 ,0) identity,
  "faqId" numeric(10,0) default '0' NOT NULL,
  "question" text default '',
  "answer" text default '',
  "created" numeric(14,0) default NULL NULL,
  "user" varchar(200) default NULL NULL,
  PRIMARY KEY ("sfqId")
) ENGINE=MyISAM  
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_survey_question_options
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 11, 2003 at 12:55 AM
--

-- DROP TABLE "tiki_survey_question_options"
go


CREATE TABLE "tiki_survey_question_options" (
  "optionId" numeric(12 ,0) identity,
  "questionId" numeric(12,0) default '0' NOT NULL,
  "qoption" text default '',
  "votes" numeric(10,0) default NULL NULL,
  PRIMARY KEY ("optionId")
) ENGINE=MyISAM  
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_survey_questions
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 11, 2003 at 11:55 PM
--

-- DROP TABLE "tiki_survey_questions"
go


CREATE TABLE "tiki_survey_questions" (
  "questionId" numeric(12 ,0) identity,
  "surveyId" numeric(12,0) default '0' NOT NULL,
  "question" text default '',
  "options" text default '',
  "type" char(1) default NULL NULL,
  "position" numeric(5,0) default NULL NULL,
  "votes" numeric(10,0) default NULL NULL,
  "value" numeric(10,0) default NULL NULL,
  "average" decimal(4,2) default NULL NULL,
  PRIMARY KEY ("questionId")
) ENGINE=MyISAM  
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_surveys
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 07:40 PM
--

-- DROP TABLE "tiki_surveys"
go


CREATE TABLE "tiki_surveys" (
  "surveyId" numeric(12 ,0) identity,
  "name" varchar(200) default NULL NULL,
  "description" text default '',
  "taken" numeric(10,0) default NULL NULL,
  "lastTaken" numeric(14,0) default NULL NULL,
  "created" numeric(14,0) default NULL NULL,
  "status" char(1) default NULL NULL,
  PRIMARY KEY ("surveyId")
) ENGINE=MyISAM  
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_tags
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 06, 2003 at 02:58 AM
--

-- DROP TABLE "tiki_tags"
go


CREATE TABLE "tiki_tags" (
  "tagName" varchar(80) default '' NOT NULL,
  "pageName" varchar(160) default '' NOT NULL,
  "hits" numeric(8,0) default NULL NULL,
  "description" varchar(200) default NULL NULL,
  "data" image default '',
  "lastModif" numeric(14,0) default NULL NULL,
  "comment" varchar(200) default NULL NULL,
  "version" numeric(8,0) default '0' NOT NULL,
  "user" varchar(200) default NULL NULL,
  "ip" varchar(15) default NULL NULL,
  "flag" char(1) default NULL NULL,
  PRIMARY KEY ("tagName","pageName")
) ENGINE=MyISAM
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_theme_control_categs
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_theme_control_categs"
go


CREATE TABLE "tiki_theme_control_categs" (
  "categId" numeric(12,0) default '0' NOT NULL,
  "theme" varchar(250) default '' NOT NULL,
  PRIMARY KEY ("categId")
) ENGINE=MyISAM
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_theme_control_objects
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_theme_control_objects"
go


CREATE TABLE "tiki_theme_control_objects" (
  "objId" varchar(250) default '' NOT NULL,
  "type" varchar(250) default '' NOT NULL,
  "name" varchar(250) default '' NOT NULL,
  "theme" varchar(250) default '' NOT NULL,
  PRIMARY KEY ("objId","type")
) ENGINE=MyISAM
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_theme_control_sections
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_theme_control_sections"
go


CREATE TABLE "tiki_theme_control_sections" (
  "section" varchar(250) default '' NOT NULL,
  "theme" varchar(250) default '' NOT NULL,
  PRIMARY KEY ("section")
) ENGINE=MyISAM
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_topics
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 04, 2003 at 10:10 PM
--

-- DROP TABLE "tiki_topics"
go


CREATE TABLE "tiki_topics" (
  "topicId" numeric(14 ,0) identity,
  "name" varchar(40) default NULL NULL,
  "image_name" varchar(80) default NULL NULL,
  "image_type" varchar(80) default NULL NULL,
  "image_size" numeric(14,0) default NULL NULL,
  "image_data" image default '',
  "active" char(1) default NULL NULL,
  "created" numeric(14,0) default NULL NULL,
  PRIMARY KEY ("topicId")
) ENGINE=MyISAM  
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_tracker_fields
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 08, 2003 at 01:48 PM
--

-- DROP TABLE "tiki_tracker_fields"
go


CREATE TABLE "tiki_tracker_fields" (
  "fieldId" numeric(12 ,0) identity,
  "trackerId" numeric(12,0) default '0' NOT NULL,
  "name" varchar(255) default NULL NULL,
  "options" text default '',
  "type" char(1) default NULL NULL,
  "isMain" char(1) default NULL NULL,
  "isTblVisible" char(1) default NULL NULL,
  "position" numeric(4,0) default NULL NULL,
  "isSearchable" char(1) default 'y' NOT NULL,
  "isPublic" char(1) default 'n' NOT NULL,
  "isHidden" char(1) default 'n' NOT NULL,
  "isMandatory" char(1) default 'n' NOT NULL,
  "isMultilingual" char(1) default 'n',
  "description" text default '',
  "itemChoices" text default '',
  PRIMARY KEY ("fieldId")
) ENGINE=MyISAM  
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_tracker_item_attachments
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_tracker_item_attachments"
go


CREATE TABLE "tiki_tracker_item_attachments" (
  "attId" numeric(12 ,0) identity,
  "itemId" numeric(12,0) default 0 NOT NULL,
  "filename" varchar(80) default NULL NULL,
  "filetype" varchar(80) default NULL NULL,
  "filesize" numeric(14,0) default NULL NULL,
  "user" varchar(200) default NULL NULL,
  "data" image default '',
  "path" varchar(255) default NULL NULL,
  "hits" numeric(10,0) default NULL NULL,
  "created" numeric(14,0) default NULL NULL,
  "comment" varchar(250) default NULL NULL,
  "longdesc" image default '',
  "version" varchar(40) default NULL NULL,
  PRIMARY KEY ("attId")
) ENGINE=MyISAM  
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_tracker_item_comments
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 08:12 AM
--

-- DROP TABLE "tiki_tracker_item_comments"
go


CREATE TABLE "tiki_tracker_item_comments" (
  "commentId" numeric(12 ,0) identity,
  "itemId" numeric(12,0) default '0' NOT NULL,
  "user" varchar(200) default NULL NULL,
  "data" text default '',
  "title" varchar(200) default NULL NULL,
  "posted" numeric(14,0) default NULL NULL,
  PRIMARY KEY ("commentId")
) ENGINE=MyISAM  
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_tracker_item_fields
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 08:26 AM
--

-- DROP TABLE "tiki_tracker_item_fields"
go


CREATE TABLE "tiki_tracker_item_fields" (
  "itemId" numeric(12,0) default '0' NOT NULL,
  "fieldId" numeric(12,0) default '0' NOT NULL,
  "lang" char(16) default NULL NULL,
  "value" text default '',
  PRIMARY KEY ("itemId","fieldId","lang")
  "INDEX" fieldId (fieldId),
  "INDEX" value (value(250)),
  "INDEX" lang (lang)
) ENGINE=MyISAM
go


CREATE  INDEX "tiki_tracker_item_fields_ft" ON "tiki_tracker_item_fields"("value")
go
-- --------------------------------------------------------

--
-- Table structure for table tiki_tracker_items
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 08:26 AM
--

-- DROP TABLE "tiki_tracker_items"
go


CREATE TABLE "tiki_tracker_items" (
  "itemId" numeric(12 ,0) identity,
  "trackerId" numeric(12,0) default '0' NOT NULL,
  "created" numeric(14,0) default NULL NULL,
  "status" char(1) default NULL NULL,
  "lastModif" numeric(14,0) default NULL NULL,
  PRIMARY KEY ("itemId")
) ENGINE=MyISAM  
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_tracker_options
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 08, 2003 at 01:48 PM
--

-- DROP TABLE "tiki_tracker_options"
go


CREATE TABLE "tiki_tracker_options" (
  "trackerId" numeric(12,0) default '0' NOT NULL,
  "name" varchar(80) default '' NOT NULL,
  "value" text default NULL NULL,
  PRIMARY KEY ("trackerId","name")
) ENGINE=MyISAM 
go


-- --------------------------------------------------------


--
-- Table structure for table tiki_trackers
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 08:26 AM
--

-- DROP TABLE "tiki_trackers"
go


CREATE TABLE "tiki_trackers" (
  "trackerId" numeric(12 ,0) identity,
  "name" varchar(255) default NULL NULL,
  "description" text default '',
  "created" numeric(14,0) default NULL NULL,
  "lastModif" numeric(14,0) default NULL NULL,
  "showCreated" char(1) default NULL NULL,
  "showStatus" char(1) default NULL NULL,
  "showLastModif" char(1) default NULL NULL,
  "useComments" char(1) default NULL NULL,
  "useAttachments" char(1) default NULL NULL,
  "items" numeric(10,0) default NULL NULL,
  "showComments" char(1) default NULL NULL,
  "showAttachments" char(1) default NULL NULL,
  "orderAttachments" varchar(255) default 'filename,created,filesize,hits,desc' NOT NULL,
  PRIMARY KEY ("trackerId")
) ENGINE=MyISAM  
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_untranslated
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_untranslated"
go


CREATE TABLE "tiki_untranslated" (
  "id" numeric(14 ,0) identity,
  "source" image NOT NULL,
  "lang" char(16) default '' NOT NULL,
  PRIMARY KEY ("source","lang")
) ENGINE=MyISAM  
go


CREATE  INDEX "tiki_untranslated_id_2" ON "tiki_untranslated"("id")
go
CREATE UNIQUE INDEX "tiki_untranslated_id" ON "tiki_untranslated"("id")
go
-- --------------------------------------------------------

--
-- Table structure for table tiki_user_answers
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_user_answers"
go


CREATE TABLE "tiki_user_answers" (
  "userResultId" numeric(10,0) default '0' NOT NULL,
  "quizId" numeric(10,0) default '0' NOT NULL,
  "questionId" numeric(10,0) default '0' NOT NULL,
  "optionId" numeric(10,0) default '0' NOT NULL,
  PRIMARY KEY ("userResultId","quizId","questionId","optionId")
) ENGINE=MyISAM
go


-- --------------------------------------------------------


--
-- Table structure for table tiki_user_answers_uploads
--
-- Creation: Jan 25, 2005 at 07:42 PM
-- Last update: Jan 25, 2005 at 07:42 PM
--


-- DROP TABLE "tiki_user_answers_uploads"
go


CREATE TABLE "tiki_user_answers_uploads" (
  "answerUploadId" numeric(4 ,0) identity,
  "userResultId" numeric(11,0) default '0' NOT NULL,
  "questionId" numeric(11,0) default '0' NOT NULL,
  "filename" varchar(255) default '' NOT NULL,
  "filetype" varchar(64) default '' NOT NULL,
  "filesize" varchar(255) default '' NOT NULL,
  "filecontent" image NOT NULL,
  PRIMARY KEY ("answerUploadId")
) ENGINE=MyISAM
go




--
-- Table structure for table tiki_user_assigned_modules
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 08:25 PM
--

-- DROP TABLE "tiki_user_assigned_modules"
go


CREATE TABLE "tiki_user_assigned_modules" (
  "moduleId" numeric(8,0) NOT NULL,
  "name" varchar(200) default '' NOT NULL,
  "position" char(1) default NULL NULL,
  "ord" numeric(4,0) default NULL NULL,
  "type" char(1) default NULL NULL,
  "user" varchar(200) default '' NOT NULL,
  PRIMARY KEY ("name","user","position","ord")
) ENGINE=MyISAM
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_user_bookmarks_folders
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 11, 2003 at 08:35 AM
--

-- DROP TABLE "tiki_user_bookmarks_folders"
go


CREATE TABLE "tiki_user_bookmarks_folders" (
  "folderId" numeric(12 ,0) identity,
  "parentId" numeric(12,0) default NULL NULL,
  "user" varchar(200) default '' NOT NULL,
  "name" varchar(30) default NULL NULL,
  PRIMARY KEY ("user","folderId")
) ENGINE=MyISAM  
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_user_bookmarks_urls
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 11, 2003 at 08:36 AM
--

-- DROP TABLE "tiki_user_bookmarks_urls"
go


CREATE TABLE "tiki_user_bookmarks_urls" (
  "urlId" numeric(12 ,0) identity,
  "name" varchar(30) default NULL NULL,
  "url" varchar(250) default NULL NULL,
  "data" image default '',
  "lastUpdated" numeric(14,0) default NULL NULL,
  "folderId" numeric(12,0) default '0' NOT NULL,
  "user" varchar(200) default '' NOT NULL,
  PRIMARY KEY ("urlId")
) ENGINE=MyISAM  
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_user_mail_accounts
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_user_mail_accounts"
go


CREATE TABLE "tiki_user_mail_accounts" (
  "accountId" numeric(12 ,0) identity,
  "user" varchar(200) default '' NOT NULL,
  "account" varchar(50) default '' NOT NULL,
  "pop" varchar(255) default NULL NULL,
  "current" char(1) default NULL NULL,
  "port" numeric(4,0) default NULL NULL,
  "username" varchar(100) default NULL NULL,
  "pass" varchar(100) default NULL NULL,
  "msgs" numeric(4,0) default NULL NULL,
  "smtp" varchar(255) default NULL NULL,
  "useAuth" char(1) default NULL NULL,
  "smtpPort" numeric(4,0) default NULL NULL,
  PRIMARY KEY ("accountId")
) ENGINE=MyISAM  
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_user_menus
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 11, 2003 at 10:58 PM
--

-- DROP TABLE "tiki_user_menus"
go


CREATE TABLE "tiki_user_menus" (
  "user" varchar(200) default '' NOT NULL,
  "menuId" numeric(12 ,0) identity,
  "url" varchar(250) default NULL NULL,
  "name" varchar(40) default NULL NULL,
  "position" numeric(4,0) default NULL NULL,
  "mode" char(1) default NULL NULL,
  PRIMARY KEY ("menuId")
) ENGINE=MyISAM  
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_user_modules
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 05, 2003 at 03:16 AM
--

-- DROP TABLE "tiki_user_modules"
go


CREATE TABLE "tiki_user_modules" (
  "name" varchar(200) default '' NOT NULL,
  "title" varchar(40) default NULL NULL,
  "data" image default '',
  "parse" char(1) default NULL NULL,
  PRIMARY KEY ("name")
) ENGINE=MyISAM
go


-- --------------------------------------------------------
INSERT INTO "tiki_user_modules" ("name","title","data","parse") VALUES ('mnu_application_menu', 'Menu', '{menu id=42}', 'n')
go



--
-- Table structure for table tiki_user_notes
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 07:52 AM
--

-- DROP TABLE "tiki_user_notes"
go


CREATE TABLE "tiki_user_notes" (
  "user" varchar(200) default '' NOT NULL,
  "noteId" numeric(12 ,0) identity,
  "created" numeric(14,0) default NULL NULL,
  "name" varchar(255) default NULL NULL,
  "lastModif" numeric(14,0) default NULL NULL,
  "data" text default '',
  "size" numeric(14,0) default NULL NULL,
  "parse_mode" varchar(20) default NULL NULL,
  PRIMARY KEY ("noteId")
) ENGINE=MyISAM  
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_user_postings
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 13, 2003 at 01:12 AM
--

-- DROP TABLE "tiki_user_postings"
go


CREATE TABLE "tiki_user_postings" (
  "user" varchar(200) default '' NOT NULL,
  "posts" numeric(12,0) default NULL NULL,
  "last" numeric(14,0) default NULL NULL,
  "first" numeric(14,0) default NULL NULL,
  "level" numeric(8,0) default NULL NULL,
  PRIMARY KEY ("user")
) ENGINE=MyISAM
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_user_preferences
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 13, 2003 at 01:09 AM
--

-- DROP TABLE "tiki_user_preferences"
go


CREATE TABLE "tiki_user_preferences" (
  "user" varchar(200) default '' NOT NULL,
  "prefName" varchar(40) default '' NOT NULL,
  "value" varchar(250) default NULL NULL,
  PRIMARY KEY ("user","prefName")
) ENGINE=MyISAM
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_user_quizzes
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_user_quizzes"
go


CREATE TABLE "tiki_user_quizzes" (
  "user" varchar(200) default '',
  "quizId" numeric(10,0) default NULL NULL,
  "timestamp" numeric(14,0) default NULL NULL,
  "timeTaken" numeric(14,0) default NULL NULL,
  "points" numeric(12,0) default NULL NULL,
  "maxPoints" numeric(12,0) default NULL NULL,
  "resultId" numeric(10,0) default NULL NULL,
  "userResultId" numeric(10 ,0) identity,
  PRIMARY KEY ("userResultId")
) ENGINE=MyISAM  
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_user_taken_quizzes
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_user_taken_quizzes"
go


CREATE TABLE "tiki_user_taken_quizzes" (
  "user" varchar(200) default '' NOT NULL,
  "quizId" varchar(255) default '' NOT NULL,
  PRIMARY KEY ("user","quizId")
) ENGINE=MyISAM
go


-- --------------------------------------------------------


--
-- Table structure for table tiki_user_tasks_history
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jan 25, 2005 by sir-b & moresun
--
-- DROP TABLE "tiki_user_tasks_history"
go


CREATE TABLE "tiki_user_tasks_history" (
  "belongs_to" integer(14) NOT NULL,                   -- the fist task in a history it has the same id as the task id
  "task_version" integer(4) DEFAULT 0 NOT NULL,        -- version number for the history it starts with 0
  "title" varchar(250) NOT NULL,                       -- title
  "description" text DEFAULT NULL NULL,                     -- description
  "start" integer(14) DEFAULT NULL NULL,                    -- date of the starting, if it is not set than there is not starting date
  "end" integer(14) DEFAULT NULL NULL,                      -- date of the end, if it is not set than there is not dealine
  "lasteditor" varchar(200) NOT NULL,                  -- lasteditor: username of last editior
  "lastchanges" integer(14) NOT NULL,                  -- date of last changes
  "priority" integer(2) DEFAULT 3 NOT NULL,                     -- priority
  "completed" integer(14) DEFAULT NULL NULL,                -- date of the completation if it is null it is not yet completed
  "deleted" integer(14) DEFAULT NULL NULL,                  -- date of the deleteation it it is null it is not deleted
  "status" char(1) DEFAULT NULL NULL,                       -- null := waiting, 
                                                     -- o := open / in progress, 
                                                     -- c := completed -> (percentage = 100) 
  "percentage" numeric(4,0) DEFAULT NULL NULL,
  "accepted_creator" char(1) DEFAULT NULL NULL,             -- y - yes, n - no, null - waiting
  "accepted_user" char(1) DEFAULT NULL NULL,                -- y - yes, n - no, null - waiting
  PRIMARY KEY (belongs_to, task_version)
) ENGINE=MyISAM  
go




--
-- Table structure for table tiki_user_tasks
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jan 25, 2005 by sir-b & moresun
--
-- DROP TABLE "tiki_user_tasks"
go


CREATE TABLE "tiki_user_tasks" (
  "taskId" integer(14) NOT NULL auto_increment,        -- task id
  "last_version" integer(4) DEFAULT 0 NOT NULL,        -- last version of the task starting with 0
  "user" varchar(200) DEFAULT '' NOT NULL,              -- task user
  "creator" varchar(200) NOT NULL,                     -- username of creator
  "public_for_group" varchar(30) DEFAULT NULL NULL,         -- this group can also view the task, if it is null it is not public
  "rights_by_creator" char(1) DEFAULT NULL NULL,            -- null the user can delete the task, 
  "created" integer(14) NOT NULL,                      -- date of the creation
  "status" char(1) default NULL NULL,
  "priority" numeric(2,0) default NULL NULL,
  "completed" numeric(14,0) default NULL NULL,
  "percentage" numeric(4,0) default NULL NULL,
  PRIMARY KEY (taskId),
  UNIQUE(creator, created)
) ENGINE=MyISAM 
go



-- --------------------------------------------------------

--
-- Table structure for table tiki_user_votings
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 11, 2003 at 11:55 PM
--

-- DROP TABLE "tiki_user_votings"
go


CREATE TABLE "tiki_user_votings" (
  "user" varchar(200) default '' NOT NULL,
  "id" varchar(255) default '' NOT NULL,
  "optionId" numeric(10,0) default 0 NOT NULL,
  PRIMARY KEY ("`user`","id")
) ENGINE=MyISAM
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_user_watches
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 08:07 AM
--

-- DROP TABLE "tiki_user_watches"
go


CREATE TABLE "tiki_user_watches" (
  "watchId" numeric(12 ,0) identity,
  "user" varchar(200) default '' NOT NULL,
  "event" varchar(40) default '' NOT NULL,
  "object" varchar(200) default '' NOT NULL,
  "title" varchar(250) default NULL NULL,
  "type" varchar(200) default NULL NULL,
  "url" varchar(250) default NULL NULL,
  "email" varchar(200) default NULL NULL,
  PRIMARY KEY ("`user`","event","object","email")
) ENGINE=MyISAM
go


CREATE  INDEX "tiki_user_watches_watchId" ON "tiki_user_watches"("watchId")
go
-- --------------------------------------------------------

--
-- Table structure for table tiki_userfiles
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_userfiles"
go


CREATE TABLE "tiki_userfiles" (
  "user" varchar(200) default '' NOT NULL,
  "fileId" numeric(12 ,0) identity,
  "name" varchar(200) default NULL NULL,
  "filename" varchar(200) default NULL NULL,
  "filetype" varchar(200) default NULL NULL,
  "filesize" varchar(200) default NULL NULL,
  "data" image default '',
  "hits" numeric(8,0) default NULL NULL,
  "isFile" char(1) default NULL NULL,
  "path" varchar(255) default NULL NULL,
  "created" numeric(14,0) default NULL NULL,
  PRIMARY KEY ("fileId")
) ENGINE=MyISAM  
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_userpoints
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 11, 2003 at 05:47 AM
--

-- DROP TABLE "tiki_userpoints"
go


CREATE TABLE "tiki_userpoints" (
  "user" varchar(200) default NULL NULL,
  "points" decimal(8,2) default NULL NULL,
  "voted" numeric(8,0) default NULL
) ENGINE=MyISAM
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_users
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_users"
go


CREATE TABLE "tiki_users" (
  "user" varchar(200) default '' NOT NULL,
  "password" varchar(40) default NULL NULL,
  "email" varchar(200) default NULL NULL,
  "lastLogin" numeric(14,0) default NULL NULL,
  PRIMARY KEY ("user")
) ENGINE=MyISAM
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_webmail_contacts
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_webmail_contacts"
go


CREATE TABLE "tiki_webmail_contacts" (
  "contactId" numeric(12 ,0) identity,
  "firstName" varchar(80) default NULL NULL,
  "lastName" varchar(80) default NULL NULL,
  "email" varchar(250) default NULL NULL,
  "nickname" varchar(200) default NULL NULL,
  "user" varchar(200) default '' NOT NULL,
  PRIMARY KEY ("contactId")
) ENGINE=MyISAM  
go


-- --------------------------------------------------------

-- DROP TABLE "tiki_webmail_contacts_groups"
go


CREATE TABLE "tiki_webmail_contacts_groups" (
  "contactId" numeric(12,0) NOT NULL,
  "groupName" varchar(255) NOT NULL,
  PRIMARY KEY ("contactId","groupName")
) ENGINE=MyISAM 
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_webmail_messages
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_webmail_messages"
go


CREATE TABLE "tiki_webmail_messages" (
  "accountId" numeric(12,0) default '0' NOT NULL,
  "mailId" varchar(255) default '' NOT NULL,
  "user" varchar(200) default '' NOT NULL,
  "isRead" char(1) default NULL NULL,
  "isReplied" char(1) default NULL NULL,
  "isFlagged" char(1) default NULL NULL,
  PRIMARY KEY ("accountId","mailId")
) ENGINE=MyISAM
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_wiki_attachments
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_wiki_attachments"
go


CREATE TABLE "tiki_wiki_attachments" (
  "attId" numeric(12 ,0) identity,
  "page" varchar(200) default '' NOT NULL,
  "filename" varchar(80) default NULL NULL,
  "filetype" varchar(80) default NULL NULL,
  "filesize" numeric(14,0) default NULL NULL,
  "user" varchar(200) default NULL NULL,
  "data" image default '',
  "path" varchar(255) default NULL NULL,
  "hits" numeric(10,0) default NULL NULL,
  "created" numeric(14,0) default NULL NULL,
  "comment" varchar(250) default NULL NULL,
  PRIMARY KEY ("attId")
) ENGINE=MyISAM  
go


-- --------------------------------------------------------

--
-- Table structure for table tiki_zones
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

-- DROP TABLE "tiki_zones"
go


CREATE TABLE "tiki_zones" (
  "zone" varchar(40) default '' NOT NULL,
  PRIMARY KEY ("zone")
) ENGINE=MyISAM
go


-- --------------------------------------------------------
--
-- Table structure for table tiki_download
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Apr 15 2004 at 07:42 PM
--

-- DROP TABLE "tiki_download"
go


CREATE TABLE "tiki_download" (
  "id" numeric(11 ,0) identity,
  "object" varchar(255) default '' NOT NULL,
  "userId" numeric(8,0) default '0' NOT NULL,
  "type" varchar(20) default '' NOT NULL,
  "date" numeric(14,0) default '0' NOT NULL,
  "IP" varchar(50) default '' NOT NULL,
  PRIMARY KEY ("id")
) ENGINE=MyISAM
go


CREATE  INDEX "tiki_download_object" ON "tiki_download"("object","userId","type")
go
CREATE  INDEX "tiki_download_userId" ON "tiki_download"("userId")
go
CREATE  INDEX "tiki_download_type" ON "tiki_download"("type")
go
CREATE  INDEX "tiki_download_date" ON "tiki_download"("date")
go
-- --------------------------------------------------------

--
-- Table structure for table users_grouppermissions
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 11, 2003 at 07:22 AM
--

-- DROP TABLE "users_grouppermissions"
go


CREATE TABLE "users_grouppermissions" (
  "groupName" varchar(255) default '' NOT NULL,
  "permName" varchar(40) default '' NOT NULL,
  "value" char(1) default '',
  PRIMARY KEY ("groupName","permName")
) ENGINE=MyISAM
go


-- --------------------------------------------------------

INSERT INTO "users_grouppermissions" ("groupName","permName") VALUES ('Anonymous','tiki_p_view')
go



--
-- Table structure for table users_groups
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 08:57 PM
--

-- DROP TABLE "users_groups"
go


CREATE TABLE "users_groups" (
  "groupName" varchar(255) default '' NOT NULL,
  "groupDesc" varchar(255) default NULL NULL,
  "groupHome" varchar(255) default '',
  "usersTrackerId" numeric(11,0) default NULL NULL,
  "groupTrackerId" numeric(11,0) default NULL NULL,
  "usersFieldId" numeric(11,0) default NULL NULL,
  "groupFieldId" numeric(11,0) default NULL NULL,
  "registrationChoice" char(1) default NULL NULL,
  "registrationUsersFieldIds" text default '',
  "userChoice" char(1) default NULL NULL,
  "groupDefCat" numeric(12,0) default 0,
  "groupTheme" varchar(255) default '',  
  PRIMARY KEY ("groupName")
) ENGINE=MyISAM
go


-- --------------------------------------------------------

--
-- Table structure for table users_objectpermissions
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 07:20 AM
--

-- DROP TABLE "users_objectpermissions"
go


CREATE TABLE "users_objectpermissions" (
  "groupName" varchar(255) default '' NOT NULL,
  "permName" varchar(40) default '' NOT NULL,
  "objectType" varchar(20) default '' NOT NULL,
  "objectId" varchar(32) default '' NOT NULL,
  PRIMARY KEY ("objectId","objectType","groupName","permName")
) ENGINE=MyISAM
go


-- --------------------------------------------------------

--
-- Table structure for table users_permissions
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 11, 2003 at 07:22 AM
--

-- DROP TABLE "users_permissions"
go


CREATE TABLE "users_permissions" (
  "permName" varchar(40) default '' NOT NULL,
  "permDesc" varchar(250) default NULL NULL,
  "level" varchar(80) default NULL NULL,
  "type" varchar(20) default NULL NULL,
  "admin" varchar(1) default NULL NULL,
  PRIMARY KEY ("permName")
) ENGINE=MyISAM
go


CREATE  INDEX "users_permissions_type" ON "users_permissions"("type")
go
-- --------------------------------------------------------
-- 

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_abort_instance', 'Can abort a process instance', 'editors', 'workflow')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_access_closed_site', 'Can access site when closed', 'admin', 'tiki')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_add_events', 'Can add events in the calendar', 'registered', 'calendar')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type","admin") VALUES ('tiki_p_admin', 'Administrator, can manage users groups and permissions, Hotwords and all the weblog features', 'admin', 'tiki', 'y')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_admin_banners', 'Administrator, can admin banners', 'admin', 'tiki')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_admin_banning', 'Can ban users or ips', 'admin', 'tiki')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type","admin") VALUES ('tiki_p_admin_calendar', 'Can create/admin calendars', 'admin', 'calendar', 'y')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type","admin") VALUES ('tiki_p_admin_categories', 'Can admin categories', 'editors', 'category', 'y')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type","admin") VALUES ('tiki_p_admin_charts', 'Can admin charts', 'admin', 'charts', 'y')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type","admin") VALUES ('tiki_p_admin_chat', 'Administrator, can create channels remove channels etc', 'editors', 'chat', 'y')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type","admin") VALUES ('tiki_p_admin_cms', 'Can admin the cms', 'editors', 'cms', 'y')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type","admin") VALUES ('tiki_p_admin_contribution', 'Can admin contributions', 'admin', 'contribution', 'y')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type","admin") VALUES ('tiki_p_admin_directory', 'Can admin the directory', 'editors', 'directory', 'y')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_admin_directory_cats', 'Can admin directory categories', 'editors', 'directory')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_admin_directory_sites', 'Can admin directory sites', 'editors', 'directory')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type","admin") VALUES ('tiki_p_admin_drawings', 'Can admin drawings', 'editors', 'drawings', 'y')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_admin_dynamic', 'Can admin the dynamic content system', 'editors', 'tiki')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type","admin") VALUES ('tiki_p_admin_faqs', 'Can admin faqs', 'editors', 'faqs', 'y')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type","admin") VALUES ('tiki_p_admin_file_galleries', 'Can admin file galleries', 'editors', 'file galleries', 'y')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type","admin") VALUES ('tiki_p_admin_forum', 'Can admin forums', 'editors', 'forums', 'y')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type","admin") VALUES ('tiki_p_admin_galleries', 'Can admin Image Galleries', 'editors', 'image galleries', 'y')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type","admin") VALUES ('tiki_p_admin_games', 'Can admin games', 'editors', 'games', 'y')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_admin_integrator', 'Can admin integrator repositories and rules', 'admin', 'tiki')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_admin_mailin', 'Can admin mail-in accounts', 'admin', 'tiki')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type","admin") VALUES ('tiki_p_admin_newsletters', 'Can admin newsletters', 'admin', 'newsletters', 'y')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_admin_objects','Can edit object permissions', 'admin', 'tiki')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type","admin") VALUES ('tiki_p_admin_polls','Can admin polls', 'admin', 'polls', 'y')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type","admin") VALUES ('tiki_p_admin_quizzes', 'Can admin quizzes', 'editors', 'quizzes', 'y')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_admin_received_articles', 'Can admin received articles', 'editors', 'comm')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_admin_received_pages', 'Can admin received pages', 'editors', 'comm')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_admin_rssmodules','Can admin rss modules', 'admin', 'tiki')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type","admin") VALUES ('tiki_p_admin_sheet', 'Can admin sheet', 'admin', 'sheet', 'y')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type","admin") VALUES ('tiki_p_admin_shoutbox', 'Can admin shoutbox (Edit/remove msgs)', 'editors', 'shoutbox', 'y')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type","admin") VALUES ('tiki_p_admin_surveys', 'Can admin surveys', 'editors', 'surveys', 'y')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type","admin") VALUES ('tiki_p_admin_trackers', 'Can admin trackers', 'editors', 'trackers', 'y')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_admin_users', 'Can admin users', 'admin', 'user')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type","admin") VALUES ('tiki_p_admin_wiki', 'Can admin the wiki', 'editors', 'wiki', 'y')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type","admin") VALUES ('tiki_p_admin_workflow', 'Can admin workflow processes', 'admin', 'workflow', 'y')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type","admin") VALUES ('tiki_p_admin_quicktags', 'Can admin quicktags', 'admin', 'quicktags', 'y')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type","admin") VALUES ('tiki_p_admin_freetags', 'Can admin freetags', 'admin', 'freetags', 'y')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_approve_submission', 'Can approve submissions', 'editors', 'cms')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_attach_trackers', 'Can attach files to tracker items', 'registered', 'trackers')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_autoapprove_submission', 'Submited articles automatically approved', 'editors', 'cms')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_autosubmit_link', 'Submited links are valid', 'editors', 'directory')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_autoval_chart_suggestio', 'Autovalidate suggestions', 'editors', 'charts')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_batch_subscribe_email', 'Can subscribe many e-mails at once (requires tiki_p_subscribe email)', 'editors', 'newsletters')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_batch_upload_files', 'Can upload zip files with files', 'editors', 'file galleries')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_batch_upload_file_dir', 'Can use Directory Batch Load', 'editors', 'file galleries')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_batch_upload_image_dir', 'Can use Directory Batch Load', 'editors', 'image galleries')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_batch_upload_images', 'Can upload zip files with images', 'editors', 'image galleries')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type","admin") VALUES ('tiki_p_blog_admin', 'Can admin blogs', 'editors', 'blogs', 'y')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_blog_post', 'Can post to a blog', 'registered', 'blogs')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_broadcast', 'Can broadcast messages to groups', 'admin', 'messu')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_broadcast_all', 'Can broadcast messages to all user', 'admin', 'messu')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_cache_bookmarks', 'Can cache user bookmarks', 'admin', 'user')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_change_events', 'Can change events in the calendar', 'registered', 'calendar')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_chat', 'Can use the chat system', 'registered', 'chat')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_comment_tracker_items', 'Can insert comments for tracker items', 'basic', 'trackers')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_configure_modules', 'Can configure modules', 'registered', 'user')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_create_blogs', 'Can create a blog', 'editors', 'blogs')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_create_bookmarks', 'Can create user bookmarks', 'registered', 'user')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_create_css', 'Can create new css suffixed with -user', 'registered', 'tiki')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_create_file_galleries', 'Can create file galleries', 'editors', 'file galleries')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_create_galleries', 'Can create image galleries', 'editors', 'image galleries')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_create_tracker_items', 'Can create new items for trackers', 'registered', 'trackers')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_download_files', 'Can download files', 'basic', 'file galleries')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_edit', 'Can edit pages', 'registered', 'wiki')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_edit_article', 'Can edit articles', 'editors', 'cms')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_edit_categorized', 'Can edit categorized items', 'registered', 'category')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_edit_categories', 'Can edit items in categories', 'registered', 'category')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_edit_comments', 'Can edit all comments', 'editors', 'comments')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_edit_content_templates', 'Can edit content templates', 'editors', 'content templates')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_edit_cookies', 'Can admin cookies', 'editors', 'tiki')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_edit_copyrights', 'Can edit copyright notices', 'editors', 'wiki')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_edit_drawings', 'Can edit drawings', 'basic', 'drawings')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_edit_dynvar', 'Can edit dynamic variables', 'editors', 'wiki')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_edit_gallery_file', 'Can edit a gallery file', 'editors', 'file galleries')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_edit_html_pages', 'Can edit HTML pages', 'editors', 'html pages')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_edit_languages', 'Can edit translations and create new languages', 'editors', 'tiki')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_edit_sheet', 'Can create and edit sheets', 'editors', 'sheet')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_edit_structures', 'Can create and edit structures', 'editors', 'wiki')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_edit_submission', 'Can edit submissions', 'editors', 'cms')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_edit_templates', 'Can edit site templates', 'admin', 'tiki')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_exception_instance', 'Can declare an instance as exception', 'registered', 'workflow')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_forum_edit_own_posts', 'Can edit own forum posts', 'registered', 'forums')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_forum_attach', 'Can attach to forum posts', 'registered', 'forums')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_forum_autoapp', 'Auto approve forum posts', 'editors', 'forums')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_forum_post', 'Can post in forums', 'registered', 'forums')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_forum_post_topic', 'Can start threads in forums', 'registered', 'forums')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_forum_read', 'Can read forums', 'basic', 'forums')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_forum_vote', 'Can vote comments in forums', 'registered', 'forums')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_forums_report', 'Can report msgs to moderator', 'registered', 'forums')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_freetags_tag', 'Can tag objects', 'registered', 'freetags')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_list_users', 'Can list registered users', 'registered', 'community')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_live_support', 'Can use live support system', 'basic', 'support')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_live_support_admin', 'Admin live support system', 'admin', 'support')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_lock', 'Can lock pages', 'editors', 'wiki')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_map_create', 'Can create new mapfile', 'admin', 'maps')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_map_delete', 'Can delete mapfiles', 'admin', 'maps')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_map_edit', 'Can edit mapfiles', 'editors', 'maps')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_map_view', 'Can view mapfiles', 'basic', 'maps')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_map_view_mapfiles', 'Can view contents of mapfiles', 'registered', 'maps')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_messages', 'Can use the messaging system', 'registered', 'messu')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_minical', 'Can use the mini event calendar', 'registered', 'user')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_minor', 'Can save as minor edit', 'registered', 'wiki')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_modify_tracker_items', 'Can change tracker items', 'registered', 'trackers')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_newsreader', 'Can use the newsreader', 'registered', 'user')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_notepad', 'Can use the notepad', 'registered', 'user')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_play_games', 'Can play games', 'basic', 'games')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_post_comments', 'Can post new comments', 'registered', 'comments')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_post_shoutbox', 'Can post messages in shoutbox', 'basic', 'shoutbox')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_read_article', 'Can read articles', 'basic', 'cms')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_read_blog', 'Can read blogs', 'basic', 'blogs')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_read_comments', 'Can read comments', 'basic', 'comments')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_remove', 'Can remove', 'editors', 'wiki')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_remove_article', 'Can remove articles', 'editors', 'cms')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_remove_comments', 'Can delete comments', 'editors', 'comments')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_remove_submission', 'Can remove submissions', 'editors', 'cms')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_rename', 'Can rename pages', 'editors', 'wiki')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_rollback', 'Can rollback pages', 'editors', 'wiki')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_send_articles', 'Can send articles to other sites', 'editors', 'comm')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_send_instance', 'Can send instances after completion', 'registered', 'workflow')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_send_newsletters', 'Can send newsletters', 'editors', 'newsletters')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_send_pages', 'Can send pages to other sites', 'registered', 'comm')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_sendme_articles', 'Can send articles to this site', 'registered', 'comm')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_sendme_pages', 'Can send pages to this site', 'registered', 'comm')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_submit_article', 'Can submit articles', 'basic', 'cms')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_submit_link', 'Can submit sites to the directory', 'basic', 'directory')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_subscribe_email', 'Can subscribe any email to newsletters', 'editors', 'newsletters')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_subscribe_newsletters', 'Can subscribe to newsletters', 'basic', 'newsletters')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_suggest_chart_item', 'Can suggest items', 'basic', 'charts')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_suggest_faq', 'Can suggest faq questions', 'basic', 'faqs')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_take_quiz', 'Can take quizzes', 'basic', 'quizzes')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_take_survey', 'Can take surveys', 'basic', 'surveys')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_tasks', 'Can use tasks', 'registered', 'user')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_tasks_admin', 'Can admin public tasks', 'admin', 'user')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_tasks_receive', 'Can receive tasks from other users', 'registered', 'user')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_tasks_send', 'Can send tasks to other users', 'registered', 'user')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_topic_read', 'Can read a topic (Applies only to individual topic perms)', 'basic', 'cms')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_tracker_view_ratings', 'Can view rating result for tracker items', 'basic', 'trackers')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_tracker_vote_ratings', 'Can vote a rating for tracker items', 'registered', 'trackers')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_upload_files', 'Can upload files', 'registered', 'file galleries')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_upload_images', 'Can upload images', 'registered', 'image galleries')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_upload_picture', 'Can upload pictures to wiki pages', 'registered', 'wiki')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_use_HTML', 'Can use HTML in pages', 'editors', 'tiki')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_use_content_templates', 'Can use content templates', 'registered', 'content templates')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_use_webmail', 'Can use webmail', 'registered', 'webmail')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_use_workflow', 'Can execute workflow activities', 'registered', 'workflow')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_userfiles', 'Can upload personal files', 'registered', 'user')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_usermenu', 'Can create items in personal menu', 'registered', 'user')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_validate_links', 'Can validate submited links', 'editors', 'directory')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view', 'Can view page/pages', 'basic', 'wiki')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_calendar', 'Can browse the calendar', 'basic', 'calendar')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_categories', 'Can view categories', 'basic', 'category')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_categorized', 'Can view categorized items', 'basic', 'category')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_chart', 'Can view charts', 'basic', 'charts')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_directory', 'Can use the directory', 'basic', 'directory')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_events', 'Can view events details', 'registered', 'calendar')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_faqs', 'Can view faqs', 'basic', 'faqs')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_file_gallery', 'Can view file galleries', 'basic', 'file galleries')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_freetags', 'Can browse freetags', 'basic', 'freetags')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_html_pages', 'Can view HTML pages', 'basic', 'html pages')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_image_gallery', 'Can view image galleries', 'basic', 'image galleries')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_list_image_galleries', 'Can list image galleries', 'basic', 'image galleries')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_integrator', 'Can view integrated repositories', 'basic', 'tiki')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_quiz_stats', 'Can view quiz stats', 'basic', 'quizzes')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_referer_stats', 'Can view referer stats', 'editors', 'tiki')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_sheet', 'Can view sheet', 'basic', 'sheet')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_sheet_history', 'Can view sheet history', 'admin', 'sheet')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_shoutbox', 'Can view shoutbox', 'basic', 'shoutbox')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_stats', 'Can view site stats', 'basic', 'tiki')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_survey_stats', 'Can view survey stats', 'basic', 'surveys')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_templates', 'Can view site templates', 'admin', 'tiki')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_tiki_calendar', 'Can view Tikiwiki tools calendar', 'basic', 'calendar')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_trackers', 'Can view trackers', 'basic', 'trackers')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_list_trackers', 'Can list trackers', 'basic', 'trackers')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_trackers_closed', 'Can view trackers closed items', 'registered', 'trackers')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_trackers_pending', 'Can view trackers pending items', 'editors', 'trackers')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_user_results', 'Can view user quiz results', 'editors', 'quizzes')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_vote_chart', 'Can vote', 'basic', 'charts')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_vote_comments', 'Can vote comments', 'registered', 'comments')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_vote_poll', 'Can vote polls', 'basic', 'polls')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_poll_results', 'Can view poll results', 'basic', 'polls')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_watch_trackers', 'Can watch tracker', 'registered', 'trackers')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_wiki_admin_attachments', 'Can admin attachments to wiki pages', 'editors', 'wiki')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_wiki_admin_ratings', 'Can add and change ratings on wiki pages', 'admin', 'wiki')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_wiki_attach_files', 'Can attach files to wiki pages', 'registered', 'wiki')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_wiki_view_attachments', 'Can view wiki attachments and download', 'registered', 'wiki')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_wiki_view_comments', 'Can view wiki comments', 'basic', 'wiki')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_wiki_view_history', 'Can view wiki history', 'basic', 'wiki')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_wiki_view_ratings', 'Can view rating of wiki pages', 'basic', 'wiki')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_wiki_view_source', 'Can view source of wiki pages', 'basic', 'wiki')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_wiki_vote_ratings', 'Can participate to rating of wiki pages', 'registered', 'wiki')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_assign_perm_file_gallery', 'Can assign perms to file gallery', 'admin', 'file galleries')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_actionlog', 'Can view action log', 'registered', 'tiki')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_actionlog_owngroups for users of his own groups', 'Can view action log', 'registered', 'tiki')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_assign_perm_blog', 'Can assign perms to blog', 'admin', 'blogs')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_tell_a_friend', 'Can send a link to a friend', 'Basic', 'tiki')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_list_file_galleries', 'Can list file galleries', 'basic', 'file galleries')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_assign_perm_wiki_page', 'Can assign perms to wiki pages', 'admin', 'wiki')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_mypage', 'Can view any mypage', 'basic', 'mypage')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_edit_own_mypage', 'Can view/edit only one\'s own mypages', 'registered', 'mypage')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_edit_mypage', 'Can edit any mypage', 'registered', 'mypage')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type","admin") VALUES ('tiki_p_admin_mypage', 'Can admin any mypage', 'admin', 'mypage','y')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_list_mypage', 'Can list mypages', 'registered', 'mypage')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_assign_perm_mypage', 'Can assign perms to mypage', 'admin', 'mypage')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_watch_structure', 'Can watch structure', 'registered', 'wiki')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_edit_menu', 'Can edit menu', 'admin', 'tiki')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_edit_menu_option', 'Can edit menu option', 'admin', 'tiki')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_subscribe_groups', 'Can subscribe to groups', 'registered', 'tiki')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_detach_translation', 'Can remove association between two pages in a translation set', 'registered', 'tiki')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_unassign_freetags', 'Can unassign tags from an object', 'basic', 'freetags')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_search', 'Can search', 'basic', 'tiki')
go


INSERT INTO users_permissions (permName, permDesc, level, type) VALUES('tiki_p_clean_cache', 'Can clean cache', 'editors', 'tiki')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_fgal_explorer', 'Can view file galleries explorer', 'basic', 'file galleries')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_fgal_path', 'Can view file galleries path', 'basic', 'file galleries')
go


INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_site_report', 'Can report a link to the webmaster', 'basic', 'tiki')
go

INSERT into users_permissions (permName,permDesc,level,type,admin) VALUES ('tiki_p_assign_perm_image_gallery','Can assign perms to image gallery','admin','image galleries',NULL);
go

-- --------------------------------------------------------

--
-- Table structure for table users_usergroups
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 09:31 PM
--

-- DROP TABLE "users_usergroups"
go


CREATE TABLE "users_usergroups" (
  "userId" numeric(8,0) default '0' NOT NULL,
  "groupName" varchar(255) default '' NOT NULL,
  PRIMARY KEY ("userId","groupName")
) ENGINE=MyISAM
go


-- --------------------------------------------------------
INSERT INTO "users_groups" ("groupName","groupDesc") VALUES ('Anonymous','Public users not logged')
go


INSERT INTO "users_groups" ("groupName","groupDesc") VALUES ('Registered','Users logged into the system')
go


INSERT INTO "users_groups" ("groupName","groupDesc") VALUES ('Admins','Administrator and accounts managers.')
go


-- --------------------------------------------------------

--
-- Table structure for table users_users
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 13, 2003 at 01:07 AM
--

-- DROP TABLE "users_users"
go


CREATE TABLE "users_users" (
  "userId" numeric(8 ,0) identity,
  "email" varchar(200) default NULL NULL,
  "login" varchar(200) default '' NOT NULL,
  "password" varchar(30) default '',
  "provpass" varchar(30) default NULL NULL,
  "default_group" varchar(255) default '',
  "lastLogin" numeric(14,0) default NULL NULL,
  "currentLogin" numeric(14,0) default NULL NULL,
  "registrationDate" numeric(14,0) default NULL NULL,
  "challenge" varchar(32) default NULL NULL,
  "pass_confirm" numeric(14,0) default NULL NULL,
  "email_confirm" numeric(14,0) default NULL NULL,
  "hash" varchar(34) default NULL NULL,
  "created" numeric(14,0) default NULL NULL,
  "avatarName" varchar(80) default NULL NULL,
  "avatarSize" numeric(14,0) default NULL NULL,
  "avatarFileType" varchar(250) default NULL NULL,
  "avatarData" image default '',
  "avatarLibName" varchar(200) default NULL NULL,
  "avatarType" char(1) default NULL NULL,
  "score" numeric(11,0) default 0 NOT NULL,
  "valid" varchar(32) default NULL NULL,
  "unsuccessful_logins" numeric(14,0) default 0,
  "openid_url" varchar(255) default NULL NULL,
  "waiting" char(1) default NULL NULL,
  PRIMARY KEY ("userId")
) ENGINE=MyISAM  
go


CREATE  INDEX "users_users_login" ON "users_users"("login")
go
CREATE  INDEX "users_users_score" ON "users_users"("score")
go
CREATE  INDEX "users_users_registrationDate" ON "users_users"("registrationDate")
go
CREATE  INDEX "users_users_openid_url" ON "users_users"("openid_url")
go
-- --------------------------------------------------------
------ Administrator account
INSERT INTO "users_users" ("email","login","password","hash") VALUES ('','admin','admin','f6fdffe48c908deb0f4c3bd36c032e72')
go


UPDATE "users_users" SET "currentLogin"="lastLogin" "registrationDate"="lastLogin"
go


INSERT INTO "tiki_user_preferences" ("user","prefName","value") VALUES ('admin','realName','System Administrator')
go


INSERT INTO users_usergroups (userId, groupName) VALUES(1,'Admins')
go


INSERT INTO "users_grouppermissions" ("groupName","permName") VALUES ('Admins','tiki_p_admin')
go


-- --------------------------------------------------------
-- 

--
-- Table structure for table 'tiki_integrator_reps'
--
-- DROP TABLE "tiki_integrator_reps"
go


CREATE TABLE "tiki_integrator_reps" (
  "repID" numeric(11 ,0) identity,
  "name" varchar(255) default '' NOT NULL,
  "path" varchar(255) default '' NOT NULL,
  "start_page" varchar(255) default '' NOT NULL,
  "css_file" varchar(255) default '' NOT NULL,
  "visibility" char(1) default 'y' NOT NULL,
  "cacheable" char(1) default 'y' NOT NULL,
  "expiration" numeric(11,0) default '0' NOT NULL,
  "description" text NOT NULL,
  PRIMARY KEY ("repID")
) ENGINE=MyISAM
go



--
-- Dumping data for table 'tiki_integrator_reps'
--
INSERT INTO tiki_integrator_reps VALUES ('1','Doxygened (1.3.4) Documentation','','index.html','doxygen.css','n','y','0','Use this repository as rule source for all your repositories based on doxygened docs. To setup yours just add new repository and copy rules from this repository :)')
go



--
-- Table structure for table 'tiki_integrator_rules'
--
-- DROP TABLE "tiki_integrator_rules"
go


CREATE TABLE "tiki_integrator_rules" (
  "ruleID" numeric(11 ,0) identity,
  "repID" numeric(11,0) default '0' NOT NULL,
  "ord" numeric(2,0) default '0' NOT NULL,
  "srch" image NOT NULL,
  "repl" image NOT NULL,
  "type" char(1) default 'n' NOT NULL,
  "casesense" char(1) default 'y' NOT NULL,
  "rxmod" varchar(20) default '' NOT NULL,
  "enabled" char(1) default 'n' NOT NULL,
  "description" text NOT NULL,
  PRIMARY KEY ("ruleID")
) ENGINE=MyISAM
go


CREATE  INDEX "tiki_integrator_rules_repID" ON "tiki_integrator_rules"("repID")
go

--
-- Dumping data for table 'tiki_integrator_rules'
--
INSERT INTO tiki_integrator_rules VALUES ('1','1','1','.*<body[^>]*?>(.*?)</body.*','\1','y','n','i','y','Extract code between <body> and </body> tags')
go


INSERT INTO tiki_integrator_rules VALUES ('2','1','2','img src=(\"|\')(?!http://)','img src=\1{path}/','y','n','i','y','Fix image paths')
go


INSERT INTO tiki_integrator_rules VALUES ('3','1','3','href=(\"|\')(?!(--|(http|ftp)://))','href=\1tiki-integrator.php?repID={repID}&file=','y','n','i','y','Replace internal links to integrator. Don\'t touch an external link.')
go



--
-- Table structures for table 'tiki_quicktags'
-- 
-- DROP TABLE "tiki_quicktags"
go


CREATE TABLE "tiki_quicktags" (
  "tagId" numeric(4 ,0) identity,
  "taglabel" varchar(255) default NULL NULL,
  "taginsert" text default '',
  "tagicon" varchar(255) default NULL NULL,
  "tagcategory" varchar(255) default NULL NULL,
  PRIMARY KEY ("tagId")
) ENGINE=MyISAM  
go


CREATE  INDEX "tiki_quicktags_tagcategory" ON "tiki_quicktags"("tagcategory")
go
CREATE  INDEX "tiki_quicktags_taglabel" ON "tiki_quicktags"("taglabel")
go

-- wiki
INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('bold','__text__','pics/icons/text_bold.png','wiki')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('italic','\'\'text\'\'','pics/icons/text_italic.png','wiki')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('underline','===text===','pics/icons/text_underline.png','wiki')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('table new','||r1c1|r1c2\nr2c1|r2c2||','pics/icons/table.png','wiki')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('external link','[http://example.com|text]','pics/icons/world_link.png','wiki')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('wiki link','((text))','pics/icons/page_link.png','wiki')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('heading1','!text','pics/icons/text_heading_1.png','wiki')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('heading2','!!text','pics/icons/text_heading_2.png','wiki')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('heading3','!!!text','pics/icons/text_heading_3.png','wiki')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('title bar','-=text=-','pics/icons/text_padding_top.png','wiki')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('box','^text^','pics/icons/box.png','wiki')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('dynamic content','{content id= }','pics/icons/database_refresh.png','wiki')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('hr','---','pics/icons/page.png','wiki')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('center text','::text::','pics/icons/text_align_center.png','wiki')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('colored text','~~--FF0000:text~~','pics/icons/palette.png','wiki')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('dynamic variable','%text%','pics/icons/book_open.png','wiki')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('image','{img src= width= height= align= desc= link= }','pics/icons/picture.png','wiki')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('list bullets', '*text', 'pics/icons/text_list_bullets.png', 'wiki')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('list numbers', '--text', 'pics/icons/text_list_numbers.png', 'wiki')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('Email Address','[mailto:text|text]','pics/icons/email.png','wiki')
go



-- maps
INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('New wms Metadata','METADATA\r\n		\"wms_name\" \"myname\"\r\n 	"wms_srs" "EPSG:4326"\r\n 	"wms_server_version" " "\r\n 	"wms_layers" "mylayers"\r\n 	"wms_request" "myrequest"\r\n 	"wms_format" " "\r\n 	"wms_time" " "\r\n END', 'pics/icons/tag_blue_add.png','maps')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('New Class', 'CLASS\r\n EXPRESSION ()\r\n SYMBOL 0\r\n OUTLINECOLOR\r\n COLOR\r\n NAME "myclass" \r\nEND --end of class', 'pics/icons/application_add.png','maps')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('New Projection','PROJECTION\r\n "init=epsg:4326"\r\nEND','pics/icons/image_add.png','maps')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('New Query','--\r\n-- Start of query definitions\r\n--\r\n QUERYMAP\r\n STATUS ON\r\n STYLE HILITE\r\nEND','pics/icons/database_gear.png','maps')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('New Scalebar','--\r\n-- Start of scalebar\r\n--\r\nSCALEBAR\r\n IMAGECOLOR 255 255 255\r\n STYLE 1\r\n SIZE 400 2\r\n COLOR 0 0 0\r\n UNITS KILOMETERS\r\n INTERVALS 5\r\n STATUS ON\r\nEND','pics/icons/layout_add.png','maps')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('New Layer','LAYER\r\n NAME\r\n TYPE\r\n STATUS ON\r\n DATA "mydata"\r\nEND --end of layer', 'pics/icons/layers.png', 'maps')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('New Label','LABEL\r\n COLOR\r\n ANGLE\r\n FONT arial\r\n TYPE TRUETYPE\r\n POSITION\r\n PARTIALS TRUE\r\n SIZE 6\r\n BUFFER 0\r\n OUTLINECOLOR \r\nEND --end of label', 'pics/icons/comment_add.png', 'maps')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('New Reference','--\r\n--start of reference\r\n--\r\n REFERENCE\r\n SIZE 120 60\r\n STATUS ON\r\n EXTENT -180 -90 182 88\r\n OUTLINECOLOR 255 0 0\r\n IMAGE "myimagedata"\r\n COLOR -1 -1 -1\r\nEND','pics/icons/picture_add.png','maps')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('New Legend','--\r\n--start of Legend\r\n--\r\n LEGEND\r\n KEYSIZE 18 12\r\n POSTLABELCACHE TRUE\r\n STATUS ON\r\nEND','pics/icons/note_add.png','maps')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('New Web','--\r\n-- Start of web interface definition\r\n--\r\nWEB\r\n TEMPLATE "myfile/url"\r\n MINSCALE 1000\r\n MAXSCALE 40000\r\n IMAGEPATH "myimagepath"\r\n IMAGEURL "mypath"\r\nEND', 'pics/icons/world_link.png', 'maps')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('New Outputformat','OUTPUTFORMAT\r\n NAME\r\n DRIVER " "\r\n MIMETYPE "myimagetype"\r\n IMAGEMODE RGB\r\n EXTENSION "png"\r\nEND','pics/icons/newspaper_go.png','maps')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('New Mapfile','--\r\n-- Start of mapfile\r\n--\r\nNAME MYMAPFLE\r\n STATUS ON\r\nSIZE \r\nEXTENT\r\nUNITS \r\nSHAPEPATH " "\r\nIMAGETYPE " "\r\nFONTSET " "\r\nIMAGECOLOR -1 -1 -1\r\n\r\n--remove this text and add objects here\r\n\r\nEND -- end of mapfile','pics/icons/world_add.png','maps')
go



-- newsletters
INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('bold','__text__','pics/icons/text_bold.png','newsletters')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('italic','\'\'text\'\'','pics/icons/text_italic.png','newsletters')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('underline','===text===','pics/icons/text_underline.png','newsletters')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('external link','[http://example.com|text|nocache]','pics/icons/world_link.png','newsletters')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('heading1','!text','pics/icons/text_heading_1.png','newsletters')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('heading2','!!text','pics/icons/text_heading_2.png','newsletters')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('heading3','!!!text','pics/icons/text_heading_3.png','newsletters')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('hr', '---', 'pics/icons/page.png', 'newsletters')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('center text','::text::','pics/icons/text_align_center.png','newsletters')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('colored text','~~--FF0000:text~~','pics/icons/palette.png','newsletters')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('image','{img src= width= height= align= desc= link= }','pics/icons/picture.png','newsletters')
go



-- trackers
INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('bold','__text__','pics/icons/text_bold.png','trackers')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('italic','\'\'text\'\'','pics/icons/text_italic.png','trackers')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('underline','===text===','pics/icons/text_underline.png','trackers')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('table new','||r1c1|r1c2\nr2c1|r2c2||','pics/icons/table.png','trackers')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('external link','[http://example.com|text]','pics/icons/world_link.png','trackers')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('wiki link','((text))','pics/icons/page_link.png','trackers')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('heading1','!text','pics/icons/text_heading_1.png','trackers')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('heading2','!!text','pics/icons/text_heading_2.png','trackers')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('heading3','!!!text','pics/icons/text_heading_3.png','trackers')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('title bar','-=text=-','pics/icons/text_padding_top.png','trackers')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('box','^text^','pics/icons/box.png','trackers')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('dynamic content','{content id= }','pics/icons/database_refresh.png','trackers')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('hr','---','pics/icons/page.png','trackers')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('center text','::text::','pics/icons/text_align_center.png','trackers')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('colored text','~~--FF0000:text~~','pics/icons/palette.png','trackers')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('dynamic variable','%text%','pics/icons/book_open.png','trackers')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('image','{img src= width= height= align= desc= link= }','pics/icons/picture.png','trackers')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('Email Address','[mailto:text|text]','pics/icons/email.png','trackers')
go



-- blogs
INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('bold','__text__','pics/icons/text_bold.png','blogs')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('italic','\'\'text\'\'','pics/icons/text_italic.png','blogs')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('underline','===text===','pics/icons/text_underline.png','blogs')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('table new','||r1c1|r1c2\nr2c1|r2c2||','pics/icons/table.png','blogs')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('external link','[http://example.com|text]','pics/icons/world_link.png','blogs')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('wiki link','((text))','pics/icons/page_link.png','blogs')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('heading1','!text','pics/icons/text_heading_1.png','blogs')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('heading2','!!text','pics/icons/text_heading_2.png','blogs')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('heading3','!!!text','pics/icons/text_heading_3.png','blogs')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('title bar','-=text=-','pics/icons/text_padding_top.png','blogs')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('box','^text^','pics/icons/box.png','blogs')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('dynamic content','{content id= }','pics/icons/database_refresh.png','blogs')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('hr','---','pics/icons/page.png','blogs')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('center text','::text::','pics/icons/text_align_center.png','blogs')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('colored text','~~--FF0000:text~~','pics/icons/palette.png','blogs')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('dynamic variable','%text%','pics/icons/book_open.png','blogs')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('image','{img src= width= height= align= desc= link= }','pics/icons/picture.png','blogs')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('Email Address','[mailto:text|text]','pics/icons/email.png','blogs')
go



-- calendar
INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('bold','__text__','pics/icons/text_bold.png','calendar')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('italic','\'\'text\'\'','pics/icons/text_italic.png','calendar')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('underline','===text===','pics/icons/text_underline.png','calendar')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('table new','||r1c1|r1c2\nr2c1|r2c2||','pics/icons/table.png','calendar')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('external link','[http://example.com|text]','pics/icons/world_link.png','calendar')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('wiki link','((text))','pics/icons/page_link.png','calendar')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('heading1','!text','pics/icons/text_heading_1.png','calendar')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('heading2','!!text','pics/icons/text_heading_2.png','calendar')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('heading3','!!!text','pics/icons/text_heading_3.png','calendar')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('title bar','-=text=-','pics/icons/text_padding_top.png','calendar')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('box','^text^','pics/icons/box.png','calendar')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('dynamic content','{content id= }','pics/icons/database_refresh.png','calendar')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('hr','---','pics/icons/page.png','calendar')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('center text','::text::','pics/icons/text_align_center.png','calendar')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('colored text','~~--FF0000:text~~','pics/icons/palette.png','calendar')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('dynamic variable','%text%','pics/icons/book_open.png','calendar')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('image','{img src= width= height= align= desc= link= }','pics/icons/picture.png','calendar')
go



-- articles
INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('bold','__text__','pics/icons/text_bold.png','articles')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('italic','\'\'text\'\'','pics/icons/text_italic.png','articles')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('underline','===text===','pics/icons/text_underline.png','articles')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('table new','||r1c1|r1c2\nr2c1|r2c2||','pics/icons/table.png','articles')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('external link','[http://example.com|text]','pics/icons/world_link.png','articles')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('wiki link','((text))','pics/icons/page_link.png','articles')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('heading1','!text','pics/icons/text_heading_1.png','articles')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('heading2','!!text','pics/icons/text_heading_2.png','articles')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('heading3','!!!text','pics/icons/text_heading_3.png','articles')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('title bar','-=text=-','pics/icons/text_padding_top.png','articles')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('box','^text^','pics/icons/box.png','articles')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('dynamic content','{content id= }','pics/icons/database_refresh.png','articles')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('hr','---','pics/icons/page.png','articles')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('center text','::text::','pics/icons/text_align_center.png','articles')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('colored text','~~--FF0000:text~~','pics/icons/palette.png','articles')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('dynamic variable','%text%','pics/icons/book_open.png','articles')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('image','{img src= width= height= align= desc= link= }','pics/icons/picture.png','articles')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('Email Address','[mailto:text|text]','pics/icons/email.png','articles')
go



-- faqs
INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('bold','__text__','pics/icons/text_bold.png','faqs')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('italic','\'\'text\'\'','pics/icons/text_italic.png','faqs')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('underline','===text===','pics/icons/text_underline.png','faqs')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('table new','||r1c1|r1c2\nr2c1|r2c2||','pics/icons/table.png','faqs')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('external link','[http://example.com|text]','pics/icons/world_link.png','faqs')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('wiki link','((text))','pics/icons/page_link.png','faqs')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('heading1','!text','pics/icons/text_heading_1.png','faqs')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('heading2','!!text','pics/icons/text_heading_2.png','faqs')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('heading3','!!!text','pics/icons/text_heading_3.png','faqs')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('title bar','-=text=-','pics/icons/text_padding_top.png','faqs')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('box','^text^','pics/icons/box.png','faqs')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('dynamic content','{content id= }','pics/icons/database_refresh.png','faqs')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('hr','---','pics/icons/page.png','faqs')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('center text','::text::','pics/icons/text_align_center.png','faqs')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('colored text','~~--FF0000:text~~','pics/icons/palette.png','faqs')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('dynamic variable','%text%','pics/icons/book_open.png','faqs')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('image','{img src= width= height= align= desc= link= }','pics/icons/picture.png','faqs')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('Email Address','[mailto:text|text]','pics/icons/email.png','faqs')
go



-- forums
INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('bold','__text__','pics/icons/text_bold.png','forums')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('italic','\'\'text\'\'','pics/icons/text_italic.png','forums')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('underline','===text===','pics/icons/text_underline.png','forums')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('table new','||r1c1|r1c2\nr2c1|r2c2||','pics/icons/table.png','forums')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('external link','[http://example.com|text]','pics/icons/world_link.png','forums')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('wiki link','((text))','pics/icons/page_link.png','forums')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('heading1','!text','pics/icons/text_heading_1.png','forums')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('heading2','!!text','pics/icons/text_heading_2.png','forums')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('heading3','!!!text','pics/icons/text_heading_3.png','forums')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('title bar','-=text=-','pics/icons/text_padding_top.png','forums')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('box','^text^','pics/icons/box.png','forums')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('dynamic content','{content id= }','pics/icons/database_refresh.png','forums')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('hr','---','pics/icons/page.png','forums')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('center text','::text::','pics/icons/text_align_center.png','forums')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('colored text','~~--FF0000:text~~','pics/icons/palette.png','forums')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('dynamic variable','%text%','pics/icons/book_open.png','forums')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('image','{img src= width= height= align= desc= link= }','pics/icons/picture.png','forums')
go


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('Email Address','[mailto:text|text]','pics/icons/email.png','forums')
go



--translated objects table
-- DROP TABLE "tiki_translated_objects"
go


CREATE TABLE "tiki_translated_objects" (
  "traId" numeric(14 ,0) identity,
  "type" varchar(50) NOT NULL,
  "objId" varchar(255) NOT NULL,
  "lang" varchar(16) default NULL NULL,
  PRIMARY KEY (type, objId)
) ENGINE=MyISAM 
go


CREATE  INDEX "tiki_translated_objects_traId" ON "tiki_translated_objects"( "traId" )
go


--
-- Community tables begin
--

-- DROP TABLE "tiki_friends"
go


CREATE TABLE "tiki_friends" (
  "user" varchar(200) default '' NOT NULL,
  "friend" varchar(200) default '' NOT NULL,
  PRIMARY KEY ("`user`","friend")
) ENGINE=MyISAM
go



-- DROP TABLE "tiki_friendship_requests"
go


CREATE TABLE "tiki_friendship_requests" (
  "userFrom" varchar(200) default '' NOT NULL,
  "userTo" varchar(200) default '' NOT NULL,
  "tstamp" timestamp NOT NULL,
  PRIMARY KEY ("userFrom","userTo")
) ENGINE=MyISAM
go



-- DROP TABLE "tiki_score"
go


CREATE TABLE "tiki_score" (
  "event" varchar(40) default '' NOT NULL,
  "score" numeric(11,0) default '0' NOT NULL,
  "expiration" numeric(11,0) default '0' NOT NULL,
  PRIMARY KEY ("event")
) ENGINE=MyISAM
go




INSERT INTO "tiki_score" ("event","score","expiration") VALUES ('login',1,0)
go


INSERT INTO "tiki_score" ("event","score","expiration") VALUES ('login_remain',2,60)
go


INSERT INTO "tiki_score" ("event","score","expiration") VALUES ('profile_fill',10,0)
go


INSERT INTO "tiki_score" ("event","score","expiration") VALUES ('profile_see',2,0)
go


INSERT INTO "tiki_score" ("event","score","expiration") VALUES ('profile_is_seen',1,0)
go


INSERT INTO "tiki_score" ("event","score","expiration") VALUES ('friend_new',10,0)
go


INSERT INTO "tiki_score" ("event","score","expiration") VALUES ('message_receive',1,0)
go


INSERT INTO "tiki_score" ("event","score","expiration") VALUES ('message_send',2,0)
go


INSERT INTO "tiki_score" ("event","score","expiration") VALUES ('article_read',2,0)
go


INSERT INTO "tiki_score" ("event","score","expiration") VALUES ('article_comment',5,0)
go


INSERT INTO "tiki_score" ("event","score","expiration") VALUES ('article_new',20,0)
go


INSERT INTO "tiki_score" ("event","score","expiration") VALUES ('article_is_read',1,0)
go


INSERT INTO "tiki_score" ("event","score","expiration") VALUES ('article_is_commented',2,0)
go


INSERT INTO "tiki_score" ("event","score","expiration") VALUES ('fgallery_new',10,0)
go


INSERT INTO "tiki_score" ("event","score","expiration") VALUES ('fgallery_new_file',10,0)
go


INSERT INTO "tiki_score" ("event","score","expiration") VALUES ('fgallery_download',5,0)
go


INSERT INTO "tiki_score" ("event","score","expiration") VALUES ('fgallery_is_downloaded',5,0)
go


INSERT INTO "tiki_score" ("event","score","expiration") VALUES ('igallery_new',10,0)
go


INSERT INTO "tiki_score" ("event","score","expiration") VALUES ('igallery_new_img',6,0)
go


INSERT INTO "tiki_score" ("event","score","expiration") VALUES ('igallery_see_img',3,0)
go


INSERT INTO "tiki_score" ("event","score","expiration") VALUES ('igallery_img_seen',1,0)
go


INSERT INTO "tiki_score" ("event","score","expiration") VALUES ('blog_new',20,0)
go


INSERT INTO "tiki_score" ("event","score","expiration") VALUES ('blog_post',5,0)
go


INSERT INTO "tiki_score" ("event","score","expiration") VALUES ('blog_read',2,0)
go


INSERT INTO "tiki_score" ("event","score","expiration") VALUES ('blog_comment',2,0)
go


INSERT INTO "tiki_score" ("event","score","expiration") VALUES ('blog_is_read',3,0)
go


INSERT INTO "tiki_score" ("event","score","expiration") VALUES ('blog_is_commented',3,0)
go


INSERT INTO "tiki_score" ("event","score","expiration") VALUES ('wiki_new',10,0)
go


INSERT INTO "tiki_score" ("event","score","expiration") VALUES ('wiki_edit',5,0)
go


INSERT INTO "tiki_score" ("event","score","expiration") VALUES ('wiki_attach_file',3,0)
go



-- DROP TABLE "tiki_users_score"
go


CREATE TABLE "tiki_users_score" (
  "user" char(200) default '' NOT NULL,
  "event_id" char(40) default '' NOT NULL,
  "expire" numeric(14,0) default '0' NOT NULL,
  "tstamp" timestamp NOT NULL,
  PRIMARY KEY ("user","event_id")
) ENGINE=MyISAM
go


CREATE  INDEX "tiki_users_score_user" ON "tiki_users_score"("user","event_id","expire")
go


--
-- Community tables end
--

--
-- Table structure for table tiki_file_handlers
--
-- Creation: Nov 02, 2004 at 05:59 PM
-- Last update: Nov 02, 2004 at 05:59 PM
--

-- DROP TABLE "tiki_file_handlers"
go


CREATE TABLE "tiki_file_handlers" (
  "mime_type" varchar(64) default NULL NULL,
  "cmd" varchar(238) default NULL
) ENGINE=MyISAM
go



--
-- Table structure for table tiki_stats
--
-- Creation: Aug 04, 2005 at 05:59 PM
-- Last update: Aug 04, 2005 at 05:59 PM
--

-- DROP TABLE "tiki_stats"
go


CREATE TABLE "tiki_stats" (
  "object" varchar(255) default '' NOT NULL,
  "type" varchar(20) default '' NOT NULL,
  "day" numeric(14,0) default '0' NOT NULL,
  "hits" numeric(14,0) default '0' NOT NULL,
  PRIMARY KEY ("object","type","day")
) ENGINE=MyISAM
go



--
-- Table structure for table tiki_events
--
-- Creation: Aug 26, 2005 at 06:59 AM - mdavey
-- Last update: Sep 31, 2005 at 12:29 PM - mdavey
--

-- DROP TABLE "tiki_events"
go


CREATE TABLE "tiki_events" (
  "callback_type" numeric(1,0) default '3' NOT NULL,
  `order` numeric(2,0) default '50' NOT NULL,
  "event" varchar(200) default '' NOT NULL,
  "file" varchar(200) default '' NOT NULL,  
  "object" varchar(200) default '' NOT NULL,
  "method" varchar(200) default '' NOT NULL,
  PRIMARY KEY ("callback_type","`order`")
) ENGINE=MyISAM
go



INSERT INTO "tiki_events" ("callback_type","`order`","event","file","object","method") VALUES ('1', '20', 'user_registers', 'lib/registration/registrationlib.php', 'registrationlib', 'callback_tikiwiki_setup_custom_fields')
go


INSERT INTO "tiki_events" ("event","file","object","method") VALUES ('user_registers', 'lib/registration/registrationlib.php', 'registrationlib', 'callback_tikiwiki_save_registration')
go


INSERT INTO "tiki_events" ("callback_type","`order`","event","file","object","method") VALUES ('5', '20', 'user_registers', 'lib/registration/registrationlib.php', 'registrationlib', 'callback_logslib_user_registers')
go


INSERT INTO "tiki_events" ("callback_type","`order`","event","file","object","method") VALUES ('5', '25', 'user_registers', 'lib/registration/registrationlib.php', 'registrationlib', 'callback_tikiwiki_send_email')
go


INSERT INTO "tiki_events" ("callback_type","`order`","event","file","object","method") VALUES ('5', '30', 'user_registers', 'lib/registration/registrationlib.php', 'registrationlib', 'callback_tikimail_user_registers')
go



--
-- Table structure for table tiki_registration_fields
--
-- Creation: Aug 31, 2005 at 12:57 PM - mdavey
-- Last update: Aug 31, 2005 at 12:57 PM - mdavey
-- 

-- DROP TABLE "tiki_registration_fields"
go


CREATE TABLE "tiki_registration_fields" (
  "id" numeric(11 ,0) identity,
  "field" varchar(255) default '' NOT NULL,
  "name" varchar(255) default NULL NULL,
  "type" varchar(255) default 'text' NOT NULL,
  `show` numeric(1,0) default '1' NOT NULL,
  "size" varchar(10) default '10',
  PRIMARY KEY ("id")
) ENGINE=MyISAM
go



-- DROP TABLE "tiki_actionlog_conf"
go


CREATE TABLE "tiki_actionlog_conf" (
  "id" numeric(11 ,0) identity,
  "action" varchar(32) default '' NOT NULL,
  "objectType" varchar(32) default '' NOT NULL,
 `status` char(1) default '',
PRIMARY KEY (action, objectType),
KEY (id)
) ENGINE=MyISAM
go


INSERT INTO "tiki_actionlog_conf" ("action","objectType","status") VALUES ('Created', 'wiki page', 'y')
go


INSERT INTO "tiki_actionlog_conf" ("action","objectType","status") VALUES ('Updated', 'wiki page', 'y')
go


INSERT INTO "tiki_actionlog_conf" ("action","objectType","status") VALUES ('Removed', 'wiki page', 'y')
go


INSERT INTO "tiki_actionlog_conf" ("action","objectType","status") VALUES ('Viewed', 'wiki page', 'n')
go


INSERT INTO "tiki_actionlog_conf" ("action","objectType","status") VALUES ('Viewed', 'forum', 'n')
go


INSERT INTO "tiki_actionlog_conf" ("action","objectType","status") VALUES ('Posted', 'forum', 'n')
go


INSERT INTO "tiki_actionlog_conf" ("action","objectType","status") VALUES ('Replied', 'forum', 'n')
go


INSERT INTO "tiki_actionlog_conf" ("action","objectType","status") VALUES ('Updated', 'forum', 'n')
go


INSERT INTO "tiki_actionlog_conf" ("action","objectType","status") VALUES ('Viewed', 'file gallery', 'n')
go


INSERT INTO "tiki_actionlog_conf" ("action","objectType","status") VALUES ('Viewed', 'image gallery', 'n')
go


INSERT INTO "tiki_actionlog_conf" ("action","objectType","status") VALUES ('Uploaded', 'file gallery', 'n')
go


INSERT INTO "tiki_actionlog_conf" ("action","objectType","status") VALUES ('Uploaded', 'image gallery', 'n')
go


INSERT INTO "tiki_actionlog_conf" ("action","objectType","status") VALUES ('Downloaded', 'file gallery', 'n')
go


INSERT INTO "tiki_actionlog_conf" ("action","objectType","status") VALUES ('*', 'category', 'n')
go


INSERT INTO "tiki_actionlog_conf" ("action","objectType","status") VALUES ('*', 'login', 'n')
go


INSERT INTO "tiki_actionlog_conf" ("action","objectType","status") VALUES ('Posted', 'message', 'n')
go


INSERT INTO "tiki_actionlog_conf" ("action","objectType","status") VALUES ('Replied', 'message', 'n')
go


INSERT INTO "tiki_actionlog_conf" ("action","objectType","status") VALUES ('Viewed', 'message', 'n')
go


INSERT INTO "tiki_actionlog_conf" ("action","objectType","status") VALUES ('Removed version', 'wiki page', 'n')
go


INSERT INTO "tiki_actionlog_conf" ("action","objectType","status") VALUES ('Removed last version', 'wiki page', 'n')
go


INSERT INTO "tiki_actionlog_conf" ("action","objectType","status") VALUES ('Rollback', 'wiki page', 'n')
go


INSERT INTO "tiki_actionlog_conf" ("action","objectType","status") VALUES ('Removed', 'forum', 'n')
go


INSERT INTO "tiki_actionlog_conf" ("action","objectType","status") VALUES ('Posted', 'comment', 'n')
go


INSERT INTO "tiki_actionlog_conf" ("action","objectType","status") VALUES ('Replied', 'comment', 'n')
go


INSERT INTO "tiki_actionlog_conf" ("action","objectType","status") VALUES ('Updated', 'comment', 'n')
go


INSERT INTO "tiki_actionlog_conf" ("action","objectType","status") VALUES ('Removed', 'comment', 'n')
go


INSERT INTO "tiki_actionlog_conf" ("action","objectType","status") VALUES ('Renamed', 'wiki page', 'n')
go


INSERT INTO "tiki_actionlog_conf" ("action","objectType","status") VALUES ('Created', 'sheet', 'n')
go


INSERT INTO "tiki_actionlog_conf" ("action","objectType","status") VALUES ('Updated', 'sheet', 'n')
go


INSERT INTO "tiki_actionlog_conf" ("action","objectType","status") VALUES ('Removed', 'sheet', 'n')
go


INSERT INTO "tiki_actionlog_conf" ("action","objectType","status") VALUES ('Viewed', 'sheet', 'n')
go


INSERT INTO "tiki_actionlog_conf" ("action","objectType","status") VALUES ('Viewed', 'blog', 'n')
go


INSERT INTO "tiki_actionlog_conf" ("action","objectType","status") VALUES ('Posted', 'blog', 'n')
go


INSERT INTO "tiki_actionlog_conf" ("action","objectType","status") VALUES ('Updated', 'blog', 'n')
go


INSERT INTO "tiki_actionlog_conf" ("action","objectType","status") VALUES ('Removed', 'blog', 'n')
go


INSERT INTO "tiki_actionlog_conf" ("action","objectType","status") VALUES ('Removed', 'file', 'n')
go


-- --------------------------------------------------------


-- Table structure for folksonomy tables
--
-- Creation: Out 16, 2005 - batawata
-- Last update: Out 16, 2005 - batawata
-- 

-- DROP TABLE "tiki_freetags"
go


CREATE TABLE "tiki_freetags" (
  "tagId" numeric(10 ,0) identity,
  "tag" varchar(30) default '' NOT NULL,
  "raw_tag" varchar(50) default '' NOT NULL,
  "lang" varchar(16) NULL,
  PRIMARY KEY ("tagId")
) ENGINE=MyISAM
go



-- DROP TABLE "tiki_freetagged_objects"
go


CREATE TABLE "tiki_freetagged_objects" (
  "tagId" numeric(12 ,0) identity,
  "objectId" numeric(11,0) default 0 NOT NULL,
  "user" varchar(200) default '' NOT NULL,
  "created" numeric(14,0) default '0' NOT NULL,
  PRIMARY KEY ("tagId","user","objectId")
  KEY (tagId),
  KEY (user),
  KEY (objectId)
) ENGINE=MyISAM
go




-- DROP TABLE "tiki_contributions"
go


CREATE TABLE "tiki_contributions" (
  "contributionId" numeric(12 ,0) identity,
  "name" varchar(100) default NULL NULL,
  "description" varchar(250) default NULL NULL,
  PRIMARY KEY ("contributionId")
) ENGINE=MyISAM
go



-- DROP TABLE "tiki_contributions_assigned"
go


CREATE TABLE "tiki_contributions_assigned" (
  "contributionId" numeric(12,0) NOT NULL,
  "objectId" numeric(12,0) NOT NULL,
  PRIMARY KEY ("objectId","contributionId")
) ENGINE=MyISAM
go



-- DROP TABLE "tiki_webmail_contacts_ext"
go


CREATE TABLE `tiki_webmail_contacts_ext` (
  `contactId` numeric(11,0) NOT NULL,
  `fieldId` numeric(10,0) NOT NULL,
  `value` varchar(255) NOT NULL,
  `hidden` numeric(1,0) NOT NULL,
  KEY `contactId` (`contactId`)
) ENGINE=MyISAM
go



-- DROP TABLE "tiki_webmail_contacts_fields"
go


CREATE TABLE `tiki_webmail_contacts_fields` (
  `fieldId numeric(10 ,0) identity,
  `user` VARCHAR( 200 ) NOT NULL ,
  `fieldname` VARCHAR( 255 ) NOT NULL ,
  `order` numeric(2,0) default '0' NOT NULL,
  `show` char(1) default 'n' NOT NULL,
  PRIMARY KEY ( `fieldId` ),
  "INDEX" ( `user` )
) ENGINE = MyISAM 
go



-- ---------- mypage ----------------
-- DROP TABLE "tiki_mypage"
go


CREATE TABLE `tiki_mypage` (
  `id numeric(11 ,0) identity,
  `id_users` numeric(11,0) NOT NULL,
  `id_types` numeric(11,0) NOT NULL,
  `created` numeric(11,0) NOT NULL,
  `modified` numeric(11,0) NOT NULL,
  `viewed` numeric(11,0) NOT NULL,
  `width` numeric(11,0) NOT NULL,
  `height` numeric(11,0) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `bgcolor` varchar(16) default NULL NULL,
  `winbgcolor` varchar(16) default NULL NULL,
  `wintitlecolor` varchar(16) default NULL NULL,
  `wintextcolor` varchar(16) default NULL NULL,
  `bgimage` varchar(255) default NULL NULL,
  `bgtype` enum ('color', 'imageurl') default 'color' NOT NULL,
  `winbgimage` varchar(255) default NULL NULL,
  `winbgtype` enum ('color', 'imageurl') default 'color' NOT NULL,
  PRIMARY KEY ("`id`")
  KEY `id_users` (`id_users`),
  KEY `name` (`name`),
  KEY `id_types` (`id_types`)
) ENGINE=MyISAM
go



-- DROP TABLE "tiki_mypagewin"
go


CREATE TABLE `tiki_mypagewin` (
  `id numeric(11 ,0) identity,
  `id_mypage` numeric(11,0) NOT NULL,
  `created` numeric(11,0) NOT NULL,
  `modified` numeric(11,0) NOT NULL,
  `viewed` numeric(11,0) NOT NULL,
  `title` varchar(255) NOT NULL,
  `inbody` enum('n','y') default 'n' NOT NULL,
  `modal` enum('n','y') default 'n' NOT NULL,
  `left` numeric(11,0) NOT NULL,
  `top` numeric(11,0) NOT NULL,
  `width` numeric(11,0) NOT NULL,
  `height` numeric(11,0) NOT NULL,
  `contenttype` varchar(31) default NULL NULL,
  `config` image,
  `content` image,
  PRIMARY KEY ("`id`")
  KEY `id_mypage` (`id_mypage`)
) ENGINE=MyISAM
go



-- DROP TABLE "tiki_mypage_types"
go


CREATE TABLE `tiki_mypage_types` (
  `id numeric(11 ,0) identity,
  `created` numeric(11,0) NOT NULL,
  `modified` numeric(11,0) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `section` varchar(255) default NULL NULL,
  `permissions` varchar(255) default NULL NULL,
  `def_height` numeric(11,0) default NULL NULL,
  `def_width` numeric(11,0) default NULL NULL,
  `fix_dimensions` enum('no','yes') NOT NULL,
  `def_bgcolor` varchar(8) default NULL NULL,
  `fix_bgcolor` enum('no','yes') NOT NULL,
  `templateuser` numeric(11,0) NOT NULL,
  PRIMARY KEY ("`id`")
  KEY `name` (`name`)
) ENGINE=MyISAM
go



-- DROP TABLE "tiki_mypage_types_components"
go


CREATE TABLE `tiki_mypage_types_components` (
  `id_mypage_types` numeric(11,0) NOT NULL,
  `compname` varchar(255) NOT NULL,
  `mincount` numeric(11,0) default '1' NOT NULL,
  `maxcount` numeric(11,0) default '1' NOT NULL,
  KEY `id_mypage_types` (`id_mypage_types`)
) ENGINE=MyISAM
go



-- ------------------------------------

-- DROP TABLE "tiki_pages_translation_bits"
go


CREATE TABLE `tiki_pages_translation_bits` (
  `translation_bit_id numeric(14 ,0) identity,
  `page_id` numeric(14,0) NOT NULL,
  `version` numeric(8,0) NOT NULL,
  `source_translation_bit` numeric(10,0) NULL,
  `original_translation_bit` numeric(10,0) NULL,
  `flags` SET('critical') DEFAULT '' NOT NULL,
  PRIMARY KEY (`translation_bit_id`),
  KEY(`page_id`),
  KEY(`original_translation_bit`),
  KEY(`source_translation_bit`)
)
go



-- DROP TABLE "tiki_pages_changes"
go


CREATE TABLE "tiki_pages_changes" (
  "page_id" numeric(14,0) default NULL NULL,
  "version" numeric(10,0) default NULL NULL,
  "segments_added" numeric(10,0) default NULL NULL,
  "segments_removed" numeric(10,0) default NULL NULL,
  "segments_total" numeric(10,0) default NULL NULL,
  PRIMARY KEY(page_id, version)
)
go




go


