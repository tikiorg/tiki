<?php
include_once('lib/Galaxia/src/common/Observable.php');  
include_once('lib/Galaxia/src/common/Observer.php');  
include_once('lib/Galaxia/src/Observers/Logger.php');  
include_once('lib/Galaxia/src/API/Base.php');  
include_once('lib/Galaxia/src/API/BaseActivity.php');  
include_once('lib/Galaxia/src/API/Process.php');  
include_once('lib/Galaxia/src/API/Instance.php');  
include_once('lib/Galaxia/src/API/activities/Activity.php');  
include_once('lib/Galaxia/src/API/activities/Start.php');  
include_once('lib/Galaxia/src/API/activities/End.php');  
include_once('lib/Galaxia/src/API/activities/Standalone.php');  
include_once('lib/Galaxia/src/API/activities/Start.php');  
include_once('lib/Galaxia/src/API/activities/End.php');  
include_once('lib/Galaxia/src/API/activities/SwitchActivity.php');  
include_once('lib/Galaxia/src/API/activities/Split.php');  
include_once('lib/Galaxia/src/API/activities/Join.php');  
include_once('lib/Galaxia/src/API/Instance.php');  
$baseActivity = new BaseActivity($dbTiki);
$process = new Process($dbTiki);
$instance = new Instance($dbTiki);
?>