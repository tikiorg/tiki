update tiki_modules set name='last_modif_pages' where name='last_modif';
update tiki_modules set name='last_image_galleries' where name = 'last_galleries';
update tiki_modules set name='top_image_galleries' where name = 'top_galleries';
update tiki_modules set name='user_image_galleries' where name = 'user_galleries';


## Table for user votings system
# This table tracks which users voted what
drop table if exists tiki_user_votings;
create table tiki_user_votings(
  user varchar(200) not null,
  id varchar(255) not null,
  primary key(user,id)
);

### Add position to featured links
alter table tiki_featured_links add position integer(6);


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
average decimal(8,4),
votes integer(8),
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


### Change permission type for existing permissions in old versions of Tiki!
## Delete old records
delete from users_permissions;
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
### end of changes


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
