INSERT INTO users_permissions(permName,type,permDesc,level) VALUES ('tiki_p_autoapprove_submission','cms','Submited articles automatically approved','editors');

INSERT INTO users_permissions(permName,type,permDesc,level) VALUES ('tiki_p_forums_report','forums','Can report msgs to moderator','registered');
### forum posts can be reported to moderator ###
drop table if exists tiki_forums_reported;
create table tiki_forums_reported(
  threadId integer(12) not null,
  forumId integer(12) not null,
  parentId integer(12) not null,
  user varchar(200),
  timestamp integer(14),
  reason varchar(250),
  primary key(threadId)
);
### 


### file galleries configuration ###
alter table tiki_files add reference_url varchar(250);
alter table tiki_files add is_reference char(1);
update tiki_files set reference_url = '';
update tiki_files set is_reference = 'n';

alter table tiki_file_galleries add show_id char(1);
alter table tiki_file_galleries add show_icon char(1);
update tiki_file_galleries set show_icon = 'y';
alter table tiki_file_galleries add show_name char(1);
alter table tiki_file_galleries add show_size char(1);
alter table tiki_file_galleries add show_description char(1);
alter table tiki_file_galleries add max_desc integer(8);
alter table tiki_file_galleries add show_created char(1);
alter table tiki_file_galleries add show_dl char(1);
update tiki_file_galleries set show_id = 'y';
update tiki_file_galleries set show_name = 'a';
update tiki_file_galleries set show_size = 'y';
update tiki_file_galleries set show_description = 'y';
update tiki_file_galleries set show_created = 'y';
update tiki_file_galleries set show_dl = 'y';
update tiki_file_galleries set max_desc = '1024';

### table to track posts read from forums ###
drop table if exists tiki_forum_reads;
create table tiki_forum_reads(
	user varchar(200) not null,
	threadId integer(14) not null,
	forumId integer(14),
	timestamp integer(14),
	primary key(user,threadId)
);




### tiki  banning system
INSERT INTO users_permissions(permName,type,permDesc,level) VALUES ('tiki_p_admin_banning','tiki','Can ban users or ips','admin');
drop table if exists tiki_banning;
create table tiki_banning(
	banId integer(12) not null auto_increment,
	mode enum('user','ip'),
	title varchar(200),
	ip1 varchar(3),
	ip2 varchar(3),
	ip3 varchar(3),
	ip4 varchar(3),
	user varchar(200),
	date_from timestamp,
	date_to timestamp,
	use_dates char(1),
	created integer(14),
	message text,
	primary key(banId)
);

drop table if exists tiki_banning_sections;
create table tiki_banning_sections(
	banId integer(12) not null,
	section varchar(100) not null,
	primary key(banId,section)
);
### banning

alter table tiki_comments add user_ip varchar(15);
update tiki_comments set user_ip = '127.0.0.1';

### Forum attachments (!) ###
INSERT INTO users_permissions(permName,type,permDesc,level) VALUES ('tiki_p_forum_attach','forums','Can attach to forum posts','editors');

drop table if exists tiki_forum_attachments;
create table tiki_forum_attachments(
	attId integer(14) not null auto_increment,
	threadId integer(14) not null,
	qId integer(14) not null,
	forumId integer(14),
	filename varchar(250),
	filetype varchar(250),
	filesize integer(12),
	data longblob,
	dir varchar(200),
	created integer(14),
	path varchar(250),
	primary key(attId)
);
alter table tiki_forums add att varchar(80);
update tiki_forums set att='att_no';
alter table tiki_forums add att_store varchar(4);
update tiki_forums set att_store='db';
alter table tiki_forums add att_store_dir varchar(250);
update tiki_forums set att_store='';
alter table tiki_forums add att_max_size integer(12);
update tiki_forums set att_max_size = 1000000;
### Forum attachments ###


INSERT INTO users_permissions(permName,type,permDesc,level) VALUES ('tiki_p_live_support_admin','support','Admin live support system','admin');
INSERT INTO users_permissions(permName,type,permDesc,level) VALUES ('tiki_p_live_support','support','Can use live support system','basic');

