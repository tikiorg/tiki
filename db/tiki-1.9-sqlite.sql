-- $Header: /cvsroot/tikiwiki/tiki/db/tiki-1.9-sqlite.sql,v 1.13 2004-03-24 13:46:45 mose Exp $
-- phpMyAdmin MySQL-Dump
-- version 2.5.1
-- http://www.phpmyadmin.net/ (download page)
--
-- Host: localhost
-- Generation Time: Jul 13, 2003 at 02:09 AM
-- Server version: 4.0.13
-- PHP Version: 4.2.3
-- Database : `tikiwiki`
-- --------------------------------------------------------

--
-- Table structure for table `galaxia_activities`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "galaxia_activities";

CREATE TABLE "galaxia_activities" (
  "activityId" bigserial,
  "name" varchar(80) default NULL,
  "normalized_name" varchar(80) default NULL,
  "pId" bigint NOT NULL default '0',
  "type" varchar(12) CHECK ("type" IN ('start','end','split','switch','join','activity','standalone')) default NULL,
  "isAutoRouted" char(1) default NULL,
  "flowNum" bigint default NULL,
  "isInteractive" char(1) default NULL,
  "lastModif" bigint default NULL,
  "description" text,
  PRIMARY KEY ("activityId")
)   ;

-- --------------------------------------------------------

--
-- Table structure for table `galaxia_activity_roles`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "galaxia_activity_roles";

CREATE TABLE "galaxia_activity_roles" (
  "activityId" bigint NOT NULL default '0',
  "roleId" bigint NOT NULL default '0',
  PRIMARY KEY ("activityId","roleId")
) ;

-- --------------------------------------------------------

--
-- Table structure for table `galaxia_instance_activities`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "galaxia_instance_activities";

CREATE TABLE "galaxia_instance_activities" (
  "instanceId" bigint NOT NULL default '0',
  "activityId" bigint NOT NULL default '0',
  "started" bigint NOT NULL default '0',
  "ended" bigint NOT NULL default '0',
  "user" varchar(200) default NULL,
  "status" varchar(11) CHECK ("status" IN ('running','completed')) default NULL,
  PRIMARY KEY ("instanceId","activityId")
) ;

-- --------------------------------------------------------

--
-- Table structure for table `galaxia_instance_comments`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "galaxia_instance_comments";

CREATE TABLE "galaxia_instance_comments" (
  "cId" bigserial,
  "instanceId" bigint NOT NULL default '0',
  "user" varchar(200) default NULL,
  "activityId" bigint default NULL,
  "hash" varchar(32) default NULL,
  "title" varchar(250) default NULL,
  "comment" text,
  "activity" varchar(80) default NULL,
  "timestamp" bigint default NULL,
  PRIMARY KEY ("cId")
)   ;

-- --------------------------------------------------------

--
-- Table structure for table `galaxia_instances`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "galaxia_instances";

CREATE TABLE "galaxia_instances" (
  "instanceId" bigserial,
  "pId" bigint NOT NULL default '0',
  "started" bigint default NULL,
  "owner" varchar(200) default NULL,
  "nextActivity" bigint default NULL,
  "nextUser" varchar(200) default NULL,
  "ended" bigint default NULL,
  "status" varchar(11) CHECK ("status" IN ('active','exception','aborted','completed')) default NULL,
  "properties" bytea,
  PRIMARY KEY ("instanceId")
)   ;

-- --------------------------------------------------------

--
-- Table structure for table `galaxia_processes`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "galaxia_processes";

CREATE TABLE "galaxia_processes" (
  "pId" bigserial,
  "name" varchar(80) default NULL,
  "isValid" char(1) default NULL,
  "isActive" char(1) default NULL,
  "version" varchar(12) default NULL,
  "description" text,
  "lastModif" bigint default NULL,
  "normalized_name" varchar(80) default NULL,
  PRIMARY KEY ("pId")
)   ;

-- --------------------------------------------------------

--
-- Table structure for table `galaxia_roles`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "galaxia_roles";

CREATE TABLE "galaxia_roles" (
  "roleId" bigserial,
  "pId" bigint NOT NULL default '0',
  "lastModif" bigint default NULL,
  "name" varchar(80) default NULL,
  "description" text,
  PRIMARY KEY ("roleId")
)   ;

-- --------------------------------------------------------

--
-- Table structure for table `galaxia_transitions`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "galaxia_transitions";

CREATE TABLE "galaxia_transitions" (
  "pId" bigint NOT NULL default '0',
  "actFromId" bigint NOT NULL default '0',
  "actToId" bigint NOT NULL default '0',
  PRIMARY KEY ("actFromId","actToId")
) ;

-- --------------------------------------------------------

--
-- Table structure for table `galaxia_user_roles`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "galaxia_user_roles";

CREATE TABLE "galaxia_user_roles" (
  "pId" bigint NOT NULL default '0',
  "roleId" bigserial,
  "user" varchar(200) NOT NULL default '',
  PRIMARY KEY ("roleId","user")
)   ;

-- --------------------------------------------------------

--
-- Table structure for table `galaxia_workitems`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "galaxia_workitems";

CREATE TABLE "galaxia_workitems" (
  "itemId" bigserial,
  "instanceId" bigint NOT NULL default '0',
  "orderId" bigint NOT NULL default '0',
  "activityId" bigint NOT NULL default '0',
  "properties" bytea,
  "started" bigint default NULL,
  "ended" bigint default NULL,
  "user" varchar(200) default NULL,
  PRIMARY KEY ("itemId")
)   ;

-- --------------------------------------------------------

--
-- Table structure for table `messu_messages`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 08:29 PM
--

DROP TABLE "messu_messages";

CREATE TABLE "messu_messages" (
  "msgId" bigserial,
  "user" varchar(200) NOT NULL default '',
  "user_from" varchar(200) NOT NULL default '',
  "user_to" text,
  "user_cc" text,
  "user_bcc" text,
  "subject" varchar(255) default NULL,
  "body" text,
  "hash" varchar(32) default NULL,
  "date" bigint default NULL,
  "isRead" char(1) default NULL,
  "isReplied" char(1) default NULL,
  "isFlagged" char(1) default NULL,
  "priority" smallint default NULL,
  PRIMARY KEY ("msgId")
)   ;

-- --------------------------------------------------------


DROP TABLE "sessions";

CREATE TABLE "sessions"(
  "SESSKEY" char(32) NOT NULL,
  "EXPIRY" bigint unsigned NOT NULL,
  "EXPIREREF" varchar(64),
  "DATA" text NOT NULL,
  PRIMARY KEY ("SESSKEY")

) ;

CREATE  INDEX "sessions_EXPIRY" ON "sessions"("EXPIRY");

--
-- Table structure for table `tiki_actionlog`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 13, 2003 at 12:29 AM
--

DROP TABLE "tiki_actionlog";

CREATE TABLE "tiki_actionlog" (
  "action" varchar(255) NOT NULL default '',
  "lastModif" bigint default NULL,
  "pageName" varchar(200) default NULL,
  "user" varchar(200) default NULL,
  "ip" varchar(15) default NULL,
  "comment" varchar(200) default NULL
) ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_articles`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 13, 2003 at 01:30 AM
-- Last check: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_articles";

CREATE TABLE "tiki_articles" (
  "articleId" serial,
  "title" varchar(80) default NULL,
  "state" char(1) default 's',
  "authorName" varchar(60) default NULL,
  "topicId" bigint default NULL,
  "topicName" varchar(40) default NULL,
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
  "body" text,
  "hash" varchar(32) default NULL,
  "author" varchar(200) default NULL,
  "reads" bigint default NULL,
  "votes" integer default NULL,
  "points" bigint default NULL,
  "type" varchar(50) default NULL,
  "rating" decimal(3,2) default NULL,
  "isfloat" char(1) default NULL,
  PRIMARY KEY ("articleId")





)   ;

CREATE  INDEX "tiki_articles_title" ON "tiki_articles"("title");
CREATE  INDEX "tiki_articles_heading" ON "tiki_articles"("heading");
CREATE  INDEX "tiki_articles_body" ON "tiki_articles"("body");
CREATE  INDEX "tiki_articles_reads" ON "tiki_articles"("reads");
CREATE  INDEX "tiki_articles_ft" ON "tiki_articles"("title","heading","body");
-- --------------------------------------------------------

DROP TABLE "tiki_article_types";

CREATE TABLE "tiki_article_types" (
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
  "creator_edit" varchar(1) default NULL,
  "comment_can_rate_article" char(1) default NULL,
  PRIMARY KEY ("type")
)  ;


INSERT INTO "tiki_article_types" ("type") VALUES ('Article');

INSERT INTO "tiki_article_types" ("type","use_ratings") VALUES ('Review','y');

INSERT INTO "tiki_article_types" ("type","show_post_expire") VALUES ('Event','n');

INSERT INTO "tiki_article_types" ("type","show_post_expire","heading_only","allow_comments") VALUES ('Classified','n','y','n');


--
-- Table structure for table `tiki_banners`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_banners";

CREATE TABLE "tiki_banners" (
  "bannerId" bigserial,
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
)   ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_banning`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_banning";

CREATE TABLE "tiki_banning" (
  "banId" bigserial,
  "mode" varchar(6) CHECK ("mode" IN ('user','ip')) default NULL,
  "title" varchar(200) default NULL,
  "ip1" char(3) default NULL,
  "ip2" char(3) default NULL,
  "ip3" char(3) default NULL,
  "ip4" char(3) default NULL,
  "user" varchar(200) default NULL,
  "date_from" timestamp(3) NOT NULL,
  "date_to" timestamp(3) NOT NULL,
  "use_dates" char(1) default NULL,
  "created" bigint default NULL,
  "message" text,
  PRIMARY KEY ("banId")
)   ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_banning_sections`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_banning_sections";

CREATE TABLE "tiki_banning_sections" (
  "banId" bigint NOT NULL default '0',
  "section" varchar(100) NOT NULL default '',
  PRIMARY KEY ("banId","section")
) ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_blog_activity`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 04:52 PM
--

DROP TABLE "tiki_blog_activity";

CREATE TABLE "tiki_blog_activity" (
  "blogId" integer NOT NULL default '0',
  "day" bigint NOT NULL default '0',
  "posts" integer default NULL,
  PRIMARY KEY ("blogId","day")
) ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_blog_posts`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 04:52 PM
-- Last check: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_blog_posts";

CREATE TABLE "tiki_blog_posts" (
  "postId" serial,
  "blogId" integer NOT NULL default '0',
  "data" text,
  "data_size" bigint unsigned NOT NULL default '0',
  "created" bigint default NULL,
  "user" varchar(200) default NULL,
  "priv" varchar(1) default NULL,
  "trackbacks_to" text,
  "trackbacks_from" text,
  "title" varchar(80) default NULL,
  PRIMARY KEY ("postId")




)   ;

CREATE  INDEX "tiki_blog_posts_data" ON "tiki_blog_posts"("data");
CREATE  INDEX "tiki_blog_posts_blogId" ON "tiki_blog_posts"("blogId");
CREATE  INDEX "tiki_blog_posts_created" ON "tiki_blog_posts"("created");
CREATE  INDEX "tiki_blog_posts_ft" ON "tiki_blog_posts"("data","title");
-- --------------------------------------------------------

--
-- Table structure for table `tiki_blog_posts_images`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_blog_posts_images";

CREATE TABLE "tiki_blog_posts_images" (
  "imgId" bigserial,
  "postId" bigint NOT NULL default '0',
  "filename" varchar(80) default NULL,
  "filetype" varchar(80) default NULL,
  "filesize" bigint default NULL,
  "data" bytea,
  PRIMARY KEY ("imgId")
)   ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_blogs`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 13, 2003 at 01:07 AM
-- Last check: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_blogs";

CREATE TABLE "tiki_blogs" (
  "blogId" serial,
  "created" bigint default NULL,
  "lastModif" bigint default NULL,
  "title" varchar(200) default NULL,
  "description" text,
  "user" varchar(200) default NULL,
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




)   ;

CREATE  INDEX "tiki_blogs_title" ON "tiki_blogs"("title");
CREATE  INDEX "tiki_blogs_description" ON "tiki_blogs"("description");
CREATE  INDEX "tiki_blogs_hits" ON "tiki_blogs"("hits");
CREATE  INDEX "tiki_blogs_ft" ON "tiki_blogs"("title","description");
-- --------------------------------------------------------

--
-- Table structure for table `tiki_calendar_categories`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 07:05 AM
--

DROP TABLE "tiki_calendar_categories";

CREATE TABLE "tiki_calendar_categories" (
  "calcatId" bigserial,
  "calendarId" bigint NOT NULL default '0',
  "name" varchar(255) NOT NULL default '',
  PRIMARY KEY ("calcatId")

)   ;

CREATE UNIQUE INDEX "tiki_calendar_categories_catname" ON "tiki_calendar_categories"("calendarId","name");
-- --------------------------------------------------------

--
-- Table structure for table `tiki_calendar_items`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 07:43 AM
--

DROP TABLE "tiki_calendar_items";

CREATE TABLE "tiki_calendar_items" (
  "calitemId" bigserial,
  "calendarId" bigint NOT NULL default '0',
  "start" bigint NOT NULL default '0',
  "end" bigint NOT NULL default '0',
  "locationId" bigint default NULL,
  "categoryId" bigint default NULL,
  "priority" varchar(3) CHECK ("priority" IN ('1','2','3','4','5','6','7','8','9')) NOT NULL default '1',
  "status" varchar(3) CHECK ("status" IN ('0','1','2')) NOT NULL default '0',
  "url" varchar(255) default NULL,
  "lang" char(2) NOT NULL default 'en',
  "name" varchar(255) NOT NULL default '',
  "description" bytea,
  "user" varchar(40) default NULL,
  "created" bigint NOT NULL default '0',
  "lastmodif" bigint NOT NULL default '0',
  PRIMARY KEY ("calitemId")

)   ;

CREATE  INDEX "tiki_calendar_items_calendarId" ON "tiki_calendar_items"("calendarId");
-- --------------------------------------------------------

--
-- Table structure for table `tiki_calendar_locations`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 07:05 AM
--

DROP TABLE "tiki_calendar_locations";

CREATE TABLE "tiki_calendar_locations" (
  "callocId" bigserial,
  "calendarId" bigint NOT NULL default '0',
  "name" varchar(255) NOT NULL default '',
  "description" bytea,
  PRIMARY KEY ("callocId")

)   ;

CREATE UNIQUE INDEX "tiki_calendar_locations_locname" ON "tiki_calendar_locations"("calendarId","name");
-- --------------------------------------------------------

--
-- Table structure for table `tiki_calendar_roles`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_calendar_roles";

