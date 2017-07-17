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

/**
 * Backlinks for tracker Files field attachments to their respective items
 * were not being recorded until r63258. This script adds missing backlinks.
 * @param $installer
 */
function upgrade_20170717_add_missing_trackeritem_attachment_backlinks_tiki($installer)
{
  $filegal = TikiLib::lib('filegal');
  $files = array();
  $relations = $installer->table('tiki_object_relations');
  $attachments = $relations->fetchAll(['source_itemId', 'target_itemId'], ['relation' => 'tiki.file.attach', 'source_type' => 'trackeritem', 'target_type' => 'file']);
  foreach ($attachments as $rel) {
    $files[$rel['source_itemId']][] = $rel['target_itemId'];
  }
  foreach ($files as $itemId => $fileIds) {
    $context = array('type' => 'trackeritem', 'object' => $itemId);
    $fileIds = array_unique($fileIds);
    $filegal->replaceBacklinks($context, $fileIds);
  }
}
