-- $Rev$
-- $Date: 2008-02-27 06:23:44 $
-- $Author: marclaporte $
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
-- ******************************************************

--
-- Table structure for table galaxia_activities
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'galaxia_activities';

CREATE TABLE 'galaxia_activities' (
  "activityId" INTEGER,
  "name" varchar(80) default NULL,
  "normalized_name" varchar(80) default NULL,
  "pId" bigint NOT NULL default '0',
  "type" varchar(12) CHECK ("type" IN ('start','end','split','switch','join','activity','standalone')) default NULL,
  "isAutoRouted" char(1) default NULL,
  "flowNum" bigint default NULL,
  "isInteractive" char(1) default NULL,
  "lastModif" bigint default NULL,
  "description" text,
  "expirationTime" integer unsigned NOT NULL default '0',
  PRIMARY KEY ("activityId")
) ENGINE=MyISAM ;

-- ******************************************************

--
-- Table structure for table galaxia_activity_roles
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'galaxia_activity_roles';

CREATE TABLE 'galaxia_activity_roles' (
  "activityId" bigint NOT NULL default '0',
  "roleId" bigint NOT NULL default '0',
  PRIMARY KEY ("activityId","roleId")
) ENGINE=MyISAM;

-- ******************************************************

--
-- Table structure for table galaxia_instance_activities
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'galaxia_instance_activities';

CREATE TABLE 'galaxia_instance_activities' (
  "instanceId" bigint NOT NULL default '0',
  "activityId" bigint NOT NULL default '0',
  "started" bigint NOT NULL default '0',
  "ended" bigint NOT NULL default '0',
  "user" varchar(200) default '',
  "status" varchar(11) CHECK ("status" IN ('running','completed')) default NULL,
  PRIMARY KEY ("instanceId","activityId")
) ENGINE=MyISAM;

-- ******************************************************

--
-- Table structure for table galaxia_instance_comments
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'galaxia_instance_comments';

CREATE TABLE 'galaxia_instance_comments' (
  "cId" INTEGER,
  "instanceId" bigint NOT NULL default '0',
  "user" varchar(200) default '',
  "activityId" bigint default NULL,
  "hash" varchar(34) default NULL,
  "title" varchar(250) default NULL,
  "comment" text,
  "activity" varchar(80) default NULL,
  "timestamp" bigint default NULL,
  PRIMARY KEY ("cId")
) ENGINE=MyISAM ;

-- ******************************************************

--
-- Table structure for table galaxia_instances
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'galaxia_instances';

CREATE TABLE 'galaxia_instances' (
  "instanceId" INTEGER,
  "pId" bigint NOT NULL default '0',
  "started" bigint default NULL,
  "name" varchar(200) NOT NULL default 'No Name',
  "owner" varchar(200) default NULL,
  "nextActivity" bigint default NULL,
  "nextUser" varchar(200) default NULL,
  "ended" bigint default NULL,
  "status" varchar(11) CHECK ("status" IN ('active','exception','aborted','completed')) default NULL,
  "properties" bytea,
  PRIMARY KEY ("instanceId")
) ENGINE=MyISAM ;

-- ******************************************************

--
-- Table structure for table galaxia_processes
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'galaxia_processes';

CREATE TABLE 'galaxia_processes' (
  "pId" INTEGER,
  "name" varchar(80) default NULL,
  "isValid" char(1) default NULL,
  "isActive" char(1) default NULL,
  "version" varchar(12) default NULL,
  "description" text,
  "lastModif" bigint default NULL,
  "normalized_name" varchar(80) default NULL,
  PRIMARY KEY ("pId")
) ENGINE=MyISAM ;

-- ******************************************************

--
-- Table structure for table galaxia_roles
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'galaxia_roles';

CREATE TABLE 'galaxia_roles' (
  "roleId" INTEGER,
  "pId" bigint NOT NULL default '0',
  "lastModif" bigint default NULL,
  "name" varchar(80) default NULL,
  "description" text,
  PRIMARY KEY ("roleId")
) ENGINE=MyISAM ;

-- ******************************************************

--
-- Table structure for table galaxia_transitions
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'galaxia_transitions';

CREATE TABLE 'galaxia_transitions' (
  "pId" bigint NOT NULL default '0',
  "actFromId" bigint NOT NULL default '0',
  "actToId" bigint NOT NULL default '0',
  PRIMARY KEY ("actFromId","actToId")
) ENGINE=MyISAM;

-- ******************************************************

--
-- Table structure for table galaxia_user_roles
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'galaxia_user_roles';

CREATE TABLE 'galaxia_user_roles' (
  "pId" bigint NOT NULL default '0',
  "roleId" INTEGER,
  "user" varchar(200) NOT NULL default '',
  PRIMARY KEY ("roleId","user")
) ENGINE=MyISAM ;

-- ******************************************************

--
-- Table structure for table galaxia_workitems
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'galaxia_workitems';

CREATE TABLE 'galaxia_workitems' (
  "itemId" INTEGER,
  "instanceId" bigint NOT NULL default '0',
  "orderId" bigint NOT NULL default '0',
  "activityId" bigint NOT NULL default '0',
  "properties" bytea,
  "started" bigint default NULL,
  "ended" bigint default NULL,
  "user" varchar(200) default '',
  PRIMARY KEY ("itemId")
) ENGINE=MyISAM ;

-- ******************************************************

--
-- Table structure for table messu_messages
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 08:29 PM
--

DROP TABLE IF EXISTS 'messu_messages';

CREATE TABLE 'messu_messages' (
  "msgId" INTEGER,
  "user" varchar(200) NOT NULL default '',
  "user_from" varchar(200) NOT NULL default '',
  "user_to" text,
  "user_cc" text,
  "user_bcc" text,
  "subject" varchar(255) default NULL,
  "body" text,
  "hash" varchar(32) default NULL,
  "replyto_hash" varchar(32) default NULL,
  "date" bigint default NULL,
  "isRead" char(1) default NULL,
  "isReplied" char(1) default NULL,
  "isFlagged" char(1) default NULL,
  "priority" smallint default NULL,
  PRIMARY KEY ("msgId")
) ENGINE=MyISAM ;

CREATE  INDEX "messu_messages_userIsRead" ON "messu_messages"("user" "isRead");
-- ******************************************************

--
-- Table structure for table messu_archive (same structure as messu_messages)
-- desc: user may archive his messages to this table to speed up default msg handling
--
-- Creation: Feb 26, 2005 at 03:00 PM
-- Last update: Feb 26, 2005 at 03:00 PM
--

DROP TABLE IF EXISTS 'messu_archive';

CREATE TABLE 'messu_archive' (
  "msgId" INTEGER,
  "user" varchar(40) NOT NULL default '',
  "user_from" varchar(40) NOT NULL default '',
  "user_to" text,
  "user_cc" text,
  "user_bcc" text,
  "subject" varchar(255) default NULL,
  "body" text,
  "hash" varchar(32) default NULL,
  "replyto_hash" varchar(32) default NULL,
  "date" bigint default NULL,
  "isRead" char(1) default NULL,
  "isReplied" char(1) default NULL,
  "isFlagged" char(1) default NULL,
  "priority" smallint default NULL,
  PRIMARY KEY ("msgId")
) ENGINE=MyISAM ;

-- ******************************************************

--
-- Table structure for table messu_sent (same structure as messu_messages)
-- desc: user may archive his messages to this table to speed up default msg handling
--
-- Creation: Feb 26, 2005 at 11:00 PM
-- Last update: Feb 26, 2005 at 11:00 PM
--

DROP TABLE IF EXISTS 'messu_sent';

CREATE TABLE 'messu_sent' (
  "msgId" INTEGER,
  "user" varchar(40) NOT NULL default '',
  "user_from" varchar(40) NOT NULL default '',
  "user_to" text,
  "user_cc" text,
  "user_bcc" text,
  "subject" varchar(255) default NULL,
  "body" text,
  "hash" varchar(32) default NULL,
  "replyto_hash" varchar(32) default NULL,
  "date" bigint default NULL,
  "isRead" char(1) default NULL,
  "isReplied" char(1) default NULL,
  "isFlagged" char(1) default NULL,
  "priority" smallint default NULL,
  PRIMARY KEY ("msgId")
) ENGINE=MyISAM ;

-- ******************************************************

DROP TABLE IF EXISTS 'sessions';

CREATE TABLE 'sessions'(
  "sesskey" char(32) NOT NULL,
  "expiry" bigint unsigned NOT NULL,
  "expireref" varchar(64),
  "data" text NOT NULL,
  PRIMARY KEY ("sesskey")
) ENGINE=MyISAM;

CREATE  INDEX "sessions_expiry" ON "sessions"("expiry");

--
-- Table structure for table tiki_actionlog
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 13, 2003 at 12:29 AM
--

DROP TABLE IF EXISTS 'tiki_actionlog';

CREATE TABLE 'tiki_actionlog' (
  "actionId" INTEGER,
  "action" varchar(255) NOT NULL default '',
  "lastModif" bigint default NULL,
  "object" varchar(255) default NULL,
  "objectType" varchar(32) NOT NULL default '',
  "user" varchar(200) default '',
  "ip" varchar(15) default NULL,
  "comment" varchar(200) default NULL,
  "categId" bigint NOT NULL default '0',
  PRIMARY KEY ("actionId")
) ENGINE=MyISAM;


DROP TABLE IF EXISTS 'tiki_actionlog_params';

CREATE TABLE 'tiki_actionlog_params' (
  "actionId" integer NOT NULL,
  "name" varchar(40) NOT NULL,
  "value" text,
  KEY (actionId)
) ENGINE=MyISAM;

CREATE  INDEX "tiki_actionlog_params_nameValue" ON "tiki_actionlog_params"("name" "value");
-- ******************************************************

--
-- Table structure for table tiki_articles
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Nov 27, 2006 at 21:53 PM
-- Last check: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_articles';

CREATE TABLE 'tiki_articles' (
  "articleId" INTEGER,
  "topline" varchar(255) default NULL,
  "title" varchar(255) default NULL,
  "subtitle" varchar(255) default NULL,
  "linkto" varchar(255) default NULL,
  "lang" varchar(16) default NULL,
  "state" char(1) default 's',
  "authorName" varchar(60) default NULL,
  "topicId" bigint default NULL,
  "topicName" varchar(40) default NULL,
  "size" bigint default NULL,
  "useImage" char(1) default NULL,
  "image_name" varchar(80) default NULL,
  "image_caption" text default NULL,
  "image_type" varchar(80) default NULL,
  "image_size" bigint default NULL,
  "image_x" smallint default NULL,
  "image_y" smallint default NULL,
  "image_data" bytea,
  "publishDate" bigint default NULL,
  "expireDate" bigint default NULL,
  "created" bigint default NULL,
  "heading" text,
  "body" text,
  "hash" varchar(32) default NULL,
  "author" varchar(200) default NULL,
  "nbreads" bigint default NULL,
  "votes" integer default NULL,
  "points" bigint default NULL,
  "type" varchar(50) default NULL,
  "rating" decimal(3,2) default NULL,
  "isfloat" char(1) default NULL,
  PRIMARY KEY ("articleId")
) ENGINE=MyISAM ;

CREATE  INDEX "tiki_articles_title" ON "tiki_articles"("title");
CREATE  INDEX "tiki_articles_heading" ON "tiki_articles"("heading");
CREATE  INDEX "tiki_articles_body" ON "tiki_articles"("body");
CREATE  INDEX "tiki_articles_author" ON "tiki_articles"("author");
CREATE  INDEX "tiki_articles_nbreads" ON "tiki_articles"("nbreads");
CREATE  INDEX "tiki_articles_topicId" ON "tiki_articles"("topicId");
CREATE  INDEX "tiki_articles_publishDate" ON "tiki_articles"("publishDate");
CREATE  INDEX "tiki_articles_expireDate" ON "tiki_articles"("expireDate");
CREATE  INDEX "tiki_articles_type" ON "tiki_articles"("type");
CREATE  INDEX "tiki_articles_ft" ON "tiki_articles"("title","heading","body");
-- ******************************************************

DROP TABLE IF EXISTS 'tiki_article_types';

CREATE TABLE 'tiki_article_types' (
  "type" varchar(50) NOT NULL,
  "use_ratings" varchar(1) default NULL,
  "show_pre_publ" varchar(1) default NULL,
  "show_post_expire" varchar(1) default 'y',
  "heading_only" varchar(1) default NULL,
  "allow_comments" varchar(1) default 'y',
  "show_image" varchar(1) default 'y',
  "show_avatar" varchar(1) default NULL,
  "show_author" varchar(1) default 'y',
  "show_pubdate" varchar(1) default 'y',
  "show_expdate" varchar(1) default NULL,
  "show_reads" varchar(1) default 'y',
  "show_size" varchar(1) default 'y',
  "show_topline" varchar(1) default 'n',
  "show_subtitle" varchar(1) default 'n',
  "show_linkto" varchar(1) default 'n',
  "show_image_caption" varchar(1) default 'n',
  "show_lang" varchar(1) default 'n',
  "creator_edit" varchar(1) default NULL,
  "comment_can_rate_article" char(1) default NULL,
  PRIMARY KEY ("type")
) ENGINE=MyISAM ;

CREATE  INDEX "tiki_article_types_show_pre_publ" ON "tiki_article_types"("show_pre_publ");
CREATE  INDEX "tiki_article_types_show_post_expire" ON "tiki_article_types"("show_post_expire");

INSERT INTO "tiki_article_types" ("type") VALUES ('Article');

INSERT INTO "tiki_article_types" ("type","use_ratings") VALUES ('Review','y');

INSERT INTO "tiki_article_types" ("type","show_post_expire") VALUES ('Event','n');

INSERT INTO "tiki_article_types" ("type","show_post_expire","heading_only","allow_comments") VALUES ('Classified','n','y','n');


--
-- Table structure for table tiki_banners
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_banners';

CREATE TABLE 'tiki_banners' (
  "bannerId" INTEGER,
  "client" varchar(200) NOT NULL default '',
  "url" varchar(255) default NULL,
  "title" varchar(255) default NULL,
  "alt" varchar(250) default NULL,
  "which" varchar(50) default NULL,
  "imageData" bytea,
  "imageType" varchar(200) default NULL,
  "imageName" varchar(100) default NULL,
  "HTMLData" text,
  "fixedURLData" varchar(255) default NULL,
  "textData" text,
  "fromDate" bigint default NULL,
  "toDate" bigint default NULL,
  "useDates" char(1) default NULL,
  "mon" char(1) default NULL,
  "tue" char(1) default NULL,
  "wed" char(1) default NULL,
  "thu" char(1) default NULL,
  "fri" char(1) default NULL,
  "sat" char(1) default NULL,
  "sun" char(1) default NULL,
  "hourFrom" varchar(4) default NULL,
  "hourTo" varchar(4) default NULL,
  "created" bigint default NULL,
  "maxImpressions" integer default NULL,
  "impressions" integer default NULL,
  "clicks" integer default NULL,
  "zone" varchar(40) default NULL,
  PRIMARY KEY ("bannerId")
) ENGINE=MyISAM ;

-- ******************************************************

--
-- Table structure for table tiki_banning
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_banning';

CREATE TABLE 'tiki_banning' (
  "banId" INTEGER,
  "mode" varchar(6) CHECK ("mode" IN ('user','ip')) default NULL,
  "title" varchar(200) default NULL,
  "ip1" char(3) default NULL,
  "ip2" char(3) default NULL,
  "ip3" char(3) default NULL,
  "ip4" char(3) default NULL,
  "user" varchar(200) default '',
  "date_from" timestamp(3) NOT NULL,
  "date_to" timestamp(3) NOT NULL,
  "use_dates" char(1) default NULL,
  "created" bigint default NULL,
  "message" text,
  PRIMARY KEY ("banId")
) ENGINE=MyISAM ;

-- ******************************************************

--
-- Table structure for table tiki_banning_sections
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_banning_sections';

CREATE TABLE 'tiki_banning_sections' (
  "banId" bigint NOT NULL default '0',
  "section" varchar(100) NOT NULL default '',
  PRIMARY KEY ("banId","section")
) ENGINE=MyISAM;

-- ******************************************************

--
-- Table structure for table tiki_blog_activity
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 04:52 PM
--

DROP TABLE IF EXISTS 'tiki_blog_activity';

CREATE TABLE 'tiki_blog_activity' (
  "blogId" integer NOT NULL default '0',
  "day" bigint NOT NULL default '0',
  "posts" integer default NULL,
  PRIMARY KEY ("blogId","day")
) ENGINE=MyISAM;

-- ******************************************************

--
-- Table structure for table tiki_blog_posts
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 04:52 PM
-- Last check: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_blog_posts';

CREATE TABLE 'tiki_blog_posts' (
  "postId" INTEGER,
  "blogId" integer NOT NULL default '0',
  "data" text,
  "data_size" bigint unsigned NOT NULL default '0',
  "created" bigint default NULL,
  "user" varchar(200) default '',
  "trackbacks_to" text,
  "trackbacks_from" text,
  "title" varchar(80) default NULL,
  "priv" varchar(1) default NULL,
  PRIMARY KEY ("postId")
) ENGINE=MyISAM ;

CREATE  INDEX "tiki_blog_posts_data" ON "tiki_blog_posts"("data");
CREATE  INDEX "tiki_blog_posts_blogId" ON "tiki_blog_posts"("blogId");
CREATE  INDEX "tiki_blog_posts_created" ON "tiki_blog_posts"("created");
CREATE  INDEX "tiki_blog_posts_ft" ON "tiki_blog_posts"("data","title");
-- ******************************************************

--
-- Table structure for table tiki_blog_posts_images
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_blog_posts_images';

CREATE TABLE 'tiki_blog_posts_images' (
  "imgId" INTEGER,
  "postId" bigint NOT NULL default '0',
  "filename" varchar(80) default NULL,
  "filetype" varchar(80) default NULL,
  "filesize" bigint default NULL,
  "data" bytea,
  PRIMARY KEY ("imgId")
) ENGINE=MyISAM ;

-- ******************************************************

--
-- Table structure for table tiki_blogs
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 13, 2003 at 01:07 AM
-- Last check: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_blogs';

CREATE TABLE 'tiki_blogs' (
  "blogId" INTEGER,
  "created" bigint default NULL,
  "lastModif" bigint default NULL,
  "title" varchar(200) default NULL,
  "description" text,
  "user" varchar(200) default '',
  "public" char(1) default NULL,
  "posts" integer default NULL,
  "maxPosts" integer default NULL,
  "hits" integer default NULL,
  "activity" decimal(4,2) default NULL,
  "heading" text,
  "use_find" char(1) default NULL,
  "use_title" char(1) default NULL,
  "add_date" char(1) default NULL,
  "add_poster" char(1) default NULL,
  "allow_comments" char(1) default NULL,
  "show_avatar" char(1) default NULL,
  PRIMARY KEY ("blogId")
) ENGINE=MyISAM ;

CREATE  INDEX "tiki_blogs_title" ON "tiki_blogs"("title");
CREATE  INDEX "tiki_blogs_description" ON "tiki_blogs"("description");
CREATE  INDEX "tiki_blogs_hits" ON "tiki_blogs"("hits");
CREATE  INDEX "tiki_blogs_ft" ON "tiki_blogs"("title","description");
-- ******************************************************

--
-- Table structure for table tiki_calendar_categories
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 07:05 AM
--

DROP TABLE IF EXISTS 'tiki_calendar_categories';

CREATE TABLE 'tiki_calendar_categories' (
  "calcatId" INTEGER,
  "calendarId" bigint NOT NULL default '0',
  "name" varchar(255) NOT NULL default '',
  PRIMARY KEY ("calcatId")
) ENGINE=MyISAM ;

CREATE UNIQUE INDEX "tiki_calendar_categories_catname" ON "tiki_calendar_categories"("calendarId","name");
-- ******************************************************

--
-- Table structure for table tiki_calendar_items
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 07:43 AM
--

DROP TABLE IF EXISTS 'tiki_calendar_items';

CREATE TABLE 'tiki_calendar_items' (
  "calitemId" INTEGER,
  "calendarId" bigint NOT NULL default '0',
  "start" bigint NOT NULL default '0',
  "end" bigint NOT NULL default '0',
  "locationId" bigint default NULL,
  "categoryId" bigint default NULL,
  "nlId" bigint NOT NULL default '0',
  "priority" varchar(3) CHECK ("priority" IN ('1','2','3','4','5','6','7','8','9')) NOT NULL default '1',
  "status" varchar(3) CHECK ("status" IN ('0','1','2')) NOT NULL default '0',
  "url" varchar(255) default NULL,
  "lang" char(16) NOT NULL default 'en',
  "name" varchar(255) NOT NULL default '',
  "description" bytea,
  "user" varchar(200) default '',
  "created" bigint NOT NULL default '0',
  "lastmodif" bigint NOT NULL default '0',
  PRIMARY KEY ("calitemId")
) ENGINE=MyISAM ;

CREATE  INDEX "tiki_calendar_items_calendarId" ON "tiki_calendar_items"("calendarId");
-- ******************************************************

--
-- Table structure for table tiki_calendar_locations
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 07:05 AM
--

DROP TABLE IF EXISTS 'tiki_calendar_locations';

CREATE TABLE 'tiki_calendar_locations' (
  "callocId" INTEGER,
  "calendarId" bigint NOT NULL default '0',
  "name" varchar(255) NOT NULL default '',
  "description" bytea,
  PRIMARY KEY ("callocId")
) ENGINE=MyISAM ;

CREATE UNIQUE INDEX "tiki_calendar_locations_locname" ON "tiki_calendar_locations"("calendarId","name");
-- ******************************************************

--
-- Table structure for table tiki_calendar_roles
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_calendar_roles';

CREATE TABLE 'tiki_calendar_roles' (
  "calitemId" bigint NOT NULL default '0',
  "username" varchar(200) NOT NULL default '',
  "role" varchar(3) CHECK ("role" IN ('0','1','2','3','6')) NOT NULL default '0',
  PRIMARY KEY ("calitemId","username","role")
) ENGINE=MyISAM;

-- ******************************************************

--
-- Table structure for table tiki_calendars
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 05, 2003 at 02:03 PM
--

DROP TABLE IF EXISTS 'tiki_calendars';

CREATE TABLE 'tiki_calendars' (
  "calendarId" INTEGER,
  "name" varchar(80) NOT NULL default '',
  "description" varchar(255) default NULL,
  "user" varchar(200) NOT NULL default '',
  "customlocations" varchar(3) CHECK ("customlocations" IN ('n','y')) NOT NULL default 'n',
  "customcategories" varchar(3) CHECK ("customcategories" IN ('n','y')) NOT NULL default 'n',
  "customlanguages" varchar(3) CHECK ("customlanguages" IN ('n','y')) NOT NULL default 'n',
  "custompriorities" varchar(3) CHECK ("custompriorities" IN ('n','y')) NOT NULL default 'n',
  "customparticipants" varchar(3) CHECK ("customparticipants" IN ('n','y')) NOT NULL default 'n',
  "customsubscription" varchar(3) CHECK ("customsubscription" IN ('n','y')) NOT NULL default 'n',
  "created" bigint NOT NULL default '0',
  "lastmodif" bigint NOT NULL default '0',
  "personal" varchar(4) CHECK ("personal" IN ('n', 'y')) NOT NULL default 'n',
  PRIMARY KEY ("calendarId")
) ENGINE=MyISAM ;

-- ******************************************************

DROP TABLE IF EXISTS 'tiki_calendar_options';

CREATE TABLE 'tiki_calendar_options' (
  "calendarId" bigint NOT NULL default 0,
  "optionName" varchar(120) NOT NULL default '',
  "value" varchar(255),
  PRIMARY KEY (calendarId,optionName)
) ENGINE=MyISAM ;

-- ******************************************************
--
-- Table structure for table tiki_categories
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 04, 2003 at 09:47 PM
--

DROP TABLE IF EXISTS 'tiki_categories';

CREATE TABLE 'tiki_categories' (
  "categId" INTEGER,
  "name" varchar(100) default NULL,
  "description" varchar(250) default NULL,
  "parentId" bigint default NULL,
  "hits" integer default NULL,
  PRIMARY KEY ("categId")
) ENGINE=MyISAM ;

-- ******************************************************

--
-- Table structure for table tiki_categorized_objects
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Dec 06, 2005 
--

DROP TABLE IF EXISTS 'tiki_objects';

CREATE TABLE 'tiki_objects' (
  "objectId" INTEGER,
  "type" varchar(50) default NULL,
  "itemId" varchar(255) default NULL,
  "description" text,
  "created" bigint default NULL,
  "name" varchar(200) default NULL,
  "href" varchar(200) default NULL,
  "hits" integer default NULL,
  PRIMARY KEY ("objectId")
  KEY (type, objectId),
  KEY (itemId, type)
) ENGINE=MyISAM ;

