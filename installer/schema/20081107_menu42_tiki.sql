# sylvieg
DELETE FROM `tiki_menu_options` WHERE menuId='42' and type='o' and name='View templates' and url='tiki-edit_templates.php' and position='1155' and section='feature_view_tpl' and perm='tiki_p_edit_templates' and groupname='' ;
INSERT INTO `tiki_menu_options` (`optionId`, `menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (147,42,'o','View Templates','tiki-edit_templates.php',1155,'feature_view_tpl','tiki_p_edit_templates','',2);

DELETE FROM `tiki_menu_options` WHERE menuId='42' and type='o' and name='Edit CSS' and url='tiki-edit_css.php' and position='1158' and section='feature_editcss' and perm='tiki_p_create_css' and groupname='' ;
INSERT INTO `tiki_menu_options` (`optionId`, `menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (146,42,'o','Edit CSS','tiki-edit_css.php',1158,'feature_editcss','tiki_p_create_css','',2);
