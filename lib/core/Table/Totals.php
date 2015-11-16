<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
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
 * Class Table_Totals
 * This is a public class for checking necessary preferences or tablesorter status
 *
 * @package Tiki
 * @subpackage Table
 */
class Table_Totals
{
	/**
	 * @param array $s
	 * @param null $count
	 * @return bool|string
	 * @throws Exception
	 */
	static public function getTotalsHtml(array $s, $count = null)
	{
		if (Table_Check::isEnabled()) {
			$mathcols = isset($s['columns']) && count(array_column($s['columns'], 'math')) > 0
				? array_column($s['columns'], 'math') : false;
			if (!empty($s['math']) || $mathcols)
			{
				if (empty($count) && $mathcols === false) {
					trigger_error(tr('Tablesorter: the number of columns is needed to produce total rows.'), E_NOTICE);
					return false;
				} else {
					$count = !empty($count) ? $count : count($mathcols);
					$smarty = TikiLib::lib('smarty');
					$smarty->assign('count', $count);
					if ($mathcols) {
						$smarty->assign('cols', $mathcols);
					}
					if (!empty($s['math'])) {
						$smarty->assign('totals', $s['math']);
					}
					return $smarty->fetch('tablesorter/totals.tpl');
				}
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
}
