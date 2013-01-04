<?php
/**
 * This redirects to the site's root to prevent directory browsing. 
 *
 * @package \
 * @copyright (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project. All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * @licence Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
 */
// $Id: index.php 44237 2012-12-11 10:29:41Z changi67 $

require_once ('tiki-setup.php');
if ( ! headers_sent($header_file, $header_line) ) {
	// rfc2616 wants this to have an absolute URI
	header('Location: '.$base_url.$prefs['tikiIndex']);
} else {
	echo "Header already sent in ".$header_file." at line ".$header_line;
	exit();
}
