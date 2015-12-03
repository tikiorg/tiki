<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_xmlupdate_info()
{
	global $prefs;
	$info = array(
		'name' => tra('XMLupdate'),
		'documentation' => 'PluginXMLupdate',
		'description' => tra('Allows multiple elements of an XML file stored in a File Gallery to be updated - the File Gallery (at present) is assumed to store all files in a Directory.'),
		'prefs' => array( 'wikiplugin_xmlupdate' ),
		'body' => tra('list (one per line) of the XML element (node) names irrespective of their path and must (at present) be unique in the file '),
		'tags' => array( 'basic' ),
		'introduced' => 15,
		'params' => array(
			'fileId' => array(
				'required' => true,
				'name' => tra('File Id'),
				'description' => tra('File Id of the XML file stored in a File Gallery which is assumed to store its data in a directory'),
				'since' => '15.0',
				'filter' => 'digits',
			),
			'attribute' => array(
				'required' => false,
				'name' => tra('XML node attribute name'),
				'description' => tra('optional use of an attribute for the XML nodes - used as a label in the input form. If used then all nodes in the XML file should have the attribute text set even if it is a blank/space'),
				'since' => '15.0',
				'filter' => 'text',
				'default' => '',
			),	
			'namelisted' => array(
				'required' => false,
				'name' => tra('XML node listed in output'),
				'description' => tra('yes/no option to include the XML node name in the plugin output - default is yes - and should always be yes if the attribute parameter is not used or not all the nodes have their attribute text set'),
				'since' => '15.0',
				'filter' => 'word',
				'options' => array(
					array('text' => tra('yes'), 'value' => 'yes'),
					array('text' => tra('no'), 'value' => 'no'),
				),
				'default' => 'yes',
			),		
		)
	);
	return $info;
}

function wikiplugin_xmlupdate( $data, $params )
{
	global $tikilib, $prefs, $info;
	$filegallib = TikiLib::lib('filegal');
	$smarty = TikiLib::lib('smarty');
	// check that File Galleries have been set for use
	if ($prefs['feature_file_galleries'] != 'y') {
		return ("<span class='error'>Error: sorry you need to have File Galleries enabled to use the XMLUPDATE plugin</span>");
	}
	if ($prefs['fgal_use_db'] != 'n') {
		return ("<span class='error'>Error: sorry you need to have set File Galleries to store content in a directory to use the XMLUPDATE plugin</span>");
	}
	// check a fileId has been set
	if (!isset($params['fileId'])) {
		return ("<span class='error'>Error: fileId# for the XML file is not set</span>");
	}
	
	// set default params
	$plugininfo = wikiplugin_xmlupdate_info();
	$default = array();
	foreach ($plugininfo['params'] as $key => $param) {
		$default["$key"] = $param['default'];
	}
	$params = array_merge($default, $params);
	
	// get the full path address for the fileId from the File Gallery info and the pref for the File Gallery directory folder
	$fileId = $params['fileId'];
	$fileinfo = $filegallib->get_file_info($fileId);
	$fileaddress = $prefs['fgal_use_dir'] . $fileinfo['path'];
	
	// load the xml file from the File Gallery into the $filecontent variable which is a SimpleXML Element Object array of strings of the individual xml elements (nodes)
	$filecontent = simplexml_load_file($fileaddress);
	if ($filecontent === false) {
		return ("<span class='error'>Error: could not load the XML file from the fileId# provided</span>");	
	}
		
	// the plugin body data are individual lines with array elements referencing node names - so split th individual lines in the body up into an array
	$data = explode("\n", $data);
	// remove empties from the array caused by blank lines
	$trimmeddata = array_map('trim', $data);
	$trimmeddata = array_filter($trimmeddata);
	// reindex the array
	$finaldata = array_values($trimmeddata);
	
	// now loop through the body line data array using the array refs to extract the current value from the xml file into the xmldata array
	$xmldata = array();
	foreach ($finaldata as $line) {
		$xml_line = $filecontent->xpath('//'.$line); // this assumes the XML name is unique in the file
		$xmldata[] = $xml_line[0];
	}
	
	$smarty->assign('attused','no');
	// check if an attribute label has been used in the XML file
	if (isset($params['attribute'])) {
		$attlabel = $params['attribute'];
		
		// convert the xmldata array to get to the attributes only - not sure why this works!
		$jsondata = json_encode($xmldata);
		$convertedarray = json_decode($jsondata,TRUE);
		
		// assume that we only have a single attribute and put these into an indiviual array
		$descriptions = array();
		$dataitems = count($convertedarray);
		for ( $item=0; $item <= $dataitems-1; $item++ ) {
			// this assumes there is a description attribute for 
			$descriptions[$item] = $convertedarray[$item]['@attributes'][$attlabel];
		}
		$smarty->assign('xmldesc',$descriptions);
		$smarty->assign('attused','yes');
	}
	
	
	
	$datacount = count($finaldata);  // get the number of items being changed
	// assign the smarty variables to be used in the .tpl file
	$smarty->assign('xmlnames', $finaldata);
	$smarty->assign('xmlvalues',$xmldata);	
	$smarty->assign('xmlcount', $datacount);
	$smarty->assign('xmlnamelist', $params['namelisted']);
	
	
	if (isset($_REQUEST['update'])) {
		// loop through the updated items to update the $filecontent array with the new values
		for ($i=0; $i <= $datacount-1; $i++) {
			$new_value = $_REQUEST[$finaldata[$i]];
			$xmlresult = $filecontent->xpath('//'.$finaldata[$i]); // get the path for each item
			$xmlresult[0][0] = $new_value;  // don't understand why this works! but it does !
		}
		
		$filecontent->asXml($fileaddress); //update file in the File Gallery with revised content
		
		return ("<span>The XML parameters have been updated.<br/><br/>Reload the page to see the current values and to edit again.</span>");
	}	
	
	$out = '~np~' . $smarty->fetch('wiki-plugins/wikiplugin_xmlupdate.tpl') . '~/np~';	
	return $out;	
	
}