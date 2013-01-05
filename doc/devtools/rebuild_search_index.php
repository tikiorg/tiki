<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$
//
// from http://dev.tiki.org/Unified+Search#Cron_job

include_once('tiki-setup.php');
require_once 'lib/search/searchlib-unified.php';

$loggit = (isset($_SERVER['argv'][1]) && $_SERVER['argv'][1] === 'loggit');

$unifiedsearchlib->rebuild($loggit);
