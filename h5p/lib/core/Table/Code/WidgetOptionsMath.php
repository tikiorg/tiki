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
 * Class Table_Code_WidgetOptionsMath
 *
 * Creates the code for the math widget options portion of the Tablesorter jQuery code
 *
 * @package Tiki
 * @subpackage Table
 * @uses Table_Code_WidgetOptions
 */
class Table_Code_WidgetOptionsMath extends Table_Code_WidgetOptions
{

	protected function getOptionArray()
	{
		if (parent::$math) {
			$format = isset(parent::$s['math']['format']) ? parent::$s['math']['format'] : '###0.00';
			$m[] = 'math_data : \'tsmath\'';
			$m[] = 'math_mask : ' . '\'' . $format . '\'';
			$m[] = 'math_event : \'tablesorter-ready\'';
			$m[] = 'math_none : \'' . tr('n/a') . '\'';
			//page totals by default
			$m[] = 'math_rowFilter : \':not(.filtered):visible\'';
			//ignore
			foreach (parent::$s['columns'] as $col => $info) {
				if (isset($info['math']['ignore']) && $info['math']['ignore']) {
					$ignore[] = $col;
				}
			}
			if (isset($ignore) && count($ignore) > 0) {
				$m[] = $this->iterate($ignore, 'math_ignore : [', ']', '', '', ',');
			} else {
				$m[] = 'math_ignore : [0]';
			}
			if (count($m) > 0) {
				return $m;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
}