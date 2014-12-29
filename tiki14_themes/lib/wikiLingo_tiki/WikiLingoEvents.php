<?php

use Types\Type;
use WikiLingo\Event;
use WikiLingo\Expression\Plugin;

include_once 'lib/wikiLingo_tiki/WikiMetadataLookup.php';

/**
 * Class WikiLingoEvents
 * Used to extend the events system within wikiLingo
 */
class WikiLingoEvents
{
	public static $bridge = null;
	public static $toc = null;
	public static $argumentParser;

	public function __construct(WikiLingo\Parser &$wikiLingoParser, $bindFLP = true)
	{
        global $prefs, $page;
		$headerlib = TikiLib::lib('header');

        if ($page == null && $bindFLP) {
            throw new \Exception("Page undefined");
        }

		require_once('lib/wikiLingo_tiki/WikiPluginBridge.php');
		$bridge = self::$bridge = new WikiPluginBridge();
        $events = Type::Events($wikiLingoParser->events);

        $events->bind(new Event\Expression\Plugin\Exists(function(Plugin &$plugin) use ($wikiLingoParser, $bridge) {
			if (!$plugin->exists) {
				switch ($plugin->classType) {
					case "WikiLingo\\Plugin\\Maketoc":
						if (self::$toc == null) {
							self::$toc = new WikiLingo\Plugin\Toc();
						}
						$plugin->exists = true;
						$plugin->class = self::$toc;
						$wikiLingoParser->pluginInstances[$plugin->classType] = self::$toc;
						break;
					default:
						$plugin->exists = true;
						$plugin->class = $bridge;
						$wikiLingoParser->pluginInstances[$plugin->classType] = $bridge;
				}
			}
		}));

		if ($bindFLP) {
		    //FutureLink-Protocol Events
		    FLP\Events::bind(new FLP\Event\MetadataLookup(function($linkType, &$metadata) use ($page, $headerlib) {
		        $metadataLookup = new WikiMetadataLookup($page);

		        $metadataTemp = $metadataLookup->getPartial();
		        $metadataTemp->href = TikiLib::tikiUrl('tiki-index.php') . '?page=' . $page;
		        $metadataTemp->text = $metadata->text;
		        $metadata = $metadataTemp;
		    }));
		}
	}
}
