<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_Formatter_ValueFormatter_Reference extends Search_Formatter_ValueFormatter_Abstract
{
	private $separator = ', ';
	private $type = 'wiki page';

	function __construct($arguments)
	{
		if (isset($arguments['separator'])) {
			$this->separator = $arguments['separator'];
		}

		if (isset($arguments['type'])) {
			$this->type = $arguments['type'];
		}
	}

	function render($name, $value, array $entry)
	{
		$smarty = TikiLib::lib('smarty');
		$smarty->loadPlugin('smarty_function_object_link');

		foreach ((array) $value as $id) {
			$params = array(
				'type' => $this->type,
				'id' => $id,
			);
			$links[] = smarty_function_object_link($params, $smarty);
		}

		return '~np~' . implode($this->separator, $links) . '~/np~';
	}
}

