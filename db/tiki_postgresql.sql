-- 
-- Created by SQL::Translator::Producer::PostgreSQL
-- Created on Sun Aug 17 00:59:13 2003
-- 
--
-- Table: galaxia_activities
--

DROP TABLE "galaxia_activities";
CREATE TABLE "galaxia_activities" (
  "activityId"  bigserial NOT NULL,
  "name" character varying(80) DEFAULT NULL,
  "normalized_name" character varying(80) DEFAULT NULL,
  "pId"  bigint DEFAULT '0' NOT NULL,
  "type" character varying(10) DEFAULT NULL,
  "isAutoRouted" char(1) DEFAULT NULL,
  "flowNum"  integer DEFAULT NULL,
  "isInteractive" char(1) DEFAULT NULL,
  "lastModif"  bigint DEFAULT NULL,
  "description" text,
  CONSTRAINT chk_galaxia_activities_type CHECK ("type" IN ('start','end','split','switch','join','activity','standalone')),
  CONSTRAINT pk_galaxia_activities PRIMARY KEY ("activityId")
);

--
-- Table: galaxia_activity_roles
--

DROP TABLE "galaxia_activity_roles";
CREATE TABLE "galaxia_activity_roles" (
  "activityId"  bigint DEFAULT '0' NOT NULL,
  "roleId"  bigint DEFAULT '0' NOT NULL,
  CONSTRAINT pk_galaxia_activity_roles PRIMARY KEY ("activityId", "roleId")
);

--
-- Table: galaxia_instance_activities
--

DROP TABLE "galaxia_instance_activities";
CREATE TABLE "galaxia_instance_activities" (
  "instanceId"  bigint DEFAULT '0' NOT NULL,
  "activityId"  bigint DEFAULT '0' NOT NULL,
  "started"  bigint DEFAULT '0' NOT NULL,
  "ended"  bigint DEFAULT '0' NOT NULL,
  "user_" character varying(200) DEFAULT NULL,
  "status" character varying(9) DEFAULT NULL,
  CONSTRAINT chk_galaxia_instance_activitie CHECK ("status" IN ('running','completed')),
  CONSTRAINT pk_galaxia_instance_activities PRIMARY KEY ("instanceId", "activityId")
);

--
-- Table: galaxia_instance_comments
--

DROP TABLE "galaxia_instance_comments";
CREATE TABLE "galaxia_instance_comments" (
  "cId"  bigserial NOT NULL,
  "instanceId"  bigint DEFAULT '0' NOT NULL,
  "user_" character varying(200) DEFAULT NULL,
  "activityId"  bigint DEFAULT NULL,
  "hash" character varying(32) DEFAULT NULL,
  "title" character varying(250) DEFAULT NULL,
  "comment" text,
  "activity" character varying(80) DEFAULT NULL,
  "timestamp"  bigint DEFAULT NULL,
  CONSTRAINT pk_galaxia_instance_comments PRIMARY KEY ("cId")
);

--
-- Table: galaxia_instances
--

DROP TABLE "galaxia_instances";
CREATE TABLE "galaxia_instances" (
  "instanceId"  bigserial NOT NULL,
  "pId"  bigint DEFAULT '0' NOT NULL,
  "started"  bigint DEFAULT NULL,
  "owner" character varying(200) DEFAULT NULL,
  "nextActivity"  bigint DEFAULT NULL,
  "nextUser" character varying(200) DEFAULT NULL,
  "ended"  bigint DEFAULT NULL,
  "status" character varying(9) DEFAULT NULL,
  "properties" bytea,
  CONSTRAINT chk_galaxia_instances_status CHECK ("status" IN ('active','exception','aborted','completed')),
  CONSTRAINT pk_galaxia_instances PRIMARY KEY ("instanceId")
);

--
-- Table: galaxia_processes
--

DROP TABLE "galaxia_processes";
CREATE TABLE "galaxia_processes" (
  "pId"  bigserial NOT NULL,
  "name" character varying(80) DEFAULT NULL,
  "isValid" char(1) DEFAULT NULL,
  "isActive" char(1) DEFAULT NULL,
  "version" character varying(12) DEFAULT NULL,
  "description" text,
  "lastModif"  bigint DEFAULT NULL,
  "normalized_name" character varying(80) DEFAULT NULL,
  CONSTRAINT pk_galaxia_processes PRIMARY KEY ("pId")
);

--
-- Table: galaxia_roles
--

DROP TABLE "galaxia_roles";
CREATE TABLE "galaxia_roles" (
  "roleId"  bigserial NOT NULL,
  "pId"  bigint DEFAULT '0' NOT NULL,
  "lastModif"  bigint DEFAULT NULL,
  "name" character varying(80) DEFAULT NULL,
  "description" text,
  CONSTRAINT pk_galaxia_roles PRIMARY KEY ("roleId")
);

--
-- Table: galaxia_transitions
--

DROP TABLE "galaxia_transitions";
CREATE TABLE "galaxia_transitions" (
  "pId"  bigint DEFAULT '0' NOT NULL,
  "actFromId"  bigint DEFAULT '0' NOT NULL,
  "actToId"  bigint DEFAULT '0' NOT NULL,
  CONSTRAINT pk_galaxia_transitions PRIMARY KEY ("actFromId", "actToId")
);

--
-- Table: galaxia_user_roles
--

DROP TABLE "galaxia_user_roles";
CREATE TABLE "galaxia_user_roles" (
  "pId"  bigint DEFAULT '0' NOT NULL,
  "roleId"  bigserial NOT NULL,
  "user_" character varying(200) DEFAULT '' NOT NULL,
  CONSTRAINT pk_galaxia_user_roles PRIMARY KEY ("roleId", "user_")
);

--
-- Table: galaxia_workitems
--

DROP TABLE "galaxia_workitems";
CREATE TABLE "galaxia_workitems" (
  "itemId"  bigserial NOT NULL,
  "instanceId"  bigint DEFAULT '0' NOT NULL,
  "orderId"  bigint DEFAULT '0' NOT NULL,
  "activityId"  bigint DEFAULT '0' NOT NULL,
  "properties" bytea,
  "started"  bigint DEFAULT NULL,
  "ended"  bigint DEFAULT NULL,
  "user_" character varying(200) DEFAULT NULL,
  CONSTRAINT pk_galaxia_workitems PRIMARY KEY ("itemId")
);

--
-- Table: messu_messages
--

DROP TABLE "messu_messages";
CREATE TABLE "messu_messages" (
  "msgId"  bigserial NOT NULL,
  "user_" character varying(200) DEFAULT '' NOT NULL,
  "user_from" character varying(200) DEFAULT '' NOT NULL,
  "user_to" text,
  "user_cc" text,
  "user_bcc" text,
  "subject" character varying(255) DEFAULT NULL,
  "body" text,
  "hash" character varying(32) DEFAULT NULL,
  "date"  bigint DEFAULT NULL,
  "isRead" char(1) DEFAULT NULL,
  "isReplied" char(1) DEFAULT NULL,
  "isFlagged" char(1) DEFAULT NULL,
  "priority"  smallint DEFAULT NULL,
  CONSTRAINT pk_messu_messages PRIMARY KEY ("msgId")
);

--
-- Table: tiki_actionlog
--

DROP TABLE "tiki_actionlog";
CREATE TABLE "tiki_actionlog" (
  "action" character varying(255) DEFAULT '' NOT NULL,
  "lastModif"  bigint DEFAULT NULL,
  "pageName" character varying(200) DEFAULT NULL,
  "user_" character varying(200) DEFAULT NULL,
  "ip" character varying(15) DEFAULT NULL,
  "comment" character varying(200) DEFAULT NULL
);

--
-- Table: tiki_articles
--

DROP TABLE "tiki_articles";
CREATE TABLE "tiki_articles" (
  "articleId"  serial NOT NULL,
  "title" character varying(80) DEFAULT NULL,
  "authorName" character varying(60) DEFAULT NULL,
  "topicId"  bigint DEFAULT NULL,
  "topicName" character varying(40) DEFAULT NULL,
  "size"  bigint DEFAULT NULL,
  "useImage" char(1) DEFAULT NULL,
  "image_name" character varying(80) DEFAULT NULL,
  "image_type" character varying(80) DEFAULT NULL,
  "image_size"  bigint DEFAULT NULL,
  "image_x"  smallint DEFAULT NULL,
  "image_y"  smallint DEFAULT NULL,
  "image_data" bytea,
  "publishDate"  bigint DEFAULT NULL,
  "created"  bigint DEFAULT NULL,
  "heading" text,
  "body" text,
  "hash" character varying(32) DEFAULT NULL,
  "author" character varying(200) DEFAULT NULL,
  "reads"  bigint DEFAULT NULL,
  "votes"  integer DEFAULT NULL,
  "points"  bigint DEFAULT NULL,
  "type" character varying(50) DEFAULT NULL,
  "rating" decimal(3, 2) DEFAULT NULL,
  "isfloat" char(1) DEFAULT NULL,
  CONSTRAINT pk_tiki_articles PRIMARY KEY ("articleId")
);

CREATE INDEX "title" on tiki_articles ("title");

CREATE INDEX "heading" on tiki_articles ("heading");

CREATE INDEX "body" on tiki_articles ("body");

CREATE INDEX "reads" on tiki_articles ("reads");

--
-- Table: tiki_banners
--

DROP TABLE "tiki_banners";
CREATE TABLE "tiki_banners" (
  "bannerId"  bigserial NOT NULL,
  "client" character varying(200) DEFAULT '' NOT NULL,
  "url" character varying(255) DEFAULT NULL,
  "title" character varying(255) DEFAULT NULL,
  "alt" character varying(250) DEFAULT NULL,
  "which" character varying(50) DEFAULT NULL,
  "imageData" bytea,
  "imageType" character varying(200) DEFAULT NULL,
  "imageName" character varying(100) DEFAULT NULL,
  "HTMLData" text,
  "fixedURLData" character varying(255) DEFAULT NULL,
  "textData" text,
  "fromDate"  bigint DEFAULT NULL,
  "toDate"  bigint DEFAULT NULL,
  "useDates" char(1) DEFAULT NULL,
  "mon" char(1) DEFAULT NULL,
  "tue" char(1) DEFAULT NULL,
  "wed" char(1) DEFAULT NULL,
  "thu" char(1) DEFAULT NULL,
  "fri" char(1) DEFAULT NULL,
  "sat" char(1) DEFAULT NULL,
  "sun" char(1) DEFAULT NULL,
  "hourFrom" character varying(4) DEFAULT NULL,
  "hourTo" character varying(4) DEFAULT NULL,
  "created"  bigint DEFAULT NULL,
  "maxImpressions"  integer DEFAULT NULL,
  "impressions"  integer DEFAULT NULL,
  "clicks"  integer DEFAULT NULL,
  "zone" character varying(40) DEFAULT NULL,
  CONSTRAINT pk_tiki_banners PRIMARY KEY ("bannerId")
);

--
-- Table: tiki_banning
--

DROP TABLE "tiki_banning";
CREATE TABLE "tiki_banning" (
  "banId"  bigserial NOT NULL,
  "mode" character varying(4) DEFAULT NULL,
  "title" character varying(200) DEFAULT NULL,
  "ip1" char(3) DEFAULT NULL,
  "ip2" char(3) DEFAULT NULL,
  "ip3" char(3) DEFAULT NULL,
  "ip4" char(3) DEFAULT NULL,
  "user_" character varying(200) DEFAULT NULL,
  "date_from" bigint NOT NULL,
  "date_to" bigint NOT NULL,
  "use_dates" char(1) DEFAULT NULL,
  "created"  bigint DEFAULT NULL,
  "message" text,
  CONSTRAINT chk_tiki_banning_mode CHECK ("mode" IN ('user','ip')),
  CONSTRAINT pk_tiki_banning PRIMARY KEY ("banId")
);

--
-- Table: tiki_banning_sections
--

DROP TABLE "tiki_banning_sections";
CREATE TABLE "tiki_banning_sections" (
  "banId"  bigint DEFAULT '0' NOT NULL,
  "section" character varying(100) DEFAULT '' NOT NULL,
  CONSTRAINT pk_tiki_banning_sections PRIMARY KEY ("banId", "section")
);

--
-- Table: tiki_blog_activity
--

DROP TABLE "tiki_blog_activity";
CREATE TABLE "tiki_blog_activity" (
  "blogId"  integer DEFAULT '0' NOT NULL,
  "day"  bigint DEFAULT '0' NOT NULL,
  "posts"  integer DEFAULT NULL,
  CONSTRAINT pk_tiki_blog_activity PRIMARY KEY ("blogId", "day")
);

--
-- Table: tiki_blog_posts
--

