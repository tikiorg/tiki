<?php
/** \file
 * $Header: /cvsroot/tikiwiki/tiki/lib/tree/categ_browse_tree.php,v 1.4 2004-01-20 06:30:38 mose Exp $
 *
 * \brief Categories browse tree
 *
 * \author zaufi@sendmail.ru
 *
 */
require_once ('lib/tree/tree.php');

/**
 * \brief Class to render categories browse tree
 */
class CatBrowseTreeMaker extends TreeMaker {
	/// Collect javascript cookie set code (internaly used after make_tree() method)
	var $jsscriptblock;

	/// Generated ID (private usage only)
	var $itemID;

	/// Constructor
	function CatBrowseTreeMaker($prefix) {
		$this->TreeMaker($prefix);

		$this->jsscriptblock = '';
	}

	/// Generate HTML code for tree. Need to redefine to add javascript cookies block
	function make_tree($rootid, $ar) {
		global $debugger;

		$r = $this->make_tree_r($rootid, $ar);
		// $debugger->var_dump('$r');
		// return tree with java script block that opens the nodes as remembered in cookies
		return $r . "<script language='Javascript' type='text/javascript'> " . $this->jsscriptblock . " </script>\n";
	}

	//
	// Change default (no code 'cept user data) generation behaviour
	//  
	// Need to generate:
	//
	// [node start = <div class=treenode>]
	//  [flipper] user data
	// [node data end = </div>]
	// [node child start = <div class=tree>]
	//   [childs code]
	// [node child end = </div>]
	//
	// Unsymmetrical calls is not important :)
	//
	function node_start_code($nodeinfo) {
		return '<div class="treenode">&nbsp;';
	}

	//
	function node_flipper_code($nodeinfo) {
		$this->itemID = $this->prefix . 'id' . $nodeinfo["id"];

		$this->jsscriptblock .= "setFlipWithSign('" . $this->itemID . "'); ";
		return '<a class="link" id="flipper' . $this->itemID . '" href="javascript:flipWithSign(\'' . $this->itemID . '\')">[+]</a>&nbsp;';
	}

	//
	function node_data_start_code($nodeinfo) {
		return '&nbsp;&nbsp;';
	}

	//
	function node_data_end_code($nodeinfo) {
		return '</div>';
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

?>
