<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id $

if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function upgrade_20101210_fgal_add_wiki_attachments_tiki( $installer )
{
	global $dbs_tiki;

	// Add wiki attachments file Gallery (feature_use_fgal_for_wiki_attachments)
	$installer->query("INSERT INTO `tiki_file_galleries` (`name`, `type`, `description`, `visible`, `user`, `public`, `parentId`) VALUES ( 'Wiki Attachments', 'system', '', 'y', 'admin', 'y', -1)");
	// Search last insert id
	$result = $installer->getOne("SELECT `galleryId` FROM `tiki_file_galleries` WHERE `name` = 'Wiki Attachments' and `type`='system'");
	
	if($result != 0){
		$galleryId = $result['galleryId'];
		$installer->query("INSERT INTO `tiki_preferences` (`name`, `value`) VALUES ('fgal_root_wiki_attachments_id', '".$galleryId."' );");	
	}
}
