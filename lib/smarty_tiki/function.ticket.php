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

/**
 * Function to return HTML for including a token in a form or in a query string
 *
 * @param $params		Set mode=get in order to return HTML for a query, otherwise HTML for a form will be returned
 * @param $smarty
 * @return string
 */
function smarty_function_ticket($params, $smarty)
{
	if (!empty($params['mode']) && $params['mode'] === 'get') {
		return '&amp;ticket=' . htmlspecialchars($smarty->getTemplateVars('ticket')) . '&amp;daconfirm=y';
	} else {
		return '<input type="hidden" name="ticket" value="' . urlencode($smarty->getTemplateVars('ticket')) . '">'
			. '<input type="hidden" name="daconfirm" value="y">';
	}
}
