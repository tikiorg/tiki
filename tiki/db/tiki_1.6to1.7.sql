alter table tiki_pages add creator varchar(200);

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


## Permissions

INSERT INTO users_permissions(permName,type,permDesc,level) VALUES ('tiki_p_admin_workflow','workflow','Can admin workflow processes','admin');
INSERT INTO users_permissions(permName,type,permDesc,level) VALUES ('tiki_p_abort_instance','workflow','Can abort a process instance','editor');
INSERT INTO users_permissions(permName,type,permDesc,level) VALUES ('tiki_p_use_workflow','workflow','Can execute workflow activities','registered');
INSERT INTO users_permissions(permName,type,permDesc,level) VALUES ('tiki_p_exception_instance','workflow','Can declare an instance as exception','registered');
INSERT INTO users_permissions(permName,type,permDesc,level) VALUES ('tiki_p_send_instance','workflow','Can send instances after completion','registered');


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

#### Workflow schema ends