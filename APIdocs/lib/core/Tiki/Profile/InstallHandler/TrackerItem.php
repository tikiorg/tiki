<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tiki_Profile_InstallHandler_TrackerItem extends Tiki_Profile_InstallHandler
{
	private $mode = 'create';
	
	private function getData() // {{{
	{
		if ( $this->data )
			return $this->data;

		$data = $this->obj->getData();

		return $this->data = $data;
	} // }}}

	function getDefaultValues() // {{{
	{
		return array(
			'tracker' => 0,
			'status' => 'o',
			'values' => array(),
		);
	} // }}}

	function getConverters() // {{{
	{
		return array(
			'status' => new Tiki_Profile_ValueMapConverter(array( 'open' => 'o', 'pending' => 'p', 'closed' => 'c' )),
		);
	} // }}}

	function canInstall()
	{
		$data = $this->getData();

		if ( ! isset( $data['tracker'], $data['values'] ) )
			return false;

		if ( ! is_array($data['values']) )
			return false;

		foreach ( $data['values'] as $row )
			if ( ! is_array($row) || count($row) != 2 )
				return false;

		return $this->convertMode($data);
	}

	function convertMode( $data )
	{
		if (isset($data['mode']) && $data['mode'] == 'update') {
			if (empty($data['itemId'])) {
				throw new Exception("itemId is mandatory to update tracker");
			} else {
				$this->mode = 'update';	
			}
		} 
		return true;
	}
 	
	function _install()
	{
		$data = $this->getData();
		$converters = $this->getConverters();
		$this->replaceReferences($data);

		foreach ( $data as $key => &$value )
			if ( isset( $converters[$key] ) )
				$value = $converters[$key]->convert($value);

		$data = array_merge($this->getDefaultValues(), $data);

		global $trklib;
		if ( ! $trklib )
			require_once 'lib/trackers/trackerlib.php';

		$fields = $trklib->list_tracker_fields($data['tracker']);
		foreach ( $data['values'] as $row ) {
			list( $f, $v) = $row;

			foreach ( $fields['data'] as $key => $entry )
				if ( $entry['fieldId'] == $f)
					$fields['data'][$key]['value'] = $v;
		}

		if ($this->mode == 'update') {
			return $trklib->replace_item($data['tracker'], $data['itemId'], $fields, $data['status']);
		} else {
			return $trklib->replace_item($data['tracker'], 0, $fields, $data['status']);
		}
	}
}
