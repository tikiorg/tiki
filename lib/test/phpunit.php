<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
* A simple wrapper around /usr/bin/phpunit to enable debugging
* tests with Xdebug from within Aptana or Eclipse.
* 
* Linux only (it should be simple to add support to other OSs).
*/

require_once('/usr/bin/phpunit');
