## THIS FILE IS JUST A HELP FOR DEVELOPERS IT SHOULDNT BE USED IN A 1.5 DISTRIBUTION

alter table users_permissions add level varchar(80);

INSERT INTO users_permissions(permName,type,permDesc,level) VALUES ('tiki_p_minor','wiki','Can save as minor edit','editor');
INSERT INTO users_permissions(permName,type,permDesc,level) VALUES ('tiki_p_rename','wiki','Can rename pages','editor');
INSERT INTO users_permissions(permName,type,permDesc,level) VALUES ('tiki_p_lock','wiki','Can lock pages','editor');

drop table if exists tiki_extwiki;
create table tiki_extwiki(
  extwikiId integer(12) not null auto_increment,
  name varchar(20) not null,
  extwiki varchar(255),
  primary key(extwikiId)
);


drop table if exists tiki_dsn;
create table tiki_dsn(
  dsnId integer(12) not null auto_increment,
  name varchar(20) not null,
  dsn varchar(255),
  primary key(dsnId)
);

alter table tiki_semaphores add user varchar(200);

drop table if exists tiki_minical_topics;
create table tiki_minical_topics(
  user varchar(200),
  topicId integer(12) not null auto_increment,
  name varchar(250),
  filename varchar(200),
  filetype varchar(200),
  filesize varchar(200),
  data longblob,  
  path varchar(250),
  isIcon char(1),
  primary key(topicId)
);

drop table if exists tiki_minical_events;
create table tiki_minical_events(
  user varchar(200),
  eventId integer(12) not null auto_increment,
  title varchar(250),
  description text,
  start integer(14),
  end integer(14),
  security char(1),
  duration integer(3),
  topicId integer(12),
  reminded char(1),
  primary key(eventId)
);


INSERT INTO users_permissions(permName,type,permDesc,level) VALUES ('tiki_p_usermenu','user','Can create items in personal menu','registered');
INSERT INTO users_permissions(permName,type,permDesc,level) VALUES ('tiki_p_minical','user','Can use the mini event calendar','registered');

drop table if exists tiki_user_menus;
create table tiki_user_menus(
  user varchar(200) not null,
  menuId integer(12) not null auto_increment,
  url varchar(250),
  name varchar(40),
  position integer(4),
  mode char(1),
  primary key(menuId)
);

drop table if exists tiki_theme_control_sections;
create table tiki_theme_control_sections(
  section varchar(250) not null,
  theme varchar(250) not null,
  primary key(section)
);

drop table if exists tiki_theme_control_objects;
create table tiki_theme_control_objects(
  objId varchar(250) not null,
  type varchar(250) not null,
  name varchar(250) not null,
  theme varchar(250) not null,
  primary key(objId)
);


drop table if exists tiki_theme_control_categs;
create table tiki_theme_control_categs(
  categId integer(12) not null,
  theme varchar(250) not null,
  primary key(categId)
);

drop table if exists tiki_eph;
create table tiki_eph(
  ephId integer(12) not null auto_increment,
  title varchar(250),
  isFile char(1),
  filename varchar(250),
  filetype varchar(250),
  filesize varchar(250),
  data longblob,
  textdata longblob,
  publish integer(14),
  hits integer(10),
  primary key(ephId)
);

INSERT INTO users_permissions(permName,type,permDesc,level) VALUES ('tiki_p_eph_admin','tiki','Can admin ephemerides','editor');




INSERT INTO users_permissions(permName,type,permDesc,level) VALUES ('tiki_p_userfiles','user','Can upload personal files','registered');
INSERT INTO users_permissions(permName,type,permDesc,level) VALUES ('tiki_p_tasks','user','Can use tasks','registered');
INSERT INTO users_permissions(permName,type,permDesc,level) VALUES ('tiki_p_notepad','user','Can use the notepad','registered');
INSERT INTO users_permissions(permName,type,permDesc,level) VALUES ('tiki_p_newsreader','user','Can use the newsreader','registered');

