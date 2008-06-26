<?php
// $Id: /cvsroot/tikiwiki/tiki/lib/wiki-plugins/wikiplugin_agentinfo.php,v 1.5.2.1 2007-12-07 12:55:20 pkdille Exp $
// Includes wiki pages listing in a wiki page
// Usage:
// {LISTPAGES(offset=Offset, max=Max Records, sort=Sort Field, find=Find String, initial=First Letter, exact_match=y|n, only_name=y|n, for_list_pages=y|n, only_orphan_pages=y|n, filter+=y|n, only_cant=y|n)}{LISTPAGES}
//

function wikiplugin_listpages_help() {
        $help = tra("Includes wiki pages listing into a wiki page");
        $help .= "<br />";
        $help .= "~np~" 
              . tra("{LISTPAGES(offset=Offset, max=Max Records, sort=Sort Field, find=Find String, initial=First Letter(s), exact_match=y|n, only_name=y|n, only_orphan_pages=y|n, filter=y|n, only_cant=y|n)}{LISTPAGES}") 
              . "~/np~" ;

        return $help;
}

function wikiplugin_listpages($data,$params) {
	global $smarty, $tikilib, $prefs, $tiki_p_view;

	extract($params,EXTR_SKIP);
	if ( $prefs['feature_wiki'] !=  'y' || $prefs['feature_listPages'] != 'y' || $tiki_p_view != 'y' ) {
		//		the feature is disabled or the user can't read wiki pages
		return '';
	}
	if (!isset($offset)) $offset = 0;
	if (!isset($max)) $max = $prefs['maxRecords'];
	if (!isset($sort)) $sort = 'pageName_desc';
	if (!isset($find)) $find = '';
	if (!isset($initial)) $initial = '';

  $exact_match = ( isset($exact_match) && $exact_match == 'y' );
  $only_name = ( isset($only_name) && $only_name == 'y' );
  $only_orphan_pages = ( isset($only_orphan_pages) && $only_orphan_pages == 'y' );
  $filter = ( isset($filter) && $filter == 'y' );
  $only_cant = ( isset($only_cant) && $only_cant == 'y' );

  $for_list_pages = true;
	$listpages = $tikilib->list_pages($offset, $max, $sort, $find, $initial, $exact_match, $only_name, $for_list_pages, $only_orphan_pages, $filter, $only_cant);

	// If there're more records then assign next_offset
	$smarty->assign_by_ref('listpages', $listpages['data']);
	$smarty->assign_by_ref('cant', $listpages['cant']);

	return "~np~ " . $smarty->fetch('tiki-listpages_content.tpl') . " ~/np~";
}
?>
