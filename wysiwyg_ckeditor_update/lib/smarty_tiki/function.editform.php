<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_function_editform($params, &$smarty) {
	global $prefs, $url_path;

	include_once 'lib/tikifck.php';
	if (!isset($params['InstanceName']))       $params['InstanceName'] = 'fckedit';
	$fcked = new TikiFCK($params['InstanceName']);
	if (isset($params['Meat']))       $fcked->Meat = $params['Meat'];
	if (isset($params['Width']))      $fcked->Width = $params['Width'];
	if (isset($params['Height']))     $fcked->Height = $params['Height'];
	if ($prefs['feature_ajax'] == 'y' && $prefs['ajax_autosave'] == 'y') {
		$fcked->Config['autoSaveSelf'] = htmlentities($_SERVER['REQUEST_URI']);
	}
	if (isset($params['ToolbarSet'])) {
		$fcked->ToolbarSet = $params['ToolbarSet'];
	} else {
		$fcked->ToolbarSet = 'Tiki';
	}
	if ($prefs['feature_detect_language'] == 'y') {
		$fcked->Config['AutoDetectLanguage'] = true;
	} else {
		$fcked->Config['AutoDetectLanguage'] = false;
	}
	$fcked->Config['DefaultLanguage'] = $prefs['language'];
	$fcked->Config['CustomConfigurationsPath'] = $url_path.'setup_fckeditor.php';
	echo $fcked->CreateHtml();
}