-- ******************************************************

-- Table structure for table tiki_categorized_objects
--

DROP TABLE IF EXISTS 'tiki_categorized_objects';

CREATE TABLE tiki_categorized_objects (
  catObjectId bigint NOT NULL default '0',
  PRIMARY KEY ("catObjectId")
) ENGINE=MyISAM ;



--
-- Table structure for table tiki_category_objects
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 13, 2003 at 01:09 AM
--

DROP TABLE IF EXISTS 'tiki_category_objects';

CREATE TABLE 'tiki_category_objects' (
  "catObjectId" bigint NOT NULL default '0',
  "categId" bigint NOT NULL default '0',
  PRIMARY KEY ("catObjectId","categId")
) ENGINE=MyISAM;

-- ******************************************************

--
-- Table structure for table tiki_category_sites
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 07, 2003 at 01:53 AM
--

DROP TABLE IF EXISTS 'tiki_object_ratings';

CREATE TABLE 'tiki_object_ratings' (
  "catObjectId" bigint NOT NULL default '0',
  "pollId" bigint NOT NULL default '0',
  PRIMARY KEY ("catObjectId","pollId")
) ENGINE=MyISAM;

-- ******************************************************

--
-- Table structure for table tiki_category_sites
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 07, 2003 at 01:53 AM
--

DROP TABLE IF EXISTS 'tiki_category_sites';

CREATE TABLE 'tiki_category_sites' (
  "categId" bigint NOT NULL default '0',
  "siteId" bigint NOT NULL default '0',
  PRIMARY KEY ("categId","siteId")
) ENGINE=MyISAM;

-- ******************************************************

--
-- Table structure for table tiki_chart_items
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_chart_items';

CREATE TABLE 'tiki_chart_items' (
  "itemId" INTEGER,
  "title" varchar(250) default NULL,
  "description" text,
  "chartId" bigint NOT NULL default '0',
  "created" bigint default NULL,
  "URL" varchar(250) default NULL,
  "votes" bigint default NULL,
  "points" bigint default NULL,
  "average" decimal(4,2) default NULL,
  PRIMARY KEY ("itemId")
) ENGINE=MyISAM ;

-- ******************************************************

--
-- Table structure for table tiki_charts
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 06, 2003 at 08:14 AM
--

DROP TABLE IF EXISTS 'tiki_charts';

CREATE TABLE 'tiki_charts' (
  "chartId" INTEGER,
  "title" varchar(250) default NULL,
  "description" text,
  "hits" bigint default NULL,
  "singleItemVotes" char(1) default NULL,
  "singleChartVotes" char(1) default NULL,
  "suggestions" char(1) default NULL,
  "autoValidate" char(1) default NULL,
  "topN" integer default NULL,
  "maxVoteValue" smallint default NULL,
  "frequency" bigint default NULL,
  "showAverage" char(1) default NULL,
  "isActive" char(1) default NULL,
  "showVotes" char(1) default NULL,
  "useCookies" char(1) default NULL,
  "lastChart" bigint default NULL,
  "voteAgainAfter" bigint default NULL,
  "created" bigint default NULL,
  PRIMARY KEY ("chartId")
) ENGINE=MyISAM ;

-- ******************************************************

--
-- Table structure for table tiki_charts_rankings
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_charts_rankings';

CREATE TABLE 'tiki_charts_rankings' (
  "chartId" bigint NOT NULL default '0',
  "itemId" bigint NOT NULL default '0',
  "position" bigint NOT NULL default '0',
  "timestamp" bigint NOT NULL default '0',
  "lastPosition" bigint NOT NULL default '0',
  "period" bigint NOT NULL default '0',
  "rvotes" bigint NOT NULL default '0',
  "raverage" decimal(4,2) NOT NULL default '0.00',
  PRIMARY KEY ("chartId","itemId","period")
) ENGINE=MyISAM;

-- ******************************************************

--
-- Table structure for table tiki_charts_votes
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_charts_votes';

CREATE TABLE 'tiki_charts_votes' (
  "user" varchar(200) NOT NULL default '',
  "itemId" bigint NOT NULL default '0',
  "timestamp" bigint default NULL,
  "chartId" bigint default NULL,
  PRIMARY KEY ("user","itemId")
) ENGINE=MyISAM;

-- ******************************************************

--
-- Table structure for table tiki_chat_channels
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_chat_channels';

CREATE TABLE 'tiki_chat_channels' (
  "channelId" INTEGER,
  "name" varchar(30) default NULL,
  "description" varchar(250) default NULL,
  "max_users" integer default NULL,
  "mode" char(1) default NULL,
  "moderator" varchar(200) default NULL,
  "active" char(1) default NULL,
  "refresh" integer default NULL,
  PRIMARY KEY ("channelId")
) ENGINE=MyISAM ;

-- ******************************************************

--
-- Table structure for table tiki_chat_messages
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_chat_messages';

CREATE TABLE 'tiki_chat_messages' (
  "messageId" INTEGER,
  "channelId" integer NOT NULL default '0',
  "data" varchar(255) default NULL,
  "poster" varchar(200) NOT NULL default 'anonymous',
  "timestamp" bigint default NULL,
  PRIMARY KEY ("messageId")
) ENGINE=MyISAM ;

-- ******************************************************

--
-- Table structure for table tiki_chat_users
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_chat_users';

CREATE TABLE 'tiki_chat_users' (
  "nickname" varchar(200) NOT NULL default '',
  "channelId" integer NOT NULL default '0',
  "timestamp" bigint default NULL,
  PRIMARY KEY ("nickname","channelId")
) ENGINE=MyISAM;

-- ******************************************************

--
-- Table structure for table tiki_comments
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 10:56 PM
-- Last check: Jul 11, 2003 at 01:52 AM
--

DROP TABLE IF EXISTS 'tiki_comments';

CREATE TABLE 'tiki_comments' (
  "threadId" INTEGER,
  "object" varchar(255) NOT NULL default '',
  "objectType" varchar(32) NOT NULL default '',
  "parentId" bigint default NULL,
  "userName" varchar(200) default '',
  "commentDate" bigint default NULL,
  "hits" integer default NULL,
  "type" char(1) default NULL,
  "points" decimal(8,2) default NULL,
  "votes" integer default NULL,
  "average" decimal(8,4) default NULL,
  "title" varchar(255) default NULL,
  "data" text,
  "hash" varchar(32) default NULL,
  "user_ip" varchar(15) default NULL,
  "summary" varchar(240) default NULL,
  "smiley" varchar(80) default NULL,
  "message_id" varchar(128) default NULL,
  "in_reply_to" varchar(128) default NULL,
  "comment_rating" smallint default NULL,
  "archived" char(1) default NULL,
  PRIMARY KEY ("threadId")
) ENGINE=MyISAM ;

CREATE  INDEX "tiki_comments_title" ON "tiki_comments"("title");
CREATE  INDEX "tiki_comments_data" ON "tiki_comments"("data");
CREATE  INDEX "tiki_comments_objectType" ON "tiki_comments"("object" "objectType");
CREATE  INDEX "tiki_comments_commentDate" ON "tiki_comments"("commentDate");
CREATE  INDEX "tiki_comments_hits" ON "tiki_comments"("hits");
CREATE  INDEX "tiki_comments_threaded" ON "tiki_comments"("message_id" "in_reply_to" "parentId");
CREATE  INDEX "tiki_comments_ft" ON "tiki_comments"("title","data");
CREATE UNIQUE INDEX "tiki_comments_no_repeats" ON "tiki_comments"("parentId" "userName" "title" "commentDate" "message_id" "in_reply_to");
-- ******************************************************

--
-- Table structure for table tiki_content
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_content';

CREATE TABLE 'tiki_content' (
  "contentId" INTEGER,
  "description" text,
  "contentLabel" varchar(255) NOT NULL default '',
  PRIMARY KEY ("contentId")
) ENGINE=MyISAM ;

-- ******************************************************

--
-- Table structure for table tiki_content_templates
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 12:37 AM
--

DROP TABLE IF EXISTS 'tiki_content_templates';

CREATE TABLE 'tiki_content_templates' (
  "templateId" INTEGER,
  "content" bytea,
  "name" varchar(200) default NULL,
  "created" bigint default NULL,
  PRIMARY KEY ("templateId")
) ENGINE=MyISAM ;

-- ******************************************************

--
-- Table structure for table tiki_content_templates_sections
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 12:37 AM
--

DROP TABLE IF EXISTS 'tiki_content_templates_sections';

CREATE TABLE 'tiki_content_templates_sections' (
  "templateId" bigint NOT NULL default '0',
  "section" varchar(250) NOT NULL default '',
  PRIMARY KEY ("templateId","section")
) ENGINE=MyISAM;

-- ******************************************************

--
-- Table structure for table tiki_cookies
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 10, 2003 at 04:00 AM
--

DROP TABLE IF EXISTS 'tiki_cookies';

CREATE TABLE 'tiki_cookies' (
  "cookieId" INTEGER,
  "cookie" text,
  PRIMARY KEY ("cookieId")
) ENGINE=MyISAM ;

-- ******************************************************

--
-- Table structure for table tiki_copyrights
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_copyrights';

CREATE TABLE 'tiki_copyrights' (
  "copyrightId" INTEGER,
  "page" varchar(200) default NULL,
  "title" varchar(200) default NULL,
  "year" bigint default NULL,
  "authors" varchar(200) default NULL,
  "copyright_order" bigint default NULL,
  "userName" varchar(200) default '',
  PRIMARY KEY ("copyrightId")
) ENGINE=MyISAM ;

-- ******************************************************

--
-- Table structure for table tiki_directory_categories
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 08:59 PM
--

DROP TABLE IF EXISTS 'tiki_directory_categories';

CREATE TABLE 'tiki_directory_categories' (
  "categId" INTEGER,
  "parent" bigint default NULL,
  "name" varchar(240) default NULL,
  "description" text,
  "childrenType" char(1) default NULL,
  "sites" bigint default NULL,
  "viewableChildren" smallint default NULL,
  "allowSites" char(1) default NULL,
  "showCount" char(1) default NULL,
  "editorGroup" varchar(200) default NULL,
  "hits" bigint default NULL,
  PRIMARY KEY ("categId")
) ENGINE=MyISAM ;

-- ******************************************************

--
-- Table structure for table tiki_directory_search
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_directory_search';

CREATE TABLE 'tiki_directory_search' (
  "term" varchar(250) NOT NULL default '',
  "hits" bigint default NULL,
  PRIMARY KEY ("term")
) ENGINE=MyISAM;

-- ******************************************************

--
-- Table structure for table tiki_directory_sites
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 07:32 PM
--

DROP TABLE IF EXISTS 'tiki_directory_sites';

CREATE TABLE 'tiki_directory_sites' (
  "siteId" INTEGER,
  "name" varchar(240) default NULL,
  "description" text,
  "url" varchar(255) default NULL,
  "country" varchar(255) default NULL,
  "hits" bigint default NULL,
  "isValid" char(1) default NULL,
  "created" bigint default NULL,
  "lastModif" bigint default NULL,
  "cache" bytea,
  "cache_timestamp" bigint default NULL,
  PRIMARY KEY ("siteId")
  KEY (isValid),
  KEY (url)
) ENGINE=MyISAM ;

CREATE  INDEX "tiki_directory_sites_ft" ON "tiki_directory_sites"("name","description");
-- ******************************************************

--
-- Table structure for table tiki_drawings
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 08, 2003 at 05:02 AM
--

DROP TABLE IF EXISTS 'tiki_drawings';

CREATE TABLE 'tiki_drawings' (
  "drawId" INTEGER,
  "version" integer default NULL,
  "name" varchar(250) default NULL,
  "filename_draw" varchar(250) default NULL,
  "filename_pad" varchar(250) default NULL,
  "timestamp" bigint default NULL,
  "user" varchar(200) default '',
  PRIMARY KEY ("drawId")
) ENGINE=MyISAM ;

-- ******************************************************

--
-- Table structure for table tiki_dsn
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_dsn';

CREATE TABLE 'tiki_dsn' (
  "dsnId" INTEGER,
  "name" varchar(200) NOT NULL default '',
  "dsn" varchar(255) default NULL,
  PRIMARY KEY ("dsnId")
) ENGINE=MyISAM ;

-- ******************************************************


DROP TABLE IF EXISTS 'tiki_dynamic_variables';

CREATE TABLE 'tiki_dynamic_variables' (
  "name" varchar(40) NOT NULL,
  "data" text,
  PRIMARY KEY ("name")
);


-- ******************************************************
--
-- Table structure for table tiki_extwiki
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_extwiki';

CREATE TABLE 'tiki_extwiki' (
  "extwikiId" INTEGER,
  "name" varchar(200) NOT NULL default '',
  "extwiki" varchar(255) default NULL,
  PRIMARY KEY ("extwikiId")
) ENGINE=MyISAM ;

-- ******************************************************

--
-- Table structure for table tiki_faq_questions
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
-- Last check: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_faq_questions';

CREATE TABLE 'tiki_faq_questions' (
  "questionId" INTEGER,
  "faqId" bigint default NULL,
  "position" smallint default NULL,
  "question" text,
  "answer" text,
  PRIMARY KEY ("questionId")
) ENGINE=MyISAM ;

CREATE  INDEX "tiki_faq_questions_faqId" ON "tiki_faq_questions"("faqId");
CREATE  INDEX "tiki_faq_questions_question" ON "tiki_faq_questions"("question");
CREATE  INDEX "tiki_faq_questions_answer" ON "tiki_faq_questions"("answer");
CREATE  INDEX "tiki_faq_questions_ft" ON "tiki_faq_questions"("question","answer");
-- ******************************************************

--
-- Table structure for table tiki_faqs
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 09:09 PM
-- Last check: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_faqs';

CREATE TABLE 'tiki_faqs' (
  "faqId" INTEGER,
  "title" varchar(200) default NULL,
  "description" text,
  "created" bigint default NULL,
  "questions" integer default NULL,
  "hits" integer default NULL,
  "canSuggest" char(1) default NULL,
  PRIMARY KEY ("faqId")
) ENGINE=MyISAM ;

CREATE  INDEX "tiki_faqs_title" ON "tiki_faqs"("title");
CREATE  INDEX "tiki_faqs_description" ON "tiki_faqs"("description");
CREATE  INDEX "tiki_faqs_hits" ON "tiki_faqs"("hits");
CREATE  INDEX "tiki_faqs_ft" ON "tiki_faqs"("title","description");
-- ******************************************************

--
-- Table structure for table tiki_featured_links
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 11:08 PM
--

DROP TABLE IF EXISTS 'tiki_featured_links';

CREATE TABLE 'tiki_featured_links' (
  "url" varchar(200) NOT NULL default '',
  "title" varchar(200) default NULL,
  "description" text,
  "hits" integer default NULL,
  "position" integer default NULL,
  "type" char(1) default NULL,
  PRIMARY KEY ("url")
) ENGINE=MyISAM;

-- ******************************************************
-- Table structure for table tiki_file_galleries
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 13, 2003 at 01:13 AM
--

DROP TABLE IF EXISTS 'tiki_file_galleries';

CREATE TABLE 'tiki_file_galleries' (
  "galleryId" INTEGER,
  "name" varchar(80) NOT NULL default '',
  "type" varchar(20) NOT NULL default 'default',
  "description" text,
  "created" bigint default NULL,
  "visible" char(1) default NULL,
  "lastModif" bigint default NULL,
  "user" varchar(200) default '',
  "hits" bigint default NULL,
  "votes" integer default NULL,
  "points" decimal(8,2) default NULL,
  "maxRows" bigint default NULL,
  "public" char(1) default NULL,
  "show_id" char(1) default NULL,
  "show_icon" char(1) default NULL,
  "show_name" char(1) default NULL,
  "show_size" char(1) default NULL,
  "show_description" char(1) default NULL,
  "max_desc" integer default NULL,
  "show_created" char(1) default NULL,
  "show_dl" char(1) default NULL,
  "parentId" bigint NOT NULL default -1,
  "lockable" char(1) default 'n',
  "show_lockedby" char(1) default NULL,
  "archives" smallint default -1,
  "sort_mode" char(20) default NULL,
  "show_modified" char(1) default NULL,
  "show_author" char(1) default NULL,
  "show_creator" char(1) default NULL,
  "subgal_conf" varchar(200) default NULL,
  "show_last_user" char(1) default NULL,
  PRIMARY KEY ("galleryId")
) ENGINE=MyISAM ;

-- ******************************************************

--
-- Table structure for table tiki_files
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Nov 02, 2004 at 05:59 PM
-- Last check: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_files';

CREATE TABLE 'tiki_files' (
  "fileId" INTEGER,
  "galleryId" bigint NOT NULL default '0',
  "name" varchar(200) NOT NULL default '',
  "description" text,
  "created" bigint default NULL,
  "filename" varchar(80) default NULL,
  "filesize" bigint default NULL,
  "filetype" varchar(250) default NULL,
  "data" bytea,
  "user" varchar(200) default '',
  "author" varchar(40) default NULL,
  "downloads" bigint default NULL,
  "votes" integer default NULL,
  "points" decimal(8,2) default NULL,
  "path" varchar(255) default NULL,
  "reference_url" varchar(250) default NULL,
  "is_reference" char(1) default NULL,
  "hash" varchar(32) default NULL,
  "search_data" longtext,
  "lastModif" integer(14) DEFAULT NULL,
  "lastModifUser" varchar(200) DEFAULT NULL,
  "lockedby" varchar(200) default '',
  "comment" varchar(200) default NULL,
  "archiveId" bigint default 0,
  PRIMARY KEY ("fileId")
) ENGINE=MyISAM ;

CREATE  INDEX "tiki_files_name" ON "tiki_files"("name");
CREATE  INDEX "tiki_files_description" ON "tiki_files"("description");
CREATE  INDEX "tiki_files_downloads" ON "tiki_files"("downloads");
CREATE  INDEX "tiki_files_created" ON "tiki_files"("created");
CREATE  INDEX "tiki_files_archiveId" ON "tiki_files"("archiveId");
CREATE  INDEX "tiki_files_galleryId" ON "tiki_files"("galleryId");
CREATE  INDEX "tiki_files_ft" ON "tiki_files"("name","description","search_data");
-- ******************************************************

--
-- Table structure for table tiki_forum_attachments
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_forum_attachments';

CREATE TABLE 'tiki_forum_attachments' (
  "attId" INTEGER,
  "threadId" bigint NOT NULL default '0',
  "qId" bigint NOT NULL default '0',
  "forumId" bigint default NULL,
  "filename" varchar(250) default NULL,
  "filetype" varchar(250) default NULL,
  "filesize" bigint default NULL,
  "data" bytea,
  "dir" varchar(200) default NULL,
  "created" bigint default NULL,
  "path" varchar(250) default NULL,
  PRIMARY KEY ("attId")
) ENGINE=MyISAM ;

CREATE  INDEX "tiki_forum_attachments_threadId" ON "tiki_forum_attachments"("threadId");
-- ******************************************************

--
-- Table structure for table tiki_forum_reads
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 07:17 PM
--

DROP TABLE IF EXISTS 'tiki_forum_reads';

CREATE TABLE 'tiki_forum_reads' (
  "user" varchar(200) NOT NULL default '',
  "threadId" bigint NOT NULL default '0',
  "forumId" bigint default NULL,
  "timestamp" bigint default NULL,
  PRIMARY KEY ("user","threadId")
) ENGINE=MyISAM;

-- ******************************************************

--
-- Table structure for table tiki_forums
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 11:14 PM
--

DROP TABLE IF EXISTS 'tiki_forums';

CREATE TABLE 'tiki_forums' (
  "forumId" INTEGER,
  "name" varchar(255) default NULL,
  "description" text,
  "created" bigint default NULL,
  "lastPost" bigint default NULL,
  "threads" integer default NULL,
  "comments" integer default NULL,
  "controlFlood" char(1) default NULL,
  "floodInterval" integer default NULL,
  "moderator" varchar(200) default NULL,
  "hits" integer default NULL,
  "mail" varchar(200) default NULL,
  "useMail" char(1) default NULL,
  "section" varchar(200) default NULL,
  "usePruneUnreplied" char(1) default NULL,
  "pruneUnrepliedAge" integer default NULL,
  "usePruneOld" char(1) default NULL,
  "pruneMaxAge" integer default NULL,
  "topicsPerPage" integer default NULL,
  "topicOrdering" varchar(100) default NULL,
  "threadOrdering" varchar(100) default NULL,
  "att" varchar(80) default NULL,
  "att_store" varchar(4) default NULL,
  "att_store_dir" varchar(250) default NULL,
  "att_max_size" bigint default NULL,
  "ui_level" char(1) default NULL,
  "forum_password" varchar(32) default NULL,
  "forum_use_password" char(1) default NULL,
  "moderator_group" varchar(200) default NULL,
  "approval_type" varchar(20) default NULL,
  "outbound_address" varchar(250) default NULL,
  "outbound_mails_for_inbound_mails" char(1) default NULL,
  "outbound_mails_reply_link" char(1) default NULL,
  "outbound_from" varchar(250) default NULL,
  "inbound_pop_server" varchar(250) default NULL,
  "inbound_pop_port" smallint default NULL,
  "inbound_pop_user" varchar(200) default NULL,
  "inbound_pop_password" varchar(80) default NULL,
  "topic_smileys" char(1) default NULL,
  "ui_avatar" char(1) default NULL,
  "ui_flag" char(1) default NULL,
  "ui_posts" char(1) default NULL,
  "ui_email" char(1) default NULL,
  "ui_online" char(1) default NULL,
  "topic_summary" char(1) default NULL,
  "show_description" char(1) default NULL,
  "topics_list_replies" char(1) default NULL,
  "topics_list_reads" char(1) default NULL,
  "topics_list_pts" char(1) default NULL,
  "topics_list_lastpost" char(1) default NULL,
  "topics_list_author" char(1) default NULL,
  "vote_threads" char(1) default NULL,
  "forum_last_n" smallint default 0,
  "mandatory_contribution" char(1) default NULL,
  "threadStyle" varchar(100) default NULL,
  "commentsPerPage" varchar(100) default NULL,
  "is_flat" char(1) default NULL,
  PRIMARY KEY ("forumId")
) ENGINE=MyISAM ;

-- ******************************************************

--
-- Table structure for table tiki_forums_queue
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_forums_queue';

CREATE TABLE 'tiki_forums_queue' (
  "qId" INTEGER,
  "object" varchar(32) default NULL,
  "parentId" bigint default NULL,
  "forumId" bigint default NULL,
  "timestamp" bigint default NULL,
  "user" varchar(200) default '',
  "title" varchar(240) default NULL,
  "data" text,
  "type" varchar(60) default NULL,
  "hash" varchar(32) default NULL,
  "topic_smiley" varchar(80) default NULL,
  "topic_title" varchar(240) default NULL,
  "summary" varchar(240) default NULL,
  "in_reply_to" varchar(128) default NULL,
  PRIMARY KEY ("qId")
) ENGINE=MyISAM ;

-- ******************************************************

--
-- Table structure for table tiki_forums_reported
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_forums_reported';

CREATE TABLE 'tiki_forums_reported' (
  "threadId" bigint NOT NULL default '0',
  "forumId" bigint NOT NULL default '0',
  "parentId" bigint NOT NULL default '0',
  "user" varchar(200) default '',
  "timestamp" bigint default NULL,
  "reason" varchar(250) default NULL,
  PRIMARY KEY ("threadId")
) ENGINE=MyISAM;

-- ******************************************************

--
-- Table structure for table tiki_galleries
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Sep 18, 2004 at 11:56 PM
-- Last check: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_galleries';

CREATE TABLE 'tiki_galleries' (
  "galleryId" INTEGER,
  "name" varchar(80) NOT NULL default '',
  "description" text,
  "created" bigint default NULL,
  "lastModif" bigint default NULL,
  "visible" char(1) default NULL,
  "geographic" char(1) default NULL,
  "theme" varchar(60) default NULL,
  "user" varchar(200) default '',
  "hits" bigint default NULL,
  "maxRows" bigint default NULL,
  "rowImages" bigint default NULL,
  "thumbSizeX" bigint default NULL,
  "thumbSizeY" bigint default NULL,
  "public" char(1) default NULL,
  "sortorder" varchar(20) NOT NULL default 'created',
  "sortdirection" varchar(4) NOT NULL default 'desc',
  "galleryimage" varchar(20) NOT NULL default 'first',
  "parentgallery" bigint NOT NULL default -1,
  "showname" char(1) NOT NULL default 'y',
  "showimageid" char(1) NOT NULL default 'n',
  "showdescription" char(1) NOT NULL default 'n',
  "showcreated" char(1) NOT NULL default 'n',
  "showuser" char(1) NOT NULL default 'n',
  "showhits" char(1) NOT NULL default 'y',
  "showxysize" char(1) NOT NULL default 'y',
  "showfilesize" char(1) NOT NULL default 'n',
  "showfilename" char(1) NOT NULL default 'n',
  "defaultscale" varchar(10) NOT NULL DEFAULT 'o',
  PRIMARY KEY ("galleryId")
) ENGINE=MyISAM ;