### Wiki drawings versioning
drop table if exists tiki_drawings;
create table tiki_drawings(
	drawId integer(12) not null auto_increment,
	version integer(8),
	name varchar(250),
	filename_draw varchar(250),
	filename_pad varchar(250),
	timestamp integer(14),
	user varchar(200),
	primary key(drawId)
);

### Wiki drawings versioning


### Live support chat system
### under construction
drop table if exists tiki_live_support_messages;
create table tiki_live_support_messages(
	msgId integer(12) not null auto_increment,
	data text,
	timestamp integer(14),
	user varchar(200),
	username varchar(200),
	priority integer(2),
	status char(1),
	assigned_to varchar(200),
	resolution varchar(100),
	title varchar(200),
	module integer(4),
	email varchar(250),
	primary key(msgId)
);

### Support canned responses for chat




drop table if exists tiki_live_support_modules;
create table tiki_live_support_modules(
	modId integer(4) not null auto_increment,
	name varchar(90),
	primary key(modId)
);

insert into tiki_live_support_modules(name) values('wiki');
insert into tiki_live_support_modules(name) values('forums');
insert into tiki_live_support_modules(name) values('image galleries');
insert into tiki_live_support_modules(name) values('file galleries');
insert into tiki_live_support_modules(name) values('directory');
insert into tiki_live_support_modules(name) values('workflow');
insert into tiki_live_support_modules(name) values('charts');

drop table if exists tiki_live_support_message_comments;
create table tiki_live_support_message_comments(
    cId integer(12) not null auto_increment,
	msgId integer(12),
	data text,
	timestamp integer(14),
	primary key(cId)
);

drop table if exists tiki_live_support_operators;
create table tiki_live_support_operators(
	user varchar(200) not null,
	accepted_requests integer(10),
	status varchar(20),
	longest_chat integer(10),
	shortest_chat integer(10),
	average_chat integer(10),
	last_chat integer(14),
	time_online integer(10),
	votes integer(10),
	points integer(10),
	status_since integer(14),
	primary key(user)
);

drop table if exists tiki_live_support_requests;
create table tiki_live_support_requests(
	reqId char(32) not null,
	user varchar(200),
	tiki_user varchar(200),
	email varchar(200),
	operator varchar(200),
	operator_id char(32),
	user_id char(32),
	reason text,
	req_timestamp integer(14),
	timestamp integer(14),
	status varchar(40),
	resolution varchar(40),
	chat_started integer(14),
	chat_ended integer(14),
	primary key(reqId)
);

drop table if exists tiki_live_support_events;
create table tiki_live_support_events(
	eventId integer(14) not null auto_increment,
	reqId char(32) not null,
	type varchar(40),
	seqId integer(14),
	senderId varchar(32),
	data text,
	timestamp integer(14),
	primary key(eventId)
);

### Live support chat system ends


INSERT INTO users_permissions(permName,type,permDesc,level) VALUES ('tiki_p_forum_autoapp','forums','Auto approve forum posts','admin');

drop table if exists tiki_user_postings;
create table  tiki_user_postings(
	user varchar(200) not null,
	posts integer(12),
	last integer(14),
	first integer(14),
	level integer(8),
	primary key(user)
);

alter table tiki_forums add ui_level char(1);
update tiki_forums set ui_level='n';

alter table tiki_forums add forum_password char(32);
update tiki_forums set forum_password = '';
alter table tiki_forums add forum_use_password char(1);
update tiki_forums set forum_use_password = 'n';

alter table tiki_forums add moderator_group varchar(200);
update tiki_forums set moderator_group='';


drop table if exists tiki_forums_queue;
create table tiki_forums_queue(
	qId integer(14) not null auto_increment,
	object char(32),
	parentId integer(14),
	forumId integer(14),
	timestamp integer(14),
	user varchar(200),
	title varchar(240),
	data text,
	type varchar(60),
	hash char(32),
	topic_smiley varchar(80),
	topic_title varchar(240),
	summary varchar(240),
	primary key(qId)
);

alter table tiki_forums add approval_type varchar(20);
update tiki_forums set approval_type='all_posted';

