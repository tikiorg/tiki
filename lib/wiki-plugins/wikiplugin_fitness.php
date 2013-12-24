<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_fitness_info()
{
	return array(
		'name' => tra('Fitness Test Suite'),
		'documentation' => 'PluginFitness',
		'description' => tra('Executable test suite'),
		'prefs' => array('wikiplugin_fitness'),
		'default' => 'n',
		'format' => 'wiki',
		'body' => tra('Test execution scenario'),
		'filter' => 'wikicontent',
		'icon' => 'img/icons/text_list_bullets.png',
		'tags' => array('advanced'),
		'params' => array(
		),
	);
}

function wikiplugin_fitness($data, $params)
{
	$fixtures = array(
		'trackermath' => 'wp_fixture_tracker_math',
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
			$table->replaceWith('__' . tr('Fixture not found: %0', $fixture) . "__\n$data\n");
		}
	}

	return $matches->getText();
}

function wp_fixture_tracker_math($data, $params)
{
	$table = new FixtureTable($data);
	$tracker = Tracker_Definition::get($params->trackerId->int());

	if (! $tracker) {
		return '__' . tr('Tracker not found.') . '__';
	}

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
		}
	}

	foreach ($table as $row) {
		$variables = array_combine($headings, $row);

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

class FixtureTable implements Iterator
{
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

		return "||" . implode("\n", array_map(function ($line) {
			return implode(' | ', $line);
		}, $lines)) . "||\n";
	}

	function getHeadings()
	{
		return $this->headings;
	}

	function rewind() {
		$this->position = 0;
	}

	function current() {
		return $this->data[$this->position];
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

		$this->data[$this->position][$pos] = $value;
	}
}

ini_set('display_errors', 1);
