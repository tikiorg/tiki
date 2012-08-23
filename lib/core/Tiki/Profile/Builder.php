<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tiki_Profile_Builder
{
	private $objects = array();

	function ref($name)
	{
		return '$' . $name;
	}

	function user($name)
	{
		return '$profilerequest:' . $name . '$undefined$';
	}
	
	function addObject($type, $ref, array $data)
	{
		$this->objects[] = array(
			'type' => $type,
			'ref' => $ref,
			'data' => $data,
		);
	}

	function getContent()
	{
		$yaml = Horde_Yaml::dump(array('objects' => $this->objects));
		return <<<SYNTAX

^The following profile was auto-generated. It may hurt your eyes when you try reading it.^
{CODE(caption="YAML")}
$yaml
{CODE}

SYNTAX;
	}
}

