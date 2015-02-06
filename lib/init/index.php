<?php
/**
 * This redirects to the site's root to prevent directory browsing.
 *
 * @ignore
 * @package TikiWiki
 * @subpackage lib\init
 * @copyright (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
 * @licence Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
 */
// $Id$

header("location: ../index.php");
die;
