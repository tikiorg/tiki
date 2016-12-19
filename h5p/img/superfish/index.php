<?php
/**
 * This redirects to the site's root to prevent directory browsing.
 *  
 * @ignore 
 * @package TikiWiki
 * @subpackage doc
 * @copyright (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
 * @licence Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
 */
// $Id: index.php 53806 2015-02-06 00:56:02Z jyhem $

header("location: ../tiki-index.php");
die;
