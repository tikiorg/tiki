<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tiki_Profile_WriterHelper
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

		$justReplace = false;
		foreach ($matches as $match) {
			if ($justReplaced) {
				$justReplaced = false;
				continue;
			}

			$pluginName = $match->getName();
			$params = $argumentParser->parse($match->getArguments());
			$params = preg_replace(array('/^&quot;/', '/&quot;$/'), '', $params);
			$body = $match->getBody();

			if ($info = $parserlib->plugin_info($pluginName)) {
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

				$match->replaceWithPlugin($pluginName, $params, $body);
				$justReplaced = true;
			}
		}

		return $matches->getText();
	}

	public static function tracker_field_string(Tiki_Profile_Writer $writer, $value)
	{
		return preg_replace_callback('/(\d+)/', function ($args) use ($writer) {
			return $writer->getReference('tracker_field', $args[1]);
		}, $value);
	}
}

