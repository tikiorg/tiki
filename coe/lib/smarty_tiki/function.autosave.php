<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

require_once("lib/ajax/autosave.php");

function smarty_function_autosave($params, &$smarty)
{
	global $user;
	global $js_script;
	global $prefs;

	if ($prefs['feature_ajax_autosave'] != 'y') {
		return $params['default'];	
	}

	$js_script[$params['id']] = "register_id('".$params['id']."');";
	if (has_autosave($params['id']) and $params['preview'] == 0) {
		$tmp = str_replace("\n","\r\n",get_autosave($params['id']));
		if ( isset($params['test']) && isset($params['default']) && $params['test'] == 'y' and $tmp == $params['default'] ) {
			$smarty->assign('has_autosave','n');
		} else {
			$smarty->assign('has_autosave','y');
		}
	} else {
		$smarty->assign('has_autosave','n');
		$tmp = $params['default'];	
	}
	if ($params['mode'] == 'fck') {
		$editplugin = $prefs['wiki_edit_plugin'];
		$prefs['wiki_edit_plugin'] = 'n';   // and the external link icons
		$parsed = $tikilib->parse_data($tmp,array('absolute_links'=>true, 'noparseplugins'=>true,'noheaderinc'=>false, 'fck' => 'y'));
		$parsed = preg_replace('/<span class=\"img\">(.*?)<\/span>/im','$1', $parsed);          // remove spans round img's
			$parsed = preg_replace("/src=\"img\/smiles\//im","src=\"".$tikiroot."img/smiles/", $parsed);  // fix smiley src's
		$parsed = str_replace(
				array( '{SUP()}', '{SUP}', '{SUB()}', '{SUB}', '<table' ),
				array( '<sup>', '</sup>', '<sub>', '</sub>', '<table border="1"' ),
				$parsed );
		$prefs['wiki_edit_section'] = $secedit;
		$prefs['feature_wiki_ext_icon'] = $exticons;
		$prefs['wiki_edit_plugin'] = $editplugin;
		return $parsed;
	} else {
		return $tmp;
	}
}