drop table if exists tiki_userfiles;
create table tiki_userfiles(
  user varchar(200) not null,
  fileId integer(12) not null auto_increment,
  name varchar(200),
  filename varchar(200),
  filetype varchar(200),
  filesize varchar(200),
  data longblob,
  hits integer(8),
  isFile char(1),
  path varchar(255),
  created integer(14),
  primary key(fileId)
);

drop table if exists tiki_user_notes;
create table tiki_user_notes(
  user varchar(200) not null,
  noteId integer(12) not null auto_increment,
  created integer(14),
  name varchar(255),
  lastModif integer(14),
  data text,
  size integer(14),
  primary key(noteId)
);

alter table tiki_categorized_objects modify objId varchar(255);

drop table if exists tiki_newsreader_marks;
create table tiki_newsreader_marks (
  user varchar(200) not null,
  serverId integer(12) not null,
  groupName varchar(255) not null,
  timestamp integer(14) not null,
  primary key(user,serverId,groupName)
);

drop table if exists tiki_newsreader_servers;
create table tiki_newsreader_servers(
  user varchar(200) not null,
  serverId integer(12) not null auto_increment,
  server varchar(250),
  port integer(4),
  username varchar(200),
  password varchar(200),
  primary key(serverId)
);


## Wiki footnotes
drop table if exists tiki_page_footnotes;
create table tiki_page_footnotes(
  user varchar(200) not null,
  pageName varchar(250) not null,
  data text,
  primary key(user,pageName)
);

### User-tasks
drop table if exists tiki_user_tasks;
create table tiki_user_tasks(
  user varchar(200),
  taskId integer(14) not null auto_increment,
  title varchar(250),
  description text,
  date integer(14),
  status char(1),
  priority integer(2),
  completed integer(14),
  percentage integer(4),
  primary key(taskId)
);



### Inter-user messages

INSERT INTO users_permissions(permName,type,permDesc,level) VALUES ('tiki_p_messages','messu','Can use the messaging system','registered');
INSERT INTO users_permissions(permName,type,permDesc,level) VALUES ('tiki_p_broadcast','messu','Can broadcast messages to groups','editor');
INSERT INTO users_permissions(permName,type,permDesc,level) VALUES ('tiki_p_broadcast_all','messu','Can broadcast messages to all user','admin');

drop table if exists messu_messages;
create table messu_messages(
  msgId integer(14) not null auto_increment,
  user varchar(200) not null,
  user_from varchar(200) not null,
  user_to text,
  user_cc text,
  user_bcc text,
  subject varchar(255),
  body text,
  hash char(32),
  date integer(14),
  isRead char(1),
  isReplied char(1),
  isFlagged char(1),
  priority integer(2),
  primary key(msgId)
);


alter table tiki_sessions add user varchar(200);

INSERT INTO users_permissions(permName,type,permDesc) VALUES ('tiki_p_admin_mailin','tiki','Can admin mail-in accounts');

### Mailin
drop table if exists tiki_mailin_accounts;
create table tiki_mailin_accounts (
  accountId integer(12) not null auto_increment,
  user varchar(200) not null,
  account varchar(50) not null,
  pop varchar(255),
  port integer(4),
  username varchar(100),
  pass varchar(100),
  active char(1),
  type varchar(40),
  smtp varchar(255),
  useAuth char(1),
  smtpPort integer(4),
  primary key(accountId)
);


### Tiki structures permissions
INSERT INTO users_permissions(permName,type,permDesc) VALUES ('tiki_p_edit_structures','wiki','Can create and edit structures');

### Cache for wiki pages
alter table tiki_pages add cache longblob;
alter table tiki_pages add cache_timestamp integer(14);





### DIRECTORIES BEGIN

