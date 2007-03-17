<?php

require_once ('lib/tree/tree.php');

/**
 * \brief Class to render layer list browse tree
 */
class layerTreeMaker extends TreeMaker {
	/// Collect javascript cookie set code (internaly used after make_tree() method)
	var $jsscriptblock;

	/// Generated ID (private usage only)
	var $itemID;

	/// Constructor
	function CatLayerTreeMaker($prefix) {
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
		return '<div class="treenode"><table width=100%><tr>';
	}

	//
	function node_flipper_code($nodeinfo) {
		$this->itemID = $this->prefix . 'id' . $nodeinfo["id"];

		$this->jsscriptblock .= "setFlipWithSign('" . $this->itemID . "'); ";
		return '<td align="left" width="20"><a class="catname" title="' . tra(
			'child categories'). ': ' . $nodeinfo["layergroup"] . ', ' . tra('layers in category'). ': ' . $nodeinfo["layers"] . '" id="flipper' . $this->itemID . '" href="javascript:flipWithSign(\'' . $this->itemID . '\')">[+]</a></td>';
	}

	//
	function node_data_start_code($nodeinfo) {
		return '<td align="left">';
	}

	//
	function node_data_end_code($nodeinfo) {
		return '</td><td width=20% align="right">' .$nodeinfo["perm"] ."&nbsp;".$nodeinfo["edit"] ."&nbsp;". $nodeinfo["remove"] . '</td></tr></table></div>';
	}

	//
	function node_child_start_code($nodeinfo) {
		return '<div class="tree" id="' . $this->itemID . '" style=" text-align: left; display: none;">';
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
