<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
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

	/**
	 * Applies the callback to every node in the tree, applying to children first.
	 * Primarily used by the lucene query building, which is a simple boolean query.
	 *
	 * $callback($expr, array $processedChildNodes)
	 */
	function walk($callback);

	/**
	 * Similar to walk, but leaves more control to the callback about the processing
	 * sequence. Primarily used by the elasticsearch query building which requires more
	 * introspection of the query.
	 *
	 * $callback($callback, $expr, array $childExpr)
	 *
	 * The callback can call traverse on child expressions when suitable.
	 */
	function traverse($callback);
}

