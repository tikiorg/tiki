
### DATA MODEL FOR VERSION 0.9 -SPICA-

#### DATA MODEL FOR MYSQL

## FEATURED SITES (A collection of some bookmarks to be displayed
DROP TABLE IF EXISTS tiki_featured_links;
CREATE TABLE tiki_featured_links (
  url varchar(200) not null,
  title varchar(40),
  description text,
  hits integer(8),
  position integer(6),
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
  groups text,
  primary key(name)
);

insert into tiki_modules(name,position,ord,cache_time) values('login_box','r',1,0);
insert into tiki_modules(name,position,ord,cache_time) values('application_menu','l',1,0);
### Removed from version 1.0 since the admin menu module can now be found at the main menu
### insert into tiki_modules(name,position,ord,cache_time) values('admin_menu','l',2,0);

DROP TABLE IF EXISTS tiki_user_modules;
CREATE TABLE tiki_user_modules (
  name varchar(200) not null,
  title varchar(40),
  data text,
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
  pageName varchar(40) not null,
  hits integer(8),
  data text,
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
  pageName varchar(40) not null,
  hits integer(8),
  data text,
  lastModif integer(14),
  comment varchar(200),
  version integer(8) not null,
  user varchar(200),
  ip varchar(15),
  flag char(1),
  points integer(8),
  votes integer(8),
  primary key(pageName)
);

### Table: history
DROP TABLE IF EXISTS tiki_history;
CREATE TABLE tiki_history (
  pageName varchar(40) not null,
  version integer(8) not null,
  lastModif integer(14),
  user varchar(200),
  ip varchar(15),
  comment varchar(200),
  data text,
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
  fromPage varchar(40) not null,
  toPage varchar(40) not null,
  primary key(fromPage, toPage)
);

### Indexes ?

DROP TABLE IF EXISTS users_users;
create table users_users(
  userId integer(8) not null auto_increment,
  email varchar(200),
  login varchar(40) not null,
  password varchar(30) not null,
  realname varchar(80),
  homePage varchar(200),
  lastLogin integer(14),
  country varchar(80),
  primary key(userId)
);

### ADministrator account
insert into users_users(email,login,password,realname) values('','admin','admin','System Administrator');

DROP TABLE IF EXISTS users_groups;
create table users_groups(
  groupName varchar(30) not null,
  groupDesc varchar(255),
  primary key(groupName)
);
insert into users_groups(groupName,groupDesc) values('Anonymous','Public users not logged');
insert into users_groups(groupName,groupDesc) values('Registered','Users logged into the system');

DROP TABLE IF EXISTS users_userGroups;
create table users_userGroups(
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

DROP TABLE IF EXISTS users_groupPermissions;
create table users_groupPermissions(
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
DROP TABLE IF EXISTS users_objectPermissions;
create table users_objectPermissions(
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