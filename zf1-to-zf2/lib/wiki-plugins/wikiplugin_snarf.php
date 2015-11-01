<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_snarf_info()
{
	return array(
		'name' => tra('Snarf'),
		'documentation' => 'PluginSnarf',
		'description' => tra('Display the contents of another web page'),
		'prefs' => array( 'wikiplugin_snarf' ),
		'validate' => 'all',
		'iconname' => 'copy',
		'introduced' => 1,
		'params' => array(
			'url' => array(
				'required' => true,
				'name' => tra('URL'),
				'description' => tra('Full URL to the page to include.'),
				'since' => '1',
				'filter' => 'url',
				'default' => '',
			),
			'regex' => array(
				'required' => false,
				'name' => tra('Regular Expression Pattern'),
				'description' => tra('PCRE-compliant regular expression pattern to find the parts you want changed'),
				'since' => '1',
				'default' => '',
				'filter' => 'text'
			),
			'regexres' => array(
				'required' => false,
				'name' => tra('Regular Expression Replacement'),
				'description' => tra('PCRE-compliant regular expression replacement syntax showing what the content should be changed to'),
				'since' => '1',
				'default' => '',
				'filter' => 'text'
			),
			'wrap' => array(
				'required' => false,
				'name' => tra('Word Wrap'),
				'description' => tra('Enable/disable word wrapping of snippets of code (enabled by default)'),
				'default' => 1,
				'since' => '3.0',
				'filter' => 'digits',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 1), 
					array('text' => tra('No'), 'value' => 0)
				),
			),
			'colors' => array(
				'required' => false,
				'name' => tra('Colors'),
				'description' => tra('Syntax highlighting to use for code snippets (Plugin Code is used for this)'),
				'since' => '3.0',
				'default' => NULL,
				'filter' => 'text'
			),
			'ln' => array(
				'required' => false,
				'name' => tra('Line Numbers'),
				'description' => tr('Set to Yes (%0) to add line numbers to code snippets (not shown by default)',
					'<code>1</code>'),
				'since' => '3.0',
				'default' => NULL,
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 1), 
					array('text' => tra('No'), 'value' => 0)
				),
				'filter' => 'int'
			),
			'wiki' => array(
				'required' => false,
				'name' => tra('Wiki Syntax'),
				'description' => tra('Parse wiki syntax within the code snippet (not parsed by default).'),
				'since' => '3.0',
				'default' => 0,
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 1), 
					array('text' => tra('No'), 'value' => 0)
				),
				'filter' => 'int',
			),
			'rtl' => array(
				'required' => false,
				'name' => tra('Right to Left'),
				'description' => tra('Switch the text display from left to right to right to left'),
				'since' => '3.0',
				'default' => NULL,
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 1), 
					array('text' => tra('No'), 'value' => 0)
				),
				'filter' => 'int'
			),
			'ishtml' => array(
				'required' => false,
				'name' => tra('HTML Content'),
				'description' => tr('Set to Yes (%0) to display the content as is instead of escaping HTML special
					characters (not set by default).', '<code>1</code>'),
				'since' => '3.0',
				'default' => NULL,
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 1), 
					array('text' => tra('No'), 'value' => 0)
				),
				'filter' => 'int'
			),
			'cache' => array(
				'required' => false,
				'name' => tra('Cache Url'),
				'description' => tr('Cache time in minutes. Default is to use site preference, Set to %0 for no cache.',
					'<code>0</code>'),
				'since' => '5.0',
				'default' => '',
				'filter' => 'int'
			),
			'ajax' => array(
				'required' => false,
				'name' => tra('Label'),
				'description' => tra('Text to click on to fetch the url via ajax'),
				'since' => '6.0',
				'default' => '',
				'filter' => 'text'
			),
		),
	);
}

function wikiplugin_snarf($data, $params)
{
    global $prefs;
	static $url=''; static $snarf; static $isFresh = true;
	static $iSnarf = 0;
	++$iSnarf;
	if (empty($params['url'])) {
		return '';
	}
	$smarty = TikiLib::lib('smarty');
	$tikilib = TikiLib::lib('tiki');
	if (!empty($params['ajax'])) {
		$params['iSnarf'] = $iSnarf;
		$params['href'] = '';
		$params['link'] = '-';
		foreach ($params as $key=>$value) {
			if ($key == 'ajax' || $key == 'href') {
				continue;
			}
			if (!empty($params['href'])) {
				$params['href'] .= '&amp;';
			}
			$params['href'] .= $key.'='.urlencode($value);
		}
		$smarty->assign('snarfParams', $params);
		return $smarty->fetch('wiki-plugins/wikiplugin_snarf.tpl');
	}
	if ($url != $params['url']) { // already fetch in the page
		if (isset($_REQUEST['snarf_refresh']) && $_REQUEST['snarf_refresh'] == $params['url']) {
			$cachetime = 0;
			unset($_REQUEST['snarf_refresh']);
		} elseif (isset($params['cache']) && $params['cache'] >= 0) {
			$cachetime = $params['cache'] * 60;
		} else {
			$cachetime = $prefs['wikiplugin_snarf_cache'];
		}
		$info = $tikilib->get_cached_url($params['url'], $isFresh, $cachetime);
		$snarf = $info['data'];
		$url = $params['url'];
	}

	// If content is HTML, keep only the content of the body
	if ( isset($params['ishtml']) && $params['ishtml'] == 1 ) {
		// Not using preg_replace due to its limitations to 100.000 characters
		$snarf = preg_replace('/^.*<\s*body[^>]*>/i', '', $snarf);
		$snarf = preg_replace('/<\s*\/body[^>]*>.*$/i', '', $snarf);
	}

	// If the user specified a more specialized regex
	if (isset($params['regex']) && isset($params['regexres'])) { 
		// fixes http://dev.tiki.org/item4059
		$params['regex'] = str_replace("\0", "", $params['regex']);
		
		if (preg_match('/^(.)(.)+\1[^e]*$/', $params['regex'])) {
			$snarf = preg_replace($params['regex'], $params['regexres'], $snarf);
		}
	}

	if ( $data == '' ) $data = NULL;
	$code_defaults = array('caption' => $data, 'wrap' => '1', 'colors' => NULL, 'wiki' => '0', 'ln' => NULL, 'rtl' => NULL, 'ishtml' => NULL);

	foreach ( $code_defaults as $k => $v ) {
		if ( isset($params[$k]) ) $code_defaults[$k] = $params[$k];
		if ( $code_defaults[$k] === NULL ) unset($code_defaults[$k]);
	}

	include_once('lib/wiki-plugins/wikiplugin_code.php');
	$ret = wikiplugin_code($snarf, $code_defaults);

	if (!$isFresh && empty($params['link'])) {
		include_once('lib/smarty_tiki/block.self_link.php');
		$icon = '<div style="text-align:right">'.smarty_block_self_link(array('_icon' => 'arrow_refresh', 'snarf_refresh'=>$params['url']), '', $smarty).'</div>';
		$ret = $icon.$ret;
	}

    return $ret;
}