DROP TABLE "tiki_blog_posts";
CREATE TABLE "tiki_blog_posts" (
  "postId"  serial NOT NULL,
  "blogId"  integer DEFAULT '0' NOT NULL,
  "data" text,
  "created"  bigint DEFAULT NULL,
  "user_" character varying(200) DEFAULT NULL,
  "trackbacks_to" text,
  "trackbacks_from" text,
  "title" character varying(80) DEFAULT NULL,
  CONSTRAINT pk_tiki_blog_posts PRIMARY KEY ("postId")
);

CREATE INDEX "data" on tiki_blog_posts ("data");

CREATE INDEX "blogId" on tiki_blog_posts ("blogId");

CREATE INDEX "created" on tiki_blog_posts ("created");

--
-- Table: tiki_blog_posts_images
--

DROP TABLE "tiki_blog_posts_images";
CREATE TABLE "tiki_blog_posts_images" (
  "imgId"  bigserial NOT NULL,
  "postId"  bigint DEFAULT '0' NOT NULL,
  "filename" character varying(80) DEFAULT NULL,
  "filetype" character varying(80) DEFAULT NULL,
  "filesize"  bigint DEFAULT NULL,
  "data" bytea,
  CONSTRAINT pk_tiki_blog_posts_images PRIMARY KEY ("imgId")
);

--
-- Table: tiki_blogs
--

DROP TABLE "tiki_blogs";
CREATE TABLE "tiki_blogs" (
  "blogId"  serial NOT NULL,
  "created"  bigint DEFAULT NULL,
  "lastModif"  bigint DEFAULT NULL,
  "title" character varying(200) DEFAULT NULL,
  "description" text,
  "user_" character varying(200) DEFAULT NULL,
  "public_" char(1) DEFAULT NULL,
  "posts"  integer DEFAULT NULL,
  "maxPosts"  integer DEFAULT NULL,
  "hits"  integer DEFAULT NULL,
  "activity" decimal(4, 2) DEFAULT NULL,
  "heading" text,
  "use_find" char(1) DEFAULT NULL,
  "use_title" char(1) DEFAULT NULL,
  "add_date" char(1) DEFAULT NULL,
  "add_poster" char(1) DEFAULT NULL,
  "allow_comments" char(1) DEFAULT NULL,
  CONSTRAINT pk_tiki_blogs PRIMARY KEY ("blogId")
);

CREATE INDEX "title2" on tiki_blogs ("title");

CREATE INDEX "description" on tiki_blogs ("description");

CREATE INDEX "hits" on tiki_blogs ("hits");

--
-- Table: tiki_calendar_categories
--

DROP TABLE "tiki_calendar_categories";
CREATE TABLE "tiki_calendar_categories" (
  "calcatId"  serial NOT NULL,
  "calendarId"  bigint DEFAULT '0' NOT NULL,
  "name" character varying(255) DEFAULT '' NOT NULL,
  CONSTRAINT pk_tiki_calendar_categories PRIMARY KEY ("calcatId"),
  CONSTRAINT catname UNIQUE ("calendarId", "name")
);

--
-- Table: tiki_calendar_items
--

DROP TABLE "tiki_calendar_items";
CREATE TABLE "tiki_calendar_items" (
  "calitemId"  bigserial NOT NULL,
  "calendarId"  bigint DEFAULT '0' NOT NULL,
  "start"  bigint DEFAULT '0' NOT NULL,
  "end_"  bigint DEFAULT '0' NOT NULL,
  "locationId"  bigint DEFAULT NULL,
  "categoryId"  bigint DEFAULT NULL,
  "priority" character varying(1) DEFAULT '1' NOT NULL,
  "status" character varying(1) DEFAULT '0' NOT NULL,
  "url" character varying(255) DEFAULT NULL,
  "lang" char(2) DEFAULT 'en' NOT NULL,
  "name" character varying(255) DEFAULT '' NOT NULL,
  "description" bytea,
  "user_" character varying(40) DEFAULT NULL,
  "created"  bigint DEFAULT '0' NOT NULL,
  "lastmodif"  bigint DEFAULT '0' NOT NULL,
  CONSTRAINT chk_tiki_calendar_items_priori CHECK ("priority" IN ('1','2','3','4','5','6','7','8','9')),
  CONSTRAINT chk_tiki_calendar_items_status CHECK ("status" IN ('0','1','2')),
  CONSTRAINT pk_tiki_calendar_items PRIMARY KEY ("calitemId")
);

CREATE INDEX "calendarId" on tiki_calendar_items ("calendarId");

--
-- Table: tiki_calendar_locations
--

DROP TABLE "tiki_calendar_locations";
CREATE TABLE "tiki_calendar_locations" (
  "callocId"  bigserial NOT NULL,
  "calendarId"  bigint DEFAULT '0' NOT NULL,
  "name" character varying(255) DEFAULT '' NOT NULL,
  "description" bytea,
  CONSTRAINT pk_tiki_calendar_locations PRIMARY KEY ("callocId"),
  CONSTRAINT locname UNIQUE ("calendarId", "name")
);

--
-- Table: tiki_calendar_roles
--

DROP TABLE "tiki_calendar_roles";
CREATE TABLE "tiki_calendar_roles" (
  "calitemId"  bigint DEFAULT '0' NOT NULL,
  "username" character varying(40) DEFAULT '' NOT NULL,
  "role" character varying(1) DEFAULT '0' NOT NULL,
  CONSTRAINT chk_tiki_calendar_roles_role CHECK ("role" IN ('0','1','2','3','6')),
  CONSTRAINT pk_tiki_calendar_roles PRIMARY KEY ("calitemId", "username", "role")
);

--
-- Table: tiki_calendars
--

DROP TABLE "tiki_calendars";
CREATE TABLE "tiki_calendars" (
  "calendarId"  bigserial NOT NULL,
  "name" character varying(80) DEFAULT '' NOT NULL,
  "description" character varying(255) DEFAULT NULL,
  "user_" character varying(40) DEFAULT '' NOT NULL,
  "customlocations" character varying(1) DEFAULT 'n' NOT NULL,
  "customcategories" character varying(1) DEFAULT 'n' NOT NULL,
  "customlanguages" character varying(1) DEFAULT 'n' NOT NULL,
  "custompriorities" character varying(1) DEFAULT 'n' NOT NULL,
  "customparticipants" character varying(1) DEFAULT 'n' NOT NULL,
  "created"  bigint DEFAULT '0' NOT NULL,
  "lastmodif"  bigint DEFAULT '0' NOT NULL,
  CONSTRAINT chk_tiki_calendars_customlocat CHECK ("customlocations" IN ('n','y')),
  CONSTRAINT chk_tiki_calendars_customcateg CHECK ("customcategories" IN ('n','y')),
  CONSTRAINT chk_tiki_calendars_customlangu CHECK ("customlanguages" IN ('n','y')),
  CONSTRAINT chk_tiki_calendars_customprior CHECK ("custompriorities" IN ('n','y')),
  CONSTRAINT chk_tiki_calendars_customparti CHECK ("customparticipants" IN ('n','y')),
  CONSTRAINT pk_tiki_calendars PRIMARY KEY ("calendarId")
);

--
-- Table: tiki_categories
--

DROP TABLE "tiki_categories";
CREATE TABLE "tiki_categories" (
  "categId"  bigserial NOT NULL,
  "name" character varying(100) DEFAULT NULL,
  "description" character varying(250) DEFAULT NULL,
  "parentId"  bigint DEFAULT NULL,
  "hits"  integer DEFAULT NULL,
  CONSTRAINT pk_tiki_categories PRIMARY KEY ("categId")
);

--
-- Table: tiki_categorized_objects
--

DROP TABLE "tiki_categorized_objects";
CREATE TABLE "tiki_categorized_objects" (
  "catObjectId"  bigserial NOT NULL,
  "type" character varying(50) DEFAULT NULL,
  "objId" character varying(255) DEFAULT NULL,
  "description" text,
  "created"  bigint DEFAULT NULL,
  "name" character varying(200) DEFAULT NULL,
  "href" character varying(200) DEFAULT NULL,
  "hits"  integer DEFAULT NULL,
  CONSTRAINT pk_tiki_categorized_objects PRIMARY KEY ("catObjectId")
);

--
-- Table: tiki_category_objects
--

DROP TABLE "tiki_category_objects";
CREATE TABLE "tiki_category_objects" (
  "catObjectId"  bigint DEFAULT '0' NOT NULL,
  "categId"  bigint DEFAULT '0' NOT NULL,
  CONSTRAINT pk_tiki_category_objects PRIMARY KEY ("catObjectId", "categId")
);

--
-- Table: tiki_category_sites
--

DROP TABLE "tiki_category_sites";
CREATE TABLE "tiki_category_sites" (
  "categId"  integer DEFAULT '0' NOT NULL,
  "siteId"  bigint DEFAULT '0' NOT NULL,
  CONSTRAINT pk_tiki_category_sites PRIMARY KEY ("categId", "siteId")
);

--
-- Table: tiki_chart_items
--

DROP TABLE "tiki_chart_items";
CREATE TABLE "tiki_chart_items" (
  "itemId"  bigserial NOT NULL,
  "title" character varying(250) DEFAULT NULL,
  "description" text,
  "chartId"  bigint DEFAULT '0' NOT NULL,
  "created"  bigint DEFAULT NULL,
  "URL" character varying(250) DEFAULT NULL,
  "votes"  bigint DEFAULT NULL,
  "points"  bigint DEFAULT NULL,
  "average" decimal(4, 2) DEFAULT NULL,
  CONSTRAINT pk_tiki_chart_items PRIMARY KEY ("itemId")
);

--
-- Table: tiki_charts
--

DROP TABLE "tiki_charts";
CREATE TABLE "tiki_charts" (
  "chartId"  bigserial NOT NULL,
  "title" character varying(250) DEFAULT NULL,
  "description" text,
  "hits"  bigint DEFAULT NULL,
  "singleItemVotes" char(1) DEFAULT NULL,
  "singleChartVotes" char(1) DEFAULT NULL,
  "suggestions" char(1) DEFAULT NULL,
  "autoValidate" char(1) DEFAULT NULL,
  "topN"  integer DEFAULT NULL,
  "maxVoteValue"  smallint DEFAULT NULL,
  "frequency"  bigint DEFAULT NULL,
  "showAverage" char(1) DEFAULT NULL,
  "isActive" char(1) DEFAULT NULL,
  "showVotes" char(1) DEFAULT NULL,
  "useCookies" char(1) DEFAULT NULL,
  "lastChart"  bigint DEFAULT NULL,
  "voteAgainAfter"  bigint DEFAULT NULL,
  "created"  bigint DEFAULT NULL,
  "hist"  bigint DEFAULT NULL,
  CONSTRAINT pk_tiki_charts PRIMARY KEY ("chartId")
);

--
-- Table: tiki_charts_rankings
--

DROP TABLE "tiki_charts_rankings";
CREATE TABLE "tiki_charts_rankings" (
  "chartId"  bigint DEFAULT '0' NOT NULL,
  "itemId"  bigint DEFAULT '0' NOT NULL,
  "position"  bigint DEFAULT '0' NOT NULL,
  "timestamp"  bigint DEFAULT '0' NOT NULL,
  "lastPosition"  bigint DEFAULT '0' NOT NULL,
  "period"  bigint DEFAULT '0' NOT NULL,
  "rvotes"  bigint DEFAULT '0' NOT NULL,
  "raverage" decimal(4, 2) DEFAULT '0.00' NOT NULL,
  CONSTRAINT pk_tiki_charts_rankings PRIMARY KEY ("chartId", "itemId", "period")
);

--
-- Table: tiki_charts_votes
--

DROP TABLE "tiki_charts_votes";
CREATE TABLE "tiki_charts_votes" (
  "user_" character varying(200) DEFAULT '' NOT NULL,
  "itemId"  bigint DEFAULT '0' NOT NULL,
  "timestamp"  bigint DEFAULT NULL,
  "chartId"  bigint DEFAULT NULL,
  CONSTRAINT pk_tiki_charts_votes PRIMARY KEY ("user_", "itemId")
);

--
-- Table: tiki_chat_channels
--

DROP TABLE "tiki_chat_channels";
CREATE TABLE "tiki_chat_channels" (
  "channelId"  serial NOT NULL,
  "name" character varying(30) DEFAULT NULL,
  "description" character varying(250) DEFAULT NULL,
  "max_users"  integer DEFAULT NULL,
  "mode" char(1) DEFAULT NULL,
  "moderator" character varying(200) DEFAULT NULL,
  "active" char(1) DEFAULT NULL,
  "refresh"  integer DEFAULT NULL,
  CONSTRAINT pk_tiki_chat_channels PRIMARY KEY ("channelId")
);

--
-- Table: tiki_chat_messages
--