INSERT INTO users_permissions(permName,type,permDesc,level) VALUES ('tiki_p_admin_directory','directory','Can admin the directory','editor');
INSERT INTO users_permissions(permName,type,permDesc,level) VALUES ('tiki_p_view_directory','directory','Can use the directory','basic');
INSERT INTO users_permissions(permName,type,permDesc,level) VALUES ('tiki_p_admin_directory_cats','directory','Can admin directory categories','editor');
REPLACE INTO users_permissions(permName,type,permDesc,level) VALUES ('tiki_p_admin_directory_sites','directory','Can admin directory sites','editor');
INSERT INTO users_permissions(permName,type,permDesc,level) VALUES ('tiki_p_submit_link','directory','Can submit sites to the directory','basic');
INSERT INTO users_permissions(permName,type,permDesc,level) VALUES ('tiki_p_autosubmit_link','directory','Submited links are valid','editor');
INSERT INTO users_permissions(permName,type,permDesc,level) VALUES ('tiki_p_validate_links','directory','Can validate submited links','editor');




drop table if exists tiki_directory_categories;
create table tiki_directory_categories(
  categId integer(10) not null auto_increment,
  parent integer(10),
  name varchar(240),
  description text,
  childrenType char(1),
  sites integer(10),
  viewableChildren integer(4),
  allowSites char(1),
  showCount char(1),
  editorGroup varchar(200),
  hits integer(12),
  primary key(categId)
);

drop table if exists tiki_directory_sites;
create table tiki_directory_sites(
  siteId integer(14) not null auto_increment,
  name varchar(240),
  description text,
  url varchar(255),
  country varchar(255),
  hits integer(12),
  isValid char(1),
  created integer(14),
  lastModif integer(14), 
  cache longblob,
  cache_timestamp integer(14),
  primary key(siteId)
);

drop table if exists tiki_category_sites;
create table tiki_category_sites(
  categId integer(10) not null,
  siteId integer(14) not null,
  primary key(categId,siteId)
);

drop table if exists tiki_related_categories;
create table tiki_related_categories(
  categId integer(10) not null,
  relatedTo integer(10) not null,
  primary key(categId,relatedTo)
);

drop table if exists tiki_directory_search;
create table tiki_directory_search(
  term varchar(250) not null,
  hits integer(14),
  primary key(term)
);

### Try to rebuild tiki_images without loss

RENAME TABLE tiki_images TO tiki_images_old;

#DROP TABLE IF EXISTS tiki_images;
# for now we drop nothing

CREATE TABLE tiki_images (
  imageId integer(14) not null auto_increment,
  galleryId integer(14) not null,
  name varchar(40) not null,
  description text,
  created integer(14),
  user varchar(200),
  hits integer(14),
  path varchar(255),
  primary key(imageId)
);

DROP TABLE IF EXISTS tiki_images_data;
CREATE TABLE tiki_images_data (
  imageId integer(14) not null,
  xsize integer(8) not null,
  ysize integer(8) not null,
  type char(1) not null, 
  filesize integer(14),
  filetype varchar(80),
  filename varchar(80),
  data longblob,
  primary key(imageId,xsize,ysize,type)
);

# insert image information
insert into tiki_images (imageId,
		galleryId,name,description,
		created,
		user,hits,path)
	select imageId,
                galleryId,name,description,
                created,
                user,hits,path
	from tiki_images_old;

# insert original images
insert into tiki_images_data (imageId,
		xsize,ysize,type,filesize,
		filename,filetype,data)
	select imageId,
                xsize,ysize,'o',filesize,
                filename,filetype,data
	from tiki_images_old;

# insert thumbnails x and y size, filesize are not correct
# best is, if user recreates the thumbs.
insert into tiki_images_data (imageId,
                xsize,ysize,type,filesize,
                filename,filetype,data)
        select imageId,
                xsize,ysize,'t',filesize,
                filename,t_type,t_data
        from tiki_images_old;

