<?php
/*
 * $Header: /cvsroot/tikiwiki/_mods/wiki-plugins/mouseover/wiki-plugins/wikiplugin_mouseover.php,v 1.2 2008-03-17 17:59:19 sylvieg Exp $
 * PLugin mouseover - See documentation http://www.bosrup.com/web/overlib/?Documentation
 */
function wikiplugin_mouseover_help() {
	return tra("Create a mouseover feature on some text").":<br />~np~{MOUSEOVER(url=url,text=text,parse=y,width=300,height=300, sticky=>y,left=y,right=n,center=n,above=n,below=y,offsetx=0,offsety=0,sticky=0|1)}".tra('text')."{MOUSEOVER}~/np~";
}
function wikiplugin_mouseover($data, $params) {
	global $smarty, $tikilib;
	extract ($params,EXTR_SKIP);
	$html = '';
	if (empty($url)) {
		$url = 'javascript:void()';
	}
	if (!isset($text))
		$text = '';
	if (!$smarty->get_template_vars('overlib_loaded')) {
		$html .= '<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>';
		$html .= '<script type="text/javascript" src="lib/overlib.js"></script>';
		$smarty->assign('overlib_loaded',1);
	}
	$html .= "<a href='$url'";
	if (!empty($parse) && ($parse == 'y' || $parse == '1')) {
		$text = $tikilib->parse_data($text);
		$text = substr($text, 0, -1);// do not ask me why - but the parsing adds a CR
	}
	$text = preg_replace('/\r\n/', '<br />', $text);
	$html .= " onmouseover=\"return overlib('".str_replace("'", "\'", htmlspecialchars($text))."'";
	foreach ($params as $param=>$value) {
		$p = strtoupper($param);
		if ($p != 'URL' && $p != 'TEXT' && $p != 'PARSE') {
			if ((!empty($value) || $value != 'n') && ($p == 'STICKY' || $p == 'LEFT' || $p == 'RIGHT' || $p == 'CENTER' || $p == 'ABOVE'  || $p == 'BELOW'  || $p == 'AUTOSTATUS' || $p == 'AUTOSTATUSCAP' || $p == 'HAUTO' || $p == 'VAUTO' || $p == 'CLOSECLICK' || $p == 'FULLHTML' || $p == 'CSSOFF' || $p == 'CSSSTYLE' || $p == 'CSSCLASS' || $p == 'NOCLOSE')) {
				$html .= ','.$p;
			} else {
				$html .= ','.$p;
				$html .= ','.$value;	
			}
		}
	}
	$html .= ");\" onmouseout='nd();' >";
	$html .= "$data</a>";

	return $html;
}
?>