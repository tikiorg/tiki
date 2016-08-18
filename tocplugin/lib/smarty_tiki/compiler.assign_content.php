<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/*
* Smarty plugin
* -------------------------------------------------------------
* File: compiler.assign_content.php
* Type: compiler
* Name: assign_content
* Purpose: assign a value from a dynamic content to a template variable
* Parameters: var [required] - name of the template variable
* id [optional] - id of the dynamic content
* label [optional] - label of the dynamic content
* -------------------------------------------------------------
*/

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_compiler_assign_content($tag_attrs, $compiler)
{
	$_params = $compiler->_parse_attrs($tag_attrs);

	if ( ! isset($_params['var']) ) {
		$compiler->_syntax_error("assign: missing 'var' parameter", E_USER_WARNING);
		return;
	}

	$func_name = 'content';
	if ( ! isset($_params['id']) && ! isset($_params['label']) ) {
		$_params['id'] = 0;
		$func_name = 'rcontent';
	}

	$str_params = 'array('
		.( isset($_params['id']) ? "'id' => ".$_params['id'].', ' : '' )
		.( isset($_params['label']) ? "'label' => ".$_params['label'].', ' : '' )
		.')';

	return "include_once('lib/smarty_tiki/function.$func_name.php');\n"
		.'$this->assign('.$_params['var'].", smarty_function_$func_name(".$str_params.', $this));';
}
