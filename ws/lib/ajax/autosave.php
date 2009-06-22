<?php
//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

global $prefs;
if ($prefs['feature_ajax'] != 'y' || $prefs['feature_ajax_autosave'] != 'y') {
	return;
}

$ajaxlib->registerFunction('auto_save');
$ajaxlib->registerFunction('remove_save');
if (isset($_REQUEST['noautosave'])) {
	$smarty->assign('noautosave',$_REQUEST['noautosave']=='y');
}

function auto_save_name($id, $request = "") {
	global $user;
	$user_agent = $_SERVER['HTTP_USER_AGENT'];
	$referer = preg_replace("/(\?|\&)noautosave=y/","",$request != "" ? $request : $_SERVER['REQUEST_URI']);
	return "temp/cache/wiki-".md5("$user:$referer:$id");
}
function auto_save_log($id, $request = "") {
	global $user;
	$user_agent = $_SERVER['HTTP_USER_AGENT'];
	$referer = preg_replace("/(\?|\&)noautosave=y/","",$request != "" ? $request : $_SERVER['REQUEST_URI']);
	file_put_contents("temp/cache/log-".md5("$user:$referer:$id"),"$user:$referer:$id");
}

function auto_save($id, $data, $referer = "") {
	//auto_save_log($id, $referer);
	file_put_contents(auto_save_name($id, $referer),rawurldecode($data));
	return new xajaxResponse();
}

function remove_save($id) {
	$file_name = auto_save_name($id);
	if (file_exists($file_name)) {
		unlink($file_name);
	}
	return new xajaxResponse();
}

function has_autosave($id) {
	return file_exists(auto_save_name($id));
}

function get_autosave($id) {
	$file_name = auto_save_name($id);
	if (file_exists($file_name)) {
		return file_get_contents($file_name);
	} else {
		return "";
	}
}
