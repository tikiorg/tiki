## THIS IS The Tiki 1.5 Data model
## If you are installing from scratch you won't need
## to use any other SQL file since they are for upgrading

#### DATA MODEL FOR MYSQL

## FEATURED SITES (A collection of some bookmarks to be displayed
DROP TABLE IF EXISTS tiki_featured_links;
CREATE TABLE tiki_featured_links (
  url varchar(200) not null,
  title varchar(40),
  description text,
  hits integer(8),
  position integer(6),
  type char(1),
  primary key(url)
);

## SESSION INFORMATION (A SIMPLE ONLINE USERS TRACKER
DROP TABLE IF EXISTS tiki_sessions;
CREATE TABLE tiki_sessions (
  sessionId char(32) not null,
  timestamp integer(14),
  primary key(sessionId)
);

## MODULES
DROP TABLE IF EXISTS tiki_modules;
CREATE TABLE tiki_modules (
  name varchar(200) not null,
  position char(1),
  ord integer(4),
  type char(1),
  title varchar(40),
  cache_time integer(14),
  rows integer(4),
  params varchar(255),
  groups text,
  primary key(name)
);

insert into tiki_modules(name,position,ord,cache_time) values('login_box','r',1,0);
insert into tiki_modules(name,position,ord,cache_time) values('application_menu','l',1,0);
### Removed from version 1.0 since the admin menu module can now be found at the main menu


DROP TABLE IF EXISTS tiki_user_modules;
CREATE TABLE tiki_user_modules (
  name varchar(200) not null,
  title varchar(40),
  data longblob,
  primary key(name)
);

### IMAGES AND GALLERIES

# GALLERIES
DROP TABLE IF EXISTS tiki_galleries;
CREATE TABLE tiki_galleries (
  galleryId integer(14) not null auto_increment,
  name varchar(80) not null,
  description text,
  created integer(14),
  lastModif integer(14),
  visible char(1),
  theme varchar(60),
  user varchar(200),
  hits integer(14),
  maxRows integer(10),
  rowImages integer(10),
  thumbSizeX integer(10),
  thumbSizeY integer(10),
  public char(1),
  primary key(galleryId)
);

DROP TABLE IF EXISTS tiki_images;
CREATE TABLE tiki_images (
  imageId integer(14) not null auto_increment,
  galleryId integer(14) not null,
  name varchar(40) not null,
  description text,
  created integer(14),
  filename varchar(80),
  filetype varchar(80),
  filesize integer(14),
  data longblob,
  xsize integer(8),
  ysize integer(8),
  user varchar(200),
  t_data longblob,
  t_type varchar(30),
  hits integer(14),
  path varchar(255),
  primary key(imageId)
);

### Table: preferences
DROP TABLE IF EXISTS tiki_preferences;
CREATE TABLE tiki_preferences (
  name varchar(40) not null,
  value varchar(250),
  primary key(name)
);

### Table: users
DROP TABLE IF EXISTS tiki_users;
CREATE TABLE tiki_users (
  user varchar(200) not null,
  password varchar(40),
  email varchar(200),
  lastLogin integer(14),
  primary key(user)
);


### Version 0.8 tables
DROP TABLE IF EXISTS tiki_tags;
CREATE TABLE tiki_tags (
  tagName varchar(80) not null,
  pageName varchar(160) not null,
  hits integer(8),
  description varchar(200),
  data longblob,
  lastModif integer(14),
  comment varchar(200),
  version integer(8) not null,
  user varchar(200),
  ip varchar(15),
  flag char(1),
  primary key(tagName,pageName)
);

### Table: pages
DROP TABLE IF EXISTS tiki_pages;
CREATE TABLE tiki_pages (
  pageName varchar(160) not null,
  hits integer(8),
  data longblob,
  description varchar(200),
  lastModif integer(14),
  comment varchar(200),
  version integer(8) not null,
  user varchar(200),
  ip varchar(15),
  flag char(1),
  points integer(8),
  votes integer(8),
  pageRank decimal(4,3),
  primary key(pageName)
);

### Table: history
DROP TABLE IF EXISTS tiki_history;
CREATE TABLE tiki_history (
  pageName varchar(160) not null,
  version integer(8) not null,
  lastModif integer(14),
  description varchar(200),
  user varchar(200),
  ip varchar(15),
  comment varchar(200),
  data longblob,
  primary key(pageName,version)
);

### Table: log
DROP TABLE IF EXISTS tiki_actionlog;
CREATE TABLE tiki_actionlog (
  action varchar(255) not null,
  lastModif integer(14),
  pageName varchar(200),
  user varchar(200),
  ip varchar(15),
  comment varchar(200)
);

### Table: links
DROP TABLE IF EXISTS tiki_links;
CREATE TABLE tiki_links (
  fromPage varchar(160) not null,
  toPage varchar(160) not null,
  primary key(fromPage, toPage)
);

### Indexes ?

DROP TABLE IF EXISTS users_users;
create table users_users(
  userId integer(8) not null auto_increment,
  email varchar(200),
  login varchar(40) not null,
  password varchar(30) not null,
  provpass varchar(30),
  realname varchar(80),
  homePage varchar(200),
  lastLogin integer(14),
  currentLogin integer(14),
  registrationDate integer(14),
  challenge char(32),
  pass_due integer(14),
  hash char(32),
  created integer(14),
  country varchar(80),
  avatarName varchar(80),
  avatarSize integer(14),
  avatarFileType varchar(250),
  avatarData longblob,
  avatarLibName varchar(200),
  avatarType char(1),
  primary key(userId)
);

### ADministrator account
insert into users_users(email,login,password,realname,hash) values('','admin','admin','System Administrator',md5('admin'));
update users_users set currentLogin=lastLogin;
update users_users set registrationDate=lastLogin;


