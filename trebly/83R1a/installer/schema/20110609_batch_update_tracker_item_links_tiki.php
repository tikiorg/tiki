<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: 20110609_batch_update_tracker_item_links_tiki.php 36250 2011-08-16 21:06:07Z changi67 $

if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function upgrade_20110609_batch_update_tracker_item_links_tiki($installer)
{
	$fields = $installer->fetchMap("SELECT fieldId, options FROM tiki_tracker_fields WHERE type = 'r'");

	foreach ($fields as $fieldId => $options) {
		$options = explode(',', $options);

		if (isset($options[1])) {
			$remoteFieldId = $options[1];

			$installer->query("
				UPDATE tiki_tracker_item_fields r
					INNER JOIN tiki_tracker_item_fields l ON r.value = l.value AND l.fieldId = ?
					SET r.value = l.itemId
					WHERE r.fieldId = ?
			", array($remoteFieldId, $fieldId));
		}
	}
}

