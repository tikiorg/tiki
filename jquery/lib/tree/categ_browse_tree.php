<?php
/** \file
 * $Id: /cvsroot/tikiwiki/tiki/lib/tree/categ_browse_tree.php,v 1.6 2006-10-26 11:23:27 luciash Exp $
 *
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
		
		$r = '<ul class="tree root">'."\n";

		$r .= $this->make_tree_r($rootid, $ar);
		// $debugger->var_dump('$r');
		// return tree with java script block that opens the nodes as remembered in cookies
		return $r . "</ul>\n<script type='text/javascript'>\n" . $this->jsscriptblock . "\n</script>\n";
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
	
	function node_start_code($nodeinfo) {
		return "\t" . '<li class="treenode">';
	}

	//
	function node_flipper_code($nodeinfo) {
		$this->itemID = $this->prefix . 'id' . $nodeinfo["id"];

		$this->jsscriptblock .= "setFlipWithSign('" . $this->itemID . "'); ";
		return '<a class="link" id="flipper' . $this->itemID . '" href="javascript:flipWithSign(\'' . $this->itemID . '\')">[+]</a>&nbsp;';
	}

	//
	function node_data_start_code($nodeinfo) {
		return '<!-- START_NODE_DATA -->';
	}

	//
	function node_data_end_code($nodeinfo) {
		return '<!-- END_NODE_DATA -->'."\n";
	}

	//
	function node_child_start_code($nodeinfo) {
		return '<ul class="tree" id="' . $this->itemID . '" style="display: none;">';
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

?>
