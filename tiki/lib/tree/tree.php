<?php
/** \file
 * $Header: /cvsroot/tikiwiki/tiki/lib/tree/tree.php,v 1.3 2003-08-07 04:34:13 rossta Exp $
 *
 * \brief Base tree maker
 *
 * \author zaufi@sendmail.ru
 *
 */
require_once ('lib/debug/debugger.php');

/**
 * \brief Base class for all tree makers
 *
 * Define base interface and provide common algotithm for tree generation
 *
 * Format of element in array for make_tree() call:
 *  id     => number of ID of current node
 *  parent => number of ID of parant node
 *  data   => user provided data to be placed as node text
 *
 */
class TreeMaker {
	/// Unique prefix for cookies generated for this tree
	var $prefix;

	/// Constructor
	function TreeMaker($prefix) {
		$this->prefix = $prefix;
	}

	/// Generate HTML code for tree
	function make_tree($rootid, $ar) {
		return $this->make_tree_r($rootid, $ar);
	}

	/// Recursive make (do not call directly)
	function make_tree_r($rootid, &$ar) {
		global $debugger;

		$debugger->msg("TreeMaker::make_tree_r: Root ID=" . $rootid);
		$result = '';

		if (count($ar) > 0) {
			$cli = array();

			$tmp = array();

			foreach ($ar as $i)
				if ($rootid == $i["parent"])
					$cli[] = $i;
				else
					$tmp[] = $i;

			//
			foreach ($cli as $i) {
				$child_result = $this->make_tree_r($i["id"], $tmp);

				$have_childs = (strlen($child_result) > 0);
				//
				// NOTE: The main rule is to call all methods in 
				//       stricty defined order!!
				//
				$nsc = $this->node_start_code($i);

				$flipper = '';

				if ($have_childs)
					$flipper = $this->node_flipper_code($i);

				$ndsc = $this->node_data_start_code($i);
				$ndec = $this->node_data_end_code($i);

				$ncsc = '';
				$ncec = '';

				if ($have_childs) {
					$ncsc = $this->node_child_start_code($i);

					$ncec = $this->node_child_end_code($i);
				}

				$nec = $this->node_end_code($i);
				// Form result
				$result .= $nsc . $flipper . $ndsc . $i["data"] . $ndec . $ncsc . $child_result . $ncec . $nec;
			}
		}

		return $result;
	}
	/**
	 * To change behavior (look and feel :) of generated tree
	 * it is enough to redefine follwing methods..
	 * (thanx that PHP have implicit vurtual functions :)
	 *
	 * General layout of generated tree code looks like this:
	 *
	 * [node start code]
	 *   [node flipper code]       (1)
	 *   [node data start code]
	 *   [node data end code]
	 *   [node childs start code]  (1)
	 *   [node childs end code]    (1)
	 * [node end code]
	 *
	 * (1) -- this code will be generated if node have childs
	 *
	 * NOTE: Methods called exactly in that order. This fact can be
	 *       (and actualy do) used by child classes to define
	 *       and use some variables depends on pervious call...
	 *
	 * NOTE: This is abstract base class... it doing nothig
	 *       except defining algirithm...
	 *       So to make smth other use inheritance and redifine
	 *       corresponding function :)
	 */
	function node_start_code($nodeinfo) {
		return '';
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
		return '';
	}

	//
	function node_child_start_code($nodeinfo) {
		return '';
	}

	//
	function node_child_end_code($nodeinfo) {
		return '';
	}

	//
	function node_end_code($nodeinfo) {
		return '';
	}
}

?>