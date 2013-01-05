<?php
/**
 * This redirects to the site's root to prevent directory browsing.
 *
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 *
 * @package \lib
 * @subpackage core
 * @ignore
 * @copyright (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
 * @licence Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
 */
// $Id$

header("location: ../../index.php");
die;