CREATE  INDEX "tiki_galleries_name" ON "tiki_galleries"("name");
CREATE  INDEX "tiki_galleries_description" ON "tiki_galleries"("description");
CREATE  INDEX "tiki_galleries_hits" ON "tiki_galleries"("hits");
CREATE  INDEX "tiki_galleries_parentgallery" ON "tiki_galleries"("parentgallery");
CREATE  INDEX "tiki_galleries_visibleUser" ON "tiki_galleries"("visible" "user");
CREATE  INDEX "tiki_galleries_ft" ON "tiki_galleries"("name","description");
-- ******************************************************

--
-- Table structure for table tiki_galleries_scales
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_galleries_scales';

CREATE TABLE 'tiki_galleries_scales' (
  "galleryId" bigint NOT NULL default '0',
  "scale" bigint NOT NULL default '0',
  PRIMARY KEY ("galleryId","scale")
) ENGINE=MyISAM;

-- ******************************************************

--
-- Table structure for table tiki_games
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 05, 2003 at 08:23 PM
--

DROP TABLE IF EXISTS 'tiki_games';

CREATE TABLE 'tiki_games' (
  "gameName" varchar(200) NOT NULL default '',
  "hits" integer default NULL,
  "votes" integer default NULL,
  "points" integer default NULL,
  PRIMARY KEY ("gameName")
) ENGINE=MyISAM;

-- ******************************************************

--
-- Table structure for table tiki_group_inclusion
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 05, 2003 at 02:03 AM
--

DROP TABLE IF EXISTS 'tiki_group_inclusion';

CREATE TABLE 'tiki_group_inclusion' (
  "groupName" varchar(255) NOT NULL default '',
  "includeGroup" varchar(255) NOT NULL default '',
  PRIMARY KEY ("groupName","includeGroup")
) ENGINE=MyISAM;

-- ******************************************************

--
-- Table structure for table tiki_history
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Mar 30, 2005 at 10:21 PM
--

DROP TABLE IF EXISTS 'tiki_history';

CREATE TABLE 'tiki_history' (
  "historyId" INTEGER,
  "pageName" varchar(160) NOT NULL default '',
  "version" integer NOT NULL default '0',
  "version_minor" integer NOT NULL default '0',
  "lastModif" bigint default NULL,
  "description" varchar(200) default NULL,
  "user" varchar(200) not null default '',
  "ip" varchar(15) default NULL,
  "comment" varchar(200) default NULL,
  "data" bytea,
  "type" varchar(50) default NULL,
  PRIMARY KEY ("pageName","version")
  KEY(historyId)
) ENGINE=MyISAM;

-- ******************************************************

--
-- Table structure for table tiki_hotwords
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 10, 2003 at 11:04 PM
--

DROP TABLE IF EXISTS 'tiki_hotwords';

CREATE TABLE 'tiki_hotwords' (
  "word" varchar(40) NOT NULL default '',
  "url" varchar(255) NOT NULL default '',
  PRIMARY KEY ("word")
) ENGINE=MyISAM;

-- ******************************************************

--
-- Table structure for table tiki_html_pages
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_html_pages';

CREATE TABLE 'tiki_html_pages' (
  "pageName" varchar(200) NOT NULL default '',
  "content" bytea,
  "refresh" bigint default NULL,
  "type" char(1) default NULL,
  "created" bigint default NULL,
  PRIMARY KEY ("pageName")
) ENGINE=MyISAM;

-- ******************************************************

--
-- Table structure for table tiki_html_pages_dynamic_zones
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_html_pages_dynamic_zones';

CREATE TABLE 'tiki_html_pages_dynamic_zones' (
  "pageName" varchar(40) NOT NULL default '',
  "zone" varchar(80) NOT NULL default '',
  "type" char(2) default NULL,
  "content" text,
  PRIMARY KEY ("pageName","zone")
) ENGINE=MyISAM;

-- ******************************************************

--
-- Table structure for table tiki_images
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Sep 18, 2004 at 08:29 PM
-- Last check: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_images';

CREATE TABLE 'tiki_images' (
  "imageId" INTEGER,
  "galleryId" bigint NOT NULL default '0',
  "name" varchar(200) NOT NULL default '',
  "description" text,
  "lon" float default NULL,
  "lat" float default NULL,
  "created" bigint default NULL,
  "user" varchar(200) NOT NULL default '',
  "hits" bigint default NULL,
  "path" varchar(255) default NULL,
  PRIMARY KEY ("imageId")
) ENGINE=MyISAM ;

CREATE  INDEX "tiki_images_name" ON "tiki_images"("name");
CREATE  INDEX "tiki_images_description" ON "tiki_images"("description");
CREATE  INDEX "tiki_images_hits" ON "tiki_images"("hits");
CREATE  INDEX "tiki_images_ti_gId" ON "tiki_images"("galleryId");
CREATE  INDEX "tiki_images_ti_cr" ON "tiki_images"("created");
CREATE  INDEX "tiki_images_ti_us" ON "tiki_images"("user");
CREATE  INDEX "tiki_images_ft" ON "tiki_images"("name","description");
-- ******************************************************

--
-- Table structure for table tiki_images_data
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 12:49 PM
-- Last check: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_images_data';

CREATE TABLE 'tiki_images_data' (
  "imageId" bigint NOT NULL default '0',
  "xsize" integer NOT NULL default '0',
  "ysize" integer NOT NULL default '0',
  "type" char(1) NOT NULL default '',
  "filesize" bigint default NULL,
  "filetype" varchar(80) default NULL,
  "filename" varchar(80) default NULL,
  "data" bytea,
  "etag" varchar(32) default NULL,
  PRIMARY KEY ("imageId","xsize","ysize","type")
) ENGINE=MyISAM;

CREATE  INDEX "tiki_images_data_t_i_d_it" ON "tiki_images_data"("imageId","type");
-- ******************************************************

--
-- Table structure for table tiki_language
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_language';

CREATE TABLE 'tiki_language' (
  "source" bytea NOT NULL,
  "lang" char(16) NOT NULL default '',
  "tran" bytea,
  PRIMARY KEY ("source","lang")
) ENGINE=MyISAM;

-- ******************************************************

--
-- Table structure for table tiki_languages
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_languages';

CREATE TABLE 'tiki_languages' (
  "lang" char(16) NOT NULL default '',
  "language" varchar(255) default NULL,
  PRIMARY KEY ("lang")
) ENGINE=MyISAM;

-- ******************************************************
INSERT INTO "tiki_languages" ("lang","language") VALUES ('en','English');

-- ******************************************************

--
-- Table structure for table tiki_link_cache
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 06:06 PM
--

DROP TABLE IF EXISTS 'tiki_link_cache';

CREATE TABLE 'tiki_link_cache' (
  "cacheId" INTEGER,
  "url" varchar(250) default NULL,
  "data" bytea,
  "refresh" bigint default NULL,
  PRIMARY KEY ("cacheId")
) ENGINE=MyISAM ;

CREATE  INDEX "tiki_link_cache_url" ON "tiki_link_cache"("url");
CREATE INDEX urlindex ON tiki_link_cache (url(250));

-- ******************************************************

--
-- Table structure for table tiki_links
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 11:39 PM
--

DROP TABLE IF EXISTS 'tiki_links';

CREATE TABLE 'tiki_links' (
  "fromPage" varchar(160) NOT NULL default '',
  "toPage" varchar(160) NOT NULL default '',
  PRIMARY KEY ("fromPage","toPage")
) ENGINE=MyISAM;

CREATE  INDEX "tiki_links_toPage" ON "tiki_links"("toPage");
-- ******************************************************

--
-- Table structure for table tiki_live_support_events
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_live_support_events';

CREATE TABLE 'tiki_live_support_events' (
  "eventId" INTEGER,
  "reqId" varchar(32) NOT NULL default '',
  "type" varchar(40) default NULL,
  "seqId" bigint default NULL,
  "senderId" varchar(32) default NULL,
  "data" text,
  "timestamp" bigint default NULL,
  PRIMARY KEY ("eventId")
) ENGINE=MyISAM ;

-- ******************************************************

--
-- Table structure for table tiki_live_support_message_comments
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_live_support_message_comments';

CREATE TABLE 'tiki_live_support_message_comments' (
  "cId" INTEGER,
  "msgId" bigint default NULL,
  "data" text,
  "timestamp" bigint default NULL,
  PRIMARY KEY ("cId")
) ENGINE=MyISAM ;

-- ******************************************************

--
-- Table structure for table tiki_live_support_messages
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_live_support_messages';

CREATE TABLE 'tiki_live_support_messages' (
  "msgId" INTEGER,
  "data" text,
  "timestamp" bigint default NULL,
  "user" varchar(200) not null default '',
  "username" varchar(200) default NULL,
  "priority" smallint default NULL,
  "status" char(1) default NULL,
  "assigned_to" varchar(200) default NULL,
  "resolution" varchar(100) default NULL,
  "title" varchar(200) default NULL,
  "module" smallint default NULL,
  "email" varchar(250) default NULL,
  PRIMARY KEY ("msgId")
) ENGINE=MyISAM ;

-- ******************************************************

--
-- Table structure for table tiki_live_support_modules
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_live_support_modules';

CREATE TABLE 'tiki_live_support_modules' (
  "modId" INTEGER,
  "name" varchar(90) default NULL,
  PRIMARY KEY ("modId")
) ENGINE=MyISAM ;

-- ******************************************************
INSERT INTO "tiki_live_support_modules" ("name") VALUES ('wiki');

INSERT INTO "tiki_live_support_modules" ("name") VALUES ('forums');

INSERT INTO "tiki_live_support_modules" ("name") VALUES ('image galleries');

INSERT INTO "tiki_live_support_modules" ("name") VALUES ('file galleries');

INSERT INTO "tiki_live_support_modules" ("name") VALUES ('directory');

INSERT INTO "tiki_live_support_modules" ("name") VALUES ('workflow');

INSERT INTO "tiki_live_support_modules" ("name") VALUES ('charts');

-- ******************************************************

--
-- Table structure for table tiki_live_support_operators
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_live_support_operators';

CREATE TABLE 'tiki_live_support_operators' (
  "user" varchar(200) NOT NULL default '',
  "accepted_requests" bigint default NULL,
  "status" varchar(20) default NULL,
  "longest_chat" bigint default NULL,
  "shortest_chat" bigint default NULL,
  "average_chat" bigint default NULL,
  "last_chat" bigint default NULL,
  "time_online" bigint default NULL,
  "votes" bigint default NULL,
  "points" bigint default NULL,
  "status_since" bigint default NULL,
  PRIMARY KEY ("user")
) ENGINE=MyISAM;

-- ******************************************************

--
-- Table structure for table tiki_live_support_requests
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_live_support_requests';

CREATE TABLE 'tiki_live_support_requests' (
  "reqId" varchar(32) NOT NULL default '',
  "user" varchar(200) NOT NULL default '',
  "tiki_user" varchar(200) default NULL,
  "email" varchar(200) default NULL,
  "operator" varchar(200) default NULL,
  "operator_id" varchar(32) default NULL,
  "user_id" varchar(32) default NULL,
  "reason" text,
  "req_timestamp" bigint default NULL,
  "timestamp" bigint default NULL,
  "status" varchar(40) default NULL,
  "resolution" varchar(40) default NULL,
  "chat_started" bigint default NULL,
  "chat_ended" bigint default NULL,
  PRIMARY KEY ("reqId")
) ENGINE=MyISAM;

-- ******************************************************

--
-- Table structure for table tiki_logs
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_logs';

CREATE TABLE 'tiki_logs' (
  "logId" INTEGER,
  "logtype" varchar(20) NOT NULL,
  "logmessage" text NOT NULL,
  "loguser" varchar(40) NOT NULL,
  "logip" varchar(200),
  "logclient" text NOT NULL,
  "logtime" bigint NOT NULL,
  PRIMARY KEY ("logId")
) ENGINE=MyISAM;

CREATE  INDEX "tiki_logs_logtype" ON "tiki_logs"("logtype");

-- ******************************************************

--
-- Table structure for table tiki_mail_events
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 11, 2003 at 05:28 AM
--

DROP TABLE IF EXISTS 'tiki_mail_events';

CREATE TABLE 'tiki_mail_events' (
  "event" varchar(200) default NULL,
  "object" varchar(200) default NULL,
  "email" varchar(200) default NULL
) ENGINE=MyISAM;

-- ******************************************************

--
-- Table structure for table tiki_mailin_accounts
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jun 17, 2004 at 03:06 PM EST
--

DROP TABLE IF EXISTS 'tiki_mailin_accounts';

CREATE TABLE 'tiki_mailin_accounts' (
  "accountId" INTEGER,
  "user" varchar(200) NOT NULL default '',
  "account" varchar(50) NOT NULL default '',
  "pop" varchar(255) default NULL,
  "port" smallint default NULL,
  "username" varchar(100) default NULL,
  "pass" varchar(100) default NULL,
  "active" char(1) default NULL,
  "type" varchar(40) default NULL,
  "smtp" varchar(255) default NULL,
  "useAuth" char(1) default NULL,
  "smtpPort" smallint default NULL,
  "anonymous" char(1) NOT NULL default 'y',
  "attachments" char(1) NOT NULL default 'n',
  "article_topicId" smallint default NULL,
  "article_type" varchar(50) default NULL,
  "discard_after" varchar(255) default NULL,
  PRIMARY KEY ("accountId")
) ENGINE=MyISAM ;

-- ******************************************************

--
-- Table structure for table tiki_menu_languages
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_menu_languages';

CREATE TABLE 'tiki_menu_languages' (
  "menuId" INTEGER,
  "language" char(16) NOT NULL default '',
  PRIMARY KEY ("menuId","language")
) ENGINE=MyISAM ;

-- ******************************************************

--
-- Table structure for table tiki_menu_options
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Nov 21, 2003 at 07:05 AM
--

DROP TABLE IF EXISTS 'tiki_menu_options';

CREATE TABLE 'tiki_menu_options' (
  "optionId" INTEGER,
  "menuId" integer default NULL,
  "type" char(1) default NULL,
  "name" varchar(200) default NULL,
  "url" varchar(255) default NULL,
  "position" smallint default NULL,
  "section" varchar(255) default NULL,
  "perm" varchar(255) default NULL,
  "groupname" varchar(255) default NULL,
  "userlevel" smallint default 0,
  PRIMARY KEY ("optionId")
) ENGINE=MyISAM ;

CREATE UNIQUE INDEX "tiki_menu_options_uniq_menu" ON "tiki_menu_options"("menuId","name","url","position","section","perm","groupname");
-- ******************************************************
INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Home','./',10,'','','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Search','tiki-searchindex.php',13,'feature_search','','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Contact us','tiki-contact.php',20,'feature_contact','','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Stats','tiki-stats.php',23,'feature_stats','tiki_p_view_stats','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Categories','tiki-browse_categories.php',25,'feature_categories','tiki_p_view_categories','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Freetags','tiki-browse_freetags.php',27,'feature_freetags','tiki_p_view_freetags','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Games','tiki-list_games.php',30,'feature_games','tiki_p_play_games','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Calendar','tiki-calendar.php',35,'feature_calendar','tiki_p_view_calendar','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Tiki Calendar','tiki-action_calendar.php',36,'feature_action_calendar','tiki_p_view_tiki_calendar','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Users map','tiki-gmap_usermap.php',36,'feature_gmap','','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Mobile','tiki-mobile.php',37,'feature_mobile','','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','(debug)','javascript:toggle(\'debugconsole\')',40,'feature_debug_console','tiki_p_admin','');


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','MyTiki','tiki-my_tiki.php',50,'feature_mytiki','','Registered');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','MyTiki home','tiki-my_tiki.php',51,'feature_mytiki','','Registered');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Preferences','tiki-user_preferences.php',55,'feature_mytiki,feature_userPreferences','','Registered');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Messages','messu-mailbox.php',60,'feature_mytiki,feature_messages','tiki_p_messages','Registered');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Tasks','tiki-user_tasks.php',65,'feature_mytiki,feature_tasks','tiki_p_tasks','Registered');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Bookmarks','tiki-user_bookmarks.php',70,'feature_mytiki,feature_user_bookmarks','tiki_p_create_bookmarks','Registered');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Modules','tiki-user_assigned_modules.php',75,'feature_mytiki,user_assigned_modules','tiki_p_configure_modules','Registered');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Newsreader','tiki-newsreader_servers.php',80,'feature_mytiki,feature_newsreader','tiki_p_newsreader','Registered');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Webmail','tiki-webmail.php',85,'feature_mytiki,feature_webmail','tiki_p_use_webmail','Registered');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Contacts','tiki-contacts.php',87,'feature_mytiki,feature_contacts','','Registered');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Notepad','tiki-notepad_list.php',90,'feature_mytiki,feature_notepad','tiki_p_notepad','Registered');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','My files','tiki-userfiles.php',95,'feature_mytiki,feature_userfiles','tiki_p_userfiles','Registered');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','User menu','tiki-usermenu.php',100,'feature_mytiki,feature_usermenu','','Registered');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Mini calendar','tiki-minical.php',105,'feature_mytiki,feature_minical','','Registered');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','My watches','tiki-user_watches.php',110,'feature_mytiki,feature_user_watches','','Registered');


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','Workflow','tiki-g-user_processes.php',150,'feature_workflow','tiki_p_use_workflow','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','User processes','tiki-g-user_processes.php',152,'feature_workflow','tiki_p_use_workflow','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','User activities','tiki-g-user_activities.php',153,'feature_workflow','tiki_p_use_workflow','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','User instances','tiki-g-user_instances.php',154,'feature_workflow','tiki_p_use_workflow','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Admin processes','tiki-g-admin_processes.php',155,'feature_workflow','tiki_p_admin_workflow','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Monitor processes','tiki-g-monitor_processes.php',160,'feature_workflow','tiki_p_admin_workflow','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Monitor activities','tiki-g-monitor_activities.php',165,'feature_workflow','tiki_p_admin_workflow','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Monitor instances','tiki-g-monitor_instances.php',170,'feature_workflow','tiki_p_admin_workflow','');


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','Community','','187','feature_friends','tiki_p_list_users','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','User list','tiki-list_users.php','188','feature_friends','tiki_p_list_users','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Friendship Network','tiki-friends.php','189','feature_friends','','');


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','Wiki','tiki-index.php',200,'feature_wiki','tiki_p_view','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Wiki Home','tiki-index.php',202,'feature_wiki','tiki_p_view','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Last Changes','tiki-lastchanges.php',205,'feature_wiki,feature_lastChanges','tiki_p_view','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Dump','dump/new.tar',210,'feature_wiki,feature_dump','tiki_p_view','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Rankings','tiki-wiki_rankings.php',215,'feature_wiki,feature_wiki_rankings','tiki_p_view','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','List pages','tiki-listpages.php',220,'feature_wiki,feature_listPages','tiki_p_view','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Orphan pages','tiki-orphan_pages.php',225,'feature_wiki,feature_listPages','tiki_p_view','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Sandbox','tiki-editpage.php?page=sandbox',230,'feature_wiki,feature_sandbox','tiki_p_view','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Multiple Print','tiki-print_pages.php',235,'feature_wiki,feature_wiki_multiprint','tiki_p_view','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Send pages','tiki-send_objects.php',240,'feature_wiki,feature_comm','tiki_p_view,tiki_p_send_pages','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Received pages','tiki-received_pages.php',245,'feature_wiki,feature_comm','tiki_p_view,tiki_p_admin_received_pages','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Structures','tiki-admin_structures.php',250,'feature_wiki','tiki_p_view','');



INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','Image Galleries','tiki-galleries.php',300,'feature_galleries','tiki_p_view_image_gallery','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Galleries','tiki-galleries.php',305,'feature_galleries','tiki_p_view_image_gallery','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Rankings','tiki-galleries_rankings.php',310,'feature_galleries,feature_gal_rankings','tiki_p_view_image_gallery','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Upload image','tiki-upload_image.php',315,'feature_galleries','tiki_p_upload_images','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Directory batch','tiki-batch_upload.php',318,'feature_galleries,feature_gal_batch','tiki_p_batch_upload','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','System gallery','tiki-list_gallery.php?galleryId=0',320,'feature_galleries','tiki_p_admin_galleries','');


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','Articles','tiki-view_articles.php',350,'feature_articles','tiki_p_read_article','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Articles home','tiki-view_articles.php',355,'feature_articles','tiki_p_read_article','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','List articles','tiki-list_articles.php',360,'feature_articles','tiki_p_read_article','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Rankings','tiki-cms_rankings.php',365,'feature_articles,feature_cms_rankings','tiki_p_read_article','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Submit article','tiki-edit_submission.php',370,'feature_articles,feature_submissions','tiki_p_read_article,tiki_p_submit_article','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','View submissions','tiki-list_submissions.php',375,'feature_articles,feature_submissions','tiki_p_read_article,tiki_p_submit_article','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','View submissions','tiki-list_submissions.php',375,'feature_articles,feature_submissions','tiki_p_read_article,tiki_p_approve_submission','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','View submissions','tiki-list_submissions.php',375,'feature_articles,feature_submissions','tiki_p_read_article,tiki_p_remove_submission','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','New article','tiki-edit_article.php',380,'feature_articles','tiki_p_read_article,tiki_p_edit_article','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Send articles','tiki-send_objects.php',385,'feature_articles,feature_comm','tiki_p_read_article,tiki_p_send_articles','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Received articles','tiki-received_articles.php',385,'feature_articles,feature_comm','tiki_p_read_article,tiki_p_admin_received_articles','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Admin topics','tiki-admin_topics.php',390,'feature_articles','tiki_p_read_article,tiki_p_admin_cms','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Admin types','tiki-article_types.php',395,'feature_articles','tiki_p_read_article,tiki_p_admin_cms','');


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','Blogs','tiki-list_blogs.php',450,'feature_blogs','tiki_p_read_blog','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','List blogs','tiki-list_blogs.php',455,'feature_blogs','tiki_p_read_blog','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Rankings','tiki-blog_rankings.php',460,'feature_blogs,feature_blog_rankings','tiki_p_read_blog','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Create/Edit blog','tiki-edit_blog.php',465,'feature_blogs','tiki_p_read_blog,tiki_p_create_blogs','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Post','tiki-blog_post.php',470,'feature_blogs','tiki_p_read_blog,tiki_p_blog_post','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Admin posts','tiki-list_posts.php',475,'feature_blogs','tiki_p_read_blog,tiki_p_blog_admin','');


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','Forums','tiki-forums.php',500,'feature_forums','tiki_p_forum_read','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','List forums','tiki-forums.php',505,'feature_forums','tiki_p_forum_read','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Rankings','tiki-forum_rankings.php',510,'feature_forums,feature_forum_rankings','tiki_p_forum_read','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Admin forums','tiki-admin_forums.php',515,'feature_forums','tiki_p_forum_read,tiki_p_admin_forum','');


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','Directory','tiki-directory_browse.php',550,'feature_directory','tiki_p_view_directory','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Browse directory','tiki-directory_browse.php',552,'feature_directory','tiki_p_view_directory','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Submit a new link','tiki-directory_add_site.php',555,'feature_directory','tiki_p_submit_link','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Admin directory','tiki-directory_admin.php',565,'feature_directory','tiki_p_view_directory,tiki_p_admin_directory_cats','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Admin directory','tiki-directory_admin.php',565,'feature_directory','tiki_p_view_directory,tiki_p_admin_directory_sites','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Admin directory','tiki-directory_admin.php',565,'feature_directory','tiki_p_view_directory,tiki_p_validate_links','');


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','File Galleries','tiki-file_galleries.php',600,'feature_file_galleries','tiki_p_view_file_gallery','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','List galleries','tiki-file_galleries.php',605,'feature_file_galleries','tiki_p_list_file_galleries','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Rankings','tiki-file_galleries_rankings.php',610,'feature_file_galleries,feature_file_galleries_rankings','tiki_p_list_file_galleries','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Upload file','tiki-upload_file.php',615,'feature_file_galleries','tiki_p_view_file_gallery,tiki_p_upload_files','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Directory batch','tiki-batch_upload_files.php',617,'feature_file_galleries_batch','tiki_p_batch_upload_file_dir','');


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','FAQs','tiki-list_faqs.php',650,'feature_faqs','tiki_p_view_faqs','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','List FAQs','tiki-list_faqs.php',665,'feature_faqs','tiki_p_view_faqs','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Admin FAQs','tiki-list_faqs.php',660,'feature_faqs','tiki_p_admin_faqs','');


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','Maps','tiki-map.phtml',700,'feature_maps','tiki_p_map_view','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','View Maps','tiki-map.phtml',703,'feature_maps','tiki_p_map_view','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Mapfiles','tiki-map_edit.php',705,'feature_maps','tiki_p_map_view','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Layer management','tiki-map_upload.php',710,'feature_maps','tiki_p_map_edit','');


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','Quizzes','tiki-list_quizzes.php',750,'feature_quizzes','','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','List quizzes','tiki-list_quizzes.php',755,'feature_quizzes','','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Quiz stats','tiki-quiz_stats.php',760,'feature_quizzes','tiki_p_view_quiz_stats','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Admin quiz','tiki-edit_quiz.php',765,'feature_quizzes','tiki_p_admin_quizzes','');


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','TikiSheet','tiki-sheets.php',780,'feature_sheet','tiki_p_view_sheet','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','List TikiSheets','tiki-sheets.php',782,'feature_sheet','tiki_p_view_sheet','');


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','Trackers','tiki-list_trackers.php',800,'feature_trackers','tiki_p_list_trackers','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','List trackers','tiki-list_trackers.php',805,'feature_trackers','tiki_p_list_trackers','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Admin trackers','tiki-admin_trackers.php',810,'feature_trackers','tiki_p_admin_trackers','');


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','Surveys','tiki-list_surveys.php',850,'feature_surveys','','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','List surveys','tiki-list_surveys.php',855,'feature_surveys','','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Stats','tiki-survey_stats.php',860,'feature_surveys','tiki_p_view_survey_stats','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Admin surveys','tiki-admin_surveys.php',865,'feature_surveys','tiki_p_admin_surveys','');


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','Newsletters','tiki-newsletters.php',900,'feature_newsletters','tiki_p_subscribe_newsletters','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','Newsletters','tiki-newsletters.php',900,'feature_newsletters','tiki_p_send_newsletters','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','Newsletters','tiki-newsletters.php',900,'feature_newsletters','tiki_p_admin_newsletters','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Newsletters','tiki-newsletters.php',903,'feature_newsletters','tiki_p_subscribe_newsletters','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Newsletters','tiki-newsletters.php',903,'feature_newsletters','tiki_p_send_newsletters','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Newsletters','tiki-newsletters.php',903,'feature_newsletters','tiki_p_admin_newsletters','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Send newsletters','tiki-send_newsletters.php',905,'feature_newsletters','tiki_p_send_newsletters','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Admin newsletters','tiki-admin_newsletters.php',910,'feature_newsletters','tiki_p_admin_newsletters','');



INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','Charts','tiki-charts.php',1000,'feature_charts','','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Charts','tiki-charts.php',1003,'feature_charts','','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Admin charts','tiki-admin_charts.php',1005,'feature_charts','tiki_p_admin_charts','');


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'r','Admin','tiki-admin.php',1050,'','tiki_p_admin','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'r','Admin','tiki-admin.php',1050,'','tiki_p_admin_chat','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'r','Admin','tiki-admin.php',1050,'','tiki_p_admin_categories','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'r','Admin','tiki-admin.php',1050,'','tiki_p_admin_banners','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'r','Admin','tiki-admin.php',1050,'','tiki_p_edit_templates','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'r','Admin','tiki-admin.php',1050,'','tiki_p_edit_cookies','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'r','Admin','tiki-admin.php',1050,'','tiki_p_admin_dynamic','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'r','Admin','tiki-admin.php',1050,'','tiki_p_admin_mailin','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'r','Admin','tiki-admin.php',1050,'','tiki_p_edit_content_templates','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'r','Admin','tiki-admin.php',1050,'','tiki_p_edit_html_pages','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'r','Admin','tiki-admin.php',1050,'','tiki_p_view_referer_stats','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'r','Admin','tiki-admin.php',1050,'','tiki_p_admin_drawings','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'r','Admin','tiki-admin.php',1050,'','tiki_p_admin_quicktags','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'r','Admin','tiki-admin.php',1050,'','tiki_p_admin_shoutbox','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'r','Admin','tiki-admin.php',1050,'','tiki_p_live_support_admin','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'r','Admin','tiki-admin.php',1050,'','user_is_operator','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'r','Admin','tiki-admin.php',1050,'feature_integrator','tiki_p_admin_integrator','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'r','Admin','tiki-admin.php',1050,'','tiki_p_admin_contribution','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'r','Admin','tiki-admin.php',1050,'','tiki_p_admin_users','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'r','Admin','tiki-admin.php',1050,'','tiki_p_edit_menu','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Admin home','tiki-admin.php',1051,'','tiki_p_admin','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Live support','tiki-live_support_admin.php',1055,'feature_live_support','tiki_p_live_support_admin','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Live support','tiki-live_support_admin.php',1055,'feature_live_support','user_is_operator','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Banning','tiki-admin_banning.php',1060,'feature_banning','tiki_p_admin_banning','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Calendar','tiki-admin_calendars.php',1065,'feature_calendar','tiki_p_admin_calendar','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Users','tiki-adminusers.php',1070,'','tiki_p_admin_users','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Groups','tiki-admingroups.php',1075,'','tiki_p_admin','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Cache','tiki-list_cache.php',1080,'','tiki_p_admin','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Modules','tiki-admin_modules.php',1085,'','tiki_p_admin','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Links','tiki-admin_links.php',1090,'feature_featuredLinks','tiki_p_admin','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Hotwords','tiki-admin_hotwords.php',1095,'feature_hotwords','tiki_p_admin','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','RSS modules','tiki-admin_rssmodules.php',1100,'','tiki_p_admin','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Menus','tiki-admin_menus.php',1105,'','tiki_p_edit_menu','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Polls','tiki-admin_polls.php',1110,'feature_polls','tiki_p_admin','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Mail notifications','tiki-admin_notifications.php',1120,'','tiki_p_admin','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Search stats','tiki-search_stats.php',1125,'feature_search_stats','tiki_p_admin','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Theme control','tiki-theme_control.php',1130,'feature_theme_control','tiki_p_admin','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','QuickTags','tiki-admin_quicktags.php',1135,'','tiki_p_admin,tiki_p_admin_quicktags','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Categories','tiki-admin_categories.php',1145,'feature_categories','tiki_p_admin_categories','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Banners','tiki-list_banners.php',1150,'feature_banners','tiki_p_admin_banners','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Edit templates','tiki-edit_templates.php',1155,'feature_edit_templates','tiki_p_edit_templates','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Drawings','tiki-admin_drawings.php',1160,'feature_drawings','tiki_p_admin_drawings','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Dynamic content','tiki-list_contents.php',1165,'feature_dynamic_content','tiki_p_admin_dynamic','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Cookies','tiki-admin_cookies.php',1170,'','tiki_p_edit_cookies','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Mail-in','tiki-admin_mailin.php',1175,'feature_mailin','tiki_p_admin_mailin','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Content templates','tiki-admin_content_templates.php',1180,'','tiki_p_edit_content_templates','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','HTML pages','tiki-admin_html_pages.php',1185,'feature_html_pages','tiki_p_edit_html_pages','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Shoutbox','tiki-shoutbox.php',1190,'feature_shoutbox','tiki_p_admin_shoutbox','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Shoutbox Words','tiki-admin_shoutbox_words.php',1191,'feature_shoutbox','tiki_p_admin_shoutbox','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Referer stats','tiki-referer_stats.php',1195,'feature_referer_stats','tiki_p_view_referer_stats','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Edit languages','tiki-edit_languages.php',1200,'lang_use_db','tiki_p_edit_languages','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Integrator','tiki-admin_integrator.php',1205,'feature_integrator','tiki_p_admin_integrator','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','phpinfo','tiki-phpinfo.php',1215,'','tiki_p_admin','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','DSN','tiki-admin_dsn.php',1220,'','tiki_p_admin','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','External wikis','tiki-admin_external_wikis.php',1225,'','tiki_p_admin','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','System Admin','tiki-admin_system.php',1230,'','tiki_p_admin','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Score','tiki-admin_score.php',1235,'feature_score','tiki_p_admin','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Admin mods','tiki-mods.php',1240,'','tiki_p_admin','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Tiki Logs','tiki-syslog.php',1245,'','tiki_p_admin','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Security Admin','tiki-admin_security.php',1250,'','tiki_p_admin','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Action Log','tiki-admin_actionlog.php',1255,'feature_actionlog','tiki_p_admin','');


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Comments','tiki-list_comments.php',1260,'feature_wiki_comments','tiki_p_admin','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Comments','tiki-list_comments.php',1260,'feature_article_comments','tiki_p_admin','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Comments','tiki-list_comments.php',1260,'feature_blog_comments','tiki_p_admin','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Comments','tiki-list_comments.php',1260,'feature_file_galleries_comments','tiki_p_admin','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Comments','tiki-list_comments.php',1260,'feature_image_galleries_comments','tiki_p_admin','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Comments','tiki-list_comments.php',1260,'feature_poll_comments','tiki_p_admin','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Comments','tiki-list_comments.php',1260,'feature_faq_comments','tiki_p_admin','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Contribution','tiki-admin_contribution.php',1265,'feature_contribution','tiki_p_admin_contribution','');

-- ******************************************************

--
-- Table structure for table tiki_menus
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_menus';

CREATE TABLE 'tiki_menus' (
  "menuId" INTEGER,
  "name" varchar(200) NOT NULL default '',
  "description" text,
  "type" char(1) default NULL,
  PRIMARY KEY ("menuId")
) ENGINE=MyISAM ;

-- ******************************************************
INSERT INTO "tiki_menus" ("menuId","name","description","type") VALUES ('42','Application menu','Main extensive navigation menu','d');

-- ******************************************************

--
-- Table structure for table tiki_minical_events
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 09, 2003 at 04:06 AM
--

DROP TABLE IF EXISTS 'tiki_minical_events';

CREATE TABLE 'tiki_minical_events' (
  "user" varchar(200) default '',
  "eventId" INTEGER,
  "title" varchar(250) default NULL,
  "description" text,
  "start" bigint default NULL,
  "end" bigint default NULL,
  "security" char(1) default NULL,
  "duration" smallint default NULL,
  "topicId" bigint default NULL,
  "reminded" char(1) default NULL,
  PRIMARY KEY ("eventId")
) ENGINE=MyISAM ;

-- ******************************************************

--
-- Table structure for table tiki_minical_topics
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_minical_topics';

CREATE TABLE 'tiki_minical_topics' (
  "user" varchar(200) default '',
  "topicId" INTEGER,
  "name" varchar(250) default NULL,
  "filename" varchar(200) default NULL,
  "filetype" varchar(200) default NULL,
  "filesize" varchar(200) default NULL,
  "data" bytea,
  "path" varchar(250) default NULL,
  "isIcon" char(1) default NULL,
  PRIMARY KEY ("topicId")
) ENGINE=MyISAM ;

-- ******************************************************

--
-- Table structure for table tiki_modules
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 11:44 PM
--

DROP TABLE IF EXISTS 'tiki_modules';

CREATE TABLE 'tiki_modules' (
  "moduleId" INTEGER,
  "name" varchar(200) NOT NULL default '',
  "position" char(1) default NULL,
  "ord" smallint default NULL,
  "type" char(1) default NULL,
  "title" varchar(255) default NULL,
  "cache_time" bigint default NULL,
  "rows" smallint default NULL,
  "params" varchar(255) default NULL,
  "groups" text,
  PRIMARY KEY ("name","position","ord","params")
) ENGINE=MyISAM;

CREATE  INDEX "tiki_modules_positionType" ON "tiki_modules"("position" "type");
CREATE  INDEX "tiki_modules_moduleId" ON "tiki_modules"("moduleId");
-- ******************************************************
INSERT INTO "tiki_modules" ("name","position","ord","cache_time","groups") VALUES ('login_box','r',1,0,'a:2:{i:0;s:10:"Registered";i:1;s:9:"Anonymous";}');

INSERT INTO "tiki_modules" ("name","position","ord","cache_time","params","groups") VALUES ('mnu_application_menu','l',1,0,'flip=y','a:2:{i:0;s:10:"Registered";i:1;s:9:"Anonymous";}');

INSERT INTO "tiki_modules" ("name","position","ord","cache_time","groups") VALUES ('quick_edit','l',2,0,'a:1:{i:0;s:10:"Registered";}');

INSERT INTO "tiki_modules" ("name","position","ord","cache_time","groups") VALUES ('assistant','l',10,0,'a:2:{i:0;s:10:"Registered";i:1;s:9:"Anonymous";}');

-- ******************************************************

--
-- Table structure for table tiki_newsletter_subscriptions
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_newsletter_subscriptions';

CREATE TABLE 'tiki_newsletter_subscriptions' (
  "nlId" bigint NOT NULL default '0',
  "email" varchar(255) NOT NULL default '',
  "code" varchar(32) default NULL,
  "valid" char(1) default NULL,
  "subscribed" bigint default NULL,
  "isUser" char(1) NOT NULL default 'n',
  PRIMARY KEY ("nlId","email","isUser")
) ENGINE=MyISAM;

-- ******************************************************

--
-- Table structure for table tiki_newsletter_groups
--
-- Creation: Jan 18, 2005
-- Last update: Jan 18, 2005
--

DROP TABLE IF EXISTS 'tiki_newsletter_groups';

CREATE TABLE 'tiki_newsletter_groups' (
  "nlId" bigint NOT NULL default '0',
  "groupName" varchar(255) NOT NULL default '',
  "code" varchar(32) default NULL,
  PRIMARY KEY ("nlId","groupName")
) ENGINE=MyISAM;

-- ******************************************************

--
-- Table structure for table tiki_newsletter_included
--
-- Creation: Sep 25, 2007
-- Last update: Sep 25, 2007
--

DROP TABLE IF EXISTS 'tiki_newsletter_included';

CREATE TABLE 'tiki_newsletter_included' (
  "nlId" bigint NOT NULL default '0',
  "includedId" bigint NOT NULL default '0',
  PRIMARY KEY ("nlId","includedId")
) ENGINE=MyISAM;

-- ******************************************************

--
-- Table structure for table tiki_newsletters
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_newsletters';

CREATE TABLE 'tiki_newsletters' (
  "nlId" INTEGER,
  "name" varchar(200) default NULL,
  "description" text,
  "created" bigint default NULL,
  "lastSent" bigint default NULL,
  "editions" bigint default NULL,
  "users" bigint default NULL,
  "allowUserSub" char(1) default 'y',
  "allowTxt" char(1) default 'y',
  "allowAnySub" char(1) default NULL,
  "unsubMsg" char(1) default 'y',
  "validateAddr" char(1) default 'y',
  "frequency" bigint default NULL,
  "author" varchar(200) default NULL,
  PRIMARY KEY ("nlId")
) ENGINE=MyISAM ;

-- ******************************************************

--
-- Table structure for table tiki_newsreader_marks
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_newsreader_marks';

CREATE TABLE 'tiki_newsreader_marks' (
  "user" varchar(200) NOT NULL default '',
  "serverId" bigint NOT NULL default '0',
  "groupName" varchar(255) NOT NULL default '',
  "timestamp" bigint NOT NULL default '0',
  PRIMARY KEY ("user","serverId","groupName")
) ENGINE=MyISAM;

-- ******************************************************

--
-- Table structure for table tiki_newsreader_servers
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_newsreader_servers';

CREATE TABLE 'tiki_newsreader_servers' (
  "user" varchar(200) NOT NULL default '',
  "serverId" INTEGER,
  "server" varchar(250) default NULL,
  "port" smallint default NULL,
  "username" varchar(200) default NULL,
  "password" varchar(200) default NULL,
  PRIMARY KEY ("serverId")
) ENGINE=MyISAM ;

-- ******************************************************

--
-- Table structure for table tiki_page_footnotes
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 10:00 AM
-- Last check: Jul 12, 2003 at 10:00 AM
--

DROP TABLE IF EXISTS 'tiki_page_footnotes';

CREATE TABLE 'tiki_page_footnotes' (
  "user" varchar(200) NOT NULL default '',
  "pageName" varchar(250) NOT NULL default '',
  "data" text,
  PRIMARY KEY ("user","pageName")
) ENGINE=MyISAM;

-- ******************************************************

--
-- Table structure for table tiki_pages
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 13, 2003 at 01:52 AM
-- Last check: Jul 12, 2003 at 10:01 AM
--

DROP TABLE IF EXISTS 'tiki_pages';

CREATE TABLE 'tiki_pages' (
  "page_id" INTEGER,
  "pageName" varchar(160) NOT NULL default '',
  "hits" integer default NULL,
  "data" mediumtext,
  "description" varchar(200) default NULL,
  "lastModif" bigint default NULL,
  "comment" varchar(200) default NULL,
  "version" integer NOT NULL default '0',
  "user" varchar(200) default '',
  "ip" varchar(15) default NULL,
  "flag" char(1) default NULL,
  "points" integer default NULL,
  "votes" integer default NULL,
  "cache" longtext,
  "wiki_cache" bigint default NULL,
  "cache_timestamp" bigint default NULL,
  "pageRank" decimal(4,3) default NULL,
  "creator" varchar(200) default NULL,
  "page_size" bigint unsigned default '0',
  "lang" varchar(16) default NULL,
  "lockedby" varchar(200) default NULL,
  "is_html" smallint default 0,
  "created" bigint,
  PRIMARY KEY ("page_id")
  KEY lastModif(lastModif)
) ENGINE=MyISAM;

CREATE  INDEX "tiki_pages_data" ON "tiki_pages"("data");
CREATE  INDEX "tiki_pages_pageRank" ON "tiki_pages"("pageRank");
CREATE  INDEX "tiki_pages_ft" ON "tiki_pages"("pageName","description","data");
CREATE UNIQUE INDEX "tiki_pages_pageName" ON "tiki_pages"("pageName");
-- ******************************************************

--
-- Table structure for table tiki_page_drafts
--
-- Creation: March 12, 2006 at 
--

DROP TABLE IF EXISTS 'tiki_page_drafts';

CREATE TABLE 'tiki_page_drafts' (
  "user" varchar(200) default '',
  "pageName" varchar(255) NOT NULL,
  "data" mediumtext,
  "description" varchar(200) default NULL,
  "comment" varchar(200) default NULL,
  "lastModif" bigint default NULL,
  PRIMARY KEY ("pageName","user")
) ENGINE=MyISAM;

-- ******************************************************

--
-- Table structure for table tiki_pageviews
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 13, 2003 at 01:52 AM
--

DROP TABLE IF EXISTS 'tiki_pageviews';

CREATE TABLE 'tiki_pageviews' (
  "day" bigint NOT NULL default '0',
  "pageviews" bigint default NULL,
  PRIMARY KEY ("day")
) ENGINE=MyISAM;

-- ******************************************************

--
-- Table structure for table tiki_poll_objects
--

DROP TABLE IF EXISTS 'tiki_poll_objects';

CREATE TABLE tiki_poll_objects (
  catObjectId bigint NOT NULL default '0',
  pollId bigint NOT NULL default '0',
  title varchar(255) default NULL,
  PRIMARY KEY ("catObjectId","pollId")
) ENGINE=MyISAM;

-- ******************************************************

--
-- Table structure for table tiki_poll_options
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 06, 2003 at 07:57 PM
--

DROP TABLE IF EXISTS 'tiki_poll_options';

CREATE TABLE 'tiki_poll_options' (
  "pollId" integer NOT NULL default '0',
  "optionId" INTEGER,
  "title" varchar(200) default NULL,
  "position" smallint NOT NULL default '0',
  "votes" integer default NULL,
  PRIMARY KEY ("optionId")
) ENGINE=MyISAM ;

-- ******************************************************

--
-- Table structure for table tiki_polls
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 06, 2003 at 07:57 PM
--

DROP TABLE IF EXISTS 'tiki_polls';

CREATE TABLE 'tiki_polls' (
  "pollId" INTEGER,
  "title" varchar(200) default NULL,
  "votes" integer default NULL,
  "active" char(1) default NULL,
  "publishDate" bigint default NULL,
  PRIMARY KEY ("pollId")
) ENGINE=MyISAM ;

-- ******************************************************

--
-- Table structure for table tiki_preferences
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 12:04 PM
--

DROP TABLE IF EXISTS 'tiki_preferences';

CREATE TABLE 'tiki_preferences' (
  "name" varchar(40) NOT NULL default '',
  "value" text,
  PRIMARY KEY ("name")
) ENGINE=MyISAM;

INSERT INTO "," ("name","value") VALUES ('pref_syntax', '1.10');

-- ******************************************************

--
-- Table structure for table tiki_private_messages
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_private_messages';

CREATE TABLE 'tiki_private_messages' (
  "messageId" INTEGER,
  "toNickname" varchar(200) NOT NULL default '',
  "message" varchar(255) default NULL,
  "poster" varchar(200) NOT NULL default 'anonymous',
  "timestamp" bigint default NULL,
  "received" smallint not null default 0,
  "key"(received),
  "key"(timestamp),
  PRIMARY KEY ("messageId")
) ENGINE=MyISAM ;

-- ******************************************************

--
-- Table structure for table tiki_programmed_content
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_programmed_content';

CREATE TABLE 'tiki_programmed_content' (
  "pId" INTEGER,
  "contentId" integer NOT NULL default '0',
  "publishDate" bigint NOT NULL default '0',
  "data" text,
  PRIMARY KEY ("pId")
) ENGINE=MyISAM ;

-- ******************************************************

--
-- Table structure for table tiki_quiz_question_options
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_quiz_question_options';

CREATE TABLE 'tiki_quiz_question_options' (
  "optionId" INTEGER,
  "questionId" bigint default NULL,
  "optionText" text,
  "points" smallint default NULL,
  PRIMARY KEY ("optionId")
) ENGINE=MyISAM ;

-- ******************************************************

--
-- Table structure for table tiki_quiz_questions
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_quiz_questions';

CREATE TABLE 'tiki_quiz_questions' (
  "questionId" INTEGER,
  "quizId" bigint default NULL,
  "question" text,
  "position" smallint default NULL,
  "type" char(1) default NULL,
  "maxPoints" smallint default NULL,
  PRIMARY KEY ("questionId")
) ENGINE=MyISAM ;

-- ******************************************************

--
-- Table structure for table tiki_quiz_results
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_quiz_results';

CREATE TABLE 'tiki_quiz_results' (
  "resultId" INTEGER,
  "quizId" bigint default NULL,
  "fromPoints" smallint default NULL,
  "toPoints" smallint default NULL,
  "answer" text,
  PRIMARY KEY ("resultId")
) ENGINE=MyISAM ;

-- ******************************************************

--
-- Table structure for table tiki_quiz_stats
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_quiz_stats';

CREATE TABLE 'tiki_quiz_stats' (
  "quizId" bigint NOT NULL default '0',
  "questionId" bigint NOT NULL default '0',
  "optionId" bigint NOT NULL default '0',
  "votes" bigint default NULL,
  PRIMARY KEY ("quizId","questionId","optionId")
) ENGINE=MyISAM;

-- ******************************************************

--
-- Table structure for table tiki_quiz_stats_sum
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_quiz_stats_sum';

CREATE TABLE 'tiki_quiz_stats_sum' (
  "quizId" bigint NOT NULL default '0',
  "quizName" varchar(255) default NULL,
  "timesTaken" bigint default NULL,
  "avgpoints" decimal(5,2) default NULL,
  "avgavg" decimal(5,2) default NULL,
  "avgtime" decimal(5,2) default NULL,
  PRIMARY KEY ("quizId")
) ENGINE=MyISAM;

-- ******************************************************

--
-- Table structure for table tiki_quizzes
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: April 29, 2004
--

DROP TABLE IF EXISTS 'tiki_quizzes';

