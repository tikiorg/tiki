<?php

require_once('../tiki-setup.php');
require_once('lib/diff/difflib.php');

if ($prefs['feature_tikitests'] != 'y') {
	$smarty->assign('msg', tra('This feature is disabled').': feature_tikitests');
	$smarty->display('error.tpl');
	die;
}

if ($tiki_p_admin_tikitests != 'y' and $tiki_p_play_tikitests != 'y') {
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

function verif_url($url, $use_tidy = TRUE) {
	global $smarty, $cookies;
	static $purifier;
	static $loaded = false;

	$result = array();
	$get = get_from_dom($url->getElementsByTagName('get')->item(0));
	$post = get_from_dom($url->getElementsByTagName('post')->item(0));
	$xpath = $url->getElementsByTagName('xpath')->item(0)->textContent;
	$data = $url->getElementsByTagName('data')->item(0)->textContent;

	$urlstr = $url->getAttribute("src");

	if (extension_loaded("http")) {
		$options["timeout"] = 2;
		$options["connecttimeout"] = 2;
		$options["url"] = $url->getAttribute("src");
		$options["referer"] = $url->getAttribute("referer");
		$options["redirect"] = 0;
		$options["cookies"] = $cookies ;
		$options["cookiestore"] = tempnam("/tmp/","tiki-tests") ;
		switch (strtolower($url->getAttribute("method"))) {
			case 'get':
				$buffer = http_get($urlstr,$options,$info);
				break;
			case 'post':
				$buffer = http_post_fields($urlstr,$post,NULL,$options,$info);
		}

	$headers = http_parse_headers($buffer);
	if (isset($headers['Set-Cookie'])) {
		foreach($headers['Set-Cookie'] as $c) {
    	parse_str($c,$cookies);
		}
	}

	$buffer = http_parse_message($buffer)->body;
	} elseif (extension_loaded("curl")) {
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $urlstr);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 2);
		curl_setopt($curl, CURLOPT_TIMEOUT, 2);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_HEADER, true);
		curl_setopt($curl, CURLOPT_REFERER, $url->getAttribute("referer"));
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false );
		curl_setopt($curl, CURLOPT_USERAGENT, "TikiTest" );
		// We deal with the cookies
		$cookies_string = "";
		foreach($cookies as $c => $v) {
			$cookies_string .= "$c=$v; path=/;";
		}
		curl_setopt($curl, CURLOPT_COOKIE, $cookies_string);
		switch (strtolower($url->getAttribute("method"))) {
			case 'get':
				curl_setopt($curl, CURLOPT_HTTPGET, true);
				break;
			case 'post':
				curl_setopt($curl, CURLOPT_POST, true);
				$post_string = "";
				foreach($post as $p => $v) {
					if ($post_string != "") {
						$post_string .= "&";
					}
					$post_string .= "$p=$v";
				}
				curl_setopt($curl, CURLOPT_POSTFIELDS, $post_string);
		}
		$http_response = curl_exec($curl);
		$header_size = curl_getinfo($curl,CURLINFO_HEADER_SIZE);
		$header = substr($http_response, 0, $header_size);
		$body = substr($http_response, $header_size);
		preg_match_all('|Set-Cookie: (.*);|U', $header, $cookies_array);
		foreach($cookies_array[1] as $c) {
			$cookies_tmp .= "&$c";
		}
		parse_str($cookies_tmp,$cookies_titi);
		if (!is_array($cookies)) {
			$cookies = array();
		}
		$cookies = array_merge($cookies, $cookies_titi);
		$buffer = $body;
	}

	if (extension_loaded("tidy")) {
		$data =  tidy_parse_string($data,array(),'utf8');
		$buffer =  tidy_parse_string($buffer,array(),'utf8');
		if ($use_tidy) {
			tidy_diagnose($data);
			$result['ref_error_count'] = tidy_error_count($data);
			$result['ref_error_msg'] = tidy_get_error_buffer($data);
			tidy_diagnose($buffer);
			$result['replay_error_count'] = tidy_error_count($buffer);
			$result['replay_error_msg'] = tidy_get_error_buffer($buffer);
		}
	} else {
		if (!$loaded) {
			require_once("HTMLPurifier.auto.php");
			$config =& HTMLPurifier_Config::createDefault();
			$config->set('HTML', 'Doctype', 'XHTML 1.0 Transitional');
			$config->set('HTML', 'TidyLevel', 'light');
			$purifier = new HTMLPurifier($config);
			$loaded = true;
		}
		if ($purifier) {
			$data = "<html><body>".$purifier->purify($data)."</body></html>";
			$buffer = "<html><body>".$purifier->purify($buffer)."</body></html>";
		}
		$result['ref_error_msg'] = tra("Tidy Extension not present");
		$result['replay_error_msg'] = tra("Tidy Extension not present");
	}
	// If we have a XPath then we extract the new DOM and print it in HTML
	if (trim($xpath) != '') {
		$dom_ref = DOMDocument::loadHTML($data);
		$xp_ref = new DomXPath($dom_ref);
		$res_ref = $xp_ref->query($xpath);
		$new_data = new DOMDocument('1.0');
		$root = $new_data->createElement('html');
		$root = $new_data->appendChild($root);
		$body = $new_data->createElement('html');
		$body = $root->appendChild($body);
		foreach($res_ref as $ref) {
			$tmp = $new_data->importNode($ref,TRUE);
			$body->appendChild($tmp);
		}
		$data = $new_data->saveHTML();
		$dom_buffer = DOMDocument::loadHTML($buffer);
		$xp_buffer = new DomXPath($dom_buffer);
		$res_buffer = $xp_buffer->query($xpath);
		$new_buffer = new DOMDocument('1.0');
		$root = $new_buffer->createElement('html');
		$root = $new_buffer->appendChild($root);
		$body = $new_buffer->createElement('html');
		$body = $root->appendChild($body);
		foreach($res_buffer as $ref) {
			$tmp = $new_buffer->importNode($ref,TRUE);
			$body->appendChild($tmp);
		}
		$buffer = $new_buffer->saveHTML();
	}
	$tmp = diff2($data, $buffer, "htmldiff");
	if (trim($xpath) != '') {
		$result['html'] = preg_replace(array("/<html>/","/<\/html>/"),array("<div style='overflow: auto; width:500px; text-align: center'> ","</div>"),$tmp);
	} else {
		$result['html'] = preg_replace(array("/<html.*<body/U","/<\/body><\/html>/U"),array("<div style='overflow: auto; width:500px; text-align: center' ","</div>"),$tmp);
	}
	$result['url'] = $urlstr;
	$result['method'] = $url->getAttribute("method");
	if (strtolower($result['method']) == 'post' ) {
		$result['post'] = $post;
	}

	return $result;
}

