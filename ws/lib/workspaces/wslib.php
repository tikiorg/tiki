<?php
/** \file
 * $Id: /cvsroot/tikiwiki/tiki/lib/workspaces/wslib.php by MangaPowerX
 *
 * \brief Workspaces support class
 *
 */

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

include_once ('lib/categories/categlib.php');
include_once ('tiki_admin.php');

class Workspacelib {

	function init_ws ($newName)
	{
		if (!$categlib->get_category_id($newName)=NULL)
		{
			$categlib->add_category(0,$newName,'Workspaces Container Category');
			$wsContainer = $categlib->get_category_id($newName);
			return $wsContainer;
		}
		else
			return -1;
				
	}

}
