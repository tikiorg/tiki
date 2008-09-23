<?php

require_once('../tiki-setup.php');
require_once('lib/diff/difflib.php');

if ($prefs['feature_tikitests'] != 'y') {
	$smarty->assign('msg', tra('This feature is disabled').': feature_tikitests');
	$smarty->display('error.tpl');
	die;
}

if ($tiki_p_admin_tikitests != 'y' and $tiki_p_edit_tikitests != 'y') {
	$smarty->assign('msg', tra('You do not have permission to do that'));
	$smarty->display('error.tpl');
	die;
}

function get_from_dom($element) {
	if ($element === NULL) return NULL;
	$es = $element->getElementsByTagName("*");
	$a = array();
	foreach($es as $e) {
		$a[$e->tagName] = $e->nodeValue;
	}
	return $a;
}

function enlight_xpath($url, $xpath) {
	global $smarty, $cookies,$base_url;
	static $purifier;
	static $loaded = false;

	$result = array();
	$data = $url->getElementsByTagName('data')->item(0)->textContent;
	if (trim($data) == '') {
		return tra('The page is empty');
	}

	if (extension_loaded("tidy")) {
		$data =  tidy_parse_string($data,array(),'utf8');
		tidy_diagnose($data);
	} else {
		if (!$loaded) {
			require_once("HTMLPurifier.auto.php");
			$config =& HTMLPurifier_Config::createDefault();
			$config->set('HTML', 'Doctype', 'XHTML 1.0 Transitional');
			$config->set('Attr','EnableID', true);
			$purifier = new HTMLPurifier($config);
			$loaded = true;
		}
		if ($purifier) {
			$data = '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head><body>'.$purifier->purify($data).'</body></html>';
			//$data = $purifier->purify($data);
		}
	}

	$dom_ref = DOMDocument::loadHTML($data);
	$xp_ref = new DomXPath($dom_ref);
	$res_ref = $xp_ref->query('//head');
	$base = $dom_ref->createElement('base');
	$base->setAttribute('href',$base_url);
	$res_ref->item(0)->insertBefore($base,$res_ref->item(0)->firstChild);
	$res_ref = $xp_ref->query($xpath);
	foreach($res_ref as $ref) {
		$ref->setAttribute('style',"background-color: red;");
	}

	return $dom_ref->saveHTML();
}


$xml = file_get_contents("tiki_tests/tests/".basename($_REQUEST['filename']));
if ($xml == '') {
	$smarty->assign('msg', tra("The TikiTests Replay File is Empty"));
	$smarty->display("error.tpl");
	die();
} else {
	$dom = DOMDocument::loadXML($xml);
	$element_test = $dom->getElementsByTagName('test')->item(0);
	if ($element_test == NULL) {
		$smarty->assign('msg', tra("The TikiTests Replay File is Empty"));
		$smarty->display("error.tpl");
		die();
	}
}

$result = array();
$urls = $dom->getElementsByTagName('url');

$count=0;
foreach($urls as $url) {
	if ($count == $_REQUEST['index']) {
		echo enlight_xpath($url,$_REQUEST['xpath']);
	}
	$count++;
}

?>
