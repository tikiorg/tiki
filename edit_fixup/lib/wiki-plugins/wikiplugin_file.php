<?php

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
				'required' => true,
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
		),
	);
}

function wikiplugin_file( $data, $params )
{
	global $tikilib;

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