INSERT INTO users_permissions(permName,type,permDesc,level) VALUES ('tiki_p_admin_charts','charts','Can admin charts','admin');
INSERT INTO users_permissions(permName,type,permDesc,level) VALUES ('tiki_p_view_chart','charts','Can view charts','basic');
INSERT INTO users_permissions(permName,type,permDesc,level) VALUES ('tiki_p_vote_chart','charts','Can vote','basic');
INSERT INTO users_permissions(permName,type,permDesc,level) VALUES ('tiki_p_suggest_chart_item','charts','Can suggest items','basic');
INSERT INTO users_permissions(permName,type,permDesc,level) VALUES ('tiki_p_autoval_chart_suggestions','charts','Autovalidate suggestions','editors');

## Change to track duplicate file uploads in file galleries
alter table tiki_files add hash char(32);

##

### Changes in articles
alter table tiki_articles add isfloat char(1);
update tiki_articles set isfloat='n';
alter table tiki_submissions add isfloat char(1);
update tiki_submissions set isfloat='n';
###

drop table if exists tiki_user_watches;
create table tiki_user_watches(
  user varchar(200) not null,
  event varchar(40) not null,
  object varchar(200) not null,
  hash char(32),
  title varchar(250),
  type varchar(200),
  url varchar(250),
  email varchar(200),
  primary key(user,event,object)
);

alter table tiki_user_notes add parse_mode char(20);
update tiki_user_notes set parse_mode='raw';

### Forums configuration ###
alter table tiki_comments add summary varchar(240);
update  tiki_comments set summary='';
alter table tiki_comments add smiley varchar(80);
update tiki_comments set smiley='';

alter table tiki_forums add outbound_address varchar(250);
alter table tiki_forums add inbound_pop_server varchar(250);
alter table tiki_forums add inbound_pop_port integer(4);
alter table tiki_forums add inbound_pop_user varchar(200);
alter table tiki_forums add inbound_pop_password varchar(80);

alter table tiki_forums add topic_smileys char(1);
alter table tiki_forums add ui_avatar char(1);
alter table tiki_forums add ui_flag char(1);
alter table tiki_forums add ui_posts char(1);
alter table tiki_forums add ui_email char(1);
alter table tiki_forums add ui_online char(1);
alter table tiki_forums add topic_summary char(1);

###


update tiki_forums set outbound_address='';
update tiki_forums set topic_smileys='n';
update tiki_forums set ui_avatar='y';
update tiki_forums set ui_flag='y';
update tiki_forums set ui_posts='n';
update tiki_forums set ui_email='n';
update tiki_forums set ui_online='y';
update tiki_forums set topic_summary='n';

alter table tiki_forums add show_description char(1);
update tiki_forums set show_description='y';
alter table tiki_forums add topics_list_replies char(1);
update tiki_forums set topics_list_replies='y';
alter table tiki_forums add topics_list_reads char(1);
update tiki_forums set topics_list_reads='y';
alter table tiki_forums add topics_list_pts char(1);
update tiki_forums set topics_list_pts='y';
alter table tiki_forums add topics_list_lastpost char(1);
update tiki_forums set topics_list_lastpost='y';
alter table tiki_forums add topics_list_author char(1);
update tiki_forums set topics_list_author='y';
alter table tiki_forums add vote_threads char(1);
update tiki_forums set vote_threads='y';

### end of forums configuration ###

### New feature: charts ###
drop table if exists tiki_charts;
create table tiki_charts(
  chartId integer(14) not null auto_increment,
  title varchar(250),
  description text,
  hits integer(14),
  singleItemVotes char(1),
  singleChartVotes char(1),
  suggestions char(1),
  autoValidate char(1),
  topN integer(6),
  maxVoteValue integer(4),
  frequency integer(14),
  showAverage char(1),
  isActive char(1),
  showVotes char(1),
  useCookies char(1),
  lastChart integer(14),
  voteAgainAfter integer(14),
  created integer(14),
  hist integer(12),
  primary key(chartId)
);

drop table if exists tiki_chart_items;
create table tiki_chart_items(
  itemId integer(14) not null auto_increment,
  title varchar(250),
  description text,
  chartId integer(14) not null,
  created integer(14),
  URL varchar(250),
  votes integer(14),
  points integer(14),
  average decimal(4,2),
  primary key(itemId)
);

