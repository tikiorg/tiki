<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/* 
 * Simple script to convert html anchors in smarty templates to self_links for ajax (tiki 6)
 * couldn't work out a clever enough regexp so trying in php
 *
 * *****                  INSTRUCTIONS                     ******
 * 
 * Move into root of your tiki and open in a browser
 * 		e.g. http://localhost/trunk/convert_tpl_ajax.php
 * 
 * Select a tpl from the drop down and check the "Show replacements" checkbox
 * Copy the resulting source into your favourite text editor and
 * compare with the original (and test lots, obviously) before committing
 * 
 * ***** N.B. 2: {if} statements inside anchor attributes  ******
 * ***** will be commented out for later manual correction ******
 * 
 * ***** N.B. 3: Nested quote marks in Smarty syntax       ******
 * I found one tpl (tiki-pagehistory.tpl) that had links with nested quotes in the hrefs (ik!)
 * e.g. <a href="tiki-rollback.php?page={$page|escape:"url"}&amp;version={$preview}" title="{tr}Rollback{/tr}">{tr}Rollback to this version{/tr}</a>
 * so i had to "manually" replace the "url" params with 'url' before running the script.
 */

// just in case
require_once ('tiki-setup.php');
$access->check_permission('tiki_p_admin');
$access->check_feature('javascript_enabled');

$tpl = $_GET['tpl'];
$count_r = 0;

//if (empty($tpl)) {
//	$access->display_error('', 'no tpl');
//}

function replace_with_self_links($original, $template_base)
{
	global $count_r;

	preg_match_all('/<a.*?\s*href=[^\.]*\.php[^>]*?>(.*?)<\/a>/mi', $original, $phplinks);
	$replacements = array();

	$count_r = count($phplinks[0]);
	for($j = 0; $j < $count_r; $j++) {
		$ahref = $phplinks[0][$j];
		preg_match_all('/([^=\s]*?)="([^"]*?)"/i', $ahref, $attrs);
		
		$str = '{self_link ';
		for($i = 0, $icount_attrs = count($attrs[1]); $i < $icount_attrs; $i++) {
			
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

function process_value ($var)
{
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
<label for='toggle'>" . (empty($_REQUEST['toggle']) ? 'Show replacements' : "Showing $count_r replacements" ) . ":</label>
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
