<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tiki_Profile_InstallHandler_RatingConfigSet extends Tiki_Profile_InstallHandler
{
	private $configs = array();

	function fetchData()
	{
		$data = $this->obj->getData();

		if (isset($data['configs']) && is_array($data['configs'])) {
			$this->configs = $data['configs'];
		}
	}

	function canInstall()
	{
		$this->fetchData();

		return true;
	}

	function _install()
	{
		$this->fetchData();
		$this->replaceReferences($this->configs);
		
		$configlib = TikiLib::lib('ratingconfig');
		$configlib->preserve_configurations($this->configs);

		return true;
	}

	public static function export($writer)
	{
		$configlib = TikiLib::lib('ratingconfig');
		$configs = $configlib->get_configurations();

		$ids = array();
		foreach ($configs as $config) {
			if (Tiki_Profile_InstallHandler_RatingConfig::export($writer, $config)) {
				$ids[] = $config['ratingConfigId'];
			}
		}

		$writer->addObject(
			'rating_config_set',
			'set',
			array(
				'configs' => array_map(function ($id) use ($writer) {
					return $writer->getReference('rating_config', $id);
				}, $ids),
			)
		);

		return true;
	}
}