CREATE TABLE 'tiki_quizzes' (
  "quizId" INTEGER,
  "name" varchar(255) default NULL,
  "description" text,
  "canRepeat" char(1) default NULL,
  "storeResults" char(1) default NULL,
  "questionsPerPage" smallint default NULL,
  "timeLimited" char(1) default NULL,
  "timeLimit" bigint default NULL,
  "created" bigint default NULL,
  "taken" bigint default NULL,
  "immediateFeedback" char(1) default NULL,
  "showAnswers" char(1) default NULL,
  "shuffleQuestions" char(1) default NULL,
  "shuffleAnswers" char(1) default NULL,
  "publishDate" bigint default NULL,
  "expireDate" bigint default NULL,
  "bDeleted" char(1) default NULL,
  "nVersion" smallint NOT NULL,
  "nAuthor" smallint default NULL,
  "bOnline" char(1) default NULL,
  "bRandomQuestions" char(1) default NULL,
  "nRandomQuestions" smallint default NULL,
  "bLimitQuestionsPerPage" char(1) default NULL,
  "nLimitQuestionsPerPage" smallint default NULL,
  "bMultiSession" char(1) default NULL,
  "nCanRepeat" smallint default NULL,
  "sGradingMethod" varchar(80) default NULL,
  "sShowScore" varchar(80) default NULL,
  "sShowCorrectAnswers" varchar(80) default NULL,
  "sPublishStats" varchar(80) default NULL,
  "bAdditionalQuestions" char(1) default NULL,
  "bForum" char(1) default NULL,
  "sForum" varchar(80) default NULL,
  "sPrologue" text,
  "sData" text,
  "sEpilogue" text,
  "passingperct" smallint default 0,
  PRIMARY KEY ("quizId","nVersion")
) ENGINE=MyISAM ;

-- ******************************************************

--
-- Table structure for table tiki_received_articles
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_received_articles';

CREATE TABLE 'tiki_received_articles' (
  "receivedArticleId" INTEGER,
  "receivedFromSite" varchar(200) default NULL,
  "receivedFromUser" varchar(200) default NULL,
  "receivedDate" bigint default NULL,
  "title" varchar(80) default NULL,
  "authorName" varchar(60) default NULL,
  "size" bigint default NULL,
  "useImage" char(1) default NULL,
  "image_name" varchar(80) default NULL,
  "image_type" varchar(80) default NULL,
  "image_size" bigint default NULL,
  "image_x" smallint default NULL,
  "image_y" smallint default NULL,
  "image_data" bytea,
  "publishDate" bigint default NULL,
  "expireDate" bigint default NULL,
  "created" bigint default NULL,
  "heading" text,
  "body" bytea,
  "hash" varchar(32) default NULL,
  "author" varchar(200) default NULL,
  "type" varchar(50) default NULL,
  "rating" decimal(3,2) default NULL,
  PRIMARY KEY ("receivedArticleId")
) ENGINE=MyISAM ;

-- ******************************************************

--
-- Table structure for table tiki_received_pages
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 09, 2003 at 03:56 AM
--

DROP TABLE IF EXISTS 'tiki_received_pages';

CREATE TABLE 'tiki_received_pages' (
  "receivedPageId" INTEGER,
  "pageName" varchar(160) NOT NULL default '',
  "data" bytea,
  "description" varchar(200) default NULL,
  "comment" varchar(200) default NULL,
  "receivedFromSite" varchar(200) default NULL,
  "receivedFromUser" varchar(200) default NULL,
  "receivedDate" bigint default NULL,
  "structureName"  varchar(250) default NULL,
  "parentName"  varchar(250) default NULL,
  "page_alias" varchar(250) default '',
  "pos" smallint default NULL,
  PRIMARY KEY ("receivedPageId")
) ENGINE=MyISAM ;

CREATE  INDEX "tiki_received_pages_structureName" ON "tiki_received_pages"("structureName");
-- ******************************************************

--
-- Table structure for table tiki_referer_stats
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 13, 2003 at 01:30 AM
--

DROP TABLE IF EXISTS 'tiki_referer_stats';

CREATE TABLE 'tiki_referer_stats' (
  "referer" varchar(255) NOT NULL default '',
  "hits" bigint default NULL,
  "last" bigint default NULL,
  PRIMARY KEY ("referer")
) ENGINE=MyISAM;

-- ******************************************************

--
-- Table structure for table tiki_related_categories
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_related_categories';

CREATE TABLE 'tiki_related_categories' (
  "categId" bigint NOT NULL default '0',
  "relatedTo" bigint NOT NULL default '0',
  PRIMARY KEY ("categId","relatedTo")
) ENGINE=MyISAM;

-- ******************************************************

--
-- Table structure for table tiki_rss_modules
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 10:19 AM
--

DROP TABLE IF EXISTS 'tiki_rss_modules';

CREATE TABLE 'tiki_rss_modules' (
  "rssId" INTEGER,
  "name" varchar(30) NOT NULL default '',
  "description" text,
  "url" varchar(255) NOT NULL default '',
  "refresh" integer default NULL,
  "lastUpdated" bigint default NULL,
  "showTitle" char(1) default 'n',
  "showPubDate" char(1) default 'n',
  "content" bytea,
  PRIMARY KEY ("rssId")
) ENGINE=MyISAM ;

CREATE  INDEX "tiki_rss_modules_name" ON "tiki_rss_modules"("name");
-- ******************************************************

--
-- Table structure for table tiki_rss_feeds
--
-- Creation: Oct 14, 2003 at 20:34 PM
-- Last update: Oct 14, 2003 at 20:34 PM
--

DROP TABLE IF EXISTS 'tiki_rss_feeds';

CREATE TABLE 'tiki_rss_feeds' (
  "name" varchar(30) NOT NULL default '',
  "rssVer" char(1) NOT NULL default '1',
  "refresh" integer default '300',
  "lastUpdated" bigint default NULL,
  "cache" bytea,
  PRIMARY KEY ("name","rssVer")
) ENGINE=MyISAM;

-- ******************************************************

DROP TABLE IF EXISTS 'tiki_searchindex';

CREATE TABLE 'tiki_searchindex'(
  "searchword" varchar(80) NOT NULL default '',
  "location" varchar(80) NOT NULL default '',
  "page" varchar(255) NOT NULL default '',
  "count" bigint NOT NULL default '1',
  "last_update" bigint NOT NULL default '0',
  PRIMARY KEY ("searchword","location","page")
) ENGINE=MyISAM;

CREATE  INDEX "tiki_searchindex_last_update" ON "tiki_searchindex"("last_update");
CREATE  INDEX "tiki_searchindex_location" ON "tiki_searchindex"("location" "page");

-- LRU (last recently used) list for searching parts of words
DROP TABLE IF EXISTS 'tiki_searchsyllable';

CREATE TABLE 'tiki_searchsyllable'(
  "syllable" varchar(80) NOT NULL default '',
  "lastUsed" bigint NOT NULL default '0',
  "lastUpdated" bigint NOT NULL default '0',
  PRIMARY KEY ("syllable")
) ENGINE=MyISAM;

CREATE  INDEX "tiki_searchsyllable_lastUsed" ON "tiki_searchsyllable"("lastUsed");

-- searchword caching table for search syllables
DROP TABLE IF EXISTS 'tiki_searchwords';

CREATE TABLE 'tiki_searchwords'(
  "syllable" varchar(80) NOT NULL default '',
  "searchword" varchar(80) NOT NULL default '',
  PRIMARY KEY ("syllable","searchword")
) ENGINE=MyISAM;


--
-- Table structure for table tiki_search_stats
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 10:55 PM
--

DROP TABLE IF EXISTS 'tiki_search_stats';

CREATE TABLE 'tiki_search_stats' (
  "term" varchar(50) NOT NULL default '',
  "hits" bigint default NULL,
  PRIMARY KEY ("term")
) ENGINE=MyISAM;

-- ******************************************************

--
-- Table structure for table tiki_secdb
--
--

DROP TABLE IF EXISTS 'tiki_secdb';

CREATE TABLE 'tiki_secdb'(
  "md5_value" varchar(32) NOT NULL,
  "filename" varchar(250) NOT NULL,
  "tiki_version" varchar(60) NOT NULL,
  "severity" smallint NOT NULL default '0',
  PRIMARY KEY ("md5_value","filename","tiki_version")
) ENGINE=MyISAM;

CREATE  INDEX "tiki_secdb_sdb_fn" ON "tiki_secdb"("filename");

--
-- Table structure for table tiki_semaphores
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 13, 2003 at 01:52 AM
--

DROP TABLE IF EXISTS 'tiki_semaphores';

CREATE TABLE 'tiki_semaphores' (
  "semName" varchar(250) NOT NULL default '',
  "objectType" varchar(20) default 'wiki page',
  "user" varchar(200) default NULL,
  "timestamp" bigint default NULL,
  PRIMARY KEY ("semName")
) ENGINE=MyISAM;

-- ******************************************************

--
-- Table structure for table tiki_sent_newsletters
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_sent_newsletters';

CREATE TABLE 'tiki_sent_newsletters' (
  "editionId" INTEGER,
  "nlId" bigint NOT NULL default '0',
  "users" bigint default NULL,
  "sent" bigint default NULL,
  "subject" varchar(200) default NULL,
  "data" bytea,
  "datatxt" bytea,
  PRIMARY KEY ("editionId")
) ENGINE=MyISAM ;

-- ******************************************************

--
-- Table structure for table tiki_sent_newsletters_errors
--

DROP TABLE IF EXISTS 'tiki_sent_newsletters_errors';

CREATE TABLE 'tiki_sent_newsletters_errors' (
  "editionId" bigint,
  "email" varchar(255),
  "login" varchar(40) default '',
  "error" char(1) default '',
  KEY  (editionId)
) ENGINE=MyISAM ;

-- ******************************************************


--
-- Table structure for table tiki_sessions
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 13, 2003 at 01:52 AM
--

DROP TABLE IF EXISTS 'tiki_sessions';

CREATE TABLE 'tiki_sessions' (
  "sessionId" varchar(32) NOT NULL default '',
  "user" varchar(200) default NULL,
  "timestamp" bigint default NULL,
  "tikihost" varchar(200) default NULL,
  PRIMARY KEY ("sessionId")
) ENGINE=MyISAM;

CREATE  INDEX "tiki_sessions_user" ON "tiki_sessions"("user");
CREATE  INDEX "tiki_sessions_timestamp" ON "tiki_sessions"("timestamp");
-- ******************************************************

-- Tables for TikiSheet
DROP TABLE IF EXISTS 'tiki_sheet_layout';

CREATE TABLE 'tiki_sheet_layout' (
  "sheetId" integer NOT NULL default '0',
  "begin" bigint NOT NULL default '0',
  "end" bigint default NULL,
  "headerRow" smallint NOT NULL default '0',
  "footerRow" smallint NOT NULL default '0',
  "className" varchar(64) default NULL
) ENGINE=MyISAM;

CREATE UNIQUE INDEX "tiki_sheet_layout_sheetId" ON "tiki_sheet_layout"("sheetId","begin");

DROP TABLE IF EXISTS 'tiki_sheet_values';

CREATE TABLE 'tiki_sheet_values' (
  "sheetId" integer NOT NULL default '0',
  "begin" bigint NOT NULL default '0',
  "end" bigint default NULL,
  "rowIndex" smallint NOT NULL default '0',
  "columnIndex" smallint NOT NULL default '0',
  "value" varchar(255) default NULL,
  "calculation" varchar(255) default NULL,
  "width" smallint NOT NULL default '1',
  "height" smallint NOT NULL default '1',
  "format" varchar(255) default NULL,
  "user" varchar(200) default NULL
) ENGINE=MyISAM;

CREATE  INDEX "tiki_sheet_values_sheetId_2" ON "tiki_sheet_values"("sheetId","rowIndex","columnIndex");
CREATE UNIQUE INDEX "tiki_sheet_values_sheetId" ON "tiki_sheet_values"("sheetId","begin","rowIndex","columnIndex");

DROP TABLE IF EXISTS 'tiki_sheets';

CREATE TABLE 'tiki_sheets' (
  "sheetId" INTEGER,
  "title" varchar(200) NOT NULL default '',
  "description" text,
  "author" varchar(200) NOT NULL default '',
  PRIMARY KEY ("sheetId")
) ENGINE=MyISAM;


--
-- Table structure for table tiki_shoutbox
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 08:21 PM
--

DROP TABLE IF EXISTS 'tiki_shoutbox';

CREATE TABLE 'tiki_shoutbox' (
  "msgId" INTEGER,
  "message" varchar(255) default NULL,
  "timestamp" bigint default NULL,
  "user" varchar(200) default NULL,
  "hash" varchar(32) default NULL,
  PRIMARY KEY ("msgId")
) ENGINE=MyISAM ;

-- ******************************************************

--
-- Table structure for table tiki_shoutbox_words
--

DROP TABLE IF EXISTS 'tiki_shoutbox_words';

CREATE TABLE 'tiki_shoutbox_words' (
  "word" VARCHAR( 40 ) NOT NULL ,
  "qty" INT DEFAULT '0' NOT NULL ,
  PRIMARY KEY ("word")
) ENGINE=MyISAM;

-- ******************************************************

--
-- Table structure for table tiki_structure_versions
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_structure_versions';

CREATE TABLE 'tiki_structure_versions' (
  "structure_id" INTEGER,
  "version" bigint default NULL,
  PRIMARY KEY ("structure_id")
) ENGINE=MyISAM ;

-- ******************************************************

--
-- Table structure for table tiki_structures
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_structures';

CREATE TABLE 'tiki_structures' (
  "page_ref_id" INTEGER,
  "structure_id" bigint NOT NULL,
  "parent_id" bigint default NULL,
  "page_id" bigint NOT NULL,
  "page_version" integer default NULL,
  "page_alias" varchar(240) NOT NULL default '',
  "pos" smallint default NULL,
  PRIMARY KEY ("page_ref_id")
) ENGINE=MyISAM ;

CREATE  INDEX "tiki_structures_pidpaid" ON "tiki_structures"("page_id","parent_id");
CREATE  INDEX "tiki_structures_page_id" ON "tiki_structures"("page_id");
-- ******************************************************

--
-- Table structure for table tiki_submissions
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Nov 29, 2006 at 08:46 PM
--

DROP TABLE IF EXISTS 'tiki_submissions';

CREATE TABLE 'tiki_submissions' (
  "subId" INTEGER,
  "topline" varchar(255) default NULL,
  "title" varchar(255) default NULL,
  "subtitle" varchar(255) default NULL,
  "linkto" varchar(255) default NULL,
  "lang" varchar(16) default NULL,
  "authorName" varchar(60) default NULL,
  "topicId" bigint default NULL,
  "topicName" varchar(40) default NULL,
  "size" bigint default NULL,
  "useImage" char(1) default NULL,
  "image_name" varchar(80) default NULL,
  "image_caption" text default NULL,
  "image_type" varchar(80) default NULL,
  "image_size" bigint default NULL,
  "image_x" smallint default NULL,
  "image_y" smallint default NULL,
  "image_data" bytea,
  "publishDate" bigint default NULL,
  "expireDate" bigint default NULL,
  "created" bigint default NULL,
  "bibliographical_references" text,
  "resume" text,
  "heading" text,
  "body" text,
  "hash" varchar(32) default NULL,
  "author" varchar(200) default NULL,
  "nbreads" bigint default NULL,
  "votes" integer default NULL,
  "points" bigint default NULL,
  "type" varchar(50) default NULL,
  "rating" decimal(3,2) default NULL,
  "isfloat" char(1) default NULL,
  PRIMARY KEY ("subId")
) ENGINE=MyISAM ;

-- ******************************************************

--
-- Table structure for table tiki_suggested_faq_questions
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 11, 2003 at 08:52 PM
--

DROP TABLE IF EXISTS 'tiki_suggested_faq_questions';

CREATE TABLE 'tiki_suggested_faq_questions' (
  "sfqId" INTEGER,
  "faqId" bigint NOT NULL default '0',
  "question" text,
  "answer" text,
  "created" bigint default NULL,
  "user" varchar(200) default NULL,
  PRIMARY KEY ("sfqId")
) ENGINE=MyISAM ;

-- ******************************************************

--
-- Table structure for table tiki_survey_question_options
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 11, 2003 at 12:55 AM
--

DROP TABLE IF EXISTS 'tiki_survey_question_options';

CREATE TABLE 'tiki_survey_question_options' (
  "optionId" INTEGER,
  "questionId" bigint NOT NULL default '0',
  "qoption" text,
  "votes" bigint default NULL,
  PRIMARY KEY ("optionId")
) ENGINE=MyISAM ;

-- ******************************************************

--
-- Table structure for table tiki_survey_questions
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 11, 2003 at 11:55 PM
--

DROP TABLE IF EXISTS 'tiki_survey_questions';

CREATE TABLE 'tiki_survey_questions' (
  "questionId" INTEGER,
  "surveyId" bigint NOT NULL default '0',
  "question" text,
  "options" text,
  "type" char(1) default NULL,
  "position" integer default NULL,
  "votes" bigint default NULL,
  "value" bigint default NULL,
  "average" decimal(4,2) default NULL,
  PRIMARY KEY ("questionId")
) ENGINE=MyISAM ;

-- ******************************************************

--
-- Table structure for table tiki_surveys
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 07:40 PM
--

DROP TABLE IF EXISTS 'tiki_surveys';

CREATE TABLE 'tiki_surveys' (
  "surveyId" INTEGER,
  "name" varchar(200) default NULL,
  "description" text,
  "taken" bigint default NULL,
  "lastTaken" bigint default NULL,
  "created" bigint default NULL,
  "status" char(1) default NULL,
  PRIMARY KEY ("surveyId")
) ENGINE=MyISAM ;

-- ******************************************************

--
-- Table structure for table tiki_tags
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 06, 2003 at 02:58 AM
--

DROP TABLE IF EXISTS 'tiki_tags';

CREATE TABLE 'tiki_tags' (
  "tagName" varchar(80) NOT NULL default '',
  "pageName" varchar(160) NOT NULL default '',
  "hits" integer default NULL,
  "description" varchar(200) default NULL,
  "data" bytea,
  "lastModif" bigint default NULL,
  "comment" varchar(200) default NULL,
  "version" integer NOT NULL default '0',
  "user" varchar(200) default NULL,
  "ip" varchar(15) default NULL,
  "flag" char(1) default NULL,
  PRIMARY KEY ("tagName","pageName")
) ENGINE=MyISAM;

-- ******************************************************

--
-- Table structure for table tiki_theme_control_categs
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_theme_control_categs';

CREATE TABLE 'tiki_theme_control_categs' (
  "categId" bigint NOT NULL default '0',
  "theme" varchar(250) NOT NULL default '',
  PRIMARY KEY ("categId")
) ENGINE=MyISAM;

-- ******************************************************

--
-- Table structure for table tiki_theme_control_objects
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_theme_control_objects';

CREATE TABLE 'tiki_theme_control_objects' (
  "objId" varchar(250) NOT NULL default '',
  "type" varchar(250) NOT NULL default '',
  "name" varchar(250) NOT NULL default '',
  "theme" varchar(250) NOT NULL default '',
  PRIMARY KEY ("objId","type")
) ENGINE=MyISAM;

-- ******************************************************

--
-- Table structure for table tiki_theme_control_sections
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_theme_control_sections';

CREATE TABLE 'tiki_theme_control_sections' (
  "section" varchar(250) NOT NULL default '',
  "theme" varchar(250) NOT NULL default '',
  PRIMARY KEY ("section")
) ENGINE=MyISAM;

-- ******************************************************

--
-- Table structure for table tiki_topics
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 04, 2003 at 10:10 PM
--

DROP TABLE IF EXISTS 'tiki_topics';

CREATE TABLE 'tiki_topics' (
  "topicId" INTEGER,
  "name" varchar(40) default NULL,
  "image_name" varchar(80) default NULL,
  "image_type" varchar(80) default NULL,
  "image_size" bigint default NULL,
  "image_data" bytea,
  "active" char(1) default NULL,
  "created" bigint default NULL,
  PRIMARY KEY ("topicId")
) ENGINE=MyISAM ;

-- ******************************************************

--
-- Table structure for table tiki_tracker_fields
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 08, 2003 at 01:48 PM
--

DROP TABLE IF EXISTS 'tiki_tracker_fields';

CREATE TABLE 'tiki_tracker_fields' (
  "fieldId" INTEGER,
  "trackerId" bigint NOT NULL default '0',
  "name" varchar(255) default NULL,
  "options" text,
  "type" char(1) default NULL,
  "isMain" char(1) default NULL,
  "isTblVisible" char(1) default NULL,
  "position" smallint default NULL,
  "isSearchable" char(1) NOT NULL default 'y',
  "isPublic" char(1) NOT NULL default 'n',
  "isHidden" char(1) NOT NULL default 'n',
  "isMandatory" char(1) NOT NULL default 'n',
  "isMultilingual" char(1) default 'n',
  "description" text,
  "itemChoices" text,
  PRIMARY KEY ("fieldId")
) ENGINE=MyISAM ;

-- ******************************************************

--
-- Table structure for table tiki_tracker_item_attachments
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_tracker_item_attachments';

CREATE TABLE 'tiki_tracker_item_attachments' (
  "attId" INTEGER,
  "itemId" bigint NOT NULL default 0,
  "filename" varchar(80) default NULL,
  "filetype" varchar(80) default NULL,
  "filesize" bigint default NULL,
  "user" varchar(200) default NULL,
  "data" bytea,
  "path" varchar(255) default NULL,
  "downloads" bigint default NULL,
  "created" bigint default NULL,
  "comment" varchar(250) default NULL,
  "longdesc" bytea,
  "version" varchar(40) default NULL,
  PRIMARY KEY ("attId")
) ENGINE=MyISAM ;

-- ******************************************************

--
-- Table structure for table tiki_tracker_item_comments
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 08:12 AM
--

DROP TABLE IF EXISTS 'tiki_tracker_item_comments';

CREATE TABLE 'tiki_tracker_item_comments' (
  "commentId" INTEGER,
  "itemId" bigint NOT NULL default '0',
  "user" varchar(200) default NULL,
  "data" text,
  "title" varchar(200) default NULL,
  "posted" bigint default NULL,
  PRIMARY KEY ("commentId")
) ENGINE=MyISAM ;

-- ******************************************************

--
-- Table structure for table tiki_tracker_item_fields
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 08:26 AM
--

DROP TABLE IF EXISTS 'tiki_tracker_item_fields';

CREATE TABLE 'tiki_tracker_item_fields' (
  "itemId" bigint NOT NULL default '0',
  "fieldId" bigint NOT NULL default '0',
  "lang" char(16) default NULL,
  "value" text,
  PRIMARY KEY ("itemId","fieldId","lang")
) ENGINE=MyISAM;

CREATE  INDEX "tiki_tracker_item_fields_ft" ON "tiki_tracker_item_fields"("value");
-- ******************************************************

--
-- Table structure for table tiki_tracker_items
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 08:26 AM
--

DROP TABLE IF EXISTS 'tiki_tracker_items';

CREATE TABLE 'tiki_tracker_items' (
  "itemId" INTEGER,
  "trackerId" bigint NOT NULL default '0',
  "created" bigint default NULL,
  "status" char(1) default NULL,
  "lastModif" bigint default NULL,
  PRIMARY KEY ("itemId")
) ENGINE=MyISAM ;

-- ******************************************************

--
-- Table structure for table tiki_tracker_options
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 08, 2003 at 01:48 PM
--

DROP TABLE IF EXISTS 'tiki_tracker_options';

CREATE TABLE 'tiki_tracker_options' (
  "trackerId" bigint NOT NULL default '0',
  "name" varchar(80) NOT NULL default '',
  "value" text default NULL,
  PRIMARY KEY ("trackerId","name")
) ENGINE=MyISAM ;

-- ******************************************************


--
-- Table structure for table tiki_trackers
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 08:26 AM
--

DROP TABLE IF EXISTS 'tiki_trackers';

CREATE TABLE 'tiki_trackers' (
  "trackerId" INTEGER,
  "name" varchar(255) default NULL,
  "description" text,
  "created" bigint default NULL,
  "lastModif" bigint default NULL,
  "showCreated" char(1) default NULL,
  "showStatus" char(1) default NULL,
  "showLastModif" char(1) default NULL,
  "useComments" char(1) default NULL,
  "useAttachments" char(1) default NULL,
  "items" bigint default NULL,
  "showComments" char(1) default NULL,
  "showAttachments" char(1) default NULL,
  "orderAttachments" varchar(255) NOT NULL default 'filename,created,filesize,downloads,desc',
  PRIMARY KEY ("trackerId")
) ENGINE=MyISAM ;

-- ******************************************************

--
-- Table structure for table tiki_untranslated
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_untranslated';

CREATE TABLE 'tiki_untranslated' (
  "id" INTEGER,
  "source" bytea NOT NULL,
  "lang" char(16) NOT NULL default '',
  PRIMARY KEY ("source","lang")
) ENGINE=MyISAM ;

CREATE  INDEX "tiki_untranslated_id_2" ON "tiki_untranslated"("id");
CREATE UNIQUE INDEX "tiki_untranslated_id" ON "tiki_untranslated"("id");
-- ******************************************************

--
-- Table structure for table tiki_user_answers
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_user_answers';

