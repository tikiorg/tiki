<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class TikiDb_Exception extends Exception
{
	public static function classify($error)
	{
		if (preg_match('/^Duplicate entry \'(?P<entry>.*)\' for key \'(?P<key>.*)\'$/', $error, $parts)) {
			throw new TikiDb_Exception_DuplicateEntry($parts['key'], $parts['entry']);
		} else {
			throw new self($error);
		}
	}
}
