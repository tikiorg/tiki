<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tiki_Profile_InstallHandler_TrackerOption extends Tiki_Profile_InstallHandler
{
	private function getData() // {{{
	{
		if ( $this->data )
			return $this->data;

		$data = $this->obj->getData();

		$data = Tiki_Profile::convertYesNo($data);

		return $this->data = $data;
	} // }}}

	private function getOptionMap() // {{{
	{
		return Tiki_Profile_InstallHandler_Tracker::getOptionMap();
	} // }}}

	private function getOptionConverters() // {{{
	{
		return Tiki_Profile_InstallHandler_Tracker::getOptionConverters();
	} // }}}

	function canInstall() // {{{
	{
		$data = $this->getData();

		// Check for mandatory fields
		if (! isset($data['tracker'], $data['name'], $data['value'])) {
			return false;
		}
		
		return true;
	} // }}}

	function _install() // {{{
	{
		$input = $this->getData();
		$this->replaceReferences($input);

		$name = $input['name'];
		$value = $input['value'];

		$conversions = $this->getOptionConverters();
		if (isset($conversions[$name])) {
			$value = $conversions[$name]->convert($value);
		}

		$optionMap = $this->getOptionMap();

		if (isset($optionMap[$name])) {
			$name = $optionMap[$name];
		}

		$trklib = TikiLib::lib('trk');
		$trklib->replace_tracker_option($input['tracker'], $name, $value);
		
		return true;
	} // }}}
}
