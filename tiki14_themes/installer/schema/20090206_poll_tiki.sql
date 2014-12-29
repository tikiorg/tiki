#sylvieg
ALTER TABLE tiki_polls DROP COLUMN anonym;
ALTER TABLE tiki_user_votings ADD COLUMN time int(14) not null;
ALTER TABLE tiki_user_votings ADD COLUMN ip varchar(15) default NULL after user;
ALTER TABLE tiki_user_votings ADD KEY ip (`ip`);
ALTER TABLE tiki_user_votings ADD KEY id (`id`);
INSERT INTO users_permissions (`permName`, `permDesc`, level, type) VALUES ('tiki_p_view_poll_voters', 'Can view poll voters', 'basic', 'polls');
ALTER TABLE tiki_polls ADD COLUMN `voteConsiderationSpan` int(4) default 0;