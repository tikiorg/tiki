<?php
/**
 * sets some constants for use in connecting to Third Party libraries.
 * 
 * this script may only be included, it will die if called directly.
 *
 * @package TikiWiki
 * @subpackage lib\setup
 * @copyright (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project. All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * @licence Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
 */
// $Id$

// die if called directly.
if ( basename($_SERVER['SCRIPT_NAME']) == basename(__FILE__) ) {
  header("location: index.php");
  exit;
}

// The following constant is at least used in the release.php script and in the error handling functions
define('THIRD_PARTY_LIBS_PATTERN', '#(lib/pear|lib/ajax|vendor)#');

define('TIKI_SMARTY_DIR', 'lib/smarty_tiki/');
// add a line like the following in db/local.php to use an external smarty installation: $smarty_path='/usr/share/php/smarty/'
if ( isset($smarty_path) && $smarty_path != '' && file_exists($smarty_path.'Smarty.class.php') ) define('SMARTY_DIR', $smarty_path);
