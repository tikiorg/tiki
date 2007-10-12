<?php
/* $Id: wikiplugin_module.php,v 1.33 2007-10-12 07:55:48 nyloth Exp $
Displays a module inlined in page

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
	return tra("Displays a module inlined in page").":<br />~np~{MODULE(module=>,float=>left|right|none,decorations=>y|n,flip=>y|n,max=>,np=>0|1,args...)}{MODULE}~/np~";
}

function wikiplugin_module($data, $params) {
	global $tikilib, $cache_time, $smarty, $dbTiki, $prefs, $ranklib, $tikidomain, $user, $tiki_p_tasks, $tiki_p_create_bookmarks, $imagegallib;

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

		$phpfile = 'modules/mod-' . $module . '.php';
		$template = 'modules/mod-' . $module . '.tpl';

		$module_rows = $max;
		$module_params = $params;
		if (!isset($module_params['decorations'])) $module_params['decorations'] = 'n';
		if (!isset($module_params['flip'])) $module_params['flip'] = 'n';
		if (isset($module_params['title'])) { 
			$smarty->assign('tpl_module_title',tra($module_params['title'])); 
		} else {
			$smarty->clear_assign('tpl_module_title');
		}
		$smarty->assign_by_ref('module_rows',$module_rows);
		$smarty->assign_by_ref('module_params', $module_params); // module code can unassign this if it wants to hide params

			if (file_exists($phpfile)) {
				include ($phpfile);
			}

			$template_file = 'templates/' . $template;
			if (file_exists($template_file)) {
				$out = $smarty->fetch($template);
			} else {
				if ($tikilib->is_user_module($module)) {
					$info = $tikilib->get_user_module($module);

					$smarty->assign_by_ref('user_title', $info["title"]);
					$smarty->assign_by_ref('user_data', $info["data"]);
					$out = $smarty->fetch('modules/user_module.tpl');
				}
			}
		$smarty->clear_assign('module_params'); // ensure params not available outside current module
//			if (!file_exists($nocache)) {
//				$fp = fopen($cachefile, "w+");
//				fwrite($fp, $data, strlen($data));
//				fclose ($fp);
//			}
//		} else {
//			$fp = fopen($cachefile, "r");
//			$out = fread($fp, filesize($cachefile));
//			fclose ($fp);
//		}

//		$out = eregi_replace("\n", "", $out);
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
		$data = "<div class=\"highlight\">" . tra("Sorry no such module"). "<br /><b>$module</b></div>" . $data;
	}

	return $data;
}

?>