DROP TABLE "tiki_chat_messages";
CREATE TABLE "tiki_chat_messages" (
  "messageId"  serial NOT NULL,
  "channelId"  integer DEFAULT '0' NOT NULL,
  "data" character varying(255) DEFAULT NULL,
  "poster" character varying(200) DEFAULT 'anonymous' NOT NULL,
  "timestamp"  bigint DEFAULT NULL,
  CONSTRAINT pk_tiki_chat_messages PRIMARY KEY ("messageId")
);

--
-- Table: tiki_chat_users
--

DROP TABLE "tiki_chat_users";
CREATE TABLE "tiki_chat_users" (
  "nickname" character varying(200) DEFAULT '' NOT NULL,
  "channelId"  integer DEFAULT '0' NOT NULL,
  "timestamp"  bigint DEFAULT NULL,
  CONSTRAINT pk_tiki_chat_users PRIMARY KEY ("nickname", "channelId")
);

--
-- Table: tiki_comments
--

DROP TABLE "tiki_comments";
CREATE TABLE "tiki_comments" (
  "threadId"  bigserial NOT NULL,
  "object" character varying(32) DEFAULT '' NOT NULL,
  "parentId"  bigint DEFAULT NULL,
  "userName" character varying(200) DEFAULT NULL,
  "commentDate"  bigint DEFAULT NULL,
  "hits"  integer DEFAULT NULL,
  "type" char(1) DEFAULT NULL,
  "points" decimal(8, 2) DEFAULT NULL,
  "votes"  integer DEFAULT NULL,
  "average" decimal(8, 4) DEFAULT NULL,
  "title" character varying(100) DEFAULT NULL,
  "data" text,
  "hash" character varying(32) DEFAULT NULL,
  "user_ip" character varying(15) DEFAULT NULL,
  "summary" character varying(240) DEFAULT NULL,
  "smiley" character varying(80) DEFAULT NULL,
  "message_id" varchar(250) default NULL,
  "in_reply_to" varchar(250) default NULL,
  CONSTRAINT pk_tiki_comments PRIMARY KEY ("threadId")
);

CREATE INDEX "title3" on tiki_comments ("title");

CREATE INDEX "data2" on tiki_comments ("data");

CREATE INDEX "object" on tiki_comments ("object");

CREATE INDEX "hits2" on tiki_comments ("hits");

CREATE INDEX "tc_pi" on tiki_comments ("parentId");

--
-- Table: tiki_content
--

DROP TABLE "tiki_content";
CREATE TABLE "tiki_content" (
  "contentId"  serial NOT NULL,
  "description" text,
  CONSTRAINT pk_tiki_content PRIMARY KEY ("contentId")
);

--
-- Table: tiki_content_templates
--

DROP TABLE "tiki_content_templates";
CREATE TABLE "tiki_content_templates" (
  "templateId"  serial NOT NULL,
  "content" bytea,
  "name" character varying(200) DEFAULT NULL,
  "created"  bigint DEFAULT NULL,
  CONSTRAINT pk_tiki_content_templates PRIMARY KEY ("templateId")
);

--
-- Table: tiki_content_templates_section
--

DROP TABLE "tiki_content_templates_section";
CREATE TABLE "tiki_content_templates_section" (
  "templateId"  integer DEFAULT '0' NOT NULL,
  "section" character varying(250) DEFAULT '' NOT NULL,
  CONSTRAINT pk_tiki_content_templates_sect PRIMARY KEY ("templateId", "section")
);

--
-- Table: tiki_cookies
--

DROP TABLE "tiki_cookies";
CREATE TABLE "tiki_cookies" (
  "cookieId"  serial NOT NULL,
  "cookie" character varying(255) DEFAULT NULL,
  CONSTRAINT pk_tiki_cookies PRIMARY KEY ("cookieId")
);

--
-- Table: tiki_copyrights
--

DROP TABLE "tiki_copyrights";
CREATE TABLE "tiki_copyrights" (
  "copyrightId"  bigserial NOT NULL,
  "page" character varying(200) DEFAULT NULL,
  "title" character varying(200) DEFAULT NULL,
  "year"  bigint DEFAULT NULL,
  "authors" character varying(200) DEFAULT NULL,
  "copyright_order"  bigint DEFAULT NULL,
  "userName" character varying(200) DEFAULT NULL,
  CONSTRAINT pk_tiki_copyrights PRIMARY KEY ("copyrightId")
);

--
-- Table: tiki_directory_categories
--

DROP TABLE "tiki_directory_categories";
CREATE TABLE "tiki_directory_categories" (
  "categId"  serial NOT NULL,
  "parent"  integer DEFAULT NULL,
  "name" character varying(240) DEFAULT NULL,
  "description" text,
  "childrenType" char(1) DEFAULT NULL,
  "sites"  integer DEFAULT NULL,
  "viewableChildren"  smallint DEFAULT NULL,
  "allowSites" char(1) DEFAULT NULL,
  "showCount" char(1) DEFAULT NULL,
  "editorGroup" character varying(200) DEFAULT NULL,
  "hits"  bigint DEFAULT NULL,
  CONSTRAINT pk_tiki_directory_categories PRIMARY KEY ("categId")
);

--
-- Table: tiki_directory_search
--

DROP TABLE "tiki_directory_search";
CREATE TABLE "tiki_directory_search" (
  "term" character varying(250) DEFAULT '' NOT NULL,
  "hits"  bigint DEFAULT NULL,
  CONSTRAINT pk_tiki_directory_search PRIMARY KEY ("term")
);

--
-- Table: tiki_directory_sites
--

DROP TABLE "tiki_directory_sites";
CREATE TABLE "tiki_directory_sites" (
  "siteId"  bigserial NOT NULL,
  "name" character varying(240) DEFAULT NULL,
  "description" text,
  "url" character varying(255) DEFAULT NULL,
  "country" character varying(255) DEFAULT NULL,
  "hits"  bigint DEFAULT NULL,
  "isValid" char(1) DEFAULT NULL,
  "created"  bigint DEFAULT NULL,
  "lastModif"  bigint DEFAULT NULL,
  "cache" bytea,
  "cache_timestamp"  bigint DEFAULT NULL,
  CONSTRAINT pk_tiki_directory_sites PRIMARY KEY ("siteId")
);

--
-- Table: tiki_drawings
--

DROP TABLE "tiki_drawings";
CREATE TABLE "tiki_drawings" (
  "drawId"  bigserial NOT NULL,
  "version"  integer DEFAULT NULL,
  "name" character varying(250) DEFAULT NULL,
  "filename_draw" character varying(250) DEFAULT NULL,
  "filename_pad" character varying(250) DEFAULT NULL,
  "timestamp"  bigint DEFAULT NULL,
  "user_" character varying(200) DEFAULT NULL,
  CONSTRAINT pk_tiki_drawings PRIMARY KEY ("drawId")
);

--
-- Table: tiki_dsn
--

DROP TABLE "tiki_dsn";
CREATE TABLE "tiki_dsn" (
  "dsnId"  bigserial NOT NULL,
  "name" character varying(200) DEFAULT '' NOT NULL,
  "dsn" character varying(255) DEFAULT NULL,
  CONSTRAINT pk_tiki_dsn PRIMARY KEY ("dsnId")
);

--
-- Table: tiki_eph
--

DROP TABLE "tiki_eph";
CREATE TABLE "tiki_eph" (
  "ephId"  bigserial NOT NULL,
  "title" character varying(250) DEFAULT NULL,
  "isFile" char(1) DEFAULT NULL,
  "filename" character varying(250) DEFAULT NULL,
  "filetype" character varying(250) DEFAULT NULL,
  "filesize" character varying(250) DEFAULT NULL,
  "data" bytea,
  "textdata" bytea,
  "publish"  bigint DEFAULT NULL,
  "hits"  integer DEFAULT NULL,
  CONSTRAINT pk_tiki_eph PRIMARY KEY ("ephId")
);

--
-- Table: tiki_extwiki
--

DROP TABLE "tiki_extwiki";
CREATE TABLE "tiki_extwiki" (
  "extwikiId"  bigserial NOT NULL,
  "name" character varying(200) DEFAULT '' NOT NULL,
  "extwiki" character varying(255) DEFAULT NULL,
  CONSTRAINT pk_tiki_extwiki PRIMARY KEY ("extwikiId")
);

--
-- Table: tiki_faq_questions
--

DROP TABLE "tiki_faq_questions";
CREATE TABLE "tiki_faq_questions" (
  "questionId"  serial NOT NULL,
  "faqId"  integer DEFAULT NULL,
  "position"  smallint DEFAULT NULL,
  "question" text,
  "answer" text,
  CONSTRAINT pk_tiki_faq_questions PRIMARY KEY ("questionId")
);

CREATE INDEX "faqId" on tiki_faq_questions ("faqId");

CREATE INDEX "question" on tiki_faq_questions ("question");

CREATE INDEX "answer" on tiki_faq_questions ("answer");

--
-- Table: tiki_faqs
--

DROP TABLE "tiki_faqs";
CREATE TABLE "tiki_faqs" (
  "faqId"  serial NOT NULL,
  "title" character varying(200) DEFAULT NULL,
  "description" text,
  "created"  bigint DEFAULT NULL,
  "questions"  integer DEFAULT NULL,
  "hits"  integer DEFAULT NULL,
  "canSuggest" char(1) DEFAULT NULL,
  CONSTRAINT pk_tiki_faqs PRIMARY KEY ("faqId")
);

CREATE INDEX "title4" on tiki_faqs ("title");

CREATE INDEX "description2" on tiki_faqs ("description");

CREATE INDEX "hits3" on tiki_faqs ("hits");

--
-- Table: tiki_featured_links
--

DROP TABLE "tiki_featured_links";
CREATE TABLE "tiki_featured_links" (
  "url" character varying(200) DEFAULT '' NOT NULL,
  "title" character varying(200) DEFAULT NULL,
  "description" text,
  "hits"  integer DEFAULT NULL,
  "position"  integer DEFAULT NULL,
  "type" char(1) DEFAULT NULL,
  CONSTRAINT pk_tiki_featured_links PRIMARY KEY ("url")
);

--
-- Table: tiki_file_galleries
--

DROP TABLE "tiki_file_galleries";
CREATE TABLE "tiki_file_galleries" (
  "galleryId"  bigserial NOT NULL,
  "name" character varying(80) DEFAULT '' NOT NULL,
  "description" text,
  "created"  bigint DEFAULT NULL,
  "visible" char(1) DEFAULT NULL,
  "lastModif"  bigint DEFAULT NULL,
  "user_" character varying(200) DEFAULT NULL,
  "hits"  bigint DEFAULT NULL,
  "votes"  integer DEFAULT NULL,
  "points" decimal(8, 2) DEFAULT NULL,
  "maxRows"  integer DEFAULT NULL,
  "public_" char(1) DEFAULT NULL,
  "show_id" char(1) DEFAULT NULL,
  "show_icon" char(1) DEFAULT NULL,
  "show_name" char(1) DEFAULT NULL,
  "show_size" char(1) DEFAULT NULL,
  "show_description" char(1) DEFAULT NULL,
  "max_desc"  integer DEFAULT NULL,
  "show_created" char(1) DEFAULT NULL,
  "show_dl" char(1) DEFAULT NULL,
  CONSTRAINT pk_tiki_file_galleries PRIMARY KEY ("galleryId")
);

--
-- Table: tiki_files
--

DROP TABLE "tiki_files";
CREATE TABLE "tiki_files" (
  "fileId"  bigserial NOT NULL,
  "galleryId"  bigint DEFAULT '0' NOT NULL,
  "name" character varying(200) DEFAULT '' NOT NULL,
  "description" text,
  "created"  bigint DEFAULT NULL,
  "filename" character varying(80) DEFAULT NULL,
  "filesize"  bigint DEFAULT NULL,
  "filetype" character varying(250) DEFAULT NULL,
  "data" bytea,
  "user_" character varying(200) DEFAULT NULL,
  "downloads"  bigint DEFAULT NULL,
  "votes"  integer DEFAULT NULL,
  "points" decimal(8, 2) DEFAULT NULL,
  "path" character varying(255) DEFAULT NULL,
  "reference_url" character varying(250) DEFAULT NULL,
  "is_reference" char(1) DEFAULT NULL,
  "hash" character varying(32) DEFAULT NULL,
  CONSTRAINT pk_tiki_files PRIMARY KEY ("fileId")
);

CREATE INDEX "name" on tiki_files ("name");

CREATE INDEX "description3" on tiki_files ("description");

CREATE INDEX "downloads" on tiki_files ("downloads");

--
-- Table: tiki_forum_attachments
--

