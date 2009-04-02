<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/*
 * smarty_function_cookie_jar: Get a cookie value from the Tiki Cookie Jar
 *
 * params:
 *	- name: Name of the cookie
 */
function smarty_function_cookie_jar($params, &$smarty) {
	if ( empty($params['name']) ) return;
	return getCookie($params['name']);
}
