#!/usr/bin/php
<?php
$message = '';
$err_state = 0;

function help()
{
	echo <<<EOHELP

Tiki monitoring for Nagios/Icinga/Shinken

Syntax:
php check_tiki-new.php -u <URL> --user <user> --pass <password>]

	-u		Full URL of your Tiki installation
			e.g.: http://www.example.com/subdirectory
	--user		User for HTTP authentication
	--pass		Password for HTTP authentication

EOHELP;
}

function get_opts()
{
	$short_opts = "u:h::";
	$long_opts = array(
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
	curl_setopt($crl, CURLOPT_URL, $options['u'].'/tiki-check.php?nagios');
	curl_setopt($crl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($crl, CURLOPT_CONNECTTIMEOUT, $timeout);
	if (!empty($options['user'])) {
		curl_setopt($crl, CURLOPT_USERPWD, $options['user'] . ":" . $options['pass']);
	}
	$ret = curl_exec($crl);
	curl_close($crl);
	$ret = json_decode($ret);
	if ($ret === NULL) {
		$ret = array('state' => 2,
			     'message' => 'Could not get information from Tiki');	
	} else {
		$ret = get_object_vars($ret);
	}
        return $ret;
}

function report($data) {
	switch($data['state']) {
		case 0:
			$message = "TIKI OK - " .$data['message'];
			break;
		case 1:
			$message = "TIKI WARNING - " .$data['message'];
			break;
		case 2:
			$message = "TIKI CRITICAL - " .$data['message'];
			break;
		case 3:
			$message = "TIKI UNKNOWN - " .$data['message'];
			break;
	}
	fwrite(STDOUT, $message);
	exit($data['state']);
}

$options = get_opts();
if (empty($options) or isset($options['h'])) {
	help();
	exit(1);
}

$data = get_data($options);
report($data);
