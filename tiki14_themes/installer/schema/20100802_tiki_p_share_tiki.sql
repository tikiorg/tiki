INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_share', 'Can share a page (email, twitter, facebook, message, forums)', 'Basic', 'tiki', NULL, NULL);
UPDATE `users_grouppermissions` SET `permName`='tiki_p_share' WHERE `permName`='tiki_p_promote';
DELETE FROM `users_permissions` WHERE `permName`='tiki_p_promote';