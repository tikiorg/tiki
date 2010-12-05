<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_file_info()
{
	global $prefs;
	$info = array(
		'name' => tra( 'File' ),
		'documentation' => tra('PluginFile'),
		'description' => tra('Displays a link to a file (either from the file gallery or an attachment to a wiki page) and can display an image attachment. For more than one file from file galleries, or more optional information shown from the files, use the plugin FILES instead'),
		'prefs' => array( 'wikiplugin_file' ),
		'body' => tra('Label for the link to the file (ignored if the file is a wiki attachment)'),
		'icon' => 'pics/icons/file-manager.png',
		'inline' => true,
		'params' => array(
			'type' => array(
				'required' => true,
				'name' => tra('Type'),
				'description' => tra('Indicate whether the file is in a file gallery or is a wiki page attachment'),
				'filter' => 'alpha',
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''), 
				), //rest filled in below
			),
			'name' => array(
				'required' => true,
				'name' => tra('Name'),
				'description' => tra('Identify an attachment by entering its file name, which will show as a link to the file.
										 If the page parameter is empty, it must be a file name of an attachment to the page where the plugin is used.'),
				'default' => '',
				'parent' => array('name' => 'type', 'value' => 'attachment'),
			),
 			'desc' => array(
				'required' => false,
				'name' => tra('Custom Description'),
				'description' => tra('Custom text that will be used for the link instead of the file name or file description'),
				'parent' => array('name' => 'type', 'value' => 'attachment'),
				'advanced' => true,
				'default' => '',
			),
			'page' => array(
				'required' => false,
				'name' => tra('Page'),
				'description' => tra('Name of the wiki page the file is attached to. Defaults to the wiki page where the plugin is used if empty.'),
				'parent' => array('name' => 'type', 'value' => 'attachment'),
				'default' => '',
				'advanced' => true,
			),
			'showdesc' => array(
				'required' => false,
				'name' => tra('Attachment Description'),
				'description' => tra('Show the attachment description as the link label instead of the attachment file name.'),
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 1),  
					array('text' => tra('No'), 'value' => 0),
				),
				'parent' => array('name' => 'type', 'value' => 'attachment'),
				'default' => '',
				'advanced' => true,
			),
			'image' =>array(
				'required' => false,
				'name' => tra('Image'),
				'description' => tra('Indicates that this attachment is an image, and should be displayed inline using the img tag'),
				'parent' => array('name' => 'type', 'value' => 'attachment'),
				'advanced' => true,
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 1), 
					array('text' => tra('No'), 'value' => 0)
				),
			),
			'fileId' => array(
				'required' => true,
				'name' => tra('File ID'),
				'description' => tra('File ID of a file in a file gallery or an archive.') . ' ' . tra('Example value:') . ' 42',
				'type' => 'fileId',
				'area' => 'fgal_picker_id',
				'filter' => 'digits',
				'default' => '',
				'parent' => array('name' => 'type', 'value' => 'gallery'),
			),
			'date' => array(
				'required' => false,
				'name' => tra('Date'),
				'description' => tra('For an archive file, the archive created just before this date will be linked to.'),
				'parent' => array('name' => 'type', 'value' => 'gallery'),
				'default' => '',
				'advanced' => true,
			),
			'showicon' => array(
				'required' => false,
				'name' => tra('Show Icon'),
				'description' => tra('Show an icon version of the file or file type with the link to the file.'),
				'filter' => 'alpha',
				'parent' => array('name' => 'type', 'value' => 'gallery'),
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				),
				'advanced' => true,
			),
		)
	);
	if ($prefs['feature_file_galleries'] == 'y') {
		$info['params']['type']['options'][] = 	array('text' => tra('File Gallery File/Archive'), 'value' => 'gallery');
	}
	if ($prefs['feature_wiki_attachments'] == 'y') {
		$info['params']['type']['options'][] = 	array('text' => tra('Wiki Page Attachment'), 'value' => 'attachment');
	}
	return $info;
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
				return tra('Incorrect parameter').' fileId';
			}
		}
			
		if (empty($data)) { // to avoid problem with parsing
			$data = empty($info['name'])?$info['filename']: $info['name'];
		}
		if (isset($params['showicon']) & $params['showicon'] == "y") {
			return "{img src=tiki-download_file.php?fileId=$fileId&amp;thumbnail=y&amp;x=16 link=tiki-download_file.php?fileId=$fileId} [tiki-download_file.php?fileId=$fileId|$data]";
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
