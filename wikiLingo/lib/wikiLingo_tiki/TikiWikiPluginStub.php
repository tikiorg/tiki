<?php

use WikiLingo\Plugin\Base;
use WikiLingo\Expression\Plugin;

class TikiWikiPluginStub extends Base
{
	public function render(Plugin $plugin, $renderedChildren, WikiLingoWYSIWYG\Parser $parser)
	{
		$name = strtolower($plugin->type);
		$element = $parser->element('WikiLingo\\Expression\\Plugin', 'span');
		$element->detailedAttributes['data-plugin-type'] = $name;
		$element->detailedAttributes['data-plugin-parameters'] = urlencode(json_encode($plugin->parametersRaw));
		$element->useDetailedAttributes = true;
		$element->staticChildren[] = $renderedChildren;
		$output = $element->render();
		return $output;
	}
}