<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/** \file
 * \brief Base tree maker
 *
 * \author zaufi@sendmail.ru
 * \enhanced by luci@sh.ground.cz
 *
 */

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

require_once ('lib/debug/debugger.php');

/**
 * \brief Base class for all tree makers
 *
 * Define base interface and provide common algorithm for tree generation
 *
 * Format of element in array for make_tree() call:
 *  id     => number of ID of current node
 *  parent => number of ID of parant node
 *  data   => user provided data to be placed as node text
 *
 */
class TreeMaker
{
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

			$ind = "";
			//
			foreach ($cli as $i) {
				$child_result = $this->make_tree_r($i["id"], $tmp);

				$have_childs = (strlen($child_result) > 0);
				//
				// NOTE: The main rule is to call all methods in 
				//       stricty defined order!
				//
				$nl = "\n";
				$ind .= "\t";
				
				if ($have_childs) {
					$flipper = $this->node_flipper_code($i);
					$nsc = $this->node_start_code_flip($i);
				} else {
					$nsc = $this->node_start_code($i);
					$flipper = '';
				}	

				$ndsc = $this->node_data_start_code($i);
				$ndec = $this->node_data_end_code($i);

				$ncsc = '';
				$ncec = '';

				if ($have_childs) {
					$ncsc = $this->node_child_start_code($i);
					$ncec = $this->node_child_end_code($i);
					$ind .= $this->indent($i);
				}
				
				$nec = $this->node_end_code($i);
				// Form result
				$result .= $nsc . $flipper . $ndsc . $i["data"] . $nl . $ind . $ncsc. $nl . $ind . $ind . $child_result . $ncec . $nl . $ind . $ind . $ndec . $nec . $nl . $ind; // this sort is for lists kind of tree
#				$result .= $nsc . $flipper . $ndsc . $i["data"] . $ndec . $ncsc . $child_result . $ncec . $nec; // this sort is for old div/table kind of tree
			}
		}

		return $result;
	}
	/**
	 * To change behavior (xhtml layout :) of generated tree
	 * it is enough to redefine following methods..
	 * (thanx that PHP have implicit virtual functions :)
	 *
	 * General layout of generated tree code looks like this:
	 *
	 * [indent]
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
	 *       (and actualy it is) used by child classes to define
	 *       and use some variables dependent on previous call...
	 *
	 * NOTE: This is abstract base class... it does nothing
	 *       except defining algorithm...
	 *       So to make smth other use inheritance and redefine
	 *       corresponding function :)
	 */
	function indent($nodeinfo) {
		return '';
	}
	
	function node_start_code($nodeinfo) {
		return '';
	}

	function node_start_code_flip($nodeinfo) {
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
