#### DATA MODEL FOR MYSQL

## FEATURED SITES (A collection of some bookmarks to be displayed
DROP TABLE IF EXISTS tiki_featured_links;
CREATE TABLE tiki_featured_links (
  url varchar(200) not null,
  title varchar(40),
  description text,
  hits integer(8),
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
  primary key(name)
);

insert into tiki_modules(name,position,ord,cache_time) values('login_box','r',1,0);
insert into tiki_modules(name,position,ord,cache_time) values('application_menu','l',1,0);
insert into tiki_modules(name,position,ord,cache_time) values('admin_menu','l',2,0);

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


### Tiki permissions
insert into users_permissions(permName,type,permDesc) values('tiki_p_admin','tiki','Administrator, can manage users groups and permissions and all the weblog features');
insert into users_permissions(permName,type,permDesc) values('tiki_p_edit','tiki','Can edit pages');
insert into users_permissions(permName,type,permDesc) values('tiki_p_view','tiki','Can view page/pages');
insert into users_permissions(permName,type,permDesc) values('tiki_p_remove','tiki','Can remove');
insert into users_permissions(permName,type,permDesc) values('tiki_p_rollback','tiki','Can rollback pages');
insert into users_permissions(permName,type,permDesc) values('tiki_p_create_galleries','tiki','Can create image galleries');
insert into users_permissions(permName,type,permDesc) values('tiki_p_upload_images','tiki','Can upload images');
insert into users_permissions(permName,type,permDesc) values('tiki_p_use_HTML','tiki','Can use HTML in pages');


  