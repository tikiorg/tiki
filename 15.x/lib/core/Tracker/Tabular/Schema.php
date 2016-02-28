<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tracker\Tabular;

class Schema
{
	private $definition;
	private $columns = [];
	private $primaryKey;
	private $schemas = [];
	private $filters;

	function __construct(\Tracker_Definition $definition)
	{
		$this->definition = $definition;
		$this->filters = new \Tracker\Filter\Collection($definition);
	}

	function getDefinition()
	{
		return $this->definition;
	}

	function getHtmlOutputSchema()
	{
		$out = new self($this->definition);
		$out->filters = $this->filters;
		$out->schemas = $this->schemas;

		foreach ($this->columns as $column) {
			$replacement = $column->getPlainReplacement();

			if ($column->isExportOnly()) {
				continue; // Skip column
			} elseif ($replacement || $replacement === false) {
				// Has a replacement means output is HTML
				// No replacement at all is the same
				$out->columns[] = $column;
			} else {
				$out->columns[] = $column->withWrappedRenderTransform('htmlspecialchars');
			}
		}

		return $out;
	}

	function getPlainOutputSchema()
	{
		$out = new self($this->definition);
		$out->filters = $this->filters;
		$out->schemas = $this->schemas;
		$out->primaryKey = $this->primaryKey;

		foreach ($this->columns as $column) {
			$replacement = $column->getPlainReplacement();

			if ($replacement) {
				$new = $this->addColumn($column->getField(), $replacement);
				$new->setLabel($column->getLabel());

				// If the replacement is read-only, leave as-is
				if (! $new->isReadOnly()) {
					$new->setReadOnly($column->isReadOnly());
				}

				// Convert the primary key field as needed
				if ($column->isPrimaryKey()) {
					$out->primaryKey = $new;
					$new->setPrimaryKey(true);
				}

				$out->columns[] = $new;
			} elseif ($replacement !== false) {
				$out->columns[] = $column;
			}
		}

		return $out;
	}

	function loadFormatDescriptor($descriptor)
	{
		foreach ($descriptor as $column) {
			try {
				$col = $this->addColumn($column['field'], $column['mode']);
			} catch (Exception\FieldNotFound $e) {
				\TikiLib::lib('errorreport')->report($e->getMessage());	// TODO make error message appear when exporting
				continue;
			}
			$col->setExportOnly(! empty($column['isExportOnly']));

			if (! $col->isReadOnly() && ! empty($column['isReadOnly'])) {
				$col->setReadOnly(true);
			}

			if (! empty($column['displayAlign'])) {
				$col->setDisplayAlign($column['displayAlign']);
			}

			if ($column['label']) {
				$col->setLabel($column['label']);
			}

			if (! empty($column['isPrimary'])) {
				$this->setPrimaryKey($col);
			}
		}
	}

	function loadFilterDescriptor(array $descriptor)
	{
		$this->filters->loadFilterDescriptor($descriptor);
	}

	function getFilterCollection()
	{
		return $this->filters;
	}

	function getFormatDescriptor()
	{
		return array_map(function ($column) {
			return [
				'label' => $column->getLabel(),
				'field' => $column->getField(),
				'mode' => $column->getMode(),
				'displayAlign' => $column->getDisplayAlign(),
				'isPrimary' => $column->isPrimaryKey(),
				'isReadOnly' => $column->isReadOnly(),
				'isExportOnly' => $column->isExportOnly(),
			];
		}, $this->columns);
	}

	function getFilterDescriptor()
	{
		return $this->filters->getFilterDescriptor();
	}

	function addColumn($permName, $mode)
	{
		if (isset($this->schemas[$permName])) {
			$partial = $this->schemas[$permName];
		} else {
			$partial = $this->getFieldSchema($permName, $mode);
			$this->schemas[$permName] = $partial;
		}

		$column = $partial->lookupMode($permName, $mode);
		$this->columns[] = $column;

		return $column;
	}

	function setPrimaryKey($field)
	{
		if ($this->primaryKey) {
			throw new \Exception(tr('Primary key already defined.'));
		}

		foreach ($this->columns as $column) {
			if ($field === $column || $column->getField() == $field) {
				$this->primaryKey = $column;
				$column->setPrimaryKey(true);
				return;
			}
		}

		throw new Exception\FieldNotFound($field);
	}

	function getPrimaryKey()
	{
		return $this->primaryKey;
	}

	private function lookupMode($permName, $mode)
	{
		foreach ($this->columns as $column) {
			if ($column->getField() == $permName && $column->getMode() == $mode) {
				return $column;
			}
		}

		throw new Exception\ModeNotSupported($permName, $mode);
	}