CREATE TABLE "tiki_calendar_roles" (
  "calitemId" bigint NOT NULL default '0',
  "username" varchar(40) NOT NULL default '',
  "role" varchar(3) CHECK ("role" IN ('0','1','2','3','6')) NOT NULL default '0',
  PRIMARY KEY ("calitemId","username","role")
) ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_calendars`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 05, 2003 at 02:03 PM
--

DROP TABLE "tiki_calendars";

CREATE TABLE "tiki_calendars" (
  "calendarId" bigserial,
  "name" varchar(80) NOT NULL default '',
  "description" varchar(255) default NULL,
  "user" varchar(40) NOT NULL default '',
  "customlocations" varchar(3) CHECK ("customlocations" IN ('n','y')) NOT NULL default 'n',
  "customcategories" varchar(3) CHECK ("customcategories" IN ('n','y')) NOT NULL default 'n',
  "customlanguages" varchar(3) CHECK ("customlanguages" IN ('n','y')) NOT NULL default 'n',
  "custompriorities" varchar(3) CHECK ("custompriorities" IN ('n','y')) NOT NULL default 'n',
  "customparticipants" varchar(3) CHECK ("customparticipants" IN ('n','y')) NOT NULL default 'n',
  "created" bigint NOT NULL default '0',
  "lastmodif" bigint NOT NULL default '0',
  PRIMARY KEY ("calendarId")
)   ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_categories`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 04, 2003 at 09:47 PM
--

DROP TABLE "tiki_categories";

CREATE TABLE "tiki_categories" (
  "categId" bigserial,
  "name" varchar(100) default NULL,
  "description" varchar(250) default NULL,
  "parentId" bigint default NULL,
  "hits" integer default NULL,
  PRIMARY KEY ("categId")
)   ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_categorized_objects`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 13, 2003 at 01:09 AM
--

DROP TABLE "tiki_categorized_objects";

CREATE TABLE "tiki_categorized_objects" (
  "catObjectId" bigserial,
  "type" varchar(50) default NULL,
  "objId" varchar(255) default NULL,
  "description" text,
  "created" bigint default NULL,
  "name" varchar(200) default NULL,
  "href" varchar(200) default NULL,
  "hits" integer default NULL,
  PRIMARY KEY ("catObjectId")
)   ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_category_objects`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 13, 2003 at 01:09 AM
--

DROP TABLE "tiki_category_objects";

CREATE TABLE "tiki_category_objects" (
  "catObjectId" bigint NOT NULL default '0',
  "categId" bigint NOT NULL default '0',
  PRIMARY KEY ("catObjectId","categId")
) ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_category_sites`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 07, 2003 at 01:53 AM
--

DROP TABLE "tiki_category_sites";

CREATE TABLE "tiki_category_sites" (
  "categId" bigint NOT NULL default '0',
  "siteId" bigint NOT NULL default '0',
  PRIMARY KEY ("categId","siteId")
) ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_chart_items`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_chart_items";

CREATE TABLE "tiki_chart_items" (
  "itemId" bigserial,
  "title" varchar(250) default NULL,
  "description" text,
  "chartId" bigint NOT NULL default '0',
  "created" bigint default NULL,
  "URL" varchar(250) default NULL,
  "votes" bigint default NULL,
  "points" bigint default NULL,
  "average" decimal(4,2) default NULL,
  PRIMARY KEY ("itemId")
)   ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_charts`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 06, 2003 at 08:14 AM
--

DROP TABLE "tiki_charts";

CREATE TABLE "tiki_charts" (
  "chartId" bigserial,
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
)   ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_charts_rankings`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_charts_rankings";

CREATE TABLE "tiki_charts_rankings" (
  "chartId" bigint NOT NULL default '0',
  "itemId" bigint NOT NULL default '0',
  "position" bigint NOT NULL default '0',
  "timestamp" bigint NOT NULL default '0',
  "lastPosition" bigint NOT NULL default '0',
  "period" bigint NOT NULL default '0',
  "rvotes" bigint NOT NULL default '0',
  "raverage" decimal(4,2) NOT NULL default '0.00',
  PRIMARY KEY ("chartId","itemId","period")
) ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_charts_votes`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_charts_votes";

CREATE TABLE "tiki_charts_votes" (
  "user" varchar(200) NOT NULL default '',
  "itemId" bigint NOT NULL default '0',
  "timestamp" bigint default NULL,
  "chartId" bigint default NULL,
  PRIMARY KEY ("user","itemId")
) ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_chat_channels`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_chat_channels";

CREATE TABLE "tiki_chat_channels" (
  "channelId" serial,
  "name" varchar(30) default NULL,
  "description" varchar(250) default NULL,
  "max_users" integer default NULL,
  "mode" char(1) default NULL,
  "moderator" varchar(200) default NULL,
  "active" char(1) default NULL,
  "refresh" integer default NULL,
  PRIMARY KEY ("channelId")
)   ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_chat_messages`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_chat_messages";

CREATE TABLE "tiki_chat_messages" (
  "messageId" serial,
  "channelId" integer NOT NULL default '0',
  "data" varchar(255) default NULL,
  "poster" varchar(200) NOT NULL default 'anonymous',
  "timestamp" bigint default NULL,
  PRIMARY KEY ("messageId")
)   ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_chat_users`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_chat_users";

CREATE TABLE "tiki_chat_users" (
  "nickname" varchar(200) NOT NULL default '',
  "channelId" integer NOT NULL default '0',
  "timestamp" bigint default NULL,
  PRIMARY KEY ("nickname","channelId")
) ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_comments`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 10:56 PM
-- Last check: Jul 11, 2003 at 01:52 AM
--

DROP TABLE "tiki_comments";

CREATE TABLE "tiki_comments" (
  "threadId" bigserial,
  "object" varchar(255) NOT NULL default '',
  "objectType" varchar(32) NOT NULL default '',
  "parentId" bigint default NULL,
  "userName" varchar(200) default NULL,
  "commentDate" bigint default NULL,
  "hits" integer default NULL,
  "type" char(1) default NULL,
  "points" decimal(8,2) default NULL,
  "votes" integer default NULL,
  "average" decimal(8,4) default NULL,
  "title" varchar(100) default NULL,
  "data" text,
  "hash" varchar(32) default NULL,
  "user_ip" varchar(15) default NULL,
  "summary" varchar(240) default NULL,
  "smiley" varchar(80) default NULL,
  "message_id" varchar(250) default NULL,
  "in_reply_to" varchar(250) default NULL,
  "comment_rating" smallint default NULL,  
  PRIMARY KEY ("threadId")






)   ;

CREATE  INDEX "tiki_comments_title" ON "tiki_comments"("title");
CREATE  INDEX "tiki_comments_data" ON "tiki_comments"("data");
CREATE  INDEX "tiki_comments_object" ON "tiki_comments"("object");
CREATE  INDEX "tiki_comments_hits" ON "tiki_comments"("hits");
CREATE  INDEX "tiki_comments_tc_pi" ON "tiki_comments"("parentId");
CREATE  INDEX "tiki_comments_ft" ON "tiki_comments"("title","data");
-- --------------------------------------------------------

--
-- Table structure for table `tiki_content`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_content";

CREATE TABLE "tiki_content" (
  "contentId" serial,
  "description" text,
  PRIMARY KEY ("contentId")
)   ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_content_templates`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 12:37 AM
--

DROP TABLE "tiki_content_templates";

CREATE TABLE "tiki_content_templates" (
  "templateId" bigserial,
  "content" bytea,
  "name" varchar(200) default NULL,
  "created" bigint default NULL,
  PRIMARY KEY ("templateId")
)   ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_content_templates_sections`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 12:37 AM
--

DROP TABLE "tiki_content_templates_sections";

CREATE TABLE "tiki_content_templates_sections" (
  "templateId" bigint NOT NULL default '0',
  "section" varchar(250) NOT NULL default '',
  PRIMARY KEY ("templateId","section")
) ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_cookies`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 10, 2003 at 04:00 AM
--

DROP TABLE "tiki_cookies";

CREATE TABLE "tiki_cookies" (
  "cookieId" bigserial,
  "cookie" varchar(255) default NULL,
  PRIMARY KEY ("cookieId")
)   ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_copyrights`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_copyrights";

CREATE TABLE "tiki_copyrights" (
  "copyrightId" bigserial,
  "page" varchar(200) default NULL,
  "title" varchar(200) default NULL,
  "year" bigint default NULL,
  "authors" varchar(200) default NULL,
  "copyright_order" bigint default NULL,
  "userName" varchar(200) default NULL,
  PRIMARY KEY ("copyrightId")
)   ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_directory_categories`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 08:59 PM
--

DROP TABLE "tiki_directory_categories";

CREATE TABLE "tiki_directory_categories" (
  "categId" bigserial,
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
)   ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_directory_search`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_directory_search";

CREATE TABLE "tiki_directory_search" (
  "term" varchar(250) NOT NULL default '',
  "hits" bigint default NULL,
  PRIMARY KEY ("term")
) ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_directory_sites`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 07:32 PM
--

DROP TABLE "tiki_directory_sites";

CREATE TABLE "tiki_directory_sites" (
  "siteId" bigserial,
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

)   ;

CREATE  INDEX "tiki_directory_sites_ft" ON "tiki_directory_sites"("name","description");
-- --------------------------------------------------------

--
-- Table structure for table `tiki_drawings`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 08, 2003 at 05:02 AM
--

DROP TABLE "tiki_drawings";

CREATE TABLE "tiki_drawings" (
  "drawId" bigserial,
  "version" integer default NULL,
  "name" varchar(250) default NULL,
  "filename_draw" varchar(250) default NULL,
  "filename_pad" varchar(250) default NULL,
  "timestamp" bigint default NULL,
  "user" varchar(200) default NULL,
  PRIMARY KEY ("drawId")
)   ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_dsn`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_dsn";

CREATE TABLE "tiki_dsn" (
  "dsnId" bigserial,
  "name" varchar(200) NOT NULL default '',
  "dsn" varchar(255) default NULL,
  PRIMARY KEY ("dsnId")
)   ;

-- --------------------------------------------------------


DROP TABLE "tiki_dynamic_variables";

CREATE TABLE "tiki_dynamic_variables" (
  "name" varchar(40) NOT NULL,
  "DATA" text,
  PRIMARY KEY ("name")
);


--
-- Table structure for table `tiki_eph`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 06, 2003 at 08:23 AM
--

DROP TABLE "tiki_eph";

CREATE TABLE "tiki_eph" (
  "ephId" bigserial,
  "title" varchar(250) default NULL,
  "isFile" char(1) default NULL,
  "filename" varchar(250) default NULL,
  "filetype" varchar(250) default NULL,
  "filesize" varchar(250) default NULL,
  "data" bytea,
  "textdata" bytea,
  "publish" bigint default NULL,
  "hits" bigint default NULL,
  PRIMARY KEY ("ephId")
)   ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_extwiki`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_extwiki";

CREATE TABLE "tiki_extwiki" (
  "extwikiId" bigserial,
  "name" varchar(200) NOT NULL default '',
  "extwiki" varchar(255) default NULL,
  PRIMARY KEY ("extwikiId")
)   ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_faq_questions`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
-- Last check: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_faq_questions";

CREATE TABLE "tiki_faq_questions" (
  "questionId" bigserial,
  "faqId" bigint default NULL,
  "position" smallint default NULL,
  "question" text,
  "answer" text,
  PRIMARY KEY ("questionId")




)   ;

CREATE  INDEX "tiki_faq_questions_faqId" ON "tiki_faq_questions"("faqId");
CREATE  INDEX "tiki_faq_questions_question" ON "tiki_faq_questions"("question");
CREATE  INDEX "tiki_faq_questions_answer" ON "tiki_faq_questions"("answer");
CREATE  INDEX "tiki_faq_questions_ft" ON "tiki_faq_questions"("question","answer");
-- --------------------------------------------------------

--
-- Table structure for table `tiki_faqs`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 09:09 PM
-- Last check: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_faqs";

CREATE TABLE "tiki_faqs" (
  "faqId" bigserial,
  "title" varchar(200) default NULL,
  "description" text,
  "created" bigint default NULL,
  "questions" integer default NULL,
  "hits" integer default NULL,
  "canSuggest" char(1) default NULL,
  PRIMARY KEY ("faqId")




)   ;

CREATE  INDEX "tiki_faqs_title" ON "tiki_faqs"("title");
CREATE  INDEX "tiki_faqs_description" ON "tiki_faqs"("description");
CREATE  INDEX "tiki_faqs_hits" ON "tiki_faqs"("hits");
CREATE  INDEX "tiki_faqs_ft" ON "tiki_faqs"("title","description");
-- --------------------------------------------------------

--
-- Table structure for table `tiki_featured_links`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 11:08 PM
--

DROP TABLE "tiki_featured_links";

CREATE TABLE "tiki_featured_links" (
  "url" varchar(200) NOT NULL default '',
  "title" varchar(200) default NULL,
  "description" text,
  "hits" integer default NULL,
  "position" integer default NULL,
  "type" char(1) default NULL,
  PRIMARY KEY ("url")
) ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_file_galleries`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 13, 2003 at 01:13 AM
--

DROP TABLE "tiki_file_galleries";

CREATE TABLE "tiki_file_galleries" (
  "galleryId" bigserial,
  "name" varchar(80) NOT NULL default '',
  "description" text,
  "created" bigint default NULL,
  "visible" char(1) default NULL,
  "lastModif" bigint default NULL,
  "user" varchar(200) default NULL,
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
  PRIMARY KEY ("galleryId")
)   ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_files`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 13, 2003 at 01:13 AM
-- Last check: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_files";

CREATE TABLE "tiki_files" (
  "fileId" bigserial,
  "galleryId" bigint NOT NULL default '0',
  "name" varchar(200) NOT NULL default '',
  "description" text,
  "created" bigint default NULL,
  "filename" varchar(80) default NULL,
  "filesize" bigint default NULL,
  "filetype" varchar(250) default NULL,
  "data" bytea,
  "user" varchar(200) default NULL,
  "downloads" bigint default NULL,
  "votes" integer default NULL,
  "points" decimal(8,2) default NULL,
  "path" varchar(255) default NULL,
  "reference_url" varchar(250) default NULL,
  "is_reference" char(1) default NULL,
  "hash" varchar(32) default NULL,
  PRIMARY KEY ("fileId")




)   ;

CREATE  INDEX "tiki_files_name" ON "tiki_files"("name");
CREATE  INDEX "tiki_files_description" ON "tiki_files"("description");
CREATE  INDEX "tiki_files_downloads" ON "tiki_files"("downloads");
CREATE  INDEX "tiki_files_ft" ON "tiki_files"("name","description");
-- --------------------------------------------------------

--
-- Table structure for table `tiki_forum_attachments`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_forum_attachments";

CREATE TABLE "tiki_forum_attachments" (
  "attId" bigserial,
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
)   ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_forum_reads`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 07:17 PM
--

DROP TABLE "tiki_forum_reads";

CREATE TABLE "tiki_forum_reads" (
  "user" varchar(200) NOT NULL default '',
  "threadId" bigint NOT NULL default '0',
  "forumId" bigint default NULL,
  "timestamp" bigint default NULL,
  PRIMARY KEY ("user","threadId")
) ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_forums`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 11:14 PM
--

DROP TABLE "tiki_forums";

CREATE TABLE "tiki_forums" (
  "forumId" serial,
  "name" varchar(200) default NULL,
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
  PRIMARY KEY ("forumId")
)   ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_forums_queue`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_forums_queue";

