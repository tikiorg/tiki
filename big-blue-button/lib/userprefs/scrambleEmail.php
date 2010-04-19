<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
*	scramble an email with a method
*	@param string email emil to be scrambled
* 	@param string method=unicode or y: each character is replaced with the unicode value
* 	@param string method=strtr: mr@tw.org -> mr AT tw DOT org
* 	@param string method=x: mr@tw.org -> mr@xxxxxx
*	@return string scrambled email
*/

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function scrambleEmail($email, $method='unicode')
{
	switch ($method) {
	case 'strtr':
		$trans = array(	"@" => tra("(AT)"),
						"." => tra("(DOT)")
		);
		return strtr($email, $trans);
	case 'x' :
		$encoded = $email;
		for ($i = strpos($email, "@") + 1, $istrlen_email = strlen($email); $i < $istrlen_email; $i++) {
			if ($encoded[$i]  != ".") $encoded[$i] = 'x';
		}
		return $encoded;
	case 'unicode':
	case 'y':// for previous compatibility
		$encoded = '';
		for ($i = 0, $istrlen_email = strlen($email); $i < $istrlen_email; $i++) {
			$encoded .= '&#' . ord($email[$i]). ';';
		}
		return $encoded;
	case 'n':
	default:
		return $email;
	}
}
