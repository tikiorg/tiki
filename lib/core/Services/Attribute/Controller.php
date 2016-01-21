<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Services_Attribute_Controller
{
	function setUp()
	{

	}

	/**
	 * Function to set an attribute
	 *
	 * @param $input JitFilter
	 *  ->attribute string      lowercase letters and two dots
	 *  ->type string           object type
	 *  ->object mixed          id or name of object
	 *  ->value mixed           value to set the attribute to
	 *
	 * @return string containing the value set if successful
	 * @throws Exception
	 * @throws Services_Exception
	 */
	function action_set($input)
	{
		$attribute = $input->attribute->text();
		$type = $input->type->text();
		$object = $input->object->text();
		$value = $input->value->text();

		// ensure the target, source, and relation info are passed to the service
		if (! $type || ! $attribute) {
			throw new Services_Exception(tr('Invalid input'), 400);
		}

		if ($object) {		// for objects yet to be created we don't get an object id, so don't set any attributes

			$tx = TikiDb::get()->begin();
			$return = TikiLib::lib('attribute')->set_attribute($type, $object, $attribute, $value);
			$tx->commit();

			if (!$return) {
				TikiLib::lib('errorreport')->report(tr('Invalid attribute name "%0"', $attribute));
				$value = '';
			}
		}

		//return the attribute value if tehre were no errors
		return [
			'value' => $value,
		];
	}

}

