#sylvieg
alter table users_groups drop primary key;
alter table users_groups add column id int(11) NOT NULL auto_increment first, add primary key (id), auto_increment = 1;
alter table users_groups add unique `groupName` (`groupName`);
