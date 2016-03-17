<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
//TODO Use a pref to handle the list
if (!empty($tikiMonitorRestriction)) {
	if (is_array($tikiMonitorRestriction)) {
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$aListIp = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
			$sIpToCheck = $aListIp[0];
		} elseif (isset($_SERVER['REMOTE_ADDR']) && !empty($_SERVER['REMOTE_ADDR'])) {
			$sIpToCheck = $_SERVER['REMOTE_ADDR'];
		} else {
			$sIpToCheck = null;
		}
		if (in_array($sIpToCheck, $tikiMonitorRestriction) === false) {
			header('location: index.php');
		}
	} else {
		echo tra("\$tikiMonitorRestriction need to be an array");
		exit;
	}
}
$opcode_stats = TikiLib::lib('admin')->getOpcodeCacheStatus();

# TODO: The results will be wrong for WinCache
# The following is the relevant snippet from
# admin/include_performance.php
$txtUsed = tr('Used');
$txtAvailable = tr('Available');
if ($opcode_cache == 'WinCache') {
	// Somehow WinCache seems to flip the representations
	$txtAvailable = tr('Used');
	$txtUsed = tr('Available');
}

$result['OPCodeCache'] = $opcode_stats['opcode_cache'];
$result['OpCodeStats'] = $opcode_stats;

include_once ('installer/installlib.php');
$installer = new Installer;
$result['DbRequiresUpdate'] = $installer->requiresUpdate();

$result['SearchIndexRebuildLast'] = $tikilib->get_preference('unified_last_rebuild');

$display = json_encode($result);
echo $display;
