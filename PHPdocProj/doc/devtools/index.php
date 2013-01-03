<?php
/**
 * This redirects to the site's root to prevent directory browsing. 
 *
 * @package Tikiwiki\doc
 * @subpackage devtools
 * @copyright (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project. All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * @licence Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
 */
// $Id$

header("location: ../../tiki-index.php");
die;
