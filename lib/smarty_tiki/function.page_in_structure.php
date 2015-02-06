<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
* Smarty function plugin
* -------------------------------------------------------------
* Type:     	function
* Name:     	page_in_structure
* Purpose:  	returns true if a pag eis in a structure
* Parameters:	pagechecked - mandatory
* -------------------------------------------------------------
*/
//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
  header('location: index.php');
  exit;
}

function smarty_function_page_in_structure($params, $smarty)
{					
	$structlib = TikiLib::lib('struct');
	extract($params, EXTR_SKIP);
	
	if ( !isset($pagechecked) ) {
		return ('<b>missing pagechecked parameter for Smarty function testing whether page is in a structure</b><br/>');
	}
	
	if ($structlib->page_is_in_structure($pagechecked)) {									
		$result = true;	
		$smarty->assign('page_in_structure', $result);
		return;
	}
	$result = false;
	$smarty->assign('page_in_structure', $result);
}	
