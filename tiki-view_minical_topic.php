<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-view_minical_topic.php,v 1.3 2003-08-07 04:33:57 rossta Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/minical/minicallib.php');

if (!$user)
	die;

if (!isset($_REQUEST["topicId"])) {
	die;
}

$info = $minicallib->minical_get_topic($user, $_REQUEST["topicId"]);
$type = &$info["filetype"];
$file = &$info["filename"];
$content = &$info["data"];
header ("Content-type: $type");
header ("Content-Disposition: inline; filename=$file");
echo "$content";

?>