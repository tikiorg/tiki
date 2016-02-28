<?php

class TikiAddons_Api_Search extends TikiAddons_Api {
	protected static $addonSources = [];

	static function setAddonSources($package, $sources)
	{
		$folder = str_replace('/', '_', $package);
		foreach ($sources as $source){
			try {
				require_once TIKI_PATH."/addons/".$folder."/".$source->location;
				self::$addonSources[] = new $source->class;
			}catch (Exception $e){
				error_log($e->getMessage());
			}
		}
	}

	static function getAddonSources()
	{
		return self::$addonSources;
	}
}