drop table if exists tiki_charts_rankings;
create table tiki_charts_rankings(
  chartId integer(14) not null,
  itemId integer(14) not null,
  position integer(14) not null,
  timestamp integer(14) not null,
  lastPosition integer(14) not null,
  period integer(14) not null,
  rvotes integer(14) not null,
  raverage decimal(4,2) not null,
  primary key(chartId,itemId,period)
);


drop table if exists tiki_charts_votes;
create table tiki_charts_votes(
  user varchar(200) not null,
  itemId integer(14) not null,
  timestamp integer(14),
  chartId integer(14),
  primary key(user,ItemId)  
);

### End of charts feature ###


### Modifications for blog improvements
drop table if exists tiki_blog_posts_images;
create table tiki_blog_posts_images(
  imgId integer(14) not null auto_increment,
  postId integer(14) not null,
  filename varchar(80),
  filetype varchar(80),
  filesize integer(14),
  data longblob,
  primary key(imgId)
);

alter table tiki_blogs add heading text;
alter table tiki_blogs add use_find char(1);
alter table tiki_blog_posts add trackbacks_to text;
update tiki_blog_posts set trackbacks_to='';
alter table tiki_blog_posts add trackbacks_from text;
update tiki_blog_posts set trackbacks_from='';
alter table tiki_blogs add use_title char(1);
alter table tiki_blogs add add_date char(1);
alter table tiki_blogs add add_poster char(1);
alter table tiki_blogs add allow_comments char(1);
alter table tiki_blog_posts add title varchar(80);
### End of modifications for blog improvements


alter table tiki_pages add creator varchar(200);
update tiki_pages set creator=user;

#### Workflow tables and permissions
## Workflow Roles, each role must be asociated with a process
## since roles are not shared among processes. (You can have
## a role with the same name in two processes with two different
## meanings and different users mapped)
drop table if exists galaxia_roles;
create table galaxia_roles(
  roleId integer(14) not null auto_increment,
  pId integer(14) not null,
  lastModif integer(14),
  name varchar(80),
  description text,
  primary key(roleId)
);

## Mapping from users to process roles
drop table if exists galaxia_user_roles;
create table galaxia_user_roles(
  pId integer(14) not null,
  roleId integer(14) not null auto_increment,
  user varchar(200) not null,
  primary key(roleId, user)
);

## Workflow processes.
drop table if exists galaxia_processes;
create table galaxia_processes(
  pId integer(14) not null auto_increment,
  name varchar(80),
  isValid char(1),
  isActive char(1),
  version varchar(12),
  description text,
  lastModif integer(14),
  normalized_name varchar(80),
  primary key(pId)
);

## Process activities
drop table if exists galaxia_activities;
create table galaxia_activities(
  activityId integer(14) not null auto_increment,
  name varchar(80),
  normalized_name varchar(80),
  pId integer(14) not null,
  type enum('start','end','split','switch','join','activity','standalone'),
  isAutoRouted char(1),
  flowNum integer(10),
  isInteractive char(1),
  lastModif integer(14), 
  description text ,
 primary key(activityId)
);

## transitions
drop table if exists galaxia_transitions;
create table galaxia_transitions(
  pId integer(14) not null,
  actFromId integer(14) not null,
  actToId integer(14) not null,
  primary key(actFromId, actToId)
);

## activity roles
drop table if exists galaxia_activity_roles;
create table galaxia_activity_roles(
  activityId integer(14) not null,
  roleId integer(14) not null,
  primary key(activityId, roleId)
);


## instances
## status can be: active,exception,waiting,aborted
drop table if exists galaxia_instances;
create table galaxia_instances(
  instanceId integer(14) not null auto_increment,
  pId integer(14) not null,
  started integer(14),
  owner varchar(200),
  nextActivity integer(14),
  nextUser varchar(200),
  ended integer(14),
  status enum('active','exception','aborted','completed'),
  properties longblob,
  primary key(instanceId)
);

