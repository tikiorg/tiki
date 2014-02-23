<?php

use WikiLingo\Plugin\Base;
use WikiLingo\Expression\Plugin;

class TikiWikiPluginBridge extends Base
{
	public function __construct()
	{
		$this->allowLines = true;
	}

	public function render(Plugin $plugin, $renderedChildren, $parser)
	{
		$name = strtolower($plugin->type);
		$fileLocation = "lib/wiki-plugins/wikiplugin_" . $name . ".php";
		if (file_exists($fileLocation)) {
			require_once($fileLocation);
			$fn = "wikiplugin_" . $name;

			//$arguments = $this->argumentsParser->parse($plugin->parsed->arguments[0]->text);

			$output = $fn($renderedChildren, $plugin->parametersRaw);

			//$output = TikiLib::lib("parser")->parse_data($output);

			return $output;
		}
		return $parser->syntax($plugin->parsed->loc);
	}
} 