### DIRECTORIES BEGIN

INSERT INTO users_permissions(permName,type,permDesc) VALUES ('tiki_p_admin_directory','directory','Can admin the directory');
INSERT INTO users_permissions(permName,type,permDesc) VALUES ('tiki_p_admin_directory_cats','directory','Can admin directory categories');
INSERT INTO users_permissions(permName,type,permDesc) VALUES ('tiki_p_submit_link','directory','Can submit sites to the directory');
INSERT INTO users_permissions(permName,type,permDesc) VALUES ('tiki_p_autosubmit_link','directory','Submited links are valid');
INSERT INTO users_permissions(permName,type,permDesc) VALUES ('tiki_p_validate_links','directory','Can validate submited links');
INSERT INTO users_permissions(permName,type,permDesc) VALUES ('tiki_p_admin_directory_sites','directory','Can admin any directory site');

drop table if exists tiki_directory_categories;
create table tiki_directory_categories(
  categId integer(10) not null auto_increment,
  parent integer(10),
  name varchar(240),
  description text,
  childrenType char(1),
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

