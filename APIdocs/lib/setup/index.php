<?php
/**
 * This redirects to the site's root to prevent directory browsing.
 *
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 *
 * @package TikiWiki\lib
 * @subpackage setup
 * @ignore
 * @copyright (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
 * @licence Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
 */
// $Id: index.php 40086 2012-03-08 15:47:37Z changi67 $

header("location: ../../index.php");
die;
