## THIS FILE IS JUST A HELP FOR DEVELOPERS IT SHOULDNT BE USED IN A 1.5 DISTRIBUTION

CREATE FULLTEXT INDEX ft ON tiki_directory_sites (name,description);

### Inter-user messages

INSERT INTO users_permissions(permName,type,permDesc) VALUES ('tiki_p_messages','messu','Can use the messaging system');
INSERT INTO users_permissions(permName,type,permDesc) VALUES ('tiki_p_broadcast','messu','Can boradcast messages');

drop table if exists messu_messages;
create table messu_messages(
  msgId integer(14) not null auto_increment,
  user varchar(200) not null,
  user_from varchar(200) not null,
  user_to text,
  user_cc text,
  user_bcc text,
  subject varchar(255),
  body text,
  hash char(32),
  date integer(14),
  isRead char(1),
  isReplied char(1),
  isFlagged char(1),
  priority integer(2),
  primary key(msgId)
);


alter table tiki_sessions add user varchar(200);

INSERT INTO users_permissions(permName,type,permDesc) VALUES ('tiki_p_admin_mailin','tiki','Can admin mail-in accounts');

### Mailin
drop table if exists tiki_mailin_accounts;
create table tiki_mailin_accounts (
  accountId integer(12) not null auto_increment,
  user varchar(200) not null,
  account varchar(50) not null,
  pop varchar(255),
  port integer(4),
  username varchar(100),
  pass varchar(100),
  active char(1),
  type varchar(40),
  smtp varchar(255),
  useAuth char(1),
  smtpPort integer(4),
  primary key(accountId)
);


### Tiki structures permissions
INSERT INTO users_permissions(permName,type,permDesc) VALUES ('tiki_p_edit_structures','wiki','Can create and edit structures');

### Cache for wiki pages
alter table tiki_pages add cache longblob;
alter table tiki_pages add cache_timestamp integer(14);





### DIRECTORIES BEGIN

INSERT INTO users_permissions(permName,type,permDesc) VALUES ('tiki_p_admin_directory','directory','Can admin the directory');
INSERT INTO users_permissions(permName,type,permDesc) VALUES ('tiki_p_view_directory','directory','Can use the directory');
INSERT INTO users_permissions(permName,type,permDesc) VALUES ('tiki_p_admin_directory_cats','directory','Can admin directory categories');
INSERT INTO users_permissions(permName,type,permDesc) VALUES ('tiki_p_admin_directory_sites','directory','Can admin directory sites');
INSERT INTO users_permissions(permName,type,permDesc) VALUES ('tiki_p_submit_link','directory','Can submit sites to the directory');
INSERT INTO users_permissions(permName,type,permDesc) VALUES ('tiki_p_autosubmit_link','directory','Submited links are valid');
INSERT INTO users_permissions(permName,type,permDesc) VALUES ('tiki_p_validate_links','directory','Can validate submited links');


drop table if exists tiki_directory_categories;
create table tiki_directory_categories(
  categId integer(10) not null auto_increment,
  parent integer(10),
  name varchar(240),
  description text,
  childrenType char(1),
  sites integer(10),
  viewableChildren integer(4),
  allowSites char(1),
  showCount char(1),
  editorGroup varchar(200),
  hits integer(12),
  primary key(categId)
);

drop table if exists tiki_directory_sites;
create table tiki_directory_sites(
  siteId integer(14) not null auto_increment,
  name varchar(240),
  description text,
  url varchar(255),
  country varchar(255),
  hits integer(12),
  isValid char(1),
  created integer(14),
  lastModif integer(14), 
  cache longblob,
  cache_timestamp integer(14),
  primary key(siteId)
);

drop table if exists tiki_category_sites;
create table tiki_category_sites(
  categId integer(10) not null,
  siteId integer(14) not null,
  primary key(categId,siteId)
);

drop table if exists tiki_related_categories;
create table tiki_related_categories(
  categId integer(10) not null,
  relatedTo integer(10) not null,
  primary key(categId,relatedTo)
);

drop table if exists tiki_directory_search;
create table tiki_directory_search(
  term varchar(250) not null,
  hits integer(14),
  primary key(term)
);

### DIRECTORIES END

### FULLTEXT SEARCH BEGIN

alter table tiki_pages modify data text;
create fulltext index ft on tiki_pages (pagename,data);
create fulltext index ft on tiki_galleries (name,description);
create fulltext index ft on tiki_faqs (title,description);
create fulltext index ft on tiki_faq_questions (question,answer);
create fulltext index ft on tiki_images (name,description);
create fulltext index ft on tiki_comments (title,data);
create fulltext index ft on tiki_files (name,description);
create fulltext index ft on tiki_blogs (title,description);
alter table tiki_articles modify body text;
create fulltext index ft on tiki_articles (title,heading,body);
create fulltext index ft on tiki_blog_posts (data);

### FULLTEXT SEARCH END
