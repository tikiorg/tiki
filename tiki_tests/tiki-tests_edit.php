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

function get_url($url, $use_tidy = TRUE) {
	global $smarty, $cookies;

	$result = array();
	$get = get_from_dom($url->getElementsByTagName('get')->item(0));
	$post = get_from_dom($url->getElementsByTagName('post')->item(0));
	$xpath = $url->getElementsByTagName('xpath')->item(0)->textContent;
	$data = $url->getElementsByTagName('data')->item(0)->textContent;
	$urlstr = $url->getAttribute("src");
	$referer = $url->getAttribute("referer");

	$result['data'] = $data;
	$data =  tidy_parse_string($data,array(),'utf8');
	tidy_diagnose($data);
	if ($use_tidy) {
		$result['ref_error_count'] = tidy_error_count($data);
		$result['ref_error_msg'] = tidy_get_error_buffer($data);
	}
	$result['html'] = preg_replace(array("/<html .*<body/U","/<\/body><\/html>/U"),array("<div style='overflow: auto; width:500px; text-align: center' ","</div>"),$data);
	$result['url'] = $urlstr;
	$result['xpath'] = $xpath;
	$result['method'] = $url->getAttribute("method");
	$result['post'] = $post;
	$result['get'] = $get;
	$result['referer'] = $referer;

	return $result;
}

function save_test($urls,$file,$options) {
	$dom = new DOMDocument('1.0', 'UTF-8');
	$element_test = $dom->createElement('test');
	$element_test->setAttribute('id','test');
	$dom->appendChild($element_test);
	$opt = $dom->createElement('options');
	$element_test->appendChild($opt);
	foreach($options as $o => $v) {
		$opt->appendChild($dom->createElement($o,$v? 'y' : 'n'));
	}

	foreach($urls as $url) {
		$u = $dom->createElement('url');
		$u->setAttribute('src',$url['url']);
		$u->setAttribute('method',$url['method']);
		$u->setAttribute('referer',$url['referer']);
		$get = $dom->createElement('get');
		if (is_array($url['get'])) {
			foreach($url['get'] as $var => $value) {
				$v = $dom->createElement($var,$value);
				$get->appendChild($v);
			}
			$u->appendChild($get);
		}

		if (is_array($url['post'])) {
			$post = $dom->createElement('post');
			foreach($url['post'] as $var => $value) {
				$v = $dom->createElement($var,$value);
				$post->appendChild($v);
			}
			$u->appendChild($post);
		}

		if (trim($url['xpath']) != '') {
			$xpath = $dom->createElement('xpath',$url['xpath']);
			$u->appendChild($xpath);
		}
		$data = $dom->createElement('data');
		$cdata = $dom->createCDATASection($url['data']);
		$data->appendChild($cdata);
		$u->appendChild($data);
		$element_test->appendChild($u);
	}
	$dom->formatOutput = true;

	$fd = fopen($file,"w");
	fwrite($fd,$dom->saveXML());
	fclose($fd);
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
$options = array();
foreach($dom->getElementsByTagName('options') as $o) {
	$es = $o->getElementsByTagName("*");
	foreach($es as $e) {
		$options[$e->tagName] = $e->nodeValue ;
	}
}

$edit = FALSE;
if (isset($_REQUEST['action'])) {
	if (strtolower($_REQUEST['action']) == strtolower(tra("Edit"))) {
		$edit = TRUE;
	}
	if (strtolower($_REQUEST['action']) != strtolower(tra("Show"))) {
		$options['use_tidy'] = $_REQUEST['show_tidy'];
		$options['show_page'] = $_REQUEST['show_page'];
		$options['show_post'] = $_REQUEST['show_post'];
		$options['summary'] = $_REQUEST['summary'];
		$options['current_session'] = $_REQUEST['current_session'];
	}
}

$count = 0;
foreach($urls as $url) {
	if (!(isset($_REQUEST['delete'][$count]) and $_REQUEST['delete'][$count] == 'delete')) {
		$result[$count] = get_url($url,$options['use_tidy'] == 'y');
		if ($edit and is_string($_REQUEST['xpath'][$count]) and trim($_REQUEST['xpath'][$count]) != '') {
			$result[$count]['xpath'] = trim($_REQUEST['xpath'][$count]);
		}
	}
	$count++;
}	

if ($edit and file_exists("tiki_tests/tests/".basename($_REQUEST['filename']))) {
	save_test($result,"tiki_tests/tests/".basename($_REQUEST['filename']),$options);
}

$smarty->assign_by_ref('result',$result);
$smarty->assign("filename",$_REQUEST['filename']);
$smarty->assign('show_page',$options['show_page']);
$smarty->assign('show_post',$options['show_post']);
$smarty->assign('show_tidy',$options['use_tidy']);
$smarty->assign('current_session',$options['current_session']);
$smarty->assign('summary',$options['summary']);
$smarty->assign('title',tra("TikiTests Edit"));
$smarty->assign('mid', 'tiki-tests_edit.tpl');
$smarty->display('tiki.tpl');

?>
