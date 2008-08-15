<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_function_editform($params, &$smarty) {
	global $prefs, $base_url;

	include_once 'lib/tikifck.php';
	if (!isset($params['InstanceName']))       $params['InstanceName'] = 'fckedit';
	$fcked = new TikiFCK($params['InstanceName']);
	if (isset($params['Meat']))       $fcked->Meat = $params['Meat'];
	if (isset($params['Width']))      $fcked->Width = $params['Width'];
	if (isset($params['Height']))     $fcked->Height = $params['Height'];
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
	$fcked->Config['CustomConfigurationsPath'] = $base_url.'setup_fckeditor.php';
	echo $fcked->CreateHtml();
}

?>
