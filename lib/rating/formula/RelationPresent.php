<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tiki_Formula_Function_RelationPresent extends Math_Formula_Function
{
	function evaluate( $element )
	{
		$default = 0;
		$allowed = ['qualifier', 'from', 'to'];

		if ( $extra = $element->getExtraValues($allowed) ) {
			$this->error(tr('Unexpected values: %0', implode(', ', $extra)));
		}

		$from = $element->from;
		$to = $element->to;
		$qualifier = $element->qualifier;

		if ( ! $qualifier || count($qualifier) != 1 ) {
			$this->error(tra('Qualifier must be provided and contain one argument: type'));
		}

		if ( ! $from || count($from) != 2 ) {
			$this->error(tra('From must be provided and contain two arguments: type, object-id'));
		}

		if ( ! $to || count($to) != 2 ) {
			$this->error(tra('To must be provided and contain two arguments: type, object-id'));
		}

		$qualifier = $this->evaluateChild($qualifier[0]);
		$typeFrom = $this->evaluateChild($from[0]);
		$objectFrom = $this->evaluateChild($from[1]);
		$typeTo = $this->evaluateChild($to[0]);
		$objectTo = $this->evaluateChild($to[1]);

		$lib = TikiLib::lib('relation');
		$id = $lib->get_relation_id($qualifier, $typeFrom, $objectFrom, $typeTo, $objectTo);
		
		return $id ? 1 : 0;
	}
}

