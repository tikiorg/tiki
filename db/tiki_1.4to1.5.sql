### MODIFICATIONS TO USERS TABLE ###
alter table users_users add challenge char(32);
alter table users_users add hash char(32);
alter table users_users add pass_due integer(14);
alter table users_users add created integer(14);
update users_users set hash=md5(password);

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

