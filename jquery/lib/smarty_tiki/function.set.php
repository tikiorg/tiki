<?php
/* {set var=$name value=$value}
 * do the same than assign but accept a varaible as var name
 */
function smarty_function_set($params, &$smarty) {
	$smarty->assign($params['var'], $params['value']);
}
