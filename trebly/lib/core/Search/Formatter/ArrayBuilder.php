<?php

class Search_Formatter_ArrayBuilder
{
	function getData($string)
	{
		$matches = WikiParser_PluginMatcher::match($string);
		$parser = new WikiParser_PluginArgumentParser;

		$data = array();

		foreach ($matches as $m) {
			$name = $m->getName();
			$arguments = $m->getArguments();

			if (isset($data[$name])) {
				if (! is_int(key($data[$name]))) {
					$data[$name] = array($data[$name]);
				}

				$data[$name][] = $parser->parse($arguments);
			} else {
				$data[$name] = $parser->parse($arguments);
			}
		}

		return $data;
	}
}

