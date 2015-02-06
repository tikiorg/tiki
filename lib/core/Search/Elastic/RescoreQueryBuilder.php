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
use Search_Expr_ExplicitPhrase as ExplicitPhrase;

/**
 * The rescore query builder generates a list of match_phrase queries to re-score
 * the first few results based on the phrase proximities.
 */
class Search_Elastic_RescoreQueryBuilder
{
	private $factory;
	private $documentReader;
	private $accumulate;

	function __construct()
	{
		$this->factory = new Search_Elastic_TypeFactory;
		$this->documentReader = function ($type, $object) {
			return null;
		};
	}

	function build(Search_Expr_Interface $expr)
	{
		$this->accumulate = [];

		$expr->walk($this);

		$query = [
			'rescore' => [
				'window_size' => 50,
				'query' => [
					'rescore_query' => [
						'bool' => [
							'should' => array_values($this->accumulate),
						],
					],
				],
			],
		];

		return $query;
	}

	function setDocumentReader($callback)
	{
		$this->documentReader = $callback;
	}

	/**
	 * Used when a negation, or a more complete phrase makes a subtree irrelevant
	 */
	private function cancelNode($node)
	{
		$node->walk(function ($node) {
			$hash = spl_object_hash($node);
			unset($this->accumulate[$hash]);
		});
	}

	private function addPhrase($node, $field = null, $phrase = null)
	{
		$field = $field ?: $node->getField();
		$phrase = $phrase ?: $this->getTerm($node);

		$boost = $node->getWeight();

		$this->cancelNode($node);

		$hash = spl_object_hash($node);
		$this->accumulate[$hash] = [
			'match_phrase' => [
				$field => [
					'query' => $phrase,
					'boost' => $boost,
					'slop' => 50,
				],
			],
		];
	}

	function __invoke($node, $childNodes)
	{
		if ($node instanceof ExplicitPhrase) {
			$type = $node->getType();
			if ($type == 'plaintext') {
				$this->addPhrase($node);
			}
			return $node;
		} elseif ($node instanceof Token) {
			$type = $node->getType();
			if ($type == 'plaintext') {
				$this->addPhrase($node);
			}
			return $node;
		} elseif ($node instanceof ImplicitPhrase) {
			$first = reset($childNodes);
			if ($first && $first instanceof Token) {
				$firstType = $first->getType();
				$firstField = $first->getField();
				$terms = [];
				foreach ($childNodes as $child) {
					if ($child instanceof Token && $firstType == $child->getType() && $firstField == $child->getField()) {
						$terms[] = $this->getTerm($child);
					}
				}
				
				if (count($terms) == count($childNodes)) {
					$this->addPhrase($node, $firstField, implode(' ', $terms));
				}
			}

			return $node;
		} elseif ($node instanceof NotX) {
			$this->cancelNode($node);
		}
	}

	private function getTerm($node)
	{
		$value = $node->getValue($this->factory);
		return strtolower($value->getValue());
	}
}

