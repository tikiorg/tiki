<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

interface Search_Expr_Interface
{
	function setField($field = 'global');
	function setType($type);
	function setWeight($weight);
	function getWeight();
	function walk($callback);
}