DROP TABLE IF EXISTS users_groups;
create table users_groups(
  groupName varchar(30) not null,
  groupDesc varchar(255),
  primary key(groupName)
);
insert into users_groups(groupName,groupDesc) values('Anonymous','Public users not logged');
insert into users_groups(groupName,groupDesc) values('Registered','Users logged into the system');

DROP TABLE IF EXISTS users_usergroups;
create table users_usergroups(
  userId integer(8) not null,
  groupName varchar(30) not null,
  primary key(userId,groupName)
);

DROP TABLE IF EXISTS users_permissions;
create table users_permissions(
  permName varchar(30) not null,
  permDesc varchar(250),
  type varchar(20),
  primary key(permName)
);

DROP TABLE IF EXISTS users_grouppermissions;
create table users_grouppermissions(
  groupName varchar(30) not null,
  permName varchar(30) not null,
  value varchar(1) not null,
  primary key(groupName, permName)
);

## This table can be used to assign permissions to groups for
## individual objects of other systems, the "type" property
## is used to determine the type of object, types should be
## unique among several systems and objectIds should be uique
## for a given type
DROP TABLE IF EXISTS users_objectpermissions;
create table users_objectpermissions(
  groupName varchar(30) not null,
  permName varchar(30) not null,
  objectType varchar(20) not null,
  objectId varchar(32) not null,
  primary key(objectId,groupName,permName)
);

## Caching system
## This table is used to cache links referenced from the wiki
DROP TABLE IF EXISTS tiki_link_cache;
create table tiki_link_cache (
  cacheId integer(14) not null auto_increment,
  url varchar(250),
  data longblob,
  refresh integer(14),
  primary key(cacheId)  
);



### ADDITIONS FROM VERSION 0.95

DROP TABLE IF EXISTS tiki_user_preferences;
create table tiki_user_preferences(
  user varchar(200) not null,
  prefName varchar(40) not null,
  value varchar(250),
  primary key(user,prefName)
);

DROP TABLE IF EXISTS tiki_hotwords;
create table tiki_hotwords(
  word varchar(40) not null,
  url varchar(255) not null,
  primary key(word)
);

DROP TABLE IF EXISTS tiki_blogs;
create table tiki_blogs(
  blogId integer(8) not null auto_increment,
  created integer(14),
  lastModif integer(14),
  title varchar(200),
  description text,
  user varchar(200),
  public char(1),
  posts integer(8),
  maxPosts integer(8),
  hits integer(8),
  activity decimal(4,2),
  primary key(blogId)
);

DROP TABLE IF EXISTS tiki_blog_posts;
create table tiki_blog_posts(
  postId integer(8) not null auto_increment,
  blogId integer(8) not null,
  data text,
  created integer(14),
  user varchar(200),
  primary key(postId)
);

DROP TABLE IF EXISTS tiki_blog_activity;
create table tiki_blog_activity(
  blogId integer(8) not null,
  day integer(14) not null,
  posts integer(8),
  primary key(blogId,day)
);


DROP TABLE IF EXISTS tiki_articles;
create table tiki_articles(
  articleId integer(8) not null auto_increment,
  title varchar(80),
  authorName varchar(60),
  topicId integer(14),
  topicName varchar(40),
  size integer(12),
  useImage char(1),
  image_name varchar(80),
  image_type varchar(80),
  image_size integer(14),
  image_x integer(4),
  image_y integer(4),
  image_data longblob,
  publishDate integer(14),
  created integer(14),
  heading text,
  body longblob,
  hash char(32),
  author varchar(200),
  reads integer(14),
  votes integer(8),
  points integer(14),
  type varchar(50),
  rating decimal(2,2),
  primary key(articleId)
);

DROP TABLE IF EXISTS tiki_submissions;
create table tiki_submissions(
  subId integer(8) not null auto_increment,
  title varchar(80),
  authorName varchar(60),
  topicId integer(14),
  topicName varchar(40),
  size integer(12),
  useImage char(1),
  image_name varchar(80),
  image_type varchar(80),
  image_size integer(14),
  image_x integer(4),
  image_y integer(4),
  image_data longblob,
  publishDate integer(14),
  created integer(14),
  heading text,
  body longblob,
  hash char(32),
  author varchar(200),
  reads integer(14),
  votes integer(8),
  points integer(14),
  type varchar(50),
  rating decimal(2,2),
  primary key(subId)
);


DROP TABLE IF EXISTS tiki_topics;
CREATE TABLE tiki_topics (
  topicId integer(14) not null auto_increment,
  name varchar(40),
  image_name varchar(80),
  image_type varchar(80),
  image_size integer(14),
  image_data longblob,
  active char(1),
  created integer(14),
  primary key(topicId)
);



### ADDITIONS FOR VERSION 1.0

### Dynamic content system
DROP TABLE IF EXISTS tiki_content;
CREATE TABLE tiki_content(
  contentId integer(8) not null auto_increment,
  description text,
  primary key(contentId)
);

DROP TABLE IF EXISTS tiki_programmed_content;
CREATE TABLE tiki_programmed_content (
  pId integer(8) not null auto_increment,
  contentId integer(8) not null,
  publishDate integer(14) not null,
  data text,
  primary key(pId)
);


### Banners System



DROP TABLE IF EXISTS tiki_zones;
CREATE TABLE tiki_zones(
  zone varchar(40) not null,
  primary key(zone)
);

DROP TABLE IF EXISTS tiki_banners;
CREATE TABLE tiki_banners (
  bannerId integer(12) not null auto_increment,
  client varchar(200) not null,
  url varchar(255),
  title varchar(255),
  alt varchar(250),
  which varchar(50),
  imageData longblob,
  imageType varchar(200),
  imageName varchar(100),
  HTMLData text,
  fixedURLData varchar(255),
  textData text,
  fromDate integer(14),
  toDate integer(14),
  useDates char(1),
  mon char(1),
  tue char(1),
  wed char(1),
  thu char(1),
  fri char(1),
  sat char(1),
  sun char(1),
  hourFrom char(4),
  hourTo char(4),
  created integer(14),
  maxImpressions integer(8),
  impressions integer(8),
  clicks integer(8),
  zone varchar(40),
  primary key(bannerId)
);

