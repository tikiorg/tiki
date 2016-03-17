<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/*
This patch will TRUNCATE the tiki_objects.itemId and name values so they don't exceed the maximum allowed page name length
The current maximum allowed page name length = 158 characters.
However, previously pages could be the full 160 characters. Because there was no bounds checking.
For pages that don't use tiki_objects, the save would work.

So, this script should truncate to 160 characters (max pageName attribute length), instead of the new 158 char max.

Errors in long names in tiki_objects for wiki page names, is caused by the difference in varchar length.
tiki_pages.pageName 	varchar(160)
tiki_objects.itemId		varchar(255)
tiki_objects.name		varchar(200)
Previously there was no check on the page name length, causing the maximum possible number of characters to be stored.
This again caused pagename comparisons to fail and an incorrect href for tiki_objects.
Pages linked to categories are affected.

New pagenames are now truncated to 158 characters in the code, thus new pages will behave correctly.

This script fixes existing records by
1) Shortening the max page name (ItemId / name) length
2) Rebuilding the href value
*/
function upgrade_20130809_limit_name_lengths_in_objects_tiki($installer)
{
	$max_pagename_length = 160;

	// Fix tiki_objects
	///////////////////////////

	// Find all records with long pagenames
	$query = 'SELECT objectId, itemId, name, href FROM tiki_objects where type = "wiki page" and  length(itemId) > ?';
	$results = $installer->query($query, array($max_pagename_length));
	if ($results) {
		$newValues = array();
		while ($row = $results->fetchRow()) {
			// Update the page name
			$itemId = substr($row['itemId'], 0, $max_pagename_length);
			$name = substr($row['name'], 0, $max_pagename_length);
			// Update the URL
			$href = "tiki-index.php?page=".urlencode($itemId);
			$objectId = intval($row['objectId']);

			// Build the query parameters
			$newValues[] = array($itemId, $name, $href, $objectId);
		}

		// Update the database record
		$query = "update tiki_objects set itemId = ?, name=?, href=? where objectId = ?";
		foreach ($newValues as $newVal) {
			$installer->query($query, $newVal);
		}
	}
}