CREATE TABLE "tiki_forums_queue" (
  "qId" bigserial,
  "object" varchar(32) default NULL,
  "parentId" bigint default NULL,
  "forumId" bigint default NULL,
  "timestamp" bigint default NULL,
  "user" varchar(200) default NULL,
  "title" varchar(240) default NULL,
  "data" text,
  "type" varchar(60) default NULL,
  "hash" varchar(32) default NULL,
  "topic_smiley" varchar(80) default NULL,
  "topic_title" varchar(240) default NULL,
  "summary" varchar(240) default NULL,
  PRIMARY KEY ("qId")
)   ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_forums_reported`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_forums_reported";

CREATE TABLE "tiki_forums_reported" (
  "threadId" bigint NOT NULL default '0',
  "forumId" bigint NOT NULL default '0',
  "parentId" bigint NOT NULL default '0',
  "user" varchar(200) default NULL,
  "timestamp" bigint default NULL,
  "reason" varchar(250) default NULL,
  PRIMARY KEY ("threadId")
) ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_galleries`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 08:59 PM
-- Last check: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_galleries";

CREATE TABLE "tiki_galleries" (
  "galleryId" bigserial,
  "name" varchar(80) NOT NULL default '',
  "description" text,
  "created" bigint default NULL,
  "lastModif" bigint default NULL,
  "visible" char(1) default NULL,
  "theme" varchar(60) default NULL,
  "user" varchar(200) default NULL,
  "hits" bigint default NULL,
  "maxRows" bigint default NULL,
  "rowImages" bigint default NULL,
  "thumbSizeX" bigint default NULL,
  "thumbSizeY" bigint default NULL,
  "public" char(1) default NULL,
  PRIMARY KEY ("galleryId")




)   ;

CREATE  INDEX "tiki_galleries_name" ON "tiki_galleries"("name");
CREATE  INDEX "tiki_galleries_description" ON "tiki_galleries"("description");
CREATE  INDEX "tiki_galleries_hits" ON "tiki_galleries"("hits");
CREATE  INDEX "tiki_galleries_ft" ON "tiki_galleries"("name","description");
-- --------------------------------------------------------

--
-- Table structure for table `tiki_galleries_scales`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_galleries_scales";

CREATE TABLE "tiki_galleries_scales" (
  "galleryId" bigint NOT NULL default '0',
  "xsize" bigint NOT NULL default '0',
  "ysize" bigint NOT NULL default '0',
  PRIMARY KEY ("galleryId","xsize","ysize")
) ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_games`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 05, 2003 at 08:23 PM
--

DROP TABLE "tiki_games";

CREATE TABLE "tiki_games" (
  "gameName" varchar(200) NOT NULL default '',
  "hits" integer default NULL,
  "votes" integer default NULL,
  "points" integer default NULL,
  PRIMARY KEY ("gameName")
) ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_group_inclusion`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 05, 2003 at 02:03 AM
--

DROP TABLE "tiki_group_inclusion";

CREATE TABLE "tiki_group_inclusion" (
  "groupName" varchar(255) NOT NULL default '',
  "includeGroup" varchar(255) NOT NULL default '',
  PRIMARY KEY ("groupName","includeGroup")
) ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_history`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 13, 2003 at 12:29 AM
--

DROP TABLE "tiki_history";

CREATE TABLE "tiki_history" (
  "pageName" varchar(160) NOT NULL default '',
  "version" integer NOT NULL default '0',
  "lastModif" bigint default NULL,
  "description" varchar(200) default NULL,
  "user" varchar(200) default NULL,
  "ip" varchar(15) default NULL,
  "comment" varchar(200) default NULL,
  "data" bytea,
  PRIMARY KEY ("pageName","version")
) ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_hotwords`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 10, 2003 at 11:04 PM
--

DROP TABLE "tiki_hotwords";

CREATE TABLE "tiki_hotwords" (
  "word" varchar(40) NOT NULL default '',
  "url" varchar(255) NOT NULL default '',
  PRIMARY KEY ("word")
) ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_html_pages`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_html_pages";

CREATE TABLE "tiki_html_pages" (
  "pageName" varchar(200) NOT NULL default '',
  "content" bytea,
  "refresh" bigint default NULL,
  "type" char(1) default NULL,
  "created" bigint default NULL,
  PRIMARY KEY ("pageName")
) ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_html_pages_dynamic_zones`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_html_pages_dynamic_zones";

CREATE TABLE "tiki_html_pages_dynamic_zones" (
  "pageName" varchar(40) NOT NULL default '',
  "zone" varchar(80) NOT NULL default '',
  "type" char(2) default NULL,
  "content" text,
  PRIMARY KEY ("pageName","zone")
) ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_images`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 08:29 PM
-- Last check: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_images";

CREATE TABLE "tiki_images" (
  "imageId" bigserial,
  "galleryId" bigint NOT NULL default '0',
  "name" varchar(200) NOT NULL default '',
  "description" text,
  "created" bigint default NULL,
  "user" varchar(200) default NULL,
  "hits" bigint default NULL,
  "path" varchar(255) default NULL,
  PRIMARY KEY ("imageId")







)   ;

CREATE  INDEX "tiki_images_name" ON "tiki_images"("name");
CREATE  INDEX "tiki_images_description" ON "tiki_images"("description");
CREATE  INDEX "tiki_images_hits" ON "tiki_images"("hits");
CREATE  INDEX "tiki_images_ti_gId" ON "tiki_images"("galleryId");
CREATE  INDEX "tiki_images_ti_cr" ON "tiki_images"("created");
CREATE  INDEX "tiki_images_ti_us" ON "tiki_images"("user");
CREATE  INDEX "tiki_images_ft" ON "tiki_images"("name","description");
-- --------------------------------------------------------

--
-- Table structure for table `tiki_images_data`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 12:49 PM
-- Last check: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_images_data";

CREATE TABLE "tiki_images_data" (
  "imageId" bigint NOT NULL default '0',
  "xsize" integer NOT NULL default '0',
  "ysize" integer NOT NULL default '0',
  "type" char(1) NOT NULL default '',
  "filesize" bigint default NULL,
  "filetype" varchar(80) default NULL,
  "filename" varchar(80) default NULL,
  "data" bytea,
  PRIMARY KEY ("imageId","xsize","ysize","type")

) ;

CREATE  INDEX "tiki_images_data_t_i_d_it" ON "tiki_images_data"("imageId","type");
-- --------------------------------------------------------

--
-- Table structure for table `tiki_language`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_language";

CREATE TABLE "tiki_language" (
  "source" bytea NOT NULL,
  "lang" char(2) NOT NULL default '',
  "tran" bytea,
  PRIMARY KEY ("source","lang")
) ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_languages`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_languages";

CREATE TABLE "tiki_languages" (
  "lang" char(2) NOT NULL default '',
  "language" varchar(255) default NULL,
  PRIMARY KEY ("lang")
) ;

-- --------------------------------------------------------
INSERT INTO tiki_languages VALUES('en','English');

-- --------------------------------------------------------

--
-- Table structure for table `tiki_link_cache`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 06:06 PM
--

DROP TABLE "tiki_link_cache";

CREATE TABLE "tiki_link_cache" (
  "cacheId" bigserial,
  "url" varchar(250) default NULL,
  "data" bytea,
  "refresh" bigint default NULL,
  PRIMARY KEY ("cacheId")
)   ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_links`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 11:39 PM
--

DROP TABLE "tiki_links";

CREATE TABLE "tiki_links" (
  "fromPage" varchar(160) NOT NULL default '',
  "toPage" varchar(160) NOT NULL default '',
  PRIMARY KEY ("fromPage","toPage")
) ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_live_support_events`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_live_support_events";

CREATE TABLE "tiki_live_support_events" (
  "eventId" bigserial,
  "reqId" varchar(32) NOT NULL default '',
  "type" varchar(40) default NULL,
  "seqId" bigint default NULL,
  "senderId" varchar(32) default NULL,
  "data" text,
  "timestamp" bigint default NULL,
  PRIMARY KEY ("eventId")
)   ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_live_support_message_comments`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_live_support_message_comments";

CREATE TABLE "tiki_live_support_message_comments" (
  "cId" bigserial,
  "msgId" bigint default NULL,
  "data" text,
  "timestamp" bigint default NULL,
  PRIMARY KEY ("cId")
)   ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_live_support_messages`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_live_support_messages";

CREATE TABLE "tiki_live_support_messages" (
  "msgId" bigserial,
  "data" text,
  "timestamp" bigint default NULL,
  "user" varchar(200) default NULL,
  "username" varchar(200) default NULL,
  "priority" smallint default NULL,
  "status" char(1) default NULL,
  "assigned_to" varchar(200) default NULL,
  "resolution" varchar(100) default NULL,
  "title" varchar(200) default NULL,
  "module" smallint default NULL,
  "email" varchar(250) default NULL,
  PRIMARY KEY ("msgId")
)   ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_live_support_modules`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_live_support_modules";

CREATE TABLE "tiki_live_support_modules" (
  "modId" serial,
  "name" varchar(90) default NULL,
  PRIMARY KEY ("modId")
)   ;

-- --------------------------------------------------------
INSERT INTO tiki_live_support_modules(name) VALUES('wiki');

INSERT INTO tiki_live_support_modules(name) VALUES('forums');

INSERT INTO tiki_live_support_modules(name) VALUES('image galleries');

INSERT INTO tiki_live_support_modules(name) VALUES('file galleries');

INSERT INTO tiki_live_support_modules(name) VALUES('directory');

INSERT INTO tiki_live_support_modules(name) VALUES('workflow');

INSERT INTO tiki_live_support_modules(name) VALUES('charts');

-- --------------------------------------------------------

--
-- Table structure for table `tiki_live_support_operators`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_live_support_operators";

CREATE TABLE "tiki_live_support_operators" (
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
) ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_live_support_requests`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_live_support_requests";

CREATE TABLE "tiki_live_support_requests" (
  "reqId" varchar(32) NOT NULL default '',
  "user" varchar(200) default NULL,
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
) ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_live_support_requests`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE `tiki_logs`;

CREATE TABLE "tiki_logs" (
  "logId" serial,
  "logtype" varchar(20) NOT NULL,
  "logmessage" text NOT NULL,
  "loguser" varchar(200) NOT NULL,
  "logip" varchar(200) NOT NULL,
  "logclient" text NOT NULL,
  "logtime" bigint NOT NULL,
  PRIMARY KEY ("logId")

) ;

CREATE  INDEX "tiki_logs_logtype" ON "tiki_logs"("logtype");

-- --------------------------------------------------------

--
-- Table structure for table `tiki_mail_events`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 11, 2003 at 05:28 AM
--

DROP TABLE "tiki_mail_events";

CREATE TABLE "tiki_mail_events" (
  "event" varchar(200) default NULL,
  "object" varchar(200) default NULL,
  "email" varchar(200) default NULL
) ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_mailin_accounts`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_mailin_accounts";

CREATE TABLE "tiki_mailin_accounts" (
  "accountId" bigserial,
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
  PRIMARY KEY ("accountId")
)   ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_menu_languages`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_menu_languages";

CREATE TABLE "tiki_menu_languages" (
  "menuId" serial,
  "language" char(2) NOT NULL default '',
  PRIMARY KEY ("menuId","language")
)   ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_menu_options`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Nov 21, 2003 at 07:05 AM
--

DROP TABLE "tiki_menu_options";

CREATE TABLE "tiki_menu_options" (
  "optionId" serial,
  "menuId" integer default NULL,
  "type" char(1) default NULL,
  "name" varchar(200) default NULL,
  "url" varchar(255) default NULL,
  "position" smallint default NULL,
  "section" varchar(255) default NULL,
  "perm" varchar(255) default NULL,
  "groupname" varchar(255) default NULL,
  PRIMARY KEY ("optionId")
)   ;

