<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tracker\Filter;
use Search_Query;
use TikiLib;

class Collection
{
	private $definition;
	private $filters = [];
	private $collections = [];
	private $resultset;

	function __construct(\Tracker_Definition $definition)
	{
		$this->definition = $definition;
	}

	function addNew($permName, $mode)
	{
		$column = new Filter($permName, $mode);
		$this->filters[] = $column;
		return $column;
	}

	function addCloned($permName, self $collection)
	{
		foreach ($collection->filters as $filter) {
			$this->addNew($permName, $filter->getField() . '-' . $filter->getMode())
				->copyProperties($filter);
		}
	}

	function getFilters()
	{
		return $this->filters;
	}
	
	function applyConditions(\Search_Query $query)
	{
		foreach ($this->filters as $filter) {
			$filter->applyCondition($query);
		}
	}

	function applyInput(\JitFilter $input)
	{
		foreach ($this->filters as $filter) {
			$filter->applyInput($input);
		}
	}

	function loadFilterDescriptor($descriptor)
	{
		foreach ($descriptor as $filter) {
			$fil = $this->addFilter($filter['field'], $filter['mode']);

			if (! empty($filter['position'])) {
				$fil->setPosition($filter['position']);
			}

			if (! empty($filter['label'])) {
				$fil->setLabel($filter['label']);
			}
		}
	}

	function addFilter($permName, $mode)
	{
		if (isset($this->collections[$permName])) {
			$partial = $this->collections[$permName];
		} else {
			$partial = $this->getFieldCollection($permName, $mode);
			$this->collections[$permName] = $partial;
		}

		$filter = $partial->lookupMode($permName, $mode);
		$this->filters[] = $filter;

		return $filter;
	}

	private function lookupMode($permName, $mode)
	{
		foreach ($this->filters as $filter) {
			if ($filter->getField() == $permName && $filter->getMode() == $mode) {
				return $filter;
			}
		}

		throw new Exception\ModeNotSupported($permName, $mode);
	}

	function getFieldCollection($permName)
	{
		if ($partial = $this->getSystemCollection($permName)) {
			return $partial;
		}

		$field = $this->definition->getFieldFromPermName($permName);
		$factory = $this->definition->getFieldFactory();

		if (! $field) {
			throw new Exception\FieldNotFound($permName);
		}

		$handler = $factory->getHandler($field);

		if (! $handler instanceof \Tracker_Field_Filterable) {
			throw new Exception\ModeNotSupported($permName, 'any mode');
		}

		return $handler->getFilterCollection();
	}

	private function getSystemCollection($name)
	{
		switch ($name) {
		case 'itemId':
			$collection = new self($this->definition);
			$collection->addNew($name, 'lookup')
				->setLabel(tr('Item ID'))
				->setHelp(tr('Look up a single item by ID.'))
				->setControl(new Control\TextField("tf_itemId"))
				->setApplyCondition(function ($control, Search_Query $query) {
					$value = $control->getValue();

					if ($value) {
						$query->filterIdentifier($value, 'object_id');
					}
				})
				;
			return $collection;
		case 'search':
			$collection = new self($this->definition);
			$collection->addNew($name, 'search')
				->setLabel(tr('Search'))
				->setHelp(tr('Full-text search across all of the content.'))
				->setControl(new Control\TextField("tf_search"))
				->setApplyCondition(function ($control, Search_Query $query) {
					$value = $control->getValue();

					if ($value) {
						$o = TikiLib::lib('tiki')->get_preference('unified_default_content', array('contents'), true);
						if (count($o) == 1 && empty($o[0])) {
							// Use "contents" field by default, if no default is specified
							$query->filterContent($value, array('contents'));
						} else {
							$query->filterContent($value, $o);
						}
					}
				})
				;
			return $collection;
		case 'status':
			$types = TikiLib::lib('trk')->status_types();
			$possibilities = array_map(function ($item) {
				return $item['label'];
			}, $types);

			$collection = new self($this->definition);
			$collection->addNew($name, 'dropdown')
				->setLabel(tr('Status'))
				->setControl(new Control\DropDown("tfdd_status", $possibilities))
				->setApplyCondition(function ($control, Search_Query $query) {
					$value = $control->getValue();

					if ($value) {
						$query->filterIdentifier($value, 'tracker_status');
					}
				});

			$controls = [
				'multiselect' => new Control\MultiSelect("tfms_status", $possibilities),
				'checkboxes' => new Control\InlineCheckboxes("tfc_status", $possibilities),
			];
			foreach ($controls as $key => $control) {
				$collection->addNew($name, $key)
					->setLabel(tr('Status'))
					->setControl($control)
					->setApplyCondition(function ($control, Search_Query $query) {
						$values = $control->getValues();

						if (! empty($values)) {
							$sub = $query->getSubQuery("tfms_status");

							foreach ($values as $v) {
								$sub->filterIdentifier((string) $v, 'tracker_status');
							}
						}
					});
			}

			return $collection;
		case 'facet':
			$collection = new self($this->definition);

			$lib = \TikiLib::lib('unifiedsearch');
			$provider = $lib->getFacetProvider();

			foreach ($provider->getFacets() as $facet) {
				$getoptions = function () use ($facet) {
					if ($this->resultset) {
						if ($filter = $this->resultset->getFacet($facet)) {
							return $filter->getOptions();
						}
					}

					return [];
				};

				$renderextra = function ($id) use ($facet) {
					$label = $facet->render($id) ?: tr('Unknown value');
					return "$label (0)";
				};

				$collection->addNew($name, 'facet-any-' . $facet->getName())
					->setLabel(tr('%0 (any of)', $facet->getLabel()))
					->setControl(new Control\MultiSelect("facet_any_{$facet->getName()}", $getoptions, $renderextra))
					->setApplyCondition(function ($control, Search_Query $query) use ($facet) {
						$query->requestFacet($facet);

						$values = $control->getValues();
						if (! empty($values)) {
							$query->getPostFilter()->filterContent(implode(" OR ", $values), $facet->getName());
						}
					});

				$collection->addNew($name, 'facet-all-' . $facet->getName())
					->setLabel(tr('%0 (all of)', $facet->getLabel()))
					->setControl(new Control\MultiSelect("facet_all_{$facet->getName()}", $getoptions, $renderextra))
					->setApplyCondition(function ($control, Search_Query $query) use ($facet) {
						$query->requestFacet($facet);

						$values = $control->getValues();
						if (! empty($values)) {
							$query->getPostFilter()->filterContent(implode(" AND ", $values), $facet->getName());
						}
					});
			}
			
			return $collection;
		}
	}

	function getFilterDescriptor()
	{
		return array_map(function ($filter) {
			return [
				'label' => $filter->getLabel(),
				'field' => $filter->getField(),
				'mode' => $filter->getMode(),
				'position' => $filter->getPosition(),
			];
		}, $this->filters);
	}

	function getQueryArguments()
	{
		$parts = [];
		foreach ($this->filters as $filter) {
			$parts += $filter->getControl()->getQueryArguments();
		}
		
		return $parts;
	}

	function getAvailableFields()
	{
		$fields = [
			'itemId' => tr('Item ID'),
			'status' => tr('Status'),
			'search' => tr('Search'),
			'actions' => tr('Actions'),
			'facet' => tr('Dynamic Filters'),
		];

		foreach ($this->definition->getFields() as $f) {
			$fields[$f['permName']] = $f['name'];
		}

		return $fields;
	}

	function setResultSet(\Search_ResultSet $resultset)
	{
		$this->resultset = $resultset;
	}
}
