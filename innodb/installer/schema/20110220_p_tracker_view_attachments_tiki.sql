# new perm to allow viewing and downloading of tracker items attachments -- luci
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_tracker_view_attachments', 'Can view tracker items attachments and download', 'registered', 'trackers', NULL, 'feature_trackers');

