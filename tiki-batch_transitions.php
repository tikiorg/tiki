<?php
if (empty($argv)) { // can only be used in a cron
	return;
}
include('tiki-setup.php');
include_once('lib/transitionlib.php');
$transitionlib = new TransitionLib();

$transitionlib->batchTransitions();