-- --------------------------------------------------------
INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Home','tiki-index.php',10,'','','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Chat','tiki-chat.php',15,'feature_chat','tiki_p_chat','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Contact us','tiki-contact.php',20,'feature_contact','','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Stats','tiki-stats.php',23,'feature_stats','tiki_p_view_stats','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Categories','tiki-browse_categories.php',25,'feature_categories','tiki_p_view_categories','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Games','tiki-games.php',30,'feature_games','tiki_p_play_games','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Calendar','tiki-calendar.php',35,'feature_calendar','tiki_p_view_calendar','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','(debug)',"javascript:toggle('debugconsole')",40,'feature_debug_console','tiki_p_admin','');


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','MyTiki (click!)','tiki-my_tiki.php',50,'','','Registered');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Preferences','tiki-user_preferences.php',55,'feature_userPreferences','','Registered');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Messages','messu-mailbox.php',60,'feature_messages','tiki_p_messages','Registered');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Tasks','tiki-user_tasks.php',65,'feature_tasks','tiki_p_tasks','Registered');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Bookmarks','tiki-user_bookmarks.php',70,'feature_user_bookmarks','tiki_p_create_bookmarks','Registered');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Modules','tiki-user_assigned_modules.php',75,'user_assigned_modules','tiki_p_configure_modules','Registered');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Newsreader','tiki-newsreader_servers.php',80,'feature_newsreader','tiki_p_newsreader','Registered');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Webmail','tiki-webmail.php',85,'feature_webmail','tiki_p_use_webmail','Registered');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Notepad','tiki-notepad_list.php',90,'feature_notepad','tiki_p_notepad','Registered');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','My files','tiki-userfiles.php',95,'feature_userfiles','tiki_p_userfiles','Registered');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','User menu','tiki-usermenu.php',100,'feature_usermenu','','Registered');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Mini calendar','tiki-minical.php',105,'feature_minical','','Registered');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','My watches','tiki-user_watches.php',110,'feature_user_watches','','Registered');


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','Workflow','tiki-g-user_processes.php',150,'feature_workflow','tiki_p_use_workflow','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Admin processes','tiki-g-admin_processes.php',155,'feature_workflow','tiki_p_admin_workflow','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Monitor processes','tiki-g-monitor_processes.php',160,'feature_workflow','tiki_p_admin_workflow','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Monitor activities','tiki-g-monitor_activities.php',165,'feature_workflow','tiki_p_admin_workflow','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Monitor instances','tiki-g-monitor_instances.php',170,'feature_workflow','tiki_p_admin_workflow','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','User processes','tiki-g-user_processes.php',175,'feature_workflow','tiki_p_use_workflow','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','User activities','tiki-g-user_activities.php',180,'feature_workflow','tiki_p_use_workflow','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','User instances','tiki-g-user_instances.php',185,'feature_workflow','tiki_p_use_workflow','');


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','Wiki','tiki-index.php',200,'feature_wiki','tiki_p_view','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Wiki Home','tiki-index.php',202,'feature_wiki','tiki_p_view','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Last Changes','tiki-lastchanges.php',205,'feature_wiki,feature_lastChanges','tiki_p_view','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Dump','dump/new.tar',210,'feature_wiki,feature_dump','tiki_p_view','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Rankings','tiki-wiki_rankings.php',215,'feature_wiki,feature_wiki_rankings','tiki_p_view','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','List pages','tiki-listpages.php',220,'feature_wiki,feature_listPages','tiki_p_view','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Orphan pages','tiki-orphan_pages.php',225,'feature_wiki,feature_listPages','tiki_p_view','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Sandbox','tiki-editpage.php?page=sandbox',230,'feature_wiki,feature_sandbox','tiki_p_view','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Print','tiki-print_pages.php',235,'feature_wiki,feature_wiki_multiprint','tiki_p_view','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Send pages','tiki-send_objects.php',240,'feature_wiki,feature_comm','tiki_p_view,tiki_p_send_pages','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Received pages','tiki-received_pages.php',245,'feature_wiki,feature_comm','tiki_p_view,tiki_p_admin_received_pages','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Structures','tiki-admin_structures.php',250,'feature_wiki','tiki_p_edit_structures','');


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','Image Galleries','tiki-galleries.php',300,'feature_galleries','tiki_p_view_image_gallery','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Galleries','tiki-galleries.php',305,'feature_galleries','tiki_p_view_image_gallery','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Rankings','tiki-galleries_rankings.php',310,'feature_galleries,feature_gal_rankings','tiki_p_view_image_gallery','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Upload image','tiki-upload_image.php',315,'feature_galleries','tiki_p_upload_images','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','System gallery','tiki-list_gallery.php?galleryId=0',320,'feature_galleries','tiki_p_admin_galleries','');


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','Articles','tiki-view_articles.php',350,'feature_articles','tiki_p_read_article','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Articles home','tiki-view_articles.php',355,'feature_articles','tiki_p_read_article','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','List articles','tiki-list_articles.php',360,'feature_articles','tiki_p_read_article','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Rankings','tiki-cms_rankings.php',365,'feature_articles,feature_cms_ranking','tiki_p_read_article','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Submit article','tiki-edit_submission.php',370,'feature_articles,feature_submissions','tiki_p_read_article,tiki_p_submit_article','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','View submissions','tiki-list_submissions.php',375,'feature_articles,feature_submissions','tiki_p_read_article,tiki_p_submit_article','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','View submissions','tiki-list_submissions.php',375,'feature_articles,feature_submissions','tiki_p_read_article,tiki_p_approve_submission','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','View submissions','tiki-list_submissions.php',375,'feature_articles,feature_submissions','tiki_p_read_article,tiki_p_remove_submission','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Edit article','tiki-edit_article.php',380,'feature_articles','tiki_p_read_article,tiki_p_edit_article','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Send articles','tiki-send_objects.php',385,'feature_articles,feature_comm','tiki_p_read_article,tiki_p_send_articles','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Received articles','tiki-send_objects.php',385,'feature_articles,feature_comm','tiki_p_read_article,tiki_p_send_articles','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Admin topics','tiki-admin_topics.php',390,'feature_articles','tiki_p_read_article,tiki_p_admin_cms','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Admin types','tiki-articles_types.php',395,'feature_articles','tiki_p_read_article,tiki_p_admin_cms','');


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','Blogs','tiki-list_blogs.php',450,'feature_blogs','tiki_p_read_blog','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','List blogs','tiki-list_blogs.php',455,'feature_blogs','tiki_p_read_blog','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Rankings','tiki-blogs_rankings.php',460,'feature_blogs,feature_blog_rankings','tiki_p_read_blog','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Create/Edit blog','tiki-edit_blog.php',465,'feature_blogs','tiki_p_read_blog,tiki_p_create_blogs','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Post','tiki-blog_post.php',470,'feature_blogs','tiki_p_read_blog,tiki_p_blog_post','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Admin posts','tiki-list_posts.php',475,'feature_blogs','tiki_p_read_blog,tiki_p_blog_admin','');


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','Forums','tiki-forums.php',500,'feature_forums','tiki_p_forum_read','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','List forums','tiki-forums.php',505,'feature_forums','tiki_p_forum_read','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Rankings','tiki-forum_rankings.php',510,'feature_forums,feature_forum_rankings','tiki_p_forum_read','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Admin forums','tiki-admin_forums.php',515,'feature_forums','tiki_p_forum_read,tiki_p_admin_forum','');


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','Directory','tiki-directory_browse.php',550,'feature_directory','tiki_p_view_directory','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Submit a new link','tiki-directory_add_site.php',555,'feature_directory','tiki_p_view_directory','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Browse directory','tiki-directory_browse.php',560,'feature_directory','tiki_p_view_directory','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Admin directory','tiki-directory_admin.php',565,'feature_directory','tiki_p_view_directory,tiki_p_admin_directory_cats','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Admin directory','tiki-directory_admin.php',565,'feature_directory','tiki_p_view_directory,tiki_p_admin_directory_sites','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Admin directory','tiki-directory_admin.php',565,'feature_directory','tiki_p_view_directory,tiki_p_validate_links','');


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','File Galleries','tiki-file_galleries.php',600,'feature_file_galleries','tiki_p_view_file_gallery','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','List galleries','tiki-file_galleries.php',605,'feature_file_galleries','tiki_p_view_file_gallery','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Rankings','tiki-file_galleries_rankings.php',610,'feature_file_galleries,feature_file_galleries_rankings','tiki_p_view_file_gallery','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Upload  "File"','tiki-upload_file.php',615,'feature_file_galleries','tiki_p_view_file_gallery,tiki_p_upload_files','');


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','FAQs','tiki-list_faqs.php',650,'feature_faqs','tiki_p_view_faqs','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','List FAQs','tiki-list_faqs.php',665,'feature_faqs','tiki_p_view_faqs','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Admin FAQs','tiki-list_faqs.php',660,'feature_faqs','tiki_p_admin_faqs','');


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','Maps','tiki-map.phtml',700,'feature_maps','tiki_p_map_view','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Mapfiles','tiki-map_edit.php',705,'feature_maps','tiki_p_map_view','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Layer management','tiki-map_upload.php',710,'feature_maps','tiki_p_map_edit','');


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','Quizzes','tiki-list_quizzes.php',750,'feature_quizzes','','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','List quizzes','tiki-list_quizzes.php',755,'feature_quizzes','','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Quiz stats','tiki-quiz_stats.php',760,'feature_quizzes','tiki_p_view_quiz_stats','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Admin quiz','tiki-edit_quiz.php',765,'feature_quizzes','tiki_p_admin_quizzes','');


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','Trackers','tiki-list_trackers.php',800,'feature_trackers','','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','List trackers','tiki-list_trackers.php',805,'feature_trackers','','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Admin trackers','tiki-admin_trackers.php',810,'feature_trackers','tiki_p_admin_trackers','');


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','Surveys','tiki-list_surveys.php',850,'feature_surveys','','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','List surveys','tiki-list_surveys.php',855,'feature_surveys','','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Stats','tiki-surveys_stats.php',860,'feature_surveys','tiki_p_view_survey_stats','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Admin surveys','tiki-admin_surveys.php',865,'feature_surveys','tiki_p_admin_surveys','');


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','Newsletters','tiki-newsletters.php',900,'feature_newsletters','','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Send newsletters','tiki-send_newsletters.php',905,'feature_newsletters','tiki_p_admin_newsletters','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Admin newsletters','tiki-admin_newsletters.php',910,'feature_newsletters','tiki_p_admin_newsletters','');


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','Ephemerides','tiki-eph.php',950,'feature_eph','','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Admin','tiki-eph_admin.php',955,'feature_eph','tiki_p_eph_admin','');


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','Charts','tiki-charts.php',1000,'feature_charts','','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Charts','tiki-admin_charts.php',1005,'feature_charts','tiki_p_admin_charts','');


INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','Admin (click!)','tiki-admin.php',1050,'','tiki_p_admin','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','Admin (click!)','tiki-admin.php',1050,'','tiki_p_admin_chat','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','Admin (click!)','tiki-admin.php',1050,'','tiki_p_admin_categories','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','Admin (click!)','tiki-admin.php',1050,'','tiki_p_admin_banners','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','Admin (click!)','tiki-admin.php',1050,'','tiki_p_edit_templates','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','Admin (click!)','tiki-admin.php',1050,'','tiki_p_edit_cookies','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','Admin (click!)','tiki-admin.php',1050,'','tiki_p_admin_dynamic','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','Admin (click!)','tiki-admin.php',1050,'','tiki_p_admin_mailin','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','Admin (click!)','tiki-admin.php',1050,'','tiki_p_edit_content_templates','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','Admin (click!)','tiki-admin.php',1050,'','tiki_p_edit_html_pages','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','Admin (click!)','tiki-admin.php',1050,'','tiki_p_view_referer_stats','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','Admin (click!)','tiki-admin.php',1050,'','tiki_p_admin_drawings','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','Admin (click!)','tiki-admin.php',1050,'','tiki_p_admin_shoutbox','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','Admin (click!)','tiki-admin.php',1050,'','tiki_p_admin_live_support','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','Admin (click!)','tiki-admin.php',1050,'','user_is_operator','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','Admin (click!)','tiki-admin.php',1050,'feature_integrator','tiki_p_admin_integrator','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Live support','tiki-live_support_admin.php',1055,'feature_live_support','tiki_p_live_support_admin','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Live support','tiki-live_support_admin.php',1055,'feature_live_support','user_is_operator','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Banning','tiki-admin_banning.php',1060,'feature_banning','tiki_p_admin_banning','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Calendar','tiki-admin_calendars.php',1065,'feature_calendar','tiki_p_admin_calendar','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Users','tiki-adminusers.php',1070,'','tiki_p_admin','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Groups','tiki-admingroups.php',1075,'','tiki_p_admin','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Cache','tiki-list_cache.php',1080,'','tiki_p_admin','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Modules','tiki-admin_modules.php',1085,'','tiki_p_admin','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Links','tiki-admin_links.php',1090,'','tiki_p_admin','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Hotwords','tiki-admin_hotwords.php',1095,'','tiki_p_admin','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','RSS modules','tiki-admin_rssmodules.php',1100,'','tiki_p_admin','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Menus','tiki-admin_menus.php',1105,'','tiki_p_admin','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Polls','tiki-admin_polls.php',1110,'','tiki_p_admin','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Backups','tiki-backup.php',1115,'','tiki_p_admin','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Mail notifications','tiki-admin_notifications.php',1120,'','tiki_p_admin','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Search stats','tiki-search_stats.php',1125,'','tiki_p_admin','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Theme control','tiki-theme_control.php',1130,'','tiki_p_admin','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','QuickTags','tiki-admin_quicktags.php',1135,'','tiki_p_admin','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Chat','tiki-admin_chat.php',1140,'','tiki_p_admin_chat','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Categories','tiki-admin_categories.php',1145,'','tiki_p_admin_categories','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Banners','tiki-list_banners.php',1150,'','tiki_p_admin_banners','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Edit templates','tiki-edit_templates.php',1155,'','tiki_p_edit_templates','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Drawings','tiki-admin_drawings.php',1160,'','tiki_p_admin_drawings','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Dynamic content','tiki-list_contents.php',1165,'','tiki_p_admin_dynamic','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Cookies','tiki-admin_cookies.php',1170,'','tiki_p_edit_cookies','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Mail-in','tiki-admin_mailin.php',1175,'','tiki_p_admin_mailin','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Content templates','tiki-admin_content_templates.php',1180,'','tiki_p_edit_content_templates','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','HTML pages','tiki-admin_html_pages.php',1185,'','tiki_p_edit_html_pages','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Shoutbox','tiki-shoutbox.php',1190,'','tiki_p_admin_shoutbox','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Shoutbox','tiki-admin_shoutbox_words.php',1191,'','tiki_p_admin_shoutbox','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Referer stats','tiki-referer_stats.php',1195,'','tiki_p_view_referer_stats','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Edit languages','tiki-edit_languages.php',1200,'','tiki_p_edit_languages,lang_use_db','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Integrator','tiki-admin_integrator.php',1205,'feature_integrator','tiki_p_admin_integrator','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Import PHPWiki Dump','tiki-import_phpwiki.php',1210,'','tiki_p_admin','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','phpinfo','tiki-phpinfo.php',1215,'','tiki_p_admin','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','DSN','tiki-admin_dsn.php',1220,'','tiki_p_admin','');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','External wikis','tiki-admin_external_wikis.php',1225,'','tiki_p_admin','');


-- Tiki Jukebox

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'s','Jukebox','tiki-jukebox_albums.php',620,'feature_jukebox','tiki_p_jukebox_albums', '');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','View Tracks','tiki-jukebox_tracks.php',625,'feature_jukebox','tiki_p_jukebox_tracks', '');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Upload Tracks','tiki-jukebox_upload.php',630,'feature_jukebox','tiki_p_jukebox_upload', '');

INSERT INTO "tiki_menu_options" ("menuId","type","name","url","position","section","perm","groupname") VALUES (42,'o','Admin','tiki-jukebox_admin.php',635,'feature_jukebox','tiki_p_jukebox_admin', '');


-- --------------------------------------------------------

--
-- Table structure for table `tiki_menus`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_menus";

CREATE TABLE "tiki_menus" (
  "menuId" serial,
  "name" varchar(200) NOT NULL default '',
  "description" text,
  "type" char(1) default NULL,
  PRIMARY KEY ("menuId")
)   ;

-- --------------------------------------------------------
INSERT INTO "tiki_menus" ("menuId","name","description","type") VALUES ('42','Application menu','Main extensive navigation menu','d');

-- --------------------------------------------------------

--
-- Table structure for table `tiki_minical_events`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 09, 2003 at 04:06 AM
--

DROP TABLE "tiki_minical_events";

CREATE TABLE "tiki_minical_events" (
  "user" varchar(200) default NULL,
  "eventId" bigserial,
  "title" varchar(250) default NULL,
  "description" text,
  "start" bigint default NULL,
  "end" bigint default NULL,
  "security" char(1) default NULL,
  "duration" smallint default NULL,
  "topicId" bigint default NULL,
  "reminded" char(1) default NULL,
  PRIMARY KEY ("eventId")
)   ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_minical_topics`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_minical_topics";

CREATE TABLE "tiki_minical_topics" (
  "user" varchar(200) default NULL,
  "topicId" bigserial,
  "name" varchar(250) default NULL,
  "filename" varchar(200) default NULL,
  "filetype" varchar(200) default NULL,
  "filesize" varchar(200) default NULL,
  "data" bytea,
  "path" varchar(250) default NULL,
  "isIcon" char(1) default NULL,
  PRIMARY KEY ("topicId")
)   ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_modules`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 11:44 PM
--

DROP TABLE "tiki_modules";

CREATE TABLE "tiki_modules" (
  "name" varchar(200) NOT NULL default '',
  "position" char(1) default NULL,
  "ord" smallint default NULL,
  "type" char(1) default NULL,
  "title" varchar(255) default NULL,
  "cache_time" bigint default NULL,
  "rows" smallint default NULL,
  "params" varchar(255) default NULL,
  "groups" text,
  PRIMARY KEY ("name")
) ;

-- --------------------------------------------------------
INSERT INTO "tiki_modules" ("name","position","ord","cache_time","groups") VALUES ('login_box','r',1,0,'a:2:{i:0;s:10:"Registered";i:1;s:9:"Anonymous";}');

