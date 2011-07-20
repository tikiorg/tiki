<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/** \file
 * \brief Categories browse tree
 *
 * \author zaufi@sendmail.ru
 * \enhanced by luci@sh.ground.cz
 *
 */
require_once ('lib/tree/tree.php');

/**
 * \brief Class to render categories browse tree
 */
class CatBrowseTreeMaker extends TreeMaker
{
	/// Generated ID (private usage only)
	var $itemID;

	/// Constructor
	function CatBrowseTreeMaker($prefix) {
		$this->TreeMaker($prefix);
	}

	/// Generate HTML code for tree. Need to redefine to add javascript cookies block
	function make_tree($rootid, $ar) {
		global $headerlib, $prefs;

		if ($prefs['mobile_feature'] === 'y' && $prefs['mobile_mode'] === 'y') {
			$r = '<ul class="tree root" data-role="listview" data-inset="true">'."\n";
		} else {
			$r = '<ul class="tree root">'."\n";
		}

		$r .= $this->make_tree_r($rootid, $ar) . "</ul>\n";

		// java script block that opens the nodes as remembered in cookies
		$headerlib->add_jq_onready('$(".tree.root:not(.init)").categ_browse_tree().addClass("init")');
		
		// return tree
		return $r;
	}

	//
	// Change default (no code 'cept user data) generation behaviour
	//  
	// Need to generate:
	//
	// [indent = <tabulator>]
	// [node start = <li class="treenode">]
	//  [node data start]
	//   [flipper] +/- link to flip
	//   [node child start = <ul class="tree">]
	//    [child's code]
	//   [node child end = </ul>]
	//  [node data end]
	// [node end = </li>]
	//
	//
	//
	function indent($nodeinfo) {
		return "\t\t";
	}
	
	function node_start_code_flip($nodeinfo, $count=0) {
		return "\t" . '<li class="treenode withflip ' . (($count % 2) ? 'odd' : 'even') . '">';
	}

	function node_start_code($nodeinfo, $count=0) {
		return "\t" . '<li class="treenode ' . (($count % 2) ? 'odd' : 'even') . '">';
	}

	//
	function node_flipper_code($nodeinfo) {
		return '';
	}

	//
	function node_data_start_code($nodeinfo) {
		return '';
	}

	//
	function node_data_end_code($nodeinfo) {
		return "\n";
	}

	//
	function node_child_start_code($nodeinfo) {
		$style = getCookie($nodeinfo['id'], $this->prefix) !== 'o' ? ' style="display:none"' : '';
		return '<ul class="tree" data-catid="' . $nodeinfo['id'] .
			   		'" data-prefix="' . $this->prefix . '"' . $style .'>';
	}

	//
	function node_child_end_code($nodeinfo) {
		return '</ul>';
	}

	//
	function node_end_code($nodeinfo) {
		return "\t" . '</li>';
	}
}
