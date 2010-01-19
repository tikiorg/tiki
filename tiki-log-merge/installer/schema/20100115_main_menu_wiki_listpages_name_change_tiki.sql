SET @pcant=0;
SELECT (@pcant:=count(*)) FROM tiki_menu_options WHERE `position` = 220;
INSERT INTO tiki_menu_options (`name`) VALUES ('Pages');