DROP TABLE "tiki_forum_attachments";
CREATE TABLE "tiki_forum_attachments" (
  "attId"  bigserial NOT NULL,
  "threadId"  bigint DEFAULT '0' NOT NULL,
  "qId"  bigint DEFAULT '0' NOT NULL,
  "forumId"  bigint DEFAULT NULL,
  "filename" character varying(250) DEFAULT NULL,
  "filetype" character varying(250) DEFAULT NULL,
  "filesize"  bigint DEFAULT NULL,
  "data" bytea,
  "dir" character varying(200) DEFAULT NULL,
  "created"  bigint DEFAULT NULL,
  "path" character varying(250) DEFAULT NULL,
  CONSTRAINT pk_tiki_forum_attachments PRIMARY KEY ("attId")
);

--
-- Table: tiki_forum_reads
--

DROP TABLE "tiki_forum_reads";
CREATE TABLE "tiki_forum_reads" (
  "user_" character varying(200) DEFAULT '' NOT NULL,
  "threadId"  bigint DEFAULT '0' NOT NULL,
  "forumId"  bigint DEFAULT NULL,
  "timestamp"  bigint DEFAULT NULL,
  CONSTRAINT pk_tiki_forum_reads PRIMARY KEY ("user_", "threadId")
);

--
-- Table: tiki_forums
--

DROP TABLE "tiki_forums";
CREATE TABLE "tiki_forums" (
  "forumId"  serial NOT NULL,
  "name" character varying(200) DEFAULT NULL,
  "description" text,
  "created"  bigint DEFAULT NULL,
  "lastPost"  bigint DEFAULT NULL,
  "threads"  integer DEFAULT NULL,
  "comments"  integer DEFAULT NULL,
  "controlFlood" char(1) DEFAULT NULL,
  "floodInterval"  integer DEFAULT NULL,
  "moderator" character varying(200) DEFAULT NULL,
  "hits"  integer DEFAULT NULL,
  "mail" character varying(200) DEFAULT NULL,
  "useMail" char(1) DEFAULT NULL,
  "section" character varying(200) DEFAULT NULL,
  "usePruneUnreplied" char(1) DEFAULT NULL,
  "pruneUnrepliedAge"  integer DEFAULT NULL,
  "usePruneOld" char(1) DEFAULT NULL,
  "pruneMaxAge"  integer DEFAULT NULL,
  "topicsPerPage"  integer DEFAULT NULL,
  "topicOrdering" character varying(100) DEFAULT NULL,
  "threadOrdering" character varying(100) DEFAULT NULL,
  "att" character varying(80) DEFAULT NULL,
  "att_store" character varying(4) DEFAULT NULL,
  "att_store_dir" character varying(250) DEFAULT NULL,
  "att_max_size"  bigint DEFAULT NULL,
  "ui_level" char(1) DEFAULT NULL,
  "forum_password" character varying(32) DEFAULT NULL,
  "forum_use_password" char(1) DEFAULT NULL,
  "moderator_group" character varying(200) DEFAULT NULL,
  "approval_type" character varying(20) DEFAULT NULL,
  "outbound_address" character varying(250) DEFAULT NULL,
  "outbound_mails_for_inbound_mails" char(1) DEFAULT NULL,
  "outbound_mails_reply_link" char(1) DEFAULT NULL,
  "outbound_from" varchar(250) default NULL,
  "inbound_pop_server" character varying(250) DEFAULT NULL,
  "inbound_pop_port"  smallint DEFAULT NULL,
  "inbound_pop_user" character varying(200) DEFAULT NULL,
  "inbound_pop_password" character varying(80) DEFAULT NULL,
  "topic_smileys" char(1) DEFAULT NULL,
  "ui_avatar" char(1) DEFAULT NULL,
  "ui_flag" char(1) DEFAULT NULL,
  "ui_posts" char(1) DEFAULT NULL,
  "ui_email" char(1) DEFAULT NULL,
  "ui_online" char(1) DEFAULT NULL,
  "topic_summary" char(1) DEFAULT NULL,
  "show_description" char(1) DEFAULT NULL,
  "topics_list_replies" char(1) DEFAULT NULL,
  "topics_list_reads" char(1) DEFAULT NULL,
  "topics_list_pts" char(1) DEFAULT NULL,
  "topics_list_lastpost" char(1) DEFAULT NULL,
  "topics_list_author" char(1) DEFAULT NULL,
  "vote_threads" char(1) DEFAULT NULL,
  CONSTRAINT pk_tiki_forums PRIMARY KEY ("forumId")
);

--
-- Table: tiki_forums_queue
--

DROP TABLE "tiki_forums_queue";
CREATE TABLE "tiki_forums_queue" (
  "qId"  bigserial NOT NULL,
  "object" character varying(32) DEFAULT NULL,
  "parentId"  bigint DEFAULT NULL,
  "forumId"  bigint DEFAULT NULL,
  "timestamp"  bigint DEFAULT NULL,
  "user_" character varying(200) DEFAULT NULL,
  "title" character varying(240) DEFAULT NULL,
  "data" text,
  "type" character varying(60) DEFAULT NULL,
  "hash" character varying(32) DEFAULT NULL,
  "topic_smiley" character varying(80) DEFAULT NULL,
  "topic_title" character varying(240) DEFAULT NULL,
  "summary" character varying(240) DEFAULT NULL,
  CONSTRAINT pk_tiki_forums_queue PRIMARY KEY ("qId")
);

--
-- Table: tiki_forums_reported
--

DROP TABLE "tiki_forums_reported";
CREATE TABLE "tiki_forums_reported" (
  "threadId"  bigint DEFAULT '0' NOT NULL,
  "forumId"  bigint DEFAULT '0' NOT NULL,
  "parentId"  bigint DEFAULT '0' NOT NULL,
  "user_" character varying(200) DEFAULT NULL,
  "timestamp"  bigint DEFAULT NULL,
  "reason" character varying(250) DEFAULT NULL,
  CONSTRAINT pk_tiki_forums_reported PRIMARY KEY ("threadId")
);

--
-- Table: tiki_galleries
--

DROP TABLE "tiki_galleries";
CREATE TABLE "tiki_galleries" (
  "galleryId"  bigserial NOT NULL,
  "name" character varying(80) DEFAULT '' NOT NULL,
  "description" text,
  "created"  bigint DEFAULT NULL,
  "lastModif"  bigint DEFAULT NULL,
  "visible" char(1) DEFAULT NULL,
  "theme" character varying(60) DEFAULT NULL,
  "user_" character varying(200) DEFAULT NULL,
  "hits"  bigint DEFAULT NULL,
  "maxRows"  integer DEFAULT NULL,
  "rowImages"  integer DEFAULT NULL,
  "thumbSizeX"  integer DEFAULT NULL,
  "thumbSizeY"  integer DEFAULT NULL,
  "public_" char(1) DEFAULT NULL,
  CONSTRAINT pk_tiki_galleries PRIMARY KEY ("galleryId")
);

CREATE INDEX "name2" on tiki_galleries ("name");

CREATE INDEX "description4" on tiki_galleries ("description");

CREATE INDEX "hits4" on tiki_galleries ("hits");

--
-- Table: tiki_galleries_scales
--

DROP TABLE "tiki_galleries_scales";
CREATE TABLE "tiki_galleries_scales" (
  "galleryId"  bigint DEFAULT '0' NOT NULL,
  "xsize"  bigint DEFAULT '0' NOT NULL,
  "ysize"  bigint DEFAULT '0' NOT NULL,
  CONSTRAINT pk_tiki_galleries_scales PRIMARY KEY ("galleryId", "xsize", "ysize")
);

--
-- Table: tiki_games
--

DROP TABLE "tiki_games";
CREATE TABLE "tiki_games" (
  "gameName" character varying(200) DEFAULT '' NOT NULL,
  "hits"  integer DEFAULT NULL,
  "votes"  integer DEFAULT NULL,
  "points"  integer DEFAULT NULL,
  CONSTRAINT pk_tiki_games PRIMARY KEY ("gameName")
);

--
-- Table: tiki_group_inclusion
--

DROP TABLE "tiki_group_inclusion";
CREATE TABLE "tiki_group_inclusion" (
  "groupName" character varying(30) DEFAULT '' NOT NULL,
  "includeGroup" character varying(30) DEFAULT '' NOT NULL,
  CONSTRAINT pk_tiki_group_inclusion PRIMARY KEY ("groupName", "includeGroup")
);

--
-- Table: tiki_history
--

DROP TABLE "tiki_history";
CREATE TABLE "tiki_history" (
  "pageName" character varying(160) DEFAULT '' NOT NULL,
  "version"  integer DEFAULT '0' NOT NULL,
  "lastModif"  bigint DEFAULT NULL,
  "description" character varying(200) DEFAULT NULL,
  "user_" character varying(200) DEFAULT NULL,
  "ip" character varying(15) DEFAULT NULL,
  "comment" character varying(200) DEFAULT NULL,
  "data" bytea,
  CONSTRAINT pk_tiki_history PRIMARY KEY ("pageName", "version")
);

--
-- Table: tiki_hotwords
--

DROP TABLE "tiki_hotwords";
CREATE TABLE "tiki_hotwords" (
  "word" character varying(40) DEFAULT '' NOT NULL,
  "url" character varying(255) DEFAULT '' NOT NULL,
  CONSTRAINT pk_tiki_hotwords PRIMARY KEY ("word")
);

--
-- Table: tiki_html_pages
--

DROP TABLE "tiki_html_pages";
CREATE TABLE "tiki_html_pages" (
  "pageName" character varying(200) DEFAULT '' NOT NULL,
  "content" bytea,
  "refresh"  integer DEFAULT NULL,
  "type" char(1) DEFAULT NULL,
  "created"  bigint DEFAULT NULL,
  CONSTRAINT pk_tiki_html_pages PRIMARY KEY ("pageName")
);

--
-- Table: tiki_html_pages_dynamic_zones
--

DROP TABLE "tiki_html_pages_dynamic_zones";
CREATE TABLE "tiki_html_pages_dynamic_zones" (
  "pageName" character varying(40) DEFAULT '' NOT NULL,
  "zone" character varying(80) DEFAULT '' NOT NULL,
  "type" char(2) DEFAULT NULL,
  "content" text,
  CONSTRAINT pk_tiki_html_pages_dynamic_zon PRIMARY KEY ("pageName", "zone")
);

--
-- Table: tiki_images
--

DROP TABLE "tiki_images";
CREATE TABLE "tiki_images" (
  "imageId"  bigserial NOT NULL,
  "galleryId"  bigint DEFAULT '0' NOT NULL,
  "name" character varying(200) DEFAULT '' NOT NULL,
  "description" text,
  "created"  bigint DEFAULT NULL,
  "user_" character varying(200) DEFAULT NULL,
  "hits"  bigint DEFAULT NULL,
  "path" character varying(255) DEFAULT NULL,
  CONSTRAINT pk_tiki_images PRIMARY KEY ("imageId")
);

CREATE INDEX "name3" on tiki_images ("name");

CREATE INDEX "description5" on tiki_images ("description");

CREATE INDEX "hits5" on tiki_images ("hits");

CREATE INDEX "ti_gId" on tiki_images ("galleryId");

CREATE INDEX "ti_cr" on tiki_images ("created");

CREATE INDEX "ti_us" on tiki_images ("user_");

--
-- Table: tiki_images_data
--

DROP TABLE "tiki_images_data";
CREATE TABLE "tiki_images_data" (
  "imageId"  bigint DEFAULT '0' NOT NULL,
  "xsize"  integer DEFAULT '0' NOT NULL,
  "ysize"  integer DEFAULT '0' NOT NULL,
  "type" char(1) DEFAULT '' NOT NULL,
  "filesize"  bigint DEFAULT NULL,
  "filetype" character varying(80) DEFAULT NULL,
  "filename" character varying(80) DEFAULT NULL,
  "data" bytea,
  CONSTRAINT pk_tiki_images_data PRIMARY KEY ("imageId", "xsize", "ysize", "type")
);

CREATE INDEX "t_i_d_it" on tiki_images_data ("imageId", "type");

--
-- Table: tiki_language
--

DROP TABLE "tiki_language";
CREATE TABLE "tiki_language" (
  "source" bytea NOT NULL,
  "lang" char(2) DEFAULT '' NOT NULL,
  "tran" bytea,
  CONSTRAINT pk_tiki_language PRIMARY KEY ("source", "lang")
);

--
-- Table: tiki_languages
--

DROP TABLE "tiki_languages";
CREATE TABLE "tiki_languages" (
  "lang" char(2) DEFAULT '' NOT NULL,
  "language" character varying(255) DEFAULT NULL,
  CONSTRAINT pk_tiki_languages PRIMARY KEY ("lang")
);

--
-- Table: tiki_link_cache
--