### END ADDITIONS FOR VERSION 1.0  


## insert new records here
INSERT INTO users_permissions(permName,type,permDesc) VALUES ('tiki_p_admin_galleries','image galleries','Can admin Image Galleries');
INSERT INTO users_permissions(permName,type,permDesc) VALUES ('tiki_p_admin_file_galleries','file galleries','Can admin file galleries');
INSERT INTO users_permissions(permName,type,permDesc) VALUES ('tiki_p_create_file_galleries','file galleries','Can create file galleries');
INSERT INTO users_permissions(permName,type,permDesc) VALUES ('tiki_p_upload_files','file galleries','Can upload files');
INSERT INTO users_permissions(permName,type,permDesc) VALUES ('tiki_p_download_files','file galleries','Can download files');
INSERT INTO users_permissions(permName,type,permDesc) VALUES ('tiki_p_post_comments','comments','Can post new comments');
INSERT INTO users_permissions(permName,type,permDesc) VALUES ('tiki_p_read_comments','comments','Can read comments');
INSERT INTO users_permissions(permName,type,permDesc) VALUES ('tiki_p_remove_comments','comments','Can delete comments');
INSERT INTO users_permissions(permName,type,permDesc) VALUES ('tiki_p_vote_comments','comments','Can vote comments');
insert into users_permissions(permName,type,permDesc) values('tiki_p_admin','tiki','Administrator, can manage users groups and permissions and all the weblog features');
insert into users_permissions(permName,type,permDesc) values('tiki_p_edit','wiki','Can edit pages');
insert into users_permissions(permName,type,permDesc) values('tiki_p_view','wiki','Can view page/pages');
insert into users_permissions(permName,type,permDesc) values('tiki_p_remove','wiki','Can remove');
insert into users_permissions(permName,type,permDesc) values('tiki_p_rollback','wiki','Can rollback pages');
insert into users_permissions(permName,type,permDesc) values('tiki_p_create_galleries','image galleries','Can create image galleries');
insert into users_permissions(permName,type,permDesc) values('tiki_p_upload_images','image galleries','Can upload images');
insert into users_permissions(permName,type,permDesc) values('tiki_p_use_HTML','tiki','Can use HTML in pages');
insert into users_permissions(permName,type,permDesc) values('tiki_p_create_blogs','blogs','Can create a blog');
insert into users_permissions(permName,type,permDesc) values('tiki_p_blog_post','blogs','Can post to a blog');
insert into users_permissions(permName,type,permDesc) values('tiki_p_blog_admin','blogs','Can admin blogs');
insert into users_permissions(permName,type,permDesc) values('tiki_p_edit_article','cms','Can edit articles');
insert into users_permissions(permName,type,permDesc) values('tiki_p_remove_article','cms','Can remove articles');
insert into users_permissions(permName,type,permDesc) values('tiki_p_read_article','cms','Can read articles');
insert into users_permissions(permName,type,permDesc) values('tiki_p_submit_article','cms','Can submit articles');
insert into users_permissions(permName,type,permDesc) values('tiki_p_edit_submission','cms','Can edit submissions');
insert into users_permissions(permName,type,permDesc) values('tiki_p_remove_submission','cms','Can remove submissions');
insert into users_permissions(permName,type,permDesc) values('tiki_p_approve_submission','cms','Can approve submissions');
insert into users_permissions(permName,type,permDesc) values('tiki_p_edit_templates','tiki','Can edit site templates');
insert into users_permissions(permName,type,permDesc) values('tiki_p_admin_dynamic','tiki','Can admin the dynamic content system');
insert into users_permissions(permName,type,permDesc) values('tiki_p_admin_banners','tiki','Administrator, can admin banners');

## Version 1.1 additions
## Table for user votings system
# This table tracks which users voted what
drop table if exists tiki_user_votings;
create table tiki_user_votings(
  user varchar(200) not null,
  id varchar(255) not null,
  primary key(user,id)
);

### end of changes


DROP TABLE IF EXISTS tiki_file_galleries;
CREATE TABLE tiki_file_galleries (
  galleryId integer(14) not null auto_increment,
  name varchar(80) not null,
  description text,
  created integer(14),
  visible char(1),
  lastModif integer(14),
  user varchar(200),
  hits integer(14),
  votes integer(8),
  points decimal(8,2),
  maxRows integer(10),
  public char(1),
  primary key(galleryId)
);

DROP TABLE IF EXISTS tiki_files;
CREATE TABLE tiki_files (
  fileId integer(14) not null auto_increment,
  galleryId integer(14) not null,
  name varchar(40) not null,
  description text,
  created integer(14),
  filename varchar(80),
  filesize integer(14),
  filetype varchar(250),
  data longblob,
  user varchar(200),
  downloads integer(14),
  votes integer(8),
  points decimal(8,2),
  path varchar(255),
  primary key(fileId)
);
# END FILE GALLERIES AND FILES


# This is a semaphore table that can be used to
# prevent multiple users from editing the same
# page, since a Wiki is a colaborative environment
# the semaphore IS NOT enforced, just a signal
drop table if exists tiki_semaphores;
create table tiki_semaphores (
  semName varchar(30) not null,
  timestamp integer(14),
  primary key(semName)
);

# Tables for the comments system

drop table if exists tiki_comments;
create table tiki_comments (
threadId integer(14) not null auto_increment,
object char(32) not null,
parentId integer(14),
userName varchar(200),
commentDate integer(14),
hits integer(8),
type char(1),
points decimal(8,2),
votes integer(8),
average decimal(8,4),
title varchar(100),
data text,
hash char(32),
primary key(threadId)
);

drop table if exists tiki_userpoints;
create table tiki_userpoints (
user varchar(200),
points decimal(8,2),
voted integer(8)
);

