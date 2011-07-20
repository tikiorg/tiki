<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty substring modifier plugin
 *
 * Type:     modifier<br>
 * Name:     substring<br>
 * Purpose:  Returns a substring of string.  Same arguments as
 *           PHP substr function.
 * @link based on substr(): http://www.zend.com/manual/function.substr.php
 * @author   Mike Kerr <tiki.kerrnel at kerris dot com>
 * @param string
 * @param position: start position of substring (default=0, negative starts N from end)
 * @param length: length of substring (default=to end of string; negative=left N from end)
 * @return string
 */
function smarty_modifier_substring($string, $position = 0, $length = null) {

	if ($length == null) {
		return substr($string, $position);
	} else {
		return substr($string, $position, $length);
	}
}
