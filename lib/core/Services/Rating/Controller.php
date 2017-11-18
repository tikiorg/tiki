<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Services_Rating_Controller
{
	/**
	 * @param JitFilter $input
	 * @return array
	 */
	function action_vote($input)
	{
		$type = $input->type->text();
		$id = $input->id->id();

		$rating_value = $input->asArray('rating_value');
		$rating_prev = $input->asArray('rating_prev');

		$_REQUEST['rating_value'] = $rating_value;
		$_REQUEST['rating_prev'] = $rating_prev;

		return [
			'type'  => $type,
			'id'    => $id
		];
	}
}
