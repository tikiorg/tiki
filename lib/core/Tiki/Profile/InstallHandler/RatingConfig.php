<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tiki_Profile_InstallHandler_RatingConfig extends Tiki_Profile_InstallHandler
{
	function getData()
	{
		if ( $this->data ) {
			return $this->data;
		}

		$defaults = array('expiry' => 3600);
		$data = array_merge($defaults, $this->obj->getData());

		$data = Tiki_Profile::convertYesNo($data);

		return $this->data = $data;
	}

	function canInstall()
	{
		$data = $this->getData();
		if ( ! isset($data['name'], $data['formula']) ) {
			return false;
		}

		return true;
	}

	function _install()
	{
		$ratingconfiglib = TikiLib::lib('ratingconfig');

		$data = $this->getData();

		$this->replaceReferences($data);

		$id = $ratingconfiglib->create_configuration($data['name']);
		$ratingconfiglib->update_configuration($id, $data['name'], $data['expiry'], $data['formula']);

		return $id;
	}

	public static function export(Tiki_Profile_Writer $writer, $id)
	{
		if (is_array($id)) {
			$info = $id;
		} else {
			$ratingconfiglib = TikiLib::lib('ratingconfig');
			$info = $ratingconfiglib->get_configuration($id);
		}

		if (! $info) {
			return false;
		}

		$writer->addObject('rating_config', $info['ratingConfigId'], array(
			'name' => $info['name'],
			'expiry' => $info['expiry'],
			'formula' => $info['formula'],
		));

		return true;
	}
}
