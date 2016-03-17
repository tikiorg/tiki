<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}

/**
 * Class Table_Code_MainOptions
 *
 * For the options within the main section of the Tablesorter jQuery function
 *
 * @package Tiki
 * @subpackage Table
 * @uses Table_Code_Manager
 */
class Table_Code_MainOptions extends Table_Code_Manager
{

	/**
	 * Generates the code for the main options
	 */
	public function setCode()
	{
		$mo = array();

		$mo[] = 'showProcessing: true';

		/***  onRenderHeader option - change html elements before table renders. Repeated for each column. ***/
		$orh = array();
		/* First handle column-specific code since the array index is used for the column number */
		foreach (parent::$s['columns'] as $col => $info) {
			//turn off column resizing per settings
			if (isset($info['resizable']) && $info['resizable'] === false) {
				$allcols[$col]['addClass'][] = 'resizable-false';
			}
			//row grouping and sorter settings
			if (parent::$sorts && parent::$sortcol) {
				//row grouping setting
				if (parent::$group) {
					if (!empty($info['sort']['group'])) {
						$allcols[$col]['addClass'][] = 'group-' . $info['sort']['group'];
					} else {
						$allcols[$col]['addClass'][] = 'group-false';
					}
				}
				if (!empty($info['sort']['group']) && parent::$group !== false) {
					$allcols[$col]['addClass'][] = 'group-' . $info['sort']['group'];
				}
				if (isset($info['sort']['type']) && $info['sort']['type'] !== true) {
					//add class for sort data type or for no sort
					$sclass = $info['sort']['type'] === false ? 'false' : $info['sort']['type'];
					$allcols[$col]['addClass'][] = 'sorter-' . $sclass;
				}
			}
			//filters
			if (parent::$filters && parent::$filtercol) {
				//set filter to false for no filter
				if (isset($info['filter']['type']) && $info['filter']['type'] === false) {
					$allcols[$col]['addClass'][] = 'filter-false';
				} else {
					//add placeholders
					if (isset($info['filter']['placeholder'])) {
						$allcols[$col]['data']['placeholder'] = $info['filter']['placeholder'];
					}
				}
			}
			//column select
			if (parent::$s['colselect']['type'] === true) {
				if (isset($info['priority'])) {
					$allcols[$col]['attr']['data-priority'] = $info['priority'];
				}
			}
		}
		unset($col, $info);
		//process columns
		if (count($allcols) > 0) {
			foreach($allcols as $col => $info) {
				if (parent::$usecolselector) {
					$orh[$col] = 'if (id == \'' . substr($col,1) . '\'){';
				} else {
					$orh[$col] = 'if (index == ' . $col . '){';
				}
				$orh[$col] .= '$(this)';
				foreach($info as $attr => $val) {
					if ($attr == 'addClass') {
						$args = implode(' ',$val);
					} else {
						foreach($info[$attr] as $type => $val2) {
							$args = $type . '\',\'' . $val2;
						}
					}
					$orh[$col] .= '.' . $attr . '(\'' . $args . '\')';
				}
				$orh[$col] .= ';}';
			}
			unset($col, $info);
		}

		/* Handle code that applies to all columns now that the array index is not important*/
		//get rid of self-links
		if (isset(parent::$s['selflinks']) && parent::$s['selflinks']) {
			$orh[] = '$(this).find(\'a\').replaceWith(\'<span>\' + $(this).find(\'a\').text() + \'</span>\');';
		}
		//no sort on all columns
		if (!parent::$sorts) {
			$orh[] = '$(this).addClass(\'sorter-false\');';
		}
		if (count($orh) > 0) {
			array_unshift($mo, 'headerTemplate: \'{content} {icon}\'');
			if (parent::$usecolselector) {
				array_unshift($orh, 'var id = $(this).attr(\'id\');');
			}
			$mo[] = $this->iterate($orh, 'onRenderHeader: function(index){', $this->nt2 . '}', $this->nt3, '', '');
		}
		/***  end onRenderHeader section ***/

		/*** widgets ***/
		//standard ones
		$w[] = 'stickyHeaders';
		//only fancytable uses this and it is set in wikiplugin_fancytable.php
		//other tables don't show up full width due to use of table-responsive class in wrapper div
		if (isset(parent::$s['resizable']) && parent::$s['resizable']) {
			$w[] = 'resizable';
		}
		if (parent::$group) {
			$w[] = 'group';
		}
		//saveSort
		if (isset(parent::$s['sorts']['type']) && strpos(parent::$s['sorts']['type'], 'save') !== false) {
			$w[] = 'saveSort';
		}
		//filter
		if (parent::$filters) {
			$w[] = 'filter';
		}
		//pager
		if (parent::$pager) {
			$w[] = 'pager';
		}
		//column selector
		if (parent::$s['colselect']) {
			$w[] = 'columnSelector';
		}
		//math
		if (parent::$math || parent::$mathcol) {
			$w[] = 'math';
		}
		if (count($w) > 0) {
			$mo[] = $this->iterate($w, 'widgets : [', ']', '\'', '\'', ',');
		}
		/*** end widget section ***/
		//debug - uncomment the line below to show log of events in the browser console
//		$mo[] = 'debug: true';

		//server side sorting
		if (parent::$sorts && parent::$ajax) {
			$mo[] = 'serverSideSorting: true';
		}

		//Turn multi-column sort off (on by default by shift-clicking column headers)
		if (isset(parent::$s['sorts']['multisort']) && parent::$s['sorts']['multisort'] === false) {
			$mo[] =  'sortMultiSortKey : \'none\'';
		}

		//Sort list
		if (parent::$sorts && parent::$sortcol) {
			$sl = '';
			$i = 0;
			foreach (parent::$s['columns'] as $col => $info) {
				$info = !empty($info['sort']) ? $info['sort'] : [];
				$colpointer =  parent::$usecolselector ? $i : $col;
				if (!empty($info['dir'])) {
					if ($info['dir'] === 'asc') {
						$sl[] = $colpointer . ',' . '0';
					} elseif ($info['dir'] === 'desc') {
						$sl[] = $colpointer . ',' . '1';
					}
				}
				$i++;
			}
			unset($col, $info);
			if (is_array($sl)) {
				$mo[] = $this->iterate($sl, 'sortList : [', ']', '[', ']', ',');
			}
		}

		//tiki popover needs to be re-applied due to late loading of tablesorter html
		$p[] = $this->nt3 . '$(document).tiki_popover();';
		$mo[] = $this->iterate($p, 'initialized: function(table){', $this->nt2 . '}', '', '', '');

		//Sort image attribute
		if (!empty(parent::$s['sorts']['imgattr'])) {
			$mo[] = 'imgAttr: \'title\'';
		}

		//process main options and add to overall code
		if (count($mo) > 0) {
			$code = $this->iterate($mo, '', '', $this->nt2, '');
			parent::$code[self::$level1][self::$level2] = $code;
		}
	}


}
