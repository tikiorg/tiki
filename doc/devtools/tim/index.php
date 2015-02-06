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
// $Id: index.php 44694 2013-01-30 20:55:37Z marclaporte $

// This redirects to the sites root to prevent directory browsing
header("location: ../tiki-index.php");
die;
