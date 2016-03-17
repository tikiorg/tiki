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
 * Smarty {assign} compiler function plugin
 *
 * Type:     compiler function<br>
 * Name:     assign<br>
 * Purpose:  assign a value to a template variable
 * @link http://smarty.php.net/manual/en/language.custom.functions.php#LANGUAGE.FUNCTION.ASSIGN {assign}
 *       (Smarty online manual)
 * @author Monte Ohrt <monte at ohrt dot com> (initial author)
 * @author messju mohr <messju at lammfellpuschen dot de> (conversion to compiler function)
 * @param string containing var-attribute and value-attribute
 * @param Smarty_Compiler
 */
function smarty_compiler_assign($tag_attrs, $compiler)
{
	
	$_params = $compiler->_parse_attrs($tag_attrs);

	if (!isset($_params['var'])) {
		$compiler->_syntax_error("assign: missing 'var' parameter", E_USER_WARNING);
		return;
	}

	if (!isset($_params['value'])) {
		$compiler->_syntax_error("assign: missing 'value' parameter", E_USER_WARNING);
		return;
	}

	// Handle assign value in array
	//   It transforms the string 'myarray.foo.bar' (the string includes the quotes) into array('myarray', 'foo', 'bar')
	//   Example: {assign var='myarray.foo.bar' value='example'}
	//     will put the 'example' value into $myarray['foo']['bar']
	//     and will be simply available in smarty as $myarray.foo.bar
	//
	if ( strpos($_params['var'], '.') !== false ) {
		//FIXME
		return "\$this->_tpl_vars[".str_replace('.', "']['", $_params['var'])."] = {$_params['value']};";
	}

	return "\$this->assign({$_params['var']}, {$_params['value']});";
}
