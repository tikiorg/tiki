<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function smarty_function_preference( $params, $smarty )
{
	global $prefs, $user_overrider_prefs;
	$prefslib = TikiLib::lib('prefs');
	if ( ! isset( $params['name'] ) ) {
		return 'Preference name not specified.';
	}

	$source = null;
	if ( isset( $params['source'] ) ) {
		$source = $params['source'];
	}
	$get_pages = isset( $params['get_pages']) && $params['get_pages'] != 'n' ? true : false;

	if ( $info = $prefslib->getPreference($params['name'], true, $source, $get_pages) ) {
		if ( isset($params['label']) ) {
			$info['name'] = $params['label'];
		}
		if ($source === null && in_array($params['name'], $user_overrider_prefs) && isset($prefs[$params['name']])) {
			$info['value'] = $prefs['site_' . $params['name']];
		}

		if (isset($info['autocomplete']) ) {
			$info['params'] .= ' autocomplete="' . $info['autocomplete'] . '" ';
		}

		if (isset($params['visible']) && $params['visible'] == 'always') {
			// Modified preferences are never hidden, so pretend it's modified when forcing display
			$info['tags'][] = 'modified';
			$info['tagstring'] .= ' modified';
		}

		if ($get_pages) {
			if (count($info['pages']) > 0) {
			foreach ($info['pages'] as $pg) {
				$ct_string = $pg[1] > 1 ? '&amp;cookietab=' . $pg[1] : '';
				$pages_string = '<a class="lm_result label label-default" href="tiki-admin.php?page=' . $pg[0] . $ct_string . '&amp;highlight=' . $info['preference'] . '">' . $pg[0] . '</a> ';
			}
			} else {
				$pages_string = tra('(not found in an admin panel)');
			}
		} else {
			$pages_string = '';
		}
		$info['pages'] = $pages_string;

		if ( !isset($info['separator']) ) { 
			$info['separator'] = array();
		}
		if ( isset($params['size']) ) { 
			$info['size'] = $params['size'];
		}

		$smarty->assign('p', $info);

		if ( isset($params['mode']) && $params['mode'] == 'invert' ) {
			$smarty->assign('mode', 'invert');
		} else {
			$smarty->assign('mode', 'normal');
		}
		
		//we reset the codemirror/syntax vars so that they are blank because they are reused for other params
		$smarty->assign('codemirror');
		$smarty->assign('syntax');
		
		if ( !empty($params['syntax']) ) {
			$smarty->assign('codemirror', 'true');
			$smarty->assign('syntax', $params['syntax']);
		}

        if (file_exists('templates/prefs/' . $info['type'] . '.tpl')) {
		    return $smarty->fetch('prefs/' . $info['type'] . '.tpl', $params['name']);
        } else {
            return $smarty->fetch('prefs/text.tpl');
        }
	} else {
		$info = array(
			'value' => tra('Error'),
			'default_val' => tra('Error'),
			'name' => tr('Preference %0 is not defined', $params['name']),
			'tags' => array('modified', 'basic', 'all'),
			'tagstring' => 'modified basic all',
			'separator' => null,
		);
		if (strpos($_SERVER["SCRIPT_NAME"], 'tiki-edit_perspective.php') !== false) {
			$info['hint'] = tra('Drag this out of the perspective and resave the perspective.');
		}

		$smarty->assign('p', $info);
		return $smarty->fetch('prefs/text.tpl');
	}
}
