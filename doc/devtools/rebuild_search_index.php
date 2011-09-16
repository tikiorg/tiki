<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: release.php 36090 2011-08-11 20:31:15Z sept_7 $
//
// from http://dev.tiki.org/Unified+Search#Cron_job

include_once('tiki-setup.php');
require_once 'lib/search/searchlib-unified.php';
$unifiedsearchlib->rebuild();
