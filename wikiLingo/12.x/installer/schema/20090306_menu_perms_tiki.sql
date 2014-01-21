#sylvieg
update `tiki_menu_options` set `perm`='tiki-list_file_gallery.php|tiki_p_view_file_gallery|tiki_p_upload_files' where url='tiki-list_file_gallery.php' and `type`='s';
update `tiki_menu_options` set `perm`='tiki_p_upload_files' where url='tiki-upload_file.php';