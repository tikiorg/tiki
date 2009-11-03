<?php
/* $Id: wikiplugin_module.php,v 1.33 2007-10-12 07:55:48 nyloth Exp $
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
	return array(
		'name' => tra('Insert Module'),
		'documentation' => 'PluginModule',
		'description' => tra("Displays a module inline in a wiki page. More parameters can be added, not supported by UI."),
		'prefs' => array( 'wikiplugin_module' ),
		'validate' => 'all',
		'extraparams' =>true,
		'params' => array(
			'module' => array(
				'required' => true,
				'name' => tra('Module Name'),
				'description' => tra('Module name as known in Tikiwiki.'),
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
		),
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
