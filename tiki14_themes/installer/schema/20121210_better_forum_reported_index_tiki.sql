ALTER TABLE `tiki_forums_reported` DROP PRIMARY KEY;
ALTER TABLE `tiki_forums_reported` ADD PRIMARY KEY (`threadId`, `forumId`, `parentId`, `user`);
