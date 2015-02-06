<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

use Search_Expr_Token as Token;
use Search_Expr_And as AndX;
use Search_Expr_Or as OrX;
use Search_Expr_Not as NotX;
use Search_Expr_Range as Range;
use Search_Expr_Initial as Initial;
use Search_Expr_MoreLikeThis as MoreLikeThis;
use Search_Expr_ImplicitPhrase as ImplicitPhrase;

class Search_Elastic_QueryBuilder
{
	private $factory;
	private $documentReader;

	function __construct()
	{
		$this->factory = new Search_Elastic_TypeFactory;
		$this->documentReader = function ($type, $object) {
			return null;
		};
	}

	function build(Search_Expr_Interface $expr)
	{
		$query = $expr->traverse($this);

		if (count($query) && isset($query['bool']) && empty($query['bool'])) {
			return [];
		}

		$query = array("query" => $query);

		return $query;
	}

	function setDocumentReader($callback)
	{
		$this->documentReader = $callback;
	}

	function __invoke($callback, $node, $childNodes)
	{
		if ($node instanceof ImplicitPhrase) {
			$node = $node->getBasicOperator();
		}

		if ($node instanceof Token) {
			return $this->handleToken($node);
		} elseif (count($childNodes) === 1 && ($node instanceof AndX || $node instanceof OrX)) {
			return reset($childNodes)->traverse($callback);
		} elseif ($node instanceof OrX) {
			$inner = array_map(
				function ($expr) use ($callback) {
					return $expr->traverse($callback);
				}, $childNodes
			);

			return array(
				'bool' => array(
					'should' => $this->flatten($inner, 'should'),
					"minimum_number_should_match" => 1,
				),
			);
		} elseif ($node instanceof AndX) {
			$not = array();
			$inner = array_map(
				function ($expr) use ($callback) {
					return $expr->traverse($callback);
				}, $childNodes
			);

			$inner = array_filter(
				$inner,
				function ($part) use (& $not) {
					// Only merge in the single-part NOT
					if (isset($part['bool']['must_not']) && count($part['bool']) == 1) {
						$not = array_merge($not, $part['bool']['must_not']);
						return false;
					} else {
						return true;
					}
				}
			);
			$inner = $this->flatten($inner, 'must');
			if (count($inner) == 1 && isset($inner[0]['bool'])) {
				$base = $inner[0]['bool'];
				if (! isset($base['must_not'])) {
					$base['must_not'] = array();
				}

				$base['must_not'] = array_merge($base['must_not'], $not);

				return array(
					'bool' => array_filter($base),
				);
			} else {
				return array(
					'bool' => array_filter(
						array(
							'must' => $inner,
							'must_not' => $not,
						)
					),
				);
			}
		} elseif ($node instanceof NotX) {
			$inner = array_map(
				function ($expr) use ($callback) {
					return $expr->traverse($callback);
				}, $childNodes
			);
			return array(
				'bool' => array(
					'must_not' => $inner,
				),
			);
		} elseif ($node instanceof Initial) {
			return array(
				'match_phrase_prefix' => array(
					$node->getField() . '.sort' => array(
						"query" => $this->getTerm($node),
						"boost" => $node->getWeight(),
					),
				),
			);
		} elseif ($node instanceof Range) {
			return array(
				'range' => array(
					$node->getField() => array(
						"from" => $this->getTerm($node->getToken('from')),
						"to" => $this->getTerm($node->getToken('to')),
						"boost" => $node->getWeight(),
						"include_upper" => false,
					),
				),
			);
		} elseif ($node instanceof MoreLikeThis) {
			$type = $node->getObjectType();
			$object = $node->getObjectId();

			$content = $node->getContent() ?: $this->getDocumentContent($type, $object);
			return array(
				'more_like_this' => array(
					'fields' => array($node->getField() ?: 'contents'),
					'like_text' => $content,
					'boost' => $node->getWeight(),
				),
			);
		} else {
			throw new Exception(tr('Feature not supported.'));
		}
	}

	private function flatten($list, $type)
	{
		// Only merge when alone, should queries contain the 'minimum_number_should_match' attribute
		$limit = ($type == 'should') ? 2 : 1;

		$out = array();
		foreach ($list as $entry) {
			if (isset($entry['bool'][$type]) && count($entry['bool']) === $limit) {
				$out = array_merge($out, $entry['bool'][$type]);
			} else {
				$out[] = $entry;
			}
		}

		return $out;
	}

	private function getTerm($node)
	{
		$value = $node->getValue($this->factory);
		return strtolower($value->getValue());
	}

	private function handleToken($node)
	{
		if ($node->getType() == 'identifier') {
			$value = $node->getValue($this->factory)->getValue();
			return array("match" => array(
				$node->getField() => array("query" => $value),
			));
		} elseif ($node->getType() == 'multivalue') {
			$value = $node->getValue($this->factory)->getValue();
			return array("match" => array(
				$node->getField() => array("query" => reset($value)),
			));
		} else {
			return array("match" => array(
				$node->getField() => array("query" => $this->getTerm($node), "boost" => $node->getWeight()),
			));
		}
	}

	private function getDocumentContent($type, $object)
	{
		$cb = $this->documentReader;
		$document = $cb($type, $object);

		if (isset($document['contents'])) {
			return $document['contents'];
		}

		return '';
	}
}

