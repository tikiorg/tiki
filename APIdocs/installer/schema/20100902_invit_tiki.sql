CREATE TABLE `tiki_invit` (
  `id` int(11) NOT NULL auto_increment,
  `inviter` varchar(200) NOT NULL,
  `groups` varchar(255) default NULL,
  `ts` int(11) NOT NULL,
  `emailsubject` varchar(255) NOT NULL,
  `emailcontent` text NOT NULL,
  `wikicontent` text,
  `wikipageafter` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
);

CREATE TABLE `tiki_invited` (
  `id` int(11) NOT NULL auto_increment,
  `id_invit` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `firstname` varchar(24) NOT NULL,
  `lastname` varchar(24) NOT NULL,
  `used` enum('no','registered','logged') NOT NULL,
  `used_on_user` varchar(200) default NULL,
  PRIMARY KEY  (`id`),
  KEY `id_invit` (`id_invit`),
  KEY `used_on_user` (`used_on_user`)
);

INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES ('tiki_p_invit', 'Can invit users by email, and include them in groups', 'registered', 'tiki', NULL, 'feature_invit');
