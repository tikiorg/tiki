<?php
/**
 * This redirects to the site's root to prevent directory browsing.
 *
 * @ignore
 * @package    Tiki
 * @copyright  (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
 * @license    LGPLv2.1. See license.txt for more details
 */
// $Id$

// redirect
header("location: ../../index.php");
die;
