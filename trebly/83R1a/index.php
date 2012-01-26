<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: index.php 37840 2011-10-01 10:23:51Z changi67 $

require_once ('tiki-setup.php');
if ( ! headers_sent() ) {
	header('location: '.$prefs['tikiIndex']);
} else {
	die("header already sent");
}
