--$Id: tiki_pgsql.sql,v 1.5 2003-10-13 23:03:16 ohertel Exp $
-- Dump of tiki_mysql.sql










CREATE TABLE galaxia_activities (
"activityId" integer NOT NULL default nextval('galaxia_activities_seq') unique not null,
"name" varchar(80) NOT NULL default '',
"normalized_name" varchar(80) NOT NULL default '',
"pId" integer NOT NULL default '0',
"type" varchar(10) check ("type" in ('start','end','split','switch','join','activity','standalone')) NOT NULL default 'start',
"isAutoRouted" varchar(1) NOT NULL default '',
"flowNum" integer NOT NULL default '0',
"isInteractive" varchar(1) NOT NULL default '',
"lastModif" integer NOT NULL default '0',
"description" text NOT NULL,
PRIMARY KEY ("activityId")
) ;











CREATE TABLE galaxia_activity_roles (
"activityId" integer NOT NULL default '0',
"roleId" integer NOT NULL default '0',
PRIMARY KEY ("roleId","activityId")
) ;











CREATE TABLE galaxia_instance_activities (
"instanceId" integer NOT NULL default '0',
"activityId" integer NOT NULL default '0',
"started" integer NOT NULL default '0',
"ended" integer NOT NULL default '0',
"user" varchar(200) NOT NULL default '',
"status" varchar(9) check ("status" in ('running','completed')) NOT NULL default 'running',
PRIMARY KEY ("instanceId","activityId")
) ;











CREATE TABLE galaxia_instance_comments (
"cId" integer NOT NULL default nextval('galaxia_instance_comments_seq') unique not null,
"instanceId" integer NOT NULL default '0',
"user" varchar(200) NOT NULL default '',
"activityId" integer NOT NULL default '0',
"hash" varchar(32) NOT NULL default '',
"title" varchar(250) NOT NULL default '',
"comment" text NOT NULL,
"activity" varchar(80) NOT NULL default '',
"timestamp" integer NOT NULL default '0',
PRIMARY KEY ("cId")
) ;











CREATE TABLE galaxia_instances (
"instanceId" integer NOT NULL default nextval('galaxia_instances_seq') unique not null,
"pId" integer NOT NULL default '0',
"started" integer NOT NULL default '0',
"owner" varchar(200) NOT NULL default '',
"nextActivity" integer NOT NULL default '0',
"nextUser" varchar(200) NOT NULL default '',
"ended" integer NOT NULL default '0',
"status" varchar(9) check ("status" in ('active','exception','aborted','completed')) NOT NULL default 'active',
"properties" text,
PRIMARY KEY ("instanceId")
) ;











CREATE TABLE galaxia_processes (
"pId" integer NOT NULL default nextval('galaxia_processes_seq') unique not null,
"name" varchar(80) NOT NULL default '',
"isValid" varchar(1) NOT NULL default '',
"isActive" varchar(1) NOT NULL default '',
"version" varchar(12) NOT NULL default '',
"description" text NOT NULL,
"lastModif" integer NOT NULL default '0',
"normalized_name" varchar(80) NOT NULL default '',
PRIMARY KEY ("pId")
) ;











CREATE TABLE galaxia_roles (
"roleId" integer NOT NULL default nextval('galaxia_roles_seq') unique not null,
"pId" integer NOT NULL default '0',
"lastModif" integer NOT NULL default '0',
"name" varchar(80) NOT NULL default '',
"description" text NOT NULL,
PRIMARY KEY ("roleId")
) ;











CREATE TABLE galaxia_transitions (
"pId" integer NOT NULL default '0',
"actFromId" integer NOT NULL default '0',
"actToId" integer NOT NULL default '0',
PRIMARY KEY ("actToId","actFromId")
) ;











CREATE TABLE galaxia_user_roles (
"pId" integer NOT NULL default '0',
"roleId" integer NOT NULL default nextval('galaxia_user_roles_seq') unique not null,
"user" varchar(200) NOT NULL default '',
PRIMARY KEY ("user","roleId")
) ;











CREATE TABLE galaxia_workitems (
"itemId" integer NOT NULL default nextval('galaxia_workitems_seq') unique not null,
"instanceId" integer NOT NULL default '0',
"orderId" integer NOT NULL default '0',
"activityId" integer NOT NULL default '0',
"properties" text,
"started" integer NOT NULL default '0',
"ended" integer NOT NULL default '0',
"user" varchar(200) NOT NULL default '',
PRIMARY KEY ("itemId")
) ;











CREATE TABLE messu_messages (
"msgId" integer NOT NULL default nextval('messu_messages_seq') unique not null,
"user" varchar(200) NOT NULL default '',
"user_from" varchar(200) NOT NULL default '',
"user_to" text NOT NULL,
"user_cc" text NOT NULL,
"user_bcc" text NOT NULL,
"subject" varchar(255) NOT NULL default '',
"body" text NOT NULL,
"hash" varchar(32) NOT NULL default '',
"datetime" integer NOT NULL default '0',
"isRead" varchar(1) NOT NULL default '',
"isReplied" varchar(1) NOT NULL default '',
"isFlagged" varchar(1) NOT NULL default '',
"priority" integer NOT NULL default '0',
PRIMARY KEY ("msgId")
) ;











CREATE TABLE tiki_actionlog (
"action" varchar(255) NOT NULL default '',
"lastModif" integer NOT NULL default '0',
"pageName" varchar(160) NOT NULL default '',
"user" varchar(200) NOT NULL default '',
"ip" varchar(15) NOT NULL default '',
"comment" varchar(200) NOT NULL default ''
) ;






INSERT INTO tiki_actionlog VALUES ('Created',1038712078,'HomePage','system','0.0.0.0','Tiki initialization');
INSERT INTO tiki_actionlog VALUES ('Updated',1038793725,'HomePage','ross','192.168.1.2','');
INSERT INTO tiki_actionlog VALUES ('Updated',1038793754,'HomePage','ross','192.168.1.2','');
INSERT INTO tiki_actionlog VALUES ('Updated',1038794026,'HomePage','ross','192.168.1.2','');
INSERT INTO tiki_actionlog VALUES ('Updated',1038794132,'HomePage','ross','192.168.1.2','');
INSERT INTO tiki_actionlog VALUES ('Created',1038794163,'NoHTMLCodeIsNeeded','ross','192.168.1.2','');
INSERT INTO tiki_actionlog VALUES ('Created',1038794197,'AWordWithCapitals','ross','192.168.1.2','');
INSERT INTO tiki_actionlog VALUES ('Updated',1038940707,'HomePage','ross','192.168.1.2','');
INSERT INTO tiki_actionlog VALUES ('Created',1038940749,'LisasPage','ross','192.168.1.2','');
INSERT INTO tiki_actionlog VALUES ('Updated',1038971383,'HomePage','ross','192.168.1.2','');
INSERT INTO tiki_actionlog VALUES ('Created',1038971411,'JobHunting','ross','192.168.1.2','');
INSERT INTO tiki_actionlog VALUES ('Updated',1038974004,'HomePage','ross','192.168.1.2','');
INSERT INTO tiki_actionlog VALUES ('Updated',1039232946,'HomePage','ross','192.168.1.2','');
INSERT INTO tiki_actionlog VALUES ('Updated',1039233364,'HomePage','ross','192.168.1.2','');
INSERT INTO tiki_actionlog VALUES ('Updated',1039234052,'HomePage','ross','192.168.1.2','');
INSERT INTO tiki_actionlog VALUES ('Updated',1039617016,'HomePage','ross','192.168.1.2','');
INSERT INTO tiki_actionlog VALUES ('Updated',1039617624,'HomePage','ross','192.168.1.2','');
INSERT INTO tiki_actionlog VALUES ('Updated',1039617646,'HomePage','ross','192.168.1.2','');
INSERT INTO tiki_actionlog VALUES ('Updated',1039617668,'HomePage','ross','192.168.1.2','');
INSERT INTO tiki_actionlog VALUES ('Updated',1039618839,'HomePage','ross','192.168.1.2','');
INSERT INTO tiki_actionlog VALUES ('Updated',1039739934,'HomePage','ross','192.168.1.2','');
INSERT INTO tiki_actionlog VALUES ('Updated',1039748495,'HomePage','ross','192.168.1.2','');
INSERT INTO tiki_actionlog VALUES ('Updated',1039768550,'HomePage','ross','192.168.1.2','');
INSERT INTO tiki_actionlog VALUES ('Updated',1039947392,'HomePage','ross','192.168.1.2','');
INSERT INTO tiki_actionlog VALUES ('Updated',1039947473,'JobHunting','ross','192.168.1.2','');
INSERT INTO tiki_actionlog VALUES ('Updated',1040866181,'HomePage','ross','192.168.1.2','');
INSERT INTO tiki_actionlog VALUES ('Updated',1040866201,'HomePage','ross','192.168.1.2','');
INSERT INTO tiki_actionlog VALUES ('Created',1040866218,'NewPage','ross','192.168.1.2','');
INSERT INTO tiki_actionlog VALUES ('Created',1040866230,'NextPage','ross','192.168.1.2','');
INSERT INTO tiki_actionlog VALUES ('Updated',1041569358,'HomePage','luis','192.168.1.2','');
INSERT INTO tiki_actionlog VALUES ('Updated',1041800610,'HomePage','ross','192.168.1.2','');
INSERT INTO tiki_actionlog VALUES ('Updated',1041822095,'HomePage','ross','192.168.1.2','');
INSERT INTO tiki_actionlog VALUES ('Updated',1042143529,'HomePage','ross','192.168.1.2','');
INSERT INTO tiki_actionlog VALUES ('Updated',1042143584,'HomePage','ross','192.168.1.2','');
INSERT INTO tiki_actionlog VALUES ('Updated',1044981129,'HomePage','ross','192.168.1.2','');
INSERT INTO tiki_actionlog VALUES ('Updated',1045204033,'HomePage','ross','192.168.1.2','');
INSERT INTO tiki_actionlog VALUES ('Updated',1045204274,'HomePage','ross','192.168.1.2','');
INSERT INTO tiki_actionlog VALUES ('Updated',1045231844,'HomePage','ross','192.168.1.2','');
INSERT INTO tiki_actionlog VALUES ('created tag: test',1057791054,'HomePage','admin','10.0.0.3','');





CREATE TABLE tiki_articles (
"articleId" integer NOT NULL default nextval('tiki_articles_seq') unique not null,
"title" varchar(80) NOT NULL default '',
"authorName" varchar(60) NOT NULL default '',
"topicId" integer NOT NULL default '0',
"topicName" varchar(40) NOT NULL default '',
"size" integer NOT NULL default '0',
"useImage" varchar(1) NOT NULL default '',
"image_name" varchar(80) NOT NULL default '',
"image_type" varchar(80) NOT NULL default '',
"image_size" integer NOT NULL default '0',
"image_x" integer NOT NULL default '0',
"image_y" integer NOT NULL default '0',
"image_data" text,
"publishDate" integer NOT NULL default '0',
"created" integer NOT NULL default '0',
"heading" text NOT NULL,
"body" text NOT NULL,
"hash" varchar(32) NOT NULL default '',
"author" varchar(200) NOT NULL default '',
"reads" integer NOT NULL default '0',
"votes" integer NOT NULL default '0',
"points" integer NOT NULL default '0',
"type" varchar(50) NOT NULL default '',
"rating" decimal(4,2) NOT NULL default '0.00',
"isfloat" varchar(1) NOT NULL default '',
PRIMARY KEY ("articleId")





) ;






INSERT INTO tiki_articles VALUES (1,'tesw','test',1,'A Test Topic',10,'n','','',0,0,0,'',1045076460,1045076538,'test','07:01 nzst','02193ee83ac93ef5d4a439b802d72034','ross',4,0,0,'Article',7.00,'n');





CREATE TABLE tiki_banners (
"bannerId" integer NOT NULL default nextval('tiki_banners_seq') unique not null,
"client" varchar(200) NOT NULL default '',
"url" varchar(255) NOT NULL default '',
"title" varchar(255) NOT NULL default '',
"alt" varchar(250) NOT NULL default '',
"which" varchar(50) NOT NULL default '',
"imageData" text,
"imageType" varchar(200) NOT NULL default '',
"imageName" varchar(100) NOT NULL default '',
"HTMLData" text NOT NULL,
"fixedURLData" varchar(255) NOT NULL default '',
"textData" text NOT NULL,
"fromDate" integer NOT NULL default '0',
"toDate" integer NOT NULL default '0',
"useDates" varchar(1) NOT NULL default '',
"mon" varchar(1) NOT NULL default '',
"tue" varchar(1) NOT NULL default '',
"wed" varchar(1) NOT NULL default '',
"thu" varchar(1) NOT NULL default '',
"fri" varchar(1) NOT NULL default '',
"sat" varchar(1) NOT NULL default '',
"sun" varchar(1) NOT NULL default '',
"hourFrom" varchar(4) NOT NULL default '',
"hourTo" varchar(4) NOT NULL default '',
"created" integer NOT NULL default '0',
"maxImpressions" integer NOT NULL default '0',
"impressions" integer NOT NULL default '0',
"clicks" integer NOT NULL default '0',
"zone" varchar(40) NOT NULL default '',
PRIMARY KEY ("bannerId")
) ;











CREATE TABLE tiki_banning (
"banId" integer NOT NULL default nextval('tiki_banning_seq') unique not null,
"mode" varchar(4) check ("mode" in ('user','ip')) NOT NULL default 'user',
"title" varchar(200) NOT NULL default '',
"ip1" varchar(3) NOT NULL default '',
"ip2" varchar(3) NOT NULL default '',
"ip3" varchar(3) NOT NULL default '',
"ip4" varchar(3) NOT NULL default '',
"user" varchar(200) NOT NULL default '',
"date_from" datetime not null default now() NOT NULL,
"date_to" datetime not null default now() NOT NULL,
"use_dates" varchar(1) NOT NULL default '',
"created" integer NOT NULL default '0',
"message" text NOT NULL,
PRIMARY KEY ("banId")
) ;











CREATE TABLE tiki_banning_sections (
"banId" integer NOT NULL default '0',
"section" varchar(100) NOT NULL default '',
PRIMARY KEY ("section","banId")
) ;











CREATE TABLE tiki_blog_activity (
"blogId" integer NOT NULL default '0',
"day" integer NOT NULL default '0',
"posts" integer NOT NULL default '0',
PRIMARY KEY ("day","blogId")
) ;






INSERT INTO tiki_blog_activity VALUES (1,1045209600,1);





CREATE TABLE tiki_blog_posts (
"postId" integer NOT NULL default nextval('tiki_blog_posts_seq') unique not null,
"blogId" integer NOT NULL default '0',
"data" text NOT NULL,
"created" integer NOT NULL default '0',
"user" varchar(200) NOT NULL default '',
"trackbacks_to" text NOT NULL,
"trackbacks_from" text NOT NULL,
"title" varchar(80) NOT NULL default '',
PRIMARY KEY ("postId")




) ;






INSERT INTO tiki_blog_posts VALUES (1,1,'test',1038714935,'ross','','','');
INSERT INTO tiki_blog_posts VALUES (2,1,'[http://www.nu2.nu/contact/bart/|Bart Lagerweij] published [http://www.nu2.nu/scr2htm/|scr2htm] and gave me credit for authoring the new version!\r\n',1040400064,'ross','','','');
INSERT INTO tiki_blog_posts VALUES (3,1,'Adding an entry to test the full text search function.\r\n\r\nHere are some search terms:\r\n\r\nJobHunting\r\ntest\r\ntiki\r\n',1040866143,'ross','','','');
INSERT INTO tiki_blog_posts VALUES (4,1,'jobtesting job testing test',1045237538,'ross','','','');





CREATE TABLE tiki_blog_posts_images (
"imgId" integer NOT NULL default nextval('tiki_blog_posts_images_seq') unique not null,
"postId" integer NOT NULL default '0',
"filename" varchar(80) NOT NULL default '',
"filetype" varchar(80) NOT NULL default '',
"filesize" integer NOT NULL default '0',
"data" text,
PRIMARY KEY ("imgId")
) ;











CREATE TABLE tiki_blogs (
"blogId" integer NOT NULL default nextval('tiki_blogs_seq') unique not null,
"created" integer NOT NULL default '0',
"lastModif" integer NOT NULL default '0',
"title" varchar(200) NOT NULL default '',
"description" text NOT NULL,
"user" varchar(200) NOT NULL default '',
"public" varchar(1) NOT NULL default '',
"posts" integer NOT NULL default '0',
"maxPosts" integer NOT NULL default '0',
"hits" integer NOT NULL default '0',
"activity" decimal(4,2) NOT NULL default '0.00',
"heading" text NOT NULL,
"use_find" varchar(1) NOT NULL default '',
"use_title" varchar(1) NOT NULL default '',
"add_date" varchar(1) NOT NULL default '',
"add_poster" varchar(1) NOT NULL default '',
"allow_comments" varchar(1) NOT NULL default '',
PRIMARY KEY ("blogId")




) ;






INSERT INTO tiki_blogs VALUES (1,1038714515,1045237538,'Stemming the tide...','A weblog that attempts to stem the growing tide of blogosity on the net.','ross','n',4,50,63,2.00,'','','','','','');
INSERT INTO tiki_blogs VALUES (2,1044982448,1044982448,'test','posted at 08:52 PST (16:53 GMT)','ross','n',0,10,2,0.00,'','','','','','');





CREATE TABLE tiki_calendar_categories (
"calcatId" integer NOT NULL default nextval('tiki_calendar_categories_seq') unique not null,
"calendarId" integer NOT NULL default '0',
"name" varchar(255) NOT NULL default '',
PRIMARY KEY ("calcatId")
,UNIQUE ("calendarId","name")
) ;











CREATE TABLE tiki_calendar_items (
"calitemId" integer NOT NULL default nextval('tiki_calendar_items_seq') unique not null,
"calendarId" integer NOT NULL default '0',
"start" integer NOT NULL default '0',
"end" integer NOT NULL default '0',
"locationId" integer NOT NULL default '0',
"categoryId" integer NOT NULL default '0',
"priority" varchar(1) check ("priority" in ('1','2','3','4','5','6','7','8','9')) NOT NULL default '1',
"status" varchar(1) check ("status" in ('0','1','2')) NOT NULL default '0',
"url" varchar(255) NOT NULL default '',
"lang" varchar(2) NOT NULL default 'en',
"name" varchar(255) NOT NULL default '',
"description" text,
"user" varchar(40) NOT NULL default '',
"created" integer NOT NULL default '0',
"lastmodif" integer NOT NULL default '0',
PRIMARY KEY ("calitemId")

) ;











CREATE TABLE tiki_calendar_locations (
"callocId" integer NOT NULL default nextval('tiki_calendar_locations_seq') unique not null,
"calendarId" integer NOT NULL default '0',
"name" varchar(255) NOT NULL default '',
"description" text,
PRIMARY KEY ("callocId")
,UNIQUE ("calendarId","name")
) ;











CREATE TABLE tiki_calendar_roles (
"calitemId" integer NOT NULL default '0',
"username" varchar(40) NOT NULL default '',
"role" varchar(1) check ("role" in ('0','1','2','3','6')) NOT NULL default '0',
PRIMARY KEY ("username","calitemId","role")
) ;











CREATE TABLE tiki_calendars (
"calendarId" integer NOT NULL default nextval('tiki_calendars_seq') unique not null,
"name" varchar(80) NOT NULL default '',
"description" varchar(255) NOT NULL default '',
"user" varchar(40) NOT NULL default '',
"customlocations" varchar(1) check ("customlocations" in ('n','y')) NOT NULL default 'n',
"customcategories" varchar(1) check ("customcategories" in ('n','y')) NOT NULL default 'n',
"customlanguages" varchar(1) check ("customlanguages" in ('n','y')) NOT NULL default 'n',
"custompriorities" varchar(1) check ("custompriorities" in ('n','y')) NOT NULL default 'n',
"customparticipants" varchar(1) check ("customparticipants" in ('n','y')) NOT NULL default 'n',
"created" integer NOT NULL default '0',
"lastmodif" integer NOT NULL default '0',
PRIMARY KEY ("calendarId")
) ;











CREATE TABLE tiki_categories (
"categId" integer NOT NULL default nextval('tiki_categories_seq') unique not null,
"name" varchar(100) NOT NULL default '',
"description" varchar(250) NOT NULL default '',
"parentId" integer NOT NULL default '0',
"hits" integer NOT NULL default '0',
PRIMARY KEY ("categId")
) ;






INSERT INTO tiki_categories VALUES (1,'test','test',0,0);





CREATE TABLE tiki_categorized_objects (
"catObjectId" integer NOT NULL default nextval('tiki_categorized_objects_seq') unique not null,
"type" varchar(50) NOT NULL default '',
"objId" varchar(255) NOT NULL default '',
"description" text NOT NULL,
"created" integer NOT NULL default '0',
"name" varchar(200) NOT NULL default '',
"href" varchar(200) NOT NULL default '',
"hits" integer NOT NULL default '0',
PRIMARY KEY ("catObjectId")
) ;











CREATE TABLE tiki_category_objects (
"catObjectId" integer NOT NULL default '0',
"categId" integer NOT NULL default '0',
PRIMARY KEY ("catObjectId","categId")
) ;











CREATE TABLE tiki_category_sites (
"categId" integer NOT NULL default '0',
"siteId" integer NOT NULL default '0',
PRIMARY KEY ("categId","siteId")
) ;






INSERT INTO tiki_category_sites VALUES (1,1);





CREATE TABLE tiki_chart_items (
"itemId" integer NOT NULL default nextval('tiki_chart_items_seq') unique not null,
"title" varchar(250) NOT NULL default '',
"description" text NOT NULL,
"chartId" integer NOT NULL default '0',
"created" integer NOT NULL default '0',
"URL" varchar(250) NOT NULL default '',
"votes" integer NOT NULL default '0',
"points" integer NOT NULL default '0',
"average" decimal(4,2) NOT NULL default '0.00',
PRIMARY KEY ("itemId")
) ;











CREATE TABLE tiki_charts (
"chartId" integer NOT NULL default nextval('tiki_charts_seq') unique not null,
"title" varchar(250) NOT NULL default '',
"description" text NOT NULL,
"hits" integer NOT NULL default '0',
"singleItemVotes" varchar(1) NOT NULL default '',
"singleChartVotes" varchar(1) NOT NULL default '',
"suggestions" varchar(1) NOT NULL default '',
"autoValidate" varchar(1) NOT NULL default '',
"topN" integer NOT NULL default '0',
"maxVoteValue" integer NOT NULL default '0',
"frequency" integer NOT NULL default '0',
"showAverage" varchar(1) NOT NULL default '',
"isActive" varchar(1) NOT NULL default '',
"showVotes" varchar(1) NOT NULL default '',
"useCookies" varchar(1) NOT NULL default '',
"lastChart" integer NOT NULL default '0',
"voteAgainAfter" integer NOT NULL default '0',
"created" integer NOT NULL default '0',
"hist" integer NOT NULL default '0',
PRIMARY KEY ("chartId")
) ;











CREATE TABLE tiki_charts_rankings (
"chartId" integer NOT NULL default '0',
"itemId" integer NOT NULL default '0',
"position" integer NOT NULL default '0',
"timestamp" integer NOT NULL default '0',
"lastPosition" integer NOT NULL default '0',
"period" integer NOT NULL default '0',
"rvotes" integer NOT NULL default '0',
"raverage" decimal(4,2) NOT NULL default '0.00',
PRIMARY KEY ("chartId","period","itemId")
) ;











CREATE TABLE tiki_charts_votes (
"user" varchar(200) NOT NULL default '',
"itemId" integer NOT NULL default '0',
"timestamp" integer NOT NULL default '0',
"chartId" integer NOT NULL default '0',
PRIMARY KEY ("itemId","user")
) ;











CREATE TABLE tiki_chat_channels (
"channelId" integer NOT NULL default nextval('tiki_chat_channels_seq') unique not null,
"name" varchar(30) NOT NULL default '',
"description" varchar(250) NOT NULL default '',
"max_users" integer NOT NULL default '0',
"mode" varchar(1) NOT NULL default '',
"moderator" varchar(200) NOT NULL default '',
"active" varchar(1) NOT NULL default '',
"refresh" integer NOT NULL default '0',
PRIMARY KEY ("channelId")
) ;






INSERT INTO tiki_chat_channels VALUES (1,'Main','Main Chat Channel',0,'n','','y',3000);





CREATE TABLE tiki_chat_messages (
"messageId" integer NOT NULL default nextval('tiki_chat_messages_seq') unique not null,
"channelId" integer NOT NULL default '0',
"data" varchar(255) NOT NULL default '',
"poster" varchar(200) NOT NULL default 'anonymous',
"timestamp" integer NOT NULL default '0',
PRIMARY KEY ("messageId")
) ;






INSERT INTO tiki_chat_messages VALUES (1,1,'test','@ross',1045321261);





CREATE TABLE tiki_chat_users (
"nickname" varchar(200) NOT NULL default '',
"channelId" integer NOT NULL default '0',
"timestamp" integer NOT NULL default '0',
PRIMARY KEY ("nickname","channelId")
) ;






INSERT INTO tiki_chat_users VALUES ('@ross',1,1045321261);





CREATE TABLE tiki_comments (
"threadId" integer NOT NULL default nextval('tiki_comments_seq') unique not null,
"object" varchar(32) NOT NULL default '',
"parentId" integer NOT NULL default '0',
"userName" varchar(200) NOT NULL default '',
"commentDate" integer NOT NULL default '0',
"hits" integer NOT NULL default '0',
"type" varchar(1) NOT NULL default '',
"points" decimal(8,2) NOT NULL default '0.00',
"votes" integer NOT NULL default '0',
"average" decimal(8,4) NOT NULL default '0.0000',
"title" varchar(100) NOT NULL default '',
"data" text NOT NULL,
"hash" varchar(32) NOT NULL default '',
"summary" varchar(240) NOT NULL default '',
"smiley" varchar(80) NOT NULL default '',
"user_ip" varchar(15) NOT NULL default '',
  "message_id" varchar(250) default NULL,
  "in_reply_to" varchar(250) default NULL,
PRIMARY KEY ("threadId")





) ;






INSERT INTO tiki_comments VALUES (1,'d25ae3a84533b6c29bd5091a3f85e5ab',0,'ross',1040866317,6,'n',0.00,0,0.0000,'test','Adding an entry to test the full text search function.\r<br />\n\r<br />\n\r<br />\nEdited at 8:20am PST\r<br />\n\r<br />\n\r<br />\nHere are some search terms:\r<br />\n\r<br />\n\r<br />\n\r<br />\nJobHunting\r<br />\n\r<br />\ntest\r<br />\n\r<br />\ntiki\r<br />\n\r<br />\n','7abe3cfdef89650bb9c6b6ffc6e225a9','','','127.0.0.1');
INSERT INTO tiki_comments VALUES (2,'d25ae3a84533b6c29bd5091a3f85e5ab',1,'ross',1040866338,0,'n',0.00,0,0.0000,'test2','Adding an entry to test the full text search function.\r<br />\n\r<br />\nHere are some search terms:\r<br />\n\r<br />\nJobHunting\r<br />\ntest\r<br />\ntiki\r<br />\n','dd0f295766976b56ce191f1460326f49','','','127.0.0.1');
INSERT INTO tiki_comments VALUES (4,'6d7f618fdb6590000b06a03865b40191',0,'ross',1041800576,0,'n',0.00,0,0.0000,'test2','test3','edba96cd2acd74c87f7f41f60ccf93c8','','','127.0.0.1');
INSERT INTO tiki_comments VALUES (5,'d25ae3a84533b6c29bd5091a3f85e5ab',1,'ross',1044980617,0,'n',0.00,0,0.0000,'Posted at 8:21am PST','Posted at 8:21am PST','7034e40ba20b87a3222213184bbd1a2a','','','127.0.0.1');
INSERT INTO tiki_comments VALUES (6,'d25ae3a84533b6c29bd5091a3f85e5ab',0,'ross',1044982298,1,'n',0.00,0,0.0000,'test','test','05a671c66aefea124cc08b76ea6d30bb','','','127.0.0.1');





CREATE TABLE tiki_content (
"contentId" integer NOT NULL default nextval('tiki_content_seq') unique not null,
"description" text NOT NULL,
PRIMARY KEY ("contentId")
) ;











CREATE TABLE tiki_content_templates (
"templateId" integer NOT NULL default nextval('tiki_content_templates_seq') unique not null,
"content" text,
"name" varchar(200) NOT NULL default '',
"created" integer NOT NULL default '0',
PRIMARY KEY ("templateId")
) ;











CREATE TABLE tiki_content_templates_sections (
"templateId" integer NOT NULL default '0',
"section" varchar(250) NOT NULL default '',
PRIMARY KEY ("section","templateId")
) ;











CREATE TABLE tiki_cookies (
"cookieId" integer NOT NULL default nextval('tiki_cookies_seq') unique not null,
"cookie" varchar(255) NOT NULL default '',
PRIMARY KEY ("cookieId")
) ;











CREATE TABLE tiki_copyrights (
"copyrightId" integer NOT NULL default nextval('tiki_copyrights_seq') unique not null,
"page" varchar(200) NOT NULL default '',
"title" varchar(200) NOT NULL default '',
"year" integer NOT NULL default '0',
"authors" varchar(200) NOT NULL default '',
"copyright_order" integer NOT NULL default '0',
"userName" varchar(200) NOT NULL default '',
PRIMARY KEY ("copyrightId")
) ;











CREATE TABLE tiki_directory_categories (
"categId" integer NOT NULL default nextval('tiki_directory_categories_seq') unique not null,
"parent" integer NOT NULL default '0',
"name" varchar(240) NOT NULL default '',
"description" text NOT NULL,
"childrenType" varchar(1) NOT NULL default '',
"sites" integer NOT NULL default '0',
"viewableChildren" integer NOT NULL default '0',
"allowSites" varchar(1) NOT NULL default '',
"showCount" varchar(1) NOT NULL default '',
"editorGroup" varchar(200) NOT NULL default '',
"hits" integer NOT NULL default '0',
PRIMARY KEY ("categId")
) ;






INSERT INTO tiki_directory_categories VALUES (1,0,'test','test','c',0,3,'y','y','',1);





CREATE TABLE tiki_directory_search (
"term" varchar(250) NOT NULL default '',
"hits" integer NOT NULL default '0',
PRIMARY KEY ("term")
) ;











CREATE TABLE tiki_directory_sites (
"siteId" integer NOT NULL default nextval('tiki_directory_sites_seq') unique not null,
"name" varchar(240) NOT NULL default '',
"description" text NOT NULL,
"url" varchar(255) NOT NULL default '',
"country" varchar(255) NOT NULL default '',
"hits" integer NOT NULL default '0',
"isValid" varchar(1) NOT NULL default '',
"created" integer NOT NULL default '0',
"lastModif" integer NOT NULL default '0',
"cache" text,
"cache_timestamp" integer NOT NULL default '0',
PRIMARY KEY ("siteId")

) ;






INSERT INTO tiki_directory_sites VALUES (1,'test','smithii','http://smithii.com/','United_States',0,'y',1051808623,1051808623,NULL,0);





CREATE TABLE tiki_drawings (
"drawId" integer NOT NULL default nextval('tiki_drawings_seq') unique not null,
"version" integer NOT NULL default '0',
"name" varchar(250) NOT NULL default '',
"filename_draw" varchar(250) NOT NULL default '',
"filename_pad" varchar(250) NOT NULL default '',
"timestamp" integer NOT NULL default '0',
"user" varchar(200) NOT NULL default '',
PRIMARY KEY ("drawId")
) ;











CREATE TABLE tiki_dsn (
"dsnId" integer NOT NULL default nextval('tiki_dsn_seq') unique not null,
"name" varchar(20) NOT NULL default '',
"dsn" varchar(255) NOT NULL default '',
PRIMARY KEY ("dsnId")
) ;











CREATE TABLE tiki_eph (
"ephId" integer NOT NULL default nextval('tiki_eph_seq') unique not null,
"title" varchar(250) NOT NULL default '',
"isFile" varchar(1) NOT NULL default '',
"filename" varchar(250) NOT NULL default '',
"filetype" varchar(250) NOT NULL default '',
"filesize" varchar(250) NOT NULL default '',
"data" text,
"textdata" text,
"publish" integer NOT NULL default '0',
"hits" integer NOT NULL default '0',
PRIMARY KEY ("ephId")
) ;






INSERT INTO tiki_eph VALUES (1,'test','','','','0','','test',1051858799,0);





CREATE TABLE tiki_extwiki (
"extwikiId" integer NOT NULL default nextval('tiki_extwiki_seq') unique not null,
"name" varchar(20) NOT NULL default '',
"extwiki" varchar(255) NOT NULL default '',
PRIMARY KEY ("extwikiId")
) ;











CREATE TABLE tiki_faq_questions (
"questionId" integer NOT NULL default nextval('tiki_faq_questions_seq') unique not null,
"faqId" integer NOT NULL default '0',
"position" integer NOT NULL default '0',
"question" text NOT NULL,
"answer" text NOT NULL,
PRIMARY KEY ("questionId")




) ;






INSERT INTO tiki_faq_questions VALUES (1,1,0,'A Question','An answer');
INSERT INTO tiki_faq_questions VALUES (2,1,0,'Another question','');
INSERT INTO tiki_faq_questions VALUES (3,2,0,'Gotta have a test question','Adding an entry to test the full text search function.\r\n\r\nHere are some search terms:\r\n\r\nJobHunting\r\ntest\r\ntiki\r\n');
INSERT INTO tiki_faq_questions VALUES (4,2,0,'Another test question','Adding an entry to test the full text search function.\r\n\r\nHere are some search terms:\r\n\r\nJobHunting\r\ntest\r\ntiki\r\n');





CREATE TABLE tiki_faqs (
"faqId" integer NOT NULL default nextval('tiki_faqs_seq') unique not null,
"title" varchar(200) NOT NULL default '',
"description" text NOT NULL,
"created" integer NOT NULL default '0',
"questions" integer NOT NULL default '0',
"hits" integer NOT NULL default '0',
"canSuggest" varchar(1) NOT NULL default '',
PRIMARY KEY ("faqId")





) ;






INSERT INTO tiki_faqs VALUES (1,'A Test FAQ','A Test FAQ',1040550065,2,3,'y');
INSERT INTO tiki_faqs VALUES (2,'test','Adding an entry to test the full text search function.\r\n\r\nHere are some search terms:\r\n\r\nJobHunting\r\ntest\r\ntiki\r\n',1040866259,2,4,'n');





CREATE TABLE tiki_featured_links (
"url" varchar(200) NOT NULL default '',
"title" varchar(40) NOT NULL default '',
"description" text NOT NULL,
"hits" integer NOT NULL default '0',
"position" integer NOT NULL default '0',
"type" varchar(1) NOT NULL default '',
PRIMARY KEY ("url")
) ;











CREATE TABLE tiki_file_galleries (
"galleryId" integer NOT NULL default nextval('tiki_file_galleries_seq') unique not null,
"name" varchar(80) NOT NULL default '',
"description" text NOT NULL,
"created" integer NOT NULL default '0',
"visible" varchar(1) NOT NULL default '',
"lastModif" integer NOT NULL default '0',
"user" varchar(200) NOT NULL default '',
"hits" integer NOT NULL default '0',
"votes" integer NOT NULL default '0',
"points" decimal(8,2) NOT NULL default '0.00',
"maxRows" integer NOT NULL default '0',
"public" varchar(1) NOT NULL default '',
"show_id" varchar(1) NOT NULL default '',
"show_icon" varchar(1) NOT NULL default '',
"show_name" varchar(1) NOT NULL default '',
"show_size" varchar(1) NOT NULL default '',
"show_description" varchar(1) NOT NULL default '',
"max_desc" integer NOT NULL default '0',
"show_created" varchar(1) NOT NULL default '',
"show_dl" varchar(1) NOT NULL default '',
PRIMARY KEY ("galleryId")
) ;






INSERT INTO tiki_file_galleries VALUES (1,'Test File Gallery','Test File Gallery',1038717160,'y',1040868439,'ross',1,0,0.00,10,'n','y','y','a','y','y',1024,'y','y');





CREATE TABLE tiki_files (
"fileId" integer NOT NULL default nextval('tiki_files_seq') unique not null,
"galleryId" integer NOT NULL default '0',
"name" varchar(40) NOT NULL default '',
"description" text NOT NULL,
"created" integer NOT NULL default '0',
"filename" varchar(80) NOT NULL default '',
"filesize" integer NOT NULL default '0',
"filetype" varchar(250) NOT NULL default '',
"data" text,
"user" varchar(200) NOT NULL default '',
"downloads" integer NOT NULL default '0',
"votes" integer NOT NULL default '0',
"points" decimal(8,2) NOT NULL default '0.00',
"path" varchar(255) NOT NULL default '',
"hash" varchar(32) NOT NULL default '',
"reference_url" varchar(250) NOT NULL default '',
"is_reference" varchar(1) NOT NULL default '',
PRIMARY KEY ("fileId")




) ;






INSERT INTO tiki_files VALUES (1,1,'gd 1.8.4','This still has gif support, right?',1040550043,'gd-1.8.4.tar.gz',257631,'application/x-gzip-compressed','‹uF•:\0gd-1.8.4.tar\0ì[í[ÛÆ²ïWû¯Øn°‰ñû`’z\Zà9ôÜ4‡³’Ö¶\ZYò‘dŒ“Ò¿ıÎËJZÙ†¦÷>é—{y\ZYÚ?™ıÍìÌììJ:[?êvµ]ûîÛı‰v½×n‹ï„­F?~Û~Öu!zÍv§Ùn5:=!\ZõN½û?è|CLéß4Še(ÄwV0?•ç=Jç¨»¿Î_ı7Læßrq0t¾Å?z½ûÄü÷ºí.Í½ŞiÖë\r˜ÿf£ÕúNÔ¿˜Å¿ÿãóÿìûÚ4\nk–ë×&*ôÄÖ¬X|V|&®ÜñÄSÂü;ÆA(a0à$\"ÄĞƒÀ?áe\\%†ıi<\nÂ]ñ£ôÅ…üì|vƒPV„t”şë?[O}·j®ˆQOvkµÙlVÍšk¿kÒ\Z“±x+í©ÏE0§>äÚQE¼“‘çŸÄ{ßd‘$®/B?¨ˆÃÏÊ‰K5™Zk3®£{Iš€œi¤v¡eÅ*²‘‘\nAĞ(ˆâİxªØ\Zøbû¾Ñ¿‰jâ-ş$Ã¡İ‘ÉÙE?\'¢8tí¸_,çbıïG—W§çgâ¥Ø¨W»õ?>µúÁZ¼À–^ìĞºQz½yüwñıKÑ,_„ã*±6?äPíŠdPÈ—c%®§ğ¦\"Ô°*Åô‹¿ÖeèçK?ÜàË\Z6†Î†¨¦SßSQ”1ü.jÿ:5W«”mH2šYiS\"-£1ä¡Ú[±–öVíµ²Ğº¿…6aôé…J:s¡îİ(†‰ŸşF,˜«YèÆJ+»,sô¨ÌÑWÊDÅKë3×‰G±>Rîp—i.K¯ƒW¿öÔ †+ˆã`¬ûÖ‡^`I?»ôCLÊ}-pìúöH†Ğ7–÷x—öØÁdâ(Ğ‡3é¨?î[·§a¨üÉÙ›†?åÆc9ë×?¦8¡’¥½WèFÅ‚=\nÆ“~±Õ~	×k5¸£b9ÙwÒ›ª2NåÄscQ5h¼­ˆ&?±Á0ªİHø·{¯AV\0»…Ø\Z	õo±qu±xzv¼!¤ï9§ùşpøî#Nx¡X0ñÃ¸šÈttvxş%}b\"¤4?‡\0³¸>`JÈ\0$¦å^ÚIâØÎ(ÉVˆ{•v’6èlğ7‡\'û—`w4|aaf\nhøuv?úƒØJš?˜c}D–b±Àƒh¾W¢â!?¬£ãåp§$ U?Q¯ğûa<*­è.g(Ä¦`_G×/”©Ç‡¬bjÛ÷t‹ìeæ‡ğÉÛéíùÙõÁùû3œäƒóŸS{~	ühpÕáƒ?c:j_[&?4$ œx1Bm?¿}£­ş’X¸•C\Z›à›Hl?ja]–UÉ<>…’„l‘¬¦ŸRo\'³äÄ_üãòôød•¬4hiés^Ş‚Äƒƒ¯µ«ÎXOšVÓdÖEŞ%ß3¬¼Ê?‘‚Û\rsjØI¤?“ô!&Òş$6N6705¥aäÆôM}î= ^¶Fî.±k®€[ÆE\n©2òÌ“õÜoépaÂ²)8	3|¤ÀÒƒ$«ƒ$lÅ\\?^¿Û¿X™6\Z?I¬Zš^ß]¨‰ yooíèüíZ¿XÛ,®G?ºá?)F°Ê©?ê,Ò’‚­\"†ÊW¡Œa–§‘ë‹…¤˜ Z*ğ³\ZÆš?RÊıbÔrÅBZ¢|H¹Úû‹(á°ò©/ı+}LÀºT\re@(Ø€Äõ¥GÒg2£Às\03ŒäyÁïRA»¸Ò$E±Y£QÁVœÍVrduã(ƒ`\n1	%e¼€¡jÊ-å‹·§?VÄÚ+³pY..?Ân±²@°É®Xÿk?LÊÉ‚”ÑK¥R&¡Ë6?ÉÔ+’ÆPÌÃrèM•/‘ŠEZßÖ¿pÙ÷ğ}ıìÏ—±×“¢nqù…R0[`›?Î2]²t¢–Èk1+$ìø¸÷2#ƒç/´Kp¸ìùK‚“Şşe.7“ÒÍˆì„˜ò?!¥X0m¸VÛ‡d¦œL\\@PˆR]T«†ø\'kSÆ¯¸ĞFòåÙR\rJ-N#¥tàŠ—¬ I^)—Á’Z4?ŒÀ¾º?	ö×ÃúêÁ³±“\\cË	/hk¿¼¿]ÿ’¸ÑÃíÉ-¢Yí~ğoèPA™zìÌÈÅ\n¯¿|Hç+‘ÄÛ^ˆÆÇ‡JVâ-£+&«6Ş™^kt\r=7*˜VJ‘”G’Ë\\KAÆáÔ·1-V1Æ9Š¶À?ôÊiybhùÌøhm£â3öÑÔf?q8t(\0a\nUè:ƒE©ÊwÜ?Æ‹BñÛíÿÓó@e£1<ÿi4š]<óË?ÿ4Z½öÿŸÿü©[îE±ãÕÑ«ÌS÷Æ2å[ğ¸Ã.¶9kåÛ>/¶h¯7ŸGÊ›@ÕAÁ\0q„ár{~õæöâüêôçëÁ¸C?m\"Z??ßœ\nÊ‘Â5;Š¡ĞÁ£?ı«ÃÓSŒİ$\0qkvut}Ë<ióí­ŒAæúö¶$ıyYÔ6!\rŒ@+ä¦Àƒ¦æƒ$Ê\nƒ—ìQY\02?\0È“\ZÀâ¡s2²]÷CiêGîW6:°G‹à”±kãñ[‹\\·ÉØìtyÍ®mÖë›5Q¿¯Cq\n×]›tmáu»C÷;tßÅkoP)&§ï÷Û=êpèªˆÔ¢«MWj¯sû ÚUş@¿Úfƒ!4Bƒ 4Bƒ!ˆ_Òu¯;=Bƒš\Z„p‡†İ¡atßà{‚ĞxB“!l„m‚°M¶B›ä2(R¹a™V Ûl$Â¹MVØf+°	ÉxõŞ£Za‡ ì°ˆg‡ ì0’µÃ²Ú„6[? ì„±5˜?­ …ĞfM‚ éªHgER·4E TÇ€ È0êîi“l‘š°I z6Béàÿªî•^üF:\Zi¨HEê(âT$EÑ¤*’®LwT4”CÓ$ MBŞ$M×\"A;äÏSß¯o–ûl…®†@ƒ4I–MV°I–MªÙ$İ&+Ø¦l²‚CİBØdıi\n:$®Åv![µÂ?+ÿqûê‚ĞcÒÖ&Ø6©`x›Û	 M²lÓ\n6\r(‰¨ÅšÎ6O\nl±vÍ¼/ì>{½ñr? l3‡¤tyRÉ\n]’Õ%[tI©.MV×Ê.³rI˜-ºÂ€r«A¤eC\\Ãw‚E<]R¤KRº¤`—¤tIJ—ìÚ«z„¹G˜%1[ìH„ÖbÒ½l„_?yc?˜ü;4 H\r?ô”<éß#ı{ÔŞã¼Hvím›Hÿ\r+	E\"ÒÈ!üá—Š\'\"Š§w³ûùg‚Å$+BƒK\ZÖ¢%Û˜î-VÍ6 X4ˆÅƒ?Ù:dÂßs/éâ,¦¦¿|Ôlí4T›iœ6ûñ·	T› ´M_h“©ÛŒ“gD: A4è„}XúŞŸœ\ZVp´X<Y´MŠ´I‘6Ém“šm²qÇô…‡#\re?»#ç¾\'¡^#~üÛOïÎÎ/şóÒ€ tD0?¼C°;¤N‡œ\0v¸×ô…±uØ	ˆÃf\'‡[HœÃV¨^]¿ÿûÍÏÿø/Â€!´ÈZ¤T‹8[ÄÙ\"‰-Ò\"¹-˜ˆhq> û0éí?\rzìŒ¯§\0¨7š­v§ÛÛŞ©~©>T\0°cJ?¥:%Ù—àngèÑõØ!ç\Z¯\\Ÿ\Z“*å.ph>Ë¡:§Ñh2ñæ%İ‚[w\\!Ş{ş™—û«x¯a_ö5¬E£[ß†\n¶o%ÚèjÊh^.ÂVoñŒ7\']<•O›X?½“Ø¥Èı¬‚AÒUÆ“@XåÎÎoÄåùÍÖ»ıÏ/Åé™8~#\ZÕÖœ këÕÄ½‡ÒeæËµÍ¢(Ê°	?P1xóSŸÎëFÓ>\n “»¬İBcÏ1nl³Ç(§Ïtã¾¬÷áº‡ƒ	<lÁ½ø)gh÷?Y–áƒŸ°~xÒ‹ö_¥\'ò¡ ¡İã±ê}tOsıd^F×A,=S·PúÑDâ™(bØj$ö‚	V¡\'m­iªÂÔXìÑÜ’èLùì?¢ğ8uÄ“Xİ*‡Û@øı0T	Ãr§åMUÚWÀSŠB¨â)FøßC±hºş;«`Áñsş››@6š9‰Cç-€)åæ*1øR™n J¦»±˜mÒƒb2òª¥A¹•)Óæ2éÉIƒÃíˆç*Q¼*ÈCşò?•3€øCB~Â?_Ò`?mz~’à†Q¬\'?8Æ.d:jZ\nƒÒ‚·•Mƒ3r&¯GÑ¶‚İYìúüßo„›%Ãg¶DXîÈPIOê2[bHï¬´+q˜-P{4l?º‰—xŠ¿‰— >Ü‘\r\Z+ıÛo¢D<{‰Òe\r4³)T ğøÈ/¿›ÕÓW‰§â—\rÙÁ™ÀÎ~cùIùüEˆñ-‡´ÇÕ Ö~Æµ‰Ä­}íjìÆ£Ÿæ°‹İéÖF3ë6ZÕQ<ö*BúB†°xªº0LÄ4Ô_œĞ»ˆ8¨Ñ—Çô¢ìäæ ¡Šæ°õGU?Ì§1?æÑIŞ8€uÍ…”B+?Gú;hç÷Lß’l\nÈ\'r¬ ? Üéë]úåÑõûË³[±4ªˆ8˜¶^?€¡F}A÷7p?Ó÷poõ“1¡©ÿ Å]”ÀÅ‡,¶^]â÷İÃıPßçÅA“)¤ß¾?ƒâêôìè?Øj¤°’¿¿ a¢ŠÈFŠª4L­ÙéTÁõ£*?”=Ò`úñ!3Â»Ó³’¬€€RI–÷JVùøİ…ß²IÒBšŠmR1£]b¼Å^“gÿçE±PÊ<É*±Àhi±$ƒÄ¢{¢\\?T\0ærmXŠæ1‚Y®‹Òd\Z*°¢SF¿uX=?\0½	ºúEO¤­/ø?8Õ2¡L`Ò›Éy$Ær!›fAiø]Ñ$B!øU*? D676¸¹Å²’·àzu”¹ñ”8#1CNˆo?õøÓ±q\0A¢€5r\n¿?Šç…‡Q°`L!ˆ¿¼@Æâ²\"+½fÿ\ZHúâÊ“Š¸aJp#¢L9İ°‰2nã€Ü?‘‡¿•Œn0¹`i+%qT%íFó¨m«â†\"—ûØ™A·´ŸÃ\Z§F?”EŠ.H8‚½ñí´Ë”)MfeÈ?Š?ô!wTY³(hÛ€{¾*ÚHß¢­ôíÅ÷DJEúÈÌ+Üáüx‹Ü\\›¸L=…;lF7Í7[‚aÜ±$HÎwˆlV6SJ.~uzav|ËRºÔ?\0\\PHìB§mĞv	m—ğ{¬ÙÜ<[k?¥Ï\r\"-(\\\0[\0\0¸dLùÚ#´Ë\0Ş7î`@õuØĞ‹´şµôoØÔíú×jÒê£İ§€7@	WüÚHûQo\ZäVØJ£áÂ7`¹”e3æÅ‘qT‹Vû\\/Œ‰ããØ0n?¤1|˜ÙŸãXÈ™ïirO7‰7ŸB¸ÃRGan¹±˜N 2€-…‚5“À$œ)TœçwŒ¦~€®ìÒ×S6¬4Ò…h&YcÌwn4Å¼Q*Æx$õÊÄïù)€`)\n]»*J?Ş”µ“	t™Fõg4ç<eZõşÊN^ÿÑ´¼ãiİ{?ID?\0£,»Åö¶§Tñ?:Ä6X¥%¶?VN?A’W¢¥k\rÍÑBšGóÒ$f3pŒxWŞd­wCXÿy—Á¯vüÙ¤ûZ÷›dä›òæRKJu?´$TF‹Q‚³‹=‹:?o¥ó\\M#<¡´¦4©”¢±|´Ìû±òU¡§CÇï@ƒ±\n`åšIÜºÅ\nÅïCqŠ±n‰âé`@üÒŸÓë22˜œ’º7,”^ÑEÑ»“œ‹¿•Œı%ÉÀœ<);İ;¨‚Òô‰_¤ş?ÏÆ9JLÕb9‘/gìªN®BG«Àı\' «Â²2Ó·°¼Xúö€2«¿*³RÅTi¥©r´Â…?Òé„İq\"Ã”‚°4âtüŠÇMÅ¹â9¤=1Ğã°/B‡‹†\n¨¡k@éc4\0åu.$B|;âö¶B´TÃZÏ°Ìœ‘!ïXB4s!z`<üğäİİì¾¾» XdÆ°™ ‘#ğIÑA3GÀ–o´	üÔTLĞ^b¶@ĞY9K‡xÀU7¶F=Z|bo‡©õOnïĞâ;<>[ùƒM{áÿt—ÇÜn›§ÊÅtÃWÉ?\nTÌc€¬ï‹¿h×¶<;G÷?ÛşìÄ|½1¿Î|Hen’!äÃ²xşÍRÈï‘¡kˆ]iOz¦û•ÄLÉ¡Ê’¶\Z+?’ƒ´?k‡EGü_Û%›J+TòS‚_/ˆİ<Ó3y~”Äg™Ö<áÊ™f«‘Ú>Ïÿâ…>ÚÑ³aÇi;Ê-<\rÜ6LNq¸ÉÒM¤7¡+.éÌ\"3ÿ¥ŠïğÀÃseÈ­w¼¸ò^J¯A(‡¸·Ú~ÁÇjè:„ÿå²[Ã^V+`¯=ül:az”-u„Ç9…XfK¯R®¾†¥€øU%~t9õmŞÂß)TÒ¹ƒÊ\0?šñüdI‡kÅáTÂÆ3VtbQL·@ºFÂåÇQ÷(\Z‹‹$Ü^İ©ÄRHKËS\\F,úº6öWºzj/ôô~ş½ıÈl¤¯ªÍÆ…^Ñ‰5×?eµÖf³ÓÁ}A³?Úz,ïÅ$ÀÏı?Œöqæ‰®Í•¬\rSµ\rÂ^:ÕMç?óÔ?«H’?RÉî?Ö?8¯$ø*}¸y|Œ8Í|?£Y¢ª¦ëÅâ–Zø ÍYN2© 8.‘Èu`\'g?‹äÖ9Æ¼–n­ ÿšcÊEŞ7ä½\'&%¥†$WŠ0µ?ÎA‹†§­u)L¼ëÑQôÄ<.O@ò·°D?î	1¹ÕS›İH?0B¹Qÿ/²Ox±9ÿğÆ„‹€8œcäH?Ğ4³¡Z]Ãu•Éí——EªLJ~©Ñ¨ÏÁ˜Â”6½,ì)Ëf‹E¾=›(­­?wÒõ0Ñ=>]ËÓ´b¹[&Î(˜èåÏìâ`á®a¾‹ƒ»¬|åİe¼\ZÒú“Ç3Ì#vğÕv¤2}	˜ZaáEMÊ%Ÿ(rˆ•JÿN†Ÿp)BÀÕôe&gJ¤Ó/ÃV?r?½‘{z˜å×wÔ·$õBÂ¾:á“Ü‹°8¨ã_	,®VI}v_óŠ˜$O°iSô‘îü³z\r_B«0J4&¥·uT-=¤÷/ë@x,¥8À—e|L÷H2\'’yJ2G’ÿfïİÛÛ¶‘Åáı×ü¬÷ik§’\"É—\\Üæ·¹7»¹½±{Ú>yh‰²¹‘H­HÅVÏéw13\0?à?’íÔÙM#‚ 0æ†Á`%ªlÁÚâğ¿t£÷°·c`Ñ‹‚:A0ÿ=^¤ìÅV\\šef::6ô\ZÜüwúMT+Pk›ÿN?±YÇXæÉÎög>OÄ×c{çk&®éï.ğ’DÑÿøÛòÓÀ÷[ó]®Kõ#1–N!},Êd!µ’*ÆædéîoöÏ#Ú³DÌ\'‘Û±Å8ò?ù¼\nÂIlïPOªoïÚÚ>¹ÀP(???EhB•§ÒÓ‡á%ëÉñ”\"ªß§y±Ä±R—?›++{ÕzçG¼ãxó[aX*+\'…›c¾ÖĞ&?f§š–k+ñ?„J@I!g8\'>$Å|ØˆE\'\r™Ü¢§ÇÙøv®Ç¹)õUb‹{Yğ]y?=^8t|Ô¦à\n¦Á‡nÔãäHìi-v¡n	ÙŠ_ü&½Âï¾û]µŠçpœ?Ma¬ÂÈM\"\0FÍ™İ®jÜ‰–Õ.áñk;~~?‡apïYØ?\Z*0„§}ŒX%0?–\Z¢AÇ¸¨¼_Œ‘31‚1£à$è$ºŞ5}	AMêwq?“ù³±;q–ÓH`_ô§ÒÂcgâÆŸÅÓ‘¾£Æï¬~ÿíò÷XşvD×*û>5BºÄb›²’éŠ‹àsùãRåƒ?ı½²‡¼0\\Œ jh1ZñÈ”¯dx?RÒø|KÃã_wâšw¡¹Õ€½]1[»^\rái€VIúğ©?_*­ı’líZ»„Ö\0şKhíRmMı\0˜?êƒœ®°û#üõ=/ãôS©À¨…?ª_bûGøë{†\"¨.u\rÉÊÌ*…¤£ “D>#pv\\ Èó¿şg	>~¬î†_	B<(nZÕñâ‘\níÄ´iú;ø>RC­…©RùCÅœèƒÂG€—<R[ÖŸp¾vˆ  E?¬8ö$yA´\\šºø¤\\2Æ O2V?çx¥TøU«?59ğ>97ÚÌÄ:2ëUñâ	ÁNE4VGL\0ê&&âOÂ´â7zYBÆ‚Ëp§\"æ$#âtu¤mBòJ}Á£àhĞÂ\r]ÿÜ™A8ÅÀ*ò|ûE0uWö7ö9¾ıŒ½’JA¯=ßh]òmìÿ÷’oc¯†&?bÌÆ8F]cDûá#Ø~K½ÓõÙïş÷rì-&S8?¿â¿Ø8Æ—|WõrØ½DwòxÅKVÃîj \"ôXé÷?°ŞøF.«2¼3î6¾Dy	İóBñ<j{Y¼Ü¨eŒëğZ¼HäC\\‹HÎü\'1vs³šlÄ—ŠÍ¥,İâ|.ÑÄ@û~(z¶è&$KM€á‚\r?aƒ\rG4»‹I1\0ûy%ëD\ZMÄSøö´ı½¨Îøİˆ2z­…Á-¾µ†¼V&«KÂLI”FÛ…¯Ûm>¶Şµ~\"?q1\rqš’4ÓÕ“S´šZ	šRI%/šN—i\nSÇ©’J^¼‰a²	¹³Q¦.?¦.‘¦.³i\nÅµ2g«Zsv¹6šj	¾–hÊ <P˜Ù·%?µ1Ö;?ÿJï@Ç‘;?Z°46}=9¶Áåºn?øœ8ÿÒŒ>¦?étìoŸıG`îZ²üjĞ_%ó¯é—)êPÜõíPÜ&DCH×,$$G?¹qüVeËğ5ğaş±ƒ¨‘‚ÁBæØûƒ[ˆ	¿…\r}p›7Æü+,šì‹2Ø;ä¿Ù+€?•@Ã?9J5Ì#«È4ãÓÎWÀO1¦ÃŒVö#îüóìÿş?A»ƒü‰¿¿”ï/wwwwÓ»>çÎ\"Ùwœ¾d’œ52c’º`ÇF9{˜‹‡	„İ±ßÒ3\"*<CD\"ƒğSCT\0fVØÍ;ÛHB&İGÁdº?|#ß‘…\rØ}„hÂİİ´?\0?Î5‚°Â¸©;ğpNÿ\\qĞyÙşø&?‡¡¹tQMduGøã{˜¬y?5/Ğ!Å<>Èw6ø°.ü¾FFZ$lKjÜËsKuÅÄ~)á’1ÑÂOó\\j?[rê*¾¥†˜\Z5tã9fB¾?\ZÎ7E\r+…\ZV’\Z.ÍÔpŒia*QCâih\"±­?n°EF‹©ë³9ãçNÃTŞT?³-€g\"8qˆ{p±Ãpûƒ˜¥Œ±U¤õÍ—;>Feúø¸F)ö:8Œë†çÁ‚IÖ0iv\"XÍZˆ­–Çëi£|MG¶¦É•Û5®ÏW7WïğèÑ?\0{/ÂD¹ZÁJÄ©ĞÃà]<0\0?ògpbmŒ—ÓÀŞ;ìCÎGØfát‘?Û}›0V}êÂ:„1bÆYF¶{-Ü™‹­òš°‚ü3ÜmŸ£O^±Œ¯a%Æš o†0LÚäI>âAúç²º÷›Pg\n0€ß]ĞyÀ±Gç@ãÏ{ö[oD£ZLHÏñVá4¸€ãíşÈeM¼²Ç˜@zÅ„ì‡Æ^é?WßÎ(Å&D¹2¶<™x#‚_X+ìË?Ò«‡Át‰ç\n;QØ³åèÜ¾Ÿì³\0¾àkÛ‹xêÎ©÷äí´•¾¢€)îSoi²`Ps8WËÆä,F!äÆ’/ñ|‹ßeäş™½‡¤Ì¿F³Åt]ø>Â£.€´\0~*Ğ³¿¡½Îœé”Ÿİ?‰öÄYèĞïöR÷Ç‹‘1Lˆ¯¨×.èŸsÄ?şqs×*°ë¤8`.†ûL–0Z/p3ï~ãONë.“¬œÜ]`Œf¹L§•R„Ã¶«ÅsóH#â,LğS°$Ï–áÙ_C;¿ƒ?Œå‚»ö ?ÜßEyŒñÿ+õ3Ì§‘úì\\ûl%ìUöƒâÓúñ†²ù¦[\\ŒÓî\"®iRB8¿\0eÊîã^TLTÁ(rã?Ø†a‹Íë+`ËéIİıÌˆ™I‰S\ZÔ)JÚ\Ztl	Œö½îğÎEŒyĞiVÙS4P)ü.åÀ“½‚ÃÃœíƒİÎPt˜ô!È·;à­X\Z}ò#?mH¹”1ğà8Ó¯*TÊt?*µ¿ğ¦Ó“àI°»)›)­Ÿb=‘OÙê¤Vp†]÷“?y®Úááµ7ó¢1Rü}¤äMïcWŒ•ºŒ‘©EuLôQJ»ÅÁ”\rŒ lÛ©K…ëª¨Î}X,B—U¶	_ª¸ô`“r`»|FÕ“Æ)ğşgut^<“J±zˆB…[ “òÿÂ°bôñ…\"‡¶sùœŸÅh\'n ~°µş1jpyú×Ağ	98[ıŒóŸŸIpº°¡¿ µ%³¨Î$Âa»ë	óg;ºbJ59|‰MÁcˆT¾¸Å€ù`Ü§¡	w3Î“ìTX)Ü(û*F\ZÄş`a„ã<æ~ÚR‹;éØTkK,.Áù×ÚxûŠs‹óˆ2{dş \0b¥\Z\Zèş.dª81Æ¿ççŸ N’ônıßmıß]ôÿ™fÊUc5Œ>Ä©È=SÄyYÍŒya_xxƒHÑçˆ±b’Ÿ`övàÄİ.ÓñpÌX–?ÙA?IÇ–‘(ìS¡/¦˜—DhÎş§A0îb×¨˜:¶\Z¾=w\"LÖíB3¯ ÇÓ?™ÈøTgĞ‰@uÒ¹\ZI‰¶î¦ø¥1¤C!%ßV?…éGéT\nÓÙ’æáƒ‰P\Z?cœñâéPBô(Ç,`÷Ô³Á5EH*ê‹ÒÜ¨VSuÁ \n4’Şlˆ_°èn6À¿œÜFîLKËM4×#}6¾<±«`/yß­y	¡ùÁSğlÚ0Ø\"iªjqÀú ŒU‡\"d¸¸ê şD.ÆTÅ¡\Z†W4hî¸%(~~nCƒWßÿ€?ÁúfĞå|ùı{üËDå˜æ1y¬jg•g&ë`öâùëWî‡YŒ~‘¿~Õ=5ñ>Î‘6Fø—pH<ÊsôBt*	—\\›;D?!Ÿ\0ÿ–’QWúN”€À?cü°G|ñÃoû;7P$ª|ôÑ/G1âa°Gä¢€¯~a_á?‰íg¯yDe|¼¸÷xìí{w‡ûw<´Ãå|~XU­\Zsp¯Å¡Øf\r‰µ?&åH,e6±Á¯¦Z ÀºşÎ+_¸¶ÂAq6Ü™B7ãG¿èĞ=æöchıJ÷Ï£ßuIŒ­¾šP.ğ«†pÑªwÙ.œr\'?£8†Î:Ä‹càLÎ8×âê7X7/ğ|ë$&åÎÑNîò–©?tF\0 pn„°yŠƒßQ«‹Ak5(½?8(÷ç–Ü%q@šk,H¿õ¢lÈdÒ?¦ÀñI¸p¿G-S¹ƒ	?pËVu ’)ğïÅ)Zyjt+g`i EÊæ@¡ş3O[\Z…‰õô§‰Ç!¯¼¤c+R»r\rü‰kZîYoAp{’	¾qgîZ8!ı3Ex>UO Ğ±Ç‰œ\nÄ&Õ’·ôG:ì?/á?O¸›6qìUëÃÌUMçÍÌ5ynĞÄjåÉ@áöŒÆüWë²?\r§?+ÆL9ù®‹N~¥bÉ7œÃ—.`æYßUåğ™ğ•cûYŸ‹?|9U‹ÅEV?ìÌuKv[Ùò&ï;–jâ¨¨Á´¨*úBüÒ¬¨k76Ñ?Æó0wñfÈf©0_Øß`çî ßï1‹?óŞï`ùP•1ÕÙaºÀšxÍbã1½]ÊÌ!Z§¥[8tÔZğ=ğqaÓxØX&t¥[-£Å”j¦¢£	‹Ø3Hÿ²Pª>¥ÊW?ìªcR,¡•mN6›R£JYÙ²‰\\­¬>ªşzYQÍâ\Zwï¤4º|¶ÿgŞëĞåŒ2C\0¦‡ÀuÅ¸‚ÁhF}ñåÂY?õ¼^?‘\'8<«¯=Şª?ñŸ[õQm	õµ–Rê\rX}à¿ì\r<è÷ïhê‹E*M¿wpÿx\'•ùr0Ø/…è&­©”Îô?}fôØ@‘TÜB?’Û€UŞRACÈô\0”ñlT”ÜFE†»¢š\\”Ş‡lqve‚\'%y>¸p‘Î¸U¹ÃŞş,ı(ßş,ıXÁŸ»\Z»¡ºã?çÛ…M»há©~vGÏGçyÂH¤a?WTùg~#‚O¹11ê¥Øsó€ ±Çµ9ƒ¸wJˆ‰ÙÕaÿWë\n©6JaÏ?™‰Œ·Sšq°„¬rÎh´„k¡$ «”ôÅM?qı€/L­JUÆdéØƒ?NP!ˆØ`x`Jbúê;ÖºówW<À‡P¢?]¿£?ğè’°ÖbW<ÿYÙ? ûQ…ìÇbÈV?m`?\0G˜Ú}AÂÆ~ñ`‚»êÂG?®Kåv€¾‹?³?€­í$¨[	¦ó‹ğ‰<»¡»ö…ÿ´xë¡ÎŞC•Íúw×Î€î\"ÈGÙ\0×Ü?(Ú¨·‘µ!¡íH$·$ÊïI”Ø”¨íCßÊ÷ß—Ú™(·5QÄJ»Û™ã+·AQt3ìÒ$æ„lØ¥à/“ÜR[ºjŒ@õ-\rmcïO¹3\"67xœ¿j?uL›ÜüiE;Ù± ~Ò=‰/Áì—ÓÙÜfoß™ŒÕ\rz~Aâ‰ãSĞÆìÕsşót¹¡?ö¢Ğë=?yš‹ŸáoƒÃ°do“37\názª\\.ÌúVÃ{xŸ­‚³¾	ëÅá“7·ÅåP@X?ÉøÑùb\'œwìoíoE<İWáÜÔ¤håÂGçöÔ9u§¼%Äg­wäíAKpı…ûBÍåç¦¯+â\"lç.Oµ‡Ş 4u?‰ó<€Æîhê,èš?©_y4!Q‚pÁt®sû®}ş¡ë™Â-èP f%*DgP1„k`ÅrköéÿPG=ö`Ğ¥BMs?ÕqşÛ…ÂÂ‚LæÜ8Ãlæ°Ÿ~áVWb?xŠbG¤‰Â’F;“qlØ?ğ:•çï^È Äv:Ş4Şh?ë}{ù­¨\'İHÒğğPšãK~?B‘ê×Ô­Ö+´uÎ–5HXL´X·‰AÜÄùoCxøöúß¢V×öm}¹İ±¿9å¡R8§]B¿¾g?‡÷w?D9–}/ƒÃÌ±6ß}‡Ñ6;§ö7ğ\\°5°	l	”âAm(ò~ÄÁYZB†•¬º’²B¹\'4–5¨y\\Ú÷ïŞgjœ’ÊTI™\0„u ,÷œé?®?Õ\'<}m?İÅ‚¡çùb,Ú§Ë38cf’_ı?¿\r³/5˜…‡r9ˆkKñªO¥V2±s0]?~úxå{0ãàqNæ§ÏC¦g¼DRI`~:$7Í»?ÀÄãMì÷ê(÷%NHÍó»ƒß{—òÈ`åLæ€/Z_]°Ä!³ÛdİãE[\ZT\ZX›`mñôOƒè×¯˜‚Í´f¦!îî8°öãG¼¯âüññÛƒ^÷/ĞDÙì?Î—Ó?ä\ZsB·=a4KÉéO~š1tñj\nöÚc±;:÷½ÿ°EÍ”´HŞó9Â³^§ IÏÁgÈ?—W3>f/ø…^âç|á~ö‚eHÄ}Ğ­h§+~•ói0wé\Zl’ñ~ÎİéUÕñ¿—a¤\ZÚê•kíÃ¹Lj\0«yÁâ#uÒ§)€¯9\n?ÇÌóW`U\\ŠŠ8jRS†^	ÇQ˜CÂ2H_»î™ÛİúÅĞ.Ÿ8©áæhìj‹_1“®\0ôıñSz95ÍßùÁ¦ÔO[y äôÚÃ¨?ëcH#‚°\0ëˆ	‘ÌL…úœ·~1Ÿæo«HO+Ûô¨?­?õÊ?pÂEeÑ¡R™¸.°gÆH½½‡@ÕlÀ0fB=Y?O„+¼Ğ-y#~ ¤âî?AoK?»xíğ«ˆîéÍØ3—ã/äYò ˆ¸Q<©—øz43Raì?C(¿Ës5KA¦1ğš^¢¦ë\Z˜™—¡?Uä˜£ô¼X4E<?Ô÷q:)JÂ+>¼¤æ/Õ//UpÒù¨”ndÏ\n4²k¥Q¥£AVGÔv:æO.a®HcÇ§xî_‰T…òƒ‰ùî;t]­º”÷ŒçŠc:,ÏÇYK=…š&uòHíğ‡¶zäËá?a°ˆôE‹M^öë¶£I3Ú¹H¦Ù‡?èz¬†ºš%ªîï\"‡mbß\r~§°g5ä¹ªLUÓ%İáU‰U9pL Qvšgº!¦M7…†ßÁ¬â\\²ï&ïéöu‘=·ÜmêòYá¢XÛÌúãn€9º³Ñ|7Ù±Å?9%Ò¡Ãò—ïÕ×”L=­í1Œ`Êğ´´•?”FZ“±\"S4háğoÚ~RM0º!`‡§£N8¨ñÎ,²©àò,“àQ(ïƒ;Şá¹³½İòâ%xŒÌ¯03(~#mŞ§æ³2Á¡\Z‘ÖV(y°ä\0“	?åV3Í_e\0\'<€+IgG×½˜6ZÆ	Š‚Ã?9…§ú’ô…6?Æ+EM”K»b¢C„&Z2½Ad‰­R’Ö[ Ù?´GFz‡yÀä@£Ò?v3EB\Z½?hêŒŒlÚ//Îä…E²›?+¤ï”DA£·;ĞˆÓ›\rmnÅ{©[sVkÎmƒ­Ñl~9ÑR(iPÂ	ÔQ’ª¯Àk0TJ¸OI¶ó?Ø/Ÿ}|úæıÇWoOxıøéó#ºcY¶¦n‰ö\"õÚ?¬O><~{üşñ‡çoOx›!P6\Z^)]°rÑ²ğ…˜\Z<~õßÏ?Â™\0ùÍã—ÏåB ¯ñ\0ç@ñ©ğ>‡¼OR(+	ÊJeƒ²*\0å×\\PVağ>‡¼OŠì[½êF\0¡]™“ÍÛŸŞ||úîõ»ÇÑÈÆlt1Ú|+S^8e+;˜ö¥¨ˆ“×Vñ*Œø’y÷Ù«¡‹şJ2ø\no~\Zü.ƒÏC¹_i\Z\nÃ€Wİ‹¥õÂï”Rúá%­ô¤uEV)=QA?¶Ì9òlÊF¾ÑÚ€HwúÂş“O´¸O|?Œçomÿ9w½û½ı»gãŞyë­ÓŸA¿¸¿oÿÍ¶í½Aÿİ? gög¸wo8´í{Ãı~ÿ`Øïl{0Ø;Øû›İ_<ÚŸ%¶¶í¿?ËÈ?N3ë?İÏ›\0gÓşîM|È¤ÉˆñGKì,Áƒ=°D–Í?Góé2„¿–{‰¹¶Ÿn³e!İ½cók‹ì	3‡ç³\Z€	-}ï’q§0rg\\ ÚöüÅãŸ^Ÿ||ñîíÉûÇ\'?ÚÛw—áânxÎÄÎ]h!¼-–n´š»Ûò#¨xüœ	?Ç\'lÍm?ÜÆ~?lª{G!¤Ìˆ/>[8ósoÔÅ{ª03^•k¿‡¨¡0?(Å?a™0Yo²\"Ç%\\ê¹ğNa?_4&Ñ$Fƒ—^BCã`´„‹ŠiãŠVö|¹˜3M+ŠœoÇ‚ìj§+\0®,é€có³7vÇĞ^`SV\0€ˆrøA^Opæã?ô6¦`£¨\rÊÙæ@)H\"Ç~ASéo±\Z`=Õ,Ás‰±õlûDC4Åa··Û¯{Û¬J0sBû	­&è\Zâ?½§pWá+ÔÔ²)óf8Ö`Ãƒí.×Ãl!î%ÜêÚpòl>õ\0StÉòhºD·5¤ãƒ‹s§p ŸBÄxM¥=piÏÜäŒœSoêE+D45ñ\"ºÀ9£¬y£%##1y<g!cî0Ø,vCÎhºŸ¿2c‹»İÃhì˜zÏuSĞRè«»ï°ÒßiH®ı=Ví??Š‹¶ÏÆ¡h›{ğ¡{Ì¶rêR^DºóCß–?/Ñì¢Çñ³»ÀyfX`AËI‰¦‚Ë0±ÔXmXv=H°!o±)7Ó1‚%C\n›1@¼Ì#@¬-€Â?º€¸¼ñ¦«ıSèò=N+3g´B¸î\Z\"¾`HÑí$ï´¦E¨ªN€<?ŒÎ˜ÆÇg*¤¡àÀ¯Ø¢]¤‚~LOLˆ\'’²Ş‰\"ÉjEb(\\ûJÏ¼´œDX#š¨”^ ¡•ó4¢x#¢áEûæ%w„Ã‹?k>ñ#ˆA4öp¡˜ß¢E,\"<4CÜ’jeŞë7çÉ’÷?€˜¼`ñ™+L?`ÅSÇKâ;ã™ıt”šÙÿEwûß1¨ˆM%³Á!ï¨ç£ô‘Q¥”±ùÈŠc¶de\\?KØÁrÇ½^ÏŞY†”Ëpt_Üyß•íP>gjç©l?â>0Áhø€?2|¤ç¼g\0	Ò3ÙÀ•V:ĞŒJ]@Ş—	´Ê“%õ˜ºøÜ	=g¸Î)£3\rÛ¥·ÓÀA¦²fd©?d.´ x$vØXˆ•09®,ïÈù„‹4tñÃx6ú;2.}ü‚­ÂSgô‰±Ù1ğCÆ#?3WX–´æ“şÑ]²]ŞüôôGºz2u/=ˆ½ÅhĞŒ‘öìÇ0¼Ğu-e÷?ü‰”T4f`\"E¿½?`36\rñ}´l“Üî¶´wºÌ?Kù½¬xo7U9~¹¯¾Ä,]¬ğ`»}ûî$:t(Q?­††	6­ÈÃÙÿ™Æ?SŠcÇrÜiCT‚/Y?‡Ôã‹xŞÆM}o¾œbt`‹\\q?è¡/Äêx´7Ä‰eÕ„€÷şY$VXõit	n?wì_t{øŸüüäÍ{Ş¼ç¿ ¼\\Eõ•>à«	~eç~öÏ¹{&»™wõ?İ ò3IFÁ×4S†–‹-\Z¾ÅN%rmÙ3ØÎæ¶\\w\0ù|]»èPr]HgŒCÜH`L‚š&ÅÎ˜3åÕ<÷¢ƒ²×áÍ|Å{†](XÒ(œ™r0wÂ?¤3Ù‹$PFq©ß¨½s‡¾Ùµwh«„÷ĞáÂót9™ˆŒ¥S×WDhÕ‰ïcS`$ô“XI9QÕå\"©Üï^åTLóË±‰^>–o}X§ù÷L•]È#—òW\"Ir1Ğ`\n?R\rç¶¬E?&¯çâAAš”Ujr“óQå+*?Z¼õñ¨±ÔÃÄØyR?-È¶¯8C¨Ÿ”#°a¼	G;À?’Ú­?1#pA¹\' -u˜rqÁJĞˆ´ì­Ğekzœ‚´…ÜXè1%v\Z$#ß\0?x“Uky»j\\ÁsÔäÖìZ‡ â½+•Ú+qs‡Ş^ŞÍ¦¦+[µñ‚+2\Z7]Ø`½Íö‘‡0Cñ„‰]{Ğ»´Q`Ëh¾dâöÙó÷?}|òü\Z4~\rš““&D„Á)˜Ùô{r&„3˜ASÎB$?òÓnó\0:â×Hr8|\n°ï$øCø·¯^œüš÷‹“«û=ÀÑ+	4¾Œ’TYpÚÎä?¯H‘Á–P??ƒGM#]?Z	W<JìIªÛÂVÅ·3úçTçrÚFn­?i[¼Ö§Ü0˜~.	´~^–}ıÌurFmä§P¦q+ùİ{r³•Hª˜ü0ÍmI‘b‹)]•«n‰¨2Kèí´¨´úÎ™/³¬Ã*ãËd§p`é ›;éäíteO“°g.SvVÜîqÇp<ƒÜñÔÃd ÒŒ?…7F@‘1ãı<?“aÿí¢–7ŞYşÉ.™Eü-Û»£Œ?õhÑBBãL¢-Ç¬É¢?ş³t@L T1!³¦.ùyKC…‹‡ªw›0i=ÿS/OÛEE½38Ï#×k60k¢5øbá±…æ‹[H°5fáº0@³>{%­`,3¿O˜ÀìƒŒ?uÌ`ÖÁì‡Av0À«4ÙğlÃ’9.ËNÀzÍ\'æWY&³¨1EŞQ†•G?íuôr¼©~†)øKô¼–‹?ÒªC“gÒ­•7Î7“g8İOıTLW65SFÏu“Bå&*‘\'ÔpqŸ4<g:ï§?îïa€8rÏ1a_aj‘”¨\\¤pê³2á ©?ªÆi£_â?¸\r&{g\0¿ú»It¾*[H“öFìÚ|ûÓó™Y2£\0âP¤\'ÿÃóÇÏìwo_ÿÚƒ?\'¼÷Y™ÈÄc˜!‰:Ø‚8‡Ä\n GÇlflÒn¸œF‰ıBÜ•±‹éğw1œ.UáW­Â*UA?šUjªmÉO ïĞ’µa3ug´û{ª*…„ê•i‹ÕX£DõÚ¸ïjn;yßˆüF\r|4|%gy¬~$§—vf^İ}góü\Z6‘pÃƒÜ?m	„È[÷6;@?ù{‡œ¸ÊÛg+ß™y#¨€æ&JŸT­ãcÒ0UïxZW’ëÙûç—l:>c:ŞN¼-L\r¢Ë„ÀŠC?†ŸşøÓÛAX¤Â©Ãû¶¹ÒÇ7¯Şnîg½|üoa¿ÿàPïç¿8NrÄ?VãÕ³4ÏÜ>·µZ/Şœ|üğøçœv ÆÓwoŞx~|üœ·9TB Pß]x!¸à+OÚ)š½Z*òWCf&¸µ5Àä,#¸E?_óêØ{ PÂvŸ²nS± [CLò?\Z3hÊ°—´>g0X¦¼%C3ØÄÖÖş–2\\ú<ó\nÏİÚºCdú\"ÌúäW6ÊÃøò5çô£ÄoíáO;©KÓgO?ı×Ëï~zûŒŞıÁ,—³xk³?’QÑ[Œ–F¹Âí?abŒxÏÂ•u:òBM(Ú€A6Æƒ°%„ˆ™Áç/šP:f÷îõO\'¯Ş½UhóÁ!ı{WD¤@ˆ›KÊ&cŠÂ‹ï”×[SĞ«_-şO‰ÿBÈ#ŸŞ¨åÃüøO{¸?¼—ˆÿŞ»wï6şs\n¢ÏDèÙcîÔ/‚³…3ã¶8³ná|kh;voé› ®?Ñ=\\Ù;ãs|®Ö\ZàXß‡‹dÑ;‚íDLÎ„êx@?™ )˜Ùµuê„/?Üc…ùÏ_ş³ôFŸ`ï\rÜØ%jRÖŞuKê—?&À)ç·èvE€&Õ\'\r¼}æx>ÆH8‹³‘ğ&ßaŸ\rIr´ç9F‰Å{·`VóãĞÄ•ïñøüd\"†ŸBÖÆC;^‚\"ŠÈF5DL2¥\0xN†-÷Ò‹vD¶3nq@Xôöù78¹½8İ–¹W<?£sîÉ¤É.!5F_»ø	UÄæ8îqoM0ŸÀÎçâˆCfïr¾@>óY1v¯¦ÈÕ‡<Œ‡,«Ä#Oª\r)H^ù°—‘?…d(Q\'yLgœÑ$APğ¤C<«\0qæJ€“°âĞe\Z¦ŒU´¶ÀIãùB!-Ğ4Í³û6ˆ©#Ç8Ù*ä=¶&å‹mo-Ÿˆœ÷-ëJº±\0@ôZ1Ñİ±‡\nÑ‘Ö+Æ—h–Ædì,‘½äªÙòÆş¨ò?òƒi[øÿ­Xşïíí%åÿğpx+ÿ7ñ\'OşCÑÔ;ee _b˜|xzÌ:ÒU€-xÔ*Üê…:„PâİæšnO¤á©h¢¾8*ôŠ{…zÎ‹¢Dğü$—öÊ¾°ÏMrÖÂô¢˜Å?s}‘â,.ÛÃ²•V¶ÿ»Â/´Wê«síÕáïÈƒù\0¶¹¯æõë1UHÓ¹óõ¸ÃŠv)ƒÈ¾¯Ç—_?Ù\0:ö¨£à›‹úxü{æîô•ÔNı#€?Ryëó”¨¸NZ¥Øj¬SÈRšNDf*/Ú¦6ò-MKÂnT.2²ƒÙpÖ¦AD?²¡d([&Ø3$;œqT\'Çî\"UËùÿzÌ»ĞMê5Ø;ß€sšÙ•d3õ)	È_U†7ù£Èÿ?ŒU¯AúÊÿ{ƒı¤ügÿÜÊÿMüÉ•ÿLª?\'5pueh	i©/<_\'~z®¸`_<~}üÜî£Vğü¼åp†E„ƒ<AÊÏ™–ßéÆÆè¬™Ggú q¦KN8°aÆîéò¬‹.Xè šø\'èKŠ€á¦ö·ÜoE\nå<¬HgÙ<ø¶Ç¿V]ËÏ¼Ü	wmø«¹şÄ„“ú<†ó®c:PÈº\0V«ã\"?ë@Pîƒf‹õ¥Ñ>ŒzR!á~ÆKeø®Cè¦&6ód¹;ß@Öe6Œ7Élœ£í+[ı9XŒ©Uek¥A«‰– Éğà©­TÍÔå3ò§˜!yŞ÷ù9›Œ¯ÇÒ5¾sòS;@Ú¤Ñ¤÷¶«ŠÜê—æ˜fvÊ \rVêòP˜Fe^G´Ë¶‘®p‹®µş4£œßù®îKšuğD&&‘é+„¢L%×-€•Q\ZÔÁÃä;äk¨{È5.N¿3b¶	Ø\"#ûœ­tôğO:ÂE§ï¸?œşÛEq<ÃS# N:î`ë‚°£sàoŞH=ÁÁjë•u¦A{rp¥ò?„½’µÒéëæ#\\Ul\'s-Ó¦“f#àa|ê\"5$ˆ–õ’ãÕø.bn–êĞª’á+=Lb[Ó—ÍÔÖOäxÏ=õ‚\'ÈJOê\0¡Qlm¡=ª»Zµ?ky`/Ó6¡Z¨–{~÷Sñgeô…#HŸ¢i¾Dz$%F;=:\"YØŞû\0âV_}x^`O/\r˜×o ùoˆx–?8ozıš§ëSˆ\ZìääÜ%„Rrı@¤ûÄÛD¾W†«ŞûxÓ{x)¯ÙñI”âš§ÉFÒã~õ¢ßC™{ÿîçî›Çÿ|÷Á~õ–­ÈQ*®úéy~dZí?µ44ˆÆ†¤ŠÊÃöû¥¢¸d†6§5õ®B¡ÔêÎv‚d7%«u÷06bÿ?”àº7WÊ¾¹Q´Â”{CÈ?³aÅ$Õ5„ì¡\n=Aï¡ø»XìWıRJqıCÌ¿Æ…?2ë?âÊ+;ë1â(“z²“Äû•xomi4Æ÷\nà…Ê@eI?#ZçcQZ×Ö9×<”Õ¾•Xî¢FbÑç.Öì‰P—°€H&E-0lˆß&E€ø ¡1^`‰³*àğ”?ÛôCı­;‹Ï)A•\0M‰œ\Zö÷ïwˆ]\0(^.>c3z¤ì2ñ˜¨Ô@o¸ ?ØUÛòuşèşŸáZ@ùşŸı{÷RñƒaÿÖÿ³‰?wïX°iî-xx5›‹$> È@H#œ×†‰ÄZñfs?ñ©<UÍAt\Z×ËíÌ\"´K1«#¼-?Â\nî\r˜¹!ìU„¬!Úe{á|ê¬ ™¦¢÷pßÂeİ?iÿY2¥#¤™ş?Ì°cÿÌ7–¿±ŸÊìWƒtì÷¬onÿìÀ¡aü«r|_îbá·åûş?™ú7 ¸‹°¬ßì¸ºlSâÖÚ–Ì¨À]Aƒ¶£?£ó¥ÿé#˜ÅLœr`g×‘æ6,­i)öº´pÓ?LtaâpÈvè%-m[øä†ÎXËIf\\P-ìøÂqläÎgÌ„?ã#$¾8Â~A,Kö?;øğj|™T—ñß£T¨ö¢÷%áİø·Hæ¤¶¼ugä?/åÉŸpı™µ•4´?ğM¢Cˆ8Ø\'J;Ãè¾pÍìgß?4w’R¶O²‚ÔòÜG†vÇäRñ›U¬|Ÿû†ÒÃˆ—`ì>´¿ÆM)ÖºçÿYzŸ{À\n¿eDè?>}Û³Õ{œa4påÖl¾ã?;è¼»}ÓĞ’İ¿\rğ¢	gŠ:ÅŠqÔ—Á†0]ÿ‹çŠKÀ;fu\"dƒÒş™£4ÂxWUÜ+MïJKe\'[©ÏŸ°%ú9«‰Ãœ|A˜-*1@³+İëS¼§vpÏ\\ft?oUA3–2:ºŠz[©½m™ºU¿®ÿóš±•oDOï¨Şìg‘-v	=Æs8Rˆ€‘Á(dL\"u¶`×ş¿ÿ£—?Òg2°	ƒÌ\rÃ’f?èÿÔÂÄ~6R8Ÿn¾&¨£48ƒ)‘±?Ğ…r¼?núH¾ˆO5ä ‚òÄ™;4 C%to	Í.¼q¹p¹Õ3_?‰<CûgÖ!Ë\ZO’°\nÙ9Sıò [µ\r„0yà?_	øV‰ÙÃ{s\r³„“„šîà¨à6ü<ÍV²Äî×‚¸)#?ÓõEFÀeå8§ Â!³!]û¡Jz¼ÿ/5Q?³ñSq±Èø²3 p’„äöGÉ+ƒ»¯ühçhîF\"%OG´æ¡ä÷Ëd_¦nnWœbÏ¾2Ó™üó\'ºKà?R¥5€4Ÿ?MR_Qvà¸ĞÔ?Ü¤ÂÃıî:Íò‹\Zwk†æíš\\u2_›\nJOW&óvt…À™\\ê»Æ.±7l¾@¶ƒiI˜/Á“,Ó®ƒºgctØg‹X\"”‡ı2:«9s(¯eoŸ‡ö§{æIqş•$µñCewšôÍÄFgr§Exï‹în¤ªÅD*ìà=Èû0ĞQ¬u\nuŸ,\'âˆøl~ŒK–¿„?ğíòuàŸMxÉk×W¬²x—gëfˆÔŸ?OÜét‡¯}êÔŒù÷AˆîŒõI;gh&YÇ?ä?çÇ®ƒ¥hŠÊû½’í>ÂL®lÑ>%ñÌl^¨hU‚eé>0³5¤pd&ûÂ%æM[EY,Ş ÓKîÈ?¹#Q£›cIhwæò?ê-“Pkæë1%ßèÙ?I€	•à?Áœ mE5\n¿”=ËEÀ²µ\Z?<á¾†û¬b¢ˆ‰ø•¡fr 	Œ`olØÿıñİ¿Ì¤Aq?_ó]ô,|mRèu´©2Ã„=ƒXßZØ@ánç0µa»{ºaS6{6±£›½1;ligÖ?à2á„;´jYÂ)KDb\nF‰?(ºu5\rØÎá¦qøuy.\"¢GçŠ´Ix2lUƒ08²1Û }?GRxÊğ/bo6_ú˜äF$PŞ’,«FÀÁğó·Ë™r³½q@·à\\pK¾Z#Î¡½TJp˜hT)w?îxÖ·Èå,ğ?ò¤Z¢n•³ÿÀ~CSñ\rJşop‚¿ÁşF:«z„.©3_\",‡tfÖ:ê¯ò–$0‡rmLî“‡oæ\\z36-‚¸ª?HÓ\':äØç3(J“÷hà]-¶Å?…æÊŒVŞ˜¼WF¶?¬+ôcş^î ¦‘ˆoœ#‡âRn¢-h0„‹ Â#QJ#|Q“+ù1IÅ5¨K^!Éë^;‹3H§“B ºbÑ ,$âİ;ÜÂ‡Ùc¼:¼´ï20É°gs‰e+*ƒ‹qøİ¦Š¢Ä‹şÄzt‡x¡ÍÓˆ6fG+œ)Ø–­ÌÎGªN»´£K¬{´#Ú¤µ2MÆ\0ç+óÌjç`HAåïrëá=¦¬î#åvğ¬ºzÛT?×ÖÉ|—R¤hÓ„?ùˆØÄÆ‡0ø!›¯Ç±ö#¸?`ä1Û&?t«x™Ò²àüP¬d~tïWÂtK.Ñ½´?éB9Iİäv—èŸ?i©UÀO¾‰µpØa…âÉÈB©~°O ¬ŸbÖS YŞÄË‘Éë;‰Éd~Ì£\Z?bydL¦b*ìõ’ÖÅeÁº?Õ‘†/×‡øäp¯“Ë2ëDï‹¾+…¾-ŒĞHÎèê‡¯™=¶İŸ?Ô0Q9:íŠ?Q\"ˆƒ#8¨:¶à?‚:ÓaYAa¢z_Ôbc?Ôøîßbå‰§{Ä(°ªÁ`ÔŞK—şi‰—I\\Iî±M‡–vÕÆ2ƒÑd“ÚİË˜*Kâ?±wâ¿‰%óİw¿—_›9¾-¥ŠOÄT1fşTp/Prú7º°@KD)ğ„÷J\\D,ÆNÆUK•.Õş3f gøÄÊÆÆï’0%ßä?–èœ»4\nÌ-ÿO$ËM›¯Ÿï4…ÖÚv\\İ&rù££\r¡ äyÇFÖ\\â¦€’$->„\nj¹ğË…_0&I;Ì=Å-XÏğCğ³2Æsi)%ìË„i(^å[ˆ©-ò”	hØX§e˜ñµñ†›|Ò×‘ñ\rZ©7ÂLƒ¨[„ù†p\\¬ÕÇ«€Ü¼? ´áf7<¥€÷!áscqÉpŞ\0Zé¢İ‰·€ñ˜’©k“.ö8€Ë.?â7íñócW¹$Œ·dã­npZV^/öÉ.ôfeu´°?~èo?èØ+¥ŠW:ånNqN‹¾½*H]îü‹Óh™ğÔ^#—aÏ|ĞeM€n6ÔÀ§å.V/eëşn¾ìe?BC:s÷¿À­?ğP‹O[uTCšv25T-Ï)›_|®M‘ÄE5G@òkÍ?|™çHÖUá/ğd}š†)íGÈúÆ  ŠTÇCê?ÁÛ`¬“ç{0u˜ã‰P@/À3d0§˜Mš^\'#Ö¨nu‡ƒRÊù @@„ºıs°ø„Qx(›îºşXìJ+„Ää$lÇ2ÉŠ½)/VôbÅ_àâÑÑ@üËşÑŸúÊÑ±Ák¬D\rYÅÅ¯v°Ãïì‹İD¯.~²ƒİ~Ç¸ŠÚ7|úW4N\r»ö 	4ƒUWTu…UW¢*wÔ0móÔå—`Îù>\np=:ı†şäì$Ü‘Ä¶?ÁáQ\ZX“É®ˆ;!èSÉY^ÁœÅ\"X?“ôI3Xá$ìq?Ñ0ÅQ”¢Ó?O‘§\'öğLòİ;ÔĞÄäÓ1YîÂ?ÅH…»°`$¥|X%mõ²6zl›OÊæ—|¤9Öx#ïUìºJ;®Ê»«¸!†[²ÇŸ°3¸l˜âk?å^ïÖhª‚fv?c\"f¾8‡bÖy—A±k³b¢dÅóoX¢Ö‡	¢A¬û˜pŒ!«.¢|0¨Yø\nGÅ¶¸)šµ)PG­Ê…+‘Çp\0uãµ—4×•?dCˆÆJT¯TÒãWD0g –ç8‹šÌú#<ˆõÈğ86kĞè¡,ú²¼s¤‚×rmNKÕg)½XåètÄ?Ö1‘\nä\n%êK¹©lÚì–­uåOTbÑ]qkTò‹¬1pjmj?G0óû&nHÙFa0væ—¥£´óû6èØ…æ88e0E®w-§å„.XúË?…\'SóaÒÍÆ­­˜BqËïÜz\r6´Ã80ƒ…„f^(Î( Õ“UÒzQÕ?BF?|bM0.CAD?P¦P;¯x3LUáÏß+*ã®,CeF~Ğß•Íğ_HOõÈv¡«ß»„a{«©ğ*á8¿›9!ëº@¡ƒí¡Gw‰s3ÃR‡u3.=‰/2‘GgÕ´3©àrËb(=ÆUÙzúì.¼ÉŠ¬%¡øªüÙóÙ:UÁLâİÔaeİ\'ö3³J–Á?‚ßA²_K†:ù6Ä?~7÷‚)îÍYYùîå)Ù¬ÓÎÉ«ìcĞ£ÔáòøÎNæ‡9g«G«âãÓ©{…r©$é3V¼À•<Àj-øSàN•8â­îÜE;²r4Mº-¶øš5%½Ã¬%ãh¼ñ¥Ñ5ÌÊ?5o?æäGjrt‹6˜ì|vE’p6+‹WˆÕ“Å\nO{˜Î—ŸU«º¦^˜<p†.`qV	o0?K¹Ø+tù‚‡³%Ç_EçâVFÕÕ+µ™>yoèÙt2\"û`„\rÿé‹Mj(‘›¼û7à®\r—\"XSº~q$˜9dòşzÑÂY¬ø-Göá¾}êÒiW6ïÌ\Z›…¢UÇzQ„dp¶cªÃ¹;Š ÿ?ò@Lr\nšög~\"n `-^B£ĞÚÈ?m)ˆ,mÂµ\Z²ÃWFkútªíØƒÃ7OÄKJB3…¸?i¼ä2]Áq\\Şø»!#†ÆXó‹ºë11¬Oî7‘Æîb+<ª1®ÀN©·Ô”B>Ÿ0%ğ ë‰ryeã™ ó·ğ.õ½éØPæ÷?QˆAºö|yó†Á“ä£‹Œ¸8:Ò˜~¤xÜ}t‹/W^k.D\\Q€2Æoı±³«OEE]NË”Ø£dĞ½<‡RÊAÎgF}Œ=š|‚“\r?y,|æàéèØ¯&v|_[!HL?d“Ï“4Ê/ëÃŠ_‘êÊ©ClQÁñi£wC;dôÈ¯?Ä@…çjÏša&Êœƒ\nG[½ş\0şÊa·ê[Ö%¬övÈœÏá6ËÀ÷T$ÈŠs=~\Zü˜ÍİPÄã‰e\\^Lô!øŒ«©2\'vr‡pz#ÆK½WœàiAÒˆK RÎUå€ç\0H×G·h8wFt?n¢M×?,Í± 9eß}²{Éj8*M±ËVW*¶¸„R÷pØM‡’Lá†¯ğtS¼éÆOÙb“ªñ-búa5@ß‰Zj2\\EJ\0Í@ÑÌü™—aF´„]œs…BÇã¡}\nôÆc’¡ú­\\×©³Õ·1òOS/ñef|£ÁMœÕ‘âÑÌña4°+H+ö»A1zYáw‰Xğ¢–ZŠ-3„v}=îõzÛ?Ë‚ø®TKRfü&ë»ï~ç¸QãÖb¿‘æm*l¿İh:Ó¸;v<lr~UÍh¥Œ+/\Z¬	·ëuU*Š®rü@<)d¦Ù?¤CsRuQÍ>EÃûFHşßú¿ƒcŸÚaÉé¯¾‰É>°¶Ô½”]õ,TÑŒ™v?”Ó_ù.ş\nt–¹7“¥8”o‰-\n?ø!m?g¶b¹Àô±%ö/‡.àì½;1äÆWñÁÀØ;‹ô/Nğ=C\rZÎ8Ş«ó½º½‡Ú¥¿bÊ¤¹í-\\ÁôCÁú=gºİ”Ò0yáÿ«Ä^Rä˜æˆ¶=jbë°°™xyfU)t¸–<É#µ]¡èv¸/=á¤ê2Ú?‚…¢ïpEßÙåš~JUcº£¸xB87°1LşŒú¤¦mr—I¦Ò›ÒF?Ê-˜|tÂ’zÚ?ÏİS¼òceXÄ”«‘48Ù$Å^¦høR%]Á³áL~ºFVIñÇø)u\'sÑìş,\\B`¨MhĞ<?ü!£ú¤!B5eû¡É3<ìŞvìxùb‡Ú×ÊT	O˜Ğø³k¡Öüî°¨!ÔÉ¢t r^$µ²·õ†î.\'“L·Œbä:`*döuğq´s†ß¶lfÈ-ÕIœtQ˜r*¦“CV¼l}=é\"ÍÀgfŒ´oLÊH%ÿã˜Óä´îõSùïíßæÜÄŸ÷ªÏpåí™©?òzâö+X?¸ñ^]pÈ?Ï)ˆ¯$¸rõ•ä=\"aWò+~Ám£)Üà#Å*Œ?Å;åy8a¢:^\'Ï«¡÷†.oO6œ3ñÁÏ@vcşsáù/L–,B–a¯y0]?Qú4?¸9?[ôáo{¿‹)Ïˆ: ¼ÂĞ>]á?dòğ“ã8¢TĞ7^xÈş#B_`şFÊ,\"\\zc?§ò\ndjÇ¥4_GåuÖ±‡ñDÇ.O·­\\ÃÌ½øjcúµêÔv*cá¸Å·®ÙÀk</|ƒ);a„.ØA#Š½ajSTlÑ	võ®í¸¥í²7>u@­@8øµVØøåÓ„Å…h©OÙûÙrtnÏ@§ô¸iô·b@ò¾v>;Şnwé‰ZHÁüp\"ŠSGuŞûgâÀŸvÑ*Ş\\À‘?¹xÙw>ì©;!?R¤òÊi›¯>¸ ªÆrÆøz5opÈ(?ıíãÿÒßÁğşg÷h‹®å„+lÊ?4ƒJ¬Ÿ¢/úâ#ø–a‰ú‚j)Ìx9şÙTã\0¯=?şâ>ş0ìã¿Ş4jª+já¿øŸõµ?òë+€h°\0Ïğ#}?#ùáá>ıİƒş{‡ì?€2	V½èkè\Zşî3Œö´Ï_Lƒ\0o šª­0õn*›ÙÇÿ+ß¼Wx!ç?ıß{`ÇìA¬i\\´âtÏKTI/?:‡û€\r^6¤Z¬L-Š«) ºcŠ„•>`X’hEFÏ~a]p{û…7Ûî¥gXèêÏÿL?ƒªïàI);ŒVS¾_EGo>\n†ƒáàío÷Ñ‡B\'ÅÉa®y\n¢ÔjÄ/Äb„µØŞ§µÉ¿9ş…ªîÊL¸Ç¿Š½\Z?§V£ÆèYÔà\r2Yphdƒ¼h£ŸİåÆí\"Üîoa{:]uDíåtÜ±W;.\'NÔ%¹“‰¸÷fk‹ğ×ÿ]œZ¦çAây˜xŞK<ï\'Ï‡‰ç{ğ<ĞQro$JøèïûB½i§–½sœ1–ˆ4¢r[mÁ÷cFŠÌòÄïŸĞ×hu’Ğ„ÓyÊÂ;ÆŒÓ²fA2åë%³‡#bÖğ×2¹	·Ï½mp‡Œ•\\AÔØOóDsÇ0cl‰éoqs\"@BÕxmêbêŒØG;˜R:´·\'Î^lC¸bÌ\\û³ç^ÀÁ<ÖH,Å8eë#„s€§?ìCHíª\"á•h:f¸ÜØÒõV ~_µÜ\\ÿö¥¦*8å–šv¡t:iÚGş\"±öÓõH:ÿyM/§Tì?Òí7}ÿópxo¸—Êÿ¿¿·wkÿmâ?u÷µ%ï?‹\"\n/€·Ğ?Ç`[,a/€)eãIœÉDÌv¿w0\0;æŸo¿wşÿá‡é¡cw\Zş?&^o¶ô½Şè¶²`c?}?M[[İ7^8ê¾`¶Ï¸û$˜»º$5ºÇÌ¢èºƒı~÷Şüÿi÷A¿ûêøİıûºCkË‰ì7¬sètxhöî<<¸Iÿï÷`\\Œ\'-¼3Ïw¦ØáS…Ï=“\0˜fà—<´¶¶_{§lÜÇŸœÅggÌ0…çÀş?Ä¤z£PŒcÛ¢£FóØB>©pgÜŸéò¿¦[foûğ}¿“üßm‘,’¨\ZlUö?­hÀÿ\'‹Êÿ°hĞ$\nMµŒm{l†ªaªØUü—C©ÿıKÔ’¨ÚkDUbŞ”	Í(JL{¹3€HZ’ˆš™¨j¿1ªT(#6Ô2à8¿(ª«-µL3kåõ(QuĞ?WeÒKßTdÀ‹‘Wå‘Á«â+ÖJe¡ê°•ØVQ‚B3?¸ªºW\rUF©¥vh.ÊQ5&góEU÷«RU¹yI±¯+6ª5›7€Z¥ÇÒ¨z°	808š‘É•¡½ÚÍY¦…¼jĞL]ÏÀUqQrI&¸l>Ôa@rí\"#®}½íÉªXd`ìUxıÚŠb\\e)ì›/ª‚«MÅ¸j¦±·_¤®¯ü¢ÍãjÿÚ¬Á*¸ÚäÆ¸:¸6¸JÊô?±hópÅ¸j®´—_;³¼ù¢Wµö¿ Fc\\UVÛÿr?qÕLoÿ+`4ö‡~™nö61\ZãêúèíqQ±Îp%rp8¼6¸Š’Wt•¸Ú»†¸º¦¼}ØÌÕŞfQ\\]\r]]½ıºÅ¸j¦·Ç?[±ôG^Kg:ıŒıšT-c[=ö“Í«ÿ3•cŒ«fz{rt¼–‘«ÅZ[	ÈµŒì¾\\mãêŠôöòƒÑ]ª¥‹É¢Aº¨*®Öª·§æ/y¹‰7ĞUñ‡)ê3®úJ¸Ú[“ŞÃ­,¥úÌ)U¤ã ô‡úG¼(ÙZÉõãjsñ1?ğ^\\´6 b\\]ûu-ŠqÕ†¿½ÜÄo¾¨¨åqÕ\\o§î\r¥¨¨Áìl²(ÆUÓC‘sQRr­ÿÃpÕŞ ìÌÁ(r0.ÒÆ—Z$9¨\'€04Ÿ?¼Æ¶Êáª©¿=î\'%ˆÊ”V”Ô+q¢¸5m*4³\'U4Ğ@­0ÆWÍõö-”[™DÔO¶¥¢ÆÒq?À‚6;:lZ[	‚,‹«6ôö?c‘AlĞã&‹âHÑZz»‘{¤peÀ¨Vd@_í¢òj„‘İçÉçW•ôv£IĞ6ä5‹jÍq@™¸º2½İ(Ô,Ì¨ ”ã?í?\Zãjsq2åH-WƒFmµ€«ëãoo»H´yYWkĞÛÛ+Ê¤«Mãª-ûu,Ê´òkeâª½8™ÔÄñš”\rÃRJ%jĞUşöâùKÙ8ºÅa0B*š7‘1åqÕ–ŞnÀ•Rd4h\Zˆ³ZEFı£®šûÛk‘ÚJñâ-&?âeY[NÄ¸jêo$qÿ4Î_bë¦|[†•ª|˜µ*®ÚÒÛ?´`ÀB‚¡¤£µer.y”‘&ùÆ_.®šŸHM0ƒ¼É,J¤?Eë#Ì\'¢Zì¾<®Úñ·WáWÍĞ×¾h,?«fz{Ã¼RFnœŠ\ZcŒqÕ\\o¯)ËMüµ¢«æzûzÆ7Ğ›Ï§Ñ\ráêJãÛ?««x\rn¶ùWW\Zß¾V\\Ú\Z$ÛJ1º<\\¶\'³?1˜Ô‡™5.8£JRW×\'N¦@¦®áÃª¸j¦·qû´°&5¢*®šûÛÕ	M€™’JkS¹Œ \ZjÕà}1®6¯·k:ƒ²nTÕ ¨H37ˆ;¥ÔæWmøÛ‹ÅYM•+£(Áœj·UWí?KUŠtæC®ÕjØVÅæ«âª=½=›_é‹ÄP”ÂBƒ¢<Ùp\r6ÓÛ×CmµIWÍóÉ$&«ydâ½F[Å VÄÕ½æùdr8íuÃU­1Æ¸j®·WåWµÆgœŠì¢µğ«{íéímğ˜\Z]CQU\\5ÕÛ3˜€\nÙu)jÌ¯Úˆ“É\0`sE)¡§+ûòÃA}ÕpÕÜß®Ã^éª}¨A>H%»Èğ¡±y#\n¨Uqu½’@®¹¨á\Zl~.U<h,Zäsmõ¿mç~EUqÕ\\oÏÄUJÂUõ3V}qQm\\5÷·ëL¥u§BMŠIaµ®î·©·7GLN[ıö‹ªâª=½]m€®Š–e!]ÕYƒ÷›ëík¦+u\"š(ÍùÕıÖõö0SãËkK}o_vQ&Ël¾®ÚJİ>Ğ!×ház•ÇÕ\Zõö+,Z?lOoo>˜rEÆ!±Ğ6]­EooP¤ş·BQ‚ÄˆäôXWkÑÛËQZH}˜j¾,\\ÕpÕNşöÍâêŠxûƒÖõöøm™¢b\\©\ZŞ?–_”	j9\\­Áßnß5*j€«ÍÄÉŠŒƒ)¿nŒ¢¿q×åWÚÓÛ‹sÅE™ –ÅUe½½†Tê+ŸT\"ÿkQãêZŸK]s‘A1ËÅU[ñíEE¢ø:áª\\QŒ«MäoÏ(JI¥—†YÖt5?r•¼AúÃÚ¼½=½½F‘Æèâ‰¾rªnF‘±­F¼ı\nãÛ‹éªöTdĞh3ıjĞo/dŞÂ1¥–„J©(Õ|ª-c?‘u…îÅêFæúÊäX©¶Z¥¬6¦¦ ĞG]TÔOØ?‘•Bi»–O%d]ÙİK*\Z²äW’<ÌE­<“Ø´yÍ\\¿Z-YÍ?î\rŠÖ*‹‹Ê?QAÖZ¢ÜËy‹%?l«(E3•‘Õf\Z÷â¢Ô‡FĞ×RT{I+ÈjrĞ¬(g†? _XdøĞØ|\\X?²Ö?R…ñz$k%d]‘SBbQ¨ÿ]wQUd5¼:µ²n 4ÜD\r¾\Z<«ñ2¼ªËS7@YÆ¶R=\ZáÊBÖŞ*\07RCÓ–jËØc?ıaªHAÖ¥…Tç9¡âÇ¬´ÕfQPd]™ÿ} üOÁ__E–aQ(âEÄ–ùa)d­%1¤>HSQğÒV/ÒX3d]‘~íJAUU\nYWxSÕ1÷3‹ŒJ?QOP;©¬+ÓàËÓŒF\rå‹ô•…å*ÈºªKTË’QYIÕJµeì±\"²®Hƒ¿<K•	Ù2SAÖ•¦uÏ?M©¢r”•j¾|?y *ÈjGƒOA``¤eŠÊ)µ›7‚ZYmX½¹E?ÕTƒ¬eê‹Šj5ŸjdUÒàãîŠy\rr¿®E\n²®0†æ†)ÈºÒ$‘7¢HAÖ•f‰¼E1²Öu?êT¤ ëúä‰¼®E\n²n/T­€¬+ôÁß?\"Y_îÕLk@Ö_9¾2²¾äË™ZGÖ­_Y·\Z|dİjğå‘UïbÕ¿T‘‚¬[\r¾²n5ø\nÈºÕà+ ëVƒ¯€¬[\r¾²n5ø\nÈºÕà+ ëVƒ¯€¬[\r¾<²Z¸bõK/R?u«ÁW@Ö­_Y·\Z|dİjğ?u«ÁW@Ö­_Y·\Z|dİjğ?u«Á—GVk—­~¹E\n²ÚĞàÛ	á/_”8;Ñ Z>.0Œ‘×R?UAƒÏÎ7—×ˆ\n¾®E\n²ÚÈ#Y¾(El)ú+Š0N˜‹Ösh …Wsğg¤?ípHê ø°ŸYËĞ–¡Çö¤ +_ƒO° 9©Æ\"?@’³|eEM)KÓà\r¥…¡í¼5{4ëÄ@3ÙEUØkdµwçª6æÊ,RíÃü¢Œƒv?>4!+[ƒ/gm,ß?\"Y\\ƒ¯‰õ¿Ö2lxíê—€¬_Öµ¬A#ÊÒEÌ ‚ë\\TD\n²†?¤aUY¼5³(5©ólå7Ô’ÈºN>xÃhj°Í\"Yûê2LÌó-e%?¥hğ5–ø_Y}ğ)o?5…Åu-R?u›\r>şÌĞã@CÖ_ÌŸÉjøS&QAV[÷¯f@?(\Z˜ŠòG“j~`**Ãqó¬°Ö`Êµ‹dûà•UŸÄ ¥N¼RKWDÌBmÓEIºªƒ¬ZQ4m[jQäòé¼\r“hh«?eXIƒ/¯|A+SAÖ_.ŠÆ %&¦:!?dUŠ¢i“²nâ2¼²(škƒ,£‚aFV-\r>_@&)Ğ$ “’i?ÒPµeÕÒà×\"\r5ŸBV’([Y†5øÔt¥ØX²àº1øÊE1²ª]ÅºV¿½®”Uı.V\Zj)U7Y­ûà„Z~‘¡­†Uÿ¬Dedí5Ü¾Ïœg?\Z29[C”Vü°)²¤_<À[d¡ol¨üÿË ë0s“õ–²RÈº§ïî$8(hH¯qí‹Ê.•,d%4ø?õşŒm§ ˆ?1LjßTÔ&f&¢aµ\"²ÚòÁßdd%B_R1²†ñ?¬T!ÑĞ-eiÈ\Z4Z†FĞ\nSiY VFV³“¬_²Œ!ÉFAÖ^#ÕáË@–RËP¤ k?Yş„¿2²\ZÚ†>¼ò¢æÈ:lhş¥?u¯µ`6¥H‡µ™mÓ°H¢˜¯ä#«Y?I&?ÏÒÆôOMEåÚÊ°F›\"ëAcçŸaRk¡A%î¢\"•k$È¨¸¨®!=w²\ZTñDüÅ?5hì)-^†cPyË°Üìç#kØØSúBÖnHu÷×FV¶‹¬²Ö?‹f <&@×x«Ê¸e[†Ñ¤ŠR\Zš7\0Qs@\n²²}ğe¥á@«×Œ@m© $¤a?ÙcE *#+áƒ×¾`¥æ9z-Ì¬±hMËğş:”Ò+/Z²4’†×3CÖ0[ƒ¿EV\nYÍ4øöA7ô¸æ¢\nÈjÓ¯ˆ˜Í?¹²3– Y•O²ªô?Ïìò(K´¡Á™ÃŠ›×€P³™H¹\"YµâàS”YMZ[1ª˜ÉD|\n¥mUDV-\r¾9²D?²ÔÏµd¤,ü\\Æ9HX[)­v\'«\r)>mäæ_²*ÅÁç0•b9÷ «’¾,²Ê´u‘ÕÌŸYë:é8®?¬zw²nŠÁ«­ğËePiSd]a>økƒ¬ÄT\'–Š‚¬Š\Z¼ºæ52Jqó/‘²jå¢iw¦(›Ï`İ9~½ÈºÂ“¬×Y†u¡ˆQYM}ğ©VWJ¯YùcT?UIƒ7¢!5f}²¾$dÕ>Éª5@V¿ğÃ¶Š?‘U9Š&T1?|1È*Oí\'j%ŠéZr?èE\Z²ÌNMr-•.2´eì±\Z²Z»“Õ¸2¿4dÕÒà›ó¬äX$ëÎ@ƒQOmZTY•4øö¤áÍDÖMÔàË ¡ü¼V@Ve\r¾mix“?UYƒW™dŸÎoëæ!«©_–Œ¾dµ‘MÒÀ Êğ¬DÑ £ÿÏ\\´y¸dUÔàSkN¦¦«¼4Ô¨T¥K\\¿ı¢ŠÈÊ×à\rÜ\\EDVQYÕA­»ÆÀ?½&8ndÕ»“5Å R”õe\"«f.š¦Òğf\"«©_S\ZŞLd]‘-?¥ò{íCMğ(ÈZ‹^—;¦¢$H¥?¬^¤‘QBş\Z§z µ¦\"«¦_TÔâ¯¼È²ş<²¬³ñ‹À?lúç¥çøÑwnÿ`ÿ¯µ5<8ìX[¬æÖöwpÀş£T{æDÒÂûh¡6ÂZøFoógçäÜí‰7uís\'´ı ²O]×·£ÅÒ9‘;îÁ´Y»ı³Ş?gãî w¿·÷l<astÖ;o¿?A¿¸¿oÿÍ¶í½Aÿİ? güs?‰JûŞp¿ß?öûÛ÷öïıÍî·JúÏ2Œ›°ÿv\Z,#w:Í¬7v?oœMÿ±şîMü±;±?¾|öâİÛ“—ühı?x¾«–ÙªbÍ?£ùtÂ_Ë½ŒÜ…oo?İf|âï®?ö&°º­-\\Şìÿ}î:cwAK}\0o°?Ò:ö™ë»Xêö2ôü3kët<‰öú³»½À·û=¦p®ì:¾ıŞùcü‡,œÍš›F?ÿ?‰×›-}¯7úÃÚš,‚™Í¾Ç¦­­î/u_x—î¸û$˜»ºoƒÅÌ™v??ìºƒı~÷Şüÿi÷A¿ûêøİıûºCkË‰ì7¬sètxhöî<<¸	‚î÷`\\®,¼3Ïw¦Øáã^ç¬6Öùt\Z\\À¯Q0_±ZçÑCkkûµwÊÆ}üÉY|vÆl\0Sxş‡çO\0¤È…bÛ2=†h4]]{ûlÜ;ß¶š?öÈ81ÊÙÿšç?±ş§½Qû4–»ş‡ÃşáŞArıïïíİ®ÿMüis±Ö\\«3X«\\«3wì-gİE×§ÕÚ*+uÔ½ßïza ¬Ô“¥‹]Úl¥>x¸÷àáğ_©W°TiÁz?êIkôµ³8sAEúíwT¦„bjô;üÕâtlíb\'åñÒ¬‡„¡¥š9cAêC£†n[ÃÎ?Òkö;ê_Ktşb5$vŒ®„:bú9	{¸øc·	êÊø¤FAšvŒ¾ƒzØIØÜs\rB³2ºMaÇh¡ô‚T?ì^$vŒÎ‚zÅØ)FF¢?\\ì¤†šò $jäbÂq¯ÉÊjVP; ã†Ş:R.°ø¿æƒƒtİ?JìwğZ“è‰ƒx®8«m¬;Æ-»–dÖ Y .ùI«5eRr/??AkÊrù¬\"Ç\\ >^‘®¦§jgN*”eÃ/ˆÑcÜˆÛPAz®¾ FOkúrí‚x­\\Côì_áâÊ@ÏM“R£çà\ZğkL=uUæŒ¡5›µëR Ü|»6Şsƒ¨\\+v\r$×µC rgÃ•K®kˆ@%!îµFÏ!PÉ6vıû5’\\j¨ëbT\\}?rNî\Z çú²fs¢§«å×‰z®Rk¾¶1zÚp4\'/ƒ¸F‚³Òm\ZFWà Y 5:€j%?úZs{c­ˆÑM¢g£Zsb$§?úIÓ‚½õñ£g3ZsÎA<ÃÌ§v22…?‰¾ôÇ†è1\'e*]?\Z‰2øøgªFj¬Y5Ê¨ä\"I2n6•¥¢zZÌXKA?†‚–»?Ñs•¾æk[£§™¯ùŠT”\ZpÔDO¶ÖœÁH*ÔÂùÕÄèi1:CGP©ƒä*Ây†äR˜QqAyô4Óš\Z\\Bb$°1Èú$ÕhB“Ìh#…â,À*.FO­9%PU\r-] >I‰¡<œ‹”6Rè1 0CY¯Œ&ZsBgQ±U²ÀĞÆÀ<ÖDAbèl€<­Ù ^4#êëRGVÖš$[_k’ôÅYA¤Æè© 5§´û¶\0o^P†å.=ëÖš8?R\'Á¼?ÍÈï\rÚBk ÇèYw„F¥•’iÂWn´-ô\\¥¯¹­‚\Z»ù«-FOËZsÓ‚5¸şjÄèi-¨ùJZvyÄèi#B#5Ñ‰‚–u?ÄÂPÍ\rĞÓÌ×œï-ÔUÜu¤º5\0V=M}ÍùDm0±6QĞ”Å§Mšøš–r™‰6ä[Û•×Rsö£§‰¯¹ô¤œùŸ”[m?VFOs­9ÅxS¤ éÄÚ\'ƒd†‚RH\r¾¸ zêkÍENÕÖAª—\"q¸êi¦5\' hÀ&R5š‰²¶ĞS_kÎá€¡â‚6d[-ô4Ñš›Ï|m¬•zšhÍm¢§2æ‘dÂ)Ò\0=?k^Ëğºl®ƒ?Ç5o=ÅÌh?Óh|\ny\rqÍùlµ¢Nœª‘14Š[à=‡?ĞhÃ›³	?PŒ}Íkœù¦X=M|Ím™¤5Ôœb·Âš×¢5kèèƒà¤JAªF~AÜq6?¶b±6ó5¯Á–iÈ5Ñk£§¾ÖÜ½´­9ÇÎ­ŒÍhÍ©\Zk)hß¨8¬¯5«0hp¶L\ne”n[3*ëkÍkñ÷´CNM5£8;M}­ù\Z£§òX2Ñ³_sÅ¡Õ’\\š¤§‡æÔS_knWÈ¬“;5@O¾f¥Æ ùÉ´(Øï5?Ğ0pÀX­dÊGå“Ö$×½&¾fôlÀ¢?5Ñı‚‚Ô\'†F?øjcq5Ñš¯mA{‚½®Ö¬5 ‚„1ÄÄ?] ÒGVA¿JA#ô4ÑšP¨øÈ*ĞÆšQ à+³@‘ã™m §‰¯y-ædó‚`õÑs¿\r­¹	z®·Ö|£\Zmh:±€ ¥5¨\n+¡§‰Ö¼6ê)#¶+t[yp1z6êk6?½ªèXquãÌ_ï©«5«Ê…2I©±^—‚šèYƒÖ|ÕmROıÌsñÃ \\AF¢mĞ‰‹Ík¤µærƒÆêß¬„ø	×FO«Zs¢@Dæ•9›ŠÚX«äª«5·³–j®”?±æ-úšSèÉ,H?}>zT¼›”CAkûƒJZóµt–-¨ Êbô´ákÎ*HÍ|¹‘¤ÈÉP`d¼ÅT\\=¥µfÕAyõ¤P©Àz9ôäiÍù\"¤¹€¸¶1z®ÙiÀMdØCFôäiÍ?$U#…?MÜ¸‚=¾ã¤ÈÈ	ç?R#S\\Ç.éƒ6´æJ	ô¨šbŞàó\nm´d’>Øp\\syQ?IdôR=ƒ~?ÍÔæİ§VF-òI´Ñâê\Zô7œ{®†‹1!fÊé£­ÑO³Ğæ	>`PœS6Tj(EŸÔ*¨?ŸÍÜtR¼¾úetE8?©4ñ‰‚Ÿúa\Z×G|?¾âàü4?n.*0Ìlå6jZ-ñŸæ©4R`Áe€|ÃåÑ4q\n~ZÌÛ\\¾ ĞAÇH©6…?Š¢†ôÓ4]¢Æu(hu}mPe×@Äc¬­ ~Ú»&°LÁÍ“_ƒë¯?×@G{ëk³®‰~mdÌBÉ?¾)ğŠíÓ’\n~6šˆ®\nı˜g¶VAeHülØ÷\\Ùÿ“ÃL6ÂZLàœ*0*\"kÔ´ÅÒ~6ê}^‹¸ªÂªãgÃŸT½JY*Ë*¨‹Ÿ?êÏñC]eØX şÕª9~6{a`-rIC¢F™FëãgÃ™5Š†Òh*è(§ÿh‹V™?ÖŸ3ötK!ÅL´FÉFËöR\ZR?•ôçb¿¯á“ü‚ÍµQ?a+øÉİø+˜ñSA6¸ÃRK¡hmáZFĞKá§´şœ0•2$o3½õº(øÙpôÆÍ(Pğs•×m_Û?×û¾í+ÇOÃ«¿Ğ?7áîÀ«ÄÏíå?ùøÙ°ÿùf(øù.BY\'~ş‚±Ï•ğóe\\…²>üÜêÏùø¹ÕŸóñs«?çâ§ú%‚…?·ús>~nõç|üÜêÏùø¹ÕŸóñs«?çãçVÎÇÏ­şœ?Ÿ[ı9?·ús.~\Z]\'øÅ(ø¹ÕŸóñs«?çãçVÎÇÏ­şœ?Ÿ[ı9?·ús>~nõç|üÜêÏùø¹ÕŸsñ³‹o~?‚ŸfúsÓÈmCAfº?&Òñ£R`håİ*øÉÒŸËÅe7;Ftmü4¹%¥VÁ@/ÈöÔë¥½øğ†—jC‰²Š$r‹¤jTşÄĞmó±(øÉÓŸ\r1ç)@	êH4º©‚é\'¡?yd·Ùà°¶ÖF\nĞJÇ)×Àü´ª?7?]Úhô­!LÁO¶şœÖ†‚6Pz-üHıÙ0úÛõ5hpÅàMÃO¬QuüTúÉş\"…UHr>¹.Es­(øÖ–_9b´¨ fcË× ×,ßÜ2¸–‚æck£@ÁÏ~mşü× E®xÈş¯?Ÿ\nÉŸkh\07´@ÁÏ_)ûs‘À—m(øùëøŸ‹•—??Mï\ZLÔ0ä/É»Ñ\nY?f¨Ü§éú*¸lPëÄ¨ê~‘\n~*ûŸ³9ƒø™*HĞO¢ÑÖZ¥ŸÊñ•dsª?æ?~ö\'ùöW=üdéÏ±TP\0­ŒZ àç¯¿aç² &8?Mâ7j(Œ7n}m8~ã\nñ“š¸l^¡à§¹ş¬	´ JAu‚©%ĞQ³ EÃGÁOeı¹‰üÊ´[6XÛ³OsoL(3‰iR¯û¶Zã§Áµƒ5èúæÑOµ{Sm¹Œ®/~Úğ?§\0Í’,†cµ—3òZ}üì5Ø_NÕ0r¨š«ğÉ:ñ³Ÿé_5p—¿ ~¸ş\\S	ıòñs˜¹qK?€M®ä3È?ëRĞ¢üº¿‘øŸÍà§k(‰Ÿ¦şç›ƒÃ\\\'V‹¬!ñ3Œï4°›[ú&ï¬¥6AG&›´Æ†\rî¼yø)³à	üìÕ–ï7?¥\'_ÁO‹ñ_$~\Zè?6˜úä\n\nŒ€ÕÅ?¢?kä\"~–Y__2~î5^_Ù(Íøk(Pº€^?M®ïnŠµ…ëŠŸ\rôŸ ƒÂ‚~\Zü\nH3úêXjâgP_şkà§¾ş¬òÆÙMvA\n°&ø6ğşğ³§â\'ÑÉ-~†ƒlıù?€Ÿ?æß0%5)¶—¿ğ›³ã<üdûŸ‹åWôclc öÒïh€eäT}üÜ«ÂŸ?êàU/§õ®¯úşçë‚õâçAmùu]Ğ±Vü³õç[ü\0~êêÏƒú‘© o¥Ÿ)qÕo¿ .~êúŸU\'?†Ò5Œ­ü¤æ:{Zü´z~°¬Ã#«@uv}’·àé\ZÊ‚S)İ<?•ãŸ«ºhmhm$\0ÍJê“¦•çZÁOeı¹	~RœÓÀ[ãZÊ\'F¦¸\\‚?\'\Z­‡ŸŠñÏt¤D^\\7?õãŸkÉÕ‡Ÿ\nşg•w–Ò?¾ü<¨½ÿe¬q\nRFoüT¿p½ü¹‘¸ª°ÎKãgÃùŸ¯?‰™Œ‹´%™ÄO“üÏş“oòß@ú©¬?oj}É‚„SFl?>?x…ø)Iù‰‚ŸVÏüG?MÎ®•ÿ\\üTÔŸó‰tPØÆ&T¦VõŸJñt(‹Ù/?ÅñÏĞQ¢†V0H$ôze9\r¤Zíƒ¾ê™©\\0Èì¥~Z¸0Å¾(üÔÈ¿¡TÒ?z9”øÃ’»rAülT¾?ø¹şús\rt´ÇŸŞ?˜ AİtüTÔŸ,ıñËÅOı95úbtÜ|ü¬;]‘Á‘(H™ké¥~šä¯[«üê·WĞ?•Î³ãbüd £y?Brª†Ø?Õïl\"ßo ~*êÏÍä×\rÄOı¹¹üº?øÙ¨ş¼6qÀO??‰m®Ö©à§eÿs\n?Z?Ñ3“ÿI•môEşç¼©UğSINI§–õëR`YYÖÙøEàG6ıóÚYœ¹Ü¹ıƒı¿ÖÖğà°cm±š[÷Ùß<(Õ9‘£´ğ>Z¨?°¾ÑÛ<Â	99÷B{âM]ûÜ	m?ˆìS×õíh±ôGNä{0SÖßÖÿçlÜôî÷öï?\'Êiï¼ı>ış!³mÿfÛöŞ ?ÿîĞ3ş¹ÇûŞp¿ß?öûÛ÷†³ûíƒ’ş³#¶6ì¿?ËÈ?N3ë?İÏ›\0gÓ¬¿{ìNì?/Ÿ½x÷öäõÇ?ZgïªeöÀ‚ªXóãh>]†ğ×r/#wáÛÛO·ÙJù»ë?½	Ğ·µ…ÎşïØç®3vDì“\0V‡\r”Ö±Ï\\ß]\0±ÛËĞóÏ¬­Óñ$\nØëÏî\"ôßî÷ìÓ•ıOÇ·ß;Œÿğ‚…Ó±YkÓ(ğÿ1ñz³¥ïõFX[“E0³ÙçØ²µÕ?yá¨;ñ.İqwæ½å¬»èúÁbæL»İÁaw°ßïŞ;€ÿ?º÷û]/îß?xĞZ[NdŸ,]ìÒ>´î=x8¼©Cî÷,•kïÌó?)öwÁVïy03øYßÓip¿FÁ|Åj?G­­í×Ş)õñ\'gñÙ3ø§ğüÏŸ\0<‘7\nÅ0¶-\\ôÍşhº»ööÙ¸w¾m	$Ì‘qZş”s!şÍšÿÄúŸ?öF­ÓXîúá`W¼ş‡°şîíİ®ÿMü¹^‹õ”-¥x©†v{İû]FBøwÔ½—Z¯çKZ¯÷íÁŞÃƒı‡b½n½\râ¥hÃŠÃõ:	–ş˜=ÚQb5÷?/\nX†#P¢hé½A¦ò„Aâÿ·ßQQz–Á\n¾‘?qâ¤5HS&5EÚ°ÉP³3Ñ²2Ø™J8úÀĞÏ5zk\Z?Á0´„U5È|ä¾5X‰ù3qõÓ2XrZ†5ÚÛÔtš‹”òŞåQ`6AÊL/«_8 ¼rºÍ9å•È~kôXÄ2ì5T?qşŠ´¶2ldM˜¶JäâL­Uƒ »åÖå€ŞüBNœb>\nÒSŒ¹–‹›*1 ƒûİ4ƒä£Öm?ù˜Å\\‹ÇWüXÀ¶&U¡˜ªè±/ó)ß0¢5òí?rÅa3Ôå“¤ù1Ÿ*=Æ#2)->?¨¥ÇxD&m¡åÇxÙe=¶:¢ıõR]ñˆZB]<¢ƒÍ¬#>„ÄãZæÈ¤0$‹¡j	ÏíÌQ)?áŒW¹ab3¼níãUrş^¥1Şâx•,l××½Pi¼J^Œ«æŞmóº8Áz¹·ùq-#Ú»jyÔ‰*Ñ<WÍëZŸ£5ë{ŒGTBg?è{K}ÀÇ¸İ~†%Ñ”ò­Ö²:éÇ| ã•Ğš\0YiøíŒ¨m?!ı(õ8H?¦?S<¢J:ƒÖriRÉ`}æG\r90»‡Kê±ÉÂs_›\Z‚émÆ£`¢ßr#ZÇ„Ù?-u?hÍ~†?=Æ#ªçghQ¦Tj¹ÌˆJéøÕÕÏ‘y_Â€º¾ºBµG»Nğºº•S?eFTRgĞf_c_ñüôGSå~VåAnåJ#*©3Y\'Á0ˆEåÛl¨Rì:æĞVÜ?ìHy[vDut†?\n¤ºŠÒ?•ûúÉÇÄ·•FTRg0CÉY¨„«ú?ñli?CyçÓUŞc1»®ª3˜¯fqïú`ä=V@N¹µ 3Ä\\#\rU¾jjB¢©Ò`Ä#jeo\"ÏGT_ë‹G´f?C9l´Àsâ•Ôê>ÏQKÅ#ªàgØÈc}#Dö&ˆÕKs³<f®>¤ËÎQ}?AS²œ­`PP9¡ÕW–Gõö&´!ÛšõTiÑÅANuö&*Ì‘ø*Á®5ÍM¶l º\Z#ªãgÈQjFÊ1Wn…êL‡ìê0Š”ĞÔ Ê[8†YÈ,\ZQu?Á bŒoKÍà\Zæ¨ÎPf•\Zà:x?éèYµ9Ê$Â~Æc¯ÓE=İû ÎP‰×m|êÄ3´äúÖQ+{)ÊÉ#³õ4?¨•x†&#2T.©šGtXAgÈ^ûÙı&X_±?op,T“G‡?÷&âÉ°RêT®ô?¨„ÎĞ:ëÎo¹Õñ3äñ:Ã*/«<\\?¨?A‚Pâ´Ç¼·\Z1$°Q‡{Öñ3Ô—G†uTúÛ²#ª®3ª¨ùğ+ÍÑºt†¾ş¶Òc3ª«®34Eûºç¨ºÎ?±²Ñn~é)S:*9¢{ÕıQi¨âµíg(dI^—¡¨?¨ºŸacìº÷¾×ØÏ÷Ùoù±æ:ª¿7‘è¨…ÇV¸÷½z~†Ú²Já¹Ÿû8Ğ[ÎZ¡eGTOgØØc\rª«®3Ä½–dæõt†Ôò#Êl:¢:~mÁ6A{Ñêp†ûMt†’#2Tî×x,;¢ué¥É·\n+0Î‘™ãÕÓšÌQŒúv]oİoÛÏ?\"•2xnuU×ì+¡¿şÇ2#jIg¸F¼®¾ÎP\Z*zìg<&`n>Gut†¦Ìm?ó¨U}Vâut­£¢IIà91)ZåvFTÏÏ?Of	˜+ÑUcª{ĞØÏãQyÌ‚Êİ×¢{?(™@{âíú«òºÕ÷&Œ24ûÑ@9	 KÊß²#*¥3pµ‘ÇZsTRgÈæWMT‹?ñˆÖÙä1_xe¨”Î00Í‘R¹h¥l`øñˆ\ZçgÈ~ÖbÏW2D[%ÎPBg(–\Z‰Ç¼UVKeª4Gíçg(3¢|äd©¥F4è×\rhÈ~¬@Xë »A¿ıÓ–E³dp»³TÒÕ 5M?µCÒ L±Ê¬ÊÅ\ZH©!µŸ×i ‡\\?´´Y*5á‰·Ê?J(WÉğ´1”œ¥Ò\'.ó«a–J~›ı˜Xx¥‡T7MCòJ²Ç\n?¥	¯z†J²Eå$inaxLT.Æ]z–J9Z£¥J?u	¯mí!cZòåR«ìÁ˜²é?®–ã\r6¯=TƒÖTé!µ?¬¡é,¾í—yT†´.í¡Ù´TgñÊ?Ú>zYn–J!>ñ˜ß¯2¤öıyC2j¢ù•«¯¥º)²I8ò*g<*¤UgHm{ZåÚÆ¥U8¤u¤yªtßø˜¯g\ri=ÚCe®mzìËÊ•†ÔzrÈ&ÓRômÉ!µ­=¬añd=jèP†´>í!¬,~h?K5ØÃ°¤öP?/\'Öt¥·ÆÊU×R?M‹«{,5¤RÚÃ İtyùœ·ÂÛYVRémêÍÄy\r`åMøz•!µ¿oqåCZsbé«Ò•f–^Ë?Ê¦‰¼ö?Ê?6?\'rÓCúâEjfŠ¼~?Ê?®<íSûCºÆ1u‡tİ?µ0¤/O{¨•.ò:>*Cúò´‡ò	#¯ù£2¤/O{h#eäµxT†ôåiWŸ4²ı!}yÚÃµKÙÂ?¾<í¡VâÈëø¨éËÓj¥¼?Ê?¾<í¡AòÈëõ¨éËÓj¥?¼?Ê?¾<í¡VÉëø¨éËÓÚI!y\r•!}yÚC•$’×úQÒæ³;$ÂQJÛW¨ø[eH&í¡(¤äZLKö,ÕÍï „öch•de?\ZÃ *‡rÔJ$©=æ“V*´I{Ì§Ãº³TZ{È_\0©¥Õoç±Î,)Úƒ†Ú¼Š2ìÁ\\Y”\' Ìš¥\nkX’Y{¨t×ÊaKC2k*5¥‹©tó?Ê?¸ö?:=×›ğj¤“¼Ê!e‹eHƒÊ³d`D	^²¾ÇR³4¬Ìñ2x«öXµkaâ52J=&à¨\0V“GeHûmŞõš¥,íá^…dM„iZªô¨é¦e{ÈjYÒÍò=¤–ÃÀ0¤R¾‡OÜDã%fÖ¢};H>æ	¢šk)\'±dI1uM•!eû4Š7j±}õ±ßú£:GU†Tzç¢ô¤å±ÇÔâw\r™§2$³öPAò^“GeH7mç\"‹ñ(C*}æ¢kj#„×şÎÅ:‡¤­a­)eHÙÚCJ ”âx)6UƒãÕYÃÊ?Jk%9^†|ÌRjRè(nªpHfíA%?FÃ?=ÆC*—b²=\\ı,•Ë1™z¼Î„÷ ‰ïAƒƒkxLTVZVg¿°©ÒCÚ[“¹h„%+×’ÔRÈËnëš	µ‡ÔÇ\ZĞ7mH&íá†ÏÒ½Zìac?ª’Cº¿Şı¥Z?%é?kHëºÓb=CÒ­~i(rMÒkeŠ›²‡+›¥¡šk²ÂZ2,ñl(\rã/=ÂêkiX6×äµRjÒø[eH&ßC>÷¼ö³Ôxçâú\ré éZR©¡İÇºC:Œg)‹‰İ´!İk@x¦ñ·ü˜\rFö?\Z_¢™B‡ö8È}[æÛêCzP[Ç3m|Lğ’üÇ†jëpP]{¸öCª®=45.J?ÖTˆD®É<(oÚ?öTÂ£oú?L;7|HmŸ¹(K[¥•¾ä?×/j••!©½¶Çñîœ^}Ù–	µë£´–¯ºïáÚéAewİ‡dÌ5yÃ‡TÏ÷P|Wñ±Ô?\ZûšBYqHYØQ†´®;5•ÇŠ•o•oKÎRé¸‡\ndY<†¾ş¶Ôc>?Ê?ªŸ¹¨¤§‹òÔJ«Ä*‰Úr¹&Ê„F×oHî¹(É[¯|H¥ï¹(­M^ù?ªûo×ó8¨7¤ò¹&ë±‡Z¯$…g\réjî¹¨;$\r•ê×ê?Ji©µTš=\\Å,•ÖZ#<{hwHíGM®sH)TÊ·Ê?\ZßÉ}ıf©B¦ê–ÖÒÚ‡TúÌEÅµ¤¼­û˜é!U¿%K?²k9¤ü¸hM[ˆÃ¤©?˜	¯ŞcMÂ«’k25eÀõ´¸üÇš:^ù\\“%×’\nEÃ!±Ö¬!µ­=\\ƒ!m^{¨AxÙP™†TámÕÖµkR…3œÑÃ•©öPTùJ‡TïÄfb-•a€Yè(İTé!Õó=4åx}ım©ÇÒCÊÒ4l	°JN‹aÍ¯/Ÿk²$¿C*¥=Tàx×`H%´‡\"®}İ†Ô¶ö°†1d=j|HRßƒÊŠ’?	—_9ã1›µ&˜–ò­2¤öoè¾šGËúóÈ²ÎÆ/?²éŸ7îØ[ÎÓñwnÿ`ÿ¯µ5<8ìX[¬úÖ=öw°Çş“¬ûÌ‰¥­÷Ñ\"ÕkëCGˆ×“s/´\'ŞÔµÏ?ĞöƒÈ>u]ßKäDî¸·şvûgóÎÆİAï~oÿîÙxÂ&ovÚ;o½?A¿È¬Œ¿Ù¶½7èã¿ûôŒîõ÷Ø˜Qß?öûC.ªìÿÍî·‰áÏ2ŒØ¢·ÿv\Z,#w:Í¬7v?oœMÿ±şîMü±;±?¾|öâİÛ“7O>şøÑú;+ñ|W+´TÆºGóé2„¿–{¹ßŞ~ºÍ˜Éß]ìM`Í[[¸èÙÿûÜuÆî‚À$\0Şa­uì3×wÀ\0ìeèùgÖÖéxìõgwz?o÷{öéÊş§ãÛï??ÆxÁÂéØ¬µiøÿ˜x½ÙÒ÷z£?¬­É\"˜ÙìslÙÚêÎ¼pÔ?x—î¸{ÊXQwÑõƒÅÌ™vCÇ»ƒ½îƒı.£Kü;êŞëw½0¸ÿàAwhm9ãXKìÕ¾oöì?<¸ˆî÷¬­·?=\næ«…wvÙ?	ìÆÔ&ÁÒ³G;:wí€½ô|g\n\0õ,dnuşhº»ööÙ¸w¾m	Äe³Ô##¾ÿ”Hÿ6šÿÄú{£–Kù“»ş{?­,±ş÷÷÷n×ÿ&ş\\¯¥:Câg‹5tgŞ(`ŒÉY1­Ùaw08ìŞ;€ÿ?º‡yKvÿ![µÃ~‹K–¬Û(‰´V?C™‚rôÛï¨K	2e_^ë‡ø„d°zµâ°0Ú2Éo\Z€?²¹?‚şZñÏkñF‚?²sÄ×¦‡Aæ›‚¦Ş@í\'û!‰í”½—v8íM?ãMR£‘ez“K$)›.÷A£àpæj©“é?¹š	ì”×·€HJ=×–±?òìš¬70<dÚÔëb€)ïmM˜ZŸÙ6Od†o)ì”‡¶‘$<Bı—J8”oòÇ` ¬AØƒ*b2·?¶¤J#µä pK9Y\Z%É¤ub¸M‚²áƒ?™´õÃ]ERVxĞÈspï·O\')¸ÛC‰rAÈºè{Íø.–)ÚÃ]3|JËk5\"%ÛºøÉšé»Š¼¼ú)GĞ¯‡YYrDÊá·\rğÁ5ğ“øèÔ\Zåü:àŞÛ\0ÿnDAtRÅ´,ù°	¹3\\ƒ¼\\ãCwãRüPl³Ê=Ğ¿I(µckÕÌ?ô™¥\\ĞŞ”†Î<¢?ŞtÂ?Uw;ò2åRÓìåÓLäÁİ®¼aä4éÙ+õ;ØªÊËlÿMù}½ZöƒD:IŸ#jçaİöeú°Ğµ~ˆá®j_¶Ë˜³›.‚»š¼,É(6€ïj¾ØRƒÈfŠ¥Ùe1ÜUå¥Æ(4¦p;C5­?Aê!%wÌDÃ]ÂÛ?×ºMa¨„ĞOˆš?Út%|W“—	1??âìj‚O›İĞeõ“ôy›òr\'O•ZÓC¼³Sİ¾,I\r%Š4Å<¸«ÉË\nİ–z¨:a1ÜUåeŠ¤å\"Í¦ ù&ñMuu †»‘?Ö,ël“T†{\röe¥?W#šîò²ÔCÉı¦Êø®c_®\rßøIì ã¡ğ”ÓÒÏxÈ€»ª}™€a ÷4¨ğ?Rªè±é&UàÎ’óÕªn»¦?‘È??ÅıôC!?$H£\"ÜUåeI¸Sk,]-E4UÖeú@Háƒ™¤ˆİü?Âj¶:?wµHŸ,÷Ç üL´„ïªò2afI¸K\r\"Ÿ¹T‚»Š¼4.jü»¤î[w5yY\n‘%«5Ãw5yY ‚9Jí­ÁİŞşeU]£Î71Üíùc[€[™×¸KÊËÚ‚°¢IYV^¶ã?-)¦+ã1Üò²%ÜU?£b¸«Ù—?õª<mµ\n?9¬ê?Íãƒ	gaÖC¢­éÒô]Õ¾LLs	fnĞi\ny~1ÜÕ‚cKÊ?êâ)å?-‚»]y™&€â‡Zrş°Š¼¬‡Èõà»Z|l¶ş]4¼Â‡jëò^µøØ5Â]\n‚îvìË\"€\nùIUú¾WÅ¾¬H«µ‰½Üµå¥è¥­‡Šø®î?´ô0ĞßT³îU³/ó§Yh3PRp×¥“µ&©óP‘Ö‰÷‘«¯¤*•7Guñ]M^¦à6?äb?ùPîjöåÚä|U~r¿¼¬wuáYîvåeÕIAF|§‰&†»š¼lß%˜b|·c_ÒÕ´LˆlFßÕ?^*LVƒ®İ‡\"¸Û”—›ä\'uäe	4Å#ë¡	¾ÈËÄC¶¨Éz Ÿ¦±Ãİ@^f£8O[mG¯ºßàüeIÒXËº|PÛ¾¥ÊCI|\'°?İtÜµå¥C+UøÉƒ:öeú! 2ú™S™‚»‘}™CÕ‡Šø.!/K°ƒÒoøÃ½îxŸ:Ù‹4†»úş¥y&âÇÍà»öùKÃˆÌk,ß	4P«•—;êÈKóƒ¢á\n?re•Á&­°.ÛÚ¿¬ï¤\Z(÷ _ç\0¦‰Ë*ó¬Q@¾2V›Pı¶N”dc¼”÷¹:Æ«‡È&Dı@}è§:D%å~	Àç,H“h¾êhvLd7 ½Q\0¯fdæ=Tg*Ùã+?ñ»˜)„eU+õP•Æ«‡ÉfµŞHñ\Zd>h8Q\0¯}Óğ ô;ˆßôjƒÌoÄc9ŒW=XÒ? Ú#•v$g,=¼*>TÂx¥T?y€oš«Ö/9KÂZ‘TZJöSã‰j%|æ\nàmeûY‡’•~P\0oçxI1ÆP9ÈlZ{P\0oËŞÌW5´o²«U¡ñª§ù¡¨Lı¨x;gFRLã¹€·•Â ¬±ÄI?dëofÀÛ’œy€—?œ©QŞRÒŸ<¼æ©\'õ•¬a[Q³yO<”dİ\ns•ƒU\0oKr¦ffŒkœ(¿?ŒW—œÕö5}¼ä7©~L€W?hr&À«4iŒ½:³d¼Pr&ô?›•``í>(€·å«İ8àkH–·À7š-¯EÀ+§ÿ¹6€¯)ÿÏú¿¡	€•3\0]À¯6¥AÀ¯ãg)À¯6©AÀoªä¬˜èªÀoªä¬‘èš\0~S%gåT@×ğ›*9¯8PÀoªä¼ât@\r\0¿©’³rB køM•œS]õƒøM•œ5’]Àoªä¬œèÚ\0~S%gÅÄ@Wı \0~S%g‹©?6øM•œe“]“ğª’³Äşjâ!?z<;´)±KM¨€§%g?pŒ+ÀxÕó(ÙYÛ°EaJ6h+%Ê›‹ì°ˆìœF/!9ı*ÊÎ¤a|Õªb\\‘œ‰à\03©”8P?ÍÔ7}Se-)€—?œ¥bİ‹ª\rÕúUª%/?œ\"2£6U¶û \0Î%§¶œ²£Œ®©TJ´IÀÍÌU|Pã©¥¿‰H?,Œ+p#Ï2é,ˆ4ÑTÔt.àk°9KQçA|¿	©\\%ÆKÜ\rv=/asÖ•,kxP\0¿¾§9S­iúx¥tAkÈ^e||\nàÕóõ3ZÏ–¥XwEÉ™™0¨Pˆ]ÉÄ(€—´9‘Âbì)W ¶%F«b¼”·¶?¦­¸4S²9ƒR\r–Ò7ó \0~?¼µY\Z¥1»8kPCM}m¤Ò–·¶mÀ‹ôñÌ¼Af?DC¿¦w¤|¦~?UtğR’³¦^ˆ½fJVFæ …[+h‰^=W)NTWS_3ÆËäª§©¯ğ:6§Öo–¾*ßâÖ”ñ%XL	À÷*¹àRØ+1Š|}ÜôP\nğ}ƒé–Ñµ%g!vm\0?LßÆµü^¥Å¹Æ‡ª\\å~ûşñŠ%èÓx»9jÛ\\>˜ÔÚ¡È!4PkVW^7ñ¡šC¨š*UL¯ÕFQ?Æ‡•rmğêøğ=©$èæzb¼¶·öª?¨ËSãkøPuqæë*×ğ{5HE›é*²<ï¡*àµ/CÑŒº„…7?ˆÈ©fø¦\nàjè*²«¬‡xÓ²ÃCy%k8¨\"9¯àU$g±È¯ıPyqÆ9„ªr•«|O<éJ¹¾€›$ç?Àx;±µ) ²–ºò¦äzÌüpí(ñÍ n-î©W¹W¼8]µ\',‘J›óZş W¹N€M’3‹«\\+À«Ùœyıf?\\k­äC!àµmÎº•Ü4rğvïF1sÀìjÚ³¹—…ñRûœ¥ˆHé7‘V­ÄC)R)%9«[£pñ&kq&šN³²,ÀKE)à•Ğb6xA„PiµqÀmNM\Ze‰¦+\0¼ŠÍ™zÓæCET.‡PF]?ó¡äl-‡Pû€Ô¦Ó€JÎ<ñ}•/%9‘Šaq¦GQğ¶\"„Ú<? Ş€xµ¡Rh3/•}¯?¯	ğR±µ›àğU/á­-­«lğì}NhGy3H¡?îYÒ ?Ãä„0=Tùesi°jCJ«\Zšbdê-è*årµ`HTy(x;’ó\n\0_¿ä,	kÅÅY&‡Pi®²QÀKIÎÄrº€W“œ×ãUO¥dYM“òu\Z(x)›³®Ò¯ğP\nğBÙ‹S¾©CÍP¹B¥Ğ†/”œ¥¸Ê\0^Mrfq•+\0¼ÉÙ\Z¬µSŞ´ÑÊ¯˜Ch?ñó¿ÜjÆ	k–{¢o¼T&„¬‡RƒmóÁ²ş<²¬³ñ‹À?lúçxæL§Ü¹ıƒı¿ÖÖğà°cm±š[ğï`?ıG©öÌ‰¥…÷ÑBm„µğ?ŞæbêäÜí‰7uís\'´ı ²O]×·£ÅÒ9‘;î\n­¿İş¹ÎÆİAï~oÿîÙxÂf2ì?·ßÇ ß?dºòßlÛŞôñßızÆ?÷úûì?Ìdìûı¡\rWYşf÷Û%ıgFlaÛ;\r–‘;?fÖ»Ÿ7Î¦ÿX÷&şØ?Ø_>{ñîíÉñÇ?ZgïªeöÀ‚ªXóãh>]†ğ×r/#wáÛÛO·7ù»ë?½	ğ\0k™\0û¿cŸ»ÎØ]C˜ÀAl ´}æúî‚½=ÿÌÚ:O¢€½şì.B/ğí~ïÀ>]Ùÿt|û½óÇø/X8›µ6?ÿ¯7[ú^oô‡µ5Y3›}-[[İ™ºïÒwgîØ[Îº‹nèÎ¼QÀÆê‡¬8tü°;vƒÃî½øÿ¨{ØïzapÿşÁƒîĞÚr\"ÆÉ–Ø»}ßì?ì=ö!ôü~ÏÚzØ£`¾Zxgç‘íù“À¾`Ìn,ı1{´£s×ØKÏw¦\0XÏB¦ÇPè?¦Ë±koŸ?{çÛ–@ ‘ÁQş§Ä³ø·Éü\'ÖÔµEYñŸüõ¸8L­ÿı½şíúßÄŸk°XßÀb}?‹õ\r-Öİ·Á‚­€n÷~÷~Ÿ¯Ï§İƒ~÷Õñ»Œõ¹÷pÿÁCFU|}$ .Ïó`:fÀ³§Óà~É5üĞÚÚ~í?²!rŸ?1~\nÏÿ€ÅÍ€‰¼Q(Æ°mXÍ´z`I?@S¤e|âù+P¢~ûÕ-¡F&,¼vÆ‡d½(º²ô£Šÿ©¥	M8§—¡[dM¥º¥²—ÄQˆäÏ?±4¡ä+rMÛ?{Ù§óg)×P?öYHø°½”×À„¼äXÄXÒ½¤¬‘¬¦Y¥²í,]r,Ú°?–ìå^ŠÆhJ–Ö¢äû¥(9‹f\r>2c/bŒÉ•¡‘dÂq‘1Ïäiy^ú¢eúÓ?SfdıšH9:Ï…ĞèÆ¬Âc†¥X™qg·øgÜÍ^i™EÂ¥ºÙ/…´Ô„TEÚA…¹i0š„³<Ù`Y¸‹F“æ-ö¨$K¨@i\rææAUÙ\\¥GåN °G%f¹AW¦´á°³©ŞÍ^?uS™’¡¤åf¡úhÊq?š?ãn’\\€şI°„¼I×5ÉÑ¸›M «AsçùİÔàYJc?§›B.0Huc¦?“âkµ&. é²¨Ó?Üz…~Ò’Á€9?›èÉĞ½vÆİè…+$w¸q7i.P@=õFs?mÙ˜©¸mº9L1ÅÌÓ¦ËØ…q7º\0}®jÖ	?¨+e«ÉÜM?A?İ¶¹BV7i.``¦E^’¡R9œÑô³`ÑÆİ¤¹@V+5Æw“äb.uüÅ[wz©R7¿¨©CçuSNHÁQ ^SİÔÕ:«QšY(ûS“B9\\ ìbú™6fÓ˜\Z[ˆ»)ËJ(\0y£1éfD.¡!?ûÈ]¹HËïÆÄÌİ¤U6óR6vcÒÒHSDOîÜ•\Zw“äJ+ƒdiƒÑ˜t\ri¦ÑTï&ÉRs™‹,ñ?ê&ÍÌ$`*­0\Z“E`nPSyÓÃÍï&×\"(`YåyZòb?\\–obË¹2!öÚú\n$KšÌM£I^ZSIÈŠ¸›\\‹ Üå¤gòB˜LA`nÛü™¡Å;¨Ğ+Ê2ñCÙHÿìëŸé„w“Ö\nbŠé¥Wªa4fï`º›R?g#­<è«¥É\nË3yíH¸ëŒ&É\nåMÖÀr…tòJ?úİh´?ê¦œ.?’pfA?=š‹ \ntòr‹$XÊ´üÌ?É\"0éôÏò}/­h­Èi\Z¨ÆŸyHKs?š?ó‘–äYóŞ?Ò\\@ëÆLiæŸyİäøª0›H^OP‰—çÉËÊÉ³ö¦?f w“æÕFS “‰úÕåœ…³q??7I. hªân@âgª›.Ğ¥™¹@?¶ó»ÉåÙ$?ÃŒİär?¸Ë3›dòvISígB­LÉTëeŒÂA\'R1‡N&F×Åà@--ü™×ME?X“J…ôò53<·\nt?\\!­ıÌ?YP†?ü´ÊÏ¸›ÊŞÁÂŸ\n»Š»ÉñäMHÕÑ”ğšø¶IŞä,Ïf.?şÙÌ¾I&¢.;š$”6è›9[kƒ~Ù½Â„l–¸*;a_79SY¬ÇÔOÙÈ!³t1s\r	ˆÒOZ!¨In‰iMöcâ¦å¨Q­égÁü„\rP¡†¬´ˆRú1ëæŸÉYoi7a!†já­?H©ĞRmP~R·æ~åˆD‹é.KâmP‹TÖ@Õ	óH\"ùSégü@şTú)D”=?ÉA˜ñVV7Ği¥Â …Bs?&å ı3?I\'™€¹ŸrÚA6ó,)çJ†¦DQS3÷S–*ÊàE|§d@aÖŠ4­*s?ewLóSA&3hæòƒôL2*¤Ç£Äß*-¦MšÄûùıäš\nµ¦ûI.ĞİO–¦İ%ùµ!¬0%ÑrVUÁO¥Ÿ²ÖBÓ~Êòƒ¦ıT/®×?1´pı”?-lÖÏf‚EÑ…­õS+°¨F?íûÌı4-*ßÏ†ø?1Äpılˆƒ×ÑÏ†øA*Ìp]ılˆÔ4¬ÑÏ†øAÃPÃòılˆƒ\r×ÑÏ†ø?!Üp=ılˆ×ÑÏ†ø?!äp=ılˆ‚×ÓÏ†øA~Øa‹ılˆ¶ÔO­hƒ‚-?>PûA~ yQM€æã1í4j?MÎ–¾°VW‰H÷“µ¿?Z¢ËAªnÁx”S‰fœk½çüÌ÷»¨á‡&÷Mz³ÌìÓ(ò[\Zâ\\=yÎ½l•@4y[¥·†~Ö€·T¢¹ñjıı²Ç£lR\'f¢º_ìŞ0›ŞJ8KÓµ!\n±à§y&ŠèàŞ~1ŞZ?ÙŸØ~?¦Š¹>ö*?•~Ö}???©PÄ?Ó>[¥ŸÂ3Šf.‘n<~Ô`Ä–&ÅÜOâÊ/)#MÇ]“ô–òûçô“Ã²è !-JñÑûéÌEëÁ[«öB¶œ»Ÿ>¯¼¼•µªôc??jLâ &²$½5Ş×¼¿½5ŞÏº¯èıNòàI{ôö _Š\Z?\'#.±ı~Ò)LL¼DZiº‚¹Ÿ½\"½×Ü?Iåö³Ÿ?ÛÚ-ösÛYÉÆ[íç0Õ?‰$š÷s/OOTè­ğg½åòóxı¤Ñmê§ÊYóxÒ4“æ£Ã~®~ĞÚx†ıA)=>5çg˜ŠO¬Öxv?ºş6¤øD3U¶:röBó~ŠèÀ$-L?ú)°ú¥»,èç^ŞÌt?şY@×9zˆ¦\'öó ˆ¿%ûIPGš¥\Zûäè­ö“ÃòøNºñ|¼\r†Õåi­~öòô·ûÙ¯.Okõ“³¿?ÕŒÇ¦”~«¯Ó\Zú›¼v½PÉiŠ·ı Õ~rì…6ûöóä\\‹ıäê¢<Çp1ÿL÷“¥49\080ôc>ÉT@Ì&îšBƒŞÏ~Õ¦JYUÚü$7ã-W?HŒÒDRXk˜î§²~P³Ÿ{yöO‹ıÜÏã×-ö£ÙÉ,MÚĞ\n‰9O¯Ú+ëO4«¦¦\næ~ªœ_(×?²Ö”~’yĞ×5\\ÿA¢Ù¸yÙ~Êú«ö“°Rñ‰Y$Şt<9úA«ıäøÌı$*”]?÷+ğ·&ı$®n—ß&f)S# ©~öÍü@v©± üm?×^Ğ¨0KÎ%šû)Çš÷S‹˜\Z/˜Ÿ\\b‹ıäÙ?-ö“ëOl±ŸÜıFÓ·Š\\XWéÇP?Şú‰Ÿæ~dÛ?ıd3\rÖéA9ı y?ƒRôÖ¼Ÿa)zkŞO9~?GÌJãIWú1§GNşTt¥T…4x¦~Êw®õÓp?\"\\Ùc¸ñ€ı½/oG÷ú/G„—ñİˆ¼½ëz5bêş¯«¸ÿoßpÿßáíı_›ø“¼ÿïÄpÿßÉ?»ÿïË¸R,ï‚@`+mÜ¨¬ÿ(˜ûg›¿ÿbİ÷RëÿŞşíúßÄIoß‡ÑØzç?Ò$È×c;<‘=_ggf_œ{£sFê>[«¬òŞ¬qÏ?(€uÎ;°è­­U°\\P}ßsıT³G—­ö?7sÎÜĞèŞÌÉteã‚vKpBà#ğ½cësÆæ‹	M{\Z0>0¶ÿ³ôFŸ¦«\r‹»Ä…emy$^0l°Š&ö•Ã™Î´;_.æAèòú$wYe{æxşüpg£=%äÎöğy×ú_ÿ¯\0X‡ŞìÈÚzñêõsûçwì;Œ~X?7±wàSû«ì½]Ğ!¶&ókp²Ãì.{û§?5ñĞæ+±ä;3W`³ÿãoï²·ÜK/ÚÀÏ?Yû>S-&ÁÜõ¡›Ï¿\r~g\r.N±&ôı•çgôúÊŸ/#š¢qàzXÓaô•¹£ëˆ÷)L”û‚ÍÉË1CÔ˜Œ¦}ü?:åv,\'Ä~ÿö%G»¹g†H}ŒCã…2FV#£¯wËHräø¤aÙ/Š@Ï\nd?|dÏÜ0Z«¼\nÇ\"&Û?cï;6ô\Z?œ?™Ú±¶n´dL»Ä¸ñõ¾éZåÿlë`ÿü8¸w¯ŸäÿÌş»åÿ›øcäÿ¤SüüêíŞ0–ß3Ş?rÃª\0ƒ\\úSÏÿ´Ãx:ãÈÄ\'%ûb«Ó–«à#¯)_¡2eÜWi?©AÖ8#	&pPSt£¹7ŞÙíˆ¾v?Ms­&%¥>ŞØ~\ZÌæÎÂÅ5îT³ğ¬ckŒ{?xÂ¢etÀ2?Lú0?õç…3Ÿ»‹l÷fÄT1!N—“VeCb«ß³q³>V?Åš˜¸µ•Šh˜ÒÂ@fCøÏH[[ªH d-€?úÑÖVø±®wO£KÖÔÖ(ºdĞ×ÖV0ù?™½¿‹ª£`úÁèÓ\'Ó%~vÌdùÈeİŸ…‹•°ÙÙÂÿ“ø?Í {¶€±E‰—??ÃBùÈ8Òµ‰(|Îö™ $$›™‚êªµ¶8pÛ\\F¨\"½‚¯ÇÌ\0šºL†Ù®-<7dğuXİGlZ‚ExDÎ[Ñ\0¡yÌ\\ù/2©ÂmI1PÛ8[ple)OÇl‘?Î²\ZÁ`Š“ÜŒêôzÊSÈ°æ·¿aºXo|FXo¢*Ş±.IvñÚ(tÅKMöÊR…\0°Á…Ö BvâOŠ `¸ª\na¤Šd+’FDÁŸqß³a.\r$ÆE” \nSùòY÷‘˜î£—Ïâ©ª_qVLÒ8?•üb8BŠÍ/ ı›ğ `Æğí[÷âÙŠ­@oÄ¸ÅNøG¾ÃÅ•9:¨È>/1\ZÖ”a0iµjÈ!ê>bÌÙ?[[Dƒ[\nÉ¾|6L“¬¨—$Ò³ñP#Ò­ê\0½Yv iPÇ¦tB?#5$HL™5W8”BŸb#0[¯¦éR‰Î<wIjcsj,Íİ½#ØàK6Hø‚ñ¾?ĞûÃe?v?ëA3jã„©Ş\"Tÿzí aÙ¨‰É8Ñ“tCŞÍÇ,¤5$q1‰™<úå³º,úlÜ€Cƒ)¸qÍÆºş,¬ÜÄ¨\nÙ3Ÿ‰V¸³1±l4Osµe#¾+Z=æö\r‹¨¡ñR2·i\\Qù¨6¯«\\¬*ãÖWÙø¬ïÜ±O@ã4I4Òy¿…Jw­\\?ÓâŠq/ÄO bNÉ×Ü(`ï=?W˜R×;ßĞwi^\rë¿şÁ~ûÓë×¤ëJÜ>³éû‡öóŞ}°ß.§S›,3wîõì.¹ü§?¶\Z´ššŞ¸>ï\nÿÉ%v\nÓ¬Ç“\0l?_1OI†ÅÌ‹J‹›$½?µ\rTÛŒÏ{ÏLI¨?Ï}C•³x?\\\0m²:è\\Ì[×æå œ8Q?Cd\\Ø2²:Ó¼{8%g]ST¥?Èàùe´pFìù(ºš‚Êm°¹‹êã°ßÿ¸ÇşØß~Ÿ\Z\r0?İîÕ\0&bR1ÀµÖÆ#†vZá^†öôŞYD;à‡`ˆíØ{ğ†Úä²¯,[±1{\'{¦:v<¦öm´»»;Fr®Té^’DŸó•ıd\nÚVL?ESÔQ^n&vg\0“xØGEıBJ\rpD<Nd\04v x£c³Dı\'Óenı>şŸ}¥ÌÓ§¦îøƒ;ŠÿlªUÜ\0T…)IZ¡2~zĞ§?°?·–J0İoÜÅ|@ÔÃéÉxÀÿ>èS[ıtÇÆî³Ê÷²\ZHwşrá¬”Ï‡¸p«?h¥‰¥P@égĞQkLX¶¶.ÆT@õBoú\rñ7¶ñká„gÛrşÎµÔ¤÷-)&y;ñR\\*f9.•˜ßÄq‡ı??ÿëùÇ¾şR÷bhléßsœ¦r›iOÿÛˆvi]R7MõŒnRä?çîYì+éÒ®š¾y–Å<9”¥|¸$´QÄH gîofù«?íØ‡?3ÎE‘6É[1ñQKãäXìp9‚˜	SáW=!$™İ/¶ÈY_¬§í(À}t?ö]‘€g¿tğ«ã`ÆÔFo\"œÌ|C?ê^Î¿d]3‚R¢†Šp%#x`¿œFE[1àd?´\'Däã0\\ÎĞ\'?«fa#>YW°?uFŸ eÜí—Ûn6°ª°÷¯|ó³}q:›Ó`?((‰ı·ïN?Äú??yó¨-ö6«ÀÆ?Î±’Œàa*)Ã¥V†HØ†!OC¦ÃÄşÅ93z¶ˆiz÷/±?ŸlOi´HÿıX¦€7àùc÷š\'+XAŒ Aµ·`E™É‘L2Š3¤9;vøÉ›Ï!äñŠ„•¶ò2¹|¤°?ÿ¨Ê@ 5¹<”õD¬˜RDb<vîrÚŠ?ÎM2Ù‰\0{¡‚-J5’sW¡1pó*ÙÒ¬\"^¸µ£ÑÉd9mĞÇÌ6v\nø`íx¤şüû}v«¶÷İwYèøº?üEüØ ’˜¦wqõb·c«/^.\\6â•ú†i[B/‚9ê4v›à·%.b Áp“³“Îø	n0\Zä3ÎónÒ¾ÍZ¡öÇ(øñs\\je‰$.Z-´i6Ì^!rÇ$‘¶CÆn*º¥Ñ¶;ìhÃ^÷h6ÿà†Ğ6ıR¹)¶¼ƒ_Ë­†T^ñûå³?Oß¼ÿøêÍã—Ïuß××_\n÷	1#Á÷Ğ~òøĞ2ƒQá}J³_±EcjìçÇŞ¾zû2Ùœ(îÚïÁ©s:]1!‘î İV‹#U„Ä wø(?_ı÷ó?¿ØßÙêó¯»:¨]hùÕÛï¸\0?°€U*“ozóñé»×ï>g5ë/g§îÂ&öœ)ê¾4ï¸Ù…=.Ê€€hÙj*3NhJ‰ÎAÊ‚ÈEL¿\n:Bæ]2œ›FÍ:hì!Hö,ˆ„kw+0údï;öÅîÚ¼•İ#¹bØ7¸fÚ±b›Š·NMíˆŠ9ğ(Ai2ş‹±¾¾?ÿÍøóWŠÿº?ÿİDü/_qZ€“\Z|­ƒ?şJÀÚN÷—\ZÿË©q¸PÀÿ‡p¹`‚ÿïìİòÿMüÉãÿP4õNoeBS™àG[£?\rz	ëEˆˆƒR\"b˜)#†ö(„vÍŞñT³Pxqî.\\øŒ;àFçKÿÚÒo`úŒ·8CF>@İÂ¹èØCü9bfÚÂ\rC÷Vj]¹Ô\Z?ùìD?G`ìa\\#LœZºÿ»\"½´¨FN©_¨»ıcø#åÿ…{º¦ãŸò08¤ÎÿìÜÊÿMüarüÉå-	Z&.™È_8&o—>Æä„xêÆ \0È\nı±³Û¯î¾Ã\rÆ!àÕÌñ½ùrê`d?¹¹\\µÃ–G??qí&¿„NàÓ²G–Ä?\"ít’(låTR	uA¨RS %«±Ÿ/üß†}~úÆ^†î1Ã?ÏşÃ^ıĞç‘)ÏÜÑvş@\"=h€VC¡îš>‚ŞjÌ“\nkâg÷ÛéÔM]Öë·~ğ-S´˜váÚŸüà)?õ½À?‰Óª@ïbsÂÀwN™?Z¸ÿY‚ŸœjE°Çôƒ=8Bğ¨íĞ?ìoÑ%•Ñø·¡hÛfjÓ H£ÃíK˜\"Oa;élc/üwŒ=ğQBÖ¡Â&Ş…³\na¾ùØl¤LK|¦e9sıè ºp`¢\0‚¸Â,`¸İ‘0°™ÆFèsS¹¼{¡È}Ï´ ÿû?û+F‘£Ù\\Qqºİsw:ßæşY#ğËŸl KPò¸Ç“ƒ0`\0‚~½œ˜ı6¦P\'fÚBï{»+$²îà÷İ~\0O6Ìy´™påşj,CûÂ‹Îíã“g¯Şb+v‚Üø„òÈX§¾æÊÖBBÔ.…nGêH~^áíç‹E°xz?¶íöuØÃ­0m0)­‹\rë-£)PıU$áVq%e?5ôj‚M`[´™Ô±=n^p•Ì!5PÃ|–\n©îëPh’ıÙ™zã¸?üAn‰Q>eœÓƒD\Z®Ã¬,I\"À¶#hhÃï‡ßîÛ¡µı= ?AÛ\n˜u0™ M´˜:#`¨Ø?L%h/AĞ°u‡¬ä9³÷VÒ\nä4ËH‰\0&øæ+ûá.µlÇÌ‚ıÖV‚ºcèÜ+‰baœÆ8å)Õ\ZÅ,°‘‡swäM<fıõ•¯l¡o­mÅĞ%Ö)íµ0óÒüJBGÇ‘\"‰UN,ñ&è+ñÕnÚÇË;5ïkÍ?EÎ]²ƒ¾Ö?QëÀÓwßáÖëãOÁ¬ÅsÉárdFÇa/©b%v\0 Æ°wÅè=ñê¨\n?XÊÀ#ÃÀÁ¡äÃö[ \0\\›ÑîÀ¹lˆÈ%”Á&§wïOâ÷8Ãİ?aŠY£ïşÕ!è#­œ1ş?ú™Ëe,Ft@XU­D€•?3¯wüp7=óÚt+t??óSuæ_{aG…BMUfD?üñÏNœÓxÏip!êˆ?ÃW\0ğÖw¼…?\n[” §¹è¿€ ‰gM\0Oı·Œ™€br0<‹)L£E‚ª‹Ôç K4Ëàñ–ø¿Œ«Øú7gYz„EÇş7?¢HX¤ßğ\0x¡LIYä?Uäs·ÄØc¢?wl7\Z©?T´şì?£ó‡°ùû£‹?à\'!î¡­Œ’Czü`RÿŠ?)jS‘/#å8”é´¸+W~îµ?~|?\'I>\"@³#ó\Z’3ÖJ½ìUšÓ\r,yIe’9ÄC‘L{ŒƒMÀ¿}h¯ğ8…xèğšjû?RÙHÿkŒMùÉ}İ—Ê¨1±¶âqÎpÊ4«OØøŸ « û{Hš‘H¥ƒëV£`6$Ô+­ÇØ“{9Ÿ:>šŠ—)Q.¼¢äª°cbjõ¾ı;û5…ÿpDÿàKÇ¿ƒUI`YÛ6H·ßØw¿3Cè„ëAwUc!¡ŞTº=ß$D\0$‚	gAØ#Î~Ù¿aÉï$² ¾BœkQxf,h¸4ëw!Û!«ğ?(şg{W´:–€|pçÁ‚C/KRãXÙ­Mf£¸Æ7½FBÄj1¶‡ÿaÓ±\n–†•ım÷[ˆøŒ£FÉK?:ÿİ?¬Œ¥<u‰«b­»4{Ø,E¹Ğ´#Ûßå&{ÖÍ\nşBÚ¢ÖÑÎPMÔ,ÑÔqìÈ?±Q£KB(Ş²Ê6¼ìE³ù¶X”gA0N|‚ÀmåPÛà¢%Ïkãk8+ìûİ#L~¤K|æ?Í¡Ğ|»FÇ;*ºğ‡-)$F$Ùf\Zt»Èî!?vÈ ºWbÜ·(À0…]ƒ©M­°yóİ°)Ä0?iÄ„|•1#º¿—cøùÂ	©‹`:V»?§Y“Æÿğâ]ÇÉ57\0À,°š@­ÖÖW?ô¤Î]PÒv¶éƒíq³é™;u#Å~¦]¤™Ë,úUlÂ	Îè^§†O§ß2“Ùe‹\0]®?¦Gjå¦ºÁ¥ÿ—TÁóh–í­û\'×ÿ»ß?¼w˜Úÿ=Ü¿İÿİÈŸï<yóú‘õı?Ï?cÿœ¼:yıüÑÙØF¢øş.=[ßßåïŸ¼{ö+ûç+Æ±<~ûöùûÇç3M”Ù+>zlŸ-œù9ˆ\"éW†ğsÑ‚¤ßÀgßé›˜K“ÜÀ`8ë°¾lÿøáù‹¶Ï£hşğîİ‹‹‹Ÿ¶S,î?ïn?š:ÔËsˆZ(ÿX;ã`„:Ì÷w?êñ®N§ÁèÓ–Aä²XñşÙ£Ÿÿú•ı?\rÖ>ùñÕñWßßåå–\Z?Õ\r?vèŒH_\'¦=ëŠ¡—¯^ğçøà€c[$?ÎÑÏ‚> ,Fÿ\"ì¡2ÑÍ7;Ø,õ?›«àE÷BËùÌ”\'²|¨ÿ‹`ñ‰³&téÍÏ™ÊÑ.a‚ÖÀı11nÅıEÈ#üxäÌù7›?ŸİSût\\„‰Àí˜vöÙõ9¸=>ÖĞ’é0£ŞÂÅl¡xÌí/²îÙrÊÖ×…Ò\"3×ŸÃ.ä|c~JÃwYC³`ÌT¢m°Y9ìM,ÊlL+O1-$Œ\\g“,´hoÒ³Å\\¾—.ì“ã-üÄth5„£œ ş¡ÆæÊIgY[/Ÿõä¼Û?ù^¸¢<¯0Sc Éñ°„×ÿı³:;–3=˜D>g—¢!¼PèK8•+e£<A!àê#+¦Ëˆ&\Z@?Ÿ]2$Ïàø“¸®?Âè[ĞaÊ»q‹6¯cHË%ÒQ§†ÎA»ƒé* šeå8\0C{0à?œ,ëû¹Bè?Â5ãDúèã¬·´¸!Ø¢Ã3.Î„?b»¬hí;¡Kâø{Ç>_¸m5S?^°8»;_BL¬húğ.ÍÙÀ¾¿ë<ÚÅ&ÿ`¯ò„L»İ?¼¹l\nØó]ø”µ-Ş\'[‡×ØÅ¿?uO?ò€³@\0î„‘xÈ2.èúß4?íGàm˜ÃJa„ƒÈ¹–©.¡7®–ã¦º’¨ù“ÅÒ=YÍ]LoÌæsF@«¢s\Z0nqt<|¦^,\\úpØ»üWN#øm`›Ö!|¤$jfyœ34Èœ±fùø^ğGÛú†ô©HK~.?¥ìŒF™]â»k†Î-½€ø¼g[oƒıJD ?ÈÖÑÅùŠ0ÕY;³Q?”m8÷í??áŒ?ñ<Û°hwA®|Ó4÷ò?şË|Æ\r¶S/š9sÜ\0RÏÀÿ¹ó™hÿÌ”)&ÀWŒ=Í\nÑ ??rmØ;Pì?¡Æ‹Å7‘ağÅülxEïùã4çm~NÆ —oÄ:‘Ù+ôÀ—ğ8^¦=ë±ED‰;c\"8$\rcşó#X‹çßßÅßÂ¼šÀ,67)úø»èd[p€‡±wDâİy„‚d?%3`ƒµÂ$Ì\'ÀæWLÑĞ„ò?{?N„½ıÂ×YƒLxï±W??ÖòúÕ#©ü?AÇÏö£§/\"£xÊJ|˜Pw1Aö\'¾¹`„2ë9Şö£ŸÙ#ÓòÁ\\a DÂö/¿ş÷6	†ÿ—Ñ†ÒÇ€¥³±©ÖÙ\rtQmÂˆR¥8Ç\'û?o£Ì?§:şÙ’-&Ss1²±=4œ™ {Er”7š‰?±ï~dŠ_€Ø\\ótéMÇZ],Éªí„LÍÛ%?~>¤ÄîSÀ°‰Ñ~|x&,¢¾ıˆş}ÈV(“™lİ;³ùÔ5â€be,ˆĞb^$_BÈ1À$óÊ8/D‰Û?ƒ.‰4íŒ±Öœ)Ærq\Z54CíÁtõ#Ö mPTAa˜øváJä|ehá‰‘ıŒa“s\'ä.¢ÔØ?3ßØ¦ósçÔe?Á??ä]¢/îâ¢yÿ¨H‘Ş~dı4û÷Ïß<zÂ_>\r˜fóÊõl…Ó³÷Ôücûíã7ÏØ–oîbäKù=ãÁŒƒ/@ú°N™ªL?Ã’XIİÂD\'K\\?L¥tÈˆqª–Ñy°Ï½9i??„^\"³ñC?J„,‰•ÁÊ¶?9r÷oÅº8Ó’a’òk—|b Ã¹ÀhŒ	‚Œ?C?  “ñA¨éØsbßp$‡©ü–d¢À‡£à!ŸŒ(“²ıè„)L]ãs?üàå~9‹šÄµ?\nÆuah³X•ercÁ˜êÓwïığêå?\'öñÉã“ço¿=±_¼{ıúİÏÇhóØ¯_½}Îh+k6’õ‰=ô7Æ\Z<x°ß?ÿàñ¿÷ğ¿÷ñ¿0qFVáSĞµ?Á5vfıè,àf„×û¯¡3†­a?|ğßS‘€9¾ßt?|è÷ïC0UoZ™Œd˜	-Ác˜ÿÑu¦Ñy‰Éf.h)\'!+\Zb¦\rE%óü’ÄôöEkïÏ½)S(v¾»ÈiÔqc/…Ûw\ZLÂœï™(?¢‰¹‰ç¾ı|ÊÔßŞqñßL—# _FJ»9­¢~\Z·	­uìgÁòÌ~â>ñíÃøıÎÓ]H KHå$ú²Ç&Öû-ß?é6¸BØïS‡›C «	3\nX‹/E³²œ1?ı¬Bl¿q–¶Îìã?fËÅ4 ³\'\0Cö¿Ø_‹5n?a6v©]É±còÈÎ†À\'¼›çQ,«9S*¥6æb³h²®¸ŒcëßÙÄÄöñÄuM5ÏÙÌ]Œ -¯3ŸO¹¹ÛîşÙÃ¸t2¯HábÃ°Ø/0ñx¼ÄNui«Y#×G=Ì™¢h?ÚÊd??„ÇP;“	pÊLv‘…\nvÈ ÷vÀÙ¼ØŒ{»ÓIGª±hõGÙ§N.(˜æ°ù€Ì^æoà\\ÕæŒwÌÚfÙl¡×A¨ã%àŸq{?>èÙªèÃ81Ú‚djjÏÚ~¦Àns»ÄÕeb’éÍSÈ„ˆ±=¤÷,’KòèÀ™ruŒjÔ’¢m9?Û??íWÇ½íxNpïKĞ(8@1R\Z1›f†@³V€Ô<pÖ¸*ƒÌäËì(?é@˜ø1%1DœN½™Ç)”×RÚ\\µ1Ú?œS†ˆh3iM¼È‡¦1¸R5œø!ˆù„X÷s”$??dc\"{†\"pù+`æ$=õŸ¡WÉ[Ğw1ñÍç½bgCG#˜/<‡.­-ƒgC«ı/¦û87ôô!¸˜ºÜÄûq?plÕ>f³¸dË^LŠõ4XÀ> Yz@nYğc³ª—4=Pü=ûÌ~÷Â6ˆL) …Ò#¬\0Pz4K€=“zô}+èïKúD­ƒÉt–Ğæ§bÆè÷ágY¬ñÂ¹Gb\0ñ¸©?34õ|tï- nŸ\rÏ–ÓÈcU,‘õc„[¬c6ÍaÄw@ÈÚP?ƒ“)¸&™–;¥‹v¨„NÌ(?5ş\"Š?cDöL[rÂÃš+ğlšLôÿÌú¶vø?İ;im.!„\'Öå<âG6Ÿ\\?&Ãg¬eîdì)ø¦°»¹œ5ô,E}cÿDw8-’õ:©J?Î’X×L?ÃØ+¥-Ş·âÀ_”hÀâ,jV¹@8\'Aˆ\\\n¯Š0úĞ&Ddïå¦ô+ybÅw?‡?¯?µÄoàÂ£OŒ?\\Ë±?ylù¸~“dJÊœ;£O³d3Âôîõö€×òeå†b=ˆw÷ºq½0S®-!®‘ëÛ÷»§ŒíŸ‰ö{Ö1‰/Z‹¥Ë÷Õ9eÆì.~\'?îà‚GáÈ,eŸ“…\0uØë^å\n•x¼DKXá–b…Ÿ‹¥|¾ºÓÁÙ˜¸™p{¢¿×T?CüËC›‡‰õ\ZP7õíãÈõüoCËà\\ë¹>›—Ş…×cl½ç—w\'Ñ}—BÖÜıùçŸï¾|†›uÌTc¿æ3487Îg	ğ ±³OçæˆÈ!\0ŒÁÚ½K›‰¡0·ß?L@á¢–\ny‡{2ÒG«\r”½$f¤è?Êè+3\ZM£OhcÂ H3½ûèå˜•O\"ƒ5¦<Äèíæúà\n4tˆ‰f‡ÂfYºf‘ò]WlHàÎæ!ÒÉrNà!ß¢˜…]šD\'d=ÑÀè7$²8g†&ã”¡ˆe…•KGß0´:î”Çñér±2OaÏ?şÇ8v?£Ã³;Ç¶áÅù]p?Læ8_r8\0Ë?L¯A»€yŒ>(T*A[rF<æèÂ–»?x†ÒÔ»Ïd1Ó~»¡pœSÓ°Aoón‹­˜[…èxEp><ÿå‹±›)¨2°=G]0fÃÎî~p//_êıhÃ°„vH¢D£ÜÛB 3{úµ¬Àœ °`‹ có=ˆY\0ªG\'ñ?¨?ô¤$uÌ$uÄ·²\"‹1K?\'€\\É8áÿÊØ46?H\"5˜Üü<h‰^Ş°…:>ù„`	€øNm‡£…7?T÷J€9ú£8ŸAmœ½ŸƒO®Tª9˜¤?Yß/§ŒÑL½GIÜ‡Ã{ış½Ş<¼`öáYğùn„Ÿì¿äus–Ñ\'S?ìõìc†0½±)˜Æ¥ïÍÜé)0šÃxÍtùÌ±_²Í7 Ú}ûåÔuÙ¢eúˆÎ¤XR|©‚Ë¦ı©XAÕŒÄP?ÇÂšÁ%,¶/†¶½£š´vÚ¤5~?aTÈ?(ÁÎ	F?:èÃ„‘<{şâñO¯O>Â—ïŸüØ±P« M¾sšV˜ºj0æğÙcr½HŸ?…Ç)•u½pq«.·ØÒGÖ®f§Á4$—3ohÇ•QÌôsŸâ§§Ë³‰w‰ç¨ÀLA½zêlH\Z²š	?À•¼\0à`uÇ4zfDP`ª’£ä±+RO.˜æú7Å7“2bqÁq—KÂâa+‘Ù??	?1PEbY%<à…İYj:q:D†0ï@®AÜëŞ…ÃHÆ×§ c˜zd´WDF{&2Ró\0r„0Îğ‰‘Ã%GmoÈc^ßL£#8ÖöÍYt$¶;¸?ìªD}:\'7F`’0UWodcóÏ¸=ä’Å<qĞÇùO?!{J®B!müo#‹²rE1NTæ^‚ú‹?ÈM(\Z¥–b¤ZF¬‹°:ÌÄªX]c—Qí§Æ™¾JÓ?^r³Ä^–ø{GşuÂ«¸t¾Ôİ›øÉOä`ê],»{†¾å»[ßÊˆRZ€ãŒ–\"ùª]¿q>¹È¾ùZDËë¿ÙÈ¦<â\r}Ò¤İZñ·Ç\n1áçÒÙwØ™?ø‘ú…ğ]/(XôÂYá¹5²¡-fåxù+è8D?õAûğœİÿO£$ZË¬R¼İ–:ØôË|²6]Šz•¤gÂì|ŒFa8hÈé2z??Ÿœã3¾·ğ€eÜq‡`Ø|\rVï ˆÎ&:{7ç[/€WÆÜ`Öü€)a>œ^@æ\r“<v\'¤;±¯&IsE?Tà.Í±«4#?z>c†‹`Îxy¤o?ÑzşŞä³?B	ÿ\\tÑ/À:ƒC´iÔ½yZ£t?q‡>û1ğÿpâ{ŒóQW€8ÚT3\nGLÌE³?TG.‰_¥i¶.h¶6WˆI\\,R?(\\Vï$á|Å\r¹Qüù‰Ü-~×¥Û!î©1:üi1Üºè¸áS\"¶ÈĞ¶lX?œ	šo{Í÷¤à@­=Â\0{²ÜA%”.^1i ™Æ[„ˆíWñ†6)ª*ª`—Şã+¼*ğ¾êò}.\0Ğ¿FĞZúdIçkF(Éİ´ç?é³ô€»»È¬´¸8a±¿úçKjGú1‘X¿lÑá„‚6¢yo¹Ä0\rá¸?1Lë,¨€4ºãöÎzäá+Û0ü.Iù|xÚ‹œEïì?íGé2lë.\rXîÍ?[7Wbœ]†w)¡<™8à?ÍĞW‹f=$âcÌÆ¢!¤7O\'ÎB†?|—ıbèÁv_<şÿ\0R8f\\éãâ:Í˜ÁDîiO÷4íé1w}8ÄÎã‘i)\"AqB)1‡\r’È¦?‘\"`Tƒï:?rã¬u.·ùIc¦]+&`’ŸÌözï.Î?y‡mq7÷Ğß1ã${€ŠÌ=M‘yì3ÅÜ£û™=îŠ3óL“?ïHÇ#?$Š&½‘Ğïàšw)={´?ßŒTóG\'áÀÕ;OÔÒ)]_D5‡8aÂ‚““9Z\nC<‘üs0?œ1ÛÑşÑa6#ÕÂ(ÔµœõÃg`\"=†Ö°ö¦¦/sz\näÿ=Mş?fëÍp6¦n$”_EKS&ö€(ŞÜÑ°oãŒFKğGZxF€iıaˆç´²§UEÜ\'t˜È	ì_6ONæˆ\nÆ£Œæ¿âRÚä÷|‘î\\F&°î×‹ğ:sÆ»C7ö¨ê„[PyÃ?¦Ğ*Mƒ<¡7Pê?-®x…Ûm¸õÉUêÚÏÎç\09¦±t—¢YÁCr†_<Oèy¤ı<—Â>hsp*ƒŞBKÙ‰“’í”M\"¦ñ°9EÙ–6š°_gQ ·+cBw¹®´8âıêÛãípŒZÿŸBrjœğü_ KùÁô÷¤/YñÀWÄÈYœ´”hX”˜s7˜ãğ!?q6?3ŸoqÈ-™f4o?00c€ÚÎŒv|Yù.™Ÿ`»çg1¦Í\Z¹“DŞ¨?›\"‚\\à–&Cî*ˆĞ0„çN;Prôl\0ñè°kÄ7óĞ² ÊEÆOã.Şœá¾€A?=X³4ûsd-ÜI`Itˆ%4áyğñ5øn™ELi\'’$®x?v{Â²Ãİ0ÖÃã·Ç¯ì§d?yúGŒgŒy \0\no¤ñîÜÃöè¨‹ì\"ä¤oM¦În>?F\0û²ÂÙsvR ˜á°ËØcäv¡§îS2*p”  ¡Å½œ?M™§ø]Öªô\0…êâdÌI•7dmàL9ü „Ü§ÈÒ?û–,ôjáš/#4l…îüœ‰WÈùÏt$¥Oğ©/|{û©tcp=_1‘tq(_¬Ø¯f,I¥¦óƒ Íï¾S6ÃB\0[â¶‘*bQÑ¢…Ë¯ÚÉ@>î—>Óæ$¬è•Ÿ,a‹ÉN¥²x$œ×{Î4¦„œKğL©2ı5ã\0V235a§‘ ?~şü_?ŸŸ1¢ğßÁ)€ø$ğƒ™G½¼œqˆ‘¿\\Çÿì9DıE†¼@¾ÉœÇWhÕÇv÷IhAÆª?]ÁÅS\'`Æ~3zËäÄÂÉwÂ©‘‡š\Zù_j¹À-qQqä1Ãtc|ë‹I¡²€©?ùÈ¿!7ÏŒq…ˆÂ°µ‘ß5æAŞ˜ÇœšàX6íTó”0™ã+Ğ\"5-RßP8bÏô÷)Ïô#XO!ìfcÓRp+r?¥vm°¼aÚ:ö\"`êı–ÕeÊ+ØÊ?X <X!ù?P¶Ê°¹!òwf™ğÀ1Ü†{JB•€ŒnùYœ›ÑÚ)¶¸Ş?‚ZÃ/3Ø‡wô8ñ£G™ı¬Æ %Ü\0x˜gOOØt{À¾œ)0&c?8S±h_™Øídáœáat¢ÁsZÁ)ÎÜşAíäù%“‡pöCçeçÉî®ı½İßUòdf|¦ÜK–õ%üI¨ÜûBßÉ@Zc\Z¶\r…CSâ|-Wóœeq\ZÏ¨ŠLÑzùö§´jp¦4¦œû’»?ˆ‡?2»S¥PPÑ®È>?y@T\0D-ßÚE/[–\Z|X¤ØjŠ½ºbZì€-=:°LMN˜q™\ZÉc“È^PU=9aµE8§mÓ){îk}EäÅ†,ƒ™pwÆ£„;t™w£ïpÌGJ«bŸ:chü‘ˆ]Qu·c9t;sLQ¹L~IGîØr¢3ú2KÀˆN·ˆp??t¹[VÂØa†ò¿!r?ñÉ€’¦AĞúg‡Iİ±¥µ%â/˜ô‡ÍTf]…’öX‡Bm@=…vìÙŠ©ÈàWå‡Ö¿åæäOşÔûä’®wØáîr¾Cº®Ï»–àñV-L:P†s?\"jÅm ¾ïÉØÃRlP)§V(8¹)`r~*Y\'?\r.\Z!-vİlRì: êb„\"„o»£OÈS0üN,âŸ»¸ù1±Æ•×\0–†r4cY,\nó’ˆd²¬C%âPõ)R ? ª÷ñ?$’o §Ç×À‘Cù›wÊYGŠhŒÜ9*dã,¾…q¯RÅvğ†\n´ßàD\"ÅĞaÄû*˜ÿú¿¶Ôƒ«òh¤<É€ée¯DûÁâìñ3ĞI¶ìÅÇg%Q>Z?ËXZÈ¤ÿ[$8ÆùG|)‘9‚,ò?Y{‚Idaîß…{içŒ§s÷ë[7bR™‰¿²P<ğë‚”<<d‹şÂeœE? ¡n_>ã+f9gÊğ]òÄP°•8Ì)?Â°!ÍıÄòL©rt&ƒÂñM#aäæ?B˜\neÊgQ¦õı$Ú8zôıé£·¬Ù82şû»§ğfüˆääc_5mi;2¤ˆÃÎô(#ë1t	Êğ ¦*qcŠ‹”SèBÜæÆSôø”ÙZ¿¦øÅP•ı<Ñ ]?CÛî\";£M!f?·òÈY°J(Ç:ª?qg\Z{‡ĞIÆ¹ù)?ê‘–$Á„S¾”×úÖ	Ïšh@›ˆ“j(çŒ®ß!~¹Xb\Z˜?‰˜?L”±:¸î-\0ŸjœFÏ}Ë„±~KÇ»‘Í.ÜÏ¤…”³ÂÃ?i‚?¸]*†M_¾ÛŒFVbªá™˜Åœ.‚OW‘lÀjFì=´½ cŸy“†‡C2s;?q*cÓ1,áÂ|ñ[³B\rÖç—ü`:ä£ek–¦œK	hOä~ü]Œ~ä$À½ŒsÅé‹d\ZíbW1àÜ‹Ê@t?İÅS_r»<?^Øö8#<=ê Ø2ƒ[Ğ`òÍ˜%Ä`CûM0^ÂQ¹Ô )šõÔ;‹!6Ş(tCd‚gÊ«/h/dr?µccdtlƒõ”±\"¹rµQúz(óÚJ,rpÿ?¢¶œºÚ°£§AâFÂ?u›4Ø\n$ÍÀfB™<]BEè8ù¾ƒY0óäR.É?úËÌ™£şşt!ºyO2¯yîÂmÏ ;R´R¸´ˆ4Æ™£­’SĞ0ºØê)¿Û2¾hROXsˆâ6Ò5ø¢âİöTÙğã+U¤0xUñ&	?åky0«?ÿ’5ÊÕ?Òf0¡äÂM¯$Fw?íÁíÂ\0?7ó`6Şè?5Èğ¤›Á@yÕâ³®[¾ÚhßûgŒDå€!ÓƒúûÎêóË±ş4úîŠK±¸Ó\0	8äâ)Ş0@ò\rh£SÅ>?.?ˆÏ*f¾\0òe/\r†<ŠÎ?¿BÕ‰\n÷šÂ’°_?ûİ[ày\r†E=¼¥ƒJÓëï\nß#ß×ñWòtã)„¦IE“ÄD}ÒMu§°•Î•!ğúqÇ?Š8ÿ÷Å?ç«™ÎŒ³|¤í¡S¼h†âQ¸oV<ö‹Ub¡w¼€€.{LWg¸Æ-3@\"pÖí8g Œ?Ä¯y‹RI3Õ`úGG5I.ı¡%ÃhAJ#?wÄ}ƒ|ûOÃº%…¨Ğ	Õö¸·½Â¯’‚ğ{0.êD_!!Âÿ>v òŸt<;¨Ô#“\nE¸?îÀÓ3xvuì6å\Zg‘ëÌ?a‹Dlbò¼‘lPfCp‘|G§û9#/ÜÎrQ®¿üÒ\Z\"HàÈ@?	™Ğß~”,1æM€6$˜òh ùDş\nPq\Z\rËMÜÖ¿¹ğœ±%ÊØOD™`˜©êˆ#S ²Aš›Ÿ¥\02. ^p€A6;µ3T™Î‚»2´­ÃgÒÅt‰GŸùfµ\n?5†íBF?s óŠ.p›Á{åô[¦\013¯Ká@0ëªÂ?Ş ¦÷ÄöjF}~Ì	şaéÛ¯]ÿŒ­ÖçL…âÂ˜HyÂÊÄ½,0pB˜Pá.O‘<	í‹ïñ0É;µuøÇ.@ÁÈzÏuñãIæ@m¢V‚/ÿ»±6	¯Š(¬”éVd9÷ìŸ1Ş-?c	£%?E\nF¸¡Ô‹¾¥\r;J;Ä?”H<|Ó/×9-òñ–°×„…®±âm’Î+¶Y­ØY¡ù“¶²Ïó%?óÅGøøP?0@d€g{Iİ—ÁyüœÛıT´x©Z©¡¦i?_Â6ã\'b[>ŞX˜ÆKœ$‰ø>&FÇ.“Ÿş?dª“QØ›-}¯7úƒ6ÒpÕF?õOÖ´á»‰\'«÷lô‹lCòœ)ßE£c<tğ\0œl˜Í†sPîÂ¤Øçî$èµ!ªlÜŸ?Ø2Å2îøŸ“X•G<ı€Ò$P%g¡@Èu[5ƒM‡v-ÅºÅ0rä®ÀÌ‡‡q\nSé^¤sÈ…«m–è|?VöÓœ•r5€ 9MÃ{¥la-œçÿÜ‰Fîâya¶¨‰*”\nü…<‰c‡tfEàŠ´r¸QÙt³,<6¾gÁÅT5”j	¶§´W†f®j P>Œ%ùq§È¥€Æ)ö2¡á<Y,Ùt<fÜvêõ2”$W?Á¨éâJÜq–[¸Œ`¼BS¬˜†SBxâ?½Dœ`€dÙfæ˜Hùï]’‹Mlê(¾[\"Oƒ`£/ZÒj¦	\rJÎ!U0mú‰(MÒá	/(?QÌêÇLá§2n0ğÕ\nZÇşŞ?=bÔòı]6åÊ‰%O˜>IUo²>L>o†î?\"„~.º˜\'?|äl’–3ŸJ±ä>¤\r‡³1’\nÓ¯iw»ê`9Å—3É\rŠŒ(¿íØl,çè1ƒJJ=ê9´dUqWp1>wrø†¼æ’õÇÈ§¸D.AÙ8&\nŠ®},¼?`É‚ßÑÿÛ×§%Àİ2>Šbs…6?bìËl«úÁy²B”\\2NH\\øGµ“Âµ£”ĞN‰ô:Èë†\\%Öƒ ”ˆ»’åè¤±¸¢îT’$ÔŸ8Õ’<8š“nI=î}\"Ê;œ\'‹<‡l•`‰±ÇxæŒca‰p?ä‚>Q¾ ¬\'c^‰úØ_Y¦EÑ*ò¦¸Å1È„ÎØJtÕIbügdÉº\\”?ù­1Kÿİ±½ßLœ°N?Éz‡ƒ™¢rÉ?ßÅÓ1NAı\\ÑÚƒĞzIØJƒ\"à%HŸ?\rË1’ô´Çt²öRÇKõJŞCÕ³‡ŸH§zuüšyx0\Z³{øg=m/‡U†•¡lÕğCÛ si~¹#Úä~æ}Ñ6—™¹§ší\rã˜r<Ğ6¿Å?}ø—ŠÖ7Ú…DçJşg™È\"Š&V*Ÿ÷¸ğãø€h8Cv(_¢\ZÂOC€?­Ô³6tØÁxğ¦D†JÃÙO5¯¢zîS¢M!o?Økk\nÇhÌŞH\0(W$t	ê¥Ş#Îò¯b\"\"éŞ\'¬ûÙs/bÑd¢OÜ\0\"’Tˆñ‰Ö>ğÔÄÂ¦ôãüÄí8‚ÖÂw`dØh4½xKi¿×ßGªÀ¿ó6¤öéÜ³¨XB_ƒİÖö–BŒ)ŸGHÙ\nÑ»w\nîùÜC;\\Eâ 2Ù\"ÖkÀˆÍlr:çÁyûyùK~AÃ?á*TçÓŠ3/~ñSWä5ä‡o!¡¢@¨´·-f[†gp¶Aß¤l1q¶ú4yĞŸÜ·N\'¹ûQ9Ãÿdeÿxròld¸5¤†7wEêly@á%[\0p°ò„öŒÆ`}»¨U«aÖŞö£Ş¿zÏ›ãSÁ[”gQØ0^d?\">i¡öK†?C¿õÇR­ù2#R§Rf Ô&SÉB¨Š[5‰œ¨¢dé$÷ÉÒŸ;´(!gÊ9„	Ğ‚L¢”s •Ó¯z_1¤Z0\\)?‘<²ñlé³AŠrÂ\Z&BÅ6rùJŒ]dƒ³ñsÿ-Á…\0Xê Ûø÷àTÏìbj¾—Æ2Q²…\'K×/˜Ñ–\\$üêµˆê\0tÈlz#ÈĞx7HZ±Â\r?àHLè6ßÏa¤FËé?‚Ëe„\'·h(³°l¯g?‡,Lz¤?øQyV(83?;Ğ WBÂ¦€öâ1(<Üq–®Æ‰\\á¡E‡‹wlLfäQ8©’ôb&YÃq@1ÒõÓéà^Á6t¹­‘nÚğ¯ã™7Eb*;âŞ`å#Á¥ƒÑqÜ»ä•¬‚–u„Ÿ<›1B¹”E31 BŞokl?Çñ¸D–Wtˆ3ZsĞ¼á˜Àã>0fñzõöéëŸ=öêÃ1ãõ«\'ø[ ”÷øÂşõİOöËç\'?.ğ_Ï?ØÏ?|x÷á¸cŸ|øÕşçO/_²â—öÉ?Ïíw±×ï^àCwj³Ö?=yõ_Ï?­Wo±ğÍã=ï-´z(Q \'ˆãÓq¬\ZÜøíA’‘R\'`Ó†I?‘ô$Uò…Öa¸uìç‘ıò,8D¨äÌ¦œ=ÿ3„ô1$@<ô\0\rªYY•\0=7\r•D’ü‚siÁ‹FŠ‡$2²Z}[R¶ÑT?ÈBİmÇ/ñ?ÕıÚåÇ—?èi?ÓòFcO\\¦Î[å[C”aŠ6{Ÿªã?ˆ›8tÓ–jïº§o¡’3WÉ…ñ?ÙZ´‹á-¦à£]Ës)Ûò\"áÛ	ù¸<<şãğÀf z‹1h{[¤J‚a1¸—îhÉÓa·cj™7Æ” rüdÌO‡gß±:pPÏŞyD<ø\rµQXkmíÃ\r#cçCå%co’¾-¡;9¡Ü6E——Š\nH7Ãà~æ…Œ¸W¶…ÌŒëQä–¥½Qn%3Rï_²¿Òe€Ñ›8~îH‹·¿ùyõpùVÂóe‰ÿxz??q\Z œ\\5ƒ¬7Sî»Ä·0û>¯ñ(ÔÚQ·¢ä0	EüÎ \\ˆdùGp‚ÉS¾1Ítc×?W‡»lxQìåd+ïÌƒóµÚ8,\Z\"?`Yìø<\n|[÷cŠ6Á”ô\n	Qä*µ¢T¾b¡g\0É3­?ì8bj64¾©ÎóZB\"[:dt¡Ì-&òIò„EpŞá¹rıOqü[eì•#Í=.šq€¯±İáùSACtæ/ÀÍ²¤¾Æ¦‹3SààB?Q]p\"R­Ì9ÒåZàşu> ’*ûQƒ¡Ÿaüsv\Zÿö@ÑßÅtMPpÖ;ïÙŒgÓ\r·¶?‹ôsØˆj©í1ÉS¼Ûg8]A¾á\'Û?Ú1Š”Ú·”„.‡\ZTÜ±Å´ğm±I¾3|ªN5gèÊ³Œ\ny?Ø9?hY‚ppğ30­¸ŸG7€E5\r;VDy;âõ³w$Ã¹\0äËîGÁïxê	î÷–©ñ¤hç§BD‹ÚÖ)1âŞHÎ¼Ñãß“õ Û·ïNp9ïóÈò[%·Ø=üjWwÛ|ÿşÃóGÚ=äÍ//mËMæ?‚IÑ ¶‰cøæ?;ÆŠïÿÆû¤”«ºãÀD¸-JM?..ë÷¡£÷fG‰Û¾#q5Š_ÎØf\0)îÀ©pºJMùH»¥N^š?Yøµà˜l?_,.Î=Äà>´÷¥ÌÆØÇe¸±Y¸l95.Ò®‘0º?Ãıkf×Ô1¿uSÜìàE\'gh†Ã*Ó%ş?èo¶„\\Æ[[Çè‰N¯x«\\MîUÏ0¡!?eÎ¾ãkÃÅâàk@Â¾ ½¨Ó˜Ô\"Û?L¥8B¼y—ş—/f?“ød?Ó¹¤q\"PT¹!PÃƒƒø?x/$‘¸$L(\Z\'¿ZÂ9;&ò&‘Pi§˜˜˜È.¶#Ip)ÄÖ3ÓùkÖ¸„˜:Ü£¿ØÄ\Z¿PñåÇCzx¿œs›bEğñŠ‰A¹i0¢|sÌøa‡)³‹^T¤\nJ\0–’¼ëoSÁÆŸ¸ğ?€yÄì‹Ü–j*?øjp¾íı{lï]Ìh´è@¸æ†ìÑÕK<(7ƒ?ÀÍño‰V\Z”¡/„_†JS*W‹½T´;<ÂPˆş³tp”Û‚ @À„$0%üºbºÖLIˆ_’Ëï!”°‹ş½œ	º¦OúV¯öK?Æ¿?Ğñgà.\\Ş÷ı]+?Ÿcô/Zx©\0işhÏˆÀ„Âİ‘§C\"+y\\çN’Åg)÷j˜à>”¯w·ƒJxˆ~çE ®I¸P‹orÂb¬W¨…+t@Lk\0×B¨Ó”–`‘İá;€ˆ¥6©|\'2r0¡ £äLøÎ\0c×sáœŸëFfçî?ÖÌõ§?Î’«â]Hjql“åj†}2ßê1:wæ#ĞIäCÆ ¡Ğï%˜3±¦;†®œèŸ\'e·h8ê-\ZòşŒŞGGEñK~–– ¢4öq\0gcõêºê‚êª\'÷eP?@.º‹ä>&›´P2cKä:r¹~Åfé„»ßÈ2ä!¹üÜå·¸ÚO;DñÖŸËlÜÒudje²ßÇ\0›Bzœ¤šlµH?ºFG¸è]~˜Ïfó°4åúî¢z›qá½$2ä·³L¦·®d*ûŒL?³¿+Õi&â8ø?o*?Ò{OĞÓ jñ˜.yûq¨\Z§±÷.˜‹L†?){Æe®¦á„14^g„a§Û?\0(~Y\n{?¨#Û?^iw)v0Ä’¬ëÈ›«?4ğ@JVƒÀy°½gôî\0^MñÇébã/ X>·¨?ñ,q¿¹a6©L,?úÿàE9ßáÁ¢‹w:¸ãİ¬ĞŒdèø0ø„»çìW9\0ˆ)o?B•È®òİ|…yJ?x>ÌĞû£Ôç3/m?zÃşËL<Çw!®æEqŸ`äâpŸŠŸJ]ºÆGåEiZÁ‹uA½g\'q=.öÙDBaö?üúşylkÑöÊ³g$(8I,[¼?\r˜6ãvx`$”W\ZfÚVÊ§P,ÇI™Ä…0²øî	bL1ùP:]5r™<Êh?§Àö\\‹5Mi™…mÉÓYLÀÃU„¡ğRn×`rÖ\0îcc\Z9!¡\Z«T?_y\r™aG^b/¼½;<qEAVd&€l‹ç¦ˆòºñğH‚h9ì\0ÌäÕX}Ähö¢]´*d×dcÚÆ¬J(ñå—`…ÈÑ$)ú.N,¤\\|.› ›ÎµóÎ|ùƒN›2póùî‘¸i1ßV¬?ÎĞ•r Ê8øBäNL	¢³ãòF…#é$ŠIH¶Åı	@.Líå4VüÒçI 0Ñ¾s‡›¾Ü|/Å?ÿ¡Ì7/a“ùÛÙø?s‰Ì\'ü?ãL˜^\0¦Må`G˜Êl2V„C	]ÖÄL‡@‹ÿ±º°¤«Yr‚œõZîV|è¬ì\n÷\"‘Å°%prp  Kø·Ì (Ò5ÅOü\n­±â·‘©pií(¤,Xğa®aÄDÅ„‘®Í?MN~‚Ğ¦¹—_?CıÉ4¯?Ä–`‰ìä\":\nÁK‹ğ#S>]ñ·tG%¿¶L0j?_\'ƒl?)v?˜IÜÁ¶Æf`n@mŠÆÀCGpè°§8Ghg”}LÌ\nã“iAìúZ®L±7M¸–1†&Àè¹o#?ÑÜ¿,*Zj:+&-GP•áª¢Ømê¦r7…È&Ëñ«\0‘¹ö™•ùwätqL2O&.}i>¼Éëö‚¬½1!?¬ËøK¯gí(‘ÃŞĞş?ö5ve;ÁdÂØµóT¶?šQı‚¯ısŞ³ ƒ#%>TİÁŒZDx×øÎ“ú¯úo?UvÈ:âÆ¿ˆC±¶¶?Üs?ø\"vÜ`Éb>ğóş³*ëI-ÙP…s4¥\Z`¾()şCfxÃN%g[Ëœæaª•à“±öprÁCß9€üD\0ña¦XŠÙBnc•O„´ó‰K{mÂğG1º>ÄÁÆ\\öÒ(Àlh`XÒ¦\Z_ÈËQ8-¶‚ÓX °ìX`Ğ3!\"_’¼ğ L$ù™V*R—\Z×]|D?²/Ù²\'ºÂñwì;ü›,*ï%ÖêĞ™:*ÃH\n<‚{•0qUHáÙDx–²&1´&ï\Z´ıøà8¦W„Jª”;!;whk×ŞÁl wøéÕW?N—“	ÆSBP×ß=’_kÕiN	.œTúÉgÁĞ(Y„AĞmè(¢;éì9(ğL\'ò`NAƒZ~ìSá.eE°ZÄ&@äÄg=Ÿn€#/œ)Ò¶ÏMŠK•ÇÉ´ïèõ“‡ös5	)PêV)~\"=ÒQÇB‚(^¡Œ XSí?kˆˆ?ıP¸F\nÛĞß¶½£œ#İÅã¢i²àî±BÊ İ$?x«¯ˆ0äe(£ü?Ó5Q?ê0hè8Êõ.õş??ññpµKŞ¹ÁüÓÛ§\'¯Ş½M?ö)æE§¸CĞöx÷2¼¢;t´,©\0’7ò8Íè©0Ï¡»}xr@ı{aŸ8\nABà6Êc°°Şşôú5´KŸùYNjsU?ôÁU<¤K*â©ksÏ<“,DVe·3vvUá\nŠ§Ç7ÆÄÃJÓb;k\Z÷võé”Û·L÷„~\nÕ=n(ašaõı—‹5æÚ“^¼C{â_‚ĞNÚåp†ÖF—ı°7Uºš›èZ´¥Ó7êçaD”^2Şš©nLD/[ÑÃâ\08\rğ\0 ªB{x;¤¯$pL¬3¨m¬V†¥®\ZsL=;üNAØÖÇÏdˆ”G[—óVY|Ì^Á)qX»½4ŠŞã-¹ì#8\rÈ>ÄÓ€ÖHÛ…Œ­aÈYÌ­;ä[+«¤-+wØbÛn<oàFxéeœÍ	)Á›Ü°­`ÃUlY/pË:Í\näÒó@¶ó?Zü}¬Aİ×J«¬Ö\'ò{0³z±#ä>—Œ¢K¨ø½@ß?[¼÷‹˜E\'PÄ+Şûëc¬í›À)d‚[FÑ”QÀ-•Éã°¯	›(¶Ív“Ô.µp!5Iv{†¥K4ˆn5GÍûÃİà<¿\r‘ÚOt£EH©şC™t‹.¥ÉµCÍ£Ô¬Åo—“-â´íqì†c?q2>£ÀÚã·Ëk_ \róì=>7 %!q3\r/s†=?n\n¾ÓÁ;\ZÉh¤cäC…etºŠèpÅ©K[vàsôüø»ì??{²‹\Z‘wCÈôT$?ä×·H&¢dKu +?\\ÀÎ0r›I\"¨‚§ûÃ]ÈÆÀHƒc94Œ09\nİªQª!¾ÑØ(7—hÈ VI%i–gÚä<Jì\n„nD7rğıL^?ßE n¦ºÉ‰ßùµ	¤\"ÌG¹tã¤¥q¢9n§røc¦ì©;\r.,Œ?¡È‹ºä\rŞL¹™`\"^°ò™îöÎ^»=îã¹³Á—1Êú™.\"Èô|¨nÅ?læŒRE±bÿŠ˜8;Œı&a?ÿƒ„ZAÌÈ6ğê8/%fËû&Ä ¸ã´tßĞY	0w°Ş Õ:6í® İ4˜ADy”?=—¡¿ˆàå¸5­Sş»X·Î÷r\\ òaŸ2\ZßË±UVá³ó>•§Êf‚\0¯zVR£Â°;1y.vZgc+&o•EÁQ\n¿SÏŞ¡ 7UÓ¾QSLñ”PJî.Ğ•ã9½]~o‚ª\ZOr¦Âúrü¥é«-x®²ôÕ³qïl\\¤­2ş±eµ?¹\r?¬gØ\"{fñ·a{.“Ã\rYÜ°4?n‚É\rKs¹ašÍ©N¤/“	Ä{g}ähîXFèC/O\ZÍ¦+	)óˆ“L`¸k°Ï¿ 7¼ezÕ˜Ş°˜ë\r¯\rÛƒ¬Éf®o$û#í3\\Œ~‘¿~¥_ôÏy’gpÎgŒ³ §™¥d!*×Ìd]\n\'¥üHÄMeX=\r‚FNly¥\r/?ĞdÇ…Œ˜©öH0Yk!B†Ñäó·q®ËM™óØ¥Ë äénpVÆã(PS‹­Yi¶fç°µU\rØ5ÙÀ¼ÈØùåÔxÑûé¬Š>`¤0ÖF?(şÅ>õ¢™3yæyX–<f\r¶#?\rsm_1æ\'Q–-Í\0ûĞ€¥™;·ÒÌ,Í.Og=ö·HœÁ²¸.g¼ÔÑTºCN˜\\È:²Æ¥ûş\r£EÊÀ/ÙµøQk?>åİ™ª/UËÖ\'nüññ=ÿÈš…¼1¡;¥+m“77‹ÈãÄÍ”x{ôXÆêZZÚ\\~×®;éRˆÏ	ŠæâhO‘ÌS`1¤Ü¬ø¡ˆÍìÙ¬Íoasgæ\"·ì€³ƒ9cóbv0¿Jv?…$Å3Ìš  ¼ã“ïŞ¾Ô8\0/B ·Àıët¡ô‚Ç?ñì qÈ?åV‡7Ä	RyRò”¨‚\\´~Š·Zî¥‡å‹«°Áâ?­èc—ëCÊ-)qÁwÛ!KÒQú{Ğ/ öî‘œG­…6YèfWŠtéğ».ğ(=˜7ü\\>Ù„6Nªm0[Ís£¥nª€Â?$Š“¤\r6îz?`§²Ø)”¸\'âMøYÓ©‹J¤â§8ÉƒEéõ§Ú?2œ—¦zàÆ˜K6è ‹\\£ØüñöÅ¶8}4Ã¿a¿\r\0PS‹Ñ^eœS^°%ƒIxmV×™F+˜.?\nƒ:	…^Œ£øá\0?ÆÉäL\"3tçáí¹®DcûÕ?_ÚZÚœãW—ñıÆ¦—ç9lÅ¥dâƒ»(&à¬ÙØ…$n¼|5Œ,™Ä=Ül¨gÛï`ü\\ş\05çpÏÓ‹„´Ñ²¥¤¡æ`q¯›³ûİêV:C	…{…x:sÓ%êâ‘yğ­‰\"ñÆ?-±f¸å]êvºV9µÊ_ñ*17‘%äŒ4ı	O-a³ó…+vÙÌ]¢İ×ÀCŠb¯<ùî]Ø˜Qì›†öÛ;ëuìŸİSq«Ã?áâYæ’-€é=Zà*àìˆ äqÒ|sI›Cç$\\ÔèØx2›µ7ĞÂÆ,dXú2	ûc¶1á/íß2Pß\'ûÆ~ğHÑ©kÉ{Â¨«<Ğ!„\ryLƒ\rÇÂı®ÿ.5Ò¦|Y?:\"’ä©(xèq,§¯.SP¨p©\"Sÿ(é†X£ëÈè£uÇ?ôó$¬ÇvY\"sœ¸uà` Q(~oe»²$ÑæƒôÁ¤ö1ÿ?’ş\nø%ú!†Ì:Ô2í\0(z¦ÖÈÏ?‚<?TXÁ\Z›`­¨\" Š“ÕÈT5\"×)?’ŒFRKëz?Eñ}\'q7^R~qß&W(¯QV¿‚l™È›ã+üP(©!Aê\\Ó|7¤fZx›´Œ¼G<C 7$E¦:.¾AG•7Dˆ	 ïAálù`?ùÀ°á?\'ÈíZª?mI}òîìr³?ÿ¶”ÀşŞò†÷©\0;7qÉ \'‰pL¬ïwøun4êÉ›eHä°ï…qK©§#oF¶¯£İYaÅoµË?àZV6aßÆR‘Ò€À§„”a€\\¢x¥¡¥ÜÉi<æí\'ÕI4ÉÒ&Ë‘‹·TO³S“Y}ÉŠ]™Òl½îVĞİ\nºR‚NKQ\',²X¢aà-	´Í\n³,®dfTºYë°?(LŞJ²MJ²,¢É:?™ö_ğã£v,ÔJÆmË6ugº¸ˆ”RÁŠ`mã9ÔÆ¡Û©TMÃ´?%‚´ÚŠÒùé\nµ	?ÜHm{£CTSp_e µv¨º0È\ZkC¬“Ópu1Ö:¬OÍ>×û1§ÃD5æOÆU\\êø¸õ¿Ö\'?	’Ì¨‘Yü§U^†híÙ\nş9²¶è‡UÍaİµ)á2Ó\n‰]Äˆ ¥«‚Î¾¡ú»áÍpÓyB{‡\"“„œœ¥ÕxC@|®Æ°ğç‚°•2L´¬Ñ\0=Ö³\ZğË¿‚Ù€­m7Ìãi\"ŒáíÅüj0Ö;)Ågƒ\n9ìåò‹€ãìÍV<+\\³™œÅ‰MxâÙmØ\"ı¾³?-â¾0eG³Dòä\Z¯å.VrîJèÔ+á&”Ÿ¼ñdDd¢\rÂA¶c³¢·}k\'İÚIeí¤‹ÓÙÜ`(vHÛçk#´cÊVl(ä½\0\'“?ÛRY2Â`Kñâ5S¸lXSÄİnÍ©+vÂ?)M³x9nÉ-ør\\O¾ç)¢/ãhû„ü·Šä¿],ÿ­,ùoqùo×—ÿV,ÿíµ»\rIËO \rœÔÇ¤›¤x¾xFÏøU¨GxÄ×›N´G7]Åw,ÒíBô?)Ñ?\'ĞN 0\'Ş*v?ÙÜ:Ao…{iáN§¹¢]?\Z©ˆr8Ùum¼¡/Ç…kß/ÇM„÷Ëg·¢ûê=¡x0!¼‡!b£ó¥ÿé˜uÅı³ÊR}ØXªGQşZ‚½ÌiµbÁ>¬)Ùg„zVÎùBüî¤áÂÈ1¥ÎŒû‡4ƒ7î(^I?W?°b­#´Ñ¹· ¯í„?Š-©ø?¼B÷[$M›V\nUÅ?1?`~Fy;0EÄ4^7@	@0~?¨2˜pyJÀÈìâÒ3ò»„å¯WÍ	|yÚ?£Åì®ŒMcã…Ì6Æp‡‚-\nF¡\\ÈãKø…¹‘¼q†?*ôx”8‚}?ä9?)Ú›é8é9Iy±­$N•K]X‹_¼9ùøáñÏÀDnõ¨[=ª‘5ÌT¤†q CĞİÓwoÍ?v-¬¡QÃ\Zf¨XiYÙ’ê5l¦{\ro•¯\r+_”S[Mœ›¼ )y™OnfÜc7z~ííGªò&JM¤xIÿ¬8aâıpU‡ˆ¶qÈ?È@ğFKº&L¹·ññôÂYáN¢¥‡0ÃÅ`¾Ä¹é›ŒObAÛ£‰¸&…gZæ¥¸gMw‡ò–R‹]®r=Ïº%nuY‡c€~r¡ÖLd=I\\\'jïğK‡Äõ»íË“-MxEr7§Êå¢ë¹[”ÁÅˆP’·€#|.ğ»\"Kä:€vúô—_*å<m˜­æsO¤¯”ºF‡_zòLÕÀùäÙâø\0Ï³õŞ¡ËMÕ¥\r%Æe=àëšÿ{9äÏÃäB/m ½ÆÃ|ñ)°±r›«¸\Z\n.§d–ÊšĞŞaP¬HĞÿj¸K\'Û§¼%¼\n^¹Tá±?×³ßÊ[)e%‹*ñ£3‚“„¼z$­ŠhÍÊ\"6…©mG«©‹¹ÀèÅĞfù	\\cGUøOÑ\\ºY×J”´?_û–­Ÿ]oL—§\Zn9æoTV…ë:­|ß$fõÌBN³¬¸|CŒ+îP»âSè§LµÅ\\`^8‹qˆV>ÓşNñêT+VûÉgc{Ğë3Æä^Øò´7?‘+±G¤„?ïÂ°3°p¦iÂ3ŞY­h¡5_Sß2™†ŒcûQ¢@kƒÜ;F|$Ø¹º™«[-quŒ|B_§5·È\"§Ut:j/ìÅÂ•[ıBZGŠÙñ?z>nYç?d?*ñ5å \n›IòÑÂ@å?LZd!¦’Å-Tdè‡Ä-é7ŞY›}ò\r¼bÎßHÄ}vÁâó\\Æ$ÀºvÁS¼·¯úN![À«iĞ‰Ùà¢ªõ.}6éïI¿…Ôz‰¹7ÜæESñÛŞï·Ìc=Ì#Zxè·ÄÎ8¶û¿÷.Y—ı#¥dÅJâ‚Uyğ@-Yé%CªÓWD•Äº Å\nÜ»Y\n›âV9,Ş?º&û3pœØ™-‘Ê	qÆuY?‹¢CõŠ¤0s#+ƒİj7?ÄD\'4èNuá‚Èğz«šBLàÔ0ı½·OoÒ\nNˆHuk¯6¨=hıjI„ æëzè›Ô<º­ûV?¸vÜÃ\0Üª}B@=*X¸Z¥ˆÏRô\"¾í©›®:³ÈViŞ-#²ú± k¹]8ß)¿ÙÄærÖ`²CÖî?â¿zTâå†´©D¯?*¦ŞøV³ú+ñF¼7¥¦v5A‚ª¬d%©ÿ¦«Z?#}y³c¨ß`­ô|á<?8ıãf.o»h}³nMØzg³àN§Ş<{¡.†6\0Ï¼ÏŒ??ÇvâËlã?KÌ??„Æ³d3öN{êä¸uX¯§î™‘%ú¶çAèQWß»@¨¡­?ñãç’Å¡dhÛõÇéï\r¹Úqi‡FLaÏ·â¥’:S8åQœ*ìÙ?í‘·ñk—O)Ë‡Ç8ŒÁD¯c_€C‰‡‘™\Zz(ßĞy.+…§Sêº?ñ©gÇ‡GÏ?Óˆ4d=û¿(¹/¶°ZG·ÌáÆÆ1ÆËi\0¯®\'Ç<ø‹0ÌÊÌò•íM‹EÈc•‘?©¬8‡ˆH\0vøà~ÇŞ¿?Ãd³Ø\"°ù“àI°»‹´ú#Ş\"?b=•-–g‰jOöd\ZcŠ[Ä·œ‹èÑ4ëã)\Z°OÎv:\nAg’Ó ¥Q0ŸSK¯CMÒ?ÿÃzöHn‹W!0¸Vº\r?BóÆîÄó•¨;¤o>€0Ï*gº¶›J@…æ*6.E:H¼]ˆgŒQªÂl™8Xa	êRû9ñx<ş€ö?€°çÃ<“Lu´HÍäÃæaÖ\"`ƒøqÇ>gÚägÌ%Á\0ŒÃ>LóêAÖÑrÉÅ¡0Ø0¤Ğú0²|RCê??22Zl\'+x¡å^Î)Ï8$+Áv±K÷áP>Us(„\rÓVÓ?²ˆiºNHÖEÍ\'‰q<\ZŞˆ-ZÊÏ2vİ9²¤Wb‰Á5ƒÏãƒoÅY{¯7`’è³·|ádç£3¸xe¤CI\06Å\'î>¸÷í‰ú¿‘—O\nÁÇ3‰b\'OŠ 8È¿{»s‰ÜŠ ›ëÏX“À{,±‹¬šâ-3¾3äÑ\Z3cÄ¼Åg?³›´F¤0JEŞ4—CZĞ–?ô­&T7(L¥(B¸\\àÂ†çû:îƒ¿iÆù±*],ö¬+ÚQ$¬_¬F¤Ä]Œ·Ë|[§+¼Î\0Ut/àÄ–#{cUÈP¨Ëâ”8,?²@¤ár\nc%öáĞ\n9_Ÿ\"¨ir8z\"Lö#ÉÙ%Šx%ÿĞ·hÃ¤«:Ë\"—´%|ø8íçxJJ\rÏQü>åOcè$®.¤”QØ$òK’g»¢á˜;„5Ø\0„˜Æ	’‰Ã´ÅµG«ã/§[Üê·zÅ­^‘¥WĞ?q¬xáÎ!ù>ò£-šp)¸<’íèª†¦bÜİâØ?0=ušK\rwzˆÇSx_rŸâ±½?Õ·ñ ¿9F÷g^0ŞÀ°qîÌáĞ-ÿ“8ÈªH¸ıO8Ğ?rh{[Øª`İ˜¤òLÜÕ„©%é5ö†©GHòYqMğwöĞ,~ì¯È&–YOâK\røalˆåte1ù$ïÌ’QâP˜úÍËÇ$+“/è,‚\Zkò7\"Ú¼”ZrÜÃ<h°u×M?¯ê­|ìÏ§ù#«lïuh5ñCÌæĞşMx˜Ä*¡3µ·aÛ\Z(ê™‰ò,ç+,á7ÇcŒ ¢ğ)›iáÒ‚ŞU7;iŠ+e1)½Ê´”Ãoœ<Å=Z »ÀÄĞ”Ów²?õ*Mò¾.¼D$¡÷Â?]¤ÜY$˜8ó€.°FhÏ¼0”ÎíuMYQ&À“òõ=èÍõƒåÙ¹hDœµÕHyÄs¶a$*‰ã›ÜDótiè%4qÇóO½?_¾û2TÈ†£Ÿrş@<‡—™„ÒMÔ!Í\rR|P#zù÷ü0)«AÃ¤SBÓŸ¹áçøÍÂ™{p¨?’‘a?=Hı©Ğ\'BÔÁ¯\0#ÿYz£Opz\0šç‰ø¬t,¡’¢ò4GO\'±¶¦Di@\Z’EÎ\'—V._H—ëÒM.°IÀ¬è˜ß}É‰Ç~Œ™åğk…5vÄ8\0bZ0(•ÔÄ×8øYé.?¥`VJéëÙÈÈ?¢G(Òp·cáÑ)‹3?S€Åq\n*?¼8îHÎ\n›8Ò`ùÌÂÉ	¹L…÷¢ÖöT:Ôº~w\\¬’6Ú‹ÆCöñ4±µB^ãSÓp eu’\')˜~BÃ…\'mPEÑçÒÑkàT‰ÏPfìŠ%×É?d¨‰¨wå?\r.’ÆÓUyÔl& y¯šX+%UÅ¿Jm<©ùÈWò,48øem%£ù^@£ø5+^Q×IÿTšH2£ªí‰)ÉWiÁƒ’Òh¡0G¡÷Cy}j›ÕY\nzÄM<ñº¢æ¨•±•1sk•Ó9#™,¨šÊ©8SZÒ8±E:¸EÂ]¤i?9?pI@J#ºE\r\0ŠÔ9ùZ?ªaĞRÉQ‡¦ZY=Õ¸ëeÖSK*ÉĞY	‘Å*+÷ŞYŠûÎ.­|xzØ–ª{Ş«èš§BAÅ3}-½?oSíTà¹~Z\'y“k)?\n•]?S.Î¤Æ‰ÀVU8ÅÚ\\‹¾	?¯WİDğËj›1ÿÏQ6qù‰=?-åÜ®?6—¹š§µUMõDĞ¾\0Í51õÜ‰©júÒ2k?©øds¹âŒåšüoşÔ5Ö*nSµ9î|?’ÁO(•XjN±\nox(ü|íúgQY?)mHÑMïŒyq#|*÷@‡Iğ(ŠŸğ\nà±^ˆÍ×°?şƒà„s³z½ä4ÅÖªYÀ¯±oğÈ*¹AÃ¸FÓ%ßhÕ\"ˆ?GXS×I²ÔÓû~ÄdN‡Ëc©?—AØÜW	´ÈÚâ»§”1u‡«‘´@´şú3Ì\r	i¾ğRqĞ&H9É?¸	X‡‹äŞ9‹Íh: ò?¾fT•ÚÒ‘³ãõèc?ï®ğíq÷Ò£8S ±F¦h[2éq—SnNüHÑÿ(%Òä¼âˆ½…¶*?ˆxu2^¿KQµt-¯w?KgWnVû?d ˜ûpÒ—r^”4fÕÛŒ‰iXE(5åã!8‡zî†›?	æA?ı?­UJèjã#éø|‹/™{BI*›2Ê0ÀR¡EYt‘3~|¢fcøäºs?\ZjÕä±^„¹GqÃVÑ&Oí~R§~,ì«0\\Î ¶\Z\0-‘\'ƒàŒõ:yò(˜?ÂQ}2Qã	åJ³Ûâì”1}Á?\rÛ,aÄ4kZ<6]ÒåùË`ò?˜õ³¼´Š¯xd\Z@@ªƒ²CSŠïùªJF<KÍwºzhc\ZÔ?ş®\\¡Å3»9‘².œV1öxl7nùÁ†€?Á®%›±›Mô€½ˆ“2HaÜõÃ¹*)å¨g\në°Éæ§~¢ÀP_^¦%.»™0,C¦:2¬¸yÂßØ?½‘½#sÛğÒ1#Ÿ…wºÄ{ã„öYiW‡ü…ÈoÃß;üyÈo‡¿gi ?6Ş¯øÎ\Zö°!=İr.d1¢°g¿!h»´¡ÜHÄFø¤¾©¸ïÿÎ€Bœª¥(ÕÄÈ‘Úi¼¿“ó…+Ö şšL”^hF“½PéÀX:4–î Rßï¼?È‘¾8rçÂåvBì\Za†:³…ı•\\ælÅlñ5?ß¥g$E\r¦NÁ.›ÇvÜ»íysMÂ`|W´¿^ş±bÇ>İ¶l\0T¿4lMM›Ûm†:¤“®şgÉ–áö£ÿş©”põ	cîŒ…+ê ÄxÌpvóÍã§Şe\\B;”>}æŒ˜$9%òù¢¼‚\Z?¨Œ1¶pÇ4;ù\'N?Ö[Sƒ\Z)oª¶”×JÎ\\¸û2´•¬¨¦ÜÇ2å¬‚„—%2ÎV¼@´¸+Ås\"áßVÓÎZ<„#N8kë	g•áÌ_e&ÙX‚j¸\")::Ò¼³ÕÜ?Óİy\"¶¤sƒ\'ËÆß#d¿:Æ•½?ã_`%Ùwía\\ô«(bŸÏ™­Mv¶! U$³§YÂˆ\\Ä±)øz|dxùDÕ¿wğÿÿãow¬­£’Y÷0¿?~§ßñ&Ÿ€úÙƒ‰}•d[æ\r\nGxìL’ëZ–·GÔq›ñÂ^,QWÕ¶”àh/DîÉ…Ù)6`©aØ{Âôv—Ôfô§\'/ÃU/t`6så?´`…àY#È¥~q` /òâñXÏÀ|ÆwgxÏgciœÇÕ„O~4õæŒµO\\ºÔ	mÚñ™jÑã×ó|#é¢?;i*ˆcúvwíÿµ¶íSYb¢È²¢éb³Ú.L“ñÃ`!2¾l7Êï%,+?Ö±¨\r!†\råK1Šc½r1öM1¬ \r|pÇùã_à¯xôÇ¿èƒg|^{…ÿ’?^:2­.}K9\\¾¾şšá¯õGøkşùQğõ1­ÃN?\n·½€è‚{uY³c}ã¼„VûôÜY$.€‚¢t€tOĞwâ”\\l¹ÔN\\¦2ğĞ\\¸¸Ûá%??\ZnígÍNÆÌµªä“L\'Ğ?xİ‚¥=›\"J•×ƒÃäƒCò<fç¢z/NâL½¼€<ŸxNéœQ\rÉ ë0ŞyÉ{wñ€O¦£^’œ¸ÕfÁæ^EL¥ èˆ™RŸ]|OÆ°tñ|Æb.O<%~Ã\r\Zâá?;ö–³\'ÁtÜ±¨ä5ÜÆD[\nTğÒsà°?Øvµh ¾x*´·áÎM?Šzç ¿ÒC¨>ÌNáÉâOSö€íóç3x†K›Ä•M¸Õ½Ã·™…\'”~w{tOÜÙŸ³!;£ó]0??Åîef¡ã5Pt¸¬7â?*—,IT´8’¨Ò)\'&Ş$:·ÔI\"—::(‡qÀ½¯kx?½4”ÖTXŸ?ÍcMÒa&è?\"›Ò¨Áõ0Œôví÷dÇ€“\Z|ØxŞM½ Cˆq2º_Åõà‚N÷³¾^?ëhSË&†Í‰ö,&îZ*r·Çy2ÓúHPİFÈôcNÏ—<?ëÛÿïÛ›•šB, ”Xûi^M°5—k?Í+J¶µ\"Ü} SĞ4?vˆ†DI?Äûi–yP†Ro×ÎPÖ­€Ú°€²³”%”?P\n¨Ó ŠÀÁÍ¬ô`Ş±ÑÁ¾#£¼.Q?A\";S€åH0M~eK¯ó`áıÁ†Ë¯[v+ÀÊ	0{9§;<˜7à…ÀSÎØÉ¦` `®»?Éƒ¤Ì³¶úZA÷Ñù\r”€ÒÔÑâ”°°š\\úòŞ	0Òî„µ…!u†y–eè¥äf†àã&m‚ûÜJ®k$¹Ğ´ò—Ói—îÉEÑóP?wK)£ZZiAVÅÒÊT±’dÒ‘J˜[-x,î°/@,íÍˆ@YøÍ4:¢yè?s­õv\rÎ83@¾ÈùÛ?ºL(ô¶oÅèšÄ¨ÈÊ§[\r\" I‘–?°w;€v×ŞaßM]\'døÓ%&ÖØåÕÏyu]¦âÆ¢µŞD±ŠæQZ°kŠÖ?éÀÑ:eëà°{êE·\"ö//b¹€…xç˜.\\Ì\0?EqbÔiuÊpj³´”p¶j\ngXoFoD9h[öäü‘¥ï®_ÀÑÌ1Â<z{0T>xõ¶Cg°¿À-s¤lœÕY°pi/hxp¨ÎEw‹}yq0fÖ³[øÍdáşg‰a³´Üÿö¾>’²Ntvß\"/åóXvÁUt-!ÉL§“ô$3?ÌD3If&03‰IÏŒC¥»Ò]“î®¦«z’dW<—CÅ|°èSYŞ*‚‚°»xÂººŠÏë­ËÂóbeWW<Pyÿãû¾úª»ª“	Mø\rİ]õİßÿû_ßÿ\0Òæ‹„|›€£¨Ø³?Wu7ÖPÌÕY 3jÄoG«<[aÑ!B“¶˜ğ°ˆ.íh°?®Gò«ï)„ñVP¦)Ç€]c(2(êl4L—5r1ñ©¬[	~ßE3çâÿ…–\rĞ‚0à« 1»‡Ú-8Øı&Ò£3d£wŸs¥Sò¦ìdÑ eDêàt9#Rp$‹uKD–r†¸ˆ‰¢ÄO\'YCòcÀ¯¸œÃAèbWÅß=\"üÔ;Bô»Aì?´pE¥cG±Ÿ¾rG¤^µ¹ì?êRAğëGzWÏ •œ0@›Í%„¨?³-YE?¢«è1]ö=çHğ“o5¹V+[°Ôİq¢L,?562±sçØ®Ñ±QÎï ·ééÉ¾d?O¾n:8ÁŠ–Bk+0\0éZÙ6SÉyD¢«[ÚÙôâtY‡tŒD$º2t‡¼ï—º»ççç“°Å¶İ\'İJ®{H†ÖB†Ã\\ X!Tº¼j¹\\À3•®TyäânZ.Í^Œ-ÃÑÇ€\Zá¢fGÒ÷g	‘Á?a€İ™˜ÁC!Cå ”ì94-¯–q4D[ßtˆÙĞ#A\'‚[²d\0æÀùÓ˜µÑ?«õ?•Ë9¶×ª2Ësî\nKØ(áÀm¾)O‰®¨ùĞ1£	$LÂüC‚p|¹mtëÄ®ôäpz{(®29êC¿T•Kßî™£c[‡wïHTud9>¢§Ñ¢ÊBÁÉÑ	Mš†¶E«Fîß\"\Zú\0x@?a×:ødtÒòKa¹Cä?+™+TËë”XÈ®ÛtââñsúSyR‹¼anIØÊ2N®à Õé¦Í±³5š ı¨û¤Ë]Æu+Y\"\'*ÉL(^D³€a¦‡·ì3·LL?Mmníd²)=5´)=jïß¶ksë1@1æñ+=1ÙjõlêN?6/€¼ı47À„¹Oå²âº¡«Eû3‡z—Ùİşåu—Zzwº/ã2§×:´~¹ı-k~­C}Ké??8Wd~ıËío™óÛ°ôş?t¶m\\fwÑ³ë¦9¤ğ\'ûBdÁBFUz\n)¬Ø9?5)¨–0Ü&Hk¶½¶r .©…A-\r>&x‰2<ÛVqr°é€C+šŠöù–¹k÷¡€®™±\ZQñPÀø¼•ˆè@6ÚrJ€`3yÛ*›\"_¤˜j€Á9d\n\nÕ€áÂ`Œ?©32²ØÒ©xs­¨Lú´!b§ÈÁ€Ñ#¿IËjBÉM?”\n…OPìvn6xc/$ç³[ÿ€}£Í#”VêR5YÇ³˜“ô—PQñú\"åÂ8ÈïNoí:v™Ğ??‚3g˜­§YÅò`[ï©ÁÖHFIt\'Bd‘hBÌÑZ¤O\"Së’iW*˜úJpT@·,§\0âá¢Z³\'NA¦\\şükmPpÌ0şA#J]fF+Ö;ì?²}=IÙÌ,Ví®z•nÔº=xjw“Ö?vŠ¶‡ì\Z4‰mÂîÃ9“Ü\0q;Â?Û%î?ÇŠgWúïSlBà84–jÀÀ£´ZM-(:ğÜ&NãÕè9?èIÌ&¼#‰$|ëIxÂ™Zî4/˜w°^IàÃA	½ƒÒˆƒB<¬\'\'Ã²d¤€²\\¨z$Aû>\"Ü¯ŒLÌó0ó?RºøË†æ:sÃ QS¯ÖËWÅ«£Õf.$jÂá~išÌæzÃ)Ûs‡ëÔ†âaœÖpåè’šTªQš‰?*‚uN`à\näloçrá¾Åüµp%=:)%Ür— L’ed‰={¨êùÈg\ZæZT9eÈ™U4î0-‘u‰9­at+©ÎÌºó%¡Û¼Şì2åóîO6Â“e5¬K\0Ò´Æ¬{°ëË€ë½Ü{§HG\n\"ñŠLÓ­]O(§qŠ!?UÄ&s?g:½5Z\0/Ê\Z€Ñ1˜ØÈpzl4Nä‡	,\"ó÷j2¿Åè³zHcDğ6bt-‘\0eqö]]«\0’a•†¶ªOXÕ\'<õ	\0Á«\n…U…ÂªBaU¡°ªPXU(;…²J«\Z…§¹F?„Œú\'«:…U?ÂÓ[§Ö¿Z…¨P9§¼uˆ€e™!rÂ¶uzh¡Ğ«(õD…?rü1s”ñôÖ1#GVˆŒ|\ZUş?$:š…U`ñ‚Ñƒ1™5üÓBñ›„ödØá¡…kD)™\nËCtmƒ8áˆÿ(”k3aˆĞï4,8¨ÈÀ˜Ñ†3\Z‹	3»`2>?2SrJ‡İ¹ºà?œ9<=óÑL=–RQÑ…WÈgÄ>L¹=DÖ¢ĞùÙi-ŒÑ~ÑdØÁ5ÙĞÉÂ±ŒsGBr?¦&ˆM0Ä±éê\rE|—l€Ù!Y=2Í*eÜbQdb1æİÊœ²MTáÁ\'wm“6X<\")2ç¡!\';Ó]‡Æf¨t?ÀUÊ¥-Ÿí-?“ç¼3H\r/Òã+?-X™ºRôˆsGE×áì>áZâa|2§E«nß»…Î\ZÎŒB‚Âò‹}‰œ(À¼Û\\‘^2?²~U¤Q˜\'…?¥¼L=+?dÒF—Ğø•ZŒ–%Œ[å²]½É\rÉÔ±šø´\rßı„\'¨FÒZàåå‰–¤ŒÚÂ¸Ëñ Ÿ>é£ÃÇ±?BŠ7Ç†@ŠÆá°Y•L^ì‰ˆ}J¯i?ñ/Z-ç?ÛÅ¨sº.;0­£sFˆÎ	³}Ü?˜Ê,eŠ¨\n¥¢@›€êi %TD?¡<ÛÇ²@<VÍT?RØÈ„Ác×í¬e–‡EABÅ¶»²@CK˜èB&$0X‚˜±ıy¼)hFŒ+—‰¨†&ª!ºZ?Dä¢ËE$%2†Ô…Ÿ-ºœ.a¶ZÙ³	TË&B²Ë™L2y×EvØ°”1­ôŒ g±;*Æ,Kï±D*Îä[&?bó&{Öúls+K?‹Ar<Xòè(9‹ÑLƒ-?ÅZ@ÑH±c‹0uFıui)á§—L Â™ˆ>\0úØaûí‰æ®\'X‹rŞõ]RÍÊ*Všç*V9ÏI\'[ôµ¨Oã¥Ú8f?®aìi¾h’[M‘Xe„ÍcD,à€Í3£Wß³ÀÈ(oQ¿›Í®^,L™¾DR?Ìš‘|f•l>ei¡&ZxÜQ¨¶nHÆ…+HÀ~·épñ$^S*í?)„,_µ…‡(ö@LM¿B¤MQ´Àå‘3\ZÁ?Å­f­,Ü}\0YØÇU d4Ò9Y?ˆPÂÆëí%H	–Èµ2D\r÷`•®­ÒµUºöÔ£kˆWI[i‹¥lâ,65z~lè5ıÄ‘2Ö7DKfÜ!a–BÍ`?–.¢™JDcú?\nnOb#?ö¡“^ÆñìB-Ñ¸ !â? S|ÊÍ?ÓÉ‹ãÃc\n“d¥ú„y…•E¬!zÂq›‚¼³­£’4&¼w|J]Ì‹İ^(à)Ã¹z×WiÀt?ôLMŞY¾ä_ø.\r—6ŸztÄ8’´†^©= Â©9–œf\'fÌÁ<`úu#Â¹Ê´\0\Zš\\9ô#˜¨úz~WO½U‰ä	–H”–¸s‹7Çw‹Æ/°·?¼xÔä}Ê¬?l?ùSêe|Vkm²º?\06õ`O‘Ë¬_¨I¼.Ôè–Çw\0\r§©3Y\'’XœøL¶…ÒE?\'Mz’¯…£%\nÛÈV,$…hAâ[O‚éERyL#hÄ*æk†ù¼´ë[…ÄÇ/–?ĞGk£yfŸRóğ`’&?ø2Õ\nç*Q%Ôgû1`Õ„É=tJ³˜Û Ù˜|Ğ‹Ê\'O²‘4!:?ÕÑ.Ï’òX•İ\nNEæ£kÌbõä¬LD†³àùŠ¬Î’óœ=EW¨1?©z¼\"ë³x2Ó§êò\0®GS°Pñºózµläj¥É\Z‰¼?†£E®—©¹rDæG£.ÙØâkdD­‘–x,RtUP¸]=‘±q-?2Q,íÅ®½–ºañµw?gõµfš‚¨!?=lEP!le¥A§QÇ6TJDÏÄĞôõ©âÿîÅ\"€QÛ\nlÙB	rÂo—šq)EĞ*Ì¯2çEyà9flÒ—+Ë¶YÒjT=@ãÌÓñáö}»XöÃ—âóywÁÙœã^+ã£Å6,&*°Ã˜ex??-4j/Äeé†gÂ®mÁ§÷Ğ_Œ%ÒâÂ¶¡ì}Éq‹4Ù‘£1*Úúh\"?4d3´i?sb)\'?ßø\r9é©ÔçÆ†ˆÛdI¾Õb7ô $)ˆ™\n3B\'n£ĞŞ®he±¯’ggªì†?P|¨çw1“dg…4g]é]GS›±yó”Í?®ä¢+i°Æöx0¶•—bkñI?ev¸îœ€ğl„À˜\\®Ò)FÓtJXÓ¤í´ã³î&`_,>´è&W¸j	Ú`J®hlşÖÒ2&ï’ˆ[ê°«D)gkbVÅºÜmSH2l\"~áÓG†	´LKÓªÕiı’»R4?’BÅŒ™„¸©=æ¬´éÀ†Õˆô}EaÉeí`dëŞØñÒ6Z!ì\r(É£©ìY?3\'Ç¶	^B2{tyWÑÉu¦–`ÄşÂh8oy¶gg?D\Zt	;_qè•Ö¦¼ï\rÂbÁ4¥zÅdA@qË¥\rhëE´Ë´Ê\'ã ”ÉKEí’ùmÆT‡ã)Ê\"†V¤àZäV&o•“hùìV²¬:°K^µ\"¨5Æ»œ´ÁòÜûP•Ë¶UÁky^Ã-¼ËĞM×£¸€bÉ™Ìh{˜c«lÍ8XZØ¢yá-m¸åš[´ê]ÌØ\'gæÄ?ÎqûÚ‰®l&ÏÇ5s)ĞÔê­Û¥!/u®,é]r6gÒØ¥1ZŒ£]u_Ñ3½4z{Õ<àD<>r´xy®?˜NÃ±±ß•7Ú	s­[õŸL\ZÆÆ½Ê5Tü­9º½ĞV/¹¸Ep3‚:_&bQ?_Õ‘À??Ãj ƒ:–Æ>©!I‡¦EŒLdà\nµ.½>ax($2EÇæÑS&z“æi“–ìBÃş3bCøÇ\nRµ(WšrÍÁ±Œğ¡Ó÷œ#Í<jbèc†&‰åšN³ŸĞ}h½ŠÈÃ	/ö©oûù¼Ü§¾‰góü‘ïTäÓ\\œ~–kºÁ-N˜$y¾RÌV¡µ@²ä–lE-\rĞ\"p!UtlUw·PVØö…»-Ì$_qJs’‘§\n2Bm¹â²óhŒã@¹6…ËMÚ‰Æ‡Y‘¯MH1†²xWGßB±r…G­?®ldÑ$É>c`¾,ÉQ°Y?ù`1´%\nš†µkÚ\"D„[%µ¶j×í:^D“¸¹¢Mò5RO÷×÷$#³`ANàûhó2\Zçe.e^ƒ@Èj>ˆ­½¡n¨xÆ%mm$¤İÎ7ö™oŞá¼“E~	ŠsúpÌÈì•¤W3?…ö!˜-¯xt– \rı}X|MèG——æ?ä	+p2pl@4¬–\nRMÁ}{¦{uBe< b_2–ğúfA€êVS8é\">^ÄchÁ“£¦[2jR¦îÌÊ\rÁ\\È5ÓÈ:ÀûèùGâ3J6;d\"ãóL5JÇRˆ12”¶‡¼X?$xÁlhu˜oxZŸâ)7¬›ĞgÒÎ7\"‹AK0‹?bi\"à[ì*İĞC?Pa„leÕƒ&Š%&×îè‰0êµäY?3‹l`|zÅ8+F¨iÛÓšåÉÅxéHI9(Œÿ?F“E²3ôÈh-a4ŒJ¸.b¥e)J,Y£qN,b`\'ÓQ¢Úx™ø?i÷ìÒy„÷“´bQi|äøãá#¢äRË\næRÆ)ù½&nĞÊâE+FÆ7Ë\ZÀwøîÛ,`@ü\n!’óêì%©R=»z?J¬(ÇJL]®úê?T„N¨‰4TÄ+Gf½ÀÑÒ:?ŸM¡kÈ[0×š}	Sş¬áOÏ.w¾a?xL*B/¨X;òÛèŞ3ˆŸ›°)³¶nñµôrA¼\\/ÄË?ÛâùˆA%àeR?vZûY£Äqã­”*?\n¢?11¸8µì²\\\\“{”<íAÚù£­$ºj¦âÑ¸±zNV¼XQ†ÑÉŞàëvõ~¯ú¶ı(™]1Ì•áy‘“Ùgr:åŠ¦‚îpHŠ0CˆÅÓĞ‰™x…öë‡­J-!¸Òà•b¹hwÖĞ˜,”é¥–+œAÅ‰©†±bÚ\ZE5M¿hˆ‚em*¡))à4aÊÃÜø*ş´fÃa–{#ç¾}yÌx#€×M2¢7<ÊËëMÌJ;dr¶A¾7°/ÒÇDa1‰›g­œ¨›…z6ë\'‚A	®’Xm!«şNIlĞ(?P?ó„WÅ„ŠåkR!+D%º vÍ9Û.ó•w\Ze#b\nİ¹2ò‹xlìã–Z©æ_•ZV¥–U©E—Z\0± òk¢‰.«R‹’Z$nPâ‰@Ø3U_™X¼ÀNUg?Ã2ˆÔè³”Áï”?Áßkü\"Bøh	ÔíÓ]ğˆ•<vÚ0»F¹ƒ5:”3şQŠ4\"2¶*	(œ2éë«3ÓC!VÚ\ZJÀYÌØeŞd8¾Ù^Ä&½v¾Øw¥.c`–Ğ¥ŠĞn®ÅF»«¤ÎÇ,l³E?\rõÇI‘m|ägäµ_àö+Ç^ ¿%w5R©Š]F«8?¤Aƒq,GĞŸM˜yw³j²??dÊH³$\Z§àw`ÊÁˆëV„,xp±\0ÿîEYZCÅ@‚ü$”+³¸…ÇÁ.€ªlÌ¦;9Ã„ÛóÀj?İlÇl‹¾”ï‚ğ†5ê?ùHÏ-8Ò´‹Üe¹÷ş?E‰AD×¡	šqªGa‹õğ¯Ÿş-õèl«XPêÕJ!ceÎĞ6{©çˆ*\rÕ? eh\'Šïæå®	Àq|æ€+‡yÏWí:Nl†¼.(µ§tÆA€;’Š-‚ƒC0Ë©YˆBÙ.ÎDîÑJÅ$X¼Æ;IíÅ\"\0±”\rANˆ$\nn6?êÅ¡„d9EtUéô¤ØN¤Iœà$/Ï%Œ°?+zLFİê/Ó:´şo\nVÉÆÌ“[?î.·ˆwŞ­C!“rz¶kê\r­šSLÕ1 ÿ&sÆñ‹VYš#ğÎF!@uçÃbwÑF“E¥–2D3TR¸²‘ÇU2ÏŒâ§„68„ÛFìœ<8¾sxÛ˜´Z	\"KË()¯±‘ñ«D9ØÈ&ŸÀ¬ã¹¨¡\nd‚a€¼ê5¡*`¤b\nM SFÖöÖÀh‹\0_Is¸TÓÊ*X©3s©±<¯ZdÅÛhˆk1L0Æ°]d#×8²5N/»n?S,ï´¼9İè?† A£ù‘mb…eK§çÉNWèT¸h\rèT›ÑQ¸¤Ñ–\"?}˜ç»lK!}K¸ß³–—\'“b¥0ÀÂ&RÀ„È¬!—„!âcºÄª?û™Ğ¡?M–Ô|ë,Úñ:¥VRŠ\0í†ÿ[˜uÂób¶Ôö­j??©:9hdô‚™í÷ÆØÌ%êìü1x³gæì!øRó)!ğ<´Ö¢z\\D>Ø¡lÀÀ>Øä€‰æv&0´È¹çÃYrü\Z–´¥Èeå¤‡´n¨Ç6ÎáHßçü\0?\"kù”ğÄ¶Š2.Ú0L»tÄ®¸B7 -$U?’•1‘«mÅ a™Z].à ?õl2°`ÔÚ\n›$¢ºƒ JÙÒpşhQJ&º.T·yBí#$u˜,\ZŸÁ?y‘É´n?ÆŸcÜ…™¢x\\o<lµ¦Ò³¨=ä6AFé°#¸—&Ør\nÏXb‹g–í¨&ñ1™N‹iHU2ŞÇ[™jÎ)SVa*Ğah™i`lEvÁB¸hTŠl!9Ò9oÏ˜3wŒ\"aÙê•?â\riì²!\0_„8zòˆxkgƒl[á@n×E\rºÅ¨N“@<äæ`ˆ¦•ü\"~ÇX¤Ñ°Í€¨Ñ«ñ¸i98!‰ÀC¨´^m‚Ø?7Ç„Û ay§î71®~P4m@€¸[÷²zaÅxDtBãi>ç£ãwHÆ•åÁ˜Ùìê:…kda\\ÖÈäò!H“ğuT1­¿Ê öÊó÷`üì umÙ¯,™œa5™/I¤•à”?2º¾ÖÈ­èî­Àã+LdH“ú†ø\\°!ıı¤ÔSº_•1­Î>ç~Øå©şÎÌUÑ\0Ù·eè)DnÚ¥ß?àp;:å\r	ãT ĞŸ4’˜+Ã¢ŒA?y†?P‰sÔ³å¯òhhºHÓ7nÈËË?ó/V§IaF­(kÉF8ó)ö¸øÚ<Øø–J¸q%Å×ğöLìšNïJ×oïn?™Ö2#åğÏŒ¥Ò”3\'Ò%B‘jÅÕÆÖƒ*z’«mÅäŒ\0í6”œ’¹ATqõ„”ÛLLÔ´;eiÊcFv3XBP3Äã´Q?áîmŸš\n¢İ‹4BŒ®\n“,…ÖD»h¬?¹Üw¨Né6Èc]¨,I»‚\ZÕ7@RÜ•4—{YÜ²`FMu&Mô?DTB\0Ó~\r RÛæĞé>Év£†Èª?tfO2‹ä ğFYr²¡?eñ(p¨ƒ/-–Ãz(¢=YÖÒyö#A(Õ¿Aÿ«‹¯œç5?ÕRAåŒâŒo°É¬UÉ2ËCŠÏ?nÕĞ[•_Ãtyqƒeş];L<«&gI¬’Ìğ£n\r>\"S\r\'RWÃ¶Ê(5O™vÉhziúA¤<Òq=Î$­¥¸êóL9øz‡Ê?ÇTè\'ózœ›0dÄõ‚oW‚‡E½Ë¯Èv\rhóAô Zb‰9+Õ$­\rn­r¡+í{vav?>b<ëù‚69æ]?¯@?:ÙBÉ?ˆÃŒÀ1àßÙüÎ†\'˜¾ÀÖE“•„àµô‘Ÿ¬e%CFÅ´!µcbÊ!U)ø¨QÔh9ùC„øÈ9W)?@éOsƒã5,>xİ\r¢°rµ‚ÔĞşŒ\r\\=˜·šëwøÆD´[Áì\ry«JAî:5§@b\"Y]å£™±óÖa‡qO#!“†„Òˆ†î%\\±:O\\uË`iB¯kÚ’†Ã‰¡z¦?#´×òûÒ$à‚]Ê±¥\0ÇbB¨EJš£2,Yñô‘­:#Q¶!ÂŒÀÉ??^?Á‘dJ«(¸n˜¥Q¯sÂÂ‹zÄ6»ìyC5$„R!k…Sı*P7\"`]˜-¡¿=-=ó\rã‰D\"šAŸdñé*@«@ó,‚â-?6ìnÚĞE_–?jK¨§)Ö\rc+åEÕP¤ı‚‰gLõI·PË¹¥úÊâqLæ,£ÖpdÁU6ín!gÔp/ò)?X.À?¸§˜yš\r²’^êÆft$Š¿qK’Db¯åÕ»Æ\"´ˆ\\³pl-i›H³¦Jc³I\r®FÄ¿PŞfª´}]âÒE\'¹	¯\\ÌÂ¶0nTP›àˆ›YÎ|É4ãå)Y¾`›ò]+¼–ä,]©¢‹Öü-ÂQ†‡´E?Ôoò¢¼îŠB?£âòL.©•a\"¶(0>ÏR™?yå¦c/Ö¿‚¶}ıĞğŒ+.™¬lÖá˜I)Ó°Ò‹Ë“Ş€Àx¼¤\nsğ9éú?Uºy)T)í¥Ôù²Ë¦••¹Ç÷™[øŒà\rñıÄH7îšìFõAü±áĞ\nƒmµ…0(<ºqs4½hH	cğİ@Á,W+v›Ü±KÊÅ¾„êOuëâ `û\"Tİ„‘B\04Ôˆrë4”q˜­lY·`“Ù›º¢â§†\'¾r?ÇhE¬\0%yAÇÈ¾O²O¯¡|êgª4¤8E`bm%Ù—H‡w4Ry½”¡›6^¥Ğ÷Â´ÔcöGƒ”	Øú¡ñÕFe„G…÷7[ÍĞ~„lÃ€Áş1Jâ¼,/Ù´Y*{ŒÂÍÌÔ¶é~¦ÚSE¤ênËªÓ°-+C°0=áLx;\re0/§ØZ.ÇÍe“™Vöé&í¥¼\"{~é`X\"µ]„®wµ~`GL»XØÆT¡VC+q<Fôxİ…ËŠõª2\Z“¤våÂyàhhÃÑ&¦ğ/²^¼ƒ¬šµçQ­J7N…Z²4¯(…@,Â©hô\03I­Rs:&¨ õdâ‚<`*Ãö²gW³n ç,ğ<Ë kU2PS+ë-u(ƒ-«‚z^I‰R1V!È‹«\'1¤¢1C*†Õ•™„%Š©¯ša—”†^RÍñKjÉ&õ81Œì)|7ã/ÔŸbüÄˆ¿lğqÑ›ìÇ·?âyÆÁPt0fàùx÷„9c!€“‡IíA\0¸e°R-wùnWïçä-‡ÈB†îlærĞq“ùNi‘ç×ÊvÖÃS£½À0Å\ZSà_ÇÚœí?tv„®íŒ.¹¥:ÛP6aâ5ÖZºÀÅ4°²\"]nqM€ÙÆ>d…Æ xtG¤°é.¨ÌŞ\\låpŸŒsF•|»PˆZ††ét¬ÅK«È¢Ê_?ÍËNÜÀà(r–*³ì!»]±;h¯%ö¹¡yÖ›3•Æ·Š×«ˆ”ôF(>4Bj•ì6?Ò%IåÚaTí†W?ääzß´°°0„€Q¸¦{‰ªÆì	f¦jZgâ¢rc¦¦€Q–tìj’æ ¦r?å­<Ş#»d\\B—èuS0êaÛqÒÅ~Æì`¡]ÎW,rCúMÔTN¤“dXêfËAMÕ\'N§K\\“¢‰ –NaÀ«˜Qé•S«­fHì	}Q&s°ƒMHŞŞcEı¼*:İÜ©xÕ\"\'QB«/_H¤EB‚æZÙ?ìM˜gV¡µŞ3Î8£“õ¡Ø_‰B¨;1E P4§MiTÄáçÔĞ¦ôèĞ0Êªğ¹ÉÏ²ÿÙæTÏĞi¥¯<¸©ÛÏR™?hP@ÀÓğŞà7İ~Ek§Tã¨m4OÄÜšÊN¬‡ìh(ªeÄ=í„íÛ¹,¡¢2(Ô’zAì#zaÌÒŒèÈz.Û@Ãèú?î ¹JCÁ!mG¼#úAlß‹¤{+Öx·Ø`C#\\i”Âü éš$AØÄº\0?¢íé™ËÂ—Ùš÷_ÉÇøøU46}ES¼?¬Ê9?óv¡ŒEĞEº6CÍ¤E¼U…’™……ç@£N»BT×ûRÆıÂùú-Wg\nN¦P3;ƒúeak7oÏÀ™7wOí`rŞÅŒñdğªê\nœhç0;¶]¾]Ê?²US¶\nƒ;dgüDÀ@?‹ŸÃS4€³˜·À¥Ú:³ Ù>^rÁ(ÒşÉ7\ZŸ¯½µ„¥§“AÓy=œÃèÀ6)Îl›}øhWÈÁªúy¼Q¼UŞ÷ËİİóóóIäR`Ÿ“0¼nñ½»u(?w‹0ì-ü€u\Z“’O¥[5M£¯±]Z°¼lVğäxİ\'ÌG`_¾;P>4ÿ2?_ƒõ€^“Vuh2bFÙÜk¡ç®ÈìVV÷ÒÆ– ô±|\"dn	o82°Ï°qx?6“¨z‘ÕÂ¦¤j|âàŒÍVô€\0)~¼Ñ¬444Æ:ã•¡B±zıp±­.«?ğ3æµ0©_£\niA¯	Y(n9®Àp%£Ê£yt1®ıˆ-ˆÔ×›¶fµâêQ\\¥‘¼¨7ñG³‚»Ë¡¢»Ë±…—{1¶ê¢yÉã*êñ•£Ÿ7­ŞÌu5¶ÒbéÄc+.U?İ@¹¦UâÈqU¨ŒÆg±ÕâüãK­UœÒj‘Š©Èš©%Ue©¶©PÛ´‰E]›Wnnw»XÕiò‰n€ß-ŞÌ¾™bDğt	UË‘UË±U½‰Œ¬oxU<úš&¦àâ—41C·wñ—wQÕT­‹Š…ãºÄQO›T%Ë\0½=hR!\nç„ÇVæìú¯¸¢‹å)ˆ¨m\0Q0úæ9v‹Õ\rkÜkTÕ?SZã³Øj?¦É1ÅÒî´SšÓó“Ø*u³Xlü.ùÅönÙ9©Êá?¸‚‹EWÉj…cA¸©±OL…0ÌO/óMï4c*Ä]bG÷+¶ïü³yáŞ\ruÅ{74¯°5]Wakºy…tzk]\rxÒ¼ŠÆÛÉG]!~Kµ&é–Ã\'Q‡……èKD?HëÓ¨\"M-ôÂ\"ìJê\n4½Çç4vqh1á¯ä,ñsÄ-&ÌñR&ÉBú·lêŞ21º?·§wî2Ö¨¿\\¶«7yz²¯;—E?£ï–K¹dfÍŠşõöôlèë3×˜¦¹¾·‡>ûúù7ş¥z×o4Í?©¾şTOOÊ4{×§6¬_cö¬ì0¢ÿ@Ä·*¦¹F,fl¹¬}ø‰Îı×æ”2…j–¢dñbaÈ=*83ğÌÄdĞhæî»º×¨\"­èuÛJNCÃ¨>¨øêªFÄoâKTÓ¨KÔÌcjºé¥òx¥Ø\"BæCù’#İ`•ù¾¸7\Z Yi¿h-3–‡ÖìtÈ¦¹¨O›±¥ÿi	0ûj:¯_b-Bù«î5ÙÌÅ>?….y?(/GÑ9\n¯P½Õ?_¬J.C9^*æÚµğãp§q?ŠD.°Zêã½·Èè6	2?4àÉ¬Ù??a°ó?ôq¶¸ÑŸí€-°+•„ÙºÛƒFLí˜ÒÚ•¬¢-×‘¾ã‹³fÎ›ùsKè]ÕÒb/8~G/‡zlÑ\"áà?Ïé= ¢àĞ8NqJ1#/)Ÿ®ÀN3†œÕ‘Ñ‚-`¸Ÿõğy-ô¬ï€vA3zÕ¯¿Ê‡^mÀWF‹^ëØ‚_ÁÀ¸ §fæ©ÙÎ…IC5Ş©Ù…S³0Æ„¶ìÔÅÖ?ÿ:pËBZô\0?b±ŠMK?˜v‰¹Z-º‹Í2u@ùÇqWèœİ×„æm‡iï]Ô£ŒïªÃbû®ãhÁ?È%XLÍM®%ä\'Ùf&×ÁŒ·o,€VÿÔ?Eÿ2óO`¥‰ÿ\Z¦ÿ==±ô¿·Ãú:úŸêï]¥ÿOÈ_Sú?0?OŒ6³!‚Âôö¤ú˜í=çŒŞ³-ãzş9ë7ôÀOƒ¨$Ş¤Ò¢ˆƒÒ–\rb?ijT`hê¡S+©Ú=\0øñ‚sK¦I¸ŒB&;›1`²cnÚlÑÓ9h:2d2?ËÁ*P·£#ëVg Yà]ÌdOïÆ¾şõ©3Rı?ğ“úcn²®E9 .4†Šº?½u†Ú9C6Ô{ú’[êâ¦ FW}sğL´—Úx´í9]P»®=hE´·¾ÿŒ£›)ìp0¼ 8íƒ@ÿAéŞúv^8(ˆQ=$xğsqHÀRM aMŸV9l?ÊÁCƒÌz­¢yªÉĞÚB$ÖAşWƒêF,´AóBÓÆLu¥hÔªHhqä¬áQËS‹&*ü?Ñ\n‘ƒ8}4—ÿz×÷lÜ@ø¿?Õ·¾·#âÿÔÆ¾UüÿDüµ?ÚÀr²E¤0qåY	ÍÊVXØİ,_øÓı-ƒKˆƒ¸ßÖ³Œå2™„Ì\Z¨‡òÀÃ:2±sd¢)ÖD†?xÏŞÆ6¤æğ®éqåAd?}¨ƒ¡*;vM¤9f!kNWKÓf_²7¹ŞÌdŒ6YjĞÄğZĞ½´ÎACÎ%Êfß™4\r9†ÍP’g@7í(‹h³€\\½†%AÃ2ŒP#\nCÉ20ÀFÛ,§+“¡ƒá©ÍVE_›R»X\Z¼üM×Ê õ‚l}¨l“?D74°¯\\Ä4c>™‰ ÑRaiéÛ–·-´bAxª%(E´Áİè±€`jû¡·ŠM.ñ$‹?6è­,2Àw\nô†QWJµyN„;+vÜ£¸È\"tr`Ì4F¶îŞ6½¹kÂì\Zİ>¼gìàñ-ø>ø…&®!€X|Æ|û¯Í;˜še´¶0&¼ı?ÍDi¢IN‹ÖŒ{Ø¼øMÎ4ÈAw>i´E\r}ßäÎ¦1Ï5ÚZ‚G[§ÆÆÒû\'Ç´RéôV¦›v•1‚¦œ®Ù›\\ÀW(.¾•–šÓˆ }F›0Åæa´5ôI‹­#vk´0?Mî3áD?\r˜û\'v›;‡÷›Ø¡™0ÏÜ½mÛø¶}Ìœ˜\Z›2\'¶ÒËÔğÔøØ4,è´‹?œJ?ÎYX—?Ì\\\rg\0“™ãt†¤z©‰‘S,L\'n´	eN£SrÉ„i;è?CÄíÛÜUÈeÍ®ÂmWáü+6ƒ.Ú´%‚–¹Ø\n|WÛ°}a‚ç™ûz{5ÀcXu`¼Ñ3¡sĞU@ƒ4áÅ¹Á?ßŸ…\ZØÏ\0áÊ­ò5ÀR‚ ,8`ªD³?ıÁb?yŒ˜¸\näÎÎ.²Ãa?„¹P.†Î¥)ğ„À7€Û„?=­z…7³¸2Ö@FHÃô”ì\ZORæs| Ûh«wÚÓ¢¸ÀÊHL-ÆLˆšÂÙC‘L·JĞ0³ï²™ôÊàÚhâé£•\Z,Çø®‘»GÇFÇ§`ÆqtİU¯Ò-dœn¹©ú°§ò|?ÚõW¶ _˜ËYz}ÅU`<@	ã1«ÍLéÄ\n(¥ìËh“>üİˆæ¡_+öHgC›¥ga$|À;;’ìNá<\0{z2™@e\"Ì‚P—’í.xVåĞw#V:T†ÎÂ‹Ok	§C#\\ PTcµco„x º³İKçó¨â\"/.a€fön	¶¹?<Wè¬²!ÉjEé\nä#:æÀu•ñÏÛÜ²@Êjho$dÑ¶À©c8‚‰víĞ@\0Z>pfDvh°ÈŒ	¼™ğ0—MZšÕ?BUÆ©•£;ò£Ö$\0òtzxÇ<ú›Ã]76?Ì‹ó£l[–Í?ĞİØ‡ˆ1\\rK]ÒË¬w\"_jŸ[ÆwéıÍ8%èÿ3GPUƒB¯)°n–i»ÉÇ+{Æ¦¦Ç\'vm&?Æ0FF6¿¤C2z?æK:4TĞ	{¹ë,|?‚oÄÆŠ¯ğÖ˜œšØ65¼s\ZŠÀ\0ÊŸX\"=6?†ş~³ğO1¥Ÿ\n€;+ëá»ú‚¡tË\0jº2ŞF$`„\Zß•qisÙ¬]t©	4´å§³¾øB4Öt@AÔK:´á‰×ß†fd´\0Ã—ìÅº<~(®Å5Èëì?£jmìï—óÖjAg?İâñ\"µR1ÕRqõäâÖU?ãk¥bª¥«\'w®±¦x_7Øê†ÊêU\\múŠdbêÌdg£vB<Ûu¼ÏÓªˆ#ƒƒLæãëd¬LŞ«(^Æ×&‡¦èºô*¾æ¬[òsquÅËæµÍj«]œiVß6¯ï5«î-VÛoV^\Z#?@’®©N/c>õ¼Ëß[¢?¡8†òœ66¼€–Ä?ˆ¦€Gb  İØ’z\rñ÷fCJ©1¥b•\n?*ÙœÀ£D-•|AkE?bšJ©¶R±?¥B­¥š6\'PË€†€\"›Şq£âwL³\né„.•£\ZÖ_RËêAÜZU\ZÔ)r%ù9/$|?]G v’ìE¯\"¿èRÄ±ÉˆfÕ˜f#FÕÕŒêŞ÷keÔ?ØF4¬è¯l¾.Ş2RM£ô5hÑ\n6?Ä²ƒÏq\\ù‰®oò+iåè‡çÁç¹xO‹Ïé|å}„/(ñÑ7?Cè“Ñ†üæ©o€Çä×‚l‘‘+Í?RVq	®Í”ˆ¿ÍY¥C|›Ÿ)–Õ˜Ä?\\=Š@H ïÉ¼†Ü$©¡Û\0okÉkÍ\0Ö¬€,=«ïÃ00€•Œâi–¼¤çÒ­yÄ².mM¹ú×5XT®¶¼…?^Y£½èQL‘‹bd@„.\rÈµZå×\"·w?d/4:»Åi=TÎ…~c«×ûOÿ?uÿC›{lú0ûz66±ÿƒ¿ºûÿŞ¾¾õkÌşc3œğßïùıOxÿùp„]8¸şõöÃ¿´){<},bÿ¹~ı†úıï‡÷«÷OÄßÅ“»¶=Ëxbòg?o?‚Ï+á_ö?ş~«~ısğñœÉé1ø|ìÆï¼óÎ;şòš/?v×Úo_³æ×Œ?§®¹ñª7ÿÀÚÿñªÍ÷ıÁk¹ıËÎËÚú¿{ÑoçüêÁ/<ó‚wÜ–ykáŞuCgm9á³¼û²éÇZßóæÖÇNKìıøš¿{ö	Ç}ëUzõy¿ùâScqÂ½]q÷ş½‰Ûîıå?\ZÏ¿û_.ûÓ.ùô¸ÿ_û¹_<0á}ãÃ¼î»wm9}îı¾ç¤W´¸%¾uÍ†í³ƒ—nß¿?ÛrıƒŸùì5gæî9ï?SWıå§>ûÿ®Oüã¯şáŞ?oÿ×ı?şÇ£÷ßğÖãÿöÏı­}éö>üåÏÔ.Û?şú›¿ıê«ËıñGËÏéûø?¯?w?ÿıë^ºÿòoŒÿøC÷ŸtËÖS6~û¿~òœáı·?9ÿ“Gï94µÿšlâ‡‰C7¼¼ûÖ»~ğ‹ß<|Ç{şşm?ş÷Óşów?ëş,uÕÄ#Ÿ¼ıPf÷9mŸ~øÃÇ]rûõuÉŸ]üŠ?ğ¥™—¿¥ú@‡µçüşŸ=»û†GOüYöªì\'æz¯¼ñÒül.Ûõõ™«^ó¼ÿxõßÜ|vúõ7µ?óÜ÷}ñÂËn¸ââ_<:RúÅ]ï¿ö†“¾|ùèÎ~Ó-_¿óì«¾Ÿ¸ëò«ª?úÔGO¼ıw\\wö»goî:şÑ÷]—Ù}Òyg½åªãÖ½ıÎ›>5tËîûóç]\\ö¸Ÿ¾©šÜşÃéƒ?\ZiİrßÎK^8ú•î›û»É±kî9´æŸİÄÙ7]ù®?ÿ²ñÊÜ{÷t&×:×Ş÷ÍŸş÷?ùâº­{Nºõôş{ÏÿuåŞOØ¯û§·|ú¡/^½íögıøÈØ³‹“ÿóãÿô–·½}óÌ¿e6yé§®|ïÇõÜôªßŒ}ÿ™—»öò‹şú…×õ_2÷ËÏvú‡¯½éû?Üv?uÅÏ¾-sYâ‘µÙÌ=oıéûöäŠ÷_4ÿ?Ï<ç\r7^şáCW½zî¼¯¾éöŸ¿kß‡døRG×)ã¯?%÷ù;F¿ûî?¹¤öËï>8òµ³n{û3NjKf¾ã|î­¯º{è”“/ıÎ©?½æ¦w·_û7í?ßóÍÍ…í›¾÷7ëŞöÑÿó™ÿ}õÙÆmŸÃ­‰/?ùÜ—¾âÆ±ÓzñÍ;÷¯şƒO­½ÿ]Ç]}í7?Õ·gÏ{~uèkÛO^ûáW|°°ï¼_~ğ7geN?{ñ­oüÏ?Ü—ïº÷CWV_wù¯?qÏsÒ[O>ë¤‰·?x?ŸÛı°·á†ÂÙµãæù?¹¹¹Ô÷ìyë7|ÇEÆ¯¾óëwåÀ‘¿üÍ¯?sæ¥¿8´áÜü.úşµ\'®ùÁÏ¾æ¼›×—ŠÏıó+Zßàv_öìÏï»ä¸Ëî¿ö7¿ùC7?÷³z®ÿÚæ}©u·¿13Ÿ¾\'ñÉÊ?š\\÷«©s^œûªÿ¿Şbï=Ùk®øÊ9ß¾øº3.¿ÿ»şõ„¶ÓoyÆŸõıé#CşiYÛ~ÛÃ_úíkNuææİ¹öìUï~âÈM¯¿ÿº«¿Øu¼wü•?è´5_yÓ+¯¿³çÜÔwvv>ğÎw>ø¹6.ıaÿ{ÿïÍkßôâç=¿oì\r·|ÚûÖÄ‹jŸÌİü‘ƒ·nüPbó?/zÙåg>tkëÆwİòÁë.^wå\r_9ÿĞm_ü£µNvÿĞİœüXöcÙDË×~ş?ÛOv^»¯”¹âŠ‹íWşÔùÛmùŒ÷×\'~à‘~tcûåsûß½mSökÏ<á?=íİğëúãŸ÷¯Ü7røä_~³¸áï¸ù?OûêÜ\'·÷ıôı7{î½oŸùÖ}¯ÿñ›İú²î{İËO}ç»^û“÷Ù3Å«s¯üzË^÷Èe£WŞßwñW¿µ÷;7¿şÑÍû~uÛ)×ù·Ûv¬Ş÷ÿÙûË¨8¾çoÜ?\0Á	\Z @Ğd?w‚Ë@\\t`pwH Ü‚î–àÁ=8Ã 30Ìœ|¿ÏïÿÈ¹Ï:/î½ëÜûâìÕkvwuUíªÚ»÷şt¯é.Á\'fÖŞX<òë)Ä8x€§>å\'oOHJ-­¾·…ÿŠƒD¸Yg1`Œš\'14c¿™¿o&ÌNZ·h5œ\":tVW_|ÛS<t÷áAläÙ•Ï#Ó$ä¸[›İˆÒ[ø—Ë}c¬\ZG;ŒR`m¯ÊÑîwÇÇ?ıÆaW¯7,èô¢ĞvFÌË›lFFÍ„UëËmŞB_›Í?	OçYHİÙüÌ?JØ]ş†¾âñ\Z0/RI—¬èj•©…f™	LM	Vü?	â¬D3{G×mmÅµë0\n®iÂß¿|Fg«» Ùj?Iòqı¤›’?Œo%j3•Az?5~ŸÅ¤ì‚æFÖíOXÓÑ6çò¯†b\"ŸEêCºÈ¶Ã[õ›×:^´F ÜÒÛÙü†ÙF{Pªß3{İ¡K’ß\rjk€eÛspÕœàµ¹E­ÇJ4ñ‡$ƒ¦}b•0ñı{KYÎğM3GÛ¶%ñAb<;$•àø—BùËY\'\r\rNG5“Œt1àoŞi/´!íÛÙneïeß(¸öX÷80­DŞY?z?øıdDŠÕËØÑ˜Õ¡ŠŞûÍ‡?YOn¹>½nˆ–6^ Ï¹Tò ıRäv]èbUÏ:ì¯S½ÿ[ÏÊ¥¡aÇì?ÜÑÂ¡E×µÀ\\á§$ª=5z,ŞÚmâ¸îq¸S2ùà#k,êÏøÎ\ZTúlµÃñÀKkYÈ\rß\0°8hËÃİé»¥Xó[7è¿şÏ:§şRçEµ’MøÿïVÚÿÿ,ÿ;ü÷Ï÷±ş¿ÙÆÿ5ş“”’’”ø?¿ÿ#.%õÿà¿ÿ;Ê¿]?…-?U`ğ\0¸\0\0NÓßó\0\0n<\0€ç\0Áÿ\"=u\0€Rö/M\0 …\0\0Ø‘\"İÏ¿õ_V†Ÿÿ?çQ?\0@H\0ğ/\0ÄÅ\0€Dà_y\0àyá_ú\0 üù/Ÿ;\0 ı[x\0¯Ş\0$_\0Æ¿mş?¼]ôßš\0pÈú[\0o?ÿê\'\0\\nşÖÙ\0€{Ê_Sßaaıcø$æ/ı‹HÿÂRÌßCà/4ı§şß@\Z\0àãı?”‚ Ùğüæ\0ğö\Z@ÂÀù—ø{»I\rø÷\0  x÷/??fğYÿëĞ¿<ÿ(ú7n\n\0V\0@\0ÈúWÂÓÈõO,şòè96ÿ?Ë_v@\0 øşoİ\0›ÿìÿÏzlÎ^Xÿ!€­ÿ.ğş/öF\0àú?&ÿ³Ãõ_t«r\0É\0‡ğŸv]»\0×\0Œ1\0Š?]üÆúkò?ô4€î|`ı?·ùŸôL\0?ÿü»Oiğöúÿßÿ\røW\0ğí¯§ÿe?ç ğ€ıo|ô®›ÿåû÷X\0b€Á@\0I\0€âéÿƒ\0\\/0ÄÿĞÓÒ\0>>ÿÑÏ\0àş—=ÿ´Ëõú4áÿ°ç;\0€ø/ßÿg=Ìl€Åÿ²mÚøwÿ#Û¡Şü»Ïü°È Æ(B“\0LŠÿ€â?ı›‘öüCé.´Áùo&t‡\0lÿcg7a Í\\D¥ıÙğa\'à+(²\0p_\"p\0…¿ÆÉ0¨8ÿŒ“\0± àğ/½[ióî¯õ¸ùi€>ŸİR\0˜˜\0‹Ø\0â¿£M:şÏD;–1¾>!À“ÀÆıG?©à&€M¤  Ğıo?ı·púçü®£‹`\'?Ú$ÄéÀĞd(7±´‰KzËM(Ò§\n—w÷6³MÀØŸÃş{?Ìô±åÿ—‚^¿ûz¥}R MzAÏ½µı·¬İ¸Ó#VjAƒÓ\'˜>á—§j/ƒÉpo¯w‚Éú‘ÆIø\\èÚJo”TµÔM:‡j?[ù ˆşı£˜nN­o”İÜÅÌ :Y2rCfîàwG(!4¦	~6àxu×°·‚À`öè‹À¨$Q\nFZı¾¿^e/Ñ•°“*¨_(`0ñª#£Åxne>{ÃÊß¼S)±q±„2À÷?“Òb0çlè>‚³	Dı{àÌ	‚Îÿs‰,™\0Ñn@f Mà(¨M¤ğ¹¡¹g¸Ç9•—yh;\nËi_—Dd£&S˜L!\n( 0U ¹ÎãÀxuõ=¿v<ƒ—¨ŠRÇbì’—q=ÄRû?IÓ?pUËè´,?Jù¥gú´S!Şû\\?6–ò¾¤£wèØÔ=\rŒ?}cĞq”`Ú¶ÖÏÿ²$FTß4xqáÃÿ0?‡õbâ#o\"{ô;Vêƒ=üVà§UugÄ±äWˆEêèŒëşÒóÏÀZÃZŸ,ëÓ‹U96<¾ß_ÛÊTåYá.f–Ü²&¾‚Ô$\\wN,?\n°?„É[r®¥˜—ªƒ°Â‘³^e{&Áö\\¾‰%Ø,Ğ§Ÿ=´»_›Ò,ã=\\·jÂß$*À4Ø\"ñ‹\"Æh\Z·ß\nÛğ49=@ˆl¨.`ÕÈ=`0ç›ñ;‡KÉ.…¯[I\"[j|?O	Ï#²™pK¢\\Ÿ¬¢\\pËH3˜Á±»ï£ŒÔ»Ï÷hi@Û€‘Ÿé)1ëHJ—,‡+Š[Ú^_‚.…ªô%ÊÊv1—Â-‚„É=¬y`^¿n;:Ë¥8çïğR?Ğü(é]0= )o|1›ƒ—ÆöÄŠéŞõöPĞ³»ì÷Ó¡ÍPŠ:GY´ ¬EqJ‚\'±™¿™0¯äaç¦p9Èı?˜‹àkì­İŸo÷üªš ¸ÿ&ïÕ•¡à\n1ƒ[0’ú^~N¼	–]A¸ÛwîíQxE‰G†=g$éxJ.&Ôú«cH×?seÙÿ†(Æ<¤ğÍŸ\\!Ò ËÅ8Ğo/-\\†‹œ®$ÔçÎBxX{û–İĞ\rÂb¨$˜£»*¼Yší¦É Š‰Ç~ó=Ox¥>ùeİ9g¶RËÉ§ÁVA4®ˆeOÌÜÜ›èSå¼D}\\^Z¢ érq-›Yø„Ó6ÜM~bÖ¦²À@˜LĞ46änÆ­ÇÔßdÃÛ?y’ğø7Òif\ZĞ}V–7 Èï<KÂå©ºÂÍ°€†. ÇUóÀ„ï\'&|ßŒ{\\‚4@øXşCú\Z¦jE¾pªyà\\cû¤¸ØÆöË\nœb²Ããø¥\nNolA€à:Õ)Rp}°cIg^ÜÛgÙ?$ğõ_M2§Mm\\Š÷`óã¶Ã’ş‰…M¿[ªı,Ø:eMIôş²L/¸Ãµb¿QÓV½¿}Q÷S»Î`¢ôÍÄôQœ) Û›“¨ù0&_“!%pHô¾ıŒÇp€¾mABC0*ce/ ¿‡û0¤?÷*şn»ø ¶…3{4úø½|Ã½Ìş\\ÔÆ2^\0;àX³\0öºF™…Ì`Pî=ëÛP<ûÔ2¸.pƒâÆ:…??l;5ŞIê!1ŒÅ*Õö¶Á3Á®¡k/ùIân‹]¸t°l„pÊ¡&üÌ1+‘\'<a?“AyçZq½¦¶m/LşÓÙ-!ïYQªALhMıZ]lÄàøÙãÜİµ©}¹Q6z›ü/K§Üo?©Eî2§¾ÈO™ır1cş?êôµ+˜éF`Ùësqlï±@á¥A†ĞÙÉ_]=^ê)ëÛ3¢íï2\"ıGŒu’ï\0¾Ù ??åCøÿYÊ½ QÏÏå³’Ü˜B:‡›İKãe½*ù¶ƒÿ}B8}ı­:Í¹·}ÓÂ!—ÂgÛ¨$O.ézxg¡±ò«ŸC1ï¤´øÀf\"	Ù\08z9Éó]•8¹9Á—jÑğRö•qÊ(pĞÑ–íï<›sR¿ÃG¨ )]Ç|?zêôñ )ûğÕ„o×àQqk¼ÿ†c¢•4?„ÙÆ şSİ?ˆJğJF?Y@ƒjàÁaÔîvş=z²\0üˆ€”c4W°Jók^Sw1@xwJ)`oW{¨ôAàw…Ô†Ï÷ôÈ7?|xËy¼|o¿õ>á%s¢Û‰ ¨Xï~c\ZráS\0\0öx‡Õ}%µ9A³RÃ›1@I^NHü‹w»MÁwOhjÍ??\0R+j{¬l›štš/±Ôæ×ŠpÅóî{S²°C?…ê¶àô$GI¼g+åvåÊ“m¬t­9è\ZT¿wØd¨?Ä{Û1Ü2Q×\"°R{~e½H%\0ñºztQœŸp\"…Yô|³Û²\0“‡êe	O>E)$•¡¤¾ÃÖ‰w	ˆÉÇÎø»CXVÍÃD?’}6à5»\\À­Åi€?5Dv‡7K]áûœì;ùñˆq\n8?°À\0ğìİtAfÈÆË‹EÙlº÷BsŸRÈğPÀ…²q¨÷fé?¢ö)ØS÷gˆÃB×\rÙÂö#\"\rÖ#¼ZEÈ°ÿÒ^²å©]ÎEò¦á<?YÒy\\—¼ğáâ3…½è›³’+SÚöâÙï—œE?«~\r‰Ëè ìs[I]ô‡5EïYB2åˆ×MÙ:¸ü›p/­|#µ›’M/‹ì*dÔÖ°¬ru?³ÏÁÕw>À\'ÃBŠE»5Vö÷Çay0_š|;Ì_ÿÆ¦Ë` º’~’i\Zxb? àC>X9£\n©÷d©?uÙø\\E>ßCq¬?c›äiĞØAZß	Ùàw?:G>I·?IY°xc+Ö+6ô?aÎ€ÔvIKAëÒ¤ÂÀåHásŒqÇ¬ÚÌ« m•ŠR¥¬‘í¡	Ã}-	f~mÉ?óªº‘Û Ÿ¯>ÿ%>)ÂjîöíÜÀ{Åâ‘óz$³ot?\\*?‘ï!èëŠ¯q|½À;?Q¤Ğò‹^¸©û_ *B+JîÇ%>w˜J…ïr>—ÿıÜúO\nĞáôH·Jû×,¸zö[HîX§‰Tå	Qk\'Æ?vÛoo?’bL.Ö2G2US“Â$ß_ãå¿`÷”Í¤m’ßùH\nÑ	¯³g2VœXş#ÎÑ\"»?ñùm>ü%YTûÉ–ëd?’äøäã¡B¦°Ø£—í1 ¾§u˜ã‡Mz¶Uy?!×Å\ZD|†xj%|å‡$ëIfB¹mÊ`ÿÈ	!…lÜÛ,5¿ŞğBÈi+šÆL	!I‚—1ñ¨8–wÃ‹í’»«Šà0c<»?«±!$‡üVKH£¾ã Ô*ÍêC?Ğ¬w5³(İ]=c“~?%“÷cÙÇ€cÖ·…\n¦rH.BÏ¾ißÄfë«ÛpNR£â<t²	· “Œ]ÕÈ¼šüÂŞÏ»ÔqËuRŠ.ÙÆÎş8¤ü\"è÷ğì\ng4\"Å|9!h·,?-’áàÓÿÃ¤Ñ\'êS	‡ÜÓM\"*H\"¥ùì9Uë].?»“î@sc\'2®µW+ˆ\nLc¦^îÒz×Éãb×>ÿºù?ÍwÄå½_À“™°€›^\rOŸz!·õG±?eÌ‰ğ£ğ>?éL,ÈÏÎAÙ?XíÀéO˜wèÏÍ{\'îô\n¸KçÉ„øš5??‘Ş|èœ¿±ïÁ²&şà‡Ì‚<¹ÎO1¸¶-FÕÌbpk?¼ó­ë‘?Õ‰ShÖiš´Gv“üA,ò÷fÀô‡1”OÒ«d‘Æëı•Á€}ş/Òıà?o0U—ç;Ëšß+ñ\ZŞ\ZÅ ÛÙ¾˜×?mÀş6m}ø±IdL¿Á7	\r%ëF|;¸óbèzÏ^Ğä¯l¼\n‰&vB?#5ÜŞ÷?§%\\È;¹<ï¥²3Nì«/Õ¨,/z¨hm—‘s¢JIü1&×=¸;ß)»?/ÈFŒ«&\n\0L\Zé½~YîaIÑ5âK›w/d¨½\'\nÜ™ú77ëh‡H6x]?ıV¦ğuA:\"o?ç<Ïe9_Yoü’%L£<l+úí=óz	9¬tİş»?d.%ıìOCÔşva)®,‡¶m8LdıÌgÓXƒ’:(o”oÁ\0õ¿l˜Eùå‰mŸë?:pxºûïÖ?Ñc(àlÁ¨t;\"Bƒó‡\"şÏ]Ş\rÇf«Ã¤?~İ.lƒøgÍMS–MåÉtë{ô{&ó›G(·4·’ƒp¾äAµŞ\\ÔŒu&Ç¬Şfœm¡U6Ş“	?7]FÀ7ûP*1ia“í˜}¸nWÉoÇ™ğïœó/¦$?ô‹ğŠ‚İâ O	™R—*Ääƒ;HDÙÔ2Lhÿ x\r¾™ãŠ®{o?ğiÕPAQıVòç¸\'·bĞÅ%¹‹‚‡îUçïë\'äƒ>Gp£}áã0¨V=Î¤fQòâ¬?P.?ª4Ş­Óİ§G;AÀ&¾À\nİR¸Ö$G§?Ä¹)©ìßür>~\"Z€¬ÃòöYıöÅ@K‡¥»?ñ™Å–kÀÕ²ğÔIÁˆ÷õ…w‰IboZ8Ua!)IÌ&ÍIbfAâ=¿Ş=Éúıdé>\"ƒˆ>-!¯9{<½¶åsœyæh1Ò™tÂ†øèà²Œä×¹}ô	C?üb%>óNùu$Š“áÁ{å›¨jÅb]ÿ=Í/ÌF;„×yO³c¬1{Éü¨®ìª©ßÉ>?:nÓø÷ÌbĞœš”UÓ\\ÉBùÈ!èvı˜ÍJU.ÅŠ‰b8î3‘ÚÖ¼ZÈÔ‰#¢b‡Dê\nO›ì7S¼ÉH¸tJåhŸ0Ø?­¤ê^Ì¦’P‡…Ív#âmßu?ÄÓ¬ß=ø‚IÄ¯“ˆ<›|šŒ&@)Ô’ŒNËöº5ë\\ÑŸ§´ô9’½áı@CÏ–?öùÏ±’õÚ7¼«S|[†­®ä[?x·¨ß=H7·1î÷pŠ—ÌáÂYĞZù4?ëUáVœ³Î§¼Ç¸î{°Oá™nÏ$}½<Û0d0-ğ6’ğø™UÓ8L ­x­DiëzÑ•c¶—ªó\\3yÇTı¾Ìz|\ZÁ ¶A/a2„¨“•F¤V¦Q	ÜKÌúªÌşèÒOL#ï;ƒá\0¥Èä^E<Ät÷X›?ì+¾_Ø×Â]ö?`f¨GOcYÿ`R°ş)%m¶øÚØfÿúğ[^îÚÔŞ×˜ÅWìûë6ĞşÕJ ?\"ƒM§Òô\rë¸ÒÁ\r›\\\Z›NĞñA(e;›•Jø«Ÿ÷¯ñŸË´³%”K8[âJü(Ç!ló(Mš\"vhä¥d}\0$|ºÑíÂµ„(”`¹Y—«Ou°Èœæ7´šÃF÷M>Îx\Z¢RqÓü©‘üŞ?/Vô»Šo—5óÉí@Ó|N>o“áş?xæ¯?´5ƒÆ¹ÆL”Œ\nŞj9#=Aˆ·™½]5}c^?Q#ò’¶3}Xıç…s|fIğw\nÉ82ˆ!?JİS½¹/X”äí¤N?M,!&ƒ=±?jA\"bFn„±şù»y(\"\'½ª.Aq;İj˜¦?ÒácÎmKİ¬#Š£).(.I8ŠÙ\"«\\dÙŞag#E¤¿°‹¿â#}Ïñ‹@&Æş hüàGtœºN*ÕËş€_\"qœÔ•B¿Eä2½?}<ü*Æ[†ëª†«èÊZ/}Õî¬3â»¼™UüW·°pÕ¨ØŸA¶‹iÅÃd±åÒC´1Ï•lËÛ%ã˜~¾*´6W‰U»ò§XªN	×‹77ÈÎjxş¶Ûtğ¥r©˜ğY˜ëCZŸº+‘3éuÄHµ?UŸJ\'ïë:YB|÷§ÚÚûJ±]èşñş‹N¡–Š™ú“‰]ì‘e“ì9®„ç?qôc\\ÉRî¾¨ß¼.ÂúÊ‰Ì-8ÆÙ¬¾¼ï=ÜéöIÒçÜ{G—Dòö[ñ?ÖÛ@YÜ—Ò\n¦DÕZGIFßj~Ô±J>X±.?pMÖ ¼Ôç¹{Ó´¼ì,1ÕhµŸß•à{;]8jq0eg9_l:ÄÃyMñú	µÆ\\MCü.?0wIHŒ=o8êãLAWú„^ÖpŒ ?Âª]\'h’Û%«ÅA]ìÅXvÜ›³…ûÀXër_µÁïì3Ó?{¯B„…oô>Nï¬(xp½ùÅW™ˆHš\"ªÏ´IÂÂd-›Ï¿XD}âÿ:\\ÒÏYsQç½°é}32x¨jş¦?å’õ:eîZ.â?z³)f±¶Šƒuâ\nKO^0ùŸgcËî}\"„\0=vŠKÔ¿	†÷é?óWq<àûÃt»ÚaÆ­›aÔiÜPiAÈíAÙÿúƒ²±/–é±ßHÿxÙ_€Ş¾_hÇ»?‘[Ø÷¾CYœ‡Á?×÷L(Ã{á¿+Ÿÿ½6ğ^dıbFxÉvÚ²ÌsÏ0uÇË~„ƒXÃƒõ‚7;Áå#ùòìÒwx©DÃïynoåV¹ÉVNï¡~ùG²î$­¨\"ÿü?óƒPlŞ5 6è>Â…5 äİ…pz¹Îr~ş¤ «e½å¹*Æœ\nÕJÇĞ¹„R¼ß~jP\\\03‘ı<„¸ó¹4º÷»`Û?È»GËŸÜ×!/Ïm5nï‡æcæº*‚ïn·O[HE&/ƒna½X¸ÅáÁj¬?&Â=´÷o]ŒA¤°“wñ—G·7÷îÛ5ÁFJ–k`¨S€šy…n\0xz¥ ,É«¹úrûÚp-}»Ívxµû¾2‘!´(¦z?9”a¡²˜ğ7=8Ì²N$<tr¢İóm]v^x›\rÿ&ç°‹c\0_™y¦èrõ@d0U°å\'R?ë³o]‡2Óe·o·?R 7ãcç!\Zİş˜f²– `\"6İ{åûeg\n÷KûAÅ¡Ÿ¿á(î‚æœ¡õj)?;1ÍÀ~VĞ8È?w ûèSésÂP”çß»Û$¢Ïøç\reÎU«Ævòì¸jæ¡ev¤\0Ü\\‚ÍLzÏ•¡`oLû)&R?!	¹r»S6ÿ Uh=öX^~Ë#W¢ôñØ\'\'Òâ;|«ŞGjà7]²ôxf;PQ@<‘+³Pî¾Ìpíû¦±×X†Óï˜¢BFÙFÙœ3ã?1Éx€íÔƒ„^ü\"ØÏ§2w2hSèGø#êÑ,Ş‰fÁn{Î`„gÂ ³›¼¢Ş{8¬H\"àò÷®ŒµB›?ñX‘“s&vû!î—Ú¦ ,Áîö¨§Z˜Šø\Z…ª“½~ÖŒ]<‰)5½ÆZ>?šAm&J-ğÿ~TĞ;o¤^,$ø#hÊË?´öƒäœÔ¥\'œÂÿÍ»ó¥úã9×óBè†Õ\'RñqBĞ4šeÈ’åWS2é«w¯¥~Ñİp¿Ê%şE*?	4L6Ñ–äà[½¼¾IeÆmüÂzˆ?ÛÙN´‹æ¬Ù;° @¾ˆ‘(}ª¼9=\\SHâ¼ÍNX»aÀO‚‡8ÊºÃÍ\0›%l¡WqèCÊ\Zğ{é÷—öNìé¶Œozoğ§×üùö?xJÕéb@v‰o¿µã1{i6IØkcó\n+ÔAú‡~ƒ›b-¹ôô¶k¾Uİ?¨?hx€aƒG–µØ?Ê…	\'<×±Ğ3(^“ê\"9¬c¬dêƒşæ~‹€}¦·Ÿf>Tø¢@Ò‘kfüü‡n5óöpF ­íùç3÷\"Æ|9÷BÇ%é{áw­.U?¹™#ÖPS`ñ €ÂtªÍ<”°ëÜs`°ÊŸÈ2\Z5œaöeÁW\r”Ë¤.î®òö4ù!¿îX0¾Y¶íQ^°\'õy¸>kî\ròL*^#ñO6XÀ*ğJ›]à@¶À£ôÙÇIıw1_Ñ‰ÛÂïícáOË€L;0$Måî}÷D+ù	…aœecotrÍMS5Ûe4,,?şù[°Ñ\"(ÚÔ¯|ÇÙãk¢C_ÎııÖP¹Š?¬R”@Ä£êVÊ¿ÅŸ(9Àòuv>ÛŒè˜¶‚Ä“gò\nb[ŠY£IÅT<]k‘[ô)ÂDø[«ì*´ìˆ‚DéIyëïXå›¶eÂTmsŞ”\\K w\rô‰Ş\"JØKı:9?™ìaNÕ<ÎÏäUä‡Øé“S»“[4j¿ï©xãé±¨íP\rÆag¸øãA\ZyÛGğê?\\iF¸/ë¢|+Ş;‡µ(«¾I9àa’Î¨çä2âÒon³¸ŒMâÌBmG•q„BÑ0˜Ñİ?iÛ»…®²6½?üä‡—Á¸²·weuèwĞ#³Kf¤“á+–4ò1\\Î(¿OİqéO\\Ÿ£ŸzèoˆùÎy Ë_e„‡RîúF3?1İ›Ó;ŠÕïÄÈï¸­“[6? Á?•²İíÌ(ınÎÎ&òì¯êé\0ü=b\0İŒ+ó›»Š=ò¼zœ¢\\DW“Z«îa~?ö*½8F§˜1KØV¼Sà*™n‰€?}|Ê®°L@\ru·?p¿Âò€¥è\"&ßÅn3âÒ:øtbõ‘¯¬Ù¢İ\nWE†>ÿI›ëÖ™\0PòÍAÅ}f®óËL+fú\"^¨«Å‰óÛ×_@—é~ß«tÛÍ<ª×Ãk1š´°Õ«U9òêN\n[<?õ³ñ”\'TU\">©l?	|Xÿ\"Xn£0LÈ\r|¥l?˜÷2Íöåü5ÓQµÒy_Åû@+´_wlòÚl-?k-Õœ^$Ùƒà¾+ÿXx¹Ø½öÍÁªà=—½ã8‹í€ÍoÓõâø}JY¿Üz`»îß‘„Xò¦7S\"¾-Ô f÷­ÈÑéİz¶ï(ØxÌõQ˜53òKc !&-îÑ:Q2×ÄUˆ5:xˆ‡·ËâÀ´?·ë¾?dƒ?jQë\ZÕ¡›:´ë!\0ˆ¤\0~?+ \ríÚL}¦Şø¶¢èƒÌy–¸ˆ½ŸÿZèÏûÙÒ³½£–9·•vBëû?ºªBZë?<×™×kà\"€KyÓÕÕùÂlµÌcàÕo‚¦=G‡ë(–ŠùXã»	ÎuòïAÂ?™Ë\ZÛq7Çòßç†¡Ñ-2ûÎA\\ŸÂ·mGáUÑ5À³1>îR¿_å¦Í·ßt<@V?kå«©S+ÑA[1ÆDÍ:SíÈ:?ÆâaUƒˆ]£KçW5Ûx¸âCASxÛÍÙş?Î(Ğ!?p¼²òó¤æZ9GK®?\0G‚­fëü¨D_ß‡hõ>z<l`æ‡V{69’¼NEJÍ…¾õê$ÔË¸yR\0?Ó?18™_ö-ö#^©nF}ÚÔé[#üœ”¼áø#L‘®Å~{1ê·š­`Ö½2­„-qèò£‚ç;Ê¿T/™Ëm–ùJòÃF¬ŸI‘4¯Û„üqİÈN¼®üëv‚ç]~€d)IŸtì»%±ÃY#êt<‘bş¥ÊôèÉã}gÀ÷/\Z6ä?µ˜|ñv\nyòõÂÇ>e/ß$ãF(mZEà{Ü:j7¡~¹âÏÇ3ÑæÏ¢OçX‰Ã¶p0µ%\0g,?´âõ¢xO)\"M\\ æ?ÖÅ²÷gğù&\\ŸæY¢ßË?M:\rùIÇß=Cè¼ŞôÑÜÙÙ‘úŸxğGüZÀÁ<¥ôš Üzò>â!\"è·¸•$ö±‘òn{£ÃmÒ>ÚÜ´Æõlm3¹úes³ûíöºå´nİ?†+?®yP†!Ë:qã??™êĞ¡9Xƒ’‰wçhReì+«–‡¿ªï¤_¼ŞâêÆ{z­dòZYÌ»ºü,~Ş¢Ôö¢óÿIë¯*õÛ ñ³l¹SLÇ–Oú/>Ãœ7ò¥õáÆÀßØ§?|÷bPY’cÔ +d²ÌZ?äÅQ¯.·\ZªÀ‚Ù$\n?­!ÑÍº@®~\0$Ë÷áxŞo—T°?µ¢+´Nöô>-‘&_xğ\0¤Y¬š¸T6oó6Bkíõ¤Íëaæñ3:C2¥?Äñ`Æ§º#³O![l²µíXÕÒÃJ.¾é³8‹uÊÖ¡ïã]íH•şâ” K#ŸÃbì¤œ9Ãüˆn xì[ü¡c³òÀğË…Â”n\r*ã3°ª?¶æNNw±?åMkª§¯—PØ½KI¢óß>ƒ-Ä¥ R‘¦?ƒ38²’Bjc?ş4¹°Ú\\‚ÆÙ–²¼şÈZ¡]NSÛ?‡°à.Çüäª»;Ç.Ù(b¬€Q²[÷IHñ‘ïeï~Ä.Õ­2È¼@ é¨ŸQ7Ş¹ı¼	àñõ\rÜZ^2ZÇÀûæàÊ¶»@ƒ¾môxHóHI—¾c¨ÿ‰åCq“¾ÇWg ¯Ş¿fdÍ£w7\"áî}4R7y1?›œ@¡Ø¯!_ÎQâ\n;i”=¢§CãG-Â¼<”¬%ğà[»6xÏA&:ı×¨š(d´?ú}_p®ÒRÌLT`,ïÓu×TºÅ¿óW\n(š\"½?„Z„(p]¢«ßæ?Äª×	mÄsoû?$Ş¾?fÁ¸?}d•~eR¡,~®l&–ru¯¤I\0¨‹8H’ÉÛ¹jÂ§¨€hı{Ö›waè+¶	í*­ÇzÅ–€1 !û¹ˆCÛà&r2µ?b?µ<ò•@­ÔaôÈ\0“ĞÊÀ½£®*«±Ùghï\r—ù_À$®L{Æ´ê4(?MK	&dG¸~	Kš\r-~‹)zôç*r§?*Scš1âNŸÓC©Õ%E8ÚoI50Ç1x/5ïõwäUbHÏ×]\Z¶Ç³”9>zˆ/ğØƒi7~v‰¯v½×d€?‡S˜¯¿ÊkïUÄëR£#Ş×¢(^%=Jbı}€X@ír={TËÿ\ZÅg‚“z.…İİÖd%ìCX!Ğ±Bu–¬68…Ç\0V,p²Í]Ş§UöŒñáu’;±d!˜Øb|ˆ?˜—¼~ 0Ø!bÕ×ÄÕ\Z‘ÁªÛŒò%òG3¿‡qWR<dÿ¨\"Ú×ø=uLã\"…û)=1Æ¡5Æg»¨G’KN#[Út›®Ş[ïÇméD%8€=4˜C ‘ıÆwï°½C,[Ú±D®?ÛÛŒ•-™6ªk°·=®LƒWÑ¼ÎœØ[—Æ¿üôP…)²®Èƒ\njwŒòüïGaÀı®ş-rÈ¥EŠÁ“õ?¤ÛËß>ŸÃîÓq õôÙ„ÀÚ•_¸Êûı\rñ\0ÄdHõw•Q”§Sì°0kwmëOİ¬Á­SÀé½ÜfRÖÛëEeGD¹µ ¤-àİj?MÍóL ï|´}Spl]hùı,P8òñÁÃpD^¸|­ÒùH~$Ü‡d P¿é¤íJ\\ß ÀÌ@Bfîk_¿Ÿ«g5˜ÆÔœÛ‡–Qt¡?çh€‹Ğ”®ª~w91Ã=Î¼€,?†Ñhñ?V@ÚäãÖ+\'·¯¹‘„”ÒfC†û^Ş÷‘ô™<MŠÈXPìk?l†yrî!—Öè˜½Ú¡‰á:_xê½cÈ)Ş°_V!µßw×?Ä–å€Š(h©Ë\ZÅ¼€#Ë±•:ZF;îŞ¢$fıZb„b2$™9N‡`g^?KGNÎº\ry…\n0¡ÅYóè¤{“1ÎfëßUR`Ã$xÍN¸ˆ;JV~·=ÎãÛ‹›çl®?Ğd¤^ä}Ú¼sÆ’K\0Ş~4ÓU¶?:{oåªö\r,•u×ßh·×4˜ªlº¿QU¢çô›“ã?;a2Üæí‘NÀhc?šš0l.>ôP#??âA‹´>íU2×½a[}Çud¼+‰6ĞOŒ?ê›Â½\0ßÏ¯‰§ñmúí{¸!ƒ\rS u÷Hù@v\0iñts&ë²¦Må¿Í$r§?íP8‘ƒJım– -§Ô0åXß„|û;?“j¦›b“ŠˆAéDwZ÷ìouÑin¢¼ Ìl4¹\'+\rÊ8ˆFk>Ó²G‡cNQ–Rn’7ı¾b´‘}M¬~º6Ë¥gC§cÓĞ½ïAŸb^VûmT±2YÊ¼7pû$ãb2õsĞÎÄ–—5ÌüÙé­Ô•¸¨€ÿƒşlÄÙ÷î³ô+ª?ÙbÂ½ÕùŠ’X?‡W>êùMkÏMŸø?æÚg¸ú´yçÔ°?4ÜÍ·‡,ú—iz>“r<z¼e?„œˆeb…¿ÅÖJQƒßÁŸBÈƒÙÂV•\"¬B5Q×Û™có”‚w$À%1ìïtßçW?E¾zl¤Ğß‘¨¼1\n½8UW+¬“Ö¡?˜ùe…™íÄ+¡¼ø\'z´ØÙ0Á\\§5Ïã‹³¾@ºRF<»r[ÉV¢tÆä±´$a)2b&2j-(( uq âSéH5@ö7¦ú­ï˜Så¦+[Ôhƒ­6¨\"ét=2Ë¶E§k°ãg˜œ ×é?N½x‚?mN¡ÖæÅ°º™ãt˜ »íÊW?a^ìùHŒ;x\\È—S~\'g3ÇıúêŒä‡á\0/úã¾Ù•aûıC¦[íı<îgçÈNşJH¢ü5~A?a_£Ak…Wæ^•\"ÂÌèĞFÂK.ıç$ÿ=],\nQ`*Ò51G95ÁJ¼á=p¶¨\nÎ“òÏ»¨òõS,>¹×„äüºY—Áãvp\0³b8Àè÷ÔÈ#—‚ÜwT!0°·nÔŠ©²<¢¢>AÎ÷ó°D©Cåk•äõàÏü`tß­LöÛí~~¤÷nÂ\\·Ìˆ8m!Ñfpò¬åÇ=nRS°Íí£1…¹NWãúÑSmÆĞ\'#wz”Ş_ïî:×|Å¯t„‡V)kã‘í?oïÓ;J‡ŞÇÖİ|’¼Wíkû¾J¹Á/ŸØ	CY¸¼ğwú­†±Zì\'ô„]?chn=n<?ğú.«¸ox¹Ü»®ø }R&^9ß’Òœeñj\nGcæè\râßA?YG¶ô¹»ŸÍ?m®«£‚TvmÂ[g|á\"äÏ!Né¯™œçc«~Ş¶éïup°\"ñhf?†ê{¡,kç`yÂU¯îÔg‰ E™İÔí{tz>`rœlµÎR†š\0µ¹¿Ã’sì%M{Š]?¸í~„hëØ¨¢µä?\rÕ$¯a6}ú„g‰}:\Z€!#FÅØ.ß÷‘ıY}¹e`ÇĞ0Ásæ?î„…fúîsbÙÂ°kè¶ö#ì\\3¹Ãë€©\0·àp šqk{V€Õİş\0ò½µj¿+z¶ìß±šßÒr3Õ´Æz×Üfí‡zh[5¦ã|¼†5Í|\Z*3ÁÒ’?äáêGøOäÈà7á ù÷0­¹°rpÜåìŠ5ºÇ °Ğ)#ß°8\"ë	æí?Ó”(?CA«äĞ#·0ŠG?Õ¦ÍfÛÚşk¸%B±[QË‹÷?è\'ˆ\0ƒ¿ûºĞ$i3\\ôf@Öä¡‹Q}\ZKS¿ììş‚À9S?¯8\0”Rp”ÑAqV¨ÏbÂ)4¹´×óùÑ?.s+ì…´ÊÃäÎ5ó´K¾¿ëÆ\\xbI5šEµ¿Ælµˆ|Òşà¡\0‡)\06éû+Ô\Z¬Ö¿q˜cwY?§Ø½ÉeC<,°†Õ¼TàÂ9¶Kş‚+—åÆIîîÀª;!Ùø˜ 4ÆßµbÜá ß<\"s3f?\Z?ß&HÙ¥cÒ.½¥‘£yNqØ$”@f+Ğõ!=F5;ˆo{¿íH7/z0\0õ0N\'eY05®qÕ•\0o‚B?‰Ù`¶“FÅU@>ıì-bùçI‹!©1AXÄL´°Şuœp#ôf7.©¾Å?§•é£6„6¤Wêu?™†ÄÒ	•ÁòÊƒ½·\rN÷ß¡³‚û)&ê\nÇá×XÎM¤“®Nå-Ğóxø0ë¸\"?Pqƒ¨àÅƒó‡^„è[ÈnŸ6\r!tpıRÁ ¸-\n?ÑãŞê[fóÿ@Õå	ô2%Ğ;œäÓ+¸»M4ë4ffÓ´?FEÍ?˜QJ§—’#fË<<\0)ï?5˜§{ïÜyÛû,û>¥*ÓŠYÿø\Z}S˜ÃÀ›Í‡(`úUÉ\Z•\"¾ş	-V÷Eï;£\råœµ¢Ôµ¤g?ó“ûSˆL¢}2Õ8Şº*ŠTäƒn×uÉ/,¶Ú>ñ‹æÆûhìRèéP~…¥-z óã£¤L*ıù?¤A/D=ÀÏñİG¦×e´ş	9=TY€\\Ç¹÷¿JÛË_î«[H[ôHÊ¡J?Cù-LK‚7|FrG)”£›•”~§ËŠ\rgÃ¶Øö£»Ó\'½ÆŒŸªõêö>ÄÛŞ\rõÈá{ÀoïB†?­Œ\nÏĞpç)<—æ¹µùrœ˜¾™põÕÔú8x?Iª€ROñˆ~àçæJ¹›bsÄìf?4F¤Î¡g‚9ô±µÊ÷¨PÃ¾&°Ø\Z}›–D\rÀ¾`ÄÊî‰	ª‹<oƒl§`á‘­—}\\ §Q¿	ğÎ#›İ–oÍ‚±2ŞØIÎSC®-Ìq\n\'fM`¨×Uİ^¿÷{¾v÷Ùî{âg\'5¦È?sm‚*\nMìšº?u„šSÙ´’*=ğêÃAægxÌ®!¬w·5G=£æ÷Ÿ?¯ôh±æ%TVT?€$õÇ;H·®°¥ªéÕÏ\\ø§¶[İõöÈ3D#ö°¶7\"19+{¢Hág‡ßš±§\0á9µ3é%èb\"~»?–D$²N”_™m\\ázNMaO_ıÄ£»¯`»ƒÿyÏëÃuÙæıî‹Ÿ\"_†4Å—?fc•r–?<ñ¸BõM¨;FÓÁ9’Ò™^a—\r¸lÚE·@7J\nµ%Á»Ä¶¼(çÛ„PZË“‰ç[œ‘_±Šñ`Ÿ›°â04ö=ÏÏ?üùRDÊ£ÎÊûc™ûñÚD%ùÈç åpÎ\n?#_ÖxäGûãã¶?şc¡-“ÅÄ ŠŒ6‰šÛ7?¼\'3Ÿ¤lm¯U›b\\fÒ\rFCäóc¬Rıo™~şª(î¾!Ì<}İˆƒUs?“w’}¸?±ù.£ô,‰aóS8+4Ò„•æFÒJÓ}E1ËÎõøi¤z?¥ÖØZíÏÑ¸Zjç?¶SëÑ­µÅ¤Km4pöåK«ÕWø©ç«o?ßÑh-r?iàà`II¾áÑ8»Y§ì|Õó\\ÎRùcÈ\"âòZ7° ªx9Tƒ€¶u‡*9ŸØ``\Z?éõî ¾|¶U³Î–z¦¸Óé‡bÊ–&şYÓl+CLb¨_ã²}Ï»ôq©4°1N°UiItaøÓÃò”¦ĞeSÖ;Ğ.ÓMw–3ñ2·¶!ìNx€B‹A²äiæ‰‡u Ö(äİ$·ì-%úÉÃşâø²p\'yíì™Ÿ_T8¨©·55ƒWKì}_?¡ì‡6{ÙıIÆ…¬ä¥®?¹?!W>”Ü¬#ŠŞk5\\…ûëÇÇ¼‘Lö¤ÌoyQÅ›éy¶Ÿg³ÍÒÒÁÊ‰Û!&aXdĞ-©Î?Ÿb7×O¾¦x$Ïº³Ãm\"P ê?¡Xİ*åD*îè÷J¸ö{Õ›Ò\0\\m?uB„Ş6ô×lÍ‚=ıJRÎ@ºn\r6Ûn×x`©p^vuFD_I!*J?u 9‹¢|´‡-ÖõkÇ^€Ÿg›?0–vÿTÒ²2mó/Uüª*ã\0æÇãEÀVÎ›nw{?³¬¸˜Î$’¬à´Âö?åJkmç^ S:o§xúeøŒa¸—‰Hqäİ/ÕÇàLã?ßdõ„m„‚Æ?­˜aá?q,hõ¯W)’QìƒmäW×Pûª xŠXh¯k1ş€ıŠáJ(j2<`.nÙ/{t»úL®Y2|‘~	#˜”]]+İ/E”¶‡|]O`;öìˆE?LZ¿–Û+¨¼ÙÈRØœ{è÷Ò[ ó¦?dâõMÖ…pF	î˜—Óã±	LúµŸßÅë<éc,ƒ?2{=Âş%\"u™q~‡K~ğ¤½üí¶ŒØHa3ağØ•ƒ%i¬áÉsºMÎ{¬ãb´\n\n¯z\0…ó°P³?cØ ¯^©É_9ÿÄ¡m!%âÙA*ÖÔçBfïs#¢\rÈ·#n»¿\0ÛùÉ›î¹•Ù®?¸1Ç1Wúá‰U?áíIŸÃ6õNb9;:!8$Ó÷äM¬í*S0‡÷ÛC|?5¯Ö,L¯sdê«	:ÅÿîÇ0«·<µçPgHKtâ ”\'_xü{Z2	e´ºMTÁÑØs–˜_Û|¿.3uƒ6È?{aØr¡¶J ;™÷Ö|ùÑZÎGoº^ç#ÎŠZb¶·1Góm~7=b§™äŠğ[n´$ö»8Ö;ÌÍÇBåemücœ°Õ?gÛ%§ñ_m~\n¸íz?Œ×œgúj\"„{Ìn½6‰X1jA¹ì-Vİ¼şé×Í5k‰]»}×¶1S\ZğÜÏi€dÜ—iª°Ô)V|¾PJ“©ÖV“_c‚ôËx8R?-›ÑPŞ:§¾=‰şvÍûİè¡µsz-_çùŞı?_¹I~Ú_v®ßÖ\\3uı “e`æ‡ŠfÜfÄš¤F<ê<Ò,{õìæ­\"Ñèüƒƒİ¦*‹¡s*BcsR¡³çˆËrŸ˜œ\\~†-êR|‡¯ŸY?Ğ\nNA+YNòŠ	p¨w¥å¢=ùÈ¶?™£Ìù³ƒ?K«€™…W­åğ\\?œ¸Ë.zù#ÕÏ7š>1ÁAÚú€u~4?ªûdPÜ2.›3ÓŸhÄ.T]$ÿ\n6P­Q;Ê+ø›Äwàõ½!‚?ßJÅ\rxcq%…µº‰|¨áÜ.@\0ÄËÿÜ¼Ì¥æÒ—Á¥›Oløíh” ßÛC7²yZöGüØw>Bsp¢Ø­}šEÓúIî#YÓ®W\'3\n÷ #f\rìú˜Ùé)sú´d\'Bñ=?wÉ¦A·O>Í\"ÌYÕuËİ.ª’Š¶?ÍÚ3É·_‹¸‰¥×[2uã‰U2è¼º{,¹tÍEæû~Ô?‰²Z?„(C®¥.ä¬äÎs`Â«‘Ü.ÔÔÉiõ•òiã\0l\rö1}fZê9ë·´5¡o/†laµ”\ZÈpO\\ÕíF’\Z½yÿ} 8¿ñqáå>=?´Øw5ê‡/§ÓP öbWÆ?;:—|,—có’%üé*,ò`?S{Ö ½^DÎas«b?Û-4 _ô ñhÂÈ ÔNğÂñ+â;_ö?ÔãøñC¬½‘}WZó‚<ß>ÿÔÛ\">*³?G)¯„üÒ!ğ^å1)ùÑ#ñ2Š»uLÚ†©7²x£fû™ºóBxñbv’ßmv¿§àHâCµÏîòq“HÜ·Aº÷/B?h•­	*£?r&ñÒXš‹ö?¦½ke6ZO¹[M›|<”ûÀZÿÛ-¥õì?r«í³)ú——êef¯Õ;—q§7#úb™a\"¶˜©? P\r5îs?š¯3­Xc´æMz?VëËü½D1W„eN²ü\\å`‰İìğlJÖád»Ö³²?à»¨¦yÇ—S’¤E‘ú«â:¿”ó#kWå«\'\r(~õO£ú=pöQÓ=âßï±\rc½O?&Ë›ŞQç«Ÿ9¾œù|šıq~1MNjP¹]}Tß…òi*wsg¾ “ˆö–„¸ñ[^?Ç?Šï®ä”§†ÛÓïŸóLŒÖè¬Á7Eøp«æ;·_É@¿»ùº?.¨Nj8úùñ_ØT?¶*Jg/tÑ._‚{Ø!¾kzó_ãäö]\'$‰ˆ~&Úë©€ë‰°R+L§¹j®B³kûúra_r\\mŸ¦ïıŞgÄÏR\"¸È!2{ıØ^c÷‚ªÖÁÕáğ&@atÌ?LpÂˆÿNl&Y²uxŒ©ìA_™°”Lé$¶ßœÚûëE×17\ZUH÷…4EĞ°z\rÛ{å¹ÃÛÜåW]™EÎ&H&¹qø•I*?Jw¿=ytÕ[6F`Ê×DôŠğ	m#ãóçå¥êäÛBÅ6ôÆ¥nJ¢ë.Yâ‹J0¯¸X]é]îó¥_Ãƒë\nâ|!aÏ|$^êl%M\'ÓJYZÏVÆäY±ËkŠîzõ3èY(Õ»ùÈ®«û7–f°^ôƒŠ.û÷ƒµn¼Fÿ0>¯ªÕÿ†H§é?_;U©òı½ÉgZìÊ*æò¦+²ç‡‡…ãüå;¦ğÓÉ¯rJ¾»×bŞñÎêŞ¥?¬àÿÔ~²lĞYˆÖ¬,(¤|YÊQ£—ú‰×§÷*À!æü)>Šâ&[Ğ‘¹S?µ–›kúã@|?İ~{‚ŒûjBø46¡	Ş-tµr_DõÊ?,1dˆ\'™İ[ø[ìx>kw;òw¸ƒñ›s?>M	pô<ÁÃ\'Üëçø3³õ]ÊY:\rVùÓ?ùTHQğ¸]Şx`D5îf¯Ó,Û“o”»ğ4§BûWG‰@kÌÕh\\ŒÃ›™œR,EÄÏ[¼YMTYN?¼\Z¾³Î~ırµÆOõ|mşÚñàPvB\r ‹Wà–=aÜÀbâ±“{¹#<½Û»U!\r1Ô=4:9tÍ)¬»õóîzHÚ*0(/2ázïŞšü¡¨XJû÷Ù¾ï6?ij£CSÀ`µØ–üœÅqĞåkK/Ñ1õ*poÙÃb?`òlu¨£ú#(8òœ¦+äFbI€BŞWŞ\n?Ãè Î’–¡W·ÜË?.´\\pTm„üöB·[8ÔbŠÇô´ŞÅ;\"<Î]q˜OŞ~’„Ø…°àI\"¯ßÜY€%{³Sf\\1¹÷n{ÀIŠ.V«f($K—\nêÙ}¦;?™ƒï?~-šÍ¾Ì‹`ÛÉ\\ô]œÄéŞ \'§` lfŞ^Ğ4DZQæE/´DŠ§ƒ7M?¢ş¾å?ŒÊµhĞş¾…ÈcAFÊaº/s=êœî]\'¹ËƒĞWSŠÑï{³G\r#0Èº¢*Pq¹tOã\n\\»+Î‹Ëm;âí;”¸º|İPí×m|×°ı«],è”Vp.|V¦ú\n\'9Ú=¢ä/¸ÀìÏ?©¢x¨­ç!\\š;0á¹–+«¨÷cû Î¢7wcR <Èeè™=ºá¨À TE+K“%@$±ĞÂ¼Cà¨ò‡NY,ÛIÛ-×\"P{ùŸèjÙÎèã$DW“Š-×ÀmLÃ‹­†¹Hcib±YQİ•^£«?mÒ.\rÂ}÷@v†Qæ¢³·²HVß­´ÖZ}’¿cêúS2Ñz+Ê<!$Æ=X‚±Æ,_ñ’Pº9Á)²¸U;•”¥á/BNørM£?\\Ú_Z…,`\\L)H+Ş©(\rğ|ı—Êà‰//õö?@ª0Sœ”‰’ÙV_í9Ò—%-€Å»CïŸCÆó÷32Ë°©BO<‰¯‡·‘‰hpTw¯z:4ZwKöô¦ò£ëÕì@ßdÙ?œoÕ|\Z´±¸ ;éMÕÛ®ŞeZH\'y§|şGåğkÅ¡«°ßho<<–\"Ë+l­‡#_©?›óª­\rğîşêvPàËÈz(9³àÚ›©|R÷[[(B§-Y—Ó1ŞÁ?4>]\'MG¸ùıŞ”?…¶á÷Õi€‚ß¾°]Ÿ#R˜æ½…ÜÜ±‰c`?c¾€™Á\rF«\'t®÷-yyè|áV%|BÄ[Rj(5`pa{‚8líÏéQ¹*â“zEèôXl)ÂÌ†|ª?=åø€Í}Qh¡Ã©yîUÕïbîºPwr†\\…9Şüô›ÙvÃ	©ÖUfÍıX¹:Ëö!)¥ÒíŞîÒµš`HCš<69Â\"©\0 ›Œ›lGi\0ü}ËìÉ˜¸Ú†M°¶z®¦²(„ø€°Ÿ›i)9£2^ŸQ7?Ph ü)Â`êVùä´@ììı«^\Z¤{=àz\'0RØ²?#ò>Ş)L×H\n„K8Gæ2F,‹u·2£UÑqax?Øğ×mn]VKbxl¨1-Ö¢?¨Mªxà{Kúg[\rt.T¹øÇ¬3¿*VÖ\nù•8|§ß®52Ó˜…:]ğ‰¤¸eü5GÍ§sU—âö5t6h³óú–ãb÷I´„ùjçá¦¿¯õnA²¬wsÀ±ãö¨+ÃÀ?´¸Œa- Ó½à\'ëz«\\g?”åéˆËç“œ5çæÏ<ûÒd…JUİ_÷Îk	éGuŞV¤¥DÖ9å{Şo	‘¸Ë3t1Öƒàğc^\'5åË\'-IŸ,ŞàÉÿ‚®ôäg4Ã¹E6C‚önnÀ®\"ÅÏ»Š:[y}+İtƒ’˜p:‘Š3D2¿bá%l¸Ìõ\ZçÍìíúÎ>¬,,ÿ_¡÷ Yy7Ë˜ÈgåæİsÙÃ)ÇE	´µÄ®V£†wŠ8#²,up7!»fè?‘ÃV\'U·–ázĞë²/vu§»•÷2<sw`áÜ±SõUó…°»úİ²?æÊ¥Mè{xì,ØëùÉú­õø/ŠM4\'l#T¤zö 3$Û?ãeÛ4Q*ñN÷ã¼ÇÖÇ)’Z\"qCÏİñyy}ñú,6D\'ú¶9¡;µ?:ÒÏF\\9·0A€6Ãˆ>[†×Œ?á/™ôÑYÚ]–¢w¿5¬t‹Ò,Èü™Ï‚˜eë¡Nï_yÀ`¥æİ\":k?=Yy­ÄÏ\ZnU\\ÖÛƒgt M+¥wY?àÂïŸÌ‰.ÖÇ½æTå?oõ2:à`Ìï—«öy\'Æ)öGÄî¾?ÏÚoÚ#Fğíò^%ğ–J{µf2wÙ¯w×Ø\núZÁ”¼-Øãç\ny\nÖêšˆî9Äöh ß>,!÷K´^h¶[ÙNe5Í*İ\'°*I+xÉX·wjÂ˜ÑF_¬ÅÑ­Š$?;jÛ…,¡Ó<§ë²{Ë . -ïX1\"¨xLË©T[æñYÓGi¼²l(É¾/c?íîö@Ss–Q\n•yÉ\'ÔI¿ zĞ÷àÕ*!3~?pD\'Ô§Øó¢Ù(ºg\r¶ôX°3õĞŠÛäËµkIÎ½ö¬…ÖGrğûqé‡ĞÜĞM§ê?Öw­Âç•õÑ+\"c5İ\Z>ãÊVa‚X©8ÌÈİ?ˆv·?üª÷ÂMç°A#ÊliBêò(Ğ^4hğñZÓ^„ârïÓ¨ÔgCŸI’ š‡˜²-’ÉÆ®i£=ñÓÓÆL¬Š³‚í`4ˆ0ÉÂpL§½†ïŸ ÅÎ¬kQjñÎº˜Ş.”„Üìd)²¹\"ìÕ}Ïš¬8°Ù³ùÜRYd!(ÒRŠæØ\"7Ã¦¯‹¥cjcš÷:,(5õÄÔıw¢×øè0}ÍşB9)Œ\n?ŞfüüšaÄ›4¡ÃfïÃÑ^øÓm‹işÓ¤p9¼½–u Wò{‚¯ì–móµµ±İàñ÷Gö÷\rO6°§Ìò&T¿ø$næØí\0?FÍúZ\0Ó­çmôU?Àli÷ëêx)e¾uTHÄÙ¤¦ó°f3Xè¶Æ½\\?©ç†´\0…ˆğ!½4€ı±–* c\nkU?ì?ë ê?°Hìµ›¶ôp“{l kEW·bâ:|‘D!J À3 t·?Õrbuø¦«Nãm®Ç&r3@Áız,Ô?íH’’RL`@®ô¸ëê^Ñ$Ö’[™«ñø†&VÁ\nÂı1Áç|}±tbÕİ´t¨?6ğ¿àbëÌ§0tæ°íãôá«K„?7ÍúÆ?Q?”6Ãt³Ğ]²ÜÖ”~ôÏÀ¾?\0=Š8êO(¤±”Œæ7¾Æİ4?rVŒh°mÜ¿ËzÅÖ^…ëÙøLB©ú˜¢wªûE::;–:æJºNWm?*´¢ĞE1½è.Ñ÷vØÂ¿?·GÛtä@ê†í\"× øùÎI\nòŒn$^L\ZM¥Ú¾\'Œ«—ÄVîƒrSİ?«£É!Y(0{(İ?İ¯?Jjü„2	ùaÏ	¨üJzïkp;zAÔ$vÄ‡­ •u\nÄİ~84*¿ïçˆß«ÁÑ9Äì&\'VB©ÔK^2Ğâ³@Æù\r¦€¼üX3ea`ß\"D ¾I4™›ñ±.€¸­æ¯,ïuXW‰ÿûªˆ?Ìóœ77F€Q?!µŞò\n¢‚2u™çD«‹×‘ûdĞÆÇÌï«ªa2Ø(¡ÖfêÚW„)Ş¢ÂÀÕ7\rÌëçšúññ!Zğ/¶|D‚Tº2Ïè5K´æjdXµ­q?ûÎËwÍŠ¥tk~,ëÛcjOUzÈò\nùa>€ˆ\rşCú?Ò[úÑÁÑ†n?ÒY=ã‰ñÕ\'ê1Uğ§ÆK|Ğ‘°>îwÙ?)ÙZ&—ò=¯Yò‘…•0£M}ÓB(Z\0­øµÉ{wibkMÑNçëEÇÇ´™:£ÍúÒˆ(”{ÓÛµÉÖ 0à¦Ÿ?`)<42»Æ°v¸°„q=˜Y[Xñ£«¶ş°cÀn°íïÂGhô4ç\"j!,»é?=#æÀ0§æbPÈÊ†êÄuz·øû}7Ï¨?™‚ÂUÔ]æW­úë5;YçF|İ˜ÂËUÂZHDŸ>_šƒ=¯R\nãÔ‘ ¿~çÚVYÄã¯z£É?1SFO(ŞĞJo¼™È\"T~l?y•8ó(õy¨åy»)?ğ¤úxØ.sú(³;pÓÕCm¹aÑG½¾Õ@¯ñˆ:?È;O]&:u»ìGj?6Ş\ZÚóñgŠå\r”©¬ƒ&&Â¡¾`\nÁºBäı@dÛîŞtÌXˆçS0Yø`ıˆåb\r‹‹ô ©:óéìø6b?£¤˜ñäÓ\0w68zóî½§FÏÙ;B?\\šb+Á=ª_gÒ_x,·K\'„sb%‘½­S R_;Ü–Œ?ÇLıÎ„eÉèûáâz¸Ş${Ï·“ï¿U£È\nÆç;ĞO±mø\r×bÓĞù·\"j˜¾ŸšßÈl·H]¬T¿N`—®ayæ|}`àBdá¿Ã¹]ÖÍ>ÜíÕŸA¼áƒ}—ÁºîJkS›Ép^£*«\\x/…XÜ\\ĞQÿã¥GJ8À[·9M?èF#oEwĞ#&âø$?ü[öİ)rù—¨„û!˜~Ç§ì­±2jfå?Ì\r¢~İr>´Pë2hô‹AÀxüSšh0éY\'?gŠÚy»ü+yÔšsƒ\ngZš³˜;Ox^6¿Ãu—ıIıe¡ÏôÓÏ3‘5@¬)nıÅ),œ‰)âS--8W—U8Jeºs	w?Àdz@*Š{ÀMU9;7v1êîÉ7àAé$%D¤¹pêäÉØ\n“1ë0ıç8Îk´?äÑ<»‡×³ö—‚2¯Á–€Õí…Gò¾Jºµ?L2>Ú_•QŒ”–ŸbÎ/òô72¤-×št7¿÷Ä9\0£lpWŠ	º5Áú6#D±©‹N‚7æ¨µ«iÊ¸Ã“&‡H¤GGNA¾$a;(nÜ…±ìd`í0FÇ]$Ée¬¼)_‡vÉ???õC&|9hœ\'…=ºQ3z®Ö“!ôÈÛwgÚªó!dÔÓÛ$e‚Ü1Ú.Ô A‡h\rõ\"íó‰_fWêç“m»ÜwËéz‡/¬Kz\röÛ·âƒ2/ˆÒ°6¯µD4ÖF±ÿdG|Q°U?É—Äq±í\0Äl:>\\Iô1×V%EÈ«äNK1]h–èÆÚUèÕoªË¼±…EC‰=LÙ¸ÀßªÅ-$’~4gˆ	¹	É{~¬o‰	?=_¥Ùö·Áâé•3ßüñ¾Dàn»0óP’·mìãÙ©4Ï¹z©Íq3‡‡\Z¾Ş×Aº?e=\\S•Œ\\xàˆ’,·eÿ±5Ôï}°z@7şÙ#¢÷ŠÑÚI„7½ç&^ğ›_È 6Ôÿ××vùÁgá0?±íˆ•7í	ã/À+?bT¸Ò Œ<³DV´°âfB“Ôw\r˜+ ‹q—¾Ê@Oãà©¤5,S‚ortŠ ?–õmn0‡\"€µ¿ ˜Ô9uÖ•ğVÑ\'§\"?kCäÒ)W_ÀÜ›‹L\'æW” ˆÃ²?Iß*Û+àš“Åµ¤œ§v$]&ÌÎôı\r¬C@KnPn6•òA†S¯ìSñ§x^°’áã§S&ûƒ?ÇX{5úQÏÍIäõÎ¶´¹\"ÏØDÃ˜«òj„?¿k:Ÿ“ÉÿÖïÑ£~ş¢ºhI¸gÏ¾O“Ï—Ÿ;¨ëı€Âgû]\"&Ò.Š¡	\\[şåñ—Ñ¯W-¦tù¥¸ÛÑCÑêH?sß÷hg.&İ9?J­?Å|G|IÄæıs©#Šğî3 j›¡±©§-pk…ı%Ñh\\¶¤Tšîb_tiÎ\'ìü?¯äÓ€?8Kd¦õ3›ÎÚÈø¶.8pcùÅZ»÷wÕà À¿+hÊ?Ç\r‚”9×£é™éSª3cé¹ Ë$ö?l“ÿ9Sª\"·ô²>)ã+†RKHºx“àkZ{Y¤h?Kë×’ü•)lÁöÃî*[Iß‰Ç\r× ŸA`Ù\"Qû‹G‘&ÛE6Ç\"wl„\0ğ[¢Ây(¥MûñÅˆw`ºmÓS X?®»>¦sÑÕhß\r]r\"#?«SÛq¿ôj-15d-ªQĞÄâÁ«şšô‡˜.ZòÅ‡Z¿®ƒcëîç¥Md8ã=s>÷¼æFÄQàÕ_z	Bò¡ï…“kŞ/Wäi„&iÁ€–ï‚eG£B?•~Œ	\"Ğ Öl=\Z¬\"ÃJEb $LxdX‰±?â‡IM5š÷ûL–?îöÏÚøÄ__?øáæºR®Ç¿h¦v¢ğ„C7@Êµ¡ûùG.dc?\rú,²~?«“¿•ğš•íÁ\0R›âŒÿychùÓ;¡I@-/¥ßJ¬T/öí÷ÉéGoJ¬Ÿ¥iŒVÜ?…öë$Øv›+$S&Aè¢‚WåbG²méáV!nt]åôª½\'÷Aj}î˜)o›-N?võm#jŞ/d ¢/ó?jgk‹6×!a°¡;œ×òåºTö‘ÔÆW¿D,?Ñ´ì,êvúËNÓMµ¾—bÀíÀK?KíßPëräT*“²şç\rî‘1Î8òN!.Ù0[ê	X¬^ãº)©t¶‚Á@\Z(¦íÒÓóç$û©ü??3ıEÌÂ‹Gµ/Ù—Öš¼Ê¤YğÑ¥	£¾¨½\Z†ÄBGaÜ1zí wkÁ¼g?çËg›ö™=«è.7å£§èBeœ†»ueƒ2˜uGQkË8Ÿ0€YV8¿}ØÁMñ»àóÇÁ›é¦iÀgL0‰«uŒâL&ˆÃw18vî´ú“ºòíO<Mß““	€iæT‰ŸY¾OöSÛFü‡Â5*åî¶1PNå]}@–ªyµf^Î7zî·ÊÑ;Eá0hV=lè`qã-‚P‡\\š¶ü™|e-\nî?ô\"Ù¹ğ`ùbïòBhsG€”×)¦É=_î?j¢Nüéÿø‹ÓXÓ½1\Z\'¿Dö¹åîÿ‹4…÷%‰….²?©ò?=oìB©.é@¹øŒ!VÛl·ñuJjdI¬ş«6ÊæàŸR,kMú\Z?‘µFŸ¾†Ì>ÙÑäI&-«ÿ´Z?˜Ù\'í2:°SZ“!£Šø‚K<ÇCÕ?ûE%e?¿;>áâéüpädgw´ØÎ§İZ[p~£ıçñZöâò:ÁQŸÑ\0j\0œ\'üÉô^Dœ×äG[ˆhÜáq¿h™\\UÀÃ;-°mõß¬mZtÛÆïê\ZQıù”„š2†%ù—¯m	­âÄ\ZcQv?5\'?\\‹kuº}xIL„ôi\n–B#ußÊ×êñ˜ÉñÆ‘D¢ˆ6PœY}§‚ƒ7|™?H¦Ÿ‡ò’|É“(ˆÉ·ù¢ğN?>R¶?…ıÀh§Ûz_{õÍªğûŞÑ¯ñ-â-æ4fÔUµGNŠRñ›/¼\nÿ|¯ú¤û«GmÚZ)¢ï?ì·	pÏ¦‹àu\"„3®ç\"ñXÈ~?hÁæA\'&†?WÜæˆ;eñ-\nÈòEpoiê2d™o=-¸RJ‚–&c{åÜÂ©¿›Kğbü]Ì»—4ºêØ?*Gë’»~Î–?Í?>Ğ7ê @4\'Ş¸½?ÅÕè¦\0ëŸÆ59H¿Op¿­¾®9´pYŸH>’=_¦²å¼;PFw}SW|NWOm>VúÇ{RkÀpŸS†L¢Y²Û>Db¸Y<\\ò±²\"¬FáwÕÄ;­mGªªWCİÖbë$Ívk†6?[õèÕûœŞKp,¢’\n|PÏèÄ½ÚÅ\"?¶Üö ®vó½?š;˜^¥zKÅ²HHß‚cô	ê$úa}ßE.£ÌAğìûX)ëH\"}?xÑ¼»gäĞivùULıç×ü?¡	-¨O\\ê>w Şõ!8Š\"¤M/GyçÿiïK\nD-)†\ZüI?$(á¦BD?/Qgè‡¬7¸NøÅà,•·F³+£a§q¸ı¸Ó†oß?ub±4Zæ¡?éiuˆ\ZµQù¥ÎäOåĞôÖ„+º$Ü/ÁãúùÎ…È1lA¸N«³Æ9‚Ü~`RÁFš»yÀÌ½Ø!(´¦e¼ê­;¸ãÙ²?/XÆ>›I´Jº¹ä¥J(Uíà‘½a’X’—ã+œd„ÄW/<6òa9ï¹Ğİ+h^,‰LÚzƒÚµèbşé$mƒn.ìëÇ<ÌGáš0¢¨¹ê§[š÷¬}%„Ùà¹\Zn¬ç~A¯ìª)‘÷5,RøH=ØÙ1”’š™ÖEÆ^´I„,<ùN³Š„á°Íò˜@î#Ì6—e5ˆ¯ 4ìÈåçIªlóÔ™ÈQr?ë®ş{Öê»¨Î‡kğVZM¾evÈTÊÙº>Ò… ÛKë¤\0èV}ÿ-1l.şÇŞs`}¬ó­À˜îo©\r³Éœ3ØvŒÿ¨ïmGÖ|ÂÄÇx6\nÕ¼Cÿ?ëŠr(?yö~¬¤yÙŒHeµşíı¥n%ŞªÅğ»$ˆ bÁ•ß@UKÓYWœCõÍÿ¦%ˆgû»ú?TGú«.ôNr5b?«Ôø‘!ŒÏMÁC?c\'Bºv?V\'³	ˆÀ¦Z‚ÛQÏˆ±¯Ş­{\rR]Ç£L›\r\n¯íBÙ’áº\".ó{OĞ¿Ô@d‰€[p×E3ëÚFàÖĞuu#<¡z-¾uBlg Ô#ùòíI\nt¨%À<&cuuoYÛÌC /.Z?í—˜şõ¡\Z$«ÍMAë]ç@Á\ZŸØı5q“ÒŞU£§ÙXÏA²„ ]º–0˜®{’\ZgÈ ÇÅ¨ío?R9?¯9bãëMCüóxc©~§œ¼q\ZƒjùØ¬ET?øCx;6ËĞçw“BkF5÷˜02ø÷…õØi°˜j_w\r)‡}²)x#<ò¡¸ƒØºÍó[×n•œ·7¸§Ä÷#±˜bğVÃ\nÁºÑÁåËCQìÔf2QR£Å®É³_l]«‰W’„ı£œ¸KlÑóOTCáF^´£•¡g-7GTÍ‚È˜Ã[Ünİm)NÔô¤6qÄB\ZòCOYák?„ì¬KÊ€£‹ƒÀtruò?íûS.œ+;¤³›—ş\\2K+“?tÈ·CkQşß€×8ß…—	«¾T|èÓ)ÒîDÈf\0Ñ	mOó{M™2«SÖĞù=G÷öpŸ?í¸v©“²%¹ÚÁáo ?¥ñ@}ÀçÚªe´³š51xj3½`¼îÈê½ $¦õiÎEì‹Ò$„ÚcU§°É&<ªV#‘â¡ß¨É?.%awRLùP/×c´\'6d?İDú`éb¾aÀjS¾Yùì?£sÚ¼å‡Jì3İg)?\r‹]K&SÚé‘!ÂåÏzÇç§C¶”²˜¹¼dD¯ª„ªƒÏî¶ü~øû$¯®X\r}spşq!ôn‰Ã[4ô\\`DëV?ólëöâUÚC…^_Q®d²-‡¸Yl÷Ó1‹Ì°·¤’íŠªF]‡¢èÛ¨\0ëA-\\w³5fSí•r$™hãò­\"?ïÁYÉø}ƒh³35‚s¸+A\nSvğ6?°-4RqİõóB¹¬${ítŸš.Àô_‰Y…¥ø€åö¢¸ä{ÇÑ7šÑµ$úZnP?¦ÄÅs:É¥¸­_ô–‰Ú`§õÅ@Hıgî\Z·9õ‘_¼	åé± €5²WgêNÒ^÷ö³¸0êdCxïkÂfàCº5•é-SFoM¾ÇŒİÊi!¶rÒçÔe‡<Ü¥a)Ë\rX+ø`×zà¹É»ä???†É…	i!üUÇÁò¢—?°R÷°ËIïÚ6nŞtŒ4ôcLĞ…ÉÇ$ òÏr~µ¿T¬c°Ñí³8´·Áù*ò^k:ó†:ä¥Æ‰’		ùS†vò¢E Ïç‘ş/glé/\rŞÇÎ™í€š\nMš<òW&bFRgÈƒİ_\"?ÙxÊ¾Šåû°´×ìÓ°mk0¶w3+Œ;U~ë@JkğD²•’¸Š¢Âts¼$ZÜŞ¸\rù…¢¡úElkcÍûÚ•SÈ”=d@;î–1?ğ5eE%ã±¥il3Dş¶Ór‚öQ;/Ã4?§Z?¹>|¡\\¡Àjy2?!•‹İNFwƒ¢j´¾SO]®èçU|øq8õÔ?ïıwÈvÙÅÈc+&×4Cs1fàéï¤¾\ZU?Á_BöÖ@ÖtüTf…\"\'Ş¸¨cÈ|‹ÉŠÏ/\0j£ûğæP£Ø0{++Í}•)\nÔ*_!Â\rô»çl>N;Ï…öL’å¤}š?PšQ<	Õo¼ÁÄWÓ*”ˆ=\r×È…†^”Ê¼D1ŠNF¬z€Õv¾.HæTğÖ¨5Ör‹pš6>àñ«Š^Ç¦£ãCÍjnµ*bi=’ŸÅC¡åB^ìŠ†u5ñ?ùUvUe=\Za®–óì¡g¬/Š1ğ<bÚÚƒÅK…\0e7„#ÈóMÓAß0©3˜Çñı¾‰ØıL€TÁŒÏbs¢Ls7Œç%Îc9Ó ®|†i˜‰›€,àûÆT“( í ¥½KÛÄ}G1-6¤kÔ\'¿bi`Ú´bÎù=Ñ®Ïb£¾8UéDrÌ8Â«g¾/_È÷*N¶ëåB+•O\Z3z\\\r¬P“\'¦0)?P\n…>\'¡7Eív£“„Im‰ĞRÍéï\r‚\Zy™€?•÷IÄ.¶NÍ†Ë?«tP\n‚?Ğ„Á{úæ29j“Fw}ŒDöS‡«¦²Ö…xY”&]7ÜÌéC`ŠÄILâüi]êR3ú²æCš(×AºUêDÄéé1MÆÔ—Ú³~§ÍÕŞ¯y¨@ÓÇäŒ:^-S¡ªÈ×¸ô¡—ùúL!ù?ªê~êtN>ƒ!ÒŞ†çÙ5pçO>åã T¤ƒ ŞÂŸÔàÈ­í?kqRµ·áè/»?™¼aQgà¶fÖ©í±Ù{d÷\rí$Z $xHi&§‹\nKÂÍËq›ÔFdÚhÚ¹äM=B;?’àÁÖAœJI™ÒEôÛŞü³’Ï1H=¦?ı$Ió±€–£ª$»¾Âà0ĞÅ…Û“c§vCäOóÏ_?Ô»[R¤ÀHq\0lĞ:ş™ÑDVÁšŞNd{[¶ï[Pbo0²Â,ªÎÖ2Šqo5\"î:¾‘¢éVN’?İ>Lñ&&ÑC¶\\eºåöÜX™Ír­“öÉgS+EÔ¬( ğÔGî½‘gƒÈe3ÒWv(\nÿî)àÀü?=¤ãe•o¿mÃâúOqüÍÓ°±{9	“ÔÜŞC°kËw¼‡Ì¾3–Òîu·2¿j5A?ãÆ?¶_|«é—›Š¿¬Í‚b§Öt	:ü\nÊ&ş D\rCK¥?3hÖ¹yP—_w~BäS²éYåL¹«\ZğCÆ0½~¶¬?{›t·ÑŸş²\rÅä4¼Ydã±%´¡.‹K¿?‰í×ùåËR+Z­«+ÕiW†#4\0–\ZLáy}Uß¢¥Ş/pÊgø2!È²Ô?>à?ø€OguÃ?âw¿Â`¾LìWH\'\n/VhÔzdÕ(dW„ÏÜj±I –êU\\ßñÜ+rêë;Ç¾_ °¤m\Z¦7%¶Û6›ˆám=šºşvâ¸ãÔôhã/r×4¬¤j$÷„:qhÊûÆ=o;?İÕ[’N4Ô^œÉÉµ£Ä=„Yé˜ò‰’N´?ñ¿sVD xTO?ÎJd¥©†1püÉ¾«?¢‹ì0ô2ñ>‰¶b\\©…‰ù™†9ñ…\nÉ/ı5%ñŸÄ˜ÎÕwËg\Z¼o\"ÄvğöØ6H\Z¿A÷l±üµ~Še8M¬TğúE§ê<Y2¢P.Ò%;nvcôwë~ª(’²Ööæ³:8ÛnÏ‘’ı«’UQ’{E@½D|¹«?£Åîy¿(ò66±\0!+ßü¹yÔ’ÑeõÑë›üš]ğÀì\'Ü(‰&Yjñ§´ƒÃA¸ıÈÔ¡Ò_‚ó^\"ía5€Ë`f’³g;Œg@ÁßfaØœ˜Õr¯ó#)Ã|jÑƒ¶w-ÙĞeÅ\\vb%ÖN´úªè™dElb)?®ë´2CMìaU–Õ×~($¥škô[¥?cìûG?>³|säÅ„Y AÇİĞá‚”ß¦j×:¦ã ¯>Õ”„²¬¶úßx²³s\"<ú uãªG`o”q4\'Xã²áÄœ\rqôšÃœyssÅ¸œøüR­(Ø»b~\rC|+Š‹?÷C9Àë?½5!QÅñ?¨¡?öÈ‰ÂKá¡ÒÇÏN’huà]ŠoP×„á‡İes¡?GDÈ¯WTéPµYë7Õ°í‡eÚ)×º(\"«„…‰¦p”µ?´ê-ÂŸÖ~¡¤ğ_?ŞA€}Ñ?drdSŞä!8eY”ƒèõ®í9|/×£ØêR¸+ v8·A†®X|:ok3š?öøQ\'<é½ÉĞ“³ÒÄØæøŞ€7í²]º°P4\'³®)Búc³]Â÷ãaÙŸ‹ÏËÂ~—í²?°7Ùn_Q&mîñ±V?Ôƒ+§[æ]Ø?¼­~Äğ¨Ë¥Y“Ïõ/„A,??¼Œ´­?$~ã’—ÛÅR#~­ÅÆxº3Üi^PÖ_8÷%í¹lIÍ›SN^Ô¼Æ yVÅœ…2µİ—‹„ïM¼¼’1+Ã>3fì”.;k*€Ş\Zb¥Ò?älw{‹à«o¾	#L9]äë0\0ñ1g…\r–´XT±B ’2e[™ï1G7?ÂìùKÚÎ…? ğÏ³X-ï»0SÑ¡Çÿ|İoÙ}‡?4º®µğÓÏÔ³n£daTYİ˜ş{„ŒWÈÌ*?YØgz€ı¶‘ˆSĞì\n-·LÇzx9TËglã3†\Z4U÷È–J.¯q<…5P\Z¥ŞqiÜîÓE6|-r¶~s?}ÉÊg‹”¥•ö!”àêzÍ*7`øk:¯h¨†Í¢tŞ§—ÔÏäC,¼T–?k`M:Ì£Ñ©èñI‰\'Ê}|JÑIPÔÒ‘_tæÔ6\nù¼4),İ«ÑS§k”Il’*+>^¢Øz0zŠş*{ÿ¼<Xõ4»Kü–ì·Ôœ_lÖÌa«BÛœôIo48·°oÒ[D ôÎÿäK½2[OÄì“½‡©gÂIÉ? ‘­¥ú:§)ò”ã×wÒ/»É÷ÈÎ‚.ßÄ•ŸpSRˆ\'aµµ¿SÇ¡âHÌNÍEªˆDğl«öaQ?óQ#O	i«c·æ?£+¶à$	páëätİ¾;İJ>AC>Ù/cZ\\¼ï¥¹<Îšª‚›L ñ{&q#k<ôº„-¢ÂˆŠ©1M~’4yQ›xàW’¡İÆ‹ †æ>Â˜º#øÃİn¯ò¡_\rS„åE?ÌUÊ§õjÖã¬÷E\r6Ê£É÷?«­@Û^[Ê ÎUl±uóşnÎäg½Ä=9IsÏ¢:6¾ËHkHÍiä_O³Â£›$3Âqãèg›ƒ^!p?T¾¶ÎŒ™ÊŞÑÄQYğçv}D‘Ãişó?åÔ—\"·™½ÀWo(Ÿ¼Œ?N—\"nÚŸœ®¥şä0?Ç1µxIö<Åmw–gV®7•Äøp~@Í™B…ÿ%¾™Qv»5ÕO‹„NŠ(ìK‰GêÚFyÌ‡>iT?ã(ççó¨İ‘1>\\[ÒªÈ®ö‡âæ8v‘qJ§”ÅÍŒöº8‘óU•?˜H>n¶e¬ÿö‹Óa7{OÊ*Á´²~2ùÕúÇ\'\':Ej•ï¹?ìß=TŠ?0iòWú¦†Êá¦´|^|ËØßcŞÃşz-õŒ#)[!åógÊ{\Zœ†BSŠmQKîk	…¢hImÏ¸~|?œ`úXõO_4ÔÄ»)^n\'óLZ¯m¾\r0¶ØÏúï:Ë¹4Ñ¦³b¼éMDˆÿ5ÏÀW+«ôCç¬İ™ë?ë[1ÜÄõ—ÙœM£Yˆ®íë?²^°Åıó_ğeßmÂ—$²Ï0˜;«Ğ¾—‰bÖVUÛİK¡É‚ŸÕbÕY‹Câ7|6IjìUûF;¸n)Ê8t´Ğ‘Å¤0HtíåPÁ(”L9ö…¢?sîR×?\Z.NËóé:?1ôcp|5>\0¥@\'@i8…ÙìBN]‰sKéÒ…\\l$·Ê•nù-9á±»¢.a·fd´ˆ’?Ã7€â?j“Xyr÷hbéà!¿G;ç\n-ÄñµÙ“‡L?FoêlëôD‚ÉBù†Õƒiàí?Vå>\0?IÌ?§?\nŞ¡Í˜švy5CtòßÌª¢O/Yzî±ê;î@ö¥oloÉo\"Cbn·m+é?N“¦ı™å2\\àÔm©ERTùÖ-«Ü<ËjÂN¸\"D5¢ñ²¾bİ2\\·Á?¸èyÈ†ÅÓ¼$Qèù}ïÖúˆƒ3W	14ÒïÎ–ÏƒÓoáõç	”¨ğÙ×HCd.ô+Ş’&ûhé)Ä	šé?ßŞ=:„dÃ=`?ƒ/7üBx9fa#PNÌ74Ä­ÙŠ¨î–4Õãâç?¼\\Ê£¨ß£?*f^\Z\ZÜuñmÅ?éäILWßt:uª=Ql—aÚL—FÖíqLBµ‘PÎ¼q<•?ÎwöXò‘¸Rw3bĞ)ùĞ‹w8§[ÖfÔû?îo(»¿İŠyî2M]clé_zú†¶ÔŒ0¯18‚·p´Î<™^z\nş,\ZÒ\\ƒâ¶õß£–Õï*3\Z‰iŞ–„“…V)¦`ú‚*Áa,3[\Z½›}¸$P)ÿk»?\r’¶¨í\Z{şáÄ+L/hKĞvÍxëKIê;ï¯YÂÁì™cKäZış,¾ÍòàÍ?–ÇØß»\0°î•~Û’£ÎÃzÌçaŸ~~¥7š¾Âìvõ}Ò»ixO=rŠç:cCHë.0“MÁvrq¦¿’™£ín£ –¯ï¢Ä¢k®SŠDŸ)¶¸Ä\0¸’€G…¸ì;uOüç¸ÕÑv\0–‚¾ã˜?¯©DÙÆØªf üsLÍºEYzU;0Üú®M\r&>6KvãôÒ½E;æÑ[x	rƒCæÿ8v!,2ÊpÛ0êšdË¡ˆ8ì~?ÇöGiTHXPŞÿ?ÂiÇ„Îòw4,’É›ÇF§?¨øö?&q²r²Í4\0 ?¹6g¶Æ—=ŠÅ=xë_¶%§0C\'ºX[¤ä¤»Õxî!ˆ´sa-d¿2Á+?~Oß?s£1ı¯Fˆ=g °M²ó„ßT³M¼œNÒëÌà	›>£˜šöuMÂ¾Ç¹—_¯YÖÊ¶,NŠNõì“\'4“Ÿ“äõÎ?\r¬­½È\n”Tªßë9Z“UÿÉiÒ\Z\0m¨Æ[€¥NT>]u8Uá%Z´*‚¥Ú³5iUXN°õ±vşŸçŠ©Ÿ]³Éî9ãY=È×ÌÈ‘ÕE:ŞÓ‡ìÙéúÁq]Iyôû0æ5….˜Ÿ´äµ]©g\ZÌç€!H”âgm\"“?–w÷jIìÁwgôè8|‹—óÇ7ÔöÂÀpnH5\\5ÏîWnÊš*ûHS(ğŸ«?ÕW>\06÷íĞÏáÊ‘NRbÙW¡@¾g‚Š\naƒ~KcÁ†ÔVYÅŠ†¯°©¢»a²[ïÓõ½²bÀÙ®‘o/ğµıÚEku¢R8ù!Í!Ù*5¾‡:®Q€³Y?®Yòy\'ŸáÍ)To9&û-Ÿ?¯I´%ÆBo°2ÔH|\\›‘Öàä=qAKéÖõZNqçé«>—.c=ö•Q?t+Q¡äNùó?™<Ø?oHBNR+úöbsiÆgı;şØ?»Tò]N\nÄ~9û]ëÇS™ÈÆÓDè†p^^*©“ˆ©…ÛòNRƒèó?uÛÊZ-ÛÄ­sECcW1ÏwÖ/Só+ÿö~Œè•m©| VÂŒ\n#M·¢óz¯îÌàfQ²1­ ‰Ï{€S@•ö@Ìe4±i€U{2à¿¨/½Üøe¹Ç˜¤ Eˆ4æY,?£Ö:¢+)|}9:|gÂË¿#?ù€MäÙ¤ê`?Îù\r¸àc§ÿÒê‰P±—¯¼4ÑÂÏè‹?*Ï›õ‡¡Õ\"za/^+à•^í†Ä?_dw­¨K¾n¾Ä¿U‚*7	îùçdhÒõ±¾œJ\r[ÅÄe”cõ7ÊÙOó¨tÆ†2P*Ò¾	û%¤Î¼{mÄYêÙ5Üƒà`‚å=G!¨?ÄØ¨í+èåÕØÇÙò/w° b§LíÛ¦#ŠøTøir÷ĞùÑşÍI‚D{ãB5ƒùÓ<ßk‚s®€E¤|†è?Öî¯Ó˜øwK58%Ô<\\Ú™\Zf¢t7d`A]Œ?i†4®?á‰kãØ jê:c?©„ñ6jğ‡Í-hÅÙ¶Ú¹8’^é@h9ã\0JgÆ­÷³İ,ó³÷çó¦»¬ä¯ßÊnSŒkÙgyœÂcfù–`a4Âğ¦·×¥5ÕİÑ@rKD_iwZœ{£6óêmudH&çTË	ª§|[fÎçíİõÜ¹ï+?¶¹UÑ`!Õ5XÃX\0k¸±ˆÒrU¯ö…1ûË%ş^?lå½÷¼­ñ%şô÷i\\çÁµ2vu÷Î{…xÜzyd¦3Ë^îÌaÔÁ}Ê}Ğ?(Ï+k²öl)lĞ‰Âôğ¸gòLªK×”|ù…ÂÁ»¢j×yÓ¬·ã?pY‘AyÎ–½iÕÑàƒû\'<z?1&¨N¬ÿÀ9«4´±p§ªC’ÏÌM[œ-~røè\"“÷rz4şg9yÕÌ+çh68áå6šÅL ‚–Ø!›ÒÒöÏı’iö©??ô…ægæ/>®y[§Vy6ãrİe#GZy¹³‹ö7ÿLsÈùº×’Ú½4ä<8úr1FÑZwUƒÌ	À)î0ä½Û\ng£ÍtÑßòï‡\'#XÛÑ>!\"6­WW[\\??½\0­ö®;í\0.4Ì*rİ,9ßoÒÂ¾OAĞ\r~Æ¹Ï\"H?Pbtq^ø©{‚­=»™´L$íùØbŸ?Ò­\\ØtEBìµ©z3¶ÊıÔ|XÑšÖç?u\Zr˜Ó7 jºf|m$hÛ(¦Mj~;®ºR”}@S¾‰Nå2?qç©(EcÆ\nÂÚ·nTX/_u¥5Êm[üQÃ¢9~óaÇ‘‘~¬N\\ÿx»™h?†Ä©±L”ß×ç°*Òõñ\"	œPÆ¿¹Ÿ`åJ¾iÆ‘ÿ #7ÔYaKspa†—ñ5‡B€à¯SşÂ´`˜\nâ‘Q#å¶ñ*İiøœBK(¾i„6N@—èu˜*wUvù’æ\Z<[åÏtaQø^NÆ¾î¦­RÖ\\VPn0XàÉ?ÛAõÃ¤,A¡=ÙœÂ5ïlH0íİÍ¸k!Ã¶ãa•p¿ ‚^ú?Pp…¤¹äº©@01ÏH«:²ò5bÚ\\ÎÑj½\Z–N<Jø--2:ÇnÓÌĞÒó¶\'	üNü/›={Õû¥O¿lî)¬’d\'’_¼f°ˆŸ·bGZÜ+ŒÃÿ¶wWfG \ZäY²Ï³mqöCb“?©ıæ§\r\\9p?‰Ò‘wÃ¬f\r7^x\0?tâğb4åÇ°µ\'ò­šÏtÁÎíÖ úò›Cq¿„!»íy:¯üü`æs?è‚}ğûŸß¡qÃ{3nºÍ×9]’Qva+Ší»Q<ø\rI×„pv¥\r|,às.\ZçËÖÓ´¿Pø³E\".áËğ´‰,qyFŒDêÓ?ÏşJEÍƒ\'OÕĞ2‰.`ı‚dƒÆh9Ù§¨ ˜/`š¡Où8£ä9Ù­Xñ}¤Yœ½ª\n‡¦G+ƒ|KÂ?ÅYÅ‡‘s72MWÇÚ·”Š½?Óôj»ÛTá–¬?ÄRäBï&\nPYYäÛ?üq*S4¤çŸ ô%\\GÒò©Ï‹ØßÍá³Ã%ÉV8ŸÚl?bq‘·ê©¾¥W!O(øhšğÓŠ&‚@;®bKÂ\nßì—CL([‰ç)¿$t³%ëÓò+†,gPâ9ÅËdÊÊ*yÜú¿‘¡Q8•¯9Şgîåó«m™Ô(ğóØëz2:cÚäÛô?íW€¨lß·¶\ZéåW¿Ê…Ş¿p Vş<3ùãÛ¢^ùÓ´¼°fé[ûF?Uã?ÜôÃ£¬Ë…W~&k2?¥–VéŞï§ÿ*ùxÖôÅ§’ÒŸ‰ä\ZgÅéÁûÿ%ÿßÿ&ÿ·›½·£ıÿ§Y¿ÿGù¿Îÿ(%&ú÷äÿšÿQ\\JBìÿÉÿøGùÍÿ?-\0`•ş“ïÃòNÅß\nøŸüßoŞlCŒç·ü!?í7oşM¾8b^¿Ş\Z†\0\0\Z\Z£cc?ÓS?ÿ–&|ëõëQ\r\r;¿=5øÑ)\0ÀøoùÂOòbÂWU¿l\"g®‘µ¦¹FaZºè>xR•¼g…î?ş0\"¦íã\0„+2­R]¹‡Àœä?Á`®ÉÉ~ÆBl#­2ùÎòŸ| ²l³`®}´¿|9RmòÖƒ§äŞm#÷8¥o†àŠªŞ§IûÙI;+lS?ÿo	NeæºµW>3ù)«‹(Ü{¹0¸#‹#~VõN¾<à§zzç¯|ñOTÑÈ_/K?Ê©5–ŒÌ¡ÑbšÕğª4\\Q8;,^\'?S?’±Õï	™ÑLu8šElfıÓ¦’¸¥ZV¶_UÑ_u#şBz>ÃinGYŒµÉ-7ÆÏÙf;ŞÈÒ(l‡eáIx{4C4¼«ş¶ 9èÃ?İ£•?0\0g.Ñ›Ò¸Öšue›]a#ËöÕ™¨äŞ7?-©/ø?}roàãñ\rÊßO qb9ßáà-Ô/ÆÕ×£Ù0™]ù0¡iBóÕQ	îQ²‡‰ˆd2k“«o}ŠA)h?Mn:êNH3M4èÛQ¿ñÁ¯áh¤Ê‘hı!É}ÒùyÃë|ª6íÃMP®X0î`İYU5ÁØy‘ıW×ÙäÔÄ£/ŸóVêš›X…%l`È­«¢×»A$¥›ğíãõ–È×´Íñ`-?i¡pm>µëV)7½WœxÎÛ?2 8ó?¥2únFn†£ÙjŸ°SûVk\Z“Í!ZP²Ï²?4k9À3äcöÊ-\'«®½Ã\'õY?\"/EûóÕ¹Ú:Işíçù8¥>û? ş\"hpøW2³–6=csÀ÷ÙX5çª<Uôokùd3ß^öâXÂîiW€?¡£Fá?ğ®²ÙÂ½‹Â™Jvsw¡¸UT#s{ÉôCyÅR³…ô²¤âËò?b-…¸\"YA?Ç¬+ßSw®Ér}œkrûzºçFZ}?»>NöÒfrş8ØfÈ?ø4Oç×`nuËV˜ûT¨Êñ?sÛV€Şö«œnŞÙL\\eç¸Àr³ç‘ ~Ãñ_Ë0Wv·)?c±Ã\nu¹4enm„J«®vâ}§><Ö!ã\\[UCÊì9èşİMRß½4©&v&Î,È2ç©ƒ4lm/Æm-’ób¢Ù?Ï%}66¸mì©_:‹D£R¢ÖÍ„c2£V¯âÄw©§®Íp3³›´xU8&,û\0x?cHùHH‰1© $Ñ/?©âEhv××A(½Dš“Äìû`ƒ‚§Ÿ¢›yµ„l³–‰øn“j	•úÊ–Eír‹/wV´\'šO(ûëœ¿Ö¾]66(ñ½ù;®²“_s‚ŸŸ¬\\L`¯æşlPVå{|ğ!í˜aàO\\Sk±QÅííìğe…³µ8‡‹sxí’Ú#vøûtg?s`itĞË}?:øl`şpN>MÎ¿Œ?³c¶†œ]4ŞûÉ²ŞGfhËlHí$Ìè…eŸwíl+G]ÿ½„x¹’\'KcMü|ÿö»İó›@å\'ŠdÙ/xx\'ë¤Ë{Ôš«zúç=•¢äş“#®~UŸÙ_¶ÔÈ%ÂvõæåcêÚ´Ô’?ëÍüÎOÊ{h‘ıºO±¢şÎ4[ÅK´á1q{­„\r§Ë²7ç>¾ Q	ıy…}&×O9³…9N,ÍÒ&½7öğ$NùÇ\\¾Àu¶Øf?¸uÍ”5ÈØÜz\Z7ågÿ\Zô?%r‚¥~ğSQ(a¤äk‰Yñ2îÕ’fĞŸƒu‹çgâù-_o¼st6ó€m¹{Tç§§ùs¯ôp˜óTÍ¼sÍ{B¸”\rsEK}Â²ïö ²OQ=³åqı?\n¢ì\'\"j³üŠI<´¼D\"¿Ú–’ôœÈÏªg\ni—“ñ¿øÒ¼NéB ÿ/=ç5ì#Ë\"±şv 3¯7CÔ‘Ï8ûÒúIYH›H•icôÑr ­äˆ³üU­ÓÇy¼QÀÌì”H7Em¸ƒ¨EGa4æê×Ÿ6\r‘ıw,Í¬µ#{ğôß8)…Ë—·Ò&ès~ıªÈ%X»òÔè%ñÍœxÖò?Dä,®\"Í TÒ—PÎ´ÿ‹˜\'?ÊÈnòV;ÃÎèy9DR¢¡Kå³‘•¨d6ó³NIÔô$ÍØoÖrlEKgSE‡\\.õCÕŸog¼gı$<û›	‹‚à³€b­Dq^ƒ½”’ÁÇªz†‘áŒQ_êfÃ<u&¾”æòìO¯À~°H®±s¬?ìfÇae®9ó?¥³™äŸ¢´øìY‹\n;87Uö¥òišs¿?,Ë?nø;›kˆWßOÆ¥är–È†È%+\"‹÷/Õœ%¥^äß<½1¬äSQtJx–Î(ó~ıú•ğ2Nñ†S·õ/?Cò>‘ºg‡T?m•÷|úpå|æî`òÕA¤lŞ=m²¤>¸í\Zè8+‚WN{’m3Ã.ñœ:XÕE]!èIçIkùÕ!‡„õT8O5Æñ+ş¤%ÜXŞOi~vZÜYBNß“xíhÿ[k=¹?áZhCÍóšì%QÉöqvÁâLœHh†‡Q@?óôwÛ²<&:ÆIù¦hªœÎ C÷V^‹ôÜ/Şa?\ZÑJêşŒÕ¹ş’F-Øq{J³ùqïÜœœÏİß¢)CX7ú Ğ!‚‘Ó½ß!?†ÈYŠ;ËCĞ¨İMh\rÊ\rŞd+\0B0Şªâ¡1W·CjÏÙBmæùøö˜åÏ­å$È÷ÛaE–Yøç+D¤P¼œõqõÂ½n=Ï‹9=äkÀ*ÉÄKçÒxÎ\0&Sm¶/›Ú!“J!sş6¨?¼‰µãP öoÔk×®$ÃuhÄ$²!c¶<2@PüFg\'SŞ5…QØ­¹R™È?ö¢r ÁÍ}YésÓT^òÑ,\n%¿ùÔ9„Q\"æÇÓ0šò€Â?£ıç„\"ükTsÑüïfåPÔ³²¿?4n6rlï>ùf{·{7­O“«LÃRör\r2„#ÇÂ³eŠZUÈÆ¿$ãÌd>fŒ™ĞŸ?ÙnÃ£”Ì]<,«?ãáó¤? øzæ—.¨«ß·Á\'.!¡.·Jö×9×xZ‡hl‰“a\'u“úıoQ`Ù$L6©V1ÁõZvöc#üGƒCbñî{#/&ŠŠò]g-Ë¢sYHİYëa³ÃŒ™oå×øD¡Ô«%‰W2<k™Ù†üövpû.ŠˆsK(Î3\'w2HXÚèË}Ãù¥/ÖÃñ@œ÷?kÛ³€vóa“^Ÿ~ŸZèG>ä4Ò²TåNÚzÃe;ÊÖ··ùûïêAÿ%šªåpÊ³çî,iú¡/®–¤¦İr¢§ã–“àHëØºQšP™!ù>“´Òä¨3,áÜ…¡v\r¡ğ#}WóëyíÃtXß‡©+ÊĞlá	ë·ÜÓ¯ƒgO)Lf‡ÉeGq†’KŞn¯Iäø7ÀŸÍZèÃïËÂD‹?“V_AĞşF¤÷´-F~|{óai\0Ws:-â©ä‚\nñ`05\\¼?Üv½ˆ³hÆeûøü“–¯Ñí¿?aLq.‡?¿ş¸\\Ÿí˜­~št1TQÆk(öÕ«ÑÇY+íİ¾hü‚0ıÅçóWÎ’ªÚ?+D­åEıi©ñ…\'\'›cs‰&˜¿×oiÏ‚1N§sz´’Ïˆ?EÅG£/k°õ—¯”º¨ó]jÏ—I4Ñ\'ß7Ïˆ\0ûí?.Çä4{)Ù¼ì>¼3¤­JÄ9Ú-=Ö‚«A¼³®õg\'ªÂGİ\nÆÆÓjûÂIÚIEÑÍ‚ÎÄÎ±Æ»“1i18\nÌ-û8ÿd]]›JÅ[ÕK@B—^<©?h ]øÕa?¶ß“¸öùh;àİ¢ë¨µgú1;‹ûD98\0ßé4C‚8ºĞ‹@BXDâSDHéaş‹QC?1¸pitó™Æ„aÓŒ]yÖËt|a	’û´;mïš€Ÿ{óV\Z	Î¼ğ‹(¦|\Z²`¤Ğ¬¬ªÚ÷¢gşfÈh\Zf<­QĞ×½·;²ùŒ¦É]÷ê³Z®]ò…âz0©o?ã5¹ú?jqj•³\ré#tÿÚ/GQløv³2DÌİ%³(™m!n§EãÑ‹f¾—–àº{?F?dCÎëµøÓK	™EKkæZıZ`Ó4”A³;³’fO­cÌ–r[ñµX¤xbús)}Õ?æÔlB·u¿ì…åŸ}flÑpÖNøÎ|²¶Ö#G±ç?üÒ¦ŞBæ\\­ÍE;cB}êp¼«éèÓıº7ôåkä×âAôçØ&F+aÛW÷‰£õûª]şÌÆ{¾ñ½ë,­•ãY\rlÌßCOyéÏÃ¨q)ËÊüïFÕÆé*İmß6ç¥zÕ¹AöÎ¯^#¦V7ß¯Ã¨tzs†³+TØ8]*Î	Íõ}~	i›Æ¾¶ı‹ûµf“IUJíë3R‡ÛŞ/ªËMè#Ş[§K(Ÿ“¯XHZ5óA‚…ÑßRXôŞ0W¬*\'<şÂ-Qï’4ÎËÊ†÷L?æ7ß¿³H·Öj„;qÍ§¤È!vC‚)Ü£š®û?Ó\\ÈSÿºŸã‰{ê^—6Wæf_£¾ì‚²«lÑá­#‹$2Z?Õ~äÔ»tï?ü]OTĞZ6#sVàCçÂ!ÁgÌwzkÅƒ,?øP‘K?•í\'ÊË?\'¯õy_‘wöL9ÿ¸kbYÿ w€Ïáp4jÎÂöW^È\0~_E–?ªøcKÍ>Aİ©9s= ¨:?”ø³?Ã‘š]G[Z:HD£)àK?~*!U³-„#r°ÈÓœêú¢¯ü¡—J´?Á–ƒs ¦ZW³_²wÇá‚ŞÂGã:ó<½¾92ÉU\0geC¹Şí}™pÖ´K~áSvblˆSN;Êo8ƒ¹6ˆÉd‘\\ÀfIß<‡Èo¨:æ-w?Òù,oáTnÆO_ÜL(´ß›‡K0?fÛ0:?z¿+L)Q¹S§Şá2û¾‰Í¸öErÍÚVZ÷^#!á£»•<TŠSØW+Ë¬wğ;ÈŞjVOa¤Ûä\"+1Ô?KªsAk0^)½¯~…]ëWy2ÈáÒßn±zXş¡!™F¢¾ÊK×¯ìCÎ¥]K®²ò“Jã\'4­­GÕ?bŠÊ?—\rà4Ie<‹Cİ{œåßNgİ’†]§Ãk]ÒW^E{-LInuyÍ„ûö~L›§y?fÁ?w4ş,Ñ“ıÜ½°¼À¨ÂVmÖuıñ¥çf~å,¡Øß¡ışÎŸ¬)YbqKUy44ÆXu™pïkQ2%¶WS—“•\r[¥ ĞËj³z…·¤7-¯hØS‹ç¼Ö¸/§\0ÿ#?» 5q³_={µ»ì5ıÿ`*}@ï«Ã1ÛèÉÜÊ=•JWÅ¼0¿@!µ1İê-gê‡ìÃù…¢½»…ı~‹öj²o\'Ó>’üFÑTÍñ°÷`M.MÃ\0¬¢( \"Ò¤\Z‘^¥„Jï½÷–=¡‚Ho@zG©‚‚€¨ôĞ”\"R¤ˆ ¢\"ş¹@PÏyÎó¾Ï÷~ßÿÿï}?ƒÉŞ»³³3³³3³³{\n0qaÖH	ëxU?IËI?·‹jÄ<ÇSB2cgß@;)‡¢·DÑyoÓ‹Œ¥ãík”:µ×î¼J>+“Ğ9;tNÿã¦êl`ù¾lñ€#‘ÇsˆUw¾â†x-ïë¢ù?Î»\n?Ş«%’±¨\'üğMÎÜ2G6åN—:\"´V¡§CœøUäM¢a^fpËªÀ™têt’5á’o…?¤xäŞJ±:k\'5¯ŞÔû\Z[Ç²t±v¾\0ÍlZ)¿^?\0æ;ñQ©ö)Ë+|¤,?”Ls•s{ŠÊb¶»Oc¼>°İğ~‚Ó›¶3§S“§Ò%ÿÈ½-|o…híóldû‘P–(ßèå¯Š?v-³iIGW2‘—\Zm\'R‡k”}˜Áºå\\ŒFZµ“_í¾ÙŞ`Ù d»rê1u2ÑåÈÖ÷Bğ¾G©#)¨Zñ+90³k…¢ƒJ?Moî~YÌpeEß_¬	—3W¥\0gÇR¾@÷Ğ¡_ğ\n‚uêuÚ»WšÜõ({¶@É2çùƒÔC5ÕO@Ôoé±\"|®³-ËÂ/ÿÉgSôæÇ¡?R‡Ñh{ã…ÍDSı•Tú§8ÓhÑOé¯»ŠèÔ·ãGû{ÅŞ^âĞßíÑ÷VúR¦nè²Ú©€>­Ë‚àÊof_U_r‹¥™Æu/+ˆæ?¡ğã7ä4w–¦ºÖh´hÔ‰?l/§¿ö}é°œ\'Xt›µË•,tXöIú€?>·Õ¤…|Çêï¶&yú|ÀæPµf†”:|KôR\"Zi’¬jJÎ=tùnÉwÚãKÍa¯øuÛ\\%í¤y«¿¹~Gë¾“¥aÜ¡”cŒhêw—¢é~ŒæO?m	f£Ïdğ?´uLmÙ<wô2ı=:Ä,Q*^Ú?ÍV¯.Ù§y®ÌMğ?\n=ÿŒDîó¼T]\r/ó\'en7Ñ¶Ê‰p*gÅI:Õ÷—4,€Xğê~ÄÓ0¾K§7[j]@]¾\0&e‚ûÄ÷\\ÖÙøÔ\ršÎĞüÁ&4±µ¦îS\\ø¸Â’¶5§„ócÏëåâkæròSï©%Ú‘Çumô?×éØ§¥¾¨NçR ä‰Î8¦êFÿxZÜ´u/táâ¨šÍ úÅûî«‘:-,\'´t‰RrjhÁ?\Zßš-êÆÎd”…A??Ê;¿|Š4+NE|ğâsÇ˜‡ÓhâXˆhğ©©É.8_\nœŠÎõ´T¤ú:RT~Y^qãkô¡V´–Î‰÷œx ª>ºª›Åbz6EïÆíˆ>ªrÕäKfã×Ù¶›^×/x<®]X¸Ûçé×°‚‹Ó#E¾!ö>ü[BŠVÊbÅ?ïu«†gDÂÒ“xOŸ?HÎ»Gzí	197!((—o#­ÔÚ¿­l?ºA¥“‘$˜\\á}Fï(¥Êœ•–Â¿¤ïuÔç‹=„âÛµ6Šr-6?d2{l\"Q·‹´/|§“;:}J-z/|ûº‡É‚SüAd1İ\'ùsëÇ«g3új”n&>Œş?Ìâ.:føã‡ òóoº3Û`}ËçÚİâšÓRº?‚Ms(nw¯V¼fßtêüFõä„ÏøÃ‹e|¯’ôIJØ&C›¯%AQ«7ß|àå<§ÕB¢ffØ¿ÉY¿98üFÂ?@\'x.<O\0»Ah<õL?bÌı$ùcÖÂy\'¿ô{gúv¨V¾2ô=³?bâf}\ZÔá7â¬¦Ü½ßòrM!Ş †ù^1ÿå;æÂ“×ƒÀ_ğ.¾ÌYÖCsµ>á—8ÍäSÑs,â3$LëÆìaRQ^0%	Û]1?)éØû\"ÒŠŒëÎùÑa…‹ï‹ÜÉd6×+i^5¹8¤,b/ÔKM!®¢­}óĞMi}‚•åì›diCãM	NÚæâÏ@¶‘±E¼Ôküƒ™‰¶oWÒhŒ‹NPË?©rX‰´y<ÂÙ®n’\0_b˜a8Ó\ZáAœÊ¢+|7„Ééõ?ÔH1é³Ê‰Ä<ïÊKß—¬N?.c“y#1w^vpuı¡\0W8äI3ºÁ½xøLédíà95ŸŠÛZŸ—½9KğY¯?•.§œ\':G¿*N(ÇEe#˜\0¡il¡l=ÆYŒ2ht=5¥ÎÁaE–¿Î’ƒ?•’–9,%Gmwå-ú‘7åİË½[[—’ìgõä»^¤E‹R}P3FŞîx-y{?ÙÎ–¹$S;ÑÉ6·?ÊÍTgEq&O€åœ -$¥·Öd¬ğìÁë \'³òUÙÏé›2­>KË	Ô ¦+YÚz©Zêä;noU}+ßñ&I„OjN]Ã.òŠR¼àÒuwù—¢àÃªªIYµ>uTĞ…÷é[ãæws½ñP•¼öBp2k@Š°UÎ^)‚JöTély€”œ¿]=l±.ìı·óÄEYgûSk]\rÜööœl‹q²¾a£p?”Ù<Ì¸¿<´ø‘Ò¼áëy5±™Øµ÷¯¼çï÷?ùg+Ô¿èPI‰f,–N<.l¸)|¼S:òÇ…K›ş…|ş·ºÏTl¾cædÊÖ+„AEO}lİôhÈT˜|i„h¡ò²£¾s(O\\&ry¾áÁñ¼P#¹Ó‹”Ç2¿3qK•5€-ú/}SøAï‘ÌIÁƒ??\\Í­<M÷ÅDÀêx‰>Çàmrùµúœ@•\")-¸˜¬-úk·´¬ÜÅHıå‘FtLè‹McÏn\n<,‘ñ¢F^gQ¤n?¥3÷_Z’5şb÷ƒ-h\Z/µTaË]ôéŠá0ÿ(?@,M#ÚË¾^N¸ü œĞ“\ZO+>,+ªÔ#çuŸ-Yõ1İRtáæ{j•ğ«6(‡sŸóÏ1dÂÒ£ÑF1ß[`R¹+¬gˆÇ=ÏŸ>¬š,%—Œd“²Ê`¬	y¹¢æ?G[~¸§·âx0\rsÄ–‹í‹¨QtÌGö¬?05üïn~}?Ğø´|—k]â9W‡]Ök‹¿¥d?€Í8*`¯|!_µ³£3-gÌÛ«”,œ¾ğ??\'‰ŸÎpØîb™èÓAû6M!?ÈıBıÑ^x7\'ØjkB…²ãüáX%ÛÄğ‹ÄõQãçÀ³ªüÆkåì¼k›\Zé†`¡x®×£)få]áøÄålŠ4q§E\nì\nÍ²ñã*EâK­xŸÓ§Q?­Ó÷ƒ«ë’èÁOY/?Ú?~ÀÌì@•:¤–òà¶°ú³B\'×ğ¼¼Á\nˆ—Ì”dKC^Ê(â™6ŞŒ)í}ÇæVŠ*±ø¢^|·„è?õ2ğ• ”©¤ØÃè+¼…Ôå?²p¡o-:!>J?JŠ«jêÕMeTàK\n˜\\l&ÎÉ)_Ag­+?e*h$æd²‚ˆL¹Á)Ö¨«~‰Æ^ŸóÜ\'ñ0Ïú2Õ[jÙJŸÂoÇ\'ŸÇ¯.W¥Í1Jû®?›û=£¡Z?ªy<ëå½IåÈÃèmè³¡7PN/X?“úé£2§‰Ms),âŸ|ÛW¢Cë›â0î™+ñğÆé‹çÓª¯©ék´úÇƒçb:XÎÎŠÉ—rÅÈ¤}ˆ3¼q.ET‡µWo)[W„ÌFíBËç†owî?:\nnGQ¸°Î…²´3qšNk;hjdW>áAûŒzã­Üi}¡ú=‹†pO\ne,R?xsğ?éÑg*®dµñ&^Qh˜ıõ)Ív‰(Ûñï\nÇ„Ÿª6oÒi$=\rIR)±¼ê?~ê¿î½á\rEbp®‚8KÎ5ø‰,”6e@5§®³—©>À¹b˜TÍ5ÁäxLŒQV‰*Ìİ4v|Ú»9h÷fe:œÀXvë9«èÙÓHuôIÔ×©Í¨|’ûGš-¿2†DVÎöZ4I`KŠg$¦jîª Å]¡ÇÔqè•¨=³S\0¸v!lğÑhİ±)¹ò)½ˆVù¡:İåÖXšó¦h{f«Vû¬:\"¼ß\Z÷?Ú²!é\\ï#§ÏFL\Zg¼O­Ìa.ï$¥=İx˜*ô®Õ§÷òÄÅD#+¿’{¦td?ÔwM@~áA’A‰‰]ßİ¨ä~FsÇŸ¿•²(åì¬š›cÏ&$ÀÚ‚ª_àCWáÕ¥&›`—Sv&©kıĞTÙçwŒCÊüòèºŠÎ\"znwlìa²q5»ÛøÂ,Ãb¼JV§çşäR \"qLÏE?¹‚”è§œÅøB×‡VÔ—Îˆ´p2.şVvïfè?Q)²¶ïìKjîQ/&+\"ÜÆµ‹w9iİ²şNo\"(ôzŠ×D?W‘XÌ‘K:…¦‚øP®¶›1Ì°*âyû£HÒô¼¯èñôÕ\r©\"R§t\náa|Ò/¡”käfk÷Yƒ\ršCKÂ/~TeÊ_¼ødQõ»ÈÄƒ/4´¬*ˆMÏû\\ü*2R^¹Á2ë\0QıË\n)§F|ˆ4 ‹WiÜo7rÂƒîÍó¯çÊô;<æ”xÉã£tRót²Õis±ò;¯ß¾pà¹d˜6İh–sï	‹}t\"Ù³\nZ¡‚~™’‡\'éó~ÏoôùÃóô¢•†àåÕÏŠÄ”Åka÷F>‹v¤y‘=_»/tŠıñ¬çÓæ²³(3“ç]çĞĞı6ˆæ*ïõ$®O\'ËûÒ.yĞÆêSb`ƒiÚSÏ¡*Q?%ƒôÛÆÇşãÂ`Å×Ylfs°3È•±ÎÃÂ¯./Àe\\ÔRCÎ¨„&ë|F¼¤çåÊ?ç[aÒ‰ĞÜ¨ä„k?íNêãæ]õ?şé¦¡«ú·µµûk£7}n‚j—èu[æ“?B÷ŸÁî?,áÃ?6ä¾IÇÜ\0WgÆæˆœg¸¤E­ÿ…¹|İ©îT¦;Z\Ztiù„¾K…µéƒ¯WÅÕYÎÄ¶hµTE¹«¹ïºx—jeR‹;æ£Ê5ìÒ_Ç\"èHäà/RÂ”™m#Ä%iÓ‚ÂG»­¯Và[„ ¹º?w|ç©¤^=~ÅSû‹eVM¸ÕÂŠÆ ?“ßÕ¤+]îòğÙ#œ¼Ç.êÚ:w,İ»_5ßp‘ku3¢°Ç“uÍ±ë^¸áwAØÛÏqÑ|¹Îë•Ox.èÚ=»\'…Yq(Òqj°ÚVıšÀÁººÄç™ãé<#!V¿?^>´Q¾±Ù8	úÄ÷–™,N]uOyú¬?š<ş4¶À-Ébè\r³?D«T@mvl%•½i¾ò2”ŞôºûËwÅÖ_ä˜Á‘ŸÏ\n_?7o·°¦d„Êß±)ua]û0ßªm*íªcèm‘ƒP¾ğ»ìS”&\nÊ+=ÔAø>P·¿×ËGß3(¨/ñŠkÁïEgµD×Ÿî6R•;ßl1j°qëã2§{UÒ“˜xå6¢…;úZÛ/¹ƒ|¡vİ2±·¢ú5íJLTßNÈDnµ,uŠ}QîÒ}¤iöİàİáV—i.‹\ZOybéü£G-ïzkÖ)·ÚÚ\r)©§>–ñ:8¥‡i9ŞzÍ‚ ÑMQB=…¥@Wğ.?zc®£øè9!³èªóùHaÆ/ñ¬“.tê~–f«º|QzÏ#Œ>òå6ìæœ6|	Ÿm\0:Ïä’r.¿ìK×7aO#^´‘*ÂgÇœE¦ojeéÕôp0‚!õŞSy¯Ÿİ?HxG•ï“Ñtíé)|´h¸Û5EÔp¦rnÅ”QĞŠíH†\\¤T›Ñ#™™y¿^ïL´‚?—™°¤eá>>}Õ›ZfeVi`®j•ş÷\n¥ÙM¾%½ôcëËGÖà‰º‘T—&C&–©#Xíô\"?VÑ=m…íTìĞu‡Th#o+Oé}’˜\'µ”‡Í*™-t?¢O/.hËï5RÖ0½9ú^‹ùZ\'Íí!šÎªî¹­&ù³…>¹ùWfY+VŸë\n´tƒÁY¹ÄÌ„EzµÅ«G¿<7™ïáÓX}tï”qô±Ø¸Ó?øÚã¥n­Œ<¨½¤#qÙUÈözÖ4¾\0æ÷5hŠPŸæ2!İyw!(8•pğ+gĞ½Y q3ôÍÃ$õi-ùGo9ÃETsÛ:hDÓæ{5ïG¹ª«E«Ö:Ç9ßEİöŒyå˜:“úÍó»ªUºk¦_¾/\Z6x‘€{É•Eá17]ÔuÕF\Z»ˆ|_Â§_j|?İæÈf|$‘ê]üŞw^ŞM÷Å«¤4ÏŠ¥nMtù»´ÂÕ>–¡O‡PGMNX1l\r¹êL921>ƒ×Û­^\\*?\Z¢×?Òv¥?<s½(“ÊÀÜ	.4¯°à[ÿX¸ _©È¦£ëLä:&ê–RâÍ%½¾\ZHgêñ‹ƒ?«+š•Ö]EEÒT{:ÕW?TâuÒ?EŒ6¹O<’µC>Á¼İÉÉŠÖ’¢]I§a5ûîW+ët½íYîZ0IÌ&ÊDÙC÷Ñ,<î^xäX¡»\\ë¤¼bäÖÙJ¦wŒÙaO?æO‰Ÿ:ú¡]?ü?†É‹Ëƒïè³]HıÂ5yùéÍ¤:„8´Â¼+F¡oM^ÂâáuƒgÏ‡ËCå(¢MS?}j¿¦a<¥ƒOÈtÂNşcIĞ›xK?×)úA\r™:Z„uS„¸ÀÇõ«›h£\\‡ä«oa¬½F‘è­??•™,Vklğæ#×/¦]Rs8ÆTú©}9ßƒîÄİ—><“ÔÓ:·ÙF	=c90„®á¹ƒ–¥Kš<¾?*KGò%Ón5*uŞÍ`_xüÀ½y6PşéCîÇÍ*Æ£ê™°7GI?®Ö0+?ómÚ2‘ërQ-—IÀ&«~iS¤ö›Çµ,ú]O]fHÓ][r•‡Ì|‰<™®ƒ$E¡û—Ö;œ!—?ÿÔnÄ¨dxc´/.x5Ä­+WNêíd‘ºû:}sìôJ?÷zV”Í{˜œ_İõ°–[çİÂ/R??M½Nn~/.m;\\s¶X\0^¦G3a?&j8ğöù‹Ñ?êòü5Z??\n¶œ«%¸YGVB2G’‚s²«#Ğ…sïÌ¦*XÊ=ˆë)›Ù˜,L¹7|¡Iúöh6K¤ÚtšD\nJìÄûwaWæ\rß*])¤ø|²v’“úcåíz-?7Õ’§ó{D_N7ÑpéqŞU‹ôŒC=X”2‹Z±Ä_Ğ,Z•™}K÷tSìã@K}\0ˆ}fñ¢\Zqk±KÄÛ¬·P|úçGŒÂÌ\nï?“uj1:?›•Án8s™®!Ï©B|º’¸ÅÎ¾\"ûeáL¿ŸÆà½P\"cBZ;ÊOQó‡7¿‹óWæ³»:e™Ê\\6`NLüºeºhr5iá_¶Õ™ëàëæı¶?aîí¯´‘ğ¡G?ò—6IIZËW¡øÊĞöÙGÀNC\" ?µoÂö…ŸíÙî!kUú?k5©?3Z”Ó»–ŸîËzˆ@‡mVÄ:×ÎÑİŞt\'\n?x;P4rQ!7æ+ª6¬\0üB9Iw¥âNıJ_ú˜„’zVËyfp“æJÆ.oÌëÿşøå—7ÆÊª,	\'’9h3ßÚ\nIÊêz&ÖèóÁ­}ôÀŠs¡ö€N2Ş‰¯ƒ3	E¤ãÁBBòœvK|¶g³a>ßàÆSõ¯-?ª¹H<û2iÂØD°øİ`]¢ñbø^\nÙ*)9gCÂ0:Q\'è±/”è²féGgc²¯R|ã\'ß]¥af)rb\'ùf®eÛF¬µæ(Ê‰şa¡+;cı\"\nçÀ*äâ¢ƒ÷î* q?eX¿„!Å\n*Å;)ÊšÒm¬%;ÛĞëÍKO)£6Q¡µ™8¿ŞÚ\'t2Éİ’KçIú\0é?ïß.Km~?ù&åúå­Ãç&w.YE‘?Q×Gây*®i‹µ¤ğƒ%”ˆz©¢S£y½çâ›ˆàÉfº\ZÇœºVÃÅÊ ­åXóa>7c¡ML£Ä;ê¡ÿÄ—noÍÎ»W®\n¸¯_È™=+÷#L¯õùø	Õœ¨{İçå=Ö«¯Ïnğ–R}¾lxIËN±œ%İµB¦tt¡S‘ÜóÙÇ\'~÷Oq)?Àëm8xÂ•É }Oà²\"l+ùÚ\'Üp0z½”]¯à«#×ô¤ê1PÑf¶…Åg]«œ$ä\rÊ†”Ç¸ƒç%i»(áxğEbçrXIÁdRï!Gø©«§iŸ?K6;’rù£ÁúÕ§èMAtf[AU)ÈĞ7fAĞ ]‘W×ú&?ı…?øÑzCÏ ¾Z¢ĞrÃ?—.¦‡Ùù6?|n?»¡Ø¸µ•lÜEÑ¶×êÕˆoš:›_š\rE²‚Y›jß:WàÑÔò¾³J/d÷KÌ/¦Ï…‘?^ÛÊ˜Ø¢EÏ±>œ8~ä>ÉŞ¢ûTá¢%ÀYÀ2W|±\ngƒf·ñ¤[çh·ö\n6N¢«İ¦#\nÔªz,ÄS)ÎĞÆ­\\A]Qg6½Qai_HI¿¨HOU1K¥Ic¡…ÜD¼æ*d¯@úÙx#¬lt‘¶U¯&&zŸxŞ¿Qg½ïÛ¥ˆÆ‹{øu³Ï|à;W9z4Nh ö¢º#ëgzÏÃîõ\\EÖşFÎ)o×«Ÿ¥¡P´7§É$–îLß$öÅ†û_)^möÚ¸Ÿ5æhÒ1u±?ÔäõbÜPÉ7ãÌ¢l‚ÔËƒù7IˆVˆUôxåVò~í¶´nåÑ|‚bœ«.»ôµş”àò‹‰ËwïLšŠŠ\"V4 Ä`?‡1j=OØ:¢Õ\n9{nº½™­¶¢|ÒÀ¿_wÛh,š Â^ãyİ•¬#p7¯§#µ¾\'ŸÃ—¨ê?Â-DF§iÊıƒQÁ?[\'7Á¬µa¨³yŞ+5Ï%“UG/¶FP³ñ@k¿1æµ6õ/Ìp›˜éÜJ¤GM°†¢~ò\\c0óh¿VÃÌ—švÙ°l‚·Q!®İO:Õá7$(½Ÿm?ËÍúŞ˜Fhç˜·?1ÎË”³kDI¼‘²i&€ê*Àéu:ïÔ•\Z2?f‚Ç˜s}äh\'çªŞšhR_ØäòĞUî%†#¶ØÖCk||ÁE‰bzóØ)ÖşvB‚¢ò¾!ÅvòK½k]“æZçê“\rCè‰O*xÒƒ$Moù®Ç—»×ããVê<•í®?ôÙkèd‘u;_Ñ…;°ú5b-Ê9/‡GM²ˆşN½¥¾]_×à«&ilJ¦ãyëúå–õA.ä‡µÚG[!ëÇ?n_G¥¥«|ò®\'‹4­©±+–„ºCd¸_ƒ×¹JœCò‹ôsªfòğà<2dÅÙ¢+oÚ˜)ú]£ñ„¨wÂ?²®×%Ñ*Í¤¤–ç§^¶Ic|z_‡¬‘?‘áu6‰ğçY…‰óY/hfıø\rÆo£–Òh8H¨ĞÁ°vÃfä)9öZúÁ\'z—V° ÄËŞzGku¯4ZÊÛ¡EìÕÈ\"ï?åÏ±-µ\nu\ZÑ³Ò³?sz\"«ôÍÒkM©¼Íõ™¦1›|Ç Âx§>½i¡»¶FÎK&I½üâTúS}ô[Å÷øJ¿ ô= …/³š~RRı»\\…u¥ÇÀ‰şüò&øÂ„’ú}BG¸UbŞ¸Ï5TFÔıj’Ü4äÕé¶<â¦—Zhx*ÄÅá??×¹Úë/º’Ü U1]Ë¨¼Ñï²‡“î†ö¥òßhZ(œ_inpp¡\\êSiÖ¿¼æhJËà—Eş.K©ó¾£Oë\'×ü¶|Â­µNR´Ù«ÕÅ‰.)€o…A]A*ìë¯™ç¦Ğ¤¸J¸oôWiÈv0Ü¸«ª~ÿîÔ¡³×Aù¦l–¼N÷ôO²ƒkÌRËËk››.VJ–œT$s»\0†Ø`/IzèF2Â0¾ac·qb;«vM™ijsS?›œª½ö¬²úÄ<›àÌ¢‚ô¦o(ï2	]ÀÖêû‰¡î“—^ORÕ®µ“ü~ÛÍ	o€®	&´CZ_ÈÃ]¦+/uñpùU½T²!t_oİ0¼ñ(ı¬¹OA?ºİ2õd¹ª¹údeˆÆ†£®îe¸íV}¶Bşñ(šë?Ñ¼Š=È‹?$œU`p»—bS&ÉÇÏ—]	Q×h²¥ˆ¶_[ µÊãD4+àŸ¶Ñ¹}6’U*ŒuCZO‹¾g-±ìŒØí¦å¢Ovyùãñ®ú_ùOÆ¾|`Â[øåìYÆõ™çÑèÍ®\n…-ÑOî…_V.³\n°öH<E]ì$¤zêìN xĞS÷^WsT?|4ñëÏ*AGCP”¬¨ÓTÃk—–¾€×È+t>`ëOiWV—k?be±¤çºĞ|¦ÆC¡ó´ö£È›—8y%¼ß\\œ]ùñı³šhxèñ¸hæ›m\"Äßï¹YÎ]zûÔı:8§Çó]Z©û©¹ïZ}Œ(„©vKÈ{êûÚ?ÚÈ£‡´ÖŞE_Ô­¤(·Ï,O¥q2Ìy¸lÙôÂríó„ƒ±4±íTñ+hé³ó_,İ/OÜ@±Ñ_zR¦Möø0²Æ´HúÌ\nU}ë9ë„×ÄìæLÕ„MÚ? \0!uÏc1¼NgÜ½]Ğ}¡ñ¸Aş?C‘3Ns\'ĞÃ?$Šï*Ë-„eÏIº\n¸ğhá\rĞÃmçi>!ZÌ¿ºñ4¶¿¡ùå”aX´,Cñ\rÔP2Ø¾múcRQÀãÂ«ğ|Y\"1b<GáiuAg¢J»\r‚-\Z·œ©?ŠÀ»•Í\nO7ÃD>.4ÖÌŠ	…=îë%:ıxcè¦•½-ë«ÒIíõ—WŒfÒ?…ş‘o\";?)	şa†=H—,qšWX\\ñ}S¢k{½ğ`Ê‡œiQx)I+ÇNSÙL¾3ÙÔÉÕö¼Šêi$c-É¸ØÉVÓfIÿ•Å(\"<ˆ­mîí„#«êTâ¡ö#/UnÇõ¹Û¨ËBğÌˆ×d\\á[ïÄ5ğ¢Î4~-0ÇËõ?çKnæäÂB/vn¶¯@·úµ×šÖçßşq\'Æ,6ò½ó,úÓ¦O¬K¢Â4¾=É?\rJô1¾?¯Á—6«ÑäŠTÍº¥Ö\\T•sdó¿ÑR?e­-«Ò²M/~}hSBsscuŠÜnC©¥“‚Â£ÓRs­İ:ÈÁÈï~¬U?<¯­ê†ö,åò¶øöIİ?‰´nµÏ©ĞĞ¬ÜT«RğG½ÖÚLD¥kvÆD?’?uÈI¬ÜAÜä.*jˆ×ÕÓJ9:õ¨[T?uø]>Ë:OJ‰bLm ‹8²ù˜¾„ªQÕg…ÎC©9a?jìS|\r™0¶ìw¤›/wjğ‡¿ïtÚ ’PáZîp Hâ	kqåá5Ç×œ‰4?K<ŞêÄã‹õf5[Ôà¡•¢¯íº×z˜F¨\\ûŸS}ˆ%Ìj‹\0QHYš]WI¿T›@Á«>^’N±”ÇN?Næ>±àBO6ú˜ò^à™õ.•›Á«®Ìğ7Š©êŠéÁ—ÖAeeêüÚ½ß•³óóaò°ceg€M^MV:Ÿ©•ùJ“šWêN**©İh{7Ó\ZnõÃ™Ï  _\'›Å[zT\\æmƒ8|F1&\nî¡±ëÔ]›\nF	›§\nÜˆä¬ĞVQDœN®1¸s8÷	ìNÖà Æjeò7bï<–wÚSŒ¯¦_ìl^TZM“h\ZiHj ›x¥ßp¢?!†¦lYğíÕ¢IS?P?X\ZÏ’;K–Å5ìéØêßšX < û=„aë¢K;OCf˜ÂÒ¢…±tæT„ì‡¹7µ2‘ì¥Ê=%Zd¡Õdàr‰Hšb\rzºS]ÊzZk#Ú9¡³_^DPğTPô•=¹åR\\tì´.êz7s¡oúÊù\n”\'—ùóª?U\"k?Õ¿ç)t\'4q`wDoŠ,?E\'ô\0Ï^p¹Aü1Qíø±_HöùéE3$[QM‚ É\'’­lMP²,§ç‹Î3uÏæjÍ¿åÌúd¶Ì=ÎÍ§¡ßû¦r¢ª%Hß)NIb‰³8–`XÑÙGÀÚ?­4ÕMùÉ#šCŞ,2ÒôØE‰Ü•$jª«(yB2µâ8¯Ñ“¶LËk4/A¢?(›ö1’ëÖÖÏaò‘çrgU¡Dkäe›+éèc.ãç—¬³]ÔõÕC£”·êÆH6bøùh¸#¤¾k?ì\ZC]zsÕ`lÜ3çŠvlûŒa6ìÕ#14ZaS»÷|ØzŞ‚=zs­ûÂÃÖuëÍ-È…¦ÀøQkëİ&ÄÑYù¦L?|äU•>”‹ô²½dßƒOÙYÏİà+A?%ŸX¡o›\n·K¢#ÇšI—8\n§\"Qwï_FˆÀ=œ”£’9âQ:/LœF¹\n¼§ÆŸ{Åê›¬üÍL¬Ö‰¦å\\Òó„¼©U‹dcVÈ+Â«ïûä¯Ü~îØ?\"kÙ@üáZ<ğ\'?QIÇÓ™órşÒ7Ä)‰¡HÈ°£÷Qp?Ú±	¹òaqÍLZÉ\'H—éŒêB÷‰Kú?y¦7õúLŞÕ$O	Û~?xAË93øµDûZ^eA\0â†~×jO@lÑ´e#“}0Í#…\'ÏoQ?¹•ñ%Îôúö?®TÓU&ÚÃÎ†D—.©WK6ke<aµz”,ÓjÕhİlEN¯nSQpeJ_?à!\\(?÷R™E«ı¸¬ˆz:àë}ÈìU”ñA‰å/CÁĞD7ñQiõ«‘EÇı³V|ã{œÏ?ÊÁ³¥ -Zq9®jh«„·¢Ğ1ffŠFH8tâ	×Dnv½>sD0åû~>£*—¹˜VàmÉœÿ?gCŞp=6>[WpÜ)UØø¨”ß?àücUø)è„!ƒ\r:¯\\EaÚÛœ¼–¨˜›#ÕÇCÂE/³e‰°}™z|8²@¡ó–æ&]Vm<ËÏ.ÓŠäËYóŒfç¡yæ*ëIH{?—ñMó\r2°?Îv É¿šõÑL£Šxs%3øú\r?úzó€q§û‚0xÛ&?ğ#Ìª-l·qéë“·\ZEÙ{gçòÎn1À7ÃĞÄp—¶‡ªÕGº®İŒ©\Zrá[*ûBr,|£ÎævTe?¹?N2?#çqiú3ß}İáb.²?Ë¡?<7¼…*Í`O4~¿–î:{˜Î7\"<y¬é«ß±–[îñœáñ}cƒ6~šú$¼­wkvÚ,ñğ×?æYò9!>WC×¨8EğñMNrªjTş¹$îQOr×ÓJÅ0ŸğFº>_D°Şğœ?ùI?Ê¹¶uÍ‡‘¶l…ìˆ¶å±âoäËÚœ8¸ä±°g$Q`5æ€ï££2‘>}·Ûz´?ÌøUC^ô#3X[™~ F-2^X7Şxäéı‡çIg„D(À‚ù.ƒw&E¨…ı8/Ãà(ı’X?™Íú-GBŒ¯²k…?«ŸŠÏ¦Om¨Ï~pÖ¾œ¦u2Ó²ıƒm”’‚Sã¥/otTk2Ğğº6f•RSt|Û„„ø’Ò6Å±u\nyW[f),Ñô;_ã5¢?H»Y®xs=¶CŞš?@›§±:å`]ˆGºvKEC.%Úâ*ğÃ7s¶2ùˆ\0uÆ{Æ?CVgyŠV#_Ê¯)<@Ë¿]CMy†ŒªÉ{Ö7?$!½ãwórîá§–Ûª_æ‰ÆÌ‚•¥•oE¥>\\oà•*š7açr åI¦ÓG×,X@ŸG\Zq3v÷ŞŠ•É,\"Œ_?Y×À*¶\\O+§ ÉñzV¯WßLªŞ%œ/®3ÅúüŞí¯F6ÁNU\\E_´Ï¹.¤Ğ|gùÄµ’öä\"±eĞ|O¯Å£<v©J›Fç[&zyö¼éÂñ7—Í\\œljï-‹3GÆ	Àç.Ø óıÙAÄg›?sº?|A& éfyÔ9¡³Ğµ:_n¬g-0;ô®< JzÖpÄ9wbn¶ïp¼âË|J$‹‘ü<.¶êÔ©?ÊÖàW‘+-?}¯V»‘\ZÛèjXNäj;­ÀÊĞ„3pSq·~çùÍ—æ¾*ŠÕ’ë®’ôwšRiSÚL¢¤2;¼ )söÃ¹G.Qw5ˆ[Û¼_…¿<‰jà>ĞÍò	Í•×)üy(±×-û?ÁË»¤*Âæ†/i.lzG¸Ó\n|¨~ÄÜ] U›½ˆVë>™ö4vIÿTùG‰Úç(Ïº¦çZ—ôïûQ+ŞàÙµö^§¢mPfÚåQm)õ+§z)İ8°\rñ9°lŒ^³‚¼z¸®¹?‹ŠÃ4Ë5:ëÚòíjÚ·c¾¡~Ÿ«kúb»©××OÙ”O%¨…%†‰%V°Œ._R45ÿˆ¢y†¯¢[¦+ğüy¯¶µ®¶®©?Q‚ù•(WY	f°Æ5½™‘¼|;Ö-½çC“>İ9ã­õŠùéfAñ­•ç«;ÎˆÀ7››\Z\n6\"t+c¿„¬dÂLn‘%MP¢Ñ\n×Ÿ~[Šsã?¦tä?»AãĞ‘çíÅ4À¬ÑŞQ5Òx£İş±Nõr¬f×KT;ï}AæöÇ‹ÚSI^^J¤Ó“Á4^¡Gà”ŸÜòY¬®–SÆ¢r5ŠDi™?}SKJS?R’Ï3?kÙ´08‰w×!6ÕÖ£ÖYÇSµª½—d‘N“Ò·¬sÃ×\\\'çáÑ¯tåÆ…M‡ìåˆ	f¾6?oÌ]ëÖÕåŠ`øœy«\"ôL4Ñ‘ç–Æ?/xÆ´\røÑÜ\'9éæ¿Hi©çqbóù~ÿîgú½Üc¿&uhŒMJÉ\\W©±@ØÕÒI×Œlè\Z÷æÆºæy~pN¾ğ­ 9ëf±œ-ò€§R?fıÍõ%Ù\'/VZä¯GÚ<(Sçãdkç1•ğğÔLL¶~U×—1V–’a_˜9ÿÆ¬°ˆtFĞE­Õ(×Yà*ÂÍy÷î7ôkû?º…î9õş#ö‘}9[\'‡¥<{7ùÓP`Äe]¼\'Èp?›öhL…sA“xS²XşVïôÚ;û¼İcul\Z_ß|Õ’é,ph|äÔXzWJyÄTZYŞUÿÎÑW|›ˆl+åN?ƒìÙó•]¨ã·É²ôs\r\'K]„•&y¶òPüj¢~PøÜMTWop¥8ÉÇ²Ñ}Ë|MùˆJÖm¦›Qá‹—øàCjÄd‘“Z«•2OéÔ¾¥Wy]Û€B\'VW\\Ä=¢”ñî<û}ì```8YöY>öú”ARÉoy¸\\—‡]gz€ı\\ëİBi§Æ÷o\n¥;-û®9Ì–æ-ø¶Ã”Âo^dç#Ñ\r¤\0órg<>¨ş¶P45Ê¯:Ğ[–qûşa3WÈÓá°r¾£¹âoü‰„–|géË=Ù´\0.šø|Päáñ{-M¥©EŞ³:UÚÉ!T¾iõ­Áìo%j†\\B ?·¸‚_ÈR¯JI¿ìÇ×­óc¡¼záş%Ï3èÌe?oJĞ«æS)2“?>ë«RN%”¡S×D(Êß\"6ÄĞï“?ÅŒÜjõO×ëê7Å?m¿Pö@İˆ~şüQãùÇÙ_?;Ö—Ê\r#~l/Õj}?ª·×:häY=GÄıIÜ£á%¾D	ü?>ÜK?ŠÃŸOÁ?Í—)ŞUè¤L,$Z³Õs××ûš§áìém0¡v~w¡sÚH°l°¾\n}:â¶DVÛÈ¨õIÂ¨·÷šMÔ“5´¦\n€ÕnöËßë	½Ø?Z,¤ûñca¯ó+ŞUx·\r3¥o­»JáÁjºqÏuâOê7QÄ£õ³FcË¾tYaÊˆ÷«Ä•×±Ej?‚3+\"´EÆn§!\"Zkp¯ÈKb¥Ù\n—Uˆ×ŒÕiD;Zhßy÷%_ex)ØõN¿‡v9§?öšé¡a¼¬µş¬ÆUšüì÷LDàODQ½\'bŒ¯0/–ç!x5:ï_–f9ˆšM¼ZNş¬‚ûÒ	Ú5|°ïY¨cÙè°æeõåÅ?äÃÄGºÛÑâ7?İÖ@æzyæHJ?¬rÚ™áŠOˆÆ‡¯¢ÓèÃ`^]??«/Ğ½*†u[Ÿ¥úk+ÎŠo´è\"Íî”ŒÜãekpn‘F(¨óØ{Ïr€Ï6EÑ(\r&€Ÿ*xŒŸÉv#~a³–÷¡¶r>Üæıyp»­R«ª.™\n{\rÛ8Ã+zû?²ùá:²äëÚ‡{F	Öo¢<‹!w%¶bå,óÛV¾Á»mÆ—š,ZHÕ‹×ˆ:ºlNßÍ³Èıv\"Ò»ö¦V?¸ÄRM]MT»x` Z­fls·Ã4IÓU¤œëîDç½ø¡›˜Øs´õ;sIÎ—{Âº³ÕZ ªa‘k=/¦kò_İhP:ıfô=Ó©U´R¬}¯zÃÚÊAçD:³Ÿ¾\'UwL?a8ÍÁÏğuÃø›&M#’¦û?2±­4Üì©JÖöñøŒ×–çŞ2²\0£?ÈéMì:†´7ïÒóïoÈ.-¯x±µnÌÃVf(²Ğÿ²æÎ0ü\"ig^a,ÏX³ã4‘Xî£Z“OÚbÈ©ŞX‘M¤¦M<÷¹ı*)-ÕóVyô†ò¸ú¤ÁãÉ1“ÙIh‘q¤8ôú3s¥à¥n<_¦úû—ø^çˆë¼Õ»\"`pûÁĞˆeÏ©Óx.¦ï¦²¥m8ê´®ÒHU–øS¿––P^=˜©ğJ÷¤È?Vä‘¼*?~1âUÌõŒ›EƒEaæ!rö!#g¹‰E‹¿Š|-UÑ^ºÁqÖÔæå­³“§¦´ê¿ßìrÛÉx-Wi9BH¤‹§ÓFÑóä%|–>êäçı¨•Ä\"ÑT-<OÑµk/§Pz–\\†o£%<9ôIÚÏHö”µ+ƒ{GlV//‚y¿$£?7Å\\ÈÑ¶GÑLtÆFdŸ;8?Ãxš¤®AæşJış5HèiØYCÁ˜µÇm^)ô˜ĞM‚C>İ¤{©k½m<¿~UÔ±O~ãÉ’Ş©É?A?@-˜Pˆb#ƒÁ]ÛÑZ³z_<uñò§á‚­Ù›´Æ4%\'EÙ>¿ò×6ÖM¾sÄQ_=ùÁx6‹%)ü>XŞZ÷Í•uè8t/–UÅIÉèPS&¾\'‡Ô…¯<ı\nHq±CàÃ—Ÿø™—‰ÎTû?Ó%ST7œ220õ>n^¶n˜“ı•.éúà\'uãx™ğj?ùıµÈ›(v¿€´“êÖ<£±mAƒ¦#_¢¯±¾ç¡¸¦¡z?œ_³£—,:?İF–ÅÄ\nî?$XĞ¼i{h£n@¾³Y^X‰ˆ£i=êÖªÓH}ÏçXÛÖÛ€	6¦ÃW8/ÜÜÂ\Z?Æİ‡+¦Š«\rÎùu?XES†(Bòƒ¹’‘©:¯9Øã:ÓNÒ’\nÇo2=An:¬Á¾¤Ø|¸í^ªşÉ~öe˜ cƒß1æ0‰ºÛøOâ¨ò%ÁílFşÒ,¬!ú¥zÖägºw9®q‘[¤£²/{ä*f§zyç@÷Ìãìâåó.¨!:+#æå£â2‹´Ï7(•·–ı›¦Bªü®ñ•r¹?Ğt¹*mæAsİoêÙ¡›rw™õÈ6Ÿ¡?æ	Æ’Ai‰êyëIş÷jå\"?è\Z_ª(çà] ÉÍy;]\'±éP™xçİ¤ÙûÖ\Zª9“Ÿk:¶··ø)Õ<Ä@ÔGf úıkt/js?ú¸¹:3”úlé\\¡D)<UÂˆõ Ê‡`šU½`íœì\ZÌÒ•M96´·?PùÕæûVûÁ±èÌQ\"f¾‡·9y?¹’\'åù ı»ãMYóìü>³?7<×(­kCÉî±\'à}ÖP˜ÔÎl³µ?:2Ë§ÄK`©0ÜÙ«Ğd“(tÕa|6‰µ!\"Å/g/¡î¢EˆPxÀ9]R?j0<\'•óšå×}[óu´,¹ûi¥“=Cî%ÄúT‘?ÌÉ®Çï£,z•büèÛ»:Qdó9u	æJü·[‰äf¶qRòA[Ü5:{…RÇÎz*–rçÂ.¡m\'™/I€9?(Ñ7³Í6Œô‚ôÒ“@ù£4Ì\rp<ã—QÅkæ²G#ÃhÒN+¸É÷?÷.Ä¿¢ç–58_Ì(jrÿFÆ¯Û}+%Ä/hÈ(çŞÕûÆl…f¯,¶X•–³`fEC’FìI–s½ÈºÏ°<åÔrz@úu\"»¼`ª?2¶(WÊî/wƒ4¾\\\\ÆO>|yüÖ’˜u„h3ù´ëÑìÒ;µé*4Ãhî-:7ÛŒFb1Ğ?2znßÎÄîö#\Z­^!KÆz#, †]Õ¨uK2ĞÖnzÌ40$€£ÔÚ[¹È½èı–\\³äó­êlØ¶ùQ+ùïÖß\"èëûÌ¤+sYRîR’%äœh0E\'F÷@>‡~\'Ò?şá¿eİü%È÷k\'¶Kj5E4n\'Ö¯{®ÊÒáÿzåÁé¶ğGÃY)ÅŒ2WC[Š¯tİ9KÑ·gË\\aU=ÀÆÙ¥~FâÁÉX”£Ti á\0EºsÙ]İom¦~´¾Nà8²»\Z±Õ´Ç\Zú\\¢…s=Í~;\\Øq»7†›³&»–‡å3Ëê•¡ÜÇ×Ã¼İ¯?¼¸@?…ŸEà|9Y[qÑ‡ûÛe\ZƒZâIG¸Z$Ù8ş’Å¸rî|Uõ1ŸVÉ‹‰i«²º\\3o>&sÙÈ.¼u[G£!C¨èğMßôeÙb¸ÚcV»ø†…mş‹ÓR:º×l¤é.È±¹n(Ã¹”tà-õJ-ê,¼¼tÖÚÜ?3]†İˆÓ4‚¸Ù\\¥ú’‚iŞŠ‰6f·I³ä?òék{ÍáÊË]SŠ*ñÈ<¿u?Øÿ^Ô?1j!UrQ¥/óŞ\n‘\rVË”W˜™hUGêeê8%Š7©¯Uú”&VåØQ¢íß>Ki–:ÆšK¥\n<8¹$j·ê³uıTd¨”ãQ‚¿³IàæcÖ‘Ø›g“¿¾\'ğƒä5?3Âı›_ÂÙ:V(‰Rææ;ÉGz·2957Ø”±TY‰çm”vùöFÜ&1_¼×sÁD?OYJºş8DĞâTJ)¨bÎéXB¢Ï™œÑëßoª²Ì94?¿îWoşã®ãAê\\°âÊC?›òÍ}¤V§3ŞÆ°¢ºŞtûÕeÑgÁP;ã4ybÏâ+:aNÄaŠÌ¯æ®¾ôéÀ{;ÊVv¾ñúÈ)Á•X””·M}ÜŠÍÄf­”ÈoKúZğk‘‰ğ&Ux®ı˜õêÑ(&/˜:q”¬YKÛ˜\'9¿?É;I‘?WCr5ÕäáFí¤µŞ\n?aÔGˆûªº©Oäu\r8Ÿ’ñ1ªéÿ8J\n/ÍHü.\'«¯ôß™8KS4MúşÁhµœìó¸ûÙøK3SOFŒšëú,ß-Ò¾+^íì}Ò¼‘jä“u¾»¨üĞ¼ËC#¿œ¶·?ÖWIÜæ™¦O¨ÕHR?eÂtµã#^h“y~_Véÿ˜Å”ç-t,™ãûLãHE­5å‹ş²FüÍ1¥ÏFËœR‡3©Ã×õTaíËYå“uºù;üÆŒbQiÆlÍl‰à!2ÃÄcœ:?æèƒ×ö ?6¸Rå²21X†G2œÅ¤¹\\ØSgº2«E†¥¬?–¢-qyB€v\\”,\'ì²³1CXÀ§w$›Ù —®&25U¬«i|T¾Dy?N\'Øo4.”¼ hø•è«ãƒ kîlç¸íÑëO{Â»Õ^7uwŸqñ€?jÒŠ6æœW!gÈf!	œ[ç¨oøf\'{Ü¯;Áşk²syãiñ×gêz(½…Û(ë3²ée*ÊÖ“×?DŞİ²š?=}êê?c‘É›òÜ/J\ZH4âÜ?l–•2Që§åñ˜óÅœêxhšg÷é·43ø³­€JÁ|Œö=ubç•ÛÎ,â1ïêñÒŸDÅ8Ûç¿’šÄ@{âW¶ĞtÍ!õ%#®xÑB‡\"µş±Íàèèu—?R.—I7Æ&:Œƒä³³šKì´§\r­7óÈ?Ä‘’”E\Z%Ş^§5•ô?vÉ:ßàüN¶Ş¦·”ó;´(6m®à¸\"¡ª©ôSÛÂëv¥uK›Í”Ü?ô„ù¾:VLfWQ??=r“;;òq¨œ”÷+~›)nÈf¶/íøÇÙÛ.kS‡?\n’t«k~—¬º?2=0ÎëÈ–swX|Æ®ò’¨èáó`êõ’Äï…Aj¯$”?iL,Xù—3÷\Zš0½Æcª•%£Efj¬èMÛŠ”z‹pÅC]U¿¬ğ%ä¶€Oà|û?óOÎ¾İ4kìË™*»>ıhÓ?h†ªáƒªR0ñ	;¡¹ÎŠAgÄòwtBã“t”vÿ¨“ÌÉ\"¿NöÎ·ÄÅîÈU´Zööêƒ×¦CPE··¼ç\Zø#á3çäˆ¥êW‘,Z‹\Zm/T¸ùƒ˜òÎ»Ûä\n£ŸÂzñîU‹4j1¡?Ú2KgÍ:š>¯&HÚŒJ “%1ô<»I¶)¾2Ö<®<odL‚$¿PJ?ÆÊ‹VÎsİ\\­?=Ó«®j\"—¾áÖ%;¶>Ä\"à¤¤f¦\0şhBoıØŸšå¾½}yĞ\r‘vîÊ³şp1í³x.sÉ|d=µÅEÏ´İ!ª6­Íkm#lO®^ÖyÂoÂ·àËu+3tıºEWK‘PUÛ?çó¬™İÔ¬\"s¶İĞªS¾ç“Á¦\0\n7×™İ”d\r÷@VÉ©3Ã]h˜{j:Gd¯¦éÄÒè{¸„V5râk·üœ}B9à¾á­­Â	\"ğ3u¶ÒÒ\"n¡Œa9ß<I×Ò›sõ½‡\\ÉoSŠèÖ¤&wK.f±³|¯ë×³²òœ$c?:mòYreAÚ’&²ó¤#ıšO†	,î“Cˆçì«`šnÖäÕùs]Sß=ê¼\"Ğ7/ÃŞ^:tò\\!q¦:÷½¢·ë”öêF…ÖÆ1FŒr“2T¦ö	zŞï—[Óiı?Áª\\Éï;üà•Ğ1–\n$·Û¸9@JwòPÓ¬Sv9²títJIÇé©¨Äª˜{Äàª\Z†g¬ŞWÕµ3ˆêÛ$j¤å;,³ê\n0?KêêâÍ?BG°?rÂm®#(Oz\"Kë²ø|íF\0nyÉU?ÿ\ZAÄM¥Ëœ‘İaÿt‘*.½«µI‚E=BÊ¦^*ğ´Í‘ÅÅ°B\'U1cÚÍç0ìª ©êÓ>ëô-éë\0‡6á-³ò¡ZÒÄä2oƒin¶SôW}c‹¾l9€³·ViU1ïğ+lMr’¢‡e¼¼Ò\'‚\\?î\0‡ğçğ’Íúrˆ¨F·„²€3ïèP?Ás\'ƒiÈ….úq$?V2Pq ä‚I7…LS°*+áà§í|p[»ÑsëP,Ê˜õ?ÒWy:/ª’»‹hrÒ8²ºçaÄß¦I‘WeÍŸT†&/Tä½òq >êäGÂ™Uef?Çışø§S©ì—İP¢úh¹f*kË©TœkçGÖ’úE.†V§¢É¢ÃŞ¬±¿Óõ‚={áAÕ§ôt¡!Ê{?²T‘F^Éˆè^-4uà?ŸzñÕŠÙ\0ßa¾ØôsÖ)°™ÍÁ´ôY¹×?$Oiû|¶bz|R\Z®ó˜EšAéƒ€¥U÷‰\0&ZÊ‡„\"\'fºëO{F8²`|ûyX½ä|x‡p·èánY<Ø†?2ArT¬ëpôØåÏæ?o7/ß	¬gª«­%ÑÆSq?Ç‡ºØ\"î¬ëXØ‡1jtÍiM¿,¦ºß¯ËŸÉ’ÌÍñW<SûTToµƒ©Á÷»^IÖB–ÛD.ş÷æşÊş7Ş\rà™¡\\‘ş1‚R£·y4Ô³N\ZVÀi>Şµì»¯ÏÙ×[\\HªÏÑáÍéiÓ(“®çL×bh¾\n©·‘&Vl$£²˜¾.?IGÆ¬tòÄ&ì[<²^3>?K]øÕ¡—n}Z¹WEÖeM!g¶È\nç?L()¤Ks\'+w¶8ÑßsÏ)J=wg8ß}Q?÷c5ä¾|\0bãê¬ËÃÙ—ú÷ZèıXÛn7è\Z_ã?«O|ºnv)…†‚ó	ó´\"$ı*¹Ñ»¨±stóµR–6Ê‘ún<´Ô§¾?kÇwfÖ®4Î-S\'ı”¥Ã?òî¶ˆšæÌİ«^†òVš‚?ß,Fçgƒ?úÌ\ZE­&ïÈÚµ‡?\n28[‘ÏééwœfàR£~¡QÔßIÈ(`Ğ˜|ÏX»¿_½¯0Óhâ_(p¹˜6æ}¶^\'C\\4#A~ı?ÖŒ0ü ]ŸXÌ7fíé8Y\n°İ™o?A‡]I5C/$? ©äª(}|¿.7bÆæYE\n2òìğ¶1!fğŒá™ÍŠ\"Dı«İD_XGŠ¬ÙJ·:Øàho<?ã¯?\Z_;{?æÍ°“Vùñ/NQŠruªCmtÌÜñkl˜wÅ?ç®òPÅI¡Sî}´‰g[ò!œ¥±k%¥2xg£%Éx.Ò…¼Ô›KÔ×9;¿ÿ›6Ï+\ZwõÎ‘cgR²ËsyurfÔ¤ly•:Ãr—ëÙ·^g9	iû&S¹uîó=òà6½¨ÊzĞ#×ËvísÏÜF]É§iJÈ<HğZªA£ñÜÄE‹,7¢•rå0?r}D’Ñ;8ä*KœÍUÍßüò°£”?C­3Â¢¯-F\\KÒ(:¡éY&éø=Jôy!ò´ìÆB*—+\'R–p)ô·¨G”:°ôˆğ¾Ix¤W;Ôˆ¤\r´¬.¬=Öé	õef\"k^ñ¸MEsbó<Jî 1íAÆTyıÀ]ÏpÑ”µúl|hˆò‹S(mv\"…Ã¦ùùŞŠê<…¾sÙ«¼¼1ñÔ¥áÍ	d«ŞC=©(ºüJ‰Ì`ª¬ğcjğt”REÉœ…q\Z\"-€Ç4şÕ\nô?W&ı\n?eh?êÚ“©ÜFGÀ\ZK9ú-dÒ(‰‡‰h+¸PÄKS«–ñûæç‹çŸßx-Îò0êh3úÈÅø	U¨r§¾öT|ÿBì\nÃóæ”€–µ7ŞH:ö_?áÛX=£hRôKæuú ûX‹ær’êñ{>eCèÀ/JøÁ?2o¥íã\'³JJõ¿Á¢òŠã\"ƒ[Ÿ’ºzÙôœ‘+J»´°ö¸ºJ—:ü¢›½¿³ógè?u¿yfTŒñÜ8§*ş?qîü(ê@4çí8!í=zùúNDÊ‡`çù‹‘Vd×NŸºI!óñJSFnúcVÅ7İKe##Æ.[àš1A>ÑçZ´—µï^òwé—ÖW’ƒ/$^/8$,A(™\Z„ÊÍºÁuìârˆˆsp”öÅ]c­x½¹İ7¡dğ×?4`.#¶ÓÉi«™oÁ\n9m7Î4³ƒ‡²b}`rªšê	`I˜K»Ìó’ÙÃqÚ¶½iº²,ùƒ##>yYËÙ,·¢”Ÿ¶“|DÓuÚW¸dò¤Ø¼B.êÈsL‡7¼İ†’‚6\Z4:ûftßdéæ ö\r’µ+ZC)û7yàùç·]ª?q…© Åá\'M¬Öù¼²‚GK\0Ò?àNDe–Ÿ8OS|\\/3´Ø~Á©†õÃ±¾ã|^—†ûs5Î¯|®Wë¤?Sx—£¨um¸g´Nu\":3U±:É¨qúDÒíjÿÜ§ÎËB›Ì¨QõjWûX+^ƒät^p~ˆß¤$œĞûı‚Mí qlíô´N¯!+\\BÈ¾|ı¬ƒG µ5¹â­7\'At/UVU052ÿ¨V<^{?ù:©0gA\nS{\'3üæØù¯Esâr‹Ğ¢§rO¼U\"ba²Ã³;7O)¾JC™ÑP÷?ÍqùZşRr¡r³0‹O™­Í‹Tjæ¼24£µÍëü‡Wò»ú¦­^Õ%	\"?‘4Ë¯²ø¬ø˜=?Ô±M¤>|^™ w@?Lç?sKµËçµÿ+9)ñ\Zp×YÖˆ?o%=L($i9}RİìûM¯ô›Ù·²Z“Õ8àÅÔ05ÂÖFıİ(\Z?´ç¤\'µ¯á_8–ªÎ ;ÁUÍrüVÑ†T\"˜ÇÁïq+¿×S+2\nŞzê‡j]é/$$´[ã[‚hÏø™ü?à÷EhZ]x¢?»8#¦kCˆ¦/ê=€&ç£Ê/ø?îØ]ûqQÎPùº*÷\r”`ÚLícSU¸Ş†\\.í½\0Šm|óŞS?tÎK?/èú&-à”u*g\nV²Û¿U Ë­Šp|È÷‰^KÆ_œEÉ È\\;B7¬QT”Y$@S”’Ş\\Ô]wm\\;»©(ŒåusùG]Å?©øsB¹U¹\nó.qÕ\ZöÜL6ËÎ?y€OtŠ‡¡mŞìú‡¸XŠ*DØ„Ù×s„YÆÙ-‹ÇÈhJÎ¼ÑYÑÌm³ˆ-L	?µ8²œ·Îv‚+?â®«Ğp§NrÑ|CÌZc<Dx4vgËĞ:€i&¥?Úv°Y·~”œwåQG åÖba9«7—q|S_b?®¬y‚·vù¥;móô‹êæ¹¡Óì†=´„h5¢•ö†ŠGi“¿‰0ûxÉîmQe\\?Läs‹pâîş?ÈW/XúóçŠÜIh/FnÅ~{sEYK%™£©—ºIC?¹çœ—A?ójXµA&…Y¶Âá¼ú…A÷`ŸÒ	[´Ö»2*pbœ.·ŠqÃÛÁ@´ƒh§ÔacQCôUùĞšEîÆûÖ_ÛİRzÌx}\0\n/.:‹gÙ“¥oT£b\nZ…!(E?E‹¼ßì©âN™3yÀò‰ÆLD\0›ú´Ò`xÁıºUõÄó“änNäæÙæ”sQÅOŠYîĞn;{ª/?Æ…‹¥oö»—uHŸ‹LD\"75JßËØç™æ[wÂËşÂƒéB—ë…İÀ¢ú\"…ˆŠĞiÒ¦†–ËùA…úÎ³\\J®íÇ¿©vËBëîÏ‹?LR?6vŞç¹yN=ª\Z®\'Oìè}?S%’\n¬¸Š¦5#›xf¤x§NèY\0êŒo758¦\rŞxY>Ò©ÛşGX!_MÅÑ\nØ!ßèôê¨¦º½Qİ?—ä¤Es½ŞwßQg¾gè÷f*»G§cZPûü\"Ã1Ë§(±	8§‰\n¾pdÁÀ5«§$–¹m÷Ë?4«¿`.ÎW¨˜dê°…Ó´D˜••º­œ8”âÉÇöñşõ¢î»\'YåØiŸÉÅáÑo>ÂdâÕÈ%Ê³µ|q|¨ø‹¿x?~àûk’}Qwªûª$K<í@œ½cÖ_ºÎùHX¸?ÅçU®ƒŞ0\"\'ÄLtxå?ƒ ùy3óá÷g×PÚMPå(CÁ?ÇÕdu09ÙÇ²ÁbÏn#/Ë¶Ä¹¦åß0i -¥8Ó|ò¬g´áHÜ’^n›ó*Ÿ]Hµ†ŒÚªŸ³¿#\\Ë‘z‹/şp:9}B™¸I©°7aT‘˜måDe…Òlı©3“d¢›!o(¾Ãë,¨rQşÇ!D ;‚¦.—³?€[›<GeŸæ€W˜ã?02¿ƒl´*oùB,Hî#IÊ†”Êú¿»¤«´à]‡$z]sÊÓx%ÆÅ7&GìªÀ\rKwpõW%ü\"Ç?ŸÅ÷ûÊé‘ÆªÙ¦.éS6ºb–.¾Án\ZÍhí?ì Pj6Ëû1 Nr©N\\Šmt\'a.HNå+›+¦m?ì{¸*FØÉÂ_¨=$ÍëyD[E|uØ¤%PJşí•SiÛ«É?´$Ò7%{	&çÃQVCiDôâ*AL§º\nc‡Ü/]9«=û¡Œ¾óı¡WwË²a^ZBõ\'ûØñ4®­ös×ÌQĞn†~¾t„~‰©\n)??ˆ¸w8EM@óã\"\"Ğ7òüm_Fao°HäÌ—Ã´2N^§XnÿtĞ—=õ\\•„wAÖ¦‹ŸıãÓ¶Ê?)7ÖWÜ/s1 xMC\nM¸_¤œ“#hnm¢c´]?»ñÊ/Saá¢‹ìUY>÷ºÒË#~ilï˜ùºh‘~GÁ‡ÑOí¹TÎ¸Ã¾ØMkŸir¬­Gpó÷tÊâ?k	DäÏ#…_w¨?W_iTP×‡ä¨ÚÕ—?Ş¿bd65‰3üÌgä>?ºÁèÊ\"ù.9µç©?N†HÆ™½Ş¬º8qN&Eş\'üï’2\n0ŸLó™˜KRñ@â™TÅOF7Nr€EWıY’9Z§U™HElÆÊ\'¿9As—êÏÈÏ5xiõ£8Kè:&ÇµûÒØ˜Î˜?|®±?¿“äÓp’oüğ¸¬CåøÒFÌu&vwiAÇ$Ï•Qº4ıÀk({·\'ö&]Â(i>ÏÍ\roìÅ.óX³yõ™æv²“Ò§×i6êa|Dš*ñĞ—¢aó?‹İıóYÕ47#ÃQ‡P*”\\…«á+–öEæ~ä`‹Ñ•~§?h;Ö#c<ÈÂSö\"‡y ˜¤Â)ì¾‰ÔÂ;SØ)+¹Q]ä-“eGæ¨Ù{ğûÙ¡OÜ7ÌÙ9ØÆR$¯‰vĞHÚÏÏ@fÀ)Gï/¬KËpUÊTzT“Èd¶;Rä°ôØ~\n“:à\'#Ş5Òs¬WıˆmEy¼)4Ó…zcŒïÛm”Å™oää%Ê?\ZûHíO!Ô´™Á?Â‰|;ê¤Sñp\"ÿ»¥¡ç„¹Ì£0«QûÇ1µf/âÊ“¥~”*Ë­Æy½1ï¤—k½!ò:dŸkØjeˆÅ×³Æ¨J×T«2	<|äÁ¹ª»>7¿&ĞpsH\Zn†#nz,Óß¯¯\"	HÏÕ4_8;*M|¼Vú˜¦9˜è¨ù±[oAıËZ‰?7P0Â ×ÂğÀ[W>yDµAx8‡ò•Ñt+ôû¯ç8wnz~©€¿Ñ‘%f–?´Bl©qäñ|šzGáÒ«Û(Ñ6KÄìK0µ‘øTõáñÚ\nğ9F>oƒ{ˆÍ—ªz´oßpÃXzÍ<ó¯Ú[™Ï·Ô?CŸºè^»túè-Ÿ¥Ğ5ÀK—ÂIİâñR‚ßFô\\»–ÍĞ~-ÿ¼+¿\0Yc?[$MÕ]HAtÑ•sVBVÇáĞuCÒé¨é§)Û®H¾7|Mf\n¿.ÅßÙï?>©áõJUísøŸ³‡kŞJ¦ÒÜ@äÃ®ŠÊ+ı¬Eùğ3œÈ-s¥ÚS\ZV\0sÏ\0“òJ¢fmÕ‰×!¯	gÏ„l-kzÜ½ë3¤:Ù^è¡uƒ?:Å»îÔ´ü—5·a˜ö`Â›Ö\n0ÿ5¹¸H¢‚KÇ×ú{Ÿ_\'	ì¼»¾0ğÂO\n.6P§M|=2LêC³Š|¸\nşyGs:z\Z;øa¸µíKçÄ¼X†övéxõ“~‡âµ¤/~Og{kªn,PeD´æsCµâg=»‚âJß¿lÜòòs`ãx{Ú?÷Å?¯äÀ9¤ïò[f8üX–8Í–¼.§-X•í?‰ìµÕàÏÇ.)ur(šO]e?><Ép!˜µÕÎYcúÚ£á«9>]-ßßç2R{íõ¢Åöl½0­/AYc^¬×SôNÛŞ[õÎeM7m\rqk¦Ô!û:ß¡o~_êq¾JÔ°‡üBç“ÕSOµÉü¾@\"™tâMnKlqzµ_SM3a?D–»rò8ÂáÈšFúšÅ³dE´ºŸ ]/Âò§ì®Qw27ÓW¾ûäÅ®s´#ñŞòLÕØTo¡>òÕh‰ùãòôöÀ$øB8ÿê´ı§¬fdşRáY|b½«·47½?GG™\Zø«\0x9È£#»@eâ¡}GÌ4;´ÇM}ëµ/gSo¡²şTºÀ;¥cäÏ=İ•Ì¥ït	ßÍ2áüŞşaùMØÚ‰@]&<»«Pl¹\0t1çGéÀŒ±gëD_IHDò¼êiç¯“k<¥e¹¬Ş9*É<ó?üa9{øÓpã©±voîº<ÛLÏa??D¯\rå\'ñLã?7MH¼:™ü)X_2*5ß[´¬QŠw½µILã•ê2s†4®?Éù»ù-íÜ<:è Êàú·Bwi“„‘GÖŸåÊ?Cá=Å‰´Ú^oÈ®R»\r&TÕLde6vYt ¹Ê?“mRÉX¦*?ù„¬£–½Zë¨)\nßb? #QB5ôÕœ¾?L£^Ÿ[<Nº$ò2púƒ1Ü	\\tßTâ\"1xé\n_«×Ë\'œs·¯ÑL2¥pQ€K(Ôç¨–Ïi?ÆFæJn½ªRÔ¶jûˆÇ/wŠku®SÈuC¬7?(¾boú#ÙüµDoğûgoÒx¨‹ªË`‘b%/_:ºx«¿*–CğÖ;:³ä„tÚQ‹Lü~‡ sÕf—ÑÇNM:«Ü˜(½3ÿĞåŞ);¾ùN´=š¤b>gÚ\0Iq¨¬œì{‰\\MhŒ²Ï¸fŞo.\n7=W©KÜœ‰¯H@s}Ò4kïs’á«pdµ,åÓ‰?³[at#õœ¯[xz ÁËğ „cÒ…ÈÀo¼¹Í]ó&óÊÔ<“?b-ğíâ¸oc&¾ÿ«Í®;’Ù³[3cúRµV!²ØCŒç]¬}š­‹?x:	,Bœœ¼|ádXî‰zšJ\"c¤ŒºŠŒY	Ş‡Arò«.ö!“Å;Gû³¯W.Cˆ¿°½v‡õ®v‘±}ò2!\\…1}Üÿ‰^DN›ğ£Ü¤‹Ò?Ó¹œeüi!yß®Ò¼´ıÒ×W6OTŸÏêTl}.‘Š|yª¿ëÉ±»b‹ºD\\ó“İó¹óX¡hõ½£Ë÷¨òò£üï?ºv&ØVkÚ÷©Â?ÒŒ€?h_#ä(Ó>ıÖD¾óôW]?îô·²sFâÔÜí9/[z\rÏ ?A4?nöc§M{\"?¾ƒıÜ©`0ÕÀW.&#{²\"öîÎ•d`?Hï¡èDåÜ|;xÛ˜{†Ã7¯©±«¨g?äŠÓm¢DÆ¨¨?)’Ò²ö—Ø²’œûqUùc?€z#<z‚6	mFa<·.ÂÂA~ê†%=Ñ•éàWòãïËÕU?£	À\"ğC45)«çåmeñ|±t2X½ô»šz\0dÅFh0¼gÕu¨×õôÛÕ	yxRş1¦£ãKÜ?HïúG´™ÜO?¶=5WE×¾‰¢5R[«^\"í.â.Li‚D±Ôe™OEêf7ÓÀ9n˜o]ó?¬`½”fúQ­şĞ1ªï¹]j;­B‘P¢=I‚œy®?‚zñkÒ¢\"°ŞøcémÿaaŞ!9Û³qæo|rËBhŸª{/ßg)»ˆ˜‘X}ùqÄ¸€¸H\Z†®çìr¾Äõœæ†ÃÒ­G0»Ü…ÔkwJ®ª¶:3ÊÌè7æ+”÷’zÖ’VÔ·“E#ü¬SF\rónIş|/S^âğ*íè£\nG‰¼æsdà5]÷`9©QI?)£BĞÅ¨Ì:ZS}ñ‡5ûYî ×Ñ¶‡?2x\\‡sÈ\'İe„³ÈÊ¹§ÚTHÜ„û‰	 !¥—sÈ‘ñ)Ä¥\"9¡6×ÈlC¯9ªiôê£ÕRÈ—t^I7.§DULF¤_§a|¡’ô¦Ï›ô•‰Ğ½Ê‚déÈSæ?c–\rRªÂNf‰	?É°!<j°ñğÅD¸Jcî®É÷j™M\Z>ÙU?R57{f÷+Æ³æÊÅUa(i‰óµšä\'jTÙ¾öeQÕâ¢®d0±½;|×OÖO?ÈÎ8KÈôÎ#ıMş…ëI¶t²‚„Æ…á±=o|^ÓmŒ÷ë©ôsTfš]Ñ6şjú1?P„ÿ‹+Úªş:ChF¶Èî×ÇÛO¤-Bñå¶o­OÎšâäyªQátİâHÄ±gÅÕ¥Eø‡Ë¾aÎY¤S½³RêâàˆÍI™²“ãÓGË\\”LN:\\èy¸Uª_Ğ™„î<Óİ“ì°ˆ•1†«ï#ŠRÂNÌğâi­\Zµ·ßD±5ß}YI àSÔ …P¡Ëè%šİ§#ĞfĞÙ/…ómTñÌ¯ñk¦)úz¸ ”¥ šû´$‚Ç|CÂæ6Õš2PRyÜİIAÂÉòSÕ9J¯®1Uè[×X7j<•}îq™¸Ş¨µûLj±\'M—ÌRKù¼JofÇ?x“\'i©Áô¡ît³§f»˜ÏÄ×øÈ<À?fX”bIhw²påŠ`?]tRT¾¾(&wähãm/õ9Ö Şˆ¯a­œëş]íÆQI“	¨WÌ`ÍÉ)ò!rIÚWÎ6u7¨êp‹5w<á©i1“ÈuşæÚû–Bwú•øö/¢äAñIó±ïıDŞplÚ9½ëj^±…i£à¬ÃLH…NúQÅÎC©¡?ƒœ—?ÖoÖX>ğ¤±Îh©Nœ¿Õëë2œ­ŒÊ\0/š šçæˆj}\rbçQ4Mõ‰gJ–½ıj	WK/ó?.W¹ôÜôVSÇ÷Kôp\"¶U˜t%~gjƒ{ŞÜ±UôaZ??Ç¶ıŠu\\v?çVKJ¦n‹€Şk\r¨%»®ëı©Ÿıß•äo\ZVVnÍ…óèšØs–ü·28ì¤aöjáÜÎn$®îó5_ÒÑ\\‡×æ8¯D?øt^ZWWªò ësd“H¨?Ä37G;ôÆñ?Ò‘\Z%	©2AzÖ]vJRQG\ZñWS2´uelmj%8óšõ»²˜÷\nçåª_‚LPz\\.½,yVqsÈ·5:P;×ŞápbÑ¹¦§õ+e?TõUÂc•ù,o2ˆZÖ§‡sCò8®Ş¼ïs$½´Ğ‡©\ZŸØÓ°SEYyB7Û7?x ¼xòÄˆ´Ü B\"ïú¡à[*Æ9\'O¹•zS@E£Ÿ.]·CßzTæÌÓÑë>ÑØ*ê)ïO\n${õT[€á^Õ)Ê0\"Õa\"ZÁ0†³5WÒ¡k0tğF­yÇ‹ÃôtŒ%Á1·ÊÊ¹Î?kb«)M<óÎ¶\"b_–¥æò›xp98ÚÍuX)ñi©½©›f³³˜ŠÍp?ù‹Åğ;|Óö¹cs©Í«,ñ•R·¦¤n„Ï°^Ÿ2Ö…•<\r¾Œ÷x&‹4@POrq ¨Ø\'?•µ#>½åäÜğV)…kÂœ£HzJ˜°~õäê[0H–‡æiL\\ÔäÍı™f78S·Öü˜p~è’ß·…î Å·ı}·Î«¾½‰jN3És’W¿?ÕæoQ|¬AÔéeüúr¡[=à¯£u\'œ?<\Z±Z•õòAewJúrUS¿òÂ8h?t’•:vB£?€4Í®1¶]yìÉÁã^îäèÜ—á¯ø>Ø•ˆ\rU=<ùôVDÛøİÁA´ŞHŠúµHß¾(»3ºDx¨v[d•/C¿¡óä¹Õµ–™e„ŠÓ£c,?cosÉé½ÔÌ™ñYŞ?é	{=`zÈÌ–%¿«ŞŞ,úø×y-Sµµœ¶Şaë&g¡ÇÊ:¼R¯À#/Å‰õ ôŠ[BñCÍ¥§¤ªk?*Vü4”ˆ¨±|ËÙ@‡Ÿ1‘Ëò¤ò#Â›Á€‹NÀ•eŒYÀˆîpˆ‹3}%#/,tê?ÏL\"‰§·éÕ\Z´ªHßO¯ofö•>“cèR¨ú÷âáyÅêÏ“›aÆÏÌ%u¼?†>4û&>Áëü?–£_›{\"SÁÉ8Œ”BÉØVU?#™ğÒ5?öÈAqqÒO%BË‰^â•W·¢»h¥\\™\r3ùòXà]ğñ3¥E½B/l²àp/d—?£i98úÔº…+ÆN\0œä;x?‰ô4˜?@†åÔqB_Eğ–ştèáË“nğ»?ˆ&ÅÀÊLoük Ih¹ŒTÍbOÅâgÍ¬ÜAdö…qsø…—›:K7U|Ú‚òŠt¦¤ÅÒ,‘±ÔÏâƒİ£>ipQÄ,~^‚Ú>÷DrÖLO?¬ñqãæÎV>wÔ?ÎúµŒÆ*ÔK†øó¼Îƒ!Aü¶Ê\nsá!ÁWsM…W?†°J¼3«˜İq¢Mÿù?¬PËÃÅ•g?\"n<<æÖÿ²¬£Vœøy±k—Mş\r˜²ú@{ÒÒ5#1ş¯1EÒ‹ò¦/ÉLÇ£Ô²Î²	FÙŸ>B<C:hdöä,JkB/Pı Š£LJ¡µ)j!ˆ‚,Áêèâ•şìÀ¸ŸYÖ,‹\0«9ßâ`nBìmc­–$»³Ù\\Î“f\nX¾Ü<yKI½oÔ&Uû³l„ÕĞ“¸»Ù½Ã³í*ãkG‰fèğ@Ü}üIKuiò\\í,\Zıƒ;?á.D4ùƒ\\ä—êmzU?©¨Q?>Q¼§aå„òH¥ä%îB‚ÆÉ#àşùLìì7Se.5ÈÑ\n«×ò{ù•±ñã	¸uqIÇ¸ÿ‘”Æl…å\ZI\rÙj\ZYWEÜ=Ÿ@%?wK‹¹’Ğ‡\'ÙÁ¥`}íœ ç}ÚVeğƒ¤qqk£~Ÿl™‹òeÏÆÒœŒy{0ñ©EÈ­´“e|•™\n¹Í÷ŒµâããK¯rø6Ò!%>ëÚ¼ó…C@¨RK@¾Ì,»t5\'Ê¸<O?rÕ\'dx?÷…[]¨û%jÔ+4šÑCÂ—,eøÕå>i`…Ù|âª½¾Û{dŞæûBğ:9I$iH6ÛÜ!p¦ºj*i–¶4Ó\'}åH	ÃÕ¨@úhÉ£²›¬‘AtµpëÔÓŸ,o-N×Õõ¿Ã«0¥Ğ{QÚûN¡õq	ğ¸ëLüÙ ÑŸÒµt­*Ù’+˜”?2A®à­#Ì>RRÊ¾G±49«WÂŸßÆÔúèè¼”lÿ\0*’•‡Êl,J!r“áÄúáŒêw#?èëiwÎğ>±6SëGš…Õ\'&7(}4œ©nº—(¨tƒ\n­´À«İÿ|^ê4õMZŸ/‚PbÃeMšÙ³b‚¥YË–ô:›?oñ3Q\rğ–´;ûÀ ìÆµ¹N±R²¸ *py-ù?eì¯¥gŠTnµöF°é?¿“½:,ÖKO[\\`—\'‹}„§š%>?<3½ñ`1#j1q6íÇj4ù‡ex1›UläØãÏÚÏï$vä	,ßhóU;´é–Íbƒ|l‰WyÌğóG]x_ĞFI¯–Í!?ŠeAº®\'e…ôÄâNæC\ræz´DÅYiàU—>Æ¿i®Æ?Ğ \\#´£-[¹aÑ½|Ï}*´·{Èe½a²ƒƒúä`ËòmÔùô?L(?ÂàRÊÃÇùz§ŒˆEí>~™u?W…Ì¯^;ñYKö-ú<såV*}[ê\rz§³ª„%¥ÑŠB\rêY`MØ1Ö„û¦[4ŸÛÚuÈ’^Ï–>íh<¶uğ²ã9ÅÌÏv»§Õ%Oo¯¯ {dÓ6B%Á÷\ZY™†kàj‘P‚~z‹iİ—~Ã²\\°\\7ŞÔË2Şôu}7j%KŞ!Õ(\'ùjL¶µDé=?B£0+ $«×üğ½^˜E¹šfŒ÷Ã«\Z1<£’m¢EVîPoL/OãñMt ÁÈ{?—]jv¨,íì£I|°Õû;øÎ±Iy½¿‹6]¼v:à9b¾¸(ë› zõQÊúá©(ñ\'#:7÷ÁuFW‹€û½¼‰é¤„ğ¯Ã´Ïıt¢hhaÃc[Ïô»ôFDÁ<e”gí”‰·¸Ÿ4@?…bF””ŞäÉˆ˜ø8ÓÇPµX¸ùµM¾¾­ƒ´t}µº6uãÊe_xÀá˜G®Ë<ƒ5OøóŒ;„o´)b¾õTM}íÍçû…gdÕ§¢íLï—…\Z:»÷*ß€OG\\6†R?:íx¡&\'Ô\0?c?³U½›Z?f8º¨üƒ¢|ª‰ˆÔ·ÿğ oëuêWN\r‡Úˆg\'Ât¯Y÷ÎW{QtÖ:ÆBíGÖÎ*kP£­Wîèë}gw~ÎV¹Eîp¼Æû`mœğ´V®SåBä1MÇo/Ryàká«BŞï‰©>\"ó?Î¾kïºÄ¶\Z_ĞIâë‹Šò#z¬+ö‘Å‹¸€*ÓD[`-M#]§4l¾ó¤İ2}8Mí­?£·®­\'e.µVÎJšËƒLñŞF”Lû²²·ê™ÖÜÇ¤5pÊ’ç=Ïè¼´Š[¹®š•\'Ôo:Lš/,O‘Ø\'ÓoÖ•µœ·?å¾üÙïVÉ±ú†7xçVïUİJ{×Ï_«:Úú¤¦!‘d¼¹<##ª\\;Ç3€’)£åQ\\³ˆ-È?è)÷˜U&Á8=Nm+BúæçÜ/°|–[Ó‚×fê<mšNÓ°4f8*jõ«¦< i‹F[-èn¥\\Œ|n€Œ1ÏÊïğ“·&æ¤¥vv!HgRyks}ü3a²èk†ù¤„‡w2®’?Şn7Èfƒ•ÙNô”\rÙ|PQ}\\ëUr-;£&Z®3>ÚD„„1Á[\"şaà¼Š°ú¹‹…îS_;‚õíI?1”{k‹ú\'À?*ïpÑTw{	¡_.¹RC%¯¼n–~2›üòŞc¾®^êÍÓp5ªq©‰\'	+*$òğ\Z\"´¸«?k²‹VÎíĞrÏ¯Ã¥ôHÒ&£êL|\nı&ebrß~4júè×ß8à›ÓFù¡„vïª\rê•Ÿ	D!IÓ½†mİÎšœ¦ê«»~æ’ât§FÆö.áËWÚñÌÄZÈÓ§lˆ_1ÕöCƒ§–JäİÊïÃ†;ÉjÄi¢î?ã£ÌüÂ—ÖR¢)Ù\nö(j#_Şá\"Ó}r®¢1,ÊdİıP5	?Yş²”?àeâzôÉˆS(ş¬”§ NUpßm^_xyÛYêU{ƒJ¿ôp}­sùjmÎ÷\"ÆS6Vgô§¡øœßãî@:{?AU†y\nWQÍ…Y…D?î*óÒ>éß éâq»¢Å«äÕ|ƒ/±gEÉé¤ÿ»SÑÎ,—¢}âÚ^Â[¯=oúûõ.–ÑäpYËC¥‰Îà›9}¯·ÉÌns!A‡…«}‚¨é ˜Q\\ë·ı¦›c8jsí´æR7—Ù§/g]ó/:>~/Ş÷ ¿ï€/%Üà±1ÁvQ–Şç¢ß2`÷”¦ÃÚëß*Œ^E=?Šæÿ$ÔÀ+T|˜ğmƒ¹ì<Lã	¿átªé\ZMñgxkJ\r,8²óIı©K¬àSôÄ¯ü,_˜Ÿ¨?@_T6æ¥ÌU™HO‘iEéÔV£ó#QºÚñ…)Ñ>¶Ãwt	–¬R†d;P?ÄçâáÅå->èŠ§ä,ÛmÄWr808·|¥³äñBrV¾lÙ	Y~xöÂ‘á ûÒøÚú6\"ïÔªÈÉ Ê%±Öù[ël.\"ô¾W[ëZJ«ŸÖ?¢éºõŸ2?õ¢MÌ¡?h	¡±,	OÈC^}t‹?»\rEwõ‹Xj_ó”2d©üòe\\¹’û8]ŞAÍ}Z“ÅetTÎ¿8#\\s8_¯=i=ï–rFfß1<Wq“MW·‘›¼í–a“$sò›\nñã@°ô£+´Ì/Új³|ÊÙ¾]–-?ŞZ?yrømZey\r¹2« ÿ›¿?ûÿëÏşß†y\0ÿüç~ù÷üıï?ƒø…ııg°?ğÿşşóÿÄóûï?\0~ÿYâ0ğûÏq÷¦/`ş¡~ÿùÇ?˜?MMM—/_¶´´ÌÈÈ8sæÌ…oÌ’8€o¶ıÛÎ·:\r_‰œ|äÌê?‹‰¡£oŒ&iÛ@k]C¤\ZÍw¿çÜxH3,ë½İ]&ÕA{…O?Šœtb+KBvÊ9«ö\n·?s¼’(„’Úœ{ÄÆ|Åséªãñó¡3)ƒñ¢ë¸`#Õ‘§×#®ßz>tbÖz6.ã‰üÚ™³³7¨’/Ô¿¢ô_[}Ôsro? 4la@—Ÿ‰?œ_÷&à	i½‘\"şuø4÷R	4üÖ{‘åÙ›/hëé¦U\'™To9SÆ!²¨\\é?œçm9r\"(ÏG•_hÈÉ\'Äˆ/\"?…åá/(0jgfv$çÄ£.wnÕysKcõÉŠdQâÕÃ#Ù?QÏçI?—rO¥“¨É­-³¾=ôôhĞØëBêµ;bÈ¸d‚)ÿG÷£¯Lvj¼x¾uAèå‹¼‰u}bÓ2ëk4°¸«?ˆi:İÒgì¾Ëğé?æ…BkˆÆkÙìç¬?`a3³Ğ¦úÊC?\"™Ø¯Nå¡Fgî™r…~7·­?™XJã»DóéÜ©wéŠöç¦(,ÊYOF¹–Ìp¢åú«jƒä-.5%LĞpÁ»è0åSÔI*ÁL–íß)¡F.2Wf×0ÿW^–êê\09œPnº +Á*¢V!j3/÷ ‰:LĞ1¤ha4­]Ö÷jò‡¥dğ™ ÊFÅêîj›xXp¢ÿâ³™Îâ;,\n\'æn¶ŞÏˆ&IË?q\Z3¬¼)w›Ô@¯=ªB^õä÷»*MÓÉ\'?©]N?f¼såI(ì~ûls3àÀÊãÇ\"•ÚsRZy7§¥‹7Óû±œ8øeÑ<ªBízù…çŸO,W%DÜ!cª„9œ×Z½®´Dú4aéöO2.%Âe\'˜EËue3)%j©oŠ&½W(Gg:úóÙs%|mÚkB*Í\nğ®¨sõ*Tå’È\'¨l¾i¡-¼0œ&€SÖ£Ï‰£Õ´y>eé–Ùo¶´8“ëmùR‰}ÌEœÃ?ªø­«¼Õ«\n_7lŠ§/9<?jı²acy¸‘£ƒ¦b°À‘å•È”7DpçÆ$?éRû~ìãª½ß€—ƒ‘ÑÍw)§ç¤N¤KœÉğè+¶fèŞ\nÆÓòò)(É~¼äÅ?rƒ§|>\rKüĞ×SvBªjîåm\'iy¤E®ø	=ğ‘ÑÇñ1+÷3ImÍc†&ÌHİÁ *üæçÄOkÔ…µÃï½«?¦à\\èN»›R¹\0oŠMM“g?’¼?1WL\\/¹ÿp*t1»‹Æà¦Ùó¯GEP‘%Ü\Z½Ê;æ\"å+Ñf\'·e»Ó\\ãEÓ÷7\Zc®‹pèÎ¼m_+ºa+Rit”g‚Îìfä;æ?—Ål¢™\'¿èQ ¹Ÿ°ôñÏÄ’Åaéïm,ÿq\\£n«×ÿHÊğe“R¸ã34ëagK¢Ûq…øö-ºÁôšWßÙ?WçãÒ&NEŸÏ<îO¾ùNVkñáü‘©i©dOéşbúÜÀ–Ú‡B).…¿ÍtËè>Ì¾¤[ÙWxeİçµø¹v~ºëßÔï1Xç39È6)üûñÙ	?ˆ$io	ã>—°Ï	ÑıßY‰§Ò\nËˆ,tû^êr·\ZéÊ¿PY0ºááÿ9Ù_Ò—«®›Ò<t?k0V5šŠï?µtó¨\0?IMÉ…bè[•‡µì¯;Á¾JÏÔßËÇù“k¥÷ú“Ç“>•T±	Šõ?~$r²şCUT8È\'šÑ’H-©ã*Ok˜vîÖqkj‘ózR\'NÕ?•Ö¼Ñ¥\nôêWm\n}œtå¼•å§Ö¤§ç‡B\"’P¶doU]h\\j›DÙk76ic7joä¿v~Ô_Á:æÃ!¢¿ôÈ’!lYfÏ„…øËÍƒšôC7İâÒ»îTÔİ½µÖ*›®lÍT:¼èvòö-’„µõ;BÏI¹¼@¦•r4G¾µ,QİğD~ú	º4n¶|ñóTı?Çn	½«ÿ@ÖÃ<¸HÏá•øœs‹’Ë€¿½¿i\0¡l—Ø»É7kE3#ù”1\'§o¸DàÏ©xKS’-Á¹²?gß\rÛXk>	áªÿ¬ÙÕ3`ğ}`Zä	\rõœğj\"ÜÇ¦O|4;9B)j‘LX|ö3…7‚1PDCˆ})gÁ¿?ƒ÷nİµå‡Ã‰ßB<†Í=íÜ?İ;9òğç–^îdyA(8±¬YÿkşçıöŸøÇÒÛÆÙÍÒÃÕcş\'lÁaÿ?„~±ÿ„øøÏÿ¯ı÷?ñüÁş»ü°ÿ¾¼_ÃüƒÿÓşÓoî¿„ù§ÛÔÓá;Ñ:.h€?/?\'{¦gë‘ì™Ù,Ş¬“?rqdŸXR–¶êJ~Şz şqå‘G>N?^O¤?™¤ PßÀŒWÇ5¬­¢hµóuô˜…Œ‘‘«ğw+ñ||3fEr!ø¼?MÌ§ìiƒ¥™ buíxŸÒ©¹ºOû¼9íAfúl3òø!ª/Šçt?T@Î¼qC¢\Zh½Un´vÊ\'R¼b¸F.¨Æ~Õ-‘[½ÓO¤ ,+MO÷Š±0=]Ä=#İÚ*ıaÚ÷—7?ñ¾& R$®å\Z›è¨òÿ?\ZfwşÛA,\\-aNPÛÿpÿbş‚…À¿úçùùşwşÿO<¼„ ]Æß”?İœ ÎP$ÈÃ\nŞ€\\< ˜µ-”[E\Z	òvprYcşWæÕ9»B<? H.Ì¤æébëáàê‚Y#0í] \'W[k\'?\'\nruqò±a¾9A!@^Ö»ÕAn®Øn?˜Ş°=?m]1E>ì¸~ÕÇ0Äê?°vsÃ4eÅv‡a©Ä\ZÁá¾[ÃáàáuáUw…8À Ÿ¨¨(HËŞÁÉÁ\rdh?p?\"°5y		y9@\nxnNÖ¸ÎÀ¨=ì1èÙº4ò€‚¤5t•A(ÌÁŠäêzxºXcäÈ—‹?@W^^ÕRW^ä€Ü®y¸‚l  ?(ó¯/v?;¸ò€€Ï9À\\0uA;m	ÏáZî€Îƒº`0apÛ-Ä5u±uò„@AÎÖö<öR{J?ÌZşKÄÉÁf_£„Çqßw{¨@W  †‡=f0˜ÿ0¬„`Im‹€Z{``ƒœ1f2–uÎÖÀ¨³+ÂÀËÃ×\r\n	ƒƒ§­–%Êš²> BÈ‚ıL`ëá#ùª ¬&O@À\'üYQœ?ğ·¶˜:;Ÿµ<˜\ZÛ€8@.PoÌ+Ìg6\0\ZˆÆ€ÁÒc,E¨‡?\'Œm§{.?—«„ù¨?iğK}-Ï_ëcä#wn…-İnfko?ØÛîÏğ1øì¯ÂäxëB¡lÛÄøL?Ÿ€?\\1,ZèatÚ¯-~ÁÔ¢€€Bwˆ¶]	Ä±?†é¨‡\'Â±ö°YcfâëbíŒi¾=añÛEÜ¢±ËĞ.°ÌŞË>,Ã1e˜A’ ¶İWìêõàjË†tğƒºÂ~¾cgd#úlØV’ \r}55hĞ’@P+p8·&¾ûó‡Çê!”ş¤¼øŞ×n»¯·9ø[sOØO\0ÑøµıÏ÷8ÑÙ\0Xh¶_üÙ×‰añö;€ÛûÂ0ŒÂ¼ÜÇ1l?íÑ³í²KßÀ]\"üWœ&ÄÍFà=ó˜Æ{Å?ğOó\0Àlÿ\\À¼Æ	9À<ìO¾cDa{¦ÃşÀxÛıƒ?yc45”\r‘??ÛÃS@2÷âGø§i?Cï?†\rÖ?¿@l?f@ß„T8Ü\0„¬·±ñtA:Ø‹Pd#ş_@ĞóÂ\Zû	#ƒ¶l6{éø\'-ø­óß¡ffØ²íëòŸh°½RÔssEş7ùL¢<¸\0x\\»k%; 8Îï£Çß*Ëÿ6M`ÀlßO”ÿÛFßg¿ıÿ7ı±Ï¿°ÿÁ`áó¿ØÿüçùşwÿçäÙ¶ÿÿlú;\0_±f8+Îd!0dÂ\Z»Øú\Z®1Ç¬Ö¾ (‰iç`íÄ²ñô\0šc¬ı?Ú o(ÆTÇ\Z?cŠQt^0å¸0p?®@_\\5gW/L={ÌG?Pjí„yïlíˆA;ı\0ï‰™µ®c\ZcË#xxö™ö 6WÈÚäµFzpá\0²ÿ??ÿŸ³?{I3r¬¡Œ5?]?‚&2†GÀÆÓÛ\r?]‰0êGm¬ñ?i©83Øæ€ã…\0\\›m€ğ\0»Èëu°P?˜ÁãZï¸\nÊš–r2ŠlHvğ?æÕo/4	A8‚A±xYƒ¼]XÿäÏkÇÈ# a—3ÌRÔe†àÍúİŒøe!ó„™ò›ÊøtŞ£.½A, ó>0ØN!PÈæ\râñƒ…Ø÷¼dÃjMœ=ÅÎ†[\'Ø°P1z»şòã4êbØõ@ÌöOˆí‚“İf»İè\'L; Y6Üz´,ÿ*èœ°Àÿ–\\ÛlÁrÆÆˆ©?•€ÆŞš¹0âìŞßºaÛ‡;†~RR ‘\\¸‚®ú/(`Úíô»?(Ê.ÿö00ßşıQğşÃÚ¸ê|ÛB³3œØLäßlõoĞ ’c`ÉşÁÌÆ=;Æ\n®[À%Áv»\r`_Œ|ã@@‘N¿Ÿ\0x‹qRÀ<ú¦p™€İ#¯©\0øKÛ?ŸÇ¼$$ØŒiŒ‚{Ç· »2ü—Xìl™İqş§İÊ¿ úåù‰èNIàî§=hƒ$$@\"ÿ“}rJîã^’ı¤ÙÎ|ù–dü‚XKõ’h@·|BÿWºùïuo‡¿Ï*-ìºÆö—î3×oNÁ_(YL³ŸJvgï_<±\Z\r\0\rTûg\nÀğŸá¶¯[\\Œäİşœ XWpïŒøƒø+’@À\Zû}°¸nÇo»S\0Â†)çêİ3N¬ïgÁz~¿©Õ_š•ş®sÀØÕá¿6öpu‚ìé÷ÿ¶İ¿óüâÿÙÿŸèã_ø|Bü¿îÿğÿïşÏÿĞ³³á (g‰X¥]_aû;ˆ?p¿ÛÈˆ!è×¨şÎÔÁÙ\"l8­ú{àùçk¬zø-–\r¨\Z\\˜œ}?òÄ* àaãÀÙg¿µÃ5Ø†¾£óş\"R¾¿?í68Íñ·±u@Y`ªâæùïc#$ØÁ“?ˆÔş±NàÎ7ñß¶Fv6C8¶?àv6°0ÿÎçß­ò—Ş?ø¿ïÚˆÿõ\"…%×ŞQış—®ø?ğ#ÄÿÚpÿÓŠô;\"ÿÔhÿÇ†­ø?5çÄÿÉº†©õ—+\0nkïrqøeÿâö ø÷ú£şÓÿ`~?ÿÕÿÿÏÎş?Àø?…\0İêû%@Æ\Zˆé¹q<7gŒœc–	_?\Z°EîäÒõ€:¸`Ã`;í\0kÕÃää`cá2	 \0¤§››+Â[Ø[jYQC Ç\0¹ï5+À%V?µÄ\nèlÖÿ»	v9-y„µ­‡°?ê€#ÔÇê†??¨9 Å@8·mFl\0o›¨®„¸€+ğÕƒ°×Ê…$F×@=ö¿Dz¸\"¬í ÿ~:Ãÿù?óAOG_c;ì|U?VÓ•?ÿ/¦ü²LBÜtq\0¿»–cÉ…İÆ(`\'W;7u1îÆN¢Şû¨®èê\nÙùq€ŒƒmYÇ-»¿uú{ÒÂNÖÂÏv ˆ”Û_¬ñÛ/€z{Öx]€ˆùOIuÅÕ\ZˆÎº`päÆã×éˆ‚²{vØ°ûærÛuØ~â…¡?PÁÁÅˆÑëb=8Ü\"\ncF?Jãù+0{?(aûÓ\'ÜÅ¢ı[|¶Yãîé€€Bö·ÅÈ¢ó_4ûcòÂv]=ÿ¸}·Ûd/(¨÷v3¶¿§Ì^Äv\0lïƒÿ??ùi!?à¶^ÿdÈ;Âìö—ım@àlÿ°ÃíuÙOÎŸÍ÷í÷n·ÿ‹Îş½\\“íF‘nò_Ë\"ùÉUĞß²;)w&èvj	h¯	€Yù3Ûd»îrM¶ßüÛ™&7Ü}µÕm9Â<‹ám@ûÒ-şùgòşÏAìKcÙÏİ?e²ìº¿HfÙ\'cÎgÙ\'ıûÁl§­ì‘?} ¶3^öHÉß$¶üäûßç¶àTĞ/+ğŸ“°f;VGa£®{4ö«»JÒş‡²-W»¾è/¯±KĞü7!ÛH,·Ù‰­Š™¶NPk?§ÎØæ†\rF·—~ìÀ1†fjà¤â†i¼½zíˆÑeÉ†Õ?X|°i.€ÌpKı²\"‚pıàŞí¬œ? ¨fùñß×øü¾ê;b¹ĞÙı³cW¬w*l#øsªl7ü	(ÚY£%Ïï”ìA(ü)	8œwó7ÿùBğßdş¿Çİ}Óû¿Åí_iüg\nÿ?°»ÿ\'Ôİ…º?…é_©÷=„ı¯Òng†ï??[\nŞşš²ô7ëÒ_f_l|= H\r(Æƒì_\Z~å;Pò_`ö¶jß;;·G…µ}qÙY?±À4Ç\Z?¸†{_Híã×Ç±àµ¨0xqí­ÌÁÏşsîÅÓÛ6¸wû1Û7;1BŠéË\n¤ras0¸€/¶©µ?bü0?š`í”Ù[{aüWO;{ÜÈpÀ1ş­«›/i\rR<\0·UÈû€`[ôp™x¸Ş°Ë‡\rÔ×SE^S?ççÀ?i›&{$vg˜¿ÿ¤*néÜ–¤?âŸ’¸\'Xñú‡vÉßš?ÿÄ8ÙgÂîJĞ^Óö¶ÈîËŸæĞì_[#XiùUR~7IØ°?hû•®Û¯ó‘ğï?á–º;ïv××¿˜t˜7ûü’]eÁ€İv?v¨´«Gö®•;»5ÈíupwÉÛyÁÍ·#:¿$pş£ı¥rşô÷°‚k»?có·a\\ÿWg±áâûUKî3ãş‘«°?@@@€À|ãÂğğ¾Å		~bB€C—àw“šà/Ñ&øUGà\0ÿn“€¸wdn\'UWQJ‹._ó	„ıd,ì2noulZ\'¶p‡ŸE‚©\r$8ì´ß†f—ÚÖÍ—Æ‹\r(s°³a“‹8Øw—RÎÔØ1„ÁùU;ØàÅ\"´+@¸¯˜şşÌ??;_\0ù÷É?ÍöFÂÛëWã·¿‰á>ˆ·?Á0øöewàÆ¿‡ZÛå6 qŒŞc³ÙÎ}bT†$?X…ÈñŸx€ì2àeŒÖÙÑ“Ü »:›9ˆ€bf¤µ?SÀ´\Zş§°Ø!ü/ÇG0äÛÑ-XëW›÷÷…h77àïâ=&Û®ößƒûo–ùŸzÚÓà/úÄ-w¿u¹kûïí§½¹wx{mŒ?õ÷—½í[Jö¬5;5ö\rí—^ö·Ø¥Zàöª?S‰8Ãc\'k(p…ıí\n‚ÿjx´cQşbNbuÈÃùŸZ‚ô±e*\'n‘ÂYA¿WÅjŸíuì¿oBîiúÿ¨	\0İÉ£P‡:ƒ4UAÀÈÄ@Ì.?–+ø`æÂÈµoÜ\\»ª[|Û\nÅp›õ³OÅØo¯;‹\0¦\'ÛNcv.@?;³q?òßGÿ?òÃTİ]ôvÌÕ?§vWo ?„´ÇŒß‘ı_ˆö¿sİAÇ˜³¸P4–?\nØÔc„ïÏ(.;¤Ã1â\nEº°z\09ª\\¸Ôlk\0?Û±êŸ®-ÀÎ=&=ˆ\r×Vëí€ßá×.vì ¿P@;5~s{wñ\'U³ã?›\rÛØî%\"´§mM¼\'6½=}puö¢õç	ò»ÖÚî?k@U¹:Ap×]Ÿ[q½píRäW‰\'üsà” ü[âıAà€`Ò.]°iùv;x ±{(XfîõŞ?ŞcÌš_¤ğï¢÷XÒíœúvÿXÿJ–úÿÁç—ı$ò|ÿæşõş~LÙÿîÿÿO<;ûÿ\0ãÿ´ÿ¯ëê‰°…òêbŸÿW.Ğû-L¿P§??wŒ™àl?Y|X1ı X¢ŒÅÀÚ	»ƒî?‹½bÏ`?â¸°?ˆŸ-€mj h…1©ØvNhyaĞPåã³ãNôc@a·³w¶è1Ãótñpp4;êá?!fÍô<??a`:ÀÑw‰?“µ/0î¿¤:›Mà˜¸»èŠƒ\\€Ü??	D7„«—d{k?h^ ¶ÆèrE9~p¦»×Ä´ÄJ‘×—k@ˆ?†! ^@7kF\\aØvÀGì¶=ö0ÖŞ¦ÛÂ€]Puuw?\r&¶t÷ŞÜâÁİ¢`ãŠùc½=Tl÷˜/˜ÿ¿˜Öğ¹ù\0‰üë;@°cona¨?-pqr\n¶ÛÿS°˜€cûÓ/W\"`7Suue±g¾wûÁ97»½€0½ü²Œ›ÿÎ]	»-şx›ÁŞŠ@§ÿîaòı;éÛ şæTõŸ2t‘²ÿ©K°ı+’bÙı“/ ‚? Şvàìç«ß#ÀÛïşk÷`pÁÀß&\\‰°E‹•&Âß·‘÷ñNœğO[Ä{b?Œí?æ=<ùóòO¾ÿñF„?Qüº­¼œşó†1–©ÿäÂ¿—„u	áŸgÄ¿ºf`g~‚8œ¶§;Pˆ{aqvÚ/	ûN?±66§†bş`1`ß[´­q÷³ÀÉ4!f•AX» ?€%X3¶Éãeíä‰\rC¸:ïYn@®6p¨­‡hwĞuòš\n\\ n>à#?pEâüh\\Ğ¹qİ#†Ø?ãn`g§¢ÄoõÎÿşÙ	¡BwâÀ?+£¿º²\0çÙb[ÿv\0u5÷±p;?Š½ÈáçöÁöØø~Áy;-âOXïû·[)şKzp¿Ê Ø#5@÷J?ÓÎœÇüB{ş 2ûF/ñ;gÿ=a·Mşµ†Ş¿YB°³[²w_d÷‡½?©ùÛzşÿşg¯ÿ÷Äù;ğ¯Ïÿ€~;ÿ#ğ¿÷?ş?<8Üó¼á¤Ëû\0`Sï8Œb€©\\¬T¸õZ0±µ\\ìô\\ÓŠm§\0cN98ï5¸0<Ä¨†í¶{jm”Åú\n˜e·èí3ß\\pŸ·MÂÿË÷8(ÊYêêşÅU{ßííù7¨|Ü{\0yÇÂØ(Àè$÷ú€ñÅµÓF|o“íş€J@8êï¼Ç|ã–Ì46\\y Ö#ù¯rä?¸âì¦ò„w\ZqáåßQŞÉürpŞsÆÌÁåw´p ÷GspÙÙşâ?.gñÿïYœöêàºßÿÀßë°€ ø·øæù_ıÿ?ñüÕÿNùYHœ=# À®!®	òÃ‚Ã†„?P¸³?=î†Ë±šœs€Q%iyK5e-\rE@?sÿçÜTß™) _YW7_„ƒ?½6XRD@í@:®P\'W;={Wgk$H\'i8`@0s÷–$lÈXoœpì·,;ÀŸ´æf… \\İ¸\0›ÜÍÉÚv;Œ¬^Û-`	÷w|	·ùóë¹ W¤ö4v³µ‚º0œ“ˆ±m¯•„ÛË\0v¡ƒmoà²ïY.?¥â€Äæ~\0}±óàˆ¢	Ä‡•¸?¡ A0–€gônë‰@`šcêlÇ=¡ñí?*$×N8Î3±qcqÀ ˆ\0Â?.¨ÂmëêÄF??¤fØh$P€½?\nˆh¡JW/(Ö.ÁîÏm“—Àè‹İÍv¯±·LánÌÀS±øóâ¿€ãíf\r \nr³vî©a>#|1]a?a±à?Ø¤?Üì­WÕÅã]aGˆñ‡\\P;‰b\\s;ÈÎ¸€×Ö˜ƒ­hÆ‰$Ô{X‚‘\Zkggëİ@í6 ®ÔØ°öOº¸î=\\‡£—³\r{(ÆÅ å\n¸t\0çÙ¬ANÖ\0_vŒ!ŒMâ\0ØYÛlSâãn\01ŒÅØA˜>€É\0TøNÄ½WåbÄ¸×OE]ËRCSÏRW_KKSGO^î×(©%F‘`¼HËí«‚±N,¦ÈSÂ½‚¬û+ıŒ‡şÒØ‚™z*Ø2Üa4qÂ_‚?˜÷òÀtPÂPŞ	Š`Ã¨K&n à³›‚û°\\_7?3ÒgéüÒˆc»Àíç=vÿ`gZbŒtÀhÔİ?8?:»_°\'ª¼dÄEs\0Ûi\'¼Éb³Áh*oöIÁ¬m=v¶1à?¸ë?·ç<1\'[¬dà\0aä˜g˜.;õ0z3B.lühçz8àÚ7ğØÂ?ÅÔaEâ€`øe\r\\‚E3Å°´ãÁ©I$ÔHÆ\"Œ•b`+E$ÀL`G·llk: ?}û <°»N?	…´v†bÏ­rá¦6¦†?§5f\n{lï^`sU€kŸ1#ÔfVàNéÁ@öSŞÆÁ£vÄ	£†¶iµ³F`†€ÄVô¶‡bId)£‹1ß5õudå½cíâPË[±Ç¬lØ±c·OØq€l P—?K¤y¶ÓYv4*fıÅ?‹Xd¹1¤Ä?fí?!.²8RŠ?˜‘Ø@Â\0ƒsòDÚo·Å±?EH˜ÆH¦ÔÃÛ\Z(cÛÚİXôŞê»!éİµ# ŞPÁ·ïÊÆ0ŞŞØ<qÁĞ›îğÛ¶-éŸ‘7ÒÓ‘W—W3ŞÒöXp]ƒ<]‘À¨i`Á¬\0P„³ƒvO{Ñ\nÎîÿu ¸åÍÁƒMT”}7`¾#\"?ÇÄ-…ûŒ6îœÌÿ}’ë@­!ØsGšà„À\' ·ÉmûH/¶–ƒÔ›{kça?ÍÿÜ½×\0W	÷†ô3:ÍNH@°Ã×}üØ—ü/ìÕ~ÿÄ¶ïøÏ!¦\0ğã¯:ı×âöµÒ.ÀUÆÛxîñuÅ±ßqøıô\rw®=ŞnäØşK??`¿§Gğ‹‡·£…·\r8@)ÙXãö?±»§Ø[Œ‘µ·v¶?ø0sØÙwLS‡³v‰º\rHxÉÓ£Ò0ŠFÑ£#1*ÙŞÃÃMŒ—×ÛÛ›ÇcÛ9ó`&¯›§\r/†\\Àÿ6®®<öÎNËi{ƒêïè¶oÃÃÁ¶M=`JìˆF•Ú™Š˜‹ïşÂ Ÿ/0í]İ@Àßıå˜m)À?Y €¨ØC‹˜C#olßö	2 ‹ÒÁÃ‚13í¹p¶?%°`sáö»ëûıguOgËmCŠûkaıìÃmÇÖú¥Ü’OÈ\rg’YÚ!¬}-v6âûÆ½ıö×B¬ÑeùÛ¸İ\nnÀ¸,wsöÖÙ\'’û?ÏëÎâpu2Æ?B/W\'ÌDÁıÔÂ>ãQrû\\Â/U¶Çi‰İ0ÄîªKî9j„UÀê@^ŞvÆ%–±.X#\Z°^Y=¶3·l|AOg?“§­#ÆBéÉÈì(gE1ŒTÕ··$·¥u÷Ä­ß8Ğ~Ğ?´„mZ\\Ø?âuÆ¬ÆÛí¸@ç¹~?Äş_\\ âŠYIİ=0øØÚC1w¼Ü¯9l_7¸XÏ\0Ë!qŒy:Apën±ÆnÔ#?\0éÃÔwÆ˜ÁÛf²Ó¶®ÃˆÖ*ØM=qÀbm¾ƒù®RÆTå‰\0’	Ãi,?|@¬|Hà¶ëm«ÙÂr7DkÈFü#3vGŞ~®Ë¸4\nKà¢ŒíéÉ´Æ¹¼–ò:–ºz:Ê\ZŠ\\ –ßìÔ?5ö7u7Xv0Cş[½b†{şb?b—ÏÂíBş5?ş/­š?¥ßë?€v¤~ÇÈÁf^à°ú¹ôÿÊla áöÄé­ıƒÃ–nn¯½ƒÃúg›ÿÚ\0Ä?¢\rt?bŞ¸úî£8Ë®ë°?¼ÿj´8k;G~fÀnÛÓ{pÀ¦kƒØ0¼ÂR‚w•³ƒ+›²µ3½03ããngy{[úÊäÅ¨K7”ks»a|bnàG¢0sØ›ÄtÅ¨4@Uí\0bØ†™œ@„#DÜÖŞXç˜Ñ (ÄÁ&àò‡qÎ8ßÒ®•øúX§gÛcDŠ©x·.Ü@™?5F?ìNs¬>Áİ!m½ƒ	ĞûÎÅ3Û¼Ù­„İÿÉ$Dd¥ß¦Îšdÿ7Dj›¥Ûğ\0%0tÁ`õïŠËğş™Ù1vw@cPÁêKìÎ¶S„]|×ĞwºõÅ^ŞƒÕÕ\"{Ôî?Æãv\0bñ„¹ü·}©ûÎR±Ï¼fÿ¹ğbÃùÙtgpÛa±\0‚NØ=àÈµë¶daWó½Ø\0¶+ğÛg¿ÃÃĞmÛŒaÙ±cXö˜-,{ì–mz²ì7_~™§»r²›h Ä¾?“Xrcv7ŒÕò‹\ZÚM«ø	@Ã‡ßš»a$#Ë?[ãfÔÇ\rëŞ»‚ø°\\Á7¨Ó.A°iF»Ã±`e^VSMSÇR]ZWÕRZMKIzß?¨%Ç˜Éìâ\0¸qÖ{\\ØœÀİ¸İ~1şeôØv¿`[¡!½\0ëe¦{QÂÆ#â­g¬%o©%­&¯§\'/ö³³=Ü~àî?Üß58Yö˜ŸÀz†5ËÉËè+îƒøWÄØCR Is,¶x÷üÊ>HÀóK§»?ü„íwÇNÙyvV`@^ÖN??? Š²†‚¦¥‡†î>u´fûÂ$Ë]8?…?ñ±ÄZh»…;vé¯­1^øn~°®éŞB áo-wPû#/°­·9±ıùU¶ËŒ¦dsÀ\"Ã`„™!?İˆ“ÓáOÃŞ¡ ¶š©ƒ9¦?üW5?‡“s?™~Çc/Ü_ÉäDıel£ßèíğ÷=ìÁ|—Ø‡?ğìåÊNû¿îe­½­ş©À?¾ù½ô÷\\Ò>”ú™‘ô?jó?v†´Ÿ’æÀIÙ¿<ÿR£aØqñ~Ün¦ÎßP?‘ïüyfæ=^?­ï_ªÀ_ıà>âÿ¹Ñ/ÇEÿ	Ä=´ù˜ÿ‚eû¿Ù`xá¸g‚ÿI+êH‹ıË\Z¸Åf¿ÊŞ{†8¸;›+XCë0bÇ¤½5ºŸğĞšl;0$q\ZJ`ßMÇÅ(1m¯u÷5;;ûŸü€?çßô€ ÄÎHş,ÿoçùÍÔû3ÿZØ{„ø\'áşÂ\n_I²g)Ã€ã“?Ømû+¿ioù·õö/5\';`!Fos8°ó²íé’›ïO$Å½Ã¨<îJ–Ÿv(öÆ€=E6N\0êğ_ıİ\\Øõ¥ÿŒò~¢\0ãüÒâ¿6¼ßôùß\rï?drü«¥o@?eïlı“}?Õ¿ÙÖìXcÔŠ=Õ…Á ¶¿ª‡?g¿ÖÜ?·vú¿(ò;6¿IÀ?‡ø;0^ìO.A·ÃZÛ>!w>!nÌˆ±zT´íì`$e§«‘¿cÁrÆà\rœÓvö±ÁÎ>6HägSÜö1vÑsuÙ‰ô9ÿ	Ãöe­Èİ3?{†	 Ö4äÂ?üqu³vÇÖùÀzù·oWsgƒ~cìùYÀáÜ[»ÏÎó\'€z;[à.®Ø½ƒ­§“53.;à¯·5°ÿçê‰İu@îMoø#v@ p3Rï!n“ˆ?â(\0Íp±ì/\Z‚?®Î„gë‰ôpuŞe&°I€İyâs?ó,¼±CÀíåıàÎ+ü	ŞŞ´à÷iq»8©Ù¡#°QuvçØà‹ËŸ mN?-Rà´>?µ íùÿaïJàšº²~Ä?€(Ti­ÜÂN—‚P±¢ H«UŠò @–€¸TÅ\"µ‚ÖªAZ—ñÓVç³j‹ƒÅTÔ¦J[¿ª£X«c]jÕëR3wy{^@k;3ı~ÍïWKòî=wy÷sî9ÿs.\nVÂ`èUb,ÌÈ\\\"è7ô)‘¬\nòåïº\'W(&??i_Ÿ\0ì¨´VşÿĞ%ÀHgªDbğ÷#efI:4Êäà³#e(q‚E¶D@0àß…e\r?%ämeüı±H ¥@€ş]˜D»\'?ÇáÓ\rí©F%0›_¸¢;Ä¾œ¦]We‚/	¥!eÉìáE\\Ki´Ã‚aaØSc*À‡éI2U˜Qg”`WÈEÊ}ÀıÉìä2Ü¬„¦\0µ¦P–h!ò±!ŸÂóà¤…P	QË/TjÔ¥Ó„Dg)ÕÚ\0:dV©åK½A—§ÒŠè°!™kÔ!ø!Ù”H=3b9¨WÁœÂë)áÔalœ5)§o’B-BãPş–\rmşP$cQ„M		Æç,{Ëš·¤G\n­Sşâ\\Ş½Å¡?|?’´zpÿ‚\n` 5?€7^\Z?4Ôåª´??T\\5–SH§‚fL$»²Ç%Oy:ó $e—‹2.uğ†t`YjÚ1èÁÎ\ZôèÊvdd¡™½ÉTç \0¨/û]£4¥3àìãŞ\Z˜¾bL?í»e{)©?°}T?i‚‰ò Ü6Ô<Ä9(³0ikN3…›Â„˜Kå@e°(€é`d?âL ôBV%¡Bòô,£PÛ©CgF¼á©şE?áßş#%Ãdö¬_6çÍğö-¦ğC­qPÏO*æ+	–È`}Î?ÕAª \0ğ8h˜=áG·OîHıIõ}i·ûÔ‡>Öfš¨MiWªÖ¡ÆÔ<¬Gv]X?°?`N3M¿…éá(€j?PàĞ¿Š?…ø ‘Ê¾ğ!¼@\0 “+,ğ^¨ÁÉø¼MfËÛdôğeÃåö­-r>)¹-)9CJ. aPÅ|R\n[R\nšT˜À^A³ïÔP\Z²\n!¤Y\"…ğm’í€s	t-Cø¥D)8?±)äÿ@ÿ`ÅRÆAXğºÂaa²…ˆ•Ùç\rR¶ı\r£A•H‰€×æ©Y5ë°6ş›ª?¿u¸kÿØøøóË7~‡K	-ô{)ÚH<Á³7~àDµƒN?éo×OÃ4èU\r–\"™ˆ*«4x‹vÈÃòL?A¦~lÖAÇnROJô˜ƒ7EÆ{ò…?ÜOxÔfúÿ›â1èAòÀ†Ì0YãÄÃcÛğDü*†é©üzC&Sn0¯ğñ MÌÅH™ê\"u&$r&(Ç8X€<eèhâÕ¨JË;ÄzrH¾“˜A¤øä°çŠM|z¼³œµèü%9~\\Œ+¿ ÂÌ\"fS£ğ¸Rœ­Ë#—ëB“;–!‚\'’Ã\0P-S§Uys@R[¸³f.÷%.èï_qo?-ıBxÛ/[,ä3îÔv¸”Ô\Z˜¦è`”ÁHöá\"Š.\"`°Çß˜\"4ø©Ã$œQ|ºš79!zL\\Ztlô„pŸÌÓ‘Ø)¶R£4ä\"3¥Âù@È°¼›\Z˜}ÑêeETÀ§hab°Å‚xj|6ú=¬„&DH¸.\"(…Å?”„m?KÜ‚P.\"‚6.\'¦ì/¦%c3>ºóÜNÃa@g\ZƒŞœí0ÈQ3?ĞÚÂ\\ju\ZÉtmp×`õ˜z¬İ÷¤–n\rÃ.aêOÄ)}2Õ\'	±D\nI‰ôhAj”zúÅ?)·²yéhŸçª,h‹70a\0Xƒäq<á\ZÀ»Åü#\n¸ê\r~@IâÚÊfS/ÅKõKß8‚d“¯??Â!,UÄ \"TĞ¾&&1Ğ?`¯g©øR=ÇÊ¸tp¦/Š°RûÚ‘\r¨Dv¾Yœñæ’,ŒA}±~´áyÂü+RÜP^Š~rPFË¨Áq\"¬|T\rU.Š	¼ág?¤ îk¢j€ÆùıàÜÓ„¯Óõƒiğ»ÃºìG\ZAo<û?B¹ôùF?gÌ¥èünÓ©ı‹¨¹Fü [Š/Œ…¦Ú„Â‚R‰4YIÀ<[D‚8?Ófù¢ ¥–S—1ö!tàĞ0èt¥Ã…lC…­\'â-\n2<ˆ›4„™vv¸ÂÂ‚ÃÚ‹ÜAŒ‡JFó;æ\\ÔƒæÆä–dfJø	Ê9>Têõ`COgqŠÔ(±8Ø?y¢&ÒĞM…2‡2ÁÌbõˆbï\0²?0œ‹6ÒrŒÓ]æ‰Â¦Ú‰ıá)å¡•GÏ\nşyŠˆän?¸ƒäS?%¿<¤ä1šı/Œ)áª?…“Hx8æÿ`<	gºFJ¯N½¯à‘”ğï)\ZÂÎ`‡ğL¶£Š?ÀÄ„bHöÀ‹½\rà…¼²´ÂI½=‹Ö?xP\ZNú´?6Tœ­ÎÈ†v]t[0<MLLœ¤î<´± ¤ËÜ˜õtŒ\ZIS$‘ÄbOáP# Óã%CFÜi×j‘Û£^¬,AY„µ::¥/>€CH‰	&Pä•P°è9Ã?ff,;™€ÂÎAipğüÊŠê??$J=É€PôL¢HrƒÊhDù Ú\\ ÖÀ­2¯\0y<ó”†,¿¬Ziyª\"UÓ—WÓbâ’§¤?Iœ?49.9y\\âD^_`z?¥?1Fl2Gª<THP:?\Z\"EØİ„»\0”§&–¥cz²°ñĞ€ı‘Qc„q‹Ğ	Ş\nTÔU\nÓ5ø‚£ˆÔöFWäĞ?DfÙÊdÃØc¢qâ¤€e#c%eeÔà—‘‚µyÖƒT°‹é-d·*|ş´Uãi””Fg	š>Fi~ÚyÀÉ LPŠH]E{J9§ø‘’l _+á¦?çh\ZfDë!vÎÇ|½ã‰\Zœ`*Ü‘¿?ÿWêœ@‘&;hÓ5Š”û\\8?ÀæÅpßÕYöåJ6VwFJä-ø…:aÓÑIL…Pá\nr»ØÁPì\Z¡L\rÁçTtÏêÂµËŒ²ÁH\"y¿B®ÅeıöâÃxQîŒ6LvR8\n‰¯+sKÓÜ×‰?{1:%a\n‡o²°ãÊl^=kÁ S<àâF†ˆò*¡´îÈ\"ÁQr“áØl6üÃ#F`è7;ş¢«pĞg”ÄXV(è–VÎÒF˜–Ÿå«âwW((m3´ĞÁ¤Äuôîµe]0á]B}l6~† Ob8»›–-ÈØ[Ë.D•\nI2*r©R‹±ı)|dœŸc:eY¿p4-JäD½d:¼…\0J4r*©ûõôÌd<9³¤±¦N0ú	f%Vkù4êœÅÔf cŒ©²ã:”g?m·ì¸c1ùo†Ã’Ú_Q¼Iá!ôgDê\'ˆú±úO­*{A?¼`EÈn¨¥„‰®°€öèe«”™(18ˆHQ6†UaL:æ\"0.Ü—}ÜÆzu‡D\r?Ïb	·fåDƒ¯œ•??Lÿ\0ş¤î«`7,*K·‡,=ÈC?³Œ(l¾\0¥Ô‚YŸ\n	BEÇ¨ë¡•Áô2Yš­û‘@u\rV÷Ù™óºí2=u5Å0£p‚5‰,HÁ`äı$0¡»!\0;¿h,˜æ@z08”À·BgÚ‚m&×ì<EŠö–¡Aà[ÚôJ#	àg½ä½beËÈTCgZ…³Ç÷²0£@#Ã¦o@Q™?Ò@©?ÙÔõ†Gª·Ø‡¡\rdÍ™ô)%\" Æä?ÆgÄÊ²QePë\n?8Ó™“ÌdËÂX»?v‹JüØÆShïãºXùW20şT˜g{Ë¸Ú—€0ª»ƒÃn¡§DÔq¯iÂ×Ù\\Şó8®ª«R[?¦?çé8OQ?QnÃBsÚ“Ç&p¨“Û’eïmçN°a¶ÇÄ–?o„¥Nóı\"¦ËY…ìİeÇ7,Pãæ³Ê×g0B3˜“ŠíèöÜ©6`X»Cafâ‰Â¼ÀˆeAaAŠHh\0¬Ş?øJ´#æIò‘˜è1ã½Ù)Œ¨¶À\Z?%L =+—,¤ùŸÎÖûëXùŸå:ıo’ºÃüÿ¡6ùŸCÃdäşw|ÚËÿLeñ†×{?=ÎzBP“Iò$µ¢ Èí	Ò\'\reA@kÁ&Q˜XV\\^«†®U¨×ğoĞ\"³å0#ô~)?ÄéJ#Œ!@ÚŞédª–\0êÊ­Pù@MB§y?“˜¼ßŒ¾À\r›C•T$[ ¾Ğ\0£Éò8¥&v©µR„2deàdüüÀ—\"Á<ƒT\"Â\0ä!#ï/‡UáqV?oã¶‘?)F@\"RBí84MZ¥Fú/g¾€\'X>ŠÅ(_%„ëÁ»ÌQFCÚ`CEÓe©€¤!•DçeµÖN»ã´z E¡—DßÎ†¢Q¼…L?86SN&C$2òÀR‰QËšv[¦ß	¾QÍ¼pÓØÉË\Z¤²˜5Hè?n+±°€&é&?Rß\'Öİ\"9´X,¤j\r§bÔ–ÇœåF&GN~¢Cû?CşË/0¥ù?ô<«µpÕıêm´ÏÿåŠĞa6÷¿WüÁÿÿŸÅ@³qqz®PhãÿŸÿsìş½snÙ=‘¨Ójh\0_„Èª\"…™PX,\n7ô“• …|,V³ø…\0ÿ·€/\nğ\'!2‹Baµ(bAYA˜‰ââb‹ÙlµšS`ma+!™	XÂ\"RX	EJJJ1¨® Ìf>$ÌV³¹”-7nÜ°‚FD\"è¨ PlÕ-3aµÂJà/QŠ¨Ò„ÔE\n‘E$dÜÜÜè«Bä†h?Î›AïA×Í[¶l¹§£Øj±ÀÖ¬V…ĞTXÌ\nÔ¢¢¢ÂL€ÆÍ\n3 –F`??‰ÌføÜ¢PXAi@?c$,°8*ˆ@?¬Vš Gú`?Ó	úd%\0!Ğ‹ÕÒÚÚjQˆÀ}||`’\0}‚°Z`ßAgÍğ?¨¾¾Î²ÕÒÓ¹xi4ª˜jSfUeœ~yyÿİ+ŸOÎUyï?<õ¥Øùú}Ç÷[ÊWoĞÕ?‰ÑxèÔŞJçàÏ>çÿÍ°øî>Êã!?®Ï¹}vÁòÉ+ãçK.?ÿÖ²·»¿…(ÊÎÚıÅÔìÓa?_üÉÌİ¦Ú–M\r?ß”è´¦O@Û??›î>î6÷’.ÿT@ï´\rmŞsÓï|y}ôôÃuy³î«•kûµ¥Ç-r^tg~Ú™ ıwï?ØùÙéê‚?åæŞû.ÿæÆu½¯L/15>uLI×—İ›»Š:à©½vaŞ¶¹ïß4ê\rüH1ëÕêyo‡XÖœ;P]wtk‹Çò=ß˜&?lØwnÛÎ¦’†ô8å²ÍÙ7¼ëcŞŒ¯¹ãéëéôIeRíëáNïZX¼çòæ\0÷×c¼Ïû¼‘xe¿:~lİsNÛ*[®,ÿ`fS§¯wßµ½RÿúÍ»g_=5m¯8wÓÂe²d7Eä€àÍƒvHW;Ş_µ®Î­òİ–/sRRÒµ^î73¦ÏÈ÷‹q“øÓíÅ>¿õuÉº:g}·²—:mjiVµæÕo.??¯?Xéznhyf]—?ÅÙùñ/ø^ã]³´µ\"ë~Qß—\"^¨ùbAr¿ÛÖ·LSöûÇÂ¬È5c§H\r®?ßÿæ‡î>£~rô}Çô­Ş«9î³wGëûl¬¯Ú³oš÷ò‹‹¶&»?”Yyë£²?\'Ê‡ƒ½ZÉİ\'ô@ãÔĞRe¹ğASêşª³gúø·87‡‡UÉÂG_T^r]X}’HÒÓ=ÖùÏ®Ùâò?^w#&Ú¸ı¤×++ƒ½õğ?]mÕûŞ0½÷× cNÚÄh÷âº9kda-‰÷×¹šg¾ÙÅÙmµGÄ¼?‹]‰>?õ?ŸùOØÙ«ÙÃgìZ?7âÖìî71!{èÜ˜9~{%xKÛ\ZÇi?ùF÷„ôîg?÷œ?/Ş|kÚœñÏ¬mÒ:§®îÑ3z•êàÇÒ~’gˆÎk¶,[drh’¸Dt×oşña—I%šMâõSŸrèğ·ªkk2ê_¹àÿÕR•sSÛ‘3+/uºàçoş\"B,_`X“~,õSeÓúŠcÏl^Ÿ©œøµüü {Æ,»å>ààì«nÅey´ºÏKR¯ ‹—<ou)Ÿe¶„É#¢js—¶qoÈºâuW\'mû|n×nûï&õpê¬½ä7ğÏ–?¯¾|òÖ[K½~¨ÎáıiKÊ¦º&mQæ#Q::t±GÜÁ>ç’†Öø«äŞÚï\\>}kİió©ó»\\ä=ã\Z¶¯Ü©ß6ùPé¸£—ï‰Èk¬Ø>sşg›.ÜŠÙğşÇ>ïlvĞ(§·h&­ÓéßYªlÜ>ââŠmôİtŸ+á¢¤Á”°sš4×söe·ôÃvKSv\Z”r|®øÅÚò~ëƒ^d.t6W„_¾Y_îŸ÷syh¨wQSM¯üƒ^gš2·F¯2½Dä¯ô˜àğ¶,§ĞuxÖÜJ·\ZóïÙÅ‘×ûv‹rıqÔù¿lùâHõ*ÃfŸ˜\\cèŞ¸CWòä+dë«.Ï)õ:ÒcãhÅˆró§}®¶œè[õÉåÿ)}/èfÄCiHsÏÕYÏm˜ß­é„»2 kÌ®äÆM-\rªæçó_vü>\'JòÕÎïºxTVÜŒ|¦fÓ²Â„¥{÷[<s+·—Èúøí\r\\‘p×ï˜{’sÚ—q+.d%5ÿ ŒşGõbíz?Û›1»>N9y´z×ñØ?·ûì<ç±IsÖİ’ÑÍa·¹&ÑsÁk×b­\r}[ÍÔï¨\rîVøZó?Ò‘}Gø{÷½U“ÿæ‘|«_ïynï”›‹G/yP¿nÿÕOs½>ôá7#ª¿kÙğ5†ËÏû-ˆŞ3¿ö²©‡GËùkš¯æ/??d\Zº%xı—;?Jp«}éqşØº>G\\]]{ïø¾àÆ¹]WNºœVëôÊáşïÌzënï¼Úšò¶³o­Ê¾öXFà¸9I1cNè|R×f~ÛËã˜gË¹n]ÌË¼¦dl\Zq`Ü€ÈÆ+^?¦b÷É[s‰!¯şıZukµé™Û•ãwæxÿÜ+\r?)GNİÖ¬ÈjŞS°mlMÛŞÜÊ·Ò·/4\'?Œrö¼éòpyëÉùâ8ëÔC1g/yÕ®HN÷G\r‘/Œ•«çL]¨!NxôÏ²Ö;—¦g]+ûşa¸kX÷„¬)¹fµvÉÕ¼¶;İ.ézáƒAı:êãˆØ\Z¿t³ÇÙ†+ú”¤?¯MxqŞ¹Ã/­?”<GúæÖíYQÕ¶GÛ[#î|PïöÀ;ú‡F\'‡·\ZÄ.µù©Ûº÷[]×àãß¹÷\nqœ¼Ó§Û5oWxnö™Vâ\\5#\\õñY?À‰“ö†(¢JGõ?:ú³¡ùÔÁ£eÉ{¶æŸbZ«²‹¨¾Ö»ÄwšU#öXw*ú«)‡&Æ¶f._iú(Ø´|âE_é?>şÉÑÚá‡õ²ÑsïÜ+»˜ı§ñYQ>k,.û.ò*?~Ôpæì»?t[;¾kµ“×ªÄÿİ²zeÌ¦KÇµC#Ÿ½ışû•ï]©ÓdnŞ?WsEä<z_¸éøõQs,µŸ7Wõ{Xqã×…K>şr‰ºÍ4w‚º\"@Ôç£WGv«n\\S¾7B¼!Só¹_îÌÓûj\Zÿ±!ˆfñŞÄÁ+·sîeoL=»ÇĞ_ı££Ô<yUï9Ä¥É3ndOüP]Óÿõ–K©ùãuó3?Õš\nßõá×Ø{zÑk·W‹«§»çåT?XÒğSiÿcÁı}+Ãfm~zÇä[‹ª?¾öÌ¡OÊŠÿò|té‡æ¦Î{{ø;tJXzù_ì}\\Éò?ˆ’€¢¢\"® HZÜ0³Ø\0AÌ€$	J?ŒŠ3æ,\nFÌY#xf3*¢\"\nŠbÿ3Ó5Ëôî½w¿wŸÿçÉçÎŞ?©®owuUuMwíttB6…w®>]ŸÁß:Å±§éˆrõêÅ\'÷©ïx^ò9ºòÊ²¢?÷GœF½vŸÀ)~°(ÓÚwÿ€ğXûõAÄšú©Ã/Ìˆ(ùPõ?JYÖe?ã:Ïbùá]:ıCÖÌ}şŠë•=êGs\rlg-ˆ.	zü}Uxáá…ò€v§|şøªı\r<üÖ8õ>{4Ú¤¸di?;}ğœ­F›ÆÎd_\\ªvÍYz}à¼³ıZx˜=—Áò,t}œ¼äõ¤Á6l¯=,~&?²Ë8°ä²ùƒW!^Í;š|B5ÍqÑ½³ï,G-.<(3|öj—x†Óa‡I,3ñˆIÛ,œmÿá÷N—+M¶>¸rÜ¶2ïa±Íƒ×/ŠÌÍŞØ˜\\¸ûqíå–áCvß­0Øœ±ØıñvIÛ‹–‹nïš²Ë¶ç!GíàÇ¦÷‡?Ø[&nQ5pv¬› Ñ [ÿ#ƒ?õ-™û,ÿUQHë\'“sdÏï>?¿b|Ìg?&f~TO¿qÌ\\O?İÁM2$ßJ¿ÖéŞƒı—ùƒ2~[°ik<ÖşÀ2ƒ¹U:stî^µóY¾áÄŠ¢s5&[5Ş?àv8ûHWÛ~ZÅ?Wu¡ïe#«ªso\'Ù7©ó—o8SĞiì…9Òs½³#Œb?Ê?,Ï_{±nŠÎ‰÷_Nª¨-K}Sl¶`?Sç’×#¸	†W‹»„f¿ÆíŞ|ø¢¶vO8¾ÕÎÁ©4ùÄíc	\'#ÔûŠ—TìJw1ïàÙ;³wõo¼˜9ëhÖÌw3Ö?HêÿmÊÚ‹ŸgînµhØËâ\'å©Ç^]›x|Á–î•ßt^·Ÿà?”“ôÅÍµü•IÍÄwµ‰?*Î]-m÷ÙwÑ’y7:=‘~è1uÉú—[ë/\nvëx±ÍÀ7öOnfŒx¨š}v¨NjÒö<»{>‘İƒÖNò´Y±Âvüøü/EÊ¯ê½{ûı?ŒGÍ¥EËçÎ°ıı¶åÅ\n¿¢©F3lÍÛ{Ïı*ßø›ı’^fÕ·ú>ï?ß¼?—|zÙÿîìãçÙÚ»»Ì%ı`?ù—ôG\"Æ|ÉÓÈŠúØwbÿƒdµ?l,miıÔ>s©8eâ»ÇŒ¢ìKc{ˆ…ËÜNíß:C ca6­Fö¤?ú¡—:Ï&Ç,]qíp…@³Päã]W}É|Eï½	z*“_¾ôp§¾él_Îñöc£[Íìs8pĞšÕ[¿¾’dõ±ˆ»6iäéÂ^?nİ{7÷û—Ú¹[Fì9şÈ4*«L4i? Jîz3duuuõû·E“FL}·Ú·ªoòó?Ú¿âö?ÜëFÇ\Z×x–ö„9LÏú”U½Ğ˜™np1å²éaÿÇ±—,UæGzv	ÿãØ`¼÷ıÏ>«ÆmÈ\\ç42¦Èòõ€p»i—Ç:>Û³àƒª£ÊÆ»KG¿:RÒ2Lu×?ªó!×†Å\Z£†-Şª½ÃEmÔ•lï@~B§i‰\'¾üaµ`ç a^}}-«Z}îÒºã;ÊzŠOÍ+L>µşã‚Kì,İ…Ïbô—n—ëZw;ÚÍz°ùÊÑã—”»_˜y+ıŠÚ¨»:ÙÎ^¯:\\Êvøé@éïq+Òr¼µ¶=á¾öÜ¹oşµ“ŞæÆÇÙt«ÑıšèÓ\"­ìS„ËB£IQ™=gY´<ßÜ7÷é¡•{,çä.¾?Ù{_Ü;Æœ¬ÑÂ“×\"Ù÷âØäùj—_ûœ²³{”»ÒÄ°sçÊoYII›¯NäZï*¿*?ılŞ™´kI{bŸW½:\\±Ü¤liÌFÿ#ƒÔ?T[ûÎ¾f0oC°–g¥SÜ?»oÄ©,ã÷³m}ï.ïxëĞşÛ·6Œ˜œ&r}?Æd?k&ãô›ø½‚ıljĞlõ¸!Ö}Ÿ]î¾o|i´skû¬g—ãÎ>xf| ìıÇ?\nB5–R»!¹`X—Â>¯Ô#Ü5\\c¶?¿:1U5õTÈê}‡×\rşãê‚å-oæNSÒ³sX˜< tÛÕŸ¦v3	4?¼R[\ZÏ)R?9y`ÉÖî¬A%*½;u=?…Ù.*Ë9³ê»Ëh‡;Ş?%w³&”´~²™×mşØËá³û^Ö¬İÇ3?Á(Ş´ên²?›Ò\'­çÕâVÿQ¡á£*ÃÆ<Z³x®XÆ>+fÇö½8°Ÿù¥³¹oø/çN<¾½p©jAö	G{ìÒå›½zÛÜoã•:sÅ?’âø6ß÷$/I=7òîœ³¦\\gx_¼~ÄRsöòM2‰Q—‰‹w&ª¥„ŒÏ:¶|³mn·ãVF£¯h¼;:âøÕÙ¼¾NûnÆÉŸÆ\'ªô7YìĞõc§AnzÌ·q‘“:tuÈãZÚ,^•˜<`—?o¤óûPû¨ºêÚ›×ï7*8Vşå’WÌ‘ëµq†w#Ş}ŞöĞ·°ğrñ•UgRßŸİûı½¾×ÅU¡ecúM¾zvŞÅş7&ú¸xª½@õäFØ¾Òn«õ}fÜÌ9üA÷sÎË÷…„jg,q|aÉıuw;³\ruç3RÔ_çöz”˜ÛúëÆéÜ®&Úƒ’Ş×,-öİá]’5øæáƒ=]ßGwõ-Íª§†n5;¯\Z=yãÇóôr?Oıx´.k÷´¼“G“–S|Û¤Z\rhïô¸ÏW·u]ö²¢ã‹f]¹´xlŞY}í‡w¬?7Ìa—¹™ÕåûÚ7u«>¯ŒÏØ¶ÔiÍU?gŸ¦MXçdñÆ·[ïİˆîİ÷>[óèÜà˜#-KÆë}1	y<Ï5øş‹ØÎw\n+Ã®UWœK?Õêã€Õ:ù6zè\r´êÎX®ÍfÌÒğ1JZSqyİ:n\'ó}‹wÌ‰sºzûôµ¸šãî¡×:Ÿ’+¼6 ½Û™/z§´\rBîŠØ÷’Ş†Ûzs…ß^NòZ¶çvõ²–ìŒ¹­´Ùæ”^ö×oõ¹fìóÅ¢8ïä°¬–Ë·~ñªÜqçyåsì¡õµdW­Â…æ¯«M›ëª•vùm\'UÁêÚÙZ™?^KÙ›İïç²êë%7Ìl…úö‘?ı7™‡D¬ÜÒeÎbÓ0]¯œ—Ï-÷&|ªş-)²|^ÆkİI6î±s¥Ó†ÏKØ®r¿d—·æç×?¦®=óÉcÁ‹½á\'íÌ;›\r«¼\\<î~®ë¼ŠŠu+lï^É=™}Å«D3+¯zR¬CßNŸ\nÉHê}¡Ó{şšu‹ß¬Úò:ìøÖ\rª\ZÂÁ\'Ëä1Ó;úÏXòA,;Yı,ë…e±ÛU£BÇ“u¿øj»›	‡ƒ_éÜÍÉyi‘ãÛ‘7OãÈ‡‰›ßõ3ÔÉ™öt_¼v…ŞU%£íHá—ä}n·£óz<>6ıuô‘7ªÃ¦Üiw¿»ö—+Î»W=–ÚN<T,¬È™““\\×e×±ºå^_Ñ{YôâVÏâ­j.jR¿Ö:!OŞ~<|Ó}Ëò?Ã6“:ÏãqË<}?¥ÙïÑ?î?Ãü´ÒÕÇıÖÕº)u;†§Ìëîé<iã?îmî¼™”$í­Qëp?*±Gİ,Ó­]·}Œí\\õ¬?ÿŒÌ·7Ü*¬=?{¯ï<#“õ¦¿~o‘ôTÆ»Ô™Ì®,ˆ\\Üö¸Ôgşoe9ãS[çõ}3?ÿİ}}á”Ï»âµOŞ^Éøºt¼†¾j^Á™	U:Ñ#VuôŸ½êÑ~Ş›?÷ÂCooìg2mß‹í[×¬Ş{QÂäƒ7h\\Í\Zq-9$çrPÕÙ´ãV…i•lÍøÖÉƒÃ0‡Æê[=Ùå¾éÕ‰ğÎ‚§öË‡oX‘Ğeú7yË“‘Æ<Ãí]÷ÏW}<8+y¹J—ºÚøî™U™…NÏŸ—µo©s${Ÿ°Å™Ó¿İ,¶¨3§¶üpsidğRõ1-Âs»EçVï»ç4~Ù\'§}wÏõb=íueØEÖ\n™ÿãG^È³[ı³¶õÌX4VÒ²ğp‘y[»a½ÚfF»µ¸Ş…ğ•qğ«½¸÷rß“†úY]6ì;•Ñ³4Y6\ZÛ>%Şhó<]õ?i÷?â={Ìİ¸ÀÔhÏŒ®Rªîå\ZX…\\¶_{øuÛÈ³§ÚËXyëfT»M°JÈÔ?™ß©ßî3íßµë°­ï“.:‰ëZŸ8;-ğäk·=>ãVÖ½¸·=]m;_½ßú·²È\\aï»×$ÅOÓ;í=ºèÁš×k»Gëvõ/®9hõqûA÷€×Û\n¯O›~F­j÷…’aUoŠÆİågZ¾î&V?TsJÏÖ¿\\`#9Åøpkú·ø¸Ûí|õ	±áµ‘ŸtÈÚà9Š¿¥\"|OúH·‹îûks­^ğFxÊ“¾¾p¯¦óã”¢wé_,âóW¹}¤¦ó?‰û\nt›mQ}ŞöÙÂK›oZ®Èö.04¯½Zi¾\"!®æKÍíÛ¶¯Ã>¾¬¹½lˆÆµÜÅ:÷\rpœ×ÙÿøÕåİt¢K’,:k}xzÉà9\ZîóVMıÃáÓ¾#·Ÿ9ïöîÅÓl‹õº2¹D[œW~›xö­T{?§‡¬è«Ú}×çgª­Ï¹êÔ=<2¹ÓëíWƒõ&´>½?¹&†½vµöŠ…¡a³­?¬ñ¾VÁÍ©t÷¸Ú³²ÎòµƒfÊÑÚ•ª¹œa†ÙŠ7j?ÉøP0?ÑiN§!¯öİ<ßÇÿf†¥f›•ß?˜|9Æ~¸pißÇá*ë:Éçz.~ÿåe°_mái‘» ;³óÙ‘a\'¤İFî`gÇÍ—×]T.•\\ääjI,M)œPçzF–À¾‘«¡YëĞ!çD•§£Ë·3pìœ·\'¢Â.øqh·àãGOèÍº8èÉƒV}·Œõï:sQïçÇ7	]yíR‘÷Ëı¥/ïVa]Ş?¢«ïfÜÑŒo6ny“_’]³22gæ¤º¯Z\Z%soïN÷ÌµÈhµ¤àıâÖG£*Ÿù5^—½Òl·r¾OkÏVåšù¿k\rm¯W:DêcÓ}ÅÇUñ/¡	ë*²?·gé´ûÀ‚áæ¨ù¡ÍÍ7œ›+V\r>,?;m•Q«æçõŸxÍûZ?™ŞNeòû©a?÷KRìÊº¥®­®|ıHÏòõ‘ã×îŸ?8lüê-‹Væöe;ë¸­\nu*ıê?­9Z}×÷û÷Ô»#»_×Zş®Ì ­ÖËÏå2ÓÇ·ŸX{8!”uÇ?Å¢Ä5ÅïmLGq?¿A‡›kåç¹ƒtÒßÌë>òS¹Ã×çû>,xâziÅ™Ò¾GÜ\'/µîª±¶Œ±dş23©•UDU¨^~úÃeeº	uoÕ»3ò–œÖPIsû<óñoÑÖô~Iqù(ÁÇ}òí{\\¶~3î¾CèRëC±Ö¥?Ë>s÷ïXcXæ_r<¤ó‰ñ_-ß|çÍò9ÚÚi+X¦[~0ëi÷Tê²6M;ÜrÑÀºÂpƒºçÇVM(_ëùíˆÉóÊ£†+yú¬áwü¾mYÛ&²çÊ;]š.şšŸåÖşë„<K£x»t7;3–Ë…n.wDf‡KM·.Õ7ósr«rµÖ‹yïp¥ ¬ığÍ®3#-æµY2|lu?µAğÃÊ£³K:%?ô)g×Í¼-ÔUR#÷y>h`vÀ«A:?­Æ?]Ö¬ËÆó^Wúòrïi8æ“á²ì¢OÎèF\'œ”[¬Ù4¥í×®wØ|xıš’¶ŠñûqîëHbœÛL=?qd“‘ ÍYK{’+ñLU<-©¢Æ›V“iÖ¾lhr³¨»Ç[ë…k?ßı0ékŸiD®ä…¿Mà¤šXF¶Ş_.{4ÕØoDºıã§l§çô4º0µàò›%³UÛÌZÊ:ÔzMÒöîµŸ>?¯?N}_2ÓY­ç\'µíoØ¡¶k;\'m€M÷<^éÌÖ\'¿çÇ	ôªæúL8©‹q°^):)‘…ï4\'W]ìà8=ø^æÚğ&m;ø?µ¦°.èÆª<?¡i.³0ÇÓœösßäG‡Èu¦ì‘Ø´oy ]¤ëşQcÛLÚ2²Ç¥%·ú¿;¸g«yàÇ‹İ7i	u5Í?áğ}°“ÃhkÛîÓŸ÷Óœ§é«\"Œn¡¯ÍÍÎã™j2føùšîâq¡4¦G‡y¯–wº!Â2´R6xõ™7en1?5˜èœ~ñzvâuqqŸÉò]ÕºÎÎâÅ‰É¦éİ2ÏF«]à?/hÑ#Ö¦…Ú\'ıÔÓ=Ö´;ëµt?şôü©%÷¯uŞ{ëÓ‡›yUÛ×Ûì–uaG©v´ä¼\Z×±µfWñ&§1=ºª™b¬Q¹â½ »ûÄzSº1îmrèĞí,÷hê˜^«µÇ¬ıö´•¶h™<Œ•½®æfÿ«IŒ/oÔ|6ÙkÕV¶Um{`Éi£-u{\'g˜µÖåQ·±j×ì\\e Ÿ¾%”S©ıT¾kûQµ$ÅSßõqì|fŞé²jÿ²\"·é‚§a5-]xgÑÂ «WM}hP¦V«Àôƒæµ‹¾³áy¿şª{5ÖîÓØgz1ÌÆèDÈ¼-ÓNÅ¸?·yäèâixNobŸßÃLw³Íçû?§°İ\nsî™XkwékQ‡îw¯ì–=g»KWçáª¦mmÓÅw’töä³ÇŠ«_øiÍÏ(zãã”7.ótÇAEëU[e¨®?(‹ã¿–[‘:iùÄZ³”¡3Œ“º–é/²o5v¸İ¬g×+FîêŸUŞn¸ÖoøİĞ™?B¯´[qËâLøùóÏ¦;M4nhÿä%\'0mµ|âR§7S*ægÆßî¿çÄ‹Ğ¶=J¹Ÿzz?ÈÒWuî·.  ÛÔCó[:¾Ò?4Å­@uò@W?Ò?ó~÷”Û¨v:së´jÎæ3gÒò6¶Ië±òÆVÃ÷·Ìem5?qÑ«&tƒ¸>(Ónğ?w}ÜzvO3.N9àöÄî¡jÑÌÏuıŞ¶Ê[Tzßè¨¾ñ}çş»Õû˜.¹0%BkÃ¡Õ³Ã»]‘Ô4òÌør¬İ•Ék\'-½“¡]Z–4Èr±HûPT/\rqa&ƒaîóîı¶Ú=“tÂ¦<–ÙÛî\\+7wã?½*^hûTËd\r£Ÿ?İ°í>ƒßÍZ˜xsè…íæÖ#&X§—–›E‰´Ngwöz3kÙĞ¤NC{<u«î³4;½ÅéÙ-9¼Û¶ İ¬gÉûK²p•¶;eÏ£>Yº¡ºÉÁC&«	c9Û×ıáÕq³İ•|n`ûGŒçÙaqÙGM.?6A%kç½k/‡]ß™¹cé6­<=¯Ú>Ksìó*31#Ucf[s|ÑSµ[ÉÌè¢@q«¹ßËz·rÚ8Eãn¾ãè]_®˜$?ù”qP…ûtè²É;ÓÎ×qDÚÚ‚İ¡ë_^è1Qb½sfä¶¥ò?–jYtnÓ¢ìÑô£÷ò{\\Pë©—Üb~ËáoVo”ŠZ©ªdº\Z~ë2¾èDË.?_Ÿ8_‘3ußgO<1\'>sªé÷=Kœ:älXêP¾ä ƒô½Ù°SÙvåÓöÙ$—Î>ñQ“‹ç1^\\LÎ¯è¥¥:cX›+Úé}jk§2]fÃX>Ì1ï|ş\07}Ú|¿öƒ&ÜÚ¦R˜œÓõÖ?U]ËTÍ{ÿrW³ëÉ•©?RîVécşhŸ_WP‘x¡ÕŞVG+úõz2µJK»ÃTcçn‡æUÖbúŒ6›œœ¦?#\ZÓb„z¤N‡=-Z\'_Ì°[2TµN4û}Ï¥{³¾œ<à÷KU)#4\r—éºşng%™óìQÔâ)ØßtaêìÜE­·jóÕÖI“ªÙWãtß•ziª‡¥?W›Ú§dGÅwã‹W´¯é£Y|‚·ëKÏ ˆİz}?z–Ü¿º¤gpÂô˜OÅ?‚o;¹†íŞŞîùEk¸š×L#?Õ\'+=¶ûŒiµ|MNF¦:cJi—?vû½h~îÒñ§G]¯\n”œ±_2Ûé¶I\\?‘ËËÔïäÛ›U0¥f{¥X£ÿÒ*{“·ïß|y?Ğ·»Go7M†Ïù^ÕYB\"ï\r(êŸ7J#·6rèÚ\'îîÛsÊ½dŸÿ\\èó¡\"8‚õÖtt[© ÕÆø[/;Xj{´¾æá¨ºlIÿ‘rŒ¥ï¿vv¿µ¿çÖñ/ûÍÊo7EK0ÖL6f„…4Ge•—FâªQG3´ƒrlÜ¾w¼¿¹Ã†Ë—j\rÛæ}Ñ}ììç:²gù«MıÕ*Z/ªxÙaïá—wË?¤<*œ<Ã÷‘?cwİ.{]â–~<Íò×‘IÓ¢Ä…ı~µ\\O¥2É»,eˆJ¢½GæÙÂÄ[…o[‹?\rÛñ2&ò^ùû~só³²uúLvm».Jåvš±×İ	çF^ı:Ôg°£fëîë?ß-`™4Oé/;T7òk\\”ÛüŠ/&Aşı×é¯Ô^1×(\'·cËİ¾;D•9:ï&†ùñÉıüGî¸)gCL¶ªØ¹Ÿ/|“•ºz±‰‰¾?›>;«ïÍŠS]N–›ì»ştĞ£„ï9]s÷-¼µ?ì÷ª/\'nËqÍ9wÉrÑ!­‡|ÔÖ¥˜£:|zX™“Î±õ»y:m¿öµÂAÕÛè³¾n+\'©·W+??âvkİ3ÕnZÎÑØjõÈOe sG7­“­¤Üşø„±ûIŞ‡ì£×ÕıŒ«­O3a>÷ã7áĞ\r#ãJdoX“Õ;İ/KçİıšÓ\rò¥¹‡wZT;Ü°îlWî3®ï6“vÚß²;w,\n¿İÃÂh·†Æùå¾½&\r›t[W?»¯?qÈ*5‘«¾6ZbàÃÉPQi%&s¢ÄÒÔÖÇŠ–|{rBewáö•ñŸÖ¶ÒM³µ¬2-w¸|3±­Ã­òi†^5>®n,jŸ¼aå&í<£‚(»•š3?;Ÿš2Äã6×ÑF3lBå©\rKªé9‚©F¹‘z#ÏL_¾údZæFs»]Ï69í¼Å¡G…ıÜ„MqIá§¿23ú~a?Å/Ö~:lü%Gÿ¾ÖÁI¾q·½Æ¼>ş5ô‹ŠlèÇ?—öÇŞ%÷Ò,ıvß\'óos3–ub¯i^’¸}İû	ê^n^¬§6Q%¿e,rä­ñİè˜A÷Œ\n??™æ5úv&:”ïİ£ûydªî†ïªº“~?ä¿lç‹XqßÙ·úÎ\Z²Ë²J”³ã7İ¡-ÃpÑè\'^ØÚmË«ûúLïïô°Ë°N£CÏÙkNÃ4§¦?{ë¿N\Z<æ³ßï¿½¿yêÈ‚m»\'êõ(Ğ?¡Ùâcp×¶éßÛtÊ?owsÑá%Ú.3]1Ñ×ÇlÓù}L¼ÔKÅ,ŒÕc·ˆxrÁ“t[ú¶0´˜Áº”òO«)ÎHnğ´l!xsãØ0µ±f]µk¼ö.œh}©[Ø—GŒ{‹ÖßIµ+oy1c¥a©¼mY¹k¿>sFpn…fÜµì?í¯›?§\'ŞÍ&o”Ö?»Ú—í¹hÙÈ\Zß‡³.Lı=ãI?gŞÓÏd»¶ß}À©s?-w—†·«™4;ø(J-rqú±GG-¿/ì°IŞ¶p\r¶)kó½‡Œè³-çt‡èvîÃjÜ¯y÷¸ÇíÇ·LÊ)^_£²-`@ïµ&²w±+5Æ?{}ãÅŠÉk,\'nz×nÅği{bOdO¼¸õpzš£ÓĞ½a¹İºµó];˜ãgCÌ—L³æ¾¿…ÍÎ™²)ï‹je×V‡»Öõ‰º4p[Â¡Qmw%ëdšôœ½!£•Ş„ŒqŸ\rÄÎú#u[›U_ß^ä¸Å#g[Ç?Z‰Ç<joËÏÅ´v>ÿÜ+{ÿTqá¨N¿‰B«g¶¹±(3ÄÂ±cê±Éì¨\'G[Ï°ò˜;FuæèíÂşŞ»İ;_³jÔ×wÏ²ìw´x´rpy‹ëÓ&v|R®rhûÄ)Wæ[ì¨>5/er?âv™/ÌFIÚ„Ï¶?jk?Ì×³q™ò¸«OË›é“Ş½ÿèíë<¹òNÈ¦h}şŒg¯	ÖŞ—™\ZÏšø)¸£³ƒW­ÅT£	7_İL\n+Õ¨S3.;}dèÛº°Û›85¼Z?NtÏm¡U?ı°‹Ác÷ÄY^5g§şVc£i?2bÈªÜ¸T?c-Ç?‹K[X?2e„×°’‰ewk´Ãv|êÁü»•©†Uw¿N8à³ëÎ¸#\ZÚ3;ë¹·0‡‹û´švtö÷9—ûz=îgàzµOôôë?Ğƒç5t2f§k?ÒIò/IÛïˆû¼Œ·B9k{HušzÇöµÃç«æ&¶õz1Pç÷=Ç\nV×Ô©™•íû²«Ó°\r¬Ómíó»e0;?=?£Ò+½§÷Fa€svx‹)Ò¢Oí89àÌÁ3ù³ÒZdN}¦f‘Ûú–—,W<?V™ÂW?vncĞIoä—“®¥êÏ%ng•ÎòÊÛ]¡~ş¡×‰S/ÏU+Ç7Ì8X?ô‘çb\'±:ÔË<AËÍfip…èdê†ÓÛ\'Ç¾~şî5xÀÃØ ¯¥ÎÛD©?\r»§em\ZéõÒ¤Ã¶èE+F×´ÊX¤wv‡1ãä©œíC²·<ñÍ«¾Ûaò?á}Kæ\Ze¨åõîâ]dà|h[?íÚ%?²Ï¶/ÍI}¼ûtçÔÚ éô=Æá&?›¾oî˜—è÷yÙŠ?Wåã]¶¼Ôñİ6ûîÛ:QêĞ±Lœa8ó[ç¯_wŒ}ôêî{_-•¡±qãêL{‡Šû±»o›mu=tXïkFâBæ„¹È\\¥»Åc¦ş#ùyÿ×Jùÿ±äotbşëùÿlÎc7ÉÿÄ~åÿÿWşÈT÷à@IdÌ?Qäï—Èô°YÌ\n•ÜF%J6”,T²Pò „úl cÁ}%u](ä£‚â.QAÕòÑ7>ºÇ ‚Ââ!†H0t‘‹.r%j™??î±(!E\"P­ğ)tb-ÀQ?¡‚‹\nÄLÀF”,ªqá£vòùŸGáñQu>Q\"1ò‘xBêO@UçñÑEKXUp).<$F’. Ğq„ãèê4ÎAßP71!UP\\0ÅCMÂ¸ˆ±&,?,¸Jº\\u‘‹£‹\\tQr„”?9ª:‡G5ƒQ°Ô\\Bg#¹°‘XÙH lÔN6­PH?B$ªéª’ÿRõ˜è3İ`\"*&ªÂDõ™À?	Ì™€Åh&´„	\rcB;™Ğl&ô‚	?bB™Ğe&H€	a‚|˜ .&H?	Âd‚l™ j&H	Á„qaÂ01aÔ˜0ˆLS&1Fœ	\nÀ}`‚z0A[˜ <LĞ%&¨4?	ŠÇ=d‚Z2AK™ ´LĞa&¨44œ	\nÏıg‚90Á:˜`,L°&˜,‹	†Æ»c‚2Á*™`¤L°Y&˜0,š	Î{g‚ù3Á0Á90ÁW0Áu0Á“0Á±0ÁÏ0Áí0i7Ä¤ı“vTLÚs1iWÆ¤}“vvLÚû1iwÈ¤ı#“v˜\r>`?¾ÅiÂ‡Õ«kÜBE›ë{ÁnĞQEÇ?CŠ-HZ°´ iÁÓA=PôÀÑ	K´bàiEà5R¬‘\"ÑŠE+\Z(­ˆ\nÅ¤•V\\Z‘iÅ¦DE‚Â0hC¡\r‡6$Ú°hCyÒ†¨0L¼‘áÒ†r \r]aø´# í(hÇ„v,\nGC;Ú=í¨‹vd´c£´‡v„\nÇH;JÚq*tä.ñ¡^Ñ^x?òñÔGt¡\Z¨:0£g\0˜`v€¹µšIÏ*0ÇÀŒóê1€©`Ş‚Y	dIÏw0ûÁ\\3#Ì“h”`Ğèk0ÛÂÜ‹ÆÔ?¥aÎ†æs˜İa®GzjHG#@Ä\0ñD[@¼‚ÔŸB8\r\"¬Aô±D6ç .`”tDñDK;A$qDYsAñ¿A¬‘ÄqÈëĞ^H)è£ƒD¸®é îÓA¥\"È¤ƒNE0ª?b‹~¬è\ZQ¸şRÇ_êø¨#”¿™”â×3Ó¯g¦_ÏL¿™àÃ¯g¦_ÏL\rõò™‰\\ÿéßFJëÿãü\"ÂBÿ–ÿÿìı|Â0¯ÿsqş¯õÿÿÆ__ò@y†Ù\0òzå!¼Û‡<ñ\'ğ6jê?~ÅŠc?_Œ_Hhl$c„_¸_°Ã,‰*C£¬BBıı˜qÖ~ÖaQÔk µö\"òÒøPÿÆ×¢C#‚›{Y‘ò÷? ñQAÑ1Ô;Œà?ê}^C¤\"\'_ç† ~Ñˆ!zï;yo yÊFP UÉEâæ:Ô×Ùœ1iR37Ì•x…’¢Cm«Ç”yx¸º5€ŠˆTF„ÎÿÙÔ”Ñ³Yht‘Q?Á®g7ºˆ]Eƒdc¿ˆ$NP\\@X”™¹qC4_ßA_ß]ôõ»y¹J›Şâå!vkryõò˜yJİ<Íçxöwñt÷?)šßë­$\n’|w¯¶I\\DıgÅ‘—¨ƒ/èûğ¥QçÉZ’?|§¨¬zWÙ0¥Ûn¥â›ÌSâÂà*‘JXıM/	oĞOù¦›u?¯¸&\"¸¹0\r 	\nÏ!c¢ä+·‚¾\Zc¬Ì•¼HüãØßİ¸<ºeL~4ÖVîáhë	=9äs†BÂ¾¤MC©M8ÃØĞ\0uªA`?\\°¡Ì1±ğ,ôÊ&+ò|9s8Ô* Ú÷•«(ëHnxÚS³V©ïç;>4†zWù*%â;uø)ù*(t Á’zSı»Ÿ¨C|Œ{ÇØ0Œ­h]\'IâÓĞm$y5Ë^E€’ï&²†Š™òsÁQ/½ü‹‚C´ÿ‚àşY¡÷dı\\VÍOd…xüLVè´$iPlP@,5¯HˆiÅÌœP•èĞø @8!‰ºMŞ’EŞ%&?Ä2?´fk˜š•4¹ş×˜y\\DLh0áp@êÄ,ÑHÚÍU¢¨šÖT’¼–Rmòèr~$ßCFTdE¾\\Õ\ZïÌèç‰Ï}F³û?¤è½ßèİe4ò}ª,sFıWärm­„òõef\rhë½	áZ•oPÕ¨é?zí\\ŸÑ¬>èİhÔ;^ÉfıjiémŞğ>E@ñö”˜SW´”Ém©+\n¢>½èzÍ‘)Ñ‰û4èáá€„\Z‹FÄ\r‰	¯‡ˆáLö†Äf±.\r™’.ü\'\\4n¯„\"®§AÚ“çQ‘ É£\n065ÍÁŒ[š›S JlÉ¡£ø*s¥\Z@¸cº[Í‹!ğ°z\0Oš#¨¿Ãc)İápêëpp4ªÙVi5É—Ç®¯Íá6­¬PÉfäÕ,‡Ÿöµc)µk8Œ^’¿ÚTÆ[ÙLåRâ\n*öc´?6°-òK³v«à«ÔlN=\Z}iÖŸ)Ö‡±?™)wç6ls\\Ö\"_~ø#3KiŞ’ yŒæZÕŒjü•?nZ©ù‘ú[¬æ_³\r…8íÿ}=%òÇzJJ9…|	eÃ?©É‚¼?AŒb0c¥g8ô¶Mêy#Ğšš©Qƒ\Z³¨×Ğ²	¤fÍ†lš!¥ÉÈ#—{2È‰[Ã™˜tLştÅ¤L°‰ˆg˜”øŠ467gÀäÚäõÎNÔœÆ5%ä))?¨÷~X[äêÔlMê:9“Sôäm¥©”PÁ€ğ(t™ˆÂü|û»[S‘8õòty¶fHT\\\"¥MêDùEÅ)×@‚Ój–šD?!E©TCëÇ´î’@ø×æ\'‚IDC.ÄprhĞú?®Y†&\n#hl!Š!5ÿ™Z6ú!Ì@/d­àP€JÖ?$ş!‚ÏĞFñ¨Å!şÿWBQ*è¯çC?OÅ†ÃL£8?bĞzB’†¼­¨@+‡?ŠSyF§l¢o½í?)8G™‚z§q`ùúä~$?é¬DG&¸Q‡£“4l’†Çb80ØlÃ†Áæó\ZÓ?¯¨\0Üf\\6Ã’ğw†=ádù„Ù?®—´\Z¢Ùä!fsf=š9Ã€2\'.!¶$!‡Áì§@?Wˆ_gbP1>	~1\r&”nÕ/E5Bxvñô•Šx™˜+\";§~:*OVıKq‡ĞP6Ÿœ•x:‹\\l›­ûs%¢{ğ\'*D“ı™QC¤Pò[ ß¸¸ú³B©K³f®†SÙà£¼GqPv…–V2«Xñx)VÔ6›¯ø$€O<œş L±¢jpÙVlŒWÿ…‡‘äıA\0ø,úG©2—\rWÙ\\®â^O ¤±…l%œfÎ§?iæ4?>·O·ŠO·ŠO·J@·J Ô*M/ é4½?¦*Ñiz!M/¤ÛÃf):ÈRj›…+.óŸøŠOÅ\'eI³ip6[!,¶¢:[?ÉQ–0G?ÄQ?rü9ŠJ\\–ò°p—1Å\'2?¢­\\/®‚¦h(ÆVÖE«1>\\&F?ù\'ç	ú)štıÈ€?²/Úœ	M\'Ü\rè3uæyŠƒ”øÎ$ıá”B .° ­q©7#g¿f 3Ú¡?µŒğ¼G±¼Á…5½Åö¦{@2RzN\'É	‡Á§ÂUòé‡X\\ämà&1ÄÊw9ğ@GŞ6·´lÌ@÷?Òuˆo<\"fF\rccòeëŒzyĞêfñ(¶æKê?ä‘=‚ü¿O†QÔ1C!Ôñ¿\rç$8¡Ñ?h|då‰i§‡ê™‰¬Äæ(‚\"¾˜‘U,ÌÍDæVJëhÄ±¹ùŸ¸ĞÀH_Øºh¼iÕh´ §\rò\\«ú•:24h¼¶YÏ‘dA×!éšA xÚj×sûÁµNOÈ?díK ƒA1D|&9øŠ°\"„I–´Â˜’\'yQ5¨Wã£•`h“™?s&©Z_4VªdÖÉ(¶¹\r\n¤;Ôæƒ¹¸\r¤´Œfï7>4Åû1QA¡cáÌ%ÆhãŞ1£?ÉSMÑ\'c+ÅSfƒ¶*Å~0â´d©ˆœ>ª‡Š\nii G<âƒYƒ¥UsªÃ’œá?¸B±—ÜÓe$-7ªó„ğ¬\rjZ˜›¢±4UµÖLqŸl•)âJ‰Iõ¯‹?ÑHfD§É³ÃB©ãĞ&V“g%Ät<¸	Jî¡•	GìâdÎ ™D¢ÃĞÁeŒHB©ÇI>­)i¹¹»ú‡à@¸…tñ‹\"ô	Ñşé(*ÆÁ??h®8ñ±‘–\"%5ÕJ¡£{22AA?uü<+D¼Èâ°M#›q¤?S:]‰¤­ô¸fF\nÔª~FñÅhp_±Ï>ÔÙD£Cû±l©>“@)­\0‘g.?\Z2B--Í‰4-¬ºÂ\nF(rßMH”×\\•º‰³¤®6í¥V†)\r!”Y›5ËºY&Mê’¬hB¤ÀøEøQ³kƒ&5h§Ò¢Tı}%°”úu\"Z&&İ0Xœ‰?fiI†¤tc,ÉuœfV[\0CÑÃúšÛ¿À-¥!Ï¦Ä\rVi”¦ÑjãÙ@iÿ–ÖP!†2(uÜ=õxF<]1êF…ù\rŸßI:´ìDmPÕĞ\n)Ñ˜?`3²jÒ`¤¨€‚j¡¥%´Q«ş9×”4YS\"lhÊJYàJ×9J×H¸?·øÓ‰ÈX1İ4ã?ÂH¹ Y7pBÿŠ‹ŒkÆï)–:Éşx3ĞØÒâL1´MOÈûÇ{‰Ôó\Z0Î—©(ğ¿Ô4³ïÕ”•¢Õ?‰cšl‚5$?\r?\Z…dèmûC÷L¥—(œÁ)!46 „aÖx\'j¥òÔá‘„G¶! é*chÃ5(?AèZ$h”b´ğ(:H«÷é‘t?ß8[š=áĞÌŞm|àŸ³¯ŸšaOn9)ó§E³5½&C“2>Ú×–\ZàEl9!:,kÁ?tNfÄÿ.ÿAŸ]e’ŸˆT&aPWh\008D‰>6¸	™ÂèF Ëô¨×»øFòúI[ˆ›Â¾!3ÒÙü˜›çŸ¥çO’^…üikÉ˜Wæß\nµ˜ßDmˆÿPÚä=ıÛ„›ñ‹û§z1.\"2!‚êà¿?F:ÚÆ“É´gŒj4å?î˜âƒ1£¡™?aI:šú©²™™²~Hî«Rs(y§~‚RĞZ²S£	\rİäxÛ2êc úU\"@T¬˜3Bá‘XêP7Q7“¥ò%z\r¯Ñäø·Íšõ»sõH2¶Ô„\n\nMfÁ?ŸéU\ZÊÆ*BÌu±¾!\rçÃúMk)ë)¥°mn¤–³™l?æ/ò¸Ï&ÓbLt€Ò5jF$È|ÃıéÉ²!OÄ„ªUOØ,ÉÚV»YÖe¥N\'m4‹F“ŞŠp²éC/?hşƒøEù¹?ˆ\r#ãı¢ƒƒŒ•‚ â1Ö?ğG®BAKöÍ3C1A\ZÎ W(È\'X²Vhl??¥Od%f$Ïu%´›Ñ;?Üú¦Í GS»Í„% Jøô?ÍJ?¦¤D¡¤?æ\Zo\r°\Z7²!‘ĞV¡¢Ñ°5A&R²š¨i¥ XQ\'\n7ÉS¢°yê˜¦éIÊjÕ8v³UºDò	%‰”tcØúëÇ¨^ıI‘(?^hıúK¬ÒÙÀÊ¢ ,µQî&Ú´Vœ¬iŞ´Å”¾*©£V¢lz?JÑRä`ÑùU?–AmÏ0ÆÅ?Éj¡æ?ùA=e…*æ†9à‡\"@ñ¤.Ô»*m-È{Û²¡·¶É¬­h©P÷ÌIqĞÔ¨éM©Ñ°*è©ã.£âşõ!BÕ¬P”ÇŠÀ ˜)$G	ÅŒ”X}úq‡„0³ -‹˜>©vÃ5RLJCŒø49ÔúŸN`ÿõ÷ı)~ÿA/´X‡üí?ıı‡ÍåğšœÿÌáã¿~ÿñßø£sò‰ˆs89üäO`gFéÆSÏ	Ô¡ºMöİ?‰¹³ïP¹¯<6)*È:À˜\\?*êÏar¬9˜5G›gÿ$†4ÚšˆùÇÅû%Pq?É–Lg%âH¯ˆPêa*Ü/*ŠŠ[ÈM3’¦qĞ„¡ò!şãGy?bh‹šZÎ\nğ‹\r\n&Ï‚g±Éê¬DñgÅ`%rÉŸø¡’C–r9K¥]—‹Ñw¶J±•6UÊ?êÅPRõY,1†îc,ô]$@ß¹2T_Æ…ïrÀ‘B	¸Bø.„ï,Š“I¥d}.ùEt?%êSø‡’í„ör%§ø?,8ºC=Jº¾\0J!”T}¢”_èKˆğÙ”À—òã=òÃ¡¨>QB}–J?\'\Z¢¤éeĞ?Æ‘Í†v\0¢\'Ø°Q)å£RÎ‡ñ“¢şó@>lw6È…Çƒ’?Jú:£úÏÃ(}!J_1É‡Ë?’\Z/›ã,y°0À•Ñí¦ï=\ZW¢½ãĞzÄGøè>Q¢qÂ%b(åPÊ $7Íq1ÁEl(Q\'p1J”R(AI¸b4ltŸ-¤K”l(¹08\\ÔxKQj7úş¯–Jõ9¤DD‰„ÀÀà	`ğ(¹ ô\"JˆğïÁç\0ŒAò?Ò%í£ÛËı;ûß¸$Ú¥)Æ‰Ã¢K¤D\Z\'×y\"(AløÎc¡?†üí*…tÉ$?6ğaqÑwO¥J8%02Ñî¾àÉÅ¼¿ÊÁeò¦–Àm8üİ#ñg%ánXàÀ\r°a\Zbƒ`Ã4B»S6¸6JÔÿ?4™p/€Ï|às\0Ÿ&8€Ï|à#K\"JÚ]?æ€[å€ûçÈ ¤§#Àç>ğ¹ôôø\\z:|.àsŸø\\Ñ¿×ğ1ÀÇ\0£§qÀÇ\0|ğ1ÀÇ ÿôƒşcĞú?AÿqÀÇ|ğqÀÇéi\ZğqÀÇ|üOûßÔ0d\\±Q‚ûÆ`NÇ æÀ æÀ æ@B J˜Ã1˜Ã1˜Ã1˜Ã1˜Ã1ˆi0˜Ã‘ˆğqÀÇ|ğqÀÇ|ğqÀÇ|:æÀ|àó\0Ÿø<Àç>ğy€Ï|àó\0Ÿø<Àç>ğy€Ï|>àóŸø|Àç>ğù€Ï|>àóŸø|Àç>ğù€Ï|à\0_\0øÀ\0¾\0ğ€/\0|à\0_\0øÀ\0¾\0ğ€/\0|!àéX\Zğ…MÃ†Ÿñ¿bôM?\0§?@?A#D AB?D AB?D AB?D 1AøbÀ¾ğÅ€/|1à‹_øbÀ¾ğÅ€/|1àK\0_øÀ—\0¾ğ%€/|	àK\0_øÀ—\0¾ğ%€/|	àK_\nøRÀ—¾ğ¥€/|)àK_\nøRÀ—¾ğ¥€/|)àË\0_ø2À—¾ğe€/|àË\0_ø2À—¾ğe€/|ı\0ørÀ—¾ğå€/|yÓ°ç?6„C\\d‰DÉ?’%%%J>””ˆRßEPŠ¡”@)…R¥•\"Ô	®ğE€+\\àŠ\0W¸èéš(…K`Í…§F®ú/†vˆ¡bhÄ\\1´CıCÿÅĞ1ô_ıCÿÅĞ1ô_øÀ—\0®p%€+\\	àJ ÿ’¿§ÿÿCõ›\ZŒ\0CKDÉ†’%JJ4(‹¦ãY¡ú|ø.€R¥J1”(¥PÊ >R\nı1ğç@;8Ğ´ƒíà\0>ğ9€O»ğ9€Ooğ9€?bôl1.ô›ıæB¿¹Ğo.ô›ıÆi:è?úÏ…şs¡ÿğŒ€q¡ÿ\\è?úÏ…şsAş°ÄƒaÀƒv`ĞÚ?A;0ÀÇ\0|ğ1ÀÇ\0|ğ1ÀÇşkòojz‰ˆ~z†¥­Çqp6\\‡õAQÂ}Z\'Ä9p?K—pŸKN,XjbÁº¾£uV¢ä?¸ëj8,à\\(1(a‰\0\'”áCû¸À‡KãÀ’—¦‡u4.Ğ“¯òúÿË“ıªÿw?ğŸ]úUÿWıÜØ¬_Fğ«şÿVı¦FÀşe¿êÿoÕojœ_Fğ«şÿVı¦Fğïÿªÿ«ş?oØ/#øUÿ«~S#ÀÁ¯úÿ[õ›\Zìc2´³‹ã(A\r¡u}!­·ã˜„bÂc£M/­ËãB9µY\"d¡M-†r\\\\´?Àá¨¤Sğ¸h{\'Cëõ<\\FåC0”“#à ?a¡m“óhç\Zç£Í0¤ºò8(×©}\\(¥p<”3# ív¾åñ¥h§V(F¹+<ê\'_\"¤ö´Œ‹Q.C;Ä<.ÊkæñĞ9O€6ƒ„8Ú¹òTûy|´ÙƒÉPzy¿$ar´cCÎCé¸?Ë‡öc wÔ/	àBz?Í‚’?äÉCû,<!‹JåäCºGíàãˆ/Dùï|)J[áKQº‚\0ÒBÊÇP®”@Œö‰„?«%„\\$œ‹öi=Aı“¡?|œ/Eòƒt!¤Ÿğ2?R>\\ ‡qÚ§á‹¸Ô>?\0ôIÀA9GB	Ú¼ÄÙhÓç¢şàÚyÇ1¤G8¥}àĞO-‰ñ§pxR”¾!€ôÎ‘65Ø1ò?²yLÈCùÔB>0ÇP~2ÎE9„²PJÀ\'s`ÈA?£Æ	äà#CFÂE‰bB)F?‹èKHåØ(ùŸ0d„<´9‡ZC]£M/v …4(¸PF)ÎE¹K<)2&>†rf„8JÔÂ!é?D6\Z´é&$•‘\Z¤<B!Ú$ÄÁXq¥Iàb´Ù†Ë‘ñâe”Síç	 ?4¸<	’OŠv®ùJèãó$0ø(‘P(@IùR?\0Ñ	D(¹_ÈA97<)R2>.¢ù 9%Tÿy?ÓE(1â#E‰lBÈƒç	‘ñrBJÄF‰p8ŸMõ@ÿ„hG£t\\†rux,¤¤<ZÎ\\9ôWL??²}ù? ÇgÃw6Jlä³QîŸ‹œ*_íäğy¨ÿ(wJ€#g#dÑıGòJ‘óÅ(Q‘ùü<	NåÃóagË‘“¢l]\\ˆq¹\0ÆO*ij°cÌã\0S\\<¶¥ğÄà?A(|.²T¾Y¼@ˆ£™@ŠÒ‡ŒW†r_p	xn	Ê9p‘§ÆÙ,¸?QœÇFÊ&`£\0eKò!Ù“!abr9x&>\Z2{S›2!(—Œ~yC/òü„§–!>8xl.åÉ19j\'&GÙ›89ÂÀˆX \\ä‘q‘f\"1Õ\\ŒŒ†/”‚‘ \'Áã£–˜ P¿ÄÊMâ“Ù¦ä r‘óàQ¶%_ˆf.ÂS\"çNGÀƒò 9Bw‘qQV©\0Ú+à¢<BŒH®\"d8ÉI A¹W¹fr0J>úÅŒ€\'€™ŒGäÔˆõ“Ê?£•Øh$Ú‡œı\"J ‘€SE9PB!šÁ„B)Ì(1’?rn!JÜÃy(Ñ ´\\€Œ0JğøÈÃÆ)BN\0å¸ñ ˜ÇF‘?ƒÒxø“o®ll°c,c0x<PBôó ”¦W\r\"Ÿ‡<	_ÊÇ‘%£éQÀG–Hx`ğ´„läñp,åı?öŞĞ¯ªº÷¿8’ˆT[Åbµ^¨ÅC8ÓŒ(gTTp@ÄKror%“É\r‹J+N­øœRõ9­â,Ö*ÔµRÔ¨-bkµúê\\´\"uxúÔ6ÿu~ë³n7ZPë¿·•?s~gØgk¯½Öw}—,s¬0”?ZŸ{müYŞ}\Z¢–Xi#ŠÕAP+9´Ku£÷w\niN‰Î÷Šºt2ÊT?tğxEy¦B\'³+\"?¢ƒPVD$¤2_Èàï¯@ç£ZY\ZÒ…Rë\"jK­Pë(šÔ?)z4vœ\'¬*?¾L®F=ÕÎNA%¢,TÚ~L*ÑÚôúXY©ê€ÆĞ°‚Õ\nGñuPIu’¦VÃĞü PsWh½?ÓşóŒùnıÎN\'?h%:é{$:+_L\n»‰Ä%ˆ õoÛ$šÅ¤ş>¡f×ŠuJA!Ü>ª0v¹i¦Æ¡~åİ¶“J…l;o++WDM/5ì,V#ï?&A?Ç8´èx„gÅBgn¨z$h@‚2(r$eÓérZ¨,*—ª1…Bm]ÇÌu‰ÍG¥5#¶*ISäyz}Oã÷İX?ìIĞ?=ÇÍD§õ¥Şç’  ‘£Æ%øÈ\n×”:(\ZÎ·.èr®?åt‚HBÕ0ùU=^%UºÂ„zPu°Ñ•$´\nüİ[%%A02Çt0+\Z‰YŒ#ªv2	tÇZ¡×\"ø´3Áó\'\0sÉéIJëIû‹:«’6tHZÓ±µŞU¯“Ò$”I£Ç]ÛêJ¤jNğ¹ “YVr¾İºËnº¦˜O„@Š\Z÷‘Æô–“q’±²\"LJöp½§WáæÂP«N_±\'cÅmlÒ0E×£¦2İ í${Líÿ “Ç»½cÏ8`¼D…¶KY-?xŒ}ÍÌïU7ô?NùV}h«F6\\*‰SÅ õI\Zjİ\0…??í’êä±ÖÎ?ºè…&öÚ	‘@WQ]u†×¨U?vFÕ£Ó\"ê:¬î)di£º¡ò…^ï+$V£?Psƒ(Œ??ÙÓhgGö@¢>è`l?|¥ÆEˆ GR!ª,éJ¦’Û¡³{…´«~wÍJ\ZQ/ƒ\ZDmìtOĞÅuã(cKÕ¯†…D\0µHRueêD@?`‰Ú\0šTÇ a#?™AB\'•İØ·ºg’~¦d²gªv¾/Ô¨©µ>74ÙJ.2GÛ·U5(°7“Á¨šº½b.j°\nİNãD]AméioE!‹š‹ÁCƒ¥b]²çÑøˆTz?“.$3¬8ö5íU³±¯—î	\n<ÆÕ Ë¥èÆ6•¤²;×?¨uƒärftÎqé*íÄÂvñ:™Ê@?‹¬ -NÂİ7Ñé¼™Y“4K\Zc Ó´QjİĞú+DÑN$a`‚Y?|9Ë ­pİwŒ2ËRÕ™„:”’C?²FT	îrmt×*[™¢äu·•m”Ùˆ6‚5I¿+´zŸ#HGVLm¿PÙÊ©õ+Q?a@Ñµ2™õ;?VËbRÙCèù¦Â\Z¤“AtôÄäÕIµd/§eS&BÚß¢^hı€¶\'„‰ìÉ°n•ô»î\\YêÆ´P	ˆ7‘~Ğ÷ºâÇ¾¦}uÏ\'¥ZÙšD®BÑï?ü:y+q?•ŠÈ:ÑPUˆ%\n\nA÷µj)G#iV·*.?xŒ}™°Zh°‹ldõ%A%Ttƒ*@g|õÁkx?t®¾<êÊz$hd/U\'w¦Aââ(9—OV\n•4ƒvzì5+ñäJÔùÓÎ¯Xnu8\"Æ7™¼*Y?3«’R#Ÿ\\ˆX—Üd¹AÕ(7 ‹Îèó`{:É¬#X]*¢†éŠCt¸?HÖïrX­jqk¦ËJÕ‘Nƒ’BÏ ¼m|uOUVm—X3©k]‰cCûô+*V´•¸hØ³çi°Ş?,IŒ°©TXùÁ6´‰IŒ©’0Ho{‹6è÷,ƒÓ´NúU8ÄšI2$3B4ÓIè öp„±Ê¤`¼°¢ŞVZÔET±C?”?³ªE9ı“lè±Ö•“\\7šxŒec¢?Xce)P\n•ä¡T“Sˆ˜¾¢v¶lŒ\'!:²J\ZÂ,E\rÒNlr¬ªæÄ¾ÃN¬•JİX\'¯3]tJU˜©×åXµ6iÈ/YmÕO@\0¼:])zÕ%EcÕ²eZŸ‹’?´:[b=0©’OW—™uIíÌ®¢s<VªÑ\Z4QÇ4Y$¹–_G@¾§3½S5@>W]¯Öµ€õ\"´HğˆÚHØkDÄ’½…×úÅZqìtP\'¾Ktv-£NŞ„:Tõ‰\rªFV#Ëd%G½%ÆUºw’-Ç€pĞïÆŠæ3„eTŞ_c\Z­t\rQW¼X\'ê‰pkTâ\'(Id…™èô©ÑğTQW´~ƒ¶³h&ú~xn¤D#¡¹×=eÁ¸`åaË^„½h‰Õ©Ê÷³1Æcì*60gQ`ù5NtñÆ¡»²<6q²1İÎt<[ÖƒT·Gê©T?s\n¶O€½Ï1!lPÙh{¬=à\ZïÕ©ãEOî÷Ø­jT,ØóœÄÆ¨U5Àw˜€»E+ƒ2±÷Ñe5`’I?u;wÛÛÈ¡ë\nÕ9&¦LH‡\"dI‘ØêHLuÌU?Œ¹Il­W,µ=D’¢ÆèJ,BE…KËŞ¡Ô:BlsÊÙkàL±D­ğ›è KŞ¬GZÿ”Tâ§ZÛ;uøaL—/jó™•Lû5vê§(\nÚ.±UÂ?ˆ•)õÎ¼ây<¿RuX„Š¶{­+GB=Jş„g(u…÷?;Å\\‰\nd¸¢fX±4Ö:Õº\'fRšıì	ğ{$™4\nƒ\r–G\'HºˆÚ?Îìa[ğ¹Jœ%¦Ö’åT;İ39‚ÃyÒ©:Ï2½èÔb?â°×«¤pNŸë:]‰dÒy&¡Z}ØØŠ:‡“Jíş!ê¤‰x°ºkŒêñ”I?‚ÉVbE!ÀßAàr5ÍÊ²ÎÆXc|]D‡MlüšÎ?¢b?ƒ$?ªù&”˜µ³Øø\'Ú3 “Ë\n¬íuÅ‰Éîb¯“P\r\ZòX&\r~‹¨5>ú1`BÊö!êWÍäB?ÄY*œŸXõ\"V˜¤êp‰ÔÄ­Â?‘:éBTsêü5ûÕÄÊÊã½Õ+G¨)r Ô¬è?Ã4_`\n/0ñêsSƒg¼1,+ê»L¾¥\\cÙh`UÁCXéîßc\"õèÔ¾Q;·èò4®ªÁëò&û3½¾T	–ÊŒİºy:&‰c°c]É0‘â4r-Ù\'M¥Ë·èŞ˜<UÂ„\\\'AÀ*ÏMbo¢{•èµ3b£+Rlmã¤”%	{¶g#ï=V&bU0çNÇr]Ò©jg—×²ì|{·èüÁã‰Á€•IÊ„Û_\'uÃdluÒ¸>ÃTÌ¤0B{çKeĞò®°ûqz¢nÁ˜&ûLöşSr£ê¤oUx‰¤ÕA—Tİ	D;°Òé ’AAM´1GÈäª{Ç„?©ê?\n)9¯êU¬ÕÄ[?ôÒ/:n¼êø)‚$h<0úcÀj8(lÅå´[ã0µÚŞ B“ÈğƒUûÙ˜Ç¸A}èMmÀÉS”èzX\\Ç€ÆIHòÌö\Zsİ€Æ*±±VİPÔ–7¬H#\'âdcçX†³¿ëı=ƒªQ	œ|Ï Ä$Yª“–é¤&4ËØ›±®À‹ãy¯‡Û/¸†N`ÃÜ¨n‰¹Mo¦YìÎªvÅç_ë\'ï?N%[èñ¸š³-×ÉµÀ*Jµ?ÇEbÒ?q®+¡´\'ê”\Z\"“Ñ`#¬Ğ	ş¢èÕDšÀf‰ÎŒnÌ¥Erg&Ï–~U	?ëÆ\\VmVÀX Ş6%ÈÁd¡GwG?);<æ‘\r(Ö»ÀõMÄ4?ß¤D?¬u?sõ˜Çg`®Â11‰DË)xj˜§ŸThøR\r3¾.™œ*Ä’­Ô¾C8©A@îß?u(ÙJ?0%1ˆºçjFÎLbÃá;tæ¤”!É&O=ß«$Ğ¼ÇI¤?$“Ã¬ŠI:ÈbÃùŞ¬R*áRVbÂó“{“êöÏşµjT0œ‰Š)ëÆ?ğôf¨O]…?!€¬Ô\'€e’A‚]š½M	àM^¬ƒ€®ëºg’ûuP·¨#1€­a#—«³(âÔ‹8Õªu+’¨	*¡†Šºgƒ¯„¨ÉV`\'²?Åy„Ÿ$aµ (j©¶CŸÔc;¨š¼r6ú=X+ö\ZydÃ?ZÊJìàf”I€©+yßç¶D?AÇBÁ6{&7`¸@5ê\rş¼¥gO:$LëVŠ½ê<U2©–Ò0–xŒ]?¼,ĞÁ±[—Úˆ®Â¤æ”?Î9&‹ÏAAâL¶gÀãØ S·4\ZË®kÙÈ´¹nŒúİ­ï\r°†›>˜L%*_ªÇÙƒ?‘½ˆ•¨5f×ÇÔtúXcOÎ$	ÎA0T’­ÍÙëJr°<9ºùh:?lÌĞáN›È$HæÄñ¨’‰Ø¨Ç5´ºİ^Õ†Ì°Vª^Æ\nk›S5%òüÀk„í.jˆ\n‘: ş±±nU¸¥œ\rr¡°‹Tj»$(sd?DM˜4AÅöfâÔ¯êÕã.+—´g@òÆ’½$&Ì¦@½ÃZ–\0¬®ÁãÄSİßwº“Ç±Ç«½Á\'¢jÀeòÆV4VÆ)+¸¨WıÒI`cC+&°#\rN$xèiTèÿ\"˜’˜\0`EİSÄ 3QváòÒt8Üé=ö}&‡S?ÖÕ…Á0°:éòé†x„a`\nÓu‘8\0ğ\"+VR‰ë;\0v˜,.¢şè²‰;]$¶n,?qÈ 4uIk’©àU›çu\r?ÎÆsÂ¹&ËrĞÁbèY€wê\\T±ì€)°?–•öÓÁ›I4Ãê Í«Ÿ a?®±V†;‘Ø:(rü 0äe2Gµıá)r!Ú(y\r*¶4”¨ª7ÖĞêÆ6˜U(‚B†Î3åÀ)Êƒ„M2µ†…\ni\rL¢Ë°.\ZZ!ˆ•RÔ&î7!è?M¨pğ)7ç ?êÜLÆÔo“\0?±7\'QLç²¯ƒ#S”¨Œq$«á½ÛŠ?¡Jú¢Ç¤ª=7àYÍÌ^nĞ’ÁTñ8k|\085˜ó\rô%â^uäÀ$”Á¢Ø\Z%´ºE8K]ìã?qà1­kœ8@¹ë–A|Šíœ•tJ­Š+ÏTx¬B}a&:L½9j‰b³ä;±zàÇ°ÔG6Ä¡/íùfµQ	ßy`\'ø\rz§ßQŠÌLË8õx/$Z¾H8ÙpÖQßß0Hjtj¯{¤Ğâ<Øó1Øª¾3“gÄÏÀäò³\Z„\0ûû>p_à×)ÔãëÁhIcø(?j·¨oøWØ+y0R~´6MöTjfÀ€z^ó1«Q×ÕK\'cÑáõ£P–yÀí]º`™vàÖkoPYİXvƒa a°ó5º´¾T§N†iM‡c#:&!+†¨èœ±ºÅeERI—Eæıà—‹æ¯Í¿ ƒ]×c?ö	§Î8X&ê’ÉáôI%&¹ÔáÀêÔø	*µó£†É\n?.Z2©Tb&Hõ¢6°WQì”¨?:XHNàXy+œKËÀ?î°ZÉÆØ\n:9¢>3)Ö¢O0x~†GšM±C®\'x<~ÇsRK{Ñn­ Ó¢%Ó~l|ÑíC?èÚ­k)Ô³²RØF	ÆªÒ½¨9Z:°\\Q%{ìl¥ÉØ«(l%Aš‚:}SÍ†\ZÎWgş¢<îg%Àcsğç®äe,£?ÁW¨%–dWØ;7,Ë¾dY\n8¥ú	Ö€õP‰_aês­3s±±ØÕ›ÓÈ¬èöpvºFY©]ã*9}…Z‡Ó/ÁN-LŸ“P7xô6 	™D?)öy—?¨ÙÀjhÕ:„µE&j\nNŸ¤Ë|ªÔA`¶±lı~2lˆ\0Ãã¼è©å½À0øIéÅê[êÕw¬„-+	+_¡ùÙ[€\"M„\'J?ë€8‚ÔcBÕ[õ½Yã\0.êÊ.êÖ¥hĞmâJöR*ùš€ô\'ğŞ‰8ÛÖ%¬ˆ’§ È0Ô\' Ñ5jNU Ã/RÇP·\0;½™®ûı¬æ1.{‚Vtã› Dİ?Æ™’›õÈ<|½Z7¬#±cù.qöèËe,èG‚?‰uƒÛì’Àádsƒx İ\"Ññôœ‚•\n<Oäì×²ÿµúÙrÈòŞ`ß¦™Z—¢oA?âÜ)ñ”?–?nœ:®F-ÄºRƒZÅœÁ2¸/SšJ©a‰©¦4”ÌŒ>â[Ú•0Ñ\Z”æÀ\nœcª\rêqOPç\' Ù‚¨¯X“tP†Æ?÷grf9Ö=$y?Àß³×…ŠşR5ªêñƒ4Iû?=?\'¨F¶*VIPNô`¢b°•³ÑI°ŒO\"\\Ö3ØØ¢PâQª+İ˜‹ºháœ†Châ?!¾E„[:	ğ;tÙµ›é	“\"¦2’D8€]²J©$Ê5<Q6á˜ÄjÃq³|bÿ&\rŠ6Vl,³\0l 47¸6V…?\'´‹¬T4V¡A)É3İ©ß2Ø\'‘:QÂ\"¼\"Ç\Z\0°˜Ö¶`šj$c°0I}^²?º$êPp€k`U‚ël??Zƒ‡?úyØ®ı»|\"|ĞqOà?Ië]ÃF¨v*	o4«vôˆß¤a/Gä_\0–áª˜E¯k[õ51Ñ†?ÂÃ‹‰T4\0¬M	»;{‚X Ù\r“ãìS¡âx®ëƒË€\ZÆºWXL7~?’Iqú?Ò\rá¢²7Ó?yÇÆ\r%Ö†0ke“¾\\:	,Æ8cyÌ6†`HŠğ*_±lU-8µnø‰åÍ½?›N¬å’ ¬$®B’?bw*\0V¦Ë&½Ë&“ÃE&a*pB9µ®ôè|ñ„Ştüœ tÌx…h+‡¾¿\"Á›½ëV€È7y‚ƒ?XoÙâ`İÁS™€W Éİ`nêœÅRëó½zlc‰u­å»Ix–2›\\l˜#şà®¨11×3¬jTÂÀ?™nL™;\"µB<9¤œ³ï#F?ŒKæ‰eP· @;`ä?u†q‚? àù?LòÈuXƒïtcÜ?ëQ«;\"áP³Î<‡:)B†½bPuSÙJgğ\nºmg?XåÊ¥l%cÙ?°\'À½íˆÎ7+H£ÏØVÌ¬\'–&ŞJd?KP²\'ìò)2©j•w$­p…°D2±±5k“/ËS¡+vjŸ?e¿Ö@ûH2‡Høc\"ÓM*\"?¦’\"™	\r¢#É&Ë9á‰lÜB0¥â4K\rjU¾gÏf8±.¡?½òµ·¸6ŠPZl5„?æÀ)fJ $C¦ßë<B e??$Ä;µóËŠÆdwx”+‹ü=I?¡Kİ3¹T¡NaÍÉ\r—lÃ<ÿN=ß2´ı3„a©Pjù,İ«$1é½öG`¥v¬ÈrNQÅn%ÔNß:Ô-P«-ş¦ÁÂ41?—HúVa;D@ô¶<ÊÎ6ğ8×r¿uÈ<ÆÄğÊK	T\' İ›L¹Áğì\0É†Ş‚:t¹¢\\c:hFÜñ!·?ìap8»°V Nä-ÖsÖ`‚ë6¸ ƒÆ˜:SÃ€r{6ò¡ËĞı1‘ææ@Í\Z\ZÅ¾¤Äª6H4N¹ÈF»¶H9‹#â®Wu°3x	á”æ$ª+Â+¡¢1k+F5¨‰ÔõøCpZúÒVM@\"z¾«\0]š¡A¹É?&“ˆ ;±ß?ßG6ÜI\r!Çc\\ĞN-VLÕÑ„ÙóĞfQ[ö¶ç¡ßö˜lğâJÔÂ«ShÙØƒıò@ø…ÛHÿÓnÚ®+b´@{¯)L=Ö·P\0‡R/¦j$` ’¬˜K&Aeãœ?nƒ\Z±Ã7c?£J–6¸x?{o3Ñü¨¨¹ª/©\0°Å??«$Nu Mg|Xô#™X$°·‹Î?J\"Y¶‘æ.odÆ?•àÇÀ?±É“Aê•$ğKğT·lˆ[ìÑED·ÖAP²w,Ç}+Ö1LˆÑbv‰¡M¹nÌeêà?9êZ?µ\r´©÷lt=ƒ…A5İ“h/%ß¾0ë\Z‘b­$ƒ´C0âöU?Á?€u?•Ğ¡–ö`ãß™ışV!\"[âM,lu¥Ã^?°&íWŒµ…±ª‰¬Z\"—Z\nJ\Zœb5ND(m\\f˜,M2â\0Ÿ!Õğ~’š„?+½i…Ç8‘dWf&l¹©\r4>ìµv¶l?¡HIJUÂ›chĞÁ–1ö	€—ÁŒõÉX<°Ô?\Z]°c2ø€:V˜ä´\rºJ”Z±;k”t™ìYp)kü­-›ºûÓ¬£¾Æş@Ø`‚*+ìİ^ÉÃa—®Â#<iæ\"0¬4_\0LB–sU\'#íoäYÕ@Lq†z€\Zz3QÒ^°M4Àò\Zá”Yü†¾Ÿ`_`bF=ñ ïQ7Í?ÁŠÖô3‘byÂ:cê‘:«ä{ˆ#`#Ú„v{Û„\rÖÊPºuàa…ÿÀÙŠƒ°mÁ®%ÔÜˆ?«R!Y1+’TËÊƒº‹±·`¦l)ùV…Ç8X@|ˆÃù“\"|xì\ngX›‰NYõ¢‰Öè²ëXé‚a™SÀ!jõOr³ßsÜâ¡ó aJÄ9È SY)«>g¦‡U§\"^À[D\ZíLı¡Å:B§»ª“È‡?tèLq™Zwœ¡j=ÎµH`±|ÓÁOğGh ¿\Z³xN„\0’\në†¬8åz³¡îtìyŒÙ®Ê\rj–È@Gày\nfíéÀÈÀÂLuOçKú³±4iÄ*gäZuE¿væ„Òûsî‡uÄ³2È‚Œ\'¼„‰<SÈ»5<ÎŠp¦Íˆ)H·lÈAíâ§háƒ\ZÉÍÔ0Â$\'n¥ô„…IJœ?5K\'?Å·ƒ\Z»0?YÑmA…\Z~gLEÈ¸¡?l¬{ÜêlXR‰\n»®maJK°¤Î0‘¤˜<á?»/A¤\0upˆ€Z“4{¦ëñ¤HÌØ@?‡Ô7f\ZeÅ3œzë¤\r]ÁŠFÉ*B$?m@\"¶‚b_|Iì´ê¦²Å‚É•ì“®C’ùJÕ	Àæ?6>‹IfåÌ¡¨)Zs\"TYW‚ˆu§Çs0¡L£ƒY·è_ÉY¥‘u!‰uEfö\\÷À òÆ–‘ °Ác_áqNÀ)=ûF>†ÿ?\n´N¿?Ú†XÇG5˜s®Å‰fƒ¾$(iâ °Ya€›(†Jêo~<Å‹êøRëPe1Æ$ŸÎ¬	ØÇƒbÙì>9XĞ\nV—\ZŞ mÔˆÛÚÃˆĞâZ%IÌ	Ë#œO$=”HÈ\0¼ 0È¬zCI± ˜&`‡9J\'vnĞ‡‰˜`0#ªQ?4l+0&^¹8ƒÇD9ÀBÆF&\'?i°4Ø©¶ˆ\\a>âœê°\"u=(L¬%˜†}°¯f²?V5LXØ*?g°PeN0ÒÀ$Uİ=fÄT8«rXˆK`{‚·ˆ;…±j yÛ‚•¸´•8NRÔ3Ÿ\ZŞ!Î¬2Ñ ì?IĞ³\nó•I‡ÆVH5,8bÁe?B;b?$È)Ö4z<Ó†2{%×?‚Å“Ÿƒşe¼‰ÆB{¸¥A5ãˆ®Áî8\"•*2?»œ?V?´DB45vzUVãŞ‡}(°gc\"XCÏrÕEÈŸĞAK<Í@µE€\0€ë@G283[Y’R¦?ç6ùO*+GÊ\rãb°\0`™ªC9‚U,b\'ÏÌ¹C`½y4ÄÊö0Û•?ªS%‘Lì	¼@—*Â±ƒ3ˆ¼áêó4„?Äƒ¡†ao°ğEV àcÕ\Z‘1Ç…\Z&€:\'<Ï	+‹v\"¬€?ÆÁ¤i@çÑá¤V²r°—i1Tà¤¨eUº\'l0É–ìˆ9F}\r©HÖÌ”{PµAƒ¢JÔVX*<&yo¤_«$Ğø˜ÕMã}¢?ç=T™7Ş$mï*.u–UxŒŒ]1ªn#Áyk¨BbM±Wp’–ª“P˜±2È³‘\\a\Z+,˜Ã‚W°ú@^à\n8ÃBŠÓèñ§!š.lT(\n¥eC½a„\"K€z^W8©\\nøvÌœQ‹`Ïï\ZX6€\\CèĞ±eeÓßÛ^÷$lÄbml\Z:y¤ıÚÍ ½‰˜\0¶ŠŒ?.Ô7¢>0ˆ°®À­ªE¨7{Å>%OdVoRŸØ`c¥ƒ2ÅµKG°RTìVÕ÷6Š:Ì ÇTŠ®í:V´Œï/±%âÀü¤Â€„\rPfİè§ÀdÅ¹™Fª›ÉÆ¼cØ4ÔÈ¢&\"®ÅÀA¤[º43.[¬Wáµægi-R?Ræ—ÕTÁœe=¤T°tZ ¸‰O_wT\nçD«¼?ÊïĞ?‹Æìäøˆ”\Z2ÓÍqš`¢KªvX}ØåL«¶„Ğ°b?\"Œ™êä1qpÌå¦‚fDİˆV®²dÏBx`fÖ‹}ÅÓÙX\0zkê1¾xÀƒ&#Oİ`¬4¦Náä1îQ×šõC{R\0›‡^0€vŒl4ÿ3òûÿW¿é$Àc,ƒ	š”25¢@‚ªz\"%Öè»\"Y?ãw8)	t—Ó •\rª­:á`¦Àˆ•\'YidêUó<à5¥¾¿á½-÷µ<Ï¬>­ıÎıÁ.?IB3Eò¾ÁLnX›`¤JPµÎØ0`µ6]EL¢¢†EÔKœCà~l#ğÜDĞ?å+Ú°sËığ¯&Ş\nR­\nÃ€”<¿ãy¬„CÇ{LòCmo\'(Ø°\r—vÖ\rİKTCn&k\"0\n³şØï¦küA\\—™uL?…YÅ†Ó.a’FBù?zû°x‘Ş! ü.3ºN0Lˆ?¬±0Ëıì	Ìcœabƒ¿F.¦2,?d¬C. –©?Ü?c—’ßñP¢«;6*.7”ğˆVêÈ2¼öÎL¥¹©/`erè	s”²ach1Å8éÏÇZB„“ÃJ,v5÷l,ğîr\Z“½?óX?òhÔ+Få1`]¢$É1ä6xñDçµS¿š÷×†é15„ç4F?ÂõlXs\"óò¶Û¨Ó?ygÖ ]‰<ƒ*ÇDIÄ ËMr‘[?AZUÀ¾]`h(r;æ÷ÂtruVä(\0`â\nb½\rVïıR¨:&%¿Ó…©E°oF™?Ár8Q³Á>%®7Ó¼a…¢©ŸPĞÔKícùÑt0*ÇÆ?¼”ŸlŒyø\"öG?]“£`2ÀA™\"?\rÑª*+%ÏÃN\\†	?ë2ÏË1ŞÜ¬Ç$é`Ò”¦5Üõ%öæ’Î1œyÉd)5Ğß•FpkNBìÓ¥ah%üB%?CxŸlÀy.ùJÃÊ0‰à9r¥å=¨íØ’Œğ\\y‰•¥4ød£”pçı@d•yŠKÃÏÓnƒ¡Gñ cR®*?•©¿¦–v`h5pWåv?&R’¡?©H¹ÏÙyŞK»T–y&©&PÑnÂ§‚s´Âj1ƒsMUs?,Û•YÇ@Æ¥´ß÷3	rc CUH~P?òp>†ÉP!+V‚\n¶Ç tHŒj ‘è”ŠÎ¨¬3ˆ¤rÄ8¥3ÎI$…£ÀÅ;hå˜€½m²Kìulx?™ô*hıX(§éšä1àÎ¡şvx¾?aLpò;õˆÆ?Iı?!8<¶ª6ìrÌ{é×ñy¡²q„_:Øä˜ïDB;ÚÕ“¹Å\rú~h4’²‘Ö÷y`#AlşO{y¥<qÆÊ`a³îX°B…”%çuOäÈ›à¼eb0â4sI?¿ÆyÆÙo™Ü?påˆ	vdöqä?pÂ`ßŸ“ö¿ÁE,2n‘rÀÊ²¥I:cQªƒw_J€Wø\r’.X§0ƒR~¸\06% ®\ZS•ó\\;9¾\\@	|t`ù\"?Û?İ‘ó|œå0C¢İÈ1‰Z?¨?F\rP‘[P+\n[?9Â}Hü`Á4´G„É,\"ÁV¹¨&J±ÃÇÊ¨U0õµMæ÷Òîçy(6¦×Ø\ZGR-sKcç?–×=k<ãÈ`‰DğEV?ˆ‹½]¯ıáZ?æ	F‡\'¯ƒ”Ü?Ó\rR,‡³Ó%Ô[L²23gÉ7\"+_bo‘XyRÁıLÎÄ¸1ş%¬mˆ=‡ÉÜ%ÆUrä70È7ı\0JUJ îLêä÷3	JC‘²±Àş,S$p„?NI@n:jR\0˜LîGÂ%³Ó›ÓÉXÓP„H©ƒ“£«Ù;Iåà¦t5Q£¦@‘\"¥¾¿æãk$WÍÆˆ<RBƒ£FÕèÔğø¸šAC0†”¼?É)˜”øI2œ…L–!?^îÓú7l0›Ò~×ú4lÍÙğ½D€IÉy6„\ro\r¦ç!B× ;7¨%\rÎ9‡piŒH‰ŞDŞ_›„ë,|a×™B¤±ïb%k,é\n+|Ã¤mŒÄË âÀšÁÂKõı­9ï€º·™a†€K Œ[œ©-Ô-m¡ê´ë,<Rè¿ø(óR??ÃcìÚEú¿?Æâ!Ş‚D°º0ØZt¶VY©]køo6ˆ-Ë#(7·k-IËcËrhĞİ‰k„ƒÏµL&C?¶ƒaITŒâ:«ØœS®cã\r[?Ğ&Ç\nçvöp#Ÿ¢±IBá !tğ:¨Õåy\\Gcw¬À@\\Çà„½AêI=¢¢½CÇ†¹+£š”<—ì?:r×¨:€_C´,êÇ­ce€ìJJc} ~½ªC˜x?aÄ,×Û\"ißMÂETÙõ¡\r€.·}7ÌíÒœ…?ƒ-ÃÙ¸ê½İÏuLævëA÷|o\r&ÖCg”6ı¢?@ag¿/õ8‹1¶\0t¨&@ZJ\ZÁÒ(1Ó{òô=ØgNoåƒÅP	`F¯Ø@a3´\0C§7×?N±Œ,wÀc8°¡3?÷`Œs•óœÊÜö -?{L?Ï²#/ƒ”<ß€wlğ$éÀ Œ^Òâ/?À˜>å˜ûØk£kè[:Ö&Æ\rtú@?\r‹	ìt\r´ç@»˜ófå2Á”éÉé3P¸ú{Lœ>#¨)3úEÔ\ZP°çc\'÷CÄœ™aseïãôj<$³$-*±=À@u¿ÏˆHË<‘rPÇd tI|(Ï£ŞFñ?}ƒ÷Á¶‘¥¥?öÎX©3KNyhRò?dA+öP®#Ğ?ğL9¦1a0û,ô‚rLå\0p?¬Ï“û?â1™úœf©DqÇ“®É?e“û?€•È®Ü:\ZÃŒ€ïŠÜ\"×à½ÉAæ°\"ç\0År`˜`=¦Yù?RS°JÉsé$’„øÖb&\"²HmêsÕi=qr÷áœÌ??!H®ç<¦Ü?â´¤\"x¾1Ízh=á”>Ç)	$Ú?lÍêqåù@œsP xÖ¥äz<µ¹õCk¬ä|\'ıš7v½ÅpÁ4\0=1ÎÌG>·~,†?ï…òÅRıbÊõÎF2I	p’~†/Écâ•r)ŠÔc Ëy(ƒ‡€l?ı×ä2+Àœ`öE¥“ \0.Q8Jf2üörÌıxJ‹@å¢ºÍ\rVQ,b\\Œ,ŠçT¶³ˆ/;\0¬3Ò)ê¯NÑBßHc†´ç´–3‹ú´VZõ œ¸ƒºìËŒ@q€jä#ö%“¨´Àzë¤Şêc\0Ú‰%““¬¯\0\"’_Ùcbõ%ÉÈKØ-JeÀó¥QÑ?à°ŒÜG»’ÜÄ—ĞÎ¤+ÉÏP6<4hIòö’ö\"É…/í»É1fÙ\'KµzI½i_(a*V‚T‹à?ÉÕ»\\ˆ‚\n!ÈW`Ì*„¦SoğeNe±ÍŒ—ÊHÇˆ4¬¢¡v—æ\'pÆJ]l«ø¸\n–ì±r3á?HlL©¾\ZHaJX?ÍÄŠ?ÌãÿƒSIƒSŞ\Z_%<€@|?IÕÃí1½z²Iz§Ö)ùİñ¼Š²¤$X…N?WÉC‰\"¥v\"&Öà*náù xyÇ uÎ~{„dvÁlCÊ1”ğ°4{—,bKƒz,áŸk¨’Õ1?[xL¬R´£Ppï,{\'rGû;Úß\r)§õñ†û\'\0’+ù]ßëi\'L«ŞÏ“Ó39}ƒ iŞR#½1¢Îx5Ù+åwı~’É{’ÉËÏÔƒÁK*`)yš‹O*Ä<”óŞ\0ŒıfÒz&¹ßß$ÀcìA_ÊE¼ô\'ÔÖŞÓ¨Şğù,g¾WIbA.	g(BrKyL«dÑ“ª7(4&U9æwŞè„@ã.^Ç êìò„ãùÀŠc´‰Ê{’ŒxL­ÒÙ\\§v~9÷î\Zˆ#·™\\G=ÀñbtƒnL=	å˜÷À\r!€\'‘?SöC—&:|°Ô£?6}Ë1“‚X^_+µ»?´kD·èöyò\Z{âC¼å7jb–ë1eÒnÑò@ Æ‘ğÏGKÓÄJk ä¨Y‘Á™ä$lôd»ôÑò=İ&êp}ŒIØ“4İ“¯ÚÃæí!8ö$÷–c?˜~=Ğ{)i‡Ş öD2¶K#Ë<cy•¢q?.ûÄ $HFÆu¨?û¯Ç,çù(yu}BÍ‚Ní~û8Ş‡™è„U	¦\\ŸXÎ“¦;’ó4a‚©´û?Ÿ§ãzÎ£Ş%?‘ö‰I¥ºO‹Ù)©\'1¸‰Î4r¨¤‘]@n«‡?[Î›©?z[ç1èÇÌ6z?÷±ç‚¿ß\'tõÄ IÆ{ÄòŸ\Z¾¿ãzŞï¯ÙĞBİîV€D?%x£’MªÌ›s¿=·ã˜öì-öWÕh}M;X¢>rÏùš=RÍ`$éºÇÄ-ÇÔ&o_[ÖRÈØêE7‹ËĞú× H?\0®YO†9æyõR©_Ìcllèf5êÄ=vuoâëÆf¸J¢šN¨ÑÉkÜúd»”cÊ’å·ÑœYÏ¨¯-é6?Šİ=5–ôÒ¦Æ2Æ—\nå6ü>iz’±T74jƒ•~\ZßnİéJ@D•Çïƒ[Ö1{{Ãäl?Œ£½}r??M&ORO’rO’”ÔËR³*o?o,æ²©†Iñ?”<u©?¿§Q??”ÜÏ$´ÌC?åqfĞ6p”6 ~{’¡·|Â mì-“¨EÍl£F?x@yŸ|Ë %W™·l¦-íĞ&;#+Ex?êá«(½\'™·Åë[c´3šÆ† ,„rkù·2-BzK)—Ú{c¥nY†Hcä‰áôØç=vwßÁƒoËkÇL&ÃŒÛy‚HHÊÜ¡cB}.çõ#;6–?7ŞJÔ—S[Çàë?:b—´@Ezêü\"&V&‚N|ÇÆ°c¯CÎ39O}Y‰ˆó$+ñ:81¿»»œ×÷““KÎS?«+jgíÇä4öêx’yøŞ’X#){ãt-í¾’ûÎ)t%Áîá’çp“¿Çt‰½^îÓúõp™Âç-o\0è_ß#Ä€†{Ãñ“xÑ÷9×°úõ–•“AÛ3™IsåI{å!G“ë!oCÈ‚\Zö˜¸=&ñ?‘Ğ¼ÇÄî1±{‚¢<ô?Ş2ëôŒã5°o—î	¼ÅÇ¦e>ï-!?ö™Ñ(×$!Ğ_ÄØHìë;º\'5§\\gÏá#Xö\Z6o¤XCm%×!I?Án`ğZ†–I8Ğè?Ù9ö{ËÈ9ÀHá*×ixef?’Y	\r\"^°§ Éò½Çe¤Ôx‰¬T.Ğ˜\0ù\rBu\röò\0ÅHÀTÈ‚2­ ±`ÀTğÈyã[†É23¢\\X¹sû^¨wr‚frr”aª\rÄ9„œ|Ò¹7ÊÂ`+îƒp€ŒB?ìœ!‡!0‡©0W!ˆå˜rCNrğœ°È¼?ˆÒ3Lµr¬‘y„mP³Ròœç?\00ö3	ğË?<t ˜Eí¬?|ŠöPXÀo_¨u\'`B\r˜PƒÅ¬¥Åšò\\8%H©\n?\n° ´Si(\"Ï‹ÄÃ„	5‰<À5Çd|!0[JÎiUË{[Ş£?â¡Xd”³’z˜Ê5?ªÔe–èN“\\„RW\Z9¶ˆ4ÂEü%?ô [¥L”:0‘†²²uşãR-á\"÷Y(IaZ;v”Dş®où?ÉA¯Pê$`\rdª QC	ËGEûVä‡Àd*«?L{ı_Á9[1i*úT©”úı•šØå˜HCØ.@™˜¹Ê&R)©IB`(”zj;UPU° `—z.ÍcìñLšfÀä` A¦K)¹ITY#XŠV£ßSçUÀ„)%¤MĞù9M&¼÷Î2ÀC·hD´Î’„0© ö®â~ò+?Î(8(RH/%Çü¾HbÅı–n‰°NGÆLœRr´’ïc²:u6KŞ?I48ÔœôZ~äšï$lĞid^p?S\Z»µ%LDÂ»ÆH¯ 9kù~ËŞÉdÆT\ZÈ)Ç´ç`%<K@±=+›£_+Ÿ£¿,??ô„ÇzC…²İÃç?sÂ;7&W)u“Ï!x(g<äfş#lVJÎ“)Ç{È¿¼?\' ŞHÊ`ï\0|X\ZYæ-ÆØÃ8‡h –XJn&Ÿ.öÛ€ıVJ?‰Øqƒ\'Í“\'¬Ñ³Œ{ƒ‡®HmğÊ[¼±4\\×Rxk0ÙJÉy:ÕCé‘`ÉK¦•48@¨@ËÒ(…b;Ş?ÌÁ(Ô?óÔX˜ŒP¶L´!(?~t^`²:%0¹“2 V€\rA7†Rò~ÚÓ¬œ†…ƒ˜í€Ä%+¥®„ÁÒPÁv˜,?IÈF\Z,·\Zí\ZŠ @¡ã>&¦Ú°˜—!S^H\n›‘A?õ/DRÁF¾“¨{y²ŠËy!¡Ô‡?:€\rX¥Ôñ-«*jÄ¿tmÀ¤*Í»u?qÀ4\ZÈ/Ho$¥>2®?ìãÑ?ÉŞ€Æ˜ÑBBíÒ\Z`5–r ¬)u9…Y,$–oÒ*8P¨Ö€	1\rïQëJÀtŒŸ)¹2‘D“ ”*	jO¢ók&K?Y`=ÇèÈäX$ş“zrKF?d«\rÌ¤©+ïìXÛßP[ä$fß]£ÔL–\ZÎÎ\Zjy¬{?ü\nV)!*€ş±F-\"?„”:ˆk¹Ã”ø	ÌM Ï‚”5%ïW.ÑPÓ®ƒ^³a”’ëiç\Zµ³&umù$X‰\Z˜ÔNL»Rê÷4ä k4?ƒ”‰ãßµ>\rê{cŒzn?+ã\0Ä6@u\Z¨À?Ë1?îÌp º¡QÊ?\0K€Ã F§?F•óÜ³Œb¡I|ì˜å˜ëYAHO`¶“c®×ßĞ°â`*”’÷B!Ò 6¬@ğ$…¦ã~Ô>ã?ÂD€ ÁÁ’g´™•*I1-†Æx™P[’L\0	@ˆ¦F)¹	Ú¢VX’“¡B¿ĞÒ-“©U?I\0ıŒÇT°”‘’ßQ[6®-ıZX\n¾“Á?‰2´5÷Á¶A™Ğ*l#@H“ZK§ÅeÀdŒghÙ“`â-š@Ë¨íìØX> €(y?ÉXZÔ7ø¢Bkí?z×eK	y=cùQoîØØ=–óÜÌà\":òóùŒ\'÷³ñÁ*×éË?‡Fj,çyŸ×I`˜?‰É4`2\r:w‡ZÑ1	º ?h|öP‚ Ç?$ÑÓhèŒŞ5¡£a°@™¥äù4:&Tyç‘¤#¯şäıH:L£¡3ÚHÔ°ÎÈ³?„–€°WgSÀô,oAÏŠÁAÀäH),ßr¯Î¢ĞÓŞ=Ìu$Û–’÷À)‹É4\0}–’ûÙÈÂzËÃL¿õê?’ç°’÷\n¥—ûì~~GİéÙ ÷L*LÆª8hPP Éz€*ôô·8÷¿Ii,\"=¼MĞ0ˆ\Zä˜÷\"L-ŞP.E‘<Æa`?9ƒ%ãfĞHÈ¸¤äz$&O)!?BbèÆ?3ĞÈ˜ZÃ \0º€)64ä\0ôXJêÅ†(r”XJíL©RR$Ñ€ä?6À[$¥åÁå»?D˜\\Iµ¦Ö€‰5?ŠVJ]ÎáÍ˜†¥Äi—\'ì\nRBı¢ƒ8fÊ¶1?F£<‡×\'fFYğ;œYÉı\nE@‘cF7òD ÍèrÌ<¿CPœ)5»”œ\'w[F¦ nÚŒAktÌ)l”PV‡\nYÊ’’÷	‡(bÒ?™²‚GˆbÖñ>¸S3¨nà?Š>DLºrß ÷C?CÉëOÊ’Rë?WKQ¤ÁX©=	İÈõsK!J¦õœ\\W™n?\"ĞŞùUÄsc«&#}p»rÌ¡Z?Í!æ:	b«1,\rv)íı|?›×|d£Ôä°/DìÉ‘¤Î1W‰sR”æºÌF ¾q´7Oî‡á,ïø.:ƒŒ=‘L)rÌ}Ğ6véÉı\0?GìáÈ¯ìsÊ‚ÒQBŞEªVLÓÓ´”Ú?Z@—Y¨d‹#šwrÅõPÔcrĞFJÉóiÇ‚T°…ZW\"èŞÛƒ”¼§ şôw±˜Çšëá}*\"ï!ç”îrÌûH;?2bzEOıiÏÂØÀ!Ë‚˜AJ­WIÿ—™?×I„	;Bø!lˆ ƒåØ~_\Z^ğÇ’¬•Ø³#vîh$U°6DX\Z\"”í(p„­!b?$åˆÒF’8Ç‰An¯XêÆ6õ?$è‹¥etg&!eäyL¦RÉ»\"áX’Á{y„İ!)$§¥e¶ÉµJèş°ŸËïÚ©?]2ªÌ®£=Ä,Ş%\'¥Q¸3¨-ë\"„½R_Î«u)::­dòTŞî«¹N;So„…!V?÷3h-™HE‚¿*ò^2Áúa—ˆ£½r?>ÿˆ?]~çş†ßInRÑ_•:Ûb…mA%K™ñ<há¬­?èU­4’¯\Zx.ıã? †£³| œ²?Dä>~gœ:ËÀ„PpÕR¶‰€ÇX~ÔJx$?â„8ÂÖš˜ÕèÁ „m!bo?ØË#Ğáè,ıƒÙ)\n2Â—a{ˆ$…ˆîM‡ä&Åk„j;:KRW©c%MO„Õ!:$âèu#½&~‹ØÏ#öñHÍˆ}[®ç|	?j‰W?Ee,Ì¬Ÿ‰‹?:z$)¦ãm¢”:ˆ<,Ø¶\"{˜ğ`i?ßyê¬ÒÑ³±°çòR·bRd!q¯ä]‘dèr^ß`´¦å‹C„$+z$qPƒB„s6BÔaˆĞ/FØ·#(áû!bˆ˜¤#&ky.÷‘Ş‰ÔÀ1X‚G\ro?:Èy¯Ö·ˆ©ZJ~wûY	,?q@íz?ÇÈ²?Ğ	?0A3µD(¼#vé{D„=aç—ë¹ÎˆsÊ±_Ç`ìÖH”`?‚d%I…Ô“:Û\"TßRr¿ZgäwÎ³ÌF:+²ÒE$Id°G$üøRr]iƒŒçÒi‘dŞ±â~Ë»ŒZuCa£ˆÑR˜B¨Q\'bâ:V°ˆ½<Ân!÷QxŒÊÛcÍı$Ïˆµ]Ïs^±¡ı?è±Q!ÕI\'%×YŞcV@Ø*¤äyî”ñ~$0&õÕ{´tO ?#?æ+EL¬Ä‰L;‰d ’#~‡ˆé]®Ë)yZ‡\"hç[…üÎ{Ôc-GZrKtÁX©º|BWLŞn¦ÎM@œc¢I$%•`eHH¾”8_óèô©Ö?9¼¤ä=P?cç—’úĞy‰I–”:Âr?(Gü\01Ñ)I=Ú±F‚ÕL‚ZóøF ¾è±”úØ1bM#Ã­\ZkÇuÊ¯É#?úÆ:ğ<Ú¶Œˆ Ö¨…5{\" ¿±f%­i§\Zæ´š$uÍ{P\'ëZöıX[N9ö>ØéeP_tìšö©5FYÎë{\ZÔŠ\Zaƒ=_~çzV”š•¶\r)÷Ûûìy2£ä:ÔÂF÷4É„£eÆiô°rDìı±¡ı1ÙÇF“œDòZGPÂ\";xı[Glü~6ÆxŒ#Ğáˆ=?BÀ±ÿK©?U‡”\\§\0¸ˆı^J\"¨ĞéKëC§µğÕC‰[å2?$ûØÁ#{wl-Íƒ»vlƒêÔmà¹HÊÖƒ?5Úï%ÏW›ÜÏó@KLìâRr?“ûwlYÉ°GX4bËFºEnÉÚª3.¶–ô\\y{\"vl)µÓŒŸ¿c%ÆTÉ¾)å@©÷“Ş*aÍ“cŞÃ¤€=CJ­_‡šÖyîG]Ã¤-\0Çô_Ç¤ê0<`Š–óÚß›Euaµˆ°kGĞ´±cRv¨cíÒ±¢ô¨_˜„å¼~/hW){Jb˜?#œ¸Ó°”eÅïû1‘\Z+5R&J*Ã ì¤Ø‰#ÔRR	–-Ò!EÓ‘?øF Áûq$YxìI·d„½=±G76â^ìÂRr{?^Ù\Z\"?ßHf)yË?„¼rÌïtf¯Pè5‡”¼—Îƒ}#öÖ9ƒ?§T(o¤ƒvÈ­,(eIÙRê X? F-Bë;@ÅNN9)y†ˆ¡Ò•pÄÄ-¥]Çs˜°iDX<\"ÔèRòúu \'Û€ú‹é9Â~1AÇA#ë\"„¿q`/6°‚½?74ï×:eÄ“`R uhİ»EÂTœÈÆ™Hm›2å²M˜œå:EXR?¬ä~k·a?ãhÉ¼A-Âœ€ò&ìÎ	\n¹.ñP•Ğİw\"wXÂ?HÆœ°;‹vÅùÀsUHØ¡”	va)ir‡e‘çÙstO’2’Dd\r?G1×O¶É”‘áBà”«?>Nd|‘RßÛD\Zœ`‹H¹š”«Ç5YöIØ%¤ä9PÁCà› øMò¦\\?uRó>¾#\'ƒ=&İ„©WÊœ2éı-ïawjÂ”›rË7aß¯“:åõ\'\0„	4q‚¥!‘…S¥>B])uğ¿3N0Å&k’}\'PÅ	±ÜŸñ;ïIÔG…_‚à aêM(¤B¡ôRò~İø\'¸j¦ÕT’Ñ¾,ö³\'ÀcœJA©ÄÁm*-_AÉ13«TÉ”JÍ^(ûıˆ’ä¥îÆåwıˆfSYó;ù|KUGäwŞOç•–5’\\Z@|¦ÊQ­kÎ³’Î2,×ëGW|G•Û±Ş‡é3Uj§Od¨O° ¤JuÒ„ÉPsyÉ²+İÈ1ÏwÜG{`b”ó=Çt\"ù†+…B\'ˆl„µ‰tRrï£]?§\naQ)ùT½š°º%¬u	ëœ”2Rêûù°ê%6ËÑ? V¥l(¹OWÒ4¢W—Oâ)x>ıBf™!@Âz—à\\•’ë•2A?°ê%gIahwO\Z*¯~‹02y]	¥l9¶óZ_O{‚B•ré$ˆ™9ËÔ©’0íÉ>É?DŠš<9£<?é5(BJ^†$ó4¦GBû†ç0@‘&¯\0®D€Mš `•’?ií<ßŠzĞˆ˜ääXÓ\\‚ˆ5aêK˜ú«r¬’_‚ A %Ï)x9Ü+EPBa);µŞ?IH¼?„?¤\'I\"˜\0„\0‰Ä}	Ÿ³D‡ RSÔ7ğ~U\'„rl¿SßÈóX™+jH:	80 ?’vj¬¤~ö á?Rrı\Z5ÆÉ^…÷!ü6…?ûI·…Õ/?VMQ­=)ÒşXïä| T!Á€”ÜÏ$À\Z\'%ç÷¥æ14*?W)õ#?²& ¬)Znª†—jöÀ„‰-qM±·X_­DBò’ª4?F•Rï‡Ú<pŸ0m%Lfr¾¢Ì([JÄÒ\'ˆ\\Sb°‘TJmd]å˜z)?”v=Ïu\\ÏäÆº•ÉÄÓbfzŞ?ÄLtnBíÀZ•ÈY&%ßÇàH=÷œ¨ÇÀıjÕJ5)Yk	ñr¿JBĞ­©fµšj¾›Ô«	n×š5Õš;‘ßYÎk½°~%Ğ®Rò^V¶šÜhµ×ş3Ô+yœ°©¦?@­&¸aS?Z\n5›@¡JYQö”¼ŸîØ\nUJû?÷“y©fï?õKÊ°tà1N¾\' ª©aPbòJ¸§u©AMj4²HÊÀ1÷±Ü7–ı’e¬aƒ°65jgOÂ\'Ë\\Š5aúJÔJÉó™¬?æÑM¸Ë1×±ü7bPª©Q²”<§áıtJÓR_V$Ğ¦	jjè¬ÉÜô¼ßP?¨a×Ê±^?U,5ò†Ni4IFjÑµA¯¦a:U[JOù]\'AK.°–•¯¥½	”—c_ØïV/Ş¯†9ÖúAd›Èù•ZÔ2P´Rr}ĞAØêÓsì?äNßÓY½¬?òI©ïë/]Á1Â·c¼ã:VÚNı<‰˜õDN¼ SÊó<o?ãˆÇ8u¨Cº+?îrÌC‘ º\'P ©Óì?	‚ÙÔÕ|ËOÇrÜ%ÊšçÖ<G¿¥®å<¾ôfÂ$—„Oƒ²k¹¿ãyH’	Ú‘,¤CòZnµNai$œ]>)õû!‚M½Â	¨Ç„U+?vL½%Ğ	´£”ÜÏ¤ì÷³’ô|¯\0¾„uKJÔ*Ï}èÔ¤¡J$MOÌ¦^cl¥ä:$\'\\²²ĞpºvÏ«7¶‰?ûÑé‰-—’ú!±I‰z“×‘YPÇLæ?I6°bc½µH¿s`å€™°N%\0˜	\0f‚6s\0`JÉs½öÜ°Rò\\1ı€µ+\rõ~Ô!c¥*¡}j11Y(,Á9?b¸.ÿ+\'n»åşÿÙ÷/?xŒÿ+}Ä-÷ßrÿÏsÿÒIào™·Üÿ?ëş¥“ Ü2	n¹ÿÖıK\'A¼eÜrÿÿ¬û—N‹16ŒNÊñèš›Th¥ãj€µ!ƒ«Ô—Êàq–áã½eR±Ô¨j:­ÈpSam¨ıˆŠL5Ø˜ŠÌ1Õ Ö–ŠÌ/a”a–Rò(“(+¸¬…`Lá‡ğAGÆ—©ÇÚedĞÉ,_±Z1\\n†Ò\'¹æ`8s)dq÷»‚Ü_	û\n2ü”Ú	R*yW©¦JWMAJÄ”+ÉWªug¨É\\ñüŠ8ğî»j û&Ù%aãvP»¸@º\'œYÈú€¿a\0Eë€r»h¹èÈˆ”v¬í?È2šjm?Ğ¾ô¦Ãä?\ZÚnT×Ò/­¢ˆ4 ®%]TGäNß?Ô–F‹~ëÈÔ©uMJıî´LX§Ü@öN¬Jnh–ú	RfíáÜÌ`e&fÔƒİñ™ı­a®N+ŸÁjœk%=É¶=ØŸ/¢2•F‘AâK(Ü+àù\nJï\n\nrg¦×Áã}n|ùL>ò xu–Éï\\Ç¦\'Ã‹wğáëàŒ=Ù‚Zc<ÛRLfış ƒQæ¤¾?®°JGà‰£ğÑ(Äac­\r:}oROğPç°JÓN	jöšöÃùä?jûšz×°p×=¬Ì`¤\Z(õ[¸9[Ø¼[è4[è2A±úNy—<&n?iÛw$!gšÇ$í1={ˆ|§ä]@~ßó¾$%=™}zÚ“Øq‘‘Ğc*v)ddPûS‡!«¡o„‚%‡%wäq†h ¯˜äE€±\n„¹†¢YÊ@—ğË?Ğîé \Zj£ĞÿÁÎ@¦’\0Æ\'”ê,\nd.	„y 3I L0?:?$:T0ˆU°¶œ1¢A÷çavó°ødd_?éä’zC.…ä/Œ´	xùC„z$x‹tĞ\rğ\rx`²h†”¬]à;‚Í`ôt÷ƒÏÈÂğ\\X¨âB³\Z‰‰÷B¹PäĞ; õ‚ƒu€*=–Ä,— @KàK‚“J‚‹*Ğ¿UC„Ÿ³pC\"ó4ç×P[¸bC;Vü„;Ä5€­‰ƒ)Ö5q?Å0ù‹K0œ??û\0”^?jª”ˆó+’èp€ê=ö&Llœâ¬Íğ ¬å<\0Bü¤ßJy·J?Ìcª” q.‘)]JĞŒ8]ŠÌP‡êq$P<xF	üNp‘&Ò9%Á¥…ö¦à#àMt\'ºS?\'’´LRò\r¯”?¶óÔ\'Q?ó¬ÄcZâÄ)T]“ûÔYUâ$#­S‚C4‘n)•ªF%8AS‰Ó¯;gg‚“3U%\07`ÆäT½’÷ë÷“i%F™B	6 bÀ£ğ¤Ãâ\0ûÀ\"õ‹ºÊókJ`“Õ`\'	ØDeğà8ßà—’O7˜#(ã¥lğXkût´Sç­}Í)‰óM=î7Öé‹\\ï+ p(@á rˆ\nr³Ié)÷GUxå`+pîIYœwŞšåËkvóºù¹q.<ä¸G=&+²xúƒÇ©0uËß/èoıº£òÕquuôúukgÖn˜]½öÿ<Ë|UMOMOO—y6)+§Çò—ç•ÓÓ#ÿqæŠ,+äTQ–nj:ûÅWeéßí3Û¦§§ÎØ²cavãÆŸxİºÙ³~ÕùeÿıÖüæµw¬›?>|ıºÕ_¾ïñ†Ù?[g·mOËù¹u³sÓ®OéOØqÍI\'\rËKNÌo?~xßw§·uûà~:—»qûì?¯Nìû“{Bÿ“n\'¹vïŞK–/—ù¿|úÈéÅ!*ãq;mŸŞ27½uËüæ©êôÂ–éÛg·Mo_Ø¶cíÂöéùÍÓ;7Ì¯İ0½°avzãìÌö…£¶Í®?İ¼°ñœ£äÂu“çÎnœİ$§¦ç·Oo›İºqf­œ—Ç[fÏ\Z?7ÌLOŞ=½i~ûöé™9yÙäw=¹afûäAÛfÇÃurñúy¹qzûüSfWSÙ‡lÙ°yºß¸qû–ÍÓÓ+f\'ÿ8vã±.«×nÙ´rzúk¦Çõ(O)pÓI³Ûä=R±?óî\'§–-›^¿víôQ[¬5¦?Z?}Ô£g6nœ>ª;©ÔIKZé¤ÅjÊcæ7mÕ?•ZqTtûüæõÒç7Ÿ)§6ÎoŸ|--²}Ò:rïôÚ-›fæ7ËÅr­=6öÌ¾Í-5?™´â³ã…›f6Ï¬Ÿ¼g|Ìbk­Ş§^fgÖqómÚ7xüxÓ¦-7î´}úlÕôÌæu‹Õ?Ù6^–¼tr³Teë–íóóÒætèäÇOšÔi~ÓìxöœÉ?ã£WOï­×Œt‘}øö±…\rÛfgÇL>^Çôºé¹›×¯Ú~¿éIG%ßbç&w??9¿8”ÖMî^7³0#-µ0ÉÛ·H}Îœ=grr¿ÏØºmËYó2;g¦7ÏîÜû\0kÿ-ûÊ±~‹òZù…&>cVÆæ¾zõ~ßµmvœ0³ôñ¾ïÙ¹A0?võ\r^²yËôÆ-›×K“Üx\0ì­?ÜO›^ûXÙ[vj‡®ß¶E¾y«üs|„ÔfëìÚù¹yùqÓÌÙó›vlÒ	7=]/ÎÏ}Ğù­M/¿m¶Q¹?±°?HX7¿}íÌ¶uZ…M3gÎNoÛ²eÓôÜ}ÃøéÎ\rë¶YÖãĞ8kfã¾fçÌ9Ûå•sÓ3‹8cvıüæÉìÚ2·w˜ª˜‘‡l›Ÿ•\'¬¾aÛ­İ±mÛ¤Š6¥g´ãöm¼…m3gÉ˜µÉ.³|vfÛøŒíRÊ´Û&_²O?ÇOÜ·9&_%ÏÑ¹k¼_Æá3gl<G~Ù4¿ ]&\r7·cãæÙíÛõ+ö736¨lLzd»vıœå…ÙÍÛ÷•<ãÇ½üË‘J?q1:úÈŸõïèq-¹Q­nâßäŞŸı½“Ul­Œ¿›½ÚKòÓúu“•ìôQêœ¾ ?Ì‰vrõŠåËDò,ÿÆ6[µ|??‹Ã°ùtùiŸ{fE¨L~ß÷ÄŞßOÔé½ïœš^9½üÜÅë«5–k¦—/_6‘ÇL¯¸ñ+×¯;~œ®kWŒİ2w£V®\\£÷õ€MÛvÈ~òÃ¶xj2C?™‹Åsû|—ü´ÏÑ?¯˜|ÚŞK&‡7¾Æ>nñ*NÈu:O\'RÍòó–/?kËü:ë„N®’N˜Ş_kL¯Ü·?ºã?ã?WM¹uÛìY²‹Y6Ê{¿]^)ZÉÆÙã+§åËV¹bµ]¹rrÍQ0<¶á²ñ¹ò¼ñ—ñ?Çë…›gÏ›Gj5È\"µbÅÚ\r2q?\\9Ş2Ş{Şòÿ2¾våâgï}š]ØÿW¯šÖm¡š4Ctş˜lÍ¿×\"ô<Gûœ‘;Ç\'Ëc´Ï½‰­\'ËÛŠ·à8N–4ß*[^Wê?“;çåßÓã&S6o¹»Q&Şºsö£yŒ³uüÌ£G]oÔ™&Kî–­£äšˆl~?J»bI¿hw-ş¸ïWÉß¾ÓÃºwì³e6Foø9ãÏç1 hHs7mŒÌß÷¾:$x™Õ7jÆÉ\\’v<‚Ó³Û¶mÙ¶ro;ÊÆ6<lQIĞ†ÕÖT?cç¨ÖIÕÇ;M››ÌØq^·eó}öJÃÅOeHœ§/˜Ÿ¾ÿô^Ia=¦wm_˜ıwTÆ%Tô˜uë&vòÑ“Gòù+ö32¢ÔZ¼f%gÜÚLŸ»÷µ²Ú‰Öm{†ıê“—}ä¤Wf\']rÆ¬hû²òMê\'ºë\rFõãâğ \r¬şÖ¹7GZHÅêpı‰Ãq?cñ†O—óûDzmï‘(?÷R=Ñ™·/ì8ã¦®Ò¿˜¥š=è¸ŸÚG¹ÿö…uó[VoxÀòåçl?¯@!V8Î‰5ú¯‰Â\'M0:}rpºÌ¸åÛG•c­Ì?…åkMF­@œnšÙº?dU©J›®X±bŸçÈ?•kWõ€ñ²c™>rÅøÆ#WÊ¡ÊqŞ‚8_»8‹WL«È?r2—¼ì¯?7Œ²WŠqÚÜğí70ûü:™*r•Û·nöƒêÂÇL—ãóµ*{º}°Vmå¤7ü\"ıü>mÇR<YÚ¦mm«¯m26ù&ÙÈñËÌ¶õg?zÚª±\'dï·~íÊ¥\nÏ²#µ‹DÍ?¥WŞ«´£—/Û{Á^­¥­\\5½ØÃüSõ¯é}ë¿r|Š¶T‘­Y\\ê¬ÕöY‰÷¾jÕôÚÓÜXş¬7V?ë?î—^Õ›ãbo¢¿í½dò#ã,›Œ?½Væ‰E$ÀŞ3û¼ş[\Z—Ø7üâßñÓí¿Ó.÷ùûo~‹ı÷—òw#Óê†[L«·˜Vo1­ŞbZ½Å´úßİ´úsîÌÆy,Mx3ÿ~Ş?ÙŞ½Ø¦ÉdÜŒÉfmóÄc([ˆ½F9XÜFd‹ÎÅ£‚`‘F’öÅ}ûXEÛİ?ŠæŠ#odn]ÉS÷š”n`õÒn×ó‹X4È®\\ñöaû»ñö}ìµ7®Â¨Ò½w±İ+õ÷ùäË??Û—œ‘}êO¼vÖÎeGbNZj»;Oëu£¥è\'Wj²ñÚ~#£ã’êpÕşë¢¦‹}\rær86÷x¨\r¾æ§[Ğ×ü;ô57Á‚¾øéû_ÿùşknÑ{ÍÏn*^óßoõ_ûoŸıßÙ[7ıG şİıŸwÙüOæü-û¿_Æß¸ÿÍå3gÌoœ_8g¢¥l‘I.ƒazn~ãìD)_/yÛì“wÌo›U}D~]¾qşŒm3ÛÎY½¼?Ù²qfóêã×>|fÓÌ¶™cwl\\=/º×†……­÷;úè?;w®^»}óêÉÙ£Ÿ¶vrñòÑÆ²Ô¬zƒSòüœû÷J›1G?9áx‘MÇmù„…mÓüSÅë ŠÙc¶nb\r¿qóÌ¦Ù•Ë—É‚2·u›ˆŞ¹òzY*V.uX¿nâ¿ıBgì˜ß¸0Ù.Nšgû­[·l[xüæÃW.Z.WŒz‡Zİ?BíSáq‚?UıYj&ç?Û<·ET’¹-kôp¼QÔ@ù/æÉùUOZuæªÍ;6?1»?UOê<wª;m²f-¾t~4£evÅ‘3[çÏİÈ3d3Æá¶ÙuÇd«ÖËl³”gˆª-ÅÚ-·l›8ú&wL·/Ş°€}MŞ!µ<QV?A¾ä¤-Våöa«˜T^\nù&ó#?76¹óQ;Ö®…wå^‡P6>yâlZ1ù„4ŞŠÉÃVïœ_·°a•6Ëê\r³óë7,¬\\¹rzù²QÒìûm&yŠ^ºyñ+ô£Õ[¾g©±[N®÷%“Û­Öv?Ú±÷VyE6^1îgVÌOÚ|ztcÑCÓó÷½ïX³±{—m—aµvƒ¼mÒ8|Ëä©›å©ó§­^{úäxåøôÉ-ËÖKwu¿‰ãpìçü4yÇ}ŸİgÍâ©ì´Å/ÜïÓä½X:zô¾/l[Ø²q…Ü¹jâ¢ÍıÄZzSVò°ÉxùùçxÜê0ØïÓ&¿Ë\08sÍb‹„½-Rü-²Ø¢?íââ?¢ùnÒ›«ÿ ¶¾)ïö?[ÇäÙŞ)Y=³8nJËÿ”n´Ÿ?>fÚW7§e«›SkwsjíoÊØ{ÁÍ®y¸95?7§æé¦ ñwê½¿UîPÕÊµ8ÔoÊ¹i£¯pş?~bß”o7çãÍ«7ó;ÓÍùÎ<»9šç7çKóâfîÉ‡Şpt?Ğ‰eh\"òì}t?ñÔ‰³Û·l<kÔxVÉ˜Píl¢›M¼W™ÜyÌôQùDe¸±n[Ÿxâ‰z0ÚëøÆeªšîóEİ‡ïİºõ¾¹©<vñşUıõÔÅûöNÎåËÌ	aÍ~D´¤#|lşşû*okF%	i¼âIrÅ“î¿?®·æIz?ªDgb^07-3Å÷Q³\'Œu\Z[íI«æWÑDg¶rcµ§Õ_¹fñ„^½??¿Iµ|‹ÿoã~æ¿ÅıÿºÙM[d¼zëæõ¿èwü;ñ?U-ñÿ_Ü²ÿÿeü=û„‡?èËï>à;÷àîD)Ïÿwà­§¦xæ×^³e,NxØIı={º®+Ërnüÿ©Ã?|JÊ©©rªÿS<5577˜:\\şOş½ëä“O+çä„Ü17UîÙ³{÷î];wîÜµk÷Ü.ùçî¹¹¹Ï}îsrZî™ÛµK{İu×íÙ=·[NíÙ³k×Üî¹©İ»Ê/¼ğòË/ß-‡ãßÜñÎÉ#å™»Ê]så®Rş;·[ş»«”‡îššÚ#gçvíÙ37>ejj÷ny»ücnW)”ßOŞ3Ş%/Ú5ş?Ï‘\nOí2·Kª²{×%r×®)yÓ%—\\²gw¹{Ï¤ÊsS»¤nãWÏ]85u¹\\#o)Ë=ROyÈî=;Ç_¦öì’Çï«1ù—¼P?©`9576ÍøİåØrÉñx¬Ê©ñ”\\3~ùxJê;%u”»\'ÿ{ÃìÌØ7,œøğGMûÕOË‡²ñ¸®>éìWüÙ©ÏßşÙ«vÜç¬eüâßï¼Ã?·;ğo¸âü¾õ?/<ö°]3Sÿo÷cŞÿëÇ?|é£/ÛøŠwo{Üüg®~ËÑ;ß÷®¿ûè??şä?>«éß~áßşÅk?òÚoış÷ğıo{ù7®¼ÕãŞñ˜?¹òµï¹æÀûßéú·İûÎÏ¾ğğ•ÿøşsùŠ_k>üñóš»÷éËöê.<ïğk~å9ÍáuÀ¡óoº}ö±Õ7›?øÀa¯¹æ²_ù•—|·¹UögÏÊî÷áßûÁ¡şÎí—ÿè²ötìcWİëoÛ^¬ÿÖ§NºpÅï<ñ«_ş‹;~ò#_{ı=_|Ì…¯üÈ%oùÚëw}ûö?ù5ï¿õ¶ÇşÃ•_Ü¶æÔOşÕ¡+ŞpÀÚîšÛU?øÍ§?pX÷«OGùÆ§N=âÀ§n~ÅŸÜıù¯{ø³ïü\n·ã?O¹ş‰[_ÿüú3:ñÇ—®šroıÍWÌ®=òÃ¿½â_ÏËîêªlıÇ^ô¾KßöùŸqlºòµıÆú·íz]wÒTyıí×?í.ÿ´ÍŸ}ñcyŸSÜóìï=-şï/<óÒç~ıŠoı¦µï¸æAïøôÕ¾Ãš»<şÅıß<fËÅŸZñ‘ƒ¿v«K\\èşğ‚w]øİôè\'Üáy?Ü¹â=?ºë_‡ßõåwùÑq=ø¾ÏºÍ¿Şé3¿ş‚C^|ío\\óø\r/òÿûß~{ş—>úm\'ì{Öñ7Oşà®¼âNG­yó9ç?ùo}Â›§:êœüÆq~÷ï}ğ°§İñê_;üÕG?râU}à|ı÷²çıÚ³NXwòe+~wşeş¸?~Î%oxÉ=>üœWøîo9çüƒõª‹ùÎøÄkÎ¼ä’?üè¤eöY‡Îÿíë>9¿ÓïzÚU_Û½y×ï}ìe?İ9ı–?şÜ¿^wñß}î^?8â²Ï¯_5{ÑçÜŸ½æüãŞºùn»Ï;ùò7¶§ì\\ñîû=oÅ¦OqÛGÏ~ÖG^ã?yÈ‹îşÜwŞúÎÏ½ú5ïù‹ß°áÉ|Ü;O~Ö®;üÙİ¾è\r+^û¯üO}íkÏ{ù…Ûîrè•ÏıèÁ×%ıø?ÃoÂŠGĞŞê?—ÜsùïŸpÿ_}ğ¯­\\xÊ3ö¤ğ’{¯~Ï½ø®ïİÿ6?üöUõ¯¸à÷£~Ùq·ë¥§î|Ï}ò£·¯}äS®}Ò3tùÕ/9øÏÚúêÙ«ğ©³/8àµï8ô£î´ığé;»³7|æê/\\õ±{ÿ/oûÈó_yåìÁ=ââw}ùœ¯qêšuW¯xÂÔŸ]ğÃ\'yÉ.›9è;s~iæóWœ}ÑË7ş}}èmÛ»­Ywb¼Í_ÿÈÇÖ]òÔüİ-ı|¼û}îô­­§¼Æ?ràªçÜÿ›§+Ÿ¼çø—Üöé[_ú\'^÷ŠC·ñ?—Ü}Ù;_ûÆëÿèÀïş+_İı‰}ØßIÏ<çÍÃ‘WüÚå=xøÑ©?|çó×~ğü¯Ì~üšGznÿíw~pá~½ø­3¸fî“÷xÆêc?6{È[Î¾Óëÿªã¿sİ_>ğß>ÂAçnşñw?¹ÓãÏzâ®ïşÑwâªsŠG}ìÚ¾×ÌæOì¾ì™+ŞÿåÏ?ğoÿäãÕï|ı»ÿ¼âŒw?i÷S.½ìÖ?Øsò×\\øèß9í¬«.>óˆ¯}ì“¿séÿ6Û:û?¯~cêCÿ÷Øw¾å¼×ŸsñŸx×ôœ·<yİ¹ßøê9—|Â³Ÿö?\'^üû‡}÷VSá§ºÏ»–½éôk?û?—¿çÏ.ÚvŞ§¿ğñç\\ówS_ù?üŞ—¿ôÉá»nı/Ë?ÿ‚s¿Ø=uàá\'ıüËowÂê7?ü¥Ûğ»×_ûşõ¾î…ÍÅ/¹àšqşÛ¿z««^ı7Ùï¾ñ\rW=çâ_¿çeÏºfWsí—?ÿ÷ùÕ­?İøî{?ïCüó÷ö›ßıµ;¼ñ£×ì¸Ç;~û‡îxÀÔù×Wÿ¯÷?1tÑı®zr¼úÊƒO»ı§¿~Õ«/9÷‚ø¾¿î??øÀ­/şÍ›{óÎ+>ıº£oõø¿Yq›s×û£Ÿ{äù×œ¶óÍÏ_sÏó^ú¢k¯8jê9×şËŸùñ\'<â=åmQ\\ñögß÷Cıù÷ı«öº7ï=ÿüì»İçÉWúu¯Ûñò—î>ûc?xíÖ?¸ÿÅ×?sşùÿïş_IxÙÜm·~óì‹sİ¿|øŒ©\'Ôó—®üöÛöîøÂ¿ò÷¾ô\'¿à‚?ÚıÜKW=ş‚Ã^zÌõ÷˜ÿ3¿wŞSë·}úÎáÇ¯~ÿ+®Œ½/;hë+üàÆ¯ùàõ÷~êo=ò¸\'=äU_ø§¹ÿµõ/>SÜíáwıÂIÏ®ûôpĞ?×^xÍƒ>ØÿÓÕG|å½w¿×…gŞë±—Şêö\'ußg\\ñæ?lÎV½ïºÏ¿á[ï}Â?øŒ7ŸòĞß¿ê·¯˜¿lå3æ?~ü•/Ûı¢zà«/á‘G¾õ”·¿àª‡ÜåÎ»¿ræÃ/>şÎûõƒ¯ıüS7OïxyĞßö\'Ÿ÷+¾øõõoœ¿à/šç¿î9×ÏÜúŠÛİfİû_vØ{½ä>şY?şÁK_íïñº‹^»bå[Ï\\¾êé—\\vÀõ¯»hç¦+¿vîå?:ıü¿¼ËÚÏüèıÙÔÙ·»úĞíüğ?Û>ô®¯_ñS—^wÒ·¾úàÏ]ÿük×}¦¼ß9[øõc_|È¡çüÛƒ¾ô\'ù­Ïšúş_?ó‡ş§ï¾ğükÎ-?{Ê[ö|ï›¯úÌÊ?~óÙ¯úÖùä}>÷á{¿ê~»ßõú•3÷|üÃÔSşÊ?ñîO¼ì•?Ê÷ªş·ùë¹‡œ¼ëï?øòÿyÛ=N{U]´öSo|ÄS?{»æ÷Şò‰Ëyßš]¿¬ü?K¾qÚû¾ÿ¤8ôş¡‡İöØã_ş7ŞóÕÏûÎ½>{äùÛ^üŞ‡¾iê#îº·¾åÉßÍ«nuş?|¿+¾ùÉ[o¼lóçî9Õ~çŒö6Ï{æŸŞæ^~›÷ø·>áSÇ>í5·{Ì¡/ùÓÏ¦®úÄu?pÔñÏİqş^şœğo;¦v?çISÏ{Ê÷ó¿ıƒsÿ`K¸óE/úÆİ/øĞ!ù‹ƒ?=úË?9á’óNùê/›ıÜ—Ş”à‘ïøÔ{™ü²õáw~õG.Zñ¶#ÏÜ®éoN5oúróå‡^ú—/ı]wşw¶?áv§ø×Ïú»—Ï¾ä¯îÖ½ãy_[±ç”©gç¿vàÉ/~×U—_õŞ\rwşø¹Ï|öŠvêÔs:ÿ„÷^{Åqß9ñ oœúô7\\·¯ÜíOŸ|ş½÷?¿ÓŸøÒ?\\şˆ{ğÂWŞğØ\rÏ½ú?ƒO9\"¾íøÇñêü®?¹Ç[ş?ö¾>ª*kDcae­‹Ê	Nz7$!DBR(â\Z^fŞL†ÌÌæÍ?eÑµËª¨¬{CVWlH±*–EED± 6ŠŠ øßsÎ½÷İ73I\0ñûö÷ÿÌOÃäÍ{çİrz»‡xoÒ.Xç¾â¡³Z¿>¢wUçû³Ï}gçwöù÷ÙïÚ½¸S¿²É}Zõîrø±[úµ5ûš>\'µ]v{æ²‚yy]£‡·\Zzğ«ş7æ?>è§ş\n—üØªjÛ³‰E­	-­}èêyÃü}÷´?ıV–½rêÉg~:óÑ;f…Zİßã»¹ç–æF;yõ¨İÏzÇã}N©ïxÎ½ç=ÚêÄ‹ï:yçà²kkîú¥Û Õkßî9üY³m«xÆÅ¯n~ğÓÁ+/hÜºíáƒüf?ÖÔ_[;µo÷ú¼ëäE®ü®ıÏ[Nñ®Ú28ûÁï×µŠM´sÙa³ØZ½÷æÎÇf~~Ã–øaæ[øæÔÅ=ŞØ±ãü;Yøõ“EÏ<>mÓC%Ufëmã¿ë®çÃlxqÒßU-Ë\Z¼ÔõìšO¯¨}ãÉK¾›ší0ûÅ¿<ÜæÆ?Ï†Z?mfùìá™K»t½å‚o£g?6êùÃnòjmYuÎ‚¡Ù¿æ«Z­ù©á /Ÿ¾Â“³ôú;Bm#ÙÓçtê°cÔî·àÙ6‰iã[Íé4¢Õñ‡œòÀª¿ìXÔî‡§Zµ-js­uî³GÔ·í¸?«=sÈ¸òSªN9sÕæGlµéª¯ù¸İKG=Ú ?}eö‘Eoôş×5§ôn½ã$ÿ1Ş?‹+Fw\\û?Ÿ÷<ìùÕğU__9ëØ@ÇC]ŸĞéxæ]®i‡¾¶âİ=£æ&¿ô´9U¶_5¾pÆ9ùÕ?ŸÚwàG‹?º$sÏŸ]4~K¿?-÷PxÚkº}Â™îK.ÛôÌÆïfeê¿yuæƒg,-©ÿÈºwÚfNö8ú½—Ûqç/æsË7?=ı¾ÖWtßˆgEòƒ£î¼6wÄhïí^,½dİG÷Íî§<ûÔAm¯şÕük.?ıå™Ë_\ræ_Úªhì§ÛN›³§ê²ª¼ÑwÿÇi·´îz?1÷ĞªÓo>ú°!?Ş;{ê·gæ—fß_yÌY‹>«÷÷Š÷–»ğŞ»—¿¨¾ğ°7æ­[zØ×Ìü.ÿµk.ì8=³üœ¡oŞ[ûä!nj•sâGæAgç}²úµiı‹v\r™{ÈÍgåÎêzäÊ^×µ6{~»E­:µ»¦Oëàıù%·½¶¬`×È·û„›pC`]ùĞaåoÌ¿oÔåmÇ^tÖcB­_Öû®Ü3o­ë‚_ßµºÓŒÁw¿«Ó«‰ØMK}ëèS=péã#/z §pWèş\'v?zíß×öšæ:ñµ­=Úùù¸‰å_Ï)Œë}ş!/û‡\r.Üe­ûbè¿úéCùÏ¿/í×ãıNWni(Ÿ¨ÇCù»û%[óé?\ZBs>š·»û×ñ;®îuïú=3\'ôUéso´òÃû·üóû×şó©Ãºîzú™Wş½ns©¿rúMeOïºûëúµ5rß¹>´ê\rÿ½úÙ×„nÚœ}õEßíœòäÃ¹CõØyßâ…·½>7c£·¬Ïwkc/¾WÙ{õ¦¥+^Í¾î°?şÅ[î\n¬|ëåËï¬\n?ßîsÛ`ï[øîÄKwtY÷Å+ŸwZºîà¹·ÿòjï¹+¢‡¼óø÷ƒÖßvâÑm®ó‹¢wv/\\ë7_¾”yëÂ?şÚæì]–çÚNe;üÏx~]óà˜[ıĞÛûÍ_§Íß³øæY}²]9à°oîû0vÃêİO2ã»×öF¯ûŞşğˆù‹—ıëë?/okİú_?×ûæÒHåÌqYÛúì	yûÊ=Cÿ«Õü_ö„[­ºrÓC­ªµ\ZZÍÙóÓ»W>¨ê‡?ş:şÓÕ\'ü¼çàm“6ÙiÍùCqÏÏfí~)4|ZÕª.¯~°læÌ{1[óLY‡g‡œ·zyÛ†Ÿ/\ZùşìG[?Ş´êˆàºIcûÿrr—÷?½jİùÕ_^uÄys£·Íi\ZšøÑóËï)Ş“ÕîİÅİŞ<ë©1=9nòàÖmÛü°Æ¶?×¾uÕò17$Ö>hÉ7Û\\GÒk×ƒã>8zü/}3v÷Zõu—cVojœèºnßğË-õÜĞóÆ›ó–l¾´úÎÙãsÖ¼qö?çWœúâkg¼ÕáÆËº¯¿K¿í‰Y×,^vd›ñ³†¬ş×—tòØikÂÿôzÛn³ÎÍ®¸áÚi[ë/éôÈ5Ç\r1?[\\sÃ}ºGJ/:gûª¿ô}é¯9K¯¨º~š±¢¢ƒ¶æŸgxôÂù\'—w?æşØæ{\ZüÔ¨6½¾<£Í¦5‡Ÿº&Ü!ûğ¥ç¼;­òğDıe¿”wëğ;3º-¸½&4õ?şÇ<}YëŞ›0aíóEúì¼û«öKWüyÔù¥?eV3£c–g—|öèâAììúĞmm×}³¬ö¢Û§ìz%ç|ïÇ?†W.˜¹|ä„¡	_í2BSŸ¬Z=që÷SYüÓG—·	Ÿ?óş‹6œq”k÷?§sO´Ï…×^òôÕOoí[6oà!s×îÓ–}İ÷õ-¡iÎúõŠ‘GÏˆ=øAåßJnš³:oËÎiî¾øí­Ï¯X¡os-=Üs]ÍğK¦­*{âš™¿ütpFù˜•W¯9jÆØ‰Ï6,Ñ\"í\ZtÂ?n½$˜ziQ¸¦Í±}ûLÜpÜöU·tçn~«o§›ç|ğó‚eŸ<¸ìÙ¢çŞßÑù™öƒOÔw`»—nê=ù?á	FoÿöˆÛÆ?ı~áŒÇ7úúîëò.èşŞÈkk?Zı×ïÏTrõIEùmÿqó	Oœ1ıo9W?vùBß¦1¿~ºé¥÷7ÜŞ§ï	mïú,š:¥ïIßwWCÿYc6Ìlú·ì6?ß»zë´™«?^ûıŸ»¼ùØ¦©wİwîŸ.:´xîÀ÷‡:=QôzãÀ;uŠßrãÆÉíŸ¹zæìÛgç=ëÃ‡>ªôİ~Lï7¥wÆÄuS/¹DïpåK7=úpë6O\r>á›ªú5ñINëxGÙîŸn^öfÿ“wLøjç²F_ÛâĞÏ}¦ÿĞÓzn™ıØÌ+><÷ÃÇO¿í3‡nıe}åô~\\±bõ¿ZÖç½‡CC\Z??2ıøÏŞyqÖÒÅã×?Õmóy¯,/z»Ûå[¶dŞ2ñägîÛ|Õ€:]ıÁû;Î?i{¶}çO‡vhã\'ù±s7Ş´fÛQÛ¯¸óİ_xî»+;t[cŞ7mõifwx|ÈîCoyğ„“??*ú¢úˆ.<¶õİ{ŞŸ^½nğ	/œ²dÄ°œ’¥+gÎºgìùÿùlÀ²1s7\\½ıŠÚ²©?5)ôsßWK;tûÛÑK¿Ü3ÏÌyëóèZ·Õ{Ò€6\r÷Ö£/i·äÍßÎ»üø¥\'LúdÎ\rŸúdÍyŸ_2~dÄÿÕˆuş\'jšpÇñOvŸ«ºè‘?Wç¿qÓåëîıxı‡:½øqf›ãjÿÕÒsÖ,¹Ş§qÄµ+_øşói3Ú]Øöë®ç¸|k«+oyŞØ­õ¡³?íşú°+\n?;³ø÷»?woW4ó¾¼§f6,¹çòªéwT-x}ûíÑ\'m3g`İ•×ô-XxúQ¼×ñãI]²)ã—Î•?ºÄÛ3äšôEÛ×ßn{|Íyë»v¸§ëÔ9_]øÉ½/\\Ú§_«…·íœ5iH¿W?ÚQÖåÔe{¶ÕşàÒ?~³ 4õoÃéÛzŞöaÆg®yÎÆïš”wÕÍ?=şÀáŞ?c–Múëí§<¿eÛé³²6ÿıüÑÑòÇŞßñËÜƒ»5üĞñÇN\'OxKÏê¼Æä=İ¶İ2ı’Mõ?ßú…\nï¸¾13¶ÃÓİ&„??Ÿ~üÔ¯Nı%Ã8å?Å?Şñ¿ş»sú^üÉŸV-~iÕº›~8bÁë|‘½¨¶mÃ/Ù‡Ï-{ÒÒÃfı¹İCk×xã3Û/|Ç–Û;yO‹œ=æ˜ùÿ^2îü•w­™YV<æş×K_>vÒ„«ZûÆ•\\í=±çŠm?øO½ñ”vİB?v½íôœö£oÎ’mç]üò…%ÕÃßºİµ|Ñİk]w[ıÁ9Õ=_aO>wè1Çİºhø¨?•ã.;æë#|k¯yç½¥7|?øˆ²kŸ¹¤ÏÌã—|ñM×Ë;ßğÖÖ‘^—ş#gôÛÿ]äk|wÔyç÷Y2¯õ’/_,ıö¤/y\Zû?Ø½¾rî…5g|Øuõñ?_~eÃ®ûoßÖ©®ñ‘6eßºûv­y¢|åÃG}ö-Ÿ0xŞ’GÚ7ó|E¿EÇÍ1ö?­×ÍZ’ñÜµ3ïšñŞÙç®nÛşÆƒ[ÿ»uÍáÛşùıK#kÖóü¸ÄáG¾wå®Wû|×~ğ)3ú/0®:şknùd\\Åüî?ƒÚñH»­‹??9gş%YÕ\\ÑîÃyc6º´ªúÙn\'?n8òa¿ÖgúG­zÿáŞ}r½owı]‹§w|oÅÔËB/~ş}ÕhcÄW\\3úàgç=ûÙŠÃÜyü…#«N<¡Má¶6g»gÆ¹÷ß¾»æÛÑ?|Ò÷¨‚W^MÜqÒ«+«?m»ğîw¯™±u©ë³?İ:ì‘šuË_ßqŠ6è›e[ÏZİøŞò7¾\\[õbßSG\rZ3õ?\rÚÕ¿Õ53/š;îÎAç=wR|ã‡}e_YğÖ\rG^{¡wıÑw®~ãù¾wÄ~\nüå5×û­ÖûÖ…~ÚyEÅ‡ËÆŸğÕ?›Z=T1cÉÔn^÷Á¬#z<3zK?Ms;®šñr«Şºá—÷š=û±I#\ZeÜ÷è„Ow\\ª_wFmı³ïİTúâ·mOœĞé/ÑV/Šyyà[=oşth;÷Òa×/ïõÈî6|wÙœ%×x.ÏÙxĞ[Á3;åİß·K›‹o|{ûk‹§ß¦êµ{¿÷ÕÊg^]šzæ‡«^/>ä†»çWwz®øÜ6“ŠgŞtÊ–™/üå¡ï?¹{èi¿.üèÃµï¬ù²ñä¾_O;®nÆ×+üsHàœ¬ïşâ«¬Ï;Ş4uÎ¡Ë·?·½Oß£_ñ\r9*ÿ?¹ãÖöYuÈ+Ñ\'÷©<´ïúoÛ3ğ?›6VßrÅY;ï˜ÛñËYg­=jÓüA§uØùÄ²×¶ç|ÕóÕ=ã¶œ	?lÌeåyƒ‹æ¯X¼½÷«®×n~¼áä³³â—~>ÿ¸Ó?›róImª?>wÖá3f\rh¤·ß£/¾œõè9?Ÿ=ø”±‹¶şRUq•ûĞëyOc?Á_öŸ1ú×•Ó__³~´ÿóïİrÇÂI³\\Ø)Ôş»i§Ü÷æi#¸÷£§6?ı}Ïé”İóâÏ_yÄ~^;±úºŠKÎ}óÇ/«{·õE3—¾ûëŒÚí+–”/ºîàõ[şåè‘=oğçÎüúµn·öÛ9nXM~Îs•#æ|µé‚éWÿåÁ{~ıåß¾ìöŒå_÷Ù2ıüÎ}^|bòŸìl=ai;÷¹óÎë·Á|óµå¡y¿ö?öÃÔÙzşÜĞ«á—vıàË­?vüœŸ·°û}™]nğ·^ğÁ£uÛ6,ëzLC«Ù\'<ZùøºşŸ>(:b[y­ë‚Â©‡\Zã\'ŸùşMÇ]|ÈíÃ^{ûÒ/o.9÷íçf5şköêƒ»zÏÚÍE­Üï|ÿÑAË_˜²»ş \'¶EÏğ×NËŒî9­Õñƒ?¬ïÓ&òñ§Ï´?Ñ\'dµaÈìÁ¯Ï8ì•Â+!pP\\XZğĞĞ	ş¯D-şø9P?2ş-/B†;Ö;àïh6ş×£O¯=Ôø_ˆÿõîÑç?øßÿÄOÆ^íø½\ZâÈŞÜ?yš?ªGê(oÂ,a¬éòéP¥‡i«Ø)`†32†™P…F5M²E”iéXÔ5VP?!Ê¼¦\'µî\ZMÍğÂq§j…yZÕğâÊSkähy³%K+/-riç”aAã˜¡#Ëi–²z	LQñ0~ÑMeYPP¦ùMêÙa¿ ĞÅ?eã7Cİƒi…t*•$@Ø¾?\Z™¬‚?&â¦a4˜ÑzKk`ØFÉÄ‘:3fò#(b|0`€ú›Ø/ å\0>K•xÙê1jµÚ¨Ù`±#(±:èºÕ²4x7Ÿ<ÔîÆŒ0ÔİÁ¶4 ÑbkkLxØ‚eÊe‰\Zşx?‘Gƒ;Ë­6Ø?Â~6\Z/Œƒ\r.l@­ é\rø¥\"-Ìà(DÙÇP†\'³Ï‰~6+U%lƒùå¢€Ï­Õ”SeŠ×¤–<V=ŠcBº?Sf§o½¸\nª¸k´êpÀJXZûk_2@EdX,¡•œ;Æ±WzĞoFÙª†\\¼´™-Ö,Â¸±	y·áM‡7†V‹ıJÌ`<Æ÷\nÍÉUÿùu€a¦\0^?\\È¶a»`%?h˜!Cƒ8’?˜A8\rµ­bR:VåZ€nº\"–ŸÓD?Hã¯¡ŠblÅƒÛIÄ\Z0,*Ğ­5pwØ \ro»/ÂnÏ´ƒ—TâßuPca#ÎRoÂ¾µo…\\ïì)?ˆr3Ş¡>1‘áEvïZÀàQÔØz\r+\0pR1Cà`1ÛT\\‰¢¨\'¸&e$Å>Ä™?ª1ÙÎ\" ªhÜ¨JD`! x²ú]xk(µ¢°|!lÜ©?¼ |,×İ(Ø\\\rt?lXÍ¯v6¢TIáÖ*iè‡:8m8¬D9dÂÃŒ+ÍxÔc3£~Æ.JM—VÌÈ(4`4F‚:Vİ\'póŒˆa=ª×l`¨FD?Æ¤[¯ &€ºÄlâÑ:Gî½X¢±‘?†)PZm MU Fæ˜²ZaÉêôÉüimL ÌÆ§U&­‡p@ª?Ñ´L¸à,ÌôÅ š*(?9eŒ…âáX4P‹”Æ˜’:Î;@¥Á	¡AŒ	™*½¿å$áµÑŸ+ Şƒëe„6³†`ïF·øÌxØ›´¿,UÏ[•¿E>t\0TÍ.çĞ™àcKxªœu•a¬,ß^\rıøäºjùl°P	2†]2¢!KÜ0†aGj1ÅVLğÅÎcÇ?Û™¸ãõN˜„ß›tÉÇĞEÅ?M³ar$\n’*Bv¡‡ıq†·ÎG±KcÛÅ$øóÊ†3LÀïıF,íu¨³ñªß0öU«[L?èÏ`Á›ıˆ\nlÁä€Ä­LR±?éO•õŒg3ªÑ¡RZ|?Ld\"ùE\rãÄa?…Ü¢å¾¸=ÔBÄİëÅnzPsÃë‰33ºæìY<ÁqtIVlŒ]°\rûTq\'4¤¨ø\Zööå#uz­Áˆ˜½Œ!‘§ÍÖk4\nış­Ei()®î|“‰­â°Çm3”º¹9|?[Šãˆz©-D”rKÙ½	)J Sƒ/8Ä=*¹›?Õ™Q«.\0Å2PÎ¸’D\np˜($rjĞÃ‰ê“=& $´5ù#,ÀàªH²$±g?Ï”¾zĞZBo€ßPìÎFŒ€ám âd §fÈm??!1›¿K‹Ğ&a\n¦ÅU1¡ÁD\'_I)5µ¸‚t3b²ÇŒF\rìÆa„Ü5ùeåã*Š‹†Wi•UyU…#K«´ae%%ec*QÕJŠK¹¦ZÎø$°*e89xhûİ÷¦%ø»/şîçÒ˜EÔ03Ÿ©1¨yıÚp=ZË¸I‰Î~ë1†³n@nà’ğ;ÊÄ!?oyÏœìŠŠî¹9}ûòf¤TçÈ\\Ì8a ?QÇ€á†ŒÕ9™iÚq7;V%¹\\ax™(S=uT%Ùêärbr¾K@.¯ª?Ñ£a#Ú28P“ÒÂ)bX¬U˜FĞôY-ƒaR!ó¥‡¤t!iºÏ´üÔ^lø¹xêB?÷kC\rO=ÛJ \\ù=Q~¢O6­?Çà\"7Ã‡°Ám‹IO¤è£s=´‡Öˆ Ãô:UË3@»Â9m¤?2bÓ*§„âÑ I\Z«É$ÁÍlöm(CS¯S[­)gÌ)@*¹lªâtTaj?”ÿ‚FÛ#A’?+!áR8t6[	8¹Ï0T\r4 PÈˆzŒôH$È?µ¼%ŒWèÊ$íaRĞş†±JlB=³u,WD~Éµ\"ö‚ãĞ?ßPæZEF­Az„îó{AÁÆô9CğVÒbŒh\0úïà†1~n}ŠáIÊuŒwV?şA–İØ\"…<(dkÁpÑêB;[¿ÀæÎû°o\'˜BÈzãLnNÆV$ô”Û!Ã&Å™åxá–H½[ë\\ Ùê,ô8bı\\p[´¸L?B#É Dy1—^¤Š3‹„Ú?¥Ytç\";ğ)…\ZäÆvÎ«ÔŠ+İ?k¨—“Ä`0\ZAFA‡\Z¦t‡„ZË„ƒL€ynP\'bŠy[³D±+K\08Ç†Ú~ìéBXÌoU\0Âº22\Z‰‰zl@m_ †­_`ótaÛH??2AÍĞ\Z·‹\r$bˆ~E(û¼Dl¥ÀuN4?•ê‚å?Züè]D	†?£‘4¬a; ¬J—S†¬:’&º êµ?­¶6‚i9:[5ú«Âl\ZÜ:?yØ`¹ç±Í?3Á÷Šàä›l†Q]*Ğ4*&Í(¸´Ä$\nK´²aZ\Zé²Ø©ó’ÚÂı=I^+†â1ê’dàJ°i¡RÆLyoToàö?À?†Æ´ĞÙ|8Qû\r\\ˆ™õñ`,A?\nêI\\š‡!¬@Dg–ö8¤j«N _\\QLİò\rQ°û„rÊv?AÖ°Çˆ=3\n`?ƒƒŠjû´l¼GiÈl\'Ç˜Q¦q??öWàFR8!Ñs6¸rúØ…Xƒ)˜“ğQ\0AFbÜ³Ã\Z(TKM•¥/ÉÍ5T¾ˆ^lŒPHÎÙƒä2ğ>¶h!!8ïC ©÷ñÆd\rQ“ı?İçàøë¸E~ş\rÀi†2hI¢{Í`Z¨¢=£çÍ9‰_À’m²+‰É‡\r`g`›Qå9úÌ\0ñ¨{0u=”‹álfW gL‚èzô%\0ß¦_»?»ğ$NIlĞ¸|ó@šá Z= v3V;hèĞ¯‡ó?°Ö7›ÿZn?x³wIÎQFãb5\0¨«8CÄ×¶\'ı²€“ÌsŒÎuwË¼ö(r¦G?Ç†ï–GKÏúõ°IÜÅ(‘Ñ7[—¬I	¬T0¬UÆŒ@˜ÒEî&çÂ=È`\0ºƒe•¯3øqu¶ÖËİ¨1)bYÜ³0œ‰F~Q•PÙ%JiU”;Ë¾\'î¢¨0BoÌôÅ4o\"¬‡Àn„şa¦î%?%ã=Ìèƒu•[—ÉŸ`-ılˆ…Ìfç{?¡¡Ş¸Uñrx/²—x,?¹³ä¨Ëu‹½SœşÔ˜l©cf#cmLú2?ÂXPh¦6’- nAÿóÖÆ£‰.°’¾ˆ¡„=œ©FĞ‡~\rÔó@QÑI›\r¶Ÿ‡ÏŸSèÛf7ZEáØ±\nD4¡#Á=§œ~|	\'?é˜%ü	¯\n¹%ã#.Ñˆ.dòŠ1êGm5›¸Ñ«™\nA=¹g?qà©04É-,·6q(@\nÚ®’g°‰m)6ÚÀ \0“§0¨?½\08”fy¢?HL%ätˆp:sñv~ N6ë\r©fòñÚ+ÿ@•ó?m)“íÚÆ!ØR„„Êß•‰±Ş1ÄPX+\n\Z†4?$±§z®PË?T\r¯W(Ù¸]Â›«eªÍ@S?±¦\0øa/çöàÆî¦:5|aŸ\n\n‡åU—TÕ++­*Ï«\Zîâ–ÔeŠ-ğ°>EâFF“Lr Ûc²\rè¶gzšbh\0[SÆÃHãV\"Tk-òÄ0•›>lï¢ \'BCé%ª?û}?Fx?v`Dí.\nJ”Eº¯†«Òû…(\r¤b++Âàs\n²‹!8mK~¹¶%únryÙÀä è^ä$\ZÅBpŞ8@£’Y ¦³†®­Œ‡(ì–œİ„­,ñ­ŠìëÈ;[òydV²EÂH\\t°ñ1&DV-GèŞ+,ê¡bZ­œ?øÜuÖ3dh´ÆîIı‡éğIÍ‰hák\ZjC·G\0(àj6µÆdCİ\\á/½ı¸>| $:9¿w‰‰ia#S¡?àÈ1>j4‚¾…Ê´kO=)‡u rØ¶öÕâêä¦¬ ¯ÁĞÍÏw’q–(v‚e{„}8Éšw€€aZà>å?F½pÁí?‡?~7ñT5Y˜Ğ.8`¶í’èÂİö]¯ã$äµ?‰y&ÁÌGÔSÇ1R¯7?)rºB{	o\"›i?8¹EÉ·	?<ØÅƒdÑP4N\ZTş¡Ÿí\04ÀI 5\ZC¡Ô K,goñr?™M&ÄPf»`ß£¨9?Ç¾ïë?dl\"ú´ƒ¦R6’Ê5?xõ†8Âõ8IˆÅg³µc/q‰Ö©^Óëça@çÁúbğ®Á¨‡hÓŞáRŠKeîm„EblvBii‹}µØz\rŸvL¦í?VŒÃ^•œœ;Ï¼†Pö*„·ŠšÌT„c—Ô`œDÑ@¸G.ğoìŒ,üÓ@¡ÉLq[NoT~Õ!Ñœ×3Aìá“ÃÍğf€éS¼S˜mªïÍ:©«¤ªÓ8-b	NİTñJŞ-\'J•?J×ÅˆoGÓ½JaƒÎW%yğÄ³j+Šü É´ÚØğ1CíV—äjß]Üp/÷…K,ò)¼Ki Ïc\"ß Í+$|	GªÃ5,ZÂcL÷Ê¼„şZ,“×£¤ÛPÙ,\rÔìÁ?(¶£uRËS—\"?N2`™Ãgx0›»³9SGŒs+’âåm&°İ-ÅÉ+tA¿?R¢©¬ñœ\"‚$}`ÂÓàÃ}ì·;w‡?X7aGvƒ |7-Ópûİäß?VÔãÜ»ÖÓ£nÿ¤$òoÃPjÀEWƒÖ™x¿Äor,4??pm‹ÖàèĞ€ğæay£@yVz\núôI–ÛŒú»Ám6ûÔMµ\ZÓ\\[Èã&¤ƒæ¬Ø4Z\nP?OFÀCÃÈ/˜@à”R@ØÌ¨‚ƒ…ÅÅõ•\'	’­Ù;JÒ‚iòÌ4¬Ó#4û†`\ZXàÙE©\0íÓ[J®=¦@Âñtğ¢z³E?&µI)´ˆÅ|LîB7C)¸g¨–Ñ££ê‰êpC®£ğSŠ¨Y=ÍMA •1EÁÙlDÏ¼?+ÀÛ¸cÌ Ï¯cØŠ™lüŠñàj¸öê³ Òç@ñÀ~ì“ºIQ¶°O-n‚ty‰Ã&®yĞˆ	UMÑ3”uw?Ï\03Àèl,&¨<8xfĞ^ÇCqBà\nËÓ±êÂŒÔ-?±WS§³İĞ[«éhû’İ…?)nË^ÂXV)fõã9–aÛŠªt¦ÑH•Üªà®éFà…ÃC]a_ÀO§L6(üÃ=Zhúd3*ZÛG\rÌÌ’J3†ı`?Øë®\ZŠ`§˜HPf?XŠb\'!	v\\Ëö“BxúŠ¢?\n‹Ì#N.ü|-Eq±±œƒº•p$Ê¹µâ.!F<Qğ+±\r¨·Èw\'–D@ñ3)\ZE%®†©¾ìŞpù¨­>&¨pœ±jH!‰f—@n€7\0F…ÈÍ{’;wå0£MÚ7`S°[+Öô?Ç€å‘?t#¶üŞSô¨—<ÔÂ©N—†NQ7™d3ò=’ÎT‘8RN«N·“sÀ%¯Õ°IÔày¶qÄ–‚›%¤çC&€ÌÄ@E¹Â}×`Dmå2\0„I(A6Á\rP¹.‚0×GP78È˜İ°•âÌ˜$OÅ‘%1=ŸÇØ«òJ+‹µ|2:ú‡<ŒCxyPàg3[‚Clä	ò-¾ \Zø=H;bccªJ$\0îc¸Ì1›q»˜‘\rïÈÎç‰š°ÏÀãeH“¹\'œŒ™ªEü0l†³á4á~°ä/B9B:3î“ÎSxet?ò½,îÎ —ŠÏ¶°ßKÑSÇrYF¤ÉÅH$ ,8/£a­s~g¡Ä:¬Iw?¤TÔÎU%&¥šnl\'á\0&û@jÕYg9¼ƒB›§A!:ÅWI¼Wí5À#Št\'†‡N_»İ§J:*ªœˆRÂÖO¢°ñçÀe¸;Tí]tt\"?ò ²°pDMea•`»Š±YaÖÂ0‡ša3?¯(1u¯…Yx\\??ĞU§‹Ó¦”ú·°{ÇFBÉtìLv¶\'H-¢5Ñ\"zo|5½mEm´zQ¬¦b+Nr½@V‡+5x™«4h‹?d@¶‡¾…Ç(âŠ@8»!ßwt`j_˜‘@]¡ÜS˜?ÚĞµ u{Ûjš:‘\\alÚîJ†ÕÆÔ\0†}äM’İeë\\Ù\Z˜vtìYÔdÚ}~‘Íô@3‰…X–<>nqÿì£Ld•Ñ\r?‚?æé/Ø\0ÆW+¤Ón­fŒÌÔ?0GV-£­²ZqÊÈ™à©A”\0Ğtüf\"CÂijò®‡p¼gX3|lÀ@ô °s„*Bñ,dz’aFu?H†ş<KÃ3c©•µúšÂF&Ÿ I`…«È54+K¨uÏPš~,?#N3OÒš%?ÈÍ[û¹8À<J:&k@„ê&G²U†H¡?ƒ¬-ÊÅvQiugUùÜ\r6Ó4ƒœ’g×2‰—Ô2kHË\r³\Z²4:t±}wˆaĞ§yh\n?7-Ó@N:\ZÈq†$¥7\0h+?I?RL˜®jO~ ı[U®,ô2:)iZ%?lÄİmÅdrÆ _“ùè}×½(#:“Ö1áF…Öš¸Ğ‘I2$İU«{y¤Ã‹‹)îÎ\"ÕOa\r¢6\' üaªë`¤C‡ıDu¦‘è?¡Jiß\"ìœŸ¡jå?PÆ3¨[M„dÆíLÑ°_÷NÖÃpøšœˆğf¹1?ÅKà®[Šóƒ†èƒ´å{\\t”2[ĞÅ¶ÊªÃÁ@½AÚVow¨òÀ*œa6ï¨g¨ÃP£\'\0|Ü‘‘à¦?7Ñ	0ˆP²·•RÒ} ÷µ´!t‰4QÈ3†œH:ò‡ë$¤O¹\0…1O\nòÊêO=2Ìş…-:1İ¢\"ñXÈ<ÒM ‰SkZ¢ƒ4T`ÇÙ›¶Àp6ªÃ‰bñë¤,RªHÎ}Qñ0\0$oá6•»†§ÆŒª>1Æ*¨~ÇÖUu<È…ÎôòS¨So0ã6‘”Üd]?¬¦aCqÊ¤«Ñ‘Å7Àø&ó¨P»\"E»`jÅÆæÀFb¼j)\Zˆ~øsœdHÓæ‡…€\'j‚gJ+ÿI˜aNac„±iîŸ+5bLæ2©–I@\0|x¼çÑV%Ì0Û5)ÊO//*àt?0½Ó‹Ö½L¾æ¼¤j é=„H#Ÿ™\\!¥¨($ŠŠZâÆ½R±°—ÀBoKXXÊ Ù‰º5Š|Ë«&˜m?±)‹29úSfùbş\\³+€¸”´qG?GGL´C¦â¾O]œi™¤ó)0(%\nW\rmo:?¢¤9Y<Lˆd’òebâq=ê7TV\r9XŒ»q£MÖœ?Z£*|ÀP†*§[ùRùˆ´È?¨–ÜA:?‚Xa4î\r ëÌv\\\0ÛbêŸ¦‚,\rí&ø9/®5¨¥‡ËÑ…ª¯…†FÚä€·ì-LsM…‘?ù‹äG´rTÄ`FšÇæ:µQ³38cäç…ìs q\\áşZÀtiş€Ï…y©~o®ºàUyN©¡iˆ„°»1#ÉÒƒ-läe?ÅİÊWˆĞ†ƒo§Æ±\"vÚ\r²«eØ30˜˜ÜÂê@P]=‘—IÄÊı?lŒF}7¬3?S°Õ²¤awÔõCÏOÕÄe6ü”¹\rIuxå’Ó˜0i¤é??äùRJ\rj¥]1À.‚9?çG‚{ ,pÓb2Ïğª)L\r®·Í*nŠq]Q:Yâ–í`a¸yZôÂ!¦Ì0?~¹hNK2X‹¼¹å?è-Ò7åQ?ZÖ)Æªó\nKR&ÖüÂêÕÅ%?»4TlBz„³wçkËIç›‘{!û+(¥tF;Ïy?IÜ…b.¦Ñaú	ğk¬!Å1cüt]~¼÷9Å€ÅR8‹8¡Ôq/\'I>wêØÙB¹Ò.›‰ª; Iª:9–?M|¤l1[ÃÉŠ\Z3O3eãC‘ˆùÕ°\n e/²š?_Õa…+¡\0D	í8„¼ƒl×˜ê?Ó4:İÕ`bw²3+\"”iÖ¤<ìg4 —Ê”Õ¿!^ªş]ä…¿Ò.-?R¶8vˆ;\Z?B,îö?¦`;šBj6®üXcÚí‚ë-ĞCS?\"¡°/í\rwò0Üzd˜TK‘B—¥eU…ık ŠÌ§¢˜©ÎÜ™ÛáBEŒİ¤Ã‘B4P´%”~&€!JêÃ\\\\ÉQk!@Ìu6¨ïI)jFçé™ªóôÜ[Í{äi3˜ğcxŠ±nfÅÄÀç–©ûõ@8ËÁM…ÓkD jAõj%ÓP‚S\\ê`PoåJÂc\"İ`0H+,@şbîòã-ç2JA-´P—ƒ/‹$\\{=’wQ¥uáRaO&FŠQ‡Vn;My±œU\'OÓ±(™%’¶xF;ªºO~Ó†—Â±µ1†•!”¢îXK2 \"iO¹TØ‡iy¡`%L^‚½˜%ÜÓä@îÀ©±	U™•¨j41?‘&Z¨à2ó3¢…tXån½e<ºÎÇ<FüqŒ\0ß‚B]X•L¢ôFì??AxÓ 7øÕBÌ¸R_J«Ô&°|hÎJãbËxNµ9«cT`x!ZÈ™´6M|\Zïq?½%øR¦h1s3›2Y`ÇU‹Du+&ÁçÁÔ&â•N`ôğÌßŠxX?Tb„ıŒ˜™ÖÆ“QekŒ$ã#X`ÔÄAUsb’†;YEŸ²hxH‡‰ï æœ˜×€‘EUÊD÷/Ñ BXB\0T‚ğKØ‡,·V“lè;,02æIÑ#;ß­?Á¼hqÀ6â(eq]8Ô4LØÂCäåù#j<G?6ÄİÂ7Š©p6Ê[)¢Œ²í8tI’i®\"A?IĞ„V?Å*ô¢ÖßBAş$W5Ù•L|%*ë 4ò¦(Ã}e:\Z¯ê›’Ÿ–lRrÛÜr˜…q…3¶$ÂúvĞöÉOÒW“2Ü¨²ŒTˆb10ö7nra[jKBQ{\'¢mh–P2:x×±ÿgºÜÙJ¹ÙxŞ?ilNÏÖFM?G\Z’V*]æ^fØäŠÁ¨\\V¿…Mª=§[u*¦¤±sõZíAá¢ûnŒ° G´·QFäöê\rü&ª{bŠ{”j:}IKåˆøäôæauåZu„]åº?”ƒä%(VãÜLèL?:‹şİ°\03–p`?rÿZ–Zh*òÁM?ëøJá˜ÛIÆĞh€­A?Ùu\\²J1yÔùçC_µm¨EAœ§\nAä?@\'”ÂØÄkãl3óë’ß“ªİ©{˜–[6–±aÀ[Æ˜aÄ-§^È4\\G¥éœ@Ò_0áƒ©²Af?\Z4{LãºA-Û?Í	BÕt %²*bqâ”¾á?ç×>ÈÁ¸&xß?$ˆù/²¶8±Eœ>wŒÊ w?´M—VÃPP5¼u\Z¼Rl†Iø‚‘ñù1Î¦pŠQa6dc+Š*°]‡ÂtE¹“²¶ñ{™5@i<Èû4f8½Ëâ©UxÕik‚h³ïsil¢¶¶áĞH”[İM2U\\%*ÙÑyz?ÃÎÖD­¼¢\nmA¿‚¤ (?j¾VgFS€‹€‹W÷NÔÑÕ,¦G~/R*}Ø†£]É‹HÃÒÃË ‰á(Õæ@ ì‚éµmj-(%&)W8¾p¿êÃaŠe$™Ÿ<<Ã‡+‹*…p‡qİËá?ãj]³­dàö*qÁÅ¥†hDÆ6QI®?ü6}(q‰˜L0	*ızôìW)wÓâ&çç0a2È7£¼\Z‹a³W?zµ|€\"+ul7«îe³dk€‹ÏæjçÓÍ‚*i˜?.« =İ GŠZØ4Ó	u›L?jäq°QD“´ hÎ¸HŠ?¿ÃÇ!°\0ã+p	8Nú  l9;’åš—JTPè³EŠ36?ĞÄD™P·êI,®,Ç>H,^Åv\ra¿[ÀØ}@Jä‹×‚N@›Éİşéœı.‘Ò?xØMæ¥@vçÚéÜX??o2Ë	Ò!x6DRR‚à[ÎnU<áAÉ”½©¸w‰×RÃ’C\no¾‚“Q`x%($	\Z$X•ªË.5X\\á–aÆı€Ú¨$vbœ œ&ÖĞRöÊä€Ñ`‹˜$$’Ì\\äPá¦[˜éb/\0—DsÔ.±AOØ±T»6İâÂÁ€¶ã¶ci=İİ{âƒFŸ›‰Ä?`İªx“„zÇ&çÖ†±o±²÷G°¨Ez©¯Ocd´pÀ%\nOÉÊ)?Å_\0UHpnÁ>6NÆ+cİTå‰ÆKãi=“·?ŠÁEØ\"£AC´1ã¥?„©ruíxfÖZ~HÿwÆB©)‡İ‚[¦Á2×¡	mxUUy†?»?aS4%€õ|U\nœÓ®eo‘Õ5îs‹Ëùu¾RS ‡Hˆi{€¡«„IÅwJ?>rFÄÃ°‰\\`ØdCê%\0ZäQQtJ\0,(è #,êÚñ0´*¤	Àî³± 9rµ“îwjbm\r½È§?`\nÂ?ª¤”ßC4øÓÃC »•\nøC¿ÁÖg6Xõ â)I0gi=ói#Yòœg`ê<G!?	üĞ÷DôıílWğÅÀLånz¼Bñ¦æ‘Š[+„–,Îôv¥Yc(™1b\0\r\\LZRL¯¯İê€k\0â]–‹a†š?O?æ766	PÖ¥Ò¥RlÉÙ9\0ö7Ğ»ŞŞÚ6?³ ‘ÕÂ·ƒÉé³ÓPã59ÿQÍP4%FªrÁ&tv?£/0\né	Îu9 {4B¨Øë¯$«ˆÜ#e•ÑİÌ°NGåš¯V°„Eq_ÆâÒü’ê‚Â‚âŠJœOIñPü,–×~ï0m\\YµVTXİÊFVh…e•.­ªbœvNuQ»\\¤U\r/ÔÊ*\nØ×eÃğ?ì Æ\0æW?.DÕ¥¸¯?ÌQnO·V‰ìWŠ)X\rQ\\.ê_ít²Z.ì±õ7¯M¶•ØßIß‰©œ]lm<.­0¦åC=º.‘—üÅÔßc\"¨¹”ğˆŠ\0Wí¡”j»5Œåñ~Ÿ<ÃÈïåIÅ|?\\…* ªƒİ’EÓ=8rø’?D?“£‘b×(ºµƒ×£ÆxG	§x.@©s2ô\"!Ø–By<@›ï(;‡¶2³RU\nxJVƒ¨¿4y\Z„?äR%æ ¶?°.R`›©˜hNñ^\"JX]4Šª\"?P€2rxqŒ\'¥2V/°	?şš«Ñhxâ¼¥pC¦y¼ØLA®Ù˜ØiEaŒĞ2KÍÏ^C}\nHQ§[ÃzÈğâ?0Ü¯tüMrò\0 ¡è–Œ£ûG]»FAÀbXŸP‡#³ÆlE?Ü˜‡´-†œÜ¾?ì|)·™0ù¿ç%+ã°\"Ğ·Âª‹Çb‚cö?$‚&˜« Ğ—#È}A -cÓX^³?‰œZ¦\" ¹r/§L+¦Q)Ò)™š1ˆGñ¡R—(Ò¿L\'´W§Z€p\'‡“~BFé\0ê.?s¢éò²\rªò†Üg§í??»—RJˆRJ/f´w=D	ÿ´ SMÉ¦ .B=•¨‘‡µMbQ¾`ÂmÛ¡\"nŸhL$zH4…¢ïÆ®·ó?Õ±¯tUÄp>t’ü¦Î.Şæ$¶›ıÆBuÜ©Sréf	¡GV@2?‚¾Á*ŸRdQr`IÜñ,fæ÷Â…ÌŠ>Zì#-%üªµ¿	R½F7ì/üî:RÖ©-.·\'K»Á¥X`š£ç26¦I?¦‘gtX•LãÓº:¨Zvö»œ“-cixrx´D„p@JÖ¨º½œ³S‹%kü<ÃeÖ2éãş‚L¬0&O,“?»¬y¾\0X\nà¤ŠQÛ2—øº ¬Rtÿã²Ğ¦±á‚ãñ~ÜÉk7ÕªÉ†SÄE—ÉÁü6P£Í‰İ¹Óißny¼²´¬Š–BçÕ ôÊÀı~E¥¡®Ë?šEşƒn]ÁQæ¥«ö2å«k·Œ¤Ö‡¤cò@ÿ2’›æ\0EÇ¤4Pá\rÄÈ­»npFô1†›™¥M•î)öJÊ\r%‘ß‹ n’Q¤{Œƒä¿ò$‹C4¡¿+Ô4³OiA?\'»;aÀ?±À~€ãVáÈPA‰Š{ıµŞ=¥Ôë³çíkH‚ê»BÚ gx>³wO{ «™7ñ6âØ«$:à3Á„ùÊP[Œc	$òBqÙ;Uù©DÏƒpÁØqh± lG1ÂhJmµã§–×R°A€u?\"„=ïVgGC´\'˜\\å¡u§ÿZ,µciz²z#MV};=ÓÌÛs{õ²eÙ»íÀğ†éØˆĞvÅ¡F?I8_Lµa‚Ù‰?/])KEœ’†£`?;\rÊ—°÷(+Ó»ı?Ï&­RYÄâÃ3(¨ÂÃ­un¨íÌ83…ÙGÔÑ±ş6Áä?Œ¤¥ßÈJÆ]L­?†ğ8U>*ƒ%úbKëc‚2œÙÛÑ@ŒË…/O\Zh?)¹÷Â©}¨¹¢œÓlô‰‘& —ÙÉ‘ÄÇØk=-N€ïàÙ·éøLØ?kNÓj\Z>Î@&MSkH•W´;¬îHY`¼}R\\Ç · Ó\rRåp4|\\ZvNÒ?°ŞIêB–ŠÏ_fÊ©$]ç@“wˆ­pÔL8“ÂEÌ#uˆüv6Jg:B\ZS‡I½hg`s2@lÉCd„Êí’%‚”äHé–¨1\'3#»–?º6ZiyN–Õp^Çà\rè~‰H³pÉI	§P°Ç„ä7iÅöŠD&GËN\\p?Eá*(©ôR÷«’‰xIZƒŒ%¿\Z¬‰×ĞÁr¨ã¥:ÜPBãzş‚n T,‹Ê´U¹/º°¾°y§ê\0@vİªßS§G ~îJê¹Š1L+H\nÅ+&\ZØø˜úQå4¡>Ei¸Oì?¯Æyœ\0jJà”èö{•ƒ‡éµÉ=\"d.–I2*ÆvFñÜó~Vä?*õq8wÍ‘1ÈÓ_ù3œæª>F(Ğ:’¥Á¬Œê²/+Ù	Š“\03s¨é“ìvË? ‘¥£hŒ\n°Øâ3ù(\nô„Á—y†WÀÙ2G—)`ÊLXz9%ŞQ¦)‘ÍåY¸Õèhu¸Ü	ÛŸ@?xŞ/	¥C\n0Z2»?çG1üóc#‹;Ò„Uj{úÌˆİm.£™Ã$2„¿^$»M;¿rÆ¹CDœ©‚{Œ%â1?ãØ¨ËŠ%‚ø¡6\Z·êğ bÿ˜V!ÈJ<?*n8ÔçLf7£ÙØÅ]æ¿te–?‚ï6»}J­¹ï#	1.†?)iï\Z°<Ì@ÒÃdRKvŸ’TÃşë™a³ôšÌªqå…YÉ5\r”¼ŠëÇW8“a´‹µ–<¹É¡Ã*PDñƒ®‰~:¢­6l¶ŒRrz²×—Zh(€°0Èc\0ãL‹·?\rD¹ˆˆÍ‘@`/Ê#&ä°ÈjÔ2±¤	çâ0mVÂ¾8_T\0‘Üª2™NÏ$şC—Ø£xÌï(JİÚx2ûz€&ºyâ?L•€´&˜–ÄXi¨¹İq…@?Šo&şãGÊ+°7J öjÉ™ø;İ­êPì‡p_$Ï‚ªXàéÎjZN\0 ¡ğ²SşfïÈ\Z ;^HõU4¤\ZF’A¹AA“Ù¤#yKì,zAf/Ûˆ?¾o$TLFàSÌ8*+–h<Ì{İ@>™Öµ+·Óœ–ŸÕ˜ôwÂù·‚Î/Œ÷{Gê?HêÖ_?ßâ>6ó=ìX3_ƒrÜÌ×Ê61%MĞ\'é}Š}?†ğóTò’~RÉ’h])o\n$çŞ·DŞ`\\êª	*iVİ=02Õ´ƒ¥´D{tÕü ¯âa?Ù‚pè?êj¢9€pÃ±C˜Ç¥¨ÖÒãæá‰ƒÂÿFı¼BÑµÔtd?A6šŒQ?°*ï>§€­¤[g‡l¸6Á¿¥CÃø?>‚5ËC,hñAàZ–é	 òŒHMŒÆnyç…`O¸C“ 0f\n¡~ìÔTR”Ò†±ë$Ì^v¦Ğ\Z(!\r!g»½ÂMŠ9&O!ƒb’vÊr9‰M“)\rQòÂÉ¶W*g&!WE#ÍÔ½Ù;öÁl§Ó?•ÚÙ¨¼ıq²C)·XGÔ02tìµxY*ã^n7}Qc‚¹ÌF_~V2TÓçc\\Ó5_Â“[*62éá\'Ã¨sNàİ\0%—OÎÒ…i-QÈbõÅ°®	<Õ×THš³êûÎí\"»?ëÕ”P »R8~õSÍ\\b×8>dn0\\ÁÛàó^±¶ÖQ\n›p-6£Š`«\"©nXÌ4… ç].ÍÑzUa¶¡/”“ª\"ÍGÆ³Æ‰yIí]ˆ5„p¶ªŒÊâ­œ$£Je¤åğštkSag‚rK¼ÜcbG-`öRâdŒ‚…°:.,2 )ó„Z%çzX\0r\'ø{Id0’FFó´Ó8~—Ö•bË*ö]ü½/–Aîı\08>C<á•¶Ç™¹¿?ÓÂÇ¨ÖÊWósÍìJ1?,-;FtåUƒ.õµqŸ“Î Å¢Vœ<Ûi¹èÕ¸^ôQ.˜c¿E<?ºÛ£?ÿ¢ªcĞ—İ)u]^\\`Á\"9õjÛ,çŞI\0\"‘H6YK†Ş–0u‡D–€RjM¯)á©JŠ¶ ¡›ÜŞ??²\Z›ŸÄ‘^^Sï5‚ÄÍ\n%¦õe{¿‰ìî³…m û?~û:ÃË:k™JA^VÜ¥n%÷‡à´R7Ô±›ä<Æí´ã<)›	€ûi÷ƒoz7›¨×ã§ş–=0¼…^Êî»ï@Ñ–yxÆjÃ0ÂÕ¥ùUÅe¥YM–Ñc·bÊı„_ãs1?Ñ”á,•òXA“œ?@bÙ¹tj9÷ÂWô~î%W=lMK«KJ 3,æ)YªÄJ‰u©Ù˜Aˆuˆ¤³Ô\ZÂ?ê?\r\"mÀáFÎrÊĞ}l¢pè–½åìjFRX°¹ØÓ`\0Nµ¥Fá\nÓ!Òy±Sö\rºâ)¢ïİ‹Çòc?-?™şcZxNLÂ\"aDA-ğìX„ª,“Ú”Ñ$\\‘m)r–Áªtl™Sa\n\rµ&+[œÈ^c£)/iì?ÍäG1?£W~#NU™¨d<ÃÃ¡dm¯È	R²S²š?	Â?×°çk4?# c›XĞ5Eu_ò˜D²Y$C¶igÎ‚\niM?I‹T·W´–DQM“ÀeFua;2J@<IÄæ¢›K¥JIJt<…Ÿ•Bônß‰”\ZAd²ëeĞ¸á\0?iy¸%*µ£?{O¤åáÿY\Z-ÿ—“¨,şƒB÷ŠB! ÄCóÍ(ÆÀ/ú$u=S1A´}¤U®ğ§Ò!·6À \0âF?Ú]‚o5u…·¨£¶e7Ã‰¥+vb•İÄ—$3±íFÈ©= &C6N¥#H“ıËT`„D¼yF˜›Jrï¹Š?gFZ¯¦8¸—sÆEv%%“·	p»6£¬lnëiºêÊ•!=ŞŠJ¾&c)Ià\Z(?2.ƒ¢ı?Aæt*Muh‹\ræ‘ªãD=z’Ë_„iy‡ )FÔD]ì< Î1Óƒ}	¨şŞ?ÌÎI¦meob?ø 3l[ßá™…Ğ†•„[¢­”\ZÉ2’7W¸Ï“¶2¹VºÕÅ\r;ÿÚîåÄËfğMSµ4Áğ;e8§%‚Ú„í¦uà\\\nŠó`÷d:\n´iÚv²=jœ‹ö±lõoÒšVMi•g6¯-dØ6»½q¶-n¹ù9¡Ş 6f?œ×ÆwDœfXá™¥Îìÿ<3œş¿ÃÌÄûr\\p›Kã³Í8”•:L3&ÒxÒô¶Úİ\n{iHÕªÈÛ‚f…?+öM±*òh½J¸Ky•‹#jAYb’>-ŞA™wİù ò`?Kp¾˜„û\'ÜZ&%¶?bãxL© 6î×ı@±­°š?ïÎJ»<R\rLãİk‚TST£æÂ&ÔÀ$áF\n¡Ü{¡¦?Œ­ª@š×\n›ÕSæÓ´VØ¤.¨ÀØ7­p¯}$Mi…~¯ÛïmI\'dÄÿû¨„EŞÜıb+¹œ¯ä¶ÈXr÷ƒ³äş~¬¥‰©Mò–ÜTæ|EpU3ò¥ïĞ+®)\r•Åkkíbx›U†ÜXŒkQ=»Ü{+=?p—å>X iÈi?îÃÉıƒã¤á8¹-³œÜß?ç@—NÉwH“²¢±òÓ8úÔ@ÿÔ©\r­ö?Gñ^ ˜Oa+ŞT^%)VaZH²Q¸ƒiÉ¸2¦´C¿	‡‘”n\0Ô¸ù\ZÓ\"£Ğìúòœ/v³q6ªI#\'Â·Yt$%t³3KöÍ;%bïøƒÊÜšSMZg(€öÕ?“nckCû!şØS-«±Zm Ò#)Ò¡%‰ “$–ºv(»¤“Ş÷ßìdË¥Lâô-Mêÿ?ãoæô?µ!7û¿%VTğû°ú±‘P&YÌ°ôP¦~@©¬|$CªU ÑÉ†¢6Ï?<œúœ¾3µ9‘JlüÌ¨áy£kàU ÑA:N0ù<LáRÓ\'‰á	œ^åx¥¹ 8kI&?Íj–ğW()Q¢ó˜X_‹ÚÎ%?Mi$\0×HÆììË¦9@©5c_:8\0[{Ç6&?\'kß´·t­ë÷U{ûıcêMÒt„Ñt¤ešXš×£l‰˜E5ùAéÎ(O,ááJn,—{É»+Ê‡dÅ$Õ©«òS¼§–Îu2\Z1§E¥6F@—´…ÇçáÙwRÄ‰„49Aq\"ê?ËŠÈéîÒrº·°ì;8ŞÇ+/ÖÜö {R-XtÂÅË°±zTi^öØäÆ%ƒãê³\nQjÓ)@“´­äW(Ø„ù”œDn*K+[´O’—Eı(xÉ\0?¨!­|i´Ö@ÉY…[mÔò¿XC­†%ûïRQ\r4,ä…ÖßĞÃ‡Ÿ•‚…Z\0¨sCg‘;Â“I!ÃPÛ¹P¤Ç®â…/¨n/Lg®1îŒ%pät|†eºËÕ¤F3À®Ã\0H¸Xé¡vÀõ®ğBÃ?\'º¸®ˆU²öQæâN\ndĞy/\0„‡bØ¥–÷Ç‡¹ˆgº!ë„Š¯=t|Ø\nƒœ§²É«•%‚[+ƒehX†ï?@V3.ŠFw.»Rß?ğ$]\Z—Ãxf?Ö=»_/ï¢çœ¼X7î\ZgØ$(évld>qúµ”ÜK»\rA•Œğ	Ù„!PÌ¿0\\²(Ã°™»!5DìÒÄrqG¿.Ã§ÿ¢~\"\0ıÅ¼-oÔ%N¸cÔŠ–çl±±Ä€wñQ` \0R ì‰bˆ	Vh\0%ëñE¾B”FˆxH\0†¥kXmÇ\0Á‰}AÒÀ Š3–ıTƒ˜/‰?)¦Å3WÕà›0?|\"²³Í?‡‹-ˆ[bïK¨÷g+óÈ\"8î}áÌ¢¯„Kô’?lY3Ï´93cÀi¹9²sÎÏe¯¥(š=·Ï½TP^ª•äº,ƒ¶}DË(ØÒ±dÄ#âšòL¿~ô?¾‚ÇÆJƒ 7ê`@œtô€W9û0 c0\\¬…hCpArö°dÅ½]m/‹ìEk®ÏPé¼R1/7ª9IÙUå¨’e FW øTY}º?5Ù\'õ _Vs±ôe–ât_\0„QJ;\0×‚´Ü*}r¸4£{ùáR\"q|/Éª0(ŞaàWP}<•ºóÒ½G*KĞq(3Ë%?Î¤*J%€?†\'º`cr nq¼†L¯àíx`Ú>uNÊÂìToÊkÄ<nÙÉ?ø-!Ì\"j}„È~\"Å^1Œ”ğ@8ä?íMÛá%\nQiğTù	·ğÁÀÅä3¼\'W5E7¥7¥b‚ıĞş©0å¥ÿ÷4˜f“ÀšQ`şàéÿ<İÑQFòt\\Ûï½[$gş=ø6?föÙ6 ãoãÚ0ó?˜öÿ<Ó¶7?jVRmS^™£µÀ¹yÉ‹Ã¿(Å’İÔ0wŠ\nzàigMÏ~ä2*è·7©‹M$,’-®wô¸i2G1©†29IQä\"!ß·ìDÑÎêôdZ\"?\'%3‘†“&5ÑNHäÍ50\n ÑÍ2UQM$Ü«ôAÅluf6Ÿ7˜‘RÏ-qí“““W¹‰lA€ƒ•üû–&(6¹¥ÄÀ”tÀ´i:?=nÃJD,5\"­íUıœ’Ç—Ü‡›Ÿ)nl&nzFÆ^keÎ„AØ’Pş±sêèo%/?O Ksô!ä·á®²g;@ŸÄs` gÒc²ë–:n8R3?„òù°AÃ8u\nój‚‘Áƒû§ƒÂ“ÿç”P\\®ßàF«A\0t\07???½˜Ô7Œ¨£Zq#~ª›İaĞŞt<‘jàóÛåÍ¼Zgp`“¢éêLİ¨ Å{VŠJJ€Ğ‡TË]Qä\n“c„^à C”S€¸:ü3ÉÂ?<îlk·îÎ¨Üÿ*wCm(’Fç†íå§¼sÄÃÓw\r{¥T.Ãâ;ô»ªåüáß¨—#êş6ÅœHüÍüÕ?RäİwoJ‘wÿåbâşá?ÿ§ÚşzU?Òïu,Î2%E•ÎÉEG?9eš€qWí#”|QòÙö17¼·>>*âº4v?‡’³ZQW¥”\0Ç¸Dä‰íÂ^¢ÿ‘EyìIËÎÑ\"Çæ¿»»O³ÿ?R©ÈûeRQÁ~H$ödjH¤#‘r[HŒğÔÅÃõ•2·ÕBd¡5)­r÷[\\˜Üß,¯p-³ımòŠ¼IØsö·É+ô×˜ÍË+g2~ZyEbÀk4-¯r÷S`‰m©+H	”¶œ†ó.,8|ÆK=•bÑ€1\'\"eÁ‡úşAï05wÀ¢me&[”Ü[>8K·NÍ×æ§hÃ\ZtAÔˆ\0ènœ l–‡BíÅ	tØ–:f±EùaEÊ¤¬x-?ÈîY“¯H€õ5vÇèxm¶?\0¡eËŒ*{5Lè³8ƒ0‚è•#Vx¤×ÎˆÉFÕlJV€\'âğ”\\ŞA?Òş˜OİªCNv÷%-«Ò/š­6²ª¦\"oÌjÂ·š?Û¤?kç°aˆ-Í/Y^QXYYXğ{ê¹M(©òBU,Ò‰ôºEîoV.rÿ°wÿçµŞÄÜÑå;Yé¨4båàvK‡>?ôOÂe·-Şû\"\'˜b:o*‹h¢£ªo¢ÀÈ6è	‹³h5P­ôÃ²…Yj‚½$õ:ÇxŸ½?šå¡»tŠßRfë*0ì(‹h,½¿¹¶öIAÊ	A-²ä¡IçWh™¼û±8í\"kßyêo<3gÄC‰aC?Ò«\ràjn·DH\0Ó«;ı/´áÉ&4«ˆvGRÈ¹x¯c‹7å\"K?ìÜå‚}Î[Æ£vÒI§şoc.ÿ›şU0…¨²)Áô~;İ«($š¼Ã)(L—DÖki™l‰lŒ\"‘›Eº]?Ã¤ÃíæŠi]ÿ*n—ÊÃP’oç™—:.­ì¹\\@n:5–(”ì÷VÆxÖ·ß;NY`Ï;ê¸Ä\rüË?¨o?¨¯¥ƒ©èX›4çSñoTM:uJ¨\\ÿDZ ‚ì©*(eËº–\nGäË¨Ô0?4\n°ó@‰nĞ£^ËÁB&úké¨š\Z\\¿WËqw‡”ì<in  ğâ»(A4‚lZƒZ\Ztp‘”@¿³JÊæ·H]ÎÎª‚¼Z˜t—¢Q¦0+J2ÛÚGfåØ&\'Û¢Œ	3*-İ˜\0†LC‰`Uf	çáÉlÊ9Q?bA¾L\0mÁ?¸Ïşs?~+RH=™ıïò ŞÖ;5ëK6\'\n ŞCŸñ0?ıbB¢»x*1ş¿öŞl»?ëZİ¯àù‡ezG(\0©Æ)Ê¡(Rb¢n“Td_[ƒ£\0È’€*lTA$¼­û??:_pÏÃù˜ûwv««\0ÕØN\"$?ªµæêg·f3–7fØïCd»QïR÷Ü1íJ¦n6ìaj.d(ô2êHŠ=š³ıO,ÇKF¨)“áÑªpxvËŸn¾ù·;XÙ$\"EÁ‘iè¼i_´Û?mçÉØ\\äŞ=÷ÉÌ²Ée:î]$·ã))¢ìí›~Àjˆ¾<;`Ú*9‹V{eîÓEÆop2lH²É¦.,SWŞ]†­ÿJ¶®vºìê¡=\'gYÓÂ+^ï¢²«]©—Ü¼Íÿ}w‹ÿûE/G¨œíã’¬å?‡×¦çlM9ÒäÊ?à®åÉ•«Â)®ı¼çƒ“y}¥Xónl“˜ÁÁáõĞ\"`¹k?ß?ôáÊ”ô&ÛîÌÕØû\'¬š0¾˜f,ğÜ3F-ŞGõˆ$rXÊABÍ?Jùzë<Ñ™]¡Ì£º ˆGıíÍÑJN(üQ´×?t•Ú¿5í¥È³W§¿Zé+“áü–üƒãİI¯ôîNn_z3?Ìœ„ŸË³¨áN´X&/â­Lc8FãT«æñÆ>ŸgÓQÇŒ}šv–¬I!“ 6¾dÉÇ \n \'İğ,Â«Dñ,JÒˆaÊAÜX©¹;Sl1ÆN-_Ù«!Qr5ë•Lª2_©äÕ#Dà¥†İU½hÒ“´FİP€uıˆo0²F!\rGÇŒBºM>/À¿Ü1odiÖrj|™¿nH©´Ñ½­¬õÍ| ­şNQ$Ü…r`‹Á$´Ñ`”£¤?&øê7ÁQ·ÿø(ê*èé0N{“ˆ¯Aô©‘?E(ÃEFxÊõ?\" {w›êÖ]\Z\0Ìş!¾<I&“~XjMà_w©ÜGİq¹\r©Á0IúlI0±é»´ÉJ‰4`Ï¨¤¶\'N_İ›œ—?leÉxìËÊ…Ò<$ÛV‚‰‚]B?¨—œî‹èŒ¦¢Ø1` ­g2—\"R…j‚,1[¬3	ªvÄVn_Ô) 2F£ 2Ôaså¦7ÃYÿÖ¿¿íÒ…Ì Š@§¬cH†(æY\Z#5—²œëó¾2€½,[Øı&{Ó	†?ÑFl¨?gÓ=/Vy`¤ßØÂ_Yk°\\/0gİd‚å&	†Â¤lúGß[P`46ÍÎB2ĞJ=b*–[²~€‚x^zÄ†œfûa8S}ŠÓ}ù€¬Ùş³cmö¨n¶7€¼?&I,Y2ê/Ğ³Ğ’Ÿh@6‹$X\Z]ùŞíõ{w×ŸŸ¸$&™YáŒLÂÈn\"Ê‡%6ÅİH°İø¬x_Ë½ÿ&à³É²_ˆ’ ÖjÑ	GPP›=ótJaz<|ãl™B©ÚÈó×†^¬5ş 2é²vMK?¬%I•ïE)Ÿ«?*Ckrto:¡QÊ­3§}2Uã•ÓhŸî-s	_‘ªiˆ1lhxws8p\0šğİ¥$æ2J&zÀ9‚§·ËÂW Gš²á†J§C\ZaÉ­(©x^.ù¥eadN2\nğ„)ZÓ<’BLaı?çÎ•0e·kí†>£Ğâ£ÇP^ßU4ÉQOË‰S(‰M·\\BÂ£à¼@\\ 0ö¦µA×§èÓ±í`MJ;Ô›]…¦/¤åLÁ½>_•–»ÜtEZîQpÏŒïŠ´Ü£à ‡–/AÇêíÀ¸2÷©·ÏÅ~¥ã_é¸¦ã4OÂ1Ætô/„ÁÑB>8Ÿ´{$ıw§åÇaFæ%?^õÏ.¾¯$ä»j•\n¬’áw,XÕÓ?]Ày?œcôF?¾¼#lREXÚê¯(]‘CËï¾=«H”†¡ı\\Â ©ä†Ê¤Ç©ƒÚ²¶:İ?g§Öû•”§wÖ5‘Wuda¦³+TÒ\nö1Ò„%N§Sb”f7½C¥šº9Ü?Aìê¾hê4åh5pŠ0À^r´\n†Úf·TØÁâpü{?&oní»CÑu…qßóCÒ’ò*\r|Õ³/Í[•\Z?¨v=?Òj™ËÑ|Ó8½E]İ*³P3g‚9.½G\ZáŞ«Z³xÙ_:`8Ò}ŒÇÃ‹ÃDÍšTì/¬œÂÁæØEW.ì!î<×Ñ¤f©lªFQš\Z½¦©bvípÏŠ¦ÔAìA\'Ó³s\rN“[]ŒY2òˆI*¥Ô¤F2QğMœÒK$?] ÃZ7ÊĞıÂ£óÒëÉÇì‘všØÍî…1¶-ìÌsË(zÖRÈú‰e&‰–F ‰?	3‡È£ô$;n×(Ûö7(¢Ú“`¡û¹$_ß?0‘³·©—MªÕóâ?ÿ÷4ê½C{SlR¼éem-»Å¬³ÄV)0ºÉ8øUê(Ü…KeÁ»?6…>[?ÙrDà€âüÇ?©Ü?[ wì.Å!8Bmb¨~3{‚ÍİZÅşÖpè^‡Ad8&sÓ]¨DMğÏ«Ğ>f¤(Ã&\rj¡I´#Ê2÷˜l¤&æş¶^+vsD¶PÖÛ·Ã5hASì(û(e|“!ùAù-w·èÚ?¼ä,š‚ãÂ*ĞnH®ª´}šyÛ•Ü Pn°~?.é©G!ûd!–+¯MÔ£¡ëtUAÀˆ•p³ Ìë§.f°ó²	6Ï°˜•ù<†øfÒì¥³ª…úf3ÿ¶¬œS(Ïgkè™*áÿP0‡ıC©y÷‡ïË™?¶@Š§EÔ‡Œ‰}ñèsŒs\\Á–9ÌXf\\Ğ-/–·;¨äÊòŠ‰\n?Ã”Q%6vg2¨}\Z]§r§äOqæyäÈ®œ³Áü„	u‘‘sµ?¤1\"©¤œ©ó4¾]f%SÁÚñT\\~Îd9;ÒyLÜÕX·“È·!q97ÙÏÈ¸±.ªÀ·9Ë~%¶M¬\'¨\\‰mó™µÜ?ÁØ¶³ævè*l[Ysá¤Î,-àÚJx5?!@K0myVÍSÊ–3mN\rÏVäÔò\\îBí7äÔx‹~\"£ælè/Í§Éù¯âÒ2/eü•™4?>™GC@ŸÌ¢Qo®È¡Yê0‡A£ƒ˜_FÇ\r*O\"ærkÀÕ¸6êèïÇ´—¡WJÔo.`Ù.>!KÚ›É²OWÌ9”Å\n2êö‹´%åaªğ?Ø¥á×§a|–-“•/8û\Z¥ƒÔYÚŸGötlAÌ2ûËg\'5Q&,Ùû1ÉšÉ;4ß„\Zíü8\\¦Í=æ‰!Ù:=¥ªi³7œÊµ™g‹áñWl©®Ã6¸®„q˜¸)äMó™ª??ñ§Ö˜¡¦?RqqÆálrƒl\nëÆ{wÂw~¨ı=£¨>‚RŒù|!3	Ã×™ŠüÎ†Ç#ÔÚSÃ9ğ 8È‡’¥QRØ0¯o!\rõe”f6‚ŒE`$1¶=ÇÔJZ‰fŞrd.›F¡êÇùşˆ<\"`ñ)»%bë>N$äÍt‡Q!?«^“sŒ ?sá·ØC?&cì¿[¡uÒ1¹wÌ…)e…fEvi%—ÚhÃÿš¶\'²A(nï8Ò´f|ò7iÔuàºf.ÃÁ!{Aèˆu¼wa8Öã%øîşĞ§?®œ#Â\"Ü Ã±uaÿ¾s—¿_Ø°Ï¦#4ÅN¥ÆÏ—G1‡O2?zÉ¨‹“¬ ØFbrj/\'¶ú·ot?+€Wil/Ÿ2	[ÅÓdšÊmB[½6qÜiæ\'•v0n	‘BE­oWI_ÎÓÅšÎ¶8V½Ó0‡K‡AÁ~[+\'jÅSãÛWºBËk˜¨úÆ€¥—\nöÀ°6â+m£/¡º¢fÃSB<¡ˆx>}Pæí| ‚m÷ü#ËQéupê¬À”&k;…~8JÚ=U7¾ü6™^XÉ¨;¥ô\n>Xr³Jƒ{D„ë§Í7MùMHç§;oªØÀE—¯_ÎÒÿj—?Ğe:Ö„™ÆÖVO‘æa\ZÒÉÌ?ê\r}Jbæ–ÜIé¼?öh°îÓ?7Ô‡@xí1Ò–öNÎ1W\'j2ş68­ğTç[á§¥O7KŸŞ,é‘ûşÖ‚÷·«GäOŸ5¿¸…©¶ªnABŒM¤u‰ËG? Á|ã‡ÓaÆ\rĞ™dXÍß,0ˆ>Ü ¦\r%ÃßöüSÁ¦ºcùt·!l¡ShèSùk?çtéÛêÿšâ–Ï‡Ú’µ1Ö4]•z{h#´g»{G/<&™?N}9\nz€Ü‰ëpl„q±Æ?óæ™fó\'q4iÖˆZg¯tÀ-†îx\0q\0-ËÍèY:?qY¬,A•%óòx‰0d•Ê]o†¤†…),xNÍ\Z\08j<¹¯CÛä¿q½‹0®eÌÎ[Òœù›£m°¥„õòišAîöeïò4ÍFòvÓ4÷lÉÇnÈ.}ŒÀ“¡ÖÕ¦}ô£~ÕÇ ídƒú*šü™˜?Ù¹v;ªsşçş¶:züĞå|şÜoÒÿW­Î³»dÔz\0¨û§ŞşN¦Bæøa‘HÁŞ$¿)\"iM?ƒA¹;ã•¶¥…eOëd*ÌTşL’ÉHR”Lß%€	jS\r€\r\r™ã£+wæ¨?Ü¬ò‘K+2	Z\"á\'?ŒWyq?¹c¢Ë~ßÉôNQ#ÙäÃìô¹úø£/÷{ÃhØxr<v’ıúg¡?Ì«?ˆÏàT	sY/n	kÕh(›èGol~•[4ÖºğÒÁîÅÌ<*Ä%›W?™f4°\n\0KoçÇx>…ø€ùÔ‡­óşéÈÏQXÈq•‰9â›§êi™ĞHeRü›8şü\'P¸7\'KLÄñóç?=CİÃ\\ÄrÖW´zäşH0>yä?^}ä?Î¹xr&ùû	i›õï0ôƒƒSĞ?Sx™µ\0~˜GÕ¦«Ş;\nN‚g}‡¿Iœ•øZzĞ[ÆÃ›gl·à¼,V“˜ì-èQÈò$/¸;óÜVu­¡?ÁØ«Úô4©)e=Õ®¿ìT`ãN1®¿98á‹/Êà^\0b²Æcb½× 	úà¦İò-	:Ì·E\Zù?ë]°\rÂÌû?Ş3ïdT¹?=*½Š\'Q<Óß?1à±şñ,ìGÓÑÃdèE@ĞÕ±ÄSŒ.ßãM|ğ8\nĞƒÂ\\ß±İÕ/ò«g}ìrÖ>GŞ”¤î?Q×ı5„¹+Î¾Çõ:@=u®.—˜ZeÈºÙâäJ(çV7 ?1LaĞ;oPâx+R‚4LAğÙA§İ+®…?wÏ›¥àÑq66½K½ˆø£³EÔ:›„õÕ$ÄsHÃD$õ‚\Z‹?9‚çâØºëàA{5–İ+şBÂÈ\0q€\ZH6ñK–u´}$9¹AŸõ €\0crî0Âl=áû•§Ü»®ï[½“`?a‰½ßz_ü;ÇÛøˆXfm\\?!{‹áå€s?¯ÿ×õßÍ{?7_*½\Z_‰.•0 ÙGÒ%8.ŸF™šj’dtxÏD‡X@­\n€\nÔzEŞ# X.­)r’•´g­)AyÕ´g.­© ,íY’Ö”¬nö,Kkªh‹¦=KÓš ‡ö,GkŠlTö,EkŠPŠ´gZS¤OI†ù|Q~OÆvK£]\"ìëïkE6Í?&MË¦ódı#^–4Í™“¯¤éCšÔtLAµñš’‘¤ˆ`Î¼e—dìkŒr´HDš™%ìxÏ[Îÿ`$1ø§‘¸iŒ¶x¸(=vzej\'rQÚ•¨e%±¡ôä0\\4&XEL±HXÊ¬j‘y¾°”\'X4ªJX*¬Šú…%?`UQ¥%„%—`•0âË	K>Á*²K\nK‚U¼X)–âépØâ„`Daöp))ñ™C¿JUm_Xv2Œ—K¤Š7<¢÷¥§Š„\"‰ƒ^–)şP„Î<¼Ï+Ô>ğ‚?ìCxU\Z˜Õ\'!?•öê¿™Ôqûdªİ»u¦•´¯É.ğ‚NµTªÃ¸Âà}ŠH%\Z~­s©å“Nºş3åÒ?\Zİ¸ó™Èh\n|fö%èèÆ?V7ÊªX¼Pñ¢, §ó„×eu?43E\\veİ#’Ó\n¸Œî±|ëÏt?_Éi9bŠ¦¿ÕûoböKD9äü&6w2ŒÍñï­Ô´ê??!Í¿QÍ?8ñŒO.ĞÉ°/æ‰gı?öMœYQÊÂÄiÙ¨&xÑOkÊg’öÏÓšÒÅÒæí;.ãÎÆÔI	Ö@s\0íğ1Â ¨ÃI¥NÈjÆ¹|æc¿9ã/¢ìOUî}&É§DÓW.û”rŸËèú?Wï¨¢|ôj<…Àr*½\n¯Æ#_UzîçwPé•ÉÆK	H%x·ÌôÈÁÃ‚ÚÂ>n|rş—Pì}•w~7yG?QBwQuº«Á	\Z¥§nà~ìÎ?\nºAÖú!úeğ¡ÕQ5w.nyIjÑ•¥æIEVzºq™ë\'K!Oµ<i.œ+ iJó{€â¿\nV_«ÁÊ\'è¿…`õ»ÑïüƒiÑ\nD‚ê`Ö!§d.õïî¢!œ5©bì\Z;F!zjOÑ?{œa–zóÓÜ÷ÎÏ„?¡Î,Àù§Gû{/=Ûşhÿ\'Î?Ğ«õnûVû´ :Çm—ÒN+±—Ğö²À£œÌÆ¡Úl_j|±Ä*o+pN9 Ê¨#\0–Ôå•õKúÄq(p¿•LZét<â9=™L¹¢à¢Ó×¡vLÑ[Z)šnĞ6®·³l@x¾ö†íE?œ­CÁ{aˆÀÁÅ?±»€‘aUN<Ê?•&?,×(£¸¿Ö‹t¢ÚêÛàì,\nÓU“š—HËF,;„	c,§s*D”×›(\ZeSãßA¯D´¢øùñ£ƒÏO^î<ñÂùR<Üú\0J¯áõbŸíì¾zzrj`˜z„(^	bDÄõÃatFx ¸/?ÉîE_v	§ƒ.)05°#ê|(´¤\"· Õ%ŸV¬&A¨|ÚX\n?†¹İGbbŸî¾±Şå’Ë)‰Å:º¸ñ? åÏ”!ÈwUÈa£SK «L¡oißÅ^’LúD{?#I×FR—pÅ1vÄÛÑqƒlªL’¡Bù’Ò?.*ízRÎ~³´Âø·Ä‰sén—V˜ÓÂ?¢›èÜ¾++?~é“ìá,Ğ\räü´§¢wŒJr…tª°µB”&jÕögUH»øé „?É >xOÒ04¡`°iËÓÌõ3Ğ4ñÔóWOŸ*‡è¢•Dy/îÑ\"»\0çxË~H.ôüÀñï?‡ÁXaXñ tª‘\rŒ‹¡Cÿ[~ÆÍ1ÀøÓôd€+YzÀI;Uub1Sb?è–pS:Ó -äÿòyé+¦Ô\'0Üñâ·^5¹À¨¾ÎŠƒğüoN_é°µh¿É¸tˆ”ò”£2	†˜Òô×©ÈĞ,ÂèMİW\'­»°ù?#ìFFïÂ-µzíÛ?{›ÛE	i!¿\"ÑÂ)e@@²\nñ(kˆ£%¼Y³ÌCœO&E&‚hòé•ÔfŸOCæşŒæl[tdĞ×í•2}™*×¬	ÛšşeouÚ\ZÌ\0«®OÓÉ:*§†ë)<\r×?KZ?‰FaŠü€D˜°m0;“PCâ$ÄŸ<!ö‰ûŠxHûûSÄ@ Ï5 ãV1føtÄÍk<ÜÎ›f§9h¦¿4;møÖi¦â´UêâR˜fp&M|¸­|cûuÉ\nÃèLÇıcaÅlÅñpš’<?eÌ§>cH4è*fKå.m¾iñ—;oÔ\rug{ef^İÔ¯¾“W%úÊËæL|ø—ÓU–©?Â4¾/×–é6İòVµ‰}`DâLBÃL Oÿ,\"wpq[æ÷¬O‹İX¤/HÆ-!~š!ai¹ÿvšf¬x_C\rS?ÜfxÄTI×%Vh†ê­­ì\'±(;e\rnª–ÒÓÏSoŸ|OÊ·Ün(š!½©:MX??ØU¯\'¸,ÑˆNDRå¤²S~!s}%d‰\0{rrğO$Á>Ú	Bìî	°9‘ÆáÈ¬¥¡ùcdÃ\rGf•¨€kG$.à-¥!Èì¸RğU¥_Ûí*ñwÎ…æÄß\nõË•Åß\"Éõª,-şàhqøªâo	;dWKy‡¯&ş	¾‡¯\"şÕ®8¼¬ø[rëëŠÃK‰¿EI·DşÃ‰¿x¨É¿¥løWù÷«üûUşı*ÿ~•½O	rı*\0ÿ“	ÀÈç•€¿JÀŸSÆ=õÏ#Ó‚-ˆ×âD´ñ\rªJDdÎä®Î$¹{£€Á–r‚w[ÁÜ}7I…M‘?–­»‹¡ù¼HYÆæ\0#´bzYøÏ	W\"	rÀ2º/O‰›‡í®E±8£¢µG¹¶ÇËÊ‡r3\ZlW”¶_û	ãøccüR5±Ã²“‘²$C•(~Ÿ¼ã`?Ea@VµiLZÜÌ~İ<bĞá¯‹3KFL‹Iã!Ï}øbrK&XÜgÁådÿà?0«w\Z$ ©¢„r#ÁÍÉRºEÒ[\0Ä™lMXpMZUıÄÉˆ\n”ŒF’Õ¢È&“wÆÎD‘~ùü±¶ãá!r‡?Á°Ê™¡´İ(¦ÆõÇb2¶\0³Ù{W\"‡OrqÌ¥ıQ?õÎ9q›lÛmd­ç±ıK\n!ï>Ú“,å¥…rO^?¤.Q€K˜gD®‡åú²–‚£ä›{jv0B\ZM%ÿé.Šöçö,é|™\Z;`ôÀ4q3G»Ä©¨q‹Ùôã¬ßÚhßio~Îè€¿SærsÚ?ËH+F²n¤ûO?òËñx+bış.‰bé“§v²Ñ¿,±Ó§.\rƒIïÜ?4›J8£Å*ÀÑ¹Ö³sà\'Ö¬”ô±rÊÒ¤\nº©Ã§²é5ÎµC(uz&Zß¤ZrH–£\Z?L£Ùz‘…Æıio¡f¬8\"@ğnM»k‰«A°è(UTBq¼[} ?1f*0¡Ş™Çî†Ù‡˜d¬¼QÃH¨zMh5fh_è‘\'oêË¬g‹+®W°Î2¢¬keˆaıi?˜%?/`‡éXáaI(‘E‰¦<IÄ˜U[Qj;{2­×yâu(U—Ë,-9g:®“vÅóŸ-óY¾Šc¬&ÃÕd·HËÉğÈ¦/º{¼1³S™?8Ôql7¤×DeK\"Í%ñUC,çH†ÃŸ(à­§av=%á7I…iŸ\'Y‚á–Y;•Âã?¢Çg“`|î‡äsç Ÿ4É€ú¨ÈÍĞ½¶\"ÔËKñIuœÈ?Èz>@ù´<PAä(2ÈUomxár×1½’$<;4ujTJ”Oƒ@èòÅi<ŸïØ‡ÏHşşi©°y¿	Dvr>\r,@‘DlW¦?¥„«ŠOCñ”!ZÆH€jñWÃºTÒ\"Åà?³ÓÀå6m6f_‘¢f|\n;+JÌ†2Ó>Cä±$HIÉ/#¿}R‡kû•Ú}¥v_©İ‡Ú!^ÿJğ\nçâËÒ:>zQ?µåwˆ?A	}b•“–}É?iˆ%¿\n¡8UÑ÷ª¥ápÖ,™5‡€,DqZÄ²d”¤[Î\rwHOÙ…£«2\nşÉJ!Ö}V¤d–/7stŒÚÃ[é(#½\"ğ0áõáö6&™sœ³bº;3öSA¦¹>aWÇ…|°Ê!éûÒøº%hb“²*<f—XÌ_»¥²\\Çpä:rı|”X‰¬?¦^L37…gêì§¯RÇçCÂúñ‹¢aiä+\"v?ˆ8Cµlò2JëZr?¸šgæ&?pºúIÈl°èãh#—jñ|”TÙ‘y³ìe•Wõ eEá6Ú¾ÌQ2(ºçĞm¡ø`ò€U‡úø÷¤?É×Pf‡‚w ¹u‰.; öËÉEÁèà³ßEüó¡Åô$É‚á•sÊ8uç\'—‰§˜ê¥L©/h±7?pNØ\n=@>áL[%F;T(Š˜¯ÏCY®»?ÊjW5yU¹™–?À¥’3?“I)â¨ÌWU=weHõ÷š»ê”_ËÎŞ²9¿\n@dF¯6ƒ%’gô\n3XÔÆ™”H1ƒ•	;—?ÀÅ;ÿUçÉ!š„\r?Oè_ùyµçL M¢X®&*yXYšÄß\Zı)\'vŠ¦Ñêq?Óå2a\ZWºôB8™‡?f%œês÷r‘ş0á?e²\0{áÜ6v8Ì¢™GRÒ¬èâ<muY/¹²¥»ëJGÃKTƒİs,ò–	|5óXğ0ÖÉ»´?\0×Ó\rË6˜µÖ?&ešâeÖÌ)È²p4Î¼Ëı¢Öy˜?‹å¨·îÀlehüÅ	Øı}P€²;@ËÒ\0­á‡Ì:Ì~Sló.3ı~î¬XóDmÕK®L„&È\r­ZÑ¦„¸ ê´¡?;$ò*ŒÏ´q¿!ï·ª,Á®°JĞxjö]@·=::|YñlÁh%ãjé¢£½à(èc_â4ìMÙ¢xå£¹ì4k1#IÌ!ue”h\'7ššnÈ¡2®>­Bélcg?‡ŒÕ¤\0‚ŸYÒzš$ïd›ç¼%2¶HûHÅX…6ì›Rm˜³rQæiŸ*p¶Ì2ÅL8NS“–‹~‚<H#£ñÖa__[/,»3bGˆø,tD]ƒÂ8.´fc[ó¿{ŒBê_%\rtñíÇ«¥\\DÆÊäUN÷ŠWƒ\ZôÌI„`ç1;åœ‘œ±”-õ=?“ÓZ…Ì‘«F‹x¨p\\?µÖ²}¹ÿXøMRéªpâ²½jeK?\nR\n1z>Hñè7Â—¯“ˆ^9m,é\'å?Pš?+£²Âò‚+z½õ›†à…—ãaÔ‹Päæ)%Ó9´ò-‘Ñû#*\Z•U&9Áé+ï6Zi\'“²è_°YÂ8?NdŞÉ¤˜·Ñ¹• MbvÆ\Z?Ã`‚–e$;Vï£P|áĞ«5¥¸t²vL¯ì(\Zv8ö°Á8èFCX#Xûº¾½×ÆìzñŠÖÁ¸œ-æÄÄ™i¥a>Ê6P£¸›a1y>€HF¤l43à¹8:W­Õ™Ñi:KØÑS4?G}.º\rEÅ©ÑTÔ­Š^f³x)1\rĞC+³ p‡{ú¹,?õ?~S­%ÓìK‘V¶.QE¢ª}¼£g^ÚUFÓ–ìvrDWš(#»üªœğ>+6^E}]‚= °šÂKÜFä‡³–O!½ÙpWc+èT>Û4Û9G!ö’9æË{?gŒHğj#?~©ôò)çxæRË~jığw:‘\\™ğâ‡òØ¾ğæG.…0ßäÙÿ9_2Í&tÇ5+îáï@;ÌRvYGq’”H$†®€GSV­¸qÇaêÂL¼xK<ŸÏ\'QüN³ú¢bà?=$ì0:×…c<;ÂûË0#µê£??¼Uüo3t¡##+=*Æ“e¢ø$<£§‚‚`æ\"ô( /ÛI¯\0=êQB¸!RÛ›¦4è(õ â İ† ˆS¥=»–?‘É½]ÏeÙ\0KÈU®ä®à¾[²+Å	sCÇ£-¿ôâyÓ»\n¿ –Ï+š¹ˆú%‰¸©†›2^Z]‚¹y­I¬9Ô]Z28b*F°vte?r>Üò¾¼ÜôN%Î{QÒ\r\'HÆ‘`G§í4jM÷-UÉ{Tt?)(-h‰°+^è,ˆÏÏ[!Ó9ùÃâ†\069¦&1Myâ‹Ws?[§ì‚Z¢¾ÖÓ\"tÃÎÆ!y5¢£÷ò<¡+îì1à‘´t¬ê9À) 4ßo`TÈ\r–ƒ¡ÕÎ(jp~U\\U\r™=”¸n\0ÇsªÃUCí@Y®Ø°D`A‘íœ?2¶šX_\\“Í\'mK‰ºƒÌ?×œcÊZ2\r†£ÓÖ \0)û%1bX~.cö©?]8$244å$ƒÌGÎ \ZOK™°µg¯Xq©æÜ«W®cD²¹%>¡ÄîÀñàsÌ\'LÀ\nZERj,ZÁj.f5OÑXÔ\"\\¦O`mJù½&Ö1èãU6ªWcN?ElK †Ÿ~B?dÒœÍ(UÊó­§TbiÖ•øBl:4ffïƒT…Ş»´i¨ˆIh˜k{Bc,¸\rRZÒKµ¦n5•ş9ÃŸÜäóä¢0DnÖÆÃ¢¡ÉĞ¡-<‹utpïlãßûJÍnÜp™\\*s)e.¥Ì¥_¦äÔÔyÒÓâÓ?,©ó³Dİ¨?Ä½W¾uàX¦ë°ÜëŒ³ÖŸÃ:ó@JØç·|Jë_|,úºy&YºåyåÌ—-à•ñ¬¿¶_Ÿ”óÛPşµ©ùdy¾ZF°{]vS;²Š½FFèÉ¸b|ûJCéÓ‰£À3‡˜—@lõ\\d\'¯Ğvÿ}0)ÙhÌ)?.1öz‡?÷yº ˆº	U\'!|ˆPÃØ0¡Œ!móì?rÚ‹~C#´¥±¦ïï<}şõşk=OÊ+Ñ õ‘ĞÄƒâÉ0|]\Z\0qÃ<‘¤¤?Š•wÎh?\\œ?I¶–]nÏ8&2_¥Y½Œ°ûUw´^¢e	KIüºHÙ_¥¡*iˆ¯Ò­*¬4à$?¢£¦	}>!ƒT£Ñ?3”¨wa8.#Xád?²!9\Zır˜ü,ŠcKI_%ÒV‰qÁ2ÒWNÚ*;åËI_¾´U.Ô-–¾<i«äVa	éËH[so7¾J_Î\nÿ;K_€´ËËØì_GúÒj#f	nïN3·q»Õ¶»\"€/6ék\nOÜá\"F¾áï3ï}™øã‰<Ô‡ßSäyB¾¨À£/øÏ¸—}ìı¼t–Œ×†t8-\nv-hÆ%rDxÙÇ+ÊÔõBJ¯óışE¢u?74F“™i™Læ\\àËáAG?`$&?sEîq™ÅÊ?«‰2ÆcÖÓZi8Ë\rJµ²¤O?S‹„Ò,·”zS?\'˜n“m³NaáN½Q)áy`ÂÌxJ{èÌ/™3áÔëş\Ze\'€Š*?&¹Éh_ó]ë»p¹Ø©(À¦˜²‡‹ë³“yı8á!rÃ×1•c&Òk©*UWì-»°§É0Òw¶ï·;[ƒÚíÙËa\ZšËÍA87á¿ÛôßÂcüx,uÏWv»÷CóªG¹È‹ÂÑ¾ÚQ~,µKóÂºMïL“M‚^+Ùl%w÷ÄIOŞË9Ÿ†9ªK¾9”A´\"X¿Ë,ÈÃc:	%@9ı§ËLÀø£8­?yë¼„K·Äñ‘ö2=”•[°ÂZ^¹ÔÔL%Ç®-@Òîu†ÑÔá­‘rf\\¡Dó\ZK?JÑÉ³(í…Ãa‡˜ò ìb??‹Á-|s´\roş¢Ñfqş–˜EşøíX8Õ?²Q0ÖFc´óËÃ…ó\'‡‡ÍE+F!š?Úàò»U/LòækŸ3w…5ù(ñ°?óçñ£Ó½g/OŸí>Ş7ÁÆL`km\0§{\\*‹KozÙ”è%D97|„úQ\nDq†$çm¸\n0DÎL¿›’S\nCNÛlŒ%lŸ\n N6~[íÆ3‚Ù´9s£K2ş RLÓéˆU¸lZ#×•<;JtE“¥*HƒŠ–M$”´L:áÏ<ŒÔ?Ÿ©gÙÃ?v?nyéğ\'ï,QfÂi|<Ğé`ùã‚WFi;mçWBÿeî¹X£Â4KÄº8 ×GØlƒ =\'ö?!xgcq±Ç•m*©Rôz‚$/äªã›gI®œ¢è²¾5N7æh‰İQQWVh[Å«¤¯¦ş\r0µHšâ&~X$_ü?ÃL‡Dy>‰gñà°Õ_;Æ?p“Ï¦ë*Ãv”\0ø,Œ‰\\VB\0)ˆoš¢e A£\0=\ZŸ0¾ô’0uhü¾ âÜ\ZçF|wLtêÿe%!üyÄÎbœõõ¼hÕésKs óç\'…?)Şğ0úAFùwÂ`T!‘??wa-â_ÂI\"Jm˜lºhï˜””ÜvğÇÔØ†ï@äjj•mk&¼JÎúUZt\0?[“5Ö%W8üÁ`O’dVt®¨NµÂĞè<1<0\Zg²¦nSµ3pÙÉã¼?ó¢qU¢ÏòÇt0ê$ŠQIà¤}/jV(‹íÜó³\0Æ_ÇáYÑ³K£oVPw\ZôzÚıÌhQÅz»Éa!gÒÙâÿj³b?Í)?BÃU!pdÊ,¿.Ân„î$¹ KgX½SX¾Sc‡k‡ÉG¡r6àÀã¹\0¨ñ2à?<ÌøDdã9Óá ™™®ÊB^TU˜–»±m4¢â\"?RNlÅøûe×Ÿüq®rq†ªI~ÎÊØW)\"?À[G„ûÎ<±Õ5FC?qÈ”£j³,\0Òı\r	˜«‹j1¤m¢1,5±Í0¶†§3“Ê9D Z-àup†ŒF?FßÒ<‰’şËèÙÊ¸\ZL…U\'¹jmœ-ò÷s+é$f’ê†sfé`Iø¸òdRE…]×‹C?.eù%$ßAÀ½#ÊÇÍ²KíÂ‹qĞ}²PGÇÃepîÖq4õ†(àª¶(^õ£F³ı”dKc%ÜCqŸR?°õ¾ö<ª€ä´<¢iÒmk«/ŒB5š’9ˆl&Å…Q¦¥ÿp2¥X§õ½Ï?OvŸŸTn\nş¼J™Ç3Â2áMXœ1¶uU4??Èo¶tÔEÃ·ç¶İ(³?ö@E?L1&_v\\šµ®D<ü1ò9ë»X[3ØaæGœÓ?ã0{ÈUS\n*{3UjĞè~øbFd\\Š^Rwzƒ#™–Fu?ro@šOÔhÌ¦Í?\05‡S4?q?^Ó£F¶7u¡ÁNå„®ÎúÇÙÎ€Ìó\\@^”5œ¤…ï`™´ØA_ÌâÎ3Ó\'7B\n4Øø-Í­[±ÿMìÎ¥ÀÉ9û»’ş`óö›Œô’„ı\\?Æª„“4¢Èß”ùé¸ìJ?˜ô™?%?eš”ôfÑ‚»½ÑRÂGM*o?9œ\"Ãú8”\"“ë^ÁŠ¦ñÄüõåaœhÎX½¤Ø&çäc_‘ÆÒı¸,8à*\Z?¹²åÜ½¹?`B;ù\ZÈñZjr—Är€”8ó±Ì0)ñÀ…è¥#6éB+¸¬ì†ÛıLcÖ€õõ?Y-8Î­Vå*ôÆÄZ–†ÃÁv!=ÈÉ‡D÷SåØj\\úÍ+%gşTáÚO8’ÿªÔŞ]%Ç9¼_æÃ\Z;Ö3Kl<œ ¦pÛ&dš=ãWì\rM†‰W?é¡<}KıÅÑ@ŞiÎ–ò¡\'pİéüƒ¥ZÂÎ©&?oò¤5j‰ß²˜/ñ2‡5‹˜q!Fæ•Õ\'K/[S?Š›< nœ:¦£€?l@s¹½l&	ñ<˜R(Y7TiÌ%÷ÃšöÔÒ…nx¼?“ë“1GÌkÊ*hÚøĞto’Çw²ïu$T¹-Áî~¼m÷Q(CõcnÇ+±rÃ0>c/Èì?Ap~_épŸdOz‹‘ƒŞ;JHØnrPŸOÃøÎU0?vÓv46\na[=/æ\rDô7ƒØ’¶R­Ã^f-=,mPø1—?d˜»bU±á£`¦>e;‰>•×ŒQ­:4fwË ÙÊ²mQÓş2ÎÎ’9ş,9HÌ¸;N’‡€¡­Ç7†Ã!t$Õ|›EäèóQí$âåb‰²[?Úğö?P/IE\':°q	¢¡¨‚ˆ\"ZºóÇÁ¨ÜLõ¦i*àÕÂ‚fb«&z!zÒ»x‚ª¶HtYœ\n›)v’ÎŸ£û¸Mî0’Ÿq«£ØÍbÄ|‚×µ¨(>ø½[@®Xó]vçëD{YŠQß¶µ[ºƒá†h¼^æï~*øô…Ë³ÛMÄ– è÷#nVµµ¼Ì\"<_ÔTiİôç06 8à­ÃÒ7?K–ÿÍ=„¾âøA_øA=œg/Á®ŒˆŒÄIbÍtl•™î^©Ÿ‡û¹àd´Ğ‰Ø³U˜–U?¬(sÈ•¥v‰§Vå»V`.¦ÊBARôF(`Œ—!¸‡Í%{„‡øWm·€qçñ²‘(:Ş’p¯h\\‡0Zİ«ãJ“a84Qè/P<ÃÛñÌáÁ‹İr_÷C?w„Ğ“a;¢ÊŒ$2ÜCüÈ4\ZÎŸïÒ\'™ï5H# !tæÖG“Iü<yÌ°ë	åÇ?´<iªûÑã&µeNÏ¥ÜHI¯Ü‘ÎÄ³€60“¤?íÉ&t,»SÎq;\n1bé9[¢&™ Ã¤úS¸Ú|ÜwÉÍã¾¦ğ$-<Ñ|’¯è#Ã;õêÔC?ÛÕ{fu\\orÖo÷V9œ\r]ôÌ5Aà;&ê &9\"eâ‘üC	ö{	lM…£1àdÛƒ3’É?¥˜cX¤=ÂUÇö¦:æ¦L–sÿN;óEPpÿ£	nH=²şö@j°€çLØBb8kÑ6±·¯@Z`ş67‚\"sXEFR1Ì|2â“Š¹€?ŒlVÑ‘­XxQAGò´b±©ŠpøZK\'–šjñWTã4œö“°#}àáuÄß„ÑãÊK:€y’”\';sá”’¤Sà?²ôÔR\'¡ó±)5¾…ĞÖ¥XF0^†R,\"¾”/á³RŠÍ¥„E¯ÆKXŸÀ,\0b©?Oa6¿(‰Ùü‚4fs\"³<Ö…‰|±—]záµçÖş»\\äo´ocW?Bü‹C¤ø¾,ZÁóÃõóÅ«\0\ny8«\\ø­–çc¤q+KZ}4|ÑÆ’r?œ\'Ùú4JÚç?¢Cˆ7+ÙlöÃ?tÁÌ‰?ã?v°ø©¯?…Ù^£î\\sbhx%N…²M²÷_#ëÊ‚\0×„SYlCW(6ÅË¢+ÛŠælu\ZßUVöÛbäbª”…ÃaÙ4†S_CC“Ò¢ô¯‚¡İºµ¢á?=Ô¬\"ëBG%ıIø>J¦ó±?l”h.ø‚‰Y$´FBÃ\'Äõó&ùÃ?S¦ØŞtİ^²µ~êàÁòÌ¬ u†}]¥ÓàöOD>¸S\'pœÉ°`šúH™C<„“¥óÎ ¤+ygæD,Ğ™W|`cX[í?Y«uh	“IßqÀëMş`GÉ)4öæ©3öwLº.&Å™@®ˆ˜L=q%aNıîô±Íş¸İÓPÍ8pj[sÍÂøƒ¶ê@k\ZŸ°ÜÌi¹îù3oJÕ\"ı¶İ£l°îÎqÄğ;<\"?‚6~ÄÜ§FÍ¡yË(ì|°MÒéˆSö¢cF&\nÏùöéü©[\"t§½ÑT?B/6îİ»×à{MÃüíÂK4‘õ®m…˜÷r¦?šzR¹ïúüı´Ï8^7!5dOœÛ,½ r?´?Í!EºN;ô:“œ+µM‘­×6½?]GB%M3º>s¢bºÈĞ¡Ù[R»ˆŸ—7Ò-i©Ñ2Mk±¬EêB\\°L‹ğò%«e±0œ<^×\'¡	¿ğÍ\"VË	³g˜3nÊŠÎaH_é¤.£³Ò\r²6è<±:\Z\'ëà4tøÒñ\"¬|„¦qğŸ²÷ z¥Øòmuœˆ*z¾xcûo,q$Ò	IPãiwõXÀ¼?Òï?Åkç\"ì6Á¿:zÊÂèE¢ñd0]tß\'6gÀ§Ñ{	I@	à®kõç•z“ñ6ìeMÏŠ£±Dé[pş\\„C¢ ‰®’v¬–**Jœ:?øÂE=4ğÕöˆ¸Aá#(2‚¦X-¼%éR`š?£IÉÉy2Iç!ĞÔ°,1…ûy©…a²cr,ÿÙÌøyÍ??ı¾h§ÂËóh?Õë\0CH}¥=}hƒœ“ô(Ùmb¼ìïÁ&…?…öè6¿¼¯ÚÏƒnÈ®Ô@x(f7G] kû_İË~üq\0óo¾ 9ù¯ ‘uû¯\Z	Úo»“ıAyµAâ’ÈŠ¥ÇÁÀµw,î‡)ùjìÔôÒš,Â\r-ã²é<æò+qFeÿ™N§¹|‡ÜkAÖx¶,1ĞVgïå\0¸šø_Ën^Sé+V#?’¼\\ÀÛ•W9fWñ+öá‡î¨ŞãÑr?¬\rŠ£ÃÂ/€îßu™Û÷¥ë;6®î2\0h½Í/ûÛrõÃŒ,Ğ< Ş&_\n\nå;4 ¬°y„»e¹má¯†g|²\\gœºõ-W;>;I£ø?S÷J­#ªÖ?^?|öÒşr–yHG.0™KU=.¬ª1“Z¶>YËØêE\\®2•İ¸“¯}pr•ú\'\'y\0HP–ğjìvÁ¬„qX¸_RğÌ_­q—ıj¨ö‚î°×¯¾9É§0-Âyí%£¦:Œ{íÓ•ÿø÷øœõ[í»í[ëgıÓ‹îhÜî}ş66:?;·n©ÿ€™¾¹Ñ¡¿·nóoøllvno*õİæ­|ét6ñÑÍ;wşCu>WŠ?.€ùRÿÑå-PY®¾ÿ-ºó[Ö×ğdÔğo©×x‹(—·\'³q¨:[êáúë¦z;ö,ÖH¨vÌÁÄ$©XáZi$¯ƒs¿pK­Ô_îïµ^ïî#?*¢`Üj?ûä|TûExâš\\ÄÆA÷õîKÀL·o©ó,o­¯_\\\\´/‚1´<µ¡¿ëŠTä5³»ù\'Ò0Nâ]ßk¨¿&è\0÷wl¨íC¼P\rUı->ıË{üwé$7Œ•ÚŞ,n¹HèSÓıÍ\riJú¹’¨öœ?G3…?SÃÚ™ÌVju÷\'úr§}J68esœ\nŞÉAÚĞ‹ØŞ/£ck\rı\nœ)É?bh¬¬ÔµÔé£å÷¤·Íò72µ­Ï÷a€È4§ÆY—ò‘¡rƒÔ|Ï‚é$şçx4?“ö\n×X[SşŠšb¿P9°ÕIëøğ„¾ı¥Q˜X@6]¿.UÚRå/˜{µ=?a?…q{0i˜–^bÌ2&c’&…ÇjªQÒ?3¶Ïunû9i\\2È.p¤ú!\'[ÖOz¾‚729?Æ3sO7¯&ğÊPÁ€ÚL¤‡÷\nä¡Üoš›R\rĞÔİä=îâi‰“gD¼íQ‹?	%¸U,aŠ(r¥Öa#¸bıXDú±?†hqåmtaœm‰og&Å½ñ]\rĞ?rÕŒ;¼$œ#î>‹#ªÀÌœ…YûóïCgösÛ0v#š°§ºº>î²„1‹¨TñG–Às|eÖI8y»1ˆãéHİÏğ×_†I2]<Ú2¶¯{ì»Çœúœ;?·GÒO¶4¡Ê!Ys!ÉaÙ¾…ßàMîgßû­|ñ­uC¢=ğ~’8Kı‡iÖG{?Ü# `ş3ºä¢ªöá*?óÕŠ¶tz<Í`MV`œ.­©×rp.G#¦ÙOGéJÁAy…ÄˆuÊÅfj\r­xTc…M\Z”PMU×w¶k\r.³½òi¾îßY›Îí^iï@ÚÏõ×ÎéÇİchuélkìQ??Ëê¦®êÊé¢NQ?j>c?}Qùõ„G¯É~Ç¡”‘Äeà.¢\"/uDT¹×ÃgTxçG»†h[“mz?8Û\"£ÄX‹|y¨³nr¶^Ä9lCÌ®…ÚÉƒàèyıõ)ç3º)ÛÔ?Œ¥ØÈkáRg!-.Ò–îÇ™BC1VAÓ–pö‚;W~Ì¶\0\rÆpQgM;İ:…ÌYàÿ5|[Ã8Õ€“”­;^ãÖÖp*·aAj°G$€½aƒ¡õ\Zf}TÌÖíHüQ7¢õuêBÃğZÇ?š\'åôõ“Ã“ıFCíì¨ç¯>…?R«\rÆ ?gƒ:œµpìçêÛ%ÂI\'°êÏñ*˜`çĞÏFMÇ¶wÖzû	ãâÄ<ÀãÂ\ZÖg;ğ}v?ß\'Éé³Rûì”»Är—÷ó£‘¼>P\nB`ô¬kY]¦ç•F78£\nºVÃYj=à?AïŞ@÷h>>İİûÛ¶b°°,¼½qc›Àï¼fãÓh)‰ó[Á`ÔŞ$j?¡³k5Y%ƒEêt@šêšA!MF‹æ‚÷Ø™Çn E:íp×\nqëNÏÑå–¾aÈÎC¬Wï‹¨Äªëã^É£{ø²€Ñ×t¤£ÀÜc;XJ{ŸÂÑãXŸ€\'´P6©Uµî (à›\Zá1è/Ç\\Â1?áÉ6???œF¿ÎAT¼ÁqiMZX²†Ï\0éÔ$7vSM’9†²n\Z¿ÖiÁ¯â?æ„½×èèñÊIŒ :¶¢-¯Â7õb2Ù:ï?ÊAÒTüƒÓ?4\Z\rÜşXÙ®£T\\ó\\+XÊìIA¸Ù‰É¯	§‡Æ:/?9ex×ÿÈ yÄK\0Ë×æó£+ùgÄ ŠHç¨“ÿ?n\0Q×U¾rÒYy0ê€B¤\0î»óFO\0)XÜıÄ‚ğç¾3Ûô€Ê1&ÀÊ#ƒ7Ş Æpğ£E9?-\rÓn\Z@c½`?p˜†ËÖÖ‰v¥.b›ÚÊJqàC¡ånkŞ®<Àş,¯\\W:qÂ?9|Î±Åàg5ç„ÒVx^`a:½º°Ò„Êæœ#÷AÇ°õ€aQ¬~³Ùõ(í K†VFËâ§\"!6!Û°zX†4çÇ¡Ëm¯äØ\"Z?„Ç¡Ğj”á€úÏ\n]†~Ät|Í/YÖù5N.ÄãĞl&~Àä½5-Ñ£YŒ¢v}³sënS	²rêTOš¼\'p?^î_f“ —=\n² Nd;çÌO‡ÀËŠ?ığï¢ÿ7ûı?f8!ÓĞ8ûÜmÌ×ÿ«;ßİ¾“×ÿß¹³ñUÿÿ[|¾}şl÷oû—[;?©× Ê%©ºw{ıŞİõç\'À½¾‡î?m¯|û’=aRò²¨gÕuørÖoÃ¿\r´íâwdù×$T??+I&Qõu4Äv¤\r³\'­¬ì½xöpıÑNo¸²ò­Ø¶f4Bá³&ax_v’KgiF¹Â@^Ä°ÄRJ+®|‹£p\n¶ÕÊîÑÎÓÃ‡t„AÙH;ÅğÅ<ØxáÈï‰Âhå[`ˆÃËñ0êE½¢i‚ï(fJkÇ†NÃY{åİîŸ>İ‘7Ü2*ïûI|],^‘ñ§;èÁ:@Aû\n)ÏqXPE¢ó7|{n8Ña¡§xÃ‘?qBëÈºÑ^Ù;xºûøx§õâRµÿ Ü–«›e³,§q?Xùö}€)\'İä’D\nh—\n‘C:A\'øÌú1HìÃäÖŞtMµ=Ùıûş)¼ıáå3çyiÙŸè»e?œÀhœGûû\'?¾Ü×wLbM\rkô±«Rï¼?ã\'Ì$zÜÀLêyØ”Œ!W¾}ª¿k[gv÷Æ¸ş?¯à¨vx¯cåq|F_A•~0JÀoÇá™şeüZİ¾€âˆc+?üÀ+ßV¿«n¦?lìô>Ö‰ÙÕYtõi¤¦B ±?G´\rV¾å¦”»Aµ_8k“TšŒBVÊ Ÿ\r°Ãë­áp>ß{úêÑş£Ã#Ø‡ª¿õ3¶£ø«\07?°qû†påîCÇ›z*ü˜}\0{U›Š°.‹Bg6ô?nxå[²lG?XŸ¦“ujÓùàØLRjÃo[OÛwŠ4Kxos)Hû@B	ÑÕ*¢f8ˆX•¬+ÅuÄ@±Ã¾õãıœ	‡C[ùÖG¯x¨®ãõ-À´Øÿ:a\"? €œNİ×Œ}ÛnV¨EšŞJ~~ßÃ%Ú–õÜ6‹¹mWrÛ]Æ§:?%O´ly›µÀ \'¼XYå\\ühÕ \\D;<Ù\0m¸.‡Ê‡Ü>?¨+Â–?êÂ=v±\rÉÖ\'q‹.hó±––móáás·½n¯àæ,¶G{È\0Õ¨Á\Z„t‰DÑn^YùûşÑñá‹ç;Èğm\0ÉİÛùÏº¦»\rõŸuç6`1Ÿÿ\rßïáD(?••—G/í>;†çĞ«SıKœìŸØ++îû@ ¼•ñôÄgı,A$Åß7½x-4†òÍìë‹°+_W¼†áRHm®ÓG‰?Bİ$”@Ìg\r¨ÆÊ\nLü–ŞuÿYwFcami¸=)¸RãI2?i¶híÄ9°¥L8…û¼¨¸ó–‚y\\›Ó‰M¯›Uİ€¥Pœ%Û2ëWœı¼Æ¦d³\nŠ~QÅÙ#[î)?d^UÀò6Ø–·ãŠĞœwU“$ÛpK¶XÙñãÊ’»e¶oÙüĞó9}??¾åìûòğ›9?ŞxU#\ZTéÅÃ¿G”tßÒÍ—ómS	Á|C¯AûƒdSù™¦ôígTóà;ù?zzXş\nD^¾á}eæ|O?ï£®óch¡âO\r‰3öê0MRƒg ôÎuÇŞñÛHw…-øó‡Ó´¥`*pp¦Úç¦cç¦[ç¶Sç¦KúÛYû¼†3½K¨šáüg?y=D½aÄ[+µ~8TkÔ‡µ\"+Ê®ıõø7·ÿ»}ûÎ&Ëÿ›776îlÜ\"û¿ïn•ÿ‹Úÿ­™íÚÛRG~È’Çê=8<hx‘„b9ŠÁ†PĞfd|õR«p¬ĞdŠÉ\0í¬¿½k\nÿ+Xjp?tRG\ní©–LŞişõ0î‡cââ\'çÆYS?\'ÉtÜVê\0¸íQB¦:Üc¶äz},	Ktku\rAx&$Mf½v£IÑ?4\nø·=?¶ã0[×#]Ç‰[Ç¡ €ç/Nö·¨•?ÍV7‚Îï>{ùt_Ãtrüã©~°³E\ZhĞC9èÑåìA@7¡ó:sª?ŠK–“b‰Ô”üCä;$ObŒ+RHX²r>†F[¤‰îœµ‘CË7õZÑ£÷S‚h˜%[Ğx—¤9G‡º†É¥ÊÏĞòH[\\ŞZß¸» N>Ü¢É;}´ÿğÕc5	¬TD~{ô¸‰õA\ZùûŞ?,8¤¢IÇ!ù¶ù‰C«4Iè Ó5èÙ[XĞ½Ç‡\nvLB?Àı·¢|3•%-PÒ0{‹æ&åV)n]4uÇgëkŠøå-˜êš;é?×›á¦z+U\0OC‡¬Uî\nj}Õ}N&°Ş3¤şoô3EUÚÄ|\r0¾†«YYµšè¡ïØšüxüè”VC8}µ£V7Û?ÕíB¤S˜…îtpªıúù~ÂSÅ/ÑîÅ/¶M×${FdñN”>ñÓ1LLˆ`q`\"\"\Z=N“\0¯BVèaŸSz_{Šz¥$>gÕÃ3¬¯Or}^ãßr…[¸?Ò†`·üîMB<O)‚ïa®8\nÂLMo©U¹ü¨¯QÃ­ğ¼õ€7åéœ…?:wŠKÃiz.?êÛdéL=ßç†\0î\r#À3§˜?Ñô%9?nÁÜMkˆßì¨\Z¤Ô0¢<Ô/Zøg *\Z\'˜¹ØÿáähÿÙşÓİ5ÙRnu¥V[•kŸ°O¾ª“d¸­8Ï ¹›²Möë©°3Wn;Ë­yQÕÔJédã³ğ2Êê÷îéK<Älìtø1Ë‹W\'t‡HOHÆZL£ÚˆPM!d’?ü×«İ§‡\'?¶ÉX~ iö	©o:-ÊTë¤×ã?ØÀdë£Hq°öœ¬¸I5*éWÄ²*ú%Ì7†`âğ&=¬ËNÛõTg04àuÊ‰ºäIZs”è1cn¢!©Æ(A™«Úl´•	›«Cúø?I‰ÔöÃôPÑ²{»‹jùRïV—/I¥÷îMér7½¹QlNßˆÒU§\\}Ûù?;¼æ‚X|«[ÒáÏzµ»Ä@>ïı.¯\Z¡!é6¡#Å™d-JvLäú€×‚@¬ä?›FwJUË/TŠ:c\Z—‡Ôşv¡a–ÓÑÙD!•å÷bğùşõ/ù°÷	zÎc²„D]„tw FX1›2-d\n%ˆ·5/±T%6îèÅk4a£|í>…¹úiãÍvå2„‹ê<:|¶ÿœÈuLIø1Ñu¹aùióöm€¤¹ËpQIA¾éé\\ƒìåÏ)`Òf9?}P¨íQK¢ß„ÿˆ£M)2‘ :ŒwÜ\'àP4ô¦Ê1¼šš!Ê¨Gld©û°\'€’ãàI¢Åàÿ°Q×7øSåı½iP;¥£¥ú’@:¼„n©:÷ÿkĞè#è\'@ˆ“°\0›ôı]lÍ¯.\nÛÒdç[`õ #°i.Q¾…#à\'ë×äDtøÜ%!ßD—zK¤ÑÂß\r=+T£\rO`bh;?~ˆ*\n,.æğPüš·›pÖ˜ÉÕ¬A›ÿ6,ï€f‘!T!=50j _¤?ÏÙí4ÿ°?eÚ%5 >0ö>m6# ¦	\"fÌt˜ãï4‹DG›ì‹Ì	—™ôç„¨ã)™„4\\£ôr»ğmËt?™W\0í\Z©?$¦¨E;êæ6ÎÁ·&«–rŞâùäĞørä5”S*zšÑÿzGıuïøôèñÃmÜ¼Ç9?\r)™ª^ÒÖŒ‰µ3T½hš|?ÀUZ©™\ZòÜì2ùİT\'G¯Ì¡‚>\0ã\0dBê@Û©MVÙäğéœ÷›\\7’bOé(ö¤Ìé-»pï1P­p m–LèSD:ŠTâ´ºÊÛ\n>«FŸ’SWÓ³@ñ`NMå$ögïƒ³£\\Êe¦Ì!PDõc×5®nœõ÷8±âF³dÏ­Ul#4ş`kú8k4ç,)5¤]5?,<ñ4&ID¯R<š„Bœ²±ÔFœÇ]R•!ÖÆ® Qr´ÊÎ®P«\ZWÃw÷ˆ¢v,Ëƒñ7_*#b…¦ĞGû»\'/°Ÿ4FõşÏ©ª3‹m8Ø÷€™›B=ò´©Y \'Õ\'%×¼º?lÁ0ŒõƒtÈ0Ç;@˜fÕ|~ŠÍ?¡&Lü|P9~Û\n)4_¤5;“wáÄÌ\rfïÅ³&LBœFg$[‘ÜŞĞ“ÆÄ	>¶†?Ìµ??ˆ¦3K;é=ò<X¸.ùV—¼„ïRÒ°ITßr>\" ‚?½E£ÆøEëšzşpKíŠº\ni/à@?ˆİã²”D•…‹Kqö\0QF×V)”$æ(H¥&Ê?†©ŞjQ°DLI‰Åï²’\r÷ªèFZÇä;sÌbaª£†f?çLğŒŞå°ùÂi#ÃbË*ô/ßlWşa^f*¨Ä·á°¼›‹z îßW·–èÆür¦/Rì[Úøß²j¦ ËÉw”Ws„š=?&ï¢]tœÑ5›·3«¹>\r˜†›ÁG”x¦¥\Z°(²X%øò”ñåE@‰·¶J›X•ƒ´jPãŸ§¸i0¸t…ş\râ¹p	Dq”—àMS¢\Zµø¤+s-·I4v\r·sÒ¯›J$Ê¢·Àp\"İ–šrÁ2ö‚âºöÜú©BØ¾È‡+•M\'=ûa‰€èœkò»Îú~Ç¥å_*èP¸õ«Ãç8Gm¥dEåºA47°É!Šõ¤`õMæW*§ ßÃéÓÛÕåÏybß\\9F8RÁ\0ø¨+\n æuŞ¥f?€j%eXs8åEÑ3Òÿó‹ ‚ªşÍÅ7|?Ì+7<»ph‡Îçñì1Ê	yy|b–Ã\"\rÁD°ÚzvÑÕë”­\ró©D©\0“FÛâÉşî£ı£ÓkÌãÄ}Êâ´¢‰	k¬q\'\ZzÂü¤t°™oÍQá—ˆ«\0·?œ>Ûıa¹^Y9I	€úŸ§\rôd§n?ÑTËí›\0F=Hƒï8³óiê•‘Z}B<ğ»Ó½Ú,«‘9Õ­ËJ?®ÿÙ‡CKSègSåGSôû«#÷]¬iØtU( n?v\"T9³š VMŠ–¡ë;U€Ò\'\ZS‘òÎÌ&Á§Í9¢Îå\'hé­I©A¦ ´Ä†Â$ÖXgú–E?´Ó:V9òêwtZpÊ Ö#ş>!ßê<¤´«-IHT.S—8…t_N¥İ:Ãn	`µĞ’ñ”;zšlQ\'à˜{MfEÖ\"ÆAd@Xby?¥ïÇø\n1?¦cN:F¸<¥P‹âm½$?t€c­°µêl#Š4¸8Åë+-VïB·Z•lÿ¹½Ù×rµ¿QÀ[X)?§XÛb?œëÓn?é†%?¢çb1²êÉØ¦È«ç{şâõs§?7GÀ¸_ï^Ÿ\rµ®£?0\'PzºiH%XÌîMÔäÊÊs¿0.ì),}/r¦Áö0…Èd8º­S4êPß«UWß¥¶w¬¢mJ«?<8ahÿÜ7Ç´o2ã®º]åÜ¹…ca2Q°tL.QÌaÔø¨‡¶ñX>>Úıñxo÷éş–£Ì›3šjB6]À‰ï4«£«Áiv*À¯ê¢?îu÷&Naú\rh¼ı®´Õ?¯şŞ¨®»÷ìÇ¿9Uñç¼†öş–o§X\\48NA½ëÄRná¥®¬=…I´ ûziq£ÕÙ»k¾ƒ›{³??÷ÉşÇåûŒøBCÈ¼ËŒ51oÃä‘ŒGNùRêKÂd¬M—İK¨LcLRJK°r6f*6¶ƒÚ9®P·ò:qUÑÃ9´pÙ›¦j•?½+23©¯š~ê¼?ÇÛ\\Fß!Km8¥än©Xh“ÍÕ\ry]©—õE=x n5Ô5Õ¹ŠË×Ù¨ªc»™¯²YRåsê?œË>–X—¹m(»ş£´°\'J/…Ş-{(?söÜæÖ „ª|É;\0;›?	›2Šîkşµ@ñ?ÈXJ4ÿù“SB ­®™4d¶Œ¾?*ì2?76“QÒ@™¶¯YĞõa[\rÑˆºÓ@\nKV•[yq9YY.ªòœ‚œ§?4åÎ1¬¨h?/Z€µ<3ZT^–ÉãLx=m«\\áuX˜UAÀ\Z\rD™öÁi·cÍºØÂ˜v´ï?q²?j\ZÅï€U:iëL\nX%bB4›Vâl˜t)77gº&?#Ôx\ZÄáu˜E4#5Şrb8»NŠËGÑ`Rèn×™N»¢?\\‘õëõ·İ$Aküëˆß®ë´o¥†~„˜e;C¶—¬·ĞkÖ/…\nAx??·†äçøahü»RıCÕ¡õ%›tğÎãz\ZBÓ,lq§šÌG¾xñt÷9œ?ô)Äs4ãÚnÈ.~™8fS^¤ÖtYìæ£ö‡x¡¤6)?×F±*À< .€~¦tøİŞh¿k†~[µN>Ó^kùª»®[”±)gĞš™NÅYşµæ·¼öeq÷/ÇAşğ¨¤ûVß3é?ÇwûX)gP‹<µ«f¤O»doÀùPÔ \nÑ\0šY½œz»†¥y‰eF‚uîÚPu§¸¥xFFòyÈœ±säú¸	¨Bnà!bCÌMiMpà³0ûêÿiåƒ\ZÍœ18ÄŞs4Ï¡Úÿ(õÊv>|şòÕÉéÃW§Ç‡ÿÏ¾R·:÷îP?¢ó„cN*Ü·½ˆÒà¢nĞ¿N”’ûòšàá=Œ£,\n†øP&¥ÕjéDpòz:¬Ó\r1’nwb‡3ŠÕ^16Š+€R3NÕE_\nØQ*¼ô\0ŞÈ>i(±é…ßD@a ¯)1OÈ~ñh…?kÑÖiq?…apFÛ*(!\ZŠ}+¤qAÛĞ‹P¬ãÑ¥g\"Ñ\npÛñú¶¹¤£ĞK&Ü¥İ5J&^œHØÊ!¹’Ûd¬„ğ€w–	eh´c&=‚»UŒF†ãáäR^†|?ÜEÁ­œ—_Aqøû%ğš\"æ wm×ğ´)ALÙV•RÇÀ¾HÏecÉrÃ`ND2Ñ	‡{Ó	áf4-\'+œ8¼ÌNÙh£;ƒG×şIáÑ)Ãh4ÙJW¯’$üƒ‚dº®Óê˜Óe[oJºc¢P8Ctè¥\'	yt|T™ôÕ ?IˆQ¬ÍUd¤C@=ÅRF…a¹gMÎİJº˜H\nC<cÊ¨ŒV“¬pq„zšOèv€ÚPä‹Î)›ÊÂ¸ÿâ@úÏ&Ç´Kã¾‰Şˆ‹¯{Í³„^n}¶A–ÔT À#±İÓşÑÑş‡\'õ†Ä?D¬tk6‹(.z/Sj\"Ğ(ÃÃ&à”Ëo^a€”XŞs`[8 ÙBèû/« 4ıê\"ıÂ«‘”ºpUÜ§´Ë(”$9ÖëËD{ô¡ó8Çœã÷<¹ İ<Â”Ùu54A›í\'\\¨©ÇÆR.v‘Å\"İ}L„¥C9›‘Îß…	¦9©áª˜ñï)\nÉGV²|GÈzmŞa¤õ HèqÄ//TÕ©Ï»Ÿƒ\náMòiœŸş4dRÍ’í9y”Ó\\¿¢!U?…”¤ë`÷éñ¾áyï‡ºµDŒÂqÑ92BãmÑÁ~/8¢9•Å$É¬0ÌÎ¡KœÄ9Ó;„QCXœ³ôD{‹Fk*ÂAd’ªØ{âŸÓO§i´àF¬ Ğ.AJX¨‰Ä¤Ñæ¬¡ÎäZd•NG¹]„¯ùW`ñÉw&Æ 4¹+õ<ÉB c‘	Ç?N§]ô’Brˆ\rf“HRŠgÛ”w:/kªw{\Z†f²¼ğÈ²[(Sl˜ófÅ´b˜BŠ\ZšPê×ø}#í¦ z$ÜIHYPûpVD\ZE?Û{“¹?;H5Ô]ùpÙ&MÙÈo{`DBÚªä‡òPz–˜\0ÓDë#\04^À`ğYZ9Z×1»¦¸§ß‡Ììê)Z\Zçhƒç×?\0à¤/Işˆïø“á‘öŸËeøñş½Ú¾·¯Vüÿ´ö¦ÕÚ‚—$älµZğ\0Ÿ¯şieÅaêV?Lèùã–?£ˆ	;…JB’ñŸ˜«‘À5n3Ï@,2¤©<?Ûrâ%4±ì4sü`ƒàq‘º´r?_FıÏŸPä¦0É	;O<¦,?.puCzÙT\"U@ñlé×ô’*ê{å?É¥J¢ujaÉbCıú«Ò¿;\rîF?FŠDy621AªÉ´á4›Â¶]\0G6Ã8Hhœµ±~?K÷£>?0ZÜšÆZ_Ñ&Ö[#Ûwîºô fV£µ±Í…>ğVCëá| Y“Â7vh­ğİü‡4/2ßÎÍ$¸¿2ì\'íF:s¨r9—>n]³\0Úî~Ÿò’ì?{yò£Èö¯w??;6…^=çƒ€ÓoŒc‰Î“ï Oá¥1g°úÊ7­l¨ÎåÁÁv¡èFYQiı?‹›™ŞÄß¤Ò! ¿µóXkÇ¾í–Ìa3(Ép·«XmBS$?8œ¥Ë{¿‹ÆL:³O%qcŠOÑ}3né \nè—œËT0\"^6 €iL. PÄH©®Ã¶Ú}ù2–Inú€Öu“ÔR–>2,Ëø|!QÊÙI„»%…\r2g~WÒ\n02\'N\0FqQVq‡zåÙS\Z\'Ì>t!e1OĞ¸æ3õS3?Wwp³D	£1öeW§‰Kl‰,±ôA¤…Õ?*b[\"1Â‡JTfSœë‹qÃúıgï+fÃ„4P˜’WY6ĞâŒaÁ¬•å˜2|œ\\O5óèÊµ¹Ù¯4ÒãÄôÀ?2®ù(Y÷¯” ˜ÇQ×fÔ¶½”¬öybÅÙ@3•éH’_ ×Çb-\"Ó$d5áû°…¸Ûy7ã°´™ñ€î”âÌ\n°„İôPÔ‹ß4¥qŞÕqà?ÊÓjm6l¥ÖÎ¢ZÚp²kĞ(îÏq˜¦ÎËâSä ynŠ[?ß“\"¨?p…ÉVMÎõÎÑ!n_¢A±ÀĞ7©V{~°ˆ±­á¨3±oØØöëä\\«ªÎ‡íÆî?…ÙyÒ·§¨Úİ™§‹%ı)œˆŒù\'låYÜ;Í’SÍ\n¸?&ÒJ‹\rJ`‡\\–V?OÙ‘š—¢FPö„?$¥ŠÓ8G4@=¨pÉìÀı?Æ’;„¼	?é*¯»›?ÏFWÎwû€XÔ”2$Ëñ–y</éyz±Â…©Ã‰³EÜE³ë¨&TK¼?>úÒJÆ&¨ÅÃ-µûi-ßğ8ÈŞO¼ë€mŒeGÈe.Ò\"*Ñ7:m<í¸†2ØÌÅçh0‚ğ{V?ØE‹CÑ¡·øbŞŸè·ƒŠ^>FD<ÑJri\\÷\'}h÷òËI8¦ÄT¤î°Áó?R—¸mÑÆhitdŞk-<Şy\nAä*¬`ŠP\0MÇ˜Ö_ÏCo˜¤,PÈZò2Ew¥Ow–>Áh»„\"h¼?cò5å¬ŸqƒIöı?2ù1)è$*¡VM2ÚO)Tª‘?hÅ&<\rF¢š‚ı‡“‰cÎMÀÅ…++´&¬\'ƒ-^×¼¸&Àƒ L\0«Â4¨Pæ\\„×1lâ»Ù«„ƒ‰ú\".qØLÃQÒb\\?R)\0×‰iŸM$RNŒ`\0œ¹€R‹óÏ+Ñep¾ˆµ“ë ™š7^\0?~‘`ƒ#­.ÖôÑîh?HH‡ûÇ¢töÂ„Ğ9ò÷}Ÿ\0\0|_~ã±ÖĞ”PÇÑ\0¹°õ€î¦O?ê‡\rºÜ@\rÍ?üõå‹OO_î=Û}¾ÿü¤IÑø•¾P÷.ôµúByÖ“\0\nìÿèmN\n5>‚¾ØÁ2¨–/ˆäšêº7;Êùå•*ò;E¾Â«‘çwò¼WºH{ÍıîùÇâv×äR(Ÿ½r@x.\ZßQÎ/Óª¤Ù»ğ¢V‡š%‹Õ´„¡BÎ”ö;!¿+E	?³‘à0@¶\ZjT!ú<¨ü¡ä±OB–¨\Zí5\"_/‹ny‰[DŒ¹JTeW‰ÎM¢vwåËÁçìSîé*0×ÿ>0ßË5ı0w+øâÕÉ•®IÂYt/èÎt	äû¯.º”´Ríä#æ6R\ZÃĞI=lê—œiıÌj| ©¨vr$„.;Ã¹V‹¬s³R\\4¼cC]“$s<ÍğDt‡Ïvïk$—_ÍJ,·b:fÎ—˜×Ès{½íF—4ï`ç\ZuÕ#û$Î—ÏbÅå$b†TM\r£»ÔÕ¤Í(Å7s~sËŞLº3pMùãün&Ax{W´Ÿz‡Äã£k˜ğÉ4^tiä«iDz•«cÒ!èò¯‚xŠ—ºIŠù\'L¼!ê…\\Ö?F¯QQÈ},†â£¤?æHî„òBw-¤*o„ˆ1¦û I’ŒDF]a^¸ú¸ë ŠşT¸ºâuĞou4ïHüv—AÛÊUtÕuP?Ám‚?#Dœ??3©5Ğ÷ø—\0$ÖWµëŞmW03`îs>¡á¸/§t?ââÛf¥6ıê ê›M5´v\'Wƒ©J™êıvO_ab®/Kª•æVQ²€QÈ9~ÏÓ•h.A©W)ó\rˆI³KFşÖPç®>aNû‹07æ§ÉVaİT«r‘?-Ã«MÂQ‘Iˆ§ä·ò­mí?¿U±ùMù’mo_j¾iá÷•?®‚Hp:áL–~kíP¹nèS¾•nW;T\"Ei•Bh„63h¡ë(‚X\"Ú 9v†ı1ê ?Æy¶ÂË°7ÍŒbˆfšúçZÍ…Lƒ8ÂYK—ÒÉ8£¹ªbÆµŒÅÈ(ãIô¸AzËĞğ¼¤ÚÆ´º´J‡?ä§ètô¡\'€~IÍN®­FÃ\\¿.ƒn,&)Hl;!Î¯PF|wÊH²_­€<w\nøÔV?3e´xˆ\"g”¬#qı†¹öœøïĞş‹´1?şû­ï6ïäó¿m|·±ù5şûoñY_»Âg?³dr„·ºO Ë#Ù•ŒÓ—şpñ¥?\\ü¯Éy¬öA,‡ó„Ãøå/ÃiQ! •\\ñ+õı#¢…›(àöÙ(ÈÎ½\'‹#xÇ¨\'{vüúğùÍMÒ(sÖ\'XìD\'j2¡ŒW]ZE–ø¢ÔxËíS0–”—‚\ZØóWÏö^<}qt¬îÚ¾äÓ§­ˆ&Pg½¥aœèH€jÍÄ¿íNˆšéô¬\\?UÀX\0¥÷ùæ~œq Ü¾¾È?Ó?^/ùÏL×å)FV\"ês½é0$^¿àºÃrO ¦ui”»lÎš[»†hh«”¦P]ˆœ?é3&İ&1ÂÔ¡÷W·íåƒòV›3€x[@Ÿ‰uı¥êuv6œ?Ïi£xëL	AA6\"lì\rÃfõ(çºKµ\'Sß]õƒÏOöv÷ì›}§|©øë“äİtlšsàœ¼ŞßNûÇBƒ=,üíS,’\"?Ëô?ìÁ-`Buƒ*.*Ô×Kñ ºav?’Ğ¾§‡Ï÷?_îîíiÜhw:T0Ùå(AYû@·m«sÂ\n\"Ûs€Ş6Ò 2Ú°ï ?(`‹?¼1¥â‘õ&Q=e¬jFo6LL<ÅIòç&ß“ã“ãı—»GQ­ªUİ™Gû»¯œâb½Ü=yB\'~±¥Qz8GëxB°6åz’¡h8áøvœ)\"Ìèb4‰ÑÙ”ldÔÙ{şÙ>gMUÊÊ#4dàœÀ\ZyW¥(‹J ?æÇÉ6Ğ]ïœ¾Š×.%5©c6	±–:,ıYßN&İßR¶Œ÷Ñ/25‚‡ò3`æ¬05«”-…¥ƒCƒk²L*?¡U}65Po\rD^W·L5S?Ô¥æ\0‘¦²c¼TßĞS[QŞ=Ûı¡4»Èø?õnã{ø»¶Èás[ä¾W¤xTÃZ«´J[ïÂ,œœr‚ÈYmMî6==€­\\«!Qæß“dX«¡$yŠğN?0œÅ¡ì!ÈzûoºÑÙí’ÇéÛ(-yŒ*‰ğÖëjèuˆƒ\\°N³Ú\Z¡4z„$p§Y1ÅÇŒ“¡A‘R€ŠDÿÊƒ?„#ø‚KàW•ìJÜÎ’“0(¼DÎ½²ú(bò´±¨Uj:qØÌ\'\Z-ÕB‘Ü\"ŒÜş*ŸÍÎ±»ãâuJq5Æ®êÃşÂ\'½áé«lp’¼Š£=+fJ&@/B1-?ªİfÄÄˆ9ë\r_Q6´V‹Uxµ#vyJuÎ„h<ò?Ê«“ƒÖ]Åœ†övíÔ{\rµqïŞw-øç®:ÆêYÔ›$ŒEÓ¦:Œ{º’Îº@b\"ğ(@æÓ?„ÌtU¸</?ÑÔ¸DMÂ~„=èNY7±f²ô²ëåOFSTõèğxïéîá³ı#õâ@í>}Š¶ØG»ÏO÷?M?öö·Ô_êß6ìDl´7o«{w×;ë›wÕÆİ­ÎæÖ­›+öF¶õù>ÆÕ»°tf9$ßM•’&å†5¾s×hàÃ\0	gM’iO\"×ÒMeZŠ„0ê»°ÙÃ|<IĞY?5³5L?ÅSLv«öÿ{\ZA¥?¯ç^ƒXİ£ÃÌĞ4ëÚ¢º–×õÁ*SêW}&VÖî—LtÃ˜âíb)B‹«ığĞ±šsAM‘øZo>ìq?œ„¤Iê…”Š4¼„­ÒıLm’ÉŒØ?m­ó\Z‰¡²cz?ïÒ»8¹ˆÙ¦\\ÙõŸ;×MŞöŒR}‹Çºh4-t<ÇÍ QZÓ5?¬9Îø^‚¶ÄıW3ÀÚæË=÷Â?E™+¶Úe?±VË>?-vD~zéXë?S¬L6Á»b­Ës6%¾x\"QBG7ö°fylnİØ®¹Û?{yÁÁc¼#4¯Çˆá\0Ãs·Sb]õù?˜õ°ÿëáñ?ÍÎ]7OU”^â#’Z$ş‰e…œ‡}æùŞÓS&…é»©S<a?âI®Ù¯é?ãEh¬?!Š1z w™×·¬»¾g¥?Y;6?ÏDİ©=1±&”ˆ3Z6	£8šON=½ÕîĞM~†~Ò˜_,ìéô8 õ†m@×¾İ¸÷y8Õä²j­^°­À.ëğ&\\jG]¿v?â›Ô$¾j¼ÓÁ?Úó  úõÆ*?k>¨oTÍDgÙäè,wm,–ÚbĞƒv€?\0àp€¯±z]¿§ÛªÕP­Y?aont\Zê†Tia\r?óAÑ±©_­ÔrƒØ6€åÀpg4TÌ@å|İ¸@0^K?Ö\rş¬?82I\0cl ¾½É]Ï¡õ&éô½‘´(®³ˆNÉŒÂYĞAX¼¨ù£Bƒê\\înàlñ…µ?½ÎåÁ¾]u8Kœ±ïİ_ÓúËC´T»Ü\0™wM¦£*}w@óœRt^ıgáİ”ŠIÕÔİ,Dçr³ƒoñfÄyıgŞè¾Ç«:À¸Söë¯Ğõ@İÛd7¬,Ïñ+¼¸ÕP²RÅ\'É.ÕÆ6­ÑJe…W,|œt‡?AÛ-µñæ\'l¿`U³‰ay”•2ÒdŞM`[jÛR¢=Q£¨—¸Èä3\'hD§–“w†Á©Ö@p­ÎegƒP;Î5?ª]ÌÎ…wm ûÿÜ¡7q€.CD¹#øÎåİn®ÎåÃÔ0ßà´cXä¨\"ÆvNÃá{Ú²M²3ÇlF3û2äËèlœtc„NÊbÒ<a/N.’ÖoYœŠéô&\r]ãÃ¾”V*à¡·8Â@?ûÍ¡´æì‘zİnë\rØÖ÷ï«;\r:ß¿züæ? %gĞ™§£]Ø9aN‡‘ÑĞ?&ÎÏtVê›eÀ{?˜ıA›_à jW™qé\Z¸kP¹€&øåfñea‰Î\'aX6få-ü’\n0EgÖ‰Wj©eêğ2ajR‰­E‹•[-^I%«¸é­\"×‘»é®¤,dÕ°J—rÅYKsuÅåÔC½êZ’oˆË¼<\0ö¥¦Sï?êÆ˜?MîáğL\",Òß‡Ø­Ÿ°	Bf¼\nòQœÆÀüBl`yz˜ÔH&¿Sˆx?\nñ?è\'B.‘tß°„»Ì“ÙAßxgÜ™ƒ¤}4-”[¿ëÎ¶şõ+Lš?|ó@(<Ï|‹)±ö¦³#Ùö0@ïÜ=É4ÛšzkÍÙ‚%²]ığ™tÄ%’>štÜj0?Å\\î*2Ú²|-Â;!ƒ	Éü²ëcS~¢Š/6j¬SkÁN]mHÙm~KªµÖåò«q§ÉtàQë?Æõ õÀ\\y¨®ıÁˆ„³N8	VAvÀ!…„¾f?Ì?*:Š«¸ƒße=«±f(6ß¨ú\0UôÈæÖ/ŞŞë gM“ªe[ë\0Y	˜†Á¤w>0‚\"=p¾’‚Ğ)>c4²ó|ûŸB©Ú?&Î;˜Á,yÇVé¨aÜÇY\0Öu\"¿q>Æ8=÷)‚c\\t?°‹‰Á¨)¤3ğÂ†Îj¡OE6éOÇuwÕ¸Œ¾;İÁ5úNÃëÓÌ°10Œ ‘F\'¦¸ÿ?¢\'úÎ§@3·5b1ÈÔñûúêãGZ»¾ªÅ–oT®t#ÿ`§ š·ÙB¤€ŒÑ¯Ø?ıäÏCàÍƒí3ñ³Ä´ä²d¦¸ZìÃ WÓ±¨y¸Šn1%À&ß„%Ù)¸º9BŞõLóšİ\"?mšHÂf%\0:sj\"\"6ÓbIŸ¶ôb2ŠN)À³hUØú8UgÓ€üãµ­µ0›‘QÑ²ƒÜƒ³ª?Éuâo¡?5çtœõ?BŞ›ú!éó%¯Tn‘lÂ+,ˆ?ïv_§Û‚ÈŠ‚nš§++é9‡´@lG.ìá%u·Õ\Z÷Ä¿õën4|3C:…—éâ*FÖ¦>lK#H4ÎNS[?’-èÀªÉ”ø,A\Z´- §¼aİê„0¼[\"Ø\0Œ>?»E;¹rK?iıÏi;Ë«M\0÷¹‡gê?Y·Å\ZJ‹ä¦˜È)gSW–§Î¡Ôo,zĞ0‹cfªó´ºG\Z=bå¢¸¿&ŒTwÕaÀoëº(9rşÀ¸ÏÃºÏª¯YlØTf60¢Ì5Äˆû†IVˆı)ëû‘–¶_œİBF¶áàğ‡ghª*¦ƒã1….ÈàØR:èQOô”PäBÇ\\#]ã8ì}Ïâôµô\"£_ãm\\Õ;¼’«zçÜËIFì‡\"¾ÌZëzè=Ká•ÖÉ#¨`JêR?ÅÄÚj*N¬½i=ĞÏN£>1™Øœ\"ú™!Ö‚ÚQ7Q4²100¢?×°}0R°(§?ÊbnåWùx6ê&ÃŠº›å\r¿XĞ(	l$¯©]œw¿ø¼5gœ‘?\"b’%EMÏÜŞO=Ä‡¸c*\Z–İôZİÌµzŒ{±¢UÙ§Ëµê5»Q¾š<ÉG´¹—\\J^\"YÍİã½ÃÃªŞú\'§ºÓ¤ƒ$\\h° ‹r^™xİˆ÷€/Ş‘Éû¡“š¦Y^ŸóF?çæ‰—?bGÀœØ±„¬Aìì£$»:(TpŸÏ?é§Jôõ\rÓÒg–Ììğ§Èg9ñŒ€R†Òªd4˜6¶poØiıv\ZıÂVv+<c9	‹Ó?zq‰![´«¿ëBô?}…äB^ŠÉ¯|¡?WhP^ˆSuñ®?Ò&HxÇ6E…J ¦`xU6º®\rÃ2ŞÍÂšÑÓê‚ìÓYwªCy½wgd™¥ošCù)&Š¨Œ	?c¾ˆõ>&¡`)4x¹GãK:ŒªNË˜.¶ÉnkpFà»g\Z,]ŸŠ—;öjNïù^ß—‡íÎ©¸B(¦•ÛÛî“…KOÚ#\ZbSÅò·{¬ÌÙ¶	}îdx¹2·ïòÒ¥ûšdLÙLÊî+³­0MÉ¿2»ÉÙLÈ!Èë?ûz`^Sj,Ú8, !#UĞ#?\"–Á?½Î`ßëÉ?ú—çƒâyÆ!ÌŸ©û¢²‚æ°£Z4?¢eÅ±—5gm)=ÀBu7‡ìQ’Ê{J ‚8½.©½Mú‡ÁÙ\"˜Ü={ƒI‡LsÅÊœ/¢¤:¿X€RL”Ô§çùê\r¢eäÁÅ¥°»\\Ó\"Ú“ª‚æsX¾!ÀA÷\'ÁÅ)[ÃÄS:ñŞn?©—˜ãÉ~üÈz£K_è\nºœ:`Š¶ÅhOŒvkŒKñ?N/í×YÃ\Z?ÕÖô.`›Qº‰&îÃ¦\Zc´\ZïC•¬wŠc,åZ\r^‰Ö\r·º?2`\'“Ûl	²ZÀ--ÃÍÃÀ›Âñ®Wß(ıH.ØÌÏ]YRĞ•ßÒ.ò	[SåĞ™û@V¾!Ü¥ªÏ8àŞ i¤A¨8€¶Åtå)!Ä,WèÌ£”“ä¾,Y›…â#-SŒ{œ%ƒôTdŒù©ğä¢Â‹–?¤!†º7ŒÆˆiĞ«?)?š¤V¡@¹¿9ç52?3¼€i(mCÚyê Œ?;ˆ_L%©<»qwˆ¹;§ûİCØUœ_	\'l]ûà”rci];ßÖ æˆHL­ÅQÃğ= 0ÏäZmt%ÎX#(“cAŒ¨„Œ0ÿ¬M0	u‹¹-OÖYKÏk¤1e]E_{oĞÕ ¾€y—b(ÒÈÖ7å]mİL‡y‰w\r¹Ğ7şKNÛ(‰=kûº~÷\rwâş}àñ{¾ÛÀ”C›wê{g‚·HÈ­¹¤ÂpÕÀŒ‹?Zú¹]Y›İy¯7|7£Då…ş–½‘™¾”]{	³ËCûmÑ¶å–.ÍÆ½Ä?{Y²q	†láî?Ê-şĞš»ïø»bI=×œ´4³7?Q*ò²±í8Ô\\I|? ?œşÎ X?\nò³Í†Äûù®)¦¾tÁògË[DÆ¥|aÃÇaV—\r©QeS]c rAe»(Ğ\\†À¹ğ²f$š?\nÉc	v;é©	âÙf8í½K!í±Ò´Æ³QÉÏ(å	à´?Ğ÷)¸ë4²$&GbtÅ\"\"êÈ@@°?›7Ï§¸ó?Múæ?GIv§6?œ^ît`â&§3ü;òoø;Ó·,ºlĞ\0D‚¯¿Š…ûÃ‡Ée­Öí&—üûY\0?]ÖFô‡ı=Dç“\ZNôçbÔª2ÛøÇÈ¶ÔhêœÇÃ$«¥ğ?{9dî†ØN\nì;ÊJÀ?_\"ßA|Ğ)!ó&F™|%Ót[»CÕj€ÓO)¦V×iö\ZÎË^’ÒKøk_;3ÄqD8SD.éÙåşmìt¶í…-šwe\'ßÌå×\Z\ZĞÙ_Ùhœf)‹øü¸üİ#éK)–êÌåi÷‘İ[Å\0aEqĞE¬m¿ª9w]VÅLå­9”Ç€O§Ú=«~Mji“	?ñ€0’hR5–Um?`š)°]¾óTsE.6™×2W©üÕã¯d\Z×™Ñ:“à=Ôzı}8SØ07sÄkÙ{1ç¦S¿µ7ƒ×¬¿?òo \r®4#,)@üû=4™3SZrzóHœ´_²H¢ƒáEn?C—ÑIÁ2¤$¦°­T¡ØŠ^Áã0£›ÔÓcX“:Ö\"=~İıF?ñÌÚ?[\ršöÇ?N?ö?_<}uBÙÜ½Ÿ\ro®İ ¬]	Â[î—EûÉ754³Ióù[Sõ?û÷7î?D-Egù¢|ËŠ^â\nµ”©è\0Á7¦mÜèh-<D{Fg?f`â&Ó˜‚[Œ“4ÒY*&IF1hóÓ”:Xˆ/LìŸşmÿèùáóÇu­ÒÓhHrƒXQŞ×˜ÕÄÊ+4]ß¼Ó¾Ó ?\'ƒÓ¾ö˜òÍ!y³•À”bS2<2K¢q?¦¾FÁñğÕœ9}ª-æ\ZMéCSI9¾š¼¶Ã¬Z™Ğ+®ÃÇ¾ØqWÇà•6Æ*5±w!Šêd²x#»Á©|AÚ&“É«4d¤,»£®ÿ<Ñ¶¯îJ0ÿz¹?ÓaŸ£œ†›²å¬FˆÀİwCİÜDİÀ?[TuV¨ª‹¹U\\®jqÒSwßP†ŠÛ­÷å®bÖ‡/FI–…Õæ^*/8™Ja\"âüDÌ0x4#<°TZ¥mIs¿fİ9·ızÎĞg-Ü5Õ‚ÑÑÖò˜ï&*}ÿ›qçÎ·âŞÉX·Ïö°Zş\'7÷~dHÆÄ%†ûØ +½s{ø0Xø0Œ?Ã·@EßÎºGÀkém2ä«õì!qFi£ëj¶{ÚÜZÛ*»T+1²î¹ÖúH[kªFG;gf}M²I¸`¯íè‡FØ‚ª×0£©.r‹Üê°¶XŞÑÉÇ+woß–3M7v¤ßò@?‘;ÿrUÜj›0¶Îåİ?m·[TeÍsİ+¼d\nˆC½á¬ANÔ£’==¸QY·ËY®¨ìŸşfu¡°5ê1Fgê¶Şƒ[ú)îÎU@Ğ°=Ïƒá EÚ\Zõ.È‚wA`ní¾Ù²w1¼cmİVGáÀ@\Zdã­õuø·?L±>v×ÃË\0ïëÓõxš¥çáp¸>}?×ûIoım0âvZ?››?{›íôm*ÀhT<SÎ®?Y¨Í™g3ÚeØ¸³Q³Áyhq¢H0àq‰Ø¬jrycÈmH°”\0ä{%’6F]zøpK·Âóó·‹ä?zœ«ñtxóömŠT\ZM†Yû5vğNùÚˆˆĞğ¯ğt\'¤N&#áé®1«_\0ÉÉ¡ZÇ›&ò®sX3\Z:j\"I‡ÌŠ0ê–#L1wóX³‡ø¬®¸!èuC_ ¹Qø>4(ı!iPt¡¼Mh?bxC‘”ËIÁlLßİnz‘¾ü?+èèJMå‰?4sƒLƒ?•\0È01$™ª†Cnì°¸\n_áÜ18††q†õqâ9?7Œğz8Á4p¦ãI,Ò…µ¦y\nXÂ•%òº‰^ì\"kıüÑş‘q;2ö4†Å~)9êtš‚²êvfS	QÁ@—‰â?jâèùFH²]ÀÜO8§ÂE€–Ü¤?°k?pğ–h‡v.ÿ€ÔÂ?éYØ»?:¼`àè£ùxÊ‘jxÇ®!°«¨ûÀÉl=?[ÑòİPÀ%ÜP²D^‘Óa8È¸0/ K9üá•Ë’qCæÈím÷&|—^Ü˜fK0 9îÎÖ?†òfR@‰^2é› ˜î)Cİ¸ŞôÜ¢e@œ½Ág—´»¸2¢%Å4®4š(:K&&{¢‰úTpœ>òM’ÊÕÃ‡h¿ÃâK¸	“>ªúƒ\Z#¥l¸Ãd=N	ÆñCÑ?®ÄlˆÍÒùÜçbÈ¨ŞšÛ9³yôK¾‚Xğü.WƒøPùFHñ¢¶y9\r—ÛĞK€¡,ªGÊº½³ÆüGVƒc®9?MK¬+—hP‰‚uÖ}Nƒ‹FJÌeèÏß¬¡;P=JÖ‘ÎÿAã[ge;d—_‹êÃ;ÄóN1ÉM¨º+!H‘d¤–äTX2®¡ş»NûÂŠO´°\"æã\n…\nZh’\nTß©°i+ĞÒ-láf¡Â‚nU¶@KUláveR!ßÂ?ÊYªhá»ÊYÊ·Àk•öÔÄ[ã›d0 8@)Õb¦è1¢ûw…°s“6yI½úwNh?„õsÊ#™[TşƒİzJT>\rmjÀ¿qU1¹ÈˆN°2Öï5ï_çãÄDëı/r~üGuëÎí|üGFî|?ÿø[|*Ã:Ám­Cu³½qkãö½M\'T×ãÍ£İGõKô#½l¬½<\\ß¸Ûi7üøX—Í¿ÇÜ{3¼ÄÇï[øİ?’eŞ/tAŞÂ¢Í_š\rõóJ­^ÇV hR+ôàxp?¾W¹[¹^7\nÀ¡g¶*ôŒxÀ½[¹~×À!ñ€?6/?ÖÁ?·ğŸ;o¼~pIêÏÜ’»?üèÀÜÀ÷7ñŸÛøÏw>Ì˜%%W(#rÅuºŸœõôİ.|ÿÓ	ÕXG²6¯—4ëƒÙÄ]e-¢£DdB«Ğ:÷°ßş9&´¤ÃNª¤b0?\Z?so[	¹_ï$9í†·9¡Hııˆïé £Ï‚w!GLÂèZU@0z4G]—p0ØzœÁ´éş[\'V‰>™3÷äp$Ã ÷N¾}&ïWzN¤õîù‰VmrwKN6xõ|ø(!à¬’àr?éO\0¥%mõ?ÿõ?ÿïÿçÿûÿWı×ìíø¿Ïš«¤¿;–€4	IÎmŠÍÇ½-ÂX\\Qœ{ÅJ ı*ßê´·W$û^Í5@®¤mæ&÷Fã”Ö½NcÛ\rÔW6Îö‘Â¢›ÑE¯Ã¦XG»™a¸ş6XÿacCBœ¬?yÜz[å<yJX4–WÏ÷^<\"¾‚“ùP	Áô®?šÅÁ<ZïÆÉxC\0r`´cèea6	&†ãN¹á€dÃ?Ú)¬Q?’n†‘‹˜•ÓAÒ/BN7K6‰ç¡µ¶uOŞæ£nwñVÍkšovšƒfú‹„KíÀÿRß—*·ññá¶2»Z›Ò?9eR¡?:\'ç­h/QGE\nûA$-Ftòë©8DPúÑ+ó£WFö™$jƒ_¾À¿İé4á?÷µ¤’»³p$­™Û)ÕY{ˆ×fŸÕjtJ«mŒQSeÿÁ	¥#>§B‡ÿ/ş¯ÚÜÇÎYSóô Ô?EıÀâİ6½×SÚ)LäM™ÈNaúnn¯Tmèíê¶»C( îÕvˆQHs‡hDÙÌõA†ÚcÙh0‡æp€ë«¹«€s/ºÖGÊ²\0SÀÌ{ªÇeKæ V®aŒú†lèÃŸÑR dÄø˜D!¬ë_ùVÈ›„w»›‘ ´9˜€GœÖÓ\ràH¥IyZ\'C,:Ûy¹Çz>Ô¾€Ücøÿ‹îhü»Äß(áÿ7¾»ÙùÊÿÿôæ{øìåÊÚš¢Èpkü[=EÓeÕÙR×_7Õ«Xg	ûX„|aM®oöw¥j¡#!Y÷@Z¯w_ÃoµÑæ8KíÍ6Â9ÌlôB1ÖUû—ÙâÓR#\\¡	àPØ¿qNµ!INYh­p)]‡¬—™ş:M`/°:Æé„½×Áøó@Ìş`¸X=œ`NGuÿ->ıË{üwéŞ\'>XAk˜…aåaW	5ïÄá£3™‹-¿ Ö<rHÏ_œ<Ú?0<ÿééÉşñIM®áˆgœ\"#jW;7dõéé£ı‡¯Ë]EÛÄèÎ]LÒHù­¨:ÃõëQ\"%A@N)²ğâQÂà^µ¿î¿æ¾?ÂZiªŠÉÔ¨¼Ë`²qİ¨éMËÛöqˆ€²ÈP@Œ?‘ŞtRfÌæ…ö‰aæ\Zk^§g×uø2§VŒÁb³dJEÈÚ™ÁpÄs\r‹˜Ÿ ‹×?”IÜ?^sjĞ?!¡¬€?;Û1ÂÔ?†ÙÀxÏ“ÉJtÖÇÅijPPiãD?çCÕÉ·¾F?kˆ¿|Ó>mĞbCGBŒš\n«ˆqX?Q+µÿu!?\rä?s¨¢ûÕ0Œ-öàúøïıûê;õ«ŠØ–b€ÔUÇ	’‡w;Ê¥tuªÖĞ™ºÔxZ\\Ğ—ÓÂ‚Âz†ä¤HÉßE¤ñÔ¹•8;¡u‘LŞ)òÆçDYv‘$„tb©-\"t%ÛkNÒuõ4IŞqZh\rª?¶ô$ `÷êâ¨dãQ†¡tŒ¡ƒŒhÖ9ç?V–+’Õ©¯Ác<\Z$x÷ôšáç»â¬K–YõĞ‘uØÄT…ÓívæZç“`#‘¦˜Å03·lPM‡ñ+VÓßõjá3Œ\'‹cî ğÙ?³º´Úk\0õ¼¦Ñ—“¤?íÉÎ7kfSİb1rÊQÃ¨ÛÚØVÃø§Õ²-ñÔy¿üŠQ.İ&‡°<À/Ä«áöáæu5]Üy?.lj…7f4f€İhz³Ã;¦c†8´1ñ¢6˜Ğ‚Ì(A\n\"‹‰k`?^éôñ¡+œ9ÜÛæÔEèé\\ÁË¹[x´:ÆA[3Ãˆ1ü1Ó ­ôFÉt1ÚkÄ1k\\€ğ6çF¤°Èäuàï$/é¡Õhh\\+n›lªNˆkGÕù-Şˆá§Î…/\ZªÑ0‰¬ÌÎĞƒ£§Û+9 úº]nOJaã­Ê\Z?`MÌ\nóíØKQíøNvnãr½ 9Ú_5î+í(¹ß•§ÜÜÒ÷Éâ>ÁÙÄêvi[¹£ù)ºqã\r9àün+³Æ4æÆvMú\\qˆ­³ÈøuW\'6fÒ‘gÓ˜4ÆŠã³àê‡io?©	{v†\\T@1¨»3?Jò?‰ÅD(•cÑ·?Ãax€Åg£©xW¬q§<õú¼X?ND+M5FOjÀÒÛşV«©OßmŞiSîê’¦Oª9©Ô–ócñA¿é`ç¶‘Z)e}Aæb‚ß$øÈ¯‚Ê!¯ÚïÔóû%%wTk£¢g¥s7ïRíè¢Wj¨†#ÆUøN´]\'™{õõ–ús¿©à©›î€š^«p\Z\0ŒÖç|f°?×¼á¸ûcé¹óT³#z´{²«ö^<?>9zµw²ÿˆ5·ï°«•¼·şûØŞwû¥]l¹O\\È{»Şw†³M»ˆ¯|9Ğ±·ƒWjº:ª?ï¶ñÏ‘6×¤.O*yÍªûŞÎbQ.Ä¥$¢åÆıû\0DŞë5ÅÁhÅ!ş8}ı„¶8„}Átl/»$€‡Ow÷ş¦ğx­me??½ë‡\ZÎ¶¸®Ô<t³£?XZÚ‘pˆc?ãQR’˜9K)ïÂ‡EdJåÎg$‘’ p½gDŠemQ?á0›ßSÍp8lJxsTÿa (¨s‘»¼)^·è´Í—j…<»€%?¸¤Ú­ˆHÀÅÅE[ô(×’€M	¦X²ÆŒ•ğ?Jl`–&«¤É\n•œš)¡éD\Z†#“.œ˜j?tÎ¢^ıûX§DA˜uÃÒ\n+\"*‹Ò¯Î{äB_§Dìà\n{À	‹¢õ±dw¦AÈÅ*è„—ìˆ:?„R¢n¤©/SˆèøUµ¢Kf(¢pH¦·+Üô„Òù°­\"Ÿ…ƒº‡é„ÃÕPq3ÒÉ+”×lY¡<¿ ¶…aggPJ«ë²ç‹Ğ?Ï(ñÌîÜµ,Í±–*Ô*ÌDèÄry¾Y\Z?Â?´&ÿ	{â¢köÂ~Ã±»¾·˜dËâ\nXÜjqo}ã*ñ^\'¬‹£¼—E3¹â€s²uZë’óe\n“CäJñ›Ó+`(î6–kÍ0¤Ÿëã#B-¬cúº<ÄïLSÌÎ&©E?Ç&DŒÃ$N\'¡+kˆîÑçÓì[CËİØTš\nkEÒÙR$Mot/0Û+–p{¾?0?w”¢©±‚º†VìXÍ\0˜¥HµÙù{î±p8.9îaX#\'\'{$ì!P‹ö6ì)¼¨¬cª‰ª-ŠºU[”ªe¿™Òè\ZfÅ*o\'…ÊF­+J8•v‰ÈR?óDÈŒœÁ5é\n»Sï¡o*]²7èx‘ZˆH7sí«¹h9Ê:ä¾|¥€4MÊÇºm™…\0³»ŸåUŠ¹1%İ#]En‘›À­sÊùˆ=IŠ•Bú¥¹œ#\0ošjuBWrŞ~ä¢\rÑ$jÙ¯®®‰Ä`Ê4Õ5}l¡ YaÔ{D\Z0ÌóÏ19|şX>Û}¼¯ïôŒw©.ÊÍ:R—a®-lÒT/híÑ«g/±5?2O½xuRÖ¬Ã¼0ÛrMS`A3n\rä×\rÂ³Rß;úsIâ„öEıò÷çŸ¿?ÿXhÿ·‰s÷w¾»ıõşï·ø|½ÿû¤û?ªüÄš•şÕDÍXÉ®A\ZüÄ½&£µ\rÆê_T¢¤İU¯£I8„¹×‚F…Rùl?ˆÈO?{|½íĞ`¹‰ÒŒÈ„‚l¶GÑ^zÅ™·ÈË*º‚fsœ\n¢<©ª±ÆCÙvj8úDJ¶TÅzdäUS¡Ë531”4ò5+–ÍŒZ¦?‡ÿ”k©Î\nG²{9I²äzêëI5¿WÓÂÔG\\Ì ¡Á÷µ+ÜÅmS\rü\\ía›ÕóÊSĞÛ¥«ĞĞ»íYâ¼´^V*ŠB³œ°®æOóØğ)gÿíû\n6œr…\nMı½ñè?ëÇµÿÇTŸ_¢?ùôÿöw?›yûÿÎ?¯ôÿ7ù\\)v™Ä(#u\"{Õ¢&Éì?JQ¸øÒ.şhÿåÑşŞîÉş#ÀÏ{ï\"˜ôSÒf‚xŞ?†Q6ûhè@İcµ?¦I¬PÅ/NQ£Š=WüJ3³ĞhÉ?}ÌÓ¹÷ÄXüø÷\'\'¥aŞàùUâ¼q>?ü\\}JÔ7í-£uLÿ1Q|Æ¦¾Ñ³0} Rm“˜ƒšoğ/Ë·yÒLĞóÛ˜:??6‡Øêë¤± /\"q?‡¤ÔîcôâI8Âî»vH^à/u\'ÜF. iÓÎY?‹4¸R£&[ºÉÖ¼&?ıñåÛ& ¾\\«Ì\'y?IĞİ?híááÉ³İ—¶¹»¹Æl?ff03ğÔ6î@*‰M«nn²z†¶\rôo˜\\ ¥Bïœà¶LøSbNr‹×Ôg‡Ï1õîÓÃİã—\'”ç£¥0\Z¼¦¼3xyĞRõçœÚc8œµ‹$ç?N‰sïF 8?(ñ\"6y1I`‹R€Dİœ?&Å©?çvµ9ì*Í&gZr‡àôÙ#½E âÃ4rİÂ&bA?ì„$A³fmÔ¯>O@’AL*AÕQ›b‚Ó»Ñ¡d¥–‡z\r‡8L2X‡.NÇÅHGN\'-D€wÛFğ:9zµo·ÿîÓcœp³ÆğR}CO­†ãÓ4»èª4Ô»?ïáïüõ½¹L‘û^‘bözÎ¹fĞ’›†Ş„J?v——CŞyK8ªê%¹Ò6Ù…v{¥vrrºŸaO´Ò~ËQ#MÜHyrú’R‹R¶`I3Šß¹\0ù(ÅT-’¯üÂä«™hH’.„q(=Á$ÎOÌ®áü¤ÌÛ:Ğu­æÇV2ĞüÇÒÆpıg\Zø‰	«Y³?5???>cóZ$Îæ¤‰÷\">ÖÖAIÌÇœ3oİ~ÃE7k‹ÀõšrW(<fioôÄ\ZDïõÆL;£”í\\sRT‚×lÛGfÂì|Á«ÓlÈ½Kø?~™¤ØD2ÄàùğïŒ‚\'§´F™³0&ó)»Œ8O/G(¶]ô£ï3ø^¶RL]ÌRqğ?Ï=;&« S*\r-^‰Kv})miVòÎì\'Ñ.@K:ËgmGæ‰š’×-U\rŠïæü°güá`Èîö­*©“6äŠê\n¯Q­Ò;\nƒ1?ÖuœÍ\'\Zm×–ébùƒ—=¥l²çŸ¿/?ÅÒùYğJ|‰ø?Ïú²-é€A@)-†B¡Sã²g±¡60·±iéb”ãÂJæ(›À†+.L0ºí\'7¡J“}ú•¨NUR¡íÅ#t°HÅ™e-#W½ê ¹ÖGÉ•æó3|VH\"ËÇMD´›@1©1]¢íK™”F§7|…Ú?yá\'ü¿v„?¢g\"ó	´9\ZO‡ñ²’äD®´ÍÅÕ^2qœVÔdoÜ»÷]ş¹«§±zõ&I:\Z2º÷t¥ã?­_èª	$6? Ò°\r¨}”®R»QÌú|\nE3Œb%êï\'!FX›D]âÅ)zÑ‰®÷$p¾??÷î>Û?R/ÔîÓ§êõîÑrùûÇ¦{{Ç[ê/õov\"6Ú›·Õ½»ë??õÍ»jãîVgsëÖMš2³ê{Ö+¤%Ëf–ƒ|`zì)î¤,¶‰¨%ïøyè­°øƒ>?§áO?„”ÏãşcL<¾?àPiì”f8LL‰ÙãŠú™àıÿFP	w/T9„ƒw?ÎH\ZÓ8¬7dzk\'t,‡Cl(e¬2É>x<i2? \0Â©`\rÉÚWx±¡Å?ğ¦´W+Ğº§˜PBĞ\0/ &”‰[á%l´™(b‚r¥$Âğ–+u8(ïÒ»\rĞ²?\0z¡¨‡¸Q£]s›¼÷\0÷`§Îƒ1FHSê€š\\DiÈ–s<!=§pÑ¿\\Ög|O,æê\r-]c	)àŒ{áÎ¢Ì•ÎMN—ïúÂŠ÷?l±£0?³tHl>3&,BnƒIˆ?sVXgÊ¢+7„âh\n¨uB.ùm¨}ÇyÏağ½¾9{àdÆ¹[Ï“8”—Ÿ÷ˆ­=ø­ND’K|Dš–5­œî8Ììf;IÛoº©ßJ×\ZEUÓÇ‹Ğ ï^™»»,L[¥?¾bE¢Y;6ÉÌŒÈ„VKÙë7Nk¿ÈìïÉÉ³§·Ú…fœ$FF1ÙD‚¡’üØmÀ×¾İ¸÷İ¶æOÄrw­^÷Ù¢µ†’HJd2F¥vÔõkbYî±âòòªÍ?‚éÅol4Œó‰õ­ÖÌNP}s[Eê¾º»-ÑşÈºvè¨!ÁD\rğ\0ñµk<¸ûğóŞu“–=ïê1ìĞ?Å%¥2-¬!p>°U°ÎBCñ©(É­ØşzƒØ6€+RÏÇTNÙ?‘Ä–ÂÚÏ‰•¥kêU<I\0{l î½ÉÈ¡ø&…²Å\\ÔX†³>jÅã\Zc‚n­i^n?ó‡‡vŒQGDf›k;™¹!RéÛTï¦€1ZóJ­$>²Øm›ÉĞ2Fn³’ßM‹Á‘)ô0Ú«g5Ú¼ı3Ç”5¯Ù( Ü‡iûõWèŠz îm6(?\'Ö•çø^Üj(?d¸rÕìÉª9WÈ¿¢†èINºÃŸ ı–Úxó¶‡_lĞ–MmƒF›Ëºäë¹72‡Şb÷aŒ{œÌNr˜?¡õNEC4œÑÉ†5åMbC½_ç²Ã¶8Õ”½\\í‘ÃÆŞTıÜ¡—qğÙK3Ùÿ6Xçòá*‰? êN#†M‘\09£4¾79â+\'‹§×™Y;+şØ÷yì”ÊÕÙ38%hT]¤÷r„jr†N.’‚hY,ÌyŸÁ|\r‡Ä.“é(?˜^´uõuùÂ*FP¯Û=½q@fÀ°%õvöÍBMÅ®?çóàe\0±oNG)ËµÓYÛUªm¦ıAaÚ:3½úôi?p¦½rŞ%ğËÍâËÒU9Ÿ„aÙpUa]`^<şÍ.ÍÒ‹ÒáEÙØ$OG¬ô«¿‡næWm³jÕnæW­j ë¦C¯Ó¹‹wÅ¥£`5.·ò\0ø|£Ã¢“?fîK²\0ı<°”¸cZ+üû»õ¶€¨Šç]^!şËÏ9‹Où–	¯ãÄ!Ş¦bzû	G$Ó74ı¯#³d6Ì7Î)ö—VFì.…GÒIAåw8h1|GS~õ&ÑF«%1®­vÛ¹­D™?œÆhüÛ:?»IÛ±2gÿYªa;üYó;É’>*“q>•±Î?4WG²0û³ÍE)k?_\rÉyi<ˆa‘z£±NM·Ä”V~0AéÚ5Ì¾*W~’Î˜~ğK…™e%ª½Ôù­ü¬Ö m˜ÄJó:8Ä?™KRsí2åæµ	°j¥cÔj]ä‰)ÿå)åñ†”´¨fóÛ7M&qJãe[\Zyùxù1a#gÂœü6ºx6i½îÍã\rN§ˆó=yóícÈz–í$ós5MO/?õRÙ‰ÇW^Â1ÿß•00sFè€ÃEáİ¦>¤üák¦PSy[åšÎ,Ş°I¼¨>€å;¥|M§Ï“LÀÁ¡İ&S·9?0âÆ:;)C&‡—M*QV	MÜüòùtë:MY²Ø«<b´tØ/Œ¬O!øƒ	å¿Ô×=a–¿\rÔéÔyøœì%Îöú^ĞäAhÏÃsÃæƒÑ¥ùLóvœ—¡‹—f—¯™ö0oƒ¹Ñ;2wê4\r¢©ÜÜ[…Ä[e=Á(eığ}Ô?‹ú´\"Ó}YW=Sz0¯XÌ¤3ç\0³„-èK1\rXI?d1óœ~h)ä?¢/#–?8üáæl!Ë?J\'Í¦ƒ™8Œ´&¹ P$¢.@Ëåï™‹ˆùHéÏ\0ÁâLoÚ;K‰[p?Ï«#2MÕk¼®z‡wÁUïèNØuEæh1$ûÇìŸÀá#»…#t„0õšÆ¨FÉP×/5ÜDVUao>‡$¢?4 ?ä\"Õ2œdJAéq.˜N9˜•ãÙ¨›y!Š`6õæğøÅ‚]MäÆîvˆ%7?ş\\\"òÎ­:kCæì	Nd6‘—‘×`îÜİâ8“O»éj=Å*İ”½ùI}Ü,ô‘võÕúˆU*ú(gdQ½Nn”oB³ä|¶Ê·MaçùûH¶›®kÙAR«£Ô§½|˜gc¹¦<ö}cVjvÌCª¤C¬~>¤»AÖƒUàbè”—¤ñÄf^4l‹—|ÑV˜•T(ÉÖè´À	]>@|ïvh›;è\\ç2‡úÖ|œ¦f®q%ñ¤u/]½ğ=Ìï6‚<“¼â$-\\_.dş?ñÇ£?®8¼‘4ØÎäQ‡b¸1bBÿ4fı,}\"W6—s5Ïÿë\\²~V1Kn•?^ÎÊ‰YË]¸“W2€Ğ\\›ï¤eí[HÔræe-9ŸÌ§°?d~¯?…GUìù½ü*£–»şw†PÓ’“íc?e§b§?Œ$•%Kœ±¼g)ˆM×©\'‘¬Ñ©Â*ÉZrÑ	orÎ#uÙùõÅ.y®?µ3‰Ş\ng£\'Ğ™?~ã˜Pak\"vië~‰Qjã{2ŠÜBI‚[Œ.ĞjKÕ³L¿¶ˆ1¤³š š!Ì?gç2m[şŸ*ÎãsË¨À¯´BÄrRÊ¯ã½İ§û§„´DÊsf\notŒßi‹\nˆ!ıj@=9|~b }?Ë(]½”3a8v#1\'ì¤y3Õ‹6îrsD4C…½?p³íy#)¡yÑª7·U-+tõÙŒó#Ú;6)7¯Ô›ˆÚ³ª€?/¯œ?`E&½yuº9^9Ú7ÅıìACQ3ÍÍØ<ëÊºm\rO?#©y‘÷ñ7Î>ö€‰Uf˜XhÛÅM¾CSÜ)5LcÌ8è€Pˆ‚a?xBãÇ‰†âw?(Ğ¨íy³Ü·/ŸÁãeÕ™•—˜ÙˆXKaÀã£´ÄÌ”X1©èíci­M2/G~wÒAÑXl¢£ÿ4‰zùlP—NuC—7yzùdP4ÎEğoÊÏ‡k|[İ–¿=¾­nËß™3?eğ¿›3?>|XMÙ=ho?ÿéiê©k4íã[úñ?7\rS=Ğ‰Ñğ¿²êæqIõYYëºÜM¯õÛúñwNõ’Öıêæq®:*£§“35Ä¬?nöK\rğ¤A‹£¬=A)q‡¨u€ä\0\Z	æ²Â·LB+©¢ÃíÕõ”Q?Ë¹uèCØö\0¾?¾\Z[2ë°Œ‰¤J/¦|HM£Û9®F½Á÷9è\rÄî86@ÏF#êC«{c¸¡¾ÃŞİv EYX9ú	#Æ±ÿïMÏç{?%LF7â€³\Z†(®İÙ_S@kÿû­5eÂäl\0æ@2Ñü˜\"Ğs×ş´ “å|D(³v§MåX“6}+Ñåä²¹æ³KH%¢™3\"W6cbkH«úíD-™öÏ\'k-iø‹Ó§MøişìGÜrLïIŞò~k?KØú‹(¥±˜5óŞÉ/>]™óÜ.4kåğ*\"|0[‡¤…amI3fw´´æŒPÄµÒ1“\0åL™/Aé†èá;3#XÚ}£=\"\0ş¢Y)^‚ù5°#Øø-R2»ó)\'JyQ›#…Ï€¥léjõ˜¼—Ñ%æıuŠ«ç5×™ÌBÓÙÂ	TëAÈY…?úB¤3ßš}©c£qGàÅUûmğ‰u¤øŒ8…€R‚Æ+Uh¦‡Íù\\Ÿš&ÿ??*¿°E4…g|ğ°¸k_@·VDZåŠœ¾ëBôa$î-RL~å\r¼BƒòBÑHŞS†aêã;W‚*Ğ?7Ÿ›ª+O	™¸Ğ¸›*–¿İ3Ì½½]H?¦\'†°WnI|æ¾$,&³¬ì„›ùF¬y&ÈIÏ 3Ë¸õåõÀ}=0¯)?Í¨ğ˜áš|‹m¬#2V‹Ã³€\\›L„Ş|”ø¼Ÿ`‚\"Ï÷ˆp&9ï6Ğ9;ªEôÁX\nÕb=.ëxÜRz€…êY§ÿÔ…Ùƒ)o=˜@ßÕOƒ3Læ\Z_tÏŞ Ëhš,8´—à‹tÑ®½\0¿È`<IH@k˜#t??ƒ=İÃæ!R{42õĞâ\n©J˜1‡úsjµ1µÒRšÔóç2 €cŒaÄA?J°\"÷rÅÀ*…À@úš\0ÿ6WtªAí@l2›Åó½v0É0â:²å´<ÆBîæÕåFÓı9ƒŸ:?×&¢W÷ì¨pÙœå¡¬Ibë&=}ø0qw\rM¥óè­¡i¾›cL›jŠƒt>\0ğåfSÍ6?Àãô)?\"%nB…›¶Ât2	5£:Øëœ¡¶Bv\'Ôl¾¸‡šgŒõW~á²næ;ôÜG}®»e-?e½ßŒèCvÀìr“Œ÷?éíÈÆßƒ59¾·0n¬ÍU“?¿ó-æ³’„|\"ŞT9Bå>°RZm=?±ÎÕ?ãÊ¢?>ÛdkÚ˜Îíê?¬ÚK\\Jhk×;\'İ ıÄdì¼Åx¼²V3ëo£Ş¶¸mˆ=ùmô?zå\Zu9Ú”Bi?€r\0=í Ä¬Š{Û?Xè%¦ÿõ^ãš±û×À¯‰İÿ\n9~MÿÑ?óàÀymGi¨w\r?\'t?XàV‡ıø¶™ˆ›|¼b÷öM1\rÎt`GÜä?ŒŒ{	ÿr=¤¨›é\\Şå¦¤GT{h.|—¹£$ÃUà\r»5³¤º”LoóömOÂ„^¢S®\Zo%w’Ëhmü0:S·õ>Üâg¸?Wÿzxô<Z¬ÂydÁ» V•X	Ğ¦½‹®³ÿ¶:\ng??·Ö×áßv2	0ÖÑúxÚ]/´QJ×ãi–‡Ãáúôm4^ï\'½õ·Á8ˆÛQ<hunnvîm¶Ó·)?Z_ù,ãwV/ir4¢eü„k??ÛLÁ8ˆ&b5›\\·ÿ?Hªl³B@02}Æ‚l%¡³Q¢K¯¿]$ïÔëà\\½ˆ·x:ÛW0{a?=YœƒÁÅÙ”Âö]Œ%4o»7“d½lŞØ[É\rŸD´É‚xGq¼óÆ¿|\nÂ§ {¡XIƒr‚ô~ÃPœœ\"ùz­ÄLáQ š\r^±Õ½3€1h2?¨Ñ/ /,?O‘ÄP‚MôßcGW¢UMÅ|q?¢O\"îõ	çF¸ĞäSßj&ùà¿ı9î´Tüı¾jáBØ!iMë­¾ ş>\nXÚl\0÷$\"¯öÛÓ~€Krh0Ôì\n¶o¨rÛêHêõË?77q+Ü¹‰nN771(Q]¦5Ãòºü«³iÏ ³y@fs€è¡#8ÛAj;ZDÔ@èšÆ 5ÏP_{\rš†úIÜ#º•Úè—´ë+EïÌ,à£Ÿaå?¥b0TyVZÙãfÚV#YÈIBåÓÖÙØH¿ÜWVåBJgˆİ%—hŒğD?‘Ş\"„5)ªW¬Çau)xrKÎnâ$àŠB1?óàáƒ’lÒnbø}ÍÃ´o7–Ó9x,øÅ‹%¾	Ü¯ñÂ¬“¸>ú3§åHVˆ—øÖ?yóàš?{Œõú52l€+\0_¿Û?t1µû÷ëãŞŸá÷µ?Í»?ïomu¸aÁåˆY0´œÍÛÌX›œ`Jú£]b0Ş®Ëå&GÉ\' TëÒ®Ì%®ÌeÉÊÔŒŠá\Zd¹á§ÙÍ7?]Ş|³]2V×°f¤t·L‰Ã9É<9^\Z³×†8Ä®éÙ—®6ÀLŞOÃè¡K [H´\'1G?g›á´÷vœ_½¦ª~‘PüYåa?¾¡#jš\rŒ“{•XŒBİç–X«}JxDGĞ?NN/w:Mü;Ã¿Ã!ÿ†¿3t×vË\nM=ÈúëÌ|m¢ä\râö¶#1•–¾å\"Ÿ‘S86EÜˆ«Øv?„äÛ¶»E5HÜ+â7Úßµ7·œ`/(á}0‰Ê©ƒ2?è8\Z§äŠÕq¥S§&q<â~rué”CK‰Tª‹Øo5íË¢VSÚÒŠre§˜VTÖ¯q%1V1‘-‚hH‘’ŠîÓHñVÒ5Ir¯öñb­ŒÈºÉ§\\”ºâÅB‘\\°ÄçX´†	Â¥KqO2A×ì[ãbdü‹ôíd¤=Œôsã0tÍ¸	ïj?PsŒªÆaB[éğ¢ğd;ì©Ï—âh[”œ!Š˜®xv++5sr°ïúèÈ6ÔQ?KıBaİ¤| \"£·t?¸‰’úá«X^½,6åŠMÁ[M%åAãáÚáÈIÏ¹ÂvIÇu9¿˜ôV|*?´„>Ó,ÜÈY&vËÎí™5\"ô\"¦a…Ÿ\':Š‚;«Óo-‚;äòƒÃ‹!E3*€Œó ?gÜñ]~Ä²LË³Óñ8.·A-×3Ç.´XĞ?Z†ÇÜF1‘¹…B¼Lá*Gÿ\\n¨\\çìLX›Û	Ç°§6›[WÛ÷¸uµ‘?LY?ÎÀÕé\"ÉÒjÜ&R?™&h¹¦‰Ã5V^£ÕgæJ—‘õäü±G?’›\rFÀZvm²SB¯&hÂJ?h´—Ò.¼?–@Hxq4\r¤dæéL?Eºç”%`zê–å§¾“wÒLZCI«h_d[fBÚp;@‘HÍ¼Şp:ÄÔ¶áö‹\"›š…¹áô“Hµ@öû2k(é?Ö¶c`âŞĞCqÀ\Zd´mÌ\'IFvL¸9‘?A6„±.I y;;\Z¼İs4Tg»uóvvR^ï3)oìÈºy;;šEğoÊÏ‡«>Í_şíJøR>ÿNåü”Ãÿ®r~rği…R`Gà@1ú(÷8‰ñBğ¡`Üèş]±¡Ö¢7Z 6¿ÑtÌˆ½µˆñSš$è¹¥?8¹?…\"5øªŠ5ü+±<»oƒ›8¡Åq,¿w`ú¯Ÿßäãä8‡cÔ\Zöó?}w+Ÿÿá»?Í¯ù~‹O1Ñ€ı­7„ûØÉd°¢êgıS@4Yòî”B¢¡Vƒú£¸?Æüï1Æ\\ÛLQ’\"ÂI(·ãı—”àOÕ%Èy2I*?…ÕxÓĞÒ¼Ó®ïfDîp¬¿®Q’(’¾ùŞ×6°yûÎ#ŞÒ}°ù%qÜ?‹yš­IöibƒbŸ6(	µæ¶á9#ÿE#‚ro”8^ÂÁêÌbÈ!&.Šzïğ&—]É/0±Á€®Ô™C]©It»½£Rì‰êjÿÅ±U¦Cÿ´Õ¢¢VòêhiSm¶ÕS±ÉÀ„Ùf^?)ñdj©v’•91¥¶yL›÷*?“¾¯’x8³²‚ÓÙ\\oİîŠ^ÀŞlcôŸä]È×Nf)SÒˆè	½	?ŸP!ŠàˆÔ\\k’®Ğ2×^*W{2ŞoÜC“· E´R:ˆl&â€<ş,S?ÖpeñRê?fj+eóÊ‹}oş$:¯û¹Î ÑƒAR7öU »Ç°ó»¼Óá™`:I>z\\Ö)Ôğ`=ó`åk;–\\~µ£?ßğï1IÊå &RÒ”q\0I~X„]dñÑ˜˜\"(÷{ãŞ?ÂÇÍÿ¦ÙÉµ€şßŞØÜ,Ğÿ›ß}¥ÿ¿Å§’ş;é‰•~y¸uãÖÆí{›æÙ£ıÇ›G»?ê—×í²±öòp}ãn§İğs…\\6gü^=Puøú½Âï[øİÏbŞ/tAŞÂ¢Í_š\rõóJ­NŞCP´‰©zğ<¸Àß«\\‰­\\	¯àĞ3[zF<à^‰­\\	¿ë?à?xèhu	b7üsÿ¹óÆë—¤şÌ-¹ûÃ?Ì\r|ÿ¹?ÿ|çÃüÑ?YRÒ¤®—\'F<Z–?ª6?¼¹À †ÀÇ«Nş\'…™Ç®¤)ù=Û’T·\0„Â‡¤İÖšy?iuR¸Y%¨eˆs›3¥Ü¡±\nû+Ì¡?Ï‚w!G-ÇœU@FPJõ†!ùı¢öûAƒ3˜,İPsYåM›éî¥;zïä;Pğ,ÔÏÅW~Û˜bz—/Û:g3`ªb8XÍÅ¢;í@dI[ıãıãÿûÇÿùÇÿşÇÿUÿ5{;şï³æ*é??%(t¢ú“à‚ni¤·E‹+²Yß4ıuouÚöîI_h<Ñº×)	f+-°åõi:¡h_Q¼K½~Ãpım°şÃÆÆ:%ùZ?9Yò¸õ6Àyò´\rd‹{øêùáŞ‹GûÄñhCO*A\"˜%›L)•Øú£YÀ£õş`œŒ7 \'8†^æh	˜£(í­gÑv?éŸŸıÌ™¿’-÷dÿÇoÔÉhşğâ¾ï«ƒ£ıı“_b\"§Kgèh÷èGØ|UÛö ™`„Yd¯™ÙÖ£¯#Ï{?Iş »®ƒŒWA‰ÃÿæôàÄ8l4%Ç×4\rÓªúxÒ†9À›Ğy9Ä¦ó´`IˆFMç—¸à´)_0:Ùdk”®p©[·§4]ÍkZ}Úì4Íô—&ßÖvàÆK`§Ÿë;>Ü¶a\"µ\'7] ‘»B6KİèÌõ;µÁßÙ˜†•Ã?°2-}½Ë¯#SÈ?ze~ôË?½‹¶êçËD$™2}­1ÔY?¯½dû|HH§Ú;\0Í«ì?¤-EŒ5§B‡ÿßğ\r®ì4µª4™Dg?Q˜®…>z¯g¨S˜—›2/?Âlà›Šõ‡.Ù§¾»«¤ıcV?t¯1ñ-,;aA<>ëƒÿ´Ç²Ğ˜ìo!ÕWsVá,\\tWÍ?\'fÁåp\Zù³ %£ì³?>¼Œ²ú†¹ö»F9=(W‘…øŒVŠï®H²ùºş‰™7ü$™©(sëÉÓ:¹­8òvNq,Zã_\"Gı×Ï—ûù/bF«{hô™ÛX ÿÁ¯|şß›?[7¿Ê¿ÅçÛoÖ»Àf¥ç++½±úÏMõŸ7Wzç£¤¯şs¿=ÎÿÚŸ’ûŸóÏİÆ¢óÿİ?;ıOçëıÏoòÑ¢üãGOöŸ¾Ü?:>}bï¸7ì}Oş²g\ZGïaãÃy·=É\08µ‰éG\\âp2:áS×cübŒ•¤–\Z1p-Øl[TÀĞK”§ê·šr}Â© (c”šfYğE”G«—L?qÎXY¬Á\ZZ²|ùíKâ&`?(N†™Î\r\ZöÒ\Zöíy\nôíeÕäÛîİ¾»?¸¿÷®ûúùúùúùúùúùúùúùúùúùúùúùúùúù­?ÿ?–\nÎä\0x\0','ross',0,0,0.00,'','','','n');
INSERT INTO tiki_files VALUES (2,1,'test','Adding an entry to test the full text search function.\r\n\r\nHere are some search terms:\r\n\r\nJobHunting\r\ntest\r\ntiki\r\n',1040868439,'license.txt',26430,'text/plain','		  GNU LESSER GENERAL PUBLIC LICENSE\n		       Version 2.1, February 1999\n\n Copyright (C) 1991, 1999 Free Software Foundation, Inc.\n     59 Temple Place, Suite 330, Boston, MA  02111-1307  USA\n Everyone is permitted to copy and distribute verbatim copies\n of this license document, but changing it is not allowed.\n\n[This is the first released version of the Lesser GPL.  It also counts\n as the successor of the GNU Library Public License, version 2, hence\n the version number 2.1.]\n\n			    Preamble\n\n  The licenses for most software are designed to take away your\nfreedom to share and change it.  By contrast, the GNU General Public\nLicenses are intended to guarantee your freedom to share and change\nfree software--to make sure the software is free for all its users.\n\n  This license, the Lesser General Public License, applies to some\nspecially designated software packages--typically libraries--of the\nFree Software Foundation and other authors who decide to use it.  You\ncan use it too, but we suggest you first think carefully about whether\nthis license or the ordinary General Public License is the better\nstrategy to use in any particular case, based on the explanations below.\n\n  When we speak of free software, we are referring to freedom of use,\nnot price.  Our General Public Licenses are designed to make sure that\nyou have the freedom to distribute copies of free software (and charge\nfor this service if you wish); that you receive source code or can get\nit if you want it; that you can change the software and use pieces of\nit in new free programs; and that you are informed that you can do\nthese things.\n\n  To protect your rights, we need to make restrictions that forbid\ndistributors to deny you these rights or to ask you to surrender these\nrights.  These restrictions translate to certain responsibilities for\nyou if you distribute copies of the library or if you modify it.\n\n  For example, if you distribute copies of the library, whether gratis\nor for a fee, you must give the recipients all the rights that we gave\nyou.  You must make sure that they, too, receive or can get the source\ncode.  If you link other code with the library, you must provide\ncomplete object files to the recipients, so that they can relink them\nwith the library after making changes to the library and recompiling\nit.  And you must show them these terms so they know their rights.\n\n  We protect your rights with a two-step method: (1) we copyright the\nlibrary, and (2) we offer you this license, which gives you legal\npermission to copy, distribute and/or modify the library.\n\n  To protect each distributor, we want to make it very clear that\nthere is no warranty for the free library.  Also, if the library is\nmodified by someone else and passed on, the recipients should know\nthat what they have is not the original version, so that the original\nauthor\'s reputation will not be affected by problems that might be\nintroduced by others.\n\n  Finally, software patents pose a constant threat to the existence of\nany free program.  We wish to make sure that a company cannot\neffectively restrict the users of a free program by obtaining a\nrestrictive license from a patent holder.  Therefore, we insist that\nany patent license obtained for a version of the library must be\nconsistent with the full freedom of use specified in this license.\n\n  Most GNU software, including some libraries, is covered by the\nordinary GNU General Public License.  This license, the GNU Lesser\nGeneral Public License, applies to certain designated libraries, and\nis quite different from the ordinary General Public License.  We use\nthis license for certain libraries in order to permit linking those\nlibraries into non-free programs.\n\n  When a program is linked with a library, whether statically or using\na shared library, the combination of the two is legally speaking a\ncombined work, a derivative of the original library.  The ordinary\nGeneral Public License therefore permits such linking only if the\nentire combination fits its criteria of freedom.  The Lesser General\nPublic License permits more lax criteria for linking other code with\nthe library.\n\n  We call this license the \"Lesser\" General Public License because it\ndoes Less to protect the user\'s freedom than the ordinary General\nPublic License.  It also provides other free software developers Less\nof an advantage over competing non-free programs.  These disadvantages\nare the reason we use the ordinary General Public License for many\nlibraries.  However, the Lesser license provides advantages in certain\nspecial circumstances.\n\n  For example, on rare occasions, there may be a special need to\nencourage the widest possible use of a certain library, so that it becomes\na de-facto standard.  To achieve this, non-free programs must be\nallowed to use the library.  A more frequent case is that a free\nlibrary does the same job as widely used non-free libraries.  In this\ncase, there is little to gain by limiting the free library to free\nsoftware only, so we use the Lesser General Public License.\n\n  In other cases, permission to use a particular library in non-free\nprograms enables a greater number of people to use a large body of\nfree software.  For example, permission to use the GNU C Library in\nnon-free programs enables many more people to use the whole GNU\noperating system, as well as its variant, the GNU/Linux operating\nsystem.\n\n  Although the Lesser General Public License is Less protective of the\nusers\' freedom, it does ensure that the user of a program that is\nlinked with the Library has the freedom and the wherewithal to run\nthat program using a modified version of the Library.\n\n  The precise terms and conditions for copying, distribution and\nmodification follow.  Pay close attention to the difference between a\n\"work based on the library\" and a \"work that uses the library\".  The\nformer contains code derived from the library, whereas the latter must\nbe combined with the library in order to run.\n\n		  GNU LESSER GENERAL PUBLIC LICENSE\n   TERMS AND CONDITIONS FOR COPYING, DISTRIBUTION AND MODIFICATION\n\n  0. This License Agreement applies to any software library or other\nprogram which contains a notice placed by the copyright holder or\nother authorized party saying it may be distributed under the terms of\nthis Lesser General Public License (also called \"this License\").\nEach licensee is addressed as \"you\".\n\n  A \"library\" means a collection of software functions and/or data\nprepared so as to be conveniently linked with application programs\n(which use some of those functions and data) to form executables.\n\n  The \"Library\", below, refers to any such software library or work\nwhich has been distributed under these terms.  A \"work based on the\nLibrary\" means either the Library or any derivative work under\ncopyright law: that is to say, a work containing the Library or a\nportion of it, either verbatim or with modifications and/or translated\nstraightforwardly into another language.  (Hereinafter, translation is\nincluded without limitation in the term \"modification\".)\n\n  \"Source code\" for a work means the preferred form of the work for\nmaking modifications to it.  For a library, complete source code means\nall the source code for all modules it contains, plus any associated\ninterface definition files, plus the scripts used to control compilation\nand installation of the library.\n\n  Activities other than copying, distribution and modification are not\ncovered by this License; they are outside its scope.  The act of\nrunning a program using the Library is not restricted, and output from\nsuch a program is covered only if its contents constitute a work based\non the Library (independent of the use of the Library in a tool for\nwriting it).  Whether that is true depends on what the Library does\nand what the program that uses the Library does.\n  \n  1. You may copy and distribute verbatim copies of the Library\'s\ncomplete source code as you receive it, in any medium, provided that\nyou conspicuously and appropriately publish on each copy an\nappropriate copyright notice and disclaimer of warranty; keep intact\nall the notices that refer to this License and to the absence of any\nwarranty; and distribute a copy of this License along with the\nLibrary.\n\n  You may charge a fee for the physical act of transferring a copy,\nand you may at your option offer warranty protection in exchange for a\nfee.\n\n  2. You may modify your copy or copies of the Library or any portion\nof it, thus forming a work based on the Library, and copy and\ndistribute such modifications or work under the terms of Section 1\nabove, provided that you also meet all of these conditions:\n\n    a) The modified work must itself be a software library.\n\n    b) You must cause the files modified to carry prominent notices\n    stating that you changed the files and the date of any change.\n\n    c) You must cause the whole of the work to be licensed at no\n    charge to all third parties under the terms of this License.\n\n    d) If a facility in the modified Library refers to a function or a\n    table of data to be supplied by an application program that uses\n    the facility, other than as an argument passed when the facility\n    is invoked, then you must make a good faith effort to ensure that,\n    in the event an application does not supply such function or\n    table, the facility still operates, and performs whatever part of\n    its purpose remains meaningful.\n\n    (For example, a function in a library to compute square roots has\n    a purpose that is entirely well-defined independent of the\n    application.  Therefore, Subsection 2d requires that any\n    application-supplied function or table used by this function must\n    be optional: if the application does not supply it, the square\n    root function must still compute square roots.)\n\nThese requirements apply to the modified work as a whole.  If\nidentifiable sections of that work are not derived from the Library,\nand can be reasonably considered independent and separate works in\nthemselves, then this License, and its terms, do not apply to those\nsections when you distribute them as separate works.  But when you\ndistribute the same sections as part of a whole which is a work based\non the Library, the distribution of the whole must be on the terms of\nthis License, whose permissions for other licensees extend to the\nentire whole, and thus to each and every part regardless of who wrote\nit.\n\nThus, it is not the intent of this section to claim rights or contest\nyour rights to work written entirely by you; rather, the intent is to\nexercise the right to control the distribution of derivative or\ncollective works based on the Library.\n\nIn addition, mere aggregation of another work not based on the Library\nwith the Library (or with a work based on the Library) on a volume of\na storage or distribution medium does not bring the other work under\nthe scope of this License.\n\n  3. You may opt to apply the terms of the ordinary GNU General Public\nLicense instead of this License to a given copy of the Library.  To do\nthis, you must alter all the notices that refer to this License, so\nthat they refer to the ordinary GNU General Public License, version 2,\ninstead of to this License.  (If a newer version than version 2 of the\nordinary GNU General Public License has appeared, then you can specify\nthat version instead if you wish.)  Do not make any other change in\nthese notices.\n\n  Once this change is made in a given copy, it is irreversible for\nthat copy, so the ordinary GNU General Public License applies to all\nsubsequent copies and derivative works made from that copy.\n\n  This option is useful when you wish to copy part of the code of\nthe Library into a program that is not a library.\n\n  4. You may copy and distribute the Library (or a portion or\nderivative of it, under Section 2) in object code or executable form\nunder the terms of Sections 1 and 2 above provided that you accompany\nit with the complete corresponding machine-readable source code, which\nmust be distributed under the terms of Sections 1 and 2 above on a\nmedium customarily used for software interchange.\n\n  If distribution of object code is made by offering access to copy\nfrom a designated place, then offering equivalent access to copy the\nsource code from the same place satisfies the requirement to\ndistribute the source code, even though third parties are not\ncompelled to copy the source along with the object code.\n\n  5. A program that contains no derivative of any portion of the\nLibrary, but is designed to work with the Library by being compiled or\nlinked with it, is called a \"work that uses the Library\".  Such a\nwork, in isolation, is not a derivative work of the Library, and\ntherefore falls outside the scope of this License.\n\n  However, linking a \"work that uses the Library\" with the Library\ncreates an executable that is a derivative of the Library (because it\ncontains portions of the Library), rather than a \"work that uses the\nlibrary\".  The executable is therefore covered by this License.\nSection 6 states terms for distribution of such executables.\n\n  When a \"work that uses the Library\" uses material from a header file\nthat is part of the Library, the object code for the work may be a\nderivative work of the Library even though the source code is not.\nWhether this is true is especially significant if the work can be\nlinked without the Library, or if the work is itself a library.  The\nthreshold for this to be true is not precisely defined by law.\n\n  If such an object file uses only numerical parameters, data\nstructure layouts and accessors, and small macros and small inline\nfunctions (ten lines or less in length), then the use of the object\nfile is unrestricted, regardless of whether it is legally a derivative\nwork.  (Executables containing this object code plus portions of the\nLibrary will still fall under Section 6.)\n\n  Otherwise, if the work is a derivative of the Library, you may\ndistribute the object code for the work under the terms of Section 6.\nAny executables containing that work also fall under Section 6,\nwhether or not they are linked directly with the Library itself.\n\n  6. As an exception to the Sections above, you may also combine or\nlink a \"work that uses the Library\" with the Library to produce a\nwork containing portions of the Library, and distribute that work\nunder terms of your choice, provided that the terms permit\nmodification of the work for the customer\'s own use and reverse\nengineering for debugging such modifications.\n\n  You must give prominent notice with each copy of the work that the\nLibrary is used in it and that the Library and its use are covered by\nthis License.  You must supply a copy of this License.  If the work\nduring execution displays copyright notices, you must include the\ncopyright notice for the Library among them, as well as a reference\ndirecting the user to the copy of this License.  Also, you must do one\nof these things:\n\n    a) Accompany the work with the complete corresponding\n    machine-readable source code for the Library including whatever\n    changes were used in the work (which must be distributed under\n    Sections 1 and 2 above); and, if the work is an executable linked\n    with the Library, with the complete machine-readable \"work that\n    uses the Library\", as object code and/or source code, so that the\n    user can modify the Library and then relink to produce a modified\n    executable containing the modified Library.  (It is understood\n    that the user who changes the contents of definitions files in the\n    Library will not necessarily be able to recompile the application\n    to use the modified definitions.)\n\n    b) Use a suitable shared library mechanism for linking with the\n    Library.  A suitable mechanism is one that (1) uses at run time a\n    copy of the library already present on the user\'s computer system,\n    rather than copying library functions into the executable, and (2)\n    will operate properly with a modified version of the library, if\n    the user installs one, as long as the modified version is\n    interface-compatible with the version that the work was made with.\n\n    c) Accompany the work with a written offer, valid for at\n    least three years, to give the same user the materials\n    specified in Subsection 6a, above, for a charge no more\n    than the cost of performing this distribution.\n\n    d) If distribution of the work is made by offering access to copy\n    from a designated place, offer equivalent access to copy the above\n    specified materials from the same place.\n\n    e) Verify that the user has already received a copy of these\n    materials or that you have already sent this user a copy.\n\n  For an executable, the required form of the \"work that uses the\nLibrary\" must include any data and utility programs needed for\nreproducing the executable from it.  However, as a special exception,\nthe materials to be distributed need not include anything that is\nnormally distributed (in either source or binary form) with the major\ncomponents (compiler, kernel, and so on) of the operating system on\nwhich the executable runs, unless that component itself accompanies\nthe executable.\n\n  It may happen that this requirement contradicts the license\nrestrictions of other proprietary libraries that do not normally\naccompany the operating system.  Such a contradiction means you cannot\nuse both them and the Library together in an executable that you\ndistribute.\n\n  7. You may place library facilities that are a work based on the\nLibrary side-by-side in a single library together with other library\nfacilities not covered by this License, and distribute such a combined\nlibrary, provided that the separate distribution of the work based on\nthe Library and of the other library facilities is otherwise\npermitted, and provided that you do these two things:\n\n    a) Accompany the combined library with a copy of the same work\n    based on the Library, uncombined with any other library\n    facilities.  This must be distributed under the terms of the\n    Sections above.\n\n    b) Give prominent notice with the combined library of the fact\n    that part of it is a work based on the Library, and explaining\n    where to find the accompanying uncombined form of the same work.\n\n  8. You may not copy, modify, sublicense, link with, or distribute\nthe Library except as expressly provided under this License.  Any\nattempt otherwise to copy, modify, sublicense, link with, or\ndistribute the Library is void, and will automatically terminate your\nrights under this License.  However, parties who have received copies,\nor rights, from you under this License will not have their licenses\nterminated so long as such parties remain in full compliance.\n\n  9. You are not required to accept this License, since you have not\nsigned it.  However, nothing else grants you permission to modify or\ndistribute the Library or its derivative works.  These actions are\nprohibited by law if you do not accept this License.  Therefore, by\nmodifying or distributing the Library (or any work based on the\nLibrary), you indicate your acceptance of this License to do so, and\nall its terms and conditions for copying, distributing or modifying\nthe Library or works based on it.\n\n  10. Each time you redistribute the Library (or any work based on the\nLibrary), the recipient automatically receives a license from the\noriginal licensor to copy, distribute, link with or modify the Library\nsubject to these terms and conditions.  You may not impose any further\nrestrictions on the recipients\' exercise of the rights granted herein.\nYou are not responsible for enforcing compliance by third parties with\nthis License.\n\n  11. If, as a consequence of a court judgment or allegation of patent\ninfringement or for any other reason (not limited to patent issues),\nconditions are imposed on you (whether by court order, agreement or\notherwise) that contradict the conditions of this License, they do not\nexcuse you from the conditions of this License.  If you cannot\ndistribute so as to satisfy simultaneously your obligations under this\nLicense and any other pertinent obligations, then as a consequence you\nmay not distribute the Library at all.  For example, if a patent\nlicense would not permit royalty-free redistribution of the Library by\nall those who receive copies directly or indirectly through you, then\nthe only way you could satisfy both it and this License would be to\nrefrain entirely from distribution of the Library.\n\nIf any portion of this section is held invalid or unenforceable under any\nparticular circumstance, the balance of the section is intended to apply,\nand the section as a whole is intended to apply in other circumstances.\n\nIt is not the purpose of this section to induce you to infringe any\npatents or other property right claims or to contest validity of any\nsuch claims; this section has the sole purpose of protecting the\nintegrity of the free software distribution system which is\nimplemented by public license practices.  Many people have made\ngenerous contributions to the wide range of software distributed\nthrough that system in reliance on consistent application of that\nsystem; it is up to the author/donor to decide if he or she is willing\nto distribute software through any other system and a licensee cannot\nimpose that choice.\n\nThis section is intended to make thoroughly clear what is believed to\nbe a consequence of the rest of this License.\n\n  12. If the distribution and/or use of the Library is restricted in\ncertain countries either by patents or by copyrighted interfaces, the\noriginal copyright holder who places the Library under this License may add\nan explicit geographical distribution limitation excluding those countries,\nso that distribution is permitted only in or among countries not thus\nexcluded.  In such case, this License incorporates the limitation as if\nwritten in the body of this License.\n\n  13. The Free Software Foundation may publish revised and/or new\nversions of the Lesser General Public License from time to time.\nSuch new versions will be similar in spirit to the present version,\nbut may differ in detail to address new problems or concerns.\n\nEach version is given a distinguishing version number.  If the Library\nspecifies a version number of this License which applies to it and\n\"any later version\", you have the option of following the terms and\nconditions either of that version or of any later version published by\nthe Free Software Foundation.  If the Library does not specify a\nlicense version number, you may choose any version ever published by\nthe Free Software Foundation.\n\n  14. If you wish to incorporate parts of the Library into other free\nprograms whose distribution conditions are incompatible with these,\nwrite to the author to ask for permission.  For software which is\ncopyrighted by the Free Software Foundation, write to the Free\nSoftware Foundation; we sometimes make exceptions for this.  Our\ndecision will be guided by the two goals of preserving the free status\nof all derivatives of our free software and of promoting the sharing\nand reuse of software generally.\n\n			    NO WARRANTY\n\n  15. BECAUSE THE LIBRARY IS LICENSED FREE OF CHARGE, THERE IS NO\nWARRANTY FOR THE LIBRARY, TO THE EXTENT PERMITTED BY APPLICABLE LAW.\nEXCEPT WHEN OTHERWISE STATED IN WRITING THE COPYRIGHT HOLDERS AND/OR\nOTHER PARTIES PROVIDE THE LIBRARY \"AS IS\" WITHOUT WARRANTY OF ANY\nKIND, EITHER EXPRESSED OR IMPLIED, INCLUDING, BUT NOT LIMITED TO, THE\nIMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR\nPURPOSE.  THE ENTIRE RISK AS TO THE QUALITY AND PERFORMANCE OF THE\nLIBRARY IS WITH YOU.  SHOULD THE LIBRARY PROVE DEFECTIVE, YOU ASSUME\nTHE COST OF ALL NECESSARY SERVICING, REPAIR OR CORRECTION.\n\n  16. IN NO EVENT UNLESS REQUIRED BY APPLICABLE LAW OR AGREED TO IN\nWRITING WILL ANY COPYRIGHT HOLDER, OR ANY OTHER PARTY WHO MAY MODIFY\nAND/OR REDISTRIBUTE THE LIBRARY AS PERMITTED ABOVE, BE LIABLE TO YOU\nFOR DAMAGES, INCLUDING ANY GENERAL, SPECIAL, INCIDENTAL OR\nCONSEQUENTIAL DAMAGES ARISING OUT OF THE USE OR INABILITY TO USE THE\nLIBRARY (INCLUDING BUT NOT LIMITED TO LOSS OF DATA OR DATA BEING\nRENDERED INACCURATE OR LOSSES SUSTAINED BY YOU OR THIRD PARTIES OR A\nFAILURE OF THE LIBRARY TO OPERATE WITH ANY OTHER SOFTWARE), EVEN IF\nSUCH HOLDER OR OTHER PARTY HAS BEEN ADVISED OF THE POSSIBILITY OF SUCH\nDAMAGES.\n\n		     END OF TERMS AND CONDITIONS\n\n           How to Apply These Terms to Your New Libraries\n\n  If you develop a new library, and you want it to be of the greatest\npossible use to the public, we recommend making it free software that\neveryone can redistribute and change.  You can do so by permitting\nredistribution under these terms (or, alternatively, under the terms of the\nordinary General Public License).\n\n  To apply these terms, attach the following notices to the library.  It is\nsafest to attach them to the start of each source file to most effectively\nconvey the exclusion of warranty; and each file should have at least the\n\"copyright\" line and a pointer to where the full notice is found.\n\n    <one line to give the library\'s name and a brief idea of what it does.>\n    Copyright (C) <year>  <name of author>\n\n    This library is free software; you can redistribute it and/or\n    modify it under the terms of the GNU Lesser General Public\n    License as published by the Free Software Foundation; either\n    version 2.1 of the License, or (at your option) any later version.\n\n    This library is distributed in the hope that it will be useful,\n    but WITHOUT ANY WARRANTY; without even the implied warranty of\n    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU\n    Lesser General Public License for more details.\n\n    You should have received a copy of the GNU Lesser General Public\n    License along with this library; if not, write to the Free Software\n    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA\n\nAlso add information on how to contact you by electronic and paper mail.\n\nYou should also get your employer (if you work as a programmer) or your\nschool, if any, to sign a \"copyright disclaimer\" for the library, if\nnecessary.  Here is a sample; alter the names:\n\n  Yoyodyne, Inc., hereby disclaims all copyright interest in the\n  library `Frob\' (a library for tweaking knobs) written by James Random Hacker.\n\n  <signature of Ty Coon>, 1 April 1990\n  Ty Coon, President of Vice\n\nThat\'s all there is to it!\n\n\n','ross',0,0,0.00,'','','','n');





CREATE TABLE tiki_forum_attachments (
"attId" integer NOT NULL default nextval('tiki_forum_attachments_seq') unique not null,
"threadId" integer NOT NULL default '0',
"qId" integer NOT NULL default '0',
"forumId" integer NOT NULL default '0',
"filename" varchar(250) NOT NULL default '',
"filetype" varchar(250) NOT NULL default '',
"filesize" integer NOT NULL default '0',
"data" text,
"dir" varchar(200) NOT NULL default '',
"created" integer NOT NULL default '0',
"path" varchar(250) NOT NULL default '',
PRIMARY KEY ("attId")
) ;











CREATE TABLE tiki_forum_reads (
"user" varchar(200) NOT NULL default '',
"threadId" integer NOT NULL default '0',
"forumId" integer NOT NULL default '0',
"timestamp" integer NOT NULL default '0',
PRIMARY KEY ("threadId","user")
) ;











CREATE TABLE tiki_forums (
"forumId" integer NOT NULL default nextval('tiki_forums_seq') unique not null,
"name" varchar(200) NOT NULL default '',
"description" text NOT NULL,
"created" integer NOT NULL default '0',
"lastPost" integer NOT NULL default '0',
"threads" integer NOT NULL default '0',
"comments" integer NOT NULL default '0',
"controlFlood" varchar(1) NOT NULL default '',
"floodInterval" integer NOT NULL default '0',
"moderator" varchar(200) NOT NULL default '',
"hits" integer NOT NULL default '0',
"mail" varchar(200) NOT NULL default '',
"useMail" varchar(1) NOT NULL default '',
"usePruneUnreplied" varchar(1) NOT NULL default '',
"pruneUnrepliedAge" integer NOT NULL default '0',
"usePruneOld" varchar(1) NOT NULL default '',
"pruneMaxAge" integer NOT NULL default '0',
"topicsPerPage" integer NOT NULL default '0',
"topicOrdering" varchar(100) NOT NULL default '',
"threadOrdering" varchar(100) NOT NULL default '',
"section" varchar(200) NOT NULL default '',
"topics_list_replies" varchar(1) NOT NULL default '',
"topics_list_reads" varchar(1) NOT NULL default '',
"topics_list_pts" varchar(1) NOT NULL default '',
"topics_list_lastpost" varchar(1) NOT NULL default '',
"topics_list_author" varchar(1) NOT NULL default '',
"vote_threads" varchar(1) NOT NULL default '',
"moderator_group" varchar(200) NOT NULL default '',
"approval_type" varchar(20) NOT NULL default '',
"outbound_address" varchar(1) NOT NULL default '',
"outbound_from" varchar(1) NOT NULL default '',
"inbound_address" varchar(1) NOT NULL default '',
"topic_smileys" varchar(1) NOT NULL default '',
"ui_avatar" varchar(1) NOT NULL default '',
"ui_flag" varchar(1) NOT NULL default '',
"ui_posts" varchar(1) NOT NULL default '',
"ui_email" varchar(1) NOT NULL default '',
"ui_online" varchar(1) NOT NULL default '',
"topic_summary" varchar(1) NOT NULL default '',
"show_description" varchar(1) NOT NULL default '',
"att" varchar(80) NOT NULL default '',
"att_store" varchar(4) NOT NULL default '',
"att_store_dir" varchar(250) NOT NULL default '',
"att_max_size" integer NOT NULL default '0',
"ui_level" varchar(1) NOT NULL default '',
"forum_password" varchar(32) NOT NULL default '',
"forum_use_password" varchar(1) NOT NULL default '',
"inbound_pop_server" varchar(250) NOT NULL default '',
"inbound_pop_port" integer NOT NULL default '0',
"inbound_pop_user" varchar(200) NOT NULL default '',
"inbound_pop_password" varchar(80) NOT NULL default '',
PRIMARY KEY ("forumId")
) ;






INSERT INTO tiki_forums VALUES (1,'Test Forum','Test Forum',1038717239,1044982298,2,4,'n',120,'ross',16,'','n','n',2592000,'n',2592000,20,'commentDate_desc','commentDate_desc','','y','y','y','y','y','y','','all_posted','','','n','y','y','n','n','y','n','y','att_no','','',1000000,'n','','n','',0,'','');





CREATE TABLE tiki_forums_queue (
"qId" integer NOT NULL default nextval('tiki_forums_queue_seq') unique not null,
"object" varchar(32) NOT NULL default '',
"parentId" integer NOT NULL default '0',
"forumId" integer NOT NULL default '0',
"timestamp" integer NOT NULL default '0',
"user" varchar(200) NOT NULL default '',
"title" varchar(240) NOT NULL default '',
"data" text NOT NULL,
"type" varchar(60) NOT NULL default '',
"hash" varchar(32) NOT NULL default '',
"topic_smiley" varchar(80) NOT NULL default '',
"topic_title" varchar(240) NOT NULL default '',
"summary" varchar(240) NOT NULL default '',
PRIMARY KEY ("qId")
) ;











CREATE TABLE tiki_forums_reported (
"threadId" integer NOT NULL default '0',
"forumId" integer NOT NULL default '0',
"parentId" integer NOT NULL default '0',
"user" varchar(200) NOT NULL default '',
"timestamp" integer NOT NULL default '0',
"reason" varchar(250) NOT NULL default '',
PRIMARY KEY ("threadId")
) ;











CREATE TABLE tiki_galleries (
"galleryId" integer NOT NULL default nextval('tiki_galleries_seq') unique not null,
"name" varchar(80) NOT NULL default '',
"description" text NOT NULL,
"created" integer NOT NULL default '0',
"lastModif" integer NOT NULL default '0',
"visible" varchar(1) NOT NULL default '',
"theme" varchar(60) NOT NULL default '',
"user" varchar(200) NOT NULL default '',
"hits" integer NOT NULL default '0',
"maxRows" integer NOT NULL default '0',
"rowImages" integer NOT NULL default '0',
"thumbSizeX" integer NOT NULL default '0',
"thumbSizeY" integer NOT NULL default '0',
"public" varchar(1) NOT NULL default '',
PRIMARY KEY ("galleryId")




) ;






INSERT INTO tiki_galleries VALUES (1,'A Test Image Gallery','A Test Image Gallery',1040549893,1040868478,'y','','ross',3,10,6,80,80,'n');





CREATE TABLE tiki_galleries_scales (
"galleryId" integer NOT NULL default '0',
"xsize" integer NOT NULL default '0',
"ysize" integer NOT NULL default '0',
PRIMARY KEY ("xsize","galleryId","ysize")
) ;











CREATE TABLE tiki_games (
"gameName" varchar(200) NOT NULL default '',
"hits" integer NOT NULL default '0',
"votes" integer NOT NULL default '0',
"points" integer NOT NULL default '0',
PRIMARY KEY ("gameName")
) ;











CREATE TABLE tiki_group_inclusion (
"groupName" varchar(30) NOT NULL default '',
"includeGroup" varchar(30) NOT NULL default '',
PRIMARY KEY ("includeGroup","groupName")
) ;











CREATE TABLE tiki_history (
"pageName" varchar(160) NOT NULL default '',
"version" integer NOT NULL default '0',
"lastModif" integer NOT NULL default '0',
"user" varchar(200) NOT NULL default '',
"ip" varchar(15) NOT NULL default '',
"comment" varchar(200) NOT NULL default '',
"data" text,
"description" varchar(200) NOT NULL default '',
PRIMARY KEY ("pageName","version")
) ;






INSERT INTO tiki_history VALUES ('HomePage',1,1038712078,'system','0.0.0.0','Tiki initialization','','');
INSERT INTO tiki_history VALUES ('HomePage',2,1038793725,'ross','192.168.1.2','','LisasPage : About Lisa Walford, my honey.','');
INSERT INTO tiki_history VALUES ('HomePage',3,1038793754,'ross','192.168.1.2','','LisasPage : About Lisa Walford, my honey.\r\n\r\nNoHTMLCodeIsNeeded','');
INSERT INTO tiki_history VALUES ('HomePage',4,1038794026,'ross','192.168.1.2','','LisasPage : About Lisa Walford, my honey.\r\n\r\n((NoHTMLCodeIsNeeded))','');
INSERT INTO tiki_history VALUES ('HomePage',5,1038794132,'ross','192.168.1.2','','LisasPage : About Lisa Walford, my honey.\r\n\r\n((NoHTMLCodeIsNeeded))','');
INSERT INTO tiki_history VALUES ('HomePage',6,1038940707,'ross','192.168.1.2','','((LisasPage)) : About Lisa Walford, my honey.\r\n\r\n((NoHTMLCodeIsNeeded))','');
INSERT INTO tiki_history VALUES ('HomePage',7,1038971383,'ross','192.168.1.2','','((LisasPage)) : About Lisa Walford, my honey.\r\n\r\n((JobHunting))','');
INSERT INTO tiki_history VALUES ('HomePage',8,1038974004,'ross','192.168.1.2','','((JobHunting))','');
INSERT INTO tiki_history VALUES ('HomePage',9,1039232946,'ross','192.168.1.2','','((JobHunting))\r\n\r\n||row1-col1|row1-col2|row1-col3||row2-col1|row2-col2|row3-col3||','');
INSERT INTO tiki_history VALUES ('HomePage',10,1039233364,'ross','192.168.1.2','','((JobHunting))\r\n\r\n||row1-col1|row1-col2|row1-col3||row2-col1|row2-col2|row3-col3||\r\n\r\n||r1-c1|r1-c2||r2c1c2||r3-c1|r3-c2|r3c3||\r\n','');
INSERT INTO tiki_history VALUES ('HomePage',11,1039234052,'ross','192.168.1.2','','((JobHunting))\r\n\r\n||row1-col1|row1-col2|row1-col3||row2-col1|row2-col2|row3-col3||\r\n\r\n||r1-c1|r1-c2||r2c1c2||r3-c1|r3-c2|r3c3||\r\n','');
INSERT INTO tiki_history VALUES ('HomePage',12,1039617016,'ross','192.168.1.2','','((JobHunting))\r\n\r\n||row1-col1|row1-col2|row1-col3||row2-col1|row2-col2|row3-col3||\r\n\r\n||r1-c1|r1-c2||r2c1c2||r3-c1|r3-c2|r3c3||\r\n\r\nTestWiki','');
INSERT INTO tiki_history VALUES ('HomePage',13,1039617624,'ross','192.168.1.2','','((JobHunting))\r\n\r\n||row1-col1|row1-col2|row1-col3||row2-col1|row2-col2|row3-col3||\r\n\r\n||r1-c1|r1-c2||r2c1c2||r3-c1|r3-c2|r3c3||\r\n\r\n((Where\'sTheSearchBox?))','');
INSERT INTO tiki_history VALUES ('HomePage',14,1039617646,'ross','192.168.1.2','','((JobHunting))\r\n\r\n||row1-col1|row1-col2|row1-col3||row2-col1|row2-col2|row3-col3||\r\n\r\n||r1-c1|r1-c2||r2c1c2||r3-c1|r3-c2|r3c3||\r\n\r\n((Where\'sTheSearchBox))','');
INSERT INTO tiki_history VALUES ('HomePage',15,1039617668,'ross','192.168.1.2','','((JobHunting))\r\n\r\n||row1-col1|row1-col2|row1-col3||row2-col1|row2-col2|row3-col3||\r\n\r\n||r1-c1|r1-c2||r2c1c2||r3-c1|r3-c2|r3c3||\r\n\r\n((WhereIsTheSearchBox))','');
INSERT INTO tiki_history VALUES ('HomePage',16,1039618839,'ross','192.168.1.2','','OK, so where\'s the damn search dialog box!\r\n\r\n[http://research.salutia.com/tiki/] has it, so why don\'t I?\r\n\r\nI\'ve turned it on in admin, but no go!\r\n\r\nTable test:\r\n\r\n||row1-col1|row1-col2|row1-col3||row2-col1|row2-col2|row3-col3||\r\n\r\n||r1-c1|r1-c2||r2c1c2||r3-c1|r3-c2|r3c3||\r\n\r\n((JobHunting))\r\n','');
INSERT INTO tiki_history VALUES ('HomePage',17,1039739934,'ross','192.168.1.2','','OK, so where\'s the damn search dialog box!\r\n\r\n[http://research.salutia.com/tiki/] has it, so why don\'t I?\r\n\r\nI\'ve turned it on in admin, but no go!\r\n\r\nTable test:\r\n\r\n||esta es|una prubea||lala||1|2|3||\r\n\r\n||row1-col1|row1-col2|row1-col3||row2-col1|row2-col2|row3-col3||\r\n\r\n||r1-c1|r1-c2||r2c1c2||r3-c1|r3-c2|r3c3||\r\n\r\n((JobHunting))\r\n','');
INSERT INTO tiki_history VALUES ('HomePage',18,1039748495,'ross','192.168.1.2','','OK, so where\'s the damn search box!\r\n\r\n[http://research.salutia.com/tiki/] has it, so why don\'t I?\r\n\r\nI\'ve turned it on in admin, but no go!\r\n\r\nTable test:\r\n\r\n||esta es|una prubea||lala||1|2|3||\r\n\r\n||row1-col1|row1-col2|row1-col3||row2-col1|row2-col2|row3-col3||\r\n\r\n||r1-c1|r1-c2||r2c1c2||r3-c1|r3-c2|r3c3||\r\n\r\n((JobHunting))\r\n','');
INSERT INTO tiki_history VALUES ('HomePage',19,1039768550,'ross','192.168.1.2','','OK, so where\'s the damn search box!\r\n\r\n[http://research.salutia.com/tiki/] has it, so why don\'t I?\r\n\r\nI\'ve turned it on in admin, but no go!\r\n\r\nTable test:\r\n\r\n||esta es|una prubea||lala||1|2|3||\r\n\r\n||row1-col1|row1-col2|row1-col3||row2-col1|row2-col2|row3-col3||\r\n\r\n||r1-c1|r1-c2||r2c1c2||r3-c1|r3-c2|r3c3||\r\n\r\n((JobHunting))\r\n\r\nStem the Tide!','');
INSERT INTO tiki_history VALUES ('JobHunting',1,1038971411,'ross','192.168.1.2','','[http://losangeles.craigslist.org/eng/]','');
INSERT INTO tiki_history VALUES ('HomePage',20,1039947392,'ross','192.168.1.2','','Search \"Entire Site\" now works.\r\n\r\nBut the results are in section, then reverse chron order.\r\n\r\nThe results really should be in relevency order across all sections.\r\n\r\n\r\nTable test:\r\n\r\n||esta es|una prubea||lala||1|2|3||\r\n\r\n||row1-col1|row1-col2|row1-col3||row2-col1|row2-col2|row3-col3||\r\n\r\n||r1-c1|r1-c2||r2c1c2||r3-c1|r3-c2|r3c3||\r\n\r\n((JobHunting))\r\n\r\nStem the Tide!','');
INSERT INTO tiki_history VALUES ('HomePage',21,1040866181,'ross','192.168.1.2','','Search \"Entire Site\" now works.\r\n\r\nBut the results are in section, then reverse chron order.\r\n\r\nThe results really should be in relevency order across all sections.\r\n\r\n\r\nTable test:\r\n\r\n||esta es|una prubea||lala||1|2|3||\r\n\r\n||row1-col1|row1-col2|row1-col3||row2-col1|row2-col2|row3-col3||\r\n\r\n||r1-c1|r1-c2||r2c1c2||r3-c1|r3-c2|r3c3||\r\n\r\n((JobHunting))\r\n\r\nStem the Tide!\r\n\r\nAdding an entry to test the full text search function.\r\n\r\nHere are some search terms:\r\n\r\nJobHunting\r\ntest\r\ntiki\r\n','');
INSERT INTO tiki_history VALUES ('HomePage',22,1040866201,'ross','192.168.1.2','','Search \"Entire Site\" now works.\r\n\r\nBut the results are in section, then reverse chron order.\r\n\r\nThe results really should be in relevency order across all sections.\r\n\r\n\r\nTable test:\r\n\r\n||esta es|una prubea||lala||1|2|3||\r\n\r\n||row1-col1|row1-col2|row1-col3||row2-col1|row2-col2|row3-col3||\r\n\r\n||r1-c1|r1-c2||r2c1c2||r3-c1|r3-c2|r3c3||\r\n\r\n((JobHunting))\r\n\r\nStem the Tide!\r\n\r\nAdding an entry to test the full text search function.\r\n\r\nHere are some search terms:\r\n\r\nJobHunting\r\ntest\r\ntiki\r\n\r\nNewPage\r\n','');
INSERT INTO tiki_history VALUES ('HomePage',23,1041569358,'luis','192.168.1.2','','Dates and times are now localized!\r\n\r\nTiki rocks!\r\n\r\n((JobHunting))\r\n','');
INSERT INTO tiki_history VALUES ('HomePage',24,1041800610,'ross','192.168.1.2','','Dates and times are now localized!\r\n\r\nTiki rocks!\r\n\r\n((JobHunting))\r\n\r\ndate test','');
INSERT INTO tiki_history VALUES ('HomePage',25,1041822095,'ross','192.168.1.2','','Dates and times are now localized!\r\n\r\nTiki rocks!\r\n\r\n((JobHunting))\r\n\r\ntest','');
INSERT INTO tiki_history VALUES ('HomePage',26,1042143529,'ross','192.168.1.2','','Dates and times are now localized!\r\n\r\nTiki rocks!\r\n\r\nJobHunting\r\n','');
INSERT INTO tiki_history VALUES ('HomePage',27,1042143584,'ross','192.168.1.2','','Dates and times are now localized!\r\n\r\nThis server is running in PST, but is reporting time in EST.\r\n\r\nJobHunting\r\n','');
INSERT INTO tiki_history VALUES ('HomePage',28,1044981129,'ross','192.168.1.2','','Dates and times are now localized!\r\n\r\nThis server is running in PST, but is reporting time in EST.\r\n\r\nJobHunting\r\n\r\nLast edited at 8:30AM PST','');
INSERT INTO tiki_history VALUES ('HomePage',29,1045204033,'ross','192.168.1.2','','Dates and times are now localized!\r\n\r\nThis server is running in PST, but is reporting time in EST.\r\n\r\nJobHunting\r\n\r\nLast edited at 8:30AM PST\r\n\r\n[http://seedcuisine.com/|Seedcuisine] rocks!\r\n\r\n','');
INSERT INTO tiki_history VALUES ('HomePage',30,1045204274,'ross','192.168.1.2','','Dates and times are now localized!\r\n\r\nThis server is running in PST, but is reporting time in EST.\r\n\r\nJobHunting\r\n\r\nLast edited at 8:30AM PST\r\n\r\n[http://seedcuisine.com/|Seedcuisine] rocks!\r\n\r\n__text__\'\'text\'\'===text===||r1c1|r1c2||r2c1|r2c2||[http://example.com|desc]((page))\r\n\r\n__This will be bold__','');





CREATE TABLE tiki_hotwords (
"word" varchar(40) NOT NULL default '',
"url" varchar(255) NOT NULL default '',
PRIMARY KEY ("word")
) ;











CREATE TABLE tiki_html_pages (
"pageName" varchar(40) NOT NULL default '',
"content" text,
"refresh" integer NOT NULL default '0',
"type" varchar(1) NOT NULL default '',
"created" integer NOT NULL default '0',
PRIMARY KEY ("pageName")
) ;











CREATE TABLE tiki_html_pages_dynamic_zones (
"pageName" varchar(40) NOT NULL default '',
"zone" varchar(80) NOT NULL default '',
"type" varchar(2) NOT NULL default '',
"content" text NOT NULL,
PRIMARY KEY ("zone","pageName")
) ;











CREATE TABLE tiki_images (
"imageId" integer NOT NULL default nextval('tiki_images_seq') unique not null,
"galleryId" integer NOT NULL default '0',
"name" varchar(40) NOT NULL default '',
"description" text NOT NULL,
"created" integer NOT NULL default '0',
"user" varchar(200) NOT NULL default '',
"hits" integer NOT NULL default '0',
"path" varchar(255) NOT NULL default '',
PRIMARY KEY ("imageId")





) ;






INSERT INTO tiki_images VALUES (1,1,'Google logo','http://www.google.com/logos/Logo_40wht.gif',1040549952,'ross',3,'');
INSERT INTO tiki_images VALUES (2,1,'test','Adding an entry to test the full text search function.\r\n\r\nHere are some search terms:\r\n\r\nJobHunting\r\ntest\r\ntiki\r\n',1040868478,'ross',1,'');





CREATE TABLE tiki_images_data (
"imageId" integer NOT NULL default '0',
"xsize" integer NOT NULL default '0',
"ysize" integer NOT NULL default '0',
"type" varchar(1) NOT NULL default '',
"filesize" integer NOT NULL default '0',
"filetype" varchar(80) NOT NULL default '',
"filename" varchar(80) NOT NULL default '',
"data" text,
PRIMARY KEY ("imageId","xsize","type","ysize")

) ;






INSERT INTO tiki_images_data VALUES (1,0,0,'o',3845,'image/gif','Logo_40wht.gif','GIF89a€\05\0÷ü\0ñğñíìíÑÑÓÉÉËããäààáÔÔÕÎÎÏ\"j¿ÁÆ\0>Å\0/š,?(„<¸6§&s1ƒ B‹;z/QœŠ’£˜ ±GÖEÌPÚ\nFÀMÉF²SÆ#XÃ+Tª8b·<UŠe}¯TgŒo}˜z‰¦²µ»·ºÀÑÔÚÎĞÔÉËÏìíïéêìWâZ×;mÊM|ÏUy½lŒÄ‚˜¿hãeß(hÖ-rã?€ç\\‰Ö?§Ğ¥³Ê¥«µ¸¾ÈÀÅÍŞãëæéîPŒè^™ïg”ØpŸãy¡İŠ®åœ¸ã¦»ÙºÅÕËÖæÈÎ×¤¨®®²¸âæìóõøğòõãåè¬ÂàêíñÕÚßÇÉËÌÌÌôõöëìíéêëèéêåæçÄÅÆíïğ÷øøïğğİŞŞÉÊÊY|e-p-v½vPuP˜Ò˜?º?q‡q¯Ï¯‘œ‘ÚèÚÊÏÊğõğÑÕÑÍÑÍØÛØÕØÕÜŞÜ÷ø÷çèçĞÑĞÁÂÁjm(—\'7©6>°=I¸HgÁf€ÏW‰VÆÆÃóóñööõŞŞİëêãñŞ„ÑÊ©ôÌ-õØWñá›ÔÆ?éá¿ÏÍÄÉÈÄÆšá±	÷Æ\ríÀÕ«Ë­>Æ°^’‚KŸ“i×ÔÉáßØèæß¤‹k²‹{`Šu/¿¶™ŞÛÑóñì®¢”öõôõôóáàßÖÕÔãÍ¹èàÚµ¬¦¾¶²ÅÁ¿èæåÏÂ¼µ²±ìéèçäãÜÙØè¨›òèæŞQ9álWä‹zÏ¡™à¸±èÒÎäÙ×Ü%Û-á1Ô8!ã>$ÊŒ‚ª?‰Í­¨Í¹¶ÕÎÍ½Ğ!£¹%Ç0Ë3Ã:)¶E8ÂWI¸bW¸}v‰`[›yuÜÔÓ²\0¥\0’\rÎ¬?\"§2&}1*?F=r\rÔÆÅĞÌÌfffÔÑÑŞÜÜüûûæååûûûùùù÷÷÷ôôôóóóïïïëëëéééçççäääâââßßßÜÜÜÚÚÚÙÙÙ×××ÓÓÓÈÈÈÇÇÇÆÆÆÄÄÄ»»»\0\0\0\0\0\0\0\0\0\0\0\0!ù\0\0ü\0,\0\0\0\0€\05\0\0ÿ\0ù	H° Áƒ*\\È°¡Ã‡#JœH±¢Å‹3jÜÈ±£Ç? CŠI²¤É“(Sª\\É²¥Ë?äÌÉ4Gî¥ÍŒæÎJ§.İ/4mÆ<çÅº£^Î½yĞ\\›5hĞüÙcf!s-czIçDÉ‘H¤(ÒEİtJ™|\r >eàdä¥fÊ˜è”y\"†_\\ÜRI\0«Ko’«óeK\Z?cÔ(à\nV”äÎ8bƒƒˆ%åB¿C1ƒƒ`/’¬³¬–ºuğÒôÉ£ÆŠ¼vé.›4——ˆç`â€¯@<y)JhpĞ€„\0èìŞô²®Àc=jîÑs§N7IŞ?ˆhÿ(A?¼x¶¸Ûò.=L¨¯²÷—çªËÆ¯Ş–î&‘ãFpÂĞvà ;[ÄS?#$0;¹©uÎ±ÍÆFî¤#İHç¨ƒœ€ <d±Î ê¨3È:î°„\0ÌC€}\ruÔQJ?X?LF‚NR‰DNL3ÍôZ‡y°¡];WØ•“QG©‘9„  „$„q@=d\0!@åDÈY´<í|?@™•²-xêÂ\0é “$?9?ÒÈ\"„¦¢Ê ?ıI$Xd¡Å£,\0ñ`˜Ñl„1•2?SK*yò‚(Pá”\Z?§J¤Ù‰TpÏò¸ÿC×ˆ¦³N\0YğÅç,ÉÛ.·0ÓL3¾Øb.¼¬£Pt3È\"‰PR	%“$¢#›|‘NZ€3…EA9¼àÁ3lA‡|`ªi;Ë¢‹-Ì,Œ±ÂÈ€e§š*9„PA„0q?=¬3gAZ]áZ?Ê‚K3Â|Ï8ßDãK0Àc&!Jy!Š\"•0\"Ê)³8b‰$‰L‚;ƒ µ¡/È°Ã1Ôpƒ1XĞğÈÁ.¦VÌãÎªÜâ1ÔÌR4ÁüâÌ)!ö‹ªDæ’„\Z `°\0\"Ä$MZ­“2Ñ\0òĞc\04½ø‚Í*[(›&ŠPâH<l·ÿ-N%’HBÉ+v(kâ\r3¼ôˆ€˜`Ï<òØ3t¦ô¼s\n.Ç°r=õÜñÌ/ÕLóÎ:WU”ŸApàõ»U:”Y\0´ØÒ\r*”Çøpc“Ì*ïdqÊ!‘`R<ÁÉSÏ#”H‰\'”ÓÎA<àƒ<Ø×c< ?…ôÜqyõÈ“0ÓÌ£>èwS?7Ş´bÇ çT„N\0Eà „!;èx—^?eÃåƒG9Ş?¢È°†4êD(‚¤@à‚¶@€Ê­ìy¤(?l0?1!o(AÀ\0À\0q¸ÜÁ¾qe¬Â\0õ Ç<èQŒgxƒÃ0Æ;ÿ¾à…Š¢E¸?\ZPÿ €ÑL.la\rRŒ)îpĞäÑ\n`ô¢\ZÜƒ#‘ˆN¬bLhG\0Øñz°b’€Ä%¬°!l`\\°‡<\nP\0zœÀh@	¬ \07\\. †-²‹ØãÂx†3¢±ŠVX¡XG\'B+Øa7Ø\0Hp?ÿAQIMÊ‰:\\?‹`\\ƒ—Ì¤OÖA€·‘Î°`…\"&ñ‰UX?åXM:P€;\0 Àƒô?&Àª:\00\0?„á†lôaa#¤(†0l¡ŒhˆC\0pd¬\0P?N^¡;¨A0.Ğ£„@åº°ÿ‚`a@\05nñ‹kL#Ûé—Ø!oÃ¤…\"\"Ñ	R¼+7æHG;è±2H|3 ?(+p€ÛÀô?A×ú‡ÍËááŞ,)¢‘‹Y¨m‡\n*?(Bˆ,¤Àp@3\rğŸû<!	CÈAŠ0¾ À¶ğ…µcŠCÄÆC#šŠbBSYÈ\'?¨F$Â£«`‚.à€¼kì\0Ã0?}X¡¥ÚÄ¢ŒjHƒ›$<Òã¢tÔÅ~\0ƒ2€?„à\0	k§@Ì¡#è@3Â\r`z€#ª`ÄC=BTs¨cÅ ¢7˜@Š–qâø¸ç&\0ÿ \"\0,`Q8à0@‹ÜáƒL€ˆ´GóªaüâÚğ%™ÜÑvèJ RD6?là\nx@gq£sxAì€Gn@Í&À\nÏèÅ1^)€w+äH‡R{\rnÀbpD¦>FkUÓn“à&`±?}P`x\0äÀ`<À‡´\\Lé•¾ˆ 4îQx¸hTšHFİQ(àÅ8eR¨ó²£\0ëU\02|?h`´BÂŠØI;Ş˜ÆnÂú\0+¦u#(ñ	Rìú0A	B?8?Æ´68¸4¬@6 PFÿ~Ò.E\\€x$àx±|?É«å\nP/¨É}àƒÊX³6öqOŒª£¸È†¼\nJDBV\0Ãc2ˆB\\ûàÂ]ß›ì@ƒ¶æ†ÁğÌÁRQzi ñclt£·‰™L–„“A°øx€0à?¼ÙHè&mğ\0¯‰Ú\n«ØF5ªqkÊÌ²Êà+ôŠ}?àÆD=Êñ…?€rÄ>ÈÌ8 0 B`WL¶ÃBãƒ¶+´¢Õ86š‘‹qdáê0….v‘âˆ¸¦:hBğ\0\\ IˆB\0Öñ…u´;p\0ÀƒkÚÚÚÿ¸F5œa?Æ‚Ëˆn¿ï`?;„œğ$,!ŠÂbÊ °äè;´£:@HÙ\Zæz¼ƒíˆCÆ †3\\9€EÊ«ñãgƒ‘Œ\0˜u‘ŒÆõx¨À\"@qx\09ˆ;@ğ??ÀúÀ<vÇŠipã\ZÉp4ñŒa€C\0  Ú´·\nL€‚?¨„%*Q‰PÜA\0“kÑ:àJ\0ô`Ë±¸,;Ø!\r?8ƒê‘7Ôã\0}ç†7®q\rk\\£¹`Ç¾z…‘}\\\0÷ÀH0‚H@\0?f°ƒ¼ûš,rO~V#\ZÂxÅ)À°C\nÛ?mšÇÿ¬ ˆU?Â¡ÈÄM¤y\08A\n9°A8 ?Tü\ZhB@íáCÃõ â—û0Ó`€¬0P8†%gçƒ?ğzap÷0\0°aÈÃ6nƒCò S×}d\0ö? ÁÁGÃ±À\'ê@#`Pà?`€‚Ãáì°Q@3À|=?\0ûÀ#Pq\n@?<õ€xôå`åĞ@Vpùp„D¸\Z¼‡™1Lâ6ˆW‚æq‚(È ë?„@ƒ\0[\0ñ ğğê¡FÚ:\0ìPÆ!\0MÔµq\'A@= ó:a€û@y¶\0?„ÅÁGïÿĞ·YĞA0ˆ@\n¨‚º¶1A\0pR 6\'˜‡î`tÖõ\" Ó)WĞ‰YĞí?¥È\'q6Y[ñ¸ÂŠ¹²q\0Ğ\0°5À8dƒx\0ø°Ã\0#``°®ˆ‹WP†¶’i8ïĞ Vq…š¨3R‹­XŠ_?‹g¡%“¥é0\0\0\0ƒğ0Ü2\r##åxhâW Fp\r`/Daå@\0/h\'ĞÆx``ÚÒŒC$W??¸Ø\'\r—Z±2#“&8E¡9YW‚$4aW@Aà-´a¥·ylDğLT„Y€Q¼FD‚àHc³k)‘QT\0Tâ@ø€0@†Ã*à€wO¤H	(§%¾Å%P”‚kRsÅ—š””j‘Qv`\0Á–g2?eÒOJ0À… `àì¤•L0qu$0mç/ÀÊç #ï°Sli^ğå@V`@|!0\"`)À7Á±—ê Y}ù\0>Vp™w?CÈƒ‡Y´,÷™-qÃÔ„ ó‹ç¡@ß7†dšBÁ‘_\0!Ÿ(?{hŠéÈš7a^é\0\0·Â¯ˆ]@á€¶™9!•C?1œl¹$Ê‰œÌy\0;');
INSERT INTO tiki_images_data VALUES (2,700,500,'o',3317,'image/x-png','background.png','‰PNG\r\n\Z\n\0\0\0\rIHDR\0\0¼\0\0ô\0\0\0P;iˆ\0\0\0bKGD\0ÿ\0ÿ\0ÿ ½§“\0\0\0	pHYs\0\0\0\0Òİ~ü\0\0\0tIMEÒ!(¶Y=ı\0\0‚IDATxœíÜQn#7EQMàı/5;’?“»l?µØİ,òœøÃ?/dÖûñ÷_>\0`?îş÷v÷\0\0£ü}÷°¸?îş\0€D\0\r\0@D4\0\0Ñ\0\0DŞkê \0vtàäÒA”ô4°8;\r0Š†’^‡h\0àTzzB\0Ñ\0\0DD\0\r\0@D4\0\0Ñ\0\0D,BB®ÃF²ÓÀÂ1”ô4‰€İèiò¦\0ˆˆ\0 \"\Z\0€ˆh\0\0\"¢\0ˆˆ\0 bÜ‰+¹hÌNWR¨PÒÓô \Z\0n§§éÁ›\0 \"\Z\0€ˆh\0\0\"¢\0ˆˆ\0 \"\Z\0€ˆq\'ˆ±Ó€j„’†÷D@IOÃ{Ş4\0\0Ñ\0\0DD\0\r\0@D4\0\0Ñ\0\0DŒ;MÈu8\03²Ó0!%=\r7\r@z\ZJ×õ´h\0€Ö®ëi!€ˆh\0\0\"¢\0ˆ¼yZÄãáY:\0ßó’ÇãáY:|BOÃ/Œ;…|rÀ–|@Â/|ÓòÉ%=\r\rÀ+ô4”ÖìiÑ\0\0Ã­ÙÓN.€ˆh\0\0\"¢\0ˆˆ\0 \"\Z\0€Hßq§5¯Y\0`Z}O.›¶œMOgé\r@IOCIO \Z\0Ø?ÀCH\0 \"\Z\0€ˆh\0\0\"¢\0ˆˆ\0 2pÜÉ5\0¬làÉ¥k(éi`v\Zàlz\ZJzºÑ\0À-ôt?B\0Ñ\0\0DD\0\r\0@D4\0\0Ñ\0\0D.BB#Äf§?=ie(éi¾\"\Z\0øIOóo\Z\0€ˆh\0\0\"¢\0ˆˆ\0 \"\Z\0€ˆh\0\0\"Æ?˜„ëp€ÙÙi`âJzš‰ˆ€™éi&âM\0\r\0@D4\0\0Ñ\0\0DD\0\r\0@Ä¸_p Àÿì4ğA	%=Í¦DÀ³ô4›ò¦\0ˆˆ\0 \"\Z\0€ˆh\0\0\"¢\0ˆˆ\0 bÜ©×á\0ÜÆNC/\nJz\Z® \Z€èi(\rîiÑ\0\0«\ZÜÓB\0Ñ\0\0DD\0\r\0@D4\0\0ãN¯s Àœ\\¾NuAIOÃjDp=\r¥Æ=-\Z\0àJ?{ÚCH\0 \"\Z\0€ˆh\0\0\"¢\0ˆˆ\0 ²ä¸Sãk\0˜Ö’\'—ëe¡§?—,\r@IOCIO§D\0›ÓÓ)!€ˆh\0\0\"¢\0ˆˆ\0 \"\Z\0€È5ãN®Y\0 ½kN.]³@IO?Øi€éi(ééI‰\0f£§\'å!$\0\r\0@D4\0\0Ñ\0\0DD\0\r\0@äšEHhÄ?8@ÍN¼#£¡¤§\r\0Dô4Ş4\0\0Ñ\0\0DŞVşÂÉ?à\0`œ¥ß4,ÜCğ\n=\r²t4\0%=\r%=ıÑ\0\0?ÇCOÏ¸óÿ\0SğMóÓµPÒÓ\\M4\04¥§¹š?\0 \"\Z\0€ˆh\0\0\"¢\0ˆˆ\0 \"\Z\0€ˆq\'q °;\r£5¡¤§Y™h\0HO³2o\Z\0€ˆh\0\0\"¢\0ˆˆ\0 \"\Z\0€ˆh\0\0\"Æ?–á:€sÙiX†øƒ’†aD°6=\r¥#=-\Z\0`CGzÚCH\0 \"\Z\0€ˆh\0\0\"¢\0ˆˆ\0 bÜé<®ÃXŠ“Ëó¨1(éièJ4\0ÓÓPjĞÓ¢\0fĞ §=„\0\"¢\0ˆˆ\0 \"\Z\0€ˆh\0\0\"»?;58h€9ívr¹U!ANOßÛ-\Z€’†’ş?h\0€Ïèéßx	\0DD\0\r\0@D4\0\0Ñ\0\0DnwrÍ\0=Ü~réšJz\Z˜ÎíÑ\0”ô4”ôô?D\0?èé;y	\0DD\0\r\0@D4\0\0Ñ\0\0DÊq\'-\0À{åÉ¥ƒ(éi`kv\Z §§¡¤§w!\Z\0x‘Ş…‡?\0@D4\0\0Ñ\0\0DD\0\r\0@D4\0\0‘rºpp;\r´&y¡¤§9…h\0X?æŞ4\0\0Ñ\0\0DD\0\r\0@D4\0\0Ñ\0\0DŒ;1–ëp€eÙi`,\r\n%=Í\nDÀô4+ğ¦\0ˆˆ\0 \"\Z\0€ˆh\0\0\"¢\0ˆˆ\0 bÜiÄÀNÃt!”ô4<G4\0ÛÒÓPú´§E\0ğ«O{ÚCH\0 \"\Z\0€ˆh\0\0\"¢\0ˆˆ\0 bÜé®ÃèÇÉå-„\Z”ô4LM4\0óĞÓPš¥§E\0Ln–ö\0ˆ¼M“/ğšY¾½X–O°\nù%=Í8¢`izšq¼i\0\0\"Æ?~ò\0|Å¿\'~OPÒÓÀDğ5=\r¥{Z4\0À;ö´‡?\0@D4\0\0Ñ\0\0DD\0\r\0@dæq§¯Y\0`Z3Ÿ\\N[3p/=\rÜcæh\0Jz\ZJzút¢€5èéÓy	\0DD\0\r\0@D4\0\0Ñ\0\0DwrĞ\0›zöäÒA”ô4°>;\r0„†’^Šh\0à<zz)B\0Ñ\0\0DD\0\r\0@D4\0\0Ñ\0\0D]„„.\\‡f§?U©a(éi\r\0[ÑÓçM\0\r\0@D4\0\0Ñ\0\0DD\0\r\0@Ä¸—qĞ›?.#O¡¤§iC4\0ÜKOÓ†7\r\0@D4\0\0Ñ\0\0DD\0\r\0@D4\0\0ãN›s @ÊNÃæ$#”ô4DÀGz\Z\nŞ4\0\0Ñ\0\0DD\0\r\0@D4\0\0Ñ\0\0DŒ;ÍÆu8\0“²Ó0\r%=\r÷\r@z\ZJ—ö´h\0€¾.íi!€ˆh\0\0\"¢\0ˆˆ\0 \"\Z\0€ˆq§„q\0prÑUPÒÓ°Ñ\0¦§¡´lO‹\0kÙö\0ˆˆ\0 \"\Z\0€ˆh\0\0\"¢\0ˆ4wZöš\0¦Õôä²cèÀô4p¢¦Ñ\0”ô4”ôô¢€åéé1<„\0\"¢\0ˆˆ\0 \"\Z\0€ˆh\0\0\"£Æ?\\³\0ÀâF?\\ºf?’Öa§N¥§¡¤§[\r\0\\OO·ä!$\0\r\0@D4\0\0Ñ\0\0DD\0\r\0@dÔ\"$4â@à;\rlH(CIOó\rÑ\0À¿ô4ßxóK2;éÀ|Ó0=U%=\r—\r@Oz\ZJgö´h\0€…œÙÓv\Z\0€ˆh\0\0\"Æ?˜?\'m\0\rxÓÀ”+”ô4s\r\0ÓÒÓÌÅ›\0 \"\Z\0€ˆh\0\0\"¢\0ˆˆ\0 \"\Z\0€ˆq\'>ã@€ßØià3jJzš}‰€§èiöåM\0\r\0@D4\0\0Ñ\0\0DD\0\r\0@Ä¸S#®Ã¸“?†Fä”ô4\\D4\0İéi(?ïiÑ\0\0K\ZßÓB\0Ñ\0\0DD\0\r\0@D4\0\0ãN¯pÀFœ\\¾BoAIOÃšD0œ†Rû\r\0p?ö=í!$\0\r\0@D4\0\0Ñ\0\0DD\0YoÜ©ıA\0Ìi½“ËÅ\ZFÑÓÀ«Ö‹ ¤§¡¤§Ÿ \Z\0Ø™~‚‡?\0@D4\0\0Ñ\0\0DD\0\r\0@ä‚q\'×,\0°‚N.]³@IOÍØi€»èi(ééy‰\0¦¢§çå!$\0\r\0@D4\0\0Ñ\0\0DD\0\r\0@ä‚EHhÄ?8À§ì4À¯44”ô4?‡h\0  §y<¼i\0\0B¢\0ˆˆ\0 \"\Z\0€ˆh\0\0\"¢\0ˆwbr®Ãfa§?É‰Z(éin \Z\0:ÒÓÜÀ›\0 \"\Z\0€ˆh\0\0\"¢\0ˆˆ\0 \"\Z\0€ˆq\'p °#;\r 4¡¤§Yœh\0EO³8o\Z\0€ˆh\0\0\"¢\0ˆˆ\0 \"\Z\0€ˆh\0\0\"Æ?Öà:€ÓÙiXƒòƒ’†‘D°0=\r¥ƒ=-\Z\0`7{ÚCH\0 \"\Z\0€ˆh\0\0\"¢\0ˆˆ\0 bÜé$®ÃX?“Ë“H1(éihìí¿n>7`Sz\ZJ=ş.ŞôMƒÏ\r(õøÜ\0†ëñwÑ¿\'`&=>7àrzz¢€ééé98¹\0\"¢\0ˆˆ\0 ²Õ¸“‡4\0pÜV!÷É#xŠ\"[EPÒÓPÒÓï‰\0(éé÷<„\0\"¢\0ˆˆ\0 \"\Z\0€ˆh\0\0\"÷;¹f€6î=¹tÍ%=\rÌÈNLHOCIOßL4\0Ğ…¾™‡?\0@D4\0\0Ñ\0\0DD\0\r\0@äã¸“ƒ\0 ğñäÒA”ô4°;;\rÒÓPÒÓ\r\0¼BOoÄCH\0 \"\Z\0€ˆh\0\0\"¢\0ˆˆ\0 \"\Z\0€ÈÇEHèÂu8À¥ì4Ğ—Ş…’æ,¢`1zš³xÓ\0\0DD\0\r\0@D4\0\0Ñ\0\0DD\01îÄ@®ÃVf§??(”ô4‹\r\0gÓÓ,Â›\0 \"\Z\0€ˆh\0\0\"¢\0ˆˆ\0 òîıİ;õlug\0\0\0\0IEND®B`‚');
INSERT INTO tiki_images_data VALUES (1,0,0,'t',3845,'','Logo_40wht.gif','');
INSERT INTO tiki_images_data VALUES (2,700,500,'t',3317,'image/jpg','background.png','ÿØÿà\0JFIF\0\0\0\0\0\0ÿş\0>CREATOR: gd-jpeg v1.0 (using IJG JPEG v62), default quality\nÿÛ\0C\0		\n\r\Z\Z $.\' \",#(7),01444\'9=82<.342ÿÛ\0C			\r\r2!!22222222222222222222222222222222222222222222222222ÿÀ\0\09\0P\"\0ÿÄ\0\0\0\0\0\0\0\0\0\0\0	\nÿÄ\0µ\0\0\0}\0!1AQa\"q2?‘¡#B±ÁRÑğ$3br‚	\n\Z%&\'()*456789:CDEFGHIJSTUVWXYZcdefghijstuvwxyzƒ„…†‡ˆ‰Š’“”•–—˜™š¢£¤¥¦§¨©ª²³´µ¶·¸¹ºÂÃÄÅÆÇÈÉÊÒÓÔÕÖ×ØÙÚáâãäåæçèéêñòóôõö÷øùúÿÄ\0\0\0\0\0\0\0\0	\nÿÄ\0µ\0\0w\0!1AQaq\"2?B‘¡±Á	#3RğbrÑ\n$4á%ñ\Z&\'()*56789:CDEFGHIJSTUVWXYZcdefghijstuvwxyz‚ƒ„…†‡ˆ‰Š’“”•–—˜™š¢£¤¥¦§¨©ª²³´µ¶·¸¹ºÂÃÄÅÆÇÈÉÊÒÓÔÕÖ×ØÙÚâãäåæçèéêòóôõö÷øùúÿÚ\0\0\0?\0÷ì?QFG¨¨¨®/­>År’äzŠ2=EEEZ}ƒ”—#ÔQ‘ê**(úÓì¤¹¢Œ?QQQGÖŸ`å%ÈõdzŠŠŠ>´û(QEÊPQE\0QE\0QE\0QE\0QE\0QE\0QE\0QE\0QE\0QE\0QE\0QE\0QE\0QEÿÙ');





CREATE TABLE tiki_images_old (
"imageId" integer NOT NULL default nextval('tiki_images_old_seq') unique not null,
"galleryId" integer NOT NULL default '0',
"name" varchar(40) NOT NULL default '',
"description" text NOT NULL,
"created" integer NOT NULL default '0',
"filename" varchar(80) NOT NULL default '',
"filetype" varchar(80) NOT NULL default '',
"filesize" integer NOT NULL default '0',
"data" text,
"xsize" integer NOT NULL default '0',
"ysize" integer NOT NULL default '0',
"user" varchar(200) NOT NULL default '',
"t_data" text,
"t_type" varchar(30) NOT NULL default '',
"hits" integer NOT NULL default '0',
"path" varchar(255) NOT NULL default '',
PRIMARY KEY ("imageId")




) ;






INSERT INTO tiki_images_old VALUES (1,1,'Google logo','http://www.google.com/logos/Logo_40wht.gif',1040549952,'Logo_40wht.gif','image/gif',3845,'GIF89a€\05\0÷ü\0ñğñíìíÑÑÓÉÉËããäààáÔÔÕÎÎÏ\"j¿ÁÆ\0>Å\0/š,?(„<¸6§&s1ƒ B‹;z/QœŠ’£˜ ±GÖEÌPÚ\nFÀMÉF²SÆ#XÃ+Tª8b·<UŠe}¯TgŒo}˜z‰¦²µ»·ºÀÑÔÚÎĞÔÉËÏìíïéêìWâZ×;mÊM|ÏUy½lŒÄ‚˜¿hãeß(hÖ-rã?€ç\\‰Ö?§Ğ¥³Ê¥«µ¸¾ÈÀÅÍŞãëæéîPŒè^™ïg”ØpŸãy¡İŠ®åœ¸ã¦»ÙºÅÕËÖæÈÎ×¤¨®®²¸âæìóõøğòõãåè¬ÂàêíñÕÚßÇÉËÌÌÌôõöëìíéêëèéêåæçÄÅÆíïğ÷øøïğğİŞŞÉÊÊY|e-p-v½vPuP˜Ò˜?º?q‡q¯Ï¯‘œ‘ÚèÚÊÏÊğõğÑÕÑÍÑÍØÛØÕØÕÜŞÜ÷ø÷çèçĞÑĞÁÂÁjm(—\'7©6>°=I¸HgÁf€ÏW‰VÆÆÃóóñööõŞŞİëêãñŞ„ÑÊ©ôÌ-õØWñá›ÔÆ?éá¿ÏÍÄÉÈÄÆšá±	÷Æ\ríÀÕ«Ë­>Æ°^’‚KŸ“i×ÔÉáßØèæß¤‹k²‹{`Šu/¿¶™ŞÛÑóñì®¢”öõôõôóáàßÖÕÔãÍ¹èàÚµ¬¦¾¶²ÅÁ¿èæåÏÂ¼µ²±ìéèçäãÜÙØè¨›òèæŞQ9álWä‹zÏ¡™à¸±èÒÎäÙ×Ü%Û-á1Ô8!ã>$ÊŒ‚ª?‰Í­¨Í¹¶ÕÎÍ½Ğ!£¹%Ç0Ë3Ã:)¶E8ÂWI¸bW¸}v‰`[›yuÜÔÓ²\0¥\0’\rÎ¬?\"§2&}1*?F=r\rÔÆÅĞÌÌfffÔÑÑŞÜÜüûûæååûûûùùù÷÷÷ôôôóóóïïïëëëéééçççäääâââßßßÜÜÜÚÚÚÙÙÙ×××ÓÓÓÈÈÈÇÇÇÆÆÆÄÄÄ»»»\0\0\0\0\0\0\0\0\0\0\0\0!ù\0\0ü\0,\0\0\0\0€\05\0\0ÿ\0ù	H° Áƒ*\\È°¡Ã‡#JœH±¢Å‹3jÜÈ±£Ç? CŠI²¤É“(Sª\\É²¥Ë?äÌÉ4Gî¥ÍŒæÎJ§.İ/4mÆ<çÅº£^Î½yĞ\\›5hĞüÙcf!s-czIçDÉ‘H¤(ÒEİtJ™|\r >eàdä¥fÊ˜è”y\"†_\\ÜRI\0«Ko’«óeK\Z?cÔ(à\nV”äÎ8bƒƒˆ%åB¿C1ƒƒ`/’¬³¬–ºuğÒôÉ£ÆŠ¼vé.›4——ˆç`â€¯@<y)JhpĞ€„\0èìŞô²®Àc=jîÑs§N7IŞ?ˆhÿ(A?¼x¶¸Ûò.=L¨¯²÷—çªËÆ¯Ş–î&‘ãFpÂĞvà ;[ÄS?#$0;¹©uÎ±ÍÆFî¤#İHç¨ƒœ€ <d±Î ê¨3È:î°„\0ÌC€}\ruÔQJ?X?LF‚NR‰DNL3ÍôZ‡y°¡];WØ•“QG©‘9„  „$„q@=d\0!@åDÈY´<í|?@™•²-xêÂ\0é “$?9?ÒÈ\"„¦¢Ê ?ıI$Xd¡Å£,\0ñ`˜Ñl„1•2?SK*yò‚(Pá”\Z?§J¤Ù‰TpÏò¸ÿC×ˆ¦³N\0YğÅç,ÉÛ.·0ÓL3¾Øb.¼¬£Pt3È\"‰PR	%“$¢#›|‘NZ€3…EA9¼àÁ3lA‡|`ªi;Ë¢‹-Ì,Œ±ÂÈ€e§š*9„PA„0q?=¬3gAZ]áZ?Ê‚K3Â|Ï8ßDãK0Àc&!Jy!Š\"•0\"Ê)³8b‰$‰L‚;ƒ µ¡/È°Ã1Ôpƒ1XĞğÈÁ.¦VÌãÎªÜâ1ÔÌR4ÁüâÌ)!ö‹ªDæ’„\Z `°\0\"Ä$MZ­“2Ñ\0òĞc\04½ø‚Í*[(›&ŠPâH<l·ÿ-N%’HBÉ+v(kâ\r3¼ôˆ€˜`Ï<òØ3t¦ô¼s\n.Ç°r=õÜñÌ/ÕLóÎ:WU”ŸApàõ»U:”Y\0´ØÒ\r*”Çøpc“Ì*ïdqÊ!‘`R<ÁÉSÏ#”H‰\'”ÓÎA<àƒ<Ø×c< ?…ôÜqyõÈ“0ÓÌ£>èwS?7Ş´bÇ çT„N\0Eà „!;èx—^?eÃåƒG9Ş?¢È°†4êD(‚¤@à‚¶@€Ê­ìy¤(?l0?1!o(AÀ\0À\0q¸ÜÁ¾qe¬Â\0õ Ç<èQŒgxƒÃ0Æ;ÿ¾à…Š¢E¸?\ZPÿ €ÑL.la\rRŒ)îpĞäÑ\n`ô¢\ZÜƒ#‘ˆN¬bLhG\0Øñz°b’€Ä%¬°!l`\\°‡<\nP\0zœÀh@	¬ \07\\. †-²‹ØãÂx†3¢±ŠVX¡XG\'B+Øa7Ø\0Hp?ÿAQIMÊ‰:\\?‹`\\ƒ—Ì¤OÖA€·‘Î°`…\"&ñ‰UX?åXM:P€;\0 Àƒô?&Àª:\00\0?„á†lôaa#¤(†0l¡ŒhˆC\0pd¬\0P?N^¡;¨A0.Ğ£„@åº°ÿ‚`a@\05nñ‹kL#Ûé—Ø!oÃ¤…\"\"Ñ	R¼+7æHG;è±2H|3 ?(+p€ÛÀô?A×ú‡ÍËááŞ,)¢‘‹Y¨m‡\n*?(Bˆ,¤Àp@3\rğŸû<!	CÈAŠ0¾ À¶ğ…µcŠCÄÆC#šŠbBSYÈ\'?¨F$Â£«`‚.à€¼kì\0Ã0?}X¡¥ÚÄ¢ŒjHƒ›$<Òã¢tÔÅ~\0ƒ2€?„à\0	k§@Ì¡#è@3Â\r`z€#ª`ÄC=BTs¨cÅ ¢7˜@Š–qâø¸ç&\0ÿ \"\0,`Q8à0@‹ÜáƒL€ˆ´GóªaüâÚğ%™ÜÑvèJ RD6?là\nx@gq£sxAì€Gn@Í&À\nÏèÅ1^)€w+äH‡R{\rnÀbpD¦>FkUÓn“à&`±?}P`x\0äÀ`<À‡´\\Lé•¾ˆ 4îQx¸hTšHFİQ(àÅ8eR¨ó²£\0ëU\02|?h`´BÂŠØI;Ş˜ÆnÂú\0+¦u#(ñ	Rìú0A	B?8?Æ´68¸4¬@6 PFÿ~Ò.E\\€x$àx±|?É«å\nP/¨É}àƒÊX³6öqOŒª£¸È†¼\nJDBV\0Ãc2ˆB\\ûàÂ]ß›ì@ƒ¶æ†ÁğÌÁRQzi ñclt£·‰™L–„“A°øx€0à?¼ÙHè&mğ\0¯‰Ú\n«ØF5ªqkÊÌ²Êà+ôŠ}?àÆD=Êñ…?€rÄ>ÈÌ8 0 B`WL¶ÃBãƒ¶+´¢Õ86š‘‹qdáê0….v‘âˆ¸¦:hBğ\0\\ IˆB\0Öñ…u´;p\0ÀƒkÚÚÚÿ¸F5œa?Æ‚Ëˆn¿ï`?;„œğ$,!ŠÂbÊ °äè;´£:@HÙ\Zæz¼ƒíˆCÆ †3\\9€EÊ«ñãgƒ‘Œ\0˜u‘ŒÆõx¨À\"@qx\09ˆ;@ğ??ÀúÀ<vÇŠipã\ZÉp4ñŒa€C\0  Ú´·\nL€‚?¨„%*Q‰PÜA\0“kÑ:àJ\0ô`Ë±¸,;Ø!\r?8ƒê‘7Ôã\0}ç†7®q\rk\\£¹`Ç¾z…‘}\\\0÷ÀH0‚H@\0?f°ƒ¼ûš,rO~V#\ZÂxÅ)À°C\nÛ?mšÇÿ¬ ˆU?Â¡ÈÄM¤y\08A\n9°A8 ?Tü\ZhB@íáCÃõ â—û0Ó`€¬0P8†%gçƒ?ğzap÷0\0°aÈÃ6nƒCò S×}d\0ö? ÁÁGÃ±À\'ê@#`Pà?`€‚Ãáì°Q@3À|=?\0ûÀ#Pq\n@?<õ€xôå`åĞ@Vpùp„D¸\Z¼‡™1Lâ6ˆW‚æq‚(È ë?„@ƒ\0[\0ñ ğğê¡FÚ:\0ìPÆ!\0MÔµq\'A@= ó:a€û@y¶\0?„ÅÁGïÿĞ·YĞA0ˆ@\n¨‚º¶1A\0pR 6\'˜‡î`tÖõ\" Ó)WĞ‰YĞí?¥È\'q6Y[ñ¸ÂŠ¹²q\0Ğ\0°5À8dƒx\0ø°Ã\0#``°®ˆ‹WP†¶’i8ïĞ Vq…š¨3R‹­XŠ_?‹g¡%“¥é0\0\0\0ƒğ0Ü2\r##åxhâW Fp\r`/Daå@\0/h\'ĞÆx``ÚÒŒC$W??¸Ø\'\r—Z±2#“&8E¡9YW‚$4aW@Aà-´a¥·ylDğLT„Y€Q¼FD‚àHc³k)‘QT\0Tâ@ø€0@†Ã*à€wO¤H	(§%¾Å%P”‚kRsÅ—š””j‘Qv`\0Á–g2?eÒOJ0À… `àì¤•L0qu$0mç/ÀÊç #ï°Sli^ğå@V`@|!0\"`)À7Á±—ê Y}ù\0>Vp™w?CÈƒ‡Y´,÷™-qÃÔ„ ó‹ç¡@ß7†dšBÁ‘_\0!Ÿ(?{hŠéÈš7a^é\0\0·Â¯ˆ]@á€¶™9!•C?1œl¹$Ê‰œÌy\0;',0,0,'ross','','',3,'');
INSERT INTO tiki_images_old VALUES (2,1,'test','Adding an entry to test the full text search function.\r\n\r\nHere are some search terms:\r\n\r\nJobHunting\r\ntest\r\ntiki\r\n',1040868478,'background.png','image/x-png',3317,'‰PNG\r\n\Z\n\0\0\0\rIHDR\0\0¼\0\0ô\0\0\0P;iˆ\0\0\0bKGD\0ÿ\0ÿ\0ÿ ½§“\0\0\0	pHYs\0\0\0\0Òİ~ü\0\0\0tIMEÒ!(¶Y=ı\0\0‚IDATxœíÜQn#7EQMàı/5;’?“»l?µØİ,òœøÃ?/dÖûñ÷_>\0`?îş÷v÷\0\0£ü}÷°¸?îş\0€D\0\r\0@D4\0\0Ñ\0\0DŞkê \0vtàäÒA”ô4°8;\r0Š†’^‡h\0àTzzB\0Ñ\0\0DD\0\r\0@D4\0\0Ñ\0\0D,BB®ÃF²ÓÀÂ1”ô4‰€İèiò¦\0ˆˆ\0 \"\Z\0€ˆh\0\0\"¢\0ˆˆ\0 bÜ‰+¹hÌNWR¨PÒÓô \Z\0n§§éÁ›\0 \"\Z\0€ˆh\0\0\"¢\0ˆˆ\0 \"\Z\0€ˆq\'ˆ±Ó€j„’†÷D@IOÃ{Ş4\0\0Ñ\0\0DD\0\r\0@D4\0\0Ñ\0\0DŒ;MÈu8\03²Ó0!%=\r7\r@z\ZJ×õ´h\0€Ö®ëi!€ˆh\0\0\"¢\0ˆ¼yZÄãáY:\0ßó’ÇãáY:|BOÃ/Œ;…|rÀ–|@Â/|ÓòÉ%=\r\rÀ+ô4”ÖìiÑ\0\0Ã­ÙÓN.€ˆh\0\0\"¢\0ˆˆ\0 \"\Z\0€Hßq§5¯Y\0`Z}O.›¶œMOgé\r@IOCIO \Z\0Ø?ÀCH\0 \"\Z\0€ˆh\0\0\"¢\0ˆˆ\0 2pÜÉ5\0¬làÉ¥k(éi`v\Zàlz\ZJzºÑ\0À-ôt?B\0Ñ\0\0DD\0\r\0@D4\0\0Ñ\0\0D.BB#Äf§?=ie(éi¾\"\Z\0øIOóo\Z\0€ˆh\0\0\"¢\0ˆˆ\0 \"\Z\0€ˆh\0\0\"Æ?˜„ëp€ÙÙi`âJzš‰ˆ€™éi&âM\0\r\0@D4\0\0Ñ\0\0DD\0\r\0@Ä¸_p Àÿì4ğA	%=Í¦DÀ³ô4›ò¦\0ˆˆ\0 \"\Z\0€ˆh\0\0\"¢\0ˆˆ\0 bÜ©×á\0ÜÆNC/\nJz\Z® \Z€èi(\rîiÑ\0\0«\ZÜÓB\0Ñ\0\0DD\0\r\0@D4\0\0ãN¯s Àœ\\¾NuAIOÃjDp=\r¥Æ=-\Z\0àJ?{ÚCH\0 \"\Z\0€ˆh\0\0\"¢\0ˆˆ\0 ²ä¸Sãk\0˜Ö’\'—ëe¡§?—,\r@IOCIO§D\0›ÓÓ)!€ˆh\0\0\"¢\0ˆˆ\0 \"\Z\0€È5ãN®Y\0 ½kN.]³@IO?Øi€éi(ééI‰\0f£§\'å!$\0\r\0@D4\0\0Ñ\0\0DD\0\r\0@äšEHhÄ?8@ÍN¼#£¡¤§\r\0Dô4Ş4\0\0Ñ\0\0DŞVşÂÉ?à\0`œ¥ß4,ÜCğ\n=\r²t4\0%=\r%=ıÑ\0\0?ÇCOÏ¸óÿ\0SğMóÓµPÒÓ\\M4\04¥§¹š?\0 \"\Z\0€ˆh\0\0\"¢\0ˆˆ\0 \"\Z\0€ˆq\'q °;\r£5¡¤§Y™h\0HO³2o\Z\0€ˆh\0\0\"¢\0ˆˆ\0 \"\Z\0€ˆh\0\0\"Æ?–á:€sÙiX†øƒ’†aD°6=\r¥#=-\Z\0`CGzÚCH\0 \"\Z\0€ˆh\0\0\"¢\0ˆˆ\0 bÜé<®ÃXŠ“Ëó¨1(éièJ4\0ÓÓPjĞÓ¢\0fĞ §=„\0\"¢\0ˆˆ\0 \"\Z\0€ˆh\0\0\"»?;58h€9ívr¹U!ANOßÛ-\Z€’†’ş?h\0€Ïèéßx	\0DD\0\r\0@D4\0\0Ñ\0\0DnwrÍ\0=Ü~réšJz\Z˜ÎíÑ\0”ô4”ôô?D\0?èé;y	\0DD\0\r\0@D4\0\0Ñ\0\0DÊq\'-\0À{åÉ¥ƒ(éi`kv\Z §§¡¤§w!\Z\0x‘Ş…‡?\0@D4\0\0Ñ\0\0DD\0\r\0@D4\0\0‘rºpp;\r´&y¡¤§9…h\0X?æŞ4\0\0Ñ\0\0DD\0\r\0@D4\0\0Ñ\0\0DŒ;1–ëp€eÙi`,\r\n%=Í\nDÀô4+ğ¦\0ˆˆ\0 \"\Z\0€ˆh\0\0\"¢\0ˆˆ\0 bÜiÄÀNÃt!”ô4<G4\0ÛÒÓPú´§E\0ğ«O{ÚCH\0 \"\Z\0€ˆh\0\0\"¢\0ˆˆ\0 bÜé®ÃèÇÉå-„\Z”ô4LM4\0óĞÓPš¥§E\0Ln–ö\0ˆ¼M“/ğšY¾½X–O°\nù%=Í8¢`izšq¼i\0\0\"Æ?~ò\0|Å¿\'~OPÒÓÀDğ5=\r¥{Z4\0À;ö´‡?\0@D4\0\0Ñ\0\0DD\0\r\0@dæq§¯Y\0`Z3Ÿ\\N[3p/=\rÜcæh\0Jz\ZJzút¢€5èéÓy	\0DD\0\r\0@D4\0\0Ñ\0\0DwrĞ\0›zöäÒA”ô4°>;\r0„†’^Šh\0à<zz)B\0Ñ\0\0DD\0\r\0@D4\0\0Ñ\0\0D]„„.\\‡f§?U©a(éi\r\0[ÑÓçM\0\r\0@D4\0\0Ñ\0\0DD\0\r\0@Ä¸—qĞ›?.#O¡¤§iC4\0ÜKOÓ†7\r\0@D4\0\0Ñ\0\0DD\0\r\0@D4\0\0ãN›s @ÊNÃæ$#”ô4DÀGz\Z\nŞ4\0\0Ñ\0\0DD\0\r\0@D4\0\0Ñ\0\0DŒ;ÍÆu8\0“²Ó0\r%=\r÷\r@z\ZJ—ö´h\0€¾.íi!€ˆh\0\0\"¢\0ˆˆ\0 \"\Z\0€ˆq§„q\0prÑUPÒÓ°Ñ\0¦§¡´lO‹\0kÙö\0ˆˆ\0 \"\Z\0€ˆh\0\0\"¢\0ˆ4wZöš\0¦Õôä²cèÀô4p¢¦Ñ\0”ô4”ôô¢€åéé1<„\0\"¢\0ˆˆ\0 \"\Z\0€ˆh\0\0\"£Æ?\\³\0ÀâF?\\ºf?’Öa§N¥§¡¤§[\r\0\\OO·ä!$\0\r\0@D4\0\0Ñ\0\0DD\0\r\0@dÔ\"$4â@à;\rlH(CIOó\rÑ\0À¿ô4ßxóK2;éÀ|Ó0=U%=\r—\r@Oz\ZJgö´h\0€…œÙÓv\Z\0€ˆh\0\0\"Æ?˜?\'m\0\rxÓÀ”+”ô4s\r\0ÓÒÓÌÅ›\0 \"\Z\0€ˆh\0\0\"¢\0ˆˆ\0 \"\Z\0€ˆq\'>ã@€ßØià3jJzš}‰€§èiöåM\0\r\0@D4\0\0Ñ\0\0DD\0\r\0@Ä¸S#®Ã¸“?†Fä”ô4\\D4\0İéi(?ïiÑ\0\0K\ZßÓB\0Ñ\0\0DD\0\r\0@D4\0\0ãN¯pÀFœ\\¾BoAIOÃšD0œ†Rû\r\0p?ö=í!$\0\r\0@D4\0\0Ñ\0\0DD\0YoÜ©ıA\0Ìi½“ËÅ\ZFÑÓÀ«Ö‹ ¤§¡¤§Ÿ \Z\0Ø™~‚‡?\0@D4\0\0Ñ\0\0DD\0\r\0@ä‚q\'×,\0°‚N.]³@IOÍØi€»èi(ééy‰\0¦¢§çå!$\0\r\0@D4\0\0Ñ\0\0DD\0\r\0@ä‚EHhÄ?8À§ì4À¯44”ô4?‡h\0  §y<¼i\0\0B¢\0ˆˆ\0 \"\Z\0€ˆh\0\0\"¢\0ˆwbr®Ãfa§?É‰Z(éin \Z\0:ÒÓÜÀ›\0 \"\Z\0€ˆh\0\0\"¢\0ˆˆ\0 \"\Z\0€ˆq\'p °#;\r 4¡¤§Yœh\0EO³8o\Z\0€ˆh\0\0\"¢\0ˆˆ\0 \"\Z\0€ˆh\0\0\"Æ?Öà:€ÓÙiXƒòƒ’†‘D°0=\r¥ƒ=-\Z\0`7{ÚCH\0 \"\Z\0€ˆh\0\0\"¢\0ˆˆ\0 bÜé$®ÃX?“Ë“H1(éihìí¿n>7`Sz\ZJ=ş.ŞôMƒÏ\r(õøÜ\0†ëñwÑ¿\'`&=>7àrzz¢€ééé98¹\0\"¢\0ˆˆ\0 ²Õ¸“‡4\0pÜV!÷É#xŠ\"[EPÒÓPÒÓï‰\0(éé÷<„\0\"¢\0ˆˆ\0 \"\Z\0€ˆh\0\0\"÷;¹f€6î=¹tÍ%=\rÌÈNLHOCIOßL4\0Ğ…¾™‡?\0@D4\0\0Ñ\0\0DD\0\r\0@äã¸“ƒ\0 ğñäÒA”ô4°;;\rÒÓPÒÓ\r\0¼BOoÄCH\0 \"\Z\0€ˆh\0\0\"¢\0ˆˆ\0 \"\Z\0€ÈÇEHèÂu8À¥ì4Ğ—Ş…’æ,¢`1zš³xÓ\0\0DD\0\r\0@D4\0\0Ñ\0\0DD\01îÄ@®ÃVf§??(”ô4‹\r\0gÓÓ,Â›\0 \"\Z\0€ˆh\0\0\"¢\0ˆˆ\0 òîıİ;õlug\0\0\0\0IEND®B`‚',700,500,'ross','ÿØÿà\0JFIF\0\0\0\0\0\0ÿş\0>CREATOR: gd-jpeg v1.0 (using IJG JPEG v62), default quality\nÿÛ\0C\0		\n\r\Z\Z $.\' \",#(7),01444\'9=82<.342ÿÛ\0C			\r\r2!!22222222222222222222222222222222222222222222222222ÿÀ\0\09\0P\"\0ÿÄ\0\0\0\0\0\0\0\0\0\0\0	\nÿÄ\0µ\0\0\0}\0!1AQa\"q2?‘¡#B±ÁRÑğ$3br‚	\n\Z%&\'()*456789:CDEFGHIJSTUVWXYZcdefghijstuvwxyzƒ„…†‡ˆ‰Š’“”•–—˜™š¢£¤¥¦§¨©ª²³´µ¶·¸¹ºÂÃÄÅÆÇÈÉÊÒÓÔÕÖ×ØÙÚáâãäåæçèéêñòóôõö÷øùúÿÄ\0\0\0\0\0\0\0\0	\nÿÄ\0µ\0\0w\0!1AQaq\"2?B‘¡±Á	#3RğbrÑ\n$4á%ñ\Z&\'()*56789:CDEFGHIJSTUVWXYZcdefghijstuvwxyz‚ƒ„…†‡ˆ‰Š’“”•–—˜™š¢£¤¥¦§¨©ª²³´µ¶·¸¹ºÂÃÄÅÆÇÈÉÊÒÓÔÕÖ×ØÙÚâãäåæçèéêòóôõö÷øùúÿÚ\0\0\0?\0÷ì?QFG¨¨¨®/­>År’äzŠ2=EEEZ}ƒ”—#ÔQ‘ê**(úÓì¤¹¢Œ?QQQGÖŸ`å%ÈõdzŠŠŠ>´û(QEÊPQE\0QE\0QE\0QE\0QE\0QE\0QE\0QE\0QE\0QE\0QE\0QE\0QE\0QEÿÙ','image/jpg',1,'');





CREATE TABLE tiki_language (
"source" text NOT NULL,
"lang" varchar(2) NOT NULL default '',
"tran" text,
PRIMARY KEY ("lang","source")
) ;











CREATE TABLE tiki_languages (
"lang" varchar(2) NOT NULL default '',
"language" varchar(255) NOT NULL default '',
PRIMARY KEY ("lang")
) ;






INSERT INTO tiki_languages VALUES ('en','English');





CREATE TABLE tiki_link_cache (
"cacheId" integer NOT NULL default nextval('tiki_link_cache_seq') unique not null,
"url" varchar(250) NOT NULL default '',
"data" text,
"refresh" integer NOT NULL default '0',
PRIMARY KEY ("cacheId")
) ;






INSERT INTO tiki_link_cache VALUES (1,'http://www.uni-koeln.de/phil-fak/indologie/tamil/mwd_search.html','<HTML>\r\n<HEAD>\r\n<TITLE>IITS - Cologne Digital Sanskrit Lexicon</TITLE>\r\n</HEAD>\r\n<BODY LINK=\"Blue\" VLINK=\"Green\" ALINK=\"Green\"\r\nBACKGROUND=\"./graphics/verl01.gif\">\r\n\r\n<CENTER><H2>\r\nCologne Digital Sanskrit Lexicon</H2></CENTER>\r\n\r\n<FORM METHOD=\"POST\" ACTION=\"/cgi-bin/SFgate\" ENCTYPE=\"x-www-form-encoded\">\r\n<INPUT NAME=\"database\" TYPE=\"hidden\"\r\nVALUE=\"local//vol/info/wais/db/tamil2/mwd\">\r\n<INPUT TYPE=\"hidden\" NAME=\"application\"\r\nVALUE=\"/phil-fak/indologie/tamil/mwd\">\r\n<INPUT TYPE=\"hidden\" NAME=\"tieinternal\" VALUE=\"und\">\r\n<INPUT TYPE=\"hidden\" NAME=\"convert\" VALUE=\"Tabelle\">\r\n<INPUT TYPE=\"hidden\" NAME=\"verbose\" VALUE=\"0\">\r\n<INPUT TYPE=\"hidden\" NAME=\"multiple\" VALUE=\"1\">\r\n<INPUT TYPE=\"hidden\" NAME=\"qu_tie\" VALUE=\"und\">\r\n<!--INPUT TYPE=\"hidden\" NAME=\"qu_name\" VALUE=\"alle\"-->\r\n\r\n<TABLE BORDER=\"0\" ALIGN=\"BLEEDLEFT\" CELLPADDING=\"5\" CELLSPACING=\"5\">\r\n<TR>\r\n<TD><FONT SIZE=\"+1\">Sanskrit : </FONT></TD>\r\n<TD><INPUT TYPE=\"text\" NAME=\"st\" SIZE=\"40\"></TD>\r\n<TD><INPUT TYPE=\"submit\" VALUE=\"Start search\"></TD>\r\n</TR>\r\n<TR>\r\n<TD><FONT SIZE=\"+1\"> English: </FONT></TD>\r\n<TD><INPUT TYPE=\"text\" NAME=\"en\" SIZE=\"40\"></TD>\r\n<TD><INPUT TYPE=\"submit\" VALUE=\"Start search\"></TD>\r\n</TR>\r\n<TR>\r\n<TD><FONT SIZE=\"+1\">Maximum Output: </FONT></TD>\r\n<TD>\r\n<SELECT NAME=\"maxhits\">\r\n<OPTION VALUE=\"\">20</OPTION>\r\n<OPTION SELECTED=\"SELECTED\" VALUE=\"\">50</OPTION>\r\n<OPTION VALUE=\"\">100</OPTION>\r\n<OPTION VALUE=\"\">200</OPTION>\r\n<OPTION VALUE=\"\">500</OPTION>\r\n<OPTION VALUE=\"\">1000</OPTION></SELECT>\r\n<INPUT TYPE=\"reset\" VALUE=\"New search\"></TD></TR>\r\n</TABLE>\r\n</FORM>\r\n\r\n<BLOCKQUOTE>At present the Cologne Digital Sanskrit\r\nLexicon contains Monier-Williams\' \'Sanskrit-English\r\nDictionary\' with approx. 160.000 main entries.<BR>\r\nYou can either search for one of the Sanskrit main entries\r\nunder <B>Sanskrit </B> or under <B>English</B>\r\nfor a translation, grammatical and any other information listed in the MW.\r\n<BR>The transliteration is based on the Harvard-Kyoto (HK)\r\n convention as follows:\r\n<CENTER><P><TT>\r\na A i I u U R RR lR lRR e ai o au M H<BR>\r\nk kh g gh G c ch j jh J<BR>\r\nT Th D Dh N t th d dh n<BR>\r\np ph b bh m y r l v z S s h</TT></CENTER><P>\r\nNote: WAIS search is not case sensitive.\r\nFor further information see:\r\n<A HREF=\"http://www.uni-koeln.de/phil-fak/indologie/tamil/mwreport.html\">\r\nReport on the Cologne Digital Sanskrit Lexicon Project</A>\r\n<A HREF=\"http://www.uni-koeln.de/phil-fak/indologie/tamil/mon-add.tif\">\r\n+ Appendix</A>\r\n\r\n<P>Suggestions and comments to:\r\n<A HREF=\"mailto:th.malten@uni-koeln.de\">\r\nIITS-lexicon@uni-koeln.de</A></BLOCKQUOTE>\r\n\r\n<P>&nbsp;\r\n<CENTER>\r\n<A HREF=\"http://www.uni-koeln.de/phil-fak/indologie/index.e.html\">\r\n<IMG SRC=\"./graphics/back.gif\" BORDER=0 HEIGHT=59 WIDTH=50><P>\r\n</A>\r\n<A HREF=\"http://www.uni-koeln.de/phil-fak/indologie/index.e.html\">\r\nHOME</A><BR>\r\n\r\n<FONT SIZE=-1>\r\nWebmasters: <A HREF=\"mailto:ar.zeini@uni-koeln.de\">A.\r\nZeini </A>&amp; <A HREF=\"mailto:grotebev@uni-koeln.de\">T.\r\nGrote-Beverborg</A>.</FONT></CENTER>\r\n<CENTER>This page has been accessed <a href=\"/cgi-bin/count3/i/CDSL\"><img src=\"/cgi-bin/count3.gif/CDSL\" width=44 height=10></a> times</CENTER>\r\n</BODY>\r\n</HTML>\r\n',1038940749);
INSERT INTO tiki_link_cache VALUES (2,'http://losangeles.craigslist.org/eng/','<META http-equiv=\"Cache-Control\" content=\"max-age=900\">\n<META http-equiv=\"Cache-Control\" content=\"public\">\n<META http-equiv=\"Expires\" content=\"Wed, 04 Dec 2002 03:16:58 GMT\">\n<html><head>\n<title>craigslist | internet engineering jobs in los angeles </title>\n</head>\n<body bgcolor=white>\n<FORM Action=/cgi-bin/search Method=GET>\n<table width=100%><TR><TD><h3><a href=/>craigslist</a> &gt; <a href=/>los angeles</a> &gt; <a href=/eng>internet engineering jobs</a></h3>\n</TD><TD align=center valign=top width=30%>[<B> <a href=\'/cgi-bin/posting.cgi?areaID=7&subAreaID=0&categoryID=14&group=J\'>post</a> </b>]</table>\n<input type=hidden name=areaID value=7>\n<input type=hidden name=subAreaID value=0>\n<input type=hidden name=catAbbreviation value=eng>\n<input type=hidden name=cat value=14>\n<input type=hidden name=group value=J>\n<input type=hidden name=type_search value=>\n&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;keywords:\n<input name=query size=30 maxsize=30 value=\"\">\n&nbsp;<select name=new_cat>\n<option value=\'all\' >ALL\n<option value=tec >all computer jobs\n<option value=esc >all engineering / science\n<option value=lgg >all legal / government\n<option value=35 >activity partners\n<option value=60 >apts broker fee\n<option value=85 >apts broker no fee\n<option value=86 >apts by owner\n<option value=1 >apts for rent\n<option value=2 >apts wanted\n<option value=70 >artists\n<option value=53 >artists / musicians\n<option value=42 >barter\n<option value=68 >bicycles\n<option value=12 >business jobs\n<option value=6 >cars / trucks\n<option value=62 >casual encounters\n<option value=56 >childcare\n<option value=3 >community\n<option value=7 >computer / tech\n<option value=76 >computer services\n<option value=77 >creative services\n<option value=43 >cycles\n<option value=57 >education jobs\n<option value=48 >engineering jobs\n<option value=78 >erotic services\n<option value=15 >etcetera jobs\n<option value=66 >event calendar\n<option value=79 >event services\n<option value=23 >finance jobs\n<option value=5 >for sale\n<option value=64 >furniture\n<option value=73 >garage sales\n<option value=61 >government jobs\n<option value=26 >healthcare jobs\n<option value=80 >household services\n<option value=65 >housing swap\n<option value=54 >human resource jobs\n<option value=14  selected>internet engineering jobs\n<option value=82 >labor / moving\n<option value=47 >legal jobs\n<option value=81 >lesson / tutoring\n<option value=13 >marketing jobs\n<option value=25 >media jobs\n<option value=33 >men seeking men\n<option value=32 >men seeking women\n<option value=63 >missed connections\n<option value=69 >motorcycles/scooters\n<option value=71 >musicians\n<option value=51 >network jobs\n<option value=28 >nonprofit jobs\n<option value=40 >office\n<option value=24 >office jobs\n<option value=41 >parking\n<option value=37 >pets\n<option value=72 >real estate for sale\n<option value=10 >resumes\n<option value=27 >retail jobs\n<option value=36 >rideshare\n<option value=18 >rooms / shares\n<option value=19 >rooms wanted\n<option value=49 >sales jobs\n<option value=75 >science jobs\n<option value=83 >skilled trade services\n<option value=59 >skilled trades jobs\n<option value=4 >small biz ads\n<option value=21 >software jobs\n<option value=58 >sublet/temp wanted\n<option value=39 >sublets / temporary\n<option value=74 >summer sublets\n<option value=50 >sys admin jobs\n<option value=55 >tech support jobs\n<option value=84 >therapeutic services\n<option value=44 >tickets\n<option value=52 >tv video radio jobs\n<option value=29 >volunteers\n<option value=20 >wanted\n<option value=11 >web design jobs\n<option value=31 >women seeking men\n<option value=30 >women seeking women\n<option value=16 >writing jobs\n</select>&nbsp;<input type=submit value=search><BR>\n&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=checkbox name=addOne value=telecommuting >telecommute\n<input type=checkbox name=addTwo value=contract >contract\n<input type=checkbox name=addThree value=internship >internship\n<input type=checkbox name=addFour value=part-time >part-time\n<input type=checkbox name=addFive value=non-profit >non-profit\n</FORM>\n<blockquote>\n<table width=100% border=0><tr><td><font size=2>Tue Dec  3 19:01 - refresh / reload to see new posts</font></td><td align=right><font size=2>&nbsp;\n</td></tr></table>\n<p><table width=90%><tr><td bgcolor=#cccccc>&nbsp;Tue Dec 3rd</td></tr></table><p>\n<p>&nbsp;<a href=/lax/eng/7159809.html>Seeking Freelance ASP.NET Web App Developer: </a>  (Woodland Hills)\n<p><table width=90%><tr><td bgcolor=#cccccc>&nbsp;Mon Dec 2nd</td></tr></table><p>\n<p>&nbsp;<a href=/lax/eng/7144107.html>Database and Web Site Developer </a>  (El Segundo)\n<p>&nbsp;<a href=/lax/eng/7144031.html>Code HTML from scratch & Tech Support </a>  (Santa Monica, CA)\n<p><table width=90%><tr><td bgcolor=#cccccc>&nbsp;Tue Nov 26th</td></tr></table><p>\n<p>&nbsp;<a href=/lax/eng/7052320.html>Application Developer - Clarify eSupport </a>  (Los Angeles)\n<p>&nbsp;<a href=/lax/eng/7052174.html>Siebel  Configuration Consultant </a>  (Los Angeles)\n<p>&nbsp;<a href=/lax/eng/7050649.html>Wireless Communication Systems Engineer/Analyst </a>  (Tarzana)\n<p>&nbsp;<a href=/lax/eng/7050009.html>Programmer </a>  (Los Angeles)\n<p>&nbsp;<a href=/lax/eng/7047077.html>WEB Developer - JavaScript </a>  (Los Angeles)\n<p>&nbsp;<a href=/lax/eng/7047016.html>WEB Developer - Perl/CGI </a>  (Los Angeles)\n<p>&nbsp;<a href=/lax/eng/7046464.html>Flash Expert </a>  (Los Angeles, CA)\n<p>&nbsp;<a href=/lax/eng/7045073.html>UI Designer </a>  (West Los Angeles)\n<p><table width=90%><tr><td bgcolor=#cccccc>&nbsp;Mon Nov 25th</td></tr></table><p>\n<p>&nbsp;<a href=/lax/eng/7031238.html>SAP Basis Administrator </a>  (Los Angeles)\n<p>&nbsp;<a href=/lax/eng/7031215.html>I need a DHTML guru!! </a>  (Los Angeles)\n<p>&nbsp;<a href=/lax/eng/7031128.html>E-mail List Developer </a>  (Los Angeles)\n<p>&nbsp;<a href=/lax/eng/7030601.html>Web Architect </a>  (Los Angeles)\n<p>&nbsp;<a href=/lax/eng/7029487.html>Interactive Traffic Manager </a>  (Palos Verdes (Los Angeles South Bay))\n<p>&nbsp;<a href=/lax/eng/7028318.html>Senior RF Analog Engineer </a>  (Camarillo)\n<p>&nbsp;<a href=/lax/eng/7027332.html>SAP Network Admin. </a>  (Los Angeles)\n<p>&nbsp;<a href=/lax/eng/7022196.html>B2C web programmer – ASP –Access - SQL – JS - HTML </a>  (Glendale / Burbank)\n<p>&nbsp;<a href=/lax/eng/7018489.html>WEB Developer - JavaScript </a>  (Los Angeles)\n<p><table width=90%><tr><td bgcolor=#cccccc>&nbsp;Sun Nov 24th</td></tr></table><p>\n<p>&nbsp;<a href=/lax/eng/7014187.html>linux c++ software engineer </a>  (los angeles)\n<p><table width=90%><tr><td bgcolor=#cccccc>&nbsp;Sat Nov 23rd</td></tr></table><p>\n<p>&nbsp;<a href=/lax/eng/6997526.html>Web site  developer </a>  (Glendora, CA)\n<p>&nbsp;<a href=/lax/eng/6995599.html>Need Web Designer by Artist/Musician </a>  (Virtual)\n<p><table width=90%><tr><td bgcolor=#cccccc>&nbsp;Fri Nov 22nd</td></tr></table><p>\n<p>&nbsp;<a href=/lax/eng/6984277.html>Web Programmer wanted at profitable internet company </a>  (Los Angeles)\n<p>&nbsp;<a href=/lax/eng/6980513.html>Electronic Fund Transfer Technical lead/Architect </a>  (Los Angeles)\n<p>&nbsp;<a href=/lax/eng/6975403.html>Siebel Analytics </a>  (SF valley)\n<p>&nbsp;<a href=/lax/eng/6974645.html>Web Help! </a>  (Marina Del Rey)\n<p><table width=90%><tr><td bgcolor=#cccccc>&nbsp;Thu Nov 21st</td></tr></table><p>\n<p>&nbsp;<a href=/lax/eng/6959744.html>Sr. Java Developer </a>  (SF Valley)\n<p>&nbsp;<a href=/lax/eng/6952856.html>WANTED  - DB/WEB Engineer </a>  (Los Angeles)\n<p><table width=90%><tr><td bgcolor=#cccccc>&nbsp;Wed Nov 20th</td></tr></table><p>\n<p>&nbsp;<a href=/lax/eng/6939558.html>Full-time Photoshop Pro and Wide Format Printer Needed </a>  (Hawthorne)\n<p>&nbsp;<a href=/lax/eng/6938949.html>Senior Server Engineer - Team Lead </a>  (Los Angeles)\n<p>&nbsp;<a href=/lax/eng/6927436.html>Looking for PHP, CFM or ASP programmer </a>  (Los Angeles (Burbank))\n<p><table width=90%><tr><td bgcolor=#cccccc>&nbsp;Tue Nov 19th</td></tr></table><p>\n<p>&nbsp;<a href=/lax/eng/6925177.html>Web Developer </a>  (Los Angeles)\n<p>&nbsp;<a href=/lax/eng/6925139.html>Siebel Configuration Specialist </a>  (South Bay)\n<p>&nbsp;<a href=/lax/eng/6916548.html>System Analysts - 4 Positions (Loan Origination Experience) </a>  (S.F. Valley)\n<p>&nbsp;<a href=/lax/eng/6915646.html>PL/SQL Developer </a>  (Los Angeles)\n<p>&nbsp;<a href=/lax/eng/6911564.html>Copywriter (freelance) </a>  (Los Angeles)\n<p>&nbsp;<a href=/lax/eng/6910787.html>Oracle Developer - 75k </a>  (Los Angeles)\n<p>&nbsp;<a href=/lax/eng/6909292.html>Web Software Engineer </a>  (Chatsworth, CA)\n<p><table width=90%><tr><td bgcolor=#cccccc>&nbsp;Mon Nov 18th</td></tr></table><p>\n<p>&nbsp;<a href=/lax/eng/6893428.html>Network Engineer preferably with MCSE </a>  (Woodland Hills)\n<p>&nbsp;<a href=/lax/eng/6890617.html>entry level web person </a>  (marina del rey)\n<p><table width=90%><tr><td bgcolor=#cccccc>&nbsp;Sun Nov 17th</td></tr></table><p>\n<p>&nbsp;<a href=/lax/eng/6881780.html>PHP Programmer </a>  (Kuala Lumpur, Malaysia)\n<p><table width=90%><tr><td bgcolor=#cccccc>&nbsp;Sat Nov 16th</td></tr></table><p>\n<p>&nbsp;<a href=/lax/eng/6866897.html>Webmaster and HTML Programmer in Venice </a>  (Venice, CA)\n<p>&nbsp;<a href=/lax/eng/6864480.html>Analog/Digital/Mixed mode/microprocessor designer </a>  (Pasadena area)\n<p>&nbsp;<a href=/lax/eng/6855540.html>Experienced WEB DESIGNERS </a>  (Glendale)\n<p><table width=90%><tr><td bgcolor=#cccccc>&nbsp;Fri Nov 15th</td></tr></table><p>\n<p>&nbsp;<a href=/lax/eng/6851371.html>Part Time Webmaster/Editor Needed For Adult Sites! </a>  (Orange County/Los Angeles)\n<p>&nbsp;<a href=/lax/eng/6849414.html>Integrator </a>  (Long Beach, CA)\n<p>&nbsp;<a href=/lax/eng/6848512.html>Senior Web Developer </a>  (Covina, CA)\n<p>&nbsp;<a href=/lax/eng/6845542.html>Are you a technical guru? </a>  (Santa Monica)\n<p>&nbsp;<a href=/lax/eng/6840568.html>configure quick php news manager on server </a>  (Los Angeles)\n<p>&nbsp;<a href=/lax/eng/6839622.html>Graphic/web designer </a>  (City of Industry, CA)\n<p><table width=90%><tr><td bgcolor=#cccccc>&nbsp;Thu Nov 14th</td></tr></table><p>\n<p>&nbsp;<a href=/lax/eng/6834810.html>Product Manager </a>  (Northern California)\n<p>&nbsp;<a href=/lax/eng/6827212.html>Senior Designer </a>  (Venice, CA)\n<p>&nbsp;<a href=/lax/eng/6824532.html>Lead Programmer </a>  (Los Angeles area)\n<p>&nbsp;<a href=/lax/eng/6819083.html>Web Developer </a>  (Los Angeles)\n<p>&nbsp;<a href=/lax/eng/6815148.html>ASP - Sequel Server Guru with E-Commerce Experience </a>  (Marina Del Rey)\n<p><table width=90%><tr><td bgcolor=#cccccc>&nbsp;Wed Nov 13th</td></tr></table><p>\n<p>&nbsp;<a href=/lax/eng/6807085.html>Need to Liquidate a Linux server - need help by linux expert - share $ </a>  (Studio City)\n<p>&nbsp;<a href=/lax/eng/6806989.html>SOFTWARE DEVELOPMENT & QA POSITIONS--InfoGenesis </a>  (Santa Barbara)\n<p>&nbsp;<a href=/lax/eng/6804712.html>J2EE Architect/Developer </a>  (Santa Barbara)\n<p>&nbsp;<a href=/lax/eng/6804453.html>PC TECH & WEB ADMINSTRATOR & VIDEO EDITING </a>  (LOS ANGELES)\n<p>&nbsp;<a href=/lax/eng/6803727.html>Computer Systems Validation Engineer </a>  (Irvine)\n<p><table width=90%><tr><td bgcolor=#cccccc>&nbsp;Tue Nov 12th</td></tr></table><p>\n<p>&nbsp;<a href=/lax/eng/6787463.html>PC Test Technician </a>  (Orange County)\n<p>&nbsp;<a href=/lax/eng/6786758.html>Webmaster & HTML Programmer </a>  (Santa Monica, CA)\n<p>&nbsp;<a href=/lax/eng/6779162.html>Web Developer Sought to Build Site </a>  (Los Angeles)\n<p>&nbsp;<a href=/lax/eng/6776410.html>HTML/PhotoShop Part-Time Work </a>  (Santa Monica)\n<p><table width=90%><tr><td bgcolor=#cccccc>&nbsp;Mon Nov 11th</td></tr></table><p>\n<p>&nbsp;<a href=/lax/eng/6771680.html>Oracle 11i Developer/DBA </a>  (SANFRANCISCO & LOS ANGELES)\n<p>&nbsp;<a href=/lax/eng/6771412.html>SQL database programming needed </a>  (Santa Barbara ONLY)\n<p>&nbsp;<a href=/lax/eng/6769124.html>Web developer with strong design experience wanted </a>  (Los Angeles, CA)\n<p>&nbsp;<a href=/lax/eng/6761922.html>web programmer/developer </a>  (Glendale)\n<p>&nbsp;<a href=/lax/eng/6759014.html>Web Developer </a>  (Los Angeles)\n<p><table width=90%><tr><td bgcolor=#cccccc>&nbsp;Sat Nov 9th</td></tr></table><p>\n<p>&nbsp;<a href=/lax/eng/6723774.html>  SQL database Programmer Wanted </a>  (Southern Calif.)\n<p><table width=90%><tr><td bgcolor=#cccccc>&nbsp;Fri Nov 8th</td></tr></table><p>\n<p>&nbsp;<a href=/lax/eng/6718814.html>Junior/Mid Level Web Programmer </a>  (West Los Angeles)\n<p>&nbsp;<a href=/lax/eng/6714309.html>SAP SD w/ Variant </a>  (Palo Alto, CA)\n<p>&nbsp;<a href=/lax/eng/6711230.html>Technology Director, Los Angeles </a>  (West Hollywood)\n<p>&nbsp;<a href=/lax/eng/6709573.html>C++/CORBA/Messaging Developer </a>  (Los Angeles)\n<p>&nbsp;<a href=/lax/eng/6709416.html>Help Desk Supervisor - ·50% supervising and 50% hand-on technical </a>  (Santa Monica)\n<p><table width=90%><tr><td bgcolor=#cccccc>&nbsp;Thu Nov 7th</td></tr></table><p>\n<p>&nbsp;<a href=/lax/eng/6698162.html>QA Test Lead </a>  (S.F.Valley)\n<p>&nbsp;<a href=/lax/eng/6694161.html>Telemarketing Closers Wanted !!!! </a>  (SFV)\n<p><table width=90%><tr><td bgcolor=#cccccc>&nbsp;Wed Nov 6th</td></tr></table><p>\n<p>&nbsp;<a href=/lax/eng/6680410.html>Web designer/programmer wanted for Splash Spa </a>  (Los Angeles)\n<p>&nbsp;<a href=/lax/eng/6680390.html>Web designer/programmer needed for InMagazine </a>  (Los Angeles)\n<p>&nbsp;<a href=/lax/eng/6678222.html>QA Engineer </a>  (Los Angeles/Santa Monica)\n<p>&nbsp;<a href=/lax/eng/6675268.html>INSIDE SALES PEOPLE WANTED </a>  (Sherman Oaks)\n<p>&nbsp;<a href=/lax/eng/6674794.html>Analytics Configurator - Technical Lead ($50 to $55 / hr) W2 </a>  (SFV)\n<p>&nbsp;<a href=/lax/eng/6672388.html>SAS Pre/Post Sales Support Engineers </a>  (Los Angeles)\n<p>&nbsp;<a href=/lax/eng/6672280.html>SAS Pre/Post Sales Support Engineers </a>  (San Diego)\n<p>&nbsp;<a href=/lax/eng/6670127.html>admin support. and help desk </a>  (beverly hills)\n<p>&nbsp;<a href=/lax/eng/6665588.html>Java/cgi programmer to update scripts </a>  (Telecommute)\n<p><table width=90%><tr><td bgcolor=#cccccc>&nbsp;Tue Nov 5th</td></tr></table><p>\n<p>&nbsp;<a href=/lax/eng/6654272.html>JD Edwards Guru/Consultant </a>  (San Fernando Valley)\n<p>&nbsp;<a href=/lax/eng/6654191.html>Network Administrator </a>  (San Fernando Valley)\n<p>&nbsp;<a href=/lax/eng/6652699.html>Need local freelance creative web designer! </a>  (Los Angeles)\n<p>&nbsp;<a href=/lax/eng/6650296.html>HIPPA Consultants </a>  (O.C.)\n<p>&nbsp;<a href=/lax/eng/6644384.html>Customer Support, Technical Support, Recruiting </a>  (Los Angeles)\n<p>&nbsp;<a href=/lax/eng/6644292.html>E-Commerce and Web Programmer </a>  (Los Angeles)\n<p>&nbsp;<a href=/lax/eng/6641798.html>Computer Help Needed </a>  (Tarzana)\n<p><table width=90%><tr><td bgcolor=#cccccc>&nbsp;Mon Nov 4th</td></tr></table><p>\n<p>&nbsp;<a href=/lax/eng/6634713.html>software integration engineer </a>  (Santa Monica, CA)\n<p>&nbsp;<a href=/lax/eng/6632202.html>Senior Server Engineer </a>  (Los Angeles)\n<p>&nbsp;<a href=/lax/eng/6632183.html>Pre/Post Sales Engineer </a>  (Los Angeles)\n<p>&nbsp;<a href=/lax/eng/6632151.html>Software Quality Assurance Engineer </a>  (Los Angeles)\n<p>&nbsp;<a href=/lax/eng/6630703.html>Adult Search Engine Optimization Expert </a>  (United States)\n</table>\n</blockquote>\n<p align=center><font size=4><a href=index100.html>next 100 postings</a></font><br><hr><br>Copyright &copy; 2002 craigslist<br><br></body>\n</html>\n',1038971411);
INSERT INTO tiki_link_cache VALUES (3,'http://tikiwiki.sf.net/','<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1DTD/xhtml1-transitional.dtd\">\r\n<html>\r\n<head>\r\n	<title>tikiwiki :: open source excellence, one line at a time</title>\r\n\r\n	<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\r\n	<meta name=\"keywords\" content=\"tiki, wiki, tikiwiki, open source, osi, php, lamp, cms, articles, image, file, gallery, ldap\" />\r\n	<meta name=\"description\" content=\"An open source web application providing a full article, wiki, local/LDAP user/group management, file and image galleries, and more.\" />\r\n\r\n	<link rel=\"stylesheet\" href=\"mozilla.css\" type=\"text/css\" media=\"screen\" />\r\n\r\n	<script src=\"utils.js\" type=\"text/javascript\"></script>\r\n	<script src=\"viewport.js\" type=\"text/javascript\"></script>\r\n	<script src=\"global.js\" type=\"text/javascript\"></script>\r\n	<script src=\"cookie.js\" type=\"text/javascript\"></script>\r\n	<script src=\"tabs.js\" type=\"text/javascript\"></script>\r\n\r\n	<script type=\"text/javascript\">\r\n	// <![CDATA[\r\n	\r\n	function chooseStyle( newstyle )\r\n	{\r\n		var expdate = new Date();\r\n		expdate.setTime(expdate.getTime() + (1000*3600*24*365));\r\n		document.cookie = \'style=\' + newstyle + \'; expires=\' + expdate.toGMTString() + \'; path=/\';\r\n		self.location = self.location;\r\n	}	\r\n\r\n	if( document.cookie.indexOf( \'style=1\' ) >= 0 )\r\n		document.write( \'<link rel=\"stylesheet\" type=\"text/css\" href=\"winxp.css\">\\n\' );\r\n	else if( document.cookie.indexOf( \'style=2\' ) >= 0 )\r\n		document.write( \'<link rel=\"stylesheet\" type=\"text/css\" href=\"aqua.css\">\\n\' );\r\n	else document.write( \'<link rel=\"stylesheet\" type=\"text/css\" href=\"mozilla.css\">\\n\' );\r\n\r\n	/*\r\n	 * TabParams        Change these to customize behavior.\r\n	 *\r\n	 * useClone         If true, uses a clone of the tabs beneath the contents.\r\n	 *\r\n	 * alwaysShowClone  If true, the clone will be visible at all times.\r\n	 *                  Otherwise, the clone will be visible only when the active \r\n	 *                  content div\'s girth extends beyond the viewport height.\r\n	 * \r\n	 * eventType        The event that triggers a tab. mouseover, mousedown, mouseup.\r\n	 *\r\n	 * tabTagName       Speeds performance. Use \"*\" for multiple types.\r\n	 *\r\n	 */\r\n\r\n	TabParams = {\r\n		useClone         : false,\r\n		alwaysShowClone  : false,\r\n		eventType        : \"click\",\r\n		tabTagName       : \"span\"\r\n		};\r\n\r\n	// ]]>\r\n	</script>\r\n</head>\r\n<body onload=\"tabInit()\">\r\n\r\n<div id=\"pagehead\">\r\n	<img src=\"banner_01.png\" width=\"400\" height=\"50\" border=\"0\" alt=\"banner\" title=\"\"/>\r\n	<h1>TIKIWIKI</h1>\r\n	<p>Open Source Excellence, One Line at a Time</p>\r\n</div> <!-- div pagehead -->\r\n\r\n<div id=\"pagemenu\">\r\n	<ul>\r\n		<li><a href=\"http://sourceforge.net/projects/tikiwiki/\">Project Page</a></li>\r\n		<li><a href=\"http://sourceforge.net/tracker/?atid=506847&amp;group_id=64258&amp;func=browse\">Support</a></li>\r\n		<li><a href=\"http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/tikiwiki/\">View CVS</a></li>\r\n		<li><a href=\"http://sourceforge.net/tracker/?atid=506849&amp;group_id=64258&amp;func=browse\">Request Feature</a></li>\r\n		<li><a href=\"http://sourceforge.net/tracker/?func=add&amp;group_id=64258&amp;atid=506846\">Report Bug</a></li>\r\n		<li><a href=\"http://sourceforge.net/tracker/?func=browse&group_id=64258&amp;atid=506846\">Browse Bugs</a></li>\r\n		<li><a href=\"http://sourceforge.net/forum/?group_id=64258\">Forums</a></li>\r\n		<li><a href=\"http://sourceforge.net/mail/?group_id=64258\">Mail Lists</a></li>\r\n		<li><a href=\"http://alt.thetinfoilhat.com/\">Demo Site</a></li>\r\n		<li><a href=\"cvsstats/index.html\">CVS stats</a></li>\r\n		<li><a href=\"http://sourceforge.net/pm/?group_id=64258\">Tasks</a></li>\r\n	</ul>\r\n</div> <!-- div menubar -->\r\n\r\n<div id=\"pagebody\">\r\n\r\n	<div class=\"tabs\">\r\n		<span id=\"tab1\" class=\"tab tabActive\">News</span>\r\n		<span id=\"tab2\" class=\"tab\">Partners</span>\r\n		<span id=\"tab3\" class=\"tab\">Features</span>\r\n		<span id=\"tab4\" class=\"tab\">Downloads</span>\r\n		<span id=\"tab6\" class=\"tab\">Meet the team</span>\r\n		<span id=\"tab5\" class=\"tab\">Join the team</span>\r\n		<span id=\"tab7\" class=\"tab\">Upcoming version</span>\r\n	</div>\r\n	\r\n	<div id=\"content1\" class=\"content\">\r\n	\r\n		<h1>All The News that\'s Fit to Print</h1>\r\n\r\n		<h2>Tiki 1.6.1 released!</h2>\r\n		<div class=\"published\">Published: 26th May 2003</div>\r\n		<p>A bugfix version of Tiki 1.6. No new features or improvements just many bugfixes from the 1.6 release.\r\n		</p>\r\n\r\n\r\n		<h2>Galaxia documentation unleashed</h2>\r\n		<div class=\"published\">Published: 19th May 2003</div>\r\n		<p>The Galaxia project is the Workflow engine that will be used in Tiki 1.7+, the engine is already available in CVS in alpha-status, the documentation describes how to use the Workflow engine, how to create a process and execute it. The tutorial should be working with the current CVS HEAD so you can try it out.<br/>\r\n		Documents:<br/>\r\n		<a href=\"Galaxia_manual.pdf\">Galaxia documentation and user manual</a><br/>\r\n		<a href=\"http://tikiwiki.sourceforge.net/Galaxia_introduction.pdf\">Galaxia introduction and concepts</a>\r\n		</p>\r\n		\r\n		<h2>Tiki 1.6 \"Tau Ceti\" Released!</h2>\r\n		<div class=\"published\">Published: 5th May 2003</div>\r\n		<p>Plenty of good news; install script added, notable performance improvement, less memory consumption, and alot of new features!  This version also fixes several bugs found in Tiki 1.5 and 1.6RC1 users that want\r\n		to enjoy the new features are encouraged to upgrade.</p>\r\n		<h3>Principal Changes/Additions (<a href=\"changelog.txt\">Read the full changelog</a>)</h3>\r\n		<ul>\r\n			<li>New MyTiki section grouping Webmail, Newsreader, User Calendar, User notepad, User personal Files, User tasks, User preferences, User bookmarks, User menus and User messages.</li>\r\n			<li>Newsreader (NTTP) added</li>\r\n			<li>User calendar added, with daily and weekly views, import/export events from/to Outlook, list and search events and event reminders</li>\r\n			<li>User files section, users can upload and download personal files, a quota can be assigned by admin</li>\r\n			<li>User notepad added, users can write notes, view notes unparsed or parsed as wiki pages. Download notes, upload notes. Etc.</li>\r\n			<li>User tasks added and user tasks module added, users can add tasks, track activity completion,etc.</li>\r\n			<li>User menus added: users can add personal links to the application menu.</li>\r\n			<li>Directory added. Dmoz-alike directory with categories, URL suggestion and validation, top sites, new sites listing and many features.</li>\r\n			<li>Internal messaging system added with option to send broadcast messages. Users can also send messages to groups, admin can broadcast to all users, messages can have priorities.</li>\r\n			<li>LDAP Authentication</li>\r\n			<li>Notable performance improvements and less memory consumption.</li>\r\n			<li>Graphviz integration, wiki graphs can be displayed and navigated.</li>\r\n			<li>Themes can now optionally redefine any Tiki template.</li>\r\n			<li>Ephemerides</li>\r\n			<li>Wiki page footnotes, personal notes per-user about wiki pages.</li>\r\n			<li>Wiki structures added, Wiki tables of contents can be created and slides can be displayed from a Wiki structure.</li>\r\n			<li>Theme control center added, opntionally admin can setup different themes for sections, categories, or even individual objects.</li>\r\n			<li>Many plugins added: SQL, INCLUDE, CODE, AVATAR, CENTER, DL, GAUGE, SPLIT.</li>\r\n			<li>Minor edits of wiki pages are available.</li>\r\n			<li>Inter wiki links added</li>\r\n			<li>New themes added.</li>\r\n			<li>Who is online modules and other new modules added.</li>\r\n			<li>Improved permission admin interface. Permissions can now be grouped in levels and levels can be assigned to groups.</li>\r\n			<li>Wiki pages can now be locked at a user-level.</li>\r\n			<li>Many bugs fixed, many surprises, many improvements.</li>\r\n		</ul>\r\n\r\n\r\n		<h2>Tiki\'s New Look .. or three!</h2>\r\n		<div class=\"published\">Published: 23rd April 2003 by Mark Limburg</div>\r\n		<p>After much struggle (I have a serious love/hate thing going on with CSS and XHTML), our new homepage is done.  This is a 100% CSS and XHTML page, with NO tables (check the source, I\'m not kidding) and with some javascripting magic, we\'ve plenty of useful eye-candy from a style swapper to a full multi-tabbed interface.  Big thanks to those at <a href=\"http://www.dhtmlkitch.com/\">DHTML Kitchen</a> for the inital scripts for the tabs.  So, dive in and enjoy!.</p>\r\n\r\n		<h2>Introduction to Galaxia: A PHP based workflow engine</h2>\r\n		<div class=\"published\">Published: 12th April 2003</div>\r\n		<p>Workflow project has started!  Galaxia, announced the release of the first project document: \"<a href=\"http://tikiwiki.sourceforge.net/Galaxia_introduction.pdf\">Galaxia introduction and concepts</a>\".  Galaxia is a Workflow engine based on Openflow (http://www.openflow.it). It\'s being designed and coded by an independant group of developers lead by Tiki\'s project manager Garland Foster.</p>\r\n\r\n\r\n	</div>\r\n	\r\n	<div id=\"content2\" class=\"content\">\r\n	\r\n		<h1>Tiki partnershipts</h1>\r\n\r\n		<p>This is a list of companies and projects that actively cooperate with Tiki.</p>\r\n		<img border=\'0\' src=\'jgraph.gif\' alt=\'jgraphpad logo\' />\r\n		<p><a href=\"http://jgraph.sourceforge.net/\"><b>JGraphPad</b></a> JGraphPad is a very advanced and powerful yet simple to use tool to create drawings, graphs, diagrams and drawings. Tiki uses JGraphPad to edit drawings that can be inserted in Wiki pages, Blogs, Articles and other Tiki objects. So you can create a drawing insert it in some page, edit it later or even better let other users edit the drawing.</p>\r\n		<hr/>\r\n		<img border=\'0\' src=\'phpopentracker.jpeg\' alt=\'phpopentracker logo\' />\r\n		<p><a href=\"http://www.phpopentracker.de/\"><b>PHPOpenTracker</b></a> is a framework solution for the analysis of website traffic and visitor analysis. Tiki provides a template based GUI to phpopentracker so you can see statistics of all sorts about your site.</p>\r\n		<hr/>\r\n		<img src=\'htmlarea.gif\' alt=\'htmlarea logo\' />\r\n		<p><a href=\"http://www.interactivetools.com/products/htmlarea/\"><b>htmlArea</b></a> is a WYSIWYG editor replacement for any textarea field. Tiki uses htmlarea to provide WYSIWYG editing in some objects. The most notable usage is in Tiki blogs where you can edit a blog post using a WYSIWYG editor without worries.<br/>\r\n		Another usages include: editing user modules, dynamic content system and HTML pages.</p>\r\n		<hr/>\r\n		<img src=\'hawhaw.gif\' alt=\'Haw Haw logo\' />\r\n		<p><a href=\"http://www.hawhaw.de/\"><b>HawHaw</b></a> HAWHAW stands for \"HTML and WML hybrid adapted Webserver\"\r\n		and is a PHP-based toolkit to create universal mobile applications. Tiki will be WML and PDA enabled from version 1.7\r\n		using HawHaw.</p>\r\n		\r\n		\r\n\r\n	</div>\r\n	\r\n	<div id=\"content3\" class=\"content\">\r\n\r\n		<h1>Tiki, Feature Overview</h1>\r\n\r\n		<h2>Design Features</h2>\r\n		<ul>\r\n			<li>Outputs valid XHTML code</li>\r\n			<li>Uses CSS to deploy themes</li>\r\n			<li>Permission system using groups and users</li>\r\n			<li>Uses PEAR::DB to access databases</li>\r\n			<li>Multi-language support</li>\r\n			<li>Template-based using Smarty, customizable layout and look and feel</li>\r\n			<li>Caching system for external pages and images</li>\r\n			<li>Externally linked images are downloaded to the Tiki image base</li>\r\n			<li>Rankings for all the features</li>\r\n			<li>Integrated Search engine</li>\r\n		</ul>\r\n		\r\n		<h2>Tiki Capabilities</h2>\r\n		<ul>\r\n			<li>A Wiki</li>\r\n			<li>Image Galleries</li>\r\n			<li>Weblogs/Journals</li>\r\n			<li>File Galleries</li>\r\n			<li>Polls</li>\r\n			<li>Articles and Submissions</li>\r\n			<li>Chat</li>\r\n			<li>Forums</li>\r\n			<li>FAQs</li>\r\n			<li>Quizzes</li>\r\n			<li>HTML and Dynamic HTML Pages (ie: Live Updates!)</li>\r\n			<li>RSS Feeds</li>\r\n			<li>Templates</li>\r\n			<li>Editable Drawings and Figures</li>\r\n			<li>Comments</li>\r\n			<li>Surveys</li>\r\n			<li>Webmail</li>\r\n			<li>Newsletters</li>\r\n			<li>Trackers</li>\r\n			<li>Internal Messaging</li>\r\n			<li>Calendar</li>\r\n			<li>User Tasks</li>\r\n			<li>User Notepad</li>\r\n			<li>Links Directory</li>\r\n			<li>User Files</li>\r\n			<li>Ephemerides</li>\r\n			<li>And a grand total of more than 375 features not listed!</li>\r\n		</ul>\r\n	</div>\r\n	\r\n	<div id=\"content4\" class=\"content\">\r\n		<h1>File Downloads</h1>\r\n		\r\n		<h2>TikiWiki, Stable</h2>\r\n		<div class=\"download\"><a href=\"https://sourceforge.net/project/showfiles.php?group_id=64258\">Download</a></div>\r\n		<p>\r\n			Grab the latest stable release of TikiWiki for your site today,  freshly served \r\n			from the sourceforge download server.\r\n		</p>\r\n		\r\n		<h2>TouchGraphWikiBrowser</h2>\r\n		<div class=\"download\"><a href=\"https://sourceforge.net/project/showfiles.php?group_id=64258&amp;release_id=127019\">Download</a></div>\r\n		<p>\r\n			A graphical visualization tool for the Wiki. This very nice Java application\r\n			will show you a graph of the Wiki along with the HTML content of the current\r\n			page and you can navigate the wiki following the links between nodes. A very\r\n			nice complement to the Wiki.\r\n		</p>\r\n		\r\n		<h2>wBloggar 3.0</h2>\r\n		<div class=\"download\"><a href=\"https://sourceforge.net/project/showfiles.php?group_id=64258&amp;release_id=127019\">Download</a></div>\r\n		<p>\r\n			One of the best desktop applications to manage weblogs and of course it is ready\r\n			to be used with Tiki weblogs. Write weblog posts from your windows desktop, save\r\n			posts edit old posts, remove posts, etc.\r\n		</p>\r\n  \r\n		<h2>Taglines Pack 1</h2>\r\n		<div class=\"download\"><a href=\"https://sourceforge.net/project/showfiles.php?group_id=64258&amp;release_id=127019\">Download</a></div>\r\n		<p>\r\n			A zipped txt file with a collection of computer-related taglines that are ready \r\n			to be uploaded to the tiki taglines system. Upload the taglines and use the {cookie}\r\n			syntax to display a random tagline in a wiki page, forum post or comment. \r\n		</p>\r\n\r\n		<h2>Game Pack 1</h2>\r\n		<div class=\"download\"><a href=\"https://sourceforge.net/project/showfiles.php?group_id=64258&amp;release_id=127019\">Download</a></div>\r\n		<p>\r\n			A collection of very nice flash games ready to be uploaded to your tiki \"games\" \r\n			directory. Installation instructions are very simple and included in the package,\r\n			about 20 games are ready to play at one click.\r\n		</p>\r\n\r\n		<h2>Spellchecking Dictionary for English</h2>\r\n		<div class=\"download\"><a href=\"https://sourceforge.net/project/showfiles.php?group_id=64258&amp;release_id=127019\">Download</a></div>\r\n		<p>\r\n			English version of the dictionary needed for the spellchecker (or it won\'t do\r\n			anything).\r\n		</p>\r\n		\r\n 	</div>\r\n	\r\n	<div id=\"content5\" class=\"content\">\r\n		<h1>CVS, Code and Developers, Oh My!</h1>\r\n\r\n		<h2>Can You Help?</h2>\r\n		<p>Tiki is easy to customize and extend, there\'re a lot of planned features so if you want to contribute, please contact Luis.  Remember, if you are not a programmer, you can always help out with adding themes, languages, bug reports, and your ideas to Tiki.</p>\r\n		<p>Contact email: <a href=\"mailto:lrargerich@yahoo.com\">lrargerich@yahoo.com</a></p>\r\n	</div>	\r\n	\r\n	\r\n	\r\n	<div id=\"content6\" class=\"content\">\r\n		<h1>The tiki develoment team</h1>\r\n		<table width=\"100%\">	\r\n			<tr>\r\n				<td width=\"50%\" valign=\"top\" id=\"team_col1\" >\r\n					<p>\r\n					<img style=\"float:left;margin-right:4px;\" src=\"team/luis.gif\" />\r\n					<a href=\"mailto:luis@fuegolabs.com\"><b>Luis Argerich (29)</b></a>. Buenos Aires, Argentina<br />\r\n					<i>\"It\'s just data\"</i><br/>\r\n					Teacher at the University of Buenos Aires, Software developer,\r\n					open source fundamentalist and Bridge player. Author of many Tiki\r\n					features. \r\n					<br /><small>YahooId: lrargerich</small>\r\n					</p>\r\n					\r\n					<p>\r\n					\r\n					<!--<img width=\"45\" height=\"59\"  style=\"float:left;margin-right:4px;\" src=\"team/Garland2.jpg\" />-->\r\n					<b>Garland Foster (44)</b>. Green Bay, Wisconsin.<br />\r\n					<i>\"Go Packers go!\"</i><br />\r\n					From the frozen tundra Mr Foster is helping the Tiki team to organize the chaos inherent\r\n					to every open-source project. He was in charge of the Tiki documentation until version\r\n					1.6 and authored the install script and the Workflow engine: Galaxia.\r\n					</p>\r\n					\r\n					<p>\r\n					<a href=\"mailto:damienmckenna@genesi-usa.com\"><b>Damien McKenna (27)</b></a>.Sanford, Florida, USA<br />\r\n					<i>\"For years I\'d hoped that someone would write a powerful content management \r\n					system that did it all, and then I found Tiki, and they have!\"</i><br />\r\n					Web developer for Genesi Sarl, an international PowerPC-based systems \r\n					developer.  I work with a team of others from around the US.\r\n					</p>\r\n					\r\n					<p>\r\n					<img width=\"45\" height=\"59\"  style=\"float:left;margin-right:4px;\" src=\"team/patrick.jpg\" />\r\n					<a href=\"mailto:\"><b>Patrick Van der Veken (34)</b></a>. Scherpenheuvel-Zichem, Belgium<br />\r\n					<i>Awesome, just awesome </i><br />\r\n					UNIX adept, trying to translate Tiki into Dutch, trying to understand \r\n					how Tiki works :rolleyes:.  In daily life, an independant UNIX sysadmin, \r\n					currently working on Production Engineering projects\r\n					<br /><small>ICQ: 4000286</small>\r\n					</p>\r\n					\r\n					<p>\r\n					<img width=\"45\" height=\"59\"  style=\"float:left;margin-right:4px;\" src=\"team/Greg_Martin.jpg\" />\r\n					<a href=\"mailto:gmartin@gmartin.org\"><b>Greg Martin</b>.</a>Perkasie, PA, US<br />\r\n					<i>\"Tiki is the fastest moving piece of software I\'ve ever seen.\"</i><br />\r\n					IT consultant/manager & computer hobbyist.  Self-taught Linux/Apache/MySQL.PHP. Tikiwiki is my first Open Source project.  Participating mostly to help flesh out Tiki ideas and to understand the community aspects of OS.\r\n					<br /><small>MSN/gregjmartin20@hotmail.com AOL/gregmartin20</small>\r\n					</p>\r\n					\r\n					<p>\r\n					<img width=\"45\" height=\"59\"  style=\"float:left;margin-right:4px;\" src=\"team/flothumb.jpg\" />\r\n					<a href=\"mailto:\"><b>Flo G. (33)</b></a>.Near Munich, Germany<br />\r\n					<i>\"A wiki with some more features! Some ...?\"</i><br />\r\n					Physican, working as UNIX and DB Admin. Playing basketball, UT and Schafkopf. I go sledding in winter and climbing in summer. vi is my editor and fvwm2 my windowmanager.\r\n					<br /><small></small>\r\n					</p>\r\n					\r\n					<p>\r\n					<img width=\"45\" height=\"59\"  style=\"float:left;margin-right:4px;\" src=\"team/ricardo.jpg\" />\r\n					<a href=\"mailto:ricardo.gladwell@btinternet.com\"><b>Ricardo Gladwell.</b></a>London, UK.<br />\r\n					<i>\"My god, it\'s full of stars!\"</i><br />\r\n					Web developer, Unix sysadmin and general jack-of-all-trades. I enjoy reading, writing, films and theater. I stumbled across Tiki when I was looking for a wiki to replace PHP-Nuke for my open-content roleplaying project, <a href=\"http://www.netbookofplanes.org/\">The Netbook of Planes</a> and never looked back.\r\n					<br /><small>YahooId:axonrg</small>\r\n					</p>\r\n					\r\n					<p>\r\n					<img width=\"45\" height=\"59\"  style=\"float:left;margin-right:4px;\" src=\"team/tetamose.jpg\" />\r\n					<a href=\"http://mose.fr/cv/?loc=en\"><b>Mose (35)</b></a>. Paris, France<br />\r\n					<i>\"That\'s a framework, a heaven for coders !\"</i><br />\r\n					Unqualified free software designer, fast coder, webdesigner, unix\r\n					admin, geek of all trades, involved in building collaborative and \r\n					free software. Speaks shell, sql, php and perl fluently.\r\n					<br /><small>irc: #tikiwiki on irc.freenode.net</small>\r\n					</p>\r\n				\r\n				\r\n				</td>\r\n				<td width=\"50%\" valign=\"top\" id=\"team_col2\" >\r\n					<p>\r\n					<img width=\"45\" height=\"59\" style=\"float:left;margin-right:4px;\" src=\"team/poli.jpg\" />\r\n					<a href=\"mailto:eduardo@polidor.net\"><b>Eduardo Polidor (30)</b></a>. Sao Paulo, Brazil<br />\r\n					<i>\"It\'s snowing in Sao Paulo.\"</i><br/>\r\n					Used to teach at the university and is now managing projects using the\r\n					web for health and finnancial transactions. He comes from the dark side\r\n					to help the Tiki team achieve world domination.\r\n					<br /><small>YahooId: epolidor</small>\r\n					</p>\r\n					\r\n					<p>\r\n					<img width=\"45\" height=\"59\"  style=\"float:left;margin-right:4px;\" src=\"team/fotogmuslera.jpg\" />\r\n					<a href=\"mailto:gmuslera@internet.com.uy\"><b>Gustavo Muslera</b></a>. Montevideo, Uruguay<br />\r\n					<i>\"I need some inspiration\"</i><br />\r\n					An active participant in the Tiki devel mailing list.\r\n					<br /><small>ICQ:16596516</small>\r\n					</p>\r\n					\r\n					<p>\r\n					<img width=\"45\" height=\"59\"  style=\"float:left;margin-right:4px;\" src=\"team/ohertel.jpg\" />\r\n					<a href=\"mailto:rom@readonly.de\"><b>Oliver \'ROM\' Hertel (32)</b></a>.Frankfurt, Germany.<br />\r\n					<i>Woah, that easy!</i><br />\r\n					Professional java and php developer, using TikiWiki at his employee\'s company, too.\r\n					<br /><small>ICQ: 329769157 YahooId:ohertel</small>\r\n					</p>\r\n					\r\n					<p>\r\n					<a href=\"mailto:tiki@marclaporte.com\"><b>Marc Laporte</b></a>.MontrÃ©al, Canada<br />\r\n					<i>I was looking for a Wiki. I found so much more!!</i><br />\r\n					IT consultant and open source enthusiast\r\n					<br /><small></small>\r\n					</p>\r\n					\r\n					<p>\r\n					<img width=\"45\" height=\"59\"  style=\"float:left;margin-right:4px;\" src=\"team/markl.jpeg\" />\r\n					<a href=\"mailto:\"><b>Mark Limburg</b></a><br />\r\n					<i>\"Don\'t give *me* that kinkier than thou look.\"</i><br />\r\n					Mark is in charge of the production of themes and usability of Tiki. He dessigned this new\r\n					home page for us here at SourceForge and is working in a brand new set of themes for Tiki.\r\n					<br /><small></small>\r\n					</p>\r\n					\r\n					<p>\r\n					<img width=\"45\" height=\"59\"  style=\"float:left;margin-right:4px;\" src=\"team/Mario.gif\" />\r\n					<a href=\"mailto:mariomene@stopspam.yahoo.com\"><b>Mario Mene (38)</b></a>. Rome, Italy<br />\r\n					<i>\"need a solution?\"</i><br />\r\n					General purpose italian engineer, knows nothing about everything, using Tiki for his personal website. First experience in open source distributed development.\r\n					<br /><small></small>\r\n					</p>\r\n					\r\n					<p>\r\n					<a href=\"mailto:tiki-stuff@heltzel.org\"><b>Dennis Heltzel</b></a>.Pottstown, PA, USA (near Philadelphia)<br />\r\n					<i>\"I wish I\'d found Tiki first\"</i><br />\r\n					Oracle DBA, Linux/Solaris Sys Admin, Webmaster for a number of small clubs devoted to my other hobby, raising tropical fish.\r\n					<br /><small></small>\r\n					</p>\r\n					\r\n					<p>\r\n					<img width=\"56\" height=\"70\"  style=\"float:left;margin-right:4px;\" src=\"team/aaron.jpg\" />\r\n					<a href=\"mailto:\"><b>Aaron Holmes (20)</b></a>. Niagara falls, Canada.<br />\r\n					<i>\"send a quote!\"</i><br />\r\n					Vice President and senior programmer of a small web development and programming firm located in Southern Ontario, Canada. Open source advocate and Tiki enthusiast.\r\n					<br /><small>Yahooid: aholmes9 ICQ: 24787134</small>\r\n					</p>\r\n					\r\n					<p>\r\n					<img width=\"45\" height=\"59\"  style=\"float:left;margin-right:4px;\" src=\"team/JoanVilarino.jpg\" />\r\n					<a href=\"mailto:lechuckdapirate@lycos.es\"><b>Joan VilariÃ±o (35)</b></a>.Barcelona, Spain.<br />\r\n					<i>\"I think we have a winner\"</i><br />\r\n					I code since I remember, I also so some network admin functions. I currently work at IT department in a construction enterprise.\r\n					<br /><small>ICQ:553088 â€“ YahooId:jai_bee â€“ MSNM:jaibee_the_jaibeer@hotmail.com</small>\r\n					</p>\r\n					\r\n					<p>\r\n					<img width=\"45\" height=\"59\"  style=\"float:left;margin-right:4px;\" src=\"team/\" />\r\n					<a href=\"mailto:\"><b></b></a><br />\r\n					<i></i><br />\r\n					\r\n					<br /><small></small>\r\n					</p>\r\n				\r\n				</td>\r\n			\r\n			</tr>\r\n		</table>\r\n	</div>\r\n	\r\n	<div id=\"content7\" class=\"content\">\r\n		<h1>Previewing Tiki 1.7 -Eta Carinae-</h1>\r\n		<p>This is just a list of <i>some</i> features in Tiki 1.7</p>\r\n		<p>Tiki 1.7 will feature a bunch of new features, many usability improvements and a general improvement\r\n		in usability and look and feel.</p>\r\n		<p><b>Better UI</b>: user interfaces will be improved adding icons and improving the layout and alignement\r\n		of tables. Listings can now be configured selecting which columns to display.</p>\r\n		<p><b>Amazing forums!</b>: the forums section will feature a lot of improvements matching top-level forum players like phpBB and others:\r\n			<ul>\r\n				<li>Forum posts can be automatically forwarded to an email address (outbound email)</li>\r\n				<li>Forum posts can be read from an email account (inbound email)</li>\r\n				<li>A group can be set as moderator, then all the group users are moderators</li>\r\n				<li>Forum posts can have attachments</li>\r\n				<li>Improved editor for forum posts including helplinks</li>\r\n				<li>Topic smileys can be used when posting topics</li>\r\n				<li>Topic summaries allowed when posting a new topic</li>\r\n				<li>Moderation queue added, posts from users without auto-approve permission are send to an approval queue</li>\r\n				<li>Posts can be reported to moderators</li>\r\n				<li>User information configurable: user name, level, number of posts, online status, etc.</li>\r\n				<li>Private responses using tiki internal messages are allowed</li>\r\n				<li>Moderators can moved/remove/merge/split posts. Many posts can be moved/removed at once.</li>\r\n				<li>Posts can be converted into a new topic</li>\r\n				<li>And believe us: more!</li>\r\n			</ul>\r\n		</p>\r\n		<p><b>Blogs revamped!</b>: The tiki blogs will have now the same or even more features than other blogging PHP packages:\r\n			<ul>\r\n				<li>Multi-page posts supported, easy and sweet</li>\r\n				<li>Improved interface to add images to blog posts</li>\r\n				<li>Permalinks supported</li>\r\n				<li>Trackback pings supported (full implementation with auto-discovery)</li>\r\n				<li>Blog headings can be configured</li>\r\n				<li>Send blog posts by email feature</li>\r\n				<li>WYSIWYG editing mode for blog posts (optional)</li>\r\n			</ul>\r\n		</p>\r\n		<p><b>Multi page articles</b>: Now articles and reviews can have any number of pages with a very simple\r\n		syntax to define pages, automatic navigation is added to multi-page articles.\r\n		</p>\r\n		<p><b>Banning system</b>: Users can be banned by IP or username from one or many sections of Tiki, the system accepts\r\n		regular expressions for user names and wildcards for IPs (IP ranges). The rules can be configured to be active in a specified period of time implementing\r\n		a suspension mechanism.\r\n		</p>\r\n		<p><b>Live support system</b>: A revolutionary new approach to live-support chat, without frames or browsers reloading. Tiki users can be\r\n		configured as operators, users can \"request\" support and operators accept support calls starting a one-on-one chat. You can now chat with\r\n		your users if they have a problem!\r\n		</p>\r\n		<p><b>Workflow(!)</b>: A full-fledged workflow engine is added to Tiki (Galaxia), the engine implements an activity-based\r\n		workflow where activities are represented as PHP scripts, interactive activities are modeled as a combination of one PHP script and one\r\n		Smarty template. A process modeler including a process graph, wizards and editors are included to create, monitor and execute processes.\r\n		Processes can be imported/exported using an XML format. This opens a whole new world of opportunities to your Tiki!.\r\n		</p>\r\n		<p><b>Charts and rankings</b>:\r\n		A new feature where carts and rankings can be created, weekly, daily, monthly or real-time charts can be created. \r\n		</p>\r\n		<p><b>JgraphPad integration</b>:\r\n		Jgraphpad is now the official editor for drawings that can be then included in Wiki pages, articles, blog posts and other Tiki objects.\r\n		Using JGP you can create really powerful and nice looking graphs, diagrams and other drawings. An amazing tool.\r\n		</p>\r\n		<p><b>PDA & WML support</b>:\r\n		Tiki is now integrated to HawHaw, and the 1.7 version will feature a PDA&WML accesible Wiki. Yes! you will be able to browse your\r\n		Wiki using a PDA or cell-phone.\r\n		</p>\r\n		<p><b>Wiki2PDF features</b>:\r\n		Wiki pages can be converted to PDF, a Wiki structure can be converted to PDF generating a booklet. This is extremely useful\r\n		in documentation projects.\r\n		</p>\r\n		<p><b>Improved uploading</b>: In image galleries, file galleries and user files up to 6 files can now be uploaded in a single post without using the batch upload feature.\r\n		</p>\r\n		<p><b>WYSIWYG editor(!)</b>:\r\n		A WYSIWYG HTML editor is added to Tiki features where HTML editing is supported and expected. Using the editor is optional.\r\n		HTMLArea is used as the WYSIWYG editor.		\r\n		</p>\r\n		<p><b>Search-engine friendliness</b>:\r\n		Re-write rules added so your Tiki objects can be accessed in a search-engine friendly way. \r\n		</p>\r\n	</div>\r\n\r\n</div> <!-- div pageleft -->\r\n\r\n<div id=\"pagebar\">\r\n\r\n	<h1>What is TikiWiki?</h1>\r\n	<p><i>\"A catch-all PHP application so you don\'t have to install many!\"</i></p>\r\n	<p>TikiWiki is an open source web application which provides a full Wiki environment, as well as Articles, Sections, User/Group Management (including optinal LDAP interaction), Polls and Quizzes, File and Image Galleries, Forums, Comments on many areas, Weblogs, and much more.</p>\r\n\r\n	<h1>Some numbers</h1>\r\n	<p>\r\n		<b>&raquo; </b>Tiki has 272202 lines of code.<br />\r\n		<b>&raquo; </b>The bug-rate is 0.0020 bugs per line.<br />\r\n		<b>&raquo; </b>Tiki has more than 375 different features.\r\n	</p>\r\n\r\n	<h1>Licensing</h1>\r\n	<p>Tiki is 100% free and open-source. Released under the LGPL license. Basically you can do whatever you want with Tiki</p>\r\n\r\n	<h1>Make a donation! (PayPal)</h1>\r\n	<div align=\"center\">\r\n		<input type=\"hidden\" name=\"cmd\" value=\"_xclick\">\r\n		<input type=\"hidden\" name=\"business\" value=\"tiki@marclaporte.com\">\r\n		<input type=\"hidden\" name=\"item_name\" value=\"Tiki CMS/Groupware donation\">\r\n		<input type=\"hidden\" name=\"no_shipping\" value=\"1\">\r\n		<input type=\"hidden\" name=\"cn\" value=\"Optional note\">\r\n		<input type=\"hidden\" name=\"currency_code\" value=\"USD\">\r\n		<input type=\"hidden\" name=\"tax\" value=\"0\">\r\n		<input type=\"image\" src=\"https://www.paypal.com/images/x-click-but21.gif\" border=\"0\" name=\"submit\" alt=\"Make payments with PayPal - it\'s fast, free and secure!\">\r\n		</form>\r\n	</div>\r\n\r\n	<h1>Style Swapper</h1>\r\n	<p>\r\n		<b>&raquo; </b><span style=\"cursor: help;\" onClick=\"chooseStyle(0);\" title=\"The Deault Mozilla Look\">Mozilla (Default)</span><br />\r\n		<b>&raquo; </b><span style=\"cursor: help;\" onClick=\"chooseStyle(1);\" title=\"A More Corporate Look\">WebXP</span><br />\r\n		<b>&raquo; </b><span style=\"cursor: help;\" onClick=\"chooseStyle(2);\" title=\"Relax into the Liquid\">Liquid</span>\r\n	</p>\r\n	\r\n	\r\n	\r\n	<h1>Associated With</h1>\r\n	<div align=\"center\">\r\n		<a href=\"http://www.opensource.org/docs/definition.php\" title=\"Certified with the OSI\"><img src=\"button_osi.png\" width=\"88\" height=\"31\" alt=\"\" border=\"0\" /></a>\r\n		<a href=\"http://www.php.net/\" title=\"Powered by PHP4\"><img src=\"button_php.png\" width=\"88\" height=\"31\" alt=\"\" border=\"0\" /></a>\r\n		<br />\r\n		<a href=\"http://smarty.php.net/\" title=\"Rendered by the SMARTY Template Engine\"><img src=\"button_smarty.gif\" width=\"88\" height=\"31\" alt=\"\" border=\"0\" /></a>\r\n		<a href=\"http://pear.php.net\" title=\"Powered by PEAR::db\"><img src=\"button_pear.png\" width=\"88\" height=\"31\" alt=\"\" border=\"0\" /></a>\r\n		<br />\r\n		<a href=\"http://www.w3.org/MarkUp/\" title=\"Rendered in XHTML1\"><img src=\"button_xhtml.png\" width=\"88\" height=\"31\" alt=\"\" border=\"0\" /></a>\r\n		<a href=\"http://www.w3.org/Style/CSS/\" title=\"Styled by CSS1/2\"><img src=\"button_css.png\" width=\"88\" height=\"31\" alt=\"\" border=\"0\" /></a>\r\n		<br />\r\n	</div>\r\n	\r\n	<form action=\"https://www.paypal.com/cgi-bin/webscr\" method=\"post\">\r\n\r\n	\r\n\r\n\r\n</div> <!-- div pageright -->\r\n\r\n<br clear=\"all\" />\r\n\r\n</body>\r\n</html>',1058245572);





CREATE TABLE tiki_links (
"fromPage" varchar(160) NOT NULL default '',
"toPage" varchar(160) NOT NULL default '',
PRIMARY KEY ("fromPage","toPage")
) ;






INSERT INTO tiki_links VALUES ('AWordWithCapitals','HomePage');
INSERT INTO tiki_links VALUES ('LisasPage','HaTha-pradIpikA');
INSERT INTO tiki_links VALUES ('LisasPage','Sanskrit-English');
INSERT INTO tiki_links VALUES ('LisasPage','SvAtmArAma');
INSERT INTO tiki_links VALUES ('NewPage','JobHunting');
INSERT INTO tiki_links VALUES ('NewPage','NextPage');
INSERT INTO tiki_links VALUES ('NextPage','JobHunting');
INSERT INTO tiki_links VALUES ('NoHTMLCodeIsNeeded','AWordWithCapitals');





CREATE TABLE tiki_live_support_events (
"eventId" integer NOT NULL default nextval('tiki_live_support_events_seq') unique not null,
"reqId" varchar(32) NOT NULL default '',
"type" varchar(40) NOT NULL default '',
"seqId" integer NOT NULL default '0',
"senderId" varchar(32) NOT NULL default '',
"data" text NOT NULL,
"timestamp" integer NOT NULL default '0',
PRIMARY KEY ("eventId")
) ;











CREATE TABLE tiki_live_support_message_comments (
"cId" integer NOT NULL default nextval('tiki_live_support_message_c_seq') unique not null,
"msgId" integer NOT NULL default '0',
"data" text NOT NULL,
"timestamp" integer NOT NULL default '0',
PRIMARY KEY ("cId")
) ;











CREATE TABLE tiki_live_support_messages (
"msgId" integer NOT NULL default nextval('tiki_live_support_messages_seq') unique not null,
"data" text NOT NULL,
"timestamp" integer NOT NULL default '0',
"user" varchar(200) NOT NULL default '',
"username" varchar(200) NOT NULL default '',
"priority" integer NOT NULL default '0',
"status" varchar(1) NOT NULL default '',
"assigned_to" varchar(200) NOT NULL default '',
"resolution" varchar(100) NOT NULL default '',
"title" varchar(200) NOT NULL default '',
"module" integer NOT NULL default '0',
"email" varchar(250) NOT NULL default '',
PRIMARY KEY ("msgId")
) ;











CREATE TABLE tiki_live_support_modules (
"modId" integer NOT NULL default nextval('tiki_live_support_modules_seq') unique not null,
"name" varchar(90) NOT NULL default '',
PRIMARY KEY ("modId")
) ;






INSERT INTO tiki_live_support_modules VALUES (1,'wiki');
INSERT INTO tiki_live_support_modules VALUES (2,'forums');
INSERT INTO tiki_live_support_modules VALUES (3,'image galleries');
INSERT INTO tiki_live_support_modules VALUES (4,'file galleries');
INSERT INTO tiki_live_support_modules VALUES (5,'directory');
INSERT INTO tiki_live_support_modules VALUES (6,'workflow');
INSERT INTO tiki_live_support_modules VALUES (7,'charts');





CREATE TABLE tiki_live_support_operators (
"user" varchar(200) NOT NULL default '',
"accepted_requests" integer NOT NULL default '0',
"status" varchar(20) NOT NULL default '',
"longest_chat" integer NOT NULL default '0',
"shortest_chat" integer NOT NULL default '0',
"average_chat" integer NOT NULL default '0',
"last_chat" integer NOT NULL default '0',
"time_online" integer NOT NULL default '0',
"votes" integer NOT NULL default '0',
"points" integer NOT NULL default '0',
"status_since" integer NOT NULL default '0',
PRIMARY KEY ("user")
) ;











CREATE TABLE tiki_live_support_requests (
"reqId" varchar(32) NOT NULL default '',
"user" varchar(200) NOT NULL default '',
"tiki_user" varchar(200) NOT NULL default '',
"email" varchar(200) NOT NULL default '',
"operator" varchar(200) NOT NULL default '',
"operator_id" varchar(32) NOT NULL default '',
"user_id" varchar(32) NOT NULL default '',
"reason" text NOT NULL,
"req_timestamp" integer NOT NULL default '0',
"timestamp" integer NOT NULL default '0',
"status" varchar(40) NOT NULL default '',
"resolution" varchar(40) NOT NULL default '',
"chat_started" integer NOT NULL default '0',
"chat_ended" integer NOT NULL default '0',
PRIMARY KEY ("reqId")
) ;











CREATE TABLE tiki_mail_events (
"event" varchar(200) NOT NULL default '',
"object" varchar(200) NOT NULL default '',
"email" varchar(200) NOT NULL default ''
) ;











CREATE TABLE tiki_mailin_accounts (
"accountId" integer NOT NULL default nextval('tiki_mailin_accounts_seq') unique not null,
"user" varchar(200) NOT NULL default '',
"account" varchar(50) NOT NULL default '',
"pop" varchar(255) NOT NULL default '',
"port" integer NOT NULL default '0',
"username" varchar(100) NOT NULL default '',
"pass" varchar(100) NOT NULL default '',
"active" varchar(1) NOT NULL default '',
"type" varchar(40) NOT NULL default '',
"smtp" varchar(255) NOT NULL default '',
"useAuth" varchar(1) NOT NULL default '',
"smtpPort" integer NOT NULL default '0',
PRIMARY KEY ("accountId")
) ;











CREATE TABLE tiki_menu_languages (
"menuId" integer NOT NULL default nextval('tiki_menu_languages_seq') unique not null,
"language" varchar(2) NOT NULL default '',
PRIMARY KEY ("language","menuId")
) ;











CREATE TABLE tiki_menu_options (
"optionId" integer NOT NULL default nextval('tiki_menu_options_seq') unique not null,
"menuId" integer NOT NULL default '0',
"type" varchar(1) NOT NULL default '',
"name" varchar(20) NOT NULL default '',
"url" varchar(255) NOT NULL default '',
"position" integer NOT NULL default '0',
PRIMARY KEY ("optionId")
) ;











CREATE TABLE tiki_menus (
"menuId" integer NOT NULL default nextval('tiki_menus_seq') unique not null,
"name" varchar(20) NOT NULL default '',
"description" text NOT NULL,
"type" varchar(1) NOT NULL default '',
PRIMARY KEY ("menuId")
) ;











CREATE TABLE tiki_minical_events (
"user" varchar(200) NOT NULL default '',
"eventId" integer NOT NULL default nextval('tiki_minical_events_seq') unique not null,
"title" varchar(250) NOT NULL default '',
"description" text NOT NULL,
"start" integer NOT NULL default '0',
"end" integer NOT NULL default '0',
"security" varchar(1) NOT NULL default '',
"duration" integer NOT NULL default '0',
"topicId" integer NOT NULL default '0',
"reminded" varchar(1) NOT NULL default '',
PRIMARY KEY ("eventId")
) ;











CREATE TABLE tiki_minical_topics (
"user" varchar(200) NOT NULL default '',
"topicId" integer NOT NULL default nextval('tiki_minical_topics_seq') unique not null,
"name" varchar(250) NOT NULL default '',
"filename" varchar(200) NOT NULL default '',
"filetype" varchar(200) NOT NULL default '',
"filesize" varchar(200) NOT NULL default '',
"data" text,
"path" varchar(250) NOT NULL default '',
"isIcon" varchar(1) NOT NULL default '',
PRIMARY KEY ("topicId")
) ;











CREATE TABLE tiki_modules (
"name" varchar(200) NOT NULL default '',
"position" varchar(1) NOT NULL default '',
"ord" integer NOT NULL default '0',
"type" varchar(1) NOT NULL default '',
"title" varchar(40) NOT NULL default '',
"cache_time" integer NOT NULL default '0',
"rows" integer NOT NULL default '0',
"groups" text NOT NULL,
"params" varchar(250) NOT NULL default '',
PRIMARY KEY ("name")
) ;






INSERT INTO tiki_modules VALUES ('login_box','r',1,'','',0,0,'','');
INSERT INTO tiki_modules VALUES ('application_menu','l',1,'','',0,10,'a:0:{}','');
INSERT INTO tiki_modules VALUES ('search_box','r',1,'','',0,10,'a:0:{}','');
INSERT INTO tiki_modules VALUES ('calendar','l',1,'','',0,10,'a:0:{}','');
INSERT INTO tiki_modules VALUES ('google','r',0,'','',0,10,'a:0:{}','');
INSERT INTO tiki_modules VALUES ('shoutbox','l',1,'','',0,10,'a:0:{}','');
INSERT INTO tiki_modules VALUES ('top_active_blogs','l',1,'','',0,10,'a:0:{}','');
INSERT INTO tiki_modules VALUES ('breadcrumb','r',1,'','',0,10,'a:0:{}','');
INSERT INTO tiki_modules VALUES ('logged_users','r',1,'','',0,10,'a:0:{}','');
INSERT INTO tiki_modules VALUES ('slashdot','r',1,'','',0,10,'a:0:{}','0');
INSERT INTO tiki_modules VALUES ('php.net','l',1,'','',60,10,'a:0:{}','0');
INSERT INTO tiki_modules VALUES ('debianplanet','r',2,'','',0,10,'a:0:{}','');





CREATE TABLE tiki_newsletter_subscriptions (
"nlId" integer NOT NULL default '0',
"email" varchar(255) NOT NULL default '',
"code" varchar(32) NOT NULL default '',
"valid" varchar(1) NOT NULL default '',
"subscribed" integer NOT NULL default '0',
PRIMARY KEY ("email","nlId")
) ;











CREATE TABLE tiki_newsletters (
"nlId" integer NOT NULL default nextval('tiki_newsletters_seq') unique not null,
"name" varchar(200) NOT NULL default '',
"description" text NOT NULL,
"created" integer NOT NULL default '0',
"lastSent" integer NOT NULL default '0',
"editions" integer NOT NULL default '0',
"users" integer NOT NULL default '0',
"allowAnySub" varchar(1) NOT NULL default '',
"frequency" integer NOT NULL default '0',
PRIMARY KEY ("nlId")
) ;






INSERT INTO tiki_newsletters VALUES (1,'test','test',1051799502,1051799502,0,0,'n',604800);





CREATE TABLE tiki_newsreader_marks (
"user" varchar(200) NOT NULL default '',
"serverId" integer NOT NULL default '0',
"groupName" varchar(255) NOT NULL default '',
"timestamp" integer NOT NULL default '0',
PRIMARY KEY ("groupName","user","serverId")
) ;











CREATE TABLE tiki_newsreader_servers (
"user" varchar(200) NOT NULL default '',
"serverId" integer NOT NULL default nextval('tiki_newsreader_servers_seq') unique not null,
"server" varchar(250) NOT NULL default '',
"port" integer NOT NULL default '0',
"username" varchar(200) NOT NULL default '',
"password" varchar(200) NOT NULL default '',
PRIMARY KEY ("serverId")
) ;











CREATE TABLE tiki_page_footnotes (
"user" varchar(200) NOT NULL default '',
"pageName" varchar(250) NOT NULL default '',
"data" text NOT NULL,
PRIMARY KEY ("pageName","user")
) ;











CREATE TABLE tiki_pages (
"pageName" varchar(160) NOT NULL default '',
"hits" integer NOT NULL default '0',
"data" text NOT NULL,
"lastModif" integer NOT NULL default '0',
"comment" varchar(200) NOT NULL default '',
"version" integer NOT NULL default '0',
"user" varchar(200) NOT NULL default '',
"ip" varchar(15) NOT NULL default '',
"flag" varchar(1) NOT NULL default '',
"points" integer NOT NULL default '0',
"votes" integer NOT NULL default '0',
"pageRank" decimal(5,3) NOT NULL default '0.000',
"description" varchar(200) NOT NULL default '',
"cache" text,
"cache_timestamp" integer NOT NULL default '0',
"creator" varchar(200) NOT NULL default '',
PRIMARY KEY ("pageName")




) ;






INSERT INTO tiki_pages VALUES ('HomePage',783,'[http://tikiwiki.sf.net/|tiki]',1045231844,'',31,'ross','192.168.1.2','',0,0,0.386,'',NULL,0,'ross');
INSERT INTO tiki_pages VALUES ('JobHunting',12,'[http://losangeles.craigslist.org/eng/] - while this site looks good, I\'ve submitted several resumes and have received 0 replies.  Probably just spammers harvesting email addresses....',1039947473,'',2,'ross','192.168.1.2','',0,0,0.395,'',NULL,0,'ross');
INSERT INTO tiki_pages VALUES ('NoHTMLCodeIsNeeded',2,'This is another page ((AWordWithCapitals))',1038794163,'',1,'ross','192.168.1.2','',0,0,0.150,'',NULL,0,'ross');
INSERT INTO tiki_pages VALUES ('AWordWithCapitals',1,'Another Page\r\n\r\n((HomePage))',1038794197,'',1,'ross','192.168.1.2','',0,0,0.278,'',NULL,0,'ross');
INSERT INTO tiki_pages VALUES ('LisasPage',1,'\r\nPer Lisa\'s request, here are the definitions from Monier-Williams\' Sanskrit-English Dictionary for ha, Tha, and haTha I found using the search engine:\r\n\r\n[http://www.uni-koeln.de/phil-fak/indologie/tamil/mwd_search.html]\r\n\r\n* ha\r\n \r\nMeaning  1 the thirty-third and last consonant of the Na1gari1 alphabet (in Pa1n2ini\'s system belonging to the guttural class , and usually pronounced like the English %{h} in %{hard} ; it is not an original letter , but is mostly derived from an older %{gh} , rarely from %{dh} or %{bh}).\r\n\r\nMeaning  2 (only L.) m. a form of S3iva or Bhairava (cf. %{nakulI7za}) ; water ; a cipher (i.e. the arithmetical figure which symbolizes o) ; meditation , auspiciousness ; sky , heaven , paradise ; blood ; dying ; fear ; knowledge ; the moon ; Vishn2u ; war , battle ; horripilation ; a horse ; pride ; a physician ; cause , motive ; = %{pApa-haraNa} ; = %{sakopa-vAraNa} ; = %{zuSka} ; (also %{A} f.) laughter ; (%{A}) f. coition ; a lute (%{am}) n. the Supreme Spirit ; pleasure , delight ; a weapon ; the sparkling of a gem ; calling , calling to the sound of a lute ; (ind.) = %{aham} (?) , IndSt. ; mfn. mad , drunk. \r\n \r\nMeaning  3 ind. (prob. orig. identical with 2. %{gha} , and used as a particle for emphasizing a preceding word , esp. if it begins a sentence closely connected with another ; very frequent in the Bra1hman2as and Su1tras , and often translatable by) indeed , assuredly , verily , of course , then &c. (often with other particles e.g. with %{tv@eva} , %{u} , %{sma} , %{vai} &c. ; %{na@ha} , `\" not indeed \"\' ; also with interrogatives and relatives e.g. %{yad@dha} , `\" when indeed \"\' ; %{kad@dha} , `\" what then? \"\' sometimes with impf. or pf. [cf. Pa1n2. 3-2 , 116] ; in later language very commonly used as a mere expletive , esp. at the end of a verse) RV. &c. &c. \r\n\r\nMeaning  4 mf(%{A})n. (fr. %{han}) killing , destroying , removing (only ifc. ; see %{arAti-} , %{vRtra-} , %{zatruha} &c.) \r\n\r\nMeaning  5 mf(%{A})n. (fr. 3. %{hA}) abandoning , deserting , avoiding (ifc. ; see %{an-oka-} and %{vApI-ha}) ; (%{A}) f. abandonment , desertion L. \r\n\r\n* Tha\r\n \r\nMeaning  1 the aspirate of the preceding consonant.\r\n\r\nMeaning  2 m. a loud noise (%{ThaThaM@ThaThaM@ThaM@ThaThaThaM@ThaThaM@ThaH} , an imitative sound as of a golden pitcher rolling down steps Maha1n.2 iii , 5) L. ; the moon\'s disk L. ; a disk L. ; a cypher L. ; a place frequented by all L. ; S3iva L. \r\n\r\n* haTha\r\n \r\nMeaning  m. violence , force (ibc. , %{ena} , and %{At} , `\" by force , forcibly \"\') R. Ra1jat. Katha1s. &c. ; obstinacy , pertinacity (ibc. and %{At} , `\" obstinately , persistently \"\') Pan5cat. Katha1s. ; absolute or inevitable necessity (as the cause of all existence and activity ; ibc. , %{At} , and %{ena} , `\" necessarily , inevitably , by all means \"\') MBh. Ka1v. &c. ; = %{haTha-yoga} Cat. ; oppression W. ; rapine ib. ; going in the rear of an enemy L. ; Pistia Stratiotes L. \r\n\r\n* haTha-yoga\r\n\r\nMeaning m. a kind of forced Yoga or abstract meditation (forcing the mind to withdraw from external objects; treated of in the HaTha-pradIpikA by SvAtmArAma and performed with much self-torture, such as standing on one leg, holding up the arms, inhaling smoke with the head inverted &c.)\r\n\r\n',1038940749,'',1,'ross','192.168.1.2','',0,0,0.150,'',NULL,0,'ross');
INSERT INTO tiki_pages VALUES ('NewPage',1,'Adding an entry to test the full text search function.\r\n\r\nHere are some search terms:\r\n\r\nJobHunting\r\ntest\r\ntiki\r\n\r\nNextPage',1040866218,'',1,'ross','192.168.1.2','',0,0,0.150,'',NULL,0,'ross');
INSERT INTO tiki_pages VALUES ('NextPage',1,'Adding an entry to test the full text search function.\r\n\r\nHere are some search terms:\r\n\r\nJobHunting\r\ntest\r\ntiki\r\n',1040866230,'',1,'ross','192.168.1.2','',0,0,0.214,'',NULL,0,'ross');





CREATE TABLE tiki_pageviews (
"day" integer NOT NULL default '0',
"pageviews" integer NOT NULL default '0',
PRIMARY KEY ("day")
) ;






INSERT INTO tiki_pageviews VALUES (1038643200,92);
INSERT INTO tiki_pageviews VALUES (1038729600,43);
INSERT INTO tiki_pageviews VALUES (1038902400,44);
INSERT INTO tiki_pageviews VALUES (1039075200,2);
INSERT INTO tiki_pageviews VALUES (1039161600,31);
INSERT INTO tiki_pageviews VALUES (1039507200,29);
INSERT INTO tiki_pageviews VALUES (1039593600,60);
INSERT INTO tiki_pageviews VALUES (1039680000,92);
INSERT INTO tiki_pageviews VALUES (1039766400,117);
INSERT INTO tiki_pageviews VALUES (1039939200,48);
INSERT INTO tiki_pageviews VALUES (1040025600,2);
INSERT INTO tiki_pageviews VALUES (1040112000,4);
INSERT INTO tiki_pageviews VALUES (1040284800,4);
INSERT INTO tiki_pageviews VALUES (1040371200,12);
INSERT INTO tiki_pageviews VALUES (1040544000,99);
INSERT INTO tiki_pageviews VALUES (1040630400,14);
INSERT INTO tiki_pageviews VALUES (1040803200,165);
INSERT INTO tiki_pageviews VALUES (1040889600,27);
INSERT INTO tiki_pageviews VALUES (1040976000,31);
INSERT INTO tiki_pageviews VALUES (1041148800,17);
INSERT INTO tiki_pageviews VALUES (1041235200,20);
INSERT INTO tiki_pageviews VALUES (1041408000,91);
INSERT INTO tiki_pageviews VALUES (1041494400,130);
INSERT INTO tiki_pageviews VALUES (1041552000,35);
INSERT INTO tiki_pageviews VALUES (1041667200,62);
INSERT INTO tiki_pageviews VALUES (1041638400,8);
INSERT INTO tiki_pageviews VALUES (1041724800,96);
INSERT INTO tiki_pageviews VALUES (1041753600,179);
INSERT INTO tiki_pageviews VALUES (1041811200,122);
INSERT INTO tiki_pageviews VALUES (1041840000,1);
INSERT INTO tiki_pageviews VALUES (1041897600,1);
INSERT INTO tiki_pageviews VALUES (1042012800,2);
INSERT INTO tiki_pageviews VALUES (1041984000,1);
INSERT INTO tiki_pageviews VALUES (1042099200,1);
INSERT INTO tiki_pageviews VALUES (1042070400,21);
INSERT INTO tiki_pageviews VALUES (1044172800,14);
INSERT INTO tiki_pageviews VALUES (1044432000,2);
INSERT INTO tiki_pageviews VALUES (1044864000,35);
INSERT INTO tiki_pageviews VALUES (1044950400,112);
INSERT INTO tiki_pageviews VALUES (1045036800,115);
INSERT INTO tiki_pageviews VALUES (1045123200,13);
INSERT INTO tiki_pageviews VALUES (1045209600,82);
INSERT INTO tiki_pageviews VALUES (1045296000,13);
INSERT INTO tiki_pageviews VALUES (1045382400,31);
INSERT INTO tiki_pageviews VALUES (1045468800,6);
INSERT INTO tiki_pageviews VALUES (1045728000,2);
INSERT INTO tiki_pageviews VALUES (1045900800,45);
INSERT INTO tiki_pageviews VALUES (1046073600,11);
INSERT INTO tiki_pageviews VALUES (1046160000,46);
INSERT INTO tiki_pageviews VALUES (1046131200,2);
INSERT INTO tiki_pageviews VALUES (1046764800,1);
INSERT INTO tiki_pageviews VALUES (1046822400,3);
INSERT INTO tiki_pageviews VALUES (1046851200,1);
INSERT INTO tiki_pageviews VALUES (1047283200,1);
INSERT INTO tiki_pageviews VALUES (1047340800,29);
INSERT INTO tiki_pageviews VALUES (1047369600,2);
INSERT INTO tiki_pageviews VALUES (1048060800,1);
INSERT INTO tiki_pageviews VALUES (1048118400,1);
INSERT INTO tiki_pageviews VALUES (1048147200,1);
INSERT INTO tiki_pageviews VALUES (1048204800,1);
INSERT INTO tiki_pageviews VALUES (1049698800,1);
INSERT INTO tiki_pageviews VALUES (1049760000,3);
INSERT INTO tiki_pageviews VALUES (1050044400,1);
INSERT INTO tiki_pageviews VALUES (1050019200,1);
INSERT INTO tiki_pageviews VALUES (1051254000,1);
INSERT INTO tiki_pageviews VALUES (1051315200,7);
INSERT INTO tiki_pageviews VALUES (1051340400,1);
INSERT INTO tiki_pageviews VALUES (1051513200,3);
INSERT INTO tiki_pageviews VALUES (1051488000,1);
INSERT INTO tiki_pageviews VALUES (1051772400,47);
INSERT INTO tiki_pageviews VALUES (1051747200,107);
INSERT INTO tiki_pageviews VALUES (1051858800,1);
INSERT INTO tiki_pageviews VALUES (1051833600,48);
INSERT INTO tiki_pageviews VALUES (1052204400,1);
INSERT INTO tiki_pageviews VALUES (1052265600,4);
INSERT INTO tiki_pageviews VALUES (1052290800,2);
INSERT INTO tiki_pageviews VALUES (1052377200,1);
INSERT INTO tiki_pageviews VALUES (1052438400,2);
INSERT INTO tiki_pageviews VALUES (1052463600,1);
INSERT INTO tiki_pageviews VALUES (1052982000,1);
INSERT INTO tiki_pageviews VALUES (1052956800,1);
INSERT INTO tiki_pageviews VALUES (1053154800,18);
INSERT INTO tiki_pageviews VALUES (1053846000,1);
INSERT INTO tiki_pageviews VALUES (1053907200,3);
INSERT INTO tiki_pageviews VALUES (1054105200,1);
INSERT INTO tiki_pageviews VALUES (1054166400,5);
INSERT INTO tiki_pageviews VALUES (1054796400,1);
INSERT INTO tiki_pageviews VALUES (1054771200,6);
INSERT INTO tiki_pageviews VALUES (1055401200,3);
INSERT INTO tiki_pageviews VALUES (1055376000,3);
INSERT INTO tiki_pageviews VALUES (1055660400,1);
INSERT INTO tiki_pageviews VALUES (1055721600,1);
INSERT INTO tiki_pageviews VALUES (1056438000,6);
INSERT INTO tiki_pageviews VALUES (1057708800,40);
INSERT INTO tiki_pageviews VALUES (1057734000,1);
INSERT INTO tiki_pageviews VALUES (1057906800,4);
INSERT INTO tiki_pageviews VALUES (1057968000,149);
INSERT INTO tiki_pageviews VALUES (1057993200,39);
INSERT INTO tiki_pageviews VALUES (1058054400,176);
INSERT INTO tiki_pageviews VALUES (1058079600,19);
INSERT INTO tiki_pageviews VALUES (1058140800,47);
INSERT INTO tiki_pageviews VALUES (1058166000,16);
INSERT INTO tiki_pageviews VALUES (1058227200,39);
INSERT INTO tiki_pageviews VALUES (1058252400,4);





CREATE TABLE tiki_poll_options (
"pollId" integer NOT NULL default '0',
"optionId" integer NOT NULL default nextval('tiki_poll_options_seq') unique not null,
"title" varchar(200) NOT NULL default '',
"votes" integer NOT NULL default '0',
PRIMARY KEY ("optionId")
) ;











CREATE TABLE tiki_polls (
"pollId" integer NOT NULL default nextval('tiki_polls_seq') unique not null,
"title" varchar(200) NOT NULL default '',
"votes" integer NOT NULL default '0',
"active" varchar(1) NOT NULL default '',
"publishDate" integer NOT NULL default '0',
PRIMARY KEY ("pollId")
) ;











CREATE TABLE tiki_preferences (
"name" varchar(40) NOT NULL default '',
"value" varchar(250) NOT NULL default '',
PRIMARY KEY ("name")
) ;






INSERT INTO tiki_preferences VALUES ('feature_wiki','y');
INSERT INTO tiki_preferences VALUES ('feature_chat','y');
INSERT INTO tiki_preferences VALUES ('feature_polls','y');
INSERT INTO tiki_preferences VALUES ('feature_custom_home','y');
INSERT INTO tiki_preferences VALUES ('feature_forums','y');
INSERT INTO tiki_preferences VALUES ('feature_file_galleries','y');
INSERT INTO tiki_preferences VALUES ('feature_banners','y');
INSERT INTO tiki_preferences VALUES ('feature_xmlrpc','y');
INSERT INTO tiki_preferences VALUES ('feature_categories','y');
INSERT INTO tiki_preferences VALUES ('feature_comm','y');
INSERT INTO tiki_preferences VALUES ('feature_search','y');
INSERT INTO tiki_preferences VALUES ('feature_edit_templates','y');
INSERT INTO tiki_preferences VALUES ('feature_dynamic_content','y');
INSERT INTO tiki_preferences VALUES ('feature_articles','y');
INSERT INTO tiki_preferences VALUES ('feature_submissions','y');
INSERT INTO tiki_preferences VALUES ('feature_blogs','y');
INSERT INTO tiki_preferences VALUES ('feature_hotwords','y');
INSERT INTO tiki_preferences VALUES ('feature_userPreferences','y');
INSERT INTO tiki_preferences VALUES ('feature_featuredLinks','y');
INSERT INTO tiki_preferences VALUES ('feature_galleries','y');
INSERT INTO tiki_preferences VALUES ('tikiIndex','tiki-index.php');
INSERT INTO tiki_preferences VALUES ('style','neat.css');
INSERT INTO tiki_preferences VALUES ('language','en');
INSERT INTO tiki_preferences VALUES ('anonCanEdit','n');
INSERT INTO tiki_preferences VALUES ('modallgroups','y');
INSERT INTO tiki_preferences VALUES ('cachepages','n');
INSERT INTO tiki_preferences VALUES ('cacheimages','n');
INSERT INTO tiki_preferences VALUES ('popupLinks','y');
INSERT INTO tiki_preferences VALUES ('allowRegister','y');
INSERT INTO tiki_preferences VALUES ('maxRecords','10');
INSERT INTO tiki_preferences VALUES ('feature_blog_rankings','y');
INSERT INTO tiki_preferences VALUES ('feature_blog_comments','y');
INSERT INTO tiki_preferences VALUES ('blog_comments_per_page','25');
INSERT INTO tiki_preferences VALUES ('blog_comments_default_ordering','points_desc');
INSERT INTO tiki_preferences VALUES ('home_blog','1');
INSERT INTO tiki_preferences VALUES ('feature_faqs','y');
INSERT INTO tiki_preferences VALUES ('feature_stats','y');
INSERT INTO tiki_preferences VALUES ('feature_games','y');
INSERT INTO tiki_preferences VALUES ('user_assigned_modules','y');
INSERT INTO tiki_preferences VALUES ('feature_user_bookmarks','y');
INSERT INTO tiki_preferences VALUES ('home_forum','');
INSERT INTO tiki_preferences VALUES ('feature_lastChanges','y');
INSERT INTO tiki_preferences VALUES ('feature_wiki_comments','y');
INSERT INTO tiki_preferences VALUES ('wiki_spellcheck','y');
INSERT INTO tiki_preferences VALUES ('feature_warn_on_edit','n');
INSERT INTO tiki_preferences VALUES ('feature_dump','y');
INSERT INTO tiki_preferences VALUES ('feature_wiki_rankings','y');
INSERT INTO tiki_preferences VALUES ('feature_wiki_undo','y');
INSERT INTO tiki_preferences VALUES ('feature_wiki_multiprint','y');
INSERT INTO tiki_preferences VALUES ('feature_ranking','n');
INSERT INTO tiki_preferences VALUES ('feature_listPages','y');
INSERT INTO tiki_preferences VALUES ('feature_history','y');
INSERT INTO tiki_preferences VALUES ('feature_sandbox','y');
INSERT INTO tiki_preferences VALUES ('feature_backlinks','y');
INSERT INTO tiki_preferences VALUES ('feature_likePages','y');
INSERT INTO tiki_preferences VALUES ('feature_userVersions','n');
INSERT INTO tiki_preferences VALUES ('siteTitle','Stemmin\' the Tide!');
INSERT INTO tiki_preferences VALUES ('useRegisterPasscode','n');
INSERT INTO tiki_preferences VALUES ('registerPasscode','');
INSERT INTO tiki_preferences VALUES ('validateUsers','n');
INSERT INTO tiki_preferences VALUES ('eponymousGroups','n');
INSERT INTO tiki_preferences VALUES ('forgotPass','n');
INSERT INTO tiki_preferences VALUES ('feature_shoutbox','y');
INSERT INTO tiki_preferences VALUES ('feature_quizzes','y');
INSERT INTO tiki_preferences VALUES ('feature_smileys','n');
INSERT INTO tiki_preferences VALUES ('feature_left_column','y');
INSERT INTO tiki_preferences VALUES ('feature_right_column','y');
INSERT INTO tiki_preferences VALUES ('feature_top_bar','y');
INSERT INTO tiki_preferences VALUES ('feature_bot_bar','y');
INSERT INTO tiki_preferences VALUES ('feature_drawings','y');
INSERT INTO tiki_preferences VALUES ('feature_html_pages','y');
INSERT INTO tiki_preferences VALUES ('feature_search_stats','y');
INSERT INTO tiki_preferences VALUES ('feature_referer_stats','y');
INSERT INTO tiki_preferences VALUES ('feature_hotwords_nw','y');
INSERT INTO tiki_preferences VALUES ('layout_section','y');
INSERT INTO tiki_preferences VALUES ('max_rss_articles','10');
INSERT INTO tiki_preferences VALUES ('max_rss_image_galleries','10');
INSERT INTO tiki_preferences VALUES ('max_rss_file_galleries','10');
INSERT INTO tiki_preferences VALUES ('max_rss_image_gallery','10');
INSERT INTO tiki_preferences VALUES ('max_rss_file_gallery','10');
INSERT INTO tiki_preferences VALUES ('max_rss_wiki','10');
INSERT INTO tiki_preferences VALUES ('max_rss_blogs','10');
INSERT INTO tiki_preferences VALUES ('max_rss_blog','10');
INSERT INTO tiki_preferences VALUES ('max_rss_forum','10');
INSERT INTO tiki_preferences VALUES ('max_rss_forums','10');
INSERT INTO tiki_preferences VALUES ('max_rss_mapfiles','10');
INSERT INTO tiki_preferences VALUES ('rss_articles','y');
INSERT INTO tiki_preferences VALUES ('rss_blogs','y');
INSERT INTO tiki_preferences VALUES ('rss_image_galleries','y');
INSERT INTO tiki_preferences VALUES ('rss_file_galleries','y');
INSERT INTO tiki_preferences VALUES ('rss_wiki','y');
INSERT INTO tiki_preferences VALUES ('rss_forum','y');
INSERT INTO tiki_preferences VALUES ('rss_forums','y');
INSERT INTO tiki_preferences VALUES ('rss_mapfiles','y');
INSERT INTO tiki_preferences VALUES ('rss_blog','y');
INSERT INTO tiki_preferences VALUES ('rss_image_gallery','y');
INSERT INTO tiki_preferences VALUES ('rss_file_gallery','y');
INSERT INTO tiki_preferences VALUES ('change_theme','y');
INSERT INTO tiki_preferences VALUES ('change_language','y');
INSERT INTO tiki_preferences VALUES ('count_admin_pvs','y');
INSERT INTO tiki_preferences VALUES ('useUrlIndex','n');
INSERT INTO tiki_preferences VALUES ('urlIndex','');
INSERT INTO tiki_preferences VALUES ('wiki_left_column','y');
INSERT INTO tiki_preferences VALUES ('wiki_right_column','y');
INSERT INTO tiki_preferences VALUES ('wiki_top_bar','y');
INSERT INTO tiki_preferences VALUES ('wiki_bot_bar','y');
INSERT INTO tiki_preferences VALUES ('feature_trackers','y');
INSERT INTO tiki_preferences VALUES ('feature_search_fulltext','y');
INSERT INTO tiki_preferences VALUES ('feature_webmail','y');
INSERT INTO tiki_preferences VALUES ('feature_surveys','y');
INSERT INTO tiki_preferences VALUES ('slide_style','slidestyle.css');
INSERT INTO tiki_preferences VALUES ('feature_server_name','wiki.netebb.com');
INSERT INTO tiki_preferences VALUES ('long_date_format','%A %B %d, %Y');
INSERT INTO tiki_preferences VALUES ('short_date_format','%a. %b. %d, %Y');
INSERT INTO tiki_preferences VALUES ('feature_obzip','n');
INSERT INTO tiki_preferences VALUES ('direct_pagination','n');
INSERT INTO tiki_preferences VALUES ('feature_bidi','n');
INSERT INTO tiki_preferences VALUES ('display_timezone','PST8PDT');
INSERT INTO tiki_preferences VALUES ('long_time_format','%H:%M:%S %Z');
INSERT INTO tiki_preferences VALUES ('short_time_format','%H:%M %Z');
INSERT INTO tiki_preferences VALUES ('feature_newsletters','y');
INSERT INTO tiki_preferences VALUES ('min_pass_length','1');
INSERT INTO tiki_preferences VALUES ('pass_due','999');
INSERT INTO tiki_preferences VALUES ('pass_chr_num','n');
INSERT INTO tiki_preferences VALUES ('feature_challenge','n');
INSERT INTO tiki_preferences VALUES ('feature_clear_passwords','n');
INSERT INTO tiki_preferences VALUES ('https_login','y');
INSERT INTO tiki_preferences VALUES ('https_login_required','n');
INSERT INTO tiki_preferences VALUES ('http_domain','wiki2.netebb.com');
INSERT INTO tiki_preferences VALUES ('http_port','18080');
INSERT INTO tiki_preferences VALUES ('https_domain','wiki2.netebb.com');
INSERT INTO tiki_preferences VALUES ('https_port','18443');
INSERT INTO tiki_preferences VALUES ('http_prefix','/');
INSERT INTO tiki_preferences VALUES ('https_prefix','/');
INSERT INTO tiki_preferences VALUES ('feature_directory','y');
INSERT INTO tiki_preferences VALUES ('feature_newsreader','y');
INSERT INTO tiki_preferences VALUES ('feature_notepad','y');
INSERT INTO tiki_preferences VALUES ('feature_userfiles','y');
INSERT INTO tiki_preferences VALUES ('feature_usermenu','y');
INSERT INTO tiki_preferences VALUES ('feature_minical','y');
INSERT INTO tiki_preferences VALUES ('feature_theme_control','y');
INSERT INTO tiki_preferences VALUES ('feature_workflow','y');
INSERT INTO tiki_preferences VALUES ('feature_user_watches','y');
INSERT INTO tiki_preferences VALUES ('feature_charts','y');
INSERT INTO tiki_preferences VALUES ('feature_phpopentracker','n');
INSERT INTO tiki_preferences VALUES ('feature_eph','y');
INSERT INTO tiki_preferences VALUES ('feature_contact','y');
INSERT INTO tiki_preferences VALUES ('feature_messages','y');
INSERT INTO tiki_preferences VALUES ('feature_tasks','y');
INSERT INTO tiki_preferences VALUES ('contact_user','admin');
INSERT INTO tiki_preferences VALUES ('system_os','unix');
INSERT INTO tiki_preferences VALUES ('tmpDir','/tmp');
INSERT INTO tiki_preferences VALUES ('lang_use_db','n');
INSERT INTO tiki_preferences VALUES ('record_untranslated','n');
INSERT INTO tiki_preferences VALUES ('feature_menusfolderstyle','n');
INSERT INTO tiki_preferences VALUES ('webserverauth','n');
INSERT INTO tiki_preferences VALUES ('rnd_num_reg','n');
INSERT INTO tiki_preferences VALUES ('rememberme','all');
INSERT INTO tiki_preferences VALUES ('remembertime','720000');
INSERT INTO tiki_preferences VALUES ('auth_method','tiki');
INSERT INTO tiki_preferences VALUES ('feature_calendar','n');
INSERT INTO tiki_preferences VALUES ('feature_editcss','n');
INSERT INTO tiki_preferences VALUES ('wiki_feature_copyrights','n');
INSERT INTO tiki_preferences VALUES ('feature_wiki_monosp','y');





CREATE TABLE tiki_private_messages (
"messageId" integer NOT NULL default nextval('tiki_private_messages_seq') unique not null,
"toNickname" varchar(200) NOT NULL default '',
"data" varchar(255) NOT NULL default '',
"poster" varchar(200) NOT NULL default 'anonymous',
"timestamp" integer NOT NULL default '0',
PRIMARY KEY ("messageId")
) ;











CREATE TABLE tiki_programmed_content (
"pId" integer NOT NULL default nextval('tiki_programmed_content_seq') unique not null,
"contentId" integer NOT NULL default '0',
"publishDate" integer NOT NULL default '0',
"data" text NOT NULL,
PRIMARY KEY ("pId")
) ;











CREATE TABLE tiki_quiz_question_options (
"optionId" integer NOT NULL default nextval('tiki_quiz_question_options_seq') unique not null,
"questionId" integer NOT NULL default '0',
"optionText" text NOT NULL,
"points" integer NOT NULL default '0',
PRIMARY KEY ("optionId")
) ;











CREATE TABLE tiki_quiz_questions (
"questionId" integer NOT NULL default nextval('tiki_quiz_questions_seq') unique not null,
"quizId" integer NOT NULL default '0',
"question" text NOT NULL,
"position" integer NOT NULL default '0',
"type" varchar(1) NOT NULL default '',
"maxPoints" integer NOT NULL default '0',
PRIMARY KEY ("questionId")
) ;






INSERT INTO tiki_quiz_questions VALUES (1,1,'Question 1',1,'o',0);
INSERT INTO tiki_quiz_questions VALUES (2,1,'Question 2',1,'o',0);





CREATE TABLE tiki_quiz_results (
"resultId" integer NOT NULL default nextval('tiki_quiz_results_seq') unique not null,
"quizId" integer NOT NULL default '0',
"fromPoints" integer NOT NULL default '0',
"toPoints" integer NOT NULL default '0',
"answer" text NOT NULL,
PRIMARY KEY ("resultId")
) ;











CREATE TABLE tiki_quiz_stats (
"quizId" integer NOT NULL default '0',
"questionId" integer NOT NULL default '0',
"optionId" integer NOT NULL default '0',
"votes" integer NOT NULL default '0',
PRIMARY KEY ("quizId","optionId","questionId")
) ;











CREATE TABLE tiki_quiz_stats_sum (
"quizId" integer NOT NULL default '0',
"quizName" varchar(255) NOT NULL default '',
"timesTaken" integer NOT NULL default '0',
"avgpoints" decimal(5,2) NOT NULL default '0.00',
"avgavg" decimal(5,2) NOT NULL default '0.00',
"avgtime" decimal(5,2) NOT NULL default '0.00',
PRIMARY KEY ("quizId")
) ;











CREATE TABLE tiki_quizzes (
"quizId" integer NOT NULL default nextval('tiki_quizzes_seq') unique not null,
"name" varchar(255) NOT NULL default '',
"description" text NOT NULL,
"canRepeat" varchar(1) NOT NULL default '',
"storeResults" varchar(1) NOT NULL default '',
"questionsPerPage" integer NOT NULL default '0',
"timeLimited" varchar(1) NOT NULL default '',
"timeLimit" integer NOT NULL default '0',
"created" integer NOT NULL default '0',
"taken" integer NOT NULL default '0',
PRIMARY KEY ("quizId")
) ;






INSERT INTO tiki_quizzes VALUES (1,'A Test Quiz','A Test Quiz','n','n',999,'n',1,1040550135,0);





CREATE TABLE tiki_received_articles (
"receivedArticleId" integer NOT NULL default nextval('tiki_received_articles_seq') unique not null,
"receivedFromSite" varchar(200) NOT NULL default '',
"receivedFromUser" varchar(200) NOT NULL default '',
"receivedDate" integer NOT NULL default '0',
"title" varchar(80) NOT NULL default '',
"authorName" varchar(60) NOT NULL default '',
"size" integer NOT NULL default '0',
"useImage" varchar(1) NOT NULL default '',
"image_name" varchar(80) NOT NULL default '',
"image_type" varchar(80) NOT NULL default '',
"image_size" integer NOT NULL default '0',
"image_x" integer NOT NULL default '0',
"image_y" integer NOT NULL default '0',
"image_data" text,
"publishDate" integer NOT NULL default '0',
"created" integer NOT NULL default '0',
"heading" text NOT NULL,
"body" text,
"hash" varchar(32) NOT NULL default '',
"author" varchar(200) NOT NULL default '',
"type" varchar(50) NOT NULL default '',
"rating" decimal(4,2) NOT NULL default '0.00',
PRIMARY KEY ("receivedArticleId")
) ;











CREATE TABLE tiki_received_pages (
"receivedPageId" integer NOT NULL default nextval('tiki_received_pages_seq') unique not null,
"pageName" varchar(160) NOT NULL default '',
"data" text,
"comment" varchar(200) NOT NULL default '',
"receivedFromSite" varchar(200) NOT NULL default '',
"receivedFromUser" varchar(200) NOT NULL default '',
"receivedDate" integer NOT NULL default '0',
"description" varchar(200) NOT NULL default '',
PRIMARY KEY ("receivedPageId")
) ;











CREATE TABLE tiki_referer_stats (
"referer" varchar(50) NOT NULL default '',
"hits" integer NOT NULL default '0',
"last" integer NOT NULL default '0',
PRIMARY KEY ("referer")
) ;






INSERT INTO tiki_referer_stats VALUES ('skanda',2,1039714895);
INSERT INTO tiki_referer_stats VALUES ('wiki2.netebb.com',10,1058198715);
INSERT INTO tiki_referer_stats VALUES ('tikiwiki.org',1,1058072094);
INSERT INTO tiki_referer_stats VALUES ('10.0.0.2',13,1058139833);





CREATE TABLE tiki_related_categories (
"categId" integer NOT NULL default '0',
"relatedTo" integer NOT NULL default '0',
PRIMARY KEY ("categId","relatedTo")
) ;











CREATE TABLE tiki_rss_modules (
"rssId" integer NOT NULL default nextval('tiki_rss_modules_seq') unique not null,
"name" varchar(30) NOT NULL default '',
"description" text NOT NULL,
"url" varchar(255) NOT NULL default '',
"refresh" integer NOT NULL default '0',
"lastUpdated" integer NOT NULL default '0',
"content" text,
PRIMARY KEY ("rssId")
) ;






INSERT INTO tiki_rss_modules VALUES (1,'php.net','','http://www.php.net/news.rss',900,1058294881,'<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<rdf:RDF\n	xmlns:rdf=\"http://www.w3.org/1999/02/22-rdf-syntax-ns#\"\n	xmlns=\"http://purl.org/rss/1.0/\"\n	xmlns:dc=\"http://purl.org/dc/elements/1.1/\"\n>\n<channel rdf:about=\"http://www.php.net/\">\n	<title>PHP: Hypertext Preprocessor</title>\n	<link>http://www.php.net/</link>\n	<description>The PHP scripting language web site</description>\n	<items>\n		<rdf:Seq>\n			<rdf:li rdf:resource=\"http://www.linuxtag.org/2003/en/index.html\" />\n			<rdf:li rdf:resource=\"http://www.php.net/downloads.php\" />\n			<rdf:li rdf:resource=\"http://www.zend.com/survey/php_net.php\" />\n			<rdf:li rdf:resource=\"http://www.phpconference.de/2003/index_en.php\" />\n			<rdf:li rdf:resource=\"http://qa.php.net/\" />\n			<rdf:li rdf:resource=\"http://www.ActiveState.com/Corporate/ActiveAwards/\" />\n			<rdf:li rdf:resource=\"http://www.php.net/release_4_3_2.php\" />\n			<rdf:li rdf:resource=\"http://www.directi.com/?site=ip-to-country\" />\n			<rdf:li rdf:resource=\"http://www.php.net/my.php\" />\n			<rdf:li rdf:resource=\"http://www.php.net/my.php\" />\n			<rdf:li rdf:resource=\"http://www.phparch.com\" />\n			<rdf:li rdf:resource=\"http://www.php.net/echo\" />\n			<rdf:li rdf:resource=\"http://phpconf.hu/\" />\n			<rdf:li rdf:resource=\"http://www.php.net/release_4_3_1.php\" />\n		</rdf:Seq>\n	</items>\n</channel>\n<!-- RSS-Items -->\n\n<item rdf:about=\"http://www.linuxtag.org/2003/en/index.html\">\n	<title>PHP @ LinuxTag</title>\n	<link>http://www.linuxtag.org/2003/en/index.html</link>\n	<description>      From July, 10th to 13th 2003  LinuxTag, Europe\'s  largest Fair and Congress dedicated to Open Source in general and Linux in  particular, takes place in Karlsruhe, Germany.    Under the motto &quot;PHP and Friends&quot; the PHP Project showcases PHP and related  projects, like MySQL, SQLite, Midgard, osCommerce or DB Designer, at booth  F21.  In addition to the booth &quot;PHP and Friends&quot; offer an interesting  programme of  technical talks and workshops  which are held by 24 speakers from 11 nations and cover the latest trends and  technologies related to PHP, Apache and MySQL.  Among the speakers are core  developers of the discussed Open Source projects, for instance Ken Coar  (Apache), Rasmus Lerdorf (PHP) or Kaj Arn&amp;ouml; (MySQL).  </description>\n	<dc:date>2003-07-06</dc:date>\n</item>\n\n<item rdf:about=\"http://www.php.net/downloads.php\">\n	<title>PHP 5.0.0 Beta 1</title>\n	<link>http://www.php.net/downloads.php</link>\n	<description>    The PHP development community is proud to announce the release of PHP 5 Beta 1.  Both source packages,  and a Windows build are available in the Downloads Section.  A list of changes   can be found in the ChangeLog file.   Some of the more major changes include:     PHP 5 features the Zend Engine 2.  For a list of Zend Engine 2 changes, please visit   this webpage.  XML support has been completely redone in PHP 5, all extensions are now focused around the   excellent libxml2 library (http://www.xmlsoft.org/).  SQLite has been bundled with PHP.  For more information on SQLite, please visit their   website.  Streams have been greatly improved, including the ability to access lowlevel socket   operations on streams.    Note: This is a beta version. It should not be used in production or even semiproduction web sites. There are known bugs in it, and in addition, some of the features may change (based on feedback). We encourage you to download and play with it (and report bugs if you find any!), but please do not replace your production installations of PHP 4 at this time.  </description>\n	<dc:date>2003-06-29</dc:date>\n</item>\n\n<item rdf:about=\"http://www.zend.com/survey/php_net.php\">\n	<title>PHP Usage Survey</title>\n	<link>http://www.zend.com/survey/php_net.php</link>\n	<description>     Zend Technologies is sponsoring a public PHP Usage Survey.  The results will be shared with the PHP Group, and will help us to better understand the ways in which PHP is being used, and what may need improvement.  Fill it out and get a chance to win one of 50 PHP Tshirts!  </description>\n	<dc:date>2003-06-24</dc:date>\n</item>\n\n<item rdf:about=\"http://www.phpconference.de/2003/index_en.php\">\n	<title>International PHP Conference 2003</title>\n	<link>http://www.phpconference.de/2003/index_en.php</link>\n	<description>     The traditional International PHP Conference 2003 will be taking place from 2nd November to 5th November in Frankfurt (FFM).  The Call for Papers has been issued, so if you have an interesting talk, the organizers would love to hear about it! You can expect a gathering of PHP experts and core developers and to focus on PHP 5 as the main topic for the conference.   You can find the CfP on the website.  The deadline is 14th July, 2003.  The conference is also featuring an OpenSource Exhibition where PHP related OpenSource projects can present themselves for free.  </description>\n	<dc:date>2003-06-24</dc:date>\n</item>\n\n<item rdf:about=\"http://qa.php.net/\">\n	<title>PHP 4.3.3RC1 released</title>\n	<link>http://qa.php.net/</link>\n	<description>    PHP 4.3.3RC1 has been released for testing.  This is the first release candidate and should have a very low number of problems  and/or bugs. Nevertheless, please download and test it as much as possible on reallife   applications to uncover any remaining issues.    List of changes can be found in the   NEWS  file.  </description>\n	<dc:date>2003-06-19</dc:date>\n</item>\n\n<item rdf:about=\"http://www.ActiveState.com/Corporate/ActiveAwards/\">\n	<title>Active Awards Programmers\' Choice Nominees are in!</title>\n	<link>http://www.ActiveState.com/Corporate/ActiveAwards/</link>\n	<description>     Thanks to the community for recognizing their peers in ActiveState\'s third annual Active Awards.  The awards honor those individuals who actively contribute to open languages and display excellence in their programming efforts.  Please visit and help choose this years award winner!  More information and voting is at: http://www.ActiveState.com/Corporate/ActiveAwards/  </description>\n	<dc:date>2002-12-06</dc:date>\n</item>\n\n<item rdf:about=\"http://www.php.net/release_4_3_2.php\">\n	<title>PHP 4.3.2 Released!</title>\n	<link>http://www.php.net/release_4_3_2.php</link>\n	<description>   The PHP developers are proud to announce the immediate availability of PHP 4.3.2. This release contains a large number of bug fixes and is a strongly recommended update for all users of PHP. Full list of fixes can be found in the NEWS file.  </description>\n	<dc:date>2003-05-29</dc:date>\n</item>\n\n<item rdf:about=\"http://www.directi.com/?site=ip-to-country\">\n	<title>Automatic Mirror Select</title>\n	<link>http://www.directi.com/?site=ip-to-country</link>\n	<description>   In our ongoing battle to fight load and keep serving you content, whenever you perform a search on www.php.net, you will be redirected onto a nearby mirror (determined using the Directi IptoCountry Database).  You may experience a few quirks in the search until your mirrors have synced an uptodate version of the site.  </description>\n	<dc:date>2003-05-23</dc:date>\n</item>\n\n<item rdf:about=\"http://www.php.net/my.php\">\n	<title>Country Detection</title>\n	<link>http://www.php.net/my.php</link>\n	<description>   We are proud to introduce you the latest addition to our My PHP.net service. The PHP.net site and mirror sites now autodetect your country using the Directi IptoCountry Database. We use this information to present events in your country in bold letters on the frontpage, and to offer close mirror sites for downloads and your usual daily work.  </description>\n	<dc:date>2003-05-04</dc:date>\n</item>\n\n<item rdf:about=\"http://www.php.net/my.php\">\n	<title>My PHP.net</title>\n	<link>http://www.php.net/my.php</link>\n	<description>    The PHP website and mirrors sites now have a \'My PHP.net\' page, which allows you to check what language settings you have, and enables you to set one which will override all the other detected parameters.    However, normally this is not needed, as we remember the language you used last time. Be sure to have cookies turned on for PHP.net to let this feature work!  </description>\n	<dc:date>2003-04-24</dc:date>\n</item>\n\n<item rdf:about=\"http://www.phparch.com\">\n	<title>Grant Program</title>\n	<link>http://www.phparch.com</link>\n	<description>    php|architect, is proud to announce the creation of the php|architect Grant Program, whose goal is to provide financial support to bestofbreed PHPrelated projects.    Participation in the program is open to all opensource projects that are related to PHP (but not necessarily written in PHP). The program is accepting submissions now and will start distributing grants in June of 2003.    For more information, visit the program\'s website.  </description>\n	<dc:date>2003-03-06</dc:date>\n</item>\n\n<item rdf:about=\"http://www.php.net/echo\">\n	<title>Set your own language preference</title>\n	<link>http://www.php.net/echo</link>\n	<description>    Starting from today, your browser\'s &quot;Accept Language&quot; setting is also honored on language sensitive pages on the php.net site. If you would like to get to the documentation page of echo for example, you can use the /echo shortcut on all mirror sites, if your browser is set to provide your language preference information to the server. This also makes the PHP error message links point to the documentation in your preferred language.    You can set your preferences under Edit/Preferences/Navigator/Languages in Mozilla, and under Tools/Internet Options/Languages in Internet Explorer. This will probably also enhance your web experience on sites providing translated content.  </description>\n	<dc:date>2003-03-01</dc:date>\n</item>\n\n<item rdf:about=\"http://phpconf.hu/\">\n	<title>First Hungarian PHP Conference</title>\n	<link>http://phpconf.hu/</link>\n	<description>      The members of the Hungarian PHP community announce the first Hungarian PHP Conference which will take place in Budapest, on Saturday March 29th, sponsored by several international and local companies. The conference offers an entirely free one day activity with several presentations addressing basic and advanced topics, as well, exclusively in Hungarian. Moreover, a five kilobytelimited PHP contest has been started to discover the most talented PHP programmers in our country. The programme includes the first session of the socalled PHP Division which will be established with the set purpose of representing the community itself and promoting their interests in any national business and official phorums.  </description>\n	<dc:date>2003-02-25</dc:date>\n</item>\n\n<item rdf:about=\"http://www.php.net/release_4_3_1.php\">\n	<title>PHP 4.3.1 released in response to CGI vulnerability</title>\n	<link>http://www.php.net/release_4_3_1.php</link>\n	<description>    The PHP Group today announced the details of a serious CGI vulnerability in PHP version 4.3.0. A security update, PHP 4.3.1, fixes the issue. Everyone running affected version of PHP (as CGI) are encouraged to upgrade immediately. The new 4.3.1 release does not include any other changes, so upgrading from 4.3.0 is safe and painless.  </description>\n	<dc:date>2003-02-17</dc:date>\n</item>\n<!-- / RSS-Items PHP/RSS -->\n</rdf:RDF>\n');
INSERT INTO tiki_rss_modules VALUES (2,'slashdot','','http://slashdot.org/slashdot.rdf',1800,1058295004,'<?xml version=\"1.0\"?>\n\n<rdf:RDF\nxmlns:rdf=\"http://www.w3.org/1999/02/22-rdf-syntax-ns#\"\nxmlns=\"http://my.netscape.com/rdf/simple/0.9/\">\n\n<channel>\n<title>Slashdot</title>\n<link>http://slashdot.org/</link>\n<description>News for nerds, stuff that matters</description>\n</channel>\n\n<image>\n<title>Slashdot</title>\n<url>http://images.slashdot.org/topics/topicslashdot.gif</url>\n<link>http://slashdot.org/</link>\n</image>\n\n<item>\n<title>The IT Market: Cyclical Downturn or New World Order?</title>\n<link>http://slashdot.org/article.pl?sid=03/07/15/188251</link>\n</item>\n\n<item>\n<title>The Mozilla Foundation</title>\n<link>http://slashdot.org/article.pl?sid=03/07/15/1736223</link>\n</item>\n\n<item>\n<title>All The Rave</title>\n<link>http://slashdot.org/article.pl?sid=03/07/11/1858235</link>\n</item>\n\n<item>\n<title>Matrix Reloaded on DVD Before Revolutions</title>\n<link>http://slashdot.org/article.pl?sid=03/07/15/1459257</link>\n</item>\n\n<item>\n<title>EU Rolls out Anti Spam Strategy</title>\n<link>http://slashdot.org/article.pl?sid=03/07/15/1359252</link>\n</item>\n\n<item>\n<title>New Kazaa Lite Protects Identity</title>\n<link>http://slashdot.org/article.pl?sid=03/07/15/1326240</link>\n</item>\n\n<item>\n<title>Big Brother Gets a Brain</title>\n<link>http://slashdot.org/article.pl?sid=03/07/15/1231217</link>\n</item>\n\n<item>\n<title>OpenOffice 1.1 RC 1 Released</title>\n<link>http://slashdot.org/article.pl?sid=03/07/15/1157247</link>\n</item>\n\n<item>\n<title>State Of The Filesystem</title>\n<link>http://slashdot.org/article.pl?sid=03/07/15/0615221</link>\n</item>\n\n<item>\n<title>LinuxTag: 40% Growth Over Last Year</title>\n<link>http://slashdot.org/article.pl?sid=03/07/15/018235</link>\n</item>\n\n<textinput>\n<title>Search Slashdot</title>\n<description>Search Slashdot stories</description>\n<name>query</name>\n<link>http://slashdot.org/search.pl</link>\n</textinput>\n\n</rdf:RDF>');
INSERT INTO tiki_rss_modules VALUES (3,'debianplanet','debianplanet','http://debianplanet.org/module.php?mod=node&op=feed',3600,1058293144,'<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n<rss version=\"0.91\">\n<channel>\n <title>Debian Planet</title>\n <link>http://www.debianplanet.org/</link>\n <description>Everything Debian! News, interviews, comment.</description>\n <language>en</language>\n<item>\n <title>IBM on building Debian packages</title>\n <link>http://www.debianplanet.org/node.php?id=982</link>\n <description>Seen over at /.: IBM has put up a document by a freelance write on how to create Debian packages. Its nothing more than a short intro but I guess it will generate some interest...the Maintainer\'s Guide does a better job though!</description>\n</item>\n<item>\n <title>open.hands.com replaced, and in need of some test load</title>\n <link>http://www.debianplanet.org/node.php?id=980</link>\n <description>As I\'m sure most of you know, open.hands.com (previously serving www.uk.d.o) has been sadly unreliable for ages, so I recently replaced all the hardware (again, for about the 4th time).\n\nThis time with hardware that actually cost real money, namely a dual Xeon 1800 machine, with 2GB of ECC RAM, and a 3ware IDE RAID card with 4 120GB disks hanging off of it.\n\nOf course, this only ran for about a week before crashing, but the new hardware gave it enough headroom for it to provide some diagnosable symptoms --- it was occasionally managing quarter of a million context switches a second.</description>\n</item>\n<item>\n <title>Debian boot-floppies Gets More Bad Press</title>\n <link>http://www.debianplanet.org/node.php?id=978</link>\n <description>The Independent Newspaper has a &lt;a href=&quot;http://news.independent.co.uk/digital/features/story.jsp?story=420948&quot;&gt; review&lt;/a&gt; which thrashes Debian solely on basis of the author\'s bad experience with the installer.</description>\n</item>\n<item>\n <title>Securing Linux - Interview of Russell Coker</title>\n <link>http://www.debianplanet.org/node.php?id=977</link>\n <description>Today\'s Australian Age has an &lt;a href=&quot;http://www.theage.com.au/articles/2003/07/02/1056825450368.html&quot;&gt;interview &lt;/a&gt; of SE-Linux Debian Developer Russel Coker. Have you tested his SE Linux &quot;play&quot; machine with a public root password? You can find more about it &lt;a href=&quot;http://www.coker.com.au/selinux/&quot;&gt;here&lt;/a&gt;.</description>\n</item>\n<item>\n <title>Debian feature on SitePoint</title>\n <link>http://www.debianplanet.org/node.php?id=976</link>\n <description>My name is &lt;a href=&quot;http://www.jonobacon.org/&quot;&gt;Jono Bacon&lt;/a&gt; and I am a KDE developer and writer. I have recently written an article that introduces Debian and includes guidelines on installing Debian. The article is available at &lt;a href=&quot;http://www.sitepoint.com/article/1158&quot;&gt;SitePoint&lt;/a&gt;.</description>\n</item>\n<item>\n <title>Debootstrap/LVM with LNX-BBC 2.1</title>\n <link>http://www.debianplanet.org/node.php?id=975</link>\n <description>The new &lt;a href=&quot;http://www.lnx-bbc.org/&quot;&gt;LNX-BBC&lt;/a&gt; is out and being distributed by me (Jim Dennis) and friends of mine.  It\'s the continuing efforts of the orginal Linuxcare &quot;bootable business card&quot; crowd after we\'ve all left Linuxcare over the years.\n\nSome of the old versions of the BBC included &lt;b&gt;debootstrap&lt;/b&gt; and I used it routinely use it as my Debian installer.  However, this version doesn\'t include it.</description>\n</item>\n<item>\n <title>Australian Personal Computer on Debian</title>\n <link>http://www.debianplanet.org/node.php?id=974</link>\n <description>I went off to my local Border\'s this morning, and in between flicking through a few cookbooks, Motor magazine (hooray for a 533kW, 1250kg Evo VII), and a book with a huge rap from Branden &quot;Overfiend&quot; Robinson, I had a not-so-quick flick through &lt;a href=&quot;http://apcmag.com&quot;&gt;APC&lt;/a&gt;, which &lt;a href=&quot;http://www.debianplanet.org/node.php?id=972&quot;&gt;included Debian on its cover CD&lt;/a&gt;. Read on to see what they had to say about Debian.</description>\n</item>\n<item>\n <title>Bonzai Linux Version 2.0</title>\n <link>http://www.debianplanet.org/node.php?id=973</link>\n <description>Immediately after the release of version 1.8, the Debian based mini-distribution Bonzai Linux is now available in version 2.0. The step in the version numbering scheme should indicate that this is a \'stable\' Release.\n\nThe only change that has been made is to the kernel version. In version 1.8 of Bonzai Linux, kernel 2.4.20 was used. The current boot-floppies have been rebuilt to use Kernel 2.4.21 instead. This kernel has been compiled with gcc-3.2 due to space restrictions.</description>\n</item>\n<item>\n <title>Australian Personal Computer includes Debian on Cover CD</title>\n <link>http://www.debianplanet.org/node.php?id=972</link>\n <description>&lt;a href=&quot;http://www.apcmag.com&quot;&gt;Australian Personal Computer&lt;/a&gt; includes Debian 3.0 (2 CDs) on its cover this Month (July 2003).  This is the first time I have seen Debian on a cover CD in Australia.  APC has a wide distribution in Australia, so I think this is great news.  I must say that I am a little surprised that they went with Debian rather than (the usually trivial to get going) Knoppix, but kudos to them for making the right choice.  DebianPlanet is mentioned in the article, too ...\n\n&lt;b&gt;DanielS&lt;/b&gt;: APC are, I think, Australia\'s largest-selling computer magazine; unfortunately their articles aren\'t online.</description>\n</item>\n<item>\n <title>Debian 10th birthday party coordination page</title>\n <link>http://www.debianplanet.org/node.php?id=971</link>\n <description>On August 16th 2003 Debian hits double digits, and lots of people think that sounds like a pretty good reason for a party - or lots of parties! Trouble is there hasn\'t been a simple way to figure out who\'s organising what or where, so Debconf.org has just &lt;a href=&quot;http://www.debconf.org/10years/&quot;&gt;put up a page&lt;/a&gt; to help potential party-goers get in touch. It\'s still pretty thin, so if you know of events in your local area make sure you send in the details.</description>\n</item>\n<item>\n <title>Compiling kernels The Debian Way</title>\n <link>http://www.debianplanet.org/node.php?id=969</link>\n <description>Debian has some really cool tools for managing the process of compiling and packaging custom kernels, but a lot of Debian users still do things the traditional way.  The current issue of &lt;a href=&quot;http://www.linmagau.org/&quot;&gt;linmagau&lt;/a&gt;, the Australian Linux magazine, is &lt;a href=&quot;http://www.linmagau.org/modules.php?op=modload&amp;name=Sections&amp;file=index&amp;req=viewarticle&amp;artid=158&quot;&gt;carrying an article&lt;/a&gt; that walks step by step through getting kernel source and configuring, then compiling and building a custom kernel package that can be installed using dpkg just like any other package.</description>\n</item>\n<item>\n <title>GNU/FreeBSD progress</title>\n <link>http://www.debianplanet.org/node.php?id=968</link>\n <description>The Debian GNU/FreeBSD port has recently made significant progress using the &lt;a href=&quot;http://www.gnu.org/software/glibc/&quot;&gt;GNU C library&lt;/a&gt; as a base instead of FreeBSD\'s libc. The result has been a great improvement in portability, which allowed a single developer to bootstrap Debian to the point of having a &lt;a href=&quot;http://lists.debian.org/debian-bsd/2003/debian-bsd-200306/msg00001.html&quot;&gt;working xfree86&lt;/a&gt; package in matter of a few weeks. There\'s currently a base &lt;a href=&quot;http://people.debian.org/~rmh/gnu-freebsd/pub/&quot;&gt;GNU/FreeBSD tarball&lt;/a&gt; that can be installed as a standalone system, an &lt;a href=&quot;http://lists.debian.org/debian-bsd/2003/debian-bsd-200306/msg00004.html&quot;&gt;APT repository&lt;/a&gt; and a &lt;a href=&quot;http://www.debian.org/ports/freebsd/gnu-libc-based&quot;&gt;website summarizing the current status of the port&lt;/a&gt;.</description>\n</item>\n<item>\n <title>X broken in unstable (GCC 3.3 fun)</title>\n <link>http://www.debianplanet.org/node.php?id=966</link>\n <description>If you are tracking unstable, you might want to postpone the upgrade of X packages.  Version 4.2.1-7 breaks XDM, KDM (GDM2 works fine for me) and startx in due to some problems with GCC 3.3 compiling code related to X authorization cookies. See &lt;a href=&quot;http://bugs.debian.org/cgi-bin/bugreport.cgi?bug=196575&quot;&gt;bug #196575&lt;/a&gt; for details. Filing further bugs is not necessary, Branden (X maintainer) is painfully aware of the problem.\n\n&lt;b&gt;DanielS&lt;/b&gt;: 4.2.1-8 was uploaded last night (my time), should start hitting the mirrors any time now.</description>\n</item>\n<item>\n <title>Bonzai Linux releases version 1.7</title>\n <link>http://www.debianplanet.org/node.php?id=965</link>\n <description>Bonzia Linux, the Debian based distribution that fits on a 180MB CD-R(W) and features KDE 3.1.2, has been updated to version 1.7 as of June 5th, 2003.  You can download an ISO at &lt;a href=&quot;http://developer.berlios.de/projects/bonzai/&quot;&gt;http://developer.berlios.de/projects/bonzai/&lt;/a&gt;.</description>\n</item>\n<item>\n <title>Exim+Courier+Debian HOWTO</title>\n <link>http://www.debianplanet.org/node.php?id=962</link>\n <description>Jason Boxman has posted a &lt;a href=&quot;http://talk.trekweb.com/~jasonb/articles/exim_maildir_imap.shtml&quot;&gt;Exim+Courier+Debian HOWTO&lt;/a&gt; on his site, that describes in some depth how to set up a working system with Exim, Courier IMAP, and Maildirs, as well as covering topics like mail filtering, and MUA setup.</description>\n</item>\n</channel>\n</rss>\n');





CREATE TABLE tiki_search_stats (
"term" varchar(50) NOT NULL default '',
"hits" integer NOT NULL default '0',
PRIMARY KEY ("term")
) ;






INSERT INTO tiki_search_stats VALUES ('jobhunting',32);
INSERT INTO tiki_search_stats VALUES ('test\'',6);
INSERT INTO tiki_search_stats VALUES ('test',124);
INSERT INTO tiki_search_stats VALUES ('blog',1);
INSERT INTO tiki_search_stats VALUES ('the',2);
INSERT INTO tiki_search_stats VALUES ('tide',42);
INSERT INTO tiki_search_stats VALUES ('job',11);
INSERT INTO tiki_search_stats VALUES ('',13);
INSERT INTO tiki_search_stats VALUES ('help',1);
INSERT INTO tiki_search_stats VALUES ('damn',2);
INSERT INTO tiki_search_stats VALUES ('search',1);
INSERT INTO tiki_search_stats VALUES ('OK',1);
INSERT INTO tiki_search_stats VALUES ('stem',3);
INSERT INTO tiki_search_stats VALUES ('test2',1);
INSERT INTO tiki_search_stats VALUES ('table',1);
INSERT INTO tiki_search_stats VALUES ('text',1);
INSERT INTO tiki_search_stats VALUES ('hunting',2);





CREATE TABLE tiki_semaphores (
"semName" varchar(30) NOT NULL default '',
"timestamp" integer NOT NULL default '0',
"user" varchar(200) NOT NULL default '',
PRIMARY KEY ("semName")
) ;











CREATE TABLE tiki_sent_newsletters (
"editionId" integer NOT NULL default nextval('tiki_sent_newsletters_seq') unique not null,
"nlId" integer NOT NULL default '0',
"users" integer NOT NULL default '0',
"sent" integer NOT NULL default '0',
"subject" varchar(200) NOT NULL default '',
"data" text,
PRIMARY KEY ("editionId")
) ;











CREATE TABLE tiki_sessions (
"sessionId" varchar(32) NOT NULL default '',
"timestamp" integer NOT NULL default '0',
"user" varchar(200) NOT NULL default '',
PRIMARY KEY ("sessionId")
) ;






INSERT INTO tiki_sessions VALUES ('b25d0d0fc59003b7620bef86facf4963',1058295027,'ross');





CREATE TABLE tiki_shoutbox (
"msgId" integer NOT NULL default nextval('tiki_shoutbox_seq') unique not null,
"message" varchar(255) NOT NULL default '',
"timestamp" integer NOT NULL default '0',
"user" varchar(200) NOT NULL default '',
"hash" varchar(32) NOT NULL default '',
PRIMARY KEY ("msgId")
) ;






INSERT INTO tiki_shoutbox VALUES (11,'11:05PST',1045076860,'ross','039d946c6361a9fe4015ebcc29d71a6b');





CREATE TABLE tiki_structures (
"page" varchar(240) NOT NULL default '',
"parent" varchar(240) NOT NULL default '',
"pos" integer NOT NULL default '0',
PRIMARY KEY ("parent","page")
) ;











CREATE TABLE tiki_submissions (
"subId" integer NOT NULL default nextval('tiki_submissions_seq') unique not null,
"title" varchar(80) NOT NULL default '',
"authorName" varchar(60) NOT NULL default '',
"topicId" integer NOT NULL default '0',
"topicName" varchar(40) NOT NULL default '',
"size" integer NOT NULL default '0',
"useImage" varchar(1) NOT NULL default '',
"image_name" varchar(80) NOT NULL default '',
"image_type" varchar(80) NOT NULL default '',
"image_size" integer NOT NULL default '0',
"image_x" integer NOT NULL default '0',
"image_y" integer NOT NULL default '0',
"image_data" text,
"publishDate" integer NOT NULL default '0',
"created" integer NOT NULL default '0',
"heading" text NOT NULL,
"body" text,
"hash" varchar(32) NOT NULL default '',
"author" varchar(200) NOT NULL default '',
"reads" integer NOT NULL default '0',
"votes" integer NOT NULL default '0',
"points" integer NOT NULL default '0',
"type" varchar(50) NOT NULL default '',
"rating" decimal(4,2) NOT NULL default '0.00',
"isfloat" varchar(1) NOT NULL default '',
PRIMARY KEY ("subId")
) ;






INSERT INTO tiki_submissions VALUES (1,'test','test',1,'A Test Topic',36,'n','','',0,0,0,'',1045228020,1045069773,'test','publish date: 05:07 NZST (09:07 PST)','1fb0e331c05a52d5eb847d6fc018320d','ross',0,0,0,'Article',7.00,'n');
INSERT INTO tiki_submissions VALUES (2,'TEST','',1,'A Test Topic',4,'n','','',0,0,0,'',1045213740,1045069809,'TEST','TEST','67a2949e8d29cd4c7f572144e46a1b85','ross',0,0,0,'Article',7.00,'n');
INSERT INTO tiki_submissions VALUES (3,'test','test',1,'A Test Topic',10,'n','','',0,0,0,'',1045142160,1045070249,'test','05:16 NZST','3832e1825d1bbdf709bde7917b2c10e0','ross',0,0,0,'Article',7.00,'n');
INSERT INTO tiki_submissions VALUES (4,'TEST','TEST',1,'A Test Topic',10,'n','','',0,0,0,'',1045142280,1045070341,'TEST','05:18 nzst','464f3675e27c061dfd790c25e1e2442d','ross',0,0,0,'Article',7.00,'n');
INSERT INTO tiki_submissions VALUES (5,'test','test',1,'A Test Topic',21,'n','','',0,0,0,'',1044900360,1045076069,'etst','10:06 pst (6:06 nzst)','7afcdbf2aec78e0fa3cef752baf6589c','ross',0,0,0,'Article',7.00,'n');





CREATE TABLE tiki_suggested_faq_questions (
"sfqId" integer NOT NULL default nextval('tiki_suggested_faq_question_seq') unique not null,
"faqId" integer NOT NULL default '0',
"question" text NOT NULL,
"answer" text NOT NULL,
"created" integer NOT NULL default '0',
"user" varchar(200) NOT NULL default '',
PRIMARY KEY ("sfqId")
) ;











CREATE TABLE tiki_survey_question_options (
"optionId" integer NOT NULL default nextval('tiki_survey_question_option_seq') unique not null,
"questionId" integer NOT NULL default '0',
"qoption" text NOT NULL,
"votes" integer NOT NULL default '0',
PRIMARY KEY ("optionId")
) ;











CREATE TABLE tiki_survey_questions (
"questionId" integer NOT NULL default nextval('tiki_survey_questions_seq') unique not null,
"surveyId" integer NOT NULL default '0',
"question" text NOT NULL,
"options" text NOT NULL,
"type" varchar(1) NOT NULL default '',
"position" integer NOT NULL default '0',
"votes" integer NOT NULL default '0',
"value" integer NOT NULL default '0',
"average" decimal(4,2) NOT NULL default '0.00',
PRIMARY KEY ("questionId")
) ;











CREATE TABLE tiki_surveys (
"surveyId" integer NOT NULL default nextval('tiki_surveys_seq') unique not null,
"name" varchar(200) NOT NULL default '',
"description" text NOT NULL,
"taken" integer NOT NULL default '0',
"lastTaken" integer NOT NULL default '0',
"created" integer NOT NULL default '0',
"status" varchar(1) NOT NULL default '',
PRIMARY KEY ("surveyId")
) ;











CREATE TABLE tiki_tags (
"tagName" varchar(80) NOT NULL default '',
"pageName" varchar(160) NOT NULL default '',
"hits" integer NOT NULL default '0',
"data" text,
"lastModif" integer NOT NULL default '0',
"comment" varchar(200) NOT NULL default '',
"version" integer NOT NULL default '0',
"user" varchar(200) NOT NULL default '',
"ip" varchar(15) NOT NULL default '',
"flag" varchar(1) NOT NULL default '',
"description" varchar(200) NOT NULL default '',
PRIMARY KEY ("pageName","tagName")
) ;






INSERT INTO tiki_tags VALUES ('test','HomePage',740,'[http://tikiwiki.sf.net/|tiki]',1045231844,'',31,'ross','192.168.1.2','','');
INSERT INTO tiki_tags VALUES ('test','JobHunting',12,'[http://losangeles.craigslist.org/eng/] - while this site looks good, I\'ve submitted several resumes and have received 0 replies.  Probably just spammers harvesting email addresses....',1039947473,'',2,'ross','192.168.1.2','','');
INSERT INTO tiki_tags VALUES ('test','NoHTMLCodeIsNeeded',2,'This is another page ((AWordWithCapitals))',1038794163,'',1,'ross','192.168.1.2','','');
INSERT INTO tiki_tags VALUES ('test','AWordWithCapitals',1,'Another Page\r\n\r\n((HomePage))',1038794197,'',1,'ross','192.168.1.2','','');
INSERT INTO tiki_tags VALUES ('test','LisasPage',1,'\r\nPer Lisa\'s request, here are the definitions from Monier-Williams\' Sanskrit-English Dictionary for ha, Tha, and haTha I found using the search engine:\r\n\r\n[http://www.uni-koeln.de/phil-fak/indologie/tamil/mwd_search.html]\r\n\r\n* ha\r\n \r\nMeaning  1 the thirty-third and last consonant of the Na1gari1 alphabet (in Pa1n2ini\'s system belonging to the guttural class , and usually pronounced like the English %{h} in %{hard} ; it is not an original letter , but is mostly derived from an older %{gh} , rarely from %{dh} or %{bh}).\r\n\r\nMeaning  2 (only L.) m. a form of S3iva or Bhairava (cf. %{nakulI7za}) ; water ; a cipher (i.e. the arithmetical figure which symbolizes o) ; meditation , auspiciousness ; sky , heaven , paradise ; blood ; dying ; fear ; knowledge ; the moon ; Vishn2u ; war , battle ; horripilation ; a horse ; pride ; a physician ; cause , motive ; = %{pApa-haraNa} ; = %{sakopa-vAraNa} ; = %{zuSka} ; (also %{A} f.) laughter ; (%{A}) f. coition ; a lute (%{am}) n. the Supreme Spirit ; pleasure , delight ; a weapon ; the sparkling of a gem ; calling , calling to the sound of a lute ; (ind.) = %{aham} (?) , IndSt. ; mfn. mad , drunk. \r\n \r\nMeaning  3 ind. (prob. orig. identical with 2. %{gha} , and used as a particle for emphasizing a preceding word , esp. if it begins a sentence closely connected with another ; very frequent in the Bra1hman2as and Su1tras , and often translatable by) indeed , assuredly , verily , of course , then &c. (often with other particles e.g. with %{tv@eva} , %{u} , %{sma} , %{vai} &c. ; %{na@ha} , `\" not indeed \"\' ; also with interrogatives and relatives e.g. %{yad@dha} , `\" when indeed \"\' ; %{kad@dha} , `\" what then? \"\' sometimes with impf. or pf. [cf. Pa1n2. 3-2 , 116] ; in later language very commonly used as a mere expletive , esp. at the end of a verse) RV. &c. &c. \r\n\r\nMeaning  4 mf(%{A})n. (fr. %{han}) killing , destroying , removing (only ifc. ; see %{arAti-} , %{vRtra-} , %{zatruha} &c.) \r\n\r\nMeaning  5 mf(%{A})n. (fr. 3. %{hA}) abandoning , deserting , avoiding (ifc. ; see %{an-oka-} and %{vApI-ha}) ; (%{A}) f. abandonment , desertion L. \r\n\r\n* Tha\r\n \r\nMeaning  1 the aspirate of the preceding consonant.\r\n\r\nMeaning  2 m. a loud noise (%{ThaThaM@ThaThaM@ThaM@ThaThaThaM@ThaThaM@ThaH} , an imitative sound as of a golden pitcher rolling down steps Maha1n.2 iii , 5) L. ; the moon\'s disk L. ; a disk L. ; a cypher L. ; a place frequented by all L. ; S3iva L. \r\n\r\n* haTha\r\n \r\nMeaning  m. violence , force (ibc. , %{ena} , and %{At} , `\" by force , forcibly \"\') R. Ra1jat. Katha1s. &c. ; obstinacy , pertinacity (ibc. and %{At} , `\" obstinately , persistently \"\') Pan5cat. Katha1s. ; absolute or inevitable necessity (as the cause of all existence and activity ; ibc. , %{At} , and %{ena} , `\" necessarily , inevitably , by all means \"\') MBh. Ka1v. &c. ; = %{haTha-yoga} Cat. ; oppression W. ; rapine ib. ; going in the rear of an enemy L. ; Pistia Stratiotes L. \r\n\r\n* haTha-yoga\r\n\r\nMeaning m. a kind of forced Yoga or abstract meditation (forcing the mind to withdraw from external objects; treated of in the HaTha-pradIpikA by SvAtmArAma and performed with much self-torture, such as standing on one leg, holding up the arms, inhaling smoke with the head inverted &c.)\r\n\r\n',1038940749,'',1,'ross','192.168.1.2','','');
INSERT INTO tiki_tags VALUES ('test','NewPage',1,'Adding an entry to test the full text search function.\r\n\r\nHere are some search terms:\r\n\r\nJobHunting\r\ntest\r\ntiki\r\n\r\nNextPage',1040866218,'',1,'ross','192.168.1.2','','');
INSERT INTO tiki_tags VALUES ('test','NextPage',1,'Adding an entry to test the full text search function.\r\n\r\nHere are some search terms:\r\n\r\nJobHunting\r\ntest\r\ntiki\r\n',1040866230,'',1,'ross','192.168.1.2','','');





CREATE TABLE tiki_theme_control_categs (
"categId" integer NOT NULL default '0',
"theme" varchar(250) NOT NULL default '',
PRIMARY KEY ("categId")
) ;











CREATE TABLE tiki_theme_control_objects (
"objId" varchar(250) NOT NULL default '',
"type" varchar(250) NOT NULL default '',
"name" varchar(250) NOT NULL default '',
"theme" varchar(250) NOT NULL default '',
PRIMARY KEY ("objId")
) ;











CREATE TABLE tiki_theme_control_sections (
"section" varchar(250) NOT NULL default '',
"theme" varchar(250) NOT NULL default '',
PRIMARY KEY ("section")
) ;











CREATE TABLE tiki_topics (
"topicId" integer NOT NULL default nextval('tiki_topics_seq') unique not null,
"name" varchar(40) NOT NULL default '',
"image_name" varchar(80) NOT NULL default '',
"image_type" varchar(80) NOT NULL default '',
"image_size" integer NOT NULL default '0',
"image_data" text,
"active" varchar(1) NOT NULL default '',
"created" integer NOT NULL default '0',
PRIMARY KEY ("topicId")
) ;






INSERT INTO tiki_topics VALUES (1,'A Test Topic','php.gif','image/gif',1160,'GIF89af\0/\0„\0\0\0\0\0ÿÿÿ™™™\0333\0333333f3fff3™ff™3f™ffÌf3ff™Ì™™Ìf™™fff™™fÌÌÌf33ÌÌÿ33\03\0\03f3ff333™ÿÿÿÿÿÿÿÿÿÿÿÿÿÿÿÿÿÿ,\0\0\0\0f\0/\0\0ş  dihª®lËp,Ïtmßx®ï·8ÀL eAÚñˆLÆ€Ì¡ó-\Zi>dmYŒ\Z½Ã°vK¾âˆ`t-ûœ~Íí)÷§+İs*XŒXµx|RiovO‚d{{lƒ]?ˆ‰yqt^U9˜‡1<Ÿ ¡Ÿ.¤¥¦§¨©ª%±²±­«º»*°	\n	ÄÄÅÄÃ\nË±·¼Ğ©ÈÕÉÇÂ	ÃÖÃµ¹Ñá)°ÆÚ\nÛÂÛØçæÆçÊÖÎàââ	\r\rÚÉÈØÂ\0ºãgî·±èE\0á_±aÑA<ADße»¶1A†g\nWA?à _DÅş°=€ b‚ÉtåÚÁ4ˆ°@ÈT(8Ø‡ò?Äl€p;uÙŞ)óu³TÎ|ì´5x°àA¾©ĞÚ?ªÕcULM\0p?€ØÀ*°Õ´dï0ğE—®€¬	Œ¨[\0ÂÙøÒõ‹ÂymO@ \0U+Ùq~ó¥¨ àä?\'\r$FqcJˆF@\rÁÂ²,ô-T5@Ñ¤I€gPóæ<ëãÖ ´ë’‹¸à uÃ³RHnxğú)c{[?İ0‡98 ÂƒdÅ›=‹@Ax\0Î7Ìv¾\0·¨	Zß°±ß6áC k/¡\0Êû”wñ&”=ìLşd[uö$U_§}çWX¤O^t\'?èsÌ€ùñã\rb‰Ñ•ZfqXh#\0aQ>ÚèE`…\0°Ó0½µØO;?Øïu´€rÿüóÀ?ØvDDVô[;Q˜?9VgÀLæ¬´]{İÅŒZª&Akpi‘\0Á\'ßmYLk’PÀ_§™âEqîUÒ…IQW?WBÓ?°øB˜6`àËŠ¨-J£j¶z×:ÙÔö§œï©dÚœÀ…U)”0~JÖ¦p¨UUºÛ1U^Š3ùvÑC)e“A)¥V?g]hX?®–`\05Ä´§[:Ü¬#QRÛ5G«2`Q™§«°ş(`^|}¡ƒOğEkV™ö`¤-ŸKM,,<pÖ]ÜF› ƒĞ\n îNMÄª¹ÁîeÀ­ûDë`­şÚgT­	\0R¾+¼²ï>‚®:°F\ZyØÎZdH?(9$K²Ùíè/ŸH)³V_H€qMİ\"‹a>%¥•ck!`‹ÅCó\nºmRÃr2ÌâÌÁrÂÆĞ>=ƒEûÑJ1ørs\07\0	? MtÕXW=‚ĞB£ µÕ[ƒ?µØa[=ö	_O]öÙkgAu	_§ uÜj\'İõÜn?]wÒfoİˆŞgÿÍ÷ßYÇ?6ßEã\röà‰7Õun÷ä~DN¹\nw«-øåŒ÷M´ß›{>wäl?^xÍ¨§®:*!\0\0;','y',1045068208);





CREATE TABLE tiki_tracker_fields (
"fieldId" integer NOT NULL default nextval('tiki_tracker_fields_seq') unique not null,
"trackerId" integer NOT NULL default '0',
"name" varchar(80) NOT NULL default '',
"options" text NOT NULL,
"type" varchar(1) NOT NULL default '',
"isMain" varchar(1) NOT NULL default '',
"isTblVisible" varchar(1) NOT NULL default '',
PRIMARY KEY ("fieldId")
) ;











CREATE TABLE tiki_tracker_item_attachments (
"attId" integer NOT NULL default nextval('tiki_tracker_item_attachmen_seq') unique not null,
"itemId" varchar(40) NOT NULL default '',
"filename" varchar(80) NOT NULL default '',
"filetype" varchar(80) NOT NULL default '',
"filesize" integer NOT NULL default '0',
"user" varchar(200) NOT NULL default '',
"data" text,
"path" varchar(255) NOT NULL default '',
"downloads" integer NOT NULL default '0',
"created" integer NOT NULL default '0',
"comment" varchar(250) NOT NULL default '',
PRIMARY KEY ("attId")
) ;











CREATE TABLE tiki_tracker_item_comments (
"commentId" integer NOT NULL default nextval('tiki_tracker_item_comments_seq') unique not null,
"itemId" integer NOT NULL default '0',
"user" varchar(200) NOT NULL default '',
"data" text NOT NULL,
"title" varchar(200) NOT NULL default '',
"posted" integer NOT NULL default '0',
PRIMARY KEY ("commentId")
) ;











CREATE TABLE tiki_tracker_item_fields (
"itemId" integer NOT NULL default '0',
"fieldId" integer NOT NULL default '0',
"value" text NOT NULL,
PRIMARY KEY ("fieldId","itemId")
) ;











CREATE TABLE tiki_tracker_items (
"itemId" integer NOT NULL default nextval('tiki_tracker_items_seq') unique not null,
"trackerId" integer NOT NULL default '0',
"created" integer NOT NULL default '0',
"status" varchar(1) NOT NULL default '',
"lastModif" integer NOT NULL default '0',
PRIMARY KEY ("itemId")
) ;











CREATE TABLE tiki_trackers (
"trackerId" integer NOT NULL default nextval('tiki_trackers_seq') unique not null,
"name" varchar(80) NOT NULL default '',
"description" text NOT NULL,
"created" integer NOT NULL default '0',
"lastModif" integer NOT NULL default '0',
"showCreated" varchar(1) NOT NULL default '',
"showStatus" varchar(1) NOT NULL default '',
"showLastModif" varchar(1) NOT NULL default '',
"useComments" varchar(1) NOT NULL default '',
"useAttachments" varchar(1) NOT NULL default '',
"items" integer NOT NULL default '0',
PRIMARY KEY ("trackerId")
) ;











CREATE TABLE tiki_untranslated (
"id" integer NOT NULL default nextval('tiki_untranslated_seq') unique not null,
"source" text NOT NULL,
"lang" varchar(2) NOT NULL default '',
PRIMARY KEY ("lang","source")
,UNIQUE ("id")

) ;











CREATE TABLE tiki_user_answers (
"userResultId" integer NOT NULL default '0',
"quizId" integer NOT NULL default '0',
"questionId" integer NOT NULL default '0',
"optionId" integer NOT NULL default '0',
PRIMARY KEY ("userResultId","quizId","optionId","questionId")
) ;











CREATE TABLE tiki_user_assigned_modules (
"name" varchar(200) NOT NULL default '',
"position" varchar(1) NOT NULL default '',
"ord" integer NOT NULL default '0',
"type" varchar(1) NOT NULL default '',
"title" varchar(40) NOT NULL default '',
"cache_time" integer NOT NULL default '0',
"rows" integer NOT NULL default '0',
"groups" text NOT NULL,
"user" varchar(200) NOT NULL default '',
PRIMARY KEY ("user","name")
) ;











CREATE TABLE tiki_user_bookmarks_folders (
"folderId" integer NOT NULL default nextval('tiki_user_bookmarks_folders_seq') unique not null,
"parentId" integer NOT NULL default '0',
"user" varchar(200) NOT NULL default '',
"name" varchar(30) NOT NULL default '',
PRIMARY KEY ("folderId","user")
) ;











CREATE TABLE tiki_user_bookmarks_urls (
"urlId" integer NOT NULL default nextval('tiki_user_bookmarks_urls_seq') unique not null,
"name" varchar(30) NOT NULL default '',
"url" varchar(250) NOT NULL default '',
"data" text,
"lastUpdated" integer NOT NULL default '0',
"folderId" integer NOT NULL default '0',
"user" varchar(200) NOT NULL default '',
PRIMARY KEY ("urlId")
) ;











CREATE TABLE tiki_user_mail_accounts (
"accountId" integer NOT NULL default nextval('tiki_user_mail_accounts_seq') unique not null,
"user" varchar(200) NOT NULL default '',
"account" varchar(50) NOT NULL default '',
"pop" varchar(255) NOT NULL default '',
"current" varchar(1) NOT NULL default '',
"port" integer NOT NULL default '0',
"username" varchar(100) NOT NULL default '',
"pass" varchar(100) NOT NULL default '',
"msgs" integer NOT NULL default '0',
"smtp" varchar(255) NOT NULL default '',
"useAuth" varchar(1) NOT NULL default '',
"smtpPort" integer NOT NULL default '0',
PRIMARY KEY ("accountId")
) ;






INSERT INTO tiki_user_mail_accounts VALUES (1,'ross','ross@yogamala.com','yogamala.com','y',110,'srikpj','joisrock',20,'localhost','n',25);





CREATE TABLE tiki_user_menus (
"user" varchar(200) NOT NULL default '',
"menuId" integer NOT NULL default nextval('tiki_user_menus_seq') unique not null,
"url" varchar(250) NOT NULL default '',
"name" varchar(40) NOT NULL default '',
"position" integer NOT NULL default '0',
"mode" varchar(1) NOT NULL default '',
PRIMARY KEY ("menuId")
) ;











CREATE TABLE tiki_user_modules (
"name" varchar(200) NOT NULL default '',
"title" varchar(40) NOT NULL default '',
"data" text,
PRIMARY KEY ("name")
) ;






INSERT INTO tiki_user_modules VALUES ('php.net','php.net','{rss id=1}');
INSERT INTO tiki_user_modules VALUES ('slashdot','slashdot','{rss id=2}');
INSERT INTO tiki_user_modules VALUES ('debianplanet','debianplanet','{rss id=3}');





CREATE TABLE tiki_user_notes (
"user" varchar(200) NOT NULL default '',
"noteId" integer NOT NULL default nextval('tiki_user_notes_seq') unique not null,
"created" integer NOT NULL default '0',
"name" varchar(255) NOT NULL default '',
"lastModif" integer NOT NULL default '0',
"data" text NOT NULL,
"size" integer NOT NULL default '0',
"parse_mode" varchar(20) NOT NULL default '',
PRIMARY KEY ("noteId")
) ;











CREATE TABLE tiki_user_postings (
"user" varchar(200) NOT NULL default '',
"posts" integer NOT NULL default '0',
"last" integer NOT NULL default '0',
"first" integer NOT NULL default '0',
"level" integer NOT NULL default '0',
PRIMARY KEY ("user")
) ;











CREATE TABLE tiki_user_preferences (
"user" varchar(200) NOT NULL default '',
"prefName" varchar(40) NOT NULL default '',
"value" varchar(250) NOT NULL default '',
PRIMARY KEY ("prefName","user")
) ;






INSERT INTO tiki_user_preferences VALUES ('ross','theme','subsilver.css');
INSERT INTO tiki_user_preferences VALUES ('ross','realName','Ross Smith');
INSERT INTO tiki_user_preferences VALUES ('ross','userbreadCrumb','4');
INSERT INTO tiki_user_preferences VALUES ('ross','homePage','');
INSERT INTO tiki_user_preferences VALUES ('ross','language','en');
INSERT INTO tiki_user_preferences VALUES ('display_timezone','PST','');
INSERT INTO tiki_user_preferences VALUES ('ross','display_timezone','default');
INSERT INTO tiki_user_preferences VALUES ('luis','theme','subsilver.css');
INSERT INTO tiki_user_preferences VALUES ('luis','realName','Luis');
INSERT INTO tiki_user_preferences VALUES ('luis','userbreadCrumb','4');
INSERT INTO tiki_user_preferences VALUES ('luis','homePage','');
INSERT INTO tiki_user_preferences VALUES ('luis','language','sp');
INSERT INTO tiki_user_preferences VALUES ('luis','display_timezone','AGT');
INSERT INTO tiki_user_preferences VALUES ('ross','country','United_States');
INSERT INTO tiki_user_preferences VALUES ('ross','user_information','public');





CREATE TABLE tiki_user_quizzes (
"user" varchar(100) NOT NULL default '',
"quizId" integer NOT NULL default '0',
"timestamp" integer NOT NULL default '0',
"timeTaken" integer NOT NULL default '0',
"points" integer NOT NULL default '0',
"maxPoints" integer NOT NULL default '0',
"resultId" integer NOT NULL default '0',
"userResultId" integer NOT NULL default nextval('tiki_user_quizzes_seq') unique not null,
PRIMARY KEY ("userResultId")
) ;











CREATE TABLE tiki_user_taken_quizzes (
"user" varchar(200) NOT NULL default '',
"quizId" varchar(255) NOT NULL default '',
PRIMARY KEY ("quizId","user")
) ;






INSERT INTO tiki_user_taken_quizzes VALUES ('ross','1');





CREATE TABLE tiki_user_tasks (
"user" varchar(200) NOT NULL default '',
"taskId" integer NOT NULL default nextval('tiki_user_tasks_seq') unique not null,
"title" varchar(250) NOT NULL default '',
"description" text NOT NULL,
"datetime" integer NOT NULL default '0',
"status" varchar(1) NOT NULL default '',
"priority" integer NOT NULL default '0',
"completed" integer NOT NULL default '0',
"percentage" integer NOT NULL default '0',
PRIMARY KEY ("taskId")
) ;











CREATE TABLE tiki_user_votings (
"user" varchar(200) NOT NULL default '',
"id" varchar(255) NOT NULL default '',
PRIMARY KEY ("id","user")
) ;






INSERT INTO tiki_user_votings VALUES ('ross','comment4');





CREATE TABLE tiki_user_watches (
"user" varchar(200) NOT NULL default '',
"event" varchar(40) NOT NULL default '',
"object" varchar(200) NOT NULL default '',
"hash" varchar(32) NOT NULL default '',
"title" varchar(250) NOT NULL default '',
"type" varchar(200) NOT NULL default '',
"url" varchar(250) NOT NULL default '',
"email" varchar(200) NOT NULL default '',
PRIMARY KEY ("object","event","user")
) ;











CREATE TABLE tiki_userfiles (
"user" varchar(200) NOT NULL default '',
"fileId" integer NOT NULL default nextval('tiki_userfiles_seq') unique not null,
"name" varchar(200) NOT NULL default '',
"filename" varchar(200) NOT NULL default '',
"filetype" varchar(200) NOT NULL default '',
"filesize" varchar(200) NOT NULL default '',
"data" text,
"hits" integer NOT NULL default '0',
"isFile" varchar(1) NOT NULL default '',
"path" varchar(255) NOT NULL default '',
"created" integer NOT NULL default '0',
PRIMARY KEY ("fileId")
) ;











CREATE TABLE tiki_userpoints (
"user" varchar(200) NOT NULL default '',
"points" decimal(8,2) NOT NULL default '0.00',
"voted" integer NOT NULL default '0'
) ;











CREATE TABLE tiki_users (
"user" varchar(200) NOT NULL default '',
"password" varchar(40) NOT NULL default '',
"email" varchar(200) NOT NULL default '',
"lastLogin" integer NOT NULL default '0',
PRIMARY KEY ("user")
) ;











CREATE TABLE tiki_webmail_contacts (
"contactId" integer NOT NULL default nextval('tiki_webmail_contacts_seq') unique not null,
"firstName" varchar(80) NOT NULL default '',
"lastName" varchar(80) NOT NULL default '',
"email" varchar(250) NOT NULL default '',
"nickname" varchar(200) NOT NULL default '',
"user" varchar(200) NOT NULL default '',
PRIMARY KEY ("contactId")
) ;











CREATE TABLE tiki_webmail_messages (
"accountId" integer NOT NULL default '0',
"mailId" varchar(255) NOT NULL default '',
"user" varchar(200) NOT NULL default '',
"isRead" varchar(1) NOT NULL default '',
"isReplied" varchar(1) NOT NULL default '',
"isFlagged" varchar(1) NOT NULL default '',
PRIMARY KEY ("mailId","accountId")
) ;











CREATE TABLE tiki_wiki_attachments (
"attId" integer NOT NULL default nextval('tiki_wiki_attachments_seq') unique not null,
"page" varchar(40) NOT NULL default '',
"filename" varchar(80) NOT NULL default '',
"filetype" varchar(80) NOT NULL default '',
"filesize" integer NOT NULL default '0',
"user" varchar(200) NOT NULL default '',
"data" text,
"path" varchar(255) NOT NULL default '',
"downloads" integer NOT NULL default '0',
"created" integer NOT NULL default '0',
"comment" varchar(250) NOT NULL default '',
PRIMARY KEY ("attId")
) ;











CREATE TABLE tiki_zones (
"zone" varchar(40) NOT NULL default '',
PRIMARY KEY ("zone")
) ;











CREATE TABLE users_grouppermissions (
"groupName" varchar(30) NOT NULL default '',
"permName" varchar(30) NOT NULL default '',
"value" varchar(1) NOT NULL default '',
PRIMARY KEY ("permName","groupName")
) ;






INSERT INTO users_grouppermissions VALUES ('Admins','tiki_p_admin','');
INSERT INTO users_grouppermissions VALUES ('Anonymous','tiki_p_view','');
INSERT INTO users_grouppermissions VALUES ('Anonymous','tiki_p_read_blog','');
INSERT INTO users_grouppermissions VALUES ('Anonymous','tiki_p_forum_read','');
INSERT INTO users_grouppermissions VALUES ('Anonymous','tiki_p_view_faqs','');
INSERT INTO users_grouppermissions VALUES ('Anonymous','tiki_p_vote_poll','');
INSERT INTO users_grouppermissions VALUES ('Anonymous','tiki_p_read_comments','');
INSERT INTO users_grouppermissions VALUES ('Anonymous','tiki_p_view_file_gallery','');
INSERT INTO users_grouppermissions VALUES ('Anonymous','tiki_p_view_image_gallery','');
INSERT INTO users_grouppermissions VALUES ('Anonymous','tiki_p_read_article','');





CREATE TABLE users_groups (
"groupName" varchar(30) NOT NULL default '',
"groupDesc" varchar(255) NOT NULL default '',
PRIMARY KEY ("groupName")
) ;






INSERT INTO users_groups VALUES ('Anonymous','Public users not logged');
INSERT INTO users_groups VALUES ('Registered','Users logged into the system');
INSERT INTO users_groups VALUES ('Admins','');





CREATE TABLE users_objectpermissions (
"groupName" varchar(30) NOT NULL default '',
"permName" varchar(30) NOT NULL default '',
"objectType" varchar(20) NOT NULL default '',
"objectId" varchar(32) NOT NULL default '',
PRIMARY KEY ("objectId","permName","groupName")
) ;






INSERT INTO users_objectpermissions VALUES ('Anonymous','tiki_p_read_blog','blog','4fb4afc64f1d794ae904d39a1ca5ebd1');
INSERT INTO users_objectpermissions VALUES ('Registered','tiki_p_read_blog','blog','4fb4afc64f1d794ae904d39a1ca5ebd1');





CREATE TABLE users_permissions (
"permName" varchar(30) NOT NULL default '',
"permDesc" varchar(250) NOT NULL default '',
"type" varchar(20) NOT NULL default '',
"level" varchar(80) NOT NULL default '',
PRIMARY KEY ("permName")
) ;






INSERT INTO users_permissions VALUES ('tiki_p_admin_galleries','Can admin Image Galleries','image galleries','editors');
INSERT INTO users_permissions VALUES ('tiki_p_admin_file_galleries','Can admin file galleries','file galleries','editors');
INSERT INTO users_permissions VALUES ('tiki_p_create_file_galleries','Can create file galleries','file galleries','editors');
INSERT INTO users_permissions VALUES ('tiki_p_upload_files','Can upload files','file galleries','registered');
INSERT INTO users_permissions VALUES ('tiki_p_download_files','Can download files','file galleries','basic');
INSERT INTO users_permissions VALUES ('tiki_p_post_comments','Can post new comments','comments','basic');
INSERT INTO users_permissions VALUES ('tiki_p_read_comments','Can read comments','comments','basic');
INSERT INTO users_permissions VALUES ('tiki_p_remove_comments','Can delete comments','comments','editors');
INSERT INTO users_permissions VALUES ('tiki_p_vote_comments','Can vote comments','comments','registered');
INSERT INTO users_permissions VALUES ('tiki_p_admin','Administrator, can manage users groups and permissions and all the weblog features','tiki','admin');
INSERT INTO users_permissions VALUES ('tiki_p_edit','Can edit pages','wiki','basic');
INSERT INTO users_permissions VALUES ('tiki_p_view','Can view page/pages','wiki','basic');
INSERT INTO users_permissions VALUES ('tiki_p_remove','Can remove','wiki','editors');
INSERT INTO users_permissions VALUES ('tiki_p_rollback','Can rollback pages','wiki','registered');
INSERT INTO users_permissions VALUES ('tiki_p_create_galleries','Can create image galleries','image galleries','editors');
INSERT INTO users_permissions VALUES ('tiki_p_upload_images','Can upload images','image galleries','registered');
INSERT INTO users_permissions VALUES ('tiki_p_use_HTML','Can use HTML in pages','tiki','editors');
INSERT INTO users_permissions VALUES ('tiki_p_create_blogs','Can create a blog','blogs','editors');
INSERT INTO users_permissions VALUES ('tiki_p_blog_post','Can post to a blog','blogs','registered');
INSERT INTO users_permissions VALUES ('tiki_p_blog_admin','Can admin blogs','blogs','editors');
INSERT INTO users_permissions VALUES ('tiki_p_edit_article','Can edit articles','cms','editors');
INSERT INTO users_permissions VALUES ('tiki_p_remove_article','Can remove articles','cms','editors');
INSERT INTO users_permissions VALUES ('tiki_p_read_article','Can read articles','cms','basic');
INSERT INTO users_permissions VALUES ('tiki_p_submit_article','Can submit articles','cms','basic');
INSERT INTO users_permissions VALUES ('tiki_p_edit_submission','Can edit submissions','cms','editors');
INSERT INTO users_permissions VALUES ('tiki_p_remove_submission','Can remove submissions','cms','editors');
INSERT INTO users_permissions VALUES ('tiki_p_approve_submission','Can approve submissions','cms','editors');
INSERT INTO users_permissions VALUES ('tiki_p_edit_templates','Can edit site templates','tiki','admin');
INSERT INTO users_permissions VALUES ('tiki_p_admin_dynamic','Can admin the dynamic content system','tiki','editors');
INSERT INTO users_permissions VALUES ('tiki_p_admin_banners','Administrator, can admin banners','tiki','admin');
INSERT INTO users_permissions VALUES ('tiki_p_admin_wiki','Can admin the wiki','wiki','editors');
INSERT INTO users_permissions VALUES ('tiki_p_admin_cms','Can admin the cms','cms','editors');
INSERT INTO users_permissions VALUES ('tiki_p_admin_categories','Can admin categories','tiki','editors');
INSERT INTO users_permissions VALUES ('tiki_p_send_pages','Can send pages to other sites','comm','registered');
INSERT INTO users_permissions VALUES ('tiki_p_sendme_pages','Can send pages to this site','comm','registered');
INSERT INTO users_permissions VALUES ('tiki_p_admin_received_pages','Can admin received pages','comm','editors');
INSERT INTO users_permissions VALUES ('tiki_p_admin_forum','Can admin forums','forums','editors');
INSERT INTO users_permissions VALUES ('tiki_p_forum_post','Can post in forums','forums','basic');
INSERT INTO users_permissions VALUES ('tiki_p_forum_post_topic','Can start threads in forums','forums','basic');
INSERT INTO users_permissions VALUES ('tiki_p_forum_read','Can read forums','forums','basic');
INSERT INTO users_permissions VALUES ('tiki_p_forum_vote','Can vote comments in forums','forums','registered');
INSERT INTO users_permissions VALUES ('tiki_p_read_blog','Can read blogs','blogs','basic');
INSERT INTO users_permissions VALUES ('tiki_p_view_image_gallery','Can view image galleries','image galleries','basic');
INSERT INTO users_permissions VALUES ('tiki_p_view_file_gallery','Can view file galleries','file galleries','basic');
INSERT INTO users_permissions VALUES ('tiki_p_edit_comments','Can edit all comments','comments','editors');
INSERT INTO users_permissions VALUES ('tiki_p_vote_poll','Can vote polls','tiki','basic');
INSERT INTO users_permissions VALUES ('tiki_p_admin_chat','Administrator, can create channels remove channels etc','chat','editors');
INSERT INTO users_permissions VALUES ('tiki_p_chat','Can use the chat system','chat','basic');
INSERT INTO users_permissions VALUES ('tiki_p_topic_read','Can read a topic (Applies only to individual topic perms)','topics','basic');
INSERT INTO users_permissions VALUES ('tiki_p_play_games','Can play games','games','basic');
INSERT INTO users_permissions VALUES ('tiki_p_admin_games','Can admin games','games','editors');
INSERT INTO users_permissions VALUES ('tiki_p_edit_cookies','Can admin cookies','tiki','editors');
INSERT INTO users_permissions VALUES ('tiki_p_view_stats','Can view site stats','tiki','basic');
INSERT INTO users_permissions VALUES ('tiki_p_create_bookmarks','Can create user bookmarksche user bookmarks','user','registered');
INSERT INTO users_permissions VALUES ('tiki_p_configure_modules','Can configure modules','user','registered');
INSERT INTO users_permissions VALUES ('tiki_p_cache_bookmarks','Can cache user bookmarks','user','registered');
INSERT INTO users_permissions VALUES ('tiki_p_admin_faqs','Can admin faqs','faqs','editors');
INSERT INTO users_permissions VALUES ('tiki_p_view_faqs','Can view faqs','faqs','basic');
INSERT INTO users_permissions VALUES ('tiki_p_send_articles','Can send articles to other sites','comm','editors');
INSERT INTO users_permissions VALUES ('tiki_p_sendme_articles','Can send articles to this site','comm','registered');
INSERT INTO users_permissions VALUES ('tiki_p_admin_received_articles','Can admin received articles','comm','editors');
INSERT INTO users_permissions VALUES ('tiki_p_view_shoutbox','Can view shoutbox','shoutbox','basic');
INSERT INTO users_permissions VALUES ('tiki_p_admin_shoutbox','Can admin shoutbox (Edit/remove msgs)','shoutbox','editors');
INSERT INTO users_permissions VALUES ('tiki_p_post_shoutbox','Can pot messages in shoutbox','shoutbox','basic');
INSERT INTO users_permissions VALUES ('tiki_p_suggest_faq','Can suggest faq questions','faqs','basic');
INSERT INTO users_permissions VALUES ('tiki_p_edit_content_templates','Can edit content templates','content templates','editors');
INSERT INTO users_permissions VALUES ('tiki_p_use_content_templates','Can use content templates','content templates','editors');
INSERT INTO users_permissions VALUES ('tiki_p_admin_quizzes','Can admin quizzes','quizzes','editors');
INSERT INTO users_permissions VALUES ('tiki_p_take_quiz','Can take quizzes','quizzes','basic');
INSERT INTO users_permissions VALUES ('tiki_p_view_quiz_stats','Can view quiz stats','quizzes','basic');
INSERT INTO users_permissions VALUES ('tiki_p_view_user_results','Can view user quiz results','quizzes','editors');
INSERT INTO users_permissions VALUES ('tiki_p_view_referer_stats','Can view referer stats','tiki','editors');
INSERT INTO users_permissions VALUES ('tiki_p_wiki_attach_files','Can attach files to wiki pages','wiki','basic');
INSERT INTO users_permissions VALUES ('tiki_p_wiki_admin_attachments','Can admin attachments to wiki pages','wiki','editors');
INSERT INTO users_permissions VALUES ('tiki_p_wiki_view_attachments','Can view wiki attachments and download','wiki','basic');
INSERT INTO users_permissions VALUES ('tiki_p_batch_upload_images','Can upload zip files with images','image galleries','editors');
INSERT INTO users_permissions VALUES ('tiki_p_admin_drawings','Can admin drawings','drawings','editors');
INSERT INTO users_permissions VALUES ('tiki_p_edit_drawings','Can edit drawings','drawings','basic');
INSERT INTO users_permissions VALUES ('tiki_p_view_html_pages','Can view HTML pages','html pages','basic');
INSERT INTO users_permissions VALUES ('tiki_p_edit_html_pages','Can edit HTML pages','html pages','editors');
INSERT INTO users_permissions VALUES ('tiki_p_modify_tracker_items','Can change tracker items','trackers','registered');
INSERT INTO users_permissions VALUES ('tiki_p_comment_tracker_items','Can insert comments for tracker items','trackers','basic');
INSERT INTO users_permissions VALUES ('tiki_p_create_tracker_items','Can create new items for trackers','trackers','registered');
INSERT INTO users_permissions VALUES ('tiki_p_admin_trackers','Can admin trackers','trackers','editors');
INSERT INTO users_permissions VALUES ('tiki_p_view_trackers','Can view trackers','trackers','basic');
INSERT INTO users_permissions VALUES ('tiki_p_attach_trackers','Can attach files to tracker items','trackers','registered');
INSERT INTO users_permissions VALUES ('tiki_p_use_webmail','Can use webmail','webmail','registered');
INSERT INTO users_permissions VALUES ('tiki_p_admin_surveys','Can admin surveys','surveys','editors');
INSERT INTO users_permissions VALUES ('tiki_p_take_survey','Can take surveys','surveys','basic');
INSERT INTO users_permissions VALUES ('tiki_p_view_survey_stats','Can view survey stats','surveys','basic');
INSERT INTO users_permissions VALUES ('tiki_p_admin_newsletters','Can admin newsletters','newsletters','editors');
INSERT INTO users_permissions VALUES ('tiki_p_subscribe_newsletters','Can subscribe to newsletters','newsletters','basic');
INSERT INTO users_permissions VALUES ('tiki_p_subscribe_email_newslet','Can subscribe any email to newsletters','newsletters','');
INSERT INTO users_permissions VALUES ('tiki_p_subscribe_email','Can subscribe any email to newsletters','newsletters','editors');
INSERT INTO users_permissions VALUES ('tiki_p_upload_picture','Can upload pictures to wiki pages','wiki','basic');
INSERT INTO users_permissions VALUES ('tiki_p_batch_upload_files','Can upload zip files with files','file galleries','editors');
INSERT INTO users_permissions VALUES ('tiki_p_admin_directory','Can admin the directory','directory','editors');
INSERT INTO users_permissions VALUES ('tiki_p_admin_directory_cats','Can admin directory categories','directory','editors');
INSERT INTO users_permissions VALUES ('tiki_p_admin_directory_sites','Can admin directory sites','directory','editors');
INSERT INTO users_permissions VALUES ('tiki_p_submit_link','Can submit sites to the directory','directory','basic');
INSERT INTO users_permissions VALUES ('tiki_p_autosubmit_link','Submited links are valid','directory','editors');
INSERT INTO users_permissions VALUES ('tiki_p_validate_links','Can validate submited links','directory','editors');
INSERT INTO users_permissions VALUES ('tiki_p_messages','Can use the messaging system','messu','registered');
INSERT INTO users_permissions VALUES ('tiki_p_broadcast','Can boradcast messages','messu','admin');
INSERT INTO users_permissions VALUES ('tiki_p_admin_mailin','Can admin mail-in accounts','tiki','admin');
INSERT INTO users_permissions VALUES ('tiki_p_edit_structures','Can create and edit structures','wiki','editors');
INSERT INTO users_permissions VALUES ('tiki_p_view_directory','Can use the directory','directory','basic');
INSERT INTO users_permissions VALUES ('tiki_p_minor','Can save as minor edit','wiki','editor');
INSERT INTO users_permissions VALUES ('tiki_p_rename','Can rename pages','wiki','editor');
INSERT INTO users_permissions VALUES ('tiki_p_lock','Can lock pages','wiki','editor');
INSERT INTO users_permissions VALUES ('tiki_p_usermenu','Can create items in personal menu','user','registered');
INSERT INTO users_permissions VALUES ('tiki_p_minical','Can use the mini event calendar','user','registered');
INSERT INTO users_permissions VALUES ('tiki_p_eph_admin','Can admin ephemerides','tiki','editor');
INSERT INTO users_permissions VALUES ('tiki_p_userfiles','Can upload personal files','user','registered');
INSERT INTO users_permissions VALUES ('tiki_p_tasks','Can use tasks','user','registered');
INSERT INTO users_permissions VALUES ('tiki_p_notepad','Can use the notepad','user','registered');
INSERT INTO users_permissions VALUES ('tiki_p_newsreader','Can use the newsreader','user','registered');
INSERT INTO users_permissions VALUES ('tiki_p_broadcast_all','Can broadcast messages to all user','messu','admin');
INSERT INTO users_permissions VALUES ('tiki_p_edit_languages','Can edit translations and create new languages','tiki','');
INSERT INTO users_permissions VALUES ('tiki_p_admin_workflow','Can admin workflow processes','workflow','admin');
INSERT INTO users_permissions VALUES ('tiki_p_abort_instance','Can abort a process instance','workflow','editor');
INSERT INTO users_permissions VALUES ('tiki_p_use_workflow','Can execute workflow activities','workflow','registered');
INSERT INTO users_permissions VALUES ('tiki_p_exception_instance','Can declare an instance as exception','workflow','registered');
INSERT INTO users_permissions VALUES ('tiki_p_send_instance','Can send instances after completion','workflow','registered');
INSERT INTO users_permissions VALUES ('tiki_p_admin_charts','Can admin charts','charts','admin');
INSERT INTO users_permissions VALUES ('tiki_p_view_chart','Can view charts','charts','basic');
INSERT INTO users_permissions VALUES ('tiki_p_vote_chart','Can vote','charts','basic');
INSERT INTO users_permissions VALUES ('tiki_p_suggest_chart_item','Can suggest items','charts','basic');
INSERT INTO users_permissions VALUES ('tiki_p_autoval_chart_suggestio','Autovalidate suggestions','charts','editor');
INSERT INTO users_permissions VALUES ('tiki_p_forum_autoapp','Auto approve forum posts','forums','admin');
INSERT INTO users_permissions VALUES ('tiki_p_forums_report','Can report msgs to moderator','forums','registered');
INSERT INTO users_permissions VALUES ('tiki_p_admin_banning','Can ban users or ips','tiki','admin');
INSERT INTO users_permissions VALUES ('tiki_p_forum_attach','Can attach to forum posts','forums','editor');
INSERT INTO users_permissions VALUES ('tiki_p_live_support_admin','Admin live support system','support','admin');
INSERT INTO users_permissions VALUES ('tiki_p_live_support','Can use live support system','support','basic');
INSERT INTO users_permissions VALUES ('tiki_p_autoapprove_submission','Submited articles automatically approved','cms','editors');
INSERT INTO users_permissions VALUES ('tiki_p_edit_copyrights','Can edit copyright notices','wiki','admin');
INSERT INTO users_permissions VALUES ('tiki_p_view_calendar','Can browse the calendar','calendar','basic');
INSERT INTO users_permissions VALUES ('tiki_p_change_events','Can change events in the calendar','calendar','registered');
INSERT INTO users_permissions VALUES ('tiki_p_add_events','Can add events in the calendar','calendar','registered');
INSERT INTO users_permissions VALUES ('tiki_p_admin_calendar','Can create/admin calendars','calendar','admin');
INSERT INTO users_permissions VALUES ('tiki_p_create_css','Can create new css suffixed with -user','tiki','registered');





CREATE TABLE users_usergroups (
"userId" integer NOT NULL default '0',
"groupName" varchar(30) NOT NULL default '',
PRIMARY KEY ("userId","groupName")
) ;






INSERT INTO users_usergroups VALUES (2,'Admins');
INSERT INTO users_usergroups VALUES (2,'Registered');
INSERT INTO users_usergroups VALUES (4,'Admins');
INSERT INTO users_usergroups VALUES (4,'Registered');





CREATE TABLE users_users (
"userId" integer NOT NULL default nextval('users_users_seq') unique not null,
"email" varchar(200) NOT NULL default '',
"login" varchar(40) NOT NULL default '',
"password" varchar(30) NOT NULL default '',
"provpass" varchar(30) NOT NULL default '',
"realname" varchar(80) NOT NULL default '',
"homePage" varchar(200) NOT NULL default '',
"lastLogin" integer NOT NULL default '0',
"country" varchar(80) NOT NULL default '',
"currentLogin" integer NOT NULL default '0',
"registrationDate" integer NOT NULL default '0',
"challenge" varchar(32) NOT NULL default '',
"hash" varchar(32) NOT NULL default '',
"pass_due" integer NOT NULL default '0',
"created" integer NOT NULL default '0',
"avatarName" varchar(80) NOT NULL default '',
"avatarSize" integer NOT NULL default '0',
"avatarFileType" varchar(250) NOT NULL default '',
"avatarData" text,
"avatarLibName" varchar(200) NOT NULL default '',
"avatarType" varchar(1) NOT NULL default '',
PRIMARY KEY ("userId")
) ;






INSERT INTO users_users VALUES (2,'ross@netebb.com','ross','','','','',1058293153,'',1058293153,1044244077,'','6cea1d4154470344ce947c444b291dcc',1131293732,0,'',0,'',NULL,'','n');
INSERT INTO users_users VALUES (4,'lrargerich@fibertel.com.ar','luis','tikirocks!','','','',1041570849,'',1041570849,1041570849,'','0fbcdc61c6c7e19cb3a4fc3500f03b65',0,0,'',0,'',NULL,'','n');
INSERT INTO users_users VALUES (5,'','admin','','','System Administrator','',1044892899,'',1044892911,0,'','d41d8cd98f00b204e9800998ecf8427e',1131294299,0,'',0,'',NULL,'','n');


-- Create Indexes

CREATE INDEX tiki_articles_body ON tiki_articles ("body");
CREATE INDEX tiki_articles_heading ON tiki_articles ("heading");
CREATE INDEX tiki_articles_reads ON tiki_articles ("reads");
CREATE INDEX tiki_articles_title ON tiki_articles ("title");
CREATE INDEX tiki_blog_posts_blogId ON tiki_blog_posts ("blogId");
CREATE INDEX tiki_blog_posts_created ON tiki_blog_posts ("created");
CREATE INDEX tiki_blog_posts_data ON tiki_blog_posts ("data");
CREATE INDEX tiki_blogs_description ON tiki_blogs ("description");
CREATE INDEX tiki_blogs_hits ON tiki_blogs ("hits");
CREATE INDEX tiki_blogs_title ON tiki_blogs ("title");
CREATE INDEX tiki_calendar_categories_calendarId ON tiki_calendar_categories ("calendarId");
CREATE INDEX tiki_calendar_categories_name ON tiki_calendar_categories ("name");
CREATE INDEX tiki_calendar_items_calendarId ON tiki_calendar_items ("calendarId");
CREATE INDEX tiki_calendar_locations_calendarId ON tiki_calendar_locations ("calendarId");
CREATE INDEX tiki_calendar_locations_name ON tiki_calendar_locations ("name");
CREATE INDEX tiki_comments_data ON tiki_comments ("data");
CREATE INDEX tiki_comments_hits ON tiki_comments ("hits");
CREATE INDEX tiki_comments_object ON tiki_comments ("object");
CREATE INDEX tiki_comments_title ON tiki_comments ("title");
CREATE INDEX tiki_directory_sites_description ON tiki_directory_sites ("description");
CREATE INDEX tiki_directory_sites_name ON tiki_directory_sites ("name");
CREATE INDEX tiki_faq_questions_answer ON tiki_faq_questions ("answer");
CREATE INDEX tiki_faq_questions_faqId ON tiki_faq_questions ("faqId");
CREATE INDEX tiki_faq_questions_question ON tiki_faq_questions ("question");
CREATE INDEX tiki_faqs_description ON tiki_faqs ("description");
CREATE INDEX tiki_faqs_hits ON tiki_faqs ("hits");
CREATE INDEX tiki_faqs_title ON tiki_faqs ("title");
CREATE INDEX tiki_files_description ON tiki_files ("description");
CREATE INDEX tiki_files_downloads ON tiki_files ("downloads");
CREATE INDEX tiki_files_name ON tiki_files ("name");
CREATE INDEX tiki_galleries_description ON tiki_galleries ("description");
CREATE INDEX tiki_galleries_hits ON tiki_galleries ("hits");
CREATE INDEX tiki_galleries_name ON tiki_galleries ("name");
CREATE INDEX tiki_images_created ON tiki_images ("created");
CREATE INDEX tiki_images_description ON tiki_images ("description");
CREATE INDEX tiki_images_galleryId ON tiki_images ("galleryId");
CREATE INDEX tiki_images_hits ON tiki_images ("hits");
CREATE INDEX tiki_images_name ON tiki_images ("name");
CREATE INDEX tiki_images_user ON tiki_images ("user");
CREATE INDEX tiki_images_data_imageId ON tiki_images_data ("imageId");
CREATE INDEX tiki_images_data_type ON tiki_images_data ("type");
CREATE INDEX tiki_images_old_description ON tiki_images_old ("description");
CREATE INDEX tiki_images_old_hits ON tiki_images_old ("hits");
CREATE INDEX tiki_images_old_name ON tiki_images_old ("name");
CREATE INDEX tiki_pages_data ON tiki_pages ("data");
CREATE INDEX tiki_pages_pageName ON tiki_pages ("pageName");
CREATE INDEX tiki_pages_pageRank ON tiki_pages ("pageRank");
CREATE INDEX tiki_untranslated_id ON tiki_untranslated ("id");

-- Create Sequences

CREATE SEQUENCE galaxia_activities_seq;
CREATE SEQUENCE galaxia_instance_comments_seq;
CREATE SEQUENCE galaxia_instances_seq;
CREATE SEQUENCE galaxia_processes_seq;
CREATE SEQUENCE galaxia_roles_seq;
CREATE SEQUENCE galaxia_user_roles_seq;
CREATE SEQUENCE galaxia_workitems_seq;
CREATE SEQUENCE messu_messages_seq;
CREATE SEQUENCE tiki_articles_seq;
CREATE SEQUENCE tiki_banners_seq;
CREATE SEQUENCE tiki_banning_seq;
CREATE SEQUENCE tiki_blog_posts_seq;
CREATE SEQUENCE tiki_blog_posts_images_seq;
CREATE SEQUENCE tiki_blogs_seq;
CREATE SEQUENCE tiki_calendar_categories_seq;
CREATE SEQUENCE tiki_calendar_items_seq;
CREATE SEQUENCE tiki_calendar_locations_seq;
CREATE SEQUENCE tiki_calendars_seq;
CREATE SEQUENCE tiki_categories_seq;
CREATE SEQUENCE tiki_categorized_objects_seq;
CREATE SEQUENCE tiki_chart_items_seq;
CREATE SEQUENCE tiki_charts_seq;
CREATE SEQUENCE tiki_chat_channels_seq;
CREATE SEQUENCE tiki_chat_messages_seq;
CREATE SEQUENCE tiki_comments_seq;
CREATE SEQUENCE tiki_content_seq;
CREATE SEQUENCE tiki_content_templates_seq;
CREATE SEQUENCE tiki_cookies_seq;
CREATE SEQUENCE tiki_copyrights_seq;
CREATE SEQUENCE tiki_directory_categories_seq;
CREATE SEQUENCE tiki_directory_sites_seq;
CREATE SEQUENCE tiki_drawings_seq;
CREATE SEQUENCE tiki_dsn_seq;
CREATE SEQUENCE tiki_eph_seq;
CREATE SEQUENCE tiki_extwiki_seq;
CREATE SEQUENCE tiki_faq_questions_seq;
CREATE SEQUENCE tiki_faqs_seq;
CREATE SEQUENCE tiki_file_galleries_seq;
CREATE SEQUENCE tiki_files_seq;
CREATE SEQUENCE tiki_forum_attachments_seq;
CREATE SEQUENCE tiki_forums_seq;
CREATE SEQUENCE tiki_forums_queue_seq;
CREATE SEQUENCE tiki_galleries_seq;
CREATE SEQUENCE tiki_images_seq;
CREATE SEQUENCE tiki_images_old_seq;
CREATE SEQUENCE tiki_link_cache_seq;
CREATE SEQUENCE tiki_live_support_events_seq;
CREATE SEQUENCE tiki_live_support_message_c_seq;
CREATE SEQUENCE tiki_live_support_messages_seq;
CREATE SEQUENCE tiki_live_support_modules_seq;
CREATE SEQUENCE tiki_mailin_accounts_seq;
CREATE SEQUENCE tiki_menu_languages_seq;
CREATE SEQUENCE tiki_menu_options_seq;
CREATE SEQUENCE tiki_menus_seq;
CREATE SEQUENCE tiki_minical_events_seq;
CREATE SEQUENCE tiki_minical_topics_seq;
CREATE SEQUENCE tiki_newsletters_seq;
CREATE SEQUENCE tiki_newsreader_servers_seq;
CREATE SEQUENCE tiki_poll_options_seq;
CREATE SEQUENCE tiki_polls_seq;
CREATE SEQUENCE tiki_private_messages_seq;
CREATE SEQUENCE tiki_programmed_content_seq;
CREATE SEQUENCE tiki_quiz_question_options_seq;
CREATE SEQUENCE tiki_quiz_questions_seq;
CREATE SEQUENCE tiki_quiz_results_seq;
CREATE SEQUENCE tiki_quizzes_seq;
CREATE SEQUENCE tiki_received_articles_seq;
CREATE SEQUENCE tiki_received_pages_seq;
CREATE SEQUENCE tiki_rss_modules_seq;
CREATE SEQUENCE tiki_sent_newsletters_seq;
CREATE SEQUENCE tiki_shoutbox_seq;
CREATE SEQUENCE tiki_submissions_seq;
CREATE SEQUENCE tiki_suggested_faq_question_seq;
CREATE SEQUENCE tiki_survey_question_option_seq;
CREATE SEQUENCE tiki_survey_questions_seq;
CREATE SEQUENCE tiki_surveys_seq;
CREATE SEQUENCE tiki_topics_seq;
CREATE SEQUENCE tiki_tracker_fields_seq;
CREATE SEQUENCE tiki_tracker_item_attachmen_seq;
CREATE SEQUENCE tiki_tracker_item_comments_seq;
CREATE SEQUENCE tiki_tracker_items_seq;
CREATE SEQUENCE tiki_trackers_seq;
CREATE SEQUENCE tiki_untranslated_seq;
CREATE SEQUENCE tiki_user_bookmarks_folders_seq;
CREATE SEQUENCE tiki_user_bookmarks_urls_seq;
CREATE SEQUENCE tiki_user_mail_accounts_seq;
CREATE SEQUENCE tiki_user_menus_seq;
CREATE SEQUENCE tiki_user_notes_seq;
CREATE SEQUENCE tiki_user_quizzes_seq;
CREATE SEQUENCE tiki_user_tasks_seq;
CREATE SEQUENCE tiki_userfiles_seq;
CREATE SEQUENCE tiki_webmail_contacts_seq;
CREATE SEQUENCE tiki_wiki_attachments_seq;
CREATE SEQUENCE users_users_seq;

-- Populate Sequences

SELECT SETVAL('galaxia_activities_seq', (SELECT MAX("activityId") FROM galaxia_activities));
SELECT SETVAL('galaxia_instance_comments_seq', (SELECT MAX("cId") FROM galaxia_instance_comments));
SELECT SETVAL('galaxia_instances_seq', (SELECT MAX("instanceId") FROM galaxia_instances));
SELECT SETVAL('galaxia_processes_seq', (SELECT MAX("pId") FROM galaxia_processes));
SELECT SETVAL('galaxia_roles_seq', (SELECT MAX("roleId") FROM galaxia_roles));
SELECT SETVAL('galaxia_user_roles_seq', (SELECT MAX("roleId") FROM galaxia_user_roles));
SELECT SETVAL('galaxia_workitems_seq', (SELECT MAX("itemId") FROM galaxia_workitems));
SELECT SETVAL('messu_messages_seq', (SELECT MAX("msgId") FROM messu_messages));
SELECT SETVAL('tiki_articles_seq', (SELECT MAX("articleId") FROM tiki_articles));
SELECT SETVAL('tiki_banners_seq', (SELECT MAX("bannerId") FROM tiki_banners));
SELECT SETVAL('tiki_banning_seq', (SELECT MAX("banId") FROM tiki_banning));
SELECT SETVAL('tiki_blog_posts_seq', (SELECT MAX("postId") FROM tiki_blog_posts));
SELECT SETVAL('tiki_blog_posts_images_seq', (SELECT MAX("imgId") FROM tiki_blog_posts_images));
SELECT SETVAL('tiki_blogs_seq', (SELECT MAX("blogId") FROM tiki_blogs));
SELECT SETVAL('tiki_calendar_categories_seq', (SELECT MAX("calcatId") FROM tiki_calendar_categories));
SELECT SETVAL('tiki_calendar_items_seq', (SELECT MAX("calitemId") FROM tiki_calendar_items));
SELECT SETVAL('tiki_calendar_locations_seq', (SELECT MAX("callocId") FROM tiki_calendar_locations));
SELECT SETVAL('tiki_calendars_seq', (SELECT MAX("calendarId") FROM tiki_calendars));
SELECT SETVAL('tiki_categories_seq', (SELECT MAX("categId") FROM tiki_categories));
SELECT SETVAL('tiki_categorized_objects_seq', (SELECT MAX("catObjectId") FROM tiki_categorized_objects));
SELECT SETVAL('tiki_chart_items_seq', (SELECT MAX("itemId") FROM tiki_chart_items));
SELECT SETVAL('tiki_charts_seq', (SELECT MAX("chartId") FROM tiki_charts));
SELECT SETVAL('tiki_chat_channels_seq', (SELECT MAX("channelId") FROM tiki_chat_channels));
SELECT SETVAL('tiki_chat_messages_seq', (SELECT MAX("messageId") FROM tiki_chat_messages));
SELECT SETVAL('tiki_comments_seq', (SELECT MAX("threadId") FROM tiki_comments));
SELECT SETVAL('tiki_content_seq', (SELECT MAX("contentId") FROM tiki_content));
SELECT SETVAL('tiki_content_templates_seq', (SELECT MAX("templateId") FROM tiki_content_templates));
SELECT SETVAL('tiki_cookies_seq', (SELECT MAX("cookieId") FROM tiki_cookies));
SELECT SETVAL('tiki_copyrights_seq', (SELECT MAX("copyrightId") FROM tiki_copyrights));
SELECT SETVAL('tiki_directory_categories_seq', (SELECT MAX("categId") FROM tiki_directory_categories));
SELECT SETVAL('tiki_directory_sites_seq', (SELECT MAX("siteId") FROM tiki_directory_sites));
SELECT SETVAL('tiki_drawings_seq', (SELECT MAX("drawId") FROM tiki_drawings));
SELECT SETVAL('tiki_dsn_seq', (SELECT MAX("dsnId") FROM tiki_dsn));
SELECT SETVAL('tiki_eph_seq', (SELECT MAX("ephId") FROM tiki_eph));
SELECT SETVAL('tiki_extwiki_seq', (SELECT MAX("extwikiId") FROM tiki_extwiki));
SELECT SETVAL('tiki_faq_questions_seq', (SELECT MAX("questionId") FROM tiki_faq_questions));
SELECT SETVAL('tiki_faqs_seq', (SELECT MAX("faqId") FROM tiki_faqs));
SELECT SETVAL('tiki_file_galleries_seq', (SELECT MAX("galleryId") FROM tiki_file_galleries));
SELECT SETVAL('tiki_files_seq', (SELECT MAX("fileId") FROM tiki_files));
SELECT SETVAL('tiki_forum_attachments_seq', (SELECT MAX("attId") FROM tiki_forum_attachments));
SELECT SETVAL('tiki_forums_seq', (SELECT MAX("forumId") FROM tiki_forums));
SELECT SETVAL('tiki_forums_queue_seq', (SELECT MAX("qId") FROM tiki_forums_queue));
SELECT SETVAL('tiki_galleries_seq', (SELECT MAX("galleryId") FROM tiki_galleries));
SELECT SETVAL('tiki_images_seq', (SELECT MAX("imageId") FROM tiki_images));
SELECT SETVAL('tiki_images_old_seq', (SELECT MAX("imageId") FROM tiki_images_old));
SELECT SETVAL('tiki_link_cache_seq', (SELECT MAX("cacheId") FROM tiki_link_cache));
SELECT SETVAL('tiki_live_support_events_seq', (SELECT MAX("eventId") FROM tiki_live_support_events));
SELECT SETVAL('tiki_live_support_message_c_seq', (SELECT MAX("cId") FROM tiki_live_support_message_comments));
SELECT SETVAL('tiki_live_support_messages_seq', (SELECT MAX("msgId") FROM tiki_live_support_messages));
SELECT SETVAL('tiki_live_support_modules_seq', (SELECT MAX("modId") FROM tiki_live_support_modules));
SELECT SETVAL('tiki_mailin_accounts_seq', (SELECT MAX("accountId") FROM tiki_mailin_accounts));
SELECT SETVAL('tiki_menu_languages_seq', (SELECT MAX("menuId") FROM tiki_menu_languages));
SELECT SETVAL('tiki_menu_options_seq', (SELECT MAX("optionId") FROM tiki_menu_options));
SELECT SETVAL('tiki_menus_seq', (SELECT MAX("menuId") FROM tiki_menus));
SELECT SETVAL('tiki_minical_events_seq', (SELECT MAX("eventId") FROM tiki_minical_events));
SELECT SETVAL('tiki_minical_topics_seq', (SELECT MAX("topicId") FROM tiki_minical_topics));
SELECT SETVAL('tiki_newsletters_seq', (SELECT MAX("nlId") FROM tiki_newsletters));
SELECT SETVAL('tiki_newsreader_servers_seq', (SELECT MAX("serverId") FROM tiki_newsreader_servers));
SELECT SETVAL('tiki_poll_options_seq', (SELECT MAX("optionId") FROM tiki_poll_options));
SELECT SETVAL('tiki_polls_seq', (SELECT MAX("pollId") FROM tiki_polls));
SELECT SETVAL('tiki_private_messages_seq', (SELECT MAX("messageId") FROM tiki_private_messages));
SELECT SETVAL('tiki_programmed_content_seq', (SELECT MAX("pId") FROM tiki_programmed_content));
SELECT SETVAL('tiki_quiz_question_options_seq', (SELECT MAX("optionId") FROM tiki_quiz_question_options));
SELECT SETVAL('tiki_quiz_questions_seq', (SELECT MAX("questionId") FROM tiki_quiz_questions));
SELECT SETVAL('tiki_quiz_results_seq', (SELECT MAX("resultId") FROM tiki_quiz_results));
SELECT SETVAL('tiki_quizzes_seq', (SELECT MAX("quizId") FROM tiki_quizzes));
SELECT SETVAL('tiki_received_articles_seq', (SELECT MAX("receivedArticleId") FROM tiki_received_articles));
SELECT SETVAL('tiki_received_pages_seq', (SELECT MAX("receivedPageId") FROM tiki_received_pages));
SELECT SETVAL('tiki_rss_modules_seq', (SELECT MAX("rssId") FROM tiki_rss_modules));
SELECT SETVAL('tiki_sent_newsletters_seq', (SELECT MAX("editionId") FROM tiki_sent_newsletters));
SELECT SETVAL('tiki_shoutbox_seq', (SELECT MAX("msgId") FROM tiki_shoutbox));
SELECT SETVAL('tiki_submissions_seq', (SELECT MAX("subId") FROM tiki_submissions));
SELECT SETVAL('tiki_suggested_faq_question_seq', (SELECT MAX("sfqId") FROM tiki_suggested_faq_questions));
SELECT SETVAL('tiki_survey_question_option_seq', (SELECT MAX("optionId") FROM tiki_survey_question_options));
SELECT SETVAL('tiki_survey_questions_seq', (SELECT MAX("questionId") FROM tiki_survey_questions));
SELECT SETVAL('tiki_surveys_seq', (SELECT MAX("surveyId") FROM tiki_surveys));
SELECT SETVAL('tiki_topics_seq', (SELECT MAX("topicId") FROM tiki_topics));
SELECT SETVAL('tiki_tracker_fields_seq', (SELECT MAX("fieldId") FROM tiki_tracker_fields));
SELECT SETVAL('tiki_tracker_item_attachmen_seq', (SELECT MAX("attId") FROM tiki_tracker_item_attachments));
SELECT SETVAL('tiki_tracker_item_comments_seq', (SELECT MAX("commentId") FROM tiki_tracker_item_comments));
SELECT SETVAL('tiki_tracker_items_seq', (SELECT MAX("itemId") FROM tiki_tracker_items));
SELECT SETVAL('tiki_trackers_seq', (SELECT MAX("trackerId") FROM tiki_trackers));
SELECT SETVAL('tiki_untranslated_seq', (SELECT MAX("id") FROM tiki_untranslated));
SELECT SETVAL('tiki_user_bookmarks_folders_seq', (SELECT MAX("folderId") FROM tiki_user_bookmarks_folders));
SELECT SETVAL('tiki_user_bookmarks_urls_seq', (SELECT MAX("urlId") FROM tiki_user_bookmarks_urls));
SELECT SETVAL('tiki_user_mail_accounts_seq', (SELECT MAX("accountId") FROM tiki_user_mail_accounts));
SELECT SETVAL('tiki_user_menus_seq', (SELECT MAX("menuId") FROM tiki_user_menus));
SELECT SETVAL('tiki_user_notes_seq', (SELECT MAX("noteId") FROM tiki_user_notes));
SELECT SETVAL('tiki_user_quizzes_seq', (SELECT MAX("userResultId") FROM tiki_user_quizzes));
SELECT SETVAL('tiki_user_tasks_seq', (SELECT MAX("taskId") FROM tiki_user_tasks));
SELECT SETVAL('tiki_userfiles_seq', (SELECT MAX("fileId") FROM tiki_userfiles));
SELECT SETVAL('tiki_webmail_contacts_seq', (SELECT MAX("contactId") FROM tiki_webmail_contacts));
SELECT SETVAL('tiki_wiki_attachments_seq', (SELECT MAX("attId") FROM tiki_wiki_attachments));
SELECT SETVAL('users_users_seq', (SELECT MAX("userId") FROM users_users));

-- EOF

