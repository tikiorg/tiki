<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_function_bootstrap_modal( $params, $smarty )
{
	$smarty->loadPlugin('smarty_function_service');
	$params['modal'] = 1;
	$href = smarty_function_service($params, $smarty);
	return "$href\" data-toggle=\"modal\" data-backdrop=\"static\" data-target=\".modal.fade:not(.in):first";
}
