<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function upgrade_20100507_flash_banner_tiki( $installer ) {
	$result = $installer->query( 'select * from `tiki_banners` where `which` = ? and `HTMLData` like ?', array('useFlash', '%embedSWF%'));
	$query = 'update `tiki_banners` set `HTMLData`=? where `bannerId`=?';
	while( $res = $result->fetchRow() ) {
		if (preg_match('/(swfobject|SWFFix)\.embedSWF\([\'" ]*([^,\'"]*)[\'" ]*,[\'" ]*([^,\'"]*)[\'" ]*,[\'" ]*([^,\'"]*)[\'" ]*,[\'" ]*([^,\'"]*)[\'" ]*,[\'" ]*([^,\'"]*)[\'" ]*,[\'" ]*([^,\'"]*)[\'" ]*/m', $res['HTMLData'], $matches)) {
			$movie['movie'] = $matches[2];
			$movie['width'] = $matches[4];
			$movie['height'] = $matches[5];
			$movie['version'] = $matches[6];
			$installer->query( $query, array(serialize($movie), $res['bannerId']));
		}
	}
}
