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
	 * Function to get an attribute
	 *
	 * @param $input JitFilter
	 *  ->attribute string      lowercase letters and two dots
	 *  ->type string           object type
	 *  ->object mixed          id or name of object
	 *
	 * @return string containing the value
	 * @throws Exception
	 * @throws Services_Exception
	 */
	function action_get($input)
	{
		$attribute = $input->attribute->text();
		$type = $input->type->text();
		$object = $input->object->text();

		// ensure the target, source, and relation info are passed to the service
		if (! $type || ! $attribute) {
			throw new Services_Exception(tr('Invalid input'), 400);
		}

		if ($object) {		// for objects yet to be created we don't get an object id, so don't set any attributes

			$value = TikiLib::lib('attribute')->get_attribute($type, $object, $attribute);
		}

		//return the attribute value if there were no errors
		return [
			'value' => $value,
		];
	}

}

