<?php
include_once('lib/Galaxia/src/common/Observable.php');  
include_once('lib/Galaxia/src/common/Observer.php');  
include_once('lib/Galaxia/src/Observers/Logger.php');  
include_once('lib/Galaxia/src/ProcessManager/BaseManager.php');  
include_once('lib/Galaxia/src/ProcessManager/ProcessManager.php');  
include_once('lib/Galaxia/src/ProcessManager/InstanceManager.php');  
include_once('lib/Galaxia/src/ProcessManager/RoleManager.php');  
include_once('lib/Galaxia/src/ProcessManager/ActivityManager.php'); 
include_once('lib/Galaxia/src/ProcessManager/GraphViz.php'); 

/// $RoleManager is the object that will be used to manipulate roles.
$roleManager = new RoleManager($dbTiki);
/// $activityManager is the object that will be used to manipulate activities.
$activityManager = new ActivityManager($dbTiki);
/// $ProcessManager is the object that will be used to manipulate processes
$processManager = new ProcessManager($dbTiki);
///
$instanceManager = new InstanceManager($dbTiki);

//$logger = new Logger('lib/Galaxia/log/pm.log');
//$processManager->attach_all($logger);
//$activityManager->attach_all($logger);
//$roleManager->attach_all($logger);

$smarty->assign('is_active_help',tra('indicates if the process is active. Invalid processes cant be active'));
?>