INSERT INTO "tiki_modules" ("name","position","ord","cache_time","groups") VALUES ('application_menu','l',1,0,'a:2:{i:0;s:10:"Registered";i:1;s:9:"Anonymous";}');

INSERT INTO "tiki_modules" ("name","position","ord","cache_time","groups") VALUES ('assistant','l',10,0,'a:2:{i:0;s:10:"Registered";i:1;s:9:"Anonymous";}');

-- --------------------------------------------------------

--
-- Table structure for table `tiki_newsletter_subscriptions`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_newsletter_subscriptions";

CREATE TABLE "tiki_newsletter_subscriptions" (
  "nlId" bigint NOT NULL default '0',
  "email" varchar(255) NOT NULL default '',
  "code" varchar(32) default NULL,
  "valid" char(1) default NULL,
  "subscribed" bigint default NULL,
  PRIMARY KEY ("nlId","email")
) ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_newsletters`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_newsletters";

CREATE TABLE "tiki_newsletters" (
  "nlId" bigserial,
  "name" varchar(200) default NULL,
  "description" text,
  "created" bigint default NULL,
  "lastSent" bigint default NULL,
  "editions" bigint default NULL,
  "users" bigint default NULL,
  "allowUserSub" char(1) default 'y',
  "allowAnySub" char(1) default NULL,
  "unsubMsg" char(1) default 'y',
  "validateAddr" char(1) default 'y',
  "frequency" bigint default NULL,
  PRIMARY KEY ("nlId")
)   ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_newsreader_marks`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_newsreader_marks";

CREATE TABLE "tiki_newsreader_marks" (
  "user" varchar(200) NOT NULL default '',
  "serverId" bigint NOT NULL default '0',
  "groupName" varchar(255) NOT NULL default '',
  "timestamp" bigint NOT NULL default '0',
  PRIMARY KEY ("user","serverId","groupName")
) ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_newsreader_servers`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_newsreader_servers";

CREATE TABLE "tiki_newsreader_servers" (
  "user" varchar(200) NOT NULL default '',
  "serverId" bigserial,
  "server" varchar(250) default NULL,
  "port" smallint default NULL,
  "username" varchar(200) default NULL,
  "password" varchar(200) default NULL,
  PRIMARY KEY ("serverId")
)   ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_page_footnotes`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 10:00 AM
-- Last check: Jul 12, 2003 at 10:00 AM
--

DROP TABLE "tiki_page_footnotes";

CREATE TABLE "tiki_page_footnotes" (
  "user" varchar(200) NOT NULL default '',
  "pageName" varchar(250) NOT NULL default '',
  "data" text,
  PRIMARY KEY ("user","pageName")
) ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_pages`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 13, 2003 at 01:52 AM
-- Last check: Jul 12, 2003 at 10:01 AM
--

DROP TABLE "tiki_pages";

CREATE TABLE "tiki_pages" (
  "page_id" bigserial,
  "pageName" varchar(160) NOT NULL default '',
  "hits" integer default NULL,
  "data" text,
  "description" varchar(200) default NULL,
  "lastModif" bigint default NULL,
  "comment" varchar(200) default NULL,
  "version" integer NOT NULL default '0',
  "user" varchar(200) default NULL,
  "ip" varchar(15) default NULL,
  "flag" char(1) default NULL,
  "points" integer default NULL,
  "votes" integer default NULL,
  "cache" text,
  "wiki_cache" bigint default NULL,
  "cache_timestamp" bigint default NULL,
  "pageRank" decimal(4,3) default NULL,
  "creator" varchar(200) default NULL,
  "page_size" bigint unsigned default 0,
  PRIMARY KEY ("page_id")




)  ;

CREATE  INDEX "tiki_pages_data" ON "tiki_pages"("data");
CREATE  INDEX "tiki_pages_pageRank" ON "tiki_pages"("pageRank");
CREATE  INDEX "tiki_pages_ft" ON "tiki_pages"("pageName","description","data");
CREATE UNIQUE INDEX "tiki_pages_pageName" ON "tiki_pages"("pageName");
-- --------------------------------------------------------

--
-- Table structure for table `tiki_pageviews`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 13, 2003 at 01:52 AM
--

DROP TABLE "tiki_pageviews";

CREATE TABLE "tiki_pageviews" (
  "day" bigint NOT NULL default '0',
  "pageviews" bigint default NULL,
  PRIMARY KEY ("day")
) ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_poll_options`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 06, 2003 at 07:57 PM
--

DROP TABLE "tiki_poll_options";

CREATE TABLE "tiki_poll_options" (
  "pollId" integer NOT NULL default '0',
  "optionId" serial,
  "title" varchar(200) default NULL,
  "votes" integer default NULL,
  PRIMARY KEY ("optionId")
)   ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_polls`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 06, 2003 at 07:57 PM
--

DROP TABLE "tiki_polls";

CREATE TABLE "tiki_polls" (
  "pollId" serial,
  "title" varchar(200) default NULL,
  "votes" integer default NULL,
  "active" char(1) default NULL,
  "publishDate" bigint default NULL,
  PRIMARY KEY ("pollId")
)   ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_preferences`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 12:04 PM
--

DROP TABLE "tiki_preferences";

CREATE TABLE "tiki_preferences" (
  "name" varchar(40) NOT NULL default '',
  "value" varchar(250) default NULL,
  PRIMARY KEY ("name")
) ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_private_messages`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_private_messages";

CREATE TABLE "tiki_private_messages" (
  "messageId" serial,
  "toNickname" varchar(200) NOT NULL default '',
  "data" varchar(255) default NULL,
  "poster" varchar(200) NOT NULL default 'anonymous',
  "timestamp" bigint default NULL,
  PRIMARY KEY ("messageId")
)   ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_programmed_content`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_programmed_content";

CREATE TABLE "tiki_programmed_content" (
  "pId" serial,
  "contentId" integer NOT NULL default '0',
  "publishDate" bigint NOT NULL default '0',
  "data" text,
  PRIMARY KEY ("pId")
)   ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_quiz_question_options`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_quiz_question_options";

CREATE TABLE "tiki_quiz_question_options" (
  "optionId" bigserial,
  "questionId" bigint default NULL,
  "optionText" text,
  "points" smallint default NULL,
  PRIMARY KEY ("optionId")
)   ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_quiz_questions`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_quiz_questions";

CREATE TABLE "tiki_quiz_questions" (
  "questionId" bigserial,
  "quizId" bigint default NULL,
  "question" text,
  "position" smallint default NULL,
  "type" char(1) default NULL,
  "maxPoints" smallint default NULL,
  PRIMARY KEY ("questionId")
)   ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_quiz_results`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_quiz_results";

CREATE TABLE "tiki_quiz_results" (
  "resultId" bigserial,
  "quizId" bigint default NULL,
  "fromPoints" smallint default NULL,
  "toPoints" smallint default NULL,
  "answer" text,
  PRIMARY KEY ("resultId")
)   ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_quiz_stats`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_quiz_stats";

CREATE TABLE "tiki_quiz_stats" (
  "quizId" bigint NOT NULL default '0',
  "questionId" bigint NOT NULL default '0',
  "optionId" bigint NOT NULL default '0',
  "votes" bigint default NULL,
  PRIMARY KEY ("quizId","questionId","optionId")
) ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_quiz_stats_sum`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_quiz_stats_sum";

CREATE TABLE "tiki_quiz_stats_sum" (
  "quizId" bigint NOT NULL default '0',
  "quizName" varchar(255) default NULL,
  "timesTaken" bigint default NULL,
  "avgpoints" decimal(5,2) default NULL,
  "avgavg" decimal(5,2) default NULL,
  "avgtime" decimal(5,2) default NULL,
  PRIMARY KEY ("quizId")
) ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_quizzes`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_quizzes";

CREATE TABLE "tiki_quizzes" (
  "quizId" bigserial,
  "name" varchar(255) default NULL,
  "description" text,
  "canRepeat" char(1) default NULL,
  "storeResults" char(1) default NULL,
  "questionsPerPage" smallint default NULL,
  "timeLimited" char(1) default NULL,
  "timeLimit" bigint default NULL,
  "created" bigint default NULL,
  "taken" bigint default NULL,
  PRIMARY KEY ("quizId")
)   ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_received_articles`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_received_articles";

CREATE TABLE "tiki_received_articles" (
  "receivedArticleId" bigserial,
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
)   ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_received_pages`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 09, 2003 at 03:56 AM
--

DROP TABLE "tiki_received_pages";

CREATE TABLE "tiki_received_pages" (
  "receivedPageId" bigserial,
  "pageName" varchar(160) NOT NULL default '',
  "data" bytea,
  "description" varchar(200) default NULL,
  "comment" varchar(200) default NULL,
  "receivedFromSite" varchar(200) default NULL,
  "receivedFromUser" varchar(200) default NULL,
  "receivedDate" bigint default NULL,
  PRIMARY KEY ("receivedPageId")
)   ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_referer_stats`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 13, 2003 at 01:30 AM
--

DROP TABLE "tiki_referer_stats";

CREATE TABLE "tiki_referer_stats" (
  "referer" varchar(50) NOT NULL default '',
  "hits" bigint default NULL,
  "last" bigint default NULL,
  PRIMARY KEY ("referer")
) ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_related_categories`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_related_categories";

CREATE TABLE "tiki_related_categories" (
  "categId" bigint NOT NULL default '0',
  "relatedTo" bigint NOT NULL default '0',
  PRIMARY KEY ("categId","relatedTo")
) ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_rss_modules`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 10:19 AM
--

DROP TABLE "tiki_rss_modules";

CREATE TABLE "tiki_rss_modules" (
  "rssId" serial,
  "name" varchar(30) NOT NULL default '',
  "description" text,
  "url" varchar(255) NOT NULL default '',
  "refresh" integer default NULL,
  "lastUpdated" bigint default NULL,
  "showTitle" char(1) default 'n',
  "showPubDate" char(1) default 'n',
  "content" bytea,
  PRIMARY KEY ("rssId")
)   ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_rss_feeds`
--
-- Creation: Oct 14, 2003 at 20:34 PM
-- Last update: Oct 14, 2003 at 20:34 PM
--

DROP TABLE "tiki_rss_feeds";

CREATE TABLE "tiki_rss_feeds" (
  "name" varchar(30) NOT NULL default '',
  "rssVer" char(1) NOT NULL default '1',
  "refresh" integer default '300',
  "lastUpdated" bigint default NULL,
  "cache" bytea,
  PRIMARY KEY ("name","rssVer")
) ;

-- --------------------------------------------------------

DROP TABLE "tiki_searchindex";

CREATE TABLE "tiki_searchindex"(
  "searchword" varchar(80) NOT NULL default '',
  "location" varchar(80) NOT NULL default '',
  "page" varchar(255) NOT NULL default '',
  "count" bigint NOT NULL default '1',
  "last_update" bigint NOT NULL default '0',
  PRIMARY KEY ("searchword","location","page")

) ;

CREATE  INDEX "tiki_searchindex_last_update" ON "tiki_searchindex"("last_update");


--
-- Table structure for table `tiki_search_stats`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 10:55 PM
--

DROP TABLE "tiki_search_stats";

CREATE TABLE "tiki_search_stats" (
  "term" varchar(50) NOT NULL default '',
  "hits" bigint default NULL,
  PRIMARY KEY ("term")
) ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_semaphores`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 13, 2003 at 01:52 AM
--

DROP TABLE "tiki_semaphores";

CREATE TABLE "tiki_semaphores" (
  "semName" varchar(250) NOT NULL default '',
  "user" varchar(200) default NULL,
  "timestamp" bigint default NULL,
  PRIMARY KEY ("semName")
) ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_sent_newsletters`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_sent_newsletters";

CREATE TABLE "tiki_sent_newsletters" (
  "editionId" bigserial,
  "nlId" bigint NOT NULL default '0',
  "users" bigint default NULL,
  "sent" bigint default NULL,
  "subject" varchar(200) default NULL,
  "data" bytea,
  PRIMARY KEY ("editionId")
)   ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_sessions`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 13, 2003 at 01:52 AM
--

DROP TABLE "tiki_sessions";

CREATE TABLE "tiki_sessions" (
  "sessionId" varchar(32) NOT NULL default '',
  "user" varchar(200) default NULL,
  "timestamp" bigint default NULL,
  PRIMARY KEY ("sessionId")
) ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_shoutbox`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 08:21 PM
--

DROP TABLE "tiki_shoutbox";

CREATE TABLE "tiki_shoutbox" (
  "msgId" bigserial,
  "message" varchar(255) default NULL,
  "timestamp" bigint default NULL,
  "user" varchar(200) default NULL,
  "hash" varchar(32) default NULL,
  PRIMARY KEY ("msgId")
)   ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_shoutbox_words`
--

DROP TABLE "tiki_shoutbox_words";

CREATE TABLE "tiki_shoutbox_words" (
  "word" VARCHAR( 40 ) NOT NULL ,
  "qty" INT DEFAULT '0' NOT NULL ,
  PRIMARY KEY ("word")
) ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_structures`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_structures";

CREATE TABLE "tiki_structures" (
  "page_ref_id" bigserial,
  "parent_id" bigint default NULL,
  "page_id" bigint NOT NULL,
  "page_alias" varchar(240) NOT NULL default '',
  "pos" smallint default NULL,
  PRIMARY KEY ("page_ref_id")

)   ;

CREATE  INDEX "tiki_structures_pidpaid" ON "tiki_structures"("page_id","parent_id");
-- --------------------------------------------------------

--
-- Table structure for table `tiki_submissions`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 08, 2003 at 04:16 PM
--

DROP TABLE "tiki_submissions";

CREATE TABLE "tiki_submissions" (
  "subId" serial,
  "title" varchar(80) default NULL,
  "authorName" varchar(60) default NULL,
  "topicId" bigint default NULL,
  "topicName" varchar(40) default NULL,
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
  "body" text,
  "hash" varchar(32) default NULL,
  "author" varchar(200) default NULL,
  "reads" bigint default NULL,
  "votes" integer default NULL,
  "points" bigint default NULL,
  "type" varchar(50) default NULL,
  "rating" decimal(3,2) default NULL,
  "isfloat" char(1) default NULL,
  PRIMARY KEY ("subId")
)   ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_suggested_faq_questions`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 11, 2003 at 08:52 PM
--

DROP TABLE "tiki_suggested_faq_questions";

CREATE TABLE "tiki_suggested_faq_questions" (
  "sfqId" bigserial,
  "faqId" bigint NOT NULL default '0',
  "question" text,
  "answer" text,
  "created" bigint default NULL,
  "user" varchar(200) default NULL,
  PRIMARY KEY ("sfqId")
)   ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_survey_question_options`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 11, 2003 at 12:55 AM
--

DROP TABLE "tiki_survey_question_options";

CREATE TABLE "tiki_survey_question_options" (
  "optionId" bigserial,
  "questionId" bigint NOT NULL default '0',
  "qoption" text,
  "votes" bigint default NULL,
  PRIMARY KEY ("optionId")
)   ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_survey_questions`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 11, 2003 at 11:55 PM
--

DROP TABLE "tiki_survey_questions";

CREATE TABLE "tiki_survey_questions" (
  "questionId" bigserial,
  "surveyId" bigint NOT NULL default '0',
  "question" text,
  "options" text,
  "type" char(1) default NULL,
  "position" integer default NULL,
  "votes" bigint default NULL,
  "value" bigint default NULL,
  "average" decimal(4,2) default NULL,
  PRIMARY KEY ("questionId")
)   ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_surveys`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 07:40 PM
--

