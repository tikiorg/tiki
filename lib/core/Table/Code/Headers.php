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
 * Class Table_Code_Headers
 *
 * Sets the headers section of the code, which indicates when no filtering or sorting is to be applied to columns
 *
 * @package Tiki
 * @subpackage Table
 * @uses Table_Code_Manager
 */
class Table_Code_Headers extends Table_Code_Manager
{
	public function setCode()
	{
		//add info to header when sorter or filter = false
		//sorter = false
		$fsorts = $this->getFalses('sort');
		//filter = false
		$ffilters = $this->getFalses('filters');
		$allfalses = array(
			'both' => array_intersect_key($fsorts, $ffilters),
			'sortsonly' => array_diff_key($fsorts, $ffilters),
			'filtersonly' => array_diff_key($ffilters, $fsorts)
		);
		$f = '';
		foreach ($allfalses as $type => $falses) {
			if (is_array($falses) && count($falses) > 0) {
				foreach ($falses as $key => $value) {
					switch ($type) {
						case 'both':
							$f[$key] = $key . ' : {sorter: false, filter: false}';
							break;
						case 'sortsonly':
							$f[$key] = $key . ' : {sorter: false}';
							break;
						case 'filtersonly':
							$f[$key] = $key . ' : {filter: false}';
					}
				}
			}
		}
		asort($f);
		$code = $this->iterate($f, $this->nt2 . 'headers: {', $this->nt2 . '}', $this->nt3, '', ',');
		parent::$code[self::$level1][self::$level2] = $code;
	}

	/**
	 * Utility to extract columns where sort or filtering has been set to false
	 *
	 * @param $type
	 *
	 * @return string
	 */
	private function getFalses($type)
	{
		$cols = isset($this->s[$type]['columns']) ? $this->s[$type]['columns'] : false;
		$falses = '';
		if ($cols) {
			foreach($cols as $key => $value) {
				if ($value['type'] === false) {
					$falses[$key][$type] = false;
				}
			}
		}
		return $falses;
	}
}