UPDATE IGNORE `tiki_preferences` SET `value`=REPLACE(`value`, 'tikiwiki.org', 'tiki.org') WHERE `name`='profile_sources';
