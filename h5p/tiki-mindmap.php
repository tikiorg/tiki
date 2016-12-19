<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once 'tiki-setup.php';
$wikilib = TikiLib::lib('wiki');
$access->check_feature('feature_wiki_mindmap');
if (!file_exists('files/visorFreemind.swf')) {
	$smarty->assign('missing', 'files/visorFreemind.swf');
	$smarty->assign('mid', 'tiki-mindmap.tpl');
	$smarty->display("tiki.tpl");
	exit;
}
if (isset($_REQUEST['export'])) { // {{{
	// Output the page relations in WikiMindMap format (derived from Freemind)
	$dom = new DOMDocument;
	$dom->appendChild($root = $dom->createElement('map'));
	$root->setAttribute('version', '0.8.0');
    /**
     * @param $dom
     * @param $text
     * @param bool $link
     * @return mixed
     */
    function create_node($dom, $text, $link = true)
	{
		$wikilib = TikiLib::lib('wiki');
		$node = $dom->createElement('node');
		$node->setAttribute('TEXT', $text);
		$node->setAttribute('STYLE', 'bubble');
		if ($link) {
			$node->setAttribute('WIKILINK', $wikilib->sefurl($text));
			$node->setAttribute('MMLINK', 'tiki-mindmap.php?page=' . urlencode($text));
		}
		return $node;
	}

    /**
     * @param $node
     * @param $pageName
     * @param int $remainingLevels
     * @param array $pages
     */
    function populate_node($node, $pageName, $remainingLevels = 3, $pages = array())
	{
		global $user;
		$wikilib = TikiLib::lib('wiki');
		$tikilib = TikiLib::lib('tiki');
		$child = $wikilib->wiki_get_neighbours($pageName);
		$child = array_diff($child, $pages);
		foreach ($child as $page) {
			if (!$tikilib->user_has_perm_on_object($user, $page, 'wiki page', 'tiki_p_view')) continue;
			$node->appendChild($new = create_node($node->ownerDocument, $page));
			if ($remainingLevels != 0) populate_node($new, $page, $remainingLevels - 1, array_merge($pages, array($page, $pageName)));
			else $new->setAttribute('STYLE', 'fork');
		}
		if (count($child) == 0) $node->setAttribute('STYLE', 'fork');
	}
	if (!isset($_REQUEST['export'])) {
		$root->appendChild(create_node($dom, tra('No page provided.'), false));
	} elseif (!$tikilib->page_exists($_REQUEST['export'])) {
		$root->appendChild(create_node($dom, tr('Page "%0" does not exist', $_REQUEST['export']), false));
	} else {
		$root->appendChild($parent = create_node($dom, $_REQUEST['export']));
		populate_node($parent, $_REQUEST['export']);
	}
	header('Content-Type: text/xml');
	echo $dom->saveXML();
	exit;
} // }}}
$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : $prefs['wikiHomePage'];
$ePage = urlencode($page);
$code = $tikilib->embed_flash(array('movie' => 'files/visorFreemind.swf', 'bgcolor' => '#cccccc', 'width' => 800, 'height' => 800,), '', array('openUrl' => '_blank', 'initLoadFile' => "tiki-mindmap.php?export={$ePage}", 'startCollapsedToLevel' => 1, 'mainNodeShape' => 'bubble',));
$smarty->assign('mindmap', $code);
$smarty->assign('page', $page);
$smarty->assign('mid', 'tiki-mindmap.tpl');
$smarty->display("tiki.tpl");
