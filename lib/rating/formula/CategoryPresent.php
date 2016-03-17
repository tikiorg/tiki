<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tiki_Formula_Function_CategoryPresent extends Math_Formula_Function
{
	function evaluate( $element )
	{
		$default = 0;
		$allowed = array( 'object', 'list' );

		if ( $extra = $element->getExtraValues($allowed) ) {
			$this->error(tr('Unexpected values: %0', implode(', ', $extra)));
		}

		$object = $element->object;

		if ( ! $object || count($object) != 2 ) {
			$this->error(tra('Item must be provided and contain one argument: type, object-id'));
		}

		$type = $this->evaluateChild($object[0]);
		$object = $this->evaluateChild($object[1]);

		$list = $element->list;

		if ( ! $list || count($list) == 0 ) {
			$this->error(tra('List must be provided and contain at least one argument: category IDs'));
		}

		$categlib = TikiLib::lib('categ');
		$categories = $categlib->get_object_categories($type, $object, -1, false);
		
		$score = 0;
		foreach ($list as $entry) {
			if (in_array($entry, $categories)) {
				++$score;
			}
		}

		return $score;
	}
}