### End 1.1 versions
### CHANGES FOR VERSION 1.2

insert into users_permissions(permName,type,permDesc) values('tiki_p_admin_wiki','wiki','Can admin the wiki');
insert into users_permissions(permName,type,permDesc) values('tiki_p_admin_cms','cms','Can admin the cms');


### CATEGORIES


insert into users_permissions(permName,type,permDesc) values('tiki_p_admin_categories','tiki','Can admin categories');

DROP TABLE IF EXISTS tiki_categories;
create table tiki_categories (
  categId integer(12) not null auto_increment,
  name varchar(100),
  description varchar(250),
  parentId integer(12),
  hits integer(8),
  primary key(categId)
);

DROP TABLE IF EXISTS tiki_category_objects;
create table tiki_category_objects (
  catObjectId integer(12) not null,
  categId integer(12) not null,
  primary key(catObjectId,categId)
);

DROP TABLE IF EXISTS tiki_categorized_objects;
create table tiki_categorized_objects (
  catObjectId integer(12) not null auto_increment,
  type varchar(50),
  objId varchar(50),
  description text,
  created integer(14),
  name varchar(200),
  href varchar(200),
  hits integer(8),
  primary key(catObjectId)
);

### CTEGORIES END


### COMMUNICATION CENTER

DROP TABLE IF EXISTS tiki_received_pages;
CREATE TABLE tiki_received_pages (
  receivedPageId integer(14) not null auto_increment,
  pageName varchar(160) not null,
  data longblob,
  description varchar(200),
  comment varchar(200),
  receivedFromSite varchar(200),
  receivedFromUser varchar(200),
  receivedDate integer(14),
  primary key(receivedPageId)
);

insert into users_permissions(permName,type,permDesc) values('tiki_p_send_pages','comm','Can send pages to other sites');
insert into users_permissions(permName,type,permDesc) values('tiki_p_sendme_pages','comm','Can send pages to this site');
insert into users_permissions(permName,type,permDesc) values('tiki_p_admin_received_pages','comm','Can admin received pages');

### COMMUNICATION CENTER END

### FORUMS BEGIN



insert into users_permissions(permName,type,permDesc) values('tiki_p_admin_forum','forums','Can admin forums');
insert into users_permissions(permName,type,permDesc) values('tiki_p_forum_post','forums','Can post in forums');
insert into users_permissions(permName,type,permDesc) values('tiki_p_forum_post_topic','forums','Can start threads in forums');
insert into users_permissions(permName,type,permDesc) values('tiki_p_forum_read','forums','Can read forums');
insert into users_permissions(permName,type,permDesc) values('tiki_p_forum_vote','forums','Can vote comments in forums');


drop table if exists tiki_forums;
create table tiki_forums(
  forumId integer(8) not null auto_increment,
  name varchar(200),
  description text,
  created integer(14),
  lastPost integer(14),
  threads integer(8),
  comments integer(8),
  controlFlood char(1),
  floodInterval integer(8),
  moderator varchar(200),
  hits integer(8),
  mail varchar(200),
  useMail char(1),
  section varchar(200),
  usePruneUnreplied char(1),
  pruneUnrepliedAge integer(8),
  usePruneOld char(1),
  pruneMaxAge integer(8),
  topicsPerPage integer(6),
  topicOrdering varchar(100),
  threadOrdering varchar(100),
  primary key(forumId)
);

### FORUMS END

### POLLS ####

insert into users_permissions(permName,type,permDesc) values('tiki_p_read_blog','blogs','Can read blogs');
insert into users_permissions(permName,type,permDesc) values('tiki_p_view_image_gallery','image galleries','Can view image galleries');
insert into users_permissions(permName,type,permDesc) values('tiki_p_view_file_gallery','file galleries','Can view file galleries');
INSERT INTO users_permissions(permName,type,permDesc) VALUES ('tiki_p_edit_comments','comments','Can edit all comments');

insert into users_permissions(permName,type,permDesc) values('tiki_p_vote_poll','tiki','Can vote polls');

DROP TABLE IF EXISTS tiki_polls;
create table tiki_polls(
  pollId integer(8) not null auto_increment,
  title varchar(200),
  votes integer(8),
  active char(1),
  publishDate integer(14),
  primary key(pollId)
);

DROP TABLE IF EXISTS tiki_poll_options;
create table tiki_poll_options (
  pollId integer(8) not null,
  optionId integer(8) not null auto_increment,
  title varchar(200),
  votes integer(8),
  primary key(optionId)
);

### POLLS ###

### EMail notification ###
DROP TABLE IF EXISTS tiki_mail_events;
create table tiki_mail_events(
  event varchar(200),
  object varchar(200),
  email varchar(200)
);

### RSS MODULES ###
DROP TABLE IF EXISTS tiki_rss_modules;
create table tiki_rss_modules(
  rssId integer(8) not null auto_increment,
  name varchar(30) not null,
  description text,
  url varchar(255) not null,
  refresh integer(8),
  lastUpdated integer(14),
  content longblob,
  primary key(rssId)
);
### /RSS MODULES ###

### MENU BUILDER ###
DROP TABLE IF EXISTS tiki_menu_languages;
create table tiki_menu_languages (
  menuId integer(8) not null auto_increment,
  language char(2) not null,
  primary key(menuId,language)
);

DROP TABLE IF EXISTS tiki_menus;
create table tiki_menus (
  menuId integer(8) not null auto_increment,
  name varchar(20) not null,
  description text,
  type char(1),
  primary key(menuId)
);


DROP TABLE IF EXISTS tiki_menu_options;
create table tiki_menu_options (
  optionId integer(8) not null auto_increment,
  menuId integer(8),
  type char(1),
  name varchar(20),
  url varchar(255),
  position integer(4),
  primary key(optionId)
);
### /MENU BUILDER ###


