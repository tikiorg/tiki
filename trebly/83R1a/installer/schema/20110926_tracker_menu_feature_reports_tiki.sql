# robertplummer
# Add menu option for tracker reports

INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42, 'o', 'Tracker Reports', 'tiki-tracker_reports.php', 812, 'feature_tracker_reports', 'tiki_p_admin_trackers', '', 0);