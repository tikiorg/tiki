<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
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
 * For the options within the main section of the Tablesorter jQuery code
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
		$orh = array();
		//onRenderHeader option - change html elements before table renders
		if ((isset($this->s['selflinks']) && $this->s['selflinks']) || !$this->sort
			|| ($this->sort && is_array($this->s['sort']['columns'])))
		{
			$mo[] = 'headerTemplate: \'{content}\'';
			//remove self-links
			if (isset($this->s['selflinks']) && $this->s['selflinks']) {
				$orh[] = '$(this).find(\'a\').replaceWith($(this).find(\'a\').text());';
			}
			//no sort on all columns
			if (!$this->sort) {
				$orh[] = '$(\'table#' . $this->id . ' th\').addClass(\'sorter-false\');';
			}
			//row grouping and sorter settings
			if ($this->sort && is_array($this->s['sort']['columns'])) {
				foreach ($this->s['sort']['columns'] as $col => $info) {
					if (!empty($info['group'])) {
						$orh[] = '$(\'table#' . $this->id . ' th:eq(' . $col . ')\').addClass(\'group-'
								. $info['group'] . '\');';
					}
					if (!empty($info['type']) && $info['type'] !== true) {
						$orh[] = '$(\'table#' . $this->id . ' th:eq(' . $col . ')\').addClass(\'sorter-'
								. $info['type'] . '\');';
					}
				}
			}
			if (count($orh) > 0) {
				$mo[] = $this->iterate($orh, 'onRenderHeader: function(index){', $this->nt2 . '}', $this->nt3, '', '');
			}
		}

		//*** widgets ***//
		//standard ones
		$w[] = 'zebra';
		$w[] = 'stickyHeaders';
		if (!isset($this->s['sort']['group']) || $this->s['sort']['group'] !== false) {
			$w[] = 'group';
		}

		//saveSort
		if (isset($this->s['sort']['type']) && strpos($this->s['sort']['type'], 'save') !== false) {
			$w[] = 'saveSort';
		}
		//filter
		if ($this->filters) {
			$w[] = 'filter';
		}
		if (count($w) > 0) {
			$mo[] = $this->iterate($w, 'widgets : [', ']', '\'', '\'', ',');
		}


		//Show processing
		$mo[] = 'showProcessing: true';

		//Turn multi-column sort off (on by default by pressing shift-clicking column headers)
		if (isset($this->s['sort']['multisort']) && $this->s['sort']['multisort'] === false) {
			$mo[] =  'sortMultiSortKey : \'none\'';
		}

		//Sort list
		if ($this->sort && is_array($this->s['sort']['columns'])) {
			$sl = '';
			foreach ($this->s['sort']['columns'] as $col => $info) {
				if ($info['type'] === 'asc') {
					$sl[] = $col . ',' . '0';
				} elseif($info['type'] === 'desc') {
					$sl[] = $col . ',' . '1';
				}
			}
			if (is_array($sl)) {
				$mo[] = $this->iterate($sl, 'sortList : [', ']', '[', ']', ',');
			}
		}
		if (count($mo) > 0) {
			$code = $this->iterate($mo, '', '', $this->nt2, '');
			parent::$code[self::$level1][self::$level2] = $code;
		}
	}


}