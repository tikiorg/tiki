<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tiki_Profile_Writer_ProfileFinder
{
	private $profiles = array();
	private $symbols;

	function __construct()
	{
		$this->symbols = TikiDb::get()->table('tiki_profile_symbols');
	}

	function lookup($type, $object)
	{
		$result = $this->symbols->fetchAll(
			array(
				'repository' => 'domain',
				'profile',
			), array(
				'type' => $type,
				'value' => $object,
			)
		);

		foreach ($result as $entry) {
			$hash = $entry['repository'] . ':' . $entry['profile'];
			$this->profiles[$hash] = $entry;
		}
	}

	function getProfiles()
	{
		return array_values($this->profiles);
	}

	function getSymbols($repository, $profile)
	{
		return $this->symbols->fetchAll(
			array(
				'type',
				'id' => 'value',
				'symbol' => 'object',
			), array(
				'domain' => $repository,
				'profile' => $profile,
			)
		);
	}

	function checkProfileAndFlush()
	{
		$count = count($this->profiles);

		$this->profiles = array();
		return $count > 0;
	}
}

