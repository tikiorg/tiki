<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/*
Displays a module inline in a wiki page

Parameters
module name : module=>lambda
float : float=>(left|none|right)
max : max=>20
np : np=>(0|1) # (for non-parsed content)
flip : flip=>(n|y)
decorations : decorations=>(y|n)
module args : arg=>value (depends on module)

Example:
{MODULE(module=>last_modified_pages,float=>left,max=>3,maxlen=>22)}
{MODULE}

about module params : all params are passed in $module_params
so if you need to use params just add them in MODULE()

*/

/**
 * \warning zaufi: using cached module template is break the idea of
 *   having different (than system default) parameters for modules...
 *   so cache checking and maintaining currently commented out
 *   'till another solution will be implemented :)
 */

function wikiplugin_module_help() {
	return tra("Displays a module inline in a wiki page").":<br />~np~{MODULE(module=>,float=>left|right|none,decorations=>y|n,flip=>y|n,max=>,np=>0|1,notitle=y|n,args...)}{MODULE}~/np~";
}

function wikiplugin_module_info() {
	global $modlib, $smarty;
	require_once ('lib/modules/modlib.php');

	$all_modules = $modlib->get_all_modules();
	$all_modules_info = array_combine( 
		$all_modules, 
		array_map( array( $modlib, 'get_module_info' ), $all_modules ) 
	);
	asort($all_modules_info);
	$modules_options = array();
	foreach($all_modules_info as $module => $module_info) {
		$modules_options[] = array('text' => $module_info['name'] . ' (' . $module . ')', 'value' => $module);
	}

	return array(
		'name' => tra('Insert Module'),
		'documentation' => 'PluginModule',
		'description' => tra('Display a module in a wiki page'),
		'prefs' => array( 'wikiplugin_module' ),
		'validate' => 'all',
		'icon' => 'pics/icons/module.png',
		'extraparams' =>true,
		'params' => array(
			'module' => array(
				'required' => true,
				'name' => tra('Module Name'),
				'description' => tra('Module name as known in Tiki'),
				'default' => '',
				'options' => $modules_options,
			),
			'float' => array(
				'required' => false,
				'name' => tra('Float'),
				'description' => tra('Align the module to the left or right on the page allowing other elements to align against it'),
				'default' => 'nofloat',
				'advanced' => true,
				'options' => array(
					array('text' => 'No Float', 'value' => ''), 
					array('text' => tra('Left'), 'value' => 'left'), 
					array('text' => tra('Right'), 'value' => 'right')
				)
			),
			'decoration' => array(
				'required' => false,
				'name' => tra('Decoration'),
				'description' => tra('Show box decorations (default is to show them)'),
				'advanced' => true,
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => '1'), 
					array('text' => tra('No'), 'value' => '0'), 
				)
			),
			'flip' => array(
				'required' => false,
				'name' => tra('Flip'),
				'description' => tra('Add ability to show/hide the content of the module (default is the site admin setting for modules)'),
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => '1'), 
					array('text' => tra('No'), 'value' => '0'), 
				),
				'advanced' => true,
			),
			'max' => array(
				'required' => false,
				'name' => tra('Max'),
				'description' => 'Number of rows (default: 10)',
				'default' => 10,
				'advanced' => true,
			),
			'np' => array(
				'required' => false,
				'name' => tra('Parse'),
				'description' => tra('Parse wiki syntax (default is to parse)'),
				'default' => '1',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => '1'), 
					array('text' => tra('No'), 'value' => '0'), 
				),
				'advanced' => true,
			),
			'notitle' =>array(
				'required' => false,
				'name' => tra('Title'),
				'description' => tra('Show/hide module title (default is to show the title)'),
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Show title'), 'value' => 'n'), 
					array('text' => tra('Hide title'), 'value' => 'y')
				)
			)
		)
	);
}

function wikiplugin_module($data, $params) {
	global $tikilib, $cache_time, $smarty, $dbTiki, $prefs, $ranklib, $tikidomain, $user, $tiki_p_tasks, $tiki_p_create_bookmarks, $imagegallib, $module_params;

	$out = '';
	
	extract ($params,EXTR_SKIP);

	if (!isset($float)) {
		$float = 'nofloat';
	}

    if (!isset($max)) {
        if (!isset($rows)) {
            $max = 10; // default value
        } else $max=$rows; // rows=> used instead of max=> ?
    }

	if (!isset($np)) {
		$np = '1';
	}

	if (!isset($module) or !$module) {
		$out = '<form class="box" id="modulebox">';

		$out .= '<br /><select name="choose">';
		$out .= '<option value="">' . tra('Please choose a module'). '</option>';
		$out .= '<option value="" style="background-color:#bebebe;">' . tra('to be used as argument'). '</option>';
		$out .= '<option value="" style="background-color:#bebebe;">{MODULE(module=>name_of_module)}</option>';
		$handle = opendir('modules');

		while ($file = readdir($handle)) {
			if ((substr($file, 0, 4) == "mod-") and (substr($file, -4, 4) == ".php")) {
				$mod = substr(substr(basename($file), 4), 0, -4);

				$out .= "<option value=\"$mod\">$mod</option>";
			}
		}

		$out .= '</select></form>';
	} else {
		if (!isset($args)) {
			$args = '';
		}

		$module_reference = array(
			'moduleId' => null,
			'name' => $module,
			'params' => $params,
			'rows' => $max,
			'position' => null,
			'ord' => null,
			'cache_time'=> 0,
		);

		global $modlib; require_once 'lib/modules/modlib.php';
		$out = $modlib->execute_module( $module_reference );
	}

	if ($out) {
		if ($float != 'nofloat') {
			$data = "<div style='float: $float;'>";
		} else {
			$data = "<div>";
		}	
		if ($np) {
  		$data.= "~np~$out~/np~</div>";
		} else {
			$data.= "$out</div>";
		}
	} else {
        // Display error message
		$data = "<div class=\"highlight\">" . tra("Sorry, no such module"). "<br /><b>$module</b></div>" . $data;
	}

	return $data;
}
