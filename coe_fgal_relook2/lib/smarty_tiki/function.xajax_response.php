<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_function_xajax_response($params, &$smarty) {
	$return = '';

	if ( isset($params['content']) && $params['content'] != '' ) {

		global $smarty, $ajaxlib;
		include_once('lib/ajax/xajax.inc.php');

		if ( $ajaxlib && $ajaxlib->canProcessRequests() ) {
			$objResponse = new xajaxResponse();
			$objResponse->assign('tiki-center', 'innerHTML', $params['content']);
			$return = $objResponse->getOutput();
			unset($objResponse);
		}
	}

	return $return;
}