	function addNew($permName, $mode)
	{
		$column = new Schema\Column($permName, $mode);
		$this->columns[] = $column;
		return $column;
	}

	function addStatic($value)     
	{     
		$column = new Schema\Column('ignore', uniqid());    
		$column->setReadOnly(true);    
		$column->setRenderTransform(function () use ($value) {     
			return $value;    
		});    

		$this->columns[] = $column;    
		return $column;    
	}     

	function getColumns()
	{
		return $this->columns;
	}

	function validate()
	{
		foreach ($this->columns as $column) {
			$column->validateAgainst($this);
		}
	}

	function validateAgainstHeaders(array $headers)
	{
		foreach ($this->columns as $column) {
			$header = array_shift($headers);

			if (! $header) {
				throw new \Exception(tr('Not enough columns, expecting "%0".', $column->getEncodedHeader()));
			}

			if (preg_match(Schema\Column::HEADER_PATTERN, $header, $parts)) {
				list($full, $pk, $field, $mode) = $parts;
				if (! $column->is($field, $mode)) {
					throw new \Exception(tr('Header "%0" found where "%1" was expected', $header, $column->getEncodedHeader()));
				}
			} else {
				if (! $column->isReadOnly()) {
					throw new \Exception(tr('Header "%0" found where ignored column was expected.', $header, $column->getEncodedHeader()));
				}
			}
		}
	}

	function getAvailableFields()
	{
		$fields = ['itemId' => tr('Item ID'), 'status' => tr('Status'), 'actions' => tr('Actions')];

		foreach ($this->definition->getFields() as $f) {
			$fields[$f['permName']] = $f['name'];
		}

		return $fields;
	}

	function getFieldSchema($permName)
	{
		if ($partial = $this->getSystemSchema($permName)) {
			return $partial;
		}

		$field = $this->definition->getFieldFromPermName($permName);
		$factory = $this->definition->getFieldFactory();

		if (! $field) {
			throw new Exception\FieldNotFound($permName);
		}

		$handler = $factory->getHandler($field);

		if (! $handler instanceof \Tracker_Field_Exportable) {
			throw new Exception\ModeNotSupported($permName, 'any mode');
		}

		return $handler->getTabularSchema();
	}

	private function getSystemSchema($name)
	{
		switch ($name) {
		case 'actions':
			$trackerId = $this->definition->getConfiguration('trackerId');
			$schema = new self($this->definition);
			$schema->addNew($name, 'all')
				->setLabel(tr('Actions'))
				->addQuerySource('itemId', 'object_id')
				->setReadOnly(true)
				->setPlainReplacement(false)
				->setRenderTransform(function ($value, $extra) use ($trackerId) {
					$smarty = \TikiLib::lib('smarty');
					$item = \Tracker_Item::fromId($extra['itemId']);

					$smarty->assign('tabular_actions', [
						'trackerId' => $trackerId,
						'itemId' => $extra['itemId'],
						'canModify' => $item->canModify(),
						'canRemove' => $item->canRemove(),
					]);

					return $smarty->fetch('tabular/item_actions.tpl');
				})
				;
			return $schema;
		case 'itemId':
			$schema = new self($this->definition);
			$schema->addNew($name, 'id')
				->setLabel(tr('Item ID'))
				->addQuerySource('itemId', 'object_id')
				->setRenderTransform(function ($value, $extra) {
					return $extra['itemId'];
				})
				->setParseIntoTransform(function (& $info, $value) {
					$info['itemId'] = (int) $value;
				})
				;
			return $schema;
		case 'status':
			$types = \TikiLib::lib('trk')->status_types();
			$invert = array_flip(array_map(function ($s) {
				return $s['name'];
			}, $types));

			$schema = new self($this->definition);
			$schema->addNew($name, 'system')
				->setLabel(tr('Status'))
				->addQuerySource('status', 'tracker_status')
				->setRenderTransform(function ($value, $extra) {
					return $extra['status'];
				})
				->setParseIntoTransform(function (& $info, $value) {
					$info['status'] = $value;
				})
				;
			$schema->addNew($name, 'name')
				->setLabel(tr('Status'))
				->addQuerySource('status', 'tracker_status')
				->setRenderTransform(function ($value, $extra) use ($types) {
					return $types[$extra['status']]['name'];
				})
				->setParseIntoTransform(function (& $info, $value) use ($invert) {
					$info['status'] = $invert[$value];
				})
				;
			return $schema;
		}
	}
}