## instance_activities
## tracks where each instance is (can be in two places at the same time!)
drop table if exists galaxia_instance_activities;
create table galaxia_instance_activities(
	instanceId integer(14) not null,
	activityId integer(14) not null,
	started integer(14) not null,
	ended integer(14) not null,
	user varchar(200),
	status enum('running','completed'),
	primary key(instanceId, activityId)
);

## workitems
drop table if exists galaxia_workitems;
create table galaxia_workitems(
  itemId integer(14) not null auto_increment,
  instanceId integer(14) not null,
  orderId integer(14) not null,
  activityId integer(14) not null,
  properties longblob,
  started integer(14),
  ended integer(14),
  user varchar(200),
  primary key(itemId)
);


### Instance comments
drop table if exists galaxia_instance_comments;
create table galaxia_instance_comments(
  cId integer(14) not null auto_increment,
  instanceId integer(14) not null,
  user varchar(200),
  activityId integer(14),
  hash char(32),
  title varchar(250),
  comment text,
  activity varchar(80),
  timestamp integer(14),
  primary key(cId)
);

### Copyright management
drop table if exists tiki_copyrights;
create table tiki_copyrights(
	copyrightId integer(12) not null auto_increment,
	page varchar(200),
	title varchar(200),
	year int,
	authors varchar(200),
	copyright_order int,
	userName varchar(200),
    primary key(copyrightId)
);

INSERT INTO users_permissions(permName,type,permDesc,level) VALUES ('tiki_p_edit_copyrights','wiki','Can edit copyright notices','admin');

#### Workflow schema ends

#### Calendar tables

DROP TABLE IF EXISTS tiki_calendars;
CREATE TABLE tiki_calendars (
  calendarId int(14) NOT NULL auto_increment,
  name varchar(80) NOT NULL default '',
  description varchar(255) default NULL,
  user varchar(40) NOT NULL default '',
  customlocations enum('n','y') NOT NULL default 'n',
  customcategories enum('n','y') NOT NULL default 'n',
  customlanguages enum('n','y') NOT NULL default 'n',
  custompriorities enum('n','y') NOT NULL default 'n',
  customparticipants enum('n','y') NOT NULL default 'n',
  created int(14) NOT NULL default '0',
  lastmodif int(14) NOT NULL default '0',
  PRIMARY KEY  (calendarId)
);

DROP TABLE IF EXISTS tiki_calendar_roles;
CREATE TABLE tiki_calendar_roles (
  calitemId int(14) NOT NULL,
  username varchar(40) NOT NULL,
  role enum('0','1','2','3','6') NOT NULL default '0',
  PRIMARY KEY  (calitemId,username(16), role)
);

DROP TABLE IF EXISTS tiki_calendar_locations;
CREATE TABLE tiki_calendar_locations (
  callocId int(14) NOT NULL auto_increment,
  calendarId int(14) NOT NULL default '0',
  name varchar(255) NOT NULL default '',
  description blob,
  PRIMARY KEY  (callocId),
	UNIQUE KEY locname (calendarId,name(16))
);

DROP TABLE IF EXISTS tiki_calendar_categories;
CREATE TABLE tiki_calendar_categories (
  calcatId int(11) NOT NULL auto_increment,
  calendarId int(14) NOT NULL default '0',
  name varchar(255) NOT NULL default '',
  PRIMARY KEY  (calcatId),
  UNIQUE KEY catname (calendarId,name(16))
);

DROP TABLE IF EXISTS tiki_calendar_items;
CREATE TABLE tiki_calendar_items (
  calitemId int(14) NOT NULL auto_increment,
  calendarId int(14) NOT NULL default '0',
  start int(14) NOT NULL default '0',
  end int(14) NOT NULL default '0',
  locationId int(14) default NULL,
  categoryId int(14) default NULL,
  priority enum('1','2','3','4','5','6','7','8','9') NOT NULL default '1',
  status enum('0','1','2') NOT NULL default '0',
  url varchar(255),
  lang char(2) NOT NULL default 'en',
  name varchar(255) NOT NULL default '',
  description blob,
  user varchar(40) default NULL,
  created int(14) NOT NULL,
  lastmodif int(14) NOT NULL,
  PRIMARY KEY  (calitemId),
  KEY calendarId (calendarId)
);

