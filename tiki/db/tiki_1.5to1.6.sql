## THIS FILE IS JUST A HELP FOR DEVELOPERS IT SHOULDNT BE USED IN A 1.5 DISTRIBUTION

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