if (isset($_REQUEST['action'])) {
	$smarty->assign("filename",$_REQUEST['filename']);
	$xml = file_get_contents("tiki_tests/tests/".$_REQUEST['filename']);
	if ($xml == '') {
		$smarty->assign('msg', tra("The TikiTest Replay File is Empty"));
		$smarty->display("error.tpl");
		die();
	} else {
		$dom = DOMDocument::loadXML($xml);
		$element_test = $dom->getElementsByTagName('test')->item(0);
		if ($element_test == NULL) {
			$smarty->assign('msg', tra("The TikiTest Replay File is Empty"));
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
			$options[$e->tagName] = $e->nodeValue;
		}
	}

	if ($options['current_session'] == 'y') {
		$cookies = $_COOKIE;
	}

	if (strtolower($_REQUEST['action']) == strtolower(tra("Replay"))) {
		$options['use_tidy'] = $_REQUEST['show_tidy'];
		$options['show_page'] = $_REQUEST['show_page'];
		$options['show_post'] = $_REQUEST['show_post'];
		$options['summary'] = $_REQUEST['summary'];
		$options['current_session'] = $_REQUEST['current_session'];
		$test_success = $test_failure = $test_count = 0;
		foreach($urls as $url) {
			$tmp = verif_url($url,$options['use_tidy'] == 'y');
			$result[] = $tmp;
			if ($summary) {
				$test_count++;
				if ($tmp['html'] == '') {
					$test_success++;
				} else {
					$test_failure++;
				}
			}
		}	
		$smarty->assign_by_ref('result',$result);
	}

	$smarty->assign("filename",$_REQUEST['filename']);
	$smarty->assign('show_page',$options['show_page']);
	$smarty->assign('show_post',$options['show_post']);
	$smarty->assign('show_tidy',$options['use_tidy']);
	$smarty->assign('current_session',$options['current_session']);
	$smarty->assign('summary',$options['summary']);
	$smarty->assign('test_count',$test_count);
	$smarty->assign('test_failure',$test_failure);
	$smarty->assign('test_success',$test_success);
	$smarty->assign('mid', 'tiki-tests_replay.tpl');
	$smarty->assign('title',tra("TikiTest Replay"));
	$smarty->display('tiki.tpl');
} else {
	header("Location: tiki-tests_list.php");
}

?>
