<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

/**
 * Smarty escape modifier plugin
 *
 * Type:     modifier<br>
 * Name:     escape<br>
 * Purpose:  Escape the string according to escapement type
 * @link http://smarty.php.net/manual/en/language.modifier.escape.php
 *          escape (Smarty online manual)
 * @author   Monte Ohrt <monte at ohrt dot com>
 * @param html|htmlall|url|quotes|hex|hexentity|javascript
 * @param string $esc_type
 * @param string $char_set
 * @param bool $double_encode
 * @return string
 */
function smarty_modifier_escape($string, $esc_type = 'html', $char_set = 'UTF-8', $double_encode = true)
{
	switch ($esc_type) {
		case 'html':
			if (is_array($string)) {
				$string = implode(',', $string);
			}
			$return = htmlspecialchars($string, ENT_QUOTES, $char_set, $double_encode);
			// Convert back sanitization tags into real tags to avoid them to be displayed
			$return = str_replace('&lt;x&gt;', '<x>', $return);
			// Convert back sanitization tags into real tags for no wrap space
			$return = str_replace('&amp;nbsp;', '&nbsp;', $return);
			return $return;

		case 'htmlall':
			$return = htmlentities($string, ENT_QUOTES, $char_set);
			if (!strlen($return) && strlen($string)) // Bug php when there is non utf8 characters in the string(http://bugs.php.net/bug.php?id=43549, http://bugs.php.net/bug.php?id=43294)
				$return = htmlentities($string, ENT_QUOTES);
			// Convert back sanitization tags into real tags to avoid them to be displayed
			$return = str_replace('&lt;x&gt;', '<x>', $return);
			return $return;

		case 'url':
			return rawurlencode($string);

		case 'urlpathinfo':
			return str_replace('%2F', '/', rawurlencode($string));

		case 'quotes':
			// escape unescaped single quotes
			return preg_replace("%(?<!\\\\)'%", "\\'", $string);

		case 'hex':
			// escape every character into hex
			$return = '';
			for ($x=0, $xstrlen_string=strlen($string); $x < $xstrlen_string; $x++) {
				$return .= '%' . bin2hex($string[$x]);
			}
			return $return;

		case 'hexentity':
			$return = '';
			for ($x=0, $x_strlen_string = strlen($string); $x < $x_strlen_string; $x++) {
				$return .= '&#x' . bin2hex($string[$x]) . ';';
			}
			return $return;

		case 'decentity':
			$return = '';
			for ($x=0, $x_strlen_string = strlen($string); $x < $x_strlen_string; $x++) {
				$return .= '&#' . ord($string[$x]) . ';';
			}
			return $return;

		case 'javascript':
			// escape quotes and backslashes, newlines, etc.
			return strtr($string, array('\\'=>'\\\\', "'"=>"\\'", '"'=>'\\"', "\r"=>'\\r', "\n"=>'\\n', '</'=>'<\/'));

		case 'mail':
			// safe way to display e-mail address on a web page
			return str_replace(array('@', '.'), array(' [AT] ', ' [DOT] '), $string);

		case 'nonstd':
			// escape non-standard chars, such as ms document quotes
			$_res = '';
			for ($_i = 0, $_len = strlen($string); $_i < $_len; $_i++) {
				$_ord = ord(substr($string, $_i, 1));
				// non-standard char, escape it
				if ($_ord >= 126) {
					$_res .= '&#' . $_ord . ';';
				} else {
					$_res .= substr($string, $_i, 1);
				}
			}
			return $_res;

		case 'unescape':
			return rawurldecode($string);

		default:
			return $string;
	}
}
