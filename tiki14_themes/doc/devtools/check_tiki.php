#!/usr/bin/php
<?php
$message = '';
$err_state = 0;

function help()
{
	echo <<<EOHELP

Tiki monitoring for Nagios/Icinga/Shinken

Syntax:
php check_tiki.php -u <URL> [-c <check>] [--bccwarn <percent> --bcccrit <percent>] [--sirwarn <seconds> --sircrit <seconds>] [--user <user> --pass <password>]

	-u	URL of tiki-monitor.php to use
	-c	Command to execute, one of:
			bcc
			db
			searchindex
		If left empty, all checks will be performed.

	--bccwarn	Byte Code Cache memory warning percentage
	--bcccrit	Byte Code Cache memory critical percentage
	--sirwarn	Search Index age warning threshold in seconds
	--sircrit	Search Index age critical threshold in seconds
	--user		User for HTTP authentication
	--pass		Password for HTTP authentication

EOHELP;
}

function get_opts()
{
	$short_opts = "u:c:h::";
	$long_opts = array(
		"bccwarn:",
		"bcccrit:",
		"sirwarn:",
		"sircrit:",
		"user:",
		"pass:",
	);
	$options = getopt($short_opts, $long_opts);
	return($options);
}

function get_data($options)
{
	$crl = curl_init();
	$timeout = 5;
	curl_setopt($crl, CURLOPT_URL, $options['u']);
	curl_setopt($crl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($crl, CURLOPT_CONNECTTIMEOUT, $timeout);
	if (!empty($options['user'])) {
		curl_setopt($crl, CURLOPT_USERPWD, $options['user'] . ":" . $options['pass']);
	}
	$ret = curl_exec($crl);
	curl_close($crl);
	$ret = json_decode($ret);
	$ret = get_object_vars($ret);
	return $ret;
}

function update_err_state($new_state, $new_message)
{
	global $err_state, $message;
	$err_state = max($new_state, $err_state);
	if (empty($message)) {
		$message = "$new_message";
	} else {
		$message .= " - $new_message";
	}
}

function report()
{
	global $message, $err_state;
	switch($err_state) {
		case 0:
			$message = "TIKI OK - " .$message;
			break;
		case 1:
			$message = "TIKI WARNING - " .$message;
			break;
		case 2:
			$message = "TIKI CRITICAL - " .$message;
			break;
		case 3:
			$message = "TIKI UNKNOWN - " .$message;
			break;
	}
	fwrite(STDOUT, $message.PHP_EOL);
	exit($err_state);
}

function displayError($message)
{
	echo $message.PHP_EOL;
	exit(3);
}
function check_bcc($data, $options)
{
	global $message;
	if (empty($options['bccwarn']) or empty($options['bcccrit'])) {
		displayError("--bccwarn and --bcccrit need to be set");
	}
	$warn = $options['bccwarn'];
	$crit = $options['bcccrit'];
	if ( $warn > $crit ) {
		displayError("--bcccrit needs to be bigger than --bccwarn");
	}
	$OPCodeCache = $data['OPCodeCache'];
	if (is_null($OPCodeCache)) {
		update_err_state(3, "OpCodeCache: None");
	} else {
		$OPCodeStats = get_object_vars($data['OpCodeStats']);
		$mem_used = $OPCodeStats['memory_used'] * 100;
		if ($mem_used > $crit) {
			update_err_state(2, "OpCodeCache: $OPCodeCache $mem_used% mem used");
		} elseif ($mem_used > $warn) {
			update_err_state(1, "OpCodeCache: $OPCodeCache $mem_used% mem used");
		} elseif ($mem_used < $warn) {
			update_err_state(0, "OpCodeCache: $OPCodeCache $mem_used% mem used");
		}
	}
}

function check_db($data, $options)
{
	if ($data['DbRequiresUpdate'] === true) {
		update_err_state(2, "DB UPDATE NEEDED");
	} elseif ($data['DbRequiresUpdate'] === false) {
		update_err_state(0, "DB up to date");
	} else {
		update_err_state(3, "DB state unknown");
	}
}

function check_searchindex($data, $options)
{
	if (empty($options['sirwarn']) or empty($options['sircrit'])) {
		displayError("--sircrit and --sirwarn need to be set");
	}
	$warn = $options['sirwarn'];
	$crit = $options['sircrit'];
	if ( $warn > $crit ) {
		displayError("--sircrit needs to be bigger than --sirwarn");
	}
	$iCurrentEpoch = date('U');
	$iDiffEpoch = $iCurrentEpoch - $data['SearchIndexRebuildLast'];
	if (empty($data['SearchIndexRebuildLast'])) {
		update_err_state(3, "Search Index never built");
	} elseif ($data['SearchIndexRebuildLast'] < ($iCurrentEpoch - $crit)) {
		update_err_state(1, "Search Index older than $crit sec|time=".$iDiffEpoch."s;;;0");
	} elseif ($data['SearchIndexRebuildLast'] < ($iCurrentEpoch - $warn)) {
		update_err_state(1, "Search Index older than $warn sec|time=".$iDiffEpoch."s;;;0");
	} elseif ($data['SearchIndexRebuildLast'] > ($iCurrentEpoch - $warn)) {
		update_err_state(0, "Search Index is fresh|time=".$iDiffEpoch."s;;;0");
	} else {
		update_err_state(3, "Search index state unknown");
	}
}

$options = get_opts();
if (empty($options) or isset($options['h'])) {
	help();
	exit(1);
}

$data = get_data($options);
if (isset($options['c'])) {
	$check = 'check_'.$options['c'];
	$check($data, $options);
} else {
	check_bcc($data, $options);
	check_db($data, $options);
	check_searchindex($data, $options);
}
report();