#### CHAT SYSTEM #####
insert into users_permissions(permName,type,permDesc) values('tiki_p_admin_chat','chat','Administrator, can create channels remove channels etc');
insert into users_permissions(permName,type,permDesc) values('tiki_p_chat','chat','Can use the chat system');
DROP TABLE IF EXISTS tiki_chat_channels;
CREATE TABLE tiki_chat_channels (
  channelId int(8) NOT NULL auto_increment,
  name varchar(30) default NULL,
  description varchar(250) default NULL,
  max_users int(8) default NULL,
  mode char(1) default NULL,
  moderator varchar(200) default NULL,
  active char(1) default NULL,
  refresh integer(6),
  PRIMARY KEY  (channelId)
); 

DROP TABLE IF EXISTS tiki_chat_messages;
CREATE TABLE tiki_chat_messages (
  messageId int(8) NOT NULL auto_increment,
  channelId int(8) NOT NULL default '0',
  data varchar(255) default NULL,
  poster varchar(200) NOT NULL default 'anonymous',
  timestamp int(14) default NULL,
  PRIMARY KEY  (messageId)
);


#### /CHAT SYSTEM #####


### END OF CHANGES FOR VERSION 1.2

### Changes for 1.3 ####

DROP TABLE IF EXISTS tiki_private_messages;
CREATE TABLE tiki_private_messages(
  messageId int(8) NOT NULL auto_increment,
  toNickname varchar(200) NOT NULL,
  data varchar(255) default NULL,
  poster varchar(200) NOT NULL default 'anonymous',
  timestamp int(14) default NULL,
  PRIMARY KEY  (messageId)
);

DROP TABLE if exists tiki_chat_users;
create table tiki_chat_users(
  nickname varchar(200) not null,
  channelId int(8) not null,
  timestamp integer(14),
  primary key(nickname,channelId)
);

insert into users_permissions(permName,type,permDesc) values('tiki_p_topic_read','topics','Can read a topic (Applies only to individual topic perms)');


DROP TABLE IF EXISTS tiki_games;
CREATE TABLE tiki_games (
  gameName varchar(200) not null,
  hits integer(8),
  votes integer(8),
  points integer(8),
  primary key(gameName)
);

insert into users_permissions(permName,type,permDesc) values('tiki_p_play_games','games','Can play games');
insert into users_permissions(permName,type,permDesc) values('tiki_p_admin_games','games','Can admin games');

### Cookies ##
insert into users_permissions(permName,type,permDesc) values('tiki_p_edit_cookies','tiki','Can admin cookies');
DROP TABLE IF EXISTS tiki_cookies;
CREATE TABLE tiki_cookies (
  cookieId integer(10) not null auto_increment,
  cookie varchar(255),
  primary key(cookieId)
);

insert into users_permissions(permName,type,permDesc) values('tiki_p_view_stats','tiki','Can view site stats');

### Statistics ###
DROP TABLE IF EXISTS tiki_pageviews;
CREATE TABLE tiki_pageviews (
  day integer(14) not null,
  pageviews integer(14),
  primary key(day)
);

insert into users_permissions(permName,type,permDesc) values('tiki_p_create_bookmarks','user','Can create user bookmarksche user bookmarks');
insert into users_permissions(permName,type,permDesc) values('tiki_p_configure_modules','user','Can configure modules');

### User asigned modules ###
DROP TABLE IF EXISTS tiki_user_assigned_modules;
CREATE TABLE tiki_user_assigned_modules (
  name varchar(200) not null,
  position char(1),
  ord integer(4),
  type char(1),
  title varchar(40),
  cache_time integer(14),
  rows integer(4),
  groups text,
  params varchar(250),
  user varchar(200) not null,
  primary key(name,user)
);
### User asigned modules ###


insert into users_permissions(permName,type,permDesc) values('tiki_p_cache_bookmarks','user','Can cache user bookmarks');

### User Bookmarks ####
DROP TABLE IF EXISTS tiki_user_bookmarks_urls;
CREATE TABLE tiki_user_bookmarks_urls (
  urlId integer(12) not null auto_increment,
  name varchar(30),
  url varchar(250),
  data longblob,
  lastUpdated integer(14),
  folderId integer(12) not null,
  user varchar(200) not null,
  primary key(urlId)
);

DROP TABLE if exists tiki_user_bookmarks_folders;
CREATE TABLE tiki_user_bookmarks_folders (
  folderId integer(12) not null auto_increment,
  parentId integer(12),
  user varchar(200) not null,
  name varchar(30),
  primary key(user,folderId)
);

### User Bookmarks ####

insert into users_permissions(permName,type,permDesc) values('tiki_p_admin_faqs','faqs','Can admin faqs');
insert into users_permissions(permName,type,permDesc) values('tiki_p_view_faqs','faqs','Can view faqs');

### FAQS
DROP TABLE IF EXISTS tiki_faqs;
create table tiki_faqs(
  faqId integer(10) not null auto_increment,
  title varchar(200),
  description text,
  created integer(14),
  questions integer(5),
  hits integer(8),
  canSuggest char(1),
  primary key(faqId)
);

DROP TABLE IF EXISTS tiki_faq_questions;
create table tiki_faq_questions(
  questionId integer(10) not null auto_increment,
  faqId integer(10),
  position integer(4),
  question text,
  answer text,
  primary key(questionId)
);
### FAQS 

insert into users_permissions(permName,type,permDesc) values('tiki_p_send_articles','comm','Can send articles to other sites');
insert into users_permissions(permName,type,permDesc) values('tiki_p_sendme_articles','comm','Can send articles to this site');
insert into users_permissions(permName,type,permDesc) values('tiki_p_admin_received_articles','comm','Can admin received articles');

