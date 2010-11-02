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
		'description' => tra("Displays a link to a file (either from the file gallery or an attachment to a wiki page) and can display an image attachment."),
		'prefs' => array( 'wikiplugin_file' ),
		'inline' => true,
		'params' => array(
    		'name' => array(
				'required' => false,
				'name' => tra('Name'),
				'description' => tra('Wiki attachment:') . ' ' . tra("Gives the name of the attached file to link to"),
			),
 			'desc' => array(
				'required' => false,
				'name' => tra('Description'),
				'description' => tra('Wiki attachment:') . ' ' . tra('Used as link label'),
			),
    		'page' => array(
				'required' => false,
				'name' => tra('Page'),
				'description' => tra('Wiki attachment:') . ' ' . tra("Name of the wiki page the file is attached to. If left empty when the plugin is used on a wiki page, this defaults to that wiki page."),
			),
    		'showdesc' => array(
				'required' => false,
				'name' => tra('Show attachment description'),
				'description' => tra('Wiki attachment:') . ' ' . tra("Show the attachment description as the link label instead of the attachment name."),
				'options' => array(
					array('text' => tra('No'), 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'),  
				),
			),
    		'image' =>array(
				'required' => false,
				'name' => tra('Image'),
				'description' => tra('Wiki attachment:') . ' ' . tra("Says that this file is an image, and should be displayed inline using the img tag"),
			),
			'fileId' => array(
				'required' => false,
				'name' => tra('File identifier'),
				'description' => tra('File from gallery:') . ' ' . tra('Identifier of a file in the file galleries.') . ' ' . tra('Example value:') . ' 42',
				'filter' => 'digits',
			),
			'date' => array(
				'required' => false,
				'name' => tra('Date'),
				'description' => tra('File from gallery:') . ' ' . tra('Pick the archive if exists created just before the date of the fileId'),
			),
		)
	);
}

function wikiplugin_file( $data, $params )
{
	global $tikilib, $prefs;
	if (isset($params['fileId'])) {
		global $filegallib; include_once ('lib/filegals/filegallib.php');
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
			$fileId = $filegallib->getArchiveJustBefore($fileId, $date);
			if (empty($fileId)) {
				return tra('No such file');
			}
		} else {
			$info = $filegallib->get_file_info($fileId);
			if (empty($info)) {
				return tra('Incorrect param').' fileId';
			}
		}
			
		if (empty($data)) { // to avaoid problem with parsing
			$data = empty($info['name'])?$info['filename']: $info['name'];
		}
		return "[tiki-download_file.php?fileId=$fileId|$data]";
	}

	if ($prefs['feature_wiki_attachments'] != 'y') {
		return "<span class='warn'>" . tra("Wiki attachments are disabled."). "</span>";
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
