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
 * Class Table_Check
 * This is a public class for checking necessary preferences or tablesorter status
 *
 * @package Tiki
 * @subpackage Table
 */
class Table_Check
{
	public static $dir = [0 => 'asc', 1 => 'desc'];

	/**
	 * Checks to see if necessary preferences are set to allow tablesorter to be used either with or without ajax
	 *
	 * @param bool $ajax    if set to true will check that appropriate preference is set to be able to use ajax
	 * @return bool
	 */
	static public function isEnabled($ajax = false)
	{
		global $prefs;
		if ($prefs['javascript_enabled'] === 'y' && $prefs['feature_jquery_tablesorter'] === 'y') {
			if ($ajax === true) {
				if ($prefs['feature_ajax'] === 'y') {
					return true;
				} else {
					return false;
				}
			} else {
				return true;
			}
		} else {
			return false;
		}
	}

	/**
	 * Checks to see whether the file has been accessed through a tablesorter ajax call
	 * @return bool
	 */
	static public function isAjaxCall()
	{
		if (isset($_GET['tsAjax']) && $_GET['tsAjax'] === 'y') {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Checks to see whether the file has been accessed through a tablesorter ajax call with a sort or filter
	 * @return bool
	 */
	static public function isFilterOrSort()
	{
		if (self::isAjaxCall() && ((!empty($_GET['filter']) && is_array($_GET['filter'])
				|| (!empty($_GET['sort']) && is_array($_GET['sort']))))) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Checks to see whether the file has been accessed through a tablesorter ajax call with a filter
	 * @return bool
	 */
	static public function isFilter()
	{
		if (self::isAjaxCall() && !empty($_GET['filter']) && is_array($_GET['filter'])) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Checks to see whether the file has been accessed through a tablesorter ajax call with a sort
	 * @return bool
	 */
	static public function isSort()
	{
		if (self::isAjaxCall() && !empty($_GET['sort']) && is_array($_GET['sort'])) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Utility to convert string entered by user for a parameter setting to an array
	 * @param $paramstr
	 * @return array
	 */
	static public function parseParam ($paramstr)
	{
		if (!empty($paramstr)) {
			$ret = explode('|', $paramstr);
			foreach ($ret as $key => $pipe) {
				$key = trim($key);
				$pipe = trim($pipe);
				$ret[$key] = strpos($pipe, ';') !== false ? explode(';', $pipe) : $pipe;
				if (!is_array($ret[$key])) {
					if (strpos($ret[$key], ':') !== false) {
						$colon = explode(':', $ret[$key]);
						unset($ret[$key]);
						if (trim($colon[1]) == 'nofilter') {
							$ret[$key][$colon[0]] = false;
						} else {
							$ret[$key][$colon[0]] = trim($colon[1]);
						}
					}
				} elseif (is_array($ret[$key])) {
					foreach ($ret[$key] as $key2 => $subparam) {
						$key2 = trim($key);
						$subparam = trim($subparam);
						if (strpos($subparam, ':') !== false) {
							$colon = explode(':', $subparam);
							unset($ret[$key][$key2]);
							if (in_array($colon[0], ['expand', 'option']))
							{
								if (trim($colon[0]) == 'option') {
									$colon[0] = 'options';
								}
								$ret[$key][$colon[0]][] = trim($colon[1]);
							} else {
								$ret[$key][$colon[0]] = trim($colon[1]);
							}
						}
					}
				}
			}
			ksort($ret);
			return $ret;
		} else {
			return $paramstr;
		}
	}

}