DROP TABLE IF EXISTS tiki_received_articles;
CREATE TABLE tiki_received_articles(
  receivedArticleId integer(14) not null auto_increment,
  receivedFromSite varchar(200),
  receivedFromUser varchar(200),
  receivedDate integer(14),
  title varchar(80),
  authorName varchar(60),
  size integer(12),
  useImage char(1),
  image_name varchar(80),
  image_type varchar(80),
  image_size integer(14),
  image_x integer(4),
  image_y integer(4),
  image_data longblob,
  publishDate integer(14),
  created integer(14),
  heading text,
  body longblob,
  hash char(32),
  author varchar(200),
  type varchar(50),
  rating decimal(2,2),
  primary key(receivedArticleId)  
);


### End of Changes for 1.3 ####

### CHANGES FOR VERSION 1.4

CREATE INDEX pageName     ON tiki_pages (pageName);
CREATE INDEX data         ON tiki_pages (data(255));
CREATE INDEX pageRank     ON tiki_pages (pageRank);
CREATE INDEX name         ON tiki_galleries (name);
CREATE INDEX description  ON tiki_galleries (description(255));
CREATE INDEX hits         ON tiki_galleries (hits);
CREATE INDEX title        ON tiki_faqs (title);
CREATE INDEX description  ON tiki_faqs (description(255));
CREATE INDEX hits         ON tiki_faqs (hits);
CREATE INDEX faqId        ON tiki_faq_questions (faqId);
CREATE INDEX question     ON tiki_faq_questions (question(255));
CREATE INDEX answer       ON tiki_faq_questions (answer(255));
CREATE INDEX name         ON tiki_images (name);
CREATE INDEX description  ON tiki_images (description(255));
CREATE INDEX hits         ON tiki_images (hits);
CREATE INDEX title        ON tiki_comments (title);
CREATE INDEX data         ON tiki_comments (data(255));
CREATE INDEX object       ON tiki_comments (object);
CREATE INDEX hits         ON tiki_comments (hits);
CREATE INDEX name         ON tiki_files (name);
CREATE INDEX description  ON tiki_files (description(255));
CREATE INDEX downloads    ON tiki_files (downloads);
CREATE INDEX title        ON tiki_blogs (title);
CREATE INDEX description  ON tiki_blogs (description(255));
CREATE INDEX hits         ON tiki_blogs (hits);
CREATE INDEX title        ON tiki_articles (title);
CREATE INDEX heading      ON tiki_articles (heading(255));
CREATE INDEX body         ON tiki_articles (body(255));
CREATE INDEX reads        ON tiki_articles (reads);
CREATE INDEX data         ON tiki_blog_posts (data(255));
CREATE INDEX blogId       ON tiki_blog_posts (blogId);
CREATE INDEX created      ON tiki_blog_posts (created);


INSERT INTO users_permissions(permName,type,permDesc) VALUES ('tiki_p_view_referer_stats','tiki','Can view referer stats');

DROP TABLE IF EXISTS tiki_referer_stats;
create table tiki_referer_stats (
  referer varchar(50) not null,
  hits integer(10),
  last integer(14),
  primary key(referer)
);


### Wiki attachments

INSERT INTO users_permissions(permName,type,permDesc) VALUES ('tiki_p_wiki_attach_files','wiki','Can attach files to wiki pages');
INSERT INTO users_permissions(permName,type,permDesc) VALUES ('tiki_p_wiki_admin_attachments','wiki','Can admin attachments to wiki pages');
INSERT INTO users_permissions(permName,type,permDesc) VALUES ('tiki_p_wiki_view_attachments','wiki','Can view wiki attachments and download');

DROP TABLE IF EXISTS tiki_wiki_attachments;
create table tiki_wiki_attachments(
  attId integer(12) not null auto_increment,
  page varchar(40) not null,
  filename varchar(80),
  filetype varchar(80),
  filesize integer(14),
  user varchar(200),
  data longblob,
  path varchar(255),
  downloads integer(10),
  created integer(14),
  comment varchar(250),
  primary key(attId)
);
###




INSERT INTO users_permissions(permName,type,permDesc) VALUES ('tiki_p_batch_upload_images','image galleries','Can upload zip files with images');

INSERT INTO users_permissions(permName,type,permDesc) VALUES ('tiki_p_admin_drawings','drawings','Can admin drawings');
INSERT INTO users_permissions(permName,type,permDesc) VALUES ('tiki_p_edit_drawings','drawings','Can edit drawings');



## search stats

DROP TABLE IF EXISTS tiki_search_stats;
create table tiki_search_stats (
  term varchar(50) not null,
  hits integer(10),
  primary key(term)
);

### Static and dynamic HTML pages ###
INSERT INTO users_permissions(permName,type,permDesc) VALUES ('tiki_p_view_html_pages','html pages','Can view HTML pages');
INSERT INTO users_permissions(permName,type,permDesc) VALUES ('tiki_p_edit_html_pages','html pages','Can edit HTML pages');
DROP TABLE IF EXISTS tiki_html_pages;
create table tiki_html_pages (
  pageName varchar(40) not null,
  content longblob,
  refresh integer(10),
  type char(1),
  created integer(14),
  primary key(pageName)
);

DROP TABLE IF EXISTS tiki_html_pages_dynamic_zones;
create table tiki_html_pages_dynamic_zones (
  pageName varchar(40) not null,
  zone varchar(80) not null,
  type char(2),
  content text,
  primary key(pageName,zone)
);
###




update tiki_files set path='';

### Groups including groups ###
DROP TABLE IF EXISTS tiki_group_inclusion;
create table tiki_group_inclusion(
  groupName varchar(30) not null, 
  includeGroup varchar(30) not null,
  primary key(groupName,includeGroup)
);
###

### ShoutBox ####
insert into users_permissions(permName,type,permDesc) values('tiki_p_view_shoutbox','shoutbox','Can view shoutbox');
insert into users_permissions(permName,type,permDesc) values('tiki_p_admin_shoutbox','shoutbox','Can admin shoutbox (Edit/remove msgs)');
insert into users_permissions(permName,type,permDesc) values('tiki_p_post_shoutbox','shoutbox','Can pot messages in shoutbox');

