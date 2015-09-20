<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_fitnesse_info()
{
	return array(
		'name' => tra('Fitnesse Test Suite'),
		'documentation' => 'PluginFitnesse',
		'description' => tra('Create test suites for applications built using Tiki'),
		'prefs' => array('wikiplugin_fitnesse'),
		'default' => 'n',
		'format' => 'wiki',
		'body' => tra('Test execution scenario'),
		'filter' => 'wikicontent',
		'iconname' => 'pencil',
		'introduced' => 12.1,
		'tags' => array('advanced'),
		'profile_reference' => 'fitnesse_content',
		'params' => array(
		),
	);
}

function wikiplugin_fitnesse($data, $params)
{
	$runner = Tracker_Field_Math::getRunner();
	$mock = new FixtureMockTrackerField;
	$runner->mockFunction('tracker-field', $mock);

	$fixtures = array(
		'trackermath' => 'wp_fixture_tracker_math',
		'trackerdata' => function ($data, $params) use ($mock) {
			return wp_fixture_tracker_data($data, $params, $mock);
		},
	);

	$matches = WikiParser_PluginMatcher::match($data);
	$argParser = new WikiParser_PluginArgumentParser;

	foreach ($matches as $table) {
		$fixture = $table->getName();

		$arguments = $table->getArguments();
		$arguments = $argParser->parse($arguments);
		$body = trim($table->getBody());

		if (isset($fixtures[$fixture])) {
			$replace = call_user_func($fixtures[$fixture], $body, new JitFilter($arguments));
			$table->replaceWith($replace);
		} else {
			$data = new FixtureTable($body);
			$table->replaceWith('__' . tr('Fixture not found: %0', $fixture) . "__\n$data");
		}
	}

	Tracker_Field_Math::resetRunner();
	return $matches->getText();
}

function wp_fixture_tracker_math($data, $params)
{
	$table = new FixtureTable($data);
	$trackerId = $params->trackerId->int();
	$tracker = Tracker_Definition::get($trackerId);

	if (! $tracker) {
		return '__' . tr('Tracker not found.') . '__';
	}

	$smarty = TikiLib::lib('smarty');
	$smarty->loadPlugin('smarty_modifier_sefurl');
	$url = smarty_modifier_sefurl($trackerId, 'tracker');
	$table->setTitle(tr('Tracker Math for [%0|%1]', $url, $tracker->getConfiguration('name')));

	$factory = new Tracker_Field_Factory($tracker);

	$checks = array();

	$headings = $table->getHeadings();
	foreach ($headings as $key => $heading) {
		$permName = rtrim($heading, '?');

		if (! $field = $tracker->getFieldFromPermName($permName)) {
			return '__' . tr('Tracker Field not found: %0', $permName) . '__';
		}

		if ($permName != $heading) {
			$checks[$key] = $factory->getHandler($field);

			if (! $checks[$key] instanceof Tracker_Field_Math) {
				return '__' . tr('Field is not a math field: %0', $permName) . '__';
			}
		}
	}

	foreach ($table as $row) {
		$variableNames = array_map('rtrim', $headings, array_fill(0, count($headings), '?'));
		$variables = array_combine($variableNames, $row);

		foreach ($checks as $key => $field) {
			$out = $field->handleFinalSave($variables);

			if ($out == $row[$key]) {
				$table->setValue($key, $out, 'green');
			} else {
				$table->setValue($key, tr("%0 (expect: %1)", $out, $row[$key]), 'red');
			}
		}
	}

	return $table;
}

function wp_fixture_tracker_data($data, $params, $mock)
{
	$table = new FixtureTable($data);
	$headings = $table->getHeadings();

	$trackerId = $params->trackerId->int();
	$tracker = Tracker_Definition::get($trackerId);

	if (! $tracker) {
		return '__' . tr('Tracker not found.') . '__';
	}

	$smarty = TikiLib::lib('smarty');
	$smarty->loadPlugin('smarty_modifier_sefurl');
	$url = smarty_modifier_sefurl($trackerId, 'tracker');
	$table->setTitle(tr('Tracker Data for [%0|%1]', $url, $tracker->getConfiguration('name')));

	if (! in_array('itemId', $headings)) {
		return '__' . tr('Table must contain at least one field named itemId') . '__';
	}

	foreach ($headings as $permName) {
		if ($permName != 'itemId' && ! $field = $tracker->getFieldFromPermName($permName)) {
			return '__' . tr('Tracker Field not found: %0', $permName) . '__';
		}
	}

	foreach ($table as $row) {
		$fields = array_combine($headings, $row);
		$itemId = $fields['itemId'];
		unset($fields['itemId']);

		$mock->addValues($itemId, $fields);
	}

	return $table;
}

class FixtureTable implements Iterator
{
	private $title;
	private $headings = array();
	private $data = array();
	private $position = 0;

	function __construct($string)
	{
		$lines = explode("\n", $string);
		$lines = array_map(function ($line) {
			return array_map('trim', explode('|', $line));
		}, $lines);

		$this->headings = array_shift($lines);
		$length = count($this->headings);
		$this->data = array_map(function ($line) use ($length) {
			return array_pad($line, $length, null);
		}, $lines);
	}

	function __toString()
	{
		$lines = $this->data;
		array_unshift($lines, array_map(function ($entry) {
			return "__{$entry}__";
		}, $this->headings));

		if ($this->title) {
			array_unshift($lines, array($this->title));
		}
		return "||" . implode("\n", array_map(function ($line) {
			return implode(' | ', $line);
		}, $lines)) . "||";
	}

	function getHeadings()
	{
		return $this->headings;
	}

	function setTitle($title)
	{
		$this->title = $title;
	}

	function rewind() {
		$this->position = 0;
	}

	function current() {
		return array_map(function ($value) {
			return str_replace('%%%', "\n", $value);
		}, $this->data[$this->position]);
	}

	function key() {
		return $this->position;
	}

	function next() {
		++$this->position;
	}

	function valid() {
		return isset($this->data[$this->position]);
	}

	function setValue($pos, $value, $color = null)
	{
		if ($color) {
			$value = "~~$color:$value~~";
		}

		$this->data[$this->position][$pos] = str_replace("\n", '%%%', $value);
	}
}

class FixtureMockTrackerField extends Tiki_Formula_Function_TrackerField
{
	private $data = array();

	function fetchValue($object, $field, $default)
	{
		if (isset($this->data[$object][$field])) {
			return $this->data[$object][$field];
		} else {
			return $default;
		}
	}

	function addValues($id, array $data)
	{
		if (! isset($this->data[$id])) {
			$this->data[$id] = $data;
		} else {
			$this->data[$id] = array_merge($this->data[$id], $data);
		}
	}
}