DROP TABLE "tiki_surveys";

CREATE TABLE "tiki_surveys" (
  "surveyId" bigserial,
  "name" varchar(200) default NULL,
  "description" text,
  "taken" bigint default NULL,
  "lastTaken" bigint default NULL,
  "created" bigint default NULL,
  "status" char(1) default NULL,
  PRIMARY KEY ("surveyId")
)   ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_tags`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 06, 2003 at 02:58 AM
--

DROP TABLE "tiki_tags";

CREATE TABLE "tiki_tags" (
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
) ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_theme_control_categs`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_theme_control_categs";

CREATE TABLE "tiki_theme_control_categs" (
  "categId" bigint NOT NULL default '0',
  "theme" varchar(250) NOT NULL default '',
  PRIMARY KEY ("categId")
) ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_theme_control_objects`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_theme_control_objects";

CREATE TABLE "tiki_theme_control_objects" (
  "objId" varchar(250) NOT NULL default '',
  "type" varchar(250) NOT NULL default '',
  "name" varchar(250) NOT NULL default '',
  "theme" varchar(250) NOT NULL default '',
  PRIMARY KEY ("objId")
) ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_theme_control_sections`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_theme_control_sections";

CREATE TABLE "tiki_theme_control_sections" (
  "section" varchar(250) NOT NULL default '',
  "theme" varchar(250) NOT NULL default '',
  PRIMARY KEY ("section")
) ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_topics`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 04, 2003 at 10:10 PM
--

DROP TABLE "tiki_topics";

CREATE TABLE "tiki_topics" (
  "topicId" bigserial,
  "name" varchar(40) default NULL,
  "image_name" varchar(80) default NULL,
  "image_type" varchar(80) default NULL,
  "image_size" bigint default NULL,
  "image_data" bytea,
  "active" char(1) default NULL,
  "created" bigint default NULL,
  PRIMARY KEY ("topicId")
)   ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_tracker_fields`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 08, 2003 at 01:48 PM
--

DROP TABLE "tiki_tracker_fields";

CREATE TABLE "tiki_tracker_fields" (
  "fieldId" bigserial,
  "trackerId" bigint NOT NULL default '0',
  "name" varchar(80) default NULL,
  "options" text,
  "position" smallint default NULL,
  "type" char(1) default NULL,
  "isMain" char(1) default NULL,
  "isTblVisible" char(1) default NULL,
  "isSearchable" char(1) default NULL,
  "isPublic" char(1) NOT NULL default 'n',
  "isHidden" char(1) NOT NULL default 'n',
  "isMandatory" char(1) NOT NULL default 'n',
  PRIMARY KEY ("fieldId")
)   ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_tracker_item_attachments`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_tracker_item_attachments";

CREATE TABLE "tiki_tracker_item_attachments" (
  "attId" bigserial,
  "itemId" bigint NOT NULL default 0,
  "filename" varchar(80) default NULL,
  "filetype" varchar(80) default NULL,
  "filesize" bigint default NULL,
  "user" varchar(200) default NULL,
  "data" bytea,
  "longdesc" bytea,
  "path" varchar(255) default NULL,
  "downloads" bigint default NULL,
  "version" varchar(40) default NULL,
  "created" bigint default NULL,
  "comment" varchar(250) default NULL,
  PRIMARY KEY ("attId")
)   ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_tracker_item_comments`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 08:12 AM
--

DROP TABLE "tiki_tracker_item_comments";

CREATE TABLE "tiki_tracker_item_comments" (
  "commentId" bigserial,
  "itemId" bigint NOT NULL default '0',
  "user" varchar(200) default NULL,
  "data" text,
  "title" varchar(200) default NULL,
  "posted" bigint default NULL,
  PRIMARY KEY ("commentId")
)   ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_tracker_item_fields`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 08:26 AM
--

DROP TABLE "tiki_tracker_item_fields";

CREATE TABLE "tiki_tracker_item_fields" (
  "itemId" bigint NOT NULL default '0',
  "fieldId" bigint NOT NULL default '0',
  "value" text,
  PRIMARY KEY ("itemId","fieldId")
) ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_tracker_items`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 08:26 AM
--

DROP TABLE "tiki_tracker_items";

CREATE TABLE "tiki_tracker_items" (
  "itemId" bigserial,
  "trackerId" bigint NOT NULL default '0',
  "created" bigint default NULL,
  "status" char(1) default NULL,
  "lastModif" bigint default NULL,
  PRIMARY KEY ("itemId")
)   ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_tracker_options`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 08, 2003 at 01:48 PM
--

DROP TABLE "tiki_tracker_options";

CREATE TABLE "tiki_tracker_options" (
  "trackerId" bigint NOT NULL default '0',
  "name" varchar(80) NOT NULL default '',
  "value" text default NULL,
  PRIMARY KEY ("trackerId","name")
)  ;

-- --------------------------------------------------------


--
-- Table structure for table `tiki_trackers`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 08:26 AM
--

DROP TABLE "tiki_trackers";

CREATE TABLE "tiki_trackers" (
  "trackerId" bigserial,
  "name" varchar(80) default NULL,
  "description" text,
  "created" bigint default NULL,
  "lastModif" bigint default NULL,
  "showCreated" char(1) default NULL,
  "showStatus" char(1) default NULL,
  "showLastModif" char(1) default NULL,
  "useComments" char(1) default NULL,
  "showComments" char(1) default NULL,
  "useAttachments" char(1) default NULL,
  "showAttachments" char(1) default NULL,
  "orderAttachments" varchar(255) NOT NULL default 'filename,created,filesize,downloads,desc',
  "items" bigint default NULL,
  PRIMARY KEY ("trackerId")
)   ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_untranslated`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_untranslated";

CREATE TABLE "tiki_untranslated" (
  "id" bigserial,
  "source" bytea NOT NULL,
  "lang" char(2) NOT NULL default '',
  PRIMARY KEY ("source","lang")


)   ;

CREATE  INDEX "tiki_untranslated_id_2" ON "tiki_untranslated"("id");
CREATE UNIQUE INDEX "tiki_untranslated_id" ON "tiki_untranslated"("id");
-- --------------------------------------------------------

--
-- Table structure for table `tiki_user_answers`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_user_answers";

CREATE TABLE "tiki_user_answers" (
  "userResultId" bigint NOT NULL default '0',
  "quizId" bigint NOT NULL default '0',
  "questionId" bigint NOT NULL default '0',
  "optionId" bigint NOT NULL default '0',
  PRIMARY KEY ("userResultId","quizId","questionId","optionId")
) ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_user_assigned_modules`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 08:25 PM
--

DROP TABLE "tiki_user_assigned_modules";

CREATE TABLE "tiki_user_assigned_modules" (
  "name" varchar(200) NOT NULL default '',
  "position" char(1) default NULL,
  "ord" smallint default NULL,
  "type" char(1) default NULL,
  "user" varchar(200) NOT NULL default '',
  PRIMARY KEY ("name","user")
) ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_user_bookmarks_folders`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 11, 2003 at 08:35 AM
--

DROP TABLE "tiki_user_bookmarks_folders";

CREATE TABLE "tiki_user_bookmarks_folders" (
  "folderId" bigserial,
  "parentId" bigint default NULL,
  "user" varchar(200) NOT NULL default '',
  "name" varchar(30) default NULL,
  PRIMARY KEY ("user","folderId")
)   ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_user_bookmarks_urls`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 11, 2003 at 08:36 AM
--

DROP TABLE "tiki_user_bookmarks_urls";

CREATE TABLE "tiki_user_bookmarks_urls" (
  "urlId" bigserial,
  "name" varchar(30) default NULL,
  "url" varchar(250) default NULL,
  "data" bytea,
  "lastUpdated" bigint default NULL,
  "folderId" bigint NOT NULL default '0',
  "user" varchar(200) NOT NULL default '',
  PRIMARY KEY ("urlId")
)   ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_user_mail_accounts`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_user_mail_accounts";

CREATE TABLE "tiki_user_mail_accounts" (
  "accountId" bigserial,
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
)   ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_user_menus`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 11, 2003 at 10:58 PM
--

DROP TABLE "tiki_user_menus";

CREATE TABLE "tiki_user_menus" (
  "user" varchar(200) NOT NULL default '',
  "menuId" bigserial,
  "url" varchar(250) default NULL,
  "name" varchar(40) default NULL,
  "position" smallint default NULL,
  "mode" char(1) default NULL,
  PRIMARY KEY ("menuId")
)   ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_user_modules`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 05, 2003 at 03:16 AM
--

DROP TABLE "tiki_user_modules";

CREATE TABLE "tiki_user_modules" (
  "name" varchar(200) NOT NULL default '',
  "title" varchar(40) default NULL,
  "data" bytea,
  PRIMARY KEY ("name")
) ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_user_notes`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 07:52 AM
--

DROP TABLE "tiki_user_notes";

CREATE TABLE "tiki_user_notes" (
  "user" varchar(200) NOT NULL default '',
  "noteId" bigserial,
  "created" bigint default NULL,
  "name" varchar(255) default NULL,
  "lastModif" bigint default NULL,
  "data" text,
  "size" bigint default NULL,
  "parse_mode" varchar(20) default NULL,
  PRIMARY KEY ("noteId")
)   ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_user_postings`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 13, 2003 at 01:12 AM
--

DROP TABLE "tiki_user_postings";

CREATE TABLE "tiki_user_postings" (
  "user" varchar(200) NOT NULL default '',
  "posts" bigint default NULL,
  "last" bigint default NULL,
  "first" bigint default NULL,
  "level" integer default NULL,
  PRIMARY KEY ("user")
) ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_user_preferences`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 13, 2003 at 01:09 AM
--

DROP TABLE "tiki_user_preferences";

CREATE TABLE "tiki_user_preferences" (
  "user" varchar(200) NOT NULL default '',
  "prefName" varchar(40) NOT NULL default '',
  "value" varchar(250) default NULL,
  PRIMARY KEY ("user","prefName")
) ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_user_quizzes`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_user_quizzes";

CREATE TABLE "tiki_user_quizzes" (
  "user" varchar(100) default NULL,
  "quizId" bigint default NULL,
  "timestamp" bigint default NULL,
  "timeTaken" bigint default NULL,
  "points" bigint default NULL,
  "maxPoints" bigint default NULL,
  "resultId" bigint default NULL,
  "userResultId" bigserial,
  PRIMARY KEY ("userResultId")
)   ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_user_taken_quizzes`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_user_taken_quizzes";

CREATE TABLE "tiki_user_taken_quizzes" (
  "user" varchar(200) NOT NULL default '',
  "quizId" varchar(255) NOT NULL default '',
  PRIMARY KEY ("user","quizId")
) ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_user_tasks`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 08, 2003 at 05:30 PM
--

DROP TABLE "tiki_user_tasks";

CREATE TABLE "tiki_user_tasks" (
  "user" varchar(200) default NULL,
  "taskId" bigserial,
  "title" varchar(250) default NULL,
  "description" text,
  "date" bigint default NULL,
  "status" char(1) default NULL,
  "priority" smallint default NULL,
  "completed" bigint default NULL,
  "percentage" smallint default NULL,
  PRIMARY KEY ("taskId")
)   ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_user_votings`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 11, 2003 at 11:55 PM
--

DROP TABLE "tiki_user_votings";

CREATE TABLE "tiki_user_votings" (
  "user" varchar(200) NOT NULL default '',
  "id" varchar(255) NOT NULL default '',
  PRIMARY KEY ("user","id")
) ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_user_watches`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 08:07 AM
--

DROP TABLE "tiki_user_watches";

CREATE TABLE "tiki_user_watches" (
  "user" varchar(200) NOT NULL default '',
  "event" varchar(40) NOT NULL default '',
  "object" varchar(200) NOT NULL default '',
  "hash" varchar(32) default NULL,
  "title" varchar(250) default NULL,
  "type" varchar(200) default NULL,
  "url" varchar(250) default NULL,
  "email" varchar(200) default NULL,
  PRIMARY KEY ("user","event","object")
) ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_userfiles`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_userfiles";

CREATE TABLE "tiki_userfiles" (
  "user" varchar(200) NOT NULL default '',
  "fileId" bigserial,
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
)   ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_userpoints`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 11, 2003 at 05:47 AM
--

DROP TABLE "tiki_userpoints";

CREATE TABLE "tiki_userpoints" (
  "user" varchar(200) default NULL,
  "points" decimal(8,2) default NULL,
  "voted" integer default NULL
) ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_users`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_users";

CREATE TABLE "tiki_users" (
  "user" varchar(200) NOT NULL default '',
  "password" varchar(40) default NULL,
  "email" varchar(200) default NULL,
  "lastLogin" bigint default NULL,
  PRIMARY KEY ("user")
) ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_webmail_contacts`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_webmail_contacts";

CREATE TABLE "tiki_webmail_contacts" (
  "contactId" bigserial,
  "firstName" varchar(80) default NULL,
  "lastName" varchar(80) default NULL,
  "email" varchar(250) default NULL,
  "nickname" varchar(200) default NULL,
  "user" varchar(200) NOT NULL default '',
  PRIMARY KEY ("contactId")
)   ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_webmail_messages`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_webmail_messages";

CREATE TABLE "tiki_webmail_messages" (
  "accountId" bigint NOT NULL default '0',
  "mailId" varchar(255) NOT NULL default '',
  "user" varchar(200) NOT NULL default '',
  "isRead" char(1) default NULL,
  "isReplied" char(1) default NULL,
  "isFlagged" char(1) default NULL,
  PRIMARY KEY ("accountId","mailId")
) ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_wiki_attachments`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_wiki_attachments";

CREATE TABLE "tiki_wiki_attachments" (
  "attId" bigserial,
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
)   ;

-- --------------------------------------------------------

--
-- Table structure for table `tiki_zones`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 07:42 PM
--

DROP TABLE "tiki_zones";

CREATE TABLE "tiki_zones" (
  "zone" varchar(40) NOT NULL default '',
  PRIMARY KEY ("zone")
) ;

-- --------------------------------------------------------

--
-- Table structure for table `users_grouppermissions`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 11, 2003 at 07:22 AM
--

DROP TABLE "users_grouppermissions";

CREATE TABLE "users_grouppermissions" (
  "groupName" varchar(255) NOT NULL default '',
  "permName" varchar(30) NOT NULL default '',
  "value" char(1) default '',
  PRIMARY KEY ("groupName","permName")
) ;

-- --------------------------------------------------------

--
-- Table structure for table `users_groups`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 03, 2003 at 08:57 PM
--

DROP TABLE "users_groups";

CREATE TABLE "users_groups" (
  "groupName" varchar(255) NOT NULL default '',
  "groupDesc" varchar(255) default NULL,
  "groupHome" varchar(255),
  "usersTrackerId" bigint,
  "groupTrackerId" bigint,
  "usersFieldId" bigint,
  "groupFieldId" bigint,
  PRIMARY KEY ("groupName")
) ;

