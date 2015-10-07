<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tiki_Profile_InstallHandler_Template extends Tiki_Profile_InstallHandler
{
	function getData()
	{
		if ( $this->data )
			return $this->data;

		$defaults = array(
			'sections' => array( 'wiki' ),
			'type' => 'static',
		);

		$data = array_merge($defaults, $this->obj->getData());

		$data = Tiki_Profile::convertYesNo($data);

		return $this->data = $data;
	}

	function canInstall()
	{
		$data = $this->getData();
		if ( ! isset( $data['name'] ) )
			return false;
		if ( ! isset( $data['content'] ) && ! isset( $data['page'] ) )
			return false;
		if ( ! isset( $data['sections'] ) || ! is_array($data['sections']) )
			return false;

		return true;
	}

	function _install()
	{
		$templateslib = TikiLib::lib('template');

		$data = $this->getData();

		$this->replaceReferences($data);

		if ( isset( $data['page'] ) ) {
			$data['content'] = 'page:' . $data['page'];
			$data['type'] = 'page';
		}

		$templateId = $templateslib->replace_template(null, $data['name'], $data['content'], $data['type']);
		foreach ( $data['sections'] as $section ) {
			$templateslib->add_template_to_section($templateId, $section);
		}

		return $templateId;
	}
}
