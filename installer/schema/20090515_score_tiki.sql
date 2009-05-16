#2008-10-19 sylvieg
ALTER TABLE  tiki_users_score DROP PRIMARY KEY;
ALTER TABLE  tiki_users_score DROP KEY user;
ALTER TABLE tiki_users_score CHANGE event_id event_id char(200) NOT NULL default '';
ALTER TABLE  tiki_users_score ADD PRIMARY KEY (user(110),event_id(110));
ALTER TABLE  tiki_users_score ADD KEY user (user(110),event_id(110),expire);