-- --------------------------------------------------------

--
-- Table structure for table `users_objectpermissions`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 07:20 AM
--

DROP TABLE "users_objectpermissions";

CREATE TABLE "users_objectpermissions" (
  "groupName" varchar(255) NOT NULL default '',
  "permName" varchar(30) NOT NULL default '',
  "objectType" varchar(20) NOT NULL default '',
  "objectId" varchar(32) NOT NULL default '',
  PRIMARY KEY ("objectId","groupName","permName")
) ;

-- --------------------------------------------------------

--
-- Table structure for table `users_permissions`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 11, 2003 at 07:22 AM
--

DROP TABLE "users_permissions";

CREATE TABLE "users_permissions" (
  "permName" varchar(30) NOT NULL default '',
  "permDesc" varchar(250) default NULL,
  "level" varchar(80) default NULL,
  "type" varchar(20) default NULL,
  PRIMARY KEY ("permName")
) ;

-- --------------------------------------------------------
-- Data set
INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_abort_instance', 'Can abort a process instance', 'editors', 'workflow');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_access_closed_site', 'Can access site when closed', 'admin', 'tiki');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_add_events', 'Can add events in the calendar', 'registered', 'calendar');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_admin', 'Administrator, can manage users groups and permissions, Hotwords and all the weblog features', 'admin', 'tiki');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_admin_banners', 'Administrator, can admin banners', 'admin', 'tiki');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_admin_banning', 'Can ban users or ips', 'admin', 'tiki');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_admin_calendar', 'Can create/admin calendars', 'admin', 'calendar');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_admin_categories', 'Can admin categories', 'editors', 'tiki');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_admin_charts', 'Can admin charts', 'admin', 'charts');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_admin_chat', 'Administrator, can create channels remove channels etc', 'editors', 'chat');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_admin_cms', 'Can admin the cms', 'editors', 'cms');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_admin_directory', 'Can admin the directory', 'editors', 'directory');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_admin_directory_cats', 'Can admin directory categories', 'editors', 'directory');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_admin_directory_sites', 'Can admin directory sites', 'editors', 'directory');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_admin_drawings', 'Can admin drawings', 'editors', 'drawings');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_admin_dynamic', 'Can admin the dynamic content system', 'editors', 'tiki');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_admin_faqs', 'Can admin faqs', 'editors', 'faqs');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_admin_file_galleries', 'Can admin file galleries', 'editors', 'file galleries');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_admin_forum', 'Can admin forums', 'editors', 'forums');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_admin_galleries', 'Can admin Image Galleries', 'editors', 'image galleries');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_admin_games', 'Can admin games', 'editors', 'games');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_admin_mailin', 'Can admin mail-in accounts', 'admin', 'tiki');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_admin_newsletters', 'Can admin newsletters', 'editors', 'newsletters');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_admin_quizzes', 'Can admin quizzes', 'editors', 'quizzes');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_admin_received_articles', 'Can admin received articles', 'editors', 'comm');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_admin_received_pages', 'Can admin received pages', 'editors', 'comm');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_admin_shoutbox', 'Can admin shoutbox (Edit/remove msgs)', 'editors', 'shoutbox');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_admin_surveys', 'Can admin surveys', 'editors', 'surveys');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_admin_trackers', 'Can admin trackers', 'editors', 'trackers');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_admin_wiki', 'Can admin the wiki', 'editors', 'wiki');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_admin_workflow', 'Can admin workflow processes', 'admin', 'workflow');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_admin_packager', 'Can admin packages/packager', 'admin', 'packages');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_approve_submission', 'Can approve submissions', 'editors', 'cms');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_attach_trackers', 'Can attach files to tracker items', 'registered', 'trackers');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_autoapprove_submission', 'Submited articles automatically approved', 'editors', 'cms');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_autosubmit_link', 'Submited links are valid', 'editors', 'directory');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_autoval_chart_suggestio', 'Autovalidate suggestions', 'editors', 'charts');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_batch_upload_files', 'Can upload zip files with files', 'editors', 'file galleries');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_batch_upload_images', 'Can upload zip files with images', 'editors', 'image galleries');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_blog_admin', 'Can admin blogs', 'editors', 'blogs');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_blog_post', 'Can post to a blog', 'registered', 'blogs');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_broadcast', 'Can broadcast messages to groups', 'admin', 'messu');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_broadcast_all', 'Can broadcast messages to all user', 'admin', 'messu');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_cache_bookmarks', 'Can cache user bookmarks', 'admin', 'user');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_change_events', 'Can change events in the calendar', 'registered', 'calendar');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_chat', 'Can use the chat system', 'registered', 'chat');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_comment_tracker_items', 'Can insert comments for tracker items', 'basic', 'trackers');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_configure_modules', 'Can configure modules', 'registered', 'user');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_create_blogs', 'Can create a blog', 'editors', 'blogs');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_create_bookmarks', 'Can create user bookmarksche user bookmarks', 'registered', 'user');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_create_css', 'Can create new css suffixed with -user', 'registered', 'tiki');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_create_file_galleries', 'Can create file galleries', 'editors', 'file galleries');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_create_galleries', 'Can create image galleries', 'editors', 'image galleries');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_create_tracker_items', 'Can create new items for trackers', 'registered', 'trackers');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_download_files', 'Can download files', 'basic', 'file galleries');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_edit', 'Can edit pages', 'registered', 'wiki');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_edit_article', 'Can edit articles', 'editors', 'cms');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_edit_comments', 'Can edit all comments', 'editors', 'comments');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_edit_content_templates', 'Can edit content templates', 'editors', 'content templates');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_edit_cookies', 'Can admin cookies', 'editors', 'tiki');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_edit_copyrights', 'Can edit copyright notices', 'editors', 'wiki');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_edit_drawings', 'Can edit drawings', 'basic', 'drawings');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_edit_html_pages', 'Can edit HTML pages', 'editors', 'html pages');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_edit_languages', 'Can edit translations and create new languages', 'editors', 'tiki');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_edit_structures', 'Can create and edit structures', 'editors', 'wiki');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_edit_submission', 'Can edit submissions', 'editors', 'cms');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_edit_templates', 'Can edit site templates', 'admin', 'tiki');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_eph_admin', 'Can admin ephemerides', 'editors', 'tiki');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_exception_instance', 'Can declare an instance as exception', 'registered', 'workflow');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_forum_attach', 'Can attach to forum posts', 'registered', 'forums');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_forum_autoapp', 'Auto approve forum posts', 'editors', 'forums');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_forum_post', 'Can post in forums', 'registered', 'forums');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_forum_post_topic', 'Can start threads in forums', 'registered', 'forums');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_forum_read', 'Can read forums', 'basic', 'forums');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_forum_vote', 'Can vote comments in forums', 'registered', 'forums');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_forums_report', 'Can report msgs to moderator', 'registered', 'forums');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_live_support', 'Can use live support system', 'basic', 'support');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_live_support_admin', 'Admin live support system', 'admin', 'support');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_lock', 'Can lock pages', 'editors', 'wiki');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_map_create', 'Can create new mapfile', 'admin', 'maps');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_map_delete', 'Can delete mapfiles', 'admin', 'maps');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_map_edit', 'Can edit mapfiles', 'editors', 'maps');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_map_view', 'Can view mapfiles', 'basic', 'maps');

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

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_topic_read', 'Can read a topic (Applies only to individual topic perms)', 'basic', 'topics');

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

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_categories', 'Can browse categories', 'registered', 'tiki');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_chart', 'Can view charts', 'basic', 'charts');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_directory', 'Can use the directory', 'basic', 'directory');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_eph', 'Can view ephemerides', 'registered', 'tiki');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_faqs', 'Can view faqs', 'basic', 'faqs');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_file_gallery', 'Can view file galleries', 'basic', 'file galleries');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_html_pages', 'Can view HTML pages', 'basic', 'html pages');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_image_gallery', 'Can view image galleries', 'basic', 'image galleries');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_quiz_stats', 'Can view quiz stats', 'basic', 'quizzes');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_referer_stats', 'Can view referer stats', 'editors', 'tiki');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_shoutbox', 'Can view shoutbox', 'basic', 'shoutbox');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_stats', 'Can view site stats', 'basic', 'tiki');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_survey_stats', 'Can view survey stats', 'basic', 'surveys');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_trackers', 'Can view trackers', 'basic', 'trackers');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_trackers_closed', 'Can view trackers closed items', 'registered', 'trackers');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_trackers_pending', 'Can view trackers pending items', 'editors', 'trackers');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_user_results', 'Can view user quiz results', 'editors', 'quizzes');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_vote_chart', 'Can vote', 'basic', 'charts');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_vote_comments', 'Can vote comments', 'registered', 'comments');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_vote_poll', 'Can vote polls', 'basic', 'tiki');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_wiki_admin_attachments', 'Can admin attachments to wiki pages', 'editors', 'wiki');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_wiki_attach_files', 'Can attach files to wiki pages', 'registered', 'wiki');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_wiki_view_attachments', 'Can view wiki attachments and download', 'registered', 'wiki');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_edit_package', 'Can create packages with packager', 'admin', 'packages');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_install_package', 'Can install packages', 'admin', 'packages');


-- TikiJukebox permissions - Damosoft aka Damian
INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_jukebox_albums', 'Can view jukebox albums', 'registered', 'jukebox');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_jukebox_tracks', 'Can view jukebox tracklist', 'registered', 'jukebox');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_jukebox_upload', 'Can upload new jukebox tracks', 'registered', 'jukebox');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_jukebox_admin', 'Can admin the jukebox system', 'admin', 'jukebox');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_jukebox_genres', 'Can admin the jukebox genres', 'admin', 'jukebox');


-- Homework permissions - ggeller
INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_hw_admin','Can adminsiter homework','admin','homework');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_hw_teacher','Can create new homework assignments, see student names and grade assignments','editors','homework');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_hw_grader','Can grade homework assignments','editors','homework');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_hw_student','Can do homework assignments','registered','homework');


-- --------------------------------------------------------

--
-- Table structure for table `users_usergroups`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 12, 2003 at 09:31 PM
--

DROP TABLE "users_usergroups";

CREATE TABLE "users_usergroups" (
  "userId" integer NOT NULL default '0',
  "groupName" varchar(255) NOT NULL default '',
  PRIMARY KEY ("userId","groupName")
) ;

-- --------------------------------------------------------
INSERT INTO "users_groups" ("groupName","groupDesc") VALUES ('Anonymous','Public users not logged');

INSERT INTO "users_groups" ("groupName","groupDesc") VALUES ('Registered','Users logged into the system');

-- --------------------------------------------------------

--
-- Table structure for table `users_users`
--
-- Creation: Jul 03, 2003 at 07:42 PM
-- Last update: Jul 13, 2003 at 01:07 AM
--

DROP TABLE "users_users";

CREATE TABLE "users_users" (
  "userId" serial,
  "email" varchar(200) default NULL,
  "login" varchar(40) NOT NULL default '',
  "password" varchar(30) default '',
  "provpass" varchar(30) default NULL,
  "default_group" varchar(255),
  "lastLogin" bigint default NULL,
  "currentLogin" bigint default NULL,
  "registrationDate" bigint default NULL,
  "challenge" varchar(32) default NULL,
  "pass_due" bigint default NULL,
  "hash" varchar(32) default NULL,
  "created" bigint default NULL,
  "avatarName" varchar(80) default NULL,
  "avatarSize" bigint default NULL,
  "avatarFileType" varchar(250) default NULL,
  "avatarData" bytea,
  "avatarLibName" varchar(200) default NULL,
  "avatarType" char(1) default NULL,
  PRIMARY KEY ("userId")
)   ;

-- --------------------------------------------------------
------ Administrator account
INSERT INTO "users_users" ("email","login","password","hash") VALUES ('','admin','admin','f6fdffe48c908deb0f4c3bd36c032e72');

UPDATE "users_users" SET "currentLogin"="lastLogin","registrationDate"="lastLogin";

INSERT INTO "tiki_user_preferences" ("user","prefName","value") VALUES ('admin','realName','System Administrator'); 
-- --------------------------------------------------------

