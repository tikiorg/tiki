<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

require_once("lib/ajax/autosave.php");

function smarty_function_autosave($params, &$smarty)
{
	global $user;
	global $js_script;

	$js_script[$params['id']] = "register_id('".$params['id']."');";
	/*$smarty->assign('autosave_msg',$file_name." "."$user:$user_ip:$request_uri:".$params['id']);*/
	if (has_autosave($params['id']) and $params['preview'] == 0) {
		$tmp = str_replace("\n","\r\n",get_autosave($params['id']));
		if ( isset($params['test']) && isset($params['default']) && $params['test'] == 'y' and $tmp == $params['default'] ) {
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
