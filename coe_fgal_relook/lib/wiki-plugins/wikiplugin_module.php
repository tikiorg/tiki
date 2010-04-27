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
		'description' => tra("Displays a module inline in a wiki page. More parameters can be added, not supported by UI."),
		'prefs' => array( 'wikiplugin_module' ),
		'validate' => 'all',
		'icon' => 'pics/icons/module.png',
		'extraparams' =>true,
		'params' => array(
			'module' => array(
				'required' => true,
				'name' => tra('Module Name'),
				'description' => tra('Module name as known in Tikiwiki.'),
				'options' => $modules_options
			),
			'float' => array(
				'required' => false,
				'name' => tra('Float'),
				'description' => 'left|right|none',
			),
			'decoration' => array(
				'required' => false,
				'name' => tra('Decoration'),
				'description' => 'y|n',
			),
			'flip' => array(
				'required' => false,
				'name' => tra('Flip'),
				'description' => 'y|n',
			),
			'max' => array(
				'required' => false,
				'name' => tra('Max'),
				'description' => 'y|n',
			),
			'np' => array(
				'required' => false,
				'name' => tra('np'),
				'description' => '0|1',
			),
			'notitle' =>array(
				'required' => false,
				'name' => tra('notitle'),
				'description' => 'y|n',
			),
			'inside_pretty' => array(
				'required' => false,
				'name' => tra('Inside Pretty Tracker'),
				'description' => tra('Set to y to use inside a pretty tracker with field reference replacement (default=n)'),
				'filter' => 'alpha'
			),
		),
	);
}

function wikiplugin_module($data, $params) {
	global $tikilib, $cache_time, $smarty, $dbTiki, $prefs, $ranklib, $tikidomain, $user, $tiki_p_tasks, $tiki_p_create_bookmarks, $imagegallib, $module_params, $trklib;

	$out = '';
	
	if (isset($params['inside_pretty']) && $params['inside_pretty'] == 'y') {
		require_once("lib/trackers/trackerlib.php");
		$trklib->replace_pretty_tracker_refs($params);
	}
	
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
