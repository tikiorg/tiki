<?php

// Displays a module inlined in page
// Parameters: module name
// Example:
// {MODULE(module=>logged_users,align=>left | right)}
// {/MODULE}

function wikiplugin_module($data,$params) {
  global $tikilib, $cache_time, $smarty;
	$out = '';
  extract($params);
  if(!isset($module)) {$module='last_modif_pages';}
  if(!isset($align)) {$align='left';}
  //If you want line numbering use something like this:
  //$lines = explode("\n",$code);
  //print_r($lines);
	$cachefile = 'modules/cache/mod-'.$module.'.tpl.cache';
	$phpfile = 'modules/mod-'.$module.'.php';
	$template= 'modules/mod-'.$module.'.tpl';
	$nocache= 'templates/modules/mod-'.$module.'.tpl.nocache';
	if((!file_exists($cachefile)) || (file_exists($nocache)) || ( (time() - filemtime($cachefile))>$cache_time )){
		if(file_exists($phpfile)) {
			include_once($phpfile);
		}
		$template_file = 'templates/'.$template;
		if(file_exists($template_file)) {
			$out = $smarty->fetch($template);
		} else {
			if($tikilib->is_user_module($module)) {
				$info = $tikilib->get_user_module($module);
				$smarty->assign_by_ref('user_title',$info["title"]);
				$smarty->assign_by_ref('user_data',$info["data"]);
				$out = $smarty->fetch('modules/user_module.tpl');
			}
		}
		$fp = fopen($cachefile,"w+");
		fwrite($fp,$data,strlen($data));
		fclose($fp);
	} else {
		$fp = fopen($cachefile,"r");
		$out = fread($fp,filesize($cachefile));
		fclose($fp);
	}
	$out = eregi_replace("\n","",$out);
	if ($out) {
  	$data = "<div style='float:$align;'>$out</div>".$data;
	} else {
		$data = "<div style='float:$align;color:#AA2200;'>".tra("Sorry no such module")."<br/><b>$module</b></div>".$data;
	}
	return $data;
}
?>
