<?php

use WikiLingo\Plugin\Base;
use WikiLingo\Expression\Plugin;

class TikiWikiPluginBridge extends Base
{
	public function render(Plugin $plugin, $renderedChildren, $parser)
	{
		$name = strtolower($plugin->type);
		require_once("lib/wiki-plugins/wikiplugin_" . $name . ".php");
		$fn = "wikiplugin_" . $name;

		//$arguments = $this->argumentsParser->parse($plugin->parsed->arguments[0]->text);

		$output = $fn($renderedChildren, $plugin->parametersRaw);

		//$output = TikiLib::lib("parser")->parse_data($output);

		return $output;
	}
} 