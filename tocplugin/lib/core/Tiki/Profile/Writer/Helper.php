<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tiki_Profile_Writer_Helper
{
	public static function type_colon_object(Tiki_Profile_Writer $writer, $object)
	{
		list($type, $id) = explode(':', $object, 2);

		return $type . ':' . $writer->getReference($type, $id);
	}

	public static function type_in_param(Tiki_Profile_Writer $writer, $id, array $params)
	{
		if (isset($params['objType'])) {
			return $writer->getReference($params['objType'], $id);
		} elseif (isset($params['objectType'])) {
			return $writer->getReference($params['objectType'], $id);
		} elseif (isset($params['type'])) {
			return $writer->getReference($params['type'], $id);
		} else {
			return $id;
		}
	}

	public static function wiki_content(Tiki_Profile_Writer $writer, $content)
	{
		$tikilib = TikiLib::lib('tiki');
		$parserlib = TikiLib::lib('parser');
		$argumentParser = new WikiParser_PluginArgumentParser;
		$matches = WikiParser_PluginMatcher::match($content);

		// Handle known parameters for plugins
		$justReplaced = false;
		foreach ($matches as $match) {
			if ($justReplaced) {
				$justReplaced = false;
				continue;
			}

			$pluginName = $match->getName();
			$params = $argumentParser->parse($match->getArguments());
			$params = preg_replace(array('/^&quot;/', '/&quot;$/'), '', $params);
			$body = $match->getBody();

			$info = $parserlib->plugin_info($pluginName);

			if ($pluginName == 'module') {
				$moduleInfo = TikiLib::lib('mod')->get_module_info($params['module']);

				if ($moduleInfo) {
					$info['params'] = array_merge($info['params'], $moduleInfo['params']);
				}
			}

			if ($info) {
				foreach ($params as $paramName => & $paramValue) {
					if (isset($info['params'][$paramName]['profile_reference'])) {
						$paramInfo = $info['params'][$paramName];

						if (isset($paramInfo['separator'])) {
							$paramValue = $tikilib->multi_explode($paramInfo['separator'], $paramValue);
						}

						$paramValue = $writer->getReference($paramInfo['profile_reference'], $paramValue, $params);

						if (isset($paramInfo['separator'])) {
							$paramValue = $tikilib->multi_implode($paramInfo['separator'], $paramValue);
						}
					}
				}

				if (isset($info['profile_reference'])) {
					$body = $writer->getReference($info['profile_reference'], $body);
				}

				$match->replaceWithPlugin($pluginName, $params, $body);
				$justReplaced = true;
			}
		}

		// Handle pretty tracker references
		$content = $matches->getText();
		$content = preg_replace_callback(
			'/{\$f_(\d+)}/',
			function ($args) use ($writer) {
				return '{$f_' . $writer->getReference('tracker_field', $args[1]) . '}';
			},
			$content
		);

		return $content;
	}

	public static function tracker_field_string(Tiki_Profile_Writer $writer, $value)
	{
		return self::uniform_string('tracker_field', $writer, $value);
	}

	public function uniform_string($type, Tiki_Profile_Writer $writer, $value)
	{
		return preg_replace_callback(
			'/(\d+)/',
			function ($args) use ($writer, $type) {
				return $writer->getReference($type, $args[1]);
			},
			$value
		);
	}

	public static function search_plugin_content(Tiki_Profile_Writer $writer, $content)
	{
		$searchlib = TikiLib::lib('unifiedsearch');
		$dataSource = $searchlib->getProfileExportHelper();

		$argumentParser = new WikiParser_PluginArgumentParser;
		$matches = WikiParser_PluginMatcher::match($content);

		$justReplaced = false;
		foreach ($matches as $match) {
			if ($justReplaced) {
				$justReplaced = false;
				continue;
			}

			$name = $match->getName();
			$args = $argumentParser->parse($match->getArguments());

			if ($name === 'filter') {
				$args = $dataSource->replaceFilterReferences($writer, $args);
				$match->replaceWithPlugin('filter', $args, $match->getBody());
				$justReplaced = true;
			}
		}

		return $matches->getText();
	}

	public static function fitnesse_content(Tiki_Profile_Writer $writer, $content)
	{
		$searchlib = TikiLib::lib('unifiedsearch');

		$argumentParser = new WikiParser_PluginArgumentParser;
		$matches = WikiParser_PluginMatcher::match($content);

		$justReplaced = false;
		foreach ($matches as $match) {
			if ($justReplaced) {
				$justReplaced = false;
				continue;
			}

			$name = $match->getName();
			$args = $argumentParser->parse($match->getArguments());

			if (isset($args['trackerId'])) {
				$args['trackerId'] = $writer->getReference('tracker', $args['trackerId']);
				$match->replaceWithPlugin($name, $args, $match->getBody());
				$justReplaced = true;
			}
		}

		return $matches->getText();
	}

	public static function search_urlencoded(Tiki_Profile_Writer $writer, $value)
	{
		$searchlib = TikiLib::lib('unifiedsearch');
		$dataSource = $searchlib->getProfileExportHelper();

		$data = array();
		parse_str($value, $data);
		foreach ($data as $key => & $value) {
			if ($type = $dataSource->getTypeForField($key)) {
				$value = self::uniform_string($type, $writer, $value);
			}
		}

		$string = http_build_query($data, '', '&');
		// Un-encode the reference portions which need to be left plain
		$string = preg_replace('/%24(profileobject|unknownobject)%3A(\w+)%24/', '\$$1:$2\$', $string);

		return $string;
	}
}

