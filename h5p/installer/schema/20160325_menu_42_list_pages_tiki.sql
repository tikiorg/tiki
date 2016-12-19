# Otherwise, once you get to "Create a Wiki Page", clicking "List Pages" gets you in the "Create a Wiki Page" tab again
UPDATE `tiki_menu_options` SET `url`='tiki-listpages.php?cookietab=1#tab1' WHERE `menuId`=42 AND `name`='List Pages';
