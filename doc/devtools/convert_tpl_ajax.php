<?php
/* $Id$
 * 
 * simple script to convert html anchors in smarty templates to self_links for ajax (tiki 6)
 * couldn't work out a clever enough regexp so trying in php
 *
 * ***** N.B. Move into root of tiki and run in a browser ******
 * 
 */

// just in case
require_once ('tiki-setup.php');
$access->check_permission('tiki_p_admin');

$tpl = $_GET['tpl'];

if (empty($tpl)) {
	$access->display_error('', 'no tpl');
}

function replace_with_self_links($original, $template_base) {

	preg_match_all('/<a.*?\s*href=[^\.]*\.php[^>]*?>(.*?)<\/a>/mi', $original, $phplinks);
	$replacements = array();

	for($j = 0; $j < count($phplinks[0]); $j++) {
		$ahref = $phplinks[0][$j];
		preg_match_all('/([^=\s]*?)="([^"]*?)"/i', $ahref, $attrs);
		
		for($i = 0; $i < count($attrs[1]); $i++) {
			
			$str = '{self_link ';
			if (strtolower($attrs[1][$i]) == 'href') {
				$query = parse_url(urldecode(str_replace('&amp;', '&', $attrs[2][$i])));
				if ($query['path'] != $template_base . '.php') {
					$str .= ' _script="' . $query['path'] . '" ';
				}
				$str .= implode(' ', explode('&', $query['query'])) . ' ';
			} else {

				$str .= '_' . $attrs[1][$i] . '=' . $attrs[2][$i] . ' ';
			}
			$str = trim($str) . '}' . $phplinks[1][$j] . '{/self_link}';
				
			$replacements[] = $str;
		}

		$replaced = str_replace($phplinks[0], $replacements, $original);
	}
	return $replaced;
}

$markup = file_get_contents('templates/' . $tpl . '.tpl');
$checked = isset($_REQUEST['toggle']) ? ' checked=\'checked\'' : '';
if (!empty($checked)) {
	$markup = replace_with_self_links($markup, $tpl);
}

// cheating - lazy ;)
$form = str_replace("\n", '', "<form action='#'>
<label for='toggle'>Show replacements:</label>
<input type='checkbox' id='toggle' name='toggle' onclick='this.form.submit();'$checked />
<input type='hidden' name='tpl' value='$tpl' />
</form>");

$headerlib->add_jq_onready(<<<JS
\$jq('#page-bar, .navbar, .titletips, h2').hide();
\$jq('a.pagetitle').text('$tpl (tpl)').parent().after(
	\$jq("$form")
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
