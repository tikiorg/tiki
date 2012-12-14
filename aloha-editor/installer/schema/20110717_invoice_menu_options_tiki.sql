# robertplummer
# Add menu options for tiki invoice

INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42, 'r', 'Invoice', 'tiki-list_invoices.php', 790, 'feature_invoice', 'tiki_p_admin', '', 0);
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42, 'o', 'New Invoice', 'tiki-edit_invoice.php', 791, 'feature_invoice', 'tiki_p_admin', '', 0);