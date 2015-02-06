<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/* {set var=$name value=$value}
 * do the same than assign but accept a varaible as var name
 */
function smarty_function_set($params, $smarty)
{
	$smarty->assign($params['var'], $params['value']);
}