## Permissions

INSERT INTO users_permissions(permName,type,permDesc,level) VALUES ('tiki_p_admin_workflow','workflow','Can admin workflow processes','admin');
INSERT INTO users_permissions(permName,type,permDesc,level) VALUES ('tiki_p_abort_instance','workflow','Can abort a process instance','editors');
INSERT INTO users_permissions(permName,type,permDesc,level) VALUES ('tiki_p_use_workflow','workflow','Can execute workflow activities','registered');
INSERT INTO users_permissions(permName,type,permDesc,level) VALUES ('tiki_p_exception_instance','workflow','Can declare an instance as exception','registered');
INSERT INTO users_permissions(permName,type,permDesc,level) VALUES ('tiki_p_send_instance','workflow','Can send instances after completion','registered');

# for calendar
INSERT INTO users_permissions(permName,type,permDesc,level) VALUES ('tiki_p_view_calendar','calendar','Can browse the calendar','basic');
INSERT INTO users_permissions(permName,type,permDesc,level) VALUES ('tiki_p_change_events','calendar','Can change events in the calendar','registered');
INSERT INTO users_permissions(permName,type,permDesc,level) VALUES ('tiki_p_add_events','calendar','Can add events in the calendar','registered');
INSERT INTO users_permissions(permName,type,permDesc,level) VALUES ('tiki_p_admin_calendar','calendar','Can create/admin calendars','admin');
INSERT INTO tiki_preferences(name,value) VALUES ('feature_calendar','n');

# for csseditor
INSERT INTO users_permissions(permName,type,permDesc,level) VALUES ('tiki_p_create_css','tiki','Can create new css suffixed with -user','registered');
INSERT INTO tiki_preferences(name,value) VALUES ('feature_editcss','n');

# for copyrights
INSERT INTO tiki_preferences(name,value) VALUES ('wiki_feature_copyrights','n');

# for wiki automonospaced text
INSERT INTO tiki_preferences(name,value) VALUES ('feature_wiki_monosp','y');

# for debugger console
INSERT INTO tiki_preferences(name,value) VALUES ('feature_debugger_console','n');

# last minute changes, rather minor
UPDATE tiki_preferences set name='Can post messages in shoutbox' where name='Can pot messages in shoutbox';

# The following translations have moved to directories matching their
# ISO 639 language codes: Danish is 'da' and Swedish is 'sv'.
UPDATE tiki_preferences SET value='da' WHERE name='language' AND value='dk';
UPDATE tiki_user_preferences SET value='da' WHERE prefName='language' AND value='dk';
UPDATE tiki_preferences SET value='sv' WHERE name='language' AND value='sw';
UPDATE tiki_user_preferences SET value='sv' WHERE prefName='language' AND value='sw';

# change of charlength for some fields
ALTER TABLE `tiki_dsn` CHANGE `name` `name` VARCHAR( 200 ) NOT NULL;
ALTER TABLE `tiki_extwiki` CHANGE `name` `name` VARCHAR( 200 ) NOT NULL;
ALTER TABLE `tiki_menu_options` CHANGE `name` `name` VARCHAR( 200 ) NOT NULL;
ALTER TABLE `tiki_menus` CHANGE `name` `name` VARCHAR( 200 ) NOT NULL;
ALTER TABLE `tiki_featured_links` CHANGE `title` `title` VARCHAR( 200 ) default NULL;
ALTER TABLE `tiki_files` CHANGE `name` `name` VARCHAR( 200 ) NOT NULL default '';
ALTER TABLE `tiki_html_pages` CHANGE `pageName` `pageName` VARCHAR( 200 ) NOT NULL default '';
ALTER TABLE `tiki_html_pages_dynamic_zones` CHANGE `pageName` `pageName` VARCHAR( 200 ) NOT NULL default '';
ALTER TABLE `tiki_images` CHANGE `name` `name` VARCHAR( 200 ) NOT NULL default '';
ALTER TABLE `tiki_wiki_attachments` CHANGE `page` `page` VARCHAR( 200 ) NOT NULL default '';


