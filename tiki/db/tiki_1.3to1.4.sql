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

alter table tiki_semaphores add timestamp integer(14);

alter table tiki_images add path varchar(255);
INSERT INTO users_permissions(permName,type,permDesc) VALUES ('tiki_p_batch_upload_images','image galleries','Can upload zip files with images');

INSERT INTO users_permissions(permName,type,permDesc) VALUES ('tiki_p_admin_drawings','drawings','Can admin drawings');
INSERT INTO users_permissions(permName,type,permDesc) VALUES ('tiki_p_edit_drawings','drawings','Can edit drawings');

alter table tiki_modules add params varchar(250);
alter table tiki_user_assigned_modules add params varchar(250);

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

alter table users_users add currentLogin integer(14);
update users_users set currentLogin=lastLogin;
alter table users_users add registrationDate integer(14);
update users_users set registrationDate=lastLogin;

alter table tiki_files add path varchar(255);
update tiki_files set path='';

### Groups including groups ###
DROP TABLE IF EXISTS tiki_group_inclusion;
create table tiki_group_inclusion(
  groupName varchar(30) not null, 
  includeGroup varchar(30) not null,
  primary key(groupName,includeGroup)
);
###

### Shoutbox ####
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

### Shoutbox ###

alter table tiki_featured_links add type char(1);
update tiki_featured_links set type='f';

insert into users_permissions(permName,type,permDesc) values('tiki_p_suggest_faq','faqs','Can suggest faq questions');
alter table tiki_faqs add canSuggest char(1);
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


  