CREATE TABLE 'tiki_user_answers' (
  "userResultId" bigint NOT NULL default '0',
  "quizId" bigint NOT NULL default '0',
  "questionId" bigint NOT NULL default '0',
  "optionId" bigint NOT NULL default '0',
  PRIMARY KEY ("userResultId","quizId","questionId","optionId")
) ENGINE=MyISAM;

-- ******************************************************


--
-- Table structure for table tiki_user_answers_uploads
--
-- Creation: Jan 25, 2005 at 07:42 PM
-- Last update: Jan 25, 2005 at 07:42 PM
--


DROP TABLE IF EXISTS 'tiki_user_answers_uploads';

CREATE TABLE 'tiki_user_answers_uploads' (
  "answerUploadId" INTEGER,
  "userResultId" bigint NOT NULL default '0',
  "questionId" bigint NOT NULL default '0',
  "filename" varchar(255) NOT NULL default '',
  "filetype" varchar(64) NOT NULL default '',
  "filesize" varchar(255) NOT NULL default '',
  "filecontent" bytea NOT NULL,
  PRIMARY KEY ("answerUploadId")
) ENGINE=MyISAM;



--
-- Table structure for table tiki_user_assigned_modules
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 08:25 PM
--

DROP TABLE IF EXISTS 'tiki_user_assigned_modules';

CREATE TABLE 'tiki_user_assigned_modules' (
  "moduleId" integer NOT NULL,
  "name" varchar(200) NOT NULL default '',
  "position" char(1) default NULL,
  "ord" smallint default NULL,
  "type" char(1) default NULL,
  "user" varchar(200) NOT NULL default '',
  PRIMARY KEY ("name","user","position","ord")
) ENGINE=MyISAM;

-- ******************************************************

--
-- Table structure for table tiki_user_bookmarks_folders
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 11, 2003 at 08:35 AM
--

DROP TABLE IF EXISTS 'tiki_user_bookmarks_folders';

CREATE TABLE 'tiki_user_bookmarks_folders' (
  "folderId" INTEGER,
  "parentId" bigint default NULL,
  "user" varchar(200) NOT NULL default '',
  "name" varchar(30) default NULL,
  PRIMARY KEY ("user","folderId")
) ENGINE=MyISAM ;

-- ******************************************************

--
-- Table structure for table tiki_user_bookmarks_urls
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 11, 2003 at 08:36 AM
--

DROP TABLE IF EXISTS 'tiki_user_bookmarks_urls';

CREATE TABLE 'tiki_user_bookmarks_urls' (
  "urlId" INTEGER,
  "name" varchar(30) default NULL,
  "url" varchar(250) default NULL,
  "data" bytea,
  "lastUpdated" bigint default NULL,
  "folderId" bigint NOT NULL default '0',
  "user" varchar(200) NOT NULL default '',
  PRIMARY KEY ("urlId")
) ENGINE=MyISAM ;

-- ******************************************************

--
-- Table structure for table tiki_user_mail_accounts
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_user_mail_accounts';

CREATE TABLE 'tiki_user_mail_accounts' (
  "accountId" INTEGER,
  "user" varchar(200) NOT NULL default '',
  "account" varchar(50) NOT NULL default '',
  "pop" varchar(255) default NULL,
  "current" char(1) default NULL,
  "port" smallint default NULL,
  "username" varchar(100) default NULL,
  "pass" varchar(100) default NULL,
  "msgs" smallint default NULL,
  "smtp" varchar(255) default NULL,
  "useAuth" char(1) default NULL,
  "smtpPort" smallint default NULL,
  PRIMARY KEY ("accountId")
) ENGINE=MyISAM ;

-- ******************************************************

--
-- Table structure for table tiki_user_menus
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 11, 2003 at 10:58 PM
--

DROP TABLE IF EXISTS 'tiki_user_menus';

CREATE TABLE 'tiki_user_menus' (
  "user" varchar(200) NOT NULL default '',
  "menuId" INTEGER,
  "url" varchar(250) default NULL,
  "name" varchar(40) default NULL,
  "position" smallint default NULL,
  "mode" char(1) default NULL,
  PRIMARY KEY ("menuId")
) ENGINE=MyISAM ;

-- ******************************************************

--
-- Table structure for table tiki_user_modules
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 05, 2003 at 03:16 AM
--

DROP TABLE IF EXISTS 'tiki_user_modules';

CREATE TABLE 'tiki_user_modules' (
  "name" varchar(200) NOT NULL default '',
  "title" varchar(40) default NULL,
  "data" bytea,
  "parse" char(1) default NULL,
  PRIMARY KEY ("name")
) ENGINE=MyISAM;

-- ******************************************************
INSERT INTO "tiki_user_modules" ("name","title","data","parse") VALUES ('mnu_application_menu', 'Menu', '{menu id=42}', 'n');


--
-- Table structure for table tiki_user_notes
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 07:52 AM
--

DROP TABLE IF EXISTS 'tiki_user_notes';

CREATE TABLE 'tiki_user_notes' (
  "user" varchar(200) NOT NULL default '',
  "noteId" INTEGER,
  "created" bigint default NULL,
  "name" varchar(255) default NULL,
  "lastModif" bigint default NULL,
  "data" text,
  "size" bigint default NULL,
  "parse_mode" varchar(20) default NULL,
  PRIMARY KEY ("noteId")
) ENGINE=MyISAM ;

-- ******************************************************

--
-- Table structure for table tiki_user_postings
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 13, 2003 at 01:12 AM
--

DROP TABLE IF EXISTS 'tiki_user_postings';

CREATE TABLE 'tiki_user_postings' (
  "user" varchar(200) NOT NULL default '',
  "posts" bigint default NULL,
  "last" bigint default NULL,
  "first" bigint default NULL,
  "level" integer default NULL,
  PRIMARY KEY ("user")
) ENGINE=MyISAM;

-- ******************************************************

--
-- Table structure for table tiki_user_preferences
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 13, 2003 at 01:09 AM
--

DROP TABLE IF EXISTS 'tiki_user_preferences';

CREATE TABLE 'tiki_user_preferences' (
  "user" varchar(200) NOT NULL default '',
  "prefName" varchar(40) NOT NULL default '',
  "value" varchar(250) default NULL,
  PRIMARY KEY ("user","prefName")
) ENGINE=MyISAM;

-- ******************************************************

--
-- Table structure for table tiki_user_quizzes
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_user_quizzes';

CREATE TABLE 'tiki_user_quizzes' (
  "user" varchar(200) default '',
  "quizId" bigint default NULL,
  "timestamp" bigint default NULL,
  "timeTaken" bigint default NULL,
  "points" bigint default NULL,
  "maxPoints" bigint default NULL,
  "resultId" bigint default NULL,
  "userResultId" INTEGER,
  PRIMARY KEY ("userResultId")
) ENGINE=MyISAM ;

-- ******************************************************

--
-- Table structure for table tiki_user_taken_quizzes
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_user_taken_quizzes';

CREATE TABLE 'tiki_user_taken_quizzes' (
  "user" varchar(200) NOT NULL default '',
  "quizId" varchar(255) NOT NULL default '',
  PRIMARY KEY ("user","quizId")
) ENGINE=MyISAM;

-- ******************************************************


--
-- Table structure for table tiki_user_tasks_history
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jan 25, 2005 by sir-b & moresun
--
DROP TABLE IF EXISTS 'tiki_user_tasks_history';

CREATE TABLE 'tiki_user_tasks_history' (
  "belongs_to" integer(14) NOT NULL,                   -- the fist task in a history it has the same id as the task id
  "task_version" integer(4) NOT NULL DEFAULT 0,        -- version number for the history it starts with 0
  "title" varchar(250) NOT NULL,                       -- title
  "description" text DEFAULT NULL,                     -- description
  "start" integer(14) DEFAULT NULL,                    -- date of the starting, if it is not set than there is not starting date
  "end" integer(14) DEFAULT NULL,                      -- date of the end, if it is not set than there is not dealine
  "lasteditor" varchar(200) NOT NULL,                  -- lasteditor: username of last editior
  "lastchanges" integer(14) NOT NULL,                  -- date of last changes
  "priority" integer(2) NOT NULL DEFAULT 3,                     -- priority
  "completed" integer(14) DEFAULT NULL,                -- date of the completation if it is null it is not yet completed
  "deleted" integer(14) DEFAULT NULL,                  -- date of the deleteation it it is null it is not deleted
  "status" char(1) DEFAULT NULL,                       -- null := waiting, 
                                                     -- o := open / in progress, 
                                                     -- c := completed -> (percentage = 100) 
  "percentage" smallint DEFAULT NULL,
  "accepted_creator" char(1) DEFAULT NULL,             -- y - yes, n - no, null - waiting
  "accepted_user" char(1) DEFAULT NULL,                -- y - yes, n - no, null - waiting
  PRIMARY KEY (belongs_to, task_version)
) ENGINE=MyISAM ;



--
-- Table structure for table tiki_user_tasks
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jan 25, 2005 by sir-b & moresun
--
DROP TABLE IF EXISTS 'tiki_user_tasks';

CREATE TABLE 'tiki_user_tasks' (
  "taskId" INTEGER,        -- task id
  "last_version" integer(4) NOT NULL DEFAULT 0,        -- last version of the task starting with 0
  "user" varchar(200) NOT NULL DEFAULT '',              -- task user
  "creator" varchar(200) NOT NULL,                     -- username of creator
  "public_for_group" varchar(30) DEFAULT NULL,         -- this group can also view the task, if it is null it is not public
  "rights_by_creator" char(1) DEFAULT NULL,            -- null the user can delete the task, 
  "created" integer(14) NOT NULL,                      -- date of the creation
  "status" char(1) default NULL,
  "priority" smallint default NULL,
  "completed" bigint default NULL,
  "percentage" smallint default NULL,
  PRIMARY KEY (taskId),
  UNIQUE(creator, created)
) ENGINE=MyISAM;


-- ******************************************************

--
-- Table structure for table tiki_user_votings
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 11, 2003 at 11:55 PM
--

DROP TABLE IF EXISTS 'tiki_user_votings';

CREATE TABLE 'tiki_user_votings' (
  "user" varchar(200) NOT NULL default '',
  "id" varchar(255) NOT NULL default '',
  "optionId" bigint NOT NULL default 0,
  PRIMARY KEY ("user","id")
) ENGINE=MyISAM;

-- ******************************************************

--
-- Table structure for table tiki_user_watches
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 08:07 AM
--

DROP TABLE IF EXISTS 'tiki_user_watches';

CREATE TABLE 'tiki_user_watches' (
  "user" varchar(200) NOT NULL default '',
  "event" varchar(40) NOT NULL default '',
  "object" varchar(200) NOT NULL default '',
  "hash" varchar(32) default NULL,
  "title" varchar(250) default NULL,
  "type" varchar(200) default NULL,
  "url" varchar(250) default NULL,
  "email" varchar(200) default NULL,
  PRIMARY KEY ("user","event","object")
) ENGINE=MyISAM;

-- ******************************************************

--
-- Table structure for table tiki_userfiles
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_userfiles';

CREATE TABLE 'tiki_userfiles' (
  "user" varchar(200) NOT NULL default '',
  "fileId" INTEGER,
  "name" varchar(200) default NULL,
  "filename" varchar(200) default NULL,
  "filetype" varchar(200) default NULL,
  "filesize" varchar(200) default NULL,
  "data" bytea,
  "hits" integer default NULL,
  "isFile" char(1) default NULL,
  "path" varchar(255) default NULL,
  "created" bigint default NULL,
  PRIMARY KEY ("fileId")
) ENGINE=MyISAM ;

-- ******************************************************

--
-- Table structure for table tiki_userpoints
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 11, 2003 at 05:47 AM
--

DROP TABLE IF EXISTS 'tiki_userpoints';

CREATE TABLE 'tiki_userpoints' (
  "user" varchar(200) default NULL,
  "points" decimal(8,2) default NULL,
  "voted" integer default NULL
) ENGINE=MyISAM;

-- ******************************************************

--
-- Table structure for table tiki_users
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_users';

CREATE TABLE 'tiki_users' (
  "user" varchar(200) NOT NULL default '',
  "password" varchar(40) default NULL,
  "email" varchar(200) default NULL,
  "lastLogin" bigint default NULL,
  PRIMARY KEY ("user")
) ENGINE=MyISAM;

-- ******************************************************

--
-- Table structure for table tiki_webmail_contacts
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_webmail_contacts';

CREATE TABLE 'tiki_webmail_contacts' (
  "contactId" INTEGER,
  "firstName" varchar(80) default NULL,
  "lastName" varchar(80) default NULL,
  "email" varchar(250) default NULL,
  "nickname" varchar(200) default NULL,
  "user" varchar(200) NOT NULL default '',
  PRIMARY KEY ("contactId")
) ENGINE=MyISAM ;

-- ******************************************************

DROP TABLE IF EXISTS 'tiki_webmail_contacts_groups';

CREATE TABLE 'tiki_webmail_contacts_groups' (
  "contactId" bigint NOT NULL,
  "groupName" varchar(255) NOT NULL,
  PRIMARY KEY ("contactId","groupName")
) ENGINE=MyISAM ;

-- ******************************************************

--
-- Table structure for table tiki_webmail_messages
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_webmail_messages';

CREATE TABLE 'tiki_webmail_messages' (
  "accountId" bigint NOT NULL default '0',
  "mailId" varchar(255) NOT NULL default '',
  "user" varchar(200) NOT NULL default '',
  "isRead" char(1) default NULL,
  "isReplied" char(1) default NULL,
  "isFlagged" char(1) default NULL,
  PRIMARY KEY ("accountId","mailId")
) ENGINE=MyISAM;

-- ******************************************************

--
-- Table structure for table tiki_wiki_attachments
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_wiki_attachments';

CREATE TABLE 'tiki_wiki_attachments' (
  "attId" INTEGER,
  "page" varchar(200) NOT NULL default '',
  "filename" varchar(80) default NULL,
  "filetype" varchar(80) default NULL,
  "filesize" bigint default NULL,
  "user" varchar(200) default NULL,
  "data" bytea,
  "path" varchar(255) default NULL,
  "downloads" bigint default NULL,
  "created" bigint default NULL,
  "comment" varchar(250) default NULL,
  PRIMARY KEY ("attId")
) ENGINE=MyISAM ;

-- ******************************************************

--
-- Table structure for table tiki_zones
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_zones';

CREATE TABLE 'tiki_zones' (
  "zone" varchar(40) NOT NULL default '',
  PRIMARY KEY ("zone")
) ENGINE=MyISAM;

-- ******************************************************
--
-- Table structure for table tiki_download
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Apr 15 2004 at 07:42 PM
--

DROP TABLE IF EXISTS 'tiki_download';

CREATE TABLE 'tiki_download' (
  "id" INTEGER,
  "object" varchar(255) NOT NULL default '',
  "userId" integer NOT NULL default '0',
  "type" varchar(20) NOT NULL default '',
  "date" bigint NOT NULL default '0',
  "IP" varchar(50) NOT NULL default '',
  PRIMARY KEY ("id")
) ENGINE=MyISAM;

CREATE  INDEX "tiki_download_object" ON "tiki_download"("object","userId","type");
CREATE  INDEX "tiki_download_userId" ON "tiki_download"("userId");
CREATE  INDEX "tiki_download_type" ON "tiki_download"("type");
CREATE  INDEX "tiki_download_date" ON "tiki_download"("date");
-- ******************************************************

--
-- Table structure for table users_grouppermissions
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 11, 2003 at 07:22 AM
--

DROP TABLE IF EXISTS 'users_grouppermissions';

CREATE TABLE 'users_grouppermissions' (
  "groupName" varchar(255) NOT NULL default '',
  "permName" varchar(31) NOT NULL default '',
  "value" char(1) default '',
  PRIMARY KEY ("groupName","permName")
) ENGINE=MyISAM;

-- ******************************************************

INSERT INTO "users_grouppermissions" ("groupName","permName") VALUES ('Anonymous','tiki_p_view');


--
-- Table structure for table users_groups
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 08:57 PM
--

DROP TABLE IF EXISTS 'users_groups';

CREATE TABLE 'users_groups' (
  "groupName" varchar(255) NOT NULL default '',
  "groupDesc" varchar(255) default NULL,
  "groupHome" varchar(255),
  "usersTrackerId" bigint,
  "groupTrackerId" bigint,
  "usersFieldId" bigint,
  "groupFieldId" bigint,
  "registrationChoice" char(1) default NULL,
  "registrationUsersFieldIds" text,
  "userChoice" char(1) default NULL,
  "groupDefCat" bigint default 0,
  "groupTheme" varchar(255) default '',  
  PRIMARY KEY ("groupName")
) ENGINE=MyISAM;

-- ******************************************************

--
-- Table structure for table users_objectpermissions
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 07:20 AM
--

DROP TABLE IF EXISTS 'users_objectpermissions';

CREATE TABLE 'users_objectpermissions' (
  "groupName" varchar(255) NOT NULL default '',
  "permName" varchar(31) NOT NULL default '',
  "objectType" varchar(20) NOT NULL default '',
  "objectId" varchar(32) NOT NULL default '',
  PRIMARY KEY ("objectId","objectType","groupName","permName")
) ENGINE=MyISAM;

-- ******************************************************

--
-- Table structure for table users_permissions
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 11, 2003 at 07:22 AM
--

DROP TABLE IF EXISTS 'users_permissions';

CREATE TABLE 'users_permissions' (
  "permName" varchar(31) NOT NULL default '',
  "permDesc" varchar(250) default NULL,
  "level" varchar(80) default NULL,
  "type" varchar(20) default NULL,
  "admin" varchar(1) default NULL,
  PRIMARY KEY ("permName")
) ENGINE=MyISAM;

CREATE  INDEX "users_permissions_type" ON "users_permissions"("type");
-- ******************************************************
-- 

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_abort_instance', 'Can abort a process instance', 'editors', 'workflow');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_access_closed_site', 'Can access site when closed', 'admin', 'tiki');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_add_events', 'Can add events in the calendar', 'registered', 'calendar');

INSERT INTO "users_permissions" ("permName","permDesc","level","type","admin") VALUES ('tiki_p_admin', 'Administrator, can manage users groups and permissions, Hotwords and all the weblog features', 'admin', 'tiki', 'y');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_admin_banners', 'Administrator, can admin banners', 'admin', 'tiki');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_admin_banning', 'Can ban users or ips', 'admin', 'tiki');

INSERT INTO "users_permissions" ("permName","permDesc","level","type","admin") VALUES ('tiki_p_admin_calendar', 'Can create/admin calendars', 'admin', 'calendar', 'y');

INSERT INTO "users_permissions" ("permName","permDesc","level","type","admin") VALUES ('tiki_p_admin_categories', 'Can admin categories', 'editors', 'category', 'y');

INSERT INTO "users_permissions" ("permName","permDesc","level","type","admin") VALUES ('tiki_p_admin_charts', 'Can admin charts', 'admin', 'charts', 'y');

INSERT INTO "users_permissions" ("permName","permDesc","level","type","admin") VALUES ('tiki_p_admin_chat', 'Administrator, can create channels remove channels etc', 'editors', 'chat', 'y');

INSERT INTO "users_permissions" ("permName","permDesc","level","type","admin") VALUES ('tiki_p_admin_cms', 'Can admin the cms', 'editors', 'cms', 'y');

INSERT INTO "users_permissions" ("permName","permDesc","level","type","admin") VALUES ('tiki_p_admin_contribution', 'Can admin contributions', 'admin', 'contribution', 'y');

INSERT INTO "users_permissions" ("permName","permDesc","level","type","admin") VALUES ('tiki_p_admin_directory', 'Can admin the directory', 'editors', 'directory', 'y');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_admin_directory_cats', 'Can admin directory categories', 'editors', 'directory');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_admin_directory_sites', 'Can admin directory sites', 'editors', 'directory');

INSERT INTO "users_permissions" ("permName","permDesc","level","type","admin") VALUES ('tiki_p_admin_drawings', 'Can admin drawings', 'editors', 'drawings', 'y');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_admin_dynamic', 'Can admin the dynamic content system', 'editors', 'tiki');

INSERT INTO "users_permissions" ("permName","permDesc","level","type","admin") VALUES ('tiki_p_admin_faqs', 'Can admin faqs', 'editors', 'faqs', 'y');

INSERT INTO "users_permissions" ("permName","permDesc","level","type","admin") VALUES ('tiki_p_admin_file_galleries', 'Can admin file galleries', 'editors', 'file galleries', 'y');

INSERT INTO "users_permissions" ("permName","permDesc","level","type","admin") VALUES ('tiki_p_admin_forum', 'Can admin forums', 'editors', 'forums', 'y');

INSERT INTO "users_permissions" ("permName","permDesc","level","type","admin") VALUES ('tiki_p_admin_galleries', 'Can admin Image Galleries', 'editors', 'image galleries', 'y');

INSERT INTO "users_permissions" ("permName","permDesc","level","type","admin") VALUES ('tiki_p_admin_games', 'Can admin games', 'editors', 'games', 'y');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_admin_integrator', 'Can admin integrator repositories and rules', 'admin', 'tiki');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_admin_mailin', 'Can admin mail-in accounts', 'admin', 'tiki');

INSERT INTO "users_permissions" ("permName","permDesc","level","type","admin") VALUES ('tiki_p_admin_newsletters', 'Can admin newsletters', 'admin', 'newsletters', 'y');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_admin_objects','Can edit object permissions', 'admin', 'tiki');

INSERT INTO "users_permissions" ("permName","permDesc","level","type","admin") VALUES ('tiki_p_admin_polls','Can admin polls', 'admin', 'polls', 'y');

INSERT INTO "users_permissions" ("permName","permDesc","level","type","admin") VALUES ('tiki_p_admin_quizzes', 'Can admin quizzes', 'editors', 'quizzes', 'y');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_admin_received_articles', 'Can admin received articles', 'editors', 'comm');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_admin_received_pages', 'Can admin received pages', 'editors', 'comm');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_admin_rssmodules','Can admin rss modules', 'admin', 'tiki');

INSERT INTO "users_permissions" ("permName","permDesc","level","type","admin") VALUES ('tiki_p_admin_sheet', 'Can admin sheet', 'admin', 'sheet', 'y');

INSERT INTO "users_permissions" ("permName","permDesc","level","type","admin") VALUES ('tiki_p_admin_shoutbox', 'Can admin shoutbox (Edit/remove msgs)', 'editors', 'shoutbox', 'y');

INSERT INTO "users_permissions" ("permName","permDesc","level","type","admin") VALUES ('tiki_p_admin_surveys', 'Can admin surveys', 'editors', 'surveys', 'y');

INSERT INTO "users_permissions" ("permName","permDesc","level","type","admin") VALUES ('tiki_p_admin_trackers', 'Can admin trackers', 'editors', 'trackers', 'y');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_admin_users', 'Can admin users', 'admin', 'user');

INSERT INTO "users_permissions" ("permName","permDesc","level","type","admin") VALUES ('tiki_p_admin_wiki', 'Can admin the wiki', 'editors', 'wiki', 'y');

INSERT INTO "users_permissions" ("permName","permDesc","level","type","admin") VALUES ('tiki_p_admin_workflow', 'Can admin workflow processes', 'admin', 'workflow', 'y');

INSERT INTO "users_permissions" ("permName","permDesc","level","type","admin") VALUES ('tiki_p_admin_quicktags', 'Can admin quicktags', 'admin', 'quicktags', 'y');

INSERT INTO "users_permissions" ("permName","permDesc","level","type","admin") VALUES ('tiki_p_admin_freetags', 'Can admin freetags', 'admin', 'freetags', 'y');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_approve_submission', 'Can approve submissions', 'editors', 'cms');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_attach_trackers', 'Can attach files to tracker items', 'registered', 'trackers');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_autoapprove_submission', 'Submited articles automatically approved', 'editors', 'cms');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_autosubmit_link', 'Submited links are valid', 'editors', 'directory');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_autoval_chart_suggestio', 'Autovalidate suggestions', 'editors', 'charts');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_batch_subscribe_email', 'Can subscribe many e-mails at once (requires tiki_p_subscribe email)', 'editors', 'newsletters');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_batch_upload_files', 'Can upload zip files with files', 'editors', 'file galleries');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_batch_upload_file_dir', 'Can use Directory Batch Load', 'editors', 'file galleries');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_batch_upload_image_dir', 'Can use Directory Batch Load', 'editors', 'image galleries');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_batch_upload_images', 'Can upload zip files with images', 'editors', 'image galleries');

