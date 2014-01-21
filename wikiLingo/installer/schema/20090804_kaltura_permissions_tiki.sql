INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES
('tiki_p_admin_kaltura', 'Can admin kaltura feature', 'admin', 'kaltura', 'y', 'feature_kaltura'),
('tiki_p_upload_videos', 'Can upload video on kaltura server', 'registered', 'kaltura', NULL, 'feature_kaltura'),
('tiki_p_edit_videos', 'Can edit information of kaltura entry', 'registered', 'kaltura', NULL, 'feature_kaltura'),
('tiki_p_remix_videos', 'Can create kaltura remix video', 'registered', 'kaltura', NULL, 'feature_kaltura'),
('tiki_p_view_videos', 'Can view kaltura entry', 'registered', 'kaltura', NULL, 'feature_kaltura'),
('tiki_p_list_videos', 'Can list kaltura entries', 'registered', 'kaltura', NULL, 'feature_kaltura'),
('tiki_p_delete_videos', 'Can delete kaltura entry', 'registered', 'kaltura', NULL, 'feature_kaltura'),
('tiki_p_download_videos', 'Can download kaltura entry', 'registered', 'kaltura', NULL, 'feature_kaltura');
