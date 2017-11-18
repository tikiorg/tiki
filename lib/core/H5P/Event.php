<?php
// (c) Copyright 2002-2017 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$


/**
 * Makes it easy to track events throughout the H5P system.
 *
 * @package    H5P
 */
class H5P_Event extends H5PEventBase
{
	private $user = null;

	/**
	 * Adds event type, h5p library and timestamp to event before saving it.
	 *
	 * @param string $type
	 *  Name of event to log
	 * @param string $sub_type
	 * @param string $content_id
	 * @param string $content_title
	 * @param string $library_name
	 * @param string $library_version
	 * @internal param string $library Name of H5P library affacted*  Name of H5P library affacted
	 */
	function __construct($type, $sub_type = null, $content_id = null, $content_title = null, $library_name = null, $library_version = null)
	{
		global $user;

		$this->user = $user;

		parent::__construct($type, $sub_type, $content_id, $content_title, $library_name, $library_version);
	}

	/**
	 * Store the event.
	 */
	protected function save()
	{

		// Get data in array format without NULL values
		$data = $this->getDataArray();

		$message = 'Library ' . $data['library_name'] . ' (' . $data['library_version'] . ')';

		$title = $data['content_title'] ? $data['content_title'] : $data['content_id'];

		TikiLib::lib('logs')->add_action(
			$data['type'],
			$title,
			'h5p',
			$message,
			$this->user,
			'',
			'',
			$data['created_at']
		);

		return $this->id;
	}

	/**
	 * Count number of events.
	 */
	protected function saveStats()
	{
		// TODO implement this
	}
}
