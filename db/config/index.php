<?php
/**
 * This redirects to the site's root to prevent directory browsing.
 *  
 * @ignore 
 * @package TikiWiki
 * @subpackage db
 * @copyright (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
 * @licence Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
 */
// old ID 44694, timestamp removed $

// This redirects to the site's root to prevent directory browsing
header("location: ../../tiki-index.php");
die;
