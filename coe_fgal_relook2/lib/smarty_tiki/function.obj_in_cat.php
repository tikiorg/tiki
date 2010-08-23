<?php
/**
* Smarty function plugin
* -------------------------------------------------------------
* Type:     	function
* Name:     	obj_in_cat
* Author:   	Enmore Services
* Purpose:  	returns true if an object is in a category
* Parameters:	all 3 parameters are mandatory
*				object is reference to the specific object to be tested eg object=$page
*				type is the content type eg type='wiki page'
*				catnumber is the category Id# eg catnumber=3
* -------------------------------------------------------------
*/
//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
  header('location: index.php');
  exit;
}

function smarty_function_obj_in_cat($params, &$smarty) 
{					
	global $categlib;
	include_once ('lib/categories/categlib.php');
	extract ($params, EXTR_SKIP);
	if	( !isset($object) ) {
		return ('<b>missing object parameter for Smarty function testing whether object is in a category</b><br/>');
	}
	
	if	( !isset($type) ) {
		return ('<b>missing type parameter for Smarty function testing whether object is in a category</b><br/>');
	}	
	
	if	( !isset($catnumber) ) {
		return ('<b>missing catnumber parameter for Smarty function testing whether object is in a category</b><br/>');
	}

	$categories = $categlib->get_object_categories($type, $object);
	$result = false;
	
	foreach ($categories as $cat) {	
		if ($cat == $catnumber) {									
			$result = true;	
			$smarty->assign('obj_in_cat', $result);
			return;
		}
	}
	$smarty->assign('obj_in_cat', $result);
}	
