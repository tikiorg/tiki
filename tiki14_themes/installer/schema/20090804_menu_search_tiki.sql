UPDATE  `tiki_menu_options` SET `section`='feature_search_fulltext' where `url`='tiki-searchresult.php';
INSERT IGNORE INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Search','tiki-searchindex.php',13,'feature_search','tiki_p_search','',0);
