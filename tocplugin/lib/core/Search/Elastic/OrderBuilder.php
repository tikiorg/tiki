<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_Elastic_OrderBuilder
{
	function build(Search_Query_Order $order)
	{
		$component = '_score';
		$field = $order->getField();

		if ($field !== Search_Query_Order::FIELD_SCORE) {
			if ($order->getMode() == Search_Query_Order::MODE_NUMERIC) {
				$component = array(
					"$field.nsort" => $order->getOrder(),
				);
			} else {
				$component = array(
					"$field.sort" => $order->getOrder(),
				);
			}
		}

		return array(
			"sort" => array(
				$component,
			),
		);
	}
}

