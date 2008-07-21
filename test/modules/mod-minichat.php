<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}


if (isset($module_params["channels"])) {
    $channels=explode(',', $module_params["channels"]);
} else $channels=array('default');

if (isset($_SESSION['minichat_channels'])) {
	$channels=$_SESSION['minichat_channels'];
}

$jscode='';
foreach($channels as $k => $channel) {
	$channel='#'.preg_replace('/[^a-zA-Z0-9\-\_]/i','',$channel);
	$channel=substr($channel, 0, 30);
	$channels[$k]=$channel;

	$jscode.="minichat_addchannel('".$channel."');\n";
}

$smarty->assign('jscode', $jscode);
$smarty->assign('module_rows', $module_rows*10);

?>