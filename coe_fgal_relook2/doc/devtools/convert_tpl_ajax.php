<?php
/* $Id$
 * 
 * simple script to convert html anchors in smarty templates to self_links for ajax (tiki 6)
 * couldn't work out a clever enough regexp so trying in php
 *
 * ***** N.B. Move into root of tiki and run in a browser  ******
 * 
 * ***** N.B. 2: {if} statements inside anchor attributes  ******
 * ***** will be commented out for later manual correction ******
 */

// just in case
require_once ('tiki-setup.php');
$access->check_permission('tiki_p_admin');
$access->check_feature('javascript_enabled');

$tpl = $_GET['tpl'];

//if (empty($tpl)) {
//	$access->display_error('', 'no tpl');
//}

function replace_with_self_links($original, $template_base) {

	preg_match_all('/<a.*?\s*href=[^\.]*\.php[^>]*?>(.*?)<\/a>/mi', $original, $phplinks);
	$replacements = array();

	for($j = 0; $j < count($phplinks[0]); $j++) {
		$ahref = $phplinks[0][$j];
		preg_match_all('/([^=\s]*?)="([^"]*?)"/i', $ahref, $attrs);
		
		$str = '{self_link ';
		for($i = 0; $i < count($attrs[1]); $i++) {
			
			if (strtolower($attrs[1][$i]) == 'href') {
				$query = parse_url(urldecode(str_replace('&amp;', '&', $attrs[2][$i])));
				if ($query['path'] != $template_base . '.php') {
					$str .= ' _script="' . $query['path'] . '" ';
				}
				$vars = explode('&', $query['query']);
				foreach($vars as &$var) {
					$ar = explode( '=', $var);
					$var = $ar[0] . '=' . process_value( $ar[1] );
				}
				$str .= implode(' ', $vars) . ' ';
			} else {
				$str .= '_' . $attrs[1][$i] . '=' . process_value( $attrs[2][$i] ) . ' ';
			}
		}
		$str = trim($str) . '}' . $phplinks[1][$j] . '{/self_link}';	
		$replacements[] = $str;
		
	}
	$replaced = str_replace($phplinks[0], $replacements, $original);
	return $replaced;
}

function process_value ($var) {
	if (strpos( $var, '{$' ) === 0) {
		$var =  trim( $var, '{}');
		$q = '';
	} else {
		$q = '"';
	}
	// comment out if's inside attributes for manual processing
	$var = preg_replace(array('/\{if\s([^\}]*)\}/i', '/\{else\}/i', '/\{\/if\}/i', '/\{elseif\s([^\}]*)\}/i'),
						array('{*if $1*}',           '{*else*}',    '{*/if*}',     '/{*elseif $1*}/'), $var);
	$var = "$q" . $var . "$q";
	return $var;
}

$markup = file_get_contents('templates/' . $tpl . '.tpl');
$checked = !empty($_REQUEST['toggle']) ? ' checked=\'checked\'' : '';
if (!empty($checked)) {
	$markup = replace_with_self_links($markup, $tpl);
}

 $fp = opendir('templates/');

$tpl_sel = '<select name=\'tpl\' id=\'tpl\' onclick=\'this.form.submit();\'><option>Select tpl</option>';
while(false !== ($f = readdir($fp))) {
	preg_match('/^(.*)\.tpl$/', $f, $m);
	if (count($m) > 0) {
		$tpl_sel .= '<option value=\'' . $m[1] . '\'';
		$tpl_sel .= $m[1] == $tpl ? ' selected=\"selected\">' : '>';
		$tpl_sel .= $m[1] . '.tpl</option>';
	}
}
$tpl_sel .= '</select>';

// cheating - lazy ;)
$form = str_replace("\n", '', "<form action='#'>
<label for='toggle'>Show replacements:</label>
<input type='checkbox' id='toggle' name='toggle' onclick='this.form.submit();'$checked />
$tpl_sel
</form>");

$headerlib->add_jq_onready(<<<JS
\$('#page-bar, .navbar, .titletips, h2').hide();
\$('a.pagetitle').text('$tpl (tpl)').parent().after(
	\$("$form")
);
JS
);

$smarty->assign('source', true);
$smarty->assign('sourced', htmlentities($markup));
$smarty->assign('noHistory', true);
$smarty->assign('info', array('version' => true));

// Display the template
$smarty->assign('mid', 'tiki-pagehistory.tpl');
$smarty->display("tiki.tpl");
