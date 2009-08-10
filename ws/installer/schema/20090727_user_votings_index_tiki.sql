ALTER TABLE  tiki_user_votings DROP PRIMARY KEY;
ALTER TABLE  tiki_user_votings ADD KEY (`user`(100),`id`(100));