INSERT INTO "users_permissions" ("permName","permDesc","level","type","admin") VALUES ('tiki_p_blog_admin', 'Can admin blogs', 'editors', 'blogs', 'y');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_blog_post', 'Can post to a blog', 'registered', 'blogs');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_broadcast', 'Can broadcast messages to groups', 'admin', 'messu');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_broadcast_all', 'Can broadcast messages to all user', 'admin', 'messu');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_cache_bookmarks', 'Can cache user bookmarks', 'admin', 'user');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_change_events', 'Can change events in the calendar', 'registered', 'calendar');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_chat', 'Can use the chat system', 'registered', 'chat');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_comment_tracker_items', 'Can insert comments for tracker items', 'basic', 'trackers');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_configure_modules', 'Can configure modules', 'registered', 'user');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_create_blogs', 'Can create a blog', 'editors', 'blogs');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_create_bookmarks', 'Can create user bookmarks', 'registered', 'user');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_create_css', 'Can create new css suffixed with -user', 'registered', 'tiki');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_create_file_galleries', 'Can create file galleries', 'editors', 'file galleries');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_create_galleries', 'Can create image galleries', 'editors', 'image galleries');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_create_tracker_items', 'Can create new items for trackers', 'registered', 'trackers');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_download_files', 'Can download files', 'basic', 'file galleries');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_edit', 'Can edit pages', 'registered', 'wiki');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_edit_article', 'Can edit articles', 'editors', 'cms');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_edit_categorized', 'Can edit categorized items', 'registered', 'category');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_edit_comments', 'Can edit all comments', 'editors', 'comments');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_edit_content_templates', 'Can edit content templates', 'editors', 'content templates');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_edit_cookies', 'Can admin cookies', 'editors', 'tiki');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_edit_copyrights', 'Can edit copyright notices', 'editors', 'wiki');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_edit_drawings', 'Can edit drawings', 'basic', 'drawings');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_edit_dynvar', 'Can edit dynamic variables', 'editors', 'wiki');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_edit_gallery_file', 'Can edit a gallery file', 'editors', 'file galleries');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_edit_html_pages', 'Can edit HTML pages', 'editors', 'html pages');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_edit_languages', 'Can edit translations and create new languages', 'editors', 'tiki');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_edit_sheet', 'Can create and edit sheets', 'editors', 'sheet');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_edit_structures', 'Can create and edit structures', 'editors', 'wiki');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_edit_submission', 'Can edit submissions', 'editors', 'cms');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_edit_templates', 'Can edit site templates', 'admin', 'tiki');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_exception_instance', 'Can declare an instance as exception', 'registered', 'workflow');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_forum_edit_own_posts', 'Can edit own forum posts', 'registered', 'forums');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_forum_attach', 'Can attach to forum posts', 'registered', 'forums');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_forum_autoapp', 'Auto approve forum posts', 'editors', 'forums');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_forum_post', 'Can post in forums', 'registered', 'forums');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_forum_post_topic', 'Can start threads in forums', 'registered', 'forums');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_forum_read', 'Can read forums', 'basic', 'forums');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_forum_vote', 'Can vote comments in forums', 'registered', 'forums');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_forums_report', 'Can report msgs to moderator', 'registered', 'forums');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_freetags_tag', 'Can tag objects', 'registered', 'freetags');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_list_users', 'Can list registered users', 'registered', 'community');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_live_support', 'Can use live support system', 'basic', 'support');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_live_support_admin', 'Admin live support system', 'admin', 'support');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_lock', 'Can lock pages', 'editors', 'wiki');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_map_create', 'Can create new mapfile', 'admin', 'maps');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_map_delete', 'Can delete mapfiles', 'admin', 'maps');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_map_edit', 'Can edit mapfiles', 'editors', 'maps');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_map_view', 'Can view mapfiles', 'basic', 'maps');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_map_view_mapfiles', 'Can view contents of mapfiles', 'registered', 'maps');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_messages', 'Can use the messaging system', 'registered', 'messu');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_minical', 'Can use the mini event calendar', 'registered', 'user');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_minor', 'Can save as minor edit', 'registered', 'wiki');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_modify_tracker_items', 'Can change tracker items', 'registered', 'trackers');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_newsreader', 'Can use the newsreader', 'registered', 'user');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_notepad', 'Can use the notepad', 'registered', 'user');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_play_games', 'Can play games', 'basic', 'games');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_post_comments', 'Can post new comments', 'registered', 'comments');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_post_shoutbox', 'Can post messages in shoutbox', 'basic', 'shoutbox');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_read_article', 'Can read articles', 'basic', 'cms');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_read_blog', 'Can read blogs', 'basic', 'blogs');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_read_comments', 'Can read comments', 'basic', 'comments');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_remove', 'Can remove', 'editors', 'wiki');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_remove_article', 'Can remove articles', 'editors', 'cms');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_remove_comments', 'Can delete comments', 'editors', 'comments');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_remove_submission', 'Can remove submissions', 'editors', 'cms');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_rename', 'Can rename pages', 'editors', 'wiki');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_rollback', 'Can rollback pages', 'editors', 'wiki');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_send_articles', 'Can send articles to other sites', 'editors', 'comm');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_send_instance', 'Can send instances after completion', 'registered', 'workflow');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_send_newsletters', 'Can send newsletters', 'editors', 'newsletters');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_send_pages', 'Can send pages to other sites', 'registered', 'comm');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_sendme_articles', 'Can send articles to this site', 'registered', 'comm');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_sendme_pages', 'Can send pages to this site', 'registered', 'comm');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_submit_article', 'Can submit articles', 'basic', 'cms');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_submit_link', 'Can submit sites to the directory', 'basic', 'directory');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_subscribe_email', 'Can subscribe any email to newsletters', 'editors', 'newsletters');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_subscribe_newsletters', 'Can subscribe to newsletters', 'basic', 'newsletters');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_suggest_chart_item', 'Can suggest items', 'basic', 'charts');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_suggest_faq', 'Can suggest faq questions', 'basic', 'faqs');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_take_quiz', 'Can take quizzes', 'basic', 'quizzes');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_take_survey', 'Can take surveys', 'basic', 'surveys');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_tasks', 'Can use tasks', 'registered', 'user');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_tasks_admin', 'Can admin public tasks', 'admin', 'user');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_tasks_receive', 'Can receive tasks from other users', 'registered', 'user');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_tasks_send', 'Can send tasks to other users', 'registered', 'user');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_topic_read', 'Can read a topic (Applies only to individual topic perms)', 'basic', 'cms');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_tracker_view_ratings', 'Can view rating result for tracker items', 'basic', 'trackers');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_tracker_vote_ratings', 'Can vote a rating for tracker items', 'registered', 'trackers');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_upload_files', 'Can upload files', 'registered', 'file galleries');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_upload_images', 'Can upload images', 'registered', 'image galleries');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_upload_picture', 'Can upload pictures to wiki pages', 'registered', 'wiki');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_use_HTML', 'Can use HTML in pages', 'editors', 'tiki');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_use_content_templates', 'Can use content templates', 'registered', 'content templates');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_use_webmail', 'Can use webmail', 'registered', 'webmail');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_use_workflow', 'Can execute workflow activities', 'registered', 'workflow');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_userfiles', 'Can upload personal files', 'registered', 'user');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_usermenu', 'Can create items in personal menu', 'registered', 'user');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_validate_links', 'Can validate submited links', 'editors', 'directory');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view', 'Can view page/pages', 'basic', 'wiki');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_calendar', 'Can browse the calendar', 'basic', 'calendar');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_categories', 'Can view categories', 'basic', 'category');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_categorized', 'Can view categorized items', 'basic', 'category');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_chart', 'Can view charts', 'basic', 'charts');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_directory', 'Can use the directory', 'basic', 'directory');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_events', 'Can view events details', 'registered', 'calendar');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_faqs', 'Can view faqs', 'basic', 'faqs');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_file_gallery', 'Can view file galleries', 'basic', 'file galleries');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_freetags', 'Can browse freetags', 'basic', 'freetags');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_html_pages', 'Can view HTML pages', 'basic', 'html pages');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_image_gallery', 'Can view image galleries', 'basic', 'image galleries');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_integrator', 'Can view integrated repositories', 'basic', 'tiki');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_quiz_stats', 'Can view quiz stats', 'basic', 'quizzes');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_referer_stats', 'Can view referer stats', 'editors', 'tiki');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_sheet', 'Can view sheet', 'basic', 'sheet');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_sheet_history', 'Can view sheet history', 'admin', 'sheet');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_shoutbox', 'Can view shoutbox', 'basic', 'shoutbox');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_stats', 'Can view site stats', 'basic', 'tiki');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_survey_stats', 'Can view survey stats', 'basic', 'surveys');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_templates', 'Can view site templates', 'admin', 'tiki');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_tiki_calendar', 'Can view Tikiwiki tools calendar', 'basic', 'calendar');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_trackers', 'Can view trackers', 'basic', 'trackers');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_list_trackers', 'Can list trackers', 'basic', 'trackers');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_trackers_closed', 'Can view trackers closed items', 'registered', 'trackers');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_trackers_pending', 'Can view trackers pending items', 'editors', 'trackers');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_user_results', 'Can view user quiz results', 'editors', 'quizzes');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_vote_chart', 'Can vote', 'basic', 'charts');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_vote_comments', 'Can vote comments', 'registered', 'comments');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_vote_poll', 'Can vote polls', 'basic', 'polls');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_poll_results', 'Can view poll results', 'basic', 'polls');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_watch_trackers', 'Can watch tracker', 'registered', 'trackers');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_wiki_admin_attachments', 'Can admin attachments to wiki pages', 'editors', 'wiki');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_wiki_admin_ratings', 'Can add and change ratings on wiki pages', 'admin', 'wiki');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_wiki_attach_files', 'Can attach files to wiki pages', 'registered', 'wiki');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_wiki_view_attachments', 'Can view wiki attachments and download', 'registered', 'wiki');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_wiki_view_comments', 'Can view wiki comments', 'basic', 'wiki');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_wiki_view_history', 'Can view wiki history', 'basic', 'wiki');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_wiki_view_ratings', 'Can view rating of wiki pages', 'basic', 'wiki');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_wiki_view_source', 'Can view source of wiki pages', 'basic', 'wiki');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_wiki_vote_ratings', 'Can participate to rating of wiki pages', 'registered', 'wiki');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_assign_perm_file_gallery', 'Can assign perms to file gallery', 'admin', 'file galleries');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_actionlog', 'Can view action log', 'registered', 'tiki');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_assign_perm_blog', 'Can assign perms to blog', 'admin', 'blogs');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_tell_a_friend', 'Can send a link to a friend', 'Basic', 'tiki');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_list_file_galleries', 'Can list file galleries', 'basic', 'file galleries');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_assign_perm_wiki_page', 'Can assign perms to wiki pages', 'admin', 'wiki');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_mypage', 'Can view any mypage', 'basic', 'mypage');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_edit_own_mypage', 'Can view/edit only one\'s own mypages', 'registered', 'mypage');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_edit_mypage', 'Can edit any mypage', 'registered', 'mypage');

INSERT INTO "users_permissions" ("permName","permDesc","level","type","admin") VALUES ('tiki_p_admin_mypage', 'Can admin any mypage', 'admin', 'mypage','y');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_list_mypage', 'Can list mypages', 'registered', 'mypage');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_assign_perm_mypage', 'Can assign perms to mypage', 'admin', 'mypage');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_watch_structure', 'Can watch structure', 'registered', 'wiki');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_edit_menu', 'Can edit menu', 'admin', 'tiki');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_edit_menu_option', 'Can edit menu option', 'admin', 'tiki');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_subscribe_groups', 'Can subscribe to groups', 'registered', 'tiki');

-- ******************************************************

--
-- Table structure for table users_usergroups
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 09:31 PM
--

DROP TABLE IF EXISTS 'users_usergroups';

CREATE TABLE 'users_usergroups' (
  "userId" integer NOT NULL default '0',
  "groupName" varchar(255) NOT NULL default '',
  PRIMARY KEY ("userId","groupName")
) ENGINE=MyISAM;

-- ******************************************************
INSERT INTO "users_groups" ("groupName","groupDesc") VALUES ('Anonymous','Public users not logged');

INSERT INTO "users_groups" ("groupName","groupDesc") VALUES ('Registered','Users logged into the system');

INSERT INTO "users_groups" ("groupName","groupDesc") VALUES ('Admins','Administrator and accounts managers.');

-- ******************************************************

--
-- Table structure for table users_users
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 13, 2003 at 01:07 AM
--

DROP TABLE IF EXISTS 'users_users';

CREATE TABLE 'users_users' (
  "userId" INTEGER,
  "email" varchar(200) default NULL,
  "login" varchar(200) NOT NULL default '',
  "password" varchar(30) default '',
  "provpass" varchar(30) default NULL,
  "default_group" varchar(255),
  "lastLogin" bigint default NULL,
  "currentLogin" bigint default NULL,
  "registrationDate" bigint default NULL,
  "challenge" varchar(32) default NULL,
  "pass_confirm" bigint default NULL,
  "email_confirm" bigint default NULL,
  "hash" varchar(32) default NULL,
  "created" bigint default NULL,
  "avatarName" varchar(80) default NULL,
  "avatarSize" bigint default NULL,
  "avatarFileType" varchar(250) default NULL,
  "avatarData" bytea,
  "avatarLibName" varchar(200) default NULL,
  "avatarType" char(1) default NULL,
  "score" bigint NOT NULL default 0,
  "valid" varchar(32) default NULL,
  "unsuccessful_logins" bigint default 0,
  "openid_url" varchar(255) default NULL,
  PRIMARY KEY ("userId")
) ENGINE=MyISAM ;

CREATE  INDEX "users_users_login" ON "users_users"("login");
CREATE  INDEX "users_users_score" ON "users_users"("score");
CREATE  INDEX "users_users_registrationDate" ON "users_users"("registrationDate");
CREATE  INDEX "users_users_openid_url" ON "users_users"("openid_url");
-- ******************************************************
------ Administrator account
INSERT INTO "users_users" ("email","login","password","hash") VALUES ('','admin','admin','f6fdffe48c908deb0f4c3bd36c032e72');

UPDATE "users_users" SET "currentLogin"="lastLogin" "registrationDate"="lastLogin";

INSERT INTO "tiki_user_preferences" ("user","prefName","value") VALUES ('admin','realName','System Administrator');

INSERT INTO "users_usergroups" ("userId","groupName") VALUES (1,'Admins');

INSERT INTO "users_grouppermissions" ("groupName","permName") VALUES ('Admins','tiki_p_admin');

-- ******************************************************
-- 

--
-- Table structure for table 'tiki_integrator_reps'
--
DROP TABLE IF EXISTS 'tiki_integrator_reps';

CREATE TABLE 'tiki_integrator_reps' (
  "repID" INTEGER,
  "name" varchar(255) NOT NULL default '',
  "path" varchar(255) NOT NULL default '',
  "start_page" varchar(255) NOT NULL default '',
  "css_file" varchar(255) NOT NULL default '',
  "visibility" char(1) NOT NULL default 'y',
  "cacheable" char(1) NOT NULL default 'y',
  "expiration" bigint NOT NULL default '0',
  "description" text NOT NULL,
  PRIMARY KEY ("repID")
) ENGINE=MyISAM;


--
-- Dumping data for table 'tiki_integrator_reps'
--
INSERT INTO tiki_integrator_reps VALUES ('1','Doxygened (1.3.4) Documentation','','index.html','doxygen.css','n','y','0','Use this repository as rule source for all your repositories based on doxygened docs. To setup yours just add new repository and copy rules from this repository :)');


--
-- Table structure for table 'tiki_integrator_rules'
--
DROP TABLE IF EXISTS 'tiki_integrator_rules';

CREATE TABLE 'tiki_integrator_rules' (
  "ruleID" INTEGER,
  "repID" bigint NOT NULL default '0',
  "ord" smallint unsigned NOT NULL default '0',
  "srch" bytea NOT NULL,
  "repl" bytea NOT NULL,
  "type" char(1) NOT NULL default 'n',
  "casesense" char(1) NOT NULL default 'y',
  "rxmod" varchar(20) NOT NULL default '',
  "enabled" char(1) NOT NULL default 'n',
  "description" text NOT NULL,
  PRIMARY KEY ("ruleID")
) ENGINE=MyISAM;

CREATE  INDEX "tiki_integrator_rules_repID" ON "tiki_integrator_rules"("repID");

--
-- Dumping data for table 'tiki_integrator_rules'
--
INSERT INTO tiki_integrator_rules VALUES ('1','1','1','.*<body[^>]*?>(.*?)</body.*','\1','y','n','i','y','Extract code between <body> and </body> tags');

INSERT INTO tiki_integrator_rules VALUES ('2','1','2','img src=(\"|\')(?!http://)','img src=\1{path}/','y','n','i','y','Fix image paths');

INSERT INTO tiki_integrator_rules VALUES ('3','1','3','href=(\"|\')(?!(--|(http|ftp)://))','href=\1tiki-integrator.php?repID={repID}&file=','y','n','i','y','Replace internal links to integrator. Don\'t touch an external link.');


--
-- Table structures for table 'tiki_quicktags'
-- 
DROP TABLE IF EXISTS 'tiki_quicktags';

CREATE TABLE 'tiki_quicktags' (
  "tagId" INTEGER,
  "taglabel" varchar(255) default NULL,
  "taginsert" text,
  "tagicon" varchar(255) default NULL,
  "tagcategory" varchar(255) default NULL,
  PRIMARY KEY ("tagId")
) ENGINE=MyISAM ;

CREATE  INDEX "tiki_quicktags_tagcategory" ON "tiki_quicktags"("tagcategory");
CREATE  INDEX "tiki_quicktags_taglabel" ON "tiki_quicktags"("taglabel");

-- wiki
INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('bold','__text__','pics/icons/text_bold.png','wiki');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('italic','\'\'text\'\'','pics/icons/text_italic.png','wiki');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('underline','===text===','pics/icons/text_underline.png','wiki');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('table new','||r1c1|r1c2\nr2c1|r2c2||','pics/icons/table.png','wiki');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('external link','[http://example.com|text]','pics/icons/world_link.png','wiki');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('wiki link','((text))','pics/icons/page_link.png','wiki');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('heading1','!text','pics/icons/text_heading_1.png','wiki');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('heading2','!!text','pics/icons/text_heading_2.png','wiki');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('heading3','!!!text','pics/icons/text_heading_3.png','wiki');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('title bar','-=text=-','pics/icons/text_padding_top.png','wiki');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('box','^text^','pics/icons/box.png','wiki');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('dynamic content','{content id= }','pics/icons/database_refresh.png','wiki');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('hr','---','pics/icons/page.png','wiki');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('center text','::text::','pics/icons/text_align_center.png','wiki');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('colored text','~~--FF0000:text~~','pics/icons/palette.png','wiki');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('dynamic variable','%text%','pics/icons/book_open.png','wiki');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('image','{img src= width= height= align= desc= link= }','pics/icons/picture.png','wiki');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('list bullets', '*text', 'pics/icons/text_list_bullets.png', 'wiki');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('list numbers', '--text', 'pics/icons/text_list_numbers.png', 'wiki');


-- maps
INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('New wms Metadata','METADATA\r\n		\"wms_name\" \"myname\"\r\n 	"wms_srs" "EPSG:4326"\r\n 	"wms_server_version" " "\r\n 	"wms_layers" "mylayers"\r\n 	"wms_request" "myrequest"\r\n 	"wms_format" " "\r\n 	"wms_time" " "\r\n END', 'pics/icons/tag_blue_add.png','maps');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('New Class', 'CLASS\r\n EXPRESSION ()\r\n SYMBOL 0\r\n OUTLINECOLOR\r\n COLOR\r\n NAME "myclass" \r\nEND --end of class', 'pics/icons/application_add.png','maps');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('New Projection','PROJECTION\r\n "init=epsg:4326"\r\nEND','pics/icons/image_add.png','maps');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('New Query','--\r\n-- Start of query definitions\r\n--\r\n QUERYMAP\r\n STATUS ON\r\n STYLE HILITE\r\nEND','pics/icons/database_gear.png','maps');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('New Scalebar','--\r\n-- Start of scalebar\r\n--\r\nSCALEBAR\r\n IMAGECOLOR 255 255 255\r\n STYLE 1\r\n SIZE 400 2\r\n COLOR 0 0 0\r\n UNITS KILOMETERS\r\n INTERVALS 5\r\n STATUS ON\r\nEND','pics/icons/layout_add.png','maps');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('New Layer','LAYER\r\n NAME\r\n TYPE\r\n STATUS ON\r\n DATA "mydata"\r\nEND --end of layer', 'pics/icons/layers.png', 'maps');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('New Label','LABEL\r\n COLOR\r\n ANGLE\r\n FONT arial\r\n TYPE TRUETYPE\r\n POSITION\r\n PARTIALS TRUE\r\n SIZE 6\r\n BUFFER 0\r\n OUTLINECOLOR \r\nEND --end of label', 'pics/icons/comment_add.png', 'maps');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('New Reference','--\r\n--start of reference\r\n--\r\n REFERENCE\r\n SIZE 120 60\r\n STATUS ON\r\n EXTENT -180 -90 182 88\r\n OUTLINECOLOR 255 0 0\r\n IMAGE "myimagedata"\r\n COLOR -1 -1 -1\r\nEND','pics/icons/picture_add.png','maps');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('New Legend','--\r\n--start of Legend\r\n--\r\n LEGEND\r\n KEYSIZE 18 12\r\n POSTLABELCACHE TRUE\r\n STATUS ON\r\nEND','pics/icons/note_add.png','maps');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('New Web','--\r\n-- Start of web interface definition\r\n--\r\nWEB\r\n TEMPLATE "myfile/url"\r\n MINSCALE 1000\r\n MAXSCALE 40000\r\n IMAGEPATH "myimagepath"\r\n IMAGEURL "mypath"\r\nEND', 'pics/icons/world_link.png', 'maps');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('New Outputformat','OUTPUTFORMAT\r\n NAME\r\n DRIVER " "\r\n MIMETYPE "myimagetype"\r\n IMAGEMODE RGB\r\n EXTENSION "png"\r\nEND','pics/icons/newspaper_go.png','maps');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('New Mapfile','--\r\n-- Start of mapfile\r\n--\r\nNAME MYMAPFLE\r\n STATUS ON\r\nSIZE \r\nEXTENT\r\nUNITS \r\nSHAPEPATH " "\r\nIMAGETYPE " "\r\nFONTSET " "\r\nIMAGECOLOR -1 -1 -1\r\n\r\n--remove this text and add objects here\r\n\r\nEND -- end of mapfile','pics/icons/world_add.png','maps');


-- newsletters
INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('bold','__text__','pics/icons/text_bold.png','newsletters');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('italic','\'\'text\'\'','pics/icons/text_italic.png','newsletters');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('underline','===text===','pics/icons/text_underline.png','newsletters');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('external link','[http://example.com|text|nocache]','pics/icons/world_link.png','newsletters');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('heading1','!text','pics/icons/text_heading_1.png','newsletters');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('heading2','!!text','pics/icons/text_heading_2.png','newsletters');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('heading3','!!!text','pics/icons/text_heading_3.png','newsletters');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('hr', '---', 'pics/icons/page.png', 'newsletters');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('center text','::text::','pics/icons/text_align_center.png','newsletters');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('colored text','~~--FF0000:text~~','pics/icons/palette.png','newsletters');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('image','{img src= width= height= align= desc= link= }','pics/icons/picture.png','newsletters');


-- trackers
INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('bold','__text__','pics/icons/text_bold.png','trackers');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('italic','\'\'text\'\'','pics/icons/text_italic.png','trackers');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('underline','===text===','pics/icons/text_underline.png','trackers');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('table new','||r1c1|r1c2\nr2c1|r2c2||','pics/icons/table.png','trackers');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('external link','[http://example.com|text]','pics/icons/world_link.png','trackers');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('wiki link','((text))','pics/icons/page_link.png','trackers');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('heading1','!text','pics/icons/text_heading_1.png','trackers');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('heading2','!!text','pics/icons/text_heading_2.png','trackers');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('heading3','!!!text','pics/icons/text_heading_3.png','trackers');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('title bar','-=text=-','pics/icons/text_padding_top.png','trackers');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('box','^text^','pics/icons/box.png','trackers');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('dynamic content','{content id= }','pics/icons/database_refresh.png','trackers');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('hr','---','pics/icons/page.png','trackers');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('center text','::text::','pics/icons/text_align_center.png','trackers');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('colored text','~~--FF0000:text~~','pics/icons/palette.png','trackers');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('dynamic variable','%text%','pics/icons/book_open.png','trackers');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('image','{img src= width= height= align= desc= link= }','pics/icons/picture.png','trackers');


