<?php

// Load configuration of the Galaxia Workflow Engine
include_once (dirname(__FILE__) . '/config.php');

include_once (GALAXIA_LIBRARY.'/src/ProcessMonitor/ProcessMonitor.php');

$processMonitor = new ProcessMonitor($dbGalaxia);

?>