DROP TABLE IF EXISTS tiki_shoutbox;
create table tiki_shoutbox(
 msgId integer(10) not null auto_increment,
 message varchar(255),
 timestamp integer(14),
 user varchar(200),
 hash char(32),
 primary key(msgId)
);

### ShoutBox ###


update tiki_featured_links set type='f';

insert into users_permissions(permName,type,permDesc) values('tiki_p_suggest_faq','faqs','Can suggest faq questions');

update tiki_faqs set canSuggest='n';

DROP TABLE IF EXISTS tiki_suggested_faq_questions;
create table tiki_suggested_faq_questions (
   sfqId integer(10) not null auto_increment,
   faqId integer(10) not null,
   question text,
   answer text,
   created integer(14),
   user varchar(200),
   primary key(sfqId)
);

####
INSERT INTO users_permissions(permName,type,permDesc) VALUES ('tiki_p_edit_content_templates','content templates','Can edit content templates');
INSERT INTO users_permissions(permName,type,permDesc) VALUES ('tiki_p_use_content_templates','content templates','Can use content templates');

DROP TABLE IF EXISTS tiki_content_templates;
create table tiki_content_templates (
  templateId integer(10) not null auto_increment,
  content longblob,
  name varchar(200),
  created integer(14),
  primary key(templateId)
);

DROP TABLE IF EXISTS tiki_content_templates_sections;
create table tiki_content_templates_sections(
  templateId integer(10) not null,
  section varchar(250) not null,
  primary key(templateId,section)
);


### SQL PART

INSERT INTO users_permissions(permName,type,permDesc) VALUES ('tiki_p_admin_quizzes','quizzes','Can admin quizzes');
INSERT INTO users_permissions(permName,type,permDesc) VALUES ('tiki_p_take_quiz','quizzes','Can take quizzes');
INSERT INTO users_permissions(permName,type,permDesc) VALUES ('tiki_p_view_quiz_stats','quizzes','Can view quiz stats');
INSERT INTO users_permissions(permName,type,permDesc) VALUES ('tiki_p_view_user_results','quizzes','Can view user quiz results');

DROP TABLE IF EXISTS tiki_quiz_stats_sum;
create table tiki_quiz_stats_sum (
  quizId integer(10) not null,
  quizName varchar(255),
  timesTaken integer(10),
  avgpoints decimal(5,2),
  avgavg decimal(5,2),
  avgtime decimal(5,2),
  primary key(quizId)
);


### Quizzes

## This table is used to prevent a registered user from
## taking the same quiz twice
drop table if exists tiki_user_taken_quizzes;
create table tiki_user_taken_quizzes(
  user varchar(200) not null,
  quizId varchar(255) not null,
  primary key(user,quizId)
);


DROP TABLE IF EXISTS tiki_quizzes;
create table tiki_quizzes(
  quizId integer(10) not null auto_increment,
  name varchar(255),
  description text,
  canRepeat char(1),
  storeResults char(1),
  questionsPerPage integer(4),
  timeLimited char(1),
  timeLimit integer(14),
  created integer(14),
  taken integer(10),
  primary key(quizId)
);

### Quiz questions
DROP TABLE IF EXISTS tiki_quiz_questions;
create table tiki_quiz_questions(
  questionId integer(10) not null auto_increment,
  quizId integer(10),
  question text,
  position integer(4),
  type char(1),
  maxPoints integer(4),
  primary key(questionId)
);

### Question options
DROP TABLE IF EXISTS tiki_quiz_question_options;
create table tiki_quiz_question_options(
  optionId integer(10) not null auto_increment,
  questionId integer(10),
  optionText text,
  points integer(4),
  primary key(optionId)
);

### Automatic quiz results shown to the user
DROP TABLE IF EXISTS tiki_quiz_results;
create table tiki_quiz_results (
  resultId integer(10) not null auto_increment,
  quizId integer(10),
  fromPoints integer(4),
  toPoints integer(4),
  answer text,
  primary key(resultId)
);

### Statistics about quizzes
DROP TABLE IF EXISTS tiki_quiz_stats;
create table tiki_quiz_stats (
  quizId integer(10) not null,
  questionId integer(10) not null,
  optionId integer(10) not null,
  votes integer(10),
  primary key(quizId,questionId,optionId)
);

### Results of quizzes taken by users
DROP TABLE IF EXISTS tiki_user_quizzes;
create table tiki_user_quizzes (
  user varchar(100),
  quizId integer(10),
  timestamp integer(14),
  timeTaken integer(14),
  points integer(12),
  maxPoints integer(12),
  resultId integer(10),
  userResultId integer(10) not null auto_increment,
  primary key(userResultId)
);

### What the user answered in the quiz
DROP TABLE IF EXISTS tiki_user_answers;
create table tiki_user_answers (
  userResultId integer(10) not null,
  quizId integer(10) not null,
  questionId integer(10) not null,
  optionId integer(10) not null,
  primary key(userResultId,quizId,questionId,optionId)
);

### END OF CHANGES FOR VERSION 1.4


### CHANGES FOR 1.5


### Newsletters
drop table if exists tiki_newsletters;
create table tiki_newsletters(
  nlId integer(12) not null auto_increment,
  name varchar(200),
  description text,
  created integer(14),
  lastSent integer(14),
  editions integer(10),
  users integer(10),
  allowAnySub char(1),
  frequency integer(14),
  primary key(nlId)
);

drop table if exists tiki_newsletter_subscriptions;
create table tiki_newsletter_subscriptions (
  nlId integer(12) not null,
  email varchar(255) not null,
  code char(32),
  valid char(1),
  subscribed integer(14),
  primary key(nlId,email)
);

drop table if exists tiki_sent_newsletters;
create table tiki_sent_newsletters (
  editionId integer(12) not null auto_increment,
  nlId integer(12) not null,  
  users integer(10),
  sent integer(14),
  subject varchar(200),
  data longblob,
  primary key(editionId)
);

