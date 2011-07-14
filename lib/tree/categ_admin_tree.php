<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/** \file
 * \brief Categories browse tree
 *
 * \author zaufi@sendmail.ru
 *
 */
require_once ('lib/tree/tree.php');

/**
 * \brief Class to render categories browse tree
 */
class CatAdminTreeMaker extends TreeMaker
{
	/// Collect javascript cookie set code (internaly used after make_tree() method)
	var $jsscriptblock;

	/// Generated ID (private usage only)
	var $itemID;

	/// Constructor
	function CatAdminTreeMaker($prefix) {
		$this->TreeMaker($prefix);

		$this->jsscriptblock = '';
	}

	/// Generate HTML code for tree. Need to redefine to add javascript cookies block
	function make_tree($rootid, $ar) {
		global $headerlib;
		
		$r = '<ul class="tree root">'."\n";

		$r .= $this->make_tree_r($rootid, $ar) . "</ul>\n";

		// java script block that opens the nodes as remembered in cookies
		$headerlib->add_jq_onready($this->jsscriptblock);
		
		// return tree
		return $r;
	}

	//
	// Change default (no code 'cept user data) generation behaviour
	//  
	// Need to generate:
	//
	// [node start = <div class="treenode"><table><tr>]
	//  [flipper] user data    [edit][del]
	// [node data end = </div>]
	// [node child start = <div class="tree">]
	//   [childs code]
	// [node child end = </div>]
	//
	// Unsymmetrical calls is not important :)
	//
	function node_start_code($nodeinfo) {
		return '<div class="treenode"><table width="100%"><tr>';
	}

	function node_start_code_flip($nodeinfo) {
		return $this->node_start_code($nodeinfo);
	}
	//
	function node_flipper_code($nodeinfo) {
		$this->itemID = $this->prefix . 'id' . $nodeinfo["id"];

		$this->jsscriptblock .= "setFlipWithSign('" . $this->itemID . "'); ";
		return '<td><a class="catname" title="' . tra(
			'child categories'). ': ' . $nodeinfo["children"] . ', ' . tra('objects in category'). ': ' . $nodeinfo["objects"] . '" id="flipper' . $this->itemID . '" href="javascript:flipWithSign(\'' . $this->itemID . '\')">[+]</a></td>';
	}

	//
	function node_data_start_code($nodeinfo) {
		return '<td>';
	}

	//
	function node_data_end_code($nodeinfo) {
		return '</td><td width="10%" align="right">' . $nodeinfo["edit"] . $nodeinfo["remove"] . '</td></tr></table></div>';
	}

	//
	function node_child_start_code($nodeinfo) {
		return '<div class="tree" id="' . $this->itemID . '" style="display: none;">';
	}

	//
	function node_child_end_code($nodeinfo) {
		return '</div>';
	}

	//
	function node_end_code($nodeinfo) {
		return '';
	}
}