DROP TABLE "tiki_link_cache";
CREATE TABLE "tiki_link_cache" (
  "cacheId"  bigserial NOT NULL,
  "url" character varying(250) DEFAULT NULL,
  "data" bytea,
  "refresh"  bigint DEFAULT NULL,
  CONSTRAINT pk_tiki_link_cache PRIMARY KEY ("cacheId")
);

--
-- Table: tiki_links
--

DROP TABLE "tiki_links";
CREATE TABLE "tiki_links" (
  "fromPage" character varying(160) DEFAULT '' NOT NULL,
  "toPage" character varying(160) DEFAULT '' NOT NULL,
  CONSTRAINT pk_tiki_links PRIMARY KEY ("fromPage", "toPage")
);

--
-- Table: tiki_live_support_events
--

DROP TABLE "tiki_live_support_events";
CREATE TABLE "tiki_live_support_events" (
  "eventId"  bigserial NOT NULL,
  "reqId" character varying(32) DEFAULT '' NOT NULL,
  "type" character varying(40) DEFAULT NULL,
  "seqId"  bigint DEFAULT NULL,
  "senderId" character varying(32) DEFAULT NULL,
  "data" text,
  "timestamp"  bigint DEFAULT NULL,
  CONSTRAINT pk_tiki_live_support_events PRIMARY KEY ("eventId")
);

--
-- Table: tiki_live_support_message_comm
--

DROP TABLE "tiki_live_support_message_comm";
CREATE TABLE "tiki_live_support_message_comm" (
  "cId"  bigserial NOT NULL,
  "msgId"  bigint DEFAULT NULL,
  "data" text,
  "timestamp"  bigint DEFAULT NULL,
  CONSTRAINT pk_tiki_live_support_message_c PRIMARY KEY ("cId")
);

--
-- Table: tiki_live_support_messages
--

DROP TABLE "tiki_live_support_messages";
CREATE TABLE "tiki_live_support_messages" (
  "msgId"  bigserial NOT NULL,
  "data" text,
  "timestamp"  bigint DEFAULT NULL,
  "user_" character varying(200) DEFAULT NULL,
  "username" character varying(200) DEFAULT NULL,
  "priority"  smallint DEFAULT NULL,
  "status" char(1) DEFAULT NULL,
  "assigned_to" character varying(200) DEFAULT NULL,
  "resolution" character varying(100) DEFAULT NULL,
  "title" character varying(200) DEFAULT NULL,
  "module"  smallint DEFAULT NULL,
  "email" character varying(250) DEFAULT NULL,
  CONSTRAINT pk_tiki_live_support_messages PRIMARY KEY ("msgId")
);

--
-- Table: tiki_live_support_modules
--

DROP TABLE "tiki_live_support_modules";
CREATE TABLE "tiki_live_support_modules" (
  "modId"  serial NOT NULL,
  "name" character varying(90) DEFAULT NULL,
  CONSTRAINT pk_tiki_live_support_modules PRIMARY KEY ("modId")
);

--
-- Table: tiki_live_support_operators
--

DROP TABLE "tiki_live_support_operators";
CREATE TABLE "tiki_live_support_operators" (
  "user_" character varying(200) DEFAULT '' NOT NULL,
  "accepted_requests"  integer DEFAULT NULL,
  "status" character varying(20) DEFAULT NULL,
  "longest_chat"  integer DEFAULT NULL,
  "shortest_chat"  integer DEFAULT NULL,
  "average_chat"  integer DEFAULT NULL,
  "last_chat"  bigint DEFAULT NULL,
  "time_online"  integer DEFAULT NULL,
  "votes"  integer DEFAULT NULL,
  "points"  integer DEFAULT NULL,
  "status_since"  bigint DEFAULT NULL,
  CONSTRAINT pk_tiki_live_support_operators PRIMARY KEY ("user_")
);

--
-- Table: tiki_live_support_requests
--

DROP TABLE "tiki_live_support_requests";
CREATE TABLE "tiki_live_support_requests" (
  "reqId" character varying(32) DEFAULT '' NOT NULL,
  "user_" character varying(200) DEFAULT NULL,
  "tiki_user" character varying(200) DEFAULT NULL,
  "email" character varying(200) DEFAULT NULL,
  "operator" character varying(200) DEFAULT NULL,
  "operator_id" character varying(32) DEFAULT NULL,
  "user_id" character varying(32) DEFAULT NULL,
  "reason" text,
  "req_timestamp"  bigint DEFAULT NULL,
  "timestamp"  bigint DEFAULT NULL,
  "status" character varying(40) DEFAULT NULL,
  "resolution" character varying(40) DEFAULT NULL,
  "chat_started"  bigint DEFAULT NULL,
  "chat_ended"  bigint DEFAULT NULL,
  CONSTRAINT pk_tiki_live_support_requests PRIMARY KEY ("reqId")
);

--
-- Table: tiki_mail_events
--

DROP TABLE "tiki_mail_events";
CREATE TABLE "tiki_mail_events" (
  "event" character varying(200) DEFAULT NULL,
  "object" character varying(200) DEFAULT NULL,
  "email" character varying(200) DEFAULT NULL
);

--
-- Table: tiki_mailin_accounts
--

DROP TABLE "tiki_mailin_accounts";
CREATE TABLE "tiki_mailin_accounts" (
  "accountId"  bigserial NOT NULL,
  "user_" character varying(200) DEFAULT '' NOT NULL,
  "account" character varying(50) DEFAULT '' NOT NULL,
  "pop" character varying(255) DEFAULT NULL,
  "port"  smallint DEFAULT NULL,
  "username" character varying(100) DEFAULT NULL,
  "pass" character varying(100) DEFAULT NULL,
  "active" char(1) DEFAULT NULL,
  "type" character varying(40) DEFAULT NULL,
  "smtp" character varying(255) DEFAULT NULL,
  "useAuth" char(1) DEFAULT NULL,
  "smtpPort"  smallint DEFAULT NULL,
  CONSTRAINT pk_tiki_mailin_accounts PRIMARY KEY ("accountId")
);

--
-- Table: tiki_menu_languages
--

DROP TABLE "tiki_menu_languages";
CREATE TABLE "tiki_menu_languages" (
  "menuId"  serial NOT NULL,
  "language" char(2) DEFAULT '' NOT NULL,
  CONSTRAINT pk_tiki_menu_languages PRIMARY KEY ("menuId", "language")
);

--
-- Table: tiki_menu_options
--

DROP TABLE "tiki_menu_options";
CREATE TABLE "tiki_menu_options" (
  "optionId"  serial NOT NULL,
  "menuId"  integer DEFAULT NULL,
  "type" char(1) DEFAULT NULL,
  "name" character varying(200) DEFAULT NULL,
  "url" character varying(255) DEFAULT NULL,
  "position"  smallint DEFAULT NULL,
  CONSTRAINT pk_tiki_menu_options PRIMARY KEY ("optionId")
);

--
-- Table: tiki_menus
--

DROP TABLE "tiki_menus";
CREATE TABLE "tiki_menus" (
  "menuId"  serial NOT NULL,
  "name" character varying(200) DEFAULT '' NOT NULL,
  "description" text,
  "type" char(1) DEFAULT NULL,
  CONSTRAINT pk_tiki_menus PRIMARY KEY ("menuId")
);

--
-- Table: tiki_minical_events
--

DROP TABLE "tiki_minical_events";
CREATE TABLE "tiki_minical_events" (
  "user_" character varying(200) DEFAULT NULL,
  "eventId"  bigserial NOT NULL,
  "title" character varying(250) DEFAULT NULL,
  "description" text,
  "start"  bigint DEFAULT NULL,
  "end_"  bigint DEFAULT NULL,
  "security" char(1) DEFAULT NULL,
  "duration"  smallint DEFAULT NULL,
  "topicId"  bigint DEFAULT NULL,
  "reminded" char(1) DEFAULT NULL,
  CONSTRAINT pk_tiki_minical_events PRIMARY KEY ("eventId")
);

--
-- Table: tiki_minical_topics
--

DROP TABLE "tiki_minical_topics";
CREATE TABLE "tiki_minical_topics" (
  "user_" character varying(200) DEFAULT NULL,
  "topicId"  bigserial NOT NULL,
  "name" character varying(250) DEFAULT NULL,
  "filename" character varying(200) DEFAULT NULL,
  "filetype" character varying(200) DEFAULT NULL,
  "filesize" character varying(200) DEFAULT NULL,
  "data" bytea,
  "path" character varying(250) DEFAULT NULL,
  "isIcon" char(1) DEFAULT NULL,
  CONSTRAINT pk_tiki_minical_topics PRIMARY KEY ("topicId")
);

--
-- Table: tiki_modules
--

DROP TABLE "tiki_modules";
CREATE TABLE "tiki_modules" (
  "name" character varying(200) DEFAULT '' NOT NULL,
  "position" char(1) DEFAULT NULL,
  "ord"  smallint DEFAULT NULL,
  "type" char(1) DEFAULT NULL,
  "title" character varying(40) DEFAULT NULL,
  "cache_time"  bigint DEFAULT NULL,
  "rows"  smallint DEFAULT NULL,
  "params" character varying(255) DEFAULT NULL,
  "groups" text,
  CONSTRAINT pk_tiki_modules PRIMARY KEY ("name")
);

--
-- Table: tiki_newsletter_subscriptions
--

DROP TABLE "tiki_newsletter_subscriptions";
CREATE TABLE "tiki_newsletter_subscriptions" (
  "nlId"  bigint DEFAULT '0' NOT NULL,
  "email" character varying(255) DEFAULT '' NOT NULL,
  "code" character varying(32) DEFAULT NULL,
  "valid" char(1) DEFAULT NULL,
  "subscribed"  bigint DEFAULT NULL,
  CONSTRAINT pk_tiki_newsletter_subscriptio PRIMARY KEY ("nlId", "email")
);

--
-- Table: tiki_newsletters
--

DROP TABLE "tiki_newsletters";
CREATE TABLE "tiki_newsletters" (
  "nlId"  bigserial NOT NULL,
  "name" character varying(200) DEFAULT NULL,
  "description" text,
  "created"  bigint DEFAULT NULL,
  "lastSent"  bigint DEFAULT NULL,
  "editions"  integer DEFAULT NULL,
  "users"  integer DEFAULT NULL,
  "allowAnySub" char(1) DEFAULT NULL,
  "frequency"  bigint DEFAULT NULL,
  CONSTRAINT pk_tiki_newsletters PRIMARY KEY ("nlId")
);

--
-- Table: tiki_newsreader_marks
--

DROP TABLE "tiki_newsreader_marks";
CREATE TABLE "tiki_newsreader_marks" (
  "user_" character varying(200) DEFAULT '' NOT NULL,
  "serverId"  bigint DEFAULT '0' NOT NULL,
  "groupName" character varying(255) DEFAULT '' NOT NULL,
  "timestamp"  bigint DEFAULT '0' NOT NULL,
  CONSTRAINT pk_tiki_newsreader_marks PRIMARY KEY ("user_", "serverId", "groupName")
);

--
-- Table: tiki_newsreader_servers
--

DROP TABLE "tiki_newsreader_servers";
CREATE TABLE "tiki_newsreader_servers" (
  "user_" character varying(200) DEFAULT '' NOT NULL,
  "serverId"  bigserial NOT NULL,
  "server" character varying(250) DEFAULT NULL,
  "port"  smallint DEFAULT NULL,
  "username" character varying(200) DEFAULT NULL,
  "password" character varying(200) DEFAULT NULL,
  CONSTRAINT pk_tiki_newsreader_servers PRIMARY KEY ("serverId")
);

--
-- Table: tiki_page_footnotes
--

DROP TABLE "tiki_page_footnotes";
CREATE TABLE "tiki_page_footnotes" (
  "user_" character varying(200) DEFAULT '' NOT NULL,
  "pageName" character varying(250) DEFAULT '' NOT NULL,
  "data" text,
  CONSTRAINT pk_tiki_page_footnotes PRIMARY KEY ("user_", "pageName")
);

--
-- Table: tiki_pages
--

DROP TABLE "tiki_pages";
CREATE TABLE "tiki_pages" (
  "pageName" character varying(160) DEFAULT '' NOT NULL,
  "hits"  integer DEFAULT NULL,
  "data" text,
  "description" character varying(200) DEFAULT NULL,
  "lastModif"  bigint DEFAULT NULL,
  "comment" character varying(200) DEFAULT NULL,
  "version"  integer DEFAULT '0' NOT NULL,
  "user_" character varying(200) DEFAULT NULL,
  "ip" character varying(15) DEFAULT NULL,
  "flag" char(1) DEFAULT NULL,
  "points"  integer DEFAULT NULL,
  "votes"  integer DEFAULT NULL,
  "cache" text,
  "cache_timestamp"  bigint DEFAULT NULL,
  "pageRank" decimal(4, 3) DEFAULT NULL,
  "creator" character varying(200) DEFAULT NULL,
  CONSTRAINT pk_tiki_pages PRIMARY KEY ("pageName")
);

