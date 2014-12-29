<?php

use WikiLingo\Plugin\Base;
use WikiLingo\Expression\Plugin;

class WikiPluginBridge extends Base
{
	public function __construct()
	{
		$this->allowLines = true;
	}

	public function render(Plugin &$plugin, &$body, &$renderer, &$parser)
	{
		$name = strtolower($plugin->type);
		$fileLocation = "lib/wiki-plugins/wikiplugin_" . $name . ".php";
		if (file_exists($fileLocation)) {
			require_once($fileLocation);
			$fn = "wikiplugin_" . $name;

			if ($plugin->parsed->type === 'Plugin') {
				$body = $parser->syntaxBetween($plugin->parsed->arguments[0]->loc, $plugin->parsed->stateEnd->loc);
			}

			$output = $fn($body, $plugin->parametersRaw);
			$output = TikiLib::lib("parser")->parse_data($output, array('is_html' => true));

			return $output;
		}
		return '';
	}
}