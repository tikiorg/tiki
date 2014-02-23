<?php

use WikiLingo\Plugin\Base;
use WikiLingo\Expression\Plugin;

class TikiWikiPluginStub extends Base
{
	public function __construct()
	{
		$this->allowLines = true;
	}

	public function render(Plugin $plugin, $renderedChildren, WikiLingoWYSIWYG\Parser $parser)
	{
		$element = $parser->element('WikiLingo\\Expression\\Plugin', 'span');
		$element->detailedAttributes['data-plugin-type'] = $plugin->type;
		$element->detailedAttributes['data-plugin-parameters'] = urlencode(json_encode($plugin->parametersRaw));
		$element->useDetailedAttributes = true;
		$element->staticChildren[] = $renderedChildren;
		$output = $element->render();
		return $output;
	}
}