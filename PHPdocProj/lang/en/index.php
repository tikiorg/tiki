<?php
/**
 * This redirects to the site's root to prevent directory browsing.
 *
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
 *
 * @package Tikiwiki\lang
 * @subpackage en
 * @copyright (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
 */
// $Id$

header('location: ../../tiki-index.php');
die;
