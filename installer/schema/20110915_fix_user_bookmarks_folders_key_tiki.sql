ALTER TABLE `tiki_user_bookmarks_folders` DROP PRIMARY KEY, ADD PRIMARY KEY (`folderId`), ADD KEY `user` (`user`);
