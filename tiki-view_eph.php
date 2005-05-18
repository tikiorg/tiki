<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-view_eph.php,v 1.5 2005-05-18 10:59:00 mose Exp $

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/ephemerides/ephlib.php');

if (!isset($_REQUEST["ephId"])) {
	die;
}

$info = $ephlib->get_eph($_REQUEST["ephId"]);
$type = &$info["filetype"];
$file = &$info["filename"];
$content = &$info["data"];

header ("Content-type: $type");
header ("Content-Disposition: inline; filename=$file");
echo "$content";

?>