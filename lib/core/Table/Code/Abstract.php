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
	protected static $s;
	public static $id;
	public static $tid;
	protected static $sorts;
	protected static $sortcol;
	protected static $filters;
	protected static $filtercol;
	protected static $math;
	protected static $mathcol;
	protected static $usecolselector;
	protected static $group;
	protected static $pager;
	protected static $ajax;
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
		$class = get_class($this);
		if ($class == 'Table_Code_Manager') {
			self::$s = $settings;
			self::$id = $settings['id'] . '-div';
			self::$tid = 'table#' . $settings['id'];
			//overall sort on unless sort type set to false
			self::$sorts = isset($settings['sorts']['type']) && $settings['sorts']['type'] === false ? false : true;
			self::$sortcol = isset(self::$s['columns']) && count(array_column(self::$s['columns'], 'sort')) > 0;
			//filter, group, pager and ajax off unless type is set and is not false
			self::$filters = empty($settings['filters']['type']) ? false : true;
			self::$filtercol = isset(self::$s['columns']) && count(array_column(self::$s['columns'], 'filter')) > 0;
			self::$math = empty($settings['math']) ? false : true;
			//whether to use array index to identify columns or a selector (id, class, etc.)
			//generally index used for plugins where columns are set by user and selectors are used with tables with
			//smarty templates to keep from recreating tpl logic that determines which columns are shown
			self::$usecolselector = !isset(self::$s['usecolselector']) || self::$s['usecolselector'] !== false;
			self::$pager = empty($settings['pager']['type']) ? false : true;
			global $prefs;
			self::$ajax = $settings['ajax']['type'] === true && $prefs['feature_ajax'] === 'y';
			self::$group = self::$sorts && isset($settings['sorts']['group']) && $settings['sorts']['group'] === true;
		}
	}

	/**
	 * Used by classes extending this class to set the code for the section handled by the extended class
	 */
	public function setCode()
	{
	}

	/**
	 * Used by some sub-classes to generate array of code to add to the parent class
	 */
	protected function getOptionArray()
	{
	}


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
		// if $data is just empty, count($data) equals 1. So need to check for type.
		if (!is_array($data)) {
			$ret = $start. $before. $after. $finish;
			return $ret;
		}
		
		$c = count($data);
		$i = 0;
		$ret = '';
		if ($c > 0) {
			$ret .= $start;
			foreach ($data as $value) {
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
