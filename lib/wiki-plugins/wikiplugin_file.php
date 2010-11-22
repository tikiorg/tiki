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
		'description' => tra("Displays a link to a file (either from the file gallery or an attachment to a wiki page) and can display an image attachment. For more than one file from file galleries, or more optional information shown from the file/s, use the plugin FILES instead"),
		'prefs' => array( 'wikiplugin_file' ),
		'body' => tra('Label for the link to the file'),
		'icon' => 'pics/icons/file-manager.png',
		'inline' => true,
		'params' => array(
			'type' => array(
				'required' => true,
				'name' => tra('Type'),
				'description' => tra('Choose either File from gallery or Wiki page attachment.'),
				'options' => array(
					array('text' => tra('File from file gallery'), 'value' => 'gallery'),
					array('text' => tra('Wiki page attachment'), 'value' => 'attachment'),
				),
			),
			'name' => array(
				'required' => true,
				'name' => tra('Name'),
				'description' => tra("Gives the name of the attached file to link to"),
				'parent' => array('name' => 'type', 'value' => 'attachment'),
			),
 			'desc' => array(
				'required' => false,
				'name' => tra('Description'),
				'description' => tra('Used as link label'),
				'parent' => array('name' => 'type', 'value' => 'attachment'),
				'advanced' => true,
			),
			'page' => array(
				'required' => false,
				'name' => tra('Page'),
				'description' => tra("Name of the wiki page the file is attached to. If left empty when the plugin is used on a wiki page, this defaults to that wiki page."),
				'parent' => array('name' => 'type', 'value' => 'attachment'),
				'advanced' => true,
			),
			'showdesc' => array(
				'required' => false,
				'name' => tra('Show attachment description'),
				'description' => tra("Show the attachment description as the link label instead of the attachment name."),
				'options' => array(
					array('text' => tra('No'), 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'),  
				),
				'parent' => array('name' => 'type', 'value' => 'attachment'),
				'advanced' => true,
			),
			'image' =>array(
				'required' => false,
				'name' => tra('Image'),
				'description' => tra("Says that this file is an image, and should be displayed inline using the img tag"),
				'parent' => array('name' => 'type', 'value' => 'attachment'),
				'advanced' => true,
			),
			'fileId' => array(
				'required' => true,
				'name' => tra('File identifier'),
				'description' => tra('Identifier of a file in the file galleries.') . ' ' . tra('Example value:') . ' 42',
				'type' => 'fileId',
				'area' => 'fgal_picker_id',
				'filter' => 'digits',
				'parent' => array('name' => 'type', 'value' => 'gallery'),
			),
			'date' => array(
				'required' => false,
				'name' => tra('Date'),
				'description' => tra('Pick the archive if exists created just before the date of the fileId'),
				'parent' => array('name' => 'type', 'value' => 'gallery'),
				'advanced' => true,
			),
			'showicon' => array(
				'required' => false,
				'name' => tra('Show icon'),
				'description' => 'y|n',
				'filter' => 'alpha',
				'parent' => array('name' => 'type', 'value' => 'gallery'),
				'advanced' => true,
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
		if (isset($params['showicon']) & $params['showicon'] == "y") {
			return "{img src=tiki-download_file.php?fileId=$fileId&amp;thumbnail=y width=16} [tiki-download_file.php?fileId=$fileId|$data]";
		} else {
			return "[tiki-download_file.php?fileId=$fileId|$data]";
		}
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
