<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\Yaml\Filter;

use Tiki_Profile;

class ReplaceUserData implements FilterInterface
{
	protected $profile;
	protected $userData;

	public function __construct(Tiki_Profile $profile, $userData)
	{
		$this->profile = $profile;
		$this->userData = $userData;
	}

	public function filter(&$value)
	{
		$this->profile->replaceReferences($value, $this->userData);
	}
}
