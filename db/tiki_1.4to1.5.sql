### SURVEYS
INSERT INTO users_permissions(permName,type,permDesc) VALUES ('tiki_p_admin_surveys','quizzes','Can admin surveys');
INSERT INTO users_permissions(permName,type,permDesc) VALUES ('tiki_p_take_survey','quizzes','Can take surveys');
INSERT INTO users_permissions(permName,type,permDesc) VALUES ('tiki_p_view_survey_stats','quizzes','Can view survey stats');

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

alter table tiki_forums add section varchar(200);
update tiki_forums set section="";

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


INSERT /*! IGNORE */ INTO users_permissions(permName,type,permDesc) VALUES ('tiki_p_modify_tracker_items','trackers','Can change tracker items');
INSERT /*! IGNORE */ INTO users_permissions(permName,type,permDesc) VALUES ('tiki_p_comment_tracker_items','trackers','Can insert comments for tracker items');
INSERT /*! IGNORE */ INTO users_permissions(permName,type,permDesc) VALUES ('tiki_p_create_tracker_items','trackers','Can create new items for trackers');
INSERT /*! IGNORE */ INTO users_permissions(permName,type,permDesc) VALUES ('tiki_p_admin_trackers','trackers','Can admin trackers');
INSERT /*! IGNORE */ INTO users_permissions(permName,type,permDesc) VALUES ('tiki_p_view_trackers','trackers','Can view trackers');
INSERT /*! IGNORE */ INTO users_permissions(permName,type,permDesc) VALUES ('tiki_p_attach_trackers','trackers','Can attach files to tracker items');

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