-- Inserts of all default values for preferences
INSERT INTO "tiki_preferences" ("name","value") VALUES ('allowRegister','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('anonCanEdit','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('article_comments_default_ordering','points_desc');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('article_comments_per_page','10');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('art_list_author','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('art_list_date','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('art_list_img','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('art_list_reads','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('art_list_size','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('art_list_title','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('art_list_topic','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('art_list_type','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('art_list_expire','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('art_list_visible','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('auth_create_user_auth','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('auth_create_user_tiki','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('auth_ldap_adminpass','');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('auth_ldap_adminuser','');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('auth_ldap_basedn','');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('auth_ldap_groupattr','cn');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('auth_ldap_groupdn','');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('auth_ldap_groupoc','groupOfUniqueNames');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('auth_ldap_host','localhost');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('auth_ldap_memberattr','uniqueMember');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('auth_ldap_memberisdn','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('auth_ldap_port','389');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('auth_ldap_scope','sub');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('auth_ldap_userattr','uid');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('auth_ldap_userdn','');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('auth_ldap_useroc','inetOrgPerson');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('auth_method','tiki');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('auth_skip_admin','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('blog_comments_default_ordering','points_desc');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('blog_comments_per_page','10');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('blog_list_activity','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('blog_list_created','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('blog_list_description','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('blog_list_lastmodif','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('blog_list_order','created_desc');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('blog_list_posts','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('blog_list_title','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('blog_list_user','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('blog_list_visits','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('blog_spellcheck','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('cacheimages','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('cachepages','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('change_language','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('change_theme','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('cms_bot_bar','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('cms_left_column','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('cms_right_column','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('cms_spellcheck','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('cms_top_bar','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('contact_user','admin');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('count_admin_pvs','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('default_map','pacific.map');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('directory_columns','3');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('directory_links_per_page','20');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('directory_open_links','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('directory_validate_urls','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('direct_pagination','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('display_timezone','EST');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('faq_comments_default_ordering','points_desc');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('faq_comments_per_page','10');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_autolinks','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_maps','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_article_comments','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_articles','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_babelfish','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_babelfish_logo','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_backlinks','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_banners','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_banning','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_blog_comments','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_blogposts_comments','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_blog_rankings','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_blogs','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_bot_bar','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_calendar','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_categories','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_categoryobjects','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_categorypath','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_challenge','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_charts','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_chat','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_clear_passwords','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_cms_rankings','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_cms_templates','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_comm','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_contact','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_custom_home','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_debug_console','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_debugger_console','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_directory','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_drawings','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_dump','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_dynamic_content','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_editcss','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_edit_templates','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_eph','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_faq_comments','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_faqs','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_featuredLinks','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_file_galleries_comments','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_file_galleries','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_file_galleries_rankings','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_forum_parse','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_forum_quickjump','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_forum_rankings','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_forums','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_forum_topicd','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_galleries','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_gal_rankings','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_games','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_history','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_hotwords_nw','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_hotwords','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_html_pages','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_image_galleries_comments','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_integrator','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_jscalendar','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_lastChanges','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_left_column','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_likePages','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_listPages','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_live_support','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_menusfolderstyle','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_messages','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_minical','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_modulecontrols', 'n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_newsletters','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_newsreader','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_notepad','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_obzip','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_page_title','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_phpopentracker','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_poll_comments','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_polls','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_phplayers','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_quizzes','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_ranking','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_referer_stats','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_right_column','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_sandbox','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_search_fulltext','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_search_stats','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_search','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_shoutbox','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_smileys','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_stats','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_submissions','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_surveys','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_tasks','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_theme_control','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_top_bar','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_trackers','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_user_bookmarks','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_userfiles','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_usermenu','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_userPreferences','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_userVersions','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_user_watches','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_view_tpl','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_warn_on_edit','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_webmail','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_wiki_attachments','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_wiki_comments','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_wiki_description','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_wiki_discuss','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_wiki_footnotes','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_wiki_monosp','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_wiki_multiprint','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_wiki_notepad','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_wiki_pdf','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_wiki_pictures','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_wiki_rankings','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_wiki_tables','old');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_wiki_templates','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_wiki_undo','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_wiki_usrlock','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_wikiwords','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_wiki','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_workflow','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_xmlrpc','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('fgal_list_created','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('fgal_list_description','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('fgal_list_files','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('fgal_list_hits','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('fgal_list_lastmodif','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('fgal_list_name','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('fgal_list_user','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('fgal_match_regex','');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('fgal_nmatch_regex','');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('fgal_use_db','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('fgal_use_dir','');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('file_galleries_comments_default_ordering','points_desc');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('file_galleries_comments_per_page','10');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('forgotPass','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('forum_list_desc','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('forum_list_lastpost','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('forum_list_posts','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('forum_list_ppd','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('forum_list_topics','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('forum_list_visits','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('forums_ordering','created_desc');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('gal_list_created','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('gal_list_description','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('gal_list_imgs','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('gal_list_lastmodif','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('gal_list_name','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('gal_list_user','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('gal_list_visits','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('gal_match_regex','');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('gal_nmatch_regex','');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('gal_use_db','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('gal_use_dir','');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('gal_use_lib','gd');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('groupTracker','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('home_file_gallery','');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('http_domain','');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('http_port','80');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('http_prefix','/');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('https_domain','');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('https_login','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('https_login_required','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('https_port','443');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('https_prefix','/');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('image_galleries_comments_default_orderin','points_desc');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('image_galleries_comments_per_page','10');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('keep_versions','1');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('language','en');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('lang_use_db','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('layout_section','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('long_date_format','%A %d of %B, %Y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('long_time_format','%H:%M:%S %Z');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('map_path','/var/www/html/map/');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('maxArticles','10');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('maxRecords','10');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('max_rss_articles','10');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('max_rss_blog','10');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('max_rss_blogs','10');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('max_rss_file_galleries','10');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('max_rss_file_gallery','10');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('max_rss_forum','10');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('max_rss_forums','10');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('max_rss_mapfiles','10');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('max_rss_image_galleries','10');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('max_rss_image_gallery','10');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('max_rss_wiki','10');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('maxVersions','0');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('min_pass_length','1');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('modallgroups','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('pass_chr_num','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('pass_due','999');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('poll_comments_default_ordering','points_desc');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('poll_comments_per_page','10');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('popupLinks','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('proxy_host','');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('proxy_port','');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('record_untranslated','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('registerPasscode','');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('rememberme','disabled');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('remembertime','7200');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('rnd_num_reg','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('rss_articles','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('rss_blog','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('rss_blogs','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('rssfeed_default_version','2');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('rssfeed_language','en-us');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('rssfeed_editor','');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('rssfeed_publisher','');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('rssfeed_webmaster','');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('rssfeed_creator','');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('rssfeed_css','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('rss_file_galleries','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('rss_file_gallery','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('rss_forums','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('rss_forum','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('rss_mapfiles','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('rss_image_galleries','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('rss_image_gallery','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('rss_wiki','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('sender_email','');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('email_encoding','utf-8');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('search_refresh_rate','5');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('search_min_wordlength','3');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('search_max_syllwords','100');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('search_lru_purge_rate','5');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('search_lru_length','100');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('search_syll_age','48');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('short_date_format','%a %d of %b, %Y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('short_time_format','%H:%M %Z');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('shoutbox_autolink','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('siteTitle','');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('slide_style','slidestyle.css');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('style','moreneat.css');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('system_os','unix');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('tikiIndex','tiki-index.php');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('tmpDir','temp');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('t_use_db','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('t_use_dir','');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('uf_use_db','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('uf_use_dir','');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('urlIndex','');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('use_proxy','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('user_assigned_modules','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('useRegisterPasscode','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('userfiles_quota','30');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('userTracker','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('useUrlIndex','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('validateUsers','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('validateEmail','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('eponymousGroups','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('warn_on_edit_time','2');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('webmail_max_attachment','1500000');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('webmail_view_html','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('webserverauth','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('wiki_bot_bar','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('wiki_cache','0');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('wiki_comments_default_ordering','points_desc');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('wiki_comments_per_page','10');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('wiki_creator_admin','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('wiki_feature_copyrights','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('wiki_forum','');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('wiki_forum_id','');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('wikiHomePage','HomePage');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('wiki_left_column','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('wikiLicensePage','');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('wiki_list_backlinks','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('wiki_list_comment','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('wiki_list_creator','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('wiki_list_hits','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('wiki_list_lastmodif','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('wiki_list_lastver','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('wiki_list_links','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('wiki_list_name','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('wiki_list_size','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('wiki_list_status','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('wiki_list_user','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('wiki_list_versions','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('wiki_page_regex','strict');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('wiki_right_column','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('wiki_spellcheck','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('wikiSubmitNotice','');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('wiki_top_bar','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('wiki_uses_slides','n');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('w_use_db','y');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('w_use_dir','');

INSERT INTO "tiki_preferences" ("name","value") VALUES ('feature_homework','n');


-- Dynamic variables
INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_edit_dynvar', 'Can edit dynamic variables', 'editors', 'wiki');


--
-- Table structure for table 'tiki_integrator_reps'
--
DROP TABLE "tiki_integrator_reps";

CREATE TABLE "tiki_integrator_reps" (
  "repID" bigserial,
  "name" varchar(255) NOT NULL default '',
  "path" varchar(255) NOT NULL default '',
  "start_page" varchar(255) NOT NULL default '',
  "css_file" varchar(255) NOT NULL default '',
  "visibility" char(1) NOT NULL default 'y',
  "cacheable" char(1) NOT NULL default 'y',
  "expiration" bigint NOT NULL default '0',
  "description" text NOT NULL,
  PRIMARY KEY ("repID")
) ;


--
-- Dumping data for table 'tiki_integrator_reps'
--
INSERT INTO tiki_integrator_reps VALUES ('1','Doxygened (1.3.4) Documentation','','index.html','doxygen.css','n','y','0','Use this repository as rule source for all your repositories based on doxygened docs. To setup yours just add new repository and copy rules from this repository :)');


--
-- Table structure for table 'tiki_integrator_rules'
--
DROP TABLE "tiki_integrator_rules";

CREATE TABLE "tiki_integrator_rules" (
  "ruleID" bigserial,
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

) ;

CREATE  INDEX "tiki_integrator_rules_repID" ON "tiki_integrator_rules"("repID");

--
-- Dumping data for table 'tiki_integrator_rules'
--
INSERT INTO tiki_integrator_rules VALUES ('1','1','1','.*<body[^>]*?>(.*?)</body.*','\1','y','n','i','y','Extract code between <BODY> tags');

INSERT INTO tiki_integrator_rules VALUES ('2','1','2','img src=(\"|\')(?!http://)','img src=\1{path}/','y','n','i','y','Fix images path');

INSERT INTO tiki_integrator_rules VALUES ('3','1','3','href=(\"|\')(?!(--|(http|ftp)://))','href=\1tiki-integrator.php?repID={repID}&file=','y','n','i','y','Relace internal links to integrator. Dont touch an external links.');


--
-- Integrator permissions
--
INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_admin_integrator', 'Can admin integrator repositories and rules', 'admin', 'tiki');

INSERT INTO "users_permissions" ("permName","permDesc","level","type") VALUES ('tiki_p_view_integrator', 'Can view integrated repositories', 'basic', 'tiki');


--
-- Table structures for table 'tiki_quicktags'
-- 
DROP TABLE "tiki_quicktags";

CREATE TABLE "tiki_quicktags" (
  "tagId" serial,
  "taglabel" varchar(255) default NULL,
  "taginsert" varchar(255) default NULL,
  "tagicon" varchar(255) default NULL,
  PRIMARY KEY ("tagId")
)   ;


INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon") VALUES ('bold','__text__','images/ed_format_bold.gif');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon") VALUES ('italic','\'\'text\'\'','images/ed_format_italic.gif');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon") VALUES ('underline','===text===','images/ed_format_underline.gif');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon") VALUES ('table','||r1c1|r1c2||r2c1|r2c2||','images/insert_table.gif');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon") VALUES ('table new','||r1c1|r1c2\nr2c1|r2c2||','images/insert_table.gif');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon") VALUES ('external link','[http://example.com|text]','images/ed_link.gif');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon") VALUES ('wiki link','((text))','images/ed_copy.gif');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon") VALUES ('heading1','!text','images/ed_custom.gif');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon") VALUES ('title bar','-=text=-','images/fullscreen_maximize.gif');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon") VALUES ('box','^text^','images/ed_about.gif');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon") VALUES ('rss feed','{rss id= }','images/ico_link.gif');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon") VALUES ('dynamic content','{content id= }','images/book.gif');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon") VALUES ('tagline','{cookie}','images/footprint.gif');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon") VALUES ('hr','---','images/ed_hr.gif');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon") VALUES ('center text','::text::','images/ed_align_center.gif');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon") VALUES ('colored text','~~--FF0000:text~~','images/fontfamily.gif');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon") VALUES ('dynamic variable','%text%','images/book.gif');

INSERT INTO "tiki_quicktags" ("taglabel","taginsert","tagicon") VALUES ('image','{img src= width= height= align= desc= link= }','images/ed_image.gif');


--
-- Tiki Jukebox tables
--

DROP TABLE "tiki_jukebox_genres";

CREATE TABLE "tiki_jukebox_genres" (
  "genreId" bigserial,
  "genreName" varchar(80),
  "genreDescription" text,
  PRIMARY KEY ("genreId")
)   ;


DROP TABLE "tiki_jukebox_albums";

CREATE TABLE "tiki_jukebox_albums" (
  "albumId" bigserial,
  "title" varchar(80) default NULL,
  "description" text,
  "created" bigint,
  "lastModif" bigint,
  "user" varchar(200),
  "visits" bigint,
  "public" char(1),
  "genreId" bigint,
  PRIMARY KEY ("albumId")
)   ;


DROP TABLE "tiki_jukebox_tracks";

CREATE TABLE "tiki_jukebox_tracks" (
  "trackId" bigserial,
  "albumId" bigint,
  "artist" varchar(200),
  "title" varchar(200),
  "created" bigint,
  "url" varchar(255),
  "filename" varchar(80),
  "filesize" bigint,
  "filetype" varchar(250),
  "genreId" bigint,
  "plays" bigint,
  PRIMARY KEY ("trackId")
)  ;


--
-- End of tiki jukebox tables
--

--
-- Homework tables start
--
-- Created Feb 22, 2004
-- Revised Mar 19, 2004
--

DROP TABLE "hw_actionlog";

CREATE TABLE "hw_actionlog" (
  "action" varchar(255) NOT NULL default '',
  "lastModif" bigint NOT NULL default '0',
  "pageId" bigint default NULL,
  "user" varchar(200) default NULL,
  "ip" varchar(15) default NULL,
  "comment" varchar(200) default NULL,
  PRIMARY KEY ("lastModif")
) ;


DROP TABLE "hw_assignments";

CREATE TABLE "hw_assignments" (
  "assignmentId" serial,
  "title" varchar(80) default NULL,
  "teacherName" varchar(40) NOT NULL default '',
  "created" bigint NOT NULL default '0',
  "dueDate" bigint default NULL,
  "modified" bigint NOT NULL default '0',
  "heading" text,
  "body" text,
  "deleted" smallint NOT NULL default '0',
  PRIMARY KEY ("assignmentId")

) ;

CREATE  INDEX "hw_assignments_dueDate" ON "hw_assignments"("dueDate");

DROP TABLE "hw_grading_queue";

CREATE TABLE "hw_grading_queue" (
  "id" bigserial,
  "status" smallint default NULL,
  "submissionDate" bigint default NULL,
  "userLogin" varchar(40) NOT NULL default '',
  "userIp" varchar(15) default NULL,
  "pageId" bigint default NULL,
  "pageDate" bigint default NULL,
  "pageVersion" bigint default NULL,
  "assignmentId" bigint default NULL,
  PRIMARY KEY ("id")
) ;


DROP TABLE "hw_grading_queue";

CREATE TABLE "hw_grading_queue" (
  "id" bigserial,
  "status" smallint default NULL,
  "submissionDate" bigint default NULL,
  "userLogin" varchar(40) NOT NULL default '',
  "userIp" varchar(15) default NULL,
  "pageId" bigint default NULL,
  "pageDate" bigint default NULL,
  "pageVersion" bigint default NULL,
  "assignmentId" bigint default NULL,
  PRIMARY KEY ("id")
) ;


DROP TABLE "hw_history";

CREATE TABLE "hw_history" (
  "id" bigint NOT NULL default '0',
  "version" integer NOT NULL default '0',
  "lastModif" bigint NOT NULL default '0',
  "user" varchar(200) NOT NULL default '',
  "ip" varchar(15) NOT NULL default '',
  "comment" varchar(200) default NULL,
  "data" text,
  PRIMARY KEY ("id","version")
) ;


DROP TABLE "hw_pages";

CREATE TABLE "hw_pages" (
  "id" bigserial,
  "assignmentId" bigint NOT NULL default '0',
  "studentName" varchar(200) NOT NULL default '',
  "data" text,
  "description" varchar(200) default NULL,
  "lastModif" bigint default NULL,
  "user" varchar(200) default NULL,
  "comment" varchar(200) default NULL,
  "version" integer NOT NULL default '0',
  "ip" varchar(15) default NULL,
  "flag" char(1) default NULL,
  "points" integer default NULL,
  "votes" integer default NULL,
  "cache" text,
  "wiki_cache" bigint default '0',
  "cache_timestamp" bigint default NULL,
  "page_size" bigint unsigned default '0',
  "lockUser" varchar(200) default NULL,
  "lockExpires" bigint default '0',
  PRIMARY KEY ("studentName","assignmentId")



) ;

CREATE  INDEX "hw_pages_id" ON "hw_pages"("id");
CREATE  INDEX "hw_pages_assignmentId" ON "hw_pages"("assignmentId");
CREATE  INDEX "hw_pages_studentName" ON "hw_pages"("studentName");

--
-- Homework tables end
--
;