CREATE INDEX "data3" on tiki_pages ("data");

CREATE INDEX "pageRank" on tiki_pages ("pageRank");

--
-- Table: tiki_pageviews
--

DROP TABLE "tiki_pageviews";
CREATE TABLE "tiki_pageviews" (
  "day"  bigint DEFAULT '0' NOT NULL,
  "pageviews"  bigint DEFAULT NULL,
  CONSTRAINT pk_tiki_pageviews PRIMARY KEY ("day")
);

--
-- Table: tiki_poll_options
--

DROP TABLE "tiki_poll_options";
CREATE TABLE "tiki_poll_options" (
  "pollId"  integer DEFAULT '0' NOT NULL,
  "optionId"  serial NOT NULL,
  "title" character varying(200) DEFAULT NULL,
  "votes"  integer DEFAULT NULL,
  CONSTRAINT pk_tiki_poll_options PRIMARY KEY ("optionId")
);

--
-- Table: tiki_polls
--

DROP TABLE "tiki_polls";
CREATE TABLE "tiki_polls" (
  "pollId"  serial NOT NULL,
  "title" character varying(200) DEFAULT NULL,
  "votes"  integer DEFAULT NULL,
  "active" char(1) DEFAULT NULL,
  "publishDate"  bigint DEFAULT NULL,
  CONSTRAINT pk_tiki_polls PRIMARY KEY ("pollId")
);

--
-- Table: tiki_preferences
--

DROP TABLE "tiki_preferences";
CREATE TABLE "tiki_preferences" (
  "name" character varying(40) DEFAULT '' NOT NULL,
  "value" character varying(250) DEFAULT NULL,
  CONSTRAINT pk_tiki_preferences PRIMARY KEY ("name")
);

--
-- Table: tiki_private_messages
--

DROP TABLE "tiki_private_messages";
CREATE TABLE "tiki_private_messages" (
  "messageId"  serial NOT NULL,
  "toNickname" character varying(200) DEFAULT '' NOT NULL,
  "data" character varying(255) DEFAULT NULL,
  "poster" character varying(200) DEFAULT 'anonymous' NOT NULL,
  "timestamp"  bigint DEFAULT NULL,
  CONSTRAINT pk_tiki_private_messages PRIMARY KEY ("messageId")
);

--
-- Table: tiki_programmed_content
--

DROP TABLE "tiki_programmed_content";
CREATE TABLE "tiki_programmed_content" (
  "pId"  serial NOT NULL,
  "contentId"  integer DEFAULT '0' NOT NULL,
  "publishDate"  bigint DEFAULT '0' NOT NULL,
  "data" text,
  CONSTRAINT pk_tiki_programmed_content PRIMARY KEY ("pId")
);

--
-- Table: tiki_quiz_question_options
--

DROP TABLE "tiki_quiz_question_options";
CREATE TABLE "tiki_quiz_question_options" (
  "optionId"  serial NOT NULL,
  "questionId"  integer DEFAULT NULL,
  "optionText" text,
  "points"  smallint DEFAULT NULL,
  CONSTRAINT pk_tiki_quiz_question_options PRIMARY KEY ("optionId")
);

--
-- Table: tiki_quiz_questions
--

DROP TABLE "tiki_quiz_questions";
CREATE TABLE "tiki_quiz_questions" (
  "questionId"  serial NOT NULL,
  "quizId"  integer DEFAULT NULL,
  "question" text,
  "position"  smallint DEFAULT NULL,
  "type" char(1) DEFAULT NULL,
  "maxPoints"  smallint DEFAULT NULL,
  CONSTRAINT pk_tiki_quiz_questions PRIMARY KEY ("questionId")
);

--
-- Table: tiki_quiz_results
--

DROP TABLE "tiki_quiz_results";
CREATE TABLE "tiki_quiz_results" (
  "resultId"  serial NOT NULL,
  "quizId"  integer DEFAULT NULL,
  "fromPoints"  smallint DEFAULT NULL,
  "toPoints"  smallint DEFAULT NULL,
  "answer" text,
  CONSTRAINT pk_tiki_quiz_results PRIMARY KEY ("resultId")
);

--
-- Table: tiki_quiz_stats
--

DROP TABLE "tiki_quiz_stats";
CREATE TABLE "tiki_quiz_stats" (
  "quizId"  integer DEFAULT '0' NOT NULL,
  "questionId"  integer DEFAULT '0' NOT NULL,
  "optionId"  integer DEFAULT '0' NOT NULL,
  "votes"  integer DEFAULT NULL,
  CONSTRAINT pk_tiki_quiz_stats PRIMARY KEY ("quizId", "questionId", "optionId")
);

--
-- Table: tiki_quiz_stats_sum
--

DROP TABLE "tiki_quiz_stats_sum";
CREATE TABLE "tiki_quiz_stats_sum" (
  "quizId"  integer DEFAULT '0' NOT NULL,
  "quizName" character varying(255) DEFAULT NULL,
  "timesTaken"  integer DEFAULT NULL,
  "avgpoints" decimal(5, 2) DEFAULT NULL,
  "avgavg" decimal(5, 2) DEFAULT NULL,
  "avgtime" decimal(5, 2) DEFAULT NULL,
  CONSTRAINT pk_tiki_quiz_stats_sum PRIMARY KEY ("quizId")
);

--
-- Table: tiki_quizzes
--

DROP TABLE "tiki_quizzes";
CREATE TABLE "tiki_quizzes" (
  "quizId"  serial NOT NULL,
  "name" character varying(255) DEFAULT NULL,
  "description" text,
  "canRepeat" char(1) DEFAULT NULL,
  "storeResults" char(1) DEFAULT NULL,
  "questionsPerPage"  smallint DEFAULT NULL,
  "timeLimited" char(1) DEFAULT NULL,
  "timeLimit"  bigint DEFAULT NULL,
  "created"  bigint DEFAULT NULL,
  "taken"  integer DEFAULT NULL,
  CONSTRAINT pk_tiki_quizzes PRIMARY KEY ("quizId")
);

--
-- Table: tiki_received_articles
--

DROP TABLE "tiki_received_articles";
CREATE TABLE "tiki_received_articles" (
  "receivedArticleId"  bigserial NOT NULL,
  "receivedFromSite" character varying(200) DEFAULT NULL,
  "receivedFromUser" character varying(200) DEFAULT NULL,
  "receivedDate"  bigint DEFAULT NULL,
  "title" character varying(80) DEFAULT NULL,
  "authorName" character varying(60) DEFAULT NULL,
  "size"  bigint DEFAULT NULL,
  "useImage" char(1) DEFAULT NULL,
  "image_name" character varying(80) DEFAULT NULL,
  "image_type" character varying(80) DEFAULT NULL,
  "image_size"  bigint DEFAULT NULL,
  "image_x"  smallint DEFAULT NULL,
  "image_y"  smallint DEFAULT NULL,
  "image_data" bytea,
  "publishDate"  bigint DEFAULT NULL,
  "created"  bigint DEFAULT NULL,
  "heading" text,
  "body" bytea,
  "hash" character varying(32) DEFAULT NULL,
  "author" character varying(200) DEFAULT NULL,
  "type" character varying(50) DEFAULT NULL,
  "rating" decimal(3, 2) DEFAULT NULL,
  CONSTRAINT pk_tiki_received_articles PRIMARY KEY ("receivedArticleId")
);

--
-- Table: tiki_received_pages
--

DROP TABLE "tiki_received_pages";
CREATE TABLE "tiki_received_pages" (
  "receivedPageId"  bigserial NOT NULL,
  "pageName" character varying(160) DEFAULT '' NOT NULL,
  "data" bytea,
  "description" character varying(200) DEFAULT NULL,
  "comment" character varying(200) DEFAULT NULL,
  "receivedFromSite" character varying(200) DEFAULT NULL,
  "receivedFromUser" character varying(200) DEFAULT NULL,
  "receivedDate"  bigint DEFAULT NULL,
  CONSTRAINT pk_tiki_received_pages PRIMARY KEY ("receivedPageId")
);

--
-- Table: tiki_referer_stats
--

DROP TABLE "tiki_referer_stats";
CREATE TABLE "tiki_referer_stats" (
  "referer" character varying(50) DEFAULT '' NOT NULL,
  "hits"  integer DEFAULT NULL,
  "last"  bigint DEFAULT NULL,
  CONSTRAINT pk_tiki_referer_stats PRIMARY KEY ("referer")
);

--
-- Table: tiki_related_categories
--

DROP TABLE "tiki_related_categories";
CREATE TABLE "tiki_related_categories" (
  "categId"  integer DEFAULT '0' NOT NULL,
  "relatedTo"  integer DEFAULT '0' NOT NULL,
  CONSTRAINT pk_tiki_related_categories PRIMARY KEY ("categId", "relatedTo")
);

--
-- Table: tiki_rss_modules
--

DROP TABLE "tiki_rss_modules";
CREATE TABLE "tiki_rss_modules" (
  "rssId"  serial NOT NULL,
  "name" character varying(30) DEFAULT '' NOT NULL,
  "description" text,
  "url" character varying(255) DEFAULT '' NOT NULL,
  "refresh"  integer DEFAULT NULL,
  "lastUpdated"  bigint DEFAULT NULL,
  "content" bytea,
  CONSTRAINT pk_tiki_rss_modules PRIMARY KEY ("rssId")
);

--
-- Table: tiki_search_stats
--

DROP TABLE "tiki_search_stats";
CREATE TABLE "tiki_search_stats" (
  "term" character varying(50) DEFAULT '' NOT NULL,
  "hits"  integer DEFAULT NULL,
  CONSTRAINT pk_tiki_search_stats PRIMARY KEY ("term")
);

--
-- Table: tiki_semaphores
--

DROP TABLE "tiki_semaphores";
CREATE TABLE "tiki_semaphores" (
  "semName" character varying(250) DEFAULT '' NOT NULL,
  "user_" character varying(200) DEFAULT NULL,
  "timestamp"  bigint DEFAULT NULL,
  CONSTRAINT pk_tiki_semaphores PRIMARY KEY ("semName")
);

--
-- Table: tiki_sent_newsletters
--

DROP TABLE "tiki_sent_newsletters";
CREATE TABLE "tiki_sent_newsletters" (
  "editionId"  bigserial NOT NULL,
  "nlId"  bigint DEFAULT '0' NOT NULL,
  "users"  integer DEFAULT NULL,
  "sent"  bigint DEFAULT NULL,
  "subject" character varying(200) DEFAULT NULL,
  "data" bytea,
  CONSTRAINT pk_tiki_sent_newsletters PRIMARY KEY ("editionId")
);

--
-- Table: tiki_sessions
--

DROP TABLE "tiki_sessions";
CREATE TABLE "tiki_sessions" (
  "sessionId" character varying(32) DEFAULT '' NOT NULL,
  "user_" character varying(200) DEFAULT NULL,
  "timestamp"  bigint DEFAULT NULL,
  CONSTRAINT pk_tiki_sessions PRIMARY KEY ("sessionId")
);

--
-- Table: tiki_shoutbox
--

DROP TABLE "tiki_shoutbox";
CREATE TABLE "tiki_shoutbox" (
  "msgId"  serial NOT NULL,
  "message" character varying(255) DEFAULT NULL,
  "timestamp"  bigint DEFAULT NULL,
  "user_" character varying(200) DEFAULT NULL,
  "hash" character varying(32) DEFAULT NULL,
  CONSTRAINT pk_tiki_shoutbox PRIMARY KEY ("msgId")
);

--
-- Table: tiki_structures
--

DROP TABLE "tiki_structures";
CREATE TABLE "tiki_structures" (
  "page" character varying(240) DEFAULT '' NOT NULL,
  "parent" character varying(240) DEFAULT '' NOT NULL,
  "pos"  smallint DEFAULT NULL,
  CONSTRAINT pk_tiki_structures PRIMARY KEY ("page", "parent")
);

--
-- Table: tiki_submissions
--

DROP TABLE "tiki_submissions";
CREATE TABLE "tiki_submissions" (
  "subId"  serial NOT NULL,
  "title" character varying(80) DEFAULT NULL,
  "authorName" character varying(60) DEFAULT NULL,
  "topicId"  bigint DEFAULT NULL,
  "topicName" character varying(40) DEFAULT NULL,
  "size"  bigint DEFAULT NULL,
  "useImage" char(1) DEFAULT NULL,
  "image_name" character varying(80) DEFAULT NULL,
  "image_type" character varying(80) DEFAULT NULL,
  "image_size"  bigint DEFAULT NULL,
  "image_x"  smallint DEFAULT NULL,
  "image_y"  smallint DEFAULT NULL,
  "image_data" bytea,
  "publishDate"  bigint DEFAULT NULL,
  "created"  bigint DEFAULT NULL,
  "heading" text,
  "body" text,
  "hash" character varying(32) DEFAULT NULL,
  "author" character varying(200) DEFAULT NULL,
  "reads"  bigint DEFAULT NULL,
  "votes"  integer DEFAULT NULL,
  "points"  bigint DEFAULT NULL,
  "type" character varying(50) DEFAULT NULL,
  "rating" decimal(3, 2) DEFAULT NULL,
  "isfloat" char(1) DEFAULT NULL,
  CONSTRAINT pk_tiki_submissions PRIMARY KEY ("subId")
);

