<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

use Tiki\Recommendation as R;

class Services_Recommendation_DevelopmentController
{
	function setUp()
	{
		Services_Exception_Denied::checkGlobal('admin');
	}

	function action_compare($input)
	{
		$user = $input->user->username() ?: $GLOBALS['user'];
		$input = new R\Input\UserInput($user);

		$comparator = new R\Comparator($this->getEngineSet('content'));

		return [
			'title' => tr('Recommendations for %0', $user),
			'recommendations' => $comparator->generate($input),
		];
	}

	private function getEngineSet($set)
	{
		$container = TikiInit::getContainer();
		return $container->get("tiki.recommendation.$set.set");
	}
}
