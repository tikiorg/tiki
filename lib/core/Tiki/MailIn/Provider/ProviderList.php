<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\MailIn\Provider;

class ProviderList
{
	private $list = [];

	function addProvider(ProviderInterface $provider)
	{
		$this->list[] = $provider;
	}

	function getList()
	{
		usort($this->list, function ($a, $b) {
			return strcmp($a->getLabel(), $b->getLabel());
		});
		return $this->list;
	}
}

