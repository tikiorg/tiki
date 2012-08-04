<?php

class Tiki_Version_Upgrade
{
	private $old;
	private $new;

	function __construct($old, $new)
	{
		$this->old = Tiki_Version_Version::get($old);
		$this->new = Tiki_Version_Version::get($new);
	}
}

