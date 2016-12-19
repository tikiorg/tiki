<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_Action_Factory
{
	private $actions = array();

	function register(array $actions)
	{
		$this->actions = array_merge($this->actions, $actions);
	}

	function fromMatch($match)
	{
		$parser = new WikiParser_PluginArgumentParser;
		$arrayBuilder = new Search_Formatter_ArrayBuilder;
		$arguments = $parser->parse($match->getArguments());

		if (! empty($arguments['name'])) {
			$sequence = $this->build($arguments['name'], $arrayBuilder->getData($match->getBody()));

			if (isset($arguments['group'])) {
				$sequence->setRequiredGroup($arguments['group']);
			}

			return $sequence;
		}
	}

	function build($name, array $data)
	{
		$sequence = new Search_Action_Sequence($name);
		
		if (! isset($data['step'])) {
			$data['step'] = array();
		}

		if (isset($data['step']['action'])) {
			$data['step'] = array($data['step']);
		}

		foreach ($data['step'] as $definition) {
			$sequence->addStep($this->buildStep($definition));
		}

		return $sequence;
	}

	private function buildStep($definition)
	{
		if (empty($definition['action'])) {
			return new Search_Action_UnknownStep;
		}

		$action = trim($definition['action']);

		if (! isset($this->actions[$action])) {
			return new Search_Action_UnknownStep($action);
		}

		$actionClass = $this->actions[$action];
		unset($definition['action']);

		if (! class_exists($actionClass)) {
			return new Search_Action_UnknownStep($action);
		}

		return new Search_Action_ActionStep(new $actionClass, $definition);
	}
}