--
-- Table: tiki_suggested_faq_questions
--

DROP TABLE "tiki_suggested_faq_questions";
CREATE TABLE "tiki_suggested_faq_questions" (
  "sfqId"  serial NOT NULL,
  "faqId"  integer DEFAULT '0' NOT NULL,
  "question" text,
  "answer" text,
  "created"  bigint DEFAULT NULL,
  "user_" character varying(200) DEFAULT NULL,
  CONSTRAINT pk_tiki_suggested_faq_question PRIMARY KEY ("sfqId")
);

--
-- Table: tiki_survey_question_options
--

DROP TABLE "tiki_survey_question_options";
CREATE TABLE "tiki_survey_question_options" (
  "optionId"  bigserial NOT NULL,
  "questionId"  bigint DEFAULT '0' NOT NULL,
  "qoption" text,
  "votes"  integer DEFAULT NULL,
  CONSTRAINT pk_tiki_survey_question_option PRIMARY KEY ("optionId")
);

--
-- Table: tiki_survey_questions
--

DROP TABLE "tiki_survey_questions";
CREATE TABLE "tiki_survey_questions" (
  "questionId"  bigserial NOT NULL,
  "surveyId"  bigint DEFAULT '0' NOT NULL,
  "question" text,
  "options" text,
  "type" char(1) DEFAULT NULL,
  "position"  integer DEFAULT NULL,
  "votes"  integer DEFAULT NULL,
  "value"  integer DEFAULT NULL,
  "average" decimal(4, 2) DEFAULT NULL,
  CONSTRAINT pk_tiki_survey_questions PRIMARY KEY ("questionId")
);

--
-- Table: tiki_surveys
--

DROP TABLE "tiki_surveys";
CREATE TABLE "tiki_surveys" (
  "surveyId"  bigserial NOT NULL,
  "name" character varying(200) DEFAULT NULL,
  "description" text,
  "taken"  integer DEFAULT NULL,
  "lastTaken"  bigint DEFAULT NULL,
  "created"  bigint DEFAULT NULL,
  "status" char(1) DEFAULT NULL,
  CONSTRAINT pk_tiki_surveys PRIMARY KEY ("surveyId")
);

--
-- Table: tiki_tags
--

DROP TABLE "tiki_tags";
CREATE TABLE "tiki_tags" (
  "tagName" character varying(80) DEFAULT '' NOT NULL,
  "pageName" character varying(160) DEFAULT '' NOT NULL,
  "hits"  integer DEFAULT NULL,
  "description" character varying(200) DEFAULT NULL,
  "data" bytea,
  "lastModif"  bigint DEFAULT NULL,
  "comment" character varying(200) DEFAULT NULL,
  "version"  integer DEFAULT '0' NOT NULL,
  "user_" character varying(200) DEFAULT NULL,
  "ip" character varying(15) DEFAULT NULL,
  "flag" char(1) DEFAULT NULL,
  CONSTRAINT pk_tiki_tags PRIMARY KEY ("tagName", "pageName")
);

--
-- Table: tiki_theme_control_categs
--

DROP TABLE "tiki_theme_control_categs";
CREATE TABLE "tiki_theme_control_categs" (
  "categId"  bigint DEFAULT '0' NOT NULL,
  "theme" character varying(250) DEFAULT '' NOT NULL,
  CONSTRAINT pk_tiki_theme_control_categs PRIMARY KEY ("categId")
);

--
-- Table: tiki_theme_control_objects
--

DROP TABLE "tiki_theme_control_objects";
CREATE TABLE "tiki_theme_control_objects" (
  "objId" character varying(250) DEFAULT '' NOT NULL,
  "type" character varying(250) DEFAULT '' NOT NULL,
  "name" character varying(250) DEFAULT '' NOT NULL,
  "theme" character varying(250) DEFAULT '' NOT NULL,
  CONSTRAINT pk_tiki_theme_control_objects PRIMARY KEY ("objId")
);

--
-- Table: tiki_theme_control_sections
--

DROP TABLE "tiki_theme_control_sections";
CREATE TABLE "tiki_theme_control_sections" (
  "section" character varying(250) DEFAULT '' NOT NULL,
  "theme" character varying(250) DEFAULT '' NOT NULL,
  CONSTRAINT pk_tiki_theme_control_sections PRIMARY KEY ("section")
);

--
-- Table: tiki_topics
--

DROP TABLE "tiki_topics";
CREATE TABLE "tiki_topics" (
  "topicId"  bigserial NOT NULL,
  "name" character varying(40) DEFAULT NULL,
  "image_name" character varying(80) DEFAULT NULL,
  "image_type" character varying(80) DEFAULT NULL,
  "image_size"  bigint DEFAULT NULL,
  "image_data" bytea,
  "active" char(1) DEFAULT NULL,
  "created"  bigint DEFAULT NULL,
  CONSTRAINT pk_tiki_topics PRIMARY KEY ("topicId")
);

--
-- Table: tiki_tracker_fields
--

DROP TABLE "tiki_tracker_fields";
CREATE TABLE "tiki_tracker_fields" (
  "fieldId"  bigserial NOT NULL,
  "trackerId"  bigint DEFAULT '0' NOT NULL,
  "name" character varying(80) DEFAULT NULL,
  "options" text,
  "type" char(1) DEFAULT NULL,
  "isMain" char(1) DEFAULT NULL,
  "isTblVisible" char(1) DEFAULT NULL,
  CONSTRAINT pk_tiki_tracker_fields PRIMARY KEY ("fieldId")
);

--
-- Table: tiki_tracker_item_attachments
--

DROP TABLE "tiki_tracker_item_attachments";
CREATE TABLE "tiki_tracker_item_attachments" (
  "attId"  bigserial NOT NULL,
  "itemId" character varying(40) DEFAULT '' NOT NULL,
  "filename" character varying(80) DEFAULT NULL,
  "filetype" character varying(80) DEFAULT NULL,
  "filesize"  bigint DEFAULT NULL,
  "user_" character varying(200) DEFAULT NULL,
  "data" bytea,
  "path" character varying(255) DEFAULT NULL,
  "downloads"  integer DEFAULT NULL,
  "created"  bigint DEFAULT NULL,
  "comment" character varying(250) DEFAULT NULL,
  CONSTRAINT pk_tiki_tracker_item_attachmen PRIMARY KEY ("attId")
);

--
-- Table: tiki_tracker_item_comments
--

DROP TABLE "tiki_tracker_item_comments";
CREATE TABLE "tiki_tracker_item_comments" (
  "commentId"  bigserial NOT NULL,
  "itemId"  bigint DEFAULT '0' NOT NULL,
  "user_" character varying(200) DEFAULT NULL,
  "data" text,
  "title" character varying(200) DEFAULT NULL,
  "posted"  bigint DEFAULT NULL,
  CONSTRAINT pk_tiki_tracker_item_comments PRIMARY KEY ("commentId")
);

--
-- Table: tiki_tracker_item_fields
--

DROP TABLE "tiki_tracker_item_fields";
CREATE TABLE "tiki_tracker_item_fields" (
  "itemId"  bigint DEFAULT '0' NOT NULL,
  "fieldId"  bigint DEFAULT '0' NOT NULL,
  "value" text,
  CONSTRAINT pk_tiki_tracker_item_fields PRIMARY KEY ("itemId", "fieldId")
);

--
-- Table: tiki_tracker_items
--

DROP TABLE "tiki_tracker_items";
CREATE TABLE "tiki_tracker_items" (
  "itemId"  bigserial NOT NULL,
  "trackerId"  bigint DEFAULT '0' NOT NULL,
  "created"  bigint DEFAULT NULL,
  "status" char(1) DEFAULT NULL,
  "lastModif"  bigint DEFAULT NULL,
  CONSTRAINT pk_tiki_tracker_items PRIMARY KEY ("itemId")
);

--
-- Table: tiki_trackers
--

DROP TABLE "tiki_trackers";
CREATE TABLE "tiki_trackers" (
  "trackerId"  bigserial NOT NULL,
  "name" character varying(80) DEFAULT NULL,
  "description" text,
  "created"  bigint DEFAULT NULL,
  "lastModif"  bigint DEFAULT NULL,
  "showCreated" char(1) DEFAULT NULL,
  "showStatus" char(1) DEFAULT NULL,
  "showLastModif" char(1) DEFAULT NULL,
  "useComments" char(1) DEFAULT NULL,
  "useAttachments" char(1) DEFAULT NULL,
  "items"  integer DEFAULT NULL,
  CONSTRAINT pk_tiki_trackers PRIMARY KEY ("trackerId")
);

--
-- Table: tiki_untranslated
--

DROP TABLE "tiki_untranslated";
CREATE TABLE "tiki_untranslated" (
  "id"  bigserial NOT NULL,
  "source" bytea NOT NULL,
  "lang" char(2) DEFAULT '' NOT NULL,
  CONSTRAINT pk_tiki_untranslated PRIMARY KEY ("source", "lang"),
  CONSTRAINT id UNIQUE ("id")
);

CREATE INDEX "id_2" on tiki_untranslated ("id");

--
-- Table: tiki_user_answers
--

DROP TABLE "tiki_user_answers";
CREATE TABLE "tiki_user_answers" (
  "userResultId"  integer DEFAULT '0' NOT NULL,
  "quizId"  integer DEFAULT '0' NOT NULL,
  "questionId"  integer DEFAULT '0' NOT NULL,
  "optionId"  integer DEFAULT '0' NOT NULL,
  CONSTRAINT pk_tiki_user_answers PRIMARY KEY ("userResultId", "quizId", "questionId", "optionId")
);

--
-- Table: tiki_user_assigned_modules
--

DROP TABLE "tiki_user_assigned_modules";
CREATE TABLE "tiki_user_assigned_modules" (
  "name" character varying(200) DEFAULT '' NOT NULL,
  "position" char(1) DEFAULT NULL,
  "ord"  smallint DEFAULT NULL,
  "type" char(1) DEFAULT NULL,
  "title" character varying(40) DEFAULT NULL,
  "cache_time"  bigint DEFAULT NULL,
  "rows"  smallint DEFAULT NULL,
  "groups" text,
  "params" character varying(250) DEFAULT NULL,
  "user_" character varying(200) DEFAULT '' NOT NULL,
  CONSTRAINT pk_tiki_user_assigned_modules PRIMARY KEY ("name", "user_")
);

--
-- Table: tiki_user_bookmarks_folders
--

DROP TABLE "tiki_user_bookmarks_folders";
CREATE TABLE "tiki_user_bookmarks_folders" (
  "folderId"  bigserial NOT NULL,
  "parentId"  bigint DEFAULT NULL,
  "user_" character varying(200) DEFAULT '' NOT NULL,
  "name" character varying(30) DEFAULT NULL,
  CONSTRAINT pk_tiki_user_bookmarks_folders PRIMARY KEY ("user_", "folderId")
);

--
-- Table: tiki_user_bookmarks_urls
--

DROP TABLE "tiki_user_bookmarks_urls";
CREATE TABLE "tiki_user_bookmarks_urls" (
  "urlId"  bigserial NOT NULL,
  "name" character varying(30) DEFAULT NULL,
  "url" character varying(250) DEFAULT NULL,
  "data" bytea,
  "lastUpdated"  bigint DEFAULT NULL,
  "folderId"  bigint DEFAULT '0' NOT NULL,
  "user_" character varying(200) DEFAULT '' NOT NULL,
  CONSTRAINT pk_tiki_user_bookmarks_urls PRIMARY KEY ("urlId")
);

--
-- Table: tiki_user_mail_accounts
--

DROP TABLE "tiki_user_mail_accounts";
CREATE TABLE "tiki_user_mail_accounts" (
  "accountId"  bigserial NOT NULL,
  "user_" character varying(200) DEFAULT '' NOT NULL,
  "account" character varying(50) DEFAULT '' NOT NULL,
  "pop" character varying(255) DEFAULT NULL,
  "current" char(1) DEFAULT NULL,
  "port"  smallint DEFAULT NULL,
  "username" character varying(100) DEFAULT NULL,
  "pass" character varying(100) DEFAULT NULL,
  "msgs"  smallint DEFAULT NULL,
  "smtp" character varying(255) DEFAULT NULL,
  "useAuth" char(1) DEFAULT NULL,
  "smtpPort"  smallint DEFAULT NULL,
  CONSTRAINT pk_tiki_user_mail_accounts PRIMARY KEY ("accountId")
);