-- blogs
INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('bold','__text__','pics/icons/text_bold.png','blogs');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('italic','\'\'text\'\'','pics/icons/text_italic.png','blogs');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('underline','===text===','pics/icons/text_underline.png','blogs');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('table new','||r1c1|r1c2\nr2c1|r2c2||','pics/icons/table.png','blogs');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('external link','[http://example.com|text]','pics/icons/world_link.png','blogs');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('wiki link','((text))','pics/icons/page_link.png','blogs');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('heading1','!text','pics/icons/text_heading_1.png','blogs');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('heading2','!!text','pics/icons/text_heading_2.png','blogs');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('heading3','!!!text','pics/icons/text_heading_3.png','blogs');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('title bar','-=text=-','pics/icons/text_padding_top.png','blogs');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('box','^text^','pics/icons/box.png','blogs');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('dynamic content','{content id= }','pics/icons/database_refresh.png','blogs');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('hr','---','pics/icons/page.png','blogs');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('center text','::text::','pics/icons/text_align_center.png','blogs');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('colored text','~~--FF0000:text~~','pics/icons/palette.png','blogs');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('dynamic variable','%text%','pics/icons/book_open.png','blogs');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('image','{img src= width= height= align= desc= link= }','pics/icons/picture.png','blogs');


-- calendar
INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('bold','__text__','pics/icons/text_bold.png','calendar');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('italic','\'\'text\'\'','pics/icons/text_italic.png','calendar');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('underline','===text===','pics/icons/text_underline.png','calendar');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('table new','||r1c1|r1c2\nr2c1|r2c2||','pics/icons/table.png','calendar');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('external link','[http://example.com|text]','pics/icons/world_link.png','calendar');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('wiki link','((text))','pics/icons/page_link.png','calendar');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('heading1','!text','pics/icons/text_heading_1.png','calendar');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('heading2','!!text','pics/icons/text_heading_2.png','calendar');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('heading3','!!!text','pics/icons/text_heading_3.png','calendar');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('title bar','-=text=-','pics/icons/text_padding_top.png','calendar');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('box','^text^','pics/icons/box.png','calendar');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('dynamic content','{content id= }','pics/icons/database_refresh.png','calendar');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('hr','---','pics/icons/page.png','calendar');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('center text','::text::','pics/icons/text_align_center.png','calendar');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('colored text','~~--FF0000:text~~','pics/icons/palette.png','calendar');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('dynamic variable','%text%','pics/icons/book_open.png','calendar');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('image','{img src= width= height= align= desc= link= }','pics/icons/picture.png','calendar');


-- articles
INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('bold','__text__','pics/icons/text_bold.png','articles');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('italic','\'\'text\'\'','pics/icons/text_italic.png','articles');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('underline','===text===','pics/icons/text_underline.png','articles');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('table new','||r1c1|r1c2\nr2c1|r2c2||','pics/icons/table.png','articles');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('external link','[http://example.com|text]','pics/icons/world_link.png','articles');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('wiki link','((text))','pics/icons/page_link.png','articles');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('heading1','!text','pics/icons/text_heading_1.png','articles');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('heading2','!!text','pics/icons/text_heading_2.png','articles');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('heading3','!!!text','pics/icons/text_heading_3.png','articles');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('title bar','-=text=-','pics/icons/text_padding_top.png','articles');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('box','^text^','pics/icons/box.png','articles');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('dynamic content','{content id= }','pics/icons/database_refresh.png','articles');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('hr','---','pics/icons/page.png','articles');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('center text','::text::','pics/icons/text_align_center.png','articles');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('colored text','~~--FF0000:text~~','pics/icons/palette.png','articles');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('dynamic variable','%text%','pics/icons/book_open.png','articles');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('image','{img src= width= height= align= desc= link= }','pics/icons/picture.png','articles');


-- faqs
INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('bold','__text__','pics/icons/text_bold.png','faqs');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('italic','\'\'text\'\'','pics/icons/text_italic.png','faqs');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('underline','===text===','pics/icons/text_underline.png','faqs');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('table new','||r1c1|r1c2\nr2c1|r2c2||','pics/icons/table.png','faqs');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('external link','[http://example.com|text]','pics/icons/world_link.png','faqs');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('wiki link','((text))','pics/icons/page_link.png','faqs');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('heading1','!text','pics/icons/text_heading_1.png','faqs');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('heading2','!!text','pics/icons/text_heading_2.png','faqs');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('heading3','!!!text','pics/icons/text_heading_3.png','faqs');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('title bar','-=text=-','pics/icons/text_padding_top.png','faqs');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('box','^text^','pics/icons/box.png','faqs');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('dynamic content','{content id= }','pics/icons/database_refresh.png','faqs');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('hr','---','pics/icons/page.png','faqs');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('center text','::text::','pics/icons/text_align_center.png','faqs');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('colored text','~~--FF0000:text~~','pics/icons/palette.png','faqs');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('dynamic variable','%text%','pics/icons/book_open.png','faqs');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('image','{img src= width= height= align= desc= link= }','pics/icons/picture.png','faqs');


-- forums
INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('bold','__text__','pics/icons/text_bold.png','forums');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('italic','\'\'text\'\'','pics/icons/text_italic.png','forums');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('underline','===text===','pics/icons/text_underline.png','forums');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('table new','||r1c1|r1c2\nr2c1|r2c2||','pics/icons/table.png','forums');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('external link','[http://example.com|text]','pics/icons/world_link.png','forums');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('wiki link','((text))','pics/icons/page_link.png','forums');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('heading1','!text','pics/icons/text_heading_1.png','forums');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('heading2','!!text','pics/icons/text_heading_2.png','forums');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('heading3','!!!text','pics/icons/text_heading_3.png','forums');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('title bar','-=text=-','pics/icons/text_padding_top.png','forums');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('box','^text^','pics/icons/box.png','forums');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('dynamic content','{content id= }','pics/icons/database_refresh.png','forums');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('hr','---','pics/icons/page.png','forums');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('center text','::text::','pics/icons/text_align_center.png','forums');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('colored text','~~--FF0000:text~~','pics/icons/palette.png','forums');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('dynamic variable','%text%','pics/icons/book_open.png','forums');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon","tagcategory") VALUES ('image','{img src= width= height= align= desc= link= }','pics/icons/picture.png','forums');


--translated objects table
DROP TABLE IF EXISTS 'tiki_translated_objects';

CREATE TABLE 'tiki_translated_objects' (
  "traId" INTEGER,
  "type" varchar(50) NOT NULL,
  "objId" varchar(255) NOT NULL,
  "lang" varchar(16) default NULL,
  PRIMARY KEY (type, objId)
) ENGINE=MyISAM;

CREATE  INDEX "tiki_translated_objects_traId" ON "tiki_translated_objects"( "traId" );


--
-- Community tables begin
--

DROP TABLE IF EXISTS 'tiki_friends';

CREATE TABLE 'tiki_friends' (
  "user" char(200) NOT NULL default '',
  "friend" char(200) NOT NULL default '',
  PRIMARY KEY ("user","friend")
) ENGINE=MyISAM;


DROP TABLE IF EXISTS 'tiki_friendship_requests';

CREATE TABLE 'tiki_friendship_requests' (
  "userFrom" char(200) NOT NULL default '',
  "userTo" char(200) NOT NULL default '',
  "tstamp" timestamp(3) NOT NULL,
  PRIMARY KEY ("userFrom","userTo")
) ENGINE=MyISAM;


DROP TABLE IF EXISTS 'tiki_score';

CREATE TABLE 'tiki_score' (
  "event" varchar(40) NOT NULL default '',
  "score" bigint NOT NULL default '0',
  "expiration" bigint NOT NULL default '0',
  PRIMARY KEY ("event")
) ENGINE=MyISAM;



INSERT INTO "tiki_score" ("event","score","expiration") VALUES ('login',1,0);

INSERT INTO "tiki_score" ("event","score","expiration") VALUES ('login_remain',2,60);

INSERT INTO "tiki_score" ("event","score","expiration") VALUES ('profile_fill',10,0);

INSERT INTO "tiki_score" ("event","score","expiration") VALUES ('profile_see',2,0);

INSERT INTO "tiki_score" ("event","score","expiration") VALUES ('profile_is_seen',1,0);

INSERT INTO "tiki_score" ("event","score","expiration") VALUES ('friend_new',10,0);

INSERT INTO "tiki_score" ("event","score","expiration") VALUES ('message_receive',1,0);

INSERT INTO "tiki_score" ("event","score","expiration") VALUES ('message_send',2,0);

INSERT INTO "tiki_score" ("event","score","expiration") VALUES ('article_read',2,0);

INSERT INTO "tiki_score" ("event","score","expiration") VALUES ('article_comment',5,0);

INSERT INTO "tiki_score" ("event","score","expiration") VALUES ('article_new',20,0);

INSERT INTO "tiki_score" ("event","score","expiration") VALUES ('article_is_read',1,0);

INSERT INTO "tiki_score" ("event","score","expiration") VALUES ('article_is_commented',2,0);

INSERT INTO "tiki_score" ("event","score","expiration") VALUES ('fgallery_new',10,0);

INSERT INTO "tiki_score" ("event","score","expiration") VALUES ('fgallery_new_file',10,0);

INSERT INTO "tiki_score" ("event","score","expiration") VALUES ('fgallery_download',5,0);

INSERT INTO "tiki_score" ("event","score","expiration") VALUES ('fgallery_is_downloaded',5,0);

INSERT INTO "tiki_score" ("event","score","expiration") VALUES ('igallery_new',10,0);

INSERT INTO "tiki_score" ("event","score","expiration") VALUES ('igallery_new_img',6,0);

INSERT INTO "tiki_score" ("event","score","expiration") VALUES ('igallery_see_img',3,0);

INSERT INTO "tiki_score" ("event","score","expiration") VALUES ('igallery_img_seen',1,0);

INSERT INTO "tiki_score" ("event","score","expiration") VALUES ('blog_new',20,0);

INSERT INTO "tiki_score" ("event","score","expiration") VALUES ('blog_post',5,0);

INSERT INTO "tiki_score" ("event","score","expiration") VALUES ('blog_read',2,0);

INSERT INTO "tiki_score" ("event","score","expiration") VALUES ('blog_comment',2,0);

INSERT INTO "tiki_score" ("event","score","expiration") VALUES ('blog_is_read',3,0);

INSERT INTO "tiki_score" ("event","score","expiration") VALUES ('blog_is_commented',3,0);

INSERT INTO "tiki_score" ("event","score","expiration") VALUES ('wiki_new',10,0);

INSERT INTO "tiki_score" ("event","score","expiration") VALUES ('wiki_edit',5,0);

INSERT INTO "tiki_score" ("event","score","expiration") VALUES ('wiki_attach_file',3,0);


DROP TABLE IF EXISTS 'tiki_users_score';

CREATE TABLE 'tiki_users_score' (
  "user" char(200) NOT NULL default '',
  "event_id" char(40) NOT NULL default '',
  "expire" bigint NOT NULL default '0',
  "tstamp" timestamp(3) NOT NULL,
  PRIMARY KEY ("user","event_id")
) ENGINE=MyISAM;

CREATE  INDEX "tiki_users_score_user" ON "tiki_users_score"("user","event_id","expire");


--
-- Community tables end
--

--
-- Table structure for table tiki_file_handlers
--
-- Creation: Nov 02, 2004 at 05:59 PM
-- Last update: Nov 02, 2004 at 05:59 PM
--

DROP TABLE IF EXISTS 'tiki_file_handlers';

CREATE TABLE 'tiki_file_handlers' (
  "mime_type" varchar(64) default NULL,
  "cmd" varchar(238) default NULL
) ENGINE=MyISAM;


--
-- Table structure for table tiki_stats
--
-- Creation: Aug 04, 2005 at 05:59 PM
-- Last update: Aug 04, 2005 at 05:59 PM
--

DROP TABLE IF EXISTS 'tiki_stats';

CREATE TABLE 'tiki_stats' (
  "object" varchar(255) NOT NULL default '',
  "type" varchar(20) NOT NULL default '',
  "day" bigint NOT NULL default '0',
  "hits" bigint NOT NULL default '0',
  PRIMARY KEY ("object","type","day")
) ENGINE=MyISAM;


--
-- Table structure for table tiki_events
--
-- Creation: Aug 26, 2005 at 06:59 AM - mdavey
-- Last update: Sep 31, 2005 at 12:29 PM - mdavey
--

DROP TABLE IF EXISTS 'tiki_events';

CREATE TABLE 'tiki_events' (
  "callback_type" smallint NOT NULL default '3',
  order smallint NOT NULL default '50',
  "event" varchar(200) NOT NULL default '',
  "file" varchar(200) NOT NULL default '',  
  "object" varchar(200) NOT NULL default '',
  "method" varchar(200) NOT NULL default '',
  PRIMARY KEY ("callback_type","order")
) ENGINE=MyISAM;


INSERT INTO "tiki_events" ("callback_type","order","event","file","object","method") VALUES ('1', '20', 'user_registers', 'lib/registration/registrationlib.php', 'registrationlib', 'callback_tikiwiki_setup_custom_fields');

INSERT INTO "tiki_events" ("event","file","object","method") VALUES ('user_registers', 'lib/registration/registrationlib.php', 'registrationlib', 'callback_tikiwiki_save_registration');

INSERT INTO "tiki_events" ("callback_type","order","event","file","object","method") VALUES ('5', '20', 'user_registers', 'lib/registration/registrationlib.php', 'registrationlib', 'callback_logslib_user_registers');

INSERT INTO "tiki_events" ("callback_type","order","event","file","object","method") VALUES ('5', '25', 'user_registers', 'lib/registration/registrationlib.php', 'registrationlib', 'callback_tikiwiki_send_email');

INSERT INTO "tiki_events" ("callback_type","order","event","file","object","method") VALUES ('5', '30', 'user_registers', 'lib/registration/registrationlib.php', 'registrationlib', 'callback_tikimail_user_registers');


--
-- Table structure for table tiki_registration_fields
--
-- Creation: Aug 31, 2005 at 12:57 PM - mdavey
-- Last update: Aug 31, 2005 at 12:57 PM - mdavey
-- 

DROP TABLE IF EXISTS 'tiki_registration_fields';

CREATE TABLE 'tiki_registration_fields' (
  "id" INTEGER,
  "field" varchar(255) NOT NULL default '',
  "name" varchar(255) default NULL,
  "type" varchar(255) NOT NULL default 'text',
  show smallint NOT NULL default '0',
  "size" varchar(10) default '10',
  PRIMARY KEY ("id")
) ENGINE=MyISAM;


DROP TABLE IF EXISTS 'tiki_actionlog_conf';

CREATE TABLE 'tiki_actionlog_conf' (
  "id" INTEGER,
  "action" varchar(32) NOT NULL default '',
  "objectType" varchar(32) NOT NULL default '',
 status char(1) default '',
PRIMARY KEY (action, objectType),
KEY (id)
) ENGINE=MyISAM;

INSERT INTO "tiki_actionlog_conf" ("action","objectType","status") VALUES ('Created', 'wiki page', 'y');

INSERT INTO "tiki_actionlog_conf" ("action","objectType","status") VALUES ('Updated', 'wiki page', 'y');

INSERT INTO "tiki_actionlog_conf" ("action","objectType","status") VALUES ('Removed', 'wiki page', 'y');

INSERT INTO "tiki_actionlog_conf" ("action","objectType","status") VALUES ('Viewed', 'wiki page', 'n');

INSERT INTO "tiki_actionlog_conf" ("action","objectType","status") VALUES ('Viewed', 'forum', 'n');

INSERT INTO "tiki_actionlog_conf" ("action","objectType","status") VALUES ('Posted', 'forum', 'n');

INSERT INTO "tiki_actionlog_conf" ("action","objectType","status") VALUES ('Replied', 'forum', 'n');

INSERT INTO "tiki_actionlog_conf" ("action","objectType","status") VALUES ('Updated', 'forum', 'n');

INSERT INTO "tiki_actionlog_conf" ("action","objectType","status") VALUES ('Viewed', 'file gallery', 'n');

INSERT INTO "tiki_actionlog_conf" ("action","objectType","status") VALUES ('Viewed', 'image gallery', 'n');

INSERT INTO "tiki_actionlog_conf" ("action","objectType","status") VALUES ('Uploaded', 'file gallery', 'n');

INSERT INTO "tiki_actionlog_conf" ("action","objectType","status") VALUES ('Uploaded', 'image gallery', 'n');

INSERT INTO "tiki_actionlog_conf" ("action","objectType","status") VALUES ('Downloaded', 'file gallery', 'n');

INSERT INTO "tiki_actionlog_conf" ("action","objectType","status") VALUES ('*', 'category', 'n');

INSERT INTO "tiki_actionlog_conf" ("action","objectType","status") VALUES ('*', 'login', 'n');

INSERT INTO "tiki_actionlog_conf" ("action","objectType","status") VALUES ('Posted', 'message', 'n');

INSERT INTO "tiki_actionlog_conf" ("action","objectType","status") VALUES ('Replied', 'message', 'n');

INSERT INTO "tiki_actionlog_conf" ("action","objectType","status") VALUES ('Viewed', 'message', 'n');

INSERT INTO "tiki_actionlog_conf" ("action","objectType","status") VALUES ('Removed version', 'wiki page', 'n');

INSERT INTO "tiki_actionlog_conf" ("action","objectType","status") VALUES ('Removed last version', 'wiki page', 'n');

INSERT INTO "tiki_actionlog_conf" ("action","objectType","status") VALUES ('Rollback', 'wiki page', 'n');

INSERT INTO "tiki_actionlog_conf" ("action","objectType","status") VALUES ('Removed', 'forum', 'n');

INSERT INTO "tiki_actionlog_conf" ("action","objectType","status") VALUES ('Posted', 'comment', 'n');

INSERT INTO "tiki_actionlog_conf" ("action","objectType","status") VALUES ('Replied', 'comment', 'n');

INSERT INTO "tiki_actionlog_conf" ("action","objectType","status") VALUES ('Updated', 'comment', 'n');

INSERT INTO "tiki_actionlog_conf" ("action","objectType","status") VALUES ('Removed', 'comment', 'n');

INSERT INTO "tiki_actionlog_conf" ("action","objectType","status") VALUES ('Renamed', 'wiki page', 'n');

INSERT INTO "tiki_actionlog_conf" ("action","objectType","status") VALUES ('Created', 'sheet', 'n');

INSERT INTO "tiki_actionlog_conf" ("action","objectType","status") VALUES ('Updated', 'sheet', 'n');

INSERT INTO "tiki_actionlog_conf" ("action","objectType","status") VALUES ('Removed', 'sheet', 'n');

INSERT INTO "tiki_actionlog_conf" ("action","objectType","status") VALUES ('Viewed', 'sheet', 'n');

INSERT INTO "tiki_actionlog_conf" ("action","objectType","status") VALUES ('Viewed', 'blog', 'n');

INSERT INTO "tiki_actionlog_conf" ("action","objectType","status") VALUES ('Posted', 'blog', 'n');

INSERT INTO "tiki_actionlog_conf" ("action","objectType","status") VALUES ('Updated', 'blog', 'n');

INSERT INTO "tiki_actionlog_conf" ("action","objectType","status") VALUES ('Removed', 'blog', 'n');

-- ******************************************************


-- Table structure for folksonomy tables
--
-- Creation: Out 16, 2005 - batawata
-- Last update: Out 16, 2005 - batawata
-- 

DROP TABLE IF EXISTS 'tiki_freetags';

CREATE TABLE 'tiki_freetags' (
  "tagId" INTEGER,
  "tag" varchar(30) NOT NULL default '',
  "raw_tag" varchar(50) NOT NULL default '',
  "lang" varchar(16) NULL,
  PRIMARY KEY ("tagId")
) ENGINE=MyISAM;


DROP TABLE IF EXISTS 'tiki_freetagged_objects';

CREATE TABLE 'tiki_freetagged_objects' (
  "tagId" INTEGER,
  "objectId" bigint NOT NULL default 0,
  "user" varchar(200) NOT NULL default '',
  "created" bigint NOT NULL default '0',
  PRIMARY KEY ("tagId","user","objectId")
  KEY (tagId),
  KEY (user),
  KEY (objectId)
) ENGINE=MyISAM;



DROP TABLE IF EXISTS 'tiki_contributions';

CREATE TABLE 'tiki_contributions' (
  "contributionId" INTEGER,
  "name" varchar(100) default NULL,
  "description" varchar(250) default NULL,
  PRIMARY KEY ("contributionId")
) ENGINE=MyISAM;


DROP TABLE IF EXISTS 'tiki_contributions_assigned';

CREATE TABLE 'tiki_contributions_assigned' (
  "contributionId" bigint NOT NULL,
  "objectId" bigint NOT NULL,
  PRIMARY KEY ("objectId","contributionId")
) ENGINE=MyISAM;


DROP TABLE IF EXISTS 'tiki_webmail_contacts_ext';

CREATE TABLE tiki_webmail_contacts_ext (
  contactId bigint NOT NULL,
  fieldId bigint unsigned NOT NULL,
  value varchar(255) NOT NULL,
  hidden smallint NOT NULL,
  KEY contactId (contactId)
) ENGINE=MyISAM;


DROP TABLE IF EXISTS 'tiki_webmail_contacts_fields';

CREATE TABLE tiki_webmail_contacts_fields (
  fieldId INTEGER,
  user VARCHAR( 200 ) NOT NULL ,
  fieldname VARCHAR( 255 ) NOT NULL ,
  order smallint NOT NULL default '0',
  show char(1) NOT NULL default 'n',
  PRIMARY KEY ( fieldId ),
  "INDEX" ( user )
) ENGINE = MyISAM ;


-- ---------- mypage ----------------
CREATE TABLE tiki_mypage (
  id INTEGER,
  id_users bigint NOT NULL,
  created bigint NOT NULL,
  modified bigint NOT NULL,
  viewed bigint NOT NULL,
  width bigint NOT NULL,
  height bigint NOT NULL,
  name varchar(255) NOT NULL,
  description varchar(255) NOT NULL,
  bgcolor varchar(16) default NULL,
  winbgcolor varchar(16) default NULL,
  wintitlecolor varchar(16) default NULL,
  wintextcolor varchar(16) default NULL,
  bgimage varchar(255) default NULL,
  bgtype enum ('color', 'imageurl') default 'color' NOT NULL,
  winbgimage varchar(255) default NULL,
  winbgtype enum ('color', 'imageurl') default 'color' NOT NULL,
  PRIMARY KEY ("id")
  KEY id_users (id_users),
  KEY name (name)
) ENGINE=MyISAM;


CREATE TABLE tiki_mypagewin (
  id INTEGER,
  id_mypage bigint NOT NULL,
  created bigint NOT NULL,
  modified bigint NOT NULL,
  viewed bigint NOT NULL,
  title varchar(255) NOT NULL,
  inbody enum('n','y') NOT NULL default 'n',
  modal enum('n','y') NOT NULL default 'n',
  left bigint NOT NULL,
  top bigint NOT NULL,
  width bigint NOT NULL,
  height bigint NOT NULL,
  contenttype varchar(31) default NULL,
  config bytea,
  content bytea,
  PRIMARY KEY ("id")
  KEY id_mypage (id_mypage)
) ENGINE=MyISAM;


CREATE TABLE tiki_mypage_types (
  id INTEGER,
  created bigint NOT NULL,
  modified bigint NOT NULL,
  name varchar(255) NOT NULL,
  description varchar(255) NOT NULL,
  section varchar(255) default NULL,
  permissions varchar(255) default NULL,
  def_height bigint default NULL,
  def_width bigint default NULL,
  fix_dimensions enum('no','yes') NOT NULL,
  def_bgcolor varchar(8) default NULL,
  fix_bgcolor enum('no','yes') NOT NULL,
  templateuser bigint NOT NULL,
  PRIMARY KEY ("id")
  KEY name (name)
) ENGINE=MyISAM;


CREATE TABLE tiki_mypage_types_components (
  id_mypage_types bigint NOT NULL,
  compname varchar(255) NOT NULL,
  mincount bigint NOT NULL default '1',
  maxcount bigint NOT NULL default '1',
  KEY id_mypage_types (id_mypage_types)
) ENGINE=MyISAM;


CREATE TABLE tiki_pages_translation_bits (
  translation_bit_id INTEGER,
  page_id bigint NOT NULL,
  version integer NOT NULL,
  source_translation_bit bigint NULL,
  original_translation_bit bigint NULL,
  flags SET('critical') NOT NULL DEFAULT '',
  PRIMARY KEY (translation_bit_id),
  KEY(page_id),
  KEY(original_translation_bit),
  KEY(source_translation_bit)
);


-- ------------------------------------
;

