### CHANGES FOR VERSION 1.3###

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

alter table tiki_articles add type varchar(50);
alter table tiki_articles add rating decimal(2,2);
update tiki_articles set type='Article';
alter table tiki_submissions add type varchar(50);
alter table tiki_submissions add rating decimal(2,2);
update tiki_submissions set type='Article';

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


alter table users_users add provpass varchar(30);

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

alter table tiki_pages add pageRank decimal(4,3);

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

### CHANGES FOR VERSION 1.3
