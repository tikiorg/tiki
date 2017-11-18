<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tracker_Field_Action implements Tracker_Field_Interface
{
	public static function getTypes()
	{
		return [
			'x' => [
				'name' => tr('Action'),
				'description' => tr('Create a form to be posted somewhere, not necessarily in a tracker or even at this Tiki site.'),
				'help' => 'Action Tracker Field',
				'prefs' => ['trackerfield_action'],
				'tags' => ['experimental'],
				'default' => 'n',
				'params' => [
					'label' => [
						'name' => tr('Name'),
						'description' => tr('The title of the button'),
						'filter' => 'text',
						'legacy_index' => 0,
					],
					'post' => [
						'name' => tr('Post'),
						'description' => tr('The protocol to use in the form: either get or post'),
						'filter' => 'text',
						'legacy_index' => 1,
					],
					'script' => [
						'name' => tr('Script'),
						'description' => tr('The URL of the destination of the form'),
						'filter' => 'text',
						'example' => 'tiki-index.php',
						'legacy_index' => 2,
					],
					'parameters' => [
						'name' => tr('Parameters'),
						'description' => tr('Here page:fieldname can be repeated several times. It specifies the variable names to pass in the form as well as the value taken from the current item in the field named fieldname. "highlight=test" can also be repeated and is useful for fixed values as parameters to pass to the form.'),
						'filter' => 'text',
						'count' => '*',
						'example' => 'page:fieldname',
						'legacy_index' => 3,
					],
				],
			],
		];
	}

	function getFieldData(array $requestData = [])
	{
		return [];
	}

	function renderInput($context = [])
	{
		return null;
	}

	function renderOutput($context = [])
	{
		return null;
	}

	function watchCompare($new, $old)
	{
		return null;
	}
}