# Information for the scales
drop table if exists tiki_galleries_scales;
create table tiki_galleries_scales (
	      galleryId int(14) not null,
              xsize integer not null, 
	      ysize integer not null, 
	      primary key (galleryId,xsize,ysize));

# Optimaziation
create index ti_gId on tiki_images (galleryId);
create index ti_cr on tiki_images (created);
create index ti_hi on tiki_images (hits);
create index ti_us on tiki_images (user);
create index t_i_d_it on tiki_images_data (imageId,type);
create index tc_pi on tiki_comments(parentId);

# Optimization of others -- try yourself
#create index tg_u on tiki_galleries (user);
#create index ti_p_hi on tiki_pages (hits);
#create index ti_p_lm on tiki_pages (lastModif);
#create index up_t on users_permissions(type);
#create index tf_n on tiki_forums(name);
#create index tf_lp on tiki_forums(lastPost);
#create index tf_h on tiki_forums(hits);
#create index ts_t on tiki_shoutbox(timestamp);
#create index ta_pd on tiki_articles(publishDate);






### DIRECTORIES END

### FULLTEXT SEARCH BEGIN

alter table tiki_pages modify data text;
create fulltext index ft on tiki_pages (pagename,data);
create fulltext index ft on tiki_galleries (name,description);
create fulltext index ft on tiki_faqs (title,description);
create fulltext index ft on tiki_faq_questions (question,answer);
create fulltext index ft on tiki_images (name,description);
create fulltext index ft on tiki_comments (title,data);
create fulltext index ft on tiki_files (name,description);
create fulltext index ft on tiki_blogs (title,description);
alter table tiki_articles modify body text;
create fulltext index ft on tiki_articles (title,heading,body);
create fulltext index ft on tiki_blog_posts (data);

### FULLTEXT SEARCH END


