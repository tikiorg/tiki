rename table users_objectPermissions to users_objectpermissions;
rename table users_userGroups to users_usergroups;
rename table users_groupPermissions to users_grouppermissions;

alter table tiki_comments add type char(1);
alter table tiki_galleries add visible char(1);
alter table tiki_file_galleries add visible char(1);
alter table tiki_comments add hits integer(8);

update tiki_galleries set visible='y';
update tiki_file_galleries set visible='y';

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
  pageName varchar(40) not null,
  data longblob,
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
) 
#### /CHAT SYSTEM #####


### END OF CHANGES FOR VERSION 1.2