INSERT INTO users_permissions(permName,type,permDesc) VALUES ('tiki_p_admin_newsletters','newsletters','Can admin newsletters');
INSERT INTO users_permissions(permName,type,permDesc) VALUES ('tiki_p_subscribe_newsletters','newsletters','Can subscribe to newsletters');
INSERT INTO users_permissions(permName,type,permDesc) VALUES ('tiki_p_subscribe_email','newsletters','Can subscribe any email to newsletters');

### Newsletters


# $Id: tiki.sql,v 1.19 2003-01-06 21:00:15 lrargerich Exp $


INSERT INTO users_permissions(permName,type,permDesc) VALUES ('tiki_p_use_webmail','webmail','Can use webmail');
### SURVEYS
INSERT INTO users_permissions(permName,type,permDesc) VALUES ('tiki_p_admin_surveys','surveys','Can admin surveys');
INSERT INTO users_permissions(permName,type,permDesc) VALUES ('tiki_p_take_survey','surveys','Can take surveys');
INSERT INTO users_permissions(permName,type,permDesc) VALUES ('tiki_p_view_survey_stats','surveys','Can view survey stats');

drop table if exists tiki_surveys;
create table tiki_surveys (
  surveyId integer(12) not null auto_increment,
  name varchar(200),
  description text,
  taken integer(10),
  lastTaken integer(14),
  created integer(14),
  status char(1),
  primary key(surveyId)
);

drop table if exists tiki_survey_questions;
create table tiki_survey_questions (
  questionId integer(12) not null auto_increment,
  surveyId integer(12) not null,
  question text,
  options text,
  type char(1),
  position integer(5),
  votes integer(10),
  value integer(10),
  average decimal(4,2),
  primary key(questionId)
);

drop table if exists tiki_survey_question_options;
create table tiki_survey_question_options (
  optionId integer(12) not null auto_increment,
  questionId integer(12) not null,
  qoption text,
  votes integer(10),
  primary key(optionId)
);
### SURVEYS


### Webmail ###
drop table if exists tiki_webmail_contacts;
create table tiki_webmail_contacts (
  contactId integer(12) not null auto_increment,
  firstName varchar(80),
  lastName varchar(80),
  email varchar(250),
  nickname varchar(200),
  user varchar(200) not null,
  primary key(contactId)
);

drop table if exists tiki_webmail_messages;
create table tiki_webmail_messages (
  accountId integer(12) not null,
  mailId varchar(255) not null,
  user varchar(200) not null,
  isRead char(1),
  isReplied char(1),
  isFlagged char(1),
  primary key(accountId,mailId)
);

drop table if exists tiki_user_mail_accounts;
create table tiki_user_mail_accounts (
  accountId integer(12) not null auto_increment,
  user varchar(200) not null,
  account varchar(50) not null,
  pop varchar(255),
  current char(1),
  port integer(4),
  username varchar(100),
  pass varchar(100),
  msgs integer(4),
  smtp varchar(255),
  useAuth char(1),
  smtpPort integer(4),
  primary key(accountId)
);

### Webmail ###

### MODIFICATIONS TO USERS TABLE ###

### TABLES FOR TRACKERS ###

DROP TABLE IF EXISTS tiki_tracker_item_attachments;
create table tiki_tracker_item_attachments(
  attId integer(12) not null auto_increment,
  itemId varchar(40) not null,
  filename varchar(80),
  filetype varchar(80),
  filesize integer(14),
  user varchar(200),
  data longblob,
  path varchar(255),
  downloads integer(10),
  created integer(14),
  comment varchar(250),
  primary key(attId)
);


INSERT INTO users_permissions(permName,type,permDesc) VALUES ('tiki_p_modify_tracker_items','trackers','Can change tracker items');
INSERT INTO users_permissions(permName,type,permDesc) VALUES ('tiki_p_comment_tracker_items','trackers','Can insert comments for tracker items');
INSERT INTO users_permissions(permName,type,permDesc) VALUES ('tiki_p_create_tracker_items','trackers','Can create new items for trackers');
INSERT INTO users_permissions(permName,type,permDesc) VALUES ('tiki_p_admin_trackers','trackers','Can admin trackers');
INSERT INTO users_permissions(permName,type,permDesc) VALUES ('tiki_p_view_trackers','trackers','Can view trackers');
INSERT INTO users_permissions(permName,type,permDesc) VALUES ('tiki_p_attach_trackers','trackers','Can attach files to tracker items');

drop table if exists tiki_tracker_item_comments;
create table tiki_tracker_item_comments(
  commentId integer(12) not null auto_increment,
  itemId integer(12) not null,
  user varchar(200),
  data text,
  title varchar(200),
  posted integer(14),
  primary key(commentId)
);

drop table if exists tiki_trackers;
create table tiki_trackers(
  trackerId integer(12) not null auto_increment,
  name varchar(80),
  description text,
  created integer(14),
  lastModif integer(14),
  showCreated char(1),
  showStatus char(1),
  showLastModif char(1),
  useComments char(1),
  useAttachments char(1),
  items integer(10),
  primary key(trackerId)
);

drop table if exists tiki_tracker_fields;
create table tiki_tracker_fields(
  fieldId integer(12) not null auto_increment,
  trackerId integer(12) not null,
  name varchar(80),
  options text,
  type char(1),
  isMain char(1),
  isTblVisible char(1),
  primary key(fieldId)  
);

drop table if exists tiki_tracker_items;
create table tiki_tracker_items(
  itemId integer(12) not null auto_increment,
  trackerId integer(12) not null,
  created integer(14),
  status char(1),
  lastModif integer(14),
  primary key(itemId)
);

drop table if exists tiki_tracker_item_fields;
create table tiki_tracker_item_fields(
  itemId integer(12) not null,
  fieldId integer(12) not null,
  value text,
  primary key(itemId,fieldId)
);

### TABLES FOR TRACKERS END ###


### END OF CHANGES FOR 1.5