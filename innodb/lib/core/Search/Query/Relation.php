<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_Query_Relation
{
	private $qualifier;
	private $type;
	private $object;

	public static function fromToken(Search_Expr_Token $token)
	{
		$token->setType('plaintext');
		$value = $token->getValue(new Search_Type_Factory_Direct);
		list($qualifier, $type, $object) = explode(':', $value->getValue(), 3);

		return new self($qualifier, $type, $object);
	}

	public static function token($qualifier, $type, $object)
	{
		$rel = new self($qualifier, $type, $object);
		return $rel->getToken();
	}

	function __construct($qualifier, $type, $object)
	{
		$this->qualifier = $qualifier;
		$this->type = $type;
		$this->object = $object;
	}

	function __toString()
	{
		return '"' . $this->getToken() . '"';
	}

	function getToken()
	{
		return "{$this->qualifier}:{$this->type}:{$this->object}";
	}

	function getQualifier()
	{
		return $this->qualifier;
	}

	function getInvert()
	{
		$qualifier = $this->qualifier;
		$length = strlen('.invert');

		if (substr($qualifier, -$length) === '.invert') {
			$qualifier = substr($qualifier, 0, -$length);
		} else {
			$qualifier .= '.invert';
		}

		return new self($qualifier, $this->type, $this->object);
	}
}

