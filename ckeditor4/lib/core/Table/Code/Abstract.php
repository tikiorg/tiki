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
 * Class Table_Code_Abstract
 *
 * Abstract class for generating the jQuery Tablesorter code to be added to the header
 * The classes that extend this class are organized by the major sections of the Tablesorter code
 *
 * @package Tiki
 * @subpackage Table
 */
class Table_Code_Abstract
{
	public $id;
	protected $s;
	protected $filters;
	protected $sort;
	protected $pager;
	protected $ajax;
	public static $code = '';
	protected static $level1;
	protected static $level2;
	protected $subclasses;
	protected $t = "\t";
	protected $nt = "\n\t";
	protected $nt2 = "\n\t\t";
	protected $nt3 = "\n\t\t\t";
	protected $nt4 = "\n\t\t\t\t";
	protected $nt5 = "\n\t\t\t\t\t";

	/**
	 * Set some shortened properties
	 *
	 * @param $settings
	 */
	public function __construct($settings)
	{
		global $prefs;
		$this->s = $settings;
		$this->id = $settings['id'];

		//overall sort on unless sort type set to false
		$this->sort = isset($settings['sort']['type']) && $settings['sort']['type'] === false ? false : true;

		//filter, pager and ajax off unless type is set and is not false
		$this->filters = empty($settings['filters']['type']) ? false : true;
		$this->pager = empty($settings['pager']['type']) ? false : true;
		$this->ajax = empty($settings['ajax']) || $prefs['feature_ajax'] === 'n'? false : true;
	}

	/**
	 * Used by classes extending this class to set the code for the section handled by the extended class
	 */
	public function setCode() {}

	/**
	 * Utility to generate lines of code within a section
	 *
	 * @param array  $data			raw variable data
	 * @param string $start			code at the overall start of the section
	 * @param string $finish		code at the overall end of the section
	 * @param string $before		code just before an individual option or line
	 * @param string $after			code just after an individual option or line
	 * @param string $separator		separator between individual options or lines
	 *
	 * @return string
	 */
	protected function iterate(array $data, $start = '', $finish = '', $before = '\'' , $after = '\'', $separator = ', ')
	{
		$c = count($data);
		$i = 0;
		$ret = '';
		if ($c > 0) {
			$ret .= $start;
			foreach($data as $value) {
				$i++;
				$ret .= $before . $value . $after;
				if ($i < $c) {
					$ret .= $separator;
				}
			}
			$ret .= $finish;
		}
		return $ret;
	}
}
