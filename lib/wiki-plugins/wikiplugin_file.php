<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_file_info()
{
	return array(
		'name' => tra( 'File' ),
		'documentation' => 'PluginFile',
		'description' => tra("Displays a link to an attachment to a wiki page and can display an image attachment. "),
		'prefs' => array( 'feature_wiki_attachments', 'wikiplugin_file' ),
		'inline' => true,
		'params' => array(
    		'name' => array(
				'required' => false,
				'name' => tra('Name'),
				'description' => tra("Gives the name of the attached file to link to"),
			),
 			'desc' => array(
				'required' => false,
				'name' => tra('Description'),
				'description' => tra('Comment'),
			),
    		'page' => array(
				'required' => false,
				'name' => tra('Page'),
				'description' => tra("Gives the name of another page the attached file is on. The file on that page is linked to instead. Only works with wiki pages"),
			),
    		'showdesc' => array(
				'required' => false,
				'name' => tra('Show description'),
				'description' => tra("Shows the description as the link text instead of the file name"),
			),
    		'image' =>array(
				'required' => false,
				'name' => tra('Image'),
				'description' => tra("Says that this file is an image, and should be displayed inline using the img tag"),
			),
			'fileId' => array(
				'required' => false,
				'name' => tra('File ID'),
				'description' => tra('Numeric ID of a file in the file galleries'),
				'filter' => 'digits',
			),
			'date' => array(
				'required' => false,
				'name' => tra('Date'),
				'description' => tra('Pick the archive if exists created just before the date'),
			),
		),
	);
}

function wikiplugin_file( $data, $params )
{
	global $tikilib, $prefs;
	if (isset($params['fileId'])) {
		if ($prefs['feature_file_galleries'] != 'y') {
			return;
		}
		$fileId = $params['fileId'];
		if (isset($params['date'])) {
			static $wikipluginFileDate = 0;
			if (empty($params['date'])) {
				if (empty($wikipluginFileDate)) {
					return tra('The date has not been set');
				}
				$date = $wikipluginFileDate;
			} else {
				if (($date = strtotime($params['date'])) === false) {
					return tra('Incorrect date format');
				}
				$wikipluginFileDate = $date;
			}
			global $filegallib; include_once ('lib/filegals/filegallib.php');
			$fileId = $filegallib->getArchiveJustBefore($fileId, $date);
			if (empty($fileId)) {
				return tra('No such file');
			}
		}
		if (empty($data)) { // to avaoid problem with parsing
			$data = ' ';
		}
		return "[tiki-download_file.php?fileId=$fileId|$data]";
	}

	$filedata = array();
	$filedata["name"] = '';
	$filedata["desc"] = '';
	$filedata["showdesc"] = '';
	$filedata["page"] = '';
	$filedata["image"] = '';

	$filedata = array_merge( $filedata, $params );

	if( ! $filedata["name"] ) {
		return;
	}

	$forward = array();
	$forward['file'] = $filedata['name'];
	$forward['inline'] = 1;
	$forward['page'] = $filedata['page'];
	if($filedata['showdesc'])
		$forward['showdesc'] = 1;
	if($filedata['image'])
		$forward['image'] = 1;
	$middle = $filedata["desc"];

	return $tikilib->plugin_execute( 'attach', $middle, $forward );
}
