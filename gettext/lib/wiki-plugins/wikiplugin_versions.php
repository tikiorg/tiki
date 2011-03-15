<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/*
 * Versions plugin: Split the text in parts visible only under some conditions:
 * 
 * Syntax:
 * {VERSIONS(nav=>y| n, title=>y| n, default=>)}text{VERSIONS}
 * 
 * Documentation
 * http://doc.tiki.org/PluginVersions
 */
function wikiplugin_versions_help()
{
	return tra("Split the text in parts visible only under some conditions") . ":<br />"
            . "~np~{VERSIONS(nav=>y|n,title=>y|n,default=>)}"
            . tra("This is the default text") . "<br />"
            . "---" . tra("(version 3)") . "-----------------------------" . "<br />" 
            . tra("This is version 3 info") . "<br />"
            . "---" . tra("(version 2)") . "-----------------------------" . "<br />"
            . tra("This is version 2 info") . "<br />"
            . "---" . tra("(version 1)") . "-----------------------------" . "<br />"
            . tra("This is version 1 info") ."{VERSIONS}~/np~";
}

function wikiplugin_versions_info()
{
	return array(
		'name' => tra('Versions'),
		'documentation' => 'PluginVersions',
		'description' => tra('Create tabs for showing alternate versions of content'),
		'prefs' => array( 'wikiplugin_versions' ),
		'body' => tra('Block of text separated by ---(version x)--- markers. Text before the first marker is used by default.'),
		'icon' => 'pics/icons/tab_edit.png',
		'params' => array(
			'nav' => array(
				'required' => false,
				'name' => tra('Navigation'),
				'description' => tra('Displays a navigation box that allows users to select a specific version to display.'),
				'default' => 'n',
				'filter' => 'alpha',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n'), 
				),
			),
			'title' => array(
				'required' => false,
				'name' => tra('Title'),
				'description' => tra('Display the current version name as the title. No title shows when nav=>y; otherwise shows by default.'),
				'default' => 'y',
				'filter' => 'alpha',
				'parent' => array('name' => 'nav', 'value' => 'n'),
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n'), 
				),
			),
			'default' => array(
				'required' => false,
				'name' => tra('Default Label'),
				'description' => tra('Specifies version label to show when displaying the page for the first time. Default label is \'Default\''),
				'default' => tra('Default'),
			),
		),
	);
}

function wikiplugin_versions($data, $params)
{
	global $use_best_language, $prefs;
	if (isset($params) and is_array($params)) {
		extract ($params, EXTR_SKIP);
	}
	$data = $data;
	$navbar = '';
	if (!isset($default)) { $default = tra('Default'); }
	if (!isset($title)) { $title = 'y'; }
	if (!isset($nav)) { $nav = 'n'; }
	
	preg_match_all('/---\(([^\):]*)( : [^\)]*)?\)---*/', $data, $v);

	if (isset($type) and $type == 'host') {
		if (isset($_SERVER['TIKI_VERSION'])) {
			$vers = $_SERVER['TIKI_VERSION'];
		} else {
			$vers = $default;
		}
	} else {
		if (isset($_REQUEST['tikiversion'])) {
			$vers = $_REQUEST['tikiversion'];
		} elseif ($use_best_language == 'y' and in_array($prefs['language'], $v[1]))  {
			$vers = $prefs['language'];
		} else {
			$vers = $default;
		}
		$type = "request";
	}
	
	if (in_array($vers, $v[1])) {
		$p = array_search($vers, $v[1]) + 1;
	} else {
		$p = 0;
	}
if (!isset($_REQUEST['preview'])){
	if ($p == 0) {
		if (strpos($data, '---(') !== false) {
			$data = substr($data, 0, strpos($data, '---('));
		}
		if ($nav == 'n' and $title == 'y') { $data = "<b class='versiontitle'>". $default .'</b>'.$data; }
		$data = ltrim(substr($data, strpos("\n", $data)));
	} elseif (isset($v[1][$p-1]) and strpos($data, '---('.$v[1][$p-1])) {
		if ($nav == 'n' and $title == 'y') {
			$data = substr($data, strpos($data, '---('.$v[1][$p-1]));
			$data = preg_replace('/\)---*[\r\n]*/', "</b>\n", "<b class='versiontitle'>". substr($data, 4));
		} else {
			// can't get it to work as a single preg_match_all, so...
			preg_match_all("/(^|---\([^\(]*\)---*\s)/", $data, $t, PREG_OFFSET_CAPTURE);
			$start = $t[0][$p][1] + strlen($t[0][$p][0]);
			$end   = $p + 1 < count($t[0]) ? $t[0][$p+1][1] : strlen($data);
			$data = substr($data, $start, $end);
		}
		if (strpos($data, '---(') !== false) {
			$data = substr($data, 0, strpos($data, '---('));
		}
	}
}	
	if ($nav == 'y') {
		$highed = false;
		for ($i=0, $icount_v = count($v[1]); $i < $icount_v; $i++) {
			$version = $v[1][$i];
			$ver = $version.$v[2][$i];
			if ($i == $p-1) {
				$high = " highlight";
				$highed = true;
			} else {
				$high = '';
			}
			if ($type == 'host') {
				$vv = preg_replace('/[^a-z0-9]/', '', strtolower($version));
				$navbar.= ' <span class="button' . $high . '"><a href="http://' . $vv 
									. '.' . preg_replace("/".$v[1][$p]."/", "", $_SERVER['SERVER_NAME']) 
									. preg_replace("~(\?|&)tikiversion=[^&]*~", "", $_SERVER['REQUEST_URI'])
									. '" class="linkbut">' . $ver . '</a></span>'
									;
			} else {
				$navbar.= ' <span class="button' . $high . '"><a href="';
				if (strpos($_SERVER['REQUEST_URI'], '?') !== false) { 
					$navb = preg_replace("~(\?|&)tikiversion=[^&]*~", "", $_SERVER['REQUEST_URI']);
				} else {
					$navb = $_SERVER['REQUEST_URI'];
				}
				if (strpos($navb, '?') !== false) {
					$navbar.= "$navb&";
				} else {
					$navbar.= "$navb?";
				}
				$navbar .= 'tikiversion=' . urlencode($version) . '" class="linkbut">' . $ver . '</a></span>';
			}
		}
		
		if (!$highed) { $high = " highlight"; } else { $high = ''; }
		if ($type == 'host') {
			$navbar = '<span class="button' . $high . '"><a href="http://'
								. preg_replace("/".$v[1][$p]."/", "", $_SERVER['SERVER_NAME']) 
								. preg_replace("~(\?|&)tikiversion=[^&]*~", "", $_SERVER['REQUEST_URI']) 
								. '" class="linkbut">' . $default . '</a></span>' . $navbar
								;
		} else {
			$navbar = '<span class="button' . $high . '"><a href="'
							. preg_replace("~(\?|&)tikiversion=[^&]*~", "", $_SERVER['REQUEST_URI']) 
							. '" class="linkbut">' . $default . '</a></span>' . $navbar
							;
		}
		$data = '<div class="versions"><div class="versionav">' . $navbar 
					. '</div><div class="versioncontent">' . $data . "</div>\n</div>"
					;
	}

	return $data;
}
