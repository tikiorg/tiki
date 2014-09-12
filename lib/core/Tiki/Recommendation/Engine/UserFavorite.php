<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: FakeEngine.php 52504 2014-09-12 17:08:17Z lphuberdeau $

namespace Tiki\Recommendation\Engine;
use Tiki\Recommendation\Recommendation;

class UserFavorite implements EngineInterface
{
	private $lib;

	function __construct($unifiedsearch)
	{
		$this->lib = $unifiedsearch;
	}

	function generate($input)
	{
		assert($input instanceof \Tiki\Recommendation\Input\UserInput);

		$user = $input->getUser();
		$context = new \Perms_Context($user);

		$query = $this->lib->buildQuery(['searchable' => 'y']);
		$query->setOrder('modification_date_desc');

		$query->setRange(0, 10);

		$token = (string) new \Search_Query_Relation('tiki.user.favorite.invert', 'user', $user);
		$query->filterRelation("\"$token\"");

		$result = $query->search($this->lib->getIndex());
		$content = '';
		foreach ($result as $row) {
			$content .= ' ' . $row['contents'];
		}

		$query = $this->lib->buildQuery([]);
		$query->filterSimilarToThese($result, $content);
		$query->filterIdentifier('y', 'searchable');
		$query->setRange(0, 5);
		$result = $query->search($this->lib->getIndex());

		foreach ($result as $row) {
			yield new Recommendation($row['object_type'], $row['object_id']);
		}
	}
}