## LEVELS
## 1-anonymous users
## 2-registered users
## 3-editors&friends
## 4-admins
UPDATE users_permissions set level='editors' where permName='tiki_p_edit_structures ';
UPDATE users_permissions set level='registered' where permName='tiki_p_messages';
UPDATE users_permissions set level='admin' where permName='tiki_p_broadcast';
UPDATE users_permissions set level='admin' where permName='tiki_p_admin_mailin';
UPDATE users_permissions set level='editors' where permName='tiki_p_admin_directory';
UPDATE users_permissions set level='basic' where permName='tiki_p_view_directory';
UPDATE users_permissions set level='editors' where permName='tiki_p_admin_directory_cats';
UPDATE users_permissions set level='editors' where permName='tiki_p_admin_directory_sites';
UPDATE users_permissions set level='basic' where permName='tiki_p_submit_link';
UPDATE users_permissions set level='editors' where permName='tiki_p_autosubmit_link';
UPDATE users_permissions set level='editors' where permName='tiki_p_validate_links';
UPDATE users_permissions set level='editors' where permName='tiki_p_admin_galleries';
UPDATE users_permissions set level='editors' where permName='tiki_p_admin_file_galleries';
UPDATE users_permissions set level='editors' where permName='tiki_p_create_file_galleries';
UPDATE users_permissions set level='registered' where permName='tiki_p_upload_files';
UPDATE users_permissions set level='basic' where permName='tiki_p_download_files';
UPDATE users_permissions set level='basic' where permName='tiki_p_post_comments';
UPDATE users_permissions set level='basic' where permName='tiki_p_read_comments';
UPDATE users_permissions set level='editors' where permName='tiki_p_remove_comments';
UPDATE users_permissions set level='registered' where permName='tiki_p_vote_comments';
UPDATE users_permissions set level='admin' where permName='tiki_p_admin';
UPDATE users_permissions set level='basic' where permName='tiki_p_edit';
UPDATE users_permissions set level='basic' where permName='tiki_p_view';
UPDATE users_permissions set level='editors' where permName='tiki_p_remove';
UPDATE users_permissions set level='registered' where permName='tiki_p_rollback';
UPDATE users_permissions set level='editors' where permName='tiki_p_create_galleries';
UPDATE users_permissions set level='registered' where permName='tiki_p_upload_images';
UPDATE users_permissions set level='editors' where permName='tiki_p_use_HTML';
UPDATE users_permissions set level='editors' where permName='tiki_p_create_blogs';
UPDATE users_permissions set level='registered' where permName='tiki_p_blog_post';
UPDATE users_permissions set level='editors' where permName='tiki_p_blog_admin';
UPDATE users_permissions set level='editors' where permName='tiki_p_edit_article';
UPDATE users_permissions set level='editors' where permName='tiki_p_remove_article';
UPDATE users_permissions set level='basic' where permName='tiki_p_read_article';
UPDATE users_permissions set level='basic' where permName='tiki_p_submit_article';
UPDATE users_permissions set level='editors' where permName='tiki_p_edit_submission';
UPDATE users_permissions set level='editors' where permName='tiki_p_remove_submission';
UPDATE users_permissions set level='editors' where permName='tiki_p_approve_submission';
UPDATE users_permissions set level='admin' where permName='tiki_p_edit_templates';
UPDATE users_permissions set level='editors' where permName='tiki_p_admin_dynamic';
UPDATE users_permissions set level='admin' where permName='tiki_p_admin_banners';
UPDATE users_permissions set level='editors' where permName='tiki_p_admin_wiki';
UPDATE users_permissions set level='editors' where permName='tiki_p_admin_cms';
UPDATE users_permissions set level='editors' where permName='tiki_p_admin_categories';
UPDATE users_permissions set level='registered' where permName='tiki_p_send_pages';
UPDATE users_permissions set level='registered' where permName='tiki_p_sendme_pages';
UPDATE users_permissions set level='editors' where permName='tiki_p_admin_received_pages';
UPDATE users_permissions set level='editors' where permName='tiki_p_admin_forum';
UPDATE users_permissions set level='basic' where permName='tiki_p_forum_post';
UPDATE users_permissions set level='basic' where permName='tiki_p_forum_post_topic';
UPDATE users_permissions set level='basic' where permName='tiki_p_forum_read';
UPDATE users_permissions set level='registered' where permName='tiki_p_forum_vote';
UPDATE users_permissions set level='basic' where permName='tiki_p_read_blog';
UPDATE users_permissions set level='basic' where permName='tiki_p_view_image_gallery';
UPDATE users_permissions set level='basic' where permName='tiki_p_view_file_gallery';
UPDATE users_permissions set level='editors' where permName='tiki_p_edit_comments';
UPDATE users_permissions set level='basic' where permName='tiki_p_vote_poll';
UPDATE users_permissions set level='editors' where permName='tiki_p_admin_chat';
UPDATE users_permissions set level='basic' where permName='tiki_p_chat';
UPDATE users_permissions set level='basic' where permName='tiki_p_topic_read';
UPDATE users_permissions set level='basic' where permName='tiki_p_play_games';
UPDATE users_permissions set level='editors' where permName='tiki_p_admin_games';
UPDATE users_permissions set level='editors' where permName='tiki_p_edit_cookies';
UPDATE users_permissions set level='basic' where permName='tiki_p_view_stats';
UPDATE users_permissions set level='registered' where permName='tiki_p_create_bookmarks';
UPDATE users_permissions set level='registered' where permName='tiki_p_configure_modules';
UPDATE users_permissions set level='registered' where permName='tiki_p_cache_bookmarks';
UPDATE users_permissions set level='editors' where permName='tiki_p_admin_faqs';
UPDATE users_permissions set level='basic' where permName='tiki_p_view_faqs';
UPDATE users_permissions set level='editors' where permName='tiki_p_send_articles';
UPDATE users_permissions set level='registered' where permName='tiki_p_sendme_articles';
UPDATE users_permissions set level='editors' where permName='tiki_p_admin_received_articles';
UPDATE users_permissions set level='editors' where permName='tiki_p_view_referer_stats';
UPDATE users_permissions set level='basic' where permName='tiki_p_wiki_attach_files';
UPDATE users_permissions set level='editors' where permName='tiki_p_wiki_admin_attachments';
UPDATE users_permissions set level='basic' where permName='tiki_p_wiki_view_attachments';
UPDATE users_permissions set level='editors' where permName='tiki_p_batch_upload_images';
UPDATE users_permissions set level='editors' where permName='tiki_p_admin_drawings';
UPDATE users_permissions set level='basic' where permName='tiki_p_edit_drawings';
UPDATE users_permissions set level='basic' where permName='tiki_p_view_html_pages';
UPDATE users_permissions set level='editors' where permName='tiki_p_edit_html_pages';
UPDATE users_permissions set level='basic' where permName='tiki_p_view_shoutbox';
UPDATE users_permissions set level='editors' where permName='tiki_p_admin_shoutbox';
UPDATE users_permissions set level='basic' where permName='tiki_p_post_shoutbox';
UPDATE users_permissions set level='basic' where permName='tiki_p_suggest_faq';
UPDATE users_permissions set level='editors' where permName='tiki_p_edit_content_templates';
UPDATE users_permissions set level='editors' where permName='tiki_p_use_content_templates';
UPDATE users_permissions set level='editors' where permName='tiki_p_admin_quizzes';
UPDATE users_permissions set level='basic' where permName='tiki_p_take_quiz';
UPDATE users_permissions set level='basic' where permName='tiki_p_view_quiz_stats';
UPDATE users_permissions set level='editors' where permName='tiki_p_view_user_results';
UPDATE users_permissions set level='editors' where permName='tiki_p_admin_newsletters';
UPDATE users_permissions set level='basic' where permName='tiki_p_subscribe_newsletters';
UPDATE users_permissions set level='editors' where permName='tiki_p_subscribe_email';
UPDATE users_permissions set level='registered' where permName='tiki_p_use_webmail';
UPDATE users_permissions set level='editors' where permName='tiki_p_admin_surveys';
UPDATE users_permissions set level='basic' where permName='tiki_p_take_survey';
UPDATE users_permissions set level='basic' where permName='tiki_p_view_survey_stats';
UPDATE users_permissions set level='registered' where permName='tiki_p_modify_tracker_items';
UPDATE users_permissions set level='basic' where permName='tiki_p_comment_tracker_items';
UPDATE users_permissions set level='registered' where permName='tiki_p_create_tracker_items';
UPDATE users_permissions set level='editors' where permName='tiki_p_admin_trackers';
UPDATE users_permissions set level='basic' where permName='tiki_p_view_trackers';
UPDATE users_permissions set level='registered' where permName='tiki_p_attach_trackers';
UPDATE users_permissions set level='basic' where permName='tiki_p_upload_picture';
UPDATE users_permissions set level='editors' where permName='tiki_p_batch_upload_files';

CREATE FULLTEXT INDEX ft ON tiki_directory_sites (name,description);


#another Optimaziation - try
#create index up_l on users_permissions(level);


### TABLES FOR LANGUAGES ###

INSERT INTO users_permissions(permName,type,permDesc) VALUES ('tiki_p_edit_languages','tiki','Can edit translations and create new languages');

drop table if exists tiki_language;
create table tiki_language(
  source tinyblob not null,
  lang char(2) not null,
  tran tinyblob,
  primary key(source(255),lang)
);

drop table if exists tiki_untranslated;
create table tiki_untranslated(
  id integer(14) unique not null auto_increment,
  source tinyblob not null,
  lang char(2) not null,
  key(id),
  primary key(source(255),lang)
);

drop table if exists tiki_languages;
create table tiki_languages(
  lang char(2) not null,
  language varchar(255),
  primary key(lang)
);


insert into tiki_languages values('en','English');

### TABLES FOR LANGUAGES END ###

