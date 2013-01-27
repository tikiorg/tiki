<?php
/**
 * This redirects to the site's root to prevent directory browsing.
 *
 * @ignore
 * @package    Tiki
 * @copyright  (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
 * @license    LGPL. See license.txt for more details
 */
// $Id$

header('location: ../tiki-index.php');
die;
