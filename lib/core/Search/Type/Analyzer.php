<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * This class aims to identify the Search_Type_Factory type based
 * on a set of heuristics.
 */
class Search_Type_Analyzer
{
	function findType($key, $value)
	{
		if (is_array($value)) {
			if ($this->isSimple($value)) {
				return 'multivalue';
			} else {
				return '';
			}
		} else {
			if ($key == 'type' || $key == 'object' || $key == 'version' || $key == 'user') {
				return 'identifier';
			} elseif ($key == 'stream') {
				return 'multivalue';
			} elseif ($this->hasSuffix($key, '_id')) {
				return 'identifier';
			} elseif ($this->hasSuffix($key, 'Id')) {
				return 'identifier';
			} elseif ($this->hasSuffix($key, '_type')) {
				return 'identifier';
			} elseif ($this->hasSuffix($key, '_date')) {
				return 'timestamp';
			} elseif ($this->hasSuffix($key, '_wiki')) {
				return 'wikitext';
			}

			return 'plaintext';
		}
	}

	private function isSimple(array $array)
	{
		return count(array_filter($array, 'is_scalar')) == count($array)
			&& ! $this->isAssociative($array);
	}

	private function isAssociative(array $array)
	{
		return count(array_filter(array_keys($array), 'is_string')) > 0;
	}

	private function hasSuffix($key, $suffix)
	{
		return $suffix === substr($key, -strlen($suffix));
	}
}

