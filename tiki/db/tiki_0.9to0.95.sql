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

alter table tiki_modules add groups text;

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

insert into users_permissions(permName,type,permDesc) values('tiki_p_create_blogs','tiki','Can create a blog');
insert into users_permissions(permName,type,permDesc) values('tiki_p_blog_post','tiki','Can post to a blog');
insert into users_permissions(permName,type,permDesc) values('tiki_p_blog_admin','tiki','Can admin blogs');


insert into users_permissions(permName,type,permDesc) values('tiki_p_edit_article','tiki','Can edit articles');
insert into users_permissions(permName,type,permDesc) values('tiki_p_remove_article','tiki','Can remove articles');
insert into users_permissions(permName,type,permDesc) values('tiki_p_read_article','tiki','Can read articles');
insert into users_permissions(permName,type,permDesc) values('tiki_p_submit_article','tiki','Can submit articles');
insert into users_permissions(permName,type,permDesc) values('tiki_p_edit_submission','tiki','Can edit submissions');
insert into users_permissions(permName,type,permDesc) values('tiki_p_remove_submission','tiki','Can remove submissions');
insert into users_permissions(permName,type,permDesc) values('tiki_p_approve_submission','tiki','Can approve submissions');

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
  data longblob,
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
