### ADDITIONS FOR VERSION 1.0

## Remove the admin menu since it is now included in the main application menu
## you can re-assign the menu later if you still want the big admin menu
## on a side bar.
delete from tiki_modules where name='admin_menu';

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

insert into users_permissions(permName,type,permDesc) values('tiki_p_edit_templates','tiki','Can edit site templates');
insert into users_permissions(permName,type,permDesc) values('tiki_p_admin_dynamic','tiki','Can admin the dynamic content system');

### Banners System

insert into users_permissions(permName,type,permDesc) values('tiki_p_admin_banners','tiki','Administrator, can admin banners');

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

### END ADDITIONS FOR VERSION 1.0  