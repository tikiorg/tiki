<?php
// (c) Copyright 2002-2017 by authors of the Tiki Wiki/CMS/Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\CustomRoute\Type;

use Tiki\CustomRoute\Type;

/**
 * Custom route for direct routing transformation
 */
class Direct extends Type
{
	/**
	 * @inheritdoc
	 */
	public function getParams()
	{
		return [
			'to' => [
				'name' => tr('To'),
				'type' => 'text',
				'required' => true,
			],
		];
	}
}
