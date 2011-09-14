<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

if ($prefs['feature_tikitests'] != 'y') {
	return;
}

$test_url = $_SERVER['QUERY_STRING'];
$test_get = $_GET;
$test_post = $_POST;

if (isset($_COOKIE['tikitest_record'])) {
  ob_start(test_callback);
	if (!isset($_SESSION['tiki_cookie_jar'])) {
	  $_SESSION['tiki_cookie_jar'] = array();
	}
	$_SESSION['tiki_cookie_jar']['javascript_enabled'] = 'n';
	if ($_COOKIE['tikitest_record'] != 3) {
		if ($_COOKIE['tikitest_record'] == '') $_COOKIE['tikitest_record'] = 1;
		$smarty->assign("tikitest_filename",$_COOKIE['tikitest_filename']);
		$smarty->assign("tikitest_state",$_COOKIE['tikitest_record']);
	} else {
		setcookie ("tikitest_record", "", time() - 3600,"/");
		setcookie ("tikitest_filename", "", time() - 3600,"/");
		$smarty->clear_assign(array("tikitest_filename","tikitest_state"));
	}
} else {
	return;
}

function test_callback($buffer) {
	global $test_cookie, $test_post, $test_get, $test_url;

	if (!isset($_COOKIE['tikitest_record']) or $_COOKIE['tikitest_record'] >= 2) {
		return $buffer;
	}

  if (strpos(basename($_SERVER['PHP_SELF']),"tiki-download_file") !== FALSE) {
	  return $buffer;
	}

	$filename = basename( trim( $_COOKIE['tikitest_filename'] ) );
	if (isset($_COOKIE['tikitest_filename'])) {
		$xml_file = dirname(__FILE__) . "/tests/". $filename .".xml";
	} else {
		$xml_file = dirname(__FILE__) . "/tests/tikitest.xml";
	}
	$xml = file_get_contents($xml_file);
	if ($xml == '') {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$element_test = $dom->createElement('test');
		$element_test->setAttribute('id','test');
		$dom->appendChild($element_test);
	} else {
		$dom = DOMDocument::loadXML($xml);
		if ($dom) {
			$element_test = $dom->getElementsByTagName('test')->item(0);
			if ($element_test == NULL) {
				return $buffer;
			}
		} else {
			// Can't read the XML file go out
			return $buffer;
		}
	}

  $url = $dom->createElement('url');
	if (!empty($_SERVER['HTTPS'])) {
		$http = "https";
	} else {
		$http = "http";
	}
	$url->setAttribute('src',"$http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
	$url->setAttribute('method',$_SERVER['REQUEST_METHOD']);
	$url->setAttribute('referer',$_SERVER['HTTP_REFERER']);
  $get = $dom->createElement('get');
	if (is_array($test_get)) {
		foreach($test_get as $var => $value) {
  		$v = $dom->createElement($var,$value);
			$get->appendChild($v);
		}
		$url->appendChild($get);
	}

	if (is_array($test_post)) {
	  $post = $dom->createElement('post');
		foreach($test_post as $var => $value) {
  		$v = $dom->createElement($var,$value);
			$post->appendChild($v);
		}
		$url->appendChild($post);
	}
error_reporting(E_ALL);

  $data = $dom->createElement('data');
	$tikitestheader = '/<!-- StartTikiTestRemoveMe -->.*<!-- EndTikiTestRemoveMe -->/';
	$cdata = $dom->createCDATASection(preg_replace($tikitestheader,'',$buffer));
	$data->appendChild($cdata);
	$url->appendChild($data);
	$element_test->appendChild($url);

	$dom->formatOutput = true;

  $fd = fopen($xml_file,"w");
	fwrite($fd,$dom->saveXML());
	fclose($fd);

	return $buffer;
}
