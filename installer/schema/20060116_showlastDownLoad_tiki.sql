# This file says 2006 but this is a mistake. Since renaming a DB schema modification file is more work (because some have already used it), it stayed as is. Should have no impact but a note is added here in case you wondered. http://tikiwiki.svn.sourceforge.net/viewvc/tikiwiki?view=revision&revision=24792

ALTER TABLE `tiki_file_galleries` ADD COLUMN  `show_lastDownload` char(1) default NULL AFTER `show_hits`;
