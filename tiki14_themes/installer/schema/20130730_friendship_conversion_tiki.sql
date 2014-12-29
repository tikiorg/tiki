INSERT INTO tiki_object_relations (`relation`, `source_type`, `source_itemId`, `target_type`, `target_itemId`)
	SELECT 'tiki.friend.follow', 'user', `user`, 'user', `friend` FROM `tiki_friends`;
INSERT INTO tiki_object_relations (`relation`, `source_type`, `source_itemId`, `target_type`, `target_itemId`)
	SELECT 'tiki.friend.follow.request', 'user', `userFrom`, 'user', `userTo` FROM `tiki_friendship_requests`;

DROP TABLE `tiki_friends`;
DROP TABLE `tiki_friendship_requests`;
