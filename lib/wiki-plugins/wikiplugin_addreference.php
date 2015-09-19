<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_addreference_info()
{
	return array(
		'name' => tra('Add Reference'),
		'description' => tra('Add a bibliography reference'),
		'format' => 'html',
		'introduced' => 10,
		'prefs' => array('wikiplugin_addreference','feature_references'),
		'iconname' => 'pencil',
		'params' => array(
			'biblio_code' => array(
				'required' => true,
				'name' => tra('Biblio Code'),
				'description' => tra('The code to be added as reference.'),
				'default' => '',
				'since' => '10.0',
			),
		),
	);
}

function wikiplugin_addreference($data,$params)
{
	global $prefs;

	if ($prefs['wikiplugin_addreference'] == 'y') {

		$referenceslib = TikiLib::lib('references');

		if (! isset($GLOBALS['referencesData'])) {
			$GLOBALS['referencesData'] = array();
		}

		$data = trim($data);

		if (strstr($_SERVER['SCRIPT_NAME'], 'tiki-print.php')) {

			$page = urldecode($_REQUEST['page']);
			$page_id = TikiLib::lib('tiki')->get_page_id_from_name($page);
			$page_info = TikiLib::lib('tiki')->get_page_info($page);

		} else {

			$object = current_object();
			$page_id = TikiLib::lib('tiki')->get_page_id_from_name($object['object']);
			$page_info = TikiLib::lib('tiki')->get_page_info($object['object']);

		}

		extract($params, EXTR_SKIP);
		if (empty($params['biblio_code'])) {
			return;
		}

		$regex = "/{ADDREFERENCE\(?\ ?biblio_code=\"(.*)\"\)?}.*({ADDREFERENCE})?/siU";
		preg_match_all($regex, $page_info['data'], $matches);

		$temp = array();
		$curr_matches = array();
		$temp = array_unique($matches[1]);
		$i = 0;
		foreach ($temp as $k=>$v) {
			if (strlen(trim($v)) > 0) {
				$curr_matches[$i] = $v;
				$i++;
			}
		}
		unset($temp);

		$found_keys = array();

		foreach ($curr_matches as $key=>$val) {
			if (strlen(trim($val)) > 0) {
				if ($val == $params['biblio_code']) {
					if (!in_array($val, $found_keys)) {
						$found_keys[] = $val;
						$index = $key + 1;
						$i++;
					}
				}
			}
		}

		$GLOBALS['referencesData'] = $curr_matches;

		$url = $GLOBALS['base_uri'] . "#" . $params['biblio_code'];

		return $data . "<a href='" . $url . "' title='" . $params['biblio_code'] . "'><sup>" . $index . "</sup></a>";

	}
}