--
-- Table: tiki_user_menus
--

DROP TABLE "tiki_user_menus";
CREATE TABLE "tiki_user_menus" (
  "user_" character varying(200) DEFAULT '' NOT NULL,
  "menuId"  bigserial NOT NULL,
  "url" character varying(250) DEFAULT NULL,
  "name" character varying(40) DEFAULT NULL,
  "position"  smallint DEFAULT NULL,
  "mode" char(1) DEFAULT NULL,
  CONSTRAINT pk_tiki_user_menus PRIMARY KEY ("menuId")
);

--
-- Table: tiki_user_modules
--

DROP TABLE "tiki_user_modules";
CREATE TABLE "tiki_user_modules" (
  "name" character varying(200) DEFAULT '' NOT NULL,
  "title" character varying(40) DEFAULT NULL,
  "data" bytea,
  CONSTRAINT pk_tiki_user_modules PRIMARY KEY ("name")
);

--
-- Table: tiki_user_notes
--

DROP TABLE "tiki_user_notes";
CREATE TABLE "tiki_user_notes" (
  "user_" character varying(200) DEFAULT '' NOT NULL,
  "noteId"  bigserial NOT NULL,
  "created"  bigint DEFAULT NULL,
  "name" character varying(255) DEFAULT NULL,
  "lastModif"  bigint DEFAULT NULL,
  "data" text,
  "size"  bigint DEFAULT NULL,
  "parse_mode" character varying(20) DEFAULT NULL,
  CONSTRAINT pk_tiki_user_notes PRIMARY KEY ("noteId")
);

--
-- Table: tiki_user_postings
--

DROP TABLE "tiki_user_postings";
CREATE TABLE "tiki_user_postings" (
  "user_" character varying(200) DEFAULT '' NOT NULL,
  "posts"  bigint DEFAULT NULL,
  "last"  bigint DEFAULT NULL,
  "first"  bigint DEFAULT NULL,
  "level"  integer DEFAULT NULL,
  CONSTRAINT pk_tiki_user_postings PRIMARY KEY ("user_")
);

--
-- Table: tiki_user_preferences
--

DROP TABLE "tiki_user_preferences";
CREATE TABLE "tiki_user_preferences" (
  "user_" character varying(200) DEFAULT '' NOT NULL,
  "prefName" character varying(40) DEFAULT '' NOT NULL,
  "value" character varying(250) DEFAULT NULL,
  CONSTRAINT pk_tiki_user_preferences PRIMARY KEY ("user_", "prefName")
);

--
-- Table: tiki_user_quizzes
--

DROP TABLE "tiki_user_quizzes";
CREATE TABLE "tiki_user_quizzes" (
  "user_" character varying(100) DEFAULT NULL,
  "quizId"  integer DEFAULT NULL,
  "timestamp"  bigint DEFAULT NULL,
  "timeTaken"  bigint DEFAULT NULL,
  "points"  bigint DEFAULT NULL,
  "maxPoints"  bigint DEFAULT NULL,
  "resultId"  integer DEFAULT NULL,
  "userResultId"  serial NOT NULL,
  CONSTRAINT pk_tiki_user_quizzes PRIMARY KEY ("userResultId")
);

--
-- Table: tiki_user_taken_quizzes
--

DROP TABLE "tiki_user_taken_quizzes";
CREATE TABLE "tiki_user_taken_quizzes" (
  "user_" character varying(200) DEFAULT '' NOT NULL,
  "quizId" character varying(255) DEFAULT '' NOT NULL,
  CONSTRAINT pk_tiki_user_taken_quizzes PRIMARY KEY ("user_", "quizId")
);

--
-- Table: tiki_user_tasks
--

DROP TABLE "tiki_user_tasks";
CREATE TABLE "tiki_user_tasks" (
  "user_" character varying(200) DEFAULT NULL,
  "taskId"  bigserial NOT NULL,
  "title" character varying(250) DEFAULT NULL,
  "description" text,
  "date"  bigint DEFAULT NULL,
  "status" char(1) DEFAULT NULL,
  "priority"  smallint DEFAULT NULL,
  "completed"  bigint DEFAULT NULL,
  "percentage"  smallint DEFAULT NULL,
  CONSTRAINT pk_tiki_user_tasks PRIMARY KEY ("taskId")
);

--
-- Table: tiki_user_votings
--

DROP TABLE "tiki_user_votings";
CREATE TABLE "tiki_user_votings" (
  "user_" character varying(200) DEFAULT '' NOT NULL,
  "id" character varying(255) DEFAULT '' NOT NULL,
  CONSTRAINT pk_tiki_user_votings PRIMARY KEY ("user_", "id")
);

--
-- Table: tiki_user_watches
--

DROP TABLE "tiki_user_watches";
CREATE TABLE "tiki_user_watches" (
  "user_" character varying(200) DEFAULT '' NOT NULL,
  "event" character varying(40) DEFAULT '' NOT NULL,
  "object" character varying(200) DEFAULT '' NOT NULL,
  "hash" character varying(32) DEFAULT NULL,
  "title" character varying(250) DEFAULT NULL,
  "type" character varying(200) DEFAULT NULL,
  "url" character varying(250) DEFAULT NULL,
  "email" character varying(200) DEFAULT NULL,
  CONSTRAINT pk_tiki_user_watches PRIMARY KEY ("user_", "event", "object")
);

--
-- Table: tiki_userfiles
--

DROP TABLE "tiki_userfiles";
CREATE TABLE "tiki_userfiles" (
  "user_" character varying(200) DEFAULT '' NOT NULL,
  "fileId"  bigserial NOT NULL,
  "name" character varying(200) DEFAULT NULL,
  "filename" character varying(200) DEFAULT NULL,
  "filetype" character varying(200) DEFAULT NULL,
  "filesize" character varying(200) DEFAULT NULL,
  "data" bytea,
  "hits"  integer DEFAULT NULL,
  "isFile" char(1) DEFAULT NULL,
  "path" character varying(255) DEFAULT NULL,
  "created"  bigint DEFAULT NULL,
  CONSTRAINT pk_tiki_userfiles PRIMARY KEY ("fileId")
);

--
-- Table: tiki_userpoints
--

DROP TABLE "tiki_userpoints";
CREATE TABLE "tiki_userpoints" (
  "user_" character varying(200) DEFAULT NULL,
  "points" decimal(8, 2) DEFAULT NULL,
  "voted"  integer DEFAULT NULL
);

--
-- Table: tiki_users
--

DROP TABLE "tiki_users";
CREATE TABLE "tiki_users" (
  "user_" character varying(200) DEFAULT '' NOT NULL,
  "password" character varying(40) DEFAULT NULL,
  "email" character varying(200) DEFAULT NULL,
  "lastLogin"  bigint DEFAULT NULL,
  CONSTRAINT pk_tiki_users PRIMARY KEY ("user_")
);

--
-- Table: tiki_webmail_contacts
--

DROP TABLE "tiki_webmail_contacts";
CREATE TABLE "tiki_webmail_contacts" (
  "contactId"  bigserial NOT NULL,
  "firstName" character varying(80) DEFAULT NULL,
  "lastName" character varying(80) DEFAULT NULL,
  "email" character varying(250) DEFAULT NULL,
  "nickname" character varying(200) DEFAULT NULL,
  "user_" character varying(200) DEFAULT '' NOT NULL,
  CONSTRAINT pk_tiki_webmail_contacts PRIMARY KEY ("contactId")
);

--
-- Table: tiki_webmail_messages
--

DROP TABLE "tiki_webmail_messages";
CREATE TABLE "tiki_webmail_messages" (
  "accountId"  bigint DEFAULT '0' NOT NULL,
  "mailId" character varying(255) DEFAULT '' NOT NULL,
  "user_" character varying(200) DEFAULT '' NOT NULL,
  "isRead" char(1) DEFAULT NULL,
  "isReplied" char(1) DEFAULT NULL,
  "isFlagged" char(1) DEFAULT NULL,
  CONSTRAINT pk_tiki_webmail_messages PRIMARY KEY ("accountId", "mailId")
);

--
-- Table: tiki_wiki_attachments
--

DROP TABLE "tiki_wiki_attachments";
CREATE TABLE "tiki_wiki_attachments" (
  "attId"  bigserial NOT NULL,
  "page" character varying(200) DEFAULT '' NOT NULL,
  "filename" character varying(80) DEFAULT NULL,
  "filetype" character varying(80) DEFAULT NULL,
  "filesize"  bigint DEFAULT NULL,
  "user_" character varying(200) DEFAULT NULL,
  "data" bytea,
  "path" character varying(255) DEFAULT NULL,
  "downloads"  integer DEFAULT NULL,
  "created"  bigint DEFAULT NULL,
  "comment" character varying(250) DEFAULT NULL,
  CONSTRAINT pk_tiki_wiki_attachments PRIMARY KEY ("attId")
);

--
-- Table: tiki_zones
--

DROP TABLE "tiki_zones";
CREATE TABLE "tiki_zones" (
  "zone" character varying(40) DEFAULT '' NOT NULL,
  CONSTRAINT pk_tiki_zones PRIMARY KEY ("zone")
);

--
-- Table: users_grouppermissions
--

DROP TABLE "users_grouppermissions";
CREATE TABLE "users_grouppermissions" (
  "groupName" character varying(30) DEFAULT '' NOT NULL,
  "permName" character varying(30) DEFAULT '' NOT NULL,
  "value" char(1) DEFAULT '' NOT NULL,
  CONSTRAINT pk_users_grouppermissions PRIMARY KEY ("groupName", "permName")
);

--
-- Table: users_groups
--

DROP TABLE "users_groups";
CREATE TABLE "users_groups" (
  "groupName" character varying(30) DEFAULT '' NOT NULL,
  "groupDesc" character varying(255) DEFAULT NULL,
  CONSTRAINT pk_users_groups PRIMARY KEY ("groupName")
);

--
-- Table: users_objectpermissions
--

DROP TABLE "users_objectpermissions";
CREATE TABLE "users_objectpermissions" (
  "groupName" character varying(30) DEFAULT '' NOT NULL,
  "permName" character varying(30) DEFAULT '' NOT NULL,
  "objectType" character varying(20) DEFAULT '' NOT NULL,
  "objectId" character varying(32) DEFAULT '' NOT NULL,
  CONSTRAINT pk_users_objectpermissions PRIMARY KEY ("objectId", "groupName", "permName")
);

--
-- Table: users_permissions
--

DROP TABLE "users_permissions";
CREATE TABLE "users_permissions" (
  "permName" character varying(30) DEFAULT '' NOT NULL,
  "permDesc" character varying(250) DEFAULT NULL,
  "level" character varying(80) DEFAULT NULL,
  "type" character varying(20) DEFAULT NULL,
  CONSTRAINT pk_users_permissions PRIMARY KEY ("permName")
);

--
-- Table: users_usergroups
--

DROP TABLE "users_usergroups";
CREATE TABLE "users_usergroups" (
  "userId"  integer DEFAULT '0' NOT NULL,
  "groupName" character varying(30) DEFAULT '' NOT NULL,
  CONSTRAINT pk_users_usergroups PRIMARY KEY ("userId", "groupName")
);

--
-- Table: users_users
--

DROP TABLE "users_users";
CREATE TABLE "users_users" (
  "userId"  serial NOT NULL,
  "email" character varying(200) DEFAULT NULL,
  "login" character varying(40) DEFAULT '' NOT NULL,
  "password" character varying(30) DEFAULT '' NOT NULL,
  "provpass" character varying(30) DEFAULT NULL,
  "realname" character varying(80) DEFAULT NULL,
  "homePage" character varying(200) DEFAULT NULL,
  "lastLogin"  bigint DEFAULT NULL,
  "currentLogin"  bigint DEFAULT NULL,
  "registrationDate"  bigint DEFAULT NULL,
  "challenge" character varying(32) DEFAULT NULL,
  "pass_due"  bigint DEFAULT NULL,
  "hash" character varying(32) DEFAULT NULL,
  "created"  bigint DEFAULT NULL,
  "country" character varying(80) DEFAULT NULL,
  "avatarName" character varying(80) DEFAULT NULL,
  "avatarSize"  bigint DEFAULT NULL,
  "avatarFileType" character varying(250) DEFAULT NULL,
  "avatarData" bytea,
  "avatarLibName" character varying(200) DEFAULT NULL,
  "avatarType" char(1) DEFAULT NULL,
  CONSTRAINT pk_users_users PRIMARY KEY ("userId")
);

