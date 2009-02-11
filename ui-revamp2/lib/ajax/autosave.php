<?php
//this script may only be included - so its better to die if called directly.
global $access;
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

global $prefs;
if ($prefs['feature_ajax'] != 'y' || $prefs['feature_ajax_autosave'] != 'y') {
	return;
}

$ajaxlib->registerFunction('auto_save');
$ajaxlib->registerFunction('remove_save');
if (isset($_REQUEST['noautosave'])) {
	$smarty->assign('noautosave',$_REQUEST['noautosave']=='y');
}

function auto_save($id, $data, $referer = "") {
	global $user,$tikilib;
	$user_agent = $_SERVER['HTTP_USER_AGENT'];
	$user_ip = $tikilib->get_ip_address();
	if ($referer == "") {
		$referer = preg_replace("/(\?|\&)noautosave=y/","",$_SERVER['REQUEST_URI']);
	}
	$file_name = md5("$user:$user_ip:$referer:$id");

	file_put_contents("temp/cache/wiki-$file_name",rawurldecode($data));
//	file_put_contents("temp/cache/log-$file_name","$user:$user_ip:$referer:$id");
	return new xajaxResponse();
}

function remove_save($id) {
	global $user;
	$user_agent = $_SERVER['HTTP_USER_AGENT'];
	$user_ip = $tikilib->get_ip_address();
	$request_uri = preg_replace("/(\?|\&)noautosave=y/","",$_SERVER['REQUEST_URI']);
	$file_name = md5("$user:$user_ip:$request_uri:$id");
	if (file_exists("temp/cache/wiki-$file_name")) {
		unlink("temp/cache/wiki-$file_name");
//		file_put_contents("temp/cache/log_del-$file_name","$user:$user_ip:$referer:$id");
//	} else {
//		file_put_contents("temp/cache/log_nodel-$file_name","$user:$user_ip:$referer:$id");
	}
	return new xajaxResponse();
}

function has_autosave($id) {
	global $user;
	$user_agent = $_SERVER['HTTP_USER_AGENT'];
	$user_ip = $tikilib->get_ip_address();
	$request_uri = preg_replace("/(?|&)noautosave='y'/","",$_SERVER['REQUEST_URI']);
	$file_name = md5("$user:$user_ip:$request_uri:$id");
	
	return file_exists("temp/cache/wiki-$file_name");
}

function get_autosave($id) {
	global $user;
	$user_agent = $_SERVER['HTTP_USER_AGENT'];
	$user_ip = $tikilib->get_ip_address();
	$request_uri = preg_replace("/(?|&)noautosave='y'/","",$_SERVER['REQUEST_URI']);
	$file_name = md5("$user:$user_ip:$request_uri:$id");
	if (file_exists("temp/cache/wiki-$file_name")) {
		return file_get_contents("temp/cache/wiki-$file_name");
	} else {
		return "";
	}
}
