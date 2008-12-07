<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

function smarty_function_autosave($params, &$smarty)
{
	global $user;
	global $js_script;
	$user_agent = $_SERVER['HTTP_USER_AGENT'];
	$user_ip = $_SERVER['REMOTE_ADDR'];
	$request_uri = $_SERVER['REQUEST_URI'];
	$file_name = md5("$user:$user_ip:$request_uri:".$params['id']);
	$js_script[$params['id']] = "register_id('".$params['id']."');";
	$smarty->assign('autosave_msg',$file_name." "."$user:$user_ip:$request_uri:".$params['id']);
	if (file_exists("temp/cache/wiki-$file_name") and $params['preview'] == 0) {
		$tmp = str_replace("\n","\r\n",file_get_contents("temp/cache/wiki-$file_name"));
		if ( $params['test'] == 'y' and $tmp == $params['default'] ) {
			$smarty->assign('has_autosave','n');
		} else {
			$smarty->assign('has_autosave','y');
		}
		return $tmp;
	} else {
		$smarty->assign('has_autosave','n');
		return $params['default'];	
	}
}
