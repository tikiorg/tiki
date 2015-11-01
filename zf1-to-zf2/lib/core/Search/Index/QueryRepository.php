<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

interface Search_Index_QueryRepository
{
	function getMatchingQueries(array $document);

	function store($name, Search_Expr_Interface $expr);
	function unstore($name);
}

